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

    function printTableMain($objTable, $strFormatterId="print_table", $mode, $filterData, &$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        //
        // return値
        $varRet = null;
        //
        $intErrorType = null;
        $error_str = "";
        $strErrorBuf = "";
        $strSysErrMsgBody = "";

        $strResultCode = "000";
        $strErrorCode = "000";
        $strDetailCode = "000";
        $strOutputStr = "";

        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable02TagId = "";
        $strShowTable02WrapDivClass = "";

        $row_counter = 0;

        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;

        $sql1 = "";
        $sql2 = "";

        $defaultValueOnFx = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeSubtotal_Table");
        $refRetKeyExists = null;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----D-TiS共通
            $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
            $checkFormatterId = $retArray[1];
            $objListFormatter = $retArray[2];

            $defaultValueOnFx = checkOverrideValue($strFxName, $defaultValueOnFx, $aryOverride);
            //D-TiS共通----

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
            $arrayObjColumn = $objTable->getColumns();

            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID(); //"DISUSE_FLAG"
            $lcRequiredUpdateButtonColumnId = $objTable->getRequiredUpdateButtonColumnID(); //"UPDATE"

            //----出力されるタグの属性値

            //----関数内デフォルト
            $strShowTable01TagId = $objListFormatter->getGeneValue("stdCurrentTable.tagIDonHTML",$refRetKeyExists);
            if( $strShowTable01TagId===null && $refRetKeyExists===false  ){
                $strShowTable01TagId = $defaultValueOnFx[0];
            }
            $strShowTable01WrapDivClass = $objListFormatter->getGeneValue("stdCurrentTable.wrapDivClass",$refRetKeyExists);
            if( $strShowTable01WrapDivClass===null && $refRetKeyExists===false  ){
                $strShowTable01WrapDivClass = $defaultValueOnFx[1];
            }
            $strShowTable02TagId = $objListFormatter->getGeneValue("stdSubtotalTable.tagIDonHTML",$refRetKeyExists);
            if( $strShowTable02TagId===null && $refRetKeyExists===false  ){
                $strShowTable02TagId = $defaultValueOnFx[2];
            }
            $strShowTable02WrapDivClass = $objListFormatter->getGeneValue("stdSubtotalTable.wrapDivClass",$refRetKeyExists);
            if( $strShowTable02WrapDivClass===null && $refRetKeyExists===false  ){
                $strShowTable02WrapDivClass = $defaultValueOnFx[3];
            }
            //関数内デフォルト----
            
            //出力されるTableタグの属性値----

            $optAllHidden=false;
            if(array_key_exists("optionAllHidden",$aryVariant)===true){
                $optAllHidden = $aryVariant['optionAllHidden'];
            }
            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_currentPrint");
            $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;
            //固有----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTiS_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }

            if( $strPrivilege === "1" ){
                // ----1はメンテナンス権限あり
                // 1はメンテナンス権限あり----
            }else if( $strPrivilege === "2" ){
                // ----2は参照のみなので更新・廃止ボタンを表示しない
                $aryObjColumn = $objTable->getColumns();
                $objColumnRUB = $aryObjColumn[$lcRequiredUpdateButtonColumnId];
                $objColumnRUB->getOutputType($strFormatterId)->setVisible(false);
                $objColumnRDF = $aryObjColumn[$lcRequiredDisuseFlagColumnId];
                $objColumnRDF->getOutputType($strFormatterId)->setVisible(false);
                // 2は参照のみなので更新・廃止ボタンを表示しない----
            }else{
                // ----0は権限がないので出力しない
                $intErrorType = 1;
                throw new Exception( '00010100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            $intWebLimit = null;
            if(isset($g['menu_web_limit'])){
                $intWebLimit = $g['menu_web_limit'];
            }

            $boolBinaryDistinctOnDTiS = $objListFormatter->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
            if( $boolBinaryDistinctOnDTiS===null && $refRetKeyExists===false ){
                $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
            }
            if(is_bool($boolBinaryDistinctOnDTiS)===false){
                $boolBinaryDistinctOnDTiS = false;
            }

            if(array_key_exists("directSQL",$aryVariant)===true){
                //----直で実行するSQLが指定された
                $sql2 = $aryVariant['directSQL']['sqlBody'];
                if(isset($aryVariant['directSQL']['bindBody'])===true){
                    $arrayFileterBody = $aryVariant['directSQL']['bindBody'];
                }else{
                    $arrayFileterBody = array();
                }
                //直で実行するSQLが指定された----
            }else{
                //----通常モード

                if(array_key_exists("filter_ctl_start_limit",$filterData)){
                    unset($filterData["filter_ctl_start_limit"]);
                }

                //----必須チェックなどを事前にしたい場合は、ここで差し替え
                if( $aryFunctionForOverride!==null ){
                     list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("printTableMain",$strFormatterId,"DTiSFilterCheckValid"),null);
                     unset($tmpBoolKeyExist);
                     if( is_callable($tmpObjFunction01ForOverride)===true ){
                         $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                     }
                     unset($tmpObjFunction01ForOverride);
                }
                //必須チェックなどを事前にしたい場合は、ここで差し替え----

                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                    if($arrayTmp[0]===false){
                        $intErrorType = $arrayTmp[1];
                        throw new Exception( '00010200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }

                //----バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）
                if( $objFunction01ForOverride===null ){
                    $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant);
                }
                else{
                    $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant);
                }

                if( $tmpAryRet[1]!==null ){
                    $intErrorType = $tmpAryRet[1];
                    $error_str = implode("", $tmpAryRet[2]);
                    unset($tmpAryRet);
                    throw new Exception( '00010300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($tmpAryRet);
                //バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）----

                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                    if($arrayTmp[0]===false){
                        $intErrorType = $arrayTmp[1];
                        throw new Exception( '00010400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }

                if( $boolBinaryDistinctOnDTiS===true ){
                    //----厳格な検出をする（あえて、各ページの開発者がしない限りは、行わない）
                    $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);
                    $boolFocusRet= dbSearchResultNormalize();
                    if($boolFocusRet === false){
                        $intErrorType = 500;
                        throw new Exception( '00010500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //厳格な検出をする（あえて、各ページの開発者がしない限りは、行わない）----
                }else{
                    //----デフォルト。通常は、検出時、大文字と小文字、カタカナと平仮名の区別なし

                    $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

                    //----DB面で、大文字小文字と全角半角を無視する設定
                    $boolFocusRet= dbSearchResultExpand();
                    if($boolFocusRet === false){
                        $intErrorType = 500;
                        throw new Exception( '00010600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //DB面で、大文字小文字と全角半角を無視する設定----

                    //デフォルト。通常は、検出時、大文字と小文字、カタカナと平仮名の区別なし----
                }

                $sql1 = generateSelectSql2(1, $objTable, $boolBinaryDistinctOnDTiS);
                $sql2 = generateSelectSql2(2, $objTable, $boolBinaryDistinctOnDTiS);

                //通常モード----
            }

            if( $aryFunctionForOverride!==null ){
                 list($tmpObjFunction02ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("printTableMain",$strFormatterId,"selectResultFetch"),null);
                 unset($tmpBoolKeyExist);
                 if( is_callable($tmpObjFunction02ForOverride)===true ){
                     $objFunction02ForOverride = $tmpObjFunction02ForOverride;
                 }
                 unset($tmpObjFunction02ForOverride);
            }

            if( $objFunction02ForOverride===null ){
                if( $sql1!="" ){
                    $row_counter = 0;
                    $retArray = singleSQLExecuteAgent($sql1, $arrayFileterBody, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $row = $objQuery->resultFetch();
                        $row_counter = $row['REC_CNT'];
                        unset($objQuery);
                    }
                    else{
                        $intErrorType = 500;
                        throw new Exception( '00010700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                if($mode=='1' && ($intWebLimit===null || $row_counter <= $intWebLimit)){
                    $row_counter = 0;
                    $retArray = singleSQLExecuteAgent($sql2, $arrayFileterBody, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        if($optAllHidden !==true){
                            //----TemplateTableへクエリの結果（行集合）を渡す
                            //
                            $intFetchCount=0;
                            $arrayTempVariantData=array();
                            //
                            while ( $row = $objQuery->resultFetch() ){
                                $intFetchCount+=1;
                                if($intWebLimit === null || $intFetchCount <= $intWebLimit){
                                    //----プリント上限まではデータを追加
                                    $objTable->addData($row, true, $arrayTempVariantData);
                                    //プリント上限まではデータを追加----
                                }
                            }
                            
                            unset($arrayTempVariantData);
                            
                            // ----レコード数を取得
                            $row_counter = $intFetchCount;
                            // レコード数を取得----
                            
                            //TemplateTableへクエリの結果（行集合）を渡す----
                        }else{
                            //----TemplateTableへクエリの結果（行集合）を渡さない
                            
                            $row_counter = 0;
                            
                            //TemplateTableへクエリの結果（行集合）を渡さない----
                        }
                        unset($objQuery);
                    }
                    else{
                        $intErrorType = 500;
                        throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
            }
            else{
                $tmpAryRet = $objFunction02ForOverride($objTable, $strFormatterId, $mode, $filterData, $aryVariant, $arySetting, $aryOverride);
                if( $tmpAryRet[1]!==null ){
                    $intErrorType = $tmpAryRet[1];
                    $error_str = implode("", $tmpAryRet[2]);
                    unset($tmpAryRet);
                    throw new Exception( '00010900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $row_counter = $tmpAryRet[0];
                unset($tmpAryRet);
            }

            if(array_key_exists("directSQL",$aryVariant)===true){
                //----直で実行するSQLが指定された
                //直で実行するSQLが指定された----
            }else{
                //----通常モード
                if( $boolBinaryDistinctOnDTiS===true ){
                }else{
                    $boolFocusRet= dbSearchResultNormalize();
                    if($boolFocusRet === false){
                        $intErrorType = 500;
                        throw new Exception( '00011000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                
                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                    if($arrayTmp[0]===false){
                        $intErrorType = $arrayTmp[1];
                        throw new Exception( '00011100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                //通常モード----
            }

            switch($mode){
                case 0 ://(常に)Web表を表示しない
                case 1 ://(行をオーバーしない限り)Web表を表示する
                    break;
                default:
                    $intErrorType = 500;
                    throw new Exception( '00011200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            $strOutputStr = "";
            $strOutputStr .= makePrintTableAreaHeadTag($objTable, $strFormatterId, $row_counter, $aryVariant, $arySetting);
            $strOutputStr .= makeTableTagFromPrintTableFormat(
                $objTable, $strFormatterId, $mode,  $row_counter, 
                $strShowTable01TagId, $strShowTable01WrapDivClass, 
                $boolBinaryDistinctOnDTiS, 
                $aryVariant, $arySetting
            );
            $strOutputStr .= makeTableTagFromPrintSubtotalTableFormat(
                $objTable, $strFormatterId, $mode,  $row_counter,
                $strShowTable02TagId, $strShowTable02WrapDivClass, 
                $aryVariant, $arySetting
            );
            $strOutputStr .= makeDownloadFormTag(
                $objTable, $strFormatterId, $filterData, $row_counter, 
                $aryVariant, $arySetting
            );
            $strOutputStr .= makePrintTableAreaTailTag($objTable, $strFormatterId, $row_counter, $aryVariant, $arySetting);

            //----メモリを確保するためにデータを解放
            $objTable->setData(array());
            //メモリを確保するためにデータを解放----
            $strOutputStr .= "<div class=\"hyouji_flag\" style=\"display:none;\"></div>";
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 1 : $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-121"); break;//----権限なし
                case 2 : break;//----バリデーションエラー
                default: $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-122"); break;//----一般エラー
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
            $strOutputStr = nl2br($error_str);
        }
        unset($aryVariant["TCA_PRESERVED"]["TCA_ACTION"]);
        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $varRet;
    }

    function makeDownloadFormTag($objTable, $strFormatterId, $filterData, $row_counter, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        //
        // return値
        $strOutputStr = "";
        //
        $strLimitRowWarningMsgBody="";
        $refRetKeyExists = null;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];

        $strCommonElementFilterData = makeHiddenInputTagFromFilterData($filterData);
        $strCommonElementEtcHiddenData = makeHiddenInputTagFromGroupPrefixData("commonHiddenSend",$aryVariant);

        //----ロードテーブルからの設定を取得
        $flag_ExcelHidden = $objListFormatter->getGeneValue("linkExcelHidden",$refRetKeyExists);
        if( $flag_ExcelHidden===null && $refRetKeyExists===false  ){
            $flag_ExcelHidden = $objTable->getGeneObject("linkExcelHidden",$refRetKeyExists);
        }
        $flag_CSVShow = $objListFormatter->getGeneValue("linkCSVFormShow",$refRetKeyExists);
        if( $flag_CSVShow===null && $refRetKeyExists===false  ){
            $flag_CSVShow = $objTable->getGeneObject("linkCSVFormShow",$refRetKeyExists);
        }
        //ロードテーブルからの設定を取得----
        
        //----ここからエクセル出力するためのタグ追加
        if( $flag_ExcelHidden!==true ){
            $btnXlsDlFlag = "";
            $intXlsLimit = isset($g['menu_xls_limit'])?$g['menu_xls_limit']:null;
            if( $intXlsLimit !== null && $intXlsLimit < $row_counter ){
                //----エクセル出力の最大行を超えていた場合
                $btnXlsDlFlag = "disabled=true ";
                if( 0 < $intXlsLimit ){
                    $strLimitRowWarningMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-341",array($row_counter, $intXlsLimit));
                }

                if( $flag_CSVShow!==false ){
                    //----無条件でCSVを隠す、という設定ではない
                    $flag_CSVShow = true;
                    //無条件でCSVを隠す、という設定ではない----
                }
                //エクセル出力の最大行を超えていた場合----
            }

            $strLinkExcelFormatterId = $objListFormatter->getGeneValue("linkExcelFormatterID",$refRetKeyExists);
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
            }
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId="excel";
            }
            if( $strLinkExcelFormatterId === null){
                //----エクセル用のフォーマットIDがnullだった
                //エクセル用のフォーマットIDがnullだった----
            }else{
                $strOutputStr .= 
<<< EOD
            <form name="reqExcelDL" action="{$g['scheme_n_authority']}/default/menu/03_create_excel.php?no={$g['page_dir']}" method="POST">
EOD;
            $strOutputStr .= $strCommonElementFilterData;

            $strHiddenInputTag = makeHiddenInputTagFromGroupPrefixData("excelHiddenSend", $aryVariant);

            $strOutputStr .= 
<<< EOD
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-342")}" {$btnXlsDlFlag}>
                <input type="hidden" name="filteroutputfiletype" value="excel" >
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkExcelFormatterId}">
                {$strCommonElementEtcHiddenData}{$strHiddenInputTag}
            </form>
            {$strLimitRowWarningMsgBody}
            <br>
EOD;
            }
        }
        //ここまでエクセル出力するためのタグ追加----

        //----ここからCSV出力するためのタグ追加
        
        if($flag_CSVShow === true){
            //----CSV系の常時ダウンロードが、有効の場合、または、エクセルダウンロード上限数以上の場合

            $refRetKeyExists = false;
            $strLinkCSVFormatterId = $objListFormatter->getGeneValue("linkCSVFormatterID",$refRetKeyExists);
            if( $strLinkCSVFormatterId===null && $refRetKeyExists===false ){
                $strLinkCSVFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
            }
            if( $strLinkCSVFormatterId===null && $refRetKeyExists===false ){
                $strLinkCSVFormatterId = "csv";
            }
            if( $strLinkCSVFormatterId===null ){
                //----CSV用のフォーマットIDがnullだった
                //CSV用のフォーマットIDがnullだった----
            }else{
                $strOutputStr .= 
<<< EOD
            <form style="display:inline" name="reqCsvDL" action="{$g['scheme_n_authority']}/default/menu/03_create_excel.php?no={$g['page_dir']}" method="POST">
EOD;
                $strOutputStr .= $strCommonElementFilterData;

                $strOutputFileType = $objTable->getFormatter($strLinkCSVFormatterId)->getGeneValue("outputFileType",$refRetKeyExists);
                if($strOutputFileType == "SafeCSV"){
                    $fileTypeNameBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-343");
                    $strHiddenInputTag = makeHiddenInputTagFromGroupPrefixData("safecsvHiddenSend", $aryVariant);
                }
                else{
                    $fileTypeNameBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-344");
                    $strHiddenInputTag = makeHiddenInputTagFromGroupPrefixData("csvHiddenSend", $aryVariant);
                }

                $strOutputStr .= 
<<< EOD
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-345",$fileTypeNameBody)}" >
                <input type="hidden" name="filteroutputfiletype" value="csv" >
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkCSVFormatterId}">
                {$strCommonElementEtcHiddenData}{$strHiddenInputTag}
            </form>
            <br>
EOD;

                if($strOutputFileType == "SafeCSV"){
                    $strOutputStr .= 
<<<EOD
            <form style="display:inline" name="reqToolDL" action="{$g['scheme_n_authority']}/webdbcore/editorBaker.zip">
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-346")}" >
            </form>
            <br>
            <form style="display:inline" name="reqExcelDL" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-347")}" >
                <input type="hidden" name="filteroutputfiletype" value="excel">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkCSVFormatterId}">
                <input type="hidden" name="requestuserclass" value="visitor">
            </form>
            <br>
            <br>
EOD;
                }
            }
            //CSV系の常時ダウンロードが、有効の場合、または、エクセルダウンロード上限数以上の場合----
        }
        //
        if($row_counter == 0){
            $strOutputStr = "";
        }
        
        //ここまでCSV出力するためのタグ追加----
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $strOutputStr;
    }

    function makeTableTagFromPrintTableFormat($objTable, $strFormatterId, $mode, $row_counter, $strShowTable01TagId, $strShowTable01WrapDivClass, $boolBinaryDistinctOnDTiS, &$aryVariant, &$arySetting){
        global $g;
        // ----ローカル変数宣言        
        $intControlDebugLevel01=250;
        //
        // return値
        $strTableBody = "";
        $strCountBody = "";

        $refRetKeyExists = null;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        //
        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];
        //
        //----ロードテーブルからの設定を取得
        $flag_printTableHidden = $objListFormatter->getGeneValue("printTableHidden",$refRetKeyExists);
        //ロードテーブルからの設定を取得----
        //
        $intWebLimit = isset($g['menu_web_limit'])?$g['menu_web_limit']:null;
        //
        if( $mode == '0' || ($mode == '1' && $intWebLimit !== null && $intWebLimit < $row_counter) ){
            $flag_printTableHidden = true;
        }
        //
        if($flag_printTableHidden !== true){
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
            //
            $strTableBody = 
<<< EOD
            <div class="{$strShowTable01WrapDivClass}">
EOD;

            $objListFormatter->setGeneValue("tempBinaryDistinctOnDTiS", $boolBinaryDistinctOnDTiS);
            // ----[1]検索結果本体テーブルのHTML出力
            $strTableBody .= $objTable->getPrintFormat($strFormatterId, $strShowTable01TagId);
            // [1]検索結果本体テーブルのHTML出力----

            $strTableBody .= 
<<< EOD
            </div>\n
EOD;
            if($row_counter == 0){
                $strTableBody = "";
            }
            
            $objListFormatter->setGeneValue("tempBinaryDistinctOnDTiS", null, true);
            
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
        }
        else{
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
        }
        $msgMatchRowCountHead = $objListFormatter->getGeneValue("msgMatchRowCountHead",$refRetKeyExists);
        $msgMatchRowCountTail = $objListFormatter->getGeneValue("msgMatchRowCountTail",$refRetKeyExists);

        if($msgMatchRowCountHead == ""){
            $msgMatchRowCountHead = "{$g['objMTS']->getSomeMessage("ITAWDCH-STD-348")}: ";
        }
        if($msgMatchRowCountTail == ""){
            $msgMatchRowCountTail = "\n";
        }

        $strCountBody .= 
<<< EOD
            {$msgMatchRowCountHead}{$row_counter}{$msgMatchRowCountTail}<br>
EOD;

        if($row_counter == 0){
            $strCountBody = "";
        }

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $strTableBody.$strCountBody;
    }

    function makeTableTagFromPrintSubtotalTableFormat($objTable, $strFormatterId, $mode, $row_counter, $strShowTable02TagId, $strShowTable02WrapDivClass, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言        
        $intControlDebugLevel01=250;
        //
        // return値
        $strOutputStr = "";
        $refRetKeyExists = false;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        //
        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];
        
        $strPrintSubtotalFormatterId = $objListFormatter->getGeneValue("printSubtotalFormatterID",$refRetKeyExists);
        if( $strPrintSubtotalFormatterId===null && $refRetKeyExists===false ){
            $strPrintSubtotalFormatterId = "print_subtotal_table";
        }
        
        $objSubtotalFormatter = $objTable->getFormatter($strPrintSubtotalFormatterId);
        
        if( $objSubtotalFormatter === null ){
            //----フォーマッタが見つからなかった
            $flag_printTableHidden = true;
            //フォーマッタが見つからなかった----
        }
        else{
            //----ロードテーブルからの設定を取得
            $flag_printTableHidden = $objListFormatter->getGeneValue("printSubtotalTableHidden",$refRetKeyExists);
            //ロードテーブルからの設定を取得----
        }
        
        $intWebLimit = isset($g['menu_web_limit'])?$g['menu_web_limit']:null;
        
        if( $mode == '0' || ($mode == '1' && $intWebLimit !== null && $intWebLimit < $row_counter) ){
            $flag_printTableHidden = true;
        }
        
        if($flag_printTableHidden !== true){
            //----ここからサブトータルのタグ
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
            $strOutputStr = 
<<< EOD
            <br />
            <div class="{$strShowTable02WrapDivClass}">
EOD;
            
            // ----[2]検索結果のサブトータルテーブルのHTML出力
            $subtotal = $objTable->getPrintFormat($strPrintSubtotalFormatterId, $strShowTable02TagId);
            // [2]検索結果のサブトータルテーブルのHTML出力----
            
            $strOutputStr .= $subtotal;
            $strOutputStr .=
<<< EOD
            </div>
EOD;
            
            if($row_counter == 0){
                $strOutputStr = "";
            }
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
            //ここまでサブトータルのタグ----
        }
        else{
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $strOutputStr;
    }

    function makePrintTableAreaHeadTag($objTable, $strFormatterId, $row_counter, &$aryVariant, &$arySetting){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=50;
        
        // return値
        $strOutputStr = "";
        // ローカル変数宣言----
        
        $strOutputStr .= 
<<<EOD
EOD;
        //
        return $strOutputStr;
        //
    }

    function makePrintTableAreaTailTag($objTable, $strFormatterId, $row_counter, &$aryVariant, &$arySetting){
        global $g;
        // ----ローカル変数宣言      
        $intControlDebugLevel01=50;
        
        // return値
        $strOutputStr = "";
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];

        if($row_counter == 0){
            // ----0件の場合はTABLEではなくメッセージを返却するようハンドリング
            $htmlPrintTableAreaTailCommon = "<br>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-349")}<br>";
            if(array_key_exists("tail_scene_rec_n0_common", $arySetting)===true){
                $htmlPrintTableAreaTailCommon = $arySetting['tail_scene_rec_n0_common'];
            }
            
            if(array_key_exists("privilege", $g)===true){
                $htmlPrintTableAreaTailPriSome="";
                if($g['privilege'] === "1"){
                    if(array_key_exists("tail_scene_rec_n0_prv1", $arySetting)===true){
                        $htmlPrintTableAreaTailPriSome=$arySetting['tail_scene_rec_n0_prv1'];
                    }
                    else{
                        $htmlPrintTableAreaTailPriSome = "{$g['objMTS']->getSomeMessage("ITAWDCH-STD-350")}<br>";
                    }
                }
                else if($g['privilege'] === "2"){
                    if(array_key_exists("tail_scene_rec_n0_prv2", $arySetting)===true){
                        $htmlPrintTableAreaTailPriSome=$arySetting['tail_scene_rec_n0_prv2'];
                    }
                    else{
                        $htmlPrintTableAreaTailPriSome="";
                    }
                }
            }
            // 0件の場合はTABLEではなくメッセージを返却するようハンドリング----
        }
        else{
            $htmlPrintTableAreaTailCommon="";
            if(array_key_exists("tail_scene_rec_nx_common", $arySetting)===true){
                $htmlPrintTableAreaTailCommon = $arySetting['tail_scene_rec_nx_common'];
            }
            //
            if(array_key_exists("privilege", $g)===true){
                $htmlPrintTableAreaTailPriSome="";
                if($g['privilege'] === "1"){
                    if(array_key_exists('tail_scene_rec_nx_prv1', $arySetting)===true){
                        $htmlPrintTableAreaTailPriSome=$arySetting['tail_scene_rec_nx_prv1'];
                    }
                    else{
                        $htmlPrintTableAreaTailPriSome="";
                    }
                }
                else if($g['privilege'] === "2"){
                    if(array_key_exists("tail_scene_rec_nx_prv2", $arySetting)===true){
                        $htmlPrintTableAreaTailPriSome=$arySetting['tail_scene_rec_nx_prv2'];
                    }
                    else{
                        $htmlPrintTableAreaTailPriSome="";
                    }
                }
            }
        }
        //
        $strOutputStr = 
<<<EOD
                {$htmlPrintTableAreaTailCommon}
                {$htmlPrintTableAreaTailPriSome}
EOD;
        //
        return $strOutputStr;
        //
    }

    function makeHiddenInputTagFromFilterData($filterData){
        //----ここからエクセル・CSV共通のタグ要素作成
        $strCommonElementFilterData="";
        foreach($filterData as $key =>$value){
            foreach($value as $key2 => $value2){
                //----change start 2018/09/21 特殊文字を無害化する処理を追加
                $value2 = htmlspecialchars($value2, ENT_QUOTES, 'UTF-8');
                //change end 2018/09/21 特殊文字を無害化する処理を追加----
                $strCommonElementFilterData .= 
<<< EOD
            <input style="display:none;" name="filter_data[{$key}][{$key2}]" value="{$value2}" />
EOD;
            }
        }
        //ここまでエクセル・CSV共通のタグ要素作成----
        return $strCommonElementFilterData;
    }

    function makeHiddenInputTagFromGroupPrefixData($strSearchKeyPrefix,&$aryVariant=array()){
        //
        $intHiddenSend = 1;
        $strlHiddenInputTag = "";
        do
        {
            //----1から99まで、「excelHiddenSend(XX)の配列キーを探す」
            $excelHiddenSendKey = $strSearchKeyPrefix.sprintf("%02d", $intHiddenSend);
            //1から99まで、「excelHiddenSend(XX)の配列キーを探す」----

            if(array_key_exists($excelHiddenSendKey,$aryVariant)){
                $strlHiddenInputTag .= 
<<< EOD
                    <input type="hidden" name="{$excelHiddenSendKey}" value="{$aryVariant[$excelHiddenSendKey]}" >
EOD;
                $intHiddenSend += 1;
            }
            else{
                $intHiddenSend = 0;
                break;
            }
        }while(0 < $intHiddenSend);
        
        return $strlHiddenInputTag;
        
    }

?>
