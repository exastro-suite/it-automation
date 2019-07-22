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
//      AnsibleTower作業実行
//      実行開始に成功した場合はステータスを実行中にする。失敗した場合は失敗にする。
//
//////////////////////////////////////////////////////////////////////

function AnsibleTowerExecution($function,$ansibleTowerIfInfo,&$toProcessRow,$exec_out_dir,$UIExecLogPath,$UIErrorLogPath,&$status='',$JobTemplatePropertyParameterAry=array(),$JobTemplatePropertyNameAry=array()) {

global $root_dir_path;
global $log_output_dir;
global $log_file_prefix;
global $log_output_php;
global $log_level;
global $db_access_user_id;
global $error_flag;
global $warning_flag;
global $objMTS;
global $objDBCA;

    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/DBAccesser.php");
    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/LogWriter.php");
    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/RestApiCaller.php");
    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/ExecuteDirector.php");
    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerCommonLib.php");
    require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php" );
    
    ////////////////////////////////
    // ログ出力設定
    ////////////////////////////////
    $logger             = LogWriter::getInstance();
    
    $logger->setUp($root_dir_path . '/' . $log_output_php, $log_output_dir, $log_file_prefix, $log_level, $UIExecLogPath, $UIErrorLogPath);
    
    $msgTplStorage      = $objMTS;
    
    ////////////////////////////////
    // 共通モジュールの呼び出し
    ////////////////////////////////
    $ansibletower_common_lib_php   = $root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerCommonLib.php";
    require_once($ansibletower_common_lib_php);
    
    ////////////////////////////////
    // 業務処理開始
    ////////////////////////////////
    
    
    $dbAccess = null;
    $restApiCaller = null;
    $process_has_error = false;
    
    try {
        $tgt_execution_no = $toProcessRow['EXECUTION_NO'];
        $dbAccess = new DBAccesser($db_access_user_id,$objDBCA);
    
        ////////////////////////////////
        // 接続トークン取得
        ////////////////////////////////
        // トレースメッセージ
        $logger->debug("Authorize AnsibleTower.");
    
        $restApiCaller = new RestApiCaller($ansibleTowerIfInfo['ANSIBLE_PROTOCOL'],
                                           $ansibleTowerIfInfo['ANSIBLE_HOSTNAME'],
                                           $ansibleTowerIfInfo['ANSIBLE_PORT'],
                                           $ansibleTowerIfInfo['ANSTWR_AUTH_TOKEN']);
                                       
        $response_array = $restApiCaller->authorize();
        if($response_array['success'] != true) {
            $process_has_error = true;
            $error_flag = 1;
            $logger->trace("URL: ". $ansibleTowerIfInfo['ANSIBLE_PROTOCOL'] . "://"
                                  . $ansibleTowerIfInfo['ANSIBLE_HOSTNAME'] . ":"
                                  . $ansibleTowerIfInfo['ANSIBLE_PORT'] . "\n"
                                  . "TOKEN: " . $ansibleTowerIfInfo['ANSTWR_AUTH_TOKEN'] . "\n");
    
            $logger->error("Faild to authorize to ansible_tower. " . $response_array['responseContents']['errorMessage']);
        }

        $workflowTplId = -1;
        $director = null;
        if(!$process_has_error) {
            // トレースメッセージ
            $logger->debug("maintenance environment (exec_no: $tgt_execution_no)");
    
            $director = new ExecuteDirector($restApiCaller, $logger, $dbAccess, $exec_out_dir, $JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry);
        }
        switch($function) {
        case DF_EXECUTION_FUNCTION:
            if($process_has_error) {
                break;
            }
            ////////////////////////////////////////////////////////////////
            // AnsibleTowerに必要なデータを生成                           //
            ////////////////////////////////////////////////////////////////
            $workflowTplId = $director->build($toProcessRow, $ansibleTowerIfInfo,
                                              $JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry);
            if($workflowTplId == -1) {
                // メイン処理での異常フラグをON
                $process_has_error = true;
                $error_flag = 1;
                $logger->error("Faild to create ansibletower environment. (exec_no: $tgt_execution_no)");
            }
            $wfId = -1;
            $process_was_scrammed = false;
            if(!$process_has_error) {
                // トレースメッセージ
                $logger->debug("launch (exec_no: $tgt_execution_no)");

                // 実行直前に緊急停止確認
                if(isScrammedExecution($dbAccess, $tgt_execution_no)) {
                    $process_was_scrammed = true;
                } else {
                    // ジョブワークフロー実行
                    $wfId = $director->launchWorkflow($workflowTplId);
                    if($wfId == -1) {
                        $process_has_error = true;
                        $error_flag = 1;
                        $logger->error("Faild to launch workflowJob. (exec_no: $tgt_execution_no)");
                        $errorMessage = $msgTplStorage->getSomeMessage("ITAANSIBLEH-ERR-6040008");
                        $director->errorLogOut($errorMessage);
                    } else {
                        $logger->debug("execution start up complated. (exec_no: $tgt_execution_no)");
                    }
                }
            }
            // 実行結果登録
            $toProcessRow['LAST_UPDATE_USER']  = $db_access_user_id;
            if($process_was_scrammed) {
                // 緊急停止時
                $toProcessRow['TIME_START'] = "DATETIMEAUTO(6)";
                $toProcessRow['TIME_END']   = "DATETIMEAUTO(6)";
                $toProcessRow['STATUS_ID']  = SCRAM;
            } else if($process_has_error) {
                // 異常時
                $toProcessRow['TIME_START'] = "DATETIMEAUTO(6)";
                $toProcessRow['TIME_END']   = "DATETIMEAUTO(6)";
                $toProcessRow['STATUS_ID']  = FAILURE;
                $status = FAILURE;
            } else {
                // 正常時
                $toProcessRow['TIME_START'] = "DATETIMEAUTO(6)";
                $toProcessRow['STATUS_ID']  = PROCESSING;
                $status = PROCESSING;
            }
            // 実行失敗時にはここで消す、成功時には確認君で確認して消す
            if(($process_was_scrammed || $process_has_error) &&
                $ansibleTowerIfInfo['ANSTWR_DEL_RUNTIME_DATA'] == 1 &&
                $director != null) {
                 $ret = $director->delete($tgt_execution_no);
                 if($ret == false) {
                     $warning_flag = 1;
                 $logger->error("Faild to cleanup ansibletower environment. (exec_no: $tgt_execution_no)");
                 } else {
                     $logger->debug("Cleanup ansibletower environment SUCCEEDED. (exec_no: $tgt_execution_no)");
                 }
            }
            break;
        case DF_CHECKCONDITION_FUNCTION:
            if($process_has_error) {
                break;
            }
            $record_status = '';
            $execution_finished_flag = false;
            // データ準備
            $tgt_execution_no = $toProcessRow['EXECUTION_NO'];

            // この時点で作業実行レコードのステータス再取得して緊急停止ボタンが押されていれば、最後のレコード更新ステータスをSCRAMにする
            // ただし、処理中のステータスはTowerから取得した値を見て処理を分ける
            $record_status = "";
            if(isScrammedExecution($dbAccess, $tgt_execution_no)) {
                $record_status = SCRAM;
            }

            ////////////////////////////////////////////////////////////////
            // AnsibleTower監視
            ////////////////////////////////////////////////////////////////
            // トレースメッセージ
            $logger->debug("monitoring environment (exec_no: $tgt_execution_no)");

            $director = new ExecuteDirector($restApiCaller, $logger, $dbAccess, "");
            $status = $director->monitoring($toProcessRow, $ansibleTowerIfInfo);

            ////////////////////////////////////////////////////////////////
            // 遅延チェック                                         //
            ////////////////////////////////////////////////////////////////
            switch($status) {
            case PROCESSING:
            case COMPLETE:
            case FAILURE:
            case SCRAM:
            case EXCEPTION:
                break;
            default:
                $error_flag = 1;
                $status = EXCEPTION;
                break;
            }
    
            // 確認前に取得したステータスがSCRAMであれば、どんな結果でもSCRAMにする
            if($record_status == SCRAM) {
                $status = SCRAM;
            }

            ////////////////////////////////////////////////////////////////
            // 実行結果登録                                         //
            ////////////////////////////////////////////////////////////////
            if($process_has_error) {
                $status  = FAILURE;
            }

            // トレースメッセージ
            $logger->debug("Update execution_management row. status=>" . $status);
            break;
        case DF_DELETERESOURCE_FUNCTION:
            if($process_has_error) {
                break;
            }
            $finishedStatusArray = array(COMPLETE, FAILURE, EXCEPTION, SCRAM);
            if(in_array($status, $finishedStatusArray)) {
                $execution_finished_flag = true;
            }

            if($ansibleTowerIfInfo['ANSTWR_DEL_RUNTIME_DATA'] == 1 && $director != null) {
                $ret = $director->delete($tgt_execution_no);
                if($ret == false) {
                    $warning_flag = 1;
                    $logger->error("Faild to clean up ansibletower environment. (exec_no: $tgt_execution_no)");
                } else {
                    $logger->debug("Clean up ansibletower environment SUCCEEDED. (exec_no: $tgt_execution_no)");
                }
            }
            break;
        }

        unset($dbAccess);
        unset($restApiCaller);
        return true;

    } catch (Exception $e) {
        $error_flag = 1;
        throw new Exception($e->getMessage());
    }
}
?>
