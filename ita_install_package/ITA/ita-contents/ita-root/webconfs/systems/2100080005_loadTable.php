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
//    ・Module素材集画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102910");
/*
Module素材集
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

    $table = new TableControlAgent('B_TERRAFORM_MODULE','MODULE_MATTER_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102920"), 'B_TERRAFORM_MODULE_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MODULE_MATTER_ID']->setSequenceID('B_TERRAFORM_MODULE_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_MODULE_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102930"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102940"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //************************************************************************************
    //----Module素材名
    //************************************************************************************
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('MODULE_MATTER_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102950"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102960"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    //************************************************************************************
    //----Module素材
    //************************************************************************************
    $c = new FileUploadColumn('MODULE_MATTER_FILE',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102970"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102980"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $c->setRequired(true);//登録/更新時には、入力必須 2018.05.23 Add
    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $table->addColumn($c);

    // Movement-Module紐付管理へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-107030");
    $c = new LinkButtonColumn('ethWakeOrder',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow", array(':MODULE_MATTER_NAME'), true);
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
                       ."WHERE ROW_ID in (2100080001) ";

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
    $tmpAryColumn['MODULE_MATTER_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
        global $g;
        global $root_dir_path;
        $retBool       = true;
        $retStrBody    = '';
        $intErrorType  = 0;
        $aryErrMsgBody = array();

        $strModeId = "";
        $modeValue_sub = "";

        $query = "";

        $boolExecuteContinue = true;
        $boolSystemErrorFlag = false;
        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        $tmpFile        = '';

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $tffile           = isset($arrayVariant['edit_target_row']['MODULE_MATTER_FILE'])?
                                       $arrayVariant['edit_target_row']['MODULE_MATTER_FILE']:null;

        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){

            $tmpFile      = array_key_exists('tmp_file_COL_IDSOP_10',$arrayRegData)?
                               $arrayRegData['tmp_file_COL_IDSOP_10']:null;
            $strTempFileFullname = $root_dir_path . "/temp/file_up_column/" . $tmpFile;

            $tffile           = array_key_exists('MODULE_MATTER_FILE',$arrayRegData)?
                                    $arrayRegData['MODULE_MATTER_FILE']:null;
            // 空更新の場合
            if($tffile === null) {
                $tffile           = isset($arrayVariant['edit_target_row']['MODULE_MATTER_FILE'])?
                                           $arrayVariant['edit_target_row']['MODULE_MATTER_FILE']:null;
            }
        }


        if (preg_match('/\.(tf)$/i',$tffile)){
            $retBool = true;
        }else{
            $retBool = false;
            $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211800");
        }

        if($strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister"){
            if(!empty($tmpFile)){
                // tfファイルチェッククラス呼び出し
                require_once ($root_dir_path . '/libs/commonlibs/common_terraform_hcl2json_parse.php');
                $objWSRA = new CommonTerraformHCL2JSONParse($root_dir_path, $strTempFileFullname);
                // チェック関数
                $parseResult = $objWSRA->getParsedResult();

                $retBool = $parseResult["res"];
                // エラーの場合はエラー文の取得
                if (!$retBool) {
                    $retStrBody = $parseResult["err"];
                }
                unset($objWSRA);

                // システムエラーではない
                $boolSystemErrorFlag = false;
            };
        };

        if( $boolSystemErrorFlag === true ){
            $retBool = false;
            //----システムエラー
            $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
        }
        if($retBool===false){
            $objClientValidator->setValidRule($retStrBody);
        }

        return $retBool;

    };
    $objVarVali = new VariableValidator();
    $objVarVali->setErrShowPrefix(false);
    $objVarVali->setFunctionForIsValid($objFunction);
    $objVarVali->setVariantForIsValid(array());

    $objLU4UColumn->addValidator($objVarVali);
    //組み合わせバリデータ----


    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>