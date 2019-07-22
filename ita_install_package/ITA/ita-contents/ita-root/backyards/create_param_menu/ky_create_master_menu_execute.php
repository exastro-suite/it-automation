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
 *  マスタ作成管理を元にメニューを作成する
 */

if( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}

define('ROOT_DIR_PATH',         $root_dir_path);
require_once ROOT_DIR_PATH      . '/libs/backyardlibs/create_param_menu/ky_create_param_menu_env.php';
require_once CPM_LIB_PATH       . 'ky_create_param_menu_classes.php';
require_once CPM_LIB_PATH       . 'ky_create_param_menu_functions.php';
require_once COMMONLIBS_PATH    . 'common_php_req_gate.php';

try{

    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // 未実行のレコードがない場合は処理を終了する
    //////////////////////////
    $createMenuStatusArray = getUnexecutedRecord();
    if(count($createMenuStatusArray) === 0){
        if(LOG_LEVEL === 'DEBUG'){
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10004'));
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
        }
        exit;
    }

    //////////////////////////
    // テンプレートファイル読み込み
    //////////////////////////
    $templatePathArray =array(TEMPLATE_PATH . FILE_MST_LOADTABLE,
                              TEMPLATE_PATH . FILE_MST_LOADTABLE_VAL,
                              TEMPLATE_PATH . FILE_MST_SQL,
                             );
    $templateArray = array();
    foreach($templatePathArray as $templatePath){
        if(!file_exists($templatePath)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5001', array($templatePath));
            outputLog($msg);
            throw new Exception($msg);
        }
        $work = file_get_contents($templatePath);
        if(false === $work){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5002', array($templatePath));
            outputLog($msg);
            throw new Exception($msg);
        }
        $templateArray[] = $work;
    }

    $masterLoadTableTmpl    = $templateArray[0];
    $masterLoadTableValTmpl = $templateArray[1];
    $masterSqlTmpl          = $templateArray[2];

    //////////////////////////
    // マスタ作成情報を取得
    //////////////////////////
    $createMstMenuInfoTable = new CreateMstMenuInfoTable($objDBCA, $db_model_ch);
    $sql = $createMstMenuInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $createMstMenuInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $createMenuInfoArray = $result;

    //////////////////////////
    // マスタ項目情報を取得
    //////////////////////////
    $createMstItemInfoTable = new CreateMstItemInfoTable($objDBCA, $db_model_ch);
    $sql = $createMstItemInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $createMstItemInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $createItemInfoArray = $result;

    //////////////////////////
    // 作業用ディレクトリ作成
    //////////////////////////
    // 最新時間を取得
    $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
    $nowTime = date("YmdHis") . $now->format("u");

    $tmpDir = TEMP_PATH . $nowTime;
    $result = mkdir($tmpDir, 0777, true);
    
    if(true != $result){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($tmpDir));
        outputLog($msg);
        throw new Exception($msg);
    }

    //////////////////////////
    // 処理対象のデータ件数分ループ
    //////////////////////////
    foreach($createMenuStatusArray as $targetData){

        //////////////////////////
        // マスタ作成情報を特定する
        //////////////////////////
        $createMenuInfoIdx = array_search($targetData['CREATE_MENU_ID'], array_column($createMenuInfoArray, 'CREATE_MENU_ID'));

        // マスタ作成情報が特定できなかった場合、完了(異常)
        if(false === $createMenuInfoIdx){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5012');
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }
        $cmiData = $createMenuInfoArray[$createMenuInfoIdx];

        //////////////////////////
        // マスタ項目作成情報を特定する
        //////////////////////////
        $itemInfoArray = array();
        foreach($createItemInfoArray as $ciiData){
            if($targetData['CREATE_MENU_ID'] === $ciiData['CREATE_MENU_ID']){
                $itemInfoArray[] = $ciiData;
            }
        }

        // マスタ項目作成情報が0件の場合、完了(異常)
        if(0 === count($itemInfoArray)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5013');
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        // マスタ項目作成情報を表示順序、項番の昇順に並べ替える
        $dispSeqArray = array();
        $idArray = array();
        foreach ($itemInfoArray as $key => $itemInfo){
            $dispSeqArray[$key] = $itemInfo['DISP_SEQ'];
            $idArray[$key]      = $itemInfo['CREATE_ITEM_ID'];
        }
        array_multisort($dispSeqArray, SORT_ASC, $idArray, SORT_ASC, $itemInfoArray);

        //////////////////////////
        // ディレクトリ名、テーブル名を決定する
        //////////////////////////
        $menuDirName = sprintf("%04d", $cmiData['CREATE_MENU_ID']);
        $menuTableName = MASTER_PREFIX . sprintf("%04d", $cmiData['CREATE_MENU_ID']);

        //////////////////////////
        // テンプレートの埋め込み部分を設定する
        //////////////////////////
        $columnTypes = "";
        $columns = "";
        $masterLoadTableVal = "";

        // 項目の件数分ループ
        foreach ($itemInfoArray as &$itemInfo){
            // カラム名を決定する
            $itemInfo['COLUMN_NAME'] = COLUMN_PREFIX . sprintf("%04d", $itemInfo['CREATE_ITEM_ID']);
            $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    VARCHAR(" . $itemInfo['MAX_LENGTH'] . "),\n";
            $columns = $columns . "       TAB_A." . $itemInfo['COLUMN_NAME'] . ",\n";

            // 「'」がある場合は「\'」に変換する
            $description    = str_replace("'", "\'", $itemInfo['DESCRIPTION']);
            $itemName       = str_replace("'", "\'", $itemInfo['ITEM_NAME']);

            // マスタ用loadTableのカラム埋め込み部分を作成する
            $work = $masterLoadTableValTmpl;
            $work = str_replace(REPLACE_INFO,   $description,               $work);
            $work = str_replace(REPLACE_INFO,   $description,               $work);
            if("" != $itemInfo['PREG_MATCH']){
                $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
            }
            else{
                $work = str_replace(REPLACE_PREG, "", $work);
            }
            $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
            $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
            $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);
            $masterLoadTableVal .= $work . "\n";
        }
        unset($itemInfo);

        // 「'」がある場合は「\'」に変換する。説明の改行コードは<BR/>に変換する。
        $description    = str_replace("'", "\'", $cmiData['DESCRIPTION']);
        $description    = str_replace("\n", "<BR/>", $description);
        $menuName       = str_replace("'", "\'", $cmiData['MENU_NAME']);

        // マスタ用の00_loadTable.php
        $work = $masterLoadTableTmpl;
        $work = str_replace(REPLACE_INFO,   $description,           $work);
        $work = str_replace(REPLACE_TABLE,  $menuTableName,         $work);
        $work = str_replace(REPLACE_MENU,   $menuName,              $work);
        $work = str_replace(REPLACE_ITEM,   $masterLoadTableVal,    $work);
        $masterLoadTable = $work;

        // マスタ用のSQL
        $work = $masterSqlTmpl;
        $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
        $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
        $work = str_replace(REPLACE_COL,        $columns,       $work);
        $masterSql = $work;

        //////////////////////////
        // メニュー専用の一時領域を作成する
        //////////////////////////
        $menuTmpDir = $tmpDir . "/" . $menuDirName . "/";

        if(!file_exists($menuTmpDir)){
            $result = mkdir($menuTmpDir, 0777, true);

            if(true != $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($menuTmpDir));
                outputLog($msg);
                // マスタ作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }
        }

        //////////////////////////
        // SQLファイルを作成する
        //////////////////////////
        $sqlFilePath = $menuTmpDir . $menuTableName . ".sql";

        // マスタ用
        $result = file_put_contents($sqlFilePath, $masterSql, FILE_APPEND);
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($sqlFilePath));
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        //////////////////////////
        // トランザクション開始
        //////////////////////////
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        //////////////////////////
        // 外部CMDB作成用SQL実行
        //////////////////////////
        $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);

        // マスタ用
        $explodeSql = explode(";", $masterSql);
        $errFlg = false;
        foreach($explodeSql as $sql){

            // SQLが空の場合はスキップ
            if("" === str_replace(" ", "", (str_replace("\n", "", $sql)))){
                continue;
            }

            // SQL実行
            $result = $baseTable->execQuery($sql, NULL, $objQuery);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                // マスタ作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, true, false);
                continue;
            }
        }

        //////////////////////////
        // メニュー・テーブル紐付更新
        //////////////////////////
        $result = updateMenuTableLink($targetData, $menuTableName);

        if(true !== $result){
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, false);
            continue;
        }

        //////////////////////////
        // テーブル項目名一覧更新
        //////////////////////////
        $result = updateTableItemList($targetData, $itemInfoArray);

        if(true !== $result){
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, false);
            continue;
        }

        //////////////////////////
        // メニュー管理更新
        //////////////////////////
        $result = updateMenuList($cmiData, $menuId);

        if(true !== $result){
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, false);
            continue;
        }

        //////////////////////////
        // ロール・メニュー紐付管理更新
        //////////////////////////
        $result = updateRoleMenuLinkList($menuId);

        if(true !== $result){
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, false);
            continue;
        }

        //////////////////////////
        // 他メニュー連携テーブル更新
        //////////////////////////
        $result = updateOtherMenuLink($menuTableName, $itemInfoArray, $menuId);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, false);
            continue;
        }

        //////////////////////////
        // loadTableを配置する
        //////////////////////////
        // マスタ用
        $masterLoadTablePath = $menuTmpDir . sprintf("%010d", $menuId) . "_loadTable.php";
        $result = deployLoadTable($masterLoadTable,
                                  $masterLoadTablePath,
                                  sprintf("%010d", $menuId),
                                  $targetData
                                 );
        if(true !== $result){
            continue;
        }

        //////////////////////////
        // 作成したファイルをZIPファイルに固める
        //////////////////////////
        $zipFileName = sprintf("%010d", $menuId) . ".zip";
        $zipFilePath = $menuTmpDir . $zipFileName;

        $zip = new ZipArchive;
        if(true != $zip->open($zipFilePath, ZipArchive::CREATE)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath));
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        // マスタ用の00_loadTable.php
        $result = $zip->addFile($masterLoadTablePath, basename($masterLoadTablePath));

        if(true != $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $masterLoadTablePath));
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            $zip->close();
            $zip = NULL;
            continue;
        }

        // SQLファイル
        $result = $zip->addFile($sqlFilePath, $menuTableName . ".sql");

        if(true != $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $sqlFilePath));
            outputLog($msg);
            // マスタ作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            $zip->close();
            $zip = NULL;
            continue;
        }

        $zip->close();
        $zip = NULL;

        //////////////////////////
        // マスタ作成管理更新処理を行う（完了）
        //////////////////////////
        updateMenuStatus($targetData, "3", NULL, false, false, $zipFileName, $zipFilePath);
    }

    // 作業用ディレクトリを削除する
    if(file_exists($tmpDir)){
        $output = NULL;
        $cmd = "rm -rf '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5009', array($tmpDir));
            outputLog($msg);
            throw new Exception($msg);
        }
    }

    if(LOG_LEVEL === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
    }
}
catch(Exception $e){
    // 作業用ディレクトリを削除する
    if(file_exists($tmpDir)){
        $output = NULL;
        $cmd = "rm -rf '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);
    }
    if(LOG_LEVEL === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10003', basename( __FILE__, '.php' )));
    }
}


