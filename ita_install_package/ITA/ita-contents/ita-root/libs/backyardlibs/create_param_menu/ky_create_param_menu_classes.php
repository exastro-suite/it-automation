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
 * 【処理内容】
 *    クラス定義
 */

/**
 * ベーステーブルクラス
 */
class BaseTable_CPM {
    public $objDBCA;
    public $db_model_ch;
    public $tableName;
    public $seqName;
    public $jnlSeqName;
    public $columnNames;

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {
        $this->objDBCA = $objDBCA;
        $this->db_model_ch = $db_model_ch;
        $this->columnNames = array();
    }

    /**
     * SQL実行
     */
    public function execQuery($sql, $bindArray, &$objQuery){

        // SQL準備
        $objQuery = $this->objDBCA->sqlPrepare($sql);
        if(false === $objQuery->getStatus()){
            return $objQuery->getLastError();
        }

        if(is_array($bindArray)){
            // SQLバインド
            $result = $objQuery->sqlBind($bindArray);
            if("" != $result){
                return $result;
            }
        }

        // SQL実行
        $result = $objQuery->sqlExecute();
        if(true !== $result){
            return $objQuery->getLastError();
        }
        return true;
    }

    /**
     * カラム名設定
     */
    public function setColNames(){

        $sql = 'DESC ' . $this->tableName;

        // SQL実行
        $result = $this->execQuery($sql, NULL, $objQuery);
        if(true !== $result){
            return $result;
        }

        $resultArray = array();
        while ($row = $objQuery->resultFetch()){
            $resultArray[] = $row;
        }

        $this->columnNames  = array_column($resultArray, 'Field');
        return true;
    }

    /**
     * select文作成
     */
    public function createSselect($conditions=null){
        $columns_str = '';
        $set_str = '';
        $condition_str = '';

        foreach ($this->columnNames as $column) {
            $columns_str .= $columns_str ? ', ' . $column : $column ;
        }

        $sql = 'SELECT ' . $columns_str . ' FROM ' . $this->tableName . ' ' . $conditions;

        return $sql;
    }

    /**
     * select文作成(JNL)
     */
    public function createSselectJnl($conditions=null){
        $columns_str = '';
        $set_str = '';
        $condition_str = '';

        $columns_str = 'JOURNAL_SEQ_NO, JOURNAL_ACTION_CLASS, JOURNAL_REG_DATETIME';

        foreach ($this->columnNames as $column) {
            $columns_str .= $columns_str ? ', ' . $column : $column ;
        }

        $sql = 'SELECT ' . $columns_str . ' FROM ' . $this->tableName . '_JNL ' . $conditions;

        return $sql;
    }

    /**
     * SELECT
     */
    public function selectTable($sql, $arrayUtnBind=null){

        // SQL実行
        $result = $this->execQuery($sql, $arrayUtnBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        $resultArray = array();
        while ($row = $objQuery->resultFetch()){
            $resultArray[] = $row;
        }

        return $resultArray;
    }

    /**
     * JNLテーブルカラム名取得
     */
    public function getJnlColmnNames(){
        $retArray = Array('JOURNAL_SEQ_NO' => '',
                          'JOURNAL_ACTION_CLASS' => '',
                          'JOURNAL_REG_DATETIME' => '',
                         );

        foreach($this->columnNames as $columnName){
            $retArray[$columnName] = '';
        }
        return $retArray;
    }

    /**
     * UPDATE
     */
    public function updateTable($updateData, &$jnlSeqNo=NULL){

        // シーケンステーブルをロックする
        $resArray = getSequenceLockInTrz($this->jnlSeqName, 'A_SEQUENCE');
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }

        // JNLのIDを取得する
        $resArray = getSequenceValueFromTable($this->jnlSeqName, 'A_SEQUENCE', false);
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }
        $jnlSeqNo = $resArray[0];

        $arrayConfig = $this->getJnlColmnNames();

        $arrayValue = array('JOURNAL_SEQ_NO'        => $jnlSeqNo,
                            'JOURNAL_ACTION_CLASS'  => '',
                            'JOURNAL_REG_DATETIME'  => '',
                           );
        $arrayValue = array_merge($arrayValue, $updateData);

