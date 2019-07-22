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
    


    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $execution_management_dir = dirname($g['page_dir'])."/execution_management";
    
    //----オーケストレータ別の設定記述
    

    // テーブル情報
    $strExeCurTableIdForSelect = 'E_OPENST_RESULT_MNG';
    $strExeJnlTableIdForSelect = 'E_OPENST_RESULT_MNG_JNL';
    
    $strExeCurTableIdForIU = 'C_OPENST_RESULT_MNG';
    $strExeJnlTableIdForIU = 'C_OPENST_RESULT_MNG_JNL';
    
    $strExeCurSeqName = 'C_OPENST_RESULT_MNG_RIC';
    $strExeJnlSeqName = 'C_OPENST_RESULT_MNG_JSQ';
    
    $arrayConfig = array(
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "EXECUTION_NO"=>"",
        "HEAT_INPUT"=>"",
        "HEAT_RESULT"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_SEQ_NO"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>"",
        "NOTE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "PATTERN_ID"=>"",
        "RUN_MODE"=>"",
        "STATUS_ID"=>"",
        "STATUS_NAME"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "TIME_START"=>"DATETIME"
    );
    
    $arrayValue = array(
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "EXECUTION_NO"=>"",
        "HEAT_INPUT"=>"",
        "HEAT_RESULT"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_SEQ_NO"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>"",
        "NOTE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "PATTERN_ID"=>"",
        "RUN_MODE"=>"",
        "STATUS_ID"=>"",
        "STATUS_NAME"=>"",
        "TIME_BOOK"=>"",
        "TIME_END"=>"",
        "TIME_START"=>""
    );
    
    $arrayConfig2 = array(
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "EXECUTION_NO"=>"",
        "HEAT_INPUT"=>"",
        "HEAT_RESULT"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_SEQ_NO"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>"",
        "NOTE"=>"",
        "OPERATION_NO_UAPK"=>"",
        "PATTERN_ID"=>"",
        "RUN_MODE"=>"",
        "STATUS_ID"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "TIME_START"=>"DATETIME"
    );
    
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    $varTrzStart = null;
    
    $strFxName = __FUNCTION__;

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    const SCRAM=5;    //緊急停止ボタンが押されました。
    const SCRAM_COMPLETE=6;     //緊急停止が完了しました。

    $retArrayMng=array();
    $retArrayDetail=array();

    try{
        ////////////////////////////////////////////////////////////////
        //  パラメータチェック(ガードロジック)                        //
        ////////////////////////////////////////////////////////////////
        
        $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($target_execution_no) === false ){
            // エラー箇所をメモ
            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        ////////////////////////////////////////////////////////////////
        // 対象レコードをSELECT(ロック)                               //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        
        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND EXECUTION_NO = :EXECUTION_NO ");

        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                             "SELECT FOR UPDATE",
                                             "EXECUTION_NO",
                                             $strExeCurTableIdForSelect,
                                             $strExeJnlTableIdForSelect,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        $varTrzStart = $g['objDBCA']->transactionStart();
        if( $varTrzStart === false ){
            throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $arrayUtnBind['EXECUTION_NO'] = $target_execution_no;
        $retArray = singleSQLExecuteAgent($sqlUtnBody, $arrayUtnBind, $strFxName);
        if( $retArray[0] === true ){
            $intTmpRowCount=0;
            $showTgtRow = array();
            $objQueryUtn =& $retArray[1];
            while($row = $objQueryUtn->resultFetch() ){
                if($row !== false){
                    $intTmpRowCount+=1;
                }
                if($intTmpRowCount==1){
                    $showTgtRow = $row;
                }
            }
            $selectRowLength = $intTmpRowCount;
            if( $selectRowLength != 1 ){
                $warning_info = $g['objMTS']->getSomeMessage("ITAOPENST-ERR-102030");
                throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tgt_execution_row = $showTgtRow;
            unset($objQueryUtn);
        }
        else{
            throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $status_id       = $tgt_execution_row['STATUS_ID'];
        $status_name     = $tgt_execution_row['STATUS_NAME'];


        ////////////////////////////////////////////////////////////////
        // 対象レコードをUPDATE                                       //
        ////////////////////////////////////////////////////////////////
        
        // シーケンスをロック
        $retArray = getSequenceLockInTrz($strExeJnlSeqName,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラー箇所をメモ
            throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // 履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable($strExeJnlSeqName, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラー箇所をメモ
            throw new Exception( '00000700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // クローン作成
        $cln_execution_row = $tgt_execution_row;
        
        // 変数バインド準備
        $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
        
        $cln_execution_row['STATUS_ID']         = SCRAM;
        $cln_execution_row['LAST_UPDATE_USER']  = $g['login_id'];
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                                "UPDATE",
                                                "EXECUTION_NO",
                                                $strExeCurTableIdForIU,
                                                $strExeJnlTableIdForIU,
                                                $arrayConfig2,
                                                $cln_execution_row );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);
        
        if( $objQueryUtn->getStatus()===false || 
            $objQueryJnl->getStatus()===false ){
            // エラー箇所をメモ
            throw new Exception( '00000800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            // エラー箇所をメモ
            throw new Exception( '00000900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            // エラー箇所をメモ
            throw new Exception( '00001000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            // エラー箇所をメモ
            throw new Exception( '00001100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $g['objDBCA']->transactionCommit();
        if (!$r){
            throw new Exception( '00001200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // DBアクセス事後処理
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $g['objDBCA']->transactionExit();
        
        // 正常向けの結果メッセージを作成
        $output_str = $g['objMTS']->getSomeMessage("ITAOPENST-STD-103000",$target_execution_no);
    }
    catch (Exception $e){
        //----正常時でも飛ばす版
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);

        if( !empty($output_str) ){
            //----正常（単なる処理省略）
            //正常（単なる処理省略）----
        }
        else if( !empty($warning_info) ){
            //----警告
            //警告----
        }
        else{
            // エラーフラグをON
            if( empty($error_info) ) $error_info = $tmpErrMsgBody;
        }
        //正常時でも飛ばす版----
    }

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
