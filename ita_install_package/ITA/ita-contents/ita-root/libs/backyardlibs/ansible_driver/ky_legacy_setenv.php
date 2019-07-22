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
    //    legacyの実行に必要な共通変数の初期値設定
    //
    //////////////////////////////////////////////////////////////////////

    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // 各オーケストレータ共通の定数をまとめる
    ////////////////////////////////
    // ansible共通define読込      //
    ////////////////////////////////
    require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php" );

    // Legacyの場合の各定数定義
    // ドライバ識別子
    $vg_driver_id                  = DF_LEGACY_DRIVER_ID;

    // オーケストレータ識別子
    $vg_OrchestratorSubId = "LEGACY_NS";
    // オーケストレータ識別子ディレクトリ名
    $vg_OrchestratorSubId_dir = "ns";
    // インターフェース情報テーブル名
    $vg_info_table_name               = "B_ANSIBLE_IF_INFO";  
    // 作業インスタンステーブル名
    $vg_exe_ins_msg_table_name        = "C_ANSIBLE_LNS_EXE_INS_MNG";
    // 作業インスタンスジャーナルテーブル名
    $vg_exe_ins_msg_table_jnl_name    = "C_ANSIBLE_LNS_EXE_INS_MNG_JNL";
    // 変数管理テーブルテーブル名
    $vg_ansible_vars_masterDB         = "B_ANSIBLE_LNS_VARS_MASTER";
    // 代入値管理テーブルテーブル名
    $vg_ansible_vars_assignDB         = "B_ANSIBLE_LNS_VARS_ASSIGN";
    // 代入変数名管理テーブルテーブル名
    $vg_ansible_pattern_vars_linkDB   = "B_ANS_LNS_PTN_VARS_LINK";
    // 素材管理テーブル テーブル名
    $vg_ansible_master_fileDB         = "B_ANSIBLE_LNS_PLAYBOOK";
    // 素材管理テーブル 素材ID(pkey)項目名
    $vg_ansible_master_file_pkeyITEM  = "PLAYBOOK_MATTER_ID";
    // 素材管理テーブル 素材ファイル項目名
    $vg_ansible_master_file_nameITEM  = "PLAYBOOK_MATTER_FILE";

    // 作業対象ホスト管理テーブルテーブル名
    $vg_ansible_pho_linkDB            = "B_ANSIBLE_LNS_PHO_LINK";
    // 作業インスタンスジャーナルテーブル名 シーケンス管理項目名
    $vg_exe_ins_msg_table_jnl_seq     = "C_ANSIBLE_LNS_EXE_INS_MNG_JSQ";

    // 作業パターン詳細 テーブル名
    $vg_ansible_pattern_linkDB        = "B_ANSIBLE_LNS_PATTERN_LINK";
    // ロールパッケージ管理 テーブル名
    $vg_ansible_role_packageDB        = "";
    // ロール管理 テーブル名
    $vg_ansible_roleDB                = "";
    // ロール変数管理 テーブル名
    $vg_ansible_role_varsDB           = "";

    // ZIPファイルの作成ディレクトリ（一時）
    $zip_temp_save_dir                  = $root_dir_path . '/temp';
    // 入力ファイル格納先ディレクトリ
    $vg_exe_ins_input_file_dir          = $root_dir_path . "/uploadfiles/2100020113";
    // 結果ファイル格納先ディレクトリ
    $vg_exe_ins_result_file_dir         = $root_dir_path . "/uploadfiles/2100020113";

    // AnsibleTowerのita_executions_prepare_buildで使用している変数
    $vg_tower_driver_type               = "legacy";
    $vg_tower_driver_id                 = "ns";
    $vg_tower_driver_name               = "legacy";
    // 親Playbookのファイル名
    $vg_parent_playbook_name            = "playbook.yml";

?>
