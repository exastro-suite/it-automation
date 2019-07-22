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
//    ・symphonyインスタンスの作成、更新をする機能を提供する。
//
//////////////////////////////////////////////////////////////////////

function symphonyInstanceBookCancel($fxVarsIntSymphonyInstanceId){
    // グローバル変数宣言
    global $g;
    //----RETSET[-PER-FX]
    $arrayResult = array();
    $arrayInfoForPrint = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strExpectedErrMsgBodyForUI = "";
    //RETSET[-PER-FX]----
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
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
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    
    $intSymphonyInstanceId = null;
    $boolExecuteContinue = true;
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyInstanceId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730102",array($objIntNumVali->getValidRule()));
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        //バリデーションチェック(入力形式)----
        $intSymphonyInstanceId = $fxVarsIntSymphonyInstanceId;
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----SYM-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-INSTANCE-シーケンスを掴む----
        
        $aryRetBody = getSingleSymphonyInfoFromSymphonyInstances($fxVarsIntSymphonyInstanceId, 1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            
            if( $aryRetBody[1] === 101 ){
                $intErrorType = 2;
                
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730103");
            }
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfSymInstanceTable = $aryRetBody[4];
        
        $update_tgt_row = $aryRowOfSymInstanceTable;
        
        // ----シンフォニー-INSTANCEを更新
        if ( $aryRowOfSymInstanceTable['STATUS_ID'] == '2' && time() < strtotime($aryRowOfSymInstanceTable['TIME_BOOK']) ){
            //----予約時刻を過ぎていない場合、予約取消のみ
            $intDetailType = 0;
            $update_tgt_row['STATUS_ID']          = 9; // 予約取消
            //予約時刻を過ぎていない場合、予約取消のみ----
        }
        else{
            //----（予約時刻を過ぎ、）ステータスが予約でない場合
            if( $aryRowOfSymInstanceTable['ABORT_EXECUTE_FLAG'] == 2 ){
                //----緊急停止を発令済、の場合
                $boolExecuteContinue = false;
                //緊急停止を発令済、の場合----
            }
            else{
                //----緊急停止を未発令、の場合
                
                // 緊急停止発令フラグをON
                $intDetailType = 1;
                $update_tgt_row['ABORT_EXECUTE_FLAG'] = 2; // 発令済
                
                //緊急停止を未発令、の場合----
            }
            //（予約時刻を過ぎ、）ステータスが予約でない場合----
        }
        
        if( $boolExecuteContinue === true ){
            $update_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
            
            $arrayConfigForIUD = $arrayConfigForSymInsIUD;
            $tgtSource_row = $update_tgt_row;
            $sqlType = "UPDATE";
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"SYMPHONY_INSTANCE_NO"
                                                ,"C_SYMPHONY_INSTANCE_MNG"
                                                ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                
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
                $strErrStepIdInFx="00000700";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
            
            // シンフォニー-INSTANCEを更新----
        }
        
        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102030");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102010");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102040");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102020");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    
    $arrayInfoForPrint = array('SYMPHONY_INSTANCE_ID'=>$intSymphonyInstanceId);
    $arrayResult = array($arrayInfoForPrint
                        ,$intErrorType
                        ,$aryErrMsgBody
                        ,$strErrMsg
                        ,$strExpectedErrMsgBodyForUI
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//予約取り消し----

//----緊急停止発令フラグの有効化
function symphonyInstanceScram($fxVarsIntSymphonyInstanceId){
    // グローバル変数宣言
    global $g;
    //----RETSET[-PER-FX]
    $arrayResult = array();
    $arrayInfoForPrint = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strExpectedErrMsgBodyForUI = "";
    //RETSET[-PER-FX]----
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
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
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    $boolScramExecute = true;
    $boolMovUpdateFlag = false;
    $intSymphonyInstanceId = null;
    $boolExecuteContinue = true;
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyInstanceId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730202",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        //バリデーションチェック(入力形式)----
        $intSymphonyInstanceId = $fxVarsIntSymphonyInstanceId;
        
        //////////////////////////////////////////////////
        // (ここから)緊急停止フラグを有効化する[最優先] //
        //////////////////////////////////////////////////
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----SYM-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-INSTANCE-シーケンスを掴む----
        
        //----バリデーションチェック(実質評価)
        $aryRetBody = getSingleSymphonyInfoFromSymphonyInstances($intSymphonyInstanceId, 1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            
            if( $aryRetBody[1] === 101 ){
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730203");
            }
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(実質評価)----
        $aryRowOfSymInstanceTable = $aryRetBody[4];
        $update_tgt_row = $aryRowOfSymInstanceTable;
        
        // ----シンフォニー-INSTANCEを更新
        
        if( $aryRowOfSymInstanceTable['ABORT_EXECUTE_FLAG'] == 2 ){
            $boolExecuteContinue = false;
        }
        
        if( $boolExecuteContinue === true ){
            $update_tgt_row['ABORT_EXECUTE_FLAG']   = 2; //発令済
            $update_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
            
            $arrayConfigForIUD = $arrayConfigForSymInsIUD;
            $tgtSource_row = $update_tgt_row;
            $sqlType = "UPDATE";
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"SYMPHONY_INSTANCE_NO"
                                                ,"C_SYMPHONY_INSTANCE_MNG"
                                                ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                
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
                $strErrStepIdInFx="00000700";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
        }
        
        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----
        
        //////////////////////////////////////////////////
        // (ここまで)緊急停止フラグを有効化する[最優先] //
        //////////////////////////////////////////////////
        
        ////////////////////////////////////////////////////////////////////
        // (ここから)現在のムーブメントを調べて、緊急停止をリクエストする //
        ////////////////////////////////////////////////////////////////////
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000900";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----MOV-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // MOV-INSTANCE-シーケンスを掴む----
        
        $aryRetBody = getSingleSymphonyInfoFromMovementInstances($intSymphonyInstanceId, 1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001100";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfMovInstanceTable = $aryRetBody[4];
        unset($aryRetBody);
        
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA,$g);
        
        $aryRetBody = $objOLA->getSymphonyStatusFromMovement($aryRowOfMovInstanceTable);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryStatusInfo = $aryRetBody[0];
        unset($aryRetBody);
        $rowOfFocusMovement = $aryStatusInfo['FOCUS_MOVEMENT_ROW'];
        
        $aryRetBody = $objOLA->getLiveOrchestratorFromMaster();
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001300";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryOrcListRow = $aryRetBody[0];
        
        $boolOrchestratorExists = false;
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            if( $varOrcId==$rowOfFocusMovement['I_ORCHESTRATOR_ID'] ){
                $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
                $boolOrchestratorExists = true;
                break;
            }
        }
        
        if( $boolOrchestratorExists === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001400";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        if( strlen($rowOfFocusMovement['EXECUTION_NO']) === 0 ){
            //----オーケストレータ側テーブルに、まだレコードが存在していない場合は、実際の、緊急停止の発令はしない。
            $boolScramExecute = false;
            //オーケストレータ側テーブルに、まだレコードが存在していない場合は、実際の、緊急停止の発令はしない。----
        }
        else if( $rowOfFocusMovement['ABORT_RECEPTED_FLAG'] === '2' ){
            //----すでに緊急停止発令受理フラグが、ムーブメントに立っている場合は、実際の、緊急停止の発令はしない。
            $boolScramExecute = false;
            //すでに緊急停止発令受理フラグが、ムーブメントに立っている場合は、実際の、緊急停止の発令はしない。----
        }
        
        if( $boolScramExecute === true ){
            $aryRetBody = $objOLA->srcamExecute($rowOfFocusMovement['I_ORCHESTRATOR_ID'], $rowOfFocusMovement['EXECUTION_NO']);
            if( $aryRetBody[1] === null && $aryRetBody[0] === 0 ){
                //----正常に受け付けられたので、ムーブメント更新フラグをONにする
                $boolMovUpdateFlag = true;
                //正常に受け付けられたので、ムーブメント更新フラグをONにする----
            }
            else{
                //----正常に受け付けられなかった場合
                $boolScramExecute = false;
                if( $aryRetBody[1] !== null ){
                    $error_info = $aryRetBody[3];
                    
                    if( 0 < strlen($error_info) ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00001500";
                        
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
                //正常に受け付けられなかった場合----
            }
        }
        
        if( $boolMovUpdateFlag===true ){
            //----ムーブメントを、「緊急停止REST-API受付完了確認フラグ」を「受付済」にするために更新する
            $update_tgt_row = $rowOfFocusMovement;
            
            $update_tgt_row['ABORT_RECEPTED_FLAG']  = 2; //確認済
            $update_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
            
            $arrayConfigForIUD = $arrayConfigForMovInsIUD;
            $tgtSource_row = $update_tgt_row;
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                            ,$sqlType
                                            ,"MOVEMENT_INSTANCE_NO"
                                            ,"C_MOVEMENT_INSTANCE_MNG"
                                            ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                            ,$arrayConfigForIUD
                                            ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001600";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001700";
                
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
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
            //ムーブメントを、「緊急停止REST-API受付完了確認フラグ」を「受付済」にするために更新する----
        }
        
        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001900";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----
        
        ////////////////////////////////////////////////////////////////////
        // (ここまで)現在のムーブメントを調べて、緊急停止をリクエストする //
        ////////////////////////////////////////////////////////////////////
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102050");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102030");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102060");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102040");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    
    $arrayInfoForPrint = array('SYMPHONY_INSTANCE_ID'=>$intSymphonyInstanceId);
    $arrayResult = array($arrayInfoForPrint
                        ,$intErrorType
                        ,$aryErrMsgBody
                        ,$strErrMsg
                        ,$strExpectedErrMsgBodyForUI
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//緊急停止発令フラグの有効化----

//----保留ポイントの解除
function movementInstanceHoldRelease($fxVarsIntSymphonyInstanceId,$fxVarsIntSeqNo){
    // グローバル変数宣言
    global $g;
    //----RETSET[-PER-FX]
    $arrayResult = array();
    $arrayInfoForPrint = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strExpectedErrMsgBodyForUI = "";
    //RETSET[-PER-FX]----
    
    $intControlDebugLevel01=250;
    
    $intSymphonyInstanceId = null;
    $intSeqNo = null;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
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
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    $boolExecuteContinue = true;
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyInstanceId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            //$strExpectedErrMsgBodyForUI = "SymphonyインスタンスID：".$objIntNumVali->getValidRule();
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730302",array($objIntNumVali->getValidRule()));
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intSymphonyInstanceId = $fxVarsIntSymphonyInstanceId;
        
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSeqNo) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730303",array($objIntNumVali->getValidRule()));
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intSeqNo = $fxVarsIntSeqNo;
        //バリデーションチェック(入力形式)----
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----MOV-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // MOV-INSTANCE-シーケンスを掴む----
        
        $aryRetBody = getInfoFromOneOfSymphonyInstances($intSymphonyInstanceId,1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000500";
            
            if( $aryRetBody[1] === 101 ){
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730304");
            }
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $aryRowOfSymInstanceTable = $aryRetBody[4];
        $aryRowOfMovInstanceTable = $aryRetBody[5];
        
        $update_tgt_row = null;
        $intFocusIndex = 0;
        foreach($aryRowOfMovInstanceTable as $arySingleRowOfMovClassTable ){
            $check_target_row = $arySingleRowOfMovClassTable;
            if( $intFocusIndex + 1 != $check_target_row['I_MOVEMENT_SEQ'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if( $intSeqNo == $check_target_row['I_MOVEMENT_SEQ'] ){
                $update_tgt_row = $arySingleRowOfMovClassTable;
            }
            $intFocusIndex += 1;
        }
        
        if ( $arySingleRowOfMovClassTable === null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        // ----ムーブメントを更新
        switch($aryRowOfSymInstanceTable['STATUS_ID']){
            case 1: //未実行
            case 2: //未実行(予約)
            case 3: //実行中
            case 4: //実行中(遅延)
                if( $update_tgt_row['RELEASED_FLAG'] == 2 ){
                    //----解除済の場合なので、処理を継続しない
                    $boolExecuteContinue = false;
                    //解除済の場合なので、処理を継続しない----
                }
                break;
            default:
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = 2;
                $strErrStepIdInFx="00000800";
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730305");
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                break;
        }
        
        if( $boolExecuteContinue === true ){
            $update_tgt_row['RELEASED_FLAG']        = 2; //解除済
            $update_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
            
            $arrayConfigForIUD = $arrayConfigForMovInsIUD;
            $tgtSource_row = $update_tgt_row;
            $sqlType = "UPDATE";
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"MOVEMENT_INSTANCE_NO"
                                                ,"C_MOVEMENT_INSTANCE_MNG"
                                                ,"C_MOVEMENT_INSTANCE_MNG_JNL"
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
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
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
            
            $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
            if( $retArray01[0] !== true || $retArray02[0] !== true ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001100";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
        }
        
        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102070");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102050");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102080");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102060");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $arrayInfoForPrint = array('SYMPHONY_INSTANCE_ID'=>$intSymphonyInstanceId
                              ,'MOVEMENT_SEQ_NO'=>$intSeqNo
                               );
    $arrayResult = array($arrayInfoForPrint
                        ,$intErrorType
                        ,$aryErrMsgBody
                        ,$strErrMsg
                        ,$strExpectedErrMsgBodyForUI
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//保留ポイントの解除----

//----ある１のシンフォニーのインスタンス状態を表示する
function symphonyInstancePrint($fxVarsIntSymphonyInstanceId){
    // グローバル変数宣言
    global $g;
    //----RETSET[-PER-FX]
    $arrayResult = array();
    $arrayInfoForPrint = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strExpectedErrMsgBodyForUI = "";
    //RETSET[-PER-FX]----
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intSymphonyClassId = null;
    $arySymphonySource = array();
    $aryMovementInsData = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";    
    
    try{
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyInstanceId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730402",array($objIntNumVali->getValidRule()));
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intSymphonyInstanceId = $fxVarsIntSymphonyInstanceId;
        //バリデーションチェック(入力形式)----
        
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA,$g);
        
        //----シンフォニーが存在するか？
        
        //----symphony_ins_noごとに作業パターンの流れを収集する
        $aryRetBody = getInfoFromOneOfSymphonyInstances($intSymphonyInstanceId,0);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            if( $aryRetBody[1] === 101 ){
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5730403");
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $aryRowOfSymInstanceTable = $aryRetBody[4];
        $aryRowOfMovInstanceTable = $aryRetBody[5];
        
        $intSymphonyClassId = $aryRowOfSymInstanceTable['I_SYMPHONY_CLASS_NO'];
        
        $aryRetBody = $objOLA->getInfoOfOneOperation($aryRowOfSymInstanceTable['OPERATION_NO_UAPK']);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfOperationTable = $aryRetBody[4];
        
        //----オーケストレータ情報の収集
        
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA,$g);
        $aryRetBody = $objOLA->getLiveOrchestratorFromMaster();
        
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryOrcListRow = $aryRetBody[0];
        
        $aryPatternListPerOrc = array();
        //----存在するオーケストレータ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
            $aryRetBody = $objOLA->getLivePatternList($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRow = $aryRetBody[0];
            
            //----オーケストレータカラーを取得
            $aryRetBody = $objOLA->getThemeColorName($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strThemeColor = $aryRetBody[0];
            //オーケストレータカラーを取得----
            
            $aryPatternListPerOrc[$varOrcId]['ThemeColor'] = $strThemeColor;
        }
        //存在するオーケストレータ分回る----
        
        //オーケストレータ情報の収集----
        
        //----作業パターンの収集
        
        $aryRetBody = $objOLA->getLivePatternFromMaster();
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryPatternList = $aryRetBody[0];
        
        //作業パターンの収集----
        
        $aryRetBody = $objOLA->getSymphonyStatusFromMovement($aryRowOfMovInstanceTable);
        if( $aryRetBody[1] !== null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $intMovementLength = $aryRetBody[0]['MOVEMENT_LENGTH'];
        $intFocusMovementSeq = $aryRetBody[0]['FOCUS_MOVEMENT_SEQ'];
        
        //----シンフォニー(インスタンス)情報を固める
        $arySymphonySource = array('SYMPHONY_INSTANCE_ID'=>$intSymphonyInstanceId
                                  ,'I_SYMPHONY_CLASS_NO'=>$aryRowOfSymInstanceTable['I_SYMPHONY_CLASS_NO']
                                  ,'I_SYMPHONY_NAME'=>$aryRowOfSymInstanceTable['I_SYMPHONY_NAME']
                                  ,'I_DESCRIPTION'=>$aryRowOfSymInstanceTable['I_DESCRIPTION']
                                  ,'STATUS_ID'=>$aryRowOfSymInstanceTable['STATUS_ID']
                                  ,'EXECUTION_USER'=>$aryRowOfSymInstanceTable['EXECUTION_USER']
                                  ,'ABORT_EXECUTE_FLAG'=>$aryRowOfSymInstanceTable['ABORT_EXECUTE_FLAG']
                                  ,'OPERATION_NO_UAPK'=>$aryRowOfSymInstanceTable['OPERATION_NO_UAPK']
                                  ,'OPERATION_NO_IDBH'=>$aryRowOfOperationTable['OPERATION_NO_IDBH']
                                  ,'OPERATION_NAME'=>$aryRowOfOperationTable['OPERATION_NAME']
                                  ,'TIME_BOOK'=>$aryRowOfSymInstanceTable['TIME_BOOK']
                                  ,'TIME_START'=>$aryRowOfSymInstanceTable['TIME_START']
                                  ,'TIME_END'=>$aryRowOfSymInstanceTable['TIME_END']
                                  ,'MOVEMENT_LENGTH'=>$intMovementLength
                                  ,'FOCUS_MOVEMENT'=>$intFocusMovementSeq
        );
        //シンフォニー(インスタンス)情報を固める----
        
        //----ムーブメント情報を固める
        
        //----発見行だけループ
        $aryMovementInsData = array();
        $intCount = 0;
        foreach( $aryRowOfMovInstanceTable as $row ){
            $aryClassItems = array();
            $varOrcIdFromMovInstanceTable     = $row['I_ORCHESTRATOR_ID'];
            $varPatternIdFromMovInstanceTable = $row['I_PATTERN_ID'];
            
            //----ここからクラスと同じ情報項目
            
            // オーケストレータID
            $aryClassItems['ORCHESTRATOR_ID']           = $varOrcIdFromMovInstanceTable;
            
            // 作業パターンID
            $aryClassItems['PATTERN_ID']                = $varPatternIdFromMovInstanceTable;
            
            //----作業パターンの名前
            $strPatternName = "";
            if( array_key_exists($varPatternIdFromMovInstanceTable,$aryPatternList) === true ){
                //----作業パターンが存在している
                if( $aryPatternList[$varPatternIdFromMovInstanceTable]['ITA_EXT_STM_ID']==$varOrcIdFromMovInstanceTable ){
                    //----オーケストレータも同じ
                    $strPatternName = $aryPatternList[$varPatternIdFromMovInstanceTable]['PATTERN_NAME'];
                    //オーケストレータも同じ----
                }
                //作業パターンが存在している----
            }
            if( $strPatternName=="" ){
                $strPatternName = $objMTS->getSomeMessage("ITABASEH-ERR-5730404",array($row['I_MOVEMENT_CLASS_NO']));
            }
            $aryClassItems['PATTERN_NAME']              = $strPatternName; //htmlspecialchars
            
            // テーマカラー
            $aryClassItems['THEME_COLOR'] = $aryPatternListPerOrc[$varOrcIdFromMovInstanceTable]['ThemeColor'];
            
            // 楽章番号
            $aryClassItems['MOVEMENT_SEQ']              = $row['I_MOVEMENT_SEQ'];
            
            // 説明
            $aryClassItems['DESCRIPTION']               = $row['I_DESCRIPTION']; //htmlspecialchars
            
            //----保留ポイントの有無
            if( $row['I_NEXT_PENDING_FLAG'] == '1' ){
                // 保留ポイントあり
                $varNextPendingFlag = 'checkedValue';
            }
            else if( $row['I_NEXT_PENDING_FLAG'] == '2' ){
                // 保留ポイントなし
                $varNextPendingFlag = '';
            }
            else{
                // ----存在しないはずの値
                
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                
                //存在しないはずの値-----
            }
            $aryClassItems['NEXT_PENDING']              = $varNextPendingFlag;
            //保留ポイントの有無----
            
            //ここまでクラスと同じ情報項目----
            
            //----ここからインスタンス固有の情報項目
            
            //----ステータス
            $aryInstanceItems = array();
            $aryInstanceItems['STATUS']                 = $row['STATUS_ID'];
            //ステータス----
            
            //----保留解除済フラグの状態
            if( $row['RELEASED_FLAG'] == '1' || $row['RELEASED_FLAG'] == '2' || strlen($row['RELEASED_FLAG']) == 0 ){
                //----未解除(1)または解除済(2)
                if( $row['I_NEXT_PENDING_FLAG'] == '1' && strlen($row['RELEASED_FLAG']) != 0 ){
                    // 保留ポイントあり(1)、で、存在する値(1,2)の場合。
                    $varReleasedFlag                    = $row['RELEASED_FLAG'];
                }
                else if( $row['I_NEXT_PENDING_FLAG'] == '2'){
                    // 保留ポイントなし(2)、で、値がNULLの場合。
                    $varReleasedFlag = '';
                }
                else{
                    // エラーフラグをON
                    $strErrStepIdInFx="00000900";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                //未解除(1)または解除済(2)----
            }
            else{
                // エラーフラグをON
                $strErrStepIdInFx="00001000";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryInstanceItems['RELEASED']               = $varReleasedFlag;
            
            //----実行インスタンスNo
            $varOrchInstanceId                          = $row['EXECUTION_NO'];
            $aryInstanceItems['EXECUTION_NO']           = $varOrchInstanceId;
            //実行インスタンスNo----
            
            //----ジャンプ用(ITA-ROOTからの)相対URL
            $aryJumpInfo = $objOLA->getJumpMonitorUrl($varOrcIdFromMovInstanceTable,$varOrchInstanceId);
            
            $aryInstanceItems['JUMP']                   = $aryJumpInfo[0];
            //ジャンプ用(ITA-ROOTからの)相対URL----
            
            $aryInstanceItems['ABORT_RECEPTED']         = $row['ABORT_RECEPTED_FLAG'];
            
            $aryInstanceItems['SKIP']                   = $row['EXE_SKIP_FLAG'];
            
            $aryInstanceItems['TIME_START']             = $row['TIME_START'];
            $aryInstanceItems['TIME_END']               = $row['TIME_END'];
            
            $aryInstanceItems['OPERATION_ID']           = $row['OVRD_I_OPERATION_NO_IDBH']; //htmlspecialchars
            $aryInstanceItems['OPERATION_NAME']         = $row['OVRD_I_OPERATION_NAME']; //htmlspecialchars
            
            $aryMovementInsData[] = array('CLASS_ITEM'=>$aryClassItems
                                         ,'INS_ITEM'=>$aryInstanceItems
                                          );
            unset($aryInstanceItems);
            unset($aryClassItems);
            //保留解除済フラグの状態----
            //ここからインスタンス固有の情報項目----
        }
        //発見行だけループ----
        
        //ムーブメント情報を固める----
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $arrayInfoForPrint = array('SYMPHONY_CLASS_ID'=>$intSymphonyClassId
                              ,'SYMPHONY_INSTANCE_INFO'=>$arySymphonySource
                              ,'MOVEMENTS'=>$aryMovementInsData
                               );
    $arrayResult = array($arrayInfoForPrint
                        ,$intErrorType
                        ,$aryErrMsgBody
                        ,$strErrMsg
                        ,$strExpectedErrMsgBodyForUI
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//ある１のシンフォニーのインスタンス状態を表示する----

//----ある１のシンフォニーインスタンスの、シンフォニー部分、ムーブメント部分の情報を取得する
function getInfoFromOneOfSymphonyInstances($intSymphonyInstanceId, $intMode=0){
    global $g;
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryRowOfSymInstanceTable = array();
    $aryRowOfMovInstanceTable = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        $aryRetBody = getSingleSymphonyInfoFromSymphonyInstances($intSymphonyInstanceId, $intMode);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            if( $aryRetBody[1] === 101 ){
                //----１行も発見できなかった場合
                $intErrorType = 101;
                //１行も発見できなかった場合----
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfSymInstanceTable = $aryRetBody[4];
        
        $aryRetBody = getSingleSymphonyInfoFromMovementInstances($intSymphonyInstanceId, $intMode);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfMovInstanceTable = $aryRetBody[4];
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfSymInstanceTable,$aryRowOfMovInstanceTable);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ある１のシンフォニーインスタンスの、シンフォニー部分、ムーブメント部分の情報を取得する----

//----シンフォニー(インスタンス)管理テーブルから、ある１のシンフォニー情報を取得する
function getSingleSymphonyInfoFromSymphonyInstances($intSymphonyInstanceId, $intMode=0){
    global $g;
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryRowOfSymInstanceTable = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
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
    
    $strSysErrMsgBody = "";
    
    try{
        $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
        $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
        
        // ----全行および全行中、最後に更新された日時を取得する
        $arrayConfigForSelect = $arrayConfigForSymInsIUD;
        $arrayConfigForSelect[$strSelectMaxLastUpdateTimestamp] = "";
        
        $arrayValue = $arraySymInsValueTmpl;
        $arrayValue[$strSelectMaxLastUpdateTimestamp]="";
        
        $strSelectMode = "SELECT";
        $strWhereDisuseFlag = "('0')";
        $strOrderByArea = "";
        if( $intMode === 0 ){
            $strWhereDisuseFlag = "('0')";
        }
        else if( $intMode === 1 ){
            $strWhereDisuseFlag = "('0')";
            
            //----更新用のため、ロック
            $strSelectMode = "SELECT FOR UPDATE";
            //更新用のため、ロック----
        }
        
        $temp_array = array('WHERE'=>"SYMPHONY_INSTANCE_NO = :SYMPHONY_INSTANCE_NO AND DISUSE_FLAG IN {$strWhereDisuseFlag} ");
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                            $strSelectMode,
                                            "SYMPHONY_INSTANCE_NO",
                                            "C_SYMPHONY_INSTANCE_MNG",
                                            "C_SYMPHONY_INSTANCE_MNG_JNL",
                                            $arrayConfigForSelect,
                                            $arrayValue,
                                            $temp_array );
        
        if( $retArray[0] === false ){
            // エラーフラグをON
            $strErrStepIdInFx="00000100";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $arrayUtnBind['SYMPHONY_INSTANCE_NO'] = $intSymphonyInstanceId;
        
        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        if( $retArray01[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objQueryUtn =& $retArray01[3];
        
        //----発見行だけループ
        $intCount = 0;
        $aryRowOfSymInstanceTable = array();
        while ( $row = $objQueryUtn->resultFetch() ){
            if( $intCount==0 ){
                $aryRowOfSymInstanceTable = $row;
            }
            $intCount += 1;
        }
        //発見行だけループ----
        unset($objQueryUtn);
        unset($retArray01);
        
        if( $intCount !== 1 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            if( $intCount === 0 ){
                //----廃止されている場合もあるので、想定内のエラー
                $intErrorType = 101;
                //廃止されている場合もあるので、想定内のエラー----
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        //シンフォニーが存在するか？----
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfSymInstanceTable);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//シンフォニー(インスタンス)管理テーブルから、ある１のシンフォニー情報を取得する----

//----ムーブメント(インスタンス)管理テーブルから、ある１のシンフォニーに紐づくムーブメント情報を取得する
function getSingleSymphonyInfoFromMovementInstances($intSymphonyInstanceId, $intMode=0){
    global $g;
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryRowOfMovInstanceTable = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $arrayConfigForMovInsSelect = array(
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
    
    $arrayMovSymInsValueTmpl = array(
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
    
    $strSysErrMsgBody = "";
    
    try{
        $strSelectMode = "SELECT";
        $strWhereDisuseFlag = "('0')";
        $strOrderByArea = " ORDER BY I_MOVEMENT_SEQ ASC";
        if( $intMode === 0 ){
            //----活性化しているレコードだけ、ロックせずセレクト
            $strWhereDisuseFlag = "('0')";
            //活性化しているレコードだけ、ロックせずセレクト----
        }
        else if( $intMode === 1 ){
            //----更新するため、廃止されているムーブメントレコードも拾う
            $strWhereDisuseFlag = "('0','1')";
            //更新するため、廃止されているムーブメントレコードも拾う----
            
            //----更新用のため、ロック
            $strSelectMode = "SELECT FOR UPDATE";
            //更新用のため、ロック----
        }
        
        $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
        $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
        
        //----各ムーブメントの情報収集
        $arrayConfigForSelect = $arrayConfigForMovInsSelect;
        $arrayConfigForSelect[$strSelectMaxLastUpdateTimestamp] = "";
        
        $arrayValueTmpl = $arrayMovSymInsValueTmpl;
        $arrayValueTmpl[$strSelectMaxLastUpdateTimestamp] = "";
        
        $arrayValue = $arrayValueTmpl;
        
        $temp_array = array('WHERE'=>"SYMPHONY_INSTANCE_NO = :SYMPHONY_INSTANCE_NO AND DISUSE_FLAG IN {$strWhereDisuseFlag} {$strOrderByArea}");
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                            ,$strSelectMode
                                            ,"MOVEMENT_INSTANCE_NO"
                                            ,"C_MOVEMENT_INSTANCE_MNG"
                                            ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                            ,$arrayConfigForSelect
                                            ,$arrayValue
                                            ,$temp_array);
        
        if( $retArray[0] === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $arrayUtnBind['SYMPHONY_INSTANCE_NO'] = $intSymphonyInstanceId;
        
        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        if( $retArray01[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objQueryUtn =& $retArray01[3];
        
        //----ムーブメントの分だけループする
        $intCount = 0;
        while ( $row = $objQueryUtn->resultFetch() ){
            $aryRowOfMovInstanceTable[] = $row;
        }
        unset($objQueryUtn);
        unset($retArray01);
        //ムーブメントの分だけループする----
        $boolRet = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryRowOfMovInstanceTable);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ムーブメント(インスタンス)管理テーブルから、ある１のシンフォニーに紐づくムーブメント情報を取得する----

//----インスタンスを作成する
function symphonyInstanceConstuct($intShmphonyClassId, $intOperationNoUAPK, $strPreserveDatetime, $strOptionOrderStream, $aryOptionOrderOverride=null){
    // グローバル変数宣言
    global $g;
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $intSymphonyInstanceId = null;
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
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
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        
        //----シンフォニーCLASSが存在するか？
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($intShmphonyClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733102",array($objIntNumVali->getValidRule()));
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($intOperationNoUAPK) === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733103",array($objIntNumVali->getValidRule()));
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        
        if( 0 < strlen($strPreserveDatetime) ){
            $tmpAryRetBody = checkPreserveDateTime($strPreserveDatetime);
            if( $tmpAryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                $intErrorType = $tmpAryRetBody[1];
                
                if( $tmpAryRetBody[1] < 500 ){
                    $strExpectedErrMsgBodyForUI = $tmpAryRetBody[4];
                }
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($tmpAryRetBody);
            
        }

        
        //----形式的バリデーションチェック
        $retArray = sortedDataDecodeForConstruct($strOptionOrderStream,$aryOptionOrderOverride);
        if( $retArray[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            $intErrorType = $retArray[1];
            if( $retArray[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$retArray[2]);
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryOptionOrder = $retArray[0];
        //形式的バリデーションチェック----

        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000500";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する
        
        // ----MOV-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000600";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // MOV-INSTANCE-シーケンスを掴む----

        // ----SYM-INSTANCE-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            $strErrStepIdInFx="00000900";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-INSTANCE-シーケンスを掴む----
        
        // シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する----
        
        $aryRetBody = getInfoFromOneOfSymphonyClasses($intShmphonyClassId, 0);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            if( $aryRetBody[1] === 101 ){
                //----該当のシンフォニーClassIDが１行も発見できなかった場合
                $intErrorType = 2;
                //$strExpectedErrMsgBodyForUI = "SymphonyクラスID：存在している必要があります。";
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733107");
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                //該当のシンフォニーClassIDが１行も発見できなかった場合----
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfSymClassTable = $aryRetBody[4];
        $aryRowOfMovClassTable = $aryRetBody[5];
        //シンフォニーCLASSが存在するか？----
        

        $arrayRetBody = $objOLA->getInfoOfOneOperation($intOperationNoUAPK);
        if( $arrayRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001100";
            if( $arrayRetBody[1] === 101 ){
                $intErrorType = 2;
                //
                //$strExpectedErrMsgBodyForUI = "オペレーション№：存在している必要があります。";
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733108");
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryRowOfOperationTable = $arrayRetBody[4];
        
        // ----シンフォニー-INSTANCEを更新
        $arrayConfigForIUD = $arrayConfigForSymInsIUD;
        $register_tgt_row = $arraySymInsValueTmpl;
        
        $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        else{
            $varRISeq = $retArray[0];
        }
        $varSymphonyInstanceNo = $varRISeq;
        $register_tgt_row['SYMPHONY_INSTANCE_NO'] = $varRISeq;
        
        $register_tgt_row['I_SYMPHONY_CLASS_NO']  = $aryRowOfSymClassTable['SYMPHONY_CLASS_NO'];
        $register_tgt_row['I_SYMPHONY_NAME']      = $aryRowOfSymClassTable['SYMPHONY_NAME'];
        $register_tgt_row['I_DESCRIPTION']        = $aryRowOfSymClassTable['DESCRIPTION'];
        
        //----開始予約時刻が設定されていた場合
        if( strlen($strPreserveDatetime)==0 ){
            $varStatus = 1;
        }
        else{
            $varStatus = 2;
            $register_tgt_row['TIME_BOOK']            = $strPreserveDatetime;
        }
        //開始予約時刻が設定されていた場合----
        $register_tgt_row['STATUS_ID']            = $varStatus; //未実行[1]または未実行(予約)[2]
        $register_tgt_row['EXECUTION_USER']       = $g['login_name_jp'];
        $register_tgt_row['OPERATION_NO_UAPK']    = $intOperationNoUAPK;
        $register_tgt_row['I_OPERATION_NAME']     = $aryRowOfOperationTable['OPERATION_NAME'];
        
        $register_tgt_row['ABORT_EXECUTE_FLAG']   = 1; //緊急停止発令フラグ(未発令)=[1]
        
        $register_tgt_row['DISUSE_FLAG']          = '0';
        $register_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
        
        $tgtSource_row = $register_tgt_row;
        $sqlType = "INSERT";
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                            ,$sqlType
                                            ,"SYMPHONY_INSTANCE_NO"
                                            ,"C_SYMPHONY_INSTANCE_MNG"
                                            ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                            ,$arrayConfigForIUD
                                            ,$tgtSource_row );
        
        if( $retArray[0] === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001300";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        // ----履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            $strErrStepIdInFx="00001400";
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
            $strErrStepIdInFx="00001500";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($retArray01);
        unset($retArray02);
        
        // シンフォニー-INSTANCEを更新----
        
        // ----ムーブメントから、廃止されているレコードを除外する
        $aryMovement = array();
        foreach($aryRowOfMovClassTable as $aryDataForMovement){
            if( $aryDataForMovement['DISUSE_FLAG']=='0' ){
                $aryMovement[] = $aryDataForMovement;
            }
        }
        // ムーブメントから、廃止されているレコードを除外する----
        
        //----RedMineチケット1054
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
        
        if( count($aryMovement) !== count($aryOptionOrder) ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001600";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        if( count($aryOptionOrder) == 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001700";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $MovementErrorMsg = "";

        // ----ムーブメントを登録
        $intFocusIndex = 0;
        foreach($aryMovement as $aryDataForMovement){
            // ----ムーブメントを更新
            $aryValuePerOptionOrderKey = $aryOptionOrder[$intFocusIndex];
            
            $arrayConfigForIUD = $arrayConfigForMovInsIUD;
            $register_tgt_row = $arrayMovInsValueTmpl;
            
            $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_RIC', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001800";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varRISeq = $retArray[0];
            }
            
            $strPatternIdNumeric = $aryDataForMovement['PATTERN_ID'];
            $retArray = $objOLA->getLivePatternFromMaster(array($aryDataForMovement['ORCHESTRATOR_ID']),"",array($strPatternIdNumeric));
            if($retArray[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001900";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryMultiLivePatternFromMaster = $retArray[0];
            if( array_key_exists($strPatternIdNumeric, $aryMultiLivePatternFromMaster) === false ){
                // #1236 2017/08/03 Update start
                // メッセージ改善
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002000";
                $intErrorType = 2;
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-1990037",array($intFocusIndex + 1));
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            //----差分がないかをチェック
            if( ($intFocusIndex + 1) != $aryValuePerOptionOrderKey['MOVEMENT_SEQ'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002100";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // オーケストレータが同じかどうか、をチェック
            if( $aryDataForMovement['ORCHESTRATOR_ID'] != $aryValuePerOptionOrderKey['ORCHESTRATOR_ID'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002200";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            // 作業パターンが同じかどうか、をチェック
            if( $strPatternIdNumeric != $aryValuePerOptionOrderKey['PATTERN_ID'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002300";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //差分がないかをチェック----
            //RedMineチケット1026----
            
            $arySinglePatternSource = $aryMultiLivePatternFromMaster[$strPatternIdNumeric];
            unset($aryMultiLivePatternFromMaster);
            
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
                $strErrStepIdInFx="00002400";
                
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
                $strErrStepIdInFx="00002500";
                
                $intErrorType = 2;
                
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733111",array($intFocusIndex + 1));
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            if( 0 < strlen($aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH']) ){
                $tmpStrOpeNoIDBH = $aryValuePerOptionOrderKey['OVRD_OPERATION_NO_IDBH'];
                
                $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($tmpStrOpeNoIDBH) === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002600";
                    $intErrorType = 2;
                    
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733109",array($intFocusIndex + 1),$objIntNumVali->getValidRule());
                    
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($objIntNumVali);
                
                $tmpAryRetBody = $objOLA->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);
                if( $tmpAryRetBody[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002700";
                    
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
            
            $register_tgt_row['I_DESCRIPTION']        = $aryDataForMovement['DESCRIPTION'];
            
            $register_tgt_row['ABORT_RECEPTED_FLAG']  = 1; //緊急停止受付確認フラグ=未確認[1]
            
            $register_tgt_row['SYMPHONY_INSTANCE_NO'] = $varSymphonyInstanceNo;
            
            $register_tgt_row['STATUS_ID']            = 1; //未実行[1]で
            $register_tgt_row['EXECUTION_USER']       = $g['login_name_jp'];
            $register_tgt_row['DISUSE_FLAG']          = '0';
            $register_tgt_row['LAST_UPDATE_USER']     = $g['login_id'];
            
            $tgtSource_row = $register_tgt_row;
            $sqlType = "INSERT";

            // 各Movementの登録状態を確認する。
            $ret = MovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,($intFocusIndex + 1));
            if( $ret === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002801";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"MOVEMENT_INSTANCE_NO"
                                                ,"C_MOVEMENT_INSTANCE_MNG"
                                                ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002800";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00002900";
                
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
                $strErrStepIdInFx="00003000";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
            
            //SQL実行----
            
            $intFocusIndex += 1;
            
            // ムーブメントを更新----
        }
        // ムーブメントを登録----

        // ムーブメントの登録内容に不備がなかったことを確認
        if($MovementErrorMsg != ""){
            $strErrStepIdInFx="00003001";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI = $MovementErrorMsg;
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        // Symphonyインターフェース情報の登録データ確認する。
        $strQuery = "SELECT * FROM C_SYMPHONY_IF_INFO WHERE DISUSE_FLAG = '0'";
        $aryForBind = array();
        $strFxName  = "";
        $tmpStrInterVal = "";
        $IF_Errormsg = "";
        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
        if( $aryRetBody[0] === true ){
            $objQuery = $aryRetBody[1];
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
        }else{
            // アクセス異常
            $IF_Errormsg = $objMTS->getSomeMessage("ITABASEH-ERR-1990009",array("C_SYMPHONY_IF_INFO"));
        }
        if($IF_Errormsg != "")
        {
            $strErrStepIdInFx="00003002";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI = $IF_Errormsg;
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

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
        
        $retBool = true;
        $intSymphonyInstanceId = $varSymphonyInstanceNo;
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102090");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102070");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-103010");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-102080");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合---- 
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($retBool,
                      $intErrorType,
                      $aryErrMsgBody,
                      $strErrMsg,
                      $intSymphonyInstanceId,
                      $strExpectedErrMsgBodyForUI
                      );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//インスタンスを作成する----

function sortedDataDecodeForConstruct($strSortedData){
    global $g;
    $aryMovement = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $arySettingForParse = array(
        0=>'MOVEMENT_SEQ'
        ,1=>'ORCHESTRATOR_ID'
        ,2=>'PATTERN_ID'
        ,3=>'EXE_SKIP_FLAG'
        ,4=>'OVRD_OPERATION_NO_IDBH'
    );
    
    $strSysErrMsgBody = "";
    
    try{
        if( is_string($strSortedData) === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5733202");
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryMovementSortBase = getArrayBySafeSeparator($strSortedData);
        
        if( is_array($aryMovementSortBase) === false ){
            $aryMovementSortBase = array();
        }
        
        //----ループ
        $intFvn1 = 0;
        $intLengthArySettingForParse = count($arySettingForParse);
        $aryMovement = array();
        foreach( $aryMovementSortBase as $value ){
            if( array_key_exists($intFvn1, $arySettingForParse) === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5733203");
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strFocusParseKey = $arySettingForParse[$intFvn1];
            $arySingleMovement[$strFocusParseKey] = $value;
            
            $intFvn1 += 1;
            if( $intFvn1 == $intLengthArySettingForParse ){
                $aryMovement[] = $arySingleMovement;
                $arySingleMovement = array();
                $intFvn1 = 0;
            }
        }
        //ループ----
        if( $intFvn1 !== 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5733204");
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 2;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($aryMovement,$intErrorType,$aryErrMsgBody,$strErrMsg);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}

// 各Movementの登録状態を確認する。
function MovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
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
        $ret = AnsibleLegacyMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex);
        break;
    case 4:   // pioneer
        $ret = AnsiblePioneerMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex);
        break;
    case 5:   // legacy role
        $ret = AnsibleLegacyRoleMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex);
        break;
    case 8:   // DSC
        $ret = DscMovementValidator($tgtSource_row,$intOperationNoUAPK,$MovementErrorMsg,$intFocusIndex);
        break;
    default:  // 対象外は無条件にtrue
        $ret = true;
    }
    return $ret;
}
// Ansible Legacy Movementの登録状態を確認
function AnsibleLegacyMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
    global $g;
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];

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
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
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
// Ansible Pioneer Movementの登録状態を確認
function AnsiblePioneerMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
    global $g;
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];

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
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
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
// Ansible Legacy Role Movementの登録状態を確認
function AnsibleLegacyRoleMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
    global $g;
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];

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
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
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
// DSC Movementの登録状態を確認
function DscMovementValidator($tgtSource_row,$intOperationNoUAPK,&$MovementErrorMsg,$intFocusIndex){
    global $g;
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];

    // 作業対象ホストの件数と作業対象ホストに紐づくマスタの登録状況までを確認
	//-------------------------------------------------------------------------------
	// ①同一オペレーションNo(OPERATION_NO_UAPK)が廃止フラグONの状態
	//   B_DSC_PHO_LINK と C_OPERATION_LIST
	// ②同一パターンID(PATTERN_ID)が廃止フラグONの状態
	//   B_DSC_PHO_LINK と C_PATTERN_PER_ORCH
	// ③同一パターンID(PATTERN_ID)が廃止フラグONの状態
	//   B_DSC_PHO_LINK と B_DSC_PATTERN_LINK
	// ④同一ホスト(SYSTEM_ID)が廃止フラグONの状態
	//   B_DSC_PHO_LINK と C_STM_LIST
	//-------------------------------------------------------------------------------
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
                   "       B_DSC_PATTERN_LINK S_TBL                                   " .
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
                   "   B_DSC_PHO_LINK M_TBL                                           " .
                   " WHERE                                                            " .
                   "   M_TBL.OPERATION_NO_UAPK  = %s    AND                           " .
                   "   M_TBL.PATTERN_ID         = %s    AND                           " .  
                   "   M_TBL.DISUSE_FLAG        = '0';                                ",
                   $intOperationNoUAPK,
                   $tgtSource_row['I_PATTERN_ID']);
    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        web_log(__FILE__ . ":" . __LINE__ . ":" . $sql);
        web_log(__FILE__ . ":" . __LINE__ . ":" . $objQuery->getLastError());
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
?>
