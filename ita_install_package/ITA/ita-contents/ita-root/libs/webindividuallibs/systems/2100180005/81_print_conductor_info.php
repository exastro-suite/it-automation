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

function printOperationInfoForRegisterationSelect($fxVarsIntOperationNo){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    
    //----オーケストレータ別の設定記述
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        $aryRetBody = getInfoOfOneOperation($fxVarsIntOperationNo);
        if( $aryRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $aryRowOfOperationTable = $aryRetBody[4];
        
        $aryOperationSource = array(htmlspecialchars($aryRowOfOperationTable['OPERATION_NO_IDBH'])
                                    ,htmlspecialchars($aryRowOfOperationTable['OPERATION_NAME'])
                                    ,htmlspecialchars($aryRowOfOperationTable['OPERATION_DATE'])
        );
        $intOperationNo = $fxVarsIntOperationNo;
        $strStreamOfOperation = makeAjaxProxyResultStream($aryOperationSource);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intOperationNo,
                         $strStreamOfOperation,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

//個別オペレーション用の取得
function printOperationListInfoRegConductor(){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    
    //----オーケストレータ別の設定記述
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);

        $aryRetBody = $objOLA->getInfoOfOperationList();
        if( $aryRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $aryRowOfOperationTable=array();
        foreach ($aryRetBody[4] as $key => $value) {
            $aryRowOfOperationTable[]=array(
                'OPERATION_NO_IDBH' =>  htmlspecialchars($value['OPERATION_NO_IDBH'])
                ,'OPERATION_NAME'   =>  htmlspecialchars($value['OPERATION_NAME'])
            );
            
        }
        $strStreamOfOperation = json_encode($aryRowOfOperationTable,JSON_UNESCAPED_UNICODE);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfOperation,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//再描画用Conductorクラスの取得
function printConductorInfoRegConductor($fxVarsIntConductorClassId){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    
    //----オーケストレータ別の設定記述
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);


        $aryRetBody = $objOLA->convertConductorClassJson($fxVarsIntConductorClassId);
        
        if( $aryRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryJsonOfConductorClass=array();

        $strStreamOfConductor = json_encode($aryRetBody[4],JSON_UNESCAPED_UNICODE);

    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfConductor,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//CALLノード用のConductorリスト用の取得
function printConductorListInfoRegConductor(){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    
    //----オーケストレータ別の設定記述
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);

        $aryRetBody = $objOLA->getInfoOfCocductorList();
        
        if( $aryRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $aryRowOfConductorTable=array();
        foreach ($aryRetBody[4] as $key => $value) {
            $aryRowOfConductorTable[]=array(
                'CONDUCTOR_CLASS_NO' =>  htmlspecialchars($value['CONDUCTOR_CLASS_NO'])
                ,'CONDUCTOR_NAME'   =>  htmlspecialchars($value['CONDUCTOR_NAME'])

            );
            
        }
        $strStreamOfConductor = json_encode($aryRowOfConductorTable,JSON_UNESCAPED_UNICODE);

    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfConductor,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

//通知リスト用の取得
function printNoticeListInfoRegConductor($fxVarsIntConductorClassId=NULL,$mode="" ){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    
    //----オーケストレータ別の設定記述
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $aryRowOfNoticeTable=array();

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    // 処理開始
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);

        $aryRetBody = $objOLA->getInfoOfNoticeList();
        if( $aryRetBody[1]!==null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[5];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $aryRowOfNoticeTable=array();
        foreach ($aryRetBody[4] as $key => $value) {
            //アクセス権無しを除外
            if( $fxVarsIntConductorClassId === ""  ){
                if($value['NOTICE_NAME'] != $g['objMTS']->getSomeMessage("ITAWDCH-STD-11102") ){
                    $aryRowOfNoticeTable[]=array(
                        'NOTICE_ID' =>  htmlspecialchars($value['NOTICE_ID'])
                        ,'NOTICE_NAME'   =>  htmlspecialchars($value['NOTICE_NAME'])
                        #,'DEFAULT' => ""
                    );
                }
            }else{
                $aryRowOfNoticeTable[]=array(
                    'NOTICE_ID' =>  htmlspecialchars($value['NOTICE_ID'])
                    ,'NOTICE_NAME'   =>  htmlspecialchars($value['NOTICE_NAME'])
                    #,'DEFAULT' => ""
                );    
            }
        }
       
        if( $fxVarsIntConductorClassId != "" && is_numeric($fxVarsIntConductorClassId) === true  ){
            
            $tmparrayResult = printConductorInfoRegConductor( $fxVarsIntConductorClassId );
            $tmpConductorClass = json_decode($tmparrayResult[2],true);
            $tmpConductorNotice = $tmpConductorClass['conductor']['NOTICE_INFO'];

            if( $tmpConductorNotice !="" ){
                $tmpConductorNotice = array_keys($tmpConductorNotice) ;                
            }else{
                $tmpConductorNotice = array() ;                
            }

            if( $mode == "" ){
                foreach ( $aryRowOfNoticeTable as $key => $value) {
                    if($value['NOTICE_NAME'] == $g['objMTS']->getSomeMessage("ITAWDCH-STD-11102") ){
                        if( array_search( $value['NOTICE_ID'], $tmpConductorNotice ) === false ){
                            unset($aryRowOfNoticeTable[$key]);
                        }                    
                    }
                }                
            }elseif( $mode == "EXECUTE" ){

                if( count($tmpConductorNotice) == 0 ){
                    foreach ( $aryRowOfNoticeTable as $key => $value) {
                        unset($aryRowOfNoticeTable[$key]);
                    } 
                }else{
                    foreach ( $aryRowOfNoticeTable as $key => $value) {
                        if( array_search( $value['NOTICE_ID'], $tmpConductorNotice ) === false ){
                            unset($aryRowOfNoticeTable[$key]);
                        }                    
                    }                  
                }
            }
            $aryRowOfNoticeTable  = array_values($aryRowOfNoticeTable );
        }
        $strStreamOfOperation = json_encode($aryRowOfNoticeTable,JSON_UNESCAPED_UNICODE);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfOperation,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}

?>
