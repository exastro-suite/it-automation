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
    //  【特記事項】
    //      オーケストレータ別の設定記述あり
    //
    //////////////////////////////////////////////////////////////////////

function symphonyInstanceControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayRetBody = array();
    
    $intResultStatusCode = null;
    $aryForResultData = array();
    $aryPreErrorData = null;
    
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $strSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $strSysErrMsgBody = '';
    $intErrorPlaceMark = "";
    $strErrorPlaceFmt = "%08d";
    
    $intUIErrorMsgSaveIndex = -1;
    $aryOverrideForErrorData = array();
    
   // 各種ローカル変数を定義
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/72_symphonyClassAdmin.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");
        
        if( is_array($objJSONOfReceptedData) !== true ){
            $tmpAryOrderData = array();
        }
        else{
            $tmpAryOrderData = $objJSONOfReceptedData;
        }
        list($intSymphonyInstanceId       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('SYMPHONY_INSTANCE_ID') ,null);
        switch($strCommand){
            case "INFO":
                $aryRetBody = symphonyInstancePrint($intSymphonyInstanceId);
                $intUIErrorMsgSaveIndex = 4;
                break;
            case "CANCEL":
                $aryRetBody = symphonyInstanceBookCancel($intSymphonyInstanceId);
                $intUIErrorMsgSaveIndex = 4;
                break;
            case "SCRAM":
                $aryRetBody = symphonyInstanceScram($intSymphonyInstanceId);
                $intUIErrorMsgSaveIndex = 4;
                break;
            case "RELEASE":
                list($intSeqNo       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('MOVEMENT_SEQ_NO') ,null);
                $aryRetBody = movementInstanceHoldRelease($intSymphonyInstanceId, $intSeqNo);
                $intUIErrorMsgSaveIndex = 4;
                break;
            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3810101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                break;
        }
        
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $intErrorPlaceMark = 2000;
            if( $intErrorType < 500 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[$intUIErrorMsgSaveIndex];
                $intResultStatusCode = 400;
            }
            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        if( headers_sent() === true ){
            $intErrorType = 900;
            $intErrorPlaceMark = 3000;
            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intResultStatusCode = 200;
        
        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata'] = $aryRetBody[0];
    }
    catch (Exception $e){
        // 失敗時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];
        foreach($aryOverrideForErrorData as $strKey=>$varVal){
            $aryForResultData[$strKey] = $varVal;
        }
        if( 0 < strlen($strExpectedErrMsgBodyForUI) ){
            $aryPreErrorData[] = $strExpectedErrMsgBodyForUI;
        }
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        if( $intResultStatusCode === null ) $intResultStatusCode = 500;
        if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}

function printOneOfSymphonyInstances($fxVarsIntSymphonyInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyClassId = "";
    $strStreamOfMovements = "";
    $strStreamOfSymphony = "";
    $strExpectedErrMsgBodyForUI = "";
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $aryOutputItemFromSymphonySource = array(
        'SYMPHONY_INSTANCE_ID'=>""
        ,'I_SYMPHONY_NAME'=>"htmlspecialchars"
        ,'I_DESCRIPTION'=>"htmlspecialchars"
        ,'STATUS_ID'=>""
        ,'EXECUTION_USER'=>""
        ,'ABORT_EXECUTE_FLAG'=>""
        ,'OPERATION_NO_UAPK'=>""
        ,'OPERATION_NO_IDBH'=>"htmlspecialchars"
        ,'OPERATION_NAME'=>"htmlspecialchars"
        ,'TIME_BOOK'=>""
    );
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");
        
        $aryRetBody = symphonyInstancePrint($fxVarsIntSymphonyInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $arySymphonySourceOrg = $aryRetBody[0]['SYMPHONY_INSTANCE_INFO'];
        $arySymphonySourceResult = array();
        foreach($aryOutputItemFromSymphonySource as $strKey=>$strFxName){
            if( 0 < strlen($strFxName) ){
                $tmpStrValue = $strFxName($arySymphonySourceOrg[$strKey]);
            }
            else{
                $tmpStrValue = $arySymphonySourceOrg[$strKey];
            }
            $arySymphonySourceResult[$strKey] = $tmpStrValue;
        }
        unset($tmpStrValue);
        $strStreamOfSymphony = makeAjaxProxyResultStream($arySymphonySourceResult);
        
        $aryListSourceOrg = $aryRetBody[0]['MOVEMENTS'];
        $aryListSourceResult = array();
        foreach($aryListSourceOrg as $aryMovementIns){
            foreach($aryMovementIns['CLASS_ITEM'] as $strKey=>$strVal){
                $aryListSourceResult[] = htmlspecialchars($strVal);
            }
            $aryListSourceResult[] = makeAjaxProxyResultStream($aryMovementIns['INS_ITEM']);
        }
        $strStreamOfMovements = makeAjaxProxyResultStream($aryListSourceResult);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyClassId,
                         $strStreamOfMovements,
                         $strStreamOfSymphony,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

function bookCancelOneOfSymphonyInstances($fxVarsIntSymphonyInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");
        
        $aryRetBody = symphonyInstanceBookCancel($fxVarsIntSymphonyInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intSymphonyInstanceId = $aryRetBody[0]['SYMPHONY_INSTANCE_ID'];
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyInstanceId,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

function scramOneOfSymphonyInstances($fxVarsIntSymphonyInstanceId){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");
        
        $aryRetBody = symphonyInstanceScram($fxVarsIntSymphonyInstanceId);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intSymphonyInstanceId = $aryRetBody[0]['SYMPHONY_INSTANCE_ID'];
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyInstanceId,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

function holdReleaseOneOfMovementInstances($fxVarsIntSymphonyInstanceId,$fxVarsIntSeqNo){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyInstanceId = "";
    $intSeqNo = "";
    $strExpectedErrMsgBodyForUI = "";
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");
        
        $aryRetBody = movementInstanceHoldRelease($fxVarsIntSymphonyInstanceId,$fxVarsIntSeqNo);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[4];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intSymphonyInstanceId = $aryRetBody[0]['SYMPHONY_INSTANCE_ID'];
        $intSeqNo = $aryRetBody[0]['MOVEMENT_SEQ_NO'];
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyInstanceId,
                         $intSeqNo,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
?>