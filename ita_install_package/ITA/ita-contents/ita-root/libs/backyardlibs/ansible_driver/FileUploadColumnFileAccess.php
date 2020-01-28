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
////////////////////////////////
// ルートディレクトリを取得   //
////////////////////////////////
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php');
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');

/////////////////////////////////////////////////////////////////////////////////
//  処理概要
//    Template管理の変数定義をspycで解析した結果をファイルで管理
//
/////////////////////////////////////////////////////////////////////////////////
class FileUploadColumnFileAccessBase{
    protected   $lv_objMTS;
    protected   $lv_objDBCA;
    protected   $menuID;
    protected   $ColumnName;
    protected   $HistoryDirUseFlg;
    protected   $lv_lasterrmsg;
    protected   $web_mode;

    function __construct($objMTS,$objDBCA,$menuID,$ColumnName,$HistoryDirUseFlg=false){
        $this->lv_objMTS                   = $objMTS;
        $this->lv_objDBCA                  = $objDBCA;
        $this->menuID                      = $menuID;
        $this->ColumnName                  = $ColumnName;
        $this->HistoryDirUseFlg            = $HistoryDirUseFlg;
        $this->web_mode                    = false;
        $this->lv_lasterrmsg               = array();
        if( isset($_SERVER) === true ){
            if( array_key_exists('HTTP_HOST', $_SERVER) === true ){
                $this->web_mode  = true;
            }
        }
    }

    function SetLastError($p1,$p2,$p3){
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        $this->lv_lasterrmsg    = array();
        $this->lv_lasterrmsg[0] = $p3;
        $this->lv_lasterrmsg[1] = "FILE:$p1 LINE:$p2 $p3";
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }

    function CreateBaseDir($pkey) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";

        $cmd_list = array();
        $dir = sprintf("%s/uploadfiles",$root_dir_path);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$this->menuID);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%s",$dir,$this->ColumnName);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/%010d",$dir,$pkey);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        foreach($cmd_list as $cmd) {
            system($cmd);
        }
        return($dir);
    }

    function getFilePath($pkey,$FileName = Null) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
        $file = sprintf("%s/uploadfiles/%s/%s/%010d/%s",
                        $root_dir_path,
                        $this->menuID,
                        $this->ColumnName,
                        $pkey,
                        $FileName);
        return($file);
    }
}
class TemplateVarsStructAnalFileAccess extends FileUploadColumnFileAccessBase {
    protected   $lv_objMTS;
    protected   $lv_objDBCA;
    protected   $menuID;
    protected   $ColumnName;
    protected   $HistoryDirUseFlg;
    protected   $lv_lasterrmsg;
    protected   $web_mode;

    function __construct($objMTS,$objDBCA,$HistoryDirUseFlg=false){
        $this->lv_objMTS                   = $objMTS;
        $this->lv_objDBCA                  = $objDBCA;
        $this->menuID                      = "2100040704";
        $this->ColumnName                  = "VAR_STRUCT_ANAL_JSON_STRING_FILE";
        $this->HistoryDirUseFlg            = $HistoryDirUseFlg;
        $this->web_mode                    = false;
        $this->lv_lasterrmsg               = array();

        parent::__construct($objMTS,$objDBCA,$this->menuID,$this->ColumnName,$HistoryDirUseFlg);
        if( isset($_SERVER) === true ){
            if( array_key_exists('HTTP_HOST', $_SERVER) === true ){
                $this->web_mode  = true;
            }
        }
    }

    function SetLastError($p1,$p2,$p3){
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        $this->lv_lasterrmsg    = array();
        $this->lv_lasterrmsg[0] = $p3;
        $this->lv_lasterrmsg[1] = "FILE:$p1 LINE:$p2 $p3";
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }
    function getFilePath($pkey,$FileName = Null) {
        return(parent::getFilePath($pkey,'AnalysFile.json'));
    }

