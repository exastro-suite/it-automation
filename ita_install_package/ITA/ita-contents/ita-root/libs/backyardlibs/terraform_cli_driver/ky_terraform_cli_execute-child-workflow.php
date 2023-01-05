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
// $interval  = getenv('INTERVAL');
// if($interval === false) {
//     $interval = 3;
// }

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

$ary_module_matter_id           = []; // モジュール素材IDを格納する配列
$ary_module_matter              = []; // モジュール素材情報を格納する配列
$ary_input_matter               = []; // 投入ファイル情報を格納する配列
$input_zip_file_name            = "";
$ary_result_matter              = []; // 結果ファイル情報を格納する配列
$result_zip_file_name           = "";
$vars_set_flag                  = false; //変数追加処理を行うかの判定
$ary_vars_data                  = []; //対象の変数を格納する配列

$variable_tfvars                = []; // terraform.tfvarsに書き込むkey=value
$secure_tfvars                  = []; // secure.tfvarsに書き込むkey=value

$workspace_work_dir = ""; // CLI実行場所
$exe_lock_file_path = ""; // ロックファイル
$resut_file_path = ""; // 実行内容を記録したファイル
$default_tfvars_file_path = ""; // terraform.tfvars
$secure_tfvars_flg = false; // secure.tfvarsファイルの作成要件
$secure_tfvars_file_path = ""; // secure.tfvars
$emergency_stop_file_path = ""; // 緊急停止ファイル

