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
    //  【特記事項】
    //    ・ModuleDistictCode(902)
    //
    //  【処理概要】
    //    ・原則として、データベースから出力されたデータを、もとに、出力タグのみを定義する
    //    ・原則として、データベースへの、アクセスモデルは、CellFormatterクラス内では定義しない
    //    ・原則として、DB出力および他状態を使って生成される論理情報をもとに、見た目をととのえる
    //
    //////////////////////////////////////////////////////////////////////

class CellFormatter {
	//----他のクラスから独立して利用するための設定
	protected $strPrintTargetKey;
	protected $strIDSynonym;
	protected $strPrintSeq;

	protected $strListFormatterMode;
	protected $varDefaultValue;
	protected $varOverrideValue;
	protected $intMaxInputLength;
	protected $boolColumnIdHidden;

	//----通常時
	protected $boolRequired;
	protected $varRegisterRequiredExcept;
	protected $varUpdateRequiredExcept;
	//通常時----

	protected $boolJsFxNamePrefix;
	protected $strRIColumnKey;
	protected $strRequiredDisuseColumnId;
	protected $strRequiredUpdateDate4UColumnId;

	protected $strPrintTableId;

	//他のクラスから独立して利用するための設定----

	//----他のクラスと連動して利用するための変数
	//----参照
	protected $objColumn; // as Column or null
	protected $objOutputType; //as OutputType or null
	//参照----
	protected $strFormatterId;  // as string フォーマット名(旧formatName)
	//他のクラスと連動して利用するための変数----

	protected $strPrintType;
	protected $boolSafingHtmlBeforePrint;

	//----ここから継承メソッドの上書き処理
	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//NEW[1]
	public function __construct(){
		//----他のクラスから独立して利用するための設定
		$this->setPrintTargetKeyAgent(null);
		$this->setIDSynonymAgent(null);
		$this->setPrintSeqAgent(null);

		$this->setListFormatterModeAgent("");
		$this->intMaxInputLength = null;
		$this->varDefaultValue = null;
		$this->varOverrideValue = null;
		$this->setColumnIDHiddenAgent(false);

		$this->setRequiredAgent(false);
		$this->setRegisterRequiredExceptAgent(null);
		$this->setUpdateRequiredExceptAgent(null);

		$this->setRIColumnKeyAgent(null);
		$this->setRequiredDisuseColumnIDAgent("DISUSE_FLAG");
		$this->setRequiredUpdateDate4UColumnIDAgent("UPD_UPDATE_TIMESTAMP");

		$this->setJsFxNamePrefixAgent(false);
		$this->setPrintTableIDAgent("");
		//他のクラスから独立して利用するための設定----

		//----他のクラスと連動して利用するための設定
		$this->objColumn = null;
		$this->objOutputType = null;
		$this->strFormatterId = "";
		//他のクラスと連動して利用するための設定----
		
		$this->setSafingHtmlBeforePrintAgent(true);
	}

	//----「__construct、ではないので注意」
	//NEW[2]
	public function init($objColumn, $strFormatterId){
		global $g;
		try{
			if( is_string($strFormatterId) === false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( gettype($objColumn) != "object" ){
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( is_a($objColumn, "Column") === false ){
				throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$this->objColumn = $objColumn;
			$this->strFormatterId = $strFormatterId;
			$this->objOutputType = $objColumn->getOutputType($strFormatterId);
			if( gettype($this->objOutputType) != "object" ){
				throw new Exception( '00000400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( is_a($this->objOutputType, "OutputType") === false ){
				throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90210101);
        	exit();
		}
	}
	//「__construct、ではないので注意」----

	//----他のクラスから独立して利用するための関数
	//NEW[3]
	public function setPrintTargetKeyAgent($strVal){
		$this->strPrintTargetKey = $strVal;
	}
	//NEW[4]
	public function getPrintTargetKey(){
		$retStrColId = $this->strPrintTargetKey;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retStrColId = $this->objColumn->getID();
			//カラムがセットされている場合----
		}
		return $retStrColId;
	}
	//NEW[5]
	public function setIDSynonymAgent($strIDSynonym){
		$this->strIDSynonym = $strIDSynonym;
	}
	//NEW[6]
	public function getIDSynonym(){
		$strIDSynonym = $this->strIDSynonym;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$strIDSynonym = $this->objColumn->getIDSOP();
			//カラムがセットされている場合----
		}
		return $strIDSynonym;
	}
	//NEW[7]
	public function setPrintSeqAgent($strPrintSeq){
		$this->strPrintSeq = $strPrintSeq;
	}
	//NEW[8]
	public function getPrintSeq(){
		$strPrintSeq = $this->strPrintSeq;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$strPrintSeq = $this->objOutputType->getPrintSeq();
			//カラムがセットされている場合----
		}
		return $strPrintSeq;
	}
	//NEW[9]
	public function setListFormatterModeAgent($strListFormatterMode){
		$this->strListFormatterMode = $strListFormatterMode;
	}
	//NEW[10]
	public function getListFormatterMode(){
		$retValue = $this->strListFormatterMode;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objFormatter = $this->objColumn->getFormatterRef();
			if( is_a($objFormatter, "ListFormatter")===true ){
				$retValue = get_class($objFormatter);
			}else{
				$objTable = $this->objColumn->getTable();
				if( is_a($objTable, "TableControlAgent")===true ){
					$objFormatter = $objTable->getFormatter($this->strFormatterId);
					if( is_a($objFormatter, "ListFormatter")===true ){
						$retValue = get_class($objFormatter);
					}
				}
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[11]
	function checkListFormatterMode($strListFormatterMode){
		$retBool = false;
		if( $this->strListFormatterMode === $strListFormatterMode ){
			$retBool = true;
		}else{
			if( is_a($this->objColumn, "Column")===true ){
				//----カラムがセットされている場合
				$objFormatter = $this->objColumn->getFormatterRef();
				if( is_a($objFormatter, "ListFormatter") === true ){
					if( is_a($objFormatter, $strListFormatterMode) === true ){
						$retBool = true;
					}
				}else{
					$objTable = $this->objColumn->getTable();
					if( is_a($objTable, "TableControlAgent") === true ){
						$objFormatter = $objTable->getFormatter($this->strFormatterId);
						if( is_a($objFormatter, "ListFormatter") === true ){
							if( is_a($objFormatter, $strListFormatterMode) === true ){
								$retBool = true;
							}
						}
					}
				}
				//カラムがセットされている場合----
			}
		}
		return $retBool;
	}
	//NEW[12]
	public function setMaxInputLengthAgent($intMaxInputLength){
		$this->intMaxInputLength = $intMaxInputLength;
	}
	//NEW[13]
	public function getMaxInputLength(){
		// タグに記述されるI要素の入力最大長(属性)
		$retValue = $this->intMaxInputLength;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objOutputType->getAttr('maxLength');
			if($retValue === null){
				$retValue = $this->objColumn->getValidator()->getMaxLength($this->strFormatterId);
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}	

	//NEW[14]
	public function setColumnIDHiddenAgent($boolColumnIdHidden){
		$this->boolColumnIdHidden = $boolColumnIdHidden;
	}
	//NEW[15]
	public function getColumnIDHidden(){
		$boolColumnIdHidden = $this->boolColumnIdHidden;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$boolColumnIdHidden = $this->objColumn->getColumnIDHidden();
			//カラムがセットされている場合----
		}
		return $boolColumnIdHidden;
	}
	//NEW[16]
	public function setRequiredAgent($boolRequired){
		$this->boolRequired = $boolRequired;
	}
	//NEW[17]
	public function getRequired(){
		$retValue = $this->boolRequired;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isRequired();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[18]
	public function setRegisterRequiredExceptAgent($varRegisterRequiredExcept){
		$this->varRegisterRequiredExcept = $varRegisterRequiredExcept;
	}
	//NEW[19]
	public function getRegisterRequiredExcept(){
		$retValue = $this->varRegisterRequiredExcept;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isRegisterRequireExcept();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[20]
	public function setUpdateRequiredExceptAgent($varUpdateRequiredExcept){
		$this->varUpdateRequiredExcept = $varUpdateRequiredExcept;
	}
	//NEW[21]
	public function getUpdateRequiredExcept(){
		$retValue = $this->varUpdateRequiredExcept;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isUpdateRequireExcept();
			//カラムがセットされている場合----
		}
		return $retValue;
	}

	//----TCA必須カラム系
	//NEW[22]
	public function setRIColumnKeyAgent($strRIColumnKey){
		$this->strRIColumnKeyAgent = $strRIColumnKey;
	}
	//NEW[23]
	public function getRIColumnKey(){
		$retStrColId = $this->strRIColumnKey;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$retStrColId = $this->objColumn->getTable()->getRIColumnID();
			}
			//カラムがセットされている場合----
		}
		return $retStrColId;
	}
	//NEW[24]
	public function setRequiredDisuseColumnIDAgent($strRequiredDisuseColumnId){
		$this->strRequiredDisuseColumnId = $strRequiredDisuseColumnId;
	}
	//NEW[25]
	public function getRequiredDisuseColumnID(){
		$strRequiredDisuseColumnId = $this->strRequiredDisuseColumnId;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$strRequiredDisuseColumnId = $this->objColumn->getRequiredDisuseColumnID();
			//カラムがセットされている場合----
		}
		return $strRequiredDisuseColumnId;
	}
	
	//NEW[26]
	public function setRequiredUpdateDate4UColumnIDAgent($strRequiredUpdateDate4UColumnId){
		$this->strRequiredUpdateDate4UColumnId = $strRequiredUpdateDate4UColumnId;
	}
	//NEW[27]
	public function getRequiredUpdateDate4UColumnID(){
		$strRequiredUpdateDate4UColumnId = $this->strRequiredUpdateDate4UColumnId;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$strRequiredUpdateDate4UColumnId = $objTable->getRequiredUpdateDate4UColumnID();
			}
			//カラムがセットされている場合----
		}
		return $strRequiredUpdateDate4UColumnId;
	}
	//TCA必須カラム系----
	
	//NEW[28]	
	public function setJsFxNamePrefixAgent($boolJsFxNamePrefix){
		$this->boolJsFxNamePrefix = $boolJsFxNamePrefix;
	}
	//NEW[29]
	function getJsFxNamePrefix(){
		$boolJsFxNamePrefix = $this->boolJsFxNamePrefix;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$boolJsFxNamePrefix = $objTable->getJsEventNamePrefix();
			}
			//カラムがセットされている場合----
		}
		return $boolJsFxNamePrefix;
	}
	
	
	//NEW[30]
	public function setPrintTableIDAgent($strPrintTableId){
		$this->strPrintTableId = $strPrintTableId;
	}
	//NEW[31]
	function getPrintTableID(){
		$strPrintTableId = $this->strPrintTableId;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$strPrintTableId = $objTable->getPrintingTableId();
			}
			//カラムがセットされている場合----
		}
		return $strPrintTableId;
	}
	
	//NEW[32]
	public function setDefaultValueAgent($varDefaultValue){
		$this->varDefaultValue = $varDefaultValue;
	}
	//NEW[33]
	public function getDefaultValue($boolDelete=false,$boolArrayIgnore=false,$rowData=array(),$aryVariant=array()){
		$data = $this->varDefaultValue;
		if( is_callable($this->objFunctionForDefaultValue) ){
			$objFunction = $this->objFunctionForDefaultValue;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$data = $objFunction($this, $boolDelete, $boolArrayIgnore, $rowData, $aryVariant, $objColumn);
		}else{
			// loadTableで設定されたもの、をそのまま返す
			// $boolArrayIgnoreがtrueの場合、array型のデフォルト値は、nullとして返す。
			if( is_a($this->objColumn, "Column")===true ){
				//----カラムがセットされている場合
				$data = $this->objOutputType->getDefaultInputValue();
				//カラムがセットされている場合----
			}

			if( $data === null){
				// ----NULLだった場合
				// NULLだった場合----
			}else{
				if( $boolDelete === true ){
					if( is_a($this->objColumn, "Column")===true ){
						//----カラムがセットされている場合
						$this->objOutputType->setDefaultInputValue(null);
						//カラムがセットされている場合----
					}else{
						$this->varDefaultValue = null;
					}
				}
			}

			if($boolArrayIgnore===true){
				if(is_array($data)===true){
					//----array型のデフォルト値は、nullとして返す。
					$date = null;
					//array型のデフォルト値は、nullとして返す。----
				}
			}
		}
		return $data;
	}
	
	//他のクラスから独立して利用するための関数----

	//NEW[34]
	public function setOverrideValueAgent($varDefaultValue){
		$this->varOverrideValue = $varDefaultValue;
	}
	//NEW[35]
	public function getOverrideValue($boolDelete=false,$boolArrayIgnore=false,$rowData=array(),$aryVariant=array()){
		if( is_callable($this->objFunctionForOverrideValue) ){
			$objFunction = $this->objFunctionForOverrideValue;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$data = $objFunction($this, $boolDelete, $boolArrayIgnore, $rowData, $aryVariant, $objColumn);
		}else{
			// loadTableで設定されたもの、をそのまま返す
			// $boolArrayIgnoreがtrueの場合、array型のデフォルト値は、nullとして返す。
			$data = $this->varOverrideValue;
			if( is_a($this->objColumn, "Column")===true ){
				//----カラムがセットされている場合
				$data = $this->objOutputType->getOverrideInputValue();
				//カラムがセットされている場合----
			}

			if( $data === null){
				// ----NULLだった場合
				// NULLだった場合----
			}else{
				if( $boolDelete === true ){
					if( is_a($this->objColumn, "Column")===true ){
						//----カラムがセットされている場合
						$this->objOutputType->setOverrideInputValue(null);
						//カラムがセットされている場合----
					}else{
						$this->varOverrideValue = null;
					}
				}
			}

			if($boolArrayIgnore===true){
				if(is_array($data)===true){
					//----array型のデフォルト値は、nullとして返す。
					$date = null;
					//array型のデフォルト値は、nullとして返す。----
				}
			}
		}
		return $data;
	}
	
	//他のクラスから独立して利用するための関数----

	//NEW[36]
	public function setSafingHtmlBeforePrintAgent($boolSafingHtmlBeforePrint){
		$this->boolSafingHtmlBeforePrint = $boolSafingHtmlBeforePrint;
	}
	//NEW[37]
	public function getSafingHtmlBeforePrint(){
		return $this->boolSafingHtmlBeforePrint;
	}

	//NEW[38]
	public function makeSafeHeaderForBrowse($strData){
		$boolValue = $this->getSafingHtmlBeforePrint();
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$boolValue = $this->objColumn->isHeaderSafingForBrowse();
			//カラムがセットされている場合----
		}
		if($boolValue!==false){
			$strData = htmlspecialchars($strData);
		}
		return $strData;
	}
	//NEW[39]
	public function makeSafeValueForBrowse($strData){
		if($this->getSafingHtmlBeforePrint()!==false){
			$strData = htmlspecialchars($strData);
		}
		return $strData;
	}

	//NEW[40]
	public function getValueForDisplay($data){
		return $data;
	}

	//NEW[41]
	public function getSelfInfoForLog(){
		$retStrBody="";
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retStrBody.=$this->objColumn->getSelfInfoForLog();
			//カラムがセットされている場合----
		}else{
			$retStrBody.='[Column]Uninited';
		}
		return $retStrBody;
	}
	//TCA必須カラム系----

	//NEW[42]
	function setFunctionForDefaultValue($varFunctionForDefaultValue){
		$this->objFunctionForDefaultValue = $varFunctionForDefaultValue;
	}
	//NEW[43]
	function getFunctionForDefaultValue(){
		return $this->objFunctionForDefaultValue;
	}

	//NEW[44]
	function setFunctionForOverrideValue($varFunctionForOverrideValue){
		$this->objFunctionForOverrideValue = $varFunctionForOverrideValue;
	}
	//NEW[45]
	function getFunctionForOverrideValue(){
		return $this->objFunctionForOverrideValue;
	}
	//ここまで新規メソッドの定義宣言処理----

}

//----ここからHFmt系
class HFmt extends CellFormatter {

	protected $strDataPreFix;
	protected $strDataPostFix;
	protected $strDescription;
	protected $varStaticPrintRawData;

	//----ここから継承メソッドの上書き処理

	public function __construct(){
		parent::__construct();
		$this->setDataPreFix("");
		$this->setDataPostFix("");
		$this->setDescriptionAgent("");
		$this->setStaticPrintRawDataAgent(null);
		$this->setSafingHtmlBeforePrintAgent(false);  // データベースからHFmtタグが作成されることは、ほぼないので、デフォルトはfalse
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function getData($colNo="", $attr=""){
		$retValue = $this->getStaticPrintRawData();
		return $retValue;
	}

	//NEW[2]
	public function setDataPreFix($strVal){
		$this->strDataPreFix = $strVal;
	}
	//NEW[3]
	public function setDataPostFix($strVal){
		$this->strDataPostFix = $strVal;
	}

	//----他のクラスから独立して利用するための関数
	//NEW[4]
	public function setStaticPrintRawDataAgent($strVal){
		$this->varStaticPrintRawData = $strVal;
	}
	//NEW[5]
	public function getStaticPrintRawData(){
		$retValue = $this->varStaticPrintRawData;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getColLabel();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	//NEW[6]
	public function setDescriptionAgent($strVal){
		$this->strDescription = $strVal;
	}
	//NEW[7]
	public function getDescription(){
		$retValue = $this->strDescription;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objOutputType->getDescription();
			//カラムがセットされている場合----
		}
		return $retValue;
	}

	//他のクラスから独立して利用するための関数----

	//----ここからSTATIC利用
	//ここまでSTATIC利用----

	//ここまで新規メソッドの定義宣言処理----

}

class VariantHFmt extends HFmt {
	protected $objFunctionForGetData;

	//----ここから継承メソッドの上書き処理

	public function getData($colNo="", $attr=""){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetData) ){
			$objFunction = $this->objFunctionForGetData;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $colNo, $attr, $objColumn);
		}else{
			$strSetValue = parent::getData($colNo, $attr);
		}
		return $strSetValue;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	
	//NEW[1]
	function setFunctionForGetData($objFunctionForGetData){
		$this->objFunctionForGetData = $objFunctionForGetData;
	}
	//NEW[2]
	function getFunctionForGetData(){
		return $this->objFunctionForGetData;
	}
	//ここまで新規メソッドの定義宣言処理----
}

class TabHFmt extends HFmt {
	protected $strBasicTagClass;

	//----ここから継承メソッドの上書き処理

	public function __construct(){
		parent::__construct();
		$this->setBasicTagClass("generalBold");
	}

	//----「__construct、ではないので注意」
	public function init($objColumn, $strFormatterId){
		parent::init($objColumn, $strFormatterId);
	}
	//「__construct、ではないので注意」----

	public function getData($colNo="", $attr=""){
		$strPrintBody = $this->makeSafeHeaderForBrowse($this->getStaticPrintRawData());
		$strPrintBody = nl2br($strPrintBody);
		return $this->getTag("{$this->strDataPreFix}<span class=\"{$this->strBasicTagClass}\">{$strPrintBody}</span>{$this->strDataPostFix}", $attr);
		
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function getTag($data, $attr=""){
		//----タグ（先頭と末尾）の結合を取得
		return $this->getSTag($attr).$data.$this->getETag();
	}

	//NEW[2]
	public function getSTag($attr){
		$strTitleBody = "";
		$strTitleData = $this->getDescription();
		if( 0 < strlen($strTitleData) ){
			$strTitleBody = " title=\"".$this->makeSafeHeaderForBrowse($strTitleData)."\"";
		}
		if( $attr === null ){
			$stag = "<th scope=\"col\"{$strTitleBody}>";
		}else{
			$stag = "<th scope=\"col\" {$attr}{$strTitleBody}>";
		}
		return $stag;
	}

	//NEW[3]
	public function getETag(){
		return "</th>\n";
	}

	//----ここからSTATIC利用
	//ここまでSTATIC利用----

	//NEW[4]
	public function setBasicTagClass($strBasicTagClass){
		$this->strBasicTagClass = $strBasicTagClass;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class VariantTabHFmt extends TabHFmt {
	protected $objFunctionForGetData;

	//----ここから継承メソッドの上書き処理

	public function getData($colNo="", $attr=""){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetData) ){
			$objFunction = $this->objFunctionForGetData;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $colNo, $attr, $objColumn);
		}else{
			$strSetValue = parent::getData($colNo, $attr);
		}
		return $strSetValue;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	
	//NEW[1]
	function setFunctionForGetData($objFunctionForGetData){
		$this->objFunctionForGetData = $objFunctionForGetData;
	}
	//NEW[2]
	function getFunctionForGetData(){
		return $this->objFunctionForGetData;
	}
	//ここまで新規メソッドの定義宣言処理----
}

class FilterTabHFmt extends TabHFmt {

	//----ここから継承メソッドの上書き処理

