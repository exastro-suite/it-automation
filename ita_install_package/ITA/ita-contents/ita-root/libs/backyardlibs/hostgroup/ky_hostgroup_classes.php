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
class BaseTable {
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
    public function createSselect($conditions){
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
    public function createSselectJnl($conditions){
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

        $arrayUtnBind['LAST_UPDATE_TIMESTAMP'] = str_replace("-", "/", $updateData['LAST_UPDATE_TIMESTAMP']);

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
 * ホストグループ親子紐付テーブルクラス
 */
class HostLinkListTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HOST_LINK_LIST';
        $this->seqName      = 'SEQ_' . $this->tableName;
        $this->jnlSeqName   = 'JSEQ_' . $this->tableName;
        $this->columnNames  = array('ROW_ID',
                                    'LOOPALARM',
                                    'PA_HOSTGROUP',
                                    'CH_HOSTGROUP',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ホスト紐付管理テーブルクラス
 */
class HostLinkTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HOST_LINK';
        $this->seqName      = 'SEQ_' . $this->tableName;
        $this->jnlSeqName   = 'JSEQ_' . $this->tableName;
        $this->columnNames  = array('ROW_ID',
                                    'HOSTGROUP_NAME',
                                    'OPERATION_ID',
                                    'HOSTNAME',
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
class SplitTargetTable extends BaseTable {

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
 * メニュー管理テーブルクラス
 */
class MenuListTable extends BaseTable {

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
 * ホストグループ一覧テーブルクラス
 */
class HostgroupListTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HOSTGROUP_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'HOSTGROUP_NAME',
                                    'STRENGTH',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ホストグループ変数化テーブルクラス
 */
class HostgroupVarTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HOSTGROUP_VAR';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'HOSTGROUP_NAME',
                                    'VARS_NAME',
                                    'HOSTNAME',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ホストグループ変数紐付(Ansible-Legacy)テーブルクラス
 */
class HgVarLinkLegacyTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HG_VAR_LINK_LEGACY';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'VARS_NAME',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 変数名一覧(Ansible-Legacy)テーブルクラス
 */
class AnsibleLnsVarsMasterTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LNS_VARS_MASTER';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('VARS_NAME_ID',
                                    'VARS_NAME',
                                    'VARS_DESCRIPTION',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * Movement変数紐付管理(Ansible-Legacy)テーブルクラス
 */
class AnsLnsPtnVarsLinkTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANS_LNS_PTN_VARS_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('VARS_LINK_ID',
                                    'PATTERN_ID',
                                    'VARS_NAME_ID',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 作業対象ホスト(Ansible-Legacy)テーブルクラス
 */
class AnsibleLnsPhoLinkTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LNS_PHO_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('PHO_LINK_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 代入値管理(Ansible-Legacy)テーブルクラス
 */
class AnsibleLnsVarsAssignTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LNS_VARS_ASSIGN';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ASSIGN_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'VARS_LINK_ID',
                                    'VARS_ENTRY',
                                    'ASSIGN_SEQ',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ホストグループ変数紐付(Ansible-LegacyRole)テーブルクラス
 */
class HgVarLinkLegacyRoleTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_HG_VAR_LINK_LEGACYROLE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'VARS_NAME',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 変数名一覧(Ansible-LegacyRole)テーブルクラス
 */
class AnsibleLrlVarsMasterTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LRL_VARS_MASTER';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('VARS_NAME_ID',
                                    'VARS_NAME',
                                    'VARS_ATTRIBUTE_01',
                                    'VARS_DESCRIPTION',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * Movement変数紐付管理(Ansible-LegacyRole)テーブルクラス
 */
class AnsLrlPtnVarsLinkTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANS_LRL_PTN_VARS_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('VARS_LINK_ID',
                                    'PATTERN_ID',
                                    'VARS_NAME_ID',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 作業対象ホスト(Ansible-LegacyRole)テーブルクラス
 */
class AnsibleLrlPhoLinkTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LRL_PHO_LINK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('PHO_LINK_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 代入値管理(Ansible-LegacyRole)テーブルクラス
 */
class AnsibleLrlVarsAssignTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LRL_VARS_ASSIGN';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ASSIGN_ID',
                                    'OPERATION_NO_UAPK',
                                    'PATTERN_ID',
                                    'SYSTEM_ID',
                                    'VARS_LINK_ID',
                                    'COL_SEQ_COMBINATION_ID',
                                    'VARS_ENTRY',
                                    'ASSIGN_SEQ',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 機器一覧テーブルクラス
 */
class StmListTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'C_STM_LIST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('SYSTEM_ID',                      
                                    'HARDAWRE_TYPE_ID',               
                                    'HOSTNAME',                       
                                    'IP_ADDRESS',                     
                                    'ETH_WOL_MAC_ADDRESS',            
                                    'ETH_WOL_NET_DEVICE',             
                                    'PROTOCOL_ID',                    
                                    'LOGIN_USER',                     
                                    'LOGIN_PW_HOLD_FLAG',             
                                    'LOGIN_PW',                       
                                    'LOGIN_AUTH_TYPE',                
                                    'WINRM_PORT',                     
                                    'WINRM_SSL_CA_FILE',              
                                    'OS_TYPE_ID',                     
                                    'SSH_EXTRA_ARGS',                 
                                    'HOSTS_EXTRA_ARGS',               
                                    'SYSTEM_NAME',                    
                                    'COBBLER_PROFILE_ID',             
                                    'INTERFACE_TYPE',                 
                                    'MAC_ADDRESS',                    
                                    'NETMASK',                        
                                    'GATEWAY',                        
                                    'STATIC',                         
                                    'CONN_SSH_KEY_FILE',              
                                    'DSC_CERTIFICATE_FILE',           
                                    'DSC_CERTIFICATE_THUMBPRINT',     
                                    'ANSTWR_INSTANCE_GRP_ITA_MNG_ID', 
                                    'DISP_SEQ',                       
                                    'NOTE',                           
                                    'DISUSE_FLAG',                    
                                    'LAST_UPDATE_TIMESTAMP',          
                                    'LAST_UPDATE_USER'
                                   );
    }
}

/**
 * メニュー・テーブル紐付テーブルクラス
 */
class MenuTableLinkTable extends BaseTable {

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
