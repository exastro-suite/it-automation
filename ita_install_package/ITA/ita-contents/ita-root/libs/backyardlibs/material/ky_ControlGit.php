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

class ControlGit {

    private $remortRepoUrl;     // GitのリモートリポジトリURL
    private $branch;            // Gitのブランチ
    private $cloneRepoDir;      // Gitクローンディレクトリ
    private $password;          // Gitのパスワード
    private $gitOption;         // Gitのオプション（--git-dir、--work-tree）
    private $tmpDir;            // 作業用ディレクトリ
    private $ClonecloneDir;     // クローンリポジトリのクローンディレクトリ
    private $libPath;           // ライブラリのパス

    /**
     * コンストラクタ
     */
    public function __construct($remortRepoUrl, $branch, $cloneRepoDir, $password, $libPath) {
        $this->remortRepoUrl = $remortRepoUrl;
        $this->branch = $branch;
        $this->cloneRepoDir = $cloneRepoDir . "/";
        $this->password = $password;
        $this->gitOption = "--git-dir " . $this->cloneRepoDir . "/.git --work-tree=" . $this->cloneRepoDir;
        $this->tmpDir = "";
        $this->ClonecloneDir = dirname($cloneRepoDir) . "/clonecloneRepo/";
        $this->gitOption2 = "--git-dir " . $this->ClonecloneDir . "/.git --work-tree=" . $this->ClonecloneDir;
        $this->libPath = $libPath;
    }

    /**
     * Gitのルートパス取得
     */
    public function getCloneRepoDir() {
        return $this->cloneRepoDir;
    }

    /**
     * クローン
     */
    public function cloneGit(&$errMsg) {

        global $g;

        // Git CLONE
        $output = NULL;
        if("" == $this->branch){
            $branch = "master";
        }
        else{
            $branch = $this->branch;
        }

        $cmd = "sudo -i " . $this->libPath . "ky_cloneGit.sh '" . $this->remortRepoUrl . "' '" . $this->cloneRepoDir . "' '" . $branch . "' '" . $this->password . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // 日本語文字化け対応
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " config --local core.quotepath false  2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }
        return $return_var;
    }

    /**
     * リビジョン取得
     */
    public function getRevision(&$revision, &$errMsg) {

        // Git LOG
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " log  2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        $revision=substr($output[0], 7);    // 先頭の「commit 」を削除
        return $return_var;
    }

    /**
     * ファイルリビジョン取得
     */
    public function getFileRevision($filePath, &$revision, &$errMsg) {

        $gitFilePath = $this->cloneRepoDir . $filePath;

        // Git LOG
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " log -- '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        $revision=substr($output[0], 7);    // 先頭の「commit 」を削除
        return $return_var;
    }

    /**
     * ステータス取得
     */
    public function getStatus(&$output, &$errMsg) {

        // Git STATUS
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " status 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }
        return $return_var;
    }

    /**
     * コミット
     */
    public function commitGit($filePath, &$errMsg) {

        global $g;

        $gitFilePath = $this->cloneRepoDir . $filePath;

        // Git ADD
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " add -- '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git ユーザー設定
        $output = NULL;
        $cmd = "sudo git config --global user.name 'Your Name' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git アドレス設定
        $output = NULL;
        $cmd = "sudo git config --global user.email 'you@example.com' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git COMMIT
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " commit '" . $gitFilePath . "' -m '" . date("Y/m/d H:m:s") . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git PUSH
        $output = NULL;
        $cmd = "sudo -i " . $this->libPath . "ky_pushGit.sh '" . $this->gitOption . "' " . $this->password . " 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        return $return_var;
    }

    /**
     * 移動
     */
    public function mvGit($orgFromPath, $orgToPath, &$errMsg) {

        global $g;
        $fromPath = $this->cloneRepoDir . $orgFromPath;
        $toPath = $this->cloneRepoDir . $orgToPath;

        // Git config
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " config --local core.quotepath false 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git ls-files
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " ls-files 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git管理上にディレクトリがあるか確認する
        $matchFlg = false;
        foreach($output as $gitFile){
            if(substr("/" . $gitFile, 0, strlen($orgFromPath)) === $orgFromPath){
                $matchFlg = true;
                break;
            }
        }

        // 移動
        $output = NULL;
        $cmd = "sudo mv -- '" . $fromPath . "' '" . $toPath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        if(true === $matchFlg){

            // Git ADD
            $output = NULL;
            $cmd = "sudo git " . $this->gitOption . " add -- '" . $toPath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git ユーザー設定
            $output = NULL;
            $cmd = "sudo git config --global user.name 'Your Name' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git アドレス設定
            $output = NULL;
            $cmd = "sudo git config --global user.email 'you@example.com' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git COMMIT
            $output = NULL;
            $cmd = "sudo git " . $this->gitOption . " commit -a -m '" . date("Y/m/d H:m:s") . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git PUSH
            $output = NULL;
            $cmd = "sudo -i " . $this->libPath . "ky_pushGit.sh '" . $this->gitOption . "' " . $this->password . " 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }
        }

        return $return_var;
    }

