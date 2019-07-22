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
    function printPetternInfoForRegisterationSelect($fxVarsIntPatternNo){
        global $g;
        // 各種ローカル定数を定義
        $intControlDebugLevel01 = 250;
        
        $arrayResult = array();
        $strResultCode = "";
        $strDetailCode = "";
        $intPatternNo = "";
        $strStreamOfPattern = "";
        $strExpectedErrMsgBodyForUI = "";
        
        //----オーケストレータ別の設定記述
        $intOrchestratorId = 8;
        //オーケストレータ別の設定記述----
        
        // 各種ローカル変数を定義
        
        $intErrorType = null;
        $intDetailType = null;
        
        $strSysErrMsgBody = "";
        $strErrStepIdInFx = "";
        
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
        
        // 処理開始
        try{
            $aryRetBody = getInfoOfOnePattern($intOrchestratorId, $fxVarsIntPatternNo);
            if( $aryRetBody[1]!==null ){
                // 例外処理へ
                $strErrStepIdInFx="00000001";
                $intErrorType = $aryRetBody[1];
                if( $aryRetBody[1] < 500 ){
                    $strExpectedErrMsgBodyForUI = $aryRetBody[5];
                }
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $arySinglePatternSource = $aryRetBody[4];
            
            $aryPatternSource = array($arySinglePatternSource['PATTERN_ID']
                                        ,htmlspecialchars($arySinglePatternSource['PATTERN_NAME'])
                                        ,$arySinglePatternSource['TIME_LIMIT']
            );
            
            $strStreamOfPattern = makeAjaxProxyResultStream($aryPatternSource);
            $intPatternNo = $fxVarsIntPatternNo;
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
                             $intPatternNo,
                             $strStreamOfPattern,
                             nl2br($strExpectedErrMsgBodyForUI)
                             );
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $arrayResult;
    }
?>