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
//    ・原則として、DBから出力された論理情報を、最終出力形式への加工工程前に、事前に加工する。
//
//////////////////////////////////////////////////////////////////////

class OutputType {
	//----他のクラスから独立して利用するための設定
	protected $strListFormatterMode;
	//他のクラスから独立して利用するための設定----

	protected $strFormatterId;  // as string フォーマット名(旧formatName)
	protected $visible;	 // as boolean 表示フラグ
	
	protected $varDefaultInputed;
	protected $varOverrideInputed;
	
	protected $bData; // as array ボディの個別データ
	
	protected $tTagLastAttr;
	protected $printSeq;
	
	protected $strDescription;

	protected $strErrMsgHead;
	protected $strErrMsgTail;

	//----参照
	protected $objColumn;	  // as Column 親
	protected $head;  // as HFmt ヘッダの表示フォーマット
	protected $body;  // as BFmt ボディの表示フォーマット
	//参照----

	protected $objFunctionForGetFADSelectList;

	//----ここから継承メソッドの上書き処理
	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理
	//NEW[1]
	function __construct($head, $body, $visible = true){
		$this->head = $head;
		$this->body = $body;
		$this->visible = $visible;
		$this->bData = array("js"=>array(), "attr"=>array());
		$this->tTagLastAttr = "";
		$this->printSeq = null;
		$this->varDefaultInputed = null;
		$this->varOverrideInputed = null;
		$this->setListFormatterModeAgent("");
		$this->setErrMsgHeadAgent("");
		$this->setErrMsgTailAgent("");
		$this->setFunctionForGetFADSelectList(null);
	}

	//NEW[2]
	function init($objColumn, $strFormatterId){
		$this->objColumn = $objColumn;
		$this->strFormatterId = $strFormatterId;
		
		$this->head->init($objColumn, $strFormatterId);
		$this->body->init($objColumn, $strFormatterId);
	}

	//----フォーマット系オブジェクトの参照の取得・設定系
	//NEW[3]
	function setHead($head){
		$this->head = $head;
		$this->head->init($this->objColumn);
	}
	//NEW[4]
	function getHead(){
		return $this->head;
	}
	//NEW[5]
	function setBody($body){
		$this->body = $body;
		$this->body->init($this->objColumn);
	}
	//NEW[6]
	function getBody(){
		return $this->body;
	}
	//フォーマット系オブジェクトの参照の取得・設定系の参照の取得・設定系----

	//----他のクラスから独立して利用するための関数
	//NEW[7]
	public function setListFormatterModeAgent($strListFormatterMode){
		$this->strListFormatterMode = $strListFormatterMode;
	}
	//NEW[8]
	public function getListFormatterMode(){
		$retValue = $this->strListFormatterMode;
		if( strlen($retValue)==0 ){
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
		}
		return $retValue;
	}
	//NEW[9]
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
	//他のクラスから独立して利用するための関数----

	//----is-set型メソット----
	//NEW[10]
	function setVisible($visible){
		$this->visible = $visible;
	}
	//NEW[11]
	function isVisible(){
		return $this->visible;
	}
	//is-set型メソット--------

	//NEW[12]
	function setPrintSeq($intSeqIndex){
		$this->printSeq = $intSeqIndex;
	}
	//NEW[13]
	function getPrintSeq(){
		return $this->printSeq;
	}

	//----入力
	//NEW[14]
	function setAttr($attrName, $attrValue){
		$this->bData['attr'][$attrName] = $attrValue;
	}
	//NEW[15]
	function setDefaultAttr($attrName, $attrValue){
		//----属性が設定されていない場合だけ、属性を付加する。
		if( array_key_exists($attrName, $this->bData['attr']) === false ){
			$this->setAttr($attrName, $attrValue);
		}
	}
	//NEW[16]
	function delAttr($attrName){
		unset($this->bData['attr'][$attrName]);
	}
	//入力----

	//----出力
	//NEW[17]
	function getAttr($attrName){
		//----鍵がなければNULLを返す
		return key_exists($attrName, $this->bData['attr'])?$this->bData['attr'][$attrName]: NULL;
	}
	//NEW[18]
	function getAttrs(){
		return $this->bData['attr'];
	}
	//出力----

	//NEW[19]
	function setJsEvent($eventName, $jsFunctionName, $jsFunctionArgs=array()){
		/*
			同じイベント名での多重登録は抑止する
		*/
		if( is_null($jsFunctionArgs) === true ){
			//----引数がnullで指定されていた場合
			$jsFunctionArgs = array();
		}
		$this->bData['js'][$eventName] = new JsEvent($eventName, $jsFunctionName, $jsFunctionArgs);
	}
	//NEW[20]
	function getJsEvents(){
		return $this->bData['js'];
	}
	//NEW[21]
	function getJsAttrs($rowData){
		$js = " ";
		$functionPreFix = "";
		
		if( is_a($this->objColumn, "Column")===true ){
			$objTable = $this->objColumn->getTable();
			if( is_a($objTable, "TableControlAgent")===true ){
				if( $objTable->getJsEventNamePrefix()===true ){
					$functionPreFix = $objTable->getPrintingTableId()."_";
				}
			}
		}
		
		foreach($this->getJsEvents() as $jsEvent){
			$js .= $jsEvent->getJsAttr($rowData, $functionPreFix)." ";
		}
		return $js;
		
	}

