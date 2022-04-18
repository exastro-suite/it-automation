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
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibleMakeMessage.php");
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/FileUploadColumnFileAccess.php");
////////////////////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・Ansible 共通モジュール
//
//   F0001  SetRunMode
//   F0002  GetRunMode
//   F0003  getCPFVarsMaster 
//   F0004  chkCPFVarsMasterReg
//   F0005  getTPFVarsMaster
//   F0006  chkTPFVarsMasterReg
//   F0007  getGBLVarsMaster
//   F0008  chkGBLVarsMasterReg
//   F0009  CommonVarssAanalys
//   F0010  selectDBRecodes
//
////////////////////////////////////////////////////////////////////////////////////
class AnsibleCommonLibs {
    // 処理モード
    protected   $lv_run_mode;
    function __construct($run_mode=LC_RUN_MODE_STD){
        $this->lv_run_mode = $run_mode;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   処理モードを変数定義ファイルチェックに設定
    //
    // パラメータ
    //   処理モード　LC_RUN_MODE_STD/LC_RUN_MODE_VARFILE
    //
    // 戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function SetRunMode($run_mode){
        $this->lv_run_mode = $run_mode;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   処理モード取得
    //
    // パラメータ
    //   なし
    //
    // 戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function GetRunMode(){
        return($this->lv_run_mode);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   ファイル管理の情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $in_cpf_var_name:      CPF変数名
    //   $in_cpf_key:           PKey格納変数
    //   $in_cpf_file_name:     ファイル格納変数
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getCPFVarsMaster($in_objMTS,$in_objDBCA,
                                 $in_cpf_var_name,&$in_cpf_key,&$in_cpf_file_name,
                                 &$in_errmsg,&$in_errdetailmsg){
        $sql = "SELECT                         \n" .
               "  CONTENTS_FILE_ID,            \n" .
               "  CONTENTS_FILE                \n" .
               "FROM                           \n" .
               "  B_ANS_CONTENTS_FILE          \n" .
               "WHERE                          \n" .
               "  CONTENTS_FILE_VARS_NAME = '" . $in_cpf_var_name . "' AND \n" .
               "  DISUSE_FLAG            = '0';\n";
    
        $in_cpf_key = "";
        $in_cpf_file_name = "";
        $in_errmsg = "";
        $in_errdetailmsg = "";
            
        $objQuery = $in_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $ina_child_playbooks = array();
        $row = $objQuery->resultFetch();
    
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            // ファイルが未登録の場合のエラー処理は呼び側にまかせる。
            unset($objQuery);
            return true;
        }
        $in_cpf_key       = $row["CONTENTS_FILE_ID"];
        $in_cpf_file_name = $row["CONTENTS_FILE"];
    
        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    // 処理内容
    //   CPF変数がファイル管理に登録されているか判定
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $ina_cpf_vars_list:    CPF変数リスト
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkCPFVarsMasterReg( $in_objMTS,$in_objDBCA,
                                 &$ina_cpf_vars_list,
                                 &$in_errmsg,&$in_errdetailmsg){
        $boolRet   = true;
        $in_errmsg = "";
        $in_errdetailmsg = "";
        $fatal_error = false;
        // CPF変数がファイル管理に登録されているか判定
        foreach( $ina_cpf_vars_list as $role_name => $tgt_file_list ){
            foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                foreach( $line_no_list as $line_no => $cpf_var_name_list ){
                    foreach( $cpf_var_name_list as $cpf_var_name => $dummy ){
                        $cpf_key = "";
                        $cpf_file_name = "";
                        // CPF変数名からファイル管理とPkeyを取得する。
                        $db_errmsg = "";
                        $ret = $this->getCPFVarsMaster($in_objMTS,$in_objDBCA,$cpf_var_name,$cpf_key,$cpf_file_name,$db_errmsg,$in_errdetailmsg);
                        if( $ret == false ){
                            // DBエラーを優先表示
                            $in_errmsg = $db_errmsg;
                            $boolRet = false;
                            $fatal_error = true;
                            break;
                        }
                        // CPF変数名が未登録の場合
                        if( $cpf_key == "" ){
                            if($in_errmsg != ""){
                                $in_errmsg = $in_errmsg . "\n";
                            }
                            $in_errmsg = $in_errmsg . AnsibleMakeMessage($in_objMTS,$this->GetRunMode(),
                                                                         "ITAANSIBLEH-ERR-90090", array($role_name,
                                                                                                        $tgt_file,
                                                                                                        $line_no,
                                                                                                        $cpf_var_name));
                            $boolRet = false;
                            continue;
                        }
                        else{
                            // ファイル名が未登録の場合
                            if($cpf_file_name == "" ){
                                if($in_errmsg != ""){
                                    $in_errmsg = $in_errmsg . "\n";
                                }
                                $in_errmsg = $in_errmsg . AnsibleMakeMessage($in_objMTS,$this->GetRunMode(),
                                                                             "ITAANSIBLEH-ERR-90091", array($role_name,
                                                                                                            $tgt_file,
                                                                                                            $line_no,
                                                                                                            $cpf_var_name));
                                $boolRet = false;
                                continue;
                            }
                        }
                        $ina_cpf_vars_list[$role_name][$tgt_file][$line_no][$cpf_var_name] = array();
                        $ina_cpf_vars_list[$role_name][$tgt_file][$line_no][$cpf_var_name]['CONTENTS_FILE_ID'] = $cpf_key;
                        $ina_cpf_vars_list[$role_name][$tgt_file][$line_no][$cpf_var_name]['CONTENTS_FILE']    = $cpf_file_name;
                    }
                    if($fatal_error === true){
                        break;
                    }
                }
                if($fatal_error === true){
                    break;
                }
            }
            if($fatal_error === true){
                break;
            }
        }
        return $boolRet;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   テンプレート管理の情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $in_tpf_var_name:      TPF変数名
    //   $ina_row:              登録情報
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getTPFVarsMaster($in_objMTS,$in_objDBCA,
                              $in_tpf_var_name,&$ina_row,
                              &$in_errmsg,&$in_errdetailmsg){
        $sql = "SELECT                         \n" .
               "  *                            \n" .
               "FROM                           \n" .
               "  B_ANS_TEMPLATE_FILE          \n" .
               "WHERE                          \n" .
               "  ANS_TEMPLATE_VARS_NAME = '" . $in_tpf_var_name . "' AND \n" .
               "  DISUSE_FLAG            = '0';\n";
    
        $in_tpf_key = "";
        $in_tpf_file_name = "";
        $in_errmsg = "";
        $in_errdetailmsg = "";
            
        $objQuery = $in_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $ina_child_playbooks = array();
        $row = $objQuery->resultFetch();
    
        $ina_row = array();
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            // ファイルが未登録の場合のエラー処理は呼び側にまかせる。
            unset($objQuery);
            return true;
        }
        $ina_row = $row;
    
        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   TPF変数がテンプレート管理に登録されているか判定
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $ina_tpf_vars_list:    TPF変数リスト
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkTPFVarsMasterReg( $in_objMTS,$in_objDBCA,
                                 &$ina_tpf_vars_list,
                                 &$in_errmsg,&$in_errdetailmsg){
        $boolRet   = true;
        $in_errmsg = "";
        $in_errdetailmsg = "";
        $fatal_error = false;
        // TPF変数がファイル管理に登録されているか判定
        foreach( $ina_tpf_vars_list as $role_name => $tgt_file_list ){
            foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                foreach( $line_no_list as $line_no => $tpf_var_name_list ){
                    foreach( $tpf_var_name_list as $tpf_var_name => $dummy ){
                        // TPF変数名からテンプレートファイル名とPkeyを取得する。
                        $db_errmsg = "";
                        $row = array();
                        $ret = $this->getTPFVarsMaster($in_objMTS,$in_objDBCA,$tpf_var_name,$row,$db_errmsg,$in_errdetailmsg);
                        if( $ret == false ){
                            // DBエラーを優先表示
                            $in_errmsg = $db_errmsg;
                            $boolRet = false;
                            $fatal_error = true;
                            break;
                        }
                        // TPF変数名が未登録の場合
                        $tpf_key            = "";
                        $tpf_file_name      = "";
                        $tpf_role_only_flag = "";
                        $vars_list          = "";
                        // Legacy Roleのバックヤードでも呼ばれている
                        // Legacy Roleのバックヤードでは登録されているテンプレートの情報
                        // で処理を進めるので、0件(未登録)でも処理を進める
                        if(count($row) != 0) {
                            $tpf_key            = $row["ANS_TEMPLATE_ID"];
                            $tpf_file_name      = $row["ANS_TEMPLATE_FILE"];
                        }
                        if( $tpf_key == "" ){
                            if($in_errmsg != ""){
                                $in_errmsg = $in_errmsg . "\n";
                            }
                            $in_errmsg = $in_errmsg . AnsibleMakeMessage($in_objMTS,$this->GetRunMode(),
                                                                         "ITAANSIBLEH-ERR-6000007", array($role_name,
                                                                                                          $tgt_file,
                                                                                                          $line_no,
                                                                                                          $tpf_var_name));
                            $boolRet = false;
                            continue;
                        }
                        else{
                            // ファイル名が未登録の場合
                            if($tpf_file_name == "" ){
                                if($in_errmsg != ""){
                                    $in_errmsg = $in_errmsg . "\n";
                                }
                                $in_errmsg = $in_errmsg . AnsibleMakeMessage($in_objMTS,$this->GetRunMode(),
                                                                             "ITAANSIBLEH-ERR-6000005", array($role_name,
                                                                                                              $tgt_file,
                                                                                                              $line_no,
                                                                                                              $tpf_var_name));
                                $boolRet = false;
                                continue;
                            }
                        }
                        $Vars_list        = array();
                        $Array_vars_list  = array();
                        $LCA_vars_use     = false;
                        $Array_vars_use   = false;
                        $GBL_vars_info    = array();
                        $VarVal_list      = array();
                        $strVarName       = $tpf_var_name;
                        $PkeyID           = $row['ANS_TEMPLATE_ID'];
                        $strVarsList      = $row['VARS_LIST'];

                        // 変数定義の解析結果を取得
                        $fileObj = new TemplateVarsStructAnalFileAccess($in_objMTS,$in_objDBCA);

                        // 変数定義の解析結果をファイルから取得
                        // ファイルがない場合は、変数定義を解析し解析結果をファイルに保存
                        $ret = $fileObj->getVarStructAnalysis($PkeyID,
                                                              $strVarName,
                                                              $strVarsList,
                                                              $Vars_list,
                                                              $Array_vars_list,
                                                              $LCA_vars_use,
                                                              $Array_vars_use,
                                                              $GBL_vars_info,
                                                              $VarVal_list);
                        if($ret === false) {
                            $errmsg = $fileObj->GetLastError();
                            if($in_errmsg != "") $in_errmsg = $in_errmsg . "\n";
                            $boolRet = false;
                            continue;
                        }
                        //変数定義の解析結果をjson形式の文字列に変換
                        $php_array = $fileObj->ArrayTOjsonString($Vars_list,
                                                                 $Array_vars_list,
                                                                 $LCA_vars_use,
                                                                 $Array_vars_use,
                                                                 $GBL_vars_info,
                                                                 $VarVal_list);
                        unset($fileObj);

                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name] = array();
                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name]['CONTENTS_FILE_ID'] = $tpf_key;
                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name]['CONTENTS_FILE']    = $tpf_file_name;
                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name]['ROLE_ONLY_FLAG']   = $row['ROLE_ONLY_FLAG'];
                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name]['VARS_LIST']        = $row['VARS_LIST'];
                        $ina_tpf_vars_list[$role_name][$tgt_file][$line_no][$tpf_var_name]['VAR_STRUCT_ANAL_JSON_STRING']   = $php_array;
                    }
                    if($fatal_error === true){
                        break;
                    }
                }
                if($fatal_error === true){
                    break;
                }
            }
            if($fatal_error === true){
                break;
            }
        }
        return $boolRet;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    // 処理内容
    //   グローバル管理の情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $in_gbl_var_name:      GBL変数名
    //   $in_gbl_key:           PKey格納変数
    //   $in_gbl_file_name:     ファイル格納変数
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getGBLVarsMaster($in_objMTS,$in_objDBCA,
                              $in_gbl_var_name,&$in_gbl_key,
                             &$in_errmsg,&$in_errdetailmsg){
        $sql = "SELECT                         \n" .
               "  GBL_VARS_NAME_ID             \n" .
               "FROM                           \n" .
               "  B_ANS_GLOBAL_VARS_MASTER     \n" .
               "WHERE                          \n" .
               "  VARS_NAME              = '" . $in_gbl_var_name . "' AND \n" .
               "  DISUSE_FLAG            = '0';\n";
    
        $in_gbl_key = "";
        $in_gbl_file_name = "";
        $in_errmsg = "";
        $in_errdetailmsg = "";
            
        $objQuery = $in_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $ina_child_playbooks = array();
        $row = $objQuery->resultFetch();
    
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            // 未登録の場合のエラー処理は呼び側にまかせる。
            unset($objQuery);
            return true;
        }
        $in_gbl_key       = $row["GBL_VARS_NAME_ID"];
    
        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    // 処理内容
    //   GBL変数がファイル管理に登録されているか判定
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $ina_gbl_vars_list:    GBL変数リスト
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkGBLVarsMasterReg( $in_objMTS,$in_objDBCA,
                                 &$ina_gbl_vars_list,
                                 &$in_errmsg,&$in_errdetailmsg){
        $boolRet   = true;
        $in_errmsg = "";
        $in_errdetailmsg = "";
        $fatal_error = false;
        // GBL変数がファイル管理に登録されているか判定
        foreach( $ina_gbl_vars_list as $role_name => $tgt_file_list ){
            foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                foreach( $line_no_list as $line_no => $gbl_var_name_list ){
                    foreach( $gbl_var_name_list as $gbl_var_name => $dummy ){
                        $gbl_key = "";
                        // GBL変数名からPkeyを取得する。
                        $db_errmsg = "";
                        $ret = $this->getGBLVarsMaster($in_objMTS,$in_objDBCA,$gbl_var_name,$gbl_key,$db_errmsg,$in_errdetailmsg);
                        if( $ret == false ){
                            // DBエラーを優先表示
                            $in_errmsg = $db_errmsg;
                            $boolRet = false;
                            $fatal_error = true;
                            break;
                        }
                        // GBL変数名が未登録の場合
                        if( $gbl_key == "" ){
                            if($in_errmsg != ""){
                                $in_errmsg = $in_errmsg . "\n";
                            }
                            $in_errmsg = $in_errmsg . AnsibleMakeMessage($in_objMTS,$this->GetRunMode(),
                                                                         "ITAANSIBLEH-ERR-6000019", array($role_name,
                                                                                                          $tgt_file,
                                                                                                          $line_no,
                                                                                                          $gbl_var_name));
                            $boolRet = false;
                            continue;
                        }
                        $ina_gbl_vars_list[$role_name][$tgt_file][$line_no][$gbl_var_name] = array();
                        $ina_gbl_vars_list[$role_name][$tgt_file][$line_no][$gbl_var_name]['CONTENTS_FILE_ID'] = $gbl_key;
                    }
                    if($fatal_error === true){
                        break;
                    }
                }
                if($fatal_error === true){
                    break;
                }
            }
            if($fatal_error === true){
                break;
            }
        }
        return $boolRet;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   Legacy/PioneerでアップロードされるPlaybook素材よの共通変数を抜き出す
    //   FileUploadColumn:checkTempFileBeforeMoveOnPreLoadイベント用
    //
    // パラメータ
    //   $inFilename:           アップロードされたデータが格納されているファイル名
    //   $outFilename:          抜き出した共通変数をJSON形式で退避するファイル名
    //   $FillterVars:          フィルター付き変数の抜き出し有無
    //
    // 戻り値
    //   checkTempFileBeforeMoveOnPreLoadイベントと同様
    ////////////////////////////////////////////////////////////////////////////////
    function CommonVarssAanalys($inFilename,$outFilename,$FillterVars=false) {
        global $g;
        $root_dir_path = $g['root_dir_path'];
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        // 共通モジュールをロード
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php' );

        global $g;

        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = null;

        // 子PlayBookの内容読込
        $playbookdataString = file_get_contents($inFilename);

        // ファイル内で定義されていたCPF変数を抜き出す
        $local_vars    = array();
        $varsLineArray = array();
        $varsArray     = array();
        // インベントリ追加オプションに定義されている変数を抜き出す。
        SimpleFillterVerSearch(DF_HOST_CPF_HED,$playbookdataString,$varsLineArray,$varsArray,$local_vars,$FillterVars);
        $cpf_vars_list = array();
        foreach( $varsLineArray as $no => $vars_info ){
            foreach( $vars_info as $line_no  => $var_name ){
                $cpf_vars_list['dummy']['Upload file'][$line_no][$var_name] = 0;
            }
        }
        
        // ファイル内で定義されていたTPF変数を抜き出す
        $local_vars    = array();
        $varsLineArray = array();
        $varsArray     = array();
        // インベントリ追加オプションに定義されている変数を抜き出す。
        SimpleFillterVerSearch(DF_HOST_TPF_HED,$playbookdataString,$varsLineArray,$varsArray,$local_vars,$FillterVars);
        $tpf_vars_list = array();
        foreach( $varsLineArray as $no => $vars_info ){
            foreach( $vars_info as $line_no  => $var_name ){
                $tpf_vars_list['dummy']['Upload file'][$line_no][$var_name] = 0;
            }
        }

        // ファイル内で定義されていたGBL変数を抜き出す
        $local_vars    = array();
        $varsLineArray = array();
        $varsArray     = array();
        // インベントリ追加オプションに定義されている変数を抜き出す。
        SimpleFillterVerSearch(DF_HOST_GBL_HED,$playbookdataString,$varsLineArray,$varsArray,$local_vars,$FillterVars);
        $gbl_vars_list = array();
        foreach( $varsLineArray as $no => $vars_info ){
            foreach( $vars_info as $line_no  => $var_name ){
                $gbl_vars_list['dummy']['Upload file'][$line_no][$var_name] = 0;
            }
        }

        $GBLVars = '1';
        $CPFVars = '2';
        $TPFVars = '3';
        $save_vars_list = array();
        $save_vars_list[$GBLVars] = array();
        $save_vars_list[$CPFVars] = array();
        $save_vars_list[$TPFVars] = array();
        $objLibs = new AnsibleCommonLibs(LC_RUN_MODE_VARFILE);

        if($boolRet === true){
            foreach( $cpf_vars_list as $role_name => $tgt_file_list ){
                foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                    foreach( $line_no_list as $line_no => $cpf_var_name_list ){
                        foreach( $cpf_var_name_list as $cpf_var_name => $dummy ){
                            $save_vars_list[$CPFVars][$cpf_var_name] = 0;
                        }
                    }
                }
            }
        }
        if($boolRet === true){
            foreach( $tpf_vars_list as $role_name => $tgt_file_list ){
                foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                    foreach( $line_no_list as $line_no => $tpf_var_name_list ){
                        foreach( $tpf_var_name_list as $tpf_var_name => $tpf_info ){
                            $save_vars_list[$TPFVars][$tpf_var_name] = 0;
                        }
                    }
                }
            }
        }
        if($boolRet === true){
            foreach( $gbl_vars_list as $role_name => $tgt_file_list ){
                foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                    foreach( $line_no_list as $line_no => $gbl_var_name_list ){
                        foreach( $gbl_var_name_list as $gbl_var_name => $dummy ){
                            $save_vars_list[$GBLVars][$gbl_var_name] = 0;
                        }
                    }
                }
            }
        }
        if($boolRet === true){
            $json = json_encode($save_vars_list);
            $path = $outFilename;
            $Ret = file_put_contents($path, $json);
            if($Ret === false) {
                $boolRet = false;
                $strErrMsg = $g['objMTS']->getSomeMessage('ITAANSIBLEH-ERR-6000018');
            }
            unset($objLibs);
        }
        unset($roleObj);

        if(strlen($strErrMsg) != 0) {
            $strErrMsg = str_replace('\n', "<BR>", $strErrMsg);
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    }
    ///////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   指定されたデータベースの全有効レコードを取得する。
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $in_sql:               SQL
    //   $ina_key:              登録レコードの配列のキー項目
    //   $ina_row:              登録レコードの配列
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:      エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function selectDBRecodes($in_objMTS,$in_objDBCA,$in_sql,$in_key,&$ina_row,
                            &$in_errmsg,&$in_errdetailmsg){
        $in_errmsg = "";
        $in_errdetailmsg = "";
            
        $objQuery = $in_objDBCA->sqlPrepare($in_sql);
        if($objQuery->getStatus()===false){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $in_sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $in_errmsg = $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_errdetailmsg = $in_errmsg;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $in_sql;
            $in_errdetailmsg = $in_errdetailmsg . "\n" . $objQuery->getLastError();
    
            unset($objQuery);
            return false;
        }
    
        $ina_row = array();
        while($row = $objQuery->resultFetch()) {
            $ina_row[$row[$in_key]] = $row;
        }
    
        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }
}
/////////////////////////////////////////////////////////////////////
// Web common database access class
/////////////////////////////////////////////////////////////////////
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
require_once ($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
class WebDBAccessClass extends CommonDBAccessCoreClass {

    /////////////////////////////////////////////////////////////////////
    // construct
    /////////////////////////////////////////////////////////////////////
    function __construct($db_model_ch,$objDBCA,$objMTS,$db_access_user_id){
        parent::__construct($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);
    }
    function CommnVarsUsedListUpdate($ContensID,$FileID,$VarsAry) {
        
        $MasterTableName = "B_ANS_COMVRAS_USLIST";
        $MemberAry    = array();
        $JNLMemberAry = array();
        $PkeyMember   = "";
        $this->ClearLastErrorMsg();
        $UsedPkeyList = array();

        $ret = parent::getTableDefinition($MasterTableName,$MemberAry,$JNLMemberAry,$PkeyMember);
        if($ret === false) {
            return false;
        }
        foreach($VarsAry as $VarID=>$VarNameList) {
            foreach($VarNameList as $VarName=>$dummy) {
                $sqlBody= sprintf("SELECT * FROM %s WHERE FILE_ID='%s' AND VRA_ID='%s' AND CONTENTS_ID='%s' AND VAR_NAME='%s' "
                                   ,$MasterTableName
                                   ,$FileID
                                   ,$VarID
                                   ,$ContensID
                                   ,$VarName);
                $arrayBind = array();
                $objQuery  = "";
                $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
                if($ret === false) {
                    return false;
                }
                $arrayValue  = $JNLMemberAry;
                $arrayConfig = $JNLMemberAry;
                $fetch_counter = $objQuery->effectedRowCount();
                if ($fetch_counter === 0) {
                    $action = 'INSERT';
                    $arrayValue['FILE_ID']                = $FileID;
                    $arrayValue['VRA_ID']                 = $VarID;
                    $arrayValue['CONTENTS_ID']            = $ContensID;
                    $arrayValue['VAR_NAME']               = $VarName;
                    $arrayValue['REVIVAL_FLAG']           = '0';
                    $arrayValue['DISUSE_FLAG']            = '0';
                    $arrayValue['LAST_UPDATE_USER']       = $this->GetDBAccessUserID();
                    $arrayValue['LAST_EXECUTE_TIMESTAMP'] = date('Y-m-d H:i:s');
                } elseif ($fetch_counter === 1) {
                    $row = $objQuery->resultFetch();
                    foreach($row as $col=>$dummy) {
                        $arrayValue[$col] = $row[$col];
                    }
                    if($row['DISUSE_FLAG'] == '0') {
                        $action = 'NONE';
                    } else {
                        $action = 'UPDATE';
                        $arrayValue['REVIVAL_FLAG']           = '0';
                        $arrayValue['DISUSE_FLAG']            = '0';
                        $arrayValue['LAST_UPDATE_USER']       = $this->GetDBAccessUserID();
                        $arrayValue['LAST_EXECUTE_TIMESTAMP'] = date('Y-m-d H:i:s');
                    }
                } else {
                    $message = sprintf("Duplicate error. (Table:%s  %s)",$MasterTableName,$sqlBody);
                    $this->SetLastErrorMsg(basename(__FILE__),__LINE__,$message);
                    return false;
                }

                switch($action) {
                case 'NONE':
                    $UsedPkeyList[] = $arrayValue[$PkeyMember];
                    break;
                case 'INSERT';
                    $PkeyID = '';
                    $ret = $this->dbaccessInsert($MasterTableName, $PkeyMember, $arrayConfig, $arrayValue, $PkeyID);
                    if($ret == false) {
                        return false;
                    }
                    // Pkey退避
                    $UsedPkeyList[] = $PkeyID;
                    break;
                case 'UPDATE':
                    // Pkey退避
                    $UsedPkeyList[] = $arrayValue[$PkeyMember];
                    $ret = $this->dbaccessUpdate($MasterTableName, $PkeyMember, $arrayConfig, $arrayValue);
                    if($ret == false) {
                        return false;
                    }
                    break;
                }
            }
        }
        if(count($UsedPkeyList) != 0) {
            // 不要レコードを抽出
            $sqlBody= sprintf("SELECT * FROM %s WHERE FILE_ID = %s AND CONTENTS_ID='%s' AND %s NOT IN (%s) "
                              ,$MasterTableName
                              ,$FileID
                              ,$ContensID
                              ,$PkeyMember
                              ,implode(",", $UsedPkeyList));
        } else {
            $sqlBody= sprintf("SELECT * FROM %s WHERE FILE_ID = %s AND CONTENTS_ID='%s' "
                              ,$MasterTableName
                              ,$FileID
                              ,$ContensID);
        }
        $arrayBind = array();
        $objQuery  = "";
        $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            return false;
        }

        // 不要なレコードを廃止
        while($row = $objQuery->resultFetch()) {
            $arrayValue  = $JNLMemberAry;
            $arrayConfig = $JNLMemberAry;
            foreach($row as $col=>$dummy) {
                $arrayValue[$col] = $row[$col];
            }
            if($row['DISUSE_FLAG'] == '1') {
                continue;
            } else {
                $arrayValue['REVIVAL_FLAG']           = '0';
                $arrayValue['DISUSE_FLAG']            = '1';
                $arrayValue['LAST_UPDATE_USER']       = $this->GetDBAccessUserID();
                $arrayValue['LAST_EXECUTE_TIMESTAMP'] = date('Y-m-d H:i:s');
                $ret = $this->dbaccessUpdate($MasterTableName, $PkeyMember, $arrayConfig, $arrayValue);
                if($ret == false) {
                    return false;
                }
            }
        }
        return true;
    }
    function CommnVarsUsedListDisuseSet($ContensID,$FileID,$DisuseFlg) {

        $MasterTableName = "B_ANS_COMVRAS_USLIST";
        $MemberAry    = array();
        $JNLMemberAry = array();
        $PkeyMember   = "";
        $this->ClearLastErrorMsg();

        $ret = parent::getTableDefinition($MasterTableName,$MemberAry,$JNLMemberAry,$PkeyMember);
        if($ret === false) {
            return false;
        }
        $sqlBody= sprintf("SELECT * FROM %s WHERE FILE_ID='%s' AND CONTENTS_ID='%s' "
                                   ,$MasterTableName
                                   ,$FileID
                                   ,$ContensID);
        $arrayBind = array();
        $objQuery  = "";
        $ret = $this->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            return false;
        }
        while($row = $objQuery->resultFetch()) {
            $arrayValue  = $JNLMemberAry;
            $arrayConfig = $JNLMemberAry;
            foreach($row as $col=>$dummy) {
                $arrayValue[$col] = $row[$col];
            }
            if($DisuseFlg == 'on') {
                // 廃止の場合、復活時の有効レコードフラグを設定
                if($arrayValue['DISUSE_FLAG'] == '0') {
                   $arrayValue['DISUSE_FLAG']            = '1';
                   $arrayValue['REVIVAL_FLAG']           = '1';
                } else {
                   continue;
                }
                $ret = $this->dbaccessUpdate($MasterTableName, $PkeyMember, $arrayConfig, $arrayValue);
                if($ret == false) {
                    return false;
                }
            } else {
                // 復活時の有効レコードフラグが設定されているレコードのみ復活
                if($arrayValue['DISUSE_FLAG'] == '1' && $arrayValue['REVIVAL_FLAG'] == '1') {
                    $arrayValue['DISUSE_FLAG']           = '0';
                    $arrayValue['REVIVAL_FLAG']          = '0';
                } else {
                    continue;
                }
                $ret = $this->dbaccessUpdate($MasterTableName, $PkeyMember, $arrayConfig, $arrayValue);
                if($ret == false) {
                    return false;
                }
            }
        }
        return true;
    }
}
class YAMLParse {
    private $lv_objMTS;
    private $lv_lasterrmsg;

