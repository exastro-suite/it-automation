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
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;
    $root_dir_path = $g['root_dir_path'];

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-101020");

    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('C_STM_LIST','SYSTEM_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-101030"), 'C_STM_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['SYSTEM_ID']->setSequenceID('C_STM_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_STM_LIST_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-101040"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101050"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    $c = new IDColumn('HARDAWRE_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101060"),'B_HARDAWRE_TYPE','HARDAWRE_TYPE_ID','HARDAWRE_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-101070"));//エクセル・ヘッダでの説明
    $table->addColumn($c);



    $objVldt = new TextValidator(1, 128, false, '/^[\._a-zA-Z0-9-]+$/', "");
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c = new TextColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101080"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-101090"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $table->addColumn($c);



    $c = new TextColumn('IP_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102010"));
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102020"));//エクセル・ヘッダでの説明
    $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
    $objVldt = new TextValidator(7, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
    $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVldt);
    $c->setUnique(true);
    $table->addColumn($c);

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102110"));

        if( $g['privilege'] === '1' ){
            $objFunction = function($rowData){
                $retLinkable = "disabled";
                if( array_key_exists('ETH_WOL_MAC_ADDRESS', $rowData) === true
                    && array_key_exists('ETH_WOL_NET_DEVICE', $rowData) === true ){
                    if( 0 < strlen($rowData['ETH_WOL_MAC_ADDRESS']) 
                        && 0 < strlen($rowData['ETH_WOL_NET_DEVICE']) ){
                        $retLinkable = "";
                    }
                }
                return $retLinkable;
            };

            // ロック解除
            $strLabelText1 = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102120");
            $strLabelText2 = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102130");
            $c = new LinkButtonColumn('ethWakeOrder',$strLabelText1, $strLabelText2, 'dummy');
            $c->setDBColumn(false);
            $c->setHiddenMainTableColumn(false);
            $c->setOutputType('print_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
            $c->setEvent("print_table", "onClick", "ethWakeOrderSend", array('this',':SYSTEM_ID',':UPD_UPDATE_TIMESTAMP'));
            $c->getOutputType('print_journal_table')->setVisible(false);
            $cg->addColumn($c);
        }

        $strPattern = "/^(([0-9A-Za-z][0-9A-Za-z][:]){5}[0-9A-Za-z][0-9A-Za-z])*$/";
        $objVldt = new TextValidator(0, 17, false, $strPattern, "(xx:xx:xx:xx:xx:xx)");
        $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
        $c = new TextColumn('ETH_WOL_MAC_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102140"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102150"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $cg->addColumn($c);

        $objVldt = new SingleTextValidator(0,256,false);
        $c = new TextColumn('ETH_WOL_NET_DEVICE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102160"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102170"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $cg->addColumn($c);
    $table->addColumn($cg);

    $objVldt = new SingleTextValidator(0,30,false);
    $c = new TextColumn('LOGIN_USER',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102060"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // ログイン
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102061"));

        $c = new IDColumn('LOGIN_PW_HOLD_FLAG',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102062"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102063"));//エクセル・ヘッダでの説明
        $cg->addColumn($c);

        $objFunction03 = function($objOutputType, $rowData, $aryVariant, $objColumn){
            $strInitedColId = $objColumn->getID();
            $aryVariant['callerClass'] = get_class($objOutputType);
            $aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
            list($strSetValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strInitedColId),null);
            $strSetValue = (strlen($strSetValue)==0)?"":"********";
            $rowData[$strInitedColId] = $strSetValue;
            $objBody = $objOutputType->getBody();
            return $objBody->getData($rowData,$aryVariant);
        };

        $objFunction02 = function($objColumn, $strCallerName, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
            $boolRet = true;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $strErrorBuf = "";
            if( array_key_exists($objColumn->getID(), $exeQueryData) === true ){
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if( $exeQueryData[$objColumn->getID()] != "" ){
                        //----値の入力があった場合
                        $strEncodeFunctionName = $objColumn->getEncodeFunctionName();
                        if( $strEncodeFunctionName != "" ){
                            $strEncodedValue = $strEncodeFunctionName($exeQueryData[$objColumn->getID()]);
                        }else{
                            $strEncodedValue = $exeQueryData[$objColumn->getID()];
                        }
                        $exeQueryData[$objColumn->getID()] = $strEncodedValue;
                        //値の入力があった場合----
                    }else{
                        //----値の入力がなかった場合
                        if( $modeValue=="DTUP_singleRecUpdate" ){
                            list($intPwHoldFlag       ,$boolRefKeyExists) = isSetInArrayNestThenAssign($exeQueryData,array('LOGIN_PW_HOLD_FLAG'),"");
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●の場合
                                list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('edit_target_row','LOGIN_PW')          ,"");
                                $exeQueryData[$objColumn->getID()] = $strLoginPwEnCoded;
                                //パスワード管理が●の場合----
                            }else{
                                //----パスワード管理が●ではない場合
                                $exeQueryData[$objColumn->getID()] = "";
                                //パスワード管理が●ではない場合----
                            }
                        }
                        //値の入力がなかった場合----
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }else{
                }
            }
            $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
            return $retArray;
        };

        // パスワードをansible-vaultで暗号化した文字列を隠しカラムに登録する。
        $objFunction04 = function($objColumn, $strCallerName, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){

            global $g;
            if ( empty($root_dir_path) ){
                $root_dir_temp = array();
                $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
                $root_dir_path = $root_dir_temp[0] . "ita-root";
            }

            require_once($root_dir_path . '/libs/commonlibs/common_php_functions.php');
            require_once($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleVault.php');
            $boolRet = true;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $strErrorBuf = "";
            $strFxName = "";
            if( array_key_exists($objColumn->getID(), $exeQueryData) === true ){
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                switch($modeValue) {
                //case "DTUP_singleRecRegister":
                case "DTUP_singleRecUpdate":
                    $db_update = false;
                    // 変更前
                    $befor_pw = array_key_exists('LOGIN_PW',$aryVariant['edit_target_row'])?
                                                            $aryVariant['edit_target_row']['LOGIN_PW']:null;
                    // 変更後
                    $after_pw = array_key_exists('LOGIN_PW',$aryVariant['arySqlExe_update_table'])?
                                                            $aryVariant['arySqlExe_update_table']['LOGIN_PW']:null;

                    // パスワードの初期化は認証方式は関係ない
                    // ログインパスワードが管理でない場合にパスワードがクリア。管理の場合は残る
                    // 変更前と変更後のパスワードを判定して、違う場合にansible-vaultで暗号化した文字列を初期化
                    if($befor_pw != $after_pw) {
                        $db_update = true;
                    }
                    if($db_update === true) {
                        // ansible-vaultで暗号化した文字列を初期化
                        $strQuery = "UPDATE C_STM_LIST SET LOGIN_PW_ANSIBLE_VAULT = '' "
                                   ."WHERE SYSTEM_ID = " . $exeQueryData['SYSTEM_ID'];

                        $aryForBind = array();

                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                        if( $aryRetBody[0] !== true ){
                            $boolRet = false;
                            $intErrorType = 2;
                            $strErrMsg = $aryRetBody[2];
                        }
                    }
                }
            }
            $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
            return $retArray;
        };

        $outputType01 = new VariantOutputType(new TabHFmt(), new TextTabBFmt());
        $outputType01->setFunctionForGetBodyTag($objFunction03);
        $outputType02 = new VariantOutputType(new TabHFmt(), new TextTabBFmt());
        $outputType02->setFunctionForGetBodyTag($objFunction03);
        $outputType03 = new VariantOutputType(new TabHFmt(), new TextTabBFmt());
        $outputType03->setFunctionForGetBodyTag($objFunction03);

        $objVldt = new SingleTextValidator(0,30,false);
        $c = new PasswordColumn('LOGIN_PW',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102070"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102080"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);
        $c->setOutputType("print_table", $outputType01);
        $c->setOutputType('delete_table', $outputType02);
        $c->setOutputType('print_journal_table', $outputType03);
        $c->setValidator($objVldt);
        $c->setEncodeFunctionName("ky_encrypt");
        $c->setFunctionForEvent('beforeTableIUDAction',$objFunction02);
        $c->setFunctionForEvent('afterTableIUDAction',$objFunction04);
        $cg->addColumn($c);
    $table->addColumn($cg);

    $c = new FileUploadColumn('CONN_SSH_KEY_FILE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-109006"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-109007"));
    $c->setMaxFileSize(10240);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $table->addColumn($c);

    $wanted_filename = "ita_ansible-driver";
    if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
        // Ansible利用情報
        $cg2 = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102024"));

            // WinRM接続プロトコル追加 
            // Ansible-Legacy/Legacy-Role利用情報
            $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102085"));
                //----汎用性の必要がないバリデーター定義
                $objFunction01 = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
                    global $g;
                    $retBool = true;
                    $retStrBody = '';
                    $strModeId = "";
                    
                    if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
                        if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                            $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                            $strModeId = $aryTcaAction["ACTION_MODE"];
                        }
                    }
                    
                    if($strModeId == "DTUP_singleRecDelete"){
                        $strLoginPw = "";
                        list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW')          ,"");
                        if( $boolRefKeyExists === true ){
                            $strLoginPw = ky_decrypt($strLoginPwEnCoded);
                        }
                        list($intPwHoldFlag       ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW_HOLD_FLAG'),"");
                        list($value               ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_AUTH_TYPE')   ,"");
                    }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                        list($strLoginPw   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW')          ,"");
                        list($intPwHoldFlag,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW_HOLD_FLAG'),"");
                    }
                    
                    if( $strModeId == "DTUP_singleRecDelete" || $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                        $boolPasswordInput = false;
                        $strErrorMsgPreBody = "";
                        if( strlen($value) == 0 || $value == 1 ){
                            //----入力値がない場合または鍵認証の場合
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●とされている場合
                                $boolPasswordInput = true;
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102071");
                                //パスワード管理が●とされている場合----
                            }else{
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102072");
                            }
                            //入力値がない場合または鍵認証の場合----
                        }else if( $value == 2 ){
                            //----パスワード認証の場合
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●とされている場合
                                $boolPasswordInput = true;
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102073");
                                //パスワード管理が●とされている場合----
                            }else{
                                //----パスワード管理が●とされていない場合
                                $retBool = false;
                                $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102074");
                                //パスワード管理が●とされていない場合----
                            }
                            //パスワード認証の場合----
                        }else{
                            //----想定外の値の場合
                            $retBool = false;
                            $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102075");
                            //想定外の値の場合----
                        }
                        
                        if( $retBool === true ){
                            if( $boolPasswordInput === true ){
                                //----ログインパスワードが必須入力の場合
                                if( strlen($strLoginPw) == 0 ){
                                    $retBool = false;
                                    $retStrBody = $strErrorMsgPreBody;
                                }
                                
                                //----更新の場合（敗者復活的なチェック）
                                if( $retBool === false && $strModeId == "DTUP_singleRecUpdate" ){
                                    //----直近入力値は0文字だった場合
                                    list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW')          ,"");
                                    if( $boolRefKeyExists === true ){
                                        $strLoginPw = ky_decrypt($strLoginPwEnCoded);
                                        if( 0 < strlen($strLoginPw) ){
                                            $retBool = true;
                                        }
                                    }
                                    //直近入力値は0文字だった場合----
                                }
                                //更新の場合（敗者復活的なチェック）----
                                
                                //ログインパスワードが必須入力の場合----
                            }else{
                                //----ログインパスワードが入力禁止の場合
                                if( 0 < strlen($strLoginPw) ){
                                    $retBool = false;
                                    $retStrBody = $strErrorMsgPreBody;
                                }
                                //ログインパスワードが入力禁止の場合----
                            }
                        }
                    }
                    $objClientValidator->setValidRule($retStrBody);
                    return $retBool;
                };
                //汎用性の必要がないバリデーター定義----
                
                $objVarVali = new VariableValidator();
                //----廃止/復活時にも、バリデーションチェックを走らせるためのフラグをON
                $objVarVali->setCheckType("DeleteTableFormatterOff");
                $objVarVali->setErrShowPrefix(false);
                $objVarVali->setFunctionForIsValid($objFunction01);
                
                $c = new IDColumn('LOGIN_AUTH_TYPE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102088"),'B_LOGIN_AUTH_TYPE','LOGIN_AUTH_TYPE_ID','LOGIN_AUTH_TYPE_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102089"));//エクセル・ヘッダでの説明
                $c->addValidator($objVarVali);
                $cg->addColumn($c);

                // WinRM接続情報
                $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-104600"));
                    // ポート番号
                    $c = new NumColumn('WINRM_PORT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104605"));
                    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104606"));
                    $c->setSubtotalFlag(false);
                    $c->setValidator(new IntNumValidator(1,65535));

                    $cg3->addColumn($c);

                    // サーバー証明書
                    $c = new FileUploadColumn('WINRM_SSL_CA_FILE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104610"));
                    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104611"));
                    $c->setMaxFileSize(10240);//単位はバイト
                    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
                    $c->setFileHideMode(true);
                    $cg3->addColumn($c);

                $cg->addColumn($cg3);

        $cg2->addColumn($cg);

            $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-102025") );

                $c = new IDColumn('PROTOCOL_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102030"),'B_PROTOCOL','PROTOCOL_ID','PROTOCOL_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102040"));//エクセル・ヘッダでの説明
                $cg->addColumn($c);

                $c = new IDColumn('OS_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102090"),'D_OS_TYPE','OS_TYPE_ID','OS_TYPE_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103010"));//エクセル・ヘッダでの説明
                $cg->addColumn($c);

        $cg2->addColumn($cg);

        $objVldt = new SingleTextValidator(0,512,false);
        $c = new TextColumn('SSH_EXTRA_ARGS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104615"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104616"));
        $c->setValidator($objVldt);
        $cg2->addColumn($c);

        $objVldt = new MultiTextValidator(0,512,false);
        $c = new MultiTextColumn('HOSTS_EXTRA_ARGS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104620"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104621"));
        $c->setValidator($objVldt);
        $cg2->addColumn($c);

        // AnsibleTower利用情報
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102026"));

           // インスタンスグループ
           $c = new IDColumn('ANSTWR_INSTANCE_GROUP_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104630"),
                              'B_ANS_TWR_INSTANCE_GROUP', 'INSTANCE_GROUP_NAME', 'INSTANCE_GROUP_NAME','');
           $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104631"));
           $cg->addColumn($c);

        $cg2->addColumn($cg);

        $table->addColumn($cg2);
    }

    $wanted_filename = "ita_cobbler-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ){
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-103015") );

            $c = new IDColumn('COBBLER_PROFILE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103020"),'C_COBBLER_PROFILE','COBBLER_PROFILE_ID','COBBLER_PROFILE_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103030"));//エクセル・ヘッダでの説明
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,256,false);
            $c = new TextColumn('INTERFACE_TYPE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103040"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103050"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $strPattern = "/^(([0-9A-Za-z][0-9A-Za-z][:]){5}[0-9A-Za-z][0-9A-Za-z])*$/";
            $objVldt = new TextValidator(0, 17, false, $strPattern, "(xx:xx:xx:xx:xx:xx)");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c = new TextColumn('MAC_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103060"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103070"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $c->setUnique(true);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,15,false);
            $c = new TextColumn('NETMASK',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103080"));
            $c->setRequired(false); // 非必須
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103090"));//エクセル・ヘッダでの説明
            $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
            $objVldt = new TextValidator(0, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,15,false);
            $c = new TextColumn('GATEWAY',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104010"));
            $c->setRequired(false); // 非必須
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104020"));//エクセル・ヘッダでの説明
            $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
            $objVldt = new TextValidator(0, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $objVldt = new TextValidator(0, 1, false, "/^$|^(0|1)$/", " [0|1] ");
            $c = new TextColumn('STATIC',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104030"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104040"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

        $table->addColumn($cg);
    }

    $wanted_filename = "ita_dsc-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ){
        //DSC
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-104501") );

            $c = new FileUploadColumn('DSC_CERTIFICATE_FILE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104502"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104503"));
            $c->setMaxFileSize(10240);//単位はバイト
            $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
            $c->setFileHideMode(true);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,256,false);
            $c = new TextColumn('DSC_CERTIFICATE_THUMBPRINT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104504"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104505"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

        $table->addColumn($cg);
    }

    // 登録/更新/廃止/復活があった場合、データベースを更新した事をマークする。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            $strQuery = "UPDATE A_PROC_LOADED_LIST "
                       ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                       ."WHERE ROW_ID IN (2100020002,2100020004,2100020006) ";

            $aryForBind = array();

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

            if( $aryRetBody[0] !== true ){
                $boolRet = false;
                $strErrMsg = $aryRetBody[2];
                $intErrorType = 500;
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['SYSTEM_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
