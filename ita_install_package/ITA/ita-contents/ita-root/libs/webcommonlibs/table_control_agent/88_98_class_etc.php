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
class SelectedQueryFaker {
	protected $boolFetch;
	protected $intEffectedRow;

	protected $intRowLength;
	protected $intFocusIndex;

	protected $aryRowFaker;

	function __construct($arySourseRowFaker,$boolSafeSkip=false){
		$this->boolFetch	  = false;
		$this->intEffectedRow = null;

		$this->aryRowFaker = array();

		if( is_array($arySourseRowFaker)===false ){
			$arySourseRowFaker = array();
		}

		if( $boolSafeSkip!==true ){
			//----インデックスが数値型0からの配列に変更
			foreach($arySourseRowFaker as $rowFaker){
				$this->aryRowFaker[] = $rowFaker;
			}
			//インデックスが数値型0からの配列に変更----
		}else{
			$this->aryRowFaker	  = $arySourseRowFaker;
		}

		$this->intRowLength   = count($this->aryRowFaker);
		$this->intFocusIndex  = 0;
	}
	
	public function resultFetch(){
		$resultRow = false;
		$this->boolFetch = true;

		if( $this->intFocusIndex + 1 <= $this->intRowLength ){
			$resultRow = $this->aryRowFaker[$this->intFocusIndex];
			$this->intFocusIndex += 1;
		}

		if( $resultRow!==false ){
			$this->intEffectedRow += 1;
		}
		return $resultRow;
	}

	public function effectedRowCount(){
		$retInt = $this->intEffectedRow;
		if( $retInt===null ){
			$retInt = 0;
		}
		return $retInt;
	}

}
//----チケット953----

class JsEvent {
	/* JsEvent : TabBFmtでevent発生時に実行するjavascriptを管理する。
		イベント名とfunction名とその引数を保持
		引数は1文字目が":"であればDBのColumnと判断し、RowData->getRowData()[:ColumnName]の値を文字列として表示する
		:から始まらない文字列はjavascriptの変数として扱うため、文字列としたい場合はシングルクォートで囲う必要がある。
		ex)  new JsEvent("onClick", "func", array( "a", "'b'", ':COLUMN'));
		  aは変数、bは文字列, :COLUMNはDBのCOLUMN列の結果を文字列化したもの
	*/

	protected $strJsEventName; // as string イベント名。onClick, onChangeなど
	protected $strCallTgtJsFxName; // as string javascriptの関数名
	protected $functionArgs; // as array of string（関数に渡す引数）

	function __construct($strJsEventName, $strCallTgtJsFxName, $functionArgs=array()){
		$this->strJsEventName = $strJsEventName;
		$this->strCallTgtJsFxName = $strCallTgtJsFxName;
		$this->functionArgs = $functionArgs;
	}

	function getEventName(){
		return $this->strJsEventName;
	}

	function getJsAttr($rowData, $strCallTgtJsFxNamePrefix="",$strOverrideJsEventName=""){
		$catArgs = "";
		$fArgs = $this->functionArgs;
		for($i = 0; $i < count($fArgs); ++$i){
			$tmpData = $fArgs[$i];
			if( 0 < strlen($tmpData) ){
				//----文字列として1バイト以上の場合
				if( $tmpData === 'this' ){
					$fArgs[$i] = 'this';
				}else if( mb_substr($tmpData,0,1,"UTF-8") === ":" ){
					//----
					$strCheckKey = mb_substr($tmpData, 1, NULL, 'UTF-8');
					if(array_key_exists($strCheckKey,$rowData)){
						//----add start 2018/09/21 JavaScriptに値を渡す際にbase64エンコードする処理を追加
						if( is_numeric($rowData[$strCheckKey]) ){
							$fArgs[$i] = '\''.$rowData[$strCheckKey].'\'';
						}else{
							$fArgs[$i] = '\''.base64_encode($rowData[$strCheckKey]).'\'';
						}
						//add end 2018/09/21 JavaScriptに値を渡す際にbase64エンコードする処理を追加----
					}else{
						$fArgs[$i] = '\''.$tmpData.'\'';
					}
				}
			}
			$catArgs .= $fArgs[$i];
			if( $i < count($fArgs) -1 ){
				$catArgs .= ", ";
			}
		}
		if( $strOverrideJsEventName=="" ){
			$strJsEventName = $this->strJsEventName;
		}else{
			$strJsEventName = $strOverrideJsEventName;
		}
		$str = "{$strJsEventName}=\"{$strCallTgtJsFxNamePrefix}{$this->strCallTgtJsFxName}(". $catArgs   .")\"";
		return $str;
	}

}

class commonWrapEvent {

	protected $objTable;
	protected $objDummy;

	function __construct($objTable){
		$this->objTable = $objTable;
		$this->objDummy = new commonDummyEvent();
	}

	//----tableクラスの関数(commonEventHandlerExecute)から呼ばれる。
	function eventExecute(&$refArrayVariant=array()){
		//----引数（$refArrayVariant['caller']・・・Tableクラスの関数setGeneObjectで格納済した配列キー）
		$refKeyExists = false;
		$objEventer = $this->objTable->getGeneObject($refArrayVariant['caller'], $refKeyExists);
		if( $refKeyExists === false ){
			$objEventer = $this->objDummy;
		}
		$objEventer->setTableRef($this->objTable);
		$objEventer->eventExecute($refArrayVariant);
	}
	//tableクラスの関数(commonEventHandlerExecute)から呼ばれる。-----

}

