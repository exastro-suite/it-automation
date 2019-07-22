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
 * 【概要】
 *  テーブルに登録されたデータインポートのタスクを実行する
 *
 */

if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}

define('ROOT_DIR_PATH',        $root_dir_path);
define('EXPORT_PATH',          ROOT_DIR_PATH . '/temp/data_export/');
define('IMPORT_PATH',          ROOT_DIR_PATH . '/temp/data_import/import/');
define('BACKUP_PATH',          ROOT_DIR_PATH . '/temp/data_import/backup/');
define('UPLOADFILES_PATH',     ROOT_DIR_PATH . '/temp/data_import/uploadfiles/');
define('LOG_DIR',              '/logs/backyardlogs/');
define('LOG_LEVEL',            getenv('LOG_LEVEL'));
define('LAST_UPDATE_USER',     -100024); // データポータビリティプロシージャ
define('STATUS_RUNNING',       2); // 実行中
define('STATUS_PROCESSED',     3); // 完了
define('STATUS_FAILURE',       4); // 完了(異常) 
define('LOG_PREFIX',           basename( __FILE__, '.php' ) . '_');

try {
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_php_req_gate.php';
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_db_connect.php';
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/ita_base/common_data_portability.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_functions_for_menu_info.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_php_functions.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_parts_for_request_init.php';

    if (LOG_LEVEL === 'DEBUG') {
        // 処理開始ログ
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900003'));
    }
    // DB接続情報取得
    $paramAry = getDbConnectParams();
    define('DB_USER',   $paramAry['user']);
    define('DB_PW',     $paramAry['password']);
    define('DB_HOST',   $paramAry['host']);
    define('DB_NAME',   $paramAry['dbname']);

    // 設定時間を超えても実行中のレコードがある場合は処理を終了する
    $recordCnt = (int)getRunningRecord();
    if ($recordCnt > 0) {
        $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900062',
                                          array(basename(__FILE__), __LINE__));
        throw new Exception($logMsg);
    }

    // 未実行のレコードがない場合は処理を終了する
    $recordAry = getUnexecutedRecord();
    if (is_array($recordAry) === true) {
        if (count($recordAry) === 0) {
            if (LOG_LEVEL === 'DEBUG') {
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900004',
                                                  array(basename(__FILE__), __LINE__)));
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900005'));
            }
            exit;
        }
    } else {
        throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
    }

    // サービス一覧を取得する
    $serviceAry = getService();

    $importedTableAry = array();
    foreach ($recordAry as $record) {

        $res = setStatus($record['TASK_ID'], STATUS_RUNNING);
        if ($res === false) {
            $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                              array('B_DP_STATUS',basename(__FILE__), __LINE__));
            outputLog(LOG_PREFIX, $logMsg);
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            continue;
        }

        // データエクスポートを行う
        if(1 == $record['DP_TYPE']){
            $result = exportData($record);

            if(false !== $result){
                $exportFile = $result;

                // uploadfilesにディレクトリを作成
                $uploadDir = ROOT_DIR_PATH . '/uploadfiles/2100000213/';

                if(!is_dir($uploadDir)){
                    $output = NULL;
                    $cmd = "sudo mkdir '{$uploadDir}' 2>&1";
                    exec($cmd, $output, $return_var);

                    if(0 != $return_var){
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                        outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
                        setStatus($record['TASK_ID'], STATUS_FAILURE);
                        continue;
                    }

                    // 権限を777にする
                    $output = NULL;
                    $cmd = "sudo chmod 777 '{$uploadDir}' 2>&1";
                    exec($cmd, $output, $return_var);

                    if(0 != $return_var){
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                        outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
                        setStatus($record['TASK_ID'], STATUS_FAILURE);
                        continue;
                    }
                }

                // exportファイルを移動
                $output = NULL;
                $cmd = "sudo mv '" . ROOT_DIR_PATH . "/temp/data_export/{$exportFile}' '{$uploadDir}{$exportFile}' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                    outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
                    setStatus($record['TASK_ID'], STATUS_FAILURE);
                    continue;
                }

                // ステータスを処理済みにする
                $res = setStatus($record['TASK_ID'], STATUS_PROCESSED, $exportFile);

                if ($res !== false) {

                    removeFiles(EXPORT_PATH . $record['TASK_ID'], true);
                }
                else{
                    setStatus($record['TASK_ID'], STATUS_FAILURE);
                }
            }
            else{
                setStatus($record['TASK_ID'], STATUS_FAILURE);
            }

            continue;
        }

        // サービスを停止する
        stopService($serviceAry);

        $taskId = registData($record, $importedTableAry);
        if ($taskId === false) {
            restoreTables();
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        $dstPath = UPLOADFILES_PATH . $taskId;
        $res = mkdir($dstPath);
        if ($res === false) {
            outputLog(LOG_PREFIX, "Failed to create directory[{$dstPath}]. FILE:" . basename(__FILE__) . " LINE:" . __LINE__);
            restoreTables();
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        // ファイルをバックアップする
        $res = fileBackup($taskId);
        if ($res === false) {
            restoreTables();
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        // ファイルをコピーする
        $res = fileImport($taskId);
        if ($res === false) {
            restoreTables();
            restoreFiles($taskId);
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        // ファイルの確認
        $res = checkCopyFiles($taskId);
        if ($res === false) {
            restoreTables();
            restoreFiles($taskId);
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        $dstPath = UPLOADFILES_PATH . $taskId;
        $res = removeFiles($dstPath);

        $dstPath = IMPORT_PATH . $taskId;
        $res = removeFiles($dstPath);

        // ステータスを処理済みにする
        $res = setStatus($record['TASK_ID'], STATUS_PROCESSED);

        if ($res === false) {
            restoreTables();
            restoreFiles($taskId);
            setStatus($record['TASK_ID'], STATUS_FAILURE);
            // サービスを開始する
            startpService($serviceAry);
            continue;
        }

        // 正常終了時はバックアップファイルを削除する
        if (file_exists(BACKUP_PATH . 'backup.sql') === true) {
            unlink(BACKUP_PATH . 'backup.sql');
        }

        // サービスを開始する
        startpService($serviceAry);
    }

    // 処理済みフラグをクリアする
    clearExecFlg();

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900005'));
    }
} catch (Exception $e) {
    outputLog(LOG_PREFIX, $e->getMessage());
}

/**
 * データをインポートする
 */
function registData($record, &$importedTableAry){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900008',
                                                      array(__FILE__, __LINE__)));
    }

    $taskId = $record['TASK_ID'];
    $importPath = IMPORT_PATH . $taskId;
    $tmpTableAry = getMenuIdTableList($importPath);

    $ricJson = file_get_contents($importPath . '/RIC_LIST');
    $ricAry = json_decode($ricJson, true);
    $jsqJson = file_get_contents($importPath . '/JSQ_LIST');
    $jsqAry = json_decode($jsqJson, true);
    $otherSeqAry = array();
    if(file_exists($importPath . '/OTHER_SEQ_LIST')){
        $otherSeqJson = file_get_contents($importPath . '/OTHER_SEQ_LIST');
        $otherSeqAry = json_decode($otherSeqJson, true);
    }

    // インポート時にPOSTされたMENU_IDを取得
    $menuIdJson = file_get_contents($importPath . '/IMPORT_MENU_ID_LIST');
    $menuIdAry = json_decode($menuIdJson, true);
    $tableAry = array();
    $lockLAry = array();
    foreach ($menuIdAry as $menuId) {
        $tableAry[$menuId] = $tmpTableAry[$menuId];
        $lockLAry[] = $tmpTableAry[$menuId]['SEQUENCE_JSQ'];
        $lockLAry[] = $tmpTableAry[$menuId]['SEQUENCE_RIC'];
        if(array_key_exists('SEQUENCE_OTHER', $tmpTableAry[$menuId]) && 0 < count($tmpTableAry[$menuId]['SEQUENCE_OTHER'])){
            foreach($tmpTableAry[$menuId]['SEQUENCE_OTHER'] as $seqName){
                $lockLAry[] = $seqName;
            }
        }
    }

    // 更新前に対象テーブルをバックアップ
    $res = backupTable($tableAry);
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900018',
                                          array(basename(__FILE__), __LINE__)));
        return false;
    }

    // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
    asort($lockLAry);
    array_unique($lockLAry);

    // シーケンステーブルをロックする
    foreach($lockLAry as $strSeqName) {
        //ジャーナルのシーケンス
        $retArray = getSequenceLockInTrz($strSeqName, "A_SEQUENCE");
    }

    $tmpTblAry = scandir($importPath);
    $tmpTblAry = array_diff($tmpTblAry, array('.', '..'));

    foreach($tableAry as $key => $table) {

        // シーケンス番号の更新
        $seqValue = $ricAry[$table['SEQUENCE_RIC']];
        $res = updateSequence(array('name' => $table['SEQUENCE_RIC'], 'value' => $seqValue));
        if ($res === false) {
            return false;
        }

        $seqValue = $jsqAry[$table['SEQUENCE_JSQ']];
        $res = updateSequence(array('name' => $table['SEQUENCE_JSQ'], 'value' => $seqValue));
        if ($res === false) {
            return false;
        }

        if(array_key_exists('SEQUENCE_OTHER', $table) && 0 < count($table['SEQUENCE_OTHER'])){
            foreach($table['SEQUENCE_OTHER'] as $seqName){
                $seqValue = $otherSeqAry[$seqName];
                $res = updateSequence(array('name' => $seqName, 'value' => $seqValue));
                if ($res === false) {
                    return false;
                }
            }
        }

        // 更新系テーブルinsert
        $tblAry = array();
        foreach($tmpTblAry as $tbl) {
            if (strpos($tbl, $key . '_' . $table['TABLE_NAME'] . '_' . $record['IMPORT_TYPE']) !== false && 
                strpos($tbl, $key . '_' . $table['JNL_TABLE_NAME'] . '_' . $record['IMPORT_TYPE']) === false) {
                $tblAry[] = $tbl;
            }
        }
        // インポート済みのファイル名を保存する配列
        foreach($tblAry as $tbl) {
            $tmpFileName = explode('_', $tbl);
            unset($tmpFileName[0]);
            $fileName = implode('_', $tmpFileName);
            // 共通使用されているテーブルに複数回INSERTしないようにする
            if (in_array($fileName, $importedTableAry) === true) {
                continue;
            }

            $importedTableAry[] = $fileName;

            $res = insertTable($table['TABLE_NAME'], $importPath . '/'. $tbl);
            if ($res === false) {
                return false;
            }
        }

        // 履歴系テーブルinsert
        $tblAry = array();
        foreach($tmpTblAry as $tbl) {
            if (strpos($tbl, $key . '_' . $table['JNL_TABLE_NAME'] . '_' . $record['IMPORT_TYPE']) !== false ) {
                $tblAry[] = $tbl;
            }
        }
        foreach($tblAry as $tbl) {
            $tmpFileName = explode('_', $tbl);
            unset($tmpFileName[0]);
            $fileName = implode('_', $tmpFileName);
            // 共通使用されているテーブルに複数回INSERTしないようにする
            if (in_array($fileName, $importedTableAry) === true) {
                continue;
            }

            $importedTableAry[] = $fileName;

            $res = insertTable($table['JNL_TABLE_NAME'], $importPath . '/'. $tbl);
            if ($res === false) {
                return false;
            }
        }

        // ビューinsert
        $tblAry = array();
        foreach($tmpTblAry as $tbl) {
            if ("" != $table['VIEW_NAME'] &&
                strpos($tbl, $key . '_' . $table['VIEW_NAME']) !== false && 
                strpos($tbl, $key . '_' . $table['JNL_VIEW_NAME']) === false) {
                $tblAry[] = $tbl;
            }
            if ("" != $table['JNL_VIEW_NAME'] &&
                strpos($tbl, $key . '_' . $table['JNL_VIEW_NAME']) !== false ) {
                $tblAry[] = $tbl;
            }
        }
        foreach($tblAry as $tbl) {
            $tmpFileName = explode('_', $tbl);
            unset($tmpFileName[0]);
            $fileName = implode('_', $tmpFileName);
            // 共通使用されているテーブルに複数回INSERTしないようにする
            if (in_array($fileName, $importedTableAry) === true) {
                continue;
            }

            $importedTableAry[] = $fileName;

            $res = insertView($importPath . '/'. $tbl);
            if ($res === false) {
                return false;
            }
        }
    }

    return $taskId;
}

