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
//     ・文字列リテラルは、原則ダブルコーテーションでラップする
//     ・連想配列の鍵は、原則シングルコーテーションでラップする
//
//////////////////////////////////////////////////////////////////////

//----ここから多言語メッセージ表示用クラス
class MessageTemplateStorage{
    protected $strLangMode;
    protected $strLangMinorVer;
    protected $aryImportedGruopId;
    protected $aryMsgTemplate;
    protected $strLAMsgTmplLibRootPath;
    public function __construct($strLangMode="",$strLangMinorVer=""){
        $this->setSettingFileDirPath("confs");
        $this->setTemplateDirPath("libs/messages");
        if(strlen($strLangMode)==0){
            $strLangMode         = file_get_contents ( $this->getSettingFileDirPath() . "/commonconfs/app_msg_language.txt" );
            $strLangMode = trim($strLangMode);
        }
        $this->setLanguageMode($strLangMode);
        $this->setLanguageMinorVersion($strLangMinorVer);
        $this->aryImportedGruopId = array();
        $this->aryMsgTemplate = array();
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
    
    //----言語種類
    public function getLanguageMode(){
        return $this->strLangMode;
    }
    public function setLanguageMode($strLangMode){
        $strLangMode= in_array($strLangMode,array("ja_JP","en_US"))?$strLangMode:"en_US";
        $this->strLangMode = $strLangMode;
    }
    public function getLanguageMinorVersion(){
        return $this->strLangMinorVer;
    }
    public function setLanguageMinorVersion($strLangMinorVer){
        //----文字コード名
        $strLangMinorVer = (in_array($strLangMinorVer,array("UTF-8")))?$strLangMinorVer:"UTF-8";
        $this->strLangMinorVer = $strLangMinorVer;
        //文字コード名----
    }
    //言語種類----
    
    public function setTemplateDirPath($strLAMsgTmplLibRootPath){
        $retBool=false;
        if( checkRiskOfDirTraversal($strLAMsgTmplLibRootPath)===false ){
            $this->strLAMsgTmplLibRootPath = getApplicationRootDirPath()."/{$strLAMsgTmplLibRootPath}";
        }
        return $retBool;
    }
    public function getTemplateDirPath(){
        return $this->strLAMsgTmplLibRootPath;
    }
    
    public function getArrayFromTemplate($strTgtFileFullname){
        // テンプレートになるファイルを読み込んで配列に格納する
        $ary = array();
        if( file_exists($strTgtFileFullname) === true ){
            if( checkRiskOfDirTraversal($strTgtFileFullname)===false ){
                require($strTgtFileFullname);
            }
        }
        return $ary;
    }
    
    public function getLanguageFullVersion()    {
    	$retStrBody = $this->getLanguageMode();
    	if( $this->getLanguageMinorVersion() != "" ){
    		$retStrBody .= "_".$this->getLanguageMinorVersion();
    	}
    	return $retStrBody;
    }
    
    public function getTemplateFilePath($strProductTypeId,$strCollectionGroupId,$strPostFix=""){
        return $this->getTemplateDirPath()."/".$this->getLanguageFullVersion()."_".$strProductTypeId."_".$strCollectionGroupId.$strPostFix.".php";
    }
    
    public function getTemplateCollection($strCollectionGroupId,$strProductTypeId){
        $retVarValue = false;
        $strMemberKey = $strProductTypeId."-".$strCollectionGroupId;
        if( array_key_exists($strMemberKey, $this->aryImportedGruopId) === true ){
            $retVarValue  = $this->aryMsgTemplate[$strMemberKey];
        }else{
            $strTgtFileFullname = $this->getTemplateFilePath($strProductTypeId,$strCollectionGroupId,"");
            if( file_exists($strTgtFileFullname ) === true ){
                if( is_file($strTgtFileFullname) === true ){
                    $ary = array();
                    $ary = $this->getArrayFromTemplate($strTgtFileFullname);
                    if( isset($ary) === true ){
                        if( is_array($ary) === true ){
                            if( 0 < count($ary) ){
                                $this->aryMsgTemplate[$strMemberKey] = $ary;
                                $this->aryImportedGruopId[$strMemberKey] = 1;
                                $retVarValue = $ary;
                            }
                        }
                    }
                }
            }
        }
        return $retVarValue;
    }
    
    public function getSomeMessage($strMsgId,$aryDataForMsg=array()){
        $retStrBody = '';
        $boolExecuteContinue = false;
        //----引数2が、文字列の場合のみ、処理を継続する
        if( is_string($strMsgId) === true ){
            //----引数2が、非オブジェクト型または配列の場合のみ、処理を継続する
            if( is_array($aryDataForMsg) === true ){
                $boolExecuteContinue = true;
                foreach($aryDataForMsg as $aryKey=>$aryVal){
                    if( is_array($aryVal) === true || gettype($aryVal) == "object" ){
                        $boolExecuteContinue = false;
                        break;
                    }
                }
            }else if( gettype($aryDataForMsg) != "object" ){
                $tmpEscValue = $aryDataForMsg;
                $aryDataForMsg = array();
                $aryDataForMsg[0] = $tmpEscValue;
                $boolExecuteContinue = true;
            }
            //引数2が、非オブジェクト型または配列の場合のみ、処理を継続する----
        }
        //引数2が、文字列の場合のみ、処理を継続する----
        if($boolExecuteContinue === true ){
            $aryLibData = explode("-",$strMsgId);
            $intCount = count($aryLibData);
            if( $intCount == 1 ){
                $msgCallNumber = $aryLibData[0];
                $strCollectionGrpId = "default";
                $strProductTypeId = "default";
            }
            else if( $intCount == 2 ){
                $msgCallNumber = $aryLibData[1];
                $strCollectionGrpId = $aryLibData[0];
                $strProductTypeId = "default";
            }
            else if( $intCount == 3 ){
                $msgCallNumber = $aryLibData[2];
                $strCollectionGrpId = $aryLibData[1];
                $strProductTypeId = $aryLibData[0];
            }
            else{
                $aryProductTemp = array();
                for($fnv1 = 0; $fnv1 < $intCount - 2; $fnv1++ ){
                    $aryProductTemp[$fnv1] = $aryLibData[$fnv1];
                }
                $msgCallNumber = $aryLibData[$intCount - 1];
                $strCollectionGrpId = $aryLibData[$intCount - 2];
                $strProductTypeId = implode("-",$aryProductTemp);
            }
            $aryTemplate = $this->getTemplateCollection($strCollectionGrpId,$strProductTypeId);
            if( is_array($aryTemplate) === true ){
                $intMsgCallNumber = Intval($msgCallNumber);
                if( array_key_exists($intMsgCallNumber, $aryTemplate) === true ){
                    $strTemplate = $aryTemplate[$intMsgCallNumber];
                    $retStrBody = "";
                    $strDelimiter = "{}";
                    if( mb_strpos($strTemplate,$strDelimiter,0,$this->getLanguageMinorVersion()) === false ){
                        $retStrBody = $strTemplate;
                    }
                    else{
                        $intSafeCount = 0;
                        $aryTempElement = explode("{}",$strTemplate);
                        foreach( $aryTempElement as $element ){
                             $retStrBody .= $element;
                             if( array_key_exists($intSafeCount,$aryDataForMsg) ){
                                 $retStrBody .= $aryDataForMsg[$intSafeCount];
                             }            
                             $intSafeCount += 1;
                        }
                    }
                }
                else{
                    //----存在しないメッセージIDが指定された場合は、停止
                    $retStrBody = "Message id is not found.(Called-ID[{$strMsgId}])";
                    //存在しないメッセージIDが指定された場合は、停止----
                }
            }
            else{
                //----存在しないメッセージテンプレートが指定された場合は、停止
                $retStrBody = "Message collection is not found.(Called-ID[{$strMsgId}])";
                //存在しないメッセージテンプレートが指定された場合は、停止----
            }
        }
        else{
            //----引数が不正の場合は、停止
            $aryTemp1 = debug_backtrace($limit=1);
            $aryTemp2 = array($aryTemp1[0]['file'],$aryTemp1[0]['line']);
            $retStrBody = "Syntax Error is occured on ([FILE]".$aryTemp2[0].",[LINE]".$aryTemp2[1].")";
            //引数が不正の場合は、停止----
        }
        return $retStrBody;
    }
}
//ここまで多言語メッセージ表示用クラス----

//----ここからDB接続用クラス
class DBConnectAgent{
    protected $varQueryTimeBase;
    protected $varQueryTimeMicro;

