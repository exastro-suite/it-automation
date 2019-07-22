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
/////////////////////////////////////////////////////////////////////
// Backyard common database access class
/////////////////////////////////////////////////////////////////////
class BackyardCommonDBAccessClass extends BackyardCommonDBAccessCoreClass {

    /////////////////////////////////////////////////////////////////////
    // construct
    /////////////////////////////////////////////////////////////////////
    function __construct($db_model_ch,$objDBCA,$objMTS,$db_access_user_id){
        parent::__construct($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);
    }

    /////////////////////////////////////////////////////////////////////
    // OperationList LAST_EXECUTE_TIMESTAMP update
    /////////////////////////////////////////////////////////////////////
    function OperationList_LastExecuteTimestamp_Update($OperationNo) {

        $TableName    = "C_OPERATION_LIST";
        $MemberAry    = array();
        $JNLMemberAry = array();
        $PkeyMember   = "";
        parent::ClearLastErrorMsg();

        $ret = parent::getTableDefinition($TableName,$MemberAry,$JNLMemberAry,$PkeyMember);
        if($ret === false) {
            return false;
        }

        $arrayConfig = $JNLMemberAry;
        $arrayValue  = $JNLMemberAry;

        $sqlBody   =    "SELECT \n"
                      . "  * \n"
                      . "FROM  C_OPERATION_LIST \n"
                      . "WHERE OPERATION_NO_UAPK = $OperationNo";
        $arrayBind = array();
        $objQuery  = "";
        $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            return false;
        }

        if($objQuery->effectedRowCount() == 0) {
            $message = sprintf("Recode not found. (Table:%s  OPERATION_NO_UAPK:%s)",$TableName,$OperationNo);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            return false;
        }

        while($row = $objQuery->resultFetch()) {
            foreach($row as $col=>$dummy)
            {
                $arrayValue[$col] = $row[$col];
            } 
        }

        // 最終実施日を設定する。
        $arrayValue['LAST_EXECUTE_TIMESTAMP'] = date('Y-m-d H:i:s');

        $ret = parent::dbaccessUpdate($TableName, $PkeyMember, $arrayConfig, $arrayValue);
        if($ret == false) {
            return false;
        }
        return true;
    }
}

/////////////////////////////////////////////////////////////////////
// Backyard common database access core class
/////////////////////////////////////////////////////////////////////
class BackyardCommonDBAccessCoreClass {
    private $objDBCA;
    private $objMTS;
    private $db_access_user_id;
    private $LastErrorMsg;
    
    /////////////////////////////////////////////////////////////////////
    // construct
    /////////////////////////////////////////////////////////////////////
    function __construct($db_model_ch,$objDBCA,$objMTS,$db_access_user_id){
        $this->db_model_ch       = $db_model_ch;
        $this->objDBCA           = $objDBCA;
        $this->objMTS            = $objMTS;
        $this->ClearLastErrorMsg();
        $this->db_access_user_id = $db_access_user_id;
    }

    /////////////////////////////////////////////////////////////////////
    // Sequence no allocation
    /////////////////////////////////////////////////////////////////////
    function dbaccessGetSequence($tableName) {
        // Sequence Lock
        $retArray = getSequenceLockInTrz($tableName, 'A_SEQUENCE');
        if($retArray[1] != 0) {
            $message = "Sequence Lock Error.";
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            $message = implode("\n",$retArray[2]);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            return null;
        }
    
        // Sequence allocate
        $retArray = getSequenceValueFromTable($tableName, 'A_SEQUENCE', FALSE);
        if($retArray[1] != 0) {
            $message = "Sequence no allocate error.";
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            $message = implode("\n",$retArray[2]);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            return null;
        }
        return $retArray[0];
    }

