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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310000");

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

    $table = new TableControlAgent('A_CONTRAST_LIST','CONTRAST_LIST_ID', 'No', 'A_CONTRAST_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CONTRAST_LIST_ID']->setSequenceID('A_CONTRAST_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('A_CONTRAST_LIST_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // マルチユニーク制約
    $table->addUniqueColumnSet(array('CONTRAST_NAME','CONTRAST_MENU_ID_1','CONTRAST_MENU_ID_2','ALL_MATCH_FLG'));
     
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-310001"));//'比較定義'
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-310001"));//'比較定義'

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    // 検索機能の制御----

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $objVldt = new SingleTextValidator(0,256,false);
    $objVldt->setRegexp('/[^\\\[\]\*\?\|<>\:\"［］＊？￥”“゛]+$/');

    //'比較定義名'
    $c = new TextColumn('CONTRAST_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310002"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310003"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $c->setUnique(true);
    $table->addColumn($c);

    //'比較対象メニュー1'
    $c = new IDColumn('CONTRAST_MENU_ID_1',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310004"),'D_CMDB_MENU_LIST_CONTRAST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310006"));//エクセル・ヘッダでの説明
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('CONTRAST_MENU_ID_1');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CMDB_MENU_LIST_CONTRAST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'MENU_PULLDOWN',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //'比較対象メニュー2'
    $c = new IDColumn('CONTRAST_MENU_ID_2',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310005"),'D_CMDB_MENU_LIST_CONTRAST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310006"));//エクセル・ヘッダでの説明
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('CONTRAST_MENU_ID_2');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CMDB_MENU_LIST_CONTRAST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'MENU_PULLDOWN',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //'全件一致'
    $objVldt = new IntNumValidator(0,1,false);
    $c = new IDColumn('ALL_MATCH_FLG',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310007"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
    $c->setValidator($objVldt);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310008"));//エクセル・ヘッダでの説明
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('ALL_MATCH_FLG');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_FLAG_LIST_01_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'FLAG_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'FLAG_NAME',
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
?>
