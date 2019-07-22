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
//    ・各メニューから呼び出されるファイル
//
//////////////////////////////////////////////////////////////////////

$tmpAry=explode('ita-root', dirname(__FILE__));
$root_dir_path=$tmpAry[0].'ita-root';
unset($tmpAry);
$fileName = basename(__FILE__);

// メニューID取得
$_GET_Id = "";
if(array_key_exists('no', $_GET)){
    $_GET_Id = $_GET['no'];
}

$individualSystemFile = $root_dir_path . "/webroot/menus/systems/$_GET_Id/$fileName";
$individualUserFile = $root_dir_path . "/webroot/menus//users/$_GET_Id/$fileName";
// メニュー個別ファイルがある場合は、それを呼び出す
if(file_exists($individualSystemFile)){
    require_once($individualSystemFile);
}
else if(file_exists($individualUserFile)){
    require_once($individualUserFile);
}
// メニュー個別ファイルがない場合は、Templateを呼び出す
else{
    require_once($root_dir_path . "/libs/templates/webdbcore/$fileName");
}

