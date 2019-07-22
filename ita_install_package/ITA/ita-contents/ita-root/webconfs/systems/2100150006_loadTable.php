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
/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100150001/validator.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101101");

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

    $table = new TableControlAgent('F_MATERIAL_LINKAGE_ANS','ROW_ID', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101102"), 'F_MATERIAL_LINKAGE_ANS_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_MATERIAL_LINKAGE_ANS_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_MATERIAL_LINKAGE_ANS_JSQ');
    unset($tmpAryColumn);

    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101103"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101104"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    $c = new TextColumn('MATERIAL_LINK_NAME',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101105"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101106"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$objVldt = new LinkNameValidator(1,128,false);
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    $c = new IDColumn('FILE_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101107"),'G_FILE_MASTER','FILE_ID','FILE_NAME_FULLPATH','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101108"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setEvent('update_table', 'onchange', 'fileId_upd');
    $c->setEvent('register_table', 'onchange', 'fileId_reg');
    $table->addColumn($c);

    $c = new IDColumn('CLOSE_REVISION_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101109"),'G_FILE_MANAGEMENT_NEWEST','FILE_M_ID','CLOSE_REVISION','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101110"));//エクセル・ヘッダでの説明

    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $fileId = $aryVariant['FILE_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.FILE_M_ID       KEY_COLUMN "
                   .",TAB_1.CLOSE_REVISION  DISP_COLUMN "
                   ."FROM "
                   ." G_FILE_MANAGEMENT_NEWEST TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.FILE_ID = :FILE_ID "
                   ."ORDER BY CLOSE_DATE";

        $aryForBind['FILE_ID'] = $fileId;

        if( 0 < strlen($fileId) ){
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

    $objFunction02 = $objFunction01;

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $fileId = $rowData['FILE_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.FILE_M_ID       KEY_COLUMN "
                   .",TAB_1.CLOSE_REVISION  DISP_COLUMN "
                   ."FROM "
                   ." G_FILE_MANAGEMENT_NEWEST TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.FILE_ID = :FILE_ID "
                   ."ORDER BY CLOSE_DATE";

        $aryForBind['FILE_ID'] = $fileId;

        if( 0 < strlen($fileId) ){
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

    $objFunction04 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$varAddResultData,&$aryVariant,&$arySetting,&$aryOverride){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();
        //空白選択させる
        $boolWhiteKeyAdd = true;

        $strOptionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);

        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    };

    $objFunction05 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$rowData,$aryVariant){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

        //空白選択させる
        $boolWhiteKeyAdd = true;

        $strFxName = "";

        $aryAddResultData = array();

        $strOptionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);

        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    };

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101111");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objVarBFmtUpd->setFunctionForGetFADMainDataOverride($objFunction04);

    $objVarBFmtUpd->setFunctionForGetMainDataOverride($objFunction05);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

    $objVarBFmtReg->setFunctionForGetFADMainDataOverride($objFunction04);

    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $table->addColumn($c);

    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    unset($objFunction04);
    unset($objFunction05);

    $cg1 = new ColumnGroup($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101127"));

        $c = new IDColumn('ANS_CONTENTS_FILE_CHK',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101117"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101118"));//エクセル・ヘッダでの説明
        $objVldt = new LinkageCheckValidator($c);
        $c->setValidator($objVldt);
        $cg1->addColumn($c);
    
        $c = new IDColumn('ANS_TEMPLATE_CHK',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101115"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101116"));//エクセル・ヘッダでの説明
        $objVldt = new LinkageCheckValidator($c);
        $c->setValidator($objVldt);
        $cg1->addColumn($c);

    $table->addColumn($cg1);

    $cg2 = new ColumnGroup($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101112"));

        $c = new IDColumn('ANS_PLAYBOOK_CHK',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101113"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101114"));//エクセル・ヘッダでの説明
        $objVldt = new LinkageCheckValidator($c);
        $c->setValidator($objVldt);
        $cg2->addColumn($c);

    $table->addColumn($cg2);

    $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101119"));

        $c = new IDColumn('OS_TYPE_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101120"),'D_OS_TYPE','OS_TYPE_ID','OS_TYPE_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101121"));//エクセル・ヘッダでの説明
        $objVldt = new OsTypeNameValidator($c);
        $c->setValidator($objVldt);
        $cg3->addColumn($c);

        $c = new IDColumn('ANSIBLE_DIALOG_CHK',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101122"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101123"));//エクセル・ヘッダでの説明
        $objVldt = new AnsibleDialogCheckValidator($c);
        $c->setValidator($objVldt);
        $cg3->addColumn($c);

    $table->addColumn($cg3);

    $cg4 = new ColumnGroup($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101124"));

        $c = new IDColumn('ANSIBLE_ROLE_CHK',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101125"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101126"));//エクセル・ヘッダでの説明
        $objVldt = new LinkageCheckValidator($c);
        $c->setValidator($objVldt);
        $cg4->addColumn($c);

    $table->addColumn($cg4);

//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
