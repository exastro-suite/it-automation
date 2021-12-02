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
 * 【概要】
 *  パラメータシート作成管理を元にメニューを作成する
 */

if( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}
define('ROOT_DIR_PATH',         $root_dir_path);
require_once ROOT_DIR_PATH      . '/libs/backyardlibs/create_param_menu/ky_create_param_menu_env.php';
require_once CPM_LIB_PATH       . 'ky_create_param_menu_classes.php';
require_once CPM_LIB_PATH       . 'ky_create_param_menu_functions.php';
require_once COMMONLIBS_PATH    . 'common_php_req_gate.php';
$logLevel = LOG_LEVEL;

try{
    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if($logLevel === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // 未実行のレコードがない場合は処理を終了する
    //////////////////////////
    $createMenuStatusArray = getUnexecutedRecord();
    
    if(count($createMenuStatusArray) === 0){
        if($logLevel === 'DEBUG'){
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10004'));
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
        }
        exit;
    }

    //////////////////////////
    // テンプレートファイル読み込み
    //////////////////////////
    $templatePathArray =array(TEMPLATE_PATH . FILE_HG_LOADTABLE,
                              TEMPLATE_PATH . FILE_H_LOADTABLE,
                              TEMPLATE_PATH . FILE_VIEW_LOADTABLE,
                              TEMPLATE_PATH . FILE_HG_SQL,
                              TEMPLATE_PATH . FILE_H_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_LOADTABLE,
                              TEMPLATE_PATH . FILE_CONVERT_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_H_LOADTABLE,
                              TEMPLATE_PATH . FILE_CONVERT_H_SQL,
                              TEMPLATE_PATH . FILE_CMDB_LOADTABLE,
                              TEMPLATE_PATH . FILE_CMDB_SQL,
                              TEMPLATE_PATH . FILE_H_LOADTABLE_OP,
                              TEMPLATE_PATH . FILE_VIEW_LOADTABLE_OP,
                              TEMPLATE_PATH . FILE_CONVERT_H_LOADTABLE_OP,
                              TEMPLATE_PATH . FILE_H_OP_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_H_OP_SQL,
                              TEMPLATE_PATH . FILE_PARTS_SNG,
                              TEMPLATE_PATH . FILE_PARTS_MUL,
                              TEMPLATE_PATH . FILE_PARTS_INT,
                              TEMPLATE_PATH . FILE_PARTS_FLT,
                              TEMPLATE_PATH . FILE_PARTS_DAY,
                              TEMPLATE_PATH . FILE_PARTS_DT,
                              TEMPLATE_PATH . FILE_PARTS_ID,
                              TEMPLATE_PATH . FILE_PARTS_PW,
                              TEMPLATE_PATH . FILE_PARTS_UPL,
                              TEMPLATE_PATH . FILE_PARTS_LNK,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_SNG,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_MUL,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_INT,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_FLT,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_DAY,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_DT,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_ID,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_PW,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_UPL,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_LNK,
                              TEMPLATE_PATH . FILE_HG_EDIT_SQL,
                              TEMPLATE_PATH . FILE_H_EDIT_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_EDIT_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_H_EDIT_SQL,
                              TEMPLATE_PATH . FILE_CMDB_EDIT_SQL,
                              TEMPLATE_PATH . FILE_H_OP_EDIT_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_H_OP_EDIT_SQL,
                              TEMPLATE_PATH . FILE_PARTS_REF,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_REF,
                              TEMPLATE_PATH . FILE_PARTS_LINK_ID,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_LINK_ID,
                              TEMPLATE_PATH . FILE_PARTS_TYPE3,
                              TEMPLATE_PATH . FILE_PARTS_VIEW_TYPE3REF,
                             );
    $templateArray = array();
    foreach($templatePathArray as $templatePath){
        if(!file_exists($templatePath)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5001', array($templatePath));
            outputLog($msg);
            throw new Exception($msg);
        }
        $work = file_get_contents($templatePath);
        if(false === $work){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5002', array($templatePath));
            outputLog($msg);
            throw new Exception($msg);
        }
        $templateArray[] = $work;
    }

    $hgLoadTableTmpl            = $templateArray[0];
    $hostLoadTableTmpl          = $templateArray[1];
    $viewLoadTableTmpl          = $templateArray[2];
    $hgSqlTmpl                  = $templateArray[3];
    $hostSqlTmpl                = $templateArray[4];
    $convLoadTableTmpl          = $templateArray[5];
    $convSqlTmpl                = $templateArray[6];
    $convHostLoadTableTmpl      = $templateArray[7];
    $convHostSqlTmpl            = $templateArray[8];
    $cmdbLoadTableTmpl          = $templateArray[9];
    $cmdbSqlTmpl                = $templateArray[10];
    $hostLoadTableOpTmpl        = $templateArray[11];
    $viewLoadTableOpTmpl        = $templateArray[12];
    $convHostLoadTableOpTmpl    = $templateArray[13];
    $hostSqlOpTmpl              = $templateArray[14];
    $convHostSqlOpTmpl          = $templateArray[15];
    $partSingle                 = $templateArray[16];
    $partMulti                  = $templateArray[17];
    $partInteger                = $templateArray[18];
    $partFloat                  = $templateArray[19];
    $partDate                   = $templateArray[20];
    $partDateTime               = $templateArray[21];
    $partId                     = $templateArray[22];
    $partPassword               = $templateArray[23];
    $partUpload                 = $templateArray[24];
    $partLink                   = $templateArray[25];
    $partViewSingle             = $templateArray[26];
    $partViewMulti              = $templateArray[27];
    $partViewInteger            = $templateArray[28];
    $partViewFloat              = $templateArray[29];
    $partViewDate               = $templateArray[30];
    $partViewDateTime           = $templateArray[31];
    $partViewId                 = $templateArray[32];
    $partViewPassword           = $templateArray[33];
    $partViewUpload             = $templateArray[34];
    $partViewLink               = $templateArray[35];
    $hgEditSqlTmpl              = $templateArray[36];
    $hostEditSqlTmpl            = $templateArray[37];
    $convEditSqlTmpl            = $templateArray[38];
    $convHostEditSqlTmpl        = $templateArray[39];
    $cmdbEditSqlTmpl            = $templateArray[40];
    $hostEditSqlOpTmpl          = $templateArray[41];
    $convHostSqlOpEditTmpl      = $templateArray[42];
    $partReference              = $templateArray[43];
    $partViewReference          = $templateArray[44];
    $partLinkId                 = $templateArray[45];
    $partViewLinkId             = $templateArray[46];
    $partType3Reference         = $templateArray[47];
    $partViewType3Reference     = $templateArray[48];

    //////////////////////////
    // パラメータシート作成情報を取得
    //////////////////////////
    $createMenuInfoTable = new CreateMenuInfoTable($objDBCA, $db_model_ch);
    $sql = $createMenuInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $createMenuInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $createMenuInfoArray = $result;

    //////////////////////////
    // パラメータシート項目作成情報を取得
    //////////////////////////
    $createItemInfoTable = new CreateItemInfoTable($objDBCA, $db_model_ch);
    $sql = $createItemInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $createItemInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $createItemInfoArray = $result;

    //////////////////////////
    // 他メニュー連携テーブルを検索
    //////////////////////////
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);
    $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $otherMenuLinkTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $otherMenuLinkArray = $result;

    $urlOptionTargetArray = array('2000000005'); //「他メニュー連携」のメニューの中でLinkIDColumnのUrlOptionをtrueにする対象。（プルダウン選択の項目名がVIEWで作られた「〇〇:△△」というような形式の場合に指定する。
    $noLinkMenuIdArray = array('2100160016', '2100160017'); //LinkIDColumnではなくIDColumnで作成したい対象のメニューID。「Yes/No」などの固定のフラグを選択するものが対象。

    //////////////////////////
    // 参照項目情報テーブルを検索
    //////////////////////////
    $referenceItemArray = array();
    $referenceItemTable = new ReferenceItemTable($objDBCA, $db_model_ch);
    $sql = $referenceItemTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $referenceItemTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    if(!empty($result)){
        //検索しやすいようにITEM_IDをkeyにする
        foreach($result as $row){
            $referenceItemArray[$row['ITEM_ID']] = $row;
        }  
    }

    //////////////////////////
    // パラメータシート参照ビューを検索
    //////////////////////////
    $type3ReferenceArray = array();
    $type3ReferenceView = new ReferenceSheetType3View($objDBCA, $db_model_ch);
    $sql = $type3ReferenceView->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $type3ReferenceView->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    if(!empty($result)){
        //検索しやすいようにITEM_IDをkeyにする
        foreach($result as $row){
            $type3ReferenceArray[$row['ITEM_ID']] = $row;
        }  
    }

    //////////////////////////
    // カラムグループ管理テーブルを検索
    //////////////////////////
    $columnGroupTable = new ColumnGroupTable($objDBCA, $db_model_ch);
    $sql = $columnGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $columnGroupTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
        throw new Exception($msg);
    }
    $columnGroupArray = $result;

    //////////////////////////
    // パラメータシート(縦)作成情報を取得
    //////////////////////////
    $convertParamInfoTable = new ConvertParamInfoTable($objDBCA, $db_model_ch);
    $sql = $convertParamInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $convertParamInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $convertParamInfoArray = $result;

    //////////////////////////
    // 一意制約管理情報を取得
    //////////////////////////
    $uniqueConstraintTable = new UniqueConstraintTable($objDBCA, $db_model_ch);
    $sql = $uniqueConstraintTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $uniqueConstraintTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $uniqueConstraintArray = $result;

    //////////////////////////
    // ロール・ユーザ紐づけ情報を取得
    //////////////////////////
    $roleAccountLinkListTable = new RoleAccountLinkListTable($objDBCA, $db_model_ch);
    $sql = $roleAccountLinkListTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $roleAccountLinkListTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
        outputLog($msg);
        throw new Exception($msg);
    }
    $roleUserLinkArray = $result;

    //////////////////////////
    // 作業用ディレクトリ作成
    //////////////////////////
    // 最新時間を取得
    $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
    $nowTime = date("YmdHis") . $now->format("u");

    $tmpDir = TEMP_PATH . $nowTime;
    $result = mkdir($tmpDir, 0777, true);
    
    if(true != $result){
        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($tmpDir));
        outputLog($msg);
        throw new Exception($msg);
    }

    //////////////////////////
    // 処理対象のデータ件数分ループ
    //////////////////////////
    foreach($createMenuStatusArray as $targetData){
        //作成タイプを取得
        $menuCreateTypeId = $targetData['MENU_CREATE_TYPE_ID'];

        //変数を定義
        $duplicateItemNameArray = array();

        //////////////////////////
        // パラメータシート作成情報を特定する
        //////////////////////////
        $createMenuInfoIdx = array_search($targetData['CREATE_MENU_ID'], array_column($createMenuInfoArray, 'CREATE_MENU_ID'));

        // パラメータシート作成情報が特定できなかった場合、完了(異常)
        if(false === $createMenuInfoIdx){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5005');
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }
        $cmiData = $createMenuInfoArray[$createMenuInfoIdx];

        //////////////////////////
        // パラメータシート項目作成情報を特定する
        //////////////////////////
        $itemInfoArray = array();
        $itemInputMethodCheckArray = array();
        foreach($createItemInfoArray as $ciiData){
            if($targetData['CREATE_MENU_ID'] === $ciiData['CREATE_MENU_ID']){
                $itemInfoArray[] = $ciiData;
                $itemInputMethodCheckArray[$ciiData['CREATE_ITEM_ID']] = $ciiData['INPUT_METHOD_ID'];
            }
        }

        // パラメータシート項目作成情報が0件の場合、完了(異常)
        if(0 === count($itemInfoArray)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5006');
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        // パラメータシート項目作成情報を表示順序、項番の昇順に並べ替える
        $dispSeqArray = array();
        $idArray = array();
        foreach ($itemInfoArray as $key => $itemInfo){
            $dispSeqArray[$key] = $itemInfo['DISP_SEQ'];
            $idArray[$key]      = $itemInfo['CREATE_ITEM_ID'];
        }
        array_multisort($dispSeqArray, SORT_ASC, $idArray, SORT_ASC, $itemInfoArray);

        $createConvFlg = false;

        // パラメータシート(縦)を作成する設定の場合
        if("" != $cmiData['VERTICAL']){

            $createConvFlg = true;

            //////////////////////////
            // パラメータシート(縦)作成情報を特定する
            //////////////////////////
            $cpiData = NULL;

            foreach($convertParamInfoArray as $convertParamInfo){

                $searchIdx = array_search($convertParamInfo['CREATE_ITEM_ID'], array_column($itemInfoArray, 'CREATE_ITEM_ID'));

                if(false !== $searchIdx){
                    $cpiData = $convertParamInfo;
                    break;
                }
            }

            if(NULL === $cpiData){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5014');
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }

            //////////////////////////
            // 繰り返し項目を確認する
            //////////////////////////
            $beforeItemArray = array();
            $repeatItemArray = array();
            $afterItemArray = array();
            $startFlg = false;
            $repeatCnt = $cpiData['REPEAT_CNT'];
            $repeatItemCnt = $cpiData['COL_CNT'] * $repeatCnt;

            // 項目を振り分ける
            foreach($itemInfoArray as $itemInfo){
                if($cpiData['CREATE_ITEM_ID'] == $itemInfo['CREATE_ITEM_ID']){
                    $startFlg = true;
                }

                if(false === $startFlg){
                    $beforeItemArray[] = $itemInfo;
                }
                else if(true === $startFlg && 0 < $repeatItemCnt){
                    $repeatItemArray[] = $itemInfo;
                    $repeatItemCnt --;
                }
                else{
                    $afterItemArray[] = $itemInfo;
                }
            }

            // 件数が合わない場合
            if(count($repeatItemArray) != $cpiData['COL_CNT'] * $repeatCnt){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5015');
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }

            // 型、サイズのチェック
            $inputMethodIdArray     = array();
            $maxLengthArray         = array();
            $otherMenuLinkIdArray   = array();
            $multiMaxLengthArray    = array();
            $intMaxArray            = array();
            $intMinArray            = array();
            $floatMaxArray          = array();
            $floatMinArray          = array();
            $floatDigitArray        = array();
            $pwMaxLengthArray       = array();
            $uploadMaxSizeArray     = array();
            $linkLengthArray        = array();
            $errFlg = false;

            for($i = 0; $i < $repeatCnt; $i ++){
                for($j = 0; $j < $cpiData['COL_CNT']; $j ++){

                    if($i === 0){
                        $inputMethodIdArray[]   = $repeatItemArray[$j]['INPUT_METHOD_ID'];
                        $maxLengthArray[]       = $repeatItemArray[$j]['MAX_LENGTH'];
                        $multiMaxLengthArray[]  = $repeatItemArray[$j]['MULTI_MAX_LENGTH'];
                        $intMaxArray[]          = $repeatItemArray[$j]['INT_MAX'];
                        $intMinArray[]          = $repeatItemArray[$j]['INT_MIN'];
                        $floatMaxArray[]        = $repeatItemArray[$j]['FLOAT_MAX'];
                        $floatMinArray[]        = $repeatItemArray[$j]['FLOAT_MIN'];
                        $floatDigitArray[]      = $repeatItemArray[$j]['FLOAT_DIGIT'];
                        $otherMenuLinkIdArray[] = $repeatItemArray[$j]['OTHER_MENU_LINK_ID'];
                        $pwMaxLengthArray[]     = $repeatItemArray[$j]['PW_MAX_LENGTH'];
                        $uploadMaxSizeArray[]   = $repeatItemArray[$j]['UPLOAD_MAX_SIZE'];
                        $linkLengthArray[]      = $repeatItemArray[$j]['LINK_LENGTH'];
                        continue;
                    }

                    // 入力方式チェック
                    if($inputMethodIdArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['INPUT_METHOD_ID']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5016');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 最大バイト数(単一)チェック
                    if(1 == $inputMethodIdArray[$j] && $maxLengthArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['MAX_LENGTH']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 最大バイト数(複数)チェック
                    if(2 == $inputMethodIdArray[$j] && $multiMaxLengthArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['MULTI_MAX_LENGTH']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 整数チェック
                    if(3 == $inputMethodIdArray[$j] && ($intMaxArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['INT_MAX'] || $intMinArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['INT_MIN'])){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 小数チェック
                    if(4 == $inputMethodIdArray[$j] && ($floatMaxArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['FLOAT_MAX'] || $floatMinArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['FLOAT_MIN'] || $floatDigitArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['FLOAT_DIGIT'])){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // プルダウン選択チェック
                    if(7 == $inputMethodIdArray[$j] && $otherMenuLinkIdArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['OTHER_MENU_LINK_ID']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5018');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 最大バイト数(PW)チェック
                    if(8 == $inputMethodIdArray[$j] && $pwMaxLengthArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['PW_MAX_LENGTH']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // ファイル最大バイト数(ファイルアップロード)チェック
                    if(9 == $inputMethodIdArray[$j] && $uploadMaxSizeArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['UPLOAD_MAX_SIZE']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 最大バイト数(リンク)チェック
                    if(10 == $inputMethodIdArray[$j] && $linkLengthArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['LINK_LENGTH']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                }
                if(true === $errFlg){
                    break;
                }
            }
            if(true === $errFlg){
                continue;
            }

            $convertItemInfoArray = $beforeItemArray;
            $repeatItemCount = 0;
            foreach($repeatItemArray as $repeatItemInfo){
                if($repeatItemCount < $cpiData['COL_CNT']){
                    $convertItemInfoArray[] = $repeatItemInfo;
                }else{
                    //リピートで複製されたItemの名前を抽出し格納
                    $duplicateItemNameArray[] = $repeatItemInfo['ITEM_NAME'];
                }
                $repeatItemCount++;
            }
            $convertItemInfoArray = array_merge($convertItemInfoArray, $afterItemArray);
        }

        //////////////////////////
        // 一意制約管理情報を特定する
        //////////////////////////
        $uniqueConstraintTargetArray = array();
        foreach($uniqueConstraintArray as $uniqueData){
            if($targetData['CREATE_MENU_ID'] === $uniqueData['CREATE_MENU_ID']){
                $uniqueConstraintTargetArray[] = $uniqueData;
            }
        }

        //一意制約(複数項目)のloadTableに記載する文字列を生成
        $uniqueConstraintSet = "";
        $noExistUniqueConstraintId = false;
        $noInputMethodId = false;
        if(!empty($uniqueConstraintTargetArray)){
            foreach($uniqueConstraintTargetArray as $uniqueData){
                $uniqueConstraintItemArray = explode(",", $uniqueData['UNIQUE_CONSTRAINT_ITEM']);
                $targetColumnSet = "";
                foreach($uniqueConstraintItemArray as $id){
                    //項目のIDと一意制約対象のIDが一致しているかどうかを判定
                    if(!in_array($id, $idArray)){
                        $noExistUniqueConstraintId = true;
                    }

                    //入力方式が「パラメータシート参照(ID:11)」の場合はエラー判定フラグをtrueに
                    if($itemInputMethodCheckArray[$id] == 11){
                        $noInputMethodId = true;
                    }

                    $columnName = COLUMN_PREFIX . sprintf("%04d", $id);
                    if($targetColumnSet == ""){
                        $targetColumnSet = "'" . $columnName . "'";
                    }else{
                        $targetColumnSet = $targetColumnSet . "," . "'" . $columnName . "'";
                    }
                }
                if($uniqueConstraintSet == ""){
                    $uniqueConstraintSet = '    $table->addUniqueColumnSet(array(' . $targetColumnSet . '));' . "\n";
                }else{
                    $uniqueConstraintSet = $uniqueConstraintSet . '    $table->addUniqueColumnSet(array(' . $targetColumnSet . '));' . "\n";
                }
            }
        }

        //一意制約の対象IDの中に項目のIDと一致しないものがあった場合エラー処理
        if($noExistUniqueConstraintId == true){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5025');
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        //項目の入力方式に「パラメータシート参照」の対象が含まれていた場合エラー処理
        if($noInputMethodId == true){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5027');
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        //////////////////////////
        // ディレクトリ名、テーブル名を決定する
        //////////////////////////
        $menuDirName = sprintf("%04d", $cmiData['CREATE_MENU_ID']);
        $menuTableName = TABLE_PREFIX . sprintf("%04d", $cmiData['CREATE_MENU_ID']);

        //////////////////////////
        // テンプレートの埋め込み部分を設定する
        //////////////////////////
        $columnTypes = "";
        $columns = "";
        $reference = "";
        $hgLoadTableVal = "";
        $hostLoadTableVal = "";
        $viewLoadTableVal = "";
        $cmdbLoadTableVal = "";
        $errFlg = false;
        $itemColumnGrpArrayArray = array();

        //追加するカラム名の一覧
        $columnNameListArray = array();

        // 項目の件数分ループ
        foreach ($itemInfoArray as &$itemInfo){
            // カラム名を決定する
            $itemInfo['COLUMN_NAME'] = COLUMN_PREFIX . sprintf("%04d", $itemInfo['CREATE_ITEM_ID']);

            //$columnNameListArrayにカラム情報を格納
            $columnNameListArray[$itemInfo['COLUMN_NAME']] = array('COLUMN_NAME'=>$itemInfo['COLUMN_NAME'], 
                                                                   'INPUT_METHOD_ID'=>$itemInfo['INPUT_METHOD_ID'], 
                                                                   'CREATE_ITEM_ID'=>$itemInfo['CREATE_ITEM_ID']
                                                             );

            // カラムグループを決定する
            $columnGroupSplit = array();
            if("" != $itemInfo['COL_GROUP_ID']){
                $columnGroupIdx = array_search($itemInfo['COL_GROUP_ID'], array_column($columnGroupArray, 'COL_GROUP_ID'));

                // カラムグループが特定できなかった場合、完了(異常)
                if(false === $columnGroupIdx){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5011', array($itemInfo['CREATE_ITEM_ID']));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, false, true);
                    $errFlg = true;
                    break;
                }
                $columnGroupSplit = explode("/", str_replace("'", "\'", $columnGroupArray[$columnGroupIdx]['FULL_COL_GROUP_NAME']));
            }
            $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']] = $columnGroupSplit;

            // 別々の入力方法のDBカラムタイプを指定
            switch($itemInfo['INPUT_METHOD_ID']){
                case 1: //文字列(単一行)
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                    break;
                case 2: //文字列(複数行)
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                    break;
                case 3: //整数
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                    break;
                case 4: //小数
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    DOUBLE,\n";
                    break;
                case 5: //日時
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    DATETIME(6),\n";
                    break;
                case 6: //日付
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    DATETIME(6),\n";
                    break;
                case 7: //プルダウン
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                    if(!empty($itemInfo['REFERENCE_ITEM'])){
                        $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                        $referenceCount1 = 0;
                        foreach($aryReferenceItem as $id){
                            $referenceCount1++;
                            $reference = $reference . "TAB_A." . $itemInfo['COLUMN_NAME'] . " " . $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount1 . ",\n";
                        }
                    }
                    break;
                case 8: //文字列(PW)
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                    break;
                case 9: //ファイルアップロード
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                    break;
                case 10://リンク
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                    break;
                case 11://パラメータシート参照
                    $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                    break;
            }

            if($itemInfo['INPUT_METHOD_ID'] == 11){
                //項目が「パラメータシート参照」の場合、値は『TAB_A.OPERATION_ID AS COLUMN_NAME_CLONE_1』とする
                $columns = $columns . "       TAB_A.OPERATION_ID AS " . $itemInfo['COLUMN_NAME'] . "_CLONE_1" . ",\n";
            }else{
                $columns = $columns . "       TAB_A." . $itemInfo['COLUMN_NAME'] . ",\n";
            }

            // 「'」がある場合は「\'」に変換する
            $description    = str_replace("'", "\'", $itemInfo['DESCRIPTION']);
            $itemName       = str_replace("'", "\'", $itemInfo['ITEM_NAME']);
                       
            if("2" == $cmiData['TARGET']){  // 作成対象; データシート用
                // データシート用loadTableのカラム埋め込み部分を作成する
                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $work = $partSingle;    // 文字列(単一行)
                        break;
                    case 2:
                        $work = $partMulti;     // 文字列(複数行)
                        break;
                    case 3:
                        $work = $partInteger;   // 整数
                        break;
                    case 4:
                        $work = $partFloat;     // 小数
                        break;
                    case 5:
                        $work = $partDateTime;  // 日時
                        break;
                    case 6:
                        $work = $partDate;  // 日付
                        break;
                    case 7:
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break 2;
                        }

                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
                            $work = $partId;     //プルダウン選択(IDColumn)
                        }else{
                            $work = $partLinkId; //プルダウン選択(LinkIDColumn)
                        }

                        //参照項目がある場合
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $work_ref = "";
                            $referenceCount2 = 0;
                            foreach($aryReferenceItem as $id){
                                $repracePassword = "";
                                $repraceDateFormat = "null";
                                $work_ref_tmpl = $partReference;
                                $referenceCount2++;

                                //カラムグループに追加
                                $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2] = $columnGroupSplit;
                                //対象の参照項目情報
                                $referenceItemInfo = $referenceItemArray[$id];

                                //パスワード表示
                                if($referenceItemInfo['SENSITIVE_FLAG'] == 2){
                                    $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_journal_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                }

                                //日時表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 5){
                                    $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                }

                                //日付表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 6){
                                    $repraceDateFormat = '\'Y/m/d\'';
                                }

                                // 「'」がある場合は「\'」に変換する
                                $cloneItemName = str_replace("'", "\'", $referenceItemInfo['ITEM_NAME']);
                                $cloneDescription = str_replace("'", "\'", $referenceItemInfo['DESCRIPTION']);

                                $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $referenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $referenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $referenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                $work_ref = $work_ref . $work_ref_tmpl;
                            }
                            $work = $work . $work_ref;
                        }
                        break;
                    case 8:
                        $work = $partPassword;  // 文字列(PW)
                        break;
                    case 9:
                        $work = $partUpload;    // ファイルアップロード
                        break;
                    case 10:
                        $work = $partLink;      // リンク
                        break;
                    case 11:
                        //データシートは入力方式(11)を利用できないためエラー処理
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5026');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break 2;
                }
                $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                $work = str_replace(REPLACE_INFO,   $description,               $work);
                if("" != $itemInfo['PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                    $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_PREG, "", $work);
                }
                if("" != $itemInfo['MULTI_PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);
                    $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                }
                $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                if(1 == $itemInfo['REQUIRED']){
                    $work = str_replace(REPLACE_REQUIRED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setRequired(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_REQUIRED, "", $work);
                }
                if(1 == $itemInfo['UNIQUED']){
                    $work = str_replace(REPLACE_UNIQUED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setUnique(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_UNIQUED, "", $work);
                }
                // 他メニュー参照の場合
                if(7 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    if($matchIdx === FALSE){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    if(5 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                    //LinkIDColumn用のurl
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                    $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);

                    //登録時の初期値
                    if("" == $itemInfo['PULLDOWN_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $pulldownDefaultValue = $itemInfo['PULLDOWN_DEFAULT_VALUE'];
                        $pulldownDefaultValue = str_replace('\'', '\\\'', $pulldownDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $pulldownDefaultValue , $work);
                    }
                }
                // 文字列(単一行)の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['SINGLE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $singleDefaultValue = $itemInfo['SINGLE_DEFAULT_VALUE'];
                        $singleDefaultValue = str_replace('\'', '\\\'', $singleDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $singleDefaultValue , $work);
                    }
                }
                // 文字列(複数行)の場合
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['MULTI_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $multiDefaultValue = $itemInfo['MULTI_DEFAULT_VALUE'];
                        $multiDefaultValue = str_replace('\'', '\\\'', $multiDefaultValue); //シングルクォーテーションをエスケープ
                        $multiDefaultValue = str_replace(array("\r", "\n"), '\'."\n".\'', $multiDefaultValue); //改行コードを文字列の「\n」に変換し左右に結合する。
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $multiDefaultValue , $work);
                    }
                }
                // 整数の場合
                if(3 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['INT_MAX']){
                        $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                    }
                    if("" == $itemInfo['INT_MIN']){
                        $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['INT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['INT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 小数の場合
                if(4 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['FLOAT_MAX']){
                        $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                    }
                    if("" == $itemInfo['FLOAT_MIN']){
                        $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                    }
                    if("" == $itemInfo['FLOAT_DIGIT']){
                        $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['FLOAT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['FLOAT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 日時の場合
                if(5 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATETIME_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $datetimeFormat = "";
                        $datetimeDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATETIME_DEFAULT_VALUE']);
                        if($datetimeDefaultValue != false) $datetimeFormat = $datetimeDefaultValue->format('Y/m/d H:i:s');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $datetimeFormat , $work);
                    }
                }
                // 日付の場合
                if(6 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $dateFormat = "";
                        $dateDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATE_DEFAULT_VALUE']);
                        if($dateDefaultValue != false) $dateFormat = $dateDefaultValue->format('Y/m/d');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $dateFormat , $work);
                    }
                }
                // リンクの場合
                if(10 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['LINK_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $linkDefaultValue = $itemInfo['LINK_DEFAULT_VALUE'];
                        $linkDefaultValue = str_replace('\'', '\\\'', $linkDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $linkDefaultValue , $work);
                    }
                }
                $cmdbLoadTableVal .= $work . "\n";
            }
            else{
                if("2" == $cmiData['PURPOSE']){
                    // ホストグループ用loadTableのカラム埋め込み部分を作成する
                    switch($itemInfo['INPUT_METHOD_ID']){
                        case 1:
                            $work = $partSingle;    // 文字列(単一行)
                            break;
                        case 2:
                            $work = $partMulti;     // 文字列(複数行)
                            break;
                        case 3:
                            $work = $partInteger;   // 整数
                            break;
                        case 4:
                            $work = $partFloat;     // 小数
                            break;
                        case 5:
                            $work = $partDateTime;  // 日時
                            break;
                        case 6:
                            $work = $partDate;  // 日付
                            break;
                        case 7:
                            $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                            if($matchIdx === FALSE){
                                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                                outputLog($msg);
                                // パラメータシート作成管理更新処理を行う
                                updateMenuStatus($targetData, "4", $msg, false, true);
                                $errFlg = true;
                                break 2;
                            }

	                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
	                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
	                            $work = $partId;     //プルダウン選択(IDColumn)
	                        }else{
	                            $work = $partLinkId; //プルダウン選択(LinkIDColumn)
	                        }

                            //参照項目がある場合
                            if(!empty($itemInfo['REFERENCE_ITEM'])){
                                //参照項目をリピートする場合、項目名の末尾に追加する[x]を抽出
                                $extractRepeatNoStr = "";
                                if(in_array($itemInfo['ITEM_NAME'], $duplicateItemNameArray)){
                                    $extractRepeatNoStr = extractRepeatItemNo($itemInfo['ITEM_NAME']);
                                }

                                $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                                $work_ref = "";
                                $referenceCount2 = 0;
                                foreach($aryReferenceItem as $id){
                                    $repracePassword = "";
                                    $repraceDateFormat = "null";
                                    $work_ref_tmpl = $partReference;
                                    $referenceCount2++;

                                    //カラムグループに追加
                                    $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2] = $columnGroupSplit;
                                    //対象の参照項目情報
                                    $referenceItemInfo = $referenceItemArray[$id];

                                    //パスワード表示
                                    if($referenceItemInfo['SENSITIVE_FLAG'] == 2){
                                        $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_journal_table", $outputType);' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                    }

                                    //日時表示
                                    if($referenceItemInfo['INPUT_METHOD_ID'] == 5){
                                        $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                    }

                                    //日付表示
                                    if($referenceItemInfo['INPUT_METHOD_ID'] == 6){
                                        $repraceDateFormat = '\'Y/m/d\'';
                                    }

                                    // 「'」がある場合は「\'」に変換する
                                    $cloneItemName = str_replace("'", "\'", $referenceItemInfo['ITEM_NAME']);
                                    $cloneDescription = str_replace("'", "\'", $referenceItemInfo['DESCRIPTION']);

                                    $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2, $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount2, $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName . $extractRepeatNoStr, $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $referenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $referenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $referenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                    $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                    $work_ref = $work_ref . $work_ref_tmpl;
                                }
                                $work = $work . $work_ref;
                            }
                            break;
                        case 8:
                            $work = $partPassword;  // 文字列(PW)
                            break;
                        case 9:
                            $work = $partUpload;    // ファイルアップロード
                            break;
                        case 10:
                            $work = $partLink;      // リンク
                            break;
                        case 11:
                            $work = $partType3Reference;       // パラメータシート参照
                            break;
                    }  

                    $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                    $work = str_replace(REPLACE_INFO,   $description,               $work);
                    if("" != $itemInfo['PREG_MATCH']){
                        $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                        $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                    }
                    else{
                        $work = str_replace(REPLACE_PREG, "", $work);
                    }
                    if("" != $itemInfo['MULTI_PREG_MATCH']){
                        $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);
                        $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                    }
                    else{
                        $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                    }
                    if(11 == $itemInfo['INPUT_METHOD_ID']){
                        //パラメータシート参照の場合カラム名に_CLONE_1を追記
                        $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'] . '_CLONE_1',      $work);
                    }else{
                        $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                    }
                    $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                    $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                    $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                    $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                    $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                    $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                    if(1 == $itemInfo['REQUIRED']){
                        $work = str_replace(REPLACE_REQUIRED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setRequired(true);", $work);
                    }
                    else{
                        $work = str_replace(REPLACE_REQUIRED, "", $work);
                    }
                    if(1 == $itemInfo['UNIQUED']){
                        $work = str_replace(REPLACE_UNIQUED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setUnique(true);", $work);
                    }
                    else{
                        $work = str_replace(REPLACE_UNIQUED, "", $work);
                    }
                    // 他メニュー参照の場合
                    if(7 == $itemInfo['INPUT_METHOD_ID']){
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break;
                        }
                        $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                        if(5 == $otherMenuLink['COLUMN_TYPE']){
                            $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                        }
                        else if(6 == $otherMenuLink['COLUMN_TYPE']){
                            $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                        }
                        else{
                            $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                        }
                        $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                        $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                        $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                        //LinkIDColumn用のurl
                        $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                        $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                        $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                        $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                        $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                        $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                        $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);

                        //登録時の初期値
                        if("" == $itemInfo['PULLDOWN_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $pulldownDefaultValue = $itemInfo['PULLDOWN_DEFAULT_VALUE'];
                            $pulldownDefaultValue = str_replace('\'', '\\\'', $pulldownDefaultValue); //シングルクォーテーションをエスケープ
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $pulldownDefaultValue , $work);
                        }
                    }
                    // 文字列(単一行)の場合
                    if(1 == $itemInfo['INPUT_METHOD_ID']){
                        //登録時の初期値
                        if("" == $itemInfo['SINGLE_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $singleDefaultValue = $itemInfo['SINGLE_DEFAULT_VALUE'];
                            $singleDefaultValue = str_replace('\'', '\\\'', $singleDefaultValue); //シングルクォーテーションをエスケープ
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $singleDefaultValue , $work);
                        }
                    }
                    // 文字列(複数行)の場合
                    if(2 == $itemInfo['INPUT_METHOD_ID']){
                        //登録時の初期値
                        if("" == $itemInfo['MULTI_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $multiDefaultValue = $itemInfo['MULTI_DEFAULT_VALUE'];
                            $multiDefaultValue = str_replace('\'', '\\\'', $multiDefaultValue); //シングルクォーテーションをエスケープ
                            $multiDefaultValue = str_replace(array("\r", "\n"), '\'."\n".\'', $multiDefaultValue); //改行コードを文字列の「\n」に変換し左右に結合する。
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $multiDefaultValue , $work);
                        }
                    }
                    // 整数の場合
                    if(3 == $itemInfo['INPUT_METHOD_ID']){
                        if("" == $itemInfo['INT_MAX']){
                            $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                        }
                        if("" == $itemInfo['INT_MIN']){
                            $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                        }
                        //登録時の初期値
                        if("" == $itemInfo['INT_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['INT_DEFAULT_VALUE'] , $work);
                        }
                    }
                    // 小数の場合
                    if(4 == $itemInfo['INPUT_METHOD_ID']){
                        if("" == $itemInfo['FLOAT_MAX']){
                            $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                        }
                        if("" == $itemInfo['FLOAT_MIN']){
                            $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                        }
                        if("" == $itemInfo['FLOAT_DIGIT']){
                            $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                        }
                        //登録時の初期値
                        if("" == $itemInfo['FLOAT_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['FLOAT_DEFAULT_VALUE'] , $work);
                        }
                    }
                    // 日時の場合
                    if(5 == $itemInfo['INPUT_METHOD_ID']){
                        //登録時の初期値
                        if("" == $itemInfo['DATETIME_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $datetimeFormat = "";
                            $datetimeDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATETIME_DEFAULT_VALUE']);
                            if($datetimeDefaultValue != false) $datetimeFormat = $datetimeDefaultValue->format('Y/m/d H:i:s');
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $datetimeFormat , $work);
                        }
                    }
                    // 日付の場合
                    if(6 == $itemInfo['INPUT_METHOD_ID']){
                        //登録時の初期値
                        if("" == $itemInfo['DATE_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $dateFormat = "";
                            $dateDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATE_DEFAULT_VALUE']);
                            if($dateDefaultValue != false) $dateFormat = $dateDefaultValue->format('Y/m/d');
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $dateFormat , $work);
                        }
                    }
                    // リンクの場合
                    if(10 == $itemInfo['INPUT_METHOD_ID']){
                        //登録時の初期値
                        if("" == $itemInfo['LINK_DEFAULT_VALUE']){
                            $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                        }
                        else{
                            $linkDefaultValue = $itemInfo['LINK_DEFAULT_VALUE'];
                            $linkDefaultValue = str_replace('\'', '\\\'', $linkDefaultValue); //シングルクォーテーションをエスケープ
                            $work = str_replace(REPLACE_DEFAULT_VALUE, $linkDefaultValue , $work);
                        }
                    }
                    // パラメータシート参照の場合
                    if(11 == $itemInfo['INPUT_METHOD_ID']){
                        $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                        if(empty($type3ReferenceData)){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break;
                        }

                        //参照先のテーブル名をREPLACE
                        $work = str_replace(REPLACE_ID_TABLE,   $type3ReferenceData['TABLE_NAME'],   $work);

                        //参照先のカラム名をREPLACE
                        $work = str_replace(REPLACE_ID_COL,     $type3ReferenceData['COLUMN_NAME'],  $work);

                        //LinkIDColumn用のurlをREPLACE
                        $url1 = '01_browse.php?no=' . sprintf('%010d', $type3ReferenceData['MENU_ID']) . '&filter=on&';
                        $url2 = str_replace('/', '\\', $type3ReferenceData['COL_TITLE']);
                        $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                        $url2 = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . '\\' . $url2; //COL_TITLEの頭に「パラメータ\」を追加
                        $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                        $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);

                        //DATE_FORMATをREPLACE
                        if(5 == $type3ReferenceData['INPUT_METHOD_ID']){
                            $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                        }
                        else if(6 == $type3ReferenceData['INPUT_METHOD_ID']){
                            $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                        }
                        else{
                            $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                        }

                        //参照元がパスワードカラムの場合、マスク処理を追記
                        $repracePassword = '';
                        if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                            $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_table", $outputType);' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_journal_table", $outputType);' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("excel", $outputType2);' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("json", $outputType2);' . "\n";
                            $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->getOutputType("filter_table")->setVisible(false);';
                        }
                        $work = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work);
                    }
                    $hgLoadTableVal .= $work . "\n";
                }

                // ホスト用loadTableのカラム埋め込み部分を作成する
                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $work = $partSingle;    // 文字列(単一行)
                        break;
                    case 2:
                        $work = $partMulti;     // 文字列(複数行)
                        break;
                    case 3:
                        $work = $partInteger;   // 整数
                        break;
                    case 4:
                        $work = $partFloat;     // 小数
                        break;
                    case 5:
                        $work = $partDateTime;  // 日時
                        break;
                    case 6:
                        $work = $partDate;  // 日付
                        break;
                    case 7:
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break 2;
                        }

                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
                            $work = $partId;     //プルダウン選択(IDColumn)
                        }else{
                            $work = $partLinkId; //プルダウン選択(LinkIDColumn)
                        }

                        //参照項目がある場合
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            //参照項目をリピートする場合、項目名の末尾に追加する[x]を抽出
                            $extractRepeatNoStr = "";
                            if(in_array($itemInfo['ITEM_NAME'], $duplicateItemNameArray)){
                                $extractRepeatNoStr = extractRepeatItemNo($itemInfo['ITEM_NAME']);
                            }

                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $work_ref = "";
                            $referenceCount2 = 0;
                            foreach($aryReferenceItem as $id){
                                $repracePassword = "";
                                $repraceDateFormat = "null";
                                $work_ref_tmpl = $partReference;
                                $referenceCount2++;

                                //カラムグループに追加
                                $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2] = $columnGroupSplit;
                                //対象の参照項目情報
                                $referenceItemInfo = $referenceItemArray[$id];

                                //パスワード表示
                                if($referenceItemInfo['SENSITIVE_FLAG'] == 2){
                                    $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_journal_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                }

                                //日時表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 5){
                                    $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                }

                                //日付表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 6){
                                    $repraceDateFormat = '\'Y/m/d\'';
                                }

                                // 「'」がある場合は「\'」に変換する
                                $cloneItemName = str_replace("'", "\'", $referenceItemInfo['ITEM_NAME']);
                                $cloneDescription = str_replace("'", "\'", $referenceItemInfo['DESCRIPTION']);

                                $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName . $extractRepeatNoStr, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $referenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $referenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $referenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                $work_ref = $work_ref . $work_ref_tmpl;
                            }
                            $work = $work . $work_ref;
                        }

                        break;
                    case 8:
                        $work = $partPassword;  // 文字列(PW)
                        break;
                    case 9:
                        $work = $partUpload;    // ファイルアップロード
                        break;
                    case 10:
                        $work = $partLink;      // リンク
                        break;
                    case 11:
                        $work = $partType3Reference;       // パラメータシート参照
                        break;
                }

                $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                $work = str_replace(REPLACE_INFO,   $description,               $work);
                if("" != $itemInfo['PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                    $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_PREG, "", $work);
                }
                if("" != $itemInfo['MULTI_PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);
                    $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                }
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    //パラメータシート参照の場合カラム名に_CLONE_1を追記
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'] . '_CLONE_1',      $work);
                }else{
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                }
                $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                if(1 == $itemInfo['REQUIRED']){
                    $work = str_replace(REPLACE_REQUIRED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setRequired(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_REQUIRED, "", $work);
                }

                if(1 == $itemInfo['UNIQUED'] && "2" != $cmiData['PURPOSE']){
                    $work = str_replace(REPLACE_UNIQUED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setUnique(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_UNIQUED, "", $work);
                }
                // 他メニュー参照の場合
                if(7 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    if($matchIdx === FALSE){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    if(5 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }          
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                    //LinkIDColumn用のurl
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                    $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);

                    //登録時の初期値
                    if("" == $itemInfo['PULLDOWN_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $pulldownDefaultValue = $itemInfo['PULLDOWN_DEFAULT_VALUE'];
                        $pulldownDefaultValue = str_replace('\'', '\\\'', $pulldownDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $pulldownDefaultValue , $work);
                    }
                }
                // 文字列(単一行)の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['SINGLE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $singleDefaultValue = $itemInfo['SINGLE_DEFAULT_VALUE'];
                        $singleDefaultValue = str_replace('\'', '\\\'', $singleDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $singleDefaultValue , $work);
                    }
                }
                // 文字列(複数行)の場合
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['MULTI_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $multiDefaultValue = $itemInfo['MULTI_DEFAULT_VALUE'];
                        $multiDefaultValue = str_replace('\'', '\\\'', $multiDefaultValue); //シングルクォーテーションをエスケープ
                        $multiDefaultValue = str_replace(array("\r", "\n"), '\'."\n".\'', $multiDefaultValue); //改行コードを文字列の「\n」に変換し左右に結合する。
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $multiDefaultValue , $work);
                    }
                }
                // 整数の場合
                if(3 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['INT_MAX']){
                        $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                    }
                    if("" == $itemInfo['INT_MIN']){
                        $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['INT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['INT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 小数の場合
                if(4 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['FLOAT_MAX']){
                        $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                    }
                    if("" == $itemInfo['FLOAT_MIN']){
                        $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                    }
                    if("" == $itemInfo['FLOAT_DIGIT']){
                        $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['FLOAT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['FLOAT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 日時の場合
                if(5 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATETIME_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $datetimeFormat = "";
                        $datetimeDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATETIME_DEFAULT_VALUE']);
                        if($datetimeDefaultValue != false) $datetimeFormat = $datetimeDefaultValue->format('Y/m/d H:i:s');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $datetimeFormat , $work);
                    }
                }
                // 日付の場合
                if(6 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $dateFormat = "";
                        $dateDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATE_DEFAULT_VALUE']);
                        if($dateDefaultValue != false) $dateFormat = $dateDefaultValue->format('Y/m/d');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $dateFormat , $work);
                    }
                }
                // リンクの場合
                if(10 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['LINK_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $linkDefaultValue = $itemInfo['LINK_DEFAULT_VALUE'];
                        $linkDefaultValue = str_replace('\'', '\\\'', $linkDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $linkDefaultValue , $work);
                    }
                }
                // パラメータシート参照の場合
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                    if(empty($type3ReferenceData)){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    //参照先のテーブル名をREPLACE
                    $work = str_replace(REPLACE_ID_TABLE,   $type3ReferenceData['TABLE_NAME'],   $work);

                    //参照先のカラム名をREPLACE
                    $work = str_replace(REPLACE_ID_COL,     $type3ReferenceData['COLUMN_NAME'],  $work);

                    //LinkIDColumn用のurlをREPLACE
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $type3ReferenceData['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $type3ReferenceData['COL_TITLE']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $url2 = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . '\\' . $url2; //COL_TITLEの頭に「パラメータ\」を追加
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);

                    //DATE_FORMATをREPLACE
                    if(5 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }

                    //参照元がパスワードカラムの場合、マスク処理を追記
                    $repracePassword = '';
                    if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_journal_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("excel", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("json", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->getOutputType("filter_table")->setVisible(false);';
                    }
                    $work = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work);
                }
                $hostLoadTableVal .= $work . "\n";

                // 最新値参照用loadTableのカラム埋め込み部分を作成する
                // 文字列の場合
                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $work = $partViewSingle;    // 文字列(単一行)
                        break;
                    case 2:
                        $work = $partViewMulti;     // 文字列(複数行)
                        break;
                    case 3:
                        $work = $partViewInteger;   // 整数
                        break;
                    case 4:
                        $work = $partViewFloat;     // 小数
                        break;
                    case 5:
                        $work = $partViewDateTime;  // 日時
                        break;
                    case 6:
                        $work = $partViewDate;      // 日付
                        break;
                    case 7:
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break 2;
                        }

                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
                            $work = $partViewId;      //プルダウン選択(IDColumn)
                        }else{
                            $work = $partViewLinkId; //プルダウン選択(LinkIDColumn)
                        }

                        //参照項目がある場合
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $work_ref = "";
                            $referenceCount2 = 0;
                            foreach($aryReferenceItem as $id){
                                $repracePassword = "";
                                $repraceDateFormat = "null";
                                $work_ref_tmpl = $partViewReference;
                                $referenceCount2++;

                                //カラムグループに追加
                                $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2] = $columnGroupSplit;
                                //対象の参照項目情報
                                $referenceItemInfo = $referenceItemArray[$id];

                                //パスワード表示
                                if($referenceItemInfo['SENSITIVE_FLAG'] == 2){
                                    $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("print_journal_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $referenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                }

                                //日時表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 5){
                                    $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                }

                                //日付表示
                                if($referenceItemInfo['INPUT_METHOD_ID'] == 6){
                                    $repraceDateFormat = '\'Y/m/d\'';
                                }

                                // 「'」がある場合は「\'」に変換する
                                $cloneItemName = str_replace("'", "\'", $referenceItemInfo['ITEM_NAME']);
                                $cloneDescription = str_replace("'", "\'", $referenceItemInfo['DESCRIPTION']);

                                $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $referenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $referenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $referenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                $work_ref = $work_ref . $work_ref_tmpl;
                            }
                            $work = $work . $work_ref;
                        }

                        break;
                    case 8:
                        $work = $partViewPassword;  // 文字列(PW)
                        break;
                    case 9:
                        $work = $partViewUpload;    // ファイルアップロード
                        break;
                    case 10:
                        $work = $partViewLink;      // リンク
                        break;
                    case 11:
                        $work = $partViewType3Reference;       // パラメータシート参照
                        break;
                }

                $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                $work = str_replace(REPLACE_INFO,   $description,               $work);
                if("" != $itemInfo['PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                    $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_PREG, "", $work);
                }
                if("" != $itemInfo['MULTI_PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);
                    $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                }
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    //パラメータシート参照の場合カラム名に_CLONE_1を追記
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'] . '_CLONE_1',      $work);
                }else{
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                }
                $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                // 他メニュー参照の場合
                if(7 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    if($matchIdx === FALSE){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    if(5 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }            
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                    //LinkIDColumn用のurl
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                    $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);
                }
                // 整数の場合
                if(3 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['INT_MAX']){
                        $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                    }
                    if("" == $itemInfo['INT_MIN']){
                        $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                    }
                }
                // 小数の場合
                if(4 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['FLOAT_MAX']){
                        $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                    }
                    if("" == $itemInfo['FLOAT_MIN']){
                        $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                    }
                    if("" == $itemInfo['FLOAT_DIGIT']){
                        $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                    }
                }
                // パラメータシート参照の場合
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                    if(empty($type3ReferenceData)){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    //参照先のテーブル名をREPLACE
                    $work = str_replace(REPLACE_ID_TABLE,   $type3ReferenceData['TABLE_NAME'],   $work);

                    //参照先のカラム名をREPLACE
                    $work = str_replace(REPLACE_ID_COL,     $type3ReferenceData['COLUMN_NAME'],  $work);

                    //LinkIDColumn用のurlをREPLACE
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $type3ReferenceData['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $type3ReferenceData['COL_TITLE']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $url2 = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . '\\' . $url2; //COL_TITLEの頭に「パラメータ\」を追加
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);

                    //DATE_FORMATをREPLACE
                    if(5 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }

                    //参照元がパスワードカラムの場合、マスク処理を追記
                    $repracePassword = '';
                    if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_journal_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("excel", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("json", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->getOutputType("filter_table")->setVisible(false);';
                    }
                    $work = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work);
                }
                $viewLoadTableVal .= $work . "\n";
            }
        } 
        
        unset($itemInfo);
        if(true === $errFlg){
            continue;
        }

        // カラムグループ部品組み立て
        $columnGrpParts = makeColumnGrpParts($itemColumnGrpArrayArray, $cmiData['TARGET']);

        // パラメータシート(縦)を作成する設定の場合
        if(true === $createConvFlg){

            //////////////////////////
            // テンプレートの埋め込み部分を設定する
            //////////////////////////
            $convColumnTypes = "";
            $convColumns = "";
            $convReference = "";
            $convertLoadTableVal = "";
            $convertViewLoadTableVal = "";
            $errFlg = false;
            $convItemColumnGrpArrayArray = array();

            //追加するカラム名の一覧
            $convColumnNameListArray = array();

            // 項目の件数分ループ
            foreach ($convertItemInfoArray as &$itemInfo){
                // カラム名を決定する
                $itemInfo['COLUMN_NAME'] = COLUMN_PREFIX . sprintf("%04d", $itemInfo['CREATE_ITEM_ID']);

                if($cpiData['CREATE_ITEM_ID'] == $itemInfo['CREATE_ITEM_ID']){
                    $startColName = $itemInfo['COLUMN_NAME'];
                }

                //$convColumnNameListArrayにカラム情報を格納
                $convColumnNameListArray[$itemInfo['COLUMN_NAME']] = array('COLUMN_NAME'=>$itemInfo['COLUMN_NAME'], 
                                                                       'INPUT_METHOD_ID'=>$itemInfo['INPUT_METHOD_ID'], 
                                                                       'CREATE_ITEM_ID'=>$itemInfo['CREATE_ITEM_ID']
                                                                     );

                // カラムグループを決定する
                $columnGroupSplit = array();
                if("" != $itemInfo['COL_GROUP_ID']){
                    $columnGroupIdx = array_search($itemInfo['COL_GROUP_ID'], array_column($columnGroupArray, 'COL_GROUP_ID'));

                    // カラムグループが特定できなかった場合、完了(異常)
                    if(false === $columnGroupIdx){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5011', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $columnGroupSplit = explode("/", str_replace("'", "\'", $columnGroupArray[$columnGroupIdx]['FULL_COL_GROUP_NAME']));
                }
                $convItemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']] = $columnGroupSplit;

                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                        break;
                    case 2:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                        break;
                    case 3:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                        break;
                    case 4:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    DOUBLE,\n";
                        break;
                    case 5:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    DATETIME(6),\n";
                        break;
                    case 6:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    DATETIME(6),\n";
                        break;
                    case 7:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $convReferenceCount1 = 0;
                            foreach($aryReferenceItem as $id){
                                $convReferenceCount1++;
                                $convReference = $convReference . "TAB_A." . $itemInfo['COLUMN_NAME'] . " " . $itemInfo['COLUMN_NAME'] . "_CLONE_" . $convReferenceCount1 . ",\n";
                            }
                        }
                        break;
                    case 8:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                        break;
                    case 9:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                        break;
                    case 10:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    TEXT,\n";
                        break;
                    case 11:
                        $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                        break;
                }
        
                if($itemInfo['INPUT_METHOD_ID'] == 11){
                    //項目が「パラメータシート参照」の場合、値は『TAB_A.OPERATION_ID AS COLUMN_NAME_CLONE_1』とする
                    $convColumns = $convColumns . "       TAB_A.OPERATION_ID AS " . $itemInfo['COLUMN_NAME'] . "_CLONE_1" . ",\n";
                }else{
                    $convColumns = $convColumns . "       TAB_A." . $itemInfo['COLUMN_NAME'] . ",\n";
                }

                // 「'」がある場合は「\'」に変換する
                $description    = str_replace("'", "\'", $itemInfo['DESCRIPTION']);
                $itemName       = str_replace("'", "\'", $itemInfo['ITEM_NAME']);

                // loadTableのカラム埋め込み部分を作成する
                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $work = $partSingle;    // 文字列（単一行）
                        break;
                    case 2:
                        $work = $partMulti;     // 文字列（複数行）
                        break;
                    case 3:
                        $work = $partInteger;   // 整数
                        break;
                    case 4:
                        $work = $partFloat;     // 小数
                        break;
                    case 5:
                        $work = $partDateTime;  // 日時
                        break;
                    case 6:
                        $work = $partDate;  // 日付
                        break;
                    case 7:
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break 2;
                        }

                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
                            $work = $partId;     //プルダウン選択(IDColumn)
                        }else{
                            $work = $partLinkId; //プルダウン選択(LinkIDColumn)
                        }

                        //参照項目がある場合
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $work_ref = "";
                            $convReferenceCount2 = 0;
                            foreach($aryReferenceItem as $id){
                                $repracePassword = "";
                                $repraceDateFormat = "null";
                                $work_ref_tmpl = $partReference;
                                $convReferenceCount2++;

                                //カラムグループに追加
                                $convItemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $convReferenceCount2] = $columnGroupSplit;
                                //対象の参照項目情報
                                $convReferenceItemInfo = $referenceItemArray[$id];

                                //パスワード表示
                                if($convReferenceItemInfo['SENSITIVE_FLAG'] == 2){
                                    $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("print_journal_table", $outputType);';
                                    $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                }

                                //日時表示
                                if($convReferenceItemInfo['INPUT_METHOD_ID'] == 5){
                                    $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                }

                                //日付表示
                                if($convReferenceItemInfo['INPUT_METHOD_ID'] == 6){
                                    $repraceDateFormat = '\'Y/m/d\'';
                                }

                                // 「'」がある場合は「\'」に変換する
                                $cloneItemName = str_replace("'", "\'", $convReferenceItemInfo['ITEM_NAME']);
                                $cloneDescription = str_replace("'", "\'", $convReferenceItemInfo['DESCRIPTION']);

                                $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $convReferenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $convReferenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $convReferenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $convReferenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $convReferenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                $work_ref = $work_ref . $work_ref_tmpl;
                            }
                            $work = $work . $work_ref;
                        }
                        break;
                    case 8:
                        $work = $partPassword;  // 文字列（PW）
                        break;
                    case 9:
                        $work = $partUpload;    // ファイルアップロード
                        break;
                    case 10:
                        $work = $partLink;      // リンク
                        break;
                    case 11:
                        $work = $partType3Reference;       // パラメータシート参照
                        break;
                }

                $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                $work = str_replace(REPLACE_INFO, $description,               $work);
                if("" != $itemInfo['PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);
                    $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_PREG, "", $work);
                }
                if("" != $itemInfo['MULTI_PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);
                    $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                }
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    //パラメータシート参照の場合カラム名に_CLONE_1を追記
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'] . '_CLONE_1',      $work);
                }else{
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                }
                $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                if(1 == $itemInfo['REQUIRED']){
                    $work = str_replace(REPLACE_REQUIRED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setRequired(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_REQUIRED, "", $work);
                }
                if(1 == $itemInfo['UNIQUED']){
                    $work = str_replace(REPLACE_UNIQUED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setUnique(true);", $work);
                }
                else{
                    $work = str_replace(REPLACE_UNIQUED, "", $work);
                }
                // 他メニュー参照の場合
                if(7 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    if($matchIdx === FALSE){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    if(5 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                    //LinkIDColumn用のurl
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                    $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);

                    //登録時の初期値
                    if("" == $itemInfo['PULLDOWN_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $pulldownDefaultValue = $itemInfo['PULLDOWN_DEFAULT_VALUE'];
                        $pulldownDefaultValue = str_replace('\'', '\\\'', $pulldownDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $pulldownDefaultValue , $work);
                    }
                }
                // 文字列(単一行)の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['SINGLE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $singleDefaultValue = $itemInfo['SINGLE_DEFAULT_VALUE'];
                        $singleDefaultValue = str_replace('\'', '\\\'', $singleDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $singleDefaultValue , $work);
                    }
                }
                // 文字列(複数行)の場合
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['MULTI_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $multiDefaultValue = $itemInfo['MULTI_DEFAULT_VALUE'];
                        $multiDefaultValue = str_replace('\'', '\\\'', $multiDefaultValue); //シングルクォーテーションをエスケープ
                        $multiDefaultValue = str_replace(array("\r", "\n"), '\'."\n".\'', $multiDefaultValue); //改行コードを文字列の「\n」に変換し左右に結合する。
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $multiDefaultValue , $work);
                    }
                }
                // 整数の場合
                if(3 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['INT_MAX']){
                        $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                    }
                    if("" == $itemInfo['INT_MIN']){
                        $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['INT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['INT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 小数の場合
                if(4 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['FLOAT_MAX']){
                        $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                    }
                    if("" == $itemInfo['FLOAT_MIN']){
                        $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                    }
                    if("" == $itemInfo['FLOAT_DIGIT']){
                        $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                    }
                    //登録時の初期値
                    if("" == $itemInfo['FLOAT_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $itemInfo['FLOAT_DEFAULT_VALUE'] , $work);
                    }
                }
                // 日時の場合
                if(5 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATETIME_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $datetimeFormat = "";
                        $datetimeDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATETIME_DEFAULT_VALUE']);
                        if($datetimeDefaultValue != false) $datetimeFormat = $datetimeDefaultValue->format('Y/m/d H:i:s');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $datetimeFormat , $work);
                    }
                }
                // 日付の場合
                if(6 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['DATE_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $dateFormat = "";
                        $dateDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfo['DATE_DEFAULT_VALUE']);
                        if($dateDefaultValue != false) $dateFormat = $dateDefaultValue->format('Y/m/d');
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $dateFormat , $work);
                    }
                }
                // リンクの場合
                if(10 == $itemInfo['INPUT_METHOD_ID']){
                    //登録時の初期値
                    if("" == $itemInfo['LINK_DEFAULT_VALUE']){
                        $work = str_replace(REPLACE_DEFAULT_VALUE, '' , $work);
                    }
                    else{
                        $linkDefaultValue = $itemInfo['LINK_DEFAULT_VALUE'];
                        $linkDefaultValue = str_replace('\'', '\\\'', $linkDefaultValue); //シングルクォーテーションをエスケープ
                        $work = str_replace(REPLACE_DEFAULT_VALUE, $linkDefaultValue , $work);
                    }
                }
                // パラメータシート参照の場合
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                    if(empty($type3ReferenceData)){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    //参照先のテーブル名をREPLACE
                    $work = str_replace(REPLACE_ID_TABLE,   $type3ReferenceData['TABLE_NAME'],   $work);

                    //参照先のカラム名をREPLACE
                    $work = str_replace(REPLACE_ID_COL,     $type3ReferenceData['COLUMN_NAME'],  $work);

                    //LinkIDColumn用のurlをREPLACE
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $type3ReferenceData['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $type3ReferenceData['COL_TITLE']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $url2 = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . '\\' . $url2; //COL_TITLEの頭に「パラメータ\」を追加
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);

                    //DATE_FORMATをREPLACE
                    if(5 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }

                    //参照元がパスワードカラムの場合、マスク処理を追記
                    $repracePassword = '';
                    if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_journal_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("excel", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("json", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->getOutputType("filter_table")->setVisible(false);';
                    }
                    $work = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work);
                }
                $convertLoadTableVal .= $work . "\n";

                // 最新値参照用loadTableのカラム埋め込み部分を作成する
                switch($itemInfo['INPUT_METHOD_ID']){
                    case 1:
                        $work = $partViewSingle;    // 文字列(単一行)
                        break;
                    case 2:
                        $work = $partViewMulti;     // 文字列(複数行)
                        break;
                    case 3:
                        $work = $partViewInteger;   // 整数
                        break;
                    case 4:
                        $work = $partViewFloat;     // 小数
                        break;
                    case 5:
                        $work = $partViewDateTime;  // 日時
                        break;
                    case 6:
                        $work = $partViewDate;      // 日付
                        break;
                    case 7:
                        $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                        if($matchIdx === FALSE){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                            outputLog($msg);
                            // パラメータシート作成管理更新処理を行う
                            updateMenuStatus($targetData, "4", $msg, false, true);
                            $errFlg = true;
                            break 2;
                        }

                        $otherMenuId = $otherMenuLinkArray[$matchIdx]['MENU_ID'];
                        if(in_array($otherMenuId, $noLinkMenuIdArray)){
                            $work = $partViewId;     //プルダウン選択(IDColumn)
                        }else{
                            $work = $partViewLinkId; //プルダウン選択(LinkIDColumn)
                        }

                        //参照項目がある場合
                        if(!empty($itemInfo['REFERENCE_ITEM'])){
                            $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                            $work_ref = "";
                            $convReferenceCount2 = 0;
                            foreach($aryReferenceItem as $id){
                                $repracePassword = "";
                                $repraceDateFormat = "null";
                                $work_ref_tmpl = $partViewReference;
                                $convReferenceCount2++;

                                //カラムグループに追加
                                $convItemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID'] . "_ref_" . $convReferenceCount2] = $columnGroupSplit;
                                //対象の参照項目情報
                                $convReferenceItemInfo = $referenceItemArray[$id];

                                //パスワード表示
                                if($convReferenceItemInfo['SENSITIVE_FLAG'] == 2){
                                    $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("print_table", $outputType);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("print_journal_table", $outputType);';
                                    $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("excel", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->setOutputType("json", $outputType2);' . "\n";
                                    $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '_ref_' . $convReferenceCount2 . '->getOutputType("filter_table")->setVisible(false);';
                                }

                                //日時表示
                                if($convReferenceItemInfo['INPUT_METHOD_ID'] == 5){
                                    $repraceDateFormat = '\'Y/m/d H:i:s\'';
                                }

                                //日付表示
                                if($convReferenceItemInfo['INPUT_METHOD_ID'] == 6){
                                    $repraceDateFormat = '\'Y/m/d\'';
                                }

                                // 「'」がある場合は「\'」に変換する
                                $cloneItemName = str_replace("'", "\'", $convReferenceItemInfo['ITEM_NAME']);
                                $cloneDescription = str_replace("'", "\'", $convReferenceItemInfo['DESCRIPTION']);

                                $work_ref_tmpl = str_replace(REPLACE_REF_NUMBER, $itemInfo['CREATE_ITEM_ID'] . "_ref_" . $convReferenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_VALUE, $itemInfo['COLUMN_NAME'] . "_CLONE_" . $convReferenceCount2, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_DISP, $cloneItemName, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_ID_TABLE, $convReferenceItemInfo['TABLE_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_PRI, $convReferenceItemInfo['PRI_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_COL, $convReferenceItemInfo['COLUMN_NAME'], $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_CLONE_INFO, $cloneDescription, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work_ref_tmpl);
                                $work_ref_tmpl = str_replace(REPLACE_REFERENCE_DATE_FORMAT, $repraceDateFormat, $work_ref_tmpl);

                                $work_ref = $work_ref . $work_ref_tmpl;
                            }
                            $work = $work . $work_ref;
                        }
                        break;
                    case 8:
                        $work = $partViewPassword;  // 文字列(PW)
                        break;
                    case 9:
                        $work = $partViewUpload;    // ファイルアップロード
                        break;
                    case 10:
                        $work = $partViewLink;      // リンク
                        break;
                    case 11:
                        $work = $partViewType3Reference;       // パラメータシート参照
                        break;
                }

                $work = str_replace(REPLACE_NUM, $itemInfo['CREATE_ITEM_ID'], $work);

                $work = str_replace(REPLACE_INFO,   $description,               $work);
                if("" != $itemInfo['PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['PREG_MATCH']);

                    $work = str_replace(REPLACE_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_PREG, "", $work);
                }
                if("" != $itemInfo['MULTI_PREG_MATCH']){
                    $pregWork = str_replace("'", "\\'", $itemInfo['MULTI_PREG_MATCH']);

                    $work = str_replace(REPLACE_MULTI_PREG, "\$objVldt->setRegexp('" . $pregWork . "');", $work);
                }
                else{
                    $work = str_replace(REPLACE_MULTI_PREG, "", $work);
                    
                }
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    //パラメータシート参照の場合カラム名に_CLONE_1を追記
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'] . '_CLONE_1',      $work);
                }else{
                    $work = str_replace(REPLACE_VALUE,            $itemInfo['COLUMN_NAME'],      $work);
                }
                $work = str_replace(REPLACE_DISP,             $itemName,                     $work);
                $work = str_replace(REPLACE_SIZE,             $itemInfo['MAX_LENGTH'],       $work);
                $work = str_replace(REPLACE_MULTI_MAX_LENGTH, $itemInfo['MULTI_MAX_LENGTH'], $work);
                $work = str_replace(REPLACE_PW_MAX_LENGTH,    $itemInfo['PW_MAX_LENGTH'],    $work);
                $work = str_replace(REPLACE_UPLOAD_FILE_SIZE, $itemInfo['UPLOAD_MAX_SIZE'],  $work);
                $work = str_replace(REPLACE_LINK_MAX_LENGTH,  $itemInfo['LINK_LENGTH'],      $work);

                // 他メニュー参照の場合
                if(7 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    if($matchIdx === FALSE){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    if(5 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $otherMenuLink['COLUMN_TYPE']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }      
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                    //LinkIDColumn用のurl
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $otherMenuLink['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $otherMenuLink['COLUMN_DISP_NAME']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $urlOption = (in_array($otherMenuLink['LINK_ID'], $urlOptionTargetArray)) ? 'true' : 'false';
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);
                    $work = str_replace(REPLACE_URL_OPTION, $urlOption, $work);
                }
                // 整数の場合
                if(3 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['INT_MAX']){
                        $work = str_replace(REPLACE_INT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MAX, $itemInfo['INT_MAX'], $work);
                    }
                    if("" == $itemInfo['INT_MIN']){
                        $work = str_replace(REPLACE_INT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_INT_MIN, $itemInfo['INT_MIN'], $work);
                    }
                }
                // 小数の場合
                if(4 == $itemInfo['INPUT_METHOD_ID']){
                    if("" == $itemInfo['FLOAT_MAX']){
                        $work = str_replace(REPLACE_FLOAT_MAX, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MAX, $itemInfo['FLOAT_MAX'], $work);
                    }
                    if("" == $itemInfo['FLOAT_MIN']){
                        $work = str_replace(REPLACE_FLOAT_MIN, 'null' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_MIN, $itemInfo['FLOAT_MIN'], $work);
                    }
                    if("" == $itemInfo['FLOAT_DIGIT']){
                        $work = str_replace(REPLACE_FLOAT_DIGIT, '14' , $work);
                    }
                    else{
                        $work = str_replace(REPLACE_FLOAT_DIGIT, $itemInfo['FLOAT_DIGIT'], $work);
                    }
                }
                // パラメータシート参照の場合
                if(11 == $itemInfo['INPUT_METHOD_ID']){
                    $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                    if(empty($type3ReferenceData)){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    //参照先のテーブル名をREPLACE
                    $work = str_replace(REPLACE_ID_TABLE,   $type3ReferenceData['TABLE_NAME'],   $work);

                    //参照先のカラム名をREPLACE
                    $work = str_replace(REPLACE_ID_COL,     $type3ReferenceData['COLUMN_NAME'],  $work);

                    //LinkIDColumn用のurlをREPLACE
                    $url1 = '01_browse.php?no=' . sprintf('%010d', $type3ReferenceData['MENU_ID']) . '&filter=on&';
                    $url2 = str_replace('/', '\\', $type3ReferenceData['COL_TITLE']);
                    $url2 = str_replace('\'', '\\\'', $url2); //シングルクォーテーションをエスケープ
                    $url2 = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . '\\' . $url2; //COL_TITLEの頭に「パラメータ\」を追加
                    $work = str_replace(REPLACE_LINK_ID_URL1, $url1, $work);
                    $work = str_replace(REPLACE_LINK_ID_URL2, $url2, $work);

                    //DATE_FORMATをREPLACE
                    if(5 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d H:i:s\'',   $work);
                    }
                    else if(6 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $work = str_replace(REPLACE_DATE_FORMAT,   '\'Y/m/d\'',   $work);
                    }
                    else{
                        $work = str_replace(REPLACE_DATE_FORMAT,   'null',   $work);
                    }

                    //参照元がパスワードカラムの場合、マスク処理を追記
                    $repracePassword = '';
                    if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                        $repracePassword = '$outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt("********"));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("print_journal_table", $outputType);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$outputType2 = new OutputType(new ExcelHFmt(), new StaticBFmt(""));' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("excel", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->setOutputType("json", $outputType2);' . "\n";
                        $repracePassword = $repracePassword.'    ' . '$c' . $itemInfo['CREATE_ITEM_ID'] . '->getOutputType("filter_table")->setVisible(false);';
                    }
                    $work = str_replace(REPLACE_ITEM_PASSWORD, $repracePassword, $work);
                }
                $convertViewLoadTableVal .= $work . "\n";
            }
            unset($itemInfo);
            if(true === $errFlg){
                continue;
            }

            // カラムグループ部品組み立て
            $convColumnGrpParts = makeColumnGrpParts($convItemColumnGrpArrayArray, $substitution="1");

            if("1" == $cmiData['TARGET']){  // 作成対象; パラメータシート用
                // 用途によってホストキーとホスト定義を設定する
                if("2" == $cmiData['PURPOSE']){
                    $hostKey = "KY_KEY";
                    $hostDef = "\$c = new IDColumn('KY_KEY',\$g['objMTS']->getSomeMessage('ITACREPAR-MNU-102601') . '/' . \$g['objMTS']->getSomeMessage('ITACREPAR-MNU-102602'),'G_UQ_HOST_LIST','KY_KEY','KY_VALUE','');";
                }
                else{
                    $hostKey = "HOST_ID";
                    $hostDef = "\$c = new IDColumn('HOST_ID',\$g['objMTS']->getSomeMessage('ITACREPAR-MNU-102601'),'C_STM_LIST','SYSTEM_ID','HOSTNAME','');";
                }
            }
        }

        // 「'」がある場合は「\'」に変換する。説明の改行コードは<BR/>に変換する。
        $description    = str_replace("'", "\'", $cmiData['DESCRIPTION']);
        $description    = str_replace("\n", "<BR/>", $description);
        $menuName       = str_replace("'", "\'", $cmiData['MENU_NAME']);

        if("2" == $cmiData['TARGET']){  // 作成対象; データシート用
            // データシート用の00_loadTable.php
            $work = $cmdbLoadTableTmpl;
            $work = str_replace(REPLACE_INFO,   $description,       $work);
            $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
            $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
            $work = str_replace(REPLACE_MENU,   $menuName,          $work);
            $cmdbLoadTableVal .= $columnGrpParts;
            $work = str_replace(REPLACE_ITEM,   $cmdbLoadTableVal, $work);
            $cmdbLoadTable = $work;

            // データシート用のSQL
            if($menuCreateTypeId == 3){ //編集モードの場合
                //カラムの追加/削除sqlを作成
                $alterColumnSql = createAlterColumnSql("cmdb", $menuTableName, $columnNameListArray);
                if($alterColumnSql === false){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                    outputLog($msg);
                    throw new Exception($msg);
                }
                $work = $cmdbEditSqlTmpl;
                $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
                $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql,   $work);
                $work = str_replace(REPLACE_COL,        $columns,       $work);
                $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                $cmdbSql = $work;

            }else{ //初期化および新規作成の場合
                $work = $cmdbSqlTmpl;
                $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
                $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
                $work = str_replace(REPLACE_COL,        $columns,       $work);
                $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                $cmdbSql = $work;
            }
        }
        else{
            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用の00_loadTable.php
                $work = $hgLoadTableTmpl;
                $work = str_replace(REPLACE_INFO,   $description,       $work);
                $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
                $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                $work = str_replace(REPLACE_MENU,   $menuName,          $work);
                $hgLoadTableVal .= $columnGrpParts;
                $work = str_replace(REPLACE_ITEM,   $hgLoadTableVal, $work);
                $hgLoadTable = $work;
            }
            if("1" == $cmiData['TARGET']){
                // ホスト用の00_loadTable.php
                $work = $hostLoadTableTmpl;
                $work = str_replace(REPLACE_INFO,   $description,       $work);
                $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
                $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                $work = str_replace(REPLACE_MENU,   $menuName,          $work);
                $hostLoadTableVal .= $columnGrpParts;
                $work = str_replace(REPLACE_ITEM,   $hostLoadTableVal, $work);
                $hostLoadTable = $work;
            }
            else if("3" == $cmiData['TARGET']){
                // ホスト用(オペレーションのみ)の00_loadTable.php
                $work = $hostLoadTableOpTmpl;
                $work = str_replace(REPLACE_INFO,   $description,       $work);
                $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
                $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                $work = str_replace(REPLACE_MENU,   $menuName,          $work);
                $hostLoadTableVal .= $columnGrpParts;
                $work = str_replace(REPLACE_ITEM,   $hostLoadTableVal, $work);
                $hostLoadTable = $work;
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){

                // 縦メニュー用の00_loadTable.php
                $convertLoadTableVal .= $convColumnGrpParts;
                if("2" == $cmiData['PURPOSE']){
                    $work = $convLoadTableTmpl;
                    $work = str_replace(REPLACE_INFO,       $description,           $work);
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                    $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                    $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                    $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                    $work = str_replace(REPLACE_REPEAT_CNT, $repeatCnt,             $work);
                    $convertLoadTable = $work;
                    $work = $convHostLoadTableTmpl;
                    $work = str_replace(REPLACE_INFO,       $description,           $work);
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                    $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                    $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                    $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                    $work = str_replace(REPLACE_REPEAT_CNT, $repeatCnt,             $work);
                    $convertHostLoadTable = $work;
                }
                else if("1" == $cmiData['TARGET']){
                    $work = $convHostLoadTableTmpl;
                    $work = str_replace(REPLACE_INFO,       $description,           $work);
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                    $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                    $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                    $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                    $work = str_replace(REPLACE_REPEAT_CNT, $repeatCnt,             $work);
                    $convertLoadTable = $work;
                }
                else if("3" == $cmiData['TARGET']){
                    $work = $convHostLoadTableOpTmpl;
                    $work = str_replace(REPLACE_INFO,       $description,           $work);
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                    $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
                    $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                    $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                    $work = str_replace(REPLACE_REPEAT_CNT, $repeatCnt,             $work);
                    $convertLoadTable = $work;
                }
            }

            // 最新値参照用の00_loadTable.php
            if("3" == $cmiData['TARGET']){
                $work = $viewLoadTableOpTmpl;
            }
            else{
                $work = $viewLoadTableTmpl;
            }
            $work = str_replace(REPLACE_INFO,   $description,       $work);
            $work = str_replace(REPLACE_MENU,   $menuName,          $work);
            $work = str_replace(REPLACE_UNIQUE_CONSTRAINT, $uniqueConstraintSet, $work);
            if(true === $createConvFlg){
                $work = str_replace(REPLACE_TABLE,  $menuTableName . '_CONV',     $work);
                $inputOrder = <<< 'EOD'
// 入力順序
$c = new NumColumn('INPUT_ORDER',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102613"));
$c->setHiddenMainTableColumn(true);
$c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102614"));
$c->setValidator(new IntNumValidator(0, null));
$c->getOutputType("filter_table")->setVisible(false);
$c->setSubtotalFlag(false);
$table->addColumn($c);
EOD;
                $work = str_replace(REPLACE_INPUT_ORDER, $inputOrder, $work);

                $convertViewLoadTableVal .= $convColumnGrpParts;
                $work = str_replace(REPLACE_ITEM,   $convertViewLoadTableVal,  $work);
            }
            else{
                $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
                $work = str_replace(REPLACE_INPUT_ORDER, "", $work);
                $viewLoadTableVal .= $columnGrpParts;
                $work = str_replace(REPLACE_ITEM,   $viewLoadTableVal,  $work);
            }
            $viewLoadTable = $work;

            // ホストグループ用のSQL
            if("2" == $cmiData['PURPOSE']){
                if($menuCreateTypeId == 3){ //編集モードの場合
                    //カラムの追加/削除sqlを作成
                    $alterColumnSql = createAlterColumnSql("hg", $menuTableName, $columnNameListArray);
                    if($alterColumnSql === false){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $work = $hgEditSqlTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                    $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                    $work = str_replace(REPLACE_COL,        $columns,        $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hgSql = $work;
                }else{ //初期化および新規作成の場合
                    $work = $hgSqlTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
                    $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
                    $work = str_replace(REPLACE_COL,        $columns,       $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hgSql = $work;
                }
            }

            // ホスト用のSQL
            if("1" == $cmiData['TARGET']){
                if($menuCreateTypeId == 3){ //編集モードの場合
                    //カラムの追加/削除sqlを作成
                    $alterColumnSql = createAlterColumnSql("host", $menuTableName, $columnNameListArray);
                    if($alterColumnSql === false){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $work = $hostEditSqlTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                    $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                    $work = str_replace(REPLACE_COL,        $columns,        $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hostSql = $work;
                }else{ //初期化および新規作成の場合
                    $work = $hostSqlTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
                    $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
                    $work = str_replace(REPLACE_COL,        $columns,       $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hostSql = $work;
                }
            }
            // ホスト(オペレーションのみ)用のSQL
            else if("3" == $cmiData['TARGET']){
                if($menuCreateTypeId == 3){ //編集モードの場合
                    //カラムの追加/削除sqlを作成
                    $alterColumnSql = createAlterColumnSql("host", $menuTableName, $columnNameListArray);
                    if($alterColumnSql === false){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    $work = $hostEditSqlOpTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                    $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                    $work = str_replace(REPLACE_COL,        $columns,        $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hostSql = $work;
                }else{ //初期化および新規作成の場合
                    $work = $hostSqlOpTmpl;
                    $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
                    $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
                    $work = str_replace(REPLACE_COL,        $columns,       $work);
                    $work = str_replace(REPLACE_REFERENCE,  $reference,     $work);
                    $hostSql = $work;
                }
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){

                // 縦メニュー用のSQL
                if("2" == $cmiData['PURPOSE']){
                    if($menuCreateTypeId == 3){ //編集モードの場合
                        //カラムの追加/削除sqlを作成
                        $alterColumnSql = createAlterColumnSql("conv", $menuTableName, $convColumnNameListArray);
                        if($alterColumnSql === false){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                            outputLog($msg);
                            throw new Exception($msg);
                        }
                        $work = $convEditSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                        $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,    $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,  $work);
                        $convertSql = $work;
                        //カラムの追加/削除sqlを作成
                        $alterColumnSql = createAlterColumnSql("conv_h", $menuTableName, $convColumnNameListArray);
                        if($alterColumnSql === false){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                            outputLog($msg);
                            throw new Exception($msg);
                        }
                        $work = $convHostEditSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                        $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,    $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,  $work);
                        $convertSql .= $work;
                    }else{ //初期化および新規作成の場合
                        $work = $convSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                        $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,         $work);
                        $convertSql = $work;
                        $work = $convHostSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                        $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,         $work);
                        $convertSql .= $work;
                    }
                }
                else if("1" == $cmiData['TARGET']){
                    if($menuCreateTypeId == 3){ //編集モードの場合
                        //カラムの追加/削除sqlを作成
                        $alterColumnSql = createAlterColumnSql("conv_h", $menuTableName, $convColumnNameListArray);
                        if($alterColumnSql === false){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                            outputLog($msg);
                            throw new Exception($msg);
                        }
                        $work = $convHostEditSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                        $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,    $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,  $work);
                        $convertSql = $work;
                    }else{ //初期化および新規作成の場合
                        $work = $convHostSqlTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                        $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,         $work);
                        $convertSql = $work;
                    }
                }
                else if("3" == $cmiData['TARGET']){
                    if($menuCreateTypeId == 3){ //編集モードの場合
                        //カラムの追加/削除sqlを作成
                        $alterColumnSql = createAlterColumnSql("conv_h", $menuTableName, $convColumnNameListArray);
                        if($alterColumnSql === false){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5023');
                            outputLog($msg);
                            throw new Exception($msg);
                        }
                        $work = $convHostSqlOpEditTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,  $work);
                        $work = str_replace(REPLACE_ALTER_COL,  $alterColumnSql, $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,    $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,  $work);
                        $convertSql = $work;
                    }else{ //初期化および新規作成の場合
                        $work = $convHostSqlOpTmpl;
                        $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                        $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                        $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                        $work = str_replace(REPLACE_REFERENCE,  $convReference,         $work);
                        $convertSql = $work; 
                    }

                }
            }  
        }

        //////////////////////////
        // メニュー専用の一時領域を作成する
        //////////////////////////
        $menuTmpDir = $tmpDir . "/" . $menuDirName . "/";

        if(!file_exists($menuTmpDir)){
            $result = mkdir($menuTmpDir, 0777, true);

            if(true != $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($menuTmpDir));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }
        }

        //////////////////////////
        // SQLファイルを作成する
        //////////////////////////
        $sqlFilePath = $menuTmpDir . $menuTableName . ".sql";

        if("2" == $cmiData['TARGET']){
            // データシート用
            $result = file_put_contents($sqlFilePath, $cmdbSql);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($sqlFilePath));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }
        }
        else{
            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用
                $result = file_put_contents($sqlFilePath, $hgSql);
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($sqlFilePath));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, false, true);
                    continue;
                }
            }
            // ホスト用
            $result = file_put_contents($sqlFilePath, $hostSql, FILE_APPEND);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($sqlFilePath));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){

                $result = file_put_contents($sqlFilePath, $convertSql, FILE_APPEND);
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($sqlFilePath));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, false, true);
                    continue;
                }
            }
        }

        //////////////////////////
        // 外部CMDB作成用SQL実行
        //////////////////////////
        $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);
        if("2" == $cmiData['TARGET']){
            // データシート用
            $explodeSql = explode(";", $cmdbSql);
            $errFlg = false;
            foreach($explodeSql as $sql){

                // SQLが空の場合はスキップ
                if("" === str_replace(" ", "", (str_replace("\n", "", $sql)))){
                    continue;
                }

                // SQL実行
                $result = $baseTable->execQuery($sql, NULL, $objQuery);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, false, true);
                    $errFlg = true;
                    break;
                }
            }
            if(true === $errFlg){
                continue;
            }
        }
        else{
            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用
                $explodeSql = explode(";", $hgSql);
                $errFlg = false;
                foreach($explodeSql as $sql){

                    // SQLが空の場合はスキップ
                    if("" === str_replace(" ", "", (str_replace("\n", "", $sql)))){
                        continue;
                    }

                    // SQL実行
                    $result = $baseTable->execQuery($sql, NULL, $objQuery);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                }
                if(true === $errFlg){
                    continue;
                }
            }

            // ホスト用
            $explodeSql = explode(";", $hostSql);
            $errFlg = false;
            foreach($explodeSql as $sql){

                // SQLが空の場合はスキップ
                if("" === str_replace(" ", "", (str_replace("\n", "", $sql)))){
                    continue;
                }

                // SQL実行
                $result = $baseTable->execQuery($sql, NULL, $objQuery);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, false, true);
                    $errFlg = true;
                    break;
                }
            }
            if(true === $errFlg){
                continue;
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){

                $explodeSql = explode(";", $convertSql);
                $errFlg = false;
                foreach($explodeSql as $sql){

                    // SQLが空の場合はスキップ
                    if("" === str_replace(" ", "", (str_replace("\n", "", $sql)))){
                        continue;
                    }

                    // SQL実行
                    $result = $baseTable->execQuery($sql, NULL, $objQuery);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                        outputLog($msg);
                        outputLog("SQL=$sql");
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                }
                if(true === $errFlg){
                    continue;
                }
            }
        }

        //////////////////////////
        // トランザクション開始
        //////////////////////////
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, false, true);
            continue;
        }

        //////////////////////////
        // メニュー管理更新
        //////////////////////////
        $hgMenuId = null;
        $hostMenuId = null;
        $hostSubMenuId = null;
        $viewMenuId = null;
        $convMenuId = null;
        $convHostMenuId = null;
        $result = updateMenuList($cmiData, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $createConvFlg);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // メニュー管理廃止(利用していないメニューグループのメニューを廃止)
        //////////////////////////
        $discardMenuIdArray = array();
        $result = discardMenuList($cmiData, $discardMenuIdArray);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // シーケンステーブル更新(シーケンス管理メニュー対応)
        //////////////////////////
        $result = updateSequence($hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData['TARGET'], $menuTableName);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // ロール・メニュー紐付管理更新
        //////////////////////////
        $result = updateRoleMenuLinkList($targetData, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData, $roleUserLinkArray);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // メニュー・テーブル紐付更新
        //////////////////////////
        $result = updateMenuTableLink($menuTableName, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // 他メニュー連携テーブル更新
        //////////////////////////
        $result = updateOtherMenuLink($menuTableName, $itemInfoArray, $itemColumnGrpArrayArray, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData);
        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        if("1" == $cmiData['TARGET']||"3" == $cmiData['TARGET']){  // 作成対象: パラメータシート
            
            // 紐づけ対象だけを確認 (紐づけ対象がないの場合はtrue)
            $noLinkTarget = true;
            $onlyUploadFlg = true;
            foreach($itemInfoArray as $key => $itemInfo){
                // 文字列(単一行)、文字列(複数行)、整数、小数、パスワード、リンクの場合
                if(in_array($itemInfo['INPUT_METHOD_ID'], array(1, 2, 3, 4, 8, 10))){
                    //作成対象
                    $noLinkTarget = false;
                    $onlyUploadFlg = false;
                }
                // 日時、日付の場合
                else if(in_array($itemInfo['INPUT_METHOD_ID'], array(5, 6))){
                    // 対象外
                    continue;
                }
                // ファイルアップロードの場合
                else if(in_array($itemInfo['INPUT_METHOD_ID'], array(9))){
                    //作成対象
                    $noLinkTarget = false;
                }
                // プルダウン選択の場合、参照元のチェック
                else if(in_array($itemInfo['INPUT_METHOD_ID'], array(7))){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    // 日時、日付、プルダウン選択、パスワード、ファイルアップロードの場合
                    if(in_array($otherMenuLink['COLUMN_TYPE'], array(5, 6, 7, 8, 9))){
                        // 対象外
                        continue;
                    } 
                    else{
                        //作成対象
                        $noLinkTarget = false;
                        $onlyUploadFlg = false;
                    }
                }
                // パラメータシート参照の場合
                else if(in_array($itemInfo['INPUT_METHOD_ID'], array(11))){
                    //作成対象
                    $noLinkTarget = false;
                    $onlyUploadFlg = false;
                }
            }

            // 紐付対象メニューに登録するメニューIDを特定する
            if("" != $hostSubMenuId){
                $targetMenuId = $hostSubMenuId;
            }
            else{
                $targetMenuId = $hostMenuId;
            }

            if(false === $noLinkTarget){
                // シートタイプを決定する
                $sheetType = null;
                if("1" == $cmiData['TARGET']){
                    if(false === $onlyUploadFlg){
                        $sheetType = 1;
                    }
                    else{
                        $sheetType = 4;
                    }
                }
                else if("3" == $cmiData['TARGET']){
                    if(false === $onlyUploadFlg){
                        $sheetType = 3;
                    }
                    else{
                        $noLinkTarget = true;
                    }
                }
            }

            //////////////////////////
            // 紐付対象メニュー更新
            //////////////////////////
            $result = updateLinkTargetMenu($targetMenuId, $noLinkTarget, $cmiData, $sheetType);
            if(true !== $result){
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $result, true, true);
                continue;
            }

            //////////////////////////
            // 紐付対象メニューテーブル管理更新
            //////////////////////////
            $result = updateLinkTargetTable($targetMenuId, "G_" . $menuTableName . "_H" ,$noLinkTarget, $cmiData);

            if(true !== $result){
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $result, true, true);
                continue;
            }

            //////////////////////////
            // 紐付対象メニューカラム管理更新
            //////////////////////////
            $result = updateLinkTargetColumn($targetMenuId, $itemInfoArray, $itemColumnGrpArrayArray, $cmiData, $noLinkTarget, $otherMenuLinkArray, $duplicateItemNameArray);

            if(true !== $result){
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $result, true, true);
                continue;
            }

            // 用途がホストグループ用の場合
            if("2" == $cmiData['PURPOSE']){

                //////////////////////////
                // ホストグループ分割対象更新
                //////////////////////////
                $result = updateDivideTarget($hgMenuId, $hostMenuId);

                if(true !== $result){
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $result, true, true);
                    continue;
                }

                if(true === $createConvFlg){
                    //////////////////////////
                    // ホストグループ分割対象更新
                    //////////////////////////
                    $result = updateDivideTarget($convMenuId, $convHostMenuId);

                    if(true !== $result){
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $result, true, true);
                        continue;
                    }
                }
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){

                // 用途がホストグループ用の場合
                if("2" == $cmiData['PURPOSE']){
                    $toMenuId = $hgMenuId;
                }
                else{
                    $toMenuId = $hostMenuId;
                }

                //////////////////////////
                // パラメータシート縦横変換管理更新
                //////////////////////////
                $result = updateColToRowMng($cpiData, $convMenuId, $toMenuId, $cmiData['PURPOSE'], $startColName);

                if(true !== $result){
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $result, true, true);
                    continue;
                }
            }
        }

        //////////////////////////
        // メニュー管理で廃止したレコードと同じメニューIDが紐付いているレコードを廃止する
        //////////////////////////
        if(!empty($discardMenuIdArray)){
            $result = discardRerationTable($discardMenuIdArray);

            if(true !== $result){
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $result, true, true);
                continue;
            }
        }

        //////////////////////////
        // loadTableを配置する
        //////////////////////////
        if("2" == $cmiData['TARGET']){  // 作成対象; データシート用
                //データシート用
                $cmdbLoadTablePath = $menuTmpDir . sprintf("%010d", $hostMenuId) . "_loadTable.php";
                $cmdbLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $hostMenuId), $cmdbLoadTable);
                $result = deployLoadTable($cmdbLoadTable,
                                          $cmdbLoadTablePath,
                                          sprintf("%010d", $hostMenuId),
                                          $targetData
                                         );
                if(true !== $result){
                    continue;
                }
        }
        else{
            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用
                $hgLoadTablePath = $menuTmpDir . sprintf("%010d", $hgMenuId) . "_loadTable.php";
                $hgLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $hgMenuId), $hgLoadTable);
                $result = deployLoadTable($hgLoadTable,
                                          $hgLoadTablePath,
                                          sprintf("%010d", $hgMenuId),
                                          $targetData
                                         );
                if(true !== $result){
                    continue;
                }
            }
            else if(true !== $createConvFlg){
                // 代入値自動登録用
                $hostSubLoadTablePath = $menuTmpDir . sprintf("%010d", $hostSubMenuId) . "_loadTable.php";
                $hostLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $hostMenuId), $hostLoadTable);
                $result = deployLoadTable($hostLoadTable,
                                          $hostSubLoadTablePath,
                                          sprintf("%010d", $hostSubMenuId),
                                          $targetData
                                         );
                if(true !== $result){
                    continue;
                }
            }
            // ホスト用
            $hostLoadTablePath = $menuTmpDir . sprintf("%010d", $hostMenuId) . "_loadTable.php";
            $hostLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $hostMenuId), $hostLoadTable);
            $result = deployLoadTable($hostLoadTable,
                                      $hostLoadTablePath,
                                      sprintf("%010d", $hostMenuId),
                                      $targetData
                                     );
            if(true !== $result){
                continue;
            }

            // 最新値参照用
            $viewLoadTablePath = $menuTmpDir . sprintf("%010d", $viewMenuId) . "_loadTable.php";
            if("2" == $cmiData['PURPOSE'] && true === $createConvFlg){
                $viewLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $convHostMenuId), $viewLoadTable);
            }
            else if("2" != $cmiData['PURPOSE'] && true === $createConvFlg){
                $viewLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $convMenuId), $viewLoadTable);
            }
            else{
                $viewLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $hostMenuId), $viewLoadTable);
            }
            $result = deployLoadTable($viewLoadTable,
                                      $viewLoadTablePath,
                                      sprintf("%010d", $viewMenuId),
                                      $targetData
                                     );
            if(true !== $result){
                continue;
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){
                // 縦メニュー用
                $convertLoadTablePath = $menuTmpDir . sprintf("%010d", $convMenuId) . "_loadTable.php";
                $convertLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $convMenuId), $convertLoadTable);
                $result = deployLoadTable($convertLoadTable,
                                          $convertLoadTablePath,
                                          sprintf("%010d", $convMenuId),
                                          $targetData
                                         );
                if(true !== $result){
                    continue;
                }

                if("2" == $cmiData['PURPOSE']){
                    $convertHostLoadTablePath = $menuTmpDir . sprintf("%010d", $convHostMenuId) . "_loadTable.php";
                    $convertHostLoadTable = str_replace(REPLACE_UPLOAD_REF_MENU_ID, sprintf("%010d", $convHostMenuId), $convertHostLoadTable);
                    $result = deployLoadTable($convertHostLoadTable,
                                              $convertHostLoadTablePath,
                                              sprintf("%010d", $convHostMenuId),
                                              $targetData
                                             );
                    if(true !== $result){
                        continue;
                    }
                }
            }
        }

        //////////////////////////
        // 作成したファイルをZIPファイルに固める
        //////////////////////////
        if(true === $createConvFlg){
            $menuId = $convMenuId;
        }
        else if("2" == $cmiData['PURPOSE']){
            $menuId = $hgMenuId;
        }
        else{
            $menuId = $hostMenuId;
        }
        $zipFileName = sprintf("%010d", $menuId) . ".zip";
        $zipFilePath = $menuTmpDir . $zipFileName;

        $zip = new ZipArchive;
        if(true != $zip->open($zipFilePath, ZipArchive::CREATE)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath));
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, true, true);
            continue;
        }

        if("2" == $cmiData['TARGET']){  // 作成対象; データシート用
            // データシート用の00_loadTable.php
            $result = $zip->addFile($cmdbLoadTablePath, basename($cmdbLoadTablePath));

            if(true != $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $cmdbLoadTablePath));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, true, true);
                $zip->close();
                $zip = NULL;
                continue;
            }
        }
        else{ // 作成対象: パラメータシート 
            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用の00_loadTable.php
                $result = $zip->addFile($hgLoadTablePath, basename($hgLoadTablePath));

                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $hgLoadTablePath));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, true, true);
                    $zip->close();
                    $zip = NULL;
                    continue;
                }
            }
            else if(true !== $createConvFlg){
                // 代入値登録設定用の00_loadTable.php
                $result = $zip->addFile($hostSubLoadTablePath, basename($hostSubLoadTablePath));

                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $hostSubLoadTablePath));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $msg, true, true);
                    $zip->close();
                    $zip = NULL;
                    continue;
                }
            }

            // ホスト用の00_loadTable.php
            $result = $zip->addFile($hostLoadTablePath, basename($hostLoadTablePath));

            if(true != $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $hostLoadTablePath));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, true, true);
                $zip->close();
                $zip = NULL;
                continue;
            }

            // 参照用の00_loadTable.php
            $result = $zip->addFile($viewLoadTablePath, basename($viewLoadTablePath));

            if(true != $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $viewLoadTablePath));
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, true, true);
                $zip->close();
                $zip = NULL;
                continue;
            }

            // パラメータシート(縦)を作成する設定の場合
            if(true === $createConvFlg){
            // 縦メニュー用の00_loadTable.php
                $result = $zip->addFile($convertLoadTablePath, basename($convertLoadTablePath));

                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $convertLoadTablePath));
                    outputLog($msg);
                    // パラメータシート作成管理更新処理を行う
                    updateMenuStatus($targetData, "4", $result, true, true);
                    $zip->close();
                    $zip = NULL;
                    continue;
                }
                if("2" == $cmiData['PURPOSE']){
                    $result = $zip->addFile($convertHostLoadTablePath, basename($convertHostLoadTablePath));

                    if(true != $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $convertHostLoadTablePath));
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $result, true, true);
                        $zip->close();
                        $zip = NULL;
                        continue;
                    }
                }
            }
        }

        // SQLファイル
        $result = $zip->addFile($sqlFilePath, $menuTableName . ".sql");

        if(true != $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($zipFilePath, $sqlFilePath));
            outputLog($msg);
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $msg, true, true);
            $zip->close();
            $zip = NULL;
            continue;
        }

        $zip->close();
        $zip = NULL;

        //////////////////////////
        // メニュー定義一覧の「メニュー作成状態」を更新する（2（作成済み）にする）。（すでに「作成済み」の場合はスキップ）
        //////////////////////////
        if($cmiData['MENU_CREATE_STATUS'] != 2){
            $result = updateMenuCreateFlag($cmiData);
            if(true !== $result){
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $result, true, true);
                continue;
            }
        }

        //////////////////////////
        // パラメータシート作成管理更新処理を行う（完了）
        //////////////////////////
        updateMenuStatus($targetData, "3", NULL, false, false, $zipFileName, $zipFilePath);
        insertERTask();
    }

    // 作業用ディレクトリを削除する
    if(file_exists($tmpDir)){
        $output = NULL;
        $cmd = "rm -rf '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5009', array($tmpDir));
            outputLog($msg);
            throw new Exception($msg);
        }
    }

    if($logLevel === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
    }
}
catch(Exception $e){
    // 作業用ディレクトリを削除する
    if(file_exists($tmpDir)){
        $output = NULL;
        $cmd = "rm -rf '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);
    }
    if($logLevel === 'DEBUG'){
        // 処理終了ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10003', basename( __FILE__, '.php' )));
    }
}


