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

//  【処理概要】
//    ・登録/更新のテーブル領域に、プルダウンリストHtmlタグを、事後的に作成する

//////////////////////////////////////////////////////////////////////

class ListFormatter {
    /*Formatter : Tableの出力フォーマットを決定する。
        サブクラスは以下の４つ
            TableFormatter
            JournalTableFormatter
            SingleRowTableFormatter (extends TableFormatter)
            CSVFormatter
            ExcelFormatter
    */

    protected $strFormatterId;  // as string 出力形式の名前（旧name）
    protected $strPrintTableId; // as string htmlのテーブルに設定するID

    //----参照
    protected $objTable; // as Table テーブルの名前
    //参照----

    protected $aryVarGene;

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        $this->strFormatterId = $strFormatterId;
        $this->objTable = $objTable;
        $this->strPrintTableId = $strPrintTableId;

        $this->aryVarGene = array();
    }

    function getFormatterID(){
        return $this->strFormatterId;
    }

    function getPrintTableID(){
        return $this->strPrintTableId;
    }

    function format($tableTagId = null){
        // クラス(Table)のメソッド(getPrintFormat)から呼ばれる。
    }

    function setGeneValue($strKey, $varVal, $boolKeyUnset=false, &$refRetResult=true){
        $retBool = true;
        if( $strKey===null || is_string($strKey)===false ){
            //----NULLキーはセットさせない
            $retBool = false;
        }else{
            if( $boolKeyUnset === false ){
                $this->aryVarGene[$strKey] = $varVal;
            }else{
                $refRetKeyExists = array_key_exists($strKey, $this->aryVarGene);
                if( $refRetKeyExists === true ){
                    unset($this->aryVarGene[$strKey]);
                }else{
                    $retBool = false;
                }
            }
        }
        return $retBool;
    }

    function getGeneValue($strKey, &$refRetKeyExists=false){
        $retVarVal = null;
        if( $strKey===null || is_string($strKey)===false ){
            //----NULLキーはセットさせないので、null返し
        }else{
            $refRetKeyExists = array_key_exists($strKey, $this->aryVarGene);
            if( $refRetKeyExists === true ){
                $retVarVal = $this->aryVarGene[$strKey];
            }
        }
        return $retVarVal;
    }

    function checkForbiddenPattern($strName){
        global $g;
        $retBool=true;
        $strRegexpFormat = "/^[^:\\?[\]\/" . "*]*$/s";
        if(preg_match($strRegexpFormat, $strName)!==1){
            $retBool = false;
        }
        return $retBool;
    }

    function makeLocalFileName($strFilePostFix, $intUnixTime){
        global $g;
        //----設定がミスしていた場合は[null]を返す
        $strTempName = $this->objTable->getDBMainTableLabel();
        if( $this->checkForbiddenPattern($strTempName)===false ){
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-21004"));
            $strFileName = null;
        }
        else{
            if( mb_strlen($strTempName,"UTF-8") <= 64 ){
                $strFileHead = $strTempName;
            }
            else{
                $strFileHead = "";
            }
            if($intUnixTime === null){
                $strFileName = $strFileHead."_".$strFilePostFix;
            }
            else{
                $strFileName = $strFileHead."_".date("YmdHis",$intUnixTime).$strFilePostFix;
            }
        }
        return $strFileName;
    }

}

class QMFileSendAreaFormatter extends ListFormatter {


}

class CSVFormatter extends ListFormatter {
    /* CSVFormatter : CSV向けのフォーマッタ */
    protected $handleTmpFile;

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        parent::__construct($strFormatterId, $objTable, $strPrintTableId);
        $this->setGeneValue("outputFileType", "SafeCSV");
        $this->handleTmpFile = null;
    }

    function fileOpen($strTmpFilename){
        $boolRet = false;
        if( $this->handleTmpFile === null ){
            $tmpRet = @fopen($strTmpFilename, "wb");
            if( $tmpRet !== false ){
                $this->handleTmpFile = $tmpRet;
                $boolRet = true;
            }
        }
        return $boolRet;
    }

    function fileClose(){
        $boolRet = false;
        if( $this->handleTmpFile !== null ){
             $boolRet = fclose($this->handleTmpFile);
        }
        return $boolRet;
    }

    function fileStreamAdd($strStream,$refIntRet=null){
        $boolRet = false;
        if( $this->handleTmpFile !== null ){
            $tmpRet = @fwrite($this->handleTmpFile, $strStream);
            if( $tmpRet === false ){
                $boolRet = false;
            }else{
                $boolRet = true;
                $refIntRet = $tmpRet;
            }
        }
        return $boolRet;
    }

    function getFileHandle(){
        $tmpRet = $this->handleTmpFile;
        return $tmpRet;
    }

    function format(){
        //----クラス(Table)のメソッド(getPrintFormat)から呼ばれる。

        $aryObjColumn = $this->objTable->getColumns();

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $aryObjRow = $this->objTable->getRows();

        $strColSepa = ",";

        $strHdd = "";
        $strRows = "";

        $strOutputFileType = $this->getGeneValue("outputFileType");

        $boolUPDColHide = $this->getGeneValue("timeStampForUpdateHide");

        $boolFieldRowAdd = $this->getGeneValue("csvFieldRowAdd");
        $boolRecordShowAdd = $this->getGeneValue("csvRecordShowAdd");

        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID();   //"UPD_UPDATE_TIMESTAMP");
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $objREBFColumn = $aryObjColumn[$lcRequiredRowEditByFileColumnId];

        if( $strOutputFileType == "SafeCSV" ){
            //----SafeCSVモード出力の場合

            $objSCSVAdmin = new SafeCSVAdminForPHP();

            if( $boolFieldRowAdd !== false ){

                $strHdd.="<SAFECSV>\r\n";
                $arrayMasterData=$this->getRowArrayFromIdColumns();
                for($dlcFnv1=0;$dlcFnv1<count($arrayMasterData);$dlcFnv1++){
                    $arrayMasterRows=$arrayMasterData[$dlcFnv1];
                    $strHdd.=$objSCSVAdmin->makeSafeCSVRecordRowFromRowArray($arrayMasterRows,array("\r","\n",",","SAFECSV"),array("%R","%L","%C","%escTag"));
                }
                $strHdd.="</SAFECSV>\r\n"; 

                $arrayFileterConds=$this->getFilterConditions();
                $strHdd.="<FILTERCON>\r\n";
                for($dlcFnv1=0;$dlcFnv1<count($arrayFileterConds);$dlcFnv1++){
                    $arrayFileterCond=$arrayFileterConds[$dlcFnv1];
                    $strHdd.=$objSCSVAdmin->makeSafeCSVRecordRowFromRowArray($arrayFileterCond,array("\r","\n",",","FILTERCON"),array("%R","%L","%C","%escTag"));
                }
                $strHdd.="</FILTERCON>\r\n";

                //----ここからレコードヘッダーを処理
                $strHeaderType = $this->getGeneValue("csvHeaderType");

                $arrayColumn=array();
                foreach($aryObjColumn as $objColumn){
                    if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                        //----Class(CSVHFmt系)
                        if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                            if($boolUPDColHide === true){
                                continue;
                            }
                        }
                        if( $strHeaderType == "id" ){
                            $objColumn->getOutputType($this->strFormatterId)->getHead()->setOutputPrintType("noWrapID", $strColSepa);
                        }else{
                            $objColumn->getOutputType($this->strFormatterId)->getHead()->setOutputPrintType("noWrapLabel", $strColSepa);
                        }
                        $arrayColumn[]=$objColumn->getOutputHeader($this->strFormatterId);

                        $objColumn->getOutputType($this->strFormatterId)->getBody()->setOutputPrintType("noWrapData", $strColSepa);
                        //Class(CSVHFmt系)----
                    }
                }
                $strHdd.=$objSCSVAdmin->makeSafeCSVRecordRowFromRowArray($arrayColumn);
                //ここまでレコードヘッダーを処理----
            }

            if( $boolRecordShowAdd !== false ){
                //----ここからレコード本体行を処理
                foreach($aryObjRow as $objRow){
                    $arrayFocusRow = array();

                    foreach($aryObjColumn as $objColumn){
                        if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                                if( $boolUPDColHide === true ){
                                    continue;
                                }
                            }
                            //----Class(CSVBFmt系)
                            $arrayFocusRow[] = $objColumn->getOutputBody($this->strFormatterId,$objRow->getRowData());
                            //Class(CSVBFmt系)----
                        }
                    }
                    $strRows .= $objSCSVAdmin->makeSafeCSVRecordRowFromRowArray($arrayFocusRow);
                }
                //ここまでレコード本体行を処理----
            }
            //SafeCSVモード出力の場合----
        }else{
            //----非(SafeCSV)のCSV出力の場合

            $strRowSepa = "\n";

            if( $boolFieldRowAdd !== false ){
                //----ここからレコードヘッダーを処理
                $strHeaderType = $this->getGeneValue("csvHeaderType");

                if( $strHeaderType == "id" ){
                    //----DB上のカラム名をヘッダーに
                    foreach($aryObjColumn as $objColumn){
                        if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                                if($boolUPDColHide === true){
                                    continue;
                                }
                            }
                            //----Class(CSVHFmt系)
                            $objColumn->getOutputType($this->strFormatterId)->getHead()->setOutputPrintType("wrapID", $strColSepa);
                            $strHdd .= $objColumn->getOutputHeader($this->strFormatterId);

                            $objColumn->getOutputType($this->strFormatterId)->getBody()->setOutputPrintType("wrapData", $strColSepa);
                            //Class(CSVHFmt系)----
                        }
                    }
                    //DB上のカラム名をヘッダーに----
                }else{
                    //----DB上のカラム名ではなく、html上表示する名前をヘッダーに
                    foreach($aryObjColumn as $objColumn){
                        if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                                if($boolUPDColHide === true){
                                    continue;
                                }
                            }
                            $objColumn->getOutputType($this->strFormatterId)->getHead()->setOutputPrintType("wrapLabel", $strColSepa);
                            $strHdd .= $objColumn->getOutputHeader($this->strFormatterId);

                            $objColumn->getOutputType($this->strFormatterId)->getBody()->setOutputPrintType("wrapData", $strColSepa);
                        }
                    }
                    //DB上のカラム名ではなく、html上表示する名前をヘッダーに----
                }

                $intShift = strlen($strHdd) - strlen($strColSepa);
                $strHdd[$intShift] = $strRowSepa;

                //ここまでレコードヘッダーを処理----
            }

            if($boolRecordShowAdd !== false){
                //----ここからレコード本体行を処理
                foreach($aryObjRow as $objRow){
                    $strRowFocus = "";

                    foreach($aryObjColumn as $objColumn){
                        if( $objColumn->getOutputType($this->strFormatterId)->isVisible() ){
                            if($objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId){
                                if($boolUPDColHide === true){
                                    continue;
                                }
                            }
                            //----Class(CSVBFmt系)
                            $strRowFocus .= $objColumn->getOutputBody($this->strFormatterId,$objRow->getRowData());
                            //Class(CSVBFmt系)----
                        }
                    }
                    $intShift = strlen($strRowFocus) - 1;
                    $strRowFocus[$intShift] = $strRowSepa;

                    $strRows .= $strRowFocus;

                }
                //ここまでレコード本体行を処理----
            }
            //非(SafeCSV)のCSV出力の場合----
        }

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }

        return $strHdd . $strRows;
    }

    function getFilterConditions(){
        global $g;
        $strTextExplain01   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15001");
        $strTextDisuse01    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15002");
        $strTextDisuse02    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15003");
        $strTextAllRecord   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15004");
        $strTextWithBlank   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15005");
        $strTextWithNoBlank = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15006");
        
        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID();
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $arrayColumnCollection = array();

        $arrayColumnElemnt = array();
        $arrayColumnElemnt[] = $strTextExplain01;//出力日時
        $arrayColumnElemnt[] = date("Y/m/d H:i:s");

        $arrayColumnCollection[] = $arrayColumnElemnt;
        $arrayColumnCollection[] = array();
        $arrayColumnCollection[] = array();

        foreach($this->objTable->getColumns() as $objColumn){
            $arrayColumnElemnt = array();
            $int_element_count = 0;
            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                //continue;
            }else if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === false ){
                //continue;
            }

            $arrayColumnElemnt[] = $objColumn->getColLabel(true);

            //----カラム別の、基本フィルターの値を取得
            $arrayBasicFilterValue = $objColumn->getFilterValues();
            //カラム別の、基本フィルターの値を取得----

            if( is_a($objColumn, "DelBtnColumn") === true ){
                //----廃止フラグだけ特別対応
                if( array_key_exists(0,$arrayBasicFilterValue)===true ){
                    $arrayColumnElemnt[] = $arrayBasicFilterValue[0]==="0"?$strTextDisuse01:$strTextDisuse02;
                }else{
                    $arrayColumnElemnt[] = $strTextAllRecord;//全レコード;
                }
                $int_element_count += 1;
                //廃止フラグだけ特別対応----
            }else{
                //----カラム別の、リッチ・フィルターの値を取得
                $arrayRichFilterValue = $objColumn->getRichFilterValues();
                //カラム別の、リッチ・フィルターの値を取得----

                //----NULL検索リクエストを取得
                $boolNullSearch = $objColumn->getNullSearchExecute();
                //NULL検索リクエストを取得----

                if( $boolNullSearch === true ){
                    $arrayColumnElemnt[] = $strTextWithBlank;//{空白}含む;
                    $int_element_count += 1;
                }else{
                    $arrayColumnElemnt[] = $strTextWithNoBlank;//{空白}なし;
                }

                if( 1 <= count($arrayBasicFilterValue) ){
                    if( $objColumn->getSearchType() == "range" ) {
                        $value=isset($arrayBasicFilterValue[0])?$arrayBasicFilterValue[0]:"";
                        $value.="～";
                        $value.=isset($arrayBasicFilterValue[1])?$arrayBasicFilterValue[1]:"";
                    }else{
                        $value = $arrayBasicFilterValue[0];
                    }
                    $int_element_count += 1;
                }else{
                    $value = "";
                }
                $arrayColumnElemnt[] = $value;
                
            }
            if( 1 <= $int_element_count + count($arrayRichFilterValue) ){
                if( 1 <= count($arrayRichFilterValue) ){
                    if( is_a($objColumn, "IDColumn") === true ){
                        //IDは文字列に変換
                        $aryFilterData = $objColumn->getMasterTableArrayFromMainTable();
                        foreach($arrayRichFilterValue as $value){
                            $value = $aryFilterData[$value];
                            $arrayColumnElemnt[] = $value;
                        }
                    }else if( is_a($objColumn, "IDRelaySearchColumn") === true ){
                        if( $objColumn->getAddSelectTagPrintType()===0 ){
                            //----表示列側での仮想マスタテーブルモードの場合
                            foreach($arrayRichFilterValue as $value){
                                $arrayColumnElemnt[] = $value;
                            }
                            //表示列側での仮想マスタテーブルモードの場合----
                        }else{
                            $aryFilterData = $objColumn->getPrimeMasterTableArray();
                            foreach($arrayRichFilterValue as $value){
                                $value = $aryFilterData[$value];
                                $arrayColumnElemnt[] = $value;
                            }
                        }
                    }else{
                        foreach($arrayRichFilterValue as $value){
                            $arrayColumnElemnt[] = $value;
                        }
                    }
                }
                $arrayColumnCollection[] = $arrayColumnElemnt;
            }
        }
        return $arrayColumnCollection;
    }

    function getCSVFilter(){
        global $g;
        $strTextExplain01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15007");
        $strTextExplain02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-15008");

        $aryObjColumn = $this->objTable->getColumns();

        $str = "\"{$strTextExplain01}\"\n";
        $str .= "\"".date("Y/m/d H:i:s") . "\"\n";
        $str .= "\n";
        $str .= "\"{$strTextExplain02}\"\n";
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                //----DBカラムか、どうかを区別なく、出力する
                $str .= "\"{$objColumn->getColLabel()}\"";
                $arySource = $objColumn->getFilterValues();
                foreach($arySource as $filter){
                    $str .= ",\"{$filter}\"";
                }
                $str .= "\n";
                //DBカラムか、どうかを区別なく、出力する----
            }
        }
        $str .= "\n";
        return $str;
    }

    function getRowArrayFromIdColumns(){

        $recRow = array();
        $intIDColumnLen = 0;

        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        //----CSVファイルヘッダーを処理
        $aryObjColumn = $this->objTable->getColumns();
        $boolUPDColHide = $this->getGeneValue("timeStampForUpdateHide");

        $objREBFColumn = $aryObjColumn[$lcRequiredRowEditByFileColumnId];

        $strHeaderType = $this->getGeneValue("csvHeaderType");

        if($boolUPDColHide !== true){
            $intIDColumnLen += 1;

            if( $strHeaderType == "id" ){
                $strColumnHead = $objREBFColumn->getID();//"ROW_EDIT_BY_FILE" "EXEC_TYPE"
            }else{
                $strColumnHead = $objREBFColumn->getColLabel();//"処理種別";
            }

            $tempRecRow = array();
            $tempRecRow[] = $strColumnHead;
            foreach($objREBFColumn->getCommandArrayForEdit() as $key=>$strDispValue){
                $tempRecRow[] = $strDispValue;
            }

            $recRow[$intIDColumnLen - 1] = $tempRecRow;
            unset($tempRecRow);
        }
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getOutputType($this->strFormatterId)->isVisible() === true ){
                if(is_a($objColumn, "IDColumn") === true ){

                    $intIDColumnLen += 1;

                    if( $strHeaderType == "id" ){
                        $recRow[$intIDColumnLen - 1][0] = $objColumn->getID();
                    }else{
                        $recRow[$intIDColumnLen - 1][0] = $objColumn->getColLabel();
                    }

                    $aryFace = $objColumn->getMasterTableArrayForInput();
                    $aryKey = array_keys($aryFace);

                    for($dlcFnv1 = 0; $dlcFnv1 < count($aryKey); $dlcFnv1 ++ ){
                        $recRow[$intIDColumnLen - 1][$dlcFnv1 + 1] = $aryFace[$aryKey[$dlcFnv1]];
                    }

                }
            }
        }
        return $recRow;
    }

    function writeToFile($sql, $arrayFileterBody, $objTable, $objFunction01ForOverride, $strFormatterId, $filterData, $aryVariant, &$arySetting){
        global $g;
        $intControlDebugLevel01=250;

        $intRowLength = null;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strFxName = __CLASS__."::".__FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            if( is_callable($objFunction01ForOverride) !== true ){
                //----標準writeToFile句

                $this->setGeneValue("csvFieldRowAdd",false);
                $this->setGeneValue("csvRecordShowAdd",true);

                $intFetchCount = 0;

                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
                if( $retArray[0] !== true ){
                    $intErrorType = 501;
                    throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }

                $objQuery =& $retArray[1];
                while ( $row = $objQuery->resultFetch() ){
                    $intFetchCount += 1;
                    $objTable->addData($row, false);
                    if( ($intFetchCount % 10000) === 0){
                        //----10000行ずつファイルへ書き込み
                        $boolRet = $this->fileStreamAdd($objTable->getPrintFormat($strFormatterId));
                        if( $boolRet !== true ){
                            $intErrorType = 501;
                            throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                        }
                        //----メモリを確保するためにデータを解放
                        $objTable->setData(array());
                        //メモリを確保するためにデータを解放---- 
                        //10000行ずつファイルへ書き込み----
                    }
                }

                if( ($intFetchCount % 10000) !== 0 ){
                    $boolRet = $this->fileStreamAdd($objTable->getPrintFormat($strFormatterId));
                    if( $boolRet !== true ){
                        $intErrorType = 501;
                        throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                    }
                    //----メモリを確保するためにデータを解放
                    $objTable->setData(array());
                    //メモリを確保するためにデータを解放---- 
                }

                unset($objQuery);
                unset($retArray);

                //標準writeToFile句----
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting);
                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryErrMsgBody = $tmpAryRet[2];
                    throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                $intRowLength = $tmpAryRet[0];
                unset($tmpAryRet);
            }
        }
        catch(Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",$tmpErrMsgBody));
        }

        $retArray = array($intRowLength,$intErrorType,$aryErrMsgBody,$strErrMsg);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }

}

