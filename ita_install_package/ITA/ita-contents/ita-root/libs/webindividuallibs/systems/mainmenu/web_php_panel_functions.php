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

    // 選択した表示モードをDBへ格納する
    function mode_select_input($display_mode){

        // 初期定義
        global $g;

        $body = array("DISUSE_FLAG__0" => "0");
        $strFxName = __FUNCTION__;
        $objDBCA = $g["objDBCA"];
        $user_name = $g["login_name"];

        $root_dir_path = $g["root_dir_path"];
        require_once( $root_dir_path . "/libs/webcommonlibs/web_php_tag_print_functions.php");

        // SQL作成,実行
        $sql1 = "update A_SORT_MENULIST set DISPLAY_MODE = '". $display_mode ."' where USER_NAME= '". $user_name ."'";
        singleSQLExecuteAgent($sql1, $body, $strFxName);

        return $display_mode;
    }

    function default_mode_select(){

        // 初期定義
        global $g;

        $body = array("DISUSE_FLAG__0" => "0");
        $strFxName = __FUNCTION__;
        $objDBCA = $g["objDBCA"];
        $user_name = $g["login_name"];

        $root_dir_path = $g["root_dir_path"];
        require_once( $root_dir_path . "/libs/webcommonlibs/web_php_tag_print_functions.php");

        // SQL作成,実行
        $sql1 = "select DISPLAY_MODE from A_SORT_MENULIST where USER_NAME='". $user_name ."'";
        $tmp_ary = singleSQLExecuteAgent($sql1, $body, $strFxName);

        $objQuery =& $tmp_ary[1];
        $result = "";
        $row = "";
        while ( $result_row = $objQuery->resultFetch() ){
            $row = $result_row;
        }
        foreach((array)$row as $key => $value){
            $result = $value;
        }
        if($result == '') {
            $sql2 = "INSERT INTO A_SORT_MENULIST (USER_NAME, MENU_ID_LIST, SORT_ID_LIST, DISPLAY_MODE)
                     VALUES('" . $user_name . "', '', '', 'middle_panel')";

            singleSQLExecuteAgent($sql2, $body, $strFxName);

            $tmp_ary = singleSQLExecuteAgent($sql1, $body, $strFxName);

            $objQuery =& $tmp_ary[1];
            $result = "";
            $row = "";
            while ( $result_row = $objQuery->resultFetch() ){
                $row = $result_row;
            }
            foreach((array)$row as $key => $value){
                $result = $value;
            }
        } 
        return $result;
    }

    // メニューの並び順をDBに格納する
    function panel_sort_update($result){

        // 初期定義
        global $g;

        $body = array("DISUSE_FLAG__0" => "0");
        $strFxName = __FUNCTION__;
        $objDBCA = $g["objDBCA"];
        $user_name = $g["login_name"];

        $root_dir_path = $g["root_dir_path"];
        require_once( $root_dir_path . "/libs/webcommonlibs/web_php_tag_print_functions.php");

        // 配列を文字列に変換する
        $menu_id_list = implode(",", $result);
        $tmp_sort_list = array();
        $sort_list_count = 10;

        foreach($result as $tmp_mg_id){
            array_push($tmp_sort_list, $sort_list_count);
            $sort_list_count = $sort_list_count + 10;
        }
        $sort_id_list = implode(",", $tmp_sort_list);

        // SQL作成,実行
        $sql2 = "update A_SORT_MENULIST set MENU_ID_LIST = '". $menu_id_list ."' where USER_NAME= '". $user_name ."'";
        $sql3 = "update A_SORT_MENULIST set SORT_ID_LIST = '". $sort_id_list ."' where USER_NAME= '". $user_name ."'";

        singleSQLExecuteAgent($sql2, $body, $strFxName);
        singleSQLExecuteAgent($sql3, $body, $strFxName);

        return $result;
    }

?>
