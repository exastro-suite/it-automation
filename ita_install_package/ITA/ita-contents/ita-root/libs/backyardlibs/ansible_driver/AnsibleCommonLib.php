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
////////////////////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・Ansible 共通モジュール
//
//   F0001  getDBLegacyRoleCopyMaster 
//   F0002  chkCPFVarsMasterReg
//
////////////////////////////////////////////////////////////////////////////////////
class AnsibleCommonLibs {
    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   copyファイルの情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $in_cpf_var_name:      copy変数名
    //   $in_cpf_key:           PKey格納変数
    //   $in_cpf_file_name:     copyファイル格納変数
    //   $in_errmsg:            エラーメッセージ格納
    //   $in_errdetailmsg:         エラー詳細格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBLegacyRoleCopyMaster($in_objMTS,$in_objDBCA,
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
            // copyファイルが未登録の場合のエラー処理は呼び側にまかせる。
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
    // F0002
    // 処理内容
    //   copy変数がファイル管理に登録されているか判定
    //
    // パラメータ
    //   $in_objMTS:            メッセージハンドル
    //   $in_objDBCA:           DBハンドル
    //   $ina_cpf_vars_list:     copy変数リスト
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
        // copyモジュールで使用している埋め込み変数がファイル管理に登録されているか判定
        foreach( $ina_cpf_vars_list as $role_name => $tgt_file_list ){
            foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                foreach( $line_no_list as $line_no => $cpf_var_name_list ){
                    foreach( $cpf_var_name_list as $cpf_var_name => $dummy ){
                        $cpf_key = "";
                        $cpf_file_name = "";
                        // copy変数名からコピーファイル名とPkeyを取得する。
                        $db_errmsg = "";
                        $ret = $this->getDBLegacyRoleCopyMaster($in_objMTS,$in_objDBCA,$cpf_var_name,$cpf_key,$cpf_file_name,$db_errmsg,$in_errdetailmsg);
                        if( $ret == false ){
                            // DBエラーを優先表示
                            $in_errmsg = $db_errmsg;
                            $boolRet = false;
                            $fatal_error = true;
                            break;
                        }
                        // copy変数名が未登録の場合
                        if( $cpf_key == "" ){
                            if($in_errmsg != ""){
                                $in_errmsg = $in_errmsg . "\n";
                            }
                            $in_errmsg = $in_errmsg . $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90090",
                                                                                                array($tgt_file,
                                                                                                      $line_no,
                                                                                                      $cpf_var_name));
                            $boolRet = false;
                            continue;
                        }
                        else{
                            // copyファイル名が未登録の場合
                            if($cpf_file_name == "" ){
                                if($in_errmsg != ""){
                                    $in_errmsg = $in_errmsg . "\n";
                                }
                                $in_errmsg = $in_errmsg . $in_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90091",
                                                                                                   array($tgt_file,
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
};
?>
