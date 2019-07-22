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
 * 【処理内容】
 *    ホストグループ分解機能
 *      ホストグループ単位に入力されている設計情報をホスト単位に分解する。
 */

if( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}

define('ROOT_DIR_PATH',         $root_dir_path);
require_once ROOT_DIR_PATH      . '/libs/backyardlibs/hostgroup/ky_hostgroup_env.php';
require_once HOSTGROUP_LIB_PATH . 'ky_hostgroup_classes.php';
require_once HOSTGROUP_LIB_PATH . 'ky_hostgroup_functions.php';
require_once COMMONLIBS_PATH    . 'common_php_req_gate.php';
require_once(WEBCOMMONLIBS_PATH . "/web_parts_for_request_init.php");

try{

    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10001', basename( __FILE__, '.php' )));
    }

    // ツリー作成
    $treeArray = makeTree($hierarchy);
    if(false === $treeArray) {
        throw new Exception();
    }

    if(LOG_LEVEL === 'DEBUG'){
        outputLog('function[makeTree] is finished.');
    }

    $targetArray = array();

    // 処理対象メニュー取得
    $result = getTargetMenu($targetArray);

    if(false === $result) {
        throw new Exception();
    }

    if(LOG_LEVEL === 'DEBUG'){
        outputLog('function[getTargetMenu] is finished.');
    }

    foreach($targetArray as $target){

        // 分割済みフラグがONの場合はスキップ
        if("1" == $target['DIVIDED_FLG']){
            continue;
        }

        //////////////////////////
        // 分割対象のテーブル構造を登録
        //////////////////////////
        $inputTable = new BaseTable($objDBCA, $db_model_ch);
        $inputTable->tableName    = $target['INPUT_TABLE_NAME'];
        $inputTable->seqName      = $inputTable->tableName . '_RIC';
        $inputTable->jnlSeqName   = $inputTable->tableName . '_JSQ';

        $result = $inputTable->setColNames();

        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            continue;
        }

        //////////////////////////
        // 登録対象のテーブル構造を登録
        //////////////////////////
        $outputTable = new BaseTable($objDBCA, $db_model_ch);
        $outputTable->tableName    = $target['OUTPUT_TABLE_NAME'];
        $outputTable->seqName      = $outputTable->tableName . '_RIC';
        $outputTable->jnlSeqName   = $outputTable->tableName . '_JSQ';

        $result = $outputTable->setColNames();

        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            continue;
        }

        $updateCnt = 0;       // 更新件数
        $insertCnt = 0;       // 登録件数
        $disuseCnt = 0;       // 廃止件数

        // INPUT_ORDERカラムを持っていない場合
        if(false === array_search('INPUT_ORDER', $inputTable->columnNames)){
            // ホストグループ分解
            $result = splitHostGrp($inputTable, $outputTable, $treeArray, $hierarchy, $target['ROW_ID'], $target['TIMESTAMP']);
        }
        // INPUT_ORDERカラムを持っている場合
        else{
            // ホストグループ分解（縦用）
            $result = splitHostGrpVertical($inputTable, $outputTable, $treeArray, $hierarchy, $target['ROW_ID'], $target['TIMESTAMP']);
        }

        if(false === $result) {
            continue;
        }

        if(LOG_LEVEL === 'DEBUG'){
            // 件数ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10004', array($outputTable->tableName, $insertCnt, $updateCnt, $disuseCnt)));
        }
    }

    // 終了ログ出力
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10002', basename( __FILE__, '.php' )));
    }
    return true;
}
catch(Exception $e){
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10003', basename( __FILE__, '.php' )));
    }
    return false;
}

/**
 * 処理対象メニュー取得
 * 
 */