/*
 * 未実行レコードを取得する
 */
function getUnexecutedRecord(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $sql  = 'SELECT TASK_ID, DP_TYPE, IMPORT_TYPE, FILE_NAME';
    $sql .= ' FROM B_DP_STATUS';
    $sql .= ' WHERE TASK_STATUS = 1';
    $sql .= " AND DISUSE_FLAG = '0'";
    $sql .= ' ORDER BY TASK_ID ASC';

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $resAry = array();
    while ($row = $objQuery->resultFetch()) {
        $resAry[] = $row;
    }

    return $resAry;
}

/**
 * メニューIDに紐づくテーブル名を取得
 *
 * @param    string    $importPath    インポート用データ保存ディレクトリ
 * @return   array     $tableAry
 */
function getMenuIdTableList($importPath){
    global $objMTS;
    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900010',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $tableAry =  array();
    if (file_exists($importPath . '/MENU_ID_TABLE_LIST') === true) {
        $json = file_get_contents($importPath . '/MENU_ID_TABLE_LIST');
        $tableAry = json_decode($json, true);
    }

    return $tableAry;
}

/**
 * テーブルをバックアップする
 *
 * @param    array    $tableAry    メニューIDとテーブルの情報
 */
function backupTable($tableAry){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900011',
                                                      array(basename(__FILE__), __LINE__)));
    }

    // バックアップするテーブル
    $tableNameAry = array();
    $tableNameAry[] = 'A_SEQUENCE';
    foreach ($tableAry as $table) {
        $tableNameAry[] = $table['TABLE_NAME'];
        $tableNameAry[] = $table['JNL_TABLE_NAME'];
    }

    $tableNameAry = array_values(array_unique($tableNameAry));

    // dump取得
    $tableNames = implode(' ', $tableNameAry);
    $filePath = BACKUP_PATH  . 'backup.sql';
    $cmd  = 'mysqldump -u ' . DB_USER . ' -p' . DB_PW;
    $cmd .= ' -h' . DB_HOST;
    $cmd .= ' ' . DB_NAME . ' ' . $tableNames;
    $cmd .= ' 2>/dev/null > ' . $filePath;
    shell_exec($cmd);

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $cmd);
    }

    if (file_exists($filePath) === false || filesize($filePath) === 0) {
        return false;
    }
}

/**
 * DB接続情報を取得する
 *
 * @return   array    $retAry    DB接続情報
 */
