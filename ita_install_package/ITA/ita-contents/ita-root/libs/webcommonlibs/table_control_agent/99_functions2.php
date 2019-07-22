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

    //mainTable:::識別子に、このソース内では、DB区切り文字を追加してはならない。

    //----00-開発者領域画面そのほかシステム用

    function loadTableFunctionAdd($objFunction,$srcFilePath){
        global $g;
        $strLrWebRootToThisPageDir = str_replace( "_loadTable.php", "", basename($srcFilePath));
        if( isset($g['aryTCABuildFunction']) === false ){
            $g['aryTCABuildFunction'] = array();
        }
        $g['aryTCABuildFunction'][$strLrWebRootToThisPageDir] = $objFunction;
    }

    function loadTable($registeredKey="",&$aryVariant=array(), &$arySetting=array()){
        global $g;
        $intControlDebugLevel01=200;
        
        $intErrorType = null;
        
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $retObject = null;
        try{
            if( $registeredKey !== null && is_string($registeredKey) === false ){
                $intErrorType = 501;
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                $intErrorType = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( is_array($g) !== true ){
                $intErrorType = 501;
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $registeredKey=="" ){
                if( array_key_exists('page_dir',$g)===true ){
                    if( is_string($g['page_dir']) ){
                        $registeredKey = $g['page_dir'];
                    }
                }
            }
            if( array_key_exists('aryTCABuildFunction',$g)===true ){
                $aryFunctions = $g['aryTCABuildFunction'];
                if( array_key_exists($registeredKey,$aryFunctions) === true){
                    $tgtFunction = $aryFunctions[$registeredKey];
                    if( is_callable($tgtFunction) ){
                        //----関数が定義されていた場合、無名関数の実行
                        $retObject = $tgtFunction($aryVariant,$arySetting);
                        //関数が定義されていた場合、無名関数の実行----
                    }
                }
            }
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5005",array($tmpErrMsgBody,$intErrorType)));
            webRequestForceQuitFromEveryWhere(500,90110101);
            exit();
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retObject;
    }

    function checkOverrideValue($strCallerName, $aryDefault, $aryOverride){
        $aryResult = $aryDefault;
        if( is_array($aryDefault)===true && is_array($aryOverride) ){
            if( count($aryDefault) == count($aryOverride) ){
                $aryKeysOfDefault  = array_keys($aryDefault);
                $aryKeysOfOverride = array_keys($aryOverride);
                $aryResult = $aryOverride;
                for($fnv1=0; $fnv1<count($aryKeysOfDefault); $fnv1++){
                    $focusKeyOfDefault = $aryKeysOfDefault[$fnv1];
                    $focusKeyOfOverride = $aryKeysOfOverride[$fnv1];
                    $focusDefault = $aryDefault[$focusKeyOfDefault];
                    
                    $focusOverride = $aryOverride[$focusKeyOfOverride];
                    if( gettype($focusDefault)!==gettype($focusOverride) ){
                        $aryResult = $aryDefault;
                        break;
                    }
                }
            }
        }
        return $aryResult;
    }

    function checkCommonSettingVariants($strCallerName, $objTable, $aryVariant, $arySetting, $strRequireListFormatterClassName=null, $strCheckListFormatterId=null, $intErrStartCount=0, $intErrAddScale=100, $strErrFormat="%08d"){
        global $g;
        $intControlDebugLevel01=200;
        
        $intErrorType = null;
        
        $retArray = array();
        $strFxName = __FUNCTION__;
        $retArray[0] = true;
        try{
            $intErrStartCount += $intErrAddScale;
            if( is_string($strCallerName) !== true ){
                //----文字列型ではない
                $intErrorType = 501;
                $strCallerName = "{$strFxName}[UNKNOWN]";
                throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 文字列型ではない----
            }
            else{
                $strCallerName = "{$strFxName}[{$strCallerName}]";
            }
            $intErrStartCount += $intErrAddScale;
            //----システムエラーが発生していた場合
            if(array_key_exists("system_error",$g)===true){
                $intErrorType = 901;
                throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //システムエラーが発生していた場合   
            
            $intErrStartCount += $intErrAddScale;
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }
            $intErrStartCount += $intErrAddScale;
            if( gettype($objTable)!="object" ){
                // ----オブジェクトではない
                $intErrorType = 501;
                throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // オブジェクトではない----
            }
            $intErrStartCount += $intErrAddScale;
            if( is_a($objTable, "TableControlAgent") !== true ){
                $intErrorType = 501;
                throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $strRequireListFormatterClassName!== null ){
                $intErrStartCount += $intErrAddScale;
                if( is_string($strRequireListFormatterClassName) !== true ){
                    //----文字列型ではない
                    $intErrorType = 501;
                    throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    // 文字列型ではない----
                }
                $intErrStartCount += $intErrAddScale;
                if( is_string($strCheckListFormatterId) !== true ){
                    //----文字列型ではない
                    $intErrorType = 501;
                    throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    // 文字列型ではない----
                }
                $strCheckFormatterId = $strCheckListFormatterId;
                if( array_key_exists($strCallerName,$arySetting) ){
                    if( is_array($arySetting[$strCallerName]) ){
                        if( array_key_exists("FORMATTER_ID",$arySetting[$strCallerName]) === true ){
                            //----loadTaユーザが設定していた場合
                            $strCheckFormatterId = $arySetting[$strCallerName]['FORMATTER_ID'];
                            //----
                        }
                    }
                }
                $intErrStartCount += $intErrAddScale;
                if( is_string($strCheckFormatterId) !== true ){
                    //----文字列型ではない
                    $intErrorType = 501;
                    throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    // 文字列型ではない----
                }
                $intErrStartCount += $intErrAddScale;
                $objListFormatter = $objTable->getFormatter($strCheckFormatterId);
                if( is_a($objListFormatter, $strRequireListFormatterClassName) !== true ){
                    // ----必要としているクラスではない
                    $intErrorType = 501;
                    throw new Exception( sprintf($strErrFormat, $intErrStartCount).'-([FUNCTION]' . $strCallerName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    // 必要としているクラスではない----
                }
                $retArray[1] = $strCheckFormatterId;
                $retArray[2] = $objListFormatter;
            }
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5005",array($tmpErrMsgBody,$intErrorType)));
            webRequestForceQuitFromEveryWhere(500,90110102);
            exit();
        }
        return $retArray;
    }

    function getTCAConfig($registeredKey=null,&$aryVariant=array(), &$arySetting=array()){
        //----web_parts_for_template_01_browse.phpから呼び出される。
        //----loadTableで$arySettingを設定＆FIXするため、試験などの事情がない限り、この関数を呼び出す場合に引数を指定しないこと。
        global $g;
        $intControlDebugLevel01=200;
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $retArrayBody = array();
        
        $pageType = "";
        $registerFilterCommand = "";
        
        //----loadTableで設定された$arySetting内容は、上書き削除しないポリシーとすること。
        $objTable1 = loadTable($registeredKey, $aryVariant, $arySetting);
        //loadTableで設定された$arySetting内容は、上書き削除しないポリシーとすること。----
        
        checkCommonSettingVariants($strFxName, $objTable1, $aryVariant, $arySetting);
        
        $arrayWebSetting = array();
        if( is_array($objTable1->getGeneObject("webSetting",$refRetKeyExists))===true ){
            $arrayWebSetting = $objTable1->getGeneObject("webSetting",$refRetKeyExists);
        }
        
        //----表示権限
        $privilege = "";
        if( array_key_exists("privilege", $g) ){
            $privilege = $g['privilege'];
        }
        //表示権限----
        
        //----JS-MSGテンプレートのリスト作成
        $aryImportFilePath = array();
        $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath("ITAWDCC","STD","_js");
        if( array_key_exists('aryJsMsgAdd',$arrayWebSetting) === true ){
            foreach($arrayWebSetting['aryJsMsgAdd'] as $tmpValue){
                if( is_string($tmpValue)===true ){
                    $tmpArray = explode("-",$tmpValue);
                    if( count($tmpArray) == 3 ){
                        $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath($tmpArray[0],$tmpArray[1],$tmpArray[2]);
                    }
                }
            }
        }
        $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$g['objMTS']);
        //JS-MSGテンプレートのリスト作成----
        
        list($strPageInfo,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arrayWebSetting,array('page_info'),null);
        list($intTableWidth,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arrayWebSetting,array('stdTableWidth'),null);
        list($intTableHeight,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arrayWebSetting,array('stdTableHeight'),null);

        $strPageInfo = (is_string($strPageInfo)===true)?$strPageInfo:"";        
        $intTableWidth = (is_int($intTableWidth)===true)?$intTableWidth:1058;  
        $intTableHeight = (is_int($intTableHeight)===true)?$intTableHeight:600;  
        
        // 開発者領域
        $tmpRetArray = getAreaForAdmin($objTable1,$aryVariant,$arySetting);
        $strDeveloperArea = $tmpRetArray[1];
        
        // CURフィルタエリア
        $tmpRetArray = getFilterCommandArea($objTable1,$aryVariant,$arySetting,"filter_table","Filter1Tbl","FilterConditionTableFormatter");
        $strHtmlFilter1Commnad = $tmpRetArray[1];
        
        //----登録対象絞り込み用フィルター
        if( is_a($objTable1, "TemplateTableForReview") === true ){
            // ----レヴュー用テンプレートの場合
            $pageType = $objTable1->getPageType();
            $aryTempVariant = array('pageType'=>'view');
            $objTable2 = loadTable($registeredKey,$aryTempVariant);
            $tmpRetArray = getRegisterFilterCommandArea($objTable2,$aryVariant,$arySetting,"register_table","Filter2Tbl","RegisterTableFormatter");
            $registerFilterCommand = $tmpRetArray[1];
            
            if( $pageType=="confirm" ){
                $tmpRetArray = checkCommonSettingVariants($strFxName, $objTable1, $aryVariant, $arySetting, "QMFileSendAreaFormatter", "all_dump_table");
                $checkFormatterId = $tmpRetArray[1];
                $objListFormatter = $tmpRetArray[2];
                $objListFormatter->setGeneValue("noRecordExcelHidden", true);
            }
            
            // レヴュー用テンプレートの場合----
        }
        //登録対象絞り込み用フィルター----
        $tmpRetArray = getRegisterEditCommandArea($objTable1,$aryVariant,$arySetting);
        $tmpBoolShowRegisterArea = $tmpRetArray[0];
        
        // DL/ULエリア
        $tmpRetArray = getInitPartOfEditByFile($objTable1,$aryVariant,$arySetting,"all_dump_table");
        $strHtmlFileEditCommnad = $tmpRetArray[1];
        
        // JNLフィルタエリア
        $tmpRetArray = getJnlSearchFilterCommandArea($objTable1,$aryVariant,$arySetting);
        $strHtmlJnlFilterCommnad = $tmpRetArray[1];
        
        // 権限に応じて、登録関連エリアを表示するかを切り替え
        $boolShowRegisterArea = false;        
        if( $privilege==='1' ){
            $boolShowRegisterArea = true;
            if( $tmpBoolShowRegisterArea === false){
                $boolShowRegisterArea = false;
            }
        }
        
        $retArrayBody['objTable'] = $objTable1;
        $retArrayBody['pageType'] = $pageType;
        $retArrayBody['privilege'] = $privilege;
        $retArrayBody['PageInfoArea'] = $strPageInfo;
        $retArrayBody['JscriptTmpl']  = $strJscriptTemplateBody;
        $retArrayBody['DeveloperArea']  = $strDeveloperArea;
        $retArrayBody['FilterCmdArea'] = $strHtmlFilter1Commnad;
        $retArrayBody['RegisterFilterArea'] = $registerFilterCommand;
        $retArrayBody['RegisterAreaShow'] = $boolShowRegisterArea;
        $retArrayBody['QMFileAreaCmd'] = $strHtmlFileEditCommnad;
        $retArrayBody['JnlSearchFilterCmdArea'] = $strHtmlJnlFilterCommnad;
        
        $varWebRowLimit = is_null($g['menu_web_limit'])?"":$g['menu_web_limit'];
        $varWebRowConfirm = is_null($g['menu_web_confirm'])?"":$g['menu_web_confirm'];
        
        // Web上の表示最大行数
        $retArrayBody['WebPrintRowLimit'] = $varWebRowLimit;
        // Web上に表示するかどうか確認する行数
        $retArrayBody['WebPrintRowConfirm'] = $varWebRowConfirm;
        
        $retArrayBody['WebStdTableWidth'] = $intTableWidth;
        $retArrayBody['WebStdTableHeight'] = $intTableHeight;
        
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArrayBody;
    }

    function getAreaForAdmin($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="all_dump_table"){
        global $g;
        $intControlDebugLevel01=200;
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        $retArray = array();   
        $dlcHtmlBody = "";

        $strTempValue = "";

        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "QMFileSendAreaFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];

        $boolShowFormatterArea = true;
        if( $objListFormatter->getGeneValue("areaForAdminHidden",$refRetKeyExists)===true ){
            $boolShowFormatterArea = false;
        }

        if( 0 < $g['dev_log_developer'] ){
            //----開発者権限がある場合
            $strTempValue = "forDeveloper";

            $refRetKeyExists = false;
            $strLinkExcelFormatterId = $objListFormatter->getGeneValue("linkExcelFormatterID",$refRetKeyExists);
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
            }
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId = "excel";
            }
            
            $dlcHtmlBody .= 
<<<EOD
            <form name="reqExcelDL" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-301")}" >
                <input type="hidden" name="filteroutputfiletype" value="excel">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkExcelFormatterId}">
                <input type="hidden" name="requestuserclass" value="{$strTempValue}">
            </form>
            <br />
EOD;
            $strPageInfoBody = "";

            $arrayWebSetting = array();
            if( is_array($objListFormatter->getGeneValue("webSettingForDeveloper",$refRetKeyExists))===true ){
                $arrayWebSetting = $objListFormatter->getGeneValue("webSettingForDeveloper",$refRetKeyExists);
                
                if( array_key_exists('(string)page_info',$arrayWebSetting)===true ){
                    
                    if( is_string($arrayWebSetting['(string)page_info'])===true ){
                        $strPageInfoBody = $arrayWebSetting['(string)page_info'];
                    }
                }
            }

            $dlcHtmlBody .= 
<<<EOD

    <!-------------------------------- 開発者領域 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>
                    <div onClick=location.href="javascript:show('Debug01Midashi','Debug01Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-302")}</div>
                </td>
                <td>
                    <div id="Debug01Midashi" align="right">
                        <input type="button" value="△{$g['objMTS']->getSomeMessage("ITAWDCH-STD-303")}" class="showbutton" onClick=location.href="javascript:show('Debug01Midashi','Debug01Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Debug01Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="Debug01_alert_area" class="alert_area" style="display:none" ></div>
            <div id="Debug01_area" class="table_area" ></div>
            {$strPageInfoBody}
        </div>
    </div>
    <!-------------------------------- 開発者領域 -------------------------------->