function getTargetMenu(&$targetArray) {

    global $objMTS, $objDBCA, $db_model_ch;
    $splitTargetTable = new SplitTargetTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // ホストグループ分割対象テーブルを検索
        //////////////////////////
        $sql = $splitTargetTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $splitTargetTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $splitTargetArray = $result;

        //////////////////////////
        // メニュー管理テーブルを検索
        //////////////////////////
        $menuListTable = new MenuListTable($objDBCA, $db_model_ch);
        $sql = $menuListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $menuListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
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
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuTableLinkArray = $result;
        $menuTableLinkIdArray = array_column($menuTableLinkArray, 'MENU_ID');

        // ホストグループ分割対象の件数分ループ
        foreach($splitTargetArray as $splitTarget){

            // メニューIDが有効ではない場合はスキップ
            if(false === array_search($splitTarget['INPUT_MENU_ID'], $menuIdArray) || false === array_search($splitTarget['OUTPUT_MENU_ID'], $menuIdArray)){
                continue;
            }

            // メニュー・テーブル紐付テーブルが特定できない場合はスキップ
            $inputMenuTableLink = array_search($splitTarget['INPUT_MENU_ID'], $menuTableLinkIdArray);
            $outputMenuTableLink = array_search($splitTarget['OUTPUT_MENU_ID'], $menuTableLinkIdArray);
            if(false === $inputMenuTableLink || false === $outputMenuTableLink){
                continue;
            }

            $targetArray[] = array('INPUT_TABLE_NAME'   => $menuTableLinkArray[$inputMenuTableLink]['TABLE_NAME'],
                                   'OUTPUT_TABLE_NAME'  => $menuTableLinkArray[$outputMenuTableLink]['TABLE_NAME'],
                                   'ROW_ID'             => $splitTarget['ROW_ID'],
                                   'DIVIDED_FLG'        => $splitTarget['DIVIDED_FLG'],
                                   'TIMESTAMP'          => $splitTarget['LAST_UPDATE_TIMESTAMP'],
                                  );
        }

        return true;
    }
    catch(Exception $e){
        return false;
    }
}


/**
 * ホストグループ分解
 * 
 */
