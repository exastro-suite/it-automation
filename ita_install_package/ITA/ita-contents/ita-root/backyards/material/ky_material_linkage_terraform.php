<?php
//   Copyright 2020 NEC Corporation
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
 *    資材自動連携機能(Terraform)
 *      資材管理メニューで管理している資材をITAの各種メニューに自動連携する。
 */

if( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}

define('ROOT_DIR_PATH',         $root_dir_path);
require_once ROOT_DIR_PATH      . '/libs/backyardlibs/material/ky_material_env.php';
require_once MATERIAL_LIB_PATH  . 'ky_material_classes.php';
require_once MATERIAL_LIB_PATH  . 'ky_material_functions.php';
require_once COMMONLIBS_PATH    . 'common_php_req_gate.php';

try{
    $normalFlg = true;          // 正常判定用フラグ
    $errMsg = NULL;             // エラーメッセージ
    $deleteCnt          = 0;
    $terraformModuleCnt = 0;
    $terraformPolicyCnt = 0;
    $errorFlg = false;

    $logPrefix = basename( __FILE__, '.php' ) . '_';
    $tmpDir = "";

    if(LOG_LEVEL === 'DEBUG'){
        // 処理開始ログ
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10001', basename( __FILE__, '.php' )));
    }

    //////////////////////////
    // インターフェース情報テーブルを検索
    //////////////////////////
    $materialIfInfoTable = new MaterialIfInfoTable($objDBCA, $db_model_ch);
    $sql = $materialIfInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $materialIfInfoTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
        outputLog($msg);
        throw new Exception();
    }
    $materialIfInfoArray = $result;

    // インターフェース情報が1件ではない場合はエラー
    if(1 != count($materialIfInfoArray)) {
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5002');
        outputLog($msg);
        throw new Exception();
    }

    // リモートリポジトリURLとクローンリポジトリが設定されていない場合はエラー
    if("" == $materialIfInfoArray[0]['REMORT_REPO_URL'] || "" == $materialIfInfoArray[0]['CLONE_REPO_DIR']) {
        if(LOG_LEVEL === 'DEBUG'){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5002');
            outputLog($msg);
        }
        throw new Exception();
    }

    // クローンリポジトリにGitが設定されていない場合はエラー
    if(!file_exists($materialIfInfoArray[0]['CLONE_REPO_DIR'] . "/.git")) {
        if(LOG_LEVEL === 'DEBUG'){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5003');
            outputLog($msg);
        }
        throw new Exception();
    }

    //////////////////////////
    // 資材紐付け管理テーブルを検索
    //////////////////////////
    $materialLinkageTerraformTable = new MaterialLinkageTerraformTable($objDBCA, $db_model_ch);
    $sql = $materialLinkageTerraformTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $materialLinkageTerraformTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
        outputLog($msg);
        throw new Exception();
    }
    $materialLinkageArray = $result;

    //////////////////////////
    // ファイル管理ビューを検索
    //////////////////////////
    $fileManegementNewestView = new FileManegementNewestView($objDBCA, $db_model_ch);
    $sql = $fileManegementNewestView->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $fileManegementNewestView->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
        outputLog($msg);
        throw new Exception();
    }
    $fileManegementNewestArray = $result;

    // 資材紐付け管理テーブルの件数分ループ
    foreach($materialLinkageArray as $materialLinkageData) {

        // 該当のファイル管理データを特定する
        $matchFlg = false;
        $fileNameFullpath = "";
        $fileName = "";

        foreach($fileManegementNewestArray as $fileManegementNewestData) {

            // ファイルIDが一致した場合
            if($materialLinkageData['FILE_ID'] == $fileManegementNewestData['FILE_ID']){

                // リビジョン指定がある場合はリビジョンの確認も行う
                if("" != $materialLinkageData['CLOSE_REVISION_ID']){
                    if($fileManegementNewestData['FILE_M_ID'] == $materialLinkageData['CLOSE_REVISION_ID']) {
                        $returnFileDir      = UPLOADFILES_PATH . "" . "RETURN_FILE/" . sprintf("%010d", $fileManegementNewestData['FILE_M_ID']). "/";
                        $fileNameFullpath   = $returnFileDir . $fileManegementNewestData['RETURN_FILE'];
                        $fileName           = $fileManegementNewestData['RETURN_FILE'];
                        $matchFlg           = true;
                        break;
                    }
                }
                else{
                    $fileNameFullpath   = $materialIfInfoArray[0]['CLONE_REPO_DIR'] . $fileManegementNewestData['FILE_NAME_FULLPATH'];
                    $fileName           = $fileManegementNewestData['RETURN_FILE'];
                    $matchFlg           = true;
                    break;
                }
            }
        }

        // ファイル管理ビューの該当データが0件の場合
        if(false === $matchFlg){
            if(LOG_LEVEL === 'DEBUG'){
                outputLog($objMTS->getSomeMessage('ITAMATERIAL-ERR-5008', $materialLinkageData['ROW_ID']));
            }
            // 次のデータへ
            $errorFlg = true;
            continue;
        }

        // 資材のBase64エンコードを取得する
        if(file_exists($fileNameFullpath)){
            $base64File = base64_encode(file_get_contents($fileNameFullpath));
        }
        else{
            if(LOG_LEVEL === 'DEBUG'){
                outputLog($objMTS->getSomeMessage('ITAMATERIAL-ERR-5008', $materialLinkageData['ROW_ID']));
            }
            $errorFlg = true;
            continue;
        }
        
        $result = true;

        //////////////////////////
        // TerraformのModule素材に連携する場合
        //////////////////////////
        if("1" == $materialLinkageData['TERRAFORM_MODULE_CHK']) {
            // Module素材連携処理を行う
            $result = linkTerraformModule($materialLinkageData, $fileName, $base64File);
        }

        //////////////////////////
        // TerraformのPolicy素材に連携する場合
        //////////////////////////
        else if("1" == $materialLinkageData['TERRAFORM_POLICY_CHK']) {
            // Policy素材連携処理を行う
            $result = linkTerraformPolicy($materialLinkageData, $fileName, $base64File);
        }

        if($result !== true){
            $errorFlg = true;
        }
    }

    if(LOG_LEVEL === 'DEBUG'){
        // 件数ログ出力
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10012',array(strval($deleteCnt),
                                                                        strval($terraformModuleCnt),
                                                                        strval($terraformPolicyCnt),
                                                                       )
                                         )
                 );
    }

    if(true === $errorFlg){
        throw new Exception();
    }

    // データベースを更新した事をマークする
    $updateKey = "";
    if($ansTemplateCnt > 0){
        $updateKey .= "2100080001";
    }

    if($updateKey != ""){

        $baseTable = new BaseTable($objDBCA, $db_model_ch);

        $strQuery = "UPDATE A_PROC_LOADED_LIST "
                   ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                   ."WHERE ROW_ID IN (${updateKey}) ";

        $aryForBind = array();

        $result = $baseTable->execQuery($strQuery, $aryForBind, $objQuery);

        if( $result !== true ){
            outputLog($result);
            throw new Exception();
        }
    }

    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10002', basename( __FILE__, '.php' )));
    }
}
catch(Exception $e){
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10003', basename( __FILE__, '.php' )));
    }
}

