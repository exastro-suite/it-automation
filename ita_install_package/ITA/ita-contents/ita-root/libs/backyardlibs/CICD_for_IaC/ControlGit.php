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

class ControlGit {

    private $RepoId;            // Gitのユーザー
    private $remortRepoUrl;     // GitのリモートリポジトリURL
    private $branch;            // Gitのブランチ
    private $cloneRepoDir;      // Gitクローンディレクトリ
    private $user;              // Gitのユーザー
    private $password;          // Gitのパスワード
    private $sshPassword;       // Gitのsshパスワード
    private $sshPassphrase;     // Gitのssh鍵認証パスフレーズ
    private $gitOption;         // Gitのオプション（--git-dir、--work-tree）
    private $tmpDir;            // 作業用ディレクトリ
    private $ClonecloneDir;     // クローンリポジトリのクローンディレクトリ
    private $libPath;           // ライブラリのパス
    private $LastErrorMsg;      // エラーメッセージ
    private $GitCommandLastErrorMsg;  // Gitコマンド　エラー出力メッセージ
    private $retryCount;
    private $retryWaitTime;
    private $objMTS;
    private $ProxyURL;
    private $ProxyAddress;
    private $ProxyPort;
    private $GitCmdRsltParsAry;
    private $sshExtraArgs;
    private $sshExtraArgsStr;

