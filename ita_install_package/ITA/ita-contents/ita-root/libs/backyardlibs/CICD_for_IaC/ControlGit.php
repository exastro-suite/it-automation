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
 *    Git操作クラス
 */

/**
 * Git操作クラス
 * 
 */
class LocalFilesControl {
    // Git同期 shellコマンドパス
    const  C_LocalShellDir = "%s/backyards/CICD_for_IaC";
    // Git同期 ローカルクローンディレクトリ
    const  C_LocalCloneDir = "%s/repositorys/%010s";
    // Git同期 子プロセス 処理情報格納ディレクトリ
    const  C_ChildProcessControlDir = "/repositorys/ControleFiles";
    // Git同期 子プロセス 起動パラメータ  LINE_リポジトリ管理項番_起動時間(T_%Y%m%d%H%i%s)
    const  C_ChildProcessExecParam  = "LINE_%010s";
    // Git CloneしたリモートリポジトリURL・ブランチ退避ファイル
    const  C_GitCloneInfoFile     = "%010s" . "_GitCloneInfo.txt";
    private $root_dir_path;

    public function __construct() {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    function getChildProcessExecParam($RepoId) {
        $param = sprintf(self::C_ChildProcessExecParam,$RepoId);
        return $param;
    }

    function getGitCloneInfoFilePath($RepoId) {
        $file = sprintf(self::C_GitCloneInfoFile,$RepoId);
        $file = $this->root_dir_path . self::C_ChildProcessControlDir . "/" . $file;
        return $file;
    }

    function getGitCloneInfo($RepoId,&$CloneRepoURL,&$CloneBranch) {
        $CloneRepoURL = "";
        $CloneBranch  = "";
        $GitCloneInfoFilePath = $this->getGitCloneInfoFilePath($RepoId);
        if(file_exists($GitCloneInfoFilePath)) {
            $json = file_get_contents($GitCloneInfoFilePath);
            if($json === false) {
                return false;
            } else {
                $LocalCloneInfo=json_decode($json,true);
                $CloneRepoURL = $LocalCloneInfo["REMORT_REPO_URL"];
                $CloneBranch  = $LocalCloneInfo["BRANCH_NAME"];
                return true;
            }
        } else {
            // ファイルがまだ出来ていない場合
            $CloneRepoURL = "";
            $CloneBranch  = "";
            return true;
        }
        return false;
    }
    function putGitCloneInfo($RepoId,$CloneRepoURL,$CloneBranch) {
        $GitCloneInfoFilePath = $this->getGitCloneInfoFilePath($RepoId);
        $LocalCloneInfo = array();
        $LocalCloneInfo["REMORT_REPO_URL"] = $CloneRepoURL;
        $LocalCloneInfo["BRANCH_NAME"]     = $CloneBranch;
        $ret = file_put_contents($GitCloneInfoFilePath,json_encode($LocalCloneInfo));
        if($ret === false) {
            return false;
        }
        return true;
    }

    function getLocalCloneDir($RepoId) {
        $dir = sprintf(self::C_LocalCloneDir,$this->root_dir_path,$RepoId);
        return $dir;
    }    

    function getLocalShellDir() {
        $dir = sprintf(self::C_LocalShellDir,$this->root_dir_path);
        return $dir;
    }    
}

class ControlGit {

    private $RepoId;            // Gitのユーザー
    private $remortRepoUrl;     // GitのリモートリポジトリURL
    private $branch;            // Gitのブランチ
    private $cloneRepoDir;      // Gitクローンディレクトリ
    private $user;              // Gitのユーザー
    private $password;          // Gitのパスワード
    private $gitOption;         // Gitのオプション（--git-dir、--work-tree）
    private $tmpDir;            // 作業用ディレクトリ
    private $ClonecloneDir;     // クローンリポジトリのクローンディレクトリ
    private $libPath;           // ライブラリのパス
    private $LastErrorMsg;      // エラーメッセージ
    private $GitCommandLastErrorMsg;  // Gitコマンド　エラー出力メッセージ
    private $retryCount;
    private $retryWaitTime;
    private $objMTS;