    /**
     * 削除
     */
    public function removeGit($filePath, &$errMsg) {

        global $g;

        $gitFilePath = $this->cloneRepoDir . $filePath;

        // Git ADD
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " rm -- '" . $gitFilePath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git ユーザー設定
        $output = NULL;
        $cmd = "sudo git config --global user.name 'Your Name' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git アドレス設定
        $output = NULL;
        $cmd = "sudo git config --global user.email 'you@example.com' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git COMMIT
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " commit '" . $gitFilePath . "' -m '" . date("Y/m/d H:m:s") . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git PUSH
        $output = NULL;
        $cmd = "sudo -i " . $this->libPath . "ky_pushGit.sh '" . $this->gitOption . "' " . $this->password . " 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        return $return_var;
    }

    /**
     * ディレクトリ削除
     */
    public function removeGitDir($orgDirPath, &$errMsg) {

        global $g;

        $dirPath = $this->cloneRepoDir . $orgDirPath;

        // Git ls-files
        $output = NULL;
        $cmd = "sudo git " . $this->gitOption . " ls-files 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        // Git管理上にディレクトリがあるか確認する
        $matchFlg = false;
        foreach($output as $gitFile){
            if(substr("/" . ltrim($gitFile, '"'), 0, strlen($orgDirPath)) === $orgDirPath){
                $matchFlg = true;
                break;
            }
        }

        if(true === $matchFlg){

            // Git ADD
            $output = NULL;
            $cmd = "sudo git " . $this->gitOption . " rm -r --cache -- '" . $dirPath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git ユーザー設定
            $output = NULL;
            $cmd = "sudo git config --global user.name 'Your Name' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git アドレス設定
            $output = NULL;
            $cmd = "sudo git config --global user.email 'you@example.com' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git COMMIT
            $output = NULL;
            $cmd = "sudo git " . $this->gitOption . " commit -m '" . date("Y/m/d H:m:s") . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }

            // Git PUSH
            $output = NULL;
            $cmd = "sudo -i " . $this->libPath . "ky_pushGit.sh '" . $this->gitOption . "' " . $this->password . " 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return $return_var;
            }
        }

        // ディレクトリ削除
        $output = NULL;
        $cmd = "sudo rm -rf -- '" . $dirPath . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            $errMsg = print_r($output, true);
            return $return_var;
        }

        return $return_var;
    }

    /**
     * ファイル取得
     */
    public function getBase64File($filePath, $revision, &$base64File, &$errMsg) {

        $gitFilePath = $this->cloneRepoDir . $filePath;

        // リビジョンの指定が無い場合、Git上の最新ファイルを返却する
        if("" == $revision){
            if(file_exists($gitFilePath)){
                $base64File = base64_encode(file_get_contents($gitFilePath));
            }
            else{
                $errMsg = "File[" . $gitFilePath . "] does not exist in Git.";
                return 1;
            }
        }
        // リビジョンの指定がある場合、Git上の該当ファイルを返却する
        else{
            // リビジョンの存在確認
            $output = NULL;
            $cmd = "sudo git " . $this->gitOption . " log 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return 2;
            }

            $matchFlg = false;
            foreach($output as $outputValue){
                if("" != $outputValue && false != strpos($outputValue, $revision)) {
                    $matchFlg = true;
                    break;
                }
            }

            if(false === $matchFlg) {
                $errMsg = "Revision[" . $revision . "] does not exist in Git.";
                return 1;
            }

            // クローンリポジトリのクローン作成
            $this->rmCloneClone();
            $output = NULL;
            $cmd = "sudo " . $this->libPath . "ky_cloneGit.sh $this->cloneRepoDir $this->ClonecloneDir $this->password 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                return 2;
            }

            // クローンクローンの対象ファイルパス
            $clonecloneFilePath = $this->ClonecloneDir . $filePath;

            // 指定のリビジョンに戻す
            $output = NULL;
            $cmd = "sudo git "  . $this->gitOption2 . " checkout " . $revision . " '" . $clonecloneFilePath . "' 2>&1";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                $errMsg = print_r($output, true);
                $this->rmCloneClone();
                return 1;
            }

            $base64File = base64_encode(file_get_contents($clonecloneFilePath));
            $this->rmCloneClone();
        }
        return 0;
    }

    /**
     * クローンクローンリポジトリ削除
     */
    private function rmCloneClone() {
        if(file_exists($this->ClonecloneDir)) {
            $output = NULL;
            $cmd = "sudo rm -rf -- '" . $this->ClonecloneDir . "' 2>&1";
            exec($cmd, $output, $return_var);
        }
    }
}
