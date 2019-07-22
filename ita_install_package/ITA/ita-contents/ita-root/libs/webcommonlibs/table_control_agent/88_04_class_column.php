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

//  【特記事項】
//    ・ModuleDistictCode(903)

//  【処理概要】
//    ・DBでの実在を問わず、アプリ上はカラムと呼んで扱うノードについて、各種情報を格納/出力する

//////////////////////////////////////////////////////////////////////

class ColumnGroup {
	//Columnのグルーピング。エッジはColumn

	//----参照
	protected $objTable; // as Table
	//参照----

	protected $fixedNo;
	protected $hColCount; // as int カラムグループ内の列数
	protected $hRowCount; // as int カラムグループ内の行数
	protected $aryHRowNo;  // as int ツリー全体からみたときの行番号
	protected $aryColNo;   // as int ツリー全体からみたときの列番号(エッジにだけつく)
	protected $parent;    // as ColumnGroupの親;
	protected $children;  // as array カラムグループの子の配列

	protected $strColGrpLabel;  // as string カラムグループの表示名（旧hName）
	protected $boolRoot;

	protected $aryRowSpanLength;
	protected $aryColSpanLength;
	protected $intStaticRowLevel;

	//----ここから継承メソッドの上書き処理
	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function __construct($strColGrpLabel, $boolRootValue=false){
		$this->objTable = null;
		$this->intCgNo = null;
		$this->strColGrpLabel = $strColGrpLabel;
		$this->hWidth = 0;
		$this->hHeight = 0;
		$this->children = array();
		$this->aryHRowNo = array();
		$this->aryColNo = array();
		$this->parent = null;
		$this->boolRoot = $boolRootValue;

		$this->intStaticRowLevel = null;
		$this->aryRowSpanLength = array();
		$this->aryColSpanLength = array();
	}

	//NEW[2]
	public function initTable(TableControlAgent $objTable, $intDummy){
		$intCgNo = $objTable->getCgNo();
		$this->objTable = $objTable;
		$this->intCgNo = $intCgNo;
		if( $this->children === null || count($this->children) === 0 ){
		}else{
			foreach($this->children as $child){
				if( is_a($this,"Column") === false ){
					//----カラムグループの場合のみ、再帰起動
					$child->initTable($objTable, null);
					//カラムグループの場合のみ、再帰起動----
				}
			}
		}	
	}

	//NEW[3]
	public function getSelfInfoForLog(){
		$retStrBody="";
		if($this->objTable===null){
			$retStrBody='[TABLE]Uninited';
		}else{
			$temoAryBody = $this->objTable->getInitInfo();
			$retStrBody='[TABLE]Inited ([FILE]'.$temoAryBody[0].',[LINE]'.$temoAryBody[1].',';
			if( is_a($this, "Column") ){
				$retStrBody.='[ColumnNo]'.$this->getColumnSeqNo().')';
			}else{
				$retStrBody.='[GroupNo]'.$this->getColGrpSeqNo().')';
			}
		}
		return $retStrBody;
	}

	//NEW[4]
	public function setParent(ColumnGroup $objColumnGroup){
		$this->parent = $objColumnGroup;
	}
	//NEW[5]
	public function getParent(){
		return $this->parent;
	}


	//NEW[6]
	public function getColGrpLabel(){
		return $this->strColGrpLabel;
	}

	//NEW[7]
	public function getColGrpSeqNo(){
		return $this->intCgNo;
	}

	//NEW[8]
	public function addColumn(ColumnGroup $objColumnGroup){
		//----ユーザが直接カスタマイズすることがあるのでハンドリング
		global $g;
		try{
			if( gettype($objColumnGroup) != "object" ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( is_a($objColumnGroup, "ColumnGroup") === false ){
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$this->children[] = $objColumnGroup;
			$objColumnGroup->setParent($this);
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310101);
			exit();
		}
		return true;
		//ユーザが直接カスタマイズすることがあるのでハンドリング----
	}

	//NEW[9]
	public function getEdgeColumn(&$ary){
		//----再帰関数
		//----TableControlAgent::addColumnから呼ばれる
		global $g;
		try{
			if( $this->children === null || count($this->children) === 0 ){
				if( is_a($this,"Column") ){
					$ary[] = $this;
				}else{
					//----カラムグループだが、末端にカラムがない
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
					//カラムグループだが、末端にカラムがない----
				}
			}else{
				foreach($this->children as $child){
					//----再帰起動
					$child->getEdgeColumn($ary);
					//再帰起動----
				}
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310102);
			exit();
		}
		//再帰関数----
	}

	//NEW[10]
	public function getStaticRowLevel(){
		
		if( $this->intStaticRowLevel=== null ){
			if( $this->boolRoot===true ){
				$this->intStaticRowLevel = 0;
			}else{
				$this->intStaticRowLevel = $this->parent->getStaticRowLevel() + 1;
			}
		}
		return $this->intStaticRowLevel;
	}

	//----フォーマッタごとに計算する必要がある
	//NEW[11]
	public function calcSpanLength($strFormatterId){
		//----まずカラム数を調べさせる
		$this->getHColCount($strFormatterId,true);
		//まずカラム数を調べさせる----
		$this->getHRowCount($strFormatterId,true);
		
		$this->setColNoRef($strFormatterId, 0);
	}
	//NEW[12]
	public function getHRowCount($strFormatterId, $boolReCalc=false){
		//----再帰関数
		global $g;
		$intSum = 0;
		$retIntVal = null;
		try{
			if( is_string($strFormatterId) === false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( $this->children === null || count($this->children) === 0 ){
				//----末端の要素である場合
				if( is_a($this, "Column") ){
					$objOutputType = $this->getOutputType($strFormatterId);
					if( $objOutputType->isVisible() ){
						$retIntVal = 1;
					}else{
						$retIntVal = 0;
					}
				}else{
					throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//末端の要素である場合----
			}else{
				//----末端の要素ではなかった場合
				$boolExecute = true;
				if( $boolReCalc=== false ){
					if( array_key_exists($strFormatterId, $this->aryRowSpanLength)===true ){
						$retIntVal = $this->aryRowSpanLength[$strFormatterId];
						$boolExecute = false;
					}
				}
				if( $boolExecute===true ){
					if( $this->getHColCount($strFormatterId)===0 ){
						//----直接または間接にADDされた実体Columnに、visible(true)の実体Columnが1個もないので、Heightも0扱い
						$retIntVal = 0;
						//直接または間接にADDされた実体Columnに、visible(true)の実体Columnが1個もないので、Heightも0扱い----
					}else{
						//----直接されたColumnGroup系インスタンスの、最大高の要素数を計算する
						foreach($this->children as $child){
							$intHRowCount = $child->getHRowCount($strFormatterId, $boolReCalc);
							if( $intSum < $intHRowCount ){
								$intSum = $intHRowCount;
							}
						}
						//直接されたColumnGroup系インスタンスの、最大高の要素数を計算する----
						
						//----直接されたColumnGroup系インスタンスの、最大高の要素数に、自身の1を追加
						$retIntVal = $intSum + 1;
						//直接されたColumnGroup系インスタンスの、最大高の要素数に、自身の1を追加----
					}
				}
				//末端の要素ではなかった場合----
			}
			$this->aryRowSpanLength[$strFormatterId] = $retIntVal;
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",$tmpErrMsgBody));
			webRequestForceQuitFromEveryWhere(500,90310103);
			exit();
		}
		return $retIntVal;
		//再帰関数----
	}
	//NEW[13]
	public function getHColCount($strFormatterId, $boolReCalc=false){
		//----再帰関数
		global $g;
		$intSum = 0;
		$retIntVal = null;
		try{
			if( is_string($strFormatterId) === false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( $this->children === null || count($this->children) === 0 ){
				//----末端の要素である場合
				if( is_a($this, "Column") ){
					//----実体カラムの場合
					$objOutputType = $this->getOutputType($strFormatterId);
					if( $objOutputType->isVisible()===true ){
						$retIntVal = 1;
					}else{
						$retIntVal = 0;
					}
					//実体カラムの場合----
				}else{
					throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//末端の要素である場合----
			}else{
				//----末端の要素ではなかった場合
				$boolExecute = true;
				if( $boolReCalc=== false ){
					if( array_key_exists($strFormatterId, $this->aryRowSpanLength)===true ){
						$retIntVal = $this->aryColSpanLength[$strFormatterId];
						$boolExecute = false;
					}
				}
				if( $boolExecute===true ){
					//----直接ADDされたColumnGruop系インスタンスに、直接または間接にADDされた実体Columnで、visible(true)数を計算する
					foreach($this->children as $child){
						$intSum += $child->getHColCount($strFormatterId, $boolReCalc);
					}
					//直接ADDされたColumnGruop系インスタンスに、直接または間接にADDされた実体Columnで、visible(true)数を計算する----
					$retIntVal = max($intSum, 0);
				}
				//末端の要素ではなかった場合----
			}
			$this->aryColSpanLength[$strFormatterId] = $retIntVal;
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",$tmpErrMsgBody));
			webRequestForceQuitFromEveryWhere(500,90310104);
			exit();
		}
		return $retIntVal;
		//再帰関数----
	}
	//NEW[14]
	public function setColNoRef($strFormatterId, $inNumber=0){
		//----再帰関数
		//ColumnGroup::calcSpanLength、から呼ばれる
		$intRet = $inNumber;
		try{
			if( is_string($strFormatterId) === false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( $this->children === null || count($this->children) === 0 ){
				if( is_a($this, "Column")===true ){
					$objOutputType = $this->getOutputType($strFormatterId);
					if( $objOutputType->isVisible()===true ){
						$this->setColNo($strFormatterId, $inNumber);
						$intRet =  $inNumber + 1;
					}
				}else{
					throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
			}else{
				foreach($this->children as $child){
					$intRet = $child->setColNoRef($strFormatterId, $intRet);
				}
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310105);
			exit();
		}
		return $intRet;
		//再帰関数----
	}
	//フォーマッタごとに計算する必要がある----
	
	//----フォーマッタごとに異なる
	//NEW[15]
	public function setHRowNo($strFormatterId, $intRowNo){
		$this->aryHRowNo[$strFormatterId] = $intRowNo;
	}
	//NEW[16]
	public function getHRowNo($strFormatterId){
		$retInt = null;
		if( array_key_exists($strFormatterId, $this->aryHRowNo)===true ){
			$retInt = $this->aryHRowNo[$strFormatterId];
		}
		return $retInt;
	}
	//NEW[17]
	public function setColNo($strFormatterId, $intColNo){
		$this->aryColNo[$strFormatterId] = $intColNo;
	}
	//NEW[18]
	public function getColNo($strFormatterId){
		$retInt = null;
		if( array_key_exists($strFormatterId, $this->aryColNo)===true ){
			$retInt = $this->aryColNo[$strFormatterId];
		}
		return $retInt;
	}

	//NEW[19]
	public function getHeaderHtml($strFormatterId, $strTrClassName=""){
		$queue = array();
		$strAry = array();

		$this->calcSpanLength($strFormatterId);
		$maxCols = 0;
		$maxRows = 0;
		$this->getHeaderHtmlRef($strFormatterId, $queue, $strAry, $maxCols, $maxRows);

		//----ルートノードは不要なので取り除く
		array_shift($strAry);
		//ルートノードは不要なので取り除く----

		if($strTrClassName==""){
			$strTrClassName = "defaultExplainRow";
		}
		$ret = "";
		foreach($strAry as $row){
			$ret .= "<tr class=\"{$strTrClassName}\">".implode("\n",$row)."</tr>";
		}
		return $ret;
	}

	//NEW[20]
	public function getHeaderHtmlRef($strFormatterId, &$queue, &$strAry, &$maxCols = 0, &$maxRows = 0){
		//----再帰関数

		$intControlDebugLevel01 = 50;

		$colspan = $this->getHColCount($strFormatterId);
		$rowspan = $this->getHRowCount($strFormatterId);

		if($maxCols == 0){
			$maxCols = $colspan;
			$maxRows = $rowspan;
		}

		$intSelfRowNo = $this->getHRowNo($strFormatterId);

		if( $this->children===null || count($this->children)===0 ){
			$rowspan = $maxRows - $intSelfRowNo;
		}else{
			$rowspan = 1;
		}

		if( array_key_exists($intSelfRowNo, $strAry) === false ){
			$strAry[$intSelfRowNo] = array();
		}

		if($rowspan != 0 && $colspan != 0){
			if( is_a($this, "Column") === true ){
				//----カラムグループではなくカラムの場合
				
				$objOutputType = $this->getOutputType($strFormatterId);
				
				if( $objOutputType->isVisible() === true ){
					//----要素行の追加
					if( array_key_exists($intSelfRowNo, $strAry) === false ){
						$strAry[$intSelfRowNo] = array();
					}
					//要素行の追加----

					$strAry[$intSelfRowNo][] = $this->getOutputHeader($strFormatterId, $this->getColNo($strFormatterId), 'rowspan="'.$rowspan.'"');
				}else{
					//非表示なので何もしない
				}
				
				//カラムグループではなくカラムの場合----
			}else{
				//----カラムグループの場合
				
				if( $colspan==0 ){
					//----直接または間接にADDされたColumnが一つのVisible(true)ではない
					//直接または間接にADDされたColumnが一つのVisible(true)ではない----
				}else{
					//----要素行の追加
					if( array_key_exists($intSelfRowNo, $strAry) === false ){
						$strAry[$intSelfRowNo] = array();
					}
					//要素行の追加----
					
					$strAry[$intSelfRowNo][] = "<th colspan=\"{$colspan}\" rowspan=\"{$rowspan}\"><span class=\"generalBold\">".$this->getColGrpLabel()."</span></th>";
					if( $this->children !== null ){
						foreach($this->children as $child){
							$child->setHRowNo($strFormatterId, $intSelfRowNo+1);
							$queue[] = $child;
						}
					}
				}
				//カラムグループの場合----
			}
		}
		$q = array_shift($queue);
		if( $q !== null){
			//----再帰起動
			$q->getHeaderHtmlRef($strFormatterId, $queue, $strAry, $maxCols, $maxRows);
			//再帰起動----
		}
		//再帰関数----
	}

	//ここまで新規メソッドの定義宣言処理----

}


class Column extends ColumnGroup {
	/* Column : 列の情報を保持。 DBの列と対応する。
	ヘッダとボディのフォーマットを集約する役目
	フィルタ条件やヘッダかどうかなど、フォーマッタが出力するために必要な情報を保持。
	*/

	protected $intColAddedSeq;
	protected $strRequiredDisuseColId;	//"DISUSE_FLAG"

	protected $tmpFormatterRef;

	protected $strColId; // as string ユニーク。DBテーブルの場合はその名前（旧idText）
	protected $strColLabel;  // as string 表示文字列（旧name）
	protected $description;  // as string 項目の説明。フルダンプ時に使用

	protected $isDBColumn;  // as boolean DBのcolumnテーブルかどうか
	protected $isHiddenMainTableColumn;

	protected $unique;   // as boolean 一意性を要求するか

	protected $required;   // as boolean 必須かどうか
	protected $registerRequireExcept;
	protected $updateRequireExcept;

	protected $allowSendFromFile;   // as boolean Excel等からの送信を許すか(旧allowUpdate)

	protected $boolJournalSearchFilter; //履歴検索の条件として許すか否か

	protected $objMultiValidator;  //as Validator バリデーションチェッカー
	protected $deleteonbeforecheck;
	protected $deleteoffbeforecheck;
	protected $boolValidErrorPrefix;

	protected $aryFilterValueRawBase; // as array of string DBの検索条件(ユーザーの生値)
	protected $aryRichFilterValueRawBase; // as array of string DBの検索条件(ユーザーの生値)

	protected $aryFilterValueForDTiS;
	protected $aryRichValueForDTiS;

	protected $aryFilterValueForMatchCheck;
	protected $aryRichValueForMatchCheck;

	protected $nullSearchExecute;  //フィルター時に、nullを検出するか

	protected $boolShowSelectTagCaller;

	protected $searchType; //as SearchType 検索の種別。"like", "in", "range"

	protected $aryFunctionsForEvent;

	protected $aryObjOutputType; // as array of OutputType 表示フォーマット

	protected $isNum;  // as boolean データが数値(整数、小数、日付)かどうか。ソートと上限下限の方法に影響
	protected $header; // as boolean headerかどうか

	protected $boolHeaderSafingForBrowse;

	protected $subtotalFlag; // as boolean subtotalを計算するかどうか
	protected $subtotalValue;   // as boolean subtotalの値
	
	protected $classes;  // as array of string ボディに設定するクラス
	protected $prefix;  // as stirng 表示用のプレフィクス円マークなどに使用
	protected $postfix;  // as stirng 表示用のポストフィクス
	protected $columnIdHidden;  // as boolea カラム識別子をhtml上から隠すか

	protected $aryAddtionalNestProperty;

    protected $allowUploadColmnSendRestApi;    // as boolean FileUploadColumnクラスのRestAPIからの送信を許すか

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel){
		global $g;
		// (ローカル：コーディングルール)----変数代入用メソッドがある変数は、メソッド経由で代入すること
		$this->objTable = null;
		$this->intColAddedSeq = null;
		$this->setRequiredDisuseColumnID('');

		$this->setFormatterRef(null);

		$this->strColId = $strColId;
		$this->setColLabel($strColLabel);
		
		//$this->description = "このColumnの説明";
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11001"));
		
		//$this->dbSqlFormat = ""; //廃止済
		$this->setDBColumn(false);
		$this->setHiddenMainTableColumn(false);
		//$this->masterTableName = ""; //廃止済
		
		$this->setUnique(false);
		$this->setRequired(false);
		$this->setRegisterRequireExcept(false);
		$this->setUpdateRequireExcept(false);
		
		$this->setAllowSendFromFile(true);
		
		$this->setJournalSearchFilter(false);
		
		$this->objMultiValidator = new MultiValidator();
		
		$this->setDeleteOnBeforeCheck(false);
		$this->setDeleteOffBeforeCheck(false); //復活時は、値のバリデーションチェックを行なわない
		$this->setDeleteOffBeforeCheck(true); //復活は、値のバリデーションチェックを行う
		$this->setValidErrorPrefix(true);

		$this->delFilters();

		$this->setSelectTagCallerShow(false);

		$this->setSearchType("like");

		$this->aryFunctionsForEvent = null;

		$this->aryObjOutputType = array();
		$this->setOutputType("print_table",new OutputType(new SortedTabHFmt(), new TabBFmt()));
		$this->setOutputType("print_journal_table",new OutputType(new TabHFmt(), new TabBFmt()));

		$this->setOutputType("print_subtotal_table", new OutputType(new TabHFmt(), new SubtotalTabBFmt()));
		$this->getOutputType("print_subtotal_table")->setVisible(false);
		$this->setOutputType("delete_table",new OutputType(new TabHFmt(), new TabBFmt()));
		$this->setOutputType("filter_table",new OutputType(new TabHFmt(), new TextFilterTabBFmt()));
		$this->setOutputType("update_table",new OutputType(new ReqTabHFmt(), new TabBFmt()));
		$this->setOutputType("register_table",new OutputType(new ReqTabHFmt(), new TabBFmt()));
		$this->setOutputType("excel",new OutputType(new ExcelHFmt(), new ExcelBFmt()));
		$this->setOutputType("csv",new OutputType(new CSVHFmt(), new CSVBFmt()));
		$this->setEvent("filter_table", "onkeydown", "pre_search_async", array("'event.keyCode'"));

		$this->setOutputType("json",new OutputType(new JSONHFmt(), new JSONBFmt()));

		$this->setHeaderSafingForBrowse(true);
		$this->setPrefix('');
		$this->setPostfix('');

		$this->setSubtotalFlag(false);
		$this->subtotalValue = 0;

		$this->setNum(false);
		$this->setHeader(false);
		$this->classes = array();

		$this->setColumnIDHidden(true);

		$this->aryAddtionalNestProperty = array();

        $this->allowUploadColmnSendRestApi = false;
	}

	//----オブジェクト間・値連絡系
	function initTable(TableControlAgent $objTable, $colNo=null){
		$this->objTable = $objTable;
		$this->intColAddedSeq = $colNo;
	}
	//ColumnGroup[2]----

	//ここまで継承メソッドの上書き処理----

	function setToNestProperty($aryKeyNest,$varSome,&$retBoolSet=false){
		$retBoolSet = false;
		$boolKeyExists = false;
		if( is_array($aryKeyNest)===true ){
			list($retBoolSet,$boolKeyExists) = setToArrayNest($this->aryAddtionalNestProperty,$aryKeyNest,$varSome);
		}
	}
	function setNestProperty($aryProperty){
		if( is_array($aryProperty)===true ){
			$this->aryAddtionalNestProperty = $aryProperty;
		}
	}
	function getFromNestProperty($aryKeyNest,&$retBoolGet=false){
		$retVar = null;
		$retBoolGet = false;
		if( is_array($aryKeyNest)===true ){
			list($retVar,$retBoolGet) = isSetInArrayNestThenAssign($this->aryAddtionalNestProperty,$aryKeyNest,null);
		}
		return $retVar;
	}
	function getNestProperty(&$refProperty=array()){
		$refProperty = &$this->aryAddtionalNestProperty;
		return $this->aryAddtionalNestProperty;
	}

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getTable(){
		return $this->objTable;
	}
	//NEW[2]
	function getColumnSeqNo(){
		// tableにaddされた順番（最小値0）を返す
		return $this->intColAddedSeq;
	}
	//NEW[3]
	function setRequiredDisuseColumnID($strColIdText){
		if( is_string($strColIdText)===true ){
			$this->strRequiredDisuseColId = $strColIdText;
		}
	}
	//NEW[4]
	function getRequiredDisuseColumnID(){
		$retStr = $this->strRequiredDisuseColId;
		if( $retStr=="" ){
			if( is_a($this->objTable, "TableControlAgent")===true ){
				$retStr = $this->objTable->getRequiredDisuseColumnID();
			}
		}
		return $retStr;
	}
	//NEW[5]
	function setFormatterRef($objFormatter){
		$this->tmpFormatterRef = $objFormatter;
	}
	//NEW[6]
	function getFormatterRef(){
		return $this->tmpFormatterRef;
	}
	//オブジェクト間・値連絡系----

	//NEW[7]
	//----カラム識別系
	function getID(){
		return $this->strColId;
	}
	//NEW[8]
	function getIDSOP(){
		// [S]ynonym [O]n [P]HP
		return "COL_IDSOP_".$this->getColumnSeqNo();
	}
	//カラム識別系----

	//----カラム説明系
	//NEW[9]
	function setColLabel($strColLabel){
		$this->strColLabel = $strColLabel;
	}
	//NEW[10]
	function getColLabel($boolParentsAdd=false){
		$retStrValue = "";
		if( $boolParentsAdd === true ){
			$boolLoopFlag = true;
			$objColumnGroup = $this;
			do {
				$objColumnGroup = $objColumnGroup->getParent();

				if( $objColumnGroup->getStaticRowLevel() === 0 ){
					//----rootに至った場合
					$boolLoopFlag = false;
					$retStrValue .= $this->strColLabel;
				}else{
					$retStrValue = "".$objColumnGroup->getColGrpLabel()."/".$retStrValue;
				}
			} while( $boolLoopFlag === true );
		
		}else{
			$retStrValue = $this->strColLabel;
		}
		return $retStrValue;
	}
	//----シノニム
	//NEW[11]
	//----廃止予定
	public function getName(){
		return $this->getColLabel();
	}
	//廃止予定----
	//シノニム----
	//NEW[12]	
	function setDescription($description){
		$this->description = $description;
	}
	//NEW[13]
	function getDescription(){
		return $this->description;
	}
	//カラム説明系----

	//NEW[14]
	//----DB上の存否フラグ
	function setDBColumn($boolValue){
		$this->isDBColumn = $boolValue;
	}
	//NEW[15]
	function isDBColumn(){
		return $this->isDBColumn;
	}
	//NEW[16]
	function setHiddenMainTableColumn($boolValue){
		$this->isHiddenMainTableColumn = $boolValue;
	}
	//NEW[17]
	function isHiddenMainTableColumn(){
		return $this->isHiddenMainTableColumn;
	}
	//DB上の存否フラグ----

	//NEW[18]
	//----一意の値を保証するか否か
	function setUnique($boolValue){
		$this->unique = $boolValue;
	}
	//NEW[19]
	function isUnique(){
		return $this->unique;
	}
	//一意の値を保証するか否か----

	//--------ここから必須（NULLまたは空白入力、禁止）制御グループ
	//----登録、更新時には（項目を送信しなければならない、かつ、空白またはNULLを許容しない）という条件を設定
	//NEW[20]
	function setRequired($required){
		$this->required = $required;
	}
	//NEW[21]
	function isRequired(){
		return $this->required;
	}
	//登録、更新時には（項目を送信しなければならない、かつ、空白またはNULLを許容しない）という条件を設定----
	//NEW[22]
	function setRegisterRequireExcept($value){
		$this->registerRequireExcept = $value;
	}
	//NEW[23]
	function isRegisterRequireExcept(){
		return $this->registerRequireExcept;
	}
	//NEW[24]
	function setUpdateRequireExcept($value){
		$this->updateRequireExcept = $value;
	}
	//NEW[25]
	function isUpdateRequireExcept(){
		return $this->updateRequireExcept;
	}
	//ここから必須（NULLまたは空白入力、禁止）制御グループ--------
	//NEW[26]
	//----エクセル等のファイルでの更新を行うか(旧AllowUpdate)
	function setAllowSendFromFile($boolValue){
		$this->allowSendFromFile = $boolValue;
	}
	//NEW[27]
	function isAllowSendFromFile(){
		return $this->allowSendFromFile;
	}
	//エクセル等のファイルでの更新を行うか----(旧AllowUpdate)
	//NEW[28]
	function setJournalSearchFilter($boolValue){
		$this->boolJournalSearchFilter = $boolValue;
	}
	//NEW[29]
	function getJournalSearchFilter(){
		return $this->boolJournalSearchFilter;
	}
	//NEW[30]
	//----バリデーター管理系
	function setValidator($objValidator){
		$objMultiValidator = new MultiValidator();
		$objMultiValidator->addValidator($objValidator);
		$this->objMultiValidator = $objMultiValidator;
	}
	//NEW[31]
	function addValidator($objValidator){
		$this->getValidator()->addValidator($objValidator);
	}
	//NEW[32]
	function getValidator(){
		return $this->objMultiValidator;
	}
	//NEW[33]
	function setDeleteOnBeforeCheck($boolValue){
		$this->deleteonbeforecheck = $boolValue;
	}
	//NEW[34]
	function getDeleteOnBeforeCheck(){
		return $this->deleteonbeforecheck;
	}
	//NEW[35]
	function setDeleteOffBeforeCheck($boolValue){
		$this->deleteoffbeforecheck = $boolValue;
	}
	//NEW[36]
	function getDeleteOffBeforeCheck(){
		return $this->deleteoffbeforecheck;
	}
	//NEW[37]
	function setValidErrorPrefix($boolValue){
		$this->boolValidErrorPrefix = $boolValue;
	}
	//NEW[38]
	function getValidErrorPrefix(){
		return $this->boolValidErrorPrefix;
	}
	//----廃止または復活時等のレコード比較用
	//NEW[39]
	function compareRow(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet=true;
		$rowEditTgt = $aryVariant['edit_target_row'];
		if( array_key_exists($this->getID(),$rowEditTgt) === true ){
			//----送信受けしたデータのキーと同じキーを、比較行がもっている
			$boolRet=false;
			if($reqOrgData[$this->getID()] == $rowEditTgt[$this->getID()]){
				if(strlen($reqOrgData[$this->getID()]) == strlen($rowEditTgt[$this->getID()])){
					$boolRet=true;
				}
			}
			
			if($boolRet===false){
				dev_log($reqOrgData[$this->getID()]."<>".$rowEditTgt[$this->getID()],10);
				dev_log(strlen($reqOrgData[$this->getID()])."<>".strlen($rowEditTgt[$this->getID()]),10);
			}
			
			//送信受けしたデータのキーと同じキーを、比較行がもっている----
		}
		//送信受けしたデータにキーがある----
		return $boolRet;
	}
	//廃止または復活時等のレコード比較用----
	//バリデーター管理系----

	//----PULLDOWN検索系
	//NEW[40]
	function setSelectTagCallerShow($boolValue){
		$this->boolShowSelectTagCaller = $boolValue;
	}
	//NEW[41]
	function getSelectTagCallerShow(){
		return $this->boolShowSelectTagCaller;
	}
	//NEW[42]
	function setAddSelectTagPrintType($intValue){
	}
	//NEW[43]
	function getAddSelectTagPrintType(){
		return 0;
	}
	//NEW[44]
	function getAddSelectTagQuery($searchTblName, $strWhereAddBody=""){
		$retStrQuery = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strWpDUFColId = "{$dbQM}{$this->getRequiredDisuseColumnID()}{$dbQM}";

		$strWpTableName = "{$dbQM}{$searchTblName}{$dbQM}";

		$retStrQuery  = "SELECT DISTINCT {$strWpTblSelfAlias}.{$strWpColId} {$dbQM}KEY_COLUMN{$dbQM} ";
		$retStrQuery .= "FROM {$strWpTableName} {$strWpTblSelfAlias} ";
		$retStrQuery .= "WHERE {$strWpTblSelfAlias}.{$strWpColId} IS NOT NULL ";
		$retStrQuery .= "AND {$strWpDUFColId} IN ('0','1') ";
		$retStrQuery .= "{$strWhereAddBody} ";
		$retStrQuery .= "ORDER BY {$strWpTblSelfAlias}.{$strWpColId} ASC";

		$retArray = array($retStrQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		return $retArray;
	}
	//PULLDOWN検索系----

	//----フィルター関連

	//NEW[45]
	//----SELECT-filter一般系
	function getFilterConvertValue($value){
		return $value;
	}
	//NEW[46]
	function setSearchType($type){
		$this->searchType = $type;
	}
	//NEW[47]
	function getSearchType(){
		return $this->searchType;
	}
	//NEW[48]
	function getPartSqlInSelectZone(){
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$retStrQuery="{$strWpTblSelfAlias}.{$strWpColId}";
		return $retStrQuery;
	}
	//SELECT-filter一般系----

	//NEW[49]
	function setNullSearchExecute($boolValue){
		$this->nullSearchExecute = $boolValue;
	}
	//NEW[50]
	function getNullSearchExecute(){
		return $this->nullSearchExecute;
	}
	
	//----UI生値保存用のフィルター操作
	//NEW[51]
	//----SELECT-filter要素系
	function setFilterValues($value=array()){
		$this->aryFilterValueRawBase = $value;
	}
	//NEW[52]
	function addFilterValue($value, $index=null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index!==null){
			$this->aryFilterValueRawBase[$index] = $value;
		}else{
			$this->aryFilterValueRawBase[] = $value;
		}
	}
	//NEW[53]
	function getFilterValues(){
		return $this->aryFilterValueRawBase;
	}
	//NEW[54]
	function setRichFilterValues($value=array()){
		$this->aryRichFilterValueRawBase = $value;
	}
	//NEW[55]
	function addRichFilterValue($value, $index=null){
		if( $index!==null ){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}
	//NEW[56]
	function getRichFilterValues(){
		return $this->aryRichFilterValueRawBase;
	}
	//UI生値保存用のフィルター操作----
	
	//----DTiS用フィルター
	//----通常フィルタ
	//NEW[57]
	function setFilterValuesForDTiS($value=array()){
		$this->aryFilterValueForDTiS = $value;
	}
	//NEW[58]
	function addFilterValueForDTiS($value, $index=null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index!==null){
			$this->aryFilterValueForDTiS[$index] = $value;
		}else{
			$this->aryFilterValueForDTiS[] = $value;
		}
	}
	//NEW[59]
	function getFilterValuesForDTiS($boolForBind=true,$boolBinaryDistinctOnDTiS=true){
		if( $boolForBind===true ){
			return $this->getFilterValuesCoreForDTiS($this->aryFilterValueForDTiS, $boolBinaryDistinctOnDTiS);
		}else{
			return $this->aryFilterValueForDTiS;
		}
	}
	//NEW[60]
	function getFilterValuesCoreForDTiS(&$arrayFilterValues,$boolBinaryDistinctOnDTiS){
		$data = array();
		foreach($arrayFilterValues as $filter){
			if(0 < strlen($filter)){
				if($this->getSearchType() == "like"){
					//----LIKE検索のときは前後に％をつけ、さらにワイルドカードの％と＿をエスケープする。

					//----エスケープ文字は#で固定

					$filter = where_queryForLike_Wrapper($filter, $boolBinaryDistinctOnDTiS);

					//エスケープ文字は#で固定----

					$data[] = '%'.$filter.'%';

					//LIKE検索のときは前後に％をつけ、さらにワイルドカードの％と＿をエスケープする。----
				}else{
					$data[] = $filter;
				}
			}
		}
		return $data;
	}
	//通常フィルタ----
	//----リッチフィルタ
	//NEW[61]
	function setRichFilterValuesForDTiS($value=array()){
		$this->aryRichFilterValueForDTiS = $value;
	}
	//NEW[62]
	function addRichFilterValueForDTiS($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueForDTiS[$index] = $value;
		}else{
			$this->aryRichFilterValueForDTiS[] = $value;
		}
	}
	//NEW[63]
	function getRichFilterValuesForDTiS($boolForBind=true){
		return $this->aryRichFilterValueForDTiS;
	}
	//リッチフィルタ----
	//DTiS用フィルター----

	//----FilterMatch用フィルター
	//----通常フィルタ
	//NEW[64]
	function setFilterValuesForMatchCheck($value=array()){
		$this->aryFilterValueForMatchCheck = $value;
	}
	//NEW[65]
	function addFilterValueForMatchCheck($value, $index=null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index!==null){
			$this->aryFilterValueForMatchCheck[$index] = $value;
		}else{
			$this->aryFilterValueForMatchCheck[] = $value;
		}
	}
	//NEW[66]
	function getFilterValuesForMatchCheck(){
		return $this->aryFilterValueForMatchCheck;
	}
	//通常フィルタ----
	//----リッチフィルタ
	//NEW[67]
	function setRichFilterValuesForMatchCheck($value=array()){
		$this->aryRichFilterValueForMatchCheck = $value;
	}
	//NEW[68]
	function addRichFilterValueForMatchCheck($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueForMatchCheck[$index] = $value;
		}else{
			$this->aryRichFilterValueForMatchCheck[] = $value;
		}
	}
	//NEW[69]
	function getRichFilterValuesForMatchCheck(){
		return $this->aryRichFilterValueForMatchCheck;
	}
	//リッチフィルタ----
	//FilterMatch用フィルター----
	
	
	//NEW[70]
	function delFilters(){
		$this->nullSearchExecute = false;

		$this->aryFilterValueRawBase = array();
		$this->aryRichFilterValueRawBase = array();

		$this->aryFilterValueForDTiS = array();
		$this->aryRichFilterValueForDTiS = array();

		$this->aryFilterValueForMatchCheck = array();
		$this->aryRichFilterValueForMatchCheck = array();
	}
	//SELECT-filter要素系----

	//SELECT-filterクエリ系----
	//NEW[71]
	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;

		$retStrQuery = "";
		switch($this->getSearchType()){
			case "like":
				$retStrQuery = $this->getFilterQueryLikeZone($boolBinaryDistinctOnDTiS);
				break;
			case "in":
				$retStrQuery = $this->getFilterQueryInZone($boolBinaryDistinctOnDTiS);
				break;
			default:
				break;
		}
		return $retStrQuery;
	}
	//NEW[72]
	function getFilterQueryLikeZone($boolBinaryDistinctOnDTiS){
		//----クラス（Column）のgetFilterQueryからのみ呼ばれる
		global $g;
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		//[W]rap[F]x[F]or[C]olumn[M]ark
		$strWFFCMInDBHead = "";
		$strWFFCMInDBTail = "";
		$strWFFCMInNeedTipHead = "";
		$strWFFCMInNeedTipTail = "";
		$strCollate="";
		if($g['db_model_ch'] == 0){
			if( $boolBinaryDistinctOnDTiS === false ){
				$strWFFCMInDBHead = "TO_VALUE_FOR_FAZZY_MATCH(";
				$strWFFCMInDBTail = ")";
				$strWFFCMInNeedTipHead = "";
				$strWFFCMInNeedTipTail = "";
			}
		}else if($g['db_model_ch'] == 1){
			if( $boolBinaryDistinctOnDTiS === false ){
				$strCollate = "COLLATE utf8_unicode_ci ";
			}
		}

		$tmpArray = array();
		$intFilterCount = 0;

		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpStr01  = "{$strWFFCMInDBHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWFFCMInDBTail}";
				$tmpStr01 .= " {$strCollate}LIKE {$strWFFCMInNeedTipHead}:{$this->getID()}__{$intFilterCount}{$strWFFCMInNeedTipTail} ESCAPE '#' ";
				$tmpArray[] = $tmpStr01;
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			$retStrQuery .= "(".implode(" OR ", $tmpArray) . ")";
		}
		return $retStrQuery;
		//クラス（Column）のgetFilterQueryからのみ呼ばれる----
	}
	//NEW[73]
	function getFilterQueryInZone($boolBinaryDistinctOnDTiS){
		//----クラス（Column）のgetFilterQueryからのみ呼ばれる
		global $g;
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;
		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = ":{$this->getID()}__{$intFilterCount}";
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWpTblSelfAlias}.{$strWpColId}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
		//クラス（Column）のgetFilterQueryからのみ呼ばれる----
	}

