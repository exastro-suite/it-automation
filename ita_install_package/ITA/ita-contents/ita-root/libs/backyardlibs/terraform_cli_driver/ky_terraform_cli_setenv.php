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

    ///////////////////////
    // ルートディレクトリを取得   //
    ///////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ///////////////////////
    // テーブル関連         //
    ///////////////////////
    // インターフェース情報テーブル名
    $vg_info_table_name               = "D_TERRAFORM_CLI_IF_INFO";

    // 作業インスタンステーブル名
    $vg_exe_ins_msg_table_name        = "C_TERRAFORM_CLI_EXE_INS_MNG";

    // 作業インスタンスジャーナルテーブル名
    $vg_exe_ins_msg_table_jnl_name    = "C_TERRAFORM_CLI_EXE_INS_MNG_JNL";

    // 作業インスタンスジャーナルテーブル名 シーケンス管理項目名
    $vg_exe_ins_msg_table_jnl_seq     = "C_TERRAFORM_CLI_EXE_INS_MNG_JSQ";

    //Workspaces管理テーブル名
    $vg_terraform_workspaces_table_name = "B_TERRAFORM_CLI_WORKSPACES";

    // Module変数紐付け管理テーブル名
    $vg_terraform_module_vars_link_table_name     = "B_TERRAFORM_CLI_MODULE_VARS_LINK";

    // 代入値管理テーブル名
    $vg_terraform_vars_assign_table_name         = "B_TERRAFORM_CLI_VARS_ASSIGN";

    // 代入値自動登録設定テーブル名
    $vg_terraform_val_assign_table_name          = "B_TERRAFORM_CLI_VAL_ASSIGN";

    // 代入値自動登録設定VIEW名
    $vg_terraform_val_assign_view_name          = "D_TERRAFORM_CLI_VAL_ASSIGN";

    // 代入値管理情報紐付け（代入値/変数名/Movement紐付け)管理VIEW
    $vg_terraform_vars_data_view_name            = "D_TERRAFORM_CLI_VARS_DATA";

    // 代入値管理作業パターン紐付け管理VIEW
    $vg_terraform_ptn_vars_link_view_name        = "D_TERRAFORM_CLI_PTN_VARS_LINK";

    // Module素材管理テーブル名
    $vg_terraform_module_table_name = "B_TERRAFORM_CLI_MODULE";

    // 作業パターン一覧VIEW
    $vg_terraform_pattern_view_name = "E_TERRAFORM_CLI_PATTERN";

    // 作業パターン詳細 テーブル名
    $vg_terraform_pattern_link_table_name = "B_TERRAFORM_CLI_PATTERN_LINK";

    // メンバー変数管理 テーブル名
    $vg_terraform_var_member_table_name = "B_TERRAFORM_CLI_VAR_MEMBER";

    // メンバー変数管理 VIEW名
    $vg_terraform_var_member_view_name = "D_TERRAFORM_CLI_VAR_MEMBER";

    // Terraformタイプマスタ
    $vg_terraform_types_master = "B_TERRAFORM_CLI_TYPES_MASTER";

    // 変数ネスト管理
    $vg_terraform_max_member_col_table_name = "B_TERRAFORM_CLI_LRL_MAX_MEMBER_COL";


    ///////////////////////
    // ディレクトリ関連       //
    ///////////////////////
    // tar.gz/ZIPファイルの作成ディレクトリ（一時）
    $tar_temp_save_dir                  = $root_dir_path . '/temp/terraform_cli_module_temp';

    //logファイル格納ディレクトリ
    $log_save_dir                       = $root_dir_path . '/logs/terraform_cli_out_logs';

    //Terraform-CLI作業実行ディレクトリ
    $exec_base_dir                      = $root_dir_path . "/terraform_cli_work";
    $exec_base_work_dir                 = "/work";

    // 入力ファイル格納先ディレクトリ  /FILE_INPUT
    $vg_exe_ins_input_file_dir          = $root_dir_path . "/uploadfiles/2100200011/FILE_INPUT";

    // 結果ファイル格納先ディレクトリ  /FILE_RESULT
    $vg_exe_ins_result_file_dir         = $root_dir_path . "/uploadfiles/2100200011/FILE_RESULT";

    // ITA側で管理している TERRAFORM用 Module素材ファイル格納先ディレクトリ
    $vg_terraform_module_contents_dir  = $root_dir_path . "/uploadfiles/2100200004/MODULE_MATTER_FILE";



    // RUN_MODE
    $RUN_MODE_APPLY = "1"; // appply
    $RUN_MODE_PLAN = "2"; // plan
    $RUN_MODE_DESTROY = "3"; // destroy

    // STATUS
    $STATUS_NOT_YET = 1; // 未実行
    $STATUS_PREPARE = 2; // 準備中
    $STATUS_PROCESSING = 3; // 実行中
    $STATUS_PROCESS_DELAYED = 4; // 実行中(遅延)
    $STATUS_COMPLETE = 5; // 完了
    $STATUS_FAILURE = 6; // 完了(異常)
    $STATUS_EXCEPTION = 7; // 想定外エラー
    $STATUS_SCRAM = 8; // 緊急停止
    $STATUS_RESERVE = 9; // 未実行(予約)
    $STATUS_RESERVE_CANCEL = 10; // 予約取消
?>
