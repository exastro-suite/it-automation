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
    public function selectTable($sql){

        // SQL実行
        $result = $this->execQuery($sql, NULL, $objQuery);
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
                                    'FILE_NAME',
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
                                    'MENUGROUP_FOR_HG',
                                    'MENUGROUP_FOR_H',
                                    'MENUGROUP_FOR_VIEW',
                                    'MENUGROUP_FOR_CONV',
                                    'DISP_SEQ',
                                    'DESCRIPTION',
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
                                    'PREG_MATCH',
                                    'OTHER_MENU_LINK_ID',
                                    'DESCRIPTION',
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
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * テーブル項目名一覧テーブルクラス
 */
class TableItemListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_TABLE_ITEM_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('TABLE_ITEM_ID',
                                    'CREATE_MENU_ID',
                                    'CREATE_ITEM_ID',
                                    'COLUMN_NAME',
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
                                    'DISP_SEQ',
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
                                    'COL_TITLE',
                                    'COL_TITLE_DISP_SEQ',
                                    'REF_TABLE_NAME',
                                    'REF_PKEY_NAME',
                                    'REF_COL_NAME',
                                    'DISP_SEQ',
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
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * マスタ作成管理テーブルクラス
 */
class CreateMstMenuStatusTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_MST_MENU_STATUS';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MM_STATUS_ID',
                                    'CREATE_MENU_ID',
                                    'STATUS_ID',
                                    'FILE_NAME',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * マスタ作成情報テーブルクラス
 */
class CreateMstMenuInfoTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_MST_MENU_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CREATE_MENU_ID',
                                    'MENU_NAME',
                                    'MENUGROUP_FOR_MST',
                                    'DISP_SEQ',
                                    'DESCRIPTION',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * マスタ項目情報テーブルクラス
 */
class CreateMstItemInfoTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_CREATE_MST_ITEM_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CREATE_ITEM_ID',
                                    'CREATE_MENU_ID',
                                    'ITEM_NAME',
                                    'DISP_SEQ',
                                    'MAX_LENGTH',
                                    'PREG_MATCH',
                                    'DESCRIPTION',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * マスタ・テーブル紐付テーブルクラス
 */
class MstTableLinkTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MST_MENU_TABLE_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('MENU_TABLE_LINK_ID',
                                    'CREATE_MENU_ID',
                                    'TABLE_NAME_MST',
                                    'TABLE_NAME_MST_JNL',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * テーブル項目名一覧テーブルクラス
 */
class MstTableItemListTable extends BaseTable_CPM {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MST_TABLE_ITEM_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('TABLE_ITEM_ID',
                                    'CREATE_MENU_ID',
                                    'CREATE_ITEM_ID',
                                    'COLUMN_NAME',
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
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}
