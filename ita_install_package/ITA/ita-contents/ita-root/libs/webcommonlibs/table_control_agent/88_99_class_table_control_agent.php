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
//    ・ModuleDistictCode(904)
//
//  【処理概要】
//    ・登録/更新のテーブル領域に、プルダウンリストHtmlタグを、事後的に作成する
//
//////////////////////////////////////////////////////////////////////

class TableControlAgent {
	/* Table : DBと１対１になるデータの集合。
		DBの情報と、DBから取得した値を保持
		データの出力はFormatterに委譲
	*/
	protected $arrayInfoOfObjInit;

	protected $shareTableAlias;

	//----mainTable(UTN)
	protected $strDBMainTableId;
	protected $strDBMainTableAgentQuery;	//エイリアスを付けないこと
	protected $strDBMainTableHiddenId;
	protected $strDBMainTableLabel;
	//mainTable(UTN)----

	//----journalTable
	protected $strDBJournalTableId;
	protected $strDBJournalTableAgentQuery;	//エイリアスを付けないこと
	protected $strDBJournalTableHiddenId;
	protected $strDBJournalTableLabel;
	//journalTable----

	protected $strRowIdentifyColumnId;	// as string プライマリキー
	protected $strNDBRowEditByFileColId;	//"ROW_EDIT_BY_FILE" "EXEC_TYPE"

	protected $strRequiredJnlSeqColId;	//"JOURNAL_SEQ_NO"
	protected $strRequiredJnlRegTimeColId;	//"JOURNAL_REG_DATETIME"
	protected $strRequiredJnlRegClassColId;	//"JOURNAL_ACTION_CLASS"

	protected $strRequiredDisuseColId;	//"DISUSE_FLAG"
	protected $strNDBUpdateBtnColId;	//"UPDATE"

	protected $strRequiredNoteColId;	//"NOTE"

	protected $strRequiredLUDateId;	//"LAST_UPDATE_TIMESTAMP"
	protected $strRequiredLUUserColId;	//"LAST_UPDATE_USER"

	protected $strNDBLUDate4UColId;	//"UPD_UPDATE_TIMESTAMP"

	//----参照
	protected $objColGroup;  // as ColGroup ヘッダの表示構成をツリー形式で保持
	protected $aryObjFormatter;  // as array of Formatter

	protected $aryObjColumn;  // as array of Column 
	protected $aryObjRow;  // as array of RowData

	protected $objCommonWrapEvent;
	protected $arrayObjGene; //as array
	//参照----

	protected $boolAddColumnContinue;
	protected $boolJsEventNamePrefix;

	protected $intMinorVersion;
	protected $intColNoSeq; // as integer

	protected $strLeftJoinTableQuery;
	protected $strPrintingTableId; // as getPrintFormatが呼び出された間のみ保持（外部からの読取専用）

	protected $aryUniqueColumnSet;
	protected $aryUniqueCheckRangeInReqDUColumn;
	protected $arySortKeyTypeSet; // as array (FieldNx=>ASC||DESC)

	protected $varCommitSpanOnTableIUDByFile;

	public function __construct($strDBMainTableId, $strRIColumnId, $strRIColumnLabel="", $strDBJournalTableId=null, $arrayVariant=array()){
		global $g;
		if( $strRIColumnLabel == "" ){
			$strRIColumnLabel = $g['objMTS']->getSomeMessage("ITAWDCH-STD-18001");
		}
		$this->boolAddColumnContinue = true;
		$this->setInitInfo(debug_backtrace($limit=1));

		$this->setShareTableAlias("T1");

		$this->strDBMainTableId = $strDBMainTableId;
		$this->strDBMainTableAgentQuery = null;
		$this->strDBMainTableHiddenId = "";

		$this->strDBMainTableLabel = $strDBMainTableId;
		if( $strDBJournalTableId===null ){
			$this->strDBJournalTableId = $strDBMainTableId."_JNL";
		}else{
			$this->strDBJournalTableId = $strDBJournalTableId;
		}
		$this->strDBJournalTableAgentQuery = null;
		$this->strDBJournalTableHiddenId = "";

		$arrayVariant['TT_SYS_00_ROW_IDENTIFY_LABEL'] = $strRIColumnLabel;

		$strJnlSeqNoColId = isset($arrayVariant['TT_SYS_01_JNL_SEQ_ID'])?$arrayVariant['TT_SYS_01_JNL_SEQ_ID']:"JOURNAL_SEQ_NO";
		$strJnlRegTimeColId = isset($arrayVariant['TT_SYS_02_JNL_TIME_ID'])?$arrayVariant['TT_SYS_02_JNL_TIME_ID']:"JOURNAL_REG_DATETIME";
		$strJnlRegClassColId = isset($arrayVariant['TT_SYS_03_JNL_CLASS_ID'])?$arrayVariant['TT_SYS_03_JNL_CLASS_ID']:"JOURNAL_ACTION_CLASS";
		$strDisuseFlagColId = isset($arrayVariant['TT_SYS_04_DISUSE_FLAG_ID'])?$arrayVariant['TT_SYS_04_DISUSE_FLAG_ID']:"DISUSE_FLAG";

		$strRowEditByFileColId = isset($arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'])?$arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_ID']:"ROW_EDIT_BY_FILE";
		$strUpdateColId = isset($arrayVariant['TT_SYS_NDB_UPDATE_ID'])?$arrayVariant['TT_SYS_NDB_UPDATE_ID']:"UPDATE";

		//----Prepare系・必須カラム名の設定
		$this->setRowIdentifyColumnID($strRIColumnId);
		$this->setRequiredJnlSeqNoColumnID($strJnlSeqNoColId);
		$this->setRequiredJnlRegTimeColumnID($strJnlRegTimeColId);
		$this->setRequiredJnlRegClassColumnID($strJnlRegClassColId);
		$this->setRequiredDisuseColumnID($strDisuseFlagColId);

		$this->setRequiredRowEditByFileColumnID($strRowEditByFileColId);
		$this->setRequiredUpdateButtonColumnID($strUpdateColId);
		//Prepare系・必須カラム名の設定----

		$strNoteColId = isset($arrayVariant['TT_SYS_04_NOTE_ID'])?$arrayVariant['TT_SYS_04_NOTE_ID']:"NOTE";
		$strLastUpdateTimeColId = isset($arrayVariant['TT_SYS_05_LUP_TIME_ID'])?$arrayVariant['TT_SYS_05_LUP_TIME_ID']:"LAST_UPDATE_TIMESTAMP";
		$strLastUpdateUserColId = isset($arrayVariant['TT_SYS_06_LUP_USER_ID'])?$arrayVariant['TT_SYS_06_LUP_USER_ID']:"LAST_UPDATE_USER";
		$strLastUpdateDate4UColId = isset($arrayVariant['TT_SYS_NDB_LUP_TIME_ID'])?$arrayVariant['TT_SYS_NDB_LUP_TIME_ID']:"UPD_UPDATE_TIMESTAMP";

		//----Fix系・必須カラム名の設定
		$this->setRequiredNoteColumnID($strNoteColId);

		$this->setRequiredLastUpdateDateColumnID($strLastUpdateTimeColId);
		$this->setRequiredLastUpdateUserColumnID($strLastUpdateUserColId);

		$this->setRequiredUpdateDate4UColumnID($strLastUpdateDate4UColId);
		//Fix系・必須カラム名の設定----

		$this->aryObjColumn = array();
		$this->aryObjRow = array();
		$this->objColGroup = new ColumnGroup("root", true);
		$this->aryObjFormatter = array();
		
		$this->setFormatter(new FilterConditionTableFormatter("filter_table", $this, "FilterTable"));
		
		$this->setFormatter(new CurrentTableFormatter("print_table", $this, "DbTable"));
		$this->setFormatter(new SubtotalTableFormatter("print_subtotal_table", $this, "DbTable"));
		
		$this->setFormatter(new UpdateTableFormatter("update_table", $this, "DbTable"));
		$this->setFormatter(new DeleteTableFormatter("delete_table", $this, "DbTable"));
		
		$this->setFormatter(new RegisterTableFormatter("register_table", $this, "InsertFormTable"));
		
		$this->setFormatter(new CSVFormatter("csv", $this, "")); //csv(_default) (input/output共通としてdefault)
		$this->setFormatter(new ExcelFormatter("excel", $this, "")); //excel(_default) (input/output共通としてdefault)
		$this->setFormatter(new JSONFormatter("json", $this, "")); //json(_default) (input/output共通としてdefault)
		
		$this->setFormatter(new QMFileSendAreaFormatter("all_dump_table", $this, "allDumpTable"));
		
		$this->setFormatter(new JournalTableFormatter("print_journal_table", $this, "DbJournalTable"));
		
		$this->objCommonWrapEvent = new commonWrapEvent($this);
		$this->arrayObjGene = array();
		
		//----ユニーク系
		$this->aryUniqueColumnSet = array();
		$this->setUniqueCheckRangeInRequiredDisuseColumn(array('0')); //'ユニーク判定は活性レコードのみ'
		//ユニーク系----
		
		$this->intCgNoSeq = 0;
		$this->intColNoSeq = 0;
		
		$this->arySortKeyTypeSet = array($strRIColumnId=>"ASC");
		
		$this->strLeftJoinTableQuery = "";
		
		//----標準系カラムの用意
		$this->prepareColumn($arrayVariant);
		//標準系カラムの用意----
		
		$this->setMinorVersion(2);
		$this->setJsEventNamePrefix(false);
		
		$this->setCommitSpanOnTableIUDByFile(0); //ファイルアップロードによる更新の場合のcommitタイミング(0は、ALLEnd時/1は行ごと)
	}

