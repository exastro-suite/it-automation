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


define('LOG_DIR',           ROOT_DIR_PATH . '/logs/backyardlogs/');
define('CPM_LIB_PATH',      ROOT_DIR_PATH . '/libs/backyardlibs/create_param_menu/');
define('COMMONLIBS_PATH',   ROOT_DIR_PATH . '/libs/commonlibs/');


define('TEMP_PATH',                 ROOT_DIR_PATH . '/temp/');
define('TEMPLATE_PATH',             ROOT_DIR_PATH . '/libs/templates/param_menu/');
define('UPLOAD_PATH',               ROOT_DIR_PATH . '/uploadfiles/2100160004/FILE_NAME/');
define('UPLOAD_PATH_MST',           ROOT_DIR_PATH . '/uploadfiles/2100160104/FILE_NAME/');
define('LOG_LEVEL',                 getenv('LOG_LEVEL'));
define('USER_ID_CREATE_PARAM',              -101601);                   // 最終更新者
define('USER_ID_CREATE_MASTER',             -101602);                   // 最終更新者
define('USER_ID_CREATE_OTHER_MENU_LINK',    -101603);                   // 最終更新者

define('MENU_GROUP_ID_CONV_HOST',           2100011609);                // 縦管理メニュー(ホスト)用のメニューグループID

define('FILE_HG_LOADTABLE',             'hostgroup_00_loadTable.php');          // ホストグループ用の00_loadTable.phpテンプレート
define('FILE_HG_LOADTABLE_VAL',         'hostgroup_00_loadTable_value.php');    // ホストグループ用の00_loadTable.phpのデータ部テンプレート
define('FILE_HG_LOADTABLE_ID',          'hostgroup_00_loadTable_id.php');       // ホストグループ用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_H_LOADTABLE',              'host_00_loadTable.php');               // ホスト用の00_loadTable.phpテンプレート
define('FILE_H_LOADTABLE_VAL',          'host_00_loadTable_value.php');         // ホスト用の00_loadTable.phpのデータ部テンプレート
define('FILE_H_LOADTABLE_ID',           'host_00_loadTable_id.php');            // ホスト用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_VIEW_LOADTABLE',           'view_00_loadTable.php');               // 最新値参照用の00_loadTable.phpテンプレート
define('FILE_VIEW_LOADTABLE_VAL',       'view_00_loadTable_value.php');         // 最新値参照用の00_loadTable.phpのデータ部テンプレート
define('FILE_VIEW_LOADTABLE_ID',        'view_00_loadTable_id.php');            // 最新値参照用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_CONVERT_LOADTABLE',        'convert_00_loadTable.php');            // 縦管理メニュー(ホストグループ)用の00_loadTable.phpテンプレート
define('FILE_CONVERT_LOADTABLE_VAL',    'convert_00_loadTable_value.php');      // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート
define('FILE_CONVERT_LOADTABLE_ID',     'convert_00_loadTable_id.php');         // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_CONVERT_H_LOADTABLE',      'convert_host_00_loadTable.php');       // 縦管理メニュー(ホスト)用の00_loadTable.phpテンプレート
define('FILE_CONVERT_H_LOADTABLE_VAL',  'convert_host_00_loadTable_value.php'); // 縦管理メニュー(ホスト)用の00_loadTable.phpのデータ部テンプレート
define('FILE_CONVERT_H_LOADTABLE_ID',   'convert_host_00_loadTable_id.php');    // 縦管理メニュー(ホスト)用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_HG_SQL',                   'hostgroupDB.sql');                     // ホストグループ用のDB作成用のSQLテンプレート
define('FILE_H_SQL',                    'hostDB.sql');                          // ホスト用のDB作成用のSQLテンプレート
define('FILE_CONVERT_SQL',              'convertDB.sql');                       // 縦管理メニュー(ホストグループ)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_H_SQL',            'convert_hostDB.sql');                  // 縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート
define('FILE_MST_LOADTABLE',            'master_00_loadTable.php');             // マスタ用の00_loadTable.phpテンプレート
define('FILE_MST_LOADTABLE_VAL',        'master_00_loadTable_value.php');       // マスタ用の00_loadTable.phpのデータ部テンプレート
define('FILE_MST_SQL',                  'masterDB.sql');                        // マスタ用のDB作成用のSQLテンプレート

define('TABLE_PREFIX',              'KY_AUTO_TABLE_');                  // テーブル名の接頭語
define('MASTER_PREFIX',             'KY_AUTO_MASTER_');                 // マスタ名の接頭語
define('COLUMN_PREFIX',             'KY_AUTO_COL_');                    // カラム名の接頭語
define('REPLACE_MENU',              '★★★MENU★★★');
define('REPLACE_INFO',              '★★★INFO★★★');
define('REPLACE_TABLE',             '★★★TABLE★★★');
define('REPLACE_ITEM',              '★★★ITEM★★★');
define('REPLACE_NUM',               '★★★NUMBER★★★');
define('REPLACE_PREG',              '★★★PREG_MATCH★★★');
define('REPLACE_VALUE',             '★★★VALUE_NAME★★★');
define('REPLACE_DISP',              '★★★DISP_NAME★★★');
define('REPLACE_REQUIRED',          '★★★REQUIRED★★★');
define('REPLACE_UNIQUED',           '★★★UNIQUED★★★');
define('REPLACE_SIZE',              '★★★SIZE★★★');
define('REPLACE_COL_TYPE',          '★★★COLUMN_TYPE★★★');
define('REPLACE_COL',               '★★★COLUMN★★★');
define('REPLACE_ID_TABLE',          '★★★ID_TABLE_NAME★★★');
define('REPLACE_ID_PRI',            '★★★PRI_KEY_NAME★★★');
define('REPLACE_ID_COL',            '★★★ID_COL_NAME★★★');
define('REPLACE_INPUT_ORDER',       '★★★INPUT_ORDER★★★');
