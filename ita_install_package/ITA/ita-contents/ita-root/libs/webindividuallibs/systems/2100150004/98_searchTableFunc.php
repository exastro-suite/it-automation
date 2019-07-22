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
    //    表示フィルタの検索条件でDBを検索する
    //
    //////////////////////////////////////////////////////////////////////

function searchFunc($aryVariant, &$resultArray, &$strErrMsgBodyToHtmlUI){

    global $g;

    // ----DBアクセスを伴う処理
    try{
        //----ここから01_系から06_系全て共通
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        //ここまで01_系から06_系全て共通----

    }
    catch (Exception $e){
        // ----DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
        // DBアクセス例外処理パーツ----
    }

    require_once("{$g['root_dir_path']}/webconfs/systems/2100150004_loadTable.php");

    $objTable = loadTable();

        // ----ローカル変数宣言
    $intErrorType = null;

    $refRetKeyExists = null;
    $boolKeyExists = null;

    $objFunction01ForOverride = null;

    $ACRCM_id = "UNKNOWN";

    $strErrMsgBodyToHtmlUI = "";
    $strErrMsgBodyToWAL = "";

    $filterData = array();
    $boolBinaryDistinctOnDTiS = null;

    $strFxName = __FUNCTION__;
    // ローカル変数宣言----

    try{
        //----出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)
        $strToAreaType = "toStd";
        //出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)----

        //----メニューIDの取得
        list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
        //メニューIDの取得----

        if( is_array($aryVariant) !== true ){
            //----引数の型が不正
            $intErrorType = 101;
            throw new Exception("var[aryVariant] is not Array. errorType=[" . $intErrorType . "]");
            //引数の型が不正----
        }

        $strFormatterId = "excel";

        $objListFormatter = $objTable->getFormatter($strFormatterId);

        if( is_a($objListFormatter, "ListFormatter") !== true ){
            // ----リストフォーマッタクラスではない
            $intErrorType = 102;
            throw new Exception("var[objListFormatter] is not class[ListFormatter]. errorType=[" . $intErrorType . "]");
        }

        //----PHPのタイムアウトを再設定する(単位は秒)
        $intDTFTimeLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFTimeLimit",$refRetKeyExists);
        if( $intDTFTimeLimit===null && $refRetKeyExists===false ){
            $intDTFTimeLimit = $objTable->getGeneObject("intDTFTimeLimit",$refRetKeyExists);
        }
        if( $intDTFTimeLimit!==null && is_int($intDTFTimeLimit)===true ){
            set_time_limit($intDTFTimeLimit);
        }
        //PHPのタイムアウトを再設定する(単位は秒)----

        //----PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))
        $intDTFMemoryLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFMemoryLimit",$refRetKeyExists);
        if( $intDTFMemoryLimit===null && $refRetKeyExists===false ){
            $intDTFMemoryLimit = $objTable->getGeneObject("intDTFMemoryLimit",$refRetKeyExists);
        }
        if( $intDTFMemoryLimit!==null && is_int($intDTFMemoryLimit) ){
            ini_set("memory_limit",strval($intDTFMemoryLimit)."M");
        }
        //PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))----

        $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
        $arrayObjColumn = $objTable->getColumns();

        if( array_key_exists("search_filter_data",$aryVariant) === true ){
            //----検出条件が指定された場合

            //----大文字小文字と全角半角を無視する設定かを調べる
            $boolBinaryDistinctOnDTiS = $objTable->getFormatter($strFormatterId)->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
            if( $boolBinaryDistinctOnDTiS === null && $refRetKeyExists === false ){
                $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
            }
            if( is_bool($boolBinaryDistinctOnDTiS) === false ){
                $boolBinaryDistinctOnDTiS = false;
            }
            // 大文字小文字と全角半角を無視する設定かを調べる----

            //----検出条件、を解析する

            $filterData = $aryVariant['search_filter_data'];

            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_currentPrint");
            $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;

            //----必須チェックなどを事前にしたい場合は、ここで差し替え
            if( $aryFunctionForOverride !== null ){
                 list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"DTiSFilterCheckValid"),null);
                 unset($tmpBoolKeyExist);
                 if( is_callable($tmpObjFunction01ForOverride) === true ){
                     $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                 }
                 unset($tmpObjFunction01ForOverride);
            }
            //必須チェックなどを事前にしたい場合は、ここで差し替え----

            foreach($arrayObjColumn as $objColumn){
                $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                if( $arrayTmp[0] === false ){
                    $intErrorType = $arrayTmp[1];
                    throw new Exception("An error occurred in function[beforeDTiSValidateCheck]. errorType=[" . $intErrorType . "]");
                }
            }

            //----バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）
            if( $objFunction01ForOverride === null ){
                $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting);
                $funcName = "DTiSFilterCheckValid";
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant);
                $funcName = "objFunction01ForOverride";
            }

            if( $tmpAryRet[1] !== null ){
                $intErrorType = $tmpAryRet[1];
                throw new Exception("An error occurred in function[" . $funcName . "]. errorType=[" . $intErrorType . "]");
            }
            //バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）----

            //検出条件、を解析する----

            foreach($arrayObjColumn as $objColumn){
                $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);

                if( $arrayTmp[0] === false ){
                    $intErrorType = $arrayTmp[1];
                    throw new Exception("An error occurred in function[beforeDTiSAction]. errorType=[" . $intErrorType . "]");
                }
            }

            //検出条件が指定された場合----
        }else{
            //----検出条件が指定されなかった場合
            $boolBinaryDistinctOnDTiS = false;
            //検出条件が指定されなかった場合----
        }

        $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

        // ----generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]
        $sql = generateSelectSql2(2, $objTable, $boolBinaryDistinctOnDTiS);
        // generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]----

        $strFxName = __CLASS__."::".__FUNCTION__;
        // SQL実行
        $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);

        if( $retArray[0] === true ){
            $objQuery =& $retArray[1];
            $intFetchCount = 0;

            while($row = $objQuery->resultFetch()){
                $intFetchCount += 1;
                $resultArray[] = $row;
            }
            unset($objQuery);
        }
        else{
            $intErrorType = 103;
            throw new Exception("");
        }

        return true;
    }
    catch (Exception $e){
        //----エラー発生時

        // ----一般訪問ユーザに見せてよいメッセージを作成
        switch($intErrorType){
            case 2: // バリデーションエラー系
                // バリデーションエラーが発生しました。
                $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-312");
                break;
            default: // システムエラーが発生しました。
                $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
                break;
        }

        $tmpErrMsgBody = $e->getMessage();

        if("" !== $tmpErrMsgBody){
            $strErrMsgBodyToWAL = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-303",array($ACRCM_id, "ZIP", $tmpErrMsgBody));
        }

        // アクセスログへ記録
        if( 0 < strlen($strErrMsgBodyToWAL) ) web_log($strErrMsgBodyToWAL);

        return false;
        //エラー発生時----
    }
}
?>
