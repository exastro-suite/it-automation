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
//    ・資格情報管理画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-800500");
/*
DSC 資格情報管理
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

    $table = new TableControlAgent('B_DSC_CREDENTIAL','CREDENTIAL_ID', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800501"), 'B_DSC_CREDENTIAL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CREDENTIAL_ID']->setSequenceID('B_DSC_CREDENTIAL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_CREDENTIAL_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-800502"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800503"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    //************************************************************************************
    //---- 埋め込み文字
    //************************************************************************************
    $objVldt = new TextValidator(1, 32, false, '/^CDT_[_a-zA-Z0-9]+$/', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800532"));
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c = new TextColumn('CREDENTIAL_VARS_NAME',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800530"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800531"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(false);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    //************************************************************************************
    //----ホスト
    //************************************************************************************
    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800540"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800541"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_STM_LIST_JNL');					// mysql_ita_model-a.sqlに存在
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----ドメイン／アカウント名
    //************************************************************************************
    $objVldt = new TextValidator(1, 32, false, '/^[a-zA-Z0-9-.\\@_]+$/', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800512"));
    $c = new TextColumn('CREDENTIAL_USER',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800510"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800511"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(false);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    //************************************************************************************
    //---- パスワード
    //************************************************************************************
    $objVldt = new SingleTextValidator(0,30,false, '/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800522"));
    $c = new PasswordColumn('CREDENTIAL_PW',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800520"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800521"));//エクセル・ヘッダでの説明
    $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setValidator($objVldt);
    $c->setEncodeFunctionName("ky_encrypt");
    $table->addColumn($c);

//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('CREDENTIAL_VARS_NAME','SYSTEM_ID'));

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
