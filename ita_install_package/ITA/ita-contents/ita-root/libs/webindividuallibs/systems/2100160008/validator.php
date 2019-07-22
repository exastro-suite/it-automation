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
* 親カラムグループ専用のバリデータクラス
*/
class PaColGroupIdValidator extends IDValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $strErrAddMsg = "";

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            return false;
        }

        $retBool = true;

        $strModeId = "";
        $modeValue_sub = "";

        $boolCheckContinue = true;

        $p_reg_delete_trig_date = "";

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if( $strModeId != "" ){
            //----更新前のレコード内容（登録時は空配列）
            $editTgtRow = $arrayVariant['edit_target_row'];
            //更新前のレコード内容（登録時は空配列）----


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

                // 更新時、自分のカラムグループを選択していないかどうか確認
                $strCheckTgtDeleteTrigId  = array_key_exists('PA_COL_GROUP_ID',$arrayRegData)?$arrayRegData['PA_COL_GROUP_ID']:null;
                if($strCheckTgtDeleteTrigId === $strNumberForRI){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1014");
                }
            }

            if($boolCheckContinue===true){

                if(array_key_exists('PA_COL_GROUP_ID', $arrayRegData) && "" != $arrayRegData['PA_COL_GROUP_ID']){

                    if(array_key_exists('COL_GROUP_ID', $arrayRegData)){
                        $colId = $arrayRegData['COL_GROUP_ID'];
                    }
                    else{
                        $colId = $editTgtRow['COL_GROUP_ID'];
                    }

                    $parentColId = $arrayRegData['PA_COL_GROUP_ID'];

                    $query01 = "SELECT PA_COL_GROUP_ID "
                                ." FROM F_COLUMN_GROUP "
                                ." WHERE COL_GROUP_ID = :COL_GROUP_ID "
                                ." AND DISUSE_FLAG = '0' ";

                    while(true){

                        $aryForBind01['COL_GROUP_ID'] = $parentColId;

                        // SQL発行
                        $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01);

                        if( $retArray01[0] === true ){
                            $objQuery01 =& $retArray01[1];
                            $aryDiscover01 = array();
                            while($row01 = $objQuery01->resultFetch()){
                                $aryDiscover01[] = $row01;
                            }
                            unset($objQuery01);

                            if(0 == count($aryDiscover01)){
                                break;
                            }
                            else{
                                // 親ディレクトリがループ関係かどうかチェック
                                if($colId == $aryDiscover01[0]['PA_COL_GROUP_ID']){
                                    $retBool = false;
                                    $boolCheckContinue = false;
                                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1015");
                                    break;
                                }
                                else if("" == $aryDiscover01[0]['PA_COL_GROUP_ID']){
                                    break;
                                }
                                else{
                                    $parentColId = $aryDiscover01[0]['PA_COL_GROUP_ID'];
                                }
                            }
                        }
                        else{
                            // DBエラー
                            $retBool = false;
                            $boolCheckContinue = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1016", $retArray01[2]);
                            break;
                        }
                    }
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
* カラムグループ名専用のバリデータクラス
*/
class ColGroupNameValidator extends SingleTextValidator {

    protected $eventMasterName;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        $strErrAddMsg = "";

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            return false;
        }

        $retBool = true;
        $strModeId = "";
        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
                if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
                    $strModeId = "DTiS_filterDefault";//filter_table
                }
            }
        }

        if( $strModeId != "" ){
            //----更新前のレコード内容（登録時は空配列）
            $editTgtRow = $arrayVariant['edit_target_row'];
            //更新前のレコード内容（登録時は空配列）----

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
                //廃止時
                if( $modeValue_sub=="on" ){

                    $query01 = "SELECT COL_GROUP_ID, PA_COL_GROUP_ID, FULL_COL_GROUP_NAME "
                              ." FROM F_COLUMN_GROUP "
                              ." WHERE DISUSE_FLAG = '0' ";

                    // SQL発行
                    $retArray01 = singleSQLExecuteAgent($query01, array());

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
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1016", $retArray01[2]);
                    }

                    // 廃止対象が親になっている場合はエラー
                    $paColGroupIdArray = array_column($aryDiscover01, 'PA_COL_GROUP_ID');
                    $matchArray = array();
                    foreach($aryDiscover01 as $data){
                        if($editTgtRow['COL_GROUP_ID'] == $data['PA_COL_GROUP_ID']){
                            $matchArray[] = $data['COL_GROUP_ID'];
                        }
                    }

                    if(0 < count($matchArray)){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1017", implode(",", $matchArray));
                        $this->setErrShowPrefix(false);
                    }
                    $boolCheckContinue = false;
                }
                else if( $modeValue_sub=="off" ){
                    //復活時
                    $boolCheckContinue = true;
                }
            }else{
                //処理をしない
            }

            if($boolCheckContinue===true){

                // 禁止文字チェック
                if( preg_match('/[\/]+/', $value) === 1){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITACREPAR-ERR-1018");
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

?>