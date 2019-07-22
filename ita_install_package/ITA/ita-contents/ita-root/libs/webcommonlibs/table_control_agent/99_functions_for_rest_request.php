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

    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/08_dumpToFile.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/07_TableIUbyFIle.php");

    function ReSTCommandDeal($strCalledRestVer,$strCommand,$objJSONOfReceptedData,$objTable){
        global $g;
        $strFxName = __FUNCTION__;

        if(null === $objTable){
            $aryForResultData = array(array('ResultStatusCode'=>404,
                                            'ResultData'=>"[loadTable.php] does not exist."
                                            ),
                                      "[loadTable.php] does not exist."
                                      );
        }
        else{
            if( $strCommand == "INFO" || $strCommand == "GET" || $strCommand == "FILTER" ){
                //----GETまたはFILTER
                $aryForResultData = ReSTCommandFilterExecute($strCommand,$objJSONOfReceptedData,$objTable);
                //GETまたはFILTER----
            }
            else if( $strCommand == "EDIT" ){
                //----EDIT
                $aryForResultData = ReSTCommandEditExecute($strCommand,$objJSONOfReceptedData,$objTable);
                //EDIT----
            }
            else{
                $aryForResultData = array(array('ResultStatusCode'=>500,
                                                'ResultData'=>$g['requestByREST']['preResponsContents']['errorInfo']
                                                )
                                          );
            }
        }

        $intResultStatusCode = $aryForResultData[0]['ResultStatusCode'];

        $tmpAryForResultData    = $aryForResultData[0]['ResultData'];
        $objJSONOfResultData = @json_encode($tmpAryForResultData);
        unset($tmpAryForResultData);

        if( $aryForResultData[1] !== null ){
            if( $aryForResultData[1] < 500 ){
                //----本作業自体までに、想定されたエラーが発生した

                // FUNCTION:｛｝,RESULT:WARINIG [RESPONSE：｛｝]
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-906001",array($strFxName,$intResultStatusCode)));

                //本作業自体までに、想定されたエラーが発生した----
            }
            else{
                //----本作業自体まで、システムエラー系(500以上)が発生した

                // FUNCTION:｛｝,RESULT:ERROR [RESPONSE：｛｝]
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-906002",array($strFxName,$intResultStatusCode)));

                //本作業自体まで、システムエラー系(500以上)が発生した----
            }
        }
        else{
            //----本作業自体までは、問題なく通過した

            // FUNCTION:｛｝,RESULT:SUCCESS [RESPONSE：｛｝]
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-906003",array($strFxName,$intResultStatusCode)));

            //本作業自体までは、問題なく通過した----
        }

        header('Content-Type: application/json; charset=utf-8', true, $intResultStatusCode);
        header('REST-API-Version: '.$strCalledRestVer);

        //念のため[$intResultStatusCode]で上書き----
        //----JSON形式で返す
        exit($objJSONOfResultData);
        //JSON形式で返す----
    }

    function ReSTCommandFilterExecute($strCommand, $objJSONOfReceptedData, $objTable){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;

        $arrayRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $intResultStatusCode = null;
        $aryForResultData = array();
        $aryPreErrorData = null;

        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            $tmpArrayReqHeaderRaw = getallheaders();
            list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($tmpArrayReqHeaderRaw,array('Formatter'),"json");
            unset($tmpArrayReqHeaderRaw);

            $tmpArrayVariant = array();
            $tmpArraySetting = array();

            $tmpArraySetting = array('system_function_control'=>array('DTiSFilterCheckValid'=>array('HiddenVars'=>array('DecodeOfSelectTagStringEscape'=>false))));

            if( is_array($objJSONOfReceptedData) !== true ){
                $tmpAryFilterData = array();
            }
            else{
                $tmpAryFilterData = $objJSONOfReceptedData;
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "ListFormatter") !== true ){
                // ----リストフォーマッタクラスではない
                $intErrorPlaceMark = 100;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // リストフォーマッタクラスではない----
            }

            if( is_a($objListFormatter, "JSONFormatter") !== true ){
                $intErrorPlaceMark = 200;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            if( $strCommand == "GET" || $strCommand == "FILTER" ){
                //----全部一覧または一部一覧の取得
                $aryLabelListOfOpendColumn = $objListFormatter->getLabelListOfOpendColumn(true);

                $aryFilterData = array();
                foreach($tmpAryFilterData as $tmpIntKey=>$tmpVarValue01){
                    if( array_key_exists($tmpIntKey, $aryLabelListOfOpendColumn) ){
                        //----Prefix(IDSOP)
                        $strKeyPrefix = $aryLabelListOfOpendColumn[$tmpIntKey];
                        //Prefix(IDSOP)----

                        //----リッチフィルタ
                        list($tmpAryRich,$boolKeyExists) = isSetInArrayNestThenAssign($tmpVarValue01,array('LIST'),null);
                        if( $boolKeyExists === true ){
                            if( is_array($aryFilterData) === true ){
                                $tmpKeyBody = $strKeyPrefix."_RF";
                                $tmpAryToFilter02 = array();
                                foreach($tmpAryRich as $tmpVarValue02){
                                    switch( gettype($tmpVarValue02) ){
                                        case "integer":
                                        case "string":
                                        case "NULL":
                                            if( strlen($tmpVarValue02) == 0 ){
                                                $tmpAryToFilter02[] = "";
                                            }
                                            else{
                                                $tmpAryToFilter02[] = $tmpVarValue02;
                                            }
                                            break;
                                        default:
                                            $intResultStatusCode = 400;
                                            $intErrorPlaceMark = 300;
                                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                            break;
                                    }
                                }
                                $aryFilterData[$tmpKeyBody] = $tmpAryToFilter02;
                                unset($tmpKeyBody);
                            }
                            else{
                                $intResultStatusCode = 400;
                                $intErrorPlaceMark = 400;
                                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                        }
                        //リッチフィルタ----

                        //----通常(範囲型)
                        list($tmpAryNormal,$boolKeyExists) = isSetInArrayNestThenAssign($tmpVarValue01,array('RANGE'),null);
                        if( $boolKeyExists === true ){
                            list($strStart,$boolKeyExists) = isSetInArrayNestThenAssign($tmpAryNormal,array('START'),"");
                            if( $boolKeyExists === true ){
                                $tmpKeyBody = $strKeyPrefix."__S";
                                switch( gettype($strStart) ){
                                    case "integer":
                                    case "string":
                                        $aryFilterData[$tmpKeyBody] = array($strStart);
                                        break;
                                    default:
                                        $intResultStatusCode = 400;
                                        $intErrorPlaceMark = 500;
                                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        break;
                                }
                                unset($tmpKeyBody);
                            }
                            list($strEnded,$boolKeyExists) = isSetInArrayNestThenAssign($tmpAryNormal,array('END'),"");
                            if( $boolKeyExists === true ){
                                $tmpKeyBody = $strKeyPrefix."__E";
                                switch( gettype($strEnded) ){
                                    case "integer":
                                    case "string":
                                        $aryFilterData[$tmpKeyBody] = array($strEnded);
                                        break;
                                    default:
                                        $intResultStatusCode = 400;
                                        $intErrorPlaceMark = 600;
                                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        break;
                                }
                                unset($tmpKeyBody);
                            }
                        }
                        //通常(範囲型)----

                        //----通常(そのほか)
                        list($tmpStrNormal,$boolKeyExists) = isSetInArrayNestThenAssign($tmpVarValue01,array('NORMAL'),null);
                        if( $boolKeyExists === true ){
                            $tmpKeyBody = $strKeyPrefix;
                            switch( gettype($tmpStrNormal) ){
                                case "integer":
                                case "string":
                                    $aryFilterData[$tmpKeyBody] = array($tmpStrNormal);
                                    break;
                                default:
                                    $intResultStatusCode = 400;
                                    $intErrorPlaceMark = 700;
                                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    break;
                            }
                            unset($tmpKeyBody);
                        }
                        //通常(そのほか)----
                    }
                }

                $tmpArrayVariant['search_filter_data'] = $aryFilterData;
                $tmpArrayVariant['dumpDataFromTable'] = array('vars'=>array('strOutputFileType'=>'arraysForJSON',
                                                                            'strFormatterId'=>$strFormatterId
                                                                            )
                                                              );

                $aryResultOfDump = dumpDataFromTable(array('to_area_type'=>'toReturn'), $objTable, $tmpArrayVariant, $tmpArraySetting);
                $aryForResultData = $aryResultOfDump[0];
                if( $aryResultOfDump[1] !== null ){
                    //----エラー発生
                    $aryPreErrorData = $aryResultOfDump[2];
                    switch($aryResultOfDump[1]){
                        case 1: //権限がない
                            $intResultStatusCode = 403;
                            break;
                        case 2: //バリデーションエラー
                            $intResultStatusCode = 400;
                            break;
                        default:
                            $intResultStatusCode = 500;
                            break;
                    }

                    $intErrorPlaceMark = 800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エラー発生----
                }
                else{
                    //----正常終了（リスト全体[ヘッダーとレコード]とレコード行を返す）
                    $intResultStatusCode = 200;
                    $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
                    $aryForResultData['resultdata'] = array('CONTENTS'=>array('RECORD_LENGTH'=>count($aryResultOfDump[0]) - 1,
                                                                              'BODY'=>$aryResultOfDump[0],
                                                                              'UPLOAD_FILE'=>$aryResultOfDump[4],
                                                                             )
                                                           );
                }
                //全部一覧または一部一覧の取得----
            }
            else{
                //----カラム対応情報の取得
                $aryLabelListOfOpendColumn = $objListFormatter->getLabelListOfOpendColumn(false);
                if( is_array($aryLabelListOfOpendColumn) === true ){
                    $intResultStatusCode = 200;
                    $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
                    $aryForResultData['resultdata'] = array('CONTENTS'=>array('INFO'=>$aryLabelListOfOpendColumn));
                }
                else{
                    $intErrorPlaceMark = 900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                } 
                //カラム対応情報の取得----
            }
            
            if( headers_sent() === true ){
                $intErrorType = 900;
                $intErrorPlaceMark = 1000;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        catch (Exception $e){
            $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];

            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            if( $intResultStatusCode === null ) $intResultStatusCode = 500;
            if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
            if( 500 <= $intErrorType ) web_log($tmpErrMsgBody);
        }
        $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                              'ResultData'=>$aryForResultData);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
    }

    function ReSTCommandEditExecute($strCommand,$objJSONOfReceptedData,$objTable){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;

        $arrayRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $intResultStatusCode = null;
        $aryForResultData = array();
        $aryPreErrorData = null;
        $semiNormalFlg = null;
        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            $tmpArrayReqHeaderRaw = getallheaders();
            list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($tmpArrayReqHeaderRaw,array('Formatter'),"all_dump_table");
            unset($tmpArrayReqHeaderRaw);

            if( count($objJSONOfReceptedData) == 0 ){
                $intResultStatusCode = 400;
                $intErrorPlaceMark = 100;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //----一時ファイルを保存する
            $tmpStrTempFilename = makeUniqueTempFilename("{$g['root_dir_path']}/temp","temp_json_ul");
            $tmpBoolFilePut = @file_put_contents($tmpStrTempFilename,@json_encode($objJSONOfReceptedData));
            if( $tmpBoolFilePut === false ){
                $intErrorPlaceMark = 200;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($tmpBoolFilePut);
            //一時ファイルを保存する----

            $tmpArrayVariant = array();
            $tmpArraySetting = array();

            $tmpArrayVariant['objTable'] = $objTable;

            $aryResultOfTableIUD = tableIUDByQMFile($tmpStrTempFilename,null,2,$strFormatterId,$tmpArrayVariant,$tmpArraySetting);
            $aryNormalResultOfEditExecute = $aryResultOfTableIUD[4];
            $aryRawResultOfEditExecute = $aryResultOfTableIUD[5];
            if( $aryResultOfTableIUD[1] !== null ){
                //----エラー発生
                switch($aryResultOfTableIUD[1]){
                    case 1: // 権限がない
                        $intResultStatusCode = 403;
                        break;
                    case 371: // JSON固有サイズover。
                    case 372: // フォーマットが一致しない。
                    case 373: // 不正なフォーマット。
                        $intResultStatusCode = 400;
                        break;
                    case 374: // アップロードファイルが不正なフォーマット。
                        $intResultStatusCode = 400;
                        $semiNormalFlg = 1;
                        break;
                    default:
                        $intResultStatusCode = 500;
                        break;
                }
                if("" != $aryResultOfTableIUD[3]){
                    $aryPreErrorData = $aryResultOfTableIUD[3];
                }
                else{
                    $aryPreErrorData = getMessageFromResultOfTableIUDByQMFile($aryResultOfTableIUD[1],0);
                }
                $intErrorPlaceMark = 300;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //エラー発生----
            }
            else{
                //----正常終了
                $intResultStatusCode = 200;
                $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
                $aryForResultData['resultdata'] = array('LIST'=>array('NORMAL'=>$aryNormalResultOfEditExecute,
                                                                      'RAW'=>$aryRawResultOfEditExecute
                                                                      )
                                                        );
                //正常終了----
            }
            unset($tmpStrTempFilename);

            if( headers_sent() === true ){
                $intErrorType = 900;
                $intErrorPlaceMark = 400;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        catch (Exception $e){
            $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];

            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            if( $intResultStatusCode === null ) $intResultStatusCode = 500;
            if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
            if( $semiNormalFlg !== null ) $aryForResultData['SemiNormal'] = $semiNormalFlg;
            if( 500 <= $intErrorType ) web_log($tmpErrMsgBody);
        }
        $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                              'ResultData'=>$aryForResultData);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
    }
?>