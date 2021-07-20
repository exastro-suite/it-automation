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
        "ACCESS_AUTH"=>"",
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

        $status_id = $tgtSource_row['STATUS_ID'];

        if ( $status_id != 1 ){
        
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
            
            if( $objDBCA->getTransactionMode() ){
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
            }
            
            ////////////////////////////////////////////////////////////////////
            // (ここまで)現在のムーブメントを調べて、緊急停止をリクエストする //
            ////////////////////////////////////////////////////////////////////
        }
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
        "ACCESS_AUTH"=>"",
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
        $objRBAC = new RoleBasedAccessControl($objDBCA);
        
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
                                  ,'OPERATION_NO_IDBH'=>$aryRowOfSymInstanceTable['OPERATION_NO_UAPK']
                                  ,'OPERATION_NAME'=>$aryRowOfSymInstanceTable['I_OPERATION_NAME']
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
            //対象のMovementのアクセス権をチェック
            $movAccessAuth = false;
            $intPatternId = $row['I_PATTERN_ID'];
            //対象MovementをSELECT
            $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                   ." WHERE PATTERN_ID = $intPatternId ";

            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $targetRow = $objQuery->resultFetch();

            $user_id = $g['login_id'];
            $ret  = $objRBAC->getAccountInfo($user_id); 
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                //例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission === true) {
                    $movAccessAuth = true;
                }
            }

            $aryClassItems = array();
            $varOrcIdFromMovInstanceTable     = $row['I_ORCHESTRATOR_ID'];
            $varPatternIdFromMovInstanceTable = $intPatternId;
            
            //----ここからクラスと同じ情報項目
            
            // オーケストレータID
            $aryClassItems['ORCHESTRATOR_ID']           = $varOrcIdFromMovInstanceTable;
            
            // 作業パターンID
            $aryClassItems['PATTERN_ID']                = $varPatternIdFromMovInstanceTable;
            
            //----作業パターンの名前
            $aryClassItems['PATTERN_NAME'] = $row['I_PATTERN_NAME'];

            // テーマカラー
            $aryClassItems['THEME_COLOR'] = $aryPatternListPerOrc[$varOrcIdFromMovInstanceTable]['ThemeColor'];
            
            // 楽章番号
            $aryClassItems['MOVEMENT_SEQ'] = $row['I_MOVEMENT_SEQ'];
            
            // 説明
            $description = "";
            if($movAccessAuth == true){
                $description = $row['I_DESCRIPTION']; //htmlspecialchars
            }
            $aryClassItems['DESCRIPTION'] = $description;
            
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
        "ACCESS_AUTH"=>"",
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
        "ACCESS_AUTH"=>"",
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
    $aryFreeErrMsgBody = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        
        //----シンフォニーCLASSIDの形式チェック
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
        //シンフォニーCLASSIDの形式チェック----
        
        //----オペレーションNoの形式チェック
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
        //オペレーションNoの形式チェック----
        
        //----$strPreserveDatetimeの形式チェック
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
        //$strPreserveDatetimeの形式チェック----

        //----形式的バリデーションチェック
        $retArray = sortedDataDecodeForConstruct($strOptionOrderStream);
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

        //----Operation、Symphonyの共通アクセス権の取得 #521 #524
        $arrOpeConAccessAuth = $objOLA->getInfoAccessAuthWorkFlowOpe($intShmphonyClassId,$intOperationNoUAPK ,"S" ,$aryOptionOrder);

        if( $arrOpeConAccessAuth[3] != "" ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI =  $arrOpeConAccessAuth[3];
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }

        $strOpeConAccessAuth = $arrOpeConAccessAuth[4];
        // Operation、、Symphonyの共通アクセス権の取得 #521 #524----


        // ----シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録
        $retArray = $objOLA->registerSymphonyInstance($intShmphonyClassId, $intOperationNoUAPK, $strPreserveDatetime, $aryOptionOrder, $aryOptionOrderOverride, $g['login_id'], $g['login_name_jp']);
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
        // シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録----

        $retBool = true;
        $intSymphonyInstanceId = $retArray[5];
        unset($retArray);

    }
    catch (Exception $e){
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

?>
