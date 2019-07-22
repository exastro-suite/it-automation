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

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100160008/validator.php");
require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100160008/web_functions.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103601");

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

    $table = new TableControlAgent('F_COLUMN_GROUP','COL_GROUP_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103602"), 'F_COLUMN_GROUP_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['COL_GROUP_ID']->setSequenceID('F_COLUMN_GROUP_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_COLUMN_GROUP_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103603"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103604"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    //////////////////////////////////////
    // 親カラムグループ
    //////////////////////////////////////
    $c = new IDColumn('PA_COL_GROUP_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103605"),'F_COLUMN_GROUP','COL_GROUP_ID','FULL_COL_GROUP_NAME','',array('OrderByThirdColumn'=>'FULL_COL_GROUP_NAME'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103606"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $objVldt = new PaColGroupIdValidator($c);
    $c->setValidator($objVldt);
    $table->addColumn($c);

    //////////////////////////////////////
    // カラムグループ名
    //////////////////////////////////////
    $objVldt = new ColGroupNameValidator(0, 256, false);
    $c = new TextColumn('COL_GROUP_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103607"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103608"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setDeleteOnBeforeCheck(true);
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //////////////////////////////////////
    // カラムグループ名
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

            if(array_key_exists('PA_COL_GROUP_ID', $reqOrgData)){

                $strQuery = "SELECT "
                           ." FULL_COL_GROUP_NAME "
                           ."FROM "
                           ." F_COLUMN_GROUP "
                           ."WHERE "
                           ." DISUSE_FLAG IN ('0') "
                           ." AND COL_GROUP_ID = :COL_GROUP_ID ";

                $aryForBind['COL_GROUP_ID'] = $reqOrgData['PA_COL_GROUP_ID'];

                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, "");
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];
                    while($row = $objQuery->resultFetch() ){
                        $aryDataSet[]= $row;
                    }
                    unset($objQuery);
                }

                if(0 < count($aryDataSet)){
                    $exeQueryData[$objColumn->getID()] = $aryDataSet[0]['FULL_COL_GROUP_NAME'] . "/" . $reqOrgData['COL_GROUP_NAME'];
                    $reqOrgData[$objColumn->getID()] = $aryDataSet[0]['FULL_COL_GROUP_NAME'] . "/" . $reqOrgData['COL_GROUP_NAME'];
                }
                else{
                    $exeQueryData[$objColumn->getID()] = $reqOrgData['COL_GROUP_NAME'];
                    $reqOrgData[$objColumn->getID()] = $reqOrgData['COL_GROUP_NAME'];
                }
            }
            else{
                $exeQueryData[$objColumn->getID()] = $reqOrgData['COL_GROUP_NAME'];
                $reqOrgData[$objColumn->getID()] = $reqOrgData['COL_GROUP_NAME'];
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    $c = new TextColumn('FULL_COL_GROUP_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103609"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103610"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(false);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    $table->addColumn($c);


//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('PA_COL_GROUP_ID', 'COL_GROUP_NAME'));
//tail of setting [multi-set-unique]----


    $table->fixColumn();

    //----埋め込み関数定義
    $objTemp01Function = function($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){

        return updateOtherData($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting);
    };
    //埋め込み関数定義----

    $table->setGeneObject("functionsForOverride", array("updateTableMain"=>array("update_table"=>array("afterUpdate"=>$objTemp01Function)),
                                                       )
                         );

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>