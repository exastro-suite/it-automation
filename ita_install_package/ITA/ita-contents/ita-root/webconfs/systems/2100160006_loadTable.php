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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102301");

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

    $table = new TableControlAgent('F_TABLE_ITEM_LIST','TABLE_ITEM_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102302"), 'F_TABLE_ITEM_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['TABLE_ITEM_ID']->setSequenceID('F_TABLE_ITEM_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_TABLE_ITEM_LIST_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102303"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102304"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    // メニュー名
    $c = new IDColumn('CREATE_MENU_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102305"),'F_CREATE_MENU_INFO','CREATE_MENU_ID','MENU_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102306"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);

    // 項目名
    $c = new IDColumn('CREATE_ITEM_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102307"),'F_CREATE_ITEM_INFO','CREATE_ITEM_ID','ITEM_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102308"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);

    // カラム名
    $objVldt = new SingleTextValidator(0,64,false);
    $c = new TextColumn('COLUMN_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102309"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102310"));//エクセル・ヘッダでの説明
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