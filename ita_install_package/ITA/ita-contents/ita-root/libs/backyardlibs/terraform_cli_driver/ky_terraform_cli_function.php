<?php
//   Copyright 2022 NEC Corporation
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
$DBaccess_php                    = '/libs/backyardlibs/common/common_db_access.php';
require_once ($root_dir_path . $DBaccess_php);

$arrayConfig = array(
    "JOURNAL_SEQ_NO" => "",
    "JOURNAL_ACTION_CLASS" => "",
    "JOURNAL_REG_DATETIME" => "",
    "EXECUTION_NO" => "",
    "EXECUTION_USER" => "",
    "SYMPHONY_NAME" => "",
    "STATUS_ID" => "",
    "SYMPHONY_INSTANCE_NO" => "",
    "PATTERN_ID" => "",
    "I_PATTERN_NAME" => "",
    "I_TIME_LIMIT" => "",
    "I_TERRAFORM_WORKSPACE_ID" => "",
    "I_TERRAFORM_WORKSPACE" => "",
    "OPERATION_NO_UAPK" => "",
    "I_OPERATION_NAME" => "",
    "I_OPERATION_NO_IDBH" => "",
    "CONDUCTOR_NAME" => "",
    "CONDUCTOR_INSTANCE_NO" => "",
    "TIME_BOOK" => "DATETIME",
    "TIME_START" => "DATETIME",
    "TIME_END" => "DATETIME",
    "FILE_INPUT" => "",
    "FILE_RESULT" => "",
    "RUN_MODE" => "",
    "DISP_SEQ" => "",
    "ACCESS_AUTH" => "",
    "DISUSE_FLAG" => "",
    "NOTE" => "",
    "LAST_UPDATE_TIMESTAMP" => "",
    "LAST_UPDATE_USER" => ""
);
$arrayValue = array(
    "JOURNAL_SEQ_NO" => "",
    "JOURNAL_ACTION_CLASS" => "",
    "JOURNAL_REG_DATETIME" => "",
    "EXECUTION_NO" => "",
    "EXECUTION_USER" => "",
    "SYMPHONY_NAME" => "",
    "STATUS_ID" => "",
    "SYMPHONY_INSTANCE_NO" => "",
    "PATTERN_ID" => "",
    "I_PATTERN_NAME" => "",
    "I_TIME_LIMIT" => "",
    "I_TERRAFORM_WORKSPACE_ID" => "",
    "I_TERRAFORM_WORKSPACE" => "",
    "OPERATION_NO_UAPK" => "",
    "I_OPERATION_NAME" => "",
    "I_OPERATION_NO_IDBH" => "",
    "CONDUCTOR_NAME" => "",
    "CONDUCTOR_INSTANCE_NO" => "",
    "TIME_BOOK" => "",
    "TIME_START" => "",
    "TIME_END" => "",
    "FILE_INPUT" => "",
    "FILE_RESULT" => "",
    "RUN_MODE" => "",
    "DISP_SEQ" => "",
    "ACCESS_AUTH" => "",
    "DISUSE_FLAG" => "",
    "NOTE" => "",
    "LAST_UPDATE_TIMESTAMP" => "",
    "LAST_UPDATE_USER" => ""
);


// トランザクション開始
function cm_transactionStart($execution_no, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    try {
        if( $objDBCA->transactionStart()===false ){
            // トランザクションスタートが失敗しました。
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208020", array($execution_no));
            require ($root_dir_path . $log_output_php );
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                    array($execution_no,
                                                    basename(__FILE__),__LINE__));
            return false;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // "[処理]トランザクション開始 (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204010", array($execution_no));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    } catch (Exception $e){
        // トランザクションスタートが失敗しました。
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208020", array($execution_no));
        require ($root_dir_path . $log_output_php );
        // 例外メッセージ
        $FREE_LOG = $e->getMessage();
        return false;
    }
}

// コミット(レコードロックを解除)
function cm_transactionCommit($execution_no, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    try {
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // トランザクションのコミットに失敗しました。
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208040",array($execution_no));
            require ($root_dir_path . $log_output_php );
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                    array($execution_no,
                                                    basename(__FILE__), __LINE__));
            return false;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // "[処理]コミット(作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204020", array($execution_no));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    } catch (Exception $e){
        // トランザクションのコミットに失敗しました。
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208040",array($execution_no));
        require ($root_dir_path . $log_output_php );
        // 例外メッセージ
        $FREE_LOG = $e->getMessage();
        return false;
    }
}
// ロールバック
function cm_transactionRollBack($execution_no, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    try {
        if ($objDBCA->transactionRollBack() === false){
            // ロールバックに失敗しました。
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208050", array($execution_no));
            require ($root_dir_path . $log_output_php );

            return false;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // "[処理]ロールバック(作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204030", array($execution_no));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    } catch (Exception $e){
        // ロールバックに失敗しました。
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208050", array($execution_no));
        require ($root_dir_path . $log_output_php );
        // 例外メッセージ
        $FREE_LOG = $e->getMessage();
        return false;
    }
}

// トランザクション終了
function cm_transactionExit($execution_no) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    $objDBCA->transactionExit();
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        // "[処理]トランザクション終了(作業No.:{})";
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60006", array($execution_no));
        require ($root_dir_path . $log_output_php );
    }
    return true;
}