	//NEW[74]
	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		return "";
	}
	//NEW[75]
	function getNullSearchQuery(){
		//----WHERE句[1]
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$retStrQuery  = " {$strWpTblSelfAlias}.{$strWpColId} IS NULL ";

		return $retStrQuery;
	}
	//NEW[76]
	function getRowSelectQuery(&$aryQueryElement=array()){
		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		if($aryQueryElement[$this->getID()] === ""){
			//----DBを問わず、空文字はNULLとしてDBへ入れる。（DBがmySQLで、指定がある場合のみ(空文字)で、DBへ投入するかは別検討）
			$strSetValue = "{$strWpTblSelfAlias}.{$strWpColId} IS NULL";
			$aryQueryElement[$this->getID()] = array("bind"=>false);
			//DBを問わず、空文字はNULLとしてDBへ入れる。（DBがmySQLで、指定がある場合のみ(空文字)で、DBへ投入するかは別検討）----
		}else{
			$strSetValue = "{$strWpTblSelfAlias}.{$strWpColId} = ".$this->getFilterConvertValue(":{$this->getID()}");
		}
		return $strSetValue;
	}
	//SELECT-filterクエリ系----

	//----IUD系クエリ作成系
	//NEW[77]
	function getRowRegisterQuery(&$aryQueryElement=array()){
		if($aryQueryElement[$this->getID()] === ""){
			$strSetValue = "NULL";
			$aryQueryElement[$this->getID()] = array("bind"=>false);
		}else{
			$strSetValue = ":{$this->getID()}";
		}
		return $strSetValue;
	}
	//NEW[78]
	function getRowUpdateQuery(&$aryQueryElement=array()){
		if($aryQueryElement[$this->getID()] === ""){
			$strSetValue = "{$this->getID()} = NULL";
			$aryQueryElement[$this->getID()] = array("bind"=>false);
		}else{
			$strSetValue = "{$this->getID()} = :{$this->getID()}";
		}
		return $strSetValue;
	}
	//IUD系クエリ作成系----

	//NEW[79]
	function setFunctionForEvent($strEventKey,$objFunction){
		if( is_string($strEventKey)===true ){
			if( is_callable($objFunction)===true ){
				if( is_array($this->aryFunctionsForEvent)===false ){
					$this->aryFunctionsForEvent = array();
				}
				$this->aryFunctionsForEvent[$strEventKey] = $objFunction;
			}
		}
	}

	//----FixColumnイベント系
	//NEW[80]
	function beforeFixColumn(){
		$boolRet = true;
		if( is_null($this->aryFunctionsForEvent)===true ){
		}else{
			if( array_key_exists('beforeFixColumn',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['beforeFixColumn'];
				return $objFunction($this,'beforeFixColumn');
			}
		}
		return $boolRet;
	}
	//NEW[81]
	function afterFixColumn(){
		$boolRet = true;
		if( is_null($this->aryFunctionsForEvent)===true ){
		}else{
			if( array_key_exists('beforeFixColumn',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['afterFixColumn'];
				return $objFunction($this,'afterFixColumn');
			}
		}
		return $boolRet;
	}
	//FixColumnイベント系----

	//----TableIUDイベント系
	//NEW[82]
	function getSequencesForTrzStart(&$arySequence=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//NEW[83]
	function afterTrzStartAction(&$aryVariant=array()){
		//----トランザクション内
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('inTrzStartAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['inTrzStartAction'];
				$retArray = $objFunction($this,'inTrzStartAction',$aryVariant);
			}
		}
		return $retArray;
		//トランザクション内----
	}
	
	//NEW[84]
	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true || array_key_exists('beforeIUDValidateCheck',$this->aryFunctionsForEvent)!==true){
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecDelete" ){
				//----廃止/復活時に、入力データを補完する
				if( array_key_exists($this->getID(),$reqOrgData)===false ){
					//----送信されてこなかった
					list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("edit_target_row",$this->getID()),null);
					if( $tmpBoolKeyExist===true ){
						$reqOrgData[$this->getID()] = $varValue;
					}
					//送信されてこなかった----
				}

				//廃止/復活時に、入力データを補完する----
			}
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
            $objFunction = $this->aryFunctionsForEvent['beforeIUDValidateCheck'];
            $retArray = $objFunction($this,'beforeIUDValidateCheck',$exeQueryData, $reqOrgData, $aryVariant);
		}
		return $retArray;
	}
	
	//NEW[85]
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----ポリシー：原則として、$exeQueryDataにキーを追加、または、削除、のみを行う。返し値は文字列型。
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('beforeTableIUDAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['beforeTableIUDAction'];
				$retArray = $objFunction($this,'beforeTableIUDAction',$exeQueryData, $reqOrgData, $aryVariant);
			}
		}
		return $retArray;
		//ポリシー：原則として、$exeQueryDataにキーを追加、または、削除、のみを行う。返し値は文字列型。----
	}
	//NEW[86]
	function inTrzBeforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----トランザクション内
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		//$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('inTrzBeforeTableIUDAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['inTrzBeforeTableIUDAction'];
				$retArray = $objFunction($this,'inTrzBeforeTableIUDAction',$exeQueryData, $reqOrgData, $aryVariant);
			}
		}
		return $retArray;
		//トランザクション内----
	}
	//NEW[87]
	function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----トランザクション内
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('inTrzAfterTableIUDAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['inTrzAfterTableIUDAction'];
				$retArray = $objFunction($this,'inTrzAfterTableIUDAction',$exeQueryData, $reqOrgData, $aryVariant);
			}
		}
		return $retArray;
		//トランザクション内----
	}
	//NEW[88]
	function afterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('afterTableIUDAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['afterTableIUDAction'];
				$retArray = $objFunction($this,'afterTableIUDAction',$exeQueryData, $reqOrgData, $aryVariant);
			}
		}
		return $retArray;
	}
	//TableIUDイベント系----
	
	//----DTiS系イベント
	//NEW[89]
	function beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, &$aryFilterData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('beforeDTiSValidateCheck',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['beforeDTiSValidateCheck'];
				$retArray = $objFunction($this,'beforeDTiSValidateCheck',$strFormatterId, $boolBinaryDistinctOnDTiS, $aryFilterData, $aryVariant);
			}
		}
		return $retArray;
	}
	//NEW[90]
	function beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, &$aryFilterData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true || array_key_exists('beforeDTiSAction',$this->aryFunctionsForEvent)!==true){
			foreach($this->aryFilterValueRawBase as $key=>$value){
				$this->aryFilterValueForDTiS[$key] = $value;
				$this->aryFilterValueForMatchCheck[$key] = $value;
			}
			foreach($this->aryRichFilterValueRawBase as $key=>$value){
				$this->aryRichFilterValueForDTiS[$key] = $value;
				$this->aryRichFilterValueForMatchCheck[$key] = $value;
			}
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
            $objFunction = $this->aryFunctionsForEvent['beforeDTiSAction'];
            $retArray = $objFunction($this,'beforeDTiSAction',$strFormatterId, $boolBinaryDistinctOnDTiS, $aryFilterData, $aryVariant);
		}
		return $retArray;
	}
	//NEW[91]
	function afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, &$aryFilterData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('afterDTiSAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['afterDTiSAction'];
				$retArray = $objFunction($this,'afterDTiSAction',$strFormatterId, $boolBinaryDistinctOnDTiS, $aryFilterData, $aryVariant);
			}
		}
		return $retArray;
	}
	//DTiS系イベント----

	//----各Webテーブル表示系（共通）

	//----出力論理情報の制御

	//----OutputType系オブジェクトの参照を、設定・取得
	//NEW[92]
	function setOutputType($strFormatterId, $outputType){
		global $g;
		try{
			if( is_string($strFormatterId) === false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( gettype($outputType) != "object" ){
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( is_a($outputType, "OutputType") === false ){
				throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$this->aryObjOutputType[$strFormatterId] = $outputType;
			$this->aryObjOutputType[$strFormatterId]->init($this, $strFormatterId);
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310106);
			exit();
		}
	}
	//NEW[93]
	function getOutputType($strFormatterId){
		global $g;
		$retVariant = null;
		try{
			if( isset($this->aryObjOutputType[$strFormatterId]) !== true ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$retVariant = $this->aryObjOutputType[$strFormatterId];
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310107);
			exit();
		}
		return $retVariant;
	}
	//OutputType系オブジェクトの参照を、設定・取得----

	//----Htmlタグ出力系
	//NEW[94]
	function getOutputHeader($strFormatterId, $colNo=null, $attr="", $nosort=""){
		//----各Formatterクラスから呼ばれる
		if( isset($this->aryObjOutputType[$strFormatterId]) === true && $this->aryObjOutputType[$strFormatterId]->isVisible() === true ){
			return $this->aryObjOutputType[$strFormatterId]->getHeadTag($colNo, $attr, $nosort);
		}
	}
	//NEW[95]
	function getOutputBody($strFormatterId, $aryRawRecord){
		//----各Formatterクラスから呼ばれる
		if( isset($this->aryObjOutputType[$strFormatterId]) === true && $this->aryObjOutputType[$strFormatterId]->isVisible() === true ){
			$aryVariant = array();
			return $this->aryObjOutputType[$strFormatterId]->getBodyTag($aryRawRecord,$aryVariant);
		}
	}
	//NEW[96]
	function setDefaultValue($strFormatterId, $value){
		//inputタグ系のデフォルト値を設定する
		$this->getOutputType($strFormatterId)->setDefaultInputValue($value);
	}
	//NEW[97]
	function setEvent($type, $eventName, $jsFunctionName, $jsFunctionArgs=array()){
		$this->getOutputType($type)->setJsEvent($eventName, $jsFunctionName, $jsFunctionArgs);
	}
	//Htmlタグ出力系----

	//NEW[98]
	function setHeaderSafingForBrowse($boolHeaderSafingForBrowse){
		$this->boolHeaderSafingForBrowse = $boolHeaderSafingForBrowse;
	}
	//NEW[99]
	function isHeaderSafingForBrowse(){
		return $this->boolHeaderSafingForBrowse;
	}
	//NEW[100]
	function setPrefix($str){
		$this->prefix = $str;
	}
	//NEW[101]
	function getPrefix(){
		return $this->prefix;
	}
	//NEW[102]
	function setPostfix($str){
		$this->postfix = $str;
	}
	//NEW[103]
	function getPostfix(){
		return $this->postfix;
	}

	//----数値系カラム
	//NEW[104]
	function setSubtotalFlag($subtotalFlag, $strFormatterId="print_subtotal_table"){
		$this->subtotalFlag = $subtotalFlag;
		$this->getOutputType($strFormatterId)->setVisible($subtotalFlag);
	}
	//NEW[105]
	function getSubtotalFlag(){
		return $this->subtotalFlag;
	}
	//NEW[106]
	function addSubtotalValue($value){
	}
	//NEW[107]
	function getSubtotalValue(){
		return $this->subtotalValue;
	}
	//NEW[108]
	function subTotalAddBeforeCheck($row, &$refArrayVariant=array()){
		return true;
	}
	//数値系カラム----
	//出力論理情報の制御----

	//NEW[109]
	function addClass($strCssClassName){
		$this->classes[] = $strCssClassName;
	}
	//NEW[110]
	function delClass($strCssClassName){
		//見つかったら全部消す。1つでもあるとNGなので。
		foreach(array_keys($this->classes, $strCssClassName) as $data){
			unset($this->classes[$data]);
		}
	}
	//NEW[111]
	function getClasses(){
		return $this->classes;
	}
	//NEW[112]
	function setHeader($header){
		// メソッド名が不適切
		$this->header = $header;
	}
	//NEW[113]
	function isHeader(){
		return $this->header;
	}
	//NEW[114]
	function setNum($isNum){
		$this->isNum = $isNum;
	}
	//NEW[115]
	function isNum(){
		return $this->isNum;
	}
	//----DBカラム識別子をhtml上に表示させるかどうか（ユーザへのアナウンンス禁止。凍結しておくべき機能）
	//NEW[116]
	function setColumnIDHidden($boolValue){
		$this->columnIdHidden = $boolValue;
	}
	//NEW[117]
	function getColumnIDHidden(){
		return $this->columnIdHidden;
	}
	//DBカラム識別子をhtml上に表示させるかどうか（ユーザへのアナウンンス禁止。凍結しておくべき機能）----

	//----FileUploadColumnクラスのRestAPIからの送信を許すか
	//NEW[118]
	function setAllowUploadColmnSendRestApi($boolValue){
		$this->allowUploadColmnSendRestApi = $boolValue;
	}
	//NEW[119]
	function isAllowUploadColmnSendRestApi(){
		return $this->allowUploadColmnSendRestApi;
	}
	//FileUploadColumnクラスのRestAPIからの送信を許すか----

	//各Webテーブル表示系（共通）----

	//----webデザイン系

	//ここまで新規メソッドの定義宣言処理

}

class IDColumn extends Column {
	/* 入力に制限を加えるため、6つ目の引数に masterTableと同じ定義でフィルタされたViewを指定することができる*/

	protected $arrayMasterSetFromMainTable;

	protected $strMasterTableIdForFilter;
	protected $strMasterTableAgentQueryForFilter;
	protected $arrayMasterSetForFilter;

	protected $strMasterTableIdForInput;
	protected $strMasterTableAgentQueryForInput;
	protected $arrayMasterSetForInput;

	protected $strKeyColumnIdOfMaster;
	protected $strDispColumnIdOfMaster;

	protected $aryFormatMultiple;
	protected $aryEtceteraParameter;

	protected $errMsgHead;
	protected $errMsgTail;

	protected $strJournalTableOfMaster;
	protected $strJournalSeqIdOfMaster;
	protected $strJournalKeyIdOfMaster;
	protected $strJournalDispIdOfMaster;
	protected $strJournalLUTSIdOfMaster;

	protected $strTempBuf;

	//----「(set/get)SearchType()で制御されてきた。

	//----ここから継承メソッドの上書き処理

	//OVR[ignored]::[1]
	function __construct($strColId, $strColLabel, $masterTableIdForFilter, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterTableIdForInput="", $aryEtcetera=array()){
		global $g;
		parent::__construct($strColId, $strColLabel);

		// ----変数の初期化
		$this->strTempBuf = null;
		
		$this->arrayMasterSetForFilter = null;
		$this->arrayMasterSetForInput = null;
		$this->arrayMasterSetFromMainTable = null;
		$this->arrayMasterSetFromJournalTable = null;

		$this->aryFormatMultiple = array();
		// 変数の初期化----

		if( array_key_exists("MasterKeyColumnType", $aryEtcetera) === false ){
			//----キー側は数値
			$aryEtcetera['MasterKeyColumnType'] = 0;
		}
		if( array_key_exists("MasterDisplayColumnType", $aryEtcetera) === false ){
			//----表示側は文字列
			$aryEtcetera['MasterDisplayColumnType'] = 1;
		}

		$this->aryEtceteraParameter = $aryEtcetera;

		$this->setDBColumn(true);
		$this->setNum(true);
		$this->setSearchType("like");


		$this->setMasterTableIDForFilter($masterTableIdForFilter);
		if( $masterTableIdForInput == "" ){
			$this->setMasterTableIDForInput($masterTableIdForFilter);
		}else{
			$this->setMasterTableIDForInput($masterTableIdForInput);
		}

		$this->setKeyColumnIDOfMaster($strKeyColumnIDOfMaster);
		$this->setDispColumnIDOfMaster($strDispColumnIdOfMaster);

		$outputType = new OutputType(new ReqTabHFmt(), new SelectTabBFmt());
		$this->setOutputType("register_table", $outputType);

		$outputType = new OutputType(new ReqTabHFmt(), new SelectTabBFmt());
		$this->setOutputType("update_table", $outputType);

		$outputType = new IDOutputType(new TabHFmt(), new TextTabBFmt());
		$this->setOutputType("delete_table", $outputType);

		$outputType = new OutputType(new FilterTabHFmt(), new TextFilterTabBFmt());
		$this->setOutputType("filter_table", $outputType);
		$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));

		$this->setMultiple("filter_table",true);

		$outputType = new IDOutputType(new SortedTabHFmt(), new TextTabBFmt());
		$this->setOutputType("print_table", $outputType);
		$outputType = new IDOutputType(new TabHFmt(), new TextTabBFmt());
		$this->setOutputType("print_journal_table", $outputType);

		$outputType = new IDOutputType(new ExcelHFmt(), new ExcelSelectBFmt());
		$this->setOutputType("excel", $outputType);
		$outputType = new IDOutputType(new CSVHFmt(), new CSVSelectBFmt());
		$this->setOutputType("csv", $outputType);
		$outputType = new IDOutputType(new JSONHFmt(), new JSONSelectBFmt());
		$this->setOutputType("json", $outputType);
		$this->setValidator(new IDValidator($this));
		$this->setErrMsgHead($g['objMTS']->getSomeMessage("ITAWDCH-STD-11101"));
		$this->setErrMsgTail("");

		$this->setJournalTableOfMaster(null);
		$this->setJournalSeqIDOfMaster(null);
		$this->setJournalKeyIDOfMaster(null);
		$this->setJournalDispIDOfMaster(null);
		$this->setJournalLUTSIDOfMaster(null);

		$this->setSelectTagCallerShow(true);
	}

	//OVR[ignored]::[01]
	function initTable($objTable, $colNo){
		parent::initTable($objTable, $colNo);
	}

	//----廃止または復活時等のレコード比較用
	//OVR[-]::[39]
	function compareRow(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----送信受けしたデータ[$reqOrgData]に、キーに、$this->getID()があることが前提
		$boolRet=true;
		$boolExeContinue=true;
		$varDataInDBColumn="";
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
			$boolRet = parent::compareRow($exeQueryData, $reqOrgData, $aryVariant);
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			$rowEditTgt = $aryVariant['edit_target_row'];
			if( array_key_exists($this->getID(),$rowEditTgt) === true ){
				//----鍵が、DB現在行に、存在していた場合
				$boolRet=false;
				//----やや、あいまい、で比較
				if( $reqOrgData[$this->getID()] == $rowEditTgt[$this->getID()] ){
					$intLenInTable = strlen($rowEditTgt[$this->getID()]);
					if( strlen($reqOrgData[$this->getID()]) == $intLenInTable ){
						$boolRet=true;
						if( $this->getTempBuffer() === null ){
							//----ブラウザからの場合
							//ブラウザからの場合----
						}else{
							if( $intLenInTable == 0 && strlen($this->getTempBuffer()) != 0 ){
								//----DB内の値は文字列長0だが、変換前の審査値は文字列長0ではない
								$boolRet=false;
								//DB内の値は文字列長0だが、変換前の審査値は文字列長0ではない----
							}
						}
					}
				}
			}
			//やや、あいまい、で比較----
			//鍵が、DB現在行に、存在していた場合----
		}

		//送信受けしたデータ[$reqOrgData]に、キーに、$this->getID()があることが前提----
		return $boolRet;
	}
	//廃止または復活時等のレコード比較用----

	//----PULLDOWN検索系
	//OVR[-]::[43]
	function getAddSelectTagPrintType(){
		return 1;
	}

	//OVR[-]::[44]
	function getAddSelectTagQuery($searchTblId, $strWhereAddBody=""){
		$retStrQuery = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		$objTable = $this->getTable();
		
		$mainTableBody = $objTable->getDBMainTableBody();
		$strThisIdColumnId = $this->getID();
		$strDUColumnOfMainTable = $objTable->getRequiredDisuseColumnID();
		
		$refMasterKeyColumn = $this->getKeyColumnIDOfMaster();
		$refMasterDispColumn = $this->getDispColumnIDOfMaster();
		$refMasterTableBody = $this->getMasterTableBodyForFilter();
		$refMasterDUColumn = $this->getRequiredDisuseColumnID();
		$aryEtcetera = $this->getEtceteraParameter();

		$retStrQuery = genSQLforGetMasValsInMainTbl($mainTableBody, $strThisIdColumnId, $strDUColumnOfMainTable
													, $refMasterTableBody, $refMasterKeyColumn, $refMasterDispColumn, $refMasterDUColumn
													, $aryEtcetera,$strWhereAddBody,"KEY_COLUMN","DISP_COLUMN");

		$retArray = array($retStrQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		return $retArray;
	}
	//PULLDOWN検索系----

	//OVR[-]::[54]
	function setRichFilterValues($value){
		$this->aryRichFilterValueRawBase = $value;
	}

	//OVR[-]::[55]
	function addRichFilterValue($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}

	//OVR[-]::[60]
	function getFilterValuesCoreForDTiS(&$arrayFilterValues,$boolBinaryDistinctOnDTiS){
		$data = array();
		foreach($arrayFilterValues as $filter){
			if(0 < strlen($filter)){
				if($this->getSearchType() == "like" && $this->getMasterDisplayColumnType()===1 ){
					//----LIKE検索のときは前後に％をつけ、さらにワイルドカードの％と＿をエスケープする。

					//----エスケープ文字は#で固定

					$filter = where_queryForLike_Wrapper($filter, $boolBinaryDistinctOnDTiS);

					//エスケープ文字は#で固定----

					$data[] = '%'.$filter.'%';

					//LIKE検索のときは前後に％をつけ、さらにワイルドカードの％と＿をエスケープする。----
				}else{
					$data[] = $filter;
				}
			}
		}
		return $data;
	}

	//OVR[-]::[71]
	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;

		$retStrQuery = "";
		switch($this->getSearchType()){
			case "like":
				if( $this->getMasterDisplayColumnType()===1 ){
					$retStrQuery = $this->getFilterQueryLikeZone($boolBinaryDistinctOnDTiS);
					break;
				}
			case "in":
				$retStrQuery = $this->getFilterQueryInZone($boolBinaryDistinctOnDTiS);
				break;
			default:
				break;
		}
		return $retStrQuery;
	}

	//OVR[-]::[72]
	function getFilterQueryLikeZone($boolBinaryDistinctOnDTiS){
		global $g;
		$retStrQuery = "";

		//----メインテーブル
		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		//メインテーブル----

		//----参照先マスタテーブル
		$strWpColKeyIdOfMaster  = "{$dbQM}{$this->getKeyColumnIDOfMaster()}{$dbQM}";
		$strWpColDispIdOfMaster = "{$dbQM}{$this->getDispColumnIDOfMaster()}{$dbQM}";
		$strWpColDisuseFlagIdOfMaster = "{$dbQM}{$this->getRequiredDisuseColumnID()}{$dbQM}";
		//参照先マスタテーブル----

		//[W]rap[F]x[F]or[C]olumn[M]ark
		$strWFFCMInDBHead = "";
		$strWFFCMInDBTail = "";
		$strWFFCMInNeedTipHead = "";
		$strWFFCMInNeedTipTail = "";
		$strCollate="";
		if($g['db_model_ch'] == 0){
			if( $boolBinaryDistinctOnDTiS === false ){
				//[W]rap[F]x[F]or[C]olumn[M]ark
				$strWFFCMInDBHead = "TO_VALUE_FOR_FAZZY_MATCH(";
				$strWFFCMInDBTail = ")";
				$strWFFCMInNeedTipHead = "";
				$strWFFCMInNeedTipTail = "";
			}
		}else if($g['db_model_ch'] == 1){
			if( $boolBinaryDistinctOnDTiS === false ){
				$strCollate = "COLLATE utf8_unicode_ci ";
			}
		}
		$tmpArray = array();
		$intFilterCount = 0;

		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpStr01  = "{$strWFFCMInDBHead}{$strWpColDispIdOfMaster}{$strWFFCMInDBTail} ";
				$tmpStr01 .= " {$strCollate}LIKE {$strWFFCMInNeedTipHead}:{$this->getID()}__{$intFilterCount}{$strWFFCMInNeedTipTail} ESCAPE '#' ";
				$tmpArray[] = $tmpStr01;
				$intFilterCount++;
			}
		}
		//表示側が文字列側の場合----

		if(0 < count($tmpArray)){
			$retStrQuery .= "{$strWpTblSelfAlias}.{$strWpColId} IN (";
			$retStrQuery .= "SELECT {$strWpColKeyIdOfMaster} ";
			$retStrQuery .= "FROM {$this->getMasterTableBodyForFilter()} ";
			$retStrQuery .= "WHERE (".implode(" OR ", $tmpArray) . ") ";
			$retStrQuery .= "AND {$strWpColDisuseFlagIdOfMaster} IN ('0','1') )";
		}
		return $retStrQuery;
	}

	//OVR[-]::[74]
	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		global $g;
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;

		$strWrapHead= "";
		$strWrapTail= "";

		if( $this->getMasterKeyColumnType()===1 ){
			//----鍵カラムが文字列型の場合
			if( $g['db_model_ch'] == 0 ){
				//----バイナリで精密な一致
				$strWrapHead="NLSSORT(";
				$strWrapTail=",'NLS_SORT=BINARY')";
				//バイナリで精密な一致----
			}else if( $g['db_model_ch'] == 1 ){
				$strWrapHead= "";
				$strWrapTail= "";
			}
			//鍵カラムが文字列型の場合----
		}

		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if( 0 < strlen($filter) ){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if( 0 < count($tmpArray) ){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}
	
	//OVR[-]::[75]
	function getNullSearchQuery(){
		//----WHERE句[1]
		global $g;
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		if( $g['db_model_ch'] == 0 ){
			//----ORACLE
			$retStrQuery = " {$strWpTblSelfAlias}.{$strWpColId} IS NULL ";
			//ORACLE----
		}else if( $g['db_model_ch'] == 1 ){
			//----mySQL/mariaDB
			$retStrQuery = " {$strWpTblSelfAlias}.{$strWpColId} IS NULL ";
			if( $this->getMasterKeyColumnType()===1 ){
				//----文字列キー型の場合は、空文字も検出する
				$retStrQuery = " ({$strWpTblSelfAlias}.{$strWpColId} IS NULL OR {$strWpTblSelfAlias}.{$strWpColId} = '') ";
				//文字列キー型の場合は、空文字も検出する----
			}
			//mySQL/mariaDB----
		}
		return $retStrQuery;
	}

	

	//----FixColumnイベント系
	//OVR[-]::[81]
	function afterFixColumn(){
		if( $this->getJournalTableOfMaster() !== null ){
			if( $this->getJournalSeqIDOfMaster() === null ){
				$this->setJournalSeqIDOfMaster($this->objTable->getRequiredJnlSeqNoColumnID());
			}
			if( $this->getJournalLUTSIDOfMaster() === null ){
				$this->setJournalLUTSIDOfMaster($this->objTable->getRequiredLastUpdateDateColumnID());
			}
			if( $this->getJournalKeyIDOfMaster() === null ){
				$this->setJournalKeyIDOfMaster($this->getKeyColumnIDOfMaster());
			}
			if( $this->getJournalDispIDOfMaster() === null ){
				$this->setJournalDispIDOfMaster($this->getDispColumnIDOfMaster());
			}
		}
	}
	//FixColumnイベント系----

	//----TableIUDイベント系
	//OVR[-]::[84]
	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
			//----行更新（登録を含む）の場合
			$this->valueConvertFromSomeFile($exeQueryData, $reqOrgData, $aryVariant);
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			//行更新（登録を含む）の場合----
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			//----廃止の場合
			//----エクセル等からの文字列を変換する特殊な処理
			$this->valueConvertFromSomeFile($exeQueryData, $reqOrgData, $aryVariant);
			//エクセル等からの文字列を変換する特殊な処理----
			//----キーがなかった場合に、値を$reqOrgDataに保管
			if( array_key_exists($this->getID(),$reqOrgData)===false ){
				list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryVariant,array("edit_target_row",$this->getID()),null);
				if( $tmpBoolKeyExist===true ){
					// JOURNAL専用のカラムではないので、$exeQueryData、に直接代入してはならない
					$reqOrgData[$this->getID()] = $varValue;
				}
			}
			//キーがなかった場合に、値を$reqOrgDataに保管----
			//廃止の場合----
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}
		return $retArray;
	}
	//OVR[-]::[87]
	function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		$this->setTempBuffer(null);
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----ここから、実装範囲拡大検討中の各メソッド
	//NEW[1]
	function setTempBuffer($strValue){
		$this->strTempBuf = $strValue;
	}
	//NEW[2]
	function getTempBuffer(){
		return $this->strTempBuf;
	}
	//NEW[3]
	function setEtceteraParameter($aryEtcetera){
		$this->aryEtceteraParameter = $aryEtcetera;
	}
	//NEW[4]
	function getEtceteraParameter(){
		return $this->aryEtceteraParameter;
	}
	//NEW[5]
	function etceteraParameterWrite($etceteraKey, $etceteraVal){
		$this->aryEtceteraParameter[$etceteraKey] = $etceteraVal;
	}
	//NEW[6]
	function etceteraParameterRead($etceteraKey, &$refRetValue){
		$keyExists = array_key_exists($etceteraKey, $this->aryEtceteraParameter);
		if($keyExists===true){
			$refRetValue = $this->aryEtceteraParameter[$etceteraKey];
		}else{
			$refRetValue = null;
		}
		return $keyExists;
	}
	//ここまで、実装範囲拡大検討中の各メソッド----

	//----鍵カラム
	//NEW[7]
	function setKeyColumnIDOfMaster($strColId){
		$this->strKeyColumnIdOfMaster = $strColId;
	}
	//NEW[8]
	function getKeyColumnIDOfMaster(){
		return $this->strKeyColumnIdOfMaster;
	}
	//----ここから応用メソッド
	//NEW[9]
	function setMasterKeyColumnType($value){
		$this->etceteraParameterWrite("MasterKeyColumnType", $value);
	}
	//NEW[10]
	function getMasterKeyColumnType(&$miRefKeyExists=false){
		$miRetValBody = "";
		$miRefKeyExists = $this->etceteraParameterRead("MasterKeyColumnType", $miRetValBody);
		return $miRetValBody;
	}
	//ここまで応用メソッド----
	//鍵カラム----

	//----表示側カラム
	//NEW[11]
	function setDispColumnIDOfMaster($strColId){
		$this->strDispColumnIdOfMaster = $strColId;
	}
	//NEW[12]
	function getDispColumnIDOfMaster(){
		return $this->strDispColumnIdOfMaster;
	}
	//----ここから応用メソッド
	//NEW[13]
	function setMasterDisplayColumnType($value){
		$this->etceteraParameterWrite("MasterDisplayColumnType", $value);
	}
	//NEW[14]
	function getMasterDisplayColumnType(&$miRefKeyExists=false){
		$miRetValBody = "";
		$miRefKeyExists = $this->etceteraParameterRead("MasterDisplayColumnType", $miRetValBody);
		return $miRetValBody;
	}
	//ここまで応用メソッド----
	//表示側カラム----

	// ----一覧表示用
	//NEW[15]
	public function getMasterTableIDForFilter(){
		return $this->strMasterTableIdForFilter;
	}
	//NEW[16]
	public function setMasterTableIDForFilter($strValue){
		$this->strMasterTableIdForFilter = $strValue;
	}
	//NEW[17]
	public function getMasterTableAgentQueryForFilter(){
		return $this->strMasterTableAgentQueryForFilter;
	}
	//NEW[18]
	public function setMasterTableAgentQueryForFilter($strValue){
		//----括弧はじまり、括弧おわり、で入れること
		$this->strMasterTableAgentQueryForFilter = $strValue;
	}
	//NEW[19]
	function getMasterTableBodyForFilter(){
		//----結合クエリまたはDBテーブル名を返す
		$retStrVal=$this->getMasterTableIDForFilter();
		if( $this->strMasterTableAgentQueryForFilter !== null ){
			$retStrVal=$this->strMasterTableAgentQueryForFilter." TABLE_AGENT ";
		}
		return $retStrVal;
	}
	// ----一覧表示用

	// ----入力表示用
	//NEW[20]
	public function getMasterTableIDForInput(){
		return $this->strMasterTableIdForInput;
	}
	//NEW[21]
	public function setMasterTableIDForInput($strValue){
		$this->strMasterTableIdForInput = $strValue;
	}
	//NEW[22]
	public function getMasterTableAgentQueryForInput(){
		return $this->strMasterTableAgentQueryForInput;
	}
	//NEW[23]
	public function setMasterTableAgentQueryForInput($strValue){
		//----括弧はじまり、括弧おわり、で入れること
		$this->strMasterTableAgentQueryForInput = $strValue;
	}
	//NEW[24]
	function getMasterTableBodyForInput(){
		//----結合クエリまたはDBテーブル名を返す
		$retStrVal=$this->getMasterTableIDForInput();
		if( $this->strMasterTableAgentQueryForInput !== null ){
			$retStrVal=$this->strMasterTableAgentQueryForInput." TABLE_AGENT ";
		}
		return $retStrVal;
	}
	// ----入力表示用

	//----履歴用マスターのジャーナル系
	//NEW[25]
	function setJournalTableOfMaster($jouranlTableOfMaster){
		$this->journalTableOfMaster = $jouranlTableOfMaster;
	}
	//NEW[26]
	function getJournalTableOfMaster(){
		return $this->journalTableOfMaster;
	}
	//NEW[27]
	function setJournalSeqIDOfMaster($jouranlSeqIdOfMaster){
		$this->journalSeqIdOfMaster = $jouranlSeqIdOfMaster;
	}
	//NEW[28]
	function getJournalSeqIDOfMaster(){
		return $this->journalSeqIdOfMaster;
	}
	//NEW[29]
	function setJournalKeyIDOfMaster($jouranlKeyIdOfMaster){
		$this->journalKeyIdOfMaster = $jouranlKeyIdOfMaster;
	}
	//NEW[30]
	function getJournalKeyIDOfMaster(){
		return $this->journalKeyIdOfMaster;
	}
	//NEW[31]
	function setJournalDispIDOfMaster($jouranlDispIdOfMaster){
		$this->journalDispIdOfMaster = $jouranlDispIdOfMaster;
	}
	//NEW[32]
	function getJournalDispIDOfMaster(){
		return $this->journalDispIdOfMaster;
	}
	//NEW[33]
	function setJournalLUTSIDOfMaster($jouranlLUTSIdOfMaster){
		$this->journalLUTSIdOfMaster = $jouranlLUTSIdOfMaster;
	}
	//NEW[34]
	function getJournalLUTSIDOfMaster(){
		return $this->journalLUTSIdOfMaster;
	}
	//履歴用マスターのジャーナル系----

	//----ここから応用メソッド
	//NEW[35]
	function setArrayMasterTableByFormatName($strFormatterId, $arrayValue){
		$this->etceteraParameterWrite("ArrayMasterTable_".$strFormatterId, $arrayValue);
	}
	//NEW[36]
	function getArrayMasterTableByFormatName($strFormatterId, &$miRefKeyExists=false){
		$miRetValBody = null;
		$miRefKeyExists = $this->etceteraParameterRead("ArrayMasterTable_".$strFormatterId, $miRetValBody);
		return $miRetValBody;
	}
	//ここまで応用メソッド----

	//NEW[37]
	function setMasterTableArrayFromMainTable($arrayData){
		$this->arrayMasterSetFromMainTable = $arrayData;
	}
	//NEW[38]
	function getMasterTableArrayFromMainTable(){
		//(1)IDColumn、がTableにAddされていない場合は「null」を返す。
		//(2)setMasterTableArrayFromMainTable、で、null、をセットしたとしても、通常は、配列を返す。
		if($this->arrayMasterSetFromMainTable === null){
			$objTable = $this->getTable();
			if( is_null($objTable) === true ){
				web_log("This column[".$this->getID()."] is not added in Table.");
			}else{
				//----フィルターテーブル用のデフォルト・データセットを作成
				$mainTableBody = $objTable->getDBMainTableBody();
				$strThisIdColumnId = $this->getID();
				$strDUColumnOfMainTable = $objTable->getRequiredDisuseColumnID();
				
				$refMasterKeyColumn = $this->getKeyColumnIDOfMaster();
				$refMasterDispColumn = $this->getDispColumnIDOfMaster();
				$refMasterTableBody = $this->getMasterTableBodyForFilter();
				$refMasterDUColumn = $this->getRequiredDisuseColumnID();
				$aryEtcetera = $this->getEtceteraParameter();

				//----マスターの全行のうち、メインテーブルで利用されている行のみに絞って、鍵カラムと表示カラム行、を取得する
				$this->arrayMasterSetFromMainTable=createMasterTableDistinctArray($mainTableBody, $strThisIdColumnId, $strDUColumnOfMainTable, $refMasterTableBody, $refMasterKeyColumn, $refMasterDispColumn, $refMasterDUColumn, $aryEtcetera);
				//マスターの全行のうち、メインテーブルで利用されている行のみに絞って、鍵カラムと表示カラム行、を取得する----

				if(is_array($this->arrayMasterSetFromMainTable)===true && 0 < count($this->arrayMasterSetFromMainTable)){
					//----正常に配列を取得できた
					//正常に配列を取得できた----
				}else{
				}
				//フィルターテーブル用のデフォルト・データセットを作成----
			}
		}
		return $this->arrayMasterSetFromMainTable;
	}

	//NEW[37]
	function setMasterTableArrayFromJournalTable($arrayData){
		$this->arrayMasterSetFromJournalTable = $arrayData;
	}
	//NEW[38]
	function getMasterTableArrayFromJournalTable(){
		//(1)IDColumn、がTableにAddされていない場合は「null」を返す。
		//(2)setMasterTableArrayFromMainTable、で、null、をセットしたとしても、通常は、配列を返す。
		if($this->arrayMasterSetFromJournalTable === null){
			$objTable = $this->getTable();
			if( is_null($objTable) === true ){
				//error_log("This column".$this->getID()." is not added in Table.");
				web_log("This column[".$this->getID()."] is not added in Table.");
			}else{
				//----フィルターテーブル用のデフォルト・データセットを作成
				$JournalTableBody = $objTable->getDBJournalTableBody();
				$strThisIdColumnId = $this->getID();
				$strDUColumnOfMainTable = $objTable->getRequiredDisuseColumnID();
				
				$refMasterKeyColumn = $this->getKeyColumnIDOfMaster();
				$refMasterDispColumn = $this->getDispColumnIDOfMaster();
				$refMasterTableBody = $this->getMasterTableBodyForFilter();
				$refMasterDUColumn = $this->getRequiredDisuseColumnID();
				$aryEtcetera = $this->getEtceteraParameter();

				//----マスターの全行のうち、メインテーブルで利用されている行のみに絞って、鍵カラムと表示カラム行、を取得する
				$this->arrayMasterSetFromJournalTable=createMasterTableDistinctArray($JournalTableBody, $strThisIdColumnId, $strDUColumnOfMainTable, $refMasterTableBody, $refMasterKeyColumn, $refMasterDispColumn, $refMasterDUColumn, $aryEtcetera);
				//マスターの全行のうち、メインテーブルで利用されている行のみに絞って、鍵カラムと表示カラム行、を取得する----

				if(is_array($this->arrayMasterSetFromJournalTable)===true && 0 < count($this->arrayMasterSetFromJournalTable)){
					//----正常に配列を取得できた
					//正常に配列を取得できた----
				}else{
					//$this->arrayMasterSetFromJournalTable = null;
				}
				//フィルターテーブル用のデフォルト・データセットを作成----
			}
		}
		return $this->arrayMasterSetFromJournalTable;
	}

	//NEW[39]
	function setMasterTableArrayForFilter($arrayData){
		$this->arrayMasterSetForFilter = $arrayData;
	}
	//NEW[40]
	function getMasterTableArrayForFilter($boolRefreshMode=false){
		if($this->arrayMasterSetForFilter === null || $boolRefreshMode === true){

			$masterTableBodyForFilter = $this->getMasterTableBodyForFilter();

			$strKeyColumnIDOfMaster = $this->getKeyColumnIDOfMaster();
			$strDispColumnIdOfMaster = $this->getDispColumnIDOfMaster();
			$aryEtcetera = $this->aryEtceteraParameter;

			$masterDisuseFlagColumnId = $this->getRequiredDisuseColumnID();
			if($masterTableBodyForFilter !== "" && $strKeyColumnIDOfMaster !== "" && $strDispColumnIdOfMaster !== ""){
				//----マスターでの廃止の有無を問わず、すべての行の鍵カラムと表示カラムのセットを取得する
				$this->arrayMasterSetForFilter = createMasterTableArrayForFilter($masterTableBodyForFilter, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterDisuseFlagColumnId, $aryEtcetera);
				//マスターでの廃止の有無を問わず、すべての行の鍵カラムと表示カラムのセットを取得する----
			}
		}

		if( $this->arrayMasterSetForFilter === null ){
			//error_log("Result of Columns[{$this->getID()}]->fx(getMasterTableArrayForFilter()) is null.");
			web_log("Result of Columns[{$this->getID()}]->fx(getMasterTableArrayForFilter()) is null.");
		}
		return $this->arrayMasterSetForFilter;
	}

	//NEW[41]
	function setMasterTableArrayForInput($arrayData){
		$this->arrayMasterSetForInput = $arrayData;
	}
	//NEW[42]
	function getMasterTableArrayForInput(){
		if($this->arrayMasterSetForInput === null){
			//----主として、新規登録、既存更新のセレクトタグ用のデフォルト・データセットを作成

			//----loadTable定義者が空白指定した場合も、通常のマスター名が代入される
			$masterTableBodyForInput = $this->getMasterTableBodyForInput();
			//loadTable定義者が空白指定した場合も、通常のマスター名が代入される----

			$strKeyColumnIDOfMaster = $this->getKeyColumnIDOfMaster();
			$strDispColumnIdOfMaster = $this->getDispColumnIDOfMaster();
			$aryEtcetera = $this->aryEtceteraParameter;

			$masterDisuseFlagColumnId = $this->getRequiredDisuseColumnID();

			if( $masterTableBodyForInput !== "" && $strKeyColumnIDOfMaster !== "" && $strDispColumnIdOfMaster !== "" ){
				//----マスターにおいて廃止されている行を除いて、マスターから、他の行の鍵カラムと表示カラムのセットを取得する
				$this->arrayMasterSetForInput = createMasterTableArrayForInput($masterTableBodyForInput, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterDisuseFlagColumnId, $aryEtcetera);
				//マスターにおいて廃止されている行を除いて、マスターから、他の行の鍵カラムと表示カラムのセットを取得する----

				if(is_array($this->arrayMasterSetForInput)===true && 0 < count($this->arrayMasterSetForInput)){
					//----正常に配列を取得できた
					//正常に配列を取得できた----
				}else{
					//$this->arrayMasterSetForInput = null;
				}
			}
			//主として、新規登録、既存更新のセレクトタグ用のデフォルト・データセットを作成----
		}
		return $this->arrayMasterSetForInput;
	}

	//NEW[43]
	function valueConvertFromSomeFile(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){

		$this->setTempBuffer(null);

		if($aryVariant["TABLE_IUD_SOURCE"]=="queryMaterialFile"){
			//----ファイル（エクセルorCSVでのアップデート受信）
			if(array_key_exists($this->getID(),$reqOrgData)===true){
				//----入力審査配列に、鍵が、存在していた場合

				$strRawDispValue = $reqOrgData[$this->getID()];
				//----バッファーへ生値をコピー
				$this->setTempBuffer($strRawDispValue);
				//バッファーへ生値をコピー----
				$keyValue = array_search($strRawDispValue, $this->getMasterTableArrayForFilter());
				if($keyValue === false){
					//----マスターDispにない値が入っていた場合
					$reqOrgData[$this->getID()] = "";
					//マスターDispにない値が入っていた場合----
				}else{
					//----マスターDispにある値が入っていた場合
					$reqOrgData[$this->getID()] = $keyValue;
					//マスターDispにある値が入っていた場合----
				}
				//----入力審査配列に、鍵が、存在していた場合----
			}
			//ファイル（エクセルorCSVでのアップデート受信）----
		}
	}

	//----タグ等の表示系制御
	//NEW[44]
	function setMultiple($strFormatterId, $boolValue){
		if( is_bool($boolValue) === true ){
			$this->aryFormatMultiple[$strFormatterId] = $boolValue;
		}
	}
	//NEW[45]
	function getMultiple($strFormatterId){
		$boolValue=null;
		if( array_key_exists($strFormatterId, $this->aryFormatMultiple) === true ){
			$boolValue = $this->aryFormatMultiple[$strFormatterId];
		}
		return $boolValue;
	}
	//NEW[46]
	function setErrMsgHead($strValue){
		$this->errMsgHead = $strValue;
	}
	//NEW[47]
	function getErrMsgHead(){
		return $this->errMsgHead;
	}
	//NEW[48]
	function setErrMsgTail($strValue){
		$this->errMsgTail = $strValue;
	}
	//NEW[49]
	function getErrMsgTail(){
		return $this->errMsgTail;
	}
	//タグ等の表示系制御----

	//----廃止予定(2以降)
	//NEW[50]
	function setJsFunction($jsFunction, $jsFunctionArgs=array()){
		$this->setEvent("update_table", "onchange", $jsFunction, $jsFunctionArgs);
		$this->setEvent("register_table", "onchange", $jsFunction, $jsFunctionArgs);
	}
	//廃止予定(2以降)----

	//ここまで新規メソッドの定義宣言処理

}

