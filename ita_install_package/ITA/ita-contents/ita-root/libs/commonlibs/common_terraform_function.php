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

    //----------------------------------------------
    // インタフェース情報からAPIを実行するためのインタフェース情報を取得
    //----------------------------------------------
    function getInterfaceInfo(){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{

            $sql = "SELECT *
                    FROM   B_TERRAFORM_IF_INFO
                    WHERE  DISUSE_FLAG = '0' ";
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);

            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;

        }

        return($retArray);

    }




    //----------------------------------------------
    // Organizationsテーブルからデータを取得
    //----------------------------------------------
    function getOrganizationData($organizationID){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{
            $sql = "SELECT *
                    FROM   B_TERRAFORM_ORGANIZATIONS
                    WHERE  DISUSE_FLAG = '0' AND ORGANIZATION_ID = :ORGANIZATION_ID";
            $tmpAryBind = array('ORGANIZATION_ID' => $organizationID);
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, __FUNCTION__);
            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[0] = false;
            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;
        }

        return($retArray);
    }



    //----------------------------------------------
    // Workspacesテーブルからデータを取得
    //----------------------------------------------
    function getWorkspaceData($workspaceID){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{
            $sql = "SELECT *
                    FROM   B_TERRAFORM_WORKSPACES
                    WHERE  DISUSE_FLAG = '0' AND WORKSPACE_ID = :WORKSPACE_ID";
            $tmpAryBind = array('WORKSPACE_ID' => $workspaceID);
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, __FUNCTION__);
            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[0] = false;
            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;
        }

        return($retArray);
    }
    ////////////////////////////
    //  Workspaceのdestroy作業インスタンスを登録する //
    ////////////////////////////
    function destroyInsRegister($destroyData) {
        global $g, $objDBCA, $objMTS;

        $root_dir_path = $g["root_dir_path"];

        require_once $root_dir_path . "/libs/backyardlibs/common/common_functions.php";

        $run_mode_destroy = 3; // RUN_MODE(3:リソース削除)
        $status_in_preparation = 1; //ステータス：準備中

        $exe_no            = ""; //実行No
        $jnl_exe_no        = ""; //実行No
        if (isset($destroyData["EXE_USER_ID"]) && !empty($destroyData["EXE_USER_ID"])) {
            $exe_user_id       = $destroyData["EXE_USER_ID"];
        } else {
            throw new Exception($objMTS->getSomeMessage('ITATERRAFORM-ERR-142019', array(basename(__FILE__), __LINE__, "EXE_USER_ID")));
        }
        if (isset($destroyData["EXE_USER_ID"]) && !empty($destroyData["EXE_USER_ID"])) {
            $workspace_id      = $destroyData["WORKSPACE_ID"]; //ワークスペースID
        } else {
            throw new Exception($objMTS->getSomeMessage('ITATERRAFORM-ERR-142019', array(basename(__FILE__), __LINE__, "WORKSPACE_ID")));
        }
        $exe_user_name     = ""; //実行ユーザ
        $date              = ""; //日時
        $db_access_user_id = -101801; // Terraform状態確認プロシージャ
        $sequenceName      = "C_TERRAFORM_EXE_INS_MNG_RIC";
        $jnlSequenceName   = "C_TERRAFORM_EXE_INS_MNG_JSQ";
        $retArray          = array(false);

        try {
            ////////////////////////////////
            // トランザクション開始       //
            ////////////////////////////////
            if ($objDBCA->transactionStart() === false) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003", array(__FILE__, __LINE__, "00001000")));
            }

            // workspaceName、organizationIDの取得とITA登録チェック
            $workspaceData = getWorkspaceData($workspace_id);
            if (!$workspaceData[0]) {
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-142020", array($workspace_id)));
            }
            $workspace_name = $workspaceData[1]["WORKSPACE_NAME"];
            $organization_id = $workspaceData[1]["ORGANIZATION_ID"];

            // organizationNameの取得
            $organizationData = getOrganizationData($organization_id);
            if (!$organizationData[0]) {
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-142021", array($organization_id)));
            }
            $organization_name = $organizationData[1]["ORGANIZATION_NAME"];

            // bindする値を取得
            // EXECUTE_NOを取得
            $exe_no = getSequenceID($sequenceName);
            // EXECUTION_USER名を取得
            $exe_user_name = getUserName($exe_user_id);
            // JNL_EXECUTE_NOを取得
            $jnl_exe_no = getSequenceID($jnlSequenceName);
            // workspace_id
            $workspace_id = $destroyData["WORKSPACE_ID"];
            //現在日時の取得
            $date = date("Y-m-d H:i:s");

            $sql = "INSERT INTO C_TERRAFORM_EXE_INS_MNG (
                EXECUTION_NO,
                EXECUTION_USER,
                SYMPHONY_NAME,
                STATUS_ID,
                SYMPHONY_INSTANCE_NO,
                PATTERN_ID,
                I_PATTERN_NAME,
                I_TIME_LIMIT,
                I_TERRAFORM_RUN_ID,
                I_TERRAFORM_WORKSPACE_ID,
                I_TERRAFORM_ORGANIZATION_WORKSPACE,
                OPERATION_NO_UAPK,
                I_OPERATION_NAME,
                I_OPERATION_NO_IDBH,
                CONDUCTOR_NAME,
                CONDUCTOR_INSTANCE_NO,
                TIME_BOOK,
                TIME_START,
                TIME_END,
                FILE_INPUT,
                FILE_RESULT,
                RUN_MODE,
                DISP_SEQ,
                ACCESS_AUTH,
                NOTE,
                DISUSE_FLAG,
                LAST_UPDATE_TIMESTAMP,
                LAST_UPDATE_USER
            ) VALUES (
                :EXECUTION_NO,
                :EXECUTION_USER,
                :SYMPHONY_NAME,
                :STATUS_ID,
                :SYMPHONY_INSTANCE_NO,
                :PATTERN_ID,
                :I_PATTERN_NAME,
                :I_TIME_LIMIT,
                :I_TERRAFORM_RUN_ID,
                :I_TERRAFORM_WORKSPACE_ID,
                :I_TERRAFORM_ORGANIZATION_WORKSPACE,
                :OPERATION_NO_UAPK,
                :I_OPERATION_NAME,
                :I_OPERATION_NO_IDBH,
                :CONDUCTOR_NAME,
                :CONDUCTOR_INSTANCE_NO,
                :TIME_BOOK,
                :TIME_START,
                :TIME_END,
                :FILE_INPUT,
                :FILE_RESULT,
                :RUN_MODE,
                :DISP_SEQ,
                :ACCESS_AUTH,
                :NOTE,
                :DISUSE_FLAG,
                :LAST_UPDATE_TIMESTAMP,
                :LAST_UPDATE_USER
            )";

            $bindAry = array(
                "EXECUTION_NO"                       => $exe_no,
                "EXECUTION_USER"                     => $exe_user_name,
                "SYMPHONY_NAME"                      => NULL,
                "STATUS_ID"                          => $status_in_preparation,
                "SYMPHONY_INSTANCE_NO"               => NULL,
                "PATTERN_ID"                         => NULL,
                "I_PATTERN_NAME"                     => NULL,
                "I_TIME_LIMIT"                       => NULL,
                "I_TERRAFORM_RUN_ID"                 => NULL,
                "I_TERRAFORM_WORKSPACE_ID"           => $workspace_id,
                "I_TERRAFORM_ORGANIZATION_WORKSPACE" => "$organization_name:$workspace_name",
                "OPERATION_NO_UAPK"                  => NULL,
                "I_OPERATION_NAME"                   => NULL,
                "I_OPERATION_NO_IDBH"                => NULL,
                "CONDUCTOR_NAME"                     => NULL,
                "CONDUCTOR_INSTANCE_NO"              => NULL,
                "TIME_BOOK"                          => NULL,
                "TIME_START"                         => NULL,
                "TIME_END"                           => NULL,
                "FILE_INPUT"                         => NULL,
                "FILE_RESULT"                        => NULL,
                "RUN_MODE"                           => $run_mode_destroy,
                "DISP_SEQ"                           => NULL,
                "ACCESS_AUTH"                        => NULL,
                "NOTE"                               => NULL,
                "DISUSE_FLAG"                        => 0,
                "LAST_UPDATE_TIMESTAMP"              => $date,
                "LAST_UPDATE_USER"                   => $db_access_user_id
            );

            $objQuery = $objDBCA->sqlPrepare($sql);
            if ($objQuery->getStatus() === false) {
                $res = $objDBCA->transactionRollback();
                if ($res === false) {
                    throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
                }
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }
            $res = $objQuery->sqlBind($bindAry);
            $res = $objQuery->sqlExecute();
            if ($res === false) {
                $res = $objDBCA->transactionRollback();
                if ($res === false) {
                    throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
                }
                web_log($objQuery->getLastError());
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }
            // シーケンスのアップデート
            $updated = updateSequenceID($sequenceName);

            if ($updated == false) {
                $res = $objDBCA->transactionRollback();
                if ($res === false) {
                    throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
                }
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }

            // 履歴テーブルの更新
            $sql = "INSERT INTO C_TERRAFORM_EXE_INS_MNG_JNL (
                        JOURNAL_SEQ_NO,
                        JOURNAL_REG_DATETIME,
                        JOURNAL_ACTION_CLASS,
                        EXECUTION_NO,
                        EXECUTION_USER,
                        SYMPHONY_NAME,
                        STATUS_ID,
                        SYMPHONY_INSTANCE_NO,
                        PATTERN_ID,
                        I_PATTERN_NAME,
                        I_TIME_LIMIT,
                        I_TERRAFORM_RUN_ID,
                        I_TERRAFORM_WORKSPACE_ID,
                        I_TERRAFORM_ORGANIZATION_WORKSPACE,
                        OPERATION_NO_UAPK,
                        I_OPERATION_NAME,
                        I_OPERATION_NO_IDBH,
                        CONDUCTOR_NAME,
                        CONDUCTOR_INSTANCE_NO,
                        TIME_BOOK,
                        TIME_START,
                        TIME_END,
                        FILE_INPUT,
                        FILE_RESULT,
                        RUN_MODE,
                        DISP_SEQ,
                        ACCESS_AUTH,
                        NOTE,
                        DISUSE_FLAG,
                        LAST_UPDATE_TIMESTAMP,
                        LAST_UPDATE_USER
                    ) VALUES (
                        :JOURNAL_SEQ_NO,
                        :JOURNAL_REG_DATETIME,
                        :JOURNAL_ACTION_CLASS,
                        :EXECUTION_NO,
                        :EXECUTION_USER,
                        :SYMPHONY_NAME,
                        :STATUS_ID,
                        :SYMPHONY_INSTANCE_NO,
                        :PATTERN_ID,
                        :I_PATTERN_NAME,
                        :I_TIME_LIMIT,
                        :I_TERRAFORM_RUN_ID,
                        :I_TERRAFORM_WORKSPACE_ID,
                        :I_TERRAFORM_ORGANIZATION_WORKSPACE,
                        :OPERATION_NO_UAPK,
                        :I_OPERATION_NAME,
                        :I_OPERATION_NO_IDBH,
                        :CONDUCTOR_NAME,
                        :CONDUCTOR_INSTANCE_NO,
                        :TIME_BOOK,
                        :TIME_START,
                        :TIME_END,
                        :FILE_INPUT,
                        :FILE_RESULT,
                        :RUN_MODE,
                        :DISP_SEQ,
                        :ACCESS_AUTH,
                        :NOTE,
                        :DISUSE_FLAG,
                        :LAST_UPDATE_TIMESTAMP,
                        :LAST_UPDATE_USER
                    )";

            $bindAry = array(
                "JOURNAL_SEQ_NO"                     => $jnl_exe_no,
                "JOURNAL_REG_DATETIME"               => $date,
                "JOURNAL_ACTION_CLASS"               => "INSERT",
                "EXECUTION_NO"                       => $exe_no,
                "EXECUTION_USER"                     => $exe_user_name,
                "SYMPHONY_NAME"                      => NULL,
                "STATUS_ID"                          => $status_in_preparation,
                "SYMPHONY_INSTANCE_NO"               => NULL,
                "PATTERN_ID"                         => NULL,
                "I_PATTERN_NAME"                     => NULL,
                "I_TIME_LIMIT"                       => NULL,
                "I_TERRAFORM_RUN_ID"                 => NULL,
                "I_TERRAFORM_WORKSPACE_ID"           => $workspace_id,
                "I_TERRAFORM_ORGANIZATION_WORKSPACE" => "$organization_name:$workspace_name",
                "OPERATION_NO_UAPK"                  => NULL,
                "I_OPERATION_NAME"                   => NULL,
                "I_OPERATION_NO_IDBH"                => NULL,
                "CONDUCTOR_NAME"                     => NULL,
                "CONDUCTOR_INSTANCE_NO"              => NULL,
                "TIME_BOOK"                          => NULL,
                "TIME_START"                         => NULL,
                "TIME_END"                           => NULL,
                "FILE_INPUT"                         => NULL,
                "FILE_RESULT"                        => NULL,
                "RUN_MODE"                           => $run_mode_destroy,
                "DISP_SEQ"                           => NULL,
                "ACCESS_AUTH"                        => NULL,
                "NOTE"                               => NULL,
                "DISUSE_FLAG"                        => NULL,
                "LAST_UPDATE_TIMESTAMP"              => $date,
                "LAST_UPDATE_USER"                   => $exe_user_id
            );

            $objQueryJnl = $objDBCA->sqlPrepare($sql);
            if ($objQueryJnl->getStatus() === false) {
            $res = $objDBCA->transactionRollback();
            if ($res === false) {
                throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
            }
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }
            $res = $objQueryJnl->sqlBind($bindAry);
            $res = $objQueryJnl->sqlExecute();
            if ($res === false) {
                $res = $objDBCA->transactionRollback();
                if ($res === false) {
                    throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
                }
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }

            $updated = updateSequenceID($jnlSequenceName);

            if ($updated == false) {
                $res = $objDBCA->transactionRollback();
                if ($res === false) {
                    throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
                }
                throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
            }

            if (isset($objQuery)) unset($objQuery);
            if (isset($objQueryJnl)) unset($objQueryJnl);

            $retArray = array(
                true,
                $exe_no
            );
            $res = $objDBCA->transactionCommit();
        } catch (Exception $e) {
            // DBアクセス事後処理
            if (isset($objQuery)) unset($objQuery);
            if (isset($objQueryJnl)) unset($objQueryJnl);

            $tmpErrMsgBody = $e->getMessage();
            $retArray = array(
                false,
                $tmpErrMsgBody
            );
        }

        return $retArray;
    }


?>