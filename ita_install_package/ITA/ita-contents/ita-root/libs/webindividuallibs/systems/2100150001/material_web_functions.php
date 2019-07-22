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
 *    関数定義
 */

require_once ( $root_dir_path . "/libs/backyardlibs/material/ky_ControlGit.php");
require_once ( $root_dir_path . "/libs/backyardlibs/material/ky_material_classes.php");

/**
 * ディレクトリマスタの更新に基づきGitを更新する
 * 
 */
function updateGitDir($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){
    global $g;

    $intErrorType = null;
    $retStrLastErrMsg = null;
    $toDirFullpath = null;

    try{
        $beforeData = $aryVariant['edit_target_row'];
        $afterData = $reqUpdateData;

        //////////////////////////
        // ディレクトリマスタを検索
        //////////////////////////
        $dirMasterTable = new DirMasterTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $dirMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $dirMasterArray = $result;

        // ディレクトリが変更されていた場合
        if($beforeData['DIR_NAME'] != $afterData['DIR_NAME']){

            // 子ディレクトリのパスを変更する
            $result = updateChildDir($dirMasterTable, $beforeData['DIR_ID'], $afterData['DIR_NAME_FULLPATH'], $afterData['LAST_UPDATE_USER'], $dirMasterArray);

            if(true !== $result){
                throw new Exception($result);
            }
        }

        //////////////////////////
        // インターフェース情報テーブルを検索
        //////////////////////////
        $materialIfInfoTable = new MaterialIfInfoTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $materialIfInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $materialIfInfoTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $materialIfInfoArray = $result;

        // インターフェース情報が1件ではない場合はエラー
        if(1 != count($materialIfInfoArray)) {
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5002');
            throw new Exception($msg);
        }

        // リモートリポジトリURLとクローンリポジトリが設定されていない場合は何もしない
        if("" == $materialIfInfoArray[0]['REMORT_REPO_URL'] || "" == $materialIfInfoArray[0]['CLONE_REPO_DIR']) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        // クローンリポジトリにGitが設定されていない場合は何もしない
        if(!file_exists($materialIfInfoArray[0]['CLONE_REPO_DIR'] . "/.git")) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        $remortRepoUrl = $materialIfInfoArray[0]['REMORT_REPO_URL'];
        $branch = $materialIfInfoArray[0]['BRANCH'];
        $cloneRepoDir = $materialIfInfoArray[0]['CLONE_REPO_DIR'];
        $password = ky_decrypt($materialIfInfoArray[0]['PASSWORD']);
        $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $password, "{$g['root_dir_path']}/libs/backyardlibs/material/");

        $fromDIrPath = $beforeData['DIR_NAME_FULLPATH'];
        $fromDIrFullpath = $cloneRepoDir .  $fromDIrPath;

        // ディレクトリが存在する場合
        if(is_dir($fromDIrFullpath)){

            // 親ディレクトリ、ディレクトリのいずれかが変更されていた場合
            if($beforeData['PARENT_DIR_ID'] != $afterData['PARENT_DIR_ID'] ||
               $beforeData['DIR_NAME']      != $afterData['DIR_NAME']){

                // 変更後のフルパスを取得する
                $parentId = $afterData['PARENT_DIR_ID'];
                $toDirPath = $afterData['DIR_NAME'];
                $afterParentDirArray = array();
                $breakFlg = false;

                while(true){
                    foreach($dirMasterArray as $dirMaster){
                        if($dirMaster['DIR_ID'] == $parentId){
                            if($dirMaster['DIR_ID'] == "1"){
                                $toDirPath = "/" . $toDirPath;
                                $breakFlg = true;
                                break;
                            }
                            else{
                                $parentId = $dirMaster['PARENT_DIR_ID'];
                                $toDirPath = $dirMaster['DIR_NAME'] . "/" . $toDirPath;
                                $afterParentDirArray[] = array($dirMaster['DIR_NAME'], $dirMaster['CHMOD'], $dirMaster['GROUP_AUTH'], $dirMaster['USER_AUTH']);
                                break;
                            }
                        }
                    }
                    if(true === $breakFlg){
                        break;
                    }
                }

                $toDirFullpath = $cloneRepoDir . $toDirPath;

                // 変更後の親ディレクトリを作成する
                $afterParentDirArray = array_reverse($afterParentDirArray);
                $parentDir = $cloneRepoDir;
                foreach($afterParentDirArray as $afterParentDirInfo){

                    $parentDir = $parentDir . "/" . $afterParentDirInfo[0];

                    // ディレクトリが存在する場合、何もしない
                    if(is_dir($parentDir)){
                        continue;
                    }

                    // ディレクトリ作成
                    $output = NULL;
                    $cmd = "sudo mkdir -m -- " . $afterParentDirInfo[1] . " '" . $parentDir . "' 2>&1";
                    exec($cmd, $output, $return_var);

                    // システムエラーの場合
                    if(0 != $return_var){
                        throw new Exception(print_r($output, true));
                    }

                    // グループと所有者を確認する
                    $result = chkOwn($afterParentDirInfo[2], $afterParentDirInfo[3], $groupAuth, $userAuth);

                    // システムエラーの場合
                    if(true != $result){
                        throw new Exception($result);
                    }

                    // グループと所有者を変更する
                    $output = NULL;
                    $cmd = "sudo chown -- " . $groupAuth . ":" . $userAuth . " '" . $parentDir . "' 2>&1";
                    exec($cmd, $output, $return_var);

                    if(0 != $return_var){
                        throw new Exception(print_r($output, true));
                    }
                }

                // Git上の変更を行う
                $result = $controlGit->mvGit($fromDIrPath, $toDirPath, $errMsg);

                if(0 != $result){
                    throw new Exception($errMsg);
                }
            }

            if(null === $toDirFullpath){
                $toDirFullpath = $fromDIrFullpath;
            }

            // 権限が変更されていた場合
            if($beforeData['CHMOD'] != $afterData['CHMOD']){

                // 権限を変更する
                $output = NULL;
                $cmd = "sudo chmod -- " . $afterData['CHMOD'] . " '" . $toDirFullpath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    throw new Exception(print_r($output, true));
                }
            }

            // グループ、ユーザのいずれかが変更されていた場合
            if($beforeData['GROUP_AUTH']    != $afterData['GROUP_AUTH'] ||
               $beforeData['USER_AUTH']     != $afterData['USER_AUTH']){

                // グループと所有者を確認する
                $result = chkOwn($afterData['GROUP_AUTH'], $afterData['USER_AUTH'], $groupAuth, $userAuth);

                // システムエラーの場合
                if(true != $result){
                    throw new Exception($result);
                }

                // グループと所有者を変更する
                $output = NULL;
                $cmd = "sudo chown -- " . $groupAuth . ":" . $userAuth . " '" . $toDirFullpath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    throw new Exception(print_r($output, true));
                }
            }
        }
    }
    catch (Exception $e){
        $intErrorType = 500;
        $strTmpStrBody = 'ERROR([FILE]' .  __FILE__  . ',[LINE]' . $e->getLine() . ')' . ' ' . $e->getMessage();
        web_log($strTmpStrBody);
    }

    return array( null, $intErrorType, array($retStrLastErrMsg) );
}