class commonDummyEvent {

	protected $objTable;

	function __construct(){
	}

	function setTableRef($objTable){
		$this->objTable = $objTable;
	}

	function eventExecute(&$refArrayVariant=array()){
		return true;
	}

}

class MailSendEventStandard {

    protected $objTable;                        //Tableクラス参照を保持する変数

    protected $confTempCheckDirNS;
    protected $confSysmailTmplPreFix;

    protected $confTempMailBodyMakeDirNS;
    protected $confSysmailQueueDirNS;

    protected $mailAddressFromWhenUserNoMA;

    protected $mailAddressTo;                   //メール送信先(TO)
    protected $mailAddressCc;                   //メール送信先(CC)

    protected $devModeMailAddressTo;            //----開発用
    protected $devModeMailAddressCc;            //----開発用

    protected $strSbjectPrefix;
    protected $strSubjectPostfix;

    protected $arrayReportColumnName;

    protected $strGuideUrl;

    protected $strWebPageTileName;

    protected $subEventName;

    protected $mailSendExecuteMode;

    protected $strLoginUserMailTable;
    protected $strLoginUserSelfColumn;
    protected $strLoginUserMailColumn;
    protected $strLoginUserNameColAsSender;
    protected $strLoginUserDisuseFlagColumn;

    protected $aryListOfSendActionName;

    protected $boolFreeFromMode;
    protected $strSettingFileDirPath;

    function __construct(){
        global $g;
        $this->setSettingFileDirPath("confs");
        
        //----ファイル名を一意にするために、一時DIRを作成するフォルダ
        $this->confTempCheckDirNS = "{$g['root_dir_path']}/temp/event_mail";
        //ファイル名を一意にするために、一時DIRを作成するフォルダ----
        
        //----メール送信依頼ファイルが下書きされるフォルダ
        $this->confTempMailBodyMakeDirNS = "{$g['root_dir_path']}/temp/event_mail";
        //メール送信依頼ファイルが下書きされるフォルダ----
        
        //----SYSMAILSENDERの送信待ちキュー配置フォルダ
        $this->confSysmailQueueDirNS = "{$g['root_dir_path']}/temp/ky_mail_queues/ky_sysmail_0_queue";
        //SYSMAILSENDERの送信待ちキュー配置フォルダ----
        
        $this->confSysmailTmplPreFix = 'sysmail_000_';
        
        $this->setMailAddressFromWhenUserNoMA("noreply@pf-pj.local");
        
        $this->strWebPageTileName = "";
        
        $this->subEventName = "";
        
        $this->setGuideUrl("");
        
        
        $this->setMailAddressTo("");
        $this->setMailAddressCc("");
        
        $this->setMailAddressToWhenDev("");
        $this->setMailAddressCcWhenDev("");
        
        $this->setLoginUserMailTable("A_ACCOUNT_LIST");
        $this->setLoginUserSelfColumn("USER_ID");
        $this->setLoginUserMailColumn("MAIL_ADDRESS");
        $this->setLoginUserNameColumnAsSender("USERNAME_JP");
        $this->setLoginUserDisuseFlagColumn("DISUSE_FLAG");
        
        //----（クラスモジュール開発時用）どんな条件があっても、送信しない場合は、ここで[false]を指定
        $this->mailSendExecuteMode = true; 
        //（クラスモジュール開発時用）どんな条件があっても、送信しない場合は、ここで[false]を指定----
        //
        $this->setFreeFromMode(false);
        
        $this->setListOfSendActionName(null);
    }

    //----設定ファイル配置ディレクトリパスのプロパティ
    public function setSettingFileDirPath($strPathNS){
        $retBool=false;
        if( checkRiskOfDirTraversal($strPathNS)===false ){
            $this->strSettingFileDirPath = getApplicationRootDirPath()."/{$strPathNS}";
        }
        return $retBool;
    }
    public function getSettingFileDirPath(){
        return $this->strSettingFileDirPath;
    }
    //設定ファイル配置ディレクトリパスのプロパティ----

    function getListOfSendActionName(){
        return $this->aryListOfSendActionName;
    }
    
    function setListOfSendActionName($arySendActionName){
        $this->aryListOfSendActionName = $arySendActionName;
    }

    function getFreeFromMode(){
        return $this->boolFreeFromMode;
    }
    
    function setFreeFromMode($boolValue){
        $this->boolFreeFromMode = $boolValue;
    }

    function getTableRef(){
        return $this->objTable;
    }
    
    function setTableRef(&$table){
        $this->objTable = &$table;
    }

    //----ディレクトリ関係のプロパティ
    function setTempCheckDir($strDirNS){
        // ファイル名を一意にするために、一時DIRを作成するフォルダ
        $this->confTempCheckDirNS = $strDirNS;
    }

    function getTempCheckDir(){
        return $this->confTempCheckDirNS;
    }

