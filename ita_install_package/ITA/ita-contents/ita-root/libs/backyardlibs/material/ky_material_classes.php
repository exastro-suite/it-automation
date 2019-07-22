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
 * インターフェース情報テーブルクラス
 */
class MaterialIfInfoTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MATERIAL_IF_INFO';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'REMORT_REPO_URL',
                                    'BRANCH',
                                    'CLONE_REPO_DIR',
                                    'PASSWORD',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ファイル管理テーブルクラス
 */
class FileManagementTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_FILE_MANAGEMENT';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('FILE_M_ID',
                                    'FILE_STATUS_ID',
                                    'FILE_ID',
                                    'REQUIRE_DATE',
                                    'REQUIRE_USER_ID',
                                    'REQUIRE_TICKET',
                                    'REQUIRE_ABSTRUCT',
                                    'REQUIRE_SCHEDULEDATE',
                                    'ASSIGN_DATE',
                                    'ASSIGN_USER_ID',
                                    'ASSIGN_FILE',
                                    'ASSIGN_REVISION',
                                    'RETURN_DATE',
                                    'RETURN_USER_ID',
                                    'RETURN_FILE',
                                    'RETURN_DIFF',
                                    'RETURN_TESTCASES',
                                    'RETURN_EVIDENCES',
                                    'CLOSE_DATE',
                                    'CLOSE_USER_ID',
                                    'CLOSE_REVISION',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ファイル管理テーブル（初期登録用）クラス
 */
class FileManagementInitialTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_FILE_MANAGEMENT_INITIAL';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('FILE_M_ID',
                                    'FILE_STATUS_ID',
                                    'FILE_ID',
                                    'REQUIRE_DATE',
                                    'REQUIRE_USER_ID',
                                    'REQUIRE_TICKET',
                                    'REQUIRE_ABSTRUCT',
                                    'REQUIRE_SCHEDULEDATE',
                                    'ASSIGN_DATE',
                                    'ASSIGN_USER_ID',
                                    'ASSIGN_FILE',
                                    'ASSIGN_REVISION',
                                    'RETURN_DATE',
                                    'RETURN_USER_ID',
                                    'RETURN_FILE',
                                    'RETURN_DIFF',
                                    'RETURN_TESTCASES',
                                    'RETURN_EVIDENCES',
                                    'CLOSE_DATE',
                                    'CLOSE_USER_ID',
                                    'CLOSE_REVISION',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ディレクトリマスタクラス
 */
class DirMasterTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName = 'F_DIR_MASTER';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('DIR_ID',
                                    'DIR_NAME',
                                    'PARENT_DIR_ID',
                                    'DIR_NAME_FULLPATH',
                                    'CHMOD',
                                    'GROUP_AUTH',
                                    'USER_AUTH',
                                    'DIR_USAGE',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 資材マスタクラス
 */
class FileMasterTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_FILE_MASTER';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('FILE_ID',
                                    'FILE_NAME',
                                    'DIR_ID',
                                    'AUTO_RETURN_FLAG',
                                    'CHMOD',
                                    'GROUP_AUTH',
                                    'USER_AUTH',
                                    'DIR_USAGE',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 資材マスタビュークラス
 */
class FileMasterView extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_FILE_MASTER';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('FILE_ID',
                                    'FILE_NAME',
                                    'DIR_ID',
                                    'FILE_NAME_FULLPATH',
                                    'AUTO_RETURN_FLAG',
                                    'CHMOD',
                                    'GROUP_AUTH',
                                    'USER_AUTH',
                                    'DIR_USAGE',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 資材一覧ビュークラス
 */
class FileManegementNewestView extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'G_FILE_MANAGEMENT_NEWEST';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('FILE_M_ID',
                                    'FILE_ID',
                                    'RETURN_FILE',
                                    'FILE_NAME_FULLPATH',
                                    'CLOSE_DATE',
                                    'RETURN_USER_ID',
                                    'CLOSE_REVISION',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                    'NEWEST_FLAG',
                                   );
    }
}

/**
 * 資材管理紐付(Ansible)テーブルクラス
 */
class MaterialLinkageAnsTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MATERIAL_LINKAGE_ANS';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'MATERIAL_LINK_NAME',
                                    'FILE_ID',
                                    'CLOSE_REVISION_ID',
                                    'ANS_PLAYBOOK_CHK',
                                    'ANS_TEMPLATE_CHK',
                                    'ANS_CONTENTS_FILE_CHK',
                                    'OS_TYPE_ID',
                                    'ANSIBLE_DIALOG_CHK',
                                    'ANSIBLE_ROLE_CHK',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ファイル管理テーブルクラス
 */
class AnsCommonContentsFileTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANS_CONTENTS_FILE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('CONTENTS_FILE_ID',
                                    'CONTENTS_FILE_VARS_NAME',
                                    'CONTENTS_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * テンプレート管理テーブルクラス
 */
class AnsibleCommonTemplateTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANS_TEMPLATE_FILE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ANS_TEMPLATE_ID',
                                    'ANS_TEMPLATE_VARS_NAME',
                                    'ANS_TEMPLATE_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * プレイブック素材集テーブルクラス
 */
class AnsibleCommonPlaybookTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LNS_PLAYBOOK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('PLAYBOOK_MATTER_ID',
                                    'PLAYBOOK_MATTER_NAME',
                                    'PLAYBOOK_MATTER_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 対話種別リストテーブルクラス
 */
class AnsiblePnsDialogTypeTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_PNS_DIALOG_TYPE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('DIALOG_TYPE_ID',
                                    'DIALOG_TYPE_NAME',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 対話ファイル素材集テーブルクラス
 */
class AnsiblePnsDialogTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_PNS_DIALOG';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('DIALOG_MATTER_ID',
                                    'DIALOG_TYPE_ID',
                                    'OS_TYPE_ID',
                                    'DIALOG_MATTER_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * ロールパッケージ管理テーブルクラス
 */
class AnsibleLrlRolePackageTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_ANSIBLE_LRL_ROLE_PACKAGE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROLE_PACKAGE_ID',
                                    'ROLE_PACKAGE_NAME',
                                    'ROLE_PACKAGE_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 資材管理紐付(OpenStack)テーブルクラス
 */
class MaterialLinkageOpenStackTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MATERIAL_LINKAGE_OPENSTACK';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'MATERIAL_LINK_NAME',
                                    'FILE_ID',
                                    'CLOSE_REVISION_ID',
                                    'OPENST_TEMPLATE_CHK',
                                    'OPENST_ENVIRONMENT_CHK',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}


/**
 * OpenStackテーブルクラス
 */
class PatternPerOrchTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'C_PATTERN_PER_ORCH';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('PATTERN_ID',
                                    'PATTERN_NAME',
                                    'ITA_EXT_STM_ID',
                                    'TIME_LIMIT',
                                    'ANS_HOST_DESIGNATE_TYPE_ID',
                                    'ANS_PARALLEL_EXE',
                                    'ANS_WINRM_ID',
                                    'ANS_GATHER_FACTS',
                                    'OPENST_TEMPLATE',
                                    'OPENST_ENVIRONMENT',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * 資材管理紐付(DSC)テーブルクラス
 */
class MaterialLinkageDscTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'F_MATERIAL_LINKAGE_DSC';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('ROW_ID',
                                    'MATERIAL_LINK_NAME',
                                    'FILE_ID',
                                    'CLOSE_REVISION_ID',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}

/**
 * DSCテーブルクラス
 */
class DscResourceTable extends BaseTable {

    /**
     * コンストラクタ
     */
    public function __construct($objDBCA, $db_model_ch) {

        parent::__construct($objDBCA, $db_model_ch);
        $this->tableName    = 'B_DSC_RESOURCE';
        $this->seqName      = $this->tableName . '_RIC';
        $this->jnlSeqName   = $this->tableName . '_JSQ';
        $this->columnNames  = array('RESOURCE_MATTER_ID',
                                    'RESOURCE_MATTER_NAME',
                                    'RESOURCE_MATTER_FILE',
                                    'DISP_SEQ',
                                    'NOTE',
                                    'DISUSE_FLAG',
                                    'LAST_UPDATE_TIMESTAMP',
                                    'LAST_UPDATE_USER',
                                   );
    }
}




