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
    //    DSCドライバ RESTAPI接続関数
    //
    //////////////////////////////////////////////////////////////////////

    function dsc_restapi_access(     $Protocol,
                                     $HostName,
                                     $PortNo,
                                     $AccessKeyId,
                                     $SecretAccessKey,
                                     $RequestURI,
                                     $Method,
                                     $RequestContents ){
        ///////////////////////////
        // 返却用のArrayを定義      //
        ///////////////////////////
        $respons_array = array();

        ///////////////////////////
        // パラメータチェック           //
        ///////////////////////////
        $check_err_flag = 0;
        if( empty( $Protocol ) )      // HTTPSスキームが空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Protocol is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $HostName ) ) // ホスト名が空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "HostName is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $PortNo ) )   // ポート番号(https:443)が空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "PortNo is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $AccessKeyId ) ) // AccessKeyId文字列が空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "AccessKeyId is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $SecretAccessKey ) ) // SecretAccessKey文字列（暗号文)が空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "SecretAccessKey is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $RequestURI ) )      // 呼び先のRESTAPIのホスト部以下のURIが空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "RequestURI is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $Method ) )          // リクエストメソッド(POST)が空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Method is empty" );
            $check_err_flag = 1;
        }
        else if( !is_array( $RequestContents ) ) // RESTAPIに渡すDSC実行用パラメータが空
        {
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "RequestContents is not array" );
            $check_err_flag = 1;
        }

        if( $check_err_flag == 0 ){
            ////////////////////////////////
            // Signature作成              //
            ////////////////////////////////
            $Date = gmdate('D, d M Y H:i:s T', time());
            $CRLF = "\r\n";
            $StringToSign = $Date . $CRLF . $RequestURI;

            //================================================================================
            // OpenSSLによるSignature文字列作成を一時的に無効化
            // 理由：Signature送信先のDSC WindowsServer2012R2において相互認証用暗号文生成処理が使用できないため
            // これは、Linuxのシェル処理と同様の処理をWindowsServer2012R2側で実装できなかったからである。以下の本来実装していたステート。
            //$Signature = shell_exec( 'echo -e -n "' . $StringToSign . '" | openssl dgst -sha256 -binary -hmac ' . $SecretAccessKey . ' | openssl base64' );
            // 代替処理としてITA-DSCサーバ間でPHP crypt関数によるシグネチャーのエンコード・デコード処理を実装した。
            //================================================================================

            // PHP crypt関数によるSignature作成
            $Signature = crypt( $StringToSign, $SecretAccessKey );

            ////////////////////////////////
            // RequestHeader作成          //
            ////////////////////////////////
            $Header = array( "Host: " . $HostName . ":" . $PortNo,
                             "Content-Type: application/json",
                             "X-UMF-API-Version: 2.1",
                             "Date: " . $Date,
                             "Authorization: SharedKeyLite " . $AccessKeyId . ":" . $Signature );

            ////////////////////////////////
            // HTTPコンテキスト作成       //
            ////////////////////////////////
        	$HttpContext = array( "http" => array( "method"        => $Method,
                                                   "header"        => implode("\r\n", $Header),
                                                   "content"       => json_encode($RequestContents, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK ),
                                                   "ignore_errors" => true));

            //=====================================================
            // SSLサーバ証明書の検証を無効化
            //=====================================================
            $HttpContext['ssl']['verify_peer']=false;                      //  *
            $HttpContext['ssl']['verify_peer_name']=false;                 //  *
            //=================================================================================================
            //開発環境で自己署名しているhttps環境において、file_get_contentsすると
            //Warning: file_get_contents(): SSL operation failed with code 1. OpenSSL Error messages:
            // error:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed in xxxxx
            //
            //というエラーが表示されます。
            // 証明書入れてくれよと思うのですが、導入できない時もあります。その場合は、optionを付与することで回避できます。おうば談
            //
            //
            //$url = "https://xxxxxx";
            //$options['ssl']['verify_peer']=false;
            //$options['ssl']['verify_peer_name']=false;
            //$response = file_get_contents($url, false, stream_context_create($options));
            //'ssl'=>array('verify_peer' => false,'verify_peer_name' => false,),
            //=================================================================================================
            ////////////////////////////////
            // REST APIアクセス           //
            ////////////////////////////////
            $http_response_header = null;

            $ResponsContents = file_get_contents( $Protocol . "://" . $HostName . ":" . $PortNo . $RequestURI,
                false,
                stream_context_create($HttpContext) );

            //----------------------------------------------------------------------
            //出力のバッファリングをオンに設定
            //----------------------------------------------------------------------
            ob_start();
            var_dump($ResponsContents);
            $respons_array['ALLResponsContents'] = "REST API ALL Response:" . ob_get_contents();
            //----------------------------------------------------------------------
            //出力バッファをクリア(消去)する
            //----------------------------------------------------------------------
            ob_clean();

            ////////////////////////////////
            // 通信結果を判定               //
            ////////////////////////////////
            if( count( $http_response_header ) > 0 ){
                ////////////////////////////////
                // HTTPレスポンスコード取得          //
                ////////////////////////////////
                preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
                $status_code = $matches[1];

                ////////////////////////////////
                // 返却用のArrayを編集           //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) $status_code;
            	$info = array();
                $info = json_decode( $ResponsContents, true );
                $respons_array['ResponsContents'] = $info;
            }
            else{
                ////////////////////////////////
                // 返却用のArrayを編集           //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) -2;
                $respons_array['ResponsContents'] = array( "ErrorMessage" => "HTTP Socket Timeout" );
            }
        }
        ////////////////////////////////
        // 結果を返却                   //
        ////////////////////////////////
       return $respons_array;
    }
?>
