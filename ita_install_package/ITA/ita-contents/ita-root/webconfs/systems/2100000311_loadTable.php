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
//    ・基本コンソール　Symphony紐付Movement一覧
//      OASE専用ユーザーからRESTAPIでアクセス
//      OASE専用ユーザー以外はメニュー・ロール紐付で廃止 
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;


    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-209000");
/*
Symphony紐付Movementの一覧
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

    $table = new TableControlAgent('C_MOVEMENT_CLASS_MNG','MOVEMENT_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209001"), 'C_MOVEMENT_CLASS_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MOVEMENT_CLASS_NO']->setSequenceID('C_MOVEMENT_CLASS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_MOVEMENT_CLASS_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-209002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209002"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    $c = new NumColumn('ORCHESTRATOR_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209003"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209003"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('PATTERN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209004"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209004"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('MOVEMENT_SEQ', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209005"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209005"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('NEXT_PENDING_FLAG', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209006"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209006"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('DESCRIPTION',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209007"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209007"));//エクセル・ヘッダ>での説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('SYMPHONY_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209008"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209008"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
