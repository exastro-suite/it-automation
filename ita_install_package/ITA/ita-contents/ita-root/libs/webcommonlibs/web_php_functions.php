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

    function Filter1Cht_recDraw($arrayReceptData){

        // グローバル変数宣言
        global $g;
        global $objMTS;

        // ローカル変数宣言
        $arrayResult = array();
        $aryVariant = array();
        $arySetting = array();
        $aryTmpSetting = array();
        $objTable = loadTable();

        // ルートディレクトリを取得
        $root_dir_path = $g['root_dir_path'];
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        $filterData = array();
        $filterData = convertReceptDataToDataForFilter($arrayReceptData);
        $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");

        $intControlDebugLevel01 = 250;
        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;

        $defaultValueOnFx = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeSubtotal_Table");
        $refRetKeyExists = null;

        $boolBinaryDistinctOnDTiS = false;
        $strFormatterId = "print_table";
        $strFxName = __FUNCTION__;

        //----D-TiS共通
        $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "CurrentTableFormatter", $strFormatterId);
        $checkFormatterId = $retArray[1];
        $objListFormatter = $retArray[2];


        $arrayObjColumn = $objTable->getColumns();
        $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);

        $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID(); //"DISUSE_FLAG"
        $lcRequiredUpdateButtonColumnId = $objTable->getRequiredUpdateButtonColumnID(); //"UPDATE"
        $lcDuplicateButtonColumnId = $objTable->getDupButtonColumnID(); //"DUPLICATE"

        //----出力されるタグの属性値

        //----関数内デフォルト
        $strShowTable01TagId = $objListFormatter->getGeneValue("stdCurrentTable.tagIDonHTML",$refRetKeyExists);
        if( $strShowTable01TagId===null && $refRetKeyExists===false  ){
            $strShowTable01TagId = $defaultValueOnFx[0];
        }
        $strShowTable01WrapDivClass = $objListFormatter->getGeneValue("stdCurrentTable.wrapDivClass",$refRetKeyExists);
        if( $strShowTable01WrapDivClass===null && $refRetKeyExists===false  ){
            $strShowTable01WrapDivClass = $defaultValueOnFx[1];
        }
        $strShowTable02TagId = $objListFormatter->getGeneValue("stdSubtotalTable.tagIDonHTML",$refRetKeyExists);
        if( $strShowTable02TagId===null && $refRetKeyExists===false  ){
            $strShowTable02TagId = $defaultValueOnFx[2];
        }
        $strShowTable02WrapDivClass = $objListFormatter->getGeneValue("stdSubtotalTable.wrapDivClass",$refRetKeyExists);
        if( $strShowTable02WrapDivClass===null && $refRetKeyExists===false  ){
            $strShowTable02WrapDivClass = $defaultValueOnFx[3];
        }
        //関数内デフォルト----

        //出力されるTableタグの属性値----

        // ----固有
        $optAllHidden=false;
        if(array_key_exists("optionAllHidden",$aryVariant)===true){
            $optAllHidden = $aryVariant['optionAllHidden'];
        }
        if( isset($aryVariant["TCA_PRESERVED"])===false ){
            $aryVariant["TCA_PRESERVED"] = array();
        }
        $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_currentPrint");
        $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;
        // 固有----

        // ----boolBinaryDistinctOnDTiS判定
        $boolBinaryDistinctOnDTiS = $objListFormatter->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
        if( $boolBinaryDistinctOnDTiS===null && $refRetKeyExists===false ){
            $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
        }
        if(is_bool($boolBinaryDistinctOnDTiS)===false){
            $boolBinaryDistinctOnDTiS = false;
        }
        // boolBinaryDistinctOnDTiS判定----

        foreach($arrayObjColumn as $objColumn){
            $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
            if($arrayTmp[0]===false){
                $intErrorType = $arrayTmp[1];
                throw new Exception( '00010200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }

        // ----バリデーションチェック(今回は独自バリデーションは無いものとして直接実行する
        $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant);
        // バリデーションチェック(今回は独自バリデーションは無いものとして直接実行する----

        // ----フィルタへの入力値をもとにフィルタデータを作成
        foreach($arrayObjColumn as $objColumn){
            $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
            if($arrayTmp[0]===false){
                $intErrorType = $arrayTmp[1];
                throw new Exception( '00010400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        // フィルタへの入力値をもとにフィルタデータを作成----

        $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

        // 生成されたSQLからグラフ描画用の独自SQLを作成
        $sql = generateSelectSql2(2, $objTable, $boolBinaryDistinctOnDTiS);

        $sqlTmp = explode("FROM", $sql, 2);

        //初期宣言
        $graphDataForLines = array();
        $dateForCalcMinMax = array();

        //ステータス：完了
        $statusIdComplete  = array( "x" => array(), "y" => array(), );
        $sumOfComplete = 0;

        //ステータス：完了(異常)
        $statusIdFailed    = array( "x" => array(), "y" => array(), );
        $sumOfFailed = 0;

        //ステータス：緊急停止
        $statusIdEmage     = array( "x" => array(), "y" => array(), );
        $sumOfEmage = 0;

        //ステータス：想定外エラー
        $statusIdUnexpected = array( "x" => array(), "y" => array(), );
        $sumOfUnexpected = 0;

        //ステータス：予約取消
        $statusIdCancel  = array( "x" => array(), "y" => array(), );
        $sumOfCancel = 0;

        //SQL生成
        $sqlHead = "SELECT `T1`.`STATUS_ID`,
                           COUNT(`T1`.`STATUS_ID`) AS COUNT,
                           SUBSTRING(`T1`.`TIME_START`,1,10) AS `DATE`,
                           SUBSTRING(`T1`.`TIME_END`,1,10) AS `DATE2`,
                           SUBSTRING(`T1`.`LAST_UPDATE_TIMESTAMP`,1,10) AS `DATE3`
                    FROM ";
        $sqlTail = " GROUP BY `T1`.`STATUS_ID`, `DATE` ";
        $where = explode("ORDER", $sqlTmp[1]);
        $sql2  = $sqlHead . $where[0] . $sqlTail;

        $retArray = singleSQLExecuteAgent($sql2, $arrayFileterBody, $strFxName);

        // 画面名を取得
        $menu_id = $g["menu_id"];

        $getloadtableinfo_php = '/libs/commonlibs/common_getInfo_LoadTable.php';
        require_once ($root_dir_path . $getloadtableinfo_php );

        $objDBCA = $g["objDBCA"];

        list($aryValue,
             $intErrorType,
             $strErrMsg) = getInfoOfLTUsingIdOfMenuForDBtoDBLink($menu_id,$objDBCA);

        $status_id_col ="STATUS_ID";
        $status_name_col ="STATUS_NAME";

        if($menu_id == '2100000310'){
            $status_id_col ="SYM_EXE_STATUS_ID";
            $status_name_col ='SYM_EXE_STATUS_NAME';
        }

        // ドライバーごとのステータスID,参照テーブル,ツール名を取得
        $sql3 = "SELECT COMPLETE_ID, FAILED_ID, UNEXPECTED_ID, EMERGENCY_ID, CANCEL_ID,
                 STATUS_TAB_NAME, RELATE_STATUS_ID FROM A_RELATE_STATUS WHERE MENU_ID = '" . $menu_id ."'";

        $status_id_array = singleSQLExecuteAgent($sql3, $arrayFileterBody, $strFxName);
        if (!($status_id_array[0])){
            // アクセスログ出力(想定外エラー)
            web_log('ERROR:UNEXPECTED_ERROR([FILE]'.__FILE__.'[LINE]'.__LINE__.'[ETC-Code]00000200');

            unset($objQuery);

            // 例外処理へ
            throw new Exception();
        }
        $objQuery =& $status_id_array[1];
        $set_status = array();
        while ( $status_row = $objQuery->resultFetch() ){
            $set_status = $status_row;
        }

        // DBアクセス事後処理
        unset($objQuery);

        $id_list = array_values($set_status);
        $menu_num = sprintf("%02d",array_pop($id_list));
        $status_tab_name = array_pop($id_list);
        $ids = implode(",", $id_list);

        $sql4 = "SELECT " . $status_name_col . " FROM " . $status_tab_name .
                " WHERE " . $status_id_col . " IN (" . $ids . ")
                ORDER BY FIELD( " . $status_id_col .", " . $ids . ")";

        $status_name_array = singleSQLExecuteAgent($sql4, $arrayFileterBody, $strFxName);
        if (!$status_name_array[0]){
            // アクセスログ出力(想定外エラー)
            web_log('ERROR:UNEXPECTED_ERROR([FILE]'.__FILE__.'[LINE]'.__LINE__.'[ETC-Code]00000200');

            unset($objQuery);

            // 例外処理へ
            throw new Exception();
        }
        $objQuery =& $status_name_array[1];
        $set_status_name = array();
        while ( $status_name_row = $objQuery->resultFetch() ){
            $st_row = $status_name_row[$status_name_col];
            array_push($set_status_name, $st_row);
        }
        // DBアクセス事後処理
        unset($objQuery);

        if (!$retArray[0]){
            // アクセスログ出力(想定外エラー)
            web_log('ERROR:UNEXPECTED_ERROR([FILE]'.__FILE__.'[LINE]'.__LINE__.'[ETC-Code]00000200');

            unset($objQuery);

            // 例外処理へ
            throw new Exception();
        }
        $objQuery =& $retArray[1];
        while ( $row = $objQuery->resultFetch() ){
            // ステータスIDを取得
            if( $row["DATE2"] != "" ){
                $dateForCalcMinMax[] = $row["DATE2"];
                $graphDataForLines[] = $row;
            }else if( $row["DATE"] != "" ){
                $dateForCalcMinMax[] = $row["DATE"];
                $graphDataForLines[] = $row;
            }else if( $row["DATE3"] != "" ){
                $dateForCalcMinMax[] = $row["DATE3"];
                $graphDataForLines[] = $row;
            }
        }

        // ループ回数を取得
        $num_rows = $objQuery->effectedRowCount();
        $graph_vals = $objQuery->effectedRowCount();

        // DBアクセス事後処理
        unset($objQuery);

        $tmpIdComplete = array();
        $tmpIdFailed = array();
        $tmpIdUnexpected = array();
        $tmpIdEmage = array();
        $tmpIdCancel = array();

        if( $num_rows !== 0 ){
            if($dateForCalcMinMax){
                // ----日付初期化
                $dateMax = new DateTime(max($dateForCalcMinMax));
                $dateMin = new DateTime(min($dateForCalcMinMax));
                $dateDiff = $dateMax->diff($dateMin);
                $dateCnt = $dateDiff->format('%a');
                // 日付初期化----

                $tmpDate = $dateMin;
                for( $i = 0; $i < $dateCnt+1; $i++){
                    $tmpIdComplete[$tmpDate->format('Y-m-d')] = null;
                    $tmpIdFailed[$tmpDate->format('Y-m-d')] = null;
                    $tmpIdUnexpected[$tmpDate->format('Y-m-d')] = null;
                    $tmpIdEmage[$tmpDate->format('Y-m-d')] = null;
                    $tmpIdCancel[$tmpDate->format('Y-m-d')] = null;
                    $tmpDate = $dateMin->modify('+1 days');
                }
            }
            else{
                $graph_vals = 0;
            }
        }

        //ステータスIDごとにグラフ用配列に格納
        foreach( $graphDataForLines as $array ){
            if( $array["DATE"] != false ){
                $date  = $array["DATE"];
            }else if( $array["DATE2"] != false ){
                $date = $array["DATE2"];
            }else if( $array["DATE3"] != false ){
                $date = $array["DATE3"];
            }else{
                continue;
            }
            switch( $array["STATUS_ID"] ){
            case ($set_status["COMPLETE_ID"]):
                if($tmpIdComplete[$date]){
                    $tmpIdComplete[$date] += $array["COUNT"];
                }
                else{
                    $tmpIdComplete[$date] = $array["COUNT"];
                }
                $sumOfComplete += $array["COUNT"];
                break;

            case ($set_status["FAILED_ID"]):
                if($tmpIdFailed[$date]){
                    $tmpIdFailed[$date] += $array["COUNT"];
                }
                else{
                    $tmpIdFailed[$date] = $array["COUNT"];
                }
                $sumOfFailed += $array["COUNT"];
                break;

            case ($set_status["UNEXPECTED_ID"]):
                if($tmpIdUnexpected[$date]){
                    $tmpIdUnexpected[$date] += $array["COUNT"];
                }
                else{
                    $tmpIdUnexpected[$date] = $array["COUNT"];
                }
                $sumOfUnexpected += $array["COUNT"];
                break;

            case ($set_status["EMERGENCY_ID"]):
                if($tmpIdEmage[$date]){
                    $tmpIdEmage[$date] += $array["COUNT"];
                }
                else{
                    $tmpIdEmage[$date] = $array["COUNT"];
                }
                $sumOfEmage += $array["COUNT"];
                break;

            case ($set_status["CANCEL_ID"]):
                if($tmpIdCancel[$date]){
                    $tmpIdCancel[$date] += $array["COUNT"];
                }
                else{
                    $tmpIdCancel[$date] = $array["COUNT"];
                }
                $sumOfCancel += $array["COUNT"];
                break;

            case 9:
                break;
            }

        }


        // データ配列の前後に0の値を挿入
        $status_arr = array($tmpIdComplete, $tmpIdFailed, $tmpIdUnexpected, $tmpIdEmage, $tmpIdCancel);
        $update_data = array();

        foreach( $status_arr as $arr ){
            $value_list = preg_grep("/^[1-9]/", $arr);

            if($value_list){
                foreach($value_list as $key => $val){
                    $val_ts = strtotime($key);
                    $result = strtotime("-1 day", $val_ts);
                    $result2 = date("Y-m-d", $result);

                    if(array_key_exists($result2, $arr)){
                        if($arr[$result2] == null){
                            $arr[$result2] = 0;
                        }
                    }
                    $result = strtotime("+1 day", $val_ts);
                    $result2 = date("Y-m-d", $result);

                    if(array_key_exists($result2, $arr)){
                        if($arr[$result2] == null){
                            $arr[$result2] = 0;
                        }
                    }
                }
            }
            array_push($update_data, $arr);
        }
        $tmpIdComplete = $update_data[0];
        $tmpIdFailed = $update_data[1];
        $tmpIdUnexpected = $update_data[2];
        $tmpIdEmage = $update_data[3];
        $tmpIdCancel = $update_data[4];

        // データ形式をグラフに適した形状に変更
        foreach( $tmpIdComplete as $key => $value ){
            $statusIdComplete["x"][] = $key;
            $statusIdComplete["y"][] = $value;
        }

        foreach( $tmpIdFailed as $key => $value ){
            $statusIdFailed["x"][] = $key;
            $statusIdFailed["y"][] = $value;
        }

        foreach( $tmpIdUnexpected as $key => $value ){
            $statusIdUnexpected["x"][] = $key;
            $statusIdUnexpected["y"][] = $value;
        }

        foreach( $tmpIdEmage as $key => $value ){
            $statusIdEmage["x"][] = $key;
            $statusIdEmage["y"][] = $value;
        }

        foreach( $tmpIdCancel as $key => $value ){
            $statusIdCancel["x"][] = $key;
            $statusIdCancel["y"][] = $value;
        }


        // ツール名を取得
        $menu_num = 'ITAWDCH-STD-' . '50' . $menu_num;
        $tool_name = $objMTS->getSomeMessage($menu_num);

        // グラフ画面に表示される日付データ件数を集計
        $date_arr = array_merge($statusIdComplete["x"], $statusIdFailed["x"], $statusIdUnexpected["x"], 
                                $statusIdEmage["x"], $statusIdCancel["x"]);
        $unique_date = array_unique($date_arr);
        $date_rows = count($unique_date);


        // 結果配列に格納し、jsonにエンコード
        $arrayResult = array();
        $arrayResult["tool_name"]          = $tool_name;
        $arrayResult["set_status_name"]    = $set_status_name;
        $arrayResult["num_rows"]           = $num_rows;
        $arrayResult["date_rows"]          = $date_rows;
        $arrayResult["graph_vals"]         = $graph_vals;
        $arrayResult["AryComplete"]["x"]   = $statusIdComplete["x"];
        $arrayResult["AryComplete"]["y"]   = $statusIdComplete["y"];
        $arrayResult["AryFailed"]["x"]     = $statusIdFailed["x"];
        $arrayResult["AryFailed"]["y"]     = $statusIdFailed["y"];
        $arrayResult["AryUnexpected"]["x"] = $statusIdUnexpected["x"];
        $arrayResult["AryUnexpected"]["y"] = $statusIdUnexpected["y"];
        $arrayResult["AryEmage"]["x"]      = $statusIdEmage["x"];
        $arrayResult["AryEmage"]["y"]      = $statusIdEmage["y"];
        $arrayResult["AryCancel"]["x"]     = $statusIdCancel["x"];
        $arrayResult["AryCancel"]["y"]     = $statusIdCancel["y"];
        $arrayResult["SumComplete"]        = $sumOfComplete;
        $arrayResult["SumFailed"]          = $sumOfFailed;
        $arrayResult["SumUnexpected"]      = $sumOfUnexpected;
        $arrayResult["SumEmage"]           = $sumOfEmage;
        $arrayResult["SumCancel"]          = $sumOfCancel;

        $arrayResult = json_encode($arrayResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $arrayResult;
    }

    //----クライアントからの受信解析用
    //----動的区切り解析用
    function getArrayBySafeSeparator($strStream, $strTailMark=""){
        $retVar = "";
        if( $strTailMark == "" ){
            $strTailMark = ";";
        }
        $varPos = mb_strpos($strStream, $strTailMark, 0,"UTF-8");
        if( $varPos !== false ){
            $strSepa = mb_substr($strStream, 0, $varPos + mb_strlen($strTailMark, 'UTF-8'));
            $intSepaLen = mb_strlen($strSepa, 'UTF-8');
            $strData = mb_substr($strStream, $intSepaLen, mb_strlen($strStream, 'UTF-8') - $intSepaLen );
            $retVar = explode($strSepa, $strData);
        }
        else{
            $retVar = $strStream;
        }
        return $retVar;
    }
    //動的区切り解析用----
    //クライアントからの受信解析用----

    //----クライアントへの送信用
    function makeAjaxProxyResultStream($aryResultElement){
        $strSafeSepa = makeSafeSeparator($aryResultElement);
        return $strSafeSepa.implode($strSafeSepa,$aryResultElement);
    }

    function makeSafeSeparator($varCheckDataSourceBody,$miStrEscAfterHead="ss",$miStrEscAfterTail=";",$boolRandom=true){
        $miBoolEscRequire=false;
        $miRandomNum  ="";
        if( $boolRandom === true ){
            $miRandomNum = rand(1,256);
        }
        if( is_string($varCheckDataSourceBody) === true ){
            $aryForCheck = array($varCheckDataSourceBody);
        }
        else if( is_array($varCheckDataSourceBody) === true ){
            $aryForCheck = $varCheckDataSourceBody;
        }
        $miStrForbiddenStr = $miStrEscAfterHead.$miRandomNum.$miStrEscAfterTail;
        foreach($aryForCheck as $miCheckDataSourceBody){
            if( mb_strpos($miCheckDataSourceBody, $miStrForbiddenStr, 0,"UTF-8") !== false ){
                //----含まれていた場合
                $miBoolEscRequire=true;
                //含まれていた場合---
            }
        }
        if( $miBoolEscRequire === true ){
            $miSearchCount = 0;
            do{
                $miSearchCount+=1;
                $miSearchPattern = $miStrEscAfterHead.$miRandomNum.strval($miSearchCount).$miStrEscAfterTail;
                foreach($aryForCheck as $miCheckDataSourceBody){
                    $miSearchResult=mb_strpos($miCheckDataSourceBody, $miSearchPattern, 0,"UTF-8");
                    if( $miSearchResult !== false ){
                        break;
                    }
                }
            }while( $miSearchResult !== false );
            $miStrForbiddenStr = $miSearchPattern;
        }
        return $miStrForbiddenStr;
    }
    //クライアントへの送信用----

    function getJscriptMessageTemplate($aryImportFilePath, &$objMTS){
        //----メッセージテンプレートの素材を収集
        $aryJsMsgOrgBody = array();
        foreach($aryImportFilePath as $strTmplFilePath){
            $aryJsMsgOrgBody = array_merge($aryJsMsgOrgBody,$objMTS->getArrayFromTemplate($strTmplFilePath));
        }
        //メッセージテンプレートの素材を収集----
        
        $aryJsMsgData = array();
        foreach($aryJsMsgOrgBody as $key=>$val){
            $key = str_replace("-","",$key);
            $aryJsMsgData[] = $key.":".$val;
        }
        //----動的にデリミッターを計算
        $strSepaHead = "dysp";
        $intCheck = 0;
        do
        {
            $strCheckDelimiter = $strSepaHead.$intCheck.";";
            foreach($aryJsMsgData as $val){
                if( mb_strpos($val, $strCheckDelimiter, 0, "UTF-8" ) !== false ){
                    $intCheck += 1;
                    break;
                }
            }
            if( $strCheckDelimiter == $strSepaHead.$intCheck.";" ){
                $intCheck = false;
                break;
            }
        } while( $intCheck !== false );
        //動的にデリミッターを計算----
        return $strCheckDelimiter.implode($strCheckDelimiter,$aryJsMsgData);
    }
    //00-開発者領域画面そのほかシステム用----

    function printHeaderForProvideFileStream($strProvideFilename,$strContentType="application/vnd.ms-excel",$varContentLength=null){
        // excelまたはcsv出力用httpレスポンスヘッダ出力
        ky_printHeaderForProvideBinaryStream($strProvideFilename,$strContentType,$varContentLength);
    }

    function dev_log($textBody, $intPointDetailLevel=1, $boolEveryone=false){
        // グローバル変数の利用宣言
        global $g;
        $intReqClientDevFlag = isset($g['dev_log_developer'])?$g['dev_log_developer']:0;
        if( 0 < $intReqClientDevFlag || $boolEveryone === true ){
            if( isset($g['root_dir_path']) ){
                $lc_root_dir_path = $g['root_dir_path'];
            }
            else{
                $lc_root_dir_path = getApplicationRootDirPath();
            }

            $stampTime = time();
            $filePrefix=str_replace(".","_",getSourceIPAddress());
            $filename=$filePrefix."_debug_dev_log_".date("Ymd",$stampTime).".log";
            $set_dir_path = $lc_root_dir_path."/logs/dev_log";
            if( is_dir($set_dir_path) === false ){
                //----構成破壊
                $boolOutput = false;
                //構成破壊----
            }
            else{
                //----＜-エラー制御演算子@を付加＞
                $boolOutput = @file_put_contents($set_dir_path."/".$filename, date("Y/m/d H:i:s",$stampTime)." ".$textBody."\n", FILE_APPEND );
                //＜-エラー制御演算子@を付加＞----
            }
            if( $boolOutput === false ){
                web_log("Dev_log error is occured on directory [{$set_dir_path}]. Dev_log text is [{$textBody}].");
                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,null);
                exit();
            }
        }
    }

    function webRequestForceQuitFromEveryWhere ($intDefaultResutStatusCode=500, $intForceQuitDatailCode=null, $aryAppendix=array()) {
        // グローバル変数の利用宣言
        global $g;
        list($aryReqByREST, $tmpBool) = isSetInArrayNestThenAssign($g, array('requestByREST'), null);
        if (is_array($aryReqByREST) === true) {
            //----RestAPIからのアクセスの場合
            $strException = 'Generic error';
            switch ($intDefaultResutStatusCode) {
                case 400: // 要求が正しくない
                    $strErrorType = "Bad Request";
                    break;
                case 401: // 認証が必要である
                    $strErrorType = "Unauthorized";
                    break;
                case 403: // 禁止されている（アクセス権がない、ホストがアクセスすることを拒否された）
                    $strErrorType = "Forbidden";
                    break;
                case 404: // リソースがみつからなかった
                    $strErrorType = "Not Found";
                    break;
                case 500: // サーバ内部エラー
                    $strErrorType = "Internal Server Error";
                    break;
                case 501: // 実装されていないメソッド
                    $strErrorType = "Not Implemented";
                    break;
                case 502: // 不正なゲートウェイ
                    $strErrorType = "Bad Gateway";
                    break;
                case 503: // サービス利用不可（過負荷、メンテナンス中による）
                    $strErrorType = "Service Unavaliable";
                    break;
                default:
                    $intDefaultResutStatusCode = 500;
                    $strErrorType = "Unexpected error";
                    break;
            }
            switch ($intForceQuitDatailCode) {
                case 11410201: // 権限がなかった
                    $strException = "No Privillege Access Error";
                    break;
                case 10410301: // IPアドレス（ホワイトリスト）に登録されていない
                    $strException = "Access Forbidden Error";
                    break;
                case 11410401: // 未認証アクセス
                    $strException = "Access Forbidden Error";
                    break;
                case 11410501: // パスワード有効期限切れ
                    $strException = "Password Expired Error";
                    break;
                case 11410601: // アカウントロック
                    $strException = "Account Locked Error";
                    break;
                case 11410701: // 開発者によるメンテナンス(中の通知)画面
                    $strException = "In Maintenance Mode, Access Forbidden Error";
                    break;
                case 11510801: // 不正なリクエスト（形式の不正）
                case 11510802: // 不正なリクエスト（形式の不正）
                    $strException = "Content-type Is Not Correct Error";
                    break;
                default: // システムエラー
                    break;
            }
            list($varStackTrace, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('StackTrace'),false);
            if (is_array($varStackTrace) === false && is_string($varStackTrace) === false) {
                $varStackTrace = 'none';
            }
            $intResultStatusCode = $intDefaultResutStatusCode;
            $aryResponsContents  = array('Error'=>$strErrorType,
                                         'Exception'=>$strException,
                                         'StackTrace'=>$varStackTrace);

            list($boolOverrideByGlobalVars, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix, array('OverrideByGlobalVars'), false);
            if ($boolOverrideByGlobalVars === true) {
                list($intResultStatusCode, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($g,array('requestByREST','resultStatusCode'),$intResultStatusCode);
                list($aryResponsContents, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($g,array('requestByREST','preResponsContents','errorInfo'),$aryResponsContents);
            }

            header('Content-Type: application/json; charset=utf-8', true, $intResultStatusCode);
            $objJSONOfResultData = @json_encode($aryResponsContents);

            exit($objJSONOfResultData);

            //RestAPIからのアクセスの場合----
        } else {
            //----その他のリクエストの場合
            switch ($intDefaultResutStatusCode) {
                case 400: // 要求が正しくない
                case 401: // 認証が必要である
                case 403: // 禁止されている（アクセス権がない、ホストがアクセスすることを拒否された）
                case 404: // リソースがみつからなかった
                case 500: // サーバ内部エラー
                    http_response_code($intDefaultResutStatusCode);
                    break;
            }

            list($intInsideRedirectMode, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('InsideRedirectMode'),1); // 0

            $getCopy = $_GET;
            unset($getCopy['m']);
            unset($getCopy['c']);
            $get_parameter_anp = "";
            if("" != http_build_query($getCopy)){
                $get_parameter_anp = "&" . http_build_query($getCopy);
            }
            $get_parameter_anp = str_replace('+', '%20', $get_parameter_anp);

            //MDC(NNN)+
            switch ($intForceQuitDatailCode) {
                // 403
                case 10000403: // 不正操作によるアクセス警告画面にリダイレクト
                case 10310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 10610201: // 不正操作によるアクセス警告画面にリダイレクト
                case 10810201: // 不正操作によるアクセス警告画面にリダイレクト
                case 11210201: // 不正操作によるアクセス警告画面にリダイレクト
                case 11310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20110201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20310202: // 不正操作によるアクセス警告画面にリダイレクト
                case 20410201: // 不正操作によるアクセス警告画面にリダイレクト
                    if ( isset($_GET['grp']) ) {
                        if ( $_GET["grp"] != "0000000000" ) {
                            $alert = "<script type='text/javascript'>alert('".$g['objMTS']->getSomeMessage("ITAWDCH-MNU-4030004")."');</script>";
                            echo $alert;
                        }
                    } else {
                        global $objMTS;
                        $alert = "<script type='text/javascript'>alert('".$objMTS->getSomeMessage("ITAWDCH-MNU-4030004")."');</script>";
                        echo $alert;
                    }
                    insideRedirectCodePrint("/default/mainmenu/01_browse.php",$intInsideRedirectMode);
                    break;
                case 10410301: // IPアドレス（ホワイトリスト）に登録されていない
                    insideRedirectCodePrint("/common/common_access_filter.php",$intInsideRedirectMode);
                    break;
                case 10610401: // 認証画面にリダイレクト
                    list($aryValueForPost,  $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('ValueForPost'),array());
                    insideRedirectCodePrint("/common/common_auth.php?login{$get_parameter_anp}",$intInsideRedirectMode,$aryValueForPost, true);
                    break;
                case 10710501: // パスワード変更画面にリダイレクト
                    list($strMenuIdNumeric, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('MenuID'),null);
                    list($aryValueForPost,  $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('ValueForPost'),array());
                    list($strMenuGroupIdNumeric, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('MenuGroupID'),null);
                    insideRedirectCodePrint("/common/common_change_password_form.php?login{$get_parameter_anp}",$intInsideRedirectMode,$aryValueForPost);
                    break;
                case 10310601: // アカウントロック画面にリダイレクト
                case 10310602: // アカウントロック画面にリダイレクト
                    insideRedirectCodePrint("/common/common_account_locked_error.php",$intInsideRedirectMode);
                    break;
                case 10610701: // 開発者によるメンテナンス(中の通知)画面
                    insideRedirectCodePrint("/common/common_dev_maintenace.php",$intInsideRedirectMode);
                    break;
                default: // エラーコード毎のエラーページを表示する
                    switch ($intDefaultResutStatusCode) {
                        case 400: // 不正なリクエスト
                            $err_file_path = "/webroot/common/common_bad_request.php";
                            break;
                        case 401: // 未認証アクセス
                            $err_file_path = "/webroot/common/common_unauthorized.php";
                            break;
                        case 403: // 禁止されているアクセス
                            $err_file_path = "/webroot/common/common_forbidden.php";
                            break;
                        case 404: // リソースがみつからなかった
                            $err_file_path = "/webroot/common/common_not_found.php";
                            break;
                        case 500: // サーバ内部エラー
                            $err_file_path = "/webroot/common/common_internal_server_error.php";
                            break;
                        default:
                            $err_file_path = "/webroot/common/common_internal_server_error.php";
                            break;
                    }
                    include preg_replace('|^(.*/ita-root)/.*$|', '$1', __FILE__).$err_file_path;
                    break;
            }
            //その他のリクエストの場合----
        }
        exit();
    }

    function insideRedirectCodePrint($strUrlOfInside="", $mode=0, $aryPostData=array(), $exeCheckAuth=false){
        // グローバル変数の利用宣言
        global $g;
        // URLのスキーム＆オーソリティを取得
        $scheme_n_authority = getSchemeNAuthority();
        $strRediretTo = $scheme_n_authority.$strUrlOfInside;
        list($strReqByHA,$tmpBool)=isSetInArrayNestThenAssign($g,array('requestByHA'),"");
        if( 0 == strlen($strReqByHA) ){
            //----HTML/AJAX経由ではない場合
            switch($mode){
                case 1:
                    $hiddenInputBody = "";
                    foreach($aryPostData as $key=>$val){
                        $hiddenInputBody .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$val}\">";
                    }

                    print 
<<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<form method="POST" id="redirectAgent" name="redirectAgent" action="{$strRediretTo}">
{$hiddenInputBody}
</form>
<script type="text/javascript">
window.onload = function(){
var obj = document.getElementById('redirectAgent');
if( obj === null ){
}else if( obj === undefined ) {
}else{
}
obj.submit();
}
</script>
</body>
</html>
EOD;
                    break;
                default:
                    header("Location: " . $strRediretTo);
                    exit();
                    break;
            }

            //HTML/AJAX経由ではない場合----
        }
        else{
            //----HTML/AJAX経由の場合
            if( $strReqByHA == "forHADAC" ){
                $strOrder = "redirectOrderForHADACClient";
            }
            else{
                $strOrder = "redirectOrderForHAGClient";
            }
            $arrayResult = array($strOrder,$mode,$strRediretTo);
            foreach($aryPostData as $key=>$val){
                $arrayResult[] = $key;
                $arrayResult[] = $val;
            }

            http_response_code(200);
            print makeAjaxProxyResultStream($arrayResult);
            exit();
            //HTML/AJAX経由の場合----
        }
    }

    function web_log($FREE_LOG){
        // グローバル変数の利用宣言
        global $root_dir_path,$p_login_name,$g;
        $aryAppliOrg = array();
        $aryContent = array();
        $aryPickItems = array();

        $strColDelimiter = "\t";
        $strLineDelimiter = "\n";

        $p_LOGIN_ID = "";
        try{
            if ( empty($root_dir_path) ){
                $root_dir_path = getApplicationRootDirPath();
            }
            $lc_root_dir_path = $root_dir_path;

            // ----ログとして出力する項目
            $aryPickItems = array(
                'APP_LOG_PRINT_TIME'=>1,
                'APP_SOURCE_IP'=>1,
                'APP_SOURCE_IP_INFOBASE'=>1,
                'REQUEST_METHOD'=>0,
                'HTTP_HOST'=>0,
                'PHP_SELF'=>0,
                'QUERY_STRING'=>0,
                'APP_LOGIN_ID'=>1,
                'APP_FREE_LOG'=>1
            );
            // ログとして出力する項目----

            // ----アクセス元IPを準備
            $tmpAryIPInfo = getSourceIPAddress(false);
            $aryAppliOrg['APP_SOURCE_IP'] = $tmpAryIPInfo[0];
            $aryAppliOrg['APP_SOURCE_IP_INFOBASE'] = $tmpAryIPInfo[1];
            unset($tmpArray);

            // ----ログインIDを準備
            if ( isset($p_login_name) ){
                $p_LOGIN_ID = $p_login_name;
            }
            else{
                if ( isset($g['login_name']) ){
                    $p_LOGIN_ID = $g['login_name'];
                }
                else{
                    $p_LOGIN_ID = "";
                }
            }
            $aryAppliOrg['APP_LOGIN_ID'] = $p_LOGIN_ID;
            // ログインIDを準備----

            // ----フリーログを準備
            if ( isset($FREE_LOG) ){
                $aryAppliOrg['APP_FREE_LOG'] = $FREE_LOG;
            }
            // フリーログを準備----

            // ----ログ出力時刻
            $tmpTimeStamp = time();
            $logtime = date("Y/m/d H:i:s",$tmpTimeStamp);
            $aryAppliOrg['APP_LOG_PRINT_TIME'] = $logtime;
            // ログ出力時刻----

            $intElementLength1 = count($aryPickItems);
            $intElementCount1 = 0;
            foreach( $aryPickItems as $strKey=>$intVal ){
                $aryBottomElement = array();
                $intElementCount1 += 1;
                $varAddElement = "";
                $strFocusElement = "";
                if( $intVal == 0 ){
                    $varAddElement = isset($_SERVER[$strKey])?$_SERVER[$strKey]:"";
                }
                else{
                    $varAddElement = isset($aryAppliOrg[$strKey])?$aryAppliOrg[$strKey]:"";
                }
                if( is_array($varAddElement) === false ){
                    $aryBottomElement = array();
                    if( is_string($varAddElement)===true ){
                        $aryBottomElement = array($varAddElement);
                    }
                }
                else{
                    $aryBottomElement = $varAddElement;
                }
                
                $intElementLength2 = count($aryBottomElement);
                $intElementCount2 = 0;
                
                foreach( $aryBottomElement as $strAddElement ){
                    $intElementCount2 += 1;
                    if( $intElementLength2 == $intElementCount2 ){
                        $strAddElement = "\"{$strAddElement}\"";
                    }
                    else{
                        $strAddElement = "\"{$strAddElement}\"{$strColDelimiter}";
                    }
                    $aryContent[] = $strAddElement;
                }
                if( $intElementLength1 != $intElementCount1 ){
                    $aryContent[] = $strColDelimiter;
                }
                else{
                    $aryContent[] = $strLineDelimiter;
                }
            }

            $set_dir_path = $lc_root_dir_path . "/logs/webaplogs";
            
            if( is_dir($set_dir_path) === false ){
                // 例外処理へ
                throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $filepointer = @fopen(  $lc_root_dir_path . "/logs/webaplogs/webap_" . date("Ymd", $tmpTimeStamp) . ".log", "a");
            if( @flock($filepointer, LOCK_EX) === false ){
                // 例外処理へ
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            foreach( $aryContent as $value ){
                if( @fputs($filepointer, $value) === false ){
                     // 例外処理へ
                     throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }            
            }
            if( @flock($filepointer, LOCK_UN) === false ){
                // 例外処理へ
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            if( @fclose($filepointer) === false ){
                // 例外処理へ
                throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        catch (Exception $e){
            $textBody = implode($aryContent,"");
            syslog(LOG_CRIT,"Web_log error is occured on directory [{$set_dir_path}]. Wev_log text is [{$textBody}].");
            exit();
        }
    }

    function getSourceIPAddress($boolValueForIpCheck=true){
        //----ipv4のみ(
        //----XFFに基本的にはある、というスタンス。その他を調べるのはオマケ
        $strPattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
        $retVarValue = "";
        $p_SOURCE_IP = "";
        $aryRemoteAddressInfo = array();

        // 8項目
        $aryCheckKey = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_VIA',
            'HTTP_SP_HOST',
            'HTTP_FORWARDED',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        );

        foreach($aryCheckKey as $strFocusCheckKey){
            $strTmpValue = "";
            if( array_key_exists($strFocusCheckKey, $_SERVER ) ){
                $strTmpValue = htmlspecialchars($_SERVER[$strFocusCheckKey], ENT_QUOTES, "UTF-8");
                $aryExploded = explode(",", $strTmpValue);
                $strCheckValue = $aryExploded[0];
                $strCheckValue = str_replace(" ","", $strCheckValue);
                if( preg_match($strPattern, $strCheckValue)===1 ){
                    if($p_SOURCE_IP == "" ){
                        $p_SOURCE_IP = $strCheckValue;
                    }
                    //break;
                }
            }
            $aryRemoteAddressInfo[] = $strTmpValue;
        }
        if( $boolValueForIpCheck === false ){
            // ----ログ用
            $retVarValue = array();
            $retVarValue[0] = $p_SOURCE_IP;
            $retVarValue[1] = $aryRemoteAddressInfo;
            // ログ用----
        }
        else{
            // ----IPチェック用
            $retVarValue = $p_SOURCE_IP;
            // IPチェック用----
        }
        return $retVarValue;
    }

    function error_log_wrapper($strErrorBody="", $speFILE="", $speLINE="", $arrayErrorBodyHead = array()){
         // グローバル変数の利用宣言
         global $g;
         if( isset($g['login_id']) === true ){
             $arrayErrorBodyHead[] .= "(login_id:=".$g['login_id'].")";
         }
         if( $speFILE != ""){
             $arrayErrorBodyHead[] = "(Source:=".$speFILE.")";
             if( $speLINE != ""){
                 $arrayErrorBodyHead[] = "(Line:=".$speLINE.")";
             }
         }
         $strErrorBodyHead = implode(".",$arrayErrorBodyHead);
         if( $strErrorBodyHead != "" ){
             $strErrorBodyHead .= ":";
         }
         $strErrorStream = $strErrorBodyHead.$strErrorBody;
         error_log($strErrorStream);
         return $strErrorStream;
    }

    // ----ここから業務色を排除した汎用系関数

    function getRequestProtocol() {
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) or isset($_SERVER['HTTP_X_FORWARDED_PROTO']) or isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // ---- リバースPROXY経由のリクエスト
            $lcStrProtocol = 'https://';  // defaultはhttpsとする
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'http' or $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                    $lcStrProtocol = htmlspecialchars($_SERVER['HTTP_X_FORWARDED_PROTO'], ENT_QUOTES, "UTF-8").'://';
                }
            }
            // リバースPROXY経由のリクエスト ----
        } else {
            // ---- 直接リクエスト
            if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] === 'on') {
                $lcStrProtocol = 'https://';
            } else {
                $lcStrProtocol = 'http://';
            }
            // 直接リクエスト ----
        }
        return $lcStrProtocol;
    }

    function getRequestHost() {
        $lcStrHost = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            // ---- リバースPROXY経由のリクエスト
            $lcStrHost = htmlspecialchars($_SERVER['HTTP_X_FORWARDED_HOST'], ENT_QUOTES, "UTF-8");
            // リバースPROXY経由のリクエスト ----
        } else {
            // ---- 直接リクエスト
            $lcStrHost = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, "UTF-8");
            // 直接リクエスト ----
        }
        return $lcStrHost;
    }

    function getSchemeNAuthority(){
        // URI部分を省略するために空を返却
        return '';
    }

    function ky_printHeaderForProvideBinaryStream($strProvideFilename,$strContentType="",$varContentLength=null,$boolFileNameUTF8=true){
        if( $boolFileNameUTF8 === true ){
            //----RFC6266が適用されたブラウザのみ有効
            $strCDABody = 'Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode($strProvideFilename);
        }
        else{
            $strCDABody = 'Content-Disposition: attachment; filename="'.$strProvideFilename.'"';
        }
        // 標準ヘッダー出力
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Description: File Transfer');
        header('Content-Type: '.$strContentType);
        header($strCDABody);
        header('Content-Transfer-Encoding: binary');
        if( $varContentLength === null ){
            // なにもしない
        }
        else{
            header('Content-Length: '.$varContentLength);
        }
        header("Cache-Control: public");
        header("Pragma: public");
    }

    // Webからtail -fできるファンクション
    function read_tail($file, $lines){
        $handle = fopen($file, "r");
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = array();
        while ($linecounter> 0){
            $t = " ";
            while ($t != "\n"){
                if(fseek($handle, $pos, SEEK_END) == -1){
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos --;
            }
            $linecounter --;
            if ($beginning){
                rewind($handle);
            }
            $text[$lines-$linecounter-1] = fgets($handle);
            if ($beginning) break;
        }
        fclose ($handle);
        return array_reverse($text);
    }

    // ここまで業務色を排除した汎用系関数----

// ----RBAC対応 
//////////////////////////////////////////////////////////////////////
//
//【概要】
//  ロールアクセス権の判定
//
//////////////////////////////////////////////////////////////////////
class RoleBasedAccessControl {
   private  $role_account_link_list;
   private  $objDBCA;
   private  $DefaultAccessRoles;
   private  $AccessRoles;
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   コンストラクタ
   // 【パラメータ】
   //   $objDBCA: DBアクセスクラスオブジェクト
   ///////////////////////////////////////////////////////////////////
   function __construct($objDBCA){
       $this->objDBCA                = $objDBCA;
       $this->role_account_link_list = array();
       $this->DefaultAccessRoles     = array();
       $this->AccessRoles            = array();
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定ユーザーのデフォルトアクセス権ロールをCSV文字列で取得
   // 【パラメータ】
   //   $userID:        ログインID
   //   $type:          指定ユーザーのデフォルトアクセス権ロールのCSV文字列タイプ
   //                   ID:ロールID  NAME:ロール名称
   //   $disuse_recode: 廃止レコードの扱い
   //                   true:廃止レコードはID変換エラー(x)として扱う
   //                   false:廃止レコードは無視する。
   // 【戻り値】
   //   false:   異常
   //   他:      指定ユーザーのデフォルトアクセス権ロールのCSV文字列
   //              
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getDefaultAccessRoleString($userID,$type,$disuse_recode) {
       // 廃止されているレコードは除かれる
       $RolesList = $this->getAllRoleList($userID);
       if($RolesList === false) {
           return false;
       }
       $DefaultAccessRoleString = "";
       foreach($RolesList as $Role) {
           if($Role['DEFAULT'] === 'checked') {
               // 廃止ロールの扱い判定
               if(($Role['ROLE_DISUSE_FLAG'] == '1') && ($disuse_recode === false)) {
                   continue;
               } else {
                   if($DefaultAccessRoleString != '')  $DefaultAccessRoleString .= ',';
                   if($type == 'ID') {
                       $DefaultAccessRoleString .= $Role['ROLE_ID'];
                   } else {
                       if($Role['ROLE_DISUSE_FLAG'] == '1') {
                           $rolename = $this->makeDisUseRoleName($Role['ROLE_ID']);
                           $DefaultAccessRoleString .= $rolename;
                       } else {
                           $DefaultAccessRoleString .= $Role['ROLE_NAME'];
                       }
                   }
               }
           }
       }
       return $DefaultAccessRoleString;
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ユーザーに許可されていないロール名か判定しロールIDを取得
   // 【パラメータ】
   //   $RoleName: ロール名
   // 【戻り値】
   //   false:  ユーザーに許可されていないロール名でない
   //   他:     ユーザーに許可されていないロールID
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function chkUnAuthRoleName($RoleName) {
       global $objMTS;
       $UnAuthRoleName = sprintf("/^%s\([0-9]+\)$/", preg_quote($objMTS->getSomeMessage("ITAWDCH-STD-11102")));
       // ロール名がユーザーに許可のないロール(********)か判定
       if(preg_match($UnAuthRoleName,$RoleName) == 1) {
           // 廃止ロール名のIDを取得
           $UnAuthRoleID = str_replace($objMTS->getSomeMessage("ITAWDCH-STD-11102"),'',$RoleName);
           $UnAuthRoleID = str_replace('(','',$UnAuthRoleID);
           $UnAuthRoleID = str_replace(')','',$UnAuthRoleID);
           return $UnAuthRoleID;
       } else {
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   廃止ロール名か判定しロールIDを取得
   // 【パラメータ】
   //   $RoleName: ロール名
   // 【戻り値】
   //   false:  廃止ロール名でない
   //   他:     廃止ロールID
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function chkDisUseRoleName($RoleName) {
       global $objMTS;
       $DisUseRoleName = sprintf("/^%s\([0-9]+\)$/", preg_quote($objMTS->getSomeMessage("ITAWDCH-STD-11101")));
       // ロール名が廃止ロール名(ID変換失敗)か判定
       if(preg_match($DisUseRoleName,$RoleName) == 1) {
           // 廃止ロール名のIDを取得
           $DisUseRoleID = str_replace($objMTS->getSomeMessage("ITAWDCH-STD-11101"),'',$RoleName);
           $DisUseRoleID = str_replace('(','',$DisUseRoleID);
           $DisUseRoleID = str_replace(')','',$DisUseRoleID);
           return $DisUseRoleID;
       } else {
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   廃止ロール名を取得
   // 【パラメータ】
   //   $id:      ロールID
   // 【戻り値】
   //   廃止ロール名
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function makeDisUseRoleName($id) {
       global $objMTS;
       $DisUseRoleName = sprintf("%s(%s)",$objMTS->getSomeMessage("ITAWDCH-STD-11101"),$id);
       return $DisUseRoleName;
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ユーザーに紐づいていないロール名を取得
   // 【パラメータ】
   //   $id:      ロールID
   // 【戻り値】
   //   廃止ロール名
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function makeUnAuthRoleName($id) {
       global $objMTS;
       $DisUseRoleName = sprintf("%s(%s)",$objMTS->getSomeMessage("ITAWDCH-STD-11102"),$id);
       return $DisUseRoleName;
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   該当ユーザーのロール情報(廃止も含む)を取得
   // 【パラメータ】
   //   $userID: 該当ユーザー
   // 【戻り値】
   //   false:   異常
   //   他:      下記ハッシュ配列
   //               "ROLE_ID"=> "1", 
   //               "ROLE_NAME"=> "システム管理者"
   //               "DEFAULT"=> "checked"
   //               "ROLE_DISUSE_FLAG"=>0:有効レコード
   //                                   1:廃止
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getAllRoleList($userID) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_ACCOUNT_LINK_LIST JOIN A_ROLE_LIST user ID:%s)";
       $error_msg2 = "[%s:%s]:Recode not found. (Table:A_ROLE_ACCOUNT_LINK_LIST JOIN A_ROLE_LIST user ID:%s)";
       try {
           $sql  = "SELECT   ";
           $sql .= " TAB_1.ROLE_ID, ";
           $sql .= " TAB_1.DEF_ACCESS_AUTH_FLAG, ";
           $sql .= " TAB_2.ROLE_NAME, ";
           $sql .= " TAB_2.DISUSE_FLAG ";
           $sql .= "FROM ";
           $sql .= " A_ROLE_ACCOUNT_LINK_LIST TAB_1 ";
           $sql .= " LEFT JOIN A_ROLE_LIST    TAB_2 ON (TAB_1.ROLE_ID=TAB_2.ROLE_ID) ";
           $sql .= "WHERE ";
           $sql .= " TAB_1.DISUSE_FLAG='0' AND ";
           $sql .= " TAB_1.USER_ID = {$userID}";
           $sql .= " ORDER BY ROLE_ID ";
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $objQuery->sqlBind( array('USER_ID'=>$userID));
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $user_role_list = array();
           if($objQuery->effectedRowCount() == 0) {
               // ロール・ユーザー紐づけにデータ未登録の場合
               $message = sprintf($error_msg2,basename(__FILE__),__LINE__,$userID);
               throw new Exception($message);
           }
           while($row = $objQuery->resultFetch()) {
               $array = array();
               $array["ROLE_ID"]     = $row["ROLE_ID"];
               $array["ROLE_NAME"]   = $row["ROLE_NAME"];
               if($row["DISUSE_FLAG"] == '0') {
                   $array["ROLE_DISUSE_FLAG"] = '0';
               } else {
                   $array["ROLE_DISUSE_FLAG"] = '1';
               }
               if($row["DEF_ACCESS_AUTH_FLAG"] == "1") {
                   $array["DEFAULT"]   = "checked";
               } else {
                   $array["DEFAULT"]   = "";
               }
               $user_role_list[] = $array;
           }
           unset($objQuery);
           return $user_role_list;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ロール名のCSV文字列をロールIDのCSV文字列に変換
   //   ID変換失敗ロールを有効なロールとして扱うかを指定する。
   //   一覧表示や変更履歴の表示は、ID変換失敗ロールを有効なロールとして扱う
   // 【パラメータ】
   //   $userID:          ログインID
   //   $RoleNameString:  ロール名のCSV文字列
   //   $disuse_role:     ID変換失敗ロールを有効なロールとして扱うかを指定する。
   //                     true:ID変換失敗(x)のロール名を有効ロールとして扱う
   //                          ロールIDをxとして扱う
   //                     false:D変換失敗(x)のロール名を無視する
   //   $Convert_error_char: 表示フィルター検索時に使用
   //                        ロール名からIDに変換できなかった場合のロール名
   // 【戻り値】
   //   false:   異常
   //   他:      ロールIDのCSV文字列
   //              
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getRoleNameStringToRoleIDString($userID,$RoleNameString,$disuse_role,$Convert_error_char="") {
       $RoleID2Name = array();
       $RoleName2ID = array();
       // 廃止されているレコードは除かれる
       $ret = $this->getRoleSearchHashList($userID,$RoleID2Name,$RoleName2ID);
       if($ret === false) {
           return false;
       }
       $RoleIDString = "";
       // ロール名をロールIDに置換
       if(strlen($RoleNameString) != 0) {
           $updRoleNamelist = explode(',',$RoleNameString);
           foreach($updRoleNamelist as $updRoleName) {
               if(array_key_exists($updRoleName,$RoleName2ID)) {
                   if($RoleIDString != '') { $RoleIDString .= ',';}
                   $RoleIDString .= $RoleName2ID[$updRoleName];
               } else {
                   // ユーザーに許可のないロール名か判定
                   $UnAuthRoleIDString = $this->chkUnAuthRoleName($updRoleName);
                   if($UnAuthRoleIDString !== false) {
                       if($RoleIDString != '') { $RoleIDString .= ',';}
                       $RoleIDString .= $UnAuthRoleIDString;
                   } else { 
                       // ロール名をロールIDに置換した文字列はUI表示用の処理なので
                       // ロール名が廃止を含めてロール管理に登録されているかは判定はしない。
                       // 廃止ロール名を扱うか判定
                       if($disuse_role === true) {
                           // 廃止ロール名か判定
                           $DisUserRoleIDString = $this->chkDisUseRoleName($updRoleName);
                           if($DisUserRoleIDString !== false) {
                               if($RoleIDString != '') { $RoleIDString .= ',';}
                               $RoleIDString .= $DisUserRoleIDString;
                           } else {
                               // 廃止ロール名が不正の場合
                               $error_msg1 = "[%s:%s]:Role Name Failed.(Name:%s)";
                               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$updRoleName);
                               if(function_exists("web_log")) {
                                   web_log($message);
                               } else {
                                   error_log($message);
                               }
                               if($Convert_error_char != "") {
                                   if($RoleIDString != '') { $RoleIDString .= ',';}
                                   $RoleIDString .= $Convert_error_char;
                               }
                           }
                       }
                   }
               }
           }
       }
       return $RoleIDString;
   }

   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   登録・更新用
   //   ロールID(CSV文字列)妥当性をチェックする
   //   ID変換失敗ロールは無視
   // 【パラメータ】
   //   $userID:          ログインID
   //   $ordMode:          登録種別
   //                      0:[ブラウザからの新規登録
   //                      1:[EXCEL]からの新規登録
   //                      2:[CSV]からの新規登録
   //                      3:[JSON]からの新規登録
   //                      4:[ブラウザからの新規登録(トランザクション無)
   //   $RoleIDString:     ロールIDのCSV文字列
   //   $ErrorRoleNameAry: 変換できなかったロール名配列
   //
   // 【戻り値】
   //   false:   異常
   //            $ErrorRoleNameAryに不正なRoleIDがロール名配列が設定される。
   //   他:      ロールIDのCSV文字列
   //
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function chkRoleIDStringForDBUpdate($userID,$ordMode,$RoleIDString,&$ErrorRoleNameAry) {
       $ErrorRoleNameAry = array();
       $RoleID2Name = array();
       $RoleName2ID = array();
       // 廃止されているレコードは除かれる
       $ret = $this->getRoleSearchHashList($userID,$RoleID2Name,$RoleName2ID);
       if($ret === false) {
           return false;
       }
       $AllRoleID2Name = array();
       $AllRoleName2ID = array();
       // 廃止されているレコードを含む
       $ret = $this->getAllRoleSearchHashList($userID,$AllRoleID2Name,$AllRoleName2ID);
       if($ret === false) {
           return false;
       }
       $makeRoleIDString = "";
       // ロール名をロールIDに置換
       if(strlen($RoleIDString) != 0) {
           $updRoleIDlist = explode(',',$RoleIDString);
           foreach($updRoleIDlist as $updRoleID) {
               if(array_key_exists($updRoleID,$RoleID2Name)) {
                   if($makeRoleIDString != '') { $makeRoleIDString .= ',';}
                   $makeRoleIDString .= $updRoleID;
               } else {
                   // 登録種別がExcel/CSV/Restの場合、ユーザーに紐づいているロール以外はエラーとして扱う
                   if(($ordMode == '1') || ($ordMode == '2') || ($ordMode == '3')) {
                       $ErrorRoleNameAry[] = $updRoleID;
                       continue;
                   }
                   // ユーザーに許可のないロール名/廃止ロール名か判定
                   if(array_key_exists($updRoleID,$AllRoleID2Name)) {
                       // 廃止されていないロールの場合、ユーザーに許可のないロールとして扱う
                       if($AllRoleID2Name[$updRoleID]['DISUSE_FLAG'] == '0') {
                           if($makeRoleIDString != '') { $makeRoleIDString .= ',';}
                           $makeRoleIDString .= $updRoleID;
                       } else {
                           // 廃止ロール名はカット
                       }
                       continue;
                   } else {
                       $ErrorRoleNameAry[] = $updRoleID;
                   }
               }
           }
       }
       if(count($ErrorRoleNameAry) == 0) {
           return $makeRoleIDString;
       } else {
           return false;
       }
   }

   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   Excel/Rest更新・廃止・復活時
   //   該当レコードのアクセス許可ロールとログインユーザのアクセス許可
   //   ロールが適合しているか判定する。
   // 【パラメータ】
   //   $userID:           ログインID
   //   $RoleIDString:     ロールIDのCSV文字列(該当レコードのアクセス許可ロール)
   //
   // 【戻り値】
   //   true:    正常
   //   false:   異常
   //
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function chkLoginUserAccessAuthForTargetRecodeAccessAuth($userID,$RoleIDString) {
       $ErrorRoleNameAry = array();
       $RoleID2Name = array();
       $RoleName2ID = array();
       // 廃止されているレコードは除かれる
       $ret = $this->getRoleSearchHashList($userID,$RoleID2Name,$RoleName2ID);
       if($ret === false) {
           return false;
       }
       $makeRoleIDString = "";
       // ロール名をロールIDに置換
       if(strlen($RoleIDString) != 0) {
           $nowRoleIDlist = explode(',',$RoleIDString);
           foreach($nowRoleIDlist as $nowRoleID) {
               if(array_key_exists($nowRoleID,$RoleID2Name)) {
                   return true;
               } else {
                   continue;
               }
           }
       } else {
           return true;
       }
       return false;
   }

   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   登録・更新用
   //   ロール名のCSV文字列をロールIDのCSV文字列に変換
   //   ID変換失敗ロールは無視
   // 【パラメータ】
   //   $userID:          ログインID
   //   $ordMode:          登録種別
   //                      0:[ブラウザからの新規登録
   //                      1:[EXCEL]からの新規登録
   //                      2:[CSV]からの新規登録
   //                      3:[JSON]からの新規登録
   //                      4:[ブラウザからの新規登録(トランザクション無)
   //   $modeValue:       DBアクセスモード
   //                     DTUP_singleRecUpdate: 更新
   //                     DTUP_singleRecRegister: 登録
   //                     DTUP_singleRecDelete:   廃止・復活
   //   $RoleNameString:  ロール名のCSV文字列
   //   $TargetRecodeRoleIDString:   ロールIDのCSV文字列(該当レコードのアクセス許可ロール)
   //   $ErrorRoleNameAry: 変換できなかったロール名配列
   //
   // 【戻り値】
   //   false:   異常
   //            $ErrorRoleNameAryに変換出来なかったロール名配列が設定される。
   //   他:      ロールIDのCSV文字列
   //              
   // 【備考】
   ///////////////////////////////////////////////////////////////////
   function getRoleNameStringToRoleIDStringForDBUpdate($userID,$ordMode,$modeValue,$RoleNameString,$TargetRecodeRoleIDString,&$ErrorRoleNameAry) {
       $ErrorRoleNameAry = array();
       $RoleID2Name = array();
       $RoleName2ID = array();
       
       // 該当レコードのアクセス許可ロールを配列化
       $TargetRecodeRoleIDList =  explode(',',$TargetRecodeRoleIDString);

       // 廃止されているレコードは除かれる
       $ret = $this->getRoleSearchHashList($userID,$RoleID2Name,$RoleName2ID);
       if($ret === false) {
           return false;
       }
       // 廃止されているレコードを含む
       $AllRoleID2Name = array();
       $AllRoleName2ID = array();
       $dummy_userID = 0;  // getAllRoleSearchHashListで$userIDは未使用
       $ret = $this->getAllRoleSearchHashList($dummy_userID,$AllRoleID2Name,$AllRoleName2ID);
       if($ret === false) {
           return false;
       }
       $RoleIDString = "";
       // ロール名をロールIDに置換
       if(strlen($RoleNameString) != 0) {
           $updRoleNamelist = explode(',',$RoleNameString);
           foreach($updRoleNamelist as $updRoleName) {
               if(array_key_exists($updRoleName,$RoleName2ID)) {
                   if($RoleIDString != '') { $RoleIDString .= ',';}
                   $RoleIDString .= $RoleName2ID[$updRoleName];
               } else {
                   // ユーザーに許可のないロール名か判定
                   $UnAuthRoleIDString = $this->chkUnAuthRoleName($updRoleName);
                   if($UnAuthRoleIDString !== false) {
                       $ErrorRoleName = true;
                       // 登録種別がExcel/CSV/Restの場合、ロールIDが有効で紐づいていないロールか判定
                       if(($ordMode == '1') || ($ordMode == '2') || ($ordMode == '3')) {
                           // ユーザーに紐づいていないロールIDでも、ターゲットレコードのアクセス許可ロールに元々設定されているロールか判定
                           // 複数ユーザーで共有されているレコードの場合を想定
                           if(array_key_exists($UnAuthRoleIDString,$RoleID2Name) === false) {
                               // 更新の場合
                               if($modeValue == "DTUP_singleRecUpdate") {
                                   // ユーザーに紐づいていない、ターゲットレコードのアクセス許可ロールに元々設定されているロールでもない
                                   if(array_search($UnAuthRoleIDString,$TargetRecodeRoleIDList) === false) {
                                       $ErrorRoleName = false;
                                   }
                               } else {
                               // ユーザーに紐づいていないロールIDならエラー
                               
                                   $ErrorRoleName = false;
                               }
                           } else {
                               $ErrorRoleName = false;
                           }
                       }
                       if($ErrorRoleName === false) {
                           $ErrorRoleNameAry[] = $updRoleName;
                           continue;
                       } else {
                           if($RoleIDString != '') { $RoleIDString .= ',';}
                           $RoleIDString .= $UnAuthRoleIDString;
                           continue;
                       }
                   } 
                   // 廃止ロール名か判定
                   $ErrorRoleName = true;
                   $DisUserRoleIDString = $this->chkDisUseRoleName($updRoleName);
                   if($DisUserRoleIDString !== false) {
                       // 登録種別がExcel/CSV/Restの場合
                       if(($ordMode == '1') || ($ordMode == '2') || ($ordMode == '3')) {
                           // ロール名とロールIDの適合を判定
                           // ロール名がID変換エラーの場合、ロールIDも廃止か未登録かを判定
                           // ユーザーに紐づいているロールIDか判定
                           if(array_key_exists($DisUserRoleIDString,$RoleID2Name)===false) {
                               // ユーザーに紐づいているロールIDならエラー
                               $ErrorRoleName = false;
                           } else {
                               if(array_key_exists($DisUserRoleIDString,$AllRoleID2Name)) {
                                   // 廃止ロールIDか判定
                                   if($AllRoleID2Name[$DisUserRoleIDString]['DISUSE_FLAG'] == '1') {
                                       // 廃止されているロールID
                                   } else {
                                       // 有効なロールIDなのでロール名エラー
                                       $ErrorRoleName = false;
                                   }
                               } else {
                                   // 未登録のロールIDの場合、廃止ロールとして扱う
                               }
                           }
                       }
                   } else {
                       // ロール名が不正
                       $ErrorRoleName = false;
                   }
                   if($ErrorRoleName === false) {
                       $ErrorRoleNameAry[] = $updRoleName;
                       continue;
                   } else {
                       // 廃止ロール名はカット
                       continue;
                   }
               }
           }
       }
       if(count($ErrorRoleNameAry) == 0) {
           return $RoleIDString;
       } else {
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ロールIDのCSV文字列をロール名称のCSV文字列に変換
   //   廃止されているロールをID変換失敗ロールとして扱うかを指定する。
   //   プルダウン検索の表示は、廃止されているロールをID変換失敗ロールとして扱う
   // 【パラメータ】
   //   $userID:        ユーザーID
   //   $RoleIDString:  ロールIDのCSV文字列
   //   $disuse_role:   廃止されているロールをID変換失敗ロールとして扱うかを指定する。
   //                     true:廃止されているロールIDを変換失敗(x)として扱う
   //                          ロールIDをxとして扱う
   //                     false:廃止されているロールIDを無視する。
   // 【戻り値】
   //   false:   異常
   //   他:      ロールCSVのCSV文字列
   //              
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getRoleIDStringToRoleNameString($userID,$RoleIDString,$disuse_role) {
        $RoleID2Name = array();
        $RoleName2ID = array();

        // ユーザIDが空の場合、空を返す
        if("" == $userID){
            return "";
        }

        // 廃止されているレコードを含む
        $ret = $this->getAllRoleSearchHashList($userID,$AllRoleID2Name,$AllRoleName2ID);
        if($ret === false) {
            return false;
        }
        // 廃止されているレコードは除かれる
        $ret = $this->getRoleSearchHashList($userID,$RoleID2Name,$RoleName2ID);
        if($ret === false) {
            return false;
        }
        $RoleNameString = "";
        // ロールIDをロール名称に置換
        if(strlen($RoleIDString) != 0) {
            $updRoleIDlist = explode(',',$RoleIDString);
            foreach($updRoleIDlist as $updRoleID) {
                if(array_key_exists($updRoleID,$RoleID2Name)) {
                    if($RoleNameString != '') { $RoleNameString .= ',';}
                    $RoleNameString .= $RoleID2Name[$updRoleID];
                } else {
                    // ロールIDからロール名に置換した文字列がDB登録用になるので
                    // ロールIDが廃止も含めてロール管理にあるか判定
                    if(array_key_exists($updRoleID,$AllRoleID2Name)) {
                        // 廃止されていないロールの場合、ユーザーに許可のないロールとして扱う
                        if($AllRoleID2Name[$updRoleID]['DISUSE_FLAG'] == '0') {
                            $UnAuthRoleName = $this->makeUnAuthRoleName($updRoleID);
                            if($RoleNameString != '') { $RoleNameString .= ',';}
                            $RoleNameString .= $UnAuthRoleName;
                            
                            continue;
                        }
                    } 
                    // 廃止ロールを扱うか判定
                    if($disuse_role === true) {
                        $DisUseRoleName = $this->makeDisUseRoleName($updRoleID);
                        if($RoleNameString != '') { $RoleNameString .= ',';}
                        $RoleNameString .= $DisUseRoleName;
                    } else {
                        // 不正なロールIDの場合はエラーにする。
                        $error_msg1 = "[%s:%s]:Role ID Failed.(ID:%s)";
                        $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$updRoleID);
                        if(function_exists("web_log")) {
                            web_log($message);
                        } else {
                            error_log($message);
                        }
                    }
                }
            }
        }
        return $RoleNameString;
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ロール情報を取得(廃止も含む)
   // 【パラメータ】
   //   $userID:        ユーザーID
   //   $RoleID2Name:   $RoleID2Name[ロールID] = array('ROLE_ID'=>'' ,'ROLE_NAME'=>'' ,DISUSE_FLAG=>'' )
   //   $RoleName2ID:   $RoleName2ID[ロール名] = array('ROLE_ID'=>'' ,'ROLE_NAME'=>'' ,DISUSE_FLAG=>'' )
   // 【戻り値】
   //   false:   異常
   //   itrue:   正常
   //
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getAllRoleSearchHashList($userID,&$RoleID2Name,&$RoleName2ID) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_LIST user ID:%s)";
       $RoleID2Name = array();
       $RoleName2ID = array();
       try {
           // ロール名やロールIDを取得
           $sql  = "SELECT   ";
           $sql .= " TAB_1.ROLE_ID, ";
           $sql .= " TAB_1.ROLE_NAME, ";
           $sql .= " TAB_1.DISUSE_FLAG ";
           $sql .= "FROM ";
           $sql .= " A_ROLE_LIST TAB_1 ";
           //$sql .= " A_ROLE_ACCOUNT_LINK_LIST TAB_1 ";
           //$sql .= " LEFT JOIN A_ROLE_LIST    TAB_2 ON (TAB_1.ROLE_ID=TAB_2.ROLE_ID) ";
           //$sql .= "WHERE ";
           //$sql .= " TAB_1.USER_ID = {$userID}";
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
//           $objQuery->sqlBind( array('USER_ID'=>$userID));
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           while($row = $objQuery->resultFetch()) {
               $RoleID2Name[$row['ROLE_ID']]   = $row;
               $RoleName2ID[$row['ROLE_NAME']] = $row;
           }
           unset($objQuery);
           return true;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ロール情報を取得
   // 【パラメータ】
   //   $userID:        ユーザーID
   //   $RoleID2Name:   $RoleID2Name[ロールID] = ロール名称
   //   $RoleName2ID:   $RoleName2ID[ロール名] = ロールID
   // 【戻り値】
   //   false:   異常
   //   itrue:   正常
   //
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getRoleSearchHashList($userID,&$RoleID2Name,&$RoleName2ID) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_ACCOUNT_LINK_LIST JOIN A_ROLE_LIST user ID:%s)";
       $RoleID2Name = array();
       $RoleName2ID = array();
       try {
           // ロール名やロールIDを取得
           $sql  = "SELECT   ";
           $sql .= " TAB_1.ROLE_ID, ";
           $sql .= " TAB_1.DEF_ACCESS_AUTH_FLAG, ";
           $sql .= " TAB_2.ROLE_NAME ";
           $sql .= "FROM ";
           $sql .= " A_ROLE_ACCOUNT_LINK_LIST TAB_1 ";
           $sql .= " LEFT JOIN A_ROLE_LIST    TAB_2 ON (TAB_1.ROLE_ID=TAB_2.ROLE_ID) ";
           $sql .= "WHERE ";
           $sql .= " TAB_1.DISUSE_FLAG='0' AND ";
           $sql .= " TAB_2.DISUSE_FLAG='0' AND ";
           $sql .= " TAB_1.USER_ID = {$userID}";
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $objQuery->sqlBind( array('USER_ID'=>$userID));
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           while($row = $objQuery->resultFetch()) {
               $RoleID2Name[$row['ROLE_ID']]   = $row['ROLE_NAME'];
               $RoleName2ID[$row['ROLE_NAME']] = $row['ROLE_ID'];
           }
           unset($objQuery);
           return true;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   該当ユーザーのロール情報を取得
   //   アクセス権ロール設定モーダル用
   // 【パラメータ】
   //   $userID: 該当ユーザー
   // 【戻り値】
   //   false:   異常
   //   他:      下記ハッシュ配列
   //               "ROLE_ID"=> "1", "ROLE_NAME"=> "システム管理者", "DEFAULT"=> "checked"
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getUserRoleList($userID) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_ACCOUNT_LINK_LIST JOIN A_ROLE_LIST user ID:%s)";
       $error_msg2 = "[%s:%s]:Recode not found. (Table:A_ROLE_ACCOUNT_LINK_LIST JOIN A_ROLE_LIST user ID:%s)";
       try {
           $sql  = "SELECT   ";
           $sql .= " TAB_1.ROLE_ID, ";
           $sql .= " TAB_1.DEF_ACCESS_AUTH_FLAG, ";
           $sql .= " TAB_2.ROLE_NAME ";
           $sql .= "FROM ";
           $sql .= " A_ROLE_ACCOUNT_LINK_LIST TAB_1 ";
           $sql .= " LEFT JOIN A_ROLE_LIST    TAB_2 ON (TAB_1.ROLE_ID=TAB_2.ROLE_ID) ";
           $sql .= "WHERE ";
           $sql .= " TAB_1.DISUSE_FLAG='0' AND ";
           $sql .= " TAB_2.DISUSE_FLAG='0' AND ";
           $sql .= " TAB_1.USER_ID = {$userID}";
           $sql .= " ORDER bY ROLE_ID ";
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $objQuery->sqlBind( array('USER_ID'=>$userID));
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $user_role_list = array();
           if($objQuery->effectedRowCount() == 0) {
               // ロール・ユーザー紐づけにデータ未登録の場合
               $message = sprintf($error_msg2,basename(__FILE__),__LINE__,$userID);
               throw new Exception($message);
           }
           while($row = $objQuery->resultFetch()) {
               $array = array();
               $array["ROLE_ID"]   = $row["ROLE_ID"];
               $array["ROLE_NAME"] = $row["ROLE_NAME"];
               if($row["DEF_ACCESS_AUTH_FLAG"] == "1") {
                   $array["DEFAULT"]   = "checked";
               } else {
                   $array["DEFAULT"]   = "";
               }
               $user_role_list[] = $array;
           }
           unset($objQuery);
           return $user_role_list;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   該当ユーザーのロール・ユーザー紐づけの情報を取得
   // 【パラメータ】
   //   $userID: 絞り込み対象のユーザーID
   //            一部例外を除きログインユーザーID $g['login_id'];
   // 【戻り値】
   //   true:    正常
   //   false:   異常
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getAccountInfo($userID) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_ACCOUNT_LINK_LIST user ID:%s)";
       $error_msg2 = "[%s:%s]:Recode not found. (Table:A_ROLE_ACCOUNT_LINK_LIST user ID:%s)";
        try {

            //ユーザIDが空の場合、trueを返す
            if("" == $userID){
                return true;
            }

            $sql = "SELECT   ";
            $sql .= " TAB_1.ROLE_ID, ";
            $sql .= " TAB_1.DEF_ACCESS_AUTH_FLAG, ";
            $sql .= " TAB_2.ROLE_NAME ";
            $sql .= "FROM ";
            $sql .= " A_ROLE_ACCOUNT_LINK_LIST TAB_1 ";
            $sql .= " LEFT JOIN A_ROLE_LIST    TAB_2 ON (TAB_1.ROLE_ID=TAB_2.ROLE_ID) ";
            $sql .= "WHERE ";
            $sql .= " TAB_1.DISUSE_FLAG='0' AND ";
            $sql .= " TAB_2.DISUSE_FLAG='0' AND ";
            $sql .= " TAB_1.USER_ID = {$userID}";
            $objQuery = $this->objDBCA->sqlPrepare($sql);
            if($objQuery->getStatus()===false){
                $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
                $message .= "\n" . $objQuery->getLastError();
                throw new Exception($message);
            }
            $objQuery->sqlBind( array('USER_ID'=>$userID));
            $r = $objQuery->sqlExecute();
            if(!$r) {
                $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$userID);
                $message .= "\n" . $objQuery->getLastError();
                throw new Exception($message);
            }
            if($objQuery->effectedRowCount() == 0) {
                // ロール・ユーザー紐づけにデータ未登録の場合
                $message = sprintf($error_msg2,basename(__FILE__),__LINE__,$userID);
                throw new Exception($message);
            }
            $this->role_account_link_list = array();
            // getDefaultAccessRolesで使用
            $this->DefaultAccessRoles = array();
            // chkOneRecodeAccessPermissionで使用
            $this->AccessRoles = array();
            while($row = $objQuery->resultFetch()) {
                $role_account_link_list[] = $row;
                // デフォルトアクセス権が設定されているRoleIDのリストを作成
                if($row["DEF_ACCESS_AUTH_FLAG"] == "1") {
                    // デフォルトアクセスロールを配列を生成
                    $this->DefaultAccessRoles[] = $row["ROLE_ID"];
                }
                // アクセス権のあるRoleIDを退避
                $this->AccessRoles[$row["ROLE_ID"]] = 0;
            }
            unset($objQuery);
            return true;
        }catch (Exception $e){
            // Webかバックヤードかを判定
            if(function_exists("web_log")) {
                web_log($e->getMessage());
            } else {
                error_log($e->getMessage());
            }
            return false;
        }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   getAccountInfoで指定したユーザーのデフォルトアクセス権取得(RoleID)を
   //  ロール・ユーザー紐づけより取得しカンマ区切りの文字列で返却
   // 【パラメータ】
   //   なし
   // 【戻り値】
   //   該当ユーザーのデフォルトアクセス権取得(RoleID)をカンマ区切りの文字列で返却
   // 【備考】
   //   getAccountInfoを呼び出し後に呼び出す。
   //   パックヤードからの場合、異常の場合など、phpのerror_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getDefaultAccessRoles() {
       return(implode(",",$this->DefaultAccessRoles));
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定レコードの複数のアクセス権と該当ユーザーのロールけユーザー紐づけに紐づいているロール
   //   が一致しているか判定(表示対象の有無を判定)
   //   IDColumn項目の表示リスト用
   // 【パラメータ】
   //   $chkRow: $objQuery->sqlExecute()などのselect結果を1レコード毎に指定する。
   // 【戻り値】
   //   戻り値1
   //     true:    正常
   //     false:   異常
   //   戻り値2
   //     true:    表示対象
   //     false:   異常
   // 【備考】
   //   getAccountInfoを呼び出し後に呼び出す。
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function chkOneRecodeMultiAccessPermission($chkRow) {
       $ret = true;
       try {
           $AccessAuthColumnName = "ACCESS_AUTH";
           $matchStr = sprintf("/^((%s)|(%s_[0-9][0-9]))$/",$AccessAuthColumnName,$AccessAuthColumnName);
           $permission = true;        
           foreach($chkRow as $Colum=>$Value) {
               // ACCESS_AUTH/ACCESS_AUTH_99のカラムか判定
               if( ! preg_match($matchStr, $Colum)) {
                   continue;
               } else {
                   $permission = false;
                   // アクセス権が空の場合
                   if(strlen($Value) == 0) {
                       // アクセス権あり
                       $permission = true;        
                   } else {
                       $access_auth_arry = explode(',', $Value);
                       foreach($access_auth_arry as $access_role) {
                           if(array_key_exists($access_role,$this->AccessRoles) === true) {
                               // アクセス権あり
                               $permission = true;        
                               break;
                           }
                       }
                   }
                   // アクセス権なし
                   if($permission == false) {
                       return [$ret, $permission]; 
                   }
               }
           }
           // アクセス権あり
           $permission = true;        
           return [$ret, $permission]; 
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           $ret        = false;
           $permission = false;
           return [$ret, $permission]; 
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定レコードのアクセス権と該当ユーザーのロールけユーザー紐づけに紐づいているロール
   //   が一致しているか判定(表示対象の有無を判定)
   // 【パラメータ】
   //   $chkRow: $objQuery->sqlExecute()などのselect結果を1レコード毎に指定する。
   //            レコードにACCESS_AUTHがあるのが前提
   // 【戻り値】
   //   戻り値1
   //     true:    正常
   //     false:   異常
   //   戻り値2
   //     true:    表示対象
   //     false:   異常
   // 【備考】
   //   getAccountInfoを呼び出し後に呼び出す。
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function chkOneRecodeAccessPermission($chkRow) {
       $ret = true;
       $error_msg = "[%s:%s]:ACCESS_AUTH Column not found.";
       try {
           if(array_key_exists("ACCESS_AUTH",$chkRow) === false) {
               $message = sprintf($error_msg,basename(__FILE__),__LINE__);
               throw new Exception($message);
           }
           $access_auth = $chkRow["ACCESS_AUTH"];
           // アクセス権が空の場合
           if(strlen($access_auth) == 0) {
               // アクセス権あり
               $permission = true;        
               return [$ret, $permission]; 
           }
           $access_auth_arry = explode(',', $access_auth);
           foreach($access_auth_arry as $access_role) {
               if(array_key_exists($access_role,$this->AccessRoles) === true) {
                   // アクセス権あり
                   $permission = true;        
                   return [$ret, $permission]; 
               }
           }
           // アクセス権なし
           $permission = false;        
           return [$ret, $permission]; 
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           $ret        = false;
           $permission = false;
           return [$ret, $permission]; 
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定レコード配列のアクセス権と該当ユーザーのロールけユーザー紐づけに紐づいているロール
   //   が一致しているか判定(表示対象の有無を判定)し、表示対象でないレコードを指定レコード配列
   //   から削除
   // 【パラメータ】
   //   $chkRow: $objQuery->sqlExecute()などのselectした複数レコードに指定する。
   //            レコードにACCESS_AUTHがあるのが前提
   //            表示対象でないレコードは削除される
   // 【戻り値】
   //     true:    正常
   //     false:   異常
   // 【備考】
   //   getAccountInfoを呼び出し後に呼び出す。
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function chkRecodeArrayAccessPermission(&$chkRows) {
       $ret = true;
       try {
           foreach($chkRows as $no=>$chkRow) {
               list($ret,$permission) = $this->chkOneRecodeAccessPermission($chkRow);
               if($ret === false) {
                   return false;
               }
               if($permission === false)
               {
                   unset($chkRows[$no]);
               }
           }
           return true;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定されたオブジェクトにアクセス権カラムが定義されているかを判定
   // 【パラメータ】
   //   $tgt_table: 確認するオブジェクト(テーブル・ビュー)
   //   $chkColumname: チェックするカラム名
   //                  基本、未設定
   // 【戻り値】
   //   戻り値1
   //     true:    正常
   //     false:   異常
   //   戻り値2
   //     true:    カラムあり
   //     false:   カラムなし
   // 【備考】
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function chkTableHasAccessPermissionColumDefine($tgt_table,$chkColumname='ACCESS_AUTH') {
       try {
           $error_msg1 = "[%s:%s]:DB Access Error. (Table %s)";
           $error_msg2 = "[%s:%s]:Object not found. (Table %s)";

           $sql = sprintf("desc %s",$tgt_table);
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$tgt_table);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$tgt_table);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           if($objQuery->effectedRowCount() == 0) {
               // ロール・ユーザー紐づけにデータ未登録の場合
               $message = sprintf($error_msg2,basename(__FILE__),__LINE__,$tgt_table);
               throw new Exception($message);
           }
           $permission = false;
           while($row = $objQuery->resultFetch()) {
               if(isset($row["Field"])) {
                   if($row["Field"] == $chkColumname) {
                       $permission = true;
                       break;
                   }
               }
           }
           unset($objQuery);
           $ret = true;
           return [$ret, $permission]; 
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           $ret        = false;
           $permission = false;
           return [$ret, $permission]; 
       }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   $loadtableのACCESS_AUTHの定義有無とIDColumの紐づいているオブジェクトに
   //   ACCESS_AUTHの定義有無により、IDColum・表示ファイル・Excelのマスタ一覧に
   //   表示するデータをACCESS_AUTHで絞り込むか判定する。
   //   指定されたオブジェクトにアクセス権カラムが定義されているかを判定
   //
   //   $loadtable_AccessAuth    true   true   false  false
   //   $AccessAuthColumDefine   true   false  true   false
   //   ACCESS_AUTHで絞り込み    あり   なし   なし   なし
   //
   // 【パラメータ】
   //   $loadtable_AccessAuth:   $loadtableのACCESS_AUTHの定義有無
   //                            $table->setAccessAuth() true:定義あり false:定義なし
   //   $AccessAuthColumDefine:  IDColumの紐づいているオブジェクトのACCESS_AUTHの定義有無
   //                            true:定義あり false:定義なし
   // 【戻り値】
   //   戻り値1
   //     true:    絞り込みあり
   //     false:   絞り込みなし
   // 【備考】
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getPullDownListAccessPermissionNeedCheck($loadtable_AccessAuth,$AccessAuthColumDefine) {
       $chkarray[true][true]   = true;
       $chkarray[true][false]  = false;
       $chkarray[false][true]  = false;
       $chkarray[false][false] = false;
       return($chkarray[$loadtable_AccessAuth][$AccessAuthColumDefine]);
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   ロール管理を指定されたロール名を曖昧検索する。
   //   表示フィルターテキスト検索・RestAPI Filterr機能用
   // 【パラメータ】
   //   $RoleName: ロール名
   //              ロール名に %　_ に含まれている場合は # でエスケープされている前提
   // 【戻り値】
   //   false:   異常
   //   他:      ロールIDの配列
   // 【備考】
   //   webからの場合、異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getRoleNameStringToRoleIDStringForFilter($RoleName) {
       $error_msg1 = "[%s:%s]:DB Access Error. (Table:A_ROLE_LIST ROLE_NAME:%s)";
       try {
           $sql  = "SELECT   ";
           $sql .= " ROLE_ID, ";
           $sql .= " ROLE_NAME ";
           $sql .= "FROM ";
           $sql .= " A_ROLE_LIST ";
           $sql .= "WHERE ";
           $sql .= " DISUSE_FLAG='0' AND ";
           $sql .= " ROLE_NAME COLLATE utf8_unicode_ci LIKE :ROLE_NAME ESCAPE '#' ";
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$RoleName);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $objQuery->sqlBind( array('ROLE_NAME'=>$RoleName));
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$RoleName);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $array = array();
           if($objQuery->effectedRowCount() != 0) {
               while($row = $objQuery->resultFetch()) {
                   $array[]   = $row["ROLE_ID"];
               }
           }
           unset($objQuery);
           return $array;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }

   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   IDColumnで指定されたオブジェクトにアクセス権カラムが定義されているかを判定
   // 【パラメータ】
   //   $tgt_table: 確認するオブジェクト(テーブル・ビュー)
   // 【戻り値】
   //   戻り値1
   //     他:      定義されているアクセス権カラム名配列
   //     false:   異常
   // 【備考】
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function getAccessAithColumnINIDColumnObject($tgt_table,$chkColumname='ACCESS_AUTH') {
       try {
           $error_msg1 = "[%s:%s]:DB Access Error. (Table %s)";
           $error_msg2 = "[%s:%s]:Object not found. (Table %s)";

           $sql = sprintf("desc %s",$tgt_table);
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if($objQuery->getStatus()===false){
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$tgt_table);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           $r = $objQuery->sqlExecute();
           if(!$r) {
               $message = sprintf($error_msg1,basename(__FILE__),__LINE__,$tgt_table);
               $message .= "\n" . $objQuery->getLastError();
               throw new Exception($message);
           }
           if($objQuery->effectedRowCount() == 0) {
               $message = sprintf($error_msg2,basename(__FILE__),__LINE__,$tgt_table);
               throw new Exception($message);
           }
           $AccessAuthColumnAry = array();
           $matchStr = sprintf("/^((%s)|(%s_[0-9][0-9]))$/",$chkColumname,$chkColumname);
           while($row = $objQuery->resultFetch()) {
               if(isset($row["Field"])) {
                   // ACCESS_AUTHカラムの判定 ACCESS_AUTH/ACCESS_AUTH_99
                   $matchStr = sprintf("/^((%s)|(%s_[0-9][0-9]))$/",$chkColumname,$chkColumname);
                   if(preg_match($matchStr, $row["Field"])) {
                       $AccessAuthColumnAry[] = $row["Field"];
                   }
               }
           }
           unset($objQuery);
           return $AccessAuthColumnAry;
       }catch (Exception $e){
           // Webかバックヤードかを判定
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
        }
   }
   ///////////////////////////////////////////////////////////////////
   // 【処理概要】
   //   指定されたアクセス許可ロール配列で、重なっているロールが
   //   あるかを判定する
   //   
   // 【パラメータ】
   //   $AccessAuthRoleAry:   アクセス許可ロール配列の配列
   //   $ResultAccessAuthStr: 重なっているロールIDのCSV文字列
   // 【戻り値】
   //   戻り値1
   //     true:    重なっているロールがあった
   //     false:   重なっているロールIDはなかった。
   // 【備考】
   //   webからの場合、戻り値1が異常の場合など、エラーログをweb_logに出力
   //   パックヤードからの場合、戻り値1が異常の場合など、エラーログをphpの
   //   error_logの出力先に出力
   ///////////////////////////////////////////////////////////////////
   function AccessAuthExclusiveAND($AccessAuthRoleAry,&$ResultAccessAuthStr) {
       $ResultAccessAuthStr = "";
       $MaxRoleCount = 0;
       $DefAccessAuthRole = array();
       // ロール管理に登録されていないロール又は廃止ロールを除外する。
       $userID = 0;  // getAllRoleSearchHashListで$userIDは未使用
       $ret = $this->getAllRoleSearchHashList($userID,$AllRoleID2Name,$AllRoleName2ID);
       if($ret === false) {
           return false;
       }
       $UpdAccessAuthRoleAry = array();
       foreach($AccessAuthRoleAry as $AccessAuthRole) {
           foreach($AccessAuthRole as $no=>$RoleID) {
               $UseRole = false;
               if(array_key_exists($RoleID,$AllRoleID2Name)) {
                   if($AllRoleID2Name[$RoleID]['DISUSE_FLAG'] == '0') {
                       $UseRole = true;
                   }
               }
               if($UseRole == false) {
                   unset($AccessAuthRole[$no]);
               }
           }
           $UpdAccessAuthRoleAry[] = $AccessAuthRole;
       }
       $AccessAuthRoleAry = $UpdAccessAuthRoleAry;
       $AryCount = count($AccessAuthRoleAry);
       if($AryCount < 2) {
           return false;
       }
       foreach($AccessAuthRoleAry as $AccessAuthRole) {
           $RoleCount = count($AccessAuthRole);
           if($MaxRoleCount < $RoleCount) {
               $MaxRoleCount = $RoleCount;
               $DefAccessAuthRole = $AccessAuthRole;
           }
       }
       // アクセス許可ロールが空の場合
       if($MaxRoleCount === 0) {
           // アクセス許可ロールを空に設定
           $ResultAccessAuthStr = "";
           return true;
       }
       for($idx=0;$idx<$AryCount;$idx++) {
           if(count($AccessAuthRoleAry[$idx]) == 0) {
               $AccessAuthRoleAry[$idx] = $DefAccessAuthRole;
           }
       }
       $AndAry = array_intersect($AccessAuthRoleAry[0],$AccessAuthRoleAry[1]);
       for($idx=2;$idx<$AryCount;$idx++) {
           $AndAry = array_intersect($AccessAuthRoleAry[$idx],$AndAry);
       }

       $ResultAccessAuthStr = implode(",", $AndAry);
       if($ResultAccessAuthStr != "") {
           return true;
       } else {
           return false;
       }
   }
   function getOperationAccessAuth($OperationNoUAPK,&$OpeAccessAuthStr) {

       $strFxName = __FUNCTION__;
       $ErrorMsgBase = "%s([FILE]%s[LINE]%s)%s";
       try {
           $sql = "SELECT * FROM C_OPERATION_LIST   WHERE OPERATION_NO_UAPK = :OPERATION_NO_UAPK";
           $AddMsg = sprintf("%s","Input operation list access error.");
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if( $objQuery->getStatus()===false ){
               $ErrorMsg = sprintf($ErrorMsgBase,$strFxName,__FILE__,__LINE__,$AddMsg);
               $ErrorMsg .= "\n" . $objQuery->getLastError();
               throw new Exception($ErrorMsg);
           }
           // SQL発行
           $aryForBind = array("OPERATION_NO_UAPK"=>$OperationNoUAPK);
           $objQuery->sqlBind($aryForBind);
           $r = $objQuery->sqlExecute();
           if (!$r){
               $ErrorMsg = sprintf($ErrorMsgBase,$strFxName,__FILE__,__LINE__,$AddMsg);
               $ErrorMsg .= "\n" . $objQuery->getLastError();
               throw new Exception($ErrorMsg);
           }
           // レコードFETCH
           $fetch_counter = $objQuery->effectedRowCount();
           if ($fetch_counter < 1){
               return 100;
           }
           while ( $row = $objQuery->resultFetch() ){
               $OpeAccessAuthStr = $row['ACCESS_AUTH'];
           }
           unset($objQuery);
           return true;
       }catch (Exception $e){
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
   function getMovementAccessAuth($PatternID,&$MovementAccessAuthStr) {

       $strFxName = __FUNCTION__;
       $ErrorMsgBase = "%s([FILE]%s[LINE]%s)%s";
       try {
           $sql = "SELECT * FROM C_PATTERN_PER_ORCH   WHERE PATTERN_ID = :PATTERN_ID";
           $AddMsg = sprintf("%s","Movement list access error.");
           $objQuery = $this->objDBCA->sqlPrepare($sql);
           if( $objQuery->getStatus()===false ){
               $ErrorMsg = sprintf($ErrorMsgBase,$strFxName,__FILE__,__LINE__,$AddMsg);
               $ErrorMsg .= "\n" . $objQuery->getLastError();
               throw new Exception($ErrorMsg);
           }
           // SQL発行
           $aryForBind = array("PATTERN_ID"=>$PatternID);
           $objQuery->sqlBind($aryForBind);
           $r = $objQuery->sqlExecute();
           if (!$r){
               $ErrorMsg = sprintf($ErrorMsgBase,$strFxName,__FILE__,__LINE__,$AddMsg);
               $ErrorMsg .= "\n" . $objQuery->getLastError();
               throw new Exception($ErrorMsg);
           }
           // レコードFETCH
           $fetch_counter = $objQuery->effectedRowCount();
           if ($fetch_counter < 1){
               return 100;
           }
           while ( $row = $objQuery->resultFetch() ){
               $MovementAccessAuthStr = $row['ACCESS_AUTH'];
           }
           unset($objQuery);
           return true;
       }catch (Exception $e){
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
           return false;
       }
   }
}
    function getTargetRecodeCount($objTable,$objQuery,&$strRecCnt) {
        // unset($objQuery); $objQueryの開放は呼び元で実施
        global $g;
        // ACCESS_AUTHカラムの有無を判定し対象レコードをカウント ----
        if($objTable->getAccessAuth() && "" != $g['login_id']) {
            // ログインユーザーに紐づていてるロールを取得 ----
            $userID = $g['login_id'];
            $obj  = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($userID);
            if($ret === false) {
                $error_msg = "[%s:%s]:Failed to get user access role. (user ID:%s)";
                $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                if(function_exists("web_log")) {
                    web_log($message);
                } else {
                    error_log($message);
                }
                return false;
            }
            // ---- ログインユーザーに紐づていてるロールを取得
            while($row = $objQuery->resultFetch()) {
               // ACCESS_AUTHカラムが有る場合は各レコードのアクセス権を判定し、対象レコードをカウント ----
               list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);
               if($ret === false) {
                   $error_msg = "[%s:%s]:User access permission check failed.(user ID:%s)";
                   $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                   if(function_exists("web_log")) {
                       web_log($message);
                   } else {
                       error_log($message);
                   }
                   return false;
               } else {
                   if($permission === true) {
                       $strRecCnt++;
                   }
               }
               // ---- ACCESS_AUTHカラムが有る場合は各レコードのアクセス権を判定し、対象レコードをカウント
            }
            unset($obj);
        } else {
            // ACCESS_AUTHカラムが無い場合はselect count値を設定 ----
//            $row = $objQuery->resultFetch();
//            $strRecCnt = $row['REC_CNT'];
            while($row = $objQuery->resultFetch()) {
                $strRecCnt++;
            }
            // ---- ACCESS_AUTHカラムが無い場合はselect count値を設定
        }
        // ---- ACCESS_AUTHカラムの有無を判定し対象レコードをカウント
        unset($obj);
        return true;
    }
    // $chkobjは呼び出し元で初期値をNULL設定
    function chkTargetRecodeMultiPermission($chkAccessAuth,&$chkobj,$row) {

        global $g;
        $userID = $g['login_id'];

        // ACCESS_AUTHカラムの有無を判定
        if($chkAccessAuth === true) {
            if($chkobj === null) {
                // ログインユーザーに紐づていてるロールを取得 ----
                $chkobj  = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $chkobj->getAccountInfo($userID);
                if($ret === false) {
                    $error_msg = "[%s:%s]:Failed to get user access role. (user ID:%s)";
                    $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                    if(function_exists("web_log")) {
                        web_log($message);
                    } else {
                        error_log($message);
                    }
                    return [false,false];
                }
                // ログインユーザーに紐づていてるロールを取得 ----
            }
            // 対象レコードのACCESS_AUTHカラムでアクセス権を判定 ----
            list($ret,$permission) = $chkobj->chkOneRecodeMultiAccessPermission($row);
            if($ret === false) {
                $error_msg = "[%s:%s]:Access permission check failed. (user ID:%s)";
                $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                if(function_exists("web_log")) {
                    web_log($message);
                } else {
                    error_log($message);
                }
                return [false,false];
            } else {
                return [true,$permission];
            }
            // ---- 対象レコードのACCESS_AUTHカラムでアクセス権を判定
        } else {
            // ACCESS_AUTHカラムがない場合 ----
            return [true,true];
            // ---- ACCESS_AUTHカラムがない場合
        }
    }
    // $chkobjは呼び出し元で初期値をNULL設定
    function chkTargetRecodePermission($chkAccessAuth,&$chkobj,$row) {

        global $g;
        $userID = $g['login_id'];

        // ACCESS_AUTHカラムの有無を判定
        if($chkAccessAuth === true && "" != $userID) {
            if($chkobj === null) {
                // ログインユーザーに紐づていてるロールを取得 ----
                $chkobj  = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $chkobj->getAccountInfo($userID);
                if($ret === false) {
                    $error_msg = "[%s:%s]:Failed to get user access role. (user ID:%s)";
                    $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                    if(function_exists("web_log")) {
                        web_log($message);
                    } else {
                        error_log($message);
                    }
                    return [false,false];
                }
                // ログインユーザーに紐づていてるロールを取得 ----
            }
            // 対象レコードのACCESS_AUTHカラムでアクセス権を判定 ----
            list($ret,$permission) = $chkobj->chkOneRecodeAccessPermission($row);
            if($ret === false) {
                $error_msg = "[%s:%s]:Access permission check failed. (user ID:%s)";
                $message = sprintf($error_msg,basename(__FILE__),__LINE__,$userID);
                if(function_exists("web_log")) {
                    web_log($message);
                } else {
                    error_log($message);
                }
                return [false,false];
            } else {
                return [true,$permission];
            }
            // ---- 対象レコードのACCESS_AUTHカラムでアクセス権を判定
        } else {
            // ACCESS_AUTHカラムがない場合 ----
            return [true,true];
            // ---- ACCESS_AUTHカラムがない場合
        }
    }
    function AccessAuthColumnFileterDataReplace($userID,$objDBCA,$AccessAuthColumnName,&$arrayFileterBody) {
        $error_role_id = "ErrorID";
        $LikeSearchStrBase = "(^%s$)|(^%s,)|(,%s,)|(,%s$)";
        $CompSearchStrBase = "%s";

        // 検索対象のロール名の数: テキスト検索(曖昧検索)かプルダウン検索(完全一致検索)
        // RestAPIの場合 NORMAL/RANGE:テキスト検索(曖昧検索) LIST:プルダウン検索(完全一致検索)
        $obj = new RoleBasedAccessControl($objDBCA);
        foreach($arrayFileterBody as $key=>$val) {
            $SearchStr     = "";
            $LikeSearchCount   = 0;
            $CompSearchCount   = 0;
            // ロール名の両端にある曖昧検索用の文字を取り除く
            $val = preg_replace("/^%/","",$val);
            $val = preg_replace("/%$/","",$val);
            // $val 検索対象のロール名(CSV形式)
            // 表示フィルターのテキスト検索/RestAPIのNORMAL/RANGE検索(曖昧検索)の場合
            // 検索文字列(カラム名__[99])
            $LikeFileter = sprintf("/^%s__[0-9]*$/",$AccessAuthColumnName);
            if(preg_match($LikeFileter,$key) == 1) {
                // ロール名を分解
                $RoleNameAry = explode(',', $val);
                // ロール名が設定されていることの確認
                foreach($RoleNameAry as $RoleName) {
                    // ロール名が空の場合は不明なロールIDを設定
                    if(strlen($RoleName) == 0) {
                        $val = $error_role_id;
                        $arrayFileterBody[$key] = $val;
                        continue 2;
                    }
                }
                foreach($RoleNameAry as $RoleName) {
                    // 指定されたロール名をLike検索しマッチするロールIDを求める
                    $RoleIDAry = $obj->getRoleNameStringToRoleIDStringForFilter("%$RoleName%");
                    if($RoleIDAry === false) {
                        $val = $error_role_id;
                        $arrayFileterBody[$key] = $val;
                        continue 2;
                    }
                    // ロール名が不正の場合
                    if(count($RoleIDAry) == 0) {
                        $val = $error_role_id;
                        $arrayFileterBody[$key] = $val;
                        continue 2;
                    }
                    $compRoleName = true;
                    // 対象ロール名が完全一致
                    if(count($RoleIDAry) == 1) {
                        // 指定されたロール名をLike検索で完全一致するロールIDを求める
                        $CmpRoleIDAry = $obj->getRoleNameStringToRoleIDStringForFilter($RoleName);
                        if($CmpRoleIDAry=== false) {
                            $val = $error_role_id;
                            $arrayFileterBody[$key] = $val;
                            continue 2;
                        }
                        if(count($CmpRoleIDAry) == 0) {
                            // 部分一致したロール名
                            $compRoleName = false;
                        } else {
                            // 完全一致検索の条件設定
                            foreach($RoleIDAry as $RoleID);
                            if($SearchStr != "")  $SearchStr .= ","; 
                            $SearchStr .= sprintf($CompSearchStrBase,$RoleID);
                            $CompSearchCount++;
                        }
                    }
                    // 対象ロール名が部分一致
                    if((count($RoleIDAry) > 1) || ($compRoleName === false)) {
                        //複数の曖昧検索ロール名が指定されている場合はエラー
                        if($LikeSearchCount != 0) {
                            $val = $error_role_id;
                            $arrayFileterBody[$key] = $val;
                            continue 2;
                        } else {
                            foreach($RoleIDAry as $RoleID) {
                                // 曖昧検索の条件設定
                                if($SearchStr != "")  $SearchStr .= "|"; 
                                $SearchStr .= sprintf($LikeSearchStrBase,$RoleID,$RoleID,$RoleID,$RoleID);
                                $LikeSearchCount++;
                            }
                        }
                    }
                    // 曖昧検索と完全一致検索が混在している場合はエラー
                    if(($CompSearchCount != 0) && ($LikeSearchCount != 0)) {
                        $val = $error_role_id;
                        $arrayFileterBody[$key] = $val;
                        continue 2;
                    }
                }
                $arrayFileterBody[$key] = $SearchStr;
            }
            // 表示フィルターのプルダウン検索(完全一致検索)/RestAPIのLIST検索(完全一致検索)の場合
            // 検索文字列(カラム名_RF__[99])
            $ListFileter = sprintf("/^%s_RF__[0-9]*$/",$AccessAuthColumnName);
            if(preg_match($ListFileter,$key) == 1) {
                $val = $obj->getRoleNameStringToRoleIDString($userID,$val,true,"Error");  // 廃止を含む
                if($val === false) {
                    $val = $error_role_id;
                    return false;
                }
                $arrayFileterBody[$key] = $val;
            }
        }
        return true;
    }
    function chkMovementAccessAuth($OperationNoUAPK,$PatternId,$objDBCA,$objMTS,$restAPI=false,$login_id=0) {
        global $g;

        ////////////////////////////////
        // ルートディレクトリを取得   //
        ////////////////////////////////
        $root_dir_path = $g['root_dir_path'];
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        $ErrorMsgBase = "([FILE]%s[LINE]%s)%s";

        try {

            $RBACobj = new RoleBasedAccessControl($objDBCA);

            if($restAPI === true) {
                $ret = $RBACobj->getAccountInfo($login_id);
            }

            ///////////////////////////////////////////////////
            // 投入オペレーション アクセス許可ロール取得
            ///////////////////////////////////////////////////
            $OpeAccessAuthStr = "";
            $ret = $RBACobj->getOperationAccessAuth($OperationNoUAPK,$OpeAccessAuthStr);
            if($ret !== true) {
                // オペレーションの登録確認は事前に行われている前提
                if($ret === false) {
                    $AddMsg = "Input operation list access error.";
                } else {
                    $AddMsg = "OperationID not found.";
                }
                $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
                $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
                throw new Exception(json_encode($Exception));
            }
    
            if($restAPI === true) {
                ///////////////////////////////////////////////////
                // 投入オペレーション アクセス許可ロール判定
                ///////////////////////////////////////////////////
                $row = array();
                $row['ACCESS_AUTH'] = $OpeAccessAuthStr;
                list($ret,$permission) = $RBACobj->chkOneRecodeAccessPermission($row);
                if($ret === false) {
                    $AddMsg = "chkOneRecodeAccessPermission error.";
                    $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
                    $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
                    throw new Exception(json_encode($Exception));
                }
                if($permission === false) {
                    $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-1119"); // オペレーションとログインユーザーのアクセス許可ロール不適合
                    return chkMovementAccessAuthResultArry("NG","",$ErrorMsg);
                }
            } 
            ///////////////////////////////////////////////////
            // Movement アクセス許可ロール取得
            ///////////////////////////////////////////////////
            $MovementAccessAuthStr = "";
            $ret = $RBACobj->getMovementAccessAuth($PatternId,$MovementAccessAuthStr);
            if($ret !== true) {
                // Movementの登録確認は事前に行われている前提
                if($ret === false) {
                    $AddMsg = "Movement list access error.";
                } else {
                    $AddMsg = "MovementID not found.";
                }
                $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
                $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
                throw new Exception(json_encode($Exception));
            }
    
            if($restAPI === true) {
                ///////////////////////////////////////////////////
                // Movement アクセス許可ロール判定
                ///////////////////////////////////////////////////
                $row = array();
                $row['ACCESS_AUTH'] = $MovementAccessAuthStr;
                list($ret,$permission) = $RBACobj->chkOneRecodeAccessPermission($row);
                if($ret === false) {
                    $AddMsg = "chkOneRecodeAccessPermission error.";
                    $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
                    $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
                    throw new Exception(json_encode($Exception));
                }
                if($permission === false) {
                    $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-1120"); // Movementとログインユーザーのアクセス許可ロール不適合
                    return chkMovementAccessAuthResultArry("NG","",$ErrorMsg);
                }
            } 
    
            ///////////////////////////////////////////////////
            // 投入オペレーションとMovementのアクセス許可ロールの適合判定
            ///////////////////////////////////////////////////
            $AccessAuthAry   = array();
            $AccessAuthAry[] = explode(",",$OpeAccessAuthStr);
            $AccessAuthAry[] = explode(",",$MovementAccessAuthStr);
            $ResultAccessAuthStr = "";
            $ret = $RBACobj->AccessAuthExclusiveAND($AccessAuthAry,$ResultAccessAuthStr);
            if($ret === false) {
                $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-1118"); // アクセス許可ロール不適合
                return chkMovementAccessAuthResultArry("NG","",$ErrorMsg);
            } else {
                return chkMovementAccessAuthResultArry("OK",$ResultAccessAuthStr,"");
            }
        }catch (Exception $e){
            $Exception = json_decode($e->getMessage(),true);
            if($Exception['ERROR_LOG'] != "") {
                if(function_exists("web_log")) {
                    web_log($Exception['ERROR_LOG']);
                } else {
                    error_log($Exception['ERROR_LOG']);
                }
            }
            $ErrorMsg = $Exception['RESPONS_MSG'];
            return chkMovementAccessAuthResultArry("ER","",$Exception['RESPONS_MSG']);
        }
    }
    function chkMovementAccessAuthResultArry($Status,$AccessAuth,$ErrorMsg) {
        return array("STATUS"=>$Status,"ACCESS_AUTH"=>$AccessAuth,"ERROR_MSG"=>$ErrorMsg);
    }
    // RBAC対応 ----
    function ky_debug($file,$func,$line,$title,$data) {
       try {
         $dump = var_export($data,true);
         $tmpVarTimeStamp = time();
         $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);

         $log = sprintf("%s:%s:%s:%s:--%s--\n[%s]\n",$logtime,basename($file),$func,$line,$title,$dump);
         error_log($log,3,"/temp/logs/trace.log");
         error_log($log,3,"/temp/logs/" . $func . ".log");
       }catch (Exception $e){
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
       }
    }
    function ky_backtrace($file,$func,$line,$title) {
       try {
         $print_backtrace = "------backtrace--\n";
         $tmpVarTimeStamp = time();
         $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);
         $trace = debug_backtrace();
         foreach($trace as $oneline) {
             $nowfile = 'None';
             $nowline = 'None';
             if(isset($oneline['file'])) $nowfile = $oneline['file'];
             if(isset($oneline['line'])) $nowline = $oneline['line'];
             $print_backtrace .= sprintf("%s: line:%s\n",$nowfile,$nowline);
         }
         //$log = sprintf("%s:%s:%s:%s:--%s--\n%s\n",$logtime,basename($file),$func,$line,$title,$print_backtrace);
         $log = sprintf("%s:%s:%s:%s\n%s\n",$logtime,basename($file),$func,$line,$print_backtrace);
         error_log($log,3,"/temp/logs/trace.log");
         error_log($log,3,"/temp/logs/" . $func . ".log");
       }catch (Exception $e){
           if(function_exists("web_log")) {
               web_log($e->getMessage());
           } else {
               error_log($e->getMessage());
           }
       }
    }
?>