    function setTempMailBodyMakeDir($strDirNS){
        // メール送信依頼ファイルが下書きされるフォルダ
        $this->confTempMailBodyMakeDirNS = $strDirNS;
    }

    function getTempMailBodyMakeDir(){
        return $this->confTempMailBodyMakeDirNS;
    }

    //----SYSMAILSENDERのQUEファイルのフォルダパス
    function setSysmailQueueDir($strDirNS){
        // SYSMAILSENDERの送信待ちキュー配置フォルダ
        $this->confSysmailQueueDirNS = $strDirNS;
    }

    function getSysmailQueueDir(){
        return $this->confSysmailQueueDirNS;
    }
    //SYSMAILSENDERのQUEファイルのフォルダパス----

    //ディレクトリ関係のプロパティ----

    function getSysmailTmplPreFix(){
        return $this->confSysmailTmplPreFix;
    }

    function setSysmailTmplPreFix($strPrefix){
        $this->confSysmailTmplPreFix = $strPrefix;
    }

    function getMailAddressTo(){
        return $this->mailAddressTo;
    }

    function setMailAddressTo($strToValue){
        $this->mailAddressTo = $strToValue;
    }

    function getMailAddressCc(){
        return $this->mailAddressCc;
    }

    function setMailAddressCc($strCcValue){
        $this->mailAddressCc = $strCcValue;
    }

    //----開発時用のメール送信項目
    function getMailAddressToWhenDev(){
        return $this->devModeMailAddressTo;
    }

    function setMailAddressToWhenDev($strToValue){
        $this->devModeMailAddressTo = $strToValue;
    }

    function getMailAddressCcWhenDev(){
        return $this->devModeMailAddressCc;
    }

    function setMailAddressCcWhenDev($strCcValue){
        $this->devModeMailAddressCc = $strCcValue;
    }
    //開発時用のメール送信項目----

    //----作業者のメールアドレスが、アカウントテーブルから取得できなかった場合の、Fromメールアドレス
    function getMailAddressFromWhenUserNoMA(){
        return $this->mailAddressFromWhenUserNoMA;
    }

    function setMailAddressFromWhenUserNoMA($strFromValue){
        $this->mailAddressFromWhenUserNoMA = $strFromValue;;
    }
    //作業者のメールアドレスが、アカウントテーブルから取得できなかった場合の、Fromメールアドレス----

    //----メールのタイトル等に使われるWebページ名
    function getWebPageTileName(){
        return $this->strWebPageTileName;
    }

    function setWebPageTileName($strValue){
        $this->strWebPageTileName = $strValue;
    }
    //メールのタイトル等に使われるWebページ名----

    function setGuideUrl($strUrl){
        $this->strGuideUrl = $strUrl;
    }

    function getGuideUrl(){
        return $this->strGuideUrl;
    }

    function setReportColumns($arrayColumnIdAndItemName){
        $this->arrayReportColumnName = $arrayColumnIdAndItemName;
    }

    function getLoginUserMailTable(){
        return $this->strLoginUserMailTable;
    }

    function setLoginUserMailTable($strValue){
        $this->strLoginUserMailTable = $strValue;
    }
    
    function getLoginUserSelfColumn(){
        return $this->strLoginUserSelfColumn;
    }

    function setLoginUserSelfColumn($strValue){
        $this->strLoginUserSelfColumn = $strValue;
    }

    function getLoginUserMailColumn(){
        return $this->strLoginUserMailColumn;
    }

    function setLoginUserMailColumn($strValue){
        $this->strLoginUserMailColumn = $strValue;
    }

    function getLoginUserNameColumnAsSender(){
        return $this->strLoginUserNameColAsSender;
    }

    function setLoginUserNameColumnAsSender($strValue){
        $this->strLoginUserNameColAsSender = $strValue;
    }
    
    function getLoginUserDisuseFlagColumn(){
        return $this->strLoginUserDisuseFlagColumn;
    }

    function setLoginUserDisuseFlagColumn($strValue){
        $this->strLoginUserDisuseFlagColumn = $strValue;
    }
    