class EditStatusControlIDColumn extends IDColumn {
    protected $strEditTableBody;
    protected $strEditTableLockTgtColumnId;
    protected $strEditTableLockTgtSeqName;
    protected $strEditTableEditStatusColumnId;
    protected $strEditTableApplyUserColumnId;
    
    protected $strEditJnlTableBody;

    protected $strResultTableLockTgtColumnId;

    protected $strResultTableBody;
    protected $strResultTableId;
    protected $strResultTableHiddenId;
    protected $strResultTableRISeqId;

    protected $strResultJnlTableId;
    protected $strResultJnlTableHiddenId;
    protected $strResultJnlTableJnlSeqId;

    protected $pageMode;

    protected $varConfirmTableIUCheck; //実行用の内部作業用フラグ

	//----ここから継承メソッドの上書き処理

    function __construct($strColId, $strColLabel){
        $masterTableIdForFilter = 'DUMMY';
        $masterTableIdForInput = null;
        
        $strKeyColumnIDOfMaster ='FLAG';
        $strDispColumnIdOfMaster = 'NAME';
        
        parent::__construct($strColId, $strColLabel, $masterTableIdForFilter, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterTableIdForInput);

        $this->setHiddenMainTableColumn(true);
        $this->setResultTableRowIdentifySequenceID(null);
        $this->setResultJournalTableSequenceID(null);
        
        $this->setAllowSendFromFile(false);
        
        $this->setDeleteOffBeforeCheck(true); //廃止復活時にバリデーションチェック
        $this->varConfirmTableIUCheck = null; //実行用の内部作業用フラグ
    }

    //----AddColumnイベント系
    function initTable(TemplateTableForReview $table, $colNo){
        parent::initTable($table, $colNo);
    }
    //AddColumnイベント系----

    //----FixColumnイベント系
    function beforeFixColumn(){
    }

    function afterFixColumn(){
        $pageType = $this->objTable->getPageType();

        $this->setPageMode($pageType);

        $masterTableName = "(
                SELECT 4 FLAG, '{$this->objTable->getStatusNameOfNonsuited()}'      NAME, '0' DISUSE_FLAG FROM DUAL
                UNION
                SELECT 3 FLAG, '{$this->objTable->getStatusNameOfAccepted()}'       NAME, '0' DISUSE_FLAG FROM DUAL
                UNION
                SELECT 2 FLAG, '{$this->objTable->getStatusNameOfWaitForAccept()}'  NAME, '0' DISUSE_FLAG FROM DUAL
                UNION
                SELECT 1 FLAG, '{$this->objTable->getStatusNameOnEdit()}'           NAME, '0' DISUSE_FLAG FROM DUAL
                UNION
                SELECT 0 FLAG, '{$this->objTable->getStatusNameOfWithdrawned()}'    NAME, '0' DISUSE_FLAG FROM DUAL
                            )";

        $this->strEditTableBody               = $this->objTable->getDBMainTableBody();
        $this->strEditTableLockTgtColumnId    = $this->objTable->getLockTargetColumnID();
        $this->strEditTableEditStatusColumnId = $this->objTable->getEditStatusColumnID();
        $this->strEditTableApplyUserColumnId  = $this->objTable->getApplyUserColumnID();

        $this->strEditJnlTableBody            = $this->objTable->getDBJournalTableBody();

        $this->strResultTableLockTgtColumnId  = $this->objTable->getLockTargetColumnID();
        $this->strResultTableBody             = $this->objTable->getDBResultTableBody();
        $this->strResultTableId               = $this->objTable->getDBResultTableID();
        $this->strResultTableHiddenId         = $this->objTable->getDBResultTableHiddenID();

        $this->strResultJnlTableId            = $this->objTable->getDBResultJournalTableID();
        $this->strResultJnlTableHiddenId      = $this->objTable->getDBResultJournalTableHiddenID();

        $this->setValidator(new EditLockValidator($this, $this->objTable));
        
        $this->setMasterTableAgentQueryForFilter($masterTableName);
        $this->setMasterTableAgentQueryForInput($masterTableName);
        
        $this->setOutputType('register_table', new OutputType(new TabHFmt(), new StaticTextTabBFmt($this->objTable->getStatusNameOnEdit())));
        $this->setOutputType('update_table', new IDOutputType(new ReqTabHFmt(), new TextTabBFmt()));
        