	public function getTag($data, $attr=""){
		//----タグ（先頭と末尾）の結合を取得
		return $this->getSTag($attr).$data.$this->getETag();
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class SortedTabHFmt extends TabHFmt {
	protected $boolIsNum;
	protected $strSortTargetTableTagId;
	protected $intSortTargetColumnNo;
	protected $intSortTargetColumnRow;
	protected $strSortTagClass;
	protected $strSortSortMarkWrapTagClass;

	protected $strSortNotSelectedTagClass;
	protected $strstrSortSelectedAscTagClass;
	protected $strSortSelectedDescTagClass;

	//----ここから継承メソッドの上書き処理
	
	public function __construct(){
		parent::__construct();
		$this->setIsNumAgent(false);
		$this->setSortTargetTableTagIDAgent("");
		$this->setSortTargetColumnNoAgent(null);
		$this->setSortTargetColumnRowAgent(null);
		$this->setSortTagClass("sortTriggerInTbl");
		$this->setSortMarkWrapTagClass("sortMarkWrap");
		$this->setSortNotSelectedTagClass("sortNotSelected");
		$this->setSortSelectedAscTagClass("sortSelectedAsc");
		$this->setSortSelectedDescTagClass("sortSelectedDesc");
	}

	public function getData($colNo, $attr=""){
		$attr2 = "";
		$strPrintBody = $this->makeSafeHeaderForBrowse($this->getStaticPrintRawData());
		$strPrintBody = nl2br($strPrintBody);
		$strSortTarget = $this->getSortTargetTableTagID();
		$strTgtColRow = $this->getSortTargetColumnRow();
		$strTgtColNo  = $this->getSortTargetColumnNo();
		$nsort = $this->getIsNum()?",nsort":",null";

		$attr2  = $attr.' onClick="tableSort('.$strTgtColRow.', this, \''.$strSortTarget.'\', '.$strTgtColNo.' '.$nsort;
		$attr2 .= ',\''.$this->strSortSortMarkWrapTagClass.'\',\''.$this->strSortNotSelectedTagClass.'\',\''.$this->strSortSelectedAscTagClass.'\',\''.$this->strSortSelectedDescTagClass.'\');" ';
		$attr2 .= 'class="'.$this->strSortTagClass.'"';

		$strBody  = "{$this->strDataPreFix}<span class=\"{$this->strBasicTagClass}\">{$strPrintBody}</span>{$this->strDataPostFix}";
		$strBody .= "<span class=\"{$this->strSortSortMarkWrapTagClass}\"><span class=\"{$this->strSortNotSelectedTagClass}\"></span></span>";
		return $this->getTag($strBody, $attr2);
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----他のクラスから独立して利用するための関数
	//NEW[1]
	public function setIsNumAgent($boolIsNum){
		$this->boolIsNum = $boolIsNum;
	}
	//NEW[2]
	public function getIsNum(){
		$retValue = $this->boolIsNum;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isNum();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[3]
	public function setSortTargetTableTagIDAgent($strSortTargetTableTagId){
		$this->strSortTargetTableTagId = $strSortTargetTableTagId;
	}
	//NEW[4]
	public function getSortTargetTableTagID(){
		$retValue = $this->strSortTargetTableTagId;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$retValue = $objTable->getPrintingTableId();
				if( $objTable->getJsEventNamePrefix() === true ){
					$retValue = $objTable->getPrintingTableId();
				}
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[5]	
	public function setSortTargetColumnNoAgent($intSortTargetColumnNo){
		$this->intSortTargetColumnNo = $intSortTargetColumnNo;
	}
	//NEW[6]
	public function getSortTargetColumnNo(){
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getColNo($this->strFormatterId);
			//カラムがセットされている場合----
		}else{
			$retValue = $this->intSortTargetColumnNo;
		}
		return $retValue;
	}
	//NEW[7]
	public function setSortTargetColumnRowAgent($intSortTargetColumnRow){
		$this->intSortTargetColumnRow = $intSortTargetColumnRow;
	}
	//NEW[8]
	public function getSortTargetColumnRow(){
		$retValue = $this->intSortTargetColumnRow;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$retValue = $objTable->getColGroup()->getHRowCount($this->strFormatterId) - 1;
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	//他のクラスから独立して利用するための関数----

	//NEW[9]
	public function setSortTagClass($strSortTagClass){
		$this->strSortTagClass = $strSortTagClass;
	}
	//NEW[10]
	public function setSortMarkWrapTagClass($strSortSortMarkWrapTagClass){
		$this->strSortSortMarkWrapTagClass = $strSortSortMarkWrapTagClass;
	}
	//NEW[11]
	public function setSortNotSelectedTagClass($strSortNotSelectedTagClass){
		$this->strSortNotSelectedTagClass = $strSortNotSelectedTagClass;
	}
	//NEW[12]
	public function setSortSelectedAscTagClass($strSortSelectedAscTagClass){
		$this->strSortSelectedAscTagClass = $strSortSelectedAscTagClass;
	}
	//NEW[13]
	public function setSortSelectedDescTagClass($strSortSelectedDescTagClass){
		$this->strSortSelectedDescTagClass = $strSortSelectedDescTagClass;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class ReqTabHFmt extends TabHFmt {
	protected $boolRequiredNote;

	//----削除復活時
	protected $strNextMode;
	protected $boolDeleteOnRequired;
	protected $boolDeleteOffRequired;
	//削除復活時----

	//----すべての場合で優先されるフラグ
	protected $boolNotRequireBut;
	//すべての場合で優先されるフラグ----

	protected $strInputRequredTagClass;

	//----ここから継承メソッドの上書き処理

	public function __construct($boolNotRequireBut=false){
		parent::__construct();
		$this->boolNotRequireBut = $boolNotRequireBut;

		$this->setRequiredNoteAgent(false);
		$this->setNextModeAgent("");
		$this->setDeleteOnRequiredAgent(false);
		$this->setDeleteOffRequiredAgent(false);
		$this->setInputRequredTagClass("input_required");
	}

	public function getData($colNo="", $attr=""){
		$star = "";
		$strPrintBody = $this->makeSafeHeaderForBrowse($this->getStaticPrintRawData());
		$strPrintBody = nl2br($strPrintBody);
		if( $this->boolNotRequireBut === true) {
			$star = '<span class="'.$this->strInputRequredTagClass.'">*</span>';
		}else{
			if( $this->getRequiredNote() === true ){
				//----必須備考カラムの場合
				if( $this->checkListFormatterMode("DeleteTableFormatter") === true ){
						$strMarkNextMode = $this->getNextMode();
					if( ( $strMarkNextMode == "on" && $this->getDeleteOnRequired()===true ) ||
						( $strMarkNextMode == "off" && $this->getDeleteOffRequired()===true ) ){
						$star = '<span class="'.$this->strInputRequredTagClass.'">*</span>';
					}
				}
				//必須備考カラムの場合----
			}else{
				if( $this->getRequired() === true ){
					$star = '<span class="'.$this->strInputRequredTagClass.'">*</span>';
					if( $this->checkListFormatterMode("RegisterTableFormatter") === true ){
						if( $this->getRegisterRequiredExcept() !== false ){
							$star = "";
						}
					}else if( $this->checkListFormatterMode("UpdateTableFormatter") === true ){
						if( $this->getUpdateRequiredExcept() !== false ){
							$star = "";
						}
					}
				}
			}
		}
		return $this->getTag("{$this->strDataPreFix}<span class=\"{$this->strBasicTagClass}\">".$strPrintBody."</span>{$star}{$this->strDataPostFix}", $attr);
	}

	//----ここからSTATIC利用
	//ここまでSTATIC利用----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----他のクラスから独立して利用するための関数
	//NEW[1]
	public function setRequiredNoteAgent($boolRequiredNote){
		$this->boolRequiredNote = $boolRequiredNote;
	}
	//NEW[2]
	public function getRequiredNote(){
		$retValue = $this->boolRequiredNote;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				if( $objTable->getRequiredNoteColumnID() === $this->getPrintTargetKey() ){
					$retValue = true;
				}
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[3]
	public function setNextModeAgent($strNextMode){
		$this->strNextMode = $strNextMode;
	}
	//NEW[4]
	public function getNextMode(){
		$retValue = $this->strNextMode;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$objFormatter = $objTable->getFormatter($this->strFormatterId);
				if( is_a($objFormatter, "DeleteTableFormatter")===true ){
					$retValue = $objFormatter->getGeneValue("PrintForMode");
				}
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[5]
	public function setDeleteOnRequiredAgent($boolDeleteOnRequired){
		$this->boolDeleteOnRequired = $boolDeleteOnRequired;
	}
	//NEW[6]
	public function getDeleteOnRequired(){
		$retValue = $this->boolDeleteOnRequired;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isRequiredWhenDeleteOn();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[7]
	public function setDeleteOffRequiredAgent($boolDeleteOffRequired){
		$this->boolDeleteOffRequired = $boolDeleteOffRequired;
	}
	//NEW[8]
	public function getDeleteOffRequired(){
		$retValue = $this->boolDeleteOffRequired;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->isRequiredWhenDeleteOff();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	//他のクラスから独立して利用するための関数----

	//NEW[9]
	public function setInputRequredTagClass($strInputRequredTagClass){
		$this->strInputRequredTagClass = $strInputRequredTagClass;
	}

	//ここまで新規メソッドの定義宣言処理----

}

//----ここからHFmt系
class ExcelHFmt extends HFmt {


}

class CSVHFmt extends HFmt {
	protected $strOutputPrintType;
	protected $strColumnSepa;

	//----ここから継承メソッドの上書き処理

	public function __construct(){
		parent::__construct();
		$this->strOutputPrintType = "";
		$this->ColumnSepa = ",";
	}

	public function getData($colNo="", $attr=""){
		$retStrVal = "";
		if($this->strOutputPrintType == "noWrapID" || $this->strOutputPrintType == "wrapID"){
			$strPrintBody = $this->getPrintTargetKey();
		}else if($this->strOutputPrintType == "noWrapLabel" || $this->strOutputPrintType == "wrapLabel"){
			$strPrintBody = $this->getStaticPrintRawData();
		}else{
			// 独自CSV編集エクセル素材用
			$strPrintBody = $this->getStaticPrintRawData();
		}
		if($this->strOutputPrintType == "noWrapID" || $this->strOutputPrintType == "noWrapLabel"){
			$retStrVal = $strPrintBody;
		}else if($this->strOutputPrintType == "wrapID" || $this->strOutputPrintType == "wrapLabel"){
			$retStrVal = '"'.$strPrintBody.'"'.$this->ColumnSepa;
		}else{
			// 独自CSV編集エクセル素材用
			$retStrVal = $strPrintBody;
		}
		return $retStrVal;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function setOutputPrintType($strType, $strColSepa=""){
		$this->strOutputPrintType = $strType;
		$this->ColumnSepa = $strColSepa;
	}
	//NEW[2]
	public function getOutputPrintType(){
		return $this->strOutputPrintType;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class JSONHFmt extends HFmt {


}

//ここまでHFmt系----

//----ここからBFmt系
class BFmt extends CellFormatter {
	protected $strColLabel;
	protected $varFormatterSearchType;
	protected $strColumnSearchType;
	protected $aryFilterValuesForMatchCheck;
	protected $aryRichFilterValuesForMatchCheck;

	//----ここから継承メソッドの上書き処理

	public function __construct(){
		parent::__construct();
		$this->setColLabelAgent(null);
		$this->setFormatterSearchTypeAgent(null);
		$this->setColumnSearchTypeAgent("in");
		$this->setFilterValuesForMatchCheckAgent(array());
		$this->setRichFilterValuesForMatchCheckAgent(array());

		$this->objFunctionForDefaultValue = null;
		$this->objFunctionForOverrideValue = null;

		$this->objFunctionForSettingDataBeforeEdit = null;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function getData($rowData,$aryVariant){
		$strColId = $this->getPrintTargetKey();
		list($retValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
		return $retValue;
	}

	//----他のクラスから独立して利用するための関数
	//NEW[2]
	public function setColLabelAgent($strColLabel){
		$this->strColLabel = $strColLabel;
	}
	//NEW[3]
	public function getColLabel(){
		$retValue = $this->strColLabel;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getColLabel();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[4]
	public function setFilterValuesForMatchCheckAgent($aryVal){
		$this->aryFilterValuesForMatchCheck = $aryVal;
	}
	//NEW[5]
	public function getFilterValuesForMatchCheck(){
		$retArray = $this->aryFilterValuesForMatchCheck;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retArray = $this->objColumn->getFilterValuesForMatchCheck();
			//カラムがセットされている場合----
		}
		return $retArray;
	}
	//NEW[6]
	public function setRichFilterValuesForMatchCheckAgent($aryVal){
		$this->aryRichFilterValuesForMatchCheck = $aryVal;
	}
	//NEW[7]
	public function getRichFilterValuesForMatchCheck(){
		$retArray = $this->aryRichFilterValuesForMatchCheck;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retArray = $this->objColumn->getRichFilterValuesForMatchCheck();
			//カラムがセットされている場合----
		}
		return $retArray;
	}
	//NEW[8]
	public function setFormatterSearchTypeAgent($varFormatterSearchType){
		$this->varFormatterSearchType = $varFormatterSearchType;
	}
	//NEW[9]
	public function getFormatterSearchType(){
		$retValue = $this->varFormatterSearchType;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				$retValue = $objTable->getFormatter($this->strFormatterId)->getGeneValue("tempBinaryDistinctOnDTiS");
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	//NEW[10]
	public function setColumnSearchTypeAgent($strColumnSearchType){
		$this->strColumnSearchType = $strColumnSearchType;
	}
	//NEW[11]
	public function getColumnSearchType(){
		$retValue = $this->strColumnSearchType;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getSearchType();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	//他のクラスから独立して利用するための関数----

	//NEW[12]
	public function getSettingDataBeforeEdit($boolDelete=false,$boolArrayIgnore=false,$rowData=array(),$aryVariant=array()){
		if( is_callable($this->objFunctionForSettingDataBeforeEdit) ){
			$objFunction = $this->objFunctionForSettingDataBeforeEdit;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$data = $objFunction($this, $boolDelete, $boolArrayIgnore, $rowData, $aryVariant, $objColumn);
		}else{
			$strColId = $this->getPrintTargetKey();
			//$strListFormatterMode = $this->getListFormatterMode();
			list($data,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),null);
			if( $tmpBoolKeyExist===false ){
				//----キーが存在していなかった（or配列でなかった等）
				$data = $this->getDefaultValue($boolDelete,$boolArrayIgnore,$rowData,$aryVariant);
			}else{
				//----キーが存在していた
				$tmpDataOver = $this->getOverrideValue($boolDelete,$boolArrayIgnore,$rowData,$aryVariant);
				if( $tmpDataOver!==null ){
					//----上書きする値が設定されていた
					$data = $tmpDataOver;
				}
				//キーが存在していた----
			}
		}
		return $data;
	}

	//NEW[13]
	function setFunctionForSettingDataBeforeEdit($varFunctionForSettingDataBeforeEdit){
		$this->objFunctionForSettingDataBeforeEdit = $varFunctionForSettingDataBeforeEdit;
	}
	//NEW[14]
	function getFunctionForSettingDataBeforeEdit(){
		return $this->objFunctionForSettingDataBeforeEdit;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class VariantBFmt extends BFmt {
	protected $objFunctionForGetData;

	//----ここから継承メソッドの上書き処理

	public function getData($rowData,$aryVariant){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetData) ){
			$objFunction = $this->objFunctionForGetData;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $rowData, $aryVariant, $objColumn);
		}else{
			$strSetValue = parent::getData($rowData,$aryVariant);
		}
		return $strSetValue;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setFunctionForGetData($objFunctionForGetData){
		$this->objFunctionForGetData = $objFunctionForGetData;
	}
	//NEW[2]
	function getFunctionForGetData(){
		return $this->objFunctionForGetData;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class StaticBFmt extends BFmt {

	private $text; // as string 固定で表示させる文字（デフォルトとして表示される文字）
	private $boolShowWhenNotNull;
	private $strShowOtherColumnId;

	//----ここから継承メソッドの上書き処理

	public function __construct($text="",$boolShowWhenNotNull=false,$strShowOtherColumnId=null){
		parent::__construct();
		$this->text = $text;
		$this->boolShowWhenNotNull  = $boolShowWhenNotNull;
		$this->strShowOtherColumnId = $strShowOtherColumnId;
	}
	
	public function getData($rowData,$aryVariant){
		$strSetValue = $this->text;
		if( $this->boolShowWhenNotNull === true ){
			if( $rowData === null ){
			}else{
				if( $this->strShowOtherColumnId === null ){
					$strColId = $this->getPrintTargetKey();
				}else{
					$strColId = $this->strShowOtherColumnId;
				}
				$strTempValue = (array_key_exists($strColId, $rowData))?$rowData[$strColId]:"";
				if( $strTempValue != "" ){
					$strSetValue = $strTempValue;
				}
			}
		}
		return $strSetValue;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function setText($text){
		$this->text = $text;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class DelBFmt extends BFmt {

	//----ここから継承メソッドの上書き処理

	public function getData($rowData,$aryVariant){
		global $g;
		$retStrVal = "";
		$strData = "";

		$strColId = $this->getPrintTargetKey();
		list($strData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
		
		if($strData == "0"){
		}else if($strData == "1"){
			$retStrVal = $g['objMTS']->getSomeMessage("ITAWDCH-STD-610");
		}else{
			$retStrVal = "";
		}
		return $retStrVal;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class TabBFmt extends BFmt {
	protected $boolTableDataLikeHeader;
	protected $aryTagClasses;
	protected $strTagIdForHandle;
	protected $strDataPrefix;
	protected $strDataPostfix;

	protected $aryObjJsEvent;
	protected $aryJsAttr;
	protected $aryAttr;
	protected $strTextTagLastAttr;

	protected $strFilterMatchClass;

	//----ここから継承メソッドの上書き処理

	public function __construct(){
		parent::__construct();
		$this->setTableDataLikeHeaderAgent(false);
		$this->setTagClassesAgent(array());
		$this->setTagIDForHandleAgent("");
		$this->setDataPrefixAgent("");
		$this->setDataPostfixAgent("");

		$this->aryObjJsEvent = null;
		$this->setAttrsAgent(null);
		$this->setTextTagLastAttr("");

		$this->setFilterMatchClass("filter_match");
	}

	public function getData($rowData,$aryVariant){
		$strData = "";
		$strColId = $this->getPrintTargetKey();
		list($strData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
		return $this->getTag($strData, $rowData);
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	public function getTag($data, $rowData){
		//----タグ（先頭と末尾）の結合を取得
		$strRetBody = $this->getSTag($rowData).$this->getDataPrefix().$data.$this->getDataPostfix().$this->getETag();
		return $strRetBody;
	}

	public function getSTag($rowData){
		$strIdOfHtmlTag = $this->getTagIDForHandle($rowData);
		$strTagClasses = "";
		$strScopeAttBody = "";

		$strTagClasses = " class=\"";

		$aryClass = $this->getTagClasses();
		if( $this->getTableDataLikeHeader() ){
			$strScopeAttBody = " scope=\"row\"";
			$strTagClasses .= " likeHeader";
		}
		$strTmpClasses = implode(" ",$aryClass);
		if($strTmpClasses != ""){
			$strTagClasses .= " {$strTmpClasses}";
		}

		$strTagClasses .= "\"";
		return  "<td {$strIdOfHtmlTag}{$strScopeAttBody}{$strTagClasses}>";
		//タグの種類の分岐----
	}

	public function getETag(){
		//----タグの種類の分岐
		return "</td>\n";
		//タグの種類の分岐----
	}

	//ここまで継承メソッドの上書き処理----

	//----他のクラスから独立して利用するための関数
	public function setTableDataLikeHeaderAgent($boolLikeHeader){
		$this->boolTableDataLikeHeader = $boolLikeHeader;
	}
	public function getTableDataLikeHeader(){
		$retBool = $this->boolTableDataLikeHeader;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retBool = $this->objColumn->isHeader();
			//カラムがセットされている場合----
		}
		return $retBool;
	}
	
	public function setTagClassesAgent($aryTagClasses){
		$this->aryTagClasses = $aryTagClasses;
	}
	public function getTagClasses(){
		$retValue = $this->aryTagClasses;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getClasses();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	public function setTagIDForHandleAgent($strValue){
		$this->strTagIdForHandle = $strValue;
	}
	public function getTagIDForHandle($rowData){
		$retValue = $this->strTagIdForHandle;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$strRIColId = $this->getRIColumnKey();
			if( $this->objColumn->getColumnIDHidden() === true ){
				$strCellHandlePostFix = $this->objOutputType->getPrintSeq();
			}else{
				$strCellHandlePostFix = $this->objColumn->getID();
			}
			$strNumberForRI = "";
			if( $rowData === null ){
			}else{
				$strNumberForRI = (array_key_exists($strRIColId, $rowData))?$rowData[$strRIColId]."_":"";
			}
			$retValue = "id=\"cell_{$this->strFormatterId}_{$strNumberForRI}{$strCellHandlePostFix}\"";
			//カラムがセットされている場合----
		}
		return $retValue;
	}

	
	public function setDataPrefixAgent($strDataPrefix){
		$this->strDataPrefix = $strDataPrefix;
	}
	public function getDataPrefix(){
		$retValue = $this->strDataPrefix;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getPrefix();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	public function setDataPostfixAgent($strDataPostfix){
		$this->strDataPostfix = $strDataPostfix;
	}
	public function getDataPostfix(){
		$retValue = $this->strDataPostfix;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$retValue = $this->objColumn->getPostfix();
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	
	public function setJsEventAgent($strEventName, $strJsFunctionName, $aryJsFunctionArgs=array()){
		if( is_null($aryJsFunctionArgs) === true ){
			//----引数がnullで指定されていた場合
			$aryJsFunctionArgs = array();
		}
		if( is_string($strEventName) === true && $strJsFunctionName === null ){
			if( array_key_exists($strEventName, $this->aryObjJsEvent) === true ){
				unset($this->aryObjJsEvent[$strEventName]);
			}
		}else{
			$this->aryObjJsEvent[$strEventName] = new JsEvent($strEventName, $strJsFunctionName, $aryJsFunctionArgs);
		}
	}
	public function getJsEvents(){
		$aryObjJsEvent = $this->aryObjJsEvent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$aryObjJsEvent = $this->objOutputType->getJsEvents();
			//カラムがセットされている場合----
		}
		return $aryObjJsEvent;
	}
	
	function printJsAttrs($rowData,$aryOverWrite=array(),$aryObjJsEvent=null){
		$strJsAttrsBody = " ";

		$strFunctionPreFix = "";
		if( $this->getJsFxNamePrefix()===true ){ 
			$strFunctionPreFix = $this->getPrintTableID()."_";
		}
		if( is_array($aryObjJsEvent) === false ){
			$aryObjJsEvent = $this->getJsEvents();
		}
		foreach($aryObjJsEvent as $jsEvent){
			$boolOverWrite=false;
			if( is_a($jsEvent,"JsEvent") === true ){
				foreach($aryOverWrite as $strEventName=>$strJsBody){
					if( strtolower($jsEvent->getEventName())==strtolower($strEventName) ){
						$boolOverWrite=true;
						break;
					}
				}
				if( $boolOverWrite===false ){
					$strJsAttrsBody .= $jsEvent->getJsAttr($rowData, $strFunctionPreFix)." ";
				}
			}
		}
		foreach($aryOverWrite as $strEventName=>$strJsBody){
			$strJsAttrsBody .= "{$strEventName}=\"{$strJsBody}\" ";
		}
		return $strJsAttrsBody;
	}
	
	public function setAttrsAgent($aryAttr){
		$this->aryAttr = $aryAttr;
	}
	function printAttrs($aryAddOnDefault,$aryOverWrite){
		$retStrAttrs = " ";
		$aryAttr = $this->aryAttr;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$aryAttr = $this->objOutputType->getAttrs();
			//カラムがセットされている場合----
		}
		foreach($aryAddOnDefault as $strKeyAttrName=>$strValAttrValue){
			if( array_key_exists($strKeyAttrName, $aryAttr)===false ){
				$aryAttr[$strKeyAttrName] = $strValAttrValue;
			}
		}
		$aryAttr = array_merge($aryAttr,$aryOverWrite);
		foreach($aryAttr as $strKeyAttrName=>$strValAttrValue){
			$retStrAttrs .= $strKeyAttrName.'="'.$strValAttrValue.'" ';
		}
		return $retStrAttrs;
	}
	
	public function setTextTagLastAttr($strTextTagtLastAttr){
		$this->strTextTagLastAttr = $strTextTagtLastAttr;
	}
	public function getTextTagLastAttr(){
		$strTextTagLastAttr = $this->strTextTagLastAttr;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$strTextTagLastAttr = $this->objOutputType->getTextTagLastAttr();
			//カラムがセットされている場合----
		}
		return $strTextTagLastAttr;
	}
	//他のクラスから独立して利用するための関数----

	public function setFilterMatchClass($strClassName){
		$this->strFilterMatchClass = $strClassName;
	}
	public function getFilterMatchClass(){
		return $this->strFilterMatchClass;
	}

	//ここまで新規メソッドの定義宣言処理

}

class VariantTabBFmt extends TabBFmt {
	protected $objFunctionForGetData;

	//----ここから継承メソッドの上書き処理

	public function getData($rowData,$aryVariant){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetData) ){
			$objFunction = $this->objFunctionForGetData;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $rowData, $aryVariant, $objColumn);
		}else{
			$strSetValue = parent::getData($rowData,$aryVariant);
		}
		return $strSetValue;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setFunctionForGetData($objFunctionForGetData){
		$this->objFunctionForGetData = $objFunctionForGetData;
	}
	//NEW[2]
	function getFunctionForGetData(){
		return $this->objFunctionForGetData;
	}

	//ここまで新規メソッドの定義宣言処理----

}


class TextTabBFmt extends TabBFmt {

	public function getData($rowData,$aryVariant){
		global $g;
		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$boolCheckContinue = false;

		$strColId = $this->getPrintTargetKey();

		if( $rowData === null ){
			$data = $this->getDefaultValue(false,true,$rowData,$aryVariant);//配列がデフォルト値の場合はnull扱い
		}else{
			$data = (array_key_exists($strColId,$rowData))?$rowData[$strColId]:"";
		}

		$escapedData = $this->makeSafeValueForBrowse($data);
		$strTagInnerBody = $escapedData;

		if( $aryVariant['callerClass'] === 'IDOutputType' ){
			$boolCheckContinue = $this->checkMatchRichFilter($escapedData, $aryVariant['callerVars']['free']['rawValue'], $rowData);
		}else{
			$boolCheckContinue = $this->checkMatchRichFilter($escapedData, $data, $rowData);
		}

		if($boolCheckContinue===false){
			$strTagInnerBody = $escapedData;
		}else{
			//----リッチ完全合致がなかった場合
			if($this->getColumnSearchType() == "range"){
				//----範囲検索タイプの場合
				foreach($this->getFilterValuesForMatchCheck() as $filterValue){
					if($filterValue != ""){
						$strTagInnerBody = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
						break;
					}
				}
				//範囲検索タイプの場合----
			}else{
				//----範囲検索タイプではない場合
				
				$boolSearchZenHanDistinct = $this->getFormatterSearchType();
				
				foreach($this->getFilterValuesForMatchCheck() as $filterValue){
					//----リッチ検索条件ではない、通常の検索条件数のループ
					if( $filterValue!="" ){
						//----1つのカラムに複数のフィルター条件がある場合は、想定していない
	
						if( $boolSearchZenHanDistinct === true ){
							//----あいまい検索ではない場合
							
							$target = $this->makeSafeValueForBrowse($filterValue);
							
							//----区切り文字を#とした。
							$pregStr = $target;
							$pregStr = str_replace('#','\#', $pregStr);
							$pregStr = str_replace('.','\.', $pregStr);
							$pregStr = str_replace('*','\*', $pregStr);
							$pregStr = str_replace('?','\?', $pregStr);
							$pregStr = str_replace('!','\!', $pregStr);
							$pregStr = str_replace('(','\(', $pregStr);
							$pregStr = str_replace('+','\+', $pregStr);
							$pregStr = '#('.str_replace(')','\)', $pregStr).')#i';
							//区切り文字を#とした。----
							
							$strTagInnerBody = preg_replace($pregStr, '<span class="'.$this->getFilterMatchClass().'">\1</span>', $escapedData);
							
							//あいまい検索ではない場合----
						}else{
							//----あいまい検索の場合
							$strTagInnerBody = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
							//あいまい検索の場合----
						}
						
						break;

						//1つのカラムに複数のフィルター条件がある場合は、想定していない----
					}
					//リッチ検索条件ではない、通常の検索条件数のループ----
				}
				//範囲検索タイプではない場合----
			}
			//リッチ完全合致がなかった場合----
		}
//		$strTagInnerBody = nl2br($strTagInnerBody);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $this->getTag($strTagInnerBody, $rowData);
	}

	public function checkMatchRichFilter(&$escapedData, $data, $rowData){
		$boolRet = true;
		
		$varRichFilterValues = $this->getRichFilterValuesForMatchCheck();
		//if($varRichFilterValues != ""){
		if( is_array($varRichFilterValues)===true ){
			//----リッチ検索条件あり
			
			//----ここからチケット234
			$strCompareData = $data;
			//ここまでチケット234----
			
			foreach($varRichFilterValues as $filterValue){
				$target = $filterValue;
				if( $target != "" && $target == $strCompareData ){
					//----完全合致
					$escapedData = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
					$boolRet= false;
					break;
					//完全合致----
				}
			}
			
			//リッチ検索条件あり----
		}else{
			//----リッチ検索条件なし
			//リッチ検索条件なし----
		}
		return $boolRet;
	}

}

class DateTextTabBFmt extends TextTabBFmt {

	public function checkMatchRichFilter(&$escapedData, $data, $rowData){
		$boolRet = true;
		
		$varRichFilterValues = $this->getRichFilterValuesForMatchCheck();
		//if( $varRichFilterValues != "" ){
		if( is_array($varRichFilterValues)===true ){
			//----リッチ検索条件あり
			
			foreach($varRichFilterValues as $filterValue){
				$target = $filterValue;
				$target = date("Y/m/d", strToTime($target));
				if($target != "" && $target == $data){
					$escapedData = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
					$boolRet= false;
					break;
				}
			}
			//リッチ検索条件あり----
		}else{
			//----リッチ検索条件なし
			//リッチ検索条件なし----
		}
		return $boolRet;
	}

}

class DateTimeTextTabBFmt extends TextTabBFmt {

	public function checkMatchRichFilter(&$escapedData, $data, $rowData){
		$boolRet = true;
		
		$varRichFilterValues = $this->getRichFilterValuesForMatchCheck();
		//if($varRichFilterValues != ""){
		if( is_array($varRichFilterValues)===true ){
			//----リッチ検索条件あり
			foreach($varRichFilterValues as $filterValue){
				$target = $filterValue;
				$target = date("Y/m/d H:i:s", strToTime($target));
				if($target != "" && $target == $data){
					$escapedData = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
					$boolRet= false;
					break;
				}
			}
			//リッチ検索条件あり----
		}else{
			//----リッチ検索条件なし
			//リッチ検索条件なし----
		}
		return $boolRet;
	}

}

class NumTabBFmt extends TextTabBFmt {
	protected $boolNumberSepaMarkShow;
	protected $intDigitScale;

	public function __construct($intDigitScale=0,$boolNumberSepaMarkShow=true){
		parent::__construct();
		$this->setNumberSepaMarkShowAgent($boolNumberSepaMarkShow);
		$this->setDigit($intDigitScale);
	}

	public function getData($rowData,$aryVariant){
		$boolCheckContinue=true;
		$strColId = $this->getPrintTargetKey();

		$varDataForCompare = null;
		if( $rowData === null ){
			$data = $this->getDefaultValue(false,true,$rowData,$aryVariant);//配列がデフォルト値の場合はnull扱い
		}else{
			$data = array_key_exists($strColId, $rowData)?$rowData[$strColId]:NULL;//あえて空文字ではなくnull値
		}

		if($data === null){
			$boolCheckContinue=false;
			$data = "";
		}else{
			$varDataForCompare = $data;
			$data = $this->getValueForDisplay($data);
		}

		$escapedData = $this->makeSafeValueForBrowse($data);
		$strTagInnerBody = $escapedData;

		if($boolCheckContinue===true){
			
			$boolCheckContinue=true;
			$varRichFilterValues = $this->getRichFilterValuesForMatchCheck();
			if( is_array($varRichFilterValues)===true ){
				foreach($varRichFilterValues as $filterValue){
					$target = $this->getValueForDisplay($filterValue);
					if($target != "" && $target == $data){
						$strTagInnerBody = '<span class="'.$this->getFilterMatchClass().'">'.$escapedData.'</span>';
						$boolCheckContinue=false;
						break;
					}
				}
			}
		}
		
		if($boolCheckContinue===true){
			switch($this->getColumnSearchType()){
				case "in":
					$arrayFilterData = $this->getFilterValuesForMatchCheck();
					if(in_array($escapedData,$arrayFilterData)===false){
					}else{
						$strTagInnerBody = "<span class=\"{$this->getFilterMatchClass()}\">{$escapedData}</span>";
					}
					break;
				case "range":
					$arrayFilterData = $this->getFilterValuesForMatchCheck();
					
					$valueStart=array_key_exists(0,$arrayFilterData)?$arrayFilterData[0]:null;
					$valueLast=array_key_exists(1,$arrayFilterData)?$arrayFilterData[1]:null;
					
					if($valueStart === null && $valueLast === null){
						//----開始・終了の両値の指定なし
						//開始・終了の両値の指定なし----
					}else if($valueStart !== null && $valueLast !== null){
						if( bccomp($varDataForCompare, $valueStart, $this->intDigitScale)!=-1 
						    && bccomp($varDataForCompare, $valueLast, $this->intDigitScale)!=1 ){
							$strTagInnerBody = "<span class=\"{$this->getFilterMatchClass()}\">{$escapedData}</span>";
						}
					}else{
						if($valueStart === null){
							//----開始の値の指定がない
							if( bccomp($varDataForCompare, $valueLast, $this->intDigitScale)!=1 ){
								$strTagInnerBody = "<span class=\"{$this->getFilterMatchClass()}\">{$escapedData}</span>";
							}
							//開始の値の指定がない----
						}else{
							//----終了の値の指定がない
							if( bccomp($varDataForCompare, $valueStart, $this->intDigitScale)!=-1 ){
								$strTagInnerBody = "<span class=\"{$this->getFilterMatchClass()}\">{$escapedData}</span>";
							}
							//終了の値の指定がない----
						}
					}
					break;
				default:
					break;
			}
		}
		$strTagInnerBody = nl2br($strTagInnerBody);
		return $this->getTag($strTagInnerBody, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setNumberSepaMarkShowAgent($boolNumberSepaMarkShow){
		$this->boolNumberSepaMarkShow = $boolNumberSepaMarkShow;
	}

	//他のクラスから独立して利用するための関数----

	public function setDigit($intDigitScale){
		$this->intDigitScale = $intDigitScale;
	}
	public function getDigit(){
		return $this->intDigitScale;
	}

	public function getValueForDisplay($data){
		if( $this->boolNumberSepaMarkShow == false ){
			$retStrVal = $data;
		}else{
			if($data != ""){
				$retStrVal = number_format($data, $this->intDigitScale, '.', ',');
			}else{
				$retStrVal = "";
			}
		}
		$retStrVal = str_replace("-.","-0.",$retStrVal);
		return $retStrVal;
	}


}

class SubtotalTabBFmt extends NumTabBFmt {
	protected $varSubTotalValue;

	public function __construct($intDigitScale=0,$boolNumberSepaMarkShow=true){
		parent::__construct($intDigitScale,$boolNumberSepaMarkShow);
		$this->setSubtotalValueAgent(null);
	}

	public function getData($rowData,$aryVariant){
		$varValue = $this->getSubTotalValue();
		
		$strTagInnerBody = nl2br(number_format($varValue, $this->intDigitScale,".",","));
		
		return $this->getTag($strTagInnerBody, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setSubtotalValueAgent($varSubTotalValue){
		$this->varSubtotalValue = $varSubTotalValue;
	}
	//他のクラスから独立して利用するための関数----

	public function getSubTotalValue(){
		$retValue = $this->varSubtotalValue;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getSubtotalValue')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getSubtotalValue();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}

}

class StaticTextTabBFmt extends TabBFmt {

	private $text; // as string 固定で表示させる文字（デフォルトとして表示される文字）
	private $boolShowWhenNotNull;
	private $strShowOtherColumnId;

	public function __construct($text="",$boolShowWhenNotNull=false,$strShowOtherColumnId=null){
		parent::__construct();
		$this->text = $text;
		$this->boolShowWhenNotNull  = $boolShowWhenNotNull;
		$this->strShowOtherColumnId = $strShowOtherColumnId;
	}

	public function getData($rowData,$aryVariant){
		$strTagInnerBody = $this->text;
		if( $this->boolShowWhenNotNull === true ){
			if( $rowData === null ){
			}else{
				if( $this->strShowOtherColumnId === null ){
					$strColId = $this->getPrintTargetKey();
				}else{
					$strColId = $this->strShowOtherColumnId;
				}
				$strTempValue = (array_key_exists($strColId, $rowData))?$rowData[$strColId]:"";
				$escapedData = $this->makeSafeValueForBrowse($strTempValue);
				if( $escapedData != "" ){
					$strTagInnerBody = $escapedData;
				}
			}
		}
		$strTagInnerBody = nl2br($strTagInnerBody);
		return $this->getTag($strTagInnerBody, $rowData);
	}


	public function setText($text){
		$this->text = $text;
	}


}

class LinkTabBFmt extends TextTabBFmt{

	public function getData($rowData,$aryVariant){
		global $g;
		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		
		$strTagInnerBody = "";
		if( $rowData === null ){
		}else{
			$aryConvValue = $aryVariant['callerVars']['free'];
			if(array_key_exists("innerHtml", $aryConvValue)===true){
				$escapedData = $this->makeSafeValueForBrowse($aryConvValue['innerHtml']);
				$strUrl = $aryConvValue['url'];
				if( 0 < strlen($strUrl) ){
					$strTagInnerBody = "<a href=\"{$strUrl}\" target=\"_blank\">{$escapedData}</a>";
				}else{
					$strTagInnerBody = $escapedData;
				}
			}
		}
		
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class LinkButtonTabBFmt extends TabBFmt {
	protected $intDisableShowMode;
	protected $varDisableCtrlPrima;
	protected $aryDisabledPattern;

	protected $strButtonFace;
	protected $boolLinkable;

	public function __construct($intDisableShowMode=0,$varDisableCtrlPrima="",$aryDisabledPattern=array()){
		//$intDisableShowMode(押させたくない場合の制御)///0:非活性化/1:ボタン自体を表示しない
		parent::__construct();
		$this->intDisableShowMode  = $intDisableShowMode;
		$this->varDisableCtrlPrima = $varDisableCtrlPrima;
		$this->aryDisabledPattern  = $aryDisabledPattern;
		$this->setButtonFaceAgent("");
		$this->setLinkableAgent(true);
	}

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();
		$buttonName = $this->getButtonFace();
		$linkable = $this->getLinkable();

		if( is_array($this->varDisableCtrlPrima) === true ){
			foreach($this->varDisableCtrlPrima as $focusKey=>$focusValue){
				if($focusKey==0){
					$linkable = $focusValue($rowData);
				}
			}
		}else{
			if( $this->varDisableCtrlPrima == "" ){
			}else{
				$linkable = "";
				if( array_key_exists($this->varDisableCtrlPrima, $rowData) === true ){
					foreach($this->aryDisabledPattern as $focusValue){
						if($focusValue == $rowData[$this->varDisableCtrlPrima]){
							$linkable = "disabled";
							break;
						}
					}
				}
			}
		}
		if( $linkable == "disabled" ){
			$strTagInnerBody = "<div style=\"display:none\">0</div>";
		}else{
			$strTagInnerBody = "<div style=\"display:none\">1</div>";
		}

		$aryAddOnDefault["class"] = "linkBtnInTbl";

		$aryOverWrite["type"] = "button";
		$aryOverWrite["value"] = $buttonName;

		if( $linkable == "disabled" && $this->intDisableShowMode == 1 ){
			$strTagInnerBody = "";
		}else{
			$strTagInnerBody .= "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$linkable}>";
		}
		return $this->getTag($strTagInnerBody, $rowData);
	}



	//----他のクラスから独立して利用するための関数
	public function setButtonFaceAgent($strButtonFace){
		$this->strButtonFace = $strButtonFace;
	}
	public function getButtonFace(){
		$strButtonFace = $this->strButtonFace;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getButtonFaceValue')===true ){
				//----メソッドが存在している場合
				$strButtonFace = $this->objColumn->getButtonFaceValue();
				//----メソッドが存在している場合
			}
			//カラムがセットされている場合----
		}
		return $strButtonFace;
	}

	public function setLinkableAgent($boolLinkable){
		$this->boolLinkable = $boolLinkable;
	}
	public function getLinkable(){
		$boolLinkable = $this->boolLinkable;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getLinkable')===true ){
				//----メソッドが存在している場合
				$boolLinkable = $this->objColumn->getLinkable();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $boolLinkable;
	}

	//他のクラスから独立して利用するための関数----

}

class UpdButtonTabBFmt extends TabBFmt {

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();
		$strRIColId = $this->getRIColumnKey();
		$strColLabel = $this->getColLabel();

		$updParam=1;

		if( $rowData === null ){
			$updable = "";
		}else{
			$strCheckColumnId = $this->getRequiredDisuseColumnID();
			if( array_key_exists($strCheckColumnId, $rowData) === true && $rowData[$strCheckColumnId] === "0" ){
				$updable = "";
			}else{
				$updable = "disabled";
			}
		}
		$aryAddOnDefault["class"] = "updateBtnInTbl";

		$aryOverWrite["type"] = "button";
		$aryOverWrite["value"] = $strColLabel;

		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$updable}>";
		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class DelButtonTabBFmt extends TabBFmt {
	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();
		$strRIColId = $this->getRIColumnKey();
		$strButtonFace = "";

		if( $rowData === null ){
			$strNumberForRI = "";
			$strButtonFace = "";
			$delParam = -1;
		}else{
			$strNumberForRI = array_key_exists($strRIColId, $rowData)?$rowData[$strRIColId]:"";
			$strCheckColumnId = $this->getRequiredDisuseColumnID();
			if( array_key_exists($strCheckColumnId, $rowData) === true && $rowData[$strCheckColumnId] === "0" ){
				//$strButtonFace = "廃止";
				$strButtonFace = $g['objMTS']->getSomeMessage("ITAWDCH-STD-611");
				$delParam = 1;
			}else{
				//$strButtonFace = "復活";
				$strButtonFace = $g['objMTS']->getSomeMessage("ITAWDCH-STD-612");
				$delParam = 4;
			}
		}
		$aryAddOnDefault["class"] = "deleteBtnInTbl";

		$aryOverWrite["type"] = "button";
		$aryOverWrite["value"] = $strButtonFace;//----毎回変わるのでValueはDefaultではない。

		$strPrefix = "";
		if( $this->getJsFxNamePrefix()===true ){ 
			$strPrefix = $this->getPrintTableID()."_";
		}

		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} ";
		if( $delParam == 1 || $delParam == 4 ){
			$strTagInnerBody .="onClick=\"{$strPrefix}delete_async($delParam,'{$strNumberForRI}');\" ";
		}
		$strTagInnerBody .= "/>";
		
		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class DelTabBFmt extends TabBFmt {

	public function getData($rowData,$aryVariant){
		global $g;
		$strColId = $this->getPrintTargetKey();
		$strBody = "";
		$strData = "";
		if( $rowData === null ){
			$strData = "";
		}else{
			$strData = array_key_exists($strColId, $rowData)?$rowData[$strColId]:"";
		}
		if($strData == "0"){
			$strTagInnerBody = "";
		}else if($strData == "1"){
			//$strBody = "廃止";
			$strTagInnerBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-621");
		}else{
			$strTagInnerBody = "";
		}
		return $this->getTag($strTagInnerBody, $rowData);
	}

}

//----ここから、Write入力系

class InputTabBFmt extends TabBFmt {
	protected $objFunctionForReturnOverrideGetData;

	public function __construct($strPrintType=""){
		parent::__construct();
		$this->strPrintType = $strPrintType; //ほぼ廃止
		$this->setFunctionForReturnOverrideGetData(null);
	}

	public function getFSTIDForIdentify(){
		// フィルター系側が1タグと限らないので、フィルター系と、この関数を共有をしない
		$retStrVal = "";
		if( $this->getJsFxNamePrefix()===true ){
			$retStrVal = $this->getPrintTableID()."_";
		}else{
			$retStrVal = $this->strFormatterId;
		}

		if( $this->getColumnIdHidden()===true ){
			$retStrVal .= $this->getPrintSeq();
		}else{
			$retStrVal .= $this->getPrintTargetKey();
		}
		return $retStrVal;
	}

	public function getFSTNameForIdentify(){
		// フィルター系側が1タグと限らないので、フィルター系と、この関数を共有をしない
		$retStrVal = "";
		if( $this->getColumnIdHidden()===true ){
			$retStrVal = $this->getIDSynonym();
		}else{
			$retStrVal = $this->getIDSynonym();
		}
		return $retStrVal;
	}

	public function setFunctionForReturnOverrideGetData($objFunctionForReturnOverrideGetData){
		$this->objFunctionForReturnOverrideGetData = $objFunctionForReturnOverrideGetData;
	}

	public function getFunctionForReturnOverrideGetData(){
		return $this->objFunctionForReturnOverrideGetData;
	}

}

class PasswordInputTabBFmt extends InputTabBFmt {

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$aryAddOnDefault["maxLength"] = $this->getMaxInputLength();
		$aryAddOnDefault["size"]      = 15;

		$aryOverWrite["type"] = "password";
		$aryOverWrite["id"]   = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();
		$aryOverWrite["value"] = "";

		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} >";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class TextInputTabBFmt extends InputTabBFmt {

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();
		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い

		//----htmlタグがdataに入っている場合に異常動作させないための処理
		$data = $this->makeSafeValueForBrowse($data);
		//htmlタグがdataに入っている場合に異常動作させないための処理----

		$aryAddOnDefault["maxLength"] = $this->getMaxInputLength();
		$aryAddOnDefault["size"]      = 15;

		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"]   = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();
		$aryOverWrite["value"] = $data;

		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class TextHiddenInputTabBFmt extends InputTabBFmt {
	//----テキストと、隠しテキストインプットを出力する

	private $strStaticTextWhenDataNull; // as string データが空文字の場合に、表示させる文字

	public function __construct($strStaticTextWhenDataNull){
		parent::__construct();
		$this->strStaticTextWhenDataNull = $strStaticTextWhenDataNull;
	}

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い

		//----htmlタグがdataに入っている場合に異常動作させないための処理
		$data = $this->makeSafeValueForBrowse($data);
		//htmlタグがdataに入っている場合に異常動作させないための処理----

		$aryOverWrite["type"] = "hidden";
		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();
		$aryOverWrite["value"] = $data;

		$strTagInnerBody = $data;
		if( $data == "" ){
			$strTagInnerBody = $this->strStaticTextWhenDataNull;
		}
		$strTagInnerBody .= "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

	public function getTextWhenDataNull(){
		return $this->strStaticTextWhenDataNull;
	}
	public function setTextWhenDataNull($strStaticTextWhenDataNull){
		$this->strStaticTextWhenDataNull = $strStaticTextWhenDataNull;
	}

}

class NumInputTabBFmt extends InputTabBFmt {
	protected $intDigitScale;
	protected $boolNumberSepaMarkShow;

	public function __construct($intDigitScale=0,$boolNumberSepaMarkShow=false){
		parent::__construct();
		$this->setNumberSepaMarkShowAgent($boolNumberSepaMarkShow);
		$this->setDigit($intDigitScale);
	}

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い

		//----htmlタグがdataに入っている場合に異常動作させないための処理
		$data = $this->makeSafeValueForBrowse($data);
		//htmlタグがdataに入っている場合に異常動作させないための処理----
		if($data !== NULL){
			$data = $this->getValueForDisplay($data);
			
			//----入力時には、区切り文字を取り除く
			//入力時には、区切り文字を取り除く----
		}

		$aryAddOnDefault["maxLength"] = $this->getMaxInputLength();
		$aryAddOnDefault["size"]      = 15;

		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();
		$aryOverWrite["value"] = $data;

		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setNumberSepaMarkShowAgent($boolNumberSepaMarkShow){
		$this->boolNumberSepaMarkShow = $boolNumberSepaMarkShow;
	}

	//他のクラスから独立して利用するための関数----

	public function setDigit($intDigitScale){
		$this->intDigitScale = $intDigitScale;
	}

	public function getDigit(){
		return $this->intDigitScale;
	}

	public function getValueForDisplay($data){
		if( $this->boolNumberSepaMarkShow == false ){
			$retStrVal = $data;
		}else{
			if($data != ""){
				$retStrVal = number_format($data, $this->intDigitScale, '.', ',');
			}else{
				$retStrVal = "";
			}
		}
		$retStrVal = str_replace("-.","-0.",$retStrVal);
		return $retStrVal;
	}

}

class TextAreaTabBFmt extends InputTabBFmt {

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い

		$strColId = $this->getPrintTargetKey();

		//----<textarea></textarea>がdataに入っている場合に異常動作させないための処理
		$data = $this->makeSafeValueForBrowse($data);
		//<textarea></textarea>がdataに入っている場合に異常動作させないための処理----

		$aryAddOnDefault["maxLength"] = $this->getMaxInputLength();
		$aryAddOnDefault["rows"]      = 5;
		$aryAddOnDefault["cols"]      = 60;

		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();
		$aryOverWrite["value"] = $data;
		
		$strTagInnerBody = "<textarea {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>{$data}</textarea>";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

}

class SelectTabBFmt extends InputTabBFmt {
	//FAD=[F]or-[A]fter-[D]emand

	//----OnTime用タグ属性
	protected $strSelectWaitingText;
	
	protected $objFunctionForGetMultiple;
	protected $objFunctionForGetSelectList;
	protected $strNoOptionMessageText;

	protected $objFunctionForGetMainDataOverride;

	protected $objFunctionForAddToTagTail;
	//OnTime用タグ属性----

	//----FADタグ属性
	protected $strFADClassOfSelectTagWrapper;
	protected $strFADClassOfSelectTagCaller;
	protected $strFADClassOfSelectTag; //出力されるSELECTタグのクラス
	protected $strFADMaxWidthOfSelectTag;

	protected $objFunctionForGetFADMultiple;
	protected $objFunctionForGetFADMainDataOverride;
	protected $aryObjFADJsEvents;

	protected $objFunctionForGetFADAddToTagTail;
	//FADタグ属性----

	public function __construct($strPrintType=""){
		parent::__construct();
		$this->strPrintType = $strPrintType; //ほぼ廃止

		//----OnTime用タグ属性
		$this->setSelectWaitingText("");
		$this->setNoOptionMessageText("");

		$this->setFunctionForGetMultiple(null);
		$this->setFunctionForGetSelectList(null);
		$this->setFunctionForAddToTagTail(null);
		
		$this->setFunctionForGetMainDataOverride(null);
		//OnTime用タグ属性----

		//----FADタグ属性
		$this->setFADNoOptionMessageText("");

		$this->setFADClassOfSelectTagWrapper("dynamicSelectListWrapper");
		$this->setFADClassOfSelectTagCaller("dynamicSelectListCaller");
		$this->setFADClassOfSelectTag("dynamicSelectList");
		$this->setFADMaxWidthOfSelectTag();

		$this->setFunctionForGetFADMultiple(null);
		$this->setFunctionForGetFADSelectList(null);
		$this->setFunctionForGetFADAddToTagTail(null);
		$this->setFunctionForGetFADMainDataOverride(null);
		$this->aryObjFADJsEvents = array();
		//FADタグ属性----
	}

	public function getSettingDataBeforeEdit($arraySelectElement,$rowData,$aryVariant){
		//----selectedを返す
		$data = parent::getSettingDataBeforeEdit(false,false,$rowData,$aryVariant); //----設定値が配列の場合も取得
		
		if( $data === null){
			$selected = null;
		}else{
			if(is_array($data)===true){
				if(0 < count($data)){
					$selected = array();
					foreach($data as $key=>$value){
						$selected[] = $value;
					}
				}else{
					$selected = $data;
				}
			}else{
				//IDが数字ならint型に変える----
				$selected = $data;
			}
			unset($data);
		}
		if( $selected === null ){
			if( is_a($this->objColumn,'MultiSelectSaveColumn') === true ){
				if( is_array($rowData) === true ){
					if( array_key_exists($this->getRIColumnKey(), $rowData) === true ){
						$tempData = getDataFromLinkTable($this->objColumn, $rowData[$this->getRIColumnKey()], 1);
						$selected = array();
						foreach($tempData as $tmpKey=>$tmpValue){
							$selected[] = $tmpValue;
						}
					}
				}
			}
		}
		return $selected;
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();

		$aryAddOnDefault["size"] = "10";
		$aryAddOnDefault["class"] = "selectList";

        if(is_array($rowData)){
            $select2ClassName = "_UPD";
            $preClass = "Mix1_";
        }
        else{
            $select2ClassName = "_REG";
            $preClass = "Mix2_";
        }

        // select2用のclass名を追加する
        if(array_key_exists('callerVars', $aryVariant)){
            $select2ClassName = $aryVariant['callerVars']['initedColumnID'] . $select2ClassName;
        }
        $aryAddOnDefault["class"] = $aryAddOnDefault["class"] . " " . $select2ClassName;

		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();

		$aryRetBody = $this->getMultiple($rowData,$aryVariant);
		if( $aryRetBody[1]===null ){
			if( $aryRetBody[0] === true ){
				//----複数選択可能なタグにする
				$aryOverWrite["multiple"] = "multiple";
				//複数選択可能にタグにする----
			}
		}

		//----初期表示用のリストソースを取得する
		$aryRetBody = $this->getSelectList($rowData,$aryVariant);
		if( $aryRetBody[1]!==null ){
			$arraySelectElement = null;
		}else{
			$arraySelectElement = $aryRetBody[4];
		}
		//初期表示用のリストソースを取得する----

		$strSelectWaitingText = $this->getSelectWaitingText();

		$strSetIdBody = "na_{$this->getPrintTableID()}_{$this->getIDSynonym()}";
		$strTagInnerBody  = "<div class=\"{$this->getPrintSeq()} {$this->strFADClassOfSelectTagWrapper}\">";
		$strTagInnerBody .= "<div class=\"{$strSetIdBody}\"></div>";

		if( strlen($strSelectWaitingText) === 0 ){
			if($arraySelectElement === null){
				//$strTagInnerBody = "テーブルまたはカラムが存在しません";
				$strTagInnerBody .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-12001");
			}else{
				if(array_key_exists("",$arraySelectElement)===true){
					//$strTagInnerBody = "マスター(テーブル)の、被参照キー列に、空白が含まれています。";
					$strTagInnerBody .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-12002");
				}else{
					// null,数値,配列が、$dataに返ってくる
					$data = $this->getSettingDataBeforeEdit($arraySelectElement,$rowData,$aryVariant);

					$boolWhiteKeyAdd = true;
					if( $this->getRequired() === true ){
						$boolWhiteKeyAdd = false;
						if( $this->checkListFormatterMode("RegisterTableFormatter") === true && $this->getRegisterRequiredExcept() !== false ){
								$boolWhiteKeyAdd = true;
						}else if( $this->checkListFormatterMode("UpdateTableFormatter") === true && $this->getUpdateRequiredExcept() !== false ){
							$boolWhiteKeyAdd = true;
						}
					}
					
					if( is_callable($this->objFunctionForGetMainDataOverride) === true ){
						$tmpObjFunction = $this->objFunctionForGetMainDataOverride;
						$aryRetBody = $tmpObjFunction($this,$arraySelectElement,$data,$boolWhiteKeyAdd,$rowData,$aryVariant);
						if( $aryRetBody[1] !== null ){
							$optionBodies = "";
							$strNoOptionMessageText = "";
						}else{
							$optionBodies = $aryRetBody[0]['optionBodies'];
							$strNoOptionMessageText = $aryRetBody[0]['NoOptionMessageText'];
						}
						unset($tmpObjFunction);
					}else{
						//----必須項目でないなら空白を選択可能
						$optionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);
						//必須項目でないなら空白を選択可能----

						$strNoOptionMessageText = $this->getNoOptionMessageText();
					}

					if( strlen($optionBodies) === 0 && 0 < strlen($strNoOptionMessageText) ){
						$strTagInnerBody .= $strNoOptionMessageText;
					}else{
						$strJsFxStream = $this->printJsAttrs($rowData);

						$strSelectTagBody = "<select {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$strJsFxStream}>\n";
						$strSelectTagBody .= $optionBodies;
						$strSelectTagBody .="</select>";
						$strTagInnerBody .= $strSelectTagBody;

                        // プルダウンあいまい検索
            			$strTagInnerBody .= 
<<< EOD
                        <script type="text/javascript">
                            var strAdjustRulerClassName = "{$select2ClassName}";
                            var objAdjustRulerForWidth = $('#'+"{$preClass}"+'Nakami'+' .'+strAdjustRulerClassName).get()[0];
                            var intNewWidth = objAdjustRulerForWidth.offsetWidth;
                            if(30 == intNewWidth){
                                intNewWidth = 45;
                            }
                            intNewWidth = intNewWidth + 5;
                            if(650 < intNewWidth){
                                //----広くなりすぎる場合は制限
                                intNewWidth = 650;
                            }
                            $(document).ready(function(){
                                $(".{$select2ClassName}").select2({
                                    width:intNewWidth
                                });
                            });
                        </script>
EOD;
					}
					
					//----追い越し判定用フラグなどの追加タグ
					$aryRetBody  = $this->getAddToTagTail($rowData,$aryVariant);
					if( $aryRetBody[1]!==null ){
					}else{
						$strTagInnerBody .= $aryRetBody[0];
					}
					//追い越し判定用フラグなどの追加タグ----
				}
				
			}
		}else{
			$strTagInnerBody .= $strSelectWaitingText;
		}
		$strTagInnerBody .= "</div>";
		$strTagInnerBody .= "</div>";

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

	//----他のクラスから独立して利用するための関数

	public function setFunctionForGetMultiple($objFunctionForGetMultiple){
		$this->objFunctionForGetMultiple = $objFunctionForGetMultiple;
	}
	public function setFunctionForGetSelectList($objFunctionForGetSelectList){
		$this->objFunctionForGetSelectList = $objFunctionForGetSelectList;
	}
	public function setFunctionForAddToTagTail($objFunctionForAddToTagTail){
		$this->objFunctionForAddToTagTail = $objFunctionForAddToTagTail;
	}
	public function setFunctionForGetMainDataOverride($objFunctionForGetMainDataOverridel){
		$this->objFunctionForGetMainDataOverride = $objFunctionForGetMainDataOverridel;
	}
	
	public function setFunctionForGetFADMultiple($objFunctionForGetFADMultiple){
		$this->objFunctionForGetFADMultiple = $objFunctionForGetFADMultiple;
	}
	public function setFunctionForGetFADSelectList($objFunctionForGetFADSelectList){
		$this->objFunctionForGetFADSelectList = $objFunctionForGetFADSelectList;
	}
	public function setFunctionForGetFADAddToTagTail($objFunctionForGetFADAddToTagTail){
		$this->objFunctionForGetFADAddToTagTail = $objFunctionForGetFADAddToTagTail;
	}
	public function setFunctionForGetFADMainDataOverride($objFunctionForGetFADMainDataOverride){
		$this->objFunctionForGetFADMainDataOverride = $objFunctionForGetFADMainDataOverride;
	}

	public function getFunctionForGetMultiple(){
		return $this->objFunctionForGetMultiple;
	}
	public function getFunctionForGetSelectList(){
		return $this->objFunctionForGetSelectList;
	}
	public function getFunctionForAddToTagTail(){
		return $this->objFunctionForAddToTagTail;
	}
	public function getFunctionForGetMainDataOverride(){
		return $this->objFunctionForGetMainDataOverride;
	}
	
	public function getFunctionForGetFADMultiple(){
		return $this->objFunctionForGetFADMultiple;
	}
	public function getFunctionForGetFADSelectList(){
		return $this->objFunctionForGetFADSelectList;
	}
	public function getFunctionForGetFADAddToTagTail(){
		return $this->objFunctionForGetFADAddToTagTail;
	}
	public function getFunctionForGetFADMainDataOverride(){
		return $this->objFunctionForGetFADMainDataOverride;
	}
	//他のクラスから独立して利用するための関数----

	public function getMultiple($rowData,$aryVariant){
		$retBool = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";

		$objFunction = $this->objFunctionForGetMultiple;
		if( is_callable($objFunction) === true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn, "IDColumn") === true ){
				//----IDColumnとの連動系
				
				$retBool = $this->objColumn->getMultiple($this->strFormatterId);
				
				//IDColumnとの連動系----
			}
			$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg);
		}
		return $aryRetBody;
	}

	public function setSelectWaitingText($strSelectWaitingText){
		$this->strSelectWaitingText = $strSelectWaitingText;
	}
	public function setNoOptionMessageText($strNoOptionMessageText){
		$this->strNoOptionMessageText = $strNoOptionMessageText;
	}
	public function setFADNoOptionMessageText($strFADNoOptionMessageText){
		$this->strFADNoOptionMessageText = $strFADNoOptionMessageText;
	}

	//----初期表示の際に作成するリストのデータソースを返す
	public function getSelectList($rowData,$aryVariant){
		$retBool = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$aryDataSet = array();
	
		$objFunction = $this->objFunctionForGetSelectList;
		if( is_callable($objFunction)=== true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn, "IDColumn")===true ){
				//----IDColumnとの連動系
				
				$arrayDispSelectTag=$this->objColumn->getArrayMasterTableByFormatName($this->strFormatterId);
				if($arrayDispSelectTag===null){
					//----通常は、ここを通る
					$aryDataSet=$this->objColumn->getMasterTableArrayForInput();
					//通常は、ここを通る----
				}else{
					//----特別に、各Formatterごとに、設定がされていた場合は、ここを通る
					if(is_array($arrayDispSelectTag)===true){
						$aryDataSet=$arrayDispSelectTag;
						//特別に、設定がされていた場合は、ここを通る----
					}else{
						//----ここを通ることは、ふつうは、考えられない
						$aryDataSet=array();
						//ここを通ることは、ふつうは、考えられない----
					}
					//特別に、各Formatterごとに、設定がされていた場合は、ここを通る----
				}
				
				//IDColumnとの連動系----
			}else{
				$aryDataSet = null;
			}
			$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
		}
		return $aryRetBody;
	}
	//初期表示の際に作成するリストのデータソースを返す----

	public function getAddToTagTail($rowData, $aryVariant){
		$strBody = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";

		$objFunction = $this->objFunctionForAddToTagTail;
		if( is_callable($objFunction)=== true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn,'MultiSelectSaveColumn') === true ){
				//----MultiSelectSaveColumnとの連動系
				
				$intAnchorValue = "";
				if( $rowData === null ){
				}else{
					if( array_key_exists($this->objColumn->getAnchorColumnIDOfLink(), $rowData) === true ){
						$intAnchorValue = $rowData[$this->objColumn->getAnchorColumnIDOfLink()];
					}
				}
		    	$strMaxLUDValue = getDataFromLinkTable($this->objColumn, $intAnchorValue, 0);
				$strBody = "<input type=\"hidden\" name=\""."tmp_mlustf_".$this->getFSTNameForIdentify()."\" value=\"".$strMaxLUDValue."\" >";
				
				//MultiSelectSaveColumnとの連動系----
			}
			$aryRetBody = array($strBody, $intErrorType, $aryErrMsgBody, $strErrMsg);
		}
		return $aryRetBody;
	}

	public function setFADMaxWidthOfSelectTag($strFADMaxWidthOfSelectTag="640px"){
		$this->strFADMaxWidthOfSelectTag = $strFADMaxWidthOfSelectTag;
	}
	public function setFADClassOfSelectTagWrapper($strClasses=""){
		$this->strFADClassOfSelectTagWrapper = $strClasses;
	}
	public function setFADClassOfSelectTagCaller($strClasses=""){
		$this->strFADClassOfSelectTagCaller = $strClasses;
	}
	public function setFADClassOfSelectTag($strClasses=""){
		$this->strFADClassOfSelectTag = $strClasses;
	}

	//----GET(OnTime系)
	public function getSelectWaitingText(){
		return $this->strSelectWaitingText;
	}
	public function getNoOptionMessageText(){
		return $this->strNoOptionMessageText;
	}
	//GET(OnTime系)----

	//----GET(FAD系)
	public function getFADNoOptionMessageText(){
		return $this->strFADNoOptionMessageText;
	}
	public function getFADMaxWidthOfSelectTag(){
		return $this->strFADMaxWidthOfSelectTag;
	}
	public function getFADClassOfSelectTagWrapper(){
		return $this->strFADClassOfSelectTagWrapper;
	}
	public function getFADClassOfSelectTagCaller(){
		return $this->strFADClassOfSelectTagCaller;
	}
	public function getFADClassOfSelectTag(){
		return $this->strFADClassOfSelectTag;
	}
	public function setFADJsEvent($strEventName, $strJsFunctionName, $aryJsFunctionArgs=array()){
		if( is_null($aryJsFunctionArgs) === true ){
			//----引数がnullで指定されていた場合
			$aryJsFunctionArgs = array();
		}
		if( is_string($strEventName) === true && $strJsFunctionName === null ){
			if( array_key_exists($strEventName, $this->aryObjFADJsEvents) === true ){
				unset($this->aryObjFADJsEvents[$strEventName]);
			}
		}else{
			$this->aryObjFADJsEvents[$strEventName] = new JsEvent($strEventName, $strJsFunctionName, $aryJsFunctionArgs);
		}
	}

	//----初期表示後に、(後発)動的に作成するリストのデータソースを返す
	public function getFADSelectList($aryVariant, $arySetting, $aryOverride){
		$retBool = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$aryDataSet = array();

		$objFunction = $this->objFunctionForGetFADSelectList;
		if( is_callable($objFunction)=== true ){
			$aryRetBody = $objFunction($this, $aryVariant, $arySetting, $aryOverride);
		}else{
			if( is_a($this->objColumn, "Column")===true ){
				//----IDColumnとの連動系
				
				$objOT = $this->objColumn->getOutputType($this->strFormatterId);
				$aryRetBody = $objOT->getFADSelectList($aryVariant, $arySetting, $aryOverride);
				
				//IDColumnとの連動系----
			}else{
				$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
			}
		}
		return $aryRetBody;
	}
	//初期表示後に、(後発)動的に作成するリストのデータソースを返す----

	public function getFADAddToTagTail($rowData, $aryVariant){
		$strBody = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		
		$objFunction = $this->objFunctionForGetFADAddToTagTail;
		if( is_callable($objFunction)=== true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn,'MultiSelectSaveColumn') === true ){
				//----MultiSelectSaveColumnとの連動系
				
				$intAnchorValue = "";
				if( $rowData === null ){
				}else{
					if( array_key_exists($this->objColumn->getAnchorColumnIDOfLink(), $rowData) === true ){
						$intAnchorValue = $rowData[$this->objColumn->getAnchorColumnIDOfLink()];
					}
				}
		    	$strMaxLUDValue = getDataFromLinkTable($this->objColumn, $intAnchorValue, 0);
				$strBody = "<input type=\"hidden\" name=\""."tmp_mlustf_".$this->getFSTNameForIdentify()."\" value=\"".$strMaxLUDValue."\" >";
			}
			$aryRetBody = array($strBody, $intErrorType, $aryErrMsgBody, $strErrMsg);
			
			//MultiSelectSaveColumnとの連動系----
		}
		return $aryRetBody;
	}

	public function getFADJsEvents(){
		return $this->aryObjFADJsEvents;
	}

	public function getFADMultiple($rowData,$aryVariant){
		$retBool = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";

		$objFunction = $this->objFunctionForGetFADMultiple;
		if( is_callable($objFunction) === true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn, "IDColumn") === true ){
				//----IDColumnとの連動系
				
				$retBool = $this->objColumn->getMultiple($this->strFormatterId);
				
				//IDColumnとの連動系----
			}
			$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg);
		}
		return $aryRetBody;
	}

	//----初期表示後に、(後発)動的に、リストタグを表示するメソッド
	function printTagFromFADSelectList(&$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
		$retStrBody = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$varAddResultData = array();

		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$boolExecuteContinue = true;

		$rowData = array();

		// サイズ
		$aryAddOnDefault["size"] = "10";

		// クラス属性
		$printTagId = $this->getPrintSeq();
		$strFADClassOfSelectTag = $this->getFADClassOfSelectTag();
		$strDefaultClassNamePre = "selectList psl_{$printTagId}";
		if(0 < strlen($strFADClassOfSelectTag)){
			$strDefaultClassNamePre .= " {$strFADClassOfSelectTag}";
		}
		$aryAddOnDefault["class"] = $strDefaultClassNamePre;

		// Inputタグ種類、ID、NAME
		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();

		$aryRetBody = $this->getFADMultiple($rowData,$aryVariant);
		if( $aryRetBody[1]===null ){
			if( $aryRetBody[0] === true ){
				//----複数選択可能なタグにする
				$aryOverWrite["multiple"] = "multiple";
				//複数選択可能にタグにする----
			}
		}

		// 最大横幅
		$strFADMaxWidthOfSelectTag = $this->getFADMaxWidthOfSelectTag();
		$aryOverWrite["style"] = "max-width:{$strFADMaxWidthOfSelectTag};";

		//----リストとして表示するデータソースを取得する
		$aryRetBody = $this->getFADSelectList($aryVariant, $arySetting, $aryOverride);
		if( $aryRetBody[1]!==null ){
			$boolExecuteContinue = false;
			$intErrorType = $aryRetBody[1];
		}else{
			//----リスト作成のための要素を配列にする
			$aryDataSetRow = $aryRetBody[4];
			$arraySelectElement = array();
			foreach($aryDataSetRow as $row){
				$arraySelectElement[$row['KEY_COLUMN']] = $row['DISP_COLUMN'];
			}
			unset($aryDataSetRow);
			//リスト作成のための要素を配列にする----
		}
		//----RedMineチケット1081
		if( array_key_exists(5,$aryRetBody) === true ){
			$varAddResultData = $aryRetBody[5];
		}
		//リストとして表示するデータソースを取得する----
		
		if( $boolExecuteContinue === true ){

			$data = array();

			$boolWhiteKeyAdd = true;
			if( $this->getRequired() === true ){
				$boolWhiteKeyAdd = false;
				if( $this->checkListFormatterMode("RegisterTableFormatter") === true && $this->getRegisterRequiredExcept() !== false ){
						$boolWhiteKeyAdd = true;
				}else if( $this->checkListFormatterMode("UpdateTableFormatter") === true && $this->getUpdateRequiredExcept() !== false ){
					$boolWhiteKeyAdd = true;
				}
			}
		}
		
		if( $boolExecuteContinue === true ){

			if( is_callable($this->objFunctionForGetFADMainDataOverride) === true ){
				$tmpObjFunction = $this->objFunctionForGetFADMainDataOverride;
				$aryRetBody = $tmpObjFunction($this,$arraySelectElement,$data,$boolWhiteKeyAdd,$varAddResultData,$aryVariant,$arySetting,$aryOverride);
				unset($tmpObjFunction);
				if( $aryRetBody[1] !== null ){
					$boolExecuteContinue = false;
				}
				else{
					$optionBodies = $aryRetBody[0]['optionBodies'];
					$strFADNoOptionMessageText = $aryRetBody[0]['NoOptionMessageText'];
				}
				unset($tmpObjFunction);
			}
			else{
				//----必須項目でないなら空白を選択可能
				$optionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);
				//必須項目でないなら空白を選択可能----
				$strFADNoOptionMessageText = $this->getFADNoOptionMessageText();
			}
		}

		if( $boolExecuteContinue === true ){
			if( strlen($optionBodies) === 0 && 0 < strlen($strFADNoOptionMessageText) ){
				$aryAddOnDefault = array();
				$aryOverWrite = array();

				$aryOverWrite["style"] = "max-width:{$strFADMaxWidthOfSelectTag};";
				$aryAddOnDefault["class"] = $strDefaultClassNamePre;

				$retStrBody = "<span {$this->printAttrs($aryAddOnDefault,$aryOverWrite)}>{$strFADNoOptionMessageText}<span>";
			}else{
				//----JSイベント
				$aryJsEventOverWrite = array();
				$aryObjJsEvent = $this->getFADJsEvents();

				$strJsFxStream = $this->printJsAttrs($rowData,$aryJsEventOverWrite,$aryObjJsEvent);

				$strSelectTagBody = "<select {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$strJsFxStream}>\n";
				$strSelectTagBody .= $optionBodies;
				$strSelectTagBody .="</select>";

				$retStrBody .= $strSelectTagBody;

				//----追い越し判定用フラグなどの追加タグ
				$aryRetBody  = $this->getFADAddToTagTail($rowData,$aryVariant);
				if( $aryRetBody[1]!==null ){
				}else{
					$retStrBody .= $aryRetBody[0];
				}
				//追い越し判定用フラグなどの追加タグ----

                // プルダウンあいまい検索
    			$retStrBody .= 
<<< EOD
                <script type="text/javascript">
                    var strAdjustRulerClassName = "psl_{$printTagId}";
                    var objAdjustRulerForWidth = $('#Mix2_Nakami'+' .'+strAdjustRulerClassName).get()[0];
                    if(objAdjustRulerForWidth == null){
                        var objAdjustRulerForWidth = $('#Mix1_Nakami'+' .'+strAdjustRulerClassName).get()[0];
                    }
                    var intNewWidth = objAdjustRulerForWidth.offsetWidth;
                    if(30 == intNewWidth){
                        intNewWidth = 45;
                    }
                    intNewWidth = intNewWidth + 5;
                    if(650 < intNewWidth){
                        //----広くなりすぎる場合は制限
                        intNewWidth = 650;
                    }
                    $(document).ready(function(){
                        $(".psl_{$printTagId}").select2({
                            width:intNewWidth

                        });
                    });
                </script>
EOD;
			}
		}
		$retArray = array($retStrBody,$intErrorType,$aryErrMsgBody,$strErrMsg,$varAddResultData);
		return $retArray;
	}
	//初期表示後に、(後発)動的に、リストタグを表示するメソッド----

	//GET(FAD系)----
}

class DateInputTabBFmt extends InputTabBFmt {
	//カレンダーの表示。
	protected $strIUInputType;
	protected $boolSecondsInputOnIU;
	protected $intMinuteScaleInputOnIU;

	public function __construct(){
		parent::__construct();
		$this->setIUInputTypeAgent(null);
		$this->setSecondsInputOnIUAgent(false);
		$this->setMinuteScaleInputOnIUAgent(null);
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い

		//----<textarea></textarea>がdataに入っている場合に異常動作させないための処理
		$data = $this->makeSafeValueForBrowse($data);
		//<textarea></textarea>がdataに入っている場合に異常動作させないための処理----

		$tagIdentify = $this->getFSTIDForIdentify();

		$strAddDiv = "";

		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $tagIdentify;
		$aryOverWrite["name"] = $this->getFSTNameForIdentify();

		switch($g['objMTS']->getLanguageMode()){
			case "ja_JP":$strLang = "ja";break;
			default:$strLang = "en";
		}

		if($this->getIUInputType()=="DATETIME"){

			//----分、秒までの入力版
                        // 秒の非表示の場合に最大入力サイズ調整
			$aryAddOnDefault["size"]      = "16";
			$aryAddOnDefault["maxLength"] = "16";
			$strSecondsHide = "1";
			if( $this->getSecondsInputOnIU() === true ){
			        $aryAddOnDefault["size"]      = "19";
			        $aryAddOnDefault["maxLength"] = "19";
				$strSecondsHide = "0";
			}

			$strSecStep = $this->getMinuteScaleInputOnIU();
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$aryAddOnDefault["class"] = "callDateTimePicker";
			$strClassName = $this->getFSTNameForIdentify().'hide';
			$strAddDiv = "<div class=\"{$strClassName}\" >{$strVals}</div>";
			//分、秒までの入力版----
		}else{
			//----日までの入力版
			$aryAddOnDefault["size"] = "10";
			$aryAddOnDefault["maxLength"] = "10";

			$strSecondsHide = "0";
			$strSecStep = 0;

			$aryAddOnDefault["class"] = "callDatePicker";
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$strClassName = $this->getFSTNameForIdentify().'hide';
			$strAddDiv = "<div class=\"{$strClassName}\" >{$strVals}</div>";
			//日までの入力版----
		}
		$aryOverWrite["value"] = $data;
		
		$strTagInnerBody = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)}>";
		$strTagInnerBody .= '<div style="display:none">'.$strAddDiv.'</div>';

		$aryJsEvent = $this->getJsEvents();
		$strFunctionPreFix = "";
		if( $this->getJsFxNamePrefix()===true ){ 
			$strFunctionPreFix = $this->getPrintTableID()."_";
		}
		$aryEventSet = array(
			"onchangeyear"=>11,
			"onchangemonth"=>12,
			"onchangedatetime"=>13,
			"onselectdate"=>21,
			"onselecttime"=>22,
			"onclose"=>99
		);

		$strTagInnerBody .= '<div style="display:none">';
		foreach($aryJsEvent as $jsEvent){
			$strEventName = $jsEvent->getEventName();
			if( array_key_exists(strtolower($strEventName), $aryEventSet)===true ){
				$strEventNo = $aryEventSet[strtolower($strEventName)];
				$strJsAttrsBody = $jsEvent->getJsAttr($rowData, $strFunctionPreFix, "onclick");
				$strTagInnerBody .= "<input name=\"TT_SYS_dummy\" id=\"{$tagIdentify}Agt{$strEventNo}\" {$strJsAttrsBody} >";
			}
		}
		$strTagInnerBody .= '</div>';

		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		return $this->getTag($strTagInnerBody, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setIUInputTypeAgent($strIUInputType){
		$this->strIUInputType = $strIUInputType;
	}
	public function setSecondsInputOnIUAgent($boolSecondsInputOnIU){
		$this->boolSecondsInputOnIU = $boolSecondsInputOnIU;
	}
	public function setMinuteScaleInputOnIUAgent($intMinuteScaleInputOnIU){
		$this->intMinuteScaleInputOnIU = $intMinuteScaleInputOnIU;
	}
	//他のクラスから独立して利用するための関数----

	public function getIUInputType(){
		$retValue = $this->strIUInputType;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getIUInputType')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getIUInputType();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getSecondsInputOnIU(){
		$retValue = $this->boolSecondsInputOnIU;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getSecondsInputOnIU')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getSecondsInputOnIU();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getMinuteScaleInputOnIU(){
		$retValue = $this->intMinuteScaleInputOnIU;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getMinuteScaleInputOnIU')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getMinuteScaleInputOnIU();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
}

class FileUploadTabBFmt extends InputTabBFmt {
	protected $aryCheckStorageSetting;

	protected $strActionUrlAgent;
	protected $strAnchorHrefAgent;
	protected $strLAPathOfAnchorHrefTargetAgent;
	
	protected $strMaxFileSizeNumericAgent;

	public function __construct(){
		parent::__construct();
		$this->setCheckStorageSettingAgent(array(false,0,""));
		$this->setActionUrlAgent(null);
		$this->setAnchorHrefAgent(null);
		$this->setLAPathOfAnchorHrefTargetAgent(null);
		$this->setMaxFileSizeAgent(null);
	}

	//----他のクラスから独立して利用するための関数
	public function setCheckStorageSettingAgent($aryCheckStorageSetting){
		$this->aryCheckStorageSetting = $aryCheckStorageSetting;
	}
	public function setActionUrlAgent($strActionUrlAgent){
		$this->strActionUrlAgent = $strActionUrlAgent;
	}
	public function setAnchorHrefAgent($strAnchorHrefAgent){
		$this->strAnchorHrefAgent = $strAnchorHrefAgent;
	}
	public function setLAPathOfAnchorHrefTargetAgent($strLAPathOfAnchorHrefTargetAgent){
		$this->strLAPathOfAnchorHrefTargetAgent = $strLAPathOfAnchorHrefTargetAgent;
	}
	public function setMaxFileSizeAgent($strMaxFileSizeNumericAgent){
		$this->strMaxFileSizeNumericAgent = $strMaxFileSizeNumericAgent;
	}
	//他のクラスから独立して利用するための関数----
	
	public function getCheckStorageSetting(){
		$retAryCheckStorageSetting = $this->aryCheckStorageSetting;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getCheckStorageSetting') === true ){
				$retAryCheckStorageSetting = $this->objColumn->getCheckStorageSetting();
			}
			//カラムがセットされている場合----
		}
		return $retAryCheckStorageSetting;
	}

	public function getActionUrl(){
		$retStrActionUrl = $this->strActionUrlAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getOAPathToUploadScriptFile') === true ){
				$retStrActionUrl = $this->objColumn->getOAPathToUploadScriptFile();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionUrl;
	}

	public function getAnchorHref($rowData){
		$retStrAnchorHref = $this->strAnchorHrefAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getOAPathToFUCItemPerRow') === true ){
				$retStrAnchorHref = $this->objColumn->getOAPathToFUCItemPerRow($rowData);
			}
			//カラムがセットされている場合----
		}
		return $retStrAnchorHref;
	}

	public function getLAPathOfAnchorHrefTarget($rowData){
		$retStrLAPathOfAnchorHrefTarget = $this->strLAPathOfAnchorHrefTargetAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getLAPathToFUCItemPerRow') === true ){
				$retStrLAPathOfAnchorHrefTarget = $this->objColumn->getLAPathToFUCItemPerRow($rowData);
			}
			//カラムがセットされている場合----
		}
		return $retStrLAPathOfAnchorHrefTarget;
	}

	public function getMaxFileSize(){
		$retStrMaxFileSizeNumeric = $this->strMaxFileSizeNumericAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getMaxFileSize') === true ){
				$retStrMaxFileSizeNumeric = $this->objColumn->getMaxFileSize();
			}
			//カラムがセットされている場合----
		}
		return $retStrMaxFileSizeNumeric;
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();
		
		$retStrVal = "";
		
		$intControlDebugLevel01 = 50;
		
		$boolProcessContinue = true;
		$strTagInnerBody = "";
		
		$arrayTempRet = $this->getCheckStorageSetting();
		
		$boolProcessContinue = $arrayTempRet[0];
		$strTagInnerBody = $arrayTempRet[2];

		if( $boolProcessContinue === true ){
			$strColId = $this->getPrintTargetKey();
			
			$strColMark = $strColId;
			if( $this->getColumnIDHidden() === true ){
				$strColMark = $this->getIDSynonym();
			}
			
			//----IU時に利用するもの
			$strIdOfFSTOfTmpFile = "{$this->strFormatterId}_tmp_file_{$strColMark}";
			$strNameOfFSTOfTmpFile = "tmp_file_{$strColMark}";

			//----ローカルでの元名前の一時保存
			$strIdOfFSTOfOrgName = "{$this->strFormatterId}_org_file_{$strColMark}";
			$strNameOfFSTOfOrgName = "org_file_{$strColMark}";
			//ローカルでの元名前の一時保存----

			$strIdOfFSTOfDelFlag = "{$this->strFormatterId}_del_{$strColMark}";
			$strNameOfFSTOfDelFlag = "del_flag_{$strColMark}";
			//IU時に利用するもの----

			//----事前アップロード時にメインで利用するもの
			$strIdOfIframe = "{$this->strFormatterId}_if_{$strColMark}";
			$strNameOfIframe = "{$this->strFormatterId}_if_{$strColMark}";
			
			$strIdOfForm = "{$this->strFormatterId}_{$strColMark}";
			
			$strIdOfInputButton = "{$this->strFormatterId}_btn_{$strColMark}";
			$strIdOfResultArea = "{$this->strFormatterId}_result_{$strColMark}";
			
			//----どのカラムに向けての送信かの識別用
			$strIdOfFSTOfFileId = "{$this->strFormatterId}_file_id_{$strColMark}";
			$strNameOfFSTOfFileId = "file_id_{$strColMark}";
			//どのカラムに向けての送信かの識別用----
			
			//----どのリストフォーマッタからの送信か
			$strNameOfFromFormatterId = "frmFmt_{$strColMark}";
			//どのリストフォーマッタからの送信か----
			
			$strLabelForDelFlag = "del{$strColMark}";
			
			//事前アップロード時にメインで利用するもの----
			
			$current = "";
			$strDummyValue01 = "dummy";
			
			list($fileName,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
			
			if( 1 <= strlen($fileName) ){
				//----ファイルがアップロードされている場合
				
				$url = $this->getAnchorHref($rowData);

				$fileName = $this->makeSafeValueForBrowse($fileName);

				$strLAPathOfRefTargetFile = $this->getLAPathOfAnchorHrefTarget($rowData);
				if( file_exists($strLAPathOfRefTargetFile) === true ){
					$strAnchorTag = "<a href=\"{$url}\" target=\"_blank\">{$fileName}</a>";
				}else{
					//$strAnchorTag="ファイル({$fileName})が見つかません。";
					$strAnchorTag = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-12102",$fileName);
				}
				//必須項目の場合
				if( $this->getRequired() === true ){
					$current = 
<<<EOD
{$g['objMTS']->getSomeMessage("ITAWDCH-STD-631")}: <br />
{$strAnchorTag}
EOD;
                }else{
                	$current = 
<<<EOD
{$g['objMTS']->getSomeMessage("ITAWDCH-STD-631")}: <br />
{$strAnchorTag}
<p>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-632")}</p>
<input type="checkbox" id="{$strIdOfFSTOfDelFlag}" name="{$strNameOfFSTOfDelFlag}" />
<label for="{$strLabelForDelFlag}">{$g['objMTS']->getSomeMessage("ITAWDCH-STD-633")}</label>

<script type="text/javascript">
document.getElementById("{$strIdOfFSTOfDelFlag}").onchange = function(){
    if(this.checked == true){
        $("#"+"{$strIdOfForm}_file").attr("disabled",true);
        $("#"+"{$strIdOfInputButton}").attr("disabled",true); 
    }
    else{
        $("#"+"{$strIdOfForm}_file").attr("disabled",false);
        $("#"+"{$strIdOfInputButton}").attr("disabled",false); 
    }
}
</script>
EOD;
				}
				//
				//ファイルがアップロードされている場合----
			}
			//Sequenceが指定されていない場合は、もはや存在しなくなったので、分岐機能を削除----
		}
		
		if( $boolProcessContinue === true ){
			
			$strConfSetFilesizeNumeric = $this->getMaxFileSize();
			
			if( 1 > strlen($strConfSetFilesizeNumeric) ){
				$strConfSetFilesizeNumeric = 20000000;
			}
			
			$strActionUrl = $this->getActionUrl();
			
			$strTagInnerBody = 
<<<EOD
{$current}<iframe id="{$strIdOfIframe}" name="{$strNameOfIframe}" style="display:none" >
</iframe><form id="{$strIdOfForm}" action="{$strActionUrl}" method="POST" encoding="multipart/form-data" enctype="multipart/form-data" target="{$strIdOfIframe}"><input type="hidden" id="{$strIdOfFSTOfTmpFile}" name="{$strNameOfFSTOfTmpFile}" value="" /><input type="hidden" id="{$strIdOfFSTOfFileId}" name="{$strNameOfFSTOfFileId}" value="{$strDummyValue01}" /><input type="hidden" name="MAX_FILE_SIZE" value="{$strConfSetFilesizeNumeric}" /><input type="hidden" name="{$strNameOfFromFormatterId}" value="{$this->strFormatterId}" /><span name="filewrapper"><input type="file" name="file" id="{$strIdOfForm}_file"/></span></form><input type="button" id="{$strIdOfInputButton}" name="1" value="{$g['objMTS']->getSomeMessage("ITAWDCH-STD-634")}" onclick="
formControlForFUCFileUpLoad(
    this,'{$strIdOfForm}','{$strIdOfResultArea}','{$strIdOfIframe}','{$strIdOfFSTOfTmpFile}','{$strIdOfInputButton}',
    '{$g['objMTS']->getSomeMessage("ITAWDCH-ERR-12103")}','{$g['objMTS']->getSomeMessage("ITAWDCH-STD-635")}'
);
" />
{$g['objMTS']->getSomeMessage("ITAWDCH-STD-636")}:<div id="{$strIdOfResultArea}"></div><br />
<script type="text/javascript">
document.getElementById("{$strIdOfForm}_file").onchange = function(){
    if(document.getElementById("{$strIdOfFSTOfDelFlag}") != null){
        if(this.value.length != 0){
            $("#"+"{$strIdOfFSTOfDelFlag}").attr("disabled",true).css("color", "#9E9E9E");
        }
        else{
            $("#"+"{$strIdOfFSTOfDelFlag}").attr("disabled",false).css("color", "#000000");
        }
    }
}
</script>
EOD;
			
		}
		
		if( is_callable($this->objFunctionForReturnOverrideGetData) === true ){
			$objFunction = $this->objFunctionForReturnOverrideGetData;
			$strTagInnerBody = $objFunction($strTagInnerBody,$this,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite);
		}

		$retStrVal = $this->getTag($strTagInnerBody, $rowData);

		return $retStrVal;
	}

}

//ここまで、Write入力系----

//----フィルター入力系
class FilterTabBFmt extends TabBFmt {
	//FAD=[F]or-[A]fter-[D]emand

	//----OnTime用タグ属性
	protected $boolSelectTagCallerShow;
	//OnTime用タグ属性----

	//----FADタグ属性
	protected $strFADClassOfSelectTagWrapper;
	protected $strFADClassOfSelectTagCaller;
	protected $strFADClassOfSelectTag; //出力されるSELECTタグのクラス
	protected $strFADMaxWidthOfSelectTag;

	protected $objFunctionForGetFADMultiple;
	protected $objFunctionForGetFADSelectList;

	protected $aryObjFADJsEvents;
	//FADタグ属性----

	public function __construct($strPrintType=""){
		parent::__construct();
		$this->strPrintType = $strPrintType; //ほぼ廃止
		
		$this->setSelectTagCallerShowAgent(false);

		//----FADタグ属性
		$this->setFADMaxWidthOfSelectTag();
		$this->setFADClassOfSelectTagWrapper("richFilterSelectListWrapper");
		$this->setFADClassOfSelectTagCaller("richFilterSelectListCaller");
		$this->setFADClassOfSelectTag("richFilterSelectList");

		$this->setFunctionForGetFADMultiple(null);
		$this->setFunctionForGetFADSelectList(null);

		$this->aryObjFADJsEvents = array();

		$this->setFADJsEvent('onChange', 'search_async', array("'idcolumn_filter_default'"));
		//FADタグ属性----
	}

	//----他のクラスから独立して利用するための関数
	public function setSelectTagCallerShowAgent($boolSelectTagCallerShow){
		$this->boolSelectTagCallerShow = $boolSelectTagCallerShow;
	}

	//他のクラスから独立して利用するための関数----

	public function getSelectTagCallerShow(){
		$boolSelectTagCallerShow = $this->boolSelectTagCallerShow;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$boolSelectTagCallerShow = $this->objColumn->getSelectTagCallerShow();
			//カラムがセットされている場合----
		}
		return $boolSelectTagCallerShow;
	}

	public function setFADMaxWidthOfSelectTag($strFADMaxWidthOfSelectTag="640px"){
		$this->strFADMaxWidthOfSelectTag = $strFADMaxWidthOfSelectTag;
	}
	public function setFADClassOfSelectTagWrapper($strClasses=""){
		$this->strFADClassOfSelectTagWrapper = $strClasses;
	}
	public function setFADClassOfSelectTagCaller($strClasses=""){
		$this->strFADClassOfSelectTagCaller = $strClasses;
	}
	public function setFADClassOfSelectTag($strClasses=""){
		$this->strFADClassOfSelectTag = $strClasses;
	}
	public function getFADMaxWidthOfSelectTag(){
		return $this->strFADMaxWidthOfSelectTag;
	}
	public function getFADClassOfSelectTagWrapper(){
		return $this->strFADClassOfSelectTagWrapper;
	}
	public function getFADClassOfSelectTagCaller(){
		return $this->strFADClassOfSelectTagCaller;
	}
	public function getFADClassOfSelectTag(){
		return $this->strFADClassOfSelectTag;
	}
	public function setFADJsEvent($strEventName, $strJsFunctionName, $aryJsFunctionArgs=array()){
		if( is_null($aryJsFunctionArgs) === true ){
			//----引数がnullで指定されていた場合
			$aryJsFunctionArgs = array();
		}
		if( is_string($strEventName) === true && $strJsFunctionName === null ){
			if( array_key_exists($strEventName, $this->aryObjFADJsEvents) === true ){
				unset($this->aryObjFADJsEvents[$strEventName]);
			}
		}else{
			$this->aryObjFADJsEvents[$strEventName] = new JsEvent($strEventName, $strJsFunctionName, $aryJsFunctionArgs);
		}
	}

	public function setFunctionForGetFADMultiple($objFunctionForGetMultiple){
		$this->objFunctionForGetFADMultiple = $objFunctionForGetMultiple;
	}
	public function setFunctionForGetFADSelectList($objFunctionForGetFADSelectList){
		$this->objFunctionForGetFADSelectList = $objFunctionForGetFADSelectList;
	}

	public function getFunctionForGetFADMultiple(){
		return $this->objFunctionForGetFADMultiple;
	}
	public function getFunctionForGetFADSelectList(){
		return $this->objFunctionForGetFADSelectList;
	}

	public function getFADJsEvents(){
		return $this->aryObjFADJsEvents;
	}

	public function getFADMultiple($rowData, $aryVariant){
		$retBool = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";

		$objFunction = $this->objFunctionForGetFADMultiple;
		if( is_callable($objFunction) === true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			if( is_a($this->objColumn, "IDColumn") === true ){
				//----IDColumnとの連動系
				
				$retBool = $this->objColumn->getMultiple($this->strFormatterId);
				
				//IDColumnとの連動系----
			}
			$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg);
		}
		return $aryRetBody;
	}

	public function getFADSelectList($aryVariant, $arySetting, $aryOverride){
		$retBool = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$aryDataSet = array();

		$objFunction = $this->objFunctionForGetFADSelectList;
		if( is_callable($objFunction)=== true ){
			$aryRetBody = $objFunction($this, $aryVariant, $arySetting, $aryOverride);
		}else{
			if( is_a($this->objColumn, "Column")===true ){
				$objOT = $this->objColumn->getOutputType($this->strFormatterId);
				$aryRetBody = $objOT->getFADSelectList($aryVariant, $arySetting, $aryOverride);
			}else{
				$aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
			}
		}
		return $aryRetBody;
	}

	public function getFSTIDForIdentify($strPostFix="", $strPreFix=""){
		// フィルター系側が1タグと限らないので、インプット系と、この関数を共有をしない
		$retStrVal = "";

		if( $strPreFix=="" ){
			$strPreFix = $this->getPrintTableID()."_";
		}

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $this->getJsFxNamePrefix()===true ){
			$retStrVal = $this->getPrintTableID()."_".$this->getPrintSeq().$strPostFix;
		}else{
			if( $this->getColumnIDHidden() === true ){
				$retStrVal = $strPreFix.$this->getPrintSeq().$strPostFix;
			}else{
				$retStrVal = $strPreFix.$strColId.$strPostFix;
			}
		}

		return $retStrVal;
	}

	// ----テキスト用プルダウンタグ
	public function getAddSelectTagArea(){
		global $g;
		$body = "";

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $this->getSelectTagCallerShow()=== true ){
			$strJsEventNamePrefix = "";
			if( $this->getJsFxNamePrefix()===true){
				$strJsEventNamePrefix = $this->getPrintTableID()."_";
			}
			$selectTagShowJSBody = "javascript:{$strJsEventNamePrefix}add_selectbox('{$this->getPrintSeq()}')";
			$strSetIdBody = "na_{$this->getPrintTableID()}_{$strColSynonym}";
			
			$body .= "<br>";
			$body .= "<div class=\"{$this->getPrintSeq()} {$this->strFADClassOfSelectTagWrapper}\">";
			$body .= "<div onclick=\"{$selectTagShowJSBody}\" class=\"{$this->strFADClassOfSelectTagCaller}\">{$g['objMTS']->getSomeMessage("ITAWDCH-STD-641")}</div>";
			$body .= "<div class=\"{$strSetIdBody}\"></div>";
			$body .= "</div>";
		}
		return $body;
	}

	function printTagFromFADSelectList(&$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
		global $g;
		$retStrBody = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$varAddResultData = array();

		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strFxName = __CLASS__."::".__FUNCTION__;

		$boolExecuteContinue = true;

		$strColSynonym = $this->getIDSynonym();

		if( $this->getJsFxNamePrefix()===true){
			$strFunctionNamePrefix = $this->getPrintTableID()."_";
		}else{
			$strFunctionNamePrefix = "";
		}

		$rowData = array();

		// サイズ
		$aryAddOnDefault["size"] = "10";

		// クラス属性
		$printTagId = $this->getPrintSeq();
		$strFADClassOfSelectTag = $this->getFADClassOfSelectTag();
		$strDefaultClassNamePre = "psl_{$printTagId}";
		if(0 < strlen($strFADClassOfSelectTag)){
			$strDefaultClassNamePre .= " {$strFADClassOfSelectTag}";
		}
		$aryAddOnDefault["class"] = $strDefaultClassNamePre;

		// Inputタグ種類、ID、NAME
		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $this->getFSTIDForIdentify();
		$strAttNamePostPix = "_RF";
		$aryOverWrite["name"] = "{$strColSynonym}{$strAttNamePostPix}";

		// 複数選択
		$aryRetBody = $this->getFADMultiple($rowData,$aryVariant);
		if( $aryRetBody[1]===null ){
			if( $aryRetBody[0] === true ){
				//----複数選択可能なタグにする
				$aryOverWrite["multiple"] = "multiple";
				//複数選択可能にタグにする----
			}
		}

		// 最大横幅
		$strFADMaxWidthOfSelectTag = $this->getFADMaxWidthOfSelectTag();
		$aryOverWrite["style"] = "max-width:{$strFADMaxWidthOfSelectTag};";

		//----リストとして表示するデータソースを取得する
		$aryRetBody = $this->getFADSelectList($aryVariant, $arySetting, $aryOverride);
		if( $aryRetBody[1]!==null ){
			$intErrorType = $aryRetBody[1];
			$boolExecuteContinue = false;
		}else{
			$aryDataSetRow = $aryRetBody[4];
			$arraySelectElement = array();
			foreach($aryDataSetRow as $row){
				$arraySelectElement[$row['KEY_COLUMN']] = $row['DISP_COLUMN'];
			}
			unset($aryDataSetRow);
		}
		if( array_key_exists(5,$aryRetBody) === true ){
			$varAddResultData = $aryRetBody[5];
		}
		//リストとして表示するデータソースを取得する----

		if( $boolExecuteContinue === true ){

			$data = array();

			$boolWhiteKeyAdd = false;

			//----空白
			$optionBodyHead = "<OPTION VALUE=\"\" >{{$g['objMTS']->getSomeMessage("ITAWDCH-STD-16301")}}</OPTION>";
			//空白----

			$optionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);

			$optionBodyTail = "";

			$aryJsEventOverWrite = array();
			$aryObjJsEvent = $this->getFADJsEvents();

			$strJsFxStream = $this->printJsAttrs($rowData,$aryJsEventOverWrite,$aryObjJsEvent);

			$strSelectTagBody = "<select {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$strJsFxStream}>\n";
			$strSelectTagBody .= $optionBodyHead.$optionBodies.$optionBodyTail;
			$strSelectTagBody .="</select>";

			$retStrBody .= $strSelectTagBody;
		}
		$retArray = array($retStrBody,$intErrorType,$aryErrMsgBody,$strErrMsg,$varAddResultData);
		return $retArray;
	}

	// テキスト用プルダウンタグ----

}

class DateFilterTabBFmt extends FilterTabBFmt {

	protected $strFilterInputType;
	protected $boolSecondsInputOnFilter;
	protected $intMinuteScaleInputOnFilter;

	public function __construct(){
		parent::__construct();
		$this->setFilterInputTypeAgent(null);
		$this->setSecondsInputOnFilterAgent(false);
		$this->setMinuteScaleInputOnFilterAgent(null);
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		$aryOverWrite["type"] = "text";

		$tagIdentify_S = $this->getFSTIDForIdentify("__S","");
		$tagIdentify_E = $this->getFSTIDForIdentify("__E","");

		$strAddDiv_S = "";
		$strAddDiv_E = "";

		switch($g['objMTS']->getLanguageMode()){
			case "ja_JP":$strLang = "ja";break;
			default:$strLang = "en";
		}

		if( $this->getFilterInputType() == "DATETIME" ){
			//----分、秒までの入力版
			
                        // 秒の非表示の場合に最大入力サイズ調整
			$aryAddOnDefault["size"] = "16";
			$aryAddOnDefault["maxLength"] = "16";
			
			$strSecondsHide = "1";
			if( $this->getSecondsInputOnFilter() === true ){
			        $aryAddOnDefault["size"]      = "19";
			        $aryAddOnDefault["maxLength"] = "19";
				$strSecondsHide = "0";
			}

			$strSecStep = $this->getMinuteScaleInputOnFilter();
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$aryAddOnDefault["class"] = "callDateTimePicker";
			$strClassName1 = $strColSynonym.'__S'.'hide';
			$strClassName2 = $strColSynonym.'__E'.'hide';
			$strAddDiv_S = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
			$strAddDiv_E = "<div class=\"{$strClassName2}\" >{$strVals}</div>";
			//分、秒までの入力版----
		}else{
			//----日までの入力版
			
			$aryAddOnDefault["size"] = "10";
			$aryAddOnDefault["maxLength"] = "10";
			
			$strSecondsHide = "0";
			$strSecStep = $this->getMinuteScaleInputOnFilter();
			$strSecStep = "0";
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$aryAddOnDefault["class"] = "callDatePicker";
			$strClassName1 = $strColSynonym.'__S'.'hide';
			$strClassName2 = $strColSynonym.'__E'.'hide';
			$strAddDiv_S = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
			$strAddDiv_E = "<div class=\"{$strClassName2}\" >{$strVals}</div>";
			//日までの入力版----
		}

		$body  = "";
		$body .= '<input id="'.$tagIdentify_S.'" name="'.$strColSynonym.'__S"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';
		$body .= " ～ ";
		$body .= '<input id="'.$tagIdentify_E.'" name="'.$strColSynonym.'__E"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';
		$body .= '<div style="display:none">'.$strAddDiv_S.$strAddDiv_E.'</div>';

		$aryJsEvent = $this->getJsEvents();
		$strFunctionPreFix = "";
		if( $this->getJsFxNamePrefix()===true ){ 
			$strFunctionPreFix = $this->getPrintTableID()."_";
		}

		$aryEventSet = array(
			"onchangeyear"=>11,
			"onchangemonth"=>12,
			"onchangedatetime"=>13,
			"onselectdate"=>21,
			"onselecttime"=>22,
			"onclose"=>99
		);

		$body .= '<div style="display:none">';
		foreach($aryJsEvent as $jsEvent){
			$strEventName = $jsEvent->getEventName();
			if( array_key_exists(strtolower($strEventName), $aryEventSet)===true ){
				$strEventNo = $aryEventSet[strtolower($strEventName)];
				$strJsAttrsBody = $jsEvent->getJsAttr($rowData, $strFunctionPreFix, "onclick");
				$body .= "<input name=\"TT_SYS_dummy\" id=\"{$tagIdentify_S}Agt{$strEventNo}\" {$strJsAttrsBody} >";
				$body .= "<input name=\"TT_SYS_dummy\" id=\"{$tagIdentify_E}Agt{$strEventNo}\" {$strJsAttrsBody} >";
			}
			
		}
		$body .= '</div>';

		$body .= $this->getAddSelectTagArea();

		return $this->getTag($body, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setFilterInputTypeAgent($strFilterInputType){
		$this->strFilterInputType = $strFilterInputType;
	}
	public function setSecondsInputOnFilterAgent($boolSecondsInputOnFilter){
		$this->boolSecondsInputOnFilter = $boolSecondsInputOnFilter;
	}
	public function setMinuteScaleInputOnFilterAgent($intMinuteScaleInputOnFilter){
		$this->intMinuteScaleInputOnFilter = $intMinuteScaleInputOnFilter;
	}
	//他のクラスから独立して利用するための関数----

	public function getFilterInputType(){
		$retValue = $this->strFilterInputType;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getFilterInputType')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getFilterInputType();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getSecondsInputOnFilter(){
		$retValue = $this->boolSecondsInputOnFilter;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getSecondsInputOnFilter')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getSecondsInputOnFilter();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getMinuteScaleInputOnFilter(){
		$retValue = $this->intMinuteScaleInputOnFilter;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getMinuteScaleInputOnFilter')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getMinuteScaleInputOnFilter();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}

}

class SingleDateFilterTabBFmt extends DateFilterTabBFmt {

    public function getData($rowData,$aryVariant){
        global $g;
        $aryAddOnDefault = array();
        $aryOverWrite = array();

        $strColId = $this->getPrintTargetKey();
        $strColSynonym = $this->getIDSynonym();

        $aryOverWrite["type"] = "text";

        $tagIdentify_S = $this->getFSTIDForIdentify("__S","");

        $strAddDiv_S = "";

        switch($g['objMTS']->getLanguageMode()){
            case "ja_JP":$strLang = "ja";break;
            default:$strLang = "en";
        }

        if( $this->getFilterInputType() == "DATETIME" ){
            //----分、秒までの入力版
            
                        // 秒の非表示の場合に最大入力サイズ調整
            $aryAddOnDefault["size"] = "16";
            $aryAddOnDefault["maxLength"] = "16";
            
            $strSecondsHide = "1";
            if( $this->getSecondsInputOnFilter() === true ){
                    $aryAddOnDefault["size"]      = "19";
                    $aryAddOnDefault["maxLength"] = "19";
                $strSecondsHide = "0";
            }

            $strSecStep = $this->getMinuteScaleInputOnFilter();
            $strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
            $aryAddOnDefault["class"] = "callDateTimePicker";
            $strClassName1 = $strColSynonym.'__S'.'hide';
            $strAddDiv_S = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
            //分、秒までの入力版----
        }else{
            //----日までの入力版
            
            $aryAddOnDefault["size"] = "10";
            $aryAddOnDefault["maxLength"] = "10";
            
            $strSecondsHide = "0";
            $strSecStep = $this->getMinuteScaleInputOnFilter();
            $strSecStep = "0";
            $strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
            $aryAddOnDefault["class"] = "callDatePicker";
            $strClassName1 = $strColSynonym.'__S'.'hide';
            $strAddDiv_S = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
            //日までの入力版----
        }

        $body  = "";
        $body .= '<input id="'.$tagIdentify_S.'" name="'.$strColSynonym.'__S"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';
        $body .= '<div style="display:none">'.$strAddDiv_S.'</div>';

        $aryJsEvent = $this->getJsEvents();
        $strFunctionPreFix = "";
        if( $this->getJsFxNamePrefix()===true ){ 
            $strFunctionPreFix = $this->getPrintTableID()."_";
        }

        $aryEventSet = array(
            "onchangeyear"=>11,
            "onchangemonth"=>12,
            "onchangedatetime"=>13,
            "onselectdate"=>21,
            "onselecttime"=>22,
            "onclose"=>99
        );

        $body .= '<div style="display:none">';
        foreach($aryJsEvent as $jsEvent){
            $strEventName = $jsEvent->getEventName();
            if( array_key_exists(strtolower($strEventName), $aryEventSet)===true ){
                $strEventNo = $aryEventSet[strtolower($strEventName)];
                $strJsAttrsBody = $jsEvent->getJsAttr($rowData, $strFunctionPreFix, "onclick");
                $body .= "<input name=\"TT_SYS_dummy\" id=\"{$tagIdentify_S}Agt{$strEventNo}\" {$strJsAttrsBody} >";
            }
            
        }
        $body .= '</div>';

        $body .= $this->getAddSelectTagArea();

        return $this->getTag($body, $rowData);
    }
}

class DateRangeInFilterTabBFmt extends FilterTabBFmt {

	protected $strFilterInputType;
	protected $boolSecondsInputOnFilter;
	protected $intMinuteScaleInputOnFilter;

	public function __construct(){
		parent::__construct();
		$this->setFilterInputTypeAgent(null);
		$this->setSecondsInputOnFilterAgent(false);
		$this->setMinuteScaleInputOnFilterAgent(null);
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $rowData === null ){
			$data = $this->getDefaultValue(false,true,$rowData,$aryVariant);//配列がデフォルト値の場合はnull扱い
		}else{
			$data = array_key_exists($strColId, $rowData)?$rowData[$strColId]:"";
		}

		$aryOverWrite["type"] = "text";
		$aryOverWrite["value"] = $data;

		$tagIdentify = $this->getFSTIDForIdentify("","");

		$strAddDiv = "";

		switch($g['objMTS']->getLanguageMode()){
			case "ja_JP":$strLang = "ja";break;
			default:$strLang = "en";
		}

		if( $this->getFilterInputType() == "DATETIME" ){
			//----分、秒までの入力版
			
                        // 秒の非表示の場合に最大入力サイズ調整
			$aryAddOnDefault["size"] = "16";
			$aryAddOnDefault["maxLength"] = "16";
			$strSecondsHide = "1";
			if( $this->getSecondsInputOnFilter() === true ){
			    $aryAddOnDefault["size"] = "19";
			    $aryAddOnDefault["maxLength"] = "19";
			    $strSecondsHide = "0";
			}

			$strSecStep = $this->getMinuteScaleInputOnFilter();
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$aryAddOnDefault["class"] = "callDateTimePicker";
			$strClassName1 = $strColSynonym.'hide';
			$strAddDiv = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
			//分、秒までの入力版----
		}else if( $this->getFilterInputType() == "DATE" ){
			//----日までの入力版
			
			$aryAddOnDefault["size"] = "10";
			$aryAddOnDefault["maxLength"] = "10";
			
			$strSecondsHide = "0";
			$strSecStep = $this->getMinuteScaleInputOnFilter();
			$strSecStep = "0";
			$strVals = $strLang.",".$strSecondsHide.",".$strSecStep;
			$aryAddOnDefault["class"] = "callDatePicker";
			$strClassName1 = $strColSynonym.'hide';
			$strAddDiv = "<div class=\"{$strClassName1}\" >{$strVals}</div>";
			//日までの入力版----
		}

		$body  = "";
		$body .= '<input id="'.$tagIdentify.'" name="'.$strColSynonym.'"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';
		$body .= '<div style="display:none">'.$strAddDiv.'</div>';

		$aryJsEvent = $this->getJsEvents();
		$strFunctionPreFix = "";
		if( $this->getJsFxNamePrefix()===true ){ 
			$strFunctionPreFix = $this->getPrintTableID()."_";
		}

		$aryEventSet = array(
			"onchangeyear"=>11,
			"onchangemonth"=>12,
			"onchangedatetime"=>13,
			"onselectdate"=>21,
			"onselecttime"=>22,
			"onclose"=>99
		);

		$body .= '<div style="display:none">';
		foreach($aryJsEvent as $jsEvent){
			$strEventName = $jsEvent->getEventName();
			if( array_key_exists(strtolower($strEventName), $aryEventSet)===true ){
				$strEventNo = $aryEventSet[strtolower($strEventName)];
				$strJsAttrsBody = $jsEvent->getJsAttr($rowData, $strFunctionPreFix, "onclick");
				$body .= "<input name=\"TT_SYS_dummy\" id=\"{$tagIdentify}Agt{$strEventNo}\" {$strJsAttrsBody} >";
			}
		}
		$body .= '</div>';

		$body .= $this->getAddSelectTagArea();

		return $this->getTag($body, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setFilterInputTypeAgent($strFilterInputType){
		$this->strFilterInputType = $strFilterInputType;
	}
	public function setSecondsInputOnFilterAgent($boolSecondsInputOnFilter){
		$this->boolSecondsInputOnFilter = $boolSecondsInputOnFilter;
	}
	public function setMinuteScaleInputOnFilterAgent($intMinuteScaleInputOnFilter){
		$this->intMinuteScaleInputOnFilter = $intMinuteScaleInputOnFilter;
	}
	//他のクラスから独立して利用するための関数----

	public function getFilterInputType(){
		$retValue = $this->strFilterInputType;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getFilterInputType')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getFilterInputType();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getSecondsInputOnFilter(){
		$retValue = $this->boolSecondsInputOnFilter;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getSecondsInputOnFilter')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getSecondsInputOnFilter();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}
	public function getMinuteScaleInputOnFilter(){
		$retValue = $this->intMinuteScaleInputOnFilter;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getMinuteScaleInputOnFilter')===true ){
				//----メソッドが存在している場合
				$retValue = $this->objColumn->getMinuteScaleInputOnFilter();
				//メソッドが存在している場合----
			}
			//カラムがセットされている場合----
		}
		return $retValue;
	}

}

class TextFilterTabBFmt extends FilterTabBFmt {

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $rowData === null ){
			$data = $this->getDefaultValue(false,true,$rowData,$aryVariant);//配列がデフォルト値の場合はnull扱い
		}else{
			$data = array_key_exists($strColId, $rowData)?$rowData[$strColId]:"";
		}

		$tagIdentify = $this->getFSTIDForIdentify("","");

		$aryAddOnDefault["maxLength"] = $this->getMaxInputLength();
		$aryAddOnDefault["size"] = "15";

		$aryOverWrite["type"] = "text";
		$aryOverWrite["id"] = $tagIdentify;
		$aryOverWrite["name"] = $strColSynonym;
		$aryOverWrite["value"] = $data;

		$body  = "";
		$body .= "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>";
		$body .= $this->getAddSelectTagArea();

		return $this->getTag($body, $rowData);
	}

}

class NumRangeFilterTabBFmt extends TextFilterTabBFmt {
	protected $boolNumberSepaMarkShow;
	protected $intDigitScale;

	public function __construct($intDigitScale=0,$boolNumberSepaMarkShow=true){
		parent::__construct();
		$this->setNumberSepaMarkShowAgent($boolNumberSepaMarkShow);
		$this->setDigit($intDigitScale);
	}

	public function getValueForDisplay($data){
		if( $this->boolNumberSepaMarkShow == false ){
			$retStrVal = $data;
		}else{
			if($data != ""){
				$retStrVal = number_format($data, $this->intDigitScale, '.', ',');
			}else{
				$retStrVal = "";
			}
		}
		$retStrVal = str_replace("-.","-0.",$retStrVal);
		return $retStrVal;
	}

	public function getData($rowData,$aryVariant){
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $rowData === null ){
			$data = $this->getDefaultValue(false,true,$rowData,$aryVariant);//配列がデフォルト値の場合はnull扱い
		}else{
			$data = array_key_exists($strColId, $rowData)?$rowData[$strColId]:"";
		}

		$aryAddOnDefault["maxLength"] = 10;
		$aryAddOnDefault["size"] = "10";

		$aryOverWrite["type"] = "text";
		$aryOverWrite["value"] = $data;

		$tagIdentify_S = $this->getFSTIDForIdentify("__S","");
		$tagIdentify_E = $this->getFSTIDForIdentify("__E","");

		$body  = "";
		$body .= '<input id="'.$tagIdentify_S.'" name="'.$strColSynonym.'__S"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';
		$body .= " ～ ";
		$body .= '<input id="'.$tagIdentify_E.'" name="'.$strColSynonym.'__E"'.$this->printAttrs($aryAddOnDefault,$aryOverWrite).$this->printJsAttrs($rowData).' >';

		$body .= $this->getAddSelectTagArea();

		return $this->getTag($body, $rowData);
	}

	//----他のクラスから独立して利用するための関数
	public function setNumberSepaMarkShowAgent($boolNumberSepaMarkShow){
		$this->boolNumberSepaMarkShow = $boolNumberSepaMarkShow;
	}

	//他のクラスから独立して利用するための関数----

	public function setDigit($intDigitScale){
		$this->intDigitScale = $intDigitScale;
	}

}

class SelectFilterTabBFmt extends TextFilterTabBFmt {

	public function getSettingDataBeforeEdit($arraySelectElement,$rowData,$aryVariant){
		//----selectedを返す
		$data = parent::getSettingDataBeforeEdit(false,false,$rowData,$aryVariant); //----設定値が配列の場合も取得
		
		if( $data === null){
			$selected = null;
		}else{
			if(is_array($data)===true){
				$selected = $data;
				if(0 < count($data)){
					$selected = $data;
				}
			}else{
				//IDが数字ならint型に変える----
				//$selected = is_numeric($data)?(int)$data:$data;
				$selected = $data;
			}
			unset($data);
		}
		return $selected;
	}

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		$strMainTableBody = $this->objColumn->getTable()->getDBMainTableBody();
		$strDUColIdOfMainTable = $this->objColumn->getTable()->getRequiredDisuseColumnID();

		$strMasterTableBody = $this->objColumn->getMasterTableBodyForFilter();
		$strKeyColumnOfMasterTable = $this->objColumn->getKeyColumnIDOfMaster();
		$strDispColumnOfMasterTable = $this->objColumn->getDispColumnIDOfMaster();
		$strDUColumnOfMasterTable = $this->objColumn->getRequiredDisuseColumnID();

		$aryEtcetera = $this->objColumn->getEtceteraParameter();

		$tagIdentify = $this->getFSTIDForIdentify("","");

		$aryAddOnDefault["size"] = "10";
		$aryAddOnDefault["class"] = "selectList";

		$aryOverWrite["id"] = $tagIdentify;
		$aryOverWrite["name"] = $strColSynonym;

		if( $this->objColumn->getMultiple($this->strFormatterId) === true ){
			//----複数選択可能なタグにする
			$aryOverWrite["multiple"] = "multiple";
			//複数選択可能にタグにする----
		}

		$arrayDispSelectTag=$this->objColumn->getArrayMasterTableByFormatName($this->strFormatterId);

		if($arrayDispSelectTag === null){
			//----filter等、各Formatter用に、独自に設定されていなかった場合（通常の場合）
			$arrayDispSelectTag=$this->objColumn->getMasterTableArrayFromMainTable();
			if(is_null($arrayDispSelectTag)===true){
				$arraySelectElement=createMasterTableDistinctArray($strMainTableBody, $strColId, $strDUColIdOfMainTable, $strMasterTableBody, $strKeyColumnOfMasterTable, $strDispColumnOfMasterTable, $strDUColumnOfMasterTable, $aryEtcetera);
			}else{
				$arraySelectElement=$arrayDispSelectTag;
			}
			//filter等、各Formatter用に、独自に設定されていなかった場合（通常の場合）----
		}else{
			if(is_array($arrayDispSelectTag)===true){
				//----filterテーブル用に、各個開発者が独自に設定した場合を最優先に
				$arraySelectElement=$arrayDispSelectTag;
				//filterテーブル用に、各個開発者が独自に設定した場合を最優先に----
			}else{
				//----まず、ここを通ることは、考えられない
				$arraySelectElement=array();
				//まず、ここを通ることは、考えられない----
			}
		}
		$body  = "";
		
		if( $arraySelectElement === null ){
			//$body .= "テーブルまたはカラムが存在しません。";
			$body = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-12201");
		}else{
			if( array_key_exists("", $arraySelectElement) === true ){
				//$body = "マスター(テーブル)の、被参照キー列に、空白が含まれています。";
				$body = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-12202");
			}else{
				
				$selected = $this->getSettingDataBeforeEdit($arraySelectElement,$rowData,$aryVariant);
				
				//$strBlankBody = "空白";
				$strBlankBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-651");
				$strOptionBody = makeSelectOption($arraySelectElement, $selected, true, "{".$strBlankBody."}", true);
				
				$body .= "<select {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} >\n";
				$body .= $strOptionBody;
				$body .= "</select>";
				
			}
		}
		return $this->getTag($body, $rowData);
	}

}

class DisuseSelectFilterTabBFmt extends TextFilterTabBFmt {