    protected $strSettingFileDirPath;

    protected $intModelChannel;
    protected $varConnectHandle;
    protected $boolModeTransaction;
    protected $strAddInfoOfModel;

    public function __construct($varModelChannel=null, $boolModeTransaction=false){
        $this->setSettingFileDirPath("confs");

        $this->intModelChannel = $varModelChannel;
        $this->boolModeTransaction = $boolModeTransaction;
        //
        $intRetModelChannel = null;
        $this->varQueryTimeMicro = null;
        $this->varQueryTimeBase = null;
        $this->strAddInfoOfModel = null;
    }

    //----読み取り専用系プロパティ
    public function getModelChannel(&$refAddInfo=""){
        $refAddInfo = $this->strAddInfoOfModel;
        return $this->intModelChannel;
    }
    public function getConnectHandle(){
        return $this->varConnectHandle;
    }
    //読み取り専用系プロパティ----

    //----DB設定ファイル配置ディレクトリパスのプロパティ
    public function setSettingFileDirPath($strPathNS){
        if( checkRiskOfDirTraversal($strPathNS)===false ){
            $this->strSettingFileDirPath  = getApplicationRootDirPath()."/{$strPathNS}";
        }
    }
    public function getSettingFileDirPath(){
        return $this->strSettingFileDirPath;
    }
    //DB設定ファイル配置ディレクトリパスのプロパティ----

