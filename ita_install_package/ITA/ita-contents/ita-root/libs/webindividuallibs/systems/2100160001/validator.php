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
 *    メニュー作成機能の独自バリデータ
 *
 */


/**
* メニュー名専用のバリデータクラス
*/

class MenuNameValidator extends SingleTextValidator {

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
            $boolCheckContinueUpdateOnly = false;
            if($strModeId == "DTUP_singleRecRegister" ){
                //----各種登録時
                $boolCheckContinue = true;
                //各種登録時----
            }else if($strModeId == "DTUP_singleRecUpdate"){
                //----各種更新時
                $boolCheckContinue = true;
                $boolCheckContinueUpdateOnly = true;
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

            //更新時のみ。メニュー作成状態が2（作成済み）の場合、メニュー名が変更されていないことをチェック。
            if($boolCheckContinueUpdateOnly===true){
                $menuCreateFlag = $arrayVariant['edit_target_row']['MENU_CREATE_STATUS']; //1（未作成）、2（作成済み）
                $beforeMenuName = $arrayVariant['edit_target_row']['MENU_NAME'];
                $afterMenuName = $arrayRegData['MENU_NAME'];
                if($menuCreateFlag == 2){
                    if($beforeMenuName != $afterMenuName){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1173");
                    }
                }
            }

            if($boolCheckContinue===true){

                if(rtrim($arrayRegData['MENU_NAME']) === rtrim($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1100001"))){
                    // メニュー名が「メインメニュー」
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1001");
                }
            }

            if($boolCheckContinue===true){

                // 禁止文字チェック
                mb_regex_encoding("UTF-8");
                if( preg_match('/[\\\\\/\:\*?"<>|\[\]：￥／＊［］]+/u', $value) === 1){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1020");
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}

/**
* 作成対象専用のバリデータクラス
*/

class SubstitutionValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;
        
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
                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                // 用途を取得
                $purpose = array_key_exists('PURPOSE',$arrayRegData)?$arrayRegData['PURPOSE']:null;
                // 入力用メニューグループを取得
                $menugroupForInput= array_key_exists('MENUGROUP_FOR_INPUT',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_INPUT']:null;
                // 代入値自動登録用メニューグループを取得
                $menugroupForSubst= array_key_exists('MENUGROUP_FOR_SUBST',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_SUBST']:null;
                // 参照用メニューグループを取得
                $menugroupForView= array_key_exists('MENUGROUP_FOR_VIEW',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_VIEW']:null;

                // 作成対象で「データシート」を選択
                if("2" == $value){  
                    // 用途が選択されている場合、エラー
                    if($purpose){  
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1026");
                    }
                    // 代入値自動登録用メニューグループ、参照用メニューグループが選択されている場合、エラー
                    else if($menugroupForSubst || $menugroupForView){  
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1028");
                    }
                }
                // 作成対象で「パラメータシート(ホスト/オペレーションあり)」を選択
                else if("1" == $value){ 
                    //用途が未選択の場合、エラー
                    if(!$purpose){ 
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1022");
                    } 
                    // 代入値自動登録用メニューグループ、または参照用メニューグループが設定されていない場合、エラー
                    else if(!$menugroupForSubst || !$menugroupForView){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1023");
                    }
                } 
                // 作成対象で「パラメータシート(オペレーションあり)」を選択
                else if("3" == $value){
                    //用途が選択されている場合、エラー
                    if($purpose){ 
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1112");
                    } 
                    // 代入値自動登録用メニューグループ、または参照用メニューグループが設定されていない場合、エラー
                    else if(!$menugroupForSubst || !$menugroupForView){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1113");
                    } 
                }

                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 用途専用のバリデータクラス
*/

class PurposeValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                  // 作成対象を取得
                $target = array_key_exists('TARGET',$arrayRegData)?$arrayRegData['TARGET']:null;

                // 作成対象が「パラメータシート(ホスト/オペレーションあり)」選択時のみ
                if("1" == $target){
                    // 用途がホストグループ用の場合
                    if("2" == $value){

                        // ホストグループ機能がインストールされていない場合、エラー
                        if(!file_exists("{$g['root_dir_path']}/libs/release/ita_hostgroup")){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1003");
                        }
                    }
                }
                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 縦メニュー利用専用のバリデータクラス
*/

class VerticalValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                  // 作成対象を取得
                $target = array_key_exists('TARGET',$arrayRegData)?$arrayRegData['TARGET']:null;

                // 作成対象で「データシート」を選択
                if("2" == $value){  

                    //縦メニュー利用が設定されている場合、エラー
                    if($value){ 
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1027");
                    } 
                }
                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}


/**
* 代入値自動登録用メニューグループ専用のバリデータクラス
*/

class MgForSubstValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                  // 作成対象を取得
                $target = array_key_exists('TARGET',$arrayRegData)?$arrayRegData['TARGET']:null;

                // 作成対象が「パラメータシート(ホスト/オペレーションあり)」、「パラメータシート(オペレーションあり)」選択時のみ
                if("1" == $target || "3" == $target){

                    // 入力用メニューグループを取得
                    $menugroupForInput= array_key_exists('MENUGROUP_FOR_INPUT',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_INPUT']:null;
                    // 代入値自動登録用メニューグループを取得
                    $menugroupForSubst= array_key_exists('MENUGROUP_FOR_SUBST',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_SUBST']:null;
                    // 参照用メニューグループを取得
                    $menugroupForView= array_key_exists('MENUGROUP_FOR_VIEW',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_VIEW']:null;

                    // 他のメニューグループと同じ場合、エラー
                    if($value && ($value == $menugroupForInput || $value == $menugroupForView)){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1005");
                    }
                }
                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 参照用メニューグループ専用のバリデータクラス
*/

class MgForViewValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                // 値が設定されている場合
                if("" != $value){

                    // 入力用メニューグループを取得
                    $menugroupForInput= array_key_exists('MENUGROUP_FOR_INPUT',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_INPUT']:null;
                    // 代入値自動登録用メニューグループを取得
                    $menugroupForSubst= array_key_exists('MENUGROUP_FOR_SUBST',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_SUBST']:null;
                    // 参照用メニューグループを取得
                    $menugroupForView= array_key_exists('MENUGROUP_FOR_VIEW',$arrayRegData)?$arrayRegData['MENUGROUP_FOR_VIEW']:null;

                    // 他のメニューグループと同じ場合、エラー
                    if($value == $menugroupForInput || $value == $menugroupForSubst){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1005");
                    }
                }
                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 入力方式専用のバリデータクラス
*/

class InputMethodValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $strErrAddMsg = "";

                // 入力方式が文字列(単一行)の場合
                if("1" == $value){
                    // 文字列(単一行)最大バイト数が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('MAX_LENGTH',$arrayRegData) || "" == $arrayRegData['MAX_LENGTH'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1009");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1034");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1035");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1037");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1036");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1039");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1038");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1040");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1010");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1094");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1114");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1134");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1160");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1180");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1181");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1182");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1183");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1184");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1185");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1186");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1283");
                    }
                }
                // 入力方式が文字列(複数行)の場合
                else if("2" == $value){
                    // 文字列(複数行)最大バイト数が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) || "" == $arrayRegData['MULTI_MAX_LENGTH'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1065");
                    }
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1066");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1067");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1070");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1069");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1072");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1071");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1073");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1068");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1095");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1115");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1135");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1161");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1187");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1188");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1188");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1190");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1191");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1192");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1193");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1284");
                    }
                }
                // 入力方式が整数の場合
                else if("3" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1048");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1049");
                    } 
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1050");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1051");
                    }
                    // 整数最小値が設定されていない場合、一時最小値にする
                    if($retBool == true && (!array_key_exists('INT_MIN',$arrayRegData) || "" == $arrayRegData['INT_MIN'])){
                        $arrayRegData['INT_MIN'] = "-2147483648";
                    }
                    // 整数最大値が設定されていない場合、一時最大値にする(最大値>最小値チェックの時エラー出ないため)
                    if($retBool == true && (!array_key_exists('INT_MAX',$arrayRegData) || "" == $arrayRegData['INT_MAX'])){
                        $arrayRegData['INT_MAX'] = "2147483648";
                    }
                     // 最大値<最小値になっている場合、エラー
                    if($retBool == true && $arrayRegData['INT_MIN'] > $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1052",[$arrayRegData['INT_MIN'],$arrayRegData['INT_MAX']]);
                    }
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1053");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1054");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1055");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1056");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1096");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1116");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1136");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1162");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1194");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1195");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1196");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1197");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1198");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1199");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1200");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1285");
                    }
                }
                // 入力方式が小数の場合
                else if("4" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1057");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1058");
                    } 
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1059");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1060");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1061");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1062");
                    } 
                    // 小数最小値が設定されていない場合、一時最小値にする
                    if($retBool == true && (!array_key_exists('FLOAT_MIN',$arrayRegData) || "" == $arrayRegData['FLOAT_MIN'])){
                        $arrayRegData['FLOAT_MIN'] = "-99999999999999";
                    }
                    // 小数最大値が設定されていない場合、一時最大値にする
                    if($retBool == true && (!array_key_exists('FLOAT_MAX',$arrayRegData) || "" == $arrayRegData['FLOAT_MAX'])){
                        $arrayRegData['FLOAT_MAX'] = "99999999999999";
                    }
                    // 最大値<最小値になっている場合、エラー
                    if($retBool == true && $arrayRegData['FLOAT_MIN'] > $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1063",[$arrayRegData['FLOAT_MIN'],$arrayRegData['FLOAT_MAX']]);
                    } 
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1064");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1097");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1117");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1137");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1163");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1201");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1202");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1203");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1204");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1205");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1206");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1207");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1286");
                    }
                }
                // 入力方式が日時の場合
                else if("5" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1074");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1075");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1076");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1077");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1078");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1079");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1080");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1081");
                    }
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1082");
                    } 
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1083");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1098");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1118");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1138");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1164");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1208");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1209");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1210");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1211");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1212");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1213");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1214");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1287");
                    }
                }
                // 入力方式が日付の場合
                else if("6" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1084");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1085");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1086");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1087");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1088");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1089");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1090");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1091");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1092");
                    } 
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1093");
                    } 
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1099");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1119");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1139");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1165");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1215");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1216");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1217");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1218");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1219");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1220");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1221");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1288");
                    }
                }
                // 入力方式がプルダウンの場合
                else if("7" == $value){
                    // 最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1011");
                    }
                    // 正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1012");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1041");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1042");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1044");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1043");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1046");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1045");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1047");
                    }
                    // メニューグループ：メニュー：項目が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) || "" == $arrayRegData['OTHER_MENU_LINK_ID'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1013");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1100");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1120");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1140");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1222");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1223");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1224");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1225");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1226");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1227");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1228");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1289");
                    }
                }
                // 入力方式がパスワードの場合
                else if("8" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1102");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1103");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1104");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1105");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1106");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1107");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1108");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1109");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1110");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1111");
                    }
                    // パスワード最大バイト数が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('PW_MAX_LENGTH',$arrayRegData) || "" == $arrayRegData['PW_MAX_LENGTH'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1101");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1121");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1141");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1166");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1229");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1230");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1231");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1232");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1233");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1234");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1235");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1236");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1290");
                    }
                }
                // 入力方式がファイルアップロードの場合
                else if("9" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1123");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1124");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1125");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1126");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1127");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1128");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1129");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1130");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1131");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1132");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1133");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) || "" == $arrayRegData['UPLOAD_MAX_SIZE'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1122");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1142");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1167");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1237");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1238");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1239");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1240");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1241");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1242");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1243");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1244");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1291");
                    }
                }
                // 入力方式がリンクの場合
                else if("10" == $value){
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1144");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1145");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1146");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1147");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1148");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1149");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1150");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1151");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1152");
                    }
                    // メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1153");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1154");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1155");
                    }
                    // リンク/最大バイト数が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('LINK_LENGTH',$arrayRegData) || "" == $arrayRegData['LINK_LENGTH'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1143");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1168");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1245");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1246");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1247");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1248");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1249");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1250");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1251");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('TYPE3_REFERENCE',$arrayRegData) && "" != $arrayRegData['TYPE3_REFERENCE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1292");
                    }
                }
                // 入力方式がパラメータシート参照の場合
                else if("11" == $value){
                    // 必須が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REQUIRED',$arrayRegData) && "" != $arrayRegData['REQUIRED']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1281");
                    }
                    // 一意制約が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UNIQUED',$arrayRegData) && "" != $arrayRegData['UNIQUED']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1282");
                    }
                    // パラメータシート参照/メニューグループ：メニュー：項目が設定されていない場合、エラー
                    if($retBool == true && (!array_key_exists('TYPE3_REFERENCE',$arrayRegData) || "" == $arrayRegData['TYPE3_REFERENCE'])){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1258");
                    }
                    // 文字列(単一行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1259");
                    }
                    // 文字列(単一行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PREG_MATCH',$arrayRegData) && "" != $arrayRegData['PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1260");
                    }
                    // 文字列(複数行)最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['MULTI_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1261");
                    }
                    // 文字列(複数行)正規表現が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_PREG_MATCH',$arrayRegData) && "" != $arrayRegData['MULTI_PREG_MATCH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1262");
                    }
                    // 整数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MIN',$arrayRegData) && "" != $arrayRegData['INT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1263");
                    } 
                    // 整数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_MAX',$arrayRegData) && "" != $arrayRegData['INT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1264");
                    } 
                    // 小数最小値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MIN',$arrayRegData) && "" != $arrayRegData['FLOAT_MIN']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1266");
                    } 
                    // 小数最大値が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_MAX',$arrayRegData) && "" != $arrayRegData['FLOAT_MAX']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1265");
                    } 
                    // 小数桁数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DIGIT',$arrayRegData) && "" != $arrayRegData['FLOAT_DIGIT']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1267");
                    }
                    // プルダウン選択/メニューグループ：メニュー：項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) && "" != $arrayRegData['OTHER_MENU_LINK_ID']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1268");
                    }
                    // パスワード最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PW_MAX_LENGTH',$arrayRegData) && "" != $arrayRegData['PW_MAX_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1269");
                    }
                    // ファイルアップロード/ファイル最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('UPLOAD_MAX_SIZE',$arrayRegData) && "" != $arrayRegData['UPLOAD_MAX_SIZE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1270");
                    }
                    // リンク/最大バイト数が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_LENGTH',$arrayRegData) && "" != $arrayRegData['LINK_LENGTH']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1271");
                    }
                    // 参照項目が設定されている場合、エラー
                    if($retBool == true && array_key_exists('REFERENCE_ITEM',$arrayRegData) && "" != $arrayRegData['REFERENCE_ITEM']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1168");
                    }
                    // 初期値(文字列(単一行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('SINGLE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['SINGLE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1273");
                    }
                    // 初期値(文字列(複数行))が設定されている場合、エラー
                    if($retBool == true && array_key_exists('MULTI_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['MULTI_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1274");
                    }
                    // 初期値(整数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('INT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['INT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1275");
                    }
                    // 初期値(小数)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('FLOAT_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['FLOAT_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1276");
                    }
                    // 初期値(日時)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATETIME_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATETIME_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1277");
                    }
                    // 初期値(日付)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('DATE_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['DATE_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1278");
                    }
                    // 初期値(リンク)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('LINK_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['LINK_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1279");
                    }
                    // 初期値(プルダウン選択)が設定されている場合、エラー
                    if($retBool == true && array_key_exists('PULLDOWN_DEFAULT_VALUE',$arrayRegData) && "" != $arrayRegData['PULLDOWN_DEFAULT_VALUE']){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1280");
                    }
                }
                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 対象メニュー名:開始項目名専用のバリデータクラス
