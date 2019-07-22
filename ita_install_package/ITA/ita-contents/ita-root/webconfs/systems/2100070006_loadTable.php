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

    //更新・廃止禁止
    $g['privilege'] = '2';

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-150000");


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

    $table = new TableControlAgent('C_OPENST_RESULT_MNG','EXECUTION_NO',  $g['objMTS']->getSomeMessage("ITAOPENST-MNU-150010"), 'C_OPENST_RESULT_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['EXECUTION_NO']->setSequenceID('C_OPENST_RESULT_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_OPENST_RESULT_MNG_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150178"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', 'C_OPENST_RESULT_MNG');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));


    $strTextBody = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-150020");
    $c = new LinkButtonColumn( 'MonitorExecution', $strTextBody, $strTextBody, 'monitor_execution', array( ":EXECUTION_NO" ) );
    $c->setDBColumn(false);
    $table->addColumn($c);
    
    $c = new LinkButtonColumn('ResultDetailJump', $g['objMTS']->getSomeMessage("ITAOPENST-MNU-150030"),  $g['objMTS']->getSomeMessage("ITAOPENST-MNU-150040"), 'result_detail_jump', array( ":EXECUTION_NO" ) ); 
    $c->setDBColumn(false);
    $table->addColumn($c);
    
    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150050"),'D_OPENST_STATUS','STATUS_ID','STATUS_NAME','',array('OrderByThirdColumn'=>'STATUS_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_OPENST_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //シンフォニークラス
    $c = new TextColumn('SYMPHONY_NAME',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130300"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130310"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //実行ユーザ
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130280"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130290"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,256,false);
    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150080"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,128,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150150"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150160"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

    $c = new NumColumn('I_OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150100"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150106"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150107"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150120"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-150130"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-150140"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