    /**
     * コンストラクタ
     */
    public function __construct($RepoId, $remortRepoUrl, $branch, $cloneRepoDir, $user, $password, $sshPassword, $sshPassphrase, $sshExtraArgs, $libPath, $objMTS, $retryCount, $retryWaitTime, $ProxyAddress, $ProxyPort, $GitCmdRsltParsAry) {
        $this->RepoId = $RepoId;
        $this->remortRepoUrl = $remortRepoUrl;
        $this->cloneRepoDir = $cloneRepoDir;
        $this->user = $user;
        if($this->user == ""){
            $this->user  = "__undefine_user__";
        }
        $this->password = ky_decrypt($password);
        if($this->password == ""){
            $this->password = "__undefine_password__";
        }
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
        $this->retryCount = $this->retryCount + 1;
        $this->retryWaitTime = $retryWaitTime;
        if($this->retryWaitTime == "") {
           $this->retryWaitTime = 1000;  //単位:ms
        }
        $this->retryWaitTime = $this->retryWaitTime * 1000; // us
        $this->ProxyAddress = $ProxyAddress;
        $this->ProxyPort    = $ProxyPort;
        if(strlen(trim($ProxyAddress)) != 0) {
            if (strlen(trim($ProxyPort)) != 0) {
                $Address  = sprintf("%s:%s",$ProxyAddress,$ProxyPort);
            } else {
                $Address  = $ProxyAddress;
            }
            $this->ProxyURL = $Address;
        } else {
            $this->ProxyURL = "__undefine__";
        }
        $this->GitCmdRsltParsAry = $GitCmdRsltParsAry;
        $this->sshPassword       = ky_decrypt($sshPassword);
        if($this->sshPassword == ""){
            $this->sshPassword = "__undefine_sshPassword__";
        }
        $this->sshPassphrase     = ky_decrypt($sshPassphrase);   
        if($this->sshPassphrase == ""){
            $this->sshPassphrase = "__undefine_sshPassphrase__";
        }
        $this->sshExtraArgs = $sshExtraArgs;
        $this->sshExtraArgsStr = "";
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

    function LocalCloneDirCheck() {
        // ローカルクローンディレクトリ有無判定
        return (file_exists($this->cloneRepoDir));
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
        for($idx =0;$idx < $this->retryCount;$idx++) {
            $shell = sprintf("%s/ky_GitClone.sh",$this->libPath);
            $cmd = sprintf("sudo -i  %s %s %s %s %s %s %s %s %s %s %s 2>&1", escapeshellarg($shell),
                                                      escapeshellarg($this->ProxyURL),
                                                      escapeshellarg($Authtype),
                                                      escapeshellarg($this->remortRepoUrl),
                                                      escapeshellarg($this->cloneRepoDir),
                                                      escapeshellarg($this->branch),
                                                      escapeshellarg($this->user),
                                                      escapeshellarg($this->password),
                                                      escapeshellarg($this->sshPassword),
                                                      escapeshellarg($this->sshPassphrase),
                                                      escapeshellarg($this->sshExtraArgsStr));
            $output = NULL;
            $this->ClearGitCommandLastErrorMsg();
            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                global $log_level;
                if($log_level == 'DEBUG') {
                    $logaddstr = "";
                    $logaddstr = implode("\n",$output);
                    $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                    error_log(__FILE__.__LINE__.$logaddstr);
                }
                // リトライ中のログは表示しない。
                if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
            } else {
                $comd_ok = true;
                break;
            }
        }
        if($comd_ok === false) {
            // clone失敗時はローカルディレクトリを削除
            $param = escapeshellarg($this->cloneRepoDir);
            $cmd = "sudo /bin/rm -rf " . $param . " 2>&1";
            exec($cmd, $outdel, $return_del);

            // Git clone commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1021"); 
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
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
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }

    /**
     * リモートリポジトリ確認
     */
    public function GitRemoteChk() {

        $cmd_ok = false;

        $cmd = sprintf("sudo git %s remote -v 2>&1",$this->gitOption);

        // Git コマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;$idx++) {
            $output = NULL;
            $return_var = 0;
            exec($cmd, $output, $return_var);
            if($return_var == 0) {
                break;
            }
            global $log_level;
            if($log_level == 'DEBUG') {
                $logaddstr = "";
                $logaddstr = implode("\n",$output);
                $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                error_log(__FILE__.__LINE__.$logaddstr);
            }
            if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
        }
        if($return_var == 0) {
            $stdout = $output[0];
            $ret = preg_match("/^origin(\s)*/", $stdout);
            if($ret == 1) {
                $url = $this->remortRepoUrl;
                $url = preg_quote($url,'/');
                $ret = preg_match("/^origin(\s)*$url/", $stdout);
                if($ret == 1) {
                   $cmd_ok = true;
                } 
            }
        } else {
            //Git remote commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1023"); 
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return -1;
        } 
        if($cmd_ok === false) {
            // ローカルクローンのリモートリポジトリが不正です。(リモートリポジトリURL:{})
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1022",array($this->remortRepoUrl)); 
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output));
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return true;
    }

    /**
     * ブラント確認
     */
    public function GitBranchChk($Authtype) {

        $return_var = 0;

        $CurrentBranch = "";
        $DefaultBranch = "";
        if($this->branch  == "__undefine_branch__") {
            // デフォルトブランチ確認
            $shell = sprintf("%s/ky_GitCommand.sh",$this->libPath);
            $cmd1 = sprintf("sudo -i %s %s %s %s %s %s %s %s %s %s 2>&1", escapeshellarg($shell),
                                                          escapeshellarg($this->ProxyURL),
                                                          escapeshellarg($Authtype),
                                                          escapeshellarg($this->cloneRepoDir),
                                                          escapeshellarg('remote show origin'),
                                                          escapeshellarg($this->user),
                                                          escapeshellarg($this->password),
                                                          escapeshellarg($this->sshPassword),
                                                          escapeshellarg($this->sshPassphrase),
                                                          escapeshellarg($this->sshExtraArgsStr));

            // Git コマンドが失敗した場合、指定時間Waitし指定回数リトライする。
            for($idx =0;$idx < $this->retryCount;$idx++) {
                $output1 = NULL;
                exec($cmd1, $output1, $return_var);
                if($return_var == 0) {
                    break;
                }
                global $log_level;
                if($log_level == 'DEBUG') {
                    $logaddstr = "";
                    $logaddstr = implode("\n",$output1);
                    $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                    error_log(__FILE__.__LINE__.$logaddstr);
                }
                if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
            }
            if($return_var != 0) {
                //Git remote show origin commandに失敗しました。
                $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024"); 
                $logaddstr = implode("\n",$output1);
                $logaddstr .= "\nexit code:($return_var)";
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
                $this->SetGitCommandLastErrorMsg(implode("\n",$output1));
                $this->SetLastErrorMsg($FREE_LOG);
                return -1;
            } else {
                for($idx=0;$idx<count($output1);$idx++) {
                     $matchstr = "/^(\s)+HEAD(\s)branch/";
                     $ret = preg_match($matchstr, $output1[$idx]);
                     if($ret == 1) {
                         // HEAD branch:
                         $matchstr = "/^(\s)+HEAD(\s)branch:(\s)+/";
                         $ret = preg_match($matchstr, $output1[$idx]);
                         if($ret == 1) {
                             $retAry = preg_split($matchstr, $output1[$idx]);
                             $DefaultBranch = $retAry[1];
                             break;
                         } else {
                             // #1600の対応
                             $matchstr = "/^(\s)+HEAD(\s)branch(\s)\(remote HEAD is ambiguous, may be one of the following\):/";
                             $ret = preg_match($matchstr, $output1[$idx]);
                             if($ret == 1) {
                                 return true;
                             } else {
                                 $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024"); 
                                 $logaddstr = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1032"); 
                                 $logaddstr .= "\n";
                                 $logaddstr .= implode("\n",$output1);
                                 $logaddstr .= "\nexit code:($return_var)";
                                 $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
                                 $this->SetGitCommandLastErrorMsg($logaddstr);
                                 $this->SetLastErrorMsg($FREE_LOG);
                                 return -1;
                            }
                        }
                    }
                }
            }
        } 
        // カレントブランチ確認
        $cmd2 = sprintf("sudo -i git %s branch 2>&1",$this->gitOption);

        // Git コマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;$idx++) {
            $output2 = NULL;
            exec($cmd2, $output2, $return_var);
            if($return_var == 0){
                break;
            }
            global $log_level;
            if($log_level == 'DEBUG') {
                $logaddstr = "";
                $logaddstr = implode("\n",$output2);
                $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                error_log(__FILE__.__LINE__.$logaddstr);
            }
            if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
        }
        if($return_var != 0) {
            //Git remote show origin commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024"); 
            $logaddstr = implode("\n",$output2);
            $logaddstr .= "\nexit code:($return_var)";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg(implode("\n",$output2));
            $this->SetLastErrorMsg($FREE_LOG);
            return -1;
        } else {
            // 空リポジトリ対応 空リポジトリの場合はremote show originの結果が(unknown)になるので(unknown)設定
            if(count($output2) == 0) {
                $CurrentBranch = "(unknown)";
            } else {
                $CurrentBranch = substr($output2[0],2);
            }
        }
        if($this->branch  == "__undefine_branch__") {
            if($DefaultBranch == "") {
                return true;
            }
            if($DefaultBranch != $CurrentBranch) {
                return false;
            }
        } else {
            if($this->branch != $CurrentBranch) {
                return false;
            }
        }
        return true;
    }

    /**
     * ファイル一覧
     */
    public function GitLsFiles(&$Files) {

        $cmd = sprintf("sudo -i git %s ls-files 2>&1",$this->gitOption);

        // Git コマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;$idx++) {
            $output = NULL;
            $return_var = 0;
            exec($cmd, $output, $return_var);
            if($return_var == 0) {
                break;
            }
            global $log_level;
            if($log_level == 'DEBUG') {
                $logaddstr = "";
                $logaddstr = implode("\n",$output);
                $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                error_log(__FILE__.__LINE__.$logaddstr);
            }
            if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
        }
        if($return_var == 0) {
            $Files = $output;
        } else {
            //Git ls-files commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1026"); 
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
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
    public function GitPull(&$pullResultAry,$Authtype,&$UpdateFlg=true) {
        global $g;

        $output = NULL;
        $return_var = 0;

        $comd_ok = false;

        $ResultParsStr = $this->GitCmdRsltParsAry['pull']['allrady-up-to-date'];
        // Git Cloneコマンドが失敗した場合、指定時間Waitし指定回数リトライする。
        for($idx =0;$idx < $this->retryCount;$idx++) {
            $output = NULL;
            $return_var = 0;
            $shell = sprintf("%s/ky_GitCommand.sh",$this->libPath);
            $cmd = sprintf("sudo -i %s %s %s %s %s %s %s %s %s %s 2>&1", escapeshellarg($shell),
                                                          escapeshellarg($this->ProxyURL),
                                                          escapeshellarg($Authtype),
                                                          escapeshellarg($this->cloneRepoDir),
                                                          escapeshellarg('pull --rebase --ff'),
                                                          escapeshellarg($this->user),
                                                          escapeshellarg($this->password),
                                                          escapeshellarg($this->sshPassword),
                                                          escapeshellarg($this->sshPassphrase),
                                                          escapeshellarg($this->sshExtraArgsStr));

            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                global $log_level;
                if($log_level == 'DEBUG') {
                    $logaddstr = "";
                    $logaddstr = implode("\n",$output);
                    $logaddstr .= "\nexit code:($return_var)\nError retry with git command";
                    error_log(__FILE__.__LINE__.$logaddstr);
                }
                // リトライ中のログは表示しない。
                if(($this->retryCount -1) > $idx) { usleep($this->retryWaitTime); }
            } else {
                $saveoutput = $output;
                $format_ok = false;
                for($idx1=0;$idx1 < count($output);$idx1++) {
                    $ret = preg_match($ResultParsStr, $output[$idx1]);
                    if($ret == 1) {
                        $format_ok = true;
                        $UpdateFlg = false;
                        break;
                    }
                    $ret = preg_match("/^Fast-forward/", $output[$idx1]);
                    if($ret == 1) {
                        $format_ok = true;
                        $UpdateFlg = true;
                        break;
                    }
                }
                if($format_ok == true) {
                    // 結果解析用にindexを0オリジンにする。
                    $pullResultAry = array();
                    foreach($output as $line) { $pullResultAry[] = $line; }
                    $comd_ok = true;
                    break;
                } else {
                    $output = $saveoutput;
                }
            }
        }
        if($comd_ok === false) {
            //Git pull commandに失敗しました。
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1030"); 
            $logaddstr = implode("\n",$output);
            $logaddstr .= "\nexit code:($return_var)";
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
     *  From https://github.com/......
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
     * From https://github.com/.....
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
    function GetGitVersion(&$nowVar) {
        $nowVar = "";
        $cmd = "git --version 2>&1";
        exec($cmd,$output,$exit_code);
        if($exit_code != 0) {
            $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1034");
            $logaddstr = $cmd . "\n" . implode("\n",$output);
            $logaddstr .= "\nexit code:($exit_code)";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            $this->SetGitCommandLastErrorMsg($logstr . "\n" . $logaddstr);
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        $varAry = explode(" ",$output[0]);
        $varStr = explode(".",$varAry[2]);
        $nowVar = sprintf("%03s%03s",$varStr[0],$varStr[1]);
        return true;
    }
    function setSshExtraArgs() {
        // Gitバージョンはチェックしないで。
        // 環境変数「GIT_SSH_COMMAND」と git config globalにssh接続オプションを設定する
        $ret = $this->setSshExtraArgsGit2_3High();
        $ret = $this->setSshExtraArgsVar2_3Low();
        return $ret;
    }
    function setSshExtraArgsGit2_3High() {
        $this->sshExtraArgsStr = sprintf("ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no %s",$this->sshExtraArgs);
        return true;
    }
    function setSshExtraArgsVar2_3Low() {
        // ssh 設定
        $output = NULL;
        $reg_flg = false;

        if(file_exists("/root/.gitconfig")) {
            $cmd = "sudo git config --global -l 2>&1";
            exec($cmd, $output, $return_var);
            if(0 == $return_var){
                foreach($output as $stdline) {
                    $ret = preg_match("/core.sshcommand/", $stdline);
                    if($ret == 1){
                        $reg_flg = true;
                    }
                }
            } else {
                $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1036",array($cmd)); 
                $logaddstr = $cmd . "\n" . implode("\n",$output);
                $logaddstr .= "\nexit code:($return_var)";
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
                $this->SetGitCommandLastErrorMsg($logstr . "\n" . $logaddstr);
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
        }

        if($reg_flg === false) {
            $output = NULL;
            $cmd = "sudo git config --global core.sshCommand 'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no' 2>&1";
            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                $logstr    = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1036",array($cmd)); 
                $logaddstr = $cmd . "\n" . implode("\n",$output);
                $logaddstr .= "\nexit code:($return_var)";
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
                $this->SetGitCommandLastErrorMsg($logstr . "\n" . $logaddstr);
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
        }
        return true;
    }
}
?>