	public function setInitInfo($array){
		$this->arrayInfoOfObjInit = array($array[0]['file'],$array[0]['line']);
	}

	public function getInitInfo(){
		return $this->arrayInfoOfObjInit;
	}

	public function getSelfInfoForLog(){
		$temoAryBody = $this->getInitInfo();
		$retStrBody='[TABLE]([FILE]'.$temoAryBody[0].',[LINE]'.$temoAryBody[1].')';
		return $retStrBody;
	}

	public function getMinorVersion(){
		return $this->intMinorVersion;
	}
	public function setMinorVersion($intMinorVersion){
		$this->intMinorVersion = $intMinorVersion;
	}

	public function getJsEventNamePrefix(){
		return $this->boolJsEventNamePrefix;
	}
	public function setJsEventNamePrefix($boolValue){
		$this->boolJsEventNamePrefix = $boolValue;
	}

	//----操作対象テーブル設定関係

	public function getShareTableAlias(){
		return $this->shareTableAlias;
	}
	public function setShareTableAlias($strAlias){
		$this->shareTableAlias = $strAlias;
	}

	//----main(UTN)テーブル関係
	public function getDBMainTableID(){
		return $this->strDBMainTableId;
	}
	public function getDBMainTableAgentQuery(){
		return $this->strDBMainTableAgentQuery;
	}
	public function getDBMainTableBody(){
		//----結合クエリまたはDBテーブル名を返す
		$retStrVal=$this->getDBMainTableID();
		if( $this->strDBMainTableAgentQuery !== null ){
			$retStrVal=$this->strDBMainTableAgentQuery;
		}
		return $retStrVal;
	}
	public function getDBMainTableHiddenID(){
		return $this->strDBMainTableHiddenId;
	}
	public function getDBMainTableLabel(){
		//----編集用ファイルのプレフィックス及びエクセルのシート名(デフォルト)に使われるべき値
		return $this->strDBMainTableLabel;
	}
	public function setDBMainTableID($strValue){
		$this->strDBMainTableId = $strValue;
	}
	public function setDBMainTableAgentQuery($strQuery){
		$this->strDBMainTableAgentQuery = $strQuery;
	}
	public function setDBMainTableHiddenID($strValue){
		$this->strDBMainTableHiddenId = $strValue;
	}
	public function setDBMainTableLabel($strValue){
		$this->strDBMainTableLabel = $strValue;
	}
	//main(UTN)テーブル関係----

	//----Journalテーブル関係
	public function getDBJournalTableID(){
		return $this->strDBJournalTableId;
	}
	public function getDBJournalTableAgentQuery(){
		return $this->strDBJournalTableAgentQuery;
	}
	public function getDBJournalTableBody(){
		//----結合クエリまたはDBテーブル名を返す
		$retStrVal=$this->getDBJournalTableID();
		if( $this->strDBJournalTableAgentQuery !== null ){
			$retStrVal=$this->strDBJournalTableAgentQuery;
		}
		return $retStrVal;
	}
	public function getDBJournalTableHiddenID(){
		return $this->strDBJournalTableHiddenId;
	}
	public function setDBJournalTableID($strValue){
		$this->strDBJournalTableId = $strValue;
	}
	public function setDBJournalTableAgentQuery($strQuery){
		$this->strDBJournalTableAgentQuery = $strQuery;
	}
	public function setDBJournalTableHiddenID($strValue){
		$this->strDBJournalTableHiddenId = $strValue;
	}
	//Journalテーブル関係----

	//操作対象テーブル設定関係----

	//----行データ操作関係

	public function setData($aryKey1varAnyVal1aryKey2strVal2str){
		$this->aryObjRow = array();
		foreach($aryKey1varAnyVal1aryKey2strVal2str as $aryKey2strVal2str){
			$this->addData($aryKey2strVal2str);
		}
	}

	public function addData($aryKeystrValstr, $addSubTotal=true, &$refArrayVariant=array()){
		$this->aryObjRow[] = new RowData($aryKeystrValstr, $this->getRequiredDisuseColumnID());
		foreach($this->getColumns() as $column){
			if( $addSubTotal === true && $column->getSubtotalFlag() === true ){
				$v = $aryKeystrValstr[$column->getID()];
				if( is_numeric($v) === true ){
					if( $column->subTotalAddBeforeCheck($aryKeystrValstr, $refArrayVariant) === true ){
						$column->addSubtotalValue($v);
					}
				}
			}
		}
	}

	public function getRows(){
		return $this->aryObjRow;
	}

	//行データ操作関係----

	public function commonEventHandlerExecute(&$refArrayVariant=array()){
		$this->objCommonWrapEvent->eventExecute($refArrayVariant);
	}

	public function setUniqueColumnSet($ary){
		$this->aryUniqueColumnSet = $ary;
	}
	public function addUniqueColumnSet($uniqueColumnSet){
		$this->aryUniqueColumnSet[] = $uniqueColumnSet;
	}
	public function getUniqueColumnSets(){
		return $this->aryUniqueColumnSet;
	}

	public function setUniqueCheckRangeInRequiredDisuseColumn($ary){
		$this->aryUniqueCheckRangeInReqDUColumn = $ary;
	}
	public function addUniqueCheckRangeInRequiredDisuseColumn($strValue){
		$this->aryUniqueCheckRangeInReqDUColumn[] = $strValue;
	}
	public function getUniqueCheckRangeInRequiredDisuseColumn(){
		return $this->aryUniqueCheckRangeInReqDUColumn;
	}

	//----列情報操作系
	public function addColumn(ColumnGroup $objColumn, $uniqueValidatorAdd=false){
		global $g;
		try{
			if( $this->boolAddColumnContinue === true ){
				if( is_a($objColumn, "Column") === true ){
					if(array_key_exists($objColumn->getID(),$this->aryObjColumn)===true){
						//----カラムIDが重複した場合
						throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
						//カラムIDが重複した場合----
					}else{
						$this->objColGroup->addColumn($objColumn);
						$this->aryObjColumn[$objColumn->getID()] = $objColumn;
						if( $objColumn->isUnique() === true || $uniqueValidatorAdd === true ){
							//----ユニークバリデーターの付加
							$this->addUniqueColumnSet(array($objColumn->getID()));
							//ユニークバリデーターの付加----
						}
						$objColumn->initTable($this, $this->getColNo());
					}
				}else{
					//----カラム系クラスではない場合
					$objColumn->initTable($this, null);
					$refObjArrayColumn = array();
					$objColumn->getEdgeColumn($refObjArrayColumn);
					if( $refObjArrayColumn === null || count($refObjArrayColumn) === 0){
						
						throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
					}else{
						$this->objColGroup->addColumn($objColumn);
						foreach($refObjArrayColumn as $objForcusColumn){
							$this->aryObjColumn[$objForcusColumn->getID()] = $objForcusColumn;
							if( is_a($objForcusColumn, "Column") === true ){
								if( $objForcusColumn->isUnique() === true ){
									//----ユニークバリデーターの付加
									$this->addUniqueColumnSet(array($objForcusColumn->getID()));
									//ユニークバリデーターの付加----
								}
							}
							$objForcusColumn->initTable($this, $this->getColNo());
						}
					}
					//カラム系クラスではない場合----
				}
			}
			else{
				//----fix後にaddした場合
				throw new Exception( '00000300-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				//fix後にaddした場合----
			}
		}
		catch (Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90410101);
			exit();
		}
		return true;
	}