/**
 * Module素材連携処理
 * 
 */
function linkTerraformModule($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $terraformModuleCnt;
    $tranStartFlg = false;
    $terraformCommonModuleTable = new TerraformCommonModuleFileTable($objDBCA, $db_model_ch);
    $cntFlg = false;

    try{
        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        //////////////////////////
        // ファイル管理テーブルを検索
        //////////////////////////
        $sql = $terraformCommonModuleTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $terraformCommonModuleTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $terraformModuleArray = $result;

        $matchFlg = false;

        // ファイル管理テーブルの件数分ループ
        foreach($terraformModuleArray as $key => $terraformModule) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $terraformModule['MODULE_MATTER_NAME']) {
                $updateData = $terraformModule;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['MODULE_MATTER_FILE']){
                $materialPath = TERRAFORM_MODULE_PATH . sprintf("%010d", $updateData['MODULE_MATTER_ID']) . "/" . $updateData['MODULE_MATTER_FILE'];
                if(file_exists($materialPath)){
                    // 資材に変更がない場合は更新しない
                    $materialBase64 = base64_encode(file_get_contents($materialPath));
                    if($base64File == $materialBase64){
                        $updateFlg = false;
                    }
                }
            }

            if(true === $updateFlg){
                $cntFlg = true;
                // 更新する
                $updateData['MODULE_MATTER_FILE']    = $fileName;                // ファイル素材
                $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $terraformCommonModuleTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = TERRAFORM_MODULE_PATH . sprintf("%010d", $updateData['MODULE_MATTER_ID']) . "/";
                $upJnlFilePath  = $upFilePath . "old/" . sprintf("%010d", $jnlSeqNo) . "/";;

                // ファイルを格納パスに配置する
                $result = deployUploadFile($objMTS, $base64File, $fileName, $upFilePath, $upJnlFilePath);

                if(true !== $result){
                    outputLog($result);
                    throw new Exception();
                }
            }
        }
        // 資材名が一致するデータが無かった場合
        else {
            $cntFlg = true;
            // 登録する
            $insertData = array();
            $insertData['MODULE_MATTER_NAME']  = $materialLinkageData['MATERIAL_LINK_NAME'];   // ファイル埋込変数名
            $insertData['MODULE_MATTER_FILE']  = $fileName;                                    // ファイル素材
            $insertData['DISUSE_FLAG']         = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']    = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // ファイル管理テーブルに登録
            //////////////////////////
            $result = $terraformCommonModuleTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = TERRAFORM_MODULE_PATH . sprintf("%010d", $seqNo) . "/";
            $upJnlFilePath  = $upFilePath . "old/" . sprintf("%010d", $jnlSeqNo). "/";;

            // ファイルを格納パスに配置する
            $result = deployUploadFile($objMTS, $base64File, $fileName, $upFilePath, $upJnlFilePath);

            if(true !== $result){
                outputLog($result);
                throw new Exception();
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = false;

        if(true === $cntFlg){
            $terraformModuleCnt ++;
        }

        return true;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        return false;
    }
}

