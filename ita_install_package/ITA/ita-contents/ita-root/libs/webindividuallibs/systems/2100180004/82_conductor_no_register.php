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
function conductorNoRegisterFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){
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
    
    $aryOverrideForErrorData = array();

    $intResultInfoCode="000";//結果コード(正常終了)

    $intOrderOvRdchkflg = "";
    $strOrderOvRdErrMsg = "";

    $tmpForOptionOrderOvRd = array();

    // 各種ローカル変数を定義
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    try{
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        
        if( is_array($objJSONOfReceptedData) !== true ){
            $tmpAryOrderData = array();
        }
        else{
            $tmpAryOrderData = $objJSONOfReceptedData;
        }
        
        switch($strCommand){
            case "EXECUTE":
                break;
            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        list($intSymphonyClassId       , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('CONDUCTOR_CLASS_NO') ,null);
        list($intOperationId           , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('OPERATION_ID')      ,null);
        list($strPreserveDatetime      , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('PRESERVE_DATETIME') ,null);
        $strOptionOrderStream = "";
        list($aryForOptionOrderOvRd    , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('OPTION')            ,array());


        //----ここから、オペIDからオペNOを取得
        $aryOpRetBody = getInfoOfOneOperation($intOperationId,1);
        //ここから、オペIDからオペNOを取得----
        
        if( $aryOpRetBody[0] == 1 ){
            $aryRowOfOperationTable = $aryOpRetBody[4];
            $intOperationNo = $aryRowOfOperationTable['OPERATION_NO_UAPK'];

            $aryRetBody = getConductorClassJson($intSymphonyClassId,1);
            if( $aryRetBody[1] !== null ){
                $intErrorType = $aryRetBody[1];
                $intErrorPlaceMark = 3000;
                if( $intErrorType == 2 || $intErrorType == 3 ){
                    $strExpectedErrMsgBodyForUI = $aryRetBody[5];
                    $intResultStatusCode = 400;
                    $intResultInfoCode="001";
                }
            }else{
                // JSON形式の変換、不要項目の削除
                $tmpReceptData = json_decode($aryRetBody[4],true);
                $strSortedData=$tmpReceptData;
                unset($strSortedData['conductor']);
                foreach ($strSortedData as $key => $value) {
                    if( preg_match('/line-/',$key) ){
                        unset($strSortedData[$key]);
                    }
                }
                unset($strSortedData['conductor']);
                unset($strSortedData['config']);
                $tmpForOptionOrderOvRd = $strSortedData;                    
            }

            //上書き対象項目（オペレーション、スキップ）
            if(  $aryForOptionOrderOvRd != array() ){
                foreach ($aryForOptionOrderOvRd as $tmpNodeId => $aryOpt ) {
                   list($intOrderOvRdOpeId         , $boolKeyExists) = isSetInArrayNestThenAssign($aryOpt ,array('OPERATION_ID') ,null);
                   list($intOrderOvRdSkipflg       , $boolKeyExists) = isSetInArrayNestThenAssign($aryOpt ,array('SKIP_FLAG') ,null);
                    //オペレーション
                    if( $intOrderOvRdOpeId !== null ){
                        //---個別指定のオペレーション取得
                        $aryOrderOpRetBody = getInfoOfOneOperation($intOrderOvRdOpeId,1);

                        if( $aryOrderOpRetBody[0] == 1 && array_key_exists($tmpNodeId, $tmpForOptionOrderOvRd) === true ){
                            $tmpForOptionOrderOvRd[$tmpNodeId]['OPERATION_NO_IDBH'] = $intOrderOvRdOpeId;
                        }else{
                            // オペレーションID不備
                            $intErrorType = $aryOrderOpRetBody[1];
                            $intErrorPlaceMark = 2000;
                            if( $intErrorType == 2 || $intErrorType == 3 ){
                                $strExpectedErrMsgBodyForUI = $aryOrderOpRetBody[5];
                                $intResultInfoCode="001";
                            } 
                            $intOrderOvRdchkflg = "1";
                            $strNodeMsg = "$tmpNodeId:".json_encode($aryOpt,JSON_UNESCAPED_UNICODE);
                            $strOrderOvRdErrMsg = $g['objMTS']->getSomeMessage("ITABASEH-ERR-170043" ,array($strNodeMsg) );
                            #"OPTION[対象ノード、項目名、値]が不正です。  ( $tmpNodeId:" .json_encode($aryOpt,JSON_UNESCAPED_UNICODE)." )";
                            break;
                        }
                    }
                    //スキップ
                    if( $intOrderOvRdSkipflg !== null ){
                        if( ( $intOrderOvRdSkipflg == 1 || $intOrderOvRdSkipflg == "" ) && array_key_exists($tmpNodeId, $tmpForOptionOrderOvRd) === true ){
                            $tmpForOptionOrderOvRd[$tmpNodeId]['SKIP_FLAG'] = $intOrderOvRdSkipflg;
                        }else{
                            $intOrderOvRdchkflg = "1";
                            $strNodeMsg = "$tmpNodeId:".json_encode($aryOpt,JSON_UNESCAPED_UNICODE);
                            $strOrderOvRdErrMsg = $g['objMTS']->getSomeMessage("ITABASEH-ERR-170043" ,array($strNodeMsg) );
                            #"OPTION[対象ノード、項目名、値]が不正です。  ( $tmpNodeId:" .json_encode($aryOpt,JSON_UNESCAPED_UNICODE)." )";
                            break;   
                        }
                    }
                }
            }

            $aryForOptionOrderOvRd = $tmpForOptionOrderOvRd;   

            if( $intOrderOvRdchkflg == "" ){
                $aryRetBody = conductorInstanceConstuct($intSymphonyClassId, $intOperationNo, $strPreserveDatetime, $strOptionOrderStream, $aryForOptionOrderOvRd);

                $strSymphonyInstanceId = (string)$aryRetBody[4];
                $intResultStatusCode = 200;
                
                //SymphonyクラスIDの不備
                if( $aryRetBody[1] !== null ){
                    $intErrorType = $aryRetBody[1];
                    $intErrorPlaceMark = 3000;
                    if( $intErrorType == 2 || $intErrorType == 3 ){
                        $strExpectedErrMsgBodyForUI = $aryRetBody[5];
                        $intResultInfoCode="001";
                    }
                }
                
                if( headers_sent() === true ){
                    $intErrorType = 900;
                    $intErrorPlaceMark = 4000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }else{
                // 上書きオプション-オペレーション,SKIP不備
                $strExpectedErrMsgBodyForUI = $strOrderOvRdErrMsg;
                $intResultInfoCode="001";
            }


        }else{
            // オペレーションID不備
            $intErrorType = $aryOpRetBody[1];
            $intErrorPlaceMark = 2000;
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryOpRetBody[5];
                $intResultInfoCode="001";
            }
        }

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata'] = array('CONDUCTOR_INSTANCE_ID'=>$strSymphonyInstanceId);
        $aryForResultData['resultdata']['RESULTCODE'] = $intResultInfoCode;
        $aryForResultData['resultdata']['RESULTINFO'] = $strExpectedErrMsgBodyForUI;

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
function conductorNoRegister($intShmphonyClassId, $intOperationNo, $strPreserveDatetime, $strOptionOrderStream){
    global $g;
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strSymphonyInstanceId = "";
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
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/71_basic_common_lib.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
        require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
        $aryRetBody = conductorInstanceConstuct($intShmphonyClassId, $intOperationNo, $strPreserveDatetime,"",$strOptionOrderStream);

        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $strErrStepIdInFx="00001000";
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $strSymphonyInstanceId = (string)$aryRetBody[4];
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
                         $strSymphonyInstanceId,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

?>
