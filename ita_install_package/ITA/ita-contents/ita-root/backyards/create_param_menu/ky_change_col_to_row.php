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
 *  縦管理のパラメータシートから横管理のパラメータシートにデータを移す
 *
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
require_once COMMONLIBS_PATH    . 'common_getInfo_LoadTable.php';

try{

    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // パラメータシート縦横変換テーブルを取得
    //////////////////////////
    $colToRowMngTable = new ColToRowMngTable($objDBCA, $db_model_ch);
    $sql = $colToRowMngTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $colToRowMngTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $colToRowMngArray = $result;

    //////////////////////////
    // メニュー管理テーブルを検索
    //////////////////////////
    $menuListTable = new MenuListTable($objDBCA, $db_model_ch);
    $sql = $menuListTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $menuListTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $menuListArray = $result;
    $menuIdArray = array_column($menuListArray, 'MENU_ID');

    //////////////////////////
    // メニュー・テーブル紐付テーブルを検索
    //////////////////////////
    $menuTableLinkTable = new MenuTableLinkTable($objDBCA, $db_model_ch);
    $sql = $menuTableLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $menuTableLinkTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $menuTableLinkArray = $result;

    foreach($colToRowMngArray as $colToRowMng){

        // 変換済みフラグがONの場合スキップ
        if('1' == $colToRowMng['CHANGED_FLG']){
            continue;
        }

        // 変換元メニューが有効か確認する
        $result = array_search($colToRowMng['FROM_MENU_ID'], $menuIdArray);
        if(false === $result){
            continue;
        }

        // 変換先メニューが有効か確認する
        $result = array_search($colToRowMng['TO_MENU_ID'], $menuIdArray);
        if(false === $result){
            continue;
        }

        $updateCnt = 0;       // 更新件数
        $insertCnt = 0;       // 登録件数
        $disuseCnt = 0;       // 廃止件数

        //////////////////////////
        // 縦横変換処理を行う
        //////////////////////////
        changeColToRow($colToRowMng, $menuTableLinkArray);
    }

    if(LOG_LEVEL === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
    }
}
catch(Exception $e){
    if(LOG_LEVEL === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10003', basename( __FILE__, '.php' )));
    }
}

/*
 * 縦横変換処理
 */