	//----いずれは、移動予定のプロパティ。bodyがTextTagだった場合、最後の属性を付加させる
	//NEW[22]
	function setTextTagLastAttr($tTagLastAttr){
		$this->tTagLastAttr = $tTagLastAttr;
	}
	//NEW[23]
	function getTextTagLastAttr(){
		return $this->tTagLastAttr;
	}
	//いずれは、移動予定のプロパティ。bodyがTextTagだった場合、最後の属性を付加させる----

	//----I要素用の特別の入力管理(Input系のvalue属性、TEXTAREAのinnerHtml要素)
	//NEW[24]
	function setDefaultInputValue($varValue){
		$this->varDefaultInputed = $varValue;
	}
	//NEW[25]
	function getDefaultInputValue(){
		return $this->varDefaultInputed;
	}
	//I要素用の特別の入力管理(Input系のvalue属性、TEXTAREAのinnerHtml要素)----

	//----I要素用の特別の入力管理(Input系のvalue属性、TEXTAREAのinnerHtml要素)
	//NEW[26]
	function setOverrideInputValue($varValue){
		$this->varOverrideInputed = $varValue;
	}
	//NEW[27]
	function getOverrideInputValue(){
		return $this->varOverrideInputed;
	}
	//I要素用の特別の入力管理(Input系のvalue属性、TEXTAREAのinnerHtml要素)----

	//----htmlタグ取得用(2014-12-01名前にTagを追加)
	//NEW[28]
	function getHeadTag($colNo, $attr=""){
		return $this->head->getData($colNo, $attr);
	}
	//NEW[29]
	function getBodyTag($rowData,$aryVariant){
		//----$rowData「NULLまたは連想配列を想定」
		global $g;
		$intControlDebugLevel01=200;
		$strFxName = __CLASS__."::".__FUNCTION__;
		$strInitedColId = $this->objColumn->getID();
		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $this->body->getData($rowData,$aryVariant);
	}
	//htmlタグ取得用(2014-12-01名前にTagを追加)----

	function getBodyTagDuplicate($rowData, $aryVariant, $option){
		//----$rowData「NULLまたは連想配列を想定」
		global $g;
		$intControlDebugLevel01=200;
		$strFxName = __CLASS__."::".__FUNCTION__;
		$strInitedColId = $this->objColumn->getID();
		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		return $this->body->getDataDuplicate($rowData, $aryVariant, $option);
	}

	//NEW[29]
	public function setDescription($strDescription){
		$this->strDescription = $strDescription;
	}
	//NEW[30]
	public function getDescription(){
		$retValue = $this->strDescription;
		if( strlen($retValue)==0 ){
			if( is_a($this->objColumn, "Column")===true ){
				$retValue = $this->objColumn->getDescription();
			}
		}
		return $retValue;
	}

	//NEW[32]
	function setFunctionForGetFADSelectList($objFunctionForGetFADSelectList){
		$this->objFunctionForGetFADSelectList = $objFunctionForGetFADSelectList;
	}
	//NEW[33]
	function getFunctionForGetFADSelectList(){
		return $this->objFunctionForGetFADSelectList;
	}

