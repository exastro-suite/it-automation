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
    //Cobllerプロファイル一覧
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACBLH-MNU-1009");


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

    //No.
    $table = new TableControlAgent('C_COBBLER_PROFILE','COBBLER_PROFILE_ID', $g['objMTS']->getSomeMessage("ITACBLH-MNU-1010"), 'C_COBBLER_PROFILE_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['COBBLER_PROFILE_ID']->setSequenceID('C_COBBLER_PROFILE_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_COBBLER_PROFILE_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    //Cobllerプロファイル一覧
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACBLH-MNU-1011"));
    // エクセルのシート名
    //Cobllerプロファイル一覧
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACBLH-MNU-1012"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    //プロファイル名
    $c= new TextColumn('COBBLER_PROFILE_NAME',$g['objMTS']->getSomeMessage("ITACBLH-MNU-1013"));
    //256バイト以下の文字列が格納可能。
    $c->setDescription($g['objMTS']->getSomeMessage("ITACBLH-MNU-1014"));
    $c->setValidator(new SingleTextValidator(0, 256, false));
    $table->addColumn($c);



    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