	public function getColGroup(){
		return $this->objColGroup;
	}

	public function getColumns(){
		return $this->aryObjColumn;
	}

	public function getColumnIDSOPs(){
		$aryRetValue = array();
		foreach($this->aryObjColumn as $strColId=>$objColumn){
			$aryRetValue[$objColumn->getIDSOP()] = $strColId;
		}
		return $aryRetValue;
	}

	public function getColNo(){
		//----返値の最少値は「0」。
		$retIntVal = null;
		if( $this->boolAddColumnContinue === true ){
			$retIntVal = $this->intColNoSeq;
			$this->intColNoSeq += 1;
		}
		return $retIntVal;
	}

	public function getCgNo(){
		//----返値の最少値は「0」。
		$retIntVal = null;
		if( $this->boolAddColumnContinue === true ){
			$retIntVal = $this->intCgNoSeq;
			$this->intCgNoSeq += 1;
		}
		return $retIntVal;
	}

	//列情報操作系----

	//----where句を生成

	function getDBQuoteMark(){
		global $g;
		$retStrDbQM = "";
		if( $g['db_model_ch'] == 0 ){
			$retStrDbQM = "\"";
		}else if( $g['db_model_ch'] == 1 ){
			$retStrDbQM = "`";
		}
		return $retStrDbQM;
	}

	public function getFilterQuery($boolBinaryDistinctOnDTiS=true){
		$retStrQuery = "";
		$aryStrQueryPart = array();
		$strQueryPart = "";
		$strQueryAddNull = "";
		$strTempStr = "";
		foreach($this->aryObjColumn as $objColumn){
			$strQueryPart = "";
			$strQueryAddNull = "";
			if( $objColumn->isDBColumn()===true || is_a($objColumn, "WhereQueryColumn")===true ){
				$intPart = 0;
				//
				//----通常のフィルタ要素
				$arySourceForDTiS = $objColumn->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
				if( is_a($objColumn, "DelBtnColumn") === true ){
					$intFilterSet = 1;
				}else{
					$intFilterSet = count($arySourceForDTiS);
				}
				//
				if( is_array($arySourceForDTiS)===true && 1 <= $intFilterSet ){
					if( 1 <= $intPart ){
						$strQueryPart .= " OR ";
					}else{
						$strQueryPart .= " (";
					}
					$strQueryPart .= " ({$objColumn->getFilterQuery($boolBinaryDistinctOnDTiS)}) ";
					$intPart += 1;
				}
				//通常のフィルタ要素-----
				
				if( $objColumn->getNullSearchExecute() === true ){
					//----ヌルサーチをするカラム
					$strQueryAddNull = $objColumn->getNullSearchQuery();
					if( 1 <= $intPart ){
						$strQueryPart .= " OR ";
					}else{
						$strQueryPart .= " (";
					}
					$strQueryPart .= "{$strQueryAddNull}";
					$intPart += 1;
					//ヌルサーチをするカラム----
				}
				if( $objColumn->getRichSearchQuery($boolBinaryDistinctOnDTiS) != "" ){
					if( 1 <= $intPart ){
						$strQueryPart .= " OR ";
					}else{
						$strQueryPart .= " (";
					}
					$strQueryPart .= " {$objColumn->getRichSearchQuery($boolBinaryDistinctOnDTiS)}";
					$intPart += 1;
				}
				if( 1 <= $intPart ){
					$strQueryPart .= ")";
					$aryStrQueryPart[] = $strQueryPart;
				}
			}
		}
		if( 1 <= count($aryStrQueryPart) ){
			$retStrQuery = implode(" AND ", $aryStrQueryPart);
		}
		return $retStrQuery;
	}
	//where句を生成----
	public function getFilterArray($boolBinaryDistinctOnDTiS){
		//-----reCount,printTable,dumpToDile,dumpTpFileから呼ばれる
		//----SQLバインド用
		//
		$data1 = $this->getBasicSearchFilterArray($boolBinaryDistinctOnDTiS);
		$data2 = $this->getRichSearchFilterArray($boolBinaryDistinctOnDTiS);
		//
		return array_merge($data1, $data2);
	}

	public function getBasicSearchFilterArray($boolBinaryDistinctOnDTiS){
		//----Table::getFilterArrayから呼ばれるだけ
		$data = array();
		foreach($this->aryObjColumn as $objColumn){
			if( $objColumn->isDBColumn()===true || is_a($objColumn, "WhereQueryColumn")===true ){
				$aryFilterValue = $objColumn->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
				foreach($aryFilterValue as $key => $value){
					//----BIND用に変数の数だけループ
					//
					//----BIND変数は ":VALUE__n" の形式 __nは数字
					$data[$objColumn->getID().'__'.$key] = $value;
					//BIND変数は ":VALUE__n" の形式 __nは数字----
					//
					//BIND用に変数の数だけループ----
				}
			}
		}
		return $data;
	}
	
	public function getRichSearchFilterArray($boolBinaryDistinctOnDTiS){
		//----Table::getFilterArray、reCount,printTable,dumpToDileから呼ばれる
		$data = array();
		foreach($this->aryObjColumn as $objColumn){
			if( $objColumn->isDBColumn()===true || is_a($objColumn, "WhereQueryColumn")===true ){
				if( $objColumn->getRichSearchQuery($boolBinaryDistinctOnDTiS) != "" ){
					//----追加
					$arrayRichValues = $objColumn->getRichFilterValuesForDTiS(true);
					foreach($arrayRichValues as $key => $value){
						//----BIND用に変数の数だけループ
						
						//----BIND変数は ":VALUE_RF__n" の形式 __nは数字
						$data[$objColumn->getID().'_RF__'.$key] = $value;
						//BIND変数は ":VALUE_RF__n" の形式 __nは数字----
						
						//BIND用に変数の数だけループ----
					}
				}
			}
		}
		return $data;
	}
	
	//where句を生成する要素をカラムごとに追加する。----

	public function setDBSortKey($aryKeysortColIdValTypeSeq){
		$this->arySortKeyTypeSet = $aryKeysortColIdValTypeSeq;
	}

	public function addDBSortKey($strKeysortColId, $strValtypeSeq){
		$this->arySortKeyTypeSet[$strKeysortColId] = $strValtypeSeq;
	}

	public function getDBSortKey(){
		return $this->arySortKeyTypeSet;
	}

	public function getDBSortText(){
		$retStrVal = "";
		$arrayBody = array();
		foreach($this->arySortKeyTypeSet as $strKeysortColId => $strValtypeSeq){
			$arrayBody[] = $strKeysortColId . " " . $strValtypeSeq;
		}
		$retStrVal = implode(",", $arrayBody);
		return $retStrVal;
	}

