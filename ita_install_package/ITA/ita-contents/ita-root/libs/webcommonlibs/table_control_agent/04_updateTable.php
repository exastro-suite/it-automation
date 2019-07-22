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

    function updateTableMain($intBaseMode, $strNumberForRI, $reqUpdateData=null, $strTCASRKey=null, $ordMode=0, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        
        //----$ordMode=0[ブラウザからのUPDATE]
        //----$ordMode=1[EXCEL]からのUPDATE
        //----$ordMode=2[CSV]からのUPDATE
        //----$ordMode=3[JSON]からのUPDATE

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
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //システムエラーが発生していた場合はスキップ----
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
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
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
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
            $strFormatterId = "update_table";
            if( array_key_exists("FORMATTER_ID",$aryVariant) === true ){
                $strFormatterId = $aryVariant['FORMATTER_ID'];
            }
            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "UpdateTableFormatter") !== true ){
                // ----UpdateTableFormatterクラスではない
                $intErrorType = 501;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // UpdateTableFormatterクラスではない----
            }

            $lcRequiredLUUserColId = $objTable->getRequiredLastUpdateUserColumnID(); //"LAST_UPDATE_USER"
            $lcRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID(); //"DISUSE_FLAG"

            if(array_key_exists($lcRequiredLUUserColId,$reqUpdateData)===true){
                unset($reqUpdateData[$lcRequiredLUUserColId]);
            }

            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTUP_singleRecUpdate");
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
                case 1 :
                    // ----更新情報入力(mode=1)
                    //
                    // ----更新対象レコードをSELECT
                    $arrayResult = selectRowForUpdate($objTable, $strNumberForRI, $ordMode, 0);
                    $selectRowLength = $arrayResult[0];
                    $editTgtRow = $arrayResult[1];
                    $intErrorType = $arrayResult[2];
                    // 更新対象レコードをSELECT----

                    if($selectRowLength == 1){
                        $strOutputStr = $objListFormatter->printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId,$strNumberForRI,$editTgtRow);
                    }else{
                        $objColumn = $arrayObjColumn[$objTable->getRIColumnID()];
                        $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-171",array($objColumn->getColLabel(true),$strNumberForRI,$selectRowLength));
                    }
                    break;
                    // 更新情報入力(mode=1)----
                case 3 :
                    // ----更新処理実行(mode=3)
                    
                    $boolZenHanDistinct = $objTable->getFormatter($strFormatterId)->getGeneValue("zenHanDistinct");
                    if($ordMode == 0){
                        //[ブラウザ]
                        hiddenColumnIdDecode($objTable,$reqUpdateData);
                        $varCommitSpan = 1;
                    }else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                        //[EXCEL/CSV/JSON]
                        $varCommitSpan = $objTable->getCommitSpanOnTableIUDByFile();
                    }else{
                        $intErrorType = 500;
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
                        //ランザクション開始----
                    }                    
                    
                    // ----一旦SELECTしてレコードの追い越し更新がないかチェックする
                    $arrayResult = selectRowForUpdate($objTable, $strNumberForRI, $ordMode, 0);
                    $selectRowLength = $arrayResult[0];
                    $editTgtRow = $arrayResult[1];
                    $aryVariant['edit_target_row'] =& $editTgtRow;
                    // 一旦SELECTしてレコードの追い越し更新がないかチェックする----
                    
                    if($selectRowLength == 1){
                        //----更新対象の行が特定できた
                        if( $editTgtRow[$lcRequiredDisuseFlagColumnId] == "1" && $intBaseMode==3) {
                            // ----更新依頼されたが、削除済：削除済み(11)を返却する。
                            $intErrorType = 212;
                            throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 更新依頼されたが、削除済：削除済み(11)を返却する。----
                        }else if( $reqUpdateData[$lcRequiredUpdateDate4UColumnId] != $editTgtRow[$lcRequiredUpdateDate4UColumnId] ) {
                            //----追い越し判明
                            $intErrorType = 201;
                            throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //追い越し判明----
                        }
                        //更新対象の行が特定できた----
                    }else{
                        //----更新対象の行が特定できなかった
                        $intErrorType = 101;
                        throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //更新対象の行が特定できなかった----
                    }
                    
                    $exeUpdateData = array();
                    $exeUpdateData[$objTable->getRIColumnID()] = $strNumberForRI;

                    $g['objDBCA']->setQueryTime();

                    //----[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加・IDColumnの値の変換
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeIUDValidateCheck($exeUpdateData, $reqUpdateData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    unset($arrayTmp);
                    //[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加・IDColumnの値の変換----

                    $aryVariant['arySqlExe_update_table'] =& $exeUpdateData;

                    foreach($reqUpdateData as $key => $value){
                        if(array_key_exists($key, $arrayObjColumn)){
                            $dlcValidateSkip = false;
                            $objColumn = $arrayObjColumn[$key];
                            if(gettype($value)=="object"){
                                $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-172", $objColumn->getColLabel(true));
                                $intErrorType = 2;
                            }else{
                                if($value != null){
                                    if($boolZenHanDistinct === false){
                                        //----a:全角英数を半角英数に、s:全角スペースを半角スペースに、KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)
                                        $reqUpdateData[$key] = convert_mb_kana_for_fazzyMode($value);
                                        //a:全角英数を半角英数に、s:全角スペースを半角スペースに、KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)----
                                    }
                                }
                                if($objColumn->isUpdateRequireExcept()!==false){
                                    if($value == "" && $objColumn->isUpdateRequireExcept()===1 ){
                                        $dlcValidateSkip = true;
                                    }
                                }
                                if($dlcValidateSkip !== true){
                                    $objMultiValidator = $objColumn->getValidator();
                                    if($objMultiValidator->isValid($value, $strNumberForRI, $reqUpdateData, $aryVariant)===true){
                                        $exeUpdateData[$key] = $value;
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
                                if(array_key_exists($objColumn->getID(),$reqUpdateData)===false || strlen($reqUpdateData[$objColumn->getID()]) === 0){
                                    if($objColumn->isUpdateRequireExcept()!==false){
                                    }else{
                                        // ファイルアップロードカラム、かつ、
                                        // 削除ボタンがONではない、かつ、
                                        // 更新前に値が入っている場合は必須チェックOKとする
                                        if("FileUploadColumn" === get_class($objColumn) &&
                                           !array_key_exists("del_flag_". $objColumn->getIDSOP(), $reqUpdateData) &&
                                           array_key_exists($objColumn->getID(), $aryVariant['edit_target_row']) &&
                                           "" != $aryVariant['edit_target_row'][$objColumn->getID()]
                                          ){
                                        }else{
                                            $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-173", $objColumn->getColLabel(true));
                                            $intErrorType = 2;
                                        }
                                    }
                                }
                                // 値が定義されている、かつ、
                                // ファイルアップロードカラム、かつ、
                                // 削除ボタンがONの場合はエラー
                                else if($objColumn->isUpdateRequireExcept()===false &&
                                        "FileUploadColumn" === get_class($objColumn) &&
                                        array_key_exists("del_flag_". $objColumn->getIDSOP(), $reqUpdateData)
                                       ){
                                    $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-173", $objColumn->getColLabel(true));
                                    $intErrorType = 2;
                                }
                            }
                        }
                        //入力必須カラムの判定----
                    }

                    if( $intErrorType !== null ){
                        throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    if( $objTable->getDBMainTableHiddenID() != "" ){
                        //----書き込み対象テーブル名が、別途指定されていた場合
                        foreach($exeUpdateData as $key=>$value){
                            $objColumn=$arrayObjColumn[$key];
                            if( $objColumn->isHiddenMainTableColumn() === true ){
                                //----そのまま通す
                            }else{
                                unset($exeUpdateData[$key]);
                            }
                        }
                        //書き込み対象テーブル名が、別途指定されていた場合----
                    }
                    
                    //----登録前の処理
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeTableIUDAction($exeUpdateData, $reqUpdateData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //登録前の処理----

                    // ----一旦SELECTしてレコードの追い越し更新がないかチェックする
                    $arrayResult = selectRowForUpdate($objTable, $strNumberForRI, $ordMode, 1);
                    $selectRowLength = $arrayResult[0];
                    $editTgtRow = $arrayResult[1];
                    $aryVariant['edit_target_row'] =& $editTgtRow;
                    // 一旦SELECTしてレコードの追い越し更新がないかチェックする----
                    //
                    if($selectRowLength == 1){
                        //----更新対象の行が特定できた
                        if( $editTgtRow[$lcRequiredDisuseFlagColumnId] == "1" && $intBaseMode==3) {
                            // ----更新依頼されたが、削除済：削除済み(11)を返却する。
                            $intErrorType = 212;
                            throw new Exception( '00001700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 更新依頼されたが、削除済：削除済み(11)を返却する。----
                        }else if( $reqUpdateData[$lcRequiredUpdateDate4UColumnId] != $editTgtRow[$lcRequiredUpdateDate4UColumnId] ) {
                            //----追い越し判明
                            $intErrorType = 201;
                            throw new Exception( '00001800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //追い越し判明----
                        } 
                        //更新対象の行が特定できた----
                    }else{
                        //----更新対象の行が特定できなかった
                        $intErrorType = 101;
                        throw new Exception( '00001900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //更新対象の行が特定できなかった----
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzBeforeTableIUDAction($exeUpdateData, $reqUpdateData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
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
                        $tmpRetArray = checkMultiColumnUnique($objTable, $exeUpdateData, $aryVariant);
                        if($tmpRetArray[0] !== 0){
                            $intErrorType = $tmpRetArray[0];
                            $error_str .= $tmpRetArray[1];
                        }
                        unset($tmpRetArray);
                        if( $intErrorType !== null ){
                            throw new Exception( '00002100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    
                    // ----UPDATE命令を組み立てる
                    list($sql,$aryDataForBind)=generateUpdateSQL($exeUpdateData, $arrayObjColumn, $objTable->getRIColumnID(), $objTable->getDBMainTableID(), $objTable->getDBMainTableHiddenID());
                    // UPDATE命令を組み立てる----
                    
                    $retArray = singleSQLExecuteAgent($sql, $aryDataForBind, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $resultRowLength = $objQuery->effectedRowCount();
                        unset($objQuery);
                        if($resultRowLength == 1){
                        }else{
                            $intErrorType = 500;
                            throw new Exception( '00002200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    else{
                        $intErrorType = 500;
                        throw new Exception( '00002300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzAfterTableIUDAction($exeUpdateData, $reqUpdateData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                        }
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->afterTableIUDAction($exeUpdateData, $reqUpdateData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                    }

                    //----DB更新後の処理が定義されている場合、ここで実行する
                    $objFunction01ForOverride = null;
                    $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
                    if( $aryFunctionForOverride !== null ){
                         list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("updateTableMain",$strFormatterId,"afterUpdate"),null);
                         unset($tmpBoolKeyExist);
                         if( is_callable($tmpObjFunction01ForOverride) === true ){
                             $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                         }
                         unset($tmpObjFunction01ForOverride);
                    }

                    if( $objFunction01ForOverride !== null ){
                        $tmpAryRet = $objFunction01ForOverride($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);

                        if( $tmpAryRet[1] !== null ){
                            $intErrorType = $tmpAryRet[1];
                            $error_str = $tmpAryRet[2];
                            throw new Exception( '00002510-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //DB更新後の処理が定義されている場合、ここで実行する----
                    
                    if( $varCommitSpan===1 ){
                        //トランザクション終了----
                        $varCommit = $g['objDBCA']->transactionCommit();
                        if( $varCommit !== true ){
                            $intErrorType = 500;
                            throw new Exception( '00002600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        //
                        $g['objDBCA']->transactionExit();
                        //----トランザクション終了
                    }
                    $strDetailCode = "200";
                    
                    if( $ordMode == 0 ){
                        //----[ブラウザ]からの更新
                        $refExeUpdateData = &$exeUpdateData;
                        $refValue = array("caller"=>"updateTableMain"
                                          ,"ordMode"=>$ordMode
                                          ,"mode"=>$intBaseMode
                                          ,"refExeUpdateData"=>$refExeUpdateData
                                          ,"refVariant"=>$aryVariant
                                          ,"refSetting"=>$arySetting
                                          ,"objListFormatter"=>$objListFormatter
                                    );
                        $objTable->commonEventHandlerExecute($refValue);
                        //
                        $strOutputStr = $objListFormatter->printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,$exeUpdateData);
                        //[ブラウザ]からの更新----
                    }
                    else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                        //----[EXCEL/CSV/JSON]からの更新
                        //[EXCEL/CSV/JSON]からの更新----
                    }
                    break;
                    // 更新処理実行(mode=3)----
                default:
                    throw new Exception( '00002700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intBaseMode)
            {
                case 3 :
                    // ----更新処理実行(mode=3)
                    if( $varTrzStart === true ){
                        $varRollBack = $g['objDBCA']->transactionRollBack();
                        if( $varRollBack === false ){
                            //----1回目のロールバックが失敗してしまった場合
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-174"));
                            //1回目のロールバックが失敗してしまった場合----
                        }
                        $varTrzExit = $g['objDBCA']->transactionExit();
                        if( $varTrzExit === false ){
                            //----トランザクションが終了できないので以降は緊急停止
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-175"));
                            $intErrorType = 900;
                            $g['system_error'] = $intErrorType;
                            //トランザクションが終了できないので以降は緊急停止----
                        }
                    }

                    $aryTemp=$objListFormatter->errorHandleForIUD($arySetting,$intErrorType);
                    if($aryTemp[0]!=="") $strResultCode = $aryTemp[0];
                    if($aryTemp[1]!=="") $error_str = $aryTemp[1];

                    if( $intErrorType !== 2 ){
                        if( $ordMode == 0 ){
                            //----[ブラウザから]のUPDATE
                            if($intErrorType == 201 || $intErrorType == 212 ){
                                $intErrorType = 3;
                            }
                            $error_str = $objListFormatter->printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str);
                            //[ブラウザから]のUPDATE----
                        }
                        else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                            //----[EXCEL/CSV/JSON]からのUPDATE
                            //[EXCEL/CSV/JSON]からのUPDATE----
                        }
                    }
                    break;
                    // 更新処理実行(mode=3)----
                default :
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
        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $varRet;
    }
?>