/*
 * 未実行レコードを取得する
 */
function getUnexecutedRecord(){
    global $objDBCA, $db_model_ch, $objMTS;
    $tranStartFlg = false;
    $createMstMenuStatusTable = new CreateMstMenuStatusTable($objDBCA, $db_model_ch);
    $returnArray = array();

    try{
        //////////////////////////
        // マスタ作成管理テーブルを検索
        //////////////////////////
        $sql = $createMstMenuStatusTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $createMstMenuStatusTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $createMenuStatusArray = $result;

        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = true;

        foreach($createMenuStatusArray as $cmsData){
            // ステータスが未実行または実行中の場合
            if("1" == $cmsData['STATUS_ID'] || "2" == $cmsData['STATUS_ID']){

                $updateData = $cmsData;

                // ステータスが未実行の場合
                if("1" == $cmsData['STATUS_ID']){

                    $updateData['STATUS_ID']        = "2";
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;
                    $returnArray[] = $cmsData;
                }
                // ステータスが実行中の場合、完了(異常) にする
                else if("2" == $cmsData['STATUS_ID']){
                    $updateData['STATUS_ID']        = "4";
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;
                }

                //////////////////////////
                // マスタ作成管理テーブルを更新
                //////////////////////////
                $result = $createMstMenuStatusTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = false;

        return $returnArray;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        throw new Exception($e->getMessage());
    }
}

/**
 * マスタ作成管理更新
 * 
 */
function updateMenuStatus($targetData, $status, $note, $rollbackFlg, $tranFlg, $zipFileName=NULL, $zipFilePath=NULL){

    global $objDBCA, $db_model_ch, $objMTS;
    $tranStartFlg = false;

    try{
        if(true === $rollbackFlg){
            // ロールバック
            $result = $objDBCA->transactionRollback();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        if(true === $tranFlg){
            // トランザクション開始
            $result = $objDBCA->transactionStart();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        $tranStartFlg = true;

        $createMstMenuStatusTable = new CreateMstMenuStatusTable($objDBCA, $db_model_ch);

        // 更新する
        $updateData = $targetData;
        $updateData['STATUS_ID']        = $status;                  // ステータス
        $updateData['FILE_NAME']        = $zipFileName;             // メニュー資材
        $updateData['NOTE']             = $note;                    // 備考
        $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;    // 最終更新者

        //////////////////////////
        // マスタ作成管理テーブルを更新
        //////////////////////////
        $result = $createMstMenuStatusTable->updateTable($updateData, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        if(NULL != $zipFileName){

            // ZIPファイルをアップロードファイル格納先にコピーする
            $pathArray = array();
            $pathArray[0] = UPLOAD_PATH_MST;
            $pathArray[1] = $pathArray[0] . sprintf("%010d", $targetData['MM_STATUS_ID']) . '/';
            $pathArray[2] = $pathArray[1] . 'old/';
            $pathArray[3] = $pathArray[2] . sprintf("%010d", $jnlSeqNo) . '/';

            foreach($pathArray as $path){

                if(!file_exists($path)){
                    $mask = umask();
                    umask(000);
                    $result = mkdir($path, 0777, true);
                    umask($mask);

                    if(true != $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($path));
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    chmod($path, 0777);
                }
            }

            $destFile = $pathArray[1] . $zipFileName;
            $result = copy($zipFilePath, $destFile);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($zipFilePath, $destFile));
                outputLog($msg);
                throw new Exception($msg);
            }

            $destFile = $pathArray[3] . $zipFileName;
            $result = copy($zipFilePath, $destFile);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($zipFilePath, $destFile));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        return true;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        return $e->getMessage();
    }
}

/**
 * loadTable配置
 * 
 */
function deployLoadTable($fileContents, $loadTablePath, $menuId, $targetData){

    global $objDBCA, $objMTS;

    try{
        // 00_loadTable.phpを一時領域に作成する
        $result = file_put_contents($loadTablePath, $fileContents);
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($loadTablePath));
            outputLog($msg);
            throw new Exception($msg);
        }

        // 00_loadTable.phpの配置
        $destFile = ROOT_DIR_PATH . "/webconfs/users/{$menuId}_loadTable.php";
        $result = copy($loadTablePath, $destFile);
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($loadTablePath, $destFile));
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;
    }
    catch(Exception $e){
        // マスタ作成管理更新処理を行う
        updateMenuStatus($targetData, "4", $e->getMessage(), false, true);
        return $e->getMessage();
    }
}

