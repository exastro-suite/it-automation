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
    $ansible_table_define_php        = '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php';
    $DBaccess_php                    = '/libs/backyardlibs/common/common_db_access.php';
    require_once ($root_dir_path . $ansible_table_define_php);
    require_once ($root_dir_path . $DBaccess_php);

    function cm_getAnsibleInterfaceInfo($in_dbobj,$in_execution_no,&$in_ans_if_info,&$in_ErrorMsg) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $vg_info_table_name;

        $in_ans_if_info = array();
        $in_ErrorMsg     = "";
            
        ////////////////////////////////////////////////////////////////
        // ANSIBLEインタフェース情報を取得                            //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sqlBody = "SELECT *
                    FROM   D_ANSIBLE_TOWER_IF_INFO
                    WHERE  DISUSE_FLAG = '0' ";

        $in_dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery  = "";
        $ret = $in_dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // DB Access Error
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50065");
            require ($root_dir_path . $log_output_php );
            $in_ErrorMsg = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                     array($in_execution_no,
                                                                     basename(__FILE__),__LINE__)),
                                                                     $in_dbobj->getLastErrorMsg());
            return false;
        }

        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        if( $num_of_rows === 0 ){
            // ANSIBLEインタフェース情報レコード無し
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50062",$in_execution_no);
            return false;
        } else if( $num_of_rows > 1 ){
            // ANSIBLEインタフェース情報レコードが単一行でない
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50063",$in_execution_no);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $in_ans_if_info = $row;
        }

        unset($objQuery);
        
        return true;
    }
    function cm_getSymphonyInterfaceInfo($in_dbobj,$in_execution_no,&$in_if_info,&$in_ErrorMsg) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $in_if_info = array();
        $in_ErrorMsg     = "";
            
        ////////////////////////////////////////////////////////////////
        // Symphonyインタフェース情報を取得                           //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sqlBody = "SELECT *
                    FROM   C_SYMPHONY_IF_INFO
                    WHERE  DISUSE_FLAG = '0' ";

        $in_dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery  = "";
        $ret = $in_dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // DB Access Error
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50076");
            require ($root_dir_path . $log_output_php );
            $in_ErrorMsg = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                     array($in_execution_no,
                                                                     basename(__FILE__),__LINE__)),
                                                                     $in_dbobj->getLastErrorMsg());
            return false;
        }

        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        if( $num_of_rows === 0 ){
            // Symphonyインタフェース情報レコード無し
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50077",$in_execution_no);
            return false;
        } else if( $num_of_rows > 1 ){
            // Symphonyインタフェース情報レコードが単一行でない
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50078",$in_execution_no);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $in_if_info = $row;
        }

        unset($objQuery);
        
        return true;
    }
    function cm_getConductorInterfaceInfo($in_dbobj,$in_execution_no,&$in_if_info,&$in_ErrorMsg) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $in_if_info = array();
        $in_ErrorMsg     = "";
            
        ////////////////////////////////////////////////////////////////
        // Conductorインタフェース情報を取得                          //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sqlBody = "SELECT *
                    FROM   C_CONDUCTOR_IF_INFO
                    WHERE  DISUSE_FLAG = '0' ";

        $in_dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery  = "";
        $ret = $in_dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // DB Access Error
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50079");
            require ($root_dir_path . $log_output_php );
            $in_ErrorMsg = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                     array($in_execution_no,
                                                                     basename(__FILE__),__LINE__)),
                                                                     $in_dbobj->getLastErrorMsg());
            return false;
        }

        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        if( $num_of_rows === 0 ){
            // Conductorインタフェース情報レコード無し
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50080",$in_execution_no);
            return false;
        } else if( $num_of_rows > 1 ){
            // Conductorインタフェース情報レコードが単一行でない
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50081",$in_execution_no);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $in_if_info = $row;
        }

        unset($objQuery);
        
        return true;
    }
    function cm_getMovementInfo($in_dbobj,$in_MovementID,&$in_Movement_info,&$in_ErrorMsg) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $in_Movement_info = array();
        $in_ErrorMsg     = "";
            
        ////////////////////////////////////////////////////////////////
        // Movement情報を取得                                         //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sqlBody = "SELECT *
                    FROM   C_PATTERN_PER_ORCH
                    WHERE  PATTERN_ID = :PATTERN_ID AND DISUSE_FLAG = '0' ";

        $in_dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $arrayBind = array("PATTERN_ID"=>$in_MovementID);
        $objQuery  = "";
        $ret = $in_dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // DB Access Error
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50079");
            require ($root_dir_path . $log_output_php );
            $in_ErrorMsg = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                     array($in_execution_no,
                                                                     basename(__FILE__),__LINE__)),
                                                                     $in_dbobj->getLastErrorMsg());
            return false;
        }

        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        if( $num_of_rows === 0 ){
            // レコード無し
            $in_ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50083",$in_MovementID);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $in_Movement_info = $row;
        }

        unset($objQuery);
        
        return true;
    }

    // トランザクション開始
    function cm_transactionStart($execution_no,&$FREE_LOG) {
        global $objDBCA;
        global $objMTS;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            if( $objDBCA->transactionStart()===false ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50067",array($execution_no));
                require ($root_dir_path . $log_output_php );
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                     array($execution_no,
                                                     basename(__FILE__),__LINE__));
                return false;
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //$ary[50057] = "[処理]トランザクション開始 (作業No.:{})";
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50057",array($execution_no));
                require ($root_dir_path . $log_output_php );
            }
            return true;
        } catch (Exception $e){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50067",array($execution_no));
            require ($root_dir_path . $log_output_php );
            $FREE_LOG = $e->getMessage();
            return false;
        }
    }

    // コミット(レコードロックを解除)
    function cm_transactionCommit($execution_no,&$FREE_LOG) {
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50068",array($execution_no));
                require ($root_dir_path . $log_output_php );
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                     array($execution_no,
                                                     basename(__FILE__),__LINE__));
                return false;
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //$ary[51077] = "[処理]コミット(作業No.:{})";
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51077",$execution_no);
                require ($root_dir_path . $log_output_php );
            }
            return true;
        } catch (Exception $e){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50068",array($execution_no));
            require ($root_dir_path . $log_output_php );
            $FREE_LOG = $e->getMessage();
            return false;
        }
    }

    function cm_transactionRollBack($execution_no,&$FREE_LOG) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            $r = $objDBCA->transactionRollBack();
            if (!$r){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50070",array($execution_no));
                require ($root_dir_path . $log_output_php );
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                     array($execution_no,
                                                     basename(__FILE__),__LINE__));
                return false;
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //$ary[51081] = "[処理]ロールバック(作業No.:{})";
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51081",$execution_no);
                require ($root_dir_path . $log_output_php );
            }
            return true;
        } catch (Exception $e){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50070",array($execution_no));
            require ($root_dir_path . $log_output_php );
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
            //$ary[51078] = "[処理]トランザクション終了(作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51078",$execution_no);
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    // シーケンスをロックし履歴シーケンス払い出し
    function cm_dbaccessGetSequence($dbobj,$table_seq_name,$execution_no,&$FREE_LOG) {
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
            //$ary[50060] = "[警告]履歴シーケンスの採番に失敗しました。(作業No.:{} Sequence:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50060",$execution_no,$table_seq_name);
            require ($root_dir_path . $log_output_php );

            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                  array($execution_no,
                                                                  basename(__FILE__),__LINE__)),
                                                                  $dbobj->getLastErrorMsg());
            return false;

        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
