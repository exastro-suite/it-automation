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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104001");

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

    $table = new TableControlAgent('F_CONVERT_PARAM_INFO','CONVERT_PARAM_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104002"), 'F_CONVERT_PARAM_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CONVERT_PARAM_ID']->setSequenceID('F_CONVERT_PARAM_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_CONVERT_PARAM_INFO_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104003"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104004"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    // 対象メニュー名:開始項目名
    $c = new IDColumn('CREATE_ITEM_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-104005"),'G_CREATE_ITEM_INFO','CREATE_ITEM_ID','LINK_PULLDOWN', '', array('SELECT_ADD_FOR_ORDER'=>array('CREATE_MENU_ID','DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1,ADD_SELECT_2') );
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104006"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $objVldt = new StartCreateItemValidator($c);
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // 項目数
    $c = new NumColumn('COL_CNT',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104007"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104008"));
    $c->setSubtotalFlag(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 繰り返し数
    $c = new NumColumn('REPEAT_CNT',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104009"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104010"));
    $c->setSubtotalFlag(false);
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