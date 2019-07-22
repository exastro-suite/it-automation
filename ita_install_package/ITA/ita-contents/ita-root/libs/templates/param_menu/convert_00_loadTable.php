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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = '★★★INFO★★★';

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

    $table = new TableControlAgent('G_★★★TABLE★★★_CONV','ROW_ID', 'No', 'G_★★★TABLE★★★_CONV_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_★★★TABLE★★★_CONV_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_★★★TABLE★★★_CONV_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_★★★TABLE★★★_CONV');
    $table->setDBJournalTableHiddenID('F_★★★TABLE★★★_CONV_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // マルチユニーク制約
    $table->addUniqueColumnSet(array('KY_KEY','OPERATION_ID','INPUT_ORDER'));

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel('★★★MENU★★★');
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', '★★★MENU★★★');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    // 検索機能の制御----

    $c = new IDColumn('KY_KEY',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102601") . "/" . $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102602"),'G_UQ_HOST_LIST','KY_KEY','KY_VALUE','');
    $c->setDescription('choose hostgroup or host');//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102603"));

        // オペレーションID
        $c = new NumColumn('OPERATION_ID_DISP',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102606"));
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102607"));
        $c->setSubtotalFlag(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $cg->addColumn($c);

        // オペレーション名
        $c = new TextColumn('OPERATION_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102608"));
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102609"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $cg->addColumn($c);

        // 基準日
        $c = new DateTimeColumn('BASE_TIMESTAMP', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102615") ,'DATETIME','DATETIME',false);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102616"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $cg->addColumn($c);

        // 実施予定日
        $c = new DateTimeColumn('OPERATION_DATE', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102604"),'DATETIME','DATETIME',false);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102605"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $cg->addColumn($c);

        // 最終実行日
        $c = new DateTimeColumn('LAST_EXECUTE_TIMESTAMP', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102617"),'DATETIME','DATETIME',false);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102618"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $cg->addColumn($c);

        // オペレーションID：オペレーション名
        $c = new IDColumn('OPERATION_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102610"), 'G_OPERATION_LIST', 'OPERATION_ID', 'OPERATION_ID_N_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('OPERATION_ID_N_NAME'),'ORDER'=>'ORDER BY ADD_SELECT_1') );
        $c->setHiddenMainTableColumn(true);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102611"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("print_table")->setVisible(false);
        $c->getOutputType("print_journal_table")->setVisible(false);
        $c->setRequired(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    // 入力順序
    $c = new NumColumn('INPUT_ORDER',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102613"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102614"));
    $c->setValidator(new IntNumValidator(0, null));
    $c->setSubtotalFlag(false);
    $c->setRequired(true);
    $table->addColumn($c);

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102612"));

★★★ITEM★★★

    $table->addColumn($cg);

    $table->fixColumn();

    // 登録/更新/廃止/復活があった場合、ホストグループ分割対象の分割済みフラグとパラメータシート縦横変換管理の変換済みフラグをOFFにする
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            $inputMenuId = intval(substr(basename(__FILE__, ".php"), 0, 10));

            $strQuery = "UPDATE F_SPLIT_TARGET "
                       ."SET DIVIDED_FLG = :DIVIDED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                       ."WHERE INPUT_MENU_ID = :INPUT_MENU_ID";

            $g['objDBCA']->setQueryTime();
            $aryForBind = array('DIVIDED_FLG'=>"0", 'LAST_UPDATE_TIMESTAMP'=>$g['objDBCA']->getQueryTime(), 'INPUT_MENU_ID'=>$inputMenuId);

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] !== true ){
                $boolRet = $aryRetBody[0];
                $strErrMsg = $aryRetBody[2];
                $intErrorType = 500;
            }
            else{
                $strQuery = "UPDATE F_COL_TO_ROW_MNG "
                           ."SET CHANGED_FLG = :CHANGED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                           ."WHERE FROM_MENU_ID = :FROM_MENU_ID";

                $g['objDBCA']->setQueryTime();
                $aryForBind = array('CHANGED_FLG'=>"0", 'LAST_UPDATE_TIMESTAMP'=>$g['objDBCA']->getQueryTime(), 'FROM_MENU_ID'=>$inputMenuId);

                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] !== true ){
                    $boolRet = $aryRetBody[0];
                    $strErrMsg = $aryRetBody[2];
                    $intErrorType = 500;
                }
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn[$table->getDBTablePK()]->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>