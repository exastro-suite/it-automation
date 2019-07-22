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
/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100160001/validator.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103101");

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

    $table = new TableControlAgent('F_CREATE_MST_ITEM_INFO','CREATE_ITEM_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103102"), 'F_CREATE_MST_ITEM_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CREATE_ITEM_ID']->setSequenceID('F_CREATE_MST_ITEM_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_CREATE_MST_ITEM_INFO_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103103"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103104"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----



    // メニュー
    $c = new IDColumn('CREATE_MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103105"),'F_CREATE_MST_MENU_INFO','CREATE_MENU_ID','MENU_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103106"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // 項目名
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('ITEM_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103107"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103108"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // 表示順序
    $c = new NumColumn('DISP_SEQ', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103109"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103110"));
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setValidator(new IntNumValidator());
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setSubtotalFlag(false);
    $table->addColumn($c);


    // 最大バイト数
    $c = new NumColumn('MAX_LENGTH',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103111"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103112"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setSubtotalFlag(false);
    $c->setValidator(new MaxLengthValidator(1,1024));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // 正規表現
    $objVldt = new SingleTextValidator(0,1024,false);
    $c = new TextColumn('PREG_MATCH',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103113"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103114"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);


    // 説明
    $objVldt = new MultiTextValidator(0,1024,false);
    $c = new MultiTextColumn('DESCRIPTION',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103115"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103116"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->setValidator($objVldt);
    $table->addColumn($c);


//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('CREATE_MENU_ID','ITEM_NAME'));
//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>