	//NEW[34]
        //表示ファイルのプルダウンに表示するデータを取得
	// OutputType Class
	public function getFADSelectList(&$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
		$retBool = false;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$aryDataSet = array();

		$strFxName = __CLASS__."::".__FUNCTION__;

		$objFunction = $this->getFunctionForGetFADSelectList();
		if( is_callable($objFunction) ){
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$retArray = $objFunction($this, $aryVariant, $arySetting, $aryOverride, $objColumn);
		}else{
			if( $this->checkListFormatterMode("FilterConditionTableFormatter") === true ){
				$boolExecute = true;

				$objColumn = $this->objColumn;
				$objTable = $this->objColumn->getTable();
                                // ---- RBAC対応
                                $AccessAuthColumn_flag = false;
                                if($objColumn->getID() == $objTable->getAccessAuthColumnName()) {
                                    if($objTable->getAccessAuth() === true) {
                                        $AccessAuthColumn_flag = true;
                                    }
                                }
                                // RBAC対応 ----
				$objFocusCF = $this->getBody();
				$strFADMaxWidthOfSelectTag = $objFocusCF->getFADMaxWidthOfSelectTag();
				$strFADClassOfSelectTag = $objFocusCF->getFADClassOfSelectTag();

				$selectPrintType = $objColumn->getAddSelectTagPrintType();

				$strWhereAddBody = "";
				$strTableBody = $objTable->getDBMainTableBody();

				if( $boolExecute===true ){
					$aryRetBody = $objColumn->getAddSelectTagQuery($objTable, $strTableBody, $strWhereAddBody);
					if( $aryRetBody[1]!==null ){
						$boolExecute = false;
						$intErrorType = $aryRetBody[1];
					}else{
						$varDataForFetch = $aryRetBody[0];
						$arrayBindElement = $aryRetBody[4];
					}
				}

				if( $boolExecute===true ){
					if( is_string($varDataForFetch)===true ){
						//----文字列だったら、一律SQL文とみなして処理
						$strSqlBody1 = $varDataForFetch;

						$retArray = singleSQLExecuteAgent($strSqlBody1, $arrayBindElement, $strFxName);
						if( $retArray[0]!== true ){
							$boolExecute = false;
							$intErrorType = 500;
						}else{
							$objQuery =& $retArray[1];
						}
						//文字列だったら、一律SQL文とみなして処理----
					}else{
						//----処理を停止して、空の配列を返す
						if( is_a($varDataForFetch,"SelectedQueryFaker")===true ){
							$objQuery = $varDataForFetch;
						}else{
							$boolExecute = false;
							$retBool = true;
							$aryDataSet = array();
						}
						//処理を停止して、空の配列を返す----
					}
					
					//異常こそなかったが、SQLの長さが0だった場合----
				}

				if( $boolExecute===true ){
					$arraySelect = array();
					$dlcCounter1 = 0;
					$chkobj = null; // RBAC対応
					$aryDataSetUnique = array();
					$referenceFlag = (strpos($objColumn->getID(),'_CLONE_')) ? true : false;
					if( $selectPrintType===1 ){
						//----DB内値と表示値が食い違う場合
						
						//----最終更新日時
						while ( $row = $objQuery->resultFetch() ){
							// ----dispRestrictValue対応
							$aryDispRestrictValue = $objTable->getDispRestrictValue();
							if($aryDispRestrictValue != null){
								$matchFlg = false;
								foreach($aryDispRestrictValue as $columnName => $aryValue){
									if(array_key_exists($columnName, $row)){
										foreach($aryValue as $value){
											//対象のカラムのデータと$aryValueに格納された値が一致した場合は処理を続行
											if($value == "" || $value == null || $value == "null" || $value == "NULL"){
												if($row[$columnName] == "" || $row[$columnName] == null || $row[$columnName] == "null" || $row[$columnName] == "NULL"){
													$matchFlg = true;
												}
											}else{
												if($row[$columnName] == $value){
													$matchFlg = true;
												}
											}
										}

										//一致する値が無い場合は、処理をスキップ
										if($matchFlg == false){
											continue 2;
										}
									}
								}
							}
							// dispRestrictValue対応----

							// ---- RBAC対応
			                                // ---- 対象レコードのACCESS_AUTHカラムでアクセス権を判定
							list($ret,$permission) = chkTargetRecodeMultiPermission($objTable->getAccessAuth(),$chkobj,$row);
							if($ret === false) {
								$retBool = false;
								$intErrorType = 501;
								$message = sprintf("[%s:%s]chkTargetRecodeMultiPermission is failed.",basename(__FILE__),__LINE__);
								web_log($message);
								throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
							}
							if($permission === false) {
								// アクセス権がないので処理対象から外す
								continue;
							}
			                                // 対象レコードのACCESS_AUTHカラムでアクセス権を判定 ----
							//  RBAC対応 ----

							// ----ここから結果データ作成
							$dlcCounter1 += 1;
							

							$tempValue1 = $row['KEY_COLUMN'];
							
							if(0 < strlen($tempValue1)){
								
								$valueHtmlSpeChr = addSelectTagStringEscape($tempValue1,"64FromOrg");
								
								$valueDispBody = $row['DISP_COLUMN'];
								
								//----date型の型変換
								if("IDColumn" === get_class($objColumn) && $objColumn->getDateFormat() !== null){
									$valueDispBody = date($objColumn->getDateFormat(), strtotime($valueDispBody));
								}
								if("LinkIDColumn" === get_class($objColumn) && $objColumn->getDateFormat() !== null){
									$valueDispBody = date($objColumn->getDateFormat(), strtotime($valueDispBody));
								}
								//date型の型変換----

								if($referenceFlag == true){
									$aryDataSet[] = array('KEY_COLUMN'=>$valueHtmlSpeChr,'DISP_COLUMN'=>$valueDispBody, 'TEMP_KEY_ID'=>$tempValue1);
									$aryDataSetUnique[$valueHtmlSpeChr] = array('KEY_COLUMN'=>$valueHtmlSpeChr,'DISP_COLUMN'=>$valueDispBody, 'TEMP_KEY_ID'=>$tempValue1);
								}else{
									$aryDataSet[] = array('KEY_COLUMN'=>$valueHtmlSpeChr,'DISP_COLUMN'=>$valueDispBody);
								}
							}
							// ここまで結果データ作成----
						}
						// RBAC対応 ----
						
						//最終更新日時----

						//親カラムのID（KEY_COLUMN)は違うが、値（DISP_COLUMN）が一致している対象をチェックし、表示名をDISP_COLUMN(ID)とする。
						if($referenceFlag == true && !empty($aryDataSet)){
							$aryTempDataSet = array();
							foreach($aryDataSet as $data){
								$newDispColumn = $data['DISP_COLUMN'];
								foreach($aryDataSetUnique as $data2){
									if($data['KEY_COLUMN'] != $data2['KEY_COLUMN'] && $data['DISP_COLUMN'] == $data2['DISP_COLUMN']){
										$newDispColumn = $data['DISP_COLUMN']."(".$data['TEMP_KEY_ID'].")";
										break;
									}
								}
								$aryTempDataSet[] = array('KEY_COLUMN'=>$data['KEY_COLUMN'],'DISP_COLUMN'=>$newDispColumn);
							}
							$aryDataSet = $aryTempDataSet;
						}

						$retBool = true;
						//DB内値と表示値が食い違う場合----
					}
					else if( $selectPrintType===0 ){
						//----その他一般[IDcolumnを想定しない。TextColumnが基本的な処理対象]
						//
						while ( $row = $objQuery->resultFetch() ){
							// ----dispRestrictValue対応
							$aryDispRestrictValue = $objTable->getDispRestrictValue();
							if($aryDispRestrictValue != null){
								$matchFlg = false;
								foreach($aryDispRestrictValue as $columnName => $aryValue){
									if(array_key_exists($columnName, $row)){
										foreach($aryValue as $value){
											//対象のカラムのデータと$aryValueに格納された値が一致した場合は処理を続行
											if($value == "" || $value == null || $value == "null" || $value == "NULL"){
												if($row[$columnName] == "" || $row[$columnName] == null || $row[$columnName] == "null" || $row[$columnName] == "NULL"){
													$matchFlg = true;
												}
											}else{
												if($row[$columnName] == $value){
													$matchFlg = true;
												}
											}
										}

										//一致する値が無い場合は、処理をスキップ
										if($matchFlg == false){
											continue 2;
										}
									}
								}
							}
							// dispRestrictValue対応----

							// ---- RBAC対応
			                                // ---- 対象レコードのACCESS_AUTHカラムでアクセス権を判定
							list($ret,$permission) = chkTargetRecodePermission($objTable->getAccessAuth(),$chkobj,$row);
							if($ret === false) {
								$retBool = false;
								$intErrorType = 501;
								$message = sprintf("[%s:%s]chkTargetRecodePermission is faile",basename(__FILE__),__LINE__);
								web_log($message);
								throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
							}
							if($permission === false) {
								// アクセス権がないので処理対象から外す
								continue;
							}
			                                // 対象レコードのACCESS_AUTHカラムでアクセス権を判定 ----
							//  RBAC対応 ----

							// ----ここから結果データ作成
							
							$dlcCounter1 += 1;
							
							// ---- RBAC対応
                                                        if($AccessAuthColumn_flag === true) {
                                                            // ---- アクセス権カラムの場合にロールIDからRole名称に変更
                                                            // 廃止されているロールはID変換失敗で表示
                                                            global $g;
                                                            $obj = new RoleBasedAccessControl($g['objDBCA']); 
                                                            $RoleNameString = $obj->getRoleIDStringToRoleNameString($g['login_id'],$row['KEY_COLUMN'],true);  // 廃止を含む
                                                            unset($obj);
                                                            if($RoleNameString === false) {
								$retBool = false;
								$intErrorType = 501;
								$message = sprintf("[%s:%s]getRoleIDStringToRoleNameString is failed.",basename(__FILE__),__LINE__);
								web_log($message);
								throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                                            }
                                                            $row['KEY_COLUMN'] = $RoleNameString;
                                                            // アクセス権カラムの場合にロールIDからRole名称に変更 ----
                                                        }
							// RBAC対応 ----

							$tempValue1 = $row['KEY_COLUMN'];
							
							if(0 < strlen($tempValue1)){
								
								$valueHtmlSpeChr = addSelectTagStringEscape($tempValue1,"64FromOrg");
								$valueDispBody = $tempValue1;
								
								$aryDataSet[] = array('KEY_COLUMN'=>$valueHtmlSpeChr,'DISP_COLUMN'=>$valueDispBody);
							}
							// ここまで結果データ作成----
						}
						
						$retBool = true;
						//その他一般[IDcolumnを想定しない。TextColumnが基本的な処理対象]----
					}
					unset($objQuery);
				}
			}else{
				$retBool = false;
				$intErrorType = 501;
				$strFADMaxWidthOfSelectTag = null;
				$strFADClassOfSelectTag = "";
			}
			$retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet,$strFADMaxWidthOfSelectTag,$strFADClassOfSelectTag);
		}
		return $retArray;
	}
	//NEW[35]
	function setErrMsgHeadAgent($strValue){
		$this->strErrMsgHead = $strValue;
	}
	//NEW[36]
	function getErrMsgHead(){
		$retValue = $this->strErrMsgHead;
		if( strlen($retValue)==0 ){
			if( is_a($this->objColumn, "Column")===true ){
				//----カラムがセットされている場合
				if( method_exists($this->objColumn,'getErrMsgHead')===true ){
					$retValue = $this->objColumn->getErrMsgHead();
				}
			}
		}
		return $retValue;
	}
	//NEW[37]
	function setErrMsgTailAgent($strValue){
		$this->strErrMsgTail = $strValue;
	}
	//NEW[38]
	function getErrMsgTail(){
		$retValue = $this->strErrMsgTail;
		if( strlen($retValue)==0 ){
			if( is_a($this->objColumn, "Column")===true ){
				//----カラムがセットされている場合
				if( method_exists($this->objColumn,'getErrMsgTail')===true ){
					$retValue = $this->objColumn->getErrMsgTail();
				}
			}
		}
		return $retValue;
	}
	
	//ここまで新規メソッドの定義宣言処理----

}