    function FileRead($path,
                     &$Vars_list,
                     &$Array_vars_list,
                     &$LCA_vars_use,
                     &$Array_vars_use,
                     &$GBL_vars_info,
                     &$VarVal_list) {

        $Vars_list         = array();
        $Array_vars_list   = array();
        $LCA_vars_use      = array();
        $Array_vars_use    = array();
        $GBL_vars_info     = array();
        $VarVal_list       = array();
      
        // UIからよばれるので、ワーニング抑止
        $json_string = @file_get_contents($path);
        if($json_string === false) {
            $this->SetLastError(basename(__FILE__),__LINE__,$this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000055",array(__LINE__,$path)));
            return false;
        } 
        $php_array         = json_decode($json_string,true);
        $Vars_list         = $php_array['Vars_list'];
        $Array_vars_list   = $php_array['Array_vars_list'];
        $LCA_vars_use      = $php_array['LCA_vars_use'];
        $Array_vars_use    = $php_array['Array_vars_use'];
        $GBL_vars_info     = $php_array['GBL_vars_info'];
        $VarVal_list       = $php_array['VarVal_list'];
        return true;
    }

    function FileWrite($path,
                       $Vars_list,
                       $Array_vars_list,
                       $LCA_vars_use,
                       $Array_vars_use,
                       $GBL_vars_info,
                       $VarVal_list) {
        $php_array                        = array();
        $php_array['Vars_list']           = $Vars_list;
        $php_array['Array_vars_list']     = $Array_vars_list;
        $php_array['LCA_vars_use']        = $LCA_vars_use;
        $php_array['Array_vars_use']      = $Array_vars_use;
        $php_array['GBL_vars_info']       = $GBL_vars_info;
        $php_array['VarVal_list']         = $VarVal_list;

        // UIからよばれるので、ワーニング抑止
        $ret = @file_put_contents($path,json_encode($php_array));
        // エラーチェック
        if($ret === false) {
            $this->SetLastError(basename(__FILE__),__LINE__,$this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000054",array($path)));
            return false;
        }
        $cmd = sprintf("chmod 0777 %s",$path);
        system($cmd);
        return true;
    }
    function putVarStructAnalysis($PkeyID,
                                  $TPFVarName,
                                  $VarStructString,
                                 &$Vars_list,
                                 &$Array_vars_list,
                                 &$LCA_vars_use,
                                 &$Array_vars_use,
                                 &$GBL_vars_info,
                                 &$VarVal_list) {

        $retBool         = true;

        $Vars_list       = array();
        $Array_vars_list = array();
        $GBL_vars_info   = array();
        $VarVal_list     = array();
        $LCA_vars_use    = false;
        $Array_vars_use  = false;

        // 変数定義を一時ファイルに保存
        $tmp_file_name = '/tmp/.TemplateVarList' . getmypid() . ".yaml";
        @file_put_contents( $tmp_file_name,$VarStructString);

        global $objMTS;
        $chkObj = new YAMLFileAnalysis($this->lv_objMTS);

        $role_pkg_name     = $TPFVarName;
        $rolename          = 'dummy';
        $display_file_name = '';
        $ITA2User_var_list = array();
        $User2ITA_var_list = array();
        $parent_vars_list  = array();

        // 変数定義を解析
        $ret = $chkObj->VarsFileAnalysis(LC_RUN_MODE_VARFILE,
                                         $tmp_file_name,
                                         $parent_vars_list,
                                         $Vars_list,
                                         $Array_vars_list,
                                         $VarVal_list,
                                         $role_pkg_name,
                                         $rolename,
                                         $display_file_name,
                                         $ITA2User_var_list,
                                         $User2ITA_var_list);

        if($ret === false) {
            // 解析結果にエラーがある場合
            $errmsg = $chkObj->GetLastError();
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg[0]);
            $retBool = false;
        } else {
            foreach($parent_vars_list as $vars_info) {
                $var_name = $vars_info['VAR_NAME'];
                $line     = $vars_info['LINE'];
                $ret = preg_match("/^VAR_[a-zA-Z0-9_]*/",$var_name);
                if($ret != 0) {
                    // 多段変数の場合
                    if(isset($Array_vars_list[$var_name])) {
                        $Array_vars_use = true;
                    }
                } else {
                    // 読替変数の場合
                    $ret = preg_match("/^LCA_[a-zA-Z0-9_]*/",$var_name);
                    if($ret != 0) {
                        $LCA_vars_use = true;
                    } else {
                        // グローバル変数の場合
                        $ret = preg_match("/^GBL_[a-zA-Z0-9_]*/",$var_name);
                        if($ret != 0) {
                            // 多段定義の場合
                            if( ! isset($Vars_list[$var_name])) {
                                $this->SetLastError(basename(__FILE__),__LINE__,
                                                    $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000020",
                                                                                     array($line,$var_name)));
                                $retBool = false;
                            } else {
                                if($Vars_list[$var_name] == 0) {
                                    $GBL_vars_info['1'][$var_name] = '0';
                                } else {
                                    // 複数具体値定義の場合
                                    $this->SetLastError(basename(__FILE__),__LINE__,
                                                        $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000020",
                                                                                         array($line,$var_name)));
                                    $retBool = false;
                                }
                            }
                        } else {
                            // 変数名がastrollで扱えない場合
                            $this->SetLastError(basename(__FILE__),__LINE__,
                                                $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000017",
                                                                                  array($line,$var_name)));
                            $retBool = false;
                        }
                    }
                }
            }
        }
        // 一時ファイル削除
        @unlink($file_name);
        if($retBool === true) {
            // 保存先パス生成・確認
            $this->CreateBaseDir($PkeyID);

            // 保存先パス取得
            $path = $this->getFilePath($PkeyID);

            $ret  = $this->FileWrite($path,
                                     $Vars_list,
                                     $Array_vars_list,
                                     $LCA_vars_use,
                                     $Array_vars_use,
                                     $GBL_vars_info,
                                     $VarVal_list);
            if($ret === false) {
                $errmsg = $this->GetLastError();
                $this->SetLastError(basename(__FILE__),__LINE__,$errmsg[0]);
                $retBool = false;
            }
        }
        return $retBool;
    }
    function getVarStructAnalysis($PkeyID,
                                  $TPFVarName,
                                  $VarStructString,
                                 &$Vars_list,
                                 &$Array_vars_list,
                                 &$LCA_vars_use,
                                 &$Array_vars_use,
                                 &$GBL_vars_info,
                                 &$VarVal_list) {

        $retBool         = true;

        $Vars_list       = array();
        $Array_vars_list = array();
        $GBL_vars_info   = array();
        $VarVal_list     = array();
        $LCA_vars_use    = false;
        $Array_vars_use  = false;
        $path = $this->getFilePath($PkeyID);

        if( file_exists($path) === true){
            // 変数定義の解析結果をファイルから取得
            $ret  = $this->FileRead($path,
                                    $Vars_list,
                                    $Array_vars_list,
                                    $LCA_vars_use,
                                    $Array_vars_use,
                                    $GBL_vars_info,
                                    $VarVal_list);
            if($ret === false) {
                $errmsg = $this->GetLastError();
                $this->SetLastError(basename(__FILE__),__LINE__,$errmsg[0]);
                $retBool = false;
            }
        } else {
            // 変数定義の解析しファイルに保存
            $ret = $this->putVarStructAnalysis($PkeyID,
                                               $TPFVarName,
                                               $VarStructString,
                                               $Vars_list,
                                               $Array_vars_list,
                                               $LCA_vars_use,
                                               $Array_vars_use,
                                               $GBL_vars_info,
                                               $VarVal_list);

            if($ret === false) {
                $errmsg = $this->GetLastError();
                $this->SetLastError(basename(__FILE__),__LINE__,$errmsg[0]);
                $retBool = false;
            }
        }
        return $retBool;
    }
    function ArrayTOjsonString($Vars_list,
                               $Array_vars_list,
                               $LCA_vars_use,
                               $Array_vars_use,
                               $GBL_vars_info,
                               $VarVal_list) {
        $php_array                        = array();
        $php_array['Vars_list']           = $Vars_list;
        $php_array['Array_vars_list']     = $Array_vars_list;
        $php_array['LCA_vars_use']        = $LCA_vars_use;
        $php_array['Array_vars_use']      = $Array_vars_use;
        $php_array['GBL_vars_info']       = $GBL_vars_info;
        $php_array['VarVal_list']         = $VarVal_list;

        return(json_encode($php_array));
    }
}
?>