    function getMailAddressFrom(){
        
        global $g;
        
        $strRetValue = "";
        $p_from_mail = "";
        
        if( $this->getFreeFromMode()===true ){
            //
            $strDbQM = "";
            if( $g['db_model_ch'] == 0 ){
                $strDbQM = "\"";
            }else if( $g['db_model_ch'] == 1 ){
                $strDbQM = "`";
            }
            $strLoginUserMailTable = "{$strDbQM}{$this->getLoginUserMailTable()}{$strDbQM}";
            $strLoginUserSelfColumn = "{$strDbQM}{$this->getLoginUserSelfColumn()}{$strDbQM}";
            $strLoginUserMailColumn = "{$strDbQM}{$this->getLoginUserMailColumn()}{$strDbQM}";
            $strLoginUserDisuseFlagColumn = "{$strDbQM}{$this->getLoginUserDisuseFlagColumn()}{$strDbQM}";
            $strLoginUserNameColumnAsSender = "{$strDbQM}{$this->getLoginUserNameColumnAsSender()}{$strDbQM}";
            
            //----ここから、ログインID(英数字)を使って、メールアドレスを取得する
            $userMailReqSql  = "SELECT {$strLoginUserMailColumn}, {$strLoginUserNameColumnAsSender} ";
            $userMailReqSql .= "FROM {$strLoginUserMailTable} ";
            $userMailReqSql .= "WHERE {$strLoginUserSelfColumn} = :{$this->getLoginUserSelfColumn()}_BV AND {$strLoginUserDisuseFlagColumn} = '0' ";
            
            $objQuery = $g['objDBCA']->sqlPrepare($userMailReqSql);
            
            $arrayElement = array(
                $this->getLoginUserSelfColumn()."_BV"=>$g['login_id']
            );
            
            $objQuery->sqlBind($arrayElement);
            $r = $objQuery->sqlExecute();
            
            $resultRowBody1=array();
            $resultRowCount=0;
            
            while( $focusRow = $objQuery->resultFetch() ){
                $resultRowCount+=1;
                $resultRowBody1[]=$focusRow;
            }
            
            unset($objQuery);
            
            //ここまで、ログインID(英数字)を使って、メールアドレスを取得する----
            
            if($resultRowCount == 1){
                //----ユーザーアカウント・テーブルから正常に行を取得できた
                
                $p_from_mail = $resultRowBody1[0][$this->getLoginUserMailColumn()];
                
                if(preg_match('/^[-_+=\.a-zA-Z0-9]+@[-a-zA-Z0-9\.]+$/', $p_from_mail) === 1){ 
                    //----メールアドレスの様式に合致した
                    $strRetValue =$p_from_mail;
                    //メールアドレスの様式に合致した----
                }else{
                    //----メールアドレスの様式に合致しなかった
                    //
                    if($p_from_mail == ""){
                        //----何も入力されていなかった
                        //何も入力されていなかった----
                    }else{
                        //----メールの様式ではなかった
                        $p_from_mail = "";
                        //メールの様式ではなかった----
                    }
                    //メールアドレスの様式に合致しなかった----
                }
                
            }else{
                //----1行を発見できなかった
                //1行を発見できなかった----
            }
            
            if($p_from_mail == ""){
                
                $p_from_mail = $this->getMailAddressFromWhenUserNoMA();
                
            }
            
        }else{
            $p_from_mail = file_get_contents ( $this->getSettingFileDirPath() . "/commonconfs/app_mail_from.txt" );
        }
        
        if( $strRetValue=="" ){
            if(preg_match('/^[-_+=\.a-zA-Z0-9]+@[-a-zA-Z0-9\.]+$/', $p_from_mail) === 1){
                $strRetValue = $p_from_mail;
            }else{
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-25001"));
            }
        }
        
        return $strRetValue;
        
    }

    function getSubject(&$arrayData){
        
        $strInfoOfUpdateTarget = $this->getUpdateTargetArea($arrayData);
        
        $strValue = $this->getWebPageTileName()."(".$this->subEventName.")".$strInfoOfUpdateTarget;
        
        return $strValue;
    }

    function getUpdateTargetArea(&$arrayData){
        if(array_key_exists($this->objTable->getRIColumnID(),$arrayData)){
            $strRIColId = $this->objTable->getRIColumnID();
            $aryObjColumn = $this->objTable->getColumns();
            $objRIColumn = $aryObjColumn[$strRIColId];
            $strInfoOfUpdateTarget="["."(".$objRIColumn->getColLabel().")".$arrayData[$objRIColumn->getID()]."]";
        }else{
            $strInfoOfUpdateTarget="";
        }
        return $strInfoOfUpdateTarget;
    }

    function eventExecute(&$refArrayVariant=array()){
        //----$refArrayVariant-key1('caller')--('registerTableMain'||'updateTableMain'||'deleteTableMain')
        global $g;

        $boolRetValue = false;

        if(array_key_exists('caller',$refArrayVariant)===true){
            switch($refArrayVariant['caller']){
                case "registerTableMain":
                    //
                    if($refArrayVariant['ordMode']==0){
                        $objListFormatter = $refArrayVariant['objListFormatter'];
                        $strActionName = $objListFormatter->getModeTypeName($refArrayVariant['refSetting']);
                        //
                        $this->table = &$refArrayVariant['objTable'];
                        //
                        $aryActionNameList = $this->getListOfSendActionName();
                        foreach($aryActionNameList as $key=>$strFocusActionName){
                            if($strFocusActionName==$strActionName){
                                $this->mainProcess1($strActionName, $refArrayVariant['refExeRegisterData']);
                                break;
                            }
                        }
                    }
                    
                    break;
                    
                case "updateTableMain":
                    
                    if($refArrayVariant['ordMode']==0){
                        $objListFormatter = $refArrayVariant['objListFormatter'];
                        $strActionName = $objListFormatter->getModeTypeName($refArrayVariant['refSetting']);
                        
                        $this->table = &$refArrayVariant['objTable'];
                        
                        $aryActionNameList = $this->getListOfSendActionName();
                        foreach($aryActionNameList as $key=>$strFocusActionName){
                            if($strFocusActionName==$strActionName){
                                $this->mainProcess1($strActionName, $refArrayVariant['refExeUpdateData']);
                                break;
                            }
                        }
                    }
                    
                    break;
                    
                case "deleteTableMain":
                    
                    if($refArrayVariant['ordMode']==0){
                        $objListFormatter = $refArrayVariant['objListFormatter'];
                        $strActionName = $objListFormatter->getModeTypeName($refArrayVariant['refSetting'],$refArrayVariant['mode']);
                        
                        $this->table = &$refArrayVariant['objTable'];
                        
                        $aryActionNameList = $this->getListOfSendActionName();
                        foreach($aryActionNameList as $key=>$strFocusActionName){
                            if($strFocusActionName==$strActionName){
                                $this->mainProcess1($strActionName, $refArrayVariant['refExeDeleteData']);
                                break;
                            }
                        }
                    }
                    
                    break;
                    
                case "tableIUDByQMFile":
                    
                    $this->mainProcess2($refArrayVariant);
                    
                    break;
                    
                default:
                    
                    $boolRetValue = true;
                    
                    break;
            }
        }else{
            $boolRetValue = true;
        }
        
        return $boolRetValue;
        
    }

