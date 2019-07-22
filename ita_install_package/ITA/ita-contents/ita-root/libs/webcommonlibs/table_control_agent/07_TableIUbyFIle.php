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
    //  【その他】
    //    ・文字列リテラルは、原則ダブルコーテーションでラップする
    //    ・連想配列の鍵は、原則シングルコーテーションでラップする
    //
    //////////////////////////////////////////////////////////////////////

    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/03_registerTable.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/04_updateTable.php");

    function noRetQMFileAccessAgent($objTable, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        if(isset($_GET['req_errlog_target_name'])){
            $req_errlog_target_name = $_GET['req_errlog_target_name'];
            printUploadLog($req_errlog_target_name, $aryVariant, $arySetting);
        }
        else{
            //----関数内で処理が終了(exit)する
            noRetTableIUDByQMFileCallAgent($objTable, $aryVariant,$arySetting);
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        exit();
    }
    
    function noRetTableIUDByQMFileCallAgent($objTable, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;

        $pblStrFileAllTailMarks = "";

        //----受付拡張子(エクセル)の設定
        $pblStrExcelFileTailMarks = ".xlsx,.xlsm";
        //受付拡張子(エクセル)の設定----

        //----受付拡張子(CSV系)の設定
        $pblStrCsvFileTailMarks = ".csv,.scsv";
        //受付拡張子(CSV系)の設定----

        $ret_str = '';
        $intErrorStatus = 0;
        $modeFileCh = -1;
        $upOrgFilename = '';

        $ACRCM_id = "UNKNOWN";

        $refRetKeyExists = false;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");
            if( $boolKeyExists === false ){
                list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
            }
            //メニューIDの取得----

            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorStatus = 501;
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }

            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorStatus = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }

            $strFormatterId = "";
            if( array_key_exists("FORMATTER_ID",$_POST) === true ){
                $strFormatterId = $_POST['FORMATTER_ID'];
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "QMFileSendAreaFormatter") !== true ){
                // ----QMFileSendAreaFormatterクラスではない
                $intErrorStatus = 501;
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // QMFileSendAreaFormatterクラスではない----
            }
            //テーブル設定の調査----

            $varErrorOfFileupload = $_FILES['file']['error'];
            if( $varErrorOfFileupload != 0 ){
                //----1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過
                $intErrorStatus = 201;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過----
            }
            else{
                $upTmpFileFullname = $_FILES['file']['tmp_name'];
                $upOrgFilename = $_FILES['file']['name'];
                if( is_uploaded_file($upTmpFileFullname) ){
                    dev_log("[{$upTmpFileFullname}] is uploaded_file.", $intControlDebugLevel01);
                }
                else{
                    $intErrorStatus = 202;
                    throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            if( $modeFileCh == -1 ){
                $flag_ExcelHidden = $objTable->getFormatter($strFormatterId)->getGeneValue("linkExcelHidden",$refRetKeyExists);
                if( $flag_ExcelHidden===null && $refRetKeyExists===false ){
                    $flag_ExcelHidden = $objTable->getGeneObject("linkExcelHidden",$refRetKeyExists);
                }
                if( $flag_ExcelHidden !== true ){
                    //----全件DL領域で、エクセルを無条件で隠す設定ではない場合
                    $pblStrFileAllTailMarks.=$pblStrExcelFileTailMarks;
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if(mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0){
                            $modeFileCh = 0;
                            break;
                        }
                    }
                    //全件DL領域で、エクセルを無条件で隠す設定ではない場合----
                }
                else{
                    //----全件DL領域で、エクセルを無条件で隠す設定の場合
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if(mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0){
                            $intErrorStatus = 203;
                            throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //全件DL領域で、エクセルを無条件で隠す設定の場合----
                }
            }

            if( $modeFileCh == -1 ){
                //----ロードテーブルからの設定を取得
                $flag_CSVShow = $objTable->getFormatter($strFormatterId)->getGeneValue("linkCSVFormShow",$refRetKeyExists);
                if( $flag_CSVShow===null && $refRetKeyExists===false ){
                    $flag_CSVShow = $objTable->getGeneObject("linkCSVFormShow",$refRetKeyExists);
                }
                //ロードテーブルからの設定を取得----

                if( $flag_CSVShow !== false ){
                    //----無条件でCSVを隠す、という設定ではない
                    $rowLength = countTableRowLength($objTable, $aryVariant);
                    $intXlsLimit = isset($g['menu_xls_limit'])?$g['menu_xls_limit']:null;

                    if( $intXlsLimit !== null && $intXlsLimit < $rowLength ){
                        $flag_CSVShow = true;
                    }
                    //無条件でCSVを隠す、という設定ではない----
                }

                if( $flag_CSVShow === true ){
                    //----CSVが隠されていない場合
                    if( $pblStrFileAllTailMarks != "" ){
                        $pblStrFileAllTailMarks.=",";
                    }
                    $pblStrFileAllTailMarks.=$pblStrCsvFileTailMarks;
                    foreach(explode(",",$pblStrCsvFileTailMarks) as $tailFileMark){
                        if( mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0 ){
                            $modeFileCh = 1;
                            break;
                        }
                    }
                    //CSVが隠されていない場合----
                }
                else{
                    //----CSVが隠されている場合
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if( mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0 ){
                            $intErrorStatus = 204;
                            throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //CSVが隠されている場合----
                }
            }
            
            if( $modeFileCh == -1 ){
                $intErrorStatus = 205;
                throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $aryVariant['objTable'] = $objTable;
            $aryVariant['tableIUDByQMFile']  = array('vars'=>array('strUpTmpFileFullname'=>$upTmpFileFullname,'strOrgFileNameOfUpTmpFile'=>$upOrgFilename));
            $aryRetBody = tableIUDByQMFile(null, null, $modeFileCh, $strFormatterId, $aryVariant);
            $ret_str = $aryRetBody[0];
            $intErrorStatus = $aryRetBody[1];
            
            if( $intErrorStatus !== null ){
                throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $intErrorStatus = 0;
            
            // WebAPIログへサクセスを記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-440",array($ACRCM_id,$upOrgFilename)));
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorStatus){
                case 201 :
                    switch($varErrorOfFileupload){
                        case 1  : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-251");break;
                        case 2  : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-252");break; 
                        case 3  : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-253");break;
                        case 4  : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-254");break;
                        default : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
                    }
                    break;
                case 202 : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1001");break;
                case 203 : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1002");break;
                case 204 : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1003");break;
                case 205 : $ret_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1004",$pblStrFileAllTailMarks);break; //受付外範囲の拡張子

                default : $ret_str .= getMessageFromResultOfTableIUDByQMFile($intErrorStatus,0);break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイズ向けメッセージを作成
                $tmp_DevStr = "";
                switch($intErrorStatus){
                    case 201 :
                        switch($varErrorOfFileupload){
                            case 6  : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-255");break;
                            case 7  : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-256");break;
                            case 8  : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-257");break;
                            default : break;
                        }
                        break;
                    case 202 : case 203 : case 204 : break;

                    default : $tmp_DevStr = getMessageFromResultOfTableIUDByQMFile($intErrorStatus,1);break;
                }
                if( 0 < strlen($tmp_DevStr) ) dev_log($tmp_DevStr, $intControlDebugLevel01);
                unset($tmp_DevStr);
                //ロードテーブルカスタマイズ向けメッセージを作成----
            }
            
            // WebAPIログへエラーを記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-271",array($ACRCM_id,$upOrgFilename,$intErrorStatus)));
        }

        $response = array();
        $response['text'] = nl2br($ret_str);
        $response['error'] = $intErrorStatus;

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        exit("<div>".htmlspecialchars(json_encode($response))."</div>");
    }

    function getMessageFromResultOfTableIUDByQMFile($intErrorStatus,$intMode=0){
        global $g;

        $retStrBody = "";

        if( $intMode == 0 ){
            switch($intErrorStatus){
                case   1 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1117");break; // メンテナンス権限がありません。

                case 351 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1112");break; // エクセル固有サイズover 旧(812)
                case 352 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1107");break; // エクセルのシート名が不正 旧(807)
                case 353 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1108");break; // 列が一致しません。最新のフォーマットを使用してください。 旧(808)

                case 361 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1113");break; // CSV系固有サイズover 旧(813)
                case 362 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1103");break; // ファイル内容が、ファイル拡張子（scsv）の形式と合致しません。 旧(803)
                case 363 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1104");break; // ファイル拡張子が（scsv）の場合の、CSV系ファイルでのファイルアップロード編集が許可されていません。旧(804)
                case 364 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1105");break; // ファイル拡張子が（scsv）の場合の、CSV系ファイルでのファイルアップロード編集が許可されていません。旧(805)
                case 365 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1106");break; // CSVファイルでのファイルアップロード編集が許可されていません。旧(806)
                case 366 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1109");break; // アップロード用のCSV系ファイルではありません。旧(809)
                case 367 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1110");break; // フォーマットが一致しません。最新版のCSV系ファイルを使用してください。旧(810)
                case 368 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1111");break; // 不正なフォーマットです。旧(811)

                case 371 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1114");break; // JSON固有サイズover
                case 372 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1115");break; // フォーマットが一致しません。最新版のJSONファイルを使用してください。
                case 373 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1116");break; // 不正なフォーマットです。

                case 801 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1101");break; // ファイルがアップロードされていません。
                case 802 : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-1102");break; // ファイルのアップロードでエラーが発生しました。

                default : $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
            }
        }
        else if( $intMode == 1 ){
            switch($intErrorStatus){
                case 351 : case 352 : case 353 : break;
                case 361 : case 362 : case 363 : case 364 : case 365 : case 366 : case 367 : case 368 : break;
                case 371 : case 372 : case 373 : break;

                case 801 : case 802 : break;
            }
        }
        return $retStrBody;
    }

    function tableIUDByQMFile($strIUDSourceFullname, $varLoadTableSetting=null, $intModeFileCh=0, $strQMFileSendAreaFormatterId, &$aryVariant=array(), &$arySetting=array()){
        global $g;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        //
        // return値
        $strRetStrBody = "";
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRawResultOfEditExecute = array();
        $aryNormalResultOfEditExecute = array();
        //
        $root_dir_path = $g['root_dir_path'];

        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $varTrzStart = null;
        $varCommit   = null;
        $varRollBack = null;
        $varTrzExit  = null;

        $row_id_info = '';

        $aryVariant["TABLE_IUD_SOURCE"] = "queryMaterialFile";

        $strHtmlRowDelimiter = "\n";
        $strFileRowDelimiter = "\r\n";

        //----デフォルトのファイルアップロード先
        $editSourceDir = "{$root_dir_path}/logs/update_by_file";
        $editErrorLogDir = "{$root_dir_path}/temp/update_by_file_error";
        //デフォルトのファイルアップロード先----
        $refRetKeyExists = false;
        $uploadFiles = array();
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        //----各サーバー管理者の任意変更先がある場合は、ファイルアップロード先を変更
        if(isset($g['editByFileLogDir'])===true){
            if(file_exists($g['editByFileLogDir'])===true){
                if(is_dir($g['editByFileLogDir'])===true){
                    $editSourceDir = $g['editByFileLogDir'];
                }
            }
        }
        //各サーバー管理者の任意変更先がある場合は、ファイルアップロード先を変更----

        //----大量行のアップロードに備えて、タイムリミット「なし」を原則とする
        //set_time_limit(0);
        //大量行のアップロードに備えて、タイムリミット「なし」を原則とする----
        try{
            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTUP_PRIVILEGE'),null);
            if( $strPrivilege == "" ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }
            switch($strPrivilege){
                case "1":
                    break;
                case "2":
                default:
                    // ----0は権限がないので出力しない
                    $intErrorType = 1;
                    $intErrorPlaceMark = 100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
                    // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 150;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
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
                $objTable = loadTable($varLoadTableSetting);
            }
            if( gettype($objTable) != "object" ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 200;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 300;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            
            if( is_string($strQMFileSendAreaFormatterId) !== true ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 400;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            $objQMFSALFormatter = $objTable->getFormatter($strQMFileSendAreaFormatterId);
            if( $objQMFSALFormatter === null ){
                // ----存在しないフォーマッタ----
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 500;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 存在しないフォーマッタ----
            }
            if( is_a($objQMFSALFormatter, "QMFileSendAreaFormatter") !== true ){
                // ----CurrentTableFormatterクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 600;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // CurrentTableFormatterクラスではない----
            }
            switch($intModeFileCh){
                case 0:
                    $strModeMark = "excel";
                    break;
                case 1:
                    $strModeMark = "csv";
                    break;
                case 2:
                    $strModeMark = "json";
                    break;
                default:
                    // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 100; 
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }

            $unixStartTimeStamp = $g['request_time'];
            $strLogTimeStamp = date("YmdHis", $unixStartTimeStamp);

            if( 0 == strlen($strIUDSourceFullname) ){
                list($strUpTmpFileFullname,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('tableIUDByQMFile','vars','strUpTmpFileFullname'),"");
                if( $boolKeyExists === false || 0 == strlen($strUpTmpFileFullname) ){
                    // 許容されない引数不足(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                list($strOrgFileNameOfUpTmpFile,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('tableIUDByQMFile','vars','strOrgFileNameOfUpTmpFile'),"");
                if( $boolKeyExists === false || 0 == strlen($strOrgFileNameOfUpTmpFile) ){
                    // 許容されない引数不足(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( file_exists($strUpTmpFileFullname) == false ){
                    //----アップロードされて作成された一時ファイルが存在しない
                    $intErrorType = 801;
                    $intErrorPlaceMark = 900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //アップロードされて作成された一時ファイルが存在しない----
                }

                $strFileReceptUniqueNumber = $strModeMark."_".$strLogTimeStamp."_".basename($strUpTmpFileFullname);
                $strMovedFileFullname = $editSourceDir."/".$strFileReceptUniqueNumber.".log";

                if( move_uploaded_file($strUpTmpFileFullname, $strMovedFileFullname) === false ){
                    //----ファイルの移動に失敗した
                    $intErrorType = 802;
                    $intErrorPlaceMark = 1000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //ファイルの移動に失敗した----
                }
                else{
                    $strIUDSourceFullname = $strMovedFileFullname;
                }
            }
            else{
                if( file_exists($strIUDSourceFullname) == false ){
                    //----指定されたファイルが存在しなかった
                    // 許容されない引数内容(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 1100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //指定されたファイルが存在しなかった----
                }
                $strOrgFileNameOfUpTmpFile = basename($strIUDSourceFullname);
                $strFileReceptUniqueNumber = $strModeMark."_".$strLogTimeStamp."_".$strOrgFileNameOfUpTmpFile;
            }

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $dlcOrderMode = 1;
                //
                $refRetKeyExists = false;
                $strLinkFormatterId = $objQMFSALFormatter->getGeneValue("linkExcelFormatterID",$refRetKeyExists);
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
                }
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = "excel";
                }
                //
                if( $strLinkFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセル用のフォーマットIDがnullだった----
                }
                $objListFormatter = $objTable->getFormatter($strLinkFormatterId);
                if( $objListFormatter === null ){
                    //----存在しないフォーマッタ
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1300;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //存在しないフォーマッタ----
                }
                if( is_a($objListFormatter, "ExcelFormatter") !== true ){
                    //----エクセルフォーマッタ系ではなかった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセルフォーマッタ系ではなかった----
                }

                $intTempFilesize = filesize($strIUDSourceFullname);
                $intMaxFileSize = $objQMFSALFormatter->getGeneValue("linkExcelMaxFileSize",$refRetKeyExists);
                if( $intMaxFileSize === null && $refRetKeyExists===false ){
                    $intMaxFileSize = $objTable->getGeneObject("linkExcelMaxFileSize",$refRetKeyExists);
                }
                if( $intMaxFileSize === null || is_int($intMaxFileSize) === false ){
                    $intMaxFileSize = 10*1024*1024;
                }else{
                    if( $intMaxFileSize < 0 ){
                        $intMaxFileSize = 10*1024*1024;
                    }
                }

                if( $intMaxFileSize < $intTempFilesize ){
                    //----許容されたサイズ以上のファイルがアップロードされた
                    $intErrorType = 351;
                    $intErrorPlaceMark = 1500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //許容されたサイズ以上のファイルがアップロードされた----
                }

                $objListFormatter->cashModeAdjust();

                $objReader = PHPExcel_IOFactory::createReader("Excel2007");
                $objWorkBook = $objReader->load($strIUDSourceFullname);
                $objWorkBook->setActiveSheetIndex(0);
                //
                $expAddBody01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-280");

                $output_logfile_prefix = "tableIUDByExcel_exec_";
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード
                $dlcOrderMode = 2;
                //
                $strLinkFormatterId = $objQMFSALFormatter->getGeneValue("linkCSVFormatterID",$refRetKeyExists);
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = $objTable->getGeneObject("linkCSVFormatterID", $refRetKeyExists);
                }
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = "csv";
                }
                //
                if( $strLinkFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセル用のフォーマットIDがnullだった----
                }
                $objListFormatter = $objTable->getFormatter($strLinkFormatterId);
                if( $objListFormatter === null ){
                    //----存在しないフォーマッタ
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //存在しないフォーマッタ----
                }
                if( is_a($objListFormatter, "CSVFormatter") !== true ){
                    //----CSVフォーマッタ系ではなかった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //CSVフォーマッタ系ではなかった----
                }
                //
                $intTempFilesize = filesize($strIUDSourceFullname);
                $intMaxFileSize = $objQMFSALFormatter->getGeneValue("linkCSVMaxFileSize",$refRetKeyExists);
                if( $intMaxFileSize === null && $refRetKeyExists === false ){
                    $intMaxFileSize = $objTable->getGeneObject("linkCSVMaxFileSize",$refRetKeyExists);
                }
                if( $intMaxFileSize === null || is_int($intMaxFileSize) === false ){
                    $intMaxFileSize = 20*1024*1024;
                }else{
                    if( $intMaxFileSize < 0 ){
                        $intMaxFileSize = 20*1024*1024;
                    }
                }
                //
                if( $intMaxFileSize  < $intTempFilesize ){
                    //----許容されたサイズ以上のファイルがアップロードされた
                    $intErrorType = 361;
                    $intErrorPlaceMark = 1900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //許容されたサイズ以上のファイルがアップロードされた----
                }
                //
                $strOutputFileType = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("outputFileType",$refRetKeyExists);
                //
                $boolCheckScsvType = false;
                if( $strOutputFileType == "SafeCSV" ){
                    //----ダウンロードタイプがSafeCSV形式の場合
                    if( preg_match('/\.scsv$/',$strOrgFileNameOfUpTmpFile) === 1 ){
                        $boolSafeSCSV2 = false;
                        //
                        $objSCSV = new SafeCSVAdminForPHP();
                        $boolSafeSCSV2 = $objSCSV->checkSafeCSV2($strIUDSourceFullname);
                        //
                        if( $boolSafeSCSV2 === false ){
                            //----この時点でエラー終了
                            $intErrorType = 362;
                            $intErrorPlaceMark = 2000;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //この時点でエラー終了----
                        }
                        else{
                            $intRecordRowStartIndex = $objSCSV->getRecordRowStart();
                            //
                            $miFileHandle=fopen($strIUDSourceFullname,"r");
                            $miLineIndex=0;
                            $aryRowFromCsv = array();
                            while(! feof($miFileHandle)){
                                $miLineIndex+=1;
                                $miReadBody=fgets($miFileHandle);
                                if( $intRecordRowStartIndex <= $miLineIndex ){
                                    if( $miReadBody != "" ){
                                        //----CSVの行を、$aryRowFromCsv[]へ格納「フィールド名の行およびデータ本体行」
                                        $arraySingle = $objSCSV->makeRowArrayFromSafeCSVRecordRow($miReadBody);
                                        $aryRowFromCsv[]=$arraySingle;
                                        //CSVの行を、$aryRowFromCsv[]へ格納「フィールド名の行およびデータ本体行」----
                                    }
                                }
                            }
                            fclose($miFileHandle);
                        }
                        unset($objSCSV);
                    }
                    else{
                        //----この時点でエラー終了
                        $intErrorType = 363;
                        $intErrorPlaceMark = 2100;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //この時点でエラー終了----
                    }
                    $output_logfile_prefix = "tableIUDBySafeCSV_exec_";
                    //ダウンロードタイプがSafeCSV形式の場合----
                }
                else{
                    //----ダウンロードタイプが通常CSV形式の場合
                    if( preg_match('/\.scsv$/',$strOrgFileNameOfUpTmpFile) === 1 ){
                        //----この時点でエラー終了
                        $intErrorType = 364;
                        $intErrorPlaceMark = 2200;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //この時点でエラー終了----
                    }
                    else{
                        $boolTestModeForCSVUpload = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("testModeForCSVUpload",$refRetKeyExists);

                        if( $boolTestModeForCSVUpload === true ){
                            //----ここから動作保証範囲外
                            //
                            $tmpFileFp =  fopen($strIUDSourceFullname,"r");
                            //
                            $csv_row_counter = 0;
                            //
                            //----CSVの行を、$aryRowFromCsv[]へ格納
                            $aryRowFromCsv = array();
                            while( $aryRowFromCsv[$csv_row_counter] = fgetcsv($tmpFileFp,0,',','"') ){
                                //----行番号を作成
                                $csv_row_counter = $csv_row_counter + 1;
                                //行番号を作成----
                            }
                            //CSVの行を、$aryRowFromCsv[]へ格納----
                            
                            fclose($tmpFileFp);
                            
                            //ここまで動作保証範囲外----
                        }
                        else{
                            //----この時点でエラー終了
                            $intErrorType = 365;
                            $intErrorPlaceMark = 2300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //この時点でエラー終了----
                        }
                    }
                    $output_logfile_prefix = "tableIUDByCSV_exec_";
                    //ダウンロードタイプが通常CSV形式の場合----
                }
                //
                //----CSVの行を、$aryRowFromCsv[]へ格納
                $csv_row_counter = count($aryRowFromCsv);
                //CSVの行を、$aryRowFromCsv[]へ格納----

                //ファイルを開いて配列へ格納----
                
                //"※上記の行数はExcel上の行番号です。\n";
                $expAddBody01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-281");
                //CSVモード----
            }
            else if( $intModeFileCh == 2 ){
                //----JSONモード
                $dlcOrderMode = 3;
                //
                $strLinkFormatterId = $objQMFSALFormatter->getGeneValue("linkJSONFormatterID",$refRetKeyExists);
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = $objTable->getGeneObject("linkJSONFormatterID", $refRetKeyExists);
                }
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = "json";
                }
                //
                if( $strLinkFormatterId === null ){
                    //----JSON用のフォーマットIDがnullだった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 2400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //JSON用のフォーマットIDがnullだった----
                }
                $objListFormatter = $objTable->getFormatter($strLinkFormatterId);
                if( $objListFormatter === null ){
                    //----存在しないフォーマッタ
                    $intErrorType = 801;
                    $intErrorPlaceMark = 2500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //存在しないフォーマッタ----
                }
                if( is_a($objListFormatter, "JSONFormatter") !== true ){
                    //----JSONフォーマッタ系ではなかった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 2600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //JSONフォーマッタ系ではなかった----
                }

                $intTempFilesize = filesize($strIUDSourceFullname);
                $intMaxFileSize = $objQMFSALFormatter->getGeneValue("linkJSONMaxFileSize",$refRetKeyExists);
                if( $intMaxFileSize === null && $refRetKeyExists === false ){
                    $intMaxFileSize = $objTable->getGeneObject("linkJSONMaxFileSize",$refRetKeyExists);
                }
                if( $intMaxFileSize === null || is_int($intMaxFileSize) === false ){
                    $intMaxFileSize = 20*1024*1024;
                }else{
                    if( $intMaxFileSize < 0 ){
                        $intMaxFileSize = 20*1024*1024;
                    }
                }
                
                if( $intMaxFileSize < $intTempFilesize ){
                    //----許容されたサイズ以上のファイルがアップロードされた
                    $intErrorType = 371;
                    $intErrorPlaceMark = 2700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //許容されたサイズ以上のファイルがアップロードされた----
                }

                // JSON文字列を連想配列に
                $aryRowFromJson = @json_decode( file_get_contents( $strIUDSourceFullname ), true, 512, JSON_BIGINT_AS_STRING);

                if( is_array($aryRowFromJson) === false ){
                    //----JSON形式の文字列ではなかった
                    $intErrorType = 701;
                    $intErrorPlaceMark = 2750;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //JSON形式の文字列ではなかった----
                }

                // アップロードファイルがある場合、退避する
                if(array_key_exists('UPLOAD_FILE', $aryRowFromJson)){
                    $uploadFiles = $aryRowFromJson['UPLOAD_FILE'];
                    unset($aryRowFromJson['UPLOAD_FILE']);
                }

                //----JSONの行を、$aryRowFromJson[]へ格納
                $json_row_counter = count($aryRowFromJson);
                //JSONの行を、$aryRowFromJson[]へ格納----

                //"※上記の行数はJSON上のレコード行番号(ヘッダーを除く)です。\n";
                $expAddBody01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-292");
                //JSONモード----

                $output_logfile_prefix = "tableIUDByJSON_exec_";
            }

            $arrayObjColumn = $objTable->getColumns();

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $objWorkSheet = $objWorkBook->getActiveSheet();

                //----Excelの書式のチェック
                //----シート名
                $strSheetName = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("sheetNameForEditByFile",$refRetKeyExists);

                if( $strSheetName == "" ){
                    $strSheetName = $objTable->getDBMainTableLabel();
                }

                if( 31 <= mb_strlen($strSheetName, "UTF-8") ){
                    $strSheetName = $g['objMTS']->getSomeMessage("ITAWDCH-STD-450");
                }

                if( $strSheetName != $objWorkSheet->getTitle() ){
                    //dev_log("エクセルのシート名が不正です。\n",$intControlDebugLevel01);
                    $intErrorType = 352;
                    $intErrorPlaceMark = 2800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //シート名----
                //Excelの書式のチェック----
            }

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $intLengthOfHeaderRows = $objTable->getColGroup()->getHRowCount($strLinkFormatterId) - 1;
                $intRowNoOfFirstBodyRow = $intLengthOfHeaderRows + 8;
                $intColNoOfLastColumn = $objWorkSheet->getHighestDataColumn();
                $intRowNoOfLastBodyRow = $objWorkSheet->getHighestDataRow();
                $excelHeaderData = $objWorkSheet->rangeToArray(ExcelFormatter::cr2s(2, $intRowNoOfFirstBodyRow - 1).":".$intColNoOfLastColumn.($intRowNoOfFirstBodyRow - 1));
                //
                $boolLabelFull = false;
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード
                $intRowNoOfFirstBodyRow = 1;
                if( array_key_exists(0,$aryRowFromCsv) === false ){
                    $aryRowFromCsv = array(array());
                }
                $intColNoOfLastColumn = count($aryRowFromCsv[0]);
                $intRowNoOfLastBodyRow = $csv_row_counter - 1;
                $csvHeaderData = $aryRowFromCsv[0];
                //
                $boolLabelFull = false;
                //CSVモード----
            }
            else if( $intModeFileCh == 2 ){
                //----JSONモード
                $intRowNoOfFirstBodyRow = 1;
                if( array_key_exists(0,$aryRowFromJson) === false ){
                    $aryRowFromJson = array(array());
                }
                $intColNoOfLastColumn = count($aryRowFromJson[0]);
                $intRowNoOfLastBodyRow = $json_row_counter - 1;
                $jsonHeaderData = $aryRowFromJson[0];
                //
                $boolLabelFull = true;
                //JSONモード----
            }

            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID();
            $lcRequiredRowEditByFileColumnId = $objTable->getRequiredRowEditByFileColumnID();
            $lcRowIdentifyColumnId = $objTable->getRowIdentifyColumnID();

            $objREBFColumn = $arrayObjColumn[$lcRequiredRowEditByFileColumnId];
            $objRIColumn = $arrayObjColumn[$lcRowIdentifyColumnId];

            $lcNDBExecuteColumnID = $objREBFColumn->getID();    //"ROW_EDIT_BY_FILE" "EXEC_TYPE"
            $lcNDBExecuteColumnName = $objREBFColumn->getColLabel();    //"実行処理種別"
            //$lcNDBExecuteColumnSynonym = $objREBFColumn->getIDSOP();

            //----配列初期化
            $tableHeaderId = array($lcNDBExecuteColumnID);
            $tableHeaderNm = array($lcNDBExecuteColumnName);
            //$tableHeaderSy = array($lcNDBExecuteColumnSynonym);
            //配列初期化----

            foreach($arrayObjColumn as $objColumn){
                if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                    continue;
                }
                else if( $objColumn->getOutputType($strLinkFormatterId)->isVisible() === false ){
                    continue;
                }
                else{
                    $tableHeaderId[] = $objColumn->getID();
                    $tableHeaderNm[] = $objColumn->getColLabel($boolLabelFull);
                }
            }

            //----列の一致チェック
            if( $intModeFileCh == 0 ){
                //----EXCELモード
                for( $fnv1 = 1; $fnv1 < count($tableHeaderId); ++$fnv1 ){
                    if($arrayObjColumn[$tableHeaderId[$fnv1]]->getColLabel() != $excelHeaderData[0][$fnv1]){
                        //dev_log("列が一致しません。最新のフォーマットを使用してください。(".$arrayObjColumn[$tableHeaderId[$fnv1]]->getColLabel(true).",".$excelHeaderData[0][$fnv1].")\n", $intControlDebugLevel01);
                        $intErrorType = 353;
                        $intErrorPlaceMark = 2900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                $upload_log_print = $objQMFSALFormatter->getGeneValue("uploadLogPrint",$refRetKeyExists);
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード

                if( $csvHeaderData[0] == $lcNDBExecuteColumnID ){
                    $arrayCheckHeader =& $tableHeaderId;
                }
                else if( $csvHeaderData[0] == $lcNDBExecuteColumnName ){
                    $arrayCheckHeader =& $tableHeaderNm;
                }
                else{
                    $intErrorType = 366;
                    $intErrorPlaceMark = 3000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                $dlcLTColumnCount = count($arrayCheckHeader);
                $dlcCSVHeaderDataColumnCount = count($csvHeaderData);
                if( $dlcLTColumnCount == $dlcCSVHeaderDataColumnCount ){
                    for( $fnv1 = 0; $fnv1 < $dlcLTColumnCount ; $fnv1++ ){
                        if( $arrayCheckHeader[$fnv1] != $csvHeaderData[$fnv1] ){
                            $intErrorType = 367;
                            $intErrorPlaceMark = 3100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    $upload_log_print = $objQMFSALFormatter->getGeneValue("uploadLogPrint",$refRetKeyExists);
                }
                else{
                    $intErrorType = 368;
                    $intErrorPlaceMark = 3200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                //CSVモード----
            }
            else if( $intModeFileCh == 2 ){
                //----JSONモード

                $arrayCheckHeader =& $tableHeaderNm;

                $tmpBoolUploadSendHeaderRequired = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("uploadSendHeaderRequired",$refRetKeyExists);
                if( $tmpBoolUploadSendHeaderRequired !== true ){
                    //----レコードのみで構成されている場合を、許容する設定（デフォルト）
                    $jsonHeaderData = $arrayCheckHeader;
                    $intRowNoOfFirstBodyRow = 0;
                    $intRowNoOfLastBodyRow = count($aryRowFromJson);
                    if( $intRowNoOfLastBodyRow == 0 ){
                        $intRowNoOfLastBodyRow = -1;
                    }
                    else{
                        $intRowNoOfLastBodyRow -= 1;
                    }
                    $intColNoOfLastColumn = count($jsonHeaderData);
                    //レコードのみで構成されている場合を、許容する設定（デフォルト）----
                }
                unset($tmpBoolUploadSendHeaderRequired);

                $dlcLTColumnCount = count($arrayCheckHeader);
                $dlcJSONHeaderDataColumnCount = count($jsonHeaderData);
                if( $dlcLTColumnCount == $dlcJSONHeaderDataColumnCount ){
                    for( $fnv1 = 0; $fnv1 < $dlcLTColumnCount ; $fnv1++ ){
                        if( $arrayCheckHeader[$fnv1] != $jsonHeaderData[$fnv1] ){
                            // フォーマットが一致しない。
                            $intErrorType = 372;
                            $intErrorPlaceMark = 3300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    $upload_log_print = $objQMFSALFormatter->getGeneValue("uploadLogPrint",$refRetKeyExists);
                }
                else{
                    // 不正なフォーマット。
                    $intErrorType = 373;
                    $intErrorPlaceMark = 3400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //JSONモード----
            }
            //列の一致チェック----
            
            //----エラー出力形式が個別には設定されていなかった
            if( $upload_log_print === null ){
                $upload_log_print = $objTable->getGeneObject("uploadLogPrint",$refRetKeyExists);
            }
            //エラー出力形式が個別には設定されていなかった----

            if( $upload_log_print !== "toHtml" ){
                $upload_log_print = "toFile";
            }
            if( $upload_log_print == "toHtml" ){
                $strLogRowHead="<tr><td>";
                $strLogRowColSepa="</td><td>";
                $strLogRowTail="</td></tr>";
                $strSepaIdInfoAndError="</td><td>";
            }
            else if( $upload_log_print == "toFile" ){
                $strLogRowHead="";
                $strLogRowColSepa="\t";
                $strLogRowTail=$strFileRowDelimiter;
                $strSepaIdInfoAndError="\n";
            }
            $strLineExplainHead = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-282");
            $strLineExplainTail = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-283");
            
            if( $objTable->getCommitSpanOnTableIUDByFile() === 0 ){
                //----トランザクション開始
                $varTrzStart = $g['objDBCA']->transactionStart();
                if( $varTrzStart !== true ){
                    $intErrorType = 901;
                    $intErrorPlaceMark = 3500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                if( $objTable->inTrzLockSequences($arrayObjColumn)===false ){
                    $intErrorType = 902;
                    $intErrorPlaceMark = 3600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->afterTrzStartAction($aryVariant);
                    if( $arrayTmp[0] === false ){
                        $intErrorType = 903;
                        $intErrorPlaceMark = 3700;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                //トランザクション開始----
            }

            //----bodyTop行目から最後までループ
            $strErrorStreamFromEditExecute = "";
            for($row_i = $intRowNoOfFirstBodyRow; $row_i <= $intRowNoOfLastBodyRow; ++$row_i){
                $inputArray = array();
                if( $intModeFileCh == 0 ){
                    //----EXCELモード

                    $excelBodyData = array();
                    for($focusCol = 0; $focusCol < count($tableHeaderId) ; $focusCol++ ){
                        $excelBodyData[] = $objWorkSheet->getCellByColumnAndRow(2+$focusCol,$row_i)->getValue();
                    }

                    //----第1引数の配列値をキーに、第2引数の配列値を値とする連想配列を形成
                    $inputArray = array_combine($tableHeaderId, $excelBodyData);
                    //第1引数の配列値をキーに、第2引数の配列値を値とする連想配列を形成----

                    //EXCELモード----
                }
                else if( $intModeFileCh == 1 ){
                    //----CSVモード
                    for($dlcFnv2 = 0; $dlcFnv2 < $intColNoOfLastColumn; $dlcFnv2++ ){
                        $colKey = $tableHeaderId[$dlcFnv2];
                        $inputArray[$colKey] = $aryRowFromCsv[$row_i][$dlcFnv2];
                    }
                    //CSVモード----
                }
                else if( $intModeFileCh == 2 ){
                    //----JSONモード
                    for($dlcFnv2 = 0; $dlcFnv2 < $intColNoOfLastColumn; $dlcFnv2++ ){
                        $colKey = $tableHeaderId[$dlcFnv2];

                        if(!array_key_exists($dlcFnv2, $aryRowFromJson[$row_i])){
                            continue;
                        }

                        $inputArray[$colKey] = $aryRowFromJson[$row_i][$dlcFnv2];

                        // アップロードファイルを登録する
                        foreach($arrayObjColumn as $objColumn){
                            if($colKey === $objColumn->getID() && "FileUploadColumn" === get_class($objColumn) && $objColumn->isAllowUploadColmnSendRestApi()){

                                // 値が無い場合はファイル削除
                                if("" === $inputArray[$colKey]){
                                    $inputArray["del_flag_".$objColumn->getIDSOP()] = "on";
                                }
                                // 値がある場合は登録・更新
                                else{
                                    if(array_key_exists($row_i, $uploadFiles) && array_key_exists($dlcFnv2, $uploadFiles[$row_i])){

                                        // 最新時間を取得（一時ファイル名に利用）
                                        $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
                                        $nowTime = date("YmdHis") . $now->format("u");

                                        $tmpFile = $objColumn->getLAPathToPreUploadSave() . "/" . $inputArray[$colKey] . "_" . $nowTime;
                                        $tmpNameFile = $objColumn->getLAPathToPreUploadSave() . "/fn_" . $inputArray[$colKey] . "_" . $nowTime;

                                        file_put_contents($tmpFile, base64_decode($uploadFiles[$row_i][$dlcFnv2]));
                                        file_put_contents($tmpNameFile, $inputArray[$colKey]);

                                        $inputArray["tmp_file_".$objColumn->getIDSOP()] = basename($tmpFile);
                                        $aryRetBodyOfTempFileCheck = $objColumn->checkTempFileBeforeMoveOnPreLoad($tmpFile,  basename($tmpFile), $aryVariant, $arySetting);
                                        if( $aryRetBodyOfTempFileCheck[0] !== true || $aryRetBodyOfTempFileCheck[1] !== null ){
                                            // 不正なフォーマット。
                                            $intErrorType = 374;
                                            $intErrorPlaceMark = 3500;
                                            $strErrMsg = $aryRetBodyOfTempFileCheck[3];
                                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //JSONモード----
                }
                
                //----テーブルへのアクセスを実行
                $arrayRetResult = $objREBFColumn->editExecute($inputArray, $dlcOrderMode, $aryVariant);
                //テーブルへのアクセスを実行----
                //
                $aryRawResultOfEditExecute[$row_i] = $arrayRetResult[4];
                //
                if( $arrayRetResult[0] === false ){
                    //----エラーあり
                    if( $arrayRetResult[2] != "" ){
                        //----CSV系の場合
                        $row_id_info = "";
                        $rowIdValue = $arrayRetResult[3];
                        $objIntNumVali = new IntNumValidator(null,null);
                        if( $objIntNumVali->isValid($rowIdValue)===false ){
                            $rowIdValue = "";
                        }
                        if( 0 < strlen($rowIdValue ) ){
                            $row_id_info = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-291",array($objRIColumn->getColLabel(true),$rowIdValue));
                        }
                        $row_id_info .= $strSepaIdInfoAndError;
                        //CSV系の場合----
                        $strErrorStreamFromEditExecute .= "{$strLogRowHead}{$strLineExplainHead}{$row_i}{$strLineExplainTail}{$strLogRowColSepa}{$row_id_info}{$arrayRetResult[1]}{$strLogRowColSepa}{$arrayRetResult[2]}{$strLogRowTail}";
                    }
                    //エラーあり----
                }
                else{
                    //----エラーなし
                    //エラーなし----
                }
            }
            //bodyTop行目から最後までループ----

            $aryNormalResultOfEditExecute = $objREBFColumn->getResultCount();
            
            //----結果出力
            //
            $strRetStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-451",$strOrgFileNameOfUpTmpFile);
            $strResultList = "";
            $aryResultCountList = array();
            
            $intSuccess =0;
            $intError =0;
            
            $strErrCountExplainHead = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-284");
            $strErrCountExplainTail = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-285");
            
            foreach($aryNormalResultOfEditExecute as $strKey=>$aryData){
                $strResultList .= $strErrCountExplainHead.sprintf("%s:%10d",$aryData['name'],$aryData['ct']).$strErrCountExplainTail."\n";
                $aryResultCountList[] = array($aryData['name'],$aryData['ct'],$strErrCountExplainHead.sprintf("%s:%10d",$aryData['name'],$aryData['ct']).$strErrCountExplainTail."\n");
                if( $strKey == "error" ){
                    $intError += $aryData['ct'];
                }
                else{
                    $intSuccess += $aryData['ct'];
                }
            }
            
            if( $objTable->getCommitSpanOnTableIUDByFile() === 0 ){
                if( 0 === $intError ){
                    //----トランザクション終了
                    $varCommit = $g['objDBCA']->transactionCommit();
                    if( $varCommit !== true ){
                        $intErrorType = 904;
                        $intErrorPlaceMark = 3800;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $varTrzExit = $g['objDBCA']->transactionExit();
                    if( $varTrzExit === false ){
                        $intErrorType = 905;
                        $intErrorPlaceMark = 3900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $strRetStrBody .= $strResultList;
                    //----トランザクション終了
                }
                else{
                    //----1件でもエラーがあったらロールバック
                    //----ロールバックする
                    $varRollBack = $g['objDBCA']->transactionRollBack();
                    if( $varRollBack === false ){
                        //----1回目のロールバックが失敗してしまった場合
                        $intErrorType = 906;
                        $intErrorPlaceMark = 4000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //1回目のロールバックが失敗してしまった場合----
                    }
                    $varTrzExit = $g['objDBCA']->transactionExit();
                    if( $varTrzExit === false ){
                        $intErrorType = 907;
                        $intErrorPlaceMark = 4100;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //ロールバックする----
                    $strRetStrBody .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-304");
                    //1件でもエラーがあったらロールバック----
                }
            }
            else{
                $strRetStrBody .= $strResultList;
            }
            
            if( $varRollBack !== true ){
                $refValue = array(
                               "caller"=>"tableIUDByQMFile",
                               "ordMode"=>$dlcOrderMode,
                               "updateResource"=>$strFileReceptUniqueNumber,
                               "request_time"=>$unixStartTimeStamp,
                               "resultList"=>$aryResultCountList,
                               "intSuccess"=>$intSuccess,
                               "intError"=>$intError
                            );
                
                $objTable->commonEventHandlerExecute($refValue);
            }
            
            if( $strErrorStreamFromEditExecute != "" ){
                if( $upload_log_print == "toHtml" ){
                    $strRetStrBody .= "<table class=\"tableIUDByQMFileErrorReport\" border=\"1\">".$strErrorStreamFromEditExecute."</table>\n";
                    $strRetStrBody .= $expAddBody01;
                }
                else if( $upload_log_print == "toFile" ){
                    if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == "on" ){
                        $protocol = "https://";
                    }
                    else{
                        $protocol = "http://";
                    }
                    $scheme_n_authority = getSchemeNAuthority();
                    do
                    {
                        $output_logfile_name = $output_logfile_prefix . date("YmdHis") . "_" . mt_rand() . ".log";
                        $output_logfile_name_full_path = $editErrorLogDir."/".$output_logfile_name;
                        $dlcExistsFile = file_exists($output_logfile_name_full_path);
                    } while($dlcExistsFile === true);

                    $objTable->writeAllToFileOnce($output_logfile_name_full_path, $strErrorStreamFromEditExecute, "w");

                    $strAnchorInnerText = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-286");
                    $strAnchorBody = "<a href=\"" . $scheme_n_authority . $_SERVER['PHP_SELF'] . "?no={$g['page_dir']}&req_errlog_target_name=" . $output_logfile_name . "\">{$strAnchorInnerText}</a>";

                    $strRetStrBody .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-287",$strAnchorBody)."<br>\n";
                }
            }
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            if( 901 <= $intErrorType ) web_log($tmpErrMsgBody);
        }
        //
        //----大量行のアップロードに備えて、タイムリミットを「30」に戻す
        //大量行のアップロードに備えて、タイムリミットを「30」に戻す----
        
        //結果出力----
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return array($strRetStrBody,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryNormalResultOfEditExecute,$aryRawResultOfEditExecute);
    }

    function printUploadLog($file_neme, &$aryVariant=array(), &$arySetting=array()){
        global $g;

        $intControlDebugLevel01 = 250;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $root_dir_path = $g['root_dir_path'];

        $editErrorLogDir = "{$root_dir_path}/temp/update_by_file_error";

        $found_flag = 0;

        $ACRCM_id = "UNKNOWN";

        try{
            // ----直接呼ばれることを想定しないので処理を削除
            // 直接呼ばれることを想定しないので処理を削除----

            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");
            if( $boolKeyExists === false ){
                list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
            }
            //メニューIDの取得----

            $objFileVali = new FileNameValidator();
            $tmpStrNumberForRI = null;
            $tmpAryRegData = array();
            $tmpAryVariant = array();
            if( $objFileVali->isValid($file_neme,$tmpStrNumberForRI,$tmpAryRegData,$tmpAryVariant) === false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // ログファイル名の作成
            $file_neme_full_path = $editErrorLogDir."/".$file_neme;

            // MIMEタイプの設定
            printHeaderForProvideFileStream($file_neme,"application/octet-stream");

            // ファイルを出力
            $log_string = file_get_contents( $file_neme_full_path );
            if( 0 == strlen($log_string) ){
                $intErrorType = 601;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            echo $log_string;
            
            // アクセスログ出力
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4051",array($strFxName,$file_neme)));
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 601: $strNoFileMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-289",$file_neme);
                default : $strErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001",$intErrorType);break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイザー向けメッセージを作成
                //ロードテーブルカスタマイザー向けメッセージを作成----
            }
            
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
{$strErrMsgBody}
</div>
</body>
</html>
EOD;
            // アクセスログ出力
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
        }
        
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    }
?>
