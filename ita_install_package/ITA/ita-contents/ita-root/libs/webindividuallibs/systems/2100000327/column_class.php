<?php
//   Copyright 2021 NEC Corporation
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
//  【処理概要】
//    ・journal tableを使わないがcolumn class自体はdummyで存在させる(journalTableへの更新系でのSQLはskipする)
//    ・start transaction直後のsequenceへのselect .. for updateをskipする
//////////////////////////////////////////////////////////////////////

class JournalSeqNoColumnDummy_2100000327 extends TextColumn {
    //通常時は表示しない

    protected $strSequenceId;

    //----ここから継承メソッドの上書き処理

    function __construct ($strColId="JOURNAL_SEQ_NO", $strColExplain="", $strSequenceId=null) {
        global $g;
        
        if ($strColExplain === "") {
            $strColExplain = $g['objMTS']->getSomeMessage("ITAWDCH-STD-11301");
        }
        parent::__construct($strColId, $strColExplain);
        $this->setNum(true);
        $this->setSubtotalFlag(false);
        $this->setHeader(true);
        $this->setDBColumn(false);
        $this->getOutputType("print_journal_table")->setVisible(true);
        $this->getOutputType("update_table")->setVisible(false);
        $this->getOutputType("register_table")->setVisible(false);
        $this->getOutputType("filter_table")->setVisible(false);
        $this->getOutputType("print_table")->setVisible(false);
        $this->getOutputType("delete_table")->setVisible(false);
        $this->getOutputType("excel")->setVisible(false);
        $this->getOutputType("csv")->setVisible(false);
        $this->getOutputType("json")->setVisible(false);

        $this->setSequenceID($strSequenceId);
        //$this->setNumberSepaMarkShow(false);
    }

    //----FixColumnイベント系
    function afterFixColumn() {
        if ($this->getSequenceID() === null) {
            $arrayColumn = $this->objTable->getColumns();
            $objRIColumnID = $arrayColumn[$this->objTable->getRowIdentifyColumnID()];
            $strSeqId = $objRIColumnID->getSequenceID();
            if ($strSeqId !== "") {
                $this->setSequenceID("J".$strSeqId);
            }
        }
    }
    //FixColumnイベント系----

    //----TableIUDイベント系
    
    /* start transaction 直後のselect .. for updateをskip
    function getSequencesForTrzStart (&$arySequence=array()) {
    }
    */
    /* journal tableへの 事前select .. for updateをskip
    public function inTrzBeforeTableIUDAction (&$exeQueryData, &$reqOrgData=[], &$aryVariant=[]) {
    }
    */
    /* journal tableへのinsertをskip
    function inTrzAfterTableIUDAction(&$exeQueryData, &$reqOrgData=[], &$aryVariant=[]) {
    }
    */
    //TableIUDイベント系----

    //ここまで継承メソッドの上書き処理----

    //----ここから新規メソッドの定義宣言処理

    //NEW[1]
    function setSequenceID ($strSequenceId) {
        $this->strSequenceId = $strSequenceId;
    }

    //NEW[2]
    function getSequenceID() {
        return $this->strSequenceId;
    }
    //ここまで新規メソッドの定義宣言処理----
}