class JSONFormatter extends ListFormatter {

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        parent::__construct($strFormatterId, $objTable, $strPrintTableId);
    }

    //----カラムの、プリント順序シーケンスを初期化・再計算する
    function setPrintSeqToOutputType(){
        $aryObjColumn = $this->objTable->getColumns();
        
        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID();   //"UPD_UPDATE_TIMESTAMP");
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $boolUPDColHide = $this->getGeneValue("timeStampForUpdateHide");

        $intPrintSeq=0;
        foreach($aryObjColumn as $objColumn){
            $focusObjOT = $objColumn->getOutputType($this->strFormatterId);
            $focusObjOT->setPrintSeq(null);
            if( $focusObjOT->isVisible() === true ){
                if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                    if( $boolUPDColHide === true ){
                        continue;
                    }
                }
                $focusObjOT->setPrintSeq($intPrintSeq);
                $intPrintSeq += 1;
            }
        }
    }
    //カラムの、プリント順序シーケンスを初期化・再計算する----

    //----RestAPI(Gate)で公開されている、カラム情報を出力する
    function getLabelListOfOpendColumn($boolForDecode=false){
        $retAryRowsOfDataHeader = array();

        $aryObjColumn = $this->objTable->getColumns();

        $this->setPrintSeqToOutputType();
        
        $aryRowsOfDataHeader = array();
        foreach($aryObjColumn as $objColumn){
            $focusObjOT = $objColumn->getOutputType($this->strFormatterId);
            $intPrintSeq = $focusObjOT->getPrintSeq();
            if( $intPrintSeq !== null ){
                if( $boolForDecode !== true ){
                    $retAryRowsOfDataHeader[$intPrintSeq] = $objColumn->getColLabel(true);
                }
                else{
                    $retAryRowsOfDataHeader[$intPrintSeq] = $objColumn->getIDSOP();
                }
            }
        }
        return $retAryRowsOfDataHeader;
    }
    //RestAPI(Gate)で公開されている、カラム情報を出力する----

    function format(){
        $aryObjColumn = $this->objTable->getColumns();

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $aryObjRow = $this->objTable->getRows();

        $boolUPDColHide = $this->getGeneValue("timeStampForUpdateHide");

        $retArray = array();

        $aryRowsOfDataHeader = array();
        $aryRowsOfDataBody = array();

        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID();   //"UPD_UPDATE_TIMESTAMP");
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $aryRowsOfDataHeader = $this->getLabelListOfOpendColumn();

        //----ここからレコード本体行を処理
        $aryRowsOfData = array();
        $aryUploadFile = array();
        $intCnt = 1;
        $aryRowsOfData[] = $aryRowsOfDataHeader;
        foreach($aryObjRow as $objRow){
            $arrayFocusRow = array();

            foreach($aryObjColumn as $objColumn){
                $focusObjOT = $objColumn->getOutputType($this->strFormatterId);
                $intPrinteSeq = $focusObjOT->getPrintSeq();
                if( $intPrinteSeq !== null ){
                    $arrayFocusRow[$intPrinteSeq] = $objColumn->getOutputBody($this->strFormatterId,$objRow->getRowData());

                    // アップロードファイルがある場合、中身を復号化する
                    if("FileUploadColumn" === get_class($objColumn)){
                        if("" != $arrayFocusRow[$intPrinteSeq]){
                            $strUploadFile = base64_encode(file_get_contents($objColumn->getLAPathToFUCItemPerRow($objRow->getRowData())));
                        }
                        else{
                            $strUploadFile = "";
                        }
                        $aryUploadFile[$intCnt][$intPrinteSeq] = $strUploadFile;
                    }
                }
            }
            $aryRowsOfData[] = $arrayFocusRow;
            $intCnt++;
        }
        //ここまでレコード本体行を処理----

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }

        return array($aryRowsOfData, $aryUploadFile);
    }

    function selectResultFetch($sql, $arrayFileterBody, $objTable, $intJsonLimit, $objFunction01ForOverride, $strFormatterId, $filterData, $aryVariant, &$arySetting){
        global $g;
        $intControlDebugLevel01=250;

        $intRowLength = null;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strFxName = __CLASS__."::".__FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            if( is_callable($objFunction01ForOverride) !== true ){
                $intFetchCount = 0;
                
                //----標準selectResultFetch句
                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    while ( $row = $objQuery->resultFetch() ){
                        $intFetchCount += 1;
                        if( $intJsonLimit === null || $intFetchCount <= $intJsonLimit ){
                            $objTable->addData($row, false);
                        }
                    }

                    // ----取得したレコード数を取得
                    $intRowLength = $intFetchCount;
                    // 取得したレコード数を取得----
                    unset($objQuery);
                }
                else{
                    $intErrorType = 501;
                    throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                unset($retArray);
                //標準selectResultFetch句----
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($arrayFileterBody, $objTable, $intJsonLimit, $strFormatterId, $filterData, $aryVariant, $arySetting);
                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryErrMsgBody = $tmpAryRet[2];
                    throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                $intRowLength = $tmpAryRet[0];
                unset($tmpAryRet);
            }
        }
        catch(Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",$tmpErrMsgBody));
        }

        $retArray = array($intRowLength,$intErrorType,$aryErrMsgBody,$strErrMsg);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
}

class ExcelFormatter extends ListFormatter {

    protected $templateFilePath; // as string フルパス指定
    protected $requireFormatFlag; // as boolean PHPExcel側でのフォーマット設定変換の要否
    protected $strExportFilePath;

    protected $bodyTopRow;
    protected $bodyTopColumn;
    protected $headerRows;

    protected $objFocusWB;
    protected $aryEditSheetDescription;
    protected $intEditSheetRecord;
    protected $intEditSheetMaxCol;
    
    protected $tempBufferForHeader;

    protected $strPrintTargetListFormatterId;

    protected $strRGBOfSendForbiddenColumn;
    protected $strRGBOfLastContentRow;

    protected $aryValidation;       // バリデーションを適用するシートデータ本体のセル範囲情報(Column/Row)とマスターテーブルのIDを格納する配列 function ValidationDataWorkSheetRecordAddから参照される

    protected $aryValidationHeader; // バリデーションデータを適用するシートデータヘッダー(C列)のセル範囲情報(Column/Row)とマスターテーブルのIDを格納する配列  function ValidationDataWorkSheetRecordAddから参照される

    protected $aryValidationTail;   // バリデーションデータを適用するデータシート最下部白行のセル範囲情報(Column/Row)とマスターテーブルのIDを格納する配列   function ValidationDataWorkSheetTailerFixから参照される

    protected $aryValidationTailHeader; // バリデーションデータを適用するデータシート最下部白行のヘッダーのセル範囲情報(Column/Row)とマスターテーブルのIDを格納する配列  function ValidationDataWorkSheetTailerFixから参照される

    const DATA_START_COL = 3;
    const WHITE_ROWS = 10;
    const DATA_START_ROW_ON_MASTER = 2;

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        global $g;
        parent::__construct($strFormatterId, $objTable, $strPrintTableId);
        ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_PHPExcel_Classes.txt", 1);
        require_once "PHPExcel.php";

        $this->setTemplateFilePath($g['objMTS']->getTemplateDirPath()."/".$g['objMTS']->getLanguageFullVersion()."_dumpTemplate.xlsx");

        $this->setPrintTargetListFormatterID($strFormatterId);

        $this->setRGBOfSendForbiddenColumn("D9D9D9");
        $this->setRGBOfLastContentRow("00459D");

