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
//  【概要】
//      AnsibleTower DBAccess クラス
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/LogWriter.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/TableDefinitionsMaster.php");
require_once($root_dir_path . "/libs/commonlibs/common_php_functions.php");

class DBAccesser {

    const ISNULL_PARAM = " IS NULL ";

    private $objDBCA; // class DBConnectAgent
    private $dbMode; // 0:oracle/ 1:MySQL
    private $logger;
    private $dbAccessUserId;

    function __construct($dbAccessUserId,$objDBCA='') {
        $this->logger = LogWriter::getInstance();
        $this->dbAccessUserId = $dbAccessUserId;
        $this->objDBCA        = $objDBCA;
        if($objDBCA != '') {
            $this->dbMode = $objDBCA->getModelChannel();
        }
    }

    function connect() {

        // 接続
        $root_dir_temp = explode("ita-root", dirname(__FILE__));
        $root_dir_path = $root_dir_temp[0] . "ita-root";
        if($this->objDBCA == '') {
            $db_connect_php = $root_dir_path . "/libs/commonlibs/common_db_connect.php";
            require ($db_connect_php);
            $this->objDBCA = $objDBCA;
        }

        $this->dbMode = $objDBCA->getModelChannel();
        $GLOBALS['objDBCA'] = $this->objDBCA; // objDBCAを直呼びしたい箇所がどうしてもあるため、、、、
    }

    function beginTransaction() {

        return $this->objDBCA->transactionStart();
    }

    function commit() {

        return $this->objDBCA->transactionCommit();
    }

    function rollback() {

        return $this->objDBCA->transactionRollBack();
    }

    function inTransaction() {

        if(empty($this->objDBCA)) {
            return false;
        }

        return $this->objDBCA->getTransactionMode();
    }

    function setDbAccessUserId($dbAccessUserId) {

        $this->dbAccessUserId = $dbAccessUserId;
    }

