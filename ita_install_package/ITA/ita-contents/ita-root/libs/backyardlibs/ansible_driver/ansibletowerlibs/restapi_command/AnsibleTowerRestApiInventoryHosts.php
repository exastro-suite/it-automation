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
//      AnsibleTower RestApi InventoryHost系を呼ぶ クラス
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
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiHosts.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiInventories.php");

class AnsibleTowerRestApiInventoryHosts extends AnsibleTowerRestApiBase {

    const API_PATH  = "inventories/";
    const API_SUB_PATH  = "hosts/";
    const IDENTIFIED_NAME_PREFIX = "";

    // static only
    private function __construct() {
    }

    static function getAllEachInventory($RestApiCaller, $inventoryId, $query = "") {

        // REST APIアクセス
        $method = "GET";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $inventoryId . "/" . self::API_SUB_PATH . $query);

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

    static function get($RestApiCaller, $inventoryId, $hostId) {

        // REST APIアクセス
        $method = "GET";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $inventoryId . "/" . self::API_SUB_PATH . $hostId . "/");

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

        if(empty($param['inventoryId'])) {
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'inventory id'.";
            return $response_array;
        }

        // content生成
        $content = array();

        if(!empty($param['name'])) {
            $content['name'] = $param['name'];
        } else {
            // 必須のためNG返す
            $response_array['success'] = false;
            $response_array['responseContents']['errorMessage'] = "Need 'name'.";
            return $response_array;
        }

        if(!empty($param['variables'])) {
            $content['variables'] = $param['variables'];
        } // 任意パラメータは無くてもNG返さない

        // REST APIアクセス
        $method = "POST";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $param['inventoryId'] . "/" . self::API_SUB_PATH, $content);

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

    static function delete($RestApiCaller, $inventoryId, $hostId) {

        // REST APIアクセス
        $method = "DELETE";
        $response_array = $RestApiCaller->restCall($method, self::API_PATH . $inventoryId . "/" . self::API_SUB_PATH . $hostId . "/");

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
        //$filteringName = self::createName(AnsibleTowerRestApiInventories::IDENTIFIED_NAME_PREFIX, $execution_no) . "_";
        $filteringName = sprintf(AnsibleTowerRestApiInventories::IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no),'');
        $query = "?name__startswith=" . $filteringName;
        $pickup_response_array = AnsibleTowerRestApiInventories::getAll($RestApiCaller, $query);
        if($pickup_response_array['success'] == false) {
            return $pickup_response_array;
        }

        foreach($pickup_response_array['responseContents'] as $inventoryData) {

            $inventoryId = $inventoryData['id'];

            // データ絞り込み(本体)
            $pickup_response_array_2 = self::getAllEachInventory($RestApiCaller, $inventoryId);
            if($pickup_response_array_2['success'] == false) {
                return $pickup_response_array_2;
            }

            foreach($pickup_response_array_2['responseContents'] as $inventoryHostData) {
                $response_array = AnsibleTowerRestApiHosts::delete($RestApiCaller, $inventoryHostData['id']);
                if($response_array['success'] == false) {
                    return $response_array;
                }
            }
        }

        return $pickup_response_array; // データ不足しているが、後続の処理はsuccessしか確認しないためこのまま
    }

}

?>