        $this->aryValidation = array();
        $this->aryValidationHeader = array();
        $this->aryValidationTail = array();
        $this->aryValidationTailHeader = array();
    }
    static function cr2s($column, $row){
        return PHPExcel_Cell::stringFromColumnIndex($column).$row;
    }

    function cashModeAdjust($intMode=0){
        global $g;

        switch($intMode){
            default:
                $strCacheDirPath = $g['root_dir_path'] . "/temp";
                $casheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cashSettings = array('dir'=>$strCacheDirPath);
                PHPExcel_Settings::setCacheStorageMethod($casheMethod,$cashSettings);
                break;
        }
    }

    function setTemplateFilePath($filePath, $requireFormatFlag=TRUE, $bodyTopColumn=3, $bodyTopRow=10){
        // 絶対パス指定かつアプリ内のディレクトリのみに設定可能)
        if( checkRiskOfDirTraversal($filePath)===false ){
            if( mb_strpos($filePath, getApplicationRootDirPath(), 0, 'UTF-8')===0 ){
                $this->templateFilePath = $filePath;
                $this->requireFormatFlag = $requireFormatFlag;
                $this->bodyTopRow = $bodyTopRow;
                $this->bodyTopColumn = $bodyTopColumn;
            }
        }
    }

    function getTemplateFilePath(){
        return $this->templateFilePath;
    }

    function setExportFilePath($filePath){
        // 標準出力または絶対パス指定かつアプリ内のディレクトリのみに設定可能)
        if($filePath == "php:/"."/output"){
            // 標準出力する
            $this->strExportFilePath = $filePath;
        }
        else{
            if( checkRiskOfDirTraversal($filePath)===false ){
                if( mb_strpos($filePath, getApplicationRootDirPath(), 0, 'UTF-8')===0 ){
                    $this->strExportFilePath = $filePath;
                }
            }
        }
    }

    function getExportFilePath(){
        return $this->strExportFilePath;
    }

    //----独自CSV向けの場合の出力用
    function setPrintTargetListFormatterID($strPrintTargetListFormatterId){
        $this->strPrintTargetListFormatterId = $strPrintTargetListFormatterId;
    }
    function getPrintTargetListFormatterID(){
        return $this->strPrintTargetListFormatterId;
    }
    //独自CSV向けの場合の出力用----

    function setRGBOfSendForbiddenColumn($strRRGGBB){
        $this->strRGBOfSendForbiddenColumn = $strRRGGBB;
    }
    function getRGBOfSendForbiddenColumn(){
        return $this->strRGBOfSendForbiddenColumn;
    }
    
    function setRGBOfLastContentRow($strRRGGBB){
        $this->strRGBOfLastContentRow = $strRRGGBB;
    }
    function getRGBOfLastContentRow(){
        return $this->strRGBOfLastContentRow;
    }

    // マスターのシート
    function makeMasterSheet($X, $formatName="", $mode=0, $sheetName=""){
        global $g;
        $strTextExplain01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16101");

        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $aryObjColumn = $this->objTable->getColumns();
        $objREBFColumn = $aryObjColumn[$lcRequiredRowEditByFileColumnId];

        $sheet = $X->createSheet();
        if( $mode == 0 ){
            $sheet->setTitle($strTextExplain01);
        }else if( $mode == 1 ){
            //----開発者モード
            $sheet->setTitle($sheetName);
            //開発者モード----
        }
        $intFocusRowOfEditSheet = 1;
        $intCountAddColOfEditSheet = 0;
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
                if( $varMinorPrintTypeMode!="forDeveloper" ){
                    continue;
                }
            }
            //----ヘッダを追加
            $sheet->setCellValue(self::cr2s(self::DATA_START_COL+$intCountAddColOfEditSheet,1), $objColumn->getColLabel(true));
            //ヘッダを追加----
            if( is_a($objColumn, "IDColumn") === true ){
                $intFocusRowOfMasterSheet = self::DATA_START_ROW_ON_MASTER;
                if( $mode == 0 ){
                    //----入力制限版をフィルタに設定する
                    $arrayDispSelectTag = $objColumn->getMasterTableArrayForInput();
                    //入力制限版をフィルタに設定する----
                }else if( $mode == 1 ){
                    //----開発者モード
                    $arrayDispSelectTag = null;
                    if( $formatName != "" ){
                        $arrayDispSelectTag = $objColumn->getArrayMasterTableByFormatName($formatName);
                    }
                    if( $arrayDispSelectTag === null ){
                        $arrayDispSelectTag = $objColumn->getMasterTableArrayForInput();
                    }
                    $arrayDispSelectTag = array_keys($arrayDispSelectTag);
                    //開発者モード----
                }

                $intCountAdd = 0;
                foreach($arrayDispSelectTag as $data){
                    $intCountAdd += 1;
                    $sheet->setCellValue(self::cr2s(self::DATA_START_COL+$intCountAddColOfEditSheet, $intFocusRowOfMasterSheet++), $data);
                }
                if( 0 < $intCountAdd ){
                    $intSetRow = $intFocusRowOfMasterSheet - 1;
                }
                else{
                    $intSetRow = self::DATA_START_ROW_ON_MASTER;
                }
                $range = self::cr2s(self::DATA_START_COL+$intCountAddColOfEditSheet, self::DATA_START_ROW_ON_MASTER).":".self::cr2s(self::DATA_START_COL+$intCountAddColOfEditSheet, $intSetRow);
                $namedRange = new PHPExcel_NamedRange("FILTER_".$objColumn->getID(), $sheet, $range);
                $X->addNamedRange($namedRange);
            }
            $intCountAddColOfEditSheet++;
        }
        //----処理種別を追加
        $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,1),$objREBFColumn->getColLabel());
        $intFocusRowOfMasterSheet = self::DATA_START_ROW_ON_MASTER;
        $intCountAdd = 0;
        foreach($objREBFColumn->getCommandArrayForEdit() as $data){
            $intCountAdd += 1;
            $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1, $intFocusRowOfMasterSheet++), $data);
        }
        if( 0 < $intCountAdd ){
            $intSetRow = $intFocusRowOfMasterSheet - 1;
        }
        else{
            $intSetRow = self::DATA_START_ROW_ON_MASTER;
        }
        
        $range = self::cr2s(self::DATA_START_COL-1, self::DATA_START_ROW_ON_MASTER).":".self::cr2s(self::DATA_START_COL-1, $intSetRow);
        $namedRange = new PHPExcel_NamedRange("FILTER_".$objREBFColumn->getID(), $sheet, $range);
        $X->addNamedRange($namedRange);

        //処理種別を追加----
    }

    // フィルターのシート
    function makeFilterSheet($X){
        global $g;

        $strTextExplain01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16102");
        $strTextExplain02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16103");

        $strTextDisuse01    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16104");
        $strTextDisuse02    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16105");
        $strTextAllRecord   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16106");
        $strTextWithBlank   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16107");
        $strTextWithNoBlank = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16108");

        $sheet = $X->createSheet();
        $sheet->setTitle($strTextExplain01);
        $i_row = 1;
        $i_col = 0;

        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID();
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $sheet->setCellValue("A1", $strTextExplain02);
        $sheet->setCellValue("A2", date("Y/m/d H:i:s"));

        foreach($this->objTable->getColumns() as $objColumn){
            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId || $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
            }else if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
            }

            //----カラム別の、基本フィルターの値を取得
            $arrayBasicFilterValue = $objColumn->getFilterValues();
            //カラム別の、基本フィルターの値を取得----

            //----カラム別の、リッチ・フィルターの値を取得
            $arrayRichFilterValue = $objColumn->getRichFilterValues();
            //カラム別の、リッチ・フィルターの値を取得----

            //----NULL検索リクエストを取得
            $boolNullSearch = $objColumn->getNullSearchExecute();
            //NULL検索リクエストを取得----

            $i_row = 2;
            $int_element_count = 0;
            $aryFilterElement = array();

            if( is_a($objColumn, "DelBtnColumn") === true ){
                //-----廃止フラグだけ特別対応
                if( array_key_exists(0,$arrayBasicFilterValue)===true ){
                    $value = $arrayBasicFilterValue[0]==="0"?$strTextDisuse01:$strTextDisuse02;
                }else{
                    $value = $strTextAllRecord;//"全レコード";
                }
                $int_element_count += 1;
                $aryFilterElement[] = $value;
                //廃止フラグだけ特別対応----
            }else{
                if( $boolNullSearch === true ){
                    $value = $strTextWithBlank;//"{空白}含む";
                    $int_element_count += 1;
                }else{
                    $value = $strTextWithNoBlank;//"{空白}なし";
                }
                $aryFilterElement[] = $value;
    
                if( 1 <= count($arrayBasicFilterValue) ){
                    if( $objColumn->getSearchType() == "range" ) {
                        $value=isset($arrayBasicFilterValue[0])?$arrayBasicFilterValue[0]:"";
                        $value.="～";
                        $value.=isset($arrayBasicFilterValue[1])?$arrayBasicFilterValue[1]:"";
                    }else{
                        $value = $arrayBasicFilterValue[0];
                    }
                    $int_element_count += 1;
                }else{
                    $value = "";
                }
                $aryFilterElement[] = $value;
            }
            if( 1 <= $int_element_count + count($arrayRichFilterValue) ){
                //----ヘッダ追加
                $sheet->setCellValue(self::cr2s(self::DATA_START_COL+$i_col,1), $objColumn->getColLabel(true));
                //ヘッダ追加----
                //----条件を付加
                if( 1 <= count($arrayRichFilterValue) ){
                    if( is_a($objColumn, "IDColumn") === true ){
                        //IDは文字列に変換
                        $aryFilterData = $objColumn->getMasterTableArrayFromMainTable();
                        foreach($arrayRichFilterValue as $value){
                            $value = $aryFilterData[$value];
                            $aryFilterElement[] = $value;
                        }
                    }else if( is_a($objColumn, "IDRelaySearchColumn") === true ){
                        if( $objColumn->getAddSelectTagPrintType()===0 ){
                            //----表示列側での仮想マスタテーブルモードの場合
                            foreach($arrayRichFilterValue as $value){
                                $aryFilterElement[] = $value;
                            }
                            //表示列側での仮想マスタテーブルモードの場合----
                        }else{
                            $aryFilterData = $objColumn->getPrimeMasterTableArray();
                            foreach($arrayRichFilterValue as $value){
                                $value = $aryFilterData[$value];
                                $aryFilterElement[] = $value;
                            }
                        }
                    }else{
                        foreach($arrayRichFilterValue as $value){
                            $aryFilterElement[] = $value;
                        }
                    }
                }
                foreach($aryFilterElement as $value){
                    $sheet->setCellValueExplicitByColumnAndRow(self::DATA_START_COL+$i_col, $i_row++ ,$value, PHPExcel_Cell_DataType::TYPE_STRING);
                }
                //条件を付加----
                $i_col++;
            }
        }
    }

    function writeHeader($sheet, $objColumn, $pos){
        //ヘッダを出力
        $c = null;
        $boolAdd = false;

        $intColumnHeaderLength = $objColumn->getStaticRowLevel();

        for($r = $intColumnHeaderLength; $r > 0; --$r){
            if($c === null){
                $c = $objColumn;
            }else{
                $c = $c->getParent();
            }
            if( get_class($c) == "ColumnGroup" ){
                //----カラムグループの場合
                $realValue = $c->getColGrpLabel();
                $cgObjIdKey = "CG-".$c->getColGrpSeqNo();
                //カラムグループの場合----
            }else{
                //----実体カラムの場合
                $realValue = $c->getColLabel();
                $cgObjIdKey = "CO-".$c->getColumnSeqNo();
                //実体カラムの場合----
            }
            if( array_key_exists($cgObjIdKey, $this->tempBufferForHeader)===false ){
                $boolAdd = true;
                $this->tempBufferForHeader[$cgObjIdKey] = array();
                $this->tempBufferForHeader[$cgObjIdKey][0] = $realValue;
            
                $this->tempBufferForHeader[$cgObjIdKey][1] = array(self::DATA_START_COL+$pos-1, $r);
            }
            $sheet->setCellValue(self::cr2s(self::DATA_START_COL+$pos-1, $r), $cgObjIdKey);
        }
    }

    function mergeHeader($sheet){
        //ヘッダ行をマージする
        $this->objTable->getColGroup()->calcSpanLength($this->strPrintTargetListFormatterId);
        $rows = $this->objTable->getColGroup()->getHRowCount($this->strPrintTargetListFormatterId)-1;
        $cols = $this->objTable->getColGroup()->getHColCount($this->strPrintTargetListFormatterId)-1;

        //----結合する
        foreach($this->tempBufferForHeader as $strSafeName=>$aryValue){
            $boolLoopFlag1 = true;
            $boolLoopFlag2 = true;
            $arySafeName = explode("-",$strSafeName);
            if( $arySafeName[0] == "CG" ){
                //----カラムグループの場合
                $count = 0;
                $headCol = $aryValue[1][0];
                $headRow = $aryValue[1][1];
                $focusCol = $headCol;
                do{
                    $focusCol += 1;
                    $current = self::cr2s($focusCol, $headRow);
                    if( $sheet->getCell($current)->getValue() == $strSafeName ){
                    }else{
                        $boolLoopFlag1 = false;
                        break;
                    }
                }while($boolLoopFlag1===true);
                $lastCol = $focusCol - 1;
                $lastRow = $headRow;
                //カラムグループの場合----
            }else{
                //----実体カラムの場合
                $headCol = $aryValue[1][0];
                $headRow = $aryValue[1][1];
                $lastCol = $headCol;
                $focusRow = $headRow;
                do{
                    $focusRow += 1;
                    $current = self::cr2s($headCol, $focusRow);
                    if( $rows < $focusRow ){
                        $boolLoopFlag2 = false;
                        break;
                    }else if( $sheet->getCell($current)->getValue() == "" ){
                        //----空白の場合は探査を続ける
                        //空白の場合は探査を続ける----
                    }else{
                        $boolLoopFlag2 = false;
                        break;
                    }
                }while($boolLoopFlag2===true);
                $lastRow = $focusRow - 1;
                //実体カラムの場合----
            }
            if( $headCol != $lastCol || $headRow != $lastRow ){
                $sheet->mergeCells(self::cr2s($headCol, $headRow).":".self::cr2s($lastCol, $lastRow));
            }
        }
        //結合する----
        
        //----名前を置き換える
        foreach($this->tempBufferForHeader as $strSafeName=>$aryValue){
            $strRealName = $aryValue[0];
            $sheet->setCellValue(self::cr2s($aryValue[1][0], $aryValue[1][1]), $strRealName);
        }
        //名前を置き換える----
        
        //ヘッダの左側も縦にマージする
        if($rows>1){
            $sheet->mergeCells(self::cr2s(0,1).":".self::cr2s(1, $rows));
            $sheet->mergeCells(self::cr2s(2,1).":".self::cr2s(2, $rows));
        }
    }

    function workBookCreate(){
        $objWB = null;
        $this->objFocusWB = null;
        if( file_exists($this->getTemplateFilePath()) === true ){
            $objReader = PHPExcel_IOFactory::createReader("Excel2007");
            $objWB = $objReader->load($this->getTemplateFilePath());
        }
        else{
            $objWB = new PHPExcel();
        }
        $this->objFocusWB = $objWB;
        $this->headerRows = $this->objTable->getColGroup()->getHRowCount($this->strPrintTargetListFormatterId) - 1;
        $this->bodyTopRow = $this->headerRows + 8;
        return $objWB;
    }

    function editHelpWorkSheetAdd(){
        global $g;
        $X = $this->objFocusWB;

        $strTextExplain01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16201");

        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        //----マスタ用シートの作成
        $this->makeMasterSheet($X,"",0);

        if( $varMinorPrintTypeMode == "forDeveloper" ){
            //----マスターの鍵値が付加されたシートを追加する
            $this->makeMasterSheet($X,"",1,$strTextExplain01);
            //マスターの鍵値が付加されたシートを追加する----
        }
        //マスタ用シートの作成----

        //フィルタ条件用シートの作成
        $this->makeFilterSheet($X);
    }

    function editWorkSheetHeaderCreate(){
        global $g;
        $strTextExplain03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16203");
        $strTextExplain04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16204");
        $strTextExplain05 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16205");
        $strTextExplain06 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16206");
        $strTextExplain07 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16207");

        $this->objFocusWB->setActiveSheetIndex(0);
        $sheet = $this->objFocusWB->getActiveSheet();

        $strRRGGBBSendForbiddenColumn = "FF".$this->getRGBOfSendForbiddenColumn();

        $this->aryEditSheetDescription = array();
        $this->intEditSheetRecord = 0;
        $this->intEditSheetMaxCol = 0;

        $this->tempBufferForHeader = array();

        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        $lcRequiredNoteColId = $this->objTable->getRequiredNoteColumnID();  //"NOTE"
        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $aryObjColumn = $this->objTable->getColumns();
        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $colREBFName = $aryObjColumn[$lcRequiredRowEditByFileColumnId]->getColLabel();

        $strSheetName = $this->getSheetNameForEditSheet();

        $sheet->setTitle($strSheetName);
        //シート名の設定----
        
        $i_row = 1;
        $i_col = 0;
        $maxCol = 0;
        //ヘッダ行数分の行を追加する(1行分はもともとあるので-1)
        $insertRows = $this->headerRows-1;
        if( $insertRows > 0 ){
            $sheet->insertNewRowBefore(2,$insertRows);
        }

        if( $varMinorPrintTypeMode == "" ){
            //----処理種別にバリデーション設定
            $dataValidation = new PHPExcel_Cell_DataValidation();
            $dataValidation->setFormula1("FILTER_".$lcRequiredRowEditByFileColumnId);
            $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $dataValidation->setShowErrorMessage(true);
            $dataValidation->setShowDropDown(true);
            $tmpAddress = self::cr2s(self::DATA_START_COL-1, $this->bodyTopRow);
            $sheet->setDataValidation($tmpAddress, $dataValidation);
            //処理種別にバリデーション設定----
        }

        //書式により桁落ちが発生するのでデータより先にスタイルを設定しておく
        $columnCount = count($aryObjColumn);

        foreach($aryObjColumn as $objColumn){
            //----ヘッダのスタイル設定

            if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                continue;
            }else if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
                if( $varMinorPrintTypeMode!="forDeveloper"){
                    continue;
                }
            }       
            $maxCol++;
            //ヘッダはマージ等めんどくさい処理があるので配列じゃなくて個別に書き込む
            $this->writeHeader($sheet, $objColumn, $maxCol);

            for($r = 1; $r <= $this->bodyTopRow; ++$r){
                $sheet->duplicateStyle($sheet->getStyle(self::cr2s(self::DATA_START_COL,$r)), self::cr2s(self::DATA_START_COL+$i_col, $r));
            }
            $sheet->getColumnDimensionByColumn(self::DATA_START_COL+$i_col)->setWidth(16);

            //----ボディ1行目の書式設定
            $cellAddress = self::cr2s(self::DATA_START_COL+$i_col, $this->bodyTopRow);
            if( $objColumn->isAllowSendFromFile() == false ){
                //----更新不可をグレーアウト
                $sheet->getStyle($cellAddress)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($strRRGGBBSendForbiddenColumn);
                //更新不可をグレーアウト----
            }else{
                //----更新可能は網掛けしない
                $sheet->getStyle($cellAddress)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
                //更新可能は網掛けしない----
            }
            if( $objColumn->getID() == $lcRequiredUpdateDate4UColumnId ){
                $sheet->getStyle($cellAddress)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($strRRGGBBSendForbiddenColumn);
                $sheet->getCell($cellAddress)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getColumnDimensionByColumn(self::DATA_START_COL+$i_col)->setVisible(false);
            }
            if( is_a($objColumn, "MultiTextColumn") ===true ){
                $sheet->getStyle($cellAddress)->getAlignment()->setWrapText(true);
            }
            $i_col++;

            //ヘッダのスタイル設定----
        }
    
        //ヘッダの横方向のマージ
        $this->mergeHeader($sheet);

        //ヘッダデータ(1回目の名前より後を定義
        $tmp_array = array("required1"=>array(), "required2"=>array(), "required3"=>array(), "required4"=>array(), "retcode"=>array(), "description"=>array(), "name2"=>array());
        //descriptionだけ文字数が長いので別途処理する
        $description_array = array();
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                continue;
            }else if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
                if( $varMinorPrintTypeMode!="forDeveloper"){
                    continue;
                }
            }
            $reqFlag = "";
            if( $objColumn->isAllowSendFromFile()==false){
                //----入力禁止
                $reqFlag = $strTextExplain03; //入力禁止
                $tmp_array['required1'][] = $reqFlag;
                $tmp_array['required2'][] = $reqFlag;
                //入力禁止----
            }else{
                //----入力可
                if( $objColumn->isRequired() === true ){
                    //必須
                    $reqFlag = $strTextExplain04;
                    if( $objColumn->isRegisterRequireExcept()!==false ){
                        $reqFlag = $strTextExplain05;
                    }
                    $tmp_array['required1'][] = $reqFlag;
                    $reqFlag = $strTextExplain04;
                    if( $objColumn->isUpdateRequireExcept()!==false ){
                        $reqFlag = $strTextExplain05;
                    }
                    $tmp_array['required2'][] = $reqFlag;
                }else{
                    //任意
                    $reqFlag = $strTextExplain05;
                    $tmp_array['required1'][] = $reqFlag;
                    $tmp_array['required2'][] = $reqFlag;
                }
                //入力可----
            }

            if( $objColumn->getID() == $lcRequiredNoteColId ){
                if( $objColumn->isRequiredWhenDeleteOn() == true ){
                    $tmp_array['required3'][] = $strTextExplain04;
                }else{
                    $tmp_array['required3'][] = $strTextExplain05;
                }               
                if( $objColumn->isRequiredWhenDeleteOff() == true ){
                    $tmp_array['required4'][] = $strTextExplain04;
                }else{
                    $tmp_array['required4'][] = $strTextExplain05;
                }
            }else{
                $tmp_array['required3'][] = $strTextExplain03;
                $tmp_array['required4'][] = $strTextExplain03;
            }
            $tmp_array['retcode'][] = is_a($objColumn, "MultiTextColumn")?$strTextExplain06:$strTextExplain07;
            $tmp_array['description'][] = "";
            $objFocusOT = $objColumn->getOutputType($this->strPrintTargetListFormatterId);
            $description_array[] = $objFocusOT->getDescription();
            
            $tmp_array['name2'][] = $objColumn->getOutputHeader($this->strPrintTargetListFormatterId);
        }

        //----ヘッダの二次元配列をExcelに張り付ける[ボディに適用するとバグるのでヘッダのみに利用(2014-08-11-1901)]
        $sheet->fromArray($tmp_array, "null",  self::cr2s(self::DATA_START_COL, $this->headerRows+1));
        //ヘッダの二次元配列をExcelに張り付ける[ボディに適用するとバグるのでヘッダのみに利用(2014-08-11-1901)]----
        
        $this->intEditSheetMaxCol = $maxCol;
        $this->aryEditSheetDescription = $description_array;
        
        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }
        $this->tempBufferForHeader = null;
    }

    function editWorkSheetRecordAdd(){
        $this->objFocusWB->setActiveSheetIndex(0);
        $sheet = $this->objFocusWB->getActiveSheet();

        $aryObjColumn = $this->objTable->getColumns();
        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }
        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        $lcRequiredNoteColId = $this->objTable->getRequiredNoteColumnID();  //"NOTE"
        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $colREBFName = $aryObjColumn[$lcRequiredRowEditByFileColumnId]->getColLabel();

        $intAddRecordOnThisCall = 0;
        $aryObjRow = $this->objTable->getRows();
        $rowCount = count($aryObjRow);

        $intThisStartRow = $this->bodyTopRow + $this->intEditSheetRecord;
        
        $intEditSheetRecordCount = $this->intEditSheetRecord; // データ開始行取得
        $strFormula1FilterID = "";             // マスターテーブルのID文字列用変数 setFormula1引数用の結合文字列に利用
        $aryValidationCellPropaties = array();

        $i_col = 0;
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                continue;
            }else if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible() === false ){
                if( $varMinorPrintTypeMode!="forDeveloper" ){
                    continue;
                }
            }
            // データシート開始行フェッチ時初回のみバリデーション設定列情報を取得
            if( $intEditSheetRecordCount === 0 ){

                if( is_a($objColumn, "IDColumn") === true ){
                    //----IDColumnは文字をマスタテーブルのIDに置き換える。
                    if($varMinorPrintTypeMode == ""){

                        $strFormula1FilterID = $objColumn->getID();

                        // バリデーション設定適用対象のセル情報(Column/Row)とマスタテーブルのIDを取得
                        $arraykey = self::DATA_START_COL+$i_col;              // フェッチ時のバリデーション対象カラムの列数(int)
                        $aryValidationCellPropaties[0] = $intThisStartRow;    // フェッチ時の行数(int)
                        $aryValidationCellPropaties[1] = $strFormula1FilterID;// マスタテーブルのID

                        // 取得したセル個々のアドレス情報とマスタテーブルのIDを配列化
                        $this->aryValidation += array( $arraykey => $aryValidationCellPropaties );
                    }
                    //IDColumnは文字をマスタテーブルのIDに置き換える。----
                }
            }

            //----ボディのスタイル(データ1行目のスタイルをコピー)
            $sheet->duplicateStyle($sheet->getStyleByColumnAndRow(self::DATA_START_COL+$i_col, $this->bodyTopRow), 
                self::cr2s(self::DATA_START_COL+$i_col, $intThisStartRow).":".self::cr2s(self::DATA_START_COL+$i_col, $intThisStartRow+$rowCount));
            //ボディのスタイル(データ1行目のスタイルをコピー)----
            $i_col++;
        }

        //処理種別の書式設定
        //ボディのスタイル(データ1行目のスタイルをコピー)
        $sheet->duplicateStyle($sheet->getStyleByColumnAndRow(self::DATA_START_COL-1, $this->bodyTopRow),
                 self::cr2s(self::DATA_START_COL-1, $intThisStartRow).":".self::cr2s(self::DATA_START_COL-1, $intThisStartRow+$rowCount));

        $i_row = $intThisStartRow;
        if( $varMinorPrintTypeMode == "" ){
            
            foreach($aryObjRow as $row){
                $tmp_row_array = array();
                $i_col = self::DATA_START_COL;
                foreach($aryObjColumn as $objColumn){
                    if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                        continue;
                    }else{
                        if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
                            if( $varMinorPrintTypeMode!="forDeveloper"){
                                continue;
                            }
                        }
                        $rowData = $row->getRowData();
                        $inputType=0;
                        $focusValue = $objColumn->getOutputBody($this->strPrintTargetListFormatterId, $rowData);
                        if( is_a($objColumn, "FileUploadColumn") === true ){
                            if( array_key_exists($objColumn->getID(), $rowData) && $rowData[$objColumn->getID()] != "" ){

                                if( $objColumn->getFileHideMode() === false ){
                                    //----ファイル隠蔽モードではない

                                    //----クライアントからファイルへアクセスするためのURLを取得
                                    $url = $objColumn->getOAPathToFUCItemPerRow($rowData);

                                    $localPath = $objColumn->getLAPathToFUCItemPerRow($rowData);

                                    if(file_exists($localPath)===true){
                                        $hyperLink = new PHPExcel_Cell_Hyperlink($url);
                                        //ハイパーリンクのスタイル(下線、青色)
                                        $sheet->getStyle(self::cr2s($i_col, $i_row))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
                                        $sheet->getStyle(self::cr2s($i_col, $i_row))->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                                        $sheet->setHyperLink(self::cr2s($i_col, $i_row), $hyperLink);
                                    }
                                    //ファイル隠蔽モードではない----
                                }
                                $focusValue = $rowData[$objColumn->getID()];
                            }
                        }elseif( get_class($objColumn) == "NumColumn" ){
                            $inputType = 1;
                        }

                        if($inputType == 0){
                            //----文字列として
                            $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row ,$focusValue, PHPExcel_Cell_DataType::TYPE_STRING);
                            //文字列として----
                        }else{
                            $sheet->setCellValueByColumnAndRow($i_col, $i_row ,$focusValue);
                        }
                        $i_col++;
                    }
                }
                $intAddRecordOnThisCall++;
                $this->intEditSheetRecord++;
                $i_row++;
            }
        }else{
            //----開発者用
            $tmp_row_array = array();
            $i_col = self::DATA_START_COL;
            $tempArrayRoleName = array(
                $this->objTable->getRIColumnID()=>"WorkPriKey",
                $this->objTable->getRequiredDisuseColumnID()=>"WorkDisuse",
                $this->objTable->getRequiredLastUpdateDateColumnID()=>"WorkUpdateTime",
                $this->objTable->getRequiredUpdateDate4UColumnID()=>"WorkUpdateCheck",
                $this->objTable->getRequiredLastUpdateUserColumnID()=>"WorkUpdateUser",
                $this->objTable->getRequiredNoteColumnID()=>"WorkNote"
            );
            
            foreach($aryObjColumn as $objColumn){
                if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                    continue;
                }else{
                    if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible()===false ){
                        if( $varMinorPrintTypeMode!="forDeveloper" ){
                            continue;
                        }
                    }
                    $focusValue = $objColumn->getID();

                    if($varMinorPrintTypeMode == "forDeveloper"){
                        // 開発者用
                        $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row ,$focusValue, PHPExcel_Cell_DataType::TYPE_STRING);
                    }else{
                        // 素材用
                        $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row ,"-", PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    if( array_key_exists($focusValue,$tempArrayRoleName)===true ){
                        $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row+1 ,$tempArrayRoleName[$focusValue], PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $i_col++;
                }
            }
            unset($tempArrayRoleName);
            //開発者用----
        }

        //----処理種別カラムの設定

        $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,1),$colREBFName);
        $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$this->bodyTopRow-1),$colREBFName);


        $aryValidationHeaderPropaties = array();
        $dataValidation = $sheet->getDataValidation(self::cr2s(self::DATA_START_COL-1, $this->bodyTopRow));
        if($varMinorPrintTypeMode == ""){
            for($i = $intThisStartRow; $i <= $intThisStartRow+$rowCount; ++$i){
                $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$i),"-");
            }
            // データシート開始行フェッチ時初回のみ処理種別カラムのバリデーション設定列情報を取得
            if( $intEditSheetRecordCount === 0 ){
                $this->aryValidationHeader[0] = $intThisStartRow; // バリデーション設定適用対象のセルアドレス情報(Row)
                $this->aryValidationHeader[1] = $dataValidation;  // 処理種別行のカラム1列のみなので、行数とバリデーションオブジェクトそのもの(C9より)を取得して追加する
            }
        }else{
            if($varMinorPrintTypeMode == "forDeveloper"){
                // 開発者用
                $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$this->bodyTopRow+0),$lcRequiredRowEditByFileColumnId);
            }else{
                // 素材用
                $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$this->bodyTopRow+0),"-");
            }
            $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$this->bodyTopRow+1),"WorkType");
        }
        //処理種別カラムの設定----

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }
    }

    /*
        Validationルール適用処理関数
        function editWorkSheetRecordAdd,function editWorkSheetTailerFixで取得したセル情報(アドレス・マスターテーブルのID)から
        データシート最上位行からデータシートTail部(白行)最下行の範囲へ一括してバリデーションを設定する
    */
    function ValidationDataWorkSheetAdd(){

        // シートオブジェクト取得
        $this->objFocusWB->setActiveSheetIndex(0);
        $sheet = $this->objFocusWB->getActiveSheet();

        $aryObjColumn = $this->objTable->getColumns();

        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();
        $colREBFName = $aryObjColumn[$lcRequiredRowEditByFileColumnId]->getColLabel();

        if(count($this->aryValidation)){
            /* 本体部 バリデーション設定  */
            foreach ( $this->aryValidation as $key => $aryValidationParamTop ) {
                $TopRowColumnNum = $key;
                list( $intPositionTopRow, $strFormula1FilterIDTopRow ) = $aryValidationParamTop;
    
                foreach ( $this->aryValidationTail as $key => $aryValidationParamLast ) {
                    $LastRowColumnNum = $key;
                    list( $intPositionTailTopRow, $strFormula1FilterIDLastRow ) = $aryValidationParamLast;
                    $intPositionTailLastRow = $intPositionTailTopRow + self::WHITE_ROWS;
                    /*
                       1行目のカラムとそれと同列の最終行のカラムの存在を確認し、1行目のカラムアドレス：最終行のカラムアドレスの範囲文字列を作成する。
                       両方のカラムに紐づくマスタテーブルのIDをsetFormula1の引数として”FILTER_”に結合する
                    */
                    if ( $TopRowColumnNum === $LastRowColumnNum && $strFormula1FilterIDTopRow === $strFormula1FilterIDLastRow ){
                        $strTopAddress = self::cr2s( $TopRowColumnNum, $intPositionTopRow );
                        $strLastAddress = self::cr2s( $LastRowColumnNum, $intPositionTailLastRow);
    
                        $strPreAreaAddress = $strTopAddress . ":" . $strLastAddress;
    
                        $dataValidation = new PHPExcel_Cell_DataValidation();
                        $dataValidation->setFormula1( "FILTER_".$strFormula1FilterIDTopRow );
                        $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                        $dataValidation->setShowErrorMessage(true);
                        $dataValidation->setShowDropDown(true);
                        $sheet->setDataValidation( $strPreAreaAddress , $dataValidation);
    
                        unset($dataValidation);
                        $strPreAreaAddress = NULL;
    
                        continue;
                    }else{
                        // 開発用
                    }
                }
            }
            /* 本体部 バリデーション設定  ここまで */
        }

        /* Header部(処理種別) バリデーション設定 設定 */
        if( $varMinorPrintTypeMode == "" ){    //#1293-----
            list( $intPositionHeaderTopRow, $objFormula1FiltIDHeadTopRow ) = $this->aryValidationHeader;
            list( $intPositionHeaderTailTopRow, $objFormula1FiltIDHeadTailTopRow ) = $this->aryValidationTailHeader;
            /*
                1行目のカラムとそれと同列の最終行のカラムの存在を確認し、1行目のカラムアドレス：最終行のカラムアドレスの範囲文字列を作成する。
                両方のカラムに紐づくバリデーション設定オブジェクト(マスタテーブルのIDから生成）をsetDataValidationの引数とする
            */
            if ( $objFormula1FiltIDHeadTopRow === $objFormula1FiltIDHeadTailTopRow ){
                $strHeaderTopAddress = self::cr2s(self::DATA_START_COL-1, $intPositionHeaderTopRow );
                $intPositionHeaderTailTLastRow = $intPositionHeaderTailTopRow + self::WHITE_ROWS;    // 最終行数算出
                $strHeaderLastAddress = self::cr2s(self::DATA_START_COL-1, $intPositionHeaderTailTLastRow );
    
                $strHeadPreAreaAddress = $strHeaderTopAddress . ":" . $strHeaderLastAddress;         // 設定列範囲
                $sheet->setDataValidation( $strHeadPreAreaAddress , $objFormula1FiltIDHeadTopRow );
                $strHeadPreAreaAddress = NULL;
    
            }else{
                // 開発用
            }
        }
        /* Header部(処理種別) バリデーション設定設定  ここまで */

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }
    }

    function editWorkSheetTailerFix(){
        global $g;
        $strTextExplain08 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16208");

        $strFontNameOnExcel = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16209");//"メイリオ";

        $this->objFocusWB->setActiveSheetIndex(0);
        $sheet = $this->objFocusWB->getActiveSheet();

        $aryObjColumn = $this->objTable->getColumns();
        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }
        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        $lcRequiredNoteColId = $this->objTable->getRequiredNoteColumnID();  //"NOTE"
        $lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();

        $colREBFName = $aryObjColumn[$lcRequiredRowEditByFileColumnId]->getColLabel();

        $strRRBBGGLastContentRow = "FF".$this->getRGBOfLastContentRow();

        $intThisStartRow = $this->bodyTopRow + $this->intEditSheetRecord;

        $strFormula1FilterID = "";             // マスターテーブルのID用変数 setFormula1用の結合文字列に利用
        $aryValidationCellPropaties = array();

        $i_col = 0;
        foreach($aryObjColumn as $objColumn){
            if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                continue;
            }else if( $objColumn->getOutputType($this->strPrintTargetListFormatterId)->isVisible() === false ){
                if( $varMinorPrintTypeMode!="forDeveloper"){
                    continue;
                }
            }
            if( is_a($objColumn, "IDColumn") === true ){
                //----IDColumnは文字をマスタテーブルのIDに置き換える。
                if($varMinorPrintTypeMode == ""){
                    $strFormula1FilterID = $objColumn->getID();
                    $arraykey = self::DATA_START_COL+$i_col;               // Cell column number
                    $aryValidationCellPropaties[0] = $intThisStartRow;     // Cell Start row number
                    $aryValidationCellPropaties[1] = $strFormula1FilterID; // Cell Formula1 Filter ColumnID
                    // ヴァリデーション対象カラムのアドレス情報とフィルターカラムを配列化
                    $this->aryValidationTail += array( $arraykey => $aryValidationCellPropaties );
                }
            }   //IDColumnは文字をマスタテーブルのIDに置き換える。----

            //----ボディのスタイル(データ1行目のスタイルをコピー)
            $sheet->duplicateStyle($sheet->getStyleByColumnAndRow(self::DATA_START_COL+$i_col, $this->bodyTopRow), 
                    self::cr2s(self::DATA_START_COL+$i_col, $intThisStartRow).":".self::cr2s(self::DATA_START_COL+$i_col, $intThisStartRow+self::WHITE_ROWS));
            //ボディのスタイル(データ1行目のスタイルをコピー)----

            $i_col++;
        }

        //処理種別の書式設定
        //ボディのスタイル(データ1行目のスタイルをコピー)
        $sheet->duplicateStyle($sheet->getStyleByColumnAndRow(self::DATA_START_COL-1, $this->bodyTopRow),
                 self::cr2s(self::DATA_START_COL-1, $intThisStartRow).":".self::cr2s(self::DATA_START_COL-1, $intThisStartRow+self::WHITE_ROWS));

        //----処理種別カラムの設定

        $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,1),$colREBFName);
        $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$this->bodyTopRow-1),$colREBFName);

        $dataValidation = $sheet->getDataValidation(self::cr2s(self::DATA_START_COL-1, $this->bodyTopRow));
        if($varMinorPrintTypeMode == ""){
            for($i = $intThisStartRow; $i <= $intThisStartRow+self::WHITE_ROWS; ++$i){
                $sheet->setCellValue(self::cr2s(self::DATA_START_COL-1,$i),"-");
            }
            $this->aryValidationTailHeader[0] = $intThisStartRow;                  // Column Top
            $this->aryValidationTailHeader[1] = $dataValidation;                   // ValidationObject
        }
        //処理種別カラムの設定----

        $maxCol = $this->intEditSheetMaxCol;

        //----最終白行の次の行に、注意書きの行を追加する
        $lastRowNumber = $intThisStartRow + self::WHITE_ROWS;
        $lastColNumber = self::DATA_START_COL + $maxCol - 1;

        //$strMessage = "行を増やす場合は、この行より上の行をコピーして挿入下さい。";
        $strMessage = $strTextExplain08;

        $sheet->getStyleByColumnAndRow(0, $lastRowNumber+1)->getFont()->getColor()->setARGB("FFFFFFFF");
        $sheet->setCellValueExplicitByColumnAndRow(0, $lastRowNumber+1 ,$strMessage, PHPExcel_Cell_DataType::TYPE_STRING);

        for($fnv1=0;$fnv1<=$lastColNumber;$fnv1++){
            $sheet->getStyleByColumnAndRow($fnv1, $lastRowNumber+1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow($fnv1, $lastRowNumber+1)->getFill()->getStartColor()->setARGB($strRRBBGGLastContentRow);
            $sheet->getStyleByColumnAndRow($fnv1, $lastRowNumber+1)->getFont()->setName($strFontNameOnExcel);
            $sheet->getStyleByColumnAndRow($fnv1, $lastRowNumber+1)->getFont()->setSize(8);
        }

        //幅指定とウィンドウ枠の固定とオートフィルタ
        //オートに設定後、幅を計算、オート設定を戻す
        for($i_col = self::DATA_START_COL-1; $i_col <= $maxCol; ++$i_col){
            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i_col))->setAutoSize(true);
        }
        $sheet->calculateColumnWidths();
        for($i_col = self::DATA_START_COL-1; $i_col <= $maxCol; ++$i_col){
            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i_col))->setAutoSize(false);
        }

        $description_array = $this->aryEditSheetDescription;

        $sheet->fromArray($description_array, "null", self::cr2s(self::DATA_START_COL, $this->bodyTopRow-2));
        $sheet->freezePane(self::cr2s(self::DATA_START_COL, $this->bodyTopRow));
        $sheet->setAutoFilter(self::cr2s(self::DATA_START_COL-1, $this->bodyTopRow-1).":".self::cr2s(self::DATA_START_COL-1+$maxCol,$intThisStartRow+self::WHITE_ROWS));
    

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }
    }

    /*
        Validationルール適用処理関数 Tail部(白行)のみ適用タイプ 新規登録用やテーブルレコードなしで”ColumnID"をもつEXCELファイルへバリデーションルールを適用する
    */
    function ValidationDataWorkSheetTailerFix() {

        // シートオブジェクト取得
        $this->objFocusWB->setActiveSheetIndex(0);
        $sheet = $this->objFocusWB->getActiveSheet();

        $aryObjColumn = $this->objTable->getColumns();

        $varMinorPrintTypeMode = $this->getGeneValue("minorPrintTypeMode");

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $lcRequiredRowEditByFileColumnId = $this->objTable->getRequiredRowEditByFileColumnID();
        $colREBFName = $aryObjColumn[$lcRequiredRowEditByFileColumnId]->getColLabel();

        /* Tail本体部 バリデーション設定 */
        if( count($this->aryValidationTail) ){
            foreach ( $this->aryValidationTail as $key => $aryValidationParam ) {
                $ColumnNum = $key;
                list( $intPositionTailTopRow, $strFormula1FilterTailID ) = $aryValidationParam;
                $intPositionTailLastRow = $intPositionTailTopRow + self::WHITE_ROWS; // 最下行数取得
                /*
                     1行目のカラムとそれに同列対応する最終行のカラムの存在を確認し、1行目のカラムアドレス：最終行のカラムアドレスの範囲文字列を作成する。
                     両方のカラムに紐づくFormula1フィルター文字列をsetFormula1の引数として”FILTER_”に結合する
                */
                $strTailTopAddress = self::cr2s( $ColumnNum, $intPositionTailTopRow );
                $strTailLastAddress = self::cr2s( $ColumnNum, $intPositionTailLastRow );
                $strTailPreAreaAddress = $strTailTopAddress . ":" . $strTailLastAddress;
                $dataValidation = new PHPExcel_Cell_DataValidation();
                $dataValidation->setFormula1( "FILTER_".$strFormula1FilterTailID );
                $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST );
                $dataValidation->setShowErrorMessage(true);
                $dataValidation->setShowDropDown(true);
                $sheet->setDataValidation( $strTailPreAreaAddress , $dataValidation );

                unset($dataValidation);
                $strPreAreaAddress = NULL;

            }
        }
        /* Tail本体部 バリデーション設定  ここまで */
       
        /* Tail Header部(処理種別) バリデーション設定 */
        if( $varMinorPrintTypeMode == "" ){

            list( $intPositionTailHeadTopRow, $objFormula1FilterTailID ) = $this->aryValidationTailHeader;

            $intPositionTailHeadLastRow = $intPositionTailHeadTopRow + self::WHITE_ROWS;

            $strTailHeadTopAddress = self::cr2s(self::DATA_START_COL-1, $intPositionTailHeadTopRow );
            $strTailHeadLastAddress = self::cr2s(self::DATA_START_COL-1, $intPositionTailHeadLastRow );

            $strTailHeadPreAreaAddress = $strTailHeadTopAddress . ":" . $strTailHeadLastAddress;
            $sheet->setDataValidation( $strTailHeadPreAreaAddress , $objFormula1FilterTailID);

            $strHeadPreAreaAddress = NULL;
        }
        /* Tail Header部(処理種別) バリデーション設定 ここまで */

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }
    }

    // クラス(Table)のメソッド(getPrintFormat)から呼ばれる。
    function format(){
        $retBool = false;

        try{
            $X = $this->objFocusWB;

            //----保存
            $objWriter = PHPExcel_IOFactory::createWriter($X, "Excel2007");
            //----ファイルへの保存または（引数が空白の場合）標準出力へ
            $ret = $objWriter->save($this->getExportFilePath());
            //ファイルへの保存または（引数が空白の場合）標準出力へ----
            //保存----

            $X->disconnectWorksheets();
            unset($X);
            $retBool = true;
        }
        catch(Exception $e){
            web_log($e->getMessage());
            $retBool = false;
        }
        return $retBool;
    }

    function getSheetNameForEditSheet(&$refBoolSetting=true){
        global $g;
        $strText01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16210");  //"匿名テーブル";
        $strText02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-16211");  //"履歴";
        //----シート名の設定
        $strSheetName = $this->getGeneValue("sheetNameForEditByFile");
        if( $strSheetName == "" ){
            $strSheetName = $this->objTable->getDBMainTableLabel();
        }
        if( $this->checkForbiddenPattern($strSheetName)===false ){
            //----使用禁止文字が設定されていた
            $strSheetName = $this->objTable->getDBMainTableLabel();
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-21001"));
            $refBoolSetting = false;
            //使用禁止文字が設定されていた----
        }
        if( 31 <= mb_strlen($strSheetName, "UTF-8") ){
            //----32文字以上だった
            $strSheetName = $strText01;
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-21002"));
            $refBoolSetting = false;
            //32文字以上だった----
        }
        if( $strSheetName==$strText02 ){
            //----エクセルの予約語の場合
            $strSheetName = $strText01;
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-21003"));
            $refBoolSetting = false;
            //エクセルの予約語の場合----
        }
        return $strSheetName;
    }

    function selectResultFetch($sql, $arrayFileterBody, $objTable, $intXlsLimit, $objFunction01ForOverride, $strFormatterId, $filterData, $aryVariant, &$arySetting){
        global $g;
        $intControlDebugLevel01=250;

        $intRowLength = null;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strFxName = __CLASS__."::".__FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            if( is_callable($objFunction01ForOverride) !== true ){
                $intFetchCount = 0;
                
                //----標準selectResultFetch句
                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    while ( $row = $objQuery->resultFetch() ){
                        $intFetchCount += 1;
                        if( $intXlsLimit === null || $intFetchCount <= $intXlsLimit ){
                            $objTable->addData($row, false);
                            //----注意ポイント（エクセルフォーマッタへデータ転写）
                            $this->editWorkSheetRecordAdd();
                            //注意ポイント（エクセルフォーマッタへデータ転写）----
                            $objTable->setData(array());
                        }
                    }

                    // ----取得したレコード数を取得
                    $intRowLength = $intFetchCount;
                    // 取得したレコード数を取得----
                    unset($objQuery);
                }
                else{
                    $intErrorType = 501;
                    throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                unset($retArray);
                //標準selectResultFetch句----
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($arrayFileterBody, $objTable, $intXlsLimit, $strFormatterId, $filterData, $aryVariant, $arySetting);
                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryErrMsgBody = $tmpAryRet[2];
                    throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                $intRowLength = $tmpAryRet[0];
                unset($tmpAryRet);
            }
        }
        catch(Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",$tmpErrMsgBody));
        }

        $retArray = array($intRowLength,$intErrorType,$aryErrMsgBody,$strErrMsg);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
}

