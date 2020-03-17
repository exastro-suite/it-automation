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
    function Vault($exec_user,$password_file,$value ,&$encode_value,$indento="  ") {
        $result = true;
        $encode_value = ""; 

        $path = sprintf('%s/confs/commonconfs/path_ANSIBLE_MODULE.txt',$this->root_dir_path);
        // 改行コードが付いている場合に取り除く
        $ansible_path = file_get_contents($path);
        $ansible_path = str_replace("\n","",$ansible_path);
        if($ansible_path === false) {
            $encode_value = 'ansible path file not found';
            return false;
        }
        //$cmd = "sudo -u $exec_user -H -i echo -n $value | sudo -i $ansible_path/ansible-vault encrypt --vault-password-file $password_file 2>&1";
        $cmd = "echo -n $value | sudo -u $exec_user -i $ansible_path/ansible-vault encrypt --vault-password-file $password_file 2>&1";




        exec($cmd,$output,$return_var);
        if($return_var != 0) {
            $indento = '';
        }
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
        //$pass_file= sprintf('%s%s/%s/%s',$this->root_dir_path,$base_dir,$dir,$file);
        $pass_file = sprintf('%s/%s/%s',$base_dir,$dir,$file);
        if(file_put_contents( $pass_file,$password) === false){
            return false;
        }
        return true;
    }
}
?>