*/

class StartCreateItemValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $retBool = true;
        $strModeId = "";
        $modeValue_sub = "";
        $boolCheckContinue = true;

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

                $this->strModeIdOfLastErr = $strModeId;
                $this->strErrAddMsg = "";

                $query01 = "SELECT CONVERT_PARAM_ID "
                            ." FROM F_CONVERT_PARAM_INFO "
                            ." WHERE CREATE_ITEM_ID IN "
                            ." (SELECT CREATE_ITEM_ID FROM G_CREATE_ITEM_INFO WHERE DISUSE_FLAG = '0' AND "
                            ."                                                      CREATE_MENU_ID = "
                            ."                                                      (SELECT CREATE_MENU_ID FROM G_CREATE_ITEM_INFO "
                            ."                                                       WHERE DISUSE_FLAG = '0' AND CREATE_ITEM_ID=:CREATE_ITEM_ID)) "
                            ." AND DISUSE_FLAG = '0' "
                            ." AND CONVERT_PARAM_ID <> :CONVERT_PARAM_ID ";

                if(array_key_exists('CONVERT_PARAM_ID',$arrayVariant['edit_target_row'])){
                    $aryForBind01['CONVERT_PARAM_ID'] = $arrayVariant['edit_target_row']['CONVERT_PARAM_ID'];
                }
                else{
                    $aryForBind01['CONVERT_PARAM_ID'] = 0;
                }
                $aryForBind01['CREATE_ITEM_ID'] = $arrayRegData['CREATE_ITEM_ID'];

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "");

                if( $retArray01[0] === true ){
                    $objQuery01 =& $retArray01[1];
                    $intCount01 = 0;
                    $aryDiscover01 = array();
                    while($row01 = $objQuery01->resultFetch()){
                        $intCount01 += 1;
                        $aryDiscover01[] = $row01;
                    }
                    unset($objQuery01);

                    if($intCount01 > 0){
                        // 最大バイト数の上限オーバー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1019", $aryDiscover01[0]['CONVERT_PARAM_ID']);
                    }
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1007", $retArray01[2]);
                }

                if( $retBool === false ){
                    $this->setValidRule($strErrAddMsg);
                }
            }
        }
        return $retBool;
    }
}

