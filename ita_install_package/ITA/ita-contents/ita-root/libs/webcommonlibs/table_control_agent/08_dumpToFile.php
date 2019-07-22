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

    //----webroot(03,04)
    function tableDumpToFile($objTable, $aryVariant=array(), &$arySetting=array()){
        dumpDataFromTable(array(), $objTable, $aryVariant, $arySetting);
    }
    //----webroot(03,04)

    //----webroot(03,04,07)
    function dumpDataFromTable($aryToArea, $objTable, $aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        $intControlDebugLevel02=250;

        $varRetBody = null;
        $aryUploadFile = array();

        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $aryFxResultErrMsgBody = array();

        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $boolConDisposition = 1;
        $intUnixTime = time();

        $refRetKeyExists = null;
        $boolKeyExists = null;

        $strOutputFileType = null;

        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;
        $objFunction03ForOverride = null;
        $objFunction04ForOverride = null;

        $ACRCM_id = "UNKNOWN";
        // ローカル変数宣言----

        $strErrMsgBodyToHtmlUI = "";
        $strErrMsgBodyToWAL = "";

        $filterData = array();
        $boolBinaryDistinctOnDTiS = null;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)
            list($strToAreaType,$boolKeyExists) = isSetInArrayNestThenAssign($aryToArea,array('to_area_type'),null);
            switch($strToAreaType){
                case "toFile":
                case "toReturn":
                    break;
                default:
                    $strToAreaType = "toStd";
            }
            //出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)----

            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");
            if( $boolKeyExists === false ){
                list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
            }
            //メニューIDの取得----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTiS_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }
            switch($strPrivilege){
                case "1":
                case "2":
                    break;
                default:
                    $intErrorType = 1;
                    $intErrorPlaceMark = 100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            //権限の取得/判定----

            //----出力するファイル形式を判別する
            list($strOutputFileType,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','strOutputFileType'),"");
            if( $strOutputFileType == "" ){
                list($strOutputFileType,$boolKeyExists) = isSetInArrayNestThenAssign($_POST,array('filteroutputfiletype'),"");
                if( $boolKeyExists === false ){
                    $intErrorType = 601;
                    $intErrorPlaceMark = 200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            switch($strOutputFileType){
                case "arraysForJSON":
                    break;
                case "csv":
                    break;
                case "excel":
                    break;
                default:
                    $intErrorType = 601;
                    $intErrorPlaceMark = 300;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            //出力するファイル形式を判別する----

            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                $intErrorPlaceMark = 400;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }

            //----テーブル設定の調査
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorType = 501;
                $intErrorPlaceMark = 500;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            //テーブル設定の調査----

            list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','strFormatterId'),"");
            if( $strFormatterId == "" ){
                list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($_POST,array('FORMATTER_ID'),"");
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "ListFormatter") !== true ){
                // ----リストフォーマッタクラスではない
                $intErrorType = 501;
                $intErrorPlaceMark = 600;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // リストフォーマッタクラスではない----
            }

            $optAllHidden=false;
            if( array_key_exists("optionAllHidden",$aryVariant) === true ){
                $optAllHidden = $aryVariant['optionAllHidden'];
            }

            //----PHPのタイムアウトを再設定する(単位は秒)
            $intDTFTimeLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFTimeLimit",$refRetKeyExists);
            if( $intDTFTimeLimit===null && $refRetKeyExists===false ){
                $intDTFTimeLimit = $objTable->getGeneObject("intDTFTimeLimit",$refRetKeyExists);
            }
            if( $intDTFTimeLimit!==null && is_int($intDTFTimeLimit)===true ){
                set_time_limit($intDTFTimeLimit);
            }
            //PHPのタイムアウトを再設定する(単位は秒)----

            //----PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))
            $intDTFMemoryLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFMemoryLimit",$refRetKeyExists);
            if( $intDTFMemoryLimit===null && $refRetKeyExists===false ){
                $intDTFMemoryLimit = $objTable->getGeneObject("intDTFMemoryLimit",$refRetKeyExists);
            }
            if( $intDTFMemoryLimit!==null && is_int($intDTFMemoryLimit) ){
                ini_set("memory_limit",strval($intDTFMemoryLimit)."M");
            }
            //PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))----

            //----[EXCEL]出力するコンテンツの種類、を判別する
            if( $strOutputFileType == "excel" ){
                $strPrintTypeMode = "normal";
                $boolNoSelectMode = false;
                list($tmpStrRequestUserClass,$boolKeyExists) = isSetInArrayNestThenAssign($_POST,array('requestuserclass'),null);
                if( $boolKeyExists === true ){
                    if( $tmpStrRequestUserClass == "forDeveloper" ){
                        list($tmpVarDevLogDeveloper,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('dev_log_developer'),null);
                        //
                        if( 1 <= $tmpVarDevLogDeveloper ){
                            //----開発者用エクセル
                            $strPrintTypeMode = "forDeveloper";
                            $boolNoSelectMode = true;
                        }
                        else{
                            // 権限が不足している
                            $intErrorType = 1;
                            $intErrorPlaceMark = 700;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($tmpVarDevLogDeveloper);
                    }
                    else if( $tmpStrRequestUserClass == "visitor" ){
                        // SafeCSV用作成用のエクセル
                        $strPrintTypeMode = "forVisitor";
                        $boolNoSelectMode = true;
                    }
                    unset($tmpStrRequestUserClass);
                    //
                    //開発者用エクセルの出力をするか----
                }
                else{
                    list($tmpStrRequestClass,$boolKeyExists) = isSetInArrayNestThenAssign($_POST,array('requestcontentclass'),null);
                    if( $boolKeyExists === true ){
                        if( $tmpStrRequestClass == "noselect" ){
                            // 新規登録用エクセル
                            $boolNoSelectMode = true;
                        }
                        else{
                            $intErrorType = 601;
                            $intErrorPlaceMark = 800;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    unset($tmpStrRequestClass);
                }
            }
            //[EXCEL]出力するコンテンツの種類、を判別する----

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
            $arrayObjColumn = $objTable->getColumns();

            //----SQL作成
            if( array_key_exists("directSQL",$aryVariant) === true ){
                //----直で実行するSQLが指定された
                $sql = $aryVariant['directSQL']['sqlBody'];
                if(isset($aryVariant['directSQL']['bindBody'])===true){
                    $arrayFileterBody = $aryVariant['directSQL']['bindBody'];
                }else{
                    $arrayFileterBody = array();
                }
                //直で実行するSQLが指定された----
            }
            else{
                //----通常モード
                if( array_key_exists("search_filter_data",$aryVariant) === true ){
                    //----検出条件が指定された場合

                    //----大文字小文字と全角半角を無視する設定かを調べる
                    $boolBinaryDistinctOnDTiS = $objTable->getFormatter($strFormatterId)->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
                    if( $boolBinaryDistinctOnDTiS === null && $refRetKeyExists === false ){
                        $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
                    }
                    if( is_bool($boolBinaryDistinctOnDTiS) === false ){
                        $boolBinaryDistinctOnDTiS = false;
                    }

                    // 大文字小文字と全角半角を無視する設定かを調べる----

                    //----検出条件、を解析する

                    $filterData = $aryVariant['search_filter_data'];

                    if( isset($aryVariant["TCA_PRESERVED"])===false ){
                        $aryVariant["TCA_PRESERVED"] = array();
                    }
                    $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_currentPrint");
                    $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;

                    //----必須チェックなどを事前にしたい場合は、ここで差し替え
                    if( $aryFunctionForOverride !== null ){
                         list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"DTiSFilterCheckValid"),null);
                         unset($tmpBoolKeyExist);
                         if( is_callable($tmpObjFunction01ForOverride) === true ){
                             $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                         }
                         unset($tmpObjFunction01ForOverride);
                    }
                    //必須チェックなどを事前にしたい場合は、ここで差し替え----

                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 900;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    //----バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）
                    if( $objFunction01ForOverride === null ){
                        $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting);
                    }
                    else{
                        $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant);
                    }
                    if( $tmpAryRet[1] !== null ){
                        $intErrorType = $tmpAryRet[1];
                        $aryFxResultErrMsgBody = $tmpAryRet[2];
                        $intErrorPlaceMark = 1000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）----

                    //検出条件、を解析する----

                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 1100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    if( $boolBinaryDistinctOnDTiS === true ){
                        //----全角半角を区別しない、という設定ではない（loadTableで例外的な設定がされている）
                        $boolFocusRet= dbSearchResultNormalize();
                        if( $boolFocusRet === false ){
                            $intErrorType = 602;
                            $intErrorPlaceMark = 1200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        // 全角半角を区別しない、という設定ではない（loadTableで例外的な設定がされている）----
                    }
                    else{
                        //----全角半角を区別しない、という設定（デフォルト）

                        //----DB面で、大文字小文字と全角半角を無視する設定
                        $boolFocusRet= dbSearchResultExpand();
                        if( $boolFocusRet === false ){
                            $intErrorType = 603;
                            $intErrorPlaceMark = 1300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        //DB面で、大文字小文字と全角半角を無視する設定----

                        //全角半角を区別しない、という設定（デフォルト）----
                    }

                    //検出条件が指定された場合----
                }else{
                    //----検出条件が指定されなかった場合
                    $boolBinaryDistinctOnDTiS = false;
                    //検出条件が指定されなかった場合----
                }

                $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

                // ----generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]
                $sql = generateSelectSql2(2, $objTable, $boolBinaryDistinctOnDTiS);
                // generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]----

                //通常モード----
            }
            //SQL作成----

            if( $strOutputFileType == "arraysForJSON" ){
                //----JSON(ストリーム)向けの出力

                $strPrintOrderJsonFormatterId = $strFormatterId;
                //
                $objJsonFormatter = $objTable->getFormatter($strPrintOrderJsonFormatterId);
                //
                if( is_a($objJsonFormatter, "JSONFormatter") === false ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( $aryFunctionForOverride !== null ){
                    list($tmpObjFunction02ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"selectResultFetch"),null);
                    unset($tmpBoolKeyExist);
                    if( is_callable($tmpObjFunction02ForOverride) === true ){
                        $objFunction02ForOverride = $tmpObjFunction02ForOverride;
                    }
                    unset($tmpObjFunction02ForOverride);
                }

                $tmpAryRet = $objJsonFormatter->selectResultFetch($sql,
                                                                  $arrayFileterBody,
                                                                  $objTable,
                                                                  null,
                                                                  $objFunction02ForOverride,
                                                                  $strFormatterId,
                                                                  $filterData,
                                                                  $aryVariant,
                                                                  $arySetting);

                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryFxResultErrMsgBody = $tmpAryRet[2];
                    unset($tmpAryRet);
                    $intErrorPlaceMark = 1500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                $aryTempResult = $objJsonFormatter->format();

                $varRetBody = $aryTempResult[0];
                $aryUploadFile = $aryTempResult[1];

                //JSON(ストリーム)向けの出力----
            }
            else if( $strOutputFileType == "csv" ){
                //----CSV出力
                
                if( $optAllHidden === true ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $strTmpFilename = makeUniqueTempFilename($g['root_dir_path'] . "/temp", "temp_csv_dl" . date("YmdHis", $intUnixTime) . "_" . mt_rand());
                
                $strCsvHeaderStream = "";
                
                $strPrintOrderCsvFormatterId = $strFormatterId;
                
                $objCsvFormatter = $objTable->getFormatter($strPrintOrderCsvFormatterId);
                
                if( is_a($objCsvFormatter, "CSVFormatter") === false ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $objCsvFormatter->getGeneValue("csvFieldRowHide",$refRetKeyExists) !== false ){
                    $objCsvFormatter->setGeneValue("csvFieldRowAdd",true);
                    $objCsvFormatter->setGeneValue("csvRecordShowAdd",false);
                    $strCsvHeaderStream .= $objTable->getPrintFormat($strFormatterId);
                }
                $strCSVOutputFileType = $objCsvFormatter->getGeneValue("outputFileType",$refRetKeyExists);
                if( $strCSVOutputFileType == "SafeCSV" ){
                    $strDLFilename = $objCsvFormatter->makeLocalFileName(".scsv",$intUnixTime);
                }
                else{
                    $strCSVOutputFileType = "NormalCSV";
                    $strDLFilename = $objCsvFormatter->makeLocalFileName(".csv",$intUnixTime);
                }
                if( $strDLFilename === null ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                if( $aryFunctionForOverride !== null ){
                    list($tmpObjFunction03ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"writeToFile"),null);
                    unset($tmpBoolKeyExist);
                    if( is_callable($tmpObjFunction03ForOverride) === true ){
                        $objFunction03ForOverride = $tmpObjFunction03ForOverride;
                    }
                    unset($tmpObjFunction03ForOverride);
                }
                //----暫定ファイルへ本体行を書き込み
                
                //----暫定ファイルの作成
                if( $objCsvFormatter->fileOpen($strTmpFilename) !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 1900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $objCsvFormatter->fileStreamAdd($strCsvHeaderStream) !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 2000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                $tmpAryRet = $objCsvFormatter->writeToFile($sql,
                                                           $arrayFileterBody,
                                                           $objTable,
                                                           $objFunction03ForOverride,
                                                           $strFormatterId,
                                                           $filterData,
                                                           $aryVariant,
                                                           $arySetting);

                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryFxResultErrMsgBody = $tmpAryRet[2];
                    unset($tmpAryRet);
                    $intErrorPlaceMark = 2100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $num_rows = $tmpAryRet[0];

                if( array_key_exists("directSQL",$aryVariant) === true ){
                    //----直で実行するSQLが指定された
                    //直で実行するSQLが指定された----
                }
                else{
                    //----通常モード
                    if( $boolBinaryDistinctOnDTiS === true ){
                    }else{
                        $boolFocusRet = dbSearchResultNormalize();
                        if($boolFocusRet === false){
                            $intErrorType = 500;
                            $intErrorPlaceMark = 2200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 2300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //通常モード----
                }
                
                if( $objCsvFormatter->fileClose() !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 2400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                //暫定ファイルへ本体行を書き込み----

                //暫定ファイルの作成----

                dev_log("output.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                if( $strToAreaType == "toFile" ){
                    $varRetBody = $strTmpFilename;
                }
                else if( $strToAreaType == "toStd" ){
                    if( headers_sent() === true ){
                        $intErrorType = 605;
                        $intErrorPlaceMark = 2500;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    // ----MIMEタイプの設定
                    printHeaderForProvideFileStream($strDLFilename);
                    // MIMEタイプの設定----

                    // 標準出力へ出力
                    echo file_get_contents( $strTmpFilename );

                    // ----テンポラリファイルを削除する
                    unlink("$strTmpFilename");
                    // テンポラリファイルを削除する----

                    // アクセスログへ記録
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-460",array($ACRCM_id, $strOutputFileType, $strCSVOutputFileType, $strDLFilename)));
                }
                else{
                    $intErrorType = 501;
                    $intErrorPlaceMark = 2600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                dev_log("output.after:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                //CSV出力----
            }else{
                // ----デフォルト（EXCELでの出力）

                $strTmpFilename = makeUniqueTempFilename($g['root_dir_path'] . "/temp", "temp_excel_dl" . date("YmdHis", $intUnixTime) . "_" . mt_rand());

                list($intXlsLimit,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','intXlsLimit'),"");
                if( $intXlsLimit == "" ){
                    list($intXlsLimit,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_xls_limit'),null);
                }

                if( $optAllHidden !== true ){
                    if( $strPrintTypeMode === "forDeveloper" || $strPrintTypeMode == "forVisitor" ){
                        $strLinkExcelFormatterIDOnNN = $objTable->getFormatter($strFormatterId)->getGeneValue("linkExcelFormatterIDOnNN",$refRetKeyExists); //NotNormal
                        if( $strLinkExcelFormatterIDOnNN===null && $refRetKeyExists===false ){
                            $strLinkExcelFormatterIDOnNN = $objTable->getGeneObject("linkExcelFormatterIDOnNN",$refRetKeyExists); //NotNormal
                        }
                        if( $strLinkExcelFormatterIDOnNN===null ){
                            $strLinkExcelFormatterIDOnNN="excel";
                        }
                        
                        $objExcelFormatter = $objTable->getFormatter($strLinkExcelFormatterIDOnNN);
                        if( $objExcelFormatter === null ){
                            $intErrorType = 501;
                            $intErrorPlaceMark = 2700;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $strPrintOrderExcelFormatterId = $strLinkExcelFormatterIDOnNN;
                    }
                    else{
                        $strPrintOrderExcelFormatterId = $strFormatterId;
                    }

                    $objExcelFormatter = $objTable->getFormatter($strPrintOrderExcelFormatterId);

                    if( is_a($objExcelFormatter, "ExcelFormatter") === false ){
                        $intErrorType = 501;
                        $intErrorPlaceMark = 2800;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $objExcelFormatter->cashModeAdjust();

                    // ----XLSXファイル名の設定
                    if( $strPrintTypeMode != "forDeveloper" ){
                        $strDLFilename = $objExcelFormatter->makeLocalFileName(".xlsx",$intUnixTime);
                    }
                    else{
                        $strDLFilename = $objExcelFormatter->makeLocalFileName(".xlsx",$intUnixTime);
                    }
                    if( $strDLFilename === null ){
                        $intErrorType = 501;
                        $intErrorPlaceMark = 2900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    // XLSXファイル名の設定----

                    //----出力先を確定
                    if( $strToAreaType == "toFile" ){
                        // ファイルへ出力
                        $objExcelFormatter->setExportFilePath($strTmpFilename);
                    }
                    else if( $strToAreaType == "toStd" ){
                        // 標準出力へ出力
                        $objExcelFormatter->setExportFilePath("php:/"."/output");
                    }
                    else{
                        $intErrorType = 501;
                        $intErrorPlaceMark = 3000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //出力先を確定----

                    if( $boolNoSelectMode === false ){
                        $intFetchCount = 0;

                        $tmpRefBoolSetting = true;
                        $objExcelFormatter->getSheetNameForEditSheet($tmpRefBoolSetting);
                        if( $objExcelFormatter===false ){
                            $intErrorType = 501;
                            $intErrorPlaceMark = 3100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($tmpRefBoolSetting);

                        dev_log("add.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);
                        $objExcelFormatter->workBookCreate();
                        $objExcelFormatter->editHelpWorkSheetAdd();
                        $objExcelFormatter->editWorkSheetHeaderCreate();
                        dev_log("sql.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                        if( $aryFunctionForOverride !== null ){
                            list($tmpObjFunction03ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"selectResultFetch"),null);
                            unset($tmpBoolKeyExist);
                            if( is_callable($tmpObjFunction03ForOverride) === true ){
                                $objFunction03ForOverride = $tmpObjFunction03ForOverride;
                            }
                            unset($tmpObjFunction03ForOverride);
                        }

                        $tmpAryRet = $objExcelFormatter->selectResultFetch($sql,
                                                                           $arrayFileterBody,
                                                                           $objTable,
                                                                           $intXlsLimit,
                                                                           $objFunction03ForOverride,
                                                                           $strFormatterId,
                                                                           $filterData,
                                                                           $aryVariant,
                                                                           $arySetting);

                        if( $tmpAryRet[1] !== null ){
                            $intErrorType = $tmpAryRet[1];
                            $aryFxResultErrMsgBody = $tmpAryRet[2];
                            unset($tmpAryRet);
                            $intErrorPlaceMark = 3200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $num_rows = $tmpAryRet[0];

                        if( array_key_exists("directSQL",$aryVariant) === true ){
                            //----直で実行するSQLが指定された
                            //直で実行するSQLが指定された----
                        }
                        else{
                            //----通常モード
                            if( $boolBinaryDistinctOnDTiS === true ){
                            }else{
                                $boolFocusRet= dbSearchResultNormalize();
                                if($boolFocusRet === false){
                                    $intErrorType = 500;
                                    $intErrorPlaceMark = 3300;
                                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }
                            //
                            foreach($arrayObjColumn as $objColumn){
                                $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                                if( $arrayTmp[0] === false ){
                                    $intErrorType = $arrayTmp[1];
                                    $intErrorPlaceMark = 3400;
                                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }
                            //通常モード----
                        }
                        $objExcelFormatter->editWorkSheetRecordAdd();
                        $objExcelFormatter->editWorkSheetTailerFix();
                        // エクセル出力,全件ダウンロードの場合
                        $objExcelFormatter->ValidationDataWorkSheetAdd(); // カラム列範囲ヴァリデーション
                        dev_log("add.after:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);
                    }
                    else{
                        $num_rows = 0;

                        if( $strPrintTypeMode === "forDeveloper" || $strPrintTypeMode == "forVisitor" ){
                            //----特殊エクセルのダウンロードの場合
                            $objExcelFormatter->setGeneValue("minorPrintTypeMode",$strPrintTypeMode);
                            $objExcelFormatter->setPrintTargetListFormatterID($strFormatterId);
                            //特殊エクセルのダウンロードの場合----
                        }
                        $objExcelFormatter->workBookCreate();
                        $objExcelFormatter->editHelpWorkSheetAdd();
                        $objExcelFormatter->editWorkSheetHeaderCreate();
                        $objExcelFormatter->editWorkSheetRecordAdd();
                        $objExcelFormatter->editWorkSheetTailerFix();

                        // 新規登録の場合
                        $objExcelFormatter->ValidationDataWorkSheetTailerFix();// カラム列範囲ヴァリデーション
                    }
                }
                else{
                    $num_rows = 0;
                }

                $boolFileDumpExecute = true;

                if( $intXlsLimit !== null && $intXlsLimit < $num_rows ){
                    //----ダウンロード制限行数を超えた
                    if( $strPrintTypeMode != "forDeveloper" && $strPrintTypeMode != "forVisitor" ){
                        //----通常のエクセルのダウンロードの場合
                        $boolFileDumpExecute = false;
                        //通常のエクセルのダウンロードの場合----
                    }
                    else{
                        //----開発者用エクセルのダウンロードの場合
                        //開発者用エクセルのダウンロードの場合----
                    }
                    //ダウンロード制限行数を超えた----
                }

                if( headers_sent() === true ){
                    $intErrorType = 607;
                    $intErrorPlaceMark = 3500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( $boolFileDumpExecute === false ){
                    //----ダウンロード制限行数を超えた
                    $intErrorType = 301;
                    $intErrorPlaceMark = 3600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //ダウンロード制限行数を超えた----
                }
                else{
                    // ----デフォルト（EXCELでの出力）

                    if( $strToAreaType == "toStd" ){
                        // ----MIMEタイプの設定
                        printHeaderForProvideFileStream($strDLFilename,"",null,array("ContentExcelType"=>"EXCEL2007"));
                        // MIMEタイプの設定----
                    }

                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);

                    //----このメソッド内で、出力する。
                    $objTable->getPrintFormat($strPrintOrderExcelFormatterId);
                    //このメソッド内で、出力する。----

                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);

                    if( $strToAreaType == "toStd" ){
                        // アクセスログへ記録
                        //"SUCCESS, DUMP TO FILE. [MENU:[｛｝] PRINTMODE:[｛｝] PRINTTYPE:[｛｝] FILENAME[｛｝]]. ";
                        web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-461",array($ACRCM_id, $strOutputFileType, $strPrintTypeMode, $strDLFilename)));
                    }
                    else{
                        $varRetBody = $strTmpFilename;
                    }
                    // デフォルト（EXCELでの出力）----
                }
            }
        }
        catch (Exception $e){
            //----エラー発生時

            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);

            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 1: // 表示権限がない
                    // 表示権限がありません。
                    $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-311");
                    break;
                case 2: // バリデーションエラー系
                    // バリデーションエラーが発生しました。
                    $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-312");
                    break;
                case 301: // ダウンロード制限行数を超えた
                    $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-313",array($num_rows, $intXlsLimit));
                    // "WARNING:DETAIL:(DUMP TO FILE. MENU:[｛｝] TYPE EXCEL) DOWNLOAD LIMIT OVER. ";
                    $strErrMsgBodyToWAL = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-301",$ACRCM_id);
                    break;
                default: // システムエラーが発生しました。
                    $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001",$intErrorType);
                    // ERROR:UNEXPECTED, DETAIL:(DUMP TO FILE. MENU:[｛｝] PRINTMODE:[｛｝] ERROR[｛｝]) 
                    $strErrMsgBodyToWAL = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-303",array($ACRCM_id, $strOutputFileType, $intErrorType));
                    break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----

            $aryErrMsgBody[] = $strErrMsgBodyToHtmlUI;
            if( 0 < count($aryFxResultErrMsgBody) ){
                $aryErrMsgBody[] = $aryFxResultErrMsgBody;
            }

            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイザー向けメッセージを作成
                //ロードテーブルカスタマイザー向けメッセージを作成----
            }

            if( $strToAreaType == "toStd" ){
                header("Content-Type: text/html; charset=UTF-8");
                print 
<<< EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<script language="JavaScript">
window.onload = function(){
    var strMsg = document.getElementById('msgbody').innerHTML;
    alert(strMsg);
}
</script>
<div id="msgbody" style="display:none">
{$strErrMsgBodyToHtmlUI}
</div>
</body>
</html>
EOD;
            }

            // アクセスログへ記録
            if( 0 < strlen($strErrMsgBodyToWAL) ) web_log($strErrMsgBodyToWAL);

            //エラー発生時----
        }
        //----大量行のダウンロードに備えて、タイムリミットを「30」に戻す
        //set_time_limit(30);
        //大量行のダウンロードに備えて、タイムリミットを「30」に戻す----

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        return array($varRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg, $aryUploadFile);
    }
    
    //webroot(03,04,07)----
?>