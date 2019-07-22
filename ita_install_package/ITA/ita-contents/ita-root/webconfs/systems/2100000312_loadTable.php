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
//    ・基本コンソール　Symphonyインスタンスに紐付Movementインスタンス一覧
//      データポータビリティ用。メニュー・ロール紐付で廃止 
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;


    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-209100");
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

    $table = new TableControlAgent('C_MOVEMENT_INSTANCE_MNG','MOVEMENT_INSTANCE_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209101"), 'C_MOVEMENT_INSTANCE_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MOVEMENT_INSTANCE_NO']->setSequenceID('C_MOVEMENT_INSTANCE_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_MOVEMENT_INSTANCE_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-209102"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209102"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    $c = new NumColumn('I_MOVEMENT_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209103"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209103"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_ORCHESTRATOR_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209104"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209104"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);

    $table->addColumn($c);
    $c = new NumColumn('I_PATTERN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209105"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209105"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209106"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209106"));//エクセル・ヘッダ>での説明
    $table->addColumn($c);

    $c = new NumColumn('I_TIME_LIMIT', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209107"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209107"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_ANS_HOST_DESIGNATE_TYPE_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209108"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209108"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_ANS_WINRM_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209109"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209109"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_DSC_RETRY_TIMEOUT', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209110"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_MOVEMENT_SEQ', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209111"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209111"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('I_NEXT_PENDING_FLAG', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209112"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209112"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);


    $c = new TextColumn('I_DESCRIPTION',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209113"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209113"));//エクセル・ヘッダ>での説明
    $table->addColumn($c);

    $c = new NumColumn('SYMPHONY_INSTANCE_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209114"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209114"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('EXECUTION_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209115"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209115"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('STATUS_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209116"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209116"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('ABORT_RECEPTED_FLAG', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209117"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209117"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209118"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209118"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209119"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209119"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $c = new NumColumn('RELEASED_FLAG', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209120"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209120"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('EXE_SKIP_FLAG', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209121"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209121"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('OVRD_OPERATION_NO_UAPK', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209122"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209122"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('OVRD_I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-209123"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209123"));//エクセル・ヘッダ>での説明
    $table->addColumn($c);

    $c = new NumColumn('OVRD_I_OPERATION_NO_IDBH', $g['objMTS']->getSomeMessage("ITABASEH-MNU-209124"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-209124"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