class VariantOutputType extends OutputType {
	protected $objFunctionForGetHeadTag;
	protected $objFunctionForGetBodyTag;
	//protected $objFunctionForGetSelectListOnLate;

	//----ここから継承メソッドの上書き処理

	function __construct($head, $body, $visible = true){
		parent::__construct($head, $body, $visible);
		$this->objFunctionForGetHeadTag = null;
		$this->objFunctionForGetBodyTag = null;
	}

	function getHeadTag($colNo, $attr=""){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetHeadTag) ){
			$objFunction = $this->objFunctionForGetHeadTag;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $colNo, $attr, $objColumn);
		}else{
			$strSetValue = parent::getHeadTag($colNo, $attr);
		}
		return $strSetValue;
	}

	function getBodyTag($rowData,$aryVariant){
		$strSetValue = "";
		if( is_callable($this->objFunctionForGetBodyTag) ){
			$objFunction = $this->objFunctionForGetBodyTag;
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$strSetValue = $objFunction($this, $rowData, $aryVariant, $objColumn);
		}else{
			$strSetValue = parent::getBodyTag($rowData,$aryVariant);
		}
		return $strSetValue;
	}

        // 表示ファイルのプルダウンに表示するデータを取得
	// VariantOutputType class
	function getFADSelectList(&$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){
		$objFunction = $this->getFunctionForGetFADSelectList();
		if( is_callable($objFunction) ){
			$objColumn = is_a($this->objColumn, "Column")?$this->objColumn:null;
			$retArray = $objFunction($this, $aryVariant, $arySetting, $aryOverride, $objColumn);
		}else{
			$retArray = parent::getFADSelectList($aryVariant, $arySetting, $aryOverride);
		}
		return $retArray;
	}

	//ここまで継承メソッドの上書き処理----

	//----ここから新規メソッドの定義宣言処理

	//NEW[1]
	function setFunctionForGetHeadTag($objFunctionForGetHeadTag){
		$this->objFunctionForGetHeadTag = $objFunctionForGetHeadTag;
	}
	//NEW[2]
	function getFunctionForGetHeadTag(){
		return $this->objFunctionForGetHeadTag;
	}
	//NEW[3]
	function setFunctionForGetBodyTag($objFunctionForGetBodyTag){
		$this->objFunctionForGetBodyTag = $objFunctionForGetBodyTag;
	}
	//NEW[4]
	function getFunctionForGetBodyTag(){
		return $this->objFunctionForGetBodyTag;
	}
	//（クラス移動）
	
	//ここまで新規メソッドの定義宣言処理----

}