    /**
     * コンストラクタ
     */
    public function __construct($RepoId, $remortRepoUrl, $branch, $cloneRepoDir, $user, $password, $libPath, $objMTS, $retryCount, $retryWaitTime) {
        $this->RepoId = $RepoId;
        $this->remortRepoUrl = $remortRepoUrl;
        $this->cloneRepoDir = $cloneRepoDir;
        $this->user = $user;
        $this->password = ky_decrypt($password);
        $this->gitOption = "--git-dir " . $this->cloneRepoDir . "/.git --work-tree=" . $this->cloneRepoDir;
        $this->tmpDir = "";
        $this->ClonecloneDir = dirname($cloneRepoDir) . "/clonecloneRepo/";
        $this->gitOption2 = "--git-dir " . $this->ClonecloneDir . "/.git --work-tree=" . $this->ClonecloneDir;
        $this->libPath = $libPath;
        $this->objMTS  = $objMTS;
        $this->ClearGitCommandLastErrorMsg();
        $this->ClearLastErrorMsg();
        $this->branch = $branch;
        if($this->branch == ""){
            $this->branch  = "__undefine_branch__";
        }
        $this->retryCount = $retryCount;
        if($this->retryCount == "") {
           $this->retryCount = 3;
        }
        $this->retryWaitTime = $retryWaitTime;
        if($this->retryWaitTime == "") {
           $this->retryWaitTime = 1000;  //i単位:ms
        }
    }

    function ClearGitCommandLastErrorMsg() {
        $this->GitCommandLastErrorMsg = "";
    }

    function SetGitCommandLastErrorMsg($errorDetail) {
        $this->GitCommandLastErrorMsg = $errorDetail;
    }

    function GetGitCommandLastErrorMsg() {
        $LastMsg = $this->GitCommandLastErrorMsg;
        $this->ClearGitCommandLastErrorMsg();
        return $LastMsg;
    }
  
    function ClearLastErrorMsg() {
        $this->LastErrorMsg = "";
    }

    function SetLastErrorMsg($errorDetail) {
        $this->LastErrorMsg = $errorDetail;
    }

    function GetLastErrorMsg() {
        $LastMsg = $this->LastErrorMsg;
        $this->ClearLastErrorMsg();
        return($LastMsg);
    }

    function LocalCloneDirClean() {
        $output = NULL;
        if(file_exists($this->cloneRepoDir)) {

            $param = escapeshellarg($this->cloneRepoDir);
            $cmd = "sudo /bin/rm -rf " . $param . " 2>&1";
            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                $logstr    = sprintf("Failed to delete clone directory.(%s) \n",$this->cloneRepoDir);
                $logaddstr = implode("\n",$output);
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
                $this->SetLastErrorMsg($FREE_LOG);
                $this->SetGitCommandLastErrorMsg($logstr . "\n" . implode("\n",$output));
                return false;
            }
        }

