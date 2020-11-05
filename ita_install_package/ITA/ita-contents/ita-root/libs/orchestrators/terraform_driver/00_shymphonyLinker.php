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
    $strExeCurTableIdForIU = 'C_TERRAFORM_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_TERRAFORM_EXE_INS_MNG_JNL';
    
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
        "I_TERRAFORM_RUN_ID"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
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
        "I_TERRAFORM_RUN_ID"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
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
    $strExeCurTableIdForIU = 'C_TERRAFORM_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_TERRAFORM_EXE_INS_MNG_JNL';

    $strExeCurSeqName = 'C_TERRAFORM_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_TERRAFORM_EXE_INS_MNG_JSQ';

    $intOrchestratorId = 10;
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
        "I_TERRAFORM_RUN_ID"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>"",
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
        "I_TERRAFORM_RUN_ID"=>"",
        "I_TERRAFORM_WORKSPACE_ID"=>$arySinglePatternSource["TERRAFORM_WORKSPACE_ID"],
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
    $strExeCurSeqName = 'C_TERRAFORM_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_TERRAFORM_EXE_INS_MNG_JSQ';
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
    $strMonitorDir = '2100080010';
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
    $strExeCurTableIdForSelect   = 'E_TERRAFORM_EXE_INS_MNG'; //作業管理テーブル
    $strIfTableIdForSelect       = 'D_TERRAFORM_IF_INFO'; //インタフェース情報テーブル
    $terraform_restapi_php  = '/libs/commonlibs/common_terraform_restapi.php'; //RESTAPI関数
    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]srcamExecute,[FILE]".__FILE__.">";

    $error_info = "";

    // 処理開始
    try{
        ////////////////////////////////////////////////////////////////
        // E_TERRAFORM_EXE_INS_MNG 対象レコードをSELECT                                       //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT  STATUS_ID "
              ."       ,STATUS_NAME "
              ."       ,PATTERN_ID "
              ."       ,OPERATION_NO_UAPK "
              ."       ,I_TERRAFORM_RUN_ID "
              ."FROM    {$strExeCurTableIdForSelect} "
              ."WHERE   DISUSE_FLAG = '0' "
              ."AND     EXECUTION_NO = :EXECUTION_NO_BV ";

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
                $strWarningInfo =  $objMTS->getSomeMessage("ITATERRAFORM-ERR-221020");
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
        $tfe_run_id         = $showTgtRow['I_TERRAFORM_RUN_ID'];

        ////////////////////////////////////////////////////////////////
        // ステータスIDによって処理を分岐                             //
        ////////////////////////////////////////////////////////////////
        // ステータスIDが実行中(3) or 実行中(遅延)(4)以外の場合
        if( $status_id != 3 && $status_id != 4 ){
            $intResultDetail = 21;
            $intErrorType = 701;
            // 処理中の対象作業のステータスは緊急停止の実施対象外です。({$status_name})
            $status_name = htmlspecialchars($status_name);
            $strWarningInfo = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221030",$status_name);

            throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        /////////////////////////////////////////////////////////////
        // インタフェース情報を取得                                //
        /////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT * "
              ."FROM   {$strIfTableIdForSelect} "
              ."WHERE  DISUSE_FLAG = '0' ";

        $tmpAryBind = array();
        $retArray = singleSQLCoreExecute($objDBCA, $sql, $tmpAryBind, $strFxName);
        if( $retArray[0] === true ){
            $intTmpRowCount=0;
            $showTgtRow = array();
            $objQuery =& $retArray[3];

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

            $row_if_info = $showTgtRow;
            unset($objQuery);
        }
        else{
            throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // インタフェース情報をローカル変数に格納
        $terraform_hostname                 = $row_if_info['TERRAFORM_HOSTNAME'];
        $terraform_token                    = ky_decrypt($row_if_info['TERRAFORM_TOKEN']);

        //----------------------------------------------
        // REST API接続function読み込み
        //----------------------------------------------
        require_once ($aryVariant['root_dir_path'] . $terraform_restapi_php );

        //----------------------------------------------
        // RUNキャンセAPIをコール
        //----------------------------------------------
        $apiResponse = cancel_run($terraform_hostname, $terraform_token, $tfe_run_id);
        $statusCode = $apiResponse['StatusCode'];

        // 結果判定
        if( $statusCode == 200 ){
            //正常終了
            $intResultDetail = 0;
        }
        else if( $statusCode == 209 ){
            //キャンセル不可
            $intResultDetail = 11;
            // $strInfoBodyにリターン情報をメモ
            // planおよびapplyはキャンセルできない状態です。(作業No.:{})
            $strInfoBody .= $objMTS->getSomeMessage("ITATERRAFORM-STD-201030",$target_execution_no);
        }else{
            //その他異常終了
            $intResultDetail = 12;
            // [Alert]HTTP_STATUS_CODE_IS_NOT_200[StatusCode]{}{}
            $strInfoBody .= $objMTS->getSomeMessage("ITATERRAFORM-ERR-221010",array($statusCode, json_encode( $apiResponse['ResponsContents'] )));
        }

        // 緊急停止しました。ステータスをご確認下さい。(作業No.:{$target_execution_no})
        $strOutputMsgBody = $objMTS->getSomeMessage("ITATERRAFORM-STD-201020",$target_execution_no);
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
    $strOrchNo = '10';  // Terraform
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
              ."ORDER  BY DISP_SEQ ASC";

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
