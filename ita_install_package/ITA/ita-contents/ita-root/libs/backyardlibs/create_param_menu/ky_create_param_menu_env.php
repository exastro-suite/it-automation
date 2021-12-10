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
define('LOG_LEVEL',                 getenv('LOG_LEVEL'));
define('USER_ID_CREATE_PARAM',              -101601);                   // 最終更新者
define('USER_ID_CREATE_OTHER_MENU_LINK',    -101603);                   // 最終更新者

define('MENU_GROUP_ID_CONV_HOST',           2100011609);                // 縦メニューホスト分解用のメニューグループID
define('MENU_GROUP_ID_MIDDLE_HG',           2100011613);                // 縦横変換用中間シート用のメニューグループID

define('FILE_HG_LOADTABLE',             'hostgroup_00_loadTable.tpl');              // ホストグループ用の00_loadTable.tplテンプレート
define('FILE_H_LOADTABLE',              'host_00_loadTable.tpl');                   // ホスト用の00_loadTable.tplテンプレート
define('FILE_VIEW_LOADTABLE',           'view_00_loadTable.tpl');                   // 最新値参照用の00_loadTable.tplテンプレート

define('FILE_CONVERT_LOADTABLE',        'convert_00_loadTable.tpl');                // 縦管理メニュー(ホストグループ)用の00_loadTable.tplテンプレート
define('FILE_CONVERT_H_LOADTABLE',      'convert_host_00_loadTable.tpl');           // 縦管理メニュー(ホスト)用の00_loadTable.tplテンプレート
define('FILE_CONVERT_H_LOADTABLE_VAL',  'convert_host_00_loadTable_value.tpl');     // 縦管理メニュー(ホスト)用の00_loadTable.tplのデータ部テンプレート
define('FILE_CONVERT_H_LOADTABLE_ID',   'convert_host_00_loadTable_id.tpl');        // 縦管理メニュー(ホスト)用の00_loadTable.tplのデータ部テンプレート(ID用)

define('FILE_HG_SQL',                   'hostgroupDB.sql');                         // ホストグループ用のDB作成用のSQLテンプレート
define('FILE_HG_EDIT_SQL',              'hostgroupDB_edit.sql');                    // ホストグループ用のDB作成用のSQLテンプレート(編集)
define('FILE_H_SQL',                    'hostDB.sql');                              // ホスト用のDB作成用のSQLテンプレート
define('FILE_H_EDIT_SQL',               'hostDB_edit.sql');                         // ホスト用のDB作成用のSQLテンプレート(編集)
define('FILE_CONVERT_SQL',              'convertDB.sql');                           // 縦管理メニュー(ホストグループ)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_EDIT_SQL',         'convertDB_edit.sql');                      // 縦管理メニュー(ホストグループ)用のDB作成用のSQLテンプレート(編集)
define('FILE_CONVERT_H_SQL',            'convert_hostDB.sql');                      // 縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_H_EDIT_SQL',       'convert_hostDB_edit.sql');                 // 縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート(編集)

define('FILE_CMDB_LOADTABLE',           'cmdb_00_loadTable.tpl');                   // データシート用の00_loadTable.tplテンプレート
define('FILE_CMDB_SQL',                 'cmdbDB.sql');                              // データシート用のDB作成用のSQLテンプレート
define('FILE_CMDB_EDIT_SQL',            'cmdbDB_edit.sql');                         // データシート用のDB作成用のSQLテンプレート(編集)

define('FILE_H_LOADTABLE_OP',           'oponly_host_00_loadTable.tpl');            // (オペレーションのみ)ホスト用の00_loadTable.tplテンプレート
define('FILE_VIEW_LOADTABLE_OP',        'oponly_view_00_loadTable.tpl');            // (オペレーションのみ)最新値参照用の00_loadTable.tplテンプレート
define('FILE_CONVERT_H_LOADTABLE_OP',   'oponly_convert_host_00_loadTable.tpl');    // (オペレーションのみ)縦管理メニュー(ホスト)用の00_loadTable.tplテンプレート
define('FILE_H_OP_SQL',                 'oponly_hostDB.sql');                       // (オペレーションのみ)ホスト用のDB作成用のSQLテンプレート
define('FILE_H_OP_EDIT_SQL',            'oponly_hostDB_edit.sql');                  // (オペレーションのみ)ホスト用のDB作成用のSQLテンプレート(編集)
define('FILE_CONVERT_H_OP_SQL',         'oponly_convert_hostDB.sql');               // (オペレーションのみ)縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート
define('FILE_CONVERT_H_OP_EDIT_SQL',    'oponly_convert_hostDB_edit.sql');          // (オペレーションのみ)縦管理メニュー(ホスト)用のDB作成用のSQLテンプレート(編集)