EOD;
            //開発者権限がある場合----
        }
        $retArray[0] = $boolShowFormatterArea;
        $retArray[1] = $dlcHtmlBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    
    //----01-フィルタ条件設定画面系部品
    function getFilterCommandArea($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="filter_table", $strControlTargetFilter="Filter1Tbl"){
        return getFilterCommandAreaGeneric($objTable, $aryVariant, $arySetting, $strFormatterId, $strControlTargetFilter, "FilterConditionTableFormatter");
    }
    
    function getFilterCommandAreaGeneric($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="filter_table", $strControlTargetFilter="Filter1Tbl", $strRequiredListFormatterClass="FilterConditionTableFormatter"){
        global $g;
        $intControlDebugLevel01=200;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        $retArray = array();   
        $dlcHtmlBody = "";        

        $valDisplay = "";
        $checkBoxChecked = "";
        $strJsEventNamePrefix = "";
        
        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, $strRequiredListFormatterClass, $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];
        
        $boolShowFormatterArea = true;
        if( $objListFormatter->getGeneValue("hidden",$refRetKeyExists)===true ){
            $boolShowFormatterArea = false;
        }
        
        //----初期のコントロールの値
        if("1" === $g['menu_autofilter']){
            $checkBoxChecked="checked=\"checked\"";
        }
        else{
            $checkBoxChecked="";
        }
        //初期のコントロールの値----
        
        //----ユーザーに初期コントロールの値を変更されるためのタグを見せるか？
        $flag_autoSearchCtl = $objListFormatter->getGeneValue("AutoSearchUserControl",$refRetKeyExists);
        if( $flag_autoSearchCtl===null && $refRetKeyExists===false ){
            $flag_autoSearchCtl = $objTable->getGeneObject("AutoSearchUserControl",$refRetKeyExists);
        }
        if($flag_autoSearchCtl === false){
            $valDisplay="none";
        }else{
            $valDisplay="block";
        }
        //ユーザーに初期コントロールの値を変更されるためのタグを見せるか？----
        
        if($objTable->getJsEventNamePrefix()===true){
            //----１ブラウズ、マルチフィルター対応バージョン
            if( is_string($strControlTargetFilter)===false ){
                $strControlTargetFilter = "Filter1Tbl";
            }
            $strJsEventNamePrefix = $strControlTargetFilter."_";
            //１ブラウズ、マルチフィルター対応バージョン----
        }
        
        $dlcHtmlBody = 
<<<EOD
            <input type="button" name="display_list_btn" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-310")}" onclick="javascript:{$strJsEventNamePrefix}search_async('orderFromFilterCmdBtn');">
            <input type="button" name="filter_clear_btn" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-311")}" onclick="javascript:{$strJsEventNamePrefix}reset_filter();">
            <div style="display:{$valDisplay}">
                <input type="checkbox" class="filter_ctl_start_limit" name="filter_ctl_start_limit" value="on" {$checkBoxChecked}>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-312")}
            </div>
