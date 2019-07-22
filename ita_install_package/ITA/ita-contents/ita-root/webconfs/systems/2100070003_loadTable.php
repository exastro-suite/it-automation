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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-130000");
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

    $table = new TableControlAgent('B_OPENST_VARS_ASSIGN','ASSIGN_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130010"), 'B_OPENST_VARS_ASSIGN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setSequenceID('B_OPENST_VARS_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_OPENST_VARS_ASSIGN_JSQ');
    unset($tmpAryColumn);

    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130020"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130030"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    //----オペレーション
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130040"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130050"));//エクセル・ヘッダでの説明

    $c->setEvent('update_table', 'onchange', 'operation_upd');
    $c->setEvent('register_table', 'onchange', 'operation_reg');

    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //オペレーション----



    //----作業パターン
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130060"),'E_OPENST_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130070"));//エクセル・ヘッダでの説明

    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $aryVariant['OPERATION_NO_UAPK'];

        $strQuery = "SELECT PATTERN_ID KEY_COLUMN, PATTERN DISP_COLUMN FROM E_OPENST_PATTERN WHERE DISUSE_FLAG = 0";
        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;

        if( 0 < strlen($strOperationNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $retArray;
    };

    $objFunction02 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $aryVariant['OPERATION_NO_UAPK'];

        $strQuery = "SELECT PATTERN_ID KEY_COLUMN, PATTERN DISP_COLUMN FROM E_OPENST_PATTERN WHERE DISUSE_FLAG = 0";
        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;
        
        if( 0 < strlen($strOperationNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $retArray;
    };
    
    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $rowData['OPERATION_NO_UAPK'];

        $strQuery = "SELECT PATTERN_ID KEY_COLUMN, PATTERN DISP_COLUMN FROM E_OPENST_PATTERN WHERE DISUSE_FLAG = 0";

        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;

        if( 0 < strlen($strOperationNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
        return $aryRetBody;
    };

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-130080");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setFADJsEvent('onChange','pattern_upd');
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADJsEvent('onChange','pattern_reg');
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setEvent('update_table','onChange','pattern_upd',array());

    $c->setJournalTableOfMaster('E_OPENST_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //作業パターン----



    //----ホスト
   $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-120030"),'B_OPENST_PROJECT_INFO','OPENST_PROJECT_ID','OPENST_PROJECT_NAME','',array('OrderByThirdColumn'=>'OPENST_PROJECT_ID'));


    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130100"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setVisible(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ホスト----



    //変数名----

    $objVldt = new SingleTextValidator(1,4000,false);
    $c = new TextColumn('VARS_ENTRY',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130120"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setValidator($objVldt);
    $c->setUpdateRequireExcept(0);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $table->addColumn($c);


    // 代入順序
    $c = new NumColumn('ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-130130"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-130140"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){

        return true;

    };

    $objVarVali = new VariableValidator();
    $objVarVali->setErrShowPrefix(false);
    $objVarVali->setFunctionForIsValid($objFunction);
    $objVarVali->setVariantForIsValid(array());

    $objLU4UColumn->addValidator($objVarVali);
    //組み合わせバリデータ----
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','SYSTEM_ID'));
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