class TableFormatter extends ListFormatter {
    /* TableFormatter : HTMLテーブル(複数行)形式のフォーマッタ*/
    
    private $aryKeyvarAnyValstrCssClassName;  //as string tr自体のclass　今は廃止フラグ用

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        parent::__construct($strFormatterId, $objTable, $strPrintTableId);
        $this->aryKeyvarAnyValstrCssClassName = array();
        $this->addRowClass("defaultExplainRow");
    }

    function addRowClass($strCssClassName){
        $this->aryKeyvarAnyValstrCssClassName[] = $strCssClassName;
    }

    function getRowClasses(){
        return $this->aryKeyvarAnyValstrCssClassName;
    }

    function headerFormat($aryObjColumn){
        //tableヘッダー（見出し）を返す
        $retStrVal = "";
        
        $intControlDebugLevel01 = 250;

        $classes = "";
        if( 0 < count($this->getRowClasses())){
            $classes = implode(" ",$this->getRowClasses());
        }
        $retStrVal = $this->objTable->getColGroup()->getHeaderHtml($this->strFormatterId, "", $classes);

        return $retStrVal;
    }

    function bodyFormat($aryObjColumn, $aryObjRow){
        //tableボディ（行）を返す
        $retStrVal = "";

        $intControlDebugLevel01 = 250;
        $intControlDebugLevel02 = 250;

        $intPrintSeq=0;
        foreach($aryObjColumn as $objColumn){
            $focusObjOT = $objColumn->getOutputType($this->strFormatterId);
            if($focusObjOT->isVisible()===true){
                $focusObjOT->setPrintSeq($intPrintSeq);
                $intPrintSeq += 1;
            }
        }

        $intRecCount = 0;
        foreach($aryObjRow as $row){
            //----行数分だけループする
            $intRecCount += 1;

            $classes ="";
            if(is_array($row->getRowClasses())===true){
                if( 0 < count($row->getRowClasses())){
                    $classes = 'class="'.implode(" ",$row->getRowClasses()).'"';
                }
            }

            $rowStr = "<tr {$classes} valign=\"top\">";
            //----PHPカラム数のだけループする
            foreach($aryObjColumn as $objColumn){
                $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $row->getRowData());
            }
            //PHPカラム数のだけループする----
            $rowStr .="</tr>\n";
            $retStrVal .= $rowStr;

            //行数分だけループする----
        }

        return $retStrVal;
    }

    function footerFormat($aryObjColumn){
        $retStrVal = "";

        $intControlDebugLevel01 = 250;

        return $retStrVal;
    }

    function format($strPrintTableTagId = null){
        //----クラス(Table)のメソッド(getPrintFormat)から呼ばれる。
        $retStrVal = "";

        $intControlDebugLevel01 = 250;
        $strFormatterBody = "";

        if( $strPrintTableTagId === null ){
            //----引数Nullの場合はデフォルトのID属性を入れる
            $strPrintTableTagId = $this->strPrintTableId;
            //引数Nullの場合はデフォルトのID属性を入れる----
        }

        $aryObjColumn = $this->objTable->getColumns();
        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef($this);
        }

        $aryObjRow = $this->objTable->getRows();
        $strFormatterBody = $this->bodyFormat($aryObjColumn, $aryObjRow);

        if( $strFormatterBody == ""){
        }else{
            $retStrVal = "<table id=\"{$strPrintTableTagId}\">\n";

            $retStrVal .= $this->headerFormat($aryObjColumn);
            $retStrVal .= $strFormatterBody;
            $retStrVal .= $this->footerFormat($aryObjColumn);

            $retStrVal .= "</table>\n";
        }

        foreach($aryObjColumn as $objColumn){
            $objColumn->setFormatterRef(null);
        }

        return $retStrVal;
    }
}

