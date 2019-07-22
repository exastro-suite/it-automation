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
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//      多段変数最大繰返数メニュー反映ファイル
//          「多次元変数メンバー管理」のMEMBER_DISPが1のレコードを「多次元変数配列組合せ管理」に入れる。
//          その際、「多次元変数最大繰返数管理」にレコードがある変数については、DEFAULT_MAX_COL_SEQの数だけ膨らませる。
//
//  【その他】
//      多次元変数メンバー管理    : B_ANS_LRL_ARRAY_MEMBER
//      多次元変数配列組合せ管理 : B_ANS_LRL_MEMBER_COL_COMB
//      多次元変数最大繰返数管理 : B_ANS_LRL_MAX_MEMBER_COL
//
//////////////////////////////////////////////////////////////////////
define('LOCAL_DEBUG_EXPANDNESTEDVARIABLES',FALSE);

// ローカルデバック用のソース
// ↓↓↓↓↓↓↓↓↓↓↓↓↓
if(LOCAL_DEBUG_EXPANDNESTEDVARIABLES){
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if( empty($root_dir_path) ) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    // PHP エラー時のログ出力先を設定
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php       = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php     = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php       = '/libs/commonlibs/common_db_connect.php';

    $error_flag   = 0;
    $warning_flag = 0;

    $db_access_user_id      = -100013;
}
// ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
// ローカルデバック用のソース

        ////////////////////////////////
        // メイン処理（移植関数の使い方）    //
        ////////////////////////////////
        $ansible_libs_dir_path = '/libs/backyardlibs/ansible_driver/';

        // 多次元変数メンバー管理との同期処理
        require($root_dir_path . $ansible_libs_dir_path . 'ansible_syncDefaultMaxMemberCol.php');
        $ret = syncDefaultMaxMemberCol();
        if( $ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001010")) );
        }

        // 多段変数最大繰返数メニュー反映ファイル 本体
        require($root_dir_path . $ansible_libs_dir_path . 'ansible_expandNestedVariables.php');
        $ret = expandNestedVariables();
        if( $ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001010")) );
        }

function rollbackTransaction() {

    global    $objDBCA;
    global    $objMTS;
    global    $log_level;

    if($objDBCA->getTransactionMode()) {
        // ロールバック
        if($objDBCA->transactionRollBack() === true) {
            $traceMessage = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55016");
        } else {
            $traceMessage = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50045");
        }
        // トレースメッセージ
        if($log_level === 'DEBUG') {
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMessage);
        }
    }
}

function dbaccessSelect($sqlBody, $arrayBind, &$arrayResult) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $sqlBody: ' . $sqlBody . ', $arrayBind: ' . var_export($arrayBind, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    global $objQuery;
    global $objDBCA;
    global $objMTS;

    $objQuery = $objDBCA->sqlPrepare($sqlBody);
    if($objQuery->getStatus() === false) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    if(isset($arrayBind) && $objQuery->sqlBind($arrayBind) != "") {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    $r = $objQuery->sqlExecute();
    if(!$r) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    while($row = $objQuery->resultFetch()) {
        $arrayResult[] = $row;
    }

    return true;
}

function dbaccessExecute($sqlBody, $arrayBind) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $sqlBody: ' . $sqlBody . ', $arrayBind: ' . var_export($arrayBind, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    global $objQuery;
    global $objDBCA;
    global $objMTS;

    $objQuery = $objDBCA->sqlPrepare($sqlBody);
    if($objQuery->getStatus() === false) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    if(isset($arrayBind) && $objQuery->sqlBind($arrayBind) != "") {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    $r = $objQuery->sqlExecute();
    if(!$r) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    return true;
}

function dbaccessInsert($tableName, $specificColumn, $targetColumns, $insertRecords) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $tableName: ' . $tableName . ', $specificColumn: ' . $specificColumn
            . ', $insertRecords: ' . var_export($insertRecords, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    $insertKeyValue = null;
    foreach($insertRecords as $record) {

        $result = dbaccessInsertEach($tableName, $specificColumn, $targetColumns, $record);

        if($result == false) {
            return false;
        }
    }

    return true;
}