class TraceOutputType extends OutputType {
	protected $aryTraceQuery;
	protected $strFirstSearchValueOwnerColumnId;

	//----ここから継承メソッドの上書き処理

	function __construct($head, $body, $visible = true){
		parent::__construct($head, $body, $visible);
		$this->aryTraceQuery = array();
		$this->setFirstSearchValueOwnerColumnID(null);
	}

	function getBodyTag($rowData,$aryVariant){
		global $g;
		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$boolAbort = false;
		$boolNullSkip = false;
		$objTable = $this->objColumn->getTable();
		$strInitedColId = $this->objColumn->getID();
		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
		$aryConvValue['value'] = "";

		if($this->getFirstSearchValueOwnerColumnID() === null){
			$strFirstSearchKeySetColId = $this->objColumn->getID();
		}else{
			$strFirstSearchKeySetColId = $this->getFirstSearchValueOwnerColumnID();
		}

		//----辿り元のテーブルの最終更新日時（秒まで）
		//$mainTimeStampValue  = $rowData[$objTable->getRequiredLastUpdateDateColumnID()];
		//辿り元のテーブルの最終更新日時（秒まで）----
		$mainTimeStampValue  = $rowData[$objTable->getRequiredLastUpdateDateColumnID()];
		if( $mainTimeStampValue=='T_' ){
			//----最終更新日時が存在しないので、中断
			$boolAbort = true;
			//最終更新日時が存在しないので、中断----
		}else{
			$mainTimeStampValue .= ".".substr($rowData[$objTable->getRequiredUpdateDate4UColumnID()],16,6); 

			$intTraceTryCount = 0;
			$strStartValue = "";
			if( array_key_exists($strFirstSearchKeySetColId, $rowData)===true ){
				$strSearchKeyValue = $rowData[$strFirstSearchKeySetColId];
				$strStartValue = $strSearchKeyValue;
                if(is_null($strStartValue)){
                    $boolNullSkip = true;
                }
			}else{
				//----キーが存在しないので、中断
				$boolAbort = true;
				//キーが存在しないので、中断----
			}
		}

		if( $boolAbort === false && $boolNullSkip === false ){
			foreach($this->aryTraceQuery as $arySingleTraceQuery){
				$intTraceTryCount += 1;
				$aryForBind = array(
					$arySingleTraceQuery['TTT_SEARCH_KEY_COLUMN_ID']=>$strSearchKeyValue,
					$arySingleTraceQuery['TTT_TIMESTAMP_COLUMN_ID']=>$mainTimeStampValue
				);
				$varValue = $this->getValueByTracing($arySingleTraceQuery, $aryForBind);
				if( $varValue===null ){
					$boolAbort = true;
				}
				if( array_key_exists($strSearchKeyValue,$varValue)===false ){
					$boolAbort = true;
				}
				if( $boolAbort===true ){
					break;
				}
				$strSearchKeyValue = $varValue[$strSearchKeyValue];
			}
		}
		if( $boolAbort === true){
			//----中断した場合は、変換なし
			$rowData[$strInitedColId] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-13004",array($intTraceTryCount,$this->getErrMsgHead(),$strStartValue,$this->getErrMsgTail()));
			//中断した場合は、変換なし----
		}else{
			//----正常に探しきった場合

            //----date型の型変換
            if("IDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                $strSearchKeyValue = date($this->objColumn->getDateFormat(), strtotime($strSearchKeyValue));
            }
            if("LinkIDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                $strSearchKeyValue = date($this->objColumn->getDateFormat(), strtotime($strSearchKeyValue));
            }
            //date型の型変換----

			$rowData[$strInitedColId] = $strSearchKeyValue;
			//正常に探しきった場合----
		}
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>$aryConvValue);
		return $this->body->getData($rowData,$aryVariant);
	}

	//----ここまで継承メソッドの上書き処理

	//----ここから新規メソッドの定義宣言処理
	//NEW[1]
	function setTraceQuery($aryAllTraceQuery){
		$this->aryTraceQuery = $aryAllTraceQuery;
	}
	//NEW[2]
	function addTraceQuery($aryAddTraceQuery){
		$this->aryTraceQuery[] = $aryAddTraceQuery;
	}
	//NEW[3]
	function setFirstSearchValueOwnerColumnID($strColumnId){
		$this->strFirstSearchValueOwnerColumnId = $strColumnId;
	}
	//NEW[4]
	function getFirstSearchValueOwnerColumnID(){
		return $this->strFirstSearchValueOwnerColumnId;
	}
	//NEW[5]
	function getValueByTracing($arySingleTraceQuery,$aryForBind){
	    global $g;
	    $intControlDebugLevel01=25;
	    $strFxName = __FUNCTION__;

	    $data = array();

	    // RBAC対応 ----
	    $RBAC_obj = new RoleBasedAccessControl($g['objDBCA']);
	    $ret = $RBAC_obj->getAccountInfo($g['login_id']);
	    if($ret === false) {
			throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
	    }
	    // ---- RBAC対応

	    $query = generateSelectSQLForTrace($arySingleTraceQuery,$RBAC_obj);
	    $retArray = singleSQLExecuteAgent($query, $aryForBind, $strFxName);
	    if( $retArray[0] === true ){
	        $objQuery =& $retArray[1];
	        while( $row = $objQuery->resultFetch() ){
	            // RBAC対応 ----
	            //  対象レコードのACCESS_AUTHカラムでアクセス権を判定
	            list($ret,$permission) = $RBAC_obj->chkOneRecodeMultiAccessPermission($row);
	            if($ret === false) {
	                $intErrorType = 501;
	                throw new Exception( '00000101-([CLASS]' . __CLASS__ . ',[FUNCTION]' . __FUNCTION__ . ')' );
	            }
	            if($permission === true) {
	                $data[$row['C1']] = $row['C2'];
	            }
	            // ---- RBAC対応
	        }
	        unset($objQuery);
	    }
	    else{
	        $data = null;
	    }
	    return $data;
	}

	//ここまで新規メソッドの定義宣言処理----
}