function getDbConnectParams(){
    global $objMTS;    

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900012',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $path = ROOT_DIR_PATH . '/confs/commonconfs/db_connection_string.txt';
    $tmp = file_get_contents($path);
    $tmp = ky_decrypt($tmp);
    $tmpAry = explode(';', $tmp);
    $retAry = array();
    foreach ($tmpAry as $param) {
        if (strpos($param, 'dbname') === false) {
            $retAry['host'] = str_replace('host=', '', $param);
        } else {
            $retAry['dbname'] = str_replace('mysql:dbname=', '', $param);
        }
    }

    $tmp = ROOT_DIR_PATH . '/confs/commonconfs/db_username.txt';
    $retAry['user'] = ky_decrypt(file_get_contents($tmp));
    $tmp = ROOT_DIR_PATH . '/confs/commonconfs/db_password.txt';
    $retAry['password'] = ky_decrypt(file_get_contents($tmp));

    return $retAry;
}

/**
 * 指定したディレクトリ内を再帰的に削除する
 */
function removeFiles($path, $recursive=false){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900013',
                                                      array($path, basename(__FILE__), __LINE__)));
    }

    $output = NULL;
    $cmd = "rm -rf $path/* 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, "Failed to delete. Path=[{$path}]. error=[" . print_r($output, true) . "] FILE:" . basename(__FILE__) . " LINE:" . __LINE__);
    }

    if ($recursive === true) {
        $output = NULL;
        $cmd = "rm -rf $path 2>&1";

        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, "Failed to delete. Path=[{$path}]. error=[" . print_r($output, true) . "] FILE:" . basename(__FILE__) . " LINE:" . __LINE__);
        }
    }

    return true;
}

/**
 * シーケンス番号を更新する
 *
 * @param    array    $paramAry    各テーブルのシーケンス名とシーケンス番号
 */
function updateSequence($paramAry){
    global $objDBCA, $objMTS;
    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $sql  = 'SELECT NAME,VALUE FROM A_SEQUENCE';
    $sql .= ' WHERE NAME = :name';

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array('name' => $paramAry['name']));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $count = 0;
    while ($row = $objQuery->resultFetch()) {
        $count++;
    }

    if(1 === $count){

        $sql  = 'UPDATE A_SEQUENCE set VALUE = :value';
        $sql .= ' WHERE NAME = :name';

        if (LOG_LEVEL === 'DEBUG') {
            outputLog(LOG_PREFIX, $sql);
        }

        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        $res = $objQuery->sqlBind($paramAry);
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
    }

    else{

        $sql  = 'INSERT INTO A_SEQUENCE(NAME,VALUE) VALUES(:name,:value)';

        if (LOG_LEVEL === 'DEBUG') {
            outputLog(LOG_PREFIX, $sql);
        }

        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        $res = $objQuery->sqlBind($paramAry);
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
    }

    return true;
}

/**
 * テーブルをリストアする
 */
function restoreTables(){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900016',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $path = BACKUP_PATH;
    $fileAry = scandir($path);
    $fileAry = array_diff($fileAry, array('.', '..'));

    // バックアップファイルでリストア
    $filePath = $path . 'backup.sql';

    // バックアップファイルが無い場合は処理終了
    if(!file_exists($filePath)){
        return;
    }

    $cmd  = 'mysql -u ' . DB_USER . ' -p' . DB_PW;
    $cmd .= ' -h' . DB_HOST;
    $cmd .= ' ' . DB_NAME;
    $cmd .= ' 2>/dev/null < ' . $filePath;
    shell_exec($cmd);

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $cmd);
    }

    return;
}

/**
 * テーブルにレコードを登録する
 *
 * @param    string    $tableName    テーブル名
 * @param    array     $recordAry    登録するレコード
 * @return   なし
 */
function insertTable($tableName, $filePath){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900017',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $cmd  = 'mysql -u ' . DB_USER . ' -p' . DB_PW;
    $cmd .= ' -h' . DB_HOST;
    $cmd .= ' ' . DB_NAME;
    $cmd .= ' 2>&1 < ' . $filePath;

    $output = NULL;
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, "Failed to import data. file=[{$filePath}]. error=[" . print_r($output, true) . "] FILE:" . basename(__FILE__) . " LINE:" . __LINE__);
        return false;
    }

    // トランザクション開始
    $res = $objDBCA->transactionStart();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                      array(basename(__FILE__), __LINE__)));
        return false;
    }

    // 最終更新者をデータポータビリティプロシージャにする
    $record['LAST_UPDATE_USER'] = LAST_UPDATE_USER;

    $sql  = "UPDATE {$tableName} SET LAST_UPDATE_USER=:LAST_UPDATE_USER WHERE LAST_UPDATE_USER > 0";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        $res = $objDBCA->transactionRollback();
        return false;
    }
    
    $res = $objQuery->sqlBind($record);
    if ($res != "") {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        $res = $objDBCA->transactionRollback();
        return false;
    }
    $res = $objQuery->sqlExecute();
    if ($res != true) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900020',
                                                      array($tableName, basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        $res = $objDBCA->transactionRollback();
        return false;
    }

    // コミットする
    $res = $objDBCA->transactionCommit();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900036',
                                                      array(basename(__FILE__), __LINE__)));
        $res = $objDBCA->transactionRollback();
        return false;
    }


    return true;
}

/**
 * ビューを登録する
 *
 * @param    array     $recordAry    登録するレコード
 * @return   なし
 */
function insertView($filePath){
    global $objDBCA, $objMTS;

    $cmd  = 'mysql -u ' . DB_USER . ' -p' . DB_PW;
    $cmd .= ' -h' . DB_HOST;
    $cmd .= ' ' . DB_NAME;
    $cmd .= ' 2>&1 < ' . $filePath;

    $output = NULL;
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, "Failed to import data. file=[{$filePath}]. error=[" . print_r($output, true) . "] FILE:" . basename(__FILE__) . " LINE:" . __LINE__);
        return false;
    }

    return true;
}

/**
 * ファイルをバックアップする
 */
function fileBackup($taskId){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900020',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $dstPath = UPLOADFILES_PATH . $taskId;
    $importPath = IMPORT_PATH . $taskId;
    $pathAry = array();
    $json = file_get_contents($importPath . '/COPY_DIR_FILE_LIST');
    $exportAry = json_decode($json, true);
    $json = file_get_contents($importPath . '/IMPORT_MENU_ID_LIST');
    $importAry = json_decode($json, true);

    $tmpAry = array();
    foreach($importAry as $key => $menuId) {
        if (array_key_exists($menuId, $exportAry) === true) {
            $tmpAry[$key] = $exportAry[$menuId];
        }
    }
    if (count($tmpAry) === 0) {
        return;
    }

    $dirAry = array();
    foreach ($tmpAry as $ary) {
        foreach($ary as $path) {
            $dirAry[] = $path;
        }
    }

    $dirAry = array_unique($dirAry);
    foreach ($dirAry as $dir) {

        if(is_dir(ROOT_DIR_PATH . "/" . $dir)){

            // コピー後のチェックのためファイル一覧を取得しておく
            $resAry = getDirFileList(ROOT_DIR_PATH . "/" . $dir);
            foreach ($resAry as $path) {
                $pathAry[] = str_replace(ROOT_DIR_PATH, $dstPath, $path);
            }

            // コピー
            $output = NULL;
            $cmd = "cd " . ROOT_DIR_PATH . ";cp -rp --parent .$dir $dstPath 2>&1";

            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                outputLog(LOG_PREFIX, "command=[{$cmd}]");
                return false;
            }

            // コピーできたかを確認する
            foreach ($pathAry as $path) {
                if (file_exists($path) === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900043',
                                                                  array(basename(__FILE__), __LINE__)));
                    return false;
                }
            }

            removeFiles(ROOT_DIR_PATH . "/" . $dir);
        }
    }

    return true;
}

/**
 * 指定したディレクトリ内のディレクトリとファイル一覧を取得する
 */