    /////////////////////////////////////////////////////////////////////
    // DB Update
    /////////////////////////////////////////////////////////////////////
    function dbaccessUpdate($targetTable, $PkeyMember, $arrayConfig, $arrayValue) {

        $strCurTable      = $targetTable;
        $strJnlTable      = $strCurTable . "_JNL";
        $strSeqOfCurTable = $strCurTable . "_RIC";
        $strSeqOfJnlTable = $strCurTable . "_JSQ";

        $jnlId = $this->dbaccessGetSequence($strSeqOfJnlTable);

        if(!$jnlId) {
            return false;
        }

        $arrayValue['JOURNAL_SEQ_NO']       = $jnlId;
        $arrayValue['LAST_UPDATE_USER']     = $this->db_access_user_id;

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             "UPDATE",
                                             $PkeyMember,
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array);

        $sqlCurBody = $retArray[1];
        $arrayCurBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        if(!$this->dbaccessExecute($sqlCurBody, $arrayCurBind)) {
            return false;
        }
        if(!$this->dbaccessExecute($sqlJnlBody, $arrayJnlBind)) {
            return false;
        }

        return true;
    }

    /////////////////////////////////////////////////////////////////////
    // DB access
    /////////////////////////////////////////////////////////////////////
    function dbaccessExecute($sqlBody, $arrayBind ,&$objQuery = null) {

        $objQuery = $this->objDBCA->sqlPrepare($sqlBody);
        if($objQuery->getStatus() === false) {
            $message = "DB Access Error.";
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$sqlBody);
            $errorDetail = $objQuery->getLastError();
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$errorDetail);
            unset($objQuery);
            return false;
        }

        if(isset($arrayBind) && $objQuery->sqlBind($arrayBind) != "") {
            $message = "DB Access Error.";
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$sqlBody);
            $errorDetail = $objQuery->getLastError();
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$errorDetail);
            unset($objQuery);
            return false;
        }

        $r = $objQuery->sqlExecute();
        if(!$r) {
            $message = "DB Access Error.";
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$sqlBody);
            $errorDetail = $objQuery->getLastError();
            $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$errorDetail);
            unset($objQuery);
            return false;
        }
        return true;
    }

    /////////////////////////////////////////////////////////////////////
    // get Table Definition
    /////////////////////////////////////////////////////////////////////
    function getTableDefinition($TableName,&$MemberAry,&$JNLMemberAry,&$PkeyName) {

        // テーブルの項目定義を取得
        $sql = "desc $TableName";

        $sqlBody = $sql;
        $arrayBind = array();
        $objQuery  = "";
        $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret == null) {
            return false;
        }

        $MemberAry = array();
        $PkeyName = '';
        while($row = $objQuery->resultFetch()) {
            $MemberAry[$row['Field']] = '';
            if($row['Key'] == "PRI")
            {
                $PkeyName = $row['Field'];
            }
        }
        unset($objQuery);

        // ジャーナルテーブルの項目定義を取得
        $sql = "desc $TableName" . "_JNL";
    
        $sqlBody = $sql;
        $arrayBind = array();
        $objQuery  = "";
        $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret == null) {
            return false;
        }

        $JNLMemberAry = array();
        while($row = $objQuery->resultFetch()) {
            $JNLMemberAry[$row['Field']] = '';
        }
        unset($objQuery);

        return true;
    }

    /////////////////////////////////////////////////////////////////////
    // Clear error message
    /////////////////////////////////////////////////////////////////////
    function ClearLastErrorMsg() {
        $this->LastErrorMsg = "";
    }
    /////////////////////////////////////////////////////////////////////
    // Set error message
    /////////////////////////////////////////////////////////////////////
    function SetLastErrorMsg($file,$line,$errorDetail) {
        $errmsg = sprintf("FILE:%s LINE:%s %s\n",$file,$line,$errorDetail);
        $this->LastErrorMsg .= $errmsg;
    }
    /////////////////////////////////////////////////////////////////////
    // Get last error message
    /////////////////////////////////////////////////////////////////////
    function GetLastErrorMsg() {
        $LastMsg = $this->LastErrorMsg;
        $this->ClearLastErrorMsg();
        return($LastMsg);
    }
}

