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
//      Terraform 作業インスタンス実行
//      対象：ステータス「未実施」および「準備中」
//      処理：対象のインスタンスについて、TFEに作業登録およびplan/applyの実行。
//          　作業登録完了後、ステータスを「実行中」とする。
//
//
/////////////////////////////////////////////////////////////////////

// 作業Noを起動パラメータで受け取る
$execution_no_str = $argv[1]; // execution no 10桁
$execution_no = intval($execution_no_str); // execution no
$workspace_id_str = preg_replace('/^wsid=/', "", $argv[2]); // workspace_id 10桁
$workspace_id = intval($workspace_id_str); // workspace_id
$execution_info = $argv[3];  // workspace_id 10桁:execution no 10桁
// var_dump("execution_no=".$execution_no);
// var_dump("workspace_id=".$workspace_id);
// var_dump($execution_info);

/////////////////////////////
// ルートディレクトリを取得
/////////////////////////////
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
////////////////////////////////
// $log_output_dirを取得
////////////////////////////////
$log_output_dir = getenv('LOG_DIR');

////////////////////////////////
// $log_file_prefixを作成
////////////////////////////////
$log_file_prefix = basename( __FILE__, '.php' ) . "_";

////////////////////////////////
// $log_levelを取得
////////////////////////////////
$log_level = getenv('LOG_LEVEL');

////////////////////////////////
// 作業状態確認インターバル   //
////////////////////////////////
$interval  = getenv('INTERVAL');
if($interval === false) {
    $interval = 3;
}

// PHP エラー時のログ出力先を設定
$tmpVarTimeStamp = time();
$logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', $logfile);

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
$error_flag                     = 0;        // 異常フラグ(想定外エラー)
$emergency_flg                  = 0;        // 緊急停止フラグ
$db_access_user_id              = -101902;  // Terraform-CLI作業実行プロシージャ

$workspace_work_dir = ""; // CLI実行場所
$exe_lock_file_path = ""; // ロックファイル
$resut_file_path = ""; // 実行内容を記録したファイル
$default_tfvars_file_path = ""; // terraform.tfvars
$secure_tfvars_flg = false; // secure.tfvarsファイルの作成要件
$secure_tfvars_file_path = ""; // secure.tfvars
$emergency_stop_file_path = ""; // 緊急停止ファイル

//----------------------------------------------
// function定義
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
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204190", $execution_no);
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

    // //----------------------------------------------
    // // logファイルを生成
    // //----------------------------------------------
    // $data_type = "out";
    // $log_path = $log_save_dir . "/" . $execution_no_str . "/" . $data_type;
    // $error_log = $log_path . "/error.log";
    // $init_log = $log_path . "/init.log";
    // $plan_log = $log_path . "/plan.log";
    // $apply_log = $log_path . "/apply.log";

    //----------------------------------------------
    // ディレクトリを準備
    //----------------------------------------------
    $workspace_dir = $exec_base_dir . "/" . $workspace_id_str;
    $workspace_work_dir = $workspace_dir . $exec_base_work_dir; // CLI実行場所

    $exe_lock_file_path = "{$workspace_work_dir}/.tf_exec_lock"; // ロックファイル
    $resut_file_path = "{$workspace_work_dir}/result.txt"; // 実行内容を記録したファイル

    $default_tfvars_file_path = "{$workspace_work_dir}/terraform.tfvars"; // terraform.tfvars
    $secure_tfvars_file_path = "{$workspace_work_dir}/secure.tfvars"; // secure.tfvars
    $emergency_stop_file_path = "{$workspace_work_dir}/emergency_stop"; // 緊急停止ファイル

    while(true){
        sleep($interval);

        $cln_execution_row        = array();    // 作業インスタンス更新用配列初期化
        $execution_row        = array();    // 作業インスタンス更新用配列初期化
        /////////////////////////////////////////////////////////////////
        // 処理対象の作業インスタンス情報取得
        /////////////////////////////////////////////////////////////////
        $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no,$vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
        if($ret === false) {
            $error_flag = 1;
            throw new Exception($FREE_LOG);
        }
        // 更新用にクローン作製
        $cln_execution_row = $execution_row;

        /////////////////////////////////////////////////////////////////
        // 終了していたら抜ける
        // 完了,完了(異常),想定外エラー,緊急停止
        /////////////////////////////////////////////////////////////////
        $finish_status_list = [$STATUS_COMPLETE, $STATUS_FAILURE, $STATUS_EXCEPTION, $STATUS_SCRAM];
        if( in_array($execution_row['STATUS_ID'], $finish_status_list) === true ){
            break;
        }

        // 遅延タイマを取得
        $time_limit = $execution_row['I_TIME_LIMIT'];
        $delay_flag = 0;

        // ステータスが実行中(3)、かつ制限時間が設定されている場合のみ遅延判定する
        if( $execution_row['STATUS_ID'] == $STATUS_PROCESSING && $time_limit != "" ){
            // 開始時刻(「UNIXタイム.マイクロ秒」)を生成
            $varTimeDotMirco = convFromStrDateToUnixtime($execution_row['TIME_START'], true);
            // 開始時刻(マイクロ秒)＋制限時間(分→秒)＝制限時刻(マイクロ秒)
            $varTimeDotMirco_limit = $varTimeDotMirco + ($time_limit * 60); //単位（秒）

            // 現在時刻(「UNIXタイム.マイクロ秒」)を生成
            $varTimeDotNowStd = getMircotime(0);

            // 制限時刻と現在時刻を比較
            if( $varTimeDotMirco_limit < $varTimeDotNowStd ){
                // 遅延
                $delay_flag = 1;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // "[処理]遅延を検出しました。(作業No.:{})";
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204230", $execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }
        }

        if( $delay_flag == 0 ){
            continue;
        }

        // 遅延が発生の場合
        $cln_execution_row['STATUS_ID'] = $STATUS_PROCESS_DELAYED;
        $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

        //----------------------------------------------
        // ステータスを実行（遅延）に更新
        //----------------------------------------------
        // ステータスを更新出来ませんでした。(作業No.:{} ステータス:{})
        $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208270", array($execution_no, $STATUS_PROCESS_DELAYED));
        //----------------------------------------------
        // トランザクション開始
        //----------------------------------------------
        $ret = cm_transactionStart($execution_no, $FREE_LOG);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($ErrorMsg);
        }
        //----------------------------------------------
        // シーケンスをロックし履歴シーケンス採番
        //----------------------------------------------
        $dbobj->ClearLastErrorMsg();
        $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
        if($intJournalSeqNo === false) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($ErrorMsg);
        }
        //----------------------------------------------
        // 処理対象の作業インスタンスのステータスを最終更新
        //----------------------------------------------
        $cln_execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;

        $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($ErrorMsg);
        }
        //----------------------------------------------
        // コミット(レコードロックを解除)
        //----------------------------------------------
        $ret = cm_transactionCommit($execution_no, $FREE_LOG);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($ErrorMsg);
        }
        //----------------------------------------------
        // トランザクション終了
        //----------------------------------------------
        cm_transactionExit($execution_no);

        break;
    }
} catch (Exception $e) {
    // メッセージ出力
    if($emergency_flg == 0 && $error_flag == 0) {
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
        // [処理]プロシージャ終了(異常) (作業No.:{})
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204210", array($execution_no));
        require($root_dir_path . $log_output_php);
    }

    exit(2);
} else {
    // 終了メッセージ
    if ($log_level === 'DEBUG') {
        // [処理]プロシージャ終了(正常) (作業No.:{})
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204200", array($execution_no));
        require($root_dir_path . $log_output_php);
    }

    exit(0);
}

?>
