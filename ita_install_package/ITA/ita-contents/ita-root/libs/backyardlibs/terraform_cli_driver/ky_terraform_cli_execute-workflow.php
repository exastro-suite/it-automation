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
//////////////////////////////////////////////////////////////////////
//
//  【概要】
//      １．Terraform 作業インスタンスのチェック
//      対象：ステータスおよび「準備中」および「実行中」
//      処理：
//          対象のインスタンスについて、プロセスが動いているかのチェックおよびエラー処理
//
//      ２．Terraform 作業インスタンス実行
//      対象：ステータス「未実行」
//      処理：
//          未実行の対象インスタンスを洗い出し、子プロセスを立ち上げて実行
//
//
/////////////////////////////////////////////////////////////////////

//----------------------------------------------
// 定数定義
//----------------------------------------------
$terraform_env                    = '/libs/backyardlibs/terraform_cli_driver/ky_terraform_cli_setenv.php';
$terraform_cli_function_php       = '/libs/backyardlibs/terraform_cli_driver/ky_terraform_cli_function.php';

$log_output_php                   = '/libs/backyardlibs/backyard_log_output.php';
$php_req_gate_php                 = '/libs/commonlibs/common_php_req_gate.php';
$db_connect_php                   = '/libs/commonlibs/common_db_connect.php';
$db_access_php                    = '/libs/backyardlibs/common/common_db_access.php';


//----------------------------------------------
// ローカル変数(全体)宣言
//----------------------------------------------
$error_flag                     = 0;        // 異常フラグ(1：異常発生)
$no_error_flag                  = 0;        // エラーではないが、exceptionを飛ばして最後の処理にまわしたときに使う
$db_access_user_id              = -101902;  // Terraform-CLI作業実行プロシージャ

$tgt_execute_order_array        = [];       // 処理対象の順番の連想配列（キー:EXECUTION_NO）
$tgt_execution_info_array       = [];       // 処理対象の情報の連想配列（キー:EXECUTION_NO）
$tgt_execution_no_array         = [];       // 処理対象のEXECUTION_NOのリストを格納
$executed_worksapce_array       = [];       // 実行中のワークスペースIDの配列

$num_of_parallel_exec           = 30;       //　同時並列実行の上限値
$num_of_run_instance            = 0;        //　現在、実行中のインスタンス数


$obj_descriptor_spec = null;
$ary_pipe = [];

//----------------------------------------------
// 変数・function定義
//----------------------------------------------
require_once($root_dir_path . $terraform_env);
require_once($root_dir_path . $terraform_cli_function_php);

