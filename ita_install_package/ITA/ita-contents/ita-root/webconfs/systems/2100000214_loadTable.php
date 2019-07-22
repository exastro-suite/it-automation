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
//      オペレーション削除管理
//
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage('ITABASEH-MNU-214001');

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

    $table = new TableControlAgent('A_DEL_OPERATION_LIST','ROW_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214002'), 'A_DEL_OPERATION_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('A_DEL_OPERATION_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('A_DEL_OPERATION_LIST_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITABASEH-MNU-214003'));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214004'));

    // 論理削除日数
    $c = new NumColumn('LG_DAYS',  $g['objMTS']->getSomeMessage('ITABASEH-MNU-214005'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214006'));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1, 2147483647));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 物理削除日数
    $c = new NumColumn('PH_DAYS',  $g['objMTS']->getSomeMessage('ITABASEH-MNU-214007'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214008'));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1, 2147483647));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // テーブル名
    $c->setValidator(new SingleTextValidator(1, 256, false));
    $c = new TextColumn('TABLE_NAME', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214009'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214010'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    // 主キーカラム名
    $c->setValidator(new SingleTextValidator(1, 256, false));
    $c = new TextColumn('PKEY_NAME', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214011'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214012'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // オペレーションIDカラム名
    $c->setValidator(new SingleTextValidator(1, 256, false));
    $c = new TextColumn('OPE_ID_COL_NAME', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214013'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214014'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // データストレージパス取得SQL
    $c->setValidator(new SingleTextValidator(0, 1024, false));
    $c = new TextColumn('GET_DATA_STRAGE_SQL', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214015'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214016'));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 履歴データパス1
    $c->setValidator(new SingleTextValidator(0, 1024, false));
    $c = new TextColumn('DATA_PATH_1', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214017'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214018'));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 履歴データパス2
    $c->setValidator(new SingleTextValidator(0, 1024, false));
    $c = new TextColumn('DATA_PATH_2', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214019'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214020'));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 履歴データパス3
    $c->setValidator(new SingleTextValidator(0, 1024, false));
    $c = new TextColumn('DATA_PATH_3', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214021'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214022'));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 履歴データパス4
    $c->setValidator(new SingleTextValidator(0, 1024, false));
    $c = new TextColumn('DATA_PATH_4', $g['objMTS']->getSomeMessage('ITABASEH-MNU-214023'));
    $c->setDescription( $g['objMTS']->getSomeMessage('ITABASEH-MNU-214024'));//エクセル・ヘッダでの説明
    $table->addColumn($c);


    $table->fixColumn();


    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
