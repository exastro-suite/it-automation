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
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }

    if( $pwc_by_user_forbidden === '1' ){
        // WARNING:ILLEGAL_ACCESS, TO CHAGE PASSWORD BY GENERIC USER IS NOT FORBIDDEN.
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190098"));
        
        // 不正操作によるアクセス警告画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,20410201);
        exit();
    }

    // ----■ログイン成功後に表示させたいメニューのＩＤが、リクエストのGETクエリーに含まれているかをチェックする。
    if( isset($_GET['no']) ){
        $req_menu_id = $_GET['no'];
    }

    $ASJTM_grp_id = "";

    // ■ログイン成功後に表示させたいメニューのＩＤが、リクエストのGETクエリーに含まれているかをチェックする。----

    if( !isset($req_menu_id) ){
        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190091"));
        
        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,20410101);
        exit();
    }
    else if( !is_numeric($req_menu_id) ){

        if( isset($_GET['grp']) ){
            $ASJTM_id = "";
            $ASJTM_grp_id = sprintf("%010d", $_GET['grp']);
        }
        else{
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190092"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20410102);
            exit();
        }
    }
    else{
        $ASJTM_id = addslashes($req_menu_id);
    }
    
    $boolChangeForced = false;
    $strExpiry = "";
    if( isset($_POST['expiry'] )){
        $strExpiry = $_POST['expiry'];
        switch($_POST['expiry']){
            case "0":
                $boolChangeForced = true;
                $strReasonMessageBody  = "";
                //$strReasonMessageBody .= "<span class=\"generalWarningMsg\"><br>初期パスワードのまま変更されていません。<br>新しいパスワードに変更して下さい。<br><br></span>";
                $strReasonMessageBody .= "<span class=\"generalWarningMsg\"><br>{$objMTS->getSomeMessage("ITAWDCH-MNU-1190001")}<br><br></span>";
                break;
            case "1":
                $boolChangeForced = true;
                $strReasonMessageBody  = "";
                //$strReasonMessageBody .= "<span class=\"generalWarningMsg\"><br>パスワードの有効期限が切れました。<br>新しいパスワードに変更して下さい。<br><br></span>";
                $strReasonMessageBody .= "<span class=\"generalWarningMsg\"><br>{$objMTS->getSomeMessage("ITAWDCH-MNU-1190002")}<br><br></span>";
                break;
            default:
                $strExpiry = "";
                $strReasonMessageBody = "";
                break;
        }
    }
    else{
        $strReasonMessageBody = "";
    }
    
    $strUsername = "";
    if( isset($_POST['username'] )){
        $strUsername = $_POST['username'];
    }
    
    require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");
    
    $auth = null;
    saLoginExecute($auth, $objDBCA, $ACRCM_id, false);
    
    $strDifferBody1 = "";
    $strDifferBody2 = "";
    // ---- ■リクエストに紐付くセッションが、ログイン状態かをチェックする。
    if($auth->checkAuth()){
        //----ログインしている状態
        $strUsername = $auth->getUsername();
        
        $strUsernameLineBody = $strUsername;
        
        if( $boolChangeForced === true ){
            //----ログアウトボタン(パスワード強制状態)
            $strDifferBody2 =
<<< EOD

                        <form method="POST" class="inputUserInfoForm" action="{$scheme_n_authority}/common/common_auth.php?grp={$ASJTM_grp_id}&no={$ASJTM_id}" >
                        <input id="logoutTryExecute" class="changePwGateSubmitElement abortTryExecute" type="submit" name="logout" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1190018")}">
                        </form>
EOD;
            //ログアウトボタン(パスワード強制状態)----
        }else{
            //----戻るボタン(パスワード任意変更状態)
            $strDifferBody2 =
<<< EOD

                        <form method="POST" class="inputUserInfoForm" action="{$scheme_n_authority}/common/common_auth.php?grp={$ASJTM_grp_id}&no={$ASJTM_id}" >
                        <input id="returnTryExecute" class="changePwGateSubmitElement abortTryExecute" type="submit" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1190019")}">
                        </form>
EOD;
            //戻るボタン(パスワード任意変更状態)---
        }
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
            //$FREE_LOG = 'ERROR:UNEXPECTED, DETAIL:CHANGE OTHER USER PASSWORD IS NOT PERMITTED END';
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190096"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20410103);
            exit();
        }
        
        unset($tmpBoolSpecialSetting);
        
        $strUsernameLineBody = "<input id=\"inputUserId\" class=\"inputUserId\" type=\"text\" name=\"username\" value=\"{$strUsername}\" />";
        
        $strDifferBody2 = 
