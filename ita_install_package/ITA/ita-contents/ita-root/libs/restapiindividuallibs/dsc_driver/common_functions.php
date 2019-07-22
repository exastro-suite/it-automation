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
    //  【概要】
    //    RESTAPI実行時共通関数
    //
    //////////////////////////////////////////////////////////////////////
    // 処理結果ステータス
    define("DF_ERR_NONE"            ,"0");
    define("DF_ERR_HTTP_REQ"        ,"1000");  // HTTPパラメータ異常
    define("DF_ERR_SCRAB_DIR"       ,"1001");  // SCRABインストールディレクトリへの移動に失敗
    define("DF_ERR_HTTP_HEDER"      ,"1002");  // HTTPヘッダーに必要な情報がない
    define("DF_ERR_AUTH"            ,"1003");  // アクセスキー認証エラー
    define("DF_ERR_TAR_DEL"         ,"2000");  // Collect Commandで作成したZIPファイルの削除失敗時のrmコマンドの戻り値への加算値

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   アクセスキー認証を行う
    // パラメータ
    //   $ina_ReqHeaderData:        HTTPヘッダー情報
    //   $in_ResultStatusCode:      HTTPレスポンスで通知するレスポンスコード (異常時のみ)
    //   $in_Exception:             HTTPレスポンスで通知するエラーメッセージ (異常時のみ)
    //
    // 戻り値
    //   true: 正常 false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkAuthorizationInfo($ina_ReqHeaderData,&$in_ResultStatusCode,&$in_Exception){
        global   $root_dir_path;

        $strCRLF = "\r\n";

        $in_ResultStatusCode = 0;
        $in_Exception        = "";

        // リクエストで送られてきた情報
        $strHeaderAuthorization     = $ina_ReqHeaderData['Authorization'];
        $strHeaderDate              = $ina_ReqHeaderData['Date'];
        $strRequestURIOnRest        = $_SERVER['PHP_SELF'];

        // サーバー上にある認証情報取得
        $strConfFileOfAccessKeyIdOnRest     = "/confs/restapiconfs/dsc_driver/accesskey.txt";
        // 認証用のアクセスキー
        $strAccessKeyIdOnRest               = file_get_contents($root_dir_path . $strConfFileOfAccessKeyIdOnRest);
        $strAccessKeyIdOnRest               = ky_decrypt($strAccessKeyIdOnRest);

        $strConfFileOfSecretAccessKeyOnRest = "/confs/restapiconfs/dsc_driver/secret_accesskey.txt";
        // 認証用の秘密鍵
        $strSecretAccessKeyOnRest           = file_get_contents($root_dir_path . $strConfFileOfSecretAccessKeyOnRest);

        $strSecretAccessKeyOnRest           = ky_decrypt($strSecretAccessKeyOnRest);

        $aryTempData = explode("SharedKeyLite {$strAccessKeyIdOnRest}:", $strHeaderAuthorization);
        if( count($aryTempData) != 2 ){
            $in_ResultStatusCode = 401;
            $in_Exception        = 'Authorization infomation format error';
            return false;
        }

        $tmpStrStringToSignOnRest = $strHeaderDate . $strCRLF . $strRequestURIOnRest;

        $tmpStrSignatureOnRest = shell_exec( 'echo -e -n "' . $tmpStrStringToSignOnRest .
                                             '" | openssl dgst -sha256 -binary -hmac ' .
                                             $strSecretAccessKeyOnRest .
                                             ' | openssl base64' );

        if( $tmpStrSignatureOnRest!==$aryTempData[1]."\n" ){
            $in_ResultStatusCode = 401;
            $in_Exception        = 'Authorization infomation is not correct';

            unset($tmpStrStringToSignOnRest);
            unset($tmpStrSignatureOnRest);
            return false;
        }

        unset($tmpStrStringToSignOnRest);
        unset($tmpStrSignatureOnRest);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   HTTPヘッダーに必要な情報が設定されているか確認
    // パラメータ
    //   $ina_ReqHeaderData:     HTTPヘッダー情報を格納
    //   $in_ResultStatusCode:   HTTPレスポンスで通知するレスポンスコード (異常時のみ)
    //   $in_Exception:          HTTPレスポンスで通知するエラー詳細       (異常時のみ)
    //
    // 戻り値
    //   true: 正常 false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkRequestHeaderForAuth(&$ina_ReqHeaderData,&$in_ResultStatusCode,&$in_Exception){
        $in_ResultStatusCode = 0;
        $in_Exception        = "";

        //リクエストヘッダ取得
        $ina_ReqHeaderData = getallheaders();

        if( $ina_ReqHeaderData === false ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Request header unknown error';
            return false;
        }
        //----http(s)リクエストヘッダに所定の項目があるかをチェック
        if( array_key_exists('Content-Type', $ina_ReqHeaderData) !== true ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Content-Type] is not exists';
            return false;
        }

        if( array_key_exists('X-UMF-API-Version', $ina_ReqHeaderData) !== true ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[X-UMF-API-Version] is not exists';
            return false;
        }
        if( array_key_exists('Date', $ina_ReqHeaderData) !== true ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Date] is not exists';
            return false;
        }
        if( array_key_exists('Date', $ina_ReqHeaderData) !== true ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Date] is not exists';
            return false;
        }
        if( array_key_exists('Authorization', $ina_ReqHeaderData) !== true ){
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Authorization] is not exists';
            return false;
        }
        return true;
    }
    // 簡易復号ファンクション
    function ky_decrypt($str){
        // グローバル変数宣言
        return base64_decode(str_rot13($str));
    }
    function DebugLogPrint($p1,$p2,$p3){
        global $logfile;

        $tmpVarTimeStamp = time();
        $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);
        $filepointer=fopen(  $logfile, "a");
        flock($filepointer, LOCK_EX);
        fputs($filepointer, "[" . $logtime . "]" . $p1 . ":" . $p2 . ":" . $p3 . "\n" );
        flock($filepointer, LOCK_UN);
        fclose($filepointer);
        unset($tmpVarTimeStamp);
    }

?>