function changeColToRow($colToRowMng, $menuTableLinkArray){
    global $objDBCA, $db_model_ch, $objMTS;
    global $updateCnt, $insertCnt, $disuseCnt;
    $tranStartFlg = false;
    $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // トランザクション開始
        //////////////////////////
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        //////////////////////////
        // テーブル情報取得処理（インプット用）を行う
        //////////////////////////
        $result = getTableInfo($colToRowMng['FROM_MENU_ID'], $menuTableLinkArray);

        if(!is_array($result)){
            throw new Exception();
        }
        $fromTableInfo = $result;

        $fromTableName = $fromTableInfo['TABLE_NAME'];
        $fromColList = $fromTableInfo['COL_LIST'];

        // データ部のカラム名一覧を作成する
        $fromDataColList = array();
        $startFlg = false;
        foreach($fromColList as $fromCol){
            if("NOTE" == $fromCol){
                break;
            }
            if(true === $startFlg){
                $fromDataColList[] = $fromCol;
            }
            if("INPUT_ORDER" == $fromCol){
                $startFlg = true;
            }
        }

        //////////////////////////
        // テーブル情報取得処理（アウトプット用）を行う
        //////////////////////////
        $result = getTableInfo($colToRowMng['TO_MENU_ID'], $menuTableLinkArray);

        if(!is_array($result)){
            throw new Exception();
        }
        $toTableInfo = $result;
        $toTableName = $toTableInfo['TABLE_NAME'];
        $toColList = $toTableInfo['COL_LIST'];

        // データ部のカラム名一覧を作成する
        $toDataColList = array();
        $startFlg = false;
        foreach($toColList as $toCol){
            if("NOTE" == $toCol){
                break;
            }
            if(true === $startFlg){
                $toDataColList[] = $toCol;
            }
            if("OPERATION_ID" == $toCol){
                $startFlg = true;
            }
        }

        // シーケンステーブルをロックする
        $resArray = getSequenceLockInTrz($toTableInfo['SEQ_MAIN'], 'A_SEQUENCE');
        if($resArray[1] != 0){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
            outputLog($msg);
            throw new Exception();
        }

        // シーケンステーブルをロックする(JNL)
        $resArray = getSequenceLockInTrz($toTableInfo['SEQ_JNL'], 'A_SEQUENCE');
        if($resArray[1] != 0){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
            outputLog($msg);
            throw new Exception();
        }

        //////////////////////////
        // インプット用のテーブルからデータを取得する
        //////////////////////////
        if("1" == $colToRowMng['PURPOSE']){
            $hostKeyName = "HOST_ID";
        }
        else{
            $hostKeyName = "KY_KEY";
        }

        $sql = "SELECT " . implode(",", $fromColList) .
               " FROM {$fromTableName} " .
               " WHERE DISUSE_FLAG='0' " .
               "ORDER BY {$hostKeyName},OPERATION_ID,INPUT_ORDER";

        // SQL実行
        $objQuery = null;
        $result = $baseTable->execQuery($sql, NULL, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            outputLog("SQL=$sql");
            throw new Exception();
        }

        $fromDataArray = array();
        while ($row = $objQuery->resultFetch()){
            $fromDataArray[] = $row;
        }

        //////////////////////////
        // アウトプット用のテーブルからデータを取得する
        //////////////////////////
        $sql = "SELECT " . implode(",", $toColList) .
               " FROM {$toTableName} " .
               " WHERE DISUSE_FLAG='0' ";

        // SQL実行
        $objQuery = null;
        $result = $baseTable->execQuery($sql, NULL, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception();
        }

        $toDataArray = array();
        while ($row = $objQuery->resultFetch()){
            $toDataArray[] = $row;
        }

        $hostKey = "";
        $operationId = "";
        $workArray = array();
        $existKey = array();

        foreach($fromDataArray as $fromData){

            if($hostKey == $fromData[$hostKeyName] &&  $operationId == $fromData['OPERATION_ID']){
                $workArray[] = $fromData;
            }
            else{
                //////////////////////////
                // データ登録処理を行う
                //////////////////////////
                $result = registData($hostKey,
                                     $operationId,
                                     $hostKeyName,
                                     $toTableInfo,
                                     $fromDataColList,
                                     $toColList,
                                     $toDataColList,
                                     $workArray,
                                     $toDataArray,
                                     $colToRowMng['START_COL_NAME'],
                                     $colToRowMng['COL_CNT'],
                                     $colToRowMng['REPEAT_CNT']);

                if(false === $result){
                    throw new Exception();
                }

                $hostKey = $fromData[$hostKeyName];
                $operationId = $fromData['OPERATION_ID'];
                $workArray = array();
                $workArray[] = $fromData;
                $existKey[] = $hostKey . ":" . $operationId;
            }
        }

        //////////////////////////
        // データ登録処理を行う
        //////////////////////////
        $result = registData($hostKey,
                             $operationId,
                             $hostKeyName,
                             $toTableInfo,
                             $fromDataColList,
                             $toColList,
                             $toDataColList,
                             $workArray,
                             $toDataArray,
                             $colToRowMng['START_COL_NAME'],
                             $colToRowMng['COL_CNT'],
                             $colToRowMng['REPEAT_CNT']);

        if(false === $result){
            throw new Exception();
        }

        //////////////////////////
        // データ廃止処理を行う
        //////////////////////////
        $result = disuseData($hostKeyName,
                             $toTableInfo,
                             $toColList,
                             $toDataArray,
                             $existKey);

        if(false === $result){
            throw new Exception();
        }

        //////////////////////////
        // 登録/更新/廃止/復活があった場合
        //////////////////////////
        if(0 != $insertCnt || 0 != $updateCnt || 0 != $disuseCnt){

            //////////////////////////
            // 用途がホスト用の場合、代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
            //////////////////////////
            if("1" == $colToRowMng['PURPOSE']){

               if(file_exists(ROOT_DIR_PATH . "/libs/release/ita_ansible-driver")){

                    $sql = "UPDATE A_PROC_LOADED_LIST "
                          ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                          ."WHERE ROW_ID IN (2100020002, 2100020004, 2100020006)";

                    $objDBCA->setQueryTime();
                    $aryForBind = array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime());

                    // SQL実行
                    $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                        outputLog($msg);
                        outputLog($sql);
                        throw new Exception();
                    }
                }
            }
            //////////////////////////
            // 用途がホストグループ用の場合、ホストグループ分割対象の分割済みフラグをOFFにする
            //////////////////////////
            else{
                $sql = "UPDATE F_SPLIT_TARGET "
                       ."SET DIVIDED_FLG = :DIVIDED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                       ."WHERE INPUT_MENU_ID = :INPUT_MENU_ID";

                $objDBCA->setQueryTime();
                $aryForBind = array('DIVIDED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime(), 'INPUT_MENU_ID' => $colToRowMng['TO_MENU_ID']);

                // SQL実行
                $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                    outputLog($msg);
                    outputLog($sql);
                    throw new Exception();
                }
            }
        }

        //////////////////////////
        // パラメータシート縦横変換管理の縦横変換済みフラグをONにする
        //////////////////////////
        $sql = "UPDATE F_COL_TO_ROW_MNG "
              ."SET CHANGED_FLG = :CHANGED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
              ."WHERE ROW_ID = :ROW_ID AND LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP2";

        $objDBCA->setQueryTime();
        $aryForBind = array('CHANGED_FLG'               => "1",
                            'LAST_UPDATE_TIMESTAMP'     => $objDBCA->getQueryTime(),
                            'ROW_ID'                    => $colToRowMng['ROW_ID'],
                            'LAST_UPDATE_TIMESTAMP2'    => $colToRowMng['LAST_UPDATE_TIMESTAMP'],
                           );

        // SQL実行
        $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            outputLog($sql);
            throw new Exception($msg);
        }

        //////////////////////////
        // コミット
        //////////////////////////
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = false;

        return true;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        return false;
    }
}

