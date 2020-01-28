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

//////////////////////////////////////////////////////////////////
//  Movement作業実行RestAPI [EXECUTE] (Ansible-Legacy)           //
//////////////////////////////////////////////////////////////////
function movementExecutionControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

    // グローバル変数宣言
    global $g;

    // 各種ローカル変数を定義
    $intControlDebugLevel01 = 250;

    $arrayRetBody = array();

    $intResultStatusCode = null;
    $aryForResultData = array();
    $aryPreErrorData = null;

    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    $strExpectedErrMsgBodyForUI = "";

    $strSysErrMsgBody = '';
    $intErrorPlaceMark = "";
    $strErrorPlaceFmt = "%08d";

    $aryOverrideForErrorData = array();

    $intPatternId = "";
    $intOperationNoUAPK = "";
    $strPreserveDatetime = "";
    $intRunMode = 1;
    $aryVars = array();

    $intResultInfoCode = "000";//結果コード(正常終了)

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
    require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
    require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/83_execution_no_register.php");

    try{

        if( is_array($objJSONOfReceptedData) !== true ){
            $tmpAryOrderData = array();
        }
        else{
            $tmpAryOrderData = $objJSONOfReceptedData;
        }

        //登録データのチェック
        list($intPatternId         , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('MOVEMENT_CLASS_ID') ,null);
        list($intOperationNoUAPK   , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('OPERATION_ID')      ,null);
        list($strPreserveDatetime  , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('PRESERVE_DATETIME') ,null);
        list($aryVars['RUN_MODE']  , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('RUN_MODE'),$intRunMode);
        if ( !in_array ( $aryVars['RUN_MODE'] , array(1,2) ) ) $aryVars['RUN_MODE'] = $intRunMode;
    
        //X-command毎の処理   
        switch ($strCommand) {
            case 'EXECUTE':
                break;
            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            break;
        }

        $tmpResult = executionNoRegister($intPatternId, $intOperationNoUAPK, $strPreserveDatetime, $aryVars);
        
        if ( $tmpResult[0] != "000" ){
            $intResultInfoCode = "001";
        }
        
        $intResultStatusCode = 200;

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        //EXECUTION_NOを結果へ格納 
        $aryForResultData['resultdata'] = array();
        $aryForResultData['resultdata']['EXECUTION_NO'] = $tmpResult[2];
        $aryForResultData['resultdata']['RESULTCODE'] = $intResultInfoCode;
        $aryForResultData['resultdata']['RESULTINFO'] = $tmpResult[3];

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

    //$aryForResultData['ROW'] = $tmpResult;
    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}


?>
