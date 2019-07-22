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
    //    ・ModuleDistictCode(103)
    //   $ASJTM：Auth-Success-Jump-Target-Menu
    //   configである以上、読み込み時にステートメントが実行させないこと
    //
    //////////////////////////////////////////////////////////////////////

    // ----関数（ログインフォームの表示）の定義・宣言
    function saLoginFunction($username, $status, &$auth){
        global $objMTS, $arySYSCON, $g;

        $strLoginFormHeadBody = "";
        $strLoginFormTailBody = "";

        // ルートディレクトリを取得
        if ( empty($root_dir_path) ){
            $root_dir_path = getApplicationRootDirPath();
        }
        $scheme_n_authority = getSchemeNAuthority();

        // クエリーのメニューIDを取得
        if( isset($_GET['no']) ){
            $ASJTM_id = addslashes($_GET['no']);
        }

        if( isset($_GET['grp']) ){
            $ASJTM_grp_id = addslashes(sprintf("%010d", $_GET['grp']));
        }

        $strLoginActionCaption = $objMTS->getSomeMessage("ITAWDCH-STD-1001");
        $strLoginIDCaption     = $objMTS->getSomeMessage("ITAWDCH-STD-1002");
        $strLoginPWCaption     = $objMTS->getSomeMessage("ITAWDCH-STD-1003");
        $strAnchorInnerToLoginIDList   = $objMTS->getSomeMessage("ITAWDCH-STD-1004");
        $strAnchorInnerToLoginPWUpdate = $objMTS->getSomeMessage("ITAWDCH-STD-1005");

        // 認証ステータスをチェック
        if( isset($_POST['status'])=== true ){
            $check_status = addslashes($_POST['status']);
        }
        else{
            $check_status = $status;
        }

        // AD連携(外部認証) ADレプリ連携未了ユーザーのログイン時システムエラー、再認証画面遷移対応
        // ログイン認証に成功したが、直後にユーザー情報取得エラーを起こしているユーザーを警告表示する
        // ログイン認証に成功しているので下の$check_status定数とは競合しない
        if( isset($_POST['trial_username'])=== true ){
            $NoticeTrialUsername = addslashes($_POST['trial_username']);
            $strLoginFormHeadBody .= "<span class=\"loginGateWarningMsg\">{$objMTS->getSomeMessage("ITAWDCH-ERR-104", $NoticeTrialUsername )}</span>\n";
        }
        // -----AD連携(外部認証) ADレプリ連携未了ユーザーのログイン時システムエラー、再認証画面遷移対応

        $strGateUrl = htmlspecialchars($_SERVER['PHP_SELF']);

        switch( $check_status ){
            case AUTH_WRONG_LOGIN:
                $strLoginFormHeadBody .= "<span class=\"loginGateWarningMsg\">{$objMTS->getSomeMessage("ITAWDCH-ERR-4")}</span>";
                break;
            case AUTH_IDLED:
                $strLoginFormHeadBody .= "<span class=\"loginGateWarningMsg\">{$objMTS->getSomeMessage("ITAWDCH-ERR-5")}</span>";
                break;
            case AUTH_EXPIRED:
                $strLoginFormHeadBody .= "<span class=\"loginGateWarningMsg\">{$objMTS->getSomeMessage("ITAWDCH-ERR-6")}</span>";
                break;
        }

        // 登録されているログインID一覧画面へのリンク
        if( isset($arySYSCON['IP_ADDRESS_LIST']) ){
            if( $arySYSCON['IP_ADDRESS_LIST'] == '1' ){
                $strLoginFormTailBody .= "<br><p><a href=\"{$scheme_n_authority}/common/common_account_list_browse.php?no={$ASJTM_id}\">".$strAnchorInnerToLoginIDList."</a>\n";
            }
        }

        // パスワード変更画面へのリンク
        if( isset($arySYSCON['PW_CHG_FROM_OTHER']) ){
            if( $arySYSCON['PW_CHG_FROM_OTHER'] == '1' ){
                $strLoginFormTailBody .= "<br><a href=\"{$scheme_n_authority}/common/common_change_password_form.php?no={$ASJTM_id}\">".$strAnchorInnerToLoginPWUpdate."</a></p>\n";
            }
        }

        ob_start();
        // フォームを読み込み
        // AD連携(外部認証) saLoginFunction連続呼出しによる入力フォーム消失対応
        require( $root_dir_path . "/libs/webcommonlibs/web_loginform.php");
        $strLoginFormBody = ob_get_contents();
        ob_end_clean();

        $g['tmpBuffer_AUTH'] = $strLoginFormBody;

    }
    // 関数（ログインフォームの表示）の定義・宣言----

    function saLogoutFunction($username,&$auth){
        $auth->username_on_logout = $username;
    }

    function saLoginAccountUpdate($objDBCA, $strRawUsername, $strRawOldPassword, $strRawNewPassword){
        // パスワード変更時に呼び出される

        global $objMTS,$pwl_expiry,$pwl_threshold,$pwl_countmax,$pw_reuse_forbid;

        $intControlDebugLevel01 = 200;
        $strCheckTriggerName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);
        
        $boolContinueOnExcept = false;
        
        $retStrBody = "";
        
        $tmpArrayRet = checkLoginRequestForUserAuth($strRawUsername, $strRawOldPassword, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA);
        $checkStatus = $tmpArrayRet[0]['CheckResultType'];
        if( $tmpArrayRet[1] !== null ){
            $strErrMsgBody = $tmpArrayRet[3];
        }
        else{
        }
        $strFixedId = $tmpArrayRet[0]['UserID'];
        unset($tmpArrayRet);

        switch($checkStatus){
            case "login_success":
                break;
            case "id_error":
            case "id_error_on_syntax":
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-7");
                break;
            case "pw_error":
            case "pw_error_on_syntax":
                // ----ログインさせないためにリストをクリア
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-8");
                // ログインさせないためにリストをクリア----
                break;
            case "locked_pw_error":
            case "locked_pw_match":
                // アクセスログ出力(ロック)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-11"),array($checkStatus,$strCheckTriggerName));
                
                // アカウントロック画面にリダイレクト
                //insideRedirectCodePrint("/common/common_account_locked_error.php",0);
                webRequestForceQuitFromEveryWhere(403,10310601);
                exit();
                break;
            default:
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-12",array($checkStatus,$strCheckTriggerName,$strErrMsgBody)));
                
                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,10310101);
                exit();
                break;
        }
        unset($tmpArrayRet);

        $aryValiUserPw = saLoginTextValidateCheck($strRawNewPassword,'/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/',8,30,false);
        if( $aryValiUserPw[0] === false ){
            $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-13");
        }
        unset($aryValiUserPw);

        //IDとパスワードの合致をチェックする----

        //----現在のパスワードと、新しいパスワードを比較する
        if( empty($retStrBody) ){
            if( strlen($strRawOldPassword) === strlen($strRawNewPassword) && $strRawOldPassword === $strRawNewPassword ){
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-10");
            }
        }
        //現在のパスワードと、新しいパスワードを比較する----

        //----履歴を見て、一度登録されたパスワードかをチェックする
        if( empty($retStrBody) ){
            
            $tempArrayRet = checkRequirementAsNewUserPassword($strRawUsername, md5($strRawNewPassword), $pw_reuse_forbid ,$objDBCA);
            if( $tempArrayRet[1] !== null ){
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-15");
                
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-16",array($tempArrayRet[3],$strCheckTriggerName)));

                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,10310102);
            }
            if( $tempArrayRet[0]['Requirement'] === true ){
            }
            else{
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-14");
            }
        }
        unset($tempArrayRet);
        //履歴を見て、一度登録されたパスワードかをチェックする----

        if( empty($retStrBody) ){
            $tempArrayRet = updateUserPasswordByUserSelf($strFixedId,$strRawOldPassword,$strRawNewPassword,$objDBCA);
            if( $tempArrayRet[1] !== null ){
                if( $tempArrayRet[1] == 504 ){
                    // 他セッションから、すでに廃止されていた場合
                    $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-3");
                }
                else{
                    // アクセスログ出力(想定外エラー)
                    $strTmpStrBody = $tempArrayRet[3];

                    // 想定外エラー通知画面にリダイレクト
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-16",array($strTmpStrBody,$strCheckTriggerName)));
                }
            }
            unset($tempArrayRet);
        }
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);
        return $retStrBody;
    }

    function saLoginExecute(&$objAuth, &$objDBCA, $ACRCM_id, $boolFromOutToIn=true){
        // session_start(); // AD連携処理で$_SESSION変数取得のため追加

        //boolFromOutToInを、falseにすると、未ログインからログインへの変更のための手続きを実行しない。
        global $objMTS, $arySYSCON , $pwl_expiry, $pwl_threshold, $pwl_countmax, $aryExternalAuthSettings, $boolLocalAuthUser, $strExternalAuthSettingsFilename;

        $intControlDebugLevel01 = 200;
        $strCheckTriggerName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);

        // ルートディレクトリを取得
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }


        $scheme_n_authority = getSchemeNAuthority();

        // 関数（ログインフォームの表示）の定義・宣言----

        // ----■ログイン処理を行うフラグが、リクエストのGETクエリーに含まれているかをチェックする。
        if( isset($_GET['login']) ){
             $optional = true;

             if( $boolFromOutToIn === false ){
                 $optional = false;
             }
        }
        else{
            $optional = false;
        }
        // ■ログイン処理を行うフラグが、リクエストのGETクエリーに含まれているかをチェックする。----

        // 接続必須情報の初期化
        $account_list = array();
        $strUsername = "";
        $strBinddn = "";
        $strBindpw = "";

        if( $optional === true ){
            // ----GETに['login']が設定されていた場合
            $strUsername = '';
            if( array_key_exists('username', $_POST ) === true ){
                $strUsername = $_POST['username'];
            }
            $strUserPass = '';
            if( array_key_exists('password', $_POST ) === true ){
                $strUserPass = $_POST['password'];
            }
            // GETに['login']が設定されていた場合----

            // -----入力されたユーザーが内部認証/外部認証ユーザーかを判別する
            $tmpArrayRet = checkLoginRequestForUserAuthInorExt($strUsername, $objDBCA);
            if( $tmpArrayRet[1] !== null ){
                $strErrMsgBody = $tmpArrayRet[3];
            }
            else{
                $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType'];
            }
            // 入力されたユーザーが内部認証/外部認証ユーザーかを判別する-----ここまで
            unset($tmpArrayRet);

            // 外部認証ユーザーのユーザー名/パスワードのチェック処理 -----
            // 外部認証コンフィグファイルが存在して and 認証対象ユーザー名が内部認証を強制されるユーザに該当しない場合 =  "外部認証"
            if( enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false )
            {
                $tmpArrayRet = checkLoginRequestForUserLdapAuth($strUsername, $strUserPass, $objDBCA);
                $checkStatus = $tmpArrayRet[0]['CheckResultType'];
                if( $tmpArrayRet[1] !== null ){
                    $strErrMsgBody = $tmpArrayRet[3];
                }
                else{
                    $account_list = $tmpArrayRet[0]['PasswordPerUsername'];
                }
                unset($tmpArrayRet);

            }
            // ----- 外部認証ユーザーのユーザー名/パスワードのチェック処理 ここまで
            else
            // 内部認証ユーザー のユーザー名/パスワードのチェック処理 -----
            {
                $tmpArrayRet = checkLoginRequestForUserAuth($strUsername, $strUserPass, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA);
                $checkStatus = $tmpArrayRet[0]['CheckResultType'];
                if( $tmpArrayRet[1] !== null ){
                    $strErrMsgBody = $tmpArrayRet[3];
                }
                else{
                    $account_list = $tmpArrayRet[0]['PasswordPerUsername'];
                }
                unset($tmpArrayRet);

            }
            // -----内部認証ユーザー のユーザー名/パスワードのチェック処理 ここまで

            switch($checkStatus){
                case "login_success":
                    break;
                case "id_error":
                case "id_error_on_syntax":
                case "pw_error":
                case "pw_error_on_syntax":
                    // ----ログインさせないためにリストをクリア
                    $account_list = array();
                    // ログインさせないためにリストをクリア----
                    break;
                case "locked_pw_error":
                case "locked_pw_match":
                    // アクセスログ出力(ロック)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-11"),array($checkStatus,$strCheckTriggerName));

                    // アカウントロック画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(403,10310602);
                    exit();
                    break;
                default:
                    // アクセスログ出力(想定外エラー)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-12",array($checkStatus,$strCheckTriggerName,$strErrMsgBody)));
                    // 想定外エラー通知画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(500,10310103);
                    exit();
                    break;
            }
            // ----GETに['login']が設定されていた場合
        }
        // ----データベース接続オプションの設定
        $options = array(
            'enableLogging'=>false,
            'cryptType'=>'md5',
            // 'sessionName'=>"NEC_OMCS_SESSION",
            'sessionName'=>"ITA_SESSION",
            'users'=> $account_list
        );
        // データベース接続オプションの設定----

        // ----Authインスタンスの作成
        $objAuth = new AuthForWDBC("Array", $options, "saLoginFunction", $optional);
        // Authインスタンスの作成----

        $objAuth->setLogoutCallback('saLogoutFunction');

        // ----設定値を取得する
        if( isset($arySYSCON['AUTH_IDLE_EXPIRY']) ){
            $VOSCT_SesIdle = intval($arySYSCON['AUTH_IDLE_EXPIRY']);
        }
        if( isset($arySYSCON['AUTH_SES_EXPIRY']) ){
            $VOSCT_SesExpry = intval($arySYSCON['AUTH_SES_EXPIRY']);
        }
        // 設定値を取得する----

        if( isset($VOSCT_SesIdle)===false || $VOSCT_SesIdle < 0 ){
            $VOSCT_SesIdle = 28800; // デフォルト値＜アイドル時間を8時間(28800秒)に設定＞
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-44"));
        }
        if( isset($VOSCT_SesExpry)===false || $VOSCT_SesExpry < 0 ){
            $VOSCT_SesExpry = 86400; // デフォルト値＜有効期限を24時間(86400秒)に設定＞
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-45"));
        }
        if( $VOSCT_SesExpry < $VOSCT_SesIdle ){
            $VOSCT_SesIdle = 28800; // デフォルト値＜アイドル時間を8時間(28800秒)に設定＞
            $VOSCT_SesExpry = 86400; // デフォルト値＜有効期限を24時間(86400秒)に設定＞
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-46"));
        }

        // ----Authのパラメータ設定
        $objAuth->setIdle($VOSCT_SesIdle);
        $objAuth->setExpire($VOSCT_SesExpry);
        // Authのパラメータ設定----

        // ----認証プロセスの開始
        $objAuth->start();
        // 認証プロセスの開始----


        unset($strUsername);
        unset($strUserPass);

        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);
        return true;
    }

    require_once ("Auth/Auth.php");

    require_once (dirname(__FILE__)."/web_functions_for_user_auth.php");

    class AuthForWDBC extends Auth
    {
        var $username_on_logout;
    }