    function __construct($objMTS){
        $this->lv_objMTS     = $objMTS;
        $this->lv_lasterrmsg = "";
    }

    function SetLastError($p1){
        $this->lv_lasterrmsg = $p1;
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }
    function Parse($yamlfile) {
        $this->lv_lasterrmsg = "";
        if(!extension_loaded('yaml')) {
            $msg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000107');
            $this->SetLastError($msg);
            return false;
        }
        $yaml = file_get_contents($yamlfile);
        $encode = mb_detect_encoding($yaml);
        switch($encode) {
        case "ASCII":
        case "UTF-8":
            break;
        default:
            // "UTF-8以外の文字文字コードが使用されています。";
            $msg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000108');
            $this->SetLastError($msg);
            return false;
        }
        $msg = "";
        $val = @yaml_parse_file($yamlfile);
        if($val === false) {
// AJAXでエラーになるのでコメント
//            $pares_error = error_get_last();
//            if(is_array($pares_error)) {
//                if(isset($pares_error["message"])) {
//                    $msg = sprintf("yaml parse text:%s %s",$yaml,$pares_error["message"]);
//                }
//            }
//            // "YAML解析で想定外のエラーが発生しました。"
//           $msg .= $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000109');
            // YAML解析で想定外のエラーのメッセージは呼び元で出している
            $this->SetLastError("");
            return false;
        } else {
            // yaml定義がない場合にnullが帰るので、空配列に設定
            if($val === null) {
                $val = array();
            }
            return $val;
        }
    }
    function StringParse($yamltext) {
        $this->lv_lasterrmsg = "";
        if(!extension_loaded('yaml')) {
            $msg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000107');
            $this->SetLastError($msg);
            return false;
        }
        $encode = mb_detect_encoding($yamltext);
        switch($encode) {
        case "ASCII":
        case "UTF-8":
            break;
        default:
            // "UTF-8以外の文字文字コードが使用されています。";
            $msg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000108');
            $this->SetLastError($msg);
            return false;
        }
        $msg = "";
        $val = @yaml_parse($yamltext);
        if($val === false) {
// AJAXでエラーになるのでコメント
//            $pares_error = error_get_last();
//            if(is_array($pares_error)) {
//              if(isset($pares_error["message"])) {
//                  $msg = sprintf("yaml parse text:%s %s",$yamltext,$pares_error["message"]);
//                  //$msg = sprintf("yaml parse text:%s ",$yamltext);
//              }
//          }
//
            $msg = sprintf("yaml parse text:%s \n",$yamltext);

            // "YAML解析で想定外のエラーが発生しました。"
            $msg .= $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000109');
            $this->SetLastError($msg);
            return false;
        } else {
            // yaml定義がない場合にnullが帰るので、空配列に設定
            if($val === null) {
                $val = array();
            }
            return $val;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   yamlファイルをパースする。
    //   ajaxを経由するとyaml_parse時のエラーが取得できないので
    //　 子プロセスでパースする。
    //
    // パラメータ
    //   $yaml_file: パースするyamlファイル
    //   $yaml_analys_array: パース結果
    //
    // 戻り値
    //   true:正常
    //   false:異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function yaml_file_parse($yaml_file,&$yaml_analys_array) {

        $yaml_analys_array = array();
        $this->lv_lasterrmsg = "";
        $error_msg = "";

        global $root_dir_path;    
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
    
        // PHPコマンドのパス取得
        $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");

        // 改行コードが付いている場合に取り除く
        $php_command = str_replace("\n","",$php_command);
    
        $yaml_analys_result_file = "/tmp/yaml_analys_result_file_" . getmypid();
        // yaml parse
        // コマンドライン用のエスケープ処理
        $yaml_file= escapeshellarg($yaml_file);
        $cmd = sprintf("%s %s/backyards/ansible_driver/ky_ansible_yaml_parse.php %s %s",$php_command,$root_dir_path,$yaml_file,$yaml_analys_result_file);
        $arry_out = array();
        $return_var = 0;
        exec($cmd,$arry_out,$return_var);
        if($return_var != 0) {
            foreach($arry_out as $line) {
                if(strlen(trim($line)) == 0) {
                    continue;
                }
                if(strlen($error_msg) != 0) {
                    $error_msg .= "\n";
                }
                $error_msg .= $line;
            }
            $this->SetLastError($error_msg);
            @unlink($yaml_analys_result_file);
            return false;
        } else {
            $json = file_get_contents($yaml_analys_result_file);
            $yaml_analys_array =  json_decode($json,true);
            @unlink($yaml_analys_result_file);
            return true;
        }
    }
}
class ValAutoRegInputParameterCheck {
    function __construct(){
    }
    function VarsInfo($in_col_type,$in_key_vars_link_id,$in_key_col_seq_comb_id,$in_key_assign_seq,
                                   $in_val_vars_link_id,$in_val_col_seq_comb_id,$in_val_assign_seq) {
        global $g;
        $retStrBody = true;
        switch($in_col_type){
        case '1':   // Value
            if((strlen($in_key_vars_link_id) != 0) ||
               (strlen($in_key_col_seq_comb_id) != 0) ||
               (strlen($in_key_assign_seq) != 0)) {
               $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-5000037",array("Value","Key"));
            }
            break;
        case '2':   // Key
            if((strlen($in_val_vars_link_id) != 0) ||
               (strlen($in_val_col_seq_comb_id) != 0) ||
               (strlen($in_val_assign_seq) != 0)) {
               $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-5000037",array("Key","Value"));
            }
            break;
        }
        return $retStrBody;
    }
}
class InventryFileAddOptionContlorl {
    private $objDBCA;
    function __construct($objDBCA){
        $this->objDBCA     = $objDBCA;
    }
    function InventryFileAddOptionCheckFormat($in_string,&$out_yaml_array,&$error_line) {
        $out_yaml_array = array();
        $SplitVarKageName = array();
        // インベントリファイル追加オプションをYAML形式を検査する
        $String = $in_string;
        $out_yaml_array = explode("\n", $String);
        $error_line = 0;
        foreach($out_yaml_array as $record) {
            $error_line++;
            $VarKageName  = trim($record);
            if(empty($VarKageName)){ // 空文字列 正常
                // スペースを取り除くと空の時
                continue;
            }
            $ret = preg_match("/^(\S)+(\s)*:(\s)+(\S)/", $record);
            if($ret !== 1){
                // 式が正しくない
                return false;
            }
        }
        return true;
    }
    function getVariablesDefinedInDeviceList($PHOLinkTbl,&$la_InventryFileAddOptionAry) {

        $la_InventryFileAddOptionAry = array();
        
        if($PHOLinkTbl == "B_ANSIBLE_LNS_PHO_LINK") {
            $sqlUtnBody = "SELECT
                             TAB_A.OPERATION_NO_UAPK,
                             TAB_A.PATTERN_ID,
                             TAB_A.SYSTEM_ID,
                             TAB_B.IP_ADDRESS,
                             TAB_B.HOSTS_EXTRA_ARGS
                           FROM 
                             B_ANSIBLE_LNS_PHO_LINK               TAB_A
                             LEFT JOIN C_STM_LIST                 TAB_B ON ( TAB_A.SYSTEM_ID         = TAB_B.SYSTEM_ID )
                             LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_C ON ( TAB_A.PATTERN_ID        = TAB_C.PATTERN_ID )
                             LEFT JOIN C_OPERATION_LIST           TAB_D ON ( TAB_A.OPERATION_NO_UAPK = TAB_D.OPERATION_NO_UAPK )
                             LEFT JOIN B_ANSIBLE_LNS_PATTERN_LINK TAB_E ON ( TAB_A.PATTERN_ID         = TAB_E.PATTERN_ID )
                             LEFT JOIN B_ANSIBLE_LNS_PLAYBOOK     TAB_F ON ( TAB_E.PLAYBOOK_MATTER_ID = TAB_F.PLAYBOOK_MATTER_ID )
                           WHERE
                             TAB_A.DISUSE_FLAG = '0' AND
                             TAB_B.DISUSE_FLAG = '0' AND
                             TAB_C.DISUSE_FLAG = '0' AND
                             TAB_D.DISUSE_FLAG = '0' AND
                             TAB_E.DISUSE_FLAG = '0' AND
                             TAB_F.DISUSE_FLAG = '0'";
        } else {
            $sqlUtnBody = "SELECT
                             TAB_A.OPERATION_NO_UAPK,
                             TAB_A.PATTERN_ID,
                             TAB_A.SYSTEM_ID,
                             TAB_B.IP_ADDRESS,
                             TAB_B.HOSTS_EXTRA_ARGS,
                             TAB_E.ROLE_PACKAGE_ID,
                             TAB_F.ROLE_PACKAGE_NAME
                           FROM
                             B_ANSIBLE_LRL_PHO_LINK                 TAB_A
                             LEFT JOIN C_STM_LIST                   TAB_B ON ( TAB_A.SYSTEM_ID         = TAB_B.SYSTEM_ID )
                             LEFT JOIN E_ANSIBLE_LRL_PATTERN        TAB_C ON ( TAB_A.PATTERN_ID        = TAB_C.PATTERN_ID )
                             LEFT JOIN C_OPERATION_LIST             TAB_D ON ( TAB_A.OPERATION_NO_UAPK = TAB_D.OPERATION_NO_UAPK )
                             LEFT JOIN B_ANSIBLE_LRL_PATTERN_LINK   TAB_E ON ( TAB_A.PATTERN_ID        = TAB_E.PATTERN_ID )
                             LEFT JOIN B_ANSIBLE_LRL_ROLE_PACKAGE   TAB_F ON ( TAB_E.ROLE_PACKAGE_ID   = TAB_F.ROLE_PACKAGE_ID )
                           WHERE
                             TAB_A.DISUSE_FLAG = '0' AND
                             TAB_B.DISUSE_FLAG = '0' AND
                             TAB_C.DISUSE_FLAG = '0' AND
                             TAB_D.DISUSE_FLAG = '0' AND
                             TAB_E.DISUSE_FLAG = '0' AND
                             TAB_F.DISUSE_FLAG = '0'";
        }

        $objQueryUtn = $this->objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            return $objQueryUtn->getLastError();
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            return $objQueryUtn->getLastError();
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $la_InventryFileAddOptionAry[] = $row;
        }
        unset($objQueryUtn);
        return true;
    }
}
    function getMasterAccessAuth(&$lva_OpeAccessAuth_list,&$lva_HostAccessAuth_list,&$lva_PatternAccessAuth_list) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;
        $sqlAry          = array();
        $sqlKeyName      = array();
        $resultDataAry   = array();
        $sqlAry[] = "SELECT %s,%s,ACCESS_AUTH  FROM C_OPERATION_LIST";
        $sqlAry[] = "SELECT %s,%s,ACCESS_AUTH  FROM C_STM_LIST";
        $sqlAry[] = "SELECT %s,%s,ACCESS_AUTH  FROM C_PATTERN_PER_ORCH";
        $sqlKeyName[] = "OPERATION_NAME";
        $sqlKeyName[] = "HOSTNAME";
        $sqlKeyName[] = "PATTERN_NAME";
        $sqlKeyId[] = "OPERATION_NO_UAPK";
        $sqlKeyId[] = "SYSTEM_ID";
        $sqlKeyId[] = "PATTERN_ID";
        $lva_OpeAccessAuth_list     = array();
        $lva_HostAccessAuth_list    = array();
        $lva_PatternAccessAuth_list = array();
        $resultDataAry[] = &$lva_OpeAccessAuth_list;
        $resultDataAry[] = &$lva_HostAccessAuth_list;
        $resultDataAry[] = &$lva_PatternAccessAuth_list;

        foreach($sqlAry as $no=>$sql) {

            // SQL準備
            $sql = sprintf($sql,$sqlKeyId[$no],$sqlKeyName[$no]);
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                $in_error_msg  = $msgstr;
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                return false;
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                $in_error_msg  = $msgstr;
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                unset($objQuery);
                return false;
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                $AccessAuthAry = array();
                if($row["ACCESS_AUTH"] != "") {
                    $AccessAuthAry = explode(",",$row["ACCESS_AUTH"]);
                }
                $resultDataAry[$no][$row[$sqlKeyId[$no]]] = array("NAME"=>$row[$sqlKeyName[$no]],
                                                                  "ACCESS_AUTH"=>$AccessAuthAry);
            }
        }
        return true;
    }
    function getCMDBMenuMaster(&$lva_CMDBMenuColumn_list,&$lva_CMDBMenu_list) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;
        $lva_CMDBMenuColumn_list    = array();
        $lva_CMDBMenu_list          = array();
        // SQL準備
        $sql = " SELECT TAB_D.TABLE_NAME, "
             . "        TAB_E.COL_NAME,   "
             . "        TAB_E.COL_TITLE,  "
             . "        CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME) MENU_NAME "
             . " FROM B_CMDB_MENU_LIST TAB_A "
             . " LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID) "
             . " LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID) "
             . " LEFT JOIN B_CMDB_MENU_TABLE TAB_D ON (TAB_A.MENU_ID = TAB_D.MENU_ID) "
             . " LEFT JOIN B_CMDB_MENU_COLUMN TAB_E ON (TAB_A.MENU_ID = TAB_E.MENU_ID) ";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $lva_CMDBMenuColumn_list[$row['TABLE_NAME']][$row['COL_NAME']] = $row['COL_TITLE'];
            $lva_CMDBMenu_list[$row['TABLE_NAME']] = $row['MENU_NAME'];
        }
        return true;
    }
    function  chkSubstitutionValueListRecodedifference($BefInfo,&$AftInfo) {
        $diff       = false;
        $befFileDel = false;
        $AftFileCpy = false;
        if(($AftInfo['COL_CLASS'] == 'FileUploadColumn') &&
           ($AftInfo['REG_TYPE']  == 'Value')) {
            $AftInfo['VARS_ENTRY_FILE'] = $AftInfo['VARS_ENTRY'];
            $AftInfo['VARS_ENTRY']      = "";
        } else {
            $AftInfo['VARS_ENTRY_FILE'] = "";
        }

        if(($BefInfo['ACCESS_AUTH']           != $AftInfo['ACCESS_AUTH']) ||
            ($BefInfo['SENSITIVE_FLAG']        != $AftInfo['SENSITIVE_FLAG']) ||
            ($BefInfo['VARS_ENTRY_FILE']       != $AftInfo['VARS_ENTRY_FILE']) ||
            ($BefInfo['VARS_ENTRY_FILE_MD5']   != $AftInfo['COL_FILEUPLOAD_MD5']) ||
            ($BefInfo['VARS_ENTRY']            != $AftInfo['VARS_ENTRY'])) {
            $diff = true;
        }
        if($diff === true) {
           // 代入値管理の具体値がファイルの場合
           if(($BefInfo['VARS_ENTRY_FILE']       != $AftInfo['VARS_ENTRY_FILE']) || 
              ($BefInfo['VARS_ENTRY_FILE_MD5']   != $AftInfo['COL_FILEUPLOAD_MD5'])) {
               if($BefInfo['VARS_ENTRY_FILE']       != "") {
                   $befFileDel = true;
               }
           }
           // パラメータシートの具体値がファイルの場合
           if(($BefInfo['VARS_ENTRY_FILE']       != $AftInfo['VARS_ENTRY_FILE'])       ||
              ($BefInfo['VARS_ENTRY_FILE_MD5']   != $AftInfo['COL_FILEUPLOAD_MD5'])) {
               if(($AftInfo['VARS_ENTRY_FILE']       != "") &&
                  ($AftInfo['REG_TYPE']              == 'Value')) {
                   $AftFileCpy = true;
               }
           }
        }
        return [$diff,$befFileDel,$AftFileCpy];
    }
    function chkSpecificsValueInput($arrayRegData, $arrayVariant, $objMTS, $UpLoadFile, $DelFlag ,$ordMode, $tgtTableName) {
        global $g;

        $retBool             = false;
        $boolSystemErrorFlag = false;
        $retStrBody          = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55294");

        $strModeId = "";
        if(array_key_exists("TCA_PRESERVED", $arrayVariant)) {
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])) {
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        $modeValue_sub = "";
        if($strModeId == "DTUP_singleRecDelete") {
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
        }

        $BefVarsEntry       = isset($arrayVariant['edit_target_row']['VARS_ENTRY'])?
                                    $arrayVariant['edit_target_row']['VARS_ENTRY']:null;
        $BefVarsEntryFile   = isset($arrayVariant['edit_target_row']['VARS_ENTRY_FILE'])?
                                    $arrayVariant['edit_target_row']['VARS_ENTRY_FILE']:null;
        $BefSensitiveFlag   = isset($arrayVariant['edit_target_row']['SENSITIVE_FLAG'])?
                                    $arrayVariant['edit_target_row']['SENSITIVE_FLAG']:null;
        $AftVarsEntry       = array_key_exists('VARS_ENTRY',$arrayRegData)?
                                 $arrayRegData['VARS_ENTRY']:null;
        $AftVarsEntryFile   = array_key_exists('VARS_ENTRY_FILE',$arrayRegData)?
                                 $arrayRegData['VARS_ENTRY_FILE']:null;
        $AftSensitiveFlag   = array_key_exists('SENSITIVE_FLAG',$arrayRegData)?
                                 $arrayRegData['SENSITIVE_FLAG']:null;
        $AftUpLoadFile      = array_key_exists($UpLoadFile,$arrayRegData)?
                                 $arrayRegData[$UpLoadFile]:null;
        $AftDelFlag         = array_key_exists($DelFlag,$arrayRegData)?
                                 $arrayRegData[$DelFlag]:null;
        // Excelからの場合、該当レコードの具体値にファイルがアップロードされているか確認
        if($ordMode == 1) {
            // 更新・復活の場合か判定
            if(($strModeId == "DTUP_singleRecUpdate") ||
               (($strModeId == "DTUP_singleRecUpdate") && ($modeValue_sub == "off"))) {
                $Pkey   = isset($arrayVariant['edit_target_row']['ASSIGN_ID'])?
                                $arrayVariant['edit_target_row']['ASSIGN_ID']:null;
                $query =  "SELECT                                             "
                         ."  TBL_A.VARS_ENTRY_FILE                            "
                         ."FROM                                               "
                         ."  $tgtTableName  TBL_A                             "
                         ."WHERE                                              "
                         ."  TBL_A.ASSIGN_ID    = :ASSIGN_ID                  ";
                $aryForBind = array();
                $aryForBind['ASSIGN_ID'] = $Pkey;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    while($row = $objQuery->resultFetch() ) {
                        if( $row['VARS_ENTRY_FILE'] != '') {
                            // 該当レコードの具体値にファイルが設定されている場合、Excelからの更新は許可しない
                            $retStrBody = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55296");
                            $retBool = false;
                            $boolSystemErrorFlag = false;
                            return [$retBool,$boolSystemErrorFlag,$retStrBody];
                        }
                    }
                }else{
                    $retStrBody = "DB Access error file:" . basename(__FILE__) . " line:" . __LINE__;
                    web_log($retStrBody);
                    $retBool             = false;
                    $boolSystemErrorFlag = true;
                    $retStrBody          = "";
                    return [$retBool,$boolSystemErrorFlag,$retStrBody];
                }
                unset($retArray);
            }
        }
        // 0:[ブラウザからの新規登録
        // 1:[EXCEL]からの新規登録
        // 2:[CSV]からの新規登録
        // 3:[JSON]からの新規登録
        // 4:[ブラウザからの新規登録(SQLトランザクション無し)
        switch($ordMode) {
        case 0:
            switch($strModeId) {
            case "DTUP_singleRecRegister":
            case "DTUP_singleRecUpdate":
                $FileSet = false;
                $StrSet  = false;
                // ファイル削除がチェックされているか判定
                if($AftDelFlag == "on") {
                    $FileSet = false;
                } else {
                    // 新規ファイルがアップロードされているか判定
                    if($AftUpLoadFile == "") {
                        // 変更前にファイルがアップロードされているか判定
                        if($BefVarsEntryFile == "") {
                            $FileSet = false;
                        } else {
                            $FileSet = true;
                        }
                    } else {
                        $FileSet = true;
                    }
                }
                // 具体値が設定されているか判定
                if($AftVarsEntry     == "") {
                    // SENSITIVE設定がONか判定
                    if($AftSensitiveFlag == 2) {
                         // 変更前のSENSITIVE設定がONか判定
                        if($BefSensitiveFlag == 2) {
                            // 変更前の具体値が設定されているか判定
                            if($BefVarsEntry == "") {
                                $StrSet  = false;
                            } else {
                                $StrSet  = true;
                            }
                        } else {
                            // 具体値は空白になる
                            $StrSet  = false;
                        }
                    } else {
                        $StrSet  = false;
                    }
                } else {
                    $StrSet  = true;
                }
                if(($FileSet === true) && ($StrSet === true)) {
                    return [$retBool,$boolSystemErrorFlag,$retStrBody];
                }
                break;
            case "DTUP_singleRecDelete":
                if($modeValue_sub == "off") {
                    if(($BefVarsEntry     != "") &&
                       ($BefVarsEntryFile != "")) {
                        $retStrBody = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55295");
                        return [$retBool,$boolSystemErrorFlag,$retStrBody];
                    }
                }
                break;
            default:
                break;
            }
            break;
        case 1:
        case 2:
        case 3:
        case 4:
            switch($strModeId) {
            case "DTUP_singleRecRegister":
                if(($AftVarsEntry     != "") &&
                   ($AftVarsEntryFile != "")) {
                    return [$retBool,$boolSystemErrorFlag,$retStrBody];
                }
                break;
            case "DTUP_singleRecUpdate":
                $FileSet = false;
                $StrSet  = false;
                // ファイル削除がチェックされているか判定
                if($AftDelFlag == "on") {
                    $FileSet = false;
                } else {
                    // 新規ファイルがアップロードされているか判定
                    if($AftUpLoadFile == "") {
                        // 変更前にファイルがアップロードされているか判定
                        if($BefVarsEntryFile == "") {
                            $FileSet = false;
                        } else {
                            $FileSet = true;
                        }
                    } else {
                        $FileSet = true;
                    }
                }
                // 具体値が設定されているか判定
                if($AftVarsEntry     == "") {
                    // SENSITIVE設定がONか判定
                    if($AftSensitiveFlag == 2) {
                         // 変更前のSENSITIVE設定がONか判定
                        if($BefSensitiveFlag == 2) {
                            // 変更前の具体値が設定されているか判定
                            if($BefVarsEntry == "") {
                                $StrSet  = false;
                            } else {
                                $StrSet  = true;
                            }
                        } else {
                            // 具体値は空白になる
                            $StrSet  = false;
                        }
                    } else {
                        $StrSet  = false;
                    }
                } else {
                    $StrSet  = true;
                }
                if(($FileSet === true) && ($StrSet === true)) {
                    return [$retBool,$boolSystemErrorFlag,$retStrBody];
                }
                break;
            case "DTUP_singleRecDelete":
                if($modeValue_sub == "off") {
                    if(($BefVarsEntry     != "") &&
                       ($BefVarsEntryFile != "")) {
                        $retStrBody = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55295");
                        return [$retBool,$boolSystemErrorFlag,$retStrBody];
                    }
                }
                break;
            default:
                break;
            }
            break;
        default:
            $retStrBody = "Illegal value for ModeType. file:" . basename(__FILE__) . " line:" . __LINE__;
            web_log($retStrBody);
            $retBool             = false;
            $boolSystemErrorFlag = true;
            $retStrBody          = "";
            return [$retBool,$boolSystemErrorFlag,$retStrBody];
            // break;
        }
        $retBool             = true;
        $boolSystemErrorFlag = false;
        $retStrBody          = "";
        return [$retBool,$boolSystemErrorFlag,$retStrBody];
    }
    function getInputDataTempDir($EcecuteNo, $DriverName) {
        global $root_dir_path;
        $ary["BASE_DIR"] = sprintf("%s/temp/ansible_driver_temp",$root_dir_path);
        $ary["DIR_NAME"] = sprintf("%s/temp/ansible_driver_temp/%s_%010s",$root_dir_path, $DriverName, $EcecuteNo);
        return $ary;
    }

?>
