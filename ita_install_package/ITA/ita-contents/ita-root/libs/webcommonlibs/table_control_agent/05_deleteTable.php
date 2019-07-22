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

    //$reqDeleteDataの配列キーとして許されるべきなのは、(1)[業務上の主キー],(2)"DISUSE_FLAG",(3)"NOTE",(4)"UPD_UPDATE_TIMESTAMP",(5)"LAST_UPDATE_USER"のみ

    function deleteTableMain($intBaseMode, $strNumberForRI, $reqDeleteData=null, $strTCASRKey=null, $ordMode=0, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        
        //----$ordMode=0[ブラウザからの廃止/復活]
        //----$ordMode=1[EXCEL]からの廃止/復活
        //----$ordMode=2[CSV]からの廃止/復活
        //----$ordMode=3[JSON]からの廃止/復活

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
            $strFormatterId = "delete_table";
            if( array_key_exists("FORMATTER_ID",$aryVariant) === true ){
                $strFormatterId = $aryVariant['FORMATTER_ID'];
            }
            
            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "DeleteTableFormatter") !== true ){
                // ----DeleteTableFormatterクラスではない
                $intErrorType = 501;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // DeleteTableFormatterクラスではない----
            }

            $lcRequiredLUUserColId = $objTable->getRequiredLastUpdateUserColumnID(); //"LAST_UPDATE_USER"
            $lcRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID(); //"DISUSE_FLAG"
            $lcRequiredNoteColId = $objTable->getRequiredNoteColumnID(); //"NOTE"

            if(array_key_exists($lcRequiredLUUserColId,$reqDeleteData)===true){
                unset($reqDeleteData[$lcRequiredLUUserColId]);
            }

            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTUP_singleRecDelete","ACTION_SUB_MODE"=>"");
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
            switch($intBaseMode) {
                case 1 :
                case 4 :
                    // ----廃止対象レコードをSELECT
                    //
                    // ----更新対象レコードをSELECT
                    $arrayResult = selectRowForUpdate($objTable, $strNumberForRI, $ordMode, 0);
                    $selectRowLength = $arrayResult[0];
                    $editTgtRow = $arrayResult[1];
                    $intErrorType = $arrayResult[2];
                    // 更新対象レコードをSELECT----
                    //
                    if($selectRowLength == 1){
                        $strOutputStr = $objListFormatter->printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId,$strNumberForRI,$editTgtRow,$intBaseMode);
                    }else{
                        $objColumn = $arrayObjColumn[$objTable->getRIColumnID()];
                        $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-190",array($objColumn->getColLabel(true),$strNumberForRI,$selectRowLength));
                    }
                    break;
                case 3:
                case 5:
                    // ----廃止,復活処理実行(mode=3, 5)
                    $strModeBody = "";
                    if($intBaseMode == 3){
                        $strModeBody = "on";
                    }else if($intBaseMode == 5){
                        $strModeBody = "off";
                    }
                    $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"]=$strModeBody;
                    
                    $boolZenHanDistinct = $objTable->getFormatter($strFormatterId)->getGeneValue("zenHanDistinct");
                    if( $ordMode == 0 ){
                        //[ブラウザ]
                        hiddenColumnIdDecode($objTable,$reqDeleteData);
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
                        //トランザクション開始----
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
                            // ----削除依頼されたが、削除済：削除済み(11)を返却する。
                            $intErrorType = 211;
                            throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 削除依頼されたが、削除済：削除済み(11)を返却する。----
                        }else if( $editTgtRow[$lcRequiredDisuseFlagColumnId] == "0" && $intBaseMode==5){
                            // ----復活依頼されたが、復活済：復活済み(21)を返却する。
                            $intErrorType = 221;
                            throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 復活依頼されたが、復活済：復活済み(21)を返却する。----
                        }else if( $reqDeleteData[$lcRequiredUpdateDate4UColumnId] != $editTgtRow[$lcRequiredUpdateDate4UColumnId] ) {
                            //----追い越し判明
                            $intErrorType = 201;
                            throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //追い越し判明----
                        }
                        //更新対象の行が特定できた----
                    }else{
                        //----更新対象の行が特定できなかった
                        $intErrorType = 101;
                        throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //更新対象の行が特定できなかった----
                    }
                    
                    $exeDeleteData = array();
                    $exeDeleteData[$objTable->getRIColumnID()] = $strNumberForRI;

                    $g['objDBCA']->setQueryTime();

                    //----[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeIUDValidateCheck($exeDeleteData, $reqDeleteData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($arrayTmp);
                    }
                    //[1]自動保存系(AutoNumを除く)カラム、を更新対象に追加----

                    $aryVariant['arySqlExe_delete_table'] =& $exeDeleteData;

                    if($intBaseMode == 3)
                    {
                        //----廃止の場合
                        $varCheckValue = null;
                        foreach($arrayObjColumn as $objFocusCol){
                            $colKey = $objFocusCol->getID();
                            if( $objFocusCol->isDBColumn()===true && $objFocusCol->getDeleteOnBeforeCheck()!==false ){
                                //----廃止前・個別チェック
                                list($varCheckValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqDeleteData,array($colKey),null);
                                
                                $objMultiValidator = $objFocusCol->getValidator();
                                $arrayObjValidator = $objMultiValidator->getAllValidator();
                                
                                foreach($arrayObjValidator as $objValidator){
                                    if($objValidator->isValid($varCheckValue, $strNumberForRI, $reqDeleteData, $aryVariant)){
                                    }else{
                                        $data=$objValidator->getValidRule();
                                        if($objValidator->getErrShowPrefix()!==false){
                                            $error_str .= $objFocusCol->getColLabel(true).":{$data}\n";
                                        }else{
                                            $error_str .= "{$data}\n";
                                        }
                                        $intErrorType = 2;
                                    }
                                }
                                //廃止前・個別チェック----
                            }
                        }
                        unset($varCheckValue);
                        //廃止の場合----
                    }
                    else if($intBaseMode == 5)
                    {
                        //----復活の場合
                        $varCheckValue = null;
                        foreach($arrayObjColumn as $objFocusCol){
                            $colKey = $objFocusCol->getID();
                            if( $objFocusCol->isDBColumn()===true && $objFocusCol->getDeleteOffBeforeCheck()!==false ){
                                //----廃止前・個別チェック
                                list($varCheckValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqDeleteData,array($colKey),null);
                                
                                $objMultiValidator = $objFocusCol->getValidator();
                                $arrayObjValidator = $objMultiValidator->getAllValidator();
                                
                                foreach($arrayObjValidator as $objValidator){
                                    if($objValidator->isValid($varCheckValue, $strNumberForRI, $reqDeleteData, $aryVariant)===true){
                                    }else{
                                        $data=$objValidator->getValidRule();
                                        if($objValidator->getErrShowPrefix()!==false){
                                            $error_str .= $objFocusCol->getColLabel(true).":{$data}\n";
                                        }else{
                                            $error_str .= "{$data}\n";
                                        }
                                        $intErrorType = 2;
                                    }
                                }
                                //廃止前・個別チェック----
                            }
                        }
                        unset($varCheckValue);
                        //復活の場合----
                    }

                    $boolRequiredColCheckSkip = null;
                    $aryRequiredColCheckSkip = $objListFormatter->getGeneValue("requiredColumnCheckSkip");
                    if( is_array($aryRequiredColCheckSkip)===true ){
                        if( array_key_exists($strModeBody,$aryRequiredColCheckSkip)===true ){
                            $boolRequiredColCheckSkip = $aryRequiredColCheckSkip[$strModeBody];
                        }
                    }
                    if(is_bool($boolRequiredColCheckSkip)===false){
                        if( $strModeBody=="on" ){
                            // アクション(廃止)は、デフォルトで必須性を判定しない
                            list($boolRequiredColCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","requiredColumnCheckSkip"),true);
                        }else if( $strModeBody=="off" ){
                            // アクション(復活)は、デフォルトで必須性を判定する。
                            list($boolRequiredColCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","requiredColumnCheckSkip"),false);
                        }
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
                                if(array_key_exists($objColumn->getID(),$reqDeleteData)===false || strlen($reqDeleteData[$objColumn->getID()]) === 0){
                                    $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-225", $objColumn->getColLabel(true));
                                    $intErrorType = 2;
                                }
                            }
                        }
                        //入力必須カラムの判定----
                    }

                    if( $intErrorType !== null ){
                        throw new Exception( '00001600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    switch($reqDeleteData[$lcRequiredDisuseFlagColumnId]){
                        case 0:
                        case 1:
                            $exeDeleteData[$lcRequiredDisuseFlagColumnId] = $reqDeleteData[$lcRequiredDisuseFlagColumnId];
                            break;
                        default:
                            $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-191",array($arrayObjColumn[$lcRequiredDisuseFlagColumnId]->getColLabel(true),$reqDeleteData[$lcRequiredDisuseFlagColumnId]));
                            $intErrorType = 2;
                            break;
                    }

                    if( $intErrorType !== null ){
                        throw new Exception( '00001700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $objColNote = $arrayObjColumn[$lcRequiredNoteColId];
                    
                    if(gettype($reqDeleteData[$lcRequiredNoteColId])=="object"){
                        $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-192",$objColNote->getColLabel(true));
                        $intErrorType = 2;
                    }else{
                        
                        if( ( $intBaseMode == 3 && $objColNote->isRequiredWhenDeleteOn()===true ) ||
                            ( $intBaseMode == 5 && $objColNote->isRequiredWhenDeleteOff()===true ) ){
                            //----理由必須の場合
                            if(array_key_exists($lcRequiredNoteColId, $reqDeleteData)==false || $reqDeleteData[$lcRequiredNoteColId] == ""){
                                if(is_string($reqDeleteData[$lcRequiredNoteColId])===true){
                                    $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-193",$objColNote->getColLabel(true));
                                        $intErrorType = 2;
                                    }else{
                                        $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-194",$objColNote->getColLabel(true));
                                    $intErrorType = 2;
                                }
                            }
                            //理由必須の場合----
                        }else{
                            if(array_key_exists($lcRequiredNoteColId, $reqDeleteData)==false){
                                $exeDeleteData[$lcRequiredNoteColId] = "";
                            }
                        }
                        if( $intErrorType === null ){
                            if($objColNote->getValidator()->isValid($reqDeleteData[$lcRequiredNoteColId], $strNumberForRI, $reqDeleteData, $aryVariant)===false){
                                foreach($objColNote->getValidator()->getValidRule() as $data){
                                    $error_str = $objColNote->getColLabel(true).":{$data}\n";
                                }
                                $intErrorType = 2;
                            }else{
                                if(is_string($reqDeleteData[$lcRequiredNoteColId])===true){
                                    if($boolZenHanDistinct === false){
                                        //----全角英数を半角に、半角カナを全角に変換
                                        
                                        $exeDeleteData[$lcRequiredNoteColId] = convert_mb_kana_for_fazzyMode($reqDeleteData[$lcRequiredNoteColId]);
                                        
                                        //全角英数を半角に、半角カナを全角に変換----
                                    }else{
                                        $exeDeleteData[$lcRequiredNoteColId] = $reqDeleteData[$lcRequiredNoteColId];
                                    }
                                }
                            }
                        }
                    }

                    if( $intErrorType !== null ){
                        throw new Exception( '00001800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    //----DBのレコードと比較する
                    $boolCompareCheckSkip = null;
                    $aryCompareCheckSkip = $objListFormatter->getGeneValue("compareCheckSkip");
                    if( is_array($aryCompareCheckSkip)===true ){
                        if( array_key_exists($strModeBody,$aryCompareCheckSkip)===true ){
                            $boolCompareCheckSkip = $aryCompareCheckSkip[$strModeBody];
                        }
                    }
                    if(is_bool($boolCompareCheckSkip)===false){
                        if( $strModeBody=="on" ){
                            // アクション(廃止)は、デフォルトでユニーク性を判定しない
                            list($boolCompareCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","compareCheckSkip"),true);
                        }else if( $strModeBody=="off" ){
                            // アクション(復活)は、デフォルトでユニーク性を判定する。
                            list($boolCompareCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","compareCheckSkip"),false);
                        }
                    }
                    if($boolCompareCheckSkip!==true){
                        $error_str = checkForDeleteTableMain($strModeBody, $objTable, $exeDeleteData, $reqDeleteData, $aryVariant);
                        if($error_str != ""){
                            $intErrorType = 2;
                            throw new Exception( '00001900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //DBのレコードと比較する----

                    if( $objTable->getDBMainTableHiddenID() != "" ){
                        //----書き込み対象テーブル名が、別途指定されていた場合
                        foreach($exeDeleteData as $key=>$value){
                            $objColumn=$arrayObjColumn[$key];
                            if( $objColumn->isHiddenMainTableColumn()===true ){
                                //----そのまま通す
                            }else{
                                unset($exeDeleteData[$key]);
                            }
                        }
                        //書き込み対象テーブル名が、別途指定されていた場合----
                    }

                    //----登録前の処理
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeTableIUDAction($exeDeleteData, $reqDeleteData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //登録前の処理----

                    // ----一旦SELECTしてレコードの追い越し更新がないかチェックする
                    $arrayResult = selectRowForUpdate($objTable, $strNumberForRI, $ordMode, 1);
                    $selectRowLength = $arrayResult[0];
                    $editTgtRow = $arrayResult[1];
                    // 一旦SELECTしてレコードの追い越し更新がないかチェックする----
                    
                    if($selectRowLength == 1){
                        //----更新対象の行が特定できた
                        if( $editTgtRow[$lcRequiredDisuseFlagColumnId] == "1" && $intBaseMode==3) {
                            // ----削除依頼されたが、削除済：削除済み(11)を返却する。
                            $intErrorType = 211;
                            throw new Exception( '00002100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 削除依頼されたが、削除済：削除済み(11)を返却する。----
                        }else if( $editTgtRow[$lcRequiredDisuseFlagColumnId] == "0" && $intBaseMode==5){
                            // ----復活依頼されたが、復活済：復活済み(21)を返却する。
                            $intErrorType = 221;
                            throw new Exception( '00002200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            // 復活依頼されたが、復活済：復活済み(21)を返却する。----
                        }else if( $reqDeleteData[$lcRequiredUpdateDate4UColumnId] != $editTgtRow[$lcRequiredUpdateDate4UColumnId] ) {
                            //----追い越し判明
                            $intErrorType = 201;
                            throw new Exception( '00002300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //追い越し判明----
                        } 
                        //更新対象の行が特定できた----
                    }else{
                        //----更新対象の行が特定できなかった
                        $intErrorType = 101;
                        throw new Exception( '00002400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //更新対象の行が特定できなかった----
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzBeforeTableIUDAction($exeDeleteData, $reqDeleteData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                        }
                    }
                    
                    $boolUniqueCheckSkip = null;
                    $aryUniqueCheckSkip = $objListFormatter->getGeneValue("uniqueCheckSkip");
                    if( is_array($aryUniqueCheckSkip)===true ){
                        if( array_key_exists($strModeBody,$aryUniqueCheckSkip)===true ){
                            $boolUniqueCheckSkip = $aryUniqueCheckSkip[$strModeBody];
                        }
                    }
                    if(is_bool($boolUniqueCheckSkip)===false){
                        if( $strModeBody=="on" ){
                            // アクション(廃止)は、デフォルトでユニーク性を判定しない
                            list($boolUniqueCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","uniqueCheckSkip"),true);
                        }else if( $strModeBody=="off" ){
                            // アクション(復活)は、デフォルトでユニーク性を判定する。
                            list($boolUniqueCheckSkip,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("action_check_order","uniqueCheckSkip"),false);
                        }
                    }
                    if($boolUniqueCheckSkip!==true){
                        $tmpRetArray = checkMultiColumnUnique($objTable, $exeDeleteData, $aryVariant);
                        if($tmpRetArray[0] !== 0){
                            $intErrorType = $tmpRetArray[0];
                            $error_str .= $tmpRetArray[1];
                        }
                        unset($tmpRetArray);
                        if( $intErrorType !== null ){
                            throw new Exception( '00002600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    
                    // ----UPDATE命令を組み立てる
                    list($sql,$aryDataForBind)=generateUpdateSQL($exeDeleteData, $arrayObjColumn, $objTable->getRIColumnID(), $objTable->getDBMainTableID(), $objTable->getDBMainTableHiddenID());
                    // UPDATE命令を組み立てる----
                    
                    $retArray = singleSQLExecuteAgent($sql, $aryDataForBind, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $resultRowLength = $objQuery->effectedRowCount();
                        unset($objQuery);
                        if($resultRowLength == 1){
                        }else{
                            $intErrorType = 500;
                            throw new Exception( '00002700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    else{
                        $intErrorType = 500;
                        throw new Exception( '00002800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->inTrzAfterTableIUDAction($exeDeleteData, $reqDeleteData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00002900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                        }
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->afterTableIUDAction($exeDeleteData, $reqDeleteData, $aryVariant);
                        if($arrayTmp[0]===false){
                            $intErrorType = $arrayTmp[1];
                            $error_str = $arrayTmp[3];
                            $strErrorBuf = $arrayTmp[4];
                            throw new Exception( '00003000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    

                    //----DB更新後の処理が定義されている場合、ここで実行する
                    $objFunction01ForOverride = null;
                    $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
                    if( $aryFunctionForOverride !== null ){
                         list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("deleteTableMain",$strFormatterId,"afterUpdate"),null);
                         unset($tmpBoolKeyExist);
                         if( is_callable($tmpObjFunction01ForOverride) === true ){
                             $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                         }
                         unset($tmpObjFunction01ForOverride);
                    }

                    if( $objFunction01ForOverride !== null ){
                        $tmpAryRet = $objFunction01ForOverride($intBaseMode, $strNumberForRI, $reqDeleteData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);

                        if( $tmpAryRet[1] !== null ){
                            $intErrorType = $tmpAryRet[1];
                            $error_str = $tmpAryRet[2];
                            throw new Exception( '00002510-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //DB更新後の処理が定義されている場合、ここで実行する----

                    if( $varCommitSpan===1 ){
                        //----トランザクション終了
                        $varCommit = $g['objDBCA']->transactionCommit();
                        if( $varCommit !== true ){
                            $intErrorType = 500;
                            throw new Exception( '00003100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $g['objDBCA']->transactionExit();
                        //----トランザクション終了
                    }
                    
                    //----更新が成功した場合
                    if($intBaseMode==3)
                    {
                        //----「廃止の処理」
                        $strDetailCode = "210";
                        //「廃止の処理」----
                    }
                    else if($intBaseMode==5)
                    {
                        //----「復活の処理」
                        $strDetailCode = "200";
                        //「復活の処理」
                    }
                    //更新が成功した場合----

                    if( $ordMode == 0 ){
                        //----[ブラウザ]からの更新
                        $refExeDeleteData = &$exeDeleteData;
                        $refValue = array("caller"=>"deleteTableMain"
                                          ,"ordMode"=>$ordMode
                                          ,"mode"=>$intBaseMode
                                          ,"refExeDeleteData"=>$refExeDeleteData
                                          ,"refVariant"=>$aryVariant
                                          ,"refSetting"=>$arySetting
                                          ,"objListFormatter"=>$objListFormatter
                                    );
                        $objTable->commonEventHandlerExecute($refValue);
                        
                        $strOutputStr = $objListFormatter->printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,$exeDeleteData,$intBaseMode);
                        //[ブラウザ]からの更新----
                    }
                    else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                        //----[EXCEL/CSV/JSON]からの更新
                        //[EXCEL/CSV/JSON]からの更新----
                    }
                    break;
                    // 廃止,復活処理実行(mode=3, 5)----
                default:
                    throw new Exception( '00003200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
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
                case 5 :
                    // ----廃止,復活処理実行(mode=3, 5)
                    if( $varTrzStart === true ){
                        $varRollBack = $g['objDBCA']->transactionRollBack();
                        if( $varRollBack === false ){
                            //----1回目のロールバックが失敗してしまった場合
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-195"));
                            //1回目のロールバックが失敗してしまった場合----
                        }
                        $varTrzExit = $g['objDBCA']->transactionExit();
                        if( $varTrzExit === false ){
                            //----トランザクションが終了できないので以降の処理を緊急停止
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-196"));
                            exit();
                            //トランザクションが終了できないので以降の処理を緊急停止----
                        }
                    }

                    $aryTemp=$objListFormatter->errorHandleForIUD($arySetting,$intErrorType,$intBaseMode);
                    if($aryTemp[0]!=="") $strResultCode = $aryTemp[0];
                    if($aryTemp[1]!=="") $error_str = $aryTemp[1];

                    if( $intErrorType !== 2 ){
                        if( $ordMode == 0 ){
                            //----[ブラウザから]のUPDATE
                            $error_str = $objListFormatter->printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str,$intBaseMode);
                            //[ブラウザから]のUPDATE----
                        }
                        else if( $ordMode == 1 || $ordMode == 2 || $ordMode == 3 ){
                            //----[EXCEL/CSV/JSON]からのUPDATE
                            //[EXCEL/CSV/JSON]からのUPDATE----
                        }
                    }
                    break;
                    // 廃止,復活処理実行(mode=3, 5)----
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
        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $varRet;
    }

    function checkForDeleteTableMain($strModeBody, &$objTable, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        //
        $objColumns=$objTable->getColumns();
        //
        $intControlDebugLevel01=200;
        //
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $ret="";
        $errorColumnName=array();
        $errorColumnCount=0;
        //----比較判定除外のカラム
        $arrayExcept = array($objTable->getRIColumnID()
                             ,$objTable->getRequiredNoteColumnID()
                             ,$objTable->getRequiredDisuseColumnID()
                             );
        //比較判定除外のカラム----
        //----ユーザー入力データのキーの分だけループ
        foreach($reqOrgData as $key=>$value){
            $boolValue=true;
            if(array_key_exists($key,$objColumns)===false){
                continue;
            }
            $objColumn = $objColumns[$key];
            if( $objColumn->isDBColumn()!==true ){
                continue;
            }
            //----DBカラムのみ
            if(in_array($key,$arrayExcept)===true){
                continue;
            }
            if( is_a($objColumn,'AutoUpdateTimeColumn')===true || is_a($objColumn,'AutoUpdateUserColumn')===true ){
                if( $objColumn->getUpdateMode()===true ){
                    continue;
                }
            }
            $boolValue = $objColumn->compareRow($exeQueryData, $reqOrgData, $aryVariant);
            if( $boolValue===false ){
                $errorColumnName[] = "[".str_replace(array("<br>","<br/>","<br />"),"・",$objColumn->getColLabel(true))."]";
                $errorColumnCount+=1;
            }
            //DBカラムのみ----
            unset($objColumn);
        }
        //ユーザー入力データのキーの分だけループ----
        //
        if(0 < $errorColumnCount){
            if( $strModeBody=="on" ){
                //----廃止の場合
                $ret  = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-210");
                //廃止の場合----
            }else if( $strModeBody=="off" ){
                //----復活の場合
                $ret  = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-211");
                //復活の場合----
            }
            $ret .= "(".implode(",",$errorColumnName).")\n";
        }
        
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $ret;
    }
    
?>
