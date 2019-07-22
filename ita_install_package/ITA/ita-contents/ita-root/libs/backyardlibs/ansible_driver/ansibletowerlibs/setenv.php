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
//    AnsibleTowerの実行に必要な共通変数の初期値設定
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ansible共通define読込      //
////////////////////////////////

// VARS_TYPE の 具体値定義
//const LC_VARS_ATTR_STD      = '1'; // 一般変数
//const LC_VARS_ATTR_LIST     = '2'; // 複数具体値
//const LC_VARS_ATTR_STRUCT   = '3'; // 多次元変数

// ユーザーホスト変数名の先頭文字
//const DF_HOST_VAR_HED       = "VAR_";
// テンプレートファイル変数名の先頭文字
//const DF_HOST_TPF_HED       = "TPF_";
// copyファイル変数名の先頭文字
//const DF_HOST_CPF_HED       = "CPF_";
// グローバル変数名の先頭文字
//const DF_HOST_GBL_HED       = "GBL_";
// テンプレートファイルからグローバル変数を取り出す場合の区分
//const DF_HOST_TEMP_GBL_HED  = "TEMP_GBL_";

// ロールパッケージ管理 ロールパッケージファイル(ZIP)格納先ディレクトリ
//const DF_ROLE_PACKAGE_FILE_CONTENTS_DIR = "/uploadfiles/2100140003/ROLE_PACKAGE_FILE";

// ステータス定義(DBの値と同期させること)
const NOT_YET           = 1;  // 未実行
const PREPARE           = 2;  // 準備中
const PROCESSING        = 3;  // 実行中
const PROCESS_DELAYED   = 4;  // 実行中(遅延)
const COMPLETE          = 5;  // 完了
const FAILURE           = 6;  // 完了(異常)
const EXCEPTION         = 7;  // 想定外エラー
const SCRAM             = 8;  // 緊急停止
const RESERVE           = 9;  // 未実行(予約中)
const RESERVE_CANCEL    = 10; // 予約取消

const DRY_RUN           = 2;  // 実行モード(ドライラン=チェック)

const INT_NUM_PADDING   = 10;

// WINRM接続ポート デフォルト値
const LC_WINRM_PORT     = 5985;

////////////////////////////////
// ルートディレクトリを取得   //
////////////////////////////////
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

// 各定数定義

// インターフェース情報テーブル名
//$vg_info_table_name                 = " B_ANSIBLE_IF_INFO"; 
// 作業インスタンステーブル名
//$vg_exe_ins_msg_table_name          = "C_ANSTWR_EXE_INS_MNG";
// 作業インスタンスジャーナルテーブル名
//$vg_exe_ins_msg_table_jnl_name      = "C_ANSTWR_EXE_INS_MNG_JNL";
// 作業インスタンスジャーナルテーブル名 シーケンス管理項目名
//$vg_exe_ins_msg_table_jnl_seq       = "C_ANSTWR_EXE_INS_MNG_JSQ";

// 変数管理テーブル名
//$vg_ansible_vars_masterDB           = "B_ANSTWR_VARS";
// 代入値管理テーブル名
//$vg_ansible_vars_assignDB           = "B_ANSTWR_VARS_ASSIGN";
// Movement変数名管理テーブル名
//$vg_ansible_pattern_vars_linkDB     = "B_ANSTWR_PTN_VARS_LINK";
// 作業対象ホスト管理テーブル名
//$vg_ansible_pho_linkDB              = "B_ANSTWR_PHO_LINK";

// Movement詳細 テーブル名
//$vg_ansible_pattern_linkDB          = "B_ANSTWR_PTN_ROLE_LINK";
// ロールパッケージ管理 テーブル名
//$vg_ansible_role_packageDB          = "B_ANSTWR_ROLE_PACKAGE";
// ロール管理 テーブル名
//$vg_ansible_roleDB                  = "B_ANSTWR_ROLE";
// ロール変数管理 テーブル名
//$vg_ansible_role_varsDB             = "B_ANSTWR_ROLE_VARS";

// 多段変数メンバ管理テーブル名
//$vg_ansible_array_memberDB          = "B_ANSTWR_NESTED_MEM_VARS";
// 多段変数配列組合せ管理テーブル名
//$vg_ansible_member_col_combDB       = "B_ANSTWR_NESTEDMEM_COL_CMB";

// 読替表 テーブル名
//$vg_ansible_rep_var_listDB          = "B_ANSTWR_TRANSLATE_VARS";

// ZIPファイルの作成ディレクトリ（一時）
//$zip_temp_save_dir                  = $root_dir_path . '/temp';

// 入力ファイル格納先ディレクトリ
//$vg_exe_ins_input_file_dir          = $root_dir_path . "/uploadfiles/2100140014";

//$file_subdir_zip_input              = 'FILE_INPUT';

// 結果ファイル格納先ディレクトリ
//$vg_exe_ins_result_file_dir         = $root_dir_path . "/uploadfiles/2100140014";

//$file_subdir_zip_result             = 'FILE_RESULT';

// ITA側で管理している copyファイル格納先ディレクトリ
//$vg_copy_contents_dir               = $root_dir_path . "/uploadfiles/2100140004/CONTENTS_FILE";

// 機器一覧のSSH認証鍵ファイル格納先ディレクトリ
//$ssh_key_file_dir                   = $root_dir_path . "/uploadfiles/2100000303/CONN_SSH_KEY_FILE/";
