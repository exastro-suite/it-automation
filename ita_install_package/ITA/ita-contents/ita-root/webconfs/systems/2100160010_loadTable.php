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

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100160001/validator.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104101");

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

    $table = new TableControlAgent('F_COL_TO_ROW_MNG','ROW_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104102"), 'F_COL_TO_ROW_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_COL_TO_ROW_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_COL_TO_ROW_MNG_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104103"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104104"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    // 変換元メニュー名
    $c = new IDColumn('FROM_MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-104105"),'D_MENU_LIST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104106"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 変換先メニュー名
    $c = new IDColumn('TO_MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-104107"),'D_MENU_LIST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104108"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 用途
    $c = new IDColumn('PURPOSE',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-104109"),'F_PARAM_PURPOSE','PURPOSE_ID','PURPOSE_NAME', '', array('OrderByThirdColumn'=>'PURPOSE_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104110"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $objVldt = new PurposeValidator($c);
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // 繰り返し開始カラム名
    $c = new TextColumn('START_COL_NAME', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104111"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104112"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $objVldt = new MenuNameValidator(1,64,false);
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    // 項目数
    $c = new NumColumn('COL_CNT',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104113"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104114"));
    $c->setSubtotalFlag(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 繰り返し数
    $c = new NumColumn('REPEAT_CNT',  $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104115"));
    $c->setDescription( $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104116"));
    $c->setSubtotalFlag(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){
            $exeQueryData[$objColumn->getID()] = "0";
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    // 縦横変換済みフラグ
    $c = new TextColumn('CHANGED_FLG', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104117"));
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-104118"));
    $c->getOutputType("print_journal_table")->setVisible(false);
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    $table->addColumn($c);

//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>