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

define('MENU_GROUP_ID_CONV_HOST',           2100011609);                // 縦メニューホスト分解用のメニューグループID
define('MENU_GROUP_ID_MIDDLE_HG',           2100011613);                // 縦横変換用中間シート用のメニューグループID

define('FILE_HG_LOADTABLE',             'hostgroup_00_loadTable.php');            // ホストグループ用の00_loadTable.phpテンプレート
define('FILE_HG_LOADTABLE_VAL',         'hostgroup_00_loadTable_value.php');      // ホストグループ用の00_loadTable.phpのデータ部テンプレート(単一行文字列用)
define('FILE_HG_LOADTABLE_MUL',         'hostgroup_00_loadTable_multiValue.php'); // ホストグループ用の00_loadTable.phpのデータ部テンプレート(複数行文字列用)
define('FILE_HG_LOADTABLE_INT',         'hostgroup_00_loadTable_integer.php');    // ホストグループ用の00_loadTable.phpのデータ部テンプレート(整数用)
define('FILE_HG_LOADTABLE_FLT',         'hostgroup_00_loadTable_float.php');      // ホストグループ用の00_loadTable.phpのデータ部テンプレート(小数用)
define('FILE_HG_LOADTABLE_DAY',         'hostgroup_00_loadTable_date.php');       // ホストグループ用の00_loadTable.phpのデータ部テンプレート(日付用)
define('FILE_HG_LOADTABLE_DT',          'hostgroup_00_loadTable_datetime.php');   // ホストグループ用の00_loadTable.phpのデータ部テンプレート(日時用)
define('FILE_HG_LOADTABLE_ID',          'hostgroup_00_loadTable_id.php');         // ホストグループ用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_HG_LOADTABLE_PW',          'hostgroup_00_loadTable_password.php');   // ホストグループ用の00_loadTable.phpのデータ部テンプレート(PW用)
define('FILE_H_LOADTABLE',              'host_00_loadTable.php');                 // ホスト用の00_loadTable.phpテンプレート
define('FILE_H_LOADTABLE_OP',           'host_00_loadTable_oponly.php');          // ホスト(オペレーションのみ)用の00_loadTable.phpテンプレート
define('FILE_H_LOADTABLE_VAL',          'host_00_loadTable_value.php');           // ホスト用の00_loadTable.phpのデータ部テンプレート(単一行文字列用)
define('FILE_H_LOADTABLE_MUL',          'host_00_loadTable_multiValue.php');      // ホスト用の00_loadTable.phpのデータ部テンプレート(複数行文字列用)
define('FILE_H_LOADTABLE_INT',          'host_00_loadTable_integer.php');         // ホスト用の00_loadTable.phpのデータ部テンプレート(整数用)
define('FILE_H_LOADTABLE_FLT',          'host_00_loadTable_float.php');           // ホスト用の00_loadTable.phpのデータ部テンプレート(小数用)
define('FILE_H_LOADTABLE_DAY',          'host_00_loadTable_date.php');            // ホスト用の00_loadTable.phpのデータ部テンプレート(日付用)
define('FILE_H_LOADTABLE_DT',           'host_00_loadTable_datetime.php');        // ホスト用の00_loadTable.phpのデータ部テンプレート(日時用)
define('FILE_H_LOADTABLE_ID',           'host_00_loadTable_id.php');              // ホスト用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_H_LOADTABLE_PW',           'host_00_loadTable_password.php');        // ホスト用の00_loadTable.phpのデータ部テンプレート(PW用)
define('FILE_VIEW_LOADTABLE',           'view_00_loadTable.php');                 // 最新値参照用の00_loadTable.phpテンプレート
define('FILE_VIEW_LOADTABLE_OP',           'view_00_loadTable_oponly.php');       // 最新値参照(オペレーションのみ)用の00_loadTable.phpテンプレート
define('FILE_VIEW_LOADTABLE_VAL',       'view_00_loadTable_value.php');           // 最新値参照用の00_loadTable.phpのデータ部テンプレート(単一行文字列用)
define('FILE_VIEW_LOADTABLE_MUL',       'view_00_loadTable_multiValue.php');      // 最新値参照用の00_loadTable.phpのデータ部テンプレート(複数行文字列用)
define('FILE_VIEW_LOADTABLE_INT',       'view_00_loadTable_integer.php');         // 最新値参照用の00_loadTable.phpのデータ部テンプレート(整数用)
define('FILE_VIEW_LOADTABLE_FLT',       'view_00_loadTable_float.php');           // 最新値参照用の00_loadTable.phpのデータ部テンプレート(小数用)
define('FILE_VIEW_LOADTABLE_DAY',       'view_00_loadTable_date.php');            // 最新値参照用の00_loadTable.phpのデータ部テンプレート(日付用)
define('FILE_VIEW_LOADTABLE_DT',        'view_00_loadTable_datetime.php');        // 最新値参照用の00_loadTable.phpのデータ部テンプレート(日時用)
define('FILE_VIEW_LOADTABLE_ID',        'view_00_loadTable_id.php');              // 最新値参照用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_VIEW_LOADTABLE_PW',        'view_00_loadTable_password.php');        // 最新値参照用の00_loadTable.phpのデータ部テンプレート(PW用)
define('FILE_CONVERT_LOADTABLE',        'convert_00_loadTable.php');              // 縦管理メニュー(ホストグループ)用の00_loadTable.phpテンプレート
define('FILE_CONVERT_LOADTABLE_VAL',    'convert_00_loadTable_value.php');        // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(単一行文字列用)
define('FILE_CONVERT_LOADTABLE_MUL',    'convert_00_loadTable_multiValue.php');   // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(複数行文字列用)
define('FILE_CONVERT_LOADTABLE_INT',    'convert_00_loadTable_integer.php');      // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(整数用)
define('FILE_CONVERT_LOADTABLE_FLT',    'convert_00_loadTable_float.php');        // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(小数用)
define('FILE_CONVERT_LOADTABLE_DAY',    'convert_00_loadTable_date.php');         // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(日付用)
define('FILE_CONVERT_LOADTABLE_DT',     'convert_00_loadTable_datetime.php');     // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(日時用)
define('FILE_CONVERT_LOADTABLE_ID',     'convert_00_loadTable_id.php');           // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_CONVERT_LOADTABLE_PW',     'convert_00_loadTable_password.php');     // 縦管理メニュー(ホストグループ)用の00_loadTable.phpのデータ部テンプレート(PW用)
define('FILE_CONVERT_H_LOADTABLE',      'convert_host_00_loadTable.php');         // 縦管理メニュー(ホスト)用の00_loadTable.phpテンプレート
define('FILE_CONVERT_H_LOADTABLE_OP',   'convert_host_00_loadTable_oponly.php');  // 縦管理メニュー(ホスト(オペレーションのみ))用の00_loadTable.phpテンプレート
define('FILE_CONVERT_H_LOADTABLE_VAL',  'convert_host_00_loadTable_value.php');   // 縦管理メニュー(ホスト)用の00_loadTable.phpのデータ部テンプレート
define('FILE_CONVERT_H_LOADTABLE_ID',   'convert_host_00_loadTable_id.php');      // 縦管理メニュー(ホスト)用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_HG_SQL',                   'hostgroupDB.sql');                       // ホストグループ用のDB作成用のSQLテンプレート
define('FILE_H_SQL',                    'hostDB.sql');                            // ホスト用のDB作成用のSQLテンプレート
define('FILE_CONVERT_SQL',              'convertDB.sql');                         // 縦管理メニュー(ホストグループ)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_H_SQL',            'convert_hostDB.sql');                    // 縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート
define('FILE_H_OP_SQL',                 'hostDB_oponly.sql');                     // ホスト(オペレーションのみ)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_H_OP_SQL',         'convert_hostDB_oponly.sql');             // 縦管理メニュー(ホスト(オペレーションのみ))用のDB作成用のSQLテンプレート
define('FILE_MST_LOADTABLE',            'master_00_loadTable.php');               // マスタ用の00_loadTable.phpテンプレート
define('FILE_MST_LOADTABLE_VAL',        'master_00_loadTable_value.php');         // マスタ用の00_loadTable.phpのデータ部テンプレート
define('FILE_MST_SQL',                  'masterDB.sql');                          // マスタ用のDB作成用のSQLテンプレート