// OK
            // $ary[50061] = "[処理]履歴シーケンス採番 (作業No.:{} Sequence:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50061",array($execution_no,$table_seq_name));
            require ($root_dir_path . $log_output_php );
        }
        return $retArray;
    }

    // 処理対象の作業インスタンス情報取得
    function cm_getEexecutionInstanceRow($dbobj,$in_execution_no,$in_exe_ins_msg_table_name, $in_exe_ins_msg_table_jnl_name, &$in_execution_row,&$FREE_LOG) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $arrayConfig = array();
        CreateExecInstMngArray($arrayConfig);
        SetExecInstMngColumnType($arrayConfig);

        $temp_array = array('WHERE'=>"EXECUTION_NO = :EXECUTION_NO");

        $arrayValue = array();
        CreateExecInstMngArray($arrayValue);

        $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                             "SELECT FOR UPDATE", 
                                             "EXECUTION_NO", 
                                             $in_exe_ins_msg_table_name,
                                             $in_exe_ins_msg_table_jnl_name,
                                             $arrayConfig, 
                                             $arrayValue, 
                                             $temp_array );
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $dbobj->ClearLastErrorMsg();
        $arrayBind = array("EXECUTION_NO"=>sprintf("%d",$in_execution_no));
        $objQueryUtn = "";
        $ret = $dbobj->dbaccessExecute($sqlUtnBody, $arrayBind, $objQueryUtn);
        if($ret === false) {
            // ary[50061] = "作業インスタンスの情報取得に失敗しました。(作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50061", array($in_execution_no));
            require ($root_dir_path . $log_output_php );

            // ログ出力
            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                   array($in_execution_no,
                                                                   basename(__FILE__),__LINE__)),
                                                                   $dbobj->getLastErrorMsg());
            return false;
        }

        $in_execution_row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        return true;

    }
    function cm_InstanceRecodeUpdate($dbobj,$in_exe_ins_msg_table_name,$in_exe_ins_msg_table_jnl_name,$in_execution_row,&$FREE_LOG) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $StatusUpdMsgArry         = array();
        $StatusUpdMsgArry['2']    = 'ITAANSIBLEH-STD-50062';
        $StatusUpdMsgArry['3']    = 'ITAANSIBLEH-STD-50063';
        $StatusUpdMsgArry['4']    = 'ITAANSIBLEH-STD-50064';
        $StatusUpdMsgArry['5']    = 'ITAANSIBLEH-STD-50065';
        $StatusUpdMsgArry['6']    = 'ITAANSIBLEH-STD-50066';
        $StatusUpdMsgArry['7']    = 'ITAANSIBLEH-STD-50067';
        $StatusUpdMsgArry['8']    = 'ITAANSIBLEH-STD-50068';
        $StatusUpdErrMsgArry      = array();
        $StatusUpdErrMsgArry['2'] = 'ITAANSIBLEH-ERR-50052';
        $StatusUpdErrMsgArry['3'] = 'ITAANSIBLEH-ERR-50053';
        $StatusUpdErrMsgArry['4'] = 'ITAANSIBLEH-ERR-50054';
        $StatusUpdErrMsgArry['5'] = 'ITAANSIBLEH-ERR-50055';
        $StatusUpdErrMsgArry['6'] = 'ITAANSIBLEH-ERR-50056';
        $StatusUpdErrMsgArry['7'] = 'ITAANSIBLEH-ERR-50057';
        $StatusUpdErrMsgArry['8'] = 'ITAANSIBLEH-ERR-50058';

        
        $arrayConfig2 = array();
        CreateExecInstMngArray($arrayConfig2);
        SetExecInstMngColumnType($arrayConfig2);

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "UPDATE",
                                             "EXECUTION_NO",
                                             $in_exe_ins_msg_table_name,
                                             $in_exe_ins_msg_table_jnl_name,
                                             $arrayConfig2,
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
            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
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
            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50051",
                                                                  array($in_execution_row['EXECUTION_NO'],
                                                                  basename(__FILE__),__LINE__)),
                                                                  $dbobj->getLastErrorMsg());

            return false;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage($StatusUpdMsgArry[$in_execution_row['STATUS_ID']], array($in_execution_row['EXECUTION_NO']));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }
?>
