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
//  インポートRestAPI (UPDATE　EXECUTE)
//////////////////////////////////////////////////////////////////
function symopeExportFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

    global $g;

    $g['upload_id'] =  date('YmdHis') . mt_rand();

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
            case "UPLOAD":
                $aryRetBody = symopeImportUploadFromRest($objJSONOfReceptedData);
                break;

            case "EXECUTE":
                
                //ファイル、パスの設定  
                $importPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/' . $objJSONOfReceptedData['upload_file_name'];

                if( file_exists($importPath) ){
                   $aryRetBody = symopeImportExecutionFromRest($objJSONOfReceptedData);                
                }else{
                    $aryRetBody["TASK_ID"] = "";
                    $aryRetBody["RESULTCODE"] = "002";
                    $aryRetBody['RESULTINFO'] = $g['objMTS']->getSomeMessage("ITABASEH-ERR-900073"); 
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

//////////////////////////////////////////////////////////////////
//  インポート登録処理
//////////////////////////////////////////////////////////////////
function symopeImportExecutionFromRest($objJSONOfReceptedData){

    global $g;

    $arrayResult = array();
    $resultMsg = "";
    $resultFlg = "";
    $intResultCode="";

    $errflg="";
    $chkidlist=array();
    $tmparray = array();
    $tmpJSONOfReceptedData=array();

    //指数表記対応
    $_SESSION['upload_id']    = str_replace( "A_", "", $objJSONOfReceptedData['upload_id'] );
    //アップロードしたファイル名($_SESSION 利用関数対応)
    $_SESSION['upload_file_name']      = $objJSONOfReceptedData['upload_file_name'];

    //ID（UPLOAD＋展開したファイル）インポート可能
    $retImportAry = makeImportCheckbox();

    //ID（RESTパラメータ）データ成形
    $chkidlist['operation'] = explode(",", $objJSONOfReceptedData['operation'][0]);
    $chkidlist['symphony'] = explode(",", $objJSONOfReceptedData['symphony'][0]);

    //オペレーションIDチェック
    foreach ($chkidlist['operation'] as $key => $value) {
        if( is_numeric($value) === true ){
                if ( !array_key_exists($value, $retImportAry[0]) )$errflg=1;
        }else{
            $errflg=1;
        }
    }
    //SymphonyIDチェック
    foreach ($chkidlist['symphony'] as $key => $value) {
        if( is_numeric($value) === true ){
                if ( !array_key_exists($value, $retImportAry[1]) )$errflg=1;
        }else{
            $errflg=1;
        }

    }

    //IDチェック判定結果
    if( $errflg != "" ){
        $arrayResult["TASK_ID"] = "";
        $arrayResult["RESULTCODE"] = "002";
        $arrayResult['RESULTINFO'] = $g['objMTS']->getSomeMessage("ITABASEH-ERR-900076");

        return $arrayResult; 
    }

    //パラメータ成形
    $tmpJSONOfReceptedData=array(
        'import_ope_' => $objJSONOfReceptedData['operation'],
        'import_sym_' => $objJSONOfReceptedData['symphony']
    );

    //($_POST 利用関数対応)
    $_POST = $tmpJSONOfReceptedData;

    try {

            $uploadId = $_SESSION['upload_id'];

            //パラメータからインポート対象のIDリストを作成
            $targetList = makeImportIdList();

            // トランザクション開始
            $varTrzStart = $g['objDBCA']->transactionStart();
            if ($varTrzStart === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900015',
                                                     array(basename(__FILE__), __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }

            // データ登録
            insertTask($targetList, $taskNo, $jnlSeqNo);

            $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900060', array($taskNo));
            $_SESSION['data_import_task_no'] = $taskNo;

            moveImportFile($taskNo, $jnlSeqNo);

            $res = $g['objDBCA']->transactionCommit();
            if ($res === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900036',
                                                     array(basename(__FILE__), __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }

            $resultFlg = true;
            $intResultCode= "000";
            unset($_SESSION['upload_id']);

        } catch (Exception $e) {
            web_log($e->getMessage());
            $res = $g['objDBCA']->transactionExit();
            if ($res === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900050', array(__FILE__, __LINE__)));
            }
            $resultMsg = $e->getMessage();
            $resultFlg = false;
            $intResultCode= "002";      
        }

        $arrayResult["TASK_ID"] = $taskNo;
        $arrayResult["RESULTCODE"] = $intResultCode;
        $arrayResult['RESULTINFO'] = strip_tags(trim($resultMsg));
    
        return $arrayResult; 
}

//////////////////////////////////////////////////////////////////
//  アップロード処理
//////////////////////////////////////////////////////////////////
function symopeImportUploadFromRest($objJSONOfReceptedData){

    global $g;

    $arrayResult = array();
    $resultMsg = "";
    $resultFlg = "";
    $intResultCode="";

    //($_SESSION 利用関数対応)
    $_SESSION['upload_id'] = $g['upload_id'];
    $_SESSION['upload_file_name'] =$objJSONOfReceptedData['zipfile']['name'];

    try {

        // ファイルアップロード
        decodeZipFile($objJSONOfReceptedData);

        // zip解凍
        unzipImportData($_SESSION['upload_file_name']);

        // zipファイルの中身確認
        checkZipFile($_SESSION['upload_file_name']);

        //メニューリストの取得
        $retImportAry = makeImportCheckbox();

        $resultFlg = true;
        $intResultCode= "000";

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg =  $e->getMessage();
        $retImportAry =  $e->getMessage();
        $resultFlg = true;
        $intResultCode= "001";

    }

    $arrayResult["upload_id"] = $g['upload_id'];
    $arrayResult["upload_file_name"] = $objJSONOfReceptedData['zipfile']['name'];
    if( $intResultCode == "000" )$arrayResult["IMPORT_LIST"] = $retImportAry;
    $arrayResult["RESULTCODE"] = $intResultCode;
    $arrayResult['RESULTINFO'] = strip_tags(trim($resultMsg));


    return $arrayResult; 

}
//////////////////////////////////////////////////////////////////
//  ファイルbase64変換処理
//////////////////////////////////////////////////////////////////
function decodeZipFile($objJSONOfReceptedData){
    global $g;
    $tmpJSONOfReceptedData = $objJSONOfReceptedData;
    //ファイル、パスの設定
    $uploadId = $g['upload_id'];
    $fileName = $uploadId . '_ita_data.tar.gz';
    $fileName = $tmpJSONOfReceptedData['zipfile']['name'];    
    $uploadFilePath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/' . $fileName;
    $uploadPath = $g['root_dir_path'] . '/temp/sym_ope_import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/sym_ope_import/import/';



    //一時ファイルへ保存
    $tmp_name=md5(uniqid(rand(), true));
    $tmp_dir = $g['root_dir_path'] . "/temp/" . $tmp_name;
    $tmp_decodedata = base64_decode($tmpJSONOfReceptedData['zipfile']['base64']);
    file_put_contents($tmp_dir, $tmp_decodedata);

    //ファイルタイプの取得、判定
    $tmp_type = finfo_buffer(finfo_open(), $tmp_decodedata, FILEINFO_MIME_TYPE);
    if(  $tmp_type  != "application/x-gzip"){
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900005'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // ファイル移動
    if ( rename( $tmp_dir, $uploadFilePath )  === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }


    return;
}

?>
