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
//      AnsibleTower メッセージ呼び出し クラス のラッパー
//
//  【特記事項】
//      不要かもしれない。
//      都度、必要なところでインスタンス化しても動作に変わりはない。
//      インスタンス生成コストを下げられるか？
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/commonlibs/common_php_classes.php");

class MessageTemplateStorageHolder {

    private static $MTS_INSTANCE = null;

    // static only
    private function __construct() {
    }

    static function getMTS() {
        if(! isset(static::$MTS_INSTANCE)) {
            static::$MTS_INSTANCE = new MessageTemplateStorage();
        }

        return static::$MTS_INSTANCE;
    }

    // function __destruct() {
    // }
}

?>
