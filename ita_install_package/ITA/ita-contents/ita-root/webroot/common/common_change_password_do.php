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
    //    ・ModuleDistictCode(203)
    //
    //////////////////////////////////////////////////////////////////////
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    $strDifferBody1 = "";

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");

        require_once ( $root_dir_path . "/libs/webcommonlibs/web_auth_config.php");

        // リファラ取得(リダイレクト判定のため)
        if(isset($_SERVER["HTTP_REFERER"])){
            $host = $_SERVER['HTTP_REFERER'];
        }
        else{
            $host = "";
        }
        
        // 代表PHPファイルからのリダイレクトでない場合はNG
        $ACRCM_representative_file_name = "/common/common_change_password_form.php";
        if(!stristr($host,$ACRCM_representative_file_name)){
            // アクセスログ出力(リダイレクト判定NG)
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190093"));
            
            // 不正操作によるアクセス警告画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20310201);
            exit();
        }
        
        if( $pwc_by_user_forbidden === '1' ){
            // WARNING:ILLEGAL_ACCESS, TO CHANGE PASSWORD BY GENERIC USER IS NOT FORBIDDEN.
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190099"));
            
            // 不正操作によるアクセス警告画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20310202);
            exit();
        }
        
        // ----■ログイン成功後に表示させたいメニューのＩＤが、リクエストのGETクエリーに含まれているかをチェックする。
        if( isset($_GET['no']) ){
            $req_menu_id = $_GET['no'];
        }
        // ■ログイン成功後に表示させたいメニューのＩＤが、リクエストのGETクエリーに含まれているかをチェックする。----
        
        $ASJTM_grp_id = "";

        // メニューIDがクエリーで与えられているか判定
        if( !isset($req_menu_id) ){
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190094"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20310101);
            exit();
        }
        else if( !is_numeric($req_menu_id) ){

            if( isset($_GET['grp']) ){
                $ASJTM_id = "";
                $ASJTM_grp_id = sprintf("%010d", $_GET['grp']);
            }
            else{
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190095"));

                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(400,20310102);
                exit();
            }

        }
        else{
            $ASJTM_id = addslashes($req_menu_id);
        }
        
        require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");
        
        $auth = null;
        saLoginExecute($auth, $objDBCA, $ACRCM_id, false);

        // 変数定義
        $err_msg = '';
        
        if($auth->checkAuth()){
            //----ログインしている状態
            $strRawUsername = $auth->getUsername();
            //ログインしている状態----
        }
        else{
            //----ログインしていない状態
            
            $tmpBoolSpecialSetting = false;
            if( isset($arySYSCON['PW_CHG_FROM_OTHER']) ){
                if( $arySYSCON['PW_CHG_FROM_OTHER'] == '1' ){
                    $tmpBoolSpecialSetting = true;
                }
            }
            if( $tmpBoolSpecialSetting === false ){
                web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190097"));

                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(400,20310103);
                exit();
            }
            unset($tmpBoolSpecialSetting);
            
            if( empty($_POST['username']) ){
                //$err_msg .= '・ログインIDが設定されていません。<br>';
                $err_msg .= "{$objMTS->getSomeMessage("ITAWDCH-MNU-1190010")}<br>";
            }
            else{
                $strRawUsername = $_POST['username'];
            }
            //ログインしている状態----
        }
        
        // 各種事前判定

        if( empty($_POST['old_password']) ){
            //$err_msg .= '・旧パスワードが設定されていません。<br>';
            $err_msg .= "{$objMTS->getSomeMessage("ITAWDCH-MNU-1190011")}<br>";
        }
        if( empty($_POST['new_password']) ){
            //$err_msg .= '・新パスワードが設定されていません。<br>';
            $err_msg .= "{$objMTS->getSomeMessage("ITAWDCH-MNU-1190012")}<br>";
        }
        if( empty($_POST['new_password_2']) ){
            //$err_msg .= '・新パスワード(再入力)が設定されていません。<br>';
            $err_msg .= "{$objMTS->getSomeMessage("ITAWDCH-MNU-1190013")}<br>";
        }
        if( empty($err_msg) && $_POST['new_password'] != $_POST['new_password_2'] ){
            //$err_msg .= '新パスワードと新パスワード(再入力)が一致しません。<br>';
            $err_msg .= "{$objMTS->getSomeMessage("ITAWDCH-MNU-1190014")}<br>";
        }
        if( empty($err_msg) ){
            $err_msg = saLoginAccountUpdate($objDBCA, $strRawUsername, $_POST['old_password'], $_POST['new_password']);
        }
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }

    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");

    $strErrShowBody = "";
    $strActionUrl = "{$scheme_n_authority}/common/common_auth.php?login&grp={$ASJTM_grp_id}&no={$ASJTM_id}";
    if( empty($err_msg) ){
        //----パスワード変更に成功した
        $strErrShowBody .= "<p>{$objMTS->getSomeMessage("ITAWDCH-MNU-1190016")}</p><br>";
        //パスワード変更に成功した----
    }
    else{
        //----パスワード変更に失敗した
        $strErrShowBody .= $err_msg;
        if($auth->checkAuth()){
            //----ログインしている場合
            $strActionUrl = "{$scheme_n_authority}/common/common_change_password_form.php?grp={$ASJTM_grp_id}&no={$ASJTM_id}";
            //ログインしている場合----
        }
        else{
            //----ログインしていない場合
            $strActionUrl = "{$scheme_n_authority}/common/common_change_password_form.php?grp={$ASJTM_grp_id}&no={$ASJTM_id}";
            //ログインしていない場合----
        }
        $strExpiry = "";
        if( isset($_POST['expiry'] )){
            $strExpiry = $_POST['expiry'];
            switch($_POST['expiry']){
                case "0": case "1": break;
                default: $strExpiry = ""; break;
            }
        }
        $strDifferBody1 = "<input type=\"hidden\" name=\"expiry\" value=\"{$strExpiry}\"> ";
        //パスワード変更に失敗した----
    }

    //2019/01/11 javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_ita_icon_png=filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");

    print 
<<< EOD

    </head>
    <body id="INDEX">
        <div class="wholecontainer">
            <div id="PAGETOP">
                <!--================-->
                <!--　　ヘッダー　　-->
                <!--================-->
                <div id="HEADER">
                    <div style="width:190px; height:70px; float:left; display:flex">
                        <img src="{$scheme_n_authority}/common/imgs/ita_icon.png?{$timeStamp_ita_icon_png}" style="margin-top:7px; height:48px;">
                        <div class="ita_name">IT<span style="margin-left:5px;"></span>Automation</div>
                    </div>
                    <ul id="PAN"><li>index</li></ul>
                </div>
                <hr>
                <!--================-->
                <!--　　記事部分　　-->
                <!--================-->
                <div id="KIZI">
                    <h2>
                        {$objMTS->getSomeMessage("ITAWDCH-MNU-1190015")}
                    </h2>
                    <div class="text">
                        {$strErrShowBody}
                        <form method="POST" name="change_pw_form" action="{$strActionUrl}">
                            <input type="submit" name="submit" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1190017")}">
                            {$strDifferBody1}
                        </form>
                    </div>
EOD;
    
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
