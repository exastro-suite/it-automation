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
/**
 * 【概要】
 *    バッチクラス基底クラス
 */
class BaseModel {

    protected $objDBCA = null;
    protected $dbAccessUserId = null;

    protected $commonColumns = array(
        "NOTE"                      => "",
        "DISUSE_FLAG"               => "",
        "LAST_UPDATE_TIMESTAMP"     => "DATETIME",
        "LAST_UPDATE_USER"          => "",
    );

    protected $jnlColumns = array(
        "JOURNAL_SEQ_NO"            => "",
        "JOURNAL_REG_DATETIME"      => "DATETIME",
        "JOURNAL_ACTION_CLASS"      => "",
    );

    protected $tableName = "";
    protected $jnlName = "";
    protected $pkey = null;
    protected $tableDefines = null;
    protected $arrayConfig = null;
    protected $utnSeqName = null;
    protected $jnlSeqName = null;

    public function __construct($objDBCA, $dbAccessUserId) {
        $this->objDBCA = $objDBCA;
        $this->dbAccessUserId = $dbAccessUserId;
    }

    // $condition は array(
    //                 "some_column_1 = '1'",
    //                 "some_column_2 = 2"
    //              )
    // の形式を想定
    function find($condition, $containsDisuse = false) {

        // #日時カラムフォーマット 置換処理#
        $dbMode = $this->objDBCA->getModelChannel();
        switch($dbMode) {
            case 0:
                $strToDatetimeFunction = "TO_CHAR";
                $strToDatetimeFormat = "YYYY/MM/DD HH24:MI:SS";
                break;
            case 1:
                $strToDatetimeFunction = "DATE_FORMAT";
                $strToDatetimeFormat = "%Y/%m/%d %H:%i:%s";
                break;
            default:
                throw new Exception("The DB mode is not set correctly. (0: Oracle / 1: MySql):" . $dbMode);
                break;
        }

        $columns = array();
        foreach($this->selectColumns as $key => $value) {
            if($value == "DATETIME") {
                $columns[] = " {$strToDatetimeFunction}({$key},'{$strToDatetimeFormat}') {$key} ";
            } else {
                $columns[] = " {$key} ";
            }
        }
        // #日時カラムフォーマット 置換処理# ここまで

        //////////////////////////
        // SQL, Bind変数 生成
        //////////////////////////
        $sqlUtnBody = " SELECT "
                    . implode(",", $columns)
                    . " FROM " . $this->tableName . " TAB_1 ";

        if(!is_array($condition) && is_string($condition)) {
            $condition = array($condition);
        }
        if(is_null($condition) || !is_array($condition)) {
            $condition = array();
        }
        if($containsDisuse == false) {
            $condition[] = " TAB_1.DISUSE_FLAG = '0'";
        }

        if(!empty($condition)) {
            $sqlUtnBody .= " WHERE " .implode(" AND ", $condition) . ";";
        }

        // #現在日時 置換処理# ita-root/libs/commonlibs/common_php_functions.php : makeSQLForUtnTableUpdate()内の処理を参考に
        switch($dbMode) {
            case 0:
                $dbSystemTime = "SYSTIMESTAMP";
                break;
            case 1:
                $dbSystemTime = "NOW(6)";
                break;
            default:
                throw new Exception("The DB mode is not set correctly. (0: Oracle / 1: MySql):" . $dbMode);
                break;
        }
        $sqlUtnBody = str_replace(":KY_DB_DATETIME(6):", $dbSystemTime, $sqlUtnBody);
        // #現在日時 置換処理# ここまで

        //////////////////////////
        // Statement実行
        //////////////////////////
        $queryStatement = $this->executeStatement($sqlUtnBody);

        //////////////////////////
        // 結果処理
        //////////////////////////
        $tgt_execution_row = array();
        // レコードFETCH
        while($row = $queryStatement->resultFetch()) {
            $tgt_execution_row[] = $row;
        }

        //////////////////////////
        // DBアクセス事後処理
        //////////////////////////
        unset($queryStatement);

        return $tgt_execution_row;
    }

    function insertRow($row, $dbAccessUserId = null) {
        $queryType = "INSERT";

        $utnId = $this->insertOrUpdateRow($queryType, $row, $dbAccessUserId);

        return $utnId; // insertは生成したIDを返す
    }

