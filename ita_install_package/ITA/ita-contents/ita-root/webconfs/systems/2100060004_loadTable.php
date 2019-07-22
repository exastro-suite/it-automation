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
//    ・作業パターン詳細画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-207080");
/*
DSC作業パターン詳細
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

    $table = new TableControlAgent('B_DSC_PATTERN_LINK','LINK_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-207090"), 'B_DSC_PATTERN_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['LINK_ID']->setSequenceID('B_DSC_PATTERN_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_PATTERN_LINK_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-208010"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITADSCH-MNU-208020"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    //************************************************************************************
    //----作業パターン
    //************************************************************************************
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208030"),'E_DSC_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208040"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_DSC_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----プレイブック素材
    //************************************************************************************
    $c = new IDColumn('RESOURCE_MATTER_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208050"),'D_DSC_RESOURCE','RESOURCE_MATTER_ID','RESOURCE_MATTER_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_DSC_RESOURCE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('RESOURCE_MATTER_ID');
    $c->setJournalDispIDOfMaster('RESOURCE_MATTER_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $objFunction01 = function($in_menu_id){
        global $g;
        $retBool = false;
        $strFxName = "";
        $aryForBind = array();
        
        $strQuery = "SELECT "
                   ." TAB_1.DISUSE_FLAG  DISUSE_FLAG "
                   ."FROM "
                   ." A_ROLE_MENU_LINK_LIST TAB_1 "
                   ."WHERE "
                   ." TAB_1.MENU_ID = :MENU_ID ";

        $aryForBind['MENU_ID'] = $in_menu_id;

        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
        if( $aryRetBody[0] === true ){
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $Disuse_Flag = $row['DISUSE_FLAG'];
            }
            unset($objQuery);
            if( $Disuse_Flag === '0' ){
                $retBool = true;
            }
        }
        return $retBool;
    };

    $menu_id = 2100060016;
    if( $objFunction01($menu_id) === true ){
        //************************************************************************************
        //----PowerShell設定ファイル
        //************************************************************************************
        $c = new IDColumn('POWERSHELL_FILE_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208100"),'D_DSC_POWERSHELL_FILE','POWERSHELL_FILE_ID','POWERSHELL_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208101"));//エクセル・ヘッダでの説明
        $c->setJournalTableOfMaster('D_DSC_POWERSHELL_FILE_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('POWERSHELL_FILE_ID');
        $c->setJournalDispIDOfMaster('POWERSHELL_NAME');
        $table->addColumn($c);
    }

    $menu_id = 2100060017;
    if( $objFunction01($menu_id) === true ){
        //************************************************************************************
        //----Param設定ファイル
        //************************************************************************************
        $c = new IDColumn('PARAM_FILE_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208110"),'D_DSC_PARAM_FILE','PARAM_FILE_ID','PARAM_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208111"));//エクセル・ヘッダでの説明
        $c->setJournalTableOfMaster('D_DSC_PARAM_FILE_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('PARAM_FILE_ID');
        $c->setJournalDispIDOfMaster('PARAM_NAME');
        $table->addColumn($c);
    }

    //************************************************************************************
    //----Import設定ファイル
    //************************************************************************************
    $c = new IDColumn('IMPORT_FILE_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208120"),'D_DSC_IMPORT_FILE','IMPORT_FILE_ID','IMPORT_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208121"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_DSC_IMPORT_FILE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('IMPORT_FILE_ID');
    $c->setJournalDispIDOfMaster('IMPORT_NAME');
    $table->addColumn($c);

    //************************************************************************************
    //----ConfigData設定ファイル
    //************************************************************************************
    $c = new IDColumn('CONFIGDATA_FILE_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208130"),'D_DSC_CONFIGDATA_FILE','CONFIGDATA_FILE_ID','CONFIGDATA_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208131"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_DSC_CONFIGDATA_FILE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('CONFIGDATA_FILE_ID');
    $c->setJournalDispIDOfMaster('CONFIGDATA_NAME');
    $table->addColumn($c);

    $menu_id = 2100060020;
    if( $objFunction01($menu_id) === true ){
        //************************************************************************************
        //----CompileOption設定ファイル
        //************************************************************************************
        $c = new IDColumn('CMPOPTION_FILE_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-208140"),'D_DSC_CMPOPTION_FILE','CMPOPTION_FILE_ID','CMPOPTION_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-208141"));//エクセル・ヘッダでの説明
        $c->setJournalTableOfMaster('D_DSC_CMPOPTION_FILE_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('CMPOPTION_FILE_ID');
        $c->setJournalDispIDOfMaster('CMPOPTION_NAME');
        $table->addColumn($c);
    }
    unset($objFunction01);

//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('PATTERN_ID'));
//----------インクルード順序削除----------

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
