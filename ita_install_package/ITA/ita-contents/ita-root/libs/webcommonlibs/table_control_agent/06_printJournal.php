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
    //    ・登録/更新のテーブル領域に、プルダウンリストHtmlタグを、事後的に作成する
    //
    //  【その他】
    //    ・文字列リテラルは、原則ダブルコーテーションでラップする
    //    ・連想配列の鍵は、原則シングルコーテーションでラップする
    //
    //////////////////////////////////////////////////////////////////////

    function printJournalMain($objTable, $strFormatterId="print_journal_table", $filterData, &$aryVariant=array(), &$arySetting=array(), $aryOverride=array()){

        global $g;
        require_once ( "{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        //
        // return値
        $varRet = null;
        //
        $intErrorType = null;
        $error_str = "";
        $strErrorBuf = "";
        $strSysErrMsgBody = "";
        
        $strResultCode = "000";
        $strErrorCode = "000";
        $strDetailCode = "000";
        $strOutputStr = "";

        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;

        $str_temp   = "";
        $defaultValueOnFx = array("Journal1Tbl","fakeContainer_Journal1Print");
        $refRetKeyExists = null;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----D-TiS共通
            $retArray = checkCommonSettingVariants($strFxName, $objTable, $aryVariant, $arySetting, "JournalTableFormatter", $strFormatterId);
            $checkFormatterId = $retArray[1];
            $objListFormatter = $retArray[2];

            $defaultValueOnFx = checkOverrideValue($strFxName, $defaultValueOnFx, $aryOverride);
            //D-TiS共通----

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
            $arrayObjColumn = $objTable->getColumns();

            $lcRequiredJnlSeqNoColumnId = $objTable->getRequiredJnlSeqNoColumnID(); //"JOURNAL_SEQ_NO"
            $lcRequiredJnlRegTimeColumnId = $objTable->getRequiredJnlRegTimeColumnID(); //"JOURNAL_REG_DATETIME"

            //----出力されるタグの属性値
            $strShowTable01TagId = $objListFormatter->getGeneValue("stdJournalTable.tagIDonHTML",$refRetKeyExists);
            if( $strShowTable01TagId===null && $refRetKeyExists===false ){
                $strShowTable01TagId = $defaultValueOnFx[0];
            }
            $strShowTable01WrapDivClass = $objListFormatter->getGeneValue("stdJournalTable.wrapDivClass",$refRetKeyExists);
            if( $strShowTable01WrapDivClass===null && $refRetKeyExists===false ){
                $strShowTable01WrapDivClass = $defaultValueOnFx[1];
            }
            //出力されるTableタグの属性値----
            if( isset($aryVariant["TCA_PRESERVED"])===false ){
                $aryVariant["TCA_PRESERVED"] = array();
            }
            $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_journalPrint");
            $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;
            //固有----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTiS_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }

            if( $strPrivilege === "1" ){
                // ----1はメンテナンス権限あり
                // 1はメンテナンス権限あり----
            }else if( $strPrivilege === "2" ){
                /// ----2は参照のみ
                // 2は参照のみ----
            }else{
                // ----0は権限がないので出力しない
                $intErrorType = 1;
                throw new Exception( '00010100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            //----念のために削除
            if(array_key_exists("filter_ctl_start_limit", $filterData)){
                unset($filterData["filter_ctl_start_limit"]);
            }
            //念のために削除----

            $columns = $objTable->getColumns();
            $arySynonyms = $objTable->getColumnIDSOPs();

            $countFilter = 0;
            foreach($filterData as $key=>$data){
                if(array_key_exists($key,$arySynonyms)===true){
                    //----設定ファイルで規定されたカラムの場合
                    $strCheckKey = $arySynonyms[$key];
                    if( $columns[$strCheckKey]->getJournalSearchFilter() === true ){
                        if( is_array($data) === true ){
                            foreach($data as $strKey2=>$varVal2){
                                if( is_string($varVal2) === true ){
                                    if( 0 < strlen($varVal2 )){
                                        $countFilter++;
                                    }
                                }
                            }
                        }
                    }else{
                        $intErrorType = 2;
                        $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-221",$columns[$strCheckKey]->getColLabel(true));
                    }
                    //設定ファイルで規定されたカラムの場合----
                }
            }
            
            if($countFilter === 0){
                $intErrorType = 2;
                $error_str .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-222");
            }
            
            if( $intErrorType != null ){
                $intErrorType = 2;
                throw new Exception( '00010200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //----注意部分：ロードテーブルの設定を一時的に破壊する
            $columns[$lcRequiredJnlSeqNoColumnId]->setDBColumn(true);
            $columns[$lcRequiredJnlRegTimeColumnId]->setDBColumn(true);
            $columns[$objTable->getRIColumnID()]->setSearchType("in");
            
            //注意部分：ロードテーブルの設定を一時的に破壊する----

            $boolBinaryDistinctOnDTiS = $objListFormatter->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
            if( $boolBinaryDistinctOnDTiS===null && $refRetKeyExists===false  ){
                $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
            }
            if(is_bool($boolBinaryDistinctOnDTiS)===false){
                $boolBinaryDistinctOnDTiS = true;
            }

            //----必須チェックなどを事前にしたい場合は、ここで差し替え
            if( $aryFunctionForOverride!==null ){
                list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("printJournalMain",$strFormatterId,"DTiSFilterCheckValid"),null);
                unset($tmpBoolKeyExist);
                if( is_callable($tmpObjFunction01ForOverride)===true ){
                    $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                }
                unset($tmpObjFunction01ForOverride);
            }
            //必須チェックなどを事前にしたい場合は、ここで差し替え----

            foreach($arrayObjColumn as $objColumn){
                $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                if($arrayTmp[0]===false){
                    $intErrorType = $arrayTmp[1];
                    throw new Exception( '00010300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            //----バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）
            if( $objFunction01ForOverride===null ){
                $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant);
            }
            else{
                $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant);
            }
            if( $tmpAryRet[1]!==null ){
                $intErrorType = $tmpAryRet[1];
                $error_str = implode("", $tmpAryRet[2]);
                unset($tmpAryRet);
                throw new Exception( '00010400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($tmpAryRet);
            //バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）----

            foreach($arrayObjColumn as $objColumn){
                $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                if($arrayTmp[0]===false){
                    $intErrorType = $arrayTmp[1];
                    throw new Exception( '00010500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            if($boolBinaryDistinctOnDTiS === true){
                //----厳格な検出をする
                $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);
                $boolFocusRet= dbSearchResultNormalize();
                if($boolFocusRet === false){
                    $intErrorType = 500;
                    throw new Exception( '00010600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //厳格な検出をする
            }else{
                //----検出時、大文字と小文字、カタカナと平仮名の区別なし
                $boolBinaryDistinctOnDTiS = false;

                $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

                //----DB面で、大文字小文字と全角半角を無視する設定
                $boolFocusRet= dbSearchResultExpand();
                if($boolFocusRet === false){
                    $intErrorType = 500;
                    throw new Exception( '00010700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //DB面で、大文字小文字と全角半角を無視する設定----

                //検出時、大文字と小文字、カタカナと平仮名の区別なし----
            }

            if( $aryFunctionForOverride!==null ){
                 list($tmpObjFunction02ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("printJournalMain",$strFormatterId,"selectResultFetch"),null);
                 unset($tmpBoolKeyExist);
                 if( is_callable($tmpObjFunction02ForOverride)===true ){
                     $objFunction02ForOverride = $tmpObjFunction02ForOverride;
                 }
                 unset($tmpObjFunction02ForOverride);
            }

            if( $objFunction02ForOverride===null ){
	            // RBAC対応 ----
	            // 指定された変更履歴の項番のマスタデータのアクセス権を判定 ----
                    $MasterRecodePermission = true;
	            if($objTable->getAccessAuth() === true) {
	                $dbQM=$objTable->getDBQuoteMark();
	                $dbTAS = $objTable->getShareTableAlias();
	                $strRIColumn = $objTable->getRowIdentifyColumnID();
	                $strAccessAuthColumnName = $objTable->getAccessAuthColumnName();
	                $strColStream = " {$dbQM}{$dbTAS}{$dbQM}.{$dbQM}{$strAccessAuthColumnName}{$dbQM} {$strAccessAuthColumnName} ";
	                // 履歴検索条件を取得
	                foreach($filterData as $dummy=>$keyAry);
	                foreach($keyAry as $dummy=>$keyStr);
	                $strwhere  = " WHERE ";
	                $strwhere .= " {$dbQM}{$dbTAS}{$dbQM}.{$dbQM}{$strRIColumn}{$dbQM} = '{$keyStr}' ";
	                $query  = "SELECT {$strColStream} ";
	                $query .= "FROM {$objTable->getDBMainTableBody()} {$dbTAS} {$strwhere}";
                        $nullarry = array();
	                $retArray = singleSQLExecuteAgent($query, $nullarry, $strFxName);
	                if( $retArray[0] === true ){
	                    $objQuery =& $retArray[1];
			    $chkobj = null;
                            // 対象レコードのACCESS_AUTHカラムでアクセス権を判定
	                    while($row = $objQuery->resultFetch()) {   // 対象レコードは1レコードの想定
                                list($ret,$MasterRecodePermission) = chkTargetRecodePermission($objTable->getAccessAuth(),$chkobj,$row);
                                if($ret === false) {
                                    $message = sprintf("[%s:%s]Master recode select failed. (TABLE:%s Pkey:%s)",basename(__FILE__),__LINE__,$objTable->getDBMainTableBody(),$objTable->getRowIdentifyColumnID());
	                            web_log($message);
                                    $intErrorType = 500;
                                    throw new Exception( '00010801-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
	                        }
	                        break;

	                    }
	                } else {
                            $message = sprintf("[%s:%s]Master recode select execute failed. (TABLE:%s Pkey:%s)",basename(__FILE__),__LINE__,$objTable->getDBMainTableBody(),$objTable->getRowIdentifyColumnID());
	                    web_log($message);
                            $intErrorType = 500;
                            throw new Exception( '00010801-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
	                }
	            }
	            // ---- 変更履歴で指定された項番のマスタデータのアクセス権を判定 ----
	            // ---- RBAC対応

	            $row_counter = 0;
	            // ---- RBAC対応
	            // ----マスタレコードにアクセス権が無い場合は履歴を表示しない
	            if($MasterRecodePermission === true) {
	                $sql = generateJournalSelectSQL(2,$objTable, $boolBinaryDistinctOnDTiS);

	                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
	                if( $retArray[0] === true ){
	                    $objQuery =& $retArray[1];
                            // ---- 対象レコードのACCESS_AUTHカラムでアクセス権を判定
			    $intTmpRowCount = 0;
			    $chkobj = null;
	                    while ( $row = $objQuery->resultFetch() ){
                                // ----RBAC対応
                                if($objTable->getAccessAuth() === false) {
                                    // アクセス権の判定が不要な場合
                                    $intTmpRowCount +=1;
				    $objTable->addData($row, false);
                                    continue;
                                }
                                // 対象レコードのACCESS_AUTHカラムでアクセス権を判定
                                list($ret,$permission) = chkTargetRecodePermission($objTable->getAccessAuth(),$chkobj,$row);
                                if($ret === false) {
                                    $intErrorType = 500;
                                    throw new Exception( '00010801-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                if($permission === true) {
                                    global $g;
                                    $AccessAuthColumName    = $objTable->getAccessAuthColumnName();
                                    // アクセス権カラム有無判定
                                    if(array_key_exists($AccessAuthColumName,$row)) {
                                        // ---- アクセス権カラムの表示データをロールIDからRole名称に変更
                                        // 廃止されているロールはID変換失敗で表示
                                        $obj = new RoleBasedAccessControl($g['objDBCA']);
                                        $RoleNameString = $obj->getRoleIDStringToRoleNameString($g['login_id'],$row[$AccessAuthColumName],true);  // 廃止も含む
                                        unset($obj);
                                        if($RoleNameString === false) {
                                            $intErrorType = 500;
                                            $message = sprintf("[%s:%s]getRoleIDStringToRoleNameString is failed.",basename(__FILE__),__LINE__);
                                            web_log($message);
                                            throw new Exception( '00010801-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }
                                        $row[$AccessAuthColumName] = $RoleNameString;
                                        // アクセス権カラムの表示データをロールIDからRole名称に変更----
                                        $intTmpRowCount +=1;
				        $objTable->addData($row, false);
                                    }
                                }
                                // RBAC対応----
			    }
                            // 対象レコードのACCESS_AUTHカラムでアクセス権を判定 ----
	                    $row_counter = $intTmpRowCount;
	                    unset($objQuery);
	                }
	                else{
	                    $intErrorType = 500;
	                    throw new Exception( '00010800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
	                }
	            }
	            // RBAC対応 ----
	            // マスタレコードにアクセス権が無い場合は履歴を表示しない ----
            }
            else{
                $tmpAryRet = $objFunction02ForOverride($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting, $aryOverride);
                if( $tmpAryRet[1]!==null ){
                    $intErrorType = $tmpAryRet[1];
                    $error_str = implode("", $tmpAryRet[2]);
                    unset($tmpAryRet);
                    throw new Exception( '00010900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $row_counter = $tmpAryRet[0];
                unset($tmpAryRet);
            }

            if($boolBinaryDistinctOnDTiS === true){
            }else{
                $boolFocusRet= dbSearchResultNormalize();
                if($boolFocusRet === false){
                    $intErrorType = 500;
                    throw new Exception( '00011000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            foreach($arrayObjColumn as $objColumn){
                $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                if($arrayTmp[0]===false){
                    $intErrorType = $arrayTmp[1];
                    throw new Exception( '00011100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            // 0件の場合はTABLEではなくメッセージのみを返却するようハンドリング
            if($row_counter == 0){
                $strOutputStr = "<br>{$g['objMTS']->getSomeMessage("ITAWDCH-STD-410")}<br>";
            }
            else{
                $str_temp = $objTable->getPrintFormat($strFormatterId, $strShowTable01TagId);
                $strOutputStr = 
<<< EOD
                <div class="{$strShowTable01WrapDivClass}">
{$str_temp}
                </div>
EOD;
            }
            //
            // DBアクセスを伴う処理を終了----
            
            $strOutputStr .= "<div class=\"hyouji_flag\" style=\"display:none;\"></div>";
            
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $strResultCode = sprintf("%03d", $intErrorType);
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 1 : $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-223"); break;//----権限なし
                case 2 : break;//----バリデーションエラー
                default: $error_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-224"); break;//----一般エラー
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイザー向けメッセージを作成
                //ロードテーブルカスタマイザー向けメッセージを作成----
            }
            //----システムエラー級エラーの場合はWebログにも残す
            if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
            if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
            //システムエラー級エラーの場合はWebログにも残す----
            $strOutputStr = nl2br($error_str);
        }
        unset($aryVariant["TCA_PRESERVED"]["TCA_ACTION"]);
        $varRet[0] = $strResultCode;
        $varRet[1] = $strDetailCode;
        $varRet[2] = $strOutputStr;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        return $varRet;

    }
?>
