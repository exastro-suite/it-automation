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
//      AnsibleTower RestApi Job系を呼ぶ クラス
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
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiJobTemplates.php");

class AnsibleTowerRestApiJobs extends AnsibleTowerRestApiBase {

    const API_PATH  = "jobs/";
    const IDENTIFIED_NAME_PREFIX = "ita_executions_job_";
    const API_SUB_PATH_STDOUT  = "stdout/?format=txt";
    const API_SUB_PATH_CANCEL  = "cancel/";

    // static only
    private function __construct() {
    }

    static function getAll($RestApiCaller, $query = "") {

        // REST APIアクセス
        $method = "GET";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $query);

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
        $response_array['responseContents'] = $response_array['responseContents']['results'];

        return $response_array;
    }

    static function get($RestApiCaller, $id) {

        // REST APIアクセス
        $method = "GET";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $id . "/");

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

    static function post($RestApiCaller, $param) {

        //      USE JobTemplates#launch();

        throw new BadMethodCallException("Not implemented.");
    }

    static function delete($RestApiCaller, $id) {

        // REST APIアクセス
        $method = "DELETE";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $id . "/");

        // REST失敗
        if($response_array['statusCode'] != 204) {
            $response_array['success'] = false;
            if(!array_key_exists("errorMessage", $response_array['responseContents'])) {
                $response_array['responseContents']['errorMessage'] = "status_code not 204. =>" . $response_array['statusCode'];
            }
            return $response_array;
        }

        // REST成功
        $response_array['success'] = true;

        return $response_array;
    }

    static function deleteRelatedCurrnetExecution($RestApiCaller, $execution_no) {

        global $vg_tower_driver_name;

        // データ絞り込み(親)
        //$filteringName = self::createName(AnsibleTowerRestApiJobTemplates::SEARCH_NAME_PREFIX, $execution_no) . "_";
        $filteringName = sprintf(AnsibleTowerRestApiJobTemplates::SEARCH_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));

        $query = "?name__startswith=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiJobTemplates::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        foreach($pickup_response_array['responseContents'] as $jobTplData) {

            // データ絞り込み(本体)
            $query = "?job_template=" . $jobTplData['id'];
            $pickup_response_array_2 = self::getAll($RestApiCaller, $query);
            if($pickup_response_array_2['success'] == false) {
                return $pickup_response_array_2;
            }

            foreach($pickup_response_array_2['responseContents'] as $jobData) {

                $response_array = self::delete($RestApiCaller, $jobData['id']);
                if($response_array['success'] == false) {
                    return $response_array;
                }
            }
        }

        return $pickup_response_array; // データ不足しているが、後続の処理はsuccessしか確認しないためこのまま
    }

    static function deleteRelatedCurrnetExecutionForPrepare($RestApiCaller, $execution_no) {

        global $vg_tower_driver_name;

        // データ絞り込み(親)
        //$filteringName = self::createName(AnsibleTowerRestApiJobTemplates::PREPARE_BUILD_NAME_PREFIX, $execution_no);
        $filteringName = sprintf(AnsibleTowerRestApiJobTemplates::PREPARE_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));
        $query = "?name=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiJobTemplates::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        $count = count($pickup_response_array['responseContents']);
        switch($count) {
            case 0:
                // 対象無し
                return $pickup_response_array;
                break;

            case 1:
                // SUCCESS
                break;

            default:
                // 2つ以上取得できる場合は異常
                $pickup_response_array['success'] = false;
                $pickup_response_array['responseContents']['errorMessage'] = "Exception! More than one prepare job template for one execution.";
                return $pickup_response_array;
        }

        $jobTplId = $pickup_response_array['responseContents'][0]['id'];

        // データ絞り込み(本体)
        $query = "?job_template=" . $jobTplId;
        $pickup_response_array_2 = self::getAll($RestApiCaller, $query);
        if($pickup_response_array_2['success'] == false) {
            return $pickup_response_array_2;
        }

        foreach($pickup_response_array_2['responseContents'] as $jobData) {

            $response_array = self::delete($RestApiCaller, $jobData['id']);
            if($response_array['success'] == false) {
                return $response_array;
            }
        }

        // データ絞り込み(親)
        //$filteringName = self::createName(AnsibleTowerRestApiJobTemplates::CLEANUP_PREPARED_BUILD_NAME_PREFIX, $execution_no);
        $filteringName = sprintf(AnsibleTowerRestApiJobTemplates::CLEANUP_PREPARED_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));

        $query = "?name=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiJobTemplates::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        $count = count($pickup_response_array['responseContents']);
        switch($count) {
            case 0:
                // 対象無し
                return $pickup_response_array;
                break;

            case 1:
                // SUCCESS
                break;

            default:
                // 2つ以上取得できる場合は異常
                $pickup_response_array['success'] = false;
                $pickup_response_array['responseContents']['errorMessage'] = "Exception! More than one cleanup job template for one execution.";
                return $pickup_response_array;
        }

        $jobTplId = $pickup_response_array['responseContents'][0]['id'];

        // データ絞り込み(本体)
        $query = "?job_template=" . $jobTplId;
        $pickup_response_array_2 = self::getAll($RestApiCaller, $query);
        if($pickup_response_array_2['success'] == false) {
            return $pickup_response_array_2;
        }

        foreach($pickup_response_array_2['responseContents'] as $jobData) {

            $response_array = self::delete($RestApiCaller, $jobData['id']);
            if($response_array['success'] == false) {
                return $response_array;
            }
        }


        return $pickup_response_array_2; // データ不足しているが、後続の処理はsuccessしか確認しないためこのまま
    }

    // 現在不使用
    static function getStdOut($RestApiCaller, $id) {

        // REST APIアクセス
        $method = "GET";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $id . "/" . self::API_SUB_PATH_STDOUT , array(), array(),true);

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

    static function cancel($RestApiCaller, $id) {

        // REST APIアクセス
        $method = "POST";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $id . "/" . self::API_SUB_PATH_CANCEL);

        // REST失敗
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

    static function cancelRelatedCurrnetExecutionForPrepare($RestApiCaller, $execution_no) {
        global $vg_tower_driver_name;

        // データ絞り込み(親)
        //$filteringName = self::createName(AnsibleTowerRestApiJobTemplates::CLEANUP_PREPARED_BUILD_NAME_PREFIX, $execution_no);
        $filteringName = sprintf(AnsibleTowerRestApiJobTemplates::CLEANUP_PREPARED_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));
        $query = "?name=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiJobTemplates::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        $count = count($pickup_response_array['responseContents']);
        switch($count) {
            case 0:
                // 対象無し
                return $pickup_response_array;
                break;

            case 1:
                // SUCCESS
                break;

            default:
                // 2つ以上取得できる場合は異常
                $pickup_response_array['success'] = false;
                $pickup_response_array['responseContents']['errorMessage'] = "Exception! More than one cleanup job template for one execution.";
                return $pickup_response_array;
        }

        $jobTplId = $pickup_response_array['responseContents'][0]['id'];

        // データ絞り込み(本体)
        $query = "?job_template=" . $jobTplId;
        $response_array = self::getAll($RestApiCaller, $query);
        if($response_array['success'] == false) {
            return $response_array;
        }

        $count = count($response_array['responseContents']);
        switch($count) {
            case 0:
                // 対象無し
                return $response_array;
                break;

            case 1:
                // SUCCESS
                break;

            default:
                // 2つ以上取得できる場合は異常
                $response_array['success'] = false;
                $response_array['responseContents']['errorMessage'] = "Exception! More than one cleanup job template for one execution.";
                return $response_array;
        }

        $jobData = $response_array['responseContents'][0];

        $response_array = self::cancel($RestApiCaller, $jobData['id']);
        if($response_array['success'] == false) {
            return $response_array;
        }

        return $response_array;

    }

}

?>
