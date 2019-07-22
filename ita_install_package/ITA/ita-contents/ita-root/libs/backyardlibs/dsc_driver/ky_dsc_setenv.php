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

    // DSCドライバ共通の定数をまとめる
    //////////////////////////
    // DSC共通define読込     //
    /////////////////////////
    require_once ($root_dir_path . "/libs/backyardlibs/dsc_driver/ky_dsc_common_setenv.php" );

    // ドライバ識別子
    $vg_driver_id                  = DF_DSC_DRIVER_ID;

    // オーケストレータ識別子
    $vg_OrchestratorSubId = "DSC_NS";

    // オーケストレータ識別子ディレクトリ名
    $vg_OrchestratorSubId_dir = "ns";

    // インターフェース情報テーブル名
    $vg_info_table_name               = "D_DSC_IF_INFO";

    // 作業インスタンステーブル名
    $vg_exe_ins_msg_table_name        = "C_DSC_EXE_INS_MNG";

    // 作業インスタンスジャーナルテーブル名
    $vg_exe_ins_msg_table_jnl_name    = "C_DSC_EXE_INS_MNG_JNL";

    // 変数管理テーブルテーブル名
    $vg_dsc_vars_masterDB         = "B_DSC_VARS_MASTER";

    // 代入値管理テーブルテーブル名
    $vg_dsc_vars_assignDB         = "B_DSC_VARS_ASSIGN";

    // 代入変数名管理テーブルテーブル名
    $vg_dsc_pattern_vars_linkDB   = "B_DSC_PTN_VARS_LINK";

    // 素材管理テーブル テーブル名
    $vg_dsc_master_fileDB         = "B_DSC_RESOURCE";

    // 素材管理テーブル 素材ID(pkey)項目名
    $vg_dsc_master_file_pkeyITEM  = "RESOURCE_MATTER_ID";

    // 素材管理テーブル 素材ファイル項目名
    $vg_dsc_master_file_nameITEM  = "RESOURCE_MATTER_FILE";

    // 作業対象ホスト管理テーブルテーブル名
    $vg_dsc_pho_linkDB            = "B_DSC_PHO_LINK";

    // 作業インスタンスジャーナルテーブル名 シーケンス管理項目名
    $vg_exe_ins_msg_table_jnl_seq     = "C_DSC_EXE_INS_MNG_JSQ";

    // 作業パターン詳細 テーブル名
    $vg_dsc_pattern_linkDB        = "B_DSC_PATTERN_LINK";

    // Powershell素材ファイル テーブル名
    $vg_dsc_powershell_fileDB     = "B_DSC_POWERSHELL_FILE";

    // Param素材ファイル テーブル名
    $vg_dsc_param_fileDB          = "B_DSC_PARAM_FILE";

    // Import素材ファイル テーブル名
    $vg_dsc_import_fileDB         = "B_DSC_IMPORT_FILE";

    // コンフィグデータ素材ファイル テーブル名
    $vg_dsc_configdata_fileDB     = "B_DSC_CONFIGDATA_FILE";

    // コンパイルオプション素材ファイル テーブル名
    $vg_dsc_cmpoption_fileDB      = "B_DSC_CMPOPTION_FILE";

    // 資格情報 テーブル名
    $vg_dsc_credentialDB          = "B_DSC_CREDENTIAL";

    // ZIPファイルの作成ディレクトリ（一時）
    $zip_temp_save_dir                  = $root_dir_path . '/temp';

    // 入力ファイル格納先ディレクトリ  /FILE_INPUT
    $vg_exe_ins_input_file_dir          = $root_dir_path . "/uploadfiles/2100060011";

    // 結果ファイル格納先ディレクトリ  /FILE_RESULT
    $vg_exe_ins_result_file_dir         = $root_dir_path . "/uploadfiles/2100060011";

    // ITA側で管理している DSC用 リソース(Config素材)ファイル格納先ディレクトリ
    $vg_dsc_resource_contents_dir  = $root_dir_path . "/uploadfiles/2100060003/RESOURCE_MATTER_FILE";

    $vg_dsc_config_dir = "";

    // ITA側で管理している DSC用 Powershell素材ファイル格納先ディレクトリ
    $vg_dsc_powershell_file_dir = $root_dir_path . "/uploadfiles/2100060016/POWERSHELL_FILE";

    // ITA側で管理している DSC用 Param素材ファイル格納先ディレクトリ
    $vg_dsc_param_file_dir      = $root_dir_path . "/uploadfiles/2100060017/PARAM_FILE";

    // ITA側で管理している DSC用 Import素材ファイル格納先ディレクトリ
    $vg_dsc_import_file_dir     = $root_dir_path . "/uploadfiles/2100060018/IMPORT_FILE";

    // ITA側で管理している DSC用 コンフィグデータ素材ファイル格納先ディレクトリ
    $vg_dsc_configdata_file_dir = $root_dir_path . "/uploadfiles/2100060019/CONFIGDATA_FILE";

    // ITA側で管理している DSC用 コンパイルオプション素材ファイル格納先ディレクトリ
    $vg_dsc_cmpoption_file_dir  = $root_dir_path . "/uploadfiles/2100060020/CMPOPTION_FILE";
    
    // ITA側で管理している DSC用 認証キーファイル格納先ディレクトリ
    $vg_dsc_certificate_file_dir  = $root_dir_path . "/uploadfiles/2100000303/DSC_CERTIFICATE_FILE";

?>