	public function getData($rowData,$aryVariant){
		global $g;
		$aryAddOnDefault = array();
		$aryOverWrite = array();

		$strColId = $this->getPrintTargetKey();
		$strColSynonym = $this->getIDSynonym();

		if( $rowData === null ){
			$data = $this->getDefaultValue(false,false,$rowData,$aryVariant);
			if( $data === null ){
				$data = 0;
			}
		}else{
			$data = array_key_exists($strColId, $rowData)?$rowData[$strColId]:0;
		}
		//
		$tagIdentify = $this->getFSTIDForIdentify("","");

		$aryOverWrite["id"] = $tagIdentify;
		$aryOverWrite["name"] = $strColSynonym;

		$body = "<select {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)}>\n";
		//(array(""=>"全レコード","0"=>"廃止含まず","1"=>"廃止のみ")
		$aryListData = array(
			""=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-661"),
			"0"=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-662"),
			"1"=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-663")
		);
		$body .= makeSelectOption($aryListData, $data, false, "", true);
		unset($aryListData);
		$body .="</select>";
		return $this->getTag($body, $rowData);
	}

}
//フィルター入力系----

class ExcelBFmt extends BFmt {

	public function getData($rowData,$aryVariant){
		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い
		return $data;
	}

}

class ExcelSelectBFmt extends ExcelBFmt {

