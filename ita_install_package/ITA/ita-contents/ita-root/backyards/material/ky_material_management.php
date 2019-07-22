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
 *    自動払出払戻機能
 *      資材管理に登録されたデータに対して、Git連携を行い資材の払出・払戻を行う。
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
require_once MATERIAL_LIB_PATH  . 'ky_ControlGit.php';
require_once COMMONLIBS_PATH    . 'common_php_req_gate.php';

try{
    $paymentCnt     = 0;    // 払出件数
    $duplicateCnt   = 0;    // 重複件数
    $repaymentCnt   = 0;    // 払戻件数
    $remandCnt      = 0;    // 差戻し件数
    $diffCnt        = 0;    // DIFF登録件数
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

    $remortRepoUrl = $materialIfInfoArray[0]['REMORT_REPO_URL'];
    $branch = $materialIfInfoArray[0]['BRANCH'];
    $cloneRepoDir = $materialIfInfoArray[0]['CLONE_REPO_DIR'];
    $password = ky_decrypt($materialIfInfoArray[0]['PASSWORD']);
    $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $password, MATERIAL_LIB_PATH);

    //////////////////////////
    // ファイル管理テーブルを検索
    //////////////////////////
    $fileManagementTable = new FileManagementTable($objDBCA, $db_model_ch);
    $sql = $fileManagementTable->createSselect("WHERE FILE_STATUS_ID IN (1, 2, 4, 5) AND DISUSE_FLAG='0' ORDER BY FILE_M_ID");

    // SQL実行
    $result = $fileManagementTable->selectTable($sql);
    if(!is_array($result)){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
        outputLog($msg);
        throw new Exception();
    }
    $fileManagementArray = $result;

    foreach($fileManagementArray as $fileManagementData) {

        //////////////////////////
        // 資材マスタビューを検索
        //////////////////////////
        $fileMasterView = new FileMasterView($objDBCA, $db_model_ch);
        $sql = $fileMasterView->createSselect("WHERE FILE_ID ='" . $fileManagementData['FILE_ID'] . "' AND DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileMasterView->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            $errorFlg = true;
            continue;
        }

        $fileMasterArray = $result;

        // 該当データが1件ではない場合
        if(1 != count($fileMasterArray)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5004', $fileManagementData['FILE_M_ID']);
            outputLog($msg);
            $errorFlg = true;
            continue;
        }

        // ステータスが「払出申請」、「払出申請中(重複待ち)」の場合
        if(in_array($fileManagementData['FILE_STATUS_ID'], array("1", "2"), true)){

            //////////////////////////
            // 資材払出処理を行う
            //////////////////////////
            $result = paymentMaterial($fileManagementData, $fileMasterArray[0], $controlGit);

            if(false === $result) {
                $errorFlg = true;
                continue;
            }
        }

        // ステータスが「払戻申請中」、「払戻中」の場合
        else if(in_array($fileManagementData['FILE_STATUS_ID'], array("4", "5"), true)){

            //////////////////////////
            // 資材払戻処理を行う
            //////////////////////////
            $result = repaymentMaterial($fileManagementData, $fileMasterArray[0], $controlGit);

            if(false === $result) {
                $errorFlg = true;
                continue;
            }
        }
    }


    // 件数ログ出力
    if(LOG_LEVEL === 'DEBUG'){
        // 終了ログ出力
        outputLog($objMTS->getSomeMessage('ITAMATERIAL-STD-10004', array(strval($paymentCnt),
                                                                         strval($duplicateCnt),
                                                                         strval($repaymentCnt),
                                                                         strval($remandCnt),
                                                                         strval($diffCnt),
                                                                        )
                                         )
                 );
    }

    if(true === $errorFlg){
        throw new Exception();
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
 * 資材払出処理
 * 
 */
function paymentMaterial($fileManagementData, $fileMasterData, $controlGit) {

    global $objMTS, $objDBCA, $db_model_ch, $paymentCnt, $duplicateCnt;
    $tranStartFlg = false;
    $fileManagementTable = new FileManagementTable($objDBCA, $db_model_ch);

    try{

        //////////////////////////
        // ファイル管理テーブルを検索
        //////////////////////////
        $sql = $fileManagementTable->createSselect("WHERE FILE_ID = '" . $fileManagementData['FILE_ID']. "' AND FILE_STATUS_ID IN (3, 4, 5, 8) AND DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileManagementTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $fileManagementArray = $result;

        // 重複データがある場合
        if(0 < count($fileManagementArray)){

            // ステータスが「払出申請中(重複待ち)」以外の場合
            if("2" != $fileManagementData['FILE_STATUS_ID']){

                // トランザクション開始
                $result = $objDBCA->transactionStart();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
                    outputLog($msg);
                    throw new Exception();
                }
                $tranStartFlg = true;

                $updateData = $fileManagementData;
                $updateData['FILE_STATUS_ID'] = 2;  // 払出申請中(重複待ち)
                $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($updateData);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                // コミット
                $result = $objDBCA->transactionCommit();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
                    outputLog($msg);
                    throw new Exception();
                }
                $tranStartFlg = false;
                $this->duplicateCnt ++;
            }
            return true;
        }

        // Git上のパスを設定する
        $filePath = $fileMasterData['FILE_NAME_FULLPATH'];
        $gitFileDir = $controlGit->getCloneRepoDir() . dirname($filePath);
        $gitFilePath = $controlGit->getCloneRepoDir() . $filePath;
        // 払出資材のパスを設定する
        $assignFileDir = UPLOADFILES_PATH . "" . "ASSIGN_FILE/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
        $assignFilePath = $assignFileDir . $fileMasterData['FILE_NAME'];

        // Git上のファイルの存在を調べる
        $resultFileExist = file_exists($gitFilePath);

        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        $updateData = $fileManagementData;

        $updateData['FILE_STATUS_ID'] = 3;  // 払出中
        $updateData['ASSIGN_DATE'] = date('Y/m/d H:i:s');
        $updateData['ASSIGN_USER_ID'] = USER_ID_MATERIAL_MANAGEMENT;
        if(true === $resultFileExist){
            // リビジョンを取得する
            $result = $controlGit->getFileRevision($filePath, $revision, $errMsg);

            if(0 != $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5005', array($errMsg));
                outputLog($msg);
                throw new Exception();
            }
            $updateData['ASSIGN_REVISION'] = $revision;
            $updateData['ASSIGN_FILE'] = $fileMasterData['FILE_NAME'];
        }
        else{
            $updateData['ASSIGN_REVISION'] = $objMTS->getSomeMessage('ITAMATERIAL-STD-50001');
        }
        $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;

        //////////////////////////
        // ファイル管理テーブルを更新
        //////////////////////////
        $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }

        if(true === $resultFileExist){

            // 払出資材のディレクトリの存在を調べる
            $result = file_exists($assignFileDir);

            if(true != $result){

                // ディレクトリを作成する
                $orgUmask = umask(0000);
                $result = mkdir($assignFileDir, 0777, true);
                umask($orgUmask);

                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $assignFileDir);
                    outputLog($msg);
                    throw new Exception();
                }
            }

            // Gitのファイルを払出資材のパスに格納する
            $output = NULL;
            $cmd = "cp -p -- '" . $gitFilePath . "' '" . $assignFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                throw new Exception();
            }

            // 払出資材の権限を変更する
            $output = NULL;
            $cmd = "chmod -- 644 '" . $assignFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                throw new Exception();
            }

            // 払出資材の所有者を変更する
            $output = NULL;
            $cmd = "chown -- daemon:daemon '" . $assignFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                throw new Exception();
            }

            // 履歴用のパスを設定する
            $assignFileJnlDir = $assignFileDir . "/old/" . sprintf("%010d", $jnlSeqNo). "/";
            $assignFileJnlPath = $assignFileJnlDir . $fileMasterData['FILE_NAME'];

            // 払出資材のディレクトリの存在を調べる
            $result = file_exists($assignFileJnlDir);

            if(true != $result){

                // ディレクトリを作成する
                $orgUmask = umask(0000);
                $result = mkdir($assignFileJnlDir, 0777, true);
                umask($orgUmask);

                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $assignFileJnlDir);
                    outputLog($msg);
                    throw new Exception();
                }
            }

            // Gitのファイルを払出資材のパスに格納する
            $output = NULL;
            $cmd = "cp -p -- '" . $gitFilePath . "' '" . $assignFileJnlPath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
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
        $paymentCnt ++;

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
 * 資材払戻処理
 * 
 */
