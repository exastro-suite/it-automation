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
 *    ホストグループ変数登録機能
 *      ホストグループ変数紐付を元に
 *      ITAの作業対象ホストと代入値管理にデータを設定する。
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
    $tranStartFlg = false;
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // ホストグループ変数紐付(Ansible-Legacy)テーブルを検索
    //////////////////////////
    $hgVarLinkLegacyTable = new HgVarLinkLegacyTable($objDBCA, $db_model_ch);
    $sql = $hgVarLinkLegacyTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $hgVarLinkLegacyTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $hostGrpVarLinkArray = $result;

    if(0 === count($hostGrpVarLinkArray)){
        // 終了ログ出力
        if(LOG_LEVEL === 'DEBUG'){
            // 終了ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10002', basename( __FILE__, '.php' )));
        }
        return true;

    }

    // トランザクション開始
    $result = $objDBCA->transactionStart();
    if(false === $result){
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', array($result));
        outputLog($msg);
        throw new Exception($msg);
    }
    $tranStartFlg = true;

    // 変数名一覧登録処理を行う
    $result = registVarsMaster($hostGrpVarLinkArray);

    if(false === $result) {
        throw new Exception();
    }

    // Movement変数紐付管理登録処理を行う
    $result = registPatternVarsLinkMaster($hostGrpVarLinkArray);

    if(false === $result) {
        throw new Exception();
    }

    // 作業対象ホスト登録処理を行う
    $result = registPatternHostOpLinkMaster($hostGrpVarLinkArray);

    if(false === $result) {
        throw new Exception();
    }

    // 代入値管理登録処理を行う
    $result = registVarsAssignMaster($hostGrpVarLinkArray);

    if(false === $result) {
        throw new Exception();
    }

    // コミット
    $result = $objDBCA->transactionCommit();
    if(false === $result){
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $tranStartFlg = false;


    // 終了ログ出力
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10002', basename( __FILE__, '.php' )));
    }
    return true;
}
catch(Exception $e){
    // ロールバック
    if(true === $tranStartFlg){
        $objDBCA->transactionRollback();
    }
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10003', basename( __FILE__, '.php' )));
    }
    return false;
}

/**
 * 変数名一覧登録処理
 * 
 */
