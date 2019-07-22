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
    //    ansibleモジュールの実行に必要な共通定数を定義
    //
    //////////////////////////////////////////////////////////////////////
    if (empty($root_dir_path)) {
        $root_dir_temp = array();
        $root_dir_temp = explode("ita-root", dirname(__FILE__));
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // ドライバ識別子
    define("DF_LEGACY_DRIVER_ID"           ,"L");
    define("DF_LEGACY_ROLE_DRIVER_ID"      ,"R");
    define("DF_PIONEER_DRIVER_ID"          ,"P");

    // ユーザーホスト変数名の先頭文字
    define("DF_HOST_VAR_HED"               ,"VAR_");
    // テンプレートファイル変数名の先頭文字
    define("DF_HOST_TPF_HED"               ,"TPF_");
    // copyファイル変数名の先頭文字
    define("DF_HOST_CPF_HED"               ,"CPF_");
    // グローバル変数名の先頭文字
    define("DF_HOST_GBL_HED"               ,"GBL_");
    // テンプレートファイルからグローバル変数を取り出す場合の区分
    define("DF_HOST_TEMP_GBL_HED"          ,"TEMP_GBL_");

    // ITA側で管理している ロールパッケージ管理 ロールパッケージファイル(ZIP)格納先ディレクトリ
    define("DF_ROLE_PACKAGE_FILE_CONTENTS_DIR"  ,"/uploadfiles/2100020303/ROLE_PACKAGE_FILE");
    // ITA側で管理している legacy用 子playbookファイル格納先ディレクトリ
    $vg_legacy_playbook_contents_dir  = $root_dir_path . "/uploadfiles/2100020104/PLAYBOOK_MATTER_FILE";
    // ITA側で管理している pioneer用 対話ファイル格納先ディレクトリ
    $vg_pioneer_playbook_contents_dir = $root_dir_path . "/uploadfiles/2100020205/DIALOG_MATTER_FILE";

    // ITA側で管理している copyファイル格納先ディレクトリ
    $vg_copy_contents_dir = $root_dir_path . "/uploadfiles/2100040703/CONTENTS_FILE";
    // ITA側で管理している テンプレートファイル格納先ディレクトリ
    $vg_template_contents_dir = $root_dir_path . "/uploadfiles/2100040704/ANS_TEMPLATE_FILE";

    // 実行エンジン
    define("DF_EXEC_MODE_ANSIBLE"         ,'1');         // Ansibleで実行
    define("DF_EXEC_MODE_TOWER"           ,'2');         // AnsibleTowerで実行

    // AnsibleTower処理区分
    define("DF_EXECUTION_FUNCTION"        ,'1');
    define("DF_CHECKCONDITION_FUNCTION"   ,'2');
    define("DF_DELETERESOURCE_FUNCTION"   ,'3');

    // B_ANS_TWR_JOBTP_PROPERTY->PROPERTY_TYPE
    define("DF_JobTemplateKeyValueProperty","1");
    define("DF_JobTemplateVerbosityProperty","2");
    define("DF_JobTemplatebooleanTrueProperty","3");
    define("DF_JobTemplateExtraVarsProperty","4");
?>