class SingleRowTableFormatter extends TableFormatter {
    /* SingleRowTableFormatter : HTMLテーブル(単一行)形式のフォーマッタ
        主キーが見つかる場合はその行を、見つからない場合は新規の行を返す
        更新時は主キーを指定、新規時は何も指定しない
    */

    protected $strNumberForRI;
    function addRowClass($strCssClassName){
        $this->aryKeyvarAnyValstrCssClassName[] = $strCssClassName;
    }
    function getRowClasses(){
        return $this->aryKeyvarAnyValstrCssClassName;
    }

    function setNumberForRI($strNumberForRI){
        $this->strNumberForRI = $strNumberForRI;
    }

    function bodyFormat($aryObjColumn, $aryObjRow){
        //tableボディ（行）を返す
        $retStrVal = "";

        $intControlDebugLevel01 = 250;

        $objRowFinded = null;
        $intCount = 0;

        //----複数行が見つかった場合も、想定
        foreach($aryObjRow as $objRow){
            $intCount += 1;
            if( 0 < strlen($this->strNumberForRI) ){
                $arySingleRow = $objRow->getRowData();
                if( array_key_exists($this->objTable->getRIColumnID(), $arySingleRow) === true ){
                    if( $arySingleRow[$this->objTable->getRIColumnID()] == $this->strNumberForRI ){
                        $objRowFinded = $objRow;
                        break;
                    }
                }
            }else{
                if($intCount === 1){
                    $objRowFinded = $objRow;
                }
            }
        }
        //複数行が見つかった場合も、想定----

        $classes ="";
        if( isset($objRowFinded) && is_array($objRowFinded->getRowClasses()) && count($objRowFinded->getRowClasses())>0){
            $classes = 'class="'.implode(" ", $objRowFinded->getRowClasses()).'"';
        }
        $tmpStr = "";

        if( isset($objRowFinded) === true ){
            $outputRowData = $objRowFinded->getRowData();
        }else{
            $outputRowData = null;
        }
        //----表示上のシーケンス値(幅を変更するための仕込み)
        $intPrintSeq=0;
        foreach($aryObjColumn as $objColumn){
            $focusObjOT = $objColumn->getOutputType($this->strFormatterId);
            if( $focusObjOT->isVisible() === true ){
                $focusObjOT->setPrintSeq($intPrintSeq);
                $intPrintSeq += 1;
            }
        }
        //表示上のシーケンス値(幅を変更するための仕込み)----

        foreach($aryObjColumn as $objColumn){
            $tmpStr .= $objColumn->getOutputBody($this->strFormatterId, $outputRowData);
        }

        if( $tmpStr == "" ){
            $retStrVal = "";
        }else{

            $retStrVal = "<tr {$classes} valign=\"top\">";
            $retStrVal .= $tmpStr;
            $retStrVal .= "</tr>\n";

        }

        return $retStrVal;
    }
}