function registVarsMaster(&$hostGrpVarLinkArray) {

    global $objMTS, $objDBCA, $db_model_ch;
    $updateCnt = 0;       // 更新件数
    $insertCnt = 0;       // 登録件数
    $disuseCnt = 0;       // 廃止件数
    $ansibleLnsVarsMasterTable = new AnsibleLnsVarsMasterTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 変数名一覧テーブルを検索
        //////////////////////////
        $sql = $ansibleLnsVarsMasterTable->createSselect("");

        // SQL実行
        $result = $ansibleLnsVarsMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $varsMasterArray = $result;

        $varsNameArray = array();

        // 紐付データの件数分ループ
        foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

            if(in_array($hostGrpVarLink['VARS_NAME'], $varsNameArray)){
                continue;
            }
            $varsNameArray[] = $hostGrpVarLink['VARS_NAME'];

            $matchFlg = false;
            $updateData = NULL;
            $insertData = NULL;

            // 変数名一覧の件数分ループ
            foreach($varsMasterArray as $varsMaster) {

                // 変数名一覧のデータと変数名が一致する場合
                if($hostGrpVarLink['VARS_NAME'] === $varsMaster['VARS_NAME']) {
                    $updateData = $varsMaster;
                    $matchFlg = true;
                    break;
                }
            }

            // 変数名が一致するデータがある場合
            if(true === $matchFlg) {

                // 廃止の場合
                if("1" === $varsMaster['DISUSE_FLAG']) {

                    // 復活する
                    $updateData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

                    //////////////////////////
                    // 変数名一覧テーブルを更新
                    //////////////////////////
                    $result = $ansibleLnsVarsMasterTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
            }

            // 変数名が一致するデータがない場合
            else {
                // 登録する
                $insertData = array();
                $insertData['VARS_NAME']        = $hostGrpVarLink['VARS_NAME']; // 変数名
                $insertData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

                //////////////////////////
                // 変数名一覧テーブルに登録
                //////////////////////////
                $result = $ansibleLnsVarsMasterTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $insertCnt ++;
            }
        }

        // 廃止処理のため、変数名一覧の件数分ループ
        foreach($varsMasterArray as $varsMaster) {

            // 最終更新者が他のユーザの場合
            if(USER_ID_REGIST_HOST_GRP_VAR != $varsMaster['LAST_UPDATE_USER']) {
                continue;
            }
            $matchFlg = false;
            $disuseData = $varsMaster;

            // 紐付データの件数分ループ
            foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

                // 変数名一覧のデータと変数名が一致する場合
                if($hostGrpVarLink['VARS_NAME'] === $disuseData['VARS_NAME']) {
                    $matchFlg = true;
                    break;
                }
            }

            if(true === $matchFlg) {
                continue;
            }

            // 廃止の場合
            if("1" === $disuseData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $disuseData['DISUSE_FLAG']      = "1";                          // 廃止フラグ
            $disuseData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

            //////////////////////////
            // 変数名一覧テーブルを更新
            //////////////////////////
            $result = $ansibleLnsVarsMasterTable->updateTable($disuseData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        //////////////////////////
        // 変数名一覧テーブルを検索
        //////////////////////////
        $sql = $ansibleLnsVarsMasterTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansibleLnsVarsMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $varsMasterArray = $result;

        // 紐付データに変数IDを設定する
        foreach($hostGrpVarLinkArray as &$hostGrpVarLink) {

            foreach($varsMasterArray as $varsMaster) {

                if($hostGrpVarLink['VARS_NAME'] === $varsMaster['VARS_NAME']) {
                    $hostGrpVarLink['VARS_NAME_ID'] = $varsMaster['VARS_NAME_ID'];
                    break;
                }
            }
        }
        unset($hostGrpVarLink);

        if(LOG_LEVEL === 'DEBUG'){
            // 件数ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10004', array($ansibleLnsVarsMasterTable->tableName, $insertCnt, $updateCnt, $disuseCnt)));
        }

        return true;
    }
    catch(Exception $e){
        return false;
    }
}

    /**
     * Movement変数紐付管理登録処理
     * 
     */
    function registPatternVarsLinkMaster(&$hostGrpVarLinkArray) {

    global $objMTS, $objDBCA, $db_model_ch;
    $updateCnt = 0;       // 更新件数
    $insertCnt = 0;       // 登録件数
    $disuseCnt = 0;       // 廃止件数
    $ansLnsPtnVarsLinkTable = new AnsLnsPtnVarsLinkTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // Movement変数紐付管理テーブルを検索
        //////////////////////////
        $sql = $ansLnsPtnVarsLinkTable->createSselect("");

        // SQL実行
        $result = $ansLnsPtnVarsLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $patternVarsLinkMasterArray = $result;

        $patternVarsArray = array();

        // 紐付データの件数分ループ
        foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

            if(in_array($hostGrpVarLink['PATTERN_ID'] . "_" . $hostGrpVarLink['VARS_NAME_ID'], $patternVarsArray)){
                continue;
            }
            $patternVarsArray[] = $hostGrpVarLink['PATTERN_ID'] . "_" . $hostGrpVarLink['VARS_NAME_ID'];

            $matchFlg = false;
            $updateData = NULL;
            $insertData = NULL;

            // Movement変数紐付管理の件数分ループ
            foreach($patternVarsLinkMasterArray as $patternVarsLinkMaster) {

                // Movement変数紐付管理のデータとMovement、変数名が一致する場合
                if($hostGrpVarLink['PATTERN_ID']    === $patternVarsLinkMaster['PATTERN_ID'] &&
                   $hostGrpVarLink['VARS_NAME_ID']  === $patternVarsLinkMaster['VARS_NAME_ID']
                  ) {
                    $updateData = $patternVarsLinkMaster;
                    $matchFlg = true;
                    break;
                }
            }

            // Movement、変数名が一致するデータがある場合
            if(true === $matchFlg) {
            
                // 廃止の場合
                if("1" === $patternVarsLinkMaster['DISUSE_FLAG']) {

                    // 復活する
                    $updateData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

                    //////////////////////////
                    // Movement変数紐付管理テーブルを更新
                    //////////////////////////
                    $result = $ansLnsPtnVarsLinkTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
            }
            
            // Movement、変数名が一致するデータがない場合
            else {

                // 登録する
                $insertData = array();
                $insertData['PATTERN_ID']       = $hostGrpVarLink['PATTERN_ID'];    // Movement
                $insertData['VARS_NAME_ID']     = $hostGrpVarLink['VARS_NAME_ID'];  // 変数名
                $insertData['DISUSE_FLAG']      = "0";                              // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;      // 最終更新者

                //////////////////////////
                // Movement変数紐付管理テーブルに登録
                //////////////////////////
                $result = $ansLnsPtnVarsLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $insertCnt ++;
            }
        }

        // 廃止処理のため、Movement変数紐付管理の件数分ループ
        foreach($patternVarsLinkMasterArray as $patternVarsLinkMaster) {

            // 最終更新者が他のユーザの場合
            if(USER_ID_REGIST_HOST_GRP_VAR != $patternVarsLinkMaster['LAST_UPDATE_USER']) {
                continue;
            }
            $matchFlg = false;
            $disuseData = $patternVarsLinkMaster;

            // 紐付データの件数分ループ
            foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

                // Movement変数紐付管理のデータとMovement、変数名が一致する場合
                if($hostGrpVarLink['PATTERN_ID']    === $disuseData['PATTERN_ID'] &&
                   $hostGrpVarLink['VARS_NAME_ID']  === $disuseData['VARS_NAME_ID']
                  ) {
                    $matchFlg = true;
                    break;
                }
            }

            if(true === $matchFlg) {
                continue;
            }

            // 廃止の場合
            if("1" === $disuseData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $disuseData['DISUSE_FLAG']      = "1";                          // 廃止フラグ
            $disuseData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

            //////////////////////////
            // Movement変数紐付管理テーブルを更新
            //////////////////////////
            $result = $ansLnsPtnVarsLinkTable->updateTable($disuseData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        //////////////////////////
        // Movement変数紐付管理テーブルを検索
        //////////////////////////
        $sql = $ansLnsPtnVarsLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansLnsPtnVarsLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $patternVarsLinkMasterArray = $result;

        // 紐付データに変数IDを設定する
        foreach($hostGrpVarLinkArray as &$hostGrpVarLink) {

            foreach($patternVarsLinkMasterArray as $patternVarsLinkMaster) {

                // Movement変数紐付管理のデータとMovement、変数名が一致する場合
                if($hostGrpVarLink['PATTERN_ID']    === $patternVarsLinkMaster['PATTERN_ID'] &&
                   $hostGrpVarLink['VARS_NAME_ID']  === $patternVarsLinkMaster['VARS_NAME_ID']
                  ) {
                    $hostGrpVarLink['VARS_LINK_ID'] = $patternVarsLinkMaster['VARS_LINK_ID'];
                    break;
                }
            }
        }
        unset($hostGrpVarLink);

        if(LOG_LEVEL === 'DEBUG'){
            // 件数ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10004', array($ansLnsPtnVarsLinkTable->tableName, $insertCnt, $updateCnt, $disuseCnt)));
        }

        return true;
    }
    catch(Exception $e){
        return false;
    }
}

/**
 * 作業対象ホスト登録処理
 * 
 */
function registPatternHostOpLinkMaster(&$hostGrpVarLinkArray) {

    global $objMTS, $objDBCA, $db_model_ch;
    $updateCnt = 0;       // 更新件数
    $insertCnt = 0;       // 登録件数
    $disuseCnt = 0;       // 廃止件数
    $ansibleLnsPhoLinkTable = new AnsibleLnsPhoLinkTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 作業対象ホストテーブルを検索
        //////////////////////////
        $sql = $ansibleLnsPhoLinkTable->createSselect("");

        // SQL実行
        $result = $ansibleLnsPhoLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $patternHostOpLinkMasterArray = $result;

        $opePatternSystemArray = array();

        // 紐付データの件数分ループ
        foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

            if(in_array($hostGrpVarLink['OPERATION_NO_UAPK'] . "_" . $hostGrpVarLink['PATTERN_ID']. "_" . $hostGrpVarLink['SYSTEM_ID'], $opePatternSystemArray)){
                continue;
            }
            $opePatternSystemArray[] = $hostGrpVarLink['OPERATION_NO_UAPK'] . "_" . $hostGrpVarLink['PATTERN_ID']. "_" . $hostGrpVarLink['SYSTEM_ID'];

            $matchFlg = false;
            $updateData = NULL;
            $insertData = NULL;

            // 作業対象ホストの件数分ループ
            foreach($patternHostOpLinkMasterArray as $patternHostOpLinkMaster) {

                // 作業対象ホストのデータとオペレーション、Movement、ホストが一致する場合
                if($hostGrpVarLink['OPERATION_NO_UAPK'] === $patternHostOpLinkMaster['OPERATION_NO_UAPK'] &&
                   $hostGrpVarLink['PATTERN_ID']        === $patternHostOpLinkMaster['PATTERN_ID'] &&
                   $hostGrpVarLink['SYSTEM_ID']         === $patternHostOpLinkMaster['SYSTEM_ID']
                  ) {
                    $updateData = $patternHostOpLinkMaster;
                    $matchFlg = true;
                    break;
                }
            }

            // オペレーション、Movement、ホストが一致するデータがある場合
            if(true === $matchFlg) {
            
                // 廃止の場合
                if("1" === $patternHostOpLinkMaster['DISUSE_FLAG']) {

                    // 復活する
                    $updateData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

                    //////////////////////////
                    // 作業対象ホストテーブルを更新
                    //////////////////////////
                    $result = $ansibleLnsPhoLinkTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
            }
            
            // オペレーション、Movement、ホストが一致するデータがない場合
            else {

                // 登録する
                $insertData = array();
                $insertData['OPERATION_NO_UAPK']    = $hostGrpVarLink['OPERATION_NO_UAPK']; // オペレーション
                $insertData['PATTERN_ID']           = $hostGrpVarLink['PATTERN_ID'];        // Movement
                $insertData['SYSTEM_ID']            = $hostGrpVarLink['SYSTEM_ID'];         // ホスト
                $insertData['DISUSE_FLAG']          = "0";                                  // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_REGIST_HOST_GRP_VAR;          // 最終更新者

                //////////////////////////
                // 作業対象ホストテーブルに登録
                //////////////////////////
                $result = $ansibleLnsPhoLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $insertCnt ++;
            }
        }

        // 廃止処理のため、作業対象ホストの件数分ループ
        foreach($patternHostOpLinkMasterArray as $patternHostOpLinkMaster) {

            // 最終更新者が他のユーザの場合
            if(USER_ID_REGIST_HOST_GRP_VAR != $patternHostOpLinkMaster['LAST_UPDATE_USER']) {
                continue;
            }
            $matchFlg = false;
            $disuseData = $patternHostOpLinkMaster;

            // 紐付データの件数分ループ
            foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

                // 作業対象ホストのデータとオペレーション、Movement、ホストが一致する場合
                if($hostGrpVarLink['OPERATION_NO_UAPK'] === $disuseData['OPERATION_NO_UAPK'] &&
                   $hostGrpVarLink['PATTERN_ID']        === $disuseData['PATTERN_ID'] &&
                   $hostGrpVarLink['SYSTEM_ID']         === $disuseData['SYSTEM_ID']
                  ) {
                    $matchFlg = true;
                    break;
                }
            }

            if(true === $matchFlg) {
                continue;
            }

            // 廃止の場合
            if("1" === $disuseData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $disuseData['DISUSE_FLAG']      = "1";                          // 廃止フラグ
            $disuseData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

            //////////////////////////
            // 作業対象ホストテーブルを更新
            //////////////////////////
            $result = $ansibleLnsPhoLinkTable->updateTable($disuseData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        //////////////////////////
        // 作業対象ホストテーブルを検索
        //////////////////////////
        $sql = $ansibleLnsPhoLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansibleLnsPhoLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $patternHostOpLinkMasterArray = $result;

        // 紐付データに変数IDを設定する
        foreach($hostGrpVarLinkArray as &$hostGrpVarLink) {

            foreach($patternHostOpLinkMasterArray as $patternHostOpLinkMaster) {

                // 作業対象ホストのデータとオペレーション、Movement、ホストが一致する場合
                if($hostGrpVarLink['OPERATION_NO_UAPK'] === $patternHostOpLinkMaster['OPERATION_NO_UAPK'] &&
                   $hostGrpVarLink['PATTERN_ID']        === $patternHostOpLinkMaster['PATTERN_ID'] &&
                   $hostGrpVarLink['SYSTEM_ID']         === $patternHostOpLinkMaster['SYSTEM_ID']
                  ) {
                    $hostGrpVarLink['PHO_LINK_ID'] = $patternHostOpLinkMaster['PHO_LINK_ID'];
                    break;
                }
            }
        }
        unset($hostGrpVarLink);

        if(LOG_LEVEL === 'DEBUG'){
            // 件数ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10004', array($ansibleLnsPhoLinkTable->tableName, $insertCnt, $updateCnt, $disuseCnt)));
        }

        return true;
    }
    catch(Exception $e){
        return false;
    }
}

/**
 * 代入値管理登録処理
 * 
 */
function registVarsAssignMaster($hostGrpVarLinkArray) {

    global $objMTS, $objDBCA, $db_model_ch;
    $updateCnt = 0;       // 更新件数
    $insertCnt = 0;       // 登録件数
    $disuseCnt = 0;       // 廃止件数
    $linkDataArray = array();
    $ansibleLnsVarsAssignTable = new AnsibleLnsVarsAssignTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // ホストグループ変数化テーブルを検索
        //////////////////////////
        $hostgroupVarTable = new HostgroupVarTable($objDBCA, $db_model_ch);
        $sql = $hostgroupVarTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $hostgroupVarTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $hostGrpVarArray = $result;

        //////////////////////////
        // 機器一覧テーブルを検索
        //////////////////////////
        $stmListTable = new StmListTable($objDBCA, $db_model_ch);
        $sql = $stmListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $stmListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $stmListArray = $result;

        // 変数名を設定する
        foreach($hostGrpVarArray as &$hostGrpVar) {

            foreach($stmListArray as $stmList) {

                // システムIDが一致する場合
                if($hostGrpVar['HOSTNAME'] === $stmList['SYSTEM_ID']) {
                    $hostGrpVar['HOST_NAME'] = $stmList['HOSTNAME'];
                    break;
                }
            }
        }
        unset($hostGrpVar);
        // 紐付データに変数名を設定する
        foreach($hostGrpVarLinkArray as $hostGrpVarLink) {

            foreach($hostGrpVarArray as $hostGrpVar) {

                // 作業対象ホストのデータとオペレーション、Movement、ホストが一致する場合
                if($hostGrpVarLink['VARS_NAME'] === $hostGrpVar['VARS_NAME']) {
                    $linkDataArray[] = array('OPERATION_NO_UAPK'    => $hostGrpVarLink['OPERATION_NO_UAPK'],
                                             'PATTERN_ID'           => $hostGrpVarLink['PATTERN_ID'],
                                             'SYSTEM_ID'            => $hostGrpVarLink['SYSTEM_ID'],
                                             'VARS_LINK_ID'         => $hostGrpVarLink['VARS_LINK_ID'],
                                             'VARS_ENTRY'           => $hostGrpVar['HOST_NAME'],
                                            );
                }
            }
        }
        if(0 < count($linkDataArray)) {
            foreach($linkDataArray as $key => $data) {
                $varNames[$key] = $data['VARS_LINK_ID'];
                $values[$key] = $data['VARS_ENTRY'];
            }
            array_multisort($varNames,  SORT_ASC, $values,  SORT_ASC, $linkDataArray);
        }

        //////////////////////////
        // 代入地管理テーブルを検索
        //////////////////////////
        $sql = $ansibleLnsVarsAssignTable->createSselect("");

        // SQL実行
        $result = $ansibleLnsVarsAssignTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $varsAssignMasterArray = $result;

        // オペレーション、Movement、ホスト、変数名ごとの代入順序の最大値を取得する
        $maxAssignSeqArray = array();
        foreach($varsAssignMasterArray as $varsAssignMaster) {

            $key = strval($varsAssignMaster['OPERATION_NO_UAPK']) .
                   strval($varsAssignMaster['PATTERN_ID']) .
                   strval($varsAssignMaster['SYSTEM_ID']) .
                   strval($varsAssignMaster['VARS_LINK_ID']);

            if(array_key_exists($key, $maxAssignSeqArray)) {

                if($maxAssignSeqArray[$key] < $varsAssignMaster['ASSIGN_SEQ']) {
                    $maxAssignSeqArray[$key] = $varsAssignMaster['ASSIGN_SEQ'];
                }
            }
            else {
                $maxAssignSeqArray[$key] = $varsAssignMaster['ASSIGN_SEQ'];
            }
        }

        // ホストグループ変数データの件数分ループ
        foreach($linkDataArray as $key => $linkData) {

            $matchFlg = false;
            $updateData = NULL;
            $insertData = NULL;

            // 代入値管理の件数分ループ
            foreach($varsAssignMasterArray as $varsAssignMaster) {

                // 代入値管理のデータとオペレーション、Movement、ホスト、変数名、具体値が一致する場合
                if($linkData['OPERATION_NO_UAPK']   === $varsAssignMaster['OPERATION_NO_UAPK'] &&
                   $linkData['PATTERN_ID']          === $varsAssignMaster['PATTERN_ID'] &&
                   $linkData['SYSTEM_ID']           === $varsAssignMaster['SYSTEM_ID'] &&
                   $linkData['VARS_LINK_ID']        === $varsAssignMaster['VARS_LINK_ID'] &&
                   $linkData['VARS_ENTRY']          === $varsAssignMaster['VARS_ENTRY']
                  ) {
                    $updateData = $varsAssignMaster;
                    $matchFlg = true;
                    break;
                }
            }

            // オペレーション、Movement、ホスト、変数名、具体値が一致するデータがある場合
            if(true === $matchFlg) {

                // 廃止の場合
                if("1" === $varsAssignMaster['DISUSE_FLAG']) {

                    // 代入順序を決定する
                   $key = strval($linkData['OPERATION_NO_UAPK']) .
                          strval($linkData['PATTERN_ID']) .
                          strval($linkData['SYSTEM_ID']) .
                          strval($linkData['VARS_LINK_ID']);

                   if(array_key_exists($key, $maxAssignSeqArray)) {

                        $maxAssignSeqArray[$key] += 100;
                        $assignSeq = $maxAssignSeqArray[$key];
                   }
                   else {
                        $maxAssignSeqArray[$key] = 10000 + 100;
                        $assignSeq = $maxAssignSeqArray[$key];
                   }

                    // 復活する
                    $updateData['ASSIGN_SEQ']       = $assignSeq;                   // 代入順序
                    $updateData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

                    //////////////////////////
                    // 代入値管理テーブルを更新
                    //////////////////////////
                    $result = $ansibleLnsVarsAssignTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $updateCnt ++;
                }
            }
            
            // オペレーション、Movement、ホスト、変数名、具体値が一致するデータがない場合
            else {

                // 代入順序を決定する
               $key = strval($linkData['OPERATION_NO_UAPK']) .
                      strval($linkData['PATTERN_ID']) .
                      strval($linkData['SYSTEM_ID']) .
                      strval($linkData['VARS_LINK_ID']);

               if(array_key_exists($key, $maxAssignSeqArray)) {

                    $maxAssignSeqArray[$key] += 100;
                    $assignSeq = $maxAssignSeqArray[$key];
               }
               else {
                    $maxAssignSeqArray[$key] = 10000 + 100;
                    $assignSeq = $maxAssignSeqArray[$key];
               }

                // 登録する
                $insertData = array();
                $insertData['OPERATION_NO_UAPK']    = $linkData['OPERATION_NO_UAPK'];   // オペレーション
                $insertData['PATTERN_ID']           = $linkData['PATTERN_ID'];          // Movement
                $insertData['SYSTEM_ID']            = $linkData['SYSTEM_ID'];           // ホスト
                $insertData['VARS_LINK_ID']         = $linkData['VARS_LINK_ID'];        // 変数名
                $insertData['VARS_ENTRY']           = $linkData['VARS_ENTRY'];          // 具体値
                $insertData['ASSIGN_SEQ']           = $assignSeq;                       // 代入順序
                $insertData['DISUSE_FLAG']          = "0";                              // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_REGIST_HOST_GRP_VAR;      // 最終更新者

                //////////////////////////
                // 代入値管理テーブルに登録
                //////////////////////////
                $result = $ansibleLnsVarsAssignTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $insertCnt ++;
            }
        }

        // 廃止処理のため、代入値管理の件数分ループ
        foreach($varsAssignMasterArray as $varsAssignMaster) {

            // 最終更新者が他のユーザの場合
            if(USER_ID_REGIST_HOST_GRP_VAR != $varsAssignMaster['LAST_UPDATE_USER']) {
                continue;
            }
            $matchFlg = false;
            $disuseData = $varsAssignMaster;

            // ホストグループ変数データの件数分ループ
            foreach($linkDataArray as $linkData) {

                // 代入値管理のデータとオペレーション、Movement、ホスト、変数名、具体値が一致する場合
                if($linkData['OPERATION_NO_UAPK']   === $disuseData['OPERATION_NO_UAPK'] &&
                   $linkData['PATTERN_ID']          === $disuseData['PATTERN_ID'] &&
                   $linkData['SYSTEM_ID']           === $disuseData['SYSTEM_ID'] &&
                   $linkData['VARS_LINK_ID']        === $disuseData['VARS_LINK_ID'] &&
                   $linkData['VARS_ENTRY']          === $disuseData['VARS_ENTRY']
                  ) {
                    $matchFlg = true;
                    break;
                }
            }

            if(true === $matchFlg) {
                continue;
            }

            // 廃止の場合
            if("1" === $disuseData['DISUSE_FLAG']) {
                continue;
            }

            // 廃止する
            $disuseData['DISUSE_FLAG']      = "1";                          // 廃止フラグ
            $disuseData['LAST_UPDATE_USER'] = USER_ID_REGIST_HOST_GRP_VAR;  // 最終更新者

            //////////////////////////
            // 代入値管理テーブルを更新
            //////////////////////////
            $result = $ansibleLnsVarsAssignTable->updateTable($disuseData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
            $disuseCnt ++;
        }

        if(LOG_LEVEL === 'DEBUG'){
            // 件数ログ出力
            outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10004', array($ansibleLnsVarsAssignTable->tableName, $insertCnt, $updateCnt, $disuseCnt)));
        }

        return true;
    }
    catch(Exception $e){
        return false;
    }
}
