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
 *    ホストグループの親子関係がループしているかどうか確認する
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

try{
    $tranStartFlg = false;
    $logPrefix = basename( __FILE__, '.php' ) . '_';

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITAHOSTGROUP-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // ホストグループ親子紐付テーブルを検索
    //////////////////////////
    $hostLinkListTable = new HostLinkListTable($objDBCA, $db_model_ch);
    $sql = $hostLinkListTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $hostLinkListTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
        outputLog($msg);
        return false;
    }
    $hostLinkListArray = $result;

    // ホストグループ親子紐付の件数分ループ
    foreach($hostLinkListArray as $hostLinkList){

        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        $loopCnt = 0;
        // ループチェック関数を呼び出す
        $result = loopCheck($hostLinkListArray, $hostLinkList['CH_HOSTGROUP'], $hostLinkList['PA_HOSTGROUP'], $loopCnt);

        // ループしている場合
        if(true === $result){

            // ループアラームが1ではない場合
            if(1 != $hostLinkList['LOOPALARM']){
                // 更新する
                $updateData = $hostLinkList;
                $updateData['LOOPALARM'] = 1;   // ループアラーム

                //////////////////////////
                // ホストグループ親子紐付テーブルを更新
                //////////////////////////
                $result = $hostLinkListTable->updateRecord($updateData);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }
            }
        }
        // ループしていない場合
        else{

            // ループアラームが1の場合
            if(1 == $hostLinkList['LOOPALARM']){
                // 更新する
                $updateData = $hostLinkList;
                $updateData['LOOPALARM'] = NULL;    // ループアラーム

                //////////////////////////
                // ホストグループ親子紐付テーブルを更新
                //////////////////////////
                $result = $hostLinkListTable->updateRecord($updateData);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }
            }
        }
        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = false;
    }

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
 * ループチェック
 * 
 */
function loopCheck($hostLinkListArray, $checkHg, $paHg, &$loopCnt){

    foreach($hostLinkListArray as $hostLinkList){

        if($hostLinkList['CH_HOSTGROUP'] == $paHg){

            if($hostLinkList['PA_HOSTGROUP'] == $checkHg){
                return  true;
            }
            $loopCnt ++;
            if(HIERARCHY_LIMIT < $loopCnt){
                return  false;
            }
            if("" != $hostLinkList['PA_HOSTGROUP']){
                $result = loopCheck($hostLinkListArray, $checkHg, $hostLinkList['PA_HOSTGROUP'], $loopCnt);
                if(true === $result){
                    return true;
                }
            }
        }
    }
    return false;
}
