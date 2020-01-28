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
 * 設計情報インポート用関数群
 * 
 */

/**
 * zipファイルをアップロードする
 */
function uploadZipFile(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';

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

    $fileName = $_FILES['zipfile']['name'];
    $uploadFilePath = $uploadPath . $fileName;

    if(preg_match("/^[^,\"'\t\/\r\n]*$/s", $fileName) !== 1){
        web_log("The file name[{$fileName}] is invalid.");
        throw new Exception($g['objMTS']->getSomeMessage('ITAWDCH-ERR-513'));
    }

    // ファイル移動
    if (move_uploaded_file($_FILES['zipfile']['tmp_name'], $uploadFilePath) === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadFilePath) === true) {
            unlink($uploadFilePath);
        }
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    $_SESSION['upload_file_name'] = $fileName;

    return $fileName;
}

/**
 * インポート用zipファイルを解凍する
 */
function unzipImportData($fileName){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';
    $uploadWorkPath = "{$uploadPath}{$uploadId}";

    $res = file_exists($uploadPath . $fileName);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900012',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    } else {

        mkdir($uploadWorkPath);

        // tar.gzを展開する
        $output = NULL;
        $cmd = "sudo tar xvfzp '" . $uploadPath . $fileName . "' -C '" . $uploadWorkPath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            web_log("COMMAND=[{$cmd}].");
            web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        }
    }
    return;
}

/**
 * zipファイルの中身を確認する
 */
function checkZipFile($fileName){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';
    $uploadWorkPath = "{$uploadPath}{$uploadId}";

    // zip中身確認
    $fileAry = scandir($uploadWorkPath);
    $fileAry = array_diff($fileAry, array('.', '..'));
    if (count($fileAry) === 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadWorkPath, true);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900016',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }
    $needleAry = array('INFO_OPERATION', 'INFO_SYMPHONY', 'ita_base');
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
        removeFiles($uploadWorkPath, true);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900016',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // リリースファイルの確認
    $releaseFile = $g['root_dir_path'] . '/libs/release/ita_base';
    $exportReleaseFile = "{$uploadWorkPath}/ita_base";
    if(file_exists($releaseFile)){
        $releaseVersion = file_get_contents($releaseFile);

        if(file_exists($exportReleaseFile)){

            $exportReleaseVersion = file_get_contents($exportReleaseFile);

            if($releaseVersion != $exportReleaseVersion){
                if (file_exists($uploadPath . $fileName) === true) {
                    unlink($uploadPath . $fileName);
                }
                removeFiles($uploadWorkPath, true);
                web_log("Version of ITA = [{$releaseVersion}].  Version of import file = [{$exportReleaseVersion}].");
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900070'));
            }
        }
        else{
            if (file_exists($uploadPath . $fileName) === true) {
                unlink($uploadPath . $fileName);
            }
            removeFiles($uploadWorkPath, true);
            web_log("Version of ITA = [{$releaseVersion}].  Version of import file = [].");
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900070'));
        }
    }
    else{
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadWorkPath, true);
        web_log("File[{$releaseFile}] does not exists.");
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    return;
}

/**
 * インポートするメニューのチェックボックス作成
 *
 * @return   array     $retImportAry    インポートするメニューのチェックボックス一覧
 */
function makeImportCheckbox(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';
    $uploadWorkPath = "{$uploadPath}{$uploadId}";

    if (isset($_SESSION['upload_id']) === false) {
        return;
    }

    $uploadId = $_SESSION['upload_id'];

    if (file_exists("{$uploadWorkPath}/INFO_OPERATION") === false || file_exists("{$uploadWorkPath}/INFO_SYMPHONY") === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-MNU-900005',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-MNU-900003'));
    } else {
        $requestJson = file_get_contents("{$uploadWorkPath}/INFO_OPERATION");
        $ImportOpeAry = json_decode($requestJson, true);
        $requestJson = file_get_contents("{$uploadWorkPath}/INFO_SYMPHONY");
        $ImportSymAry = json_decode($requestJson, true);

        $retImportAry = array($ImportOpeAry, $ImportSymAry);

        return $retImportAry;
    }
}

/**
 * インポートするリストを作成する
 */
function makeImportIdList(){
    global $g;
    $tmpIdAry = $_POST;
    unset($tmpIdAry['post_kind']);
    unset($tmpIdAry['menu_on']);
    $operationAry = array();
    $symphonyAry = array();

    foreach ($tmpIdAry as $key => $valueAry) {
        if(false !== strpos($key, 'import_ope_')){
            $operationAry[] = $valueAry[0];
        }
        else if(false !== strpos($key, 'import_sym_')){
            $symphonyAry[] = $valueAry[0];
        }
    }

    $retStr = "SymphonyID:" . implode(",", $symphonyAry) . "\nOperationID:" . implode(",", $operationAry);
    return $retStr;
}

/**
 * データインポート管理テーブル更新処理
 */
