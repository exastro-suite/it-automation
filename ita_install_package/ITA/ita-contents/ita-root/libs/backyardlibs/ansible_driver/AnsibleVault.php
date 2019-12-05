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
    function __construct() {}
    function Vault($password_file,$value ,&$encode_value,$indento="  ") {
        $result = true;
        $encode_value = ""; 
        exec("echo -n $value | ansible-vault encrypt --vault-password-file $password_file 2>&1",$output,$return_var);
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
    function getValutPasswdFileInfo() {
                     // ディレクトリ ファイル パスワード
        return array(".tmp",".key","passord");
    }
}
?>