function splitHostGrp($inputTable, $outputTable, $treeArray, $hierarchy, $targetRowId, $targetTimestamp){

    global $objDBCA, $db_model_ch, $objMTS, $insertCnt, $updateCnt, $disuseCnt;
    $tranStartFlg = false;
    $idxs = array();

    try{
        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = true;

        // 入力用と出力用の可変部分のキーを取得する
        $idxs['FREE_START'] = array_search('OPERATION_ID', $inputTable->columnNames);
        if(false === $idxs['FREE_START']){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5005', $inputTable->tableName);
            outputLog($msg);
            throw new Exception($msg);
        }
        $idxs['FREE_START'] ++;

        $idxs['FREE_END'] = array_search('NOTE', $inputTable->columnNames);
        if(false === $idxs['FREE_END']){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5005', $inputTable->tableName);
            outputLog($msg);
            throw new Exception($msg);
        }
        $idxs['FREE_END'] --;

        // 入力用と出力用の項目が一致しているか確認する
        if(count($inputTable->columnNames) != count($outputTable->columnNames)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5006', array($inputTable->tableName, $outputTable->tableName));
            outputLog($msg);
            throw new Exception($msg);
        }

        for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 1; $i++){
            if($inputTable->columnNames[$i] != $inputTable->columnNames[$i]){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5006', array($inputTable->tableName, $outputTable->tableName));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        //////////////////////////
        // 入力用テーブルを検索
        //////////////////////////
        $sql = $inputTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $inputTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $inputDataArray = $result;

        // オペレーションID(実施予定日_ID_オペレーション名)の順に昇順に並べ替える
        if(0 < count($inputDataArray)) {
            foreach($inputDataArray as $key => $inputData) {
                $operationIds[$key] = $inputData['OPERATION_ID'];
            }
            unset($inputData);
            array_multisort($operationIds,  SORT_ASC, $inputDataArray);
        }

        //////////////////////////
        // 優先順位を取得するためにホストグループ一覧を検索
        //////////////////////////
        $hostgroupListTable = new HostgroupListTable($objDBCA, $db_model_ch);
        $sql = $hostgroupListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $hostgroupListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $hostGroupListArray = $result;

        //////////////////////////
        // 出力用テーブルを検索
        //////////////////////////
        $sql = $outputTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $outputTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $outputDataArray = $result;

        $sameIdArray = NULL;
        $holdHostID = array();

        foreach($inputDataArray as &$inputData) {

            // 優先順位を入力データと紐付ける
            $matchFlg = false;
            foreach($hostGroupListArray as $hostGroupList) {
                if($inputData['KY_KEY'] === $hostGroupList['ROW_ID'] + 10000) {
                    $inputData['STRENGTH'] = $hostGroupList['STRENGTH'];
                    $matchFlg = true;
                }
            }

            if(false === $matchFlg) {
                $inputData['STRENGTH'] = 0;
            }

            // 一回目の場合
            if(NULL === $sameIdArray) {
                $sameIdArray[] = $inputData;
                continue;
            }

            // オペレーションIDが一致した場合
            if($sameIdArray[0]['OPERATION_ID'] === $inputData['OPERATION_ID']) {
                $sameIdArray[] = $inputData;
                continue;
            }

            // オペレーションIDが異なっていた場合、ホストデータを作成する
            $result = makeHostData($idxs, $outputDataArray, $updateArray, $sameIdArray, $treeArray, $hierarchy, $holdHostID, $outputTable);

            if(false === $result) {
                return false;
            }

            $sameIdArray = NULL;
            $sameIdArray[] = $inputData;
        }
        unset($inputData);

        if(NULL != $sameIdArray) {
            // オペレーションIDが異なっていた場合、ホストデータを作成する
            $result = makeHostData($idxs, $outputDataArray, $updateArray, $sameIdArray, $treeArray, $hierarchy, $holdHostID, $outputTable);

            if(false === $result) {
                return false;
            }
        }

        // ホストデータの廃止を行うために対象のレコードを特定する
        foreach($outputDataArray as $outputData) {

            // 分割データにある場合は廃止しない
            if(in_array($outputData['HOST_ID'] . $outputData['OPERATION_ID'], $holdHostID)) {
                continue;
            }
            // すでに廃止の場合は廃止しない
            if("1" === $outputData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $updateData = $outputData;
            $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
            $updateData['LAST_UPDATE_USER'] = USER_ID_SPLIT_HOST_GRP;   // 最終更新者

            //////////////////////////
            // 出力用テーブルを更新
            //////////////////////////
            $result = $outputTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        //////////////////////////
        // 登録/更新/廃止/復活があった場合、代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
        //////////////////////////
        $baseTable = new BaseTable($objDBCA, $db_model_ch);
        if(0 != $insertCnt || 0 != $updateCnt || 0 != $disuseCnt){
           if(file_exists(ROOT_DIR_PATH . "/libs/release/ita_ansible-driver")){

                $sql = "UPDATE A_PROC_LOADED_LIST "
                      ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                      ."WHERE ROW_ID IN (2100020002, 2100020004, 2100020006)";

                $objDBCA->setQueryTime();
                $aryForBind = array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime());

                // SQL実行
                $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    outputLog($sql);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // ホストグループ分割対象の分割済みフラグをONにする
        //////////////////////////
        $sql = "UPDATE F_SPLIT_TARGET "
              ."SET DIVIDED_FLG = :DIVIDED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
              ."WHERE ROW_ID = :ROW_ID AND LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP2";

        $objDBCA->setQueryTime();
        $aryForBind = array('DIVIDED_FLG'               => "1",
                            'LAST_UPDATE_TIMESTAMP'     => $objDBCA->getQueryTime(),
                            'ROW_ID'                    => $targetRowId,
                            'LAST_UPDATE_TIMESTAMP2'    => $targetTimestamp,
                           );

        // SQL実行
        $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            outputLog($sql);
            throw new Exception($msg);
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
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

/**
 * ホストグループ分解（縦用）
 * 
 */
function splitHostGrpVertical($inputTable, $outputTable, $treeArray, $hierarchy, $targetRowId, $targetTimestamp){

    global $objDBCA, $db_model_ch, $objMTS, $insertCnt, $updateCnt, $disuseCnt;
    $tranStartFlg = false;
    $idxs = array();

    try{
        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = true;

        // 入力用と出力用の可変部分のキーを取得する
        $idxs['FREE_START'] = array_search('INPUT_ORDER', $inputTable->columnNames);
        if(false === $idxs['FREE_START']){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5005', $inputTable->tableName);
            outputLog($msg);
            throw new Exception($msg);
        }
        $idxs['FREE_START'] ++;

        $idxs['FREE_END'] = array_search('NOTE', $inputTable->columnNames);
        if(false === $idxs['FREE_END']){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5005', $inputTable->tableName);
            outputLog($msg);
            throw new Exception($msg);
        }
        $idxs['FREE_END'] --;

        // 入力用と出力用の項目が一致しているか確認する
        if(count($inputTable->columnNames) != count($outputTable->columnNames)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5006', array($inputTable->tableName, $outputTable->tableName));
            outputLog($msg);
            throw new Exception($msg);
        }

        for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 1; $i++){
            if($inputTable->columnNames[$i] != $inputTable->columnNames[$i]){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5006', array($inputTable->tableName, $outputTable->tableName));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        //////////////////////////
        // 入力用テーブルを検索
        //////////////////////////
        $sql = $inputTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $inputTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $inputDataArray = $result;

        // オペレーションID(実施予定日_ID_オペレーション名)、入力順序の順に昇順に並べ替える
        if(0 < count($inputDataArray)) {
            foreach($inputDataArray as $key => $inputData) {
                $operationIds[$key] = $inputData['OPERATION_ID'];
                $inputOrders[$key] = $inputData['INPUT_ORDER'];
            }
            unset($inputData);
            array_multisort($operationIds, SORT_ASC, $inputOrders, SORT_ASC, $inputDataArray);
        }

        //////////////////////////
        // 優先順位を取得するためにホストグループ一覧を検索
        //////////////////////////
        $hostgroupListTable = new HostgroupListTable($objDBCA, $db_model_ch);
        $sql = $hostgroupListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $hostgroupListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $hostGroupListArray = $result;

        //////////////////////////
        // 出力用テーブルを検索
        //////////////////////////
        $sql = $outputTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $outputTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $outputDataArray = $result;

        $sameIdArray = NULL;
        $holdHostID = array();

        foreach($inputDataArray as &$inputData) {

            // 優先順位を入力データと紐付ける
            $matchFlg = false;
            foreach($hostGroupListArray as $hostGroupList) {
                if($inputData['KY_KEY'] === $hostGroupList['ROW_ID'] + 10000) {
                    $inputData['STRENGTH'] = $hostGroupList['STRENGTH'];
                    $matchFlg = true;
                }
            }

            if(false === $matchFlg) {
                $inputData['STRENGTH'] = 0;
            }

            // 一回目の場合
            if(NULL === $sameIdArray) {
                $sameIdArray[] = $inputData;
                continue;
            }

            // オペレーションIDと入力順序が一致した場合
            if($sameIdArray[0]['OPERATION_ID'] === $inputData['OPERATION_ID'] && $sameIdArray[0]['INPUT_ORDER'] === $inputData['INPUT_ORDER']) {
                $sameIdArray[] = $inputData;
                continue;
            }

            // オペレーションIDと入力順序が異なっていた場合、ホストデータを作成する
            $result = makeHostData($idxs, $outputDataArray, $updateArray, $sameIdArray, $treeArray, $hierarchy, $holdHostID, $outputTable, true);

            if(false === $result) {
                return false;
            }

            $sameIdArray = NULL;
            $sameIdArray[] = $inputData;
        }
        unset($inputData);

        if(NULL != $sameIdArray) {
            // ホストデータを作成する
            $result = makeHostData($idxs, $outputDataArray, $updateArray, $sameIdArray, $treeArray, $hierarchy, $holdHostID, $outputTable, true);

            if(false === $result) {
                return false;
            }
        }

        // ホストデータの廃止を行うために対象のレコードを特定する
        foreach($outputDataArray as $outputData) {

            // 分割データにある場合は廃止しない
            if(in_array($outputData['HOST_ID'] . $outputData['OPERATION_ID'] .  $outputData['INPUT_ORDER'], $holdHostID)) {
                continue;
            }
            // すでに廃止の場合は廃止しない
            if("1" === $outputData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $updateData = $outputData;
            $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
            $updateData['LAST_UPDATE_USER'] = USER_ID_SPLIT_HOST_GRP;   // 最終更新者

            //////////////////////////
            // 出力用テーブルを更新
            //////////////////////////
            $result = $outputTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        //////////////////////////
        // ホストグループ分割対象の分割済みフラグをONにする
        //////////////////////////
        $sql = "UPDATE F_SPLIT_TARGET "
              ."SET DIVIDED_FLG = :DIVIDED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
              ."WHERE ROW_ID = :ROW_ID AND LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP2";

        $objDBCA->setQueryTime();
        $aryForBind = array('DIVIDED_FLG'               => "1",
                            'LAST_UPDATE_TIMESTAMP'     => $objDBCA->getQueryTime(),
                            'ROW_ID'                    => $targetRowId,
                            'LAST_UPDATE_TIMESTAMP2'    => $targetTimestamp
                           );

        // SQL実行
        $baseTable = new BaseTable($objDBCA, $db_model_ch);
        $result = $baseTable->execQuery($sql, $aryForBind, $objQuery);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            outputLog($sql);
            throw new Exception($msg);
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
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

/**
 * ホストデータ作成
 * 
 */
function makeHostData($idxs, $outputDataArray, &$updateArray, $sameIdArray, $treeArray, $hierarchy, &$holdHostID, $outputTable, $verticalFlg = false) {

    global $objDBCA, $db_model_ch, $objMTS, $insertCnt, $updateCnt, $disuseCnt;

    try{

        $aloneDataArray = array();

        // ツリー配列にデータを設定する
        foreach($sameIdArray as $sameIdData) {

            $treeMatchFlg = false;
            foreach($treeArray as &$treeData) {

                $treeData['ALL_PARENT_IDS'] = $treeData['PARENT_IDS'];
                if($sameIdData['KY_KEY'] == $treeData['KY_KEY']) {
                    $treeMatchFlg = true;
                    $treeData['DATA'] = $sameIdData;
                    $treeData['DATA_HIERARCHY'] = $treeData['HIERARCHY'];
                }
            }
            if(false === $treeMatchFlg){
                $aloneDataArray[] = $sameIdData;
            }
            unset($treeData);
        }

        // ツリー上にいなかったデータを単独で登録する
        foreach($aloneDataArray as $aloneData){

            // ホストグループの場合は無視する
            if($aloneData['KY_KEY'] > 10000){
                continue;
            }

            // 保有しているホストIDを退避しておく
            if(false === $verticalFlg){
                $holdHostID[] = $aloneData['KY_KEY'] . $aloneData['OPERATION_ID'];
            }
            else{
                $holdHostID[] = $aloneData['KY_KEY'] . $aloneData['OPERATION_ID'] . $aloneData['INPUT_ORDER'];
            }

            $matchFlg = false;
            foreach($outputDataArray as $outputData) {

                $updateData = NULL;
                $insertData = NULL;

                // ホストIDとオペレーションIDが一致した場合
                if(false === $verticalFlg){
                    // ホストIDとオペレーションIDが一致しなかった場合
                    if($outputData['HOST_ID']       !== $aloneData['KY_KEY'] ||
                       $outputData['OPERATION_ID']  !== $aloneData['OPERATION_ID']) {
                        continue;
                    }
                }
                else{
                    // ホストIDとオペレーションIDと入力順序が一致しなかった場合
                    if($outputData['HOST_ID']       !== $aloneData['KY_KEY'] ||
                       $outputData['OPERATION_ID']  !== $aloneData['OPERATION_ID'] ||
                       $outputData['INPUT_ORDER']   !== $aloneData['INPUT_ORDER']) {
                        continue;
                    }
                }

                $matchFlg = true;

                // 自由部分＋備考に差分があるか確認
                $chgFlg = false;
                for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {
                    if($outputData[$outputTable->columnNames[$i]] != $aloneData[$outputTable->columnNames[$i]]) {
                        $chgFlg = true;
                        break;
                    }
                }

                if(true === $chgFlg) {

                    // 更新する
                    $updateData = $outputData;
                    for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {
                        $updateData[$outputTable->columnNames[$i]] = $aloneData[$outputTable->columnNames[$i]];
                    }
                    $updateData['LAST_UPDATE_USER'] = USER_ID_SPLIT_HOST_GRP;   // 最終更新者

                    //////////////////////////
                    // 出力用テーブルを更新
                    //////////////////////////
                    $result = $outputTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
                break;
                
            }
            if(false === $matchFlg) {
                // 登録する
                $insertData = array();
                $insertData['HOST_ID']      = $aloneData['KY_KEY'];                 // ホスト名
                $insertData['OPERATION_ID'] = $aloneData['OPERATION_ID'];           // オペレーション/オペレーション
                if(true === $verticalFlg){
                    $insertData['INPUT_ORDER']  = $aloneData['INPUT_ORDER'];        // 入力順序
                }
                for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {                           // 自由部分＋備考
                    $insertData[$outputTable->columnNames[$i]] = $aloneData[$outputTable->columnNames[$i]];
                }

                $insertData['DISUSE_FLAG']          = "0";                                                  // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_SPLIT_HOST_GRP;                               // 最終更新者

                //////////////////////////
                // 出力用テーブルに登録
                //////////////////////////
                $result = $outputTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $insertCnt ++;
            }
        }

        // ホストデータを作成するために親から子にデータをコピーしていく
        for($i = $hierarchy; 1 < $i; $i--) {


            foreach($treeArray as $parentData) {

                if($i != $parentData['HIERARCHY']) {
                    continue;
                }
                // 親のデータが入っていない場合
                if(NULL === $parentData['DATA']) {
                    continue;
                }

                // 子のID分ループ
                foreach($parentData['CHILD_IDS'] as $childId) {
                    // 子を特定するためにツリー配列分ループ
                    foreach($treeArray as &$treeDataChild) {

                        // 階層が親の階層の一つ下であること
                        if($i - 1 != $treeDataChild['HIERARCHY']) {
                            continue;
                        }

                       // 子のIDと親のIDが紐づいている場合
                        if($childId == $treeDataChild['KY_KEY'] && in_array($parentData['KY_KEY'], $treeDataChild['PARENT_IDS'])) {

                        if(0 < count($treeDataChild['ALL_PARENT_IDS'])){
                            $treeDataChild['ALL_PARENT_IDS'] = array_merge($treeDataChild['ALL_PARENT_IDS'], $parentData['PARENT_IDS']);
                        }
                        else{
                            $treeDataChild['ALL_PARENT_IDS'] = array_merge($treeDataChild['PARENT_IDS'], $parentData['PARENT_IDS']);
                        }
                        $treeDataChild['ALL_PARENT_IDS'] = array_unique($treeDataChild['ALL_PARENT_IDS']);
                        $treeDataChild['ALL_PARENT_IDS'] = array_values($treeDataChild['ALL_PARENT_IDS']);

                            // 子のデータが入っていない場合
                            if(NULL === $treeDataChild['DATA']) {
                                // 親のデータをそのままコピー
                                $treeDataChild['DATA'] = $parentData['DATA'];
                                $treeDataChild['DATA_HIERARCHY'] = $parentData['DATA_HIERARCHY'];
                            }
                            // 親も子もデータが入っている場合
                            else {
                                $chgFlg = false;
                                // 親のデータと差分があるかすべての対象データで確認
                                for($j = $idxs['FREE_START']; $j < $idxs['FREE_END'] + 1; $j++) {
                                    // 親も子も値が入っていない場合
                                    if($treeDataChild['DATA'][$outputTable->columnNames[$j]] == "" && $parentData['DATA'][$outputTable->columnNames[$j]] == "") {
                                        // 何もしない
                                    }
                                    // 親のみに値が入っている場合
                                    else if($treeDataChild['DATA'][$outputTable->columnNames[$j]] == "" && $parentData['DATA'][$outputTable->columnNames[$j]] != "") {
                                        $treeDataChild['DATA'][$outputTable->columnNames[$j]] = $parentData['DATA'][$outputTable->columnNames[$j]];
                                        $chgFlg = true;
                                    }
                                    // 子のみに値が入っている場合
                                    else if($treeDataChild['DATA'][$outputTable->columnNames[$j]] != "" && $parentData['DATA'][$outputTable->columnNames[$j]] == "") {
                                        // 何もしない
                                    }
                                    // 親も子も値が入っている場合
                                    else {
                                        // 子のデータの階層が親のデータの階層よりも大きい場合
                                        if($treeDataChild['DATA_HIERARCHY'] > $parentData['DATA_HIERARCHY']) {
                                            $treeDataChild['DATA'][$outputTable->columnNames[$j]] = $parentData['DATA'][$outputTable->columnNames[$j]];
                                            $chgFlg = true;
                                        }
                                        // 子のデータの階層と親のデータの階層が同じ場合
                                        else if($treeDataChild['DATA_HIERARCHY'] ===  $parentData['DATA_HIERARCHY']) {
                                            // 子のデータの優先順位が親のデータの優先順位より小さい場合
                                            if($treeDataChild['DATA']['STRENGTH'] < $parentData['DATA']['STRENGTH']) {
                                                $treeDataChild['DATA'][$outputTable->columnNames[$j]] = $parentData['DATA'][$outputTable->columnNames[$j]];
                                                $chgFlg = true;
                                            }
                                         }
                                        // 子のデータの階層が親のデータの階層よりも小さい場合
                                        else {
                                            // 何もしない
                                        }
                                    }
                                }
                                if(false === $chgFlg) {
                                     $treeDataChild['DATA_HIERARCHY'] = $parentData['DATA_HIERARCHY'];
                                     $treeDataChild['DATA']['STRENGTH'] = $parentData['DATA']['STRENGTH'];
                                }
                            }
                        }
                    }
                    unset($treeDataChild);
                }
            }
            unset($parentData);
        }

        $kyKeyArray = array_column($treeArray, 'KY_KEY');

        // ホストデータの作成を行う
        foreach($treeArray as $key => $hostData) {

            if(1 != $hostData['HIERARCHY']) {
                continue;
            }

            if(NULL === $hostData['DATA']) {
                continue;
            }

            $matchKyKeyIdx = array_search($hostData['DATA']['KY_KEY'], $kyKeyArray);

            if(false === $matchKyKeyIdx){
                continue;
            }

            $opeMatchFlg = false;
            if(1 == $treeArray[$matchKyKeyIdx]['HIERARCHY']) {
                $opeMatchFlg = true;
            }
            else{
                foreach($hostData['PARENT_IDS'] as $parentIdKey => $parentId){

                    $matchParentKyKeyIdx = array_search($parentId, $kyKeyArray);
                    if(false === $matchParentKyKeyIdx){
                        continue;
                    }

                    if($hostData['DATA']['KY_KEY'] == $parentId ||
                       false !== array_search($hostData['DATA']['KY_KEY'], $treeArray[$matchParentKyKeyIdx]['ALL_PARENT_IDS'])){

                        if("" == $hostData['OPERATION'][$parentIdKey] ||
                           $hostData['DATA']['OPERATION_ID'] == $hostData['OPERATION'][$parentIdKey]){

                            $opeMatchFlg = true;
                        }
                    }
                }
            }

            if(false === $opeMatchFlg){
                continue;
            }

            // 上から継承されてきたデータのKY_KEYを自分のKY_KEYにする
            $hostData['DATA']['KY_KEY'] = $hostData['KY_KEY'];

            $matchFlg = false;
            foreach($outputDataArray as $outputData) {

                $updateData = NULL;
                $insertData = NULL;

                //// ホストIDとオペレーションIDが一致した場合
                if(false === $verticalFlg){
                    // ホストIDとオペレーションIDが一致しなかった場合
                    if($outputData['HOST_ID']       !== $hostData['DATA']['KY_KEY'] ||
                       $outputData['OPERATION_ID']  !== $hostData['DATA']['OPERATION_ID']) {
                        continue;
                    }
                }
                else{
                    // ホストIDとオペレーションIDと入力順序が一致しなかった場合
                    if($outputData['HOST_ID']       !== $hostData['DATA']['KY_KEY'] ||
                       $outputData['OPERATION_ID']  !== $hostData['DATA']['OPERATION_ID'] ||
                       $outputData['INPUT_ORDER']   !== $hostData['DATA']['INPUT_ORDER']) {
                        continue;
                    }
                }

                // 保有しているホストIDを退避しておく
                if(false === $verticalFlg){
                    $holdHostID[] = $hostData['KY_KEY'] . $hostData['DATA']['OPERATION_ID'];
                }
                else{
                    $holdHostID[] = $hostData['KY_KEY'] . $hostData['DATA']['OPERATION_ID'] . $hostData['DATA']['INPUT_ORDER'];
                }

                $matchFlg = true;

                // 自由部分＋備考に差分があるか確認
                $chgFlg = false;
                for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {
                    if($outputData[$outputTable->columnNames[$i]] != $hostData['DATA'][$outputTable->columnNames[$i]]) {
                        $chgFlg = true;
                        break;
                    }
                }

                // 廃止になっている場合は復活する
                if("1" === $outputData['DISUSE_FLAG']) {
                       $chgFlg = true;
                }

                if(true === $chgFlg) {

                    // 更新する
                    $updateData = $outputData;
                    for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {
                        $updateData[$outputTable->columnNames[$i]] = $hostData['DATA'][$outputTable->columnNames[$i]];
                    }
                    $updateData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_SPLIT_HOST_GRP;   // 最終更新者

                    //////////////////////////
                    // 出力用テーブルを更新
                    //////////////////////////
                    $result = $outputTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
            }

            if(false === $matchFlg) {

                // 保有しているホストIDを退避しておく
                if(false === $verticalFlg){
                    $holdHostID[] = $hostData['KY_KEY'] . $hostData['DATA']['OPERATION_ID'];
                }
                else{
                    $holdHostID[] = $hostData['KY_KEY'] . $hostData['DATA']['OPERATION_ID'] . $hostData['DATA']['INPUT_ORDER'];
                }

                // 登録する
                $insertData['HOST_ID']      = $hostData['DATA']['KY_KEY'];                  // ホスト名
                $insertData['OPERATION_ID'] = $hostData['DATA']['OPERATION_ID'];            // オペレーション/オペレーション
                if(true === $verticalFlg){
                    $insertData['INPUT_ORDER']  = $hostData['DATA']['INPUT_ORDER'];         // 入力順序
                }
                for($i = $idxs['FREE_START']; $i < $idxs['FREE_END'] + 2; $i++) {                           // 自由部分＋備考
                    $insertData[$outputTable->columnNames[$i]] = $hostData['DATA'][$outputTable->columnNames[$i]];
                }

                $insertData['DISUSE_FLAG']          = "0";                                                  // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_SPLIT_HOST_GRP;                               // 最終更新者

                //////////////////////////
                // 出力用テーブルに登録
                //////////////////////////
                $result = $outputTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }

                $insertCnt ++;
                
            }
        }
        return true;
    }
    catch(Exception $e){
        return false;
    }
}

