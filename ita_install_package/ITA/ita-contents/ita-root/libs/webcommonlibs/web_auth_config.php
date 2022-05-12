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
    function saLoginFunction($status = null) {
        global $objMTS, $arySYSCON, $g;

        $strLoginFormHeadBody = "";
        $strLoginFormTailBody = "";

        // ルートディレクトリを取得
        if($g != null && array_key_exists('root_dir_path', $g)){
            $root_dir_path = $g['root_dir_path'];
        }
        else{
            $root_dir_path = getApplicationRootDirPath();
        }
        $scheme_n_authority = getSchemeNAuthority();

        // クエリーのメニューIDを取得
        if (isset($_GET['no'])) {
            $ASJTM_id = htmlspecialchars(addslashes($_GET['no']), ENT_QUOTES, "UTF-8");
        }

        if (isset($_GET['grp'])) {
            $ASJTM_grp_id = htmlspecialchars(addslashes(sprintf("%010d", $_GET['grp'])), ENT_QUOTES, "UTF-8");
        }

        $strLoginActionCaption = $objMTS->getSomeMessage("ITAWDCH-STD-1001");
        $strLoginIDCaption     = $objMTS->getSomeMessage("ITAWDCH-STD-1002");
        $strLoginPWCaption     = $objMTS->getSomeMessage("ITAWDCH-STD-1003");
        $strAnchorInnerToLoginIDList   = $objMTS->getSomeMessage("ITAWDCH-STD-1004");
        $strAnchorInnerToLoginPWUpdate = $objMTS->getSomeMessage("ITAWDCH-STD-1005");

        // 認証ステータスをチェック
        if (isset($_POST['status'])=== true) {
            $check_status = htmlspecialchars(addslashes($_POST['status']), ENT_QUOTES, "UTF-8");
        } else {
            $check_status = $status;
        }

        // AD連携(外部認証) ADレプリ連携未了ユーザーのログイン時システムエラー、再認証画面遷移対応
        // ログイン認証に成功したが、直後にユーザー情報取得エラーを起こしているユーザーを警告表示する
        // ログイン認証に成功しているので下の$check_status定数とは競合しない
        if (isset($_POST['trial_username'])=== true) {
            $NoticeTrialUsername = htmlspecialchars(addslashes($_POST['trial_username']), ENT_QUOTES, "UTF-8");
            $strLoginFormHeadBody .= "<span class=\"loginGateWarningMsg\">{$objMTS->getSomeMessage("ITAWDCH-ERR-104", $NoticeTrialUsername )}</span>\n";
        }
        // -----AD連携(外部認証) ADレプリ連携未了ユーザーのログイン時システムエラー、再認証画面遷移対応

        $strGateUrl = htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES, "UTF-8");

        switch ($check_status) {
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

        $getCopy = $_GET;
        unset($getCopy['login']);
        $get_parameter = "";
        if("" != http_build_query($getCopy)){
            $get_parameter = "?" . http_build_query($getCopy);
        }
        $get_parameter = str_replace('+', '%20', $get_parameter);

        // 登録されているログインID一覧画面へのリンク
        if (isset($arySYSCON['IP_ADDRESS_LIST'])) {
            if ($arySYSCON['IP_ADDRESS_LIST'] == '1') {
                $strLoginFormTailBody .= "<br><p><a href=\"{$scheme_n_authority}/common/common_account_list_browse.php{$get_parameter}\">".$strAnchorInnerToLoginIDList."</a>\n";
            }
        }

        // パスワード変更画面へのリンク
        if (isset($arySYSCON['PW_CHG_FROM_OTHER'])) {
            if ($arySYSCON['PW_CHG_FROM_OTHER'] == '1') {
                $strLoginFormTailBody .= "<br><a href=\"{$scheme_n_authority}/common/common_change_password_form.php{$get_parameter}\">".$strAnchorInnerToLoginPWUpdate."</a></p>\n";
            }
        }
        // SSO認証用設定の読み込み
        require $root_dir_path."/libs/webcommonlibs/web_functions_for_sso_auth.php";
        $arySsoProviderList = getConfigSsoAuth();

        ob_start();
        // フォームを読み込み
        // AD連携(外部認証) saLoginFunction連続呼出しによる入力フォーム消失対応
        include $root_dir_path . "/libs/webcommonlibs/web_loginform.php";
        $strLoginFormBody = ob_get_contents();
        ob_end_clean();

        $g['tmpBuffer_AUTH'] = $strLoginFormBody;

    }
    // 関数（ログインフォームの表示）の定義・宣言----


    function saLoginAccountUpdate($objDBCA, $strRawUsername, $strRawOldPassword, $strRawNewPassword) {
        // パスワード変更時に呼び出される

        global $objMTS,$pwl_expiry,$pwl_threshold,$pwl_countmax,$pw_reuse_forbid;

        $intControlDebugLevel01 = 200;
        $strCheckTriggerName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);
        
        $boolContinueOnExcept = false;
        
        $retStrBody = "";
        
        $tmpArrayRet = checkLoginRequestForUserAuth($strRawUsername, $strRawOldPassword, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA);
        $checkStatus = $tmpArrayRet[0]['CheckResultType'];
        if ($tmpArrayRet[1] !== null) {
            $strErrMsgBody = $tmpArrayRet[3];
        }
        $strFixedId = $tmpArrayRet[0]['UserID'];
        unset($tmpArrayRet);

        switch ($checkStatus) {
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
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-11", array($checkStatus,$strCheckTriggerName)));
                
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
        if ($aryValiUserPw[0] === false) {
            $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-13");
        }
        unset($aryValiUserPw);

        //IDとパスワードの合致をチェックする----

        //----現在のパスワードと、新しいパスワードを比較する
        if (empty($retStrBody)) {
            if (strlen($strRawOldPassword) === strlen($strRawNewPassword) && $strRawOldPassword === $strRawNewPassword) {
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-10");
            }
        }
        //現在のパスワードと、新しいパスワードを比較する----

        //----履歴を見て、一度登録されたパスワードかをチェックする
        if (empty($retStrBody)) {
            
            $tempArrayRet = checkRequirementAsNewUserPassword($strRawUsername, md5($strRawNewPassword), $pw_reuse_forbid ,$objDBCA);
            if ($tempArrayRet[1] !== null) {
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-15");
                
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-16",array($tempArrayRet[3],$strCheckTriggerName)));

                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,10310102);
            }
            if ($tempArrayRet[0]['Requirement'] !== true) {
                $retStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-14");
            }
        }
        unset($tempArrayRet);
        //履歴を見て、一度登録されたパスワードかをチェックする----

        if (empty($retStrBody)) {
            $tempArrayRet = updateUserPasswordByUserSelf($strFixedId,$strRawOldPassword,$strRawNewPassword,$objDBCA);
            if ($tempArrayRet[1] !== null) {
                if ($tempArrayRet[1] == 504) {
                    // 他セッションから、すでに廃止されていた場合
                    $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-3");
                } else {
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

    function saLoginExecute(&$objAuth, &$objDBCA, $ACRCM_id, $boolFromOutToIn = true) {
        //boolFromOutToInを、falseにすると、未ログインからログインへの変更のための手続きを実行しない。
        global $objMTS, $arySYSCON , $pwl_expiry, $pwl_threshold, $pwl_countmax, $aryExternalAuthSettings, $boolLocalAuthUser, $strExternalAuthSettingsFilename, $objOBCA;

        $intControlDebugLevel01 = 200;
        $strCheckTriggerName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);


        $scheme_n_authority = getSchemeNAuthority();

        // 関数（ログインフォームの表示）の定義・宣言----

        // ----■ログイン処理を行うフラグがリクエストのGETクエリーに含まれているかをチェックする。
        $boolAllowLogin = false;
        if (isset($_GET['login']) && $boolFromOutToIn !== false) {
            $boolAllowLogin = true;
        }
        // ■ログイン処理を行うフラグがリクエストのGETクエリーに含まれているかをチェックする。----

        // ----Authインスタンスの作成
        $objAuth = new Auth();
        // Authインスタンスの作成----

        $objAuth->setSessionName('ITA_SESSION_'.md5(getRequestHost()));
        $objAuth->setAllowLogin($boolAllowLogin);
        $objAuth->setLoginFunction('saLoginAuthentication');
        $objAuth->setLoginFormFunction('saLoginFunction');
        // ----設定値を取得する
        if (isset($arySYSCON['AUTH_IDLE_EXPIRY'])) {
            $VOSCT_SesIdle = intval($arySYSCON['AUTH_IDLE_EXPIRY']);
        }
        if (isset($arySYSCON['AUTH_SES_EXPIRY'])) {
            $VOSCT_SesExpry = intval($arySYSCON['AUTH_SES_EXPIRY']);
        }
        // 設定値を取得する----

        if (isset($VOSCT_SesIdle) === false || $VOSCT_SesIdle < 0) {
            $VOSCT_SesIdle = 3600; // デフォルト値＜アイドル時間を8時間(3600秒)に設定＞
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-44"));
        }
        if (isset($VOSCT_SesExpry) === false || $VOSCT_SesExpry < 0) {
            $VOSCT_SesExpry = 86400; // デフォルト値＜有効期限を24時間(86400秒)に設定＞
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-45"));
        }
        if ($VOSCT_SesExpry < $VOSCT_SesIdle) {
            $VOSCT_SesIdle = 3600; // デフォルト値＜アイドル時間を8時間(3600秒)に設定＞
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

        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strCheckTriggerName)),$intControlDebugLevel01);
        return true;
    }

    function saLoginAuthentication() {
        global $pwl_expiry, $pwl_threshold, $pwl_countmax, $strExternalAuthSettingsFilename, $objDBCA, $strErrMsgBody, $objMTS;

        $strCheckTriggerName = __FUNCTION__;

        // 接続必須情報の初期化
        $account_list = [];
        $strUsername = "";
        $strBinddn = "";
        $strBindpw = "";

        $strUsername = '';
        if (array_key_exists('username', $_POST)) {
            $strUsername = htmlspecialchars($_POST['username'], ENT_QUOTES, "UTF-8");
        }
        $strUserPass = '';
        if (array_key_exists('password', $_POST)) {
            $strUserPass = htmlspecialchars($_POST['password'], ENT_QUOTES, "UTF-8");
        }

        // -----入力されたユーザーが内部認証/外部認証ユーザーかを判別する
        $tmpArrayRet = checkLoginRequestForUserAuthInorExt($strUsername, $objDBCA);
        if ($tmpArrayRet[1] !== null) {
            $strErrMsgBody = $tmpArrayRet[3];
        } else {
            $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType'];
        }
        // 入力されたユーザーが内部認証/外部認証ユーザーかを判別する-----ここまで
        unset($tmpArrayRet);

        // 外部認証ユーザーのユーザー名/パスワードのチェック処理 -----
        // 外部認証コンフィグファイルが存在して and 認証対象ユーザー名が内部認証を強制されるユーザに該当しない場合 =  "外部認証"
        if (enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false) {
            $tmpArrayRet = checkLoginRequestForUserLdapAuth($strUsername, $strUserPass, $objDBCA);
            $checkStatus = $tmpArrayRet[0]['CheckResultType'];
            if ($tmpArrayRet[1] !== null) {
                $strErrMsgBody = $tmpArrayRet[3];
            } else {
                $account_list = $tmpArrayRet[0]['PasswordPerUsername'];
            }
            unset($tmpArrayRet);

            // ----- 外部認証ユーザーのユーザー名/パスワードのチェック処理 ここまで
        } else {
            // 内部認証ユーザー のユーザー名/パスワードのチェック処理 -----
            $tmpArrayRet = checkLoginRequestForUserAuth($strUsername, $strUserPass, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA);
            $checkStatus = $tmpArrayRet[0]['CheckResultType'];
            if ($tmpArrayRet[1] !== null) {
                $strErrMsgBody = $tmpArrayRet[3];
            } else {
                $account_list = $tmpArrayRet[0]['PasswordPerUsername'];
            }
            unset($tmpArrayRet);
        }
        // -----内部認証ユーザー のユーザー名/パスワードのチェック処理 ここまで

        switch ($checkStatus) {
            case "login_success":

                if (enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false) {
                        $objDBCA = new DBConnectAgent();
                        $tmpResult = $objDBCA->connectOpen();
                        $tmpArrayBind = array('USERNAME'=>$strUsername );
                        $sql = "UPDATE A_ACCOUNT_LIST SET LAST_LOGIN_TIME = SYSDATE() WHERE USERNAME = :USERNAME";
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        $objQuery->sqlBind($tmpArrayBind);
                        $r = $objQuery->sqlExecute();
                }
                //csrf対策
                if( $_POST["csrf_token"] != $_SESSION["csrf_token"] ){
                  header("Location: /common/common_forbidden.php");
                  exit();
                } 
                
                break;
            case "id_error":
            case "id_error_on_syntax":
            case "pw_error":
            case "pw_error_on_syntax":
                // ----ログインさせないためにリストをクリア
                $account_list = [];
                // ログインさせないためにリストをクリア----
                break;
            case "locked_pw_error":
            case "locked_pw_match":
                // アクセスログ出力(ロック)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-11", array($checkStatus,$strCheckTriggerName)));

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
        // ----POSTされたusername,passwordとDB or ADからの配列のusername,passwordの比較をする
        // $account_listの配列構造 $account_list = [ 'username' => md5(password) ];
        if (!empty($account_list) && $account_list[$strUsername] === (string)md5($strUserPass)) {
            // id/password認証成功
            return true;
        } else {
            // id/password認証失敗
            return false;
        }
        // POSTされたusername,passwordとDB or ADからの配列のusername,passwordの比較をする----
    }

    require dirname(__FILE__)."/web_auth_class.php";
    require dirname(__FILE__)."/web_functions_for_user_auth.php";