        if($pageType=="apply"){
            //----エクセルでのアップロードのために
            $this->setArrayMasterTableByFormatName("register_table",array('1'=>$this->objTable->getStatusNameOnEdit()));
            $this->setArrayMasterTableByFormatName("update_table",array('0'=>$this->objTable->getStatusNameOfWithdrawned(),'1'=>$this->objTable->getStatusNameOnEdit(),'2'=>$this->objTable->getStatusNameOfWaitForAccept()));
            //エクセルでのアップロードのために----
            
        }else if($pageType=="confirm"){
            //----エクセルでのアップロードのために
            $this->setArrayMasterTableByFormatName("register_table",array());
            $this->setArrayMasterTableByFormatName("update_table",array('1'=>$this->objTable->getStatusNameOnEdit(),'2'=>$this->objTable->getStatusNameOfWaitForAccept(),'3'=>$this->objTable->getStatusNameOfAccepted(),'4'=>$this->objTable->getStatusNameOfNonsuited()));
            //エクセルでのアップロードのために----
        }
    }
    //FixColumnイベント系----

    //----TableIUDイベント系
	function getSequencesForTrzStart(&$arySequence=array()){
		//----トランザクション内
		global $g;
		$intControlDebugLevel01=250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		try{
			if( strlen($this->getResultTableRowIdentifySequenceID())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			if( strlen($this->getResultJournalTableSequenceID())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			$arySequence[$this->getResultTableRowIdentifySequenceID().'_'] = $this->getResultTableRowIdentifySequenceID();
			$arySequence[$this->getResultJournalTableSequenceID().'_'] = $this->getResultJournalTableSequenceID();
			$boolRet = true;
			//履歴シーケンスを捕まえる（デッドロック防止）----
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15001",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}

    function inTrzBeforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;
        $intControlDebugLevel01=250;

		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $retArray = parent::inTrzBeforeTableIUDAction($exeQueryData, $reqOrgData, $aryVariant);

        $this->varConfirmTableIUCheck = null;

        if( $retArray[0]===true ){
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);

            $boolRet = false;
            $intErrorType = null;
            $strErrMsg = "";

            $strEditStatusSaveId = $this->strEditTableEditStatusColumnId;
            $strLockTargetNoId = $this->strEditTableLockTgtColumnId;

            $boolSpecialUpdate = false;

            $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
            if( $modeValue=="DTUP_singleRecUpdate" ){
                $remainColKeys=array();
                $arrayObjColumn = $this->objTable->getColumns();
                if($this->getPageMode()=="apply"){
                    //----申請者ページの場合
                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                    
                    $rowEditTgt = $aryVariant['edit_target_row'];
                    
                    if($rowEditTgt[$strEditStatusSaveId]==1){
                        //----編集中の場合
                        
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        
                        if(array_key_exists($strEditStatusSaveId, $exeQueryData)===true){
                            if($exeQueryData[$strEditStatusSaveId] == 0){
                                //----申請取下
                                $boolSpecialUpdate = true;
                                //申請取下----
                            }else if($exeQueryData[$strEditStatusSaveId] == 2){
                                //----申請開始
                                $boolSpecialUpdate = true;
                                //申請開始----
                            }
                        }
                        //編集中の場合----
                    }else if($rowEditTgt[$strEditStatusSaveId] == 2){
                        //----申請中の場合
                        
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        
                        if(array_key_exists($strEditStatusSaveId, $exeQueryData)===true){
                            if($exeQueryData[$strEditStatusSaveId] == 0){
                                //----申請取下
                                $boolSpecialUpdate = true;
                                //申請取下----
                            }else if($exeQueryData[$strEditStatusSaveId]==1){
                                //----再編集
                                if(array_key_exists($this->objTable->getRequiredRowEditByFileColumnID(),$reqOrgData)===true){
                                    //----ファイルからのアップロード
                                    $strEditActionName = $reqOrgData[$this->objTable->getRequiredRowEditByFileColumnID()];
                                    if($strEditActionName==$this->objTable->getActionNameOfApplyEditRestart()){
                                        $boolSpecialUpdate = true;
                                    }else{
                                        $strErrMsg .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-14001", $strEditActionName);
                                    }
                                    //ファイルからのアップロード----
                                }else{
                                    //----ブラウザからのアップロード
                                    $boolSpecialUpdate = true;
                                    //ブラウザからのアップロード----
                                }
                                //再編集----
                            }else if($exeQueryData[$strEditStatusSaveId]==2){
                                $strErrMsg .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-14002");
                                $boolSpecialUpdate = false;
                            }
                        }
                        //申請中の場合----
                    }else{
                        //----その他の場合
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        //dev_log("現在のページからは、現在の編集ステータスは、変更できない状態、です。\n",$intControlDebugLevel01);
                        //その他の場合----
                    }
                    //申請者ページの場合----
                }else if($this->getPageMode()=="confirm"){
                    //----承認者ページの場合
                    $rowEditTgt = $aryVariant['edit_target_row'];
                    
                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                    
                    if($rowEditTgt[$strEditStatusSaveId]==2){
                        //----申請中の場合
                        
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        
                        if(array_key_exists($strEditStatusSaveId, $exeQueryData)===true){
                            if($exeQueryData[$strEditStatusSaveId]==1){
                                //----差戻の場合
                                $boolSpecialUpdate = true;
                                //差戻の場合----
                            }else if($exeQueryData[$strEditStatusSaveId]==3){
                                //----承認する場合
                                
                                if($rowEditTgt[$strLockTargetNoId]==""){
                                    //----新規申請だった場合
                                    
                                    $boolSpecialUpdate = true;
                                    
                                    //----指定のシーケンスから、自動採番する
                                    $retArray= getSequenceValue($this->getResultTableRowIdentifySequenceID(),true);
                                    //指定のシーケンスから、自動採番する----
                                    
                                    if( $retArray[1]=== 0 ){
                                        //----シーケンス取得成功
                                        $exeQueryData[$strLockTargetNoId] = $retArray[0];
                                        
                                        $this->varConfirmTableIUCheck = array('toStatus'=>3,'mode'=>'confirmInsert');
                                        
                                        //シーケンス取得成功----
                                    }else{
                                        //----シーケンス取得失敗
                                        $boolSpecialUpdate = false;
                                        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",$this->getResultTableRowIdentifySequenceID()));
                                        $strErrMsg .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3002",$this->getColLabel(true));
                                    }
                                    
                                    //新規申請だった場合----
                                }else{
                                    //----更新申請だった場合
                                    $exeQueryData[$strLockTargetNoId] = $rowEditTgt[$strLockTargetNoId];
                                    $boolSpecialUpdate = true;
                                    
                                    $this->varConfirmTableIUCheck = array('toStatus'=>3,'mode'=>'confirmUpdate');
                                    
                                    //更新申請だった場合----
                                }
                                //承認する場合----
                            }else if($exeQueryData[$strEditStatusSaveId]==4){
                                //----却下の場合
                                $boolSpecialUpdate = true;
                                //却下の場合----
                            }
                        }
                        //申請中の場合----
                    }else{
                        //----申請中ではない場合
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        //申請中ではない場合----
                    }
                    //承認者ページの場合----
                }
                
                if($boolSpecialUpdate===true){
                    //----特定のカラムを除いて、更新させない
                    foreach($arrayObjColumn as $objColumn){
                        if(is_a($objColumn,'AutoUpdateTimeColumn')===true){
                            if($objColumn->getUpdateMode()===true){
                                $remainColKeys[] = $objColumn->getID();
                            }
                        }else if(is_a($objColumn,'AutoUpdateUserColumn')===true){
                            if($objColumn->getUpdateMode()===true){
                                $remainColKeys[] = $objColumn->getID();
                            }
                        }
                    }
                    $remainColKeys[] = $this->objTable->getRIColumnID();
                    $remainColKeys[] = $strEditStatusSaveId;
                    $remainColKeys[] = $strLockTargetNoId;
                    $remainColKeys[] = $this->objTable->getRequiredUpdateDate4UColumnID();
                    
                    $remainColKeys[] = $this->objTable->getRequiredJnlSeqNoColumnID();
                    $remainColKeys[] = $this->objTable->getRequiredJnlRegTimeColumnID();
                    $remainColKeys[] = $this->objTable->getRequiredJnlRegClassColumnID();
                    
                    $intDiffColumn = 0;
                    $arrayDiffColumnName = array();
                    
                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                    foreach($exeQueryData as $key=>$value){
                        if( in_array($key, $remainColKeys) === true ){
                            if( is_array($value) === true ){
                                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                            }else{
                                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                            }
                        }else{
                            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                            if($arrayObjColumn[$key]->compareRow($exeQueryData, $reqOrgData, $aryVariant)===true){
                                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                                unset($exeQueryData[$key]);
                            }else{
                                $intDiffColumn += 1;
                                $arrayDiffColumnName[] = $arrayObjColumn[$key]->getColLabel(true);
                            }
                            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                        }
                    }
                    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                    
                    
                    if(1 <= $intDiffColumn){
                        $intErrorType = 2;
                        $strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-14004",$this->getColLabel());
                        $strErrMsg.="(".implode(",",$arrayDiffColumnName).")\n";
                    }
                    //特定のカラムを除いて、更新させない----
                }
            }else if( $modeValue=="DTUP_singleRecRegister" ){
                if($this->getPageMode()=="apply"){
                    //----申請者ページの場合
                    $exeQueryData[$strEditStatusSaveId] = 1;
                    //申請者ページの場合----
                }
            }else if( $modeValue=="DTUP_singleRecDelete" ){
                $this->varConfirmTableIUCheck = array('toStatus'=>false);
            }
            if( $strErrMsg == "" ){
                $boolRet = true;
            }
            $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
    }
    
    function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        //----トランザクション内
        global $g;
        $intControlDebugLevel01=250;
        
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

        $strFxName = __CLASS__."::".__FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $boolRet = false;
        $intErrorType = 0;
        $strErrMsg = "";
        try{
            if( $this->varConfirmTableIUCheck!==null ){
                $mainSql = "";
                if( $this->varConfirmTableIUCheck['toStatus']===3 ){
                    //----承認アクションの場合、結果テーブルに反映させる。
                    $toResultExeQueryData = generateElementFromEditTargetRow($exeQueryData
                                                                             ,$aryVariant['edit_target_row']
                                                                             ,$this->objTable->getColumns()
                                                                             ,$this->objTable->getRequiredUpdateDate4UColumnID()
                                                                             ,$this->strResultTableHiddenId);
                    if( $this->varConfirmTableIUCheck['mode']=='confirmInsert' ){
                        // ----INSERT命令を組み立てる
                        list($mainSql,$aryDataForBind)=generateRegisterSQL($toResultExeQueryData
                                                                       ,$this->objTable->getColumns()
                                                                       ,$this->strResultTableId
                                                                       ,$this->strResultTableHiddenId);
                        $toResultExeQueryData[$this->objTable->getRequiredJnlRegClassColumnID()] = array('JNL'=>'INSERT');
                        // INSERT命令を組み立てる----
                    }else if( $this->varConfirmTableIUCheck['mode']=='confirmUpdate' ){
                        // ----UPDATE命令を組み立てる
                        // ロックNoのカラムを主キー役にさせる
                        list($mainSql,$aryDataForBind)=generateUpdateSQL($toResultExeQueryData
                                                                     ,$this->objTable->getColumns()
                                                                     ,$this->strResultTableLockTgtColumnId
                                                                     ,$this->strResultTableId
                                                                     ,$this->strResultTableHiddenId);
                        // UPDATE命令を組み立てる----
                    }else{
                        $intErrorType = 500;
                        throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                    }
                }else if( $this->varConfirmTableIUCheck['toStatus']===false ){
                    //----廃止/復活で特定条件が満たされた場合は、結果テーブルに反映させる。
                    
                    //----廃止/復活の場合、承認済レコードの場合は、最大の申請番号かどうか、を調べる。
                    $strLockTargetNoId = $this->strEditTableLockTgtColumnId;
                    
                    $varLockTargetNo = $aryVariant['edit_target_row'][$strLockTargetNoId];
                    if( 0 < strlen($varLockTargetNo) ){
                        //----承認番号がある
                        
                        $varApplyNo = $exeQueryData[$this->objTable->getRIColumnID()];
                        
                        //----申請テーブル履歴から、該当の承認番号の、承認済の廃止されていないレコード(1)を抽出する。
                        //----(1)の中で、各申請番号ごとに、最初に承認されたレコード(2)を検出する。
                        //----(2)の中で、更新日時が最後のものが、最新の承認済レコードが、承認された時のレコードである。
                        
                        $arrayObjColumn = $this->objTable->getColumns();
                        $strLUTColumnId = $this->objTable->getRequiredLastUpdateDateColumnID();
                        
                        $strRIColumnId  = $this->objTable->getRIColumnID();
                        
                        $objLUTColumn   = $arrayObjColumn[$strLUTColumnId];
                        
                        $strLUTColumnOfSelectZone = $objLUTColumn->getPartSqlInSelectZone();
                        
                        //----編集テーブル履歴から、廃止されていない、承認済レコードを抽出する
                        $checkSql = "SELECT {$this->objTable->getRequiredJnlSeqNoColumnID()} , {$strRIColumnId}, {$strLUTColumnOfSelectZone} "
                                   ." FROM {$this->strEditJnlTableBody} {$this->objTable->getShareTableAlias()} "
                                   ." WHERE {$this->strEditTableLockTgtColumnId} = :{$this->strEditTableLockTgtColumnId} "
                                   ." AND {$this->getID()} IN (3) "
                                   ." AND {$this->objTable->getRequiredDisuseColumnID()} IN ('0') ";
                        //編集テーブル履歴から、廃止されていない、承認済レコードを抽出する----

                        $aryDataForBind = array();
                        $aryDataForBind[$this->strEditTableLockTgtColumnId] = $varLockTargetNo;
                        $retArray = singleSQLExecuteAgent($checkSql, $aryDataForBind, $strFxName);
                        if( $retArray[0]===false ){
                            $intErrorType = 500;
                            throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                        }
                        
                        $objQuery =& $retArray[1];
                        $aryFirstRowPerRI = array();
                        //----承認されたレコードから、各申請番号ごとに、もっとも最初のレコードを抽出する
                        while( $row = $objQuery->resultFetch() ){
                            if( strlen($row[$strLUTColumnId])==0 ){
                                //----最終更新日時がなかった
                                $intErrorType = 500;
                                throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                                //最終更新日時がなかった----
                            }
                            if( array_key_exists($row[$strRIColumnId], $aryFirstRowPerRI)===false ){
                                // 申請番号=>最終更新日時（YYYY/MM/DD HH:II:SS整数秒）の組み合わせ
                                $aryFirstRowPerRI[$row[$strRIColumnId]] = $row[$strLUTColumnId];
                            }else{
                                //----フォーカスされたレコードの最終更新日時と、発見済レコードのうち、同じ申請番号と同じレコードの最終更新日時と比較
                                if( strtotime($row[$strLUTColumnId]) < strtotime($aryFirstRowPerRI[$row[$strRIColumnId]]) ){
                                    //----フォーカスされたレコードのほうが、早い時刻だった
                                    // 申請番号=>最終更新日時（YYYY/MM/DD HH:II:SS整数秒）の組み合わせ
                                    $aryFirstRowPerRI[$row[$strRIColumnId]] = $row[$strLUTColumnId];
                                    //フォーカスされたレコードのほうが、早い時刻だった----
                                }
                                //フォーカスされたレコードの最終更新日時と、発見済レコードのうち、同じ申請番号と同じレコードの最終更新日時と比較----
                            }
                        }
                        unset($objQuery);
                        //承認されたレコードから、各申請番号ごとに、もっとも最初のレコードを抽出する----
                        if( 0 < count($aryFirstRowPerRI) ){
                            $strLastTime = null;
                            $strLastAccepted = null;
                            $intLoopCount = 0;
                            foreach( $aryFirstRowPerRI as $key=>$value ){
                                if( $intLoopCount==0 ){
                                    //----1回目の要素は、比較値なので、無条件に代入
                                    $strLastAccepted = $key;
                                    $strLastTime = $value;
                                    //1回目の要素は、比較値なので、無条件に代入----
                                }else{
                                    //----最後に承認されたレコードかを判定する
                                    if( strtotime($strLastTime) < strtotime($value) ){
                                        $strLastAccepted = $key;
                                        $strLastTime = $value;
                                    }
                                    //最後に承認されたレコードかを判定する----
                                }
                                $intLoopCount += 1;
                            }

                            if( $strLastAccepted==$varApplyNo ){
                                //----ロックNoのカラムを主キー役にさせる
                                $toResultExeQueryData = $exeQueryData;
                                $toResultExeQueryData[$this->strEditTableLockTgtColumnId] = $varLockTargetNo;
                                list($mainSql,$aryDataForBind)=generateUpdateSQL($toResultExeQueryData, $this->objTable->getColumns(), $this->strResultTableLockTgtColumnId, $this->strResultTableId, $this->strResultTableHiddenId);
                                //ロックNoのカラムを主キー役にさせる----
                            }
                        }
                        //----承認番号がある
                    }
                    //廃止/復活で特定条件が満たされた場合は、結果テーブルに反映させる。----
                }else{
                    $intErrorType = 500;
                    throw new Exception( '00000400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }

                if( 0 < strlen($mainSql) ){
                    $retArray = singleSQLExecuteAgent($mainSql, $aryDataForBind, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $resultRowLength = $objQuery->effectedRowCount();
                        unset($objQuery);
                        if($resultRowLength == 1){
                        }else{
                            $intErrorType = 500;
                            throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                        }                                                            
                    }else{
                        $intErrorType = 500;
                        throw new Exception( '00000600-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                    }
                    
                    $exeJournalData = generateElementForJournalReg($toResultExeQueryData
                                                                   ,$aryVariant['edit_target_row']
                                                                   ,$this->objTable->getColumns()
                                                                   ,$this->objTable->getRequiredUpdateDate4UColumnID()
                                                                   ,$this->strResultTableHiddenId);

                    $retArray= getSequenceValue($this->strResultJnlTableJnlSeqId,true);
                    
                    if( $retArray[1] === 0 ){
                        $exeJournalData[$this->objTable->getRequiredJnlSeqNoColumnID()] = $retArray[0];
                        $boolRet = true;
                    }else{
                        $intErrorType = 500;
                        throw new Exception( '00000700-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                    }

                    $sqlJnlBody = generateJournalRegisterSQL($exeJournalData
                                                             ,$this->objTable->getColumns()
                                                             ,$this->strResultJnlTableId
                                                             ,$this->strResultJnlTableHiddenId );
                
                    $retArray = singleSQLExecuteAgent($sqlJnlBody, $exeJournalData, $strFxName);
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $resultRowLength = $objQuery->effectedRowCount();
                        unset($objQuery);
                        if($resultRowLength == 1){
                        }else{
                            $intErrorType = 500;
                            throw new Exception( '00000800-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                        }                                                            
                    }else{
                        $intErrorType = 500;
                        throw new Exception( '00000900-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                    }
                }
                $boolRet = true;
            }else{
                $boolRet = true;
            }
        }
        catch(Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

            $strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15004",$this->getColLabel(true));

            $boolRet = false;
        }
        //----念のために初期化
        $this->varConfirmTableIUCheck = null;
        //念のために初期化----
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retArray;
        //トランザクション内----
    }

    //TableIUDイベント系----

    //ここまで継承メソッドの上書き処理----

    //----ここから新規メソッドの定義宣言処理

    //NEW[1]
    function setPageMode($intValue){
        $this->pageMode = $intValue;
    }
    //NEW[2]
    function getPageMode(){
        return $this->pageMode;
    }
	//NEW[3]
    function setResultTableRowIdentifySequenceID($intValue){
        $this->strResultTableRISeqId = $intValue;
    }
   	//NEW[4]
    function getResultTableRowIdentifySequenceID(){
        return $this->strResultTableRISeqId;
    }
	//NEW[5]
    function setResultJournalTableSequenceID($intValue){
        $this->strResultJnlTableJnlSeqId = $intValue;
    }
   	//NEW[6]
    function getResultJournalTableSequenceID(){
        return $this->strResultJnlTableJnlSeqId;
    }
	//ここまで新規メソッドの定義宣言処理----

}

class MultiSelectSaveColumn extends IDColumn {

	// ----外部保存
	protected $strTableIdOfLinkUtn;
	protected $strSeqIdOfLinkUtn;

	protected $strTableIdOfLinkJnl;
	protected $strSeqIdOfLinkJnl;
	protected $strJSNColumnIdOfLink;
	protected $strJRCColumnIdOfLink;
	protected $strJRTColumnIdOfLink;

	protected $strRIColumnIdOfLink;
	protected $strDisuseColumnIdOfLink;
	protected $strLUTSColumnIdOfLink;
	protected $strLUUColumnIdOfLink;
	protected $strMasterKeyColumnIdOfLink;
	protected $strAnchorColumnIdOfLink;

	protected $arrayOtherColumnType;
	// 外部保存----

	protected $aryBufferUIDReq; //リクエストを一時的に格納する「キーは意味なし。値はマスターの値」

	protected $strMaxLUSTFUFromReq; //リクエストを一時的に格納「最大の最終更新日時」

	protected $arrayLinkTableConf;
	protected $arrayLinkTableValue;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $masterTableIdForFilter, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterTableIdForInput="", $aryEtcetera=array()){
		parent::__construct($strColId, $strColLabel, $masterTableIdForFilter, $strKeyColumnIDOfMaster, $strDispColumnIdOfMaster, $masterTableIdForInput="", $aryEtcetera);
		$this->setDBColumn(false);
		$this->allowSendFromFile = false;

		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("filter_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("print_journal_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("delete_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new SelectTabBFmt());
		$this->setOutputType("register_table", $outputType);
		$this->setMultiple("register_table",true);
		$outputType = new OutputType(new ReqTabHFmt(), new SelectTabBFmt());
		$this->setOutputType("update_table", $outputType);
		$this->setMultiple("update_table",true);
		$outputType = new OutputType(new ExcelHFmt(), new StaticBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("excel", $outputType);
		$outputType = new OutputType(new CSVHFmt(), new StaticCSVBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("csv", $outputType);
		$outputType = new OutputType(new JSONHFmt(), new StaticBFmt(""));
		$this->setOutputType("json", $outputType);
		$this->setMultiple("register_table", true);
		$this->setMultiple("update_table", true);

		$strMaxLUSTFUFromLink = null;
		$this->arrayOtherColumnType = null;
		
		$this->arrayLinkTableConf = array();
		$this->arrayLinkTableValue = array();
	}

	function afterFixColumn(){
		//----履歴
		if( $this->strJSNColumnIdOfLink === null ){
			$this->strJSNColumnIdOfLink = $this->objTable->getRequiredJnlSeqNoColumnID();
		}
		if( $this->strJRCColumnIdOfLink === null ){
			$this->strJRCColumnIdOfLink = $this->objTable->getRequiredJnlSeqNoColumnID();
		}
		if( $this->strJRTColumnIdOfLink === null ){
			$this->strJRTColumnIdOfLink = $this->objTable->getRequiredJnlRegTimeColumnID();
		}
		//履歴----

		if( $this->strRIColumnIdOfLink === null ){
			$this->strRIColumnIdOfLink = "LINK_ID";
		}

		if( $this->strDisuseColumnIdOfLink === null ){
			$this->strDisuseColumnIdOfLink = $this->objTable->getRequiredDisuseColumnID();
		}
		if( $this->strLUTSColumnIdOfLink === null ){
			$this->strLUTSColumnIdOfLink = $this->objTable->getRequiredLastUpdateDateColumnID();
		}
		if( $this->strLUUColumnIdOfLink === null ){
			$this->strLUUColumnIdOfLink = $this->objTable->getRequiredLastUpdateUserColumnID();
		}
		if( $this->strAnchorColumnIdOfLink === null ){
			$this->strAnchorColumnIdOfLink = $this->objTable->getRIColumnID();
		}
		if( $this->strMasterKeyColumnIdOfLink === null ){
			$this->strMasterKeyColumnIdOfLink = $this->getKeyColumnIDOfMaster();
		}

		if( $this->arrayOtherColumnType === null ){
			$this->arrayOtherColumnType = array("NOTE"=>"");
		}

		$this->arrayLinkTableConf = array(
			$this->getJSNColumnColumnIDOfLink()=>"",
			$this->getJRCColumnColumnIDOfLink()=>"",
			$this->getJRTColumnColumnIDOfLink()=>"",
			$this->getRIColumnIDOfLink()=>"",
			$this->getAnchorColumnIDOfLink()=>"",
			$this->getMasterKeyColumnIDOfLink()=>"",
			$this->getDisuseColumnIDOfLink()=>"",
			$this->getLUTSColumnIDOfLink()=>"",
			$this->getLUUColumnIDOfLink()=>""
		);
		foreach($this->arrayOtherColumnType as $key=>$type){
			$this->arrayLinkTableConf[$key] = $type;
		}

		$this->arrayLinkTableValue = array(
			$this->getJSNColumnColumnIDOfLink()=>"",
			$this->getJRCColumnColumnIDOfLink()=>"",
			$this->getJRTColumnColumnIDOfLink()=>"",
			$this->getRIColumnIDOfLink()=>"",
			$this->getAnchorColumnIDOfLink()=>"",
			$this->getMasterKeyColumnIDOfLink()=>"",
			$this->getDisuseColumnIDOfLink()=>"",
			$this->getLUTSColumnIDOfLink()=>"",
			$this->getLUUColumnIDOfLink()=>""
		);
		foreach($this->arrayOtherColumnType as $key=>$type){
			$this->arrayLinkTableValue[$key] = "";
		}

		return true;
	}

	function getSequencesForTrzStart(&$arySequence=array()){
		//----トランザクション内
		global $g;
		$intControlDebugLevel01=250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		try{
			if( strlen($this->getSeqIDOfLinkUtn())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			if( strlen($this->getSeqIDOfLinkJnl())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			$arySequence[$this->getSeqIDOfLinkUtn().'_'] = $this->getSeqIDOfLinkUtn();
			$arySequence[$this->getSeqIDOfLinkJnl().'_'] = $this->getSeqIDOfLinkJnl();
			$boolRet = true;
			//履歴シーケンスを捕まえる（デッドロック防止）----
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15001",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}

	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;
		$intControlDebugLevel01=250;
		
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		try{
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
				$this->aryBufferUIDReq = array();
				$this->strMaxLUSTFUFromReq = "";

				if( array_key_exists($this->getID(), $reqOrgData ) === true ){
					//----何等かの項目が選択された場合
					$tmpArySelected = array();
					$tmpValueEsc = $reqOrgData[$this->getID()];
					unset($reqOrgData[$this->getID()]);
					if( is_array($tmpValueEsc) === false){
						if(is_string($tmpValueEsc)===true){
							//----1個の選択肢が選ばれた場合
							//----空白の選択肢が選ばれた場合も、ここを通る
							if( $tmpValueEsc == "" ){
							}else{
								$tmpArySelected[0] = $tmpValueEsc;
							}
							//空白の選択肢が選ばれた場合も、ここを通る----
							//1個の選択肢が選ばれた場合----
						}else{
							//----不正な入力と推定
							$tmpArySelected = false;
							//不正な入力と推定----
						}
					}else{
						//----複数の選択肢が選ばれた場合
						foreach($tmpValueEsc as $tmpKey=>$tmpValue){
							if(is_string($tmpValue)===true){
								//----文字列の場合
								if( 0<strlen($tmpValue) ){
									//----空白ではない選択肢の場合
									$tmpArySelected[] = $tmpValue;
									//空白ではない選択肢の場合----
								}
								//文字列の場合----
							}else{
								//----不正な入力と推定
								$tmpArySelected = false;
								//不正な入力と推定----
							}
						}
						//複数の選択肢が選ばれた場合----
					}
					unset($tmpValueEsc);
					$this->aryBufferUIDReq = $tmpArySelected;
					//何等かの項目が選択された場合----
				}else{
					//----空白の選択肢すらも、何も選ばれなかった場合
					//空白の選択肢すらも、何も選ばれなかった場合----
				}

				//----追い越し判定値の格納
				$strColId = $this->getID();
				$strColMark = $strColId;
				if( $this->getColumnIDHidden() === true ){
					$strColMark = $this->getIDSOP();
				}
				$this->strMaxLUSTFUFromReq = $reqOrgData["tmp_mlustf_".$strColMark];
				unset($reqOrgData["tmp_mlustf_".$strColMark]);
				//追い越し判定値の格納----

			}else if( $modeValue=="DTUP_singleRecDelete" ){
				//----追い越し判定値の格納
				$this->aryBufferUIDReq = array();
				$this->strMaxLUSTFUFromReq = "";
				//追い越し判定値の格納----
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15001",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}

	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----ポリシー：原則として、$exeQueryDataにキーを追加、または、削除、のみを行う。返し値は文字列型。
		global $g;
		$boolRet = true;
		$intErrorType = null;
		$strErrMsg = "";
		$strErrorBuf = "";
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
			$arrayReqData = $this->aryBufferUIDReq;
			$objMultiValidator = $this->getValidator();
			$intDummyRIN=null;
			if( $arrayReqData===false ){
				// 予備-＞不正な値が入力されました
				$intErrorType = 2;
				//$ret .= $this->getColLabel(true).":{$g['objMTS']->getSomeMessage("ITAWDCH-ERR-15002")}\n";
				$strErrMsg .= $this->getColLabel(true).":{$g['objMTS']->getSomeMessage("ITAWDCH-ERR-15002")}\n";
			}else{
				foreach($arrayReqData as $key=>$value){
					//----バリデーターチェック
					if($objMultiValidator->isValid($value, $intDummyRIN, $reqOrgData, $aryVariant)===true){
					}else{
						$intErrorType = 2;
						$arrayRule=$objMultiValidator->getValidRule();
						$arrayPrefix=$objMultiValidator->getShowPrefixs();
						$intColumnErrSeq=0;
						foreach($arrayRule as $data){
							$intColumnErrSeq+=1;
							if($arrayPrefix[$intColumnErrSeq - 1]!==false){
								$strErrMsg .= $this->getColLabel(true).":{$data}\n";
							}else{
								$strErrMsg .= "{$data}\n";
							}
						}
					}
					//バリデーターチェック----
				}
			}
			//追い越し判定値があるかをチェック----
		}
		$retArray = array($boolRet,$intErrorType,$strErrMsg,$strErrorBuf);
		return $retArray;
		//ポリシー：原則として、$exeQueryDataにキーを追加、または、削除、のみを行う。返し値は文字列型。----
	}

	function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----トランザクション内
		global $g;
		$intControlDebugLevel01=250;
		
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		try{
			$boolExecuteTrigger = false;

			$db_model_ch = $g['db_model_ch'];

			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecDelete" ){
				$intAnchorValue = $exeQueryData[$this->objTable->getRIColumnID()];
				if( $exeQueryData[$this->objTable->getRequiredDisuseColumnID()] == '1' ){
					//----この行が廃止された場合
					$boolExecuteTrigger = true;
					//この行が廃止された場合----
				}else{
					$boolRet = true;
				}
			}
			else if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
				$intAnchorValue = $exeQueryData[$this->objTable->getRIColumnID()];
				$strMaxLUDValue = getDataFromLinkTable($this, $intAnchorValue, 0);
				if( $this->strMaxLUSTFUFromReq != $strMaxLUDValue ){
					//｛｝:リンク保存テーブルが、追い越し更新されました。
					$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15003",$this->getColLabel(true));
					$intErrorType = 500;
				}else{
					$boolExecuteTrigger = true;
					if( $aryVariant["TABLE_IUD_SOURCE"] == "queryMaterialFile" ){
						//----ファイルからの登録・更新の場合は、処理をスキップする
						$boolExecuteTrigger = false;
						$boolRet = true;
						//ファイルからの登録・更新の場合は、処理をスキップする----
					}
				}
			}

			if( $boolExecuteTrigger === true ){
				// ----テーブルのコンフィグ設定
				$arrayConfig = $this->arrayLinkTableConf;
				// テーブルのコンフィグ設定----

				// ----値の器を用意
				$arrayValue = $this->arrayLinkTableValue;
				// 値の器を用意----

				$arrayForInput = $this->getMasterTableArrayForInput();
				$arrayKeys = array_keys($arrayForInput);

				$strEscaped = addslashes(implode(",",$arrayKeys));

				$strWhere  = " {$this->strTableIdOfLinkUtn}.{$this->getAnchorColumnIDOfLink()} = :{$this->getAnchorColumnIDOfLink()} ";
				$strWhere .= " AND {$this->strTableIdOfLinkUtn}.{$this->getMasterKeyColumnIDOfLink()} IN (".$strEscaped.") ";
				$strWhere .= " AND {$this->strTableIdOfLinkUtn}.{$this->getDisuseColumnIDOfLink()} IN ('0', '1') ";

				$temp_array = array('WHERE'=>$strWhere);

				$retArray = makeSQLForUtnTableUpdate($db_model_ch,
													 "SELECT FOR UPDATE",
													 $this->getRIColumnIDOfLink(),
													 $this->getTableIDOfLinkUtn(),
													 $this->getTableIDOfLinkJnl(),
													 $arrayConfig,
													 $arrayValue,
													 $temp_array );
				if( $retArray === false ){
					$intErrorType = 500;
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}

				$sqlUtnBody = $retArray[1];
				$arrayUtnBind = $retArray[2];
				
				$arrayUtnBind[$this->getAnchorColumnIDOfLink()] = $exeQueryData[$this->objTable->getRIColumnID()];
				
				$retSQLResultArray = singleSQLExecuteAgent($sqlUtnBody, $arrayUtnBind, $strFxName);
				if( $retSQLResultArray[0] === true ){
					$objQueryUtn =& $retSQLResultArray[1];
					
					$arrayRowBody = array();
					$arrayRowIndex = array();
					// ----リンクテーブルにある関連行を、格納する。
					while ( $row = $objQueryUtn->resultFetch() ) {
						$strMasterValue = $row[$this->getMasterKeyColumnIDOfLink()];

						$arrayRowBody[$strMasterValue] = $row;
					}
					// リンクテーブルにある関連行を、格納する。----
					
					$arrayReqData = $this->aryBufferUIDReq;
					
					unset($objQueryUtn);
				}
				else{
					$intErrorType = 500;
					throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}

				//dev_log("選択された項目の処理を開始します", $intControlDebugLevel01);
				// ---リストで選ばれたもの
				foreach( $arrayReqData as $intQuerySeq=>$selectedValue){

					if( array_key_exists($selectedValue, $arrayRowBody) === true ){

						$updateRow = $arrayRowBody[$selectedValue];
						
						if( $updateRow[$this->getDisuseColumnIDOfLink()] == "0" ){
							// ----廃止されていないので、なにもしない
							// 廃止されていないので、なにもしない----
						}
						else if( $updateRow[$this->getDisuseColumnIDOfLink()] == "1" ){
							// ----廃止されているので、復活させる
							$retArray = getSequenceValue($this->getSeqIDOfLinkJnl(), true, false );
							if( $retArray[1] != 0 ){
								$intErrorType = 500;
								web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName, $this->getSeqIDOfLinkJnl())));
								throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
							else{
								$strJnlSeq = $retArray[0];
							}

							$updateRow[$this->getJSNColumnColumnIDOfLink()] = $strJnlSeq;
							$updateRow[$this->getDisuseColumnIDOfLink()]    = "0";
							$updateRow[$this->getLUUColumnIDOfLink()]       = $g['login_id'];

							$retArray = makeSQLForUtnTableUpdate($db_model_ch,
								"UPDATE",
								$this->getRIColumnIDOfLink(),
								$this->getTableIDOfLinkUtn(),
								$this->getTableIDOfLinkJnl(),
								$arrayConfig,
								$updateRow );
								
							if($retArray === false){
								$intErrorType = 500;
								throw new Exception( '00000400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}

							$sqlUtnBody = $retArray[1];
							$arrayUtnBind = $retArray[2];

							$sqlJnlBody = $retArray[3];
							$arrayJnlBind = $retArray[4];

							$retUtnSQLResultArray = singleSQLExecuteAgent($sqlUtnBody, $arrayUtnBind, $strFxName);
							if( $retUtnSQLResultArray[0] === true ){
								unset($retUtnSQLResultArray);
							}
							else{
								$intErrorType = 500;
								//$strErrMsg = "UTNテーブル更新エラーです";
								throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
							
							$retJnlSQLResultArray = singleSQLExecuteAgent($sqlJnlBody, $arrayJnlBind, $strFxName);
							if( $retJnlSQLResultArray[0] === true ){
								unset($retJnlSQLResultArray);
							}
							else{
								$intErrorType = 500;
								//$strErrMsg = "JNLテーブル更新エラーです";
								throw new Exception( '00000600-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
							// 廃止されているので、復活させる----
						}
						//-＞テーブルにもあった
					}else{
						//----テーブルにはなかった
						//挿入
						$retArray = getSequenceValue($this->getSeqIDOfLinkUtn(), true, false );
						if( $retArray[1] != 0 ){
							$intErrorType = 500;
							web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSeqIDOfLinkUtn())));
							$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15010");
						}
						else{
							$strUtnSeq = $retArray[0];
						}

						$retArray = getSequenceValue($this->getSeqIDOfLinkJnl(), true, false );
						if( $retArray[1] != 0 ){
							$intErrorType = 500;
							web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSeqIDOfLinkJnl())));
							throw new Exception( '00000700-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
						else{
							$strJnlSeq = $retArray[0];
						}

						$incertRow = $this->arrayLinkTableValue;

						$incertRow[$this->getJSNColumnColumnIDOfLink()] = $strJnlSeq;
						$incertRow[$this->getRIColumnIDOfLink()]        = $strUtnSeq;

						$incertRow[$this->getAnchorColumnIDOfLink()]    = $exeQueryData[$this->objTable->getRIColumnID()];
						$incertRow[$this->getMasterKeyColumnIDOfLink()] = $selectedValue;

						$incertRow[$this->getDisuseColumnIDOfLink()]    = "0";
						
						$incertRow[$this->getLUUColumnIDOfLink()]       = $g['login_id'];

						$retArray = makeSQLForUtnTableUpdate($db_model_ch,
							"INSERT",
							$this->getRIColumnIDOfLink(),
							$this->getTableIDOfLinkUtn(),
							$this->getTableIDOfLinkJnl(),
							$arrayConfig,
							$incertRow );

						if($retArray === false){
							$intErrorType = 500;
							throw new Exception( '00000800-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
						
						$sqlUtnBody = $retArray[1];
						$arrayUtnBind = $retArray[2];

						$sqlJnlBody = $retArray[3];
						$arrayJnlBind = $retArray[4];

						$retUtnSQLResultArray = singleSQLExecuteAgent($sqlUtnBody, $arrayUtnBind, $strFxName);
						if( $retUtnSQLResultArray[0] === true ){
							unset($retUtnSQLResultArray);
						}
						else{
							$intErrorType = 500;
							throw new Exception( '00000900-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
						
						$retJnlSQLResultArray = singleSQLExecuteAgent($sqlJnlBody, $arrayJnlBind, $strFxName);
						if( $retJnlSQLResultArray[0] === true ){
							unset($retJnlSQLResultArray);
						}
						else{
							$intErrorType = 500;
							throw new Exception( '00001000-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
						//テーブルにはなかった----
					} 
				}
				// リストで選ばれたもの----

				// ---- すでにテーブルにあるもの
				foreach( $arrayRowBody as $existsValue=>$updateRow ){
					if( in_array($existsValue, $arrayReqData) === true ){
						// ----テーブルにもあり、今回も選ばれたもの
						//何もしない。
						// テーブルにもあり、今回も選ばれたもの----
					}else{
						// ---テーブルにはあるが、選ばれれなかったもの
						
						if( $updateRow[$this->getDisuseColumnIDOfLink()] == "0" ){
							// ----廃止されていないので、廃止する
							$retArray = getSequenceValue($this->getSeqIDOfLinkJnl(), true, false );
							if( $retArray[1] != 0 ){
								$intErrorType = 500;
								web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSeqIDOfLinkJnl())));
								throw new Exception( '00001100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
							else{
								$strJnlSeq = $retArray[0];
							}

							$updateRow[$this->getJSNColumnColumnIDOfLink()] = $strJnlSeq;
							$updateRow[$this->getDisuseColumnIDOfLink()]    = "1";
							$updateRow[$this->getLUUColumnIDOfLink()]       = $g['login_id'];

							$retArray = makeSQLForUtnTableUpdate($db_model_ch,
								"UPDATE",
								$this->getRIColumnIDOfLink(),
								$this->getTableIDOfLinkUtn(),
								$this->getTableIDOfLinkJnl(),
								$arrayConfig,
								$updateRow );

							if($retArray === false){
								$intErrorType = 500;
								throw new Exception( '00001200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}

							$sqlUtnBody = $retArray[1];
							$arrayUtnBind = $retArray[2];

							$sqlJnlBody = $retArray[3];
							$arrayJnlBind = $retArray[4];
							$retUtnSQLResultArray = singleSQLExecuteAgent($sqlUtnBody, $arrayUtnBind, $strFxName);
							if( $retUtnSQLResultArray[0] === true ){
								unset($retUtnSQLResultArray);
							}
							else{
								$intErrorType = 500;
								throw new Exception( '00001300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}

							$retJnlSQLResultArray = singleSQLExecuteAgent($sqlJnlBody, $arrayJnlBind, $strFxName);
							if( $retJnlSQLResultArray[0] === true ){
								unset($retJnlSQLResultArray);
							}
							else{
								$intErrorType = 500;
								throw new Exception( '00001400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
							// 廃止されていないので、廃止する----
						}
						else if( $updateRow[$this->getDisuseColumnIDOfLink()] == "1" ){
							// ----廃止されているので、なにもしない
							// 廃止されているので、なにもしない----
						}
						// テーブルにはあるが、選ばれれなかったもの---
					}
				}
				// すでにテーブルにあるもの----
				$boolRet = true;
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
		
			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15004",$this->getColLabel(true));
		}
		//----保存テーブルに書き込む
		//保存テーブルに書き込む----

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}

	function afterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----最新テーブル
	//NEW[1]
	public function getTableIDOfLinkUtn(){
		return $this->strTableIdOfLinkUtn;
	}
	//NEW[2]
	public function setTableIDOfLinkUtn($strValue){
		$this->strTableIdOfLinkUtn = $strValue;
	}
	//NEW[3]
	public function getSeqIDOfLinkUtn(){
		return $this->strSeqIdOfLinkUtn;
	}
	//NEW[4]
	public function setSeqIDOfLinkUtn($strValue){
		$this->strSeqIdOfLinkUtn = $strValue;
	}
	//最新テーブル----

	//----履歴テーブル
	//NEW[5]
	public function getTableIDOfLinkJnl(){
		return $this->strTableIdOfLinkJnl;
	}
	//NEW[6]
	public function setTableIDOfLinkJnl($strValue){
		$this->strTableIdOfLinkJnl = $strValue;
	}
	//NEW[7]
	public function getSeqIDOfLinkJnl(){
		return $this->strSeqIdOfLinkJnl;
	}
	//NEW[8]
	public function setSeqIDOfLinkJnl($strValue){
		$this->strSeqIdOfLinkJnl = $strValue;
	}
	//NEW[9]
	public function getJSNColumnColumnIDOfLink(){
		return $this->strJSNColumnIdOfLink;
	}
	//NEW[10]
	public function setJSNColumnColumnIDOfLink($strValue){
		$this->strJSNColumnIdOfLink = $strValue;
	}
	//NEW[11]
	public function getJRCColumnColumnIDOfLink(){
		return $this->strJRCColumnIdOfLink;
	}
	//NEW[12]
	public function setJRCColumnColumnIDOfLink($strValue){
		$this->strJRCColumnIdOfLink = $strValue;
	}
	//NEW[13]
	public function getJRTColumnColumnIDOfLink(){
		return $this->strJRTColumnIdOfLink;
	}
	//NEW[14]
	public function setJRTColumnColumnIDOfLink($strValue){
		$this->strJRTColumnIdOfLink = $strValue;
	}
	//履歴テーブル----

	//----最新および履歴の共通事項
	//NEW[15]
	public function getRIColumnIDOfLink(){
		return $this->strRIColumnIdOfLink;
	}
	//NEW[16]
	public function setRIColumnIDOfLink($strValue){
		$this->strRIColumnIdOfLink = $strValue;
	}
	//NEW[17]
	public function getDisuseColumnIDOfLink(){
		return $this->strDisuseColumnIdOfLink;
	}
	//NEW[18]
	public function setDisuseColumnIDOfLink($strValue){
		$this->strDisuseColumnIdOfLink = $strValue;
	}
	//NEW[19]
	public function getLUTSColumnIDOfLink(){
		return $this->strLUTSColumnIdOfLink;
	}
	//NEW[20]
	public function setLUTSColumnIDOfLink($strValue){
		$this->strLUTSColumnIdOfLink = $strValue;
	}
	//NEW[21]
	public function getLUUColumnIDOfLink(){
		return $this->strLUUColumnIdOfLink;
	}
	//NEW[22]
	public function setLUUColumnIDOfLink($strValue){
		$this->strLUUColumnIdOfLink = $strValue;
	}
	//NEW[23]
	public function getMasterKeyColumnIDOfLink(){
		return $this->strMasterKeyColumnIdOfLink;
	}
	//NEW[24]
	public function setMasterKeyColumnIDOfLink($strValue){
		$this->strMasterKeyColumnIdOfLink = $strValue;
	}

	//----通常は、MainTableのRIColumnIDと等しくなる
	//NEW[25]
	public function getAnchorColumnIDOfLink(){
		return $this->strAnchorColumnIdOfLink;
	}
	//NEW[26]
	public function setAnchorColumnIDOfLink($strValue){
		$this->strAnchorColumnIdOfLink = $strValue;
	}
	//通常は、MainTableのRIColumnIDと等しくなる----

	//NEW[27]
	public function getOtherColumnType(){
		return $this->arrayOtherColumnType;
	}
	//NEW[28]
	public function setOtherColumnType($arrayColumnType){
		$this->arrayOtherColumnType = $arrayColumnType;
	}
	//最新および履歴の共通事項----

	//ここまで新規メソッドの定義宣言処理----

}

class TextColumn extends Column {

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel){
		parent::__construct($strColId, $strColLabel);

		$this->setDBColumn(true);
		$this->setSelectTagCallerShow(true);

		$outputType = new OutputType(new ReqTabHFmt(), new TextInputTabBFmt());
		$this->setOutputType("register_table", $outputType);

		$outputType = new OutputType(new ReqTabHFmt(), new TextInputTabBFmt());
		$this->setOutputType("update_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new TextTabBFmt());
		$this->setOutputType("delete_table", $outputType);

		$outputType = new OutputType(new FilterTabHFmt(), new TextFilterTabBFmt());
		$this->setOutputType("filter_table", $outputType);
		$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));

		$outputType = new OutputType(new SortedTabHFmt(), new TextTabBFmt());
		$this->setOutputType("print_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new TextTabBFmt());
		$this->setOutputType("print_journal_table", $outputType);

		$this->setValidator(new SingleTextValidator(0,256));

	}

	function setRichFilterValues($value){
		$this->aryRichFilterValueRawBase = $value;
	}

	function addRichFilterValue($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}

	function getFilterQueryInZone($boolBinaryDistinctOnDTiS){
		global $g;
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		//----INの場合は、あいまいモードでも厳密な一致をさせる必要がある
		if( $g['db_model_ch'] == 0 ){
			//----バイナリで精密な一致
			$strWrapHead="NLSSORT(";
			$strWrapTail=",'NLS_SORT=BINARY')";
			//バイナリで精密な一致----
		}else if( $g['db_model_ch'] == 1 ){
			$strWrapHead= "";
			$strWrapTail= "";
		}
		//INの場合は、あいまいモードでも厳密な一致をさせる必要がある----

		$tmpArray = array();
		$intFilterCount = 0;
		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		global $g;
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;

		if( $g['db_model_ch'] == 0 ){
			//----バイナリで精密な一致
			$strWrapHead="NLSSORT(";
			$strWrapTail=",'NLS_SORT=BINARY')";
			//バイナリで精密な一致----
		}else if( $g['db_model_ch'] == 1 ){
			$strWrapHead= "";
			$strWrapTail= "";
		}

		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if( 0 < strlen($filter) ){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if( 0 < count($tmpArray) ){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	function getNullSearchQuery(){
		//----WHERE句[1]
		global $g;
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		if( $g['db_model_ch'] == 0 ){
			//----ORACLE
			$retStrQuery = " {$strWpTblSelfAlias}.{$strWpColId} IS NULL ";
			//----
		}else if( $g['db_model_ch'] == 1 ){
			//----mySQL/mariaDB
			$retStrQuery = " ({$strWpTblSelfAlias}.{$strWpColId} IS NULL OR {$strWpTblSelfAlias}.{$strWpColId} = '') ";
			//mySQL/mariaDB----
		}
		return $retStrQuery;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class HostInsideLinkTextColumn extends TextColumn{

	protected $origin;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $orgin=""){
		parent::__construct($strColId, $strColLabel);

		$outputType = new OutputType(new SortedTabHFmt(), new HostInsideLinkTextTabBFmt());
		$this->setOutputType('print_table', $outputType);

		$outputType = new OutputType(new TabHFmt(), new HostInsideLinkTextTabBFmt());
		$this->setOutputType('print_journal_table', $outputType);

		$this->origin = $orgin;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function setOrigin($origin){
		$this->origin = rtrim($origin,'/');
	}
	//NEW[2]
	public function getOrigin(){
		return $this->origin;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class PasswordColumn extends TextColumn {

	protected $strEncodeFunctionName;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $aryEtcetera=array()){
		parent::__construct($strColId, $strColLabel);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));
		$this->setOutputType("print_journal_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$this->setOutputType("delete_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("filter_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new PasswordInputTabBFmt());
		$this->setOutputType("update_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new PasswordInputTabBFmt());
		$this->setOutputType("register_table", $outputType);

		$outputType = new OutputType(new ExcelHFmt(), new StaticBFmt(""));
		$this->setOutputType("excel", $outputType);

		$outputType = new OutputType(new CSVHFmt(), new StaticCSVBFmt(""));
		$this->setOutputType("csv", $outputType);

		$outputType = new OutputType(new ExcelHFmt(), new StaticBFmt(""));
		$this->setOutputType("json", $outputType);

		if( array_key_exists("updateRequireExcept", $aryEtcetera) === true ){
			$this->updateRequireExcept = $updateRequireExcept['updateRequireExcept'];
		}
		$this->setEncodeFunctionName("md5");
		$this->setSelectTagCallerShow(false);
		
		$this->setDeleteOffBeforeCheck(false); //復活は、値のバリデーションチェックを行わない
	}

	//----廃止または復活時等のレコード比較用
	//NEW[39]
	function compareRow(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet=true;
		$rowEditTgt = $aryVariant['edit_target_row'];
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( array_key_exists($this->getID(), $rowEditTgt) === true ){
			//----送信受けしたデータのキーと同じキーを、比較行がもっている
			$boolRet=false;
			if( strlen($reqOrgData[$this->getID()]) == 0 ){
				$boolRet=true;
			}else{
				$strEncodeFunctionName = $this->getEncodeFunctionName();
				$strEncodedValue = $reqOrgData[$this->getID()];
				if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
					//----承認等の場合の比較ロジック（エンコードされていない送信値をエンコードする）
					if( $strEncodeFunctionName!="" ){
						$strEncodedValue = $strEncodeFunctionName($reqOrgData[$this->getID()]);
					}
					//承認等の場合の比較ロジック（エンコードされていない送信値をエンコードする）----
				}else if( $modeValue=="DTUP_singleRecDelete" ){
					//----廃止/復活時の比較ロジック（エンコード済のレコード内値を想定している）
					$strEncodedValue = $reqOrgData[$this->getID()];
					//廃止/復活時の比較ロジック（エンコード済のレコード内値を想定している）----
				}
				if( $strEncodedValue == $rowEditTgt[$this->getID()] ){
					$boolRet=true;
				}
			}
			//送信受けしたデータのキーと同じキーを、比較行がもっている----
		}
		//送信受けしたデータにキーがある----
		return $boolRet;
	}
	//廃止または復活時等のレコード比較用----

	//----TableIUDイベント系
	//NEW[70]
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		$boolOverride = false;
		if( is_null($this->aryFunctionsForEvent) === false ){
			if( array_key_exists('beforeTableIUDAction',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['beforeTableIUDAction'];
				$retArray = $objFunction($this,'beforeTableIUDAction',$exeQueryData, $reqOrgData, $aryVariant);
				$boolOverride = true;
			}
		}
		if( $boolOverride === false ){
			if( array_key_exists($this->getID(), $exeQueryData) === true ){
				$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
				if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
					if( $exeQueryData[$this->getID()] != "" ){
						//----値の入力があった場合
						$strEncodeFunctionName = $this->getEncodeFunctionName();
						if( $strEncodeFunctionName != "" ){
							$strEncodedValue = $strEncodeFunctionName($exeQueryData[$this->getID()]);
						}else{
							$strEncodedValue = $exeQueryData[$this->getID()];
						}
						$exeQueryData[$this->getID()] = $strEncodedValue;
						//値の入力があった場合----
					}
				}else if( $modeValue=="DTUP_singleRecDelete" ){
				}else{
				}
			}
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setEncodeFunctionName($strValue){
		$retBool = false;
		$aryForCheck = array("md5"=>0,"ky_encrypt"=>1,"base64_encode"=>2);
		if( array_key_exists($strValue,$aryForCheck) === true ){
			$this->strEncodeFunctionName = $strValue;
		}
		return $retBool;
	}
	//NEW[2]
	function getEncodeFunctionName(){
		return $this->strEncodeFunctionName;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class MultiTextColumn extends TextColumn {

	protected $intParaGpaphMode; //----1はLf統一

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel){
		parent::__construct($strColId, $strColLabel);
		$outputType = new OutputType(new ReqTabHFmt(), new TextAreaTabBFmt());
		$this->setOutputType("update_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new TextAreaTabBFmt());
		$this->setOutputType("register_table", $outputType);

		$this->setValidator(new MultiTextValidator(0,4000));

		$this->setSelectTagCallerShow(true);

		$this->setParaGpaphMode(1);
	}

	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( $this->getParaGpaphMode()===1 ){
			//----CrLfがあったらLfへ統一するモード
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
				list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqOrgData,array($this->getID()),null);
				if( $tmpBoolKeyExist===true ){
					$strConvedValue = str_replace(array("\r\n","\r"),"\n",$varValue);
					$reqOrgData[$this->getID()] = $strConvedValue;
					$exeQueryData[$this->getID()] = $strConvedValue;
				}
			}
			//CrLfがあったらLfへ統一するモード----
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	function setParaGpaphMode($intParaGpaphMode){
		$intParaGpaphMode = ($intParaGpaphMode===1)?1:0;
		$this->intParaGpaphMode = $intParaGpaphMode;
	}
	function getParaGpaphMode(){
		return $this->intParaGpaphMode;
	}
	//ここまで新規メソッドの定義宣言処理----

}

class NoteColumn extends MultiTextColumn {
	protected $boolRequiredWhenDeleteOn;
	protected $boolRequiredWhenDeleteOff;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId="NOTE", $strColLabel=""){
		global $g;
		if( $strColLabel == "" ){
			$strColLabel = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11201");
		}
		parent::__construct($strColId, $strColLabel);
		$this->setHiddenMainTableColumn(true);
		$outputType = new OutputType(new ReqTabHFmt(), new TextAreaTabBFmt());
		$this->setOutputType("delete_table", $outputType);
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11202"));

		$this->setRequiredWhenDeleteOn(false);
		$this->setRequiredWhenDeleteOff(false);

		$this->setSelectTagCallerShow(true);
	}

	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( $this->getParaGpaphMode()===1 ){
			//----CrLfがあったらLfへ統一するモード
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			$boolCleanExecute = false;
			if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
				$boolCleanExecute=true;
			}else if($modeValue=="DTUP_singleRecDelete" ){
				if( $this->objTable->getRequiredNoteColumnID()==$this->getID() ){
					$boolCleanExecute=true;
				}
			}
			if( $boolCleanExecute===true ){
				list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqOrgData,array($this->getID()),null);
				if( $tmpBoolKeyExist===true ){
					$strConvedValue = str_replace(array("\r\n","\r"),"\n",$varValue);
					$reqOrgData[$this->getID()] = $strConvedValue;
					$exeQueryData[$this->getID()] = $strConvedValue;
				}
			}
			//CrLfがあったらLfへ統一するモード----
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setRequiredWhenDeleteOn($boolValue){
		$this->boolRequiredWhenDeleteOn = $boolValue;
	}
	//NEW[2]
	function isRequiredWhenDeleteOn(){
		return $this->boolRequiredWhenDeleteOn;
	}
	//NEW[3]
	function setRequiredWhenDeleteOff($boolValue){
		$this->boolRequiredWhenDeleteOff = $boolValue;
	}
	//NEW[4]
	function isRequiredWhenDeleteOff(){
		return $this->boolRequiredWhenDeleteOff;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class NumColumn extends Column {
	protected $intDigit;

	protected $boolNumberSepaMarkShow;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $intDigitScale=0){
		assert($intDigitScale >= 0);
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(true);
		$this->setNum(true);
		$this->setSubtotalFlag(true);

		$this->setSearchType("range");

		$this->intDigit = $intDigitScale;
		$outputType = new OutputType(new FilterTabHFmt(), new NumRangeFilterTabBFmt($intDigitScale,true));
		$this->setOutputType("filter_table", $outputType);
		$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));

		$outputType = new OutputType(new SortedTabHFmt(), new NumTabBFmt($intDigitScale,true));
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new NumTabBFmt($intDigitScale,true));
		$this->setOutputType("print_journal_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new NumInputTabBFmt($intDigitScale,false));
		$this->setOutputType("update_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new NumInputTabBFmt($intDigitScale,false));
		$this->setOutputType("register_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new NumTabBFmt($intDigitScale,true));
		$this->setOutputType("delete_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new SubtotalTabBFmt($intDigitScale,true));
		$this->setOutputType("print_subtotal_table", $outputType);

		$this->addClass("number");
		$this->setNumberSepaMarkShow(true);

		if($intDigitScale === 0){
			$this->setValidator(new IntNumValidator(0));
		}else{
			$this->setValidator(new FloatNumValidator(0, null, $intDigitScale));
		}
		$this->setSelectTagCallerShow(true);
	}

	//----廃止または復活時等のレコード比較用
	function compareRow(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet=true;
		$rowEditTgt = $aryVariant['edit_target_row'];
		if( array_key_exists($this->getID(),$rowEditTgt) === true ){
			//----送信受けしたデータのキーと同じキーを、比較行がもっている
			$boolRet=false;
			$intLenReqOrg = strlen($reqOrgData[$this->getID()]);
			$intLenEditTgt = strlen($rowEditTgt[$this->getID()]);
			if( 0 < $intLenReqOrg && 0 < $intLenEditTgt ){
				//----双方ともに長さ1以上
				if( is_numeric($reqOrgData[$this->getID()])===true && is_numeric($rowEditTgt[$this->getID()])===true ) {
					//----双方ともに数値として評価できる場合
					if( bccomp($reqOrgData[$this->getID()], $rowEditTgt[$this->getID()], $this->intDigit)===0 ){
						//----値として評価しても等しい
						$boolRet=true;
						//値として評価しても等しい----
					}
					//双方ともに数値として評価できる場合----
				}
				//双方ともに長さ1以上----
			}else if( $intLenReqOrg===0 && $intLenEditTgt===0 ){
				//----NULLだった場合
				$boolRet=true;
				//NULLだった場合----
			}
			//送信受けしたデータのキーと同じキーを、比較行がもっている----
		}
		//送信受けしたデータにキーがある----
		return $boolRet;
	}
	//廃止または復活時等のレコード比較用----

	function getFilterValuesForDTiS($boolForBind=true, $boolBinaryDistinctOnDTiS=true){
		$arySource = $this->aryFilterValueForDTiS;

		if( $this->getSearchType() == "range" ){
			//----期間の開始と終了の2個だけ返す
			$data = array();
			if( isset($arySource[0])===true ){
				if( strlen($arySource[0]) != 0 ){
					$data[0] = $arySource[0];
				}
			}
			if( isset($arySource[1])===true ){
				if( strlen($arySource[1]) != 0 ){
					$data[1] = $arySource[1];
				}
			}
			return $data;
			//期間の開始と終了の2個だけ返す----
		}else{
			return parent::getFilterValuesForDTiS($boolForBind, $boolBinaryDistinctOnDTiS);
		}
	}

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		$retStrQuery = "";
		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		switch($this->getSearchType()){
			case "in":
				$tmpArray = array();
				$intFilterCount = 0;

				$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
				foreach($arySource as $filter){
					if(0 < strlen($filter)){
						$tmpArray[] = ":{$this->getID()}__{$intFilterCount}";
						$intFilterCount++;
					}
				}
				if(0 < count($tmpArray)){
					$retStrQuery .= "{$strWpTblSelfAlias}.{$strWpColId}";
					$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
				}
				break;
			case "range":
				$strSelfAliasStrConColId = "{$strWpTblSelfAlias}.{$strWpColId}";
				$flag = false;
				$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
				if( isset($arySource[0])===true ){
					if( 0 < strlen($arySource[0]) ){
						//----長さが0ではない
						$retStrQuery .= "{$strSelfAliasStrConColId} >= :{$this->getID()}__0";
						$flag = true;
					}
				}
				if( isset($arySource[1])===true ){
					if( 0 < strlen($arySource[1]) ){
						//----長さが0ではない
						if($flag){
							$retStrQuery .= " AND ";
						}
						$retStrQuery .= "{$strSelfAliasStrConColId} <= :{$this->getID()}__1";
					}
				}
				break;
			default:
				break;
		}
		return $retStrQuery;
		//クラス(Table)のメソッド(getFilterQuery)から呼び出される----
	}

	function setRichFilterValues($value){
		$this->aryRichFilterValueRawBase = $value;
	}

	function addRichFilterValue($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		$retStrQuery = "";
		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;

		//----数値は、バイナリで精密な一致をする必要はない
		$strWrapHead="";
		$strWrapTail="";
		//数値は、バイナリで精密な一致をする必要はない----

		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setNumberSepaMarkShow($boolValue){
		$this->boolNumberSepaMarkShow = $boolValue;
	}
	//NEW[2]
	function getNumberSepaMarkShow(){
		return $this->boolNumberSepaMarkShow;
	}
	//NEW[3]
	function addSubtotalValue($value){
		if($this->intDigit === 0){
			// 整数のみが想定されている場合
			//$this->subtotalValue += $value;
			$this->subtotalValue = bcadd($this->subtotalValue, $value, 0);
		}else{
			// 小数が入ることが想定される場合
			// 関数bcaddの引数(1,2)及び返値は「文字列型」
			$this->subtotalValue = bcadd($this->subtotalValue, $value, $this->intDigit);
		}
	}

	//ここまで新規メソッドの定義宣言処理----

}

class JournalSeqNoColumn extends NumColumn {
	//通常時は表示しない

	protected $strSequenceId;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId="JOURNAL_SEQ_NO", $strColExplain="", $strSequenceId=null){
		if( $strColExplain == "" ){
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11301");
		}
		parent::__construct($strColId, $strColExplain);
		$this->setNum(true);
		$this->setSubtotalFlag(false);
		$this->setHeader(true);
		$this->setDBColumn(false);
		$this->getOutputType("print_journal_table")->setVisible(true);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("print_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("excel")->setVisible(false);
		$this->getOutputType("csv")->setVisible(false);
		$this->getOutputType("json")->setVisible(false);

		$this->setSequenceID($strSequenceId);
		$this->setNumberSepaMarkShow(false);
	}

	//----FixColumnイベント系
	function afterFixColumn(){
		if($this->getSequenceID() === null){
			$arrayColumn = $this->objTable->getColumns();
			$objRIColumnID = $arrayColumn[$this->objTable->getRowIdentifyColumnID()];
			$strSeqId = $objRIColumnID->getSequenceID();
			if($strSeqId != ""){
				$this->setSequenceID("J".$strSeqId);
			}
		}
	}
	//FixColumnイベント系----

	//----TableIUDイベント系
	
	function getSequencesForTrzStart(&$arySequence=array()){
		//----トランザクション内
		global $g;
		$intControlDebugLevel01=250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		try{
			if( strlen($this->getSequenceID())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			$arySequence[$this->getSequenceID().'_'] = $this->getSequenceID();
			$boolRet = true;
			//履歴シーケンスを捕まえる（デッドロック防止）----
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15001",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}
	
	public function inTrzBeforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;

		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$retArray = array();
		$boolRet=false;
		$intErrorType=0;
		$strErrMsg="";
		try{
			if( strlen($this->getSequenceID())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			//----シーケンスが設定されている場合
			$retArray= getSequenceValue($this->getSequenceID(),true);

			if( $retArray[1] === 0 ){
				// JOURNAL専用のカラムなので、$reqOrgData、に代入してはならない
				$exeQueryData[$this->getID()] = array('JNL'=>$retArray[0]);
				$boolRet = true;
			}
			else{
				$intErrorType = 500;
				web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSequenceID())));
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			//シーケンスが設定されている場合----
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-16001",$this->getColLabel(true));
		}
		
		$retArray[0] = $boolRet;
		$retArray[1] = $intErrorType;
		$retArray[2] = $strErrMsg;
		$retArray[3] = "";
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}

	function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		//----トランザクション内
		global $g;

		$intControlDebugLevel01 = 250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		try{
			$objTable = $this->objTable;
			$arrayObjColumn = $objTable->getColumns();
			$objTable->getRequiredUpdateDate4UColumnID();

			$exeJournalData = generateElementForJournalReg($exeQueryData,$aryVariant['edit_target_row'],$arrayObjColumn,$objTable->getRequiredUpdateDate4UColumnID(),$objTable->getDBMainTableHiddenID());

			$sqlJnlBody = generateJournalRegisterSQL($exeJournalData,$arrayObjColumn,$objTable->getDBJournalTableID(),$objTable->getDBJournalTableHiddenID() );
			
			$retSQLResultArray = singleSQLExecuteAgent($sqlJnlBody, $exeJournalData, $strFxName);
			if( $retSQLResultArray[0]===true ){
				$objQueryJnl =& $retSQLResultArray[1];
				$resultRowLength = $objQueryJnl->effectedRowCount();
				if( $resultRowLength!= 1 ){
					$intErrorType = 500;
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				$boolRet = true;
				unset($objQueryJnl);
			}
			else{
				$intErrorType = 500;
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",$tmpErrMsgBody));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-16002",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setSequenceID($strSequenceId){
		$this->strSequenceId = $strSequenceId;
	}

	//NEW[2]
	function getSequenceID(){
		return $this->strSequenceId;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class AutoNumColumn extends NumColumn {

	protected $strSequenceId;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strSequenceId=null){
		parent::__construct($strColId, $strColLabel);
		$this->setSearchType("in");
		$this->setSubtotalFlag(false);
		$this->setAllowSendFromFile(false);
		if( $strSequenceId === null ){
			$this->setSequenceID("SEQ_".$this->getID());
		}else{
			$this->setSequenceID($strSequenceId);
		}
		$this->setSelectTagCallerShow(true);
		$this->setNumberSepaMarkShow(false);
	}

	//----TableIUDイベント系
	
	function getSequencesForTrzStart(&$arySequence=array()){
		//----トランザクション内
		global $g;
		$intControlDebugLevel01=250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		try{
			if( strlen($this->getSequenceID())==0 ){
				//----シーケンスが設定されていない場合
				$intErrorType = 500;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//シーケンスが設定されていない場合----
			}
			$arySequence[$this->getSequenceID().'_'] = $this->getSequenceID();
			$boolRet = true;
			//履歴シーケンスを捕まえる（デッドロック防止）----
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5002",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-15001",$this->getColLabel(true));
		}

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
		//トランザクション内----
	}

	public function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;

		$intControlDebugLevel01 = 250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		try{
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
				if( strlen($this->getSequenceID())==0 ){
					$intErrorType = 500;
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//----シーケンスが設定されている場合
				$retArray= getSequenceValue($this->getSequenceID(),true);

				if( $retArray[1]===0 ){
					$reqOrgData[$this->getID()] = $retArray[0];
					$boolRet = true;
				}
				else{
					$intErrorType = 500;
					web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSequenceID())));
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//シーケンスが設定されている場合----
			}else if( $modeValue=="DTUP_singleRecDelete" ){
				$boolRet = true;
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-17001",$this->getColLabel(true));
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setSequenceID($strSequenceId){
		$this->strSequenceId = $strSequenceId;
	}
	//NEW[2]
	function getSequenceID(){
		return $this->strSequenceId;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class AutoNumRegisterColumn extends AutoNumColumn {
	function __construct($strColId, $strColLabel, $strSequenceId=null, $uniqueColumns=array()){
		global $g;
		
		parent::__construct($strColId, $strColLabel, $strSequenceId);

		$outputType = new OutputType(new ReqTabHFmt(), new TextTabBFmt());
		$this->setOutputType("update_table", $outputType);

		//自動入力
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($g['objMTS']->getSomeMessage("ITAWDCH-STD-11401")));
		$this->setOutputType("register_table", $outputType);
	}

	public function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;

		$intControlDebugLevel01 = 250;

		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		try{
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecRegister" ){
				if( strlen($this->getSequenceID())==0 ){
					$intErrorType = 500;
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//----シーケンスが設定されている場合
				$retArray= getSequenceValue($this->getSequenceID(),true);

				if( $retArray[1]===0 ){
					$reqOrgData[$this->getID()] = $retArray[0];
					$boolRet = true;
				}
				else{
					$intErrorType = 500;
					web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$this->getSequenceID())));
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
				//シーケンスが設定されている場合----
				
			}else if( $modeValue=="DTUP_singleRecUpdate" ){
				$boolRet = true;
			}else if( $modeValue=="DTUP_singleRecDelete" ){
				$boolRet = true;
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));

			$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-17001",$this->getColLabel(true));
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}
	//TableIUDイベント系----
}
//----ここまでクラス定義

class RowIdentifyColumn extends AutoNumColumn {
	protected $uniqueColumns;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strSequenceId=null, $uniqueColumns=array()){
		global $g;
		
		parent::__construct($strColId, $strColLabel, $strSequenceId);
		$this->setHiddenMainTableColumn(true);
		$this->setHeader(true);
		$this->setSearchType("range");

		$outputType = new OutputType(new ReqTabHFmt(), new TextTabBFmt());
		$this->setOutputType("update_table", $outputType);
		//自動入力
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($g['objMTS']->getSomeMessage("ITAWDCH-STD-11401")));
		$this->setOutputType("register_table", $outputType);

		//----このインスタンスに紐づくOutputTypeインスタンスにアクセスする
		$this->getOutputType("delete_table")->init($this, "delete_table");
		$this->getOutputType("filter_table")->init($this, "filter_table");
		$this->getOutputType("print_table")->init($this, "print_table");
		$this->getOutputType("print_journal_table")->init($this, "print_journal_table");
		//このインスタンスに紐づくOutputTypeインスタンスにアクセスする----

		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11402"));

		$this->setJournalSearchFilter(true);

		$this->setValidator(new RowIDNoValidator());
	}

	//----AddColumnイベント系
	function initTable($objTable, $colNo){
		parent::initTable($objTable, $colNo);
	}
	//AddColumnイベント系----

	//----TableIUDイベント系
	public function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" ){
			//----親クラス[AutoNumColumn]の同名関数を呼んで、その後作業
			$retArray = parent::beforeIUDValidateCheck($exeQueryData, $reqOrgData, $aryVariant);
			//親クラス[AutoNumColumn]の同名関数を呼んで、その後作業----
		}else if( $modeValue=="DTUP_singleRecUpdate" ){
			//----更新の場合
			$boolRet = true;
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			//更新の場合----
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			//----廃止の場合
			$boolRet = true;
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			//廃止の場合----
		}
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class LockTargetColumn extends NumColumn {

	//----ここから継承メソッドの上書き処理

    function __construct($strViewTableWkPKId, $strViewTableWkPKName){
        parent::__construct($strViewTableWkPKId, $strViewTableWkPKName);
		$this->setHiddenMainTableColumn(true);
		
        $this->setOutputType('update_table', new OutputType(new TabHFmt(), new TextTabBFmt()));
        $this->setOutputType('register_table', new OutputType(new TabHFmt(), new LockTargetInputTabBFmt()));
        
        $this->setHeader(true);
        
        $this->setSubtotalFlag(false);

		$this->setValidator(new RowIDNoValidator());

		$this->setNumberSepaMarkShow(false);
    }

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

//----ここからDateカラム系
class DateColumn extends Column {

	protected $strDateFormat;
	protected $strFxNameStrToDate;
	protected $strFxNameDateToStr;

	protected $intSelectTagPrintType;

	protected $strDateHiddenFormat;

	protected $strFilterInputType;
	protected $boolFilterSecondInput;
	protected $intFilterMinuteScale;
	
	protected $strIUInputType;
	protected $boolIUSecondInput;
	protected $intIUMinuteScale;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strFilterType="DATE", $strIUInputType="DATE"){
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(true);
		
		global $g;
		if($g['db_model_ch'] === 0){
			$this->strDateHiddenFormat = "YYYY/MM/DD HH24:MI:SS";

			$this->strDateFormat = "YYYY/MM/DD";

			$this->strFxNameStrToDate = "TO_DATE";
			$this->strFxNameDateToStr = "TO_CHAR";
		}else if($g['db_model_ch'] === 1){
			$this->strDateHiddenFormat = "%Y/%m/%d %H:%i:%s";

			$this->strDateFormat = "%Y/%m/%d";

			$this->strFxNameStrToDate = "STR_TO_DATE";
			$this->strFxNameDateToStr = "DATE_FORMAT";
		}
		$outputType = new OutputType(new SortedTabHFmt(), new DateTextTabBFmt());
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new DateTextTabBFmt());
		$this->setOutputType("print_journal_table", $outputType);

		$this->setIUInputType($strIUInputType);
		$this->setFilterInputType($strFilterType);

		$this->setSearchType("range");
		$this->setValidator(new DateValidator());

		$this->setAddSelectTagPrintType(1);

		$this->setSelectTagCallerShow(false);
		
		$this->setEvent("filter_table", "onclose", "search_async", array("'idcolumn_filter_default'"));
	}

	function setAddSelectTagPrintType($intValue){
		$this->intSelectTagPrintType = $intValue;
	}
	function getAddSelectTagPrintType(){
		return $this->intSelectTagPrintType;
	}

	function getAddSelectTagQuery($searchTblId, $strWhereAddBody=""){
		$retStrQuery = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strWpDUFColId = "{$dbQM}{$this->getRequiredDisuseColumnID()}{$dbQM}";

		$strWpTableId = "{$dbQM}{$searchTblId}{$dbQM}";

		$strBodyFrom  = "SELECT ";
		$strBodyFrom .= "{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId}, '{$this->getDataHiddenFormat()}') {$dbQM}KEY_COLUMN{$dbQM}, ";
		$strBodyFrom .= "{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId}, '{$this->strDateFormat}') {$dbQM}DISP_COLUMN{$dbQM} ";
		$strBodyFrom .= "FROM {$strWpTableId} {$strWpTblSelfAlias} ";
		$strBodyFrom .= "WHERE {$strWpTblSelfAlias}.{$strWpColId} IS NOT NULL ";
		$strBodyFrom .= "AND {$strWpDUFColId} IN ('0','1') ";
		$strBodyFrom .= "{$strWhereAddBody} ";

		$retStrQuery  = "SELECT DISTINCT {$dbQM}KEY_COLUMN{$dbQM}, {$dbQM}DISP_COLUMN{$dbQM} ";
		$retStrQuery .= "FROM ({$strBodyFrom}) {$dbQM}TT_SYS_FROM{$dbQM} ";
		$retStrQuery .= "ORDER BY {$dbQM}DISP_COLUMN{$dbQM} ASC";

		$retArray = array($retStrQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		return $retArray;
	}

	function getFilterConvertValue($value){
		//----WHERE句[-]
		return "{$this->strFxNameStrToDate}({$value}, '{$this->getDataFormat()}')";
	}

	function getPartSqlInSelectZone(){
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$retStrQuery  = "{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId}, '{$this->getDataFormat()}') {$strWpColId}";
		return $retStrQuery;
	}

	function addFilterValue($value, $index=null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if( $this->getSearchType()=="in" ){
			$this->aryFilterValueRawBase[0] = $value;
		}else{
			if( $index!== null ){
				$this->aryFilterValueRawBase[$index] = $value;
			}else{
				$this->aryFilterValueRawBase[] = $value;
			}
		}
		//クラス(Table)のメソッド(addFilter)から呼び出される----
	}

	//----期間の開始と終了の2個だけ返す
	function getFilterValuesForDTiS($boolForBind=true, $boolBinaryDistinctOnDTiS=true){
		$arySource = $this->aryFilterValueForDTiS;

		$data = array();
		if($this->getSearchType() == "in"){
			$intCount = 0;
			foreach($arySource as $key=>$val){
				if( isset($val)===true ){
					if( strlen($val) != 0 ){
						$data[$intCount] = $val;
						$intCount += 1;
					}
				}
			}
		}else{
			if( isset($arySource[0])===true ){
				if( strlen($arySource[0]) != 0 ){
					$data[0] = $arySource[0];
				}
			}
			if( isset($arySource[1])===true ){
				if( strlen($arySource[1]) != 0 ){
					$data[1] = $arySource[1];
				}
			}
		}
		return $data;
	}
	//期間の開始と終了の2個だけ返す----

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;

		$retStrQuery = "";
		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strSelfAliasStrConColId = "{$strWpTblSelfAlias}.{$strWpColId}";

		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		if($this->getSearchType() == "in"){
			$intCount = 0;
			$arrayElement = array();
			foreach($arySource as $key=>$val){
				if( isset($val)===true ){
					if( strlen($val) != 0 ){
						$arrayElement[] = $this->getFilterConvertValue(":{$this->getID()}__".$intCount);
						$intCount += 1;
					}
				}
			}
			
			if( 1 <= $intCount ){
				$retStrQuery  = "{$strSelfAliasStrConColId} IN (";
				$retStrQuery .= implode(",",$arrayElement);
				$retStrQuery .= ") ";
			}
		}else{
			$flag = false;
			if( isset($arySource[0])===true ){
				if( strlen($arySource[0]) != 0 ){
					$retStrQuery .= "{$strSelfAliasStrConColId} >= ".$this->getFilterConvertValue(":{$this->getID()}__0");
					$flag = true;
				}
			}

			if( isset($arySource[1])===true ){
				if( strlen($arySource[1]) != 0 ){
					if($flag===true){
						$retStrQuery .= " AND ";
					}
					//----期間の終わりは約1日足す
					$retStrQuery .= "{$strSelfAliasStrConColId} < ".$this->getFilterConvertValue(":{$this->getID()}__1")."+1-(1/86400) ";
					//期間の終わりは約1日足す----
				}
			}
		}
		return $retStrQuery;
	}

	function setRichFilterValues($value){
		$this->aryRichFilterValueRawBase = $value;
	}

	function addRichFilterValue($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		$retStrQuery = "";
		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;

		//----バイナリで精密な一致
		$strWrapHead="";
		$strWrapTail="";
		//バイナリで精密な一致----

		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = "{$strWrapHead}{$this->strFxNameStrToDate}(:{$this->getID()}_RF__{$intFilterCount}, '{$this->getDataHiddenFormat()}'){$strWrapTail}";
				$intFilterCount++;
			}
		}

		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	//----IUD系クエリ作成系
	function getRowRegisterQuery(&$aryQueryElement=array()){
		if($aryQueryElement[$this->getID()] === ""){
			$strSetValue = "NULL";
			$aryQueryElement[$this->getID()] = array("bind"=>false);
		}else{
			$strSetValue = "{$this->strFxNameStrToDate}(:{$this->getID()},'{$this->getDataHiddenFormat()}')";
		}
		return $strSetValue;
	}
	function getRowUpdateQuery(&$aryQueryElement=array()){
		if($aryQueryElement[$this->getID()] === ""){
			$strSetValue = "{$this->getID()} = NULL";
			$aryQueryElement[$this->getID()] = array("bind"=>false);
		}else{
			$strSetValue = "{$this->getID()} = {$this->strFxNameStrToDate}(:{$this->getID()},'{$this->getDataHiddenFormat()}')";
		}
		return $strSetValue;
	}
	//IUD系クエリ作成系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----IU用インプット
	//NEW[1]
	function setIUInputType($strType){
		if($strType == "DATETIME"){
			$this->setOutputType("update_table", new OutputType(new ReqTabHFmt(), new DateInputTabBFmt()));
			$this->setOutputType("register_table", new OutputType(new ReqTabHFmt(), new DateInputTabBFmt()));
			$this->setMinuteScaleInputOnIU(5);
			$this->setSecondsInputOnIU(true);
		}else{
			$this->setOutputType("update_table", new OutputType(new ReqTabHFmt(), new DateInputTabBFmt()));
			$this->setOutputType("register_table", new OutputType(new ReqTabHFmt(), new DateInputTabBFmt()));
			$this->setMinuteScaleInputOnIU(null);
			$this->setSecondsInputOnIU(false);
			$strType = "DATE";
		}
		$this->strIUInputType = $strType;
	}

	//NEW[2]
	function getIUInputType(){
		return $this->strIUInputType;
	}
	//NEW[3]
	function setMinuteScaleInputOnIU($intValue){
		$this->intIUMinuteScale = $intValue;
	}
	//NEW[4]
	function getMinuteScaleInputOnIU(){
		return $this->intIUMinuteScale;
	}
	//NEW[5]
	function setSecondsInputOnIU($boolValue){
		$this->boolIUSecondInput = $boolValue;
	}
	//NEW[6]
	function getSecondsInputOnIU(){
		return $this->boolIUSecondInput;
	}
	//IU用インプット----

	//----フィルター用インプット
	//NEW[7]
	function setFilterInputType($strType){
		if($strType == "DATETIME"){
			$this->setOutputType("filter_table", new OutputType(new FilterTabHFmt(), new DateFilterTabBFmt()));
			$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));
			$this->setMinuteScaleInputOnFilter(5);
			$this->setSecondsInputOnFilter(true);
		}else{
			$this->setOutputType("filter_table", new OutputType(new FilterTabHFmt(), new DateFilterTabBFmt()));
			$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));
			$this->setMinuteScaleInputOnFilter(null);
			$this->setSecondsInputOnFilter(false);
			$strType = "DATE";
		}
		$this->strFilterInputType = $strType;
	}
	//NEW[8]
	function getFilterInputType(){
		return $this->strFilterInputType;
	}
	//NEW[9]
	function setMinuteScaleInputOnFilter($intValue){
		$this->intFilterMinuteScale = $intValue;
	}
	//NEW[10]
	function getMinuteScaleInputOnFilter(){
		return $this->intFilterMinuteScale;
	}
	//NEW[11]
	function setSecondsInputOnFilter($boolValue){
		$this->boolFilterSecondInput = $boolValue;
	}
	//NEW[12]
	function getSecondsInputOnFilter(){
		return $this->boolFilterSecondInput;
	}
	//フィルター用インプット----

	//----DBとのデータ受け渡し用フォーマット
	//NEW[13]
	function getDataFormat(){
		return $this->strDateFormat;
	}
	//NEW[14]
	function getDataHiddenFormat(){
		return $this->strDateHiddenFormat;
	}
	//DBとのデータ受け渡し用フォーマット----

	//ここまで新規メソッドの定義宣言処理----

}

class DateTimeColumn extends DateColumn {

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strFilterType="DATETIME", $strIUInputType="DATETIME" ,$boolSecondsPrint=true){
		global $g;
		parent::__construct($strColId, $strColLabel, $strFilterType, $strIUInputType);
		if($g['db_model_ch'] === 0){
			$this->strDateHiddenFormat = "YYYY/MM/DD HH24:MI:SS.FF6";

			$this->strDateFormat = "YYYY/MM/DD HH24:MI:SS";

                        if ($boolSecondsPrint === false ) {
			    $this->strDateFormat = "YYYY/MM/DD HH24:MI";
                        }


			$this->strFxNameStrToDate = "TO_TIMESTAMP";
			$this->strFxNameDateToStr = "TO_CHAR";
		}else if($g['db_model_ch'] === 1){
			$this->strDateHiddenFormat = "%Y/%m/%d %H:%i:%s.%f";

			$this->strDateFormat = "%Y/%m/%d %H:%i:%s";

                        if ($boolSecondsPrint === false ) {
			    $this->strDateFormat = "%Y/%m/%d %H:%i";
                        }


			$this->strFxNameStrToDate = "STR_TO_DATE";
			$this->strFxNameDateToStr = "DATE_FORMAT";
		}

		$outputType = new OutputType(new SortedTabHFmt(), new DateTimeTextTabBFmt());
		$this->setOutputType("print_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new DateTimeTextTabBFmt());
		
		$this->setOutputType("print_journal_table", $outputType);

		$this->setValidator(new DateTimeValidator());

		$this->setSelectTagCallerShow(false);

		$this->setEvent("filter_table", "onclose", "search_async", array("'idcolumn_filter_default'"));
	}

	function getPartSqlInSelectZone(){
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$retStrQuery  = "{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId}, '{$this->getDataFormat()}') {$strWpColId}";
		return $retStrQuery;
	}
	
	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		$retStrQuery = "";
		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strSelfAliasStrConColId = "{$strWpTblSelfAlias}.{$strWpColId}";

		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		if($this->getSearchType() == "in"){
			$intCount = 0;
			$arrayElement = array();
			foreach($arySource as $key=>$val){
				if( isset($val)===true ){
					if( strlen($val) != 0){
						$arrayElement[] = $this->getFilterConvertValue(":{$this->getID()}__".$intCount);
						$intCount += 1;
					}
				}
			}
			
			if( 1 <= $intCount ){
				$retStrQuery  = "{$strSelfAliasStrConColId} IN (";
				$retStrQuery .= implode(",",$arrayElement);
				$retStrQuery .= ") ";
			}
		}else{
			//----range
			$flag = false;
			if( isset($arySource[0])===true ){
				if( strlen($arySource[0]) != 0 ){
					$retStrQuery .= "{$strSelfAliasStrConColId} >= ".$this->getFilterConvertValue(":{$this->getID()}__0");
					$flag = true;
				}
			}

			if( isset($arySource[1])===true ){
				if( strlen($arySource[1]) != 0){
					if($flag===true){
						$retStrQuery .= " AND ";
					}
					//----期間の終わりは約1秒足す
					$retStrQuery .= "{$strSelfAliasStrConColId} < ".$this->getFilterConvertValue(":{$this->getID()}__1")."+(1/86400)-(1/86400/1000000) ";
					//期間の終わりは約1秒足す----
				}
			}
			//range----
		}
		
		return $retStrQuery;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class JournalRegDateTimeColumn extends DateTimeColumn{

	//----ここから継承メソッドの上書き処理

	function __construct($strColId="JOURNAL_REG_DATETIME", $strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="変更日時"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11501");
		}
		parent::__construct($strColId, $strColExplain);
		$this->setNum(true);
		$this->setHeader(true);
		$this->setDBColumn(false);
		$this->getOutputType("print_journal_table")->setVisible(true);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("print_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("excel")->setVisible(false);
		$this->getOutputType("csv")->setVisible(false);
		$this->getOutputType("json")->setVisible(false);
	}

	//----TableIUDイベント系
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		// JOURNAL専用のカラムなので、$reqOrgData、に代入してはならない
		$exeQueryData[$this->getID()] = array('JNL'=>$this->getAutoSetValue($aryVariant));
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getAutoSetValue(&$aryVariant=array()){
		global $g;
		$retStrVal="";
		if($g['db_model_ch'] === 0){
			$retStrVal = $g['objDBCA']->getQueryTime();
		}else if($g['db_model_ch'] === 1){
			$retStrVal = $g['objDBCA']->getQueryTime();
		}
		return $retStrVal;
	}
	//TableIUDイベント系----

	//ここまで新規メソッドの定義宣言処理----

}

class AutoUpdateTimeColumn extends DateTimeColumn {
	protected $boolUpdateMode;

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText, $strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="更新日時"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11601");
		}
		parent::__construct($strColIdText, $strColExplain, "DATE");
		$this->setHiddenMainTableColumn(true);
		
		if($g['db_model_ch'] === 0){
			$this->strDateFormat = "YYYY/MM/DD HH24:MI:SS";
		}else if($g['db_model_ch'] === 1){
			$this->strDateFormat = "%Y/%m/%d %H:%i:%s";
		}

		//$strStaticTextBody = "自動入力";
		$strStaticTextBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11602");

		$this->setHeader(true);
		//----自動更新なので、エクセルからのアップロードを禁止する
		$this->setAllowSendFromFile(false);
		//自動更新なので、エクセルからのアップロードを禁止する----
		$this->setOutputType("update_table", new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));
		$this->setOutputType("register_table", new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));
		$this->setOutputType("delete_table", new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));

		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);

		//$this->setDescription("レコードの最終更新日。自動登録のため編集不可。");
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11603"));

		$this->setUpdateMode(true);
		$this->setDeleteOnBeforeCheck(true);
		$this->setDeleteOffBeforeCheck(true);

		$objDTV = new DateTimeValidator();
		$objDTV->setRegExp('#^\d{4}/\d{1,2}/\d{1,2}\s{1}\d{1,2}:\d{1,2}:\d{1,2}\.{1}[0-9]{6}$#',"DTUP_singleRecRegister");
		$objDTV->setDisplayFormat("yyyy/mm/dd hh:ii:ss.sssuuu","DTUP_singleRecRegister");
		$objDTV->setRegExp('#^\d{4}/\d{1,2}/\d{1,2}\s{1}\d{1,2}:\d{1,2}:\d{1,2}\.{1}[0-9]{6}$#',"DTUP_singleRecUpdate");
		$objDTV->setDisplayFormat("yyyy/mm/dd hh:ii:ss.sssuuu","DTUP_singleRecUpdate");
		$objDTV->setRegExp('#^\d{4}/\d{1,2}/\d{1,2}\s{1}\d{1,2}:\d{1,2}:\d{1,2}\.{1}[0-9]{6}$#',"DTUP_singleRecDelete");
		$objDTV->setDisplayFormat("yyyy/mm/dd hh:ii:ss.sssuuu","DTUP_singleRecDelete");
		$this->setValidator($objDTV);
	}

	function getAddSelectTagQuery($searchTblName, $strWhereAddBody=""){
		$retStrQuery = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		if($this->getAddSelectTagPrintType()==0){
			$dbQM=$this->objTable->getDBQuoteMark();
			$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
			$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

			$strWpTableName = "{$dbQM}{$searchTblName}{$dbQM}";

			$strWpDUFColId = "{$dbQM}{$this->getRequiredDisuseColumnID()}{$dbQM}";

			$strBodyFrom  = "SELECT {$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId},'{$this->getDataFormat()}') {$dbQM}KEY_COLUMN{$dbQM} ";
			$strBodyFrom .= "FROM {$strWpTableName} {$strWpTblSelfAlias} ";
			$strBodyFrom .= "WHERE {$strWpTblSelfAlias}.{$strWpColId} IS NOT NULL ";
			$strBodyFrom .= "AND {$strWpDUFColId} IN ('0','1') ";
			$strBodyFrom .= "{$strWhereAddBody}";

			$retStrQuery  = "SELECT DISTINCT {$dbQM}KEY_COLUMN{$dbQM} ";
			$retStrQuery .= "FROM ({$strBodyFrom}) {$dbQM}TT_SYS_FROM{$dbQM} ";
			$retStrQuery .= "ORDER BY {$dbQM}KEY_COLUMN{$dbQM} ASC";

			$retArray = array($retStrQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		}else{
			$retArray = parent::getAddSelectTagQuery($searchTblName, $strWhereAddBody);
		}
		return $retArray;
	}

	function getPartSqlInSelectZone(){
		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$retStrQuery  = "{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColId}, '{$this->getDataFormat()}') {$strWpColId}";
		return $retStrQuery;
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		$retStrQuery = "";
		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		if($this->getAddSelectTagPrintType()==0){
			$tmpArray = array();
			$intFilterCount = 0;

			$arySource = $this->getRichFilterValuesForDTiS(true);
			foreach($arySource as $filter){
				if(0 < strlen($filter)){
					$strBody  = "";
					$strBody .= "({$this->strFxNameStrToDate}(:{$this->getID()}_RF__{$intFilterCount}, '{$this->strDateFormat}')";
					$strBody .= " <= {$strWpTblSelfAlias}.{$strWpColId} AND {$strWpTblSelfAlias}.{$strWpColId} < ";
					$strBody .= "{$this->strFxNameStrToDate}(:{$this->getID()}_RF__{$intFilterCount}, '{$this->strDateFormat}') +(1/86400)-(1/86400/1000000))";
					$tmpArray[] = $strBody;
					$intFilterCount++;
				}
			}
			if(0 < count($tmpArray)){
				//----IN候補型の検出条件クエリを作成
				$retStrQuery .= implode(" OR ", $tmpArray);
				//IN候補型の検出条件クエリを作成----
			}
		}else{
			$retStrQuery = parent::getRichSearchQuery($boolBinaryDistinctOnDTiS);
		}
		return $retStrQuery;
	}

	//----IUD系クエリ作成系
	function getRowRegisterQuery(&$aryQueryElement=array()){
		$strSetValue = "{$this->strFxNameStrToDate}(:{$this->getID()},'{$this->getDataHiddenFormat()}')";
		return $strSetValue;
	}
	function getRowUpdateQuery(&$aryQueryElement=array()){
		$strSetValue = "{$this->getID()} = {$this->strFxNameStrToDate}(:{$this->getID()},'{$this->getDataHiddenFormat()}')";
		return $strSetValue;
	}
	//IUD系クエリ作成系----

	//----TableIUDイベント系
	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if($this->getUpdateMode()===true){
			// JOURNAL専用のカラムではないので、beforeIUDValidateCheckのタイミングでは、$exeQueryData、に直接代入してはならない
			$reqOrgData[$this->getID()] = $this->getAutoSetValue($aryVariant);
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if($this->getUpdateMode()===true){
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecDelete" ){
				//----廃止の場合のみ、存在すれば、レコードを更新
				list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqOrgData,array($this->getID()),null);
				if( $tmpBoolKeyExist===true ){
					$exeQueryData[$this->getID()] = $varValue;
				}
				//廃止の場合のみ、存在すれば、レコードを更新----
			}
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}
	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getAutoSetValue(&$aryVariant=array()){
		global $g;
		$retStrVal="";
		if($g['db_model_ch'] === 0){
			$retStrVal = $g['objDBCA']->getQueryTime();
		}else if($g['db_model_ch'] === 1){
			$retStrVal = $g['objDBCA']->getQueryTime();
		}
		return $retStrVal;
	}
	//TableIUDイベント系----

	//NEW[2]
	function setUpdateMode($boolValue){
		$this->boolUpdateMode = $boolValue;
	}
	//NEW[3]
	function getUpdateMode(){
		return $this->boolUpdateMode;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class LastUpdateDateColumn extends AutoUpdateTimeColumn {

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText="LAST_UPDATE_TIMESTAMP",$strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="最終更新日時"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11701");
		}
		parent::__construct($strColIdText, $strColExplain);
		$this->setHiddenMainTableColumn(true);

		//----このインスタンスに紐づくOutputTypeインスタンスにアクセスする
		$this->getOutputType("print_table")->init($this, "print_table");
		$this->getOutputType("filter_table")->init($this, "filter_table");
		//----このインスタンスに紐づくOutputTypeインスタンスにアクセスする----

		$this->getOutputType("update_table")->setVisible(true);
		$this->getOutputType("register_table")->setVisible(true);
		$this->getOutputType("delete_table")->setVisible(true);

		//$this->setDescription("レコードの最終更新日。更新可否判定に使用。自動登録のため編集不可。");
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11702"));
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}
//ここまでDateカラム系----

class JournalRegClassColumn extends TextColumn{

	//----ここから継承メソッドの上書き処理

	function __construct($strColId="JOURNAL_ACTION_CLASS",$strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="履歴種類"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11801");
		}
		parent::__construct($strColId, $strColExplain);
		$this->setDBColumn(false);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("print_table")->setVisible(false);
		$this->getOutputType("print_journal_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("excel")->setVisible(false);
		$this->getOutputType("csv")->setVisible(false);
		
		$this->getOutputType("json")->setVisible(false);
	}

	//----TableIUDイベント系
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		// JOURNAL専用のカラムなので、$reqOrgData、に代入してはならない
		$exeQueryData[$this->getID()] = array('JNL'=>$this->getAutoSetValue($aryVariant));
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getAutoSetValue(&$aryVariant=array()){
		$retStrVal = "";
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" ){
			$retStrVal = "INSERT";
		}else if( $modeValue=="DTUP_singleRecUpdate" ){
			$retStrVal = "UPDATE";
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			$modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];
			if($modeValue_sub == "on"){
				$retStrVal = "L_DELETE";
			}else if($modeValue_sub == "off"){
				$retStrVal = "L_REVIVE";
			}else{
				$retStrVal = "";
			}
		}
		return $retStrVal;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class LastUpdateDate4UColumn extends TextColumn {

	protected $strConDateFormat;

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText="UPD_UPDATE_TIMESTAMP",$strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="更新用の最終更新日時"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11901");
		}
		parent::__construct($strColIdText, $strColExplain);
		if($g['db_model_ch'] === 0){
			$this->strFxNameDateToStr = "TO_CHAR";
			$this->strConDateFormat = "YYYYMMDDHH24MISSFF6";
		}else if($g['db_model_ch'] === 1){
			$this->strFxNameDateToStr = "DATE_FORMAT";
			$this->strConDateFormat = "%Y%m%d%H%i%s%f";
		}
		$this->isDBColumn(true);
		$this->setDeleteOnBeforeCheck(true);
		$this->setDeleteOffBeforeCheck(true);

		//----ファイルアップロードによるテーブル更新時に必ず比較されるので更新対象扱い
		$this->setAllowSendFromFile(true);
		//ファイルアップロードによるテーブル更新時に必ず比較されるので更新対象扱い----

		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("print_table")->setVisible(false);
		$this->getOutputType("print_journal_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("excel")->setVisible(true);
		$this->getOutputType("csv")->setVisible(true);
		
		$this->getOutputType("json")->setVisible(true);
		
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-11902"));
	}
	function getPartSqlInSelectZone(){
		global $g;
		$retStrQuery="";
		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strWpColOtherId = "{$dbQM}{$this->objTable->getRequiredLastUpdateDateColumnID()}{$dbQM}";

		//文字列として扱うため、プレフィクスとして"T_"を付与
		$strConStr1 = makeStringConnectForSQLPart($g['db_model_ch'],array("'T_'","{$this->strFxNameDateToStr}({$strWpTblSelfAlias}.{$strWpColOtherId}, '{$this->strConDateFormat}')"));

		if( $strConStr1 != "" ){
			$retStrQuery  = "(CASE WHEN {$strWpTblSelfAlias}.{$strWpColOtherId} IS NOT NULL THEN {$strConStr1} ";
			$retStrQuery .= "ELSE 'T_' END) {$strWpColId}";
		}
		return $retStrQuery;
	}


	//----TableIUDイベント系
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if(array_key_exists($this->getID(),$exeQueryData)===true){
			// 実際のDBにないカラムなので、beforeTableIUDAction、で除去すること
			unset($exeQueryData[$this->getID()]);
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class AutoUpdateUserColumn extends TextColumn {
	protected $boolUpdateMode;

	protected $strRefJoinTableId;
	protected $strRefJoinColumnId;
	protected $strRefShowColumnId;

	protected $intAutoUpdateUserNo;

	protected $strAliasIdPrefix;
	protected $arrayFixSet;

	protected $strJournalTableOfMaster;
	protected $strJournalSeqIdOfMaster;
	protected $strJournalKeyIdOfMaster;
	protected $strJournalDispIdOfMaster;
	protected $strJournalLUTSIdOfMaster;

	protected $errMsgHead;
	protected $errMsgTail;

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText, $strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="更新者"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12001");
		}
		parent::__construct($strColIdText, $strColExplain);
		$this->setHiddenMainTableColumn(true);
		$this->setHeader(true);
		$this->setSearchType("in");

		//----エクセルからの抽出はしない
		$this->setAllowSendFromFile(false);
		//エクセルからの抽出はしない----
		
		//$strStaticTextBody = "自動入力";
		$strStaticTextBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12002");

		$outputType = new AUUOutputType(new SortedTabHFmt(), new TextTabBFmt());

		$this->setOutputType("print_table", $outputType);
		$outputType = new AUUOutputType(new TabHFmt(), new TextTabBFmt());
		$this->setOutputType("print_journal_table", $outputType);

		$this->setOutputType("update_table", new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));
		$this->setOutputType("register_table", new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));
		$this->setOutputType("delete_table", new OutputType(new TabHFmt(), new StaticTextTabBFmt($strStaticTextBody)));

		$this->setOutputType("excel",new AUUOutputType(new ExcelHFmt(), new ExcelBFmt()));
		$this->setOutputType("csv",new AUUOutputType(new CSVHFmt(), new CSVBFmt()));
		$this->setOutputType("json",new AUUOutputType(new JSONHFmt(), new JSONBFmt()));
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);

		//$this->setDescription("更新者。ログインユーザのIDが自動的に登録される。編集不可。");
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-12003"));

		$this->setSelectTagCallerShow(true);

		$this->setUpdateMode(true);
		$this->setDeleteOnBeforeCheck(true);
		$this->setDeleteOffBeforeCheck(true);

		$this->setRefJoinTableID("A_ACCOUNT_LIST");
		$this->setRefJoinColumnID("USER_ID");
		$this->setRefShowColumnID("USERNAME_JP");

		$this->setJournalTableOfMaster("A_ACCOUNT_LIST_JNL");
		$this->setJournalSeqIDOfMaster("JOURNAL_SEQ_NO");
		$this->setJournalKeyIDOfMaster("USER_ID");
		$this->setJournalDispIDOfMaster("USERNAME_JP");
		$this->setJournalLUTSIDOfMaster("LAST_UPDATE_TIMESTAMP");

		$this->setAliasIdPrefix("TT_SYS_ALIAS_AUUC_");

		$this->setAutoUpdateUserNo(null);

		$this->arrayFixSet = array("(",")");

		$this->setValidator(new LongUserNameValidator());
	}

	function getAddSelectTagQuery($searchTblId, $strWhereAddBody=""){
		$retStrQuery = "";
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strWpTableId = "{$dbQM}{$searchTblId}{$dbQM}";

		$strTableJoin = "{$dbQM}{$this->getRefJoinTableID()}{$dbQM}";
		$strTblJoinAlias = "{$dbQM}JT{$this->getAutoUpdateUserNo()}{$dbQM}";

		$strJoinColKey = "{$dbQM}{$this->getRefJoinColumnID()}{$dbQM}";
		$strShowColKey = "{$dbQM}{$this->getRefShowColumnID()}{$dbQM}";

		$strWpDUFColId = "{$dbQM}{$this->getRequiredDisuseColumnID()}{$dbQM}";

		$strBodyFrom  = "{$strWpTableId} {$strWpTblSelfAlias} LEFT JOIN {$strTableJoin} {$strTblJoinAlias} ";
		$strBodyFrom .= "ON ( {$strWpTblSelfAlias}.{$strWpColId} = {$strTblJoinAlias}.{$strJoinColKey} ) ";

		$retStrQuery  = "SELECT DISTINCT {$strTblJoinAlias}.{$strShowColKey} {$dbQM}KEY_COLUMN{$dbQM} ";
		$retStrQuery .= "FROM {$strBodyFrom} ";
		$retStrQuery .= "WHERE {$strWpTblSelfAlias}.{$strWpColId} IS NOT NULL ";
		$retStrQuery .= "AND {$strWpTblSelfAlias}.{$strWpDUFColId} IN ('0','1') ";
		$retStrQuery .= "{$strWhereAddBody} ";
		$retStrQuery .= "ORDER BY {$strTblJoinAlias}.{$strShowColKey} ASC";

		$retArray = array($retStrQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		return $retArray;
	}

	function getPartSqlInSelectZone(){
		global $g;
		$retStrQuery="";
		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpColAliasId = "{$dbQM}{$this->getAliasIdPrefix()}{$this->getAutoUpdateUserNo()}{$dbQM}";

		$strTblJoinAlias = "{$dbQM}JT{$this->getAutoUpdateUserNo()}{$dbQM}";
		$strShowColKey = "{$dbQM}{$this->getRefShowColumnID()}{$dbQM}";

        $strConStr1 = makeStringConnectForSQLPart($g['db_model_ch'],array("'{$this->arrayFixSet[0]}'","{$strWpTblSelfAlias}.{$strWpColId}","'{$this->arrayFixSet[1]}'"));

		if( $strConStr1 != "" ){
			$retStrQuery  = "(CASE WHEN {$strTblJoinAlias}.{$strShowColKey} IS NULL THEN {$strConStr1} ";
			$retStrQuery .= "ELSE {$strTblJoinAlias}.{$strShowColKey} END) {$strWpColAliasId}";
			$retStrQuery .= ",";
			$retStrQuery .= "{$strWpTblSelfAlias}.{$strWpColId} {$strWpColId}";
		}
		return $retStrQuery;
	}

	function getFilterValuesCoreForDTiS(&$arrayFilterValues,$boolBinaryDistinctOnDTiS){
		$strRegexpFormat='/^0$|^-?[1-9][0-9]*$/s';
		$data = array();
		$arySource = $this->aryFilterValueRawBase;
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				if( preg_match($strRegexpFormat, $filter) === 1 ){
					//----半角数値(＝ユーザIDを指定している)評価できる場合
					$data[] = $filter;
					$tmpStrFilter = where_queryForLike_Wrapper($filter, $boolBinaryDistinctOnDTiS);
					$data[] = '%'.$tmpStrFilter.'%';
					unset($tmpStrFilter);
					//半角数値(＝ユーザIDを指定している)評価できる場合----
				}else{
					$filter = where_queryForLike_Wrapper($filter, $boolBinaryDistinctOnDTiS);

					$data[] = '%'.$filter.'%';
				}
				
			}
		}
		return $data;
	}

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		global $g;

		$retStrQuery = "";

		$strWFFCMInDBHead = "";
		$strWFFCMInDBTail = "";
		$strWFFCMInNeedTipHead = "";
		$strWFFCMInNeedTipTail = "";
		$strCollate="";
		if($g['db_model_ch'] == 0){
			if( $boolBinaryDistinctOnDTiS === false ){
				$strWFFCMInDBHead = "TO_VALUE_FOR_FAZZY_MATCH(";
				$strWFFCMInDBTail = ")";
				$strWFFCMInNeedTipHead = "";
				$strWFFCMInNeedTipTail = "";
			}
		}else if($g['db_model_ch'] == 1){
			if( $boolBinaryDistinctOnDTiS === false ){
				$strCollate = "COLLATE utf8_unicode_ci ";
			}
		}

		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";

		$strTblJoinAlias = "{$dbQM}JT{$this->getAutoUpdateUserNo()}{$dbQM}";
		$strShowColKey = "{$dbQM}{$this->getRefShowColumnID()}{$dbQM}";

		$strRegexpFormat='/^0$|^-?[1-9][0-9]*$/s';
		$arySource = $this->aryFilterValueRawBase;
		foreach($arySource as $key=>$val){
			if( preg_match($strRegexpFormat, $val) === 1 ){
				//----半角数値(＝ユーザIDを指定している)評価できる場合
				$retStrQuery  = "({$strWpTblSelfAlias}.{$strWpColId} IN (:{$this->getID()}__0)";
				//$retStrQuery .= " OR {$strTblJoinAlias}.{$strShowColKey} {$strCollate}LIKE :{$this->getID()}__1)";
				$retStrQuery .= " OR {$strWFFCMInDBHead}{$strTblJoinAlias}.{$strShowColKey}{$strWFFCMInDBTail}";
				$retStrQuery .= " {$strCollate}LIKE {$strWFFCMInNeedTipHead}:{$this->getID()}__1{$strWFFCMInNeedTipTail} ESCAPE '#')";
				//半角数値(＝ユーザIDを指定している)評価できる場合----
			}else{
				//$retStrQuery    = "( {$strTblJoinAlias}.{$strShowColKey} {$strCollate}LIKE :{$this->getID()}__0 ESCAPE '#')";
				$retStrQuery    = "( {$strWFFCMInDBHead}{$strTblJoinAlias}.{$strShowColKey}{$strWFFCMInDBTail}";
				$retStrQuery   .= " {$strCollate}LIKE {$strWFFCMInDBHead}:{$this->getID()}__0{$strWFFCMInDBTail} ESCAPE '#')";
			}
		}
		return $retStrQuery;
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		global $g;
		$retStrQuery = "";
		$dbQM=$this->objTable->getDBQuoteMark();

		$tmpArray = array();
		$intFilterCount = 0;

		$strTblJoinAlias = "{$dbQM}JT{$this->getAutoUpdateUserNo()}{$dbQM}";
		$strShowColKey = "{$dbQM}{$this->getRefShowColumnID()}{$dbQM}";

		if($g['db_model_ch'] == 0){
			//----バイナリで精密な一致
			$strWrapHead="NLSSORT(";
			$strWrapTail=",'NLS_SORT=BINARY')";
			//バイナリで精密な一致----
		}else if($g['db_model_ch'] == 1){
			$strWrapHead="";
			$strWrapTail="";
		}
		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strTblJoinAlias}.{$strShowColKey}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ") ";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	function getNullSearchQuery(){
		//----WHERE句[1]
		global $g;
		
		$retStrQuery = "";

		$dbQM=$this->objTable->getDBQuoteMark();
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";

		if( $g['db_model_ch'] == 0 ){
			//----ORACLE
			$retStrQuery  = " {$strWpTblSelfAlias}.{$strWpColId} IS NULL ";
			//ORACLE----
		}else if( $g['db_model_ch'] == 1 ){
			//----mySQL/mariaDB
			$retStrQuery  = " ({$strWpTblSelfAlias}.{$strWpColId} IS NULL OR {$strWpTblSelfAlias}.{$strWpColId} = '') ";
			//mySQL/mariaDB----
		}
		return $retStrQuery;
	}

	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if($this->getUpdateMode()===true){
			//----更新するモードの場合に、ログインしている者の、ログインIDを入力
			// JOURNAL専用のカラムではないので、beforeIUDValidateCheckのタイミングでは、$exeQueryData、に直接代入してはならない
			$reqOrgData[$this->getID()] = $this->getAutoSetValue($aryVariant);
			//更新するモードの場合に、ログインしている者の、ログインIDを入力----
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if($this->getUpdateMode()===true){
			$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
			if( $modeValue=="DTUP_singleRecDelete" ){
				//----廃止の場合のみ、存在すれば、レコードを更新
				list($varValue,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($reqOrgData,array($this->getID()),null);
				if( $tmpBoolKeyExist===true ){
					$exeQueryData[$this->getID()] = $varValue;
				}
				//廃止の場合のみ、存在すれば、レコードを更新----
			}
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getUpdateMode(){
		return $this->boolUpdateMode;
	}
	//NEW[2]
	function setUpdateMode($boolValue){
		$this->boolUpdateMode = $boolValue;
	}
	//NEW[3]
	function setSqlFixSet($array){
		$this->arrayFixSet = $array;
	}
	//NEW[4]
	function getSqlFixSet(){
		return $this->arrayFixSet;
	}
	//NEW[5]
	function setAutoUpdateUserNo($intValue){
		$this->intAutoUpdateUserNo = $intValue;
	}
	//NEW[6]
	function getAutoUpdateUserNo(){
		return $this->intAutoUpdateUserNo;
	}
	//NEW[7]
	function setAliasIdPrefix($strValue){
		$this->strAliasIdPrefix = $strValue;
	}
	//NEW[8]
	function getAliasIdPrefix(){
		return $this->strAliasIdPrefix;
	}

	//----UTN(CURRENT)
	//NEW[9]
	function setRefJoinTableID($strValue){
		$this->strRefJoinTableId = $strValue;
	}
	//NEW[10]
	function getRefJoinTableID(){
		return $this->strRefJoinTableId;
	}
	//NEW[11]
	function setRefJoinColumnID($strValue){
		$this->strRefJoinColumnId = $strValue;
	}
	//NEW[12]
	function getRefJoinColumnID(){
		return $this->strRefJoinColumnId;
	}
	//NEW[13]
	function setRefShowColumnID($strValue){
		$this->strRefShowColumnId = $strValue;
	}
	//NEW[14]
	function getRefShowColumnID(){
		return $this->strRefShowColumnId;
	}
	//UTN(CURRENT)----

	//----JNL
	//----履歴用マスターのジャーナル系
	//NEW[15]
	function setJournalTableOfMaster($jouranlTableOfMaster){
		$this->journalTableOfMaster = $jouranlTableOfMaster;
	}
	//NEW[16]
	function getJournalTableOfMaster(){
		return $this->journalTableOfMaster;
	}
	//NEW[17]
	function setJournalSeqIDOfMaster($jouranlSeqIdOfMaster){
		$this->journalSeqIdOfMaster = $jouranlSeqIdOfMaster;
	}
	//NEW[18]
	function getJournalSeqIDOfMaster(){
		return $this->journalSeqIdOfMaster;
	}
	//NEW[19]
	function setJournalKeyIDOfMaster($jouranlKeyIdOfMaster){
		$this->journalKeyIdOfMaster = $jouranlKeyIdOfMaster;
	}
	//NEW[20]
	function getJournalKeyIDOfMaster(){
		return $this->journalKeyIdOfMaster;
	}
	//NEW[21]
	function setJournalDispIDOfMaster($jouranlDispIdOfMaster){
		$this->journalDispIdOfMaster = $jouranlDispIdOfMaster;
	}
	//NEW[22]
	function getJournalDispIDOfMaster(){
		return $this->journalDispIdOfMaster;
	}
	//NEW[23]
	function setJournalLUTSIDOfMaster($jouranlLUTSIdOfMaster){
		$this->journalLUTSIdOfMaster = $jouranlLUTSIdOfMaster;
	}
	//NEW[24]
	function getJournalLUTSIDOfMaster(){
		return $this->journalLUTSIdOfMaster;
	}
	//履歴用マスターのジャーナル系----	
	//JNL----

	//NEW[25]
	function setErrMsgHead($strValue){
		$this->errMsgHead = $strValue;
	}
	//NEW[26]
	function getErrMsgHead(){
		return $this->errMsgHead;
	}
	//NEW[27]
	function setErrMsgTail($strValue){
		$this->errMsgTail = $strValue;
	}
	//NEW[28]
	function getErrMsgTail(){
		return $this->errMsgTail;
	}

	//----TableIUDイベント系
	//NEW[29]
	function getAutoSetValue(&$aryVariant=array()){
		$retStrVal = "";
		global $g;
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" ){
			$retStrVal = $g['login_id'];
		}else if( $modeValue=="DTUP_singleRecUpdate" ){
			$retStrVal = $g['login_id'];
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			$retStrVal = $g['login_id'];
		}
		return $retStrVal;
	}
	//TableIUDイベント系----

	//ここまで新規メソッドの定義宣言処理----

}

class LastUpdateUserColumn extends AutoUpdateUserColumn {

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText="LAST_UPDATE_USER",$strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="最終更新者""
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12101");
		}
		parent::__construct($strColIdText, $strColExplain);
		$this->getOutputType("update_table")->setVisible(true);
		$this->getOutputType("register_table")->setVisible(true);
		$this->getOutputType("delete_table")->setVisible(true);
		//----このインスタンスに紐づくOutputTypeインスタンスにアクセスする
		$this->getOutputType("print_table")->init($this, "print_table");
		$this->getOutputType("filter_table")->init($this, "filter_table");
		//このインスタンスに紐づくOutputTypeインスタンスにアクセスする----

	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class WhereQueryColumn extends Column { 

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel){
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(false);
		$this->setHeader(false);
		$outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt());
		$outputType->setVisible(false);
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("print_journal_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new TextFilterTabBFmt());
		$this->setOutputType("filter_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("register_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("update_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("delete_table", $outputType);
		$outputType = new OutputType(new ExcelHFmt(), new StaticBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("excel", $outputType);
		$outputType = new OutputType(new CSVHFmt(), new StaticCSVBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("csv", $outputType);
		$outputType = new OutputType(new JSONHFmt(), new StaticBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("json", $outputType);
		$this->setSearchType("other");
	}

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;
		$retStrQuery = "";
		return $retStrQuery;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class RangeInQueryColumn extends WhereQueryColumn {
	//----開始カラム
	protected $strColIdOfRangeStart;
	protected $boolStartEqRangeOut; //----スタート値と等しい場合は除外する
	//開始カラム----
	
	//----終端カラム
	protected $strColIdOfRangeEnd;
	protected $boolEndEqRangeOut; //----エンド値と等しい場合は除外する
	//終端カラム----

	protected $strDateFormat;
	protected $strFxNameStrToDate;

	protected $strFilterInputType;
	protected $boolFilterSecondInput;
	protected $intFilterMinuteScale;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strColIdOfRangeStart, $strColIdOfRangeEnd, $strFilterType=""){
		parent::__construct($strColId, $strColLabel);
		$this->setSearchType("other");

		$this->setColIDOfRangeStart($strColIdOfRangeStart);
		$this->setStartEqualValueRangeOut(false);
		
		$this->setColIDOfRangeEnd($strColIdOfRangeEnd);
		$this->setEndEqualValueRangeOut(false);
		
		$this->setFilterInputType($strFilterType);
	}

	function getFilterConvertValue($value){
		//----WHERE句[-]
		$retStrValue = "";
		if( $this->strFxNameStrToDate != "" ){
			$retStrValue = "{$this->strFxNameStrToDate}({$value}, '{$this->strDateFormat}')";
		}else{
			$retStrValue = $value;
		}
		return $retStrValue;
	}

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;
		
		$retStrQuery = "";

		$dbQM = $this->objTable->getDBQuoteMark();
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$strWpIdOfRangeStartColumn = "{$dbQM}{$this->strColIdOfRangeStart}{$dbQM}";
		$strWpIdOfRangeEndColumn = "{$dbQM}{$this->strColIdOfRangeEnd}{$dbQM}";

		$strValueForFilter = $this->getFilterConvertValue(":{$this->getID()}__0");

		$arySource = $this->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
		foreach($arySource as $filter){
			if( 0 < strlen($filter) ){
				//----両方がNULLではなく
				$retStrQuery  = "({$strWpTblSelfAlias}.{$strWpIdOfRangeStartColumn} IS NOT NULL OR {$strWpTblSelfAlias}.{$strWpIdOfRangeEndColumn} IS NOT NULL)";
				//両方がNULLではなく----
				//----開始がNULLまたは条件値よりも小さい
				if( $this->boolStartEqRangeOut !== true ){
					$strCheckPart01 = "<=";
				}else{
					$strCheckPart01 = "<";
				}
				$retStrQuery .= " AND ({$strWpTblSelfAlias}.{$strWpIdOfRangeStartColumn} IS NULL OR ({$strWpTblSelfAlias}.{$strWpIdOfRangeStartColumn} IS NOT NULL AND {$strWpTblSelfAlias}.{$strWpIdOfRangeStartColumn} {$strCheckPart01} {$strValueForFilter})) ";
				//開始がNULLまたは条件値よりも小さい----
				//----終端がNULLまたは条件値よりも大きい
				if( $this->boolEndEqRangeOut !== true ){
					$strCheckPart02 = "<=";
				}else{
					$strCheckPart02 = "<";
				}
				$retStrQuery .= " AND ({$strWpTblSelfAlias}.{$strWpIdOfRangeEndColumn} IS NULL OR ({$strWpTblSelfAlias}.{$strWpIdOfRangeEndColumn} IS NOT NULL AND {$strValueForFilter} {$strCheckPart02} {$strWpTblSelfAlias}.{$strWpIdOfRangeEndColumn})) ";
				//終端がNULLまたは条件値よりも大きい----
			}
		}
		return $retStrQuery;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//----開始定義カラム
	//NEW[1]
	function setColIDOfRangeStart($strColId){
		$this->strColIdOfRangeStart = $strColId;
	}
	//NEW[2]
	function getColIDOfRangeStart(){
		return $this->strColIdOfRangeStart;
	}
	//NEW[3]
	function setStartEqualValueRangeOut($boolValue){
		$this->boolStartEqRangeOut = $boolValue;
	}
	//NEW[4]
	function getStartEqualValueRangeOut(){
		return $this->boolStartEqRangeOut;
	}
	//開始定義カラム----
	
	//----終端定義カラム
	//NEW[5]
	function setColIDOfRangeEnd($strColId){
		$this->strColIdOfRangeEnd = $strColId;
	}
	//NEW[6]
	function getColIDOfRangeEnd(){
		return $this->strColIdOfRangeEnd;
	}
	//NEW[7]
	function setEndEqualValueRangeOut($boolValue){
		$this->boolEndEqRangeOut = $boolValue;
	}
	//NEW[8]
	function getEndEqualValueRangeOut(){
		return $this->boolEndEqRangeOut;
	}
	//終端定義カラム----

	//----フィルター用インプット
	//NEW[9]
	function setFilterInputType($strType){
		global $g;
		$this->strFxNameStrToDate = "";
		$this->strDateFormat = "";
		if($strType == "DATETIME"){
			$this->setOutputType("filter_table", new OutputType(new FilterTabHFmt(), new DateRangeInFilterTabBFmt()));
			$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));
			$this->setMinuteScaleInputOnFilter(5);
			$this->setSecondsInputOnFilter(true);
			$this->setEvent("filter_table", "onclose", "search_async", array("'idcolumn_filter_default'"));
			if($g['db_model_ch'] === 0){
				$this->strFxNameStrToDate = "TO_TIMESTAMP";
				$this->strDateFormat      = "YYYY/MM/DD HH24:MI:SS";
			}else if($g['db_model_ch'] === 1){
				$this->strFxNameStrToDate = "STR_TO_DATE";
				$this->strDateFormat = "%Y/%m/%d %H:%i:%s";
			}
		}else if($strType == "DATE"){
			$this->setOutputType("filter_table", new OutputType(new FilterTabHFmt(), new DateRangeInFilterTabBFmt()));
			$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));
			$this->setMinuteScaleInputOnFilter(null);
			$this->setSecondsInputOnFilter(false);
			$this->setEvent("filter_table", "onclose", "search_async", array("'idcolumn_filter_default'"));
			if($g['db_model_ch'] === 0){
				$this->strFxNameStrToDate = "TO_TIMESTAMP";
				$this->strDateFormat      = "YYYY/MM/DD";
			}else if($g['db_model_ch'] === 1){
				$this->strFxNameStrToDate = "STR_TO_DATE";
				$this->strDateFormat      = "%Y/%m/%d";
			}
		}else{
			$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));
			$strType = "Other";
		}
		$this->strFilterInputType = $strType;
	}
	//NEW[10]
	function getFilterInputType(){
		return $this->strFilterInputType;
	}
	//NEW[11]
	function setMinuteScaleInputOnFilter($intValue){
		$this->intFilterMinuteScale = $intValue;
	}
	//NEW[12]
	function getMinuteScaleInputOnFilter(){
		return $this->intFilterMinuteScale;
	}
	//NEW[13]
	function setSecondsInputOnFilter($boolValue){
		$this->boolFilterSecondInput = $boolValue;
	}
	//NEW[14]
	function getSecondsInputOnFilter(){
		return $this->boolFilterSecondInput;
	}
	//フィルター用インプット----

	//ここまで新規メソッドの定義宣言処理----

}

//class IDRelaySearchColumn extends TextColumn {
class IDRelaySearchColumn extends WhereQueryColumn {

	protected $objIDColumn;
	protected $strTargetColumnId;

	protected $aryRelayInfoFromMasterToPrime;
	protected $aryPrimeMasterTableInfo;

	protected $intSelectTagPrintType;


	protected $aryFilterValueRawBase; // as array of string DBの検索条件(ユーザーの生値)
	protected $aryRichFilterValueRawBase; // as array of string DBの検索条件(ユーザーの生値)

	protected $aryFilterValueForDTiS;
	protected $aryRichValueForDTiS;

	protected $aryFilterValueForMatchCheck;
	protected $aryRichValueForMatchCheck;

	protected $boolNotFoundFlag;

	protected $aryPrimeMasterSet;

	function __construct($strColId, $strColLabel, $objTargetColumnId, $aryRelayInfoFromMasterToPrime=array()){
		parent::__construct($strColId, $strColLabel);
		$this->isDBColumn(false);
		$this->setSearchType("like");

		$outputType = new OutputType(new FilterTabHFmt(), new TextFilterTabBFmt());
		$this->setOutputType("filter_table", $outputType);

		$this->setSelectTagCallerShow(true);

		$this->strTargetColumnId = $objTargetColumnId;

		$this->aryRelayInfoFromMasterToPrime = $aryRelayInfoFromMasterToPrime;

		$this->aryPrimeMasterTableInfo = array();

		$this->setAddSelectTagPrintType(1);

		$this->boolNotFoundFlag = false;

		$this->aryPrimeMasterSet = null;
		
		$this->setValidator(new IDValidator($this));
	}

	function setAddSelectTagPrintType($intValue){
		$this->intSelectTagPrintType = $intValue;
	}
	function getAddSelectTagPrintType(){
		//(鍵と表示が異なるので1固定)から(仮想Distictマスタモード機能追加により可変へ)
		return $this->intSelectTagPrintType;
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		global $g;
		$retStrQuery = "";

		if( $this->boolNotFoundFlag===true ){
			$retStrQuery = " 1 = 0 ";
		}else{
			$objMainColumn = $this->objIDColumn;

			$dbQM = $this->objTable->getDBQuoteMark();

			$strWpTgtColId = "{$dbQM}{$objMainColumn->getID()}{$dbQM}";
			$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

			$tmpArray = array();
			$intFilterCount = 0;

			$strWrapHead= "";
			$strWrapTail= "";

			//----メインテーブルの鍵カラムが数値ではなく文字列型の場合
			if( $objMainColumn->getMasterKeyColumnType()===1 ){
				if( $g['db_model_ch'] == 0 ){
					//----バイナリで精密な一致
					$strWrapHead="NLSSORT(";
					$strWrapTail=",'NLS_SORT=BINARY')";
					//バイナリで精密な一致----
				}else if( $g['db_model_ch'] == 1 ){
					$strWrapHead= "";
					$strWrapTail= "";
				}
			}
			//メインテーブルの鍵カラムが数値ではなく文字列型の場合----

			$arySourceFromDTiS = $this->getRichFilterValuesForDTiS(true);
			foreach($arySourceFromDTiS as $filter){
				if( 0 < strlen($filter) ){
					$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
					$intFilterCount++;
				}
			}
			if( 0 < count($tmpArray) ){
				//----IN候補型の検出条件クエリを作成
				$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpTgtColId}{$strWrapTail}";
				$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
				//IN候補型の検出条件クエリを作成----
			}
		}
		return $retStrQuery;
	}

	function getNullSearchQuery(){
		//----WHERE句[1]
		global $g;
		$retStrQuery = "";

		$objMainColumn = $this->objIDColumn;

		$dbQM = $this->objTable->getDBQuoteMark();

		$strWpTgtColId = "{$dbQM}{$objMainColumn->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		if( $g['db_model_ch'] == 0 ){
			//----ORACLE
			$retStrQuery = " {$strWpTblSelfAlias}.{$strWpTgtColId} IS NULL ";
			//ORACLE----
		}else if( $g['db_model_ch'] == 1 ){
			//----mySQL/mariaDB
			$retStrQuery = " {$strWpTblSelfAlias}.{$strWpTgtColId} IS NULL ";
			if( $objMainColumn->getMasterKeyColumnType()===1 ){
				//----文字列キー型の場合は、空文字も検出する
				$retStrQuery = " ({$strWpTblSelfAlias}.{$strWpTgtColId} IS NULL OR {$strWpTblSelfAlias}.{$strWpTgtColId} = '') ";
				//文字列キー型の場合は、空文字も検出する----
			}
			//mySQL/mariaDB----
		}
		return $retStrQuery;
	}

	function getAddSelectTagQuery($searchTblId, $strWhereAddBody=""){
		//strWhereAddBody・・基本サポートしていないパラメータ
		global $g;
		$intControlDebugLevel01 = 50;

		$retVarQuery = null;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$retArrayForBind = array();

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$boolExecute = true;

		$objMainColumn = $this->objIDColumn;
		$objTable = $this->getTable();

		$mainTableBody = $objTable->getDBMainTableBody();

		//----蓄積系テーブル側
		$strDUColumnOfMainTable = $objTable->getRequiredDisuseColumnID();
		$strMainIdColumnId = $objMainColumn->getID();
		//蓄積系テーブル側----

		$aryEtcetera = $objMainColumn->getEtceteraParameter();

		//----[1]（メインテーブルの対象列から、存在するパターンを取得
		if( $boolExecute===true ){
			$strNullPartWhereQuery = $this->getWhereQueryForZeroLengthRecord($objMainColumn->getMasterKeyColumnType(),$strMainIdColumnId);

			$str1stQuery = "SELECT DISTINCT {$strMainIdColumnId} KEY_COLUMN "
						  ."FROM {$mainTableBody} "
						  ."WHERE {$strNullPartWhereQuery} "
						  ."AND {$strDUColumnOfMainTable} IN ('0','1') "
						  ."{$strWhereAddBody}";

			$arrayBindElement = array();
			$arraySelected = array();
			$retArray = singleSQLExecuteAgent($str1stQuery, $arrayBindElement, $strFxName);
			if( $retArray[0]===true ){
				$objQuery =& $retArray[1];

				while ( $row = $objQuery->resultFetch() ){
					$arraySelected[] = $row['KEY_COLUMN'];
				}
				unset($objQuery);
			}else{
				$boolExecute = false;
				$intErrorType = 500;
				//CLONE KEY DATA IN MAINTABLE NOT FOUND ERROR [｛｝].
				$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20211",array($this->getSelfInfoForLog()));
				web_log($tmpStrForLogOutput);
				dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
			}
		}
		//[1]（メインテーブルの対象列から、存在するパターンを取得----

		//----[2]配列の個数分(最終の直前まで)、SQL発行を繰り返す
		if( $boolExecute===true ){
			if( 0===count($arraySelected) ){
				//----該当列にデータがなかったので、SQLを作らないが、正常($intErrorType=null)として返す
				$boolExecute = false;
				//該当列にデータがなかったので、SQLを作らないが、正常($intErrorType=null)として返す----
			}else{
				$intCountMaster = count($this->aryRelayInfoFromMasterToPrime);
				$intFoucsCount = 0;
				foreach($this->aryRelayInfoFromMasterToPrime as $arySingleInfoOfMidTable){
					$intFoucsCount += 1;
					if( $intFoucsCount==$intCountMaster ){
						break;
					}

					list($strSchTgtTableBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_TARGET_TABLE"),'');

					list($strColIdOfKeyNearMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_MASTER_COLUMN_ID"),'');
					list($strColTypeOfNearMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_MASTER_COLUMN_TYPE"),0);

					list($strColIdOfKeyNearPrime,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_PRIME_COLUMN_ID"),'');
					list($strColTypeOfNearPrime,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_PRIME_COLUMN_TYPE"),0);

					list($strColIdOfDisuseFlag,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_DISUSE_FLAG_COLUMN_ID"),'DISUSE_FLAG');
					list($strWhereAddBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_WHERE_ADD"),'');

					$aryDataForQuery = $this->getQueryInPlacesFromSelected($strColIdOfKeyNearMaster, $arraySelected);
					$strWhereKeyNearMasterBody = $aryDataForQuery[0];
					$aryValueForBind = $aryDataForQuery[1];

					$strNullPartWhereQuery = $this->getWhereQueryForZeroLengthRecord($strColTypeOfNearPrime,$strColIdOfKeyNearPrime);
					$strMidQuery = "SELECT DISTINCT {$strColIdOfKeyNearPrime} KEY_COLUMN "
								  ."FROM {$strSchTgtTableBody} "
								  ."WHERE {$strNullPartWhereQuery} "
								  ."AND {$strColIdOfDisuseFlag} IN ('0','1') "
								  ."AND {$strWhereKeyNearMasterBody} "
								  ."{$strWhereAddBody}";

					$arraySelected = array();
					$retArray = singleSQLExecuteAgent($strMidQuery, $aryValueForBind, $strFxName);
					if( $retArray[0]!==true ){
						$boolExecute = false;
						$intErrorType = 500;
						// RERAY DATA BOUND FOR PRIME MASTER SELECT SQL IS ERROR OCCURRED ON LOOP([｛｝] OF [｛｝]) [｛｝].
						$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20212",array($intFoucsCount,$intCountMaster,$this->getSelfInfoForLog()));
						web_log($tmpStrForLogOutput);
						dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
						break;
					}

					$objQuery =& $retArray[1];

					while ( $row = $objQuery->resultFetch() ){
						$arraySelected[] = $row['KEY_COLUMN'];
					}
					unset($objQuery);

					if( 0===count($arraySelected) ){
						//----途中で途切れたので、システムエラー
						$boolExecute = false;
						$intErrorType = 500;
						// RERAY DATA BOUND FOR PRIME MASTER IS BROKEN ON LOOP([｛｝] OF [｛｝]) [｛｝].
						$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20213",array($intFoucsCount,$intCountMaster,$this->getSelfInfoForLog()));
						web_log($tmpStrForLogOutput);
						dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
						break;
						//途中で途切れたので、システムエラー----
					}
				}
			}
		}
		//[2]配列の個数分(最終の直前まで)、SQL発行を繰り返す----
		
		//----[3]最終的にプルダウン表示させるのに使うマスタ情報からSQL作成
		if( $boolExecute===true ){
			$aryPrimeMasterInfo = $this->aryPrimeMasterTableInfo;

			list($strPrimeMasterTableBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_TARGET_TABLE"),'');

			list($strColIdOfKeyOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_ID"),'');
			list($strColTypeOfKeyOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_TYPE"),0);

			list($strColIdOfDisplayOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_ID"),'');
			list($strColTypeOfDisplayOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_TYPE"),1);

			list($strColIdOfDisuseFlag,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_DISUSE_FLAG_COLUMN_ID"),'DISUSE_FLAG');
			list($strWhereAddBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_WHERE_ADD"),'');

			$aryDataForQuery = $this->getQueryInPlacesFromSelected($strColIdOfKeyOfPrimeMaster, $arraySelected);
			$strWhereKeyNearMasterBody = $aryDataForQuery[0];
			$aryValueForBind = $aryDataForQuery[1];

			$strNullPartWhereQuery = $this->getWhereQueryForZeroLengthRecord(0,$strColIdOfKeyOfPrimeMaster);

			$strMidQuery = "SELECT {$strColIdOfKeyOfPrimeMaster} KEY_COLUMN, {$strColIdOfDisplayOfPrimeMaster} DISP_COLUMN "
			  ."FROM {$strPrimeMasterTableBody} "
			  ."WHERE {$strNullPartWhereQuery} "
			  ."AND {$strColIdOfDisuseFlag} IN ('0','1') "
			  ."AND {$strWhereKeyNearMasterBody} "
			  ."{$strWhereAddBody} "
			  ."ORDER BY DISP_COLUMN ASC";
			$retArrayForBind = $aryValueForBind;
			$retVarQuery = $strMidQuery;
			
			if( $this->getAddSelectTagPrintType()===0 ){
				//----表示列側での仮想マスタテーブルモードの場合
				$retVarQuery = null;
				$retArrayForBind = array();
				$retArray = singleSQLExecuteAgent($strMidQuery, $aryValueForBind, $strFxName);
				if( $retArray[0]!==true ){
					$boolExecute = false;
					$intErrorType = 500;
					// ERROR OCCURED ON SELECT FROM PRIME MASTER [｛｝].
					$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20214",array($this->getSelfInfoForLog()));
					web_log($tmpStrForLogOutput);
					dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
				}
				else{
					$objQuery =& $retArray[1];

					$arraySelected = array();
					$arrayCheckRegistered = array();
					while ( $row = $objQuery->resultFetch() ){
						$checkKey = base64_encode($row['DISP_COLUMN']);
						if( array_key_exists($checkKey,$arrayCheckRegistered)===false ){
							//----登録されていないものだけ登録
							$arraySelected[] = array('KEY_COLUMN'=>$row['DISP_COLUMN']);
							$arrayCheckRegistered[$checkKey] = 1;
							//登録されていないものだけ登録----
						}
					}
					unset($objQuery);

					$retVarQuery = new SelectedQueryFaker($arraySelected);
				}
				//表示列側での仮想マスタテーブルモードの場合----
			}
			
		}
		//[3]最終的にプルダウン表示させるのに使うマスタ情報からSQL作成----

		$retArray = array($retVarQuery,$intErrorType,$aryErrMsgBody,$strErrMsg,$retArrayForBind);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}

	//NEW[68]
	function afterFixColumn(){
		global $g;
		$boolRet = true;
		$objTable = $this->getTable();
		$objColumns = $objTable->getColumns();
		try{
			if( is_string($this->strTargetColumnId)===false ){
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			if( array_key_exists($this->strTargetColumnId, $objColumns)===false ){
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$objTgtIDColumn = $objColumns[$this->strTargetColumnId];
			if( is_a($objTgtIDColumn, 'IDColumn')===false ){
				throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$this->objIDColumn = $objTgtIDColumn;
			if( is_array($this->aryRelayInfoFromMasterToPrime)===false ){
				throw new Exception( '00000400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			//----原始マスタから、組み合わせマスタ順の情報を作成する
			$aryFromPrimeToMaster = array_reverse($this->aryRelayInfoFromMasterToPrime);
			//原始マスタから、組み合わせマスタ順の情報を作成する----

			$intCountMaster = count($aryFromPrimeToMaster);
			$intFoucsCount = 1;
			foreach($aryFromPrimeToMaster as $arySingleInfoOfMidTable){
				if( $intFoucsCount==1 ){
					$this->aryPrimeMasterTableInfo = $arySingleInfoOfMidTable;
					break;
				}
			}
			if( is_array($this->aryPrimeMasterTableInfo)===false ){
				throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90310108);
			exit();
		}
		return $boolRet;
	}
	//FixColumnイベント系----

	function beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, &$aryFilterData=array(), &$aryVariant=array()){
		global $g;
		$intControlDebugLevel01 = 50;

		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$boolExecute = true;

		$this->boolNotFoundFlag = false;

		$objMainColumn = $this->objIDColumn;

		$strWrapHead= "";
		$strWrapTail= "";

		$strWFFCMInDBHead = "";
		$strWFFCMInDBTail = "";
		$strWFFCMInNeedTipHead = "";
		$strWFFCMInNeedTipTail = "";
		$strCollate="";

		//----[1]最終的にプルダウン表示させるのに使うマスタから該当キーを抽出
		if( $boolExecute===true ){
			$aryPrimeMasterInfo = $this->aryPrimeMasterTableInfo;

			list($strPrimeMasterTableBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_TARGET_TABLE"),'');

			list($strColIdOfKeyOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_ID"),'');
			list($strColTypeOfKeyOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_TYPE"),0);

			list($strColIdOfDisplayOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_ID"),'');
			list($strColTypeOfDisplayOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_TYPE"),1);

			list($strColIdOfDisuseFlag,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_DISUSE_FLAG_COLUMN_ID"),'DISUSE_FLAG');
			list($strWhereAddBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_WHERE_ADD"),'');

			$arraySelected = array();
			$str1stQuery = "";
			switch($this->getSearchType()){
				case "like":
					if( $strColTypeOfDisplayOnPrimeMaster===1 ){
						//----原始マスタの表示列が、文字列型だった場合
						$aryValueForBind = array();
						$arySource = $this->getFilterValues();
						
						if( is_array($arySource)===true && 0 < count($arySource) ){
							$tmpArray = array();
							$tmpBindKey = '';
							$tmpIntFilterCount = 0;
							if($g['db_model_ch'] == 0){
								if( $boolBinaryDistinctOnDTiS === false ){
									$strWFFCMInDBHead = "TO_VALUE_FOR_FAZZY_MATCH(";
									$strWFFCMInDBTail = ")";
									$strWFFCMInNeedTipHead = "";
									$strWFFCMInNeedTipTail = "";
								}
							}else if($g['db_model_ch'] == 1){
								//----mySQL系DBの場合のあいまい検索
								if( $boolBinaryDistinctOnDTiS === false ){
									$strCollate = "COLLATE utf8_unicode_ci ";
								}
								//mySQL系DBの場合のあいまい検索----
							}

							foreach($arySource as $filter){
								if(0 < strlen($filter)){
									$filter = where_queryForLike_Wrapper($filter, $boolBinaryDistinctOnDTiS);
									$tmpBindKey = "BIND__{$tmpIntFilterCount}";
									$aryValueForBind[$tmpBindKey] = '%'.$filter.'%';
									$tmpStr01  = "{$strWFFCMInDBHead}{$strColIdOfDisplayOfPrimeMaster}{$strWFFCMInDBTail} ";
									$tmpStr01 .= " {$strCollate}LIKE {$strWFFCMInNeedTipHead}:{$tmpBindKey}{$strWFFCMInNeedTipTail} ESCAPE '#' ";
									$tmpArray[] = $tmpStr01;
								}
							}
							if( 0 < count($tmpArray) ){
								$str1stQuery  = "SELECT DISTINCT {$strColIdOfKeyOfPrimeMaster} KEY_COLUMN ";
								$str1stQuery .= "FROM {$strPrimeMasterTableBody} ";
								$str1stQuery .= "WHERE (".implode(" OR ", $tmpArray) . ") ";
								$str1stQuery .= "AND {$strColIdOfDisuseFlag} IN ('0','1') ";
							}
							unset($tmpBindKey);
							unset($tmpArray);
							unset($tmpIntFilterCount);
						}

						if( $str1stQuery!="" ){
							//----あいまいモードを一旦ON
							if( $boolExecute===true ){
								if( $boolBinaryDistinctOnDTiS===true ){
								}else{
									$boolFocusRet= dbSearchResultExpand();
									if($boolFocusRet === false){
										$boolExecute = false;
										$intErrorType = 500;
									}
								}
							}
							//あいまいモードを一旦ON----

							if( $boolExecute===true ){
								$retArray = singleSQLExecuteAgent($str1stQuery, $aryValueForBind, $strFxName);
								if( $retArray[0] === true ){
									$objQuery =& $retArray[1];

									while ( $row = $objQuery->resultFetch() ){
										$arraySelected[] = $row['KEY_COLUMN'];
									}
									unset($objQuery);
								}else{
									$boolExecute = false;
									$intErrorType = 500;
									//ERROR OCCURED ON SELECT WHERE LIKE MODE FROM PRIME MASTER [｛｝].
									$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20301",array($this->getSelfInfoForLog()));
									web_log($tmpStrForLogOutput);
									dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
								}
							}

							//----あいまいモードを一旦OFF
							if( $boolExecute===true ){
								if( $boolBinaryDistinctOnDTiS===true ){
								}else{
								    $boolFocusRet= dbSearchResultNormalize();
		                    		if($boolFocusRet === false){
										$boolExecute = false;
										$intErrorType = 500;
		                    		}
								}
							}
							//あいまいモードを一旦OFF----
						}
						
						break;
						//原始マスタの表示列が、文字列型だった場合----
					}
				case "in":

					$arySource = $this->getFilterValues();

					if( is_array($arySource)===true && 0 < count($arySource) ){

						$aryDataForQuery = $this->getQueryInPlacesFromSelected($strColIdOfDisplayOfPrimeMaster,$arySource);
						$strWhereKeyNearMasterBody = $aryDataForQuery[0];
						$aryValueForBind = $aryDataForQuery[1];

						$str1stQuery  = "SELECT DISTINCT {$strColIdOfKeyOfPrimeMaster} KEY_COLUMN ";
						$str1stQuery .= "FROM {$strPrimeMasterTableBody} ";
						$str1stQuery .= "WHERE ({$strWhereKeyNearMasterBody}) ";
						$str1stQuery .= "AND {$strColIdOfDisuseFlag} IN ('0','1') ";

						$retArray = singleSQLExecuteAgent($str1stQuery, $aryValueForBind, $strFxName);
						if( $retArray[0]===true ){
							$objQuery =& $retArray[1];

							while ( $row = $objQuery->resultFetch() ){
								$arraySelected[] = $row['KEY_COLUMN'];
							}
							unset($objQuery);
						}else{
							$boolExecute = false;
							$intErrorType = 500;
							//ERROR OCCURED ON SELECT WHERE IN MODE FROM PRIME MASTER [｛｝].
							$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20302",array($this->getSelfInfoForLog()));
							web_log($tmpStrForLogOutput);
							dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
						}

					}
					break;
			}
			//1.通常入力分から、最終マスタにある該当キーのパターンを取得----

			//----2.セレクト入力分から、最終マスタにある該当キーを追加
			if( $boolExecute===true ){
				if( $this->getAddSelectTagPrintType()===0 ){
					//----表示列側での仮想マスタテーブルモードの場合
					$arySource = $this->getRichFilterValues();

					if( is_array($arySource)===true && 0 < count($arySource) ){

						$aryDataForQuery = $this->getQueryInPlacesFromSelected($strColIdOfDisplayOfPrimeMaster,$arySource);
						$strWhereKeyNearMasterBody = $aryDataForQuery[0];
						$aryValueForBind = $aryDataForQuery[1];

						$str2ndQuery  = "SELECT DISTINCT {$strColIdOfKeyOfPrimeMaster} KEY_COLUMN ";
						$str2ndQuery .= "FROM {$strPrimeMasterTableBody} ";
						$str2ndQuery .= "WHERE ({$strWhereKeyNearMasterBody}) ";
						$str2ndQuery .= "AND {$strColIdOfDisuseFlag} IN ('0','1') ";

						$retArray = singleSQLExecuteAgent($str2ndQuery, $aryValueForBind, $strFxName);
						if( $retArray[0] === true ){
							$objQuery =& $retArray[1];

							while ( $row = $objQuery->resultFetch() ){
								$arraySelected[] = $row['KEY_COLUMN'];
							}
							unset($objQuery);
						}else{
							$boolExecute = false;
							$intErrorType = 500;
							//ERROR OCCURED ON SELECT WHERE IN MODE FROM VITUAL PRIME MASTER [｛｝].
							$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20303",array($this->getSelfInfoForLog()));
							web_log($tmpStrForLogOutput);
							dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
						}

					}
					//表示列側での仮想マスタテーブルモードの場合----
				}else{
					//----通常テーブル（鍵が一意マスタ）の場合
					$arySource = $this->getRichFilterValues();
					foreach($arySource as $filter){
						$arraySelected[] = $filter;
					}
					//通常テーブル（鍵が一意マスタ）の場合----
				}
			}
			//2.セレクト入力分から、最終マスタにある該当キーを追加----

			if( 0===count($arraySelected) ){
				$boolExecute = false;
			}
		}
		//[1]最終的にプルダウン表示させるのに使うマスタから該当キーを抽出----

		//----[2]配列の個数分(最終の直前から、最初まで)、SQL発行を繰り返す
		if( $boolExecute===true ){
			//----原始マスタから、組み合わせマスタ順の情報を作成する
			$aryFromPrimeToMaster = array_reverse($this->aryRelayInfoFromMasterToPrime);
			//原始マスタから、組み合わせマスタ順の情報を作成する----

			$intCountMaster = count($aryFromPrimeToMaster);
			$intFoucsCount = 0;
			foreach($aryFromPrimeToMaster as $arySingleInfoOfMidTable){
				$intFoucsCount += 1;
				if( $intFoucsCount==1 ){
					continue;
				}
				list($strSchTgtTableBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_TARGET_TABLE"),'');

				list($strColIdOfKeyNearMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_MASTER_COLUMN_ID"),'');
				list($strColTypeOfNearMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_MASTER_COLUMN_TYPE"),0);

				list($strColIdOfKeyNearPrime,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_PRIME_COLUMN_ID"),'');
				list($strColTypeOfNearPrime,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_NEAR_PRIME_COLUMN_TYPE"),0);

				list($strColIdOfDisuseFlag,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_DISUSE_FLAG_COLUMN_ID"),'DISUSE_FLAG');
				list($strWhereAddBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($arySingleInfoOfMidTable,array("MTP_WHERE_ADD"),'');

				if( 0===count($arraySelected) ){
					$boolExecute = false;
					$intErrorType = 500;
					//RERAY DATA BOUND FOR MAINTABLE IS BROKEN ON LOOP([｛｝] OF [｛｝]) [｛｝].
					$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20304",array($intFoucsCount,$intCountMaster,$this->getSelfInfoForLog()));
					web_log($tmpStrForLogOutput);
					dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
					break;
				}

				$aryDataForQuery = $this->getQueryInPlacesFromSelected($strColIdOfKeyNearPrime,$arraySelected);
				$strWhereKeyNearMasterBody = $aryDataForQuery[0];
				$aryValueForBind = $aryDataForQuery[1];

				$strMidQuery  = "SELECT DISTINCT {$strColIdOfKeyNearMaster} KEY_COLUMN ";
				$strMidQuery .= "FROM {$strSchTgtTableBody} ";
				$strMidQuery .= "WHERE {$strWhereKeyNearMasterBody} ";

				$arraySelected = array();
				$retArray = singleSQLExecuteAgent($strMidQuery, $aryValueForBind, $strFxName);
				if( $retArray[0]!== true ){
					$boolExecute = false;
					$intErrorType = 500;
					//RERAY DATA BOUND FOR MAINTABLE SELECT SQL IS ERROR OCCURRED ERROR ON LOOP([｛｝] OF [｛｝]) [｛｝].
					$tmpStrForLogOutput = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20305",array($intFoucsCount,$intCountMaster,$this->getSelfInfoForLog()));
					web_log($tmpStrForLogOutput);
					dev_log($tmpStrForLogOutput,$intControlDebugLevel01);
					break;
				}
				$objQuery =& $retArray[1];

				while ( $row = $objQuery->resultFetch() ){
					$arraySelected[] = $row['KEY_COLUMN'];
				}
				unset($objQuery);
			}
		}
		//[2]配列の個数分(最終の直前から、最初まで)、SQL発行を繰り返す----

		//----[3]（メインテーブルの対象列から、検索するパターンをフィルタへ設定
		if( $intErrorType===null ){
			if( 0===count($arraySelected) ){
				if( 0 < count($this->getFilterValues()) || 0 < count($this->getRichFilterValues()) ){
					//----要素は入力されたが、条件に合致するものなし
					$this->boolNotFoundFlag = true;
					//要素は入力されたが、条件に合致するものなし----
				}
			}else{
				//----通常のフィルタには渡さず、リッチフィルタにのみ要素配置
				foreach($arraySelected as $key=>$value){
					$this->aryRichFilterValueForDTiS[$key] = $value;
					$this->aryRichFilterValueForMatchCheck[$key] = $value;
				}
				//通常のフィルタには渡さず、リッチフィルタにのみ要素配置----

				//----表示のためにIDカラム側へ塗る要素を渡す
				if( 0 < count($this->aryRichFilterValueForMatchCheck) ){

					$objMainColumn->setRichFilterValuesForMatchCheck($this->aryRichFilterValueForMatchCheck);

				}
				//表示のためにIDカラム側へ塗る要素を渡す----
			}
		}
		//[3]（メインテーブルの対象列から、検索するパターンをフィルタへ設定----

		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}
	//DTiS系イベント----
	
	//----IDColumnのマスタから、さらにさかのぼって表示に使うマスタまでの情報を格納/取出する関数
	function setRelayInfoFromMasterToPrime($aryRelayInfoFromMasterToPrime){
		$this->aryRelayInfoFromMasterToPrime = $aryRelayInfoFromMasterToPrime;
	}

	function getRelayInfoFromMasterToPrime(){
		return $this->aryRelayInfoFromMasterToPrime;
	}
	//IDColumnのマスタから、さらにさかのぼって表示に使うマスタまでの情報を格納/取出する関数----

	function getWhereQueryForZeroLengthRecord($intColumnType,$strColumnId){
		//----長さ0の値を排除するためのクエリを作成する
		global $g;
		$retStrNullPartWhereQuery = "";
		if( $g['db_model_ch'] == 0 ){
			//----ORACLE
			$retStrNullPartWhereQuery  = " {$strColumnId} IS NOT NULL ";
			//ORACLE----
		}else if( $g['db_model_ch'] == 1 ){
			//----mySQL/mariaDB
			$retStrNullPartWhereQuery  = " {$strColumnId} IS NOT NULL ";
			if( $intColumnType===1 ){
				//----文字列キー型の場合は、空文字も検出する
				$retStrNullPartWhereQuery  = " ({$strColumnId} IS NOT NULL AND {$strColumnId} <> '') ";
				//文字列キー型の場合は、空文字も検出する----
			}
			//mySQL/mariaDB----
		}
		return $retStrNullPartWhereQuery;
		//長さ0の値を排除するためのクエリを作成する----
	}

	function getQueryInPlacesFromSelected($strColIdOfSeachIn,$arraySelected){
		//----Where句でInで変数を作成したい場合に作成する
		$aryValueForBind = array();
		$aryBindPlace = array();
		$intBindCount = 0;
		foreach($arraySelected as $value){
			$strBindKey = 'BIND__'.$intBindCount;
			$aryBindPlace[] = ":{$strBindKey}";
			$aryValueForBind[$strBindKey] = $value;
			$intBindCount += 1;
		}
		if( 0 < $intBindCount ){
			$strWhereKeyNearMasterBody = "{$strColIdOfSeachIn} IN (".implode(",",$aryBindPlace).")";
		}else{
			$strWhereKeyNearMasterBody = "";
		}
		return array($strWhereKeyNearMasterBody,$aryValueForBind);
		//Where句でInで変数を作成したい場合に作成する----
	}

	function getPrimeMasterTableArray($boolRefreshMode=false){

		$strFxName = __CLASS__."::".__FUNCTION__;

		if( $this->aryPrimeMasterSet===null || $boolRefreshMode===true ){
			$aryPrimeMasterInfo = $this->aryPrimeMasterTableInfo;

			list($strPrimeMasterTableBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_TARGET_TABLE"),'');

			list($strColIdOfKeyOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_ID"),'');
			list($strColTypeOfKeyOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_MASTER_COLUMN_TYPE"),0);

			list($strColIdOfDisplayOfPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_ID"),'');
			list($strColTypeOfDisplayOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_TYPE"),1);

			list($strColIdOfDisuseFlag,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_DISUSE_FLAG_COLUMN_ID"),'DISUSE_FLAG');
			list($strWhereAddBody,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_WHERE_ADD"),'');

			$strSelectQuery = "SELECT {$strColIdOfKeyOfPrimeMaster} C1, {$strColIdOfDisplayOfPrimeMaster} C2 "
			  ."FROM {$strPrimeMasterTableBody} "
			  ."WHERE {$strColIdOfDisuseFlag} IN ('0','1') "
			  ."{$strWhereAddBody}";
			$retArrayForBind = array();

			$data = array();
			$retArray = singleSQLExecuteAgent($strSelectQuery, $retArrayForBind, $strFxName);
			if( $retArray[0]===true ){
				$objQuery =& $retArray[1];

				while ( $row = $objQuery->resultFetch() ){
					$data[$row['C1']] = $row['C2'];
				}
				unset($objQuery);
			}else{
				$data = null;
			}
			$this->aryPrimeMasterSet = $data;
		}

		return $this->aryPrimeMasterSet;
	}

	function getPrimeMasterDisplayColumnType(){
		$aryPrimeMasterInfo = $this->aryPrimeMasterTableInfo;

		list($strColTypeOfDisplayOnPrimeMaster,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryPrimeMasterInfo,array("MTP_NEAR_PRIME_COLUMN_TYPE"),1);
		return $strColTypeOfDisplayOnPrimeMaster;
	}

}

//----ここからColumn直継承クラス（孫継承なし）
class RowEditByFileColumn extends Column{
	protected $strFocusEditType;

	protected $arrayCounter;
	protected $boolRegisterRestrict;
	
	protected $arrayCommandArrayForEdit;
	protected $arrayIgnoreCommandArrayForEdit;

	//----ここから継承メソッドの上書き処理

	function __construct($strColIdText="ROW_EDIT_BY_FILE",$strColExplain=""){
		global $g;
		if( $strColExplain == "" ){
			//$strColExplain="実行処理種別"
			$strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12201");
		}
		parent::__construct($strColIdText, $strColExplain);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("print_table")->setVisible(false);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("print_journal_table")->setVisible(false);

		$this->setCommandArrayForEdit(null);
		$this->setIgnoreCommandArrayForEdit(array(1=>"",2=>"-"));
		
		$this->setResultCount(null);

		$this->setRegisterRestrict(true);
		$this->setFocusEditType("");
	}

	//----FixColumnイベント系
	function afterFixColumn(){
		global $g;    
		//$strResultType01 = "登録";
		//$strResultType02 = "更新";
		//$strResultType03 = "廃止";
		//$strResultType04 = "復活";

		//$strResultType99 ="エラー";

		$strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12202");
		$strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12203");

		$strResultType03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204");
		$strResultType04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12205");

		$strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12206");

		if( $this->getCommandArrayForEdit()===null ){
			$this->setCommandArrayForEdit(array(1=>$strResultType01,2=>$strResultType02,3=>$strResultType03,4=>$strResultType04));
		}

		if( $this->getResultCount()===null ){
			$count = array();
			$count['register']['name'] = $strResultType01;
			$count['register']['ct'] = 0;
			$count['update']['name'] = $strResultType02;
			$count['update']['ct'] = 0;
			$count['delete']['name'] = $strResultType03;
			$count['delete']['ct'] = 0;
			$count['revive']['name'] = $strResultType04;
			$count['revive']['ct'] = 0;
			$count['error']['name'] = $strResultType99;
			$count['error']['ct'] = 0;

			$this->setResultCount($count);    
		}

	}

	//----TableIUDイベント系
	function beforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		if(array_key_exists($this->getID(),$exeQueryData)===true){
			// 実際のDBにないカラムなので、beforeTableIUDAction、で除去すること
			unset($exeQueryData[$this->getID()]);
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setFocusEditType($strEditType){
		$this->strFocusEditType = $strEditType;
	}
	//NEW[2]
	function getFocusEditType(){
		return $this->strFocusEditType;
	}

	//NEW[3]
	function setCommandArrayForEdit($arrayCommandArrayForEdit){
		$this->arrayCommandArrayForEdit = $arrayCommandArrayForEdit;
	}
	//NEW[4]
	function getCommandArrayForEdit(){
		return $this->arrayCommandArrayForEdit;
	}

	//NEW[5]
	function setIgnoreCommandArrayForEdit($arrayIgnoreCommandArrayForEdit){
		$this->arrayIgnoreCommandArrayForEdit = $arrayIgnoreCommandArrayForEdit;
	}
	//NEW[6]
	function getIgnoreCommandArrayForEdit(){
		return $this->arrayIgnoreCommandArrayForEdit;
	}

	//NEW[7]
	function setResultCount($arrayCount){
		$this->arrayCounter = $arrayCount;
	}
	//NEW[8]
	function getResultCount(){
		return $this->arrayCounter;
	}

	//----登録時に、主キー値が指定されていた場合
	//NEW[9]
	function setRegisterRestrict($boolValue){
		$this->boolRegisterRestrict = $boolValue;
	}
	//NEW[10]
	function getRegisterRestrict(){
		return $this->boolRegisterRestrict;
	}
	//登録時に、主キー値が指定されていた場合----

	//NEW[11]
	function editExecute(&$inputArray, $dlcOrderMode, &$aryVariant=array()){
		global $g;
		$arrayRetResult = array();

        $arrayObjColumn = $this->objTable->getColumns();

        $lcRequiredDisuseFlagColumnId = $this->objTable->getRequiredDisuseColumnID();

		$boolValue = false;
		$editType = $inputArray[$this->getID()];

		$retRetMsgBody = "";

		$strNumberForRI = "";

		$arrayTempRet = array();

		$boolExeCountinue = true;

		//----簡易バリデーションチェック
		if( is_array($editType) === true || gettype($editType) == "object" ){
			$intCmdKey = -1;
			$editType = "";
		}else{
			$intCmdKey = array_search($editType,$this->getCommandArrayForEdit());
			if( $intCmdKey === false ){
				$intCmdKey = -1;
				$tmpChkKey = array_search($editType,$this->getIgnoreCommandArrayForEdit());
				if( $tmpChkKey !== false ){
					$intCmdKey = 0;
				}
				$editType = "";
			}
			else{
				$this->setFocusEditType($editType);
			}
		}
		//簡易バリデーションチェック----

		$strActionSubClassName = '';

		//----switch
		switch($intCmdKey){
			case 1 ://case "登録":
				//----登録(新規登録)が入力されている
				$boolUniqueCheckSkip = false; //ユニークチェックをスキップするか？(原則：スキップしない)
				$boolRequiredColumnCheckSkip = false; //必須カラムの送信チェックをスキップするか？(原則：スキップしない)

				$mode = 2;  //実行モード

				if(array_key_exists($this->objTable->getRIColumnID(), $inputArray) && $inputArray[$this->objTable->getRIColumnID()] != ""){
					if($this->getRegisterRestrict()===true){
						$arrayTempRet[0] = "002";
						$arrayTempRet[1] = "000";
						$arrayTempRet[2] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18001",$arrayObjColumn[$this->objTable->getRIColumnID()]->getColLabel());
						$retRetMsgBody = $arrayTempRet[2];
						$boolExeCountinue = false;
						$this->arrayCounter['error']['ct']++;
					}
				}

				if($boolExeCountinue === true){
					//----ここではRIColumnも削除される
					foreach($inputArray as $key2 => $value2){
                        if(!array_key_exists($key2, $arrayObjColumn)){
                            continue;
                        }

						if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
						  ){
							unset($inputArray[$key2]);
						}
					}
					//ここではRIColumnも削除される----

					$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
															,'uniqueCheckSkip'=>$boolUniqueCheckSkip
															,'requiredColumnCheckSkip'=>$boolRequiredColumnCheckSkip
															);
					$arrayTempRet = registerTableMain($mode, $inputArray, null, $dlcOrderMode, $aryVariant);
					$retRetMsgBody = $arrayTempRet[2];

					//----switch
					switch($arrayTempRet[0]){
						case "000":
							switch($arrayTempRet[1]){
								case "201":
									//----登録が成功した
									$this->arrayCounter['register']['ct']++;
									$boolValue = true;
									$retRetMsgBody = "";
									break;
									//登録が成功した----
								default:
									$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18002",$arrayTempRet[1]);
									$this->arrayCounter['error']['ct']++;
									break;
							}
							break;
						case "001"://権限欠如エラー(mode=2の場合除く)
						case "002"://バリデーションエラー
						case "003"://権限欠如エラー
							$this->arrayCounter['error']['ct']++;
							break;
						default:
							$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18003",$retRetMsgBody);
							$this->arrayCounter['error']['ct']++;
							break;
					}
					//switch----
				}

				break;

				//登録(新規登録)が入力されていた場合----
			case 2 ://case "更新":
				//----更新が入力されていた場合
				$boolUniqueCheckSkip = false; //ユニークチェックをスキップするか？(原則：スキップしない)
				$boolRequiredColumnCheckSkip = false; //必須カラムの送信チェックをスキップするか？(原則：スキップしない)

				$strNumberForRI = $inputArray[$this->objTable->getRIColumnID()];
				$mode = 3;  //実行モード

				//----ここではRIColumnも削除される
				foreach($inputArray as $key2 => $value2){
                    if(!array_key_exists($key2, $arrayObjColumn)){
                        continue;
                    }

					if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
					  ){
						unset($inputArray[$key2]);
					}
				}
				//ここではRIColumnも削除される----

				$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
														,'uniqueCheckSkip'=>$boolUniqueCheckSkip
														,'requiredColumnCheckSkip'=>$boolRequiredColumnCheckSkip
														);
				$arrayTempRet = updateTableMain($mode, $strNumberForRI, $inputArray, null, $dlcOrderMode, $aryVariant);
				$retRetMsgBody = $arrayTempRet[2];

				//----switch
				switch($arrayTempRet[0]){
					case "000":
						switch($arrayTempRet[1]){
							case "200":
								//----更新が成功した
								$this->arrayCounter['update']['ct']++;
								$boolValue = true;
								$retRetMsgBody = "";
								break;
								//更新が成功した----
							default:
								$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18004",$arrayTempRet[1]);
								$this->arrayCounter['error']['ct']++;
								break;
						}
						break;
					case "001"://権限欠如エラー(mode=3の場合除く)
					case "002"://バリデーションエラー
					case "003"://権限欠如エラー・追い越し更新・削除済
					case "101"://行特定ミス
					case "201"://追越更新
					case "212"://廃止済レコードへの更新
						$this->arrayCounter['error']['ct']++;
						break;
					default:
						$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18005",$retRetMsgBody);
						$this->arrayCounter['error']['ct']++;
						break;
				}
				//switch----

				break;

				//更新が入力されていた場合----
			case 3 ://case "廃止":
			case 4 ://case "復活":
				//----廃止または復活が入力されていた場合
				$boolUniqueCheckSkip = true; //ユニークチェックをスキップするか？(原則：スキップする）

				$strNumberForRI = $inputArray[$this->objTable->getRIColumnID()];

				//----ここではRIColumnも削除される
				foreach($inputArray as $key2 => $value2){
                    if(!array_key_exists($key2, $arrayObjColumn)){
                        continue;
                    }

					if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
					  ){
						unset($inputArray[$key2]);
					}
				}
				//ここではRIColumnも削除される----

				if( $intCmdKey == 3 ){
					$mode = 3;
				}else{
					$mode = 5;
				}

				$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
								 						,'uniqueCheckSkip'=>$boolUniqueCheckSkip
														);
				$arrayTempRet = deleteTableMain($mode, $strNumberForRI, $inputArray, null, $dlcOrderMode, $aryVariant);
				$retRetMsgBody = $arrayTempRet[2]; 

				//----switch
				switch($arrayTempRet[0]){
					case "000":
						switch($arrayTempRet[1]){
							case "200":
								//----復活が成功した
								$this->arrayCounter['revive']['ct']++;
								$boolValue = true;
								$retRetMsgBody = "";
								break;
								//復活が成功した----
							case "210":
								//----廃止が成功した
								$this->arrayCounter['delete']['ct']++;
								$boolValue = true;
								$retRetMsgBody = "";
								break;
								//廃止が成功した----
							default:
								$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18006",$arrayTempRet[1]);
								$this->arrayCounter['error']['ct']++;
						}
						break;
					case "001"://権限欠如エラー(mode=3,5の場合除く)
					case "002"://バリデーションエラー
					case "003"://権限欠如エラー・追い越し更新・削除済・復活済
					case "101"://行特定ミス
					case "201"://追越更新
					case "211"://廃止済レコードへの廃止
					case "221"://復活済レコードへの復活
						$this->arrayCounter['error']['ct']++;
						break;
					default:
						$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18007",$retRetMsgBody);
						$this->arrayCounter['error']['ct']++;
						break;
				}
				//switch----

				break;

				//廃止または復活が入力されていた場合----
			case  0 :
				//----入力なし
				$arrayTempRet[0] = "000";
				$arrayTempRet[1] = "000";
				break;
			default:
				$arrayTempRet[0] = "002";
				$arrayTempRet[1] = "000";
				$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-18008",$this->getColLabel(true));
				$this->arrayCounter['error']['ct']++;
				break;
		}
		//switch----

		$arrayRetResult[0] = $boolValue;
		$arrayRetResult[1] = $editType;
		$arrayRetResult[2] = $retRetMsgBody;
		$arrayRetResult[3] = $strNumberForRI;
		$arrayRetResult[4] = $arrayTempRet;

		$this->setFocusEditType("");

		return $arrayRetResult;

	}

	//ここまで新規メソッドの定義宣言処理----

}

class RowEditByFileColumnForReview extends RowEditByFileColumn{

	//----ここから継承メソッドの上書き処理

	//----FixColumnイベント系
	function afterFixColumn(){
		global $g;
		$pageType = $this->objTable->getPageType();

		$count = array();

		//$strResultType01 = "廃止";
		//$strResultType02 = "復活";
		//$strResultType99 = "エラー";

		$strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12301");
		$strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12302");
		$strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12303");

		if( $this->getCommandArrayForEdit()===null ){
			if($pageType=="apply"){
				$this->setCommandArrayForEdit(array(
					1=>$this->objTable->getActionNameOfApplyRegistrationForNew(),
					2=>$this->objTable->getActionNameOfApplyUpdate(),
					3=>$this->objTable->getActionNameOfApplyExecute(),
					4=>$this->objTable->getActionNameOfApplyEditRestart(),
					5=>$this->objTable->getActionNameOfApplyWithdrawn(),
					8=>$this->objTable->getActionNameOfLogicDeleteOn(),
					9=>$this->objTable->getActionNameOfLogicDeleteOff()
				));

				$count['register_ForNew']['name'] = $this->objTable->getActionNameOfApplyRegistrationForNew();
				$count['register_ForNew']['ct'] = 0;
				$count['register_ForUpdate']['name'] = $this->objTable->getActionNameOfApplyRegistrationForUpdate();
				$count['register_ForUpdate']['ct'] = 0;

				$count['update_Apply']['name'] = $this->objTable->getActionNameOfApplyUpdate();
				$count['update_Apply']['ct'] = 0;
				$count['update_ApplyExecute']['name'] = $this->objTable->getActionNameOfApplyExecute();
				$count['update_ApplyExecute']['ct'] = 0;
				$count['update_EditRestart']['name'] = $this->objTable->getActionNameOfApplyEditRestart();
				$count['update_EditRestart']['ct'] = 0;
				$count['update_Withdraw']['name'] = $this->objTable->getActionNameOfApplyWithdrawn();
				$count['update_Withdraw']['ct'] = 0;

			}else if($pageType=="confirm"){
				$this->setCommandArrayForEdit(array(
					12=>$this->objTable->getActionNameOfConfirmUpdate(),
					14=>$this->objTable->getActionNameOfConfirmReturn(),
					16=>$this->objTable->getActionNameOfConfirmAccept(),
					17=>$this->objTable->getActionNameOfConfirmNonsuit(),
					18=>$this->objTable->getActionNameOfLogicDeleteOn(),
					19=>$this->objTable->getActionNameOfLogicDeleteOff()
				));
				
				$count['update_Confirm']['name'] = $this->objTable->getActionNameOfConfirmUpdate();
				$count['update_Confirm']['ct'] = 0;
				$count['update_Nonsuit']['name'] = $this->objTable->getActionNameOfConfirmNonsuit();
				$count['update_Nonsuit']['ct'] = 0;
				$count['update_Return']['name'] = $this->objTable->getActionNameOfConfirmReturn();
				$count['update_Return']['ct'] = 0;
				$count['update_Accept']['name'] = $this->objTable->getActionNameOfConfirmAccept();
				$count['update_Accept']['ct'] = 0;
				
			}else{
				$this->setCommandArrayForEdit(array(
					21=>$this->objTable->getActionNameOfApplyRegistrationForUpdate(),
					28=>$this->objTable->getActionNameOfLogicDeleteOn(),
					29=>$this->objTable->getActionNameOfLogicDeleteOff()
				));
			}
		}

		if( $this->getResultCount()===null ){
			$count['delete']['name'] = $strResultType01;
			$count['delete']['ct'] = 0;
			$count['revive']['name'] = $strResultType02;
			$count['revive']['ct'] = 0;
			$count['error']['name'] = $strResultType99;
			$count['error']['ct'] = 0;

			$this->setResultCount($count);
		}
	}
	//FixColumnイベント系----

	function editExecute(&$inputArray, $dlcOrderMode, &$aryVariant=array()){
		global $g;
		$arrayRetResult = array();

		$arrayObjColumn = $this->objTable->getColumns();
		$pageType = $this->objTable->getPageType();

		$lcRequiredDisuseFlagColumnId = $this->objTable->getRequiredDisuseColumnID();

		$boolValue = false;
		$editType = $inputArray[$this->getID()];

		$retRetMsgBody = "";

		$strNumberForRI = "";

		$arrayTempRet = array();

		$boolExeCountinue = true;

		//----簡易バリデーションチェック
		if( is_array($editType) === true || gettype($editType) == "object" ){
			$intCmdKey = -1;
			$editType = "";
		}else{
			$aryCommand = $this->getCommandArrayForEdit();
			if($pageType=="apply"){
				//----修正申請を追加
				$aryCommand[21] = $this->objTable->getActionNameOfApplyRegistrationForUpdate();
				//修正申請を追加----
			}
			
			$intCmdKey = array_search($editType,$aryCommand);
			if( $intCmdKey === false ){
				$intCmdKey = -1;
				$tmpChkKey = array_search($editType,$this->getIgnoreCommandArrayForEdit());
				if( $tmpChkKey !== false ){
					$intCmdKey = 0;
				}
				$editType = "";
			}
			else{
				$this->setFocusEditType($editType);
			}
		}
		//簡易バリデーションチェック----

		$strActionSubClassName = '';

		//----switch
		switch($intCmdKey){
			case  1: //新規申請登録
			case 21: //修正申請登録
				//----登録(新規登録)が入力されている
				$boolUniqueCheckSkip = false; //ユニークチェックをスキップするか？(原則：スキップしない)
				$boolCheckExistLockTargetSkip = false; //ロック対象番号の実在チェックをスキップするか？(原則：スキップしない)

				$mode = 2;  //実行モード

				if($inputArray[$this->objTable->getRIColumnID()] != ""){
					if($this->getRegisterRestrict()===true){
						$arrayTempRet[0] = "002";
						$arrayTempRet[1] = "000";
						$arrayTempRet[2] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19001",$arrayObjColumn[$this->objTable->getRIColumnID()]->getColLabel());
						$retRetMsgBody = $arrayTempRet[2];
						$boolExeCountinue = false;
						$this->arrayCounter['error']['ct']++;
					}
				}

				if($boolExeCountinue === true){
					//----ここではRIColumnも削除される
					foreach($inputArray as $key2 => $value2){
                        if(!array_key_exists($key2, $arrayObjColumn)){
                            continue;
                        }

						if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
						  ){
							unset($inputArray[$key2]);
						}
					}
					//ここではRIColumnも削除される----

					if($editType == $this->objTable->getActionNameOfApplyRegistrationForNew()){
						$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOnEdit();
						$countName = "register_ForNew";
					}else if ($editType == $this->objTable->getActionNameOfApplyRegistrationForUpdate()){
						$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOnEdit();
						$countName = "register_ForUpdate";
					}else{
						$countName = "_dummy";
					}

					if(array_key_exists($countName, $this->arrayCounter)===false){
						$arrayTempRet[0] = "002";
						$arrayTempRet[1] = "000";
						$arrayTempRet[2] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19002",array($this->getColLabel(),$editType));
						$retRetMsgBody = $arrayTempRet[2];
						$boolExeCountinue = false;
						$this->arrayCounter['error']['ct']++;
					}
				}

				if($boolExeCountinue === true){

					$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
															,'uniqueCheckSkip'=>$boolUniqueCheckSkip
															,'checkExistLockTargetSkip'=>$boolCheckExistLockTargetSkip
															,'actionNameOnUI'=>$editType
															);
					$arrayTempRet = registerTableMain($mode, $inputArray, null, $dlcOrderMode, $aryVariant);
					$retRetMsgBody = $arrayTempRet[2];

					//----switch
					switch($arrayTempRet[0]){
						case "000":
							switch($arrayTempRet[1]){
								case "201":
									//----登録が成功した
									$this->arrayCounter[$countName]['ct']++;
									$boolValue = true;
									$retRetMsgBody = "";
									break;
									//登録が成功した----
								default:
									//$retRetMsgBody = "想定外エラーです。:".$arrayTempRet[1];
									$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19003",$arrayTempRet[1]);
									$this->arrayCounter['error']['ct']++;
									break;
							}
							break;
						case "001"://権限欠如エラー(mode=2の場合除く)
						case "002"://バリデーションエラー
						case "003"://権限欠如エラー
							$this->arrayCounter['error']['ct']++;
							break;
						default:
							//$retRetMsgBody = "想定外エラーです。:".$retRetMsgBody;
							$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19004",$retRetMsgBody);
							$this->arrayCounter['error']['ct']++;
							break;
					}
					//switch----
				}

				break;

				//登録(新規登録)が入力されていた場合----
			case  2: //申請内容変更
			case  3: //申請実行
			case  4: //申請編集再開'
			case  5: //申請取下'
			case 12: //申請内容変更
			case 14: //申請差戻
			case 16: //申請承認
			case 17: //申請却下
				//----更新が入力されていた場合
				$boolUniqueCheckSkip = false; //ユニークチェックをスキップするか？(原則：スキップしない)
				$boolCheckExistLockTargetSkip = false; //ロック対象番号の実在チェックをスキップするか？(原則：スキップしない)
				$boolRequiredColumnCheckSkip = true; //必須カラムの送信チェックをスキップするか？(原則：スキップする)

				$mode = 3;  //実行モード

				$strNumberForRI = $inputArray[$this->objTable->getRIColumnID()];

				if($boolExeCountinue === true){
					//----ここではRIColumnも削除される
					foreach($inputArray as $key2 => $value2){
                        if(!array_key_exists($key2, $arrayObjColumn)){
                            continue;
                        }

						if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
						   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
						  ){
							unset($inputArray[$key2]);
						}
					}
					//ここではRIColumnも削除される----

					$countName = "_dummy";

					if($pageType=="apply"){
						if($editType == $this->objTable->getActionNameOfApplyUpdate()){
							// 修正
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOnEdit();
							$countName = "update_Apply";
							$boolRequiredColumnCheckSkip = false; //必須カラムチェックをする
						}else if ($editType == $this->objTable->getActionNameOfApplyExecute()){
							// 申請
							$strActionSubClassName = 'editStatus';
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOfWaitForAccept();
							$countName = "update_ApplyExecute";
						}else if ($editType == $this->objTable->getActionNameOfApplyEditRestart()){
							// 申請取消
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOnEdit();
							$countName = "update_EditRestart";
							$strActionSubClassName = 'editStatus';
							$boolUniqueCheckSkip = true; //ユニークチェックしない
						}else if($editType == $this->objTable->getActionNameOfApplyWithdrawn()){
							// 取下
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOfWithdrawned();
							$countName = "update_Withdraw";
							$strActionSubClassName = 'editStatus';
							$boolUniqueCheckSkip = true; //ユニークチェックしない
							$boolCheckExistLockTargetSkip = true; //ロックNO実在チェックをスキップ
						}
					}else if($pageType=="confirm"){
						if($editType == $this->objTable->getActionNameOfConfirmUpdate()){
							// 修正
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOfWaitForAccept();
							$countName = "update_Confirm";
							$boolRequiredColumnCheckSkip = false; //必須カラムチェックをする
						}else if ($editType == $this->objTable->getActionNameOfConfirmNonsuit()){
							// 却下
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOfNonsuited();
							$countName = "update_Nonsuit";
							$strActionSubClassName = 'editStatus';
							$boolUniqueCheckSkip = true; //ユニークチェックしない
							$boolCheckExistLockTargetSkip = true; //ロックNO実在チェックをスキップ
						}else if ($editType == $this->objTable->getActionNameOfConfirmReturn()){
							// 差戻
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOnEdit();
							$countName = "update_Return";
							$strActionSubClassName = 'editStatus';
							$boolUniqueCheckSkip = true; //ユニークチェックしない
						}else if ($editType == $this->objTable->getActionNameOfConfirmAccept()){
							// 承認
							$inputArray[$this->objTable->getEditStatusColumnID()] = $this->objTable->getStatusNameOfAccepted();
							$countName = "update_Accept";
							$strActionSubClassName = 'editStatus';
						}
					}

					if(array_key_exists($countName, $this->arrayCounter)===false){
						$arrayTempRet[0] = "002";
						$arrayTempRet[1] = "000";
						$arrayTempRet[2] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19005",array($this->getColLabel(),$editType."[".$countName."]".print_r($this->arrayCounter,true)));
						$retRetMsgBody = $arrayTempRet[2];
						$boolExeCountinue = false;
						$this->arrayCounter['error']['ct']++;
					}
				}

				if($boolExeCountinue === true){

					$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
															,'uniqueCheckSkip'=>$boolUniqueCheckSkip
															,'checkExistLockTargetSkip'=>$boolCheckExistLockTargetSkip
															,'requiredColumnCheckSkip'=>$boolRequiredColumnCheckSkip
															,'actionNameOnUI'=>$editType
															);
					$arrayTempRet = updateTableMain($mode, $strNumberForRI, $inputArray, null, $dlcOrderMode, $aryVariant);
					$retRetMsgBody = $arrayTempRet[2];

					//----switch
					switch($arrayTempRet[0]){
						case "000":
							switch($arrayTempRet[1]){
								case "200":
									//----更新が成功した
									$this->arrayCounter[$countName]['ct']++;
									$boolValue = true;
									$retRetMsgBody = "";
									break;
									//更新が成功した----
								default:
									//$retRetMsgBody = "想定外エラーです。:".$arrayTempRet[1];
									$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19006",$arrayTempRet[1]);
									$this->arrayCounter['error']['ct']++;
									break;
							}
							break;
						case "001"://権限欠如エラー(mode=3の場合を除く)
						case "002"://バリデーションエラー
						case "003"://権限欠如エラー・追い越し更新・削除済
						case "101"://行特定ミス
						case "201"://追越更新
						case "212"://廃止済レコードへの更新
							$this->arrayCounter['error']['ct']++;
							break;
						default:
							//$retRetMsgBody = "想定外エラーです。:".$retRetMsgBody;
							$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19007",$retRetMsgBody);
							$this->arrayCounter['error']['ct']++;
							break;
					}
					//switch----
				}

				break;

				//更新が入力されていた場合----
			case  8 : case 18 : case 28 : //'廃止':
			case  9 : case 19 : case 29 : //'復活':
				//----廃止または復活が入力されていた場合
				$boolUniqueCheckSkip = true; //ユニークチェックをスキップするか？(原則：スキップする）
				$boolCheckExistLockTargetSkip = true; //ロック対象番号の実在チェックをスキップするか？(原則：スキップする)
				$strNumberForRI = $inputArray[$this->objTable->getRIColumnID()];

				//----ここではRIColumnも削除される
				foreach($inputArray as $key2 => $value2){
                    if(!array_key_exists($key2, $arrayObjColumn)){
                        continue;
                    }

					if(("FileUploadColumn" !=  get_class($arrayObjColumn[$key2]) && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 != $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowSendFromFile()) ||
					   ("FileUploadColumn" === get_class($arrayObjColumn[$key2]) && 3 == $dlcOrderMode && false === $arrayObjColumn[$key2]->isAllowUploadColmnSendRestApi())
					  ){
						unset($inputArray[$key2]);
					}
				}
				//ここではRIColumnも削除される----

				if( $intCmdKey == 8 || $intCmdKey == 18 || $intCmdKey == 28 ){
					//----廃止の場合
					$mode = 3;
					$editType = $this->objTable->getActionNameOfLogicDeleteOn();
				}else{
					$mode = 5;
					$editType = $this->objTable->getActionNameOfLogicDeleteOff();
				}

				$aryVariant['action_sub_order'] = array('name'=>$strActionSubClassName
														,'uniqueCheckSkip'=>$boolUniqueCheckSkip
														,'checkExistLockTargetSkip'=>$boolCheckExistLockTargetSkip
														,'actionNameOnUI'=>$editType
				);
				$arrayTempRet = deleteTableMain($mode, $strNumberForRI, $inputArray, null, $dlcOrderMode, $aryVariant);

				$retRetMsgBody = $arrayTempRet[2]; 

				//----switch
				switch($arrayTempRet[0]){
					case "000":
						switch($arrayTempRet[1]){
							case "200":
								//----復活が成功した
								$this->arrayCounter['revive']['ct']++;
								$boolValue = true;
								$retRetMsgBody = "";
								break;
								//復活が成功した----
							case "210":
								//----廃止が成功した
								$this->arrayCounter['delete']['ct']++;
								$boolValue = true;
								$retRetMsgBody = "";
								break;
								//廃止が成功した----
							default:
								//$retRetMsgBody = "想定外エラーです。:".$arrayTempRet[1];
								$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19008",$arrayTempRet[1]);
								$this->arrayCounter['error']['ct']++;
						}
						break;
					case "001"://権限欠如エラー(mode=3,5の場合除く)
					case "002"://バリデーションエラー
					case "003"://権限欠如エラー・追い越し更新・削除済・復活済
					case "101"://行特定ミス
					case "201"://追越更新
					case "211"://廃止済レコードへの廃止
					case "221"://復活済レコードへの復活
						$this->arrayCounter['error']['ct']++;
						break;
					default:
						$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19009",$retRetMsgBody);
						$this->arrayCounter['error']['ct']++;
						break;
				}
				//switch----

				break;

				//廃止または復活が入力されていた場合----
			case  0 :
				//----入力なし
				$arrayTempRet[0] = "000";
				$arrayTempRet[1] = "000";
				break;
			default:
				$arrayTempRet[0] = "002";
				$arrayTempRet[1] = "000";
				$retRetMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19010",$this->getColLabel(true));
				$this->arrayCounter['error']['ct']++;
				break;
		}
		//switch----

		$arrayRetResult[0] = $boolValue;
		$arrayRetResult[1] = $editType;
		$arrayRetResult[2] = $retRetMsgBody;
		$arrayRetResult[3] = $strNumberForRI;
		$arrayRetResult[4] = $arrayTempRet;

		$this->setFocusEditType("");

		return $arrayRetResult;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class FileUploadColumn extends Column{
	protected $LAPathToPackageRoot;
	protected $LAPathToPreUploadSave;

	protected $origin;	//----ブラウザで、ファイルへ、アクセスするためのパス（オリジン）
	protected $nrPathAnyToBranchPerFUC;	//----ブラウザで、ファイルへ、アクセスするためのパス（オリジンを除く）

	protected $boolFileHideMode;

	protected $OAPathToUploadScriptFile;

	protected $maxFileSize;
	protected $aryStrRejectType;
	protected $aryStrForbiddenType;

	protected $strWkPkSprintFormat;
	protected $arrayCorrectDirPermsOfBase;  //最終保存先のパーミッション

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $OAPathToUploadScriptFile="", $nrPathAnyToBranchPerFUC="", $maxFileSize=10485760, $sprintFormat="%010d", $arrayCorrectDirPerms=array("0777")){
		global $g;

		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(true);
		$this->setAllowSendFromFile(false); //excelでの更新を禁止
		$this->setOutputType("print_table", new FileLinkOutputType(new TabHFmt(), new LinkTabBFmt()));
		$this->setOutputType("print_journal_table", new FileLinkOutputType(new TabHFmt(), new LinkTabBFmt()));
		$this->setOutputType("delete_table", new FileLinkOutputType(new TabHFmt(), new LinkTabBFmt()));
		$this->setOutputType("filter_table", new FileLinkOutputType(new FilterTabHFmt(), new TextFilterTabBFmt()));
		$this->setOutputType("update_table", new OutputType(new ReqTabHFmt(), new FileUploadTabBFmt()));
		$this->setOutputType("register_table", new OutputType(new ReqTabHFmt(), new FileUploadTabBFmt()));
		$this->setOutputType("csv", new OutputType(new CSVHFmt(), new FileUploadCSVBFmt()));
		$outputType = new OutputType(new JSONHFmt(), new JSONBFmt());
		$outputType->setVisible(true);
		$this->setOutputType("json", $outputType);
		$this->setOAPathToUploadScriptFile($OAPathToUploadScriptFile);

		//----基本的には変更しない
		$this->setLAPathToPackageRoot("{$g['root_dir_path']}");
		$this->setOrigin($g['scheme_n_authority']);

		$this->setLAPathToPreUploadSave("{$g['root_dir_path']}/temp/file_up_column");
		//基本的には変更しない-----

		$this->setNRPathAnyToBranchPerFUC($nrPathAnyToBranchPerFUC);

		$this->setMaxFileSize($maxFileSize);
		$this->aryStrForbiddenType = array();
		$this->aryStrAcceptType = array();
		$this->setEvent("filter_table", "onkeydown", "pre_search_async", array('event.keyCode'));

		$this->setSelectTagCallerShow(true);

		$this->setWkPkSprintFormat($sprintFormat);
		$this->setCorrectDirPermsOfBase($arrayCorrectDirPerms);

		$this->setFileHideMode(true);
		$this->setValidator(new FileNameValidator());
	}

	function setRichFilterValues($value){
		$this->aryRichFilterValueRawBase = $value;
	}

	function addRichFilterValue($value, $index = null){
		//----クラス(Table)のメソッド(addFilter)から呼び出される
		if($index != null){
			$this->aryRichFilterValueRawBase[$index] = $value;
		}else{
			$this->aryRichFilterValueRawBase[] = $value;
		}
	}

	function getRichSearchQuery($boolBinaryDistinctOnDTiS){
		//----WHERE句[2]
		global $g;
		$retStrQuery = "";
		$dbQM=$this->objTable->getDBQuoteMark();

		$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
		$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";

		$tmpArray = array();
		$intFilterCount = 0;

		if($g['db_model_ch'] == 0){
			//----バイナリで精密な一致
			$strWrapHead="NLSSORT(";
			$strWrapTail=",'NLS_SORT=BINARY')";
			//バイナリで精密な一致----
		}else if($g['db_model_ch'] == 1){
			$strWrapHead="";
			$strWrapTail="";
		}

		$arySource = $this->getRichFilterValuesForDTiS(true);
		foreach($arySource as $filter){
			if(0 < strlen($filter)){
				$tmpArray[] = "{$strWrapHead}:{$this->getID()}_RF__{$intFilterCount}{$strWrapTail}";
				$intFilterCount++;
			}
		}
		if(0 < count($tmpArray)){
			//----IN候補型の検出条件クエリを作成
			$retStrQuery .= "{$strWrapHead}{$strWpTblSelfAlias}.{$strWpColId}{$strWrapTail}";
			$retStrQuery .= " IN (".implode(",", $tmpArray) . ")";
			//IN候補型の検出条件クエリを作成----
		}
		return $retStrQuery;
	}

	function afterFixColumn(){
		global $g;
		$objTable = $this->getTable();
		$tmpArray1 = $objTable->getInitInfo();
		$strRelativePath = str_replace("_loadTable.php", "", basename($tmpArray1[0]));

		if( $this->getNRPathAnyToBranchPerFUC() == "" ){
			$this->setNRPathAnyToBranchPerFUC("/uploadfiles/{$strRelativePath}/{$this->getID()}");
		}
		if( $this->getOAPathToUploadScriptFile() == ""){
			$this->setOAPathToUploadScriptFile("{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strRelativePath}");
		}
		return true;
	}

	//----TableIUDイベント系
	public function inTrzBeforeTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;
		$intControlDebugLevel01 = 50;

		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$boolExecute = false;

		$strColId = $this->getID();
		$strColMark = $strColId;

		$properFileName = "";
		$boolActionFlag = false;

		if( $this->getColumnIDHidden() === true ){
			$strColMark = $this->getIDSOP();
		}

		//----ファイル保存処理を実行するかどうかの判定(3.9までの調整用)
		$boolExecute = $this->checkFileSaveProcess($exeQueryData, $reqOrgData, $aryVariant);
		//ファイル保存処理を実行するかどうかの判定(3.9までの調整用)----

		if( $boolExecute === true ){
			$tmpFile = array_key_exists("tmp_file_".$strColMark, $reqOrgData)?$reqOrgData['tmp_file_'.$strColMark]:"";
			$orgFile = "";
			$tempFileOfOrgFileName = $this->getLAPathToPreUploadSave()."/fn_".$tmpFile;
			$this->objTable->readAllFromFileOnce($tempFileOfOrgFileName, $orgFile);

			$editTgtRow = $aryVariant['edit_target_row'];
			$oldFile = array_key_exists($strColId, $editTgtRow)?$editTgtRow[$strColId]:"";

			if( array_key_exists("del_flag_".$strColMark, $reqOrgData) === true && $reqOrgData['del_flag_'.$strColMark] == "on" && $oldFile != "" ){
				//----ファイル削除オーダーがあった場合
				$boolActionFlag = true;
				$properFileName = "";
				//ファイル削除オーダーがあった場合----
			}
			else if($tmpFile != "" && $orgFile != ""){
				//----処理オーダー（事前アップロード）があった場合
				if( $orgFile == $exeQueryData[$strColId] ){
					$boolActionFlag = true;
					$properFileName = $this->makeFileName($orgFile, $exeQueryData, $reqOrgData);
				}
				else{
					$boolRet = false;
					$intErrorType = 2;
					$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20004",$this->getColLabel(true));
				}
				//処理オーダー（事前アップロード）があった場合----
			}
			else{
				if( array_key_exists($strColId, $exeQueryData) === true && array_key_exists($strColId, $editTgtRow) === true )
				{
					if( $exeQueryData[$strColId] == $editTgtRow[$strColId] ){
						//----すでにテーブルにある値と同じ場合
						//すでにテーブルにある値と同じ場合----
					}
					else{
						//----(ファイル事前ULなしで、不正なクエリが送信された場合）
						$boolRet = false;
						$intErrorType = 2;
						$strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20005",$this->getColLabel(true));
						//(ファイル事前ULなしで、不正なクエリが送信された場合）----
					}
				}
				$boolActionFlag = false;
				//処理オーダーがなかった場合----
			}

			if( $boolActionFlag === true ){
				$exeQueryData[$strColId] = $properFileName;
			}
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}

	public function afterTableIUDAction(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		global $g;
		$intControlDebugLevel01 = 50;
		
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$boolExecute = false;

		$strColId = $this->getID();
		$strColMark = $strColId;

		$properFileName = "";
		$ActionFlag = true;

		$aryTempForMsg = array($this->getID());

		if( $this->getColumnIDHidden() === true ){
			$strColMark = $this->getIDSOP();
		}

		try{
			$fileBaseDirPath = $this->getLAPathToPackageRoot();
			$tmpFileDir = $this->getLAPathToPreUploadSave();

			$boolExecute = $this->checkFileSaveProcess($exeQueryData, $reqOrgData, $aryVariant);

			if( $boolExecute === true ){
				$strNumberForRI = $exeQueryData[$this->objTable->getRowIdentifyColumnID()];
				$strDirnameOfNumberForRI = sprintf($this->getWkPkSprintFormat(), $strNumberForRI);

				$aryNumberForJSN = $exeQueryData[$this->getTable()->getRequiredJnlSeqNoColumnID()];
				$strNumberForJSN = $aryNumberForJSN['JNL'];
				$strDirnameOfNumberForJSN = sprintf($this->getWkPkSprintFormat(), $strNumberForJSN);

				$tmpFile = array_key_exists("tmp_file_".$strColMark, $reqOrgData)?$reqOrgData['tmp_file_'.$strColMark]:"";
				$orgFile = "";
				$tempFileOfOrgFileName = $this->getLAPathToPreUploadSave()."/fn_".$tmpFile;
				$this->objTable->readAllFromFileOnce($tempFileOfOrgFileName, $orgFile);

				$editTgtRow = $aryVariant['edit_target_row'];
				$oldFile = array_key_exists($strColId, $editTgtRow)?$editTgtRow[$strColId]:"";

				$utnUpDirPerFUC = "{$this->getLRPathPackageRootToBranchPerFUC()}/{$strDirnameOfNumberForRI}/";

				$lcRequiredUpdateDate4UColumnId = $this->objTable->getRequiredUpdateDate4UColumnID(); //'UPD_UPDATE_TIMESTAMP'

				if(array_key_exists("del_flag_".$strColMark,$reqOrgData) && $reqOrgData['del_flag_'.$strColMark] == "on" && $oldFile != ""){
					//----ファイル削除オーダーがあった場合
					$properFileName = "";
					$LAPathToUtnOldFile = "{$fileBaseDirPath}/{$utnUpDirPerFUC}/{$oldFile}";

					if( file_exists($LAPathToUtnOldFile) === true ){
						$boolUnlink = @unlink($LAPathToUtnOldFile);
						if( $boolUnlink !== true ){
							$intErrorType = 1;
							$aryTempForMsg[] = $LAPathToUtnOldFile;
							throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}
					else{
						$intErrorType = 2;
						$aryTempForMsg[] = $LAPathToUtnOldFile;
						throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
					}
					//ファイル削除オーダーがあった場合----
				}
				else if( $tmpFile != "" && $orgFile != "" ){
					//----事前アップロードが終わっている場合
					$properFileName = $this->makeFileName($orgFile, $exeQueryData, $reqOrgData);

					//ファイルを正当な場所へ移動する処理
					$srcPath	= "{$tmpFileDir}/{$tmpFile}";
					$LAPathToDstUtnFile = "{$fileBaseDirPath}/{$utnUpDirPerFUC}/{$properFileName}";
					$LAPathToDstUtnRi = dirname($LAPathToDstUtnFile);

					$LAPathToDstJnlBase = "{$fileBaseDirPath}/{$utnUpDirPerFUC}/old";
					$LAPathToDstJnlJsn = "{$LAPathToDstJnlBase}/{$strDirnameOfNumberForJSN}";
					$LAPathToDstJnlJsnFile = "{$LAPathToDstJnlJsn}/{$properFileName}";

					if( $oldFile != "" ){
						$LAPathToUtnOldFile = "{$fileBaseDirPath}/{$utnUpDirPerFUC}/{$oldFile}";

						if( file_exists($LAPathToUtnOldFile) === true ){
							$boolUnlink = @unlink($LAPathToUtnOldFile);
							if( $boolUnlink !== true ){
								$intErrorType = 3;
								$aryTempForMsg[] = $LAPathToUtnOldFile;
								throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
						}
						else{
							$intErrorType = 4;
							throw new Exception( '00000400-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}

					if( is_dir($LAPathToDstUtnRi) !== true ){
                        if(!file_exists(dirname($LAPathToDstUtnRi))){
    						$boolMkDir = @mkdir(dirname($LAPathToDstUtnRi), 0777, true);

    						if( $boolMkDir != true ){
    							$intErrorType = 6;
    							$aryTempForMsg[] = $LAPathToDstUtnRi;
    							throw new Exception( '00000600-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                            }

							$boolChmod = @chmod(dirname($LAPathToDstUtnRi),0777);
							if( $boolChmod === false ){
								$intErrorType = 5;
								$aryTempForMsg[] = $LAPathToDstUtnRi;
								throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
                        }

						$boolMkDir = @mkdir($LAPathToDstUtnRi, 0777, true);
						if( $boolMkDir === true ){
							$boolChmod = @chmod($LAPathToDstUtnRi,0777);
							if( $boolChmod === false ){
								$intErrorType = 5;
								$aryTempForMsg[] = $LAPathToDstUtnRi;
								throw new Exception( '00000500-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
						}
						else{
							$intErrorType = 6;
							$aryTempForMsg[] = $LAPathToDstUtnRi;
							throw new Exception( '00000600-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}

					if( is_dir($LAPathToDstJnlBase) !== true ){
						$boolMkDir = @mkdir($LAPathToDstJnlBase);
						if( $boolMkDir === true ){
							$boolChmod = @chmod($LAPathToDstJnlBase,0777);
							if( $boolChmod === false ){
								$intErrorType = 7;
								$aryTempForMsg[] = $LAPathToDstJnlBase;
								throw new Exception( '00000700-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
						}
						else{
							$intErrorType = 8;
							$aryTempForMsg[] = $LAPathToDstJnlBase;
							throw new Exception( '00000800-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}

					$boolMkDir = @mkdir($LAPathToDstJnlJsn);
					if($boolMkDir===true){
						$boolChmod = @chmod($LAPathToDstJnlJsn,0777);
						if( $boolChmod === false ){
							$intErrorType = 9;
							$aryTempForMsg[] = $LAPathToDstJnlJsn;
							throw new Exception( '00000900-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}
					else{
						$intErrorType = 10;
						$aryTempForMsg[] = $LAPathToDstJnlJsn;
						throw new Exception( '00001000-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
					}

					$boolRename = @rename($srcPath, $LAPathToDstJnlJsnFile);
					if( $boolRename === true ){
						$boolCopy = @copy($LAPathToDstJnlJsnFile, $LAPathToDstUtnFile);
						if( $boolCopy === true ){
							$boolUnlink = @unlink($tempFileOfOrgFileName);
							if( $boolUnlink !== true ){
								$intErrorType = 11;
								$aryTempForMsg[] = $tempFileOfOrgFileName;
								throw new Exception( '00001100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
							}
						}
						else{
							$intErrorType = 12;
							$aryTempForMsg[] = $LAPathToDstJnlJsnFile;
							$aryTempForMsg[] = $LAPathToDstUtnFile;
							throw new Exception( '00001200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						}
					}
					else{
						$intErrorType = 13;
						$aryTempForMsg[] = $srcPath;
						$aryTempForMsg[] = $LAPathToDstJnlJsnFile;
						throw new Exception( '00001300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
					}

					//事前アップロードが終わっている場合----
				}
			}
		}
		catch(Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			$strSysErrMsgBody = "";
			$boolRet = false;
			// ----一般訪問ユーザに見せてよいメッセージを作成
			switch($intErrorType){
				default : $strErrMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
			}
			// 一般訪問ユーザに見せてよいメッセージを作成----
			if( 0 < $g['dev_log_developer'] ){
				//----ロードテーブルカスタマイザー向け追加メッセージを作成
				//ロードテーブルカスタマイザー向け追加メッセージを作成----
			}
			//----システムエラー級エラーの場合はWebログにも残す
			switch($intErrorType){
				case  1 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20101",$aryTempForMsg);break;
				case  2 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20102",$aryTempForMsg);break;
				case  3 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20103",$aryTempForMsg);break;
				case  4 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20104",$aryTempForMsg);break;
				case  5 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20105",$aryTempForMsg);break;
				case  6 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20106",$aryTempForMsg);break;
				case  7 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20107",$aryTempForMsg);break;
				case  8 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20108",$aryTempForMsg);break;
				case  9 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20109",$aryTempForMsg);break;
				case 10 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20110",$aryTempForMsg);break;
				case 11 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20111",$aryTempForMsg);break;
				case 12 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20112",$aryTempForMsg);break;
				case 13 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20113",$aryTempForMsg);break;
			}
			if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
			//システムエラー級エラーの場合はWebログにも残す----
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retArray;
	}
	//TableIUDイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	public function addForbiddenFileType($type,$boolFazzy=true){
		// 曖昧モードは、アルファベット部分（ロケールにより変動ある）を小文字へ変換
		$this->aryStrForbiddenType[] = array($type,$boolFazzy);
	}
	//NEW[2]
	public function getForbiddenFileTypes(){
		return $this->aryStrForbiddenType;
	}

	//NEW[3]
	public function addAcceptFileType($type){
		$this->aryStrAcceptType[] = $type;
	}
	//NEW[4]
	public function getAcceptFileTypes(){
		return $this->aryStrAcceptType;
	}

	//NEW[5]
	public function setMaxFileSize($maxFileSize){
		$this->maxFileSize = $maxFileSize;
	}
	//NEW[6]
	public function getMaxFileSize(){
		return $this->maxFileSize;
	}

	// ----中立[neutral]系パス
	//NEW[7]
	public function setNRPathAnyToBranchPerFUC($nrPathAnyToBranchPerFUC){
		//最初にスラッシュがついていることが前提
		
		//最後がスラッシュなら外す
		if( checkRiskOfDirTraversal($nrPathAnyToBranchPerFUC)===false ){
			$strRTrimed = rtrim($nrPathAnyToBranchPerFUC,"/");
			$this->nrPathAnyToBranchPerFUC = $strRTrimed;
		}
	}
	//NEW[8]
	public function getNRPathAnyToBranchPerFUC(){
		return $this->nrPathAnyToBranchPerFUC;
	}

	//NEW[9]
	public function makeFileName($orgFile,&$exeQueryData, &$reqOrgData){
		return $orgFile;
	}
	// 中立系[neutral]パス----

	// ----ローカル系パス
	//NEW[10]
	public function setLAPathToPreUploadSave($LAPathToPreUploadSave){
		//----PHPから、ファイルへ、アクセスするためのパス（絶対パスで）
		if( checkRiskOfDirTraversal($LAPathToPreUploadSave)===false ){
			$strRTrimed = rtrim($LAPathToPreUploadSave,"/");
			if( mb_strpos($strRTrimed, getApplicationRootDirPath(), 0, 'UTF-8')===0 ){
				$this->LAPathToPreUploadSave = $strRTrimed;
			}
		}
	}
	//NEW[11]
	public function getLAPathToPreUploadSave(){
		//----PHPから、ファイルへ、アクセスするためのパス（絶対パスで）
		return $this->LAPathToPreUploadSave;
	}

	//NEW[12]
	public function setLAPathToPackageRoot($LAPathToPackageRoot){
		//----PHPから、ファイルへ、アクセスするためのパス（絶対パスで）
		//$this->LAPathToPackageRoot = rtrim($LAPathToPackageRoot,"/");
		if( checkRiskOfDirTraversal($LAPathToPackageRoot)===false ){
			$strRTrimed = rtrim($LAPathToPackageRoot,"/");
			if( mb_strpos($strRTrimed, getApplicationRootDirPath(), 0, 'UTF-8')===0 ){
				$this->LAPathToPackageRoot = $strRTrimed;
			}
		}
	}
	//NEW[13]
	public function getLAPathToPackageRoot(){
		//----PHPから、ファイルへ、アクセスするためのパス（絶対パスで）
		return $this->LAPathToPackageRoot;
	}
	//NEW[14]
	public function getLRPathPackageRootToBranchPerFUC(){
		//----ファイルアップロードカラムごとのディレクトリ
		$retStr="";
		if( $this->getFileHideMode() === false ){
			//----隠蔽されていない場合
			$retStr = "/webroot".$this->getNRPathAnyToBranchPerFUC();
			//隠蔽されていない場合----
		}else{
			//----ファイルが隠蔽されている場合
			$retStr = $this->getNRPathAnyToBranchPerFUC();
			//ファイルが隠蔽されている場合----
		}
		return $retStr;
	}

	//NEW[15]
	public function getLAPathToFUCItemPerRow($rowData){
		// PHPスクリプトからファイルへのアクセスのためのパス(履歴へはアクセスできない)
		$fileBaseDirPath=$this->getLAPathToPackageRoot();

		$strNumberForRI = $rowData[$this->getTable()->getRIColumnID()];
		$strDirnameOfNumberForRI = sprintf($this->getWkPkSprintFormat(), $strNumberForRI);

		$strJnlSeqNoColId = $this->getTable()->getRequiredJnlSeqNoColumnID();
		if( array_key_exists( $strJnlSeqNoColId ,$rowData) === true ){
			$strNumberForJSN = $rowData[$strJnlSeqNoColId];
			$strDirnameOfNumberForJSN = sprintf($this->getWkPkSprintFormat(), $strNumberForJSN);
			$strMidPath = "{$strDirnameOfNumberForRI}/old/{$strDirnameOfNumberForJSN}";
		}else{
			$strMidPath = "{$strDirnameOfNumberForRI}";
		}

		$filePath = "{$fileBaseDirPath}/{$this->getLRPathPackageRootToBranchPerFUC()}/{$strMidPath}/".$rowData[$this->getID()];
		return $filePath;
	}
	// ローカル系パス----

	//----ここからブラウザによるアクセスのためのメソッド
	//NEW[16]
	public function setOrigin($origin){
		$this->origin = rtrim($origin,"/");
	}
	//NEW[17]
	public function getOrigin(){
		return $this->origin;
	}

	//NEW[18]
	public function setOAPathToUploadScriptFile($OAPathToUploadScriptFile){
		$this->OAPathToUploadScriptFile = $OAPathToUploadScriptFile;
	}
	//NEW[19]
	public function getOAPathToUploadScriptFile(){
		return $this->OAPathToUploadScriptFile;
	}

	//NEW[20]
	public function getOAPathToFUCItemPerRow($rowData){
		// ブラウザからファイルへのアクセスのためのパス(URL)(履歴へはアクセスできない)
		$strFilename=rawurlencode($rowData[$this->getID()]);

		$strNumberForRI = $rowData[$this->getTable()->getRIColumnID()];

		$strJnlSeqNoColId = $this->getTable()->getRequiredJnlSeqNoColumnID();
		if( array_key_exists( $strJnlSeqNoColId ,$rowData) === true ){
			$strNumberForJSN = $rowData[$strJnlSeqNoColId];
		}

		if( $this->getFileHideMode() === false ){
			//----隠蔽されていない場合
			$origin=$this->getOrigin();
			$upDir=$this->getNRPathAnyToBranchPerFUC();

			$strDirnameOfNumberForRI = sprintf($this->getWkPkSprintFormat(), $strNumberForRI);

			if( array_key_exists( $strJnlSeqNoColId ,$rowData) === true ){
				$strDirnameOfNumberForJSN = sprintf($this->getWkPkSprintFormat(), $strNumberForJSN);
				$strMidPath = "{$strDirnameOfNumberForRI}/old/{$strDirnameOfNumberForJSN}";
			}else{
				$strMidPath = "{$strDirnameOfNumberForRI}";
			}
			$url = "{$origin}/{$upDir}/{$strMidPath}/{$strFilename}";
			//隠蔽されていない場合----
		}else{
			//----ファイルが隠蔽されている場合

			if( array_key_exists( $strJnlSeqNoColId ,$rowData) === true ){
				$strMidQuery = "rin={$strNumberForRI}&jsn={$strNumberForJSN}";	
			}else{
				$strMidQuery = "rin={$strNumberForRI}";
			}
            if(false !== strpos($this->getOAPathToUploadScriptFile(), "?")){
    			$url = $this->getOAPathToUploadScriptFile()."&mode=dl&{$strMidQuery}&csn={$this->getColumnSeqNo()}&fn={$strFilename}";
            }
            else{
    			$url = $this->getOAPathToUploadScriptFile()."?mode=dl&{$strMidQuery}&csn={$this->getColumnSeqNo()}&fn={$strFilename}";
            }

			//ファイルが隠蔽されている場合----
		}
		return $url;
	}
	//ここからブラまでによるアクセスのためのメソッド----

	//NEW[21]
	function checkFileSaveProcess(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = false;

		$intControlDebugLevel01=10;

		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecUpdate" ){
			//----アップデートの場合のみ処理を継続する
			//dev_log("更新時のファイル処理を実行を許可します。",$intControlDebugLevel01);

			$boolRet = true;

			//アップデートの場合のみ処理を継続する----
		}else if( $modeValue=="DTUP_singleRecRegister" ){
			$aryObjColumn = $this->objTable->getColumns();
			$objRIColumn = $aryObjColumn[$this->objTable->getRowIdentifyColumnID()];
			if($objRIColumn->getSequenceID()!=""){
				$boolRet = true;

			}else{
			}
		}
		return $boolRet;
	}

	//NEW[22]
	public function getCheckStorageSetting(){
		//----最終格納先ディレクトリのチェック
		global $g;
		$intControlDebugLevel01 = 250;

		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$aryRetValue = array();
		$boolFlagBody = "";
		$intErrorCode = 0;
		$strErrMsgBody = "";
		$strSysErrMsgBody = "";
		$aryTempForMsg = array($this->getID());

		try{
			$strCorrectPermsOfBase = $this->getCorrectDirPermsOfBase();
			$strLocalFileBaseDirPath = $this->getLAPathToPackageRoot().$this->getLRPathPackageRootToBranchPerFUC();

			$intSettingOfBaseDir = $this->checkDirectorySetting($strLocalFileBaseDirPath, $strCorrectPermsOfBase);

			$aryTempForMsg[] = $strLocalFileBaseDirPath;
			$aryTempForMsg[] = implode(",",$strCorrectPermsOfBase);

			if( $intSettingOfBaseDir === 1 ){
				$intErrorCode = 1;
				throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			else if( $intSettingOfBaseDir === 2 ){
				$intErrorCode = 2;
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			else if( $intSettingOfBaseDir === 3 ){
				$intErrorCode = 3;
				throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
			$boolFlagBody = true;
		}
		catch(Exception $e){
			$boolFlagBody = false;
			$tmpErrMsgBody = $e->getMessage();
			// ----一般訪問ユーザに見せてよいメッセージを作成
			switch($intErrorCode){
				default : $strErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
			}
			// 一般訪問ユーザに見せてよいメッセージを作成----
			if( 0 < $g['dev_log_developer'] ){
				//----ロードテーブルカスタマイザー向け追加メッセージを作成
				//ロードテーブルカスタマイザー向け追加メッセージを作成----
			}
			//----システムエラー級エラーの場合はWebログにも残す
			switch($intErrorCode){
				case 1 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20001",$aryTempForMsg[0]);break;
				case 2 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20002",$aryTempForMsg[0]);break;
				case 3 : $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-20003",$aryTempForMsg);break;
			}
			if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
			//システムエラー級エラーの場合はWebログにも残す----
		}

		$aryRetValue[0] = $boolFlagBody;
		$aryRetValue[1] = $intErrorCode;
		$aryRetValue[2] = $strErrMsgBody;

		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $aryRetValue;
	}

	//NEW[23]
	public function checkDirectorySetting($strDirPath, $aryCorrectPerms){
		$retIntValue = 0;
		if( file_exists($strDirPath) === false ){
			// 該当のパスのものがない
			$retIntValue = 1;
			//----再帰的にディレクトリを作成する
			$tmp = $aryCorrectPerms[0];
			$boolMkDir = mkdir($strDirPath, 0777, true);
			if( $boolMkDir === true ){
				//----ディレクトリの作成に成功した
				$boolChgMod = chmod($strDirPath, octdec($tmp));
				if( $boolChgMod == true ){
					//----権限の変更に成功した
					$retIntValue = 0;
				}else{
					//----権限の変更に失敗した
				    $retIntValue = 3;
				}
			}
		}else if( is_dir($strDirPath) === false ){
			// 該当するパスのものがディレクトリではなかった
			$retIntValue = 2;
		}else{
			$varfilePerm = fileperms($strDirPath);
			$strPerm = substr(sprintf("%o", $varfilePerm),-4);
			if( in_array($strPerm, $aryCorrectPerms) === true ){
			}else{
				// パーミッションが規定のものと異なる
				$retIntValue = 3;
			}
		}
		return $retIntValue;
	}

	//NEW[24]
	function setFileHideMode($boolValue){
		$this->boolFileHideMode = $boolValue;
	}
	//NEW[25]
	function getFileHideMode(){
		return $this->boolFileHideMode;
	}
	//NEW[26]
	function setWkPkSprintFormat($strValue){
		$this->strWkPkSprintFormat = $strValue;
	}
	//NEW[27]
	function getWkPkSprintFormat(){
		return $this->strWkPkSprintFormat;
	}
	//NEW[28]
	function setCorrectDirPermsOfBase($arrayValue){
		$this->arrayCorrectDirPermsOfBase = $arrayValue;
	}
	//NEW[29]
	function getCorrectDirPermsOfBase(){
		return $this->arrayCorrectDirPermsOfBase;
	}
	//NEW[30]
	function checkTempFileBeforeMoveOnPreLoad($strTempFileFullname, $strOrgFileName, &$aryVariant=array(), $arySetting=array()){
		//----トランザクション内
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		if( is_null($this->aryFunctionsForEvent)===true ){
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		}else{
			$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
			if( array_key_exists('checkTempFileBeforeMoveOnPreLoad',$this->aryFunctionsForEvent)===true ){
				$objFunction = $this->aryFunctionsForEvent['checkTempFileBeforeMoveOnPreLoad'];
				$retArray = $objFunction($this,'checkTempFileBeforeMoveOnPreLoad',$strTempFileFullname, $strOrgFileName, $aryVariant, $arySetting);
			}
		}
		return $retArray;
		//トランザクション内----
	}
	//ここまで新規メソッドの定義宣言処理----

}

class LinkButtonColumn extends Column { 

	protected $buttonName;
	protected $linkable;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $buttonName, $jsFunction, $args=array(), $linkable=""){
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(false);
		$this->setHeader(false);
		$outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt());
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("print_journal_table", $outputType);

		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("filter_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("register_table", $outputType);
		$outputType = new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("update_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("delete_table", $outputType);
		$outputType = new OutputType(new ExcelHFmt(), new StaticBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("excel", $outputType);
		$outputType = new OutputType(new CSVHFmt(), new StaticCSVBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("csv", $outputType);
		$outputType = new OutputType(new JSONHFmt(), new StaticBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("json", $outputType);
		$this->setButtonFaceValue($buttonName);
		$this->setEvent("print_table", "onClick", $jsFunction, $args);
		$this->setLinkable($linkable);
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setButtonFaceValue($name){
		$this->buttonName = $name;
	}
	//NEW[2]
	function getButtonFaceValue(){
		return $this->buttonName;
	}
	//NEW[3]
	function setLinkable($linkable){
		$this->linkable = $linkable;
	}
	//NEW[4]
	function getLinkable(){
		return $this->linkable;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class UpdBtnColumn extends Column {

	protected $strCheckDisuseColumnId;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $checkDisuseColumnId="DISUSE_FLAG"){
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(false);
		$this->setHeader(true);
		$outputType = new OutputType(new TabHFmt(), new UpdButtonTabBFmt());
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$outputType->setVisible(false);
		$this->setOutputType("print_journal_table", $outputType);
		$this->getOutputType("filter_table")->setVisible(false);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$this->getOutputType("excel")->setVisible(false);
		$this->getOutputType("csv")->setVisible(false);
		$this->getOutputType("json")->setVisible(false);

		$this->setCheckDisuseColumnID($checkDisuseColumnId);
	}

	//----AddColumnイベント系
	function initTable($objTable, $colNo){
		parent::initTable($objTable, $colNo);
		$this->setEvent("print_table", "onclick", "update_async", array(1, ":".$this->objTable->getRIColumnID()));
	}
	//AddColumnイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//NEW[1]
	function setCheckDisuseColumnID($strColname){
		$this->strCheckDisuseColumnId = $strColname;
	}
	//NEW[2]
	function getCheckDisuseColumnID(){
		return $this->strCheckDisuseColumnId;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class EditLockUpdBtnColumn extends UpdBtnColumn {

	protected $strEditUserColumnId;
	protected $strEditStatusColumnId;

	//----ここから継承メソッドの上書き処理

	function __construct($idText, $name, $checkDisuseColumnId='DISUSE_FLAG', $strEditStatusColumnId="", $strEditUserColumnId=""){
		parent::__construct($idText,$name, $checkDisuseColumnId);
		$this->setOutPutType('print_table', new OutputType(new TabHFmt(), new EditLockUpdButtonTabBFmt()));
		$this->setEditStatusColumnID($strEditStatusColumnId);
		$this->setEditUserColumnID($strEditUserColumnId);
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setEditStatusColumnID($strColname){
		$this->strEditStatusColumnId = $strColname;
	}
	//NEW[2]
	function getEditStatusColumnID(){
		return $this->strEditStatusColumnId;
	}

	//NEW[3]
	function setEditUserColumnID($strColname){
		$this->strEditUserColumnId = $strColname;
	}
	//NEW[4]
	function getEditUserColumnID(){
		return $this->strEditUserColumnId;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class DelBtnColumn extends Column {

	protected $strCheckDisuseColumnId;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId="DISUSE_FLAG", $strColLabel=""){
		global $g;
		if( $strColLabel == "" ){
			//$strColLabel="廃止"
			$strColLabel = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12401");
		}
		parent::__construct($strColId, $strColLabel);
		$this->setHiddenMainTableColumn(true);
		$this->setDBColumn(true);
		$this->setHeader(true);
		$this->setAllowSendFromFile(false);
		$this->setSearchType("in");
		$outputType = new OutputType(new TabHFmt(), new DelButtonTabBFmt());
		$this->setOutputType("print_table", $outputType);
		$outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
		$this->setOutputType("print_journal_table", $outputType);
		$this->getOutputType("update_table")->setVisible(false);
		$this->getOutputType("register_table")->setVisible(false);
		$this->getOutputType("delete_table")->setVisible(false);
		$outputType = new OutputType(new TabHFmt(), new DisuseSelectFilterTabBFmt());
		$this->setOutputType("filter_table", $outputType);
		$outputType = new OutputType(new ExcelHFmt(), new DelBFmt());
		$this->setOutputType("excel", $outputType);
		$outputType = new OutputType(new CSVHFmt(), new DelCSVBFmt());
		$this->setOutputType("csv", $outputType);
		$outputType = new OutputType(new JSONHFmt(), new DelBFmt());
		$this->setOutputType("json", $outputType);
		$this->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-STD-12402"));
		$this->setEvent("filter_table", "onchange", "search_async", array("'idcolumn_filter_default'"));

		$this->setCheckDisuseColumnID($strColId);	//"DISUSE_FLAG"

		$this->setValidator(new DelBtnValidator());
	}

	function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		//----WHERE句[0]
		//----クラス(Table)のメソッド(getFilterQuery)から呼び出される
		global $g;
		$retStrQuery = parent::getFilterQuery($boolBinaryDistinctOnDTiS);
		if( $retStrQuery == "" ){
			//----隠しフラグがある場合のために特別扱い
			$dbQM = $this->objTable->getDBQuoteMark();
			$strWpColId = "{$dbQM}{$this->getID()}{$dbQM}";
			$strWpTblSelfAlias = "{$dbQM}{$this->objTable->getShareTableAlias()}{$dbQM}";
			$retStrQuery .= "{$strWpTblSelfAlias}.{$strWpColId}";
			$retStrQuery .= " IN ('0','1')";
			//隠しフラグがある場合のために特別扱い----
		}
		return $retStrQuery;
	}

	//----TableIUDイベント系
	function beforeIUDValidateCheck(&$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		// JOURNAL専用のカラムではないので、beforeIUDValidateCheckのタイミングでは、$exeQueryData、に直接代入してはならない
		$reqOrgData[$this->getID()] = $this->getAutoSetValue($aryVariant);
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function getAutoSetValue(&$aryVariant=array()){
		$retStrVal = "";
		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" ){
			//----行の、登録時に、規定値"0"を追加
			$retStrVal = "0";
			//----行の、登録時に、規定値"0"を追加
		}else if( $modeValue=="DTUP_singleRecUpdate" ){
			//----行の、更新時に、規定値"0"を追加
			$retStrVal = "0";
			//行の、更新時に、規定値"0"を追加----
		}else if( $modeValue=="DTUP_singleRecDelete" ){
			$modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//$aryVariant['mode_sub'];
			if($modeValue_sub == "on"){
				$retStrVal = "1";
			}else if($modeValue_sub == "off"){
				$retStrVal = "0";
			}
		}
		return $retStrVal;
	}
	//TableIUDイベント系----

	//NEW[2]
	function setCheckDisuseColumnID($strDisuseColId){
		$this->strCheckDisuseColumnId = $strDisuseColId;
	}
	//NEW[3]
	function getCheckDisuseColumnID(){
		return $this->strCheckDisuseColumnId;
	}

	//ここまで新規メソッドの定義宣言処理----

}

class EditStatusControlBtnColumn extends Column {

	protected $strCheckDisuseColumnId;
	protected $strEditUserColumnId;
	protected $strEditStatusColumnId;

	protected $pageMode;

	//----ここから継承メソッドの上書き処理

	function __construct($strColId, $strColLabel, $strCheckDisuseColumnId, $strEditStatusColumnId, $strEditUserColumnId, $pageMode="apply", $aryEtcetera=array()){
		parent::__construct($strColId, $strColLabel);
		$this->setDBColumn(false);
		$this->setHeader(true);
		$outputType = new OutputType(new TabHFmt(), new EditStatusControlBtnTabBFmt());
		$this->setOutputType('print_table', $outputType);
		$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
		$this->setOutputType('print_journal_table', $outputType);
		$this->getOutputType('filter_table')->setVisible(false);
		$this->getOutputType('update_table')->setVisible(false);
		$this->getOutputType('register_table')->setVisible(false);
		$this->getOutputType('delete_table')->setVisible(false);
		$this->getOutputType('print_journal_table')->setVisible(false);
		$this->getOutputType('excel')->setVisible(false);
		$this->getOutputType('csv')->setVisible(false);
		$this->getOutputType("json")->setVisible(false);

		$this->setPageMode($pageMode);
		$this->setCheckDisuseColumnID($strCheckDisuseColumnId);
		$this->setEditStatusColumnID($strEditStatusColumnId);
		$this->setEditUserColumnID($strEditUserColumnId);
	}

	//----AddColumnイベント系
	function initTable($table, $colNo){
		parent::initTable($table, $colNo);
	}
	//AddColumnイベント系----

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setPageMode($intValue){
		$this->pageMode = $intValue;
	}
	//NEW[2]
	function getPageMode(){
		return $this->pageMode;
	}

	//NEW[3]
	function setEditStatusColumnID($strColname){
		$this->strEditStatusColumnId = $strColname;
	}
	//NEW[4]
	function getEditStatusColumnID(){
		return $this->strEditStatusColumnId;
	}
	//NEW[5]
	function setEditUserColumnID($strColname){
		$this->strEditUserColumnId = $strColname;
	}
	//NEW[6]
	function getEditUserColumnID(){
		return $this->strEditUserColumnId;
	}
	//NEW[7]
	function setCheckDisuseColumnID($strColname){
		$this->strCheckDisuseColumnId = $strColname;
	}
	//NEW[8]
	function getCheckDisuseColumnID(){
		return $this->strCheckDisuseColumnId;
	}

	//ここまで新規メソッドの定義宣言処理----

}
//ここまでColumn直継承クラス（孫継承なし）----

?>