	public function setFormatter($formatter){
		global $g;
		try{
			if(gettype($formatter)=="object"){
				if( is_a($formatter, "ListFormatter") === true ){
					$this->aryObjFormatter[$formatter->getFormatterID()] = $formatter;
				}
				else{
					throw new Exception( '00000100-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
				}
			}
			else{
				throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
			}
		}
		catch (Exception $e){
			$tmpErrMsgBody = $e->getMessage();
			web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$this->getSelfInfoForLog())));
			webRequestForceQuitFromEveryWhere(500,90410102);
			exit();
		}
	}

	public function getFormatter($strFormatterId){
		$retValue = null;
		//-
		if(array_key_exists($strFormatterId,$this->aryObjFormatter)===true){
			$retValue = $this->aryObjFormatter[$strFormatterId];
		}
		return $retValue;
	}

	//----外部からは利用不可の扱いをすること
	public function setPrintingTableID($strPrintTableTagId){
		$this->strPrintingTableId = $strPrintTableTagId;
	}
	//外部からは利用不可の扱いをすること----
	public function getPrintingTableID(){
		return $this->strPrintingTableId;
	}

	public function getPrintFormat($strFormatterId, $strIdOfTableTag=null, $strNumberForRI=null){
		global $g;
		$retStrVal = "";
		$intControlDebugLevel01 = 250;
		$strFxName = __FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		if(array_key_exists($strFormatterId,$this->aryObjFormatter)===true){
			$strNowPrintingId="";
			if( $strNumberForRI != null ){
				$this->aryObjFormatter[$strFormatterId]->setNumberForRI($strNumberForRI);
			}

			if( $strIdOfTableTag === null ){
				$strNowPrintingId = $this->aryObjFormatter[$strFormatterId]->getPrintTableID();
			}else{
				$strNowPrintingId = $strIdOfTableTag;
			}

			//----瞬間存在値なのでセット
			$this->setPrintingTableID($strNowPrintingId);
			//瞬間存在値なのでセット----

			$retStrVal = $this->aryObjFormatter[$strFormatterId]->format($strIdOfTableTag);

			//----瞬間存在値なのでクリア
			$this->setPrintingTableID(null);
			//瞬間存在値なのでクリア----
		}

		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $retStrVal;
	}

	public function setGeneObject($dlcKey, $dlcObject, $boolKeyUnset=false){
		$retBool = true;
		if( $boolKeyUnset === false ){
			$this->arrayObjGene[$dlcKey] = $dlcObject;
		}else{
			$refRetKeyExists = array_key_exists($dlcKey, $this->arrayObjGene);
			if( $refRetKeyExists === true ){
				unset($this->arrayObjGene[$dlcKey]);
			}else{
				$retBool = false;
			}
		}
		return $retBool;
	}

	public function getGeneObject($dlcKey, &$refRetKeyExists=false){
		$refRetKeyExists = array_key_exists($dlcKey, $this->arrayObjGene);
		if( $refRetKeyExists === true ){
			return $this->arrayObjGene[$dlcKey];
		}
		else{
			return null;
		}
	}

	public function getLeftJoinTableQuery(){
		return $this->strLeftJoinTableQuery;
	}

	public function setLeftJoinTableQuery($strQuery){
		$this->strLeftJoinTableQuery = $strQuery;
	}

	//----必須系カラム名の設定・取得用[2015-01-05-1312]
	
	public function getRowIdentifyColumnID(){
		return $this->strRowIdentifyColumnId;
	}
	//----シノニム
	public function getRIColumnID(){
		return $this->getRowIdentifyColumnID();
	}
	//----廃止予定
	public function getDBTablePK(){
		return $this->getRowIdentifyColumnID();
	}
	//廃止予定----
	//シノニム----
	public function setRowIdentifyColumnID($strColId){
		$this->strRowIdentifyColumnId = $strColId;
	}
	//----シノニム
	public function setRIColumnID($strColIdText){
		return $this->setRowIdentifyColumnID($strColIdText);
	}
	//シノニム----

	public function getRequiredRowEditByFileColumnID(){
		return $this->strNDBRowEditByFileColId;
	}
	public function setRequiredRowEditByFileColumnID($strColIdText){
		$this->strNDBRowEditByFileColId = $strColIdText;
	}

	public function getRequiredJnlSeqNoColumnID(){
		return $this->strRequiredJnlSeqColId;
	}
	public function setRequiredJnlSeqNoColumnID($strColIdText){
		$this->strRequiredJnlSeqColId = $strColIdText;
	}

	public function getRequiredJnlRegTimeColumnID(){
		return $this->strRequiredJnlRegTimeColId;
	}
	public function setRequiredJnlRegTimeColumnID($strColIdText){
		$this->strRequiredJnlRegTimeColId = $strColIdText;
	}

	public function getRequiredJnlRegClassColumnID(){
		return $this->strRequiredJnlRegClassColId;
	}
	public function setRequiredJnlRegClassColumnID($strColIdText){
		$this->strRequiredJnlRegClassColId = $strColIdText;
	}

	public function getRequiredUpdateButtonColumnID(){
		return $this->strNDBUpdateBtnColId;
	}
	public function setRequiredUpdateButtonColumnID($strColIdText){
		$this->strNDBUpdateBtnColId = $strColIdText;
	}

	public function getRequiredDisuseColumnID(){
		return $this->strRequiredDisuseColId;
	}
	public function setRequiredDisuseColumnID($strColIdText){
		$this->strRequiredDisuseColId = $strColIdText;
	}

	public function getRequiredNoteColumnID(){
		return $this->strRequiredNoteColId;
	}
	public function setRequiredNoteColumnID($strColIdText){
		$this->strRequiredNoteColId = $strColIdText;
	}

	public function getRequiredLastUpdateDateColumnID(){
		return $this->strRequiredLUDateId;
	}
	public function setRequiredLastUpdateDateColumnID($strColIdText){
		$this->strRequiredLUDateId = $strColIdText;
	}

	public function getRequiredLastUpdateUserColumnID(){
		return $this->strRequiredLUUserColId;
	}
	public function setRequiredLastUpdateUserColumnID($strColIdText){
		$this->strRequiredLUUserColId = $strColIdText;
	}

	public function getRequiredUpdateDate4UColumnID(){
		return $this->strNDBLUDate4UColId;
	}
	public function setRequiredUpdateDate4UColumnID($strColIdText){
		$this->strNDBLUDate4UColId = $strColIdText;
	}
	//必須系カラム名の設定・取得用----

	function prepareColumn(&$arrayVariant=array()){
		global $g;
		$strRIColumnLabel = isset($arrayVariant['TT_SYS_00_ROW_IDENTIFY_LABEL'])?$arrayVariant['TT_SYS_00_ROW_IDENTIFY_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18002");
		$strJnlSeqNoColLabel = isset($arrayVariant['TT_SYS_01_JNL_SEQ_LABEL'])?$arrayVariant['TT_SYS_01_JNL_SEQ_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18003");
		$strJnlRegTimeColLabel= isset($arrayVariant['TT_SYS_02_JNL_TIME_LABEL'])?$arrayVariant['TT_SYS_02_JNL_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18004");
		$strJnlClassColLabel= isset($arrayVariant['TT_SYS_03_JNL_CLASS_LABEL'])?$arrayVariant['TT_SYS_03_JNL_CLASS_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18005");
		$strDisuseFlagColLabel = isset($arrayVariant['TT_SYS_04_DISUSE_FLAG_LABEL'])?$arrayVariant['TT_SYS_04_DISUSE_FLAG_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18006");
		//
		$strRowEditByFileColLabel = isset($arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_LABEL'])?$arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18007");
		$strUpdateColLabel = isset($arrayVariant['TT_SYS_NDB_UPDATE_LABEL'])?$arrayVariant['TT_SYS_NDB_UPDATE_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18008");
		//
		$boolDefaultColumnsSet = isset($arrayVariant['DEFAULT_COLUMNS_SET'])?$arrayVariant['DEFAULT_COLUMNS_SET']:true;
		//
		if( $boolDefaultColumnsSet === true ){
			//----ここから必須系[1]
			$c = new RowEditByFileColumn($this->getRequiredRowEditByFileColumnID(), $strRowEditByFileColLabel);
			$this->addColumn($c);
			
			$c = new JournalSeqNoColumn($this->getRequiredJnlSeqNoColumnID(), $strJnlSeqNoColLabel);
			$this->addColumn($c);
			$c = new JournalRegDateTimeColumn($this->getRequiredJnlRegTimeColumnID(), $strJnlRegTimeColLabel);
			$this->addColumn($c);
			$c = new JournalRegClassColumn($this->getRequiredJnlRegClassColumnID(), $strJnlClassColLabel);
			$this->addColumn($c);
			//
			$c = new UpdBtnColumn($this->getRequiredUpdateButtonColumnID(), $strUpdateColLabel, $this->getRequiredDisuseColumnID());
			$this->addColumn($c);
			$c = new DelBtnColumn($this->getRequiredDisuseColumnID(), $strDisuseFlagColLabel);
			$this->addColumn($c);
			//
			$c = new RowIdentifyColumn($this->getRowIdentifyColumnID(), $strRIColumnLabel);
			$this->addColumn($c);
			//ここまで必須系[1]----
		}
		//
	}

	//----ここからFixColumn系
	public function beforeFixColumn(&$arrayVariant=array()){
		global $g;
		$boolDefaultColumnsSet = isset($arrayVariant['DEFAULT_COLUMNS_SET'])?$arrayVariant['DEFAULT_COLUMNS_SET']:true;
		
		if( $boolDefaultColumnsSet === true ){
			//----ここから必須系[2]
			$strNoteColLabel = isset($arrayVariant['TT_SYS_04_NOTE_LABEL'])?$arrayVariant['TT_SYS_04_NOTE_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18009");
			$c = new NoteColumn($this->getRequiredNoteColumnID(), $strNoteColLabel);
			$this->addColumn($c);
			//ここまで必須系[2]----
		}
		
		if( array_key_exists("ColumnsInsertAfterNote_Body", $arrayVariant) === true ){
			$arrayColsInsertAfterNoteFlg = array();
			if( array_key_exists("ColumnsInsertAfterNote_Unq", $arrayVariant) === true ){
				$arrayColsInsertAfterNoteFlg = $arrayVariant['ColumnsInsertAfterNote_Unq'];
			}
			foreach($arrayVariant['ColumnsInsertAfterNote_Body'] as $objColumn){
				$boolUniqueFlag = false;
				if( array_key_exists($objColumn->getID(), $arrayColsInsertAfterNoteFlg) === true ){
					$boolUniqueFlag = $arrayColsInsertAfterNoteFlg[$objColumn->getID()];
				}
				$this->addColumn($objColumn, $boolUniqueFlag);
			}
		}
		
		if( $boolDefaultColumnsSet === true ){
			//----ここから必須系[3]
			
			$strLastUpdateTimeColLabel = isset($arrayVariant['TT_SYS_05_LUP_TIME_LABEL'])?$arrayVariant['TT_SYS_05_LUP_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18010");
			$strLastUpdateDate4UColLabel = isset($arrayVariant['TT_SYS_NDB_LUP_TIME_LABEL'])?$arrayVariant['TT_SYS_NDB_LUP_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18011");
			
			$boolLastUpdateUserColSet = isset($arrayVariant['TT_SYS_06_LUP_USER_SET'])?$arrayVariant['TT_SYS_06_LUP_USER_SET']:true;
			
			if( $boolLastUpdateUserColSet === true ){
				$strLastUpdateUserColLabel = isset($arrayVariant['TT_SYS_06_LUP_USER_LABEL'])?$arrayVariant['TT_SYS_06_LUP_USER_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18012");
			}
			
			$c = new LastUpdateDateColumn($this->getRequiredLastUpdateDateColumnID(), $strLastUpdateTimeColLabel);
			$this->addColumn($c);
			$c = new LastUpdateDate4UColumn($this->getRequiredUpdateDate4UColumnID(), $strLastUpdateDate4UColLabel);
			$this->addColumn($c);	
			
			if( $boolLastUpdateUserColSet === true ){
				$c = new LastUpdateUserColumn($this->getRequiredLastUpdateUserColumnID(), $strLastUpdateUserColLabel);
				$this->addColumn($c);
			}
			//ここまで必須系[3]----
		}
		
		if( array_key_exists("ColumnsInsertAfterFixs_Body", $arrayVariant) === true ){
			$arrayColsInsertAfterFixsFlg = array();
			if( array_key_exists("ColumnsInsertAfterFixs_Unq", $arrayVariant) === true ){
				$arrayColsInsertAfterFixsFlg = $arrayVariant['ColumnsInsertAfterFixs_Unq'];
			}
			foreach($arrayVariant['ColumnsInsertAfterFixs_Body'] as $objColumn){
				$boolUniqueFlag = false;
				if( array_key_exists($objColumn->getID(), $arrayColsInsertAfterFixsFlg) === true ){
					$boolUniqueFlag = $arrayColsInsertAfterFixsFlg[$objColumn->getID()];
				}
				$this->addColumn($objColumn, $boolUniqueFlag);
			}
		}

		if( array_key_exists("PaddingRightColumn", $arrayVariant) === true ){
			$this->addPaddingRightColumn($arrayVariant['PaddingRightColumn']);
		}

		foreach($this->aryObjColumn as $objColumn){
			$objColumn->beforeFixColumn();
		}

	}

	public function fixColumn($arrayVariant=array()){
		global $g;

		$dbQM = $this->getDBQuoteMark();

		$this->beforeFixColumn($arrayVariant);

		//----ここから本当のFIX
		
		//----FIXされたら、addColumnを禁止する
		$this->boolAddColumnContinue = false;
		if( array_key_exists("AddColumn_Continue", $arrayVariant) === true ){
			$boolAddColumnFlag = $arrayVariant['AddColumn_Continue'];
			if( $boolAddColumnFlag === true ) $this->boolAddColumnContinue = true;
		}
		//FIXされたら、addColumnを禁止する----
		
		//----左外部結合するクエリを作成する
		$intJoinTable = 0;
		$strQuery = "";

		$strWpTblSelfAlias = "{$dbQM}{$this->getShareTableAlias()}{$dbQM}";

		foreach($this->aryObjColumn as $objColumn){
			if(is_a($objColumn, "AutoUpdateUserColumn")){
				if( $objColumn->isDBColumn() === true ){
					$intJoinTable = $intJoinTable + 1;
					$objColumn->setAutoUpdateUserNo($intJoinTable);
					//
					$strWpJoinTblId = "{$dbQM}{$objColumn->getRefJoinTableID()}{$dbQM}";
					$strWpJoinTblAlias = "{$dbQM}JT{$intJoinTable}{$dbQM}";
					$strWpFocusColumnId = "{$dbQM}{$objColumn->GetID()}{$dbQM}";
					$strWpRefJoinColumnId = "{$dbQM}{$objColumn->getRefJoinColumnID()}{$dbQM}";
					//
					$strQuery.="LEFT JOIN {$strWpJoinTblId} {$strWpJoinTblAlias} ON ({$strWpTblSelfAlias}.{$strWpFocusColumnId} = {$strWpJoinTblAlias}.{$strWpRefJoinColumnId})";
				}
			}
		}
		
		$this->setLeftJoinTableQuery($strQuery);
		//左外部結合するクエリを作成する----
		
		//ここまで本当のFIX
		
		$this->afterFixColumn($arrayVariant);
	}

	public function afterFixColumn(&$arrayVariant=array()){
		foreach($this->aryObjColumn as $objColumn){
			$objColumn->afterFixColumn();
		}
	}

	public function addPaddingRightColumn($arrayVariant=array()){
		$intWidth=360;
		if( array_key_exists("width", $arrayVariant) === true ){
			$intWidth = $arrayVariant['width'];
		}
		$objColumn = new TextColumn("TT_SYS_DUMMY_WIDTH_PADDING","");
		$objColumn->setHeader(true);
		$objColumn->setOutputType("filter_table", new OutputType(new TabHFmt(), new StaticTextTabBFmt("<div style=\"width:{$intWidth}px;height:10px\"><div>")));
		$objColumn->setDBColumn(false);
		$objColumn->getOutputType("print_table")->setVisible(false);
		$objColumn->getOutputType("update_table")->setVisible(false);
		$objColumn->getOutputType("delete_table")->setVisible(false);
		$objColumn->getOutputType("register_table")->setVisible(false);
		$objColumn->getOutputType("print_journal_table")->setVisible(false);
		$objColumn->getOutputType("excel")->setVisible(false);
		$objColumn->getOutputType("csv")->setVisible(false);
		$this->addColumn($objColumn);
	}
	//ここまでFixColumn系----

	public function setCommitSpanOnTableIUDByFile($varValue){
		$this->varCommitSpanOnTableIUDByFile = $varValue;
	}
	public function getCommitSpanOnTableIUDByFile(){
		return $this->varCommitSpanOnTableIUDByFile;
	}

    function inTrzLockSequences($arrayObjColumn){
        global $g;
        $intControlDebugLevel01=200;
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        $retBool = false;
        try{
            $arraySequence = array();
            //----シーケンスを収集する
            foreach($arrayObjColumn as $objColumn){
                $aryRetBody = $objColumn->getSequencesForTrzStart($arraySequence);
                if( $aryRetBody[0]===false ){
                    throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            //シーケンスを収集する----
            //----昇り順でキーをソートする
            ksort($arraySequence, SORT_STRING);
            //昇り順でキーをソートする----
            foreach($arraySequence as $key=>$strSequenceId){
                //----関連シーケンスを捕まえる（デッドロック防止）
                $arrayLockRet = getSequenceValue($strSequenceId, true, true);
                if( $arrayLockRet[1]!= 0 ){
                    // 捕まえられなかった
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5004",array($strFxName,$strSequenceId)));
                    throw new Exception( '00000200-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
                }
                //関連シーケンスを捕まえる（デッドロック防止）----
            }
            $retBool = true;
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-5001",array($tmpErrMsgBody,$intErrorStatus)));
            webRequestForceQuitFromEveryWhere(500,90410103);
            exit();
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $retBool;
    }
	
	//----ファイル読み書き系
	public function writeAllToFileOnce($miFilePath, $strBody, $mode="wb"){
		$filepointer=fopen( $miFilePath , $mode);
		$bool = flock($filepointer, LOCK_EX);
		if( $bool === true ){
			fwrite($filepointer, $strBody);
			flock($filepointer, LOCK_UN);
		}
		fclose($filepointer);
		return $bool;
	}

	public function readAllFromFileOnce($miFilePath, &$strBody){
		$bool = file_exists($miFilePath);
		if( $bool === true ){
			$filepointer = fopen($miFilePath ,"rb");
			$strBody = fread($filepointer, filesize($miFilePath));
			fclose($filepointer);
		}
		return $bool;
	}
	//ファイル読み書き系----

	//----デバッグ用
	public function compareMainTableAndConfig($mode=1){
		return getMainTableColumnStatus($mode, $this);
	}
	//デバッグ用----

}

class TemplateTableForReview extends TableControlAgent {

	protected $pageType;

	protected $strEditStatusColumnId;
	protected $strLockTargetColumnId;

	protected $strApplyUpdateColId;
	protected $strApplyUserColId;
	protected $strConfirmUpdateColId;
	protected $strConfirmUserColId;

	//----ステータス名
	protected $statusNameOfOnEdit;
	protected $statusNameOfWithdrawned;
	protected $statusNameOfWaitForAccept;
	protected $statusNameOfAccepted;
	protected $statusNameOfNonsuited;
	//ステータス名----

	//----処理名
	protected $actionNameOfApplyRegistrationForNew;
	protected $actionNameOfApplyRegistrationForUpdate;
	protected $actionNameOfApplyUpdate;
	protected $actionNameOfApplyExecute;
	protected $actionNameOfApplyEditRestart;
	protected $actionNameOfApplyWithdrawn;

	protected $actionNameOfConfirmUpdate;
	protected $actionNameOfConfirmReturn;
	protected $actionNameOfConfirmAccept;
	protected $actionNameOfConfirmNonsuit;

	protected $actionNameOfLogicDeleteOn;
	protected $actionNameOfLogicDeleteOff;
	//処理名----

	protected $strDBResultTableAgentQuery;
	protected $strDBResultTableId;

	protected $strDBResultTableHiddenId;
	protected $strDBResultTableSeqId;

	protected $strDBResultJournalTableAgentQuery;
	protected $strDBResultJournalTableId;

	protected $strDBResultJournalTableHiddenId;
	protected $strDBResultJournalTableSeqId;

	public function __construct($strEditTableWkPKId, $strEditTableWkPKLabel, $strResultTableWkPKId, $strResultTableWkPKLabel, $pageType, $arrayVariant=array()){
		global $g;

        //----編集系テーブル
        list($strTableIdOfEditTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','ID'),'DEFAULT_EDIT_TABLE');
        list($strTableIdOfEditJnlTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','JNL','ID'),'DEFAULT_EDIT_JNL_TABLE');

        list($strTableHiddenIdOfEditTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','HiddenID'),null);
        list($strTableHiddenIdOfEditJnlTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','JNL','HiddenID'),null);

        //----編集系テーブルSEQ
        list($strEditTableRICSeq,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','RICSequence'),$strTableIdOfEditTable.'_RIC');
        list($strEditTableJNLSeq,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('editTable','JNL','Sequence'),$strTableIdOfEditTable.'_JSQ');
        //編集系テーブルSEQ----
        //編集系テーブル----

        //----結果系テーブル
        list($strTableIdOfResultTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','ID'),'DEFAULT_RESULT_TABLE');
        list($strTableIdOfResultJnlTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','JNL','ID'),'DEFAULT_RESULT_JNL_TABLE');

        list($strTableHiddenIdOfResultTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','HiddenID'),null);
        list($strTableHiddenIdOfResultJnlTable,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','JNL','HiddenID'),null);
        
        //----承認済閲覧専用テーブル用SEQ
        list($strResultTableRICSeq,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','RICSequence'),$strTableIdOfResultTable.'_RIC');
        list($strResultTableJNLSeq,$boolSet)=isSetInArrayNestThenAssign($arrayVariant,array('resultTable','JNL','Sequence'),$strTableIdOfResultTable.'_JSQ');
        //承認済閲覧専用テーブル用SEQ----

        //結果系テーブル----

        if( $pageType=="apply" || $pageType=="confirm" ){
            $strWkPKId = $strEditTableWkPKId;
            $strWkPKName = $strEditTableWkPKLabel;
            $strMainTableName = $strTableIdOfEditTable;
            $strMainTableJournal = $strTableIdOfEditJnlTable;
        }else{
            $strWkPKId = $strResultTableWkPKId;
            $strWkPKName = $strResultTableWkPKLabel;
            $strMainTableName = $strTableIdOfResultTable;
            $strMainTableJournal = $strTableIdOfResultJnlTable;
        }

        //----parent::__constructの前でないと渡せない
        $arrayVariant['DEFAULT_COLUMNS_SET'] = false;
        //parent::__constructの前でないと渡せない----

        parent::__construct($strMainTableName, $strWkPKId, $strWkPKName, $strMainTableJournal, $arrayVariant);
        $this->setInitInfo(debug_backtrace($limit=1));

        //----__constructの後でないと初期化される
        $this->setDBMainTableHiddenID($strTableHiddenIdOfEditTable);
        $this->setDBJournalTableHiddenID($strTableHiddenIdOfEditJnlTable);
        //----__constructの後でないと初期化される----

        $this->setDBResultTableID($strTableIdOfResultTable);
        $this->strDBResultTableAgentQuery = null;
        $this->setDBResultTableHiddenID($strTableHiddenIdOfResultTable);

        $this->setDBResultJournalTableID($strTableIdOfResultJnlTable);
        $this->strDBResultJournalTableAgentQuery = null;
        $this->setDBResultJournalTableHiddenID($strTableHiddenIdOfResultJnlTable);

        $this->pageType = $pageType;

        //----固有の必須カラム
        $strEditStatusId       = isset($arrayVariant['TT_SYS_51_EDIT_STATAS_ID'])?$arrayVariant['TT_SYS_51_EDIT_STATAS_ID']:"EDIT_STATUS";
        $strEditStatusButtonId = isset($arrayVariant['TT_SYS_NDB_EDIT_STATUS_BTN_ID'])?$arrayVariant['TT_SYS_NDB_EDIT_STATUS_BTN_ID']:"EDIT_STATUS_BTN";
        //
        $strApplyUpdateColId   = isset($arrayVariant['TT_SYS_53_APPLY_TIME_ID'])?$arrayVariant['TT_SYS_53_APPLY_TIME_ID']:"APPLY_UPDATE_TIMESTAMP";
        $strApplyUserColId     = isset($arrayVariant['TT_SYS_52_APPLY_USER_ID'])?$arrayVariant['TT_SYS_52_APPLY_USER_ID']:"APPLY_UPDATE_USER";
        $strConfirmUpdateColId = isset($arrayVariant['TT_SYS_55_CONFIRM_TIME_ID'])?$arrayVariant['TT_SYS_55_CONFIRM_TIME_ID']:"CONFIRM_UPDATE_TIMESTAMP";
        $strConfirmUserColId   = isset($arrayVariant['TT_SYS_54_CONFIRM_USER_ID'])?$arrayVariant['TT_SYS_54_CONFIRM_USER_ID']:"CONFIRM_UPDATE_USER";
        //固有の必須カラム----

        $this->setEditStatusColumnID($strEditStatusId);
        $this->setLockTargetColumnID($strResultTableWkPKId);

        $this->setApplyUpdateColumnID($strApplyUpdateColId);
        $this->setApplyUserColumnID($strApplyUserColId);
        $this->setConfirmUpdateColumnID($strConfirmUpdateColId);
        $this->setConfirmUserColumnID($strConfirmUserColId);

        $c = new RowEditByFileColumnForReview($this->getRequiredRowEditByFileColumnID());
        $this->addColumn($c);
        $c = new JournalSeqNoColumn($this->getRequiredJnlSeqNoColumnID());
        $c->setSequenceID($strEditTableJNLSeq);
        $this->addColumn($c);
        $c = new JournalRegDateTimeColumn($this->getRequiredJnlRegTimeColumnID());
        $this->addColumn($c);
        $c = new JournalRegClassColumn($this->getRequiredJnlRegClassColumnID());
        $this->addColumn($c);

        /*
        $strColLabel01 = "更新";
        $strColLabel02 = "廃止";
        $strColLabel03 = "各種処理";
        $strColLabel04 = "編集ステータス";

        $strColDescript01 = "Webブラウザでみ表示される列。";
        $strColDescript02 = "このテーブルのID。自動採番のため編集不可。新規登録時は記載不要。";
        $strColDescript03 = "このテーブルから編集するテーブルのID。修正申請の新規登録時を除いて編集不可。";
        $strColDescript04 = "このテーブルのID。編集不可。";
        $strColDescript05 = "このテーブルを編集するテーブルのID。編集不可。";
        $strColDescript06 = "各レコードの編集ステータス。";

        $strTextCmd01 = "編集中";
        $strTextCmd02 = "取下済";
        $strTextCmd03 = "申請中";
        $strTextCmd04 = "承認済";
        $strTextCmd05 = "却下済";
        $strTextCmd06 = "新規申請";
        $strTextCmd07 = "修正申請";
        $strTextCmd08 = "内容変更";
        $strTextCmd09 = "申請";
        $strTextCmd10 = "編集再開";
        $strTextCmd11 = "取下";
        $strTextCmd12 = "職権変更";
        $strTextCmd13 = "差戻";
        $strTextCmd14 = "承認";
        $strTextCmd15 = "却下";
        $strTextCmd16 = "廃止";
        $strTextCmd17 = "復活";
        */

        $strColLabel01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19001");
        $strColLabel02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19002");
        $strColLabel03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19003");
        $strColLabel04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19004");

        $strColDescript01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19005");
        $strColDescript02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19006");
        $strColDescript03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19007");
        $strColDescript04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19008");
        $strColDescript05 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19009");
        $strColDescript06 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19010");

        $strTextCmd01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19011");
        $strTextCmd02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19012");
        $strTextCmd03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19013");
        $strTextCmd04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19014");
        $strTextCmd05 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19015");
        $strTextCmd06 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19016");
        $strTextCmd07 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19017");
        $strTextCmd08 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19018");
        $strTextCmd09 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19019");
        $strTextCmd10 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19020");
        $strTextCmd11 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19021");
        $strTextCmd12 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19022");
        $strTextCmd13 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19023");
        $strTextCmd14 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19024");
        $strTextCmd15 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19025");

        $strTextCmd16 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19030");
        $strTextCmd17 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19031");

        //----更新ボタン
        if($pageType=="apply"){
            $strColLabel01 = $strTextCmd08;
        }else if($pageType=="confirm"){
            $strColLabel01 = $strTextCmd12;
        }else{
            $strColLabel01 = $strTextCmd07;
        }
        $c = new EditLockUpdBtnColumn($this->getRequiredUpdateButtonColumnID(),$strColLabel01,$this->getRequiredDisuseColumnID(),$strEditStatusId,$strApplyUserColId);
        $c->setDescription($strColDescript01);
        $this->addColumn($c);
        //更新ボタン----
        
        $c = new DelBtnColumn($this->getRequiredDisuseColumnID(),$strColLabel02);
        $this->addColumn($c);

        if($pageType=="apply"){
            //----申請者ページのみ
            
            $c = new EditStatusControlBtnColumn($strEditStatusButtonId,$strColLabel03,$this->getRequiredDisuseColumnID(),$strEditStatusId,$strApplyUserColId, $pageType);
            $c->setDescription($strColDescript01);
            $this->addColumn($c);
            
            //申請者ページのみ----
        }else if($pageType=="confirm"){
            //----承認者ページのみ
            
            $c = new EditStatusControlBtnColumn($strEditStatusButtonId,$strColLabel03,$this->getRequiredDisuseColumnID(),$strEditStatusId,$strApplyUserColId,$pageType);
            $c->setDescription($strColDescript01);
            $this->addColumn($c);
            
            $this->getFormatter('register_table')->setGeneValue('hidden',true);
            
            //承認者ページのみ----
        }else{
            //----参照ページのみ
            
            $this->getFormatter('register_table')->setGeneValue('hidden',true);
            
            //参照ページのみ----
        }

        if($pageType=="apply" || $pageType=="confirm"){
            $c = new RowIdentifyColumn($strWkPKId, $strWkPKName);
            $c->setDescription($strColDescript02);
            $c->setSequenceID($strEditTableRICSeq);
            $this->addColumn($c);

            //-----ロック保存対象
            $c = new LockTargetColumn($strResultTableWkPKId, $strResultTableWkPKLabel);
            $c->setDescription($strColDescript03);
            $this->addColumn($c);
            //ロック保存対象-----
        }else{
            //----参照ページのみ
            
            //-----ロック保存対象
            $c = new LockTargetColumn($strEditTableWkPKId, $strEditTableWkPKLabel);
            $c->setDescription($strColDescript02);
            $c->setAllowSendFromFile(false);
            $this->addColumn($c);
            //ロック保存対象-----
            
            $c = new RowIdentifyColumn($strWkPKId, $strWkPKName);
            $c->setDescription($strColDescript03);
            $c->setAllowSendFromFile(true);
            $c->setSequenceID($strEditTableRICSeq);
            $this->addColumn($c);
            
            //参照ページのみ----
        }
        
        //-----ステータスID
        $c = new EditStatusControlIDColumn($strEditStatusId, $strColLabel04);
        $c->setDescription($strColDescript06);
        $c->setResultTableRowIdentifySequenceID($strResultTableRICSeq);
        $c->setResultJournalTableSequenceID($strResultTableJNLSeq);
        $this->addColumn($c);
        //ステータスID-----
        //
        $this->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$strTableIdOfEditTable);

        $this->setStatusNameOnEdit($strTextCmd01);
        $this->setStatusNameOfWithdrawned($strTextCmd02);
        $this->setStatusNameOfWaitForAccept($strTextCmd03);
        $this->setStatusNameOfAccepted($strTextCmd04);
        $this->setStatusNameOfNonsuited($strTextCmd05);

        $this->setActionNameOfApplyRegistrationForNew($strTextCmd06);

        $this->setActionNameOfApplyRegistrationForUpdate($strTextCmd07);

        $this->setActionNameOfApplyUpdate($strTextCmd08);
        $this->setActionNameOfApplyExecute($strTextCmd09);
        $this->setActionNameOfApplyEditRestart($strTextCmd10);
        $this->setActionNameOfApplyWithdrawn($strTextCmd11);

        $this->setActionNameOfConfirmUpdate($strTextCmd12);
        $this->setActionNameOfConfirmReturn($strTextCmd13);
        $this->setActionNameOfConfirmAccept($strTextCmd14);
        $this->setActionNameOfConfirmNonsuit($strTextCmd15);

        $this->setActionNameOfLogicDeleteOn($strTextCmd16);
        $this->setActionNameOfLogicDeleteOff($strTextCmd17);

        $this->setMinorVersion(2);
        $this->setJsEventNamePrefix(true);

    }
    //----結果Current系
    public function setDBResultTableID($strValue){
        return $this->strDBResultTableId = $strValue;
    }
    public function getDBResultTableID(){
        return $this->strDBResultTableId;
    }
    public function setDBResultTableAgentQuery($strQuery){
        $this->strDBResultTableAgentQuery = $strQuery;
    }
    public function getDBResultTableAgentQuery(){
        return $this->strDBResultTableAgentQuery;
    }
    public function getDBResultTableBody(){
        //----結合クエリまたはDBテーブル名を返す
        $retStrVal=$this->getDBResultTableID();
        if( $this->strDBResultTableAgentQuery !== null ){
            $retStrVal=$this->strDBResultTableAgentQuery;
        }
        return $retStrVal;
    }
    public function setDBResultTableHiddenID($strValue){
        $this->strDBResultTableHiddenId = $strValue;
    }
    public function getDBResultTableHiddenID(){
        return $this->strDBResultTableHiddenId;
    }
    //結果Current系----

    //----結果履歴系
    public function setDBResultJournalTableID($strValue){
        return $this->strDBResultJournalTableId = $strValue;
    }
    public function getDBResultJournalTableID(){
        return $this->strDBResultJournalTableId;
    }
    public function setDBResultJournalTableAgentQuery($strQuery){
        $this->strDBResultJournalTableAgentQuery = $strQuery;
    }
    public function getDBResultJournalTableAgentQuery(){
        return $this->strDBResultJournalTableAgentQuery;
    }
    public function getDBResultJournalTableBody(){
        //----結合クエリまたはDBテーブル名を返す
        $retStrVal=$this->getDBResultJournalTableID();
        if( $this->strDBResultJournalTableAgentQuery !== null ){
            $retStrVal=$this->strDBResultJournalTableAgentQuery;
        }
        return $retStrVal;
    }
    public function setDBResultJournalTableHiddenID($strValue){
        $this->strDBResultJournalTableHiddenId = $strValue;
    }
    public function getDBResultJournalTableHiddenID(){
        return $this->strDBResultJournalTableHiddenId;
    }
    //結果履歴系----
    //----読み取り専用プロパティ
    function getPageType(){
        return $this->pageType;
    }
    //読み取り専用プロパティ----

    function setEditStatusColumnID($strValue){
        $this->strEditStatusColumnId = $strValue;
    }
    function getEditStatusColumnID(){
        return $this->strEditStatusColumnId;
    }
    function setLockTargetColumnID($strValue){
        $this->strLockTargetColumnId = $strValue;
    }
    function getLockTargetColumnID(){
        return $this->strLockTargetColumnId;
    }
    function setApplyUpdateColumnID($strValue){
        $this->strApplyUpdateColId = $strValue;
    }
    function getApplyUpdateColumnID(){
        return $this->strApplyUpdateColId;
    }
    function setApplyUserColumnID($strValue){
        $this->strApplyUserColId = $strValue;
    }
    function getApplyUserColumnID(){
        return $this->strApplyUserColId;
    }
    function setConfirmUpdateColumnID($strValue){
        $this->strConfirmUpdateColId = $strValue;
    }
    function getConfirmUpdateColumnID(){
        return $this->strConfirmUpdateColId;
    }
    function setConfirmUserColumnID($strValue){
        $this->strConfirmUserColId = $strValue;
    }
    function getConfirmUserColumnID(){
        return $this->strConfirmUserColId;
    }

    //----ステータス名
    function setStatusNameOnEdit($strValue){
        $this->statusNameOfOnEdit = $strValue;
    }
    function getStatusNameOnEdit(){
        return $this->statusNameOfOnEdit;
    }

    function setStatusNameOfWithdrawned($strValue){
        $this->statusNameOfWithdrawned = $strValue;
    }
    function getStatusNameOfWithdrawned(){
        return $this->statusNameOfWithdrawned;
    }

    function setStatusNameOfWaitForAccept($strValue){
        $this->statusNameOfWaitForAccept = $strValue;
    }
    function getStatusNameOfWaitForAccept(){
        return $this->statusNameOfWaitForAccept;
    }

    function setStatusNameOfAccepted($strValue){
        $this->statusNameOfAccepted = $strValue;
    }
    function getStatusNameOfAccepted(){
        return $this->statusNameOfAccepted;
    }

    function setStatusNameOfNonsuited($strValue){
        $this->statusNameOfNonsuited = $strValue;
    }
    function getStatusNameOfNonsuited(){
        return $this->statusNameOfNonsuited;
    }
    //ステータス名----

    //----ここから申請者用
    function setActionNameOfApplyRegistrationForNew($strValue){
        $this->actionNameOfApplyRegistrationForNew = $strValue;
    }
    function getActionNameOfApplyRegistrationForNew(){
        return $this->actionNameOfApplyRegistrationForNew;
    }
    function setActionNameOfApplyRegistrationForUpdate($strValue){
        $this->actionNameOfApplyRegistrationForUpdate = $strValue;
    }
    function getActionNameOfApplyRegistrationForUpdate(){
        return $this->actionNameOfApplyRegistrationForUpdate;
    }

    function setActionNameOfApplyUpdate($strValue){
        $this->actionNameOfApplyUpdate = $strValue;
    }
    function getActionNameOfApplyUpdate(){
        return $this->actionNameOfApplyUpdate;
    }

    function setActionNameOfApplyExecute($strValue){
        $this->actionNameOfApplyExecute = $strValue;
    }
    function getActionNameOfApplyExecute(){
        return $this->actionNameOfApplyExecute;
    }

    function setActionNameOfApplyEditRestart($strValue){
        $this->actionNameOfApplyEditRestart = $strValue;
    }
    function getActionNameOfApplyEditRestart(){
        return $this->actionNameOfApplyEditRestart;
    }

    function setActionNameOfApplyWithdrawn($strValue){
        $this->actionNameOfApplyWithdrawn = $strValue;
    }
    function getActionNameOfApplyWithdrawn(){
        return $this->actionNameOfApplyWithdrawn;
    }
    //ここまで申請者用----

    //----ここから承認権者
    function setActionNameOfConfirmUpdate($strValue){
        $this->actionNameOfConfirmUpdate = $strValue;
    }
    function getActionNameOfConfirmUpdate(){
        return $this->actionNameOfConfirmUpdate;
    }

    function setActionNameOfConfirmReturn($strValue){
        $this->actionNameOfConfirmReturn = $strValue;
    }
    function getActionNameOfConfirmReturn(){
        return $this->actionNameOfConfirmReturn;
    }
    function setActionNameOfConfirmAccept($strValue){
        $this->actionNameOfConfirmAccept = $strValue;
    }
    function getActionNameOfConfirmAccept(){
        return $this->actionNameOfConfirmAccept;
    }

    function setActionNameOfConfirmNonsuit($strValue){
        $this->actionNameOfConfirmNonsuit = $strValue;
    }
    function getActionNameOfConfirmNonsuit(){
        return $this->actionNameOfConfirmNonsuit;
    }
    //ここまで承認権者----

    //----廃止/復活用
    function setActionNameOfLogicDeleteOn($strValue){
        $this->actionNameOfLogicDeleteOn = $strValue;
    }
    function getActionNameOfLogicDeleteOn(){
        return $this->actionNameOfLogicDeleteOn;
    }
    function setActionNameOfLogicDeleteOff($strValue){
        $this->actionNameOfLogicDeleteOff = $strValue;
    }
    function getActionNameOfLogicDeleteOff(){
        return $this->actionNameOfLogicDeleteOff;
    }
    //廃止/復活用----

    function fixColumn($arrayVariant=array()){
        global $g;
        $pageType = $this->pageType;

        //$strColLabel01 = "申請者更新日時";
        //$strColLabel02 = "申請者氏名";
        //$strColLabel03 = "承認権者更新日時";
        //$strColLabel04 = "承認権者氏名";

        //$strColLabel01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19026");
        //$strColLabel02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19027");
        //$strColLabel03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19028");
        //$strColLabel04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19029");

        $strApplyUpdateColLabel   = isset($arrayVariant['TT_SYS_53_APPLY_TIME_LABEL'])?$arrayVariant['TT_SYS_53_APPLY_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-19026");
        $strApplyUserColLabel     = isset($arrayVariant['TT_SYS_52_APPLY_USER_LABEL'])?$arrayVariant['TT_SYS_52_APPLY_USER_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-19027");
        $strConfirmUpdateColLabel = isset($arrayVariant['TT_SYS_55_CONFIRM_TIME_LABEL'])?$arrayVariant['TT_SYS_55_CONFIRM_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-19028");
        $strConfirmUserColLabel   = isset($arrayVariant['TT_SYS_54_CONFIRM_USER_LABEL'])?$arrayVariant['TT_SYS_54_CONFIRM_USER_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-19029");

        //----履歴系
        $arrayObj = array();

        $c = new AutoUpdateTimeColumn($this->getApplyUpdateColumnID(),$strApplyUpdateColLabel);
        if($pageType!="apply") $c->setUpdateMode(false);
        $arrayObj[] = $c;

        $c = new AutoUpdateUserColumn($this->getApplyUserColumnID(),$strApplyUserColLabel);
        $c->setSqlFixSet(array("",""));
        if($pageType!="apply") $c->setUpdateMode(false);
        $arrayObj[] = $c;

        $c = new AutoUpdateTimeColumn($this->getConfirmUpdateColumnID(),$strConfirmUpdateColLabel);
        if($pageType!="confirm") $c->setUpdateMode(false);
        $arrayObj[] = $c;

        $c = new AutoUpdateUserColumn($this->getConfirmUserColumnID(),$strConfirmUserColLabel);
        $c->setSqlFixSet(array("",""));
        if($pageType!="confirm") $c->setUpdateMode(false);
        $arrayObj[] = $c;
        //履歴系----

        $arrayVariant['ColumnsInsertAfterNote_Body'] = $arrayObj;

        parent::fixColumn($arrayVariant);
    }
}
?>