/**
 * Policy素材連携処理
 * 
 */
function linkTerraformPolicy($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $terraformPolicyCnt;
    $tranStartFlg = false;
    $terraformCommonPolicyTable = new TerraformCommonPolicyFileTable($objDBCA, $db_model_ch);
    $cntFlg = false;

    try{
        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        //////////////////////////
        // テンプレート管理テーブルを検索
        //////////////////////////
        $sql = $terraformCommonPolicyTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $terraformCommonPolicyTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $terraformPolicyArray = $result;

        $matchFlg = false;

        // テンプレート管理テーブルの件数分ループ
        foreach($terraformPolicyArray as $key => $terraformPolicy) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $terraformPolicy['POLICY_NAME']) {
                $updateData = $terraformPolicy;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['POLICY_MATTER_FILE']){
                $materialPath = TERRAFORM_POLICY_PATH . sprintf("%010d", $updateData['POLICY_ID']) . "/" . $updateData['POLICY_MATTER_FILE'];
                if(file_exists($materialPath)){
                    // 資材に変更がない場合は更新しない
                    $materialBase64 = base64_encode(file_get_contents($materialPath));
                    if($base64File == $materialBase64){
                        $updateFlg = false;
                    }
                }
            }

            if(true === $updateFlg){
                $cntFlg = true;
                // 更新する
                $updateData['POLICY_MATTER_FILE']    = $fileName;                // テンプレート素材
                $updateData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // テンプレート管理テーブルを更新
                //////////////////////////
                $result = $terraformCommonPolicyTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = TERRAFORM_POLICY_PATH . sprintf("%010d", $updateData['POLICY_ID']) . "/";
                $upJnlFilePath  = $upFilePath . "old/" . sprintf("%010d", $jnlSeqNo) . "/";;

                // ファイルを格納パスに配置する
                $result = deployUploadFile($objMTS, $base64File, $fileName, $upFilePath, $upJnlFilePath);

                if(true !== $result){
                    outputLog($result);
                    throw new Exception();
                }
            }
        }

        // 資材名が一致するデータが無かった場合
        else {
            $cntFlg = true;
            // 登録する
            $insertData = array();
            $insertData['POLICY_NAME']   = $materialLinkageData['MATERIAL_LINK_NAME'];   // テンプレート埋込変数名
            $insertData['POLICY_MATTER_FILE']        = $fileName;                                    // テンプレート素材
            $insertData['DISUSE_FLAG']              = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']         = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // テンプレート管理テーブルに登録
            //////////////////////////
            $result = $terraformCommonPolicyTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = TERRAFORM_POLICY_PATH . sprintf("%010d", $seqNo) . "/";
            $upJnlFilePath  = $upFilePath . "old/" . sprintf("%010d", $jnlSeqNo). "/";;

            // ファイルを格納パスに配置する
            $result = deployUploadFile($objMTS, $base64File, $fileName, $upFilePath, $upJnlFilePath);

            if(true !== $result){
                outputLog($result);
                throw new Exception();
            }
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = false;

        if(true === $cntFlg){
            $terraformPolicyCnt ++;
        }

        return true;
    }
    catch(Exception $e){
        // ロールバック
        if(true === $tranStartFlg){
            $objDBCA->transactionRollback();
        }
        return false;
    }
}