function getDirFileList($dir) {
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900021',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $retAry = array();

    if(is_dir($dir)){
        $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($dir,
                            FilesystemIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $fileinfo) {
            $retAry[] = $fileinfo->getPathname();
        }
    }

    return $retAry;
}

/**
 * ディレクトリとファイルをインポートする
 */
function fileImport($taskId){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900022',
                                                      array($taskId, basename(__FILE__), __LINE__)));
    }

    $srcPath = IMPORT_PATH . $taskId;

    $json = file_get_contents($srcPath . '/COPY_DIR_FILE_LIST');
    $exportAry = json_decode($json, true);
    $json = file_get_contents($srcPath . '/IMPORT_MENU_ID_LIST');
    $importAry = json_decode($json, true);

    $tmpAry = array();
    foreach($importAry as $key => $menuId) {
        if (array_key_exists($menuId, $exportAry) === true) {
            $tmpAry[$key] = $exportAry[$menuId];
        }
    }
    if (count($tmpAry) === 0) {
        return;
    }

    $dirAry = array();
    foreach ($tmpAry as $ary) {
        foreach($ary as $path) {
            $dirAry[] = $path;
        }
    }

    $dirAry = array_unique($dirAry);
    foreach ($dirAry as $dir) {

        $output = NULL;
        $cmd = "cd " . IMPORT_PATH . $taskId . ";cp -rp --parents .$dir " . ROOT_DIR_PATH . " 2>&1";

        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            outputLog(LOG_PREFIX, "command=[{$cmd}]");
            return false;
        }
    }

}

/**
 * ディレクトリとファイルをリストアする
 */
function restoreFiles($taskId){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900023',
                                                      array($taskId, basename(__FILE__), __LINE__)));
    }

    $dstPath = UPLOADFILES_PATH . $taskId;
    $importPath = IMPORT_PATH . $taskId;
    $pathAry = array();
    $json = file_get_contents($importPath . '/COPY_DIR_FILE_LIST');
    $exportAry = json_decode($json, true);
    $json = file_get_contents($importPath . '/IMPORT_MENU_ID_LIST');
    $importAry = json_decode($json, true);

    $tmpAry = array();
    foreach($importAry as $key => $menuId) {
        if (array_key_exists($menuId, $exportAry) === true) {
            $tmpAry[$key] = $exportAry[$menuId];
        }
    }
    if (count($tmpAry) === 0) {
        return;
    }

    $dirAry = array();
    foreach ($tmpAry as $ary) {
        foreach($ary as $path) {
            $dirAry[] = $path;
        }
    }

    $dirAry = array_unique($dirAry);
    foreach ($dirAry as $dir) {

        if(is_dir(ROOT_DIR_PATH . "/" . $dir)){
            removeFiles(ROOT_DIR_PATH . "/" . $dir, true);
        }

        if(is_dir($dstPath . $dir)){

            // コピー
            $output = NULL;
            $cmd = "cp -rp $dstPath$dir " . ROOT_DIR_PATH . " 2>&1";

            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                outputLog(LOG_PREFIX, "command=[{$cmd}]");
                return false;
            }
        }
    }

    return true;
}

/**
 * 再帰的にディレクトリとファイルをコピーする
 *
 * @param    string    $srcPath    コピー元ディレクトリ
 * @param    string    $dstPath    コピー先ディレクトリ
 * @return   bool
 */
function recursiveCopyFiles($srcPath, $dstPath){
    global $objMTS;

    if(!is_dir($dstPath)){
        $res = mkdir($dstPath);
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900039',
                                                          array(basename(__FILE__), __LINE__)));
            return false;
        }
        chmod($dstPath, 0777);

    }

    $output = NULL;
    $cmd = "cp -rp " . $srcPath . "/* " . $dstPath . "/. 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        outputLog(LOG_PREFIX, "command=[{$cmd}]");
        return false;
    }

    return true;
}

/**
 * ファイルコピーチェック
 *     「COPY_DIR_FILE_LIST」の内容をループして
 *      ディレクトリとファイルの存在を確認する
 *
 * @param    int    $taskId    タスクID
 * @return   bool
 */
function checkCopyFiles($taskId){
    global $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900024',
                                                      array($taskId, basename(__FILE__), __LINE__)));
    }

    $checkListPath = ROOT_DIR_PATH . '/temp/data_import/import/' . $taskId  . '/';
    $checkListFile = $checkListPath . 'COPY_DIR_FILE_LIST';
    $json = file_get_contents($checkListFile);
    $checkList = json_decode($json, true);
    $checkFilesAry = array();

    $menuIdListFile = $checkListPath . 'IMPORT_MENU_ID_LIST';
    $json = file_get_contents($menuIdListFile);
    $menuIdAry = json_decode($json, true);
    foreach($menuIdAry as $menuId) {
        if (array_key_exists($menuId, $checkList) === true) {
            $tmpFilesAry = array();
            $tmpFilesAry = $checkList[$menuId];
            $checkFilesAry = array_merge($checkFilesAry, $tmpFilesAry);
        }
    }

    foreach($checkFilesAry as $file) {
        if (file_exists(ROOT_DIR_PATH . $file) === false) {
            if (LOG_LEVEL === 'DEBUG') {
                outputLog(LOG_PREFIX, $file);
            }
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900039',
                                                          array(basename(__FILE__), __LINE__)));
            return false;
        }
    }

    return true;
}

/**
 * ステータスを更新する
 *
 * @param    int    $taskId        タスクID（プライマリキー)
 * @param    int    $status        ステータス
 *                                     1:未実行
 *                                     2:実行中
 *                                     3:処理済み
 *                                     4:失敗
 */
