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
    $vg_info_table_name               = "D_TERRAFORM_IF_INFO";

    // 作業インスタンステーブル名
    $vg_exe_ins_msg_table_name        = "C_TERRAFORM_EXE_INS_MNG";

    // 作業インスタンスジャーナルテーブル名
    $vg_exe_ins_msg_table_jnl_name    = "C_TERRAFORM_EXE_INS_MNG_JNL";

    // 作業インスタンスジャーナルテーブル名 シーケンス管理項目名
    $vg_exe_ins_msg_table_jnl_seq     = "C_TERRAFORM_EXE_INS_MNG_JSQ";

    //Organization管理テーブル名
    $vg_terraform_organization_table_name = "B_TERRAFORM_ORGANIZATIONS";

    //Workspaces管理テーブル名
    $vg_terraform_workspaces_table_name = "B_TERRAFORM_WORKSPACES";

    //Organization-Workspacde紐付けVIEW
    $vg_terraform_organization_workspace_link_view_name = "D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK";

    // Module変数紐付け管理テーブル名
    $vg_terraform_module_vars_link_table_name     = "B_TERRAFORM_MODULE_VARS_LINK";

    // 代入値管理テーブル名
    $vg_terraform_vars_assign_table_name         = "B_TERRAFORM_VARS_ASSIGN";

    // 代入値自動登録設定テーブル名
    $vg_terraform_val_assign_table_name          = "B_TERRAFORM_VAL_ASSIGN";

    // 代入値自動登録設定VIEW名
    $vg_terraform_val_assign_view_name          = "D_TERRAFORM_VAL_ASSIGN";

    // 代入値管理情報紐付け（代入値/変数名/Movement紐付け)管理VIEW
    $vg_terraform_vars_data_view_name            = "D_TERRAFORM_VARS_DATA";

    // 代入値管理作業パターン紐付け管理VIEW
    $vg_terraform_ptn_vars_link_view_name        = "D_TERRAFORM_PTN_VARS_LINK";

    // Module素材管理テーブル名
    $vg_terraform_module_table_name = "B_TERRAFORM_MODULE";

    // Policy管理テーブル名
    $vg_terraform_policy_table_name = "B_TERRAFORM_POLICY";

    // Policy Set管理テーブル名
    $vg_terraform_policy_set_table_name = "B_TERRAFORM_POLICY_SETS";

    // PolicySet-Policy紐付け管理テーブル名
    $vg_terraform_policyset_policy_link_table_name ="B_TERRAFORM_POLICYSET_POLICY_LINK";

    // PolicySet-Workspace紐付け管理テーブル名
    $vg_terraform_policyset_workspace_link_table_name = "B_TERRAFORM_POLICYSET_WORKSPACE_LINK";

    // 作業パターン一覧VIEW
    $vg_terraform_pattern_view_name = "E_TERRAFORM_PATTERN";

    // 作業パターン詳細 テーブル名
    $vg_terraform_pattern_link_table_name = "B_TERRAFORM_PATTERN_LINK";

    // メンバー変数管理 テーブル名
    $vg_terraform_var_member_table_name = "B_TERRAFORM_VAR_MEMBER";

    // メンバー変数管理 VIEW名
    $vg_terraform_var_member_view_name = "D_TERRAFORM_VAR_MEMBER";

    // Terraformタイプマスタ
    $vg_terraform_types_master = "B_TERRAFORM_TYPES_MASTER";

    // 変数ネスト管理
    $vg_terraform_max_member_col_table_name = "B_TERRAFORM_LRL_MAX_MEMBER_COL";


    ///////////////////////
    // ディレクトリ関連       //
    ///////////////////////
    // tar.gz/ZIPファイルの作成ディレクトリ（一時）
    $tar_temp_save_dir                  = $root_dir_path . '/temp/terraform_module_temp';

    //logファイル格納ディレクトリ
    $log_save_dir                       = $root_dir_path . '/logs/terraform_out_logs';

    // 入力ファイル格納先ディレクトリ  /FILE_INPUT
    $vg_exe_ins_input_file_dir          = $root_dir_path . "/uploadfiles/2100080011/FILE_INPUT";

    // 結果ファイル格納先ディレクトリ  /FILE_RESULT
    $vg_exe_ins_result_file_dir         = $root_dir_path . "/uploadfiles/2100080011/FILE_RESULT";

    // ITA側で管理している TERRAFORM用 Module素材ファイル格納先ディレクトリ
    $vg_terraform_module_contents_dir  = $root_dir_path . "/uploadfiles/2100080005/MODULE_MATTER_FILE";

    // ITA側で管理している TERRAFORM用 Policy素材ファイル格納先ディレクトリ
    $vg_terraform_policy_contents_dir  = $root_dir_path . "/uploadfiles/2100080006/POLICY_MATTER_FILE";


?>