    function updateRow($row, $dbAccessUserId = null) {
        $queryType = "UPDATE";

        $this->insertOrUpdateRow($queryType, $row, $dbAccessUserId);
    }

    function insertOrUpdateRow($queryType, $row, $dbAccessUserId = null) {

        //////////////////////////
        // SQL, Bind変数 生成
        //////////////////////////
        $arrayValue = $this->getColumnsWithJNL();
        $temp_array = array();

        // レコード生成
        foreach($arrayValue as $key => $value) {
            if(array_key_exists($key, $row)) {
                $arrayValue[$key] = $row[$key];
            }
        }

        // 共通カラム
        if(!array_key_exists("DISUSE_FLAG", $arrayValue) ||
            is_null($arrayValue['DISUSE_FLAG'])) {
            $arrayValue['DISUSE_FLAG']  = "0";
        }
        $arrayValue['LAST_UPDATE_USER'] = $dbAccessUserId != null ? $dbAccessUserId : $this->dbAccessUserId;

        // 主キー
        if($queryType == "INSERT") {
            $utnId = $this->getAndLockSequence($this->utnSeqName);
            $arrayValue[$this->pkey]    = $utnId;
        } else {
            $utnId = $arrayValue[$this->pkey];
        }

        // ジャーナル主キー
        $jnlId = $this->getAndLockSequence($this->jnlSeqName);
        $arrayValue['JOURNAL_SEQ_NO']   = $jnlId;

        // SQL作成＋バインド用変数準備
        $retArray = makeSQLForUtnTableUpdate($this->objDBCA->getModelChannel(),
                                             $queryType,
                                             $this->pkey,
                                             $this->tableName,
                                             $this->jnlName,
                                             $this->arrayConfig,
                                             $arrayValue,
                                             $temp_array);

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //////////////////////////
        // Statement実行
        //////////////////////////
        $utnStatement = $this->executeStatement($sqlUtnBody, $arrayUtnBind);
        $jnlStatement = $this->executeStatement($sqlJnlBody, $arrayJnlBind);

        //////////////////////////
        // 結果処理
        //////////////////////////
        // 処理成功していればOK

        //////////////////////////
        // DBアクセス事後処理
        //////////////////////////
        unset($utnStatement);
        unset($jnlStatement);

        return $utnId;
    }

    private function getAndLockSequence($seqName) {

        $GLOBALS['objDBCA'] = $this->objDBCA; // getSequenceLockInTrz() 内で参照されるため
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスをロック                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($seqName, 'A_SEQUENCE');
        if($retArray[1] != 0) {
            throw new Exception("Lock sequence has failed.: " . implode(", ", $retArray[2]));
        }

        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスを採番                                   //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($seqName, 'A_SEQUENCE', FALSE);
        if($retArray[1] != 0) {
            throw new Exception("Allocating sequence number has failed.: " . implode(", ", $retArray[2]));
        }

        return $retArray[0];
    }

    private function getColumns() {

        $resultArray = array();
        foreach($this->selectColumns as $key => $value) {
            $resultArray[$key] = "";
        }
        return $resultArray;
    }

    private function getColumnsWithJNL() {

        $resultArray = array();
        foreach($this->arrayConfig as $key => $value) {
            $resultArray[$key] = "";
        }
        return $resultArray;
    }

    private function executeStatement($sqlBody, $bindValues = null) {

        $queryStatement = $this->objDBCA->sqlPrepare($sqlBody);

        //生成したクエリをチェックする。
        if($queryStatement->getStatus() === false) {
            throw new Exception("ERROR:QUERY:" . $queryStatement->getLastError() . ":" . $sqlBody . ":" . json_encode($bindValues));
        }

        if($bindValues != null && $queryStatement->sqlBind($bindValues) != "") {
            throw new Exception("ERROR:QUERY_BIND:" . $queryStatement->getLastError() . ":" . $sqlBody . ":" . json_encode($bindValues));
        }

        $boolResult = $queryStatement->sqlExecute();
        if(!$boolResult) {
            throw new Exception("ERROR:QUERY_EXECUTE:" . $queryStatement->getLastError() . ":" . $sqlBody . ":" . json_encode($bindValues));
        }

        return $queryStatement;
    }
}