    //----トランザクションモード
    public function setTransactionMode($varModeValue){
        $this->boolModeTransaction = $varModeValue;
    }
    public function getTransactionMode(){
        return $this->boolModeTransaction;
    }
    //トランザクションモード----

    public function checkModelChannelOnSetting($varModelChannel){
        $intRetModelChannel = null;
        if($varModelChannel===null){
            $varModelChannel = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_model_string.txt" );
        }
        $intRetModelChannel = intval($varModelChannel);
        if( $intRetModelChannel == 0 || $intRetModelChannel == 1 ){
        }else{
            $intRetModelChannel = null;
        }
        return $intRetModelChannel;
    }

    public function connectOpen($db_username_e="", $db_password_e="", $db_connection_string_e=""){
        $tmpResult = false;
        if( $this->intModelChannel === null){
            $intModelChannel = $this->checkModelChannelOnSetting(null);
        }
        $this->intModelChannel = $intModelChannel;

        if(strlen($db_username_e)==0){
            $db_username_d          = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_username.txt" );
            $db_username_e          = $this->ky_decrypt( $db_username_d );
        }

        if(strlen($db_password_e)==0){
            $db_password_d          = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_password.txt" );
            $db_password_e          = $this->ky_decrypt( $db_password_d );
        }

        if(strlen($db_connection_string_e)==0){
            $db_connection_string_d = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_connection_string.txt" );
            $db_connection_string_e = $this->ky_decrypt( $db_connection_string_d );
        }

        if($this->intModelChannel===0){
            // オラクルの場合
            try {
                $lcConn = oci_connect( $db_username_e, $db_password_e, $db_connection_string_e );
                if($lcConn === false){
                    $tmpResult = false;
                }else{
                    $this->varConnectHandle = $lcConn;
                    $tmpResult = true;
                    // SQLを発行してバージョンを取得する
                    $strSql = "SELECT * FROM V\$VERSION";
                    $objQuery = $this->sqlPrepare($strSql);
                    if( $objQuery->getStatus() !== true ){
                        throw new Exception( "DBConnectAgent:ORACLE VERSION CHECK, QUERY PREPARE FAILED." );
                    }
                    $boolExe = $objQuery->sqlExecute();
                    if( $boolExe == false ){
                        throw new Exception( "DBConnectAgent:ORACLE VERSION CHECK, QUERY EXECUTE FAILED." );
                    }
                    $tmpRow = $objQuery->resultFetch();
                    if( array_key_exists('BANNER',$tmpRow) === false ){
                        throw new Exception( "DBConnectAgent:ORACLE VERSION CHECK,COLUMN NOT FOUND ERROR." );
                    }
                    $aryVerInfo1 = explode("Release ",$tmpRow['BANNER']);
                    if( array_key_exists(1,$aryVerInfo1) === false ){
                        throw new Exception( "DBConnectAgent:ORACLE VERSION CHECK,DATA TYPE UNEXPECTED ERROR." );
                    }
                    $aryVerInfo2 = explode("-",$aryVerInfo1[1]);
                    $this->strAddInfoOfModel = trim($aryVerInfo2[0]);
                }
            }
            catch (Exception $e){
                $tmpResult = false;
                error_log($e->getMessage());
            }
        }else if ($this->intModelChannel===1){
            // mySQL系の場合

            try {
                $objPDO = new PDO($db_connection_string_e, $db_username_e, $db_password_e, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
                $objPDO->exec("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;");
                $this->varConnectHandle = $objPDO;
                //
                $tmpResult = true;
            }
            catch (PDOException $e ){
                $tmpResult = false;
            }
        }
        return $tmpResult;
    }

    public function connectClose(){
        if($this->intModelChannel===0){
            // オラクルの場合
            oci_close($this->varConnectHandle);
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $this->varConnectHandle = null;
        }
    }

    public function getConnection(){
        if($this->intModelChannel===0){
            // オラクルの場合
            $db_connection_string_d = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_connection_string.txt");
            $db_connection_string_e = $this->ky_decrypt( $db_connection_string_d );
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $db_connection_string_d = file_get_contents ( $this->strSettingFileDirPath . "/commonconfs/db_connection_string.txt");
            $db_connection_string_e = $this->ky_decrypt( $db_connection_string_d );
        }
        return $db_connection_string_e;
    }

    //----ここからトランザクション系操作
    public function transactionStart(){
        // トランザクション開始
        $boolRet = false;
        if( $this->boolModeTransaction === false ){
            if($this->intModelChannel===0){
                // オラクルの場合
                $boolRet = true;
                $this->boolModeTransaction = true;
            }else if ($this->intModelChannel===1){
                // mySQL系の場合
                $boolRet = $this->varConnectHandle->beginTransaction();
                if($boolRet === true){
                    $this->boolModeTransaction = true;
                }
            }
        }
        return $boolRet;
    }

    public function transactionExit($boolMode=0){
        // トランザクション終了
        $boolRet = false;
        if( $this->boolModeTransaction === false ){
            $boolRet = true;
        }else{
            if( $boolMode === 1 ){
                $boolRet = $this->transactionCommit();
            }else{
                $boolRet = $this->transactionRollBack();
            }
        }
        return $boolRet;
    }

    public function transactionRollBack(){
        // ロールバック
        $boolRet = false;
        if($this->intModelChannel===0){
            // オラクルの場合
            $boolRet = oci_rollback($this->varConnectHandle);
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $boolRet = $this->varConnectHandle->rollBack();
        }
        //
        if( $boolRet === true ){
            $this->boolModeTransaction = false;
        }
        return $boolRet;
    }

    public function transactionCommit(){
        $boolRet = false;
        if($this->intModelChannel===0){
            // オラクルの場合
            $boolRet = oci_commit($this->varConnectHandle);
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $boolRet = $this->varConnectHandle->commit();
        }
        if( $boolRet === true ){
            $this->boolModeTransaction = false;
        }
        return $boolRet;
    }
    //ここまでトランザクション系操作----

    public function sqlPrepare($sql){
        $retValue=null;
        $varTransactionMode=null;
        if($this->intModelChannel===0){
            // オラクルの場合
            if($this->boolModeTransaction===true){
                $varTransactionMode = OCI_NO_AUTO_COMMIT;
            }else{
                $varTransactionMode = OCI_COMMIT_ON_SUCCESS;
            }
            $tmpParse = @oci_parse($this->varConnectHandle, $sql);
            if( $tmpParse === false ){
                //error_log($sql);
            }else{
                $retValue = new sqlStatementGripper($this->varConnectHandle, $this->intModelChannel, $tmpParse, $varTransactionMode, $sql);
            }
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $varPrepare = $this->varConnectHandle->prepare($sql);
            $retValue = new sqlStatementGripper($this->varConnectHandle, $this->intModelChannel, $varPrepare, $varTransactionMode, $sql);
        }
        return $retValue;
    }

    // 簡易暗号化ファンクション
    function ky_encrypt($str){
        // グローバル変数宣言
        return str_rot13(base64_encode($str));
    }

    // 簡易復号ファンクション
    function ky_decrypt($str){
        // グローバル変数宣言
        return base64_decode(str_rot13($str));
    }

    function setQueryTime(){
        // 文字列として取得
        list($this->varQueryTimeMicro, $this->varQueryTimeBase) = explode(" ", microtime());
    }

    function getQueryTime($formatMode=0){
        $ret = "";
        if( $this->varQueryTimeMicro!==null && $this->varQueryTimeBase!==null){
            if($formatMode == 0){
                $ret = sprintf("%s.%06d",date("Y/m/d H:i:s", $this->varQueryTimeBase),($this->varQueryTimeMicro*1000000));

            }else{
                $ret = date("Y/m/d H:i:s", $this->varQueryTimeBase);
            }
        }
        return $ret;
    }

}

class sqlStatementGripper{
    protected $varConnectHandle;
    protected $boolStatus;
    protected $varStatementBody;
    protected $intModelChannel;
    protected $varTransactionMode;
    protected $boolFetch;
    protected $intEffectedRow;
    protected $lastError;
    protected $strSql;

