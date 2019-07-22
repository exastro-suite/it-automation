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

    $intControlDebugLevel01 = 50;
    $varTrzStart = null;

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);

    $intResultCode = 0;
    $intDetailCode = 0;
    $boolProcessCountinue = true;

    // パラメータに対する各種チェック(ガードロジックとしてjavascriptと同処理を実装)

    if( $g['privilege'] != '1' ){
        $intDetailCode = 100;
        //'メンテナンス権限がありません。'
        $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070059");
        $boolProcessCountinue = false;
    }

    if( $boolProcessCountinue === true ){
        if( $pwl_threshold < 1 || $pwl_expiry == 0 ){
            $intDetailCode = 101;
            //----ロックされていません（アカウントロック機能：無効）
            $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070060");
            $boolProcessCountinue = false;
        }
    }

    if( $boolProcessCountinue === true ){
        // DBアクセスを伴う処理を開始
        try{
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($p_user_id) === false ){
                throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $varTrzStart = $g['objDBCA']->transactionStart();
            
            $arrayConfig = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LOCK_ID"=>"",
                "USER_ID"=>"",
                "LOCKED_TIMESTAMP"=>"DATETIME",
                "MISS_INPUT_COUNTER"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LOCK_ID"=>"",
                "USER_ID"=>"",
                "LOCKED_TIMESTAMP"=>"",
                "MISS_INPUT_COUNTER"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );

            $arrayValue = $arrayValueTmpl;
            
            $temp_array = array('WHERE'=>"USER_ID = :USER_ID AND DISUSE_FLAG = '0' ");
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                            "SELECT FOR UPDATE",
                                            "USER_ID",
                                            "A_ACCOUNT_LOCK",
                                            "A_ACCOUNT_LOCK_JNL",
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array
            );
            
            $aryResult01 = array();
            if( $retArray[0] === false ){
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind['USER_ID'] = $p_user_id;
            
            $objQuery = $g['objDBCA']->sqlPrepare($sqlUtnBody);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            if( $objQuery->sqlBind($arrayUtnBind) != "" ){
                throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $r = $objQuery->sqlExecute();
            if(!$r){
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            //----発見行だけループ
            while ( $row = $objQuery->resultFetch() ){
                $aryResult01[] = $row;
            }
            //発見行だけループ----
            $intEffectCount = $objQuery->effectedRowCount();
            unset($objQuery);
            
            if( $intEffectCount == 0 ){
                $intDetailCode = 102;
                $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070061");
            }
            else if( $intEffectCount == 1 ){
                // ----更新するケース
                
                $boolRecordUpdate = false;
                
                $arrayValue           = $aryResult01[0];
                $intMissedCount       = $arrayValue['MISS_INPUT_COUNTER'];
                $varLockedTimeStamp   = $arrayValue['LOCKED_TIMESTAMP'];
                
                // ----有効期限内かどうかを判定
                $boolInOfLockExpiry = saLoginLockCheckInExpiry($pwl_expiry,$varLockedTimeStamp);
                
                if( $boolInOfLockExpiry === false ){
                    // ----有効期限外
                    $intDetailCode = 103;
                    $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070062");
                    // 有効期限内----
                }
                else{
                    // ----有効期限内
                    if( $intMissedCount < $pwl_threshold ){
                        // ----連続失敗回数が、閾値未満
                        
                        // ----普段は通過することはない。
                        $intDetailCode = 104;
                        $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070063");
                        // 普段は通過することはない。----
                        
                        // 連続失敗回数が、閾値未満----
                    }
                    else{
                        // ----連続失敗回数が、閾値以上
                        $boolRecordUpdate = true;
                        // 連続失敗回数が、閾値以上----
                    }
                    // 有効期限内----
                }
                
                if( $boolRecordUpdate === true ){
                    $retArray = getSequenceValueFromTable('JSEQ_A_ACCOUNT_LOCK', 'A_SEQUENCE', FALSE );
                    if( $retArray[1] != 0 ){
                        throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    // ----有効期限内かつ閾値以上で、ロックされている
                    $arrayValue['JOURNAL_SEQ_NO']     = $retArray[0];
                    $arrayValue['MISS_INPUT_COUNTER'] = "0";
                    $arrayValue['LOCKED_TIMESTAMP']   = "";
                    $arrayValue['LAST_UPDATE_USER']   = $g['login_id'];
                    // 有効期限内かつ閾値以上で、ロックされている----
                    
                    $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                        "UPDATE",
                        "LOCK_ID",
                        "A_ACCOUNT_LOCK",
                        "A_ACCOUNT_LOCK_JNL",
                        $arrayConfig,
                        $arrayValue );
                    
                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];
                    
                    $sqlJnlBody = $retArray[3];
                    $arrayJnlBind = $retArray[4];
                    
                    $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
                    $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);
                    
                    if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                        throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                        throw new Exception( '00000700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    //----SQL実行
                    $rUtn = $objQueryUtn->sqlExecute();
                    if($rUtn!=true){
                        throw new Exception( '00000800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $rJnl = $objQueryJnl->sqlExecute();
                    if($rJnl!=true){
                        throw new Exception( '00000900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $r = $g['objDBCA']->transactionCommit();
                    if (!$r){
                        throw new Exception( '00001000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    unset($objQueryUtn);
                    unset($objQueryJnl);
                    
                    //$retStrOutput = 'ロックを解除しました';
                    $retStrOutput = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070064");
                    
                    // 更新するケース----
                }
                else{
                    // ----更新しないケース
                    
                    // ----掴んでいるレコードを解放する
                    $r = $g['objDBCA']->transactionCommit();
                    if (!$r){
                        throw new Exception( '00001100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }   
                    // 掴んでいるレコードを解放する----
                    
                    // 更新しないケース----
                }
            }
            else{
                // ----レコードが複数行存在した場合
                // 例外処理へ
                throw new Exception( '00001200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // レコードが複数行存在した場合----
            }
            unset($objQueryUtn);

            $g['objDBCA']->transactionExit();
            $strResultCode = sprintf("%03d", $intResultCode);
            $strDetailCode = sprintf("%03d", $intDetailCode);
        }
        catch (Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery) )    unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            
            $intErrorFlag = 1;
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
            $strResultCode = sprintf("%03d", $intErrorFlag);
            $strDetailCode = sprintf("%03d", $intDetailCode);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
        }
    }
    
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