/*
 * テーブル情報取得処理
 */
function getTableInfo($menuId, $menuTableLinkArray){
    global $objDBCA, $db_model_ch, $objMTS;

    $resultArray = array();
    $columnNames;

    // メニュー・テーブル紐付テーブルが特定できない場合はスキップ
    $menuTableLinkIdArray = array_column($menuTableLinkArray, 'MENU_ID');
    $matchKey = array_search($menuId, array_column($menuTableLinkArray, 'MENU_ID'));
    if(false === $matchKey){
        $msg = "F_MENU_TABLE_LINK is not identified. Menu id=[$menuId].";
        outputLog($msg);
        return false;
    }

    //////////////////////////
    // 対象のテーブル構造を登録
    //////////////////////////
    $targetTable = new BaseTable_CPM($objDBCA, $db_model_ch);
    $targetTable->tableName    = $menuTableLinkArray[$matchKey]['TABLE_NAME'];
    $targetTable->seqName      = $targetTable->tableName . '_RIC';
    $targetTable->jnlSeqName   = $targetTable->tableName . '_JSQ';

    $result = $targetTable->setColNames();

    if(true !== $result){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($result, true));
        outputLog($msg);
        return false;
    }

    $resultArray['TABLE_NAME'] = $targetTable->tableName;
    $resultArray['MAIN_KEY'] = $menuTableLinkArray[$matchKey]['KEY_COL_NAME'];
    $resultArray['SEQ_MAIN'] = $targetTable->seqName;
    $resultArray['SEQ_JNL'] = $targetTable->jnlSeqName;
    $resultArray['COL_LIST'] = array();
    foreach($targetTable->columnNames as $columnName){
        $resultArray['COL_LIST'][$columnName] = $columnName;
    }
    return $resultArray;
}

/*
 * データ登録処理
 */
