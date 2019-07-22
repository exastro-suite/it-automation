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
//      Table Definition 呼び出し クラス
//
//////////////////////////////////////////////////////////////////////

class TableDefinitionsMaster {

    // static only
    private function __construct() {
    }

    private static $tableDefinitions;

    public static function getDefinition($tableName) {

        // 事前準備
        if(empty(self::$tableDefinitions)) {

            self::$tableDefinitions = array();

            ////////////////////////////////
            // ルートディレクトリを取得
            ////////////////////////////////
            if (empty($root_dir_path)) {
                $root_dir_temp = array();
                $root_dir_temp = explode("ita-root", dirname(__FILE__));
                $root_dir_path = $root_dir_temp[0] . "ita-root";
            }

            $tableDefinitionDirPath = $root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/table_definition/";   

            ////////////////////////////////
            // テーブルクラス定義ファイル
            ////////////////////////////////
            $tableFileList = array_diff(scandir($tableDefinitionDirPath), array("..", ".", "view", "TableBaseDefinition.php"));
            foreach($tableFileList as $tableClassPHP) {
                require_once($tableDefinitionDirPath . $tableClassPHP);

                $className = basename($tableClassPHP, ".php");
                self::$tableDefinitions[$className::getTableName()] = $className;
            }

            ////////////////////////////////
            // ビュークラス定義ファイル
            ////////////////////////////////
            // 今のところビュークラス定義ファイルはないのでコメント
            //$viewFileList  = array_diff(scandir($tableDefinitionDirPath . "view/"), array("..", "."));
            //foreach($viewFileList as $viewClassPHP) {
            //    require_once($tableDefinitionDirPath . "view/" . $viewClassPHP);
            //
            //    $className = basename($viewClassPHP, ".php");
            //    self::$tableDefinitions[$className::getTableName()] = $className;
            //}
        }

        // 定義取得
        if(array_key_exists($tableName, self::$tableDefinitions)) {
            return self::$tableDefinitions[$tableName];
        } else {
            throw new Exception("No such definition. ($tableName)"); // 開発時のみ発生
        }
    }
}

?>
