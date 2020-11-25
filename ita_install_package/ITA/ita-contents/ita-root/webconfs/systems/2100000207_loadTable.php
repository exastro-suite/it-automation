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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060001");

    // ロールID
    $table = new TableControlAgent('A_ROLE_LIST','ROLE_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060101"), 'A_ROLE_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060003"));
    
    $table->setAccessAuth(true);    // データごとのRBAC設定
    
    $table->setJsEventNamePrefix(true);

    $table->setGeneObject("webSetting", $arrayWebSetting);

    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['ROLE_ID']->setSequenceID('SEQ_A_ROLE_LIST');



    // ロール名称
    $c = new TextColumn('ROLE_NAME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060201"));
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060202"));
    $c->setValidator(new SingleTextValidator(1, 256, false));
    $table->addColumn($c);


    // ユーザ情報
    $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060301");
    $c = new LinkButtonColumn('UserInfo',$strLabelText, $strLabelText, 'disp_user_list', array(':ROLE_ID')); 
    $c->setDBColumn(false);
    $table->addColumn($c);

    // メニュー情報
    $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060401");
    $c = new LinkButtonColumn('MenuInfo',$strLabelText, $strLabelText, 'disp_menu_list', array(':ROLE_ID')); 
    $c->setDBColumn(false);
    $table->addColumn($c);

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
        global $g;
        global $root_dir_path;
        $retBool = true;
        $retStrBody = '';
        $boolExecuteContinue = true;
        $boolSystemErrorFlag = false;

        $strModeId = "";
        $modeValue_sub = "";

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
            
        if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $RoleName = array_key_exists('ROLE_NAME',$arrayRegData)?$arrayRegData['ROLE_NAME']:null;
        } else {
            $boolExecuteContinue = false;
        }
        
        if($boolExecuteContinue === true && $boolSystemErrorFlag === false) {
            // ロール名にカンマが含まれていないかチェック
            $ret = preg_match("/,/",$RoleName);
            if($ret == 1) {
            $retBool = false;
            $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-19020");
            }
        }
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