/**
* 正規表現専用のバリデータクラス
*/

class PregMatchValidator extends SingleTextValidator {

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

                // 正規表現が不正な文法
                if("" != $value && false === @preg_match($value, "")){
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1021");
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}

/**
* 参照項目専用のバリデータクラス
*/

class ReferenceItemValidator extends SingleTextValidator {

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

            //入力方式が「プルダウン選択」以外、もしくは値が空ならチェックをしない
            if(!array_key_exists('INPUT_METHOD_ID',$arrayRegData) || $arrayRegData['INPUT_METHOD_ID'] != 7 || $value == ""){
                return true;
            }

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
                if(!empty($arrayRegData['OTHER_MENU_LINK_ID'])){
                    //プルダウン選択のIDを取得
                    $otherMenuLinkId = $arrayRegData['OTHER_MENU_LINK_ID']; 
                }else{
                    //プルダウン選択がないのでエラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1169");
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }

                //$valueがカンマ区切りの数字であることをチェック
                $aryReferenceItem = explode(',', $value);
                foreach($aryReferenceItem as $id){
                    if(!is_numeric($id)){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1170");
                        $this->setValidRule($strErrAddMsg);
                        return $retBool;
                    }
                }

                //プルダウン選択のIDから対象のメニューIDを取得
                $query01 = "SELECT LINK_ID, MENU_ID "
                            ." FROM G_OTHER_MENU_LINK "
                            ." WHERE DISUSE_FLAG = '0' AND LINK_ID = :LINK_ID ";

