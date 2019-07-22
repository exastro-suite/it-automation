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
    //    ・標準RestAPIからも呼び出されるので、関数（insideRedirectCodePrint）を直接呼び出さないこと。
    //
    //////////////////////////////////////////////////////////////////////

    require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_get_sysconfig.php");

    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-36",$tmpAryRetBody[3]));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10410101);
        exit();
    }
    $arySYSCON = $tmpAryRetBody[0]['Items'];
    unset($tmpAryRetBody);

    $ip_filter_conf = array_key_exists('IP_FILTER', $arySYSCON)?$arySYSCON['IP_FILTER']:"";

    $pwc_by_user_forbidden = 0;
    if(array_key_exists('PWC_BY_USER_FORBIDDEN', $arySYSCON)){
        $pwc_by_user_forbidden = $arySYSCON['PWC_BY_USER_FORBIDDEN'];
    }

    if(array_key_exists('PASSWORD_EXPIRY', $arySYSCON)){
        $pass_word_expiry = $arySYSCON['PASSWORD_EXPIRY'];
    }
    if(array_key_exists('FORBIDDEN_UPLOAD', $arySYSCON)){
        $forbidden_upload = $arySYSCON['FORBIDDEN_UPLOAD'];
    }
    if(array_key_exists('EVENT_MAIL_SEND', $arySYSCON)){
        $event_mail_send = $arySYSCON['EVENT_MAIL_SEND'];
    }
    if(array_key_exists('AED_ROLE_ID', $arySYSCON)){
        $aed_role_ids = $arySYSCON['AED_ROLE_ID'];
    }
    $pwl_expiry=0;
    if(array_key_exists('PWL_EXPIRY', $arySYSCON)){
        $pwl_expiry = intval($arySYSCON['PWL_EXPIRY']);
    }
    $pwl_threshold=0;
    if(array_key_exists('PWL_THRESHOLD', $arySYSCON)){
        $pwl_threshold = intval($arySYSCON['PWL_THRESHOLD']);
    }
    $pwl_countmax=0;
    if(array_key_exists('PWL_COUNT_MAX', $arySYSCON)){
        $pwl_countmax = intval($arySYSCON['PWL_COUNT_MAX']);
    }
    $pw_reuse_forbid=0;
    if(array_key_exists('PW_REUSE_FORBID', $arySYSCON)){
        $pw_reuse_forbid = intval($arySYSCON['PW_REUSE_FORBID']);
    }

    //----ここから開発ログ系
    if(array_key_exists('DEV_LOG_DIR', $arySYSCON)){
        $dev_log_dir = $arySYSCON['DEV_LOG_DIR'];
        if(checkRiskOfDirTraversal($dev_log_dir)==true ){
            $dev_log_dir = "";
        }
    }
    if(array_key_exists('DEV_LOG_LEVEL', $arySYSCON)){
        $dev_log_level = $arySYSCON['DEV_LOG_LEVEL'];
    }
    if(array_key_exists('DEV_ROLE_ID', $arySYSCON)){
        $dev_role_ids = $arySYSCON['DEV_ROLE_ID'];
    }
    //ここまで開発ログ系----

    //----RedMineチケット1003
    // -----システム設定情報を用いてIPフィルタ機能がONかをチェックする。
    if( $ip_filter_conf === "1" ){
        // ----IPフィルタ機能がONだった。[ホワイトリストとの突き合わせ処理]

        $tmpStrSourceIp = getSourceIPAddress(true);
        $tmpAryRetBody = checkIPByPermittedWhiteList($tmpStrSourceIp,$objDBCA);
        if( $tmpAryRetBody[1] !== null ){
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-39",$tmpAryRetBody[3]));

            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(500,10410102);
            exit();
        }
        else if( $tmpAryRetBody[0]['rowLength'] < 1 ){
            //----登録が確認できなかった

            // アクセスログ出力(部外者アクセスNG)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-38"));

            // アクセスフィルタ画面にリダイレクト
            webRequestForceQuitFromEveryWhere(403,10410301);
            exit();

            //登録が確認できなかった----
        }
        unset($tmpAryRetBody);
        // IPフィルタ機能がONだった。[ホワイトリストとの突き合わせ処理]----
    }
    // フィルタ機能がON(1)の場合はホワイトリストとの突き合わせ処理----

    // 外部認証設定ファイルの有無チェック
    require_once ($root_dir_path . "/libs/backyardlibs/ita_base/activedirectory_synchronization/ExternalAuthSettingsDefinition.php");
    $strExternalAuthSettingsFilename = ExternalAuthSettingsDefinition::getFilePath();
    if( enableActiveDirectorySync($strExternalAuthSettingsFilename) ){
        $aryExternalAuthSettings = array();
        // 外部認証設定ファイルの読み込み
        $errorMessage = "";
        $aryExternalAuthSettings = ExternalAuthSettingsDefinition::parse($strExternalAuthSettingsFilename, $errorMessage);
        if( $aryExternalAuthSettings === false ){
        //-----ユーザー外部認証設定を連想配列形式で取得できず フォーマットが不正であることが疑われる

            // アクセスログ出力(部外者アクセスNG)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-50")); // 外部認証設定パースエラー

            // アクセスフィルタ画面にリダイレクト
            webRequestForceQuitFromEveryWhere(500,00000001); // システムエラー
            exit();

        // ユーザー外部認証設定を連想配列形式で取得できず-----
        }
    }
    // ユーザ外部認証（AD連携）設定取得-----

?>
