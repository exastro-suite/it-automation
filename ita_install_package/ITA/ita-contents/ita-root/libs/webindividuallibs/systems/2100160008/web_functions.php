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
 */

require_once ( $root_dir_path . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");

/**
 * 別データを更新する
 * 
 */
function updateOtherData($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){
    global $g;

    $intErrorType = null;
    $retStrLastErrMsg = null;
    $toDirFullpath = null;

    try{
        $beforeData = $aryVariant['edit_target_row'];
        $afterData = $reqUpdateData;

        //////////////////////////
        // カラムグループ管理テーブルを検索
        //////////////////////////
        $columnGroupTable = new ColumnGroupTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $columnGroupTable->createSselect("");

        // SQL実行
        $result = $columnGroupTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
            throw new Exception($msg);
        }
        $columnGroupArray = $result;

        $beforePa  = array_key_exists('PA_COL_GROUP_ID',$beforeData)?$beforeData['PA_COL_GROUP_ID']:null;
        $afterPa  = array_key_exists('PA_COL_GROUP_ID',$beforeData)?$beforeData['PA_COL_GROUP_ID']:null;

        // ディレクトリが変更されていた場合
        if($beforePa != $afterPa || $beforeData['COL_GROUP_NAME'] != $afterData['COL_GROUP_NAME']){

            // 子カラムグループのパスを変更する
            $result = updateChildCol($columnGroupTable, $beforeData['COL_GROUP_ID'], $afterData['FULL_COL_GROUP_NAME'], $afterData['LAST_UPDATE_USER'], $columnGroupArray);

            if(true !== $result){
                throw new Exception($result);
            }
        }
    }
    catch (Exception $e){
        $intErrorType = 500;
        $strTmpStrBody = 'ERROR([FILE]' .  __FILE__  . ',[LINE]' . $e->getLine() . ')' . ' ' . $e->getMessage();
        web_log($strTmpStrBody);
    }

    return array( null, $intErrorType, array($retStrLastErrMsg) );
}

/**
 * 子カラムグループ更新
 * 
 */
function updateChildCol($columnGroupTable, $dirId, $dirNameFullpath, $lastUpdateUser, $columnGroupArray){

    global $g;

    foreach($columnGroupArray as $columnGroupData){
        if($columnGroupData['PA_COL_GROUP_ID'] == $dirId){
            $updateData = $columnGroupData;
            $updateData['FULL_COL_GROUP_NAME']  = $dirNameFullpath . "/" . $updateData['COL_GROUP_NAME'];
            $updateData['LAST_UPDATE_USER']     = $lastUpdateUser;

            //////////////////////////
            // ディレクトリマスタを更新
            //////////////////////////
            $result = $columnGroupTable->updateTable($updateData);
            if(true !== $result){
                return $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            }

            // 子ディレクトリのパスを変更する
            $result = updateChildCol($columnGroupTable, $updateData['COL_GROUP_ID'], $updateData['FULL_COL_GROUP_NAME'], $lastUpdateUser, $columnGroupArray);
            if(true !== $result){
                return $result;
            }
        }
    }
    return true;
}
