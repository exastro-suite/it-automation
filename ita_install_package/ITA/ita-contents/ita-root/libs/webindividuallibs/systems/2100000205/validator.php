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
/**
 * 【処理内容】
 *    メニュー管理の独自バリデータ
 *
 */


/**
* メニュー名専用のバリデータクラス
*/

class MenuNameValidator_2100000205 extends SingleTextValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            return false;
        }

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if( $strModeId != "" ){
            $boolCheckContinue = false;
            if($strModeId == "DTUP_singleRecRegister" ){
                //----各種登録時
                $boolCheckContinue = true;
                //各種登録時----
            }else if($strModeId == "DTUP_singleRecUpdate"){
                //----各種更新時
                $boolCheckContinue = true;
                //各種更新時----
            }else if($strModeId == "DTUP_singleRecDelete"){
                $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];
                if( $modeValue_sub=="on" ){
                    //処理をしない
                }else if( $modeValue_sub=="off" ){
                    //復活時
                    $boolCheckContinue = true;
                }
            }else{
                //処理をしない
            }

            if($boolCheckContinue===true){

                if(rtrim($arrayRegData['MENU_NAME']) === rtrim($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1100001"))){
                    // メニュー名が「メインメニュー」
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-22001");
                }
            }

            if($boolCheckContinue===true){

                // 禁止文字チェック
                mb_regex_encoding("UTF-8");
                if( preg_match('/[\\\\\/\:\*?"<>|\[\]：￥／＊［］]+/u', $value) === 1){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-22002");
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}