    /**
     * テーブルの複数行を選択する
     *  (内部でITA共通のmakeSQLForUtnTableUpdateを使用する)
     *  データバインドを使用する
     *
     * @param string        $tableName          テーブル物理名
     * @param boolean       $containsDisuse     廃止レコードを 含むtrue/含まないfalse
     * @param array(str)    $conditionData      検索条件(1つの条件毎のバインド形式連想配列)
     *                                          array(key1 => value1,   // condition1
     *                                                key2 => value2)   // condition2
     *
     */
    function selectRowsUseBind($tableName, $containsDisuse = false, $conditionData) {

        $tableDefinition    = TableDefinitionsMaster::getDefinition($tableName);
        $strCurTable        = $tableName;
        $strJnlTable        = $tableDefinition::getJnlTableName();
        $strSeqOfCurTable   = $tableDefinition::getSequenceName();
        $strSeqOfJnlTable   = $tableDefinition::getJnlSequenceName();
        $arrayConfig        = $tableDefinition::getColumnSettingsWithJNL();
        $arrayValue         = $tableDefinition::getColumnSettingsWithJNL();
        $pkeyCol            = $tableDefinition::getPKColumnName();

        $conditionByBind = array();
        $arrayUtnBind = array();
        foreach($conditionData as $columnName => $value) {
            if(self::ISNULL_PARAM === $value) { // $valueが0のときNULL誤認に注意
                $this->logger->trace(__FUNCTION__ . " / IS NULL: $columnName => $value");
                $conditionByBind[] = $columnName . " IS NULL ";
            } else {
                $conditionByBind[] = $columnName . " = :" .$columnName;
                $arrayUtnBind[$columnName] = $value;
            }
        }
        if($containsDisuse == false) {
            $conditionByBind[] = "DISUSE_FLAG = '0'";
        }
        $temp_array = array('WHERE' => implode(" AND ", $conditionByBind));

        $retArray = makeSQLForUtnTableUpdate($this->dbMode,
                                             "SELECT FOR UPDATE",
                                             $pkeyCol,
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];

        //////////////////////////
        // Statement実行
        //////////////////////////
        $queryStatement = $this->executeStatement($sqlUtnBody, $arrayUtnBind);

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

    /**
     * テーブルの複数行を選択する
     *  (内部でITA共通のmakeSQLForUtnTableUpdateを使用しない)
     *
     * @param string        $tableName          テーブル物理名
     * @param boolean       $containsDisuse     廃止レコードを 含むtrue/含まないfalse
     * @param array(str)    $condition          検索条件(1つの条件毎の文字列配列)
     *                                          array("key1 = value1",          // condition1
     *                                                "key2 LIKE 'value2%'")    // condition2
     *
     */
    function selectRows($tableName, $containsDisuse = false, $condition = array()) {

        // #日時カラムフォーマット 置換処理#
        switch($this->dbMode) {
            case 0:
                $datetimeToStrFunction = "TO_CHAR";
                $datetimeToStrFormat = "YYYY/MM/DD HH24:MI:SS";
                break;
            case 1:
                $datetimeToStrFunction = "DATE_FORMAT";
                $datetimeToStrFormat = "%Y/%m/%d %H:%i:%s";
                break;
            default:
                //(0: Oracle / 1: MySql)
                throw new Exception("Unexpected DB mode: { $this->dbMode }");
                break;
        }
        $tableDefinition = TableDefinitionsMaster::getDefinition($tableName);
        $columnSettings = $tableDefinition::getColumnSettings();
        $columns = array();
        foreach($columnSettings as $key => $value) {
            if($value == "DATETIME") {
                $columns[] = " {$datetimeToStrFunction}({$key},'{$datetimeToStrFormat}') {$key} ";
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
                    . " FROM " . $tableName . " TAB_1 ";

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
        switch($this->dbMode) {
            case 0:
                $dbSystemTime = "SYSTIMESTAMP";
                break;
            case 1:
                $dbSystemTime = "NOW(6)";
                break;
            default:
                //(0: Oracle / 1: MySql)
                throw new Exception("Unexpected DB mode: { $this->dbMode }");
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

    function selectRow($tableName, $id, $containsDisuse = false) {

        //////////////////////////
        // SQL, Bind変数 生成
        //////////////////////////
        $queryType = "SELECT";

        $tableDefinition    = TableDefinitionsMaster::getDefinition($tableName);
        $pkey           = $tableDefinition::getPKColumnName();
        $jnlName        = $tableDefinition::getJnlTableName();
        $arrayConfig    = $tableDefinition::getColumnSettings();
        $arrayValue     = $tableDefinition::getColumns();
        $temp_array = array();

        $arrayValue[$pkey] = $id;

        if($containsDisuse == false) {
            $arrayValue['DISUSE_FLAG'] = "0";
        }

        // SQL作成＋バインド用変数準備
        $retArray = makeSQLForUtnTableUpdate($this->dbMode,
                                             $queryType,
                                             $pkey,
                                             $tableName,
                                             $jnlName,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array);

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        //////////////////////////
        // Statement実行
        //////////////////////////
        $queryStatement = $this->executeStatement($sqlUtnBody, $arrayUtnBind);

        //////////////////////////
        // 結果処理
        //////////////////////////
        // レコードFETCH
        $row1 = $queryStatement->resultFetch();
        $row2 = $queryStatement->resultFetch();
        if($row2 !== false) {
            throw new Exception("There are multiple rows returned for the primary key specification.: " . $tableName . ": " . $sqlUtnBody);
        }

        if($row1 == false) {
            $row1 = null;
        }

        //////////////////////////
        // DBアクセス事後処理
        //////////////////////////
        unset($queryStatement);

        return $row1;
    }

    function insertRow($tableName, $row, $dbAccessUserId = null) {
        $queryType = "INSERT";

        $pkeyVal = $this->insertOrUpdateRow($queryType, $tableName, $row, $dbAccessUserId);

        return $pkeyVal;
    }

    function updateRow($tableName, $row, $dbAccessUserId = null) {
        $queryType = "UPDATE";

        $pkeyVal = $this->insertOrUpdateRow($queryType, $tableName, $row, $dbAccessUserId);

        return $pkeyVal;
    }

    function insertOrUpdateRow($queryType, $tableName, $row, $dbAccessUserId = null) {

        //////////////////////////
        // SQL, Bind変数 生成
        //////////////////////////
        $tableDefinition    = TableDefinitionsMaster::getDefinition($tableName);
        $pkey           = $tableDefinition::getPKColumnName();
        $jnlName        = $tableDefinition::getJnlTableName();
        $utnSeqName     = $tableDefinition::getSequenceName();
        $jnlSeqName     = $tableDefinition::getJnlSequenceName();
        $arrayConfig    = $tableDefinition::getColumnSettingsWithJNL();
        $arrayValue     = $tableDefinition::getColumnsWithJNL();
        $temp_array = array();

        // レコード生成
        foreach($arrayValue as $key => $value) {
            if(array_key_exists($key, $row) && self::ISNULL_PARAM !== $row[$key]) { // $row[$key]が0のときNULL誤認に注意
                $arrayValue[$key] = $row[$key];
            }
        }

        // 共通カラム
        if(!array_key_exists("DISUSE_FLAG", $arrayValue) ||
            empty($arrayValue['DISUSE_FLAG'])) {
            $arrayValue['DISUSE_FLAG']     = "0";
        }
        $arrayValue['LAST_UPDATE_USER']    = $dbAccessUserId != null ? $dbAccessUserId : $this->dbAccessUserId;

        // 主キー
        if($queryType == "INSERT") {
            $utnId = $this->getAndLockSequence($utnSeqName);
            $arrayValue[$pkey]             = $utnId;
        }

        // ジャーナル主キー
        $jnlId = $this->getAndLockSequence($jnlSeqName);
        $arrayValue['JOURNAL_SEQ_NO']      = $jnlId;

        // SQL作成＋バインド用変数準備
        $retArray = makeSQLForUtnTableUpdate($this->dbMode,
                                             $queryType,
                                             $pkey,
                                             $tableName,
                                             $jnlName,
                                             $arrayConfig,
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

        return $arrayValue[$pkey];
    }

    private function executeStatement($sqlBody, $bindValues = null) {

        $queryStatement = $this->objDBCA->sqlPrepare($sqlBody);

        //生成したクエリをチェックする。
        if($queryStatement->getStatus() === false) {
            throw new Exception("Statement Error: " . $queryStatement->getLastError() . ": " . $sqlBody . ": " . json_encode($bindValues));
        }

        if($bindValues != null && $queryStatement->sqlBind($bindValues) != "") {
            throw new Exception("Parameter Bind Error: " . $queryStatement->getLastError() . ": " . $sqlBody . ": " . json_encode($bindValues));
        }

        $boolResult = $queryStatement->sqlExecute();
        if(!$boolResult) {
            throw new Exception("Execute Error: " . $queryStatement->getLastError() . ": " . $sqlBody . ": " . json_encode($bindValues));
        }

        return $queryStatement;
    }

    private function getAndLockSequence($seqName) {

        $GLOBALS['objDBCA'] = $this->objDBCA; // getSequenceLockInTrz() 内で参照されるため
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスをロック                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($seqName, 'A_SEQUENCE');
        if($retArray[1] != 0) {
            throw new Exception("Sequence Lock Error:" . implode(", ", $retArray[2]));
        }

        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスを採番                                   //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($seqName, 'A_SEQUENCE', FALSE);
        if($retArray[1] != 0) {
            throw new Exception("Get Sequence Error:" . implode(", ", $retArray[2]));
        }

        return $retArray[0];
    }

    function lockRelationTableSequence($tableNames) {

        $GLOBALS['objDBCA'] = $this->objDBCA; // getSequenceLockInTrz() 内で参照されるため

        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
        asort($tableNames);

        foreach($tableNames as $tableName) {
            $tableDefinition    = TableDefinitionsMaster::getDefinition($tableName);
            $jnlName        = $tableDefinition::getJnlTableName();
            $utnSeqName     = $tableDefinition::getSequenceName();
            $jnlSeqName     = $tableDefinition::getJnlSequenceName();
            // 実テーブルのシーケンス
            $retArray = getSequenceLockInTrz($utnSeqName,'A_SEQUENCE');
            if($retArray[1] != 0) {
                throw new Exception("Sequence Lock Error:" . implode(", ", $retArray[2]));
            }
            // ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($jnlSeqName,'A_SEQUENCE');
            if($retArray[1] != 0) {
                throw new Exception("Sequence Lock Error:" . implode(", ", $retArray[2]));
            }
        }
    }

    function __destruct() {
//        if(isset($this->objDBCA)) {
//            if($this->objDBCA->getTransactionMode()) {
//                $this->objDBCA->transactionRollBack();
//            }
//
//            $this->objDBCA = null;
//        }
    }
    function getdbMode()
    {
        return $this->dbMode;
    }
}

?>