EOD;
        $retArray[0] = $boolShowFormatterArea;
        $retArray[1] = $dlcHtmlBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    //01-フィルタ条件設定画面系部品----

    //----06-登録領域系部品
    function getRegisterFilterCommandArea($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="register_table", $strControlTargetFilter="Filter2Tbl"){
        return getFilterCommandAreaGeneric($objTable, $aryVariant, $arySetting, $strFormatterId, $strControlTargetFilter, "RegisterTableFormatter");

    }
    
    function getRegisterEditCommandArea($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="register_table"){
        global $g;
        $intControlDebugLevel01=200;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        $retArray = array();
        $dlcHtmlBody = "";

        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "RegisterTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];
        
        $boolShowFormatterArea = true;
        if( $objListFormatter->getGeneValue("hidden",$refRetKeyExists)===true ){
            $boolShowFormatterArea = false;
        }
        
        $retArray[0] = $boolShowFormatterArea;
        $retArray[1] = $dlcHtmlBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    
    //06-登録領域系部品----

    //----07-全件ダウンロード領域系部品
    function getInitPartOfEditByFile($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="all_dump_table"){
        global $g;
        $intControlDebugLevel01=200;

        $intErrorType = null;

        $objFunction01ForOverride = null;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        $retArray = array();
        $dlcHtmlBody = "";

        $strLimitRowWarningMsgBody="";

        try{
            $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "QMFileSendAreaFormatter", $strFormatterId);
            $checkFormatterId = $retArray[1];
            $objListFormatter = $retArray[2];

            $boolShowFormatterArea = true;
            if( $objListFormatter->getGeneValue("hidden")===true ){
                $boolShowFormatterArea = false;
            }

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);

            $refRetKeyExists = false;
            $strLinkExcelFormatterId = $objListFormatter->getGeneValue("linkExcelFormatterID",$refRetKeyExists);
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
            }
            if( $strLinkExcelFormatterId===null && $refRetKeyExists===false ){
                $strLinkExcelFormatterId = "excel";
            }

            $refRetKeyExists = false;
            $strLinkCSVFormatterId = $objListFormatter->getGeneValue("linkCSVFormatterID",$refRetKeyExists);
            if( $strLinkCSVFormatterId===null && $refRetKeyExists===false ){
                $strLinkCSVFormatterId = $objTable->getGeneObject("linkCSVFormatterID",$refRetKeyExists);
            }
            if( $strLinkCSVFormatterId===null && $refRetKeyExists===false ){
                $strLinkCSVFormatterId = "csv";
            }

            //----必須チェックなどを事前にしたい場合は、ここで差し替え
            if( $aryFunctionForOverride!==null ){
                list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("getInitPartOfEditByFile",$strFormatterId,"countTableRowLength"),null);
                unset($tmpBoolKeyExist);
                if( is_callable($tmpObjFunction01ForOverride)===true ){
                    $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                }
                unset($tmpObjFunction01ForOverride);
            }
            //必須チェックなどを事前にしたい場合は、ここで差し替え----

            if( $objFunction01ForOverride===null ){
                $tmpAryRet = countTableRowLength($objTable, $aryVariant, $arySetting, $strFormatterId);
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($objTable, $aryVariant, $arySetting, $strFormatterId);
            }

            if( $tmpAryRet[1]!==null ){
                $intErrorType = $tmpAryRet[1];
                $error_str = implode("", $tmpAryRet[2]);
                unset($tmpAryRet);
                throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $rowLength = $tmpAryRet[0];
            unset($tmpAryRet);

            $dlcHtmlBody =
<<<EOD
    <div id="AllDumpNakami" style="display:block" class="text">
        <div style="margin-left:10px">
EOD;

            //----ロードテーブルからの設定を取得
            $flag_ExcelHidden = $objListFormatter->getGeneValue("linkExcelHidden",$refRetKeyExists);
            if( $flag_ExcelHidden===null && $refRetKeyExists===false ){
                $flag_ExcelHidden = $objTable->getGeneObject("linkExcelHidden",$refRetKeyExists);
            }
            $flag_CSVShow = $objListFormatter->getGeneValue("linkCSVFormShow",$refRetKeyExists);
            if( $flag_CSVShow===null && $refRetKeyExists===false ){
                $flag_CSVShow = $objTable->getGeneObject("linkCSVFormShow",$refRetKeyExists);
            }
            //ロードテーブルからの設定を取得----

            $htmlFirstBake_AddArea_reqExcelDL = "";
            $htmlFirstBake_AddArea_reqCsvDL = "";
            $htmlFirstBake_AddArea_QMUL = "";

            $tmpArray = array();
            if( is_array($objListFormatter->getGeneValue("getInitPartOfEditByFile"))===true ){
                $tmpArray = $objListFormatter->getGeneValue("getInitPartOfEditByFile");
            }
            else{
                if( array_key_exists("getInitPartOfEditByFile", $arySetting) ){
                    $tmpArray = $arySetting["getInitPartOfEditByFile"];
                }
            }

            if($flag_ExcelHidden !== true){
                //----無条件で隠す、という設定ではない
                $btnXlsDlFlag = "";
                $intXlsLimit = isset($g['menu_xls_limit'])?$g['menu_xls_limit']:null;

                if( $intXlsLimit !== null && $intXlsLimit < $rowLength ){
                    //----エクセル出力の最大行を超えていた場合
                    $btnXlsDlFlag = "disabled=true ";
                    if( 0 < $intXlsLimit ){
                        $strLimitRowWarningMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-321",array($rowLength, $intXlsLimit));
                    }
                    if( $flag_CSVShow!==false ){
                        //----無条件でCSVを隠す、という設定ではない
                        $flag_CSVShow = true;
                        //無条件でCSVを隠す、という設定ではない----
                    }
                    //エクセル出力の最大行を超えていた場合----
                }

                if(array_key_exists("FirstBake_AddArea_reqExcelDL", $tmpArray)===true){
                    $htmlFirstBake_AddArea_reqExcelDL = $tmpArray['FirstBake_AddArea_i_exup'];
                }

                if( $strLinkExcelFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    //エクセル用のフォーマットIDがnullだった----
                }else{
                    $dlcHtmlBody .=
<<<EOD
            <form name="reqExcelDL_print_table" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-322")}" {$btnXlsDlFlag}>
                <input type="hidden" name="filteroutputfiletype" value="excel">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkExcelFormatterId}">
                {$htmlFirstBake_AddArea_reqExcelDL}
            </form>
            {$strLimitRowWarningMsgBody}
            <br>
EOD;
                //無条件で隠す、という設定ではない----
                }
            }

            if( $flag_CSVShow===true ){
                //----CSV系の常時ダウンロードを無条件で隠すという設定ではない場合、または、エクセルダウンロード上限数以上の場合

                if( $strLinkCSVFormatterId === null){
                    //----CSV用のフォーマットIDがnullだった
                    //CSV用のフォーマットIDがnullだった----
                }else{
                    $strOutputFileType = $objTable->getFormatter($strLinkCSVFormatterId)->getGeneValue("outputFileType");
                    if($strOutputFileType=="SafeCSV"){
                        $fileTypeNameBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-323");
                    }else{
                        $fileTypeNameBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-324");
                    }

                    if(array_key_exists("FirstBake_AddArea_reqCsvDL", $tmpArray)===true){
                        $htmlFirstBake_AddArea_reqCsvDL = $tmpArray['FirstBake_AddArea_reqCsvDL'];
                    }

                    $dlcHtmlBody .=
<<<EOD
            <form style="display:inline" name="reqCsvDL" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-325")}({$fileTypeNameBody})" >
                <input type="hidden" name="filteroutputfiletype" value="csv">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkCSVFormatterId}">
                {$htmlFirstBake_AddArea_reqCsvDL}
            </form>
            <br>
EOD;
                    $strOutputFileType = $objTable->getFormatter($strLinkCSVFormatterId)->getGeneValue("outputFileType");
                    if($strOutputFileType == "SafeCSV"){
                        $dlcHtmlBody .= 
<<<EOD
            <form style="display:inline" name="reqToolDL" action="{$g['scheme_n_authority']}/webdbcore/editorBaker.zip">
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-326")}" >
            </form>
            <br>
            <form style="display:inline" name="reqExcelDL" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-327")}" >
                <input type="hidden" name="filteroutputfiletype" value="excel">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkCSVFormatterId}">
                <input type="hidden" name="requestuserclass" value="visitor">
            </form>
            <br>
            <br>
            <br>
EOD;
                    }
                }
                //CSV系の常時ダウンロードを無条件で隠すという設定ではない場合、または、エクセルダウンロード上限数以上の場合----
            }

            if(array_key_exists("privilege", $g) && $g['privilege'] === "2"){
                //----権限がないので何もしない
                //権限がないので何もしない----
            }else{
                //----メンテナンス権限があった場合
                
                if( $strLinkExcelFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    //エクセル用のフォーマットIDがnullだった----
                }else{
                    //----新規登録用の空のエクセル
                    
                    $flag_NoRecExcelHidden = $objListFormatter->getGeneValue("noRecordExcelHidden",$refRetKeyExists);
                    
                    if( $flag_NoRecExcelHidden===true ){
                    }else{
                        $dlcHtmlBody .= 
<<<EOD
            <form name="reqExcelDL_register" action="{$g['scheme_n_authority']}/default/menu/04_all_dump_excel.php?no={$g['page_dir']}" method="POST" >
                <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-328")}" >
                <input type="hidden" name="filteroutputfiletype" value="excel">
                <input type="hidden" name="FORMATTER_ID" value="{$strLinkExcelFormatterId}">
                <input type="hidden" name="requestcontentclass" value="noselect">
            </form>
            <br />
EOD;
                    }
                    
                    //新規登録用の空のエクセル----
                }

                $boolUploadQMFileFormHidden = $objListFormatter->getGeneValue("uploadQMFileFormHidden");
                if($boolUploadQMFileFormHidden === true){
                    //----みせない（機能をOFFにするわけではない）
                    //みせない（機能をOFFにするわけではない）----
                }else{
                    //----アップロードフォームをみせる場合

                    if(array_key_exists("FirstBake_AddArea_QMUL", $tmpArray)===true){
                        $htmlFirstBake_AddArea_QMUL = $aryVariant['FirstBake_AddArea_QMUL'];
                    }

                    $strUploadQMFileMaxSize = $objListFormatter->getGeneValue("uploadQMFileMaxSize");
                    if( $strUploadQMFileMaxSize == "" ){
                        $strUploadQMFileMaxSize = 20*1024*1024;
                    }
                    //アップロードフォームをみせる場合----
                }

                $strSysIFOfQSALF = "SYSIF_{$checkFormatterId}"; //i_exup
                $strSysIdOFForm = "SYSFM_{$checkFormatterId}"; //input_exup
                $strSysIdOFRM = "SYSRMA_{$checkFormatterId}"; //result_exup
                $strSysIdOFUFB = "SYSULB_{$checkFormatterId}"; //btn_exup
                $strSysIdOfFileName = "SYSTFN_{$checkFormatterId}";// tmp_file_exup

                $strSysIdOfIAA = "SYSIAA_{$checkFormatterId}"; //input_exup_addarea

                $dlcHtmlBody .=
<<<EOD
<iframe id="{$strSysIFOfQSALF}" name="{$strSysIFOfQSALF}" style="display:none" ></iframe>
<form id="{$strSysIdOFForm}" action="./06_upload_excel.php?no={$g['page_dir']}" method="POST" encoding="multipart/form-data" enctype="multipart/form-data" target="{$strSysIFOfQSALF}">
    <input type="hidden" id="{$strSysIdOfFileName}" name="{$strSysIdOfFileName}" value="" />
    <input type="hidden" name="MAX_FILE_SIZE" value="{$strUploadQMFileMaxSize}" />
    <input type="hidden" name="FORMATTER_ID" value="{$checkFormatterId}">
    <span name="filewrapper"><input type="file" name="file" /></span>
    {$htmlFirstBake_AddArea_QMUL}
    <div id="{$strSysIdOfIAA}" style="display:none" ></div>
</form>
<p>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-329")}:<br /><span id="{$strSysIdOFRM}"></span></p>
<input type="button" id="{$strSysIdOFUFB}" name="1" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-330")}" onclick="
formControlForQMfileUpLoad(
    this,'{$strSysIdOFForm}','{$strSysIdOFRM}','{$strSysIdOFUFB}','{$strSysIFOfQSALF}',
    '{$g['objMTS']->getSomeMessage("ITAWDCH-STD-331")}','{$g['objMTS']->getSomeMessage("ITAWDCH-STD-332")}'
);
" />
EOD;

                //メンテナンス権限があった場合----
            }
            $dlcHtmlBody .= 
<<<EOD
            </div>
        </div>
