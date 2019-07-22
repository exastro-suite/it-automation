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
//  【概要】
//      パラメータで指定されたメニューIDに対応する00_loadTable.phpに
//      PHPの構文エラーなどが無いことを確認する。
//
//////////////////////////////////////////////////////////////////////
$root_dir_temp = array();
$root_dir_temp = explode( "ita-root", dirname(__FILE__) );
$root_dir_path = $root_dir_temp[0] . "ita-root";

require_once ( $root_dir_path . "/libs/commonlibs/common_getInfo_LoadTable.php");

// メニューID取得
$intCheckTgtMenuId = $argv[1];

list($aryTemp,$intErrorType,$strErrMsg)  = getInfoOfLTUsingIdOfMenuForDBtoDBLink($intCheckTgtMenuId,$objDBCA);
exit(0);
?>
