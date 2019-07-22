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
 * メニュー作成用関数群
 */
/**
 * 作成可能なメニューを取得する
 *
 * @return    arrray    $retAry    作成可能なメニュー一覧
 */
function makeMenuCheckbox(){
    global $g;

    $sql  = 'SELECT DISTINCT ';
    $sql .= ' UCMI.CREATE_MENU_ID,';
    $sql .= ' MENU_NAME ';
    $sql .= 'FROM F_CREATE_MENU_INFO UCMI ';
    $sql .= 'RIGHT JOIN F_CREATE_ITEM_INFO UCII ';
    $sql .= 'ON UCMI.CREATE_MENU_ID = UCII.CREATE_MENU_ID ';
    $sql .= 'WHERE ';
    $sql .= ' UCMI.DISUSE_FLAG = 0 ';
    $sql .= ' AND UCII.DISUSE_FLAG = 0 ';

    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();

    if ($res === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $checkboxMenuAry = array();
    while ($row = $objQuery->resultFetch()){
        $checkboxMenuAry[] = $row;
    }

    $retAry = array();
    foreach ($checkboxMenuAry as $key => $value) {
        $retAry[$value['CREATE_MENU_ID']] = htmlentities($value['MENU_NAME'],
                                                              ENT_QUOTES,
                                                              'utf-8');
    }
    return $retAry;

}
/**
 * タイムスタンプ文字列作成
 *
 * @return    string       タイムスタンプ文字列
 */
function getTimeStampString($time){
    $dt = new DateTime(date_create($time));
    return $dt->format("Y/m/d H:i:s").".".substr(explode(".",($time.""))[1],0,6);
}

/**
 * ユーザID取得
 *
 * @return           ユーザID
 */
function getUserId($username){

    global $g;
    $sql  = "SELECT USER_ID FROM A_ACCOUNT_LIST WHERE USERNAME ='".$username."'";

    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array('A_SEQUENCE', 'F_CREATE_MENU_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array('A_SEQUENCE', 'F_CREATE_MENU_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $tmpAry = array();
    $row = $objQuery->resultFetch();
    return $row['USER_ID'];
}


/**
 * シーケンス取得
 *
 * @return           シーケンス番号
 */
function getSeq($seqKey){

    global $g;
    $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = '".$seqKey."'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', $seqKey, basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', $seqKey, basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $row = $objQuery->resultFetch();
    return $row['VALUE'];

}
/**
 * シーケンス更新
 *
 */
function updateSeq($seqKey){

    global $g;
    $sql = "UPDATE A_SEQUENCE SET VALUE = VALUE + 1 WHERE NAME = '".$seqKey."'";

    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900017',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900017',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    return $res;

}
/**
 * トランザクション開始
 *
 */
function startTransaction(){
    global $g;
    $varTrzStart = $g['objDBCA']->transactionStart();
    if ($varTrzStart === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900015',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
}
/**
 * トランザクションコミット終了
 *
 */
function endTransaction(){
    global $g;
    $res = $g['objDBCA']->transactionCommit();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900036',
                                             array(basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $g['objDBCA']->transactionExit();
}


/**
 * メニュー作成ステータステーブルに挿入
 *
 * @return    arrray    $seq    挿入したシーケンス番号
 */
function insertCMStatus($create_menu_id){

    global $g;
    // ---- AD連携（外部認証）
    $userId = getUserId($_SESSION['ITA_SESSION']['username']);
    // AD連携（外部認証） ----

    // トランザクション開始
    startTransaction();

    // シーケンスを取得する
    $seq = getSeq("F_CREATE_MENU_STATUS_RIC");

    // 履歴l№を取得する    
    $seqj = getSeq("F_CREATE_MENU_STATUS_JSQ");

    //タイムスタンプ文字列取得
    $tsString = getTimeStampString( sprintf("%6F",microtime(true)));


    $sql  = "INSERT INTO F_CREATE_MENU_STATUS SET ";
    $sql  .= " MM_STATUS_ID =".$seq;
    $sql  .= " ,CREATE_MENU_ID =".$create_menu_id;
    $sql  .= " ,STATUS_ID = 1";
    $sql  .= " ,DISUSE_FLAG = 0";
    $sql  .= " ,LAST_UPDATE_TIMESTAMP = '". $tsString."'";
    $sql  .= " ,LAST_UPDATE_USER =".$userId;
    

    $sql_j  = "INSERT INTO F_CREATE_MENU_STATUS_JNL SET ";
    $sql_j  .= " JOURNAL_SEQ_NO =".$seqj;
    $sql_j  .= " ,JOURNAL_REG_DATETIME = '".$tsString."'";
    $sql_j  .= " ,JOURNAL_ACTION_CLASS = 'INSERT' ";
    $sql_j  .= " ,MM_STATUS_ID =".$seq;
    $sql_j  .= " ,CREATE_MENU_ID =".$create_menu_id;
    $sql_j  .= " ,STATUS_ID = 1";
    $sql_j  .= " ,DISUSE_FLAG = 0";
    $sql_j  .= " ,LAST_UPDATE_TIMESTAMP = '". $tsString."'";
    $sql_j  .= " ,LAST_UPDATE_USER =".$userId;
    
    $objQuery  = $g['objDBCA']->sqlPrepare($sql);
    $objQueryJ = $g['objDBCA']->sqlPrepare($sql_j);
    $insert_res    = $objQuery->sqlExecute();//ステータステーブル挿入
    $insert_res_j  = $objQueryJ->sqlExecute();//ステータス履歴テーブル挿入

    if ($insert_res === false) {
        web_log($sql);
        web_log($objQuery->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900020',
                                             array('F_CREATE_MENU_STATUS', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    if ($insert_res_j === false) {
        web_log($sql_j);
        web_log($objQueryJ->getLastError());
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900020',
                                             array('F_CREATE_MENU_STATUS_JNL', basename(__FILE__), __LINE__)));
        throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    //シーケンス更新
    updateSeq("F_CREATE_MENU_STATUS_RIC");
    updateSeq("F_CREATE_MENU_STATUS_JSQ");
    
    //シーケンス更新
    //履歴シーケンス更新
    // トランザクション終了 コミット
    endTransaction();

    return $seq;
}

