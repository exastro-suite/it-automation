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
//    ・収集インターフェース情報
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207200");
/*
収集インターフェース情報
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

    $table = new TableControlAgent('C_COLLECT_IF_INFO','COLLECT_IF_INFO_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207201"), 'C_COLLECT_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['COLLECT_IF_INFO_ID']->setSequenceID('C_COLLECT_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_COLLECT_IF_INFO_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207202"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207203"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);      // 登録画面無し


    //ホスト名
	$objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207204"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207205"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new TextColumn('IP_ADDRESS',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207206"));
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207207"));//エクセル・ヘッダでの説明
    $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
    $objVldt = new TextValidator(7, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
    $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVldt);
    $c->setUnique(true);
    $table->addColumn($c);

    //RESTユーザー
    $objVldt = new SingleTextValidator(0,30,false);
    $c = new TextColumn('LOGIN_USER',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207208"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207209"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    //RESTパスワード
    $objVldt = new SingleTextValidator(0,64,false);
    $c = new PasswordColumn('LOGIN_PW',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207210"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207211"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setEncodeFunctionName("ky_encrypt");
    $table->addColumn($c);

    //REST方式
    $c = new IDColumn('HOST_DESIGNATE_TYPE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207212"),'    B_HOST_DESIGNATE_TYPE_LIST','HOST_DESIGNATE_TYPE_ID','HOST_DESIGNATE_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207213"));//エクセル・ヘッダでの説明
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('HOST_DESIGNATE_TYPE_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_HOST_DESIGNATE_TYPE_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'HOST_DESIGNATE_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'HOST_DESIGNATE_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //プロトコル
    $objVldt = new SingleTextValidator(1,8,false);
    $c = new TextColumn('PROTOCOL',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207214"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207215"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    //ポート
    $c = new NumColumn('PORT',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207216"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207217"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
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
    $tmpAryColumn = $table->getColumns();
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
