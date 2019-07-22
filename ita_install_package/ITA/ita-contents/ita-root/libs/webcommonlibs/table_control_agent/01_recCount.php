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


    function recCountMain($objTable, $strFormatterId="print_table", $filterData=array(), &$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
        global $g;
        require_once ("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
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

        $strRecCnt = "";

        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;

        $defaultValueOnFx = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
        $refRetKeyExists = null;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----D-TiS共通
            $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "FilterResultTableFormatter", $strFormatterId);
            $checkFormatterId = $retArray[1];
            $objListFormatter = $retArray[2];

            $defaultValueOnFx = checkOverrideValue($strFxName, $defaultValueOnFx, $aryOverride);
            //D-TiS共通----

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
            $arrayObjColumn = $objTable->getColumns();

            $optAllHidden=false;
            if(array_key_exists("optionAllHidden",$aryVariant)===true){
                $optAllHidden = $aryVariant['optionAllHidden'];
            }

            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_recCount");
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
                /// ----2は参照のみ
                // 2は参照のみ----
            }else{
                // ----0は権限がないので出力しない
                $intErrorType = 1;
                throw new Exception( '00010100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            $boolBinaryDistinctOnDTiS = $objListFormatter->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
            if( $boolBinaryDistinctOnDTiS===null && $refRetKeyExists===false  ){
                $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
            }
            if(is_bool($boolBinaryDistinctOnDTiS)===false){
                $boolBinaryDistinctOnDTiS = false;
            }

            if(array_key_exists("directSQL",$aryVariant)===true){
                //----直で実行するSQLが指定された
                $sql = $aryVariant['directSQL']['sqlBody'];
                if(isset($aryVariant['directSQL']['bindBody'])===true){
                    $arrayFileterBody = $aryVariant['directSQL']['bindBody'];
                }else{
                    $arrayFileterBody = array();
                }
                //直で実行するSQLが指定された----
            }else{
                //----通常モード

                //----必須チェックなどを事前にしたい場合は、ここで差し替え
                if(array_key_exists("filter_ctl_start_limit",$filterData)){
                     unset($filterData["filter_ctl_start_limit"]);
                }
                
                if( $aryFunctionForOverride!==null ){
                     list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("recCountMain",$strFormatterId,"DTiSFilterCheckValid"),null);
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

                if($boolBinaryDistinctOnDTiS === true){
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

                // ----generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]
                $mode = 1;
                $sql = generateSelectSql2($mode, $objTable, $boolBinaryDistinctOnDTiS);
                // generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]----

                //通常モード----
            }
            //
            
            if( $aryFunctionForOverride!==null ){
                 list($tmpObjFunction02ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("recCountMain",$strFormatterId,"recCount"),null);
                 unset($tmpBoolKeyExist);
                 if( is_callable($tmpObjFunction02ForOverride)===true ){
                     $objFunction02ForOverride = $tmpObjFunction02ForOverride;
                 }
                 unset($tmpObjFunction02ForOverride);
            }

            if( $objFunction02ForOverride===null ){
                $strRecCnt =  0;
                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $row = $objQuery->resultFetch();
                    $strRecCnt = $row['REC_CNT'];
                    unset($objQuery);
                }
                else{
                    $intErrorType = 500;
                    throw new Exception( '00010700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            else{
                $tmpAryRet = $objFunction02ForOverride($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting, $aryOverride);
                if( $tmpAryRet[1]!==null ){
                    $intErrorType = $tmpAryRet[1];
                    $error_str = implode("", $tmpAryRet[2]);
                    unset($tmpAryRet);
                    throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $strRecCnt = $tmpAryRet[0];
                unset($tmpAryRet);
            }
            
            if(array_key_exists("directSQL",$aryVariant)===true){
                //----直で実行するSQLが指定された
                //直で実行するSQLが指定された----
            }else{
                //----通常モード
                if($boolBinaryDistinctOnDTiS === true){
                }else{
                    $boolFocusRet= dbSearchResultNormalize();
                    if($boolFocusRet === false){
                        $intErrorType = 500;
                        throw new Exception( '00010900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                //
                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                    if($arrayTmp[0]===false){
                        $intErrorType = $arrayTmp[1];
                        throw new Exception( '00011000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                //通常モード----
            }
            $strOutputStr = $strRecCnt;
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 1 : $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-111"); break; //----権限なし
                case 2 : break; //----バリデーションエラー
                default: $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-112"); break;//----一般エラー
            }
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
?>