                $aryForBind01['LINK_ID'] = $otherMenuLinkId;

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "");
                $aryDiscover01 = array();
                if( $retArray01[0] === true ){
                    $objQuery01 =& $retArray01[1];
                    while($row01 = $objQuery01->resultFetch()){
                        $aryDiscover01[] = $row01;
                    }
                    unset($objQuery01);
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1171", $retArray01[2]);
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }
                //メニューIDを取得
                $menuId = "";
                if(!empty($aryDiscover01)){
                    $menuId = $aryDiscover01[0]['MENU_ID'];
                }else{
                    // エラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1175");
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }


                //参照項目リストを取得
                $query02 = "SELECT ITEM_ID, LINK_ID, MENU_ID, ORIGINAL_MENU_FLAG "
                            ." FROM G_MENU_REFERENCE_ITEM "
                            ." WHERE DISUSE_FLAG = '0' AND MENU_ID = :MENU_ID ";

                $aryForBind02['MENU_ID'] = $menuId;

                // SQL発行
                $retArray02 = singleSQLExecuteAgent($query02, $aryForBind02, "");
                $aryDiscover02 = array();
                if( $retArray02[0] === true ){
                    $objQuery02 =& $retArray02[1];
                    while($row02 = $objQuery02->resultFetch()){
                        $aryDiscover02[] = $row02;
                    }
                    unset($objQuery02);
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1171", $retArray02[2]);
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }

