<?php
//   Copyright 2021 NEC Corporation
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
class FileUploadColumnDirectoryControl {
    protected   $lv_objMTS;
    protected   $lv_lasterrmsg;

    function __construct(){
        $this->lv_lasterrmsg = "";
    }

    function ClearLastError(){
        $this->lv_lasterrmsg = "";
    }

    function SetLastError($msg){
        $this->lv_lasterrmsg = $msg;
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }

    function CreateFileUpLoadMenuColumnFileDirectory($menuID,$ColumnName,$Pkey,$FileName,$Jnlkey,&$FilePath,&$JnlFilePath) {
        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        $this->ClearLastError();

        $cmd_list = array();
        $dir = sprintf("%s/uploadfiles",$root_dir_path);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$menuID);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$ColumnName);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%010d",$dir,$Pkey);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $FilePath = sprintf("%s/%s",$dir,$FileName);
        $dir = sprintf("%s/old",$dir,$Pkey);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%010d",$dir,$Jnlkey);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $JnlFilePath = sprintf("%s/%s",$dir,$FileName);
        foreach($cmd_list as $cmd) {
            exec($cmd . " 2>&1",$outAry,$retCode);
            if($retCode != 0) {
                $msg = sprintf("Failed to create directory for file upload imenu column file. \ncommand: %s\ndetail:%s",$cmd,implode("\n",$outAry));
                $this->SetLastError($msg);
                return false;
            }
        }
        return true;
    }

    function getFileUpLoadFilePath($menuID,$ColumnName,$pkey,$FileName) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
        $file = sprintf("%s/uploadfiles/%s/%s/%010d/%s",
                        $root_dir_path,
                        $menuID,
                        $ColumnName,
                        $pkey,
                        $FileName);
        return($file);
    }
    function FileUPloadColumnBackup($in_varass_menuID,$ColumnName,&$in_FileUPloadColumnBackupFilePath) {
        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        $this->ClearLastError();

        $FileUploadMenuPath = sprintf("%s/uploadfiles/%s",$root_dir_path,$in_varass_menuID);
        if($in_FileUPloadColumnBackupFilePath == "") {
            $in_FileUPloadColumnBackupFilePath = sprintf("%s/temp/ansible_driver_temp/%s_%s.tar.gz",$root_dir_path,$in_varass_menuID,$ColumnName);
            if(file_exists($in_FileUPloadColumnBackupFilePath)) {
                // 古いファイルを削除
                unlink($in_FileUPloadColumnBackupFilePath);
            }
        }

        $cmd = sprintf("sh %s/backyards/ansible_driver/ky_ansible_FileUploadColumnBackup.sh %s %s %s 2>&1",$root_dir_path,$FileUploadMenuPath,$in_FileUPloadColumnBackupFilePath,$ColumnName);
        $arry_out = array();
        $return_var = 0;
        $error_msg = "";
        exec($cmd,$arry_out,$return_var);
        if($return_var != 0) {
            $error_msg = "exit code: $return_var\n";
            $error_msg .= implode("\n",$arry_out);
            $this->SetLastError($error_msg);
            if(file_exists($in_FileUPloadColumnBackupFilePath)) {
                // バックアップ失敗の場合はtarファイルを消す
                unlink($in_FileUPloadColumnBackupFilePath);
            }
            return false;
        } else {
            return true;
        }
    }
    function CreateFileUpLoadMenuColumnDirectory($menuID,$ColumnName) {
        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        $this->ClearLastError();

        $cmd_list = array();
        $dir = sprintf("%s/uploadfiles",$root_dir_path);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$menuID);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$ColumnName);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        foreach($cmd_list as $cmd) {
            exec($cmd . " 2>&1",$arry_out,$retCode);
            if($retCode != 0) {
                $msg = sprintf("Failed to create directory. \ncommand: %s\ndetail:%s",$cmd,implode("\n",$arry_out));
                $this->SetLastError($msg);
                return false;
            }
        }
        return true;
    }
    function FileUPloadColumnRestore($in_varass_menuID,$ColumnName,$in_FileUPloadColumnBackupFilePath) {
        global $root_dir_path;
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        $this->ClearLastError();

        $FileUploadMenuPath = sprintf("%s/uploadfiles/%s",$root_dir_path,$in_varass_menuID);
        $FileUploadMenuColumnPath = sprintf("%s/uploadfiles/%s/%s",$root_dir_path,$in_varass_menuID,$ColumnName);
        // ファイルアップロードカラムのディレクトリを削除してからリストア
        //$cmd = sprintf("/bin/rm -rf %s 2>&1",$FileUploadMenuColumnPath);
        $cmd = sprintf("/bin/rm -rf %s 2>&1",escapeshellarg($FileUploadMenuColumnPath));
        $arry_out = array();
        $return_var = 0;
        $error_msg = "";
        exec($cmd,$arry_out,$return_var);
        if($return_var != 0) {
            $error_msg = "exit code: $return_var\n";
            $error_msg .= implode("\n",$arry_out);
            $this->SetLastError($error_msg);
            return false;
        }
        $cmd = sprintf("sh %s/backyards/ansible_driver/ky_ansible_FileUploadColumnRestore.sh %s %s 2>&1",$root_dir_path,$FileUploadMenuPath,$in_FileUPloadColumnBackupFilePath);
        $arry_out = array();
        $return_var = 0;
        $error_msg = "";
        exec($cmd,$arry_out,$return_var);
        if($return_var != 0) {
            $error_msg = "exit code: $return_var\n";
            $error_msg .= implode("\n",$arry_out);
            $this->SetLastError($error_msg);
            return false;
        } else {
            return true;
        }
    }
}
function getFileUpLoadColumnFilePath($menuID,$table_name,&$lva_FileUpLoadColumnFilePath_list,$objDBCA) {
    $menuID10str = sprintf("%010s",$menuID);
    list($aryTemp,$intErrorType,$strErrMsg) = getInfoOfLTUsingIdOfMenuForDBtoDBLink($menuID10str,$objDBCA);
    if($intErrorType !== null) {
        return $strErrMsg;
    }
    foreach($aryTemp['UPLOADFILE_PATHS'] as $ColumnName=>$FilePath) {
        $lva_FileUpLoadColumnFilePath_list[$table_name][$ColumnName] = $FilePath;
    }
    return true;
}
?>
