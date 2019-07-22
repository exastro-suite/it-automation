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
//      AnsibleTower RestApi WorkflowJobTemplateNode系を呼ぶ クラス
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

class AnsibleTowerRestApiWorkflowJobTemplateNodes extends AnsibleTowerRestApiBase {

    const API_PATH  = "workflow_job_template_nodes/";
    const IDENTIFIED_NAME_PREFIX = "";

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

        // content生成
        $content = array();

        if(!empty($param['execution_no']) && !empty($param['loopCount'])) {
            $content['name'] = self::createName(self::IDENTIFIED_NAME_PREFIX, $param['execution_no'], $param['loopCount']);
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'execution_no' and 'loopCount'.";
            return $response_array;
        }

        if(!empty($param['workflowTplId'])) {
            $content['workflow_job_template'] = $param['workflowTplId'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'workflow_job_tmplate Id'.";
            return $response_array;
        }

        if(!empty($param['jobtplId'])) {
            $content['unified_job_template'] = $param['jobtplId'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'job_template Id'.";
            return $response_array;
        }

        // REST APIアクセス
        $method = "POST";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH, $content);

        // REST失敗
        if($response_array['statusCode'] != 201) {
            $response_array['success'] = false;
            if(!array_key_exists("errorMessage", $response_array['responseContents'])) {
                $response_array['responseContents']['errorMessage'] = "status_code not 201. =>" . $response_array['statusCode'];
            }
            return $response_array;
        }

        // REST成功
        $response_array['success'] = true;

        return $response_array;
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
        $filteringName = self::createName(AnsibleTowerRestApiWorkflowJobTemplates::IDENTIFIED_NAME_PREFIX, $execution_no);
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

        foreach($pickup_response_array_2['responseContents'] as $wfJobTplNode) {

            $response_array = self::delete($RestApiCaller, $wfJobTplNode['id']);
            if($response_array['success'] == false) {
                return $response_array;
            }
        }

        return $pickup_response_array_2; // データ不足しているが、後続の処理はsuccessしか確認しないためこのまま
    }

}

?>
