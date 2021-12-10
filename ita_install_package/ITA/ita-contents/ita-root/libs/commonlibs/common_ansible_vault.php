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
//  【概要】
//      ansible vault関連
//
//////////////////////////////////////////////////////////////////////
class  AnsibleVault {
    private $root_dir_path;
    private $dir;
    private $file;
    function __construct() {
        // ルートディレクトリを取得
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path = $root_dir_temp[0] . "ita-root";
        $this->dir     = '.tmp';
        $this->file    = '.tmpkey';
    }

    // このfunctionはファイル単位のansible_vault処理なので、現在未使用　使用時はvirtualenvの対応が必要
    function FileVault($exec_user,$password_file,$vault_file,&$vaultData,&$error_msg,$indento="  ") {
        $result = true;

        $path = sprintf('%s/confs/commonconfs/path_ANSIBLE_MODULE.txt',$this->root_dir_path);
        // 改行コードが付いている場合に取り除く
        $ansible_path = file_get_contents($path);
        $ansible_path = str_replace("\n","",$ansible_path);
        if($ansible_path === false) {
            $error_msg = 'ansible path file not found';
            return false;
        }
        // CR+LFをLFに置換

        $cmd = "sudo -u $exec_user -i $ansible_path/ansible-vault encrypt $vault_file --vault-password-file $password_file 2>&1";

        exec($cmd,$output,$return_var);
        if($return_var != 0) {
            $indento = '';
        }
        // vaultされたファイルはパーミッションが$exec_userで 0600 になるので
        // パーミッション変更
        $cmd = "sudo -u $exec_user -i chmod 0777 $vault_file 2>&1";
        exec($cmd,$output_chmod,$return_var_chmod);
        if($return_var_chmod != 0) {
           error_log("FILE:". basename(__FILE__) .":LINE:" .__LINE__.":".print_r($output_chmod,true));
           // エラーでも先に進む
        }

        $vaultData = file_get_contents($vault_file);
        //unlink($vault_file);

        foreach($output as $line) {
            if(strlen(trim($line)) == 0) {
                continue;
            }
            if(strlen($error_msg) != 0) {
                $error_msg .= "\n";
            }
            // インデント設定
            $error_msg .= $indento . $line;
        }
        $result = true;
        if($return_var != 0) {
            $result = false;
        }
        return $result;
    }

    function Vault($exec_user,$password_file,$value ,&$encode_value,$indento="  ",$in_engine_virtualenv_name, $execDirPath) {
        global $root_dir_path;
        $result = true;
        $encode_value = ""; 

        $strExecshellTemplateName = $root_dir_path . '/backyards/ansible_driver/ky_ansible_vault_command_shell_template.sh';
        // execDirPathはTower利用時はNFSの設定に依存してしまうので、~/ita-root/tempにshellを作成
        $strExecshellName         = sprintf("%s/temp/playbook_vault_execute_shell_%s.sh",$root_dir_path,getmypid());

        $path = sprintf('%s/confs/commonconfs/path_ANSIBLE_MODULE.txt',$this->root_dir_path);
        // 改行コードが付いている場合に取り除く
        $ansible_path = file_get_contents($path);
        $ansible_path = str_replace("\n","",$ansible_path);
        if($ansible_path === false) {
            $encode_value = "file not found.($path)";
            return false;
        }
        // virtualenv が設定されている場合、ansible_vaultのパスを空白にする。
        if($in_engine_virtualenv_name != "") {
            $virtualenv_flg = "__define__";
            $in_engine_virtualenv_name .= "/bin/activate";
            $ansible_path = "";
        } else {
            $virtualenv_flg = "__undefine__";
            $in_engine_virtualenv_name = "__undefine__";
            $ansible_path .= "/";
        }
        // CR+LFをLFに置換
        $value = str_replace("\r\n","\n", $value);


        $vault_value_file   = $root_dir_path . "/temp/ansible_vault_value_" . getmypid();
        exec("/bin/rm -rf " . $vault_value_file);
        $vault_stderr_file  = $root_dir_path . "/temp/ansible_vault_stderr_" . getmypid();
        exec("/bin/rm -rf " . $vault_stderr_file);

        $ret = file_put_contents($vault_value_file,$value);
        if($ret === false) {
            exec("/bin/rm -rf " . $vault_value_file);
            $encode_value = 'Failed to create temporary file for ansible vault.';
            return false;
        }

        $VaultCmd = "cat $vault_value_file | ${ansible_path}ansible-vault encrypt --vault-password-file $password_file";

        // sshAgentの設定とPlaybookを実行するshellのテンプレートを読み込み
        $strShell = file_get_contents($strExecshellTemplateName);
        if($strShell === false) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000020");
        }
        // テンプレート内の変数を実値に置き換え
        $strShell = str_replace('<<ansible_valut_command>>',$VaultCmd                       ,$strShell);
        $strShell = str_replace('<<virtualenv_path>>'      ,escapeshellarg($in_engine_virtualenv_name) ,$strShell);

        // Ansible実行shell作成
        $boolFilePut = file_put_contents($strExecshellName, $strShell);
        if( $boolFilePut===false ){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000010");
        }
        if( !chmod( $strExecshellName, 0777 ) ){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000020");
        }

        // Ansible_vault実行Commnad発行
        $cmd     = "sudo -u $exec_user -i {$strExecshellName} {$virtualenv_flg} 2>{$vault_stderr_file}";

        exec($cmd,$output,$return_var);

        if($return_var != 0) {
            $indento = '';
            $output = explode("\n", file_get_contents($vault_stderr_file));
        }

        exec("/bin/rm -rf " . $vault_value_file);
        exec("/bin/rm -rf " . $vault_stderr_file);
        exec("/bin/rm -rf " . $strExecshellName);

        foreach($output as $line) {
            if(strlen(trim($line)) == 0) {
                continue;
            }
            if(strlen($encode_value) != 0) {
                $encode_value .= "\n";
            }
            // インデント設定
            $encode_value .= $indento . $line;
        }
        if($return_var != 0) {
            $result = false;
        }
        return $result;
    }

    function setValutPasswdIndento($val,$indento) {
        $edit_val = "";
        $arry = explode("\n",$val);
        foreach($arry as $line){
            if($edit_val == "") {
                $edit_val  = $line;
            } else {
                $edit_val .= "\n" . $indento . $line;
            }
        }
        return $edit_val;
    }
    function setValutPasswdFileInfo($dir,$file) {
        $this->dir  = $dir;
        $this->file = $file;
    }
    function getValutPasswdFileInfo() {
        $ret      = true;
        $dir      = $this->dir;
        $file     = $this->file;
        $password = '';
        $file_path = sprintf('%s/confs/commonconfs/ansible_vault_accesskey.txt',$this->root_dir_path);
        $ret = file_get_contents($file_path);
        if( $ret !== false){
            $password = base64_decode(str_rot13($ret));
        }
                     // ディレクトリ ファイル パスワード
        return array($ret,$dir,$file,$password);
    }
    function CraeteValutPasswdFile($base_dir,&$pass_file) {
        $pass_file = '';
        list($ret,$dir,$file,$password) = $this->getValutPasswdFileInfo();
        if($ret === false) {
            return false;
        }
        $pass_file = sprintf('%s/%s/%s',$base_dir,$dir,$file);
        if(file_put_contents( $pass_file,$password) === false){
            return false;
        }
        return true;
    }
}
?>