define('FILE_PARTS_SNG',                'parts_string.tpl');                        // 00_loadTable.tplのデータ部テンプレート(単一行文字列用)
define('FILE_PARTS_MUL',                'parts_multiString.tpl');                   // 00_loadTable.tplのデータ部テンプレート(複数行文字列用)
define('FILE_PARTS_INT',                'parts_integer.tpl');                       // 00_loadTable.tplのデータ部テンプレート(整数用)
define('FILE_PARTS_FLT',                'parts_float.tpl');                         // 00_loadTable.tplのデータ部テンプレート(小数用)
define('FILE_PARTS_DAY',                'parts_date.tpl');                          // 00_loadTable.tplのデータ部テンプレート(日付用)
define('FILE_PARTS_DT',                 'parts_datetime.tpl');                      // 00_loadTable.tplのデータ部テンプレート(日時用)
define('FILE_PARTS_ID',                 'parts_id.tpl');                            // 00_loadTable.tplのデータ部テンプレート(ID用)
define('FILE_PARTS_LINK_ID',            'parts_link_id.tpl');                       // 00_loadTable.tplのデータ部テンプレート(リンクID用)
define('FILE_PARTS_PW',                 'parts_password.tpl');                      // 00_loadTable.tplのデータ部テンプレート(PW用)
define('FILE_PARTS_UPL',                'parts_upload.tpl');                        // 00_loadTable.tplのデータ部テンプレート(ファイルアップロード用)
define('FILE_PARTS_LNK',                'parts_link.tpl');                          // 00_loadTable.tplのデータ部テンプレート(リンク用)
define('FILE_PARTS_REF',                'parts_reference.tpl');                     // 00_loadTable.tplのデータ部テンプレート(プルダウン選択の参照項目用)
define('FILE_PARTS_TYPE3',              'parts_type3reference.tpl');                // 00_loadTable.tplのデータ部テンプレート(パラメータシート参照用)

define('FILE_PARTS_VIEW_SNG',           'parts_view_string.tpl');                   // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(単一行文字列用)
define('FILE_PARTS_VIEW_MUL',           'parts_view_multiString.tpl');              // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(複数行文字列用)
define('FILE_PARTS_VIEW_INT',           'parts_view_integer.tpl');                  // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(整数用)
define('FILE_PARTS_VIEW_FLT',           'parts_view_float.tpl');                    // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(小数用)
define('FILE_PARTS_VIEW_DAY',           'parts_view_date.tpl');                     // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(日付用)
define('FILE_PARTS_VIEW_DT',            'parts_view_datetime.tpl');                 // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(日時用)
define('FILE_PARTS_VIEW_ID',            'parts_view_id.tpl');                       // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(ID用)
define('FILE_PARTS_VIEW_LINK_ID',       'parts_view_link_id.tpl');                  // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(リンクID用)
define('FILE_PARTS_VIEW_PW',            'parts_view_password.tpl');                 // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(PW用)
define('FILE_PARTS_VIEW_UPL',           'parts_view_upload.tpl');                   // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(ファイルアップロード用)
define('FILE_PARTS_VIEW_LNK',           'parts_view_link.tpl');                     // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(リンク用)
define('FILE_PARTS_VIEW_REF',           'parts_view_reference.tpl');                // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(プルダウン選択の参照項目用)
define('FILE_PARTS_VIEW_TYPE3REF',      'parts_view_type3reference.tpl');           // 00_loadTable.tpl(VIEW用)のデータ部テンプレート(パラメータシート参照用)


