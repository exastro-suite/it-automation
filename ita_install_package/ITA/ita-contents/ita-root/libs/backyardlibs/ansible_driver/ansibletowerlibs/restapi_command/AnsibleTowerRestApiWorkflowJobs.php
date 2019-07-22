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
//      AnsibleTower RestApi WorkflowJob系を呼ぶ クラス
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
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiWorkflowJobTemplates.php");

class AnsibleTowerRestApiWorkflowJobs extends AnsibleTowerRestApiBase {

    const API_PATH  = "workflow_jobs/";
    const IDENTIFIED_NAME_PREFIX = "ita_executions_workflow_";
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

        //      USE WorkflowJobTemplates#launch();

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

        $response_array = self::getByExecutionNo($RestApiCaller, $execution_no);
        if($response_array['success'] == false) {
            return $response_array;
        }

        if(empty($response_array['responseContents'])) {
            return $response_array;
        }

        $wfJobData = $response_array['responseContents'];

        $response_array = self::delete($RestApiCaller, $wfJobData['id']);
        if($response_array['success'] == false) {
            return $response_array;
        }

        return $response_array;
    }

    static function getByExecutionNo($RestApiCaller, $execution_no) {
        global $g;
        global $vg_tower_driver_name;
        if(isset($g['TOWER_DRIVER_NAME']))
             $vg_tower_driver_name = $g['TOWER_DRIVER_NAME'];

        // データ絞り込み(親)
        $filteringName = sprintf(AnsibleTowerRestApiWorkflowJobTemplates::IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));
        $query = "?name=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiWorkflowJobTemplates::getAll($RestApiCaller, $query);
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
                $pickup_response_array['responseContents']['errorMessage'] = "Exception! More than one workflow job template.";
                return $pickup_response_array;
        }

        $wfJobTplId = $pickup_response_array['responseContents'][0]['id'];

        // データ絞り込み(本体)
        $query = "?workflow_job_template=" . $wfJobTplId;
        $pickup_response_array_2 = self::getAll($RestApiCaller, $query);
        if($pickup_response_array_2['success'] == false) {
            return $pickup_response_array_2;
        }

        $count = count($pickup_response_array_2['responseContents']);
        switch($count) {
            case 0:
                // 対象無しでもそのまま返す(responseContents = 空配列)
                return $pickup_response_array_2;
                break;

            case 1:
                // SUCCESS
                break;

            default:
                // 2つ以上取得できる場合は異常
                $pickup_response_array_2['success'] = false;
                $pickup_response_array_2['responseContents']['errorMessage'] = "Exception! More than one workflow job.";
                return $pickup_response_array_2;
        }

        $pickup_response_array_2['responseContents'] = $pickup_response_array_2['responseContents'][0];
        return $pickup_response_array_2;
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

    static function cancelRelatedCurrnetExecution($RestApiCaller, $execution_no) {

        $response_array = self::getByExecutionNo($RestApiCaller, $execution_no);
        if($response_array['success'] == false) {
            return $response_array;
        }

        if(empty($response_array['responseContents'])) {
            return $response_array;
        }

        $wfJobData = $response_array['responseContents'];

        $response_array = self::cancel($RestApiCaller, $wfJobData['id']);
        if($response_array['success'] == false) {
            return $response_array;
        }

        return $response_array;

    }

}

?>
