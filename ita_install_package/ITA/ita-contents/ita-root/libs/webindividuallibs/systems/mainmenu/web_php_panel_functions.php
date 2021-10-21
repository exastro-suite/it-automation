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

    // ウィジット情報取得
    function b_get_widget_info(){
        global $g;
        $root_dir_path = $g["root_dir_path"];
        require_once($root_dir_path . "/libs/commonlibs/common_php_classes.php");
        $user_id = $g["login_id"];

        $objDBCA = $g["objDBCA"];
        $err_msg = "";

        // menu_group情報の中でDB上に登録がないもの（取得したJSONから情報のアップデートの可能性のないカラム）
        $not_regist_menu_group_info_list = array(
            "order",
            "position",
        );

        try{
            if (!is_login_status()) {
                throw new Exception($err_msg);
            }
            $objMTS = $g['objMTS'];
            //----------------------------------------------
            // WIDGET情報の取得
            //----------------------------------------------
            //SQL作成
            $sql = "SELECT *
                    FROM A_WIDGET_LIST
                    WHERE USER_ID = ".$user_id;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $row = $objQuery->resultFetch();
                $num_of_rows = $objQuery->effectedRowCount();
                //WIDGET情報が重複登録されている場合も以降の処理をスキップ
                if( $num_of_rows > 1 ){
                    $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                    throw new Exception($err_msg);
                }
            } else {
                throw new Exception($err_msg);
            }

            // JSON登録がなかった場合
            if (!$row) {
                $result = array(
                   "data" => array(),
                   "menu" => get_panel_info(),
                   "widget" => array(),
                );
            }
            // JSON登録があった場合
            else {
                $update_result = array();
                $result = array();
                $widget_data = json_decode($row["WIDGET_DATA"]);
                $panel_list = get_panel_list();
                $menu_group_array = array();
                $menu_group_added_array = array();

                if ($widget_data != NULL) {
                    foreach ($widget_data as $key => $value) {
                        if ($key == "menu") {
                            if(empty($value)) $value = get_panel_info(); // $value が空の場合にメニュー情報を格納
                            foreach ($value as $menu_group_id => $menu_data) {
                                foreach ($panel_list as $panel_menu_group_id) {
                                    if ($panel_menu_group_id == $menu_group_id) {
                                        $menu_group_result = get_menu_group_info($menu_group_id);
                                        if ( !empty($menu_group_result) ) {
                                            $menu_group_array[$menu_group_id] = $menu_group_result;
                                        }
                                        // JSONから引き継ぐデータの後入れ
                                        foreach ($not_regist_menu_group_info_list as $column) {
                                            foreach ($menu_data as $column2 => $content2) {
                                                if ($column == $column2) {
                                                    if ($content2 !== "") {
                                                        $menu_group_array[$menu_group_id][$column] = $content2;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (can_get_menu_group_info($panel_menu_group_id)) {
                                            $menu_group_result = get_menu_group_info($panel_menu_group_id);
                                            if ( !empty($menu_group_result) ) {
                                                $menu_group_added_array[$panel_menu_group_id] = $menu_group_result;
                                            }
                                        }
                                    }
                                }
                            }
                            $result[$key] = $menu_group_array + $menu_group_added_array;
                            $update_result[$key] = $menu_group_array;
                        } else {
                            $result[$key] = $value;
                            $update_result[$key] = $value;
                        }
                    }
                    // JSONの情報を更新
                    $updated_result = update_widget_info($update_result);
                    if ($updated_result[1] != 200) {
                        $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                        throw new Exception($err_msg);
                    }
                } else {
                    throw new Exception($err_msg);
                }

            }
            // DBアクセス事後処理
            unset($objQuery);
            return $result;
        }
        catch (Exception $e){
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            if ( $objDBCA->getTransactionMode() ) {
                $objDBCA->transactionRollBack();
            }
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // 登録してあるJSON情報の更新
    function update_widget_info($data) {
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            //----------------------------------------------
            // 共通モジュールの呼び出し
            //----------------------------------------------
            $objMTS = $g['objMTS'];

            // // JSONに変換
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);

            // 登録済みの場合
            $sql = "UPDATE A_WIDGET_LIST
                    SET WIDGET_DATA = :WIDGET_DATA,
                    LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
                    WHERE USER_ID = :USER_ID";

            $rows = array();
            $tmpAryBind = array(
                "WIDGET_DATA" => $json,
                "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s"),
                "USER_ID" => $user_id
            );
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $result = array(
                    "000",
                    "200",
                    ""
                );
                return $result;
            } else {
                $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                throw new Exception($err_msg);
            }
            unset($objQuery);
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // ウィジット情報登録
    function b_regist_widget_info($data){
        global $g;
        $root_dir_path = $g["root_dir_path"];
        require_once($root_dir_path . "/libs/commonlibs/common_php_classes.php");
        $objMTS = $g['objMTS'];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";

        $necessary_column_list = array(
            "data",
            "widget",
            "menu",
        );

        try{
            if (!is_login_status()) {
                throw new Exception($err_msg);
            }
            // JSONかどうかのチェック
            if ($data == NULL) {
                throw new Exception($err_msg);
            }
            $data = htmlspecialchars_decode($data);
            $array_data = json_decode($data);
            if (!is_json($data)) {
                throw new Exception($err_msg);
            }
            // 必要な項目があるかどうかのチェック
            foreach ($array_data as $column => $content) {
                $is_ok = false;
                foreach ($necessary_column_list as $searched_column) {
                    if ($column == $searched_column) {
                        $is_ok = true;
                        break;
                    }
                }
                if ( !$is_ok ) {
                    throw new Exception($err_msg);
                }
            }
            //----------------------------------------------
            // WIDGET情報登録
            //----------------------------------------------
            // すでに登録されてあるWIDGET情報があるかどうかチェック
            $sql = "SELECT *
                    FROM A_WIDGET_LIST
                    WHERE USER_ID = ".$user_id;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $num_of_rows = $objQuery->effectedRowCount();
                $row = $objQuery->resultFetch();
            }
            // WIDGETが1つ以上ある場合
            if ($num_of_rows > 1) {
                throw new Exception($err_msg);
            // WIDGET登録済み
            } elseif($num_of_rows == 1) {
                // 登録済みの場合
                $sql = "UPDATE A_WIDGET_LIST
                        SET WIDGET_DATA = :WIDGET_DATA,
                        LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
                        WHERE USER_ID = :USER_ID";

                $rows = array();
                $tmpAryBind = array(
                    "WIDGET_DATA" => $data,
                    "USER_ID" => $user_id,
                    "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s")
                );
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
                if($retArray[0] === true){
                    $objQuery =& $retArray[1];
                    $num_of_rows = $objQuery->effectedRowCount();
                }
                //FETCH行数を取得
                if( $num_of_rows == 1 ){
                    $result = array(
                        "000",
                        "200",
                        ""
                    );
                    return $result;
                } else {
                    $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                    throw new Exception($err_msg);
                }
            // WIDGET未登録
            } else {
                $sql = "INSERT INTO A_WIDGET_LIST
                        (WIDGET_ID, WIDGET_DATA, USER_ID, LAST_UPDATE_TIMESTAMP)
                        VALUES (:WIDGET_ID, :WIDGET_DATA, :USER_ID, :LAST_UPDATE_TIMESTAMP)";
                $rows = array();
                // bind
                $widget_id = get_ric("A_WIDGET_LIST");
                if ($widget_id == "") {
                    throw new Exception($err_msg);
                }
                $tmpAryBind = array(
                    "WIDGET_ID" => $widget_id,
                    "WIDGET_DATA" => $data,
                    "USER_ID" => $user_id,
                    "LAST_UPDATE_TIMESTAMP"=>date("Y-m-d H:i:s")
                );
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
                if($retArray[0] === true){
                    $objQuery =& $retArray[1];
                }
                $table_name = "A_WIDGET_LIST";
                $add_ric_result = add_ric($table_name);
                if ($add_ric_result[1] == 200) {
                    $result = array(
                        "000",
                        "200",
                        ""
                    );
                    return $result;
                } else {
                    throw new Exception($err_msg);
                }
            }

            // DBアクセス事後処理
            unset($objQuery);
        }
        catch (Exception $e){
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            if ( $objDBCA->getTransactionMode() ) {
                $objDBCA->transactionRollBack();
            }
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // Momement取得
    function b_get_movement(){
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $db_access_user_id = $user_id;
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            $objMTS = $g['objMTS'];
            if (!is_login_status()) {
                throw new Exception($err_msg);
            }
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // movement一覧の取得
            //----------------------------------------------
            //SQL作成
            $sql = "SELECT ITA_EXT_STM_ID,ITA_EXT_STM_NAME,MENU_ID,ACCESS_AUTH
                    FROM   B_ITA_EXT_STM_MASTER
                    WHERE  DISUSE_FLAG = '0' ";
            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            if ( can_get_menu_info($row["MENU_ID"]) ) {
                                $number = count(get_movements_by_orch($row["ITA_EXT_STM_ID"]));
                            } else {
                                $number = 0;
                            }
                            $rows[$row["ITA_EXT_STM_ID"]] = array(
                                "name" => $row["ITA_EXT_STM_NAME"], // オーケストレータ名
                                "menu_id" => $row["MENU_ID"], //
                                "number" => $number,
                            );
                        }
                    }
                 }
            }
            // DBアクセス事後処理
            unset($objQuery);
            return $rows;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // オーケストレータidからmovementを取得
    function get_movements_by_orch($orch_id) {
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // 共通モジュールの呼び出し
            //----------------------------------------------
            $objMTS = $g['objMTS'];

            //----------------------------------------------
            // movement情報取得
            //----------------------------------------------
            //SQL作成
            $sql = "SELECT *
                    FROM   C_PATTERN_PER_ORCH
                    WHERE  DISUSE_FLAG = '0' 
                    AND    ITA_EXT_STM_ID = ".$orch_id;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $result = array();
                //廃止フラグOFFの全レコード処理(FETCH
                while ( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $result[] = $row;
                        }
                    }
                }
            }

            // DBアクセス事後処理
            unset($objQuery);

            return $result;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }
    // 作業状況取得
    function b_get_work_info(){
        if (!is_login_status()) {
            $result = array(
                "100",
                "000",
                ""
            );
            return $result;
        }
        $status_list = array(
            1, // 未実行
            2, // 未実行(予約)
            3, // 実行中
            4, // 実行中(遅延)
        );
        $con_menu_id = "2100180006";
        $sym_menu_id = "2100000310";
        if ( can_get_menu_info($con_menu_id) ) {
            $con_list = get_conductor($status_list);
        } else {
            $con_list = array();
        }
        if ( can_get_menu_info($sym_menu_id) ) {
            $sym_list = get_symphony($status_list);
        } else {
            $sym_list = array();
        }
        $result = array(
            "conductor" => $con_list,
            "symphony" => $sym_list,
        );
        return $result;
    }
    // 作業結果取得
    function b_get_work_result(){
        if (!is_login_status()) {
            $result = array(
                "100",
                "000",
                ""
            );
            return $result;
        }
        $status_list = array(
            5, // 正常終了
            6, // 緊急停止
            7, // 異常終了
            8, // 想定外エラー
            9, // 予約取り消し
            10, // 想定外エラー(ループ)
        );
        $con_menu_id = "2100180006";
        $sym_menu_id = "2100000310";
        if ( can_get_menu_info($con_menu_id) ) {
            $con_list = get_conductor($status_list);
        } else {
            $con_list = array();
        }
        if ( can_get_menu_info($sym_menu_id) ) {
            $sym_list = get_symphony($status_list);
        } else {
            $sym_list = array();
        }
        $result = array(
            "conductor" => $con_list,
            "symphony" => $sym_list,
        );
        return $result;
    }

    // 未実行(予約)のSymphony/Conductor取得
    function b_get_symphony_conductor( $days = null ){
        if (!is_login_status()) {
            $result = array(
                "100",
                "000",
                ""
            );
            return $result;
        }

        $sym_menu_id = "2100000310";
        $con_menu_id = "2100180006";

        if ( can_get_menu_info($sym_menu_id) ) {
            $sym_list = get_symphony_reserve($days);
        } else {
            $sym_list = array();
        }

        if ( can_get_menu_info($con_menu_id) ) {
            $con_list = get_conductor_reserve($days);
        } else {
            $con_list = array();
        }

        $result = array(
            "symphony" => $sym_list,
            "conductor" => $con_list
        );
       
        return $result;
    }

    function get_panel_list(){
        global $g;
        $objMTS = $g['objMTS'];
        $objDBCA = $g['objDBCA'];
        
        $aryInfoRepreFile = array();
        
        $tmpAryRetBody = getInfoOfRepresentativeFiles($objDBCA);

        if( $tmpAryRetBody[1] !== null ){
            // ERROR:UNEXPECTED, DETAIL:PRESENTIVE FILES INFO REFER FAILER.
            web_log($objMTS->getSomeMessage("ITABASEH-ERR-102090"));
            webRequestForceQuitFromEveryWhere(500,11610101);//105-＞116
            exit();
        }
        $aryInfoRepreFile = $tmpAryRetBody[0]['InfoOfRepresentativeFilenames'];
        
        // 表示順序、項番の順に並び替え
        $array = array();
        foreach ($aryInfoRepreFile as $key => $value){
            $menu_group_id = $value['MENU_GROUP_ID'];
            if (can_get_menu_group_info($menu_group_id)) {
                $array[] = $menu_group_id;
            }
        }
        return $array;
    }

    function get_panel_info(){
        $panel_list = get_panel_list();

        // 表示順序、項番の順に並び替え
        $array = array();
        foreach ($panel_list as $menu_group_id){
            $menu_group_result = get_menu_group_info($menu_group_id);
            if ( !empty($menu_group_result) ) {
                // throw new Exception($err_msg);
                $array[$menu_group_id] = $menu_group_result;
            }
        }
        return $array;
    }

    function can_get_menu_group_info($menu_group_id) {
        global $g;
        $username = $g['login_name'];
        $objDBCA = $g['objDBCA'];
        // ログイン状態を確認する
        if(0 === strlen($username)){
            $login_status_flag = 0;
        }
        else{
            $login_status_flag = 1;
        }
        $tmpAryRetBody = getFilenameAndMenuNameByMenuGroupID($menu_group_id, $login_status_flag, $username, $objDBCA);
        if(0 < count($tmpAryRetBody[0]['MenuNames'])){
            $is_ok = true;
        } else {
            $is_ok = false;
        }
        return $is_ok;
    }

    function can_get_menu_info($menu_id) {
        global $g;
        $user_id = $g['login_id'];
        try{
            // ユーザのメニュー表示権限
            $sql = "SELECT A_ROLE_MENU_LINK_LIST.MENU_ID
                    FROM   A_ROLE_ACCOUNT_LINK_LIST
                    LEFT OUTER JOIN A_ROLE_MENU_LINK_LIST
                    ON A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
                    WHERE A_ROLE_ACCOUNT_LINK_LIST.DISUSE_FLAG = '0'
                    AND A_ROLE_MENU_LINK_LIST.DISUSE_FLAG = '0'
                    AND A_ROLE_ACCOUNT_LINK_LIST.USER_ID = :USER_ID
                    AND A_ROLE_MENU_LINK_LIST.MENU_ID = :MENU_ID";
            $tmpAryBind = array(
                "USER_ID" => $user_id,
                "MENU_ID" => $menu_id
            );
            $role_list = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                if ( $objQuery->effectedRowCount() > 0 ) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // コンダクター取得
    function get_conductor($status_list){
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $objDBCA = $g["objDBCA"];
        $err_msg = "";

        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // コンダクター一覧の取得
            //----------------------------------------------
            //SQL作成
            $where_sql = "";
            if (!empty($status_list)) {
                foreach ($status_list as $status_id) {
                    if ($where_sql == "") {
                        $where_sql = " WHERE C_CONDUCTOR_INSTANCE_MNG.DISUSE_FLAG = 0 AND C_CONDUCTOR_INSTANCE_MNG.STATUS_ID = ". $status_id;
                    } else {
                        $where_sql .= " OR C_CONDUCTOR_INSTANCE_MNG.DISUSE_FLAG = 0 AND C_CONDUCTOR_INSTANCE_MNG.STATUS_ID = ". $status_id;
                    }
                }
            }
            // TIME_ENDがNULLの場合はLAST_UPDATE_TIMEを入れる
            $sql = "SELECT C_CONDUCTOR_INSTANCE_MNG.CONDUCTOR_INSTANCE_NO,C_CONDUCTOR_INSTANCE_MNG.ACCESS_AUTH,SYM_EXE_STATUS_NAME,
                    CASE WHEN C_CONDUCTOR_INSTANCE_MNG.TIME_END IS NULL THEN C_CONDUCTOR_INSTANCE_MNG.LAST_UPDATE_TIMESTAMP ELSE C_CONDUCTOR_INSTANCE_MNG.TIME_END END AS TIME_END
                    FROM C_CONDUCTOR_INSTANCE_MNG
                    LEFT OUTER JOIN B_SYM_EXE_STATUS
                    ON C_CONDUCTOR_INSTANCE_MNG.STATUS_ID = B_SYM_EXE_STATUS.SYM_EXE_STATUS_ID
                    ". $where_sql;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while ( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $result[$row["CONDUCTOR_INSTANCE_NO"]] = array(
                                "status" => $row["SYM_EXE_STATUS_NAME"],
                                "end" => $row["TIME_END"],
                            );
                        }
                    }
                }
            }

            // DBアクセス事後処理
            unset($objQuery);

            return $result;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }
    // コンダクター取得
    function get_symphony($status_list){
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // 共通モジュールの呼び出し
            //----------------------------------------------
            $objMTS = $g['objMTS'];

            $where_sql = "";
            if (!empty($status_list)) {
                foreach ($status_list as $status_id) {
                    if ($where_sql == "") {
                        $where_sql = " WHERE C_SYMPHONY_INSTANCE_MNG.STATUS_ID = ". $status_id;
                    } else {
                        $where_sql .= " OR C_SYMPHONY_INSTANCE_MNG.STATUS_ID = ". $status_id;
                    }
                }
            }
            //----------------------------------------------
            // Symphony情報取得
            //----------------------------------------------
            // SQL作成
            // TIME_ENDがNULLの場合、LAST_UPDATE_TIME_STAMPを入れる
            $sql = "SELECT C_SYMPHONY_INSTANCE_MNG.SYMPHONY_INSTANCE_NO,C_SYMPHONY_INSTANCE_MNG.ACCESS_AUTH,SYM_EXE_STATUS_NAME,
                    CASE WHEN C_SYMPHONY_INSTANCE_MNG.TIME_END IS NULL THEN C_SYMPHONY_INSTANCE_MNG.LAST_UPDATE_TIMESTAMP ELSE C_SYMPHONY_INSTANCE_MNG.TIME_END END AS TIME_END
                    FROM C_SYMPHONY_INSTANCE_MNG
                    LEFT OUTER JOIN B_SYM_EXE_STATUS
                    ON C_SYMPHONY_INSTANCE_MNG.STATUS_ID = B_SYM_EXE_STATUS.SYM_EXE_STATUS_ID
                    ".$where_sql;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while ( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $result[$row["SYMPHONY_INSTANCE_NO"]] = array(
                                "status" => $row["SYM_EXE_STATUS_NAME"],
                                "end" => $row["TIME_END"],
                            );
                        }
                    }
                }
            }

            // DBアクセス事後処理
            unset($objQuery);

            return $result;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    //----------------------------------------------
    // 未実行（予約）のSymphony取得
    // $days：指定日数後までのデータを取得
    //----------------------------------------------
    function get_symphony_reserve( $days=null ){
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // 共通モジュールの呼び出し
            //----------------------------------------------
            $objMTS = $g['objMTS'];
            // 未実行（予約）= 2
            $where_sql = " WHERE C_SYMPHONY_INSTANCE_MNG.STATUS_ID = 2";
            $where_sql .= " AND C_SYMPHONY_INSTANCE_MNG.TIME_BOOK >= CURRENT_TIMESTAMP ";

            if( isset($days) ){
                $where_d = date("Y-m-d",strtotime("+$days day"));
                $where_d .= " 23:59:59";
                $where_sql .= " AND C_SYMPHONY_INSTANCE_MNG.TIME_BOOK <= '$where_d' ";
            }

            $order_by_sql = " ORDER BY C_SYMPHONY_INSTANCE_MNG.TIME_BOOK ASC;";
            //----------------------------------------------
            // Symphony情報取得
            //----------------------------------------------
            // SQL作成
            $sql = "SELECT C_SYMPHONY_INSTANCE_MNG.SYMPHONY_INSTANCE_NO,"
                            ."C_SYMPHONY_INSTANCE_MNG.I_SYMPHONY_NAME,"
                            ."C_SYMPHONY_INSTANCE_MNG.ACCESS_AUTH,"
                            ."C_SYMPHONY_INSTANCE_MNG.I_OPERATION_NAME,"
                            ."C_SYMPHONY_INSTANCE_MNG.TIME_BOOK,"
                            ."SYM_EXE_STATUS_NAME "
                            ."FROM C_SYMPHONY_INSTANCE_MNG "
                            ."LEFT OUTER JOIN B_SYM_EXE_STATUS "
                            ."ON C_SYMPHONY_INSTANCE_MNG.STATUS_ID = B_SYM_EXE_STATUS.SYM_EXE_STATUS_ID "
                            .$where_sql . $order_by_sql;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while ( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $result[$row["SYMPHONY_INSTANCE_NO"]] = array(
                                "symphony_name" => $row["I_SYMPHONY_NAME"],
                                "operation_name" => $row["I_OPERATION_NAME"],
                                "time_book" => date("Y/m/d H:i", strtotime($row["TIME_BOOK"])),
                                "status" => $row["SYM_EXE_STATUS_NAME"],
                            );
                        }
                    }
                }
            }

            // DBアクセス事後処理
            unset($objQuery);

            return $result;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    //----------------------------------------------
    // 未実行（予約）のConductor取得
    // $days：指定日数後までのデータを取得
    //----------------------------------------------
    function get_conductor_reserve( $days=null ){
        global $g;
        $root_dir_path = $g["root_dir_path"];

        $user_id = $g["login_id"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";
        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            //----------------------------------------------
            // 共通モジュールの呼び出し
            //----------------------------------------------
            $objMTS = $g['objMTS'];
            // 未実行（予約）= 2
            $where_sql = " WHERE C_CONDUCTOR_INSTANCE_MNG.STATUS_ID = 2";
            $where_sql .= " AND C_CONDUCTOR_INSTANCE_MNG.TIME_BOOK >= CURRENT_TIMESTAMP ";

            if( isset($days) ){
                $where_d = date("Y-m-d",strtotime("+$days day"));
                $where_d .= " 23:59:59";
                $where_sql .= " AND C_CONDUCTOR_INSTANCE_MNG.TIME_BOOK <= '$where_d' ";
            }

            $order_by_sql = " ORDER BY C_CONDUCTOR_INSTANCE_MNG.TIME_BOOK ASC;";
            //----------------------------------------------
            // conductor情報取得
            //----------------------------------------------
            // SQL作成
            $sql = "SELECT C_CONDUCTOR_INSTANCE_MNG.CONDUCTOR_INSTANCE_NO,"
                            ."C_CONDUCTOR_INSTANCE_MNG.I_CONDUCTOR_NAME,"
                            ."C_CONDUCTOR_INSTANCE_MNG.ACCESS_AUTH,"
                            ."C_CONDUCTOR_INSTANCE_MNG.I_OPERATION_NAME,"
                            ."C_CONDUCTOR_INSTANCE_MNG.TIME_BOOK,"
                            ."SYM_EXE_STATUS_NAME "
                            ."FROM C_CONDUCTOR_INSTANCE_MNG "
                            ."LEFT OUTER JOIN B_SYM_EXE_STATUS "
                            ."ON C_CONDUCTOR_INSTANCE_MNG.STATUS_ID = B_SYM_EXE_STATUS.SYM_EXE_STATUS_ID "
                            .$where_sql . $order_by_sql;
                            
            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while ( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $result[$row["CONDUCTOR_INSTANCE_NO"]] = array(
                                "conductor_name" => $row["I_CONDUCTOR_NAME"],
                                "operation_name" => $row["I_OPERATION_NAME"],
                                "time_book" => date("Y/m/d H:i", strtotime($row["TIME_BOOK"])),
                                "status" => $row["SYM_EXE_STATUS_NAME"],
                            );
                        }
                    }
                }
            }

            // DBアクセス事後処理
            unset($objQuery);

            return $result;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    // 作業履歴取得
    function b_get_work_history(){
        $result = array();
        return $result;
    }

    // メニューグループの情報取得
    function get_menu_group_info($menu_group_id) {
        global $g;
        $root_dir_path = $g["root_dir_path"];
        $objDBCA = $g["objDBCA"];
        $err_msg = "";

        try{
            //----------------------------------------------
            // WIDGET情報の取得
            //----------------------------------------------
            //SQL作成
            $sql = "SELECT *
                    FROM A_MENU_GROUP_LIST
                    WHERE DISUSE_FLAG = 0
                    AND DISP_SEQ IS NOT NULL
                    AND MENU_GROUP_ID = ".$menu_group_id;

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $row = $objQuery->resultFetch();
            }

            if ( !$row ) {
                 $menu_group = array();
            } else {
                $strMenuGroupId = sprintf("%010d", strval($menu_group_id));
                $menu_icon = "/common/imgs/default.png";
                $dir = $root_dir_path . "/webroot/uploadfiles/2100000204/MENU_GROUP_ICON/" . $strMenuGroupId . "/";
                $dir_ = "/uploadfiles/2100000204/MENU_GROUP_ICON/" . $strMenuGroupId . "/";
                if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
                    while(false !== ($file = readdir($handle))) {
                        if( filetype( $path = $dir . $file ) == "file" ) {
                            $menu_icon = $dir_ . $file;
                        }
                    }
                }
                $menu_group = array(
                    "name" => htmlspecialchars($row["MENU_GROUP_NAME"], ENT_QUOTES, "UTF-8"),
                    "order"=> $row['DISP_SEQ'],
                    "icon"=> $menu_icon,
                    "remarks"=> $row["NOTE"],
                    "position"=> ""
                );
            }
            // DBアクセス事後処理
            unset($objQuery);
            return $menu_group;
        }
        catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    function get_ric($table_name) {
        global $g;
        $table_name = $table_name."_RIC";
        $objMTS = $g['objMTS'];
        $err_msg = "";
        try{
            $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = '".$table_name."'";

            $rows = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            $result = array();
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
            }
            //FETCH行数を取得
            $num_of_rows = $objQuery->effectedRowCount();

            if( $num_of_rows == 1 ){
                $ric = $objQuery->resultFetch();
                return $ric["VALUE"];
            } else {
                $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                throw new Exception($err_msg);
            }
        } catch (Exception $e){
            return "";
        }
    }

    function add_ric($table_name) {
        global $g;
        $num = get_ric($table_name);
        $column_name = $table_name."_RIC";
        $added_num = $num + 1;
        $err_msg = "";
        $objMTS = $g['objMTS'];
        try{
            $sql = "UPDATE A_SEQUENCE 
                    SET VALUE = :VALUE,
                    LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
                    WHERE NAME = :NAME";
            $tmpAryBind = array(
                "VALUE" => $added_num,
                "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s"),
                "NAME" => $column_name,
            );
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                $result = array(
                    "000",
                    "200",
                    ""
                );
                return $result;
            } else {
                $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
                throw new Exception($err_msg);
            }
        } catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }

    function is_json($json) {
        if (json_decode($json) == NULL) {
            $is_ok = false;
        } else {
            $is_ok = true;
        }
        return $is_ok;
    }

    function is_login_status() {
        global $g;
        $username = $g['login_name'];
        // ログイン状態を確認する
        if(0 === strlen($username)){
            $login_status_flag = false;
        }
        else{
            $login_status_flag = true;
        }
        return $login_status_flag;
    }

    function get_orch() {
        global $g;
        $uer_id = $g['login_id'];
        $objDBCA = $g["objDBCA"];
        $root_dir_path = $g['root_dir_path'];
        try{
            require_once($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
            $obj = new RoleBasedAccessControl($objDBCA);
            $ret  = $obj->getAccountInfo($g['login_id']);
            // オーケストレーションの取得
            $sql = "SELECT ITA_EXT_STM_ID,ITA_EXT_STM_NAME,MENU_ID,ACCESS_AUTH
                    FROM   B_ITA_EXT_STM_MASTER
                    WHERE  DISUSE_FLAG = '0' ";
            $orch_list = array();
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
            if($retArray[0] === true){
                $objQuery =& $retArray[1];
                while( $row = $objQuery->resultFetch() ){
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                        throw new Exception($err_msg);
                    } else {
                        if($permission === true) {
                            $orch_list[$row["ITA_EXT_STM_ID"]] = array(
                                "name" => $row["ITA_EXT_STM_NAME"], // オーケストレータ名
                                "menu_id" => $row["MENU_ID"], //
                            );
                        }
                    }
                 }
            }
            return $orch_list;
        } catch (Exception $e){
            $result = array(
                "100",
                "000",
                $e->getMessage()
            );
            return $result;
        }
    }
?>