                //参照項目リストから、プルダウン選択のメニューの中に参照項目のIDが存在するかをチェック
                foreach($aryReferenceItem as $id){
                    $checkFlg = false;
                    foreach($aryDiscover02 as $data){
                        if($data['ORIGINAL_MENU_FLAG'] == 1){
                            //既存メニューの場合、LINK_IDが一致している場合のみ許可
                            if($data['LINK_ID'] == $otherMenuLinkId){
                                if($data['ITEM_ID'] == $id){
                                    $checkFlg = true;
                                    break;
                                } 
                            }
                        }else{
                            if($data['ITEM_ID'] == $id){
                                $checkFlg = true;
                                break;
                            } 
                        }

                    }
                    if($checkFlg == false){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1172");
                    }
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}

/**
* 項目名専用のバリデータクラス（「/」を含んでいる場合はエラー）
*/

class ItemNameValidator extends SingleTextValidator {

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
                //「/」を含んでいる場合はエラー
                if(preg_match("/\//", $value)){
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1174");
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }
            }
        }

        return $retBool;
    }
}

/**
* 一意制約管理専用のバリデータクラス
*/

class UniqueConstraintValidator extends SingleTextValidator {

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
                if(!empty($arrayRegData['CREATE_MENU_ID'])){
                    //メニューIDを取得
                    $createMenuId = $arrayRegData['CREATE_MENU_ID']; 
                }else{
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1176");
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }

