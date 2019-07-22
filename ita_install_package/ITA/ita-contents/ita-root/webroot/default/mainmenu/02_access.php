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
    //  【特記事項】
    //     表記規則
    //     ・インデントは半角スペースx4
    //     ・関数名宣言行に『 { 』は配置する。
    //     ・if文の宣言行に『 { 』を配置する。
    //     ・elseは『 } 』と同じ行に配置する。
    //     ・文字列リテラルは、原則ダブルコーテーションでラップする
    //     ・連想配列の文字列型鍵は、原則シングルコーテーションでラップする
    //      その他
    //      ・スケルトンディレクトリへの配置用
    //
    //////////////////////////////////////////////////////////////////////

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
 
    //-- サイト個別PHP要素、ここから--
    require_once ( $root_dir_path . "/libs/webindividuallibs/systems/mainmenu/web_parts_for_template_mainmenu_02_access.php");
    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--

        ///////////////////////////////////////
        //  mode_select_inputファンクション  //
        ///////////////////////////////////////
        function mode_select_input($mode){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $executionResult = "";

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/mainmenu/web_php_panel_functions.php");
            $executionResult = mode_select_input($mode);

            return $executionResult;
        }

        ///////////////////////////////////////
        //  panel_sort_updateファンクション  //
        ///////////////////////////////////////
        function panel_sort_update($result){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $executionResult = "";

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/mainmenu/web_php_panel_functions.php");
            $executionResult = panel_sort_update($result);

            return $executionResult;
        }

        /////////////////////////////////////////
        //  default_mode_selectファンクション  //
        /////////////////////////////////////////
        function default_mode_select(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $executionResult = "";

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/mainmenu/web_php_panel_functions.php");
            $executionResult = default_mode_select();
            
            return $executionResult;
        }

        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
