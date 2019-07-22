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
/**
 * 【処理内容】
 *    定数定義
 */


define('LOG_DIR',                         ROOT_DIR_PATH . '/logs/backyardlogs/');
define('MATERIAL_LIB_PATH',               ROOT_DIR_PATH . '/libs/backyardlibs/material/');
define('COMMONLIBS_PATH',                 ROOT_DIR_PATH . '/libs/commonlibs/');
define('UPLOADFILES_PATH',                ROOT_DIR_PATH . '/uploadfiles/2100150101/');                          // アップロードファイルパス
define('TMP_PATH',                        ROOT_DIR_PATH . '/temp/');

define('ANS_FILE_PATH',                   ROOT_DIR_PATH . '/uploadfiles/2100040703/CONTENTS_FILE/');            // アップロードファイルパス：Ansible   ：ファイル
define('ANS_TEMPLATE_PATH',               ROOT_DIR_PATH . '/uploadfiles/2100040704/ANS_TEMPLATE_FILE//');        // アップロードファイルパス：Ansible   ：テンプレート
define('ANS_PLAYBOOK_PATH',               ROOT_DIR_PATH . '/uploadfiles/2100020104/PLAYBOOK_MATTER_FILE/');     // アップロードファイルパス：Ansible   ：プレイブック
define('ANS_DIALOG_PATH',                 ROOT_DIR_PATH . '/uploadfiles/2100020205/DIALOG_MATTER_FILE/');       // アップロードファイルパス：Ansible   ：対話ファイル素材集
define('ANS_ROLE_PATH',                   ROOT_DIR_PATH . '/uploadfiles/2100020303/ROLE_PACKAGE_FILE/');        // アップロードファイルパス：Ansible   ：ロールパッケージ

define('OPENST_TEMPLATE_PATH',            ROOT_DIR_PATH . '/uploadfiles/2100070002/OPENST_TEMPLATE/');          // アップロードファイルパス：OpenStack ：HEATテンプレート
define('OPENST_ENVIRONMENT_PATH',         ROOT_DIR_PATH . '/uploadfiles/2100070002/OPENST_ENVIRONMENT/');       // アップロードファイルパス：OpenStack ：環境設定ファイル

define('OPENSF_CONFIG_FILE_PATH',         ROOT_DIR_PATH . '/uploadfiles/2100120003/CONFIG_FILE/');              // アップロードファイルパス：OpenShift ：コンフィグファイル

define('DSC_RESOURCE_PATH',               ROOT_DIR_PATH . '/uploadfiles/2100060003/RESOURCE_MATTER_FILE/');     // アップロードファイルパス：DSC       ：コンフィグ素材

define('TP_RESOURCE_PATH',                ROOT_DIR_PATH . '/uploadfiles/2100130103/SCENARIO_FILE/');            // アップロードファイルパス：TestPlayer：シナリオ素材集

define('SCRAB_SGFILE_MASTER_PATH',        ROOT_DIR_PATH . '/uploadfiles/2100040119/SG_FILE/');                  // アップロードファイルパス：SCRAB     ：標準シナリオSGファイル管理

define('SCRAB_USER_SCRIPT_FILE_PATH',     ROOT_DIR_PATH . '/uploadfiles/2100040114/USER_SCRIPT_FILE/');         // アップロードファイルパス：SCRAB     ：ユーザーシナリオ詳細管理 スクリプトファイル
define('SCRAB_USER_SCRIPT_CSV_FILE_PATH', ROOT_DIR_PATH . '/uploadfiles/2100040114/USER_SCRIPT_CSV_FILE/');     // アップロードファイルパス：SCRAB     ：ユーザーシナリオ詳細管理 CSVファイル
define('SCRAB_USER_SCRIPT_SG_FILE_PATH',  ROOT_DIR_PATH . '/uploadfiles/2100040114/USER_SCRIPT_SG_FILE/');      // アップロードファイルパス：SCRAB     ：ユーザーシナリオ詳細管理 SGファイル

define('LOG_LEVEL',         getenv('LOG_LEVEL'));

define('USER_ID_MATERIAL_MANAGEMENT',   -101502);       // 自動払出払戻機能
define('USER_ID_MATERIAL_LINKAGE',      -101503);       // 資材自動連携機能