	public function getData($rowData,$aryVariant){
		//----IDOutputTypeからくること前提
		$retStrData = "";
		$strColId = $this->getPrintTargetKey();
		list($rawData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','rawValue'),null);
		if( $tmpBoolKeyExist===false ){
			//----生値の鍵がなかった
			$schData = $this->getDefaultValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどEXCELなので）
			//生値の鍵がなかった----
		}else{
			//----生値の鍵はあった
			$tmpDataOver = $this->getOverrideValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどEXCELなので）
			if( $tmpDataOver!==null ){
				//----上書きする値が設定されていた
				$schData = $tmpDataOver;
				//上書きする値が設定されていた----
			}else{
				$schData = null;
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
			}
			//生値の鍵はあった----
		}
		if( $schData!==null ){
			list($aryListElement,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','convIDList'),null);
			if( $aryListElement!==null ){
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryListElement,array((string)$schData),"");
			}
		}
		return $retStrData;
	}

}

class CSVBFmt extends BFmt {
	protected $strOutputPrintType;
	protected $strColumnSepa;

	public function __construct(){
		parent::__construct();
		$this->strOutputPrintType = "";
		$this->ColumnSepa = ",";
	}

	public function getData($rowData,$aryVariant){
		$strRetData = "";
		$strColId = $this->getPrintTargetKey();
		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い
		if( $this->strOutputPrintType == "noWrapData" ){
			$strRetData = $data;
		}else if( $this->strOutputPrintType == "wrapData" ){
			$strRetData = '"'.$data.'"'.$this->ColumnSepa;
		}
		return $strRetData;
	}

