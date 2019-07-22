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
 *    A_ROLE_LISTモデルクラス
 */
class ARoleListModel extends BaseModel {

    public function __construct($objDBCA, $dbAccessUserId) {

        parent::__construct($objDBCA, $dbAccessUserId);

        //  テーブル名設定
        $this->tableName = "A_ROLE_LIST";

        //  テーブル名(履歴)設定
        $this->jnlName = "A_ROLE_LIST_JNL";

        //  主キー設定
        $this->pkey = "ROLE_ID";

        //  テーブル シーケンス名設定
        $this->utnSeqName = "SEQ_A_ROLE_LIST";

        //  テーブル(履歴) シーケンス名設定
        $this->jnlSeqName = "JSEQ_A_ROLE_LIST";

        //  テーブル定義(固有)
        $this->tableDefines = array(
            "ROLE_ID"                => "",
            "ROLE_NAME"              => "",
        );

        //  makeSQLForUtnTableUpdate用カラムリスト
        $this->arrayConfig = array_merge($this->jnlColumns, $this->tableDefines, $this->commonColumns);

        //  find用カラムリスト
        $this->selectColumns = array_merge($this->tableDefines, $this->commonColumns);
    }
}
