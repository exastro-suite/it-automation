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

//////////////////////////////////////////////////////////////////
//  　エクスポートRestAPI (INFO　EXECUTE)
//////////////////////////////////////////////////////////////////
function symopeExportFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayRetBody = array();
    
    $intResultStatusCode = null;
    $aryForResultData = array();
    $aryPreErrorData = null;
    
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $strSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $strSysErrMsgBody = '';
    $intErrorPlaceMark = "";
    $strErrorPlaceFmt = "%08d";
    
    $aryOverrideForErrorData = array();

    $intResultInfoCode="000";//結果コード(正常終了)

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        
        try{
            //X-command毎の処理 
            switch($strCommand){
                case "INFO":
                        $aryRetBody['LIST']['symphony'] = makeExportSymList("");
                        $aryRetBody['LIST']['operation'] = makeExportOpeList("");
                    break;

                case "EXECUTE":
                    //エクスポート対象の確認
                    $chkflg = validateExportListNo($objJSONOfReceptedData);

                    if ( $chkflg == "" ) {
                        $SymphonyClassList = $objJSONOfReceptedData['symphony'][0];
                        $OperationClassList = $objJSONOfReceptedData['operation'][0];

                        $taskNo = registerExportInfo($SymphonyClassList, $OperationClassList);

                        $aryRetBody['TASK_ID'] = $taskNo;
                        $aryRetBody['RESULTCODE'] = $intResultInfoCode;
                        $aryRetBody['RESULTINFO'] = "";

                    }else{
                        $aryRetBody['TASK_ID'] = "";
                        $aryRetBody['RESULTCODE'] = "002";
                        $aryRetBody['RESULTINFO'] = $g['objMTS']->getSomeMessage('ITABASEH-ERR-900075');
                    }
                    break;

            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        $aryForResultData['resultdata']=$aryRetBody;

    }
    catch (Exception $e){
        // 失敗時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];
        foreach($aryOverrideForErrorData as $strKey=>$varVal){
            $aryForResultData[$strKey] = $varVal;
        }
        if( 0 < strlen($strExpectedErrMsgBodyForUI) ){
            $aryPreErrorData[] = $strExpectedErrMsgBodyForUI;
        }
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        if( $intResultStatusCode === null ) $intResultStatusCode = 500;
        if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}

//////////////////////////////////////////
// エクスポート可否判定（SYMPHONYクラス,オペレーション）  //
//////////////////////////////////////////
function validateExportListNo($objJSONOfReceptedData){
    global $g;

    $chkflag="";
    $retSymNo ="";
    $retOpeNo ="";
    $chkidlist=array();

    //IDチェックデータ成形
    $chkidlist['operation'] = explode(",", $objJSONOfReceptedData['operation'][0]);
    $chkidlist['symphony']  = explode(",", $objJSONOfReceptedData['symphony'][0]);

    //オペレーションIDチェック
    foreach ($chkidlist['operation'] as $key => $value) {
        if( is_numeric($value) === true ){
                $retOpeNo = makeExportOpeList($value);
                if( $retOpeNo === false )$chkflag="1";
        }else{
            $chkflag="1";
        }

    }
    //SymphonyIDチェック
    foreach ($chkidlist['symphony'] as $key => $value) {
        if( is_numeric($value) === true ){
            $retSymNo = makeExportSymList($value);
            if( $retSymNo === false )$chkflag="1";
        }else{
            $chkflag="1";
        }

    }

    return $chkflag;
}

//////////////////////////////////////////
//エクスポート可能なSYMPHONYクラスの情報取得  //
//////////////////////////////////////////
function makeExportSymList($objPkey){
    global $g;

    $sql =        " SELECT                                         \n " ;
    $sql = $sql . "  SYMPHONY_CLASS_NO,                            \n " ;
    $sql = $sql . "  SYMPHONY_NAME,                                \n " ;
    $sql = $sql . "  DESCRIPTION,                                  \n " ;
    $sql = $sql . "  NOTE,                                         \n " ;
    $sql = $sql . "  DISUSE_FLAG,                                  \n " ;
    $sql = $sql . "  LAST_UPDATE_TIMESTAMP,                        \n " ;
    $sql = $sql . "  LAST_UPDATE_USER                              \n " ;
    $sql = $sql . " FROM                                           \n " ;
    $sql = $sql . "  C_SYMPHONY_CLASS_MNG                          \n " ;
    if($objPkey != ""){
        $sql = $sql . " WHERE                                      \n " ;
        $sql = $sql . "  SYMPHONY_CLASS_NO = :SYMPHONY_CLASS_NO    \n " ;        
    }
    $sql = $sql . " ORDER BY                                       \n " ;
    $sql = $sql . "   SYMPHONY_CLASS_NO ASC                        \n " ;


    $objQuery = $g['objDBCA']->sqlPrepare($sql);

    if($objQuery->getStatus()===false){
        web_log($objQuery->getLastError());
        return false;
    }
    if($objPkey != "")$objQuery->sqlBind( array('SYMPHONY_CLASS_NO'=>$objPkey));
    $r = $objQuery->sqlExecute($sql);

    if (!$r){
        web_log($objQuery->getLastError());

        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    // レコード無しの場合
    if( $num_of_rows < 1 ){
        unset($objQuery);
        return false;
    }
    $rows = $objQuery->resultFetchALL();

    return $rows;


}

//////////////////////////////////////////
// エクスポート可能なオペレーションの情報取得  //
//////////////////////////////////////////
function makeExportOpeList($objPkey){
    global $g;

    $sql =        " SELECT                                         \n " ;
    $sql = $sql . "  OPERATION_NO_UAPK,                            \n " ;
    $sql = $sql . "  OPERATION_NAME,                               \n " ;
    $sql = $sql . "  OPERATION_DATE,                               \n " ;
    $sql = $sql . "  LAST_EXECUTE_TIMESTAMP,                       \n " ;
    $sql = $sql . "  NOTE,                                         \n " ;
    $sql = $sql . "  DISUSE_FLAG,                                  \n " ;
    $sql = $sql . "  LAST_UPDATE_TIMESTAMP,                        \n " ;
    $sql = $sql . "  LAST_UPDATE_USER                              \n " ;
    $sql = $sql . " FROM                                           \n " ;
    $sql = $sql . "  C_OPERATION_LIST                          \n " ;
    if($objPkey != ""){
        $sql = $sql . " WHERE                                      \n " ;
        $sql = $sql . "  OPERATION_NO_UAPK = :OPERATION_NO_UAPK    \n " ;        
    }
    $sql = $sql . " ORDER BY                                       \n " ;
    $sql = $sql . "   OPERATION_NO_UAPK ASC                        \n " ;

    $objQuery = $g['objDBCA']->sqlPrepare($sql);

    if($objQuery->getStatus()===false){
        web_log($objQuery->getLastError());
        return false;
    }
    $objQuery->sqlBind( array('OPERATION_NO_UAPK'=>$objPkey));
    $r = $objQuery->sqlExecute($sql);

    if (!$r){
        web_log($objQuery->getLastError());

        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    // レコード無しの場合
    if( $num_of_rows < 1 ){
        unset($objQuery);
        return false;
    }
    $rows = $objQuery->resultFetchALL();

    return $rows;
     
}

?>