	public function setOutputPrintType($strType, $strColSepa=""){
		$this->strOutputPrintType = $strType;
		$this->ColumnSepa = $strColSepa;
	}

	public function getOutputPrintType(){
		return $this->strOutputPrintType;
	}

}

class CSVSelectBFmt extends CSVBFmt {

	public function getData($rowData,$aryVariant){
		//----IDOutputTypeからくること前提
		$retStrData = "";
		$strColId = $this->getPrintTargetKey();
		list($rawData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','rawValue'),null);
		if( $tmpBoolKeyExist===false ){
			//----生値の鍵がなかった
			$schData = $this->getDefaultValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどCSVなので）
			//生値の鍵がなかった----
		}else{
			//----生値の鍵はあった
			$tmpDataOver = $this->getOverrideValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどCSVなので）
			if( $tmpDataOver!==null ){
				//----上書きする値が設定されていた
				$schData = $tmpDataOver;
				//上書きする値が設定されていた----
			}else{
				$schData = null;
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
			}
			//生値の鍵はあった----
		}
		if( $schData!==null ){
			list($aryListElement,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','convIDList'),null);
			if( $aryListElement!==null ){
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryListElement,array((string)$schData),"");
			}
		}
		return $retStrData;
	}

}

class StaticCSVBFmt extends CSVBFmt {

