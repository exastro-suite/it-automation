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
 *    関数定義
 *
 */

/**
*親ディレクトリ専用のバリデータクラス
*/
class parentDirValidator extends IDValidator {

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

                // 更新時、自分のディレクトリを選択していないかどうか確認
                $strCheckTgtDeleteTrigId  = array_key_exists('PARENT_DIR_ID',$arrayRegData)?$arrayRegData['PARENT_DIR_ID']:null;
                if($strCheckTgtDeleteTrigId === $strNumberForRI){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1006");
                }
            }

            if($boolCheckContinue===true){

                if(array_key_exists('PARENT_DIR_ID', $arrayRegData) && "" != $arrayRegData['PARENT_DIR_ID']){

                    if(array_key_exists('DIR_ID', $arrayRegData)){
                        $dirId = $arrayRegData['DIR_ID'];
                    }
                    else{
                        $dirId = $editTgtRow['DIR_ID'];
                    }

                    $parentDirId = $arrayRegData['PARENT_DIR_ID'];

                    $query01 = "SELECT PARENT_DIR_ID "
                                ." FROM F_DIR_MASTER "
                                ." WHERE DIR_ID = :DIR_ID "
                                ." AND DISUSE_FLAG = '0' ";

                    while(true){

                        $aryForBind01['DIR_ID'] = $parentDirId;

                        // SQL発行
                        $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "NONAME_FUNC(PARENT_DIR_ID)");

                        if( $retArray01[0] === true ){
                            $objQuery01 =& $retArray01[1];
                            $intCount01 = 0;
                            $aryDiscover01 = array();
                            while($row01 = $objQuery01->resultFetch()){
                                $intCount01 += 1;
                                $aryDiscover01[] = $row01;
                            }
                            unset($objQuery01);

                            if(0 == $intCount01){
                                break;
                            }
                            else{
                                // 親ディレクトリがループ関係かどうかチェック
                                if($dirId == $aryDiscover01[0]['PARENT_DIR_ID']){
                                    // 親ディレクトリの取得失敗
                                    $retBool = false;
                                    $boolCheckContinue = false;
                                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1007");
                                    break;
                                }
                                else if("" == $aryDiscover01[0]['PARENT_DIR_ID']){
                                    break;
                                }
                                else{
                                    $parentDirId = $aryDiscover01[0]['PARENT_DIR_ID'];
                                }
                            }
                        }
                        else{
                            // DBエラー
                            $retBool = false;
                            $boolCheckContinue = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1008", $retArray01[2]);
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
*ディレクトリ専用のバリデータクラス
*/
class dirValidator extends SingleTextValidator {

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

                // ディレクトリの先頭が"/"であるかチェック
                if("/" === substr($value, 0, 1)){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1009");
                }
            }

            if($boolCheckContinue===true){

                // ディレクトリの末尾が"/"であるかチェック
                if("/" === substr($value, -1)){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1010");
                }
            }

            if($boolCheckContinue===true){

                // 禁止文字チェック
                if( preg_match('/[!"#$%&\'|`;:\*<>?\\\\]+/', $value) === 1 || $value === "." || $value === ".."){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1011");
                }
            }

            if($boolCheckContinue===true){

                $query01 = "SELECT DIR_ID, DIR_NAME_FULLPATH "
                            ." FROM F_DIR_MASTER "
                            ." WHERE DISUSE_FLAG = '0' ";

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, array(), "NONAME_FUNC(PARENT_DIR_ID)");

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
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1012", $retArray01[2]);
                }
            }

            if($boolCheckContinue===true){
                // 親ディレクトリの取得
                $parentDir = "";
                if(array_key_exists('PARENT_DIR_ID', $arrayRegData) && "" != $arrayRegData['PARENT_DIR_ID']){

                    $dirIdArray = array_column($aryDiscover01, 'DIR_ID');
                    $parentIdx = array_search($arrayRegData['PARENT_DIR_ID'], $dirIdArray);

                    if(false !== $parentIdx){
                        $parentDir = $aryDiscover01[$parentIdx]['DIR_NAME_FULLPATH'];
                    }
                    else{
                        // 親ディレクトリの取得失敗
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1013", $arrayRegData['PARENT_DIR_ID']);
                    }
                }
            }

            if($boolCheckContinue===true){

                $targetDir = $parentDir . $value . "/";

                $dirNameFullpathArray = array_column($aryDiscover01, 'DIR_NAME_FULLPATH');
                $matchNameIdx = array_search($targetDir, $dirNameFullpathArray);

                if(false === $matchNameIdx){
                    // 重複無し
                }
                else if(array_key_exists('DIR_ID', $editTgtRow) && $editTgtRow['DIR_ID'] === $aryDiscover01[$matchNameIdx]['DIR_ID']){
                    // 重複データは自分なので問題なし
                }
                else{
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1014", $aryDiscover01[$matchNameIdx]['DIR_ID']);
                }
            }
            // 更新または廃止の場合
            if(($boolCheckContinue===true && $strModeId == "DTUP_singleRecUpdate") ||
               ($strModeId == "DTUP_singleRecDelete" && $modeValue_sub=="on")){

                if("1" == $editTgtRow['DIR_ID']){
                    // 項番1のデータの更新・廃止はエラー
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1034");
                }
                else{
                    // 配下に申請中のデータがある場合はバリデータエラーとする

                    $query02 = "SELECT FILE_M_ID, FILE_NAME_FULLPATH "
                                ." FROM G_FILE_MANAGEMENT_1 "
                                ." WHERE DISUSE_FLAG = '0' AND "
                                ." FILE_STATUS_ID IN (1,2,3,4,5,7,8)";

                    // SQL発行
                    $retArray02 = singleSQLExecuteAgent($query02, array(), "NONAME_FUNC(FILE_NAME_FULLPATH)");

                    if( $retArray02[0] === true ){
                        $objQuery02 =& $retArray02[1];
                        $aryDiscover02 = array();
                        while($row02 = $objQuery02->resultFetch()){
                            $aryDiscover02[] = $row02;
                        }
                        unset($objQuery02);

                        // 申請中のデータがあるか確認する
                        $matchIds = array();
                        foreach($aryDiscover02 as $discover){
                           if(substr($discover['FILE_NAME_FULLPATH'], 0, strlen($editTgtRow['DIR_NAME_FULLPATH'])) === $editTgtRow['DIR_NAME_FULLPATH']){
                                $matchIds[] = $discover['FILE_M_ID'];
                            }
                        }

                        if(0 < count($matchIds)){
                            $retBool = false;
                            $boolCheckContinue = false;
                            $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1035", implode(",", $matchIds));
                        }
                    }
                    else{
                        // DBエラー
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1036", $retArray02[2]);
                    }
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
*ファイル専用のバリデータクラス
*/
class fileValidator extends SingleTextValidator {

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

                // 禁止文字チェック
                if( preg_match('/[!"#$%&\'|`;:\*<>?\/\\\\]+/', $arrayRegData['FILE_NAME']) === 1 || $value === "." || $value === ".."){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1015");
                }
            }

            if($boolCheckContinue===true){
                // ディレクトリ名の取得
                $query01 = "SELECT DIR_NAME_FULLPATH "
                            ." FROM F_DIR_MASTER "
                            ." WHERE DIR_ID = :DIR_ID "
                            ." AND DISUSE_FLAG = '0' ";

                $aryForBind01['DIR_ID'] = $arrayRegData['DIR_ID'];

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "NONAME_FUNC(DIR_NAME_FULLPATH)");

                if( $retArray01[0] === true ){
                    $objQuery01 =& $retArray01[1];
                    $intCount01 = 0;
                    $aryDiscover01 = array();
                    while($row01 = $objQuery01->resultFetch()){
                        $intCount01 += 1;
                        $aryDiscover01[] = $row01;
                    }
                    unset($objQuery01);
                    $aryDiscover01[] = $intCount01;

                    if(1 == $intCount01){
                        $dirName = $aryDiscover01[0]['DIR_NAME_FULLPATH'];
                    }
                    else{
                        // ディレクトリの取得失敗
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1016", $arrayRegData['DIR_ID']);
                    }
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1017", $retArray01[2]);
                }
            }

            // 更新または廃止の場合
            if(($boolCheckContinue===true && $strModeId == "DTUP_singleRecUpdate") ||
               ($strModeId == "DTUP_singleRecDelete" && $modeValue_sub=="on")){

                // 配下に申請中のデータがある場合はバリデータエラーとする
                $query02 = "SELECT FILE_M_ID, FILE_NAME_FULLPATH "
                            ." FROM G_FILE_MANAGEMENT_1 "
                            ." WHERE DISUSE_FLAG = '0' AND "
                            ." FILE_ID =:FILE_ID AND "
                            ." FILE_STATUS_ID IN (1,2,3,4,5,7,8)";

                $aryForBind02['FILE_ID'] = $editTgtRow['FILE_ID'];

                // SQL発行
                $retArray02 = singleSQLExecuteAgent($query02, $aryForBind02, "NONAME_FUNC(FILE_NAME_FULLPATH)");

                if( $retArray02[0] === true ){
                    $objQuery02 =& $retArray02[1];
                    $aryDiscover02 = array();
                    while($row02 = $objQuery02->resultFetch()){
                        $aryDiscover02[] = $row02;
                    }
                    unset($objQuery02);

                    // 申請中のデータがあるか確認する
                    if(0 < count($aryDiscover02)){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1037", implode(",", array_column($aryDiscover02, 'FILE_M_ID')));
                    }
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1036", $retArray02[2]);
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
*紐付け先資材名専用のバリデータクラス
*/
class LinkNameValidator extends SingleTextValidator {

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

                if(array_key_exists('ANS_TEMPLATE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_TEMPLATE_CHK']) {
                    if( preg_match('/^TPF_[_a-zA-Z0-9]+$/', $arrayRegData['MATERIAL_LINK_NAME']) != 1){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1018");
                    }
                }
            }
            if($boolCheckContinue===true){

                if(array_key_exists('ANS_CONTENTS_FILE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_CONTENTS_FILE_CHK']) {
                    if( preg_match('/^CPF_[_a-zA-Z0-9]+$/', $arrayRegData['MATERIAL_LINK_NAME']) != 1){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1019");
                    }
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
*資材紐付け先選択専用（対話ファイル素材集以外）のバリデータクラス(Ansible用)
*/
class LinkageCheckValidator extends IDValidator {

	protected $strErrAddMsg;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1020");
            $this->setValidRule($this->makeValidRule());
            return false;
        }

        $retBool = true;

        $strModeId = "";
        $modeValue_sub = "";

        $boolCheckContinue = true;

        $p_reg_release_trig_date = "";
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
                if("" == $value) {
                    $boolCheckContinue = false;
                }
            }

            if($boolCheckContinue===true){

                $chkCnt = 0;

                if(array_key_exists('ANS_PLAYBOOK_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_PLAYBOOK_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANS_TEMPLATE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_TEMPLATE_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANS_CONTENTS_FILE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_CONTENTS_FILE_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANSIBLE_DIALOG_CHK', $arrayRegData) && "1" == $arrayRegData['ANSIBLE_DIALOG_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANSIBLE_ROLE_CHK', $arrayRegData) && "1" == $arrayRegData['ANSIBLE_ROLE_CHK']){
                    $chkCnt ++;
                }

                if(1 < $chkCnt){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1021");
                }
            }
        }

        if( $retBool === false ){
            $this->setValidRule($this->strErrAddMsg);
        }
        return $retBool;
    }
}

/**
*対話ファイル素材集専用のバリデータクラス(Ansible用)
*/
class AnsibleDialogCheckValidator extends IDValidator {

	protected $strErrAddMsg;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1022");
            $this->setValidRule($this->makeValidRule());
            return false;
        }

        $retBool = true;

        $strModeId = "";
        $modeValue_sub = "";

        $boolCheckContinue = true;

        $p_reg_release_trig_date = "";
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

                $chkCnt = 0;

                if(array_key_exists('ANS_PLAYBOOK_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_PLAYBOOK_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANS_TEMPLATE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_TEMPLATE_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANS_CONTENTS_FILE_CHK', $arrayRegData) && "1" == $arrayRegData['ANS_CONTENTS_FILE_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANSIBLE_DIALOG_CHK', $arrayRegData) && "1" == $arrayRegData['ANSIBLE_DIALOG_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('ANSIBLE_ROLE_CHK', $arrayRegData) && "1" == $arrayRegData['ANSIBLE_ROLE_CHK']){
                    $chkCnt ++;
                }

                if(1 < $chkCnt){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1023");
                }
            }

            if($boolCheckContinue===true){
                if("" == $value){
                    if(array_key_exists('OS_TYPE_ID', $arrayRegData) && "" != $arrayRegData['OS_TYPE_ID']){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1024");
                    }
                }
                else{
                    if(!array_key_exists('OS_TYPE_ID', $arrayRegData) || "" == $arrayRegData['OS_TYPE_ID']){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1025");
                    }
                }
            }
        }

        if( $retBool === false ){
            $this->setValidRule($this->strErrAddMsg);
        }
        return $retBool;
    }
}

