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
 * 【処理内容】
 *    資材自動連携機能(Ansible)
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
    $ansPlaybookCnt     = 0;
    $ansTemplateCnt     = 0;
    $ansContentsFileCnt = 0;
    $ansibleDialogCnt   = 0;
    $ansibleRoleCnt     = 0;
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
    $materialLinkageAnsTable = new MaterialLinkageAnsTable($objDBCA, $db_model_ch);
    $sql = $materialLinkageAnsTable->createSselect("WHERE DISUSE_FLAG = '0'");

    // SQL実行
    $result = $materialLinkageAnsTable->selectTable($sql);
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
        // Ansible共通のファイル管理に連携する場合
        //////////////////////////
        if("1" == $materialLinkageData['ANS_CONTENTS_FILE_CHK']) {

            // ファイル管理連携処理を行う
            $result = linkAnsContentsFile($materialLinkageData, $fileName, $base64File);
        }

        //////////////////////////
        // Ansible共通のテンプレート管理に連携する場合
        //////////////////////////
        else if("1" == $materialLinkageData['ANS_TEMPLATE_CHK']) {

            // テンプレート管理連携処理を行う
            $result = linkAnsTemplate($materialLinkageData, $fileName, $base64File);
        }

        //////////////////////////
        // Ansible-Legacyのプレイブック素材集に連携する場合
        //////////////////////////
        else if("1" == $materialLinkageData['ANS_PLAYBOOK_CHK']) {

            // プレイブック素材集連携処理を行う
            $result = linkAnsPlaybook($materialLinkageData, $fileName, $base64File);
        }

        //////////////////////////
        // Ansible-Pioneerの対話ファイル素材集に連携する場合
        //////////////////////////
        else if("1" == $materialLinkageData['ANSIBLE_DIALOG_CHK']) {

            // 対話ファイル素材集連携処理を行う
            $result = linkAnsibleDialog($materialLinkageData, $fileName, $base64File);
        }

        //////////////////////////
        // Ansible-Roleのロールパッケージファイルに連携する場合
        //////////////////////////
        else if("1" == $materialLinkageData['ANSIBLE_ROLE_CHK']) {

            // ロールパッケージファイル連携処理を行う
            $result = linkAnsibleRole($materialLinkageData, $fileName, $base64File);
        }
        
        if($result !== true){
            $errorFlg = true;
        }
    }

    if(LOG_LEVEL === 'DEBUG'){
        // 件数ログ出力
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10005',array(strval($deleteCnt),
                                                                        strval($ansPlaybookCnt),
                                                                        strval($ansTemplateCnt),
                                                                        strval($ansContentsFileCnt),
                                                                        strval($ansibleDialogCnt),
                                                                        strval($ansibleRoleCnt),
                                                                       )
                                         )
                 );

        if(true === $errorFlg){
            throw new Exception();
        }

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
 * ファイル管理連携処理
 * 
 */
