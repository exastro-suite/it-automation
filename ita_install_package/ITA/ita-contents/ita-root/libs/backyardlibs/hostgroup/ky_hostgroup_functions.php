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
 *    関数定義
 */

/**
 * loadTable関数を使うためにgetSchemeNAuthorityとdev_logを空で定義
 * 
 */
function getSchemeNAuthority(){
}
function dev_log(){
}


/**
 * ログを出力する
 * 
 * @param    string    $msg    出力するメッセージ
 */
function outputLog($msg){
    global $logPrefix;

    $bt = debug_backtrace();
    $file = basename($bt[0]['file']);
    $line = sprintf("%04d", $bt[0]['line']);

    $dt = '[' . date('Y/m/d H:i:s') . '][' . $file . '][' . $line . ']';
    $msg = $dt . $msg . "\n";
    $filePath = LOG_DIR . $logPrefix . date('Ymd') . '.log';
    error_log($msg, 3, $filePath);
}

/**
 * ツリー作成
 * 
 */
function makeTree(&$hierarchy) {

    global $objDBCA, $objMTS, $db_model_ch;

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

    // ループが発生している場合、処理終了
    if(in_array(1, array_column($hostLinkListArray, 'LOOPALARM'))) {
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5002');
        outputLog($msg);
        return false;
    }

    //////////////////////////
    // ホスト紐付管理テーブルを検索
    //////////////////////////
    $hostLinkTable = new HostLinkTable($objDBCA, $db_model_ch);
    $sql = $hostLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $hostLinkTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5001', $result);
        outputLog($msg);
        return false;
    }
    $hostLinkArray = $result;

    $treeArray = array();

    //////////////////////////
    // ホスト紐付管理テーブルのレコード数分ループ
    //////////////////////////
    foreach($hostLinkArray as $hostLink) {

        $parentMatchFlg = false;
        $childMatchFlg = false;
        // ツリー用配列の数分ループ
        foreach($treeArray as &$treeData) {

            // すでに子が登録されている場合
            if($treeData['KY_KEY'] === $hostLink['HOSTNAME']) {
                // 親を配列に追加
                $treeData['PARENT_IDS'][] = $hostLink['HOSTGROUP_NAME'] + 10000;
                $treeData['OPERATION'][] = $hostLink['OPERATION_ID'];
                $childMatchFlg = true;
            }
            // すでに親が登録されている場合
            if($treeData['KY_KEY'] === $hostLink['HOSTGROUP_NAME'] + 10000) {
                // 子を配列に追加
                $treeData['CHILD_IDS'][] = $hostLink['HOSTNAME'];
                $treeData['ALL_CHILD_IDS'][] = $hostLink['HOSTNAME'];
                $parentMatchFlg = true;
            }
        }

        if($childMatchFlg === false) {
            // 子追加
            $treeArray[] = array('KY_KEY' => $hostLink['HOSTNAME'],
                                 'OPERATION' =>  array($hostLink['OPERATION_ID']),
                                 'HIERARCHY' => 1,
                                 'DATA' => NULL,
                                 'PARENT_IDS' => array($hostLink['HOSTGROUP_NAME'] + 10000),
                                 'CHILD_IDS' => array(),
                                 'ALL_CHILD_IDS' => array(),
                                );

        }
        if($parentMatchFlg === false) {
            // 親追加
            $treeArray[] = array('KY_KEY' => $hostLink['HOSTGROUP_NAME'] + 10000,
                                 'OPERATION' => array(),
                                 'HIERARCHY' => 2,
                                 'DATA' => NULL,
                                 'PARENT_IDS' => array(),
                                 'CHILD_IDS' => array($hostLink['HOSTNAME']),
                                 'ALL_CHILD_IDS' => array($hostLink['HOSTNAME']),
                                );
        }
    }

    $stopFlg = false;
    $hierarchy = 1;

    while(false === $stopFlg) {

        $hierarchy ++;
        
        // 階層が一定数に達した場合、処理を終了する
        if(HIERARCHY_LIMIT < $hierarchy) {
            $msg = $objMTS->getSomeMessage('ITAHOSTGROUP-ERR-5003', $result);
            outputLog($msg);
            return false;
        }

        $treeUpdFlg = false;

        // ツリー用配列の数分ループ
        foreach($treeArray as &$treeData) {
        
            if($treeData['HIERARCHY'] != $hierarchy) {
                continue;
            }

            // ホストグループ親子紐付テーブルのレコード数分ループ
            foreach($hostLinkListArray as $hostLinkList) {

                if($treeData['KY_KEY'] != $hostLinkList['CH_HOSTGROUP'] + 10000) {
                    continue;
                }

                $treeUpdFlg = true;

                // 親を配列に追加
                $treeData['PARENT_IDS'][] = $hostLinkList['PA_HOSTGROUP'] + 10000;

                // すでに親が登録されているか確認
                $treeMatchFlg = false;
                foreach($treeArray as &$treeData2) {
                    if($treeData2['KY_KEY'] === $hostLinkList['PA_HOSTGROUP'] + 10000 && $treeData2['HIERARCHY'] === $hierarchy + 1) {
                        // 子を配列に追加
                        $treeData2['CHILD_IDS'][] = $hostLinkList['CH_HOSTGROUP'] + 10000;
                        $treeData2['ALL_CHILD_IDS'] = array_merge($treeData2['ALL_CHILD_IDS'], $treeData['ALL_CHILD_IDS']);
                        $treeMatchFlg = true;
                        break;
                    }
                }
                if(false === $treeMatchFlg) {
                    // 親追加
                    $treeArray[] = array('KY_KEY' => $hostLinkList['PA_HOSTGROUP'] + 10000,
                                         'OPERATION' => NULL,
                                         'HIERARCHY' => $hierarchy + 1,
                                         'DATA' => NULL,
                                         'PARENT_IDS' => array(),
                                         'CHILD_IDS' => array($hostLinkList['CH_HOSTGROUP'] + 10000),
                                         'ALL_CHILD_IDS' => $treeData['ALL_CHILD_IDS'],
                                        );
                }
            }
        }
        if($treeUpdFlg === false) {
            $stopFlg = true;
        }
    }

    return $treeArray;
}
