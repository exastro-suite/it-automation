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

    //----リクエスト・メソッドを調べる
    $tmpArrayReqHeaderRaw = getallheaders();
    list($strReqMethod  , $tmpBoolKey01Exists) = isSetInArrayNestThenAssign($_SERVER             , array('REQUEST_METHOD'),"");
    // 独自のhttpリクエストヘッダフィールド名なので[X-]を付加
    $tmpArrayReqHeaderPrepare=array_change_key_case($tmpArrayReqHeaderRaw);
    list($strCommand    , $tmpBoolKey02Exists) = isSetInArrayNestThenAssign($tmpArrayReqHeaderPrepare, array('x-command'),"");
    list($strContentType, $tmpBoolKey03Exists) = isSetInArrayNestThenAssign($tmpArrayReqHeaderPrepare, array('content-type'),"");
    unset($tmpArrayReqHeaderRaw);

    $tmpStrJsonString = "";
    if( $tmpBoolKey03Exists === true ){
        if( trim($strContentType) === "application/json" ){
        }
        else{
            // WARNING:ILLEGAL_ACCESS, DETAIL:CONTENT TYPE IS NOT CORRECT.
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115001"));

            webRequestForceQuitFromEveryWhere(400,11510801);
            exit();
        }
    }
    //----ともかくコンテンツは取得してみる
    $tmpStrJsonString = file_get_contents('php:/'.'/input');
    //ともかくコンテンツは取得してみる----
    unset($tmpBoolKey03Exists);

    switch($strReqMethod){
        case "GET":
            if( $ACRCM_login_nf === "1" ){
                // ----【ログイン必須フラグが有効の場合】

                //----認証情報が送信されてきているはずなので、エラー

                // WARNING:ILLEGAL_ACCESS, DETAIL:METHOD(GET) USED FOR NECESSARY LOGIN MENU[｛｝].
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115002",array($ACRCM_id)));

                webRequestForceQuitFromEveryWhere(400,11510802);
                exit();
                //認証情報が送信されてきているはずなので、エラー----

                // 【ログイン必須フラグが有効の場合】----
            }
            if( $tmpBoolKey02Exists === true ){
                // WARNING:ILLEGAL_ACCESS, DETAIL:X-COMMAND SENT FOR GET METHOD CONTENTS.
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115003"));

                webRequestForceQuitFromEveryWhere(400,11510803);
                exit();
            }
            if( 0 < strlen($tmpStrJsonString) ){
                // WARNING:ILLEGAL_ACCESS, DETAIL:JSON DATA SENT FOR GET METHOD CONTENTS.
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115004"));

                webRequestForceQuitFromEveryWhere(400,11510804);
                exit();
            }
            $strCommand = "GET";
            $objJSONOfReceptedData = array();
            break;
        case "POST":
            if( $ACRCM_login_nf !== "1" ){
                //----ログイン不要メニューの場合

                // WARNING:ILLEGAL_ACCESS, DETAIL:METHOD(POST) USED FOR OPENED MENU[｛｝].
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115005",array($ACRCM_id)));

                webRequestForceQuitFromEveryWhere(500,11510101);
                exit();
                //ログイン不要メニューの場合----
            }
            if( $tmpBoolKey02Exists === false ){
                // WARNING:ILLEGAL_ACCESS, DETAIL:METHOD(POST) USED FOR OPENED MENU[｛｝].
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115006"));

                webRequestForceQuitFromEveryWhere(400,11510805);
                exit();
            }
            //----JSONで送られてきたパラメータを取得
            if( 0 < strlen($tmpStrJsonString) ){
                // (コメントのみ追加)もし配列が代入されなかった場合でも、あとのチェックでハンドリングする。
                $objJSONOfReceptedData = @json_decode($tmpStrJsonString, true, 512, 0);
            }
            else{
                $objJSONOfReceptedData = array();
            }
            //JSONで送られてきたパラメータを取得----
            unset($tmpStrJsonString);
            break;
        default:
            //----不正な要求（内容が不正）

            // WARNING:ILLEGAL_ACCESS, DETAIL:UNEXPECTED METHOD SENT FOR REST CONTENT.
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115007"));

            webRequestForceQuitFromEveryWhere(400,11510806);
            exit();
            break;
            //不正な要求（内容が不正）----
    }
    unset($tmpBoolKey01Exists);
    unset($tmpBoolKey02Exists);

    switch($strCommand){
        case "GET":
        case "INFO":
        case "FILTER":
        case "EDIT":
            break;
        default:
            //----不正な要求（内容が不正）

            if( isset($expandRestCommandPerMenu) === false ){
                // WARNING:ILLEGAL_ACCESS, DETAIL:UNEXPECTED X-COMMAND SENT FOR REST CONTENT.
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115008"));

                webRequestForceQuitFromEveryWhere(400,11510807);
                exit();
                break;
                //不正な要求（内容が不正）----
            }

    }
    //リクエスト・メソッドを調べる----

    if( isset($strReqFileNameAgentForRVCheck) === false ){
        //----ファイル名から、バージョンを調べる。
        list($tmpStrReqFile  , $tmpBoolKeyExists      ) = isSetInArrayNestThenAssign($_SERVER             , array('PHP_SELF'),"");
        if( 0 < strlen($tmpStrReqFile ) ){
            $tmpStrReqFile = basename($tmpStrReqFile);
        }
    }
    else{
        $tmpStrReqFile = $strReqFileNameAgentForRVCheck;
    }

    switch($tmpStrReqFile){
        case "07_rest_api_ver1.php":
            $strCalledRestVer = "1";
            break;
        default:
            // WARNING:ILLEGAL_ACCESS, DETAIL:UNEXPECTED REST-API VERSION.
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115009"));

            webRequestForceQuitFromEveryWhere(500,11510102);
            exit();
            break;
    }
    unset($tmpBoolKeyExists);
    unset($tmpStrReqFile);
    //ファイル名から、バージョンを調べる。----
?>
