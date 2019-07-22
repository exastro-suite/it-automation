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
            $userPath = $g['root_dir_path'] . '/webconfs/users/' . $menuAry['menu_id'] . '_loadTable.php';

            if(file_exists($systemPath) || file_exists($userPath)){
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

    $sql  = 'SELECT mg.MENU_GROUP_ID, mg.MENU_GROUP_NAME, m.MENU_ID, m.MENU_NAME, m.DISP_SEQ';
    $sql .= ' FROM A_MENU_GROUP_LIST AS mg, A_MENU_LIST AS m';
    $sql .= ' WHERE mg.MENU_GROUP_ID = m.MENU_GROUP_ID';
    $sql .= " AND m.DISUSE_FLAG = '0'";
    $sql .= ' AND NOT EXISTS (';
    $sql .= ' SELECT hm.MENU_ID';
    $sql .= ' FROM B_DP_HIDE_MENU_LIST AS hm';
    $sql .= ' WHERE m.MENU_ID = hm.MENU_ID)';
    $sql .= ' ORDER BY m.MENU_GROUP_ID ASC, m.DISP_SEQ ASC';

    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
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

    // メニューが一つも選択されていない場合はエラー
    if (count($_POST) === 0) {
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900047'));
    }

    foreach ($_POST as $key => $value) {
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
 * データインポート管理テーブル更新処理
 */
function insertTask(){
    global $g;

    // トランザクション開始
    $varTrzStart = $g['objDBCA']->transactionStart();
    if ($varTrzStart === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900015',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    $resArray = getSequenceLockInTrz('B_DP_STATUS_RIC','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $resArray = getSequenceLockInTrz('B_DP_STATUS_JSQ','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    // 作業No.を取得する
    $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = 'B_DP_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $seqAry = array();
    while ($row = $objQuery->resultFetch()){
        $seqAry[] = $row;
    }

    $p_execution_utn_no = $seqAry[0]['VALUE'];

    // Jnl№を取得する
    $resArray = array();
    $resArray = getSequenceValueFromTable('B_DP_STATUS_JSQ', 'A_SEQUENCE', FALSE);
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    $p_execution_jnl_no = $resArray[0];

    $arrayConfig = array(
        'JOURNAL_SEQ_NO' => '',
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => '',
        'TASK_STATUS' => '',
        'DP_TYPE' => '',
        'IMPORT_TYPE' => '',
        'FILE_NAME' => '',
        'DISP_SEQ' => '',
        'NOTE' => '',
        'DISUSE_FLAG' => '',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => ''
    );

    $arrayValue = array(
        'JOURNAL_SEQ_NO' => $p_execution_jnl_no,
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => $p_execution_utn_no,
        'TASK_STATUS' => 1,
        'DP_TYPE' => 1,
        'IMPORT_TYPE' => '',
        'FILE_NAME' => '',
        'DISP_SEQ' => '',
        'NOTE' => '',
        'DISUSE_FLAG' => '0',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => ACCOUNT_NAME
    );

    $resAry = makeSQLForUtnTableUpdate(
                  $g['db_model_ch'],
                  'INSERT',
                  'TASK_ID',
                  'B_DP_STATUS',
                  'B_DP_STATUS_JNL',
                  $arrayConfig,
                  $arrayValue
              );
    if ($resAry[0] === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900046',
                                             array('B_DP_STATUS', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $sqlUtnBody = $resAry[1];
    $arrayUtnBind = $resAry[2];
    $sqlJnlBody = $resAry[3];
    $arrayJnlBind = $resAry[4];

    $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
    $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);

    if ($objQueryUtn->getStatus() === false || $objQueryJnl->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    if ($objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "") {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $rUtn = $objQueryUtn->sqlExecute();
    if ($rUtn != true) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_STATUS')));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $rJnl = $objQueryJnl->sqlExecute();
    if ($rJnl != true) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_STATUS_JNL')));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    // 更新系のシーケンスを増やす
    $p_execution_utn_next_no = $p_execution_utn_no + 1;
    $sql = "UPDATE A_SEQUENCE set VALUE = :value WHERE NAME = 'B_DP_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    
    $res = $objQuery->sqlBind(array('value' => $p_execution_utn_next_no));
    if ($res != "") {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    $res = $objQuery->sqlExecute();
    if ($res != true) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_STATUS')));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    $res = $g['objDBCA']->transactionCommit();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900036',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }
    $g['objDBCA']->transactionExit();

    return $p_execution_utn_no;
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
