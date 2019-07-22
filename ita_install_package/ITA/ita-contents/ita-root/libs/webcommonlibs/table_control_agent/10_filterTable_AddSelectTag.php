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
    //    ・フィルター領域に、プルダウンリストHtmlタグを、事後的に作成する
    //
    //////////////////////////////////////////////////////////////////////

    function AddSelectTagToTextFilterTab($objTable, $strFormatterId, $intColumnSeq, &$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        // ----ローカル変数宣言
        $intControlDebugLevel01 = 250;
        $intControlDebugLevel02 = 500;
        
        // return値
        $varRet = array();
        $varRet[0] = null;
        $varRet[1] = null;
        $varRet[2] = null;
        $varRet[3] = null;
        
        $intErrorType = null;
        $error_str = "";
        $strErrorBuf = "";
        $strSysErrMsgBody = "";
        
        $strResultCode = "000";
        $strErrorCode = "000";
        $strDetailCode = "000";
        $strOutputStr = "";

        $column_id = "";
        $optionBodies = "";

        $strRetTagBody = "";
        $varAddResultData = array();

        $ACRCM_id = "UNKNOWN";

        $refRetKeyExists = null;
        // ----ローカル変数宣言

        $defaultValueOnFx = array("Filter1Tbl");

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");
            if( $boolKeyExists === false ){
                list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
            }
            //メニューIDの取得----

            //----テーブル設定の調査

            $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "FilterConditionTableFormatter", $strFormatterId);
            $checkFormatterId = $retArray[1];
            $objListFormatter = $retArray[2];

            //テーブル設定の調査----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTiS_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }

            if( $strPrivilege === "1" ){
                // ----1はメンテナンス権限あり
                // 1はメンテナンス権限あり----
            }else if( $strPrivilege === "2" ){
                /// ----2は参照のみ
                // 2は参照のみ----
            }else{
                // ----0は権限がないので出力しない
                $intErrorType = 1;
                throw new Exception( '00010100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            $aryObjColumn = $objTable->getColumns();

            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($intColumnSeq) === false ){
                //----不正な値が送信された。
                $intErrorType = 601;
                throw new Exception( '00010200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //不正な値が送信された。----
            }

            $intPrintSeq = 0;
            foreach($aryObjColumn as $focusColumn){
                $focusObjOT = $focusColumn->getOutputType($strFormatterId);
                if( $focusObjOT->isVisible() === true ){
                    $focusObjOT->setPrintSeq($intPrintSeq);
                    if( $intColumnSeq == $intPrintSeq ){
                        $objFocusCF = $focusObjOT->getBody();
                        if( $focusColumn->getSelectTagCallerShow() === true && is_a($objFocusCF, "FilterTabBFmt") === true ){
                            $column_id = $focusColumn->getID();
                            break;
                        }
                    }
                    $intPrintSeq += 1;
                }
            }

            if( $column_id == "" ){
                // ----カラムが特定できなかった
                $intErrorType = 602;
                throw new Exception( '00010300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // カラムが特定できなかった----
            }

            $objColumn = $aryObjColumn[$column_id];

            if($objColumn->isDBColumn()!==true && is_a($objColumn, 'WhereQueryColumn')===false ){
                // ----DBカラムではなく、かつ、検索専用カラム系でもなかった
                $intErrorType = 603;
                throw new Exception( '00010400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // DBカラムではなく、かつ、検索専用カラム系でもなかった----
            }

            $printTagId = $intColumnSeq;
            
            $defaultValueOnFx = checkOverrideValue($strFxName, $defaultValueOnFx, $aryOverride);
            $strShowTable01TagId = $objListFormatter->getGeneValue("tableTagIdForFilter",$refRetKeyExists);
            if( $strShowTable01TagId===null && $refRetKeyExists===false  ){
                $strShowTable01TagId = $defaultValueOnFx[0];
            }
            
            $strNowPrintingId = $strShowTable01TagId;
            
            //----瞬間存在値なのでセット
            $objTable->setPrintingTableID($strNowPrintingId);
            //瞬間存在値なのでセット----
            
            $aryRetBody = $objFocusCF->printTagFromFADSelectList($aryVariant, $arySetting, $aryOverride);
            
            //----瞬間存在値なのでクリア
            $objTable->setPrintingTableID(null);
            //瞬間存在値なのでクリア----
            
            if( $aryRetBody[1]!==null ){
                $intErrorType = $aryRetBody[1];
                throw new Exception( '00010500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $strRetTagBody = $aryRetBody[0];
            if( array_key_exists(4,$aryRetBody) === true ){
                $varAddResultData = $aryRetBody[4];
            }
            $strOutputStr = $printTagId;

            // アクセスログへ記録
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                default : $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
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
            
            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-367",array($ACRCM_id,$intErrorType)));
        }

        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        $varRet[3] = $strRetTagBody;
        
        if( is_string($varAddResultData) === true ){
            $varRet[4] = $varAddResultData;
        }
        else if( is_array($varAddResultData) === true && 1 <= count($varAddResultData) ){
            foreach($varAddResultData as $strData){
                $varRet[] = $strData;
            }
        }
        else{
            $varRet[4] = "";
        }

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        return $varRet;

    }
?>