function repaymentMaterial($fileManagementData, $fileMasterData, $controlGit) {

    global $objMTS, $objDBCA, $db_model_ch, $remandCnt, $diffCnt, $repaymentCnt;
    $tranStartFlg = false;
    $fileManagementTable = new FileManagementTable($objDBCA, $db_model_ch);
    $updateData = "";

    try{
        // ファイル名のチェック
        if($fileMasterData['FILE_NAME'] != $fileManagementData['RETURN_FILE']){
            $updateData = $fileManagementData;
            $updateData['FILE_STATUS_ID'] = 8;  // 差戻し(払戻申請)
            $updateData['RETURN_DATE']      = "";
            $updateData['RETURN_USER_ID']   = "";
            $updateData['RETURN_FILE']      = "";
            $updateData['RETURN_DIFF']      = "";
            $updateData['RETURN_TESTCASES'] = "";
            $updateData['RETURN_EVIDENCES'] = "";

            if("" != $updateData['NOTE']){
                $updateData['NOTE'] .= "\n\n";
            }
            $updateData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $objMTS->getSomeMessage('ITAMATERIAL-STD-50002');
            $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;

            // トランザクション開始
            $result = $objDBCA->transactionStart();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
                outputLog($msg);
                throw new Exception();
            }
            $tranStartFlg = true;

            //////////////////////////
            // ファイル管理テーブルを更新
            //////////////////////////
            $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
            if(true !== $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }

            // コミット
            $result = $objDBCA->transactionCommit();
            if(false === $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                outputLog($msg);
                throw new Exception();
            }
            $tranStartFlg = false;

            $remandCnt ++;
            return true;
        }

        // Git上のパスを設定する
        $filePath = $fileMasterData['FILE_NAME_FULLPATH'];
        $gitFileDir = $controlGit->getCloneRepoDir() . dirname($filePath);
        $gitFilePath = $controlGit->getCloneRepoDir() . $filePath;
        // 払出資材のパスを設定する
        $assignFileDir = UPLOADFILES_PATH . "" . "ASSIGN_FILE/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
        $assignFilePath = $assignFileDir . $fileMasterData['FILE_NAME'];
        // 払戻資材のパスを設定する
        $returnFileDir = UPLOADFILES_PATH . "" . "RETURN_FILE/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
        $returnFilePath = $returnFileDir . $fileMasterData['FILE_NAME'];

        // Git上のファイルの存在を調べる
        $resultFileExist = file_exists($gitFilePath);
        
        if(true === $resultFileExist){

            // リビジョンを取得する
            $result = $controlGit->getFileRevision($filePath, $revision, $errMsg);

            if(0 != $result){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5005', array($errMsg));
                outputLog($msg);
                throw new Exception();
            }

            // 払出時のリビジョンと比較する
            if($fileManagementData['ASSIGN_REVISION'] != $revision){
                $updateData = $fileManagementData;
                $updateData['FILE_STATUS_ID'] = 8;  // 差戻し(払戻申請)
                $updateData['RETURN_DATE']      = "";
                $updateData['RETURN_USER_ID']   = "";
                $updateData['RETURN_FILE']      = "";
                $updateData['RETURN_DIFF']      = "";
                $updateData['RETURN_TESTCASES'] = "";
                $updateData['RETURN_EVIDENCES'] = "";

                if("" != $updateData['NOTE']){
                    $updateData['NOTE'] .= "\n\n";
                }
                $updateData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $objMTS->getSomeMessage('ITAMATERIAL-STD-50003');
                $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;
            }
            else{
                // ファイルの中身チェック
                $output = NULL;
                $cmd = "cmp -s -- '" . $returnFilePath . "' '" . $gitFilePath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 === $return_var){

                    $updateData = $fileManagementData;
                    $updateData['FILE_STATUS_ID'] = 8;  // 差戻し(払戻申請)
                    $updateData['RETURN_DATE']      = "";
                    $updateData['RETURN_USER_ID']   = "";
                    $updateData['RETURN_FILE']      = "";
                    $updateData['RETURN_DIFF']      = "";
                    $updateData['RETURN_TESTCASES'] = "";
                    $updateData['RETURN_EVIDENCES'] = "";

                    if("" != $updateData['NOTE']){
                        $updateData['NOTE'] .= "\n\n";
                    }
                    $updateData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $objMTS->getSomeMessage('ITAMATERIAL-STD-50004');
                    $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;
                }
                else if(1 != $return_var){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                    outputLog($msg);
                    throw new Exception();
                }
            }

            if("" != $updateData){
                // トランザクション開始
                $result = $objDBCA->transactionStart();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
                    outputLog($msg);
                    throw new Exception();
                }

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                // コミット
                $result = $objDBCA->transactionCommit();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }
                $tranStartFlg = false;
                $remandCnt ++;
                return true;
            }
        }

        $updateData = $fileManagementData;

        // 払出資材がある、かつDIFFが登録されていない場合
        if("" != $fileManagementData['ASSIGN_FILE'] && "" == $fileManagementData['RETURN_DIFF']){

            // 払出資材のファイル種別を判定する
            $output = NULL;
            $cmd = "file -b -- '" . $assignFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                throw new Exception();
            }

            // textかどうか確認する
            $resultAssign = strpos($output[0], " text");

            // 払戻資材ファイル種別を判定する
            $output = NULL;
            $cmd = "file -b -- '" . $returnFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                throw new Exception();
            }

            // textかどうか確認する
            $resultReturn = strpos($output[0], " text");

            // textの場合
            if(false !== $resultAssign && false !== $resultReturn){

                // 最新時間を取得
                $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
                $nowTime = date("YmdHis") . $now->format("u");

                $tmpDir = TMP_PATH . $nowTime;

                // 作業用ディレクトリ作成
                $orgUmask = umask(0000);
                $result = mkdir($tmpDir, 0777, true);
                umask($orgUmask);
                
                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $tmpDir);
                    outputLog($msg);
                    throw new Exception();
                }

                // DIFFファイルパスを設定
                $diffFileName = $fileMasterData['FILE_NAME'] . "_diff.txt";
                $diffFilePath = $tmpDir . "/" . $diffFileName;
                $returnDiffDir = UPLOADFILES_PATH . "" . "RETURN_DIFF/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
                $returnDiffPath = $returnDiffDir . $diffFileName;

                // DIFFを出力する
                $output = NULL;
                $cmd = "diff -ty -- '" . $assignFilePath . "' '" . $returnFilePath . "' > '" . $diffFilePath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(1 != $return_var){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                    outputLog($msg);
                    throw new Exception();
                }

                $updateData['RETURN_DIFF'] = $diffFileName;

                // DIFFのディレクトリの存在を調べる
                $result = file_exists($returnDiffDir);

                if(true != $result){

                    // ディレクトリを作成する
                    $orgUmask = umask(0000);
                    $result = mkdir($returnDiffDir, 0777, true);
                    umask($orgUmask);
                    
                    if(true != $result){
                        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $returnDiffDir);
                        outputLog($msg);
                        throw new Exception();
                    }
                }

                // DIFFファイルを格納先に格納する
                $output = NULL;
                $cmd = "cp -p -- '" . $diffFilePath . "' '" . $returnDiffPath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                    outputLog($msg);
                    throw new Exception();
                }

                $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;

                // トランザクション開始
                $result = $objDBCA->transactionStart();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
                    outputLog($msg);
                    throw new Exception();
                }
                $tranStartFlg = true;

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
                if(true !== $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }

                // 履歴用のパスを設定する
                $returnDiffJnlDir = $returnDiffDir . "/old/" . sprintf("%010d", $jnlSeqNo). "/";
                $returnDiffJnlPath = $returnDiffJnlDir . $diffFileName;

                // 払出資材のディレクトリの存在を調べる
                $result = file_exists($returnDiffJnlDir);

                if(true != $result){

                    // ディレクトリを作成する
                    $orgUmask = umask(0000);
                    $result = mkdir($returnDiffJnlDir, 0777, true);
                    umask($orgUmask);
                    
                    if(true != $result){
                        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $returnDiffJnlDir);
                        outputLog($msg);
                        throw new Exception();
                    }
                }

                // DIFFファイルを履歴用パスに格納する
                $output = NULL;
                $cmd = "cp -p -- '" . $diffFilePath . "' '" . $returnDiffJnlDir . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                    outputLog($msg);
                    throw new Exception();
                }

                // 作業用ディレクトリを削除する
                $output = NULL;
                $cmd = "rm -rf -- '" . $tmpDir . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                    outputLog($msg);
                    throw new Exception();
                }

                // コミット
                $result = $objDBCA->transactionCommit();
                if(false === $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    outputLog($msg);
                    throw new Exception();
                }
                $tranStartFlg = false;

                $diffCnt ++;
            }
        }

        // ステータスが「払戻申請中」、かつ自動払戻有無が「自動で払戻」ではない場合
        if("4" === $fileManagementData['FILE_STATUS_ID'] && "2" != $fileMasterData['AUTO_RETURN_FLAG']){
            // 何もせずに終了
            return true;
        }

        if(false === $resultFileExist){
            // Git上のディレクトリの存在を調べる
            $resultDirExist = file_exists($gitFileDir);

            if(true != $resultDirExist){
                // ディレクトリを作成する
                $orgUmask = umask(0000);
                $result = mkdir($gitFileDir, 0777, true);
                umask($orgUmask);
                
                if(true != $result){
                    $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $gitFileDir);
                     outputLog($msg);
                    throw new Exception();
                }
            }
        }

        // 払戻資材をGit上に格納する
        $output = NULL;
        $cmd = "cp -p -- '" . $returnFilePath . "' '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
            outputLog($msg);
            throw new Exception();
        }

        // Git上のディレクトリの権限・所有者を変更する
        $result = setDirOwn($fileMasterData['DIR_ID'], $controlGit);

        if(true != $result){
            throw new Exception();
        }

        // Git上のファイルの権限を変更する
        $output = NULL;
        $cmd = "chmod -- " . $fileMasterData['CHMOD'] . " '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
            outputLog($msg);
            throw new Exception();
        }

        // ファイルの所有者を確認する
        $result = chkOwn($fileMasterData['GROUP_AUTH'], $fileMasterData['USER_AUTH'], $groupAuth, $userAuth);

        if(true != $result){
            throw new Exception();
        }

        // Git上のファイルの所有者を変更する
        $output = NULL;
        $cmd = "chown -- " . $groupAuth . ":" . $userAuth . " '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
            outputLog($msg);
            throw new Exception();
        }

        // Gitにコミットする
        $result = $controlGit->commitGit($filePath, $errMsg);

        if(0 != $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5005', array($errMsg));
            outputLog($msg);
            throw new Exception();
        }

        // リビジョンを取得する
        $result = $controlGit->getFileRevision($filePath, $revision, $errMsg);

        if(0 != $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5005', array($errMsg));
            outputLog($msg);
            throw new Exception();
        }

        $updateData['FILE_STATUS_ID'] = 6;  // 払戻完了
        if("" == $updateData['CLOSE_DATE']){
            $updateData['CLOSE_DATE'] = date('Y/m/d H:i:s');
        }
        if("" == $updateData['CLOSE_USER_ID']){
            $updateData['CLOSE_USER_ID'] = USER_ID_MATERIAL_MANAGEMENT;
        }
        $updateData['CLOSE_REVISION'] = $revision;
        $updateData['LAST_UPDATE_USER'] = USER_ID_MATERIAL_MANAGEMENT;

        // トランザクション開始
        $result = $objDBCA->transactionStart();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', array($result));
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = true;

        //////////////////////////
        // ファイル管理テーブルを更新
        //////////////////////////
        $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
        if(true !== $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }

        // コミット
        $result = $objDBCA->transactionCommit();
        if(false === $result){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            throw new Exception();
        }
        $tranStartFlg = false;

        $repaymentCnt ++;

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
 * ディレクトリ権限・所有者変更
 * 
 */