function registData($hostKey, $operationId, $hostKeyName, $toTableInfo, $fromDataColList, $toColList, $toDataColList, $workArray, $toDataArray, $startColName, $targetColCnt, $repeatCnt){
    global $objDBCA, $db_model_ch, $objMTS;
    global $updateCnt, $insertCnt;
    $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);

    if(0 === count($workArray)){
        return true;
    }

    $dataPartsArray = array();
    // 前半部分のデータを設定する
    $beforeCnt = 0;
    foreach($fromDataColList as $fromDataCol){
        if($fromDataCol == $startColName){
            break;
        }
        $beforeCnt ++;
        $dataPartsArray[$fromDataCol] = $workArray[0][$fromDataCol];
    }

    // 繰り返し部分のデータを設定する
    for($i = 0; $i < $repeatCnt; $i++){

        $workKey = array_search($i + 1, array_column($workArray, 'INPUT_ORDER'));

        for($j = 0; $j < $targetColCnt; $j++){

            if(false !== $workKey){
                $dataPartsArray[$toDataColList[$beforeCnt + $i * $targetColCnt + $j]] = $workArray[$workKey][$fromDataColList[$beforeCnt + $j]];
            }
            else{
                $dataPartsArray[$toDataColList[$beforeCnt + $i * $targetColCnt + $j]] = "";
            }
        }
    }

    // 後半部分のデータを設定する
    for($k = 0; $k < count($fromDataColList) - $beforeCnt - $targetColCnt; $k++){
        $dataPartsArray[$toDataColList[$beforeCnt + $repeatCnt * $targetColCnt + $k]] = $workArray[0][$fromDataColList[$beforeCnt + $targetColCnt + $k]];
    }

    $targetData = null;
    // ホストとオペレーションが一致するデータを検索する
    foreach($toDataArray as $toData){
        if($hostKey == $toData[$hostKeyName] && $operationId == $toData['OPERATION_ID']){
            $targetData = $toData;
            break;
        }
    }

    // ホストとオペレーションが一致するデータがある場合
    if(null != $targetData){

        $updateData = $targetData;

        foreach($dataPartsArray as $key => $value){
            $updateData[$key] = $value;
        }
        $updateData['DISUSE_FLAG'] = "0";

        if($updateData != $targetData){
            // 更新する

            // JNLのIDを取得する
            $resArray = getSequenceValueFromTable($toTableInfo['SEQ_JNL'], 'A_SEQUENCE', false);
            if($resArray[1] != 0){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
                outputLog($msg);
                return false;
            }
            $jnlSeqNo = $resArray[0];

            $arrayConfig = $toColList;
            $arrayConfig['JOURNAL_SEQ_NO'] = 'JOURNAL_SEQ_NO';
            $arrayConfig['JOURNAL_ACTION_CLASS'] = 'JOURNAL_ACTION_CLASS';
            $arrayConfig['JOURNAL_REG_DATETIME'] = 'JOURNAL_REG_DATETIME';

            $arrayValue = array('JOURNAL_SEQ_NO'        => $jnlSeqNo,
                                'JOURNAL_ACTION_CLASS'  => '',
                                'JOURNAL_REG_DATETIME'  => '',
                               );

            $arrayValue = array_merge($arrayValue, $updateData);
            $arrayValue['DISUSE_FLAG'] = '0';
            $arrayValue['LAST_UPDATE_TIMESTAMP'] = '';
            $arrayValue['LAST_UPDATE_USER'] =-101604;

            // SQL作成
            $tmpAry = array();
            $resArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 'UPDATE',
                                                 $toTableInfo['MAIN_KEY'],
                                                 $toTableInfo['TABLE_NAME'],
                                                 $toTableInfo['TABLE_NAME'] . '_JNL',
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $tmpAry
                                                );

            list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

            // SQL実行
            $objQuery = null;
            $result = $baseTable->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                return false;
            }

            // SQL実行
            $objQuery = null;
            $result = $baseTable->execQuery($sqlJnlBody, $arrayJnlBind, $objQuery);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                return false;
            }
            $updateCnt++;
        }
    }
    // ホストとオペレーションが一致するデータがない場合
    else{
        // 登録する

        // IDを取得する
        $resArray = getSequenceValueFromTable($toTableInfo['SEQ_MAIN'], 'A_SEQUENCE', false);
        if($resArray[1] != 0){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
            outputLog($msg);
            return false;
        }
        $seqNo = $resArray[0];

        // JNLのIDを取得する
        $resArray = getSequenceValueFromTable($toTableInfo['SEQ_JNL'], 'A_SEQUENCE', false);
        if($resArray[1] != 0){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
            outputLog($msg);
            return false;
        }
        $jnlSeqNo = $resArray[0];

        $arrayConfig = $toColList;
        $arrayConfig['JOURNAL_SEQ_NO'] = 'JOURNAL_SEQ_NO';
        $arrayConfig['JOURNAL_ACTION_CLASS'] = 'JOURNAL_ACTION_CLASS';
        $arrayConfig['JOURNAL_REG_DATETIME'] = 'JOURNAL_REG_DATETIME';

        $arrayValue = array('JOURNAL_SEQ_NO'        => $jnlSeqNo,
                            'JOURNAL_ACTION_CLASS'  => '',
                            'JOURNAL_REG_DATETIME'  => '',
                           );

        $arrayValue = array_merge($arrayValue, $dataPartsArray);
        $arrayValue[$toTableInfo['MAIN_KEY']] = $seqNo;
        $arrayValue[$hostKeyName] = $hostKey;
        $arrayValue['OPERATION_ID'] = $operationId;
        $arrayValue['NOTE'] = '';
        $arrayValue['DISUSE_FLAG'] = '0';
        $arrayValue['LAST_UPDATE_TIMESTAMP'] = '';
        $arrayValue['LAST_UPDATE_USER'] =-101604;

        // SQL作成
        $tmpAry = array();
        $resArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             'INSERT',
                                             $toTableInfo['MAIN_KEY'],
                                             $toTableInfo['TABLE_NAME'],
                                             $toTableInfo['TABLE_NAME'] . '_JNL',
                                             $arrayConfig,
                                             $arrayValue,
                                             $tmpAry
                                            );

        list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

        // SQL実行
        $objQuery = null;
        $result = $baseTable->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
        if(true !== $result){
            outputLog("SQL=[$sqlUtnBody]");
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            return false;
        }

        // SQL実行
        $objQuery = null;
        $result = $baseTable->execQuery($sqlJnlBody, $arrayJnlBind, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            return false;
        }
        $insertCnt++;
    }

    return true;
}

