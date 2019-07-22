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

    $intControlDebugLevel01 = 250;

    $intSecOfSleepTime = 5;

    $arrayResult = array();
    $intErrorType = null;
    $intDetailCode = null;

    $strMsgBody = "";

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);

    $aryConfigForStmIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYSTEM_ID"=>"",
        "HARDAWRE_TYPE_ID"=>"",
        "HOSTNAME"=>"",
        "IP_ADDRESS"=>"",
        "ETH_WOL_MAC_ADDRESS"=>"",
        "ETH_WOL_NET_DEVICE"=>"",
        "LOGIN_USER"=>"",
        "LOGIN_PW"=>"",
        "LOGIN_AUTH_TYPE"=>"",
        "WINRM_PORT"=>"",
        "PROTOCOL_ID"=>"",
        "OS_TYPE_ID"=>"",
        "COBBLER_PROFILE_ID"=>"",
        "INTERFACE_TYPE"=>"",
        "MAC_ADDRESS"=>"",
        "NETMASK"=>"",
        "GATEWAY"=>"",
        "STATIC"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryStmIUDValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYSTEM_ID"=>"",
        "HARDAWRE_TYPE_ID"=>"",
        "HOSTNAME"=>"",
        "IP_ADDRESS"=>"",
        "ETH_WOL_MAC_ADDRESS"=>"",
        "ETH_WOL_NET_DEVICE"=>"",
        "LOGIN_USER"=>"",
        "LOGIN_PW"=>"",
        "LOGIN_AUTH_TYPE"=>"",
        "WINRM_PORT"=>"",
        "PROTOCOL_ID"=>"",
        "OS_TYPE_ID"=>"",
        "COBBLER_PROFILE_ID"=>"",
        "INTERFACE_TYPE"=>"",
        "MAC_ADDRESS"=>"",
        "NETMASK"=>"",
        "GATEWAY"=>"",
        "STATIC"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $strKeyValue1 = $p_target_system_id;
    $strKeyValue2 = $intMode;
    $strKeyValue3 = $p_tid_for_tag_identify;
    $strKeyValue4 = $p_last_updatetime_for_update;

    // DBアクセスを伴う処理開始
    try{
        $boolExecuteContinue = true;

        if( $g['privilege'] !== '1' ){
            $strErrStepIdInFx="00000100";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        //----呼び出しモードのチェック
        $objIntNumVali = new IntNumValidator(1,2,"","",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($strKeyValue2) === false ){
            $strErrStepIdInFx="00000200";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        //呼び出しモードのチェック----

        //----押されたボタンタグ特定用の鍵(tid(3)+13)
        $objTextVldt = new SingleTextValidator(16,16,false);
        if( $objTextVldt->isValid($strKeyValue3) === false ){
            $strErrStepIdInFx="00000300";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        //押されたボタンタグ特定用の鍵----

        if( $intMode == '1' ){
            //----管理システム項番のチェック
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($strKeyValue1) === false ){
                $intErrorType = 2;
                $strMsgBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-130010",array($objIntNumVali->getValidRule()));
                $strErrStepIdInFx="00000400";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //----管理システム項番のチェック
            $objTextVldt = new SingleTextValidator(22,22,false);
            if( $objTextVldt->isValid($strKeyValue4) === false ){
                $strErrStepIdInFx="00000500";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }

        if( $intMode == '1' ){
            if( $boolExecuteContinue === true ){
                $aryConfig = $aryConfigForStmIUD;
                $aryValue = $aryStmIUDValueTmpl;
                $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND SYSTEM_ID = :SYSTEM_ID ");

                $aryRetBody = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                      ,"SELECT"
                                                      ,"SYSTEM_ID"
                                                      ,"C_STM_LIST"
                                                      ,"C_STM_LIST_JNL"
                                                      ,$aryConfigForStmIUD
                                                      ,$aryValue
                                                      ,$aryTempForSql);
                
                if( $aryRetBody[0] === false ){
                    // 例外処理へ
                    $strErrStepIdInFx="00000600";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $sql = $aryRetBody[1];

                $tmpAryBind = array('SYSTEM_ID'=>$strKeyValue1);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $intTmpRowCount=0;
                    $showTgtRow = array();
                    $objQuery =& $retArray[1];
                    while($row = $objQuery->resultFetch() ){
                        if( $row !== false ){
                            $intTmpRowCount += 1;
                        }
                        if( $intTmpRowCount == 1 ){
                            $showTgtRow = $row;
                        }
                    }
                    $selectRowLength = $intTmpRowCount;
                    if( $selectRowLength != 1 ){
                        $intErrorType = 501;
                        $strErrStepIdInFx="00000700";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    unset($objQuery);
                }
                else{
                    $intErrorType = 502;
                    $strErrStepIdInFx="00000800";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            if( $boolExecuteContinue === true ){
                if( $showTgtRow['UPD_UPDATE_TIMESTAMP'] !== $strKeyValue4 ){
                    $strMsgBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-130040");
                    $intErrorType = 2;
                    $strErrStepIdInFx="00000900";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            if( $boolExecuteContinue === true ){
                $strNetDeviceName = $showTgtRow['ETH_WOL_NET_DEVICE'];
                if( strlen($strNetDeviceName) === 0 ){
                    $intErrorType = 2;
                    $boolExecuteContinue = false;
                    $strMsgBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-130020");
                }
            }

            if( $boolExecuteContinue === true ){
                $strMacAddress = $showTgtRow['ETH_WOL_MAC_ADDRESS'];
                if( strlen($strMacAddress) === 0 ){
                    $intErrorType = 2;
                    $boolExecuteContinue = false;
                    $strMsgBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-130030");
                }
            }

            if( $boolExecuteContinue === true ){
                $retAryOutput = array();
                $retIntOutput = null;
                
                $strCommand = "sudo ether-wake -i {$strNetDeviceName} {$strMacAddress}";
                
                $varRetOfResult = exec($strCommand,$retAryOutput,$retIntOutput);
                
                $strMsgBody = $g['objMTS']->getSomeMessage("ITABASEH-STD-130040");
            }
            $intDetailCode = 1;
        }
        else if( $intMode == '2' ){
            sleep($intSecOfSleepTime);
            $intDetailCode = 2;
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);
        if ( $intErrorType === null ) $intErrorType = 500;
        
        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
        
        if( 500 <= $intErrorType ){
            $strMsgBody = "";
        }
    }
    
    if( $intErrorType === null ){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }
    else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }
    
    $arrayResult[] = sprintf("%03d", $intErrorType);
    $arrayResult[] = sprintf("%03d", $intDetailCode);
    $arrayResult[] = $strMsgBody;
    $arrayResult[] = $intMode;
    $arrayResult[] = $p_tid_for_tag_identify;
    
    $output_str = makeAjaxProxyResultStream($arrayResult);
    
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
