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
//      AnsibleTower RestApi JobTemplate系を呼ぶ クラス
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

class AnsibleTowerRestApiJobTemplates extends AnsibleTowerRestApiBase {

    const API_PATH  = "job_templates/";
    const IDENTIFIED_NAME_PREFIX = "ita_%s_executions_jobtpl_%s_%s";
    const PREPARE_BUILD_NAME_PREFIX = "ita_%s_executions_prepare_build_%s";
    const API_SUB_PATH_LAUNCH  = "launch/";
    const LAUNCH_PLAYBOOK_NAME = "site.yml";
    const CLEANUP_PREPARED_BUILD_NAME_PREFIX = "ita_%s_executions_cleanup_%s";
    const SEARCH_NAME_PREFIX = "ita_%s_executions_jobtpl_%s_";

    const SEARCH_IDENTIFIED_NAME_PREFIX = "ita_%s_executions_jobtpl_%s";

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

    static function post($RestApiCaller, $param, $addparam=array()) {

        global $vg_tower_driver_name;

        // content生成
        $content = array();

        if(!empty($param['execution_no']) && !empty($param['loopCount'])) {
            //$content['name'] = self::createName(self::IDENTIFIED_NAME_PREFIX, $param['execution_no'], $param['loopCount']);
            $content['name'] = sprintf(self::IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($param['execution_no']),addPadding($param['loopCount']));

        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'execution_no' and 'loopCount'.";
            return $response_array;
        }

        if(!empty($param['inventory'])) {
            $content['inventory'] = $param['inventory'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'inventory'.";
            return $response_array;
        }

        if(!empty($param['project'])) {
            $content['project'] = $param['project'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'project'.";
            return $response_array;
        }

        if(!empty($param['playbook'])) {
            $content['playbook'] = $param['playbook'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'playbook'.";
            return $response_array;
        }

        if(!empty($param['credential'])) {
            $content['credential'] = $param['credential'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'credential'.";
            return $response_array;
        }

        if(!empty($param['vault_credential'])) {
            $content['vault_credential'] = $param['vault_credential'];
        }

        if(!empty($param['job_type'])) {
            $content['job_type'] = $param['job_type'];
        } // 任意パラメータは無くてもNG返さない

        foreach($addparam as $key=>$val)
        {
            $content[$key] = $val;
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

        // データ絞り込み
        //$filteringName = self::createName(self::IDENTIFIED_NAME_PREFIX, $execution_no) . "_";
        $filteringName = sprintf(self::IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no),'');
        $query = "?name__startswith=" . $filteringName;
        $pickup_response_array = self::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        foreach($pickup_response_array['responseContents'] as $jobTplData) {

            $response_array = self::delete($RestApiCaller, $jobTplData['id']);
            if($response_array['success'] == false) {
                return $response_array;
            }
        }

        return $pickup_response_array; // データ不足しているが、後続の処理はsuccessしか確認しないためこのまま
    }

    static function deleteRelatedCurrnetExecutionForPrepare($RestApiCaller, $execution_no) {
        global $vg_tower_driver_name;

        // データ絞り込み
        //$filteringName = self::createName(self::PREPARE_BUILD_NAME_PREFIX, $execution_no);
        $filteringName = sprintf(self::PREPARE_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));

        $query = "?name=" . $filteringName;
        $pickup_response_array = self::getAll($RestApiCaller, $query);
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

        $jobTplData = $pickup_response_array['responseContents'][0];

        $response_array = self::delete($RestApiCaller, $jobTplData['id']);
        if($response_array['success'] == false) {
            return $response_array;
        }

        // データ絞り込み
        //$filteringName = self::createName(self::CLEANUP_PREPARED_BUILD_NAME_PREFIX, $execution_no);
        $filteringName = sprintf(self::CLEANUP_PREPARED_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));

        $query = "?name=" . $filteringName;
        $pickup_response_array = self::getAll($RestApiCaller, $query);
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

        $jobTplData = $pickup_response_array['responseContents'][0];

        $response_array = self::delete($RestApiCaller, $jobTplData['id']);
        if($response_array['success'] == false) {
            return $response_array;
        }

        return $response_array;
    }

    static function postForPrepare($RestApiCaller, $param) {
        global $vg_tower_driver_name;

        // content生成
        $content = array();

        if(!empty($param['execution_no'])) {
            //$content['name'] = self::createName(self::PREPARE_BUILD_NAME_PREFIX, $param['execution_no']);
            $content['name'] = sprintf(self::PREPARE_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($param['execution_no']));
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'execution_no'.";
            return $response_array;
        }

        if(!empty($param['inventory'])) {
            $content['inventory'] = $param['inventory'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'inventory'.";
            return $response_array;
        }

        if(!empty($param['project'])) {
            $content['project'] = $param['project'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'project'.";
            return $response_array;
        }

        if(!empty($param['playbook'])) {
            $content['playbook'] = $param['playbook'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'playbook'.";
            return $response_array;
        }

        if(!empty($param['credential'])) {
            $content['credential'] = $param['credential'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'credential'.";
            return $response_array;
        }

        if(!empty($param['execution_no']) && !empty($param['dataRelayStorage'])) {
            // 構築用のplaybookと同期させること
            $content['extra_vars'] = json_encode(array(
                "execution_no_with_padding" => addPadding($param['execution_no']),
                "if_info_data_relay_storage" => $param['dataRelayStorage'],
                "driver_type" => $param['driver_type'],
                "driver_id" => $param['driver_id'],
                "driver_name" => $param['driver_name']
            ));
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'data_relay_storage'.";
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

    static function postForCleanupPreparedProjectDirectory($RestApiCaller, $param) {
        global $vg_tower_driver_name;

        // content生成
        $content = array();

        if(!empty($param['execution_no'])) {
            //$content['name'] = self::createName(self::CLEANUP_PREPARED_BUILD_NAME_PREFIX, $param['execution_no']);
            $content['name'] = sprintf(self::CLEANUP_PREPARED_BUILD_NAME_PREFIX,$vg_tower_driver_name,addPadding($param['execution_no']));

        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'execution_no'.";
            return $response_array;
        }

        if(!empty($param['driver_name'])) {
            $content['driver_name'] = $param['driver_name'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'driver_name'.";
            return $response_array;
        }
        // 掃除用のplaybookと同期させること
        $content['extra_vars'] = json_encode(array(
            "execution_no_with_padding" => addPadding($param['execution_no']),
            "driver_name" => $param['driver_name']
        ));

        if(!empty($param['inventory'])) {
            $content['inventory'] = $param['inventory'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'inventory'.";
            return $response_array;
        }

        if(!empty($param['project'])) {
            $content['project'] = $param['project'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'project'.";
            return $response_array;
        }

        if(!empty($param['playbook'])) {
            $content['playbook'] = $param['playbook'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'playbook'.";
            return $response_array;
        }

        if(!empty($param['credential'])) {
            $content['credential'] = $param['credential'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'credential'.";
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

    static function launch($RestApiCaller, $param) {

        // ※prepare実行のみを想定
        //   汎用性は検討していない

        // content生成
        $content = array();

        if(empty($param['jobTplId'])) {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'job_template id'.";
            return $response_array;
        }


        // REST APIアクセス
        $method = "POST";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $param['jobTplId'] . "/" . self::API_SUB_PATH_LAUNCH);

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

}

?>
