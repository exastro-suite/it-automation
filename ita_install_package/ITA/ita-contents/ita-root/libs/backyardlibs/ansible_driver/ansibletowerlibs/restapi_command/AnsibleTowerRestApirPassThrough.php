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
//      AnsibleTower RestApi Project系を呼ぶ クラス
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiBase.php"); 

class  AnsibleTowerRestApirPassThrough  extends AnsibleTowerRestApiBase {

    // static only
    private function __construct() {
    }

    static function get($RestApiCaller, $query, $Rest_stdout_flg = false) {

        // REST APIアクセス
        $method  = "GET";
        $content = array();
        $header  = array(); 
        $DirectUrl = true;

        $response_array = $RestApiCaller->restCall($method, $query, $content, $header, $Rest_stdout_flg, $DirectUrl);

        // REST失敗
        if($response_array['statusCode'] != 200) {
            $response_array['success'] = false;
            if(!array_key_exists("errorMessage", $response_array['responseContents'])) {
                $response_array['responseContents']['errorMessage'] = "status_code not 200. =>" . $response_array['statusCode'];
            }
            return $response_array;
        }

        // REST成功
        $response_array['success'] = true;

        return $response_array;
    }

    static function post($RestApiCaller, $query, $Rest_stdout_flg = false) {

        // REST APIアクセス
        $method  = "POST";
        $content = array();
        $header  = array(); 
        $DirectUrl = true;

        $response_array = $RestApiCaller->restCall($method, $query, $content, $header, $Rest_stdout_flg, $DirectUrl);

        // REST失敗
        // マニュアルは201となっている模様
        if($response_array['statusCode'] != 202) {
            $response_array['success'] = false;
            if(!array_key_exists("errorMessage", $response_array['responseContents'])) {
                $response_array['responseContents']['errorMessage'] = "status_code not 202. =>" . $response_array['statusCode'];
            }
            return $response_array;
        }

        // REST成功
        $response_array['success'] = true;

        return $response_array;
    }
}
?>
