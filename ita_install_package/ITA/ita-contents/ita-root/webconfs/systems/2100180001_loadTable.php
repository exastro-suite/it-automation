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
//    ・CONDUCTORインターフェース情報
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-304000");
/*
CONDUCTORインターフェース情報
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
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP',
        'TT_SYS_08_DUPLICATE_ID'=>'WEB_BUTTON_DUPLICATE'
    );

    $table = new TableControlAgent('C_CONDUCTOR_IF_INFO','CONDUCTOR_IF_INFO_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-304010"), 'C_CONDUCTOR_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CONDUCTOR_IF_INFO_ID']->setSequenceID('C_CONDUCTOR_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_CONDUCTOR_IF_INFO_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-304020"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-304030"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);      // 登録画面無し


    //CONDUCTOR作業用データリレイストレージパス(ITA)
	$objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('CONDUCTOR_STORAGE_PATH_ITA',$g['objMTS']->getSomeMessage("ITABASEH-MNU-304040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-304050"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new NumColumn('CONDUCTOR_REFRESH_INTERVAL',$g['objMTS']->getSomeMessage("ITABASEH-MNU-304060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-304070"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(1000,null));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(
        array(
         'update'  =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203"), 'ct'=>0),
         'error'   =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12206"), 'ct'=>0)
        )
    );
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(
        array(
            2=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203")
        )
    );

    // 廃止ボタン
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('json')->setVisible(false);

    // 複製ボタン
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('json')->setVisible(false);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
