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
    $strExeCurTableIdForIU = 'C_DSC_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_DSC_EXE_INS_MNG_JNL';
    
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
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_PARALLEL_EXE"=>"",
        "I_DSC_RETRY_TIMEOUT"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RUN_MODE"=>"",
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
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_PARALLEL_EXE"=>"",
        "I_DSC_RETRY_TIMEOUT"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "RUN_MODE"=>"",
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
    $strExeCurTableIdForIU = 'C_DSC_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_DSC_EXE_INS_MNG_JNL';

    $strExeCurSeqName = 'C_DSC_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_DSC_EXE_INS_MNG_JSQ';

    $intOrchestratorId = 8;
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
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_PARALLEL_EXE"=>"",
        "I_DSC_RETRY_TIMEOUT"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RUN_MODE"=>"",
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
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>$arySinglePatternSource["ANS_HOST_DESIGNATE_TYPE_ID"],
        "I_ANS_PARALLEL_EXE"=>$arySinglePatternSource["ANS_PARALLEL_EXE"],
        "I_DSC_RETRY_TIMEOUT"=>$arySinglePatternSource["DSC_RETRY_TIMEOUT"],
        "OPERATION_NO_UAPK"=>$intOperationNoUAPK,
        "I_OPERATION_NAME"=>$aryRowOfOperationTable["OPERATION_NAME"],
        "I_OPERATION_NO_IDBH"=>$aryRowOfOperationTable["OPERATION_NO_IDBH"],
        "TIME_BOOK"=>$strPreserveDatetime,
        "TIME_START"=>"",
        "TIME_END"=>"",
        "RUN_MODE"=>$strRunMode,
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
    $strExeCurSeqName = 'C_DSC_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_DSC_EXE_INS_MNG_JSQ';
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
    $strMonitorDir = '2100060010'; //
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

    // テーブル情報
    $strExeCurTableIdForSelect   = 'E_DSC_EXE_INS_MNG';
    $strVarTableIdForSelect      = 'D_DSC_VARS_ASSIGN';     // 緊急停止
    $strStmListTableSelect       = 'C_STM_LIST';            // 緊急停止
    $strPattnLinkTableSelect     = 'B_DSC_PATTERN_LINK';    // 緊急停止
    $strResourceMastTableSelect  = 'D_DSC_RESOURCE';        // 緊急停止

    $strIfTableIdForSelect       = 'D_DSC_IF_INFO';
    $strColIdOfDRSRPathFromWebSv = 'DSC_STORAGE_PATH_LNX';
    $strColIdOfDRSRPathFromDrvSv = 'DSC_STORAGE_PATH_DSC';

    $strColIdOfRestAPIProtocol   = 'DSC_PROTOCOL';
    $strColIdOfRestAPIHostName   = 'DSC_HOSTNAME';
    $strColIdOfRestAPIPort       = 'DSC_PORT';
    $strColIdOfRestAPIAccessKey  = 'DSC_ACCESS_KEY_ID';
    $strColIdOfRestAPISAKey      = 'DSC_SECRET_ACCESS_KEY';

    // ターゲットユーザネーム・ターゲットパスワード設定
    $strSystemKeyColId  = 'SYSTEM_ID';                      // 緊急停止
    $strPatternMasterDispColId = 'OPERATION_NO_UAPK';       // 緊急停止
    $strPHOLinkTableId   = 'B_DSC_PHO_LINK';                // 緊急停止

	// ターゲットユーザネーム・ターゲットパスワード設定
    $strIncludeLibFileName       = 'common_dsc_restapi.php';// 緊急停止
    $strCallFunctionName         = 'dsc_restapi_access';    // 緊急停止
    $strOrchestratorSubId        = "DSC_NS";                // "DSC_SV"
    $strRequestURI               = "/restapi/dsc_driver/CollectCommandStop.php";
    $strMethod                   = "DELETE";

    //オーケストレータ別の設定記述----

    $objMTS = $objOLA->getMessageTemplateStorage();
    $objDBCA = $objOLA->getDBConnectAgent();
    $aryVariant = $objOLA->getVariant();

    $strFxName = "<noname:[GROUP]srcamExecute,[FILE]".__FILE__.">";

    $error_info = "";

    // 処理開始
    try{
        ////////////////////////////////////////////////////////////////
        //  REST API接続function定義ファイル読み込み                  //
        ////////////////////////////////////////////////////////////////
        $strRestAPIFuncPath = $aryVariant['root_dir_path'] . '/libs/commonlibs/'.$strIncludeLibFileName;
        if( file_exists( $strRestAPIFuncPath ) && !is_dir( $strRestAPIFuncPath ) ){
            // REST API接続function定義
            require_once ( $strRestAPIFuncPath );
        }
        else{
            // REST API接続function定義なし
            $error_info = '[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[MESSAGE]' .  $objMTS->getSomeMessage("ITADSCH-ERR-101060");

            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        ////////////////////////////////////////////////////////////////
        // E_DSC_EXE_INS_MNG 対象レコードをSELECT                                       //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT  STATUS_ID "
              ."       ,STATUS_NAME "
              ."       ,PATTERN_ID "
              ."       ,OPERATION_NO_UAPK "
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
                $strWarningInfo =  $objMTS->getSomeMessage("ITADSCH-ERR-101070");
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
        ////////////////////////////////////////////////////////////////
        // D_DSC_VARS_ASSIGN 対象レコードをSELECT                         //
        ///////////////////////////////////////////////////////////////
        $sql = "SELECT * "
              ."FROM   {$strVarTableIdForSelect} "
              ."WHERE  DISUSE_FLAG = '0' "
              ."AND    OPERATION_NO_UAPK = :OPERATION_NO_UAPK_BV "
              ."AND    PATTERN_ID = :PATTERN_ID_BV ";

        $tmpAryBind = array();
        $tmpAryBind = array( 'OPERATION_NO_UAPK_BV'=>$operation_no_uapk ,
                             'PATTERN_ID_BV'=>$pattern_id );
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

            if( $selectRowLength == 0 ){
                throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $row_if_info = $showTgtRow;
            unset($objQuery);
        }
        else{
            throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $system_id          = $showTgtRow['SYSTEM_ID'];
        ////////////////////////////////////////////////////////////////
        // C_STM_LIST 対象レコードをSELECT                                //
        ///////////////////////////////////////////////////////////////
        $sql = "SELECT * "
              ."FROM   {$strStmListTableSelect} "
              ."WHERE  DISUSE_FLAG = '0' "
              ."AND    SYSTEM_ID = :SYSTEM_ID_BV ";

        $tmpAryBind = array( 'SYSTEM_ID_BV'=>$system_id );
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

            if( $selectRowLength != 1 ){
                throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $row_if_info = $showTgtRow;
            unset($objQuery);
        }
        else{
            throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $target_hostname    = $showTgtRow['HOSTNAME'];
        $target_ip          = $showTgtRow['IP_ADDRESS'];
        $target_username    = $showTgtRow['LOGIN_USER'];
        $target_password    = ky_decrypt( $showTgtRow['LOGIN_PW'] );
        ////////////////////////////////////////////////////////////////
        // B_DSC_PATTERN_LINK 対象レコードをSELECT                                       //
        ///////////////////////////////////////////////////////////////
        $sql = "SELECT * "
              ."FROM   {$strPattnLinkTableSelect} "
              ."WHERE  DISUSE_FLAG = '0' "
              ."AND    PATTERN_ID = :PATTERN_ID_BV ";

        $tmpAryBind = array( 'PATTERN_ID_BV'=>$pattern_id );
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
        $master_id    = $showTgtRow['RESOURCE_MATTER_ID'];
        ////////////////////////////////////////////////////////////////
        // D_DSC_RESOURCE_LINK 対象レコードをSELECT                                       //
        ///////////////////////////////////////////////////////////////
        $sql = "SELECT * "
              ."FROM   {$strResourceMastTableSelect} "
              ."WHERE  DISUSE_FLAG = '0' "
              ."AND    RESOURCE_MATTER_ID = :RESOURCE_MATTER_ID_BV ";

        $tmpAryBind = array( 'RESOURCE_MATTER_ID_BV'=>$master_id );
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

        ////////////////////////////////////////////////////////////////
        // ステータスIDによって処理を分岐                             //
        ////////////////////////////////////////////////////////////////
        // ステータスIDが実行中(3) or 実行中(遅延)(4)以外の場合
        if( $status_id != 3 && $status_id != 4 ){
            $intResultDetail = 21;
            $intErrorType = 701;
            // 処理中の対象作業のステータスは緊急停止の実施対象外です。({$status_name})
            $status_name = htmlspecialchars($status_name);
            $strWarningInfo = $objMTS->getSomeMessage("ITADSCH-ERR-101080",$status_name);

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
        $strDRSRPathFromWeb = $row_if_info[$strColIdOfDRSRPathFromWebSv];
        $strDRSRPathFromDrv = $row_if_info[$strColIdOfDRSRPathFromDrvSv];

        ////////////////////////////////////////////////////////////////
        $strProtocol          = $row_if_info['DSC_PROTOCOL'];
        $strHostname          = $row_if_info['DSC_HOSTNAME'];
        $strPort              = $row_if_info['DSC_PORT'];
        $strAccessKeyId     = $row_if_info['DSC_ACCESS_KEY_ID'];
        $strSecretAccessKey = ky_decrypt($row_if_info['DSC_SECRET_ACCESS_KEY']);
        $strRequestURI  = "/restapi/dsc_driver/CollectCommandStop.php";
        $strMethod  = "POST";
        ////////////////////////////////////////////////////////////////
        $lv_dsc_process_id  = "3";
        $lv_dsc_storage_path_lnx  = $row_if_info['DSC_STORAGE_PATH_LNX'];
        $vg_OrchestratorSubId_dir  = "ns";
        $tgt_execution_no  = $target_execution_no;
        $lv_dsc_storage_path_dsc  = $row_if_info['DSC_STORAGE_PATH_DSC'];
        $lv_dsc_target_hostname  = $target_hostname;
        $lv_dsc_target_ip  = $target_ip;
        $lv_dsc_target_username  = $target_username;
        $lv_dsc_target_password  = $target_password;
        ////////////////////////////////////////////////////////////////
        // ワークフロー実行キャンセルのREST APIをコール               //
        ////////////////////////////////////////////////////////////////
        // REST API向けのリクエストURLを準備
        $aryRequestContents
                = array(
                        // DSC処理コード
                        'DSC_PROCESS_ID'=>$lv_dsc_process_id,
                        // データリレイパス
                        'DATA_RELAY_STORAGE_TRUNK'=>$lv_dsc_storage_path_lnx,
                        //オーケストレータ識別子
                        "ORCHESTRATOR_SUB_ID"=>$vg_OrchestratorSubId_dir,
                        //作業実行ID
                        "EXE_NO"=>$tgt_execution_no,
                        //データストレージパス
                        "DSC_DATA_RELAY_STORAGE"=>$lv_dsc_storage_path_dsc,
                        );

        $aryRestAPIResponse = $strCallFunctionName( $strProtocol
                                                   ,$strHostname
                                                   ,$strPort
                                                   ,$strAccessKeyId
                                                   ,$strSecretAccessKey
                                                   ,$strRequestURI
                                                   ,$strMethod
                                                   ,$aryRequestContents );


        // 結果判定
        if( $aryRestAPIResponse['StatusCode'] != 200 ){
            $intResultDetail = 11;
            // [Alert]HTTP_STATUS_CODE_IS_NOT_200[StatusCode]{}{}
            $strInfoBody .= $objMTS->getSomeMessage("ITADSCH-ERR-101090",array($aryRestAPIResponse['StatusCode'], json_encode( $aryRestAPIResponse['ResponsContents'] )));
        }
        else if( $aryRestAPIResponse['ResponsContents'] == null ){
            $intResultDetail = 12;
            // [Alert]HTTP_STATUS_CODE_IS_NOT_200[StatusCode]
            $strInfoBody .= $objMTS->getSomeMessage("ITADSCH-ERR-102010");
        }
        else{
            $intResultDetail = 0;
        }

        // 緊急停止しました。ステータスをご確認下さい。(作業No.:{$target_execution_no})
        $strOutputMsgBody = $objMTS->getSomeMessage("ITADSCH-STD-101020",$target_execution_no);
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
    $strOrchNo = '8';  // DSC
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
    $strColorName = 'DSCblue';  //
    //オーケストレータ別の設定記述----

    $retArray = array($strColorName,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    return $retArray;
};
$tmpAryFx['getThemeColorName'] = $tmpFx;
?>