        // SQL作成
        $tmpAry = array();
        $resArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             'UPDATE',
                                             $this->columnNames[0],
                                             $this->tableName,
                                             $this->tableName . '_JNL',
                                             $arrayConfig,
                                             $arrayValue,
                                             $tmpAry
                                            );

        list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

        // SQL実行
        $result = $this->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        // SQL実行
        $result = $this->execQuery($sqlJnlBody, $arrayJnlBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        return true;
    }

    /**
     * UPDATE(メインテーブルのみ)
     */
    public function updateRecord($updateData){

        $arrayConfig = $this->getJnlColmnNames();

        $arrayValue = array('JOURNAL_SEQ_NO'        => '',
                            'JOURNAL_ACTION_CLASS'  => '',
                            'JOURNAL_REG_DATETIME'  => '',
                           );
        $arrayValue = array_merge($arrayValue, $updateData);

        // SQL作成
        $tmpAry = array();
        $resArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             'UPDATE',
                                             $this->columnNames[0],
                                             $this->tableName,
                                             $this->tableName . '_JNL',
                                             $arrayConfig,
                                             $arrayValue,
                                             $tmpAry
                                            );

        list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

        $arrayJnlBind['LAST_UPDATE_TIMESTAMP'] = str_replace("-", "/", $updateData['LAST_UPDATE_TIMESTAMP']);

        // SQL実行
        $result = $this->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        return true;
    }

    /**
     * UPDATE(ジャーナルテーブルのみ)
     */
    public function updateRecordJnl($updateData){

        $arrayConfig = $this->getJnlColmnNames();

        $arrayValue = $updateData;

        // SQL作成
        $tmpAry = array();

        $tmpAry = array('TT_SYS_01_JNL_SEQ_ID'      => 'dummy',
                        'TT_SYS_02_JNL_TIME_ID'     => 'dummy',
                        'TT_SYS_03_JNL_CLASS_ID'    => 'dummy',
                       );

        $resArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             'UPDATE',
                                             'JOURNAL_SEQ_NO',
                                             $this->tableName . '_JNL',
                                             $this->tableName . '_JNL',
                                             $arrayConfig,
                                             $arrayValue,
                                             $tmpAry
                                            );

        list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

        $arrayJnlBind['LAST_UPDATE_TIMESTAMP'] = str_replace("-", "/", $updateData['LAST_UPDATE_TIMESTAMP']);

        // SQL実行
        $result = $this->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        return true;
    }

    /**
     * INSERT
     */
    public function insertTable($insertData, &$seqNo=NULL, &$jnlSeqNo=NULL){

        // シーケンステーブルをロックする
        $resArray = getSequenceLockInTrz($this->seqName, 'A_SEQUENCE');
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }

        $resArray = getSequenceLockInTrz($this->jnlSeqName, 'A_SEQUENCE');
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }

        // IDを取得する
        $resArray = getSequenceValueFromTable($this->seqName, 'A_SEQUENCE', false);
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }
        $seqNo = $resArray[0];

        // JNLのIDを取得する
        $resArray = getSequenceValueFromTable($this->jnlSeqName, 'A_SEQUENCE', false);
        if($resArray[1] != 0){
            return print_r($resArray[2], true);
        }
        $jnlSeqNo = $resArray[0];

        $arrayConfig = $this->getJnlColmnNames();

        $arrayValue = array('JOURNAL_SEQ_NO'        => $jnlSeqNo,
                            'JOURNAL_ACTION_CLASS'  => '',
                            'JOURNAL_REG_DATETIME'  => '',
                           );

        // 先頭にIDを設定する
        $insertData[$this->columnNames[0]] = $seqNo;

        $arrayValue = array_merge($arrayValue, $insertData);

        // SQL作成
        $tmpAry = array();
        $resArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             'INSERT',
                                             $this->columnNames[0],
                                             $this->tableName,
                                             $this->tableName . '_JNL',
                                             $arrayConfig,
                                             $arrayValue,
                                             $tmpAry
                                            );

        list( , $sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) = $resArray;

        // SQL実行
        $result = $this->execQuery($sqlUtnBody, $arrayUtnBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        // SQL実行
        $result = $this->execQuery($sqlJnlBody, $arrayJnlBind, $objQuery);
        if(true !== $result){
            return $result;
        }

        return true;
    }

    /**
     * トランケート
     */
    public function truncate() {

        // テーブルのトランケート
        $sql = "TRUNCATE TABLE " . $this->tableName;

        // SQL準備
        $objQuery = $this->objDBCA->sqlPrepare($sql);
        if(false === $objQuery->getStatus()){
            return $objQuery->getLastError();
        }
        // SQL実行
        $result = $objQuery->sqlExecute();
        if(true !== $result){
            return $objQuery->getLastError();
        }

        // ジャーナルテーブルのトランケート
        $sql = "TRUNCATE TABLE " . $this->tableName . "_JNL";

        // SQL準備
        $objQuery = $this->objDBCA->sqlPrepare($sql);
        if(false === $objQuery->getStatus()){
            return $objQuery->getLastError();
        }
        // SQL実行
        $result = $objQuery->sqlExecute();
        if(true !== $result){
            return $objQuery->getLastError();
        }

        // シーケンステーブルの更新
        $sql = "UPDATE A_SEQUENCE SET VALUE=1 WHERE NAME='" . $this->seqName . "'";

        // SQL準備
        $objQuery = $this->objDBCA->sqlPrepare($sql);
        if(false === $objQuery->getStatus()){
            return $objQuery->getLastError();
        }
        // SQL実行
        $result = $objQuery->sqlExecute();
        if(true !== $result){
            return $objQuery->getLastError();
        }

        // シーケンステーブルの更新
        $sql = "UPDATE A_SEQUENCE SET VALUE=1 WHERE NAME='" . $this->jnlSeqName . "'";

        // SQL準備
        $objQuery = $this->objDBCA->sqlPrepare($sql);
        if(false === $objQuery->getStatus()){
            return $objQuery->getLastError();
        }
        // SQL実行
        $result = $objQuery->sqlExecute();
        if(true !== $result){
            return $objQuery->getLastError();
        }

        return true;
    }
}