    function mainProcess1($strSubModeName, &$arrayData){
        
        $boolExeContinue = true;
        
        $this->subEventName = $strSubModeName;
        
        //----ファイル本文の名前を確保
        $miNewMailQueFileFullname = $this->getNewMailFilename();
        //ファイル本文の名前を確保----
        
        //----ファイル本文ファイルを作成
        $boolExeContinue = $this->makeFileBody($miNewMailQueFileFullname, $arrayData);
        //ファイル本文ファイルを作成----
        
        if($boolExeContinue === true){
            if($this->mailSendExecuteMode === true){
                //----メールキューフォルダへ移動
                $this->sendExecute($miNewMailQueFileFullname);
                //メールキューフォルダへ移動----
            }
        }
    }

    function mainProcess2(&$arrayData){
        global $g;
        
        $boolExeContinue = true;
        
        $miNewMailQueFileFullname = $this->getNewMailFilename();
        
        $arrayNull=array();
        
        if(0 < $arrayData['intSuccess']){
            $strUpdateUserBody = "";
            if( array_key_exists("login_name_jp",$g) === true ){
                $strUpdateUserBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17005",$g['login_name_jp']);
            }
            $strMailContentsBody  = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17006",array($this->getWebPageTileName(),$strUpdateUserBody));
            
            $aryResultList = $arrayData['resultList'];
            $aryActionNameList = $this->getListOfSendActionName();
            $strResultList = '';
            if( $this->getListOfSendActionName()===null ){
                //----制限する設定がなかった場合
                foreach($aryResultList as $key=>$aryData){
                    $strResultList .= $aryData[2];
                }
                //制限する設定がなかった場合----
            }else{
                $intListSuccess = 0;
                foreach($aryResultList as $key=>$aryData){
                    foreach($aryActionNameList as $key=>$strFocusActionName){
                        if( $aryData[0]==$strFocusActionName ){
                            if( 0 < $aryData[1] ){
                                $intListSuccess += 1;
                            }
                            $strResultList .= $aryData[2];
                            break;
                        }
                    }
                }
                if( $intListSuccess==0 ){
                    $boolExeContinue = false;
                    
                    unlink($miNewMailQueFileFullname);
                }
            }
            //$strMailContentsBody .= $arrayData['resultList']."\n";
            $strMailContentsBody .= $strResultList."\n";
        }
        else{
            $boolExeContinue = false;
            
            unlink($miNewMailQueFileFullname);
        }
        
        if($boolExeContinue === true){
            $strTextUpdate = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17007",$this->getWebPageTileName());
            $boolExeContinue = $this->makeFileBody($miNewMailQueFileFullname, $arrayData, $strMailContentsBody, $strTextUpdate);
        }
        
        if($boolExeContinue === true){
            
            if($this->mailSendExecuteMode === true){
                //----メールキューフォルダへ移動
                $this->sendExecute($miNewMailQueFileFullname);
                //メールキューフォルダへ移動----
            }
        }
    }

    function sendExecute($strMailQueFileFullPath){
        
        //----キューファイルの名前を作成
        $confSysmailQueueDirNS = $this->confSysmailQueueDirNS;
        $confRequestQueFileNewPath = $confSysmailQueueDirNS."/".basename($strMailQueFileFullPath);
        //キューファイルの名前を作成----
        
        //----SYSMAILのフォルダへファイル移動
        $boolRename = rename($strMailQueFileFullPath, $confRequestQueFileNewPath);
        //SYSMAILのフォルダへファイル移動----
        
        return $boolRename;
        
    }

