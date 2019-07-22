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
//    ・資材管理の初期同期を行う。
//
//////////////////////////////////////////////////////////////////////

require_once ("{$g['root_dir_path']}/libs/backyardlibs/material/ky_material_classes.php");
require_once ("{$g['root_dir_path']}/libs/backyardlibs/material/ky_ControlGit.php");

/**
 * 初期同期クラス
 */
class InitialSync {

    private $strLogMsg;

    /**
     * コンストラクタ
     */
    public function __construct(){
    }

    /**
     * 処理実行
     */
    public function execute($remortRepoUrl, $branch, $cloneRepoDir, $password, $strTid){

        global $g;
        $this->strLogMsg = "";
        $strAlertMsg = "";
        $decPassword = ky_decrypt($password);
        $dbh = NULL;
        $cloneRepoFlg = false;
        $intErrorType = 0;
        $tranStartFlg = false;
        $uploadFilePath = "{$g['root_dir_path']}/uploadfiles/2100150101/";

        try{
            // クローンリポジトリがある場合
            if(file_exists($cloneRepoDir)){

                // ディレクトリの場合
                if(is_dir($cloneRepoDir)){

                    $output = NULL;
                    $cmd = "sudo find '" . $cloneRepoDir . "' -type f 2>&1";
                    exec($cmd, $output, $return_var);

                    if(0 === $return_var){
                        // ファイルが存在する場合
                        if(0 != count($output)){
                            throw new Exception($g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1001"));
                        }
                    }
                    // システムエラーの場合
                    else{
                        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                    }
                }
                // ファイルの場合
                else{
                    throw new Exception($g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1002"));
                }
            }
            // クローンリポジトリが無い場合
            else{

                // ディレクトリを作成する
                $output = NULL;
                $cmd = "sudo mkdir -p -m 777 -- '" . $cloneRepoDir . "' 2>&1";
                exec($cmd, $output, $return_var);

                // システムエラーの場合
                if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }
            }

            $cloneRepoFlg = true;
            $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $decPassword, "{$g['root_dir_path']}/libs/backyardlibs/material/");

            // Gitのクローンを作成する
            $errMsg = NULL;
            $result = $controlGit->cloneGit($errMsg);

            // パスワードエラーの場合
            if(253 == $result){
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", $errMsg));
                throw new Exception($g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1003"));
            }
            // システムエラーの場合
            else if(0 != $result){
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", $errMsg));
                throw new Exception($g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1004"));
            }

            // クローンリポジトリ直下のファイル一覧を取得する
            $output = NULL;
            $cmd = "sudo ls -1 -- '" . $cloneRepoDir . "' 2>&1";
            exec($cmd, $output, $return_var);

            // システムエラーの場合
            if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $cloneDirFileList = $output;

            // ダミーファイルの名前を決定する
            $i = 0;
            $dummyFilePre = "_dummy.txt";
            while(true){
                $dummyFile = $i . $dummyFilePre;
                $matchFlg = false;
                foreach($cloneDirFileList as $cloneDirFile){
                    if($cloneDirFile == $dummyFile){
                        $matchFlg = true;
                    }
                }
                if(false === $matchFlg){
                    break;
                }
                $i ++;
            }

            $dummyFileFullpath = $cloneRepoDir . "/" . $dummyFile;

            // ダミーファイルでadd、commit、pushが可能か確認する
            // ダミーファイル作成
            $output = NULL;
            $cmd = "sudo touch '" . $dummyFileFullpath . "' 2>&1";
            exec($cmd, $output, $return_var);

            // システムエラーの場合
            if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }

            // ダミーファイルが登録できるか確認する
            $errMsg = NULL;
            $result = $controlGit->commitGit($dummyFile, $errMsg);

            if(0 != $result){
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", $errMsg));
                throw new Exception($g['objMTS']->getSomeMessage("ITAMATERIAL-ERR-1005"));
            }

            // ダミーファイルを削除
            $errMsg = NULL;
            $result = $controlGit->removeGit($dummyFile, $errMsg);

            if(0 != $result){
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", $errMsg));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }

            // ディレクトリ一覧取得
            $output = NULL;
            $cmd = "sudo find '" . $cloneRepoDir . "/' -type d 2>&1";
            exec($cmd, $output, $return_var);

            // システムエラーの場合
            if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $dirList = $output;

            $insertDirList = array();
            $insertKey = 0;

            // ルートディレクトリ分のデータを設定
            $insertDirList[$insertKey]['DIR_FULLPATH'] = "/";
            $insertDirList[$insertKey]['PARENT_DIR'] = "";
            $insertDirList[$insertKey]['CHILD_DIR'] = "/";
            $insertKey ++;

            foreach($dirList as $key => $dirData){

                // substr_replace以降のパスを取得
                $dirData = substr_replace($dirData, "", 0, strlen($cloneRepoDir));

                // 「/.git」配下のディレクトリは除外
                if(substr($dirData, 0, strlen("/.git")) === "/.git"){
                    continue;
                }

                // ディレクトリを/で分割
                $explodeDirDataList = explode("/", substr_replace($dirData, "", 0, 1));
                $tmpDirData = "";

                foreach($explodeDirDataList as $key => $explodeDirData){

                    $tmpDirData = $tmpDirData . "/" . $explodeDirData;
                    $insertDirFullpathList = array_column($insertDirList, 'DIR_FULLPATH');

                    if(!in_array($tmpDirData, $insertDirFullpathList)){

                        $insertDirList[$insertKey]['DIR_FULLPATH'] = $tmpDirData . "/";
                        $insertDirList[$insertKey]['PARENT_DIR'] = dirname($tmpDirData);
                        if("/" != mb_substr($insertDirList[$insertKey]['PARENT_DIR'], -1)){
                            $insertDirList[$insertKey]['PARENT_DIR'] = $insertDirList[$insertKey]['PARENT_DIR'] . "/";
                        }
                        $insertDirList[$insertKey]['CHILD_DIR'] = basename($tmpDirData);
                        $insertKey ++;
                    }
                }
            }

            // トランザクション開始
            $result = $g['objDBCA']->transactionStart();
            if(false === $result){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $tranStartFlg = true;
            $dirMasterTable = new DirMasterTable($g['objDBCA'], $g['db_model_ch']);

            // メニューに無いデータを登録する
            foreach($insertDirList as $insertDirData){

                //////////////////////////
                // ディレクトリマスタを検索
                //////////////////////////
                $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

                // SQL実行
                $result = $dirMasterTable->selectTable($sql);
                if(!is_array($result)){
                    web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }
                $dirMasterArray = $result;
                $dirMasterDirList = array_column($dirMasterArray, 'DIR_NAME_FULLPATH');

                // ディレクトリが未登録かどうか確認
                if(!in_array($insertDirData['DIR_FULLPATH'], $dirMasterDirList)){

                    // 親ディレクトリを確認
                    $parentId = array_search($insertDirData['PARENT_DIR'], $dirMasterDirList);
                    if(false !== $parentId){

                        $insertData = array();
                        $insertData['DIR_ID']                   = "";
                        $insertData['DIR_NAME']                 = $insertDirData['CHILD_DIR'];
                        $insertData['PARENT_DIR_ID']            = $dirMasterArray[$parentId]['DIR_ID'];
                        $insertData['DIR_NAME_FULLPATH']        = $insertDirData['DIR_FULLPATH'];
                        $insertData['CHMOD']                    = "755";
                        $insertData['GROUP_AUTH']               = "root";
                        $insertData['USER_AUTH']                = "root";
                        $insertData['DIR_USAGE']                = "";
                        $insertData['NOTE']                     = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50006');
                        $insertData['DISUSE_FLAG']              = "0";
                        $insertData['LAST_UPDATE_TIMESTAMP']    = "";
                        $insertData['LAST_UPDATE_USER']         = -101501;

                        //////////////////////////
                        // ディレクトリマスタに登録
                        //////////////////////////
                        $result = $dirMasterTable->insertTable($insertData);
                        if(true !== $result){
                            web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                            throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                        }
                    }
                }
            }

            //////////////////////////
            // ディレクトリマスタを検索
            //////////////////////////
            $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $dirMasterTable->selectTable($sql);
            if(!is_array($result)){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $dirMasterArray = $result;

            // メニューにしか無いデータを廃止する
            $insertDirFullpathList = array_column($insertDirList, 'DIR_FULLPATH');
            $disuseDataArray = array();
            foreach($dirMasterArray as $dirMasterData){

                if(!in_array($dirMasterData['DIR_NAME_FULLPATH'], $insertDirFullpathList)){

                    $disuseData = $dirMasterData;
                    $disuseData['DISUSE_FLAG']      = "1";                                                      // 廃止
                    $disuseData['NOTE']             = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50007');    // 備考
                    $disuseData['LAST_UPDATE_USER'] = -101501;                                                  // 最終更新者

                    //////////////////////////
                    // ディレクトリマスタを更新
                    //////////////////////////
                    $result = $dirMasterTable->updateTable($disuseData);
                    if(true !== $result){
                        web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                    }
                }
            }

            // ファイル一覧取得
            $output = NULL;
            $cmd = "sudo find '" . $cloneRepoDir . "' -type f 2>&1";
            exec($cmd, $output, $return_var);

            // システムエラーの場合
            if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $fileList = $output;

            $insertFileList = array();
            $insertKey = 0;

            foreach($fileList as $key => $fileData){

                $fileData = substr_replace($fileData, "", 0, strlen($cloneRepoDir));

                // 「/.git」配下のディレクトリは除外
                if(substr($fileData, 0, strlen("/.git")) === "/.git"){
                    continue;
                }

                // ディレクトリとファイル名を取得
                $fileDataDir = substr($fileData, 0, strrpos($fileData, "/"));
                $fileDataDir = $fileDataDir === "" ? "/" : $fileDataDir;
                $fileDataName = substr($fileData, strrpos($fileData, "/") + 1);

                $insertFileList[$insertKey]['FILE_FULLPATH'] = $fileData;
                $insertFileList[$insertKey]['DIR'] = $fileDataDir . "/";
                $insertFileList[$insertKey]['FILE'] = $fileDataName;
                $insertKey ++;
            }

            //////////////////////////
            // 資材マスタを検索
            //////////////////////////
            $fileMasterView = new FileMasterView($g['objDBCA'], $g['db_model_ch']);
            $sql = $fileMasterView->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileMasterView->selectTable($sql);
            if(!is_array($result)){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $fileMasterArray = $result;

            $fileMasterTable = new FileMasterTable($g['objDBCA'], $g['db_model_ch']);
            $fileMasterFileList = array_column($fileMasterArray, 'FILE_NAME_FULLPATH');
            $dirMasterDirList = array_column($dirMasterArray, 'DIR_NAME_FULLPATH');

            // メニューに無いデータを登録する
            foreach($insertFileList as $insertFileData){

                if(!in_array($insertFileData['FILE_FULLPATH'], $fileMasterFileList)){

                    $dirIdx = array_search($insertFileData['DIR'], $dirMasterDirList);

                    $insertData = array();
                    $insertData['FILE_ID']                  = "";
                    $insertData['FILE_NAME']                = $insertFileData['FILE'];
                    $insertData['DIR_ID']                   = $dirMasterArray[$dirIdx]['DIR_ID'];
                    $insertData['AUTO_RETURN_FLAG']         = 1;
                    $insertData['CHMOD']                    = "644";
                    $insertData['GROUP_AUTH']               = "root";
                    $insertData['USER_AUTH']                = "root";
                    $insertData['NOTE']                     = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50006');
                    $insertData['DISUSE_FLAG']              = "0";
                    $insertData['LAST_UPDATE_TIMESTAMP']    = "";
                    $insertData['LAST_UPDATE_USER']         = -101501;

                    //////////////////////////
                    // 資材マスタに登録
                    //////////////////////////
                    $result = $fileMasterTable->insertTable($insertData);
                    if(true !== $result){
                        web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                    }
                }
            }

            $insertFileFullpathList = array_column($insertFileList, 'FILE_FULLPATH');

            // メニューにしか無いデータを廃止する
            foreach($fileMasterArray as $fileMasterData){

                if(!in_array($fileMasterData['FILE_NAME_FULLPATH'], $insertFileFullpathList)){

                    $disuseData = $fileMasterData;
                    $disuseData['DISUSE_FLAG']      = "1";                                                      // 廃止
                    $disuseData['NOTE']             = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50007');    // 備考
                    $disuseData['LAST_UPDATE_USER'] = -101501;                                                  // 最終更新者

                    //////////////////////////
                    // 資材マスタを更新
                    //////////////////////////
                    $result = $fileMasterTable->updateTable($disuseData);
                    if(true !== $result){
                        web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                    }

                }
            }

            // 資材管理廃止処理を行う
            $this->disuseFileManagement($dbh);

            // 資材管理初期登録処理を行う
            $this->initialRegistFileManagement(array_column($insertFileList, 'FILE_FULLPATH'), $controlGit, $uploadFilePath);

            // コミット
            $result = $g['objDBCA']->transactionCommit();
            if(false === $result){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $tranStartFlg = false;

        }
        catch(Exception $e){
            $intErrorType = 9;
            $strAlertMsg = $e->getMessage();

            // クローンリポジトリ削除
            if(true === $cloneRepoFlg){
                $this->rmCloneRepoDir($cloneRepoDir);
            }
            // ロールバック
            if(true === $tranStartFlg){
                $g['objDBCA']->transactionRollback();
            }
        }

        $result = array();
        $result[] = sprintf("%03d", $intErrorType);
        $result[] = $strAlertMsg;
        $result[] = $strTid;

        return $result;
    }

    /**
     * 資材管理廃止処理
     */
    private function disuseFileManagement(){

        global $g;
        $fileManagementTable = new FileManagementTable($g['objDBCA'], $g['db_model_ch']);

        try{
            //////////////////////////
            // ファイル管理テーブルを検索
            //////////////////////////
            $sql = $fileManagementTable->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileManagementTable->selectTable($sql);
            if(!is_array($result)){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $fileManagementArray = $result;

            foreach($fileManagementArray as $fileManagementData) {

                $disuseData = $fileManagementData;
                $disuseData['DISUSE_FLAG']      = "1";                                                      // 廃止
                $disuseData['NOTE']             = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50007');    // 備考
                $disuseData['LAST_UPDATE_USER'] = -101501;                                                  // 最終更新者

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($disuseData);
                if(true !== $result){
                    web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }
            }
        }
        catch(Exception $e){
            //エラー処理は上位で行う
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 資材管理初期登録処理
     */
    private function initialRegistFileManagement($fileList, $controlGit, $uploadFilePath){

        global $g;

        try{
            // 最新のリビジョンを取得
            $errMsg = NULL;
            $revision = "";
            $result = $controlGit->getRevision($revision, $errMsg);

            if(0 != $result){
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", $errMsg));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }

            $fileManagementInitialTable = new FileManagementInitialTable($g['objDBCA'], $g['db_model_ch']);

            //////////////////////////
            // ファイル管理テーブル（初期登録用）をトランケート
            //////////////////////////
            $result = $fileManagementInitialTable->truncate();
            if(true !== $result){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }

            //////////////////////////
            // 資材マスタを検索
            //////////////////////////
            $fileMasterView = new FileMasterView($g['objDBCA'], $g['db_model_ch']);
            $sql = $fileMasterView->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileMasterView->selectTable($sql);
            if(!is_array($result)){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $fileMasterArray = $result;


            $fullPathArray = array_column($fileMasterArray, 'FILE_NAME_FULLPATH');

            foreach($fileList as $fileData){

                // 該当データがない場合
                $fileIdx = array_search($fileData, $fullPathArray);

                if(false === $fileIdx){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", "Data could not be obtained from table[G_FILE_MASTER].FILE_NAME_FULLPATH=" . $fileData));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }

                // 最新時間を取得
                $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
                $nowTime = date("YmdHis.") . $now->format("u");

                $insertData = array();
                $insertData['FILE_STATUS_ID']           = 10;  // 新規登録
                $insertData['FILE_ID']                  = $fileMasterArray[$fileIdx]['FILE_ID'];
                $insertData['RETURN_FILE']              = basename($fileData);
                $insertData['CLOSE_DATE']               = $nowTime;
                $insertData['CLOSE_USER_ID']            = -101501;
                $insertData['CLOSE_REVISION']           = $revision;
                $insertData['NOTE']                     = $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50006');
                $insertData['DISUSE_FLAG']              = "0";
                $insertData['LAST_UPDATE_TIMESTAMP']    = "";
                $insertData['LAST_UPDATE_USER']         = -101501;

                //////////////////////////
                // ファイル管理テーブル（初期登録用）に登録
                //////////////////////////
                $result = $fileManagementInitialTable->insertTable($insertData);
                if(true !== $result){
                    web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }
            }

            // アップロードファイルのディレクトリを削除する
            $rmDir = $uploadFilePath . "" . "RETURN_FILE/-*";
            $output = NULL;
            $cmd = "sudo rm -rf -- '" . $rmDir . "' 2>&1";
            exec($cmd, $output, $return_var);

            // システムエラーの場合
            if(0 != $return_var){
                    web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                    throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }

            //////////////////////////
            // ファイル管理テーブル（初期登録用）を検索
            //////////////////////////
            $sql = $fileManagementInitialTable->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileManagementInitialTable->selectTable($sql);
            if(!is_array($result)){
                web_log($g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', array($result)));
                throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
            }
            $filemanagementInitialArray = $result;

            foreach($filemanagementInitialArray as $filemanagementInitialData) {

                $fileMId = (-1) * intval($filemanagementInitialData['FILE_M_ID']);
                $returnFile = $filemanagementInitialData['RETURN_FILE'];
                $fileIdArray = array_column($fileMasterArray, 'FILE_ID');
                $fileMasterIdx = array_search($filemanagementInitialData['FILE_ID'], $fileIdArray);
                $fileNameFullpath = $fileMasterArray[$fileMasterIdx]['FILE_NAME_FULLPATH'];

                // 払戻資材のパスを設定する
                $returnDir = $uploadFilePath . "RETURN_FILE/";
                $returnFileDir = $returnDir . sprintf("%010d", $fileMId). "/";
                $returnFilePath = $returnFileDir . $returnFile;
                $gitFilePath = $controlGit->getCloneRepoDir() . $fileNameFullpath;

                if(!file_exists($returnDir)){
                    // アップロードファイルのディレクトリを作成する
                    $cmd = "mkdir -p -m 777 -- '" . $returnDir . "' 2>&1";
                    exec($cmd, $output, $return_var);

                    // システムエラーの場合
                    if(0 != $return_var){
                            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                            throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                    }
                }

                // アップロードファイルのディレクトリを作成する
                $cmd = "mkdir -p -m 777 -- '" . $returnFileDir . "' 2>&1";
                exec($cmd, $output, $return_var);

                // システムエラーの場合
                if(0 != $return_var){
                        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }

                // アップロードファイルを配置する
                $cmd = "sudo cp -p -- '" . $gitFilePath . "' '" . $returnFilePath . "' 2>&1";
                exec($cmd, $output, $return_var);

                // システムエラーの場合
                if(0 != $return_var){
                        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-2001", print_r($output, true)));
                        throw new Exception($g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001"));
                }
            }
        }
        catch(Exception $e){
            //エラー処理は上位で行う
            throw new Exception($e->getMessage());
        }
    }

    /**
     * クローンリポジトリ削除
     */
    private function rmCloneRepoDir($cloneRepoDir) {
        if(file_exists($cloneRepoDir)) {
            $output = NULL;
            $cmd = "sudo rm -rf -- '" . $cloneRepoDir . "' 2>&1";
            exec($cmd, $output, $return_var);
        }
    }
}
?>
