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
//    ・SSOプロバイダー基本情報管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210001");

    $table = new TableControlAgent('D_PROVIDER_LIST','PROVIDER_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210011"), 'D_PROVIDER_LIST_JNL');

    // PROVIDER_ID
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210012"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210013"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['PROVIDER_ID']->setSequenceID('SEQ_A_PROVIDER_LIST');
    $table->setDBMainTableHiddenID('A_PROVIDER_LIST');
    $table->setDBJournalTableHiddenID('A_PROVIDER_LIST_JNL');

    // プロバイダー名
    $c = new TextColumn('PROVIDER_NAME', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210021"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setValidator(new TextValidator(1, 100, false));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210022"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    // 認証方式
    $c = new IDColumn('AUTH_TYPE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210031"), 'A_PROVIDER_AUTH_TYPE_LIST', 'NAME', 'NAME', null);
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210032"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('AUTH_TYPE');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_PROVIDER_AUTH_TYPE_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'NAME',
        'TTT_GET_TARGET_COLUMN_ID'=>'NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c1->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    // ロゴ(FILE UPLOAD)
    $c = new FileUploadColumn('LOGO',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210041"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210042"));
    $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $c->setFileHideMode(false);
    $table->addColumn($c);

    // 表示フラグ
    $c = new IDColumn('VISIBLE_FLAG', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210051"), 'A_VISIBLE_FLAG_LIST', 'ID', 'FLAG', null);
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1210052"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('VISIBLE_FLAG');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_VISIBLE_FLAG_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'FLAG',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c1->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