function setStatus($taskId, $status, $uploadFile=NULL){
    global $objMTS, $objDBCA, $db_model_ch;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900025',
                                                      array($taskId, $status,
                                                            basename(__FILE__), __LINE__)));
    }

    if($status != STATUS_PROCESSED){
        $res = $objDBCA->transactionStart();
        if ($res === false) {
            $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900015',
                                              array(basename(__FILE__), __LINE__));
            outputLog(LOG_PREFIX, $logMsg);
            return;
        }
    }
    $errFlg = 0;
    $resArray = getSequenceLockInTrz('B_DP_STATUS_RIC', 'A_SEQUENCE');
    if ($resArray[1] != 0) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900052',
                                                      array('A_SEQUENCE', 'B_DP_STATUS_RIC',
                                                      basename(__FILE__), __LINE__)));
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    $resArray = getSequenceLockInTrz('B_DP_STATUS_JSQ', 'A_SEQUENCE');
    if ($resArray[1] != 0) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900052',
                                                      array('A_SEQUENCE', 'B_DP_STATUS_RIC',
                                                      basename(__FILE__), __LINE__)));
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050', 
                                                      array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = 'B_DP_STATUS_RIC'";
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                          array('A_SEQUENCE', 'B_DP_STATUS_RIC', basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                      array('A_SEQUENCE', 'B_DP_STATUS_RIC',
                                                      basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $p_execution_utn_no = $resArray[0];

    // Jnl№を取得する
    $resArray = getSequenceValueFromTable('B_DP_STATUS_JSQ', 'A_SEQUENCE', FALSE);
    if ($resArray[1] != 0) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                      array(basename(__FILE__), __LINE__)));
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }
    $p_execution_jnl_no = $resArray[0];

    // 更新系テーブルの情報取得
    $resAry = getRecordById($taskId);
    if ($resAry === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900058',
                                                      array(basename(__FILE__), __LINE__)));
        return false;
    }

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

    if(NULL !== $uploadFile){
        $fileName = $uploadFile;
    }
    else{
        $fileName = $resAry[0]['FILE_NAME'];
    }

    $arrayValue = array(
        'JOURNAL_SEQ_NO' => $p_execution_jnl_no,
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => $resAry[0]['TASK_ID'],
        'TASK_STATUS' => $status,
        'DP_TYPE' => $resAry[0]['DP_TYPE'],
        'IMPORT_TYPE' => $resAry[0]['IMPORT_TYPE'],
        'FILE_NAME' => $fileName,
        'DISP_SEQ' => $resAry[0]['DISP_SEQ'],
        'NOTE' => $resAry[0]['NOTE'],
        'DISUSE_FLAG' => $resAry[0]['DISUSE_FLAG'],
        'LAST_UPDATE_TIMESTAMP' => $resAry[0]['LAST_UPDATE_TIMESTAMP'],
        'LAST_UPDATE_USER' => $resAry[0]['LAST_UPDATE_USER']
    );

    $tmpAry = array();

    $resAry = makeSQLForUtnTableUpdate($db_model_ch,
                                         'UPDATE',
                                         'TASK_ID',
                                         'B_DP_STATUS',
                                         'B_DP_STATUS_JNL',
                                         $arrayConfig,
                                         $arrayValue,
                                         $tmpAry );

    $sqlUtnBody = $resAry[1];
    $arrayUtnBind = $resAry[2];
    $sqlJnlBody = $resAry[3];
    $arrayJnlBind = $resAry[4];

    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

    if ($objQueryUtn->getStatus() === false || $objQueryJnl->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $taskId)));
        outputLog(LOG_PREFIX, $objQueryUtn->getLastError());
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    if ($objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "") {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $taskId)));
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    $objQueryUtn->sqlBind(array('TASK_ID' => $taskId));

    $rUtn = $objQueryUtn->sqlExecute();
    if ($rUtn !== true) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $taskId)));
        outputLog(LOG_PREFIX, $objQueryUtn->getLastError());
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    $objQueryJnl->sqlBind($arrayJnlBind);

    $rJnl = $objQueryJnl->sqlExecute();
    if ($rJnl !== true) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $taskId)));
        outputLog(LOG_PREFIX, $objQueryJnl->getLastError());
        $res = $objDBCA->transactionRollback();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900050',
                                                          array(basename(__FILE__), __LINE__)));
        }
        return false;
    }

    if($status != STATUS_PROCESSED){
        $res = $objDBCA->transactionCommit();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900036',
                                                          array(basename(__FILE__), __LINE__)));
            return false;
        }
    }

    return true;
}

/**
 * プライマリキーをもとにレコードを一件取得する
 *
 * @param    string    $id                プライマリキー
 * @return   array     $retAry            取得したレコード  
 */
function getRecordById($id){
    global $objDBCA, $objMTS;

    $errFlg = 0;
    $sql  = 'SELECT TASK_ID, TASK_STATUS, DP_TYPE, IMPORT_TYPE, FILE_NAME, DISP_SEQ, NOTE, DISUSE_FLAG,';
    $sql .= ' LAST_UPDATE_TIMESTAMP, LAST_UPDATE_USER';
    $sql .= ' FROM B_DP_STATUS';
    $sql .= ' WHERE TASK_ID = :TASK_ID';

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(array('TASK_ID' => $id));
    if ($res != '') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(baename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $retAry = array();
    while ($row = $objQuery->resultFetch()) {
        $retAry[] = $row;
    }

    if (count($retAry) === 0) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $id)));
        return false;
    }

    return $retAry;
}

/**
 * 設定期間を過ぎても実行中のレコード件数を取得する
 * (設定期間は秒単位)
 *
 * @return    int    $retAry    件数
 */
function getRunningRecord(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900026',
                                                      array(basename(__FILE__), __LINE__)));
    }
    
    $num = file_get_contents(ROOT_DIR_PATH . '/confs/backyardconfs/ita_base/data_portability_running_limit.txt');
    $param = '-' . trim($num) . ' seconds';
    $now = strtotime($param);
    $limitDatetime = date('Y-m-d H:i:s', $now) . '.000000';

    $sql  = 'SELECT COUNT(*) AS COUNT';
    $sql .= ' FROM B_DP_STATUS';
    $sql .= ' WHERE TASK_STATUS = ' . STATUS_RUNNING;
    $sql .= " AND LAST_UPDATE_TIMESTAMP < '" . $limitDatetime . "'";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                    array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
    }

    $res = $objQuery->sqlExecute();
    if ($res === false) {
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                    array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
    }

    while ($row = $objQuery->resultFetch()){
        $count = $row['COUNT'];
    }

    return $count;
}

/**
 * サービスの一覧を取得する
 *
 * @return   array     $retAry            取得したサービス一覧
 */
function getService(){

    $tmpAry = array();
    $resAry = array();

    $targetDir1 = '/etc/init.d/';
    $targetDir2 = '/usr/lib/systemd/system/';

    // RHEL6系のサービスを確認する
    if(is_dir($targetDir1)){
        foreach(glob($targetDir1 . 'ky_*') as $file) {
            if(false === strpos($file, 'ky_data_portability_execute-workflow')){
                $tmpAry[] = basename($file);
            }
        }
    }

    // RHEL7系のサービスを確認する
    if(is_dir($targetDir2)){
        foreach(glob($targetDir2 . 'ky_*') as $file) {
            if(false === strpos($file, 'ky_data_portability_execute-workflow')){
                $tmpAry[] = str_replace(".service", "", basename($file));
            }
        }
    }

    // 起動中のサービスを確認する
    $cmd = "ps -ef | grep ky_";
    $output = NULL;
    $return_var = NULL;
    exec($cmd, $output, $return_var);

    foreach($tmpAry as $service){
        foreach($output as $psData){

            if(false !== strpos($psData, $service)){
                $resAry[] = $service;
                break;
            }
        }
    }

    return $resAry;
}

/**
 * サービスを停止する
 *
 * @param    array     $serviceAry    サービス一覧
 * @return   なし
 */
function stopService($serviceAry){

    foreach($serviceAry as $service){
        $cmd = "service {$service} stop 2>/dev/null";
        shell_exec($cmd);
    }
}

/**
 * サービスを開始する
 *
 * @param    array     $serviceAry    サービス一覧
 * @return   なし
 */
function startpService($serviceAry){

    foreach($serviceAry as $service){
        $cmd = "service {$service} start 2>/dev/null";
        shell_exec($cmd);
    }
}

/**
 * 処理済みフラグをクリアする
 *
 * @param    なし
 * @return   なし
 */
function clearExecFlg(){

    global $objDBCA, $objMTS;

    $sql  = 'UPDATE A_PROC_LOADED_LIST ';
    $sql .= 'SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP ';

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime()));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    return true;
}

/**
 * データをエクスポートする
 */