class RegisterTableFormatter extends SingleRowTableFormatter{
    function format($strPrintTableTagId = null){
        return parent::format($strPrintTableTagId);
    }

    function getModeTypeName($arySetting){
        global $g;
        $strModeTypeName = $g['objMTS']->getSomeMessage("ITAWDCH-STD-351");
        if(array_key_exists("system_action_names",$arySetting)===true){
            if(array_key_exists("register",$arySetting['system_action_names'])===true){
                $strModeTypeName = $arySetting["system_action_names"]["register"];
            }
        }
        return $strModeTypeName;
    }

    function printWebUIStartForm($arySetting,$objTable){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix2_1";
            $strShowTable01WrapDivClass = "fakeContainer_Register2";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $g['objMTS']->getSomeMessage("ITAWDCH-STD-30052");
        //共通----
        if(array_key_exists("register_start_scene", $arySetting)===true){
            $strOutputStr  = $arySetting['register_start_scene'];
        }else{
            $strStart01ButtonShow    = true;
            $strStart01ButtonFace    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-352",$strModeTypeName);
            $strStart01ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strStart01ButtonJsFxName = "register_async";
            $strStart01ButtonJsFxVars = "1";
            $strStart01ButtonJsFxAddVars = "";
            if(array_key_exists("register_start_setting", $arySetting)===true){
                $tmpArray1Setting = $arySetting["register_start_setting"];
                if( array_key_exists("Start01Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Start01Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strStart01ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strStart01ButtonFace = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strStart01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strStart01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strStart01ButtonJsFxAddVars = $tmpArray2Setting['JsFunction'];
                    unset($tmpArray2Setting);
                }
                unset($tmpArray1Setting);
            }
            $strStart01ButtonBody=($strStart01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strStart01ButtonFace}\" onClick=location.href=\"javascript:{$strStart01ButtonJsFxPrfx}{$strStart01ButtonJsFxName}({$strStart01ButtonJsFxVars}{$strStart01ButtonJsFxAddVars});\" >":"";
            $strOutputStr = $strStart01ButtonBody;
        }
        return $strOutputStr;
    }

    function printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix2_1";
            $strShowTable01WrapDivClass = "fakeContainer_Register2";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        if(array_key_exists("register_edit_scene", $arySetting)===true){
            $strOutputStr  = $arySetting['register_edit_scene'];
        }else{
            if(array_key_exists("register_default_row", $aryVariant)===true){
                $tmpEditDefaultRow = $aryVariant['register_default_row'];
                $objTable->addData($tmpEditDefaultRow);
                unset($tmpEditDefaultRow);
            }

            $strEdit01ButtonShow     = true;
            $strEdit01ButtonFace     = $g['objMTS']->getSomeMessage("ITAWDCH-STD-354");
            $strEdit01ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit01ButtonJsFxName = "pre_register_async";
            $strEdit01ButtonJsFxVars = "0";
            $strEdit01ButtonJsFxAddVars = "";

            $strEdit02ButtonShow     = true;
            $strEdit02ButtonFace     = $strModeTypeName;
            $strEdit02ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit02ButtonJsFxName = "register_async";
            $strEdit02ButtonJsFxVars = "2";
            $strEdit02ButtonJsFxAddVars = "";

            if(array_key_exists("register_edit_setting", $arySetting)===true){
                $tmpArray1Setting = $arySetting["register_edit_setting"];
                if( array_key_exists("Edit01Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit01Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit01ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit01ButtonFace = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                if( array_key_exists("Edit02Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit02Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit02ButtonShow = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit02ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit02ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                unset($tmpArray1Setting);
            }
            $strEdit01ButtonBody=($strEdit01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strEdit01ButtonFace}\" onClick=location.href=\"javascript:{$strEdit01ButtonJsFxPrfx}{$strEdit01ButtonJsFxName}({$strEdit01ButtonJsFxVars}{$strEdit01ButtonJsFxAddVars});\" >":"";
            $strEdit02ButtonBody=($strEdit02ButtonShow===true)?"<input class=\"disableAfterPush\" type=\"button\" value=\"{$strEdit02ButtonFace}\" onClick=location.href=\"javascript:{$strEdit02ButtonJsFxPrfx}{$strEdit02ButtonJsFxName}({$strEdit02ButtonJsFxVars}{$strEdit02ButtonJsFxAddVars});\" >":"";
            $strOutputStr = 
<<< EOD
            <div class="{$strShowTable01WrapDivClass}">
EOD;

            $strOutputStr .= $objTable->getPrintFormat($strFormatterId, $strShowTable01TagId);

            $strOutputStr .= 
<<< EOD
            </div>
            &nbsp&nbsp&nbsp&nbsp※<span class="input_required">*</span>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-353")}<br><br>
            {$strEdit01ButtonBody}
            {$strEdit02ButtonBody}
EOD;
            $strOutputStr .= "<div class=\"editing_flag\" style=\"display:none;\"></div>";
        }
        return $strOutputStr;
    }

    function printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,&$exeRegisterData){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix2_1";
            $strShowTable01WrapDivClass = "fakeContainer_Register2";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        $arrayObjColumn = $objTable->getColumns();
        $numWkPk='';
        $strRIColumnLabel='';
        if(isset($exeRegisterData[$objTable->getRIColumnID()])===false){
        }else{
            $numWkPk = $exeRegisterData[$objTable->getRIColumnID()];
            $strRIColumnLabel = $arrayObjColumn[$objTable->getRIColumnID()]->getColLabel();
        }
        if($numWkPk==''){
            $strFinishMsgMainPartBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-355",$strModeTypeName);
        }else{
            $strFinishMsgMainPartBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-356", array($strModeTypeName, $strRIColumnLabel, $numWkPk));
        }
        
        if(array_key_exists("register_finish_scene", $arySetting)===true){
            $strOutputStr  = $arySetting['register_finish_scene'];
        }else{
            $strFinish01ButtonShow   = true;
            $strFinish01ButtonFace   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-358");
            $strFinish01ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strFinish01ButtonJsFxName = "register_async";
            $strFinish01ButtonJsFxVars = "0";
            $strFinish01ButtonJsFxAddVars = "";


            $strFinish02ButtonShow   = true;
            $strFinish02ButtonFace   = $g['objMTS']->getSomeMessage("ITAWDCH-STD-359");
            $strFinish02ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strFinish02ButtonJsFxName = "register_async";
            $strFinish02ButtonJsFxVars = "1";
            $strFinish02ButtonJsFxAddVars = "";

            if(array_key_exists("register_finish_setting", $arySetting)===true){
                $tmpArray1Setting = $arySetting["register_finish_setting"];
                if( array_key_exists("Finish01Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Finish01Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strFinish01ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strFinish01ButtonFace = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strFinish01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strFinish01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strFinish01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                if( array_key_exists("Finish02Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Finish02Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strFinish02ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strFinish02ButtonShow = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strFinish02ButtonShow = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strFinish02ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strFinish02ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                unset($tmpArray1Setting);
            }
            $strFinish01ButtonBody=($strFinish01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strFinish01ButtonFace}\" onClick=location.href=\"javascript:{$strFinish01ButtonJsFxPrfx}{$strFinish01ButtonJsFxName}({$strFinish01ButtonJsFxVars}{$strFinish01ButtonJsFxAddVars});\" >":"";
            $strFinish02ButtonBody=($strFinish02ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strFinish02ButtonFace}\" onClick=location.href=\"javascript:{$strFinish02ButtonJsFxPrfx}{$strFinish02ButtonJsFxName}({$strFinish02ButtonJsFxVars}{$strFinish02ButtonJsFxAddVars});\" >":"";
            
            $strOutputStr = 
<<< EOD
                    {$strFinishMsgMainPartBody}
                    {$g['objMTS']->getSomeMessage("ITAWDCH-STD-357")}<br>
                    {$strFinish01ButtonBody}
                    {$strFinish02ButtonBody}
EOD;
        }
        return $strOutputStr;
    }

    function printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str){
        global $g;

        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix2_1";
            $strShowTable01WrapDivClass = "fakeContainer_Register2";

            $strFiterTable01TagId = "Filter1Tbl";
        }


        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        $strError01ButtonShow    = true;
        $strError01ButtonFace    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-360");
        $strError01ButtonJsFxPrfx = $strFiterTable01FunctionPreFix;
        $strError01ButtonJsFxName = "search_async";
        $strError01ButtonJsFxVars = "";
        $strError01ButtonJsFxAddVars = "";

        if(array_key_exists("register_error_setting", $arySetting)===true){
            $tmpArray1Setting = $arySetting["register_error_setting"];
            if( array_key_exists("Error01Button",$tmpArray1Setting)===true){
                $tmpArray2Setting = $tmpArray1Setting["Error01Button"];
                if(isset($tmpArray2Setting['Show'])===true) $strError01ButtonShow = $tmpArray2Setting['Show'];
                if(isset($tmpArray2Setting['Face'])===true) $strError01ButtonFace = $tmpArray2Setting['Face'];
                if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strError01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                if(isset($tmpArray2Setting['JsFunctionName'])===true) $strError01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strError01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                unset($tmpArray2Setting);
            }
            unset($tmpArray1Setting);
        }
        $strError01ButtonBody=($strError01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strError01ButtonFace}\" onClick=location.href=\"javascript:{$strError01ButtonJsFxPrfx}{$strError01ButtonJsFxName}({$strError01ButtonJsFxVars}{$strError01ButtonJsFxAddVars});\" >":"";
        $strMsgBody01 = $error_str.$g['objMTS']->getSomeMessage("ITAWDCH-ERR-158",$strModeTypeName);
        $strOutputStr = 
<<< EOD
                        <span class="generalErrMsg">{$strMsgBody01}</span><br>
                        {$strError01ButtonBody}
EOD;

        return $strOutputStr;
    }

    function errorHandleForIUD($arySetting,$intErrorType){
        global $g;
        $arrayRet = array();
        $arrayRet[0] = '';
        $arrayRet[1] = '';
        $strModeTypeName = $g['objMTS']->getSomeMessage("ITAWDCH-STD-351");
        if(array_key_exists("system_action_names",$arySetting)===true){
            if(array_key_exists("register",$arySetting['system_action_names'])===true){
                $strModeTypeName = $arySetting["system_action_names"]["register"];
            }
        }
        switch($intErrorType){
            case 1  : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-155", $strModeTypeName); $arrayRet[0]="003";break;//----権限なし
            case 2  : break; //----バリデーションエラー
            case 901: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-156", $strModeTypeName); break;//----SYSTEMエラーによるスキップ
            default : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-157"); break;//----一般エラー
        }
        return $arrayRet;
    }
}

class UpdateTableFormatter extends SingleRowTableFormatter{
    function format($strPrintTableTagId = null){
        return parent::format($strPrintTableTagId);
    }
    