/**
*OS種別専用のバリデータクラス(Ansible用)
*/
class OsTypeNameValidator extends IDValidator {

	protected $strErrAddMsg;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;

        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1026");
            $this->setValidRule($this->makeValidRule());
            return false;
        }

        $retBool = true;

        $strModeId = "";
        $modeValue_sub = "";

        $boolCheckContinue = true;

        $p_reg_release_trig_date = "";
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
                if("" == $value){
                    if(array_key_exists('ANSIBLE_DIALOG_CHK', $arrayRegData) && "1" == $arrayRegData['ANSIBLE_DIALOG_CHK']){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1027");
                    }
                }
                else{
                    if(!array_key_exists('ANSIBLE_DIALOG_CHK', $arrayRegData) || "1" !== $arrayRegData['ANSIBLE_DIALOG_CHK']){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1028");
                    }
                }
            }
        }

        if( $retBool === false ){
            $this->setValidRule($this->strErrAddMsg);
        }
        return $retBool;
    }
}

/**
*紐付け先資材名専用のバリデータクラス(Openstack用)
*/
class LinkNameValidator2 extends SingleTextValidator {

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

                //チェック内容
                $query01 = "SELECT ROW_ID , MATERIAL_LINK_NAME , OPENST_TEMPLATE_CHK , OPENST_ENVIRONMENT_CHK"
                            ." FROM F_MATERIAL_LINKAGE_OPENSTACK WHERE DISUSE_FLAG='0' AND MATERIAL_LINK_NAME=:MATERIAL_LINK_NAME";
                