EOD;

        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5005",array($tmpErrMsgBody,$intErrorType)));
            webRequestForceQuitFromEveryWhere(500,90110103);
            exit();
        }
        $retArray[0] = $boolShowFormatterArea;
        $retArray[1] = $dlcHtmlBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    
    function countTableRowLength($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="all_dump_table"){
        global $g;
        //----SQL指定がない限り、廃止前('0')と廃止('1')のレコードの合計行数を返す。
        $intControlDebugLevel01=200;
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $intRowLength = null;
        $intErrorType = null;
        $aryErrorMsgBody = array();
        
        $aryRetBody = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "QMFileSendAreaFormatter", $strFormatterId);
        $checkFormatterId = $aryRetBody[1];
        $objListFormatter = $aryRetBody[2];

        $query = generateSelectSql2(1, $objTable);
        $aryForBind = $objTable->getFilterArray(true);
        
        $arySettingOnLF = $objListFormatter->getGeneValue("countTableRowLength");
        
        if( array_key_exists("countTableRowLength",$arySetting)===true ){
            $tmpArray = $arySettingOnLF["countTableRowLength"];
            if( is_array($tmpArray)===true ){
                if( array_key_exists("sql",$tmpArray) ){
                    $query = $tmpArray["sql"];
                    if( array_key_exists("bindArray",$tmpArray) ){
                        if( is_array($tmpArray["bindArray"]) ){
                            $aryForBind = $tmpArray["bindArray"];
                        }
                    }
                }
            }
        }

        $intUnixTimeBegin=time();
        $intRowLength = 0;

        $aryRetBody = singleSQLExecuteAgent($query, $aryForBind, $strFxName);
        if( $aryRetBody[0] === true ){
            $objQuery = $aryRetBody[1];
            $row = $objQuery->resultFetch();
            unset($objQuery);
            $intRowLength = $row['REC_CNT'];
        }
        else{
            $intErrorType = 500;
            $intRowLength = -1;
        }
        

        $intUnixTimeFin=time();

        //レコード行数を取得する----
        
        if($intUnixTimeBegin + 10 <= $intUnixTimeFin){
            $intTimeSecond = $intUnixTimeFin - $intUnixTimeBegin;
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2001",array($intTimeSecond, $query)));
        }
        $retArray = array($intRowLength, $intErrorType, $aryErrorMsgBody);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    //07-全件ダウンロード領域系部品----

    //----08-履歴フィルタ作成部品
    function getJnlSearchFilterCommandArea($objTable, &$aryVariant=array(), &$arySetting=array(), $strFormatterId="print_journal_table"){
        global $g;
        $intControlDebugLevel01=200;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        $retArray = array();
        $dlcHtmlBody = "";

        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "JournalTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];

        $boolShowFormatterArea = true;
        if( $objListFormatter->getGeneValue("hidden")===true ){
            $boolShowFormatterArea = false;
        }

        $aryColumn = $objTable->getColumns();
        $objRIColumn = $aryColumn[$objTable->getRIColumnID()];
        $dlcHtmlBody = 
<<<EOD
            <table cellpadding="3">
                <tr>
                    <td>{$objRIColumn->getColLabel()}</td>
                    <td width="5" ></td>
                    <td><input id="j_{$objTable->getRIColumnID()}" type="text" name="{$objRIColumn->getIDSOP()}" size="10" maxlength="10" onkeydown="Journal1Tbl_pre_search_async(event.keyCode)" ></td>
                </tr>
            </table>
            <input type="button" id="display_journal_btn" name="display_journal_btn" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-313")}" onclick="javascript:Journal1Tbl_search_async();">
            <input type="button" id="reset_journal_btn"   name="reset_journal_btn"   value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-314")}" onclick="javascript:Journal1Tbl_reset_query();">
EOD;
        $retArray[0] = $boolShowFormatterArea;
        $retArray[1] = $dlcHtmlBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    //08-履歴フィルタ作成部品----

    function DTiSFilterCheckValid($objTable, $strFormatterId, $aryFilterData, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        $intControlDebugLevel01=50;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $intErrCount = 0;
        $intErrorType = null;
        $aryErrorMsgBody = array();

        $strRIValueNumeric=null;

        // 通常発動しない隠された機能は、$arySettingに[system_function_control.関数名.HiddenVars.フラグ名]、で発動を制御する。
        list($boolDecodeOfSelectTagStringEscape,$tmpKeyExists) = isSetInArrayNestThenAssign($arySetting,array('system_function_control',$strFxName,'HiddenVars','DecodeOfSelectTagStringEscape'),true);

        $aryObjColumn = $objTable->getColumns();
        $arySynonyms = $objTable->getColumnIDSOPs();

        foreach($aryFilterData as $strKey => $aryValue){
            $intAddCount = 0;
            foreach($aryValue as $intKeyNonUse => $strValue){
                $strParsedKey = "";
                if( 1 <= strlen($strValue) ){
                    $intFocusLoopErr = 0;
                    $strPostfix = substr($strKey, strlen($strKey)-3);

                    if( $strPostfix == "__S" ){
                        //----DateFilterTabBFmt(START)
                        $strParsedKey=substr($strKey, 0, strlen($strKey)-3);
                        //DateFilterTabBFmt(START)----
                    }else if( $strPostfix == "__E" ){
                        //----DateFilterTabBFmt(END)
                        $strParsedKey=substr($strKey, 0, strlen($strKey)-3);
                        //DateFilterTabBFmt(END)----
                    }else{
                        $strParsedKey=$strKey;
                    }

                    if( array_key_exists($strParsedKey, $arySynonyms) === true ){
                        //----存在するカラム名のPOSTの場合
                        //
                        $strParsedKey = $arySynonyms[$strParsedKey];
                        $objColumn = $aryObjColumn[$strParsedKey];
                        if($objColumn->getValidator()->isValid($strValue, $strRIValueNumeric, $aryFilterData, $aryVariant)){
                        }else{
                            $intErrCount += 1;
                            foreach($objColumn->getValidator()->getValidRule() as $strData){
                                $aryErrorMsgBody[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-101", array($strData, $objColumn->getColLabel(true)));
                            }
                            //----ココだけでよい
                            $intFocusLoopErr += 1;
                            //ココだけでよい----
                        }
                        if( $intFocusLoopErr == 0 ){
                            if( $strPostfix == "__S" ){
                                //----DateFilterTabBFmt(START)
                                $aryObjColumn[$strParsedKey]->addFilterValue($strValue, 0);
                                $intAddCount += 1;
                                //DateFilterTabBFmt(START)----
                            }else if( $strPostfix == "__E" ){
                                //----DateFilterTabBFmt(END)
                                $aryObjColumn[$strParsedKey]->addFilterValue($strValue, 1);
                                $intAddCount += 1;
                                //DateFilterTabBFmt(END)----
                            }else{
                                $aryObjColumn[$strParsedKey]->addFilterValue($strValue, null);
                                $intAddCount += 2;
                            }
                        }
                        if( 2 < $intAddCount ){
                            //----1個しか存在していないはずの通常検索条件項目に複数が入力された場合
                            $intErrCount+=1;
                            $aryErrorMsgBody[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-102", $objColumn->getColLabel(true));
                            //----1個しか存在していないはずの通常検索条件項目に複数が入力された場合
                        }
                        //存在するカラム名のPOSTの場合----
                    }
                }
            }
        }
        //----テキストカラムのリッチな検索機能用に追加
        $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["RICH_FILTER_TYPE"] = "DTiS_richFilterDefault";
        foreach($aryObjColumn as $objColumn){
            $strColumnAddSearchKey = $objColumn->getIDSOP()."_RF";
            if( array_key_exists($strColumnAddSearchKey,$aryFilterData) === true ){
                if( $objColumn->getSelectTagCallerShow() === true ){
                    $aryValue = $aryFilterData[$strColumnAddSearchKey];
                    foreach($aryValue as $intKeyNonUse => $strValue){
                        if( 1 <= strlen($strValue) ){
                            //----送信時に破壊されないように安全化された送信値を元に戻す
                            if( $boolDecodeOfSelectTagStringEscape === true ){
                                $strValue = addSelectTagStringEscape($strValue,"64ToOrg");
                            }
                            //送信時に破壊されないように安全化された送信値を元に戻す----
                            if($objColumn->getValidator()->isValid($strValue, $strRIValueNumeric, $aryFilterData, $aryVariant)){
                                $objColumn->addRichFilterValue($strValue);
                            }else{
                                $intErrCount+=1;
                                foreach($objColumn->getValidator()->getValidRule() as $strData){
                                    $aryErrorMsgBody[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-101", array($strData, $objColumn->getColLabel(true)));
                                }
                            }
                        }else{
                            $objColumn->setNullSearchExecute(true);
                        }
                    }
                }
                else{
                    //----プルダウン検索機能が無効にも関わらず、フィルタが設定された
                    $intErrCount+=1;
                    $aryErrorMsgBody[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-103", $objColumn->getColLabel(true));
                    //プルダウン検索機能が無効にも関わらず、フィルタが設定された----
                }
            }
        }
        unset($aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["RICH_FILTER_TYPE"]);
        //テキストカラムのリッチな検索機能用に追加
        if( 0 < $intErrCount ){
            $intErrorType = 2;
        }
        $retArray = array($intErrCount, $intErrorType, $aryErrorMsgBody);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }

    function convertReceptDataToDataForIUD($arrayReceptData){
        /*
        jQuery::serializeArray()で送られてくる：変数「$arrayReceptData」
        テキストの場合は、空白だと「配列キー」が登録される。
        セレクトボックスの場合は、未選択だと「配列キー」が登録されない。
        */
        global $g;
        $intControlDebugLevel01=300;
        $arrayIUDData = array();
        $arrayCountByName = array();
        $intTempValue = null;
        $strTempValue = "";
        
        foreach($arrayReceptData as $intKey=>$data){
            $dataName = $data['name'];
            $dataValue = $data['value'];
            if( array_key_exists($dataName, $arrayIUDData) === false ){
                $arrayIUDData[$dataName] = $dataValue;
            }else{
                if( array_key_exists($dataName, $arrayCountByName) === false ){
                    $strTempValue = $arrayIUDData[$dataName];
                    $arrayIUDData[$dataName] = array();
                    $arrayIUDData[$dataName][0] = $strTempValue;
                    $arrayCountByName[$dataName] = 1;
                }
                $arrayCountByName[$dataName] += 1;
                $intTempValue = $arrayCountByName[$dataName] - 1;
                $arrayIUDData[$dataName][$intTempValue] = $dataValue;
            }
        }
        return $arrayIUDData;
    }

    function convertReceptDataToDataForFilter($arrayReceptData){
        /*
        jQuery::serializeArray()で送られてくる：変数「$arrayReceptData」
        テキストの場合は、空白だと「配列キー」が登録される。
        セレクトボックスの場合は、未選択だと「配列キー」が登録されない。
        */
        global $g;
        $intControlDebugLevel01=300;
        $arrayPrintData = array();
        foreach($arrayReceptData as $data){
            if(!isset($arrayPrintData[$data['name']])){
                $arrayPrintData[$data['name']] = array();
            }
            $arrayPrintData[$data['name']][] = $data['value'];
        }
        return $arrayPrintData;
    }

    //----DBのカラム名を隠すための関数
    function hiddenColumnIdDecode($objTable, &$arrayData){
        global $g;
        $intControlDebugLevel01=300;
        $arrayColumn = $objTable->getColumns();
        foreach($arrayColumn as $key => $objColumn){
            $strIdSynonym = $objColumn->getIDSOP();
            if( array_key_exists($strIdSynonym, $arrayData) === true ){
                $arrayData[$key] = $arrayData[$strIdSynonym];
                unset($arrayData[$strIdSynonym]);
            }
        }
    }

    function hiddenColumnIdEncode($objTable, &$arrayData){
        global $g;
        $intControlDebugLevel01=300;
        $arrayColumn = $objTable->getColumns();
        foreach($arrayColumn as $key => $objColumn){
            $strIdSynonym = $objColumn->getIDSOP();
            if( array_key_exists($key, $arrayData)===true ){
                $arrayData[$strIdSynonym] = $arrayData[$key];
                unset($arrayData[$key]);
            }
        }
    }
    //DBのカラム名を隠すための関数----



    function makeSelectOption($array, $selected=null, $boolWhiteKeyAdd=false, $whiteDisp="", $boolKeySafe=true, $boolDispSafe=true){
        global $g;
        $intControlDebugLevel01=50;
        $str = "";
        if( $boolWhiteKeyAdd===true ){
            $opt = "";
            if(isset($selected) && $selected === ""){
                $opt = "SELECTED";
            }
            if( $boolDispSafe === true ){
                $whiteDisp = htmlspecialchars($whiteDisp);
            }
            $str .= "<OPTION VALUE=\"\" {$opt}>{$whiteDisp}</OPTION>\n";
        }
        foreach($array as $strSendKey => $strDispValue){
            $opt = "";
            // ----$strSendKeyが数値と評価できる値の場合は、暗黙型変換で数値型になっているケースに備え、型変換して比較する。
            if(is_array($selected)){
                foreach($selected as $keySelected=>$valSelected){
                    
                    if( (string)$strSendKey === (string)$valSelected ){
                        $opt = "SELECTED";
                    }else{
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-102",array(gettype($strSendKey),$strSendKey,gettype($valSelected),$valSelected)), $intControlDebugLevel01);
                    }
                }
            }else if( (string)$strSendKey === (string)$selected){
                $opt = "SELECTED";
            }
            // $strSendKeyが数値と評価できる値の場合は、暗黙型変換で数値型になっているケースに備え、型変換して比較する。----
            if( $boolKeySafe === true ){
                $strSendKey = htmlspecialchars($strSendKey);
            }
            if( $boolDispSafe === true ){
                $strDispValue = htmlspecialchars($strDispValue);
            }
            $str .= "<OPTION VALUE = \"{$strSendKey}\" {$opt}>{$strDispValue}</OPTION>\n";
        }
        return $str;
    }

    function addSelectTagStringEscape($value,$strVector=""){
        if($strVector=="64ToOrg"){
            $value = base64_decode($value);
        }else if($strVector=="64FromOrg"){
            $value = base64_encode($value);
        }
        return $value;
    }

    //----ここから、オラクルのみ処理のある関数
    function dbSearchResultExpand(){
        //----DB面で、大文字小文字と全角半角を無視する設定(ON)
        global $g;
        $boolRet=true;
        return $boolRet;
        //DB面で、大文字小文字と全角半角を無視する設定(ON)----
    }

    function dbSearchResultNormalize(){
        //----DB面で、大文字小文字と全角半角を無視する設定(OFF)
        global $g;
        $boolRet=true;
        return $boolRet;
        //DB面で、大文字小文字と全角半角を無視する設定(OFF)----
    }
    //ここまで、オラクルのみ処理のある関数----

    function getSequenceValue($strSequenceID,$boolGetInTrz=false,$boolLockOnly=false){
        global $g;
        
        $intControlDebugLevel01=500;
        
        if( $boolGetInTrz === true ){
            $retVal = getSequenceLockInTrz($strSequenceID,'A_SEQUENCE');
            if( $retVal[1] != 0 ){
            }else{
                if( $boolLockOnly === true ){
                }else{
                    $retVal = getSequenceValueFromTable($strSequenceID, 'A_SEQUENCE', FALSE );
                }
            }
        }else{
            $retVal = getSequenceValueFromTable($strSequenceID, 'A_SEQUENCE');
        }
        return $retVal;
    }

    function selectRowForUpdate(&$objTCA, $intRINo, $strOrdMode, $intMode=0){
        global $g;
        
        $intControlDebugLevel01=150;
        $arrayRetResult = array();
        $intErrorType = null;
        
        $sql = generateSelectSQLforUpdate($objTCA, $intMode);
        
        $strFxName = __FUNCTION__;
        
        $intTmpRowCount=0;
        $editTgtRow=array();
        $selectRowLength=0;
        
        try{
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($intRINo) === false ){
                if( 0==strlen($intRINo) ){
                    throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                else{
                    throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            $tmpAryBind = array($objTCA->getRIColumnID()=>$intRINo);
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    
                    if($intTmpRowCount==1){
                        $editTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                
                unset($objQuery);
            }
            else{
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //数値が指定された----
        }
        catch (Exception $e){
            $intErrorType = 500;
            $strTmpStrBody = $e->getMessage();
            $boolRet = false;
            if ( isset($objQuery) )    unset($objQuery);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$strTmpStrBody)));
        }
        
        $arrayRetResult[0] = $selectRowLength;
        $arrayRetResult[1] = $editTgtRow;
        $arrayRetResult[2] = $intErrorType;
        
        return $arrayRetResult;
        
        // 一旦SELECTしてレコードの追い越し更新がないかチェックする----
    }

    /* ----クラスIDColumn用「$masterTableBodyから、IDと$columnName列の項目のペアを配列で返す」(親クラスColumnも利用している)*/
    function createMasterTableArrayForFilter($masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseFlagColumnOfMasterTable, $aryEtcetera=array()){
        global $g;
        
        $intControlDebugLevel01=250;
        $strFxName = __FUNCTION__;
        
        $query = genSQLforGetMasValsForFilter($masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseFlagColumnOfMasterTable, $aryEtcetera);
        
        $data = array();
        
        $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
        if( $retArray[0] === true ){
            $objQuery = $retArray[1];
            while( $row = $objQuery->resultFetch() ){
                $data[$row['C1']] = $row['C2'];
            }
            unset($objQuery);
        }
        else{
            $data = null;
        }
        return $data;
    }

    /* ----クラスIDColumn用：登録および更新「参照先テーブル($masterTableBody)から、IDと$columnName列の項目のペアを配列で返す」*/
    function createMasterTableArrayForInput($masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseFlagColumnOfMasterTable, $aryEtcetera=array()){
        global $g;
        
        $intControlDebugLevel01=250;
        $strFxName = __FUNCTION__;
        
        $query = genSQLforGetMasValsForInput($masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseFlagColumnOfMasterTable, $aryEtcetera);
        
        $data = array();
        
        $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
        if( $retArray[0] === true ){
            $objQuery = $retArray[1];
            while( $row = $objQuery->resultFetch() ){
                $data[$row['C1']] = $row['C2'];
            }
            unset($objQuery);
        }
        else{
            $data = null;
        }
        return $data;
    }

    /* ----クラスIDColumn用：検索用（検索SELECT-TAG生成用）「参照先テーブル($masterTableBody)から、読書対象テーブル($mainTableBody)に使われているIDのみの配列を返す。*/
    function createMasterTableDistinctArray(
        $mainTableBody, $keyColumnOfMainTable, $disuseColumnOfMainTable
        , $masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseColumnOfMasterTable
        , $aryEtcetera=array()){
        global $g;
        
        $intControlDebugLevel01=250;
        $strFxName = __FUNCTION__;
        
        $query = genSQLforGetMasValsInMainTbl($mainTableBody, $keyColumnOfMainTable, $disuseColumnOfMainTable
                                              ,$masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseColumnOfMasterTable
                                              , $aryEtcetera);
        
        $data = array();
        
        $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
        if( $retArray[0] === true ){
            $objQuery = $retArray[1];
            while( $row = $objQuery->resultFetch() ){
                $data[$row['C1']] = $row['C2'];
            }
            unset($objQuery);
        }
        else{
            $data = null;
        }
        unset($objQuery);

        return $data;
    }

    /* ----履歴テーブルのIDColumnカラムで、マスターテーブルの履歴を遡及して、当時のDISP値を表示させるための、配列を返す*/
    function getDispValueFromJournalOfMasterTable(&$objTable, $objIdColumn, $mainColumnValue, $mainTimeStampValue, $aryEtcetera=array()){
        global $g;

        $intControlDebugLevel01=25;
        $strFxName = __FUNCTION__;

        $data = array();

        $query = generateMasterJournalSelectSQL($objTable, $objIdColumn, $aryEtcetera);
        $aryForBind = array($objIdColumn->getJournalKeyIDOfMaster()=>$mainColumnValue,$objIdColumn->getJournalLUTSIDOfMaster()=>$mainTimeStampValue);
        $retArray = singleSQLExecuteAgent($query, $aryForBind, $strFxName);
        if( $retArray[0] === true ){
            $objQuery = $retArray[1];
            while( $row = $objQuery->resultFetch() ){
                $data[$row['C1']] = $row['C2'];
            }
            unset($objQuery);
        }
        else{
            $data = null;
            $aryForMsg = array(
                $objIdColumn->getJournalTableOfMaster(),
                $mainColumnValue,
                $objIdColumn->getJournalKeyIDOfMaster(),
                $mainTimeStampValue,
                $objIdColumn->getJournalLUTSIDOfMaster()
            );
        }
        return $data;
    }

    function getDataFromLinkTable($objColumn, $intAnchorValue, $intGetType){
        global $g;
        $intControlDebugLevel01=250;
        $strFxName = __FUNCTION__;

        $strTableIdOfLink = $objColumn->getTableIDOfLinkUtn();

        $strAnchorColIdOfLink = $objColumn->getAnchorColumnIDOfLink();
        $strMasterKeyColIdOfLink = $objColumn->getMasterKeyColumnIDOfLink();

        $strLUDColIdOfLink = $objColumn->getLUTSColumnIDOfLink();
        $strDisuseColIdOfLink = $objColumn->getDisuseColumnIDOfLink();
        $arrayInputSet = $objColumn->getMasterTableArrayForInput();

        $data = null;
        if( $intGetType == 0 ){
            //----最後の更新日時を取得する
            $data = "NO-RECORD";
            if( $intAnchorValue != "" ){
                $query = generateSQLForGetDataFromLink($strTableIdOfLink, $strAnchorColIdOfLink, $strMasterKeyColIdOfLink, $strLUDColIdOfLink, $strDisuseColIdOfLink, $arrayInputSet, $intAnchorValue, $intGetType);
                $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
                if( $retArray[0] === true ){
                    $objQuery = $retArray[1];
                    while( $row = $objQuery->resultFetch() ){
                        $data = $row['TT_SYS_M_LAST_UPDATE_TIMESTAMP'];
                    }
                    unset($objQuery);
                }
                else{
                    $data = null;
                }
                unset($objQuery);
            }
            //最後の更新日時を取得する----
        }
        else{
            //----リストを取得する
            $data = array();
            if( $intAnchorValue != "" ){
                $query = generateSQLForGetDataFromLink($strTableIdOfLink, $strAnchorColIdOfLink, $strMasterKeyColIdOfLink, $strLUDColIdOfLink, $strDisuseColIdOfLink, $arrayInputSet, $intAnchorValue, $intGetType);
                $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
                if( $retArray[0] === true ){
                    $objQuery = $retArray[1];
                    while( $row = $objQuery->resultFetch() ){
                        $data[] = $row[$strMasterKeyColIdOfLink];
                    }
                    unset($objQuery);
                }
                else{
                    $data = null;
                }
                unset($objQuery);
            }
            //リストを取得する----
        }
        return $data;
    }

    function checkMultiColumnUnique(&$objTable, &$excSqlDataArray, &$aryVariant=array()){
        global $g;
        $intControlDebugLevel01=250;
        $retResultArray = array();
        $intErrorType = null;
        $strRetMsgBody="";
        $strSysErrMsgBody="";
        $boolFocusErr = false;
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            $arrayObjColumn = $objTable->getColumns();
            
            $strWhereDisuseRange = "";
            $aryDisuseCheckRange = $objTable->getUniqueCheckRangeInRequiredDisuseColumn();
            if( is_array($aryDisuseCheckRange) ){
                $aryTemp = array();
                foreach($aryDisuseCheckRange as $value){
                    $aryTemp[] = "'".$value."'";
                }
                $strWhereDisuseRange = "{$objTable->getShareTableAlias()}.{$objTable->getRequiredDisuseColumnID()} IN (".implode(",",$aryTemp).") "; 
                unset($aryTemp);
            }

            if( is_a($objTable,'TemplateTableForReview')===true ){
                //----レヴュー用コンテンツの場合
                //----(新規/追加｜修正/修正｜承認）
                $strCheckTableBody        = $objTable->getDBResultTableBody();
                $strRIColIdOfResultTbl    = $objTable->getLockTargetColumnID();
                $objRIColumnOfResultTable = $arrayObjColumn[$strRIColIdOfResultTbl];
                $strRIColLblOfResultTbl   = $objRIColumnOfResultTable->getColLabel();

                list($intRIColumnValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($excSqlDataArray,array($strRIColIdOfResultTbl),null);
                if( $intRIColumnValue===null ){
                    list($intRIColumnValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('edit_target_row',$strRIColIdOfResultTbl),null);
                }
                //(新規/追加｜修正/修正｜承認）----
                //レヴュー用コンテンツの場合----
            }else{
                $strCheckTableBody        = $objTable->getDBMainTableBody();
                $strRIColIdOfResultTbl    = $objTable->getRIColumnID();
                $objRIColumnOfResultTable = $arrayObjColumn[$strRIColIdOfResultTbl];
                $strRIColLblOfResultTbl   = $objRIColumnOfResultTable->getColLabel();

                list($intRIColumnValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($excSqlDataArray,array($strRIColIdOfResultTbl),null);
            }
            $arrayCheckSets = $objTable->getUniqueColumnSets();

            $intFocusNoInLoop1 = 0;

            foreach($arrayCheckSets as $key=>$arrayUniqueColumns){
                $boolCheckExcute=true;

                $intFocusNoInLoop1 += 1;

                $arraySet=array();
                $intCount=0;

                $boolFocusErr = false;

                $intTotalCount = count($arrayUniqueColumns);
                $intBlankCount = 0;
                
                $intFocusNoInLoop2 = 0;
                
                foreach($arrayUniqueColumns as $strColumnId){
                    
                    $intFocusNoInLoop2 += 1;
                    
                    if( is_string($strColumnId)===false ){
                        //----文字列型ではなかった
                        $intErrorType = 500;
                        $boolCheckExcute = false;
                        
                        throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[LOOP]' . $intFocusNoInLoop1 . '-' . $intFocusNoInLoop2 .')' );
                        //-文字列型ではなかった----
                    }else if( array_key_exists($strColumnId, $arrayObjColumn)===false ){
                        //----存在していないカラムだった
                        $intErrorType = 500;
                        $boolCheckExcute = false;

                        throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[LOOP]' . $intFocusNoInLoop1 . '-' . $intFocusNoInLoop2 .')' );
                        //存在していないカラムだった----
                    }
                    if(array_key_exists($strColumnId,$excSqlDataArray)===true){
                        $checkValue = $excSqlDataArray[$strColumnId];
                    }else{
                        //----キーが送信されなかった場合
                        if( $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"]=="DTUP_singleRecRegister" ){
                            //----登録の場合
                            $checkValue = "";
                            //登録の場合----
                        }else if( $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"]=="DTUP_singleRecUpdate" ){
                            //----更新の場合
                            if( array_key_exists($strColumnId, $aryVariant["edit_target_row"])===false ){
                                //----更新実体テーブルにないカラムが指定された場合等
                                throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[LOOP]' . $intFocusNoInLoop1 . '-' . $intFocusNoInLoop2 .')' );
                            }
                            $checkValue = $aryVariant["edit_target_row"][$strColumnId];
                            //更新の場合----
                        }else if( $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"]=="DTUP_singleRecDelete" ){
                            //----廃止・復活の場合
                            if( array_key_exists($strColumnId, $aryVariant["edit_target_row"])===false ){
                                //----更新実体テーブルにないカラムが指定された場合等
                                throw new Exception( '00000350-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[LOOP]' . $intFocusNoInLoop1 . '-' . $intFocusNoInLoop2 .')' );
                            }
                            $checkValue = $aryVariant["edit_target_row"][$strColumnId];
                            //廃止・復活の場合----
                        }else{
                            //----その他の場合
                            $checkValue = "";
                            //その他の場合----
                        }
                        //キーが送信されなかった場合
                    }
                    if( strlen($checkValue) == 0 ){
                        $intBlankCount += 1;
                    }
                    $arraySet[$strColumnId] = $checkValue;
                    $objColumn = $arrayObjColumn[$strColumnId];
                    $intCount+=1;
                }
                if( $intBlankCount == $intTotalCount ){
                    //----組み合わせ内の全列が空文字の場合は組み合わせチェックをしない
                    $boolCheckExcute = false;
                    //組み合わせ内の全列が空文字の場合は組み合わせチェックをしない----
                }

                if($boolCheckExcute === true && 0 < $intCount){
                    //----マルチユニーク判定条件の設定が存在する
                    $strWhereEle = "";
                    $arraySqlSelectCols = array();
                    $arraySqlWhereCols = array();
                    $strColStream = "";
                    $strWheresAdd = "";
                    $strWhereStream = "";
                    $strOrderStream = "";
                    $query = "";
                    //----存在するDBテーブルの、すべてカラムをSELECT句へ
                    foreach($arrayObjColumn as $strFocusColumnId=>$objFocusColumn){
                        if( $objFocusColumn->isDBColumn()===true ){
                            $arraySqlSelectCols[] = $objFocusColumn->getPartSqlInSelectZone();
                            if( array_key_exists($objFocusColumn->getID(), $arraySet)===true ){
                                $arraySqlWhereCols[$strFocusColumnId] = $objFocusColumn->getRowSelectQuery($arraySet);
                            }
                        }
                    }
                    //存在するDBテーブルの、すべてカラムをSELECT句へ----

                    $strColStream = implode(",",$arraySqlSelectCols);
                    $strWhereEle = implode(" AND ",$arraySqlWhereCols);
                    if( 0 < strlen($strWhereDisuseRange) ){
                        if( 0 < strlen($strWhereEle) ){
                            $strWhereEle .= " AND ";
                        }
                        $strWhereEle .= $strWhereDisuseRange;
                    }
                    if( 0<strlen($strWhereEle) ){
                        $strWhereStream  = "WHERE ". $strWhereEle;
                    }else{
                        $strWhereStream  = "";
                    }

                    $query  = "SELECT {$strColStream} ";
                    $query .= "FROM {$strCheckTableBody} {$objTable->getShareTableAlias()} ";
                    $query .= "{$objTable->getLeftJoinTableQuery()} ";
                    $query .= "{$strWhereStream} {$strOrderStream}";

                    unset($strWhereEle);
                    unset($arraySqlSelectCols);
                    unset($arraySqlWhereCols);
                    unset($strColStream);
                    unset($strWheresAdd);
                    unset($strWhereStream);
                    unset($strOrderStream);

                    $arrayRows = array();
                    $row_counter = 0;
                    $retArray = singleSQLExecuteAgent($query, $arraySet, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery = $retArray[1];
                        $count=0;
                        // 行取得
                        while( $row = $objQuery->resultFetch() ){
                            if( $row !== false ){
                                $count+=1;
                                $arrayRows[] = $row;
                            }
                        }
                        // ----レコード数を取得
                        $row_counter = $count;
                        // レコード数を取得----
                        unset($objQuery);
                    }
                    else{
                        $row_counter = -1;
                    }

                    if($row_counter == 0){
                        //----1行も存在しなかった
                        //1行も存在しなかった----
                    }else{
                        $arrayColumnName = array();
                        foreach($arrayUniqueColumns as $strColumnId){
                            $arrayColumnName[] = "(".str_replace(array("<br>","<br/>","<br />"),"・",$arrayObjColumn[$strColumnId]->getColLabel(true)).")";
                        }
                        if($row_counter == -1){
                            $boolFocusErr = true;
                            $intErrorType = 500;
                            
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-601",implode(",",$arrayColumnName)));                      
                            
                            $strRetMsgBody.=$g['objMTS']->getSomeMessage("ITAWDCH-ERR-602",implode(",",$arrayColumnName))."\n";
                            //----ループを抜ける
                            break;
                        }else if($row_counter == 1){
                            //----1行だけ存在した
                            
                            if( strlen($arrayRows[0][$strRIColIdOfResultTbl])==0 ){
                                //----想定外（識別カラムに入っている値の長さが0）
                                $boolFocusErr = true;
                                $intErrorType = 500;
                                
                                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[LOOP]' . $intFocusNoInLoop1 );
                                //想定外（識別カラムに入っている値の長さが0）----
                            }
                            
                            $boolResult=false;
                            if( $intRIColumnValue!==null ){
                                if( $intRIColumnValue == $arrayRows[0][$strRIColIdOfResultTbl]){
                                    //----更新する場合など、同じ業務上の主キーだった場合のみ、通過させる
                                    $boolResult = true;
                                    //更新する場合など、同じ業務上の主キーだった場合のみ、通過させる----
                                }else{
                                }
                                //更新する場合----
                            }
                            if($boolResult===false){
                                $boolFocusErr = true;
                                if(count($arraySet)===1){
                                    //----シングルユニーク判定の場合
                                    $boolFocusErr = true;
                                    foreach($arraySet as $val){
                                        if(is_array($val)===true){
                                            //----NULLだった場合
                                            $boolFocusErr = false;
                                            //NULLだった場合----
                                        }else{
                                            //----NULLではなかった場合
                                            //NULLではなかった場合----
                                        }
                                    }
                                    //シングルユニーク判定の場合----
                                }else{
                                    //----マルチユニーク判定の場合
                                    $boolFocusErr = true;
                                    //マルチユニーク判定の場合----
                                }
                                
                                if($boolFocusErr === true){
                                    $intErrorType = 2;
                                    $strRetMsgBody.= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-603",array($objRIColumnOfResultTable->getColLabel(true),$arrayRows[0][$strRIColIdOfResultTbl]))."\n";
                                    $strRetMsgBody.= "[".implode(",",$arrayColumnName)."]\n";
                                }
                            }else{
                            }
                            //1行だけ存在した----
                        }else{
                            //----2行以上存在している。
                            $boolFocusErr = true;

                            $arrayPK = array();
                            foreach($arrayRows as $row){
                                //----チケット942
                                $arrayPK[] = "(".$row[$strRIColIdOfResultTbl].")";
                                //チケット942----
                            }

                            if(count($arraySet)===1){
                                //----シングルユニーク判定の場合
                                foreach($arraySet as $val){
                                    if(is_array($val)===true){
                                        //----NULLだった場合
                                        $boolFocusErr = false;
                                        //NULLだった場合----
                                    }else{
                                        //----NULLではなかった場合
                                        //NULLではなかった場合----
                                    }
                                }
                                //シングルユニーク判定の場合----
                            }else{
                                //----マルチユニーク判定の場合
                                //マルチユニーク判定の場合----
                            }
                            
                            if($boolFocusErr === true){
                                $intErrorType = 2;
                                $strRetMsgBody.= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-604",implode(",",$arrayColumnName))."\n";
                                $strRetMsgBody.= "{$strRIColLblOfResultTbl}:[".implode(",",$arrayPK)."]\n";
                            }
                        }
                    }
                    //マルチユニーク判定条件の設定が存在する----
                }else{
                    //----マルチユニーク判定条件の設定が存在しない
                    //マルチユニーク判定条件の設定が存在しない----
                }
            }
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);

            //----システムエラー級エラーの場合はWebログにも残す
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
            if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
            //システムエラー級エラーの場合はWebログにも残す----
        }

        $retResultArray[0] = $intErrorType;
        $retResultArray[1] = $strRetMsgBody;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retResultArray;
    }

    //ここまでDB別に分岐する----

    //####----####----SQL文生成系

    function generateElementFromEditTargetRow(
        $excUtnDataArray
        ,$editUtnTargetRow
        ,$arrayObjColumn
        ,$strRequiredUpdateDate4UColumnId
        ,$strDBMainTableHiddenId){
        $exeQueryDataArray =array();

        //----更新前のUTNテーブルの要素
        foreach($editUtnTargetRow as $key => $value){
            if(array_key_exists($key, $arrayObjColumn) === false){
                //----現状テーブルからのレコードにあったが、設定にはないデータ[TT_SYS_ALIAS_AUUC_(NX)]を除去
                continue;
                //現状テーブルからのレコードにあったが、設定にはないデータ[TT_SYS_ALIAS_AUUC_(NX)]を除去----
            }else if( $key == $strRequiredUpdateDate4UColumnId ){
                //----追い越し更新防止カラムを除去
                continue;
                //追い越し更新防止カラムを除去----
            }else{
                $exeQueryDataArray[$key] = $value;
            }
        }

        if( 0 < strlen($strDBMainTableHiddenId) ){
            foreach($exeQueryDataArray as $key=>$value){
                $objColumn=$arrayObjColumn[$key];
                if( $objColumn->isHiddenMainTableColumn() !== true ){
                    unset($exeQueryDataArray[$key]);
                }
            }
        }
        //更新前のUTNテーブルの要素----

        //----登録または更新した分だけを上書き
        foreach($excUtnDataArray as $key => $value){
            $exeQueryDataArray[$key] = $value;
        }
        //登録または更新した分だけを上書き----

        return $exeQueryDataArray;
    }

    //----JOURNAL[登録]
    function generateElementForJournalReg(
        $excUtnDataArray
        ,$editUtnTargetRow
        ,$arrayObjColumn
        ,$strRequiredUpdateDate4UColumnId
        ,$strDBMainTableHiddenId){
        $excJnlDataArray =array();

        $excJnlDataArray = generateElementFromEditTargetRow($excUtnDataArray
                                                             ,$editUtnTargetRow
                                                             ,$arrayObjColumn
                                                             ,$strRequiredUpdateDate4UColumnId
                                                             ,$strDBMainTableHiddenId);

        //----JNLのみの要素を追加
        foreach($excUtnDataArray as $key => $value){
            if( is_array($value) === true ){
                if(array_key_exists('JNL',$value) === true){
                    $excJnlDataArray[$key] = $value['JNL'];
                }
            }
        }
        //JNLのみの要素を追加----

        return $excJnlDataArray;
    }
    
    //----UTN[登録]
    function generateRegisterSQL(
        $excRegDataArray
        ,$arrayObjColumn
        ,$strInsertTargetTableId
        ,$strDBMainTableHiddenId){
        //----この中で更新対象追加は、自動のもの、のみ、とすること。
        global $g;

        if( 0 < strlen($strDBMainTableHiddenId) ){
            $strInsertTargetTableId = $strDBMainTableHiddenId;
        }

        $arrayInsColId = array();
        $arrayInsValue = array();

        foreach($excRegDataArray as $key => $value){
            if( is_array($value) === true ){
                continue;
            }
            $objColumn = $arrayObjColumn[$key];

            $arrayInsColId[] = "{$key}";
            $arrayInsValue[] = $objColumn->getRowRegisterQuery($excRegDataArray);
        }

        $strColIdStream = "(".implode(",", $arrayInsColId).")";
        $strValueStream = "VALUES(".implode(",", $arrayInsValue).")";

        $query = "INSERT INTO {$strInsertTargetTableId} {$strColIdStream} {$strValueStream}";
        return array($query,$excRegDataArray);
    }
    //UTN[登録]----

    //----UTN[更新]
    function generateUpdateSQL(
        $exeUpdateDataArray
        ,$arrayObjColumn
        ,$strRIColumnId
        ,$strUpdateTargetTableId
        ,$strDBMainTableHiddenId){
        //----この中で更新対象追加は、自動のもの、のみ、とすること。
        global $g;

        if( 0 < strlen($strDBMainTableHiddenId) ){
            $strUpdateTargetTableId = $strDBMainTableHiddenId;
        }

        $arrayUpdColIdEqValue = array();

        foreach($exeUpdateDataArray as $key => $value){
            if( $key === $strRIColumnId ){
                //----wkPKはスキップ
                continue;
                //wkPKはスキップ----
            }else if( is_array($value) === true ){
                continue;
            }
            $objColumn = $arrayObjColumn[$key];
            $arrayUpdColIdEqValue[] = $objColumn->getRowUpdateQuery($exeUpdateDataArray);
        }

        $strColIdEqValStream = implode(",", $arrayUpdColIdEqValue);

        $query  = "UPDATE {$strUpdateTargetTableId} ";
        $query .= "SET {$strColIdEqValStream} ";
        $query .= "WHERE {$strRIColumnId} = :{$strRIColumnId}";
        return array($query,$exeUpdateDataArray);
    }
    //UTN[更新]----

    function generateJournalRegisterSQL(
        &$excJourDataArray
        ,$aryObjColumn
        ,$strInsertTargetTableId
        ,$strDBJournalTableHiddenId){
        //----この中で更新対象追加は、自動のもの、のみ、とすること。
        global $g;

        if( 0 < strlen($strDBJournalTableHiddenId) ){
            $strInsertTargetTableId = $strDBJournalTableHiddenId;
        }

        $arrayInsColId = array();
        $arrayInsValue = array();

        foreach($excJourDataArray as $key => $value){
            $objColumn = $aryObjColumn[$key];

            $arrayInsColId[] = "{$key}";
            $arrayInsValue[] = $objColumn->getRowRegisterQuery($excJourDataArray);
        }

        $strColIdStream = "(".implode(",", $arrayInsColId).")";
        $strValueStream = "VALUES(".implode(",", $arrayInsValue).")";

        $query = "INSERT INTO {$strInsertTargetTableId} {$strColIdStream} {$strValueStream}";

        return $query;
    }
    //JOURNAL[登録]----


    //----[2]更新系
    function generateSelectSQLforUpdate($objTable, $swhich=0){
        //----追越更新のチェックおよびWeb上の編集(更新または削除)テーブル生成の時に呼び出される
        global $g;

        $tgtChannel=0;
        $strForUpdateFlag="";
        $updTargetTable=$objTable->getDBMainTableBody();

        if($swhich==0){
            //----(Template)編集(更新または削除)Htmlタグテーブル生成の時に通過
            //(Template)編集(更新または削除)Htmlタグテーブル生成の時に通過----
        }else{
            //----(Template)追越更新判定の直前で通過
            if($objTable->getDBMainTableHiddenID()!=""){
                $updTargetTable=$objTable->getDBMainTableHiddenID();
                $tgtChannel=1;
            }
            $strForUpdateFlag="FOR UPDATE";
            //(Template)追越更新判定の直前で通過----
        }

        $arraySqlSelectCols = array();

        foreach($objTable->getColumns() as $key=>$objColumn){
            if($objColumn->isDBColumn()){
                if($tgtChannel==0){
                    $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                }else if($tgtChannel==1){
                    if($objColumn->isHiddenMainTableColumn()){
                        $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                    }else if( $key == $objTable->getRequiredUpdateDate4UColumnID() ){
                        //----メインテーブルに実体はないが、特別にクエリに追加
                        $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                        //メインテーブルに実体はないが、特別にクエリに追加----
                    }
                }
            }
        }

        $strColStream = implode(",", $arraySqlSelectCols);

        $query  = "SELECT {$strColStream} ";
        $query .= "FROM {$updTargetTable} {$objTable->getShareTableAlias()} ";
        $query .= "{$objTable->getLeftJoinTableQuery()} ";
        $query .= "WHERE {$objTable->getShareTableAlias()}.{$objTable->getRIColumnID()} = :{$objTable->getRIColumnID()} ";
        $query .= $strForUpdateFlag;

        return $query;
    }

    //[2]更新系----

    //----[3]UTN複数行SELECT用(1)
    function generateSelectSql2($mode, $objTable, $boolSchZenHanDistinct=true){
        // SELECT文を生成する関数
        global $g;
        $dbQM=$objTable->getDBQuoteMark();

        // ローカル変数宣言
        $query = "";
        $arrayObjColumn = $objTable->getColumns();

        if($mode == 1){
            $objRIColumn = $arrayObjColumn[$objTable->getRowIdentifyColumnID()];
            $strColStream = "COUNT({$dbQM}{$objTable->getShareTableAlias()}{$dbQM}.{$dbQM}{$objRIColumn->getID()}{$dbQM}) {$dbQM}REC_CNT{$dbQM}";
        }else if($mode == 2){
            $arraySqlSelectCols = array();

            foreach($arrayObjColumn as $objColumn){
                if($objColumn->isDBColumn()){
                    $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                }
            }
            $strColStream = implode(",",$arraySqlSelectCols);
        }

        $strWhereEle = $objTable->getFilterQuery($boolSchZenHanDistinct);
        if(0<strlen($strWhereEle)){
            $strWhereStream  = "WHERE ". $strWhereEle;
        }else{
            $strWhereStream  = "";
        }

        //---- ORDER BY句を付加
        $strOrderStream = "";
        if($mode == 2){
            $strOrderStream .= " ORDER BY {$objTable->getDBSortText()}";
        }
        // ORDER BY句を付加----

        $query  = "SELECT {$strColStream} ";
        $query .= "FROM {$objTable->getDBMainTableBody()} {$objTable->getShareTableAlias()} ";
        $query .= "{$objTable->getLeftJoinTableQuery()} ";
        $query .= "{$strWhereStream} {$strOrderStream}";

        return $query;
    }
    //[3]UTN複数行SELECT用(1)----

    //----[4]UTN複数行SELECT用(2)
    function generateSelectSql3(&$objTable, &$arrayColumnAndValue, &$aryVariant) {
        // SELECT文を生成する関数
        global $g;

        // ローカル変数宣言
        $query = "";

        $arrayObjColumn = $objTable->getColumns();

        $arraySqlSelectCols = array();

        $strWheresAdd = "";
        $arraySqlWhereCols = array();

        foreach($arrayObjColumn as $columnId=>$objColumn){
            if($objColumn->isDBColumn()){
                $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                if(array_key_exists($objColumn->getID(), $arrayColumnAndValue)===true){
                    $arraySqlWhereCols[] = $objColumn->getRowSelectQuery($arrayColumnAndValue);
                }
            }
        }

        $strColStream = implode(",",$arraySqlSelectCols);

        $strWhereEle = implode(" AND ",$arraySqlWhereCols);
        if(0<strlen($strWhereEle)){
            $strWhereStream  = "WHERE ". $strWhereEle;
        }else{
            $strWhereStream  = "";
        }

        //---- ORDER BY句を付加
        $strOrderStream = "";
        // ORDER BY句を付加----

        $query  = "SELECT {$strColStream} ";
        $query .= "FROM {$objTable->getDBMainTableBody()} {$objTable->getShareTableAlias()} ";
        $query .= "{$objTable->getLeftJoinTableQuery()} ";
        $query .= "{$strWhereStream} {$strOrderStream}";

        return $query;
    }
    //UTN複数行SELECT用(2)----

    //----[5]履歴複数行SELECT用
    function generateJournalSelectSQL($objTable,$boolSchZenHanDistinct=true){
        // SELECT文を生成する関数
        global $g;

        $arrayObjColumn = $objTable->getColumns();

        $arraySqlSelectCols = array();

        foreach($arrayObjColumn as $objColumn){
            if($objColumn->isDBColumn()){
                $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
            }
        }
        $strColStream = implode(",",$arraySqlSelectCols);

        if(is_bool($boolSchZenHanDistinct)===false) $boolSchZenHanDistinct = true;

        $strWhereEle = $objTable->getFilterQuery($boolSchZenHanDistinct);
        if(0<strlen($strWhereEle)){
            $strWhereStream  = "WHERE ". $strWhereEle;
        }else{
            $strWhereStream  = "";
        }

        // ----ORDER BY句を付加
        $strOrderStream = " ORDER BY {$objTable->getRequiredJnlSeqNoColumnID()} DESC";
        // ORDER BY句を付加----

        $query  = "SELECT {$strColStream} ";
        $query .= "FROM {$objTable->getDBJournalTableBody()} {$objTable->getShareTableAlias()} ";
        $query .= "{$objTable->getLeftJoinTableQuery()} ";
        $query .= "{$strWhereStream} {$strOrderStream}";

        return $query;
    }
    //[5]履歴複数行SELECT用----

    function generateMasterJournalSelectSQL(&$objTable, $objIdColumn, $aryEtcetera=array()){
        global $g;
        $lc_db_model_ch = $g['objDBCA']->getModelChannel();

        $arrayObjColumn = $objTable->getColumns();

        $strTimeStampColId = $objTable->getRequiredLastUpdateDateColumnID();

        $jnlMasterTable = $objIdColumn->getJournalTableOfMaster();

        $jnlMasterRefKeyId = $objIdColumn->getJournalKeyIDOfMaster();
        $jnlMasterRefDispId = $objIdColumn->getJournalDispIDOfMaster();

        $jnlSeqColId = $objIdColumn->getJournalSeqIDOfMaster();

        $jnlMasterLUDId = $objIdColumn->getJournalLUTSIDOfMaster();

        if( is_a($objIdColumn, 'AutoUpdateUserColumn') == true ){
            $strAddWhere = "AND ";
        }else{
            $strAddWhere = "AND {$objIdColumn->getRequiredDisuseColumnID()} IN ('0','1') AND ";
        }

        //----YYYY/MM/DD HH:NN:SS.uuuuuu形式でWhere句用クエリを取得
        $strWherePart = makeConvToDateSQLPartForDateWildColumn($lc_db_model_ch, ":".$strTimeStampColId, "DATETIME", true, false);

        $query  = "SELECT "
                 ."    {$jnlMasterRefKeyId} C1, {$jnlMasterRefDispId} C2 "
                 ."FROM "
                 ."    {$jnlMasterTable} "
                 ."WHERE "
                 ."    {$jnlSeqColId} = (SELECT "
                 ."                          MAX({$jnlSeqColId}) "
                 ."                      FROM "
                 ."                          (SELECT "
                 ."                               {$jnlSeqColId} "
                 ."                           FROM "
                 ."                               {$jnlMasterTable} "
                 ."                           WHERE "
                 ."                               {$jnlMasterRefKeyId} = :{$jnlMasterRefKeyId} "
                 ."                               {$strAddWhere} "
                 ."                               {$strTimeStampColId} <= {$strWherePart} "
                 ."                          ) TT_SYS_FROM"
                 ."                     )";
        return $query;
    }

    function generateSelectSQLForTrace($arySingleTraceQuery){
        global $g;
        $lc_db_model_ch = $g['objDBCA']->getModelChannel();

        $strSearchTableBody = $arySingleTraceQuery['TRACE_TARGET_TABLE'];
        $strJnlSeqNoColId = isset($arySingleTraceQuery['TTT_JOURNAL_SEQ_NO'])?$arySingleTraceQuery['TTT_JOURNAL_SEQ_NO']:"JOURNAL_SEQ_NO";
        $strTimeStampColId = isset($arySingleTraceQuery['TTT_TIMESTAMP_COLUMN_ID'])?$arySingleTraceQuery['TTT_TIMESTAMP_COLUMN_ID']:"LAST_UPDATE_TIMESTAMP";
        $strDisuseFlagColId = isset($arySingleTraceQuery['TTT_DISUSE_FLAG_COLUMN_ID'])?$arySingleTraceQuery['TTT_DISUSE_FLAG_COLUMN_ID']:"DISUSE_FLAG";

        $strSearchKeyColId = $arySingleTraceQuery['TTT_SEARCH_KEY_COLUMN_ID'];
        $strSelectColId = $arySingleTraceQuery['TTT_GET_TARGET_COLUMN_ID'];

        //----YYYY/MM/DD HH:NN:SS.uuuuuu形式でWhere句用クエリを取得
        $strWherePart = makeConvToDateSQLPartForDateWildColumn($lc_db_model_ch, ":".$strTimeStampColId, "DATETIME", true, false);

        $query  = "SELECT "
                 ."    {$strSearchKeyColId} C1, {$strSelectColId} C2 "
                 ."FROM "
                 ."    {$strSearchTableBody} "
                 ."WHERE "
                 ."    {$strJnlSeqNoColId} = (SELECT "
                 ."                               MAX({$strJnlSeqNoColId}) "
                 ."                           FROM "
                 ."                               (SELECT "
                 ."                                    {$strJnlSeqNoColId} "
                 ."                                FROM "
                 ."                                    {$strSearchTableBody} "
                 ."                                WHERE "
                 ."                                    {$strSearchKeyColId} = :{$strSearchKeyColId} "
                 ."                                    AND {$strTimeStampColId} <= {$strWherePart} "
                 ."                                    AND {$strDisuseFlagColId} IN ('0','1') "
                 ."                               ) TT_SYS_FROM"
                 ."                           )";
        return $query;
    }


    //[7]新規登録、更新用----

    function generateSQLForGetDataFromLink(
        $strTableIdOfLink, $strAnchorColIdOfLink, $strMasterKeyColIdOfLink, $strLUDColIdOfLink, $strDisuseColIdOfLink
        , $arrayInputSet, $intAnchorValue, $intGetType){
        global $g;
        $strSelectArea = "";
        if( $intGetType == 0 ){
            $strCaseColumn = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"MAX({$strLUDColIdOfLink})","DATETIME",true,true);
            
            $strSelectMaxLastUpdateTimestamp = "CASE WHEN MAX({$strLUDColIdOfLink}) IS NULL THEN 'NO-RECORD' ELSE {$strCaseColumn} END";
            $strSelectArea = "{$strSelectMaxLastUpdateTimestamp} TT_SYS_M_LAST_UPDATE_TIMESTAMP ";
            $strDisuseArea = "'0','1'";
        }
        else{
            $strSelectArea = "{$strMasterKeyColIdOfLink}";
            $strDisuseArea = "'0'";
        }
        $arrayKeys = array_keys($arrayInputSet);
        $strAnchorWhere = "";
        if( $intAnchorValue != "" ){
            $strAnchorWhere = "{$strTableIdOfLink}.{$strAnchorColIdOfLink} = {$intAnchorValue} ";
        }else{
            $strAnchorWhere = "";
        }
        $strEscaped = addslashes(implode(",",$arrayKeys));
        $query = "SELECT {$strSelectArea} ".
                 "FROM   {$strTableIdOfLink} ".
                 "WHERE  {$strAnchorWhere} ".
                 "AND {$strTableIdOfLink}.{$strMasterKeyColIdOfLink} IN (".$strEscaped.") ".
                 "AND {$strTableIdOfLink}.{$strDisuseColIdOfLink} IN (".$strDisuseArea.")";
        return $query;
    }

    //----ここからTableクラス・インスタンスを必要としない

    //----[NoTable-0001]フィルター・検出条件表示用
    function genSQLforGetMasValsInMainTbl(
        $mainTableBody, $keyColumnOfMainTable, $disuseColumnOfMainTable
        , $masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $disuseColumnOfMasterTable
        , $aryEtcetera=array()
        , $strWhereAddBody="", $strGetColIdOfKey="C1", $strGetColIdOfDisp="C2"){
        global $g;
        if(isset($aryEtcetera['OrderSortSeqType'])){
            $sortSeqType=$aryEtcetera['OrderSortSeqType'];
        }else{
            $sortSeqType="ASC";
        }
        if(isset($aryEtcetera['OrderByThirdColumn'])){
            $queryPartSelectAdd = "," . $aryEtcetera['OrderByThirdColumn'] . " SEQCOLUMN ";
            $queryPartOrd = isset($aryEtcetera['ORDER'])?$aryEtcetera['ORDER']:"ORDER BY JM1.SEQCOLUMN {$sortSeqType}";
        }else{
            $queryPartSelectAdd = "";
            if(isset($aryEtcetera['SELECT_ADD_FOR_ORDER'])){
                $intCount=1;
                foreach($aryEtcetera['SELECT_ADD_FOR_ORDER'] as $value){
                    $queryPartSelectAdd .= ",{$value} ADD_SELECT_{$intCount}";
                    $intCount+=1;
                }
            }
            $queryPartOrd = isset($aryEtcetera['ORDER'])?$aryEtcetera['ORDER']:"ORDER BY {$strGetColIdOfDisp} {$sortSeqType}";
        }
        $query = "SELECT "
                ."    JM1.IDCOLUMN {$strGetColIdOfKey}, JM1.DISPCOLUMN {$strGetColIdOfDisp} "
                ."FROM "
                ."    (SELECT "
                ."         DISTINCT {$keyColumnOfMainTable} "
                ."     FROM "
                ."         {$mainTableBody} "
                ."     WHERE "
                ."         {$disuseColumnOfMainTable} IN ('0','1') "
                ."         {$strWhereAddBody} "
                ."    ) MT1 "
                ."    INNER JOIN "
                ."    (SELECT "
                ."         {$keyColumnOfMasterTable} IDCOLUMN, {$dispColumnOfMasterTable} DISPCOLUMN {$queryPartSelectAdd} "
                ."     FROM "
                ."         {$masterTableBody} "
                ."     WHERE "
                ."         {$disuseColumnOfMasterTable} IN ('0','1') "
                ."    ) JM1 "
                ."    ON MT1.{$keyColumnOfMainTable} = JM1.IDCOLUMN "
                ."{$queryPartOrd}";
        return $query;
    }
    //[NoTable-0001]フィルター・検出条件表示用----

    //----[NoTable-0002]フィルター結果、その他マスターの通常利用
    function genSQLforGetMasValsForFilter(
        $masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $masterDisuseFlagColumnId
        , $aryEtcetera=array()){
        $query  = "SELECT "
                 ."    {$keyColumnOfMasterTable} C1, {$dispColumnOfMasterTable} C2 "
                 ."FROM "
                 ."    {$masterTableBody} "
                 ."WHERE "
                 ."    {$masterDisuseFlagColumnId} IN ('0') "
                 ."ORDER BY C2";
        return $query;
    }
    //[NoTable-0002]フィルター結果、その他マスターの通常利用----

    //----[NoTable-0003]新規登録、更新用・マスター利用
    function genSQLforGetMasValsForInput(
        $masterTableBody, $keyColumnOfMasterTable, $dispColumnOfMasterTable, $masterDisuseFlagColumnId
        , $aryEtcetera=array()){
        global $g;
        if(isset($aryEtcetera['OrderSortSeqType'])){
            $sortSeqType=$aryEtcetera['OrderSortSeqType'];
        }else{
            $sortSeqType="ASC";
        }
        if(isset($aryEtcetera['OrderByThirdColumn'])){
             $queryPartSelectAdd = "," . $aryEtcetera['OrderByThirdColumn'] . " SEQCOLUMN ";
             $queryPartOrd = isset($aryEtcetera['ORDER'])?$aryEtcetera['ORDER']:"ORDER BY SEQCOLUMN {$sortSeqType}";
        }else{
            $queryPartSelectAdd = "";
            if(isset($aryEtcetera['SELECT_ADD_FOR_ORDER'])){
                $intCount=1;
                foreach($aryEtcetera['SELECT_ADD_FOR_ORDER'] as $value){
                    $queryPartSelectAdd .= ",{$value} ADD_SELECT_{$intCount}";
                    $intCount+=1;
                }
            }
            $queryPartOrd = isset($aryEtcetera['ORDER'])?$aryEtcetera['ORDER']:"ORDER BY C2 {$sortSeqType}";
        }
        $query = "SELECT "
                ."    {$keyColumnOfMasterTable} C1, {$dispColumnOfMasterTable} C2 {$queryPartSelectAdd} "
                ."FROM "
                ."    {$masterTableBody} "
                ."WHERE "
                ."    {$masterDisuseFlagColumnId} IN ('0') "
                ."{$queryPartOrd}";
        return $query;
    }
    //[NoTable-0003]新規登録、更新用・マスター利用----
    
    //ここまでTableクラス・インスタンスを必要としない----

    //SQL文生成系----####----####

    function singleSQLExecuteAgent($strSql,$aryForBind=array(),$strCallOwnerMark=""){
        global $g;
        $retArray = array();
        $intControlDebugLevel01=100;
        $strFxName = __FUNCTION__;
        $retBoolResult = false;
        $retStrLastErrMsg = "";
        if($strCallOwnerMark==""){
            $strCallOwnerMark = "UNKNOWN";
        }
        try{
            $objQuery = $g['objDBCA']->sqlPrepare($strSql);
            if( $objQuery->getStatus()===false ){
                // 例外処理へ
                throw new Exception( 'SQL PREPARE' );
            }
            if( $objQuery->sqlBind($aryForBind) != "" ){
                // 例外処理へ
                throw new Exception( 'SQL BIND' );
            }
            $retBoolResult = $objQuery->sqlExecute();
            if($retBoolResult!=true){
                // 例外処理へ
                throw new Exception( 'SQL EXECUTE' );
            }
        }
        catch (Exception $e){
            $strErrInitTime = getMircotime(1);
            if( is_string($strSql)!==true ){
                $strSql = "";
            }
            $strSSEAErrInitKey = '[sSEA-Err-initKey:'.md5($strErrInitTime.bin2hex($strSql)).']';
            
            $tmpAryData = debug_backtrace($limit=1);
            $aryBackTrace = array($tmpAryData[0]['file'],$tmpAryData[0]['line']);
            
            $strTmpStrBody = '([FILE]'.$aryBackTrace[0].',[LINE]'.$aryBackTrace[1].')'.$strSSEAErrInitKey.' '.$e->getMessage();
            $boolRet = false;
            $retStrLastErrMsg = $objQuery->getLastError();
            
            if ( isset($objQuery) )    unset($objQuery);
            $objQuery = null;
            
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-605",array($strTmpStrBody,$strCallOwnerMark)));
            web_log($strSSEAErrInitKey.$strSql);
            web_log($strSSEAErrInitKey.print_r($aryForBind,true));
            web_log($strSSEAErrInitKey.$retStrLastErrMsg);
        }
        $retArray[0] = $retBoolResult;
        $retArray[1] = $objQuery;
        $retArray[2] = $retStrLastErrMsg;
        return $retArray;
    }
    
?>