function exportData($record){
    global $objDBCA, $objMTS;

    $taskId = $record['TASK_ID'];
    $exportPath = EXPORT_PATH . $taskId;

    if(!is_dir($exportPath)){
        outputLog(LOG_PREFIX, "Export infomation is not exists.Directory=[{$exportPath}].");
        return false;
    }

    $json = file_get_contents($exportPath . '/MENU_ID_LIST');
    $menuIdAry = json_decode($json, true);

    $tmpAry = getTableName($menuIdAry);

    $resAry = $tmpAry[0];
    $uploadAry = $tmpAry[1];
    $json = json_encode($resAry);
    $fileputflg = file_put_contents($exportPath . '/MENU_ID_TABLE_LIST', $json);
    // export先ディレクトリ書き込みエラー

    if ($fileputflg === false ){
        outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/MENU_ID_TABLE_LIST' . "],Value={$json}");
        return false;
    }

    $ricAry = array();      // 更新系テーブルのシーケンス番号用
    $jsqAry = array();      // 履歴系テーブルのシーケンス番号用
    $otherSeqAry = array(); // その他のシーケンス番号用

    foreach ($resAry as $key => $value) {

        // 更新系テーブル取得

        // dump取得
        $filePath = "{$exportPath}/{$key}_" . $value['TABLE_NAME'] . "_1";
        $cmd  = 'mysqldump --single-transaction --opt';
        $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
        $cmd .= ' -h' . DB_HOST;
        $cmd .= ' ' . DB_NAME . ' ' . $value['TABLE_NAME'];
        $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
        $cmd .= ' 2>&1 > ' . $filePath;

        $output = NULL;
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
            return false;
        }

        // dump取得（廃止除く）
        $filePath = "{$exportPath}/{$key}_" . $value['TABLE_NAME'] . "_2";
        $cmd  = 'mysqldump --single-transaction --opt';
        $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
        $cmd .= ' -h' . DB_HOST;
        $cmd .= ' ' . DB_NAME . ' ' . $value['TABLE_NAME'];
        $cmd .= ' --where \'DISUSE_FLAG<>"1" OR (DISUSE_FLAG="1" AND ' . $value['PRIMARY_KEY'] . '>200000000)\'';
        $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
        $cmd .= ' 2>&1 > ' . $filePath;

        $output = NULL;
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
            return false;
        }

        // dump取得（VIEW）
        if("" != $value['VIEW_NAME']){
            $filePath = "{$exportPath}/{$key}_" . $value['VIEW_NAME'];
            $cmd  = 'mysqldump --single-transaction --opt';
            $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
            $cmd .= ' -h' . DB_HOST;
            $cmd .= ' ' . DB_NAME . ' ' . $value['VIEW_NAME'];
            $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
            $cmd .= ' 2>&1 > ' . $filePath;

            $output = NULL;
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
            outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
            return false;
            }
        }

        // JNLのdump取得
        $filePath = "{$exportPath}/{$key}_" . $value['JNL_TABLE_NAME'] . "_1";
        $cmd  = 'mysqldump --single-transaction --opt';
        $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
        $cmd .= ' -h' . DB_HOST;
        $cmd .= ' ' . DB_NAME . ' ' . $value['JNL_TABLE_NAME'];
        $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
        $cmd .= ' 2>&1 > ' . $filePath;

        $output = NULL;
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
            return false;
        }

        // JNLのdump取得（廃止除く）
        $filePath = "{$exportPath}/{$key}_" . $value['JNL_TABLE_NAME'] . "_2";
        $cmd  = 'mysqldump --single-transaction --opt';
        $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
        $cmd .= ' -h' . DB_HOST;
        $cmd .= ' ' . DB_NAME . ' ' . $value['JNL_TABLE_NAME'];
        $cmd .= ' --where \'' . $value['PRIMARY_KEY'] . ' IN (SELECT ' . $value['PRIMARY_KEY'] . ' FROM ' . $value['TABLE_NAME'] . ' WHERE DISUSE_FLAG<>"1" OR (DISUSE_FLAG="1" AND ' . $value['PRIMARY_KEY'] . '>200000000))\'';
        $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
        $cmd .= ' 2>&1 > ' . $filePath;

        $output = NULL;
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
            return false;
        }

        // JNLのdump取得（VIEW）
        if("" != $value['JNL_VIEW_NAME']){
            $filePath = "{$exportPath}/{$key}_" . $value['JNL_VIEW_NAME'];
            $cmd  = 'mysqldump --single-transaction --opt';
            $cmd .= ' -u ' . DB_USER . ' -p' . DB_PW;
            $cmd .= ' -h' . DB_HOST;
            $cmd .= ' ' . DB_NAME . ' ' . $value['JNL_VIEW_NAME'];
            $cmd .= ' | sed -e "s/DEFINER[ ]*=[ ]*[^*]*\*/\*/" ';
            $cmd .= ' 2>&1 > ' . $filePath;

            $output = NULL;
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                outputLog(LOG_PREFIX, "An error occurred in mysqldump.Command=[$cmd].Error=[" . print_r($output, true) . "]");
                return false;
            }
        }

        // 更新系シーケンス番号取得
        $sql  = 'SELECT VALUE FROM A_SEQUENCE';
        $sql .= " WHERE NAME = '" . $value['SEQUENCE_RIC'] . "'";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, "SQL=[$sql].");
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, "SQL=[$sql].");
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        while ($row = $objQuery->resultFetch()) {
            $ricAry[$value['SEQUENCE_RIC']] = $row['VALUE'];
        }

        // 履歴系シーケンス番号取得
        $sql  = 'SELECT VALUE FROM A_SEQUENCE';
        $sql .= " WHERE NAME = '" . $value['SEQUENCE_JSQ'] . "'";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, "SQL=[$sql].");
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, "SQL=[$sql].");
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
        while ($row = $objQuery->resultFetch()) {
            $jsqAry[$value['SEQUENCE_JSQ']] = $row['VALUE'];
        }

        // その他のシーケンス番号取得
        if(array_key_exists('SEQUENCE_OTHER', $value) && 0 < $value['SEQUENCE_OTHER']){
            foreach($value['SEQUENCE_OTHER'] as $seqName){
                $sql  = 'SELECT VALUE FROM A_SEQUENCE';
                $sql .= " WHERE NAME = '" . $seqName . "'";
                $objQuery = $objDBCA->sqlPrepare($sql);
                if ($objQuery->getStatus() === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
                    outputLog(LOG_PREFIX, "SQL=[$sql].");
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
                    return false;
                }
                $res = $objQuery->sqlExecute();
                if ($res === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
                    outputLog(LOG_PREFIX, "SQL=[$sql].");
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
                    return false;
                }
                while ($row = $objQuery->resultFetch()) {
                    $otherSeqAry[$seqName] = $row['VALUE'];
                }
            }
        }
    }

    $json = json_encode($ricAry);
    $res = file_put_contents($exportPath . '/RIC_LIST', $json);
    if ($res === false) {
        outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/RIC_LIST' . "],Value={$json}");
        return false;
    }

    $json = json_encode($jsqAry);
    $res = file_put_contents($exportPath . '/JSQ_LIST', $json);
    if ($res === false) {
        outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/JSQ_LIST' . "],Value={$json}");
        return false;
    }
    if(0 < count($otherSeqAry)){
        $json = json_encode($otherSeqAry);
        $res = file_put_contents($exportPath . '/OTHER_SEQ_LIST', $json);
        if ($res === false) {
            outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/OTHER_SEQ_LIST' . "],Value={$json}");
            return false;
        }
    }

    $sql  = 'SELECT MENU_GROUP_ID, MENU_GROUP_NAME, MENU_ID, MENU_NAME, DISP_SEQ';
    $sql .= ' FROM D_MENU_LIST ';
    $sql .= ' WHERE DISUSE_FLAG = "0"';
    $sql .= ' ORDER BY MENU_GROUP_ID, MENU_ID, DISP_SEQ';

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(__FILE__, __LINE__)));
        return false;
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(__FILE__, __LINE__)));
        return false;
    }

    $menuListAry = array();
    while ($row = $objQuery->resultFetch()){
        $menuListAry[] = $row;
    }

    $menuListMenuIdAry = array_column($menuListAry, 'MENU_ID');

    $dispMenuListAry = array();
    foreach($menuIdAry as $targetMenuId){

        $key = array_search(intval($targetMenuId), $menuListMenuIdAry);

        if(false === $key){
            continue;
        }

        $menuGroupId = sprintf("%010d", $menuListAry[$key]['MENU_GROUP_ID']);
        $menuGroupName =  $menuListAry[$key]['MENU_GROUP_NAME'];
        $menuId = sprintf("%010d", $menuListAry[$key]['MENU_ID']);
        $menuName =  $menuListAry[$key]['MENU_NAME'];

        if(!array_key_exists($menuGroupId, $dispMenuListAry)){
            $dispMenuListAry[$menuGroupId] = array();
            $dispMenuListAry[$menuGroupId]['menu_group_name'] = $menuGroupName;
            $dispMenuListAry[$menuGroupId]['menu'] = array();
        }

        $dispMenuListAry[$menuGroupId]['menu'][] = array('menu_id' => $menuId, 'menu_name' => $menuName);
    }

    $json = json_encode($dispMenuListAry);
    $res = file_put_contents($exportPath . '/REQUEST', $json);
    if ($res === false) {
        outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/REQUEST' . "],Value={$json}");
        return false;
    }

    // 移行するファイルをコピーする
    if(!empty($uploadAry)){
        foreach( $uploadAry as $targetDirAry){
            foreach( $targetDirAry as $targetDir){

                if (file_exists(ROOT_DIR_PATH . $targetDir) === false ) {
                    continue;
                }

                $output = NULL;
                $cmd = "cd '" . ROOT_DIR_PATH . "';sudo cp -frp --parents '" . substr($targetDir, 1) . "' '" . $exportPath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    web_log($objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                    outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
                    return false;
                }
            }
        }
    }

    $json = json_encode($uploadAry);
    $res = file_put_contents($exportPath . '/COPY_DIR_FILE_LIST', $json);
    if ($res === false) {
        outputLog(LOG_PREFIX, "Function[file_put_contents] is error. File=[" . $exportPath . '/COPY_DIR_FILE_LIST' . "],Value={$json}");
        return false;
    }

    // kymに固める
    $exportFile = 'ita_exportdata_' . date('YmdHis') . '.kym';
    $output = NULL;
    $cmd = "cd '" . $exportPath . "';find . > target_list.txt 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
        return false;
    }

    $output = NULL;
    $cmd = "cd '" . $exportPath . "';sudo tar cvfz '" . ROOT_DIR_PATH . '/temp/data_export/' . $exportFile . "' -T target_list.txt 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
        return false;
    }

    return $exportFile;
}