                $aryForBind01['MATERIAL_LINK_NAME'] = $value;

                // SQL発行
                $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "NONAME_FUNC(MATERIAL_LINK_NAME)");

                if( $retArray01[0] === true ){
                    $objQuery01 =& $retArray01[1];
                    $aryDiscover01 = array();
                    while($row01 = $objQuery01->resultFetch()){
                        $aryDiscover01[] = $row01;
                    }
                    unset($objQuery01);

                    //DBの情報の数だけループ
                    $errFlg = false;
                    $matchId = null;
                    foreach($aryDiscover01 as $targetData){
                        if(array_key_exists('OPENST_TEMPLATE_CHK',$arrayRegData)){
                            if($targetData['OPENST_TEMPLATE_CHK'] == "1" && $arrayRegData['OPENST_TEMPLATE_CHK'] == "1"){
                                if(array_key_exists('ROW_ID' , $arrayVariant['edit_target_row']) && $arrayVariant['edit_target_row']['ROW_ID'] == $targetData['ROW_ID']){
                                    $errFlg = false;
                                }else{
                                    $matchId = $targetData['ROW_ID'];
                                    $errFlg = true;
                                    break;
                                }
                            }
                        }
                        
                        if(array_key_exists('OPENST_ENVIRONMENT_CHK',$arrayRegData)){
                            if($targetData['OPENST_ENVIRONMENT_CHK'] == "1" && $arrayRegData['OPENST_ENVIRONMENT_CHK'] == "1"){
                                if(array_key_exists('ROW_ID' , $arrayVariant['edit_target_row']) && $arrayVariant['edit_target_row']['ROW_ID'] == $targetData['ROW_ID']){
                                    $errFlg = false;
                                }else{
                                    $matchId = $targetData['ROW_ID'];
                                    $errFlg = true;
                                    break;
                                }
                            }
                        }
                    }
                    if($errFlg == true){
                        $retBool = false;
                        $boolCheckContinue = false;
                        $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1034" , $matchId);
                    }
                }
                else{
                    // DBエラー
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1012", $retArray01[2]);
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
*資材紐付け先選択専用のバリデータクラス(OpenStack用)
*/
class LinkageCheckValidator2 extends IDValidator {

	protected $strErrAddMsg;

    function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

        global $g;
        if( parent::isValid($value, $strNumberForRI, $arrayRegData, $arrayVariant) != true ) {
            $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1020");
            $this->setValidRule($this->makeValidRule());
            return false;
        }

        $retBool = true;

        $strModeId = "";
        $modeValue_sub = "";

        $boolCheckContinue = true;

        $p_reg_release_trig_date = "";
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
                if("" == $value) {
                    $boolCheckContinue = false;
                }
            }

            if($boolCheckContinue===true){

                $chkCnt = 0;

                if(array_key_exists('OPENST_TEMPLATE_CHK', $arrayRegData) && "1" == $arrayRegData['OPENST_TEMPLATE_CHK']){
                    $chkCnt ++;
                }
                if(array_key_exists('OPENST_ENVIRONMENT_CHK', $arrayRegData) && "1" == $arrayRegData['OPENST_ENVIRONMENT_CHK']){
                    $chkCnt ++;
                }

                if(1 < $chkCnt){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1021");
                }
            }
        }

        if( $retBool === false ){
            $this->setValidRule($this->strErrAddMsg);
        }
        return $retBool;
    }
}