/**
 * ディレクトリマスタの廃止に基づきGitから削除する
 * 
 */
function deleteGitDir($intBaseMode, $strNumberForRI, $reqDeleteData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){
    global $g;

    $intErrorType = null;
    $retStrLastErrMsg = null;

    try{
        // 復活の場合は何もしない
        if(5 == $intBaseMode){
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        //////////////////////////
        // ディレクトリマスタを検索
        //////////////////////////
        $dirMasterTable = new DirMasterTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $dirMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $dirMasterArray = $result;

        $disuseDirArray = array();
        // 配下のディレクトリを特定する
        foreach($dirMasterArray as $dirMasterData){
            // 廃止されたデータとIDが異なり、かつ廃止されたデータのディレクトリ配下だった場合は廃止対象
            if($aryVariant['edit_target_row']['DIR_ID'] != $dirMasterData['DIR_ID'] &&
               substr($dirMasterData['DIR_NAME_FULLPATH'], 0, strlen($reqDeleteData['DIR_NAME_FULLPATH'])) === $reqDeleteData['DIR_NAME_FULLPATH']){

                $disuseDirArray[] = $dirMasterData;
            }
        }

        // 配下のディレクトリをすべて廃止にする
        foreach($disuseDirArray as $dirMasterData){

            $disuseData = $dirMasterData;
            if("" != $disuseData['NOTE']){
                $disuseData['NOTE'] .= "\n\n";
            }
            $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50008');
            $disuseData['DISUSE_FLAG']      = "1";      // 廃止
            $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

            //////////////////////////
            // ディレクトリマスタを更新
            //////////////////////////
            $result = $dirMasterTable->updateTable($disuseData);
            if(true !== $result){
                $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                throw new Exception($msg);
            }
        }

        // 廃止対象のディレクトリIDを設定
        $disuseDirIdArray = array_column($disuseDirArray, 'DIR_ID');
        $disuseDirIdArray[] = $aryVariant['edit_target_row']['DIR_ID'];

        //////////////////////////
        // 資材マスタを検索
        //////////////////////////
        $fileMasterTable = new FileMasterTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $fileMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $fileMasterArray = $result;

        $disuseFileArray = array();
        // 配下の資材を特定する
        foreach($fileMasterArray as $fileMasterData){
            // 廃止対象のディレクトリ配下の資材は廃止対象
            if(in_array($fileMasterData['DIR_ID'], $disuseDirIdArray)){
                $disuseFileArray[] = $fileMasterData;
            }
        }

        // 配下の資材をすべて廃止にする
        foreach($disuseFileArray as $fileMasterData){

            $disuseData = $fileMasterData;
            if("" != $disuseData['NOTE']){
                $disuseData['NOTE'] .= "\n\n";
            }
            $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50008');
            $disuseData['DISUSE_FLAG']      = "1";      // 廃止
            $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

            //////////////////////////
            // 資材マスタを更新
            //////////////////////////
            $fileMasterTable = new FileMasterTable($g['objDBCA'], $g['db_model_ch']);
            $result = $fileMasterTable->updateTable($disuseData);
            if(true !== $result){
                $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                throw new Exception($msg);
            }
        }

        // 廃止対象のファイルIDを設定
        $disuseFileIdArray = array_column($disuseFileArray, 'FILE_ID');

        //////////////////////////
        // ファイル管理テーブル（初期登録用）を検索
        //////////////////////////
        $fileManagementInitialTable = new FileManagementInitialTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $fileManagementInitialTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileManagementInitialTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $fileManagementArray = $result;

        foreach($fileManagementArray as $fileManagementData){
            // 廃止対象の資材と一致した場合は廃止対象
            if(in_array($fileManagementData['FILE_ID'], $disuseFileIdArray)){

                $disuseData = $fileManagementData;
                if("" != $disuseData['NOTE']){
                    $disuseData['NOTE'] .= "\n\n";
                }
                $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50008');
                $disuseData['DISUSE_FLAG']      = "1";      // 廃止
                $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                //////////////////////////
                // ファイル管理テーブル（初期登録用）を更新
                //////////////////////////
                $result = $fileManagementInitialTable->updateTable($disuseData);
                if(true !== $result){
                    $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // ファイル管理テーブルを検索
        //////////////////////////
        $fileManagementTable = new FileManagementTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $fileManagementTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileManagementTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $fileManagementArray = $result;

        foreach($fileManagementArray as $fileManagementData){
            // 廃止対象の資材と一致した場合は廃止対象
            if(in_array($fileManagementData['FILE_ID'], $disuseFileIdArray)){

                $disuseData = $fileManagementData;
                if("" != $disuseData['NOTE']){
                    $disuseData['NOTE'] .= "\n\n";
                }
                $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50008');
                $disuseData['DISUSE_FLAG']      = "1";      // 廃止
                $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($disuseData);
                if(true !== $result){
                    $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // インターフェース情報テーブルを検索
        //////////////////////////
        $materialIfInfoTable = new MaterialIfInfoTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $materialIfInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $materialIfInfoTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $materialIfInfoArray = $result;

        // インターフェース情報が1件ではない場合はエラー
        if(1 != count($materialIfInfoArray)) {
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5002');
            throw new Exception($msg);
        }

        // リモートリポジトリURLとクローンリポジトリが設定されていない場合は何もしない
        if("" == $materialIfInfoArray[0]['REMORT_REPO_URL'] || "" == $materialIfInfoArray[0]['CLONE_REPO_DIR']) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        // クローンリポジトリにGitが設定されていない場合は何もしない
        if(!file_exists($materialIfInfoArray[0]['CLONE_REPO_DIR'] . "/.git")) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        $remortRepoUrl = $materialIfInfoArray[0]['REMORT_REPO_URL'];
        $branch = $materialIfInfoArray[0]['BRANCH'];
        $cloneRepoDir = $materialIfInfoArray[0]['CLONE_REPO_DIR'];
        $password = ky_decrypt($materialIfInfoArray[0]['PASSWORD']);
        $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $password, "{$g['root_dir_path']}/libs/backyardlibs/material/");

        // ディレクトリが存在する場合
        if(is_dir($cloneRepoDir . $reqDeleteData['DIR_NAME_FULLPATH'])){

            $result = $controlGit->removeGitDir($reqDeleteData['DIR_NAME_FULLPATH'], $errMsg);

            if(0 != $result){
                throw new Exception($errMsg);
            }
        }
    }
    catch (Exception $e){
        $intErrorType = 500;
        $strTmpStrBody = 'ERROR([FILE]' .  __FILE__  . ',[LINE]' . $e->getLine() . ')' . ' ' . $e->getMessage();
        web_log($strTmpStrBody);
    }

    return array( null, $intErrorType, array($retStrLastErrMsg) );   
}

/**
 * 資材マスタの更新に基づきGitを更新する
 * 
 */
function updateGitFile($intBaseMode, $strNumberForRI, $reqUpdateData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){
    global $g;

    $intErrorType = null;
    $retStrLastErrMsg = null;
    $toDirFullpath = null;

    try{
        $beforeData = $aryVariant['edit_target_row'];
        $afterData = $reqUpdateData;

        //////////////////////////
        // インターフェース情報テーブルを検索
        //////////////////////////
        $materialIfInfoTable = new MaterialIfInfoTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $materialIfInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $materialIfInfoTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $materialIfInfoArray = $result;

        // インターフェース情報が1件ではない場合はエラー
        if(1 != count($materialIfInfoArray)) {
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5002');
            throw new Exception($msg);
        }

        // リモートリポジトリURLとクローンリポジトリが設定されていない場合は何もしない
        if("" == $materialIfInfoArray[0]['REMORT_REPO_URL'] || "" == $materialIfInfoArray[0]['CLONE_REPO_DIR']) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        // クローンリポジトリにGitが設定されていない場合は何もしない
        if(!file_exists($materialIfInfoArray[0]['CLONE_REPO_DIR'] . "/.git")) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        $remortRepoUrl = $materialIfInfoArray[0]['REMORT_REPO_URL'];
        $branch = $materialIfInfoArray[0]['BRANCH'];
        $cloneRepoDir = $materialIfInfoArray[0]['CLONE_REPO_DIR'];
        $password = ky_decrypt($materialIfInfoArray[0]['PASSWORD']);
        $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $password, "{$g['root_dir_path']}/libs/backyardlibs/material/");

        $uploadPath = "{$g['root_dir_path']}/uploadfiles/2100150101/file_management_1/";

        // ファイル名が変更されていた場合
        if($beforeData['FILE_NAME'] != $afterData['FILE_NAME']){

            //////////////////////////
            // ファイル管理テーブル（初期登録用）を検索
            //////////////////////////
            $fileManagementInitialTable = new FileManagementInitialTable($g['objDBCA'], $g['db_model_ch']);
            $sql = $fileManagementInitialTable->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileManagementInitialTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                throw new Exception($msg);
            }
            $fileManagementArray = $result;

            foreach($fileManagementArray as $fileManagementData){
                // ファイルIDが一致した場合
                if($fileManagementData['FILE_ID'] == $beforeData['FILE_ID']){

                    $updateData = $fileManagementData;
                    $updateData['ASSIGN_FILE'] = $afterData['FILE_NAME'];      // 払出資材
                    $updateData['RETURN_FILE'] = $afterData['FILE_NAME'];      // 払戻資材
                    if("" != $updateData['NOTE']){
                        $updateData['NOTE'] .= "\n\n";
                    }
                    $updateData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50009');
                    $updateData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                    //////////////////////////
                    // ファイル管理テーブル（初期登録用）を更新
                    //////////////////////////
                    $result = $fileManagementInitialTable->updateTable($updateData);
                    if(true !== $result){
                        $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                        throw new Exception($msg);
                    }

                    $fileMId = (-1) * intval($fileManagementData['FILE_M_ID']);

                    // 払戻資材のパスを設定する
                    $returnFileDir = $uploadPath . "RETURN_FILE/" . sprintf("%010d", $fileMId). "/";
                    $beforeReturnFilePath       = $returnFileDir . $beforeData['FILE_NAME'];
                    $afterReturnFilePath        = $returnFileDir . $afterData['FILE_NAME'];

                    if(is_file($beforeReturnFilePath)){

                        // 払戻資材をリネームする
                        $output = NULL;
                        $cmd = "mv -- '" . $beforeReturnFilePath . "' '" . $afterReturnFilePath . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }
                    }
                }
            }

            //////////////////////////
            // ファイル管理テーブルを検索
            //////////////////////////
            $fileManagementTable = new FileManagementTable($g['objDBCA'], $g['db_model_ch']);
            $sql = $fileManagementTable->createSselect("WHERE DISUSE_FLAG='0'");

            // SQL実行
            $result = $fileManagementTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                throw new Exception($msg);
            }
            $fileManagementArray = $result;

            foreach($fileManagementArray as $fileManagementData){
                // ファイルIDが一致、かつステータスが払戻完了の場合
                if($fileManagementData['FILE_ID'] == $beforeData['FILE_ID'] && "6" == $fileManagementData['FILE_STATUS_ID']){

                    $updateData = $fileManagementData;
                    $updateData['ASSIGN_FILE'] = $afterData['FILE_NAME'];      // 払出資材
                    $updateData['RETURN_FILE'] = $afterData['FILE_NAME'];      // 払戻資材
                    if("" != $updateData['NOTE']){
                        $updateData['NOTE'] .= "\n\n";
                    }
                    $updateData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50009');
                    $updateData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                    //////////////////////////
                    // ファイル管理テーブルを更新
                    //////////////////////////
                    $result = $fileManagementTable->updateTable($updateData, $jnlSeqNo);
                    if(true !== $result){
                        $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                        throw new Exception($msg);
                    }

                    // 払出資材のパスを設定する
                    $assignFileDir = $uploadPath . "ASSIGN_FILE/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
                    $beforeAssignFilePath       = $assignFileDir . $beforeData['FILE_NAME'];
                    $afterAssignFilePath        = $assignFileDir . $afterData['FILE_NAME'];
                    $afterAssignFileJnlDir      = $assignFileDir . "old/" . sprintf("%010d", $jnlSeqNo). "/";
                    $afterAssignFileJnlPath     = $afterAssignFileJnlDir . $afterData['FILE_NAME'];

                    if(is_file($beforeAssignFilePath)){

                        // 払出資材をリネームする
                        $output = NULL;
                        $cmd = "mv -- '" . $beforeAssignFilePath . "' '" . $afterAssignFilePath . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }

                        // 履歴ディレクトリを作成する
                        $output = NULL;
                        $cmd = "mkdir -- '" . $afterAssignFileJnlDir . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }

                        // 履歴ディレクトリにファイルを格納する
                        $output = NULL;
                        $cmd = "cp -p -- '" . $afterAssignFilePath . "' '" . $afterAssignFileJnlPath . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }
                    }

                    // 払戻資材のパスを設定する
                    $returnFileDir = $uploadPath . "RETURN_FILE/" . sprintf("%010d", $fileManagementData['FILE_M_ID']). "/";
                    $beforeReturnFilePath       = $returnFileDir . $beforeData['FILE_NAME'];
                    $afterReturnFilePath        = $returnFileDir . $afterData['FILE_NAME'];
                    $afterReturnFileJnlDir      = $returnFileDir . "old/" . sprintf("%010d", $jnlSeqNo). "/";
                    $afterReturnFileJnlPath     = $afterReturnFileJnlDir . $afterData['FILE_NAME'];

                    if(is_file($beforeReturnFilePath)){

                        // 払戻資材をリネームする
                        $output = NULL;
                        $cmd = "mv -- '" . $beforeReturnFilePath . "' '" . $afterReturnFilePath . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }

                        // 履歴ディレクトリを作成する
                        $output = NULL;
                        $cmd = "mkdir -- '" . $afterReturnFileJnlDir . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }

                        // 履歴ディレクトリにファイルを格納する
                        $output = NULL;
                        $cmd = "cp -p -- '" . $afterReturnFilePath . "' '" . $afterReturnFileJnlPath . "' 2>&1";
                        exec($cmd, $output, $return_var);

                        if(0 != $return_var){
                            throw new Exception(print_r($output, true));
                        }
                    }
                }
            }
        }

        //////////////////////////
        // ディレクトリマスタを検索
        //////////////////////////
        $dirMasterTable = new DirMasterTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $dirMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $dirMasterArray = $result;

        $beforeDir = null;
        $afterDir = null;
        // 変更前と変更後のディレクトリを取得する
        foreach($dirMasterArray as $dirMasterData){

            if($dirMasterData['DIR_ID'] == $beforeData['DIR_ID']){
                $beforeDir = $dirMasterData;
            }
            if($dirMasterData['DIR_ID'] == $afterData['DIR_ID']){
                $afterDir = $dirMasterData;
            }
        }

        // ディレクトリが取得できなかった場合は何もしない
        if(null === $beforeDir || null === $afterDir){
            return array( null, $intErrorType, array($retStrLastErrMsg) );
        }

        // ファイルパスを設定
        $beforeFilePath = $beforeDir['DIR_NAME_FULLPATH'] . $beforeData['FILE_NAME'];
        $afterFilePath  = $afterDir['DIR_NAME_FULLPATH'] . $afterData['FILE_NAME'];

        // ファイルが存在する場合
        if(is_file($cloneRepoDir . $beforeFilePath)){

            // ディレクトリ、ファイル名のいずれかが変更されていた場合
            if($beforeData['DIR_ID'] != $afterData['DIR_ID'] ||
               $beforeData['FILE_NAME']      != $afterData['FILE_NAME']){

                // 変更後のディレクトリを取得する
                $parentId = $afterDir['DIR_ID'];
                $afterParentDirArray = array();
                $breakFlg = false;

                while(true){
                    foreach($dirMasterArray as $dirMaster){
                        if($dirMaster['DIR_ID'] == $parentId){
                            if($dirMaster['DIR_ID'] == "1"){
                                $breakFlg = true;
                                break;
                            }
                            else{
                                $parentId = $dirMaster['PARENT_DIR_ID'];
                                $afterParentDirArray[] = array($dirMaster['DIR_NAME'], $dirMaster['CHMOD'], $dirMaster['GROUP_AUTH'], $dirMaster['USER_AUTH']);
                                break;
                            }
                        }
                    }
                    if(true === $breakFlg){
                        break;
                    }
                }

                // 変更後の親ディレクトリを作成する
                $afterParentDirArray = array_reverse($afterParentDirArray);
                $parentDir = $cloneRepoDir;
                foreach($afterParentDirArray as $afterParentDirInfo){

                    $parentDir = $parentDir . "/" . $afterParentDirInfo[0];

                    // ディレクトリが存在する場合、何もしない
                    if(is_dir($parentDir)){
                        continue;
                    }

                    // ディレクトリ作成
                    $output = NULL;
                    $cmd = "sudo mkdir -m -- " . $afterParentDirInfo[1] . " '" . $parentDir . "' 2>&1";
                    exec($cmd, $output, $return_var);

                    // システムエラーの場合
                    if(0 != $return_var){
                        throw new Exception(print_r($output, true));
                    }

                    // グループと所有者を確認する
                    $result = chkOwn($afterParentDirInfo[2], $afterParentDirInfo[3], $groupAuth, $userAuth);

                    // システムエラーの場合
                    if(true != $result){
                        throw new Exception($result);
                    }

                    // グループと所有者を変更する
                    $output = NULL;
                    $cmd = "sudo chown -- " . $groupAuth . ":" . $userAuth . " '" . $parentDir . "' 2>&1";
                    exec($cmd, $output, $return_var);

                    if(0 != $return_var){
                        throw new Exception(print_r($output, true));
                    }
                }

                // Git上の変更を行う
                $result = $controlGit->mvGit($beforeFilePath, $afterFilePath, $errMsg);

                if(0 != $result){
                    throw new Exception($errMsg);
                }
            }

            // 権限が変更されていた場合
            if($beforeData['CHMOD'] != $afterData['CHMOD']){

                // 権限を変更する
                $output = NULL;
                $cmd = "sudo chmod -- " . $afterData['CHMOD'] . " '" . $cloneRepoDir . $afterFilePath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    throw new Exception(print_r($output, true));
                }
            }

            // グループ、ユーザのいずれかが変更されていた場合
            if($beforeData['GROUP_AUTH']    != $afterData['GROUP_AUTH'] ||
               $beforeData['USER_AUTH']     != $afterData['USER_AUTH']){

                // グループと所有者を確認する
                $result = chkOwn($afterData['GROUP_AUTH'], $afterData['USER_AUTH'], $groupAuth, $userAuth);

                // システムエラーの場合
                if(true != $result){
                    throw new Exception($result);
                }

                // グループと所有者を変更する
                $output = NULL;
                $cmd = "sudo chown -- " . $groupAuth . ":" . $userAuth . " '" . $cloneRepoDir . $afterFilePath . "' 2>&1";
                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    throw new Exception(print_r($output, true));
                }
            }

            // Gitステータスを取得する
            $result = $controlGit->getStatus($output, $errMsg);

            if(0 != $result){
                throw new Exception($errMsg);
            }

            // chmodやchownによって変更をした場合、Git上で変更されたりされなかったりするので
            // ステータスを確認して変更されていればcommitを行う
            $commitFlg = false;
            foreach($output as $data){
                if(false !== strpos($data, "modified:")){
                    $commitFlg = true;
                    break;
                }
            }

            if(true === $commitFlg){
                // Gitコミットする
                $result = $controlGit->commitGit($afterFilePath, $errMsg);

                if(0 != $result){
                    throw new Exception($errMsg);
                }
            }
        }
    }
    catch (Exception $e){
        $intErrorType = 500;
        $strTmpStrBody = 'ERROR([FILE]' .  __FILE__  . ',[LINE]' . $e->getLine() . ')' . ' ' . $e->getMessage();
        web_log($strTmpStrBody);
    }

    return array( null, $intErrorType, array($retStrLastErrMsg) );
}

