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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102201");

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

    $table = new TableControlAgent('F_MENU_TABLE_LINK','MENU_TABLE_LINK_ID',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102202"), 'F_MENU_TABLE_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MENU_TABLE_LINK_ID']->setSequenceID('F_MENU_TABLE_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_MENU_TABLE_LINK_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102203"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102204"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    // メニューグループ:メニュー
    $c = new IDColumn('MENU_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102205"),'D_MENU_LIST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102206"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_MENU_LIST_JNL',
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

    // テーブル名
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('TABLE_NAME', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102207"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102208"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);

    $table->addColumn($c);

    // 主キー
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('KEY_COL_NAME', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102209"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102210"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);

    $table->addColumn($c);

    // テーブル名(履歴)
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('TABLE_NAME_JNL', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102211"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102212"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);


//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>