	private $text; // as string 固定で表示させる文字（デフォルトとして表示される文字）
	private $boolShowWhenNotNull;
	private $strShowOtherColumnId;

	public function __construct($text="",$boolShowWhenNotNull=false,$strShowOtherColumnId=null){
		parent::__construct();
		$this->text = $text;
		$this->boolShowWhenNotNull  = $boolShowWhenNotNull;
		$this->strShowOtherColumnId = $strShowOtherColumnId;
	}

	public function getData($rowData,$aryVariant){
		$strSetValue = $this->text;
		if( $this->boolShowWhenNotNull === true ){
			if( $rowData === null ){
			}else{
				if( $this->strShowOtherColumnId === null ){
					$strColId = $this->getPrintTargetKey();
				}else{
					$strColId = $this->strShowOtherColumnId;
				}
				list($strTempValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
				if( $strTempValue != "" ){
					$strSetValue = $strTempValue;
				}
			}
		}
		if( $this->strOutputPrintType == "noWrapData" ){
			$strRetData = $strSetValue;
		}else if( $this->strOutputPrintType == "wrapData" ){
			$strRetData = '"'.$strSetValue.'"'.$this->ColumnSepa;
		}
		return $strRetData;
	}


	public function setText($text){
		$this->text = $text;
	}

}

class JSONBFmt extends BFmt {

