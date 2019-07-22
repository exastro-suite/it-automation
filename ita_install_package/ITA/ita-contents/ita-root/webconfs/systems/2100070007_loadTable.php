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

    //更新・廃止禁止
    $g['privilege'] = '2';

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-170000");


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

    $table = new TableControlAgent('C_OPENST_RESULT_DETAIL','RESULT_DETAIL_ID', $g['objMTS']->getSomeMessage("ITAOPENST-MNU-170160"), 'C_OPENST_RESULT_DETAIL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['RESULT_DETAIL_ID']->setSequenceID('C_OPENST_RESULT_DETAIL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_OPENST_RESULT_DETAIL_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170150"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', 'C_OPENST_RESULT_DETAIL');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));

    $c = new NumColumn('EXECUTION_NO',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170170"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170180"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170010"),'B_OPENST_DETAIL_STATUS','STATUS_ID','STATUS_NAME','',array('OrderByThirdColumn'=>'STATUS_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170020"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_OPENST_DETAIL_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,45,false);
   $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-120030"),'B_OPENST_PROJECT_INFO','OPENST_PROJECT_ID','OPENST_PROJECT_NAME','',array('OrderByThirdColumn'=>'OPENST_PROJECT_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170040"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,2048,true);
    $c = new TextColumn('REQUEST_TEMPLATE',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170060"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,2048,false);
    $c = new TextColumn('RESPONSE_JSON',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170080"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,256,false);
    $c = new TextColumn('RESPONSE_MESSAGE',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170100"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170120"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-170130"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-170140"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);




    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