// シーケンスをロックし履歴シーケンス払い出し
function cm_dbaccessGetSequence($dbobj, $table_seq_name, $execution_no, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    $dbobj->ClearLastErrorMsg();
    $retArray = $dbobj->dbaccessGetSequence($table_seq_name);
    if($retArray === null) {
        // OK
        // 履歴シーケンスの採番に失敗しました。(作業No.:{} Sequence:{});
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208030", array($execution_no, $table_seq_name));
        require($root_dir_path . $log_output_php);
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                                array($execution_no,
                                                                basename(__FILE__), __LINE__)),
                                                                $dbobj->getLastErrorMsg());
        return false;
    }
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        // OK
        // "[処理]履歴シーケンス採番 (作業No.:{} Sequence:{})";
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204040", array($execution_no, $table_seq_name));
        require($root_dir_path . $log_output_php);
    }
    return $retArray;
}

// 処理対象の作業インスタンス情報取得
function cm_getEexecutionInstanceRow($dbobj, $in_execution_no, $in_exe_ins_msg_table_name, $in_exe_ins_msg_table_jnl_name, &$in_execution_row, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    global $arrayConfig;
    global $arrayValue;

    $temp_array = array('WHERE'=>"EXECUTION_NO = :EXECUTION_NO");

    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT FOR UPDATE",
                                            "EXECUTION_NO",
                                            $in_exe_ins_msg_table_name,
                                            $in_exe_ins_msg_table_jnl_name,
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array );
    $sqlUtnBody = $retArray[1];
    // $arrayUtnBind = $retArray[2];

    $dbobj->ClearLastErrorMsg();
    $arrayBind = array("EXECUTION_NO"=>sprintf("%d",$in_execution_no));
    $objQueryUtn = null;
    $ret = $dbobj->dbaccessExecute($sqlUtnBody, $arrayBind, $objQueryUtn);
    if($ret === false) {
        // "作業インスタンスの情報取得に失敗しました。(作業No.:{})";
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208100", array($in_execution_no));
        require($root_dir_path . $log_output_php);

        // ログ出力
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                                array($in_execution_no,
                                                                basename(__FILE__),__LINE__)),
                                                                $dbobj->getLastErrorMsg());
        require($root_dir_path . $log_output_php);
        return false;
    }

    $in_execution_row = $objQueryUtn->resultFetch();
    unset($objQueryUtn);

    return true;
}
// 処理対象の作業インスタンス情報更新
function cm_InstanceRecodeUpdate($dbobj, $in_exe_ins_msg_table_name, $in_exe_ins_msg_table_jnl_name, $in_execution_row, &$FREE_LOG) {
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;

    global $arrayConfig;

    $StatusUpdMsgArry         = array();
    $StatusUpdMsgArry['2']    = 'ITATERRAFORMCLI-STD-204050';
    $StatusUpdMsgArry['3']    = 'ITATERRAFORMCLI-STD-204060';
    $StatusUpdMsgArry['4']    = 'ITATERRAFORMCLI-STD-204070';
    $StatusUpdMsgArry['5']    = 'ITATERRAFORMCLI-STD-204080';
    $StatusUpdMsgArry['6']    = 'ITATERRAFORMCLI-STD-204090';
    $StatusUpdMsgArry['7']    = 'ITATERRAFORMCLI-STD-204100';
    $StatusUpdMsgArry['8']    = 'ITATERRAFORMCLI-STD-204110';
    $StatusUpdErrMsgArry      = array();
    $StatusUpdErrMsgArry['2'] = 'ITATERRAFORMCLI-ERR-208100';
    $StatusUpdErrMsgArry['3'] = 'ITATERRAFORMCLI-ERR-208110';
    $StatusUpdErrMsgArry['4'] = 'ITATERRAFORMCLI-ERR-208120';
    $StatusUpdErrMsgArry['5'] = 'ITATERRAFORMCLI-ERR-208130';
    $StatusUpdErrMsgArry['6'] = 'ITATERRAFORMCLI-ERR-208140';
    $StatusUpdErrMsgArry['7'] = 'ITATERRAFORMCLI-ERR-208150';
    $StatusUpdErrMsgArry['8'] = 'ITATERRAFORMCLI-ERR-208160';


    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         "UPDATE",
                                         "EXECUTION_NO",
                                         $in_exe_ins_msg_table_name,
                                         $in_exe_ins_msg_table_jnl_name,
                                         $arrayConfig,
                                         $in_execution_row );

    $sqlCurBody   = $retArray[1];
    $arrayCurBind = $retArray[2];
    $sqlJnlBody   = $retArray[3];
    $arrayJnlBind = $retArray[4];

    $dbobj->ClearLastErrorMsg();
    if(!$dbobj->dbaccessExecute($sqlCurBody, $arrayCurBind)) {
        $FREE_LOG = $objMTS->getSomeMessage($StatusUpdErrMsgArry[$in_execution_row['STATUS_ID']], array($in_execution_row['EXECUTION_NO']));
        require ($root_dir_path . $log_output_php );

        // ログ出力
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                              array($in_execution_row['EXECUTION_NO'],
                                                              basename(__FILE__),__LINE__)),
                                                              $dbobj->getLastErrorMsg());
        return false;
    }
    $dbobj->ClearLastErrorMsg();
    if(!$dbobj->dbaccessExecute($sqlJnlBody, $arrayJnlBind)) {
        $FREE_LOG = $objMTS->getSomeMessage($StatusUpdErrMsgArry[$in_execution_row['STATUS_ID']], array($in_execution_row['EXECUTION_NO']));
        require ($root_dir_path . $log_output_php );

        // ログ出力
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010",
                                                              array($in_execution_row['EXECUTION_NO'],
                                                              basename(__FILE__),__LINE__)),
                                                              $dbobj->getLastErrorMsg());

        return false;
    }
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = $objMTS->getSomeMessage($StatusUpdMsgArry[$in_execution_row['STATUS_ID']], array($in_execution_row['EXECUTION_NO']));
        require($root_dir_path . $log_output_php);
    }
    return true;
}
?>