class IDOutputType extends OutputType {

	//----ここから継承メソッドの上書き処理

	//----htmlタグ取得用
	function getBodyTag($rowData,$aryVariant){
		global $g;
		//----$rowData「NULLまたは連想配列を想定」
		//----columnクラスのgetOutputBody関数から呼ばれる
		//----自身クラスの変数（$this->body）のメソッドgetData($rowData)を実行する;
		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$boolOutReferfence = false;
		$strInitedColId = $this->objColumn->getID();
		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array();
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = null;

		$aryConvValue['value'] = "";
		$aryConvValue['rawValue'] = ""; //$aryVariant['callerVars']['free']['rawValue']で取得可能
		$aryConvValue['convIDList'] = null;

		list($mainIdColVal,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strInitedColId),"");
		if( $tmpBoolKeyExist===true ){
			
			$noConv=false;
			
			if( $this->checkListFormatterMode("JournalTableFormatter") === true ){
				//----マスターの履歴テーブルが指定されている。
				if( $this->objColumn->getJournalTableOfMaster() !== null ){
					//----参照するマスターの履歴テーブル名が指定されている
					$boolOutReferfence = true;
					//参照するマスターの履歴テーブル名が指定されている----
				}
				//マスターの履歴テーブルが指定されている。----
			}
			
			$aryConvValue['rawValue'] = $mainIdColVal;
			
			if( $boolOutReferfence===false ){
				$intMasterKeyColumnType=$this->objColumn->getMasterKeyColumnType();
				if( $intMasterKeyColumnType == 0 ){
					//----メインテーブルのカラムが、数値型(デフォルト指定=0)の場合
					if( strlen($mainIdColVal)==0 ){
						//----メインテーブルのカラム値がNULLの場合は、ここを通る
						$noConv=true;
						//メインテーブルのカラム値がNULLの場合は、ここを通る----
					}else{
						//----メインテーブルのIDカラム型への保存値が、整数値と評価できる値の場合は、[数値型]へ置き換える
						if(preg_match('/^(-[1-9])?[0-9]*$/s', $mainIdColVal)===1){
							$mainIdColVal=intval($mainIdColVal);
						}
						//メインテーブルのIDカラム型への保存値が、整数値と評価できる値の場合は、[数値型]へ置き換える----
					}
					//メインテーブルのカラムが、数値型(デフォルト指定=0)の場合----
				}else{
					//----メインテーブルのカラムが、数値型(デフォルト指定=0)ではない場合
					//メインテーブルのカラムが、数値型(デフォルト指定=0)ではない場合----
				}
				
				if( $noConv===true ){
					$rowData[$strInitedColId] = "";
				}else{
					$utnMasterTable = $this->objColumn->getMasterTableArrayForFilter();
					if($utnMasterTable===null){
						$rowData[$strInitedColId] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-13001",array($this->getErrMsgHead(),$mainIdColVal,$this->getErrMsgTail()));
					}else if( array_key_exists( $mainIdColVal, $utnMasterTable)===true ){

                        //----date型の型変換
                        $arrayTmp = array();
                        if("IDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                            foreach($utnMasterTable as $key => $value){
                                $arrayTmp[$key] = date($this->objColumn->getDateFormat(), strtotime($value));
                            }
                            $utnMasterTable = $arrayTmp;
                        }
                        if("LinkIDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                            foreach($utnMasterTable as $key => $value){
                                $arrayTmp[$key] = date($this->objColumn->getDateFormat(), strtotime($value));
                            }
                            $utnMasterTable = $arrayTmp;
                        }
                        //date型の型変換----

						$rowData[$strInitedColId] = $utnMasterTable[$mainIdColVal];
						$aryConvValue['convIDList'] = $utnMasterTable;
					}else if( 0 < strlen($mainIdColVal) ){
						//----テーブルに値が入っている場合
						$rowData[$strInitedColId] = "{$this->getErrMsgHead()}({$mainIdColVal}){$this->getErrMsgTail()}";
						//テーブルに値が入っている場合
						//
						$retVal="";
						$retBool=$this->objColumn->etceteraParameterRead("ID-match_errmsg-1", $retVal);
						if($retBool===false){
							//----変換失敗一回目のみ処理
							$arrayKeys=array_keys($utnMasterTable);
							$msgBody="";
							if( array_key_exists(0,$arrayKeys)===true ){
								$strKeyType=gettype($arrayKeys[0]);
							}else{
								$strKeyType="unknown";
							}
							for($fnv1=0;$fnv1<count($arrayKeys);$fnv1++){
								$msgBody.="[".$arrayKeys[$fnv1]."]";
							}
							$objTable = $this->objColumn->getTable();
							$strTableBody = $objTable->getDBMainTableBody();
							$msgSetBody = "Column(".$strInitedColId.") of table(".$strTableBody.") can't show another value, if it has't these value, which is in (".$msgBody.")(type:".$strKeyType.").";
							$this->objColumn->etceteraParameterWrite("ID-match_errmsg-1", $msgSetBody);
							//変換失敗一回目のみ処理----
						}
					}
				}
				//
			}else{
				if( strlen($mainIdColVal)==0 ){
					$noConv = true;
				}
				
				if( $noConv===true ){
					$rowData[$strInitedColId] = "";
				}else{
					$objTable = $this->objColumn->getTable();
					
					$mainTimeStampValue = $rowData[$objTable->getRequiredLastUpdateDateColumnID()];
					if( $mainTimeStampValue=='T_' ){
						$jnlMasterTable = null;
					}else{
						$mainTimeStampValue .= ".".substr($rowData[$objTable->getRequiredUpdateDate4UColumnID()],16,6);
						$jnlMasterTable = getDispValueFromJournalOfMasterTable($objTable, $this->objColumn, $mainIdColVal, $mainTimeStampValue);
					}
					
					if( $jnlMasterTable===null ){
						$rowData[$strInitedColId] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-13002",array($this->getErrMsgHead(),$mainIdColVal,$this->getErrMsgTail()));
					}else if( array_key_exists($mainIdColVal, $jnlMasterTable)===true ){

                        //----date型の型変換
                        $arrayTmp = array();
                        if("IDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                            foreach($jnlMasterTable as $key => $value){
                                $arrayTmp[$key] = date($this->objColumn->getDateFormat(), strtotime($value));
                            }
                            $jnlMasterTable = $arrayTmp;
                        }
                        if("LinkIDColumn" === get_class($this->objColumn) && $this->objColumn->getDateFormat() !== null){
                            foreach($jnlMasterTable as $key => $value){
                                $arrayTmp[$key] = date($this->objColumn->getDateFormat(), strtotime($value));
                            }
                            $jnlMasterTable = $arrayTmp;
                        }
                        //date型の型変換----

						$rowData[$strInitedColId] = $jnlMasterTable[$mainIdColVal];
						$aryConvValue['convIDList'] = $jnlMasterTable;
					}else{
						$rowData[$strInitedColId] = "{$this->getErrMsgHead()}({$mainIdColVal}){$this->getErrMsgTail()}";
					}
				}
			}
		}
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		//----親クラスの変数（$this->body）のメソッドgetData($rowData)を実行する;
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = $aryConvValue;
		return $this->body->getData($rowData,$aryVariant);
	}
	//htmlタグ取得用(2014-12-01名前にTagを追加)----

	//----ここまで継承メソッドの上書き処理

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class AUUOutputType extends OutputType {

	//----ここから継承メソッドの上書き処理

	function getBodyTag($rowData,$aryVariant){
		//----$rowData「NULLまたは連想配列を想定」
		global $g;
		$intControlDebugLevel01=50;
		$strFxName = __FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$boolOutReferfence = false;
		$strInitedColId = $this->objColumn->getID();

		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array();
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = null;

		$aryConvValue['value'] = "";

		list($strRawVal,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strInitedColId),"");
		if( $tmpBoolKeyExist===true ){
			if( $this->checkListFormatterMode("JournalTableFormatter") === true ){
				//----マスターの履歴テーブルが指定されている。
				if( $this->objColumn->getJournalTableOfMaster() !== null ){
					//----参照するマスターの履歴テーブル名が指定されている
					$boolOutReferfence = true;
					//参照するマスターの履歴テーブル名が指定されている----
				}
				//マスターの履歴テーブルが指定されている。----
			}
			
			$strConvertedColumnId = $this->objColumn->getAliasIdPrefix().$this->objColumn->getAutoUpdateUserNo();
			if( $boolOutReferfence===false ){
				//----履歴モードではない場合は、置き換え済のカラムデータへ置き換え
				list($dataConverted,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strConvertedColumnId),"");
				if( $tmpBoolKeyExist===true ){
					$rowData[$strInitedColId] = $dataConverted;
				}
				//履歴モードではない場合は、置き換え済のカラムデータへ置き換え----
			}else{
				//----履歴モードの場合
				list($strConvertedVal,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strConvertedColumnId),"");
				if( $tmpBoolKeyExist===true ){
					$objTable = $this->objColumn->getTable();
					
					$rowData[$strInitedColId] = "";
					if( 0 < strlen($strRawVal) ){
						$mainTimeStampValue = $rowData[$objTable->getRequiredLastUpdateDateColumnID()];
						if( $mainTimeStampValue=='T_' ){
							$jnlMasterTable = null;
						}else{
							$mainTimeStampValue .= ".".substr($rowData[$objTable->getRequiredUpdateDate4UColumnID()],16,6);
							$jnlMasterTable = getDispValueFromJournalOfMasterTable($objTable, $this->objColumn, $strRawVal, $mainTimeStampValue);
						}
						if( $jnlMasterTable===null ){
							$rowData[$strInitedColId] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-13003",array($this->getErrMsgHead(),$strConvertedVal,$this->getErrMsgTail()));
						}else if( array_key_exists($strRawVal, $jnlMasterTable)===true ){
							$rowData[$strInitedColId] = $jnlMasterTable[$strRawVal];
						}else{
							$rowData[$strInitedColId] = "{$this->getErrMsgHead()}({$strConvertedVal}){$this->getErrMsgTail()}";
						}
					}
				}else{
					$rowData[$strInitedColId] = "";
				}
				//履歴モードの場合----
			}
		}
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = $aryConvValue;
		return $this->body->getData($rowData,$aryVariant);
	}

	//----ここまで継承メソッドの上書き処理

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}