    function makeFileBody($miNewMailQueFileFullname, &$arrayData, $p_mailBody="", $p_title=""){
        global $g;

        $strTextBody01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17008");
        $strTextBody02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17009");
        


        $boolRetValue = false;
        $filepointer    = fopen($miNewMailQueFileFullname, "w");

        if($p_title == ""){
            $p_title     = $this->getSubject($arrayData);
        }

        $p_from_mail = $this->getMailAddressFrom();

        if( $p_from_mail != "" ){
            $intControl = intval($g['event_mail_send']);
        }
        else{
            $intControl = 0;
        }
        //----ここから分岐
        
        switch($intControl){
            case 0:
                //----強制的に送信停止
                
                $p_to_mail  = "stop-to";
                $p_cc_mail  = "stop-cc";
                
                $this->mailSendExecuteMode = false;
                
                break;
                
                //強制的に送信停止----
            case 1:
                //----通常の送信先へ
                
                $p_to_mail  = $this->getMailAddressTo();
                $p_cc_mail  = $this->getMailAddressCc();
                
                break;
                
                //通常の送信先へ----
            case 2:
                //----開発者へ
                
                $p_to_mail  = $this->getMailAddressToWhenDev();
                $p_cc_mail  = $this->getMailAddressCcWhenDev();
                //
                break;
                
                //開発者へ----
            default:
                //----強制的に送信停止
                
                $this->mailSendExecuteMode = false;
                
                break;
                
                //強制的に送信停止----
        }
        
        //ここまで分岐----
        
        
        if( $p_to_mail == "" ){
            //----宛先が、指定されていなかった場合
            $boolRetValue = false;
            //宛先が、指定されていなかった場合----
        }
        else{
            if( $p_mailBody == "" ){
                $p_mailBody  = "";
                
                if( is_array($this->arrayReportColumnName) === true ){
                    $strInfoOfUpdateTarget = $this->getUpdateTargetArea($arrayData);
                    
                    $strUpdateUserBody = "";
                    if( array_key_exists("login_name_jp",$g) === true ){
                        $strUpdateUserBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-17010",$g['login_name_jp']);
                    }
                    $p_mailBody .= $g['objMTS']->getSomeMessage("ITAWDCH-STD-17011",array($this->getWebPageTileName(),$strInfoOfUpdateTarget,$strUpdateUserBody));
                    
                    $arrayKeys = array_keys($this->arrayReportColumnName);
                    
                    $objColumns = $this->objTable->getColumns();
                    
                    for($miFnv1 = 0; $miFnv1 < count($arrayKeys); $miFnv1++ ){
                        $boolIdConvert = false;
                        $boolSetItemName = false;
                        
                        $strOutPutColumnId = $arrayKeys[$miFnv1];
                        
                        if(array_key_exists($strOutPutColumnId, $arrayData)===true){
                            //----入力されたデータに含まれている（キーがある）場合のみ
                            
                            if($this->arrayReportColumnName[$strOutPutColumnId] == ""){
                                //----メールに表示する項目名が指定されなかった場合
                                
                                $strItemNameLine = "[" . $strOutPutColumnId ."]"."\n";
                                //メールに表示する項目名が指定されなかった場合----
                            }
                            else{
                                $strItemNameLine = "[" . $this->arrayReportColumnName[$strOutPutColumnId] ."]"."\n";
                                $boolSetItemName = true;
                            }
                            
                            $strItemValueLines = "";
                            
                            if( array_key_exists($strOutPutColumnId, $objColumns) === true ){
                                $objColumn = $objColumns[$strOutPutColumnId];
                                if( is_a($objColumn,'IDColumn') === true ){
                                    //----IDカラムだった場合
                                    $arrayMasterData = $objColumn->getMasterTableArrayForFilter();
                                    $valueKeyIdColumn = $arrayData[$strOutPutColumnId];
                                    if($valueKeyIdColumn != ""){
                                        $strItemValueLines = $arrayMasterData[$valueKeyIdColumn];
                                        $boolIdConvert = true;
                                    }
                                    //IDカラムだった場合----
                                }
                                if($boolSetItemName === false){
                                    $strItemNameLine = "[" . $objColumn->getColLabel(true) ."]"."\n";
                                }
                            }
                            
                            if($boolIdConvert===false){
                                $strItemValueLines = $arrayData[$strOutPutColumnId];
                            }
                            
                            $p_mailBody .= $strItemNameLine . $strItemValueLines."\n\n";
                            //入力されたデータに含まれている（キーがある）場合のみ----
                        }
                    }
                }
            }
            
            $strGuideUrl = $this->getGuideUrl();
            if( array_key_exists('updateResource',$arrayData) === true ){
                //$p_mailBody .= "[作業リソース識別子]\n";
                //$p_mailBody .= $arrayData['updateResource']."\n\n";
            }
            $p_mailBody .= "{$strTextBody01}\n";
            $p_mailBody .= date("Y/m/d H:i:s", $g['request_time'])."\n\n";
            if($strGuideUrl != ""){
                $p_mailBody .= "{$strTextBody02}\n";
                $p_mailBody .= $strGuideUrl;
            }
            //----作成途中で、処理されないようにロックする
            flock($filepointer,LOCK_EX);
            //作成途中で、処理されないようにロックする----
            fputs($filepointer,$p_title."\n"); // 件名
            fputs($filepointer,$p_from_mail."\n");                  // FROM_MAIL_ADDRESS
            fputs($filepointer,$p_to_mail."\n");                    // 宛先のメールアドレス(to)
            fputs($filepointer,$p_cc_mail."\n");                    // CC_MAIL_ADDRESS
            fputs($filepointer,$p_mailBody."\n");                   // 本文
            //----作成途中で、処理されないようにロックする
            flock($filepointer,LOCK_EX);
            //作成途中で、処理されないようにロックする----
            $boolRetValue = true;
            //
        }
        return $boolRetValue;
    }

