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
//  メニューインポートRestAPI (INFO　EXECUTE)
//////////////////////////////////////////////////////////////////
function menuImportFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

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

    // メニューIDの桁数
    define('MENU_ID_LENGTH', 11);
    // インポートファイル一つに保存するレコード数
    define('MAX_RECORD_CNT', 1000);
    //ログインID取得
    define('ACCOUNT_NAME', $g['login_id']);

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    try{
        //X-command毎の処理
        switch($strCommand){
            case "UPLOAD":
                $aryRetBody = menuImportUploadFromRest($objJSONOfReceptedData);
                break;

            case "EXECUTE":

                //ファイル、パスの設定
                $dirName = str_replace( "A_", "", $objJSONOfReceptedData['upload_id'] );// . '_ita_data.tar.gz';
                $importPath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $dirName;

                if( file_exists($importPath) ){
                    $aryRetBody = menuImportExecutionFromRest($objJSONOfReceptedData);
                }else{
                    $aryRetBody["TASK_ID"] = "";
                    $aryRetBody["RESULTCODE"] = "002";
                    $aryRetBody['RESULTINFO'] = $g['objMTS']->getSomeMessage("ITABASEH-ERR-2100000330_13");
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
//  メニューインポート登録処理
//////////////////////////////////////////////////////////////////
function menuImportExecutionFromRest($objJSONOfReceptedData){

    global $g;

    $arrayResult = array();
    $resultMsg = "";
    $resultFlg = "";
    $intResultCode="";

    $tmparray = array();

    $tmpJSONOfReceptedData = $objJSONOfReceptedData;

    //指数表記対応
    $_SESSION['upload_id']    = str_replace( "A_", "", $tmpJSONOfReceptedData['upload_id'] );
    //アップロードしたファイル名($_SESSION 利用関数対応)
    $_SESSION['data_portability_upload_file_name']      = $tmpJSONOfReceptedData['data_portability_upload_file_name'];

    //不要な要素の削除
    unset($tmpJSONOfReceptedData['no']);
    unset($tmpJSONOfReceptedData['post_kind']);
    unset($tmpJSONOfReceptedData['menu_on']);
    unset($tmpJSONOfReceptedData['upload_id']);
    unset($tmpJSONOfReceptedData['data_portability_upload_file_name']);

    $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));

    //メニューidをint型からstring型へ変換
    // メニューidの判定
    foreach ($tmpJSONOfReceptedData as $key => $value) {
        foreach ($value as $value2) {
            if (!is_int($value2) && strlen($value2) == 10) {
                $value2 = (int)$value2;
                $importableMenuId = chkImpotableMenuId($key, $value2, ACCOUNT_NAME);
                if ($importableMenuId != false) {
                    $tmparray[$key][]= (string)sprintf('%010d', $importableMenuId);
                }
            }
        }
    }
    if (empty($tmparray)) {
        //メニューID不正時
        $arrayResult["TASK_ID"] = "";
        $arrayResult["RESULTCODE"] = "002";
        $arrayResult['RESULTINFO'] = $g['objMTS']->getSomeMessage("ITABASEH-ERR-900071",$objIntNumVali->getValidRule());
        return $arrayResult;
    }

    //($_POST 利用関数対応)
    $_POST = $tmparray;
    class DBException extends Exception{}
    try {
            $uploadId = $_SESSION['upload_id'];
            $taskNo = null;
            // 入力値チェック
            $requestAry = $_POST;
            //メニューグループ、IDチェック
            foreach ($requestAry as $menuGroupId =>$menuIds) {
                if (ctype_digit($menuGroupId) === false || strlen($menuGroupId) > MENU_ID_LENGTH) {
                    $errFlg = 1;
                }
                foreach ($menuIds as $menuId) {
                    if (ctype_digit($menuId) === false || strlen($menuId) > MENU_ID_LENGTH) {
                        $errFlg = 1;
                    }
                }
            }

            // POSTされたメニューIDリストを作成
            makeImportMenuIdList();

            // データ登録
            $taskNo = insertBulkExcelTask();

            $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900009', array($taskNo));

            renameImportFiles($taskNo);

            moveZipFile($taskNo);

            $dirPath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $uploadId;
            removeFiles($dirPath);;

            $filePath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $taskNo . '_ita_data.zip';
            if (file_exists($filePath) === true) {
                unlink($filePath);
            }

            $resultFlg = true;
            $intResultCode= "000";

        } catch (Exception $e) {

            $resultMsg = $e->getMessage();
            $resultFlg = true;
            $intResultCode= "002";

        }

        $arrayResult["TASK_ID"] = $taskNo;
        $arrayResult["RESULTCODE"] = $intResultCode;
        $arrayResult['RESULTINFO'] = strip_tags(trim($resultMsg));

        return $arrayResult;
}

//////////////////////////////////////////////////////////////////
//  メニューアップロード処理
//////////////////////////////////////////////////////////////////
function menuImportUploadFromRest($objJSONOfReceptedData){

    global $g;

    $arrayResult = array();
    $resultMsg = "";
    $resultFlg = "";
    $intResultCode="";

    //($_SESSION 利用関数対応)
    $_SESSION['upload_id'] = $g['upload_id'];
    $_SESSION['data_portability_upload_file_name'] =$objJSONOfReceptedData['zipfile']['name'];

    try {

        // ファイルアップロード
        decodeZipFile($objJSONOfReceptedData);

        // zip解凍
        unzipImportData();

        // zipファイルの中身確認
        $declare_list = checkZipFile();

        //メニューリストの取得
        $tmpRetImportAry = makeImportCheckbox($declare_list);
        if (empty($tmpRetImportAry)) {
            throw new Exception($g['objMTS']->getSomeMessage("ITABASEH-ERR-2100000330_16"));
        }
        $retImportAry = array();
        $retUnImportAry = array();
        foreach ($tmpRetImportAry as $menuGroupId => $menuGroupInfo) {
            foreach ($menuGroupInfo["menu"] as $menuInfo) {
                $menuId   = $menuInfo["menu_id"];
                $menuName = $menuInfo["menu_name"];
                $fileName = $menuInfo["file_name"];

                if (isset($menuInfo["error"])) {
                    $error    = $menuInfo["error"];
                    if (array_key_exists($menuGroupId, $retUnImportAry)) {
                        // $key = array_search($menuId, array_column($tmpRetImportAry[$menuGroupId]["menu"], "menu_id"));
                        $retUnImportAry[$menuGroupId]["menu"][] = array(
                            "menu_id"   => $menuId,
                            "menu_name" => $menuName,
                            "file_name" => $fileName,
                            "error"     => $error
                        );
                    }
                    else {
                        $retUnImportAry[$menuGroupId] = array(
                            "menu_group_name" => $menuGroupInfo["menu_group_name"],
                            "menu"            => array(
                                array(
                                    "menu_id"   => $menuId,
                                    "menu_name" => $menuName,
                                    "file_name" => $fileName,
                                    "error"     => $error
                                )
                            )
                        );
                    }
                } else {
                    if (array_key_exists($menuGroupId, $retImportAry)) {
                        // $key = array_search($menuId, array_column($tmpRetImportAry[$menuGroupId]["menu"], "menu_id"));
                        $retImportAry[$menuGroupId]["menu"][] = array(
                            "menu_id"   => $menuId,
                            "menu_name" => $menuName,
                            "file_name" => $fileName,
                        );
                    }
                    else {
                        $retImportAry[$menuGroupId] = array(
                            "menu_group_name" => $menuGroupInfo["menu_group_name"],
                            "menu"            => array(
                                array(
                                    "menu_id"   => $menuId,
                                    "menu_name" => $menuName,
                                    "file_name" => $fileName,
                                )
                            )
                        );
                    }
                }
            }
        }
        if (empty($retImportAry)) {
            throw new Exception($g['objMTS']->getSomeMessage("ITABASEH-ERR-2100000330_16"));
        }

        $resultFlg = true;
        $intResultCode= "000";

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg =  $e->getMessage();
        $retImportAry =  $e->getMessage();
        $resultFlg = true;
        $intResultCode= "002";

    }

    $arrayResult["upload_id"] = $g['upload_id'];
    $arrayResult["data_portability_upload_file_name"] = $objJSONOfReceptedData['zipfile']['name'];

    if( $intResultCode == "000" )$arrayResult["IMPORT_LIST"] = $retImportAry;
    if( $intResultCode == "000" )$arrayResult["UNIMPORT_LIST"] = $retUnImportAry;
    if( $intResultCode == "002" ) unset($arrayResult["upload_id"]);

    $arrayResult["RESULTCODE"] = $intResultCode;
    $arrayResult['RESULTINFO'] = strip_tags(trim($resultMsg));


    return $arrayResult;
}


function chkImpotableMenuId($menuGroupId, $menuId, $userId) {
    global $objDBCA, $objMTS;

    $sql = "SELECT
                A_MENU_LIST.MENU_ID
            FROM
                A_ACCOUNT_LIST
            LEFT OUTER JOIN
                A_ROLE_ACCOUNT_LINK_LIST
            ON
                A_ACCOUNT_LIST.USER_ID = A_ROLE_ACCOUNT_LINK_LIST.USER_ID
            LEFT OUTER JOIN
                A_ROLE_MENU_LINK_LIST
            ON
                A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
            LEFT OUTER JOIN
                A_MENU_LIST
            ON
                A_MENU_LIST.MENU_ID = A_ROLE_MENU_LINK_LIST.MENU_ID
            WHERE
                A_ACCOUNT_LIST.USER_ID = :USER_ID
            AND
                A_ROLE_MENU_LINK_LIST.MENU_ID = :MENU_ID
            AND
                PRIVILEGE = 1
            AND
                A_MENU_LIST.MENU_GROUP_ID = :MENU_GROUP_ID
            AND
                A_ACCOUNT_LIST.DISUSE_FLAG = 0
            AND
                A_ROLE_ACCOUNT_LINK_LIST.DISUSE_FLAG = 0
            AND
                A_ROLE_MENU_LINK_LIST.DISUSE_FLAG = 0
            AND
                A_MENU_LIST.DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);

    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "MENU_GROUP_ID" => $menuGroupId,
            "USER_ID"       => $userId,
            "MENU_ID"       => $menuId
        )
    );

    $res = $objQuery->sqlExecute();
    $result = "";
    while ($row = $objQuery->resultFetch()) {
        $result = $row["MENU_ID"];
    }

    if (empty($result)) {
        $result = false;
    }

    return $result;
}
//////////////////////////////////////////////////////////////////
//  ファイルbase64変換処理
//////////////////////////////////////////////////////////////////
function decodeZipFile($objJSONOfReceptedData){
    global $g;

    //ファイル、パスの設定
    $uploadId = $g['upload_id'];
    $fileName = $uploadId . '_ita_data.zip';
    // $fileName = $objJSONOfReceptedData['zipfile']['name'];
    $uploadFilePath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/' . $fileName;
    $uploadPath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/bulk_excel/import/import';

    $tmpJSONOfReceptedData = $objJSONOfReceptedData;

    //一時ファイルへ保存
    $tmp_name=md5(uniqid(rand(), true));
    $tmp_dir = $g['root_dir_path'] . "/temp/" . $tmp_name;
    $tmp_decodedata = base64_decode($tmpJSONOfReceptedData['zipfile']['base64']);
    file_put_contents($tmp_dir, $tmp_decodedata);

    //ファイルタイプの取得、判定
    $tmp_type = finfo_buffer(finfo_open(), $tmp_decodedata, FILEINFO_MIME_TYPE);
    if(  $tmp_type  != "application/zip"){
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_10'));
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
