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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030001");

    //メニューグループID
    $table = new TableControlAgent('A_MENU_GROUP_LIST','MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030101"), 'A_MENU_GROUP_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030003"));
    
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    
    $table->setJsEventNamePrefix(true);
    
    $table->setGeneObject("webSetting", $arrayWebSetting);
    
    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['MENU_GROUP_ID']->setSequenceID('SEQ_A_MENU_GROUP_LIST');

    //メニューグループ名称'
    $c = new TextColumn('MENU_GROUP_NAME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030201"));
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030202"));
    $objVdt = new TextValidator(1, 64, false, "/^[^:\t\r\n]*$/s", $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030203"));
    $objVdt->setRegExp("/^[^\t\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVdt);
    $table->addColumn($c);
    
    // 表示順序
    $c = new NumColumn('DISP_SEQ',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030401"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030402"));
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    // パネル用画像
    $c = new FileUploadColumn('MENU_GROUP_ICON',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030501"));
    $c->setFileHideMode(false);
    $c->setMaxFileSize(20971520);//単位はバイト
    $table->addColumn($c);
    
    //メニュー情報
    $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1030301");
    $c = new LinkButtonColumn('MenuInfo',$strLabelText, $strLabelText, 'disp_menu_list', array(':MENU_GROUP_ID')); 
    $c->setDBColumn(false);
    $table->addColumn($c);
    
    $table->fixColumn();

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