                //$valueがカンマ区切りの数字であることをチェック
                $aryReferenceItem = explode(',', $value);
                foreach($aryReferenceItem as $id){
                    if(!is_numeric($id)){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1177");
                        $this->setValidRule($strErrAddMsg);
                        return $retBool;
                    }
                }

                //IDが1つしか設定されていない場合はエラー
                if(count($aryReferenceItem) <= 1){
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1178");
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }

                //メニューIDから、メニューに紐付く項目を取得
                $query01 = "SELECT CREATE_ITEM_ID, CREATE_MENU_ID, INPUT_METHOD_ID "
                            ." FROM F_CREATE_ITEM_INFO "
                            ." WHERE DISUSE_FLAG = '0' AND CREATE_MENU_ID = :CREATE_MENU_ID ";

                $aryForBind01['CREATE_MENU_ID'] = $createMenuId;

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "");
                $aryDiscover01 = array();
                $targetItemIdArray = array();
                if( $retArray01[0] === true ){
                    $objQuery01 =& $retArray01[1];
                    while($row01 = $objQuery01->resultFetch()){
                        $aryDiscover01[$row01['CREATE_ITEM_ID']] = $row01; //値を特定しやすいようにkeyをCREATE_ITEM_IDにして格納
                        $targetItemIdArray[] = $row01['CREATE_ITEM_ID'];
                    }
                    unset($objQuery01);
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1171", $retArray01[2]);
                    $this->setValidRule($strErrAddMsg);
                    return $retBool;
                }

                //項目のIDに、一意制約で指定したIDが含まれているかを確認
                foreach($aryReferenceItem as $id){
                    if(!in_array($id, $targetItemIdArray)){
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1179");
                        $this->setValidRule($strErrAddMsg);
                        return $retBool;
                    }

                    //入力方式が「パラメータシート参照」の場合はエラーにする。
                    if($aryDiscover01[$id]['INPUT_METHOD_ID'] == 11){
                        $retBool = false;
                        $strErrAddMsg =  $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1293");
                        $this->setValidRule($strErrAddMsg);
                        return $retBool;
                    }
                }

            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}


