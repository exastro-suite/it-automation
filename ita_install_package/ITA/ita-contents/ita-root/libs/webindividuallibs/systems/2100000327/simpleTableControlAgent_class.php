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
//
//  【処理概要】
//    ・JournalTable(履歴テーブル)を使用しないようにする
//    ・シーケンスの指定をしなくても機能するようにする
//    ・DISUSE_FLAGを本体テーブルでは存在しなくても機能するようにする(viewには固定値で存在させる)
//
//////////////////////////////////////////////////////////////////////

class simpleTableControlAgent_2100000327 extends TableControlAgent {
    /* Table : DBと１対１になるデータの集合。
        DBの情報と、DBから取得した値を保持
        データの出力はFormatterに委譲
    */

    function prepareColumn(&$arrayVariant=[]) {
        global $g;
        $strRIColumnLabel = isset($arrayVariant['TT_SYS_00_ROW_IDENTIFY_LABEL'])?$arrayVariant['TT_SYS_00_ROW_IDENTIFY_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18002");
        $strJnlSeqNoColLabel = isset($arrayVariant['TT_SYS_01_JNL_SEQ_LABEL'])?$arrayVariant['TT_SYS_01_JNL_SEQ_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18003");
        $strJnlRegTimeColLabel= isset($arrayVariant['TT_SYS_02_JNL_TIME_LABEL'])?$arrayVariant['TT_SYS_02_JNL_TIME_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18004");
        $strJnlClassColLabel= isset($arrayVariant['TT_SYS_03_JNL_CLASS_LABEL'])?$arrayVariant['TT_SYS_03_JNL_CLASS_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18005");
        $strDisuseFlagColLabel = isset($arrayVariant['TT_SYS_04_DISUSE_FLAG_LABEL'])?$arrayVariant['TT_SYS_04_DISUSE_FLAG_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18006");
        //
        $strRowEditByFileColLabel = isset($arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_LABEL'])?$arrayVariant['TT_SYS_NDB_ROW_EDIT_BY_FILE_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18007");
        $strUpdateColLabel = isset($arrayVariant['TT_SYS_NDB_UPDATE_LABEL'])?$arrayVariant['TT_SYS_NDB_UPDATE_LABEL']:$g['objMTS']->getSomeMessage("ITAWDCH-STD-18008");
        $strDuplicateColLabel = $g['objMTS']->getSomeMessage("ITAWDCH-STD-19033");
        //
        $boolDefaultColumnsSet = isset($arrayVariant['DEFAULT_COLUMNS_SET'])?$arrayVariant['DEFAULT_COLUMNS_SET']:true;
        //
        if ($boolDefaultColumnsSet === true) {
            //----ここから必須系[1]
            $c = new RowEditByFileColumn($this->getRequiredRowEditByFileColumnID(), $strRowEditByFileColLabel);
            $this->addColumn($c);
            
            $c = new JournalSeqNoColumnDummy_2100000327($this->getRequiredJnlSeqNoColumnID(), $strJnlSeqNoColLabel);  // Dummyに変更
            $this->addColumn($c);
            $c = new JournalRegDateTimeColumn($this->getRequiredJnlRegTimeColumnID(), $strJnlRegTimeColLabel);
            $this->addColumn($c);
            $c = new JournalRegClassColumn($this->getRequiredJnlRegClassColumnID(), $strJnlClassColLabel);
            $this->addColumn($c);
            //
            $c = new DuplicateBtnColumn($this->getDupButtonColumnID(), $strDuplicateColLabel);
            $this->addColumn($c);
            $c = new UpdBtnColumn($this->getRequiredUpdateButtonColumnID(), $strUpdateColLabel, $this->getRequiredDisuseColumnID());
            $this->addColumn($c);
            $c = new DelBtnColumn($this->getRequiredDisuseColumnID(), $strDisuseFlagColLabel);
            $this->addColumn($c);
            //
            $c = new RowIdentifyColumn($this->getRowIdentifyColumnID(), $strRIColumnLabel);
            $this->addColumn($c);
            //ここまで必須系[1]----
        }
    }
}

