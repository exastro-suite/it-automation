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
    //    ・管理コンソールのメニュー関連の情報を取得する
    //
    //////////////////////////////////////////////////////////////////////

    // admin_auth_config.phpの読み込み
    require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");

    global $aryExternalAuthSettings, $boolLocalAuthUser; // 外部認証設定変数

    // 開発者権限格納用の変数を宣言
    $p_debug_log_developer = 0;

    //----ここからユーザ認証

    //----BASIC認証
    $tmpArrayReqHeaderRaw = getallheaders();
    $tmpArrayReqHeaderPrepare=array_change_key_case($tmpArrayReqHeaderRaw);
    list($tmpStrReqAuthInfo, $strKeyExists) = isSetInArrayNestThenAssign($tmpArrayReqHeaderPrepare,array('authorization'),"");
    unset($tmpArrayReqHeaderRaw);

    $strReqUsername = "";
    $strReqUserPass = "";
    if( 0 < strlen($tmpStrReqAuthInfo) ){
        $tmpStrReqAuthInfo = base64_decode($tmpStrReqAuthInfo);
        $tmpStrReqAuthInfo = trim($tmpStrReqAuthInfo);

        //----要素数は最大2個で、文字列を分解
        $tmpAryAuthInfo = explode(":",$tmpStrReqAuthInfo,2);
        $strReqUsername = isset($tmpAryAuthInfo[0])?$tmpAryAuthInfo[0]:""; // ユーザー名
        $strReqUserPass = isset($tmpAryAuthInfo[1])?$tmpAryAuthInfo[1]:""; // パスワード
        //要素数は最大2個で、文字列を分解----
    }
    unset($tmpStrReqAuthInfo);
    //BASIC認証----

    // -----RESTから連携されたユーザーID/パスワードが内部認証/外部認証ユーザーかを判別する
    $tmpArrayRet = checkLoginRequestForUserAuthInorExt($strReqUsername, $objDBCA);
    if( $tmpArrayRet[1] !== null ){
        $strErrMsgBody = $tmpArrayRet[3];
    }
    else{
        $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType'];
    }
    // RESTから連携されたユーザーID/パスワードが内部認証/外部認証ユーザーかを判別する-----
    unset($tmpArrayRet);

    // 外部認証コンフィグファイルが存在して && 認証対象ユーザー名が内部認証を強制されるユーザに該当しない場合 =  "外部認証"
    if( enableActiveDirectorySync($strExternalAuthSettingsFilename ) && $boolLocalAuthUser === false )
    {
        // 外部認証用ユーザー名/パスワードバリデーションチェック
        $tmpAryRetBody = checkLoginRequestForUserLdapAuth( $strReqUsername, $strReqUserPass, $objDBCA );
        $boolAuthCheck = $tmpAryRetBody[0]['CorrectRequest'];   // 正常の場合：true
        $tmpCheckStatus = $tmpAryRetBody[0]['CheckResultType']; // 正常の場合："login_success"
        $tmpIntDetailCheck = $tmpAryRetBody[0]['DetailOfCheck'];

        if( $tmpAryRetBody[1] !== null ){
            $strErrMsgBody = $tmpAryRetBody[3];
        }

        unset($tmpAryRetBody);

    }
    else{
        // （Nullバイトチェックも込みで、)アカウントと突き合わせ
        $tmpAryRetBody = checkLoginRequestForUserAuth($strReqUsername, $strReqUserPass, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA);
        $boolAuthCheck = $tmpAryRetBody[0]['CorrectRequest'];
        $tmpCheckStatus = $tmpAryRetBody[0]['CheckResultType'];
        $tmpIntDetailCheck = $tmpAryRetBody[0]['DetailOfCheck'];
        if( $tmpAryRetBody[1] !== null ){
            $tmpStrErrMsgBody = $tmpAryRetBody[3];
        }
        unset($tmpAryRetBody);
    }
    // REST 内部認証処理-----


    switch($tmpCheckStatus){
        case "login_success":
            break;
        case "id_error":
        case "id_error_on_syntax":
        case "pw_error":
        case "pw_error_on_syntax":
            break;
        case "locked_pw_error":
        case "locked_pw_match":
            // アクセスログ出力(ロック)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114001",array($tmpCheckStatus)));

            webRequestForceQuitFromEveryWhere(403,11410601);
            exit();
            break;
            // アクセスログ出力(ロック)
        default:
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114002",array($tmpCheckStatus,$tmpStrErrMsgBody)));

            webRequestForceQuitFromEveryWhere(500,11410101);
            exit();
            break;
            // アクセスログ出力(想定外エラー)
    }
    unset($tmpCheckStatus);
    unset($tmpIntDetailCheck);

    if( $boolAuthCheck === true ){
        // ----ログイン状態である、と判定された場合
        $username = $strReqUsername;

        $tmpAryRetBody = getUserInfosByUsername($username,$objDBCA,$db_model_ch);
        if( $tmpAryRetBody[1] !== null ){
            $tmpStrErrMsgBody = $tmpAryRetBody[3];

            if( $tmpAryRetBody[1] == 502 ){
                // ----同じログインIDのユーザが存在した
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114003"));

                webRequestForceQuitFromEveryWhere(500,11410102);
                // 同じログインIDのユーザが存在した----
            }
            else{
                //----その他の想定外エラー
                // 汎用系メッセージ
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-2001",array($tmpStrErrMsgBody)));

                webRequestForceQuitFromEveryWhere(500,11410103);
                //その他の想定外エラー----
            }
        }
        $user_id          = $tmpAryRetBody[0]['UserID'];
        $username_jp      = $tmpAryRetBody[0]['UserDisplayName'];
        $user_pw_l_update = $tmpAryRetBody[0]['PasswordLastUpdateTime'];

        unset($tmpAryRetBody);
        // ----■システム設定情報を用いて開発者権限ロールの設定がされているかを、チェックする。

        if( isset($dev_role_ids)===true ){
            $tmp_chk_abort = false;
            $tmpAryRole = explode(";",$dev_role_ids);
            foreach($tmpAryRole as $tmpValue){
                if( is_numeric($tmpValue)===false || strlen($tmpValue)==0 ){
                    $tmp_chk_abort = true;
                    break;
                }
            }
            unset($tmpValue);
            unset($tmpAryRole);
            $searchDevRoles = addslashes( str_replace(";" , "," , $dev_role_ids) );
            if( $tmp_chk_abort === false ){
                $tmpAryRetBody = searchOneUserRolesLengthByUsername($username,$searchDevRoles,$objDBCA);
                if( $tmpAryRetBody[1] !== null ){
                    $tmpStrErrMsgBody = $tmpAryRetBody[3];

                    // 汎用系メッセージ
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-2001",array($tmpStrErrMsgBody)));

                    webRequestForceQuitFromEveryWhere(500,11410104);
                    exit();
                }

                if( 1 <= $tmpAryRetBody[0]['rowLength'] ){
                    // ----セッション.ユーザ名のユーザが、開発者権限を有する場合

                    // ----■開発者権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。
                    $p_debug_log_developer = 1;
                    // 開発者権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。----

                    // セッション.ユーザ名のユーザが、開発者権限を有する場合----
                }
                unset($tmpAryRetBody);
            }
            unset($tmp_chk_abort);
            unset($dev_role_ids);
            unset($searchDevRoles);
        }

        // システム設定情報を用いて開発者権限ロールの設定がされているかを、チェックする。■----

        // ログイン状態である、と判定された場合----
    }

    // ----ログイン系情報を変数に格納
    if ( isset($username) ){
        $p_login_id      = $user_id;     //----ユーザID[数字型ID]
        $p_login_name    = $username;    //----ログイン用ID(英数字)
        $p_login_name_jp = $username_jp; //----ユーザ名[和名]＝利用者名(日本語)
        
        $p_login_pw_l_update = $user_pw_l_update;
    }
    else{
        $p_login_id      = "";           //----ユーザID[数字型ID]
        $p_login_name    = "";           //----ログイン用ID(英数字)
        $p_login_name_jp = "";           //----ユーザ名[和名]＝利用者名(日本語)
        
        $p_login_pw_l_update = "";
    }
    // ログイン系情報を変数に格納----

    if( $ACRCM_login_nf === "1" ){
        // ----【ログイン必須フラグが有効の場合】

        if( $boolAuthCheck !== true ){
            // ----要ログインメニューに未ログインでアクセスしている場合

            //----ERROR:UNAUTHORIZED_ACCESS.
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114004"));

            webRequestForceQuitFromEveryWhere(401,11410401);
            exit();

            // 要ログインメニューに未ログインでアクセスしている場合----
        }
        else{
            // ----ログイン状態である、と判定された場合

            // ----パスワードの有効期限を判定
            $tmpAryRetBody = checkLoginPasswordExpiryOut($username,$p_login_pw_l_update,$pass_word_expiry,$objDBCA);
            if( $tmpAryRetBody[1] !== null ){
                $tmpStrErrMsgBody = $tmpAryRetBody[3];

                // 汎用系メッセージ
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-2001",array($tmpStrErrMsgBody)));

                webRequestForceQuitFromEveryWhere(500,11410501);
                exit();
            }
            if( $tmpAryRetBody[0]['ExpiryOut'] === true ){
                //----パスワード有効期限切れ
                //パスワード有効期限切れ----
            }
            unset($tmpAryRetBody);
            // パスワードの有効期限を判定-----

            // ----メニューに対する権限を取得
            $tmpAryRetBody = getUserPrivilegesForMenuByUsername($ACRCM_id,$username,$objDBCA);
            if( $tmpAryRetBody[1] !== null ){
                $tmpStrErrMsgBody = $tmpAryRetBody[3];

                // 汎用系メッセージ
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-2001",array($tmpStrErrMsgBody)));

                webRequestForceQuitFromEveryWhere(500,11410105);
                exit();
            }
            if( $tmpAryRetBody[0]['rowLength'] < 1 ){
                // ----何の権限もなかった。

                //----WARNING:ILLEGAL_ACCESS, DETAIL:USER HAS INSUFFICIENT PRIVILEGE FOR MENU[｛｝].
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114005",array($ACRCM_id)));

                webRequestForceQuitFromEveryWhere(403,11410201);
                exit();
                // 何の権限もなかった。----
            }
            $privilege = $tmpAryRetBody[0]['Privilege'];

            unset($tmpAryRetBody);
            // メニューに対する権限を取得----

            // ログイン状態である、と判定された場合----
        }
        // 【ログイン必須フラグが有効の場合】----
    }else{
        // ----ログイン不要メニューの場合

        // ----2：参照のみOK権限
        $privilege = '2';
        // 2：参照のみOK権限----

        // ログイン不要メニューの場合----
    }

    // ----■リクエストされたPHPが所属するメニューが、メンテナンス中かをチェックする
    $tmpBoolSeriveOff = false;
    if( isset($ACRCM_serv_status) === true ){
        if( $ACRCM_serv_status == "1" ){
            // ----メンテナンス中である。
            
            // ----原則として見せない
            $tmpBoolSeriveOff = true;
            // 原則として見せない----
            
            if($p_debug_log_developer == 1){
                // ----開発者権限を有する
                $tmpBoolSeriveOff = false;
                // 開発者権限を有する----
            }
            
            // メンテナンス中である。----
        }
        else{
            
            //error_log("service in....");
            
        }
    }
    // リクエストされたPHPが所属するメニューが、メンテナンス中かをチェックする■----

    if( $tmpBoolSeriveOff === true ){
        //----開発者によるメンテナンス中へのアクセス)

        //----WARNING:THIS MENU[｛｝] IS LOCKED FOR MAINTENANCE BY DEVELOPER.
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-114006",array($ACRCM_id)));

        webRequestForceQuitFromEveryWhere(503,11410701);
        exit();
        //開発者によるメンテナンス中へのアクセス)----
    }
    unset($tmpBoolSeriveOff);

    require_once("{$g['root_dir_path']}/libs/webcommonlibs/web_parts_for_request_init.php");
?>