    function addAfterFormat(){
    
    }

    function getModeTypeName($arySetting){
        global $g;
        $strModeTypeName = $g['objMTS']->getSomeMessage("ITAWDCH-STD-371");
        if(array_key_exists("system_action_names",$arySetting)===true){
            if(array_key_exists("update",$arySetting["system_action_names"])===true){
                $strModeTypeName = $arySetting["system_action_names"]["update"];
            }
        }
        return $strModeTypeName;
    }

    function printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId,$strNumberForRI,$editTgtRow){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Update1";

            $strFiterTable01TagId = "Filter1Tbl";
        }
        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        if(array_key_exists("update_edit_scene", $arySetting)===true){
            $strOutputStr  = $arySetting['update_edit_scene'];
        }else{

            $lcRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"

            $objTable->addData($editTgtRow);

            //----追い越し防止用・隠しタグの埋め込み
            $strOutputStr = 
<<< EOD
                <input type="hidden" class="upd_update_timestamp" name="{$lcRequiredUpdateDate4UColumnId}" style="display:none;" value="{$editTgtRow[$lcRequiredUpdateDate4UColumnId]}" />
                <div class="{$strShowTable01WrapDivClass}">
EOD;
            //追い越し防止用・隠しタグの埋め込み----

            //----更新用テーブルhtmlの出力
            $strOutputStr .= $objTable->getPrintFormat($strFormatterId, $strShowTable01TagId, $strNumberForRI);
            //更新用テーブルhtmlの出力----

            $strEdit01ButtonShow     = true;
            $strEdit01ButtonFace     = $g['objMTS']->getSomeMessage("ITAWDCH-STD-373");
            $strEdit01ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit01ButtonJsFxName = "update_async";
            $strEdit01ButtonJsFxVars = "2,'{$strNumberForRI}'";
            $strEdit01ButtonJsFxAddVars = "";

            $strEdit02ButtonShow     = true;
            $strEdit02ButtonFace     = $strModeTypeName;
            $strEdit02ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit02ButtonJsFxName = "update_async";
            $strEdit02ButtonJsFxVars = "3,'{$strNumberForRI}'";
            $strEdit02ButtonJsFxAddVars = "";

            if(array_key_exists("update_edit_setting", $arySetting)===true){
                $tmpArray1Setting = $arySetting["update_edit_setting"];
                if( array_key_exists("Edit01Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit01Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit01ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit01ButtonFace = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                if( array_key_exists("Edit02Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit02Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit02ButtonShow = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit02ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit02ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                unset($tmpArray1Setting);
            }
            $strEdit01ButtonBody=($strEdit01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strEdit01ButtonFace}\" onClick=location.href=\"javascript:{$strEdit01ButtonJsFxPrfx}{$strEdit01ButtonJsFxName}({$strEdit01ButtonJsFxVars}{$strEdit01ButtonJsFxAddVars});\" >":"";
            $strEdit02ButtonBody=($strEdit02ButtonShow===true)?"<input class=\"disableAfterPush\" type=\"button\" value=\"{$strEdit02ButtonFace}\" onClick=location.href=\"javascript:{$strEdit02ButtonJsFxPrfx}{$strEdit02ButtonJsFxName}({$strEdit02ButtonJsFxVars}{$strEdit02ButtonJsFxAddVars});\" >":"";

            $strOutputStr .= 
<<< EOD
                </div>
                &nbsp&nbsp&nbsp&nbsp※<span class="input_required">*</span>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-372")}<br>
                {$strEdit01ButtonBody}
                {$strEdit02ButtonBody}
EOD;

            $strOutputStr .= "<div class=\"editing_flag\" style=\"display:none;\"></div>";
        }

        return $strOutputStr;
    }

    function printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,&$exeUpdateData){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Update1";

            $strFiterTable01TagId = "Filter1Tbl";
        }
        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        $strOutputStr = $g['objMTS']->getSomeMessage("ITAWDCH-STD-375", $strModeTypeName);

        return $strOutputStr;
    }

    function printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Update1";

            $strFiterTable01TagId = "Filter1Tbl";
        }
        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $strModeTypeName = $this->getModeTypeName($arySetting);
        //共通----
        $strError01ButtonShow    = true;
        $strError01ButtonFace    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-376");
        $strError01ButtonJsFxPrfx = $strFiterTable01FunctionPreFix;
        $strError01ButtonJsFxName = "search_async";
        $strError01ButtonJsFxVars = "";
        $strError01ButtonJsFxAddVars = "";
        if(array_key_exists("update_error_setting", $arySetting)===true){
            $tmpArray1Setting = $arySetting["update_error_setting"];
            if( array_key_exists("Error01Button",$tmpArray1Setting)===true){
                $tmpArray2Setting = $tmpArray1Setting["Error01Button"];
                if(isset($tmpArray2Setting['Show'])===true) $strError01ButtonShow = $tmpArray2Setting['Show'];
                if(isset($tmpArray2Setting['Face'])===true) $strError01ButtonFace = $tmpArray2Setting['Face'];
                if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strError01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                if(isset($tmpArray2Setting['JsFunctionName'])===true) $strError01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strError01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                unset($tmpArray2Setting);
            }
            unset($tmpArray1Setting);
        }
        $strError01ButtonBody=($strError01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strError01ButtonFace}\" onClick=location.href=\"javascript:{$strError01ButtonJsFxPrfx}{$strError01ButtonJsFxName}({$strError01ButtonJsFxVars}{$strError01ButtonJsFxAddVars});\" >":"";

        $strMsgBody01 = $error_str.$g['objMTS']->getSomeMessage("ITAWDCH-ERR-182", $strModeTypeName);
        $strOutputStr = 
<<< EOD
                        <span class="generalErrMsg">{$strMsgBody01}</span><br>
                        {$strError01ButtonBody}
EOD;

        return $strOutputStr;
    }

    function errorHandleForIUD($arySetting,$intErrorType){
        global $g;
        $arrayRet = array();
        $arrayRet[0] = '';
        $arrayRet[1] = '';
        $strModeTypeName = $this->getModeTypeName($arySetting);
        switch($intErrorType){
            case 1  : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-176", $strModeTypeName); $arrayRet[0]="003"; break;//----権限なし
            case 2  : break;//----バリデーションエラー
            case 101: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-177", $strModeTypeName); break;//----行特定できず
            case 201: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-178", $strModeTypeName); $arrayRet[0]="003"; break;//----追い越し更新
            case 212: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-179", $strModeTypeName); $arrayRet[0]="003"; break;//----削除済
            case 901: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-180", $strModeTypeName); break;//----SYSTEMエラーによるスキップ
            default : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-181", $strModeTypeName); break;//----一般エラー
        }
        return $arrayRet;
    }
}

class DeleteTableFormatter extends SingleRowTableFormatter{
    /* DeleteTableFormatter : HTMLテーブル(単一行・削除用)形式のフォーマッタ
    削除用に備考のみ書き込み可能
    */
    function format($strPrintTableTagId = null){
        return parent::format($strPrintTableTagId);
    }

    function getModeTypeName($arySetting,$mode){
        global $g;
        $aryRetBody = array();
        $strModeTypePost01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-391");
        $strModeTypePost02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-392");
        if(array_key_exists("system_action_names",$arySetting)===true){
            if(array_key_exists("delete_on",$arySetting["system_action_names"])===true){
                $strModeTypePost01 = $arySetting["system_action_names"]["delete_on"];
            }
            if(array_key_exists("delete_off",$arySetting["system_action_names"])===true){
                $strModeTypePost02 = $arySetting["system_action_names"]["delete_off"];
            }
        }
        $strModeTypeName="";
        if( $mode==1 || $mode==3 ){
            $strModeTypeName = $strModeTypePost01;
        }else if($mode==4 || $mode==5 ){
            $strModeTypeName = $strModeTypePost02;
        }
        $aryRetBody[0] = $strModeTypeName;
        $aryRetBody[1] = $strModeTypePost01;
        $aryRetBody[2] = $strModeTypePost02;
        return $aryRetBody;
    }