/**
 * MENU_IDをもとにメニューで使われているテーブル名を取得する
 *
 * @param    array    $menuIdAry    メニューIDを格納した配列
 * @return   array    $retAry       メニューIDをkeyとするテーブル情報
 */
function getTableName($menuIdAry){
    $retAry = array();
    $uploadAry = array();

    foreach ($menuIdAry as $value) {
        $tmpAry = getInfoOfLTUsingIdOfMenuForDBtoDBLink($value);

        if(false === $tmpAry){
            continue;
        }

        $retAry[$value] = $tmpAry[0];
        $uploadAry[$value] = $tmpAry[1];

        // loadTableを対象に含める
        $loadTableSystemPath = "/webconfs/systems/{$value}_loadTable.php";
        $loadTableUserPath = "/webconfs/users/{$value}_loadTable.php";
        if(file_exists(ROOT_DIR_PATH . '/' . $loadTableSystemPath)){
            $uploadAry[$value][] = $loadTableSystemPath;
        }
        else if(file_exists(ROOT_DIR_PATH . '/' . $loadTableUserPath)){
            $uploadAry[$value][] = $loadTableUserPath;
        }

        // メニューに必要なソースを対象に含める
        $menuPartsSystemPath = "/webroot/menus/systems/{$value}/";
        $menuPartsUserPath = "/webroot/menus/systems/{$value}/";
        if(is_dir(ROOT_DIR_PATH . '/' . $menuPartsSystemPath)){
            $uploadAry[$value][] = $menuPartsSystemPath;
        }
        else if(is_dir(ROOT_DIR_PATH . '/' . $menuPartsUserPath)){
            $uploadAry[$value][] = $menuPartsUserPath;
        }
    }
    return array($retAry, $uploadAry);
}

/**
 * loadTableの情報取得
 */
function getInfoOfLTUsingIdOfMenuForDBtoDBLink($strMenuIdNumeric){
    //$strMenuIdNumeric[CMDB用メニュー一覧にあるものであることが前提]
    $aryValues = array();
    $intErrorType = null;
    $strErrMsg = "";
    $retAry = array();
    $retUploadAry = array();

    $strFxName = __FUNCTION__; // getInfoOfRepresentativeFiles

    $tmpAryRetBody = getInfoOfLoadTable($strMenuIdNumeric);

    if( $tmpAryRetBody[1] !== null ){
        $intErrorType = $tmpAryRetBody[1];

        // エラー終了
        return false;
    }
    $aryValues = $tmpAryRetBody[0];

    $retAry['PRIMARY_KEY'] = $aryValues['TABLE_INFO']['UTN_ROW_INDENTIFY'];
    $retAry['TABLE_NAME'] = $aryValues['TABLE_INFO']['UTN']['OBJECT_ID'];
    $retAry['JNL_TABLE_NAME'] = $aryValues['TABLE_INFO']['JNL']['OBJECT_ID'];
    $retAry['VIEW_NAME'] = $aryValues['TABLE_INFO']['VIEW']['UTN_VIEW'];
    $retAry['JNL_VIEW_NAME'] = $aryValues['TABLE_INFO']['VIEW']['JNL_VIEW'];
    $retAry['SEQUENCE_RIC'] = $aryValues['TABLE_INFO']['REQUIRED_COLUMNS']['UtnSeqName'];
    $retAry['SEQUENCE_JSQ'] = $aryValues['TABLE_INFO']['REQUIRED_COLUMNS']['JnlSeqName'];
    if(0 < count($aryValues['SEQ_IDS'])){
        $retAry['SEQUENCE_OTHER'] = $aryValues['SEQ_IDS'];
    }

    $retUploadAry =  $aryValues['UPLOAD_DIRS'];
    // 存在しないディレクトリは対象から削除
    foreach($retUploadAry as $key => $value){
        if(!is_dir(ROOT_DIR_PATH . '/' . $value)){
            unset($retUploadAry[$key]);
        }
    }
    array_values($retUploadAry);

    return array($retAry, $retUploadAry);
}

/**
 * loadTableの情報取得
 */
