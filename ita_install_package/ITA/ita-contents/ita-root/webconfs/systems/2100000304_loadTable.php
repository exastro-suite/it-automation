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
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] =  $g['objMTS']->getSomeMessage("ITABASEH-MNU-104070");
/*--------↑
投入オペレーション一覧情報
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

    //オペレーションID
    $table = new TableControlAgent('C_OPERATION_LIST','OPERATION_NO_UAPK', $g['objMTS']->getSomeMessage("ITABASEH-MNU-104080"), 'C_OPERATION_LIST_JNL', $tmpAry); //No.IDentifyByMachine

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['OPERATION_NO_UAPK']->setSequenceID('C_OPERATION_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_OPERATION_LIST_JSQ');
    $table->setJsEventNamePrefix(true);
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-104090"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-105010"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $c = new AutoNumRegisterColumn('OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITABASEH-MNU-105060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-105070"));
    $c->setSequenceID('C_OPERATION_LIST_ANR1');
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('OPERATION_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-105020"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-105030"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    $c = new DateTimeColumn('OPERATION_DATE', $g['objMTS']->getSomeMessage("ITABASEH-MNU-105040"),'DATETIME','DATETIME',false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-105050"));
    $c->setRequired(true);

    $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
    $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */

    $table->addColumn($c);

    $c = new DateTimeColumn('LAST_EXECUTE_TIMESTAMP', $g['objMTS']->getSomeMessage("ITABASEH-MNU-105075"),'DATETIME','DATETIME',false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-105076"));
    $c->setSecondsInputOnIU(false);                              /* UI表示時に秒を非表示       */
    $c->setSecondsInputOnFilter(false);                          /* フィルタ表示時に秒を非表示 */
    $c->setAllowSendFromFile(false);                             /* エクセル/CSVからのアップロードは不可能 */
    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);        /* 更新時は非表示 */
    $c->getOutputType('register_table')->setVisible(false);      /* 追加時は非表示 */
    $c->getOutputType('delete_table')->setVisible(false);        /* 廃止時は非表示 */
    $c->getOutputType('print_journal_table')->setVisible(true);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);
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
                       ."WHERE ROW_ID IN (2100020002,2100020004,2100020006) ";

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
    $tmpAryColumn['OPERATION_NO_UAPK']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->fixColumn();
    $tmpAryColumn = $table->getColumns();

    list($strTmpValue,$tmpKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('callType'),null);
    if( $tmpKeyExists===true ){
        if( $strTmpValue=="insConstruct" ){
            $objRadioColumn = $tmpAryColumn['WEB_BUTTON_UPDATE'];
            $objRadioColumn->setColLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-106010"));
            
            $objFunctionB = function ($objOutputType, $rowData, $aryVariant, $objColumn){
                $strInitedColId = $objColumn->getID();
                
                $aryVariant['callerClass'] = get_class($objOutputType);
                $aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
                $strRIColId = $objColumn->getTable()->getRIColumnID();
                
                $rowData[$strInitedColId] = '<input type="radio" name="opeNo" onclick="javascript:operationLoadForExecute(' . $rowData[$strRIColId] . ')"/>';
                
                return $objOutputType->getBody()->getData($rowData,$aryVariant);
            };
            
            $objTTBF = new TextTabBFmt();
            $objTTHF = new TabHFmt();//new SortedTabHFmt();
            $objTTBF->setSafingHtmlBeforePrintAgent(false);
            $objOutputType = new VariantOutputType($objTTHF, $objTTBF);
            $objOutputType->setFunctionForGetBodyTag($objFunctionB);
            $objOutputType->setVisible(true);
            $objRadioColumn->setOutputType("print_table", $objOutputType);
            
            $table->getFormatter('print_table')->setGeneValue("linkExcelHidden",true);
            $table->getFormatter('print_table')->setGeneValue("linkCSVFormShow",false);
        }
    }
    unset($tmpAryColumn);
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