/**
*リモートリポジトリURL専用のバリデータクラス
*/
class RemortRepoUrlValidator extends SingleTextValidator {

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

                // 禁止文字チェック
                if( preg_match('/[!"#$%&\^|`;\*<>?\\\\]+/', $value) === 1 || $value === "." || $value === ".."){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1029");
                }
            }
        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

/**
*クローンリポジトリディレクトリ専用のバリデータクラス
*/
class CloneRepoDirValidator extends SingleTextValidator {

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

                // 禁止文字チェック
                if( preg_match('/[!"#$%&\'|`;:\*<>?\\\\]+/', $value) === 1 || $value === "." || $value === ".."){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1030");
                }
            }

            if($boolCheckContinue===true){

                // ディレクトリの先頭が"/"であるかチェック
                if("/" != substr($value, 0, 1)){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1031");
                }
            }

            if($boolCheckContinue===true){

                // 「/tmp」で始まっていないかチェック
                if("/tmp" === substr($value, 0, 4)){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1032");
                }
            }

            if($boolCheckContinue===true){

                // ディレクトリの末尾が"/"であるかチェック
                if("/" == substr($value, -1, 1)){
                    $retBool = false;
                    $boolCheckContinue = false;
                    $strErrAddMsg = $g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1033");
                }
            }

        }
        $this->setValidRule($strErrAddMsg);
        return $retBool;
    }
}

?>