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

    // admin_auth_config.phpの読み込み
    require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");

    // ログイン状態フラグ初期化
    $login_status_flag = 0;

    // 開発者権限格納用の変数を宣言
    $p_debug_log_developer = 0;

    $auth = null;

    saLoginExecute($auth, $objDBCA, $ACRCM_id, false);
    // ---- ■リクエストに紐付くセッションが、ログイン状態かをチェックする。
    if($auth->checkAuth()){
        // ----ログイン状態である、と判定された場合

        // ログイン状態フラグを"ログイン中"とする
        $login_status_flag = 1;

        // ----■リクエストに紐付くセッションから、ユーザ名を取得する。
        $username = $auth->getUsername();
        // リクエストに紐付くセッションから、ユーザ名を取得する。■----

        // ----■次のもの≪テーブル【アカウント】とセッション.ユーザ名≫を用いて、ユーザの各情報（ユーザＩＤ、ユーザ名(和名)、パスワード最終更新日時）、を取得する。
        $tmpAryRetBody = getUserInfosByUsername($username,$objDBCA);
        if( $tmpAryRetBody[1] !== null ){
            if( $tmpAryRetBody[1] == 502 ){
                // AD連携機能(外部認証) 2018/03/05 認証成功直後にユーザー情報取得でエラーが生じた場合(以下の状況)認証画面へリダイレクトさせる（ユーザーIDつき）-----
                // 説明：ユーザーの各情報を（ユーザＩＤ、ユーザ名(和名)、パスワード最終更新日時）、を取得した際に、対象ユーザの情報が無いか複数以上存在する場合(ユーザーアカウントに紐づくユーザー情報がユニークに検索できなかった。AD連携でレプリ未実行もしくはレプリ実行によるアカウントテーブル異常状態が想定される)

                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-27"));

                $status = $auth->getStatus(); // ログインは成功している

                web_log($objMTS->getSomeMessage("ITAWDCH-STD-51",array($auth->username_on_logout)));

                $auth->logout();  // 現状のログイン状態を破棄(セッションが残るとループする)

                // AD連携認証成功直後のエラーで、認証画面にリダイレクト
                webRequestForceQuitFromEveryWhere(
                        401,
                        10610401,
                        array('InsideRedirectMode'=>1,
                            'MenuID'=>$ACRCM_id,
                            'ValueForPost'=>array('status'=>$status, 'trial_username'=>$username )
                        )
                );
                exit();
                //----- AD連携機能(外部認証) 2018/03/05 認証成功直後にユーザー情報取得でエラーが生じた場合,認証画面へリダイレクトさせる（ユーザーIDつき）
            }
            $tmpErrMsgBody = $tmpAryRetBody[3];
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-26",$tmpErrMsgBody));

            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(500,10610102);
            exit();
        }
        $user_id          = $tmpAryRetBody[0]['UserID'];
        $username_jp      = $tmpAryRetBody[0]['UserDisplayName'];
        $user_pw_l_update = $tmpAryRetBody[0]['PasswordLastUpdateTime'];
        unset($tmpAryRetBody);
        // 次のもの≪テーブル【アカウント】とセッション.ユーザ名≫を用いて、ユーザの各情報（ユーザＩＤ、ユーザ名(和名)、パスワード最終更新日時）、を取得する。■----

        // ----■システム設定情報を用いて開発者権限ロールの設定がされているかを、チェックする。
        if( isset($dev_role_ids)===true ){
            // ----■次のもの≪テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、セッション.ユーザ名≫を用いて、セッション.ユーザ名のユーザが、開発者権限を有するかを、チェックする。

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
                    // アクセスログ出力(想定外エラー)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-28",$tmpAryRetBody[3]));
                    
                    // 想定外エラー通知画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(500,10610103);
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
    // リクエストに紐付くセッションが、ログイン状態かをチェックする。■----

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

    // 権限格納用の変数を宣言
    $privilege = 0;

    // 管理者用EXCELダウンロード権限格納用の変数を宣言
    $p_admin_excel_download = 0;

    // ----■リクエストされたPHPが所属するメニューが、ログイン必須フラグが有効になっているかをチェックする。
    if( $ACRCM_login_nf === "1" ){
        // ----【ログイン必須フラグが有効の場合】

        // ----■リクエストに紐付くセッションが、ログイン状態かをチェックする。

        if(!$auth->checkAuth()){
            // ----要ログインメニューに未ログインでアクセスしている場合

            // アクセスログ出力(未認証NG)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-29"));
            
            $status = $auth->getStatus();
            
            switch( $status ){
                case AUTH_IDLED:
                    web_log($objMTS->getSomeMessage("ITAWDCH-STD-51",array($auth->username_on_logout)));
                    break;
                case AUTH_EXPIRED:
                    web_log($objMTS->getSomeMessage("ITAWDCH-STD-52",array($auth->username_on_logout)));
                    break;
            }
            
            // 未認証の場合は認証画面にリダイレクト
            webRequestForceQuitFromEveryWhere(
                401,
                10610401,
                array('InsideRedirectMode'=>1,
                      'MenuID'=>$ACRCM_id,
                      'MenuGroupID'=>$ACRCM_group_id,
                      'ValueForPost'=>array('status'=>$status)
                     )
            );
            exit();

            //要ログインメニューに未ログインでアクセスしている場合----
        }
        else{
            // ----ログイン状態である、と判定された場合

            $tmpArrayRet = checkLoginRequestForUserAuthInorExt($p_login_name, $objDBCA);
            if( $tmpArrayRet[1] !== null ){
                $strErrMsgBody = $tmpArrayRet[3];
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-12",array($checkStatus,$strCheckTriggerName,$strErrMsgBody)));
                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,10310103);
                exit();
                break;
            }
            else{
                $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType']; // 判別結果
            }
            unset($tmpArrayRet);

            // ----■リクエストされたPHPが代表ページかをチェックする。
            $tempRepresentativeFlag = false;
            if(basename($_SERVER["PHP_SELF"]) === "01_browse.php"){
                $tempRepresentativeFlag = true;
            }
            // ■リクエストされたPHPが代表ページかをチェックする。----

            /*
                外部認証後対応
                説明：AD認証完了後に代表ページがリクエストされた時に以下の処理で内部DBのpassword_expiryを参照し,パスワード有効期限切れや未入力のユーザーは、パスワード更新画面に強制遷移させられる。2018/02/26
            */
            // -----外部認証コンフィグファイルが存在して and 認証対象ユーザー名が外部認証ユーザの場合処理をスキップ
            if( !( enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false ) )
            {
                if( $tempRepresentativeFlag === true ){
                    // ----代表ページへのリクエストの場合
                    require ($root_dir_path . "/libs/webcommonlibs/web_parts_for_password_expiry_null.php");
                    // 代表ページへのリクエストの場合----
                }
                else{
                    // ----代表ではないページへのリクエストの場合            
                    // 代表ではないページへのリクエストの場合----
                }
                unset($tempRepresentativeFlag);
            }// 外部認証コンフィグファイルが存在して and 認証対象ユーザー名が外部認証ユーザの場合処理をスキップ-----


            if($ACRCM_id === ""){
                $privilege = '1';
            }
            else{
                // ----■次のもの≪テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、テーブル【ロールメニュー紐付リスト】、セッション.ユーザ名≫を用いて、セッション.ユーザ名のユーザが保有する、リクエストされたメニューに対する権限を取得する。
                $tmpAryRetBody = getUserPrivilegesForMenuByUsername($ACRCM_id,$username,$objDBCA);
                if( $tmpAryRetBody[1] !== null ){
                    $tmpErrMsgBody = $tmpAryRetBody[3];
                    
                    // アクセスログ出力(想定外エラー)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-30",$tmpErrMsgBody));

                    // 想定外エラー通知画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(500,10610104);
                    exit();
                }
                if( $tmpAryRetBody[0]['rowLength'] < 1 ){
                    // ----何の権限もなかった。
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-31"));

                    // アクセスフィルタ画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(403,10610201);
                    exit();
                    // 何の権限もなかった。----
                }
                $privilege = $tmpAryRetBody[0]['Privilege'];
                unset($tmpAryRetBody);
                // 次のもの≪テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、テーブル【ロールメニュー紐付リスト】、セッション.ユーザ名≫を用いて、セッション.ユーザ名のユーザが保有する、リクエストされたメニューに対する権限を取得する。■----
            }

            // ログイン状態である、と判定された場合----
        }
        // リクエストに紐付くセッションが、ログイン状態かをチェックする。■----

    }
    else{
        // ----ログイン不要メニューの場合

        // ----2：参照のみOK権限
        $privilege = '2';
        // 2：参照のみOK権限----

        // ログイン不要メニューの場合----
    }
    // リクエストされたPHPが所属するメニューが、ログイン必須フラグが有効になっているかをチェックする。■----

    // ----■リクエストされたPHPが所属するメニューが、メンテナンス中かをチェックする
    $boolSeriveOff = false;
    if( isset($ACRCM_serv_status) === true ){
        if( $ACRCM_serv_status == "1" ){
            // ----メンテナンス中である。
            
            // ----原則として見せない
            $boolSeriveOff = true;
            // 原則として見せない----
            
            if($p_debug_log_developer == 1){
                // ----開発者権限を有する
                $boolSeriveOff = false;
                // 開発者権限を有する----
            }
            
            // メンテナンス中である。----
        }
        else{
            
            //error_log("service in....");
            
        }
    }
    // リクエストされたPHPが所属するメニューが、メンテナンス中かをチェックする■----

    if( $boolSeriveOff === true ){
        // アクセスログ出力(開発者によるメンテナンス中へのアクセス)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-32"));

        // 開発者によるメンテナンス画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10610701);
        exit();
    }

    // ----■リクエストに紐付くセッションが、ログイン状態かをチェックする。
    if($auth->checkAuth()){
        // ----ログイン状態である、と判定された場合

        // ----■システム設定情報を用いて管理者用DL権限ロールの設定がされているかを、チェックする。
        if( isset($aed_role_ids)===true ){
            // ----■設定されている。 

            // ----■次のもの≪テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、セッション.ユーザ名≫を用いて、セッション.ユーザ名のユーザが、管理者用DL権限を有するかを、チェックする。
            $tmp_chk_abort = false;
            $tmpAryRole = explode(";",$aed_role_ids);
            foreach($tmpAryRole as $tmpValue){
                if( is_numeric($tmpValue)===false || strlen($tmpValue)==0 ){
                    $tmp_chk_abort = true;
                    break;
                }
            }
            unset($tmpValue);
            unset($tmpAryRole);

            if( $tmp_chk_abort === false ){
                $searchAedRoles = addslashes( str_replace(";" , "," , $aed_role_ids) );
                $tmpAryRetBody = searchOneUserRolesLengthByUsername($username,$searchAedRoles,$objDBCA);
                if( $tmpAryRetBody[1] !== null ){
                    // アクセスログ出力(想定外エラー)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-33",$tmpAryRetBody[3]));
                    
                    // 想定外エラー通知画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(500,10610105);
                    exit();
                }

                // 次のもの≪テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、セッション.ユーザ名≫を用いて、セッション.ユーザ名のユーザが、管理者用DL権限を有するかを、チェックする。■----
                if( 1 <= $tmpAryRetBody[0]['rowLength'] ){
                    // ----管理者用DL権限を有する

                    // ----■管理者用DL権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。
                    $p_admin_excel_download = 1;
                    // 管理者用DL権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。■----

                    // 管理者用DL権限を有する
                }
                unset($tmpAryRetBody);
            }
            unset($tmp_chk_abort);
            unset($aed_role_ids);
            unset($searchAedRoles);

            // 設定されている。 ■----
        }
        // システム設定情報を用いて管理者用DL権限ロールの設定がされているかを、チェックする。■----

        // ログイン状態である、と判定された場合----
    }
    // リクエストに紐付くセッションが、ログイン状態かをチェックする。■----

    unset($boolSeriveOff);

    require ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_request_init.php");

?>