    function getNewMailFilename(){
        
        $strNewFileTempName = "";
        
        $confTempCheckDirNS = $this->confTempCheckDirNS;
        $confSysmailTmplPreFix = $this->confSysmailTmplPreFix;
        $confTempMailBodyMakeDirNS = $this->confTempMailBodyMakeDirNS;
        
        $dlc_loop_flag = 0;
        
        do {
            
            $dlc_bool_mkdir = false;
            
            $file_basename = $confSysmailTmplPreFix . date("YmdHis") . '_' . mt_rand();
            $chkfullname = $confTempCheckDirNS . "/" . $file_basename . "_dir";
            if(file_exists($chkfullname)===false){
                //----一時ディレクトリがあるか？
                
                $dlc_bool_mkdir = mkdir($chkfullname,0777);
                
                //一時ディレクトリがあるか？----
            }
            if($dlc_bool_mkdir === true){
                //----ディレクトリは作成できた
                
                $strNewFileTempName = $confTempMailBodyMakeDirNS . "/" . $file_basename;
                
                if(file_exists($strNewFileTempName)===false){
                    
                    //----サイズ0のファイルを作成する
                    touch($strNewFileTempName);
                    //サイズ0のファイルを作成する----
                    
                    rmdir($chkfullname);
                    
                    $dlc_loop_flag = 1;
                    break;
                    
                }
                //ディレクトリは作成できた----
            }
            
        } while( $dlc_loop_flag == 0 );
        
        return $strNewFileTempName;
    }

}

class SafeCSVAdminForPHP{

    /*
    [----作成されるファイルの様式]
        [SOF]<SAFECSV>
        %R0,%L0,%C0,%escTag0,[EXEC_TYPE],登録,更新,削除,復活
        %R0,%L0,%C0,%escTag0,[IDColumn(1)],選択1,選択2,選択3,選択4,選択5
        %R0,%L0,%C0,%escTag0,[IDColumn(2)],選択1,選択2,選択3,選択4,選択5
        </SAFECSV>
        フィールドID＞%R0,%L0,%C0,EXEC_TYPE,DISUSE_FLAG,・・・,NOTE,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER,UPD_UPDATE_TIMESTAMP
        レコード(X)＞%R0,%L0,%C0,,,,
        [EOF]
    [作成されるファイルの様式----]
    */

    protected $prMasterRowStart;
    protected $prMasterRowEnd;

    protected $prFilterCondiRowStart;
    protected $prFilterCondiRowEnd;

    protected $prRecordRowStart;//レコード行の最初の行

    function __construct(){
        
        $this->prMasterRowStart = null;
        $this->prMasterRowEnd = null;
        
        $this->prFilterCondiRowStart = null;
        $this->FilterCondiRowEnd = null;
        
        $this->prRecordRowStart = null;
        
    }

    function __destruct(){

    }

    //----プルダウン
    function getMasterRowStart(){
        return $this->prMasterRowStart;
    }
    function getMasterRowEnd(){
        return $this->prMasterRowEnd;
    }
    //プルダウン----

    //----フィルタ条件
    function getFilterCondiRowStart(){
        return $this->prFilterCondiRowStart;
    }
    function getFilterCondiRowEnd(){
        return $this->FilterCondiRowEnd;
    }
    //フィルタ条件----

    function getRecordRowStart(){
        return $this->prRecordRowStart;
    }

    function checkSafeCSV($miFilename){
        $miRet=-1;
        $miLineIndex=0;
        $miFileHandle=fopen($miFilename,"r");
        while(! feof($miFileHandle)){
            $miLineIndex+=1;
            $miReadBody=fgets($miFileHandle);
            $miArray=explode("\r\n",$miReadBody);
            $miStrLine=$miArray[0];
            if($miLineIndex == 1){
                if($miStrLine != "<SAFECSV>"){
                    break;
                }
            }
            else if($miStrLine == "</SAFECSV>"){
                $miRet=$miLineIndex+1;
                break;
            }
        }
        fclose($miFileHandle);
        return $miRet;
    }

    function checkSafeCSV2($miFilename){
        $miRet=false;
        
        $this->prMasterRowStart = 0;
        $this->prMasterRowEnd = 0;
        
        $this->prFilterCondiRowStart = 0;
        $this->FilterCondiRowEnd = 0;
        
        $this->prRecordRowStart = 0;
        
        $miLineIndex=0;//1行目が1となるインデックス
        $miFileHandle=fopen($miFilename,"r");
        while(! feof($miFileHandle)){
            $miLineIndex+=1;
            $miReadBody=fgets($miFileHandle);
            $miArray=explode("\r\n",$miReadBody);
            $miStrLine=$miArray[0];
            if($this->prMasterRowEnd == 0){
                if($miLineIndex == 1){
                    if($miStrLine != "<SAFECSV>"){
                        break;
                    }
                    $this->prMasterRowStart = 1;
                }
                else if($miStrLine == "</SAFECSV>"){
                    $this->prMasterRowEnd = $miLineIndex;
                }
            }else{
                if($miLineIndex == $this->prMasterRowEnd + 1){
                    if($miStrLine != "<FILTERCON>"){
                        break;
                    }
                    $this->prFilterCondiRowStart = $miLineIndex;
                }else{
                    if($miStrLine == "</FILTERCON>"){
                        $this->FilterCondiRowEnd = $miLineIndex;
                        $this->prRecordRowStart = $miLineIndex + 1;
                        $miRet = true;
                    }
                }
            }
        }
        fclose($miFileHandle);
        return $miRet;
    }

