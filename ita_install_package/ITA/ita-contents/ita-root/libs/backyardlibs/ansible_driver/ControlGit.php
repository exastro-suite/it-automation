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

    private $GitRepoBaseDir;       // Gitリポジトリベースディレクトリ
    private $GitRepoDir;        // Gitリポジトリ
    private $HostName;          // Gitリポジトリ
    private $gitOption;         // Gitのオプション（--git-dir、--work-tree）
    private $LastErrorMsg;      // エラーメッセージ

    /**
     * コンストラクタ
     */
    public function __construct($EcecuteNo, $DriverName, $HostName) {
        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        $this->GitRepoBaseDir = sprintf("%s/repositorys/ansible_driver",$root_dir_path);
        $this->GitRepoDir     = sprintf("%s/%s_%010s",$this->GitRepoBaseDir, $DriverName, $EcecuteNo);
        $this->HostName       = $HostName;
        $this->gitOption      = "--git-dir " . $this->GitRepoDir . "/.git --work-tree=" . $this->GitRepoDir;
        $this->ClearLastErrorMsg();
    }

    function getGitRepoDir() {
        return $this->GitRepoDir;
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

    function GitRepoDirCheck() {
        // ローカルリポジトリ有無判定
        return (file_exists($this->GitRepoDir));
    }

    function GetiRepoUrl() {
        $Url = sprintf("ssh://%s/%s/.git",$this->HostName, $this->GitRepoDir);
        return $Url;
    }

    /**
     * ローカルリポジトリ削除
     */
    function GitRepoDirDelete() {
        $output = NULL;
        if($this->GitRepoDirCheck()) {

            $cmd = sprintf("/bin/rm -rf %s 2>&1",$this->GitRepoDir);
            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                $logstr    = sprintf("Failed to delete Git repository.(%s)\n",$cmd);
                $logstr    .= implode("\n",$output);
                $this->SetLastErrorMsg($logstr);
                return false;
            }
        }
        return true;
    }

    /**
     * ローカルリポジトリ作成
     */
    public function GitInit() {

        global $g;

        $output     = NULL;
        $return_var = 0; 
        $cmd = sprintf("git init %s --share 2>&1", $this->GitRepoDir);
        $output = NULL;
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to create Git repository. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }

        // 日本語文字化け対応
        $output = NULL;
        $cmd = "git " . $this->gitOption . " config --local core.quotepath false  2>&1";
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to configure git config. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }

        $output = NULL;
        $cmd = "git " . $this->gitOption . " config --local user.email mail@example.com  2>&1";
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to configure git config. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }

        $output = NULL;
        $cmd = "git " . $this->gitOption . " config --local user.name exastro 2>&1";
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to configure git config. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }

        $output     = NULL;
        $return_var = 0; 
        $cmd = sprintf("git %s commit --allow-empty -m \"first commit\" 2>&1", $this->gitOption);
        $output = NULL;
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to First commit to Git repository. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }
        return true;
    }

    /**
     * リポジトリにファイル追加
     */
    public function GitAddFiles($AddFiles) {

        global $g;

        $output     = NULL;
        $return_var = 0; 
        $cmd = sprintf("/bin/cp -rfp %s %s 2>&1", $AddFiles, $this->GitRepoDir);
        $output = NULL;
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to copy files to Git repository. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }

        // リポジトリにファイル追加
        $output     = NULL;
        $return_var = 0; 
        $cmd = sprintf("git %s add . 2>&1", $this->gitOption);
        $output = NULL;
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to add file to Git repository. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }
        return true;
    }
    /**
     * ローカルリポジトリコミット
     */
    public function GitCommit() {

        global $g;

        $tmpVarTimeStamp = time();
        $committime = date("YmdHis",$tmpVarTimeStamp);
        // コミット
        $output     = NULL;
        $return_var = 0; 
        $cmd = sprintf("git %s commit -m \"%s\" 2>&1", $this->gitOption,$committime);
        $output = NULL;
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            $logstr = sprintf("Failed to commit to Git repository. (%s)\n",$cmd);
            $logstr .= implode("\n",$output);
            $this->SetLastErrorMsg($logstr);
            return false;
        }
        return true;
    }
}
?>