    function printWebUIEditForm($arySetting,$objTable,$aryVariant,$strFormatterId,$strNumberForRI,$editTgtRow,$mode){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Delete1";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $tmpArrayRet = $this->getModeTypeName($arySetting,$mode);
        $strModeTypeName = $tmpArrayRet[0];
        $strModeTypePost01 = $tmpArrayRet[1];
        $strModeTypePost02 = $tmpArrayRet[2];
        //共通----
        $arrayObjColumn = $objTable->getColumns();
        if(array_key_exists("delete_edit_scene", $arySetting)===true){
            $strOutputStr  = $arySetting['update_delete_scene'];
        }else{
            $lcRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID(); //"DISUSE_FLAG"
            $lcRequiredNoteColId = $objTable->getRequiredNoteColumnID(); //"NOTE"

            $objTable->addData($editTgtRow);

            if($mode == 1){
                $disuse_flag = 1;
            }else if($mode == 4){
                $disuse_flag = 0;
            }

            $strOutputStr .= 
<<< EOD
                <input type="hidden" name="{$lcRequiredDisuseFlagColumnId}" value="{$disuse_flag}" />
                <input type="hidden" name="{$lcRequiredUpdateDate4UColumnId}" value="{$editTgtRow[$lcRequiredUpdateDate4UColumnId]}" />
                <div class="{$strShowTable01WrapDivClass}">
EOD;

            if($mode == 1){
                $nextMode = 3;
                $strMarkNextMode = "on";
            }else if($mode ==4){
                $nextMode = 5;
                $strMarkNextMode = "off";
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId)->setGeneValue("PrintForMode",$strMarkNextMode);
            $strOutputStr .= $objTable->getPrintFormat($strFormatterId, $strShowTable01TagId, $strNumberForRI);
            $objListFormatter = $objTable->getFormatter($strFormatterId)->setGeneValue("PrintForMode","",true);

            $objColNote = $arrayObjColumn[$lcRequiredNoteColId];
            if( ( $strMarkNextMode == "on" && $objColNote->isRequiredWhenDeleteOn()===true ) ||
                ( $strMarkNextMode == "off" && $objColNote->isRequiredWhenDeleteOff()===true ) ){
                $strTempMessage = "※<span class=\"input_required\">*</span>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-398")}<br>";
            }else{
                $strTempMessage = "{$g['objMTS']->getSomeMessage("ITAWDCH-STD-393")}<br>";
            }

            $strEdit01ButtonShow     = true;
            $strEdit01ButtonFace     = $g['objMTS']->getSomeMessage("ITAWDCH-STD-394");
            $strEdit01ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit01ButtonJsFxName = "delete_async";
            $strEdit01ButtonJsFxVars = "2,'{$strNumberForRI}'";
            $strEdit01ButtonJsFxAddVars = "";

            $strEdit02ButtonShow     = true;
            $strEdit02ButtonFace     = $strModeTypeName;
            $strEdit02ButtonJsFxPrfx = $strShowTable01FunctionPreFix;
            $strEdit02ButtonJsFxName = "delete_async";
            $strEdit02ButtonJsFxVars = "{$nextMode},'{$strNumberForRI}'";
            $strEdit02ButtonJsFxAddVars = "";

            if(array_key_exists("delete_edit_setting", $arySetting)===true){
                $tmpArray1Setting = $arySetting["delete_edit_setting"];
                if( array_key_exists("Edit01Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit01Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit01ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit01ButtonFace = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];  
                    unset($tmpArray2Setting);
                }
                if( array_key_exists("Edit02Button",$tmpArray1Setting)===true){
                    $tmpArray2Setting = $tmpArray1Setting["Edit02Button"];
                    if(isset($tmpArray2Setting['Show'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Show'];
                    if(isset($tmpArray2Setting['Face'])===true) $strEdit02ButtonShow = $tmpArray2Setting['Face'];
                    if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strEdit02ButtonShow = $tmpArray2Setting['JsFunctionPrefix'];
                    if(isset($tmpArray2Setting['JsFunctionName'])===true) $strEdit02ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                    if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strEdit02ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                    unset($tmpArray2Setting);
                }
                unset($tmpArray1Setting);
            }
            $strEdit01ButtonBody=($strEdit01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strEdit01ButtonFace}\" onClick=location.href=\"javascript:{$strEdit01ButtonJsFxPrfx}{$strEdit01ButtonJsFxName}({$strEdit01ButtonJsFxVars}{$strEdit01ButtonJsFxAddVars});\" >":"";
            $strEdit02ButtonBody=($strEdit02ButtonShow===true)?"<input class=\"disableAfterPush\" type=\"button\" value=\"{$strEdit02ButtonFace}\" onClick=location.href=\"javascript:{$strEdit02ButtonJsFxPrfx}{$strEdit02ButtonJsFxName}({$strEdit02ButtonJsFxVars}{$strEdit02ButtonJsFxAddVars});\" >":"";
            $strOutputStr .= 
<<<EOD
                </div>
      &nbsp&nbsp&nbsp&nbsp{$strTempMessage}
      {$strEdit01ButtonBody}
      {$strEdit02ButtonBody}
EOD;

            $strOutputStr .= "<div class=\"editing_flag\" style=\"display:none;\"></div>";
        }

        return $strOutputStr;
    }

    function printSuccessOnWebUIAfterWebUIAction($arySetting,$objTable,&$exeUpdateData,$mode){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Delete1";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $tmpArrayRet = $this->getModeTypeName($arySetting,$mode);
        $strModeTypeName = $tmpArrayRet[0];
        $strModeTypePost01 = $tmpArrayRet[1];
        $strModeTypePost02 = $tmpArrayRet[2];
        //共通----
        $strOutputStr = $g['objMTS']->getSomeMessage("ITAWDCH-STD-396");

        return $strOutputStr;
    }

    function printErrorOnWebUIAfterWebUIAction($arySetting,$objTable,$intErrorType,$error_str,$mode){
        global $g;
        $strOutputStr ='';
        //----共通
        $strShowTable01TagId = "";
        $strShowTable01WrapDivClass = "";
        $strShowTable01FunctionPreFix = "";
        $strFiterTable01TagId = "";
        $strFiterTable01FunctionPreFix = "";
        //----出力されるタグの属性値
        if(array_key_exists("printTagId",$arySetting)===true){
            $strShowTable01TagId = $arySetting['printTagId'][0];
            $strShowTable01WrapDivClass = $arySetting['printTagId'][1];

            $strFiterTable01TagId = $arySetting['printTagId'][2];
        }else{
            $strShowTable01TagId = "Mix1_1";
            $strShowTable01WrapDivClass = "fakeContainer_Delete1";

            $strFiterTable01TagId = "Filter1Tbl";
        }

        if($objTable->getJsEventNamePrefix()===true){
            $strShowTable01FunctionPreFix = $strShowTable01TagId."_";
            $strFiterTable01FunctionPreFix = $strFiterTable01TagId."_";
        }
        //出力されるTableタグの属性値----
        $tmpArrayRet = $this->getModeTypeName($arySetting,$mode);
        $strModeTypeName = $tmpArrayRet[0];
        $strModeTypePost01 = $tmpArrayRet[1];
        $strModeTypePost02 = $tmpArrayRet[2];
        //共通----
        $strError01ButtonShow    = true;
        $strError01ButtonFace    = $g['objMTS']->getSomeMessage("ITAWDCH-STD-397");
        $strError01ButtonJsFxPrfx = $strFiterTable01FunctionPreFix;
        $strError01ButtonJsFxName = "search_async";
        $strError01ButtonJsFxVars = "";
        $strError01ButtonJsFxAddVars = "";
        if(array_key_exists("delete_error_setting", $arySetting)===true){
            $tmpArray1Setting = $arySetting["delete_error_setting"];
            if( array_key_exists("Error01Button",$tmpArray1Setting)===true){
                $tmpArray2Setting = $tmpArray1Setting["Error01Button"];
                if(isset($tmpArray2Setting['Show'])===true) $strError01ButtonShow = $tmpArray2Setting['Show'];
                if(isset($tmpArray2Setting['Face'])===true) $strError01ButtonFace = $tmpArray2Setting['Face'];
                if(isset($tmpArray2Setting['JsFunctionPrefix'])===true) $strError01ButtonJsFxPrfx = $tmpArray2Setting['JsFunctionPrefix'];
                if(isset($tmpArray2Setting['JsFunctionName'])===true) $strError01ButtonJsFxName = $tmpArray2Setting['JsFunctionName'];
                if(isset($tmpArray2Setting['JsFunctionAddVars'])===true) $strError01ButtonJsFxAddVars = $tmpArray2Setting['JsFunctionAddVars'];
                unset($tmpArray2Setting);
            }
            unset($tmpArray1Setting);
        }
        $strError01ButtonBody=($strError01ButtonShow===true)?"<input class=\"linkbutton\" type=\"button\" value=\"{$strError01ButtonFace}\" onClick=location.href=\"javascript:{$strError01ButtonJsFxPrfx}{$strError01ButtonJsFxName}({$strError01ButtonJsFxVars}{$strError01ButtonJsFxAddVars});\" >":"";
        $strMsgBody01 = $error_str.$g['objMTS']->getSomeMessage("ITAWDCH-ERR-204", $strModeTypeName);
        $strOutputStr = 
<<< EOD
                        <span class="generalErrMsg">{$strMsgBody01}</span><br>
                        {$strError01ButtonBody}
EOD;

        return $strOutputStr;
    }

    function errorHandleForIUD($arySetting,$intErrorType,$mode){
        global $g;
        $arrayRet = array();
        $arrayRet[0] = '';
        $arrayRet[1] = '';
        $tmpArrayRet = $this->getModeTypeName($arySetting,$mode);
        $strModeTypeName = $tmpArrayRet[0];
        $strModeTypePost01 = $tmpArrayRet[1];
        $strModeTypePost02 = $tmpArrayRet[2];
        switch($intErrorType){
            case 1  : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-197", "{$strModeTypePost01}・{$strModeTypePost02}"); $arrayRet[0]="003"; break;//権限なし
            case 2  : break; //----バリデーションエラー
            case 101: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-198",$strModeTypeName); break;//行特定できず
            case 201: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-199",$strModeTypeName); $arrayRet[0]="003"; break;//追い越し更新
            case 211: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-200"); $arrayRet[0]="003"; break;//削除済
            case 221: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-201"); $arrayRet[0]="003"; break;//復活済
            case 901: $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-202",$strModeTypeName); break;//----SYSTEMエラーによるスキップ
            default : $arrayRet[1] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-203"); break;//一般エラー
        }
        return $arrayRet;
    }
}

class FilterConditionTableFormatter extends SingleRowTableFormatter{
}

class SubtotalTableFormatter extends SingleRowTableFormatter {

    function headerFormat($aryObjColumn){
        $retStrVal = "";
        
        $classes = "";
        if( 0 < count($this->getRowClasses())){
            $classes = implode(" ",$this->getRowClasses());
        }
        $retStrVal = $this->objTable->getColGroup()->getHeaderHtml($this->strFormatterId, $classes);
        
        return $retStrVal;
    }
}

class FilterResultTableFormatter extends TableFormatter{
}

class CurrentTableFormatter extends FilterResultTableFormatter{
}

class JournalTableFormatter extends FilterResultTableFormatter {
    /* JournalTableFormatter : ジャーナル表示用のHTMLテーブル形式のフォーマッタ
        可変項目の表示可否はprint_tableと同じなのでprint_tableを流用する形で作成
    */

    protected $intFocusRowIndex;

    function __construct($strFormatterId, $objTable, $strPrintTableId){
        parent::__construct($strFormatterId, $objTable, $strPrintTableId);

        $this->intFocusRowIndex = null;
    }

    function getFocusRowIndex(){
        return $this->intFocusRowIndex;
    }

    function headerFormat($aryObjColumn){
        //tableヘッダー（見出し）を返す
        $retStrVal = "";
        
        $intControlDebugLevel01 = 250;

        $classes = "";
        if( 0 < count($this->getRowClasses())){
            $classes = implode(" ",$this->getRowClasses());
        }

        $retStrVal = $this->objTable->getColGroup()->getHeaderHtml($this->strFormatterId, "", $classes);

        return $retStrVal;
    }

    function bodyFormat($aryObjColumn, $aryObjRow){
        //tableボディ（行）を返す
        $retStrVal = "";

        $intControlDebugLevel01 = 250;

        //「$aryObjColumn」----テーブルが保有する全てのカラム(オブジェクト配列)
        //「$aryObjRow」----テ—ブルが保有する全ての行(オブジェクト配列)【最新のSEQ-＞古いSEQ順に登録されている】

        $strJnlSeqNoColId = $this->objTable->getRequiredJnlSeqNoColumnID();
        $strJnlRegTimeColId = $this->objTable->getRequiredJnlRegTimeColumnID();

        $this->intFocusRowIndex = 0;

        for($intLoopPoint = count($aryObjRow) - 1 ; 0 <= $intLoopPoint ; $intLoopPoint-- ){
            // ----行数分だけループする（古いレコードから処理する）

            $intPrevPoint = $intLoopPoint + 1;
            $arySingleRow = $aryObjRow[$intLoopPoint]->getRowData();
            $intJsn = $arySingleRow[$strJnlSeqNoColId];

            $classes ="";
            if( is_array($aryObjRow[$intLoopPoint]->getRowClasses() === true) && 0 < count($aryObjRow[$intLoopPoint]->getRowClasses()) ){
                $classes = 'class="'.implode(" ",$aryObjRow[$intLoopPoint]->getRowClasses()).'"';
            }

            $rowStr = "<tr {$classes} valign=\"top\">";

            foreach($aryObjColumn as $objColumn){
                $strColId = $objColumn->getID();
                if( $objColumn->isDBColumn() === false ){
                    $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                    continue;
                }else if ( $strColId == $strJnlSeqNoColId || $strColId == $strJnlRegTimeColId ){
                    $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                    continue;
                }
                if($intLoopPoint == count($aryObjRow) - 1 ){
                    // ---- 最初の１行目（もっとも古いレコード）
                    $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                    // 最初の１行目（もっとも古いレコード）----
                }else{
                    if( is_a($objColumn, 'FileUploadColumn') === true ){
                        $strPre = $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intPrevPoint]->getRowData());
                        $strFcs = $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                        $boolDiff = ($strPre !== $strFcs)?true:false;
                    }else{
                        $arySingleRow2 = $aryObjRow[$intPrevPoint]->getRowData();
                        $arySingleRow3 = $aryObjRow[$intLoopPoint]->getRowData();
                        $boolDiff = (nl2br($arySingleRow2[$strColId]) !== nl2br($arySingleRow3[$strColId]))?true:false;
                    }
                    if( $boolDiff === true ){
                        $objColumn->addClass("diff");
                        $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                        $objColumn->delClass("diff");
                    }else{
                        $rowStr .= $objColumn->getOutputBody($this->strFormatterId, $aryObjRow[$intLoopPoint]->getRowData());
                    }
                }
            }

            $rowStr .="</tr>\n";

            $retStrVal = $rowStr . $retStrVal;

            $this->intFocusRowIndex += 1;

            // 行数分だけループする（古いレコードから処理する）----
        }

        $this->intFocusRowIndex = null;

        return $retStrVal;
    }
}
?>
