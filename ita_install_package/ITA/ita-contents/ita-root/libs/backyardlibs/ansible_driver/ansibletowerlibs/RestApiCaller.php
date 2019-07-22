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
//      AnsibleTower REST API call クラス
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if (empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/commonlibs/common_php_functions.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/LogWriter.php"); 
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/MessageTemplateStorageHolder.php");

class RestApiCaller {

    const API_BASE_PATH     = "/api/v2/";
    const API_TOKEN_PATH    = "authtoken/";

    private $logger;
    private $msgTplStorage;

    private $baseURI;
    private $decryptedAuthToken;

    private $accessToken;

    private $exec_mode;

    private $UIErrorMsg;
    function __construct($protocol, $hostName, $portNo, $encryptedAuthToken) {
        $this->baseURI = $protocol . "://" . $hostName . ":" . $portNo . self::API_BASE_PATH;
        $this->decryptedAuthToken= ky_decrypt($encryptedAuthToken);

        $this->logger = LogWriter::getInstance();
        $this->msgTplStorage = MessageTemplateStorageHolder::getMTS();
        if( isset($_SERVER) === true ){
            if( array_key_exists('HTTP_HOST', $_SERVER) === true ){
                $exec_mode = "web";
            }else{
                $exec_mode = "backyard";
            }
        }else{
            $exec_mode = "unknowned";
        }
        $UIErrorMsg = array();
    }
    function setUp($log_output_php, $log_output_dir, $log_file_prefix, $log_level, $UIExecLogPath, $UIErrorLogPath) {
        $this->logger->setUp($log_output_php, $log_output_dir, $log_file_prefix, $log_level, $UIExecLogPath, $UIErrorLogPath);
    }

    function authorize() {

// AnsibleTowe 2.3以前の認証方法---------
//        // 認証
//        $content = array(
//            "username" => $this->userId,
//            "password" => $this->decryptedPasswd,
//        );
//
//        // REST APIアクセス
//        $method = "POST";
//        $apiUri = self::API_TOKEN_PATH;
//
//        $response_array = $this->restCall($method, $apiUri, $content);
//
//        // REST失敗
//        if($response_array['statusCode'] != 200) {
//            $response_array['success'] = false;
//            return $response_array;
//        }
//
//        // REST成功
//        $responseContents = $response_array['responseContents'];
//
//        $this->accessToken = $responseContents['token'];
// ------------AnsibleTowe 2.3以前の認証方法

        $this->accessToken = $this->decryptedAuthToken;

        $response_array = array();
        $response_array['success'] = true;
        $response_array['responseContents'] = $this->accessToken;

        return $response_array;
    }

    function restCall($method, $apiUri, $content = array(), $header = array(), $Rest_stdout_flg=false) {

        $httpContext = array();
        $header      = array();

        if($Rest_stdout_flg == false) {
            // コンテンツ付与
            if(!empty($content)) {
                $httpContext['http']['content'] = json_encode($content);
                $header[] = "Content-type: application/json";
            }

            // Header精査
            if(!empty($this->accessToken)) {
                // AnsibleTowe 2.3以前の認証方法---------
                //$header[] = "Authorization: Token " . $this->accessToken;
                // ------------AnsibleTowe 2.3以前の認証方法
                $header[] = "Authorization: Bearer " . $this->accessToken;
            }

            if(empty($header) || self::hasHeaderField($header, "Accept:") == false) {
                $header[] = "Accept: application/json";
            }
        }
        else
        {
            if(!empty($content)) {
                $httpContext['http']['content'] = json_encode($content);
            }
            $header[] = "Authorization: Bearer " . $this->accessToken;
        }

        // HTTPコンテキスト作成
        $httpContext['http']['method']          = $method;
        $httpContext['http']['ignore_errors']   = true;
        $httpContext['http']['header']          = implode("\r\n", $header);

        // 暫定対応 SSL認証エラー無視
        $httpContext['ssl']['verify_peer']      = false;
        $httpContext['ssl']['verify_peer_name'] = false;
        // 暫定ここまで

        $trace = debug_backtrace();
        $print_backtrace = "-------------------------backtrace----------------------\n";
        foreach($trace as $line) {
            $nowfile = 'None';
            $nowline = 'None';
            if(isset($line['file'])) $nowfile = $line['file'];
            if(isset($line['line'])) $nowline = $line['line'];
            $print_backtrace .= sprintf("%s: line:%s\n",$nowfile,$nowline);
        }
        $print_url = sprintf("URL: %s%s\n",$this->baseURI,$apiUri);
        $print_HttpContext = sprintf("http context\n%s",print_r($httpContext,true));

        ////////////////////////////////
        // RestCall
        ////////////////////////////////
        $http_response_header = null;
        $responseContents = file_get_contents(
                                $this->baseURI . $apiUri,
                                false,
                                stream_context_create($httpContext)
                            );

        $print_HttpResponsHeader =  sprintf("http response header\n%s",print_r($http_response_header,true));

        // 通信結果を判定
        if(count($http_response_header) > 0) {

            // HTTPレスポンスコード取得
            preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
            $status_code = $matches[1];

            // 返却用のArrayを編集
            $response_array['statusCode'] = (int) $status_code;
            if($status_code < 200 || 400 <= $status_code) {
                $response_array['responseHeaders']  = $http_response_header;
                $response_array['responseContents'] = array("errorMessage" => $responseContents);

                $this->logger->error($print_backtrace);
                $this->logger->error($print_url);
                $this->logger->error($print_HttpContext);
                $this->logger->error($print_HttpResponsHeader);
                $this->logger->error('http response content\n%s' . print_r($response_array['responseContents'],true));
            } else {
                $response_array['responseHeaders']  = $http_response_header;
                $response_array['responseContents'] = $responseContents;
                foreach($response_array['responseHeaders'] as $header) {
                    if( preg_match("/^(\s)*Content-Type:/",$header) == 1) {
                        if( preg_match("/(\s)*application\/json/",$header) == 1) {
                            $response_array['responseContents'] = json_decode($responseContents, true);
                        }
                    }
                }

                $this->logger->debug($print_backtrace);
                $this->logger->debug($print_url);
                $this->logger->debug($print_HttpContext);
                $this->logger->debug($print_HttpResponsHeader);
                $this->logger->debug('http response content\n%s' . print_r($response_array['responseContents'],true));
            }
        } else {

            // 返却用のArrayを編集
            $response_array['statusCode'] = (int) -2;
            $response_array['responseContents'] = array("errorMessage" => "HTTP Socket Timeout");

            $this->logger->error($print_backtrace);
            $this->logger->error($print_url);
            $this->logger->error($print_HttpContext);
        }
        
        $this->UIErrorMsg = array();
        $this->UIErrorMsg['URL']         = $print_url;
        $this->UIErrorMsg['METHOD']      = $method;
        $this->UIErrorMsg['HTTP_STATUS'] = $response_array['statusCode'];

        return $response_array;
    }

    private static function hasHeaderField($header, $field) {

        foreach($header as $headerItem) {
            // 前方一致確認
            if(strpos($headerItem, $field) === 0) {
                return true;
            }
        }
        return false;
    }

    function getAccessToken() {

        return $this->accessToken;
    }

    function getLastErrorMsg() {
        return $this->UIErrorMsg;
    }  
}

?>
