<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
/**
 * データポータビリティ用関数群
 *
 */

/**
 * インポート後のファイルコピーチェック用のリストを作成する
 *
 * @param    array    $contentFileAry    uploadfileのpath']
 * @param    int      $dirName           ディレクトリ名
 */
function makeCopyCheckList($contentFileAry, $dirName){
    global $g;
    $path = $g['root_dir_path'] . '/temp/data_export/' . $dirName . '/';
    if (count($contentFileAry) === 0) {
        return;
    }
    $pathAry = array();
    $resAry = $contentFileAry;

    $json = json_encode($resAry);
    $res = file_put_contents($path . 'COPY_DIR_FILE_LIST', $json);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900035',
                                             array(basename(__FILE__), __LINE__)));
        removeFiles($path, true);
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
}

/**
 * エクスポートするメニュー一覧からloadTableを使っていないメニューを除去する
 * 
 * @param    array    $menuGroupAry    メニューグループとメニューID一覧
 * @param    array    $menuIdAry       loadTableを使っているメニューID 
 *
 * @return   array    $retAry          loadTableを使っているメニューの配列
 */
function getExportMenuList($menuGroupAry){
    global $g;    
    $retAry = array();

    foreach($menuGroupAry as $mgKey => $menuGroup) {
        foreach($menuGroup['menu'] as $mKey => $menuAry) {

            $systemPath = $g['root_dir_path'] . '/webconfs/systems/' . $menuAry['menu_id'] . '_loadTable.php';
            $sheetPath = $g['root_dir_path'] . '/webconfs/sheets/' . $menuAry['menu_id'] . '_loadTable.php';
            $userPath = $g['root_dir_path'] . '/webconfs/users/' . $menuAry['menu_id'] . '_loadTable.php';

            if(file_exists($systemPath) || file_exists($sheetPath) || file_exists($userPath)){
                $retAry[$mgKey]['menu_group_name'] = $menuGroup['menu_group_name'];
                $retAry[$mgKey]['menu'][] = $menuAry;
            }
        }
    }

    return $retAry;
}

/**
 * エクスポート可能なメニューを取得する
 * (画面に表示しないメニューは、メニューIDをB_DP_HIDE_MENU_LISTに登録しておくこと)
 *
 * @return    arrray    $retAry    エクスポート可能なメニュー一覧
 */
function makeExportCheckbox(){
    global $g;

    $sql = "SELECT
                A_MENU_GROUP_LIST.MENU_GROUP_ID,
                A_MENU_GROUP_LIST.MENU_GROUP_NAME,
                A_MENU_LIST.MENU_ID,
                A_MENU_LIST.MENU_NAME,
                A_MENU_LIST.DISP_SEQ
            FROM
                A_MENU_GROUP_LIST
            LEFT OUTER JOIN
                A_MENU_LIST
            ON
                A_MENU_GROUP_LIST.MENU_GROUP_ID = A_MENU_LIST.MENU_GROUP_ID
            LEFT OUTER JOIN
                A_ROLE_MENU_LINK_LIST
            ON
                A_MENU_LIST.MENU_ID = A_ROLE_MENU_LINK_LIST.MENU_ID
            LEFT OUTER JOIN
                A_ROLE_ACCOUNT_LINK_LIST
            ON
                A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
            WHERE
                USER_ID = :USER_ID
            AND
                A_MENU_GROUP_LIST.DISUSE_FLAG = '0'
            AND
                A_MENU_LIST.DISUSE_FLAG = '0'
            AND
                NOT EXISTS(
                    SELECT
                        B_DP_HIDE_MENU_LIST.MENU_ID
                    FROM
                        B_DP_HIDE_MENU_LIST
                    WHERE
                        A_MENU_LIST.MENU_ID = B_DP_HIDE_MENU_LIST.MENU_ID
                )
            ORDER BY
                A_MENU_LIST.MENU_GROUP_ID ASC,
                A_MENU_LIST.DISP_SEQ ASC,
                A_MENU_LIST.MENU_ID ASC
            ";

    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(array(
        "USER_ID" => $g["login_id"]
    ));

    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $checkboxExportAry = array();
    while ($row = $objQuery->resultFetch()){
        $checkboxExportAry[] = $row;
    }

    $menuGroupAry = array();
    foreach ($checkboxExportAry as $key => $value) {
        $menuGroupAry[$value['MENU_GROUP_ID']] = htmlentities($value['MENU_GROUP_NAME'],
                                                              ENT_QUOTES,
                                                              'utf-8');
    }

    $retAry = array();
    foreach ($menuGroupAry as $key => $value) {
        $cnt = 0;
        $retAry[$key]['menu_group_name'] = $value;
        foreach ($checkboxExportAry as $value2) {
            if ($key === (int)$value2['MENU_GROUP_ID']) {
                $retAry[$key]['menu'][$cnt]['menu_id'] = sprintf("%010d", $value2['MENU_ID']);
                $retAry[$key]['menu'][$cnt]['menu_name'] = htmlentities($value2['MENU_NAME'],
                                                                        ENT_QUOTES,
                                                                        'utf-8');
                $cnt++;
            }
        }
    }

    return $retAry;
}

/**
 * エクスポート用メニューリストを作成する
 *
 * @param    int    $dirName    ディレクトリ名
 */
function makeExportDataList($dirName){
    global $g;

    $path = $g['root_dir_path'] . '/temp/data_export/' . $dirName;
    mkdir($path);

    unset($_POST['zip']);
    unset($_POST['menu_on']);
    $menuIdAry = array();

    // 廃止情報未選択はエラー
    if ( !isset($_POST["abolished_type"]) || is_int($_POST["abolished_type"] )) {
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900077'));
    }

    foreach ($_POST as $key => $value) {
        if ( is_int($key) ) {
            // バリデーションチェック
            foreach ($value as $value2) {
                if(strlen($value2) > MENU_ID_LENGTH || ctype_digit($value2) === false) {
                    // 不正アクセスで処理終了
                    web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900025'));
                    // 不正操作によるアクセス警告画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(400,10310201);
                    exit;
                }
            }
            $menuIdAry = array_merge($menuIdAry, $value);
        }
    }

    // メニューが一つも選択されていない場合はエラー
    if (count($menuIdAry) === 0) {
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900047'));
    }

    $json = json_encode($menuIdAry);
    $fileputflg = file_put_contents($path . '/MENU_ID_LIST', $json);

    return true;
}

/**
 * 指定したディレクトリ内を再帰的に削除する
 *
 * @param    string    $path    ディレクトリパス
 * @param    bool      ディレクトリ削除フラグ
 */
function removeFiles($path, $recursive=false){
    foreach(glob($path . '*') as $file) {
        if(is_dir($file)) {
            removeFiles($file . '/', true);
        } else {
            if (file_exists($file) === true) {
                unlink($file);
            }
        }
    }
    if ($recursive === true) {
        if (file_exists($path) === true) {
            rmdir($path);
        }
    }
    return;
}


/**
 * ディレクトリをリネームする
 *
 * @param    int    $taskNo
 */
function renameExportDir($dirName, $taskNo){
    global $g;
    $src = $g['root_dir_path'] . '/temp/data_export/' . $dirName;
    $dst = $g['root_dir_path'] . '/temp/data_export/' . $taskNo;

    $output = NULL;
    $cmd = "sudo mv $src $dst 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
}

/**
 * 日付時刻の有効性のチェック
 *
 */
function validateDate1($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}