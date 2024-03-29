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
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-301010");
/*
管理コンソール ADグループ判定(データポータビリティ用)
*/
    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('A_AD_GROUP_JUDGEMENT','GROUP_JUDGE_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-301020"), 'A_AD_GROUP_JUDGEMENT_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['GROUP_JUDGE_ID']->setSequenceID('SEQ_A_AD_GROUP_JUDGEMENT');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('JSEQ_A_AD_GROUP_JUDGEMENT');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-301030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-301040"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $c = new TextColumn('AD_GROUP_SID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-301050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-301060"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new IDColumn('ITA_ROLE_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-301070"), 'A_ROLE_LIST', 'ROLE_ID', 'ROLE_NAME', '', array('OrderByThirdColumn'=>'ROLE_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-301080"));//エクセル・ヘッダでの説明
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('ITA_ROLE_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_ROLE_LIST_JNL',
	        'TTT_SEARCH_KEY_COLUMN_ID'=>'ROLE_ID',
        	'TTT_GET_TARGET_COLUMN_ID'=>'ROLE_NAME',
	    	'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
	    	'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
	    	'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
	    )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
