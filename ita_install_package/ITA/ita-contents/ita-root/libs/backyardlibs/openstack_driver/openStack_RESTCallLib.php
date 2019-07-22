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
//    ・OpenStack RESTAPI呼出処理モジュール
//
/////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // 共通定数ロード             //
    ////////////////////////////////


    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   SCRAB REST API を呼出す
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
    function openStack_restapi_access( $Protocol,
                                   $HostName,
                                   $PortNo,
                                   $RequestURI,
                                   $Method,
                                   $Header,
                                   $RequestContents){
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



        if( $check_err_flag === 0 ){
            ////////////////////////////////
            // HTTPコンテキスト作成       //
            ////////////////////////////////
            if( strlen( $RequestContents ) != 0 ){
                $HttpContext = array( "http" => array( "method"        => $Method,
                                                       "header"        => implode("\r\n", $Header),
                                                       "content"       => $RequestContents,
                                                       "ignore_errors" => true));
            }
            else{
                $HttpContext = array( "http" => array( "method"        => $Method,
                                                       "header"        => implode("\r\n", $Header),
                                                       "ignore_errors" => true));
            }

            ////////////////////////////////
            // REST APIアクセス           //
            ////////////////////////////////
            $http_response_header = null;
    
            $URL = $RequestURI;

            $ResponsContents = file_get_contents( $URL,
                                                  false,
                                                  stream_context_create($HttpContext) );

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
                if($status_code != 200){
                    $respons_array['ResponsContents'] = array( "ErrorMessage" => $ResponsContents );
                }
                else{
                    $respons_array['ResponsContents'] = $ResponsContents;
                }
            }
            else{
                ////////////////////////////////
                // 返却用のArrayを編集        //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) -2;
                $respons_array['ResponsContents'] = array( "ErrorMessage" => "HTTP Socket Timeout" );
            }
        }
        return $respons_array;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   SCRAB REST API を呼出す
    // 
    //  【パラメータ】
    //     $function:          RESTAPI種別
    //     $Protocol:          アクセスプロトコル
    //     $HostName:          接続先ホスト名
    //     $PortNo:            接続先ポート番号
    //     $token:       アクセスキーID
    //     $SecretAccessKey:   シークレットアクセスキー
    //     $RequestContents:   HTTPリクエスト時にペイロード部に設定する情報
    //     $aryResult:
    //      array( "StatusCode"      => HTTPステータスコード,
    //             "ResponsContents" => REST APIのレスポンスコンテンツ )
    //      ※REST APIアクセス前後の処理で何らかのエラーが発生した場合は下記を返却。
    //        パラメータチェックエラーの場合
    //        ( "StatusCode" => -1, "ResponsContents" => array( "ErrorMessage" => エラー原因 ) )
    //        通信タイムアウトの場合
    //        ( "StatusCode" => -2, "ResponsContents" => array( "ErrorMessage" => "通信タイムアウト" ) )
    //     $ina_parm_list:     RESTAPIに必要なパラメータ配列
    //
    //  【戻り値】
    //     $aryResult['StatusCode']
    //
    //////////////////////////////////////////////////////////////////////
    function openstack_rest_call($function,$Protocol,$HostName,$PortNo,$prioryUrl,$token,$RequestContents,&$aryResult){
        $Date = gmdate('D, d M Y H:i:s T', time());
        switch($function){
         case "DELETE_STACK":
            $RequestURI = $prioryUrl;
            $Method = "DELETE";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;           
        case "VALIDATE_TEMPLATE":
            $RequestURI = $prioryUrl."/validate";
            $Method = "POST";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "SHOW_STACK_DETAILS":
            $RequestURI = $prioryUrl;
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "CREATE_STACK":
            $RequestURI = $prioryUrl."/stacks";
            $Method = "POST";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");

            break;


        case "TOKENS":
            // Meta Dataクリーニング
            $RequestURI = $Protocol."://".$HostName."/tokens";
            $Method = "POST";
            $http_Header = array("Content-type: application/json; charset=UTF-8");
            break;


        case "PROJECTS":

            // Meta Dataクリーニング
            $RequestURI = $Protocol."://".$HostName."/tenants";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");

            break;
        case "AVAIL":
            $RequestURI = $prioryUrl."/os-availability-zone";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");


            break;

        case "IMAGE":
            $RequestURI = $prioryUrl."/v2/images?visibility=public";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;


        case "SERVER":

            $RequestURI = $prioryUrl."/servers/detail";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");

            break;
        case "FLAVOR":

            $RequestURI = $prioryUrl."/flavors";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "IP":

            $RequestURI = $prioryUrl."/v2.0/floatingips";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;


        case "SECURITYGROUP":
            $RequestURI = $prioryUrl."/v2.0/security-groups";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;


        case "KEYPAIR":
            $RequestURI = $prioryUrl."/os-keypairs";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "NETWORKS":
            $RequestURI = $prioryUrl."/v2.0/networks";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "ROUTERS":
            $RequestURI = $prioryUrl."/v2.0/routers";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;
      
        case "VOLUME":
            $RequestURI = $prioryUrl."/volumes/detail";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "FIREWALL":
            $RequestURI = $prioryUrl."/v2.0/fwaas/firewall_groups";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;

        case "LOADBALANCERS":
            $RequestURI = $prioryUrl."/v2.0/lbaas/loadbalancers";
            $Method = "GET";
            $http_Header = array('X-Auth-Token:'.$token,"Content-type: application/json");
            break;



        default:
            $aryResult['StatusCode'] = ( int ) -1;
            $aryResult['ResponsContents'] = array( "ErrorMessage" => 'Function error' );
            return $aryResult['StatusCode'];
        }


        $aryResult = openStack_restapi_access( $Protocol,
                                           $HostName,
                                           $PortNo,
                                           $RequestURI,
                                           $Method,
                                           $http_Header,
                                           $RequestContents );

        return $aryResult['StatusCode'];
    }

define('LOCAL_DEBUG',FALSE);
if(LOCAL_DEBUG){
    $Protocol = 'https';
    $HostName = 'ky-labo';
    $PortNo = 443;
    $Method = "GET";
    $token = "11111";
    $SecretAccessKey = "123";
    $Date = gmdate('D, d M Y H:i:s T', time());
    ////////////////////////////////
    // Signature作成              //
    ////////////////////////////////
    $RequestURI = "/index.php";
    $CRLF = "\r\n";
    $StringToSign = $Date . $CRLF . $RequestURI;
    $Signature = shell_exec( 'echo -e -n "' . $StringToSign . '" | openssl dgst -sha256 -binary -hmac ' . $SecretAccessKey . ' | openssl base64' );
    $arry = array("EXECNO"=>"0000000001",
                            "FORMAT"=>"format",
                            "LABLE"=>"lable",
                            "CONF_FILE"=>"conf_file",
                            "LOG_FILE"=>"log_file",
                            "ERROR_FILE"=>"error_file");
    $RequestContents = json_encode($arry, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK );
    $http_Header = array("Host: " . $HostName,
                                 "Content-Type: application/json",
                                 "X-UMF-API-Version: 2.1",
                                 "Date: " . $Date,
                                 "Authorization: SharedKeyLite " . $token . ":" . $Signature );
    $aryResult = openStack_restapi_access( $Protocol,
                                       $HostName,
                                       $PortNo,
                                       $RequestURI,
                                       $Method,
                                       $http_Header,
                                       $RequestContents );

    var_dump($aryResult);
}
?>
