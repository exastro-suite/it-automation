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

try{

    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // 未実行のレコードがない場合は処理を終了する
    //////////////////////////
    $createMenuStatusArray = getUnexecutedRecord();
    if(count($createMenuStatusArray) === 0){
        if(LOG_LEVEL === 'DEBUG'){
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10004'));
            outputLog($objMTS->getSomeMessage('ITACREPAR-STD-10002', basename( __FILE__, '.php' )));
        }
        exit;
    }

    //////////////////////////
    // テンプレートファイル読み込み
    //////////////////////////
    $templatePathArray =array(TEMPLATE_PATH . FILE_HG_LOADTABLE,
                              TEMPLATE_PATH . FILE_HG_LOADTABLE_VAL,
                              TEMPLATE_PATH . FILE_H_LOADTABLE,
                              TEMPLATE_PATH . FILE_H_LOADTABLE_VAL,
                              TEMPLATE_PATH . FILE_VIEW_LOADTABLE,
                              TEMPLATE_PATH . FILE_VIEW_LOADTABLE_VAL,
                              TEMPLATE_PATH . FILE_HG_SQL,
                              TEMPLATE_PATH . FILE_H_SQL,
                              TEMPLATE_PATH . FILE_HG_LOADTABLE_ID,
                              TEMPLATE_PATH . FILE_H_LOADTABLE_ID,
                              TEMPLATE_PATH . FILE_VIEW_LOADTABLE_ID,
                              TEMPLATE_PATH . FILE_CONVERT_LOADTABLE,
                              TEMPLATE_PATH . FILE_CONVERT_LOADTABLE_VAL,
                              TEMPLATE_PATH . FILE_CONVERT_LOADTABLE_ID,
                              TEMPLATE_PATH . FILE_CONVERT_SQL,
                              TEMPLATE_PATH . FILE_CONVERT_H_LOADTABLE,
                              TEMPLATE_PATH . FILE_CONVERT_H_SQL,
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
    $hgLoadTableValTmpl         = $templateArray[1];
    $hostLoadTableTmpl          = $templateArray[2];
    $hostLoadTableValTmpl       = $templateArray[3];
    $viewLoadTableTmpl          = $templateArray[4];
    $viewLoadTableValTmpl       = $templateArray[5];
    $hgSqlTmpl                  = $templateArray[6];
    $hostSqlTmpl                = $templateArray[7];
    $hgLoadTableIdTmpl          = $templateArray[8];
    $hostLoadTableIdTmpl        = $templateArray[9];
    $viewLoadTableIdTmpl        = $templateArray[10];
    $convLoadTableTmpl          = $templateArray[11];
    $convLoadTableValTmpl       = $templateArray[12];
    $convLoadTableIdTmpl        = $templateArray[13];
    $convSqlTmpl                = $templateArray[14];
    $convHostLoadTableTmpl      = $templateArray[15];
    $convHostSqlTmpl            = $templateArray[16];

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
        foreach($createItemInfoArray as $ciiData){
            if($targetData['CREATE_MENU_ID'] === $ciiData['CREATE_MENU_ID']){
                $itemInfoArray[] = $ciiData;
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
        if("" != $cmiData['MENUGROUP_FOR_CONV']){

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
            $repeatItemCnt = $cpiData['COL_CNT'] * $cpiData['REPEAT_CNT'];

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
            if(count($repeatItemArray) != $cpiData['COL_CNT'] * $cpiData['REPEAT_CNT']){
                $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5015');
                outputLog($msg);
                // パラメータシート作成管理更新処理を行う
                updateMenuStatus($targetData, "4", $msg, false, true);
                continue;
            }

            // 型、サイズのチェック
            $inputMethodIdArray = array();
            $maxLengthArray = array();
            $otherMenuLinkIdArray = array();
            $errFlg = false;

            for($i = 0; $i < $cpiData['REPEAT_CNT']; $i ++){
                for($j = 0; $j < $cpiData['COL_CNT']; $j ++){

                    if($i === 0){
                        $inputMethodIdArray[] = $repeatItemArray[$j]['INPUT_METHOD_ID'];
                        $maxLengthArray[] = $repeatItemArray[$j]['MAX_LENGTH'];
                        $otherMenuLinkIdArray[] = $repeatItemArray[$j]['OTHER_MENU_LINK_ID'];
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
                    // 最大バイト数チェック
                    if(1 == $inputMethodIdArray[$j] && $maxLengthArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['MAX_LENGTH']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5017');
                        outputLog($msg);
                        // パラメータシート作成管理更新処理を行う
                        updateMenuStatus($targetData, "4", $msg, false, true);
                        $errFlg = true;
                        break;
                    }
                    // 他メニュー参照チェック
                    if(2 == $inputMethodIdArray[$j] && $otherMenuLinkIdArray[$j] != $repeatItemArray[$i * $cpiData['COL_CNT'] + $j]['OTHER_MENU_LINK_ID']){
                        $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5018');
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
            for($i = 0; $i < $cpiData['COL_CNT']; $i ++){
                $convertItemInfoArray[] = $repeatItemArray[$i];
            }
            $convertItemInfoArray = array_merge($convertItemInfoArray, $afterItemArray);
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
        $hgLoadTableVal = "";
        $hostLoadTableVal = "";
        $viewLoadTableVal = "";
        $errFlg = false;
        $itemColumnGrpArrayArray = array();

        // 項目の件数分ループ
        foreach ($itemInfoArray as &$itemInfo){
            // カラム名を決定する
            $itemInfo['COLUMN_NAME'] = COLUMN_PREFIX . sprintf("%04d", $itemInfo['CREATE_ITEM_ID']);

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

            // 文字列の場合
            if(1 == $itemInfo['INPUT_METHOD_ID']){
                $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    VARCHAR(" . $itemInfo['MAX_LENGTH'] . "),\n";
            }
            // 他メニュー参照の場合
            else{
                $columnTypes = $columnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
            }
            $columns = $columns . "       TAB_A." . $itemInfo['COLUMN_NAME'] . ",\n";

            // 「'」がある場合は「\'」に変換する
            $description    = str_replace("'", "\'", $itemInfo['DESCRIPTION']);
            $itemName       = str_replace("'", "\'", $itemInfo['ITEM_NAME']);

            if("2" == $cmiData['PURPOSE']){
                // ホストグループ用loadTableのカラム埋め込み部分を作成する
                // 文字列の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    $work = $hgLoadTableValTmpl;
                }
                // 他メニュー参照の場合
                else{
                    $work = $hgLoadTableIdTmpl;
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
                $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
                $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
                $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);
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
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                }
                $hgLoadTableVal .= $work . "\n";
            }

            // ホスト用loadTableのカラム埋め込み部分を作成する
            // 文字列の場合
            if(1 == $itemInfo['INPUT_METHOD_ID']){
                $work = $hostLoadTableValTmpl;
            }
            // 他メニュー参照の場合
            else{
                $work = $hostLoadTableIdTmpl;
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
            $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
            $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
            $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);
            if(1 == $itemInfo['REQUIRED']){
                $work = str_replace(REPLACE_REQUIRED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setRequired(true);", $work);
            }
            else{
                $work = str_replace(REPLACE_REQUIRED, "", $work);
            }

            if(1 == $itemInfo['UNIQUED'] && "1" == $cmiData['PURPOSE']){
                $work = str_replace(REPLACE_UNIQUED, "\$c" . $itemInfo['CREATE_ITEM_ID'] . "->setUnique(true);", $work);
            }
            else{
                $work = str_replace(REPLACE_UNIQUED, "", $work);
            }
            // 他メニュー参照の場合
            if(2 == $itemInfo['INPUT_METHOD_ID']){
                $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
            }
            $hostLoadTableVal .= $work . "\n";

            // 最新値参照用loadTableのカラム埋め込み部分を作成する
            // 文字列の場合
            if(1 == $itemInfo['INPUT_METHOD_ID']){
                $work = $viewLoadTableValTmpl;
            }
            // 他メニュー参照の場合
            else{
                $work = $viewLoadTableIdTmpl;
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
            $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
            $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
            $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);
            // 他メニュー参照の場合
            if(2 == $itemInfo['INPUT_METHOD_ID']){
                $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
            }
            $viewLoadTableVal .= $work . "\n";
        }
        unset($itemInfo);
        if(true === $errFlg){
            continue;
        }

        // カラムグループ部品組み立て
        $columnGrpParts = makeColumnGrpParts($itemColumnGrpArrayArray);

        // パラメータシート(縦)を作成する設定の場合
        if(true === $createConvFlg){

            //////////////////////////
            // テンプレートの埋め込み部分を設定する
            //////////////////////////
            $convColumnTypes = "";
            $convColumns = "";
            $convertLoadTableVal = "";
            $convertViewLoadTableVal = "";
            $errFlg = false;
            $convItemColumnGrpArrayArray = array();

            // 項目の件数分ループ
            foreach ($convertItemInfoArray as &$itemInfo){
                // カラム名を決定する
                $itemInfo['COLUMN_NAME'] = COLUMN_PREFIX . sprintf("%04d", $itemInfo['CREATE_ITEM_ID']);

                if($cpiData['CREATE_ITEM_ID'] == $itemInfo['CREATE_ITEM_ID']){
                    $startColName = $itemInfo['COLUMN_NAME'];
                }

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

                // 文字列の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    VARCHAR(" . $itemInfo['MAX_LENGTH'] . "),\n";
                }
                // 他メニュー参照の場合
                else{
                    $convColumnTypes = $convColumnTypes . $itemInfo['COLUMN_NAME'] . "    INT,\n";
                }
                $convColumns = $convColumns . "       TAB_A." . $itemInfo['COLUMN_NAME'] . ",\n";

                // 「'」がある場合は「\'」に変換する
                $description    = str_replace("'", "\'", $itemInfo['DESCRIPTION']);
                $itemName       = str_replace("'", "\'", $itemInfo['ITEM_NAME']);

                // loadTableのカラム埋め込み部分を作成する
                // 文字列の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    $work = $convLoadTableValTmpl;
                }
                // 他メニュー参照の場合
                else{
                    $work = $convLoadTableIdTmpl;
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
                $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
                $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
                $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);
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
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                }
                $convertLoadTableVal .= $work . "\n";

                // 最新値参照用loadTableのカラム埋め込み部分を作成する
                // 文字列の場合
                if(1 == $itemInfo['INPUT_METHOD_ID']){
                    $work = $viewLoadTableValTmpl;
                }
                // 他メニュー参照の場合
                else{
                    $work = $viewLoadTableIdTmpl;
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
                $work = str_replace(REPLACE_VALUE,  $itemInfo['COLUMN_NAME'],   $work);
                $work = str_replace(REPLACE_DISP,   $itemName,                  $work);
                $work = str_replace(REPLACE_SIZE,   $itemInfo['MAX_LENGTH'],    $work);

                // 他メニュー参照の場合
                if(2 == $itemInfo['INPUT_METHOD_ID']){
                    $matchIdx = array_search($itemInfo['OTHER_MENU_LINK_ID'], array_column($otherMenuLinkArray, 'LINK_ID'));
                    $otherMenuLink = $otherMenuLinkArray[$matchIdx];
                    $work = str_replace(REPLACE_ID_TABLE,   $otherMenuLink['TABLE_NAME'],   $work);
                    $work = str_replace(REPLACE_ID_PRI,     $otherMenuLink['PRI_NAME'],     $work);
                    $work = str_replace(REPLACE_ID_COL,     $otherMenuLink['COLUMN_NAME'],  $work);
                }
                $convertViewLoadTableVal .= $work . "\n";
            }
            unset($itemInfo);
            if(true === $errFlg){
                continue;
            }

            // カラムグループ部品組み立て
            $convColumnGrpParts = makeColumnGrpParts($convItemColumnGrpArrayArray);

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

        // 「'」がある場合は「\'」に変換する。説明の改行コードは<BR/>に変換する。
        $description    = str_replace("'", "\'", $cmiData['DESCRIPTION']);
        $description    = str_replace("\n", "<BR/>", $description);
        $menuName       = str_replace("'", "\'", $cmiData['MENU_NAME']);

        if("2" == $cmiData['PURPOSE']){
            // ホストグループ用の00_loadTable.php
            $work = $hgLoadTableTmpl;
            $work = str_replace(REPLACE_INFO,   $description,       $work);
            $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
            $work = str_replace(REPLACE_MENU,   $menuName,          $work);
            $hgLoadTableVal .= $columnGrpParts;
            $work = str_replace(REPLACE_ITEM,   $hgLoadTableVal, $work);
            $hgLoadTable = $work;
        }

        // ホスト用の00_loadTable.php
        $work = $hostLoadTableTmpl;
        $work = str_replace(REPLACE_INFO,   $description,       $work);
        $work = str_replace(REPLACE_TABLE,  $menuTableName,     $work);
        $work = str_replace(REPLACE_MENU,   $menuName,          $work);
        $hostLoadTableVal .= $columnGrpParts;
        $work = str_replace(REPLACE_ITEM,   $hostLoadTableVal, $work);
        $hostLoadTable = $work;

        // パラメータシート(縦)を作成する設定の場合
        if(true === $createConvFlg){

            // 縦メニュー用の00_loadTable.php
            $convertLoadTableVal .= $convColumnGrpParts;
            if("2" == $cmiData['PURPOSE']){
                $work = $convLoadTableTmpl;
                $work = str_replace(REPLACE_INFO,       $description,           $work);
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                $convertLoadTable = $work;
                $work = $convHostLoadTableTmpl;
                $work = str_replace(REPLACE_INFO,       $description,           $work);
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                $convertHostLoadTable = $work;
            }
            else{
                $work = $convHostLoadTableTmpl;
                $work = str_replace(REPLACE_INFO,       $description,           $work);
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_MENU,       $menuName,              $work);
                $work = str_replace(REPLACE_ITEM,       $convertLoadTableVal,   $work);
                $convertLoadTable = $work;
            }
        }

        // 最新値参照用の00_loadTable.php
        $work = $viewLoadTableTmpl;
        $work = str_replace(REPLACE_INFO,   $description,       $work);
        $work = str_replace(REPLACE_MENU,   $menuName,          $work);
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
        $work = $hgSqlTmpl;
        $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
        $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
        $work = str_replace(REPLACE_COL,        $columns,       $work);
        $hgSql = $work;

        // ホスト用のSQL
        $work = $hostSqlTmpl;
        $work = str_replace(REPLACE_TABLE,      $menuTableName, $work);
        $work = str_replace(REPLACE_COL_TYPE,   $columnTypes,   $work);
        $work = str_replace(REPLACE_COL,        $columns,       $work);
        $hostSql = $work;

        // パラメータシート(縦)を作成する設定の場合
        if(true === $createConvFlg){

            // 縦メニュー用のSQL
            if("2" == $cmiData['PURPOSE']){
                $work = $convSqlTmpl;
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                $convertSql = $work;
                $work = $convHostSqlTmpl;
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                $convertSql .= $work;
            }
            else{
                $work = $convHostSqlTmpl;
                $work = str_replace(REPLACE_TABLE,      $menuTableName,         $work);
                $work = str_replace(REPLACE_COL_TYPE,   $convColumnTypes,       $work);
                $work = str_replace(REPLACE_COL,        $convColumns,           $work);
                $convertSql = $work;
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

        //////////////////////////
        // 外部CMDB作成用SQL実行
        //////////////////////////
        $baseTable = new BaseTable_CPM($objDBCA, $db_model_ch);
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
        $result = updateMenuList($cmiData, $hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId, $createConvFlg);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // ロール・メニュー紐付管理更新
        //////////////////////////
        $result = updateRoleMenuLinkList($hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // メニュー・テーブル紐付更新
        //////////////////////////
        $result = updateMenuTableLink($menuTableName, $hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // 他メニュー連携テーブル更新
        //////////////////////////
        $result = updateOtherMenuLink($menuTableName, $itemInfoArray, $itemColumnGrpArrayArray, $hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // 紐付対象メニュー更新
        //////////////////////////
        $result = updateLinkTargetMenu($hostMenuId);

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // 紐付対象メニューテーブル管理更新
        //////////////////////////
        $result = updateLinkTargetTable($hostMenuId, "F_" . $menuTableName . "_H");

        if(true !== $result){
            // パラメータシート作成管理更新処理を行う
            updateMenuStatus($targetData, "4", $result, true, true);
            continue;
        }

        //////////////////////////
        // 紐付対象メニューカラム管理更新
        //////////////////////////
        $result = updateLinkTargetColumn($hostMenuId, $itemInfoArray, $itemColumnGrpArrayArray);

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

        //////////////////////////
        // loadTableを配置する
        //////////////////////////
        if("2" == $cmiData['PURPOSE']){
            // ホストグループ用
            $hgLoadTablePath = $menuTmpDir . sprintf("%010d", $hgMenuId) . "_loadTable.php";
            $result = deployLoadTable($hgLoadTable,
                                      $hgLoadTablePath,
                                      sprintf("%010d", $hgMenuId),
                                      $targetData
                                     );
            if(true !== $result){
                continue;
            }
        }

        // ホスト用
        $hostLoadTablePath = $menuTmpDir . sprintf("%010d", $hostMenuId) . "_loadTable.php";
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
        // パラメータシート作成管理更新処理を行う（完了）
        //////////////////////////
        updateMenuStatus($targetData, "3", NULL, false, false, $zipFileName, $zipFilePath);
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

    if(LOG_LEVEL === 'DEBUG'){
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
    if(LOG_LEVEL === 'DEBUG'){
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
 * カラムグループ部品組み立て
 * 
 */
function makeColumnGrpParts($itemColumnGrpArrayArray){

    $columnGrpParts = "";
    $beforeKey = null;
    $beforeColumnGrpArray = array();
    $numsetColumnGrpArrayArray = array();
    $columnGrpNum = 1;

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
                $columnGrpParts .= "    \$cg->addColumn(\$c{$beforeKey});\n";
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
                        $columnGrpParts .= "    \$cg->addColumn(\$cg" . $beforeColumnGrpArray[$loopCnt2]['ID'] . ");\n";
                    }
                }
            }
        }

        // ループの最後の場合
        if($loopCnt === count($numsetColumnGrpArrayArray)){

            if(0 === count($numsetColumnGrpArray)){
                // カラムグループの設定が無い場合、カラムを根本に紐付ける
                $columnGrpParts .= "    \$cg->addColumn(\$c{$key});\n";
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
                    $columnGrpParts .= "    \$cg->addColumn(\$cg" . $numsetColumnGrpArray[$loopCnt3]['ID'] . ");\n";
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
        $destFile = ROOT_DIR_PATH . "/webconfs/users/{$menuId}_loadTable.php";
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
function updateMenuTableLink($menuTableName, $hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId){
    global $objDBCA, $db_model_ch, $objMTS;
    $menuTableLinkTable = new MenuTableLinkTable($objDBCA, $db_model_ch);

    try{
        $menuInfoArray = array(array($hgMenuId,     "F_" . $menuTableName . "_HG",      "F_" . $menuTableName . "_HG_JNL"),
                               array($hostMenuId,   "F_" . $menuTableName . "_H",       "F_" . $menuTableName . "_H_JNL"),
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
                $insertData['MENU_ID']          = $menuInfo[0];         // メニュー名
                $insertData['TABLE_NAME']       = $menuInfo[1];         // テーブル名
                $insertData['KEY_COL_NAME']     = "ROW_ID";             // 主キー
                $insertData['TABLE_NAME_JNL']   = $menuInfo[2];         // テーブル名(履歴)
                $insertData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

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
function updateOtherMenuLink($menuTableName, $itemInfoArray, $itemColumnGrpArrayArray, $hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId){
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

        foreach($otherMenuLinkArray as $omlData){
            // メニューIDが一致した場合、廃止
            if($omlData['MENU_ID'] == $hgMenuId ||
               $omlData['MENU_ID'] == $hostMenuId ||
               $omlData['MENU_ID'] == $viewMenuId ||
               $omlData['MENU_ID'] == $convMenuId ||
               $omlData['MENU_ID'] == $convHostMenuId){

                // 廃止する
                $updateData = $omlData;
                $updateData['DISUSE_FLAG']      = "1";                  // 廃止フラグ
                $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                //////////////////////////
                // 他メニュー連携テーブルを更新
                //////////////////////////
                $result = $otherMenuLinkTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    outputLog($msg);
                    throw new Exception($msg);
                }
            }
        }

        // 登録するメニューID、テーブル名を決定する
        if("" != $hgMenuId){
            $insertMenuId = $hgMenuId;
            $insertTableName = "F_" . $menuTableName . "_HG";
        }
        else{
            $insertMenuId = $hostMenuId;
            $insertTableName = "F_" . $menuTableName . "_H";
        }

        // 登録する
        foreach($itemInfoArray as $itemInfo){

            // 必須かつ一意の場合
            if(1 == $itemInfo['REQUIRED'] && 1 == $itemInfo['UNIQUED']){

                // 項目名を決定する
                if(0 < count($itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']])){
                    $columnDispName = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") .
                                      "/" .
                                      implode("/", $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']]) .
                                      "/" .
                                      $itemInfo['ITEM_NAME'];
                }
                else{
                    $columnDispName = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") .
                                      "/" .
                                      $itemInfo['ITEM_NAME'];
                }

                $insertData = array();
                $insertData['MENU_ID']          = $insertMenuId;            // メニュー
                $insertData['COLUMN_DISP_NAME'] = $columnDispName;          // 項目名
                $insertData['TABLE_NAME']       = $insertTableName;         // テーブル名
                $insertData['PRI_NAME']         = "ROW_ID";                 // 主キー
                $insertData['COLUMN_NAME']      = $itemInfo['COLUMN_NAME']; // カラム名
                $insertData['DISUSE_FLAG']      = "0";                      // 廃止フラグ
                $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM;     // 最終更新者

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

        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}


/*
 * メニュー管理更新
 */
function updateMenuList($cmiData, &$hgMenuId, &$hostMenuId, &$viewMenuId, &$convMenuId, &$convHostMenuId, $createConvFlg){
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

        $hgMatchFlg = false;
        $hostMatchFlg = false;
        $viewMatchFlg = false;
        $convMatchFlg = false;
        $convHostMatchFlg = false;
        $hgMenuList = NULL;
        $hostMenuList = NULL;
        $viewMenuList = NULL;
        $convMenuList = NULL;
        $convHostMenuList = NULL;

        foreach($menuListArray as $menu){
            // メニューグループとメニューが一致するデータを検索
            if($cmiData['MENUGROUP_FOR_HG'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $hgMatchFlg = true;
                $hgMenuList = $menu;
            }
            else if($cmiData['MENUGROUP_FOR_H'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $hostMatchFlg = true;
                $hostMenuList = $menu;
            }
            else if($cmiData['MENUGROUP_FOR_VIEW'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $viewMatchFlg = true;
                $viewMenuList = $menu;
            }
            else if($cmiData['MENUGROUP_FOR_CONV'] === $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $convMatchFlg = true;
                $convMenuList = $menu;
            }
            else if(MENU_GROUP_ID_CONV_HOST == $menu['MENU_GROUP_ID'] && $cmiData['MENU_NAME'] === $menu['MENU_NAME']){
                $convHostMatchFlg = true;
                $convHostMenuList = $menu;
            }
        }

        $targetArray = array();
        if(true === $createConvFlg){
            $targetArray[] = array('MATCH_FLG' => $convMatchFlg, 'DATA' => $convMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_CONV']);
        }
        if("2" == $cmiData['PURPOSE']){
            $targetArray[] = array('MATCH_FLG' => $hgMatchFlg, 'DATA' => $hgMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_HG']);
        }
        $targetArray[] = array('MATCH_FLG' => $hostMatchFlg, 'DATA' => $hostMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_H']);
        $targetArray[] = array('MATCH_FLG' => $viewMatchFlg, 'DATA' => $viewMenuList, 'MENU_GROUP' => $cmiData['MENUGROUP_FOR_VIEW']);

        if(true === $createConvFlg && "2" == $cmiData['PURPOSE']){
            $targetArray[] = array('MATCH_FLG' => $convHostMatchFlg, 'DATA' => $convHostMenuList, 'MENU_GROUP' => MENU_GROUP_ID_CONV_HOST);
        }

        foreach($targetArray as &$target){

            // メニューグループとメニューが一致するデータがあった場合
            if(true === $target['MATCH_FLG']){

                $target['MENU_ID'] = $target['DATA']['MENU_ID'];

                // 更新する
                $updateData = $target['DATA'];
                $updateData['LOGIN_NECESSITY']      = 1;                    // 認証要否
                $updateData['SERVICE_STATUS']       = 0;                    // サービス状態
                $updateData['DISP_SEQ']             = $cmiData['DISP_SEQ']; // メニューグループ内表示順序
                $updateData['AUTOFILTER_FLG']       = 1;                    // オートフィルタチェック
                $updateData['INITIAL_FILTER_FLG']   = 2;                    // 初回フィルタ
                $updateData['WEB_PRINT_LIMIT']      = NULL;                 // Web表示最大行数
                $updateData['WEB_PRINT_CONFIRM']    = NULL;                 // Web表示前確認行数
                $updateData['XLS_PRINT_LIMIT']      = NULL;                 // Excel出力最大行数
                $updateData['NOTE']                 = NULL;                 // 備考
                $updateData['LAST_UPDATE_USER']     = USER_ID_CREATE_PARAM; // 最終更新者

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

        if(true === $createConvFlg){
            if("2" == $cmiData['PURPOSE']){
                $convMenuId = $targetArray[0]['MENU_ID'];
                $hgMenuId   = $targetArray[1]['MENU_ID'];
                $hostMenuId = $targetArray[2]['MENU_ID'];
                $viewMenuId = $targetArray[3]['MENU_ID'];
                $convHostMenuId = $targetArray[4]['MENU_ID'];
            }
            else{
                $convMenuId = $targetArray[0]['MENU_ID'];
                $hgMenuId   = NULL;
                $hostMenuId = $targetArray[1]['MENU_ID'];
                $viewMenuId = $targetArray[2]['MENU_ID'];
                $convHostMenuId = NULL;
            }
        }
        else{
            if("2" == $cmiData['PURPOSE']){
                $convMenuId = NULL;
                $hgMenuId   = $targetArray[0]['MENU_ID'];
                $hostMenuId = $targetArray[1]['MENU_ID'];
                $viewMenuId = $targetArray[2]['MENU_ID'];
                $convHostMenuId = NULL;
            }
            else{
                $convMenuId = NULL;
                $hgMenuId   = NULL;
                $hostMenuId = $targetArray[0]['MENU_ID'];
                $viewMenuId = $targetArray[1]['MENU_ID'];
                $convHostMenuId = NULL;
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
function updateRoleMenuLinkList($hgMenuId, $hostMenuId, $viewMenuId, $convMenuId, $convHostMenuId){
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
               $roleMenuLink['MENU_ID'] == $viewMenuId ||
               $roleMenuLink['MENU_ID'] == $convMenuId ||
               $roleMenuLink['MENU_ID'] == $convHostMenuId){

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

        if(NULL !== $convMenuId && NULL !== $hgMenuId){
            $menuArray = array(array($convMenuId,       1,  '0'),
                               array($hgMenuId,         2,  '0'),
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
                               array($viewMenuId,   2,  '0'),
                              );
        }

        foreach($menuArray as $menu){

            // 登録する
            $insertData = array();
            $insertData['ROLE_ID']          = 1;                    // ロール
            $insertData['MENU_ID']          = $menu[0];             // メニュー
            $insertData['PRIVILEGE']        = $menu[1];             // 紐付
            $insertData['DISUSE_FLAG']      = $menu[2];             // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

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
        return true;
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}

/*
 * 紐付対象メニュー更新
 */
function updateLinkTargetMenu($hostMenuId){
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
            if($cmdbMenuList['MENU_ID'] == $hostMenuId){
                $matchFlg = true;

                // 廃止の場合
                if($cmdbMenuList['DISUSE_FLAG'] == "1"){

                    // 復活する
                    $updateData = $cmdbMenuList;
                    $updateData['NOTE']             = "";                   // 備考
                    $updateData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                    //////////////////////////
                    // 紐付対象メニューテーブルを更新
                    //////////////////////////
                    $result = $cmdbMenuListTable->updateTable($updateData, $jnlSeqNo);
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
            $insertData['MENU_ID']          = $hostMenuId;          // メニュー
            $insertData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

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
function updateLinkTargetTable($hostMenuId, $tableName){
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

                $updateFlg = false;
                // 廃止の場合、更新する
                if($cmdbMenuTable['DISUSE_FLAG'] == "1"){
                    $updateFlg = true;
                }
                // テーブル名が異なる場合、更新する
                if($cmdbMenuTable['TABLE_NAME'] != $tableName){
                    $updateFlg = true;
                }

                if(true === $updateFlg){
                    // 更新する
                    $updateData = $cmdbMenuTable;
                    $updateData['TABLE_NAME']       = $tableName;           // テーブル名
                    $updateData['PKEY_NAME']        = "ROW_ID";             // 主キー
                    $updateData['NOTE']             = "";                   // 備考
                    $updateData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
                    $updateData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

                    //////////////////////////
                    // 紐付対象メニューテーブル管理テーブルを更新
                    //////////////////////////
                    $result = $cmdbMenuTableTable->updateTable($updateData, $jnlSeqNo);
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
            $insertData['MENU_ID']          = $hostMenuId;          // メニュー
            $insertData['TABLE_NAME']       = $tableName;           // テーブル名
            $insertData['PKEY_NAME']        = "ROW_ID";             // 主キー
            $insertData['DISUSE_FLAG']      = "0";                  // 廃止フラグ
            $insertData['LAST_UPDATE_USER'] = USER_ID_CREATE_PARAM; // 最終更新者

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
function updateLinkTargetColumn($hostMenuId, $itemInfoArray, $itemColumnGrpArrayArray){
    global $objDBCA, $db_model_ch, $objMTS;
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);
    $cmdbMenuColumnTable = new CmdbMenuColumnTable($objDBCA, $db_model_ch);

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

        //////////////////////////
        // 登録するカラム情報を作成する
        //////////////////////////
        $columnInfoArray = array();

        foreach($itemInfoArray as $key => $itemInfo){

            // 項目名を作成
            $columnGrp = implode("/", $itemColumnGrpArrayArray[$itemInfo['CREATE_ITEM_ID']]);
            if("" != $columnGrp){
                $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $columnGrp . "/" . $itemInfo['ITEM_NAME'];
            }
            else{
                $columnTitle = $objMTS->getSomeMessage("ITACREPAR-MNU-102612") . "/" . $itemInfo['ITEM_NAME'];
            }

            // 他メニュー連携の情報を取得する
            $otherTableName = null;
            $otherPriName = null;
            $otherColumnName = null;
            if("" != $itemInfo['OTHER_MENU_LINK_ID']){
                foreach($otherMenuLinkArray as $otherMenuLink){

                    if($itemInfo['OTHER_MENU_LINK_ID'] == $otherMenuLink['LINK_ID']){
                        $otherTableName = $otherMenuLink['TABLE_NAME'];
                        $otherPriName = $otherMenuLink['PRI_NAME'];
                        $otherColumnName = $otherMenuLink['COLUMN_NAME'];
                        break;
                    }
                }
            }

            $columnInfoArray[] = array('COL_NAME' => $itemInfo['COLUMN_NAME'],
                                       'COL_TITLE' => $columnTitle,
                                       'COL_TITLE_DISP_SEQ' => $key + 2,
                                       'REF_TABLE_NAME' => $otherTableName,
                                       'REF_PKEY_NAME' => $otherPriName,
                                       'REF_COL_NAME' => $otherColumnName,
                                      );
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
                    if($cmdbMenuColumn['COL_TITLE']             != $columnInfo['COL_TITLE'] ||
                       $cmdbMenuColumn['COL_TITLE_DISP_SEQ']    != $columnInfo['COL_TITLE_DISP_SEQ'] ||
                       $cmdbMenuColumn['REF_TABLE_NAME']        != $columnInfo['REF_TABLE_NAME'] ||
                       $cmdbMenuColumn['REF_PKEY_NAME']         != $columnInfo['REF_PKEY_NAME'] ||
                       $cmdbMenuColumn['REF_COL_NAME']          != $columnInfo['REF_COL_NAME']){

                        $updateFlg = true;
                    }

                    if(true === $updateFlg){
                        // 更新する
                        $updateData = $cmdbMenuColumn;
                        $updateData['MENU_ID']              = $hostMenuId;                          // メニュー
                        $updateData['COL_NAME']             = $columnInfo['COL_NAME'];              // カラム名
                        $updateData['COL_TITLE']            = $columnInfo['COL_TITLE'];             // 項目名
                        $updateData['COL_TITLE_DISP_SEQ']   = $columnInfo['COL_TITLE_DISP_SEQ'];    // 表示順
                        $updateData['REF_TABLE_NAME']       = $columnInfo['REF_TABLE_NAME'];        // 参照テーブル
                        $updateData['REF_PKEY_NAME']        = $columnInfo['REF_PKEY_NAME'];         // 参照主キー
                        $updateData['REF_COL_NAME']         = $columnInfo['REF_COL_NAME'];          // 参照カラム
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
                $insertData['COL_TITLE']            = $columnInfo['COL_TITLE'];             // 項目名
                $insertData['COL_TITLE_DISP_SEQ']   = $columnInfo['COL_TITLE_DISP_SEQ'];    // 表示順
                $insertData['REF_TABLE_NAME']       = $columnInfo['REF_TABLE_NAME'];        // 参照テーブル
                $insertData['REF_PKEY_NAME']        = $columnInfo['REF_PKEY_NAME'];         // 参照主キー
                $insertData['REF_COL_NAME']         = $columnInfo['REF_COL_NAME'];          // 参照カラム
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
        $insertData['COL_CNT']   = $cpiData['COL_CNT'];             // 項目数
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
