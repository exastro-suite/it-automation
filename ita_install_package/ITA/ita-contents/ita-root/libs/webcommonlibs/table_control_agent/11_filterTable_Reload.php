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

    function filterTableReloadMain($objTable, $strFormatterId, &$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        // ----ローカル変数宣言
        $intControlDebugLevel01 = 250;
        $intControlDebugLevel02 = 500;
        //
        // return値
        $varRet = null;
        //
        $strResultCode = "000";
        $strErrorCode = "000";
        $strDetailCode = "000";
        $strOutputStr = "";
        $intErrorType = null;
        $error_str = "";

        $strShowTable01TagId = null;

        $ACRCM_id = "UNKNOWN";

        $defaultValueOnFx = array("Filter1Tbl","fakeContainer_Filter1Setting");
        $refRetKeyExists = null;
        // ----ローカル変数宣言

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

            $defaultValueOnFx = checkOverrideValue($strFxName, $defaultValueOnFx, $aryOverride);

            //----関数内デフォルト
            $strShowTable01TagId = $objListFormatter->getGeneValue("stdFilterTable.tagIDonHTML",$refRetKeyExists);
            if( $strShowTable01TagId===null ){
                $strShowTable01TagId = $defaultValueOnFx[0];
            }
            $strShowTable01WrapDivClass = $objListFormatter->getGeneValue("stdFilterTable.wrapDivClass",$refRetKeyExists);
            if( $strShowTable01WrapDivClass===null ){
                $strShowTable01WrapDivClass = $defaultValueOnFx[1];
            }
            //関数内デフォルト----

            $strFilterBody = $objTable->getPrintFormat($checkFormatterId, $strShowTable01TagId);

            if( $strFilterBody == "" ){
                // ----何のタグも出力されなかった
                $intErrorType = 601;
                throw new Exception( '00010200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 何のタグも出力されなかった----
            }

            $strOutputStr = "<div class=\"{$strShowTable01WrapDivClass}\">".$strFilterBody."</div>";
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
             
            $strOutputStr = nl2br($error_str);

            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-373",array($ACRCM_id,$intErrorType)));
        }

        $varRet[0] = $strResultCode;
        $varRet[1] = $strErrorCode;
        $varRet[2] = $strOutputStr;

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        return $varRet;

    }
?>
