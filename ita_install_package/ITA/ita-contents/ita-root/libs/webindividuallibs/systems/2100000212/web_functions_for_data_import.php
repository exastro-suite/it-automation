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
 * zipファイルをアップロードする
 */
function uploadZipFile(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $fileName = $uploadId . '_ita_data.tar.gz';
    $uploadFilePath = $g['root_dir_path'] . '/temp/data_import/upload/' . $fileName;
    $uploadPath = $g['root_dir_path'] . '/temp/data_import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/data_import/import/';

    if (strlen($_FILES['zipfile']['tmp_name']) === 0) {
        // ファイルを指定していないためエラー
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900011'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    $uploadRes = $_FILES['zipfile']['error'];
    switch ($uploadRes) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            // サイズを超えているためエラー
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900010'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_PARTIAL:
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900022'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_NO_FILE:
            // アップロードされなかった
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900009'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_NO_TMP_DIR:
            // テンポラリフォルダがない
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900023'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_CANT_WRITE:
            // 書き込み権限なし
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900007'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        default:
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900006',
                                                 array(basename(__FILE__), __LINE__)));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // HTTP経由でアップロードされたかを確認
    if (is_uploaded_file($_FILES['zipfile']['tmp_name']) === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900004'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // zipファイルであることを確認
    $objFinfo = new finfo(FILEINFO_MIME_TYPE);
    $ext = array_search($objFinfo->file($_FILES['zipfile']['tmp_name']),
                                        array('zip' => 'application/x-gzip'));
    if ($ext === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900005'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // ファイル移動
    if (move_uploaded_file($_FILES['zipfile']['tmp_name'], $uploadFilePath) === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    $_SESSION['data_portability_upload_file_name'] = $_FILES['zipfile']['name'];

    return;
}

/**
 * zipファイルの中身を確認する
 */
function checkZipFile(){
    global $g;
    $uploadPath = $g['root_dir_path'] . '/temp/data_import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/data_import/import/';
    $uploadId = $_SESSION['upload_id'];
    $fileName = $uploadId . '_ita_data.tar.gz';

    // zip中身確認
    $fileAry = scandir($uploadPath . $uploadId);
    $fileAry = array_diff($fileAry, array('.', '..'));
    if (count($fileAry) === 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900016',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }
    $needleAry = array('JSQ_LIST', 'REQUEST', 'RIC_LIST', 'MENU_ID_TABLE_LIST', 'COPY_DIR_FILE_LIST');
    $errCnt = 0;
    foreach ($needleAry as $value) {
        $res = in_array($value, $fileAry);
        if ($res === false) {
            $errCnt++;
        }
    }
    if ($errCnt > 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900016',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    if ($errCnt > 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900016',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // ファイル移動
    $res = copy($uploadPath . '/' . $fileName, $importPath . '/' . $fileName);
    if ($res === false) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        if (file_exists($importPath . $fileName) === true) {
            unlink($importPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        removeFiles($importPath . $uploadId);

        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900039',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    mkdir($importPath . $uploadId);
    $from = $uploadPath . $uploadId;
    $to   = $importPath . '.';
    $cmd = "sudo cp -frp $from $to";
    exec($cmd);

    $errCnt = 0;
    foreach ($fileAry as $file) {
        $filePath = $importPath . $uploadId . '/' . $file;
        $res = file_exists($filePath);
        if ($res === false) {
            $errCnt++;
            break;
        }
    }

    if ($errCnt > 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        if (file_exists($importPath . $fileName) === true) {
            unlink($importPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        removeFiles($importPath . $uploadId);
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    removeFiles($uploadPath . $uploadId);

    return;
}

/**
 * インポートするメニューのチェックボックス作成
 *
 * @return   array     $retImportAry    インポートするメニューのチェックボックス一覧
 */
function makeImportCheckbox(){
    global $g;
    $path = $g['root_dir_path'] . '/temp/data_import/import/';

    if (isset($_SESSION['upload_id']) === false) {
        return;
    }

    $uploadId = $_SESSION['upload_id'];

    if (file_exists($path . $uploadId . '/REQUEST') === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-MNU-900005',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-MNU-900005'));
    } else {
        $requestJson = file_get_contents($path . $uploadId .'/REQUEST');
        $retImportAry = json_decode($requestJson, true);
        if (count($retImportAry) === 0) {
            return array();
        }

        return $retImportAry;
    }
}

/**
 * インポート用zipファイルを解凍する
 */
function unzipImportData(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $fileName = $uploadId . '_ita_data.tar.gz';
    $uploadPath = $g['root_dir_path'] . '/temp/data_import/upload/';
    $res = file_exists($uploadPath . $fileName);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900012',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    } else {

        mkdir($uploadPath . $uploadId);

        // tar.gzを展開する
        $output = NULL;
        $cmd = "sudo tar xvfz '" . $uploadPath . $fileName . "' -C '" . $uploadPath . $uploadId . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
        }
    }
    return;
}

/**
 * インポートするメニューIDのリストを作成する
 */
function makeImportMenuIdList(){
    $tmpMenuIdAry = $_POST;
    unset($tmpMenuIdAry['post_kind']);
    unset($tmpMenuIdAry['menu_on']);
    unset($tmpMenuIdAry['importButton']);
    unset($tmpMenuIdAry['importButton2']);
    $menuIdAry = array();
    foreach ($tmpMenuIdAry as $key => $v1) {
        $key = str_replace('import_', '', $key);
        foreach ($v1 as $v2) {
            $menuIdAry[] = sprintf("%010d", $v2);
        }
    }

    global $g;
    $filePath = $g['root_dir_path'] . '/temp/data_import/import/' . $_SESSION['upload_id'] . '/IMPORT_MENU_ID_LIST'; 
    $json = json_encode($menuIdAry);
    $res = file_put_contents($filePath , $json);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900060',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    return;
}

/**
 * 入力値チェック
 */
function checkInputFormat(){
    global $g;
    $errFlg = 0;
    if (strlen($_POST['menu_on']) === 0 || ctype_alnum($_POST['menu_on']) === false) {
        $errFlg = 1;
    }

    $requestAry = $_POST;
    unset($requestAry['post_kind']);
    unset($requestAry['menu_on']);
    unset($requestAry['importButton']);
    unset($requestAry['importButton2']);
    foreach ($requestAry as $menuGroupId =>$menuIds) {
        $menuGroupId = str_replace('import_', '', $menuGroupId);
        if (ctype_digit($menuGroupId) === false || strlen($menuGroupId) > MENU_ID_LENGTH) {
            $errFlg = 1;
        }
        foreach ($menuIds as $menuId) {
            if (ctype_digit($menuId) === false || strlen($menuId) > MENU_ID_LENGTH) {
                $errFlg = 1;
            }
        }
    }

    if ($errFlg === 1) {
        // 不正なアクセス
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-MNU-1140002'));
        webRequestForceQuitFromEveryWhere(400, 10310201);
    }

    return;
}

/**
 * zipファイルをuploadfilesに移動する
 */
function moveZipFile($taskNo){
    global $g;

    $uploadId = $_SESSION['upload_id'];
    $dpDir = $g['root_dir_path'].'/uploadfiles/2100000213';

    if (file_exists($dpDir) === false) {
        $res = mkdir($dpDir, 0777, true);
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900051',
                                                 array(basename(__FILE__), __LINE__)));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
        }
    }

    $src = $g['root_dir_path'] . '/temp/data_import/import/' . $uploadId . '_ita_data.tar.gz';
    $dst = $dpDir . '/' . $taskNo . '_' . $_SESSION['data_portability_upload_file_name'];
    $res = copy($src, $dst);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900063',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
    }

    unset($_SESSION['data_portability_upload_file_name']);
}

/**
 * データインポート管理テーブル更新処理
 */
function insertTask($importType){
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

    $filePath = $p_execution_utn_no . '_' . $_SESSION['data_portability_upload_file_name'];

    $arrayValue = array(
        'JOURNAL_SEQ_NO' => $p_execution_jnl_no,
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => $p_execution_utn_no,
        'TASK_STATUS' => 1,
        'DP_TYPE' => 2,
        'IMPORT_TYPE' => $importType,
        'FILE_NAME' => $filePath,
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
 * 指定したディレクトリ内のディレクトリとファイル一覧を取得する
 */
function getDirFileList($dir) {
    $retAry = array();
    $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir,
                        FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST);
                    
    foreach ($iterator as $fileinfo) {
        $retAry[] = $fileinfo->getPathname();
    }

    return $retAry;
}

/**
 * 再帰的にディレクトリとファイルをコピーする
 */
function recursiveCopyFiles($srcPath, $dstPath){
    global $g;

    if(!is_dir($dstPath)){
        $res = mkdir($dstPath);
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900039',
                                                 array(basename(__FILE__), __LINE__)));
            restoreTable();
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
        }
    }

    $output = NULL;
    $cmd = "sudo cp -rp " . $srcPath . "/* " . $dstPath . "/. 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
    return;
}

/**
 * 指定したディレクトリ内を再帰的に削除する
 */
function removeFiles($path){

    $output = NULL;
    $cmd = "sudo rm -rf $path 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }

    return;
}

/**
 * ディテクトリを再帰的にコピーする
 *
 * @param    int    $taskNo
 */
function renameImportFiles($taskNo){
    global $g;
    $src = $g['root_dir_path'] . '/temp/data_import/import/' . $_SESSION['upload_id'];
    $dst = $g['root_dir_path'] . '/temp/data_import/import/' . $taskNo;

    $output = NULL;
    $cmd = "sudo cp -frp $src $dst 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
}
