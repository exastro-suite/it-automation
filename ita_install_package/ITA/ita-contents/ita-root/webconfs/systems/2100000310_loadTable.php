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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-201060");
/*
交響曲インスタンス情報
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

    $table = new TableControlAgent('C_SYMPHONY_INSTANCE_MNG','SYMPHONY_INSTANCE_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-201070"), 'C_SYMPHONY_INSTANCE_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['SYMPHONY_INSTANCE_NO']->setSequenceID('C_SYMPHONY_INSTANCE_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_SYMPHONY_INSTANCE_MNG_JSQ');
    unset($tmpAryColumn);
    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-201080"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-201090"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    // UL/DL機能の制御----

    $table->setDBSortKey(array("SYMPHONY_INSTANCE_NO"=>"DESC"));

    //----リンクボタン
    $c = new LinkButtonColumn('detail_show',$g['objMTS']->getSomeMessage("ITABASEH-MNU-202010"), $g['objMTS']->getSomeMessage("ITABASEH-MNU-202020"), 'jumpToSymphonyInstanceMonitor', array(':SYMPHONY_INSTANCE_NO')); 
    $table->addColumn($c);
    //リンクボタン----

    $c = new IDColumn('I_SYMPHONY_CLASS_NO',$g['objMTS']->getSomeMessage("ITABASEH-MNU-202030"),'C_SYMPHONY_CLASS_MNG','SYMPHONY_CLASS_NO','SYMPHONY_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-202040"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITABASEH-MNU-202050"),'C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-202060"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $table->addColumn($c);



	$objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-202070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-202080"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $table->addColumn($c);

    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-202090"),'B_SYM_EXE_STATUS','SYM_EXE_STATUS_ID','SYM_EXE_STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-203010"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //実行ユーザ
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITABASEH-MNU-201110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-201120"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new IDColumn('ABORT_EXECUTE_FLAG',$g['objMTS']->getSomeMessage("ITABASEH-MNU-203020"),'B_SYM_ABORT_FLAG','SYM_ABORT_FLAG_ID','SYM_ABORT_FLAG_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-203030"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITABASEH-MNU-203040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-203050"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITABASEH-MNU-203060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-203070"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITABASEH-MNU-203080"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-203090"));//エクセル・ヘッダでの説明
	$c->setValidator(new DateTimeValidator(null,null));
    $table->addColumn($c);


    $table->fixColumn();
    $tmpAryColumn= $table->getColumns();

    list($strTmpValue,$tmpKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('callType'),null);
    if( $tmpKeyExists===true ){
        if( $strTmpValue=="insConstruct" ){
            $objRadioColumn = $tmpAryColumn['WEB_BUTTON_UPDATE'];
            
            $objRadioColumn->setColLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-204030"));
            
            $objFunctionB = function ($objOutputType, $rowData, $aryVariant, $objColumn){
                $strInitedColId = $objColumn->getID();
                
                $aryVariant['callerClass'] = get_class($objOutputType);
                $aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
                $strRIColId = $objColumn->getTable()->getRIColumnID();
                
                $rowData[$strInitedColId] = '<input type="radio" name="symNo" onclick="javascript:loadSymphonyForMonitor(' . $rowData[$strRIColId] . ')"/>';
                
                return $objOutputType->getBody()->getData($rowData,$aryVariant);
            };
            
            $objTTBF = new TextTabBFmt();
            $objTTHF = new TabHFmt();//new SortedTabHFmt();
            $objTTBF->setSafingHtmlBeforePrintAgent(false);
            $objOutputType = new VariantOutputType($objTTHF, $objTTBF);
            $objOutputType->setFunctionForGetBodyTag($objFunctionB);
            $objOutputType->setVisible(true);
            $objRadioColumn->setOutputType("print_table", $objOutputType);
            
            $table->getFormatter('print_table')->setGeneValue("linkExcelHidden",true);
            $table->getFormatter('print_table')->setGeneValue("linkCSVFormShow",false);
        }
    }
    unset($tmpAryColumn);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