/*
 * メニュー・テーブル紐付更新
 */
function updateMenuTableLink($targetData, $menuTableName){
    global $objDBCA, $db_model_ch, $objMTS;
    $mstTableLinkTable = new MstTableLinkTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー・テーブル紐付テーブルを検索
        //////////////////////////
        $sql = $mstTableLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $mstTableLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuTableLinkArray = $result;

        foreach($menuTableLinkArray as $mtlData){
            // メニューIDが一致した場合、廃止
            if($mtlData['CREATE_MENU_ID'] === $targetData['CREATE_MENU_ID']){

                // 廃止する
                $updateData = $mtlData;
                $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;    // 最終更新者

                //////////////////////////
                // メニュー・テーブル紐付テーブルを更新
                //////////////////////////
                $result = $mstTableLinkTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録する
        $insertData = array();
        $insertData['CREATE_MENU_ID']       = $targetData['CREATE_MENU_ID'];    // メニュー名
        $insertData['TABLE_NAME_MST']       = "F_" . $menuTableName;            // テーブル名(ホスト用)
        $insertData['TABLE_NAME_MST_JNL']   = "F_" . $menuTableName . "_JNL";   // テーブル名(ホスト用履歴)
        $insertData['DISUSE_FLAG']          = "0";                              // 廃止フラグ
        $insertData['LAST_UPDATE_USER']     = USER_ID_CREATE_MASTER;            // 最終更新者

        //////////////////////////
        // メニュー・テーブル紐付テーブルに登録
        //////////////////////////
        $result = $mstTableLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * テーブル項目名一覧更新
 */
function updateTableItemList($targetData, $itemInfoArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $mstTableItemListTable = new MstTableItemListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // テーブル項目名一覧テーブルを検索
        //////////////////////////
        $sql = $mstTableItemListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $mstTableItemListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $tableItemListArray = $result;

        foreach($tableItemListArray as $tilData){
            // 廃止対象のレコードを廃止
            if($tilData['CREATE_MENU_ID'] === $targetData['CREATE_MENU_ID']){

                // 廃止する
                $updateData = $tilData;
                $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;    // 最終更新者

                //////////////////////////
                // メニュー・テーブル紐付テーブルを更新
                //////////////////////////
                $result = $mstTableItemListTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        foreach($itemInfoArray as $itemInfo){

            // 登録する
            $insertData = array();
            $insertData['CREATE_MENU_ID']       = $itemInfo['CREATE_MENU_ID'];  // メニュー名
            $insertData['CREATE_ITEM_ID']       = $itemInfo['CREATE_ITEM_ID'];  // 項目名
            $insertData['COLUMN_NAME']          = $itemInfo['COLUMN_NAME'];     // カラム名
            $insertData['DISUSE_FLAG']          = "0";                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']     = USER_ID_CREATE_MASTER;        // 最終更新者

            //////////////////////////
            // テーブル項目名一覧テーブルに登録
            //////////////////////////
            $result = $mstTableItemListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * メニュー管理更新
 */
function updateMenuList($cmiData, &$menuId){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuListTable = new MenuListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー管理テーブルを検索
        //////////////////////////
        $sql = $menuListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $menuListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuListArray = $result;

        $matchFlg = false;
        $menuList = NULL;

        foreach($menuListArray as $menu){
            // メニューグループとメニューが一致するデータを検索
            if($cmiData['MENUGROUP_FOR_MST'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $matchFlg = true;
                $menuList = $menu;
                break;
            }
        }

        // メニューグループとメニューが一致するデータがあった場合
        if(null !== $menuList){

            $menuId = $menuList['MENU_ID'];

            // 更新する
            $updateData = $menuList;
            $updateData['LOGIN_NECESSITY']      = 1;                        // 認証要否
            $updateData['SERVICE_STATUS']       = 0;                        // サービス状態
            $updateData['DISP_SEQ']             = $cmiData['DISP_SEQ'];     // メニューグループ内表示順序
            $updateData['AUTOFILTER_FLG']       = 1;                        // オートフィルタチェック
            $updateData['INITIAL_FILTER_FLG']   = 2;                        // 初回フィルタ
            $updateData['WEB_PRINT_LIMIT']      = NULL;                     // Web表示最大行数
            $updateData['WEB_PRINT_CONFIRM']    = NULL;                     // Web表示前確認行数
            $updateData['XLS_PRINT_LIMIT']      = NULL;                     // Excel出力最大行数
            $updateData['NOTE']                 = NULL;                     // 備考
            $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_MASTER;    // 最終更新者

            //////////////////////////
            // メニュー管理テーブルを更新
            //////////////////////////
            $result = $menuListTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        // メニューグループとメニューが一致するデータが無かった場合
        else{

            // 登録する
            $insertData = array();
            $insertData['MENU_GROUP_ID']        = $cmiData['MENUGROUP_FOR_MST'];    // メニューグループ
            $insertData['MENU_NAME']            = $cmiData['MENU_NAME'];            // メニュー
            $insertData['LOGIN_NECESSITY']      = 1;                                // 認証要否
            $insertData['SERVICE_STATUS']       = 0;                                // サービス状態
            $insertData['DISP_SEQ']             = $cmiData['DISP_SEQ'];             // メニューグループ内表示順序
            $insertData['AUTOFILTER_FLG']       = 1;                                // オートフィルタチェック
            $insertData['INITIAL_FILTER_FLG']   = 2;                                // 初回フィルタ
            $insertData['DISUSE_FLAG']          = "0";                              // 廃止フラグ
            $insertData['LAST_UPDATE_USER']     = USER_ID_CREATE_MASTER;            // 最終更新者

            //////////////////////////
            // メニュー管理テーブルに登録
            //////////////////////////
            $result = $menuListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }

            $menuId = $seqNo;
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * ロール・メニュー紐付管理更新
 */
function updateRoleMenuLinkList($menuId){
    global $objDBCA, $db_model_ch, $objMTS;
    $roleMenuLinkListTable = new RoleMenuLinkListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // ロール・メニュー紐付管理テーブルを検索
        //////////////////////////
        $sql = $roleMenuLinkListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $roleMenuLinkListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $roleMenuLinkListArray = $result;

        foreach($roleMenuLinkListArray as $roleMenuLink){
            // メニューIDが一致したデータを廃止
            if($roleMenuLink['MENU_ID'] === $menuId){

                // 廃止する
                $updateData = $roleMenuLink;
                $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;    // 最終更新者

                //////////////////////////
                // ロール・メニュー紐付管理テーブルを更新
                //////////////////////////
                $result = $roleMenuLinkListTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録する
        $insertData = array();
        $insertData['ROLE_ID']          = 1;                        // ロール
        $insertData['MENU_ID']          = $menuId;                  // メニュー
        $insertData['PRIVILEGE']        = 1;                        // 紐付
        $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
        $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_MASTER;    // 最終更新者

        //////////////////////////
        // ロール・メニュー紐付管理テーブルに登録
        //////////////////////////
        $result = $roleMenuLinkListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 他メニュー連携テーブル更新
 */
function updateOtherMenuLink($menuTableName, $itemInfoArray, $menuId){
    global $objDBCA, $db_model_ch, $objMTS;
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 他メニュー連携テーブルを検索
        //////////////////////////
        $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $otherMenuLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $otherMenuLinkArray = $result;

        foreach($otherMenuLinkArray as $omlData){
            // メニューIDが一致した場合、廃止
            if($omlData['MENU_ID'] == $menuId){

                // 廃止する
                $updateData = $omlData;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // 他メニュー連携テーブルを更新
                //////////////////////////
                $result = $otherMenuLinkTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録する
        foreach($itemInfoArray as $itemInfo){

            $insertData = array();
            $insertData['MENU_ID']          = $menuId;                  // メニュー
            $insertData['COLUMN_DISP_NAME'] = $itemInfo['ITEM_NAME'];   // 項目名
            $insertData['TABLE_NAME']       = "F_" . $menuTableName;;   // テーブル名
            $insertData['PRI_NAME']         = "ROW_ID";                 // 主キー
            $insertData['COLUMN_NAME']      = $itemInfo['COLUMN_NAME']; // カラム名
            $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

            //////////////////////////
            // 他メニュー連携テーブルに登録
            //////////////////////////
            $result = $otherMenuLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}