/**
* 初期値(文字列(単一行)/リンク)専用のバリデータクラス
*/

class SingleDefaultValueValidator extends SingleTextValidator {

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
                if($value != ''){
                    $inputMethodId = $arrayRegData['INPUT_METHOD_ID'];
                    $maxLength = '';
                    $pregMatch = '';

                    if($inputMethodId == 1){ //文字列(単一行)の場合
                        $maxLength = $arrayRegData['MAX_LENGTH'];
                        $pregMatch = $arrayRegData['PREG_MATCH'];
                    }elseif($inputMethodId == 10){ //リンクの場合
                        $maxLength = $arrayRegData['LINK_LENGTH'];
                    }

                    // 最大バイト数と初期値の条件一致をチェック
                    if($maxLength != ''){
                        if((int)$maxLength < strlen(bin2hex($value))/2){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1252");
                        }
                    }

                    //文字列(単一行)の場合のみ正規表現チェック
                    if($inputMethodId == 1){
                        // 正規表現と初期値の条件一致をチェック
                        if(@preg_match($pregMatch, "") !== false && $pregMatch != ''){
                            if(preg_match($pregMatch, $value) !== 1){
                                $retBool = false;
                                $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1253");
                            }
                        }
                    }
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}


/**
* 初期値(文字列(複数行))専用のバリデータクラス
*/

class MultiDefaultValueValidator extends MultiTextValidator {

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
                if($value != ''){
                    $maxLength = $arrayRegData['MULTI_MAX_LENGTH'];
                    $pregMatch = $arrayRegData['MULTI_PREG_MATCH'];

                    // 最大バイト数と初期値の条件一致をチェック
                    if($maxLength != ''){
                        //各メニューから「登録」する際にチェックする改行コードに統一するため、改行コードを\r\nに置換(改行は2バイトとして計算する)
                        $value=str_replace("\r\n", "\n", $value); //一度\r\nを\nに統一
                        $value=str_replace(["\r","\n"],"\r\n",$value); //\rと\nを再度\r\nに置換
                        if((int)$maxLength < strlen(bin2hex($value))/2){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1252");
                        }
                    }

                    // 正規表現と初期値の条件一致をチェック
                    if(@preg_match($pregMatch, "") !== false && $pregMatch != ''){
                        if(preg_match($pregMatch, $value) !== 1){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1253");
                        }
                    }
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}


/**
* 初期値(整数)専用のバリデータクラス
*/

class IntDefaultValueValidator extends IntNumValidator {

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
                if($value != ''){
                    $intMax = $arrayRegData['INT_MAX'];
                    $intMin = $arrayRegData['INT_MIN'];

                    // 最大数をチェック
                    if($intMax != ''){
                        if($intMax < $value){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1254");
                        }
                    }

                    // 際小数をチェック
                    if($intMin != ''){
                        if($value < $intMin){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1254");
                        }
                    }
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}


/**
* 初期値(小数)専用のバリデータクラス
*/

class FloatDefaultValueValidator extends FloatNumValidator {

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
                if($value != ''){
                    $floatMax = $arrayRegData['FLOAT_MAX'];
                    $floatMin = $arrayRegData['FLOAT_MIN'];
                    $floatDigit = $arrayRegData['FLOAT_DIGIT'];

                    // 最大数をチェック
                    if($floatMax != ''){
                        if($floatMax < $value){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1254");
                        }
                    }

                    // 際小数をチェック
                    if($floatMin != ''){
                        if($value < $floatMin){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1254");
                        }
                    }

                    // 桁数をチェック
                    if($floatDigit != ''){
                        if(strstr($value,'.')) $value= rtrim($value,"0"); //後ろの0を抜く
                        $vlen = strlen($value);
                        if(strstr($value,'.')) $vlen -= 1;
                        if(strstr($value,'-')) $vlen -= 1;
                        if($floatDigit < $vlen){
                            $retBool = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1255"); 
                        }
                    }
                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}

/**
* 初期値(プルダウン選択)専用のバリデータクラス
*/

class PulldownDefaultValueValidator extends RowIDNoValidator {

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
                if(!array_key_exists('INPUT_METHOD_ID',$arrayRegData) || 7 != $arrayRegData['INPUT_METHOD_ID']){
                    //入力方式が7(プルダウン選択)ではない場合は処理をしない
                    $retBool = true;
                    return $retBool;
                }