	public function getData($rowData,$aryVariant){
		$data = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い
		return $data;
	}

}

class JSONSelectBFmt extends JSONBFmt {

	public function getData($rowData,$aryVariant){
		//----IDOutputTypeからくること前提
		$retStrData = "";
		$strColId = $this->getPrintTargetKey();
		list($rawData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','rawValue'),null);
		if( $tmpBoolKeyExist===false ){
			//----生値の鍵がなかった
			$schData = $this->getDefaultValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどREST(-GATE)-APIなので）
			//生値の鍵がなかった----
		}else{
			//----生値の鍵はあった
			$tmpDataOver = $this->getOverrideValue(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い(SELECTだけどREST(-GATE)-APIなので）
			if( $tmpDataOver!==null ){
				//----上書きする値が設定されていた
				$schData = $tmpDataOver;
				//上書きする値が設定されていた----
			}else{
				$schData = null;
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
			}
			//生値の鍵はあった----
		}
		if( $schData!==null ){
			list($aryListElement,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array('callerVars','free','convIDList'),null);
			if( $aryListElement!==null ){
				list($retStrData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryListElement,array((string)$schData),"");
			}
		}
		return $retStrData;
	}

}

class FileUploadCSVBFmt extends CSVBFmt {
	protected $strAnchorHrefAgent;
	protected $strLAPathOfAnchorHrefTargetAgent;

	protected $boolFileHideMode;

	public function __construct(){
		parent::__construct();
		$this->setAnchorHrefAgent(null);
		$this->setLAPathOfAnchorHrefTargetAgent(null);

		$this->setFileHideModeAgent(null);
	}

	//----他のクラスから独立して利用するための関数
	public function setAnchorHrefAgent($strAnchorHrefAgent){
		$this->strAnchorHrefAgent = $strAnchorHrefAgent;
	}
	public function setLAPathOfAnchorHrefTargetAgent($strLAPathOfAnchorHrefTargetAgent){
		$this->strLAPathOfAnchorHrefTargetAgent = $strLAPathOfAnchorHrefTargetAgent;
	}
	public function setFileHideModeAgent($boolFileHideMode){
		$this->boolFileHideMode = $boolFileHideMode;
	}
	//他のクラスから独立して利用するための関数----

	public function getAnchorHref($rowData){
		$retStrAnchorHref = $this->strAnchorHrefAgent;
		if( is_a($this->objColumn, "Column") === true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getOAPathToFUCItemPerRow') === true ){
				$retStrAnchorHref = $this->objColumn->getOAPathToFUCItemPerRow($rowData);
			}
			//カラムがセットされている場合----
		}
		return $retStrAnchorHref;
	}

	public function getLAPathOfAnchorHrefTarget($rowData){
		$retStrLAPathOfAnchorHrefTarget = $this->strLAPathOfAnchorHrefTargetAgent;
		if( is_a($this->objColumn, "Column") === true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getLAPathToFUCItemPerRow') === true ){
				$retStrLAPathOfAnchorHrefTarget = $this->objColumn->getLAPathToFUCItemPerRow($rowData);
			}
			//カラムがセットされている場合----
		}
		return $retStrLAPathOfAnchorHrefTarget;
	}

	public function getFileHideMode(){
		$retBoolFileHideMode = $this->boolFileHideMode;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getFileHideMode') === true ){
				$retBoolFileHideMode = $this->objColumn->getFileHideMode();
			}
			//カラムがセットされている場合----
		}
		return $retBoolFileHideMode;
	}

	public function getData($rowData,$aryVariant){
		$strRetData = "";
		$strFileUrl="";
		$strColId = $this->getPrintTargetKey();

		list($strFileUrl,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strColId),"");

		if( $strFileUrl != "" ){
			//----鍵があり、値が空文字と等しくない
			if( $this->getFileHideMode() === false ){
				//----ファイル隠蔽モードではない
				$localPath = $this->getLAPathOfAnchorHrefTarget($rowData);
				if( file_exists($localPath) === true ){
					$strFileUrl = $this->getAnchorHref($rowData);
				}else{
					$strFileUrl  = "";
				}
				//ファイル隠蔽モードではない----
			}else{
				//----コメント化
				//$strFileUrl = $rowData[$strColId];
				//コメント化----
			}
			//鍵があり、値が空文字と等しくない----
		}

		if( $this->strOutputPrintType == "noWrapData" ){
			$strRetData = $strFileUrl;
		}else if( $this->strOutputPrintType == "wrapData" ){
			$strRetData = '"'.$strFileUrl.'"'.$this->ColumnSepa;
		}
		return $strRetData;
	}

}

//----CSVBFmtの継承ではないので注意！
class DelCSVBFmt extends DelBFmt {
	protected $strOutputPrintType;
	protected $strColumnSepa;

	public function __construct(){
		parent::__construct();
		$this->strOutputPrintType = "";
		$this->ColumnSepa = ",";
	}

	public function getData($rowData,$aryVariant){
		$strRetData = "";
		$data = parent::getData($rowData,$aryVariant);
		if( $this->strOutputPrintType == "noWrapData" ){
			$strRetData = $data;
		}else if( $this->strOutputPrintType == "wrapData" ){
			$strRetData = '"'.$data.'"'.$this->ColumnSepa;
		}
		return $strRetData;
	}


	public function setOutputPrintType($strType, $strColSepa=""){
		$this->strOutputPrintType = $strType;
		$this->ColumnSepa = $strColSepa;
	}

	public function getOutputPrintType(){
		return $this->strOutputPrintType;
	}


}
//CSVBFmtの継承ではないので注意！----

class HostInsideLinkTextTabBFmt extends TextTabBFmt {
	protected $strOriginAgent;

	protected $objFunctionForGetContentInTag; 

	public function __construct(){
		parent::__construct();

		$this->setOriginAgent(null);
		$this->setFunctionForGetContentInTag(null);
	}

	//----他のクラスから独立して利用するための関数
	public function setOriginAgent($strOriginAgent){
		$this->strOriginAgent = $strOriginAgent;
	}
	//他のクラスから独立して利用するための関数----

	public function setFunctionForGetContentInTag($objFunctionForGetContentInTag){
		$this->objFunctionForGetContentInTag = $objFunctionForGetContentInTag;
	}

	public function getOrigin(){
		$retStrOrigin = $this->strOriginAgent;
		if( is_a($this->objColumn, "Column") === true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getOrigin') === true ){
				$retStrOrigin = $this->objColumn->getOrigin();
			}
			//カラムがセットされている場合----
		}
		return $retStrOrigin;
	}

	public function getFunctionForGetContentInTag(){
		return $this->objFunctionForGetContentInTag;
	}

	public function GetContentInTag($rowData,$aryVariant){
		$strTextInTag = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";

		$objFunction = $this->objFunctionForGetContentInTag;
		if( is_callable($objFunction) === true ){
			$aryRetBody = $objFunction($this, $rowData, $aryVariant);
		}else{
			$strColId = $this->getPrintTargetKey();

			list($strUrlBody,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array($strColId),"");
			$strTextInTag = $strUrlBody;
			if( $this->checkListFormatterMode("CurrentTableFormatter") === true ){
				if( 1 <= strlen($strUrlBody) ){
					list($strRepresentiveNumeric,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array('REPRESENTATIVE_FLAG'),"");
					if( $tmpBoolKeyExist01===false ){
						$strRepresentiveNumeric = "1";
					}
					if( $strRepresentiveNumeric == "1" ){
						$strOrigin = $this->getOrigin();
						$strTextInTag = "<a href=\"{$strOrigin}{$strUrlBody}\" target=\"blank\">{$strUrlBody}</a>";
					}
				}
			}
			$aryRetBody = array($strTextInTag, $intErrorType, $aryErrMsgBody, $strErrMsg);
		}
		return $aryRetBody;
	}

	public function getData($rowData,$aryVariant){
		global $g;

		$strBodyInCell = "";

		$aryRetBody = $this->GetContentInTag($rowData,$aryVariant);

		if( $aryRetBody[1]===null ){
			$strBodyInCell = $aryRetBody[0];
		}

		return $this->getTag($strBodyInCell, $rowData);
	}
}

class LockTargetInputTabBFmt extends InputTabBFmt {

