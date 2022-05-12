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
        $strPatternMasterAccessAuth = 'ACCESS_AUTH';

        $strPatternMasterAnsHostDesignType = 'ANS_HOST_DESIGNATE_TYPE_ID';
        $strPatternMasterAnsWinRM = 'ANS_WINRM_ID';
        $strPatternMasterAnsParaEx = 'ANS_PARALLEL_EXE';

        $boolBinaryDistinctOnDTiS = false; //false=あいまい

        $strPatternMasterAnsPlaybookHedDef    = 'ANS_PLAYBOOK_HED_DEF';
        $strPatternMasterAnsExecOption        = 'ANS_EXEC_OPTIONS';
        $strPatternMasterOpenst_Template      = 'OPENST_TEMPLATE';
        $strPatternMasterOpenst_Env           = 'OPENST_ENVIRONMENT';
        $strPatternMasterAnsVirtualEnvName    = 'ANS_VIRTUALENV_NAME';
        $strPatternMasterAnsEngineVirtualEnvName  = 'ANS_ENGINE_VIRTUALENV_NAME';
        $strPatternMasterAnsExecutionEnvName      = "ANS_EXECUTION_ENVIRONMENT_NAME";
        $strPatternMasterAnsAnsibleConfFile       = "ANS_ANSIBLE_CONFIG_FILE";

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
                  .",{$strPatternMasterAccessAuth} ACCESS_AUTH "
                  .",{$strPatternMasterAnsHostDesignType} ANS_HOST_DESIGNATE_TYPE_ID "
                  .",{$strPatternMasterAnsParaEx} ANS_PARALLEL_EXE "
                  .",{$strPatternMasterAnsWinRM} ANS_WINRM_ID "
                  .",{$strPatternMasterAnsPlaybookHedDef} ANS_PLAYBOOK_HED_DEF "
                  .",{$strPatternMasterAnsExecOption} ANS_EXEC_OPTIONS "
                  .",{$strPatternMasterAnsVirtualEnvName} ANS_VIRTUALENV_NAME "
                  .",{$strPatternMasterAnsEngineVirtualEnvName} ANS_ENGINE_VIRTUALENV_NAME "
                  .",{$strPatternMasterAnsExecutionEnvName} ANS_EXECUTION_ENVIRONMENT_NAME"
                  .",{$strPatternMasterAnsAnsibleConfFile} ANS_ANSIBLE_CONFIG_FILE"
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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
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
            $objMTS = $this->getMessageTemplateStorage();
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

        // グローバル変数宣言
        global $g;

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
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "ACCESS_AUTH"=>"",
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
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
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

            // ----ムーブメントから、廃止されているレコードを除外する
            $aryMovement = array();
            foreach($aryRowOfMovClassTable as $aryDataForMovement){
                if( $aryDataForMovement['DISUSE_FLAG']=='0' ){
                    $aryMovement[] = $aryDataForMovement;
                }
            }
            // ムーブメントから、廃止されているレコードを除外する----

            // ----SymphonyとOperationと紐づくMovementに対してのアクセス権をチェックする
            $objRBAC = new RoleBasedAccessControl($objDBCA);

            //Symphonyのアクセス権をチェックする
            $symphonyClassNo = $aryRowOfSymClassTable['SYMPHONY_CLASS_NO'];
            $sql =  " SELECT * FROM C_SYMPHONY_CLASS_MNG "
                   ." WHERE SYMPHONY_CLASS_NO = $symphonyClassNo ";
            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $targetRow = $objQuery->resultFetch();
            if($targetRow == false){
                //例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $ret  = $objRBAC->getAccountInfo($userId);
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                //例外処理へ
                $strErrStepIdInFx="00000610";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission === false) {
                    $intErrorType = 103; //システムエラー判定
                    $strErrStepIdInFx="00000620";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }

            //Operationのアクセス権をチェック
            $sql =  " SELECT * FROM C_OPERATION_LIST "
                   ." WHERE OPERATION_NO_UAPK = $intOperationNoUAPK ";
            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $targetRow = $objQuery->resultFetch();
            if($targetRow == false){
                //例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $ret  = $objRBAC->getAccountInfo($userId);
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                //例外処理へ
                $strErrStepIdInFx="00000610";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission === false) {
                    $intErrorType = 103; //システムエラー判定
                    $strErrStepIdInFx="00000620";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }

            //Movementのアクセス権をチェックする
            if(!empty($aryMovement)){
                $intFocusIndex = 0;
                foreach($aryMovement as $movementData){
                    $patternId = $movementData['PATTERN_ID'];

                    //対象MovementをSELECT
                    $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                           ." WHERE PATTERN_ID = $patternId ";
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    $targetRow = $objQuery->resultFetch();
                    if($targetRow == false){
                        //例外処理へ
                        $strErrStepIdInFx="00000600";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }

                    $ret  = $objRBAC->getAccountInfo($userId);
                    list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                    if($ret === false) {
                        //例外処理へ
                        $strErrStepIdInFx="00000630";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    } else {
                        if($permission === false) {
                            $intErrorType = 2; //Movementの存在エラー判定
                            $strErrStepIdInFx="00000640";
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-1990037",array($intFocusIndex + 1));
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                    }
                    $intFocusIndex += 1;
                }
            }

            //Movementに個別指定したオペレーションのアクセス権をチェックする
            if(is_array($aryOptionOrder) == true){
                $intFocusIndex = 0;
                foreach($aryOptionOrder as $operationOrderData){
                    $skipFlag = $operationOrderData['EXE_SKIP_FLAG'];
                    $operationNo = $operationOrderData['OVRD_OPERATION_NO_IDBH'];
                    if($skipFlag == "" && $operationNo != ""){
                        $sql =  " SELECT * FROM C_OPERATION_LIST "
                               ." WHERE OPERATION_NO_UAPK = $operationNo ";
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        $r = $objQuery->sqlExecute();
                        $targetRow = $objQuery->resultFetch();
                        if($targetRow == false){
                            //例外処理へ
                            $strErrStepIdInFx="00000600";
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }

                        $ret  = $objRBAC->getAccountInfo($userId);
                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                        if($ret === false) {
                            //例外処理へ
                            $strErrStepIdInFx="00000650";
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        } else {
                            if($permission === false) {
                                $intErrorType = 2; //バリデーションエラー判定
                                $strErrStepIdInFx="00000660";
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733110",array($intFocusIndex + 1));
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            }
                        }
                    }
                    $intFocusIndex += 1;
                }
            }
            // SymphonyとOperationと紐づくMovementに対してのアクセス権をチェックする----

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


            //----Operation、Conductorの共通アクセス権の取得 #519
            $arrOpeConAccessAuth = $this->getInfoAccessAuthWorkFlowOpe($intShmphonyClassId,$intOperationNoUAPK ,"S" );

            if( $arrOpeConAccessAuth[3] != "" ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI =  $arrOpeConAccessAuth[3];
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $strOpeConAccessAuth = $arrOpeConAccessAuth[4];
            // Operation、Conductorの共通アクセス権の取得 #519----


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
            $register_tgt_row['PAUSE_STATUS_ID']       = 2;

            $register_tgt_row['ACCESS_AUTH']     = $aryRowOfSymClassTable['ACCESS_AUTH'];

            //上位アクセス権継承
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }else{
                $register_tgt_row['ACCESS_AUTH'] = $strOpeConAccessAuth;
            }

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

                $register_tgt_row['ACCESS_AUTH']     = $aryRowOfSymClassTable['ACCESS_AUTH'];

                //上位アクセス権継承
                if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                    $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
                }else{
                    $register_tgt_row['ACCESS_AUTH'] = $strOpeConAccessAuth;
                }

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
                
                //保留ステータス更新
                if($aryDataForMovement['NEXT_PENDING_FLAG'] == '1' && $register_tgt_row['RELEASED_FLAG'] == '1'){
                  $sql = "UPDATE C_SYMPHONY_INSTANCE_MNG SET PAUSE_STATUS_ID = '1' WHERE SYMPHONY_INSTANCE_NO = {$varSymphonyInstanceNo}";
                  
                  $tmpRetArray01 = singleSQLCoreExecute($objDBCA, $sql, array(), $strFxName);
                  if( $tmpRetArray01[0] !== true ){
                      // エラーフラグをON
                      // 例外処理へ
                      $strErrStepIdInFx="00001100";
                      //
                      throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                  }
                  unset($tmpRetArray01);
                }

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

//----ここからConductor用