    public function __construct($varConnectHandle, $intModelChannel, $varStatementBody, $varTransactionMode, $strSql){
        $this->varConnectHandle   = $varConnectHandle;
        $this->intModelChannel    = $intModelChannel;
        $this->varStatementBody   = $varStatementBody;
        $this->varTransactionMode = $varTransactionMode;
        $this->boolFetch          = false;
        $this->intEffectedRow     = null;
        $this->strSql             = $strSql;
        $this->arrayError         = null;
        //
        if($intModelChannel===0){
            // オラクルの場合
            if($varStatementBody===false){
                $this->boolStatus = false;
                $this->lastError = "Error on construct class(sqlStatementGripper).";
            }else{
                $this->boolStatus = true;
            }
        }else if($intModelChannel===1){
            // mySQL系の場合
            if($varStatementBody===false){
                $this->boolStatus = false;
                $this->lastError = "Error on construct class(sqlStatementGripper).";
            }else{
                $this->boolStatus = true;
            }
        }
    }

    public function __destruct(){
        if($this->intModelChannel===0){
            // オラクルの場合
            if($this->varStatementBody !== null){
                oci_free_statement($this->varStatementBody);
            }
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
        }
    }

    public function getStatus(){
        return $this->boolStatus;
    }

    public function getStatementHandle(){
        return $this->varStatementBody;
    }