function setDirOwn($dirId, $controlGit){

    global $objMTS, $objDBCA, $db_model_ch;

    $dirMasterTable = new DirMasterTable($objDBCA, $db_model_ch);

    $targetDirId = $dirId;
    $dirArray = array();

    while(true){
        //////////////////////////
        // ディレクトリマスタビューを検索
        //////////////////////////
        $sql = $dirMasterTable->createSselect("WHERE DIR_ID=" . $targetDirId . " AND DISUSE_FLAG='0'");

        // SQL実行
        $result = $dirMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            outputLog($msg);
            return false;
        }
        $dirMasterArray = $result;

        if(0 === count($dirMasterArray)){
            break;
        }

        $dirArray[] = $dirMasterArray[0];

        if("" == $dirMasterArray[0]['PARENT_DIR_ID']){
            break;
        }
        else{
            $targetDirId = $dirMasterArray[0]['PARENT_DIR_ID'];
        }
    }

    $dirArray = array_reverse($dirArray);

    $dirNameConnect = $controlGit->getCloneRepoDir();

    foreach($dirArray as $dirData){
        $dirName = $dirData['DIR_NAME'];
        $chmod = $dirData['CHMOD'];
        $groupAuth = $dirData['GROUP_AUTH'];
        $userAuth = $dirData['USER_AUTH'];

        $dirNameSep = explode("/", $dirName);

        foreach($dirNameSep as $dirNameSepData){

            if("" == $dirNameSepData){
                continue;
            }

            $dirNameConnect .= "/" . $dirNameSepData;

            // Git上のディレクトリの権限を変更する
            $output = NULL;
            $cmd = "chmod -- " . $chmod . " '" .$dirNameConnect . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                return false;
            }

            // ファイルの所有者を確認する
            $outGroupAuth = "";
            $outUserAuth = "";
            $result = chkOwn($groupAuth, $userAuth, $outGroupAuth, $outUserAuth);

            if(true != $result){
                return false;
            }

            // Git上のファイルの所有者を変更する
            $output = NULL;
            $cmd = "chown -- " . $outGroupAuth . ":" . $outUserAuth . " '" . $dirNameConnect . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
                outputLog($msg);
                return false;
            }
        }
    }
    return true;
}

/**
 * 所有者チェック
 * 
 */
function chkOwn($inGroupAuth, $inUserAuth, &$outGroupAuth, &$outUserAuth){

    $inGroupAuthCol = $inGroupAuth . ":";
    $inUserAuthCol = $inUserAuth . ":";

    // グループの存在を確認する
    $output = NULL;
    $cmd = "cat /etc/group 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
        outputLog($msg);
        return false;
    }

    $outGroupAuth = "root";
    foreach($output as $subOutput){
        if(substr($subOutput, 0, strlen($inGroupAuthCol)) === $inGroupAuthCol){
            $outGroupAuth = $inGroupAuth;
            break;
        }
    }

    // 所有者の存在を確認する
    $output = NULL;
    $cmd = "cat /etc/passwd 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        $msg = $objMTS->getSomeMessage('ITAMATERIAL-ERR-5007', array($cmd, $return_var, print_r($output, true)));
        outputLog($msg);
        return false;
    }

    $outUserAuth = "root";
    foreach($output as $subOutput){
        if(substr($subOutput, 0, strlen($inUserAuthCol)) === $inUserAuthCol){
            $outUserAuth = $inUserAuth;
            break;
        }
    }
    return true;
}
