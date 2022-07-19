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
require_once($root_dir_path . '/libs/commonlibs/common_ansible_vault.php');
require_once($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php');

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
require_once($rest_api_command . "AnsibleTowerRestApiExecutionEnvironment.php");
require_once($rest_api_command . "AnsibleTowerRestApirPassThrough.php");

class ExecuteDirector {

    public  $restinfo;
    private $restApiCaller;
    private $logger;
    private $dbAccess;
    private $exec_out_dir;
    private $dataRelayStoragePath;
    private $version;
    private $MultipleLogFileJsonAry;
    private $MultipleLogMark;
    // $JobDetailAry[workflow job id][workflow job node id] = array('JobData'=>Job Detail,'stdout'=>job stdout);
    private $JobDetailAry;
    // $workflowJobNodeAry[workflow job id][workflow job node id] = workflow job node detail
    private $workflowJobNodeAry;
    // $workflowJobNodeIdAry[workflow job id][] = workflow job node id;
    private $workflowJobNodeIdAry;
    // $workflowJobAry[workflow job id] = workflow job detail
    private $workflowJobAry;
    private $jobFileList;
    private $jobLogFileList;
    private $jobOrgLogFileList;

    private $AnsibleExecMode;
    function __construct($restApiCaller, $logger, $dbAccess, $exec_out_dir, $ifInfoRow, $JobTemplatePropertyParameterAry=array(),$JobTemplatePropertyNameAry=array()) {
        $this->restApiCaller = $restApiCaller;
        $this->logger = $logger;
        $this->dbAccess = $dbAccess;
        $this->exec_out_dir = $exec_out_dir;
        $this->JobTemplatePropertyParameterAry = $JobTemplatePropertyParameterAry;
        $this->JobTemplatePropertyNameAry      = $JobTemplatePropertyNameAry;

        $this->objMTS = MessageTemplateStorageHolder::getMTS();
        $this->dataRelayStoragePath = "";
        $this->MultipleLogFileJsonAry = "";
        $this->MultipleLogMark        = "";
        $this->JobDetailAry           = array();
        $this->workflowJobNodeAry     = array();
        $this->workflowJobAry         = array();
        $this->workflowJobNodeIdAry   = array();
        $this->jobFileList = array();
        $this->jobLogFileList = array();
        $this->jobOrgLogFileList = array();
        $this->restinfo          = array();
        $this->AnsibleExecMode   = $ifInfoRow["ANSIBLE_EXEC_MODE"];
    }

    function setTowerVersion($version) {
        $this->version = $version;
    }
    function getTowerVersion() {
        return($this->version);
    }

    function build($GitObj, $exeInsRow, $ifInfoRow, &$TowerHostList) {

        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        $execution_no = $exeInsRow['EXECUTION_NO'];

        $virtualenv_name = $exeInsRow['I_VIRTUALENV_NAME'];

        // Towerのvirtualenv確認
        // 実行エンジンがTower以外はI_VIRTUALENV_NAMEは空なので、実行エンジンのチェックはしない
        $virtualenv_name_ok = false;
        if($virtualenv_name != "") {
            $response_array = AnsibleTowerRestApiConfig::get($this->restApiCaller);
            if($response_array['success'] == false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040026",array($virtualenv_name));
                $this->errorLogOut($errorMessage);
                return -1;
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
                return -1;
            }
        }
        // AACの実行環境確認
        // 実行エンジンがAAC以外はI_EXECUTION_ENVIRONMENT_NAMEは空なので、実行エンジンのチェックはしない
        $execution_environment_id = false;
        $execution_environment_name = $exeInsRow['I_EXECUTION_ENVIRONMENT_NAME'];
        if($execution_environment_name != "") {
            $response_array = AnsibleTowerRestApiExecutionEnvironment::get($this->restApiCaller);

            if($response_array['success'] == false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040038",array($execution_environment_name));
                $this->errorLogOut($errorMessage);
                return -1;
            }
            if( ! array_key_exists('responseContents',$response_array)) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040038",array($execution_environment_name));
                $this->errorLogOut($errorMessage);
                return -1;
            }

            if( ! array_key_exists('results',$response_array['responseContents'] )) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040038",array($execution_environment_name));
                $this->errorLogOut($errorMessage);
                return -1;
            }
            if( isset($response_array['responseContents']['results'] )) {
                foreach($response_array['responseContents']['results'] as $no=>$paramList) {
                    if($paramList['name'] == $execution_environment_name) {
                        $execution_environment_id = $paramList['id'];
                        break;
                    }
                }
            }

            if($execution_environment_id === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040039",array($execution_environment_name));
                $this->errorLogOut($errorMessage);
                return -1;
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
                return -1;
            }
            if(count($response_array['responseContents']) === 0
                || array_key_exists("id", $response_array['responseContents'][0]) == false) {
                $this->logger->error("No inventory id. (prepare)");
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040025",array($OrganizationName));
                $this->errorLogOut($errorMessage);
                return -1;
            }
            $OrganizationId = $response_array['responseContents'][0]['id'];
        } else {
            // 組織名未登録
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040030");
            $this->errorLogOut($errorMessage);
            return -1;
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

        // 複数の認証情報によりログが分割されるか確認
        if(count($inventoryForEachCredentials) != 1) {
            $this->settMultipleLogMark($execution_no, $ifInfoRow['ANSIBLE_STORAGE_PATH_LNX']);
        }

        // AnsibleTowerHost情報取得
        $this->dataRelayStoragePath = $ifInfoRow['ANSIBLE_STORAGE_PATH_LNX'];
        $TowerHostList = array();
        $ret = $this->getTowerHostInfo($execution_no,$ifInfoRow['ANSTWR_HOST_ID'],$ifInfoRow['ANSIBLE_STORAGE_PATH_LNX'],$TowerHostList);
        if($ret == false) {
            // AnsibleTowerホスト一覧の取得に失敗しました。
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040033");
            $this->errorLogOut($errorMessage);
            return -1;
        }
        if(count($TowerHostList) == 0) {
            // AnsibleTowerホスト一覧に有効なホスト情報が登録されていません。
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040034");
            $this->errorLogOut($errorMessage);
            return -1;
        }

        // Gitリポジトリに展開する資材を作業ディレクトリに作成
        $tmp_path_ary = getInputDataTempDir($execution_no, $vg_tower_driver_name);
        $ret = $this->createMaterialsTransferTempDir($execution_no, $ifInfoRow, $TowerHostList, $tmp_path_ary["DIR_NAME"]);
        if($ret == false) {
            return -1;
        }

        // 実行エンジンを判定　AACの場合にAnsible Automation Controllerと連携するGitリポジトリを作成
        if($this->AnsibleExecMode == DF_EXEC_MODE_AAC) {

            $srcFiles = $tmp_path_ary["DIR_NAME"] . "/*";
            $ret = $this->createGitRepo($GitObj, $srcFiles);
            if($ret == false) {

                return -1;
            }
        }

        $tmp_path_ary = getInputDataTempDir($execution_no, $vg_tower_driver_name);
        $ret = $this->MaterialsTransferToTower($execution_no, $ifInfoRow, $TowerHostList, $tmp_path_ary['DIR_NAME']);
        if($ret == false) {
            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040036");
            $this->errorLogOut($errorMessage);
            return -1;
        }

        // project生成
        // Git連携用 認証情報生成
        if($this->AnsibleExecMode == DF_EXEC_MODE_AAC) {
            // Git連携用 認証情報生成
            $credential['username']       = $ifInfoRow["ANS_GIT_USER"];
            $credential['ssh_key_data']   = getGitSshKeyFileContent($ifInfoRow["ANSIBLE_IF_INFO_ID"], $ifInfoRow["ANS_GIT_SSH_KEY_FILE"]);
            $credential['ssh_key_unlock'] = ky_decrypt($ifInfoRow["ANS_GIT_SSH_KEY_FILE_PASSPHRASE"]);

            $git_credentialId = $this->createGitCredential($execution_no, $credential, $OrganizationId);
            if($git_credentialId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040040");
                $this->errorLogOut($errorMessage);
                return -1;
            }

            // project生成  scmタイプ:git
            $addParam               = array();
            $addParam["scm_type"]   = AnsibleTowerRestApiProjects::SCMTYPE_GIT;
            $addParam["scm_url"]    = $GitObj->GetiRepoUrl();
            $addParam["credential"] = $git_credentialId; 
            $response_array = $this->createProject($execution_no,$OrganizationId,$virtualenv_name,$addParam);
            if($response_array == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040003");
                $this->errorLogOut($errorMessage);
                return -1;
            }
            $projectId = $response_array['responseContents']['id'];
            // project_updatesオブジェクトは明示的に削除する必要なし
            $ret = $this->createProjectStatusCheck($response_array);
            if($ret !== true) {
                // エラーログはcreateProjectStatusCheckで出力
                return -1;
            }
        } else {
            // project生成  scmタイプ:手動
            $addParam               = array();
            $addParam["scm_type"]   = "";
            $projectId = $this->createProject($execution_no,$OrganizationId,$virtualenv_name,$addParam);
            if($projectId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040003");
                $this->errorLogOut($errorMessage);
                return -1;
            }
            $projectId = $projectId['responseContents']['id'];
        }

        // ansible vault認証情報生成
        $vault_credentialId = -1;
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
            $jobTemplateId = $this->createEachJobTemplate($execution_no, $loopCount, $projectId, $credentialId, $vault_credentialId, $inventoryId, $exeInsRow['RUN_MODE'],$execution_environment_id);
            if($jobTemplateId == -1) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040006");
                $this->errorLogOut($errorMessage);
                return -1;
            }

            ///////////////////////////////////////////////////////////////////////
            // JobTemplateにcredentialIdを紐づけ(Ansible Tower3.6～)
            ///////////////////////////////////////////////////////////////////////
            //---- Ansible Tower Version Check (Not Ver3.5)
            if($this->getTowerVersion() != TOWER_VER35) {

                $response_array = AnsibleTowerRestApiJobTemplates::postCredentialsAdd($this->restApiCaller,$jobTemplateId, $credentialId);
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040032");
                    $this->errorLogOut($errorMessage);
                    return -1;
                }
                $response_array = AnsibleTowerRestApiJobTemplates::postCredentialsAdd($this->restApiCaller,$jobTemplateId, $vault_credentialId);
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040032");
                    $this->errorLogOut($errorMessage);
                    return -1;
                }
            }
            //Ansible Tower Version Check (Not Ver3.5) ----

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

    function transfer($execution_no, $TowerHostList) {
        $this->logger->trace(__METHOD__);

        global $vg_tower_driver_name;
        $allResult = true;

        $ret = $this->ResultFileTransfer($execution_no, $TowerHostList);
        if($ret == false) {
            $allResult = false;
        }
        return $allResult;

    }
    function delete($GitObj, $execution_no, $TowerHostList) {

        $this->logger->trace(__METHOD__);

        global $vg_tower_driver_name;
        $allResult = true;

        // Ansible Automation Controller側の/var/lib/exastro配下の該当ディレクトリ削除
        $ret = $this->MaterialsDelete("ExastroPath",$execution_no, $TowerHostList);
        if($ret == false) {
            $allResult = false;
        }

        // Gitリポジトリに展開する資材を作業ディレクトリを削除
        $ret = $this->deleteMaterialsTransferTempDir($execution_no);

        // Ansible Automation Controllerと連携するGitリポジトリを削除
        if($this->AnsibleExecMode == DF_EXEC_MODE_AAC) {
            $ret = $GitObj->GitRepoDirDelete();
        }

        // 実行エンジンを判定　Towerの場合に/var/lib/awx/projects配下の該当ディレクトリ削除
        if($this->AnsibleExecMode == DF_EXEC_MODE_TOWER) {
            $ret = $this->MaterialsDelete("TowerPath",$execution_no, $TowerHostList);
            if($ret == false) {
                $allResult = false;
            }
        }

        // ジョブテンプレート名でジョブテンプレートを抽出
        // 抽出したジョブテンプレートIDでジョブテンプレートの情報取得
        // ジョブテンプレートに紐づいているジョブを削除
        // /api/v2/job_templates/?name__startswith=ita_(driver_name)_executions_jobtpl_(execution_no)
        // /api/v2/jobs/?job_template=(job template id)
        // /api/v2/jobs/(job id)/
        $ret = $this->cleanUpJob($execution_no);
        if($ret == false) {
            $allResult = false;
        }

        // ジョブテンプレート名に紐づくジョブテンプレートを抽出
        // 抽出したジョブテンプレートのIDでジョブテンプレートを削除
        // /api/v2/job_templates/?name__startswith=ita_(driver name)_executions_jobtpl_(execution_no)
        // /api/v2/job_templates/(job template id)/
        $ret = $this->cleanUpJobTemplate($execution_no);
        if($ret == false) {
            $allResult = false;
        }

        // ワークフロージョブテンプレート名でワークフロージョブテンプレートを抽出
        // ワークフロージョブテンプレートIDに紐づくワークフロージョブノードを抽出
        // 抽出されたワークフロージョブノードを削除
        // /api/v2/workflow_job_templates/?name=ita_legacy_executions_workflowtpl_0000010442
        // /api/v2/workflow_job_template_nodes/?workflow_job_template=2689
        // /api/v2/workflow_job_template_nodes/828/
        // ワークフロージョブテンプレート名でワークフロージョブテンプレートを抽出
        // 抽出されたワークフロージョブテンプレートを削除
        // /api/v2/workflow_job_templates/?name=ita_legacy_executions_workflowtpl_0000010442
        // /api/v2/workflow_job_templates/2293/
        $ret = $this->cleanUpWorkflowJobTemplate($execution_no);
        if($ret == false) {
            $allResult = false;
        }

        // ワークフローとテンプレート名(Job slice)ワークフローを抽出
        // 抽出したワークフローを削除
        // ワークフローノードは削除不要
        // /api/v2/workflow_jobs/?name__startswith=ita_(driver_name)_executions&name__contains=(execution_no)
        // /api/v2/workflow_jobs/(work flow id)/

        $ret = $this->cleanUpWorkflowJobs($execution_no);
        if($ret == false) {
            $allResult = false;
        }

        // Git連携用 認証情報を抽出
        // 抽出したワークフローを削除
        if($this->AnsibleExecMode == DF_EXEC_MODE_AAC) {
            $ret = $this->cleanUpGitCredential($execution_no);
            if($ret == false) {
                $allResult = false;
            }
        }

        //
        // /api/v2/projects/?name=ita_legacy_executions_project_(execution_no)
        // /api/v2/projects/2666/
        $ret = $this->cleanUpProject($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        // /api/v2/credentials/?name__startswith=ita_legacy_executions_vault_credential_0000010436
        // /api/v2/credentials/1612/
        $ret = $this->cleanUpCredential($execution_no);
        if($ret == false) {
            $allResult = false;
        }
        // /api/v2/credentials/?name__startswith=ita_legacy_executions_vault_credential_0000010436
        // /api/v2/credentials/1612/
        $ret = $this->cleanUpVaultCredential($execution_no);
        if($ret == false) {
            $allResult = false;
        }

        // /api/v2/inventories/?name__startswith=ita_legacy_executions_inventory_0000010436
        // /api/v2/inventories/1030/hosts/
        // /api/v2/hosts/1622/
        // /api/v2/inventories/?name__startswith=ita_legacy_executions_inventory_0000010436
        // /api/v2/inventories/1030/
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

    private function createMaterialsTransferTempDir($execution_no, $ifInfoRow, $TowerHostList, $tmp_path) {

        $this->logger->trace(__METHOD__);

        global $root_dir_path;
        global $vg_tower_driver_name;

        $result_code = true;

        ///////////////////////////////////////////////////////////
        // 一時ディレクトリを削除
        // src path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ
        ///////////////////////////////////////////////////////////
        $src_path = $tmp_path;
        if(file_exists($src_path)) {
            $cmd = sprintf("/bin/rm -rf %s 2>&1",
                            $src_path);
            exec($cmd,$arry_out,$return_var);
            if($return_var !== 0) {
                $log         = implode("\n",$arry_out);
                $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
                $this->errorLogOut($log);
                $this->logger->error($log);
    
                $result_code = false;
                return $result_code;
            }
        }
        ///////////////////////////////////////////////////////////
        // in配下を一時ディレクトリにコピー
        // src  path: ansible data_relay_storage path(ita)/legacy/ns/0000000007/in
        // dest path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ
        ///////////////////////////////////////////////////////////
        $src_path  = $this->getMaterialsTransferSourcePath($ifInfoRow['ANSIBLE_STORAGE_PATH_LNX'],$execution_no);
        $dest_path = $tmp_path;
        $cmd = sprintf("/bin/cp -rfp %s %s 2>&1",
                       $src_path,
                       $dest_path);
        exec($cmd,$arry_out,$return_var);
        if($return_var !== 0) {
            $log         = implode("\n",$arry_out);
            $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            $result_code = false;
            return $result_code;
        }

        global $vg_TowerProjectsScpPathArray;
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        // out配下を一時ディレクトリ配下にコピー
        // src  path: ansible data_relay_storage path(ita)/legacy/ns/0000000007/out
        // dest path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ/__ita_out_dir__
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        $src_path  = $vg_TowerProjectsScpPathArray[DF_SCP_OUT_ITA_PATH];
        $dest_path = $vg_TowerProjectsScpPathArray[DF_GITREPO_OUT_PATH];
        $cmd = sprintf("/bin/cp -rfp %s %s 2>&1",
                       $src_path,
                       $dest_path);
        exec($cmd,$arry_out,$return_var);
        if($return_var !== 0) {
            $log         = implode("\n",$arry_out);
            $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            $result_code = false;
            return $result_code;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        // tmp配下を一時ディレクトリ配下にコピー
        // src  path: ansible data_relay_storage path(ita)/xx mode name xx/xx mode id xx/0000050044/tmp
        // dest path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ/__ita_tmp_dir__
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        $src_path  = $vg_TowerProjectsScpPathArray[DF_SCP_TMP_ITA_PATH];
        $dest_path = $vg_TowerProjectsScpPathArray[DF_GITREPO_TMP_PATH];
        $cmd = sprintf("/bin/cp -rfp %s %s 2>&1",
                       $src_path,
                       $dest_path);
        exec($cmd,$arry_out,$return_var);
        if($return_var !== 0) {
            $log         = implode("\n",$arry_out);
            $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            $result_code = false;
            return $result_code;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        // symphony/インスタンス番号配下をx一時ディレクトリ配下にコピー
        // src  path:  symphony data_relay_storage path(ita)/symphonyインスタンス番号
        // dest path:  ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ/__ita_tmp_dir__/__ita_symphony_dir__/symphonyインスタンス番号
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        if( array_key_exists(DF_SCP_SYMPHONY_ITA_PATH,$vg_TowerProjectsScpPathArray) ) {
            $src_path  = $vg_TowerProjectsScpPathArray[DF_SCP_SYMPHONY_ITA_PATH];
            $dest_path = dirname($vg_TowerProjectsScpPathArray[DF_GITREPO_SYMPHONY_PATH]);
            $cmd = sprintf("/bin/cp -rfp %s %s 2>&1",
                            $src_path,
                            $dest_path);
            exec($cmd,$arry_out,$return_var);
            if($return_var !== 0) {
                $log         = implode("\n",$arry_out);
                $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
                $this->errorLogOut($log);
                $this->logger->error($log);
        
                $result_code = false;
                return $result_code;
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        // conductor/インスタンス番号配下を一時ディレクトリ配下にコピー
        // src  path:  conductor ata_relay_storage path(ita)/conductorインスタンス番号
        // dest path:  ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ/__ita_tmp_dir__/__ita_conductor_dir__/conductorインスタンス番号
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        if( array_key_exists(DF_SCP_CONDUCTOR_ITA_PATH,$vg_TowerProjectsScpPathArray) ) {
            $src_path  = $vg_TowerProjectsScpPathArray[DF_SCP_CONDUCTOR_ITA_PATH];
            $dest_path = dirname($vg_TowerProjectsScpPathArray[DF_GITREPO_CONDUCTOR_PATH]);
            $cmd = sprintf("/bin/cp -rfp %s %s 2>&1",
                            $src_path,
                            $dest_path);
            exec($cmd,$arry_out,$return_var);
            if($return_var !== 0) {
                $log         = implode("\n",$arry_out);
                $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
                $this->errorLogOut($log);
                $this->logger->error($log);
        
                $result_code = false;
                return $result_code;
            }
        }

        return $result_code;
    } 

    private function MaterialsTransferToTower($execution_no, $ifInfoRow, $TowerHostList, $srcBasePath) {

        $this->logger->trace(__METHOD__);

        global $root_dir_path;

        $tmp_TowerInfo_File = '/tmp/.ky_ansible_materials_transfer_TowerInfo_' . getmypid() . ".log";
        @unlink($tmp_TowerInfo_File);

        $tmp_log_file = '/tmp/.ky_ansible_materials_transfer_logfile_' . getmypid() . ".log";
        @unlink($tmp_log_file);

        $result_code = true;
        foreach($TowerHostList as $credential) {

            ///////////////////////////////////////////////////////////
            // exastro用Towerプロジェクトディレクトリ配下(/var/lib/exastro)に資材コピー
            // src  path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ
            // dest path: /var/lib/exastro/ita_legacy_executions_0000000001
            ///////////////////////////////////////////////////////////
            $src_path  = $srcBasePath;
            $dest_path = $this->getMaterialsTransferDestinationPath("ExastroPath",$execution_no);
            $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                             $credential['host_name'],
                             $credential['auth_type'],
                             $credential['username'],
                             $credential['password'],
                             $credential['ssh_key_file'],
                             $src_path,
                             $dest_path,
                             $credential['ssh_key_file_pass'],
                             $root_dir_path,
                             "ITA");
       
            if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                $this->errorLogOut($errorMessage);
                $this->logger->error($errorMessage);
                $result_code = false;
                return $result_code;
            } else {
                $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                               $root_dir_path,
                               "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                               $tmp_TowerInfo_File,
                               $tmp_log_file);

                exec($cmd,$arry_out,$return_var);
                if($return_var !== 0) {
                    $log = file_get_contents($tmp_log_file);
                    $this->errorLogOut($log);
                    $this->logger->error($log);
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040035",array($credential['host_name']));
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
    
                    $result_code = false;
                    return $result_code;
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);

            global $vg_TowerProjectsScpPathArray;
            if($credential['node_type'] == DF_CONTROL_NODE) {
                // 実行エンジンを判定　Towerの場合にTowerプロジェクトディレクトリ(/var/lib/awx/projects)に資材展開
                if($this->AnsibleExecMode == DF_EXEC_MODE_TOWER) {
                    ///////////////////////////////////////////////////////////
                    // 制御ノードの場合にTowerプロジェクトディレクトリ配下(/var/lib/awx/projects)に資材コピー
                    // src  path: ~/ita-root/temp/ansible_driver_temp/Movement毎のディレクトリ
                    // dest path: Towerプロジェクトディレクトリ(/var/lib/awx/projects)
                    ///////////////////////////////////////////////////////////
                    $src_path  = $this->getMaterialsTransferDestinationPath("ExastroPath",$execution_no);
                    $dest_path = $this->getMaterialsTransferDestinationPath("TowerPath",  $execution_no);
                    $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                                     $credential['host_name'],
                                     $credential['auth_type'],
                                     $credential['username'],
                                     $credential['password'],
                                     $credential['ssh_key_file'],
                                     $src_path,
                                     $dest_path,
                                     $credential['ssh_key_file_pass'],
                                     $root_dir_path);
       
                    if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                        $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                        $this->errorLogOut($errorMessage);
                        $this->logger->error($errorMessage);
                        $result_code = false;
                        return $result_code;
                    } else {
                        $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                                       $root_dir_path,
                                       "backyards/ansible_driver/ky_ansible_materials_remotecopy.sh",
                                       $tmp_TowerInfo_File,
                                       $tmp_log_file);
    
                        exec($cmd,$arry_out,$return_var);
                        if($return_var !== 0) {
                            $log = file_get_contents($tmp_log_file);
                            $this->errorLogOut($log);
                            $this->logger->error($log);
                            $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040035",array($credential['host_name']));
                            $this->errorLogOut($errorMessage);
                            $this->logger->error($errorMessage);
            
                            $result_code = false;
                            return $result_code;
                        }
                    }
                }

                @unlink($tmp_log_file);
                @unlink($tmp_TowerInfo_File);
            }
        }
        return $result_code;
    } 

    private function MaterialsDelete($PathId,$execution_no, $TowerHostList) {

        $this->logger->trace(__METHOD__);

        global $root_dir_path;

        $dest_path = $this->getMaterialsTransferDestinationPath($PathId,$execution_no);

        $tmp_log_file = '/tmp/.ky_ansible_materials_delete_logfile_' . getmypid() . ".log";

        $result_code = true;
        foreach($TowerHostList as $credential) {
       
            // 実行ノードの場合、Towerプロジェクトディレクトリ配下(/var/lib/awx/projects)は無いので削除対象外
            if(($credential['node_type'] != DF_CONTROL_NODE) && ($PathId =="TowerPath")) {
                continue;
            }
            $tmp_TowerInfo_File = '/tmp/.ky_ansible_materials_delete_TowerInfo_' . getmypid() . ".log";
            @unlink($tmp_TowerInfo_File);

            $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                             $credential['host_name'],
                             $credential['auth_type'],
                             $credential['username'],
                             $credential['password'],
                             $credential['ssh_key_file'],
                             $dest_path,
                             $credential['ssh_key_file_pass'],
                             $root_dir_path);
       
            if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                $this->errorLogOut($errorMessage);
                $this->logger->error($errorMessage);
                $result_code = false;
            } else {
                $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                               $root_dir_path,
                               "backyards/ansible_driver/ky_ansible_materials_delete.sh",
                               $tmp_TowerInfo_File,
                               $tmp_log_file);

                exec($cmd,$arry_out,$return_var);
                if($return_var !== 0) {
                    $log = file_get_contents($tmp_log_file);
                    $this->logger->error($log);
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6040037",array($credential['host_name'],$dest_path));
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);

                    $result_code = false;
                }
                @unlink($tmp_log_file);
                @unlink($tmp_TowerInfo_File);
            }

        }
        return $result_code;
    }

    private function ResultFileTransfer($execution_no, $TowerHostList) {

        $this->logger->trace(__METHOD__);

        global $root_dir_path;

        $tmp_TowerInfo_File = '/tmp/.ky_ansible_resultfile_transfer_TowerInfo_' . getmypid() . ".log";
        @unlink($tmp_TowerInfo_File);

        $tmp_log_file = '/tmp/.ky_ansible_resultfile_transfer_delete_logfile_' . getmypid() . ".log";
        @unlink($tmp_log_file);

        $result_code = true;
        foreach($TowerHostList as $credential) {
            global $vg_TowerProjectsScpPathArray;
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ITA作業ディレクトリ配下のsymphonyディレクトリをITAに転送
            // src  path:  /var/lib/exastro/ita_legacy_executions_0000050063/__ita_tmp_dir__/__ita_symphony_dir__/symphonyインスタンス番号
            // dest path:  symphony data_relay_storage path(ita)/symphonyインスタンス番号
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            if( array_key_exists(DF_SCP_SYMPHONY_ITA_PATH,$vg_TowerProjectsScpPathArray) ) {
                $src_path   = $vg_TowerProjectsScpPathArray[DF_SCP_SYMPHONY_TOWER_PATH];
                $dest_path  = dirname($vg_TowerProjectsScpPathArray[DF_SCP_SYMPHONY_ITA_PATH]);
                $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                                 $credential['host_name'],
                                 $credential['auth_type'],
                                 $credential['username'],
                                 $credential['password'],
                                 $credential['ssh_key_file'],
                                 $src_path,
                                 $dest_path,
                                 $credential['ssh_key_file_pass'],
                                 $root_dir_path,
                                 "TOWER");
       
                if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
                    $result_code = false;
                    return $result_code;
                } else {
                    $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                                   $root_dir_path,
                                   "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                                   $tmp_TowerInfo_File,
                                   $tmp_log_file);

                    exec($cmd,$arry_out,$return_var);
                    if($return_var !== 0) {
                        $log = file_get_contents($tmp_log_file);
                        $this->errorLogOut($log);
                        $this->logger->error($log);
                        $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-50082",array($credential['host_name']));
                        $this->errorLogOut($errorMessage);
                        $this->logger->error($errorMessage);
        
                        $result_code = false;
                        return $result_code;
                    }
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);

            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ITA作業ディレクトリ配下のconductorディレクトリをITAに転送
            // src  path:  /var/lib/exastro/ita_legacy_executions_0000050063/__ita_tmp_dir__/__ita_conductor_dir__/conductorインスタンス番号
            // dest path:  conductor data_relay_storage path(ita)/conductorインスタンス番号
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            if( array_key_exists(DF_SCP_CONDUCTOR_ITA_PATH,$vg_TowerProjectsScpPathArray) ) {
                $src_path   = $vg_TowerProjectsScpPathArray[DF_SCP_CONDUCTOR_TOWER_PATH];
                $dest_path  = dirname($vg_TowerProjectsScpPathArray[DF_SCP_CONDUCTOR_ITA_PATH]);
                $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                                 $credential['host_name'],
                                 $credential['auth_type'],
                                 $credential['username'],
                                 $credential['password'],
                                 $credential['ssh_key_file'],
                                 $src_path,
                                 $dest_path,
                                 $credential['ssh_key_file_pass'],
                                 $root_dir_path,
                                 "TOWER");
       
                if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
                    $result_code = false;
                    return $result_code;
                } else {
                    $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                                   $root_dir_path,
                                   "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                                   $tmp_TowerInfo_File,
                                   $tmp_log_file);

                    exec($cmd,$arry_out,$return_var);
                    if($return_var !== 0) {
                        $log = file_get_contents($tmp_log_file);
                        $this->errorLogOut($log);
                        $this->logger->error($log);
                        $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-50082",array($credential['host_name']));
                        $this->errorLogOut($errorMessage);
                        $this->logger->error($errorMessage);
        
                        $result_code = false;
                        return $result_code;
                    }
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);

            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ITA作業ディレクトリ配下のoutディレクトリ(__ita_out_dir__)をITAに転送
            // src   path: /var/lib/exastro/ita_xxmode namexx_executions_作業番号/__ita_out_dir__/*
            // dest  path: ansible data_relay_storage path(ita)/xx mode name xx/xx mode id xx/0000050044/out
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $src_path   = $vg_TowerProjectsScpPathArray[DF_SCP_OUT_TOWER_PATH] . "/*";
            $dest_path  = $vg_TowerProjectsScpPathArray[DF_SCP_OUT_ITA_PATH];
            $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                             $credential['host_name'],
                             $credential['auth_type'],
                             $credential['username'],
                             $credential['password'],
                             $credential['ssh_key_file'],
                             $src_path,
                             $dest_path,
                             $credential['ssh_key_file_pass'],
                             $root_dir_path,
                             "TOWER");
       
            if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                $this->errorLogOut($errorMessage);
                $this->logger->error($errorMessage);
                $result_code = false;
                return $result_code;
            } else {
                $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                               $root_dir_path,
                               "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                               $tmp_TowerInfo_File,
                               $tmp_log_file);

                exec($cmd,$arry_out,$return_var);
                if($return_var !== 0) {
                    $log = file_get_contents($tmp_log_file);
                    $this->errorLogOut($log);
                    $this->logger->error($log);
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-50082",array($credential['host_name']));
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
    
                    $result_code = false;
                    return $result_code;
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);

            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ITA作業ディレクトリ配下の_parameters配下をITAに転送
            // src   path: /var/lib/exastro/ita_xxmode namexx_executions_作業番号/_parameters
            // dest  path: ansible data_relay_storage path(ita)/xx mode name xx/xx mode id xx/0000050044/in/_parameters
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $src_path   = $vg_TowerProjectsScpPathArray[DF_SCP_IN_PARAMATERS_TOWER_PATH];
            $dest_path  = dirname($vg_TowerProjectsScpPathArray[DF_SCP_IN_PARAMATERS_ITA_PATH]);
            $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                             $credential['host_name'],
                             $credential['auth_type'],
                             $credential['username'],
                             $credential['password'],
                             $credential['ssh_key_file'],
                             $src_path,
                             $dest_path,
                             $credential['ssh_key_file_pass'],
                             $root_dir_path,
                             "TOWER");
       
            if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                $this->errorLogOut($errorMessage);
                $this->logger->error($errorMessage);
                $result_code = false;
                return $result_code;
            } else {
                $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                               $root_dir_path,
                               "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                               $tmp_TowerInfo_File,
                               $tmp_log_file);

                exec($cmd,$arry_out,$return_var);
                if($return_var !== 0) {
                    $log = file_get_contents($tmp_log_file);
                    $this->errorLogOut($log);
                    $this->logger->error($log);
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-50082",array($credential['host_name']));
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
    
                    $result_code = false;
                    return $result_code;
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);

            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Towerプロジェクトディレクトリ配下の_parameters_file配下をITAに転送
            // src   path: /var/lib/awx/projects/ita_xxmode namexx_executions_作業番号/_parameters_file
            // dest  path: ansible data_relay_storage path(ita)/xx mode name xx/xx mode id xx/0000050044/in/_parameters_file
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $src_path   = $vg_TowerProjectsScpPathArray[DF_SCP_IN_PARAMATERS_FILE_TOWER_PATH];
            $dest_path  = dirname($vg_TowerProjectsScpPathArray[DF_SCP_IN_PARAMATERS_FILE_ITA_PATH]);
            $info = sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\n",
                             $credential['host_name'],
                             $credential['auth_type'],
                             $credential['username'],
                             $credential['password'],
                             $credential['ssh_key_file'],
                             $src_path,
                             $dest_path,
                             $credential['ssh_key_file_pass'],
                             $root_dir_path,
                             "TOWER");
       
            if(file_put_contents($tmp_TowerInfo_File, $info) === false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000018");
                $this->errorLogOut($errorMessage);
                $this->logger->error($errorMessage);
                $result_code = false;
                return $result_code;
            } else {
                $cmd = sprintf("sh %s/%s %s > %s 2>&1",
                               $root_dir_path,
                               "backyards/ansible_driver/ky_ansible_materials_transfer.sh",
                               $tmp_TowerInfo_File,
                               $tmp_log_file);

                exec($cmd,$arry_out,$return_var);
                if($return_var !== 0) {
                    $log = file_get_contents($tmp_log_file);
                    $this->errorLogOut($log);
                    $this->logger->error($log);
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-50082",array($credential['host_name']));
                    $this->errorLogOut($errorMessage);
                    $this->logger->error($errorMessage);
    
                    $result_code = false;
                    return $result_code;
                }
            }

            @unlink($tmp_log_file);
            @unlink($tmp_TowerInfo_File);
        }
        return $result_code;
    }

    private function getTowerHostInfo($execution_no,$anstwr_host_id,$dataRelayStoragePath,&$TowerHostList) {


        global $vg_tower_driver_type;
        global $vg_tower_driver_id;
        global $root_dir_temp;
        global $g;

        $this->logger->trace(__METHOD__);

        $TowerHostList = array();

        $condition = array(
            "DISUSE_FLAG" => '0',
        );

        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        // 共通モジュールをロード
        require_once ($root_dir_path . "/libs/commonlibs/common_required_check.php");

        $rows = $this->dbAccess->selectRowsUseBind('B_ANS_TWR_HOST', false, $condition);

        $chkobj = new AuthTypeParameterRequiredCheck();

        foreach($rows as $row) {
            if(strlen($row['ANSTWR_ISOLATED_TYPE']) != 0) {
                $node_type = DF_EXECUTE_NODE;
            } else {
                $node_type = DF_CONTROL_NODE;
            }

            // 認証方式に応じた必須項目の設定確認
            $errMsgParameterAry = array();
            $errMsgParameterAry = array($row['ANSTWR_HOSTNAME']);
            $strError = $chkobj->TowerHostListAuthTypeRequiredParameterCheck($chkobj->chkType_WorkflowExec_TowerHostList,
                                                                             $this->objMTS,
                                                                             $errMsgParameterAry,
                                                                             $row['ANSTWR_LOGIN_AUTH_TYPE'],
                                                                             $row['ANSTWR_LOGIN_PASSWORD'],
                                                                             $row['ANSTWR_LOGIN_SSH_KEY_FILE'],
                                                                             $row['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE']);
            if($strError !== true) {
                $this->errorLogOut($strError);
                return false;
            }

            $username      = $row['ANSTWR_LOGIN_USER'];
            $password      = "undefine";
            switch($row['ANSTWR_LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_PW:          // パスワード認証
                if(strlen(trim($row['ANSTWR_LOGIN_PASSWORD'])) != 0) {
                    $password          = ky_decrypt($row['ANSTWR_LOGIN_PASSWORD']);
                    if(strlen(trim($password)) == 0) {
                        $password      = "undefine";;
                    }
                }
                break;
            }

            $sshKeyFile    = "undefine";
            switch($row['ANSTWR_LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_KEY:         // 鍵認証(パスフレーズなし)
            case DF_LOGIN_AUTH_TYPE_KEY_PP_USE:  // 認証方式:鍵認証(パスフレーズあり)
                $sshKeyFile    = $row['ANSTWR_LOGIN_SSH_KEY_FILE'];
                if(strlen(trim($sshKeyFile)) == 0) {
                    $sshKeyFile    = "undefine";
                } else {
                    $src_file   = getAnsibleTowerSshKeyFileContent($row['ANSTWR_HOST_ID'],$row['ANSTWR_LOGIN_SSH_KEY_FILE']);
                    $sshKeyFile = sprintf("%s/%s/%s/%s/in/ssh_key_files/AnsibleTower_%s_%s",
                                          $dataRelayStoragePath,
                                          $vg_tower_driver_type,
                                          $vg_tower_driver_id,
                                          addPadding($execution_no),
                                          addPadding($row['ANSTWR_HOST_ID']),
                                          $row['ANSTWR_LOGIN_SSH_KEY_FILE']);
                    if( copy($src_file,$sshKeyFile) === false ){
                        $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000106",array(basename($src_file)));
                        $this->errorLogOut($errorMessage);
                        return false;
                    }


                    // ky_encryptで中身がスクランブルされているので復元する
                    $ret = ky_file_decrypt($sshKeyFile,$sshKeyFile);
                    if($ret === false) {
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000117",array());
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }

                    if( !chmod( $sshKeyFile, 0600 ) ){
                        $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                        $this->errorLogOut($errorMessage);
                        return false;
                    }
                }
                break;
            }

            $sshKeyFilePass = "undefine";
            switch($row['ANSTWR_LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_KEY_PP_USE:  // 鍵認証(パスフレーズあり)
                $sshKeyFilePass  = ky_decrypt($row['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE']);
                if(strlen(trim($sshKeyFilePass)) == 0) {
                    $sshKeyFilePass = "undefine";
                }
                break;
            }

            switch($row['ANSTWR_LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_KEY:         // 鍵認証(パスフレーズなし)
            case DF_LOGIN_AUTH_TYPE_KEY_PP_USE:  // 鍵認証(パスフレーズあり)
                $auth_type   = "key";
                break;
            case DF_LOGIN_AUTH_TYPE_KEY_EXCH:    // 鍵認証(鍵交換済み)
                $auth_type   = "none";
                break;
            case DF_LOGIN_AUTH_TYPE_PW:          // パスワード認証
                $auth_type   = "pass";
                break;
            }

            $credential = array(
                "id"               => $row['ANSTWR_HOST_ID'],
                "host_name"        => $row['ANSTWR_HOSTNAME'],
                "auth_type"        => $auth_type,
                "username"         => $username,
                "password"         => $password,
                "ssh_key_file"     => $sshKeyFile,
                "ssh_key_file_pass"=> $sshKeyFilePass,
                "node_type"        => $node_type
            );

            $TowerHostList[] = $credential;
        }
        return true;
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

            $username        = $hostInfo['LOGIN_USER'];

            $password        = "";
            switch($hostInfo['LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_PW:          // パスワード認証
            case DF_LOGIN_AUTH_TYPE_PW_WINRM:    // 認証方式:パスワード認証(winrm)
                $password    = $hostInfo['LOGIN_PW'];
                break;
            }

            $sshPrivateKey = "";
            switch($hostInfo['LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_KEY:         // 鍵認証(パスフレーズなし)
            case DF_LOGIN_AUTH_TYPE_KEY_PP_USE:  // 認証方式:鍵認証(パスフレーズあり)
                if(!empty($hostInfo['CONN_SSH_KEY_FILE'])) {
                    $sshPrivateKey = getSshKeyFileContent($hostInfo['SYSTEM_ID'], $hostInfo['CONN_SSH_KEY_FILE']);
                    // ky_encrptのスクランブルを復号
                    $sshPrivateKey = ky_decrypt($sshPrivateKey);
                }
                break;
            }

            $sshPrivateKeyPass = "";
            switch($hostInfo['LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_KEY_PP_USE:  // 認証方式:鍵認証(パスフレーズあり)
                if(!empty($hostInfo['SSH_KEY_FILE_PASSPHRASE'])) {
                    $sshPrivateKeyPass = $hostInfo['SSH_KEY_FILE_PASSPHRASE'];
                    // ky_encrptのスクランブルを復号
                    $sshPrivateKeyPass = ky_decrypt($sshPrivateKeyPass);
                }
                break;
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
            $credential_type_id = $hostInfo['CREDENTIAL_TYPE_ID'];

            // 配列のキーに使いたいだけ
            $key = sprintf("username_%s_password_%s_sshPrivateKey_%s_sshPrivateKeyPass_%s_instanceGroupId_%s_credential_type_id_%s",$username,$password,$sshPrivateKey,$sshPrivateKeyPass,$instanceGroupId,$credential_type_id);
            $credential = array(
                "username"        => $username,
                "password"        => $password,
                "ssh_private_key" => $sshPrivateKey,
                "ssh_private_key_pass" => $sshPrivateKeyPass,
                "credential_type_id"   => $credential_type_id
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
            $hostData['winrm'] = 0;
            switch($hostInfo['LOGIN_AUTH_TYPE']) {
            case DF_LOGIN_AUTH_TYPE_PW_WINRM:    // 認証方式:パスワード認証(winrm)
                $hostData['winrm'] = 1;
                if(empty($hostInfo['WINRM_PORT'])) {
                    $hostInfo['WINRM_PORT'] = LC_WINRM_PORT;
                }
                $hostData['winrmPort'] = $hostInfo['WINRM_PORT'];

                // username/password delete
                if(strlen($hostInfo['WINRM_SSL_CA_FILE']) != 0) {
                    $filePath = "winrm_ca_files/" . addPadding($hostInfo['SYSTEM_ID']) . "-" . $hostInfo['WINRM_SSL_CA_FILE'];
                    $hostData['ansible_winrm_ca_trust_path'] = $filePath;
                }
                break;
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


    private function createProject($execution_no,$OrganizationId,$virtualenv_name,$addParam) {
        $this->logger->trace(__METHOD__);

        $param = $addParam;

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

        return $response_array;
    }


    private function createGitCredential($execution_no, $credential, $OrganizationId) {

        $this->logger->trace(__METHOD__);

        $param = array();
        $param['organization'] = $OrganizationId;

        $param['execution_no'] = $execution_no;

        if(!empty($credential['username'])) {
            $param['username']       = $credential['username'];
        }

        if(!empty($credential['ssh_key_data'])) {
            $param['ssh_key_data']   = $credential['ssh_key_data'];
        }

        if(!empty($credential['ssh_key_unlock'])) {
            $param['ssh_key_unlock'] = $credential['ssh_key_unlock'];
        }

        $this->logger->trace(var_export($param, true));

        $response_array = AnsibleTowerRestApiCredentials::git_post($this->restApiCaller, $param);

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
        if(array_key_exists("credential_type_id", $credential)) {
            $param['credential_type_id'] = $credential['credential_type_id'];
        }
        if(array_key_exists("ssh_private_key_pass", $credential)) {
            $param['ssh_private_key_pass'] = $credential['ssh_private_key_pass'];
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

        global $vg_tower_driver_name;

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

            // ホスト名がlocalhostでpioneer実行の場合、インベントリオプションにansible_connection: localを追加
            if(($hostname== 'localhost') &&
               ($vg_tower_driver_name == "pioneer")) {
                 $variables_array[] = "ansible_connection: local";
            }

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

    private function createEachJobTemplate($execution_no, $loopCount, $projectId, $credentialId, $vault_credentialId, $inventoryId, $runMode, $execution_environment_id) {
        global $vg_parent_playbook_name;

        $this->logger->trace(__METHOD__);

        $param = array();

        $param['execution_no'] = $execution_no;
        $param['loopCount'] = $loopCount;
        $param['inventory'] = $inventoryId;
        $param['project'] = $projectId;
        $param['playbook'] = $vg_parent_playbook_name;
        $param['credential'] = $credentialId;

        if($execution_environment_id !== false) {
            $param['execution_environment'] = $execution_environment_id;
        }

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
    private function cleanUpWorkflowJobs($execution_no) {
        $this->logger->trace(__METHOD__);

        $this->workflowJobAry       = array();
        $ret = $this->getworkflowJobs($execution_no);
        if($ret === false) {
            $this->logger->error("Faild to get workflow jobs.");
            return false;
        }
        foreach($this->workflowJobAry as $wfJobId=>$workflowJobData) {
            $response_array = AnsibleTowerRestApiWorkflowJobs::delete($this->restApiCaller, $wfJobId);
            if($response_array['success'] == false) {
                $this->logger->error("Faild to delete workflow job node.");
                $this->logger->error(var_export($response_array, true));
                return false;
            }
        }
        return true;
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

    private function cleanUpGitCredential($execution_no) {

        $this->logger->trace(__METHOD__);

        $response_array = AnsibleTowerRestApiCredentials::deleteGit($this->restApiCaller, $execution_no);

        $this->logger->trace(var_export($response_array, true));

        if($response_array['success'] == false) {
            $this->logger->debug($response_array['responseContents']['errorMessage']);
            return false;
        }

        $this->logger->trace("Clean up git credentials finished. (execution_no: $execution_no)");

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

//        $response_array = AnsibleTowerRestApiJobTemplates::deleteRelatedCurrnetExecutionForPrepare($this->restApiCaller, $execution_no);
//
//        $this->logger->trace(var_export($response_array, true));
//
//        if($response_array['success'] == false) {
//            $this->logger->debug($response_array['responseContents']['errorMessage']);
//            return false;
//        }

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

//        $response_array = AnsibleTowerRestApiJobs::deleteRelatedCurrnetExecutionForPrepare($this->restApiCaller, $execution_no);

//        $this->logger->trace(var_export($response_array, true));
//
//        if($response_array['success'] == false) {
//            $this->logger->debug($response_array['responseContents']['errorMessage']);
//            return false;
//        }

        $this->logger->trace("Clean up job templates finished. (execution_no: $execution_no)");

        return true;
    }

    function monitoring($toProcessRow, $ansibleTowerIfInfo) {

        global $vg_tower_driver_name;

        $this->logger->trace(__METHOD__);

        $execution_no = $toProcessRow['EXECUTION_NO'];

        $this->dataRelayStoragePath = $ansibleTowerIfInfo['ANSIBLE_STORAGE_PATH_LNX'];

        // ジョブワークフローの情報取得
        $this->workflowJobAry = array();
        // $wfJobDataAry[workflow job id] = workflow job detail
        $ret = $this->getworkflowJobs($execution_no);
        if($ret === false) {
            $this->logger->error("Faild to get workflow jobs.");
            return EXCEPTION;
        }

        $result_code = array();

        $this->JobDetailAry       = array();
        $this->workflowJobNodeAry = array();
        // ジョブワークフローの状態を確認
        foreach($this->workflowJobAry as $wfJobId=>$workflowJobData) {
            $ret = $this->searchworkflowJobNodesJobDetail($execution_no,$wfJobId);
            if($ret === false) {
                return EXCEPTION;
            }
            // AnsibleTower Status チェック
            $status = $this->checkWorkflowJobStatus($execution_no,$wfJobId);
            $result_code[$wfJobId] = $status; 

            // Ansibleログ書き出し
            $ret = $this->createAnsibleLogs($execution_no, $ansibleTowerIfInfo['ANSIBLE_STORAGE_PATH_LNX'],$wfJobId);  
            if($ret == false) {
                $result_code[$wfJobId] =  EXCEPTION;
            }
        }

        //ジョブワークフローの状態をマージ
        $ststus = $this->workflowStatusMerge($result_code);
        $name = "エラー";
        switch($ststus) {
        case PREPARE:          $name = "準備中";         break;
        case PROCESSING:       $name = "実行中";         break;
        case PROCESS_DELAYED:  $name = "実行中(遅延)";   break;
        case COMPLETE:         $name = "完了";           break;
        case FAILURE:          $name = "完了(異常)";     break;
        case EXCEPTION:        $name = "想定外エラー";   break;
        case SCRAM:            $name = "緊急停止";       break;
        case RESERVE:          $name = "未実行(予約中)"; break;
        case RESERVE_CANCEL:   $name = "予約取消";       break;
        }
        return $ststus;
    }

    function workflowStatusMerge($result_code) {

        $this->logger->trace(__METHOD__);

        $comp_job_count         = 0;
        $run_job_count          = 0;
        $scram_job_count        = 0;
        $exce_job_count         = 0;
        $fail_job_count         = 0;
        $status_error_job_count = 0;

        foreach($result_code as $wfJobId=>$status) {
            switch($status) {
            case COMPLETE:
                // 状態:完了のワークフローをカウント
                $comp_job_count++;
                break; 
            case PROCESSING:
                // 状態:処理中のワークフローをカウント
                $run_job_count++;
                break;
            case SCRAM:
                // 緊急停止のワークフローをカウント
                $scram_job_count++;
                break;
            case EXCEPTION:
                // 想定外エラーのワークフローをカウント
                $exce_job_count++;
                break;
            case FAILURE:
                // 異常終了のワークフローをカウント
                $fail_job_count++;
                break;
            default:
                // 状態不明のワークフローをカウント
                $status_error_job_count++;
                break;
            }
        }
        // 状態:処理中のワークフローが1件でもあれば、状態は処理中
        if($run_job_count != 0) {
            return PROCESSING;
        }
        // 全てのワークフローの状態が完了か判定
        if(count($result_code) == $comp_job_count) {
            return COMPLETE;
        }
        // 全てのワークフローの状態が緊急停止か判定
        if(count($result_code) == $scram_job_count) {
            return SCRAM;
        }
        // 全てのワークフローの状態が想定外エラーか判定
        if(count($result_code) == $exce_job_count) {
            return EXCEPTION;
        }
        // 全てのワークフローの状態が異常終了か判定
        if(count($result_code) == $fail_job_count) {
            return FAILURE;
        }
        // 状態:完了のワークフローが1件でもあれば、状態:完了(異常)
        if($comp_job_count != 0) {
            return FAILURE;
        }
        // 状態:完了と緊急停止の場合、状態:緊急停止
        if(count($result_code) == ($comp_job_count + $scram_job_count)) {
            return SCRAM;
        }
        // その他、状態が混在している場合、状態:完了(異常)
        $this->logger->error(var_export($result_code, true));
        return FAILURE;
    }

    // $workflowJobNodeAry[workflow job id][] = workflow job node detail
    // $JobDetailAry[workflow job id][workflow job node id][job id][] = array('JobData'=>Job Detail,'stdout'=>job stdout);
    // $workflowJobNodeIdAry[workflow job id][] = workflow job node id;
    function searchworkflowJobNodesJobDetail($execution_no,$wfJobId,$nodeId_only=false) {

        $this->logger->trace(__METHOD__);

        $this->workflowJobNodeAry[$wfJobId] = array();

        // workflow job idに紐づくworkflow job nodeを取得
        // /api/v2/workflow_jobs/(workflow job id)/workflow_nodes/
        $query = sprintf("%s/workflow_nodes/",$wfJobId);
        $response_array = AnsibleTowerRestApiWorkflowJobs::getAll($this->restApiCaller, $query);
        if($response_array['success'] == false) {
            $this->logger->error("Faild to rest api access get workflow job nodes.");
            $this->logger->error(var_export($response_array, true));
            return false;
        }

        foreach($response_array['responseContents'] as $workflowJobNodeData) {
            $wfJobNodeId = $workflowJobNodeData['id'];
            $this->JobDetailAry[$wfJobId][$wfJobNodeId] = array();
            $this->workflowJobNodeAry[$wfJobId][] = $workflowJobNodeData;

            if($nodeId_only === true) {
                $this->workflowJobNodeIdAry[$wfJobId][] = $wfJobNodeId;
                continue;
            }

            $type = $workflowJobNodeData['summary_fields']['job']['type'];
            // ジョブスライスが設定されているとスライスされた workfolw job (type = workfolw job)
            // の情報がJob情報として表示される
            // typeが workfolw job のjobの情報はworkfolw jobで取得出来ているので無視
            // typeがjobのデータだけを処理する。
            if($type == "job") {
                $JobId = $workflowJobNodeData['job'];
                // workflow job node IDに紐づくJobを取得
                // /api/v2/jobs/(job id)/
                $response_array = AnsibleTowerRestApiJobs::get($this->restApiCaller, $JobId);
                if($response_array['success'] == false) {
                    $this->logger->error("Faild to rest api access get job detail.");
                    $this->logger->error(var_export($response_array, true));
                    return false;
                }
                // データがなくてもエラーにしない。
                $JobDetail = $response_array['responseContents'];
                if(is_array($JobDetail)) { 
                    if(count($JobDetail)==0) {
                        $JobDetail = array();
                        // $this->logger->error("Not found to job detail.");
                        // $this->logger->error(var_export($response_array, true));
                        // return false;
                    }
                } 
                $response_array = AnsibleTowerRestApiJobs::getStdOut($this->restApiCaller, $JobId);
                if($response_array['success'] == false) {
                    $this->logger->error("Faild to get job stdout. (job id:$JobId)");
                    $this->logger->error(var_export($response_array, true));
                    return false;
                }
                $stdout = $response_array['responseContents'];
                
                $this->JobDetailAry[$wfJobId][$wfJobNodeId][] = array('JobData'=>$JobDetail,'stdout'=>$stdout);
            } else {
            }
        }
        return true;
    }
    // $wfJobDataAry[workflow job id] = workflow job detail
    function getworkflowJobs($execution_no) {

        $this->logger->trace(__METHOD__);

        $this->workflowJobAry = array();

        // 作業番号に紐づくworkflow jobを取得
        // /api/v2/workflow_jobs/?name__startswith=ita_(drive_name)_executions&name__contains=(execution_no)
        $response_array = AnsibleTowerRestApiWorkflowJobs::NameSearch($this->restApiCaller, $execution_no);
        if($response_array['success'] == false) {
            $this->logger->error("Faild to rest api access get workflow job.");
            $this->logger->error(var_export($response_array, true));
            return false;
        }
        foreach($response_array['responseContents'] as $workflowJobData) {
            $wfJobId = $workflowJobData['id'];
            $this->workflowJobAry[$wfJobId] = $workflowJobData;
        }
        // workflow jobが取得出来ない場合はエラー
        if(count($this->workflowJobAry) == 0) {
            $this->logger->error("Not found to workflow jobs.");
            $this->logger->error(var_export($response_array, true));
            return false;
        }
        return true;
    }

    function checkWorkflowJobStatus($execution_no,$wfJobId) {

        $this->logger->trace(__METHOD__);

        $workflowJobData = $this->workflowJobAry[$wfJobId];
        if(!array_key_exists("id",     $workflowJobData) ||
            !array_key_exists("status", $workflowJobData) ||
            !array_key_exists("failed", $workflowJobData)) {
            $this->logger->debug("Not expected data.");
            return EXCEPTION;
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
                $status = $this->checkAllJobsStatus($wfJobId);
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
        return $status;
    }


    function checkAllJobsStatus($wfJobId) {

        $this->logger->trace(__METHOD__);

        foreach($this->workflowJobNodeAry[$wfJobId] as $workflowJobNodeData) {
            $wfJobNodeId = $workflowJobNodeData['id'];
            foreach($this->JobDetailAry[$wfJobId][$wfJobNodeId] as $JobDetail) {
                $JobData = $JobDetail['JobData'];
                $JobId   = $JobData['id'];
                if(!array_key_exists("id",     $JobData) ||
                   !array_key_exists("status", $JobData) ||
                   !array_key_exists("failed", $JobData)) {
                    $this->logger->debug("Not expected data.");
                    return EXCEPTION;
                }
                if($JobData['status'] != "successful") {
                    return FAILURE;
                }
            }
        }
        // 全て成功
        return COMPLETE;
    }

    function createAnsibleLogs($execution_no, $dataRelayStoragePath, $wfJobId) {

        global $vg_tower_driver_type;
        global $vg_tower_driver_id;

        $this->logger->trace(__METHOD__);

        $execlogFilename        = "exec.log";
        $joblistFilename        = "joblist.txt";
        $outDirectoryPath       = $dataRelayStoragePath . "/" . $vg_tower_driver_type . '/' . $vg_tower_driver_id . '/' . addPadding($execution_no) . "/out";

        $execlogFullPath        = $outDirectoryPath . "/" . $execlogFilename;
        $joblistFullPath        = $outDirectoryPath . "/" . $joblistFilename;

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
        $ret = $this->CreateLogs($execution_no,$wfJobId,$joblistFullPath,$outDirectoryPath,$execlogFullPath);
        return $ret;
    }
    function CreateLogs($execution_no,$wfJobId,$joblistFullPath,$outDirectoryPath,$execlogFullPath) {

        $this->logger->trace(__METHOD__);

        global  $vg_tower_driver_name;

        // ワークフローの情報取得
        $workflow_contentArray = array();
        $workflowJobData = $this->workflowJobAry[$wfJobId];
        $workflow_contentArray[$wfJobId] = "  workflow_name: " . $workflowJobData['name'];
        if(@strlen($workflowJobData['result_traceback']) != 0) {
            $workflow_contentArray[$wfJobId] .= "\n" . "    status: " .  $workflowJobData['status'];
            $workflow_contentArray[$wfJobId] .= "\n" . "    result_traceback: " .  $workflowJobData['result_traceback'];
        }

        $jobSummaryAry = array();
        foreach($this->workflowJobNodeAry[$wfJobId] as $workflowJobNodeData) {
            $wfJobNodeId = $workflowJobNodeData['id'];
            foreach($this->JobDetailAry[$wfJobId][$wfJobNodeId] as $JobDetail) {
                $JobData = $JobDetail['JobData'];
                $JobId   = $JobData['id'];
                $jobName = $JobData['name'];

                $contentArray = array();

                $contentArray[] = "------------------------------------------------------------------------------------------------------------------------"; // セパレータ
                $contentArray[] = $workflow_contentArray[$wfJobId];

                $contentArray[] = "  job_name: " . $jobName;
                if(@strlen($JobData['result_traceback']) != 0) {
                    $contentArray[] = "    status: " .  $JobData['status'];
                    $contentArray[] = "    result_traceback: " .  $JobData['result_traceback'];
                }
                 
                $response_array = AnsibleTowerRestApiProjects::get($this->restApiCaller, $JobData['project']);
                if($response_array['success'] == false) {
                    $this->logger->error("Faild to get project. " . $response_array['responseContents']['errorMessage']);
                    return false;
                }
                $projectData = $response_array['responseContents'];

                $contentArray[] = "  project_name: " . $projectData['name'];
                $contentArray[] = "  project_local_path: " . $projectData['local_path'];

                //---- Ansible Tower Version Check 
                if($this->getTowerVersion() == TOWER_VER35) {
                    $response_array = AnsibleTowerRestApiCredentials::get($this->restApiCaller, $JobData['credential']);
                    if($response_array['success'] == false) {
                        $this->logger->error("Faild to get credential. " . $response_array['responseContents']['errorMessage']);
                        return false;
                    }
                    $credentialData = $response_array['responseContents'];
                } else {
                    foreach($JobData['summary_fields']['credentials'] as $CredentialArray) {
                        if($CredentialArray['kind'] != 'vault') {
                            $response_array = AnsibleTowerRestApiCredentials::get($this->restApiCaller, $CredentialArray['id']);
                            if($response_array['success'] == false) {
                                $this->logger->error("Faild to get credential. " . $response_array['responseContents']['errorMessage']);
                                return false;
                            }
                            $credentialData = $response_array['responseContents'];
                        }
                    }
                    if( ! isset($credentialData)) {
                        $this->logger->error("non set to get credential. " . $response_array['responseContents']);
                        return false;
                    }
                }
                //---- Ansible Tower Version Check 

                $contentArray[] = "  credential_name: " . $credentialData['name'];
                $contentArray[] = "  credential_type: " . $credentialData['credential_type'];
                $contentArray[] = "  credential_inputs: " . json_encode($credentialData['inputs']);
                $contentArray[] = "  virtualenv: " . $projectData['custom_virtualenv'];
                if($this->workflowJobAry[$wfJobId]['is_sliced_job'] === true) {
                    $contentArray[] = "  job_slice_count: " . $JobData["job_slice_count"];
                }
                $query = sprintf("%s/instance_groups/",$JobData['inventory']);
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
    
                $response_array = AnsibleTowerRestApiInventories::get($this->restApiCaller, $JobData['inventory']);
                if($response_array['success'] == false) {
                    $this->logger->error("Faild to get inventory. " . $response_array['responseContents']['errorMessage']);
                    return false;
                }
                $inventoryData = $response_array['responseContents'];
    
                $response_array = AnsibleTowerRestApiInventoryHosts::getAllEachInventory($this->restApiCaller, $JobData['inventory']);
    
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
                $contentArray[] = "------------------------------------------------------------------------------------------------------------------------"; // セパレータ
                $contentArray[] = "";
                $jobSummaryAry[$JobId] = join("\n", $contentArray);
            }
            $contentArray[] = ""; // 空行
        }
        ////////////////////////////////////////////////////////////////
        // 各WorkflowJobNode分のstdoutをファイル化
        ////////////////////////////////////////////////////////////////
        foreach($this->workflowJobNodeAry[$wfJobId] as $workflowJobNodeData) {
            $wfJobNodeId = $workflowJobNodeData['id'];
            foreach($this->JobDetailAry[$wfJobId][$wfJobNodeId] as $JobDetail) {
                $JobData = $JobDetail['JobData'];
                $stdout  = $JobDetail['stdout'];
                $JobId   = $JobData['id'];
                $jobName = $JobData['name'];
                if($this->workflowJobAry[$wfJobId]['is_sliced_job'] === true) {
                        // ジョブスライス数
                        $job_slice_count = $JobData['job_slice_count'];
                        $job_slice_number = $JobData['job_slice_number']; 
                        $job_slice_number_str = str_pad($job_slice_number, 10, "0", STR_PAD_LEFT );
                        $page = sprintf("\n(%d/%d)\n",$JobData['job_slice_number'],$JobData['job_slice_count']);

                } else {
                        $job_slice_number = 0;
                        $job_slice_number_str = str_pad($job_slice_number, 10, "0", STR_PAD_LEFT );
                        $page = "";
                }
                // jobサマリ出力
                $result_stdout     = $jobSummaryAry[$JobId];
                // jobログ出力
                $result_stdout    .= $page;
                $result_stdout    .= $stdout;

                // オリジナルログファイル
                $jobFileFullPath = $outDirectoryPath . "/" . $JobData['name'] . "_" . $job_slice_number_str . ".txt.org";
                if(file_put_contents($jobFileFullPath, $result_stdout) === false) {
                    $this->logger->error("Faild to write file. " . $jobFileFullPath);
                    return false;
                }
                $this->jobOrgLogFileList[$JobData['name']][] = $jobFileFullPath;

                // jobログを加工
                $result_stdout    = $this->LogReplacement($result_stdout);
                $jobFileFullPath = $outDirectoryPath . "/" . $JobData['name'] . "_" . $job_slice_number_str . ".txt";
                if(file_put_contents($jobFileFullPath, $result_stdout) === false) {
                    $this->logger->error("Faild to write file. " . $jobFileFullPath);
                    return false;
                }

                // 加工ログファイル
                $this->jobFileList[$JobData['name']][] = $jobFileFullPath;
                $this->jobLogFileList[] = basename($jobFileFullPath);
            }
        }
        // ジョブスライなどでファイルが複数に分かれた場合にファイルのリスト
        if(count($this->jobLogFileList) != 0) {
            $this->setMultipleLogFileJsonAry($execution_no, $this->jobLogFileList);
        }
        
        // ジョブスライなどでファイルが複数に分かれた場合のマーク
        if(count($this->jobLogFileList) > 1) {
            $this->settMultipleLogMark($execution_no, $outDirectoryPath);
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


            // 全ジョブのオリジナルログファイル
            $execlogContent = "";
            foreach($this->jobOrgLogFileList as $jobName => $jobFileFullPathAry) {
                foreach($jobFileFullPathAry as $jobFileFullPath) {
//                    $execlogContent .= "\n";
//                    $execlogContent .= "========================================================================================================================\n"; // セパレータ
//                    $execlogContent .= "[job_stdout: " . $jobName . "]\n";
//                    $execlogContent .= "\n";
                    $jobFileContent = file_get_contents($jobFileFullPath);
                    if($jobFileContent === false) {
                        $this->logger->error("Faild to read file. " . $jobFileFullPath);
                        return false;
                    }
                    $execlogContent .= $jobFileContent . "\n";
                }
            }
            $execlogFullPath_org  = $execlogFullPath . ".org";

            if(file_put_contents($execlogFullPath_org, $execlogContent) === false){
                $this->logger->error("Faild to write file. " . $execlogFullPath);
                return false;
            }

            // 全ジョブの加工ログファイル
            $execlogContent = "";
            foreach($this->jobFileList as $jobName => $jobFileFullPathAry) {
                foreach($jobFileFullPathAry as $jobFileFullPath) {
//                    $execlogContent .= "\n";
//                    $execlogContent .= "========================================================================================================================\n"; // セパレータ
//                    $execlogContent .= "[job_stdout: " . $jobName . "]\n";
//                    $execlogContent .= "\n";
                    $jobFileContent = file_get_contents($jobFileFullPath);
                    if($jobFileContent === false) {
                        $this->logger->error("Faild to read file. " . $jobFileFullPath);
                        return false;
                    }
                    $execlogContent .= $jobFileContent . "\n";
                }
            }


            if(file_put_contents($execlogFullPath, $execlogContent) === false){
                $this->logger->error("Faild to write file. " . $execlogFullPath);
                return false;
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

            if(preg_match('/\\n$/',$message) == 0) $message.= "\n";

            $ret = file_put_contents($errorLogfile, $message, FILE_APPEND | LOCK_EX);
            if($ret === false) {
                $this->logger->error("Error. Faild to write message. $message");
            }
        }
    }
    function geterrorLogPath() {
        $errorLogfile = "";
        if($this->exec_out_dir != "" && file_exists($this->exec_out_dir)) {
            $errorLogfile = $this->exec_out_dir . "/" . "error.log";
        }
        return $errorLogfile;
    }

    function getMaterialsTransferSourcePath($dataRelayStoragePath,$execution_no) {
        global $vg_tower_driver_type;
        global $vg_tower_driver_id;

        $path = sprintf("%s/%s/%s/%s/in",$dataRelayStoragePath,$vg_tower_driver_type,$vg_tower_driver_id,addPadding($execution_no));
        return $path;
    }

    function getMaterialsTransferDestinationPath($PathId,$execution_no) {
        global $vg_tower_driver_name;
        global $vg_TowerProjectPath;
        global $vg_TowerExastroProjectPath;

        $path = array();
        $path["TowerPath"]   = sprintf("%s/ita_%s_executions_%s",$vg_TowerProjectPath,       $vg_tower_driver_name,addPadding($execution_no)); 
        $path["ExastroPath"] = sprintf("%s/ita_%s_executions_%s",$vg_TowerExastroProjectPath,$vg_tower_driver_name,addPadding($execution_no)); 
        return $path[$PathId];
    }

    function LogReplacement($log_data) {
        global $vg_tower_driver_name;
        // /exastro/ita-root/libs/restapiindividuallibs/ansible_driver/execute_statuscheck.phpに同等の処理あり
        if($vg_tower_driver_name == "pioneer") {
            // ログ(", ")  =>  (",\n")を改行する
            $log_data = preg_replace( "/\", \"/","\",\n\"",$log_data,-1,$count);
            // 改行文字列\\r\\nを改行コードに置換える
            $log_data = preg_replace( '/\\\\\\\\r\\\\\\\\n/', "\n",$log_data,-1,$count);
            // 改行文字列\r\nを改行コードに置換える
            $log_data = preg_replace( '/\\\\r\\\\n/', "\n",$log_data,-1,$count);
            // python改行文字列\\nを改行コードに置換える
            $log_data = preg_replace( "/\\\\\\\\n/", "\n",$log_data,-1,$count);
            // python改行文字列\nを改行コードに置換える
            $log_data = preg_replace( "/\\\\n/", "\n",$log_data,-1,$count);
        } else {
            // ログ(", ")  =>  (",\n")を改行する
            $log_data = preg_replace( "/\", \"/","\",\n\"",$log_data,-1,$count);
            // ログ(=> {)  =>  (=> {\n)を改行する
            $log_data = preg_replace( "/=> {/", "=> {\n",$log_data,-1,$count);
            // ログ(, ")  =>  (,\n")を改行する
            $log_data = preg_replace( "/, \"/", ",\n\"",$log_data,-1,$count);
            // 改行文字列\\r\\nを改行コードに置換える
            $log_data = preg_replace( '/\\\\\\\\r\\\\\\\\n/', "\n",$log_data,-1,$count);
            // 改行文字列\r\nを改行コードに置換える
            $log_data = preg_replace( '/\\\\r\\\\n/', "\n",$log_data,-1,$count);
            // python改行文字列\\nを改行コードに置換える
            $log_data = preg_replace( "/\\\\\\\\n/", "\n",$log_data,-1,$count);
            // python改行文字列\nを改行コードに置換える
            $log_data = preg_replace( "/\\\\n/", "\n",$log_data,-1,$count);
        }
        return($log_data);
    }

    function getMultipleLogMark() {
        return $this->MultipleLogMark;
    }
    function settMultipleLogMark($execution_no, $dataRelayStoragePath) {
        $this->MultipleLogMark = "1";
    }
    function getMultipleLogFileJsonAry() {
        return $this->MultipleLogFileJsonAry;
    }
    function setMultipleLogFileJsonAry($execution_no, $MultipleLogFileNameList) {
        $this->MultipleLogFileJsonAry  = json_encode($MultipleLogFileNameList);
        return true;
    }

    function __destruct() {
    }

    function createGitRepo($GitObj, $SrcFilePath) {

        $GitObj->ClearLastErrorMsg();
        $ret = $GitObj->GitRepoDirDelete();
        if($ret === false) {
            $log         = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2003",array($GitObj->GetLastErrorMsg()));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            return false;
        }
        $GitObj->ClearLastErrorMsg();
        $ret = $GitObj->GitInit();
        if($ret === false) {
            $log         = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2003",array($GitObj->GetLastErrorMsg()));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            return false;
        }
        $GitObj->ClearLastErrorMsg();
        $ret = $GitObj->GitAddFiles($SrcFilePath);
        if($ret === false) {
            $log         = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2003",array($GitObj->GetLastErrorMsg()));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            return false;
        }
        $GitObj->ClearLastErrorMsg();
        $ret = $GitObj->GitCommit();
        if($ret === false) {
            $log         = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2003",array($GitObj->GetLastErrorMsg()));
            $this->errorLogOut($log);
            $this->logger->error($log);
    
            return false;
        }
        return true;
    }
    
    function deleteMaterialsTransferTempDir($execution_no) {
        global $vg_tower_driver_name;
        $tmp_path_ary = getInputDataTempDir($execution_no, $vg_tower_driver_name);
        $src_path     = $tmp_path_ary["DIR_NAME"];
        // Gitリポジトリ用の一時ディレクトリを削除
        if(file_exists($src_path)) {
            $cmd = sprintf("/bin/rm -rf %s 2>&1",
                            $src_path);
            exec($cmd,$arry_out,$return_var);
            if($return_var !== 0) {
                $log         = implode("\n",$arry_out);
                $log         .= "\n".$this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2002",array($cmd));
                $this->errorLogOut($log);
                $this->logger->error($log);
                return false;
            }
        }
        return true;
    }

    function createProjectStatusCheck($response_array) {

        $this->logger->trace(__METHOD__);

        $projectId = $response_array['responseContents']['id'];
        // Git連携の状態を確認する
        for(;;) {
            switch($response_array['responseContents']['status']) {
            case "new":
            case "pending":
            case "waiting":
            case "running":
                sleep(5);
                $response_array =  AnsibleTowerRestApiProjects::get($this->restApiCaller, $projectId);
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error(var_export($response_array,true));
                    return -1;
                }
                break;
            case "successful":
                return true;
                break;
            case "failed":
            case "error":
                // プロジェクト更新用のURL退避
                $updateUurl = $response_array['responseContents']['related']['update'];
                // Git連携に失敗した場合、エラー情報を取得する
                $url = $response_array['responseContents']['related']['project_updates'];
                $response_array =  AnsibleTowerRestApirPassThrough::get($this->restApiCaller, $url);
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error(var_export($response_array,true));
                    return -1;
                }
                $url = $response_array['responseContents']['results'][0]['related']['stdout'] . "?format=txt";
                $response_array =  AnsibleTowerRestApirPassThrough::get($this->restApiCaller, $url, true);
                $ProjectUpdateStdout = $response_array['responseContents'];
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error(var_export($response_array,true));
                    return -1;
                }
                // 制御ノードにコンテナイメージがロードされていないと、プロジェクト作成でGit連携が失敗する
                // プロジェクトの更新だとコンテナイメージがロードていなくても問題ないので、プロジェクトを更新する
                $response_array =  AnsibleTowerRestApirPassThrough::post($this->restApiCaller, $updateUurl);
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error(var_export($response_array,true));
                    return -1;
                }
                $ret = $this->projectUpdate($response_array);
                if($ret === true) {
                    return true;
                }
                return -1;
                break;
            default:
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                $this->errorLogOut($errorMessage);
                $this->logger->error(var_export($response_array,true));
                return -1;
                break;
            }
        }
    }

    function projectUpdate($response_array) {
        $url = $response_array['responseContents']['url'];
        // プロジェクト更新の結果判定
        for(;;) {
            $response_array =  AnsibleTowerRestApirPassThrough::get($this->restApiCaller, $url);
            if($response_array['success'] == false) {
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                $this->errorLogOut($errorMessage);
                $this->logger->error(var_export($response_array,true));
                return response_array;
            }
            switch($response_array['responseContents']['status']) {
            case "new":
            case "pending":
            case "waiting":
            case "running":
                sleep(5);
                break;
            case "successful":
                return true;
                break;
            case "failed":
            case "error":
                $url = $response_array['responseContents']['related']['stdout'] . "?format=txt";
                $response_array =  AnsibleTowerRestApirPassThrough::get($this->restApiCaller, $url, true);
                $ProjectUpdateStdout = $response_array['responseContents'];
                if($response_array['success'] == false) {
                    $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                    $this->errorLogOut($errorMessage);
                    $this->logger->error(var_export($response_array,true));
                    return -1;
                }
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2005",array(print_r($ProjectUpdateStdout,true)));
                $this->errorLogOut($errorMessage);
                return -1;
                break;
            default:
                $errorMessage = $this->objMTS->getSomeMessage("ITAANSIBLEH-ERR-2006");
                $this->errorLogOut($errorMessage);
                $this->logger->error(var_export($response_array,true));
                return -1;
                break;
            }
        }
    }
}

function getGitSshKeyFileContent($systemId, $sshKeyFileName) {

    global $root_dir_path;

    $ssh_key_file_dir = $root_dir_path . "/uploadfiles/2100040702/ANS_GIT_SSH_KEY_FILE/";

    $content = "";

    $filePath = $ssh_key_file_dir . addPadding($systemId) . "/" . $sshKeyFileName;
    $content = file_get_contents($filePath);

    $content = ky_decrypt($content);

    return $content;
}

function getSshKeyFileContent($systemId, $sshKeyFileName) {

    global $root_dir_path;

    $ssh_key_file_dir = $root_dir_path . "/uploadfiles/2100000303/CONN_SSH_KEY_FILE/";

    $content = "";

    $filePath = $ssh_key_file_dir . addPadding($systemId) . "/" . $sshKeyFileName;
    $content = file_get_contents($filePath);

    return $content;
}

function getAnsibleTowerSshKeyFileContent($TowerHostID, $sshKeyFileName) {
   
    global $root_dir_path;
   
    $ssh_key_file_dir = $root_dir_path . "/uploadfiles/2100040708/ANSTWR_LOGIN_SSH_KEY_FILE/";
   
    $filePath = $ssh_key_file_dir . addPadding($TowerHostID) . "/" . $sshKeyFileName;
   
    return $filePath;
}

?>