function dbaccessInsertEach($targetTable, $specificColumn, $targetColumns, $insertKeyValue) {

    global $db_model_ch;
    global $db_access_user_id;

    $strCurTable      = $targetTable;
    $strJnlTable      = $strCurTable . "_JNL";
    $strSeqOfCurTable = $strCurTable . "_RIC";
    $strSeqOfJnlTable = $strCurTable . "_JSQ";

    $curId = dbaccessGetSequence($strSeqOfCurTable);
    $jnlId = dbaccessGetSequence($strSeqOfJnlTable);

    if(!$curId || !$jnlId) {
        return false;
    }

    // 主キーカラム
    $insertKeyValue[$specificColumn]        = $curId;

    // ロール管理ジャーナルに登録する情報設定
    $insertKeyValue['JOURNAL_SEQ_NO']       = $jnlId;

    // 共通カラム
    $insertKeyValue['DISUSE_FLAG']     = "0";
    $insertKeyValue['LAST_UPDATE_USER']     = $db_access_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         "INSERT",
                                         $specificColumn,
                                         $strCurTable,
                                         $strJnlTable,
                                         $targetColumns, // 仮…keyしか使っていない？
                                         $insertKeyValue,
                                         $temp_array);

    $sqlCurBody = $retArray[1];
    $arrayCurBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!dbaccessExecute($sqlCurBody, $arrayCurBind) ||
        !dbaccessExecute($sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

function dbaccessUpdateRevive($tableName, $specificColumn, $targetColumns, $targetRecords) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $tableName: ' . $tableName . ', $specificColumn: ' . $specificColumn
            . ', $targetRecords: ' . var_export($targetRecords, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    $updateKeyValue = null;
    foreach($targetRecords as $record) {
        $record['DISUSE_FLAG'] = "0";
        $result = dbaccessUpdateEach($tableName, $specificColumn, $targetColumns, $record);

        if($result == false) {
            return false;
        }
    }

    return true;
}

function dbaccessUpdateDisuse($tableName, $specificColumn, $targetColumns, $targetRecords) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $tableName: ' . $tableName . ', $specificColumn: ' . $specificColumn
            . ', $targetRecords: ' . var_export($targetRecords, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    $updateKeyValue = null;
    foreach($targetRecords as $record) {

        $record['DISUSE_FLAG'] = "1";
        $result = dbaccessUpdateEach($tableName, $specificColumn, $targetColumns, $record);

        if($result == false) {
            return false;
        }
    }

    return true;
}

function dbaccessUpdateEach($targetTable, $specificColumn, $targetColumns, $updateKeyValue) {

    global $db_model_ch;
    global $db_access_user_id;

    $strCurTable      = $targetTable;
    $strJnlTable      = $strCurTable . "_JNL";
    $strSeqOfCurTable = $strCurTable . "_RIC";
    $strSeqOfJnlTable = $strCurTable . "_JSQ";

    $jnlId = dbaccessGetSequence($strSeqOfJnlTable);

    if(!$jnlId) {
        return false;
    }

    // ロール管理ジャーナルに登録する情報設定
    $updateKeyValue['JOURNAL_SEQ_NO']       = $jnlId;
    $updateKeyValue['LAST_UPDATE_USER']     = $db_access_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         "UPDATE",
                                         $specificColumn,
                                         $strCurTable,
                                         $strJnlTable,
                                         $targetColumns, // 仮…keyしか使っていない？
                                         $updateKeyValue,
                                         $temp_array);

    $sqlCurBody = $retArray[1];
    $arrayCurBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!dbaccessExecute($sqlCurBody, $arrayCurBind) ||
        !dbaccessExecute($sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

function dbaccessGetSequence($tableName) {

    ////////////////////////////////////////////////////////////////
    // テーブルシーケンスをロック                                 //
    ////////////////////////////////////////////////////////////////
    $retArray = getSequenceLockInTrz($tableName, 'A_SEQUENCE');
    if($retArray[1] != 0) {
        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000", array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        return null;
    }

    ////////////////////////////////////////////////////////////////
    // テーブルシーケンスを採番                                   //
    ////////////////////////////////////////////////////////////////
    $retArray = getSequenceValueFromTable($tableName, 'A_SEQUENCE', FALSE);
    if($retArray[1] != 0) {
        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000", array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        return null;
    }

    return $retArray[0];
}
function dbaccessSelect_ind($sqlBody, $arrayBind, &$arrayResult, &$arrayKeyListResult) {

    global    $log_level;
    if($log_level === "DEBUG") {
        $debugLog = 'PARAMS => $sqlBody: ' . $sqlBody . ', $arrayBind: ' . var_export($arrayBind, true);
        LocalLogPrint(basename(__FILE__), __LINE__, $debugLog);
    }

    global $objQuery;
    global $objDBCA;
    global $objMTS;

    $objQuery = $objDBCA->sqlPrepare($sqlBody);
    if($objQuery->getStatus() === false) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    if(isset($arrayBind) && $objQuery->sqlBind($arrayBind) != "") {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    $r = $objQuery->sqlExecute();
    if(!$r) {
        $message = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$message);
        $errorDetail = $objQuery->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorDetail);
        unset($objQuery);
        return false;
    }

    while($row = $objQuery->resultFetch()) {
        $Search_Key =   $row['VARS_NAME_ID'] . '_'
                      . $row['ARRAY_MEMBER_ID'] . '_'
                      . $row['COL_SEQ_VALUE'] . '_'
                      . $row['COL_COMBINATION_MEMBER_ALIAS'];
        $arrayKeyListResult[$Search_Key] = 1;
        $arrayResult[] = $row;
    }
    return true;
}
?>