        $param = escapeshellarg($this->cloneRepoDir);
        $cmd = "sudo /bin/mkdir -m 0777 " . $param . " 2>&1";
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr    = sprintf("Failed to create clone directory.(%s) \n",$this->cloneRepoDir);
            $logaddstr = implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetLastErrorMsg($FREE_LOG);
            $this->SetGitCommandLastErrorMsg($logstr . "\n" . implode("\n",$output));
            return false;
        }
        return true;
    }

    /**
     * クローン
     */
    public function GitClone($Authtype) {

        global $g;

        $output = NULL;
        $return_var = 0;
        
        $comd_ok = false;
        
        // Git Cloneコマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;usleep($this->retryWaitTime),$idx++) {
            $shell = sprintf("%s/ky_GitClone.sh",$this->libPath);
            $cmd = sprintf("sudo -i  %s %s %s %s %s %s %s 2>&1", escapeshellarg($shell),
                                                      escapeshellarg($Authtype),
                                                      escapeshellarg($this->remortRepoUrl),
                                                      escapeshellarg($this->cloneRepoDir),
                                                      escapeshellarg($this->branch),
                                                      escapeshellarg($this->user),
                                                      escapeshellarg($this->password));
            $output = NULL;
            $this->ClearGitCommandLastErrorMsg();
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                // リトライ中のログは表示しない。
            } else {
                $comd_ok = true;
                break;
            }
        }
        if($comd_ok === false) {
            // Git clone commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1021"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }

        // 日本語文字化け対応
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " config --local core.quotepath false  2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            // Git config の設定に失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1020"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }

    /**
     * remote
     */
    public function GitRemoteChk() {

        $output = NULL;
        $return_var = 0;
        $cmd_ok = false;

        $cmd = sprintf("sudo git %s remote -v 2>&1",$this->gitOption);

        exec($cmd, $output, $return_var);
        if($return_var == 0) {
            $stdout = $output[0];
            $ret = preg_match("/^origin(\s)/", $stdout);
            if($ret == 1) {
                $ret = strstr($stdout,$this->remortRepoUrl);
                if($ret !== false) {
                    $cmd_ok = true;
                }
            }
        } else {
            //Git remote commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1023"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        } 
        if($cmd_ok === false) {
            // ローカルクローンのリモートリポジトリが不正です。(リモートリポジトリURL:{})
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1022",array($this->remortRepoUrl)); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(mplode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }

    /**
     * checkout
     */
    public function GitCheckoutChk() {

        $output = NULL;
        $return_var = 0;
        $cmd_ok = false;

        if($this->branch  == "__undefine_branch__") {
            $branch = "";
        } else {
            $branch = escapeshellarg($this->branch);
        }
        $cmd = sprintf("sudo git %s checkout %s 2>&1",$this->gitOption,$branch);
        exec($cmd, $output, $return_var);
        if($return_var != 0) {
            //Git checkout commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        } 
        return true;
    }

    /**
     * ファイル一覧
     */
    public function GitLsFiles(&$Files) {

        $output = NULL;
        $return_var = 0;

        $shell = sprintf("%s/ky_GitCommand.sh",$this->libPath);
        $cmd = sprintf("sudo -i %s %s ls-files %s 2>&1",escapeshellarg($shell),
                                               escapeshellarg($this->cloneRepoDir),
                                               escapeshellarg($this->branch));

        exec($cmd, $output, $return_var);
        if($return_var == 0) {
            // git checkout command stdout delete
            unset($output[0]);
            $Files = $output;
        } else {
            //Git ls-files commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1026"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        } 
        return true;
    }

    /**
     * git pull
     */
    public function GitPull(&$pullResultAry,$Authtype) {
        global $g;

        $output = NULL;
        $return_var = 0;

        $comd_ok = false;

        // Git Cloneコマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;usleep($this->retryWaitTime),$idx++) {
            $output = NULL;
            $return_var = 0;
            $shell = sprintf("%s/ky_GitPull.sh",$this->libPath);
            $cmd = sprintf("sudo -i %s %s %s %s %s %s 2>&1", escapeshellarg($shell),
                                                            escapeshellarg($Authtype),
                                                            escapeshellarg($this->cloneRepoDir),
                                                            escapeshellarg($this->branch),
                                                            escapeshellarg($this->user),
                                                            escapeshellarg($this->password));

            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                // リトライ中のログは表示しない。
            } else {
                // git checkout command stdout delete
                unset($output[0]);
                unset($output[1]);
                if($Authtype == "pass") {
                   unset($output[2]);
                }
                // 結果解析用にindexを0オリジンにする。
                $pullResultAry = array();
                foreach($output as $line) 
                { $pullResultAry[] = $line; }
                $comd_ok = true;
                break;
            }
        }
        if($comd_ok === false) {
            //Git pull commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1030"); 
            $logaddstr = $cmd . "\n";
            $logaddstr .= implode("\n",$output);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }

    /**
     * git pull結果判定
     *
     *変更なしの場合
     *  $ git pull
     *  Already up-to-date.
     *変更あの場合
     *  remote: Enumerating objects: 16, done.
     *  remote: Counting objects: 100% (16/16), done.
     *  remote: Compressing objects: 100% (8/8), done.
     *  remote: Total 12 (delta 2), reused 0 (delta 0), pack-reused 0
     *  Unpacking objects: 100% (12/12), done.
     *  From https://github.com/enomantest/newrepo
     *     a40645c..067a362  master     -> origin/master
     *  Updating a40645c..067a362
     *  Fast-forward
     *  adddir/add1          |  1 -
     *  hoge1/roles/main.yml | 60 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++--
     *  hoge1/roles/test.yml | 17 +++++++++++++++++
     *  3 files changed, 75 insertions(+), 3 deletions(-)
     *  delete mode 100644 adddir/add1
     *  create mode 100644 hoge1/roles/test.yml
     * 1ファイルの変更
     * From https://github.com/enomantest/newrepo
     *    710b91d..288610f  master     -> origin/master
     * Updating 710b91d..288610f
     * Fast-forward
     * roles/vvv | 3 +--
     * 1 file changed, 1 insertion(+), 2 deletions(-)

     * 
    */
    function GitPullResultAnalysis($pullResultAry,&$UpdateFiles,&$UpdateFlg) {
        $ErrMsg = "";
        $ret = $this->GitPullResultFirstAnalysis($pullResultAry,$UpdateFiles,$ErrMsg,$UpdateFlg);
        if($ret != true) {
            //Git pull commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1031"); 
            $logaddstr = implode("\n",$pullResultAry);
            $logaddstr .= "\n---------------------------------------------------------------------------\n";
            $logaddstr .= $ErrMsg;
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg($logaddstr);
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }
    function GitPullResultFirstAnalysis($pullResultAry,&$UpdateFiles,&$ErrMsg,&$UpdateFlg) {
        $UpdateFiles = array();
        $UpdateFiles['Delete']  = array();
        $UpdateFiles['Insert']  = array();
        $UpdateFiles['Update']  = array();
        for($idx=0;$idx < count($pullResultAry);$idx++) {
            // 変更なしの判定
            if($idx == 0) {
                // Git pullの差分抽出が不完全なので、変更の有無だけを判定 -----
                $ret = preg_match("/^Already up-to-date./", $pullResultAry[$idx]);
                if($ret == 1) {
                    $UpdateFlg = false;
                } else {
                    $UpdateFlg = true;
                }
                return true;
                //if($ret == 1){
                //    return $UpdateFiles;
                //}
                // ----- Git pullの差分抽出が不完全なので、変更の有無だけを判定
            }
            $ret = preg_match("/Fast-forward/", $pullResultAry[$idx]);
            if($ret === 1){
                $ret = $this->GitPullResultMiddleAnalysis($pullResultAry,$idx,$UpdateFiles,$ErrMsg);
                return $ret;
            }
        }
        $ErrMsg =  sprintf("[FILE]%s[LINE]%s:Unexpected format. No start of change file list.",basename(__FILE__),__LINE__);
        return false;
    }
    function GitPullResultMiddleAnalysis($pullResultAry,$start_idx,&$UpdateFiles,&$ErrMsg) {
        $start_idx++;
        for($idx=$start_idx;$idx < count($pullResultAry);$idx++) {
            // ファイルリストの終端か判定
            // 3 files changed, 75 insertions(+), 3 deletions(-)
            $ret = preg_match("/^(\s)+[0-9]+(\s)+(files|file)/", $pullResultAry[$idx]);
            if($ret == 1) {
                $ret = $this->GitPullResultLastAnalysis($pullResultAry,$idx,$UpdateFiles,$ErrMsg);
                return $ret;
            } else {
                // 変更ファイル名取得
                // adddir/add1          |  1 -
                $ret = preg_split("/(\s)+\|(\s)+[0-9]+(\s)+/",$pullResultAry[$idx]);
                if($ret === false) {
                    $ErrMsg = sprintf("[FILE]%s[LINE]%s:Unexpected format of change file list.(line:%s [%s])",basename(__FILE__),__LINE__,$idx,$pullResultAry[$idx]);
                    return false;
                }
                $file = trim($ret[0]);
                $UpdateFiles['Update'][$file] = $file;
            }
        }
        $ErrMsg = sprintf("[FILE]%s[LINE]%s:Unexpected format. No end of change file list.",basename(__FILE__),__LINE__);
        return false;
    }    
    function GitPullResultLastAnalysis($pullResultAry,$start_idx,&$UpdateFiles,&$ErrMsg) {
        $start_idx++;
        for($idx=$start_idx;$idx < count($pullResultAry);$idx++) {
            if(trim($pullResultAry[$idx]) == "") {
                continue;
            }
            // delete mode 100644 adddir/add1
            $ret = preg_split("/delete(\s)+mode(\s)+[0-9]+(\s)+/",$pullResultAry[$idx]);
            // preg_split bug 配列で返却されない
            $error = false;
            if($ret === false) {
               $error = true;
            } else if(!is_array($ret)) {
               $error = true;
            } else if(count($ret) != 2) {
               $error = true;
            }
            if($error === true) {
                // create mode 100644 hoge1/roles/test.yml
                $ret = preg_split("/create(\s)+mode(\s)+[0-9]+(\s)+/",$pullResultAry[$idx]);
                // preg_split bug 配列で返却されない
                $error = false;
                if($ret === false) {
                    $error = true;
                } else if(!is_array($ret)) {
                    $error = true;
                } else if(count($ret) != 2) {
                    $error = true;
                }
                if($error === true) {
                    $ErrMsg = sprintf("[FILE]%s[LINE]%s:Unexpected format.(line:%s [%s])",basename(__FILE__),__LINE__,$idx,$pullResultAry[$idx]);
                    return false;
                }
                $file = trim($ret[1]);
                $UpdateFiles['Insert'][$file] = $file;
                if(@count($UpdateFiles['Update'][$file]) == 0) {
                    $ErrMsg = sprintf("[FILE]%s[LINE]%s:Unexpected format of change file list(create). (line:%s [%s])",basename(__FILE__),__LINE__,$idx,$pullResultAry[$idx]);
                    return false;
                }
                unset($UpdateFiles['Update'][$file]);
            } else {
                $file = trim($ret[1]);
                $UpdateFiles['Delete'][$file] = $file;
                if(@count($UpdateFiles['Update'][$file]) == 0) {
                    $ErrMsg = sprintf("[FILE]%s[LINE]%s:Unexpected format of change file list(delete). (line:%s [%s])",basename(__FILE__),__LINE__,$idx,$pullResultAry[$idx]);
                    return false;
                }
                unset($UpdateFiles['Update'][$file]);
            }
        }
        return true;
    }
}