class FileLinkOutputType extends OutputType {

	//----ここから継承メソッドの上書き処理

	function getBodyTag($rowData,$aryVariant){
		//----$rowData「NULLまたは連想配列を想定」
		global $g;
		$intControlDebugLevel01=250;
		$strFxName = __CLASS__."::".__FUNCTION__;
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		//
		$strInitedColId = $this->objColumn->getID();
		
		$aryVariant['callerClass'] = get_class($this);
		$aryVariant['callerVars'] = array();
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = null;

		$aryConvValue['url'] = "";
		$aryConvValue['innerHtml'] = "";

		$lcRequiredUpdateDate4UColumnId = $this->objColumn->getTable()->getRequiredUpdateDate4UColumnID(); //"UPD_UPDATE_TIMESTAMP"
		list($fileNameOfData,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array($strInitedColId),"");

		if( $this->checkListFormatterMode("JournalTableFormatter") === true ){
			//----ここから履歴の場合の例外的処理
			
			$refFormartter = $this->objColumn->getFormatterRef();
			$boolRefKeyExists = false;
			
			$arrayPrev = $refFormartter->getGeneValue($strInitedColId, $boolRefKeyExists);
			if( $boolRefKeyExists === false ){
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				$arrayPrev = array("", "");
				$aryConvValue['url'] = "";
				$aryConvValue['innerHtml'] = $fileNameOfData;
			}
			
			if( 1 <= strlen($fileNameOfData) ){
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				
				$strCheckFilePath = $this->objColumn->getLAPathToFUCItemPerRow($rowData);
				$boolFileExists = file_exists($strCheckFilePath);
				
				if( $boolFileExists === true ){
					$url = $this->objColumn->getOAPathToFUCItemPerRow($rowData);
					$refFormartter->setGeneValue($strInitedColId, array($fileNameOfData, $url));
					
					$aryConvValue['url'] = $url;
					$aryConvValue['innerHtml'] = $fileNameOfData;
				}else{
					
					if( $arrayPrev[0] == $fileNameOfData ){
						$aryConvValue['url'] = $arrayPrev[1];
						$aryConvValue['innerHtml'] = $fileNameOfData;
					}else{
						$refFormartter->setGeneValue($strInitedColId, array("", ""));
						$aryConvValue['url'] = "";
						$aryConvValue['innerHtml'] = $fileNameOfData;
					}
					
				}
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
			}else{
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				$refFormartter->setGeneValue($strInitedColId, array("", ""));
				
				$aryConvValue['url'] = "";
				$aryConvValue['innerHtml'] = $fileNameOfData;
				
			}
			//ここまで履歴の場合の例外的処理----
		}else{
			if( 1 <= strlen($fileNameOfData) ){
				//----ファイル名が入力され、文字列の長さが1以上
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				$strCheckFilePath = $this->objColumn->getLAPathToFUCItemPerRow($rowData);
				
				$boolFileExists = file_exists($strCheckFilePath);
				
				if($boolFileExists===true){
					//----ファイルが存在している
					dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
					$url = $this->objColumn->getOAPathToFUCItemPerRow($rowData);
					//$strTagInnerBody = "<a href=\"{$url}\" target=\"_blank\">{$escapedData}</a>";
					$aryConvValue['url'] = $url;
					$aryConvValue['innerHtml'] = $fileNameOfData;
					//ファイルが存在している----
				}else{
					dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
					$aryConvValue['url'] = "";
					$aryConvValue['innerHtml'] = $fileNameOfData;
				}
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				//ファイル名が入力され、文字列の長さが1以上----
			}else{
				$aryConvValue['url'] = "";
				$aryConvValue['innerHtml'] = "";
			}
		}
		dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		$aryVariant['callerVars']['initedColumnID'] = $strInitedColId;
		$aryVariant['callerVars']['free'] = $aryConvValue;
		$FileEncryptFunctionName = $this->objColumn->getFileEncryptFunctionName();

		if($FileEncryptFunctionName == "ky_file_encrypt"){
			return $this->body->getData($rowData,$aryVariant,1);
		}else{
			return $this->body->getData($rowData,$aryVariant);
		}
	}

	//----ここまで継承メソッドの上書き処理

	//----ここから新規メソッドの定義宣言処理
	//ここまで新規メソッドの定義宣言処理----

}
?>
