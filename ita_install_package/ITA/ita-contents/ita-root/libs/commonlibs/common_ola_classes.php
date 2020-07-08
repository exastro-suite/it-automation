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
//  【処理概要】
//    ・webおよびbackyard共通で呼び出される。
//
//////////////////////////////////////////////////////////////////////

//----ここから個別オーケストレータ/シンフォニー用クラス定義
class OrchestratorLinkAgent {
    protected $aryRPathFromAppRootPerOrcId;
    protected $aryFxPerOrchestrator;
    
    protected $objMTS;
    protected $objDBCA;
    protected $objAryVariant;
    
    function __construct($objMTS=null,$objDBCA=null,$aryVariant=array()){
        //----変数を初期化
        $this->aryRPathFromAppRootPerOrcId = array();
        $this->aryFxPerOrchestrator = array();
        
        $this->objMTS = $objMTS;
        $this->objDBCA = $objDBCA;
        
        $this->objAryVariant = $aryVariant;
        //変数を初期化----
    }
    
    function getDBConnectAgent(){
        return $this->objDBCA;
    }
    
    function getMessageTemplateStorage(){
        return $this->objMTS;
    }

    function getVariant(){
        return $this->objAryVariant;
    }
    
    function addFuncionsPerOrchestrator($varOrchestratorId,$strRPathFromOrcLibRoot,$strFileName="00_shymphonyLinker.php"){
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_string($strRPathFromOrcLibRoot)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_string($strFileName)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strRequireFileFullname = getApplicationRootDirPath()."/libs/orchestrators/{$strRPathFromOrcLibRoot}/{$strFileName}";
            $boolRequire = require($strRequireFileFullname);
            if( $boolRequire===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $this->aryFxPerOrchestrator["ORC_".$intOrchestratorId] = $tmpAryFx;
            $this->aryRPathFromAppRootPerOrcId[$strRPathFromOrcLibRoot] = $varOrchestratorId;
            $boolRet = true;
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
        //自動的に、オーケストレータテーブルから、調べる？----
    }
    
    //----（下位）オーケストレータのステータスを、ムーブメントに反映させる
    function getMovementStatusFromOrchestrator($varOrchestratorId, $target_execution_no, $aryProperParameter=array()){
        //----RETSET[2016-03-11]
        $strStatusNumeric = null;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryTargetParameter = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"getMovementStatusFromOrchestrator"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this, $target_execution_no, $aryProperParameter);
            $strStatusNumeric = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrMsg = $aryRetBody[3];
            $aryTargetParameter = $aryRetBody[4];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($strStatusNumeric,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryTargetParameter);
        return $retArray;
    }
    //（下位）オーケストレータのステータスを、ムーブメントに反映させる----
    
    //----（下位）オーケストレータの、作業インスタンステーブルのシーケンスをロック
    function sequencesLockInTrz($varOrchestratorId, $aryProperParameter=array()){
        $boolResult = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"sequencesLockInTrz"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$aryProperParameter);
            $boolResult = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrMsg = $aryRetBody[3];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($boolResult,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    }
    //（下位）オーケストレータの、作業インスタンステーブルのシーケンスをロック----
    
