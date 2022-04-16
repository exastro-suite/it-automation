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

    // 二重 require 対応
    if(!defined('define_ky_ansible_common_setenv')) {
        define("define_ky_ansible_common_setenv","ky_ansible_common_setenv");

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

        // 実行エンジン
        define("DF_EXEC_MODE_ANSIBLE"         ,'1');         // Ansibleで実行
        define("DF_EXEC_MODE_TOWER"           ,'2');         // AnsibleTowerで実行
        define("DF_EXEC_MODE_AAC"             ,'3');         // ansible automation controllerで実行

        // Towerノードタイプ
        define("DF_EXECUTE_NODE"              ,'execute_node');         //  ISOLATED・実行ノード
        define("DF_CONTROL_NODE"              ,'control?nide');         //  ハイブリット・制御ノード

        $vg_TowerProjectsScpPathArray = array();       // Tower(/var/lib/awx/projects)ディレクトリへのファイル転送パス配列
        define("DF_SCP_CONDUCTOR_ITA_PATH",            "SCP_CONDUCTOR_ITA_PATH");
        define("DF_SCP_CONDUCTOR_TOWER_PATH",          "SCP_CONDUCTOR_TOWER_PATH");
        define("DF_SCP_SYMPHONY_ITA_PATH",             "SCP_SYMPHONY_ITA_PATH");
        define("DF_SCP_SYMPHONY_TOWER_PATH",           "SCP_SYMPHONY_TOWER_PATH");
        define("DF_SCP_OUT_ITA_PATH",                  "SCP_OUT_ITA_PATH");
        define("DF_SCP_OUT_TOWER_PATH",                "SCP_OUT_TOWER_PATH");
        define("DF_SCP_TMP_ITA_PATH",                  "SCP_TMP_ITA_PATH");
        define("DF_SCP_TMP_TOWER_PATH",                "SCP_TMP_TOWER_PATH");
        define("DF_SCP_IN_PARAMATERS_ITA_PATH",        "SCP_IN_PARAMATERS_ITA_PATH");
        define("DF_SCP_IN_PARAMATERS_TOWER_PATH",      "SCP_IN_PARAMATERS_TOWER_PATH");
        define("DF_SCP_IN_PARAMATERS_FILE_ITA_PATH",   "SCP_IN_PARAMATERS_FILE_ITA_PATH");
        define("DF_SCP_IN_PARAMATERS_FILE_TOWER_PATH", "SCP_IN_PARAMATERS_FILE_TOWER_PATH");
        define("DF_GITREPO_OUT_PATH",                  "GITREPO_OUT_PATH");
        define("DF_GITREPO_TMP_PATH",                  "GITREPO_TMP_PATH");
        define("DF_GITREPO_SYMPHONY_PATH",             "GITREPO_SYMPHONY_PATH");
        define("DF_GITREPO_CONDUCTOR_PATH",            "GITREPO_CONDUCTOR_PATH");

        // AnsibleTower処理区分
        define("DF_EXECUTION_FUNCTION"        ,'1');
        define("DF_CHECKCONDITION_FUNCTION"   ,'2');
        define("DF_DELETERESOURCE_FUNCTION"   ,'3');
        define("DF_RESULTFILETRANSFER_FUNCTION"   ,'4');

        // B_ANS_TWR_JOBTP_PROPERTY->PROPERTY_TYPE
        define("DF_JobTemplateKeyValueProperty","1");
        define("DF_JobTemplateVerbosityProperty","2");
        define("DF_JobTemplatebooleanTrueProperty","3");
        define("DF_JobTemplateExtraVarsProperty","4");

        // 変数定義場所 setVariableDefineLocationの種別
        define('DF_DEF_VARS','DEFAULT_VARS');        // role　default変数定義
        define('DF_TEMP_VARS','TEMPLART_VARS');      // テンプレート管理　変数定義
        define('DF_README_VARS','ITAREADME_VARS');   // ITA_readme

        // 変数タイプ
        define("DF_VAR_TYPE_VAR"               ,"VAR");
        define("DF_VAR_TYPE_LCA"               ,"LCA");
        define("DF_VAR_TYPE_GBL"               ,"GBL");
        define("DF_VAR_TYPE_USER"              ,"USER");

        // 具体値 SENSITIVE設定値
        define("DF_SENSITIVE_OFF"              ,"1");  //OFF
        define("DF_SENSITIVE_ON"               ,"2");  //ON

   	 // 機器一覧 パスワード管理フラグ(LOGIN_PW_HOLD_FLAG)
    	define("DF_LOGIN_PW_HOLD_FLAG_OFF"     ,'0');  // パスワード管理なし
    	define("DF_LOGIN_PW_HOLD_FLAG_ON"      ,'1');  // パスワード管理あり
    	define("DF_LOGIN_PW_HOLD_FLAG_DEF"     ,'0');  // デフォルト値 パスワード管理なし
    	// 機器一覧 認証方式(LOGIN_AUTH_TYPE)
    	define("DF_LOGIN_AUTH_TYPE_KEY"        ,'1');  // 鍵認証(パスフレーズなし)
    	define("DF_LOGIN_AUTH_TYPE_PW"         ,'2');  // パスワード認証
    	define("DF_LOGIN_AUTH_TYPE_KEY_EXCH"   ,'3');  // 認証方式:鍵認証(鍵交換済み)
    	define("DF_LOGIN_AUTH_TYPE_KEY_PP_USE" ,'4');  // 認証方式:鍵認証(パスフレーズあり)
    	define("DF_LOGIN_AUTH_TYPE_PW_WINRM"   ,'5');  // 認証方式:パスワード認証(winrm)
    }

    // ITA側で管理している legacy用 子playbookファイル格納先ディレクトリ
    $vg_legacy_playbook_contents_dir  = $root_dir_path . "/uploadfiles/2100020104/PLAYBOOK_MATTER_FILE";
    // ITA側で管理している pioneer用 対話ファイル格納先ディレクトリ
    $vg_pioneer_playbook_contents_dir = $root_dir_path . "/uploadfiles/2100020205/DIALOG_MATTER_FILE";

    // ITA側で管理している copyファイル格納先ディレクトリ
    $vg_copy_contents_dir = $root_dir_path . "/uploadfiles/2100040703/CONTENTS_FILE";
    // ITA側で管理している テンプレートファイル格納先ディレクトリ
    $vg_template_contents_dir = $root_dir_path . "/uploadfiles/2100040704/ANS_TEMPLATE_FILE";
    
    // 正規表記でエスケープが必要な文字 $ ( ) * + - / ? ^ { | }
    // 親変数で許容する文字: xxx_[0-9a-zA-Z_]　  xxx:VAR/LCA/GBL
    $VAR_parent_VarName  = "/^(\s*)VAR_[0-9a-zA-Z_]*(\s*)$/";  /* 通常の変数     */
    $LCA_parent_VarName  = "/^(\s*)LCA_[0-9a-zA-Z_]*(\s*)$/";  /* 読替変数       */
    $GBL_parent_VarName  = "/^(\s*)GBL_[0-9a-zA-Z_]*(\s*)$/";  /* グローバル変数 */
    $USER_parent_VarName = "/^(\s*)[0-9a-zA-Z_]*(\s*)$/";
    // メンバー変数名定義の正規表記
    // 許容文字は、英数字と!#$%&()*+,-/;<=>?@^_`{|}~ "':\
    // 許容しない記号   . [ ] 合計3文字
    ////////////////////////////////////////////////////////////
    // 変数定義の変数名定義判定配列
    //'parant':親変数名として利用可否
    //           true:利用可能
    //           false:利用不可
    //'member':メンバー変数名として利用可否
    //           true:利用可能
    //           false:利用不可
    // pattern:親変数名として許可する正規表記
    ////////////////////////////////////////////////////////////
    // default変数定義ファイル変数定義用
    $VarName_pattenAry = array();
    $VarName_pattenAry[DF_DEF_VARS]      = array();
    $VarName_pattenAry[DF_TEMP_VARS]     = array();
    $VarName_pattenAry[DF_README_VARS]   = array();
    $VarName_pattenAry[DF_DEF_VARS][]    = array('parent'=>true,  'type'=>DF_VAR_TYPE_VAR,  'pattern'=>$VAR_parent_VarName);
    $VarName_pattenAry[DF_DEF_VARS][]    = array('parent'=>false,  'type'=>DF_VAR_TYPE_LCA,  'pattern'=>$LCA_parent_VarName);
    $VarName_pattenAry[DF_DEF_VARS][]    = array('parent'=>false, 'type'=>DF_VAR_TYPE_GBL,  'pattern'=>$GBL_parent_VarName);
    $VarName_pattenAry[DF_DEF_VARS][]    = array('parent'=>true,  'type'=>DF_VAR_TYPE_USER, 'pattern'=>$USER_parent_VarName);
    // テンプレート管理変数定義用
    $VarName_pattenAry[DF_TEMP_VARS][]   = array('parent'=>true,  'type'=>DF_VAR_TYPE_VAR,  'pattern'=>$VAR_parent_VarName);
    $VarName_pattenAry[DF_TEMP_VARS][]   = array('parent'=>true,  'type'=>DF_VAR_TYPE_LCA,  'pattern'=>$LCA_parent_VarName);
    $VarName_pattenAry[DF_TEMP_VARS][]   = array('parent'=>true,  'type'=>DF_VAR_TYPE_GBL,  'pattern'=>$GBL_parent_VarName);
    $VarName_pattenAry[DF_TEMP_VARS][]   = array('parent'=>false, 'type'=>DF_VAR_TYPE_USER, 'pattern'=>$USER_parent_VarName);
    // ITA-Readme変数定義用
    $VarName_pattenAry[DF_README_VARS][] = array('parent'=>true,  'type'=>DF_VAR_TYPE_VAR,  'pattern'=>$VAR_parent_VarName);
    $VarName_pattenAry[DF_README_VARS][] = array('parent'=>false, 'type'=>DF_VAR_TYPE_LCA,  'pattern'=>$LCA_parent_VarName);
    $VarName_pattenAry[DF_README_VARS][] = array('parent'=>false, 'type'=>DF_VAR_TYPE_GBL,  'pattern'=>$GBL_parent_VarName);
    $VarName_pattenAry[DF_README_VARS][] = array('parent'=>true,  'type'=>DF_VAR_TYPE_USER, 'pattern'=>$USER_parent_VarName);

    $vg_TowerProjectPath         = "/var/lib/awx/projects";  // Tower Project Path
    $vg_TowerExastroProjectPath  = "/var/lib/exastro";       // Tower Exastro専用 Project Path

?>
