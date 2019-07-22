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
 *    A_AD_USER_JUDGEMENTモデルクラス
 */
class AAdUserJudgementModel extends BaseModel {

    public function __construct($objDBCA, $dbAccessUserId) {

        parent::__construct($objDBCA, $dbAccessUserId);

        //  テーブル名設定
        $this->tableName = "A_AD_USER_JUDGEMENT";

        //  テーブル名(履歴)設定
        $this->jnlName = "A_AD_USER_JUDGEMENT_JNL";

        //  主キー設定
        $this->pkey = "USER_JUDGE_ID";

        //  テーブル シーケンス名設定
        $this->utnSeqName = "SEQ_A_AD_USER_JUDGEMENT";

        //  テーブル(履歴) シーケンス名設定
        $this->jnlSeqName = "JSEQ_A_AD_USER_JUDGEMENT";

        //  テーブル定義(固有)
        $this->tableDefines = array(
            "USER_JUDGE_ID"         => "",
            "AD_USER_SID"           => "",
            "ITA_USER_ID"           => "",
        );

        //  makeSQLForUtnTableUpdate用カラムリスト
        $this->arrayConfig = array_merge($this->jnlColumns, $this->tableDefines, $this->commonColumns);

        //  find用カラムリスト
        $this->selectColumns = array_merge($this->tableDefines, $this->commonColumns);
    }
}