//----------------------------------------------
// 業務処理開始
//----------------------------------------------
try {
    //----------------------------------------------
    // 共通モジュールの呼び出し
    //----------------------------------------------
    $aryOrderToReqGate = array('DBConnect' => 'LATE');
    require_once($root_dir_path . $php_req_gate_php);
    require_once($root_dir_path . $db_access_php);

    // 開始メッセージ
    if ($log_level === 'DEBUG') {
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-202010");
        require($root_dir_path . $log_output_php);
    }

    //----------------------------------------------
    // DBコネクト
    //----------------------------------------------
    require($root_dir_path . $db_connect_php);

    // トレースメッセージ
    if ($log_level === 'DEBUG') {
        // DBコネクト完了
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-202030");
        require($root_dir_path . $log_output_php);
    }

    $dbobj = new CommonDBAccessCoreClass($db_model_ch, $objDBCA, $objMTS, $db_access_user_id);

    //----------------------------------------------
    // 準備中/実行中の作業インスタンスに関して、実行プロセスがあるかの存在確認
    //----------------------------------------------
    $ret = ChildProcessExistCheck($dbobj);
    if($ret === false) {
        // 作業インスタンスの実行プロセスの起動確認が失敗しました。(作業No.:{})
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208180");

        // 異常フラグON
        $error_flag = 1;

        // 例外処理へ
        throw new Exception( $FREE_LOG );
    }

    //----------------------------------------------
    // TERRAFORMインタフェース情報取得
    //----------------------------------------------
    $lv_terraform_if_info = null;
    $sql = "SELECT *
            FROM   $vg_info_table_name
            WHERE  DISUSE_FLAG = '0' ";
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        // 異常フラグON
        $error_flag = 1;
        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010", array(__FILE__, __LINE__, "00000100")));
    }
    //SQL発行
    $r = $objQuery->sqlExecute();
    if (!$r) {
        // 異常フラグON
        $error_flag = 1;
        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010", array(__FILE__, __LINE__, "00000200")));
    }

    //行数を取得
    $num_of_rows = $objQuery->effectedRowCount();
    //レコード無しの場合は「TERRAFORMインタフェース情報」が登録されていないので以降の処理をスキップ
    if ($num_of_rows === 0) {
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ：TERRAFORMインタフェース情報レコード無し
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208190"));
    }
    //「TERRAFORMインタフェース情報」が重複登録されている場合も以降の処理をスキップ
    else if ($num_of_rows > 1) {
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ：TERRAFORMインタフェース情報レコードが単一行でない
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208191"));
    }
    //1レコード取得
    while ($row = $objQuery->resultFetch()) {
        $lv_terraform_if_info = $row;
    }

    // DBアクセス事後処理
    unset($objQuery);
    //----------------------------------------------
    // TERRAFORMインタフェース情報をローカル変数に格納
    //----------------------------------------------
    $num_of_parallel_exec = $lv_terraform_if_info['TERRAFORM_NUM_PARALLEL_EXEC'];
    // var_dump($lv_num_of_parallel_exec);

    //----------------------------------------------
    // 実行中の作業インスタンスを抽出
    // ・実行中の数を取得（同時実行数を制御するため）
    // ・実行中のワークスペースIDを保存
    //----------------------------------------------
    $sqlBody = "SELECT *
                FROM   $vg_exe_ins_msg_table_name
                WHERE  STATUS_ID in ($STATUS_PREPARE, $STATUS_PROCESSING, $STATUS_PROCESS_DELAYED) AND DISUSE_FLAG = '0' ";
    $dbobj->ClearLastErrorMsg();
    $arrayBind = [];
    $objQuery = null;
    $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
    if($ret === false) {
        // 実行中の作業インスタンスの取得に失敗しました。
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208200");
        require($root_dir_path . $log_output_php);

        // ログ出力
        // 異常発生([FILE]{}[LINE]{}[ETC-Code]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",
                                                                array(basename(__FILE__), __LINE__, "00000300")),
                                                                $dbobj->getLastErrorMsg());
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception($FREE_LOG);
    }

    // 実行中の数
    $num_of_run_instance = $objQuery->effectedRowCount();

    // 実行中のワークスペースIDを保存
    while ( $row = $objQuery->resultFetch() ){
        $workspace_id = $row['I_TERRAFORM_WORKSPACE_ID'];

        if(in_array($workspace_id, $executed_worksapce_array, true) === true) {
            continue;
        }
        $executed_worksapce_array[] = $workspace_id;
    }

    // トレースメッセージ
    if ($log_level === 'DEBUG') {
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204120", array($num_of_run_instance, implode(",", $executed_worksapce_array)));
        require($root_dir_path . $log_output_php);
    }

    unset($objQuery);

    //----------------------------------------------
    // 未実行の作業インスタンス取得
    //----------------------------------------------
    $execute_list = [];
    $sqlBody = "SELECT *
                FROM   $vg_exe_ins_msg_table_name
                WHERE  DISUSE_FLAG = '0' AND
                        (
                            ( TIME_BOOK IS NULL AND STATUS_ID = $STATUS_NOT_YET ) OR
                            ( TIME_BOOK <= NOW(6) AND STATUS_ID = $STATUS_RESERVE )
                        )";

    $dbobj->ClearLastErrorMsg();
    $arrayBind = [];
    $objQuery = null;
    $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
    if($ret === false) {
        // ログ出力
        // 
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208210");
        require($root_dir_path . $log_output_php);
        // 異常発生([FILE]{}[LINE]{}[ETC-Code]{})
        $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",
                                                                array(basename(__FILE__),__LINE__,"00000400")),
                                                                $dbobj->getLastErrorMsg());
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception($FREE_LOG);
    }

    //----------------------------------------------
    // 処理対象レコードが0件の場合は処理終了へ
    //----------------------------------------------
    if ($objQuery->effectedRowCount() < 1) {
        // 例外処理へ(例外ではないが・・・)
        $no_error_flag = 1;
        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204130") );
    }

    //----------------------------------------------
    // 作業インスタンス実行順リストを生成
    // ・並び替え（workspace混在・同一ワークスペースを含む）
    //----------------------------------------------
    while( $row = $objQuery->resultFetch() ){
        $id = $row['EXECUTION_NO'];

        //情報つめこみ
        $tgt_execution_info_array[$id]['EXECUTION_NO'] = $row['EXECUTION_NO'];
        $tgt_execution_info_array[$id]['I_TERRAFORM_WORKSPACE_ID'] = $row['I_TERRAFORM_WORKSPACE_ID'];
        $tgt_execution_info_array[$id]['I_TERRAFORM_WORKSPACE'] = $row['I_TERRAFORM_WORKSPACE'];
        // 予約時間 or 最終更新日+作業番号でリスト生成
        $tgt_execute_order_array[$id] = $row['LAST_UPDATE_TIMESTAMP'] . "-" . sprintf("%010d",$row['EXECUTION_NO']);
        if(strlen($row['TIME_BOOK']) != 0) {
            if($row['LAST_UPDATE_TIMESTAMP'] < $row['TIME_BOOK']) {
                $tgt_execute_order_array[$id] = $row['TIME_BOOK'] . "-" . sprintf("%010d",$row['EXECUTION_NO']);
            }
        }
    }
    // ソート
    asort($tgt_execute_order_array);
    // var_dump($tgt_execute_order_array);

    // EXECUTION_NOのみ保管
    $tgt_execution_no_array = array_keys($tgt_execute_order_array);

    // DBアクセス事後処理
    unset($objQuery);

    // トレースメッセージ
    if ($log_level === 'DEBUG') {
        // 処理対象候補のレコードを検出(EXECUTION_NOのリスト:{})
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204140", implode(",", $tgt_execution_no_array));
        require($root_dir_path . $log_output_php);
    }

    // トレースメッセージ
    if ($log_level === 'DEBUG') {
        // ステータス「準備中」へのUPDATEループ開始
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204150");
        require($root_dir_path . $log_output_php);
    }

    //----------------------------------------------
    // 実行順に作業インスタンスを実行
    //----------------------------------------------
    // var_dump($tgt_execute_order_array);
    foreach($tgt_execute_order_array as $id => $sort_keys){
        $tgt_data = $tgt_execution_info_array[$id];
        $execution_no = $tgt_data['EXECUTION_NO'];
        $workspace_id = $tgt_data['I_TERRAFORM_WORKSPACE_ID'];
        $workspace_name = $tgt_data['I_TERRAFORM_WORKSPACE'];

        //既に実行に回されたワークスペースは実行しない（次回のサービス実行まで待つ）
        if(in_array($workspace_id, $executed_worksapce_array, true) === true) {
            continue;
        }

        // 並列実行数判定
        if($num_of_run_instance >= $num_of_parallel_exec) {
            break;
        }
        $num_of_run_instance++;

        // 対象作業インスタンスを実行（ワークスペースを配列にキャッシュ）
        // トレースメッセージ
        if ($log_level === 'DEBUG') {
            // 処理対象レコードを検出(EXECUTION_NO:{} workspace-id:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204160", array($execution_no, $workspace_id));
            require($root_dir_path . $log_output_php);
        }
        $executed_worksapce_array[] = $workspace_id;
        $ret = InstanceExecution($tgt_data);
        if($ret === false) {
            //----------------------------------------------
            // 準備中にエラーが発生
            //   →作業インスタンスの状態を想定外エラーに設定する。
            //----------------------------------------------
            // 異常フラグON
            $error_flag = 1;
            // 作業インスタンスの実行プロセスが起動していません。(作業No.:{}:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208220", array($execution_no, $workspace_id));
            require ($root_dir_path . $log_output_php );

            // ステータスの更新に失敗しました。 (ステータス: 想定外エラー 作業No.:{})
            $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208160",array($execution_no));
            //----------------------------------------------
            // トランザクション開始
            //----------------------------------------------
            $ret = cm_transactionStart($execution_no, $FREE_LOG);
            if($ret === false) {
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // 処理対象の作業インスタンス情報取得
            //----------------------------------------------
            $execution_row = array();
            $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
            if($ret === false) {
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // シーケンスをロックし履歴シーケンス採番
            //----------------------------------------------
            $dbobj->ClearLastErrorMsg();
            $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
            if($intJournalSeqNo === false) {
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // 処理対象の作業インスタンスのステータスを想定外エラーに設定
            //----------------------------------------------
            $execution_row["JOURNAL_SEQ_NO"] = $intJournalSeqNo;
            if(strlen(trim($execution_row['TIME_START'])) == 0) {
                $execution_row['TIME_START'] = "DATETIMEAUTO(6)";
            }
            $execution_row['TIME_END'] = "DATETIMEAUTO(6)";
            $execution_row["STATUS_ID"] = $STATUS_EXCEPTION;
            $execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

            $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
            if($ret === false) {
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // コミット(レコードロックを解除)
            //----------------------------------------------
            $ret = cm_transactionCommit($execution_no, $FREE_LOG);
            if($ret === false) {
                throw new Exception($ErrorMsg);
            }
            //----------------------------------------------
            // トランザクション終了
            //----------------------------------------------
            cm_transactionExit($execution_no);

            // [処理]ステータスの更新 (ステータス: 想定外エラー 作業No.:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204100", array($execution_no));
            require ($root_dir_path . $log_output_php );

            break;
        }
    }

} catch (Exception $e) {
    if($no_error_flag == 1){
        if($log_level === 'DEBUG'){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require($root_dir_path . $log_output_php);
        }
    }else{
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        require($root_dir_path . $log_output_php);
    }

    // DBアクセス事後処理
    if (isset($objQuery)) unset($objQuery);

    // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
    // 念のためロールバック/トランザクション終了
    if( $objDBCA->getTransactionMode() ){
        // ロールバック
        $ret = cm_transactionRollBack('-', $FREE_LOG);
        if($ret === false) {
            require ($root_dir_path . $log_output_php );
        }
        // トランザクション終了
        cm_transactionExit('-');
    }
}
//----------------------------------------------
// 結果出力
//----------------------------------------------
// 処理結果コードを判定してアクセスログを出し分ける
if ($error_flag != 0) {
    // 終了メッセージ
    if ($log_level === 'DEBUG') {
        // プロシージャ終了(異常)
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-206030");
        require($root_dir_path . $log_output_php);
    }

    // リターンコード
    // 常駐プロセスが死なないようにした
    exit(2);
} else {
    // 終了メッセージ
    if ($log_level === 'DEBUG') {
        // プロシージャ終了(正常)
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-202020");
        require($root_dir_path . $log_output_php);
    }

    // リターンコード
    exit(0);
}

function ChildProcessExistCheck($dbobj) {
    global $terraform_env;

    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $db_access_user_id;

    //----------------------------------------------
    // 変数・function定義
    //----------------------------------------------
    require($root_dir_path . $terraform_env);

    try {
        // psコマンドでky_terraform_cli_execute-child-workflow.phpの起動リストを作成
        // psコマンドがマレに起動プロセスリストを取りこぼすことがあるので3回分を作成
        $strBuildCommand = "ps -efw|grep ky_terraform_cli_execute-child-workflow.php|grep -v grep";
        exec($strBuildCommand, $ps_array1, $ret);

        usleep(50000);   // sleep 50ms

        exec($strBuildCommand, $ps_array2, $ret);

        usleep(100000);  // sleep 100ms

        exec($strBuildCommand, $ps_array3, $ret);

        //----------------------------------------------
        // 実行中の作業インスタンス数取得
        //----------------------------------------------
        $sqlBody = "SELECT *
                    FROM   $vg_exe_ins_msg_table_name
                    WHERE  STATUS_ID in ($STATUS_PREPARE, $STATUS_PROCESSING, $STATUS_PROCESS_DELAYED) AND DISUSE_FLAG = '0' ";
        $dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery = null;
        $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // ログ出力
            // 異常発生([FILE]{}[LINE]{}[ETC-Code]{})
            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",
                                                                   array(basename(__FILE__),__LINE__,"00000500")),
                                                                   $dbobj->getLastErrorMsg());

            require ($root_dir_path . $log_output_php );

            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208200")); 
        }
        while( $row = $objQuery->resultFetch() ){
            $execution_no = $row['EXECUTION_NO'];
            $workspace_id = $row['I_TERRAFORM_WORKSPACE_ID'];
            // $workspace_name = $row['I_TERRAFORM_WORKSPACE'];
            $child_process_name = sprintf("%010s-%010s", $workspace_id, $execution_no);
            // var_dump($child_process_name);
            $is_hit = false;
            foreach($ps_array1 as $line) {
                $ret = preg_match("/$child_process_name/",$line);
                if($ret == 1){
                    $is_hit = true;
                    break;
                }
            }
            if($is_hit === false) {
                foreach($ps_array2 as $line) {
                    $ret = preg_match("/$child_process_name/",$line);
                    if($ret == 1){
                        $is_hit = true;
                        break;
                    }
                }
                if($is_hit === false) {
                    foreach($ps_array3 as $line) {
                        $ret = preg_match("/$child_process_name/",$line);
                        if($ret == 1){
                            $is_hit = true;
                            break;
                        }
                    }
                }
            }
            if($is_hit === true) {
                if ($log_level === 'DEBUG') {
                    // "実行中インスタンスの子プロセスが存在していることを確認しました。(作業No.:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204170", array($execution_no));
                    require($root_dir_path . $log_output_php);
                }
                continue;
            }

            //----------------------------------------------
            // 作業インスタンスの状態が準備中/実行中でプロセスが存在していない
            //   →作業インスタンスの状態を想定外エラーに設定する。
            //----------------------------------------------

            // 作業インスタンスの実行プロセスが起動していません。(作業No.:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208220", array($execution_no, $workspace_id));
            require ($root_dir_path . $log_output_php );

            // ステータスの更新に失敗しました。 (ステータス: 想定外エラー 作業No.:{})
            $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208160", array($execution_no));
            //----------------------------------------------
            // トランザクション開始
            //----------------------------------------------
            $ret = cm_transactionStart($execution_no, $FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // 処理対象の作業インスタンス情報取得
            //----------------------------------------------
            $execution_row = array();
            $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row,$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // シーケンスをロックし履歴シーケンス採番
            //----------------------------------------------
            $dbobj->ClearLastErrorMsg();
            $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
            if($intJournalSeqNo === false) {
                require ($root_dir_path . $log_output_php );
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // 処理対象の作業インスタンスのステータスを想定外エラーに設定
            //----------------------------------------------
            $execution_row["JOURNAL_SEQ_NO"] = $intJournalSeqNo;
            if(strlen(trim($execution_row['TIME_START'])) == 0) {
                $execution_row['TIME_START'] = "DATETIMEAUTO(6)";
            }
            $execution_row['TIME_END'] = "DATETIMEAUTO(6)";
            $execution_row["STATUS_ID"] = $STATUS_EXCEPTION;
            $execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

            $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php);
                throw new Exception($ErrorMsg);
            }

            //----------------------------------------------
            // コミット(レコードロックを解除)
            //----------------------------------------------
            $ret = cm_transactionCommit($execution_no, $FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                throw new Exception($ErrorMsg);
            }
            //----------------------------------------------
            // トランザクション終了
            //----------------------------------------------
            cm_transactionExit($execution_no);

            // [処理]ステータスの更新 (ステータス: 想定外エラー 作業No.:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204100", array($execution_no));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    } catch (Exception $e){
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        require($root_dir_path . $log_output_php);
        return false;
    }
}

function InstanceExecution($execution_instance) {
    global $terraform_env;

    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $db_access_user_id;

    //----------------------------------------------
    // 変数・function定義
    //----------------------------------------------
    require($root_dir_path . $terraform_env);

    $execution_no = $execution_instance['EXECUTION_NO'];
    $workspace_id  = $execution_instance['I_TERRAFORM_WORKSPACE_ID'];
    $workspace_name = $execution_instance['I_TERRAFORM_WORKSPACE'];
    // var_dump($execution_no);

    try {
        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS, $db_access_user_id);

        // ステータスの更新に失敗しました。 (ステータス: 準備中 作業No.:{})
        $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208110",array($execution_no));
        //----------------------------------------------
        // トランザクション開始
        //----------------------------------------------
        $ret = cm_transactionStart($execution_no, $FREE_LOG);
        if($ret === false) {
            require($root_dir_path . $log_output_php);
            return false;
        }

        //----------------------------------------------
        // 処理対象の作業インスタンス情報取得
        //----------------------------------------------
        $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
        if($ret === false) {
            require($root_dir_path . $log_output_php);
            return false;
        }

        //----------------------------------------------
        // シーケンスをロックし履歴シーケンス採番
        //----------------------------------------------
        $dbobj->ClearLastErrorMsg();
        $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
        if($intJournalSeqNo === false) {
            require($root_dir_path . $log_output_php);
            return false;
        }
        // 未実行状態で緊急停止出来るようにしているので
        // 未実行状態かを判定
        if(($execution_row["STATUS_ID"] != 1) &&
           ($execution_row["STATUS_ID"] != 9)) {
            $FREE_LOG = "Emergency stop in unexecuted state.(execution_no: $execution_no)";
            require($root_dir_path . $log_output_php);
            return false;
        }

        //----------------------------------------------
        // 処理対象の作業インスタンスのステータスを準備中に設定
        //----------------------------------------------
        $execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;
        $execution_row["STATUS_ID"]        = $STATUS_PREPARE;
        $execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

        $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
        if($ret === false) {
            require($root_dir_path . $log_output_php);
            return false;
        }

        //----------------------------------------------
        // コミット(レコードロックを解除)
        //----------------------------------------------
        $ret = cm_transactionCommit($execution_no, $FREE_LOG);
        if($ret === false) {
            require($root_dir_path . $log_output_php);
            return false;
        }

        //----------------------------------------------
        // トランザクション終了
        //----------------------------------------------
        cm_transactionExit($execution_no);


        //----------------------------------------------
        // ワークスペース毎のディレクトリを準備
        //----------------------------------------------
        // 実行用のワークスペース毎のディレクトリの存在をチェックし、なければ作成
        $workspace_dir = $exec_base_dir . "/" . sprintf("%010s", $workspace_id);
        if (!file_exists($workspace_dir)) {
            if (!mkdir($workspace_dir, 0777, true)) {
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
            if (!chmod($workspace_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
        }
        $workspace_work_dir = $workspace_dir . "/" . $exec_base_work_dir;
        if (!file_exists($workspace_work_dir)) {
            if (!mkdir($workspace_work_dir, 0777, true)) {
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
            if (!chmod($workspace_work_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
        }
        //----------------------------------------------
        // （前回実行した）緊急停止ファイルを削除しておく
        //----------------------------------------------
        $emergency_stop_file_path = "{$workspace_work_dir}/emergency_stop";
        chdir($workspace_work_dir);
        $rm_list = [$emergency_stop_file_path];
        $cp_cmd = sprintf('/bin/rm -fr %s', implode(" ", $rm_list));
        system($cp_cmd);

        //----------------------------------------------
        // 子プロセスのコマンドを生成
        //----------------------------------------------
        $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");
        // 改行コードが付いている場合に取り除く
        $php_command = str_replace("\n","",$php_command);

        $child_process_name = sprintf("%010s-%010s", $workspace_id, $execution_no);
        // var_dump($child_process_name);
        $build_command = sprintf("%s %s %010s wsid=%010s %s > /dev/null &",
            $php_command,
            $root_dir_path."/libs/backyardlibs/terraform_cli_driver/ky_terraform_cli_execute-child-workflow.php",
            $execution_no,
            $workspace_id,
            $child_process_name);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // "[処理]処理対象インスタンス 実行プロセス起動開始(作業No.:{} workspace-name:{})"
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204180",array($execution_no, $workspace_name));
            require($root_dir_path . $log_output_php);
        }

        // プロセス起動 バックグラウンドで起動しているのでエラーは判定不可。エラー情報はログファイルにリダイレクト
        // var_dump($build_command);
        exec($build_command, $arry_out, $return_var);

        return true;
    } catch (Exception $e){
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );

        return false;
    }
}
?>
