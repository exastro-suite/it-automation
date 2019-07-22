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
    //  【特記事項】
    //   ・多言語化禁止（＝英語のみでメッセージは記述）
    //
    //////////////////////////////////////////////////////////////////////

    function makeUniqueTempFilename($strPlaceDir,$strPrefix)
    {
        $retValue = tempnam($strPlaceDir,$strPrefix);
        return $retValue;
    }

    function where_queryForLike_Wrapper($strValue, $boolSchZenHanDistinct)
    {
        global $g;
        $retStrValue = "";
        if($boolSchZenHanDistinct===false){
            //----曖昧検索モード
            //----ここを通ると記号が小文字になる
            $strValue = convert_mb_kana_for_fazzyMode($strValue);
            //ここを通ると記号が小文字になる----
            if($g['db_model_ch'] == 0){
                //----オラクル版
                $strAddInfo = "";
                $g['objDBCA']->getModelChannel($strAddInfo);
                $aryVersion = explode(".",$strAddInfo);
                if( 12 <= $aryVersion[0] || ( 11 == $aryVersion[0] && 2 <= $aryVersion[1] ) ){
                }else{
                    //----11gR2以降はエラー(ORA-01424)になる
                    $strValue = str_replace('％', '#％', $strValue);
                    $strValue = str_replace('＿', '#＿', $strValue);
                     //11gR2以降はエラー(ORA-01424)になる----
                }
                //オラクル版----
            }else{
                //----mysql/MariaDB版
                $strValue = str_replace('#', '＃', $strValue);
                $strValue = str_replace('%', '#%', $strValue);
                $strValue = str_replace('_', '#_', $strValue);
                //mysql/MariaDB版----
            }
            //曖昧検索モード----
        }else{
            //----厳格一致モード
            $strValue = str_replace('#',  '##', $strValue);
            $strValue = str_replace('%',  '#%', $strValue);
            $strValue = str_replace('_',  '#_', $strValue);
            
            $strValue = str_replace('％', '#％', $strValue);
            $strValue = str_replace('＿', '#＿', $strValue);
            //厳格一致モード----
        }
        $retStrValue = $strValue;
        return $retStrValue;
    }

    function convert_mb_kana_for_fazzyMode($strValue)
    {
        global $g;
        $strRetValue = $strValue;
        
        // (U+0021-U+007Eの範囲のうち、(U+0022/U+0027/U+005C/U+007Eを除いた）、全角文字を、半角文字にする）。
        // (U+0021-U+002F)!#$%&()*+,-./　　U+0022["]/U+0027[']
        // (U+0030-U+0039)0123456789
        // (U+003a-U+0040):;<=>?@
        // (U+0041-U+005A)ABCDEFGHIJKLMNOPQRSTUVWXYZ
        // (U+005B-U+0060)[]^_`　　U+005C[\]
        // (U+0061-U+007A)abcdfeghijklmnopqrstuvwxyz
        // (U+007B-U+007E){|}　　U+007E[~]
        
        // UTF-8([C2A5]<==>\(円マーク))
        // Unicode([5C]<=>バックスラッシュ) 
        //
        // 2016/12/01時点で、ITAでは、半角\マークは[5C]で保存される
        
        if($g['db_model_ch'] == 0)
        {
            //----オラクル版(ポリシー：リクエスト側の鍵は、できるものは、すべて全角にする。カナは「ひらがな」に)
            
            // 前提1：あいまい検索モード下では、以下の場合で、突合せ時に全角「￥」に統一する
            // (1)16進数で「5C」の文字
            // (2)16進数で「C2A5」の文字(本来UTF-8での半角円マーク)
            // (3)全角バックスラッシュ
            
            //A:半角英数を全角英数に、一定の半角記号を全角に
            //S:半角スペースを全角スペースに、
            //KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)
            
            $strRetValue = mb_convert_kana($strRetValue, "ASKV","UTF-8");
            
            //c：全角カタカナを全角ひらがなに変換
            $strRetValue = mb_convert_kana($strRetValue, "c","UTF-8");
            
            $strRetValue = str_replace('"' , '”', $strRetValue);
            $strRetValue = str_replace("'" , "’", $strRetValue);
            
            // オラクル側ではハイフン[-](2d)が、
            // TO_MULTI_BYTE関数で[－](e2,88,92)になるので、クエリ側も合わせる
            $strRetValue = str_replace("-"               ,hex2bin("E28892"), $strRetValue);
            // 全角[－]を、オラクル側の全角[－]に合わせる
            $strRetValue = str_replace(hex2bin("EFBC8D") ,hex2bin("E28892"), $strRetValue); 
            
            $strRetValue = str_replace("~"               ,hex2bin("E28892"), $strRetValue);
            $strRetValue = str_replace("～"              ,hex2bin("E28892"), $strRetValue);
            
            $strRetValue = str_replace("^"               ,hex2bin("E28892"), $strRetValue);            
            $strRetValue = str_replace("＾"              ,hex2bin("E28892"), $strRetValue);
            
            // オラクル側では[\](5C)が、
            //TO_MULTI_BYTE関数で[＼](ef,bc,bc)になるので、クエリ側も合わせる
            $strRetValue = str_replace("\\"              ,hex2bin("EFBCBC"), $strRetValue);
            $strRetValue = str_replace(hex2bin("C2A5")   ,hex2bin("EFBCBC"), $strRetValue);
            $strRetValue = str_replace("￥"              ,hex2bin("EFBCBC"), $strRetValue);

            // オラクル側ではバッククォート[`](60)が、
            // TO_MULTI_BYTE関数で[‘](e2,80,98)になるので、クエリ側も合わせる
            $strRetValue = str_replace("`"               ,hex2bin("E28098"), $strRetValue);
            $strRetValue = str_replace(hex2bin("EFBD80") ,hex2bin("E28098"), $strRetValue);
            
            //オラクル版(ポリシー：リクエスト側の鍵は、できるものは、すべて全角にする。カナは「ひらがな」に)
        }
        else
        {
            //----mysql/MariaDB版
            
            //a:全角英数を半角英数に、一定の全角記号を半角に
            //s:全角スペースを半角スペースに、
            //KV:半角カタカナを全角カタカナに変換(濁点付きは全角1文字へ)
            $strRetValue = mb_convert_kana($strValue, "asKV","UTF-8");
            
            //mysql/MariaDB版----
        }
        return $strRetValue;
    }

    function singleSQLCoreExecute($objDBCA,$strSql,$aryForBind=array(),$strCallOwnerMark="",$strErrorPrintFxName=""){
        global $g;
        $strFxName = __FUNCTION__;
        $retBoolResult = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $objQuery = null;
        $retStrLastErrMsg = "";
        
        if($strCallOwnerMark=="")
        {
            $strCallOwnerMark = "UNKNOWN";
        }
        try
        {
            $objQuery = $objDBCA->sqlPrepare($strSql);
            if( $objQuery->getStatus()===false )
            {
                // 例外処理へ
                throw new Exception( 'SQL PREPARE' );
            }
            if( $objQuery->sqlBind($aryForBind) != "" )
            {
                // 例外処理へ
                throw new Exception( 'SQL BIND' );
            }
            $retBoolResult = $objQuery->sqlExecute();
            if($retBoolResult!=true)
            {
                // 例外処理へ
                throw new Exception( 'SQL EXECUTE' );
            }
        }
        catch (Exception $e)
        {
            $strErrInitTime = getMircotime(1);
            if( is_string($strSql)!==true ){
                $strSql = "";
            }
            $strSSEAErrInitKey = '[sCSE-Err-initKey:'.md5($strErrInitTime.bin2hex($strSql)).']';
            
            $tmpAryData = debug_backtrace($limit=1);
            $aryBackTrace = array($tmpAryData[0]['file'],$tmpAryData[0]['line']);
            
            $strTmpStrBody = '([FILE]'.$aryBackTrace[0].',[LINE]'.$aryBackTrace[1].')'.$strSSEAErrInitKey.' '.$e->getMessage();
            $retStrLastErrMsg = $objQuery->getLastError();
            
            if ( isset($objQuery) )    unset($objQuery);
            $objQuery = null;
            
            $aryErrMsgBody[] = "ERROR OCCURRED ON STEP[{$strTmpStrBody}] OF PROCESS SingleSQLCoreExecute. CLIENT NAME IS [{$strCallOwnerMark}].";
            $aryErrMsgBody[] = $strSSEAErrInitKey.$strSql;
            $aryErrMsgBody[] = $strSSEAErrInitKey.print_r($aryForBind,true);
            $aryErrMsgBody[] = $strSSEAErrInitKey.$retStrLastErrMsg;

            if( is_callable($strErrorPrintFxName)===true ){
                foreach($aryErrMsgBody as $strElementBody){
                    $strErrorPrintFxName($strElementBody);
                }
            }
        }
        $retArray = array($retBoolResult,$intErrorType,$aryErrMsgBody,$objQuery,$retStrLastErrMsg);
        return $retArray;
    }

    function setToArrayNest(&$ary,$aryKeyNest,$varAssignTarget)
    {
        $retArray = array();
        $retBoolSet = true;
        $boolFullKey = false;
        $boolExeContinue = true;
        if( is_array($aryKeyNest)===false ){
            $retBoolSet = false;
        }else{
            //----キー値の型チェック
            foreach($aryKeyNest as $value){
                if( is_string($value)===false ){
                    $retBoolSet = false;
                    $boolExeContinue = false;
                    break;
                }
            }
            if( $boolExeContinue === true ){
                if( is_array($ary) === false ){
                    $retBoolSet = false;
                    $boolExeContinue = false;
                }
            }
            if( $boolExeContinue === true ){
                $aryFocus = &$ary;
                $intCount = count($aryKeyNest);
                $intFocusLevel = 0;
                foreach($aryKeyNest as $value){
                    if( preg_match('/^0$|^-?[1-9][0-9]*$/s', $value) === 1 ){
                        //----整数の表現の場合（配列の鍵が整数の場合、整数型へ暗黙キャストされるので安全のため文字列型へ）
                        $value = "PREFIX_ON_LIKE_INT_".$value;
                        //整数の表現の場合（配列の鍵が整数の場合、整数型へ暗黙キャストされるので安全のため文字列型へ）----
                    }
                    if( array_key_exists($value, $aryFocus)===false ){
                        //----途中で鍵がなかった
                        $strFocusKey = $value;
                        for( $Fnv1 = $intFocusLevel ; $Fnv1 <= $intCount - 1 ;$Fnv1++){
                            if( $Fnv1 == $intCount - 1 ){
                                $aryFocus[$strFocusKey] = $varAssignTarget;
                            }else{
                                $aryFocus[$strFocusKey] = array();
                                $aryFocus = & $aryFocus[$strFocusKey];
                                $strFocusKey = $aryKeyNest[$intFocusLevel + 1];
                            }
                        }
                        break;
                        //途中で鍵がなかった----
                    }else{
                        //----鍵があった
                        $boolFullKey = true;
                        if( $intFocusLevel == $intCount - 1 ){
                            // 最後の層までいって存在した
                            $aryFocus[$value] = $varAssignTarget;
                            break;
                        }
                        $intFocusLevel += 1;
                        $aryFocus = & $aryFocus[$value];
                        //鍵があった----
                    }
                }
            }
        }
        $retArray[1] = $boolFullKey; //代入前のキーの有無
        $retArray[0] = $retBoolSet; //代入の成否
        return $retArray;
    }
    function isSetInArrayNestThenAssign($ary,$aryKeyNest=array(),$varNotExistDefault=null)
    {
        // $ary:探索範囲とする連想配列
        // $aryKeyNest:（'探す文字列キー(1層目)'[,'探す文字列キー[2層目]・・・']）
        // $varNotExistDefault:見つからなかった場合の代入値
        $retArray = array();
        $retBoolSet = true;
        $retVar = $varNotExistDefault;
        $aryFocus = $ary;
        $intCount = 0;
        if( is_array($aryKeyNest)===false ){
            $retBoolSet = false;
        }else{
            foreach($aryKeyNest as $value){
                if( is_array($aryFocus)===false ){
                    $retBoolSet = false;
                    break;
                }
                if( is_string($value)===false ){
                    $retBoolSet = false;
                    break;
                }
                if( preg_match('/^0$|^-?[1-9][0-9]*$/s', $value) === 1 ){
                    //----整数の表現の場合（配列の鍵が整数の場合、整数型へ暗黙キャストされるので安全のため文字列型へ）
                    $value = "PREFIX_ON_LIKE_INT_".$value;
                    //整数の表現の場合（配列の鍵が整数の場合、整数型へ暗黙キャストされるので安全のため文字列型へ）----
                }
                if( array_key_exists($value, $aryFocus)===false ){
                    $retBoolSet = false;
                    break; 
                }
                $aryFocus = $aryFocus[$value];
                $intCount += 1;
            }
            if( $intCount === count($aryKeyNest) ){
                //----発見した場合に、値を代入
                $retVar = $aryFocus;
            }
        }
        $retArray[2] = $intCount; //発見できたキーの層数
        $retArray[1] = $retBoolSet; //キー発見の有無
        $retArray[0] = $retVar; //発見した値または未発見時のデフォルト値
        return $retArray;
    }

    function checkRiskOfDirTraversal($strPath){
        $boolValue = true;
        if(preg_match("/(\.\.\/|\.\.\\\\)/", $strPath)==0 ){
            $boolValue=false;
        }
        return $boolValue;
    }

    function getSequenceLockInTrz($strSeqName, $strSeqTable="A_SEQUENCE"){
        //----トランザクションがスタートしていることが前提
        global $arrayReqInfo, $g, $objDBCA;
        
        $retVal=array();
        
        $intLatelstValue = null;
        $intResultStatus = 0;
        
        $aryMsgBody = array();
        $aryMsgBody[] = "(get original-sequence[{$strSeqName}] from [{$strSeqTable}])";
        
        $array1 = array('SEQ_NAME'=>$strSeqName);
        
        if( $arrayReqInfo[0] == "web" ){
            if(isset($g)){
                $lcObjDBCA = $g['objDBCA'];
            }else{
                
                $lcObjDBCA = $objDBCA;
            }
        }else{
            $lcObjDBCA = $objDBCA;
        }
        
        $sqlBody1 = "SELECT VALUE FROM {$strSeqTable} WHERE NAME = :SEQ_NAME FOR UPDATE";
        $objQuery1 = $lcObjDBCA->sqlPrepare($sqlBody1);
        if( $objQuery1->getStatus() === false ){
            $intResultStatus=101;
            $aryMsgBody[] = "SQL perse error is occured.";
        }else{
            $objQuery1->sqlBind($array1);
            $retSql1 = $objQuery1->sqlExecute();
            if($retSql1!==false){
                $intTmpRowCount = 0;
                $row1 = $objQuery1->resultFetch();
                $row2 = $objQuery1->resultFetch();
                if($row2 !== false){
                    //----二行以上見つかった異常
                    $intResultStatus=103;
                    $aryMsgBody[] = "Selected row count is not 1.";
                    //二行以上見つかった異常----
                }else{
                    $intNowVal = $row1['VALUE'];
                    $intLatelstValue = $intNowVal;
                }
            }else{
                $intResultStatus=102;
                $aryMsgBody[] = "Select value error is occured.";
                $aryMsgBody[] = $objQuery1->getLastError();
            }
        }
        
        unset($objQuery1);
        
        $retVal[0] = $intLatelstValue;
        $retVal[1] = $intResultStatus;
        $retVal[2] = $aryMsgBody;
        
        return $retVal;
    }

    function getSequenceValueFromTable($strSeqName, $strSeqTable="A_SEQUENCE", $boolTrzAutoStart=true){
        global $arrayReqInfo, $g, $objDBCA;
        
        $retVal=array();
        
        $intLatelstValue = null;
        $intResultStatus = 0;
        
        $varTrzStart = null;
        $varCommit = null;
        $varRollBack = null;
        $varTrzExit = null;
        
        $strSqlQueryTail = "";
        
        $aryMsgBody = array();
        $aryMsgBody[] = "(get original-sequence[{$strSeqName}] from [{$strSeqTable}])";
        
        $array1 = array('SEQ_NAME'=>$strSeqName);
        
        if( $arrayReqInfo[0] == "web" ){
            if(isset($g)){
                $lcObjDBCA = $g['objDBCA'];
            }else{
                
                $lcObjDBCA = $objDBCA;
            }
        }else{
            $lcObjDBCA = $objDBCA;
        }
        if( $boolTrzAutoStart === true ){
            //----毎回コミットするモード
            $strSqlQueryTail = "FOR UPDATE";
            $varTrzStart = $lcObjDBCA->transactionStart();
            //毎回コミットするモード----
        }else{
            //----コミットしないモード
            $varTrzStart = true;
            //コミットしないモード----
        }
        if( $varTrzStart === true ){
            $sqlBody1 = "SELECT VALUE FROM {$strSeqTable} WHERE NAME = :SEQ_NAME ".$strSqlQueryTail;
            $objQuery1 = $lcObjDBCA->sqlPrepare($sqlBody1);
            if( $objQuery1->getStatus() === false ){
                $intResultStatus=101;
                $aryMsgBody[] = "SQL perse error is occured.";
            }else{
                $objQuery1->sqlBind($array1);
                $retSql1 = $objQuery1->sqlExecute();
                if($retSql1!==false){
                    $intTmpRowCount = 0;
                    $row1 = $objQuery1->resultFetch();
                    $row2 = $objQuery1->resultFetch();
                    if($row2 !== false){
                        //----二行以上見つかった異常
                        $intResultStatus=103;
                        $aryMsgBody[] = "Selected row count is not 1.";
                        //二行以上見つかった異常----
                    }else{
                        $intNowVal = $row1['VALUE'];
                    }
                    
                    if( 1 <= strlen($intNowVal) ){
                        $intNextVal = $intNowVal + 1;
                        
                        $array2 = array('SEQ_NAME'=>$strSeqName, 'NEXT_VAL'=>$intNextVal);
                        $sqlBody2 = "UPDATE {$strSeqTable} SET VALUE = :NEXT_VAL WHERE NAME = :SEQ_NAME";
                        $objQuery2 = $lcObjDBCA->sqlPrepare($sqlBody2);
                        
                        $objQuery2->sqlBind($array2);
                        
                        $retSql2 = $objQuery2->sqlExecute();
                        if($retSql2===true){
                            $resultRowLength = $objQuery2->effectedRowCount();
                            if($resultRowLength == 1){
                                //----値を次に進めた場合のみ値を返す
                                
                                if( $boolTrzAutoStart === true ){
                                    //----毎回コミットするモード
                                    $varCommit = $lcObjDBCA->transactionCommit();
                                    if( $varCommit === true ){
                                        $intLatelstValue = $intNowVal;
                                    }else{
                                        $intResultStatus=301;
                                        $aryMsgBody[] = "Commit error is occured.";
                                    }
                                    //毎回コミットするモード----
                                }else{
                                    //----コミットしないモード
                                    $intLatelstValue = $intNowVal;
                                    //コミットしないモード----
                                }
                                //値を次に進めた場合のみ値を返す----
                            }else{
                                $intResultStatus=201;
                                $aryMsgBody[] = "Effected row count[{$resultRowLength}] is not 1.";
                            }
                        }else{
                            $intResultStatus=105;
                            $aryMsgBody[] = "Updating sequence[{$strSeqName}] is failed.";
                        }
                        unset($objQuery2);
                    }else{
                        $intResultStatus=104;
                        $aryMsgBody[] = "Sequence[{$strSeqName}] not founded in [{$strSeqTable}].";
                    }
                }else{
                    $intResultStatus=102;
                    $aryMsgBody[] = "Select value error is occured.";
                    $aryMsgBody[] = $objQuery1->getLastError();
                }
            }
        }else{
            $intResultStatus=580;
            $aryMsgBody[] = "Transaction-start error is occured.";
        }
        
        if( $varTrzStart === true ){
            if( $boolTrzAutoStart === true ){
                //----毎回コミットするモード
                if( $intResultStatus == 0 ){
                }else{
                    $varRollBack = $lcObjDBCA->transactionRollBack();
                    if( $varRollBack === true ){
                    }else if( $varRollBack === false ){
                        $aryMsgBody[] = "Rollback error is occured.";
                    }
                }
                $varTrzExit = $lcObjDBCA->transactionExit();
                if( $varTrzExit === true ){
                }else{
                    $aryMsgBody[] = "Transaction-exit error is occured.";
                }
                //毎回コミットするモード----
            }else{
                //----コミットしないモード
                //コミットしないモード----
            }
        }
        
        unset($objQuery1);
        
        $retVal[0] = $intLatelstValue;
        $retVal[1] = $intResultStatus;
        $retVal[2] = $aryMsgBody;
        
        return $retVal;
    }

    function requestTypeAnalyze(){
        $arrayRet = array();
        //----$_SERVERは、バッチから呼んでも宣言されてしまうので分析
        if( isset($_SERVER) === true ){
            if( array_key_exists('HTTP_HOST', $_SERVER) === true ){
                $arrayRet[0] = "web";
            }else{
                $arrayRet[0] = "backyard";
            }
        }else{
            $arrayRet[0] = "unknowned";
        }
        $tmpDebugTrace = debug_backtrace();
        $tmpLastArray = end($tmpDebugTrace);
        $arrayRet[1] = $tmpLastArray['file'];
        return $arrayRet;
    }

    function getApplicationRootDirPath(){
        $root_dir_temp = array();
        $root_dir_temp = explode( "wwwroot", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "wwwroot";
        unset($root_dir_temp);
        return  $root_dir_path;

    }

    function makeSQLForUtnTableUpdate($lc_db_model_ch, $queryType, $utnRIColumnID, $utnTableID, $jnlTableID, $arrayKeyAndType, $arrayKeyAndValue, $aryVariant=array()){
        //----あくまで、UTNテーブルとJNLテーブルを同時に更新する用（そして、あくまでUTNテーブルのメンテがメイン。）
        //----IUDの場合は、ほぼほぼ「$arrayKeyAndValue」に値が入っていること前提。

        //セレクト文は、$arrayKeyAndType、調査しないこと
        $jnlRIColumnID = isset($aryVariant['TT_SYS_01_JNL_SEQ_ID'])?$aryVariant['TT_SYS_01_JNL_SEQ_ID']:"JOURNAL_SEQ_NO";
        $jnlACColumnID = isset($aryVariant['TT_SYS_02_JNL_TIME_ID'])?$aryVariant['TT_SYS_02_JNL_TIME_ID']:"JOURNAL_ACTION_CLASS";
        $jnlRDColumnID = isset($aryVariant['TT_SYS_03_JNL_CLASS_ID'])?$aryVariant['TT_SYS_03_JNL_CLASS_ID']:"JOURNAL_REG_DATETIME";

        $cmnLUDFUDSColumnID = isset($aryVariant['TT_SYS_05_LUP_TIME_ID'])?$aryVariant['TT_SYS_05_LUP_TIME_ID']:"LAST_UPDATE_TIMESTAMP";
        $cmnLUDFUNEColumnID = isset($aryVariant['TT_SYS_NDB_LUP_TIME_ID'])?$aryVariant['TT_SYS_NDB_LUP_TIME_ID']:"UPD_UPDATE_TIMESTAMP";

        $arraySQL = array();

        $boolRetResult = true;
        $boolExecute = true;

        $strDefaultAC = "";

        $queryUIDFlag = null;
        $strSQLForUTN = "";
        $strSQLForUTNWhere = "";
        $strSQLForUTNTail = "";
        
        $arrayUtnElement1 = array();
        $arrayUtnElement2 = array();

        $strSQLForJNL = "";
        $strSQLForJNLWhere = "";
        $strSQLForJNLTail = "";

        $strSQLForUTNIDs = array();
        $strSQLForUTNValues = array();

        $arrayJnlElement1 = array();
        $arrayJnlElement2 = array();

        $arrayBindForUTN = array();
        $arrayBindForJNL = array();

        $arrayStr = explode(" ", microtime());

        $strDateMcr = date("Y/m/d H:i:", $arrayStr[1]).(date("s",$arrayStr[1]) + $arrayStr[0]);
        $strDateTime = date("Y/m/d H:i:s", $arrayStr[1]);
        $strDateDate = date("Y/m/d", $arrayStr[1]);

        if( $boolExecute === true ){
            switch( $lc_db_model_ch ){
            case 0:

                $strToDBFxNameDateDate = "TO_DATE";
                $strToDBFormatDateDate = "YYYY/MM/DD HH24:MI:SS";
                $strFromDBFxNameDateDate = "TO_CHAR";
                $strFromDBFormatDateDate = "YYYY/MM/DD";

                $strToDBFxNameDateTime = "TO_TIMESTAMP";
                $strToDBFormatDateTime = "YYYY/MM/DD HH24:MI:SS.FF6";
                $strFromDBFxNameDateTime = "TO_CHAR";
                $strFromDBFormatDateTime = "YYYY/MM/DD HH24:MI:SS";
                $strFromDBFormatLUDFU = "YYYYMMDDHH24MISSFF6";
                
                $strDBSystemTime      = "SYSTIMESTAMP";
                break;
            case 1:

                $strToDBFxNameDateDate = "STR_TO_DATE";
                $strToDBFormatDateDate = "%Y/%m/%d %H:%i:%s";
                $strFromDBFxNameDateDate = "DATE_FORMAT";
                $strFromDBFormatDateDate = "%Y/%m/%d";
                
                $strToDBFxNameDateTime = "STR_TO_DATE";
                $strToDBFormatDateTime = "%Y/%m/%d %H:%i:%s.%f";
                $strFromDBFxNameDateTime = "DATE_FORMAT";
                $strFromDBFormatDateTime = "%Y/%m/%d %H:%i:%s";
                $strFromDBFormatLUDFU = "%Y%m%d%H%i%s%f";
                
                $strDBSystemTime      = "NOW(6)";
                break;
            default:
                $boolExecute = false;
                $boolRetResult = false; 
                break;
            }
        }

        if( $boolExecute === true ){
            switch( $queryType ){
            case "INSERT":
                $queryUIDFlag = true;
                $strSQLForUTNWhere = "";
                $strSQLForUTNTail = "";
                $strSQLForJNLWhere = "";
                $strSQLForJNLTail = "";
                $strDefaultAC = $queryType;
                $utnQueryHead = "INSERT INTO";
                $jnlQueryHead = "INSERT INTO";
                break;
            case "SELECT":
                $queryUIDFlag = false;
                $strSQLForUTNWhere = "WHERE {$utnRIColumnID} = :{$utnRIColumnID}";
                if( array_key_exists('WHERE', $aryVariant) === true ){
                    $strSQLForUTNWhere = "WHERE ".$aryVariant['WHERE'];
                    $strSQLForUTNWhere = str_replace(":KY_DB_DATETIME(6):",$strDBSystemTime,$strSQLForUTNWhere);
                }else{
                    if( array_key_exists($utnRIColumnID, $arrayKeyAndValue) === true ){
                        $arrayBindForUTN = array($utnRIColumnID=>$arrayKeyAndValue[$utnRIColumnID]);
                    }
                }
                $strSQLForUTNTail = "";
                $strSQLForJNLWhere = "";
                $strSQLForJNLTail = "";
                $strDefaultAC = "";
                $utnQueryHead = "SELECT";
                $jnlQueryHead = "";
                break;
            case "SELECT FOR UPDATE":
                //----SELECT-FOR-UPDATE用
                $queryUIDFlag = false;
                $strSQLForUTNWhere = "WHERE {$utnRIColumnID} = :{$utnRIColumnID}";
                if( array_key_exists('WHERE', $aryVariant) === true ){
                    $strSQLForUTNWhere = "WHERE ".$aryVariant['WHERE'];
                    $strSQLForUTNWhere = str_replace(":KY_DB_DATETIME(6):",$strDBSystemTime,$strSQLForUTNWhere);
                }else{
                    if( array_key_exists($utnRIColumnID, $arrayKeyAndValue) === true ){
                        $arrayBindForUTN = array($utnRIColumnID=>$arrayKeyAndValue[$utnRIColumnID]);
                    }
                }
                $strSQLForUTNTail = " FOR UPDATE";
                $strSQLForJNLWhere = "";
                $strSQLForJNLTail = "";
                $strDefaultAC = "";
                $utnQueryHead = "SELECT";
                $jnlQueryHead = "";
                break;
            case "UPDATE":
                $queryUIDFlag = true;
                $strSQLForUTNWhere = "WHERE {$utnRIColumnID} = :{$utnRIColumnID}";
                $strSQLForUTNTail = "";
                $strSQLForJNLWhere = "";
                $strSQLForJNLTail = "";
                $strDefaultAC = $queryType;
                $utnQueryHead = "UPDATE";
                $jnlQueryHead = "INSERT INTO";
                break;
            case "DELETE":
                //----物理削除
                $queryUIDFlag = true;
                $strSQLForUTNWhere = "WHERE {$utnRIColumnID} = :{$utnRIColumnID}";
                $strSQLForUTNTail = "";
                $strSQLForJNLWhere = "";
                $strSQLForJNLTail = "";
                $strDefaultAC = $queryType;
                $utnQueryHead = "DELETE";
                $jnlQueryHead = "INSERT INTO";
                break;
            default:
                $boolExecute = false;
                $boolRetResult = false; 
                break;
            }
        }

        if( $boolExecute === true ){
            if( $queryUIDFlag === false ){
                //----セレクト文の場合
                //セレクト文の場合----
            }else{
                //----UID文の場合
                foreach($arrayKeyAndType as $key => $type){
                    if( array_key_exists($key, $arrayKeyAndValue) === true ){
                        //----コンフィグ情報(配列)の中にあるカラムだけバインド準備
                        $arrayBindForUTN[$key] = $arrayKeyAndValue[$key];
                        $arrayBindForJNL[$key] = $arrayKeyAndValue[$key];
                    }              
                }
                //UID文の場合----
            }
        }

        if( $boolExecute === true ){
            //----コンフィグ配列にあるカラムIDの分だけループ
            foreach($arrayKeyAndType as $key => $type){
                if( $queryUIDFlag === true ){
                    //----UID文の場合
                    $tmpValue = "";
                    if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                        $tmpValue = $arrayKeyAndValue[$key];
                    }
                    //UID文の場合----
                }

                if( $key == $jnlRIColumnID ){
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        //最後にJNL付加
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                            //----UTNにあるはずがないので落とす
                            unset($arrayBindForUTN[$key]);
                            //UTNにあるはずがないので落とす----
                        }
                        
                        if( $tmpValue === "" ){
                            $arrayBindForJNL[$key] = $tmpValue;
                        }
                        
                        //----このグループはNULL禁止
                        $arrayJnlElement2[$key] = ":{$key}";
                        //このグループはNULL禁止---
                        
                        //UID文の場合----
                    }
                    continue;
                }else if( $key == $jnlACColumnID || $key == $jnlRDColumnID ){
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        //最後にJNL付加
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                            //----UTNにあるはずがないので落とす
                            unset($arrayBindForUTN[$key]);
                            //UTNにあるはずがないので落とす----
                        }
                        
                        if( $tmpValue == "SPECIAL-ORDER-NULL" ){
                            $arrayJnlElement2[$key] = "NULL";
                            if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                                unset($arrayBindForJNL[$key]);
                            }
                        }else{
                            if( $key == $jnlRDColumnID ){
                                $arrayJnlElement2[$key] = "{$strToDBFxNameDateTime}(:{$key},'{$strToDBFormatDateTime}')";
                            }else if( $key == $jnlACColumnID ){
                                $arrayJnlElement2[$key] = ":{$key}";
                            }
                            if( $tmpValue === "" ){
                                if( $key == $jnlRDColumnID ){
                                    $tmpValue = $strDateMcr;
                                    $arrayBindForJNL[$key] = $tmpValue;
                                }else if( $key == $jnlACColumnID ){
                                    $tmpValue = $strDefaultAC;
                                    $arrayBindForJNL[$key] = $tmpValue;
                                }
                            }
                        }
                        
                        //UID文の場合----
                    }
                    continue;
                }else if( $key == $utnRIColumnID ){
                    //----RIカラムの場合
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        $arrayUtnElement1[$key] = $key;
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( $queryType == "INSERT" ){
                            $arrayUtnElement2[$key] = ":{$key}";  
                        }
                        
                        //----このグループはNULL禁止
                        $arrayJnlElement2[$key] = ":{$key}";  
                        //このグループはNULL禁止----
                        
                        //UID文の場合----
                    }
                    continue;
                    //RIカラムの場合----
                }else if( $key == $cmnLUDFUDSColumnID ){
                    //----自動カラムなので、なにがし、かが入る
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        $arrayUtnElement1[$key] = "{$strFromDBFxNameDateTime}({$key},'{$strFromDBFormatDateTime}') {$key}";
                        
                        $strNullDataMark = "VALNULL";
                        if( array_key_exists('NDB_LUP_TIME_NULLARG', $aryVariant) === true )
                        {
                            $strNullDataMark = $aryVariant['NDB_LUP_TIME_NULLARG'];
                        }
                        
                        //----追い越し防止用カラムをおまけで
                        $strConnectString1 = makeStringConnectForSQLPart($lc_db_model_ch, array("'T_'","{$strFromDBFxNameDateTime}({$key},'{$strFromDBFormatLUDFU}')")); 
                        
                        $arrayUtnElement1[$cmnLUDFUNEColumnID] = "CASE WHEN {$key} IS NULL THEN '{$strNullDataMark}' ELSE {$strConnectString1} END {$cmnLUDFUNEColumnID}";
                        //追い越し防止用カラムをおまけで----
                        
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        
                        if( $tmpValue == "SPECIAL-ORDER-NULL" ){
                            $arrayUtnElement2[$key] = "NULL";
                            $arrayJnlElement2[$key] = "NULL";
                            if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                                unset($arrayBindForUTN[$key]);
                                unset($arrayBindForJNL[$key]);
                            }
                        }else{
                            $tmpValue = $strDateMcr;
                            $arrayBindForUTN[$key] = $tmpValue;
                            $arrayBindForJNL[$key] = $tmpValue;
                            $arrayUtnElement2[$key] = "{$strToDBFxNameDateTime}(:{$key},'{$strToDBFormatDateTime}')";
                            $arrayJnlElement2[$key] = "{$strToDBFxNameDateTime}(:{$key},'{$strToDBFormatDateTime}')";
                        }
                        //----追い越し防止用カラムがバインド元データにある場合
                        if( array_key_exists( $cmnLUDFUNEColumnID, $arrayKeyAndValue) === true ){
                            unset($arrayBindForUTN[$cmnLUDFUNEColumnID]);
                            unset($arrayBindForJNL[$cmnLUDFUNEColumnID]);
                        }
                        //追い越し防止用カラムがバインド元データにある場合----
                        //
                        //UID文の場合----
                    }
                    continue;
                    //自動カラムなので、なにがし、かが入る----
                }else if( $key == $cmnLUDFUNEColumnID ){
                    //----追越防止用カラムの場合
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        //ソースカラムがある場合に
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( array_key_exists( $cmnLUDFUNEColumnID, $arrayKeyAndValue) === true ){
                            unset($arrayBindForUTN[$cmnLUDFUNEColumnID]);
                            unset($arrayBindForJNL[$cmnLUDFUNEColumnID]);
                        }
                        //UID文の場合----
                    }
                    continue;
                    //追越防止用カラムの場合----
                }

                switch( $type ){
                case "DATEDATE":
                case "DATEDATEAUTO":
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        $arrayUtnElement1[$key] = "{$strFromDBFxNameDateDate}({$key},'{$strFromDBFormatDateDate}') {$key}";
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( $type == "DATEDATEAUTO" ){
                            $tmpValue = $strDateDate;
                            $arrayBindForUTN[$key] = $tmpValue;
                            $arrayBindForJNL[$key] = $tmpValue;
                        }else{
                            //----置き換え
                            if( $tmpValue == "DATEDATEAUTO" ){
                                $tmpValue = $strDateDate;
                                $arrayBindForUTN[$key] = $tmpValue;
                                $arrayBindForJNL[$key] = $tmpValue;
                            }
                            //置き換え----
                        }
                        
                        if( $tmpValue === "" ){
                            $arrayUtnElement2[$key] = "NULL";
                            $arrayJnlElement2[$key] = "NULL";
                            if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                                unset($arrayBindForUTN[$key]);
                                unset($arrayBindForJNL[$key]);
                            }
                        }else{
                            $arrayUtnElement2[$key] = "{$strToDBFxNameDateDate}(:{$key},'{$strToDBFormatDateDate}')";
                            $arrayJnlElement2[$key] = "{$strToDBFxNameDateDate}(:{$key},'{$strToDBFormatDateDate}')";
                        }
                        
                        //UID文の場合----
                    }
                    break;
                case "DATETIME":
                case "DATETIMEAUTO":
                case "DATETIMEAUTO(6)":
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        $arrayUtnElement1[$key] = "{$strFromDBFxNameDateTime}({$key},'{$strFromDBFormatDateTime}') {$key}";
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( $tmpValue === "" ){
                            if( $type == "DATETIMEAUTO" ){
                                $tmpValue = $strDateTime;
                                $arrayBindForUTN[$key] = $tmpValue;
                                $arrayBindForJNL[$key] = $tmpValue;
                            }else if( $type == "DATETIMEAUTO(6)" ){
                                $tmpValue = $strDateMcr;
                                $arrayBindForUTN[$key] = $tmpValue;
                                $arrayBindForJNL[$key] = $tmpValue;
                            }
                        }else{
                            //----置き換え
                            if( $tmpValue == "DATETIMEAUTO" ){
                                $tmpValue = $strDateTime;
                                $arrayBindForUTN[$key] = $tmpValue;
                                $arrayBindForJNL[$key] = $tmpValue;
                            }else if( $tmpValue == "DATETIMEAUTO(6)" ){
                                $tmpValue = $strDateMcr;
                                $arrayBindForUTN[$key] = $tmpValue;
                                $arrayBindForJNL[$key] = $tmpValue;
                            }
                            //置き換え----
                        }
                        
                        if( $tmpValue === "" ){
                            $arrayUtnElement2[$key] = "NULL";
                            $arrayJnlElement2[$key] = "NULL";
                            if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                                unset($arrayBindForUTN[$key]);
                                unset($arrayBindForJNL[$key]);
                            }
                        }else{
                            $arrayUtnElement2[$key] = "{$strToDBFxNameDateTime}(:{$key},'{$strToDBFormatDateTime}')";
                            $arrayJnlElement2[$key] = "{$strToDBFxNameDateTime}(:{$key},'{$strToDBFormatDateTime}')";
                        }
                        //UID文の場合----
                    }
                    break;
                default:
                    if( $queryUIDFlag === false ){
                        //----セレクト文の場合
                        $arrayUtnElement1[$key] = "{$key}";
                        //セレクト文の場合----
                    }else{
                        //----UID文の場合
                        if( $tmpValue === "" ){
                            $arrayUtnElement2[$key] = "NULL";
                            $arrayJnlElement2[$key] = "NULL";
                            if( array_key_exists( $key, $arrayKeyAndValue) === true ){
                                unset($arrayBindForUTN[$key]);
                                unset($arrayBindForJNL[$key]);
                            }
                        }else{
                            $arrayUtnElement2[$key] = ":{$key}";
                            $arrayJnlElement2[$key] = ":{$key}";
                        }
                        
                        //UID文の場合----
                    }
                    break;
                }
            }
            //コンフィグ配列にあるカラムIDの分だけループ----
        }

        if( $boolExecute === true ){
            if( $queryUIDFlag === false ){
                //----セレクト文の場合
                
                $strSQLForUTN = $utnQueryHead." ";
                $arrayUtnValues = array_values($arrayUtnElement1);
                $strSQLForUTN .= implode(",",$arrayUtnValues);
                $strSQLForUTN .= " FROM ".$utnTableID;
                $strSQLForUTN .= " ".$strSQLForUTNWhere.$strSQLForUTNTail;
                
                //セレクト文の場合----
            }else{
                //----UID文の場合
                $strSQLForUTN = $utnQueryHead." ".$utnTableID;
                $arrayUtnKeys = array_keys($arrayUtnElement2);
                $arrayUtnValues = array_values($arrayUtnElement2);
                
                switch( $queryType ){
                case "INSERT":
                    $strSQLForUTN .= " (".implode(",",$arrayUtnKeys).")";
                    $strSQLForUTN .= " VALUES(".implode(",",$arrayUtnValues).")";
                    break;
                case "UPDATE":
                    $arrayTemp = array();
                    foreach( $arrayUtnElement2 as $tmpKey=>$tmpVal ){
                        $arrayTemp[] = "{$tmpKey} = {$tmpVal}";
                    }
                    $strSQLForUTN .= " SET ".implode(",",$arrayTemp);
                    break;
                case "DELETE":
                    //----UTNへのバインドを掃除
                    foreach( $arrayBindForUTN as $tmpKey=>$tmpVal ){
                        if( $utnRIColumnID == $tmpKey ){
                        }else{
                            unset($arrayBindForUTN[$tmpKey]);
                        }
                    }
                    //UTNへのバインドを掃除----
                    break;
                default:
                    $boolRetResult = false; 
                    break;
                }

                $strSQLForUTN .= " ".$strSQLForUTNWhere.$strSQLForUTNTail;

                $strSQLForJNL = $jnlQueryHead." ".$jnlTableID;
                $arrayJnlKeys = array_keys($arrayJnlElement2);
                $arrayJnlValues = array_values($arrayJnlElement2);
                $strSQLForJNL .= " (".implode(",",$arrayJnlKeys).")";
                $strSQLForJNL .= " VALUES(".implode(",",$arrayJnlValues).")";
                $strSQLForJNL .= " ".$strSQLForJNLWhere.$strSQLForJNLTail;

                //UID文の場合----
            }
            
        }else{
        }

        $arraySQL[0] = $boolRetResult;
        
        $arraySQL[1] = $strSQLForUTN;
        
        $arraySQL[2] = $arrayBindForUTN;

        $arraySQL[3] = $strSQLForJNL;
        
        $arraySQL[4] = $arrayBindForJNL;
        
        return $arraySQL;
    }

    function makeSelectSQLPartForDateWildColumn($lc_db_model_ch,$strColumn,$strDateType,$boolAddMicro=false,$boolLikeIntMode=false){
        return makeConvToStrSQLPartForDateWildColumn($lc_db_model_ch, $strColumn, $strDateType, $boolAddMicro, $boolLikeIntMode);
    }

    function makeConvToDateSQLPartForDateWildColumn($lc_db_model_ch,$strColumn,$strDateType,$boolAddMicro=false,$boolLikeIntMode=false){
        $retStrBody="";
        $strFxName="";
        switch( $lc_db_model_ch ){
            case 0:
                $strFxName="TO_DATE";
                if( $strDateType == "DATETIME" && $boolAddMicro === true ) $strFxName="TO_TIMESTAMP";
                break;
            case 1:
                $strFxName="STR_TO_DATE";
                break;
        }
        if( $strFxName != "" ){
            $retStrBody = makeSQLPartForDateWildColumn($lc_db_model_ch, $strColumn, $strDateType, $boolAddMicro, $boolLikeIntMode, $strFxName);
        }
        return $retStrBody;
    }

    function makeConvToStrSQLPartForDateWildColumn($lc_db_model_ch,$strColumn,$strDateType,$boolAddMicro=false,$boolLikeIntMode=false){
        $retStrBody="";
        $strFxName="";
        switch( $lc_db_model_ch ){
            case 0:$strFxName="TO_CHAR";break;
            case 1:$strFxName="DATE_FORMAT";break;
        }
        if( $strFxName != "" ){
            $retStrBody = makeSQLPartForDateWildColumn($lc_db_model_ch, $strColumn, $strDateType, $boolAddMicro, $boolLikeIntMode, $strFxName);
        }
        return $retStrBody;
    }

    function makeSQLPartForDateWildColumn($lc_db_model_ch,$strColumn,$strDateType,$boolAddMicro=false,$boolLikeIntMode=false,$strFxName=""){
        $retStrBody="";
        switch( $lc_db_model_ch ){
        case 0:
            // オラクル
            if($strDateType=="DATEDATE"){
                $strFormat="YYYY/MM/DD HH24:MI:SS";
            }else if($strDateType=="DATETIME"){
                $strFormat="YYYY/MM/DD HH24:MI:SS";
                if($boolAddMicro===true) $strFormat.=".FF6";
            }else if($strDateType=="DATE_YMD"){
                $strFormat="YYYY/MM/DD";
            }
            break;
        case 1:
            // mySQL/mariaDB
            if($strDateType=="DATEDATE"){
                $strFormat="%Y/%m/%d %H:%i:%s";
            }else if($strDateType=="DATETIME"){
                $strFormat="%Y/%m/%d %H:%i:%s";
                if($boolAddMicro===true) $strFormat.=".%f";
            }else if($strDateType=="DATE_YMD"){
                $strFormat="%Y/%m/%d";
            }
            break;
        default:
            break;
        }
        if( $strFxName != "" ){
            if( $boolLikeIntMode === true )
            {
                $strFormat = str_replace("/","",$strFormat);
                $strFormat = str_replace(":","",$strFormat);
                $strFormat = str_replace(" ","",$strFormat);
            }
            $retStrBody = "{$strFxName}({$strColumn},'{$strFormat}')";
        }
        return $retStrBody;
    }

    // ----ここから業務色を排除した汎用系関数

    function makeStringConnectForSQLPart($lc_db_model_ch,$aryElement){
        $retStrBody="";
        $boolRecept=false;
        switch( $lc_db_model_ch ){
        case 0:
            $boolRecept=true;
            $strConcatHead = "";
            $strConcatMid = " || ";
            $strConcatTail = "";
            break;
        case 1:
            $boolRecept=true;
            $strConcatHead = "CONCAT(";
            $strConcatMid = ",";
            $strConcatTail = ")";
            break;
        case 1:
            // mySQL/mariaDB
            break;
        default:
            break;
        }
        if( $boolRecept===true ){
            if( count($aryElement) ==1 ){
                if(is_string($aryElement)===true){
                    $retStrBody=$aryElement;
                }else if(is_array($aryElement)===true){
                    $retStrBody=$aryElement[0];
                }
            }else if(2<=count($aryElement)){
                if(is_array($aryElement)===true){
                    $retStrBody = $strConcatHead;
                    $retStrBody .= implode($strConcatMid,$aryElement);
                    $retStrBody .= $strConcatTail;
                }
            }
        }
        return $retStrBody;
    }

    function searchNullByte($strCheckTarget, $strEncode="utf-8"){
        $retBoolValue = false;
        $strTestBody = str_replace("\0" , "" , $strCheckTarget);
        if( mb_strlen($strTestBody, $strEncode) == mb_strlen($strCheckTarget, $strEncode) ){
        }else{
            // 存在していた
            $retBoolValue = true;
        }
        return $retBoolValue;
    }

    function getSystemMaxSizeOfUploadfile(){
        $strFirstResult = (getByteFromIni('post_max_size') < getByteFromIni('upload_max_filesize') )?'post_max_size' : 'upload_max_filesize';
        $strSecondResult = (getByteFromIni($strFirstResult) < getByteFromIni('memory_limit'))?$strFirstResult : 'memory_limit';
        return getByteFromIni($strSecondResult);
    }

    function getByteFromIni($strVarname) {
        //----$strVarname(memory_limit,post_max_size,upload_max_filesize)
        $val = ini_get($strVarname);
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // 'G' は PHP 5.1.0 以降で使用可能です
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    function ky_include_path_add($strAddPathSource, $intMode=0, $boolRootSet=false){
        $retBool = false;
        $boolExecuteContinue = true;
        switch($intMode){
            case 1:
                $strAddPathRaw = file_get_contents($strAddPathSource);
                break;
            default:
                $strAddPathRaw = $strAddPathSource;
                break;
        }
        if( strlen($strAddPathRaw) === 0 && $boolRootSet !== true ){
            $boolExecuteContinue = false;
        }
        
        if($boolExecuteContinue === true){
            $strAddTgtPath = rtrim($strAddPathRaw,'/');
            $strBeforePath = get_include_path();
            $aryPath = explode(PATH_SEPARATOR, $strBeforePath);
            $boolExists = false;
            foreach($aryPath as $strValue){
                if( rtrim($strValue,'/') === $strAddTgtPath ){
                    //----最後のスラッシュを取って比較合致した場合
                    $boolExecuteContinue = false;
                    break;
                    //最後のスラッシュを取って比較合致した場合----
                }
            }
        }
        if( $boolExecuteContinue === true ){
            $retBool = set_include_path($strBeforePath.PATH_SEPARATOR.$strAddTgtPath);
        }
        return $retBool;
    }

    // ----簡易暗号化・復号化ファンクション
    function ky_encrypt($lcStr){
        // 暗号化
        return str_rot13(base64_encode($lcStr));
    }

    function ky_decrypt($lcStr){
        // 復号化
        return base64_decode(str_rot13($lcStr));
    }
    // 簡易暗号化・復号化ファンクション----

    function ky_phpProcessSleep($lcIntSec){
        // 簡易スリープ
        $lcIntStartTimeSec = time();
        do{
        } while(time() < $lcIntStartTimeSec + $lcIntSec);
        return true;
    }


    function getMircotime($mode=0){
        //----$mode[0:Unixtimestamp/1:YmdHis/2:Y/m/d H:i:s]
        $strFormat = "";
        $arrayStr = explode(" ", microtime());
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $arrayStr[1].".".substr(str_replace("0.","",$arrayStr[0]),0,6);
        }else{
            $sec = date("s",$arrayStr[1]) + $arrayStr[0];
            $ret = date($strFormat, $arrayStr[1]).$sec;
        }
        return $ret;
    }

    function convFromStrDateToUnixtime($str,$boolPlusMirco=false){
        //----$str[YYYYMMDDNNSS(.000000)||YYYY/MM/DD HH:NN:SS(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        $array = explode(".", $str);
        $intTime = strtotime($array[0]);
        $decTime = "";
        if($boolPlusMirco === true){
            if( isset($array[1]) === true ){
                $decTime = ".".sprintf('%06d', $array[1]);
            }else{
                $decTime = ".000000";
            }
        }
        $ret = $intTime.$decTime;
        return $ret;
    }
    
    function convFromUnixtimeToStrDate($str,$boolPlusMirco=false,$mode=0){
        //----$str[unixtimestamp(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        //----$mode[0:Unixtimestamp||1:YmdHis||2:Y/m/d H:i:s]
        $strFormat = "";
        if($boolPlusMirco === true){
            $array = explode(".", $str);
            $intTime = date($array[0]);
            $decTime = ".".sprintf('%06d', $array[1]);
        }else{
            $intTime = $str;
            $decTime = ".000000";
        }
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $intTime.$decTime;
        }else{
            $sec = date("s",$intTime).$decTime;
            $ret = date($strFormat, $intTime).$sec;
        }
        return $ret;
    }

    function ky_devStrToBinary($lcStrValue,$boolSepaAdd=true){
         // 開発補助用：16進文字列化
         $lcStrHexStream = bin2hex($lcStrValue);
         if( $boolSepaAdd === true ){
             $lcArrayHex = str_split($lcStrHexStream,2);
             $lcStrBody = implode(":",$lcArrayHex);
         }else{
             $lcStrBody = $lcStrHexStream;
         }
         return $lcStrBody;
    }


    // ここまで業務色を排除した汎用系関数----

    // ----ここから追加標準関数を読み込み
    if(file_exists(dirname(__FILE__)."/common_php_functions_add.php")){
        require_once(dirname(__FILE__)."/common_php_functions_add.php");
    }
    // ここまで追加標準関数を読み込み----

?>
