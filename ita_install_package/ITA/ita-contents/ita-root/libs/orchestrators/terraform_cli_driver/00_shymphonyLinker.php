<?php
//   Copyright 2022 NEC Corporation
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
//    ・symphonyインスタンスの作成、更新をする機能を提供する。
//
//////////////////////////////////////////////////////////////////////

$tmpAryFx = array();
$tmpFx = function ($objOLA, $target_execution_no, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////////////
    //  Movementに反映させるべきステータスの値、と、終了日時を返す //
    /////////////////////////////////////////////////////////////////
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;

    $strStatusNumeric = null;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryTargetParameter = array();

    //----オーケストレータ別の設定記述
    $strExeCurTableIdForIU = 'C_TERRAFORM_CLI_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_TERRAFORM_CLI_EXE_INS_MNG_JNL';

    $arrayConfigForSelect = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
        "SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
        "I_TERRAFORM_WORKSPACE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "CONDUCTOR_NAME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RUN_MODE"=>"",
        "ACCESS_AUTH"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
        "SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
        "I_TERRAFORM_WORKSPACE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "CONDUCTOR_NAME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "RUN_MODE"=>"",
        "ACCESS_AUTH"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]getMovementStatusFromOrchestrator,[FILE]".__FILE__.">";

    // 処理開始
    try{
        $lc_db_model_ch = $objDBCA->getModelChannel();

        $arrayValue = $arrayValueTmpl;

        $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                , "SELECT"
                                                , "EXECUTION_NO"
                                                , $strExeCurTableIdForIU
                                                , $strExeJnlTableIdForIU
                                                , $arrayConfigForSelect
                                                , $arrayValue);
        if( $retArray[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $arrayUtnBind["EXECUTION_NO"] = $target_execution_no;

        $aryRow = array();
        $retArray = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        if( $retArray[0]===true ){
            $objQuery =& $retArray[3];
            while($row = $objQuery->resultFetch() ){
                $aryRow[] = $row;
            }
            //
            unset($objQuery);
        }
        else{
            // 例外処理へ
            $strErrStepIdInFx="00000002";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        if( count($aryRow)!= 1 ){
            // 例外処理へ
            $strErrStepIdInFx="00000003";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryRowOfOrchIns = $aryRow[0];

        $strPreTimeEnd = $aryRowOfOrchIns["TIME_END"];
        $strRetTimeEnd = "";

        switch( $aryRowOfOrchIns["STATUS_ID"] ){
            case "1": //orc「未実行」
            case "2": //orc「準備中」
            case "3": //orc「実行中」
                $strStatusNumeric = "3"; //mov「実行中」
                $strRetTimeEnd = "";
                break;
            case "4": //orc「実行中(遅延)」
                $strStatusNumeric = "4"; //mov「実行中(遅延)」
                $strRetTimeEnd = "";
                break;
            case "5": //orc「完了」
                $strStatusNumeric = "5"; //mov「実行完了」
                $strRetTimeEnd = $strPreTimeEnd;
                break;
            case "6": //orc「完了(異常)」
                $strStatusNumeric = "6"; //mov「異常終了」
                $strRetTimeEnd = $strPreTimeEnd;
                break;
            case "8": //orc「緊急停止)」
                $strStatusNumeric = "7"; //mov「緊急停止」
                $strRetTimeEnd = $strPreTimeEnd;
                break;
            case "7": //orc「想定外エラー」
            case "9": //orc「未実行(予約)」
            case "10": //orc「予約取消」
                $strStatusNumeric = "11"; //「想定外エラー」
                $strRetTimeEnd = "";
                break;
        }

        $aryTargetParameter = array("TIME_END"=>$strRetTimeEnd);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 901;

        $tmpErrMsgBody = $e->getMessage();

        $strErrMsg = $tmpErrMsgBody;
        // {},RESULT:UNEXPECTED_ERROR [RUN-PLACE[{}]]
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) $aryErrMsgBody[] = $strSysErrMsgBody;

        // DBアクセス事後処理
        if ( isset($objQueryUtn) )    unset($objQueryUtn);
    }
    $arrayResult = array($strStatusNumeric,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryTargetParameter);
    return $arrayResult;

};
$tmpAryFx['getMovementStatusFromOrchestrator'] = $tmpFx;

$tmpFx = function ($objOLA, $intPatternId, $intOperationNoUAPK, $strPreserveDatetime, $boolTrzAlreadyStarted, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////
    //  作業№を登録する                                   //
    /////////////////////////////////////////////////////////
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;

    $strExecutionNo = "";
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strRegisterDate = "";
    $strExpectedErrMsgBodyForUI = "";

    //----オーケストレータ別の設定記述
    $strExeCurTableIdForIU = 'C_TERRAFORM_CLI_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_TERRAFORM_CLI_EXE_INS_MNG_JNL';

    $strExeCurSeqName = 'C_TERRAFORM_CLI_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_TERRAFORM_CLI_EXE_INS_MNG_JSQ';

    $intOrchestratorId = 11;
    //オーケストレータ別の設定記述----

    // 各種ローカル変数を定義

    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";

    // global変数からshymphonから実行された場合のSYMPHONYインスタンス情報を取得する
    global $g;
    $int_Symphony_instance_no = '';
    // SYMPHONYインスタンスNo
    if(isset($g['__SYMPHONY_INSTANCE_NO__'])) {
        $int_Symphony_instance_no = $g['__SYMPHONY_INSTANCE_NO__'];
    }

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]registerExecuteNo,[FILE]".__FILE__.">";

    // 処理開始
    try{
        $user_name = '';
        $symphony_name = '';
        $conductor_name = "";
        $conductor_instance_no = "";
        list($strTmpRunMode,$boolKeyExists) = isSetInArrayNestThenAssign($aryProperParameter,array('RUN_MODE'),"");
        if( $boolKeyExists === false ){
            //----シンフォニーから呼ばれる場合を想定
            $strRunMode = 1;
            // 実行ユーザ名情報を取得する
            if(isset($g['__SYMPHONY_INSTANCE_NO__'])) {
                // SQL作成
                $sql = "SELECT EXECUTION_USER FROM C_SYMPHONY_INSTANCE_MNG WHERE SYMPHONY_INSTANCE_NO = $int_Symphony_instance_no";
                // SQL準備
                $objQuery = $objDBCA->sqlPrepare($sql);
                if( $objQuery->getStatus()===false ){
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // SQL発行
                $r = $objQuery->sqlExecute();

                if (!$r){
                    unset($objQuery);
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    $user_name = $row['EXECUTION_USER'];
                }
                // DBアクセス事後処理
                unset($objQuery);
            }
            // シンフォニークラス名情報を取得する
            if(isset($g['__SYMPHONY_INSTANCE_NO__'])) {
                // SQL作成
                $sql = "SELECT I_SYMPHONY_NAME FROM C_SYMPHONY_INSTANCE_MNG WHERE SYMPHONY_INSTANCE_NO = $int_Symphony_instance_no";
                // SQL準備
                $objQuery = $objDBCA->sqlPrepare($sql);
                if( $objQuery->getStatus()===false ){
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // SQL発行
                $r = $objQuery->sqlExecute();
                if (!$r){
                    unset($objQuery);
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    $symphony_name = $row['I_SYMPHONY_NAME'];
                }
                // DBアクセス事後処理
                unset($objQuery);
            }
            //シンフォニーから呼ばれる場合を想定----

            //----conductorから呼ばれる場合を想定
            // CONDUCTOR_NAMEと実行ユーザ名情報を取得する
            if(isset($g['__CONDUCTOR_INSTANCE_NO__'])) {
                $conductor_instance_no = $g['__CONDUCTOR_INSTANCE_NO__'];
                // SQL作成
                $sql = "SELECT I_CONDUCTOR_NAME,EXECUTION_USER FROM C_CONDUCTOR_INSTANCE_MNG WHERE CONDUCTOR_INSTANCE_NO = $conductor_instance_no";
                // SQL準備
                $objQuery = $objDBCA->sqlPrepare($sql);
                if( $objQuery->getStatus()===false ){
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // SQL発行
                $r = $objQuery->sqlExecute();

                if (!$r){
                    unset($objQuery);
                    $strErrStepIdInFx="00000001";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    $user_name = $row['EXECUTION_USER'];
                    $conductor_name = $row['I_CONDUCTOR_NAME'];
                }
                // DBアクセス事後処理
                unset($objQuery);
            }
            //conductorから呼ばれる場合を想定-----

        }
        else{
            // RBAC対応 ----
            // オペレーションとMovementのアクセス許可ロールをANDし作業イスタンスに設定するアクセス許可ロールを求める。
            $restAPI=false;
            $login_id=0;
            $retAry = chkMovementAccessAuth($intOperationNoUAPK,$intPatternId,$objDBCA,$objMTS,$restAPI,$login_id);
            if($retAry['STATUS'] != 'OK') {
                $strErrStepIdInFx="00000008";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ':' . $retAry['ERROR_MSG'] . ')' );
            }
            // 作業インスタンスに設定するアクセス許可ロールを退避
            $g['__TOP_ACCESS_AUTH__'] = $retAry['ACCESS_AUTH'];
            // ---- RBAC対応


            //----各オーケストレータ個別で呼ばれる場合を想定
            if( $strTmpRunMode == '1' || $strTmpRunMode == '2' ){
                // 1:通常実行/2:ドライラン
                $strRunMode = $strTmpRunMode;
            }
            else{
                // 例外処理へ
                $strErrStepIdInFx="00000001";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // 実行ユーザ名情報を取得する
            if(isset($g['login_name_jp'])) {
                $user_name = $g['login_name_jp'];
            }
            //各オーケストレータ個別で呼ばれる場合を想定----
        }
        unset($boolKeyExists);

        $lc_db_model_ch = $objDBCA->getModelChannel();
        if( $boolTrzAlreadyStarted!==true ){
            // トランザクション開始
            $varTrzStart = $objDBCA->transactionStart();
            if( $varTrzStart === false ){
                // 例外処理へ
                $strErrStepIdInFx="00000002";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // シーケンスをロックする
            $retArray = $objOLA->sequencesLockInTrz($intOrchestratorId, $aryProperParameter);
            if( $retArray[1] != 0 ){
                $intErrorType = $retArray[1];
                throw new Exception( $retArray[3] );
            }
        }
        else
        {
            // シーケンスをロックする
            $retArray = $objOLA->sequencesLockInTrz($intOrchestratorId, $aryProperParameter);
            if( $retArray[1] != 0 ){
                $intErrorType = $retArray[1];
                throw new Exception( $retArray[3] );
            }
        }
        // 作業№を取得する
        $retArray = getSequenceValueFromTable($strExeCurSeqName, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // 例外処理へ
            $strErrStepIdInFx="00000003";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $p_execution_utn_no = $retArray[0];
        $strExecutionNo     = $p_execution_utn_no;

        // Jnl№を取得する
        $retArray = getSequenceValueFromTable($strExeJnlSeqName , 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // 例外処理へ
            $strErrStepIdInFx="00000004";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $p_execution_jnl_no = $retArray[0];

        $retArray = $objOLA->getLivePatternFromMaster(array($intOrchestratorId),"",array($intPatternId));
        if($retArray[1] !== null ){
            // 例外処理へ
            $strErrStepIdInFx="00000005";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryMultiLivePatternFromMaster = $retArray[0];
        if( array_key_exists($intPatternId, $aryMultiLivePatternFromMaster)===false ){
            // 例外処理へ
            $strErrStepIdInFx="00000006";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $arySinglePatternSource = $aryMultiLivePatternFromMaster[$intPatternId];
        $arrayRetBody = $objOLA->getInfoOfOneOperation($intOperationNoUAPK);
        if( $arrayRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00000007";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryRowOfOperationTable = $arrayRetBody[4];

        //Workspace名を取得する
        $workspace_id = $arySinglePatternSource["TERRAFORM_CLI_WORKSPACE_ID"];
        // SQL作成
        $sql = "SELECT WORKSPACE_NAME FROM B_TERRAFORM_CLI_WORKSPACES WHERE WORKSPACE_ID = $workspace_id AND DISUSE_FLAG = '0'";
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        // SQL発行
        $r = $objQuery->sqlExecute();

        if (!$r){
            unset($objQuery);
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // レコードFETCH
        $workspace_name = "";
        $aryRow = array();
        while ( $row = $objQuery->resultFetch() ){
            $aryRow[] = $row;
            $workspace_name = $row['WORKSPACE_NAME'];
        }

        if( count($aryRow)!= 1 ){
            // 例外処理へ
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // DBアクセス事後処理
        unset($objQuery);

        // 実行インスタンス管理テーブルにレコードをINSERT
        $arrayConfig = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
        "SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
        "I_TERRAFORM_WORKSPACE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "CONDUCTOR_NAME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RUN_MODE"=>"",
        "ACCESS_AUTH"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
        );

        if( empty($strPreserveDatetime) ){
            // ステータスを「未実行」にする
            $status_id_for_update = 1;
        }
        else{
            // ステータスを「未実行(予約)」にする
            $status_id_for_update = 9;
        }

        list($update_user_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('vars','fx','registerExecuteNo','update_user_id'),null);
        if( $boolKeyExists===false ){
            list($update_user_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('login_id'),null);
        }
        if( strlen($update_user_id)==0 ){
            // 例外処理へ
            $strErrStepIdInFx="00000008";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        //Symphony/Conductorから実行する場合はアクセス権を継承する
        $strAccessAuth = "";
        if(array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true){
            $strAccessAuth = $g['__TOP_ACCESS_AUTH__'];
        }else{
            //Movement単体実行の場合は、Movementのアクセス権を継承する
            $strAccessAuth = $arySinglePatternSource["ACCESS_AUTH"];
        }

        $arrayValue = array(
        "JOURNAL_SEQ_NO"=>$p_execution_jnl_no,
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>$p_execution_utn_no,
        "EXECUTION_USER"=>$user_name,
        "SYMPHONY_NAME"=>$symphony_name,
        "STATUS_ID"=>$status_id_for_update,
        "SYMPHONY_INSTANCE_NO"=>$int_Symphony_instance_no,
        "PATTERN_ID"=>$intPatternId,
        "I_PATTERN_NAME"=>$arySinglePatternSource["PATTERN_NAME"],
        "I_TIME_LIMIT"=>$arySinglePatternSource["TIME_LIMIT"],
        "I_TERRAFORM_WORKSPACE_ID"=>$workspace_id,
        "I_TERRAFORM_WORKSPACE"=>$workspace_name,
        "OPERATION_NO_UAPK"=>$intOperationNoUAPK,
        "I_OPERATION_NAME"=>$aryRowOfOperationTable["OPERATION_NAME"],
        "I_OPERATION_NO_IDBH"=>$aryRowOfOperationTable["OPERATION_NO_IDBH"],
        "CONDUCTOR_NAME"=>$conductor_name,
        "CONDUCTOR_INSTANCE_NO"=>$conductor_instance_no,
        "TIME_BOOK"=>$strPreserveDatetime,
        "TIME_START"=>"",
        "TIME_END"=>"",
        "RUN_MODE"=>$strRunMode,
        "ACCESS_AUTH"=>$strAccessAuth,
        "DISUSE_FLAG"=>"0",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>$update_user_id
        );

        $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                , "INSERT"
                                                , "EXECUTION_NO"
                                                , $strExeCurTableIdForIU
                                                , $strExeJnlTableIdForIU
                                                , $arrayConfig
                                                , $arrayValue);
        if( $retArray[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00000009";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
        if( $retArray01[0]!==true || $retArray02[0]!==true ){
            $strErrStepIdInFx="00000010";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        unset($retArray01);
        unset($retArray02);

        $strRegisterDate = $arrayUtnBind['LAST_UPDATE_TIMESTAMP'];

        if( $boolTrzAlreadyStarted!==true ){
            // コミット
            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 例外処理へ
                $strErrStepIdInFx="00000011";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // トランザクション終了
            $objDBCA->transactionExit();
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 901;

        $tmpErrMsgBody = $e->getMessage();

        $strErrMsg = $tmpErrMsgBody;

        // {},RESULT:UNEXPECTED_ERROR [RUN-PLACE[{}]]
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) $aryErrMsgBody[] = $strSysErrMsgBody;

        // DBアクセス事後処理
        if ( isset($retArray01) )    unset($retArray01);
        if ( isset($retArray02) )    unset($retArray02);
    }
    $arrayResult = array($strExecutionNo,$intErrorType,$aryErrMsgBody,$strErrMsg,$strRegisterDate,$strExpectedErrMsgBodyForUI);
    return $arrayResult;
};
$tmpAryFx['registerExecuteNo'] = $tmpFx;

$tmpFx = function ($objOLA, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////
    // シーケンスをロックする                              //
    /////////////////////////////////////////////////////////
    $boolResult = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    // 各種ローカル定数を定義

    $intControlDebugLevel01 = 250;

    //----オーケストレータ別の設定記述
    $strExeCurSeqName = 'C_TERRAFORM_CLI_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_TERRAFORM_CLI_EXE_INS_MNG_JSQ';
    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]sequencesLockInTrz,[FILE]".__FILE__.">";

    try{
        //ジャーナルのシーケンス
        $retArray = getSequenceLockInTrz($strExeJnlSeqName,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // 例外処理へ
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        //作業№のシーケンス
        $retArray = getSequenceLockInTrz($strExeCurSeqName,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // 例外処理へ
            $strErrStepIdInFx="00000002";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $boolResult = true;
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 901;

        $tmpErrMsgBody = $e->getMessage();

        $strErrMsg = $tmpErrMsgBody;
    }
    $arrayResult = array($boolResult,$intErrorType,$aryErrMsgBody,$strErrMsg);
    return $arrayResult;
};
$tmpAryFx['sequencesLockInTrz'] = $tmpFx;

$tmpFx = function ($objOLA, $target_execution_no, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////
    // monitorページへのURLを返す                          //
    /////////////////////////////////////////////////////////
    $strUrlBody = '';
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    // 各種ローカル定数を定義

    $intControlDebugLevel01 = 250;

    //----オーケストレータ別の設定記述
    $strMonitorDir = '2100200010';
    $strExecutionNoKey = 'execution_no';
    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    // 各種ローカル変数を定義

    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";

    $strFxName = "<noname:[GROUP]getJumpMonitorUrl,[FILE]".__FILE__.">";

    try{
        list($lc_scheme_n_authority,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('scheme_n_authority'),null);
        if( $boolKeyExists===false ){
            // 例外処理へ
            $strErrStepIdInFx="00000001";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $strUrlBody = "{$lc_scheme_n_authority}/default/menu/01_browse.php?no={$strMonitorDir}&{$strExecutionNoKey}={$target_execution_no}";
    }
    catch (Exception $e){
        if( $intErrorType===null ) $intErrorType = 901;

        $tmpErrMsgBody = $e->getMessage();

        $strErrMsg = $tmpErrMsgBody;

        // {},RESULT:UNEXPECTED_ERROR [RUN-PLACE[{}]]
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) $aryErrMsgBody[] = $strSysErrMsgBody;//web_log($strSysErrMsgBody);
    }
    // エラーと警告以外のメッセージ系
    $retArray = array($strUrlBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
    return $retArray;
};
$tmpAryFx['getJumpMonitorUrl'] = $tmpFx;

$tmpFx = function ($objOLA, $target_execution_no, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////
    // 緊急停止を実行                                      //
    /////////////////////////////////////////////////////////
    $intResultDetail = null;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryRetMsgBody = array();
    $strInfoBody = "";
    $strOutputMsgBody = "";
    $strWarningInfo = "";

    // 各種ローカル定数を定義

    $intControlDebugLevel01 = 250;

    //----オーケストレータ別の設定記述
    $strExeCurTableIdForSelect   = 'E_TERRAFORM_CLI_EXE_INS_MNG'; //作業管理テーブル
    // $strIfTableIdForSelect       = 'D_TERRAFORM_CLI_IF_INFO'; //インタフェース情報テーブル
    // $terraform_restapi_php  = '/libs/commonlibs/common_terraform_restapi.php'; //RESTAPI関数
    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]srcamExecute,[FILE]".__FILE__.">";

    $error_info = "";
    $temp_array = "";

    global $root_dir_path;
    global $g;
    global $log_level;

    require_once ($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
    $log_output_php = '/libs/backyardlibs/backyard_log_output.php';

    //----------------------------------------------
    // 作業インスタンス情報 configのSQl生成
    //----------------------------------------------
    $arrayConfig_terraform = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
        "SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "CONDUCTOR_NAME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "FILE_INPUT"=>"",
        "FILE_RESULT"=>"",
        "RUN_MODE"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    if( $objDBCA->getTransactionMode()===false ){
        ////////////////////////////////////////////////////////////////
        // トランザクション開始
        ////////////////////////////////////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00000800")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // トランザクション開始(ステータスを変更する処理)(作業No.:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-200010", $tgt_execution_no);
            require ($root_dir_path . $log_output_php );
        }
    }


    // 処理開始
    try{
        ////////////////////////////////////////////////////////////////
        // E_TERRAFORM_CLI_EXE_INS_MNG 対象レコードをSELECT                                       //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT * "
              ."FROM    {$strExeCurTableIdForSelect} "
              ."WHERE   DISUSE_FLAG = '0' "
              ."AND     EXECUTION_NO = :EXECUTION_NO_BV "
              ."FOR UPDATE ";

        $tmpAryBind = array( 'EXECUTION_NO_BV'=>$target_execution_no );
        $retArray = singleSQLCoreExecute($objDBCA, $sql, $tmpAryBind, $strFxName);
        if( $retArray[0] === true ){
            $intTmpRowCount=0;
            $showTgtRow = array();
            $objQuery =& $retArray[3];

            while($row = $objQuery->resultFetch() ){
                if($row !== false){
                    $intTmpRowCount+=1;
                }
                //
                if($intTmpRowCount==1){
                    $showTgtRow = $row;
                }
            }
            $selectRowLength = $intTmpRowCount;
            //
            if( $selectRowLength != 1 ){
                $strWarningInfo =  $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-200010");
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //
            unset($objQuery);
        }
        else{
            throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $status_id          = $showTgtRow['STATUS_ID'];
        $status_name        = $showTgtRow['STATUS_NAME'];
        $pattern_id         = $showTgtRow['PATTERN_ID'];
        $operation_no_uapk  = $showTgtRow['OPERATION_NO_UAPK'];
        $workspace_id       = $showTgtRow['I_TERRAFORM_WORKSPACE_ID'];

        ////////////////////////////////////////////////////////////////
        // ステータスIDによって処理を分岐                             //
        ////////////////////////////////////////////////////////////////
        // ステータスIDが未実行(1)の場合
        if( $status_id == 1 ){
            $login_id = $g['login_id'];
            $vg_exe_ins_msg_table_name       = 'C_TERRAFORM_CLI_EXE_INS_MNG';
            $vg_exe_ins_msg_table_jnl_name   = 'C_TERRAFORM_CLI_EXE_INS_MNG_JNL';
            $vg_exe_ins_msg_table_jnl_seq    = 'C_TERRAFORM_CLI_EXE_INS_MNG_JSQ';
            $db_model_ch = $objDBCA->getModelChannel();

            $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$login_id);
            $tgt_execution_row = $showTgtRow;
            $tgt_execution_no = $tgt_execution_row['EXECUTION_NO'];

            //----------------------------------------------
            // シーケンスをロック
            //----------------------------------------------
            $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00000900")) );
            }

            //----------------------------------------------
            // 「C_TERRAFORM_CLI_EXE_INS_MNG」の処理対象レコードのステータスをUPDATE
            //----------------------------------------------
            // 履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001000")) );
            }

            ////////////////////////////////////////////////////////////////
            // 処理対象の作業インスタンスのステータスを緊急停止に設定
            ////////////////////////////////////////////////////////////////
            $tgt_execution_row['JOURNAL_SEQ_NO']   = $retArray[0];
            $tgt_execution_row["STATUS_ID"]        = 8;
            $tgt_execution_row["LAST_UPDATE_USER"] = $login_id;

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                    "UPDATE",
                                                    "EXECUTION_NO",
                                                    $vg_exe_ins_msg_table_name,
                                                    $vg_exe_ins_msg_table_jnl_name,
                                                    $arrayConfig_terraform,
                                                    $tgt_execution_row,
                                                    $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001100")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001200")) );
            }

            //SQL実行
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001300")) );
            }

            //SQL実行(JNL)
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001400")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]UPDATE実行(作業No.:{$tgt_row["EXECUTION_NO"]})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-200030",$tgt_row["EXECUTION_NO"]);
                require ($root_dir_path . $log_output_php );
            }

            if( $objDBCA->getTransactionMode() ){
                //----------------------------------------------
                // コミット(レコードロックを解除)
                //----------------------------------------------
                $r = $objDBCA->transactionCommit();
                if (!$r){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00001500")) );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // コミット(ステータスを変更する処理)
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-200040");
                    require ($root_dir_path . $log_output_php );
                }

                //----------------------------------------------
                // トランザクション終了
                //----------------------------------------------
                $objDBCA->transactionExit();

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // トランザクション終了(ステータスを変更する処理)(作業No.:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-200020", $tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }

            // 正常向けの結果メッセージを作成
            $strOutputMsgBody = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-201020",$target_execution_no);
            // エラーと警告以外のメッセージ系
            $aryRetMsgBody = array($strOutputMsgBody,$strInfoBody,$strWarningInfo);

            $retArray = array(0,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRetMsgBody);

            return $retArray;

        }
        // ステータスIDが実行中(3) or 実行中(遅延)(4) or 準備中(2)以外の場合
        else if( $status_id != 3 && $status_id != 4 && $status_id != 2){
            $intResultDetail = 21;
            $intErrorType = 701;
            // 処理中の対象作業のステータスは緊急停止の実施対象外です。({$status_name})
            $status_name = htmlspecialchars($status_name);
            $strWarningInfo = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-200020",$status_name);

            throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        if( $objDBCA->getTransactionMode() ){
            //----------------------------------------------
            // コミット(レコードロックを解除)
            //----------------------------------------------
            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00005000")) );
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-201040");
                require ($root_dir_path . $log_output_php );
            }

            //----------------------------------------------
            // トランザクション終了
            //----------------------------------------------
            $objDBCA->transactionExit();
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-201050");
                require ($root_dir_path . $log_output_php );
            }
        }

        // Terraform-CLI作業実行ディレクトリに、緊急停止フラグ用ファイルを格納する。
        $tgt_working_dir = $root_dir_path . '/terraform_cli_work';
        $tgt_workspace_id_dir = sprintf("%010s", $workspace_id);  // workspace_id 10桁
        $emergency_stop_file = "emergency_stop";  // 緊急停止フラグファイル
        $tgt_full_file_path = $tgt_working_dir . '/' . $tgt_workspace_id_dir . '/work/' . $emergency_stop_file;
        $touch_cmd = sprintf('/bin/touch %s ', $tgt_full_file_path);
        system($touch_cmd);

        // 緊急停止フラグ用ファイルの存在チェック
        if (!file_exists($tgt_full_file_path)) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            $strOutputMsgBody = $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-200030");
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORMCLI-ERR-101010",array(__FILE__,__LINE__,"00005000")) );
        }

        // 緊急停止しました。ステータスをご確認下さい。(作業No.:{$target_execution_no})
        $strOutputMsgBody = $objMTS->getSomeMessage("ITATERRAFORMCLI-STD-201020",$target_execution_no);
    }
    catch (Exception $e){
        $tmpErrMsgBody = $e->getMessage();

        if( $intErrorType===null ) $intErrorType = 901;

        if( 0 < strlen($error_info) ) $strErrMsg = $error_info;

        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);

    }
    // エラーと警告以外のメッセージ系
    $aryRetMsgBody = array($strOutputMsgBody,$strInfoBody,$strWarningInfo);

    $retArray = array($intResultDetail,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRetMsgBody);
    return $retArray;
};
$tmpAryFx['srcamExecute'] = $tmpFx;

$tmpFx = function ($objOLA, $strSearchKeyValue="", $boolBinaryDistinctOnDTiS=false, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////////
    // 作業パターン情報を取得                                       //
    /////////////////////////////////////////////////////////////
    $aryRow = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strErrorBuf = "";

    // 各種ローカル定数を定義

    $intControlDebugLevel01 = 250;

    $strPatternMasterTableId   = 'C_PATTERN_PER_ORCH';
    $strPatternMasterKeyColId  = 'PATTERN_ID';
    $strPatternMasterDispColId = 'PATTERN_NAME';

    //----オーケストレータ別の設定記述
    $strOrchNo = '11';  // Terraform-CLI
    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]getLivePatternList,[FILE]".__FILE__.">";

    try{
        $lc_db_model_ch = $objDBCA->getModelChannel();

        // SQL作成
        $tmpAryBind = array();
        $aryWhereZone = array();
        $aryWhereZone[] = "ITA_EXT_STM_ID IN ({$strOrchNo}) ";
        //
        if( 0 < strlen($strSearchKeyValue) ){
            $aryWhereZone[] = "DISUSE_FLAG = '0' ";
            $strWFFCMInDBHead = "";
            $strWFFCMInDBTail = "";
            $strWFFCMInNeedTipHead = "";
            $strWFFCMInNeedTipTail = "";
            $strCollate="";
            if( $lc_db_model_ch==0 ){
                if( $boolBinaryDistinctOnDTiS === false ){
                    $strWFFCMInDBHead = "TO_VALUE_FOR_FAZZY_MATCH(";
                    $strWFFCMInDBTail = ")";
                    $strWFFCMInNeedTipHead = "";
                    $strWFFCMInNeedTipTail = "";
                }
            }else if( $lc_db_model_ch==1 ){
                //----mySQL/maria
                if( $boolBinaryDistinctOnDTiS === false ){
                    $strCollate = "COLLATE utf8_unicode_ci ";
                }
                //mySQL/maria----
            }
            $tmpStr01  = "{$strWFFCMInDBHead}{$strPatternMasterDispColId}{$strWFFCMInDBTail} ";
            $tmpStr01 .= " {$strCollate}LIKE {$strWFFCMInNeedTipHead}:SEARCH_BY_LIKE_1{$strWFFCMInNeedTipTail} ESCAPE '#' ";
            $aryWhereZone[] = $tmpStr01;
            $strBindValue = '%'.where_queryForLike_Wrapper($strSearchKeyValue, $boolBinaryDistinctOnDTiS).'%';
            $tmpAryBind['SEARCH_BY_LIKE_1'] = $strBindValue;
        }
        else{
            $aryWhereZone[] = "DISUSE_FLAG = '0' ";
        }
        $strWhereZone = implode(" AND ",$aryWhereZone);

        $sql = "SELECT {$strPatternMasterKeyColId} PATTERN_ID, "
              ."{$strPatternMasterDispColId} PATTERN_NAME "
              ."FROM   {$strPatternMasterTableId} "
              ."WHERE  {$strWhereZone} "
              ."ORDER  BY PATTERN_ID ASC";

        $retArray = singleSQLCoreExecute($objDBCA, $sql, $tmpAryBind, $strFxName);
        if( $retArray[0]===true ){
            $objQuery =& $retArray[3];
            while($row = $objQuery->resultFetch() ){
                $aryRow[] = $row;
            }
            //
            unset($objQuery);
        }
        else{
            throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
    }
    catch (Exception $e){
        if( $intErrorType===null ) $intErrorType = 901;
    }
    $retArray = array($aryRow,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

    return $retArray;
};
$tmpAryFx['getLivePatternList'] = $tmpFx;

$tmpFx = function ($objOLA, $aryProperParameter=array()){
    /////////////////////////////////////////////////////////////
    // テーマカラーを定義                                           //
    /////////////////////////////////////////////////////////////
    $strColorName = '';
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strErrorBuf = "";

    $strFxName = "<noname:[GROUP]getThemeColorName,[FILE]".__FILE__.">";

    //----オーケストレータ別の設定記述
    $strColorName = 'terraformblue';
    //オーケストレータ別の設定記述----

    $retArray = array($strColorName,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    return $retArray;
};
$tmpAryFx['getThemeColorName'] = $tmpFx;

?>