define('TABLE_PREFIX',                  'KY_AUTO_TABLE_');                          // テーブル名の接頭語
define('COLUMN_PREFIX',                 'KY_AUTO_COL_');                            // カラム名の接頭語
define('REPLACE_MENU',                  '★★★MENU★★★');
define('REPLACE_INFO',                  '★★★INFO★★★');
define('REPLACE_TABLE',                 '★★★TABLE★★★');
define('REPLACE_ITEM',                  '★★★ITEM★★★');
define('REPLACE_NUM',                   '★★★NUMBER★★★');
define('REPLACE_PREG',                  '★★★PREG_MATCH★★★');
define('REPLACE_VALUE',                 '★★★VALUE_NAME★★★');
define('REPLACE_DISP',                  '★★★DISP_NAME★★★');
define('REPLACE_REQUIRED',              '★★★REQUIRED★★★');
define('REPLACE_UNIQUED',               '★★★UNIQUED★★★');
define('REPLACE_SIZE',                  '★★★SIZE★★★');
define('REPLACE_COL_TYPE',              '★★★COLUMN_TYPE★★★');
define('REPLACE_COL',                   '★★★COLUMN★★★');
define('REPLACE_ALTER_COL',             '★★★ALTER_COLUMN★★★');
define('REPLACE_ID_TABLE',              '★★★ID_TABLE_NAME★★★');
define('REPLACE_ID_PRI',                '★★★PRI_KEY_NAME★★★');
define('REPLACE_ID_COL',                '★★★ID_COL_NAME★★★');
define('REPLACE_INPUT_ORDER',           '★★★INPUT_ORDER★★★');
define('REPLACE_FLOAT_MAX',             '★★★FLOAT_MAX★★★');
define('REPLACE_FLOAT_MIN',             '★★★FLOAT_MIN★★★');
define('REPLACE_FLOAT_DIGIT',           '★★★FLOAT_DIGIT★★★');
define('REPLACE_INT_MAX',               '★★★INT_MAX★★★');
define('REPLACE_INT_MIN',               '★★★INT_MIN★★★');
define('REPLACE_MULTI_MAX_LENGTH',      '★★★MULTI_MAX_LENGTH★★★');
define('REPLACE_MULTI_PREG',            '★★★MULTI_PREG_MATCH★★★');
define('REPLACE_DATE_FORMAT',           '★★★DATE_FORMAT★★★');
define('REPLACE_PW_MAX_LENGTH',         '★★★PW_MAX_LENGTH★★★');
define('REPLACE_UPLOAD_FILE_SIZE',      '★★★UPLOAD_FILE_SIZE★★★');
define('REPLACE_UPLOAD_REF_MENU_ID',    '★★★UPLOAD_REF_MENU_ID★★★');
define('REPLACE_LINK_MAX_LENGTH',       '★★★LINK_MAX_LENGTH★★★');
define('REPLACE_REFERENCE',             '★★★REFERENCE★★★');
define('REPLACE_REF_NUMBER',            '★★★REFERENCE_NUMBER★★★');
define('REPLACE_CLONE_VALUE',           '★★★CLONE_VALUE_NAME★★★');
define('REPLACE_CLONE_DISP',            '★★★CLONE_DISP_NAME★★★');
define('REPLACE_CLONE_ID_TABLE',        '★★★CLONE_ID_TABLE_NAME★★★');
define('REPLACE_CLONE_PRI',             '★★★CLONE_PRI_KEY_NAME★★★');
define('REPLACE_CLONE_COL',             '★★★CLONE_ID_COL_NAME★★★');
define('REPLACE_CLONE_INFO',            '★★★CLONE_INFO★★★');
define('REPLACE_ITEM_PASSWORD',         '★★★REFERENCE_ITEM_PASSWORD★★★');
define('REPLACE_REFERENCE_DATE_FORMAT', '★★★REFERENCE_DATE_FORMAT★★★');
define('REPLACE_LINK_ID_URL1',          '★★★LINK_ID_URL1★★★');
define('REPLACE_LINK_ID_URL2',          '★★★LINK_ID_URL2★★★');
define('REPLACE_URL_OPTION',            '★★★URL_OPTION★★★');
define('REPLACE_UNIQUE_CONSTRAINT',     '★★★UNIQUE_CONSTRAINT★★★');
define('REPLACE_DEFAULT_VALUE',         '★★★DEFAULT_VALUE★★★');
define('REPLACE_REPEAT_CNT',            '★★★REPEAT_CNT★★★');