function getInfoOfLoadTable($strMenuIdNumeric){

    $aryValues = array();
    $intErrorType = null;
    $strErrMsg = "";

    $strFxName = __FUNCTION__; // getInfoOfRepresentativeFiles

    $registeredKey = "";
    $strLoadTableFullname = "";
    $aryVariant = array();
    $arySetting = array();

    $objTable = null;

    $strHiddenTableMode = false;

    $aryInfoOfTable = array();
    $strPageType = "";

    $strUTNTableId = "";
    $strJNLTableId = "";

    $strUTNViewId = "";
    $strJNLViewId = "";

    $strUTNRIColumnId = "";
    $strJNLRIColumnId = "";

    $aryColumnInfo01 = array();
    $aryColumnInfo02 = array();
    $aryUploadColumnDir = array();
    $aryOtherSeqIds = array();
    try{
        $systemDir = "systems/{$strMenuIdNumeric}";
        $userDir = "users/{$strMenuIdNumeric}";
        if(file_exists(ROOT_DIR_PATH . "/webconfs/systems/{$strMenuIdNumeric}_loadTable.php")){
            $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs/systems/{$strMenuIdNumeric}_loadTable.php";
        }
        else if(file_exists(ROOT_DIR_PATH . "/webconfs/users/{$strMenuIdNumeric}_loadTable.php")){
            $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs/users/{$strMenuIdNumeric}_loadTable.php";
        }
        else{
            outputLog(LOG_PREFIX, "loadTable with menuId[{$strMenuIdNumeric}] does not exists.");
            // 例外処理へ
            throw new Exception();
        }

        require_once($strLoadTableFullname);
        $registeredKey = $strMenuIdNumeric;

        if( 0 < strlen($registeredKey) ){
            $objTable = loadTable($registeredKey,$aryVariant,$arySetting);
            if($objTable === null){
                // 00_loadTable.phpの読込失敗
                $intErrorType = 101;
                $strErrMsg = "[" . $strLoadTableFullname . "] Analysis Error";
            }
        }

        if( $objTable !== null ){
            $aryColumns = $objTable->getColumns();

            if( is_a($objTable,"TemplateTableForReview")=== true ){
                //----ReView用テーブル
                $strPageType = $objTable->getPageType();
                
                $tmpStrRIColumn = "";
                $tmpStrLockTargetColumn = "";
                foreach($aryColumns as $strColumnId=>$objColumn){
                    if( is_a($objColumn,"RowIdentifyColumn") === true ){
                        $tmpStrRIColumn = $objColumn->getID();
                        continue;
                    }
                    if( is_a($objColumn,"LockTargetColumn") === true ){
                        $tmpStrLockTargetColumn = $objColumn->getID();
                        continue;
                    }
                }
                $strUTNRIColumnId = $tmpStrRIColumn;
                $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();

                $strLockTargetColumnId = $tmpStrLockTargetColumn;
                unset($tmpStrRIColumn);
                unset($tmpStrLockTargetColumn);
                
                $aryRequiredColumnId = array(
                    "RowIdentify"    =>$strUTNRIColumnId
                    
                    ,"LockTarget"    =>$strLockTargetColumnId
                    ,"EditStatus"    =>$objTable->getEditStatusColumnID()
                    
                    ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                    ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                    ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                    
                    ,"Note"          =>$objTable->getRequiredNoteColumnID()
                    
                    ,"ApplyUpdate"   =>$objTable->getApplyUpdateColumnID()
                    ,"ApplyUser"     =>$objTable->getApplyUserColumnID()
                    ,"ConfirmUpdate" =>$objTable->getConfirmUpdateColumnID()
                    ,"ConfirmUser"   =>$objTable->getConfirmUserColumnID()
                    
                    ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                    ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                    ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                    ,"JnlSeqNo"      =>$strJNLRIColumnId
                    ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                    ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                    ,"UtnSeqName"    =>$aryColumns[$strUTNRIColumnId]->getSequenceID()
                    ,"JnlSeqName"    =>$aryColumns[$strJNLRIColumnId]->getSequenceID()
                );
                
                if( $strPageType == "apply" || $strPageType == "confirm" ){
                    $strUTNTableId = $objTable->getDBMainTableHiddenID();
                    $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                    if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                        $strUTNViewId = $objTable->getDBMainTableBody();
                        $strJNLViewId = $objTable->getDBJournalTableBody();
                        $strHiddenTableMode = true;
                    }
                    else{
                        $strUTNTableId = $objTable->getDBMainTableBody();
                        $strJNLTableId = $objTable->getDBJournalTableBody();
                    }
                }
                else{
                    $strUTNTableId = $objTable->getDBResultTableHiddenID();
                    $strJNLTableId = $objTable->getDBResultJournalTableHiddenID();
                    $strUTNViewId = $objTable->getDBMainTableBody();
                    $strJNLViewId = $objTable->getDBJournalTableBody();
                    if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                        $strHiddenTableMode = true;
                    }
                    else{
                        $strUTNTableId = $objTable->getDBResultTableBody();
                        $strJNLTableId = $objTable->getDBResultJournalTableBody();
                    }
                }
                
                $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                       ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                   )
                                       ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                   )
                                       ,"VIEW"             =>array("UTN_VIEW"           =>$strUTNViewId
                                                                  ,"JNL_VIEW"           =>$strJNLViewId
                                                                   )
                                       ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                       ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                       ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                        );
                //ReView用テーブル----
            }
            else{
                //----標準テーブル
                $strUTNRIColumnId = $objTable->getRowIdentifyColumnID();
                $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                
                $aryRequiredColumnId = array(
                    "RowIdentify"    =>$strUTNRIColumnId
                    ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                    ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                    ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                    
                    ,"Note"          =>$objTable->getRequiredNoteColumnID()

                    ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                    ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                    ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                    ,"JnlSeqNo"      =>$strJNLRIColumnId
                    ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                    ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                    ,"UtnSeqName"    =>$aryColumns[$strUTNRIColumnId]->getSequenceID()
                    ,"JnlSeqName"    =>$aryColumns[$strJNLRIColumnId]->getSequenceID()
                
                );
                
                $strUTNTableId = $objTable->getDBMainTableHiddenID();
                $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                    $strUTNViewId = $objTable->getDBMainTableBody();
                    $strJNLViewId = $objTable->getDBJournalTableBody();
                    $strHiddenTableMode = true;
                }
                else{
                    $strUTNTableId = $objTable->getDBMainTableBody();
                    $strJNLTableId = $objTable->getDBJournalTableBody();
                }
                $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                       ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                   )
                                       ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                   )
                                       ,"VIEW"             =>array("UTN_VIEW"           =>$strUTNViewId
                                                                  ,"JNL_VIEW"           =>$strJNLViewId
                                                                   )
                                       ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                       ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                       ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                        );
                //標準テーブル----
            }
            
            //必須カラムのID----
            
            //----カラムインスタンスの取得
            foreach($aryColumns as $strColumnId=>$objColumn){
                $boolAddInfo = false;
                if( in_array($strColumnId,$aryRequiredColumnId) === false ){
                    //----必須カラムではない任意カラム
                    if( $strHiddenTableMode === true ){
                        //----VIEWを表示、TABLEを更新させる設定の場合
                        if( $objColumn->isDBColumn() === true && $objColumn->isHiddenMainTableColumn() ){
                            $boolAddInfo = true;
                        }
                        //VIEWを表示、TABLEを更新させる設定の場合----
                    }
                    else{
                        //----TABLEを表示/更新させる設定の場合
                        if( $objColumn->isDBColumn() === true ){
                            $boolAddInfo = true;
                        }
                        //----TABLEを表示/更新させる設定の場合
                    }
                    if( $boolAddInfo === true ){
                        $aryColumnInfo01[] = array($strColumnId,$objColumn->getColLabel(true));
                        if("FileUploadColumn" ===  get_class($objColumn)){
                            $aryUploadColumnDir[] = $objColumn->getNRPathAnyToBranchPerFUC();
                        }
                        if("AutoNumRegisterColumn" ===  get_class($objColumn)){
                            $aryOtherSeqIds[] = $objColumn->getSequenceID();
                        }
                    }
                    else{
                        $aryColumnInfo02[] = array($strColumnId,$objColumn->getColLabel(true));
                    }
                    //必須カラムではない任意カラム----
                }
            }
        }
    }
    catch (Exception $e){
        if( $intErrorType === null ) $intErrorType = 501;
        $tmpErrMsgBody = $e->getMessage();
        $strErrMsg = $tmpErrMsgBody;
    }
    $aryValues = array("TABLE_INFO"       =>$aryInfoOfTable
                      ,"TABLE_IUD_COLUMNS"=>$aryColumnInfo01
                      ,"OTHER_COLUMNS"    =>$aryColumnInfo02
                      ,"UPLOAD_DIRS"      =>$aryUploadColumnDir
                      ,"SEQ_IDS"          =>$aryOtherSeqIds
                       );
    return array($aryValues,$intErrorType,$strErrMsg);
}