/*
 * 未実行レコードを取得する
 */
function getUnexecutedRecord(){
    global $objDBCA, $db_model_ch, $objMTS;
    $tranStartFlg = false;
    $createMenuStatusTable = new CreateMenuStatusTable($objDBCA, $db_model_ch);
    $returnArray = array();

    try{
        //////////////////////////
        // パラメータシート作成管理テーブルを検索
        //////////////////////////
        $sql = $createMenuStatusTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $createMenuStatusTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $createMenuStatusArray = $result;

        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = true;

        foreach($createMenuStatusArray as $cmsData){
            // ステータスが未実行または実行中の場合
            if("1" == $cmsData['STATUS_ID'] || "2" == $cmsData['STATUS_ID']){

                $updateData = $cmsData;

                // ステータスが未実行の場合
                if("1" == $cmsData['STATUS_ID']){

                    $updateData['STATUS_ID']        = "2";
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;
                    $returnArray[] = $cmsData;
                }
                // ステータスが実行中の場合、完了(異常) にする
                else if("2" == $cmsData['STATUS_ID']){
                    $updateData['STATUS_ID']        = "4";
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;
                }

                //////////////////////////
                // パラメータシート作成管理テーブルを更新
                //////////////////////////
                $result = $createMenuStatusTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $tranStartFlg = false;

        return $returnArray;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        throw new Exception($e->getMessage());
    }
}


/**
 * パラメータシート作成管理更新
 * 
 */
function updateMenuStatus($targetData, $status, $note, $rollbackFlg, $tranFlg, $zipFileName=NULL, $zipFilePath=NULL){

    global $objDBCA, $db_model_ch, $objMTS;

    try{
        if(true === $rollbackFlg){
            // ロールバック
            $result = $objDBCA->transactionRollback();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        if(true === $tranFlg){
            // トランザクション開始
            $result = $objDBCA->transactionStart();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        $tranFlg = true;

        $createMenuStatusTable = new CreateMenuStatusTable($objDBCA, $db_model_ch);

        // 更新する
        $updateData = $targetData;
        $updateData['STATUS_ID']        = $status;              // ステータス
        $updateData['FILE_NAME']        = $zipFileName;         // メニュー資材
        $updateData['NOTE']             = $note;                // 備考
        $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

        //////////////////////////
        // パラメータシート作成管理テーブルを更新
        //////////////////////////
        $result = $createMenuStatusTable->updateTable($updateData, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        if(NULL != $zipFileName){

            // ZIPファイルをアップロードファイル格納先にコピーする
            $pathArray = array();
            $pathArray[0] = UPLOAD_PATH;
            $pathArray[1] = $pathArray[0] . sprintf("%010d", $targetData['MM_STATUS_ID']) . '/';
            $pathArray[2] = $pathArray[1] . 'old/';
            $pathArray[3] = $pathArray[2] . sprintf("%010d", $jnlSeqNo) . '/';

            foreach($pathArray as $path){

                if(!file_exists($path)){
                    $mask = umask();
                    umask(000);
                    $result = mkdir($path, 0777, true);
                    umask($mask);

                    if(true != $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5004', array($path));
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                    chmod($path, 0777);
                }
            }

            $destFile = $pathArray[1] . $zipFileName;
            $result = copy($zipFilePath, $destFile);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($zipFilePath, $destFile));
                outputLog($msg);
                throw new Exception($msg);
            }

            $destFile = $pathArray[3] . $zipFileName;
            $result = copy($zipFilePath, $destFile);
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($zipFilePath, $destFile));
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', array($result));
            outputLog($msg);
            throw new Exception($msg);
        }
        return true;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranFlg){
            $objDBCA->transactionRollback();
        }
        return $e->getMessage();
    }
}

/**
 * メニュー作成状態
 * 
 */
function updateMenuCreateFlag($cmiData){
    global $objDBCA, $db_model_ch, $objMTS;
    $createMenuInfoTable = new CreateMenuInfoTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー定義一覧テーブルを更新
        //////////////////////////
        $updateData = $cmiData;
        $updateData['MENU_CREATE_STATUS'] = "2"; // メニュー作成状態
        $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者
        $result = $createMenuInfoTable->updateTable($updateData, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5024', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;

    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/**
 * カラムグループ部品組み立て
 * 
 */
function makeColumnGrpParts($itemColumnGrpArrayArray, $substitution){

    $columnGrpParts = "";
    $beforeKey = null;
    $beforeColumnGrpArray = array();
    $numsetColumnGrpArrayArray = array();
    $columnGrpNum = 1;
    $substitutionFlag = ($substitution == "2") ? true : false;

    // カラムグループに番号を振る
    foreach($itemColumnGrpArrayArray as $key => $itemColumnGrpArray){

        $numsetColumnGrpArray = array();
        $matchFlg = true;
        foreach($itemColumnGrpArray as $key2 => $itemColumnGrp){

            $numsetColumnGrp = array();
            if(array_key_exists($key2, $beforeColumnGrpArray) && $beforeColumnGrpArray[$key2]['NAME'] == $itemColumnGrp && true === $matchFlg){
                $numsetColumnGrp['ID'] = $beforeColumnGrpArray[$key2]['ID'];
                $numsetColumnGrp['NAME'] = $beforeColumnGrpArray[$key2]['NAME'];
            }
            else{
                $numsetColumnGrp['ID'] = $columnGrpNum;
                $numsetColumnGrp['NAME'] = $itemColumnGrp;
                $columnGrpParts .= "    \$cg{$columnGrpNum} = new ColumnGroup('{$itemColumnGrp}');\n";
                $columnGrpNum++;
                $matchFlg = false;
            }
            $numsetColumnGrpArray[] = $numsetColumnGrp;
        }
        $numsetColumnGrpArrayArray[$key] = $numsetColumnGrpArray;
        $beforeColumnGrpArray = $numsetColumnGrpArray;
    }

    $beforeColumnGrpArray = null;
    $beforeKey = null;
    $loopCnt = 0;
    // カラムグループとカラムを順序通り設定する
    foreach($numsetColumnGrpArrayArray as $key => $numsetColumnGrpArray){
        $loopCnt++;

        // 2回目以降の場合
        if($loopCnt !== 1){

            if(0 === count($beforeColumnGrpArray)){
                // カラムグループの設定が無い場合、カラムを根本に紐付ける
                if($substitutionFlag){
                    $columnGrpParts .= "    \$table->addColumn(\$c{$beforeKey});\n";
                }else{
                    $columnGrpParts .= "    \$cg->addColumn(\$c{$beforeKey});\n";
                }
            }
            else{
                // カラムグループの設定がある場合、カラムグループの末端に紐付ける
                $columnGrpParts .= "    \$cg" . $beforeColumnGrpArray[count($beforeColumnGrpArray) - 1]['ID'] . "->addColumn(\$c{$beforeKey});\n";
            }

            for($loopCnt2 = count($beforeColumnGrpArray) -1; 0 <= $loopCnt2; $loopCnt2--){
                if(array_key_exists($loopCnt2, $numsetColumnGrpArray) && $numsetColumnGrpArray[$loopCnt2]['ID'] == $beforeColumnGrpArray[$loopCnt2]['ID']){
                    break;
                }
                else{
                    if($loopCnt2 !== 0){
                        $columnGrpParts .= "    \$cg" . $beforeColumnGrpArray[$loopCnt2 - 1]['ID'] . "->addColumn(\$cg" . $beforeColumnGrpArray[$loopCnt2]['ID'] . ");\n";
                    }
                    else{
                        if($substitutionFlag){
                                // データシート用はテーブル直付け
                                $columnGrpParts .= "    \$table->addColumn(\$cg" . $beforeColumnGrpArray[$loopCnt2]['ID'] . ");\n";
                        }else{
                                 // パラメータシートは「パラメータ」直下に付ける
                                $columnGrpParts .= "    \$cg->addColumn(\$cg" . $beforeColumnGrpArray[$loopCnt2]['ID'] . ");\n";
                        }
                    }
                }
            }
        }

        // ループの最後の場合
        if($loopCnt === count($numsetColumnGrpArrayArray)){
            if(0 === count($numsetColumnGrpArray)){
                // カラムグループの設定が無い場合、カラムを根本に紐付ける
                if($substitutionFlag){
                    // データシート用はテーブル直付け
                    $columnGrpParts .= "    \$table->addColumn(\$c{$key});\n";
                }else{
                    // パラメータシートは「パラメータ」直下に付ける
                    $columnGrpParts .= "    \$cg->addColumn(\$c{$key});\n";
                }
            }
            else{
                // カラムグループの設定がある場合、カラムグループの末端に紐付ける
                $columnGrpParts .= "    \$cg" . $numsetColumnGrpArray[count($numsetColumnGrpArray) - 1]['ID'] . "->addColumn(\$c{$key});\n";
            }

            for($loopCnt3 = count($numsetColumnGrpArray) -1; 0 <= $loopCnt3; $loopCnt3--){
                if($loopCnt3 !== 0){
                    $columnGrpParts .= "    \$cg" . $numsetColumnGrpArray[$loopCnt3 - 1]['ID'] . "->addColumn(\$cg" . $numsetColumnGrpArray[$loopCnt3]['ID'] . ");\n";
                }
                else{
                    if($substitutionFlag){
                        // データシート用はテーブル直付け
                        $columnGrpParts .= "    \$table->addColumn(\$cg" . $numsetColumnGrpArray[$loopCnt3]['ID'] . ");\n"; 
                    }else{
                        // パラメータシートは「パラメータ」直下に付ける
                        $columnGrpParts .= "    \$cg->addColumn(\$cg" . $numsetColumnGrpArray[$loopCnt3]['ID'] . ");\n";
                    }
                }
            }
        }

        $beforeColumnGrpArray = $numsetColumnGrpArray;
        $beforeKey = $key;
    }
    return $columnGrpParts;
}

/**
 * loadTable配置
 * 
 */
function deployLoadTable($fileContents, $loadTablePath, $menuId, $targetData){

    global $objDBCA, $objMTS;

    try{
        // 00_loadTable.phpを一時領域に作成する
        $result = file_put_contents($loadTablePath, $fileContents);
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5007', array($loadTablePath));
            outputLog($msg);
            throw new Exception($msg);
        }

        // 00_loadTable.phpの配置
        $destFile = ROOT_DIR_PATH . "/webconfs/sheets/{$menuId}_loadTable.php";
        $result = copy($loadTablePath, $destFile);
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($loadTablePath, $destFile));
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;
    }
    catch(Exception $e){
        // パラメータシート作成管理更新処理を行う
        updateMenuStatus($targetData, "4", $e->getMessage(), false, true);
        return $e->getMessage();
    }
}

/*
 * メニュー・テーブル紐付更新
 */
function updateMenuTableLink($menuTableName, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuTableLinkTable = new MenuTableLinkTable($objDBCA, $db_model_ch);

    try{
        $menuInfoArray = array(array($hgMenuId,     "F_" . $menuTableName . "_HG",      "F_" . $menuTableName . "_HG_JNL"),
                               array($hostMenuId,   "F_" . $menuTableName . "_H",       "F_" . $menuTableName . "_H_JNL"),
                               array($hostSubMenuId,"F_" . $menuTableName . "_H",       "F_" . $menuTableName . "_H_JNL"),
                               array($viewMenuId,   "F_" . $menuTableName . "_H",       "F_" . $menuTableName . "_H_JNL"),
                              );

        if("" != $convMenuId){
            if("" != $convHostMenuId){
                $menuInfoArray[] = array($convMenuId,       "F_" . $menuTableName . "_CONV",    "F_" . $menuTableName . "_CONV_JNL");
                $menuInfoArray[] = array($convHostMenuId,   "F_" . $menuTableName . "_CONV_H",  "F_" . $menuTableName . "_CONV_H_JNL");
            }
            else{
                $menuInfoArray[] = array($convMenuId,       "F_" . $menuTableName . "_CONV_H",  "F_" . $menuTableName . "_CONV_JNL");
            }
        }

        //////////////////////////
        // メニュー・テーブル紐付テーブルを検索
        //////////////////////////
        $sql = $menuTableLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $menuTableLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuTableLinkArray = $result;

        foreach($menuTableLinkArray as $mtlData){
            // メニューIDが一致した場合、廃止
            if($mtlData['MENU_ID'] == $hgMenuId ||
               $mtlData['MENU_ID'] == $hostMenuId ||
               $mtlData['MENU_ID'] == $hostSubMenuId ||
               $mtlData['MENU_ID'] == $viewMenuId ||
               $mtlData['MENU_ID'] == $convMenuId ||
               $mtlData['MENU_ID'] == $convHostMenuId){

                // 廃止する
                $updateData = $mtlData;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // メニュー・テーブル紐付テーブルを更新
                //////////////////////////
                $result = $menuTableLinkTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録する

        foreach($menuInfoArray as $menuInfo){

            // メニューIDが設定されている場合
            if("" != $menuInfo[0]){
                $insertData = array();
                $insertData['MENU_ID']          = $menuInfo[0];             // メニュー名
                $insertData['TABLE_NAME']       = $menuInfo[1];             // テーブル名
                $insertData['KEY_COL_NAME']     = "ROW_ID";                 // 主キー
                $insertData['TABLE_NAME_JNL']   = $menuInfo[2];             // テーブル名(履歴)
                $insertData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

                //////////////////////////
                // メニュー・テーブル紐付テーブルに登録
                //////////////////////////
                $result = $menuTableLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        return true;

    }
    catch(Exception $e){
        return $e->getMessage();
    }
}


/*
 * 他メニュー連携テーブル更新
 */
function updateOtherMenuLink($menuTableName, $itemInfoArray, $itemColumnGrpArrayArray, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData){
    global $objDBCA, $db_model_ch, $objMTS;
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 他メニュー連携テーブルを検索
        //////////////////////////
        $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $otherMenuLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $otherMenuLinkArray = $result;

        //廃止対象候補配列
        $disuseList = array();

        foreach($otherMenuLinkArray as $omlData){
            // メニューIDが一致した場合、廃止
            if($omlData['MENU_ID'] == $hgMenuId ||
               $omlData['MENU_ID'] == $hostMenuId ||
               $omlData['MENU_ID'] == $hostSubMenuId ||
               $omlData['MENU_ID'] == $viewMenuId ||
               $omlData['MENU_ID'] == $convMenuId ||
               $omlData['MENU_ID'] == $convHostMenuId){

                //廃止候補のリストに追加
                $disuseList[$omlData['LINK_ID']] = $omlData;
            }
        }

        //廃止候補の複製を作成
        $disuseListRep = $disuseList;

        // 登録するメニューID、テーブル名を決定する
        if("" != $hgMenuId && "" == $convMenuId){
            $insertMenuId = $hgMenuId;
            $insertTableName = "F_" . $menuTableName . "_HG";
        }
        else{
            $insertMenuId = $hostMenuId;
            $insertTableName = "F_" . $menuTableName . "_H";
        }

        // 登録する
        foreach($itemInfoArray as $itemInfo){
            //最終的に登録するかしないかのフラグ
            $noRegisterFlag = false;

            // プルダウン選択、パスワード、ファイルアップロードは対象外のため、スキップする
            if(7 == $itemInfo['INPUT_METHOD_ID'] || 8 == $itemInfo['INPUT_METHOD_ID'] || 9 == $itemInfo['INPUT_METHOD_ID']){
                continue;
            }
            // 必須かつ一意の場合
            if(1 == $itemInfo['REQUIRED'] && 1 == $itemInfo['UNIQUED']){

                // 項目名を決定する
                if(0 < count($itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']])){
                    if($cmiData['TARGET'] == 2){
                        //「データシート」の場合は「パラメータ」グループを付けない
                        $columnDispName = implode("/", $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']]) .
                                          "/" .
                                          $itemInfo['ITEM_NAME'];
                    }else{
                        $columnDispName = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") .
                                          "/" .
                                          implode("/", $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']]) .
                                          "/" .
                                          $itemInfo['ITEM_NAME'];
                    }
                }
                else{
                    if($cmiData['TARGET'] == 2){
                        //「データシート」の場合は「パラメータ」グループを付けない
                        $columnDispName = $itemInfo['ITEM_NAME'];
                    }else{
                        $columnDispName = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") .
                                          "/" .
                                          $itemInfo['ITEM_NAME']; 
                    }

                }

                $insertData = array();
                $insertData['MENU_ID']          = $insertMenuId;                // メニュー
                $insertData['COLUMN_DISP_NAME'] = $columnDispName;              // 項目名
                $insertData['TABLE_NAME']       = $insertTableName;             // テーブル名
                $insertData['PRI_NAME']         = "ROW_ID";                     // 主キー
                $insertData['COLUMN_NAME']      = $itemInfo['COLUMN_NAME'];     // カラム名
                $insertData['COLUMN_TYPE']      = $itemInfo['INPUT_METHOD_ID']; // カラム種別
                $insertData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];      // アクセス許可ロール
                $insertData['DISUSE_FLAG']      = "0";                          // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;         // 最終更新者

                //廃止候補のデータと「メニューID」「項目名」「テーブル名」「主キー」「カラム名」「アクセス許可ロール」が一致した場合、廃止も新規登録もしない。
                foreach($disuseList as $data){
                    if($data['MENU_ID'] == $insertData['MENU_ID'] &&
                       $data['COLUMN_DISP_NAME'] == $insertData['COLUMN_DISP_NAME'] &&
                       $data['TABLE_NAME'] == $insertData['TABLE_NAME'] &&
                       $data['PRI_NAME'] == $insertData['PRI_NAME'] &&
                       $data['COLUMN_NAME'] == $insertData['COLUMN_NAME'] &&
                       $data['ACCESS_AUTH'] == $insertData['ACCESS_AUTH']){

                        //登録しないフラグをたてる
                        $noRegisterFlag = true;

                        //廃止リストから除外
                        unset($disuseListRep[$data['LINK_ID']]);
                    }
                }

                //登録しないフラグがfalseの場合、登録を実行
                if($noRegisterFlag == false){
                    //////////////////////////
                    // 他メニュー連携テーブルに登録
                    //////////////////////////
                    $result = $otherMenuLinkTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    } 
                }
            }
        }

        //最終的に廃止リストに残ったものを廃止する
        foreach($disuseListRep as $data){
            // 廃止する
            $data['DISUSE_FLAG']      = "1";                  // 廃止フラグ
            $data['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

            //////////////////////////
            // 他メニュー連携テーブルを更新
            //////////////////////////
            $result = $otherMenuLinkTable->updateTable($data, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            } 
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}


/*
 * メニュー管理更新
 */
function updateMenuList($cmiData, &$hgMenuId, &$hostMenuId, &$hostSubMenuId,&$viewMenuId, &$convMenuId, &$convHostMenuId, $createConvFlg){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuListTable = new MenuListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー管理テーブルを検索
        //////////////////////////
        $sql = $menuListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $menuListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuListArray = $result;

        $inputMatchFlg = false;
        $substMatchFlg = false;
        $viewMatchFlg = false;
        $convHostMatchFlg = false;
        $middleHgMatchFlg = false;
        $hostSubMenuList = NULL;
        $inputMenuList = NULL;
        $substMenuList = NULL;
        $viewMenuList = NULL;
        $convHostMenuList = NULL;
        $middleHgMenuList = NULL;

        foreach($menuListArray as $menu){
            // メニューグループとメニューが一致するデータを検索
            if($cmiData['MENUGROUP_FOR_INPUT'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $inputMatchFlg = true;
                $inputMenuList = $menu;
            }
            if($cmiData['MENUGROUP_FOR_SUBST'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $substMatchFlg = true;
                $substMenuList = $menu;
            }
            else if($cmiData['MENUGROUP_FOR_VIEW'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $viewMatchFlg = true;
                $viewMenuList = $menu;
            }
            else if(MENU_GROUP_ID_CONV_HOST == $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $convHostMatchFlg = true;
                $convHostMenuList = $menu;
            }
            else if(MENU_GROUP_ID_MIDDLE_HG == $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $middleHgMatchFlg = true;
                $middleHgMenuList = $menu;
            }
        }

        $targetArray = array();

        // 作成対象:パラメータシート(ホスト/オペレーションあり)
        if("1" == $cmiData['TARGET']){
            // ホストグループあり
            if("2" == $cmiData['PURPOSE']){
                // 縦メニューあり
                if(true === $createConvFlg){
                    $targetArray[] = array('MATCH_FLG' => $inputMatchFlg, 'DATA' => $inputMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_INPUT']);
                    $targetArray[] = array('MATCH_FLG' => $middleHgMatchFlg, 'DATA' => $middleHgMenuList, 'MENU_GROUP' => MENU_GROUP_ID_MIDDLE_HG);
                    $targetArray[] = array('MATCH_FLG' => $convHostMatchFlg, 'DATA' => $convHostMenuList, 'MENU_GROUP' => MENU_GROUP_ID_CONV_HOST);
                    $targetArray[] = array('MATCH_FLG' => $substMatchFlg, 'DATA' => $substMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_SUBST']);
                    $targetArray[] = array('MATCH_FLG' => $viewMatchFlg, 'DATA' => $viewMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_VIEW']);
                }
                // 縦メニューなし
                else{
                    $targetArray[] = array('MATCH_FLG' => $inputMatchFlg, 'DATA' => $inputMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_INPUT']);
                    $targetArray[] = array('MATCH_FLG' => $substMatchFlg, 'DATA' => $substMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_SUBST']);
                    $targetArray[] = array('MATCH_FLG' => $viewMatchFlg, 'DATA' => $viewMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_VIEW']);
                }
            }
            // ホストグループなし
            else{
                $targetArray[] = array('MATCH_FLG' => $inputMatchFlg, 'DATA' => $inputMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_INPUT']);
                $targetArray[] = array('MATCH_FLG' => $substMatchFlg, 'DATA' => $substMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_SUBST']);
                $targetArray[] = array('MATCH_FLG' => $viewMatchFlg, 'DATA' => $viewMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_VIEW']);
            }
        }
        // 作成対象:パラメータシート(オペレーションあり)
        else if("3" == $cmiData['TARGET']){
            $targetArray[] = array('MATCH_FLG' => $inputMatchFlg, 'DATA' => $inputMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_INPUT']);
            $targetArray[] = array('MATCH_FLG' => $substMatchFlg, 'DATA' => $substMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_SUBST']);
            $targetArray[] = array('MATCH_FLG' => $viewMatchFlg, 'DATA' => $viewMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_VIEW']);
        }
        // 作成対象; データシート
        else if("2" == $cmiData['TARGET']){
            $targetArray[] = array('MATCH_FLG' => $inputMatchFlg, 'DATA' => $inputMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_INPUT']);
        }

        foreach($targetArray as &$target){

            // メニューグループとメニューが一致するデータがあった場合
            if(true === $target['MATCH_FLG']){

                $target['MENU_ID'] = $target['DATA']['MENU_ID'];

                // 更新する
                $updateData = $target['DATA'];
                $updateData['LOGIN_NECESSITY']      = 1;                        // 認証要否
                $updateData['SERVICE_STATUS']       = 0;                        // サービス状態
                $updateData['DISP_SEQ']             = $cmiData['DISP_SEQ'];     // メニューグループ内表示順序
                $updateData['AUTOFILTER_FLG']       = 1;                        // オートフィルタチェック
                $updateData['INITIAL_FILTER_FLG']   = 2;                        // 初回フィルタ
                $updateData['WEB_PRINT_LIMIT']      = NULL;                     // Web表示最大行数
                $updateData['WEB_PRINT_CONFIRM']    = NULL;                     // Web表示前確認行数
                $updateData['XLS_PRINT_LIMIT']      = NULL;                     // Excel出力最大行数
                $updateData['ACCESS_AUTH']          = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                $updateData['NOTE']                 = NULL;                     // 備考
                $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;     // 最終更新者

                //////////////////////////
                // メニュー管理テーブルを更新
                //////////////////////////
                $result = $menuListTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
            // メニューグループとメニューが一致するデータが無かった場合
            else{

                // 登録する
                $insertData = array();
                $insertData['MENU_GROUP_ID']        = $target['MENU_GROUP'];    // メニューグループ
                $insertData['MENU_NAME']            = $cmiData['MENU_NAME'];    // メニュー
                $insertData['LOGIN_NECESSITY']      = 1;                        // 認証要否
                $insertData['SERVICE_STATUS']       = 0;                        // サービス状態
                $insertData['DISP_SEQ']             = $cmiData['DISP_SEQ'];     // メニューグループ内表示順序
                $insertData['AUTOFILTER_FLG']       = 1;                        // オートフィルタチェック
                $insertData['INITIAL_FILTER_FLG']   = 2;                        // 初回フィルタ
                $insertData['ACCESS_AUTH']          = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                $insertData['DISUSE_FLAG']          = "0";                      // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;     // 最終更新者

                //////////////////////////
                // メニュー管理テーブルに登録
                //////////////////////////
                $result = $menuListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }

                $target['MENU_ID'] = $seqNo;
            }
        }
        unset($target);

        //////////////////////////
        // メニューIDを取得する
        //////////////////////////
        // 作成対象:パラメータシート(ホスト/オペレーションあり)
        if("1" == $cmiData['TARGET']){
            // ホストグループあり
            if("2" == $cmiData['PURPOSE']){
                // 縦メニューあり
                if(true === $createConvFlg){
                    $convMenuId = $targetArray[0]['MENU_ID'];
                    $hgMenuId   = $targetArray[1]['MENU_ID'];
                    $convHostMenuId = $targetArray[2]['MENU_ID'];
                    $hostMenuId = $targetArray[3]['MENU_ID'];
                    $viewMenuId = $targetArray[4]['MENU_ID'];
                }
                // 縦メニューなし
                else{
                    $hgMenuId   = $targetArray[0]['MENU_ID'];
                    $hostMenuId = $targetArray[1]['MENU_ID'];
                    $viewMenuId = $targetArray[2]['MENU_ID'];
                }
            }
            // ホストグループなし
            else{
                // 縦メニューあり
                if(true === $createConvFlg){
                    $convMenuId = $targetArray[0]['MENU_ID'];
                    $hostMenuId = $targetArray[1]['MENU_ID'];
                    $viewMenuId = $targetArray[2]['MENU_ID'];
                }
                // 縦メニューなし
                else{
                    $hostMenuId = $targetArray[0]['MENU_ID'];
                    $hostSubMenuId = $targetArray[1]['MENU_ID'];
                    $viewMenuId = $targetArray[2]['MENU_ID'];
                }
            }
        }
        // 作成対象:パラメータシート(オペレーションあり)
        else if("3" == $cmiData['TARGET']){
            // 縦メニューあり
            if(true === $createConvFlg){
                $convMenuId = $targetArray[0]['MENU_ID'];
                $hostMenuId = $targetArray[1]['MENU_ID'];
                $viewMenuId = $targetArray[2]['MENU_ID'];

            }
            // 縦メニューなし
            else{
                $hostMenuId = $targetArray[0]['MENU_ID'];
                $hostSubMenuId = $targetArray[1]['MENU_ID'];
                $viewMenuId = $targetArray[2]['MENU_ID'];
            }
        }
        // 作成対象; データシート
        else if("2" == $cmiData['TARGET']){
            $hostMenuId = $targetArray[0]['MENU_ID'];  // MenuID はホスト用を使用
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * メニュー管理廃止(利用していないメニューグループのメニューを廃止)
 */
function discardMenuList($cmiData, &$discardMenuIdArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuListTable = new MenuListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー管理テーブル内を対象のメニュー名で検索
        //////////////////////////
        $menuName = $cmiData['MENU_NAME'];
        $sql = $menuListTable->createSselect("WHERE DISUSE_FLAG = '0' AND MENU_NAME = :MENU_NAME");
        $sqlBind = array('MENU_NAME' => $menuName);

        // SQL実行
        $result = $menuListTable->selectTable($sql, $sqlBind);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuListArray = $result;

        $menuGroupForInput = $cmiData['MENUGROUP_FOR_INPUT'];
        $menuGroupForSubst = $cmiData['MENUGROUP_FOR_SUBST'];
        $menuGroupForView  = $cmiData['MENUGROUP_FOR_VIEW'];

        foreach($menuListArray as $menuData){
            //入力用のメニューグループが一致する場合は処理をスキップ
            if($menuData['MENU_GROUP_ID'] == $menuGroupForInput){
                continue;
            }
            //代入値自動登録用のメニューグループが一致する場合は処理をスキップ
            if($menuData['MENU_GROUP_ID'] == $menuGroupForSubst){
                continue;
            }
            //参照用のメニューグループが一致する場合は処理をスキップ
            if($menuData['MENU_GROUP_ID'] == $menuGroupForView){
                continue;
            }
            //「ホストグループ」「縦メニュー利用」が両方利用ありの場合
            if($cmiData['PURPOSE'] == 2 && $cmiData['TARGET'] == 1){
                //メニューグループIDが2100011609(縦メニューホスト分解用中間シート)の場合はスキップ
                if($menuData['MENU_GROUP_ID'] == '2100011609'){
                    continue;
                }
                //メニューグループIDが2100011613(縦横変換用中間シート)の場合はスキップ
                if($menuData['MENU_GROUP_ID'] == '2100011613'){
                    continue;
                }
            }

            //対象メニューグループで利用が無いメニューを廃止する
            $updateData = $menuData;
            $updateData['DISUSE_FLAG']          = "1"; //廃止
            $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;     // 最終更新者

            //////////////////////////
            // メニュー管理テーブルを更新
            //////////////////////////
            $result = $menuListTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }

            //廃止したメニューIDを保管
            array_push($discardMenuIdArray, $menuData['MENU_ID']);

        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * メニュー管理で廃止したレコードと同じメニューIDが紐付いているレコードを廃止する（「メニュー・テーブル紐付」「メニュー縦横変換管理」テーブルが対象）
 */
function discardRerationTable($discardMenuIdArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuTableLinkTable = new MenuTableLinkTable($objDBCA, $db_model_ch);
    $colToRowMngTable = new ColToRowMngTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // メニュー・テーブル紐付テーブルを検索
        //////////////////////////
        $sql = $menuTableLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $menuTableLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $menuTableLinkArray = $result;

        //「メニュー・テーブル紐付」のレコードで、「メニュー管理」で廃止されたIDを利用している対象を廃止する。
        foreach($menuTableLinkArray as $mtlData){
            if(in_array($mtlData['MENU_ID'], $discardMenuIdArray, true)){
                //すでに廃止されている場合は除外
                if($mtlData['DISUSE_FLAG'] != "1"){
                    // 廃止する
                    $updateData = $mtlData;
                    $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                    //////////////////////////
                    // メニュー・テーブル紐付テーブルを更新
                    //////////////////////////
                    $result = $menuTableLinkTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                }
            }
        }

        //////////////////////////
        // パラメータシート縦横変換管理テーブルを検索
        //////////////////////////
        $sql = $colToRowMngTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $colToRowMngTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $colToRowMngArray = $result;

        //「メニュー縦横変換管理」のレコードで、「メニュー管理」で廃止されたIDを利用している対象を廃止する。
        foreach($colToRowMngArray as $colToRowMng){
            if(in_array($colToRowMng['FROM_MENU_ID'], $discardMenuIdArray, true) || in_array($colToRowMng['TO_MENU_ID'], $discardMenuIdArray, true)){
                //すでに廃止されている場合は除外
                if($colToRowMng['DISUSE_FLAG'] != "1"){
                    // 廃止する
                    $updateData = $colToRowMng;
                    $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                    //////////////////////////
                    // パラメータシート縦横変換管理テーブルを更新
                    //////////////////////////
                    $result = $colToRowMngTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                        outputLog($msg);
                        throw new Exception($msg);
                    }
                }
            }
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * シーケンステーブル更新(シーケンス管理メニュー対応)
 */
function updateSequence($hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $target, $menuTableName){
    global $objDBCA, $db_model_ch, $objMTS;
    $sqlArray = array();
    try{
        // データシートの場合
        if("2" == $target){
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} WHERE NAME ='F_${menuTableName}_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_H_JSQ'";
        }
        // パラメータシートの場合
        else{
            // ホストグループかつ縦メニュー
            if(NULL !== $convMenuId && NULL !== $hgMenuId){
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hgMenuId} WHERE NAME ='F_${menuTableName}_HG_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hgMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_HG_JSQ'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convMenuId} WHERE NAME ='F_${menuTableName}_CONV_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_CONV_JSQ'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convHostMenuId} WHERE NAME ='F_${menuTableName}_CONV_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convHostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_CONV_H_JSQ'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} WHERE NAME ='F_${menuTableName}_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_H_JSQ'";
            }
            // 縦メニュー
            else if(NULL !== $convMenuId && NULL === $hgMenuId){
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convMenuId} WHERE NAME ='F_${menuTableName}_CONV_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${convMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_CONV_H_JSQ'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} WHERE NAME ='F_${menuTableName}_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_H_JSQ'";
            }
            // ホストグループ
            else if(NULL === $convMenuId && NULL !== $hgMenuId){
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hgMenuId} WHERE NAME ='F_${menuTableName}_HG_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hgMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_HG_JSQ'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} WHERE NAME ='F_${menuTableName}_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_H_JSQ'";
            }
            // 通常
            else{
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} WHERE NAME ='F_${menuTableName}_H_RIC'";
                $sqlArray[] = "UPDATE A_SEQUENCE SET MENU_ID=${hostMenuId} ,NOTE='" . $objMTS->getSomeMessage('ITACREPAR-STD-50003') . "' WHERE NAME ='F_${menuTableName}_H_JSQ'";
            }
        }

        $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);
        foreach($sqlArray as $sql){

            // SQL実行
            $result = $baseTable->execQuery($sql, NULL, $objQuery);
            if(true !== $result){
                outputLog("SQL=$sql");
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * ロール・メニュー紐付管理更新
 */
function updateRoleMenuLinkList($targetData, $hgMenuId, $hostMenuId, $hostSubMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $cmiData, $roleUserLinkArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $roleMenuLinkListTable = new RoleMenuLinkListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // ロール・メニュー紐付管理テーブルを検索
        //////////////////////////
        $sql = $roleMenuLinkListTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $roleMenuLinkListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $roleMenuLinkListArray = $result;

        foreach($roleMenuLinkListArray as $roleMenuLink){
            // メニューIDが一致したデータを廃止
            if($roleMenuLink['MENU_ID'] == $hgMenuId ||
               $roleMenuLink['MENU_ID'] == $hostMenuId ||
               $roleMenuLink['MENU_ID'] == $hostSubMenuId ||
               $roleMenuLink['MENU_ID'] == $viewMenuId ||
               $roleMenuLink['MENU_ID'] == $convMenuId ||
               $roleMenuLink['MENU_ID'] == $convHostMenuId){

                // すでに廃止ならスキップ
                if($roleMenuLink['DISUSE_FLAG'] == "1"){
                    continue;
                }

                // 廃止する
                $updateData = $roleMenuLink;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // ロール・メニュー紐付管理テーブルを更新
                //////////////////////////
                $result = $roleMenuLinkListTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }
        
        // データシートの場合
        if("2" == $cmiData['TARGET']){
                $menuArray = array(array($hostMenuId,   1,  '0'));
        }
        // パラメータシートの場合
        else{
            if(NULL !== $convMenuId && NULL !== $hgMenuId){
                $menuArray = array(array($convMenuId,       1,  '0'),
                                   array($hgMenuId,         2,  '1'),
                                   array($hostMenuId,       2,  '0'),
                                   array($viewMenuId,       2,  '0'),
                                   array($convHostMenuId,   2,  '1'),
                                  );
            }
            else if(NULL !== $convMenuId && NULL === $hgMenuId){
                $menuArray = array(array($convMenuId,   1,  '0'),
                                   array($hostMenuId,   2,  '0'),
                                   array($viewMenuId,   2,  '0'),
                                  );
            }
            else if(NULL === $convMenuId && NULL !== $hgMenuId){
                $menuArray = array(array($hgMenuId,     1,  '0'),
                                   array($hostMenuId,   2,  '0'),
                                   array($viewMenuId,   2,  '0'),
                                  );
            }
            else{
                $menuArray = array(array($hostMenuId,   1,  '0'),
                                   array($hostSubMenuId,2,  '0'),
                                   array($viewMenuId,   2,  '0'),
                                  );
            }
        }

        $roles = array();

        // アクセス許可ロールが設定されている場合、アクセス許可ロールにのみレコードを作成する
        if($cmiData['ACCESS_AUTH'] != ""){
            $roles = explode(",", $cmiData['ACCESS_AUTH']);
        }
        // アクセス許可ロールが設定されていない場合、administrator＋所属するロールにレコードを作成する
        else{
            foreach($roleUserLinkArray as $rUL){
                if($rUL['USER_ID'] == $targetData['LAST_UPDATE_USER']){
                    $roles[] = $rUL['ROLE_ID'];
                }

            }
            // 管理者ロールは入れていない場合
            if(!in_array("1",$roles)){
                $roles[] = "1";
            }
        }

        foreach($menuArray as $menu){
            foreach($roles as $role){
                // 登録する
                $insertData = array();
                $insertData['ROLE_ID']          = $role;                    // ロール
                $insertData['MENU_ID']          = $menu[0];                 // メニュー
                $insertData['PRIVILEGE']        = $menu[1];                 // 紐付
                $insertData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                $insertData['DISUSE_FLAG']      = $menu[2];                 // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

                //////////////////////////
                // ロール・メニュー紐付管理テーブルに登録
                //////////////////////////
                $result = $roleMenuLinkListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 紐付対象メニュー更新
 */
function updateLinkTargetMenu($targetMenuId, $noLinkTarget, $cmiData, $sheetType){
    global $objDBCA, $db_model_ch, $objMTS;
    $matchFlg = false;
    $cmdbMenuListTable = new CmdbMenuListTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 紐付対象メニューテーブルを検索
        //////////////////////////
        $sql = $cmdbMenuListTable->createSselect();

        // SQL実行
        $result = $cmdbMenuListTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $cmdbMenuListArray = $result;

        foreach($cmdbMenuListArray as $cmdbMenuList){
            // メニューIDが一致するデータがあった場合
            if($cmdbMenuList['MENU_ID'] == $targetMenuId){
                $matchFlg = true;
                $updateData = $cmdbMenuList;

                // 廃止、紐づけ対象カラムがある場合
                if($cmdbMenuList['DISUSE_FLAG'] == "1" && $noLinkTarget == false){

                    // 復活する
                    $updateData['SHEET_TYPE']       = $sheetType;               // シートタイプ
                    $updateData['ACCESS_AUTH_FLG']  = 1;                        // アクセス許可ロール有無
                    $updateData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                    $updateData['NOTE']             = "";                       // 備考
                    $updateData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                // 紐づけ対象カラムがない場合 
                else if($cmdbMenuList['DISUSE_FLAG'] == "0" && $noLinkTarget == true){

                    // 廃止する
                    $updateData['SHEET_TYPE']       = $sheetType;               // シートタイプ
                    $updateData['NOTE']             = "";                       // 備考
                    $updateData['DISUSE_FLAG']      = "1";                      // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                // 値が更新されている場合
                else if(($updateData['SHEET_TYPE'] != $sheetType || $updateData['ACCESS_AUTH_FLG'] != 1 || $updateData['ACCESS_AUTH'] != $cmiData['ACCESS_AUTH']) &&  $noLinkTarget == false){
                    $updateData['SHEET_TYPE']       = $sheetType;               // シートタイプ
                    $updateData['ACCESS_AUTH_FLG']  = 1;                        // アクセス許可ロール有無
                    $updateData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                // 他の場合は更新しない
                else{
                    break;
                }
                //////////////////////////
                // 紐付対象メニューテーブルを更新
                //////////////////////////
                $result = $cmdbMenuListTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                break;
            }
        }

        // メニューIDが一致するデータが無かった場合
        if(false === $matchFlg && $noLinkTarget == false){

            // 登録する
            $insertData = array();
            $insertData['MENU_ID']          = $targetMenuId;            // メニュー
            $insertData['SHEET_TYPE']       = $sheetType;               // シートタイプ
            $insertData['ACCESS_AUTH_FLG']  = 1;                        // アクセス許可ロール有無
            $insertData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
            $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

            //////////////////////////
            // 紐付対象メニューテーブルに登録
            //////////////////////////
            $result = $cmdbMenuListTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 紐付対象メニューテーブル管理更新
 */
function updateLinkTargetTable($hostMenuId, $tableName, $noLinkTarget, $cmiData){
    global $objDBCA, $db_model_ch, $objMTS;
    $matchFlg = false;
    $cmdbMenuTableTable = new CmdbMenuTableTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 紐付対象メニューテーブル管理テーブルを検索
        //////////////////////////
        $sql = $cmdbMenuTableTable->createSselect();

        // SQL実行
        $result = $cmdbMenuTableTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $cmdbMenuTableArray = $result;

        foreach($cmdbMenuTableArray as $cmdbMenuTable){
            // メニューIDが一致するデータがあった場合
            if($cmdbMenuTable['MENU_ID'] == $hostMenuId){
                $matchFlg = true;
                $updateData = $cmdbMenuTable;

                // 廃止の場合は更新する
                if($cmdbMenuTable['DISUSE_FLAG'] == "1" && $noLinkTarget == false){
                    $updateData['TABLE_NAME']       = $tableName;               // テーブル名
                    $updateData['PKEY_NAME']        = "ROW_ID";                 // 主キー
                    $updateData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                    $updateData['NOTE']             = "";                       // 備考
                    $updateData['DISUSE_FLAG']      = "0";                      // 廃止フラグ(紐づけ対象あり)
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                // 紐づけ対象がなくなった場合、更新する
                else if($cmdbMenuTable['DISUSE_FLAG'] == "0" && $noLinkTarget == true){
                    $updateData['DISUSE_FLAG']  = "1";                          // 廃止フラグ(紐づけ対象なし)
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                // テーブル名が異なる場合は更新する
                else if(($cmdbMenuTable['TABLE_NAME'] != $tableName || $cmdbMenuTable['ACCESS_AUTH'] != $cmiData['ACCESS_AUTH']) && $noLinkTarget == false){
                    $updateData['TABLE_NAME']       = $tableName;               // テーブル名
                    $updateData['PKEY_NAME']        = "ROW_ID";                 // 主キー
                    $updateData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
                    $updateData['NOTE']             = "";                       // 備考
                    $updateData['DISUSE_FLAG']      = "0";                      // 廃止フラグ(紐づけ対象あり)
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者
                }
                else{
                    break;
                }

                //////////////////////////
                // 紐付対象メニューテーブル管理テーブルを更新
                //////////////////////////
                $result = $cmdbMenuTableTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
                break;
            }
        }

        // メニューIDが一致するデータが無かった場合
        if(false === $matchFlg && $noLinkTarget == false){

            // 登録する
            $insertData = array();
            $insertData['MENU_ID']          = $hostMenuId;              // メニュー
            $insertData['TABLE_NAME']       = $tableName;               // テーブル名
            $insertData['PKEY_NAME']        = "ROW_ID";                 // 主キー
            $insertData['ACCESS_AUTH']      = $cmiData['ACCESS_AUTH'];  // アクセス許可ロール
            $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

            //////////////////////////
            // 紐付対象メニューテーブル管理テーブルに登録
            //////////////////////////
            $result = $cmdbMenuTableTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 紐付対象メニューカラム管理更新
 */
function updateLinkTargetColumn($hostMenuId, $itemInfoArray, $itemColumnGrpArrayArray, $cmiData, $noLinkTarget, $otherMenuLinkArray, $duplicateItemNameArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);
    $cmdbMenuColumnTable = new CmdbMenuColumnTable($objDBCA, $db_model_ch);
    $createItemInfoTable = new CreateItemInfoTable($objDBCA, $db_model_ch);
    $referenceItemTable = new ReferenceItemTable($objDBCA, $db_model_ch);
    $type3ReferenceView = new ReferenceSheetType3View($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // 他メニュー連携テーブルを検索
        //////////////////////////
        $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $otherMenuLinkTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $otherMenuLinkArrayTmp = $result;

        //////////////////////////
        // 参照項目情報テーブルを検索
        //////////////////////////
        $referenceItemArray = array();
        $sql = $referenceItemTable->createSselect("WHERE DISUSE_FLAG = '0'");
        // SQL実行
        $result = $referenceItemTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        if(!empty($result)){
            //検索しやすいようにITEM_IDをkeyにする
            foreach($result as $row){
                $referenceItemArray[$row['ITEM_ID']] = $row;
            }  
        }

        //////////////////////////
        // パラメータシート参照ビューを検索
        //////////////////////////
        $type3ReferenceArray = array();
        $sql = $type3ReferenceView->createSselect("WHERE DISUSE_FLAG = '0'");
        // SQL実行
        $result = $type3ReferenceView->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        if(!empty($result)){
            //検索しやすいようにITEM_IDをkeyにする
            foreach($result as $row){
                $type3ReferenceArray[$row['ITEM_ID']] = $row;
            }  
        }

        //////////////////////////
        // 登録するカラム情報を作成する
        //////////////////////////
        $columnInfoArray = array();
        $loopCount = 0;
        foreach($itemInfoArray as $key => $itemInfo){
            if($loopCount == 0){
                //COL_TITLE_DISP_SEQにセットする最初の値を決定
                $seqNo = $key + 2;
            }
            $loopCount++;
            $aryReferenceItem = array();
            $referenceCount = 0;

            if(5 == $itemInfo['INPUT_METHOD_ID'] || 6 == $itemInfo['INPUT_METHOD_ID']){
                continue;
            }
            if(7 == $itemInfo['INPUT_METHOD_ID']){
                $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArrayTmp, 'LINK_ID'));
                $otherMenuLink = $otherMenuLinkArrayTmp[$matchIdx];
                if(5 == $otherMenuLink['COLUMN_TYPE'] || 6 == $otherMenuLink['COLUMN_TYPE'] || 8 == $otherMenuLink['COLUMN_TYPE'] || 9 == $otherMenuLink['COLUMN_TYPE']){
                    continue;
                }
                if(1 == $otherMenuLink['COLUMN_TYPE']){
                    $colClass = "TextColumn";
                }
                else if(2 == $otherMenuLink['COLUMN_TYPE']){
                    $colClass = "MultiTextColumn";
                }
                else if(3 == $otherMenuLink['COLUMN_TYPE'] || 4 == $otherMenuLink['COLUMN_TYPE']){
                    $colClass = "NumColumn";
                }
                else if(10 == $otherMenuLink['COLUMN_TYPE']){
                    $colClass = "HostInsideLinkTextColumn";
                }

                //参照項目がある場合、配列化する
                if(!empty($itemInfo['REFERENCE_ITEM'])){
                    $aryReferenceItem = explode(',', $itemInfo['REFERENCE_ITEM']);
                }
            }
            else if(1 == $itemInfo['INPUT_METHOD_ID']){
                $colClass = "TextColumn";
            }
            else if(2 == $itemInfo['INPUT_METHOD_ID']){
                $colClass = "MultiTextColumn";
            }
            else if(3 == $itemInfo['INPUT_METHOD_ID'] || 4 == $itemInfo['INPUT_METHOD_ID']){
                $colClass = "NumColumn";
            }
            else if(8 == $itemInfo['INPUT_METHOD_ID']){
                $colClass = "PasswordColumn";
            }
            else if(9 == $itemInfo['INPUT_METHOD_ID']){
                if(true == $noLinkTarget){
                    continue;
                }
                $colClass = "FileUploadColumn";
            }
            else if(10 == $itemInfo['INPUT_METHOD_ID']){
                $colClass = "HostInsideLinkTextColumn";
            }
            else if(11 == $itemInfo['INPUT_METHOD_ID']){
                $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                if(empty($type3ReferenceData)){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                    outputLog($msg);
                    throw new Exception($msg);
                }

                if(5 == $type3ReferenceData['INPUT_METHOD_ID'] || 6 == $type3ReferenceData['INPUT_METHOD_ID']){
                    continue;
                }
                if(7 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "TextColumn";
                }
                else if(1 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "TextColumn";
                }
                else if(2 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "MultiTextColumn";
                }
                else if(3 == $type3ReferenceData['INPUT_METHOD_ID'] || 4 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "NumColumn";
                }
                else if(8 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "PasswordColumn";
                }
                else if(9 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "TextColumn"; //ファイルアップロードカラムを参照の場合、TextColumnとして登録する。
                }
                else if(10 == $type3ReferenceData['INPUT_METHOD_ID']){
                    $colClass = "HostInsideLinkTextColumn";
                }
                else if(11 == $type3ReferenceData['INPUT_METHOD_ID']){
                    continue;
                }else{
                    $colClass = "TextColumn";
                }
            }
            
            // 項目名を作成
            $columnGrp = implode("/", $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']]);
            if("" != $columnGrp){
                $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $columnGrp . "/" . $itemInfo['ITEM_NAME'];
            }
            else{
                $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $itemInfo['ITEM_NAME'];
            }

            //カラム名
            $columnName = $itemInfo['COLUMN_NAME'];

            $otherTableName = null;
            $otherPriName = null;
            $otherColumnName = null;
            //プルダウン選択の場合の、参照元情報を決定
            if(7 == $itemInfo['INPUT_METHOD_ID']){
                // 他メニュー連携の情報を取得する
                if("" != $itemInfo['OTHER_MENU_LINK_ID']){
                    foreach($otherMenuLinkArrayTmp as $otherMenuLink){
                        if($itemInfo['OTHER_MENU_LINK_ID'] == $otherMenuLink['LINK_ID']){
                            $otherTableName = $otherMenuLink['TABLE_NAME'];
                            $otherPriName = $otherMenuLink['PRI_NAME'];
                            $otherColumnName = $otherMenuLink['COLUMN_NAME'];
                            break;
                        }
                    }
                }
            }

            //パラメータシート参照の場合の、参照元情報を決定
            if(11 == $itemInfo['INPUT_METHOD_ID']){
                if("" != $itemInfo['TYPE3_REFERENCE']){
                    $type3ReferenceData = $type3ReferenceArray[$itemInfo['TYPE3_REFERENCE']];
                    if(empty($type3ReferenceData)){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5019', array($itemInfo['CREATE_ITEM_ID']));
                        outputLog($msg);
                        throw new Exception($msg);
                    }

                    $otherTableName = $type3ReferenceData['TABLE_NAME'];
                    $otherPriName = 'OPERATION_ID';
                    $otherColumnName = $type3ReferenceData['COLUMN_NAME'];

                    //カラム名に_CLONE_1を追記
                    $columnName = $itemInfo['COLUMN_NAME'] . "_CLONE_1";
                }
            }

            $columnInfoArray[] = array('COL_NAME' => $columnName,
                                       'COL_CLASS' => $colClass,
                                       'COL_TITLE' => $columnTitle,
                                       'COL_TITLE_DISP_SEQ' => $seqNo,
                                       'REF_TABLE_NAME' => $otherTableName,
                                       'REF_PKEY_NAME' => $otherPriName,
                                       'REF_COL_NAME' => $otherColumnName,
                                      );
            //COL_TITLE_DISP_SEQにセットする値を++
            $seqNo++;

            //参照項目についての紐付対象メニューカラム情報を作成する
            if(!empty($aryReferenceItem)){
                //参照項目をリピートする場合、項目名の末尾に追加する[x]を抽出
                $extractRepeatNoStr = "";
                if(in_array($itemInfo['ITEM_NAME'], $duplicateItemNameArray)){
                    $extractRepeatNoStr = extractRepeatItemNo($itemInfo['ITEM_NAME']);
                }

                foreach($aryReferenceItem as $id){
                    $referenceCount++;

                    //対象の参照項目情報
                    $referenceItemInfo = $referenceItemArray[$id];

                    if(5 == $referenceItemInfo['INPUT_METHOD_ID'] || 6 == $referenceItemInfo['INPUT_METHOD_ID']){
                        continue;
                    }
                    if(7 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "TextColumn";
                    }
                    else if(1 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "TextColumn";
                    }
                    else if(2 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "MultiTextColumn";
                    }
                    else if(3 == $referenceItemInfo['INPUT_METHOD_ID'] || 4 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "NumColumn";
                    }
                    else if(8 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "PasswordColumn";
                    }
                    else if(9 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "TextColumn"; //ファイルアップロードカラムを参照の場合、TextColumnとして登録する。
                    }
                    else if(10 == $referenceItemInfo['INPUT_METHOD_ID']){
                        $colClass = "HostInsideLinkTextColumn";
                    }else{
                        $colClass = "TextColumn";
                    }

                    // 項目名を作成
                    if("" != $columnGrp){
                        $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $columnGrp . "/" . $referenceItemInfo['ITEM_NAME'] . $extractRepeatNoStr;
                    }
                    else{
                        $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $referenceItemInfo['ITEM_NAME'] . $extractRepeatNoStr;
                    }

                    $colName = $itemInfo['COLUMN_NAME'] . "_CLONE_" . $referenceCount;
                    $otherTableName =  $referenceItemInfo['TABLE_NAME'];
                    $otherPriName = $referenceItemInfo['PRI_NAME'];
                    $otherColumnName = $referenceItemInfo['COLUMN_NAME'];

                    $columnInfoArray[] = array('COL_NAME'           => $colName,
                                               'COL_CLASS'          => $colClass,
                                               'COL_TITLE'          => $columnTitle,
                                               'COL_TITLE_DISP_SEQ' => $seqNo,
                                               'REF_TABLE_NAME'     => $otherTableName,
                                               'REF_PKEY_NAME'      => $otherPriName,
                                               'REF_COL_NAME'       => $otherColumnName,
                                               );
                    //COL_TITLE_DISP_SEQにセットする値を++
                    $seqNo++;
                }
            }
        }

        //////////////////////////
        // 紐付対象メニューカラム管理テーブルを検索
        //////////////////////////
        $sql = $cmdbMenuColumnTable->createSselect();

        // SQL実行
        $result = $cmdbMenuColumnTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $cmdbMenuColumnArray = $result;

        //////////////////////////
        // 紐付対象メニューカラム管理テーブルの登録・更新処理を行う
        //////////////////////////
        foreach($columnInfoArray as $columnInfo){

            $matchFlg = false;
            foreach($cmdbMenuColumnArray as $cmdbMenuColumn){
                // メニューID、カラム名が一致するデータがあった場合
                if($cmdbMenuColumn['MENU_ID'] == $hostMenuId && $cmdbMenuColumn['COL_NAME'] === $columnInfo['COL_NAME']){
                    $matchFlg = true;

                    $updateFlg = false;
                    // 廃止の場合、更新する
                    if($cmdbMenuColumn['DISUSE_FLAG'] == "1"){
                        $updateFlg = true;
                    }
                    // 各値のいずれかに変更がある場合、更新する
                    if($cmdbMenuColumn['COL_CLASS']             != $columnInfo['COL_CLASS'] ||
                       $cmdbMenuColumn['COL_TITLE']             != $columnInfo['COL_TITLE'] ||
                       $cmdbMenuColumn['COL_TITLE_DISP_SEQ']    != $columnInfo['COL_TITLE_DISP_SEQ'] ||
                       $cmdbMenuColumn['REF_TABLE_NAME']        != $columnInfo['REF_TABLE_NAME'] ||
                       $cmdbMenuColumn['REF_PKEY_NAME']         != $columnInfo['REF_PKEY_NAME'] ||
                       $cmdbMenuColumn['REF_COL_NAME']          != $columnInfo['REF_COL_NAME'] ||
                       $cmdbMenuColumn['ACCESS_AUTH']           != $cmiData['ACCESS_AUTH']){

                        $updateFlg = true;
                    }

                    if(true === $updateFlg){
                        // 更新する
                        $updateData = $cmdbMenuColumn;
                        $updateData['MENU_ID']              = $hostMenuId;                          // メニュー
                        $updateData['COL_NAME']             = $columnInfo['COL_NAME'];              // カラム名
                        $updateData['COL_CLASS']            = $columnInfo['COL_CLASS'];             // カラムタイプ
                        $updateData['COL_TITLE']            = $columnInfo['COL_TITLE'];             // 項目名
                        $updateData['COL_TITLE_DISP_SEQ']   = $columnInfo['COL_TITLE_DISP_SEQ'];    // 表示順
                        $updateData['REF_TABLE_NAME']       = $columnInfo['REF_TABLE_NAME'];        // 参照テーブル
                        $updateData['REF_PKEY_NAME']        = $columnInfo['REF_PKEY_NAME'];         // 参照主キー
                        $updateData['REF_COL_NAME']         = $columnInfo['REF_COL_NAME'];          // 参照カラム
                        $updateData['ACCESS_AUTH']          = $cmiData['ACCESS_AUTH'];              // アクセス許可ロール
                        $updateData['NOTE']                 = "";                                   // 備考
                        $updateData['DISUSE_FLAG']          = "0";                                  // 廃止フラグ
                        $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;                 // 最終更新者

                        //////////////////////////
                        // 紐付対象メニューカラム管理テーブルを更新
                        //////////////////////////
                        $result = $cmdbMenuColumnTable->updateTable($updateData, $jnlSeqNo);
                        if(true !== $result){
                            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                            outputLog($msg);
                            throw new Exception($msg);
                        }
                    }
                    break;
                }
            }

            // メニューIDが一致するデータが無かった場合
            if(false === $matchFlg){

                // 登録する
                $insertData = array();
                $insertData['MENU_ID']              = $hostMenuId;                          // メニュー
                $insertData['COL_NAME']             = $columnInfo['COL_NAME'];              // カラム名
                $insertData['COL_CLASS']            = $columnInfo['COL_CLASS'];             // カラムタイプ
                $insertData['COL_TITLE']            = $columnInfo['COL_TITLE'];             // 項目名
                $insertData['COL_TITLE_DISP_SEQ']   = $columnInfo['COL_TITLE_DISP_SEQ'];    // 表示順
                $insertData['REF_TABLE_NAME']       = $columnInfo['REF_TABLE_NAME'];        // 参照テーブル
                $insertData['REF_PKEY_NAME']        = $columnInfo['REF_PKEY_NAME'];         // 参照主キー
                $insertData['REF_COL_NAME']         = $columnInfo['REF_COL_NAME'];          // 参照カラム
                $insertData['ACCESS_AUTH']          = $cmiData['ACCESS_AUTH'];              // アクセス許可ロール
                $insertData['DISUSE_FLAG']          = "0";                                  // 廃止フラグ
                $insertData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;                 // 最終更新者

                //////////////////////////
                // 紐付対象メニューカラム管理テーブルに登録
                //////////////////////////
                $result = $cmdbMenuColumnTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // 紐付対象メニューカラム管理テーブルの廃止を行う
        //////////////////////////
        foreach($cmdbMenuColumnArray as $cmdbMenuColumn){

            // メニューIDが異なる場合はスキップ
            if($cmdbMenuColumn['MENU_ID'] != $hostMenuId){
                continue;
            }

            // 廃止の場合、スキップ
            if($cmdbMenuColumn['DISUSE_FLAG'] == "1"){
                continue;
            }

            $matchFlg = false;

            foreach($columnInfoArray as $columnInfo){

                // カラム名が一致するデータがあった場合
                if($cmdbMenuColumn['COL_NAME'] === $columnInfo['COL_NAME']){
                    $matchFlg = true;
                    break;
                }
            }

            // 一致するデータがあった場合はスキップ
            if(true === $matchFlg){
                continue;
            }

            // 廃止する
            $updateData = $cmdbMenuColumn;
            $updateData['DISUSE_FLAG']          = "1";                                  // 廃止フラグ
            $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM;                 // 最終更新者

            //////////////////////////
            // 紐付対象メニューカラム管理テーブルを更新
            //////////////////////////
            $result = $cmdbMenuColumnTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                outputLog($msg);
                throw new Exception($msg);
            }
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * ホストグループ分割対象更新
 */
function updateDivideTarget($hgMenuId, $hostMenuId){
    global $objDBCA, $db_model_ch, $objMTS;
    $splitTargetTable = new SplitTargetTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // ホストグループ分割対象テーブルを検索
        //////////////////////////
        $sql = $splitTargetTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $splitTargetTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $splitTargetArray = $result;

        foreach($splitTargetArray as $splitTarget){
            // メニューIDが一致した場合
            if($splitTarget['INPUT_MENU_ID'] === $hgMenuId || $splitTarget['OUTPUT_MENU_ID'] == $hostMenuId){

                // 廃止する
                $updateData = $splitTarget;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // ホストグループ分割対象テーブルを更新
                //////////////////////////
                $result = $splitTargetTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録する
        $insertData = array();
        $insertData['INPUT_MENU_ID']    = $hgMenuId;            // 分割対象メニュー
        $insertData['OUTPUT_MENU_ID']   = $hostMenuId;          // 分割データ登録メニュー
        $insertData['DIVIDED_FLG']      = "0";                  // 分割済みフラグ
        $insertData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
        $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

        //////////////////////////
        // ホストグループ分割対象テーブルに登録
        //////////////////////////
        $result = $splitTargetTable->insertTable($insertData, $seqNo, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * パラメータシート縦横変換管理更新
 */
function updateColToRowMng($cpiData, $menuId, $toMenuId, $purpose, $startColName){
    global $objDBCA, $db_model_ch, $objMTS;
    $matchFlg = false;
    $colToRowMngTable = new ColToRowMngTable($objDBCA, $db_model_ch);

    try{
        //////////////////////////
        // パラメータシート縦横変換管理テーブルを検索
        //////////////////////////
        $sql = $colToRowMngTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $colToRowMngTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }
        $colToRowMngArray = $result;

        foreach($colToRowMngArray as $colToRowMng){
            // メニューIDが一致するデータがあった場合
            if($colToRowMng['FROM_MENU_ID'] == $menuId || $colToRowMng['TO_MENU_ID'] == $toMenuId){

                // 廃止する
                $updateData = $colToRowMng;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // パラメータシート縦横変換管理テーブルを更新
                //////////////////////////
                $result = $colToRowMngTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }
        // 登録する
        $insertData = array();
        $insertData['FROM_MENU_ID']     = $menuId;                  // 変換元メニュー
        $insertData['TO_MENU_ID']       = $toMenuId;                // 変換先メニュー
        $insertData['PURPOSE']          = $purpose;                 // 用途
        $insertData['START_COL_NAME']   = $startColName;            // 開始カラム名
        $insertData['COL_CNT']          = $cpiData['COL_CNT'];      // 項目数
        $insertData['REPEAT_CNT']       = $cpiData['REPEAT_CNT'];   // 繰り返し数
        $insertData['CHANGED_FLG']      = "0";                      // 縦横変換済みフラグ
        $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
        $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

        //////////////////////////
        // パラメータシート縦横変換管理テーブルに登録
        //////////////////////////
        $result = $colToRowMngTable->insertTable($insertData, $seqNo, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 編集時のカラム追加/削除用SQLを作成
 */
function createAlterColumnSql($tableType, $menuTableName, $columnNameListArray){
    global $objDBCA, $db_model_ch, $objMTS;

    $alterColumnSql = "";

    try{
        //更新対象のテーブル名
        $targetTable = "";
        $targetTableJnl = "";
        $afterColumn = "";
        switch($tableType){
            case 'cmdb':
                $targetTable = "F_" . $menuTableName . "_H";
                $targetTableJnl = "F_" . $menuTableName . "_H_JNL";
                $afterColumn = "ROW_ID";
                break;
            case 'host':
                $targetTable = "F_" . $menuTableName . "_H";
                $targetTableJnl = "F_" . $menuTableName . "_H_JNL";
                $afterColumn = "OPERATION_ID";
                break;
            case 'hg':
                $targetTable = "F_" . $menuTableName . "_HG";
                $targetTableJnl = "F_" . $menuTableName . "_HG_JNL";
                $afterColumn = "OPERATION_ID";
                break;
            case 'conv':
                $targetTable = "F_" . $menuTableName . "_CONV";
                $targetTableJnl = "F_" . $menuTableName . "_CONV_JNL";
                $afterColumn = "INPUT_ORDER";
                break;
            case 'conv_h':
                $targetTable = "F_" . $menuTableName . "_CONV_H";
                $targetTableJnl = "F_" . $menuTableName . "_CONV_H_JNL";
                $afterColumn = "INPUT_ORDER";
                break;
        }

        //$columnNameListArrayからカラム名だけを抽出
        $newColumnListArray = array();
        foreach($columnNameListArray as $target){
            $newColumnListArray[] = $target['COLUMN_NAME'];
        }

        //作成済みのテーブルからカラムの一覧を取得
        $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);
        $sql = "DESC " . $targetTable;
        $result = $baseTable->execQuery($sql, NULL, $objQuery);
        if(true !== $result){
            outputLog("SQL=$sql");
            $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
            outputLog($msg);
            throw new Exception($msg);
        }

        $fieldListArray = array();
        while ($row = $objQuery->resultFetch()){
            $fieldListArray[] = $row['Field'];
        }

        //カラム名"KY_AUTO_COL_XXXX"のものだけ抜出す
        $existColumnArray = array();
        foreach($fieldListArray as $column){
            if(strpos($column, COLUMN_PREFIX) !== false){
                $existColumnArray[] = $column;
            }
        }

        //作成済みのテーブルにしかないカラム名を抽出(カラム削除の対象)
        $existOnlyColumnArray = array_diff($existColumnArray, $newColumnListArray);

        //編集後のテーブルにしかないカラム名を抽出(カラム追加の対象)
        $newOnlyColumnArray = array_diff($newColumnListArray, $existColumnArray);

        //カラムを追加するSQLを作成    
        foreach($newOnlyColumnArray as $columnName){
            $inputMethodId = $columnNameListArray[$columnName]['INPUT_METHOD_ID'];
            switch($inputMethodId){
                case 1: //文字列(単一行)
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " TEXT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " TEXT;\n";
                    break;
                case 2: //文字列(複数行)
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " TEXT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " TEXT;\n";
                    break;
                case 3: //整数
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " INT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " INT;\n";
                    break;
                case 4: //小数
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " DOUBLE;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " DOUBLE;\n";
                    break;
                case 5: //日時
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " DATETIME(6);\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " DATETIME(6);\n";
                    break;
                case 6: //日付
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " DATETIME(6);\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " DATETIME(6);\n";
                    break;
                case 7: //プルダウン
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " INT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " INT;\n";
                    break;
                case 8: //文字列(PW)
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " TEXT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " TEXT;\n";
                    break;
                case 9: //ファイルアップロード
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " TEXT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " TEXT;\n";
                    break;
                case 10://リンク
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " TEXT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " TEXT;\n";
                    break;
                case 11://パラメータシート参照
                    $alterAddSql    = "ALTER TABLE " . $targetTable    . " ADD " . $columnName . " INT;\n";
                    $alterAddSqlJnl = "ALTER TABLE " . $targetTableJnl . " ADD " . $columnName . " INT;\n";
                    break;
            }
            $alterColumnSql = $alterColumnSql . $alterAddSql;
            $alterColumnSql = $alterColumnSql . $alterAddSqlJnl;
        }

        //カラムを削除するSQLを作成
        foreach($existOnlyColumnArray as $columnName){
            $alterDropSql    = "ALTER TABLE " . $targetTable . " DROP " . $columnName . ";\n";
            $alterDropSqlJnl = "ALTER TABLE " . $targetTableJnl . " DROP " . $columnName . ";\n";
            $alterColumnSql = $alterColumnSql . $alterDropSql;
            $alterColumnSql = $alterColumnSql . $alterDropSqlJnl; 
        }

        //$columnNameListArrayの順番になるようテーブルのカラム順を整列
        foreach($columnNameListArray as $columnName => $target){
            $inputMethodId = $target['INPUT_METHOD_ID'];

            switch($inputMethodId){
                case 1: //文字列(単一行)
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " TEXT AFTER ". $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " TEXT AFTER ". $afterColumn . ";\n";
                    break;
                case 2: //文字列(複数行)
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    break;
                case 3: //整数
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    break;
                case 4: //小数
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " DOUBLE AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " DOUBLE AFTER " . $afterColumn . ";\n";
                    break;
                case 5: //日時
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " DATETIME(6) AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " DATETIME(6) AFTER " . $afterColumn . ";\n";
                    break;
                case 6: //日付
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " DATETIME(6) AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " DATETIME(6) AFTER " . $afterColumn . ";\n";
                    break;
                case 7: //プルダウン
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    break;
                case 8: //文字列(PW)
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    break;
                case 9: //ファイルアップロード
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    break;
                case 10://リンク
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " TEXT AFTER " . $afterColumn . ";\n";
                    break;
                case 11://パラメータシート参照
                    $alterModifySql    = "ALTER TABLE " . $targetTable    . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    $alterModifySqlJnl = "ALTER TABLE " . $targetTableJnl . " MODIFY " . $columnName . " INT AFTER " . $afterColumn . ";\n";
                    break;
            }
            $alterColumnSql = $alterColumnSql . $alterModifySql;
            $alterColumnSql = $alterColumnSql . $alterModifySqlJnl;

            //現在のColumnNameを次の$afterColumnに設定
            $afterColumn = $columnName;
        }
        
        //ACCESS_AUTHカラムが存在しない場合は追加処理を追記
        if(!(in_array("ACCESS_AUTH", $fieldListArray))){
            $alterColumnSql = $alterColumnSql . "ALTER TABLE " . $targetTable      . " ADD ACCESS_AUTH TEXT AFTER " . $afterColumn . ";\n";
            $alterColumnSql = $alterColumnSql . "ALTER TABLE " . $targetTableJnl   . " ADD ACCESS_AUTH TEXT AFTER " . $afterColumn . ";\n";
        }

        return $alterColumnSql;

    }catch(Exception $e){
        return false;
    }

}


/*
 * 項目名から[x]部分を抽出する
 */
function extractRepeatItemNo($itemName){
    $cutItemName = $itemName;
    $extractRepeatNoStr = "";
    $extractEndFlg = false;
    while(!$extractEndFlg && $cutItemName != ""){
        $checkWord = mb_substr($cutItemName, -1);
        if($checkWord == "]"){
            $extractRepeatNoStr = $checkWord . $extractRepeatNoStr;
        }

        if(is_numeric($checkWord)){
            $extractRepeatNoStr = $checkWord . $extractRepeatNoStr;
        }

        if($checkWord == "["){
            $extractRepeatNoStr = $checkWord . $extractRepeatNoStr;
            $extractEndFlg = true;
        }

        $cutItemName = mb_substr($cutItemName, 0, -1);

    }

    return $extractRepeatNoStr;

}

/**
 * 処理済みフラグをクリアする
 *
 * @param    なし
 * @return   なし
 */
function insertERTask(){
    global $objDBCA, $objMTS;

    $sql = "UPDATE A_PROC_LOADED_LIST
            SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE PROC_NAME = 'ky_create_er-workflow'";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime()));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    return true;
}
