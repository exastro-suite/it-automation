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
require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100150001/material_web_functions.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100801");

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

    $table = new TableControlAgent('F_DIR_MASTER','DIR_ID', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100802"), 'F_DIR_MASTER_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['DIR_ID']->setSequenceID('F_DIR_MASTER_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_DIR_MASTER_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100803"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100804"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    //////////////////////////////////////
    // 親ディレクトリ
    //////////////////////////////////////
    $c = new IDColumn('PARENT_DIR_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100805"),'F_DIR_MASTER','DIR_ID','DIR_NAME_FULLPATH','',array('OrderByThirdColumn'=>'DIR_NAME_FULLPATH'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100806"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $objVldt = new parentDirValidator($c);
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // ディレクトリ
    //////////////////////////////////////
    $objVldt = new dirValidator(1, 128, false);
    $c = new TextColumn('DIR_NAME',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100807"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100808"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setDeleteOnBeforeCheck(true);
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // ディレクトリ
    //////////////////////////////////////
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $aryDataSet = array();

        // ディレクトリのフルパスを設定
        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if($modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete"){

            $strQuery = "SELECT "
                       ." DIR_NAME_FULLPATH "
                       ."FROM "
                       ." F_DIR_MASTER "
                       ."WHERE "
                       ." DISUSE_FLAG IN ('0') "
                       ." AND DIR_ID = :DIR_ID ";

            $aryForBind['DIR_ID'] = $reqOrgData['PARENT_DIR_ID'];

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, "");
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
            }

            if(0 < count($aryDataSet)){
                $exeQueryData[$objColumn->getID()] = $aryDataSet[0]['DIR_NAME_FULLPATH'] . $reqOrgData['DIR_NAME'] . "/";
                $reqOrgData[$objColumn->getID()] = $aryDataSet[0]['DIR_NAME_FULLPATH'] . $reqOrgData['DIR_NAME'] . "/";
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    $c = new TextColumn('DIR_NAME_FULLPATH',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100807"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100808"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(false);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    $table->addColumn($c);

    //////////////////////////////////////
    // 権限
    //////////////////////////////////////
    $objVldt = new SingleTextValidator(3,3,false);
    $objVldt->setRegexp('/^[0-7][0-7][0-7]$/');
    $c = new TextColumn('CHMOD',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100809"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100810"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // グループ
    //////////////////////////////////////
    $objVldt = new SingleTextValidator(1,128,false);
    $objVldt->setRegexp('/^[a-zA-Z0-9]+$/');
    $c = new TextColumn('GROUP_AUTH',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100811"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100812"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // ユーザ
    //////////////////////////////////////
    $objVldt = new SingleTextValidator(1,128,false);
    $objVldt->setRegexp('/^[a-zA-Z0-9]+$/');
    $c = new TextColumn('USER_AUTH',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100813"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100814"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // 用途
    //////////////////////////////////////
    $objVldt = new MultiTextValidator(0,4000,false);
    $c = new MultiTextColumn('DIR_USAGE',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100815"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100816"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);



//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    //----埋め込み関数定義
    $objTemp01Function = function($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){

        return updateGitDir($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);
    };
    $objTemp02Function = function($intBaseMode, $strNumberForRI, $reqDeleteData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){

        return deleteGitDir($intBaseMode, $strNumberForRI, $reqDeleteData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);
    };
    //埋め込み関数定義----

    $table->setGeneObject("functionsForOverride", array("updateTableMain"=>array("update_table"=>array("afterUpdate"=>$objTemp01Function)),
                                                        "deleteTableMain"=>array("delete_table"=>array("afterUpdate"=>$objTemp02Function)),
                                                       )
                         );

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>