    public function getData($rowData,$aryVariant){
        global $g;
        $aryAddOnDefault = array();
        $aryOverWrite = array();

        $strBodyInCell = "";
        $strVisibleText = "";
        $strHiddenText = "";

        $strData = $this->getSettingDataBeforeEdit(false,true,$rowData,$aryVariant); //----設定値が配列の場合はnull扱い
        if( 0 < strlen($strData) ){
            $strVisibleText = $this->makeSafeValueForBrowse($strData);
        }else{
            //$body = "自動採番";
            $strVisibleText = $g['objMTS']->getSomeMessage("ITAWDCH-STD-671");
        }
        $strBodyInCell .= $strVisibleText;

        //----送信する値は隠しもっておく。
        //送信する値は隠しもっておく。----

        $aryOverWrite["type"] = "hidden";
        $aryOverWrite["id"] = $this->getFSTIDForIdentify();
        $aryOverWrite["name"] = $this->getFSTNameForIdentify();
        $aryOverWrite["value"] = $strData;

        $strHiddenText = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} {$this->getTextTagLastAttr()}>";

        $strBodyInCell .= $strHiddenText;

        return $this->getTag($strBodyInCell, $rowData);
    }
}

//----レヴューページテンプレート用の各種タブ
class ReviewTemplateTableTabBFmt extends TabBFmt {
	protected $strPageTypeAgent;

	protected $strEditUserColumnIdAgent;
	protected $strCheckDisuseColumnIdAgent;
	protected $strEditStatusColumnIdAgent;

	protected $strActionNameOfApplyRegistrationForNewAgent;

	protected $strActionNameOfApplyRegistrationForUpdateAgent;

	protected $strActionNameOfApplyUpdateAgent;
	protected $strActionNameOfApplyExecuteAgent;
	protected $strActionNameOfApplyEditRestartAgent;
	protected $strActionNameOfApplyWithdrawnAgent;

	protected $strActionNameOfConfirmUpdateAgent;
	protected $strActionNameOfConfirmReturnAgent;
	protected $strActionNameOfConfirmAcceptAgent;
	protected $strActionNameOfConfirmNonsuitAgent;

	protected $strActionNameOfLogicDeleteOnAgent;
	protected $strActionNameOfLogicDeleteOffAgent;

	public function __construct(){
		parent::__construct();

		$this->setPageTypeAgent(null);

		$this->setEditUserColumnIDAgent(null);
		$this->setCheckDisuseColumnIDAgent(null);
		$this->setEditStatusColumnIDAgent(null);

		$this->setActionNameOfApplyRegistrationForNewAgent(null);

		$this->setActionNameOfApplyRegistrationForUpdateAgent(null);

		$this->setActionNameOfApplyUpdateAgent(null);
		$this->setActionNameOfApplyExecuteAgent(null);
		$this->setActionNameOfApplyEditRestartAgent(null);
		$this->setActionNameOfApplyWithdrawnAgent(null);

		$this->setActionNameOfConfirmUpdateAgent(null);
		$this->setActionNameOfConfirmReturnAgent(null);
		$this->setActionNameOfConfirmAcceptAgent(null);
		$this->setActionNameOfConfirmNonsuitAgent(null);

		$this->setActionNameOfLogicDeleteOnAgent(null);
		$this->setActionNameOfLogicDeleteOffAgent(null);
	}

	//----他のクラスから独立して利用するための関数
	
	//----テーブル系
	public function setPageTypeAgent($strPageTypeAgent){
		$this->strPageTypeAgent = $strPageTypeAgent;
	}

	public function setActionNameOfApplyRegistrationForNewAgent($strActionNameOfApplyRegistrationForNewAgent){
		$this->strActionNameOfApplyRegistrationForNewAgent = $strActionNameOfApplyRegistrationForNewAgent;
	}
	public function setActionNameOfApplyRegistrationForUpdateAgent($strActionNameOfApplyRegistrationForUpdateAgent){
		$this->strActionNameOfApplyRegistrationForUpdateAgent = $strActionNameOfApplyRegistrationForUpdateAgent;
	}
	public function setActionNameOfApplyUpdateAgent($strActionNameOfApplyUpdateAgent){
		$this->strActionNameOfApplyUpdateAgent = $strActionNameOfApplyUpdateAgent;
	}
	public function setActionNameOfApplyExecuteAgent($strActionNameOfApplyExecuteAgent){
		$this->strActionNameOfApplyExecuteAgent = $strActionNameOfApplyExecuteAgent;
	}
	public function setActionNameOfApplyEditRestartAgent($strActionNameOfApplyEditRestartAgent){
		$this->strActionNameOfApplyEditRestartAgent = $strActionNameOfApplyEditRestartAgent;
	}
	public function setActionNameOfApplyWithdrawnAgent($strActionNameOfApplyWithdrawnAgent){
		$this->strActionNameOfApplyWithdrawnAgent = $strActionNameOfApplyWithdrawnAgent;
	}
	public function setActionNameOfConfirmUpdateAgent($strActionNameOfConfirmUpdateAgent){
		$this->strActionNameOfConfirmUpdateAgent = $strActionNameOfConfirmUpdateAgent;
	}
	public function setActionNameOfConfirmReturnAgent($strActionNameOfConfirmReturnAgent){
		$this->strActionNameOfConfirmReturnAgent = $strActionNameOfConfirmReturnAgent;
	}
	public function setActionNameOfConfirmAcceptAgent($strActionNameOfConfirmAcceptAgent){
		$this->strActionNameOfConfirmAcceptAgent = $strActionNameOfConfirmAcceptAgent;
	}
	public function setActionNameOfConfirmNonsuitAgent($strActionNameOfConfirmNonsuitAgent){
		$this->strActionNameOfConfirmNonsuitAgent = $strActionNameOfConfirmNonsuitAgent;
	}
	public function setActionNameOfLogicDeleteOnAgent($strActionNameOfLogicDeleteOnAgent){
		$this->strActionNameOfLogicDeleteOnAgent = $strActionNameOfLogicDeleteOnAgent;
	}
	public function setActionNameOfLogicDeleteOffAgent($strActionNameOfLogicDeleteOffAgent){
		$this->strActionNameOfLogicDeleteOffAgent = $strActionNameOfLogicDeleteOffAgent;
	}
	//テーブル系----

	//----カラム系
	public function setEditUserColumnIDAgent($strEditUserColumnIdAgent){
		$this->strEditUserColumnIdAgent = $strEditUserColumnIdAgent;
	}
	public function setCheckDisuseColumnIDAgent($strCheckDisuseColumnIDAgent){
		$this->strCheckDisuseColumnIDAgent = $strCheckDisuseColumnIDAgent;
	}
	public function setEditStatusColumnIDAgent($strEditStatusColumnIDAgent){
		$this->strEditStatusColumnIDAgent = $strEditStatusColumnIDAgent;
	}
	//カラム系----

	//----テーブル系
	public function getPageType(){
		$retStrPageType = $this->strPageTypeAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getPageType') === true ){
				$retStrPageType = $objTable->getPageType();
			}
			//カラムがセットされている場合----
		}
		return $retStrPageType;
	}
	public function getActionNameOfApplyRegistrationForNew(){
		$retStrActionNameOfApplyRegistrationForNew = $this->strActionNameOfApplyRegistrationForNewAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyRegistrationForNew') === true ){
				$retStrPageType = $objTable->getActionNameOfApplyRegistrationForNew();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyRegistrationForNew;
	}
	
	public function getActionNameOfApplyRegistrationForUpdate(){
		$retStrActionNameOfApplyRegistrationForUpdate = $this->strActionNameOfApplyRegistrationForUpdateAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyRegistrationForUpdate') === true ){
				$retStrActionNameOfApplyRegistrationForUpdate = $objTable->getActionNameOfApplyRegistrationForUpdate();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyRegistrationForUpdate;
	}
	public function getActionNameOfApplyUpdate(){
		$retStrActionNameOfApplyUpdate = $this->strActionNameOfApplyUpdateAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyUpdate') === true ){
				$retStrActionNameOfApplyUpdate = $objTable->getActionNameOfApplyUpdate();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyUpdate;
	}
	public function getActionNameOfApplyExecute(){
		$retStrActionNameOfApplyExecute = $this->strActionNameOfApplyExecuteAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyExecute') === true ){
				$retStrActionNameOfApplyExecute = $objTable->getActionNameOfApplyExecute();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyExecute;
	}
	public function getActionNameOfApplyEditRestart(){
		$retStrActionNameOfApplyEditRestart = $this->strActionNameOfApplyEditRestartAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyEditRestart') === true ){
				$retStrActionNameOfApplyEditRestart = $objTable->getActionNameOfApplyEditRestart();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyEditRestart;
	}
	public function getActionNameOfApplyWithdrawn(){
		$retStrActionNameOfApplyWithdrawn = $this->strActionNameOfApplyWithdrawnAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfApplyWithdrawn') === true ){
				$retStrActionNameOfApplyWithdrawn = $objTable->getActionNameOfApplyWithdrawn();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfApplyWithdrawn;
	}
	public function getActionNameOfConfirmUpdate(){
		$retStrActionNameOfConfirmUpdate = $this->strActionNameOfConfirmUpdateAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfConfirmUpdate') === true ){
				$retStrActionNameOfConfirmUpdate = $objTable->getActionNameOfConfirmUpdate();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfConfirmUpdate;
	}
	public function getActionNameOfConfirmReturn(){
		$retStrActionNameOfConfirmReturn = $this->strActionNameOfConfirmReturnAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfConfirmReturn') === true ){
				$retStrActionNameOfConfirmReturn = $objTable->getActionNameOfConfirmReturn();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfConfirmReturn;
	}
	public function getActionNameOfConfirmAccept(){
		$retStrActionNameOfConfirmAccept = $this->strActionNameOfConfirmAcceptAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfConfirmAccept') === true ){
				$retStrActionNameOfConfirmAccept = $objTable->getActionNameOfConfirmAccept();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfConfirmAccept;
	}
	public function getActionNameOfConfirmNonsuit(){
		$retStrActionNameOfConfirmNonsuit = $this->strActionNameOfConfirmNonsuitAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfConfirmNonsuit') === true ){
				$retStrActionNameOfConfirmNonsuit = $objTable->getActionNameOfConfirmNonsuit();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfConfirmNonsuit;
	}
	public function getActionNameOfLogicDeleteOn(){
		$retStrActionNameOfLogicDeleteOn = $this->strActionNameOfLogicDeleteOnAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfLogicDeleteOn') === true ){
				$retStrActionNameOfLogicDeleteOn = $objTable->getActionNameOfLogicDeleteOn();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfLogicDeleteOn;
	}
	public function getActionNameOfLogicDeleteOff(){
		$retStrActionNameOfLogicDeleteOff = $this->strActionNameOfLogicDeleteOffAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			$objTable = $this->objColumn->getTable();
			if( method_exists($objTable,'getActionNameOfLogicDeleteOff') === true ){
				$retStrActionNameOfLogicDeleteOff = $objTable->getActionNameOfLogicDeleteOff();
			}
			//カラムがセットされている場合----
		}
		return $retStrActionNameOfLogicDeleteOff;
	}
	//テーブル系----
	
	//----カラム系
	public function getEditUserColumnID(){
		$retStrEditUserColumnId = $this->strEditUserColumnIdAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getEditUserColumnID') === true ){
				$retStrEditUserColumnId = $this->objColumn->getEditUserColumnID();
			}
			//----カラムがセットされている場合
		}
		return $retStrEditUserColumnId;
	}
	public function getCheckDisuseColumnID(){
		$retStrCheckDisuseColumnId = $this->strCheckDisuseColumnIdAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getCheckDisuseColumnID') === true ){
				$retStrCheckDisuseColumnId = $this->objColumn->getCheckDisuseColumnID();
			}
			//----カラムがセットされている場合
		}
		return $retStrCheckDisuseColumnId;
	}
	public function getEditStatusColumnID(){
		$retStrEditStatusColumnId = $this->strEditStatusColumnIdAgent;
		if( is_a($this->objColumn, "Column")===true ){
			//----カラムがセットされている場合
			if( method_exists($this->objColumn,'getEditStatusColumnID') === true ){
				$retStrEditStatusColumnId = $this->objColumn->getEditStatusColumnID();
			}
			//----カラムがセットされている場合
		}
		return $retStrEditStatusColumnId;
	}
	//カラム系----

	//他のクラスから独立して利用するための関数----
}

//----レヴューページテンプレート用の更新ボタン
class EditLockUpdButtonTabBFmt extends ReviewTemplateTableTabBFmt {

    public function getData($rowData,$aryVariant){
        global $g;
        $intControlDebugLevel01=250;
        $intControlDebugLevel02=250;

        $aryAddOnDefault = array();
        $aryOverWrite = array();

        $strBodyInCell = "";
        $strUpdable = "";

        $strDefaultButtonFace = $this->getColLabel();

        //$updParam = 1; // 利用されていない
        $strRIColId = $this->getRIColumnKey();
        
        $strApplyUserColId = $this->getEditUserColumnID();
        $strDisuseFlagColId = $this->getCheckDisuseColumnID();
        $strEditStatusColId = $this->getEditStatusColumnID();
        
        $strPageType = $this->getPageType();
        
        list($strValueOfDisuseFlag,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array($strDisuseFlagColId),"");
        
        if( 0 < strlen($strValueOfDisuseFlag) ){
            //----レコードに1バイト以上の値が入っている場合
            if( $strValueOfDisuseFlag === "0" ){
                //----活性中の場合
                
                // 編集ステータスの値を取得
                list($strValueOfEditStatusNumeric,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array($strEditStatusColId),"");
                
                if( $strValueOfEditStatusNumeric == "1" ){
                    //----編集中の場合
                    
                    $strUpdable="disabled";
                    
                    if( $strPageType == "apply" ){
                        
                        list($strValueOfApplyUserNumeric,$tmpBoolKeyExist02)=isSetInArrayNestThenAssign($rowData,array($strApplyUserColId),"");
                        
                        if( $strValueOfApplyUserNumeric === $g['login_id'] ){
                            //----申請者IDと、ログインしているユーザのIDが等しい
                            
                            $strUpdable="";
                            
                            //申請者IDと、ログインしているユーザのIDが等しい----
                        }else{
                            //----申請者IDと、ログインしているユーザのIDが等しくない
                            
                            $strUpdable="disabled";
                            
                            //申請者IDと、ログインしているユーザのIDが等しくない----
                        }
                    }
                    
                    //編集中の場合----
                }else if( $strValueOfEditStatusNumeric == "2" ){
                    //----申請中の場合
                    
                    $strUpdable="disabled";
                    
                    if( $strPageType == "confirm" ){
                        
                        $strUpdable="";
                        
                    }
                    
                    //申請中の場合----
                }else{
                    //----編集中ではない場合
                    
                    $strUpdable="disabled";
                    
                    if( $strPageType == "view" ){
                        $strDefaultButtonFace = $this->getActionNameOfApplyRegistrationForUpdate();
                        $strUpdable="";
                    }
                    
                    //編集中ではない場合----
                }
                //活性中の場合----
            }else{
                //----活性中ではない場合
                if( $strValueOfDisuseFlag === "1" ){
                    //----廃止されている場合
                    
                    $strUpdable="disabled";
                    
                    //廃止されている場合----
                }else{
                    //----そのほかのステータスの場合
                    
                    $strUpdable="disabled";
                    
                    //そのほかのステータスの場合----
                }
                //活性中ではない場合----
            }
            if( $strPageType == "apply" ){
                $strDefaultButtonFace = $this->getActionNameOfApplyUpdate();
            }else if( $strPageType == "confirm" ){
                $strDefaultButtonFace = $this->getActionNameOfConfirmUpdate();
            }else{
                $strDefaultButtonFace = $this->getActionNameOfApplyRegistrationForUpdate();
            }            
            //レコードに1バイト以上の値が入っている場合----
        }

        $aryAddOnDefault["class"] = "updateBtnInTbl";

        $aryOverWrite["type"] = "button";
        $aryOverWrite["value"] = $strDefaultButtonFace;

        $strBodyInCell = "<input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData)} $strUpdable />";
        return $this->getTag($strBodyInCell, $rowData);
    }
}
//レヴューページテンプレート用の更新ボタン----

//----レヴューページテンプレート用の編集コマンドボタン
class EditStatusControlBtnTabBFmt extends ReviewTemplateTableTabBFmt {
    public function getData($rowData,$aryVariant){
        global $g;
        
        $intControlDebugLevel01=250;
        $intControlDebugLevel02=250;

        $aryAddOnDefault = array();
        $aryOverWrite = array();
        $aryJsEventOverWrite = array();
        
        $strBodyInCell = "";
        
        $strRIColId = $this->getRIColumnKey();
        $strApplyUserColId = $this->getEditUserColumnID();
        $strDisuseFlagColId = $this->getCheckDisuseColumnID();
        $strEditStatusColId = $this->getEditStatusColumnID();
        
        $strPageType = $this->getPageType();
        
        list($strValueOfDisuseFlag,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array($strDisuseFlagColId),"");
        
        if( $strValueOfDisuseFlag === "0" ){
            //----活性中の場合
            
            list($strRIValueNumeric       ,$tmpBoolKeyExist02)=isSetInArrayNestThenAssign($rowData,array($strRIColId),"");
            list($strValueForChekTimeStamp,$tmpBoolKeyExist03)=isSetInArrayNestThenAssign($rowData,array($this->getRequiredUpdateDate4UColumnID()),"");            
            
            if( $tmpBoolKeyExist02 === true && $tmpBoolKeyExist03 === true ){
                
                // 編集ステータスの値を取得
                list($strValueOfEditStatusNumeric,$tmpBoolKeyExist01)=isSetInArrayNestThenAssign($rowData,array($strEditStatusColId),"");
                
                if( $strPageType == "apply" ){
                    //----申請者ページの場合
                    
                    list($strValueOfApplyUserNumeric,$tmpBoolKeyExist02)=isSetInArrayNestThenAssign($rowData,array($strApplyUserColId),"");
                    
                    if( $strValueOfApplyUserNumeric === $g['login_id'] ){
                        //----閲覧者と編集者が同じである
                        
                        if( $strValueOfEditStatusNumeric == "1" ){
                            //----編集ステータスが、編集中である場合

                            $aryAddOnDefault["class"] = "updateBtnInTbl";
                            $aryOverWrite["type"] = "button";

                            $strButtonFace = $this->getActionNameOfApplyWithdrawn();
                            $aryOverWrite["value"] = $strButtonFace;

                            $aryJsEventOverWrite["onClick"] = "pre_apply_async('{$strRIValueNumeric}',0,'{$strValueForChekTimeStamp}');";
                            $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";

                            $strBodyInCell .= "　";
                            $strButtonFace = $this->getActionNameOfApplyExecute();
                            $aryOverWrite["value"] = $strButtonFace;

                            $aryJsEventOverWrite["onClick"] = "pre_apply_async('{$strRIValueNumeric}',2,'{$strValueForChekTimeStamp}');";
                            $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                            unset($aryJsEventOverWrite["onClick"]);

                            //編集ステータスが、編集中である場合----
                        }else if( $strValueOfEditStatusNumeric == "2" ){
                            //----編集ステータスが、申請中である場合

                            $aryAddOnDefault["class"] = "updateBtnInTbl";
                            $aryOverWrite["type"] = "button";

                            $strButtonFace = $this->getActionNameOfApplyWithdrawn();
                            $aryOverWrite["value"] = $strButtonFace;  //$this->objOutputType->setAttr('value', $strButtonFace);

                            $aryJsEventOverWrite["onClick"] = "pre_apply_async('{$strRIValueNumeric}',0,'{$strValueForChekTimeStamp}');";
                            $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                            
                            $strBodyInCell .= "　";
                            $strButtonFace = $this->getActionNameOfApplyEditRestart();
                            $aryOverWrite["value"] = $strButtonFace;

                            $aryJsEventOverWrite["onClick"] = "pre_apply_async('{$strRIValueNumeric}',1,'{$strValueForChekTimeStamp}');";
                            $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                            unset($aryJsEventOverWrite["onClick"]);

                            //編集ステータスが、申請中である場合----
                        }
                        //
                        //閲覧者と編集者が同じである場合----
                    }
                    //申請者ページの場合----
                }else if( $strPageType == "confirm" ){
                    //----承認者ページの場合
                    //
                    $aryAddOnDefault["class"] = "updateBtnInTbl";
                    $aryOverWrite["type"] = "button";
                    //
                    if( $strValueOfEditStatusNumeric == "2" ){
                        //----編集ステータスが、申請中である場合

                        $aryAddOnDefault["class"] = "updateBtnInTbl";
                        $aryOverWrite["type"] = "button";

                        $strButtonFace = $this->getActionNameOfConfirmReturn();
                        $aryOverWrite["value"] = $strButtonFace;

                        $aryJsEventOverWrite["onClick"] = "pre_confirm_async('{$strRIValueNumeric}',1,'{$strValueForChekTimeStamp}');";
                        $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                        $strBodyInCell .= "　";

                        $strButtonFace = $this->getActionNameOfConfirmAccept();
                        $aryOverWrite["value"] = $strButtonFace;

                        $aryJsEventOverWrite["onClick"] = "pre_confirm_async('{$strRIValueNumeric}',3,'{$strValueForChekTimeStamp}');";
                        $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                        $strBodyInCell .= "　";

                        $strButtonFace = $this->getActionNameOfConfirmNonsuit();
                        $aryOverWrite["value"] = $strButtonFace;

                        $aryJsEventOverWrite["onClick"] = "pre_confirm_async('{$strRIValueNumeric}',4,'{$strValueForChekTimeStamp}');";
                        $strBodyInCell .= "<p><input {$this->printAttrs($aryAddOnDefault,$aryOverWrite)} {$this->printJsAttrs($rowData,$aryJsEventOverWrite)} /></p>";
                        unset($aryJsEventOverWrite["onClick"]);
                        //申請中の場合----

                        //編集ステータスが、申請中である場合----
                    }
                    //承認者ページの場合----
                }
            }
            //活性中の場合----
        }
        return $this->getTag($strBodyInCell, $rowData);
    }
}
//レヴューページテンプレート用の編集コマンドボタン----
//レヴューページテンプレート用の各種タブ----
//ここまでBFmt系----
?>