    //----（下位）オーケストレータに、作業№を登録する
    function registerExecuteNo($varOrchestratorId, $intPatternId, $intOperationNoUAPK, $strPreserveDatetime, $boolTrzAlreadyStarted=false, $aryProperParameter=array()){
        //----RETSET[2016-03-11]
        $strExecutionNo = "";
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strRegisterDate = "";
        $strExpectedErrMsgBodyForUI = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"registerExecuteNo"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$intPatternId,$intOperationNoUAPK,$strPreserveDatetime,$boolTrzAlreadyStarted,$aryProperParameter);
            $strExecutionNo = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrMsg = $aryRetBody[3];
            $strRegisterDate = $aryRetBody[4];
            $strExpectedErrMsgBodyForUI = $aryRetBody[5];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($strExecutionNo,$intErrorType,$aryErrMsgBody,$strErrMsg,$strRegisterDate,$strExpectedErrMsgBodyForUI);
        return $retArray;
    }
    //（下位）オーケストレータに、作業№を登録する----
    
    //----モニターページへのジャンプ用URLを出力する
    function getJumpMonitorUrl($varOrchestratorId, $target_execution_no, $aryProperParameter=array()){
        $strUrlBody = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"getJumpMonitorUrl"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$target_execution_no,$aryProperParameter);
            $strUrlBody = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrMsg = $aryRetBody[3];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($strUrlBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    }
    //モニターページへのジャンプ用URLを出力する----
    
    //----ある1のオーケストレータの、存在している作業パターンを緊急停止する
    function srcamExecute($varOrchestratorId, $target_execution_no, $aryProperParameter=array()){
        $intResultDetail = null;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRetMsgBody = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"srcamExecute"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$target_execution_no,$aryProperParameter);
            $intResultDetail = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrMsg = $aryRetBody[3];
            $aryRetMsgBody = $aryRetBody[4];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($intResultDetail,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRetMsgBody);
        return $retArray;
    }
    //ある1のオーケストレータの、存在している作業パターンを緊急停止する----
    
    //----ある1のオーケストレータの、存在している作業パターンを取得する
    function getLivePatternList($varOrchestratorId, $strSearchKeyValue="", $boolBinaryDistinctOnDTiS=true, $aryProperParameter=array()){
        $aryRow = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"getLivePatternList"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$strSearchKeyValue,$boolBinaryDistinctOnDTiS,$aryProperParameter);
            $aryRow = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrorBuf = $aryRetBody[3];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($aryRow,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
    //ある1のオーケストレータの、存在している作業パターンを取得する----
    
    //----テーマカラーの名前を取得する
    function getThemeColorName($varOrchestratorId, $aryProperParameter=array()){
        $strThemeColorName = '';
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        try{
            $intOrchestratorId = intval($varOrchestratorId);
            if( $intOrchestratorId < 1 ){
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            list($objFunction,$tmpBoolKeyExists) = isSetInArrayNestThenAssign($this->aryFxPerOrchestrator,array("ORC_".$intOrchestratorId,"getThemeColorName"),null);
            if( $tmpBoolKeyExists===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( is_callable($objFunction)===false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRetBody = $objFunction($this,$aryProperParameter);
            $strThemeColorName = $aryRetBody[0];
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
            $strErrorBuf = $aryRetBody[3];
        }
        catch (Exception $e){
            $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
        }
        $retArray = array($strThemeColorName,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
    //テーマカラーの名前を取得する----
    
    
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    // ----ここから固有定義関数                                //
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    
    function getSymphonyStatusFromMovement($aryMovementInstanceOfSingleSymphony){
        ////////////////////////////////////////////////////////////////
        // ムーブメントインスタンスから、シンフォニー関連の情報を取得 //
        ////////////////////////////////////////////////////////////////
        $aryStatusInfo = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        // 各種ローカル定数を定義
        $intFocusCorrectSeq = 0;
        $aryUnStartedMovement = array();
        $aryRunningMovement = array();
        $rowEndedMovement = array();
        $aryAbortedMovement = array();
        
        $rowOfFocusMovement = null;
        
        try{
            //----MOVシーケンスの値とループカウンタの値を比較しつつ、現在の楽章を探す

            // 楽章の数を取得
            $intMovementLength = count($aryMovementInstanceOfSingleSymphony);

            foreach($aryMovementInstanceOfSingleSymphony as $rowOfMovement ){
                $intFocusCorrectSeq += 1;
                if( $rowOfMovement['I_MOVEMENT_SEQ']!= $intFocusCorrectSeq ){
                    // 例外処理へ
                    $strErrStepIdInFx="00000100";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                //
                switch( $rowOfMovement['STATUS_ID'] ){
                    case "1": // 未実行
                        $aryUnStartedMovement[] = $rowOfMovement;
                        break;
                    case "2":  // 準備中
                    case "3":  // 実行中
                    case "4":  // 実行中(遅延)
                    case "5":  // 実行完了
                    case "12": // Skip完了
                    case "8":  // 保留中
                    case "13": // Skip後保留中
                        $aryRunningMovement[] = $rowOfMovement;
                        break;
                    case "9":  // 正常終了
                    case "14": // SKIP終了
                        $rowEndedMovement[] = $rowOfMovement;
                        break;
                    case "6": // 異常終了
                    case "7": // 緊急停止
                    case "10": // 準備エラー
                    case "11": // 想定外エラー
                    default:
                        $aryAbortedMovement[] = $rowOfMovement;
                        break;
                }
            }
            //MOVシーケンスの値とループカウンタの値を比較しつつ、現在の楽章を探す----
            
            // 開始していない楽章の数を取得
            $intUnstartedMovementLength = count($aryUnStartedMovement);

            // 中断された楽章の数を取得
            $intAbortedMovementLength = count($aryAbortedMovement);
            
            // 終了した楽章の数を取得
            $intEndedMovementLength = count($rowEndedMovement);
            
            if( $intUnstartedMovementLength==$intMovementLength ){
                //----まだ第1楽章も始まっていない場合
                $intFocusMovementSeq = 0;
                //まだ第1楽章も始まっていない場合----
            }
            else{
                //----すでに1個は楽章がはじまった後である場合
                
                //----ムーブメントで、オーケストレータに問い合わせが必要なステータスのレコードが1個かどうかを確認する
                if( 2 <= count($aryRunningMovement) ){
                    // 例外処理へ
                    $strErrStepIdInFx="00000200";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                //ムーブメントで、オーケストレータに問い合わせが必要なステータスのレコードが1個かどうかを確認する----
                
                if( count($aryRunningMovement)== 1 ){
                    //----現在のムーブメントが、実行中系だった場合
                    $rowOfFocusMovement = $aryRunningMovement[0];
                    //現在のムーブメントが、実行中系だった場合----
                }
                else if(count( $aryRunningMovement)== 0 ){
                    //----現在のムーブメントが、実行中系ではなかった場合
                    if( 0 < $intAbortedMovementLength ){
                        //----中断されていた場合
                        if( 1 < $intAbortedMovementLength ){
                            // 例外処理へ
                            $strErrStepIdInFx="00000300";
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                        $rowOfFocusMovement = $aryAbortedMovement[$intAbortedMovementLength - 1];
                        //中断されていた場合----
                    }
                    else{
                        //----正常終了系だった場合
                        $rowOfFocusMovement = $rowEndedMovement[$intEndedMovementLength - 1];
                        //正常終了系だった場合----
                    }
                    //現在のムーブメントが、実行中系ではなかった場合----
                }
                $intFocusMovementSeq = intval($rowOfFocusMovement['I_MOVEMENT_SEQ']);
                
                //すでに1個は楽章がはじまった後である場合----
            }
            $aryStatusInfo = array('MOVEMENT_LENGTH'=>$intMovementLength
                                  ,'FOCUS_MOVEMENT_SEQ'=>$intFocusMovementSeq
                                  ,'FOCUS_MOVEMENT_ROW'=>$rowOfFocusMovement);
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($aryStatusInfo,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
    
    //----存在しているオーケストレータ情報を取得する
    function getLiveOrchestratorFromMaster(){
        /////////////////////////////////////////////////////////////
        // 作業パターン情報を取得                                  //
        /////////////////////////////////////////////////////////////
        $aryRow = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        // 各種ローカル定数を定義
        $strOrchestratorMasterTableId      = 'B_ITA_EXT_STM_MASTER';
        
        $strOrchestratorMasterKeyColId     = 'ITA_EXT_STM_ID';
        $strOrchestratorMasterDispColId    = 'ITA_EXT_STM_NAME';
        $strOrchestratorMasterLibPayhColId = 'ITA_EXT_LINK_LIB_PATH';
        
        try{
            $objDBCA = $this->getDBConnectAgent();
            
            // SQL作成
            $tmpAryBind = array();
            $aryWhereZone = array();
            //
            $aryWhereZone[] = "DISUSE_FLAG IN ('0') ";
            //
            $strWhereZone = implode(" AND ",$aryWhereZone);
            
            $sql = "SELECT {$strOrchestratorMasterKeyColId} ITA_EXT_STM_ID, "
                  ."{$strOrchestratorMasterDispColId} ITA_EXT_STM_NAME, "
                  ."{$strOrchestratorMasterLibPayhColId} ITA_EXT_LINK_LIB_PATH "
                  ."FROM   {$strOrchestratorMasterTableId} "
                  ."WHERE  {$strWhereZone} "
                  ."ORDER  BY DISP_SEQ ASC";
            
            $retArray = singleSQLCoreExecute($objDBCA, $sql, $tmpAryBind, $strFxName);
            if( $retArray[0]!==true ){
                $intErrorType = $retArray[1];
                $aryErrMsgBody = $retArray[2];
                $strErrMsg = $retArray[4];
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objQuery =& $retArray[3];
            while($row = $objQuery->resultFetch() ){
                $varRIKeyValue = $row['ITA_EXT_STM_ID'];
                $aryRow[$varRIKeyValue] = $row;
            }
            //
            unset($objQuery);
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($aryRow,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
    //存在しているオーケストレータ情報を取得する
    
    //----存在している作業パターンを取得する
    function getLivePatternFromMaster($aryOrchestratorId=array(),$strSearchKeyValue="",$aryPatternId=array()){
        /////////////////////////////////////////////////////////////
        // 作業パターン情報を取得                                  //
        /////////////////////////////////////////////////////////////
        $aryRow = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        // 各種ローカル定数を定義
        $strPatternMasterTableId   = 'C_PATTERN_PER_ORCH';
        
        $strPatternMasterKeyColId  = 'PATTERN_ID';
        $strPatternMasterDispColId = 'PATTERN_NAME';
        $strPatternMasterOrcColId  = 'ITA_EXT_STM_ID';
        $strPatternMasterTimeLimit = 'TIME_LIMIT';
        
        $strPatternMasterAnsHostDesignType = 'ANS_HOST_DESIGNATE_TYPE_ID';
        $strPatternMasterAnsWinRM = 'ANS_WINRM_ID';
        $strPatternMasterAnsParaEx = 'ANS_PARALLEL_EXE';

        $boolBinaryDistinctOnDTiS = false; //false=あいまい

        $strPatternMasterAnsPlaybookHedDef    = 'ANS_PLAYBOOK_HED_DEF';
        $strPatternMasterAnsExecOption        = 'ANS_EXEC_OPTIONS';
        $strPatternMasterOpenst_Template      = 'OPENST_TEMPLATE';
        $strPatternMasterOpenst_Env           = 'OPENST_ENVIRONMENT';
        $strPatternMasterAnsVirtualEnvName    = 'ANS_VIRTUALENV_NAME';

        $strPatternMasterTerraformWorkspaceID = 'TERRAFORM_WORKSPACE_ID';
        
        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            
            // SQL作成
            $tmpAryBind = array();
            $aryWhereZone = array();
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
            //----オーケストレータで絞り込む
            if( 0 < count($aryOrchestratorId) ){
                $arySearchValueBody = array();
                $arySearchValueCount = 0;
                $tmpValue = null;
                foreach( $aryOrchestratorId as $tmpValue ){
                    $arySearchValueCount += 1;
                    $strBindKey = "ITA_EXT_STM_ID__".$arySearchValueCount;
                    $arySearchValueBody[] = ":".$strBindKey;
                    $tmpAryBind[$strBindKey] = $tmpValue;
                }
                $aryWhereZone[] = "ITA_EXT_STM_ID IN (".implode(",",$arySearchValueBody). ")";
                unset($arySearchValueCount);
                unset($arySearchValueBody);
                unset($tmpValue);
            }
            //オーケストレータで絞り込む----
            //----作業パターンで絞り込む
            if( 0 < count($aryPatternId) ){
                $arySearchValueBody = array();
                $arySearchValueCount = 0;
                $tmpValue = null;
                foreach( $aryPatternId as $tmpValue ){
                    $arySearchValueCount += 1;
                    $strBindKey = "PATTERN_ID__".$arySearchValueCount;
                    $arySearchValueBody[] = ":".$strBindKey;
                    $tmpAryBind[$strBindKey] = $tmpValue;
                }
                $aryWhereZone[] = "PATTERN_ID IN (".implode(",",$arySearchValueBody). ")";
                unset($arySearchValueCount);
                unset($arySearchValueBody);
                unset($tmpValue);
            }
            //作業パターンで絞り込む----
            $strWhereZone = implode(" AND ",$aryWhereZone);
            
            $sql = "SELECT {$strPatternMasterKeyColId} PATTERN_ID "
                  .",{$strPatternMasterDispColId} PATTERN_NAME "
                  .",{$strPatternMasterOrcColId} ITA_EXT_STM_ID "
                  .",{$strPatternMasterTimeLimit} TIME_LIMIT "
                  .",{$strPatternMasterAnsHostDesignType} ANS_HOST_DESIGNATE_TYPE_ID "
                  .",{$strPatternMasterAnsParaEx} ANS_PARALLEL_EXE "
                  .",{$strPatternMasterAnsWinRM} ANS_WINRM_ID "
                  .",{$strPatternMasterAnsPlaybookHedDef} ANS_PLAYBOOK_HED_DEF "
                  .",{$strPatternMasterAnsExecOption} ANS_EXEC_OPTIONS "
                  .",{$strPatternMasterAnsVirtualEnvName} ANS_VIRTUALENV_NAME "
                  .",{$strPatternMasterOpenst_Template} OPENST_TEMPLATE "
                  .",{$strPatternMasterOpenst_Env} OPENST_ENVIRONMENT "
                  .",{$strPatternMasterTerraformWorkspaceID} TERRAFORM_WORKSPACE_ID "
                  ."FROM   {$strPatternMasterTableId} "
                  ."WHERE  {$strWhereZone} "
                  ."ORDER  BY DISP_SEQ ASC";
            
            $retArray = singleSQLCoreExecute($objDBCA, $sql, $tmpAryBind, $strFxName);
            if( $retArray[0]!==true ){
                $intErrorType = $retArray[1];
                $aryErrMsgBody = $retArray[2];
                $strErrMsg = $retArray[4];
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objQuery =& $retArray[3];
            while($row = $objQuery->resultFetch() ){
                $varRIKeyValue = $row['PATTERN_ID'];
                $aryRow[$varRIKeyValue] = $row;
            }
            //
            unset($objQuery);
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($aryRow,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
    //存在している作業パターンを取得する----
    
    
    //----オペレーション情報を取得する
    function getInfoOfOneOperation($intValueForSearchOneOpeRecord,$intSearchMode=0){
        /////////////////////////////////////////////////////////////
        // オペレーション情報を取得                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfOperationTable = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        $strSysErrMsgBody = "";
        //
        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            
            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($lc_db_model_ch,"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
            $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
            
            // ----全行および全行中、最後に更新された日時を取得する
            $arrayConfigForSelect = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "OPERATION_NO_UAPK"=>"",
                "OPERATION_NAME"=>"",
                "OPERATION_DATE"=>"DATEDATE",
                "OPERATION_NO_IDBH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "OPERATION_NO_UAPK"=>"",
                "OPERATION_NAME"=>"",
                "OPERATION_DATE"=>"",
                "OPERATION_NO_IDBH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;
            
            $strSelectMode = "SELECT";
            $strSelectForUpdateLock = "";
            
            if( $intSearchMode === 0 ){
                $strColumnIdForSearch = "OPERATION_NO_UAPK";
            }
            else if( $intSearchMode === 1 ){
                $strColumnIdForSearch = "OPERATION_NO_IDBH";
            }
            else{
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $temp_array = array('WHERE'=>"{$strColumnIdForSearch} = :{$strColumnIdForSearch} AND DISUSE_FLAG IN ('0') {$strSelectForUpdateLock}");
            
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"OPERATION_NO_UAPK"
                                                ,"C_OPERATION_LIST"
                                                ,"C_OPERATION_LIST_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind[$strColumnIdForSearch] = $intValueForSearchOneOpeRecord;
            
            $retArray = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            if( $retArray[0]!==true ){
                $intErrorType = $retArray[1];
                $aryErrMsgBody = $retArray[2];
                $strErrMsg = $retArray[4];
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objQueryUtn =& $retArray[3];
            
            //----発見行だけループ
            $intCount = 0;
            $aryRowOfSymClassTable = array();
            while ( $row = $objQueryUtn->resultFetch() ){
                if( $intCount==0 ){
                    $aryRowOfOperationTable = $row;
                }
                $intCount += 1;
            }
            //発見行だけループ----
            
            if( $intCount!== 1 ){
                // 例外処理へ
                if( $intCount === 0 ){
                    //----廃止などで存在しない場合があるので、想定内エラー
                    $intErrorType = 101;
                    //廃止などで存在しない場合があるので、想定内エラー----
                }
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfOperationTable);
        return $retArray;
    }
    //オペレーション情報を取得する----

    //----シンフォニー情報を取得する
    function getInfoOfOneSymphony($intValueForSearchOneOpeRecord, $fxVarsIntMode=0){
        /////////////////////////////////////////////////////////////
        // シンフォニー情報を取得                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfSymClassTable = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        $strSysErrMsgBody = "";
        //
        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            
            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($lc_db_model_ch,"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
            $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
            
            // ----全行および全行中、最後に更新された日時を取得する
            $arrayConfigForSelect = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "SYMPHONY_CLASS_NO"=>"",
                "SYMPHONY_NAME"=>"",
                "DESCRIPTION"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "SYMPHONY_CLASS_NO"=>"",
                "SYMPHONY_NAME"=>"",
                "DESCRIPTION"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;
            
            $strSelectMode = "SELECT";
            $strWhereDisuseFlag = "('0')";
            $strOrderByArea = "";
            if( $fxVarsIntMode === 1 ){
                //----更新用のため、ロック
                $strSelectMode = "SELECT FOR UPDATE";
                //更新用のため、ロック----
            }
            
            $temp_array = array('WHERE'=>"SYMPHONY_CLASS_NO = :SYMPHONY_CLASS_NO AND DISUSE_FLAG IN {$strWhereDisuseFlag}");
            
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"SYMPHONY_CLASS_NO"
                                                ,"C_SYMPHONY_CLASS_MNG"
                                                ,"C_SYMPHONY_CLASS_MNG_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind['SYMPHONY_CLASS_NO'] = $intValueForSearchOneOpeRecord;
            
            $retArray = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            if( $retArray[0]!==true ){
                $intErrorType = $retArray[1];
                $aryErrMsgBody = $retArray[2];
                $strErrMsg = $retArray[4];
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objQueryUtn =& $retArray[3];
            
            //----発見行だけループ
            $intCount = 0;
            $aryRowOfSymClassTable = array();
            while ( $row = $objQueryUtn->resultFetch() ){
                if( $intCount==0 ){
                    $aryRowOfSymClassTable = $row;
                }
                $intCount += 1;
            }
            //発見行だけループ----
            
            if( $intCount!== 1 ){
                // 例外処理へ
                if( $intCount === 0 ){
                    //----廃止などで存在しない場合があるので、想定内エラー
                    $intErrorType = 101;
                    //廃止などで存在しない場合があるので、想定内エラー----
                }
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfSymClassTable);
        return $retArray;
    }
    //シンフォニー情報を取得する----

    //----ムーブメント情報を取得する
    function getInfoOfOneMovement($intValueForSearchOneMovRecord, $fxVarsIntMode=0, $intSearchMode=0){
        /////////////////////////////////////////////////////////////
        // ムーブメント情報を取得                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfOperationTable = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        
        $strSysErrMsgBody = "";

        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            
            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($lc_db_model_ch,"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
            $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
            
            // ----全行および全行中、最後に更新された日時を取得する
            $arrayConfigForSelect = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "MOVEMENT_CLASS_NO"=>"",
                "ORCHESTRATOR_ID"=>"",
                "PATTERN_ID"=>"",
                "MOVEMENT_SEQ"=>"",
                "NEXT_PENDING_FLAG"=>"",
                "DESCRIPTION"=>"",
                "SYMPHONY_CLASS_NO"=>"",
                "OPERATION_NO_IDBH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "MOVEMENT_CLASS_NO"=>"",
                "ORCHESTRATOR_ID"=>"",
                "PATTERN_ID"=>"",
                "MOVEMENT_SEQ"=>"",
                "NEXT_PENDING_FLAG"=>"",
                "DESCRIPTION"=>"",
                "SYMPHONY_CLASS_NO"=>"",
                "OPERATION_NO_IDBH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;
            
            $strSelectMode = "SELECT";
            $strWhereDisuseFlag = "('0')";
            $strOrderByArea = " ORDER BY MOVEMENT_SEQ ASC";
            if( $fxVarsIntMode === 1 ){
                //----更新するため、廃止されているムーブメントレコードも拾う
                $strWhereDisuseFlag = "('0','1')";
                //更新するため、廃止されているムーブメントレコードも拾う----
                
                //----更新用のため、ロック
                $strSelectMode = "SELECT FOR UPDATE";
                //更新用のため、ロック----
            }
            
            if( $intSearchMode === 0 ){
                $strColumnIdForSearch = "SYMPHONY_CLASS_NO";
            }
            else if( $intSearchMode === 1 ){
                $strColumnIdForSearch = "MOVEMENT_CLASS_NO";
            }
            else if( $intSearchMode === 2 ){
                $strColumnIdForSearch = "OPERATION_NO_IDBH";
            }
            else{
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $temp_array = array('WHERE'=>"{$strColumnIdForSearch} = :{$strColumnIdForSearch} AND DISUSE_FLAG IN {$strWhereDisuseFlag} {$strOrderByArea}");
            
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"MOVEMENT_CLASS_NO"
                                                ,"C_MOVEMENT_CLASS_MNG"
                                                ,"C_MOVEMENT_CLASS_MNG_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind[$strColumnIdForSearch] = $intValueForSearchOneMovRecord;

            $retArray = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            if( $retArray[0]!==true ){
                $intErrorType = $retArray[1];
                $aryErrMsgBody = $retArray[2];
                $strErrMsg = $retArray[4];
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objQueryUtn =& $retArray[3];

            //----発見行だけループ
            $intCount = 0;
            $aryRowOfMovClassTable = array();
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryRowOfMovClassTable[] = $row;
                $intCount += 1;
            }
            //発見行だけループ----

            // 例外処理へ
            if( $intCount === 0 ){
                //----廃止などで存在しない場合があるので、想定内エラー
                $intErrorType = 101;
                //廃止などで存在しない場合があるので、想定内エラー----
            }

            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfMovClassTable);
        return $retArray;
    }
    //ムーブメント情報を取得する----

    //----ある１のシンフォニークラスの、シンフォニー部分、ムーブメント部分の情報を取得する
    function getInfoFromOneOfSymphonyClasses($fxVarsIntSymphonyClassId, $fxVarsIntMode=0 , $intSearchMode=0){
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfSymClassTable = array();
        $aryRowOfMovClassTable = array();
        $strFxName = '([FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        
        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $aryRetBody = $this->getInfoOfOneSymphony($fxVarsIntSymphonyClassId, $fxVarsIntMode);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrMsg = $aryRetBody[4];
                $strErrStepIdInFx="00000100";
                if( $aryRetBody[1] === 101 ){
                    //----１行も発見できなかった場合
                    $intErrorType = 101;
                    //１行も発見できなかった場合----
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfSymClassTable = $aryRetBody[4];
            
            $aryRetBody = $this->getInfoOfOneMovement($fxVarsIntSymphonyClassId, $fxVarsIntMode, $intSearchMode);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrMsg = $aryRetBody[4];
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $aryRowOfMovClassTable = $aryRetBody[4];
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType === null ) $intErrorType = 500;
            $tmpErrMsgBody = $e->getMessage();
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfSymClassTable,$aryRowOfMovClassTable);
        return $retArray;
    }
//ある１のシンフォニークラスの、シンフォニー部分、ムーブメント部分の情報を取得する----


//----シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録する
    function registerSymphonyInstance($intShmphonyClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName){
        // ----変数定義
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $intSymphonyInstanceId = null;
        $strExpectedErrMsgBodyForUI = "";
        $aryFreeErrMsgBody = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $boolInTransactionFlag = false;

        $arrayConfigForSymInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "I_SYMPHONY_CLASS_NO"=>"",
            "I_SYMPHONY_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        
        $arraySymInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "I_SYMPHONY_CLASS_NO"=>"",
            "I_SYMPHONY_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        
        $arrayConfigForMovInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "MOVEMENT_INSTANCE_NO"=>"",
            "I_MOVEMENT_CLASS_NO"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            "I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "EXECUTION_NO"=>"",
            "STATUS_ID"=>"",
            "ABORT_RECEPTED_FLAG"=>"",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "RELEASED_FLAG"=>"",
            "EXE_SKIP_FLAG"=>"",
            "OVRD_OPERATION_NO_UAPK"=>"",
            "OVRD_I_OPERATION_NAME"=>"",
            "OVRD_I_OPERATION_NO_IDBH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        
        $arrayMovInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "MOVEMENT_INSTANCE_NO"=>"",
            "I_MOVEMENT_CLASS_NO"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            "I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "EXECUTION_NO"=>"",
            "STATUS_ID"=>"",
            "ABORT_RECEPTED_FLAG"=>"",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "RELEASED_FLAG"=>"",
            "EXE_SKIP_FLAG"=>"",
            "OVRD_OPERATION_NO_UAPK"=>"",
            "OVRD_I_OPERATION_NAME"=>"",
            "OVRD_I_OPERATION_NO_IDBH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        // 変数定義----


        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $objMTS = $this->getMessageTemplateStorage();

            // ----トランザクション開始
            $varTrzStart = $objDBCA->transactionStart();
            if( $varTrzStart === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $boolInTransactionFlag = true;
            // トランザクション開始----


            ////////////////////////////////////////////////////////
            // (ここから) シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する//
            ///////////////////////////////////////////////////////
            // ----MOV-INSTANCE-シーケンスを掴む
            $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_RIC','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // MOV-INSTANCE-シーケンスを掴む----

            // ----SYM-INSTANCE-シーケンスを掴む
            $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_RIC','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // -SYM-INSTANCE-シーケンスを掴む----
            ////////////////////////////////////////////////////////
            // (ここまで) シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する//
            ///////////////////////////////////////////////////////


            //////////////////////////////////////////////////////
            // (ここから) シンフォニー、ムーブメント、オペレーションの情報を取得する//
            /////////////////////////////////////////////////////
            // ----シンフォニークラスIDからシンフォニー部分、ムーブメント部分の情報を取得する
            $aryRetBody = $this->getInfoFromOneOfSymphonyClasses($intShmphonyClassId, 0);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                if( $aryRetBody[1] === 101 ){
                    //----該当のシンフォニーClassIDが１行も発見できなかった場合
                    $intErrorType = 101;
                    //$strExpectedErrMsgBodyForUI = "SymphonyクラスID：存在している必要があります。";
                    $strErrMsg = $aryRetBody[3];
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733107");
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    //該当のシンフォニーClassIDが１行も発見できなかった場合----
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfSymClassTable = $aryRetBody[4];
            $aryRowOfMovClassTable = $aryRetBody[5];
            // シンフォニークラスIDからシンフォニー部分、ムーブメント部分の情報を取得する----

            // ----オペレーションNoからオペレーションの情報を取得する
            $arrayRetBody = $this->getInfoOfOneOperation($intOperationNoUAPK);
            if( $arrayRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                if( $arrayRetBody[1] === 101 ){
                    $intErrorType = 102;
                    //$strExpectedErrMsgBodyForUI = "オペレーション№：存在している必要があります。";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfOperationTable = $arrayRetBody[4];
            // オペレーションNoからオペレーションの情報を取得する----
            //////////////////////////////////////////////////////
            // (ここまで) シンフォニー、ムーブメント、オペレーションの情報を取得する//
            /////////////////////////////////////////////////////


            /////////////////////////////////////
            // (ここから) シンフォニーインスタンスを登録する//
            /////////////////////////////////////
            //テーブル情報をセット
            $arrayConfigForIUD = $arrayConfigForSymInsIUD;
            $register_tgt_row = $arraySymInsValueTmpl;

            // ----シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varRISeq = $retArray[0];
            }
            // シーケンス払い出し----

            // ----シンフォニーインスタンス登録用の値をセット
            $varSymphonyInstanceNo = $varRISeq;
            $register_tgt_row['SYMPHONY_INSTANCE_NO'] = $varSymphonyInstanceNo;
            $register_tgt_row['I_SYMPHONY_CLASS_NO']  = $aryRowOfSymClassTable['SYMPHONY_CLASS_NO'];
            $register_tgt_row['I_SYMPHONY_NAME']      = $aryRowOfSymClassTable['SYMPHONY_NAME'];
            $register_tgt_row['I_DESCRIPTION']        = $aryRowOfSymClassTable['DESCRIPTION'];
            //----開始予約時刻が設定されていた場合
            if( strlen($strPreserveDatetime)==0 ){
                $varStatus = 1; //未実行
            }
            else{
                $varStatus = 2; //未実行(予約)
                $register_tgt_row['TIME_BOOK']            = $strPreserveDatetime;
            }
            //開始予約時刻が設定されていた場合----
            $register_tgt_row['STATUS_ID']            = $varStatus; //未実行[1]または未実行(予約)[2]
            $register_tgt_row['EXECUTION_USER']       = $userName;
            $register_tgt_row['OPERATION_NO_UAPK']    = $intOperationNoUAPK;
            $register_tgt_row['I_OPERATION_NAME']     = $aryRowOfOperationTable['OPERATION_NAME'];
            $register_tgt_row['ABORT_EXECUTE_FLAG']   = 1; //緊急停止発令フラグ(未発令)=[1]
            $register_tgt_row['DISUSE_FLAG']          = '0';
            $register_tgt_row['LAST_UPDATE_USER']     = $userId;
            $tgtSource_row = $register_tgt_row;
            // シンフォニーインスタンス登録用の値をセット----

            // ----シンフォニーインスタンス登録用SQLを作成
            $sqlType = "INSERT";
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$sqlType
                                                ,"SYMPHONY_INSTANCE_NO"
                                                ,"C_SYMPHONY_INSTANCE_MNG"
                                                ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            // シンフォニーインスタンス登録用SQLを作成----

            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varJSeq = $retArray[0];
                $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            // 履歴シーケンス払い出し----

            // ----シンフォニーインスタンス登録の実行
            $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
            if( $retArray01[0] !== true || $retArray02[0] !== true ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
            // シンフォニーインスタンス登録の実行----

            /////////////////////////////////////
            // (ここまで) シンフォニーインスタンスを登録する//
            /////////////////////////////////////


            /////////////////////////////////////
            // (ここから) ムーブメントインスタンスを登録する//
            /////////////////////////////////////
            // ----ムーブメントから、廃止されているレコードを除外する
            $aryMovement = array();
            foreach($aryRowOfMovClassTable as $aryDataForMovement){
                if( $aryDataForMovement['DISUSE_FLAG']=='0' ){
                    $aryMovement[] = $aryDataForMovement;
                }
            }
            // ムーブメントから、廃止されているレコードを除外する----

            //----$aryOptionOrderOverrideがnullでない場合、各値をセットする
            //（RESTおよびbackyard処理で登録する場合を想定。）
            if( is_array($aryOptionOrderOverride) === true ){
                $intFocusIndex = 0;
                $aryOptionOrder = array();
                foreach($aryMovement as $aryDataForMovement){
                    $aryTmp1ForOverride = array();
                    $aryTmp1ForOverride['MOVEMENT_SEQ']           = $intFocusIndex + 1;
                    $tmp1StrOrcId     = $aryDataForMovement['ORCHESTRATOR_ID'];
                    $tmp1StrPatternId = $aryDataForMovement['PATTERN_ID'];
                    
                    if( array_key_exists($intFocusIndex + 1, $aryOptionOrderOverride) === true ){
                        //----あるムーブメントについて指定があった場合
                        $aryTmp2ForOverride = $aryOptionOrderOverride[$intFocusIndex + 1];
                        //あるムーブメントについて指定があった場合----
                    }
                    else{
                        $aryTmp2ForOverride = array();
                    }
                    
                    list($tmp1StrExeSkipFlag, $boolTempKeyExistFlag) = isSetInArrayNestThenAssign($aryTmp2ForOverride, array('SKIP')        , ""); 
                    list($tmp1StrOvrdOpeId  , $boolTempKeyExistFlag) = isSetInArrayNestThenAssign($aryTmp2ForOverride, array('OPERATION_ID'), "");
                    
                    if( $tmp1StrExeSkipFlag === "YES" ){
                        // checkedValueならスキップ
                        $tmp1StrExeSkipFlag = "checkedValue";
                    }
                    else if( $tmp1StrExeSkipFlag === "NO" || strlen($tmp1StrExeSkipFlag) === 0 ){
                        $tmp1StrExeSkipFlag = "";
                    }
                    else{
                        $tmp1StrExeSkipFlag = "FORBIDDEN_VALUE";
                    }
                    
                    $aryTmp1ForOverride['ORCHESTRATOR_ID']        = $tmp1StrOrcId;
                    $aryTmp1ForOverride['PATTERN_ID']             = $tmp1StrPatternId;
                    $aryTmp1ForOverride['EXE_SKIP_FLAG']          = $tmp1StrExeSkipFlag;
                    $aryTmp1ForOverride['OVRD_OPERATION_NO_IDBH'] = $tmp1StrOvrdOpeId;
                    $aryOptionOrder[] = $aryTmp1ForOverride;
                    
                    unset($tmp1StrOrcId);
                    unset($tmp1StrPatternId);
                    unset($tmp1StrExeSkipFlag);
                    unset($tmp1StrOvrdOpeNo);
                    unset($aryTmp1ForOverride);
                    unset($aryTmp2ForOverride);
                    
                    $intFocusIndex += 1;
                }
                unset($tmpAryMultiLivePatternFromMaster);
            }
            //$aryOptionOrderOverrideがnullでない場合、各値をセットする----

            //----$aryMovementのカウントチェック
            if( count($aryMovement) !== count($aryOptionOrder) ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //$aryMovementのカウントチェック----
            //----$aryOptionOrderのカウントチェック
            if( count($aryOptionOrder) == 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //$aryOptionOrderのカウントチェック----
            

            // ----ムーブメントインスタンス登録処理
            $MovementErrorMsg = "";
            $intFocusIndex = 0;

            foreach($aryMovement as $aryDataForMovement){
                $aryValuePerOptionOrderKey = $aryOptionOrder[$intFocusIndex];
                //テーブル情報をセット
                $arrayConfigForIUD = $arrayConfigForMovInsIUD;
                $register_tgt_row = $arrayMovInsValueTmpl;

                // ----シーケンス払い出し
                $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001400";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                else{
                    $varRISeq = $retArray[0];
                }
                // シーケンス払い出し----

                // ----PATTERN_IDからパターン情報を取得
                $strPatternIdNumeric = $aryDataForMovement['PATTERN_ID'];
                $retArray = $this->getLivePatternFromMaster(array($aryDataForMovement['ORCHESTRATOR_ID']),"",array($strPatternIdNumeric));
                if($retArray[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001500";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                $aryMultiLivePatternFromMaster = $retArray[0];
                // PATTERN_IDからパターン情報を取得----

                // ----movementの存在をチェック
                if( array_key_exists($strPatternIdNumeric, $aryMultiLivePatternFromMaster) === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001600";
                    $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-1990037",array($intFocusIndex + 1));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                // movementの存在をチェック----

                //----差分がないかをチェック
                if( ($intFocusIndex + 1) != $aryValuePerOptionOrderKey['MOVEMENT_SEQ'] ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001700";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                // オーケストレータが同じかどうか、をチェック
                if( $aryDataForMovement['ORCHESTRATOR_ID'] != $aryValuePerOptionOrderKey['ORCHESTRATOR_ID'] ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001800";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                // 作業パターンが同じかどうか、をチェック
                if( $strPatternIdNumeric != $aryValuePerOptionOrderKey['PATTERN_ID'] ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001900";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }

                $arySinglePatternSource = $aryMultiLivePatternFromMaster[$strPatternIdNumeric];
                unset($aryMultiLivePatternFromMaster);
                //差分がないかをチェック----

                // ----ムーブメントインスタンス登録用の値をセット
                $register_tgt_row = array();
                $register_tgt_row['MOVEMENT_INSTANCE_NO'] = $varRISeq;
                $register_tgt_row['I_MOVEMENT_SEQ']       = $intFocusIndex + 1;
                $register_tgt_row['I_MOVEMENT_CLASS_NO']  = $aryDataForMovement['MOVEMENT_CLASS_NO'];
                $register_tgt_row['I_PATTERN_ID']         = $strPatternIdNumeric;
                $register_tgt_row['I_PATTERN_NAME']       = $arySinglePatternSource['PATTERN_NAME'];
                $register_tgt_row['I_ANS_HOST_DESIGNATE_TYPE_ID'] = $arySinglePatternSource['ANS_HOST_DESIGNATE_TYPE_ID'];
                $register_tgt_row['I_ANS_WINRM_ID'] = $arySinglePatternSource['ANS_WINRM_ID'];
                $register_tgt_row['I_ORCHESTRATOR_ID']    = $aryDataForMovement['ORCHESTRATOR_ID'];
                $register_tgt_row['I_NEXT_PENDING_FLAG']  = $aryDataForMovement['NEXT_PENDING_FLAG'];
                if( $aryDataForMovement['NEXT_PENDING_FLAG'] === '1' ){
                    //----保留解除ポイントが存在する場合
                    $register_tgt_row['RELEASED_FLAG']  = '1'; //1=未解除
                    //保留解除ポイントが存在する場合----
                }
                else if( $aryDataForMovement['NEXT_PENDING_FLAG'] === '2' ){
                    //----保留解除ポイントが存在しない場合
                    //$register_tgt_row['RELEASED_FLAG']  = '';
                    //保留解除ポイントが存在しない場合----
                }
                else{
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002000";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }

                if( $aryValuePerOptionOrderKey['EXE_SKIP_FLAG'] == '' ){
                    $register_tgt_row['EXE_SKIP_FLAG']        = 1; //スキップしない
                }
                else if( $aryValuePerOptionOrderKey['EXE_SKIP_FLAG'] == 'checkedValue' ){
                    $register_tgt_row['EXE_SKIP_FLAG']        = 2; //スキップする
                }
                else{
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002100";
                    $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733111",array($intFocusIndex + 1));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                // ----オペレーション情報を取得し値をセット
                if( 0 < strlen($aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH']) ){
                    $tmpStrOpeNoIDBH = $aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH'];
                    $strRegexpFormat='/^0$|^-?[1-9][0-9]*$/s';
                    if( preg_match($strRegexpFormat, $tmpStrOpeNoIDBH) !== 1 ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00002200";
                        $intErrorType = 2;
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733109",array($intFocusIndex + 1),$tmpStrOpeNoIDBH);
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    $tmpAryRetBody = $this->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);
                    if( $tmpAryRetBody[1] !== null ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00002300";
                        if( $tmpAryRetBody[1] == 101 ){
                            $intErrorType = 2;
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733110",array($intFocusIndex + 1));
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                    }
                    $tmpAryRowOfOpeTblPerMov = $tmpAryRetBody[4];
                    $register_tgt_row['OVRD_OPERATION_NO_UAPK']   = $tmpAryRowOfOpeTblPerMov['OPERATION_NO_UAPK'];
                    $register_tgt_row['OVRD_I_OPERATION_NAME']    = $tmpAryRowOfOpeTblPerMov['OPERATION_NAME'];
                    $register_tgt_row['OVRD_I_OPERATION_NO_IDBH'] = $tmpStrOpeNoIDBH;
                    unset($tmpAryRowOfOpeTblPerMov);
                    unset($tmpAryRetBody);
                }
                else{
                    $register_tgt_row['OVRD_OPERATION_NO_UAPK']   = $intOperationNoUAPK;
                }
                // オペレーション情報を取得し値をセット----

                $register_tgt_row['I_DESCRIPTION']        = $aryDataForMovement['DESCRIPTION'];
                $register_tgt_row['ABORT_RECEPTED_FLAG']  = 1; //緊急停止受付確認フラグ=未確認[1]
                $register_tgt_row['SYMPHONY_INSTANCE_NO'] = $varSymphonyInstanceNo;
                $register_tgt_row['STATUS_ID']            = 1; //未実行[1]で
                $register_tgt_row['EXECUTION_USER']       = $userName;
                $register_tgt_row['DISUSE_FLAG']          = '0';
                $register_tgt_row['LAST_UPDATE_USER']     = $userId;
                // ムーブメントインスタンス登録用の値をセット----

                // 各Movementの登録状態を確認する。
                $tgtSource_row = $register_tgt_row;
                $ret = $this->MovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,($intFocusIndex + 1),$aryFreeErrMsgBody);
                if( $ret === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002400";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                // ----ムーブメントインスタンス登録用SQLを作成
                $sqlType = "INSERT";
                $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                    ,$sqlType
                                                    ,"MOVEMENT_INSTANCE_NO"
                                                    ,"C_MOVEMENT_INSTANCE_MNG"
                                                    ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                                    ,$arrayConfigForIUD
                                                    ,$tgtSource_row);
                if( $retArray[0] === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002500";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];
                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                // ムーブメントインスタンス登録用SQLを作成----

                // ----履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002600";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                else{
                    $varJSeq = $retArray[0];
                    $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                }
                // 履歴シーケンス払い出し----

                // ----ムーブメントインスタンス登録の実行
                $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
                $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
                if( $retArray01[0] !== true || $retArray02[0] !== true ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002700";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($retArray01);
                unset($retArray02);
                // ムーブメントインスタンス登録の実行----


                $intFocusIndex += 1;
            }
            // ムーブメントインスタンス登録処理----

            // ----ムーブメントインスタンス登録処理後のチェック
            // ムーブメントの登録内容に不備がなかったことを確認
            if($MovementErrorMsg != ""){
                $strErrStepIdInFx="00002800";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $MovementErrorMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            // Symphonyインターフェース情報の登録データ確認する。
            $strQuery = "SELECT * FROM C_SYMPHONY_IF_INFO WHERE DISUSE_FLAG = '0'";
            $tmpStrInterVal = "";
            $IF_Errormsg = "";
            $objQuery = $objDBCA->sqlPrepare($strQuery);
            $retBoolResult = $objQuery->sqlExecute();
            if($retBoolResult!=true){
                // 例外処理へ
                $strErrStepIdInFx="0002900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if($objQuery->effectedRowCount() == 0) {
                // 未登録
                $IF_Errormsg= $objMTS->getSomeMessage("ITABASEH-ERR-900067");
            } else {
                if($objQuery->effectedRowCount() == 1) {
                    $row = $objQuery->resultFetch();
                    $tmpStrInterVal = $row['SYMPHONY_REFRESH_INTERVAL'];
                    // データリレイストレージのパスを確認
                    if( !is_dir( $row['SYMPHONY_STORAGE_PATH_ITA'] ) ) {
                        $IF_Errormsg = $objMTS->getSomeMessage("ITABASEH-ERR-900069");
                    }
                } else {
                    // 複数登録
                    $IF_Errormsg = $objMTS->getSomeMessage("ITABASEH-ERR-900068");
                }
            }
            unset($objQuery);

            if($IF_Errormsg != "")
            {
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00003000";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $IF_Errormsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // ムーブメントインスタンス登録処理後のチェック----

            /////////////////////////////////////
            // (ここまで) ムーブメントインスタンスを登録する//
            /////////////////////////////////////

            // ----トランザクション終了
            $boolResult = $objDBCA->transactionCommit();
            if ( $boolResult === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00003100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $objDBCA->transactionExit();
            $boolInTransactionFlag = false;
            // トランザクション終了----

            $boolRet = true;
            $intSymphonyInstanceId = $varSymphonyInstanceNo;
        }catch(Exception $e){
            //----トランザクション中のエラーの場合
            if( $boolInTransactionFlag === true){
                if( $objDBCA->transactionRollBack() === true ){
                    $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102090");
                }
                else{
                    $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102070");
                }
                $aryErrMsgBody[] = $tmpMsgBody;
                
                // トランザクション終了
                if( $objDBCA->transactionExit() === true ){
                    $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-103010");
                }
                else{
                    $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102080");
                }
                $aryErrMsgBody[] = $tmpMsgBody;
                unset($tmpMsgBody);
            }
            //トランザクション中のエラーの場合---- 

            // エラーフラグをON
            if( $intErrorType === null ) $intErrorType = 500;
            $tmpErrMsgBody = $e->getMessage();
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        }

        $retArray = array($boolRet,
                          $intErrorType,
                          $aryErrMsgBody,
                          $strErrMsg,
                          $strSysErrMsgBody,
                          $intSymphonyInstanceId,
                          $strExpectedErrMsgBodyForUI,
                          $aryFreeErrMsgBody,
                          );

        return $retArray;
    }
// シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録する----


// ----各Movementの登録状態を確認する
    function MovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex,&$aryFreeErrMsgBody){
        // SKIPが設定されているか判定
        if($tgtSource_row['EXE_SKIP_FLAG'] == 2){
            return true;
        }
        // オペレーションIDを取得
        if(@strlen($tgtSource_row['OVRD_OPERATION_NO_UAPK']) != 0){
            // オペレーションIDの上書きがされている場合
            $intOperationNoUAPK = $tgtSource_row['OVRD_OPERATION_NO_UAPK'];
        }
        switch($tgtSource_row['I_ORCHESTRATOR_ID']){
        case 3:   // legacy
            $ret = $this->AnsibleLegacyMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex,$aryFreeErrMsgBody);
            break;
        case 4:   // pioneer
            $ret = $this->AnsiblePioneerMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex,$aryFreeErrMsgBody);
            break;
        case 5:   // legacy role
            $ret = $this->AnsibleLegacyRoleMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex,$aryFreeErrMsgBody);
            break;
        default:  // 対象外は無条件にtrue
            $ret = true;
        }
        return $ret;
    }
// 各Movementの登録状態を確認する----
// ----Ansible Legacy Movementの登録状態を確認
    function AnsibleLegacyMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex,&$aryFreeErrMsgBody){
        $objMTS = $this->getMessageTemplateStorage();
        $objDBCA = $this->getDBConnectAgent();

        // 作業対象ホストの件数と作業対象ホストに紐づくマスタの登録状況までを確認
        $sql = sprintf(" SELECT                                                           " .
                       "   PHO_LINK_ID AS PKEY,                                           " .
                       "   COUNT(*) AS ROW_COUNT,                                         " .
                       "   OPERATION_NO_UAPK,                                             " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_OPERATION_LIST S_TBL                                     " .
                       "     WHERE                                                        " .
                       "       S_TBL.OPERATION_NO_UAPK = M_TBL.OPERATION_NO_UAPK AND      " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) OPE_COUNT,                                                   " .
                       "   PATTERN_ID,                                                    " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_PATTERN_PER_ORCH S_TBL                                   " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) PTN_COUNT,                                                   " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       B_ANSIBLE_LNS_PATTERN_LINK S_TBL                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) BOOK_COUNT,                                                  " .
                       "   SYSTEM_ID,                                                     " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_STM_LIST S_TBL                                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.SYSTEM_ID  = M_TBL.SYSTEM_ID AND                     " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) HOST_COUNT                                                   " .
                       " FROM                                                             " .
                       "   B_ANSIBLE_LNS_PHO_LINK M_TBL                                   " .
                       " WHERE                                                            " .
                       "   M_TBL.OPERATION_NO_UAPK  = %s    AND                           " .
                       "   M_TBL.PATTERN_ID         = %s    AND                           " .  
                       "   M_TBL.DISUSE_FLAG        = '0';                                ",
                       $intOperationNoUAPK,
                       $tgtSource_row['I_PATTERN_ID']);
        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }

        // 作業パターンID登録確認
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            unset($objQuery);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            // 作業対象ホストが未登録
            if($row['ROW_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990033",array($intFocusIndex));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['OPE_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990034",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['HOST_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990036",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['PTN_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990035",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['BOOK_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990038",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
        }
        // DBアクセス事後処理
        unset($objQuery);
        return true;
    }
// Ansible Legacy Movementの登録状態を確認----
// ----Ansible Pioneer Movementの登録状態を確認
    function AnsiblePioneerMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
        $objMTS = $this->getMessageTemplateStorage();
        $objDBCA = $this->getDBConnectAgent();

        // 作業対象ホストの件数と作業対象ホストに紐づくマスタの登録状況までを確認
        $sql = sprintf(" SELECT                                                           " .
                       "   PHO_LINK_ID AS PKEY,                                           " .
                       "   COUNT(*) AS ROW_COUNT,                                         " .
                       "   OPERATION_NO_UAPK,                                             " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_OPERATION_LIST S_TBL                                     " .
                       "     WHERE                                                        " .
                       "       S_TBL.OPERATION_NO_UAPK = M_TBL.OPERATION_NO_UAPK AND      " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) OPE_COUNT,                                                   " .
                       "   PATTERN_ID,                                                    " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_PATTERN_PER_ORCH S_TBL                                   " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) PTN_COUNT,                                                   " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       B_ANSIBLE_PNS_PATTERN_LINK S_TBL                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) BOOK_COUNT,                                                  " .
                       "   SYSTEM_ID,                                                     " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_STM_LIST S_TBL                                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.SYSTEM_ID  = M_TBL.SYSTEM_ID AND                     " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) HOST_COUNT                                                   " .
                       " FROM                                                             " .
                       "   B_ANSIBLE_PNS_PHO_LINK M_TBL                                   " .
                       " WHERE                                                            " .
                       "   M_TBL.OPERATION_NO_UAPK  = %s    AND                           " .
                       "   M_TBL.PATTERN_ID         = %s    AND                           " .  
                       "   M_TBL.DISUSE_FLAG        = '0';                                ",
                       $intOperationNoUAPK,
                       $tgtSource_row['I_PATTERN_ID']);
        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }

        // 作業パターンID登録確認
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            unset($objQuery);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            // 作業対象ホストが未登録
            if($row['ROW_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990033",array($intFocusIndex));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['OPE_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990034",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['HOST_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990036",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['PTN_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990035",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['BOOK_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990038",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
        }
        // DBアクセス事後処理
        unset($objQuery);
        return true;
    }
// Ansible Pioneer Movementの登録状態を確認----
// ----Ansible Legacy Role Movementの登録状態を確認
    function AnsibleLegacyRoleMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
        $objMTS = $this->getMessageTemplateStorage();
        $objDBCA = $this->getDBConnectAgent();

        // 作業対象ホストの件数と作業対象ホストに紐づくマスタの登録状況までを確認
        $sql = sprintf(" SELECT                                                           " .
                       "   PHO_LINK_ID AS PKEY,                                           " .
                       "   COUNT(*) AS ROW_COUNT,                                         " .
                       "   OPERATION_NO_UAPK,                                             " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_OPERATION_LIST S_TBL                                     " .
                       "     WHERE                                                        " .
                       "       S_TBL.OPERATION_NO_UAPK = M_TBL.OPERATION_NO_UAPK AND      " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) OPE_COUNT,                                                   " .
                       "   PATTERN_ID,                                                    " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_PATTERN_PER_ORCH S_TBL                                   " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) PTN_COUNT,                                                   " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       B_ANSIBLE_LRL_PATTERN_LINK S_TBL                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.PATTERN_ID  = M_TBL.PATTERN_ID AND                   " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) BOOK_COUNT,                                                  " .
                       "   SYSTEM_ID,                                                     " .
                       "   (                                                              " .
                       "     SELECT                                                       " .
                       "       COUNT(*)                                                   " .
                       "     FROM                                                         " .
                       "       C_STM_LIST S_TBL                                           " .
                       "     WHERE                                                        " .
                       "       S_TBL.SYSTEM_ID  = M_TBL.SYSTEM_ID AND                     " .
                       "       S_TBL.DISUSE_FLAG = '0'                                    " .
                       "   ) HOST_COUNT                                                   " .
                       " FROM                                                             " .
                       "   B_ANSIBLE_LRL_PHO_LINK M_TBL                                   " .
                       " WHERE                                                            " .
                       "   M_TBL.OPERATION_NO_UAPK  = %s    AND                           " .
                       "   M_TBL.PATTERN_ID         = %s    AND                           " .  
                       "   M_TBL.DISUSE_FLAG        = '0';                                ",
                       $intOperationNoUAPK,
                       $tgtSource_row['I_PATTERN_ID']);
        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $sql;
            $aryFreeErrMsgBody[] = __FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError();
            unset($objQuery);
            return false;
        }

        // 作業パターンID登録確認
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            unset($objQuery);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            // 作業対象ホストが未登録
            if($row['ROW_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990033",array($intFocusIndex));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['OPE_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990034",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['HOST_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990036",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['PTN_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990035",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
            if($row['BOOK_COUNT'] == 0){
                $msg = $objMTS->getSomeMessage("ITABASEH-ERR-1990038",array($intFocusIndex,$row['PKEY']));
                $MovementErrorMsg = sprintf("%s\n%s",$MovementErrorMsg,$msg);
                continue;
            }
        }
        // DBアクセス事後処理
        unset($objQuery);
        return true;
    }
// Ansible Legacy Role Movementの登録状態を確認----
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    // ここまで固有定義関数----                                //
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
}
//ここまで個別オーケストレータ/シンフォニー用クラス定義----
?>