    public function getLastError(){
        return $this->lastError;
    }

    public function sqlBind($dataArray){
        $retValue="";
        if($this->varStatementBody === null){
        }else{
            //
            foreach($dataArray as $key=>$value){
                if(is_array($value)===true){
                    //----$valueが配列型の場合は、バインドしない。
                    //$valueが配列型の場合は、バインドしない。----
                }else{
                    $ret = null;
                    if($this->intModelChannel===0){
                        //オラクルの場合
                        $ret = @oci_bind_by_name($this->varStatementBody, ":".$key, $dataArray[$key]);
                    }else if ($this->intModelChannel===1){
                        //mySQL系の場合
                        $ret = $this->varStatementBody->bindValue(":".$key, $dataArray[$key]);
                    }
                    if( $ret === false ){
                        $retValue = "Bind by key[{$key}] error is occured.";
                        break;
                    }
                }
            }
            //
        }
        return $retValue;
    }

    public function sqlExecute(){
        $boolResult = false;
        if($this->intModelChannel===0){
            // オラクルの場合
            $this->intEffectedRow = null;
            $boolResult = @oci_execute($this->varStatementBody, $this->varTransactionMode);
            if($boolResult === true){
            }else{
                $this->lastError = print_r(oci_error($this->varStatementBody), true);
            }
        }else if ($this->intModelChannel===1){
            $this->intEffectedRow = null;
            // mySQL系の場合
            try {
                $boolResult = $this->varStatementBody->execute();
            }
            catch (PDOException $e){
                $this->lastError = $e->getMessage();
                $this->lastError = print_r($e->errorInfo,true);
            }
        }
        return $boolResult;
    }

    public function resultFetch(){
        $resultRow = false;
        $this->boolFetch = true;
        if($this->intModelChannel===0){
            // オラクルの場合
            $resultRow = oci_fetch_array($this->varStatementBody, OCI_ASSOC+OCI_RETURN_NULLS);
        }else if ($this->intModelChannel===1){
            // mySQL系の場合
            $resultRow = $this->varStatementBody->fetch(PDO::FETCH_ASSOC);
        }
        if( $resultRow !== false ){
            $this->intEffectedRow += 1;
        }
        return $resultRow;
    }

    public function effectedRowCount(){
        $retInt = null;
        if( $this->boolFetch === true ){
            $retInt = $this->intEffectedRow;
            if( $retInt === null ){
                $retInt = 0;
            }
        }else{
            if($this->intModelChannel===0){
                // オラクルの場合
                $retInt = oci_num_rows($this->varStatementBody);
            }else if ($this->intModelChannel===1){
                // mySQL系の場合
                $retInt = $this->varStatementBody->rowCount();
            }
        }
        return $retInt;
    }

}
//ここまでDB接続用クラス----
?>
