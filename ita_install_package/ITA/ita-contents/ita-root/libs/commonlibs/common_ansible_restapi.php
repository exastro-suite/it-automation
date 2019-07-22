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
    //  【パラメータ】
    //     $Protocol
    //      Type        : string
    //      Description : アクセスプロトコル
    //      Sample      : http
    //     $HostName
    //      Type        : string
    //      Description : 接続先ホスト名
    //      Sample      : 192.168.0.38
    //     $PortNo
    //      Type        : string
    //      Description : 接続先ポート番号
    //      Sample      : 12080
    //     $AccessKeyId
    //      Type        : string
    //      Description : アクセスキーID
    //      Sample      : czQj2T3/7OVCzG8TYnbYi6wbE3syMC3MvNMgKU/IK7M=
    //     $SecretAccessKey
    //      Type        : string
    //      Description : シークレットアクセスキー
    //      Sample      : 0kMbGSokM/fNJtCpdaE/vykGiDvxFDDdEbNsYTMvRMc=
    //     $RequestURI
    //      Type        : string
    //      Description : HTTPアクセスURI
    //      Sample      : /umf/workflow/list
    //     $Method
    //      Type        : string
    //      Description : HTTPアクセスMethod
    //      Sample      : GET
    //     $RequestContents
    //      Type        : array
    //      Description : HTTPリクエスト時にペイロード部に設定する情報
    //                    json_encodeはファンクション内で実施するので、
    //                    呼び出し元はkey⇔value型のシンプルなArrayを準備する
    //      Sample      : array( "key01" => "value01", "key02" => "value02" )
    //
    //  【リターン】
    //      array( "StatusCode"      => HTTPステータスコード,
    //             "ResponsContents" => REST APIのレスポンスコンテンツ(json_encode済) )
    //      ※REST APIアクセス前後の処理で何らかのエラーが発生した場合は下記を返却。
    //        パラメータチェックエラーの場合
    //        ( "StatusCode" => -1, "ResponsContents" => array( "ErrorMessage" => エラー原因 ) )
    //        通信タイムアウトの場合
    //        ( "StatusCode" => -2, "ResponsContents" => array( "ErrorMessage" => "通信タイムアウト" ) )
    //
    //////////////////////////////////////////////////////////////////////
    
    function ansible_restapi_access( $Protocol,
                                     $HostName,
                                     $PortNo,
                                     $AccessKeyId,
                                     $SecretAccessKey,
                                     $RequestURI,
                                     $Method,
                                     $RequestContents ){
        ////////////////////////////////
        // 返却用のArrayを定義        //
        ////////////////////////////////
        $respons_array = array();
        
        ////////////////////////////////
        // パラメータチェック         //
        ////////////////////////////////
        $check_err_flag = 0;
        if( empty( $Protocol ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Protocol is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $HostName ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "HostName is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $PortNo ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "PortNo is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $AccessKeyId ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "AccessKeyId is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $SecretAccessKey ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "SecretAccessKey is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $RequestURI ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "RequestURI is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $Method ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Method is empty" );
            $check_err_flag = 1;
        }
        else if( !is_array( $RequestContents ) ){
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
            $Signature = shell_exec( 'echo -e -n "' . $StringToSign . '" | openssl dgst -sha256 -binary -hmac ' . $SecretAccessKey . ' | openssl base64' );
            
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

            $HttpContext['ssl']['verify_peer']=false;                      
            $HttpContext['ssl']['verify_peer_name']=false;                 

            ////////////////////////////////
            // REST APIアクセス           //
            ////////////////////////////////
            $http_response_header = null;
            $ResponsContents = file_get_contents( $Protocol . "://" . $HostName . ":" . $PortNo . $RequestURI,
                                                   false,
                                                   stream_context_create($HttpContext) );
            
            // JSON形式でコンテンツデータとれない事があるのでコンテンツデータをダンプする。必要に応じて呼び元でこのデータをログに出力する。
            ob_start();
            var_dump($ResponsContents);
            $respons_array['ALLResponsContents'] = "REST API ALL Response:" . ob_get_contents();
            ob_clean();

            ////////////////////////////////
            // 通信結果を判定             //
            ////////////////////////////////
            if( count( $http_response_header ) > 0 ){
                ////////////////////////////////
                // HTTPレスポンスコード取得   //
                ////////////////////////////////
                preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
                $status_code = $matches[1];
                
                ////////////////////////////////
                // 返却用のArrayを編集        //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) $status_code;
                $respons_array['ResponsContents'] = json_decode( $ResponsContents, true );
            }
            else{
                ////////////////////////////////
                // 返却用のArrayを編集        //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) -2;
                $respons_array['ResponsContents'] = array( "ErrorMessage" => "HTTP Socket Timeout" );
            }
        }
        
        ////////////////////////////////
        // 結果を返却                  //
        ////////////////////////////////
        return $respons_array;
    }
?>
