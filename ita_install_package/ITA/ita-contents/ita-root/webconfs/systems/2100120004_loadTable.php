<?php
//   Copyright 2020 NEC Corporation
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
//      CI/CD For IaCインターフェース情報
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040000");

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

    $table = new TableControlAgent('B_CICD_IF_INFO','IF_INFO_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040001"), 'B_CICD_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['IF_INFO_ROW_ID']->setSequenceID('B_CICD_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_IF_INFO_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);    // 登録画面無し


    //ホスト名
    $objVldt = new TextValidator(1, 128, false, '/^[\._a-zA-Z0-9-]+$/', "");
    $c = new TextColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040101"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $table->addColumn($c);

    //プロトコル
    $objVldt = new SingleTextValidator(1,8,false);
    $c = new TextColumn('PROTOCOL',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040400"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040401"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $table->addColumn($c);


    //ポート
    $c = new NumColumn('PORT',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040500"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200040501"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1,65535));
    $c->setRequired(true);
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
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('json')->setVisible(false);

    // 複製ボタン
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_journal_table')->setVisible(false
);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('json')->setVisible(false);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
