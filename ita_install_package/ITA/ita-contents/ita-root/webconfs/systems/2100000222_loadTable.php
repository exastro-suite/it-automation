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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-302010");
/*
管理コンソール ADユーザ判定(データポータビリティ用)
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

    $table = new TableControlAgent('A_AD_USER_JUDGEMENT','USER_JUDGE_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-302020"), 'A_AD_USER_JUDGEMENT_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['USER_JUDGE_ID']->setSequenceID('SEQ_A_AD_USER_JUDGEMENT');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('JSEQ_A_AD_USER_JUDGEMENT');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-302030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-302040"));

    $c = new TextColumn('AD_USER_SID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-302050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-302060"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new IDColumn('ITA_USER_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-302070"), 'A_ACCOUNT_LIST', 'USER_ID', 'USERNAME_JP', '', array('OrderByThirdColumn'=>'USER_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-302080"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
