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
    //    ・登録/更新のテーブル領域に、プルダウンリストHtmlタグを、事後的に作成する
    //
    //////////////////////////////////////////////////////////////////////

    function registerTableMain($intBaseMode, $reqRegisterData=null, $strTCASRKey=null, $ordMode=0, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        
        //----$ordMode=0[ブラウザからの新規登録
        //----$ordMode=1[EXCEL]からの新規登録
        //----$ordMode=2[CSV]からの新規登録
        //----$ordMode=3[JSON]からの新規登録

        //----返し値:$varRet
        //----処理結果次第で書き換えるグローバル変数：$g['error_flag']

        $varRet = null;

        $intControlDebugLevel01=50;

        // ----ローカル変数宣言
        $intErrorType = null;
        $error_str = "";
        $strErrorBuf = "";
        $strSysErrMsgBody = "";
        
        $strResultCode = "000";
        $strErrorCode = "000";
        $strDetailCode = "000";
        $strOutputStr = "";

        $varTrzStart = null;
        $varCommit = null;
        $varRollBack = null;
        $varTrzExit = null;

        $varCommitSpan = null;

        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----D-TUP共通
            //----システムエラーが発生していた場合はスキップ
            if( array_key_exists("system_error",$g) === true ){
                $intErrorType = 901;
                throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //システムエラーが発生していた場合はスキップ----
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }
            if( array_key_exists("objTable",$aryVariant) === true ){
                $objTable = $aryVariant['objTable'];
            }
            else{
                $systemFile = "{$g['root_dir_path']}/webconfs/systems/{$g['page_dir']}_loadTable.php";
                $userFile = "{$g['root_dir_path']}/webconfs/users/{$g['page_dir']}_loadTable.php";
                if(file_exists($systemFile)){
                    require_once($systemFile);
                }
                else if(file_exists($userFile)){
                    require_once($userFile);
                }
                else{
                    $intErrorType = 901;
                    throw new Exception( 'ERROR LOADING (' . $g['page_dir'] . '}_loadTable.php)-[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $objTable = loadTable($strTCASRKey);
            }
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorType = 501;
                throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            $strTableIUDFrom="";
            if( array_key_exists("TABLE_IUD_SOURCE",$aryVariant) === true ){
                $strTableIUDFrom = $aryVariant["TABLE_IUD_SOURCE"];
            }
            else{
                $aryVariant["TABLE_IUD_SOURCE"] = $strTableIUDFrom;
            }
            //D-TUP共通----

            //----固有
            $strFormatterId = "register_table";
            if( array_key_exists("FORMATTER_ID",$aryVariant) === true ){
                $strFormatterId = $aryVariant['FORMATTER_ID'];
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "RegisterTableFormatter") !== true ){
                // ----RegisterTableFormatterクラスではない
                $intErrorType = 501;
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // RegisterTableFormatterクラスではない----
            }
            
            $lcRequiredLUUserColId = $objTable->getRequiredLastUpdateUserColumnID(); //"LAST_UPDATE_USER"
            $lcRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"

            if(array_key_exists($lcRequiredLUUserColId,$reqRegisterData)===true){
                unset($reqRegisterData[$lcRequiredLUUserColId]);
            }
            
            if( isset($aryVariant["TCA_PRESERVED"])===false){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTUP_singleRecRegister");
            //固有----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTUP_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }

            if( $strPrivilege === "1" ){
                // ----1はメンテナンス権限あり
                // 1はメンテナンス権限あり----
            }else if( $strPrivilege === "2" ){
                /// ----2は参照のみ
                $intErrorType = 1;
                throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 2は参照のみ----
            }else{
                // ----0は権限がないので出力しない
                $intErrorType = 1;
                throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            $arrayObjColumn = $objTable->getColumns();

            // ----モードによって処理分岐
            switch($intBaseMode)
            {
                case 0 :
                    // ----初期画面(mode=0)
                    
                    $strOutputStr = $objListFormatter->printWebUIStartForm($arySetting,$objTable);
                    
                    break;
                    
                    // 初期画面(mode=0)----
                case 1 :
                    // ----登録フォーム画面(mode=1)
                    
                    $strOutputStr = $objListFormatter->printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId);
                    
                    break;
                    
                    // 登録フォーム画面(mode=1)----
                case 2 :
                    // ----登録実行処理＆結果画面(mode=2)
                    
                    $boolZenHanDistinct = $objTable->getFormatter($strFormatterId)->getGeneValue("zenHanDistinct");
                    if( $ordMode == 0 ){
                        //[ブラウザ]
                        hiddenColumnIdDecode($objTable,$reqRegisterData);
                        $varCommitSpan = 1;
                    }else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                        //[EXCEL/CSV/JSON]
                        $varCommitSpan = $objTable->getCommitSpanOnTableIUDByFile();
                    }else{
                        throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    if( $varCommitSpan===1 ){
                        //----トランザクション開始
                        $varTrzStart = $g['objDBCA']->transactionStart();
                        if( $varTrzStart !== true ){
                            $intErrorType = 500;
                            throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        if( $objTable->inTrzLockSequences($arrayObjColumn)===false ){
                            $intErrorType = 500;
                            throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        foreach($arrayObjColumn as $objColumn){
                            $arrayTmp = $objColumn->afterTrzStartAction($aryVariant);
                            if($arrayTmp[0]===false){
                                $intErrorType = $arrayTmp[1];
                                $error_str = $arrayTmp[3];
                                $strErrorBuf = $arrayTmp[4];
                                throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                        }
                        //トランザクション開始----
                    }
                    
                    $editTgtRow = array();
                    $aryVariant['edit_target_row'] =& $editTgtRow;
                    
                    $reqRegisterData[$lcRequiredUpdateDate4UColumnId] = null;
                    
                    $exeRegisterData = array();
                    $exeRegisterData[$objTable->getRIColumnID()] = null;

                    $numWkPk = null;

                    $g['objDBCA']->setQueryTime();

                    //----[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加・IDColumnの値の変換
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeIUDValidateCheck($exeRegisterData, $reqRegisterData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    unset($arrayTmp);
                    //[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加・IDColumnの値の変換----

                    $aryVariant['arySqlExe_register_table'] =& $exeRegisterData;

                    //----本格的なバリデーションチェック
                    foreach($reqRegisterData as $key => $value){
                        if(array_key_exists($key,$arrayObjColumn)){
                            $objColumn = $arrayObjColumn[$key];
                            if(gettype($value)=="object"){
                                if( get_class($value) === "PHPExcel_RichText" ){
                                    $value = $value->getPlainText();
                                }
                                else{
                                    $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-151", $objColumn->getColLabel(true));
                                    $intErrorType = 2;
                                }
                            }
                            if( true ){
                                if($value != null){
                                    if($boolZenHanDistinct === false){
                                        //----a:全角英数を半角英数に、s:全角スペースを半角スペースに、KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)
                                        $value = convert_mb_kana_for_fazzyMode($value);
                                        //a:全角英数を半角英数に、s:全角スペースを半角スペースに、KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)----
                                    }
                                }
                                $dlcValidateSkip = false;
                                if($objColumn->isRegisterRequireExcept()!==false){
                                    if($value == "" && $objColumn->isRegisterRequireExcept()===1 ){
                                        $dlcValidateSkip = true;
                                    }
                                }
                                if($dlcValidateSkip !== true){
                                    $objMultiValidator = $objColumn->getValidator();
                                    if($objMultiValidator->isValid($value, $numWkPk, $reqRegisterData, $aryVariant)===true){
                                        $exeRegisterData[$key] = $value;
                                    }else{
                                        $arrayRule=$objMultiValidator->getValidRule();
                                        $arrayPrefix=$objMultiValidator->getShowPrefixs();
                                        $intColumnErrSeq=0;
                                        foreach($arrayRule as $data){
                                            $intColumnErrSeq+=1;
                                            if($arrayPrefix[$intColumnErrSeq - 1]!==false){
                                                $error_str .= $objColumn->getColLabel(true).":{$data}\n";
                                            }else{
                                                $error_str .= "{$data}\n";
                                            }
                                        }
                                        $intErrorType = 2;
                                    }
                                }
                            }
                        }
                    }

                    $boolRequiredColCheckSkip = null;
                    $aryRequiredColCheckSkip = $objListFormatter->getGeneValue("requiredColumnCheckSkip");
                    if( is_array($aryRequiredColCheckSkip)===true ){
                        if( array_key_exists("value",$aryRequiredColCheckSkip)===true ){
                            $boolRequiredColCheckSkip = $aryRequiredColCheckSkip["value"];
                        }
                    }
                    if(is_bool($boolRequiredColCheckSkip)===false){
                        list($boolRequiredColCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_sub_order","requiredColumnCheckSkip"),false);
                    }
                    if($boolRequiredColCheckSkip!==true){
                        //----入力必須カラムの判定
                        foreach($arrayObjColumn as $objColumn){
                            $boolRequiredCheck = false;
                            $tmpVarRequired = $objColumn->isRequired();
                            if( $tmpVarRequired===true ){
                                if( $objColumn->isDBColumn()===true ){
                                    $boolRequiredCheck=true;
                                }
                            }
                            if( $boolRequiredCheck===true ){
                                if(array_key_exists($objColumn->getID(), $reqRegisterData)===false || strlen($reqRegisterData[$objColumn->getID()]) === 0){
                                    if($objColumn->isRegisterRequireExcept()!==false){
                                    }else{
                                        $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-152", $objColumn->getColLabel(true));
                                        $intErrorType = 2;
                                    }
                                }
                            }
                        }
                        //入力必須カラムの判定----
                    }

                    if( $intErrorType !== null ){
                        throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    if( $objTable->getDBMainTableHiddenID() != "" ){
                        //----書き込み対象テーブル名が、別途指定されていた場合
                        foreach($exeRegisterData as $key=>$value){
                            $objColumn=$arrayObjColumn[$key];
                            if( $objColumn->isHiddenMainTableColumn()===true){
                            }else{
                                unset($exeRegisterData[$key]);
                            }
                        }
                        //書き込み対象テーブル名が、別途指定されていた場合----
                    }
                    
                    //----登録前の処理
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeTableIUDAction($exeRegisterData, $reqRegisterData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //登録前の処理----

                    $editTgtRow = array();
                    $aryVariant['edit_target_row'] =& $editTgtRow;
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzBeforeTableIUDAction($exeRegisterData, $reqRegisterData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                        }
                    }
                    
                    $boolUniqueCheckSkip = null;
                    $aryUniqueCheckSkip = $objListFormatter->getGeneValue("uniqueCheckSkip");
                    if( is_array($aryUniqueCheckSkip)===true ){
                        if( array_key_exists("value",$aryUniqueCheckSkip)===true ){
                            $boolUniqueCheckSkip = $aryUniqueCheckSkip["value"];
                        }
                    }
                    if(is_bool($boolUniqueCheckSkip)===false){
                        list($boolUniqueCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_sub_order","uniqueCheckSkip"),false);
                    }
                    if($boolUniqueCheckSkip!==true){
                        $tmpRetArray = checkMultiColumnUnique($objTable, $exeRegisterData,$aryVariant);
                        if($tmpRetArray[0] !== 0){
                            $intErrorType = $tmpRetArray[0];
                            $error_str .= $tmpRetArray[1];
                        }
                        unset($tmpRetArray);
                        if( $intErrorType !== null ){
                            throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    // ----UPDATE命令を組み立てる
                    list($sql,$aryDataForBind)=generateRegisterSQL($exeRegisterData, $arrayObjColumn, $objTable->getDBMainTableID(), $objTable->getDBMainTableHiddenID());
                    // UPDATE命令を組み立てる----
                    
                    $retArray = singleSQLExecuteAgent($sql, $aryDataForBind, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $resultRowLength = $objQuery->effectedRowCount();
                        unset($objQuery);
                        if($resultRowLength == 1){
                        }else{
                            $intErrorType = 500;
                            throw new Exception( '00001600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }                                                            
                    }
                    else{
                        $intErrorType = 500;
                        throw new Exception( '00001700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzAfterTableIUDAction($exeRegisterData, $reqRegisterData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->afterTableIUDAction($exeRegisterData, $reqRegisterData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    //----DB更新後の処理が定義されている場合、ここで実行する
                    $objFunction01ForOverride = null;
                    $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
                    if( $aryFunctionForOverride !== null ){
                         list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("registerTableMain",$strFormatterId,"afterRegist"),null);
                         unset($tmpBoolKeyExist);
                         if( is_callable($tmpObjFunction01ForOverride) === true ){
                             $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                         }
                         unset($tmpObjFunction01ForOverride);
                    }

                    if( $objFunction01ForOverride !== null ){
                        $tmpAryRet = $objFunction01ForOverride($intBaseMode, $reqRegisterData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);

                        if( $tmpAryRet[1] !== null ){
                            $intErrorType = $tmpAryRet[1];
                            $error_str = $tmpAryRet[2];
                            throw new Exception( '00001950-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //DB更新後の処理が定義されている場合、ここで実行する----

                    if( $varCommitSpan===1 ){
                        $varCommit = $g['objDBCA']->transactionCommit();
                        if( $varCommit !== true ){
                            $intErrorType = 500;
                            throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $g['objDBCA']->transactionExit();
                        
                        //----トランザクション終了
                    }
                    $strDetailCode = "201";

                    if( $ordMode == 0 ){
                        //----[ブラウザ]からの新規登録
                        $refExeRegisterData = &$exeRegisterData;
                        $refValue = array("caller"=>"registerTableMain"
                                          ,"ordMode"=>$ordMode
                                          ,"mode"=>$intBaseMode
                                          ,"refExeRegisterData"=>$refExeRegisterData
                                          ,"refVariant"=>$aryVariant
                                          ,"refSetting"=>$arySetting
                                          ,"objListFormatter"=>$objListFormatter
                                    );
                        $objTable->commonEventHandlerExecute($refValue);
                        
                        $strOutputStr = $objListFormatter->printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,$exeRegisterData);
                        //[ブラウザ]からの新規登録----
                    }
                    else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                        //----[EXCEL/CSV/JSON]からの新規登録
                        //[EXCEL/CSV/JSON]からの新規登録----
                    }
                    
                    break;
                    
                    // 登録実行処理＆結果画面(mode=2)----
                default:
                    throw new Exception( '00002100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            // モードによって処理分岐----
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intBaseMode)
            {
                case 2 :
                    // ----登録実行処理＆結果画面(mode=2)
                    if( $varTrzStart === true ){
                        $varRollBack = $g['objDBCA']->transactionRollBack();
                        if( $varRollBack === false ){
                            //----1回目のロールバックが失敗してしまった場合
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-153"));
                            //1回目のロールバックが失敗してしまった場合----
                        }
                        $varTrzExit = $g['objDBCA']->transactionExit();
                        if( $varTrzExit === false ){
                            //----トランザクションが終了できないので以降は緊急停止
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-154"));
                            exit();
                            //トランザクションが終了できないので以降は緊急停止----
                        }
                    }

                    $aryTemp=$objListFormatter->errorHandleForIUD($arySetting,$intErrorType);
                    if($aryTemp[0]!=="") $strResultCode = $aryTemp[0];
                    if($aryTemp[1]!=="") $error_str = $aryTemp[1];

                    if( $intErrorType !== 2 ){
                        if( $ordMode == 0 ){
                            //----[ブラウザから]のINSERT
                            $error_str = $objListFormatter->printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str);
                            //[ブラウザから]のINSERT----
                        }
                        else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                            //----[EXCEL/CSV/JSON]からのINSERT
                            //[EXCEL/CSV/JSON]からのINSERT----
                        }
                    }
                    break;
                    // 登録実行処理＆結果画面(mode=2)----
                default:
                    break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイザー向けメッセージを作成
                //ロードテーブルカスタマイザー向けメッセージを作成----
            }
            //----システムエラー級エラーの場合はWebログにも残す
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
            if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
            //システムエラー級エラーの場合はWebログにも残す----
            $strOutputStr = $error_str;
            if( $ordMode == 0 ){
                $strOutputStr = nl2br($strOutputStr);
            }
        }
        unset($aryVariant["TCA_PRESERVED"]["TCA_ACTION"]);
        //unset($aryVariant["DTUP_LIST_FORMATTER"]);
        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $varRet;
        //返し値[文字列型]----
    }
?>