//----ConductorIDおよびOperationNoからConductorインスタンスを新規登録する
    function registerConductorInstance($intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName,$intCallNo=0){
        // ----変数定義
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $intConductorInstanceId = null;
        $strExpectedErrMsgBodyForUI = "";
        $aryFreeErrMsgBody = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $boolInTransactionFlag = false;

        $arrayConfigForSymInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "I_NOTICE_INFO"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arraySymInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayConfigForMovInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
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
            #ACCESS_AUTH"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayMovInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
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
            // (ここから) ConductorとNodeのCUR/JNLの、シーケンスを取得する//
            ///////////////////////////////////////////////////////

            // ----TERMINAL-INSTANCE-シーケンスを掴む
            $retArray = getSequenceLockInTrz('C_NODE_TERMINALS_CLASS_MNG_JSQ','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $retArray = getSequenceLockInTrz('C_NODE_TERMINALS_CLASS_MNG_RIC','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // TERMINAL-INSTANCE-シーケンスを掴む----

            // ----NODE-INSTANCE-シーケンスを掴む
            $retArray = getSequenceLockInTrz('C_NODE_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $retArray = getSequenceLockInTrz('C_NODE_INSTANCE_MNG_RIC','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // NODE-INSTANCE-シーケンスを掴む----

            // ----SYM-INSTANCE-シーケンスを掴む
            $retArray = getSequenceLockInTrz('C_CONDUCTOR_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $retArray = getSequenceLockInTrz('C_CONDUCTOR_INSTANCE_MNG_RIC','A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // -SYM-INSTANCE-シーケンスを掴む----

            ////////////////////////////////////////////////////////
            // (ここまで) ConductorとNodeのCUR/JNLの、シーケンスを取得する//
            ///////////////////////////////////////////////////////

            //////////////////////////////////////////////////////
            // (ここから) Conductor、Node、Terminalの情報を登録する//
            /////////////////////////////////////////////////////

            $retArray = $this->registerInstanceConductorNode($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, "", $aryOptionOrderOverride, $userId, $userName,$intCallNo);

            if($retArray[0] == false){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                $intErrorType = $retArray[1];
                if( $retArray[1] < 500 ){
                    $aryErrMsgBody = $retArray[2];
                    $strErrMsg = $retArray[3];
                    $strSysErrMsgBody = $retArray[4];
                    $strExpectedErrMsgBodyForUI = $retArray[6];
                }
                //webError出力用メッセージを出力
                $aryFreeErrMsgBody = $retArray[7];
                foreach($aryFreeErrMsgBody as $msg){
                    web_log($msg);
                }

                if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //////////////////////////////////////////////////////
            // (ここまで) Conductor、Node、Terminalの情報を登録する//
            /////////////////////////////////////////////////////

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

            $intConductorInstanceId = $retArray[5];
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
                          $intConductorInstanceId,
                          $strExpectedErrMsgBodyForUI,
                          $aryFreeErrMsgBody,
                          );
        return $retArray;
    }
//ConductorIDおよびOperationNoからConductorインスタンスを新規登録する----

//----Conductorインスタンス、Nodeインスタンスを登録処理の読み出し
    function registerInstanceConductorNode($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName,$intCallNo=0){
        // ----変数定義
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $intConductorInstanceId = null;
        $strExpectedErrMsgBodyForUI = "";
        $aryFreeErrMsgBody = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $boolInTransactionFlag = false;

        $arrayConfigForSymInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
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
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
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
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
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
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
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

            $retArray = $this->conductorInstanceRegister($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, "", $aryOptionOrderOverride, $userId, $userName,$intCallNo);

            if($retArray[0] == false){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                $intErrorType = $retArray[1];
                if( $retArray[1] < 500 ){
                    $aryErrMsgBody = $retArray[2];
                    $strErrMsg = $retArray[3];
                    $strSysErrMsgBody = $retArray[4];
                    $strExpectedErrMsgBodyForUI = $retArray[6];
                }
                //webError出力用メッセージを出力
                $aryFreeErrMsgBody = $retArray[7];
                foreach($aryFreeErrMsgBody as $msg){
                    web_log($msg);
                }

                if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $intConductorInstanceId = $retArray[5];
            $retArray = $this->nodeInstanceRegister($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, "", $aryOptionOrderOverride, $userId, $userName,$intCallNo,$intConductorInstanceId);

            if($retArray[0] == false){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                $intErrorType = $retArray[1];
                if( $retArray[1] < 500 ){
                    $aryErrMsgBody = $retArray[2];
                    $strErrMsg = $retArray[3];
                    $strSysErrMsgBody = $retArray[4];
                    $strExpectedErrMsgBodyForUI = $retArray[6];
                }
                //webError出力用メッセージを出力
                $aryFreeErrMsgBody = $retArray[7];
                foreach($aryFreeErrMsgBody as $msg){
                    web_log($msg);
                }

                if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }


            $boolRet = true;

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
                          $intConductorInstanceId,
                          $strExpectedErrMsgBodyForUI,
                          $aryFreeErrMsgBody,
                          );

        return $retArray;
    }
// Conductorインスタンス、Nodeインスタンスを登録処理の読み出し----

//----Conductor　Conductorインスタンスの新規登録処理
    function conductorInstanceRegister($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName,$intCallNo=0){

        // グローバル変数宣言
        global $g;

        // ----変数定義
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $intConductorInstanceId = null;
        $strExpectedErrMsgBodyForUI = "";
        $aryFreeErrMsgBody = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $boolInTransactionFlag = false;

        $arrayConfigForSymInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "I_NOTICE_INFO"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arraySymInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "PAUSE_STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "I_NOTICE_INFO"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayConfigForMovInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "CONDUCTOR_INSTANCE_CALL_NO"=>"",
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
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayMovInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "CONDUCTOR_INSTANCE_CALL_NO"=>"",
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
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        // 変数定義----

        try{

            //////////////////////////////////////////////////////
            // (ここから) Conductor、Node、Operationの情報を取得する//
            /////////////////////////////////////////////////////
            // ---ConductorクラスIDからConductor部分、NODE部分の情報を取得する
            $aryRetBody = $this->getInfoFromOneOfConductorClass($intConductorClassId, 0);

            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                if( $aryRetBody[1] === 101 ){
                    //----該当のConductorClassIDが１行も発見できなかった場合
                    $intErrorType = 101;
                    //$strExpectedErrMsgBodyForUI = "ConductorクラスID：存在している必要があります。";
                    $strErrMsg = $aryRetBody[3];
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170008");
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    //該当のConductorClassIDが１行も発見できなかった場合----
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfSymClassTable = $aryRetBody[4];
            $aryRowOfMovClassTable = $aryRetBody[5];
            // ConductorクラスIDからConductor部分、Node部分の情報を取得する----

            // ----オペレーションNoからオペレーションの情報を取得する
            $arrayRetBody = $this->getInfoOfOneOperation($intOperationNoUAPK);
            if( $arrayRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                if( $arrayRetBody[1] === 101 ){
                    $intErrorType = 102;
                    //$strExpectedErrMsgBodyForUI = "オペレーションNO：存在している必要があります。";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfOperationTable = $arrayRetBody[4];
            // オペレーションNoからオペレーションの情報を取得する----
            //////////////////////////////////////////////////////
            // (ここまで) Conductor、Node、Operationの情報を取得する//
            /////////////////////////////////////////////////////


            /////////////////////////////////////
            // (ここから) Conductorインスタンスを登録する//
            /////////////////////////////////////
            //テーブル情報をセット
            $arrayConfigForIUD = $arrayConfigForSymInsIUD;
            $register_tgt_row = $arraySymInsValueTmpl;

            // ----シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_CONDUCTOR_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
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

            // ----Conductorインスタンス登録用の値をセット
            $varConductorInstanceNo = $varRISeq;
            $register_tgt_row['CONDUCTOR_INSTANCE_NO'] = $varConductorInstanceNo;
            $register_tgt_row['I_CONDUCTOR_CLASS_NO']  = $aryRowOfSymClassTable['CONDUCTOR_CLASS_NO'];
            $register_tgt_row['I_CONDUCTOR_NAME']      = $aryRowOfSymClassTable['CONDUCTOR_NAME'];
            $register_tgt_row['I_DESCRIPTION']         = $aryRowOfSymClassTable['DESCRIPTION'];
            $register_tgt_row['ACCESS_AUTH']           = $aryRowOfSymClassTable['ACCESS_AUTH'];
            $register_tgt_row['PAUSE_STATUS_ID']       = 2;

            #312
            $arrNoticeInfo = array();
            $tmpNoticeInfo = array();
            $strNoticeList = implode( ",", array_keys( json_decode($aryRowOfSymClassTable['NOTICE_INFO'],true)  ) );
            
            //通知情報取得
            $retArray = $this->getNoticeInfo($strNoticeList);
            if( $retArray[0] === true ){
                $arrNoticeRows = $retArray[4];
                foreach ( $arrNoticeRows as $arrNoticeRow ) {
                    $arrNoticeInfo[ $arrNoticeRow['NOTICE_ID'] ] = $arrNoticeRow['NOTICE_NAME'];
                } 
            }
            //通知対象ステータス取得
            $retArray = $this->getInfoOfNoticeStatusList();
            if( $retArray[0] === true ){
                $arrNoticeStatusRows = $retArray[4];
                foreach ( $arrNoticeStatusRows as $arrNoticeStatusRow ) {
                    $tmpNoticeInfo["STATUS_NAME"][ $arrNoticeStatusRow['SYM_EXE_STATUS_ID'] ] = $arrNoticeStatusRow['SYM_EXE_STATUS_NAME'];
                } 
            }
            $tmpNoticeInfo['NOTICE_INFO'] = json_decode($aryRowOfSymClassTable['NOTICE_INFO'],true);
            $tmpNoticeInfo['NOTICE_NAME'] = $arrNoticeInfo;

            $register_tgt_row['I_NOTICE_INFO'] = json_encode($tmpNoticeInfo,JSON_UNESCAPED_UNICODE);

            //上位アクセス権継承 
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }


            //----開始予約時刻が設定されていた場合

            if( strlen($strPreserveDatetime)==0 ){
                $varStatus = 1; //未実行
            }
            else{
                $varStatus = 2; //未実行(予約)
                $register_tgt_row['TIME_BOOK']            = $strPreserveDatetime;
            }
            //開始予約時刻が設定されていた場合----

            //----CONDUCTOR_CALLが設定されていた場合
            if( $intCallNo == 0 ){
                $register_tgt_row['CONDUCTOR_CALL_FLAG']   = 1; //デフォルト
            }else{
                $register_tgt_row['CONDUCTOR_CALL_FLAG']   = 2; //Symphon呼び出しフラグ(サブ)=[2]
                $register_tgt_row['CONDUCTOR_CALLER_NO']   = $intCallNo; //Symphon呼び出しフラグ(サブ)=[2]
            }
            //CONDUCTOR_CALLが設定されていた場合----

            $register_tgt_row['STATUS_ID']            = $varStatus; //未実行[1]または未実行(予約)[2]
            $register_tgt_row['PAUSE_STATUS_ID']      = 2; //保留ステータスオフ
            $register_tgt_row['EXECUTION_USER']       = $userName;
            $register_tgt_row['OPERATION_NO_UAPK']    = $intOperationNoUAPK;
            $register_tgt_row['I_OPERATION_NAME']     = $aryRowOfOperationTable['OPERATION_NAME'];
            $register_tgt_row['ABORT_EXECUTE_FLAG']   = 1; //緊急停止発令フラグ(未発令)=[1]
            $register_tgt_row['DISUSE_FLAG']          = '0';
            $register_tgt_row['LAST_UPDATE_USER']     = $userId;
            $tgtSource_row = $register_tgt_row;
            // Conductorインスタンス登録用の値をセット----

            // ---Conductorインスタンス登録用SQLを作成
            $sqlType = "INSERT";
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$sqlType
                                                ,"CONDUCTOR_INSTANCE_NO"
                                                ,"C_CONDUCTOR_INSTANCE_MNG"
                                                ,"C_CONDUCTOR_INSTANCE_MNG_JNL"
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
            // Conductorインスタンス登録用SQLを作成----

            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_CONDUCTOR_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
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

            // ----Conductorインスタンス登録の実行
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
            //Conductorインスタンス登録の実行----

            /////////////////////////////////////
            // (ここまで) Conductorインスタンスを登録する//
            /////////////////////////////////////

            /////////////////////////////////////
            // (ここから) NODEインスタンスを登録する//
            /////////////////////////////////////
            // ----NODEから、廃止されているレコードを除外する
            $aryMovement = array();
            foreach($aryRowOfMovClassTable as $aryDataForMovement){
                if( $aryDataForMovement['DISUSE_FLAG']=='0' ){
                    $aryMovement[] = $aryDataForMovement;
                }
            }
            // NODEから、廃止されているレコードを除外する----

            // Conductorインターフェース情報の登録データ確認する。
            $strQuery = "SELECT * FROM C_CONDUCTOR_IF_INFO WHERE DISUSE_FLAG = '0'";
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
                $IF_Errormsg= $objMTS->getSomeMessage("ITABASEH-ERR-170013");
            } else {
                if($objQuery->effectedRowCount() == 1) {
                    $row = $objQuery->resultFetch();
                    $tmpStrInterVal = $row['CONDUCTOR_REFRESH_INTERVAL'];
                    // データリレイストレージのパスを確認
                    if( !is_dir( $row['CONDUCTOR_STORAGE_PATH_ITA'] ) ) {
                        $IF_Errormsg = $objMTS->getSomeMessage("ITABASEH-ERR-170018");
                    }
                } else {
                    // 複数登録
                    $IF_Errormsg = $objMTS->getSomeMessage("ITABASEH-ERR-170014");
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
            // Nodeインスタンス登録処理後のチェック----

            /////////////////////////////////////
            // (ここまで) Nodeインスタンスを登録する//
            /////////////////////////////////////

            $boolRet = true;
            $intConductorInstanceId = $varConductorInstanceNo;
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
                          $intConductorInstanceId,
                          $strExpectedErrMsgBodyForUI,
                          $aryFreeErrMsgBody,
                          );

        return $retArray;
    }
// Conductor　Conductorインスタンスの新規登録処理----

//----Conductor　Nodeインスタンスの新規登録処理
    function nodeInstanceRegister($objDBCA, $lc_db_model_ch, $objMTS, $intConductorClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName,$intCallNo=0,$intConductorInstanceId){

        // グローバル変数宣言
        global $g;

        // ----変数定義
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strExpectedErrMsgBodyForUI = "";
        $aryFreeErrMsgBody = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $boolInTransactionFlag = false;

        $arrayConfigForSymInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arraySymInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "I_CONDUCTOR_CLASS_NO"=>"",
            "I_CONDUCTOR_NAME"=>"",
            "I_DESCRIPTION"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "STATUS_ID"=>"",
            "EXECUTION_USER"=>"",
            "ABORT_EXECUTE_FLAG"=>"",
            "CONDUCTOR_CALL_FLAG"=>"",
            "CONDUCTOR_CALLER_NO"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayConfigForMovInsIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "CONDUCTOR_INSTANCE_CALL_NO"=>"",
            "EXECUTION_NO"=>"",
            "STATUS_ID"=>"",
            "ABORT_RECEPTED_FLAG"=>"",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "RELEASED_FLAG"=>"",
            "EXE_SKIP_FLAG"=>"",
            "END_TYPE"=>"",
            "OVRD_OPERATION_NO_UAPK"=>"",
            "OVRD_I_OPERATION_NAME"=>"",
            "OVRD_I_OPERATION_NO_IDBH"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayMovInsValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "NODE_INSTANCE_NO"=>"",
            "I_NODE_CLASS_NO"=>"",
            "I_NODE_TYPE_ID"=>"",
            "I_ORCHESTRATOR_ID"=>"",
            "I_PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
            "I_ANS_WINRM_ID"=>"",
            #"I_MOVEMENT_SEQ"=>"",
            "I_NEXT_PENDING_FLAG"=>"",
            "I_DESCRIPTION"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "CONDUCTOR_INSTANCE_CALL_NO"=>"",
            "EXECUTION_NO"=>"",
            "STATUS_ID"=>"",
            "ABORT_RECEPTED_FLAG"=>"",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "RELEASED_FLAG"=>"",
            "EXE_SKIP_FLAG"=>"",
            "END_TYPE"=>"",
            "OVRD_OPERATION_NO_UAPK"=>"",
            "OVRD_I_OPERATION_NAME"=>"",
            "OVRD_I_OPERATION_NO_IDBH"=>"",
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        // 変数定義----

        try{

            //////////////////////////////////////////////////////
            // (ここから) Conductor、Node、Operationの情報を取得する//
            /////////////////////////////////////////////////////
            // ---ConductorクラスIDからConductor部分、NODE部分の情報を取得する
            $aryRetBody = $this->getInfoFromOneOfConductorClass($intConductorClassId, 0);


            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                if( $aryRetBody[1] === 101 ){
                    //----該当のConductorClassIDが１行も発見できなかった場合
                    $intErrorType = 101;
                    //$strExpectedErrMsgBodyForUI = "ConductorクラスID：存在している必要があります。";
                    $strErrMsg = $aryRetBody[3];
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170008");
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    //該当のConductorClassIDが１行も発見できなかった場合----
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfSymClassTable = $aryRetBody[4];
            $aryRowOfMovClassTable = $aryRetBody[5];
            // ConductorクラスIDからConductor部分、Node部分の情報を取得する----

            // ----オペレーションNoからオペレーションの情報を取得する
            $arrayRetBody = $this->getInfoOfOneOperation($intOperationNoUAPK);
            if( $arrayRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                if( $arrayRetBody[1] === 101 ){
                    $intErrorType = 102;
                    //$strExpectedErrMsgBodyForUI = "オペレーションNO：存在している必要があります。";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRowOfOperationTable = $arrayRetBody[4];
            // オペレーションNoからオペレーションの情報を取得する----
            //////////////////////////////////////////////////////
            // (ここまで) Conductor、Node、Operationの情報を取得する//
            /////////////////////////////////////////////////////


            /////////////////////////////////////
            // (ここから) NODEインスタンスを登録する//
            /////////////////////////////////////
            // ----NODEから、廃止されているレコードを除外する
            $aryMovement = array();
            foreach($aryRowOfMovClassTable as $aryDataForMovement){
                if( $aryDataForMovement['DISUSE_FLAG']=='0' ){
                    $aryMovement[] = $aryDataForMovement;
                }
            }
            // NODEから、廃止されているレコードを除外する----

            // ---- NODEインスタンス登録処理
            $NodeErrorMsg = "";
            $intFocusIndex = 0;

            foreach($aryMovement as $aryDataForMovement){

                //テーブル情報をセット
                $arrayConfigForIUD = $arrayConfigForMovInsIUD;
                $register_tgt_row = $arrayMovInsValueTmpl;

                // ----シーケンス払い出し
                $retArray = getSequenceValueFromTable('C_NODE_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
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

                // --- NODEインスタンス登録用の値をセット
                $register_tgt_row = array();
                $register_tgt_row['NODE_INSTANCE_NO'] = $varRISeq;
                $register_tgt_row['I_NODE_CLASS_NO']  = $aryDataForMovement['NODE_CLASS_NO'];
                $register_tgt_row['I_NODE_TYPE_ID']   = $aryDataForMovement['NODE_TYPE_ID'];
                $register_tgt_row['I_DESCRIPTION']    = $aryDataForMovement['DESCRIPTION'];
                $register_tgt_row['ACCESS_AUTH']      = $aryRowOfSymClassTable['ACCESS_AUTH'];

                //上位アクセス権継承
                if( isset( $g['__TOP_ACCESS_AUTH__']) === true ){
                    $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
                }


                //Movementの場合  [NODE_TYPE_ID(=3)の場合]
                if( $aryDataForMovement['NODE_TYPE_ID'] == 3){

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

                    $arySinglePatternSource = $aryMultiLivePatternFromMaster[$strPatternIdNumeric];
                    unset($aryMultiLivePatternFromMaster);

                    $register_tgt_row['I_PATTERN_ID']         = $strPatternIdNumeric;
                    $register_tgt_row['I_PATTERN_NAME']       = $arySinglePatternSource['PATTERN_NAME'];
                    $register_tgt_row['I_ANS_HOST_DESIGNATE_TYPE_ID'] = $arySinglePatternSource['ANS_HOST_DESIGNATE_TYPE_ID'];
                    $register_tgt_row['I_ANS_WINRM_ID'] = $arySinglePatternSource['ANS_WINRM_ID'];
                    $register_tgt_row['I_ORCHESTRATOR_ID']    = $aryDataForMovement['ORCHESTRATOR_ID'];
                    $register_tgt_row['I_NEXT_PENDING_FLAG']  = $aryDataForMovement['NEXT_PENDING_FLAG'];

                    if( $aryDataForMovement['SKIP_FLAG'] != 1 ){
                        $register_tgt_row['EXE_SKIP_FLAG']        = 1; //スキップしない
                    }
                    else{
                        $register_tgt_row['EXE_SKIP_FLAG']        = 2; //スキップする
                    }

                    //実行時、変更(SKIP、オペレーション個別指定)の上書き
                    if( isset( $aryOptionOrderOverride[ $aryDataForMovement['NODE_NAME'] ] ) ){
                        $aryNodesOverride = $aryOptionOrderOverride[ $aryDataForMovement['NODE_NAME'] ];

                        if( isset(  $aryNodesOverride['OPERATION_NO_IDBH'] ) || isset(  $aryNodesOverride['SKIP_FLAG'] ) ){

                            if( $aryNodesOverride['SKIP_FLAG'] == 1 ){
                                $register_tgt_row['EXE_SKIP_FLAG']          = 2;//スキップする
                            }else{
                                $register_tgt_row['EXE_SKIP_FLAG']          = 1;//スキップしない
                            }

                            if( $aryNodesOverride['OPERATION_NO_IDBH'] != "" ){
                                // ----オペレーションNo（個別指定）からオペレーションの情報を取得する
                                $tmparrayRetBody = $this->getInfoOfOneOperation($aryNodesOverride['OPERATION_NO_IDBH']);
                                if( $tmparrayRetBody[1] !== null ){
                                    // エラーフラグをON
                                    // 例外処理へ
                                    $strErrStepIdInFx="00000700";
                                    if( $tmparrayRetBody[1] === 101 ){
                                        $intErrorType = 102;
                                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
                                    }
                                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                }
                                $tmpOvrdOperation = $tmparrayRetBody[4];

                                $register_tgt_row['OVRD_OPERATION_NO_UAPK']      = $tmpOvrdOperation['OPERATION_NO_UAPK'];
                                $register_tgt_row['OVRD_I_OPERATION_NO_IDBH']      = $tmpOvrdOperation['OPERATION_NO_IDBH'];
                                $register_tgt_row['OVRD_I_OPERATION_NAME']      = $tmpOvrdOperation['OPERATION_NAME'];
                            }
                        }
                    }

                    $ret = $this->MovementValidator($register_tgt_row,$intOperationNoUAPK,$NodeErrorMsg,$register_tgt_row['I_PATTERN_ID'],$aryFreeErrMsgBody);
                    if( $ret === false ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00002400";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }

                }


                //Callの場合  [NODE_TYPE_ID(=4)の場合]
                if( $aryDataForMovement['NODE_TYPE_ID'] == 4 ||  $aryDataForMovement['NODE_TYPE_ID'] == 10){

                    if( $aryDataForMovement['SKIP_FLAG'] != 1 ){
                        $register_tgt_row['EXE_SKIP_FLAG']        = 1; //スキップしない
                    }
                    else{
                        $register_tgt_row['EXE_SKIP_FLAG']        = 2; //スキップする
                    }

                    $strCallname="";
                    if( $aryDataForMovement['NODE_TYPE_ID'] == 4 ){

                        $tmpRetBody = $this->getInfoFromOneOfConductorClass($aryDataForMovement['CONDUCTOR_CALL_CLASS_NO'],  0,0,1,1);

                        if( $tmpRetBody[1] !== null ){
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00000600";
                            if( $tmpRetBody[1] === 101 ){
                                //----該当のConductorClassIDが１行も発見できなかった場合
                                $intErrorType = 101;
                                //$strExpectedErrMsgBodyForUI = "ConductorクラスID：存在している必要があります。";
                                $strErrMsg = $tmpRetBody[3];
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170008");
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                //該当のConductorClassIDが１行も発見できなかった場合----
                            }
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                        $strCallname = $tmpRetBody[4]['CONDUCTOR_NAME'];

                    }elseif( $aryDataForMovement['NODE_TYPE_ID'] == 10 ){

                        $tmpRetBody = $this->getInfoFromOneOfSymphonyClasses($aryDataForMovement['CONDUCTOR_CALL_CLASS_NO'], 0);
                        if( $tmpRetBody[1] !== null ){
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00000600";
                            if( $aryRetBody[1] === 101 ){
                                //----該当のシンフォニーClassIDが１行も発見できなかった場合
                                $intErrorType = 101;
                                //$strExpectedErrMsgBodyForUI = "SymphonyクラスID：存在している必要があります。";
                                $strErrMsg = $tmpRetBody[3];
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733107");
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                //該当のシンフォニーClassIDが１行も発見できなかった場合----
                            }
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                        $aryRowOfSymClassTable = $tmpRetBody[4];
                        $strCallname = $tmpRetBody[4]['SYMPHONY_NAME'];
                    }
                    $register_tgt_row['I_PATTERN_NAME'] = $strCallname;


                    //実行時、変更(SKIP、オペレーション個別指定)の上書き
                    if( isset( $aryOptionOrderOverride[ $aryDataForMovement['NODE_NAME'] ] ) ){
                        $aryNodesOverride = $aryOptionOrderOverride[ $aryDataForMovement['NODE_NAME'] ];

                        if( isset(  $aryNodesOverride['OPERATION_NO_IDBH'] ) || isset(  $aryNodesOverride['SKIP_FLAG'] ) ){

                            if( $aryNodesOverride['SKIP_FLAG'] == 1 ){
                                $register_tgt_row['EXE_SKIP_FLAG']          = 2;//スキップする
                            }else{
                                $register_tgt_row['EXE_SKIP_FLAG']          = 1;//スキップしない
                            }

                            if( $aryNodesOverride['OPERATION_NO_IDBH'] != "" ){
                                // ----オペレーションNo（個別指定）からオペレーションの情報を取得する
                                $tmparrayRetBody = $this->getInfoOfOneOperation($aryNodesOverride['OPERATION_NO_IDBH']);
                                if( $tmparrayRetBody[1] !== null ){
                                    // エラーフラグをON
                                    // 例外処理へ
                                    $strErrStepIdInFx="00000700";
                                    if( $tmparrayRetBody[1] === 101 ){
                                        $intErrorType = 102;
                                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
                                    }
                                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                }
                                $tmpOvrdOperation = $tmparrayRetBody[4];

                                $register_tgt_row['OVRD_OPERATION_NO_UAPK']      = $tmpOvrdOperation['OPERATION_NO_UAPK'];
                                $register_tgt_row['OVRD_I_OPERATION_NO_IDBH']      = $tmpOvrdOperation['OPERATION_NO_IDBH'];
                                $register_tgt_row['OVRD_I_OPERATION_NAME']      = $tmpOvrdOperation['OPERATION_NAME'];
                            }
                        }
                    }
                }

                //pauseの場合  [NODE_TYPE_ID(=8)の場合]
                if( $aryDataForMovement['NODE_TYPE_ID'] == 8){
                    $register_tgt_row['RELEASED_FLAG']  = '1'; //1=未解除
                }

                //endの場合  [NODE_TYPE_ID(=2)の場合] #467
                if( $aryDataForMovement['NODE_TYPE_ID'] == 2){
                    if( isset( $aryDataForMovement['END_TYPE'] ) === true ){
                        $register_tgt_row['END_TYPE'] = $aryDataForMovement['END_TYPE'];
                        if( $register_tgt_row['END_TYPE'] == "" ){
                            $register_tgt_row['END_TYPE'] = "5";
                        }
                    }else{
                        $register_tgt_row['END_TYPE'] = "5";
                    }
                }

                //NODE共通パラメータ
                $register_tgt_row['ABORT_RECEPTED_FLAG']  = 1; //緊急停止受付確認フラグ=未確認[1]
                $register_tgt_row['CONDUCTOR_INSTANCE_NO'] = $intConductorInstanceId;
                $register_tgt_row['STATUS_ID']            = 1; //未実行[1]で
                $register_tgt_row['EXECUTION_USER']       = $userName;
                $register_tgt_row['DISUSE_FLAG']          = '0';
                $register_tgt_row['LAST_UPDATE_USER']     = $userId;
                // NODEインスタンス登録用の値をセット----

                $tgtSource_row = $register_tgt_row;

                // ----NODEインスタンス登録用SQLを作成
                $sqlType = "INSERT";
                $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                    ,$sqlType
                                                    ,"NODE_INSTANCE_NO"
                                                    ,"C_NODE_INSTANCE_MNG"
                                                    ,"C_NODE_INSTANCE_MNG_JNL"
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
                // NODEインスタンス登録用SQLを作成----


                // ----履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable('C_NODE_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
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

                // ----NODEインスタンス登録の実行
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
                // NODEインスタンス登録の実行----


            }
            //NODEインスタンス登録処理----

            // ----NODEインスタンス登録処理後のチェック
            // NODEの登録内容に不備がなかったことを確認
            if($NodeErrorMsg != ""){
                $strErrStepIdInFx="00002800";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $NodeErrorMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            /////////////////////////////////////
            // (ここまで) NODEインスタンスを登録する//
            /////////////////////////////////////

            $boolRet = true;

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
                          $intConductorInstanceId,
                          $strExpectedErrMsgBodyForUI,
                          $aryFreeErrMsgBody,
                          );

        return $retArray;
    }
//　Conductor　Nodeインスタンスの新規登録処理----

//----conductorクラス情報を取得する
    function getInfoOfOneConductor($intValueForSearchOneOpeRecord, $fxVarsIntMode=0,$getmode=""){
        /////////////////////////////////////////////////////////////
        // Conductor情報を取得                                //
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
                "CONDUCTOR_CLASS_NO"=>"",
                "CONDUCTOR_NAME"=>"",
                "DESCRIPTION"=>"",
                "NOTICE_INFO"=>"",
                "ACCESS_AUTH"=>"",
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
                "CONDUCTOR_CLASS_NO"=>"",
                "CONDUCTOR_NAME"=>"",
                "DESCRIPTION"=>"",
                "NOTICE_INFO"=>"",
                "ACCESS_AUTH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strWhereDisuseFlag = "('0', '1')";
            $strOrderByArea = "";
            if( $fxVarsIntMode === 1 ){
                //----更新用のため、ロック
                $strSelectMode = "SELECT FOR UPDATE";
                //更新用のため、ロック----
            }

            $temp_array = array('WHERE'=>"CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO AND DISUSE_FLAG IN {$strWhereDisuseFlag}");

            if( $getmode != "" ){
                //クラス編集時 (2100180003)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_EDIT_CLASS_MNG",
                    "node"          => "C_NODE_EDIT_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_EDIT_CLASS_MNG"
                );
            }else{
                //クラス状態保存 (2100180004)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_CLASS_MNG",
                    "node"          => "C_NODE_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_CLASS_MNG"
                );
            }
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"CONDUCTOR_CLASS_NO"
                                                ,$arrTableName['conductor']
                                                ,$arrTableName['conductor']."_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind['CONDUCTOR_CLASS_NO'] = $intValueForSearchOneOpeRecord;

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
//conductorクラス情報を取得する----

//----NODEクラス情報を取得する
    function getInfoOfOneNodeTerminal($intValueForSearchOneMovRecord, $fxVarsIntMode=0, $intSearchMode=0,$intTerminalInfo=0,$intTerminaltype=0,$getmode=""){
        /////////////////////////////////////////////////////////////
        // Node情報を取得                                //
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
                "JOURNAL_REG_DATETIME"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "NODE_CLASS_NO"=>"",
                "NODE_NAME"=>"",
                "NODE_TYPE_ID"=>"",
                "ORCHESTRATOR_ID"=>"",
                "PATTERN_ID"=>"",
                "CONDUCTOR_CALL_CLASS_NO"=>"",
                "DESCRIPTION"=>"",
                "CONDUCTOR_CLASS_NO"=>"",
                "OPERATION_NO_IDBH"=>"",
                "SKIP_FLAG"=>"",
                "NEXT_PENDING_FLAG"=>"",
                "POINT_X"=>"",
                "POINT_Y"=>"",
                "POINT_W"=>"",
                "POINT_H"=>"",
                "END_TYPE"=>"",
                "DISP_SEQ"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );

            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "NODE_CLASS_NO"=>"",
                "NODE_NAME"=>"",
                "NODE_TYPE_ID"=>"",
                "ORCHESTRATOR_ID"=>"",
                "PATTERN_ID"=>"",
                "CONDUCTOR_CALL_CLASS_NO"=>"",
                "DESCRIPTION"=>"",
                "CONDUCTOR_CLASS_NO"=>"",
                "OPERATION_NO_IDBH"=>"",
                "SKIP_FLAG"=>"",
                "NEXT_PENDING_FLAG"=>"",
                "POINT_X"=>"",
                "POINT_Y"=>"",
                "POINT_W"=>"",
                "POINT_H"=>"",
                "END_TYPE"=>"",
                "DISP_SEQ"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strWhereDisuseFlag = "('0')";
            $strOrderByArea = " ORDER BY NODE_CLASS_NO ASC";
            if( $fxVarsIntMode === 1 ){
                //----更新するため、廃止されているNodeレコードも拾う
                $strWhereDisuseFlag = "('0')";
                //更新するため、廃止されているNodeレコードも拾う----

                //----更新用のため、ロック
                $strSelectMode = "SELECT FOR UPDATE";
                //更新用のため、ロック----
            }elseif( $fxVarsIntMode === 2 ){
                //----インスタンスから参照用取得の為、廃止も拾う
                $strWhereDisuseFlag = "('0','1')";
                //インスタンスから参照用取得の為、廃止も拾う----

            }

            if( $getmode != "" ){
                //クラス編集時 (2100180003)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_EDIT_CLASS_MNG",
                    "node"          => "C_NODE_EDIT_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_EDIT_CLASS_MNG"
                );

                $arrayConfigForSelect["ACCESS_AUTH"]="";
                $arrayValue["ACCESS_AUTH"]="";

            }else{
                //クラス状態保存 (2100180004)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_CLASS_MNG",
                    "node"          => "C_NODE_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_CLASS_MNG"
                );
            }

            $temp_array = array('WHERE'=>"CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO AND DISUSE_FLAG IN {$strWhereDisuseFlag} {$strOrderByArea}");
            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"NODE_CLASS_NO"
                                                ,$arrTableName['node']
                                                ,$arrTableName['node']."_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind['CONDUCTOR_CLASS_NO'] = $intValueForSearchOneMovRecord;

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
                if($intTerminalInfo != 0){
                    $tmpArray=array();
                    $tmpArray=$this->getInfoOfOneTerminal($row['NODE_CLASS_NO'], $fxVarsIntMode, $intSearchMode,$intTerminaltype,$getmode);
                    $row['TERMINAL']=$tmpArray[4];
                }
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
//NODEクラス情報を取得する----

//----TERMINALクラス情報を取得する
    function getInfoOfOneTerminal($intValueForSearchOneMovRecord, $fxVarsIntMode=0, $intSearchMode=0,$intTerminaltype=0,$getmode=""){
        /////////////////////////////////////////////////////////////
        // Node情報を取得                                //
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
                "JOURNAL_REG_DATETIME"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "TERMINAL_CLASS_NO"=>"",
                "TERMINAL_CLASS_NAME"=>"",
                "TERMINAL_TYPE_ID"=>"",
                "NODE_CLASS_NO"=>"",
                "CONDUCTOR_CLASS_NO"=>"",
                "CONNECTED_NODE_NAME"=>"",
                "LINE_NAME"=>"",
                "TERMINAL_NAME"=>"",
                "CONDITIONAL_ID"=>"",
                "CASE_NO"=>"",
                "DESCRIPTION"=>"",
                "POINT_X"=>"",
                "POINT_Y"=>"",
                "DISP_SEQ"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );

            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "TERMINAL_CLASS_NO"=>"",
                "TERMINAL_CLASS_NAME"=>"",
                "TERMINAL_TYPE_ID"=>"",
                "NODE_CLASS_NO"=>"",
                "CONDUCTOR_CLASS_NO"=>"",
                "CONNECTED_NODE_NAME"=>"",
                "LINE_NAME"=>"",
                "TERMINAL_NAME"=>"",
                "CONDITIONAL_ID"=>"",
                "CASE_NO"=>"",
                "DESCRIPTION"=>"",
                "POINT_X"=>"",
                "POINT_Y"=>"",
                "DISP_SEQ"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strWhereDisuseFlag = "('0')";
            $strOrderByArea = " ORDER BY TERMINAL_CLASS_NO ASC";
            if( $fxVarsIntMode === 1 ){
                //----更新するため、廃止されているNodeレコードも拾う
                $strWhereDisuseFlag = "('0')";
                //更新するため、廃止されているNodeレコードも拾う----

                //----更新用のため、ロック
                $strSelectMode = "SELECT FOR UPDATE";
                //更新用のため、ロック----
            }elseif( $fxVarsIntMode === 2 ){
                //----インスタンスから参照用取得の為、廃止も拾う
                $strWhereDisuseFlag = "('0','1')";
                //インスタンスから参照用取得の為、廃止も拾う----

            }
            $strWhereTerminaltype = "('1','2')";
            if( $intTerminaltype == "1" )$strWhereTerminaltype = "('1')";
            if( $intTerminaltype == "2" )$strWhereTerminaltype = "('2')";

            if( $getmode != "" ){
                //クラス編集時 (2100180003)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_EDIT_CLASS_MNG",
                    "node"          => "C_NODE_EDIT_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_EDIT_CLASS_MNG"
                );

                $arrayConfigForSelect["ACCESS_AUTH"]="";
                $arrayValue["ACCESS_AUTH"]="";

            }else{
                //クラス状態保存 (2100180004)
                $arrTableName=array(
                    "conductor"     => "C_CONDUCTOR_CLASS_MNG",
                    "node"          => "C_NODE_CLASS_MNG",
                    "terminal"      => "C_NODE_TERMINALS_CLASS_MNG"
                );
            }

            $temp_array = array('WHERE'=>"NODE_CLASS_NO = :NODE_CLASS_NO AND TERMINAL_TYPE_ID IN {$strWhereTerminaltype} AND DISUSE_FLAG IN {$strWhereDisuseFlag} {$strOrderByArea}");

            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"TERMINAL_CLASS_NO"
                                                ,$arrTableName['terminal']
                                                ,$arrTableName['terminal']."_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind['NODE_CLASS_NO'] = $intValueForSearchOneMovRecord;

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
//TERMINALクラス情報を取得する----

//---Conductorクラス、NODE、TERMINAL情報を取得する
    function getInfoFromOneOfConductorClass($fxVarsIntConductorClassId, $fxVarsIntMode=0 , $intSearchMode=0,$intTerminalInfo=0,$getmode=""){
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
            $aryRetBody = $this->getInfoOfOneConductor($fxVarsIntConductorClassId, $fxVarsIntMode,$getmode);
            $objMTS = $this->getMessageTemplateStorage();

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

            $aryRetBody = $this->getInfoOfOneNodeTerminal($fxVarsIntConductorClassId, $fxVarsIntMode, $intSearchMode,$intTerminalInfo,0,$getmode);

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
//Conductorクラス、NODE、TERMINAL情報を取得する----

//Conductorクラス情報の整形＋JSON形式へ----
    function convertConductorClassJson($intConductorClassId,$getmode=""){

        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        //Conductorクラス情報取得
        $aryRetBody = $this->getInfoFromOneOfConductorClass($intConductorClassId, 0,0,1,$getmode);#TERMINALあり

        if( $aryRetBody[1] !== null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $boolRet = true;
        $arrConductorData = $aryRetBody[4];
        $arrNodeData = $aryRetBody[5];

        //----作業パターンの収集

        $aryRetBody = $this->getLivePatternFromMaster();
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryPatternList = array();
        foreach ($aryRetBody[0] as $key => $value) {
            $aryPatternList[$value['PATTERN_ID']]=$value;
        }
        //作業パターンの収集----


        $arr_json=array();
        $arr_json['config']['editorVersion']="1.0.2";
        $arr_json['conductor']['conductor_name']=$arrConductorData['CONDUCTOR_NAME'];
        $arr_json['conductor']['note']=$arrConductorData['DESCRIPTION'];
        $arr_json['conductor']['id']=$intConductorClassId;
        $arr_json['conductor']['LUT4U']=$arrConductorData['LUT4U'];

        $arr_json['conductor']['ACCESS_AUTH'] = "";
        if( isset( $arrConductorData['ACCESS_AUTH'] )  == true ){
            $arr_json['conductor']['ACCESS_AUTH']=$arrConductorData['ACCESS_AUTH'];
        }

        #312
        $arr_json['conductor']['NOTICE_INFO'] = array();
        if( isset( $arrConductorData['NOTICE_INFO'] )  == true ){
            $arr_json['conductor']['NOTICE_INFO']= json_decode( $arrConductorData['NOTICE_INFO'] , true );
        }

        $intNodeNumber=0;
        $intTerminalNumber=0;
        $intEdgeNumber=0;

        //NODE2成形
        foreach ($arrNodeData as $key => $value) {

            $arr_json[$value['NODE_NAME']]['h']=$value['POINT_H'];
            $arr_json[$value['NODE_NAME']]['id']=$value['NODE_NAME'];
            $arr_json[$value['NODE_NAME']]['terminal']=array();
            //NODE_TYPE置換
            if( $value['NODE_TYPE_ID'] == 1) $arr_json[$value['NODE_NAME']]['type']="start";
            if( $value['NODE_TYPE_ID'] == 2) $arr_json[$value['NODE_NAME']]['type']="end";
            if( $value['NODE_TYPE_ID'] == 3) $arr_json[$value['NODE_NAME']]['type']="movement";
            if( $value['NODE_TYPE_ID'] == 4) $arr_json[$value['NODE_NAME']]['type']="call";
            if( $value['NODE_TYPE_ID'] == 5) $arr_json[$value['NODE_NAME']]['type']="parallel-branch";
            if( $value['NODE_TYPE_ID'] == 6) $arr_json[$value['NODE_NAME']]['type']="conditional-branch";
            if( $value['NODE_TYPE_ID'] == 7) $arr_json[$value['NODE_NAME']]['type']="merge";
            if( $value['NODE_TYPE_ID'] == 8) $arr_json[$value['NODE_NAME']]['type']="pause";
            if( $value['NODE_TYPE_ID'] == 9) $arr_json[$value['NODE_NAME']]['type']="blank";
            if( $value['NODE_TYPE_ID'] == 10) $arr_json[$value['NODE_NAME']]['type']="call_s";

            //#587
            if( $value['NODE_TYPE_ID'] == 11) $arr_json[$value['NODE_NAME']]['type']="status-file-branch";

            //END個別 #467
            if( $value['NODE_TYPE_ID'] == 2) {
                if( isset($value['END_TYPE']) === true ){
                    $arr_json[$value['NODE_NAME']]['END_TYPE']=$value['END_TYPE'];
                }else{
                    $arr_json[$value['NODE_NAME']]['END_TYPE']="5";
                }
            }

            //Movement個別
            if( $value['NODE_TYPE_ID'] == 3) {
                if( isset( $aryPatternList[$value['PATTERN_ID']] ) ){
                    $arr_json[$value['NODE_NAME']]['PATTERN_ID']=$value['PATTERN_ID'];
                    $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']=$value['ORCHESTRATOR_ID'];
                    $arr_json[$value['NODE_NAME']]['Name']=$aryPatternList[$value['PATTERN_ID']]['PATTERN_NAME'];
                }else{
                    if( $getmode == "" ){
                        $arr_json[$value['NODE_NAME']]['PATTERN_ID']=$value['PATTERN_ID'];
                        $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']=$value['ORCHESTRATOR_ID'];
                        $arr_json[$value['NODE_NAME']]['Name']="";
                    }else{
                        //廃止済みMovemnt対応
                        $arr_json[$value['NODE_NAME']]['PATTERN_ID']=$value['PATTERN_ID'];#"-";
                        $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']="-";
                        $arr_json[$value['NODE_NAME']]['Name']="-";
                    }

                }
            }

            //call個別
            if( $value['NODE_TYPE_ID'] == 4) {
                $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];

                $strConductorName="";
                if( $value['CONDUCTOR_CALL_CLASS_NO'] != "" ){
                    //Conductorクラス情報取得
                    $aryRetBody = $this->getInfoFromOneOfConductorClass($value['CONDUCTOR_CALL_CLASS_NO'], 0,0,1,$getmode);#TERMINALあり

                    if( $aryRetBody[1] !== null ){
                        //廃止済みの場合
                        $strConductorName = "";
                        $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];#"---";
                    }else{
                        if($aryRetBody[4]['DISUSE_FLAG'] == 1){
                            //廃止済みの場合
                            $strConductorName = "";
                            $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];#"---";
                        }else{
                            $strConductorName = $aryRetBody[4]['CONDUCTOR_NAME'];
                        }
                    }
                }
                $arr_json[$value['NODE_NAME']]['CONDUCTOR_NAME']=$strConductorName;
            }

            //call(symphony)個別
            if( $value['NODE_TYPE_ID'] == 10) {
                #$arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];
                $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];

                $strConductorName="";
                if( $value['CONDUCTOR_CALL_CLASS_NO'] != "" ){
                    //Symphonyクラス情報取得
                    $aryRetBody = $this->getInfoFromOneOfSymphonyClasses($value['CONDUCTOR_CALL_CLASS_NO'], 0);

                    if( $aryRetBody[1] !== null ){
                        //廃止済みの場合
                        $strConductorName = "";
                        #$arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']="---";
                        #$arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']="---";
                    }else{
                        $strConductorName = $aryRetBody[4]['SYMPHONY_NAME'];
                    }
                }
                $arr_json[$value['NODE_NAME']]['SYMPHONY_NAME']=$strConductorName;
            }

            //#648 対応
            $objDBCA = $this->getDBConnectAgent();

            $sql = "SELECT * FROM C_CONDUCTOR_INSTANCE_MNG
                    WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']}
                    AND STATUS_ID NOT IN (1,2,3,4)
                    AND DISUSE_FLAG = 0
                    ";

            //SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            //SQL発行
            $r = $objQuery->sqlExecute();
            $arrEndIns=array();
            while ( $row = $objQuery->resultFetch() ){
                $arrEndIns = $row;
            }

            if( count($arrEndIns) != 0 ){
                if( $value['NODE_TYPE_ID'] == 4 || $value['NODE_TYPE_ID'] == 10) {
                    $rows=array();

                    if( $getmode == ""){
                        if( $value['NODE_TYPE_ID'] == 4  ){
                            $sql = "SELECT * FROM C_NODE_INSTANCE_MNG TAB_A
                                    LEFT JOIN C_NODE_CLASS_MNG TAB_B ON TAB_A.I_NODE_CLASS_NO = TAB_B.NODE_CLASS_NO
                                    LEFT JOIN C_CONDUCTOR_INSTANCE_MNG TAB_C ON TAB_B.CONDUCTOR_CLASS_NO = TAB_C.CONDUCTOR_CALLER_NO
                                    WHERE TAB_A.CONDUCTOR_INSTANCE_NO IN
                                    (SELECT CONDUCTOR_INSTANCE_NO FROM C_CONDUCTOR_INSTANCE_MNG WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']} )
                                    AND I_NODE_TYPE_ID IN ( {$value['NODE_TYPE_ID']} )
                                    AND TAB_A.DISUSE_FLAG = 0
                                    ";
                        }

                        if( $value['NODE_TYPE_ID'] == 10  ){
                        $sql = "SELECT * FROM C_NODE_INSTANCE_MNG TAB_A
                                LEFT JOIN C_NODE_CLASS_MNG TAB_B ON TAB_A.I_NODE_CLASS_NO = TAB_B.NODE_CLASS_NO
                                LEFT JOIN C_SYMPHONY_INSTANCE_MNG TAB_C ON TAB_A.CONDUCTOR_INSTANCE_CALL_NO = TAB_C.SYMPHONY_INSTANCE_NO
                                WHERE TAB_A.CONDUCTOR_INSTANCE_NO IN
                                (SELECT CONDUCTOR_INSTANCE_NO FROM C_CONDUCTOR_INSTANCE_MNG WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']} )
                                AND I_NODE_TYPE_ID IN ( {$value['NODE_TYPE_ID']} )
                                AND TAB_A.DISUSE_FLAG = 0
                                ";
                        }

                        //SQL準備
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        //SQL発行
                        $r = $objQuery->sqlExecute();

                        while ( $row = $objQuery->resultFetch() ){
                            $rows[$row['NODE_CLASS_NO']] = $row;
                        }

                        if( $value['NODE_TYPE_ID'] == 4 && isset( $rows[$value['NODE_CLASS_NO']] ) ==  true ){
                            $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$rows[$value['NODE_CLASS_NO']]['CONDUCTOR_INSTANCE_CALL_NO'];
                            //Conductorインスタンス無し場合
                            if( $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID'] == "" ){
                                $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];
                            }

                            $arr_json[$value['NODE_NAME']]['CONDUCTOR_NAME']=$rows[$value['NODE_CLASS_NO']]['I_PATTERN_NAME'];
                        }

                        if( $value['NODE_TYPE_ID'] == 10 && isset( $rows[$value['NODE_CLASS_NO']] ) ==  true ){
                            $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']=$rows[$value['NODE_CLASS_NO']]['CONDUCTOR_INSTANCE_CALL_NO'];

                            //Symphonyインスタンス無し場合
                            if( $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID'] == "" ){
                                $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];
                            }

                            $arr_json[$value['NODE_NAME']]['SYMPHONY_NAME']=$rows[$value['NODE_CLASS_NO']]['I_PATTERN_NAME'];
                        }
                    }
                }
            }

            //Movement,call,call_s共通
            if( $value['NODE_TYPE_ID'] == 3 || $value['NODE_TYPE_ID'] == 4 || $value['NODE_TYPE_ID'] == 10 ) {
                $arr_json[$value['NODE_NAME']]['OPERATION_NO_IDBH']=$value['OPERATION_NO_IDBH'];
                $arr_json[$value['NODE_NAME']]['SKIP_FLAG']=$value['SKIP_FLAG'];

                $strOpeName="";
                if( $value['OPERATION_NO_IDBH'] != "" ){
                    // ----オペレーションNoからオペレーションの情報を取得する
                    $arrayRetBody = $this->getInfoOfOneOperation( $value['OPERATION_NO_IDBH'] );
                    if( $arrayRetBody[1] !== null ){
                        //廃止済みの場合
                        $strOpeName = "";
                        $arr_json[$value['NODE_NAME']]['OPERATION_NO_IDBH']=$value['OPERATION_NO_IDBH'];#"-";
                    }else{
                        // オペレーションNoからオペレーションの情報を取得する----
                        $aryRowOfOperationTable = $arrayRetBody[4];
                        $strOpeName = $aryRowOfOperationTable['OPERATION_NAME'];
                    }

                }

                $arr_json[$value['NODE_NAME']]['OPERATION_NAME']=$strOpeName;
            }

            $arr_json[$value['NODE_NAME']]['note']=$value['DESCRIPTION'];

            $arr_json[$value['NODE_NAME']]['w']=$value['POINT_W'];
            $arr_json[$value['NODE_NAME']]['x']=$value['POINT_X'];
            $arr_json[$value['NODE_NAME']]['y']=$value['POINT_Y'];

            //NODEカウンタの取得
            $tmpNodeNumber  = intval( str_replace( "node-", "", $value['NODE_NAME'] ));
            if( $intNodeNumber < $tmpNodeNumber )$intNodeNumber=$tmpNodeNumber;

            //TERMINAL整形
            foreach ($value['TERMINAL'] as $tkey => $tval) {

                if( $tval['CASE_NO'] != "" ){
                    $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['case']=$tval['CASE_NO'];

                    //#587
                    if( $tval['CASE_NO'] == "0" && $value['NODE_TYPE_ID'] == 11 ){
                        $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['case'] = "else";
                    }

                }
                if($tval['LINE_NAME'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['edge']=$tval['LINE_NAME'];
                $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['id']=$tval['TERMINAL_CLASS_NAME'];
                $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['targetNode']=$tval['CONNECTED_NODE_NAME'];
                if( $tval['TERMINAL_TYPE_ID'] == 1) $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['type']="in";
                if( $tval['TERMINAL_TYPE_ID'] == 2) $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['type']="out";

                if($tval['CONDITIONAL_ID'] != null ){
                    $arrConditionalID = explode(',', $tval['CONDITIONAL_ID']);
                    foreach ($arrConditionalID as $tckey => $tcvalue) {
                        $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['condition'][]=$tcvalue;
                    }

                }

                if($tval['POINT_X'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['x']=$tval['POINT_X'];
                if($tval['POINT_Y'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['y']=$tval['POINT_Y'];

                //LINE生成
                if( $tval['TERMINAL_TYPE_ID'] == "1"  && $tval['LINE_NAME'] != "" ){
                    $arr_json[$tval['LINE_NAME']]['type']="egde";
                    $arr_json[$tval['LINE_NAME']]['id']=$tval['LINE_NAME'];

                    $arr_json[$tval['LINE_NAME']]['inTerminal']=$tval['TERMINAL_CLASS_NAME'];
                    $arr_json[$tval['LINE_NAME']]['outNode']=$tval['CONNECTED_NODE_NAME'];

                }elseif($tval['TERMINAL_TYPE_ID'] == "2"  && $tval['LINE_NAME'] != "" ){
                    $arr_json[$tval['LINE_NAME']]['inNode']=$tval['CONNECTED_NODE_NAME'];
                    $arr_json[$tval['LINE_NAME']]['outTerminal']=$tval['TERMINAL_CLASS_NAME'];
                }
                ksort($arr_json[$tval['LINE_NAME']]);


            //TERMINAL、LINEカウンタの取得
            $tmpTerminalNumber  = intval( str_replace( "terminal-", "", $tval['TERMINAL_CLASS_NAME'] ));
            if( $intTerminalNumber < $tmpTerminalNumber )$intTerminalNumber=$tmpTerminalNumber;
            $tmpEdgeNumber  = intval( str_replace( "line-", "", $tval['LINE_NAME'] ));
            if( $intEdgeNumber < $tmpEdgeNumber )$intEdgeNumber=$tmpEdgeNumber;

            }
        }

        $intNodeNumber++;
        $intTerminalNumber++;
        $intEdgeNumber++;

        $arr_json['config']['nodeNumber']=$intNodeNumber;
        $arr_json['config']['terminalNumber']=$intTerminalNumber;
        $arr_json['config']['edgeNumber']=$intEdgeNumber;

        ksort($arr_json);

        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$arr_json,json_encode($arr_json,JSON_UNESCAPED_UNICODE));
        return $retArray;
    }
//Conductorクラス情報の整形＋JSON形式へ----

//NODEインスタンスの情報取得----
    function getConductorStatusFromNode($aryNodeInstanceOfSingleConductor){
        ////////////////////////////////////////////////////////////////
        // Nodeインスタンスから、Conductor関連の情報を取得 //
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

            // 楽章の数を取得
            $intMovementLength = count($aryNodeInstanceOfSingleConductor);

            foreach($aryNodeInstanceOfSingleConductor as $rowOfMovement ){

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
                    case "15": // 警告終了
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

                if( count($aryRunningMovement)>= 1 ){
                    //----現在のNodeが、実行中系だった場合
                    $rowOfFocusMovement = $aryRunningMovement[0];
                    //現在のNodeが、実行中系だった場合----
                }
                else if(count( $aryRunningMovement)== 0 ){
                    //----現在のNodeが、実行中系ではなかった場合
                    if( 0 < $intAbortedMovementLength ){
                        //----中断されていた場合
                        $rowOfFocusMovement = $aryAbortedMovement[$intAbortedMovementLength - 1];
                        //中断されていた場合----
                    }
                    else{
                        //----正常終了系だった場合
                        $rowOfFocusMovement = $rowEndedMovement[$intEndedMovementLength - 1];
                        //正常終了系だった場合----
                    }
                    //現在のNodeが、実行中系ではなかった場合----
                }

                $intFocusMovementSeq = intval($rowOfFocusMovement['NODE_INSTANCE_NO']);

                //すでに1個は楽章がはじまった後である場合----
            }
            $aryStatusInfo = array('NODE_LENGTH'=>$intMovementLength
                                  ,'FOCUS_NODE_SEQ'=>$intFocusMovementSeq
                                  ,'FOCUS_NODE_ROW'=>$rowOfFocusMovement
                                  ,'RUNS_NODE'=>$aryRunningMovement
                                   ,'ERR_NODE'=>$aryAbortedMovement
                              );
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($aryStatusInfo,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    }
//----NODEインスタンスの情報取得

//----オペレーション一覧を取得する
    function getInfoOfOperationList(){
        /////////////////////////////////////////////////////////////
        // オペレーション情報を取得                                //
        /////////////////////////////////////////////////////////////
        // グローバル変数宣言
        global $g;

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
            $obj = new RoleBasedAccessControl($objDBCA);

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
                "ACCESS_AUTH"=>"",
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
                "ACCESS_AUTH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strSelectForUpdateLock = "";

            $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0') {$strSelectForUpdateLock}");

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
            $rows = array();
            while ( $row = $objQueryUtn->resultFetch() ){

                $user_id = $g['login_id'];
                $ret  = $obj->getAccountInfo($user_id);
                list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                if($ret === false) {
                } else {
                    if($permission === true) {
                        $rows[] = $row;
                    }
                }
            }
            //発見行だけループ----

            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//オペレーション一覧を取得する----

//----conductor一覧を取得する
    function getInfoOfCocductorList(){
        /////////////////////////////////////////////////////////////
        // conductor一覧を取得                                //
        /////////////////////////////////////////////////////////////

        // グローバル変数宣言
        global $g;

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
            $obj = new RoleBasedAccessControl($objDBCA);

            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($lc_db_model_ch,"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
            $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";

            // ----全行および全行中、最後に更新された日時を取得する
            $arrayConfigForSelect = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "CONDUCTOR_CLASS_NO"=>"",
                "CONDUCTOR_NAME"=>"",
                "DESCRIPTION"=>"",
                "ACCESS_AUTH"=>"",
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
                "CONDUCTOR_CLASS_NO"=>"",
                "CONDUCTOR_NAME"=>"",
                "DESCRIPTION"=>"",
                "ACCESS_AUTH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strSelectForUpdateLock = "";

            $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0') {$strSelectForUpdateLock}");

            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$strSelectMode
                                                ,"CONDUCTOR_CLASS_NO"
                                                ,"C_CONDUCTOR_EDIT_CLASS_MNG"
                                                ,"C_CONDUCTOR_EDIT_CLASS_MNG_JNL"
                                                ,$arrayConfigForSelect
                                                ,$arrayValue
                                                ,$temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

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
            $rows = array();
            while ( $row = $objQueryUtn->resultFetch() ){

                $user_id = $g['login_id'];
                $ret  = $obj->getAccountInfo($user_id);
                list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                if($ret === false) {
                } else {
                    if($permission === true) {
                        $rows[] = $row;
                    }
                }
            }
            //発見行だけループ----

            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//conductor一覧を取得する----



//----ある１のConductorの定義を新規登録（追加）する
function conductorClassRegister($fxVarsIntConductorClassId ,$fxVarsAryReceptData, $fxVarsStrSortedData, $fxVarsStrLT4UBody,$getmode=""){

    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "000";
    $intConductorClassId = '';
    $strExpectedErrMsgBodyForUI = "";

    $intControlDebugLevel01=250;

    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];

    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();

    $strFxName = '([FUNCTION]'.__FUNCTION__.')';

    $aryConfigForSymClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONDUCTOR_NAME"=>"",
        "DESCRIPTION"=>"",
        "NOTICE_INFO"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arySymClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONDUCTOR_NAME"=>"",
        "DESCRIPTION"=>"",
        "NOTICE_INFO"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayConfigForNodeClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "END_TYPE"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $aryNodeClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "END_TYPE"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );


    $arrayConfigForTermClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "TERMINAL_CLASS_NO"=>"",
        "TERMINAL_CLASS_NAME"=>"",
        "TERMINAL_TYPE_ID"=>"",
        "NODE_CLASS_NO"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONNECTED_NODE_NAME"=>"",
        "LINE_NAME"=>"",
        "TERMINAL_NAME"=>"",
        "CONDITIONAL_ID"=>"",
        "CASE_NO"=>"",
        "DESCRIPTION"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $aryTermClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "TERMINAL_CLASS_NO"=>"",
        "TERMINAL_CLASS_NAME"=>"",
        "TERMINAL_TYPE_ID"=>"",
        "NODE_CLASS_NO"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONNECTED_NODE_NAME"=>"",
        "LINE_NAME"=>"",
        "TERMINAL_NAME"=>"",
        "CONDITIONAL_ID"=>"",
        "CASE_NO"=>"",
        "DESCRIPTION"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;

    #$getmode= 1;
    //Conductor対象テーブル先
    if( $getmode != "" ){
        //クラス編集時 (2100180003)
        $arrTableName=array(
            "conductor"     => "C_CONDUCTOR_EDIT_CLASS_MNG",
            "node"          => "C_NODE_EDIT_CLASS_MNG",
            "terminal"      => "C_NODE_TERMINALS_EDIT_CLASS_MNG"
        );

        $arrayConfigForNodeClassIUD["ACCESS_AUTH"]="";
        $aryNodeClassValueTmpl["ACCESS_AUTH"]="";
        $arrayConfigForTermClassIUD["ACCESS_AUTH"]="";
        $aryTermClassValueTmpl["ACCESS_AUTH"]="";

    }else{
        //クラス状態保存 (2100180004)
        $arrTableName=array(
            "conductor"     => "C_CONDUCTOR_CLASS_MNG",
            "node"          => "C_NODE_CLASS_MNG",
            "terminal"      => "C_NODE_TERMINALS_CLASS_MNG"
        );
    }

    try{

        $objDBCA = $this->getDBConnectAgent();
        $lc_db_model_ch = $objDBCA->getModelChannel();

        #Conductor-nodeパラメータ整形
        $aryExecuteData = $fxVarsAryReceptData;
        $aryNodeData = $this->nodeDateDecodeForedit($fxVarsStrSortedData);
        #'start','end','movement','call','parallel-branch','conditional-branch','merge','pause','blank'

        $boolInTransactionFlag = true;



        // ---CONCUCTOR、NODE、TERMINALクラスのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））

        // ----NODE-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz($arrTableName['node'].'_JSQ','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        $retArray = getSequenceLockInTrz($arrTableName['node'].'_RIC','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        // ----TERMINAL-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz($arrTableName['terminal'].'_JSQ','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        $retArray = getSequenceLockInTrz($arrTableName['terminal'].'_RIC','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        // ----SYM-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz($arrTableName['conductor'].'_JSQ','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000900";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        $retArray = getSequenceLockInTrz($arrTableName['conductor'].'_RIC','A_SEQUENCE');

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-CLASS-シーケンスを掴む----

        //----CONCUCTOR、NODE、TERMINALクラスのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））----

        // ----Conductorを登録

        if( $fxVarsIntConductorClassId == "" ){

            $register_tgt_row = $arySymClassValueTmpl;

            $retArray = getSequenceValueFromTable($arrTableName['conductor'].'_RIC', 'A_SEQUENCE', FALSE );

            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001100";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varRISeq = $retArray[0];
            }

            $varConductorClassNo = $varRISeq;
            $register_tgt_row['CONDUCTOR_CLASS_NO'] = $varRISeq;
            $register_tgt_row['CONDUCTOR_NAME']     = $aryExecuteData['conductor_name'];
            $register_tgt_row['DESCRIPTION'] = "";
            if(isset($aryExecuteData['note'])){
              $register_tgt_row['DESCRIPTION']       = $aryExecuteData['note'];
            }
            $register_tgt_row['DISUSE_FLAG']       = '0';
            $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

            #312 
            $register_tgt_row['NOTICE_INFO'] = "";
            if(isset($aryExecuteData['NOTICE_INFO'])){
                //通知チェック,廃止除外
                $retArray = $this->getNoticeInfo( implode( ",", array_keys($aryExecuteData['NOTICE_INFO']) ) );
                $tmpnoticeIDs = array_keys($retArray[2]);
                if( $tmpnoticeIDs !== array() ){
                    foreach ($tmpnoticeIDs as $tmpnoticeID ) {
                        unset($aryExecuteData['NOTICE_INFO'][$tmpnoticeID]);
                    }
                }
              $register_tgt_row['NOTICE_INFO']       = json_encode( $aryExecuteData['NOTICE_INFO'] );
            }

            $register_tgt_row['ACCESS_AUTH'] = "";

            if( isset( $aryExecuteData['ACCESS_AUTH'] ) === true ){
                $register_tgt_row['ACCESS_AUTH']=$aryExecuteData['ACCESS_AUTH'];
            }

            //上位アクセス権継承
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){

                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }


            $arrayConfigForIUD = $aryConfigForSymClassIUD;
            $tgtSource_row = $register_tgt_row;
            $sqlType = "INSERT";
        }else{

            $aryRetBody = $this->getInfoOfOneConductor($fxVarsIntConductorClassId, 0 ,$getmode);

            $aryRowOfSymClassTable=$aryRetBody[4];

            //追い越しチェック　
            if( 'T_'.$aryRetBody[4]['LUT4U'] != 'T_'.$aryRowOfSymClassTable['LUT4U'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001200";
                $intErrorType = 2;

                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720305");

                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }


            $varConductorClassNo = $fxVarsIntConductorClassId;
            $register_tgt_row['CONDUCTOR_CLASS_NO'] = $fxVarsIntConductorClassId;
            $register_tgt_row['CONDUCTOR_NAME']     = $aryExecuteData['conductor_name'];
            $register_tgt_row['DESCRIPTION'] = "";
            if(isset($aryExecuteData['note'])){
              $register_tgt_row['DESCRIPTION']       = $aryExecuteData['note'];
            }
            $register_tgt_row['DISUSE_FLAG']       = '0';
            $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

            #312 
            $register_tgt_row['NOTICE_INFO'] = "";
            if(isset($aryExecuteData['NOTICE_INFO'])){
                //通知チェック,廃止除外
                $retArray = $this->getNoticeInfo( implode( ",", array_keys($aryExecuteData['NOTICE_INFO']) ) );
                $tmpnoticeIDs = array_keys($retArray[2]);
                if( $tmpnoticeIDs !== array() ){
                    foreach ($tmpnoticeIDs as $tmpnoticeID ) {
                        unset($aryExecuteData['NOTICE_INFO'][$tmpnoticeID]);
                    }
                }
                $register_tgt_row['NOTICE_INFO']       = json_encode( $aryExecuteData['NOTICE_INFO'] );
            }

            $register_tgt_row['ACCESS_AUTH'] = "";
            if( isset( $aryExecuteData['ACCESS_AUTH'] )  == true ){
                $register_tgt_row['ACCESS_AUTH']=$aryExecuteData['ACCESS_AUTH'];
            }

            //上位アクセス権継承
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){

                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }


            $arrayConfigForIUD = $aryConfigForSymClassIUD;
            $tgtSource_row = $register_tgt_row;
            $sqlType = "UPDATE";

        }

        $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                            ,$sqlType
                                            ,"CONDUCTOR_CLASS_NO"
                                            ,$arrTableName['conductor']
                                            ,$arrTableName['conductor']."_JNL"
                                            ,$arrayConfigForIUD
                                            ,$tgtSource_row);

        if( $retArray[0] === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        // ----履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable($arrTableName['conductor'].'_JSQ', 'A_SEQUENCE', FALSE );

        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001300";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        else{
            $varJSeq = $retArray[0];
            $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
        }
        // 履歴シーケンス払い出し----

        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);

        if( $retArray01[0] !== true || $retArray02[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001400";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($retArray01);
        unset($retArray02);
        // Conductorを登録----

        // ----廃止nodeを取得、廃止
        if( $fxVarsIntConductorClassId !="" ){
            $strQuery = "SELECT"
                        ." * "
                        ." FROM "
                        ." ${arrTableName['node']} "
                        ."WHERE "
                        ." DISUSE_FLAG IN ('0') "
                        ."AND CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO "
                        ."ORDER BY "
                        ."NODE_CLASS_NO"
                        ."";

            $tmpDataSet = array();
            $tmpForBind = array();
            $tmpForBind['CONDUCTOR_CLASS_NO']=$fxVarsIntConductorClassId;

            $tmpRetBody = singleSQLExecuteAgent($strQuery, $tmpForBind, $strFxName);

            if( $tmpRetBody[0] === true ){
                $objQuery = $tmpRetBody[1];
                while($tmprow = $objQuery->resultFetch() ){
                    $tmpDataSet[]= $tmprow;
                }
                unset($objQuery);
                //$retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
            $aryMovement = $tmpDataSet;

            foreach($aryMovement as $aryDataForMovement){

                // ----ムーブメントを更新
                $register_tgt_row = array();
                $register_tgt_row['NODE_CLASS_NO']     = $aryDataForMovement['NODE_CLASS_NO'];
                $register_tgt_row['DISUSE_FLAG']       = '1';
                $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];


                $tmparrayConfigForNodeClassIUD_2 = array(
                    "JOURNAL_SEQ_NO"=>"",
                    "JOURNAL_REG_DATETIME"=>"",
                    "JOURNAL_ACTION_CLASS"=>"",
                    "NODE_CLASS_NO"=>"",
                    "DISUSE_FLAG"=>"",
                    "LAST_UPDATE_TIMESTAMP"=>"",
                    "LAST_UPDATE_USER"=>""
                );

                $arrayConfigForIUD = $tmparrayConfigForNodeClassIUD_2;
                $tgtSource_row = $register_tgt_row;
                $sqlType = "UPDATE";

                $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                    ,$sqlType
                                                    ,"NODE_CLASS_NO"
                                                    ,$arrTableName['node']
                                                    ,$arrTableName['node']."_JNL"
                                                    ,$arrayConfigForIUD
                                                    ,$tgtSource_row);


                if( $retArray[0] === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001600";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];

                // ----履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($arrTableName['node'].'_JSQ', 'A_SEQUENCE', FALSE );

                if( $retArray[1] != 0 ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001700";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }else{
                    $varJSeq = $retArray[0];
                    $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                }
                // 履歴シーケンス払い出し----

                $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
                $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);


                if( $retArray01[0] !== true || $retArray02[0] !== true ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001800";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($retArray01);
                unset($retArray02);

                #terminal廃止
                if( $fxVarsIntConductorClassId !="" ){
                    $strQuery = "SELECT"
                                ." * "
                                ." FROM "
                                ." ${arrTableName['terminal']} "
                                ."WHERE "
                                ." DISUSE_FLAG IN ('0') "
                                ."AND CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO "
                                ."AND NODE_CLASS_NO = :NODE_CLASS_NO "
                                ."ORDER BY "
                                ."NODE_CLASS_NO"
                                ."";

                    $tmpDataSet = array();
                    $tmpForBind = array();
                    $tmpForBind['CONDUCTOR_CLASS_NO']=$fxVarsIntConductorClassId;
                    $tmpForBind['NODE_CLASS_NO']=$aryDataForMovement['NODE_CLASS_NO'];

                    $tmpRetBody = singleSQLExecuteAgent($strQuery, $tmpForBind, $strFxName);

                    if( $tmpRetBody[0] === true ){
                        $objQuery = $tmpRetBody[1];
                        while($tmprow = $objQuery->resultFetch() ){
                            $tmpDataSet[]= $tmprow;
                        }
                        unset($objQuery);
                        //$retBool = true;

                    }else{
                        $intErrorType = 500;
                        $intRowLength = -1;
                    }
                    $aryTerminals = $tmpDataSet;

                    foreach($aryTerminals as $aryDataForTerminal){

                        // ----ムーブメントを更新

                        $register_tgt_row = array();
                        $register_tgt_row['TERMINAL_CLASS_NO']     = $aryDataForTerminal['TERMINAL_CLASS_NO'];
                        $register_tgt_row['DISUSE_FLAG']       = '1';
                        $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

                        $arrayConfigForTermClassIUD2 = array(
                            "JOURNAL_SEQ_NO"=>"",
                            "JOURNAL_REG_DATETIME"=>"",
                            "JOURNAL_ACTION_CLASS"=>"",
                            "TERMINAL_CLASS_NO"=>"",
                            "DISUSE_FLAG"=>"",
                            "LAST_UPDATE_TIMESTAMP"=>"",
                            "LAST_UPDATE_USER"=>""
                        );

                        $arrayConfigForIUD = $arrayConfigForTermClassIUD2;
                        $tgtSource_row = $register_tgt_row;
                        $sqlType = "UPDATE";

                        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                            ,$sqlType
                                                            ,"TERMINAL_CLASS_NO"
                                                            ,$arrTableName['terminal']
                                                            ,$arrTableName['terminal']."_JNL"
                                                            ,$arrayConfigForIUD
                                                            ,$tgtSource_row);


                        if( $retArray[0] === false ){
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00001600";
                            //
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }

                        $sqlUtnBody = $retArray[1];
                        $arrayUtnBind = $retArray[2];

                        $sqlJnlBody = $retArray[3];
                        $arrayJnlBind = $retArray[4];

                        // ----履歴シーケンス払い出し
                        $retArray = getSequenceValueFromTable($arrTableName['terminal'].'_JSQ', 'A_SEQUENCE', FALSE );

                        if( $retArray[1] != 0 ){
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00001700";
                            //
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }else{
                            $varJSeq = $retArray[0];
                            $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                        }
                        // 履歴シーケンス払い出し----

                        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
                        $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);


                        if( $retArray01[0] !== true || $retArray02[0] !== true ){
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00001800";
                            //
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                        unset($retArray01);
                        unset($retArray02);
                    }
                }
            }
        }

        // 廃止nodeを取得、廃止----

        // ----nodeを登録
        $aryMovement  = $aryNodeData;
        foreach($aryMovement as $aryDataForMovement){
            // ----ムーブメントを更新
            $register_tgt_row = $aryNodeClassValueTmpl;

            $retArray = getSequenceValueFromTable($arrTableName['node'].'_RIC', 'A_SEQUENCE', FALSE );

            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001500";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varRISeq = $retArray[0];
            }

            //個別オペレーションのチェック、取得
            if( !isset($aryDataForMovement['OPERATION_NO_IDBH']) )$aryDataForMovement['OPERATION_NO_IDBH']="";
            if( !isset($aryDataForMovement['PATTERN_ID']) )$aryDataForMovement['PATTERN_ID']="";
            if($aryDataForMovement['OPERATION_NO_IDBH'] != "")
            {
                $tmpStrOpeNoIDBH = $aryDataForMovement['OPERATION_NO_IDBH'];
                $tmpStrPatternID = $aryDataForMovement['PATTERN_ID'];

                $tmpAryRetBody = $this->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);

                if( $tmpAryRetBody[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002700";
                    //
                    if( $tmpAryRetBody[1] == 101 ){
                        $intErrorType = 2;
                        //
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170005",array($tmpStrPatternID));
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
            }

            if( !isset( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) )$aryDataForMovement['CALL_CONDUCTOR_ID']="";
            if( !isset( $aryDataForMovement['note'] ) )$aryDataForMovement['note']="";
            if( !isset( $aryDataForMovement['NEXT_PENDING_FLAG'] ) )$aryDataForMovement['NEXT_PENDING_FLAG']="";
            if( !isset( $aryDataForMovement['DESCRIPTION'] ) )$aryDataForMovement['DESCRIPTION']="";
            if( !isset( $aryDataForMovement['ORCHESTRATOR_ID'] ) )$aryDataForMovement['ORCHESTRATOR_ID']="";
            if( !isset( $aryDataForMovement['PATTERN_ID'] ) )$aryDataForMovement['PATTERN_ID']="";
            if( !isset( $aryDataForMovement['OPERATION_NO_IDBH'] ) )$aryDataForMovement['OPERATION_NO_IDBH']="";
            if( !isset( $aryDataForMovement['SKIP_FLAG'] ) )$aryDataForMovement['SKIP_FLAG']="";
            if( !isset( $aryDataForMovement['NEXT_PENDING_FLAG'] ) )$aryDataForMovement['NEXT_PENDING_FLAG']="";
            if( !isset( $aryDataForMovement['CALL_SYMPHONY_ID'] ) )$aryDataForMovement['CALL_SYMPHONY_ID']="";

            if( !isset( $aryDataForMovement['x'] ) )$aryDataForMovement['x']="";
            if( !isset( $aryDataForMovement['y'] ) )$aryDataForMovement['y']="";
            if( !isset( $aryDataForMovement['w'] ) )$aryDataForMovement['w']="";
            if( !isset( $aryDataForMovement['h'] ) )$aryDataForMovement['h']="";

            //廃止済みMovement対応
            if( $aryDataForMovement['type'] == "movement" ){
                if (  ( $aryDataForMovement['ORCHESTRATOR_ID'] == "" || !is_numeric( $aryDataForMovement['ORCHESTRATOR_ID'] ) ) &&
                      ( $aryDataForMovement['PATTERN_ID'] == "" || !is_numeric( $aryDataForMovement['PATTERN_ID'] ) )
                ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170013");
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }
            //CALL呼び出し値有無
            if( $aryDataForMovement['type'] == "call" && ( $aryDataForMovement['CALL_CONDUCTOR_ID'] == "" || !is_numeric( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) ) ){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170006",array($fxVarsIntConductorClassId));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            //CALL呼び出しのループ簡易バリデーション（インスタンス実行時に詳細確認）
            if( $fxVarsIntConductorClassId != "" ){
                if ( $fxVarsIntConductorClassId == $aryDataForMovement['CALL_CONDUCTOR_ID']){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170006",array($fxVarsIntConductorClassId));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }

            //CALL呼び出し値有無(symphony)
            if( $aryDataForMovement['type'] == "call_s" && ( $aryDataForMovement['CALL_SYMPHONY_ID'] == "" || !is_numeric( $aryDataForMovement['CALL_SYMPHONY_ID'] ) ) ){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170015");
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $varNodeClassID = $varRISeq;
            $register_tgt_row = array();
            $register_tgt_row['NODE_CLASS_NO']     = $varRISeq;
            $register_tgt_row['NODE_NAME']         = $aryDataForMovement['id'];
            $register_tgt_row['NODE_TYPE_ID']      = $aryDataForMovement['type'];
            $register_tgt_row['ORCHESTRATOR_ID']   = $aryDataForMovement['ORCHESTRATOR_ID'];
            $register_tgt_row['PATTERN_ID']        = $aryDataForMovement['PATTERN_ID'];

            if( $aryDataForMovement['type'] == "call" )$register_tgt_row['CONDUCTOR_CALL_CLASS_NO']   = $aryDataForMovement['CALL_CONDUCTOR_ID'];
            if( $aryDataForMovement['type'] == "call_s" )$register_tgt_row['CONDUCTOR_CALL_CLASS_NO']   = $aryDataForMovement['CALL_SYMPHONY_ID'];

            $register_tgt_row['DESCRIPTION']       = $aryDataForMovement['note'];
            $register_tgt_row['CONDUCTOR_CLASS_NO'] = $varConductorClassNo;
            $register_tgt_row['OPERATION_NO_IDBH'] = $aryDataForMovement['OPERATION_NO_IDBH'];
            $register_tgt_row['SKIP_FLAG'] = $aryDataForMovement['SKIP_FLAG'];
            $register_tgt_row['NEXT_PENDING_FLAG'] = $aryDataForMovement['NEXT_PENDING_FLAG'];
            $register_tgt_row['DISUSE_FLAG']       = '0';
            $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

            $register_tgt_row['POINT_X']   = $aryDataForMovement['x'];
            $register_tgt_row['POINT_Y']   = $aryDataForMovement['y'];
            $register_tgt_row['POINT_W']   = $aryDataForMovement['w'];
            $register_tgt_row['POINT_H']   = $aryDataForMovement['h'];

            //ENDタイプ設定 #467 
            if( $aryDataForMovement['type'] == "end" ){
                if( isset( $aryDataForMovement['END_TYPE'] ) === true ){
                    $register_tgt_row['END_TYPE'] = $aryDataForMovement['END_TYPE'];
                    if( $register_tgt_row['END_TYPE'] == "" ){
                        $register_tgt_row['END_TYPE'] = "5";
                    }
                }else{
                    $register_tgt_row['END_TYPE'] = "5";
                }
            }

            #変換
            if( $aryDataForMovement['type'] == "start" )            $register_tgt_row['NODE_TYPE_ID']=1;
            if( $aryDataForMovement['type'] == "end")               $register_tgt_row['NODE_TYPE_ID']=2;
            if( $aryDataForMovement['type'] == "movement")          $register_tgt_row['NODE_TYPE_ID']=3;
            if( $aryDataForMovement['type'] == "call")              $register_tgt_row['NODE_TYPE_ID']=4;
            if( $aryDataForMovement['type'] == "parallel-branch")   $register_tgt_row['NODE_TYPE_ID']=5;
            if( $aryDataForMovement['type'] == "conditional-branch")$register_tgt_row['NODE_TYPE_ID']=6;
            if( $aryDataForMovement['type'] == "merge")             $register_tgt_row['NODE_TYPE_ID']=7;
            if( $aryDataForMovement['type'] == "pause")             $register_tgt_row['NODE_TYPE_ID']=8;
            if( $aryDataForMovement['type'] == "blank")             $register_tgt_row['NODE_TYPE_ID']=9;
            if( $aryDataForMovement['type'] == "call_s")             $register_tgt_row['NODE_TYPE_ID']=10;

            //#587
            if( $aryDataForMovement['type'] == "status-file-branch")$register_tgt_row['NODE_TYPE_ID']=11;
            
            $register_tgt_row['ACCESS_AUTH'] = "";
            if( isset( $aryExecuteData['ACCESS_AUTH'] ) === true ){
                $register_tgt_row['ACCESS_AUTH']=$aryExecuteData['ACCESS_AUTH'];
            }

            //上位アクセス権継承
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){

                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }

            $arrayConfigForIUD = $arrayConfigForNodeClassIUD;
            $tgtSource_row = $register_tgt_row;
            $sqlType = "INSERT";

            $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                ,$sqlType
                                                ,"NODE_CLASS_NO"
                                                ,$arrTableName['node']
                                                ,$arrTableName['node']."_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);

            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001600";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable($arrTableName['node'].'_JSQ', 'A_SEQUENCE', FALSE );

            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001700";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varJSeq = $retArray[0];
                $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            // 履歴シーケンス払い出し----

            $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);

            if( $retArray01[0] !== true || $retArray02[0] !== true ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001800";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);


            // ムーブメントを更新----

            // ----TERMINALを登録
            if( isset($aryDataForMovement['terminal']) ){
                $aryTerminals = $aryDataForMovement['terminal'];

                foreach($aryTerminals as $aryDataForTerminal){

                    $retArray = getSequenceValueFromTable($arrTableName['terminal'].'_RIC', 'A_SEQUENCE', FALSE );

                    if( $retArray[1] != 0 ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00001500";
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    else{
                        $varRISeq = $retArray[0];
                    }

                    if( !isset( $aryDataForTerminal['case'] ) )$aryDataForTerminal['case']="";
                    if( !isset( $aryDataForTerminal['condition'] ) )$aryDataForTerminal['condition']="";
                    if( !isset( $aryDataForTerminal['x'] ) )$aryDataForTerminal['x']="";
                    if( !isset( $aryDataForTerminal['y'] ) )$aryDataForTerminal['y']="";

                    $register_tgt_row = array();
                    $register_tgt_row['TERMINAL_CLASS_NO']     = $varRISeq;
                    $register_tgt_row['TERMINAL_CLASS_NAME']   = $aryDataForTerminal['id'];
                    $register_tgt_row['TERMINAL_TYPE_ID']      = $aryDataForTerminal['type'];
                    $register_tgt_row['NODE_CLASS_NO']         = $varNodeClassID;
                    $register_tgt_row['CONDUCTOR_CLASS_NO']     = $varConductorClassNo;
                    $register_tgt_row['CONNECTED_NODE_NAME']   = $aryDataForTerminal['targetNode'];
                    $register_tgt_row['LINE_NAME']             = $aryDataForTerminal['edge'];
                    $register_tgt_row['TERMINAL_NAME']         = $aryDataForTerminal['id'];

                    //条件のstr化
                    $strterminalval="";
                    if(is_array($aryDataForTerminal['condition'])){
                        foreach ($aryDataForTerminal['condition'] as $tckey => $tcvalue) {
                            if($strterminalval == "" ){
                                $strterminalval = $tcvalue;
                            }else{
                                $strterminalval = $strterminalval .",". $tcvalue;
                            }
                        }
                        $register_tgt_row['CONDITIONAL_ID']        = $strterminalval;
                    }


                    $register_tgt_row['CASE_NO']               = $aryDataForTerminal['case'];

                    // #587
                    if( $aryDataForTerminal['case'] == "else" && $aryDataForMovement['type'] == "status-file-branch" ){
                        $register_tgt_row['CASE_NO'] = 0 ;
                    }

                    $register_tgt_row['DISUSE_FLAG']       = '0';
                    $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

                    $register_tgt_row['POINT_X']   = $aryDataForTerminal['x'];
                    $register_tgt_row['POINT_Y']   = $aryDataForTerminal['y'];

                    if( $aryDataForTerminal['type'] == "in" )$register_tgt_row['TERMINAL_TYPE_ID']=1;
                    if( $aryDataForTerminal['type'] == "out")$register_tgt_row['TERMINAL_TYPE_ID']=2;

                    $register_tgt_row['ACCESS_AUTH'] = "";
                    if( isset( $aryExecuteData['ACCESS_AUTH'] )  == true ){
                        $register_tgt_row['ACCESS_AUTH']=$aryExecuteData['ACCESS_AUTH'];
                    }

                    //上位アクセス権継承
                    if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){

                        $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
                    }


                    $arrayConfigForIUD = $arrayConfigForTermClassIUD;
                    $tgtSource_row = $register_tgt_row;
                    $sqlType = "INSERT";

                    $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                        ,$sqlType
                                                        ,"TERMINAL_CLASS_NO"
                                                        ,$arrTableName['terminal']
                                                        ,$arrTableName['terminal']."_JNL"
                                                        ,$arrayConfigForIUD
                                                        ,$tgtSource_row);

                    if( $retArray[0] === false ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00001600";
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }

                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];

                    $sqlJnlBody = $retArray[3];
                    $arrayJnlBind = $retArray[4];

                    // ----履歴シーケンス払い出し
                    $retArray = getSequenceValueFromTable($arrTableName['terminal'].'_JSQ', 'A_SEQUENCE', FALSE );

                    if( $retArray[1] != 0 ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00001700";
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    else{
                        $varJSeq = $retArray[0];
                        $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                    }
                    // 履歴シーケンス払い出し----

                    $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
                    $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);

                    if( $retArray01[0] !== true || $retArray02[0] !== true ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00001800";
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    unset($retArray01);
                    unset($retArray02);

                }
            }
            // TERMINALを登録----

        }
        // ムーブメントを登録----

        $retBool = true;
        $intConductorClassId = $varConductorClassNo;
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102010");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101030");
            }
            web_log($tmpMsgBody);

            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102020");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101040");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----

        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        #if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $retArray = array($strResultCode,
                      $strDetailCode,
                      $intConductorClassId,
                      nl2br($strExpectedErrMsgBodyForUI)
                      );
    #dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ある１のConductorの定義を新規登録（追加）する----

//----Conductorのパラメータの整形
function nodeDateDecodeForEdit($fxVarsStrSortedData){
    global $g;
    $aryMovement = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    $intControlDebugLevel01=250;

    $objMTS = $g['objMTS'];

    $strFxName = '([FUNCTION]'.__FUNCTION__.')';

    $strSysErrMsgBody = "";

    $intLengthArySettingForParse = count($fxVarsStrSortedData);

    $aryMovement = array();
    //node分繰り返し
    $aryNode = array();
    $arrpatternDel = array('/__proto__/');
    $arrpatternPrm = array('/node/','/id/','/type/','/note/','/condition/','/case/','/x/','/y/','/w/','/h/','/edge/','/targetNode/','/PATTERN_ID/','/ORCHESTRATOR_ID/','/OPERATION_NO_IDBH/','/SYMPHONY_CALL_CLASS_NO/','/SKIP_FLAG/','/CONDUCTOR_CALL_CLASS_NO/','/CALL_CONDUCTOR_ID/','/CALL_SYMPHONY_ID/','/ACCESS_AUTH/','/END_TYPE/' );

    foreach( $fxVarsStrSortedData as $nodename => $nodeinfo ){
        //　nodeの処理開始
        if( strpos($nodename,'node-') !== false  ){
            foreach ($nodeinfo as $key => $value) {
                #nodeパラメータ整形
                $ASD = preg_replace( $arrpatternPrm, "" , $key );
                if( $ASD == "" ){
                    if( is_array($value) ){
                        foreach ($value as $optionkey => $optionval) {
                            $aryNode[$nodename][$optionkey]=$optionval;
                        }
                    }else{
                        $aryNode[$nodename][$key]=$value;
                    }
                    #terminalパラメータ
                }elseif( strpos($key,'terminal') !== false  ){
                    foreach ($value as $terminalname => $terminalarr) {
                        if( is_array($terminalarr) ){
                            #terminalパラメータ整形
                            foreach ($terminalarr as $terminalkey => $terminalinfo) {
                                $ZXC = preg_replace( $arrpatternDel, "" , $terminalkey);
                                if( is_array($terminalinfo) && isset($terminalarr['condition'])){
                                    foreach ($terminalinfo as $arrterminalval)$aryNode[$nodename][$key][$terminalname][$terminalkey][] = $arrterminalval;
                                 }elseif( $ZXC != ""  ){
                                    if( !is_array($terminalinfo) && strlen($terminalkey) >= 1){
                                        $aryNode[$nodename][$key][$terminalname][$terminalkey] = $terminalinfo ;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $aryNode;

}


//----symphony一覧を取得する
    function getInfoOfSymphonyList(){
        /////////////////////////////////////////////////////////////
        // symphony一覧を取得                                //
        /////////////////////////////////////////////////////////////

        // グローバル変数宣言
        global $g;

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
            $obj = new RoleBasedAccessControl($objDBCA);

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
                "ACCESS_AUTH"=>"",
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
                "CSYMPHONY_NAME"=>"",
                "DESCRIPTION"=>"",
                "ACCESS_AUTH"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>"",
                $strSelectMaxLastUpdateTimestamp=>""
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strSelectForUpdateLock = "";

            $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0') {$strSelectForUpdateLock}");

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
            $rows = array();
            while ( $row = $objQueryUtn->resultFetch() ){

                $user_id = $g['login_id'];
                $ret  = $obj->getAccountInfo($user_id);
                list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                if($ret === false) {
                } else {
                    if($permission === true) {
                        $rows[] = $row;
                    }
                }
            }
            //発見行だけループ----

            unset($objQueryUtn);
            unset($retArray);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//symphony一覧を取得する----
//----シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録する(ConductorからのSymphony呼び出し)
    function registerSymphonyInstanceForConductor($intShmphonyClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName){

        // グローバル変数宣言
        global $g;

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
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        // 変数定義----


        try{
            $objDBCA = $g['objDBCA'];
            $objMTS  = $g['objMTS'];
            $lc_db_model_ch = $objDBCA->getModelChannel();

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

            $register_tgt_row['ACCESS_AUTH']          = $aryRowOfSymClassTable['ACCESS_AUTH'];

            //上位アクセス権継承
            if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
            }

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

                //Conductor(SymphonyCall)用処理
                if( isset( $aryValuePerOptionOrderKey['EXE_SKIP_FLAG'] ) !== true )$aryValuePerOptionOrderKey['EXE_SKIP_FLAG']='';
                if( isset( $aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH'] ) !== true )$aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH']='';

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

                $register_tgt_row['ACCESS_AUTH']          = $aryRowOfSymClassTable['ACCESS_AUTH'];

                //上位アクセス権継承
                if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                    $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
                }

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
//シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録する(ConductorからのSymphony呼び出し)----

//----ロール一覧を取得する
    function getInfoOfRoleList(){
        /////////////////////////////////////////////////////////////
        // ロール一覧を取得                                //
        /////////////////////////////////////////////////////////////

        global $g;

        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $rows = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";
        //
        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $obj = new RoleBasedAccessControl($objDBCA);

            $user_id = $g['login_id'];
            $ret  = $obj->getAccountInfo($user_id);
            $DefaultAccessRoles = $obj->getDefaultAccessRoles();
            $arrAccessAuth = explode( ",", $DefaultAccessRoles );

            // 表示データをSELECT
            $sql =  " SELECT "
                   ." TAB_A.*,TAB_B.ROLE_NAME AS ROLE_NAME "
                   ." FROM A_ROLE_ACCOUNT_LINK_LIST TAB_A "
                   ." LEFT JOIN A_ROLE_LIST TAB_B"
                   ." ON TAB_A.ROLE_ID = TAB_B.ROLE_ID "
                   ." WHERE TAB_A.DISUSE_FLAG='0' "
                   ." AND TAB_A.USER_ID = ${user_id} "
                   ."";

            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $rows = array();

            while($row = $objQuery->resultFetch()) {
                if( in_array( $row['ROLE_ID'], $arrAccessAuth) ){
                    $row['DEFAULT_ROLE'] = "checked";
                }else{
                    $row['DEFAULT_ROLE'] = "";
                }
                $rows[] = $row;
            }

            unset($objQuery);
            unset($r);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//ロール一覧を取得する----


//---作業実行（Conductor/Symphony、Operation)時のアクセス件設定
    function getInfoAccessAuthWorkFlowOpe($fxVarsIntClassId, $fxVarsIntOperationNo , $mode="C" ,$aryOptionOverride=array() ){
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strConAccesAuth = array();
        $strOpeAccesAuth = array();
        $strFxName = '([FUNCTION]'.__FUNCTION__.')';
        $strSysErrMsgBody = "";
        $strExeAccesAuth = "";

        $strClassAccesAuth = "";
        $strOpeAccesAuth = "";

        $arrExeNode = array(
                3 => "movement",
                4 => "call",
                10 => "call_s",
            );

        try{
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $objMTS = $this->getMessageTemplateStorage();

            //Symphony
            if( $mode == "S" ) $aryRetBody = $this->getInfoFromOneOfSymphonyClasses($fxVarsIntClassId);
            //Conductor
            if( $mode == "C" ) $aryRetBody = $this->getInfoFromOneOfConductorClass($fxVarsIntClassId, 0,0,0,1);

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
            $strClassAccesAuth = $aryRetBody[4]['ACCESS_AUTH'];
            $arrClassMovCall = $aryRetBody[5];

            //Operation
            $aryRetBody = $this->getInfoOfOneOperation($fxVarsIntOperationNo);

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
            $strOpeAccesAuth = $aryRetBody[4]['ACCESS_AUTH'];


            // --- 個別指定のオペレーションIDからアクセス権取得
            $arrMovSpAccesAuthList=array();

            //個別指定のオペレーションのID取得
            $arrOverrideOpeAccessAuth=array();
            //Symphony
            if( $mode == "S" ){
                foreach ($aryOptionOverride as $nodename => $nodeinfo) {
                    //個別指定のオペレーションのID取得（作業実行時変更分）
                    if( $nodeinfo["OVRD_OPERATION_NO_IDBH"] != "" ){
                        $arrMovSpAccesAuthList[] = $nodeinfo['OVRD_OPERATION_NO_IDBH'];
                    }
                }
            //Conductor
            }elseif( $mode == "C" ){
                //Conductor配下、Call先の個別指定のオペレーションID取
                foreach ( $arrClassMovCall as $key => $nodeinfo ) {
                    //ConductorCall
                    if( $nodeinfo['NODE_TYPE_ID'] == 4 ){
                        $arrMovSpAccesAuthList = $this->checkCallLoopValidator( $nodeinfo['CONDUCTOR_CALL_CLASS_NO'] ,$arrMovSpAccesAuthList );
                    //SymphonyCall
                    }elseif( $nodeinfo['NODE_TYPE_ID'] == 10 ){
                        $tmpRetBody = $this->getInfoFromOneOfSymphonyClasses( $nodeinfo['CONDUCTOR_CALL_CLASS_NO'] );
                        $tmpMovLists = $tmpRetBody[5];
                        foreach ($tmpMovLists as $tmpMovement) {
                           if( $tmpMovement["OPERATION_NO_IDBH"] != "")$arrMovSpAccesAuthList[]=$tmpMovement["OPERATION_NO_IDBH"];
                        }
                    }
                }
                //個別指定のオペレーションのID取得（作業実行時変更分）
                foreach ($aryOptionOverride as $nodename => $nodeinfo) {
                    if( array_search( $nodeinfo['type'] , $arrExeNode ) == true ){
                        #作業実行時個別指定変更
                        if( $nodeinfo["OPERATION_NO_IDBH"] != "" ){
                            $arrMovSpAccesAuthList[] = $nodeinfo['OPERATION_NO_IDBH'];
                        }
                    }
                }
            }

            //個別指定のオペレーションIDリストからオペレーションのアクセス権取得
            $arrSpOpeAccesAuth=array();
            foreach ($arrMovSpAccesAuthList as $tmpOperationId ) {
                //Operation
                $tmpRetBody = $this->getInfoOfOneOperation($tmpOperationId);

                if( $tmpRetBody[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrMsg = $tmpRetBody[4];
                    $strErrStepIdInFx="00000100";
                    if( $tmpRetBody[1] === 101 ){
                        //----１行も発見できなかった場合
                        $intErrorType = 101;
                        $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170038");
                        //１行も発見できなかった場合----
                    }
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                $arrSpOpeAccesAuth[] = $tmpRetBody[4]['ACCESS_AUTH'];
            }
            // 個別指定のオペレーションIDからアクセス権取得 ---

            //作業実行時のアクセス権　（ Symphony/Conductor ∩　Operation ）

            if( $strClassAccesAuth == "" && $strOpeAccesAuth == "" ){
                //Conductor/Symphony、Operatuonのアクセス権全公開時
                $strExeAccesAuth = "";
            }elseif( $strClassAccesAuth == "" && $strOpeAccesAuth != "" ){
                //Operationのみアクセス権設定あり
                $strExeAccesAuth = $strOpeAccesAuth;

            }elseif( $strClassAccesAuth != "" && $strOpeAccesAuth == "" ){
                //Conductor/Symphonyのみアクセス権設定あり
                $strExeAccesAuth = $strClassAccesAuth;
            }else{
                //Conductor/Symphony、Operatuonのアクセス権設定あり
                $arrClassAccesAuth = explode(",", $strClassAccesAuth);
                $arrOpeAccesAuth = explode(",", $strOpeAccesAuth);
                //共通のアクセス権抽出
                $strExeAccesAuth = implode(",", array_intersect( $arrClassAccesAuth, $arrOpeAccesAuth ) );

                //共通のアクセス権無しの場合、作業実行不可
                if( $strExeAccesAuth == "" ){
                    // エラーフラグをON
                    // 例外処理へ
                    if( $mode == "S" )$workflowName = "Symphony";
                    if( $mode == "C" )$workflowName = "Conductor";
                    ###$strErrMsg = "選択した${workflowName}、Operation で設定されているアクセス権では作業実行できません。";
                    $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170019",array($workflowName));
                    $strErrStepIdInFx="00000100";
                    $intErrorType = 101;
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }

            //　作業実行時のアクセス権（個別）　（ 作業実行時のアクセス権 ∩　個別指定オペレーション ）
            foreach ($arrSpOpeAccesAuth as $strSpOpeAccesAuth) {
                if( $strExeAccesAuth == "" && $strSpOpeAccesAuth == "" ){

                    $strExeAccesAuth = "";
                }elseif( $strExeAccesAuth == "" && $strSpOpeAccesAuth != "" ){

                    $strExeAccesAuth = $strSpOpeAccesAuth;

                }elseif( $strExeAccesAuth != "" && $strSpOpeAccesAuth == "" ){

                    continue;
                }else{
                    $arrExeAccesAuth = explode(",", $strExeAccesAuth);
                    $tmpSpOpeAccesAuth = explode(",", $strSpOpeAccesAuth);

                    //共通のアクセス権抽出
                    $strExeAccesAuth = implode(",", array_intersect( $arrExeAccesAuth, $tmpSpOpeAccesAuth ) );

                    //共通のアクセス権無しの場合、作業実行不可
                    if( $strExeAccesAuth == "" ){
                        // エラーフラグをON
                        // 例外処理へ
                        if( $mode == "S" )$workflowName = "Symphony";
                        if( $mode == "C" )$workflowName = "Conductor";
                        ###$strErrMsg = "選択した${workflowName}、Operation で設定されているアクセス権では作業実行できません。";
                        $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170019",array($workflowName));
                        $strErrStepIdInFx="00000100";
                        $intErrorType = 101;
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
            }

            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType === null ) $intErrorType = 500;
            $tmpErrMsgBody = $e->getMessage();
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        }

        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strExeAccesAuth);

        return $retArray;
    }
//作業実行（Conductor/Symphony、Operation)時のアクセス件設定----

//---- 作業実行時、対象の実行コンダクタIDに紐づくすべてのcallコンダクタ/callシンフォニー/Movementについてのアクセス権をチェックする
    function checkConductorNodeAccessAuth($fxVarsIntClassId, $fxVarsIntExecuteUserId, $fxVarsaryOptionOrderOverride = array()){
        $objDBCA = $this->getDBConnectAgent();
        $objMTS = $this->getMessageTemplateStorage();
        $objRBAC = new RoleBasedAccessControl($objDBCA);
        $ret  = $objRBAC->getAccountInfo($fxVarsIntExecuteUserId);

        $strFxName = '([FUNCTION]'.__FUNCTION__.')';
        $boolRet = false;
        $intErrorType = null;
        $strExpectedErrMsgBodyForUI = "";
        $strSysErrMsgBody = "";
        $tmpErrMsgBody = "";

        $parentConductorID = $fxVarsIntClassId; //チェック対象コンダクタID（親コンダクタ）
        $aryTmpConductorList = array(); //親コンダクタから始まる末端のcallされたコンダクタをチェックするための配列
        $aryCheckConductorList = array(); //すべての対象のコンダクタIDのみを格納するための配列
        $aryCallConductorList = array(); //親コンダクタおよびcallされたコンダクタのノードを格納するための配列
        $aryCallSymphonyList = array(); //callされたシンフォニーのノードを格納するための配列
        $aryMovementList = array(); //単体実行Movementのノードを格納するための配列
        array_push($aryTmpConductorList, $parentConductorID);
        array_push($aryCheckConductorList, $parentConductorID);

        try{
            //親コンダクタから付随するすべての末端のcallコンダクタ/callシンフォニー/Movementノードを取得する
            while(!empty($aryTmpConductorList)){
                foreach($aryTmpConductorList as $key => $conductorID){
                    $conductorNodeList = array();
                    $getmode = 1;
                    $retArray = $this->getInfoFromOneOfConductorClass($conductorID, 0,0,0,$getmode);
                    $conductorNodeList = $retArray[5];
                    foreach($conductorNodeList as $node){
                        $callConductorNo = $node['CONDUCTOR_CALL_CLASS_NO'];
                        $nodeType = $node['NODE_TYPE_ID'];
                        //CONDUCTOR_CALL_CLASS_NOが存在し、NODE_TYPE_IDが4(call)のものについて、CONDUCTOR_CALL_CLASS_NOを格納する
                        if(isset($callConductorNo) && $nodeType == 4){

                            $sql = "SELECT *
                                    FROM C_CONDUCTOR_EDIT_CLASS_MNG
                                    WHERE CONDUCTOR_CLASS_NO = {$callConductorNo}";

                            //SQL準備
                            $objQuery = $objDBCA->sqlPrepare($sql);
                            //SQL発行
                            $r = $objQuery->sqlExecute();
                            //廃止フラグOFFの全レコード処理(FETCH)
                            $disuseFlag = false;
                            while ( $row = $objQuery->resultFetch() ){
                                if($row['DISUSE_FLAG'] == 1){
                                    $disuseFlag = true;
                                }
                            }

                            //廃止コンダクタはループ対象に含めない
                            if($disuseFlag == false){
                                //callコンダクタの中をチェックするため、$aryTmpConductorListに格納
                                array_push($aryTmpConductorList, $callConductorNo);
                                //対象のコンダクタ一覧として、aryCheckConductorListに格納
                                array_push($aryCheckConductorList, $callConductorNo);
                            }

                            //CONDUCTOR_CALL_CLASS_NOが存在し、NODE_TYPE_IDが4(call)のものについて、$callConductorNoをkeyとした配列にノード情報を格納する
                            if(empty($aryCallConductorList[$conductorID])){
                                $aryCallConductorList[$conductorID] = array();
                            }
                            array_push($aryCallConductorList[$conductorID], $node);
                        }

                        //CONDUCTOR_CALL_CLASS_NOが存在し、NODE_TYPE_IDが10(call_s)のものについて、$conductorIDをkeyとした配列にノード情報を格納する
                        if(isset($callConductorNo) && $nodeType == 10){
                            if(empty($aryCallSymphonyList[$conductorID])){
                                $aryCallSymphonyList[$conductorID] = array();
                            }
                            array_push($aryCallSymphonyList[$conductorID], $node);
                        }

                        //NODE_TYPE_IDが3(Movement)のものについて、$conductorIDをkeyとした配列にノード情報を格納する
                        if($nodeType == 3){
                            if(empty($aryMovementList[$conductorID])){
                                $aryMovementList[$conductorID] = array();
                            }
                            array_push($aryMovementList[$conductorID], $node);
                        }
                    }

                    //自分自身を$aryTmpConductorListから外す
                    unset($aryTmpConductorList[$key]);

                }
            }

            //対象のコンダクタ一覧をループしそれぞれのノードについてのアクセス権をチェックする
            foreach($aryCheckConductorList as $conductorID){
                //callコンダクタチェック
                if(!empty($aryCallConductorList[$conductorID])){
                    $aryConductorNodeList = $aryCallConductorList[$conductorID];
                    foreach($aryConductorNodeList as $node){
                        $operationId = $node['OPERATION_NO_IDBH'];
                        $callConductorID = $node['CONDUCTOR_CALL_CLASS_NO'];

                        //$fxVarsaryOptionOrderOverrideがある場合かつ対象が親コンダクタIDの場合、作業実行ページからオペレーションの付け替えを考慮する
                        if(!empty($fxVarsaryOptionOrderOverride) && $conductorID == $parentConductorID){
                            $nodeName = $node['NODE_NAME'];
                            $overrideOperationId = $fxVarsaryOptionOrderOverride[$nodeName]['OPERATION_NO_IDBH'];
                            if(isset($overrideOperationId)){
                                $operationId = $overrideOperationId;
                            }
                        }

                        //callコンダクタのアクセス権をチェック
                        $sql = "SELECT *
                                FROM C_CONDUCTOR_EDIT_CLASS_MNG
                                WHERE CONDUCTOR_CLASS_NO = {$callConductorID}
                                AND DISUSE_FLAG = 0";

                        //SQL準備
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        //SQL発行
                        $r = $objQuery->sqlExecute();
                        //廃止フラグOFFの全レコード処理(FETCH)
                        $targetRow = "";
                        while ( $row = $objQuery->resultFetch() ){
                            $targetRow = $row;
                        }

                        //対象レコードが無い場合はcontinue
                        if($targetRow == ""){
                            continue;
                        }

                        //アクセス権チェック
                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                        if($ret === false) {
                            // 例外処理へ
                            $strErrStepIdInFx="00000100";
                            $intErrorType = 1; //システムエラー
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        } else {
                            if($permission !== true) {
                                //アクセス権限を持っていない場合
                                $intErrorType = 2; //バリデーションエラー
                                $strErrStepIdInFx="00000200";
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170029",array($conductorID)); //ConductorCall - 指定できないConductorクラスIDが含まれています。（Conductor:{}）
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            }
                        }

                        //callコンダクタに指定されたオペレーションのアクセス権をチェック
                        if($operationId != ""){
                            $sql = "SELECT *
                                    FROM C_OPERATION_LIST
                                    WHERE OPERATION_NO_UAPK = {$operationId}
                                    AND DISUSE_FLAG = 0";

                            //SQL準備
                            $objQuery = $objDBCA->sqlPrepare($sql);
                            //SQL発行
                            $r = $objQuery->sqlExecute();
                            //廃止フラグOFFの全レコード処理(FETCH)
                            $targetRow = "";
                            while ( $row = $objQuery->resultFetch() ){
                                $targetRow = $row;
                            }

                            //対象レコードが無い場合はcontinue
                            if($targetRow == ""){
                                continue;
                            }

                            //アクセス権チェック
                            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                            if($ret === false) {
                                // 例外処理へ
                                $strErrStepIdInFx="00000100";
                                $intErrorType = 1; //システムエラー
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            } else {
                                if($permission !== true) {
                                    //アクセス権限を持っていない場合
                                    $intErrorType = 2; //バリデーションエラー
                                    $strErrStepIdInFx="00000200";
                                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170030",array($conductorID)); //ConductorCall - 指定できないオペレーションIDが含まれています。(Conductor:{})
                                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                }
                            }
                        }
                    }
                }

                //callシンフォニーチェック
                if(!empty($aryCallSymphonyList[$conductorID])){
                    $arySymphonyNodeList = $aryCallSymphonyList[$conductorID];
                    foreach($arySymphonyNodeList as $node){
                        $callSymphonyID = $node['CONDUCTOR_CALL_CLASS_NO'];
                        $operationId = $node['OPERATION_NO_IDBH'];

                        //$fxVarsaryOptionOrderOverrideがある場合かつ対象が親コンダクタIDの場合、作業実行ページからオペレーションの付け替えを考慮する
                        if(!empty($fxVarsaryOptionOrderOverride) && $conductorID == $parentConductorID){
                            $nodeName = $node['NODE_NAME'];
                            $overrideOperationId = $fxVarsaryOptionOrderOverride[$nodeName]['OPERATION_NO_IDBH'];
                            if(isset($overrideOperationId)){
                                $operationId = $overrideOperationId;
                            }
                        }

                        //callシンフォニーおよびシンフォニー内で実行するMovementのアクセス権をチェック
                        $ret = $this->checkSymphonyAccessAuth($callSymphonyID, $fxVarsIntExecuteUserId, $conductorID);
                        if($ret[0] == false){
                            $strErrStepIdInFx="00000300";
                            $intErrorType = $ret[1];
                            $strExpectedErrMsgBodyForUI = $ret[3];
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }

                        //callシンフォニーに指定されたオペレーションのアクセス権をチェック
                        if($operationId != ""){
                            $sql = "SELECT *
                                    FROM C_OPERATION_LIST
                                    WHERE OPERATION_NO_UAPK = {$operationId}
                                    AND DISUSE_FLAG = 0";

                            //SQL準備
                            $objQuery = $objDBCA->sqlPrepare($sql);
                            //SQL発行
                            $r = $objQuery->sqlExecute();
                            //廃止フラグOFFの全レコード処理(FETCH)
                            $targetRow = "";
                            while ( $row = $objQuery->resultFetch() ){
                                $targetRow = $row;
                            }

                            //対象レコードが無い場合はcontinue
                            if($targetRow == ""){
                                continue;
                            }

                            //アクセス権チェック
                            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                            if($ret === false) {
                                // 例外処理へ
                                $strErrStepIdInFx="00000100";
                                $intErrorType = 1; //システムエラー
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            } else {
                                if($permission !== true) {
                                    //アクセス権限を持っていない場合
                                    $intErrorType = 2; //バリデーションエラー
                                    $strErrStepIdInFx="00000200";
                                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170031",array($conductorID)); //SymphonyCall - 指定できないオペレーションIDが含まれています。(Conductor:{})
                                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                }
                            }
                        }
                    }
                }

                //単発Movementチェック
                if(!empty($aryMovementList[$conductorID])){
                    $aryMovementNodeList = $aryMovementList[$conductorID];
                    foreach($aryMovementNodeList as $node){
                        $operationId = $node['OPERATION_NO_IDBH'];
                        $patternId = $node['PATTERN_ID'];

                        //$fxVarsaryOptionOrderOverrideがある場合かつ対象が親コンダクタIDの場合、作業実行ページからオペレーションの付け替えを考慮する
                        if(!empty($fxVarsaryOptionOrderOverride) && $conductorID == $parentConductorID){
                            $nodeName = $node['NODE_NAME'];
                            $overrideOperationId = $fxVarsaryOptionOrderOverride[$nodeName]['OPERATION_NO_IDBH'];
                            if(isset($overrideOperationId)){
                                $operationId = $overrideOperationId;
                            }
                        }

                        //Movementのアクセス権をチェック
                        $sql = "SELECT *
                                FROM C_PATTERN_PER_ORCH
                                WHERE PATTERN_ID = {$patternId}
                                AND DISUSE_FLAG = 0";

                        //SQL準備
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        //SQL発行
                        $r = $objQuery->sqlExecute();
                        //廃止フラグOFFの全レコード処理(FETCH)
                        $targetRow = "";
                        while ( $row = $objQuery->resultFetch() ){
                            $targetRow = $row;
                        }

                        //対象レコードが無い場合はcontinue
                        if($targetRow == ""){
                            continue;
                        }

                        //アクセス権チェック
                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                        if($ret === false) {
                            // 例外処理へ
                            $strErrStepIdInFx="00000300";
                            $intErrorType = 1; //システムエラー
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        } else {
                            if($permission !== true) {
                                //アクセス権限を持っていない場合
                                $intErrorType = 2; //バリデーションエラー
                                $strErrStepIdInFx="00000400";
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170032",array($conductorID)); //Movement - 指定できないMovementが含まれています。(Conductor:{})
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            }
                        }

                        //Movementに指定されたオペレーションのアクセス権をチェック
                        if($operationId != ""){
                            $sql = "SELECT *
                                    FROM C_OPERATION_LIST
                                    WHERE OPERATION_NO_UAPK = {$operationId}
                                    AND DISUSE_FLAG = 0";

                            //SQL準備
                            $objQuery = $objDBCA->sqlPrepare($sql);
                            //SQL発行
                            $r = $objQuery->sqlExecute();
                            //廃止フラグOFFの全レコード処理(FETCH)
                            $targetRow = "";
                            while ( $row = $objQuery->resultFetch() ){
                                $targetRow = $row;
                            }

                            //対象レコードが無い場合はcontinue
                            if($targetRow == ""){
                                continue;
                            }

                            //アクセス権チェック
                            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                            if($ret === false) {
                                // 例外処理へ
                                $strErrStepIdInFx="00000100";
                                $intErrorType = 1; //システムエラー
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            } else {
                                if($permission !== true) {
                                    //アクセス権限を持っていない場合
                                    $intErrorType = 2; //バリデーションエラー
                                    $strErrStepIdInFx="00000200";
                                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170033",array($conductorID)); //Movement - 指定できないオペレーションIDが含まれています。(Conductor:{})
                                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                }
                            }
                        }
                    }
                }
            }

            $boolRet = true;

        }catch(Exception $e){
            if( $intErrorType === null ) $intErrorType = 500;
            $tmpErrMsgBody = $e->getMessage();
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        }

        $retArray = array($boolRet, $intErrorType, $tmpErrMsgBody, $strExpectedErrMsgBodyForUI);

        return $retArray;
    }
//作業実行時、対象の実行コンダクタIDに紐づくすべてのcallコンダクタ/callシンフォニー/Movementについてのアクセス権をチェックする ----

//---- 作業実行時、対象の実行シンフォニーIDおよびシンフォニーに紐づくMovementについてのアクセス権をチェックする
    function checkSymphonyAccessAuth($fxVarsIntClassId, $fxVarsIntExecuteUserId, $fxvarsIntConductorClassId){
        $objDBCA = $this->getDBConnectAgent();
        $objMTS = $this->getMessageTemplateStorage();
        $objRBAC = new RoleBasedAccessControl($objDBCA);
        $ret  = $objRBAC->getAccountInfo($fxVarsIntExecuteUserId);

        $strFxName = '([FUNCTION]'.__FUNCTION__.')';
        $boolRet = false;
        $intErrorType = null;
        $strExpectedErrMsgBodyForUI = "";
        $strSysErrMsgBody = "";
        $tmpErrMsgBody = "";

        $parentSymphonyID = $fxVarsIntClassId; //チェック対象シンフォニーID
        $conductorID = $fxvarsIntConductorClassId;

        try{
            //シンフォニーのアクセス権をチェック
            $sql = "SELECT *
                    FROM C_SYMPHONY_CLASS_MNG
                    WHERE SYMPHONY_CLASS_NO = {$parentSymphonyID}
                    AND DISUSE_FLAG = 0";

            //SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            //SQL発行
            $r = $objQuery->sqlExecute();
            //廃止フラグOFFの全レコード処理(FETCH)
            $targetRow = "";
            while ( $row = $objQuery->resultFetch() ){
                $targetRow = $row;
            }

            //対象レコードが無い場合はreturn
            if($targetRow == ""){
                $boolRet = true;
                $intErrorType = 500;
                $retArray = array($boolRet, $intErrorType, $tmpErrMsgBody, $strExpectedErrMsgBodyForUI);
                return $retArray;
            }

            //アクセス権チェック
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                $intErrorType = 1; //システムエラー
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission !== true) {
                    //アクセス権限を持っていない場合
                    $intErrorType = 2;
                    $strErrStepIdInFx="00000200";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170034",array($conductorID)); //SymphonyCall - 指定できないSymphonyクラスIDが含まれています。(Conductor:{})
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }


            //シンフォニーに紐づくMovementのアクセス権をチェック
            $sql = "SELECT *
                    FROM C_MOVEMENT_CLASS_MNG
                    WHERE SYMPHONY_CLASS_NO = {$parentSymphonyID}
                    AND DISUSE_FLAG = 0";

            //SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);

            //SQL発行
            $r = $objQuery->sqlExecute();

            //廃止フラグOFFの全レコード処理(FETCH)
            $movClassMngRowList = array();
            while ( $row = $objQuery->resultFetch() ){
                array_push($movClassMngRowList, $row);
            }

            foreach($movClassMngRowList as $movClassMngRow){
                //対象MovementのIDを取得
                $patternId = $movClassMngRow['PATTERN_ID'];
                //対象Movementに指定されたオペレーションIDのアクセス権をチェック
                $operationId = $movClassMngRow['OPERATION_NO_IDBH'];
                if($operationId != ""){
                    $sql = "SELECT *
                            FROM C_OPERATION_LIST
                            WHERE OPERATION_NO_UAPK = {$operationId}
                            AND DISUSE_FLAG = 0";

                    //SQL準備
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    //SQL発行
                    $r = $objQuery->sqlExecute();
                    //廃止フラグOFFの全レコード処理(FETCH)
                    while ( $row = $objQuery->resultFetch() ){
                        $targetRow = $row;
                    }

                    //対象レコードが無い場合はcontinue
                    if($targetRow == ""){
                        continue;
                    }

                    //アクセス権チェック
                    list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                    if($ret === false) {
                        // 例外処理へ
                        $strErrStepIdInFx="00000100";
                        $intErrorType = 1; //システムエラー
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    } else {
                        if($permission !== true) {
                            //アクセス権限を持っていない場合
                            $intErrorType = 2;
                            $strErrStepIdInFx="00000200";
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170035",array($conductorID, $parentSymphonyID)); //SymphonyCall - Movementに指定できないオペレーションがIDが含まれています。(Conductor:{} Symphony:{})
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                    }
                }

                //対象Movementのアクセス権チェック
                $sql = "SELECT *
                        FROM C_PATTERN_PER_ORCH
                        WHERE PATTERN_ID = {$patternId}
                        AND DISUSE_FLAG = 0";

                //SQL準備
                $objQuery = $objDBCA->sqlPrepare($sql);
                //SQL発行
                $r = $objQuery->sqlExecute();
                //廃止フラグOFFの全レコード処理(FETCH)
                $targetRow = "";
                while ( $row = $objQuery->resultFetch() ){
                    $targetRow = $row;
                }

                //対象レコードが無い場合はcontinue
                if($targetRow == ""){
                    continue;
                }

                //アクセス権チェック
                list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                if($ret === false) {
                    // 例外処理へ
                    $strErrStepIdInFx="00000300";
                    $intErrorType = 1; //システムエラー
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                } else {
                    if($permission !== true) {
                        //アクセス権限を持っていない場合
                        $intErrorType = 2;
                        $strErrStepIdInFx="00000400";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170036",array($conductorID, $parentSymphonyID)); //SymphonyCall - 指定できないMovementが含まれています。(Conductor:{} Symphony:{})
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
            }

            $boolRet = true;

        }catch(Exception $e){
            if( $intErrorType === null ) $intErrorType = 500;
            $tmpErrMsgBody = $e->getMessage();
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        }

        $retArray = array($boolRet, $intErrorType, $tmpErrMsgBody, $strExpectedErrMsgBodyForUI);

        return $retArray;
    }
//作業実行時、対象の実行シンフォニーIDおよびシンフォニーに紐づくMovementについてのアクセス権をチェックする ----


//---- 個別指定オペレーションの取得
function checkCallLoopValidator( $intConductorclass,$arrOperationList=array() ){
    $getmode = 1;
    $retArray = $this->getInfoFromOneOfConductorClass($intConductorclass, 0,0,0,$getmode);#TERMINALあり
    $tmpNodeLists = $retArray[5];

    foreach ($tmpNodeLists as $key => $value) {
        //Movement
        if( $value["NODE_TYPE_ID"] == 3 ){
            if( $value["OPERATION_NO_IDBH"] != "")$arrOperationList[]=$value["OPERATION_NO_IDBH"];
        }
        //ConductorCall
        if( $value["NODE_TYPE_ID"] == 4 ){
            if( $value["OPERATION_NO_IDBH"] != "")$arrOperationList[]=$value["OPERATION_NO_IDBH"];
            $arrOperationList = $this->checkCallLoopValidator ( $value["CONDUCTOR_CALL_CLASS_NO"], $arrOperationList );
        }
        //SymphonyCall
        if( $value["NODE_TYPE_ID"] == 10 ){
            if( $value["OPERATION_NO_IDBH"] != "")$arrOperationList[]=$value["OPERATION_NO_IDBH"];
            $tmpRetBody = $this->getInfoFromOneOfSymphonyClasses($value["CONDUCTOR_CALL_CLASS_NO"]);
            $tmpMovLists = $tmpRetBody[5];

            foreach ($tmpMovLists as $tmpMovement) {
               if( $tmpMovement["OPERATION_NO_IDBH"] != "")$arrOperationList[]=$tmpMovement["OPERATION_NO_IDBH"];
            }
        }
    }

    return $arrOperationList;
}
// 個別指定オペレーションの取得 ----

//----シンフォニーIDおよびオペレーションNoからMovementのバリデーション(ConductorからのSymphony呼び出し)
    function chkSymphonyInstanceForConductor($intShmphonyClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride=null, $userId, $userName){

        // グローバル変数宣言
        global $g;

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
            "ACCESS_AUTH"=>"",
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
            "ACCESS_AUTH"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );
        // 変数定義----

        try{
            $objDBCA = $g['objDBCA'];
            $objMTS  = $g['objMTS'];
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $boolInTransactionFlag = true;

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
                #$register_tgt_row['MOVEMENT_INSTANCE_NO'] = $varRISeq;
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

                //Conductor(SymphonyCall)用処理
                if( isset( $aryValuePerOptionOrderKey['EXE_SKIP_FLAG'] ) !== true )$aryValuePerOptionOrderKey['EXE_SKIP_FLAG']='';
                if( isset( $aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH'] ) !== true )$aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH']='';

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
                #$register_tgt_row['SYMPHONY_INSTANCE_NO'] = $varSymphonyInstanceNo;
                $register_tgt_row['STATUS_ID']            = 1; //未実行[1]で
                $register_tgt_row['EXECUTION_USER']       = $userName;
                $register_tgt_row['DISUSE_FLAG']          = '0';
                $register_tgt_row['LAST_UPDATE_USER']     = $userId;

                $register_tgt_row['ACCESS_AUTH']          = $aryRowOfSymClassTable['ACCESS_AUTH'];

                //上位アクセス権継承
                if( array_key_exists( '__TOP_ACCESS_AUTH__' , $g ) === true ){
                    $register_tgt_row['ACCESS_AUTH'] = $g['__TOP_ACCESS_AUTH__'];
                }

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

                $intFocusIndex += 1;
            }

            // ----ムーブメントインスタンス登録処理後のチェック
            // ムーブメントの登録内容に不備がなかったことを確認
            if($MovementErrorMsg != ""){
                $strErrStepIdInFx="00002800";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $MovementErrorMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // ムーブメントインスタンス登録処理後のチェック----

            /////////////////////////////////////
            // (ここまで) ムーブメントインスタンスを登録する//
            /////////////////////////////////////
            $boolRet = true;

        }catch(Exception $e){

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

//----通知の取得 #312 
    function getNoticeInfo($strNoticeList,$intSearchMode=0){
        /////////////////////////////////////////////////////////////
        // 通知の取得                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfNotificationTable = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";
        //
        try{
            #/*
            $objDBCA = $this->getDBConnectAgent();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $objMTS = $this->getMessageTemplateStorage();

            #$tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($lc_db_model_ch,"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
            #$strSelectMaxLastUpdateTimestamp = "";#"CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";

            // ----全行および全行中、最後に更新された日時を取得する
            $arrayConfigForSelect = array(
                "JOURNAL_SEQ_NO" => "" ,
                "JOURNAL_REG_DATETIME" => "",
                "JOURNAL_ACTION_CLASS" => "",
                "NOTICE_ID" => "" ,
                "NOTICE_NAME" => "" ,
                "NOTICE_URL" => "" ,
                "HEADER" => "" ,
                "FIELDS" => "" ,
                "PROXY_URL" => "" ,
                "PROXY_PORT" => "" ,
                "FQDN" => "" ,
                "OTHER" => "" ,
                "SUPPRESS_START" => "" ,
                "SUPPRESS_END" => "" ,
                "ACCESS_AUTH" => "" ,
                "NOTE" => "" ,
                "DISUSE_FLAG" => "" ,
                "LAST_UPDATE_TIMESTAMP" => "" ,
                "LAST_UPDATE_USER" => "" ,
            );

            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO" => "" ,
                "JOURNAL_REG_DATETIME" => "",
                "JOURNAL_ACTION_CLASS" => "",
                "NOTICE_ID" => "" ,
                "NOTICE_NAME" => "" ,
                "NOTICE_URL" => "" ,
                "HEADER" => "" ,
                "FIELDS" => "" ,
                "PROXY_URL" => "" ,
                "PROXY_PORT" => "" ,
                "FQDN" => "" ,
                "OTHER" => "" ,
                "SUPPRESS_START" => "" ,
                "SUPPRESS_END" => "" ,
                "ACCESS_AUTH" => "" ,
                "NOTE" => "" ,
                "DISUSE_FLAG" => "" ,
                "LAST_UPDATE_TIMESTAMP" => "" ,
                "LAST_UPDATE_USER" => "" ,
            );
            $arrayValue = $arrayValueTmpl;

            $strSelectMode = "SELECT";
            $strSelectForUpdateLock = "";
            $strColumnIdForSearch = "NOTICE_ID";
            #*/
            $arrNotificationList = explode(",", $strNoticeList);

            foreach ($arrNotificationList as $NotificationId ) {
                
                $temp_array = array('WHERE'=>"{$strColumnIdForSearch} = :{$strColumnIdForSearch} AND DISUSE_FLAG IN ('0') {$strSelectForUpdateLock}");

                $retArray = makeSQLForUtnTableUpdate($lc_db_model_ch
                                                    ,$strSelectMode
                                                    ,"NOTICE_ID"
                                                    ,"C_CONDUCTOR_NOTICE_INFO"
                                                    ,"C_CONDUCTOR_NOTICE_INFO_JNL"
                                                    ,$arrayConfigForSelect
                                                    ,$arrayValue
                                                    ,$temp_array );
                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $arrayUtnBind[$strColumnIdForSearch] = $NotificationId;

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
                $aryRowOfTable = array();
                while ( $row = $objQueryUtn->resultFetch() ){
                    if( $intCount==0 ){
                        $aryRowOfTable = $row;
                    }
                    $intCount += 1;
                }
                //発見行だけループ----

                if( $intCount == 1 ){
                    $aryRowOfNotificationTable[] =  $aryRowOfTable;
                }else{
                    $aryErrMsgBody[$NotificationId] = $objMTS->getSomeMessage("ITABASEH-STD-171004",array($NotificationId) );//"対象の通知が見つかりません。レコードが廃止されている可能性があります。()"
                }
                unset($objQueryUtn);
                unset($retArray);
            }
            if( count($aryRowOfNotificationTable) == 0 ){
                $boolRet = false;
            }else{
                $boolRet = true;
            }

        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfNotificationTable);
        return $retArray;
    }
//通知の取得----

//----通知実行 #312 
    function execNotice($arrNoticeRows,$arrDefinedList=array() ){
        /////////////////////////////////////////////////////////////
        // 通知実行(curl_exec)                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRowOfexecNotification = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";

        ////////////////////////////////
        // ルートディレクトリを取得   //
        ////////////////////////////////       
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";

        try{
            $objMTS = $this->getMessageTemplateStorage();

            //作業No
            $execNo  = $arrDefinedList['__CONDUCTOR_INSTANCE_ID__'];
            //通知結果　ログ出力先
            $tmpNoticelogdir = $root_dir_path . "/uploadfiles/2100180006/NOTICE_LOG/" . sprintf('%010d', $execNo) ;
            $tmpNoticelogfile = "NoticeLog_". sprintf('%010d', $execNo) . ".log" ;
            $logPath = $tmpNoticelogdir . "/" . $tmpNoticelogfile;

            //ログ出力先チェック、ディレクトリ作成
            if( !is_dir($tmpNoticelogdir) ){
                #1907　umask退避-設定-戻し
                $mask = umask();
                umask(000);
                if ( mkdir($tmpNoticelogdir,0777,true) ){
                    chmod($tmpNoticelogdir, 0777);
                }
                umask($mask);
            }

            foreach( $arrNoticeRows as $tmpNotice ){

                $suppressflg = "";
                $nowDate = "";
                $suppressStartDate = "";
                $suppressEndDat = "";
                $subject = "";
                $arrNoticeResult = array();
                $strURL = "";

                $restApiResponse = "";
                $restApiResponseInfo = "";

                //日時(strtotime)
                $nowDate = strtotime( date('Y/m/d H:i:s.u') );
                $suppressStartDate = "";
                $suppressEndDate = "";

                //抑止期間ありなら比較用に
                if( array_key_exists('SUPPRESS_START', $tmpNotice) &&  array_key_exists('SUPPRESS_END', $tmpNotice) ){
                    $suppressStartDate = strtotime( $tmpNotice['SUPPRESS_START'] );
                    $suppressEndDate = strtotime( $tmpNotice['SUPPRESS_END'] );
                }

                //抑止期間設定あり( 開始-終了 / 開始 / 終了 )
                if( $suppressStartDate != "" && $suppressEndDate != "" ){
                    if( $suppressStartDate < $nowDate && $nowDate < $suppressEndDate  ){
                        //抑止期間中
                        $suppressflg = 1; //抑止
                    }
                }elseif( $suppressStartDate == "" && $suppressEndDate != "" && $nowDate < $suppressEndDate ) {
                    //　 （抑止開始無し） ～ 抑止終了 
                        $suppressflg = 1; //抑止
                }elseif( $suppressStartDate != "" && $suppressEndDate == "" && $nowDate > $suppressStartDate ){
                    //  抑止開始　～ （抑止終了無し）
                        $suppressflg = 1; //抑止       
                }

                //抑止フラグOFF時
                if( $suppressflg == "" ){
                    //通知名の初期化
                    $arrDefinedList['__NOTICE_NAME__'] = "";
                    if( isset($tmpNotice['NOTICE_NAME']) === true ){
                        $arrDefinedList['__NOTICE_NAME__'] = $tmpNotice['NOTICE_NAME'];
                    }else{
                        $arrDefinedList['__NOTICE_NAME__'] = $objMTS->getSomeMessage("ITABASEH-STD-171005");//"不明な通知"; 
                    }

                    $strURL = $tmpNotice['FQDN'] . $arrDefinedList['___TMP_URL___'];

                    //作業確認URLの初期化
                    $arrDefinedList['__JUMP_URL__'] = $strURL; 

                    //上書き禁止項目
                    $arrConstList = array(
                        "CURLOPT_URL",
                        "CURLOPT_HTTPHEADER",
                        "CURLOPT_POSTFIELDS",
                        "CURLOPT_PROXY",
                        "CURLOPT_PROXYPORT",
                        "CURLOPT_RETURNTRANSFER",
                    );

                    //基本設定値
                    $method = "POST";                
                    $Notificationurl = $tmpNotice['NOTICE_URL'];
                    $str_header = $tmpNotice['HEADER'];
                    $arr_header = json_decode($str_header);
                    if( is_array($arr_header) !== true ){
                        $arr_header = array( 
                            "Content-Type: application/json"
                        );
                    }

                    $str_post_data =  $tmpNotice['FIELDS'];
                    $proxy_url  = $tmpNotice['PROXY_URL'];
                    $proxy_port = $tmpNotice['PROXY_PORT'];

                    //予約変数置換
                    foreach ($arrDefinedList as $tmpkey => $tmpval) {
                        $str_post_data = str_replace( $tmpkey, $tmpval , $str_post_data);
                    }

                    //curl_setoptオプションリスト
                    $arrCurlPptList = array();
                    //初期設定固定値
                    $arrCurlPptList['CURLOPT_CUSTOMREQUEST']    = $method;  //初期値:POST
                    $arrCurlPptList['CURLOPT_HEADER']           = FALSE;    //true を設定すると、ヘッダの内容も出力します。
                    $arrCurlPptList['CURLOPT_SSL_VERIFYPEER']   = FALSE;    //false を設定すると、cURL はサーバー証明書の検証を行いません。
                    $arrCurlPptList['CURLOPT_SSL_VERIFYHOST']   = 0;        //0 は、名前をチェックしません。
                    $arrCurlPptList['CURLOPT_TIMEOUT']   = 5;               //cURL 関数の実行にかけられる時間の最大値。
                    $arrCurlPptList['CURLOPT_CONNECTTIMEOUT']   = 2;        //接続の試行を待ち続ける秒数。0 は永遠に待ち続けることを意味します。
                    $arrCurlPptList['CURLOPT_RETURNTRANSFER']   = TRUE;     //true を設定すると、curl_exec() の返り値を 文字列で返します。
                    $arrCurlPptList['CURLOPT_HTTPPROXYTUNNEL']  = TRUE;
                    //WEB入力項目
                    $arrCurlPptList['CURLOPT_URL']              = $Notificationurl;
                    $arrCurlPptList['CURLOPT_HTTPHEADER']       = $arr_header;
                    $arrCurlPptList['CURLOPT_POSTFIELDS']       = $str_post_data;
                    $arrCurlPptList['CURLOPT_PROXY']            = $proxy_url;
                    $arrCurlPptList['CURLOPT_PROXYPORT']        = $proxy_port;

                    $arrOtherOption = json_decode( $tmpNotice['OTHER'] ,true );

                    //形式不正の場合、その他無効
                    if( is_array($arrOtherOption) !== true ){
                        $arrOtherOption =array();
                    }

                    //その他のオプションを設定
                    foreach ($arrOtherOption as $curlkey => $curlval ) {
                        if( array_search($curlkey, $arrConstList) === false ){
                            if($curlval === null || $curlval === "" ){
                            }else{
                                $arrCurlPptList[$curlkey] =  $curlval;        
                            }
                        }else{
                            if(strpos($curlkey,'__FORCED__') !== false){
                                $curlkey = str_replace('__FORCED__','',$curlkey);
                                $arrCurlPptList[$curlkey] =  $curlval;
                            }
                        }
                    }

                    //CURL実行準備
                    $curl = curl_init();
                    //curl_setopt設定
                    foreach ($arrCurlPptList as $curlkey => $curlval) {
                        if($curlval === null || $curlval === "" ){
                        }else{  
                            curl_setopt($curl, constant($curlkey), $curlval );    
                        }
                    }
                    //CURL実行
                    $restApiResponse = curl_exec($curl);
                    $restApiResponseInfo = curl_getinfo($curl);

                    $aryRowOfexecNotification[$tmpNotice['NOTICE_ID']] = $restApiResponseInfo;

                    //通知ログ出力
                    $strNoticeStatus = $tmpNotice['NOTICE_ID'].":". $tmpNotice['NOTICE_NAME'] .",". $arrDefinedList['__STATUS_ID__'] .":". $arrDefinedList['__STATUS_NAME__'];
                    $subject = $objMTS->getSomeMessage("ITABASEH-STD-171000",array($strNoticeStatus) );//通知実行結果()
                    $arrNoticeResult = array(
                        "RETURN_MSG" => $restApiResponse,
                        "OPTION"  => $arrCurlPptList,
                        "RESSULT" => $aryRowOfexecNotification[$tmpNotice['NOTICE_ID']],
                        
                    );
                    error_log(print_r( date('Y-m-d H:i:s') . " " . $subject . "\n", true), 3, $logPath );

                    if( $arrNoticeResult != "" || $arrNoticeResult != array() ){
                        error_log(print_r( $arrNoticeResult, true ), 3, $logPath );
                    }
                     //CURL終了
                    curl_close($curl);

                }else{
                    $strNoticeStatus = $tmpNotice['NOTICE_ID'].":". $tmpNotice['NOTICE_NAME'] .",". $arrDefinedList['__STATUS_ID__'] .":". $arrDefinedList['__STATUS_NAME__'];
                    $aryRowOfexecNotification[$tmpNotice['NOTICE_ID']]  = $objMTS->getSomeMessage("ITABASEH-STD-171001",array($strNoticeStatus) );//通知を抑止しました。()
                    //通知ログ出力
                    $subject = $aryRowOfexecNotification[$tmpNotice['NOTICE_ID']];
                    error_log(print_r( date('Y-m-d H:i:s') . " " . $subject . "\n", true), 3, $logPath );
                }
            }
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfexecNotification);
        return $retArray;
    }
//通知実行----

//----通知取得、実行 #312 
    function getExecNotice($aryConInsInfo,$strNoticeList,$strNoticeStatusList){
        /////////////////////////////////////////////////////////////
        // 通知の取得、実行                                //
        /////////////////////////////////////////////////////////////
        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryResultNotice = array();

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";
        //
        try{
            $objMTS = $this->getMessageTemplateStorage();

            //通知情報取得
            $aryRetBody = $this->getNoticeInfo($strNoticeList);

            if( $aryRetBody[0] !== true ){
                $strErrMsg = $objMTS->getSomeMessage("ITABASEH-STD-171002" );//"通知対象がありません"

                if( $aryRetBody[2] != array() ){
                    $aryErrMsgBody = $aryRetBody[2];
                }
            }

            if( $aryRetBody[1] !== null ){
                $strErrMsg = $objMTS->getSomeMessage("ITABASEH-STD-171003" );//"通知対象が不正なため、通知処理をSKIPしました。";
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //通知処理設定
            $arrNoticeRows = $aryRetBody[4];
            
            //通知用予約変数：値の設定（デフォルト空）
            $arrDefinedList = array(
                "__CONDUCTOR_INSTANCE_ID__" => "",  //ConductorインスタンスID
                "__CONDUCTOR_NAME__"        => "",  //Conductorインスタンス名
                "__OPERATION_ID__"          => "",  //オペレーションID
                "__OPERATION_NAME__"        => "",  //オペレーション名
                "__STATUS_ID__"             => "",  //ステータスID
                "__STATUS_NAME__"           => "",  //ステータス名
                "__EXECUTION_USER__"        => "",  //実行ユーザー
                "__TIME_BOOK__"             => "",  //予約日時
                "__TIME_START__"            => "",  //開始日時
                "__TIME_END__"              => "",  //終了日時
                "__JUMP_URL__"              => "",  //作業確認URL
                "___TMP_URL___"              => "",  //作業確認URLの/defaultから
            );

            //通知用予約変数：値の設定
            if( isset($aryConInsInfo['CONDUCTOR_INSTANCE_NO']) === true ) $arrDefinedList['__CONDUCTOR_INSTANCE_ID__']  = $aryConInsInfo['CONDUCTOR_INSTANCE_NO'];
            if( isset($aryConInsInfo['I_CONDUCTOR_NAME'])      === true ) $arrDefinedList['__CONDUCTOR_NAME__']         = $aryConInsInfo['I_CONDUCTOR_NAME'];
            if( isset($aryConInsInfo['OPERATION_NO_UAPK'])     === true ) $arrDefinedList['__OPERATION_ID__']           = $aryConInsInfo['OPERATION_NO_UAPK'];
            if( isset($aryConInsInfo['I_OPERATION_NAME'])      === true ) $arrDefinedList['__OPERATION_NAME__']         = $aryConInsInfo['I_OPERATION_NAME'];
            if( isset($aryConInsInfo['STATUS_ID'])             === true ) $arrDefinedList['__STATUS_ID__']              = $aryConInsInfo['STATUS_ID'];
            if( isset($aryConInsInfo['STATUS_NAME'])           === true ) $arrDefinedList['__STATUS_NAME__']            = $aryConInsInfo['STATUS_NAME'];
            if( isset($aryConInsInfo['EXECUTION_USER'])        === true ) $arrDefinedList['__EXECUTION_USER__']         = $aryConInsInfo['EXECUTION_USER'];
            if( isset($aryConInsInfo['ABORT_FLAG_NAME'])       === true ) $arrDefinedList['__ABORT_FLAG__']             = $aryConInsInfo['ABORT_FLAG_NAME'];
            if( isset($aryConInsInfo['TIME_BOOK'])             === true ) $arrDefinedList['__TIME_BOOK__']              = date('Y/m/d H:i:s',  strtotime($aryConInsInfo['TIME_BOOK']));
            if( isset($aryConInsInfo['TIME_START'])            === true ) $arrDefinedList['__TIME_START__']             = date('Y/m/d H:i:s',  strtotime($aryConInsInfo['TIME_START']));
            if( isset($aryConInsInfo['TIME_END'])              === true ) $arrDefinedList['__TIME_END__']               = date('Y/m/d H:i:s',  strtotime($aryConInsInfo['TIME_END']));
            
            if( isset($aryConInsInfo['CONDUCTOR_INSTANCE_NO']) === true ){                            
                ###URL　作業確認URLのFQDN    
                $arrDefinedList['___TMP_URL___'] = "/default/menu/01_browse.php?no=2100180005&conductor_instance_id=".$aryConInsInfo['CONDUCTOR_INSTANCE_NO'];

            //通知実行
                $arrNoticeStatusList =  explode(',', $strNoticeStatusList);

                if( array_search( $aryConInsInfo['STATUS_ID'] , $arrNoticeStatusList ) !== false || $strNoticeStatusList !== "" && count($arrNoticeRows) != 0 ){
                    $aryRetBody = $this->execNotice($arrNoticeRows,$arrDefinedList);
                    if( $aryRetBody[2] != array() ){
                        $aryErrMsgBody = $aryRetBody[2];
                    }
                    $aryResultNotice = $aryRetBody[4];
                }

            }else{
                $strErrStepIdInFx="00000100";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryResultNotice);
        return $retArray;
    }
//通知取得、実行----


//----通知一覧を取得する #312 
    function getInfoOfNoticeList(){
        /////////////////////////////////////////////////////////////
        // 通知一覧を取得                                //
        /////////////////////////////////////////////////////////////

        global $g;

        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $rows = array();
        $user_id = "";
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";
        //
        try{
            $objDBCA = $this->getDBConnectAgent();
            $objMTS = $this->getMessageTemplateStorage();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $objRBAC = new RoleBasedAccessControl($objDBCA);

            #$g['login_id'] = "4";
            if( isset($g['login_id']) === true ){
                $user_id = $g['login_id'];
                $ret  = $objRBAC->getAccountInfo($user_id);
            }

            // 表示データをSELECT
            $sql =  " SELECT "
                   ." * "
                   ." FROM C_CONDUCTOR_NOTICE_INFO TAB_A "
                   ." WHERE TAB_A.DISUSE_FLAG='0' "
                   ."";

            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $rows = array();

            while($row = $objQuery->resultFetch()) {
                list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($row);

                if( $user_id != "" ){
                    if($ret === false) {
                        // 例外処理へ
                        $strErrStepIdInFx="00000100";
                        $intErrorType = 1; //システムエラー
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    } else {
                        if($permission !== true) {
                            //アクセス権限を持っていない場合 伏字対応
                            $row["NOTICE_NAME"] = $objMTS->getSomeMessage("ITAWDCH-STD-11102");
                        }
                    }
                }
                $rows[] = $row;
            }

            unset($objQuery);
            unset($r);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//通知一覧を取得する----

//----ステータスス一覧を取得する #312 
    function getInfoOfNoticeStatusList($getmode=""){
        /////////////////////////////////////////////////////////////
        // ステータス一覧を取得                                //
        /////////////////////////////////////////////////////////////

        global $g;

        $boolRet = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $rows = array();
        $user_id = "";
        $strStatusList = "";

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        $strSysErrMsgBody = "";
        //
        try{
            $objDBCA = $this->getDBConnectAgent();
            $objMTS = $this->getMessageTemplateStorage();
            $lc_db_model_ch = $objDBCA->getModelChannel();
            $objRBAC = new RoleBasedAccessControl($objDBCA);
            
            if( $getmode == "" ){
                $arrStatusList = array(3,4,5,11,6,7,8); //実行中,実行中(遅延),正常終了,警告終了,緊急停止,異常終了,想定外エラー
                $strStatusList = implode(",", $arrStatusList);
            }

            $rows = array();
            // ステータス表示順変更 #587
            if( $getmode == "" ){
                foreach ($arrStatusList as $tmpstatusid ) {
                    // 表示データをSELECT
                    $sql =  " SELECT "
                           ." * "
                           ." FROM B_SYM_EXE_STATUS TAB_A "
                           ." WHERE TAB_A.DISUSE_FLAG='0' "
                           ."";
                    if( $getmode == "" ){
                        $sql .=" AND TAB_A.SYM_EXE_STATUS_ID = {$tmpstatusid} ";;               
                    }

                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    
                    while($row = $objQuery->resultFetch()) {
                        $rows[] = $row;
                    }
                }                
            }else{
            
                // 表示データをSELECT
                $sql =  " SELECT "
                       ." * "
                       ." FROM B_SYM_EXE_STATUS TAB_A "
                       ." WHERE TAB_A.DISUSE_FLAG='0' "
                       ."";

                $objQuery = $objDBCA->sqlPrepare($sql);
                $r = $objQuery->sqlExecute();
                $rows = array();

                while($row = $objQuery->resultFetch()) {
                    $rows[] = $row;
                }
                            
            }

            unset($objQuery);
            unset($r);
            $boolRet = true;
        }
        catch(Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$rows);
        return $retArray;
    }
//ステータス一覧を取得する----


function getStatusFileInfo(){
    $arrDriversList = array(
        3 => array(
                    "table" => "B_ANSIBLE_IF_INFO",
                    "column"   => "ANSIBLE_STORAGE_PATH_ANS",
                    "path"   => "legacy/ns",
                ),
        4 => array(
                    "table" => "B_ANSIBLE_IF_INFO",
                    "column"   => "ANSIBLE_STORAGE_PATH_ANS",
                    "path"   => "pioneer/ns",
                ),
        5 => array(
                    "table" => "B_ANSIBLE_IF_INFO",
                    "column"   => "ANSIBLE_STORAGE_PATH_ANS",
                    "path"   => "legacy/rl",
                ),
        10 => array(
                    "table" => "B_TERRAFORM_IF_INFO",
                    "column"   => "",
                    "path"   => "",
                ),
    );
    return $arrDriversList ;
}
//ここまでConductor用----

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    // ここまで固有定義関数----                                //
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
}
//ここまで個別オーケストレータ/シンフォニー用クラス定義----
?>
