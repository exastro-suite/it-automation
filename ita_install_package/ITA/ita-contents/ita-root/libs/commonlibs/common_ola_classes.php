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
    function getLivePatternFromMaster($aryOrchestratorId=null,$strSearchKeyValue="",$aryPatternId=null){
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

        $strPatternMasterDscRetryTimeout = 'DSC_RETRY_TIMEOUT';

        $boolBinaryDistinctOnDTiS = false; //false=あいまい

        $strPatternMasterAnsPlaybookHedDef    = 'ANS_PLAYBOOK_HED_DEF';
        $strPatternMasterAnsExecOption        = 'ANS_EXEC_OPTIONS';
        $strPatternMasterOpenst_Template      = 'OPENST_TEMPLATE';
        $strPatternMasterOpenst_Env           = 'OPENST_ENVIRONMENT';

        
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
                  .",{$strPatternMasterDscRetryTimeout} DSC_RETRY_TIMEOUT " 
                  .",{$strPatternMasterAnsPlaybookHedDef} ANS_PLAYBOOK_HED_DEF "
                  .",{$strPatternMasterAnsExecOption} ANS_EXEC_OPTIONS "
                  .",{$strPatternMasterOpenst_Template} OPENST_TEMPLATE "
                  .",{$strPatternMasterOpenst_Env} OPENST_ENVIRONMENT "
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
    
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    // ここまで固有定義関数----                                //
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
}
//ここまで個別オーケストレータ/シンフォニー用クラス定義----
?>
