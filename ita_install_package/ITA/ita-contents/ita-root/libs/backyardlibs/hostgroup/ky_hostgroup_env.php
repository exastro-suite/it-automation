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

define('LOG_DIR',               ROOT_DIR_PATH . '/logs/backyardlogs/');
define('HOSTGROUP_LIB_PATH',    ROOT_DIR_PATH . '/libs/backyardlibs/hostgroup/');
define('COMMONLIBS_PATH',       ROOT_DIR_PATH . '/libs/commonlibs/');
define('WEBCOMMONLIBS_PATH',    ROOT_DIR_PATH . '/libs/webcommonlibs/');

define('HIERARCHY_LIMIT',       15);            // 親子紐付の階層の上限値

define('USER_ID_SPLIT_HOST_GRP',        -101701);       // ホストグループ分解機能
define('USER_ID_MAKE_HOST_GRP_VAR',     -101702);       // ホストグループ変数化機能
define('USER_ID_REGIST_HOST_GRP_VAR',   -101703);       // ホストグループ変数登録機能

define('LOG_LEVEL',                     getenv('LOG_LEVEL'));
define('LAST_UPDATE_USER',              -100025);       // 最終更新者