/**
 * 資材マスタの廃止に基づきGitから削除する
 * 
 */
function deleteGitFile($intBaseMode, $strNumberForRI, $reqDeleteData, $strTCASRKey, $ordMode, $aryVariant, $arySetting){
    global $g;

    $intErrorType = null;
    $retStrLastErrMsg = null;

    try{
        // 復活の場合は何もしない
        if(5 == $intBaseMode){
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        //////////////////////////
        // インターフェース情報テーブルを検索
        //////////////////////////
        $materialIfInfoTable = new MaterialIfInfoTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $materialIfInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

        // SQL実行
        $result = $materialIfInfoTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $materialIfInfoArray = $result;

        // インターフェース情報が1件ではない場合はエラー
        if(1 != count($materialIfInfoArray)) {
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5002');
            throw new Exception($msg);
        }

        // リモートリポジトリURLとクローンリポジトリが設定されていない場合は何もしない
        if("" == $materialIfInfoArray[0]['REMORT_REPO_URL'] || "" == $materialIfInfoArray[0]['CLONE_REPO_DIR']) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        // クローンリポジトリにGitが設定されていない場合は何もしない
        if(!file_exists($materialIfInfoArray[0]['CLONE_REPO_DIR'] . "/.git")) {
            return array( null, $intErrorType, array($retStrLastErrMsg) );   
        }

        $remortRepoUrl = $materialIfInfoArray[0]['REMORT_REPO_URL'];
        $branch = $materialIfInfoArray[0]['BRANCH'];
        $cloneRepoDir = $materialIfInfoArray[0]['CLONE_REPO_DIR'];
        $password = ky_decrypt($materialIfInfoArray[0]['PASSWORD']);
        $controlGit = new ControlGit($remortRepoUrl, $branch, $cloneRepoDir, $password, "{$g['root_dir_path']}/libs/backyardlibs/material/");

        //////////////////////////
        // ファイル管理テーブル（初期登録用）を検索
        //////////////////////////
        $fileManagementInitialTable = new FileManagementInitialTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $fileManagementInitialTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileManagementInitialTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $fileManagementArray = $result;

        $disuseFileManagementArray = array();
        // 資材を確認する
        foreach($fileManagementArray as $fileManagementData){
            // 廃止対象の資材と一致した場合は廃止対象
            if($fileManagementData['FILE_ID'] == $aryVariant['edit_target_row']['FILE_ID']){

                $disuseData = $fileManagementData;
                if("" != $disuseData['NOTE']){
                    $disuseData['NOTE'] .= "\n\n";
                }
                $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50010');
                $disuseData['DISUSE_FLAG']      = "1";      // 廃止
                $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                //////////////////////////
                // ファイル管理テーブル（初期登録用）を更新
                //////////////////////////
                $result = $fileManagementInitialTable->updateTable($disuseData);
                if(true !== $result){
                    $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // ファイル管理テーブルを検索
        //////////////////////////
        $fileManagementTable = new FileManagementTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $fileManagementTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $fileManagementTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $fileManagementArray = $result;

        $disuseFileManagementArray = array();
        // 資材を確認する
        foreach($fileManagementArray as $fileManagementData){
            // 廃止対象の資材と一致した場合は廃止対象
            if($fileManagementData['FILE_ID'] == $aryVariant['edit_target_row']['FILE_ID']){

                $disuseData = $fileManagementData;
                if("" != $disuseData['NOTE']){
                    $disuseData['NOTE'] .= "\n\n";
                }
                $disuseData['NOTE'] .= date("Y/m/d H:i:s") . "\n" . $g['objMTS']->getSomeMessage('ITAMATERIAL-STD-50010');
                $disuseData['DISUSE_FLAG']      = "1";      // 廃止
                $disuseData['LAST_UPDATE_USER'] = -101504;  // 最終更新者

                //////////////////////////
                // ファイル管理テーブルを更新
                //////////////////////////
                $result = $fileManagementTable->updateTable($disuseData);
                if(true !== $result){
                    $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
                    throw new Exception($msg);
                }
            }
        }

        //////////////////////////
        // ディレクトリマスタを検索
        //////////////////////////
        $dirMasterTable = new DirMasterTable($g['objDBCA'], $g['db_model_ch']);
        $sql = $dirMasterTable->createSselect("WHERE DISUSE_FLAG='0'");

        // SQL実行
        $result = $dirMasterTable->selectTable($sql);
        if(!is_array($result)){
            $msg = $g['objMTS']->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            throw new Exception($msg);
        }
        $dirMasterArray = $result;

        $dirNameFullpath = null;
        // ディレクトリを取得する
        foreach($dirMasterArray as $dirMasterData){

            if($dirMasterData['DIR_ID'] == $reqDeleteData['DIR_ID']){
                $dirNameFullpath = $dirMasterData['DIR_NAME_FULLPATH'];
                break;
            }
        }

        // ディレクトリが取得できなかった場合は何もしない
        if(null === $dirNameFullpath){
            return array( null, $intErrorType, array($retStrLastErrMsg) );
        }

        // ファイルが存在する場合
        if(is_file($cloneRepoDir . $dirNameFullpath . $reqDeleteData['FILE_NAME'])){

            $result = $controlGit->removeGitDir($dirNameFullpath . $reqDeleteData['FILE_NAME'], $errMsg);

            if(0 != $result){
                throw new Exception($errMsg);
            }
        }
    }
    catch (Exception $e){
        $intErrorType = 500;
        $strTmpStrBody = 'ERROR([FILE]' .  __FILE__  . ',[LINE]' . $e->getLine() . ')' . ' ' . $e->getMessage();
        web_log($strTmpStrBody);
    }

    return array( null, $intErrorType, array($retStrLastErrMsg) );   
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
    $cmd = "sudo cat /etc/group 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        return print_r($output, true);
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
    $cmd = "sudo cat /etc/passwd 2>&1";
    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        return print_r($output, true);
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

/**
 * 子ディレクトリ更新
 * 
 */
function updateChildDir($dirMasterTable, $dirId, $dirNameFullpath, $lastUpdateUser, $dirMasterArray){

    global $g;

    foreach($dirMasterArray as $dirMaster){
        if($dirMaster['PARENT_DIR_ID'] == $dirId){
            $updateData = $dirMaster;
            $updateData['DIR_NAME_FULLPATH']    = $dirNameFullpath . $updateData['DIR_NAME'] . "/";
            $updateData['LAST_UPDATE_USER']     = $lastUpdateUser;

            //////////////////////////
            // ディレクトリマスタを更新
            //////////////////////////
            $result = $dirMasterTable->updateTable($updateData);
            if(true !== $result){
                return $objMTS->getSomeMessage('ITAMATERIAL-ERR-5001', $result);
            }

            // 子ディレクトリのパスを変更する
            $result = updateChildDir($dirMasterTable, $updateData['DIR_ID'], $updateData['DIR_NAME_FULLPATH'], $lastUpdateUser, $dirMasterArray);
            if(true !== $result){
                return $result;
            }
        }
    }
    return true;
}