/**
 * メニュー作成管理テーブルクラス
 */
class CreateMenuStatusTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_MENU_STATUS';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MM_STATUS_ID',
                                    'CREATE_MENU_ID',
                                    'STATUS_ID',
                                    'MENU_CREATE_TYPE_ID',
                                    'FILE_NAME',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * メニュー作成情報テーブルクラス
 */
class CreateMenuInfoTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_MENU_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CREATE_MENU_ID',
                                    'MENU_NAME',
                                    'PURPOSE',
                                    'TARGET',
                                    'VERTICAL',
                                    'MENUGROUP_FOR_INPUT',
                                    'MENUGROUP_FOR_SUBST',
                                    'MENUGROUP_FOR_VIEW',
                                    'MENU_CREATE_STATUS',
                                    'DISP_SEQ',
                                    'DESCRIPTION',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * メニュー項目情報テーブルクラス
 */
class CreateItemInfoTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_ITEM_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CREATE_ITEM_ID',
                                    'CREATE_MENU_ID',
                                    'ITEM_NAME',
                                    'DISP_SEQ',
                                    'REQUIRED',
                                    'UNIQUED',
                                    'COL_GROUP_ID',
                                    'INPUT_METHOD_ID',
                                    'MAX_LENGTH',
                                    'MULTI_MAX_LENGTH',
                                    'PREG_MATCH',
                                    'MULTI_PREG_MATCH',
                                    'OTHER_MENU_LINK_ID',
                                    'INT_MAX',
                                    'INT_MIN',
                                    'FLOAT_MAX',
                                    'FLOAT_MIN',
                                    'FLOAT_DIGIT',
                                    'PW_MAX_LENGTH',
                                    'UPLOAD_MAX_SIZE',
                                    'LINK_LENGTH',
                                    'REFERENCE_ITEM',
                                    'TYPE3_REFERENCE',
                                    'SINGLE_DEFAULT_VALUE',
                                    'MULTI_DEFAULT_VALUE',
                                    'INT_DEFAULT_VALUE',
                                    'FLOAT_DEFAULT_VALUE',
                                    'DATE_DEFAULT_VALUE',
                                    'DATETIME_DEFAULT_VALUE',
                                    'PULLDOWN_DEFAULT_VALUE',
                                    'LINK_DEFAULT_VALUE',
                                    'DESCRIPTION',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * メニュー・テーブル紐付テーブルクラス
 */
class MenuTableLinkTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MENU_TABLE_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MENU_TABLE_LINK_ID',
                                    'MENU_ID',
                                    'TABLE_NAME',
                                    'KEY_COL_NAME',
                                    'TABLE_NAME_JNL',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * メニュー管理テーブルクラス
 */
class MenuListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'A_MENU_LIST';
        $this->seqName      = 'SEQ_' . $this->tableName;
        $this->jnlSeqName   = 'JSEQ_' . $this->tableName;
        $this->columnNames  = array('MENU_ID',
                                    'MENU_GROUP_ID',
                                    'MENU_NAME',
                                    'LOGIN_NECESSITY',
                                    'SERVICE_STATUS',
                                    'AUTOFILTER_FLG',
                                    'INITIAL_FILTER_FLG',
                                    'WEB_PRINT_LIMIT',
                                    'WEB_PRINT_CONFIRM',
                                    'XLS_PRINT_LIMIT',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ロール・メニュー紐付管理テーブルクラス
 */
class RoleMenuLinkListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'A_ROLE_MENU_LINK_LIST';
        $this->seqName      = 'SEQ_' . $this->tableName;
        $this->jnlSeqName   = 'JSEQ_' . $this->tableName;
        $this->columnNames  = array('LINK_ID',
                                    'ROLE_ID',
                                    'MENU_ID',
                                    'PRIVILEGE',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ロール・ユーザー紐付管理テーブルクラス
 */
class RoleAccountLinkListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'A_ROLE_ACCOUNT_LINK_LIST';
        $this->seqName      = 'SEQ_' . $this->tableName;
        $this->jnlSeqName   = 'JSEQ_' . $this->tableName;
        $this->columnNames  = array('LINK_ID',
                                    'ROLE_ID',
                                    'USER_ID',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 紐付対象メニューテーブルクラス
 */
class CmdbMenuListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_CMDB_MENU_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MENU_LIST_ID',
                                    'MENU_ID',
                                    'SHEET_TYPE',
                                    'ACCESS_AUTH_FLG',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 紐付対象メニューテーブル管理テーブルクラス
 */
class CmdbMenuTableTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_CMDB_MENU_TABLE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('TABLE_ID',
                                    'MENU_ID',
                                    'TABLE_NAME',
                                    'PKEY_NAME',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 紐付対象メニューカラム管理テーブルクラス
 */
class CmdbMenuColumnTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_CMDB_MENU_COLUMN';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('COLUMN_LIST_ID',
                                    'MENU_ID',
                                    'COL_NAME',
                                    'COL_CLASS',
                                    'COL_TITLE',
                                    'COL_TITLE_DISP_SEQ',
                                    'REF_TABLE_NAME',
                                    'REF_PKEY_NAME',
                                    'REF_COL_NAME',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ホストグループ分割対象テーブルクラス
 */
class SplitTargetTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_SPLIT_TARGET';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'INPUT_MENU_ID',
                                    'OUTPUT_MENU_ID',
                                    'DIVIDED_FLG',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 非対象紐付メニューグループ一覧用テーブルクラス
 */
class CmdbHideMenuGrpTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_CMDB_HIDE_MENU_GRP';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('HIDE_ID',
                                    'MENU_GROUP_ID',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 他メニュー連携テーブルクラス
 */
class OtherMenuLinkTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_OTHER_MENU_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('LINK_ID',
                                    'MENU_ID',
                                    'COLUMN_DISP_NAME',
                                    'TABLE_NAME',
                                    'PRI_NAME',
                                    'COLUMN_NAME',
                                    'COLUMN_TYPE',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

class ColumnGroupTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_COLUMN_GROUP';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('COL_GROUP_ID',
                                    'PA_COL_GROUP_ID',
                                    'FULL_COL_GROUP_NAME',
                                    'COL_GROUP_NAME',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 縦管理メニュー作成情報テーブルクラス
 */
class ConvertParamInfoTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CONVERT_PARAM_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CONVERT_PARAM_ID',
                                    'CREATE_ITEM_ID',
                                    'COL_CNT',
                                    'REPEAT_CNT',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * パラメータシート縦横変換テーブルクラス
 */
class ColToRowMngTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_COL_TO_ROW_MNG';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'FROM_MENU_ID',
                                    'TO_MENU_ID',
                                    'PURPOSE',
                                    'START_COL_NAME',
                                    'COL_CNT',
                                    'REPEAT_CNT',
                                    'CHANGED_FLG',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 入力方式テーブルクラス
 */
class InputMethodTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_INPUT_METHOD';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('INPUT_METHOD_ID',
                                    'INPUT_METHOD_NAME',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 作成対象テーブルクラス
 */
class ParamTargetTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_PARAM_TARGET';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('TARGET_ID',
                                    'DISP_SEQ',
                                    'TARGET_NAME',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 用途テーブルクラス
 */
class ParamPurposeTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_PARAM_PURPOSE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('PURPOSE_ID',
                                    'PURPOSE_NAME',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * カラムグループクラス
 */
class MenuGroupTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'D_CMDB_MENU_GRP_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MENU_GROUP_ID',
                                    'MENU_GROUP_NAME',
                                    'MENU_GROUP_ICON',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * プルダウン選択中身クラス
 */
class PullDownTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_OTHER_MENU_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('LINK_ID',
                                    'MENU_GROUP_ID',
                                    'MENU_GROUP_NAME',
                                    'MENU_ID',
                                    'MENU_ID_CLONE',
                                    'MENU_NAME',
                                    'COLUMN_DISP_NAME',
                                    'LINK_PULLDOWN',
                                    'TABLE_NAME',
                                    'PRI_NAME',
                                    'COLUMN_NAME',
                                    'COLUMN_TYPE',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * アカウントリストクラス
 */
class AccountListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'A_ACCOUNT_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('USER_ID',
                                    'USERNAME',
                                    'USERNAME_JP',
                                    'PW_LAST_UPDATE_TIME',
                                    'AUTH_TYPE',
                                    'PROVIDER_ID',
                                    'PROVIDER_USER_ID',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 参照項目情報テーブル（ビュー）クラス
 */
class ReferenceItemTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_MENU_REFERENCE_ITEM';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ITEM_ID',
                                    'LINK_ID',
                                    'MENU_ID',
                                    'DISP_SEQ',
                                    'TABLE_NAME',
                                    'PRI_NAME',
                                    'COLUMN_NAME',
                                    'ITEM_NAME',
                                    'COL_GROUP_NAME',
                                    'DESCRIPTION',
                                    'INPUT_METHOD_ID',
                                    'SENSITIVE_FLAG',
                                    'ORIGINAL_MENU_FLAG',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 一意制約(複数項目)管理テーブルクラス
 */
class UniqueConstraintTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_UNIQUE_CONSTRAINT';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('UNIQUE_CONSTRAINT_ID',
                                    'CREATE_MENU_ID',
                                    'UNIQUE_CONSTRAINT_ITEM',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * パラメータシート参照用管理ビュー
 */
class ReferenceSheetType3View extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_CREATE_REFERENCE_SHEET_TYPE_3';
        $this->seqName      = '';
        $this->jnlSeqName   = '';
        $this->columnNames  = array('ITEM_ID',
                                    'MENU_NAME',
                                    'MENUGROUP_FOR_SUBST',
                                    'MENU_ID',
                                    'MENU_GROUP_ID',
                                    'MENU_GROUP_NAME',
                                    'MENU_TABLE_LINK_ID',
                                    'TABLE_NAME',
                                    'CREATE_ITEM_ID',
                                    'ITEM_NAME',
                                    'INPUT_METHOD_ID',
                                    'COL_GROUP_ID',
                                    'FULL_COL_GROUP_NAME',
                                    'COL_TITLE',
                                    'MENU_PULLDOWN',
                                    'COLUMN_NAME',
                                    'DISP_SEQ',
                                    'ACCESS_AUTH',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                    'ACCESS_AUTH_01',
                                    'ACCESS_AUTH_02',
                                    'ACCESS_AUTH_03',
                                    'ACCESS_AUTH_04',
                                   );
    }
}