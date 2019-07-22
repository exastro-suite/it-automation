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

//----下位(オーケストレータ・ドライバ)相互の共通利用関数
function getInfoOfOnePattern($fxVarsIntOrchestratorId, $fxVarsIntPatternNo){
    global $g;
    
    $intControlDebugLevel01=250;
    
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $arySinglePatternSource = array();
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    $aryVariant = $g;
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    
    $strSysErrMsgBody = "";
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntPatternNo) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710102",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        //バリデーションチェック(入力形式)----
        
        //----オーケストレータ情報の収集
        require_once($aryVariant['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        
        $aryRet = $objOLA->getLivePatternFromMaster(array($fxVarsIntOrchestratorId),"");
        if( $aryRet[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryMultiLivePatternFromMaster = $aryRet[0];
        
        //----バリデーションチェック(実質評価)
        if( array_key_exists($fxVarsIntPatternNo, $aryMultiLivePatternFromMaster) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000300";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710103");
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(実質評価)----
        $arySinglePatternSource = $aryMultiLivePatternFromMaster[$fxVarsIntPatternNo];
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,
                      $intErrorType,
                      $aryErrMsgBody,
                      $strErrMsg,
                      $arySinglePatternSource,
                      $strExpectedErrMsgBodyForUI
                      );
    return $retArray;
}
//下位(オーケストレータ・ドライバ)相互の共通利用関数----

//----下位(オーケストレータ・ドライバ)および上位(シンフォニー)の共通利用関数
//function getInfoOfOneOperation($intOperationNoUAPK){
function getInfoOfOneOperation($intValueForSearchOneOpeRecord,$fxVarsIntSearchMode=0){
    global $g;
    
    $intControlDebugLevel01=250;
    
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryRowOfOperationTable = array();
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    $aryVariant = $g;
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    
    $strSysErrMsgBody = "";
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($intValueForSearchOneOpeRecord) === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            //
            if( $fxVarsIntSearchMode === 0 ){
                $intErrorType = 2;

                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710203",array($objIntNumVali->getValidRule()));
            }
            else if( $fxVarsIntSearchMode === 1 ){
                $intErrorType = 2;

                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710204",array($objIntNumVali->getValidRule()));
            }

            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        //バリデーションチェック(入力形式)----
        
        require_once($aryVariant['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        
        //----バリデーションチェック(実質評価)
        $aryRet = $objOLA->getInfoOfOneOperation($intValueForSearchOneOpeRecord,$fxVarsIntSearchMode);
        if( $aryRet[1]!==null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            $intErrorType = $aryRet[1];

            $aryErrMsgBody = $aryRet[2];
            $strErrMsg = $aryRet[3];
            if( $aryRet[1] === 101 ){
                //----１行も見つからなかった
                if( $fxVarsIntSearchMode === 0 ){
                    $intErrorType = 2;

                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710205");
                }
                else if( $fxVarsIntSearchMode === 1 ){
                    $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710206");
                }
                //１行も見つからなかった----
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(実質評価)----
        $aryRowOfOperationTable = $aryRet[4];
        
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,
                      $intErrorType,
                      $aryErrMsgBody,
                      $strErrMsg,
                      $aryRowOfOperationTable,
                      $strExpectedErrMsgBodyForUI
                      );
    return $retArray;
}
//下位(オーケストレータ・ドライバ)および上位(シンフォニー)の共通利用関数----

function checkPreserveDateTime($fxVarsStrPreserveDatetime){
    global $g;
    
    $intControlDebugLevel01=250;
    
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    
    $strSysErrMsgBody = "";
    
    try{
        $objTxtValiBDT = new TextValidator();
        if( $objTxtValiBDT->isValid($fxVarsStrPreserveDatetime) === false ){
            $strErrStepIdInFx="00000100";
            //
            //"入力値[NULLバイト文字等が含まれた値]が不正です"
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710302");
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //
        $date_check_tmp = date_parse_from_format( 'Y/m/d G:i', $fxVarsStrPreserveDatetime );

        if( $date_check_tmp['warning_count'] > 0 ||
            $date_check_tmp['error_count']   > 0 ){
            $strErrStepIdInFx="00000200";
            //
            //"予約日時が日付フォーマット(YYYY/MM/DD HH:MM)に則っていません"
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710303");
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //
        // 予約日時が過去日時でないかチェックする
        if( strtotime( $fxVarsStrPreserveDatetime ) <= strtotime(date('Y/m/d G:i')) ){
            $strErrStepIdInFx="00000300";
            //
            //"予約日時には未来の日時を指定して下さい"
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5710304");

            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($date_check_tmp);
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 2;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,
                      $intErrorType,
                      $aryErrMsgBody,
                      $strErrMsg,
                      $strExpectedErrMsgBodyForUI
                      );
    return $retArray;
}
?>