$in_zip_file_name = ""; // 投入ファイル
$result_zip_file_name = ""; // 結果ファイル


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

    //----------------------------------------------
    // 処理対象の作業インスタンス情報取得
    //----------------------------------------------
    $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $execution_row, $FREE_LOG);
    if( $ret === false ) {
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception($FREE_LOG);
    }
    // var_dump($execution_row);
    // 更新用にクローン作製
    $cln_execution_row = $execution_row;
    $cln_execution_row['TIME_START'] = "DATETIMEAUTO(6)";
    $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

    $workspace_id  = $execution_row['I_TERRAFORM_WORKSPACE_ID'];
    $workspace_name_org = $execution_row['I_TERRAFORM_WORKSPACE'];

    //operation_noを定義
    $operation_no = $execution_row['OPERATION_NO_UAPK'];
    //pattern_idを定義
    $pattern_id = $execution_row['PATTERN_ID'];
    //RUN_MODEを定義
    // 1:apply 2:plan 3:destroy
    $run_mode = $execution_row['RUN_MODE'];
    // var_dump($run_mode);

    // トレースメッセージ
    if ($log_level === 'DEBUG') {
        // 作業インスタンス情報を取得しました。(作業No.:{})
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204220", $execution_no);
        require($root_dir_path . $log_output_php);
    }

    //----------------------------------------------
    // logファイルを生成
    //----------------------------------------------
    $data_type = "out";
    $log_path = $log_save_dir . "/" . $execution_no_str . "/" . $data_type;
    $error_log = $log_path . "/error.log";
    $init_log = $log_path . "/init.log";
    $plan_log = $log_path . "/plan.log";
    $apply_log = $log_path . "/apply.log";

    //log格納ディレクトリを作成
    if (!file_exists($log_path)) {
        if (!mkdir($log_path, 0777, true)) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
        if (!chmod($log_path, 0777)) {// mkdirのpermissisonは失敗するケースがある
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
    }

    //----------------------------------------------
    // ディレクトリを準備
    //----------------------------------------------
    // 実行用のワークスペースのディレクトリの存在をチェック → なければエラー
    $workspace_dir = $exec_base_dir . "/" . $workspace_id_str;
    $workspace_work_dir = $workspace_dir . $exec_base_work_dir; // CLI実行場所

    $exe_lock_file_path = "{$workspace_work_dir}/.tf_exec_lock"; // ロックファイル
    $resut_file_path = "{$workspace_work_dir}/result.txt"; // 実行内容を記録したファイル

    $default_tfvars_file_path = "{$workspace_work_dir}/terraform.tfvars"; // terraform.tfvars
    $secure_tfvars_file_path = "{$workspace_work_dir}/secure.tfvars"; // secure.tfvars
    $emergency_stop_file_path = "{$workspace_work_dir}/emergency_stop"; // 緊急停止ファイル

    // var_dump($workspace_work_dir);
    if(file_exists($workspace_work_dir) === false) {
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }

    //----------------------------------------------
    // 前回の実行ファイルの削除
    // destroy以外・・・tfファイルなど、stateファイル以外の全てを削除
    // destroy・・・・・結果ファイル・ロックファイルのみを削除（前回実行状態にする）
    //----------------------------------------------
    chdir($workspace_work_dir);
    // ロックファイル, 実行結果ファイル
    $rm_list = [$exe_lock_file_path, $resut_file_path, $emergency_stop_file_path];
    if ($run_mode != $RUN_MODE_DESTROY) {
        $cp_cmd = sprintf('/bin/rm -fr *.tf *.tfvars %s', implode(" ", $rm_list));
    }else{
        $cp_cmd = sprintf('/bin/rm -fr %s', implode(" ", $rm_list));
    }
    system($cp_cmd);

    // 緊急停止のチェック
    $ret = IsEmergencyStop();
    if($ret == false) {
        throw new Exception($FREE_LOG);
    }

    //----------------------------------------------
    // WORKSPACE_IDから対象Workspace(B_TERRAFORM_CLI_WORKSPACES)のレコードを取得
    //----------------------------------------------
    $sql = "SELECT * "
        . "FROM   {$vg_terraform_workspaces_table_name} "
        . "WHERE  DISUSE_FLAG = '0' "
        . "AND    WORKSPACE_ID = {$workspace_id} ";
    // SQL準備
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }

    // SQL発行
    $r = $objQuery->sqlExecute();
    if (!$r) {
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    // fetch行数を取得
    $fetch_counter = $objQuery->effectedRowCount();
    if ($fetch_counter < 1) {
        $error_flag = 1;
        //error_logにメッセージを追記
        $message = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__));
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        LocalLogPrint($error_log, $message);

        throw new Exception($message);
    }

    // レコードFETCH
    while ($row = $objQuery->resultFetch()) {
        //最新のworkspace name
        $workspace_name = $row['WORKSPACE_NAME'];
    }
    // DBアクセス事後処理
    unset($objQuery);

    if ($run_mode != $RUN_MODE_DESTROY) {
        //----------------------------------------------
        // PATTERN_IDからMovement詳細(B_TERRAFORM_CLI_PATTERN_LINK)のレコードを取得
        //----------------------------------------------
        $sql = "SELECT * "
            . "FROM   {$vg_terraform_pattern_link_table_name} "
            . "WHERE  DISUSE_FLAG = '0' "
            . "AND    PATTERN_ID = {$pattern_id} ";
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1) {
            //error_logにメッセージを追記
            $message = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208230", $pattern_id);
            LocalLogPrint($error_log, $message);

            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($message);
        }

        // レコードFETCH
        while ($row = $objQuery->resultFetch()) {
            //module素材のIDを配列に格納
            array_push($ary_module_matter_id, $row['MODULE_MATTER_ID']);
        }
        // DBアクセス事後処理
        unset($objQuery);

        //----------------------------------------------
        // 投入オペレーションの最終実施日を更新する。
        //----------------------------------------------
        require_once($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
        $dbaobj = new BackyardCommonDBAccessClass($db_model_ch, $objDBCA, $objMTS, $db_access_user_id);
        $ret = $dbaobj->OperationList_LastExecuteTimestamp_Update($operation_no);
        if ($ret === false) {
            $FREE_LOG = $dbaobj->GetLastErrorMsg();
            require($log_output_php);
            throw new Exception("OperationList update error.");
        }
        unset($dbaobj);

        //----------------------------------------------
        // Moduleのファイル名を取得
        //----------------------------------------------
        $module_matter_id_implode = implode(',', $ary_module_matter_id);
        $sql = "SELECT * "
            . "FROM   {$vg_terraform_module_table_name} "
            . "WHERE  DISUSE_FLAG = '0' "
            . "AND    MODULE_MATTER_ID in ({$module_matter_id_implode}) ";

        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter > 0) {
            // 1件以上ある場合、レコードFETCH
            while ($row = $objQuery->resultFetch()) {
                $ary_module_matter[$row['MODULE_MATTER_ID']] = array(
                    'matter_name' => $row['MODULE_MATTER_NAME'],
                    'matter_file_name' => $row['MODULE_MATTER_FILE']
                );
            }
        } else {
            // 警告フラグON
            $error_flag = 1;

            //error_logにメッセージを追記
            //Movementに紐づくModuleが存在しません(MovementID:{})
            $message = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208240", $pattern_id);
            LocalLogPrint($error_log, $message);
            // 例外処理へ
            throw new Exception($message);
        }
        // var_dump($ary_module_matter);

        // DBアクセス事後処理
        unset($objQuery);

        //----------------------------------------------
        // 作業実行ディレクトリに、対象のModuleファイルをコピー
        //----------------------------------------------
        foreach ($ary_module_matter as $matter_id => $matter) {
            $tgt_matter_no_str_pad = sprintf("%010s", $matter_id);
            $matter_file_name = $matter['matter_file_name'];
            $cp_cmd = sprintf('/bin/cp -rfp %s %s/.', $vg_terraform_module_contents_dir . '/' . $tgt_matter_no_str_pad . '/"' . $matter_file_name . '"', $workspace_work_dir);
            system($cp_cmd);

            array_push($ary_input_matter, $workspace_work_dir . '/"' . $matter_file_name . '"');
        }

        //----------------------------------------------
        // operation_noとpattern_idから変数名と代入値を取得
        //----------------------------------------------
        $sql = "SELECT "
            . "{$vg_terraform_vars_data_view_name}.MODULE_VARS_LINK_ID, "
            . "{$vg_terraform_vars_data_view_name}.VARS_NAME, "
            . "{$vg_terraform_vars_data_view_name}.HCL_FLAG, "
            . "{$vg_terraform_vars_data_view_name}.SENSITIVE_FLAG, "
            . "{$vg_terraform_vars_data_view_name}.VARS_ENTRY, "
            . "{$vg_terraform_vars_data_view_name}.MEMBER_VARS, "
            . "{$vg_terraform_vars_data_view_name}.ASSIGN_SEQ, "
            . "{$vg_terraform_module_vars_link_table_name}.TYPE_ID, "
            . "{$vg_terraform_var_member_view_name}.VARS_ASSIGN_FLAG "
            . "FROM   {$vg_terraform_vars_data_view_name} "
            . "LEFT OUTER JOIN {$vg_terraform_module_vars_link_table_name} "
            . "ON {$vg_terraform_vars_data_view_name}.MODULE_VARS_LINK_ID = {$vg_terraform_module_vars_link_table_name}.MODULE_VARS_LINK_ID "
            . "LEFT OUTER JOIN {$vg_terraform_var_member_view_name} "
            . "ON {$vg_terraform_vars_data_view_name}.MEMBER_VARS = {$vg_terraform_var_member_view_name}.CHILD_MEMBER_VARS_ID "
            . "WHERE  {$vg_terraform_vars_data_view_name}.DISUSE_FLAG = '0' "
            . "AND    {$vg_terraform_module_vars_link_table_name}.DISUSE_FLAG = '0' "
            . "AND    {$vg_terraform_vars_data_view_name}.OPERATION_NO_UAPK = {$operation_no} "
            . "AND    {$vg_terraform_vars_data_view_name}.PATTERN_ID = {$pattern_id} ";

        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter > 0) {
            $vars_array = [];
            $member_vars_link_id_list = [];
            // 1件以上ある場合、レコードFETCH
            while ($row = $objQuery->resultFetch()) {
                if (isset($row["MEMBER_VARS"]) && $row["MEMBER_VARS"] != NULL) {
                    $member_vars_link_id_list[] = $row;
                } else {
                    $vars_array[] = $row;
                }
            }

            if (!empty($vars_array)) {
                foreach ($vars_array as $vars) {
                    //VARS_LINK_ID(key)/VARS_NAME/ASSIGN_SEQ/MEMBER_VARS/VARS_ENTRY/TYPE_IDを配列に格納
                    $vars_link_id     = $vars['MODULE_VARS_LINK_ID'];
                    $vars_name        = $vars['VARS_NAME'];
                    $vars_entry       = $vars['VARS_ENTRY'];
                    $vars_assign_seq  = $vars['ASSIGN_SEQ'];
                    $vars_type_id     = $vars['TYPE_ID'];
                    $vars_list        = [];

                    //HCL設定を判定
                    $hcl_flag = $vars['HCL_FLAG'];
                    $hcl_boolean = false;
                    if ($hcl_flag == 1) {
                        $hcl_boolean = false; //1(OFF)ならfalse
                    } elseif ($hcl_flag == 2) {
                        $hcl_boolean = true; //2(ON)ならtrue
                    }

                    //Sensitive設定を判定
                    $sensitive_flag = $vars['SENSITIVE_FLAG'];
                    $sensitive_boolean = false;
                    if ($sensitive_flag == 1) {
                        $sensitive_boolean = false; //1(OFF)ならfalse
                    } elseif ($sensitive_flag == 2) {
                        $sensitive_boolean = true; //2(ON)ならtrue
                        $vars_entry = ky_decrypt($vars_entry); //具体値をデコード
                    }

                    if (isset($ary_vars_data[$vars_link_id])) {
                        $ary_vars_data[$vars_link_id]['VARS_LIST'][intval($vars_assign_seq)] = $vars_entry;
                    } else {
                        $ary_vars_data[$vars_link_id] = ['VARS_NAME' => $vars_name, 'VARS_ENTRY' => $vars_entry, 'ASSIGN_SEQ' => $vars_assign_seq, 'MEMBER_VARS' => [], 'HCL_FLAG' => $hcl_boolean, 'SENSITIVE_FLAG' => $sensitive_boolean, "VARS_TYPE_ID" => $vars_type_id];
                        $ary_vars_data[$vars_link_id]['VARS_LIST'][intval($vars_assign_seq)] = $vars_entry;
                    }
                }
            }

            if (!empty($member_vars_link_id_list)) {
                foreach ($member_vars_link_id_list as $vars) {
                    //VARS_LINK_ID(key)/VARS_NAME/ASSIGN_SEQ/MEMBER_VARS/VARS_ENTRYを配列に格納
                    $vars_link_id     = $vars['MODULE_VARS_LINK_ID'];
                    $vars_name        = $vars['VARS_NAME'];
                    $vars_entry       = $vars['VARS_ENTRY'];
                    $vars_assign_seq  = $vars['ASSIGN_SEQ'];
                    $vars_type_id     = $vars['TYPE_ID'];
                    $vars_type_info   = getTypeInfo($vars_type_id);
                    $vars_member_vars = $vars['MEMBER_VARS'];
                    $vars_assign_flag = $vars["VARS_ASSIGN_FLAG"]; // 代入値系管理フラグ

                    //HCL設定を判定
                    $hcl_boolean = false;

                    //Sensitive設定を判定
                    $sensitive_flag = $vars['SENSITIVE_FLAG'];
                    $sensitive_boolean = false;
                    if ($sensitive_flag == 1) {
                        $sensitive_boolean = false; //1(OFF)ならfalse
                    } elseif ($sensitive_flag == 2) {
                        $sensitive_boolean = true; //2(ON)ならtrue
                        $vars_entry = ky_decrypt($vars_entry); //具体値をデコード
                    }

                    if (isset($ary_vars_data[$vars_link_id])) {
                        // メンバー変数を取らない配列のタイプ
                        $ary_vars_data[$vars_link_id]['MEMBER_VARS_LIST'][] = ["MEMBER_VARS" => $vars_member_vars, "SENSITIVE_FLAG" => $sensitive_flag, "VARS_ENTRY" => $vars_entry, "ASSIGN_SEQ" =>$vars_assign_seq, "VARS_ASSIGN_FLAG" => $vars_assign_flag];
                    } else {
                        // メンバー変数を取らない配列のタイプ
                        $ary_vars_data[$vars_link_id] = ['VARS_NAME' => $vars_name, 'VARS_ENTRY' => $vars_entry, 'ASSIGN_SEQ' => $vars_assign_seq, 'MEMBER_VARS' => [], 'HCL_FLAG' => $hcl_boolean, 'SENSITIVE_FLAG' => $sensitive_boolean, "VARS_TYPE_ID" => $vars_type_id];
                        $ary_vars_data[$vars_link_id]['MEMBER_VARS_LIST'][] = ["MEMBER_VARS" => $vars_member_vars, "SENSITIVE_FLAG" => $sensitive_flag, "VARS_ENTRY" => $vars_entry, "ASSIGN_SEQ" => $vars_assign_seq, "VARS_ASSIGN_FLAG" => $vars_assign_flag];
                    }
                }
            }

            //変数追加処理のフラグをtrueにする
            $vars_set_flag = true;
        }

        // DBアクセス事後処理
        unset($objQuery);

        //--------------------------------------------------------------
        // Movementに紐づく代入値がある場合、代入値(Variables)登録処理を実行
        //--------------------------------------------------------------
        if ($vars_set_flag == true) {
            foreach ($ary_vars_data as $vars_link_id => $data) {
                $var_key          = $data['VARS_NAME'];
                $var_value        = $data['VARS_ENTRY'];
                $assign_seq       = $data['ASSIGN_SEQ'];
                $vars_list        = [];
                $member_vars_list = [];
                $hclFlag          = $data['HCL_FLAG'];
                $sensitiveFlag    = $data['SENSITIVE_FLAG'];
                $varsTypeID       = $data['VARS_TYPE_ID'];
                $varsTypeInfo     = getTypeInfo($varsTypeID);
                $category         = 'terraform';
                if (isset($data['VARS_LIST'])) {
                    $vars_list    = $data['VARS_LIST'];
                }
                if (isset($data['MEMBER_VARS_LIST'])) {
                    $member_vars_list = $data['MEMBER_VARS_LIST'];
                }

                // HCL組み立て
                /*------------------------------
                * 1.Module変数紐付けのタイプが配列型でない場合
                * 2.Module変数紐付けのタイプが配列型且つメンバー変数がない場合
                * 3.Module変数紐付けのタイプが配列型且つメンバー変数である場合
                ---------------------------------*/
                // 1.Module変数紐付けのタイプが配列型でない場合
                if ($hclFlag == true || $varsTypeInfo["MEMBER_VARS_FLAG"] == 0 && $varsTypeInfo["ASSIGN_SEQ_FLAG"] == 0 && $varsTypeInfo["ENCODE_FLAG"] == 0) {
                }
                // 2.Module変数紐付けのタイプが配列型且つメンバー変数がない場合
                elseif($varsTypeInfo["MEMBER_VARS_FLAG"] == 0 && $varsTypeInfo["ASSIGN_SEQ_FLAG"] == 1 && $varsTypeInfo["ENCODE_FLAG"] == 1) {
                    // HCL組み立て(メンバー変数)
                    if (count($vars_list) > 0) {
                        // HCLに変換
                        asort($vars_list);
                        $temp_ary = [];
                        foreach($vars_list as $vars_data) {
                            $temp_ary[] = $vars_data;
                        }
                        $var_value = encodeHCL($temp_ary);
                    }
                    $hclFlag = true;
                }
                // 3.Module変数紐付けのタイプが配列型且つメンバー変数である場合
                else {
                    // HCL組み立て(メンバー変数)
                    if (count($member_vars_list) > 0 && $hclFlag == false) {
                        $temp_member_vars_list = [];
                        // １．対象変数のメンバー変数を全て取得（引数：Module変数紐付け/MODULE_VARS_LINK_ID）
                        $trgMemberVarsRecords = getMemberVarsByModuleVarsLinkIDForHCL($vars_link_id);
                        // 重複を削除
                        $member_ids_array = array_unique(array_column($member_vars_list, "MEMBER_VARS"));
                        // ２．配列型の変数を配列にする
                        foreach ($member_ids_array as $member_idx => $member_id) {
                            // メンバー変数IDからタイプ情報を取得する
                            $key = array_search($member_id, array_column($trgMemberVarsRecords, "CHILD_MEMBER_VARS_ID"));
                            $typeInfo = getTypeInfo($trgMemberVarsRecords[$key]["CHILD_VARS_TYPE_ID"]);
                            // メンバー変数対象でない配列型のみ配列型に形成する
                            if ($typeInfo["MEMBER_VARS_FLAG"] == 0 && $typeInfo["ASSIGN_SEQ_FLAG"] == 1 && $typeInfo["ENCODE_FLAG"] == 1) {
                                $temp_ary  = [];
                                $i = 0;
                                // 代入順序をキーインデックスにして具体値をtemp_aryに収める
                                foreach ($member_vars_list as $member_vars_data) {
                                    if ($member_id == $member_vars_data["MEMBER_VARS"]) {
                                        $temp_ary[$member_vars_data["ASSIGN_SEQ"]] = $member_vars_data["VARS_ENTRY"];
                                    }
                                }
                                // 降順に並べ替え
                                asort($temp_ary);
                                $sensitive_flag = false;
                                if (isset($trgMemberVarsRecords[$key]["SENSITIVE_FLAG"])) {
                                    $sensitive_flag = $trgMemberVarsRecords[$key]["SENSITIVE_FLAG"];
                                }

                                $temp_member_vars_list[] = [
                                    "MEMBER_VARS"      => $member_id,
                                    "SENSITIVE_FLAG"   => $sensitive_flag,
                                    "VARS_ENTRY"       => array_values($temp_ary),
                                    "VARS_ASSIGN_FLAG" => $trgMemberVarsRecords[$key]["VARS_ASSIGN_FLAG"],
                                ];
                            }
                            else {
                                $sensitive_flag = false;
                                if (isset($trgMemberVarsRecords[$key]["SENSITIVE_FLAG"])) {
                                    $sensitive_flag = $trgMemberVarsRecords[$key]["SENSITIVE_FLAG"];
                                }

                                $key = array_search($member_id, array_column($member_vars_list, "MEMBER_VARS"));
                                // 配列型でない場合、何もしない
                                $temp_member_vars_list[] = [
                                    "MEMBER_VARS"      => $member_id,
                                    "SENSITIVE_FLAG"   => $member_vars_list[$key]["SENSITIVE_FLAG"],
                                    "VARS_ENTRY"       => $member_vars_list[$key]["VARS_ENTRY"],
                                    "VARS_ASSIGN_FLAG" => $member_vars_list[$key]["VARS_ASSIGN_FLAG"],
                                ];
                            }
                        }

                        // MEMBER_VARS_LISTの中身を入れ替える
                        $member_vars_list = $temp_member_vars_list;

                        // ３．代入値管理で取得した値を置き換え
                        foreach ($member_vars_list as $member_vars) {
                            foreach ($trgMemberVarsRecords as &$trgMemberVarsRecord) {
                                if ($member_vars["MEMBER_VARS"] == $trgMemberVarsRecord["CHILD_MEMBER_VARS_ID"]) {
                                    $trgMemberVarsRecord["CHILD_MEMBER_VARS_VALUE"] = $member_vars["VARS_ENTRY"];
                                    $trgMemberVarsRecord["VARS_ENTRY_FLAG"]  = 1;
                                    $trgMemberVarsRecord["VARS_ASSIGN_FLAG"] = $member_vars["VARS_ASSIGN_FLAG"];
                                }
                            }
                            unset($trgMemberVarsRecord);
                            // sensitive設定をチェック
                            // 対象代入値に一つでもsensitive設定があればseneitiveはON
                            if ($sensitiveFlag == false && $member_vars["SENSITIVE_FLAG"] == 2) {
                                $sensitiveFlag = true;
                            }
                        }

                        // ４．置換する値がなかった場合、エラーとする
                        $err_id_list = [];
                        foreach ($trgMemberVarsRecords as $trgMemberVarsRecord) {
                            if($trgMemberVarsRecord["VARS_ENTRY_FLAG"] == 0 && $trgMemberVarsRecord["VARS_ASSIGN_FLAG"] == 1) {
                                $err_id_list[] = $trgMemberVarsRecord["CHILD_MEMBER_VARS_ID"];
                            }
                        }
                        if (!empty($err_id_list)) {
                            $ids_string = json_encode($err_id_list);
                            // error_logにメッセージを追記
                            // メンバー変数の取得に失敗しました。ID:[]
                            $message = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208250", array($ids_string));
                            LocalLogPrint($error_log, $message);

                            // 異常フラグON
                            $error_flag = 1;
                            // メンバー変数の取得に失敗しました。(FILE:{} LINE:{} ID:{})
                            $backyard_log = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208251", array(__FILE__, __LINE__, $ids_string));
                            throw new Exception($backyard_log);
                        }

                        // ５．取得したデータから配列を形成
                        $trgMemberVarsArray = generateMemberVarsArrayForHCL($trgMemberVarsRecords);

                        // ６．HCLに変換
                        $var_value = encodeHCL($trgMemberVarsArray);
                        $hclFlag = true;
                    }
                }

                // 変数エラーキャッチ(ID変換失敗時)
                if ($var_key == NULL) {
                    // error_logにメッセージを追記
                    // 変数名の取得に失敗しました。
                    $message = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208260");
                    LocalLogPrint($error_log, $message);

                    // 異常フラグON
                    $error_flag = 1;
                    // 変数の取得に失敗しました。(FILE:{} LINE:{})
                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208261", array(__FILE__, __LINE__));
                    throw new Exception($backyard_log);
                }

                if ( !$sensitiveFlag ) {
                    $variable_tfvars[] = makeKVStrRow($var_key, $var_value, $varsTypeID);
                } else {
                    $secure_tfvars[] = makeKVStrRow($var_key, $var_value, $varsTypeID);
                }
            }
            if( count($variable_tfvars) > 0 ){
                array_push($ary_input_matter, $default_tfvars_file_path);
                $str_variable_tfvars = implode("\n", $variable_tfvars);
                file_put_contents($default_tfvars_file_path, $str_variable_tfvars, FILE_APPEND | LOCK_EX);
            }
            if( count($secure_tfvars) > 0 ){
                array_push($ary_input_matter, $secure_tfvars_file_path);
                $str_secure_tfvars = implode("\n", $secure_tfvars);
                file_put_contents($secure_tfvars_file_path, $str_secure_tfvars, FILE_APPEND | LOCK_EX);
                $secure_tfvars_flg = true;
            }
        }
    }else{
        array_push($ary_input_matter, $workspace_work_dir . '/*.tf');
        array_push($ary_input_matter, $default_tfvars_file_path);
        array_push($ary_input_matter, $secure_tfvars_file_path);
    }

    //----------------------------------------------
    // 投入ファイル:ZIPファイルを作成する(ITAダウンロード用)
    //----------------------------------------------
    MakeInputZipFile();

    //----------------------------------------------
    // ステータスを実行中に更新
    //----------------------------------------------
    $cln_execution_row['STATUS_ID'] = $STATUS_PROCESSING;
    $cln_execution_row["FILE_INPUT"] = $input_zip_file_name;

    // 履歴シーケンス採番
    $dbobj->ClearLastErrorMsg();
    $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
    if($intJournalSeqNo === false) {
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception($FREE_LOG);
    }
    $cln_execution_row['JOURNAL_SEQ_NO'] = $intJournalSeqNo;
    // ステータス更新
    $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
    if($ret === false) {
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception($FREE_LOG);
    }

    // 緊急停止のチェック
    $ret = IsEmergencyStop();
    if($ret == false) {
        throw new Exception($FREE_LOG);
    }

    //----------------------------------------------
    // terraformコマンドの実行
    //----------------------------------------------
    // ファイルによる実行中の排他ロック
    // エラー時のロック解除はしない。プロセス終了でロック解除
    // terraformコマンド～result.txt作成までのロックファイル
    $exec_lock = fopen($exe_lock_file_path, "w");
    if($exec_lock === false){
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    flock($exec_lock, LOCK_EX, $wouldblock);

    // コマンドを実行する位置に移動
    chdir($workspace_work_dir);
    // 更新するステータスの初期値
    $update_status = $STATUS_EXCEPTION;

    //----------------------------------------------
    // initの実行
    //----------------------------------------------
    $command = "terraform init";
    $ret = ExecCommand($command, $init_log, true, $FREE_LOG);
    if( $ret !== $STATUS_COMPLETE ){
        // 異常フラグON
        $error_flag = 1;
        $update_status = $ret;
    }
    array_push($ary_result_matter, $error_log);
    array_push($ary_result_matter, $workspace_work_dir . '/.terraform.lock.hcl');
    array_push($ary_result_matter, $init_log);

    if ($error_flag == 0) {
        // 緊急停止のチェック
        $ret = IsEmergencyStop();
        if($ret == false) {
            fclose($exec_lock);
            throw new Exception($FREE_LOG);
        }

        //----------------------------------------------
        // planの実行
        //----------------------------------------------
        if ($run_mode != $RUN_MODE_DESTROY) {
            $command = "terraform plan";
        } else {
            $command = "terraform plan -destroy";
        }
        $ret = ExecCommand($command, $plan_log, false, $FREE_LOG);
        if( $ret !== $STATUS_COMPLETE ){
            // 異常フラグON
            $error_flag = 1;
            $update_status = $ret;
        }
        array_push($ary_result_matter, $plan_log);
    }

    if ($error_flag == 0) {
        // 緊急停止のチェック
        $ret = IsEmergencyStop();
        if($ret == false) {
            fclose($exec_lock);
            throw new Exception($FREE_LOG);
        }

        //----------------------------------------------
        // applyの実行
        //----------------------------------------------
        if ($run_mode == $RUN_MODE_APPLY) {
            $command_options = [];
            if($secure_tfvars_flg === true){
                array_push($command_options, "-var-file secure.tfvars");
            }
            $command = "terraform apply -auto-approve ".implode(" ", $command_options);
            $ret = ExecCommand($command, $apply_log, false, $FREE_LOG);
        //----------------------------------------------
        // destroyの実行
        //----------------------------------------------
        } elseif ($run_mode == $RUN_MODE_DESTROY){
            $command = "terraform destroy -auto-approve";
            $ret = ExecCommand($command, $apply_log, false, $FREE_LOG);
        }

        if( $ret !== $STATUS_COMPLETE ){
            // 異常フラグON
            $error_flag = 1;
            $update_status = $ret;
        }else{
            $update_status = $STATUS_COMPLETE;
            array_push($ary_result_matter, $apply_log);
        }
        //stateファイルの暗号化
        SaveEncryptStateFile($FREE_LOG);
    }

    // ファイルによる排他ロック解除
    fclose($exec_lock);

    // //----------------------------------------------
    // // secure.tfvarsの削除
    // //----------------------------------------------
    // chdir($workspace_work_dir);
    // // secure.tfvars
    // $rm_list = [$secure_tfvars_file_path];
    // $cp_cmd = sprintf('/bin/rm -fr %s', implode(" ", $rm_list));
    // system($cp_cmd);

    //----------------------------------------------
    // 結果ファイルの格納:ZIPファイルを作成する(ITAダウンロード用)
    //----------------------------------------------
    array_push($ary_result_matter, $resut_file_path);
    MakeResultZipFile();

    //----------------------------------------------
    // Conductorからの実行時、output出力結果を格納する。
    //----------------------------------------------
    $conductor_instance_no  = $execution_row['CONDUCTOR_INSTANCE_NO'];
    if(!empty($conductor_instance_no)){
        // データリレイストレージパスの取得
        //SQL作成
        $sql = "SELECT CONDUCTOR_STORAGE_PATH_ITA
                FROM   C_CONDUCTOR_IF_INFO
                WHERE  DISUSE_FLAG = '0'";

        //SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        //SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }

        //呼び出し元ConductorのインスタンスNoを取得
        while ( $row = $objQuery->resultFetch() ){
            $conductor_storage_path = $row["CONDUCTOR_STORAGE_PATH_ITA"];
        }

        // terraform outputコマンドを実行し、データリレイストレージに格納
        $conductor_instance_no_str = sprintf("%010s", $conductor_instance_no);  // conductor_instance_no 10桁
        $output_file_name = 'terraform_output_' . $execution_no_str . '.json';
        $output_full_path = $conductor_storage_path . '/' . $conductor_instance_no_str . '/' . $output_file_name;
        $command = "terraform output -json";
        $obj_descriptor_spec = [
            0 => ["pipe", "r"],
            1 => ["file", $output_full_path,  "w"],
        ];
        $ary_pipe = [];
        $res_process = proc_open($command, $obj_descriptor_spec, $ary_pipe);
        if (is_resource($res_process)===false ){
            // 異常発生(作業No.:{} [FILE]{}[LINE]{})
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
        }
    }

    //----------------------------------------------
    // 最終ステータスを更新
    //----------------------------------------------
    // ステータスを更新出来ませんでした。(作業No.:{} ステータス:{})
    $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208270", array($execution_no, $update_status));
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
    // 処理対象の作業インスタンス情報取得
    //----------------------------------------------
    $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
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
    $cln_execution_row["STATUS_ID"]        = $update_status;
    $cln_execution_row['TIME_END']         = "DATETIMEAUTO(6)";
    $cln_execution_row["FILE_RESULT"]      = $result_zip_file_name;

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

    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = "MAIN PROCCESS IS COMPLETED.";
        require ($root_dir_path . $log_output_php );
    }
} catch (Exception $e) {
    if($emergency_flg == 0) {
    //緊急停止でなければ
        // 異常フラグON
        $error_flag = 1;
    }

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
    } elseif($cln_execution_row && $error_flag == 1) {
        //----------------------------------------------
        // 処理中にエラーがあった場合、ステータスを「想定外エラー」に設定
        //----------------------------------------------
        // ステータスの更新に失敗しました。 (ステータス: 想定外エラー 作業No.:{})
        $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208160", array($execution_no));
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
        $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
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

        //----------------------------------------------
        // 処理対象の作業インスタンスのステータスを更新
        //----------------------------------------------
        $cln_execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;
        $cln_execution_row["STATUS_ID"]        = $STATUS_EXCEPTION;
        // $cln_execution_row['TIME_START']       = "DATETIMEAUTO(6)";
        $cln_execution_row['TIME_END']         = "DATETIMEAUTO(6)";
        if (isset($in_zip_file_name) && !empty($in_zip_file_name)) {
            $tgt_row["FILE_INPUT"]             = $in_zip_file_name;
        }
        if (isset($result_zip_file_name) && !empty($result_zip_file_name)) {
            $tgt_row["FILE_RESULT"]            = $result_zip_file_name;
        }
        $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

        $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
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


//----------------------------------------------
// terraformコマンドの発行
//----------------------------------------------
function ExecCommand($command, $cmd_log, $is_make_file = false, &$FREE_LOG) {
    global $terraform_env;

    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $db_access_user_id;

    // global $workspace_work_dir;
    global $execution_no;
    global $resut_file_path;
    global $error_log;
    global $error_flag;

    //----------------------------------------------
    // 変数・function定義
    //----------------------------------------------
    require($root_dir_path . $terraform_env);

    $update_status = $STATUS_COMPLETE;

    try {
        // var_dump($command);

        // 0は使用しない(標準入力はない)が、コマンドの出力先は添え字1に、エラーは添え字2に固定
        // 双方向ではないものの proc_open を使うのは proc_get_statusなど後続の関数を利用するため
        $obj_descriptor_spec = [
            0 => ["pipe", "r"],
            1 => ["file", "{$cmd_log}",  "w"],
            2 => ["file", "{$error_log}", "a"],
        ];
        $ary_pipe = [];

        $build_command = $command. " -no-color";
        $res_process = proc_open($build_command, $obj_descriptor_spec, $ary_pipe);

        // 起動できたかを確認する
        if (is_resource($res_process)===false ){
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__);
        }

        // コマンドの実行ステータスを取得
        $ary_status_on_begin = proc_get_status($res_process);
        // ステータスからプロセスIDを取得
        $int_trg_pid = $ary_status_on_begin['pid'];

        // すでにファイルが存在していた
        if( $is_make_file === true && is_file($resut_file_path) === true ){
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__);
        }

        // コマンドとPIDを書き込む
        $str_body = $build_command . " : PID=" .$int_trg_pid ."\n";
        if( $is_make_file === true ){
            $is_write_result_file = file_put_contents($resut_file_path, $str_body, LOCK_EX);
        } else {
            $is_write_result_file = file_put_contents($resut_file_path, $str_body, FILE_APPEND | LOCK_EX);
        }
        if( $is_write_result_file === false ){
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__);
        }

        // 終了するまで待つ
        pcntl_waitpid($int_trg_pid, $return_status);

        // プロセスが終了した以降の処理
        if( pcntl_wifexited($return_status) === true ){
        // 正常終了した場合
            // リターンコードを取得する
            $exit_code = pcntl_wexitstatus($return_status);
            if($exit_code != 0){
                $update_status = $STATUS_FAILURE;
            }

            $str_body = "COMPLETED({$exit_code})\n";
        } else {
        // 正常終了していない(exitステータスまで取得できなかった)場合
            $str_body = 'PREVENTED\n';
            $update_status = $STATUS_EXCEPTION;
        }

        //----------------------------------------------
        // 結果を書き込む
        //----------------------------------------------
        $is_write_result_file = file_put_contents($resut_file_path, $str_body, FILE_APPEND | LOCK_EX);
        if( $is_write_result_file === false ){
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__);
        }

        return $update_status;
    } catch (Exception $e) {
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        // var_dump($FREE_LOG);
        require($root_dir_path . $log_output_php);

        return $STATUS_EXCEPTION;
    }
}
//----------------------------------------------
// stateファイルを、一時格納先ディレクトリに暗号化して保存
//----------------------------------------------
function SaveEncryptStateFile(&$FREE_LOG) {
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $db_access_user_id;

    global $tar_temp_save_dir;

    global $error_log;
    global $error_flag;
    global $workspace_work_dir;
    global $ary_result_matter;
    global $execution_no;
    global $execution_no_str;
    global $workspace_id;

    try {
        //一時利用ディレクトリの存在をチェックし、なければ作成
        if (!file_exists($tar_temp_save_dir)) {
            if (!mkdir($tar_temp_save_dir, 0777, true)) {
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
            if (!chmod($tar_temp_save_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
            }
        }

        //一時格納先ディレクトリ名を定義
        $tgt_execution_dir = $tar_temp_save_dir . "/" . $execution_no_str;

        //作業実行Noのディレクトリを作成
        if (!mkdir($tgt_execution_dir, 0777, true)) {
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
        if (!chmod($tgt_execution_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }

        //1:tfstate
        $org_state_file = $workspace_work_dir . "/terraform.tfstate";
        $encrypt_state_file = $tgt_execution_dir . "/terraform.tfstate";

        if(!file_exists($org_state_file)){
            return true;
        }else{
            //stateファイルの中身を取得
            $state_file_content = file_get_contents($org_state_file);
        }

        //空ファイルを生成
        if(!touch($encrypt_state_file)){
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300",array($execution_no, $workspace_id, __FILE__ , __LINE__)));
        }else{
            if(!chmod($encrypt_state_file, 0777)){
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310",array($execution_no, $workspace_id, __FILE__ , __LINE__)));
            }
        }
        //ファイルに中身を追記
        file_put_contents($encrypt_state_file, ky_encrypt($state_file_content), LOCK_EX);
        array_push($ary_result_matter, $encrypt_state_file);

        //2:tfstate.backup
        $org_state_file = $workspace_work_dir . "/terraform.tfstate.backup";
        $encrypt_state_file = $tgt_execution_dir . "/terraform.tfstate.backup";

        if(!file_exists($org_state_file)){
            return true;
        }else{
            //stateファイルの中身を取得
            $state_file_content = file_get_contents($org_state_file);
        }

        //空ファイルを生成
        if(!touch($encrypt_state_file)){
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300",array($execution_no, $workspace_id, __FILE__ , __LINE__)));
        }else{
            if(!chmod($encrypt_state_file, 0777)){
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310",array($execution_no, $workspace_id, __FILE__ , __LINE__)));
            }
        }
        //ファイルに中身を追記
        file_put_contents($encrypt_state_file, ky_encrypt($state_file_content), LOCK_EX);
        array_push($ary_result_matter, $encrypt_state_file);

        return true;
    } catch (Exception $e) {
        // 異常フラグON
        $error_flag = 1;
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        // var_dump($FREE_LOG);
        require($root_dir_path . $log_output_php);

        return false;
    }
}
//----------------------------------------------
// 投入ファイル:ZIPファイルを作成する(ITAダウンロード用)
//----------------------------------------------
function MakeInputZipFile() {
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $db_access_user_id;

    global $vg_exe_ins_input_file_dir;

    global $error_log;
    global $error_flag;
    global $execution_no;
    global $execution_no_str;
    global $workspace_id;
    global $ary_input_matter;
    global $input_zip_file_name;

    //zipファイルを格納するディレクトリ
    $in_utn_file_dir = $vg_exe_ins_input_file_dir . "/" . $execution_no_str;
    if (!is_dir($in_utn_file_dir)) {
        // ここ(UTNのdir)だけは再帰的に作成する
        if (!mkdir($in_utn_file_dir, 0777, true)) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
        if (!chmod($in_utn_file_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
    }
    //ZIPファイル名を定義
    $input_zip_file_name = 'InputData_' . $execution_no_str . '.zip';
    //圧縮するファイル名のリスト
    $str_input_matter = implode(" ", $ary_input_matter);

    //ZIPファイルを作成
    $zip_cmd = "zip -j " . $in_utn_file_dir . "/" . $input_zip_file_name . " ".$str_input_matter;
    shell_exec($zip_cmd);

    //zipファイルの存在を確認
    if (!file_exists($in_utn_file_dir . "/" . $input_zip_file_name)) {
        // 異常フラグON
        $error_flag = 1;
        // zipファイルの作成に失敗しました。(作業No:{} FILE:{} LINE:{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208320", array($execution_no, __FILE__, __LINE__)));
    }
}
//----------------------------------------------
// 結果ファイルの格納:ZIPファイルを作成する(ITAダウンロード用)
//----------------------------------------------
function MakeResultZipFile() {
    global $objMTS;

    global $vg_exe_ins_result_file_dir;

    global $error_flag;
    global $execution_no;
    global $execution_no_str;
    global $workspace_id;
    global $ary_result_matter;
    global $result_zip_file_name;

    //zipファイルを格納するディレクトリ
    $in_utn_file_dir = $vg_exe_ins_result_file_dir . "/" . $execution_no_str;
    if (!is_dir($in_utn_file_dir)) {
        // ここ(UTNのdir)だけは再帰的に作成する
        if (!mkdir($in_utn_file_dir, 0777, true)) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208300", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
        if (!chmod($in_utn_file_dir, 0777)) {// mkdirのpermissisonは失敗するケースがある
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208310", array($execution_no, $workspace_id, __FILE__, __LINE__)));
        }
    }

    //ZIPファイル名を定義
    $result_zip_file_name = 'ResultData_' . $execution_no_str . '.zip';
    //圧縮するファイル名のリスト
    $str_result_matter = implode(" ", $ary_result_matter);

    //ZIPファイルを作成
    $zip_cmd = "zip -j " . $in_utn_file_dir . "/" . $result_zip_file_name . " ".$str_result_matter;
    shell_exec($zip_cmd);

    //zipファイルの存在を確認
    if (!file_exists($in_utn_file_dir . "/" . $result_zip_file_name)) {
        // 異常フラグON
        $error_flag = 1;
        // zipファイルの作成に失敗しました。(作業No:{} FILE:{} LINE:{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208320", array($execution_no, __FILE__, __LINE__)));
    }
}
//----------------------------------------------
// 緊急停止
//----------------------------------------------
function IsEmergencyStop() {
    global $dbobj;
    global $objMTS;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $FREE_LOG;

    global $vg_exe_ins_msg_table_name;
    global $vg_exe_ins_msg_table_jnl_seq;
    global $vg_exe_ins_msg_table_jnl_name;
    global $STATUS_SCRAM;

    global $error_flag;
    global $emergency_flg;
    global $db_access_user_id;
    global $workspace_work_dir;
    global $execution_no;
    global $execution_no_str;

    global $ary_result_matter;
    global $in_zip_file_name;
    global $result_zip_file_name;
    global $emergency_stop_file_path;

    if(!file_exists($emergency_stop_file_path)){
        return true;
    }

    // 緊急停止を検知しました。
    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-204240", array($execution_no));
    require ($root_dir_path . $log_output_php );
    // 緊急停止フラグON
    $emergency_flg = 1;

    // 結果ファイルがあれば作る
    if(count($ary_result_matter) > 0){
        // array_push($ary_result_matter, $emergency_stop_file_path);
        MakeResultZipFile();
    }

    // ステータスを「緊急停止」に更新
    // ステータスの更新に失敗しました。 (ステータス: 緊急停止 作業No.:{})
    $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208170", array($execution_no));
    //----------------------------------------------
    // トランザクション開始
    //----------------------------------------------
    $ret = cm_transactionStart($execution_no, $FREE_LOG);
    if($ret === false) {
        $error_flag = 1;
        require($root_dir_path . $log_output_php);
        return false;
    }

    //----------------------------------------------
    // 処理対象の作業インスタンス情報取得
    //----------------------------------------------
    $ret = cm_getEexecutionInstanceRow($dbobj, $execution_no, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
    if($ret === false) {
        $error_flag = 1;
        require($root_dir_path . $log_output_php);
        return false;
    }

    //----------------------------------------------
    // シーケンスをロックし履歴シーケンス採番
    //----------------------------------------------
    $dbobj->ClearLastErrorMsg();
    $intJournalSeqNo = cm_dbaccessGetSequence($dbobj, $vg_exe_ins_msg_table_jnl_seq, $execution_no, $FREE_LOG);
    if($intJournalSeqNo === false) {
        $error_flag = 1;
        require($root_dir_path . $log_output_php);
        return false;
    }

    //----------------------------------------------
    // 処理対象の作業インスタンスのステータスを更新
    //----------------------------------------------
    $cln_execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;
    $cln_execution_row["STATUS_ID"]        = $STATUS_SCRAM;
    // $cln_execution_row['TIME_START']       = "DATETIMEAUTO(6)";
    $cln_execution_row['TIME_END']         = "DATETIMEAUTO(6)";
    if (isset($in_zip_file_name) && !empty($in_zip_file_name)) {
        $tgt_row["FILE_INPUT"]             = $in_zip_file_name;
    }
    if (isset($result_zip_file_name) && !empty($result_zip_file_name)) {
        $tgt_row["FILE_RESULT"]            = $result_zip_file_name;
    }
    $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

    $ret = cm_InstanceRecodeUpdate($dbobj, $vg_exe_ins_msg_table_name, $vg_exe_ins_msg_table_jnl_name, $cln_execution_row, $FREE_LOG);
    if($ret === false) {
        $error_flag = 1;
        require($root_dir_path . $log_output_php);
        return false;
    }

    //----------------------------------------------
    // コミット(レコードロックを解除)
    //----------------------------------------------
    $ret = cm_transactionCommit($execution_no, $FREE_LOG);
    if($ret === false) {
        $error_flag = 1;
        require($root_dir_path . $log_output_php);
        return false;
    }

    //----------------------------------------------
    // トランザクション終了
    //----------------------------------------------
    cm_transactionExit($execution_no);

    return false;
}

//----------------------------------------------
// Typeの情報を取得する
//----------------------------------------------
function getTypeInfo($typeID)
{
    global $objDBCA;
    global $objMTS;
    global $db_model_ch;
    global $root_dir_path;
    global $log_output_php;
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $root_dir_path;

    global $vg_terraform_types_master;

    global $error_flag;

    $typeID = $typeID? $typeID : "1";
    $typeInfo = [];

    $sqlUtnBody = "SELECT "
        . " * "
        . "FROM {$vg_terraform_types_master} "
        . "WHERE DISUSE_FLAG = '0' "
        . "AND TYPE_ID = :TYPE_ID ";

    $arrayUtnBind = array("TYPE_ID" => $typeID);

    //----------------------------------------------
    // クエリー生成
    //----------------------------------------------
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

    if ($objQueryUtn->getStatus() === false) {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    //----------------------------------------------
    // SQL実行
    //----------------------------------------------
    $r = $objQueryUtn->sqlExecute();
    if (!$r) {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    //----------------------------------------------
    // 格納
    //----------------------------------------------
    while ($row = $objQueryUtn->resultFetch()) {
        $typeInfo = $row;
    }

    // DBアクセス事後処理
    unset($objQueryUtn);

    return $typeInfo;
}
//----------------------------------------------
// 配列からHCLへencodeする
//----------------------------------------------
function encodeHCL($array)
{
    $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $res = preg_replace('/\"(.*?)\"\:(.*?)/', '"${1}" = ${2}', $json);
    return $res;
}
//----------------------------------------------
// HCL作成のためにメンバー変数一覧を取得
//----------------------------------------------
function getMemberVarsByModuleVarsLinkIDForHCL($moduleVarsLinkID)
{
    global $objDBCA;
    global $objMTS;
    global $vg_terraform_var_member_view_name;
    global $log_output_php;
    global $root_dir_path;
    $res = [];

    $sqlUtnBody = "SELECT * "
    . "FROM {$vg_terraform_var_member_view_name} "     // メンバー変数テーブル(B_TERRAFORM_VAR_MEMBER)
        . "WHERE DISUSE_FLAG = '0' "
        . "AND PARENT_VARS_ID = :PARENT_VARS_ID " // or is null
        . "ORDER BY ARRAY_NEST_LEVEL, ASSIGN_SEQ ASC ";

    $arrayUtnBind = array(
        "PARENT_VARS_ID" => $moduleVarsLinkID,
    );

    //----------------------------------------------
    // クエリー生成
    //----------------------------------------------
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

    if ($objQueryUtn->getStatus() === false) {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    //----------------------------------------------
    // SQL実行 メンバー変数管理(B_TERRAFORM_VAR_MEMBER)
    //----------------------------------------------
    $r = $objQueryUtn->sqlExecute();
    if (!$r) {
        $FREE_LOG = sprintf(
            "FILE:%s LINE:%s %s",
            basename(__FILE__),
            __LINE__,
            $objQueryUtn->getLastError()
        );
        require($root_dir_path . $log_output_php);
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        // 異常発生(作業No.:{} [FILE]{}[LINE]{})
        throw new Exception($objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-208010", array($execution_no, __FILE__, __LINE__)));
    }
    //----------------------------------------------
    // リソース（Module素材)ファイル名格納
    //----------------------------------------------
    while ($row = $objQueryUtn->resultFetch()) {
        $row["VARS_ENTRY_FLAG"] = 0;
        $res[] = $row;
    }

    // DBアクセス事後処理
    unset($objQueryUtn);

    return $res;
}
//----------------------------------------------
// HCL作成のためにメンバー変数一覧を配列に形成
//----------------------------------------------
function generateMemberVarsArrayForHCL($memberVarsRecords)
{
    $member_vars_res = [];
    // 親リストの取得
    $parentIDMap = makeParentIDMap($memberVarsRecords);

    // 階層リストの作成
    $array_nest_level_list = array_column($memberVarsRecords, "ARRAY_NEST_LEVEL");
    // 階層順に並べ替え
    rsort($array_nest_level_list);
    // 階層リストから重複の削除
    $array_nest_level_list = array_unique($array_nest_level_list);

    $member_vars_array = [];

    foreach ($memberVarsRecords as $memberVarsRecord) {
        // $temp_member_vars_res = [];
        $key = $memberVarsRecord["CHILD_MEMBER_VARS_KEY"];
        if (preg_match("/^\[([0-9]+)\]$/", $memberVarsRecord["CHILD_MEMBER_VARS_KEY"], $match)) {
            $key = $match[1];
        }
        // タイプ情報の取得
        $typeInfo = getTypeInfo($memberVarsRecord["CHILD_VARS_TYPE_ID"]);
        // 配列組み立て
        $trgParentIDMapID = array_search($memberVarsRecord["CHILD_MEMBER_VARS_ID"], array_column($parentIDMap, "child_member_vars_id"));
        $trgParentIDMap = $parentIDMap[$trgParentIDMapID];
        // 配列型のものは配列を具体値に代入する
        $member_vars_res = generateMemberVarsArray($member_vars_res, $key, $memberVarsRecord["CHILD_MEMBER_VARS_VALUE"], $typeInfo, $trgParentIDMap["parent_member_keys_list"]);
    }
    return $member_vars_res;
}

//----------------------------------------------
// 親のインデックスを集めた配列作成
//----------------------------------------------
function makeParentIDMap($memberVarsRecords)
{
    // 返却用配列
    $res = [];
    // 親メンバー変数のキー一覧
    $parent_member_keys_list = [];

    // ネスト取得
    $array_nest_level_list = array_column($memberVarsRecords, "ARRAY_NEST_LEVEL");
    // 並び替え
    sort($array_nest_level_list);
    // 重複削除
    $array_nest_level_list = array_merge(array_unique($array_nest_level_list));

    foreach ($array_nest_level_list as $array_nest_level) {
        foreach ($memberVarsRecords as $memberVarsRecord) {
            // キーの取得
            $key = $memberVarsRecord["CHILD_MEMBER_VARS_KEY"];
            // indexが数値の場合は[]を外す
            if (preg_match("/^\[([0-9]+)\]$/", $memberVarsRecord["CHILD_MEMBER_VARS_KEY"], $match)) {
                $key = $match[1];
            }
            // タイプ情報の取得
            $typeInfo = getTypeInfo($memberVarsRecord["CHILD_VARS_TYPE_ID"]);
            if ($memberVarsRecord["ARRAY_NEST_LEVEL"] == $array_nest_level) {
                // 親のネストリストを取得
                // インデックスを検索
                if ($memberVarsRecord["PARENT_MEMBER_VARS_ID"] != NULL) {
                    $parent_index = array_search($memberVarsRecord["PARENT_MEMBER_VARS_ID"], array_column($res, "child_member_vars_id"));

                    $parent_member_keys_list = $res[$parent_index]["parent_member_keys_list"];
                    $parent_key = $res[$parent_index]["child_member_vars_key"];
                    // indexが数値の場合は[]を外す
                    if ($res[$parent_index]["child_member_vars_key"] != "") {
                        if (preg_match("/^\[([0-9]+)\]$/", $res[$parent_index]["child_member_vars_key"], $match_2)) {
                            $parent_key = $match_2[1];
                        }
                        $parent_member_keys_list[] = $parent_key;
                    }
                }
                $res[] = [
                    "child_member_vars_id"    => $memberVarsRecord["CHILD_MEMBER_VARS_ID"],
                    "child_member_vars_key"   => $key,
                    "parent_member_keys_list" => $parent_member_keys_list,
                ];
            }
        }
    }
    return $res;
}

//----------------------------------------------
// HCL作成のためにメンバー変数一覧を多次元配列に整形
//----------------------------------------------
function generateMemberVarsArray($member_vars_array, $member_vars_key, $member_vars_value, $typeInfo, $map)
{
    $res = [];

    if (empty($map)) {
        // 仮配列と返却用配列をマージ
        $member_vars_array[$member_vars_key] = $member_vars_value;
        $res = $member_vars_array;
    } else {
        // 返却用配列
        $res = [];
        // 仮配列
        $temp_array = [];
        $temp = [];
        $ref = &$temp_array;

        // 多次元配列作成
        foreach ($map as $key) {
            $ref = &$ref[$key];
        }

        // メンバー変数を設定・具体値を代入
        if ($typeInfo["ENCODE_FLAG"] == 1) {
            $member_vars_value = decodeHCL($member_vars_value);
        }
        if ($typeInfo["MEMBER_VARS_FLAG"] == 1 && $typeInfo["MEMBER_VARS_FLAG"] != 1) {
            $ref[$member_vars_key] = [];
        } else {
            $ref[$member_vars_key] = $member_vars_value;
        }

        // 仮配列と返却用配列をマージ
        $res = array_replace_recursive($member_vars_array, $temp_array);
    }

    return $res;
}

//----------------------------------------------
// 
//----------------------------------------------
function makeKVStrRow($key, $value, $type_id){
    $str_row = '';
    switch( $type_id ) {
        case '1':
        case '18':
            $str_row = $key .'="'. $value .'"';
            break;
        default:
            $str_row = $key .'='. $value;
    }

    return $str_row;
}
//----------------------------------------------
// logファイルへの出力関数
//----------------------------------------------
function LocalLogPrint($log_file, $message)
{
    if (file_exists($log_file)) {
        $filepointer = fopen($log_file, "a");
        flock($filepointer, LOCK_EX);
        fputs($filepointer, $message . "\n");
        flock($filepointer, LOCK_UN);
        fclose($filepointer);
    }
}
?>
