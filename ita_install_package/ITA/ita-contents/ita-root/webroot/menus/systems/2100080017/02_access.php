<?php
//   Copyright 2020 NEC Corporation
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

	$tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

	require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
	require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100080017/81_terraformEnterpriseData.php");
	require_once ( $root_dir_path . "/libs/commonlibs/common_terraform_function.php");
	require_once ( $root_dir_path . "/libs/commonlibs/common_terraform_restapi.php");

	class Db_Access extends Db_Access_Core {
		function getOrganizationData(){
			//グローバル変数宣言
			global $g;

			$ret = array();
			$ret['result'] = false;
			$ret['htmlBody'] = "";
			$terraformEnerpriseData = new terraformEnerpriseData();

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
				$ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181010').'</p>';

				return $ret;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Organization一覧のhtmlBodyを取得
			$getData = $terraformEnerpriseData->getOrganizationData($hostname, $token, $proxySetting);
			$ret['result'] = $getData['result'];
			$ret['htmlBody'] = $getData['htmlBody'];

			return $ret;
		}

		function getWorkspaceData(){
			//グローバル変数宣言
			global $g;

			$ret = array();
			$ret['result'] = false;
			$ret['htmlBody'] = "";
			$terraformEnerpriseData = new terraformEnerpriseData();

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
				$ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181010').'</p>';

				return $ret;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Workspace一覧のhtmlBodyを取得
			$getData = $terraformEnerpriseData->getWorkspaceData($hostname, $token, $proxySetting);
			$ret['result'] = $getData['result'];
			$ret['htmlBody'] = $getData['htmlBody'];

			return $ret;
		}

		function getPolicyData(){
			//グローバル変数宣言
			global $g;

			$ret = array();
			$ret['result'] = false;
			$ret['htmlBody'] = "";
			$terraformEnerpriseData = new terraformEnerpriseData();

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
				$ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181010').'</p>';

				return $ret;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Policye一覧のhtmlBodyを取得
			$getData = $terraformEnerpriseData->getPolicyData($hostname, $token, $proxySetting);
			$ret['result'] = $getData['result'];
			$ret['htmlBody'] = $getData['htmlBody'];

			return $ret;
		}

		function getPolicySetData(){
			//グローバル変数宣言
			global $g;

			$ret = array();
			$ret['result'] = false;
			$ret['htmlBody'] = "";

			$terraformEnerpriseData = new terraformEnerpriseData();

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
				$ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181010').'</p>';

				return $ret;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//PolicyeSet一覧のhtmlBodyを取得
			$getData = $terraformEnerpriseData->getPolicySetData($hostname, $token, $proxySetting);
			$ret['result'] = $getData['result'];
			$ret['htmlBody'] = $getData['htmlBody'];

			return $ret;
		}

		function deleteOrganization($data){
			$ret = array();
			$ret['result'] = false;
			$organizationName = urldecode($data['organizationName']);
			$ret['target'] = $organizationName;

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Organization削除APIを実行
			$apiResponse = delete_organization($hostname, $token, $organizationName, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 204){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function deleteWorkspace($data){
			$ret = array();
			$ret['result'] = false;
			$organizationName = urldecode($data['organizationName']);
			$workspaceName = urldecode($data['workspaceName']);
			$ret['target'] = $workspaceName;

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Workspace削除APIを実行
			$apiResponse = delete_workspace($hostname, $token, $organizationName, $workspaceName, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 200){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function deletePolicy($data){
			$ret = array();
			$ret['result'] = false;
			$policyId = urldecode($data['policyId']);
			$policyName = urldecode($data['policyName']);
			$ret['target'] = $policyName;

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//Policy削除APIを実行
			$apiResponse = delete_policy($hostname, $token, $policyId, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 204){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function deletePolicySet($data){
			$ret = array();
			$ret['result'] = false;
			$policySetId = urldecode($data['policySetId']);
			$policySetName = urldecode($data['policySetName']);
			$ret['target'] = $policySetName;

			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//PolicySet削除APIを実行
			$apiResponse = delete_policy_set($hostname, $token, $policySetId, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 204){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function deleteRelationshipWorkspace($data){
			$ret = array();
			$ret['result'] = false;
			$policySetId = urldecode($data['policySetId']);
			$policySetName = urldecode($data['policySetName']);
			$workspaceId = urldecode($data['workspaceId']);
			$workspaceName = urldecode($data['workspaceName']);
			$ret['policySetName'] = $policySetName;
			$ret['workspaceName'] = $workspaceName;


			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//PolicySetからWorkspaceを切り離すAPIを実行
	        $workspaceData = array(
	            "data" => array(
	                array(
	                    "id" => $workspaceId,
	                    "type" => "workspaces"
	                )
	            )
	        );
			$apiResponse = delete_relationships_workspace($hostname, $token, $policySetId, $workspaceData, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 204){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function deleteRelationshipPolicy($data){
			$ret = array();
			$ret['result'] = false;
			$policySetId = urldecode($data['policySetId']);
			$policySetName = urldecode($data['policySetName']);
			$policyId = urldecode($data['policyId']);
			$policyName = urldecode($data['policyName']);
			$ret['policySetName'] = $policySetName;
			$ret['policyName'] = $policyName;


			//インターフェース情報を取得
			$interfaceData = $this->setInterfaceInfo();
			if($interfaceData == false){
				$ret['result'] = false;
			}
			$hostname = $interfaceData['hostName'];
			$token = $interfaceData['token'];
			$proxySetting = $interfaceData['proxySetting'];

			//PolicySetからWorkspaceを切り離すAPIを実行
	        $policyData = array(
	            "data" => array(
	                array(
	                    "id" => $policyId,
	                    "type" => "policies"
	                )
	            )
	        );
			$apiResponse = delete_relationships_policy($hostname, $token, $policySetId, $policyData, $proxySetting);
			$statusCode = $apiResponse['StatusCode'];
			if($statusCode != 204){
				$ret['result'] = false;
			}else{
				$ret['result'] = true;
			}

			return $ret;
		}

		function setInterfaceInfo(){		
			$interfaceData = array();
			$retInterfaceInfo = getInterfaceInfo();
			if($retInterfaceInfo[0] == false){
				//エラーログ出力
				web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2]));
				$ret = false;
				return($ret);
			}

			//データをセット
			$interfaceData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
			$interfaceData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $interfaceData['proxySetting'] = array();
            $interfaceData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $interfaceData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

			return($interfaceData);
		}

	}

	$server = new HTML_AJAX_Server();
	$db_access = new Db_Access();
	$server->registerClass($db_access);
	$server->handleRequest();

?>