<<< EOD

                        <form method="POST" class="inputUserInfoForm" method="POST" name="change_pw_form" action="{$scheme_n_authority}/common/common_auth.php?login&grp={$ASJTM_grp_id}&no={$ASJTM_id}">
                        <input type="hidden" name="expiry" value="{$strExpiry}">
                        <input id="jumpToLoginGateTryExecute" class="changePwGateSubmitElement abortTryExecute" type="submit" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1190009")}">
                        </form>
EOD;
        
        //ログインしていない状態----
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");

    // サイト個別のHTMLステートメント
    
    //2019/01/11 javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_common_change_password_00_javascript_js=filemtime("$root_dir_path/webroot/common/javascripts/common_change_password_00_javascript.js");
    $timeStamp_ita_icon_png=filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");
    
    print 
<<< EOD

        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_change_password_00_javascript.js?{$timeStamp_common_change_password_00_javascript_js}"></script>
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
                <div id="gateChangePw">
                    <h2>
                        {$objMTS->getSomeMessage("ITAWDCH-MNU-1190003")}
                    </h2>
                    <div class="text">
                        <div id="gateChangePwContainerHeader" class="gateContainer">
                            {$strReasonMessageBody}
                        </div>
                        <div id="gateChangePwContainerBody" class="gateContainer">
                            <form id="gateChangePwForm" class="inputUserInfoForm" method="POST" name="change_pw_form" action="{$scheme_n_authority}/common/common_change_password_do.php?grp={$ASJTM_grp_id}&no={$ASJTM_id}">
                                <table id="gateChangePwTable" class="headerLeftTable inputItemTable" border="0">
                                    <tr>
                                        <th class="inputItemExplain">{$objMTS->getSomeMessage("ITAWDCH-MNU-1190004")}</th>
                                        <td class="inputItemWrapper">{$strUsernameLineBody}</td>
                                    </tr>
                                    <tr>
                                        <th class="inputItemExplain">{$objMTS->getSomeMessage("ITAWDCH-MNU-1190005")}</th>
                                        <td class="inputItemWrapper"><input id="oldPassword" class="inputUserPw" type="password" name="old_password" /></td>
                                    </tr>
                                    <tr>
                                        <th class="inputItemExplain">{$objMTS->getSomeMessage("ITAWDCH-MNU-1190006")}</th>
                                        <td class="inputItemWrapper"><input id="newPassword1" class="inputUserPw" type="password" name="new_password" /></td>
                                    </tr>
                                    <tr>
                                        <th class="inputItemExplain">{$objMTS->getSomeMessage("ITAWDCH-MNU-1190007")}</th>
                                        <td class="inputItemWrapper"><input id="newPassword2" class="inputUserPw" type="password" name="new_password_2" /></td>
                                    </tr>
                                </table>
                                <input id="changePwTryExecute" class="changePwGateSubmitElement tryExecute" type="submit" name="submit" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1190008")}" />
                                <input type="hidden" name="expiry" value="{$strExpiry}">
                                {$strDifferBody1}
                            </form>
                        </div>
                        <div id="gateChangePwContainerFooter" class="gateContainer">
                            {$strDifferBody2}
                        </div>
                    </div>
EOD;
    
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