    function makeRowArrayFromSafeCSVRecordRow($miStrSingleLineCrLf,$miArrayForbiddenUseStrBody=array("\r","\n",","),$miArrayEscHeadBody=array("%R","%L","%C")){
        $miArrayForbiddenUseStrLen = count($miArrayForbiddenUseStrBody);
        $miArrayFromStrLine=explode("\r\n",$miStrSingleLineCrLf);
        $miStrLineSource=$miArrayFromStrLine[0];
        $miArrayColBody=explode(",",$miStrLineSource);
        $miArrayColLength=count($miArrayColBody);
        $miArrayRetBody=array();
        for($miFnv1=0;$miFnv1 < $miArrayForbiddenUseStrLen; $miFnv1 ++){
            $miStrEscAfterBody=$miArrayColBody[$miFnv1];
            $miStrForbiddenStr=$miArrayForbiddenUseStrBody[$miFnv1];
            $miStrNoExistsForbidden=$miArrayEscHeadBody[$miFnv1]."0";
            if($miStrEscAfterBody!=$miStrNoExistsForbidden){
                for($miFnv2=$miArrayForbiddenUseStrLen; $miFnv2 < $miArrayColLength; $miFnv2 ++){
                    $miArrayColBody[$miFnv2] = str_replace($miStrEscAfterBody,$miStrForbiddenStr,$miArrayColBody[$miFnv2]);
                }
            }
        }
        $miIntFocusIndex=0;
        for($miFnv2=$miArrayForbiddenUseStrLen ;$miFnv2 < $miArrayColLength; $miFnv2 ++){
            $miArrayRetBody[$miIntFocusIndex]=$miArrayColBody[$miFnv2];
            $miIntFocusIndex+=1;
        }
        return $miArrayRetBody;
    }

    function makeSafeCSVRecordRowFromRowArray($miArrayRowSourceBody,$miArrayForbiddenUseBody=array("\r","\n",","),$miArrayEscHeadBody=array("%R","%L","%C")){
        $miRetRowHead="";
        $miSearchResult="";
        //
        $miArrayRowSourceLen = count($miArrayRowSourceBody);
        $miArrayForbiddenUseStrLen = count($miArrayForbiddenUseBody);
        
        $miArrayEscAfterBody=array();
        
        for($miFnv1=0; $miFnv1 < $miArrayForbiddenUseStrLen; $miFnv1 ++){
            $miSearchCount=0;
            $miStrForbiddenStr=$miArrayForbiddenUseBody[$miFnv1];
            $miStrEscAfterHead=$miArrayEscHeadBody[$miFnv1];
            //
            $miBoolEscRequire=false;
            for($miFnv2=0; $miFnv2 < $miArrayRowSourceLen; $miFnv2 ++){
                if(mb_strpos($miArrayRowSourceBody[$miFnv2], $miStrForbiddenStr, 0,"UTF-8")!==false){
                    //----含まれていた場合
                    $miBoolEscRequire=true;
                    //含まれていた場合---
                }
            }
            if($miBoolEscRequire===true){
                do{
                    $miSearchCount+=1;
                    for($miFnv2=0; $miFnv2 < $miArrayRowSourceLen; $miFnv2 ++){
                        $miSearchPattern=$miStrEscAfterHead.strval($miSearchCount);
                        $miSearchResult=mb_strpos($miArrayRowSourceBody[$miFnv2], $miSearchPattern, 0,"UTF-8");
                        if($miSearchResult!==false){
                            break;
                        }
                    }
                }while($miSearchResult!==false);
            }
            $miStrEscAfterBody=$miStrEscAfterHead.strval($miSearchCount);
            $miArrayEscAfterBody[$miFnv1]=$miStrEscAfterBody;
            $miRetRowHead.=$miStrEscAfterBody.",";
        }
        //----ここから置き換え
        for($miFnv1=0; $miFnv1 < $miArrayForbiddenUseStrLen; $miFnv1 ++){
            $miStrForbiddenStr=$miArrayForbiddenUseBody[$miFnv1];
            $miStrFocusEscAfterBody=$miArrayEscAfterBody[$miFnv1];
            $miStrNoExistsForbidden=$miArrayForbiddenUseBody[$miFnv1]."0";
            if($miStrFocusEscAfterBody!=$miStrNoExistsForbidden){
                for($miFnv2=0;$miFnv2 < $miArrayRowSourceLen; $miFnv2 ++){
                    $miArrayRowSourceBody[$miFnv2]=str_replace($miStrForbiddenStr,$miStrFocusEscAfterBody,$miArrayRowSourceBody[$miFnv2]);
                }
            }
        }
        //ここから置き換え----
        return $miRetRowHead.implode(",",$miArrayRowSourceBody)."\r\n";
        //ここまで置き換え----
    }
}
?>