define('FILE_CMDB_LOADTABLE',              'cmdb_00_loadTable.php');            // CMDB用の00_loadTable.phpテンプレート
define('FILE_CMDB_LOADTABLE_VAL',          'cmdb_00_loadTable_value.php');      // CMDB用の00_loadTable.phpのデータ部テンプレート(単一行文字列用)
define('FILE_CMDB_LOADTABLE_MUL',          'cmdb_00_loadTable_multiValue.php'); // CMDB用の00_loadTable.phpのデータ部テンプレート(複数行文字列用)
define('FILE_CMDB_LOADTABLE_INT',          'cmdb_00_loadTable_integer.php');    // CMDB用の00_loadTable.phpのデータ部テンプレート(整数用)
define('FILE_CMDB_LOADTABLE_FLT',          'cmdb_00_loadTable_float.php');      // CMDB用の00_loadTable.phpのデータ部テンプレート(小数用)
define('FILE_CMDB_LOADTABLE_DAY',          'cmdb_00_loadTable_date.php');       // CMDB用の00_loadTable.phpのデータ部テンプレート(日付用)
define('FILE_CMDB_LOADTABLE_DT',           'cmdb_00_loadTable_datetime.php');   // CMDB用の00_loadTable.phpのデータ部テンプレート(日時用)
define('FILE_CMDB_LOADTABLE_ID',           'cmdb_00_loadTable_id.php');         // CMDB用の00_loadTable.phpのデータ部テンプレート(ID用)
define('FILE_CMDB_LOADTABLE_PW',           'cmdb_00_loadTable_password.php');   // CMDB用の00_loadTable.phpのデータ部テンプレート(PW用)
define('FILE_CMDB_SQL',                    'cmdbDB.sql');                       // CMDB用のDB作成用のSQLテンプレート

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
define('REPLACE_FLOAT_MAX',         '★★★FLOAT_MAX★★★');
define('REPLACE_FLOAT_MIN',         '★★★FLOAT_MIN★★★');
define('REPLACE_FLOAT_DIGIT',       '★★★FLOAT_DIGIT★★★');
define('REPLACE_INT_MAX',           '★★★INT_MAX★★★');
define('REPLACE_INT_MIN',           '★★★INT_MIN★★★');
define('REPLACE_MULTI_MAX_LENGTH',  '★★★MULTI_MAX_LENGTH★★★');
define('REPLACE_MULTI_PREG',        '★★★MULTI_PREG_MATCH★★★');
define('REPLACE_DATE_FORMAT',       '★★★DATE_FORMAT★★★');
define('REPLACE_PW_MAX_LENGTH',     '★★★PW_MAX_LENGTH★★★');