                if(!array_key_exists('OTHER_MENU_LINK_ID',$arrayRegData) || "" == $arrayRegData['OTHER_MENU_LINK_ID']){
                    //プルダウン選択が空の場合は処理をしない
                    $retBool = true;
                    return $retBool;
                }

                if($value != ''){
                    $web_php_functions = '/libs/webcommonlibs/web_php_functions.php';
                    $create_param_menu = '/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php';
                    require_once ($g['root_dir_path'] . $create_param_menu);
                    require_once ($g['root_dir_path'] . $web_php_functions);

                    //他メニュー連携から、必要な情報を取得
                    $otherMenuLinkTable = new OtherMenuLinkTable($g['objDBCA'], $g['db_model_ch']);
                    $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0' AND LINK_ID = :LINK_ID");
                    $link_id = $arrayRegData['OTHER_MENU_LINK_ID'];
                    $sqlBind = array('LINK_ID' => $link_id);
                    $result = $otherMenuLinkTable->selectTable($sql, $sqlBind);
                    if(!is_array($result)){
                        // DBエラー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1256");
                    }
                    $result_other_menu_link = $result;

                    //必要なデータを取得
                    if(!empty($result_other_menu_link)){
                        $menu_id = $result_other_menu_link[0]['MENU_ID'];
                        $table_name = $result_other_menu_link[0]['TABLE_NAME'];
                        $pri_name = $result_other_menu_link[0]['PRI_NAME'];
                        $column_name = $result_other_menu_link[0]['COLUMN_NAME'];
                    }else{
                        // DBエラー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1256");
                    }

                    //対象のテーブルからレコードを取得
                    $query01 = "SELECT $pri_name, $column_name, ACCESS_AUTH "
                                ." FROM $table_name "
                                ." WHERE DISUSE_FLAG = '0' ";

                    // SQL発行
                    $aryForBind01 = array();
                    $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "");
                    if( $retArray01[0] === true ){
                        $objQuery01 =& $retArray01[1];
                        $aryDiscover01 = array();
                        while($row01 = $objQuery01->resultFetch()){
                            $aryDiscover01[] = $row01;
                        }
                        unset($objQuery01);
                    }
                    else{
                        // DBエラー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1256");
                    }

                    //アクセス許可ロールのあるレコードに絞る
                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        // アクセス許可ロールチェックエラー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1256");
                    }

                    // 権限があるデータのみに絞る
                    $ret = $obj->chkRecodeArrayAccessPermission($aryDiscover01);
                    if($ret === false) {
                        // アクセス許可ロールチェックエラー
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1256");
                    }

                    //指定した初期値のIDが指定可能なレコードであるかどうかをチェック
                    foreach($aryDiscover01 as $data){
                        $checkFlg = false;
                        if($data[$pri_name] == $value){
                            $checkFlg = true;
                            break;
                        }
                    }

                    if($checkFlg == false){
                        // 指定できない初期値を入力した場合
                        $retBool = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1257");
                    }

                }
            }

            if( $retBool === false ){
                $this->setValidRule($strErrAddMsg);
            }
        }
        return $retBool;
    }
}

?>
