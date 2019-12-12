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
//      AnsibleTower 作業実行管理(Tower側) クラス
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

require_once($root_dir_path . "/libs/commonlibs/common_php_functions.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerCommonLib.php");   
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/setenv.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/MessageTemplateStorageHolder.php");

require_once($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleVault.php');

$rest_api_command = $root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/";
require_once($rest_api_command . "AnsibleTowerRestApiProjects.php");
require_once($rest_api_command . "AnsibleTowerRestApiCredentials.php");
require_once($rest_api_command . "AnsibleTowerRestApiInventories.php");
require_once($rest_api_command . "AnsibleTowerRestApiInventoryHosts.php");
require_once($rest_api_command . "AnsibleTowerRestApiJobs.php");
require_once($rest_api_command . "AnsibleTowerRestApiJobTemplates.php");
require_once($rest_api_command . "AnsibleTowerRestApiWorkflowJobs.php");
require_once($rest_api_command . "AnsibleTowerRestApiWorkflowJobNodes.php");
require_once($rest_api_command . "AnsibleTowerRestApiWorkflowJobTemplates.php");
require_once($rest_api_command . "AnsibleTowerRestApiWorkflowJobTemplateNodes.php");

require_once($rest_api_command . "AnsibleTowerRestApiOrganization.php");
require_once($rest_api_command . "AnsibleTowerRestApiInstanceGroups.php");
require_once($rest_api_command . "AnsibleTowerRestApiConfig.php");

class ExecuteDirector {

    private $restApiCaller;
    private $logger;
    private $dbAccess;
    private $exec_out_dir;

    function __construct($restApiCaller, $logger, $dbAccess, $exec_out_dir, $JobTemplatePropertyParameterAry=array(),$JobTemplatePropertyNameAry=array()) {
        $this->restApiCaller = $restApiCaller;
        $this->logger = $logger;
        $this->dbAccess = $dbAccess;
        $this->exec_out_dir = $exec_out_dir;
        $this->JobTemplatePropertyParameterAry = $JobTemplatePropertyParameterAry;
        $this->JobTemplatePropertyNameAry      = $JobTemplatePropertyNameAry;

        $this->objMTS = MessageTemplateStorageHolder::getMTS();
    }

    function build($exeInsRow, $ifInfoRow) {

        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        $execution_no = $exeInsRow['EXECUTION_NO'];

        $virtualenv_name = $exeInsRow['I_VIRTUALENV_NAME'];

        // Towerのvirtualenv確認
        $virtualenv_name_ok = false;
        if($virtualenv_name != "") {
            $response_array = AnsibleTowerRestApiConfig::get($this->restApiCaller);
            if($response_array['success'] == false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040026",array($virtualenv_name));
                $this->errorLogOut($errorMessage);
                return false;
            }
            if( isset($response_array['responseContents']['custom_virtualenvs'] )) {
                foreach($response_array['responseContents']['custom_virtualenvs'] as $no=>$name) {
                    if($name == $virtualenv_name) {
                        $virtualenv_name_ok = true;
                        break;
                    }
                }
            }
            if($virtualenv_name_ok === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040027",array($virtualenv_name));
                $this->errorLogOut($errorMessage);
                return false;
            }
        }


        $OrganizationName = trim($ifInfoRow['ANSTWR_ORGANIZATION']);
        if(strlen($OrganizationName) != 0) {
            //組織情報取得
            //   [[Inventory]]
            $query = "?name=" . $OrganizationName;
            $response_array = AnsibleTowerRestApiOrganizations::getAll($this->restApiCaller, $query);
            $this->logger->trace(var_export($response_array, true));
            if($response_array['success'] == false) {
                $this->logger->error($response_array['responseContents']['errorMessage']);
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040024",array($OrganizationName));
                $this->errorLogOut($errorMessage);
                return false;
            }
            if(count($response_array['responseContents']) === 0
                || array_key_exists("id", $response_array['responseContents'][0]) == false) {
                $this->logger->error("No inventory id. (prepare)");
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040025",array($OrganizationName));
                $this->errorLogOut($errorMessage);
                return false;
            }
            $OrganizationId = $response_array['responseContents'][0]['id'];
        } else {
            // 組織名未登録
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040030");
            $this->errorLogOut($errorMessage);
            return false;
        }

        // Host情報取得
        $inventoryForEachCredentials = array();
        $ret = $this->getHostInfo($exeInsRow, $inventoryForEachCredentials);
        if($ret == false) {
            // array[40002] = "ホスト情報の取得に失敗しました。";
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040002");
            $this->errorLogOut($errorMessage);
            return -1;
        }
//------------

        $ret = $this->prepareProject($execution_no, $ifInfoRow['ANSIBLE_STORAGE_PATH_ANS']); 
        if($ret == false) {
            // array[40001] = "SCM更新作業に失敗しました。";
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040001");
            $this->errorLogOut($errorMessage);
            return -1;
        }

        // project生成
        $projectId = $this->createProject($execution_no,$OrganizationId,$virtualenv_name);
        if($projectId == -1) {
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040003");
            $this->errorLogOut($errorMessage);
            return -1;
        }

        // ansible vault認証情報生成
        $vault_credentialId = -1;
        if($vg_tower_driver_name != "pioneer") {
           $vaultobj = new AnsibleVault();
           list($ret,$dir,$file,$vault_password) = $vaultobj->getValutPasswdFileInfo();
           if($ret === false) {
               $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000080");
               $this->errorLogOut($errorMessage);
               return -1;
           }
           unset($vaultobj);
           $vault_credentialId = $this->createVaultCredential($execution_no, $vault_password, $OrganizationId);
           if($vault_credentialId == -1) {
               $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040031");
               $this->errorLogOut($errorMessage);
               return -1;
           }
        }

        $jobTemplateIds = array();
        $loopCount = 1;
        foreach($inventoryForEachCredentials as $dummy => $data) {
            // 認証情報生成
            $credentialId = $this->createEachCredential($execution_no, $loopCount, $data['credential'],$OrganizationId);
            if($credentialId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040004");
                $this->errorLogOut($errorMessage);
                return -1;
            }

            // インベントリ生成
            $inventoryId = $this->createEachInventory($execution_no, $loopCount, $data['inventory'],$OrganizationId);
            if($inventoryId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040005");
                $this->errorLogOut($errorMessage);
                return -1;
            }

            // ジョブテンプレート生成
            $jobTemplateId = $this->createEachJobTemplate($execution_no, $loopCount, $projectId, $credentialId, $vault_credentialId, $inventoryId, $exeInsRow['RUN_MODE']);
            if($jobTemplateId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040006");
                $this->errorLogOut($errorMessage);
                return -1;
            }

            $jobTemplateIds[] = $jobTemplateId;
            $loopCount++;
        }

        // ジョブテンプレートをワークフローに結合
        $workflowTplId = $this->createWorkflowJobTemplate($execution_no, $jobTemplateIds, $OrganizationId);
        if($workflowTplId == -1) {
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040007");
            $this->errorLogOut($errorMessage);
            return -1;
        }

        return $workflowTplId;
    }

    function delete($execution_no) {

        $this->logger->trace(__METHOD__);

        global $vg_tower_driver_name;
        $allResult = true;

        // ジョブスライス設定有無判定
        $search_name = sprintf(AnsibleTowerRestApiJobTemplates::SEARCH_IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));
        $query = sprintf("?name__startswith=%s",$search_name);
        $response_array = AnsibleTowerRestApiJobTemplates::getAll($this->restApiCaller, $query);

        $job_slice_use = false;
        $dummy = array();
        $IDData = array();
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == true) {
            if(@count($response_array['responseContents']) != 0) {
                if($response_array['responseContents'][0]['job_slice_count'] > 1) {
                    $job_slice_use = true;
                    // ジョブスライスされたジョブIDを取得
                    $dummy1 = array();
                    $job_id_get     = true;
                    $job_status_get = false;
                    $job_stdout_get = false;
                    $this->SliceJobsMonitoring($execution_no,$dummy1,$job_id_get,$job_status_get,$job_stdout_get, $IDData);
                    // 戻り値は判定しない
                }
            }
        }
        $allResult = true;
        $ret = $this->cleanUpPreparedProjectDirectory($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        $ret = $this->cleanUpJob($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        $ret = $this->cleanUpWorkflowJob($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        $ret = $this->cleanUpJobTemplate($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        $ret = $this->cleanUpWorkflowJobTemplate($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        if($job_slice_use = true) {
            // ジョブスライスされたワークフロージョブを削除
            $this->cleanUpSliceJobs($execution_no,$job_slice_use,$IDData);
        }
        $ret = $this->cleanUpProject($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        $ret = $this->cleanUpCredential($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        if($vg_tower_driver_name != "pioneer") {
            $ret = $this->cleanUpVaultCredential($execution_no);
            if($ret == false) {
                $allResult = false;
            }
        }
        $ret = $this->cleanUpInventory($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        return $allResult;
    }

    function launchWorkflow($wfJobTplId) {

        $this->logger->trace(__METHOD__);

        $param = array();

        $param = array("wfJobTplId" => $wfJobTplId);

        $response_array = AnsibleTowerRestApiWorkflowJobTemplates::launch($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->error("No workflow-job id.");
            return -1;
        }

        $wfJobId = $response_array['responseContents']['id'];

        return $wfJobId;
    }

    private function prepareProject($execution_no, $dataRelayStoragePath) {
        global  $vg_tower_driver_type;
        global  $vg_tower_driver_id;
        global  $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        ///////////////////////////////////////////////////////////////////////
        // prepare必要情報取得
        ///////////////////////////////////////////////////////////////////////
        $prepareParam = array();

        $prepareParam['execution_no']     = $execution_no;
        $prepareParam['dataRelayStorage'] = $dataRelayStoragePath;
        $prepareParam['driver_type']      = $vg_tower_driver_type;
        $prepareParam['driver_id']        = $vg_tower_driver_id;
        $prepareParam['driver_name']      = $vg_tower_driver_name;

        //   [[Inventory]]
        $query = "?name=" . AnsibleTowerRestApiInventories::PREPARE_BUILD_INVENTORY_NAME;
        $response_array = AnsibleTowerRestApiInventories::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040009");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->error("No inventory id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040010");
            $this->errorLogOut($errorMessage);
            return false;
        }
        $inventoryId = $response_array['responseContents'][0]['id'];
        $prepareParam['inventory'] = $inventoryId;

        //   [[Inventory][host]]
        $response_array = AnsibleTowerRestApiInventoryHosts::getAllEachInventory($this->restApiCaller, $inventoryId);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040022");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->error("No invenroty-host id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040023");
            $this->errorLogOut($errorMessage);
            return false;
        }
        // ホストがあるかのチェックのみ

        //   [[project]]
        $query = "?name=" . AnsibleTowerRestApiProjects::PREPARE_BUILD_PROJECT_NAME;
        $response_array = AnsibleTowerRestApiProjects::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040011");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->error("No project id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040012");
            $this->errorLogOut($errorMessage);
            return false;
        }
        $projectId = $response_array['responseContents'][0]['id'];
        $prepareParam['project'] = $projectId;

        $prepareParam['playbook'] = AnsibleTowerRestApiJobTemplates::LAUNCH_PLAYBOOK_NAME; // 固定

        //   [[Credential]]
        $query = "?name=" . AnsibleTowerRestApiCredentials::PREPARE_BUILD_CREDENTIAL_NAME;
        $response_array = AnsibleTowerRestApiCredentials::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040013");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->error("No credential id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040014");
            $this->errorLogOut($errorMessage);
            return false;
        }
        $credentialId = $response_array['responseContents'][0]['id'];
        $prepareParam['credential'] = $credentialId;

        ///////////////////////////////////////////////////////////////////////
        // prepareJobTemplate作成
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("prepare build");

        $response_array = AnsibleTowerRestApiJobTemplates::postForPrepare($this->restApiCaller, $prepareParam);
        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040015");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->error("No job-template id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040016");
            $this->errorLogOut($errorMessage);
            return false;
        }
        $jobTplId = $response_array['responseContents']['id'];

        ///////////////////////////////////////////////////////////////////////
        // prepare実行
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("build request");

        $param = array();

        $param['jobTplId'] = $jobTplId;

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiJobTemplates::launch($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->error($response_array['responseContents']['errorMessage']);
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040017");
            $this->errorLogOut($errorMessage);
            return false;
        }
        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->error("No job id. (prepare)");
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040018");
            $this->errorLogOut($errorMessage);
            return false;
        }
        $jobId = $response_array['responseContents']['id'];

        ///////////////////////////////////////////////////////////////////////
        // prepare build実行完了待ち
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("Waiting for finish prepare build. Job Id: $jobId");

        $now = 0;
        $limit = 120;
        $sleepTime = 1; // sleep(sec)
        while(true) {

            if($now > $limit) {
                $this->logger->error("Time out (limit: " . $limit * $sleepTime . " sec)");
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040019");
                $this->errorLogOut($errorMessage);
                return false;
            }
            $now++;
            sleep($sleepTime);

            $response_array = AnsibleTowerRestApiJobs::get($this->restApiCaller, $jobId);

            $responseContents = $response_array['responseContents'];
            $this->logger->trace(__METHOD__ . " / " . __LINE__);
            $this->logger->trace("rest_success: " . $response_array['success']);
            $job_status = array_key_exists("status", $responseContents) ? $responseContents['status'] : "";
            $this->logger->trace("job_status: " . $job_status);
            $job_failed = array_key_exists("failed", $responseContents) ? $responseContents['failed'] : "";
            $this->logger->trace("job_failed: " . $job_failed);
            $this->logger->trace("wait count: " . $now);

            if($response_array['success'] == false) {
                $this->logger->error($response_array['responseContents']['errorMessage']);
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040020");
                $this->errorLogOut($errorMessage);
                return false;
            }

            $responseContents = $response_array['responseContents'];
            if((array_key_exists("status", $responseContents) &&
                                                ($responseContents['status'] == "failed" ||
                                                 $responseContents['status'] == "error" ||
                                                 $responseContents['status'] == "canceled")) ||
                (array_key_exists("failed", $responseContents) && $responseContents['failed'] == true)) {
                $this->logger->error("parepare failed. execution_no:$execution_no");
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040021" . $responseContents['status']);
                $this->errorLogOut($errorMessage);
                return false;
            }

            if(array_key_exists("status", $responseContents) && $responseContents['status'] == "successful" &&
                array_key_exists("failed", $responseContents) && $responseContents['failed'] == false) {
                $this->logger->trace("prepare successful. execution_no:$execution_no");

                return true;
            }
        }
    } // prepareProject

    private function cleanUpPreparedProjectDirectory($execution_no) {
        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        ///////////////////////////////////////////////////////////////////////
        // cleanup必要情報取得
        ///////////////////////////////////////////////////////////////////////
        $cleanupParam = array();
        $cleanupParam['execution_no'] = $execution_no;
        $cleanupParam['driver_name']  = $vg_tower_driver_name;

        //   [[Inventory]]
        $query = "?name=" . AnsibleTowerRestApiInventories::PREPARE_BUILD_INVENTORY_NAME;
        $response_array = AnsibleTowerRestApiInventories::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->debug("No inventory id. (cleanup)");
            return false;
        }
        $inventoryId = $response_array['responseContents'][0]['id'];
        $cleanupParam['inventory'] = $inventoryId;

        //   [[project]]
        $query = "?name=" . AnsibleTowerRestApiProjects::CLEANUP_PREPARED_BUILD_PROJECT_NAME;
        $response_array = AnsibleTowerRestApiProjects::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->debug("No project id. (cleanup)");
            return false;
        }
        $projectId = $response_array['responseContents'][0]['id'];
        $cleanupParam['project'] = $projectId;

        $cleanupParam['playbook'] = AnsibleTowerRestApiJobTemplates::LAUNCH_PLAYBOOK_NAME; // 固定

        //   [[Credential]]
        $query = "?name=" . AnsibleTowerRestApiCredentials::PREPARE_BUILD_CREDENTIAL_NAME;
        $response_array = AnsibleTowerRestApiCredentials::getAll($this->restApiCaller, $query);
        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }
        if(count($response_array['responseContents']) === 0
            || array_key_exists("id", $response_array['responseContents'][0]) == false) {
            $this->logger->debug("No credential id. (cleanup)");
            return false;
        }
        $credentialId = $response_array['responseContents'][0]['id'];
        $cleanupParam['credential'] = $credentialId;

        ///////////////////////////////////////////////////////////////////////
        // cleanupJobTemplate作成
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("cleanup prepared project dir");

        $response_array = AnsibleTowerRestApiJobTemplates::postForCleanupPreparedProjectDirectory($this->restApiCaller, $cleanupParam);
        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }
        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No job-template id. (cleanup)");
            return false;
        }
        $jobTplId = $response_array['responseContents']['id'];

        ///////////////////////////////////////////////////////////////////////
        // cleanup実行
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("cleanup request");

        $param = array();

        $param['jobTplId'] = $jobTplId;

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiJobTemplates::launch($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }
        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No job id. (cleanup)");
            return false;
        }
        $jobId = $response_array['responseContents']['id'];

        ///////////////////////////////////////////////////////////////////////
        // cleanup prepare project実行完了待ち
        ///////////////////////////////////////////////////////////////////////
        $this->logger->trace("Waiting for finish cleanup prepared project. Job Id: $jobId");

        $now = 0;
        $limit = 120;
        $sleepTime = 1; // sleep(sec)
        while(true) {

            if($now > $limit) {
                $this->logger->debug("Time out (limit: " . $limit * $sleepTime . " sec)");
                return false;
            }
            $now++;
            sleep($sleepTime);

            $response_array = AnsibleTowerRestApiJobs::get($this->restApiCaller, $jobId);

            $responseContents = $response_array['responseContents'];
            $this->logger->trace(__METHOD__ . " / " . __LINE__);
            $this->logger->trace("rest_success: " . $response_array['success']);
            $job_status = array_key_exists("status", $responseContents) ? $responseContents['status'] : "";
            $this->logger->trace("job_status: " . $job_status);
            $job_failed = array_key_exists("failed", $responseContents) ? $responseContents['failed'] : "";
            $this->logger->trace("job_failed: " . $job_failed);
            $this->logger->trace("wait count: " . $now);

            if($response_array['success'] == false) {
                $this->logger->debug($response_array['responseContents']['errorMessage']);
                return false;
            }

            $responseContents = $response_array['responseContents'];
            if((array_key_exists("status", $responseContents) &&
                                                ($responseContents['status'] == "failed" ||
                                                 $responseContents['status'] == "error" ||
                                                 $responseContents['status'] == "canceled")) ||
                (array_key_exists("failed", $responseContents) && $responseContents['failed'] == true)) {
                $this->logger->debug("Cleanup failed. execution_no:$execution_no");
                return false;
            }


            if(array_key_exists("status", $responseContents) && $responseContents['status'] == "successful" &&
                array_key_exists("failed", $responseContents) && $responseContents['failed'] == false) {
                $this->logger->trace("Cleanup successful. execution_no:$execution_no");

                return true;
            }
        }
    }

    private function getHostInfo($exeInsRow, &$inventoryForEachCredentials) {

        global $vg_ansible_pho_linkDB;

        $this->logger->trace(__METHOD__);

        $condition = array(
            "OPERATION_NO_UAPK" => $exeInsRow['OPERATION_NO_UAPK'],
            "PATTERN_ID" => $exeInsRow['PATTERN_ID'],
        );
        $rows = $this->dbAccess->selectRowsUseBind($vg_ansible_pho_linkDB, false, $condition);

        foreach($rows as $ptnOpHostLink) {
            $hostInfo = $this->dbAccess->selectRow("C_STM_LIST", $ptnOpHostLink['SYSTEM_ID']);

            if(empty($hostInfo)) {
                $this->logger->error("Not exists or disabled host. SYSTEM_ID: " . $ptnOpHostLink['SYSTEM_ID']);
                return false;
            }

            $sshPrivateKey = "";
            if(!empty($hostInfo['CONN_SSH_KEY_FILE'])) {
                $sshPrivateKey = getSshKeyFileContent($hostInfo['SYSTEM_ID'], $hostInfo['CONN_SSH_KEY_FILE']);
            }

            $instanceGroupId = null;
            if(!empty($hostInfo['ANSTWR_INSTANCE_GROUP_NAME'])) {
                // Towerのインスタンスグループ情報取得
                $response_array = AnsibleTowerRestApiInstanceGroups::getAll($this->restApiCaller);
                if($response_array['success'] == false) {
                    // 組織名未登録
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040028",array($hostInfo['ANSTWR_INSTANCE_GROUP_NAME']));
                    $this->errorLogOut($errorMessage);
                    return false;
                }

                foreach($response_array['responseContents'] as $info) {
                    if($info['name'] == $hostInfo['ANSTWR_INSTANCE_GROUP_NAME']) {
                        $instanceGroupId = $info['id'];
                    }
                }
                if($instanceGroupId == null) {
                    // インスタンスグループ未登録
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040029",array($hostInfo['ANSTWR_INSTANCE_GROUP_NAME']));
                    $this->errorLogOut($errorMessage);
                    return false;
                }                
            }

            // 配列のキーに使いたいだけ
            $key = $hostInfo['LOGIN_USER'] . $hostInfo['LOGIN_PW'] . $sshPrivateKey . $instanceGroupId;

            $username        = $hostInfo['LOGIN_USER'];
            $password        = $hostInfo['LOGIN_PW'];
            switch($hostInfo['LOGIN_AUTH_TYPE']) {
            case 1:   // 鍵認証
                $password      = "";
                break;
            case 2:   // パスワード認証
                $sshPrivateKey = "";
                break;
            default:  // 認証未指定
                $password      = "";
                $sshPrivateKey = "";
                break;
            }
            $credential = array(
                "username"        => $username,
                "password"        => $password,
                "ssh_private_key" => $sshPrivateKey
            );

            $inventory = array();
            if(array_key_exists($key, $inventoryForEachCredentials)) {
                $inventory = $inventoryForEachCredentials[$key]['inventory'];
            } else {
                // 初回のみインベントリグループ指定
                $inventory['instanceGroupId'] = $instanceGroupId;
            }

            // ホスト情報
            $hostData = array();
            // ホストアドレス指定方式
            // null or 1 がIP方式 2 がホスト名方式
            if(empty($exeInsRow['I_ANS_HOST_DESIGNATE_TYPE_ID']) ||
                $exeInsRow['I_ANS_HOST_DESIGNATE_TYPE_ID'] == 1) {
                $hostData['ipAddress'] = $hostInfo['IP_ADDRESS'];
            }

            // WinRM接続
            if(empty($exeInsRow['I_ANS_WINRM_ID'])) {
                $exeInsRow['I_ANS_WINRM_ID'] = 0;
            }
            $hostData['winrm'] = $exeInsRow['I_ANS_WINRM_ID'];
            if($exeInsRow['I_ANS_WINRM_ID'] == 1) {
                if(empty($hostInfo['WINRM_PORT'])) {
                    $hostInfo['WINRM_PORT'] = LC_WINRM_PORT;
                }
                $hostData['winrmPort'] = $hostInfo['WINRM_PORT'];

                if(empty($hostInfo['LOGIN_USER'])) {
                    $this->logger->error("Need 'LOGIN_USER'.");
                    return false;
                }
                $hostData['username'] = $hostInfo['LOGIN_USER'];

                if(empty($hostInfo['LOGIN_PW'])) {
                    $this->logger->error("Need 'LOGIN_PW'.");
                    return false;
                }
                $hostData['password'] = $hostInfo['LOGIN_PW'];

                if(strlen($hostInfo['WINRM_SSL_CA_FILE']) != 0) {
                    $filePath = "winrm_ca_files/" . addPadding($hostInfo['SYSTEM_ID']) . "-" . $hostInfo['WINRM_SSL_CA_FILE'];
                    $hostData['ansible_winrm_ca_trust_path'] = $filePath;
                }
            }

            $hostData['hosts_extra_args'] = $hostInfo['HOSTS_EXTRA_ARGS'];

            $hostData['ansible_ssh_extra_args'] = $hostInfo['SSH_EXTRA_ARGS'];

            $inventory['hosts'][$hostInfo['HOSTNAME']] = $hostData;

            $inventoryForEachCredentials[$key] = array(
                "credential" => $credential,
                "inventory" => $inventory,
            );
        }

        $this->logger->trace(var_export($inventoryForEachCredentials, true));

        return true;
    }

    private function createProject($execution_no,$OrganizationId,$virtualenv_name) {
        $this->logger->trace(__METHOD__);

        $param = array();

        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;

        if(strlen($virtualenv_name) != 0) {
            $param['custom_virtualenv'] = $virtualenv_name;
        }

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiProjects::post($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No project id.");
            return -1;
        }

        $projectId = $response_array['responseContents']['id'];
        return $projectId;
    }

    private function createEachCredential($execution_no, $loopCount, $credential,$OrganizationId) {

        $this->logger->trace(__METHOD__);

        $param = array();
        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;
        $param['loopCount'] = $loopCount;

        if(array_key_exists("username", $credential)) {
            $param['username'] = $credential['username'];
        }
        if(array_key_exists("password", $credential)) {
            $param['password'] = $credential['password'];
        }
        if(array_key_exists("ssh_private_key", $credential)) {
            $param['ssh_private_key'] = $credential['ssh_private_key'];
        }

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiCredentials::post($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No credential id.");
            return -1;
        }

        $credentialId = $response_array['responseContents']['id'];
        return $credentialId;
    }

    private function createVaultCredential($execution_no, $vault_password, $OrganizationId) {

        $this->logger->trace(__METHOD__);

        $param = array();
        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;

        $param['vault_password'] = $vault_password;

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiCredentials::vault_post($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No vault credential id.");
            return -1;
        }

        $vault_credentialId = $response_array['responseContents']['id'];
        return $vault_credentialId;
    }

    private function createEachInventory($execution_no, $loopCount, $inventory, $OrganizationId) {

        $this->logger->trace(__METHOD__);

        if(!array_key_exists("hosts", $inventory) || empty($inventory['hosts'])) {
            $this->logger->debug(__METHOD__ . " no hosts.");
            return -1;
        }

        // inventory
        $param = array();
        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;
        $param['loopCount'] = $loopCount;

        if(array_key_exists("instanceGroupId", $inventory) && !empty($inventory['instanceGroupId'])) {
            $param['instanceGroupId'] = $inventory['instanceGroupId'];
        }

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiInventories::post($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No inventory id.");
            return -1;
        }

        $inventoryId = $response_array['responseContents']['id'];

        $param['inventoryId'] = $inventoryId;
        foreach($inventory['hosts'] as $hostname => $hostData) {

            unset($param['variables']);

            $param['name'] = $hostname;

            $variables_array = array();
            if(array_key_exists("ipAddress", $hostData) && !empty($hostData['ipAddress'])) {
                $variables_array[] = "ansible_ssh_host: " . $hostData['ipAddress'];
            }

            if($hostData['winrm'] == 1) {
                $variables_array[] = "ansible_connection: winrm";
                $variables_array[] = "ansible_ssh_port: " . $hostData['winrmPort'];
                if( isset($hostData['ansible_winrm_ca_trust_path']) ) {
                    $variables_array[] = "ansible_winrm_ca_trust_path: " . $hostData['ansible_winrm_ca_trust_path'];
                }
            }

            if(strlen(trim($hostData['ansible_ssh_extra_args'])) != 0) {
                $variables_array[] = "ansible_ssh_extra_args: " . trim($hostData['ansible_ssh_extra_args']);
            }

            // インベントりファイル追加オプションの空白行を取り除く
            $yaml_array = explode("\n", $hostData['hosts_extra_args']);
            foreach($yaml_array as $record) {
                if(strlen(trim($record)) == 0) {
                    continue;
                }
                $variables_array[] = $record;   
            }

            // インベントりファイル追加オプションを設定
            if(count($variables_array) != 0) {
                $param['variables'] = implode("\n", $variables_array);
            }

            $this->logger->trace(var_export($param, true));

            $response_array = AnsibleTowerRestApiInventoryHosts::post($this->restApiCaller, $param);

            $this->logger->trace(var_export($response_array, true));

            if($response_array['success'] == false) {
                $this->logger->debug($response_array['responseContents']['errorMessage']);
                return -1;
            }

        }

        return $inventoryId;
    }

    private function createEachJobTemplate($execution_no, $loopCount, $projectId, $credentialId, $vault_credentialId, $inventoryId, $runMode) {
        global $vg_parent_playbook_name;

        $this->logger->trace(__METHOD__);

        $param = array();

        $param['execution_no'] = $execution_no;
        $param['loopCount'] = $loopCount;
        $param['inventory'] = $inventoryId;
        $param['project'] = $projectId;
        $param['playbook'] = $vg_parent_playbook_name;
        $param['credential'] = $credentialId;

        if($vault_credentialId != -1) {
            $param['vault_credential'] = $vault_credentialId;
        }

        $addparam = array();
        foreach($this->JobTemplatePropertyParameterAry as $key=>$val)
        {
            $addparam[$key] = $val;
        }

        if($runMode == DRY_RUN) {
            $param['job_type'] = "check";
        }

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiJobTemplates::post($this->restApiCaller, $param , $addparam);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No job-template id.");
            return -1;
        }

        $jobTemplateId = $response_array['responseContents']['id'];
        return $jobTemplateId;
    }

    private function createWorkflowJobTemplate($execution_no, $jobTemplateIds, $OrganizationId) {

        $this->logger->trace(__METHOD__);

        if(empty($jobTemplateIds)) {
            $this->logger->debug(__METHOD__ . " no job templates.");
            return -1;
        }

        $param = array();

        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiWorkflowJobTemplates::post($this->restApiCaller, $param);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return -1;
        }

        if(array_key_exists("id", $response_array['responseContents']) == false) {
            $this->logger->debug("No workflow-job-template id.");
            return -1;
        }

        $workflowTplId = $response_array['responseContents']['id'];

        $param['workflowTplId'] = $workflowTplId;

        $loopCount = 1;
        foreach($jobTemplateIds as $jobtplId) {

            $param['jobtplId'] = $jobtplId;
            $param['loopCount'] = $loopCount;

            $this->logger->trace(var_export($param, true));

            $response_array = AnsibleTowerRestApiWorkflowJobTemplateNodes::post($this->restApiCaller, $param);

            $this->logger->trace(var_export($response_array, true));

            if($response_array['success'] == false) {
                $this->logger->debug($response_array['responseContents']['errorMessage']);
                return -1;
            }

            $loopCount++;
        }

        return $workflowTplId;
    }

    private function cleanUpProject($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiProjects::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up project finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpCredential($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiCredentials::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up credentials finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpVaultCredential($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiCredentials::deleteVault($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up vault credentials finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpInventory($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiInventoryHosts::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $response_array = AnsibleTowerRestApiInventories::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up inventories finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpJobTemplate($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiJobTemplates::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $response_array = AnsibleTowerRestApiJobTemplates::deleteRelatedCurrnetExecutionForPrepare($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up job templates finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpWorkflowJobTemplate($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiWorkflowJobTemplateNodes::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $response_array = AnsibleTowerRestApiWorkflowJobTemplates::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up workflow job template finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpJob($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiJobs::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $response_array = AnsibleTowerRestApiJobs::deleteRelatedCurrnetExecutionForPrepare($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up job templates finished. (execution_no: $execution_no)");

        return true;
    }

    private function cleanUpWorkflowJob($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiWorkflowJobs::deleteRelatedCurrnetExecution($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up workflow job finished. (execution_no: $execution_no)");

        return true;
    }
    private function cleanUpSliceJobs($execution_no,$job_slice_use,$IDData) {

        $this->logger->trace(__METHOD__);

        if($job_slice_use === false) {
            return true;
        }

        // ジョブスライスされたジョブは後方の処理で削除される。

        // ジョブスライスされたワークフロージョブを削除
        if(isset($IDData['workflow_job_id']) === true) {
            foreach($IDData['workflow_job_id'] as $workflow_job_id) {
                // エラーでも先に進む
                $response_array = AnsibleTowerRestApiWorkflowJobs::delete($this->restApiCaller, $workflow_job_id);
                if($response_array['success'] == false) {
                    $this->logger->error(var_export($response_array, true));
                }
            }
        }
        return true;
    }


    function monitoring($toProcessRow, $ansibleTowerIfInfo) {

        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        $execution_no = $toProcessRow['EXECUTION_NO'];

        $search_name = sprintf(AnsibleTowerRestApiJobTemplates::SEARCH_IDENTIFIED_NAME_PREFIX,$vg_tower_driver_name,addPadding($execution_no));
        $query = sprintf("?name__startswith=%s",$search_name);
        $response_array = AnsibleTowerRestApiJobTemplates::getAll($this->restApiCaller, $query);

        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error("Faild to RestAPI access job templates. query:".$query);
            return EXCEPTION;
        }
        if(@count($response_array['responseContents']) === 0) {
            $this->logger->error("Faild to get job templates contents. query:".$query);
            $this->logger->error(var_export($response_array, true));
            return EXCEPTION;
        }

        // ジョブスライス数を判定
        $job_slice_use = true;
        if($response_array['responseContents'][0]['job_slice_count'] == 1) {
            $job_slice_use = false;
        }

        // AnsibleTower Status チェック
        list($status, $wfJobId) = $this->checkWorkflowJobStatus($execution_no,$job_slice_use);
        if($status == EXCEPTION || $wfJobId == -1) {
            return $status;
        }

        // Ansibleログ書き出し
        $ret = $this->createAnsibleLogs($execution_no, $ansibleTowerIfInfo['ANSIBLE_STORAGE_PATH_LNX'], $wfJobId, $job_slice_use);  
        if($ret == false) {
            $status = EXCEPTION;
        }

        return $status;
    }

    function SliceJobsMonitoring($execution_no,&$SliceJobsData,
                                 $job_id_get,$job_status_get,$job_stdout_get,
                                &$IDData=array()) {

        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        // ジョブテンプレート情報検索　ita_%s_executions_jobtpl_%s"
        $search_name = sprintf(AnsibleTowerRestApiJobTemplates::SEARCH_IDENTIFIED_NAME_PREFIX,
                               $vg_tower_driver_name,addPadding($execution_no));
        $query = sprintf("?name__startswith=%s",$search_name);
        $response_array = AnsibleTowerRestApiJobTemplates::getAll($this->restApiCaller, $query);

        $this->logger->trace(var_export($response_array, true));
        if($response_array['success'] == false) {
            $this->logger->error("Faild to RestAPI access job template. query:".$query);
            return EXCEPTION;
        }
        if(@count($response_array['responseContents']) === 0) {
            $this->logger->error("Faild to get job template contents. query:".$query);
            $this->logger->error(var_export($response_array, true));
            return EXCEPTION;
        }

        foreach($response_array['responseContents'] as $job_tmp_row) {
            // ジョブテンプレート有無判定
            if(isset($job_tmp_row['id']) === false) {
                $this->logger->error("Faild to get job template id. query:".$query);
                $this->logger->error(var_export($response_array, true));
                return EXCEPTION;
            }

            //$this->logger->error("job templates id:[".$job_tmp_row['id']."]");

            $query = sprintf("%s/slice_workflow_jobs/",$job_tmp_row['id']);

            // ジョブテンプレートIDから分割されたワークフロージョブ情報取得
            $response_array_workflow_jobs_templates = AnsibleTowerRestApiJobTemplates::getAll($this->restApiCaller, $query);
            if($response_array_workflow_jobs_templates['success'] == false) {
                $this->logger->error("Faild to RestAPI access slice workflow job. query:" . $query);
                return EXCEPTION;
            }
            if(@count($response_array_workflow_jobs_templates['responseContents']) === 0 ) {
                $this->logger->error("Faild to get lice workflow job query:".$query);
                $this->logger->error(var_export($response_array_workflow_jobs_templates, true));
                return EXCEPTION;
            }
            foreach($response_array_workflow_jobs_templates['responseContents'] as $workflow_jobs_templates_row) {
                // ワークフロージョブ有無判定
                if(isset($job_tmp_row['id']) === false) {
                    $this->logger->error("Faild to get workflow job. query:".$query);
                    $this->logger->error(var_export($response_array_workflow_jobs_templates, true));
                    return EXCEPTION;
                }
 
                // ワークフロージョブID
                $workflow_job_id = $workflow_jobs_templates_row['id'];
                $query = sprintf("%s/workflow_nodes/",$workflow_job_id);

                // ワークフロージョブID退避
                $IDData['workflow_job_id'][] = $workflow_job_id;

               //$this->logger->error("workflow job id:[" . $workflow_job_id . "]");

                // ワークフロージョブIDからワークフロージョブノード情報取得 
                $response_array_workflow_nodes = AnsibleTowerRestApiWorkflowJobs::getAll($this->restApiCaller, $query);
                if($response_array_workflow_nodes['success'] == false) {
                    $this->logger->error("Faild to RestAPI access workflow job node. query:" . $query);
                    return EXCEPTION;
                }
                if(@count($response_array_workflow_nodes['responseContents']) === 0 ) {
                    $this->logger->error("Faild to get workflow job node contents. query:".$query);
                    $this->logger->error(var_export($response_array_workflow_nodes, true));
                    return EXCEPTION;
                }
                $stdout_log = "";

                foreach($response_array_workflow_nodes['responseContents'] as $workflow_nodes_row) {
                    // ワークフロージョブノード有無
                    if(isset($workflow_nodes_row['id']) === false) {
                        $this->logger->error("Faild to get workflow job node. query:".$query);
                        $this->logger->error(var_export($response_array_workflow_nodes, true));
                        return EXCEPTION;
                    }
                    // ワークフロージョブノードID退避
                    $IDData['workflow_job_node_id'][] = $workflow_nodes_row['id'];

                    // ジョブ有無判定
                    if(isset($workflow_nodes_row['summary_fields']['job']["id"]) === false) {
                        // summary_fields が生成されていない場合がある。
                        if($job_stdout_get === false) {
                            $this->logger->error("Faild to get jobs id. query:".$query);
                            $this->logger->error(var_export($response_array_workflow_nodes, true));
                        }
                        return EXCEPTION;
                    }

                    // ジョブ名
                    $workflow_job_name = $workflow_nodes_row['summary_fields']['workflow_job']["name"];
                    // ジョブID
                    $jobId = $workflow_nodes_row['summary_fields']['job']["id"];

                    // ジョブID退避
                    if($job_id_get === true) {
                        $IDData['job_id'][] = $jobId;
                    }
                    if(($job_status_get === false) &&
                       ($job_stdout_get === false))
                    {
                        continue;
                    }

                    $query = sprintf("%s",$jobId);
                    // ジョブ情報取得
                    $response_array_jobs = AnsibleTowerRestApiJobs::get($this->restApiCaller, $query);
                    if($response_array_jobs['success'] == false) {
                        $this->logger->error("Faild to RestAPI access job detail. query:".$query);
                        return EXCEPTION;
                    }
                    if(@count($response_array_jobs['responseContents']) === 0 ) {
                        $this->logger->error("Faild to get job detail. query:".$query);
                        $this->logger->error(var_export($response_array_jobs, true));
                        return EXCEPTION;
                    }

                    // ジョブ実行状態判定
                    $jobData = $response_array_jobs['responseContents'];
                    if(!array_key_exists("id",     $jobData) ||
                       !array_key_exists("status", $jobData) ||
                       !array_key_exists("failed", $jobData)) {
                        $this->logger->error("Not expected data. query:".$query);
                        $this->logger->error(var_export($response_array_jobs, true));
                        return EXCEPTION;
                    }
                    // ジョブの実行状態か必要な場合
                    if($job_status_get === true) {
                       if($jobData['status'] != "successful") {
                           return FAILURE;
                       }
                    }  
                    // ジョブの標準出力が必要か判定
                    if($job_stdout_get === false) {
                       continue;
                    }

                    // 標準出力情報取得
                    $response_array_stdout = AnsibleTowerRestApiJobs::getStdOut($this->restApiCaller, $jobId);
                    if($response_array_stdout['success'] == false) {
                        $this->logger->error("Faild to get job stdout.. " . print_r($response_array_stdout,true));
                        $response_array['responseContents'] = "Faild to get job stdout. " . $response_array['responseContents']['errorMessage'];
                    }
                    if(@count($response_array_stdout['responseContents']) === 0 ) {
                        $response_array_stdout['responseContents'] = "";
                    }

                    // inventory/project/credentialsの情報退避
                    // 複数Jobの場合は最後の情報を使用
                    $SliceJobsData[$workflow_job_name] = $jobData;

                    // ログを退避
                    $page = sprintf("(%d/%d)\n",$jobData['job_slice_number'],$jobData['job_slice_count']);
                    $stdout_log .= $page;
                    $stdout_log .= $response_array_stdout['responseContents'];
                    $SliceJobsData[$workflow_job_name]['result_stdout'] = $stdout_log;
                }
            }
        }
        return COMPLETE;
    }

    function checkWorkflowJobStatus($execution_no,&$job_slice_use) {

        $this->logger->trace(__METHOD__);
        $wfJobId = -1;

        $response_array = AnsibleTowerRestApiWorkflowJobs::getByExecutionNo($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return array(EXCEPTION, $wfJobId);
        }

        $workflowJobData = $response_array['responseContents'];
        if(!array_key_exists("id",     $workflowJobData) ||
            !array_key_exists("status", $workflowJobData) ||
            !array_key_exists("failed", $workflowJobData)) {
            $this->logger->debug("Not expected data.");
            return array(EXCEPTION, $wfJobId);
        }

        $wfJobId     = $workflowJobData['id'];
        $wfJobStatus = $workflowJobData['status'];
        $wfJobFailed = $workflowJobData['failed'];

        switch($wfJobStatus) {
            case "new":
            case "pending":
            case "waiting":
            case "running":
                $status = PROCESSING;
                break;
            case "successful":
                // 子Jobが全て成功であればCOMPLETE、ひとつでも失敗していればFAILURE
                // ジョブスライスの有無判定
                if($job_slice_use === true) {
                    $dummy1 = array();
                    $dummy2 = array();
                    $job_id_get     = false;
                    $job_status_get = true;
                    $job_stdout_get = false;
                    $status = $this->SliceJobsMonitoring($execution_no,$dummy1,$job_id_get,$job_status_get,$job_stdout_get, $dummy2);
                } else {
                    $status = $this->checkAllJobsStatus($wfJobId);
                }
                break;
            case "failed":
            case "error":
                $status = FAILURE;
                break;
            case "canceled":
                $status = SCRAM;
                // 子Jobのステータスは感知しない（100%キャンセルはできない）
                break;
            default:
                $status = EXCEPTION;
                break;
        }

        return array($status, $wfJobId);
    }

    function checkAllJobsStatus($wfJobId) {

        $this->logger->trace(__METHOD__);

        $query = "?workflow_job=" . $wfJobId;
        $this->logger->trace("AnsibleTowerRestApiWorkflowJobNodes::getAll / query = " . $query);
        $response_array = AnsibleTowerRestApiWorkflowJobNodes::getAll($this->restApiCaller, $query);
        if($response_array['success'] == false) {
            $this->logger->error("Faild to get workflow job node. " . $response_array['responseContents']['errorMessage']);
            return EXCEPTION;
        }

        $workflowJobNodeArray_restResult = $response_array['responseContents'];

        $this->logger->trace(var_export($workflowJobNodeArray_restResult, true));

        $workflowJobNodeArray_jobDataAdded = array();
        foreach($workflowJobNodeArray_restResult as $workflowJobNodeData) {
            if(empty($workflowJobNodeData['job'])) {
                $this->logger->error("Faild to get job. " . $response_array['responseContents']['errorMessage']);
                return EXCEPTION;
            }
            $jobId = $workflowJobNodeData['job'];
            $this->logger->trace("AnsibleTowerRestApiJobs::get / param = " . $jobId);
            $response_array = AnsibleTowerRestApiJobs::get($this->restApiCaller, $jobId);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to get job detail. " . $response_array['responseContents']['errorMessage']);
                return EXCEPTION;
            }

            $jobData = $response_array['responseContents'];
            $this->logger->trace(var_export($jobData, true));

            if(!array_key_exists("id",     $jobData) ||
                !array_key_exists("status", $jobData) ||
                !array_key_exists("failed", $jobData)) {
                $this->logger->debug("Not expected data.");
                return EXCEPTION;
            }
            if($jobData['status'] != "successful") {
                return FAILURE;
            }
        }

        // 全て成功
        return COMPLETE;
    }

    function createAnsibleLogs($execution_no, $dataRelayStoragePath, $wfJobId, $job_slice_use) {
        global $vg_tower_driver_type;
        global $vg_tower_driver_id;

        $this->logger->trace(__METHOD__);

        $execlogFilename        = "exec.log";
        $joblistFilename        = "joblist.txt";
        $outDirectoryPath       = $dataRelayStoragePath . "/" . $vg_tower_driver_type . '/' . $vg_tower_driver_id . '/' . addPadding($execution_no) . "/out";

        $execlogFullPath        = $outDirectoryPath . "/" . $execlogFilename;
        $joblistFullPath        = $outDirectoryPath . "/" . $joblistFilename;

        ////////////////////////////////////////////////////////////////
        // ジョブスライスの場合にジョブの標準出力を取得
        ////////////////////////////////////////////////////////////////
        $SliceJobsData          = array();
        if($job_slice_use === true) {
            $dummy = array();
            $job_id_get     = false;
            $job_status_get = false;
            $job_stdout_get = true;
            $status = $this->SliceJobsMonitoring($execution_no,$SliceJobsData,
                                                 $job_id_get,$job_status_get,$job_stdout_get, 
                                                 $dummyx2);
            // 戻り値はチェックしない
        }
        ////////////////////////////////////////////////////////////////
        // データ取得
        ////////////////////////////////////////////////////////////////
        $this->logger->trace("AnsibleTowerRestApiWorkflowJobs::get / param = " . $wfJobId);
        $response_array = AnsibleTowerRestApiWorkflowJobs::get($this->restApiCaller, $wfJobId);
        if($response_array['success'] == false) {
            $this->logger->error("Faild to get workflow job. " . $response_array['responseContents']['errorMessage']);
            return false;
        }

        $workflowJobData = $response_array['responseContents'];

        $this->logger->trace(var_export($workflowJobData, true));

        $query = "?workflow_job=" . $wfJobId;
        $this->logger->trace("AnsibleTowerRestApiWorkflowJobNodes::getAll / query = " . $query);
        $response_array = AnsibleTowerRestApiWorkflowJobNodes::getAll($this->restApiCaller, $query);
        if($response_array['success'] == false) {
            $this->logger->error("Faild to get workflow job node. " . $response_array['responseContents']['errorMessage']);
            return false;
        }

        $workflowJobNodeArray_restResult = $response_array['responseContents'];
        $this->logger->trace(var_export($workflowJobNodeArray_restResult, true));

        $workflowJobNodeArray_jobDataAdded = array();
        foreach($workflowJobNodeArray_restResult as $workflowJobNodeData) {
            if(empty($workflowJobNodeData['job'])) {
                // Nodeに対してJobが設定される前の状態が存在する
                continue;
            }
            $jobId = $workflowJobNodeData['job'];

            if($job_slice_use === false) {
                $this->logger->trace("AnsibleTowerRestApiJobs::get / param = " . $jobId);
                $response_array = AnsibleTowerRestApiJobs::get($this->restApiCaller, $jobId);
                if($response_array['success'] == false) {
                    $this->logger->error("Faild to get job detail. " . $response_array['responseContents']['errorMessage']);
                    return false;
                }

                $jobData = $response_array['responseContents'];
                $this->logger->trace(var_export($jobData, true));

                $response_array = AnsibleTowerRestApiJobs::getStdOut($this->restApiCaller, $jobId);

                if($response_array['success'] == false) {
                    $this->logger->error("Faild to get job stdout.. " . print_r($response_array,true));
                    // 標準出力が取得できなかった場合
                    $response_array['responseContents'] = "Faild to get job stdout. " . $response_array['responseContents']['errorMessage'];
                }
                $jobData['result_stdout'] = $response_array['responseContents'];

                $workflowJobNodeData['jobData'] = $jobData;

                $workflowJobNodeArray_jobDataAdded[] = $workflowJobNodeData;
            } else {
                $jobName = $workflowJobNodeData['summary_fields']['job']['name'];

                $jobData = "";
                if(isset($SliceJobsData[$jobName]) === true) {
                    $jobData = $SliceJobsData[$jobName];
                } else {
                    continue;
                }
                $workflowJobNodeData['jobData'] = $jobData;

                $workflowJobNodeArray_jobDataAdded[] = $workflowJobNodeData;
            }
        }

        ////////////////////////////////////////////////////////////////
        // 全体構造ファイル作成(無ければ)
        ////////////////////////////////////////////////////////////////
        if(is_dir($joblistFullPath)) {
            $this->logger->error("Error: '" . $joblistFullPath . "' is directory. Remove this.");
            return false;
        }

        ////////////////////////////////////////////////////////////////
        // ログファイル生成
        ////////////////////////////////////////////////////////////////
        $ret = $this->CreateLogs($workflowJobData,$workflowJobNodeArray_jobDataAdded,$joblistFullPath,$outDirectoryPath,$execlogFullPath,$job_slice_use);
        return $ret;
    }
    function CreateLogs($workflowJobData,$workflowJobNodeArray_jobDataAdded,$joblistFullPath,$outDirectoryPath,$execlogFullPath,$job_slice_use) {
        global  $vg_tower_driver_name;

        $contentArray = array();

        // workflow job data
        $contentArray[] = "workflow_name: " . $workflowJobData['name'];
        $contentArray[] = "started: " . $workflowJobData['started'];

        // node job data
        foreach($workflowJobNodeArray_jobDataAdded as $workflowJobNodeData) {
            $jobData = $workflowJobNodeData['jobData'];
            $contentArray[] = ""; // 空行
            $contentArray[] = "------------------------------------------------------------------------------------------------------------------------"; // セパレータ
            $contentArray[] = "node_job_name: " . $jobData['name'];

            $response_array = AnsibleTowerRestApiProjects::get($this->restApiCaller, $jobData['project']);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to get project. " . $response_array['responseContents']['errorMessage']);
                return false;
            }
            $projectData = $response_array['responseContents'];

            $contentArray[] = "  project_name: " . $projectData['name'];
            $contentArray[] = "  project_local_path: " . $projectData['local_path'];

            $response_array = AnsibleTowerRestApiCredentials::get($this->restApiCaller, $jobData['credential']);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to get credential. " . $response_array['responseContents']['errorMessage']);
                return false;
            }
            $credentialData = $response_array['responseContents'];

            $contentArray[] = "  credential_name: " . $credentialData['name'];
            $contentArray[] = "  credential_type: " . $credentialData['credential_type'];
            $contentArray[] = "  credential_inputs: " . json_encode($credentialData['inputs']);
            $contentArray[] = "  virtualenv: " . $projectData['custom_virtualenv'];
            if($job_slice_use === true) {
                $contentArray[] = "  job_slice_count: " . $jobData["job_slice_count"];
            }
            $query = sprintf("%s/instance_groups/",$jobData['inventory']);
            $response_array  = AnsibleTowerRestApiInventories::getAll($this->restApiCaller, $query);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to get inventory. " . $response_array['responseContents']['errorMessage']);
                return false;
            }
            $instance_group = "";
            if(isset($response_array['responseContents'][0]['name']) === true) {
                $instance_group = $response_array['responseContents'][0]['name'];
            }
            $contentArray[] = "  instance_group: " . $instance_group;

            $response_array = AnsibleTowerRestApiInventories::get($this->restApiCaller, $jobData['inventory']);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to get inventory. " . $response_array['responseContents']['errorMessage']);
                return false;
            }
            $inventoryData = $response_array['responseContents'];

            $response_array = AnsibleTowerRestApiInventoryHosts::getAllEachInventory($this->restApiCaller, $jobData['inventory']);

            if($response_array['success'] == false) {
                $this->logger->error("Faild to get hosts. " . $response_array['responseContents']['errorMessage']);
                return false;
            }
            $hostsData = $response_array['responseContents'];

            $contentArray[] = "  inventory_name: " . $inventoryData['name'];

            foreach($hostsData as $hostData) {
                $contentArray[] = "    host_name: " . $hostData['name'];
                $contentArray[] = "    host_variables: " . $hostData['variables'];
            }
        }
        $contentArray[] = ""; // 空行

        if(file_put_contents($joblistFullPath, join("\n", $contentArray)) === false) {
            $this->logger->error("Faild to write file. " . $joblistFullPath);
            return false;
        }

        ////////////////////////////////////////////////////////////////
        // 各WorkflowJobNode分のstdoutをファイル化
        ////////////////////////////////////////////////////////////////
        $jobFileList = array();
        foreach($workflowJobNodeArray_jobDataAdded as $workflowJobNodeData) {

            $jobData = $workflowJobNodeData['jobData'];
            $jobFileFullPath = $outDirectoryPath . "/" . $jobData['name'] . ".txt";
            if(file_put_contents($jobFileFullPath, $jobData['result_stdout']) === false) {
                $this->logger->error("Faild to write file. " . $jobFileFullPath);
                return false;
            }

            $jobFileList[$jobData['name']] = $jobFileFullPath;
        }

        ////////////////////////////////////////////////////////////////
        // 結合 & exec.log差し替え
        ////////////////////////////////////////////////////////////////

        // ファイル入出力排他処理
        $semaphore = getSemaphoreKey(); // 作業確認内の描画処理とロックを取り合う([webroot]monitor_execution\05_disp_taillog.php)
        try {
            $tryCount = 0;
            while(true) {
                if(sem_acquire($semaphore)) {
                    break;
                }
                usleep(100000); // 0.1秒遅延
                $tryCount++;
                if($tryCount > 50) {
                    $this->logger->error("Faild to lock file.");
                    return false;
                }
            }

            // LOCK用ファイルをロック中にやりたい処理 => 実ファイル書き込み
            $joblistContent = file_get_contents($joblistFullPath);
            if($joblistContent === false) {
                $this->logger->error("Faild to read file. " . $joblistFullPath);
                return false;
            }


            $execlogContent = $joblistContent;
            foreach($jobFileList as $jobName => $jobFileFullPath) {
                $execlogContent .= "\n";
                $execlogContent .= "========================================================================================================================\n"; // セパレータ
                $execlogContent .= "[job_stdout: " . $jobName . "]\n";
                $execlogContent .= "\n";
                $jobFileContent = file_get_contents($jobFileFullPath);
                if($jobFileContent === false) {
                    $this->logger->error("Faild to read file. " . $jobFileFullPath);
                    return false;
                }
                $execlogContent .= $jobFileContent . "\n";
            }
            $execlogFullPath_org  = $execlogFullPath . ".org";
            $execlogFullPath_tmp1 = $execlogFullPath . ".tmp1";
            $execlogFullPath_tmp2 = $execlogFullPath . ".tmp2";
            $execlogFullPath_tmp3 = $execlogFullPath . ".tmp3";

            if(file_put_contents($execlogFullPath_org, $execlogContent) === false){
                $this->logger->error("Faild to write file. " . $execlogFullPath);
                return false;
            }
            // /exastro/ita-root/libs/restapiindividuallibs/ansible_driver/execute_statuscheck.phpに同等の処理あり
            if($vg_tower_driver_name == "pioneer") {
                // ユーザログ("xxx", )を改行する
                $cmd = "sed -e 's/\", \"/\",\\n\"/g' " . $execlogFullPath_org  .  " > " . $execlogFullPath_tmp1;
                exec($cmd);

                // 改行文字 \\r\\nを改行コードに置換える
                $cmd = "sed -e 's/\\\\\\\\r\\\\\\\\n/\\n/g' "  . $execlogFullPath_tmp1 . " > " . $execlogFullPath;
                exec($cmd);

                exec("/bin/rm -f " . $execlogFullPath_tmp1 );
            } else {
                // ログ(", ")  =>  (",\n")を改行する
                $cmd = "sed -e 's/\", \"/\",\\n\"/g' " . $execlogFullPath_org  .  " > " . $execlogFullPath_tmp1;
                exec($cmd);

                // ログ(=> {)  =>  (=> {\n)を改行する
                $cmd = "sed -e 's/=> {/=> {\\n/g' "    . $execlogFullPath_tmp1 .  " > " . $execlogFullPath_tmp2;
                exec($cmd);

                // ログ(, ")  =>  (,\n")を改行する
                $cmd = "sed -e 's/, \"/,\\n\"/g' "     . $execlogFullPath_tmp2 .  " > " . $execlogFullPath_tmp3;
                exec($cmd);

                // 改行文字を改行コードに置換える
                $cmd = "sed -e 's/\\\\\\\\r\\\\\\\\n/\\n/g' "  . $execlogFullPath_tmp3 .  " > " . $execlogFullPath;
                exec($cmd);

                exec("/bin/rm -f " . $execlogFullPath_tmp1 . " " . $execlogFullPath_tmp2 . " " . $execlogFullPath_tmp3);

            }
        } finally {
            // ロック解除
            sem_release($semaphore);
        }
        return true;
    }

    function errorLogOut($message) {
        if($this->exec_out_dir != "" && file_exists($this->exec_out_dir)) {
            $errorLogfile = $this->exec_out_dir . "/" . "error.log";
            $ret = file_put_contents($errorLogfile, "\n\n" . $message, FILE_APPEND | LOCK_EX);
            if($ret === false) {
                $this->logger->error("Error. Faild to write message. $message");
            }
        }
    }

    function __destruct() {
    }
}

function getSshKeyFileContent($systemId, $sshKeyFileName) {

    global $root_dir_path;

    $ssh_key_file_dir = $root_dir_path . "/uploadfiles/2100000303/CONN_SSH_KEY_FILE/";

    $content = "";

    $filePath = $ssh_key_file_dir . addPadding($systemId) . "/" . $sshKeyFileName;
    $content = file_get_contents($filePath);

    return $content;
}