function linkAnsContentsFile($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $ansContentsFileCnt;
    $tranStartFlg = false;
    $ansCommonContentsFileTable = new AnsCommonContentsFileTable($objDBCA, $db_model_ch);
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
        $sql = $ansCommonContentsFileTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansCommonContentsFileTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $ansContentsArray = $result;

        $matchFlg = false;

        // ファイル管理テーブルの件数分ループ
        foreach($ansContentsArray as $key => $ansContents) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $ansContents['CONTENTS_FILE_VARS_NAME']) {
                $updateData = $ansContents;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['CONTENTS_FILE']){
                $materialPath = ANS_FILE_PATH . sprintf("%010d", $updateData['CONTENTS_FILE_ID']) . "/" . $updateData['CONTENTS_FILE'];
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
                $updateData['CONTENTS_FILE']    = $fileName;                // ファイル素材
                $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $ansCommonContentsFileTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = ANS_FILE_PATH . sprintf("%010d", $updateData['CONTENTS_FILE_ID']) . "/";
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
            $insertData['CONTENTS_FILE_VARS_NAME']  = $materialLinkageData['MATERIAL_LINK_NAME'];   // ファイル埋込変数名
            $insertData['CONTENTS_FILE']            = $fileName;                                    // ファイル素材
            $insertData['DISUSE_FLAG']              = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']         = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // ファイル管理テーブルに登録
            //////////////////////////
            $result = $ansCommonContentsFileTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = ANS_FILE_PATH . sprintf("%010d", $seqNo) . "/";
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
            $ansContentsFileCnt ++;
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
 * テンプレート管理連携処理
 * 
 */
function linkAnsTemplate($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $ansTemplateCnt;
    $tranStartFlg = false;
    $ansibleCommonTemplateTable = new AnsibleCommonTemplateTable($objDBCA, $db_model_ch);
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
        $sql = $ansibleCommonTemplateTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansibleCommonTemplateTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $ansTemplateArray = $result;

        $matchFlg = false;

        // テンプレート管理テーブルの件数分ループ
        foreach($ansTemplateArray as $key => $ansTemplate) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $ansTemplate['ANS_TEMPLATE_VARS_NAME']) {
                $updateData = $ansTemplate;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['ANS_TEMPLATE_FILE']){
                $materialPath = ANS_TEMPLATE_PATH . sprintf("%010d", $updateData['ANS_TEMPLATE_ID']) . "/" . $updateData['ANS_TEMPLATE_FILE'];
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
                $updateData['ANS_TEMPLATE_FILE']    = $fileName;                // テンプレート素材
                $updateData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // テンプレート管理テーブルを更新
                //////////////////////////
                $result = $ansibleCommonTemplateTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = ANS_TEMPLATE_PATH . sprintf("%010d", $updateData['ANS_TEMPLATE_ID']) . "/";
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
            $insertData['ANS_TEMPLATE_VARS_NAME']   = $materialLinkageData['MATERIAL_LINK_NAME'];   // テンプレート埋込変数名
            $insertData['ANS_TEMPLATE_FILE']        = $fileName;                                    // テンプレート素材
            $insertData['DISUSE_FLAG']              = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']         = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // テンプレート管理テーブルに登録
            //////////////////////////
            $result = $ansibleCommonTemplateTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = ANS_TEMPLATE_PATH . sprintf("%010d", $seqNo) . "/";
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
            $ansTemplateCnt ++;
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
 * プレイブック素材集連携処理
 * 
 */
function linkAnsPlaybook($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $ansPlaybookCnt;
    $tranStartFlg = false;
    $ansibleLnsPlaybookTable = new AnsibleLnsPlaybookTable($objDBCA, $db_model_ch);
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
        // プレイブック素材集テーブルを検索
        //////////////////////////
        $sql = $ansibleLnsPlaybookTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansibleLnsPlaybookTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $ansPlaybookArray = $result;

        $matchFlg = false;

        // プレイブック素材集テーブルの件数分ループ
        foreach($ansPlaybookArray as $key => $ansPlaybook) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $ansPlaybook['PLAYBOOK_MATTER_NAME']) {
                $updateData = $ansPlaybook;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['PLAYBOOK_MATTER_FILE']){
                $materialPath = ANS_PLAYBOOK_PATH . sprintf("%010d", $updateData['PLAYBOOK_MATTER_ID']) . "/" . $updateData['PLAYBOOK_MATTER_FILE'];
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
                $updateData['PLAYBOOK_MATTER_FILE'] = $fileName;                // プレイブック素材
                $updateData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // プレイブック素材集テーブルを更新
                //////////////////////////
                $result = $ansibleLnsPlaybookTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = ANS_PLAYBOOK_PATH . sprintf("%010d", $updateData['PLAYBOOK_MATTER_ID']) . "/";
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
            $insertData['PLAYBOOK_MATTER_NAME'] = $materialLinkageData['MATERIAL_LINK_NAME'];   // プレイブック素材名
            $insertData['PLAYBOOK_MATTER_FILE'] = $fileName;                                    // プレイブック素材
            $insertData['DISUSE_FLAG']          = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // プレイブック素材集テーブルに登録
            //////////////////////////
            $result = $ansibleLnsPlaybookTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = ANS_PLAYBOOK_PATH . sprintf("%010d", $seqNo) . "/";
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
            $ansPlaybookCnt ++;
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
 * 対話ファイル素材集連携処理
 * 
 */
function linkAnsibleDialog($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $ansibleDialogCnt;
    $tranStartFlg = false;
    $ansiblePnsDialogTypeTable = new AnsiblePnsDialogTypeTable($objDBCA, $db_model_ch);
    $AnsiblePnsDialogTable = new AnsiblePnsDialogTable($objDBCA, $db_model_ch);
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
        // 対話種別リストテーブルを検索
        //////////////////////////
        $sql = $ansiblePnsDialogTypeTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansiblePnsDialogTypeTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $dialogMasterArray = $result;

        $matchFlg = false;

        // ファイル管理メニューの件数分ループ
        foreach($dialogMasterArray as $key => $dialogMaster) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $dialogMaster['DIALOG_TYPE_NAME']) {
                $dialogTypeId = $dialogMaster['DIALOG_TYPE_ID'];
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータが無かった場合
        if(false === $matchFlg) {

            // 登録する
            $insertData = array();
            $insertData['DIALOG_TYPE_NAME']  =      $materialLinkageData['MATERIAL_LINK_NAME']; // 対話種別名
            $insertData['DISUSE_FLAG']              = "0";                                      // 廃止フラグ
            $insertData['LAST_UPDATE_USER']         = USER_ID_MATERIAL_LINKAGE;                 // 最終更新者

            //////////////////////////
            // 対話種別リストテーブルに登録
            //////////////////////////
            $result = $ansiblePnsDialogTypeTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $dialogTypeId = $seqNo;
        }

        //////////////////////////
        // 対話ファイル素材集テーブルを検索
        //////////////////////////
        $sql = $AnsiblePnsDialogTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $AnsiblePnsDialogTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $ansibleDialogArray = $result;

        $matchFlg = false;

        // 対話ファイル素材集テーブルの件数分ループ
        foreach($ansibleDialogArray as $key => $ansibleDialog) {

            // 対話種別とOS種別が一致するデータを検索
            if($dialogTypeId == $ansibleDialog['DIALOG_TYPE_ID'] && $materialLinkageData['OS_TYPE_ID'] == $ansibleDialog['OS_TYPE_ID']) {
                $updateData = $ansibleDialog;
                $matchFlg = true;
                break;
            }
        }

        // 対話種別とOS種別が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['DIALOG_MATTER_FILE']){
                $materialPath = ANS_DIALOG_PATH . sprintf("%010d", $updateData['DIALOG_MATTER_ID']) . "/" . $updateData['DIALOG_MATTER_FILE'];
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
                $updateData['DIALOG_MATTER_FILE']   = $fileName;                // 対話ファイル素材
                $updateData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // 対話ファイル素材集テーブルを更新
                //////////////////////////
                $result = $AnsiblePnsDialogTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = ANS_DIALOG_PATH . sprintf("%010d", $updateData['DIALOG_MATTER_ID']) . "/";
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
            $insertData['DIALOG_TYPE_ID']       = $dialogTypeId;                        // 対話種別
            $insertData['OS_TYPE_ID']           = $materialLinkageData['OS_TYPE_ID'];   // OS種別
            $insertData['DIALOG_MATTER_FILE']   = $fileName;                            // 対話ファイル素材
            $insertData['DISUSE_FLAG']          = "0";                                  // 廃止フラグ
            $insertData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE;             // 最終更新者

            //////////////////////////
            // 対話ファイル素材集テーブルに登録
            //////////////////////////
            $result = $AnsiblePnsDialogTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = ANS_DIALOG_PATH . sprintf("%010d", $seqNo) . "/";
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
            $ansibleDialogCnt ++;
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
 * ロールパッケージファイル連携処理
 * 
 */
function linkAnsibleRole($materialLinkageData, $fileName, $base64File) {

    global $objMTS, $objDBCA, $db_model_ch, $ansibleRoleCnt;
    $tranStartFlg = false;
    $ansibleLrlRolePackageTable = new AnsibleLrlRolePackageTable($objDBCA, $db_model_ch);
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
        // ロールパッケージ管理テーブルを検索
        //////////////////////////
        $sql = $ansibleLrlRolePackageTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $ansibleLrlRolePackageTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $ansibleRoleArray = $result;

        $matchFlg = false;

        // ロールパッケージ管理テーブルの件数分ループ
        foreach($ansibleRoleArray as $key => $ansibleRole) {

            // 資材名が一致するデータを検索
            if($materialLinkageData['MATERIAL_LINK_NAME'] == $ansibleRole['ROLE_PACKAGE_NAME']) {
                $updateData = $ansibleRole;
                $matchFlg = true;
                break;
            }
        }

        // 資材名が一致するデータがあった場合
        if(true === $matchFlg) {

            $updateFlg = true;
            // 資材が一致した場合
            if($fileName == $updateData['ROLE_PACKAGE_FILE']){
                $materialPath = ANS_ROLE_PATH . sprintf("%010d", $updateData['ROLE_PACKAGE_ID']) . "/" . $updateData['ROLE_PACKAGE_FILE'];
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
                $updateData['ROLE_PACKAGE_FILE']    = $fileName;                // ロールパッケージファイル
                $updateData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE; // 最終更新者

                //////////////////////////
                // ロールパッケージ管理テーブルを更新
                //////////////////////////
                $result = $ansibleLrlRolePackageTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                $upFilePath     = ANS_ROLE_PATH . sprintf("%010d", $updateData['PLAYBOOK_MATTER_ID']) . "/";
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
            $insertData['ROLE_PACKAGE_NAME']    = $materialLinkageData['MATERIAL_LINK_NAME'];   // ロールパッケージファイル名
            $insertData['ROLE_PACKAGE_FILE']    = $fileName;                                    // ロールパッケージファイル
            $insertData['DISUSE_FLAG']          = "0";                                          // 廃止フラグ
            $insertData['LAST_UPDATE_USER']     = USER_ID_MATERIAL_LINKAGE;                     // 最終更新者

            //////////////////////////
            // ロールパッケージ管理テーブルに登録
            //////////////////////////
            $result = $ansibleLrlRolePackageTable->insertTable($insertData, $seqNo, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            $upFilePath     = ANS_ROLE_PATH . sprintf("%010d", $seqNo) . "/";
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
            $ansibleRoleCnt ++;
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
