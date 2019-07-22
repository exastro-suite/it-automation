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
//    ・Ansible（Legacy Role）多次元変数最大繰返数管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705010");

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

    $table = new TableControlAgent('D_ANS_LRL_MAX_MEMBER_COL','MAX_COL_SEQ_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705020"), 'D_ANS_LRL_MAX_MEMBER_COL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MAX_COL_SEQ_ID']->setSequenceID('B_ANS_LRL_MAX_MEMBER_COL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_LRL_MAX_MEMBER_COL_JSQ');

    // ファイルアップロードで廃止／復活を無効にする。
    $strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12202");   //登録
    $strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12203");   //更新
    $strResultType03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204");   //廃止
    $strResultType04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12205");   //復活
    $strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12206");   //エラー

    $tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(array( 'register'=>array('name'=>$strResultType01  ,'ct'=>0)
                                                            ,'update'  =>array('name'=>$strResultType02  ,'ct'=>0)
                                                            ,'error'   =>array('name'=>$strResultType99  ,'ct'=>0)
                                                            )
                                                      );
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(array( 1=>$strResultType01
                                                                    ,2=>$strResultType02
                                                                    )
                                                      );
    // 廃止フラグを表示しない
    $outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
    $tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_ANS_LRL_MAX_MEMBER_COL');
    $table->setDBJournalTableHiddenID('B_ANS_LRL_MAX_MEMBER_COL_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705040"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    /////////////////////////////////////////////////////
    // 変数名(エクセル/CSVからのアップロード用)
    /////////////////////////////////////////////////////
    $c = new IDColumn('VARS_NAME_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705050"),'B_ANSIBLE_LRL_VARS_MASTER','VARS_NAME_ID','VARS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_ANSIBLE_LRL_VARS_MASTER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('VARS_NAME_ID');
    $c->setJournalDispIDOfMaster('VARS_NAME');

    //エクセル/CSVからのアップロードは不可能
    $c->setAllowSendFromFile(false);
    //更新対象カラム
    $c->setHiddenMainTableColumn(true);

    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(true);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    $table->addColumn($c);

    /////////////////////////////////////////////////////
    // メンバー変数名(エクセル/CSVからのアップロード用)
    /////////////////////////////////////////////////////
    $c = new IDColumn('ARRAY_MEMBER_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705070"),'D_ANS_LRL_ARRAY_MEMBER','ARRAY_MEMBER_ID','VRAS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705080"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANS_LRL_ARRAY_MEMBER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ARRAY_MEMBER_ID');
    $c->setJournalDispIDOfMaster('VRAS_NAME');

    //エクセル/CSVからのアップロードは不可能
    $c->setAllowSendFromFile(false);
    //更新対象カラム
    $c->setHiddenMainTableColumn(true);

    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(true);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    $table->addColumn($c);

    /////////////////////////////////////////////////////
    // 変数名(更新メニュー用)
    /////////////////////////////////////////////////////
    $c = new TextColumn('DISP_VARS_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705060"));//エクセル・ヘッダでの説明

    //更新対象外カラム
    $c->setHiddenMainTableColumn(false); 

    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(true);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // 入力禁止設定
    $c->setOutputType('update_table'  , new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('',true)));
    $table->addColumn($c);


    /////////////////////////////////////////////////////
    // メンバー変数名(更新メニュー用)
    /////////////////////////////////////////////////////
    $c = new TextColumn('DISP_VRAS_NAME_ALIAS',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705080"));//エクセル・ヘッダでの説明

    //更新対象外カラム
    $c->setHiddenMainTableColumn(false);

    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(true);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // 入力禁止設定
    $c->setOutputType('update_table'  , new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('',true)));
    $table->addColumn($c);


    /////////////////////////////////////////////////////
    // 繰返最大数
    /////////////////////////////////////////////////////
    $c = new NumColumn('MAX_COL_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1705100"));//エクセル・ヘッダでの説明

    //更新対象カラム
    $c->setHiddenMainTableColumn(true); 
    //エクセル/CSVからのアップロードは可能
    $c->setAllowSendFromFile(true);    

    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1, 999));
    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);

    // 登録/更新/廃止/復活があった場合、データベースを更新した事をマークする。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            $strQuery = "UPDATE A_PROC_LOADED_LIST "
                       ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                       ."WHERE ROW_ID IN (2100020005) ";

            $aryForBind = array();

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

            if( $aryRetBody[0] !== true ){
                $boolRet = false;
                $strErrMsg = $aryRetBody[2];
                $intErrorType = 500;
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MAX_COL_SEQ_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('VARS_NAME_ID','ARRAY_MEMBER_ID'));
//tail of setting [multi-set-unique]----

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
    //廃止・復活ボタンを隠す
    $outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
    $tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
