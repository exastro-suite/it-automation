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
//  【画面】
//      ファイル削除管理
//
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage('ITABASEH-MNU-215001');

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

    $table = new TableControlAgent('A_DEL_FILE_LIST','ROW_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-215002'), 'A_DEL_FILE_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('A_DEL_FILE_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('A_DEL_FILE_LIST_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITABASEH-MNU-215003'));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage('ITABASEH-MNU-215004'));

    // 削除日数
    $c = new NumColumn('DEL_DAYS',  $g['objMTS']->getSomeMessage('ITABASEH-MNU-215005'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-215006'));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1, 2147483647));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 削除対象ディレクトリ
    $c->setValidator(new SingleTextValidator(1, 1024, false));
    $c = new TextColumn('TARGET_DIR', $g['objMTS']->getSomeMessage('ITABASEH-MNU-215007'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-215008'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    // 削除対象ファイル
    $c->setValidator(new SingleTextValidator(1, 1024, false));
    $c = new TextColumn('TARGET_FILE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-215009'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-215010'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // サブディレクトリ削除有無
    $c = new IDColumn('DEL_SUB_DIR_FLG',$g['objMTS']->getSomeMessage('ITABASEH-MNU-215011'),'A_TODO_MASTER','TODO_ID','TODO_STATUS','', array('OrderByThirdColumn'=>'TODO_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-215012'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();


    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