/*
 * データ廃止処理
 */
function disuseData($hostKeyName, $toTableInfo, $toColList, $toDataArray, $existKey){
    global $objDBCA, $db_model_ch, $objMTS;
    global $disuseCnt;
    $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);

    foreach($toDataArray as $toData){

        if(false === array_search($toData[$hostKeyName] . ":" . $toData['OPERATION_ID'], $existKey)){
            // 廃止する

            $updateData = $toData;

            // JNLのIDを取得する
            $resArray = getSequenceValueFromTable($toTableInfo['SEQ_JNL'], 'A_SEQUENCE', false);
            if($resArray[1] != 0){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', print_r($resArray[2], true));
                outputLog($msg);
                return false;
            }
            $jnlSeqNo = $resArray[0];

            $arrayConfig = $toColList;
            $arrayConfig['JOURNAL_SEQ_NO'] = 'JOURNAL_SEQ_NO';
            $arrayConfig['JOURNAL_ACTION_CLASS'] = 'JOURNAL_ACTION_CLASS';
            $arrayConfig['JOURNAL_REG_DATETIME'] = 'JOURNAL_REG_DATETIME';

            $arrayValue = array('JOURNAL_SEQ_NO'        => $jnlSeqNo,
                                'JOURNAL_ACTION_CLASS'  => '',
                                'JOURNAL_REG_DATETIME'  => '',
                               );

            $arrayValue = array_merge($arrayValue, $updateData);
            $arrayValue['DISUSE_FLAG'] = '1';
            $arrayValue['LAST_UPDATE_TIMESTAMP'] = '';
            $arrayValue['LAST_UPDATE_USER'] =-101604;

            // SQL作成
            $tmpAry = array();
            $resArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 'UPDATE',
                                                 $toTableInfo['MAIN_KEY'],
                                                 $toTableInfo['TABLE_NAME'],
                                                 $toTableInfo['TABLE_NAME'] . '_JNL',
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $tmpAry
                                                );

            list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

            // SQL実行
            $objQuery = null;
            $result = $baseTable->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                return false;
            }

            // SQL実行
            $objQuery = null;
            $result = $baseTable->execQuery($sqlJnlBody, $arrayJnlBind, $objQuery);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                return false;
            }
            $disuseCnt++;
        }
    }

    return true;
}
