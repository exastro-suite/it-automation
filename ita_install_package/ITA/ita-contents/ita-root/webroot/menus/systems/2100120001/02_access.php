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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");

    //-- サイト個別PHP要素、ここから--
    require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_functions.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');

    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--
        function RestartFunction($p_tid_for_tag_identify, $tgtRepoId , $p_last_updatetime_for_update ){
            // グローバル変数宣言
            global $g;
            global $db_model_ch;

            // ローカル変数宣言
            $strFxName = __FUNCTION__;

            $arrayResult = array();
            $intErrorType = 0;
            $strMsgBody = "";
            $output_str = '';

            $logfile    = "";
            $log_level  = "";
            $RepositoryNo = '-';

            $p_last_updatetime_for_update = rawurldecode(base64_decode($p_last_updatetime_for_update));

            try {
                $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($g['objMTS']);

                $cmDBobj = new CommonDBAccessCoreClass($db_model_ch,$g['objDBCA'],$g['objMTS'],$g['login_id']);

                $DBobj = new LocalDBAccessClass($db_model_ch,$cmDBobj,$g['objDBCA'],$g['objMTS'],$g['login_id'],$logfile,$log_level,$RepositoryNo);

                $objTextVldt = new SingleTextValidator(22,22,false);
                if( $objTextVldt->isValid($p_last_updatetime_for_update) === false ){
                    $logstr = "Input parameters failed.";
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,"");
                    throw new Exception($FREE_LOG);
                }

                // DBアクセスを伴う処理開始
                $ret = $DBobj->transactionStart();
                if($ret !== true) {
                    $logstr = "db access failed.";
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                    throw new Exception($FREE_LOG);
                }

                $dbAcction      = "SELECT FOR UPDATE";
                $BindArray      = array('WHERE'=>"REPO_ROW_ID=:REPO_ROW_ID AND DISUSE_FLAG=:DISUSE_FLAG");
                $TDobj          = new TD_B_CICD_REPOSITORY_LIST();
                $ret = $TDobj->setConfig($cmDBobj);
                if($ret === false) {
                    $logstr = "db access failed.";
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDobj->GetLastErrorMsg());
                    throw new Exception($FREE_LOG);
                }

                $ColumnConfigArray = $TDobj->getColumnDefine();
                $ColumnValueArray  = $TDobj->setColumnConfigAttr();
                $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDobj,$ColumnConfigArray,$ColumnValueArray);
                $arrayBind = array();
                $arrayBind = array("REPO_ROW_ID"=>$tgtRepoId,"DISUSE_FLAG"=>"0");
                $objQuery  = $DBobj->SelectForSimple($objQueryArray[1],$arrayBind);
                if($objQuery === false) {
                    $logstr = "db access failed.";
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                    throw new Exception($FREE_LOG);
                }
                $num_of_rows = $objQuery->effectedRowCount();
                if($num_of_rows != 1) {
                    $strMsgBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2005",array($tgtRepoId));
                    $intErrorType = 1;
                }
                if($intErrorType == 0) {
                    $row = $objQuery->resultFetch();

                    // 最終更新日付を確認し別セッションで更新されていないことを確認
                    if( $row['UPD_UPDATE_TIMESTAMP'] != $p_last_updatetime_for_update){
                        $strMsgBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2006",array($tgtRepoId));
                        $intErrorType = 1;
                    }
                    // 同期状態が異常でない場合
                    if( $row['SYNC_STATUS_ROW_ID'] != $SyncStatusNameobj->ERROR()){
                        $strMsgBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2007",array($tgtRepoId));
                        $intErrorType = 1;
                    }
                }
                if($intErrorType == 0) {

                    // 同期状態を再開に設定
                    $row['SYNC_STATUS_ROW_ID'] = $SyncStatusNameobj->RESTART();
                    // 詳細情報をクリア
                    $row['SYNC_ERROR_NOTE'] = "";

                    $BindArray      = array();
                    $ColumnConfigArray = $TDobj->setColumnConfigAttr();
                    $ColumnValueArray  = $TDobj->getColumnDefine();
                    $ColumnValueArray  = $row;
                    $JnlInsert_Flag    = true;
                    $ret = $DBobj->UpdateRow($BindArray,$TDobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
                    if($ret === false) {
                        $logstr = "db access failed.";
                        $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                        throw new Exception($FREE_LOG);
                    }

                    $strFxName = "[FILE]:" . basename(__FILE__) . " [LINE]:" . __LINE__;
                    $strQuery = "UPDATE T_CICD_SYNC_STATUS SET SYNC_LAST_TIMESTAMP = null "
                               ." WHERE ROW_ID = :ROW_ID ";
                    $aryForBind = array('ROW_ID'=>$tgtRepoId);
                    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                    if( $aryRetBody[0] !== true ){
                        $logstr = "db access failed.";
                        $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,"");
                        throw new Exception($FREE_LOG);
                    }

                    $ret = $DBobj->transactionCommit();
                    if($ret !== true) {
                        $logstr = "db access failed.";
                        $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                        throw new Exception($FREE_LOG);
                    }
                }
            }
            catch (Exception $e){
                $tmpErrMsgBody = $e->getMessage();
        
                if ( $intErrorType == 0) $intErrorType = 500;

                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
                if( 500 <= $intErrorType ){
                    $strMsgBody = "";
                }
                $DBobj->transactionRollBack();
            }
    
            if( $intErrorType == 0 ){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",$strFxName));
            } else {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",$strFxName));
            }
    
            $arrayResult[] = sprintf("%03d", $intErrorType);
            $arrayResult[] = $strMsgBody;
            $arrayResult[] = $p_tid_for_tag_identify;
    
            $output_str = makeAjaxProxyResultStream($arrayResult);
    
            return $output_str;
        }
    }
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();
?>