function insertTask($targetList, &$seqNo, &$jnlSeqNo){
    global $g;

    $resArray = getSequenceLockInTrz('B_DP_SYM_OPE_STATUS_RIC','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    $resArray = getSequenceLockInTrz('B_DP_SYM_OPE_STATUS_JSQ','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    // 作業No.を取得する
    $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = 'B_DP_SYM_OPE_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    $seqAry = array();
    while ($row = $objQuery->resultFetch()){
        $seqAry[] = $row;
    }

    $seqNo = $seqAry[0]['VALUE'];

    // Jnl№を取得する
    $resArray = array();
    $resArray = getSequenceValueFromTable('B_DP_SYM_OPE_STATUS_JSQ', 'A_SEQUENCE', FALSE);
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }
    $jnlSeqNo = $resArray[0];

    $arrayConfig = array(
        'JOURNAL_SEQ_NO' => '',
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => '',
        'TASK_STATUS' => '',
        'DP_TYPE' => '',
        'FILE_NAME' => '',
        'DISP_SEQ' => '',
        'NOTE' => '',
        'DISUSE_FLAG' => '',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => ''
    );

    $arrayValue = array(
        'JOURNAL_SEQ_NO' => $jnlSeqNo,
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => $seqNo,
        'TASK_STATUS' => 1,
        'DP_TYPE' => 2,
        'FILE_NAME' => $_SESSION['upload_file_name'],
        'DISP_SEQ' => '',
        'NOTE' => $targetList,
        'DISUSE_FLAG' => '0',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => $g['login_id']
    );

    $resAry = makeSQLForUtnTableUpdate(
        $g['db_model_ch'],
        'INSERT',
        'TASK_ID',
        'B_DP_SYM_OPE_STATUS',
        'B_DP_SYM_OPE_STATUS_JNL',
        $arrayConfig,
        $arrayValue
    );

    if ($resAry[0] === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900046',
                                             array('B_DP_SYM_OPE_STATUS', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
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
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    if ($objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "") {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    $rUtn = $objQueryUtn->sqlExecute();
    if ($rUtn != true) {
        web_log("SQL=[{$sqlUtnBody}].");
        web_log($objQueryUtn->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    $rJnl = $objQueryJnl->sqlExecute();
    if ($rJnl != true) {
        web_log("SQL=[{$sqlJnlBody}].");
        web_log($objQueryJnl->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS_JNL')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    // 更新系のシーケンスを増やす
    $p_execution_utn_next_no = $seqNo + 1;
    $sql = "UPDATE A_SEQUENCE set VALUE = :value WHERE NAME = 'B_DP_SYM_OPE_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }
    
    $res = $objQuery->sqlBind(array('value' => $p_execution_utn_next_no));
    if ($res != "") {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }
    $res = $objQuery->sqlExecute();
    if ($res != true) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    return;
}

/**
 * zipファイルをuploadfilesに移動する
 */
function moveImportFile($taskNo, $jnlSeqNo){
    global $g;

    $uploadId = $_SESSION['upload_id'];
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';
    $uploadWorkPath = "{$uploadPath}{$uploadId}";

    // uploadfilesにディレクトリを作成
    $pathArray = array();
    $pathArray[0] = $g['root_dir_path'] . '/uploadfiles/2100000403/';
    $pathArray[1] = $pathArray[0] . 'FILE_NAME/';
    $pathArray[2] = $pathArray[1] . sprintf("%010d", $taskNo) . '/';
    $pathArray[3] = $pathArray[2] . 'old/';
    $pathArray[4] = $pathArray[3] . sprintf("%010d", $jnlSeqNo) . '/';
    $uploadFilesDir = $pathArray[2];
    $uploadFilesDirJnl = $pathArray[4];

    foreach($pathArray as $path){

        if(!file_exists($path)){
            $mask = umask();
            umask(000);
            $result = mkdir($path, 0777, true);
            umask($mask);

            if(true != $result){
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900051',
                                                     array(basename(__FILE__), __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }
            chmod($path, 0777);
        }
    }

    // ファイルを移動
    $src = $uploadPath . $_SESSION['upload_file_name'];
    $output = NULL;
    $cmd = "sudo mv '{$src}' '{$uploadFilesDir}' 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log("COMMAND=[{$cmd}].");
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    // ファイルをコピー(JNL)
    $output = NULL;
    $cmd = "sudo cp -p '{$uploadFilesDir}" . $_SESSION['upload_file_name'] . "' '{$uploadFilesDirJnl}' 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log("COMMAND=[{$cmd}].");
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
    }

    unset($_SESSION['upload_file_name']);
    removeFiles($uploadWorkPath, true);
}

/**
 * 指定したディレクトリ内を再帰的に削除する
 */
function removeFiles($path, $recursive=false){
    global $g;

    if ($recursive === true) {
        if (file_exists($path) === true) {
            $output = NULL;
            $cmd = "sudo rm -rf '" . $path . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }
        }
    }
    else{
        if (file_exists($path) === true) {
            $output = NULL;
            $cmd = "sudo rm -rf '" . $path . "/*' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }
        }
    }
    return;
}
