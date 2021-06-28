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
 *  ファイル操作
 * 
 */
class LocalFilesControl {
    // Gitコマンド標準出力結果解析文字列定義ファイル
    const  C_GitCmdRsltParstrFileName    = "%s/confs/backyardconfs/CICD_For_IaC/gitCommandResultParsingStringDefinition.ini";
    // Git同期 shellコマンドパス
    const  C_LocalShellDir               = "%s/backyards/CICD_for_IaC";

    // Git同期 ローカルクローンディレクトリ
    const  C_LocalCloneDir               = "%s/repositorys/%010s";

    // Git同期 子プロセログファイル名
    const  C_ChildProcessName            = "ky_CICD_for_IaC_git_synchronize-child-workflow.php";

    // Git同期 子プロセログファイル名
    const  C_GrandChildProcessName       = "ky_CICD_for_IaC_git_synchronize-grandchild-workflow.php";

    private $root_dir_path;

    public function __construct() {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    function getChildProcessExecName() {
        return self::C_ChildProcessName;
    }

    function getGrandChildProcessExecName() {
        return self::C_GrandChildProcessName;
    }

    function getLocalCloneDir($RepoId) {
        $dir = sprintf(self::C_LocalCloneDir,$this->root_dir_path,$RepoId);
        return $dir;
    }    

    function getLocalShellDir() {
        $dir = sprintf(self::C_LocalShellDir,$this->root_dir_path);
        return $dir;
    }    
    
    function getGitCmdRsltParsStrFileNamePath() {
        $dir = sprintf(self::C_GitCmdRsltParstrFileName,$this->root_dir_path);
        return $dir;
    }
}
?>
