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
    $intControlDebugLevel01 = 50;
    
    //----オーケストレータ別の設定記述
    
    // テーブル情報
    $strExeCurTableIdForSelect = 'E_ANSIBLE_LNS_EXE_INS_MNG';
    $strExeJnlTableIdForSelect = 'E_ANSIBLE_LNS_EXE_INS_MNG_JNL';
    
    $strExeCurTableIdForIU = 'C_ANSIBLE_LNS_EXE_INS_MNG';
    $strExeJnlTableIdForIU = 'C_ANSIBLE_LNS_EXE_INS_MNG_JNL';
    
    $strExeCurSeqName = 'C_ANSIBLE_LNS_EXE_INS_MNG_RIC';
    $strExeJnlSeqName = 'C_ANSIBLE_LNS_EXE_INS_MNG_JSQ';


    global $root_dir_path;    
    require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php');

    $arrayConfig = array();
    CreateExecInstMngViewArray($arrayConfig);
    SetExecInstMngColumnType($arrayConfig);
    
    $arrayValue = array();
    CreateExecInstMngViewArray($arrayValue);
    
    $arrayConfig2 = array();
    CreateExecInstMngArray($arrayConfig2);
    SetExecInstMngColumnType($arrayConfig2);
    
    //オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    $varTrzStart = null;
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
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
                $warning_info = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-102030");
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
        // ステータスIDによって処理を分岐                             //
        ////////////////////////////////////////////////////////////////
        // ステータスIDが未実行(予約)(9)以外の場合
        if( $status_id != 9 ){
            // エラー箇所をメモ
            $warning_info = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-102040",$status_name);
            throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
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
        
        $cln_execution_row['STATUS_ID']         = "10";
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
        $output_str = $g['objMTS']->getSomeMessage("ITAANSIBLEH-STD-102050",$target_execution_no);
    }
    catch (Exception $e){
        //----正常時でも飛ばす版
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        if( $varTrzStart === true ){
            $varRollBack = $g['objDBCA']->transactionRollBack();
            if( $varRollBack === false ){
                //----1回目のロールバックが失敗してしまった場合
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4021",$strFxName));
                //1回目のロールバックが失敗してしまった場合----
            }
            $varTrzExit = $g['objDBCA']->transactionExit();
            if( $varTrzExit === false ){
                //----トランザクションが終了できないので以降は緊急停止
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4022",$strFxName));
                exit();
                //トランザクションが終了できないので以降は緊急停止----
            }
        }
        
        // DBアクセス事後処理
        if( isset($objQuery) )    unset($objQuery);
        if( isset($objQueryUtn) ) unset($objQueryUtn) ;
        if( isset($objQueryJnl) ) unset($objQueryJnl) ;

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
