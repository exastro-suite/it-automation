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
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibleMakeMessage.php");
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/FileUploadColumnFileAccess.php");
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・roleディレクトリの内容をチェックする。
//
//  C0001  rolesディレクトリ解析
//    F0001  __construct
//    F0002  getvarname
//    F0002-1 getglobalvarname
//    F0003  getrolename
//    F0004  getlasterror
//    F0005  ZipextractTo
//    F0006  chkRolesDirectory
//    F0007  chkRoleDirectory
//    F0008  chkRoleSubDirectory
//    F0009  chkRoleFiles
//    F0011  readTranslationFile
//    F0012  chkTranslationVars
//
//  Class外
//    F0010  getDBGlobalVarsMaster
//    F0013  getFileList
//
//  C0002  デフォルト定義ファイルの変数抽出
//    F1001  __construct
//    F1002  chkVarsStruct
//    F1003  chkallVarsStruct
//    F1004  VarsStructErrmsgEdit
//    F1005  allVarsStructErrmsgEdit
//    F1006  chkDefVarsListPlayBookVarsList
//    F1007  margeDefaultVarsList
//    F1008  FirstAnalysis
//    F1009  VarTypeAnalysis
//    F1010  VarPosAnalysis
//    F1011  ParentVarAnalysis
//    F1012  MiddleAnalysis
//    F1013  CreateTempDefaultsVarsFile
//    F1014  LoadSpycModule
//    F1015  LastAnalysis
//    F1016  chkStandardVariable
//    F1017  chkMultiValueVariable
//    F1018  chkMultiValueVariableSub
//    F1019  chkMultiArrayVariable
//    F1020  MakeMultiArrayToDiffMultiArray
//    F1021  MultiArrayDiff
//    F1022  InnerArrayDiff
//    F1023  is_assoc
//    F1024  is_stroc
//    F1025  MakeMultiArrayToFirstVarChainArray
//    F1026  MakeMultiArrayToLastVarChainArray
//    F1027  chkDefVarsListPlayBookGlobalVarsList
//    F1028  readTranslationFile
//    F1029  chkTranslationTableVarsCombination
//    F1030  TranslationTableCombinationErrmsgEdit
//    F1031  ApplyTranslationTable
//    F1032  SetRunModeVarFile
//    F1033  GetRunModeVarFile
//
//  C0003  変数定義を解析する。
//
/////////////////////////////////////////////////////////////////////////////////////////
class CheckAnsibleRoleFiles {
    // role名一覧
    private $lva_rolename;
    // role変数名一覧
    private $lva_varname;
    // role変数取得有無
    private $lv_get_rolevar;
    // エラーメッセージ退避                    
    private $lv_lasterrmsg;
    //
    private $lv_objMTS;
    // roleグローバル変数名一覧
    private $lva_globalvarname;
    
    private $lva_msg_role_pkg_name;

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   コンストラクタ
    // パラメータ
    //   $in_errorsave:    エラーメッセージ退避有無(ZIPファイルUpload時)
    //                     true: Yes   false: no
    //   $in_errorlogfile: ログ出力先ファイル(role実行時のerror.logファイル)
    //                     不要の場合はnullを設定
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function __construct(&$in_objMTS){
        $this->lv_lasterrmsg   = array();
        $this->lv_objMTS       = $in_objMTS;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   zipファイル内で定義されているロール変数名を取得
    // パラメータ
    //   なし
    // 戻り値
    //   ロール変数名配列
    //   $lva_varname[role名][変数名]=0
    ////////////////////////////////////////////////////////////////////////////////
    function getvarname(){
        return $this->lva_varname;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002-1
    // 処理内容
    //   zipファイル内で定義されているグローバル変数名を取得
    // パラメータ
    //   なし
    // 戻り値
    //   グローバル変数名配列
    //   $lva_globalvarname[role名][グローバル変数名]=0
    ////////////////////////////////////////////////////////////////////////////////
    function getglobalvarname(){
        return $this->lva_globalvarname;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   zipファイル内で定義されているロール名を取得
    // パラメータ
    //   なし
    // 戻り値
    //   role名配列
    //   $lva_rolename[role名]
    ////////////////////////////////////////////////////////////////////////////////
    function getrolename(){
        return $this->lva_rolename;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    // 処理内容
    //   エラーメッセージ取得
    // パラメータ
    //   なし
    // 戻り値
    //   エラーメッセージ
    ////////////////////////////////////////////////////////////////////////////////
    function getlasterror(){
        return $this->lv_lasterrmsg;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   zipファイルを展開する
    // パラメータ
    //   $in_zip_path:    zipファイル
    //   $in_dist_path:   zipファイル展開先ディレクトリ
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function ZipextractTo($in_zip_path,$in_dist_path){
        $zip = new ZipArchive();
        if($zip->open($in_zip_path) === true){
            $zip->extractTo($in_dist_path);
            $zip->close();
        }
        else{
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70005");
            $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   rolesディレクトリ配下のディレクトリとファイルが妥当かチェックする。
    // パラメータ
    //   $in_dir:             rolesディレクトリがあるディレクトリ
    //   $in_getrolevar:      ロール変数取得有無
    //                          false: ロール変数取得しない (default値)
    //                          true:  ロール変数取得する
    //   $ina_system_vars:    システム変数リスト(機器一覧)
    //
    // #1081 Append Start
    //   $in_role_pkg_name:   ロールパッケージ名
    //   $ina_def_vars_list:  各ロールのデフォルト変数ファイル内に定義されている
    //                        変数名のリスト 
    //                          一般変数
    //                            $ina_def_vars_list[ロール名][変数名]=0
    //                          配列変数
    //                            $ina_def_vars_list[ロール名][配列数名]=array([子供変数名]=0,...)
    //
    //   $ina_err_vars_list:  ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                            $in_err_vars_list[変数名][ロールパッケージ名][ロール名]
    // #1081 Append End
    //
    // #1182 Append
    //   $ina_def_varsval_list:  
    //                        各ロールのデフォルト変数ファイル内に定義されている変数名の具体値リスト
    //                          一般変数
    //                            $ina_def_vars_val_list[ロール名][変数名][0]=具体値
    //                          複数具体値変数
    //                            $ina_def_vars_val_list[ロール名][変数名][1]=array(1=>具体値,2=>具体値....)
    //                          配列変数
    //                            $ina_def_vars_val_list[ロール名][変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //
    //   $in_get_copyvar:     PlaybookからCPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_copyvars_list:  Playbookで使用しているCPF変数のリスト
    //                           $ina_copyvars_list[ロール名][変数名]=1
    //   $in_get_tpfvar:      PlaybookからTPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_tpfvars_list:   Playbookで使用しているTPF変数のリスト
    //                           $ina_tpfvars_list[ロール名][変数名]=1
    //   $ina_ITA2User_var_list  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list  読替表の変数リスト　ユーザ変数=>ITA変数
    //   $ina_comb_err_vars_list 読替変数と任意変数の組合せが一意でないリスト 
    //
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkRolesDirectory($in_dir,
                               $ina_system_vars,
                               $in_role_pkg_name,
                              &$ina_def_vars_list,
                              &$ina_err_vars_list,
                              &$ina_def_varsval_list,     
                              &$ina_def_array_vars_list,
                               $in_get_copyvar,          
                              &$ina_copyvars_list,       
                               $in_get_tpfvar,
                              &$ina_tpfvars_list,
                              &$ina_ITA2User_var_list,  
                              &$ina_User2ITA_var_list,  
                              &$ina_comb_err_vars_list, 
                               $in_get_rolevar=false)   
    {

        // パッケージ名の退避
        if($in_role_pkg_name == ""){
            $this->lva_msg_role_pkg_name = "Current roll package";
        }
        else{
            $this->lva_msg_role_pkg_name = $in_role_pkg_name;
        }

        // Playbookで使用しているCPF/TPF変数のリスト 初期化
        $ina_copyvars_list = array(); 
        $ina_tpfvars_list  = array();

        //デフォルト変数定義一覧 初期化
        $ina_def_vars_list = array();

        // role名一覧 初期化
        $this->lva_rolename = array();

        // role変数名一覧 初期化
        $this->llva_varname = array();

        // roleグローバル変数名一覧
        $this->lva_globalvarname = array();

        // role変数取得有無
        $this->lv_get_rolevar = $in_get_rolevar;

        // roleディレクトリ抽出
        $files = array();
        $ret = RoleDirectoryAnalysis($in_dir,$files,$this->lv_objMTS,$errormsg);
        if($ret === false)
        {
            $this->SetLasteError(basename(__FILE__),__LINE__,$errormsg);
            return(false);
        }

        $result_code = true;
        $roles_flg   = false;

        foreach ($files as $fullpath=>$role_name){
            if(is_dir($fullpath)){
                $roles_flg = true;

                /////////////////////////////////////////////////////
                // rolesディレクトリ配下のroleディレクトリをチェック
                /////////////////////////////////////////////////////
                $ret = $this->chkRoleDirectory($in_dir,
                                               $fullpath,
                                               $ina_system_vars,
                                               $in_role_pkg_name,
                                               $role_name,   // APPDEN
                                               $ina_def_vars_list,
                                               $ina_def_varsval_list,   
                                               $ina_def_array_vars_list,
                                               $in_get_copyvar,       
                                               $ina_copyvars_list,   
                                               $in_get_tpfvar,
                                               $ina_tpfvars_list,
                                               $ina_ITA2User_var_list, 
                                               $ina_User2ITA_var_list 
                                               );                    
                if($ret === false){
                    return(false);
                }
            }
        }

        $chkObj = new DefaultVarsFileAnalysis($this->lv_objMTS);

        $ina_comb_err_vars_list = array();
        $ITA2User_var_list      = array();
        $User2ITA_var_list      = array();
        $ITA2User_var_list[$in_role_pkg_name]=$ina_ITA2User_var_list;
        $User2ITA_var_list[$in_role_pkg_name]=$ina_User2ITA_var_list;
        // 読替変数と任意変数の組合せを確認する。
        $ret = $chkObj->chkTranslationTableVarsCombination($ITA2User_var_list, $User2ITA_var_list,$ina_comb_err_vars_list);
        if($ret === false){
            // エラーメッセージは呼び元で編集
            return(false);
        }
        
        // 読替表を元に変数名を更新
        $chkObj->ApplyTranslationTable($ina_def_vars_list, $ina_User2ITA_var_list);
        $chkObj->ApplyTranslationTable($ina_def_array_vars_list, $ina_User2ITA_var_list);
        $chkObj->ApplyTranslationTable($ina_def_varsval_list, $ina_User2ITA_var_list);

//  Playbookのみで使用している変数があった場合のエラー処理は無くす
//        // ロールパッケージ内のPlaybookで定義している変数がdefalte変数定義ファイルにあるか
//        // ITA独自変数はチェック対象外にする。
//        $msgstr = "";
//        $ret = $chkObj->chkDefVarsListPlayBookVarsList( $this->lva_varname, $ina_def_vars_list ,$ina_def_array_vars_list, $msgstr ,$ina_system_vars);
//
//        if($ret === false){
//            unset($chkObj);
//            $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
//            return(false);
//        }

        // ロールパッケージ内のデフォルト変数で定義されている変数の構造を確認
        $ret = $chkObj->chkVarsStruct($ina_def_vars_list, $ina_def_array_vars_list, $ina_err_vars_list);

        if($ret === false){
            unset($chkObj);

            // エラーメッセージは呼び元で編集
            return(false);
        }

        return(true);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    // 処理内容
    //   rolesディレクトリ配下のroleディレクトリとファイルが妥当かチェックする。
    // パラメータ
    //   $in_base_dir:        ベースディレクトリ
    //   $in_dir:             rolesディレクトリ
    //   $ina_system_vars     システム変数リスト(機器一覧)
    //   $in_role_pkg_name:   ロールパッケージ名
    //   $in_role_name:       ロール名
    //   $ina_def_vars_list:  各ロールのデフォルト変数ファイル内に定義されている
    //                        変数名のリスト 
    //                          一般変数
    //                            $ina_def_vars_list[ロール名][変数名]=0
    //                          配列変数
    //                            $ina_def_vars_list[ロール名][配列数名]=array([子供変数名]=0,...)
    // #1081 Append End
    // #1182 Append
    //   $ina_def_varsval_list:  
    //                        各ロールのデフォルト変数ファイル内に定義されている変数名の具体値リスト
    //                          一般変数
    //                            $ina_def_vars_val_list[ロール名][変数名][0]=具体値
    //                          複数具体値変数
    //                            $ina_def_vars_val_list[ロール名][変数名][1]=array(1=>具体値,2=>具体値....)
    //                          配列変数
    //                            $ina_def_vars_val_list[ロール名][変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //   $in_base_dir:        zipファイル解凍ディレクトリ
    //   $in_get_copyvar:     PlaybookからCPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_copyvars_list:  Playbookで使用しているCPF変数のリスト
    //                           $ina_copyvars_list[ロール名][変数名]=1
    //   $in_get_tpfvar:      PlaybookからTPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_tpfvars_list:   Playbookで使用しているTPF変数のリスト
    //                           $ina_tpfvars_list[ロール名][変数名]=1
    //   $ina_ITA2User_var_list 読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list 読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkRoleDirectory($in_base_dir,
                              $in_dir,
                              $ina_system_vars,
                              $in_role_pkg_name,
                              $in_role_name,
                             &$ina_def_vars_list,
                             &$ina_def_varsval_list,
                             &$ina_def_array_vars_list,
                              $in_get_copyvar,
                             &$ina_copyvars_list,
                              $in_get_tpfvar,
                             &$ina_tpfvars_list,
                             &$ina_ITA2User_var_list,
                             &$ina_User2ITA_var_list
                              )
    {
        $result_code = true;
        $this->lva_rolevar = array();
        /////////////////////////////////////////////////////
        // roleディレクトリを取得
        /////////////////////////////////////////////////////
        $fullpath = $in_dir;

        //デフォルト変数定義一覧 初期化
        $ina_def_vars_list[$in_role_name] = array();

        //role名退避
        $this->lva_rolename[] = $in_role_name;
        $ret = $this->chkRoleSubDirectory($in_base_dir,
                                          $fullpath,
                                          $ina_system_vars,
                                          $in_role_pkg_name,
                                          $in_role_name,
                                          $ina_def_vars_list,
                                          $ina_def_varsval_list,
                                          $ina_def_array_vars_list,
                                          $in_get_copyvar,
                                          $ina_copyvars_list,
                                          $in_get_tpfvar,
                                          $ina_tpfvars_list,
                                          $ina_ITA2User_var_list,
                                          $ina_User2ITA_var_list
                                          );
                if($ret === false){
                    return(false);
                }

        return(true);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    // 処理内容
    //   roleディレクトリ配下のディレクトリとファイルが妥当かチェックする。
    // パラメータ
    //   $in_base_dir:        ベースディレクトリ
    //   $in_dir:             roleディレクトリ
    //   $ina_system_vars:    システム変数リスト(機器一覧)
    //   $in_role_pkg_name:   ロールパッケージ名
    //   $in_role_name:       ロール名
    //   $ina_def_vars_list:  各ロールのデフォルト変数ファイル内に定義されている
    //                        変数名のリスト 
    //                          一般変数
    //                            $ina_def_vars_list[ロール名][変数名]=0
    //                          配列変数
    //                            $ina_def_vars_list[ロール名][配列数名]=array([子供変数名]=0,...)
    //   $ina_def_varsval_list:  
    //                        各ロールのデフォルト変数ファイル内に定義されている変数名の具体値リスト
    //                          一般変数
    //                            $ina_def_vars_val_list[ロール名][変数名][0]=具体値
    //                          複数具体値変数
    //                            $ina_def_vars_val_list[ロール名][変数名][1]=array(1=>具体値,2=>具体値....)
    //                          配列変数
    //                            $ina_def_vars_val_list[ロール名][変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //   $in_base_dir:        zipファイル解凍ディレクトリ
    //   $in_get_copyvar:     PlaybookからCPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_copyvars_list:  Playbookで使用しているCPF変数のリスト
    //                          $ina_copyvars_list[ロール名][変数名]=1
    //   $in_get_tpfvar:      PlaybookからTPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_tpfars_list:    Playbookで使用しているTPF変数のリスト
    //                          $ina_copyvars_list[ロール名][変数名]=1
    //   $ina_ITA2User_var_list   読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list   読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkRoleSubDirectory($in_base_dir,
                                 $in_dir,
                                 $ina_system_vars,
                                 $in_role_pkg_name,
                                 $in_rolename,
                                &$ina_def_vars_list,
                                &$ina_def_varsval_list,
                                &$ina_def_array_vars_list,
                                 $in_get_copyvar,
                                &$ina_copyvars_list,
                                 $in_get_tpfvar,           
                                &$ina_tpfvars_list,        
                                &$ina_ITA2User_var_list,
                                &$ina_User2ITA_var_list
                                )
    {

        ///////////////////////////////////
        // 該当ロールの読替表の読込み
        ///////////////////////////////////
        $ina_ITA2User_var_list[$in_rolename] = array();
        $ina_User2ITA_var_list[$in_rolename] = array();
        $ITA2User_var_list = array();
        $User2ITA_var_list = array();
        $errmsg            = "";
                       
        // ロール名の / を % に置き換える
        $edit_role_name = preg_replace('/\//','%', $in_rolename);
        // 該当ロールの読替表のファイル名生成
        $translation_table_file = $in_base_dir . "/ita_translation-table_" . $edit_role_name . ".txt";


        // 該当ロールの読替表のファイルの有無判定
        if((file_exists($translation_table_file) === true) &&
            (is_file($translation_table_file) === true)){
             // 該当ロールの読替表を読込
             $ret = $this->readTranslationFile($translation_table_file,
                                               $ITA2User_var_list,
                                               $User2ITA_var_list,
                                               $errmsg);
            if($ret === false){
                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                return(false);
            }                                                 
        }
        // 読替変数の情報を退避
        $ina_ITA2User_var_list[$in_rolename] = $ITA2User_var_list;
        $ina_User2ITA_var_list[$in_rolename] = $User2ITA_var_list;
                       
        ///////////////////////////////////
        // 該当ロールのITA readmeの読込み
        ///////////////////////////////////
        $all_parent_vars_list = array();
        $user_vars_file = $in_base_dir . "/ita_readme_" . $edit_role_name. ".yml";

        $user_vars_list = array();
        $errmsg = "";
        $f_line = "";
        $f_name = "";
        $user_varsval_list = array();
        $user_array_vars_list = array();
        $user_array_varsval_list = array();

        // ユーザー定義変数ファイルの有無判定
        if((file_exists($user_vars_file) === true) &&
            (is_file($user_vars_file) === true)){
            // ユーザー定義変数ファイルのデータ読込
            $dataString = file_get_contents($user_vars_file);
   
            // ユーザー定義変数ファイルから変数取得
            $chkObj = new DefaultVarsFileAnalysis($this->lv_objMTS);

            // Spycモジュールの読み込み
            $ret = $chkObj->LoadSpycModule($errmsg, $f_name, $f_line);
            if($ret === false){
                $errmsg = $errmsg . "(" . $f_line . ")";
                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                return(false);
            }

            $parent_vars_list = array();
            $ret = $chkObj->FirstAnalysis($dataString,
                                          $parent_vars_list,
                                          $in_rolename, 
                                          $user_vars_file,
                                          $ITA2User_var_list, 
                                          $User2ITA_var_list,
                                          $errmsg, $f_name, $f_line,
                                          $this->lva_msg_role_pkg_name);
            if($ret === false){
                // defaults=>main.ymlからの変数取得失敗
                $errmsg = $errmsg . "(" . $f_line . ")";
                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                return(false);
            }
    
            $ret = $chkObj->MiddleAnalysis($parent_vars_list,
                                           $in_rolename,
                                           $user_vars_file,
                                           $errmsg, $f_name, $f_line,
                                           $this->lva_msg_role_pkg_name);
    
            if($ret === false){
                // defaults=>main.ymlからの変数取得失敗
                $errmsg = $errmsg . "(" . $f_line . ")";
                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                return(false);
            }
    
            // 一時ファイル名
            $tmp_file_name  = "/tmp/LegacyRoleDefaultsVarsFile_" . getmypid() . ".yaml";
    
            $ret = $chkObj->LastAnalysis($tmp_file_name,$parent_vars_list,
                                         $user_vars_list,$user_varsval_list,
                                         $user_array_vars_list,
                                         $in_rolename,
                                         $user_vars_file,
                                         $errmsg, $f_name, $f_line,  
                                         $this->lva_msg_role_pkg_name);
    
            if($ret === false){
                // defaults=>main.ymlからの変数取得失敗
                $errmsg = $errmsg . "(" . $f_line . ")";
                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                return(false);
            }
    
            // ita readmeに定義されている変数(親)を取り出す。
            for($idx=0;$idx<count($parent_vars_list);$idx++){
                $all_parent_vars_list[$parent_vars_list[$idx]['VAR_NAME']] = 0;
            }
        }

        ////////////////////////////////////////////////////////
        // role内のディレクトリをチェック
        ////////////////////////////////////////////////////////
        $files = scandir($in_dir);
        $files = array_filter($files,
                              function ($file){
                                  return !in_array($file,array('.','..'));
                              }
                             );
        $result_code    = true;
        $tasks_dir      = false;
        foreach ($files as $file){
            $fullpath = rtrim($in_dir,'/') . '/' . $file;

            if(is_dir($fullpath)){
               switch($file){
               case "tasks":
                   $tasks_dir      = true;
                   // p1:ロール変数取得有(true)/無(false)
                   // p2:main.yml必須有(true)/無(false)
                   // p3:サブディレクトリ(許可(true)/許可しない(false))
                   // p4:main.ymlファイル以外のファイル(許可(true)/許可しない(false))
                   // p5:TPF/CPF変数取得有(true)/無(false)
                   //                                                       p1     p2     p3     p4    p5
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, true,  true,  true,  true, true,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "handlers":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, true,  false, true,  true, true,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "templates":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, true,  false, true, true, true,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "meta":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, false,  false, true, true, false,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "files":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, false, false, true, true, false,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "vars":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, false, false, true, true, false,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   break;
               case "defaults":
                   $ret = $this->chkRoleFiles($fullpath,$in_rolename,$file, false, false, true, true, false,
                                              $in_get_copyvar,$ina_copyvars_list, $in_get_tpfvar,$ina_tpfvars_list,
                                              $ina_system_vars);
                   if($ret === true) {
                       $parent_vars_list = array();
                       $vars_list        = array();
                       $array_vars_list  = array();
                       $varsval_list      = array();
                       // defaultsディレクトリ内の変数定義を読み取る
                       $ret = $this->AnalysisDefaultVarsFiles(LC_RUN_MODE_STD,
                                                              $in_base_dir,
                                                              $fullpath,
                                                              $in_role_pkg_name,
                                                              $in_rolename,
                                                              $parent_vars_list,
                                                              $vars_list,
                                                              $array_vars_list,
                                                              $varsval_list,
                                                              $ITA2User_var_list,
                                                              $User2ITA_var_list);
                       if($ret === false) {
                           return false;
                       }
                       // ita readmeに定義されている変数(親)とdefault定義に定義されている変数(親)をマージ
                       for($idx=0;$idx<count($parent_vars_list);$idx++){
                           $all_parent_vars_list[$parent_vars_list[$idx]['VAR_NAME']] = 0;
                       }

                       // 読替表の任意変数がデフォルト変数定義ファイルやita Readmeファイルに登録されているか判>定する。
                       $ret = $this->chkTranslationVars($all_parent_vars_list,$User2ITA_var_list,
                                                         basename($translation_table_file), $errmsg);
                       if($ret === false){
                           $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg);
                           return(false);
                       }
                       // ユーザー定義変数ファイルから変数取得
                       $chkObj = new DefaultVarsFileAnalysis($this->lv_objMTS);

                       // default変数定義ファイルの変数情報とユーザー定義変数ファイル
                       // の変数情報をマージする。
                       $chkObj->margeDefaultVarsList($vars_list     , $varsval_list,
                                                     $user_vars_list, $user_varsval_list,
                                                     $array_vars_list, $user_array_vars_list );
                       unset($chkObj);

                       //デフォルト変数定義一覧 に変数の情報を登録
                       $ina_def_vars_list[$in_rolename] = $vars_list;
                       $ina_def_array_vars_list[$in_rolename] = $array_vars_list;

                       //デフォルト変数定義の変数の具体値情報を登録
                       $ina_def_varsval_list[$in_rolename] = $varsval_list;

                   }
                   break;
               default:
                   // ベストプラクティスのディレクトリ以外はチェックしない
                   $ret = true;
                   break;
               }
               if($ret === false){
                   return($ret); 
               }
            }
        }
        if($tasks_dir === false){
            //$ary[70006] = "｛｝にtasksディレクトリがありません。";
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70006",array('./roles/' . $in_rolename));
            $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
            return(false);
        }
        return(true);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   defaultディレクトリの変数定義ファイルを読み取る
    // パラメータ
    //   $in_mode:               解析ファイル種別  LC_RUN_MODE_STD
    //   $in_base_dir:           ベースディレクトリ
    //   $in_dir:                roleディレクトリ
    //   $in_role_pkg_name:      ロールパッケージ名
    //   $in_rolename:           ロール名
    //   $ina_parent_vars_list:  デフォルト変数定義ファイルやita Readmeファイルに登録されている変数リスト
    //   $ina_vars_list:         各ロールのデフォルト変数ファイル内に定義されている
    //                           変数名のリスト 
    //                             一般変数
    //                               $ina_vars_list[ロール名][変数名]=0
    //                             配列変数
    //                               $ina_vars_list[ロール名][配列数名]=array([子供変数名]=0,...)
    //   $ina_array_vars_list:   各ロールのデフォルト変数ファイル内に定義されている
    //                           多段変数リスト
    //   $ina_varsval_list:      各ロールのデフォルト変数ファイル内に定義されている変数名の具体値リスト
    //                             一般変数
    //                               $ina_varsval_list[ロール名][変数名][0]=具体値
    //                             複数具体値変数
    //                               $ina_varsval_list[ロール名][変数名][1]=array(1=>具体値,2=>具体値....)
    //                             配列変数
    //                               $ina_varsval_list[ロール名][変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //   $ina_ITA2User_var_list  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list  読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function AnalysisDefaultVarsFiles($in_mode,    //LC_RUN_MODE_STD
                                      $in_base_dir,
                                      $in_dir,
                                      $in_role_pkg_name,
                                      $in_rolename,
                                     &$ina_parent_vars_list,
                                     &$ina_vars_list,
                                     &$ina_array_vars_list,
                                     &$ina_varsval_list,
                                      $ina_ITA2User_var_list,
                                      $ina_User2ITA_var_list) {

        $files = array();
        
        // ディレクトリ配下のファイル一覧取得
        $filelist = getFileList($in_dir);
        foreach ($filelist as $file) {
            $files[] = trim(str_replace($in_dir . "/","",$file));
        }
        foreach ($files as $file){
            $fullpath = rtrim($in_dir,'/') . '/' . $file;
            $preg_base_dir = str_replace("/","\/", $in_base_dir);
            $display_file_name = preg_replace('/^' . $preg_base_dir . '/','', $fullpath);
            // ディレクトリは無視
            if(is_dir($fullpath)){
                continue;
            }
            $chkObj = new YAMLFileAnalysis($this->lv_objMTS);

            $parent_vars_list = array();
            $vars_list        = array();
            $array_vars_list  = array();
            $varsval_list     = array();
            $ret = $chkObj->VarsFileAnalysis($in_mode,
                                             $fullpath,
                                             $parent_vars_list,
                                             $vars_list,
                                             $array_vars_list,
                                             $varsval_list,
                                             $in_role_pkg_name,
                                             $in_rolename,
                                             $display_file_name,
                                             $ina_ITA2User_var_list,
                                             $ina_User2ITA_var_list);
            if($ret === false) {
                // 解析結果にエラーがある場合
                $errmsg = $chkObj->GetLastError();
                unset($chkObj);

                $this->SetLasteError(basename(__FILE__),__LINE__,$errmsg[0]);
                return(false);
            }
            unset($chkObj);
            // 定義されている変数(親)を取り出す。
            for($idx=0;$idx<count($parent_vars_list);$idx++){
                $var_name = $parent_vars_list[$idx]['VAR_NAME'];
                // 同じ変数名が複数のdefault定義ファイルに記述されている場合はエラー
                if(isset($ina_parent_vars_list[$parent_vars_list[$idx]['VAR_NAME']])) {
                    //$ary[6000065] = ""変数が複数のdefault定義ファイルに記述されています。(ロールパッケージ名:{} ロール名:{} 変数名:{})"
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000065",
                                                               array($in_role_pkg_name,
                                                                     $in_rolename,
                                                                     $var_name));

                    $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
                $ina_parent_vars_list[] = $parent_vars_list[$idx];
            }
            // 変数の情報をマージする。
            foreach($vars_list as $var_name=>$var_info) {
                $ina_vars_list[$var_name] = $var_info;
            }
            foreach($array_vars_list as $var_name=>$var_info) {
                $ina_array_vars_list[$var_name] = $var_info;
            }
            foreach($varsval_list as $var_name=>$var_info) {
                $ina_varsval_list[$var_name] = $var_info;
            }
        }
        return(true);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   roleの各ディレクトリとファイルが妥当かチェックする。
    // パラメータ
    //   $in_base_dir:        ベースディレクトリ
    //   $in_dir:         roleディレクトリ
    //   $in_rolename     ロール名
    //   $in_dirname      ディレクトリ名
    ////   $in_get_rolevar  ロール変数取得有(true)/無(false)
    ////   $in_main_yml     main.yml必須有(true)/無(false)
    ////   $in_etc_yml      main.ymlと他ファイルの依存有(true)/無(false)
    ////   $in_main_yml_only
    ////                    main.ymlファイル以外のファイルは存在不可
    //   $in_get_rolevar  ロール変数取得有(true)/無(false)
    //   $in_main_yml     main.yml必須有(true)/無(false)
    //   $in_etc_yml      main.ymlファイル以外のファイル(許可(true)/許可しない(false))
    //   $in_sub_dir      サブディレクトリ(許可(true)/許可しない(false))

    //   $in_get_var_tgt_dir: CPF/TPF変数を取得対象ディレクトリ判定 true:取得　false:取得しない
    //   $in_get_copyvar:    PlaybookからCPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_copyvars_list: Playbookで使用しているCPF変数のリスト
    //                       $ina_copyvars_list[ロール名][変数名]=1
    //   $in_get_tpfvar:     PlaybookからTPF変数を取得の有無  true:取得　false:取得しない
    //   $ina_tpfvars_list:  Playbookで使用しているTPF変数のリスト
    //                       $ina_tpfvars_list[ロール名][変数名]=1
    //   $ina_system_vars システム変数リスト(機器一覧)
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkRoleFiles($in_dir,
                          $in_rolename,
                          $in_dirname,
                          $in_get_rolevar,
                          $in_main_yml,
                          $in_etc_yml,
                          $in_sub_dir,
                          $in_get_var_tgt_dir,
                          $in_get_copyvar,
                         &$ina_copyvars_list,
                          $in_get_tpfvar, 
                         &$ina_tpfvars_list,        
                          $ina_system_vars){
        $files = array();
        
        // ディレクトリ配下のファイル一覧取得
        $filelist = getFileList($in_dir);
        foreach ($filelist as $file) {
            $files[] = trim(str_replace($in_dir . "/","",$file));
        }

        $main_yml = false;
        $etc_yml = false;
        $result_code = true;
        foreach ($files as $file){
            $fullpath = rtrim($in_dir,'/') . '/' . $file;
            if(is_dir($fullpath)){
                // サブディレクトリを許可しているか判定
                if($in_sub_dir === false) {
                    //$ary[70025] = "サブディレクトリ(｛｝)が存在します。";
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70025",  // #1244 2017/08/22 Update
                                                            array('./roles/' .
                                                                   $in_rolename . '/' .
                                                                   $in_dirname . '/' .
                                                                   $file));
                    $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
                    return(false);
                }
            }
            if(is_file($fullpath)){
                if($file == "main.yml"){
                     $main_yml = true;
                }
                else{
                     $etc_yml  = true;
                }

////////////////////////////////
// $ary[70051] = "defaultsディレクトリにmain.yml以外のファイルがあります。(file:{})";
//                if(($etc_yml === true)&&($in_main_yml_only === true)){
//                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70051",
//                                                                array('./roles/' .
//                                                                      $in_rolename . '/' .
//                                                                      $in_dirname . '/'  .
//                                                                      $file ));
//                    $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
//                    return(false);
//                }
///////////////////////////////
                // 変数初期化
                $file_vars_list        = array();
                $file_global_vars_list = array();

                // ファイルの内容を読込む
                $dataString = file_get_contents($fullpath);

                // ホスト変数の抜出が指定されている場合
                if($in_get_rolevar === true){
                    if($in_dirname == "templates"){
                        // テンプレートから変数を抜出す
                        $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$ina_system_vars);
                        $file_vars_list = $objWSRA->getTPFVARSParsedResult();
                        unset($objWSRA);

                        // テンプレートからグローバル変数を抜出す
                        $system_vars = array();
                        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_TEMP_GBL_HED ,$dataString,$system_vars);
                        $file_global_vars_list = $objWSRA->getTPFVARSParsedResult();
                        unset($objWSRA);

                    }
                    else{
                        // テンプレート以外から変数を抜出す
                        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$ina_system_vars);
                        $aryResultParse = $objWSRA->getParsedResult();
                        $file_vars_list = $aryResultParse[1];
                        unset($objWSRA);

                        // テンプレート以外からグローバル変数を抜出す
                        $system_vars = array();
                        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_GBL_HED,$dataString,$system_vars);
                        $aryResultParse = $objWSRA->getParsedResult();
                        $file_global_vars_list = $aryResultParse[1];
                        unset($objWSRA);

                    }
                    // ファイル内で定義されていた変数を退避
                    if(count($file_vars_list) > 0){
                         foreach ($file_vars_list as $var){
                             $this->lva_varname[$in_rolename][$var] = 0;
                         }
                    }

                    // ファイル内で定義されていたグローバル変数を退避
                    if(count($file_global_vars_list) > 0){
                         foreach ($file_global_vars_list as $var){
                             $this->lva_globalvarname[$in_rolename][$var] = 0;
                         }
                    }
                }
                // CPF/TPF変数を取得するか判定
                if($in_get_var_tgt_dir === true) {
                    $tgt_file = $in_rolename . "/" . $in_dirname . "/" . $file;
                    if($in_get_copyvar === true) {
                        $la_cpf_vars = array();
                        SimpleVerSearch(DF_HOST_CPF_HED,$dataString,$la_cpf_vars);
                        // ファイル内で定義されていたCPF変数を退避
                        if(count($la_cpf_vars) > 0){
                            foreach( $la_cpf_vars as $no => $cpf_var_list ){
                                foreach( $cpf_var_list as $line_no  => $cpf_var_name ){
                                    $ina_copyvars_list[$in_rolename][$tgt_file][$line_no][$cpf_var_name] = 0;
                                }
                            }
                        }
                    }
                    if($in_get_tpfvar === true) {
                        $la_tpf_vars = array();
                        SimpleVerSearch(DF_HOST_TPF_HED,$dataString,$la_tpf_vars);
                        // ファイル内で定義されていたCPF変数を退避
                        if(count($la_tpf_vars) > 0){
                            foreach( $la_tpf_vars as $no => $tpf_var_list ){
                                foreach( $tpf_var_list as $line_no  => $tpf_var_name ){
                                    $ina_tpfvars_list[$in_rolename][$tgt_file][$line_no][$tpf_var_name] = 0;
                                }
                            }
                        }
                    }
                }
                // ディレクトリがdefaultsの場合、変数構造を解析する。
                if($in_dirname == "defaults"){
                }
            }
        }
        // main.ymlが必要なディレクトリにmain.ymlがない場合
        if(($in_main_yml === true) && ($main_yml===false)){
            // $ary[70003] = "main.ymlファイルがありません。(ディレクトリ:{})";
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70003",
                                                        array('./roles/' .
                                                               $in_rolename . '/' .
                                                               $in_dirname . '/'));
            $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
            return(false);
        }
/////////////////////////////////////////////
//        // main.ymlと他ファイルの依存有の場合でmain.ymlがない場合
//        if(($in_etc_yml === true) && ($main_yml===false) && ($etc_yml === true)){
//               $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70003",
//                                                           array('./roles/' .
//                                                                  $in_rolename . '/' .
//                                                                  $in_dirname . '/'));
//               $this->SetLasteError(basename(__FILE__),__LINE__,$msgstr);
//               return(false);
//        }
/////////////////////////////////////////////
        return(true);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0011
    // 処理内容
    //   読替表より変数の情報を取得する。
    //
    // パラメータ
    //   $in_filepath:            読替表ファイルパス
    //   $ina_ITA2User_var_list:  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list:  読替表の変数リスト　ユーザ変数=>ITA変数
    //   $in_errmsg:              エラーメッセージリスト
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function readTranslationFile($in_filepath,&$ina_ITA2User_var_list,&$ina_User2ITA_var_list,&$in_errmsg){
        $in_errmsg = "";
        $ret_code  = true;
        $dataString = file_get_contents($in_filepath);
        $line = 0;
        // 入力データを行単位に分解
        $arry_list = explode("\n",$dataString);
        foreach($arry_list as $strSourceString){
            $line = $line + 1;
            // コメント行は読み飛ばす。
            if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
                continue;
            }
            // 空行を読み飛ばす。
            if(strlen(trim($strSourceString)) == 0){
                continue;
            }
            // 読替変数の構文を確認
            // LCA_[0-9,a-Z_*]($s*):($s+)playbook内で使用している変数名
            // 読替変数名の構文判定
            $ret = preg_match_all("/^(\s*)LCA_[a-zA-Z0-9_]*(\s*):(\s+)/",$strSourceString,$ita_var_match);
            if($ret == 1){

                // :を取除き、読替変数名取得
                $ita_var_name    = trim(str_replace(':','',$ita_var_match[0][0]));
                // 任意変数を取得
                $user_var_name = trim(preg_replace('/^(\s*)LCA_[a-zA-Z0-9_]*(\s*):(\s+)/','',$strSourceString));
                if(strlen($user_var_name) != 0){
                    // 任意変数がVAR_でないことを判定
                    $ret = preg_match_all("/^VAR_/",$user_var_name ,$user_var_match);
                    if($ret == 1){
                        if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                        $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000000",array(basename($in_filepath),$line));
                        $ret_code = false;
                        continue;
                    }
                    // 任意変数が文字列になっているか
                    $ret = preg_match_all("/^(\S+)$/",$user_var_name ,$user_var_match);
                    if($ret != 1){
                        if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                        $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000001",array(basename($in_filepath),$line));
                        $ret_code = false;
                        continue;
                    }
                }
                else{
                    if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                    $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000001",array(basename($in_filepath),$line));
                    $ret_code = false;
                    continue;
                }
            }
            else{
                if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000002",array(basename($in_filepath),$line));
                $ret_code = false;
                continue;
            }
            // 任意変数が重複登録の二重登録判定
            if(@count($ina_User2ITA_var_list[$user_var_name]) != 0){
                if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000003",array(basename($in_filepath),$user_var_name));
                $ret_code = false;
                continue;
            }
            else{
                $ina_User2ITA_var_list[$user_var_name] = $ita_var_name;
            }
            // 読替変数が重複登録の二重登録判定
            if(@count($ina_ITA2User_var_list[$ita_var_name]) != 0){
                if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000004",array(basename($in_filepath),$ita_var_name));
                $ret_code = false;
                continue;
            }
            else{
                $ina_ITA2User_var_list[$ita_var_name] = $user_var_name;
            }
        }
        return $ret_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0012
    // 処理内容
    //   読替表の任意変数がデフォルト変数定義ファイルやita Readmeファイルに登録されているか判定する。
    //
    // パラメータ
    //   $ina_all_parent_vars_list:  デフォルト変数定義ファイルやita Readmeファイルに登録されている変数リスト
    //   $ina_User2ITA_var_list:     読替表の変数リスト　ユーザ変数=>ITA変数
    //    $in_translation_table_file: 読替表ファイル
    //   $in_errmsg:                 エラーメッセージリスト
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkTranslationVars($ina_all_parent_vars_list,$ina_User2ITA_var_list,$in_translation_table_file,&$in_errmsg){
        $ret_code   = true;
        $in_errmsg  = "";
        foreach ($ina_User2ITA_var_list as $user_var_name=>$rep_var_name){
            if(@count($ina_all_parent_vars_list[$user_var_name])==0){
                if(strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                {
                    $in_errmsg = $in_errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000005",array(basename($in_translation_table_file),$user_var_name));
                    $ret_code = false;
                    continue;
                }
            }
        }
        return $ret_code;
    }

    function SetLasteError($p1,$p2,$p3){
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        $this->lv_lasterrmsg[0] = $p3;
        $this->lv_lasterrmsg[1] = "FILE:$p1 LINE:$p2 $p3";
    }
    function debuglog($line,$msg){
    }
}
    ////////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   グローバル変数の情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_global_vars_list:     グローバル変数のリスト
    //   $
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBGlobalVarsMaster(&$ina_global_vars_list,&$in_msgstr){
        global $objDBCA;
        global $objMTS;

        $sql = "SELECT                         \n" .
               "  VARS_NAME,                   \n" .
               "  VARS_ENTRY                   \n" .
               "FROM                           \n" .
               "  B_ANS_GLOBAL_VARS_MASTER     \n" .
               "WHERE                          \n" .
               "  DISUSE_FLAG            = '0';\n";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $in_msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));

            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $in_msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $in_msgstr = $in_msgstr . "\n" . $objQuery->getLastError();

            unset($objQuery);
            return false;
        }

        $ina_global_vars_list = array();

        while ( $row = $objQuery->resultFetch() ){
            $ina_global_vars_list[$row['VARS_NAME']] = $row['VARS_ENTRY'];
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0013
    // 処理内容
    //   指定ディレクトリ配下のファイル一覧取得
    //
    // パラメータ
    //   $dir:       ディレクトリ
    //
    // 戻り値
    //   ファイル一覧
    ////////////////////////////////////////////////////////////////////////////////
    function getFileList($dir) {
        $files = scandir($dir);
        $files = array_filter($files, function ($file) {
            return !in_array($file, array('.', '..'));
        });

        $list = array();
        foreach ($files as $file) {
            $fullpath = rtrim($dir, '/') . '/' . $file;
            $list[] = $fullpath;
            if (is_dir($fullpath)) {
                $list = array_merge($list, getFileList($fullpath));
            }
        }
        return $list;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0014
    // 処理内容
    //   指定ディレクトリ配下からroleディレクトリを探す
    //
    // パラメータ
    //   $BaseDir:     rolesディレクトリを含む階層のパス
    //   $RoleDirList: roleディレクトリ一覧
    //                 tasksが定義されているディレクトリ
    //                 $RoleDirList[roleディレクトリパス] = role名
    //   $errormsg:    エラーメッセージ
    // 戻り値
    //   true:  roleディレクトリ一覧
    //   false: rolesディレクトリがない
    ////////////////////////////////////////////////////////////////////////////////
    function RoleDirectoryAnalysis($BaseDir,&$RoleDirList,$objMTS,&$errormsg) {
        $role_task_list = array();
        $RoleDirList  = array();

        $result_code    = false;
        $role_dir_list  = array();
        $roles_dir      = $BaseDir . "/roles/";
        $preg_roles_dir = str_replace("/","\/", $roles_dir);
        $errormsg       = "";
        
        // ディレクトリか判定
        if( ! is_dir($roles_dir)) {
            // rolesディレクトリがない
            $errormsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70002");
            return $result_code;
        }
        // ディレクトリリスト取得
        $dir_list = getFileList($BaseDir);

        //tasksフォルダリスト
        //roles以降は除外
        foreach($dir_list as $dir) {
            // ディレクトリ確認
            if( is_file($dir)) {
                continue;
            }
            // rolesディレクトリ確認
            if($dir . "/" == $roles_dir) {
                $result_code = true;
            }
            // rolesディレクトリ以外はスキップ
            if(0 !== strpos($dir,$roles_dir)) {
                continue;
            }
            if (basename($dir) == "tasks") {
                $role_dir_list[] = preg_replace('/\/tasks$/','', $dir);
            }
        }
        foreach ($role_dir_list as $role_dir) {
            //前方一致したものは除外関数使う
            if (childRole($role_dir,$role_dir_list)) {
                continue;
            }
            // rolesディレクトリ以降の階層をrole名にする。
            $role_name = preg_replace('/^' . $preg_roles_dir. '/','', $role_dir);
            $RoleDirList[$role_dir] = $role_name;
        }
        if($result_code === true) {
            // roleディレクトリが存在しているか
            if(@count($RoleDirList) == 0) {
                $errormsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70004");
                $result_code = false;
                return(false);
            }
        }
        if($result_code === true) {
            // ロール名に%が含まれていないか
            $errormsg = "";
            foreach($RoleDirList as $role_dir=>$role_name) {
                $matchi = array();
                $ret = preg_match('/%/',$role_name,$matchi,PREG_OFFSET_CAPTURE);
                if ($ret != 0){
                    if(strlen($errormsg) != 0) $errormsg .= "\n";
                    $errormsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000063",array($role_name));
                    $result_code = false;
                }
            }
        }
        if($result_code === true) {
            // ロール名が1024バイト以上あるか
            $errormsg = "";
            foreach($RoleDirList as $role_dir=>$role_name) {
                if(strlen($role_name) > 1024) {
                    if(strlen($errormsg) != 0) $errormsg .= "\n";
                    $errormsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000064",array($role_name));
                    $result_code = false;
                }
            }
        }
        if($result_code === true) {
            // ディレクトリのパーミッションを変更
            $cmd = sprintf("find %s -type d -exec chmod 755 {} +",$BaseDir);
            system($cmd);
        }
        return $result_code;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0015
    // 処理内容
    //   roleディレクトリ配下にtasksディレクトリがある場合の除外処理
    //
    // パラメータ
    //   $data:        roleディレクトリ
    //   $RoleDirList: rolesディレクトリ配下のasksが定義されているディレクトリ一覧
    // 戻り値
    //   true:   roleディレクトリ配下にtasksディレクトリがある
    //   false:  roleディレクトリ配下のtasksディレクトリではない
    ////////////////////////////////////////////////////////////////////////////////
    function childRole($data,$DirList) {
        foreach ($DirList as $dirs) {
            //完全一致は除外
            if ($data === $dirs) {
                continue;
            //前方一致は格納
            }if (0 === strpos($data,$dirs)) {
                return true;
            }
        }
        return false;
    }

/////////////////////////////////////////////////////////////////////////////////
//  C0002
//  処理概要
//    defalte変数ファイルに登録されている変数を解析
//
//    F1001  __construct
//    F1002  chkVarsStruct
//    F1003  chkallVarsStruct
//    F1004  VarsStructErrmsgEdit
//    F1005  allVarsStructErrmsgEdit
//    F1006  chkDefVarsListPlayBookVarsList
//    F1007  margeDefaultVarsList
//    F1008  FirstAnalysis
//    F1009  VarTypeAnalysis
//    F1010  VarPosAnalysis
//    F1011  ParentVarAnalysis
//    F1012  MiddleAnalysis
//    F1013  CreateTempDefaultsVarsFile
//    F1014  LoadSpycModule
//    F1015  LastAnalysis
//    F1016  chkStandardVariable
//    F1017  chkMultiValueVariable
//    F1018  chkMultiValueVariableSub
//    F1019  chkMultiArrayVariable
//    F1020  MakeMultiArrayToDiffMultiArray
//    F1021  MultiArrayDiff
//    F1022  InnerArrayDiff
//    F1023  is_assoc
//    F1024  is_stroc
//    F1025  MakeMultiArrayToFirstVarChainArray
//    F1026  MakeMultiArrayToLastVarChainArray
////////////////////////////////////////////////////////////////////////////////
//----ここからクラス定義
class DefaultVarsFileAnalysis{
    const LC_IDEL                            = "0";
    const LC_VAR_VAL                         = "1";
    const LC_ARRAY_VAR_VAL                   = "2";
    const LC_MULTI_VAR_VAL                   = "3";
    const LC_LISTARRAY_VAR_VAL               = "4";
    const LC_LIST_IDEL                       = "0";
    const LC_LIST_VAR                        = "1";
    const LC_LIST_VAL                        = "2";
    
    // 変数定義を判定する正規表記  /^VAR_(\S+):/ => /^VAR_(\S+)(\s*):/
    const LC_VARNAME_MATCHING                = "/^VAR_(\S+)(\s*):/";

    const LC_LISTARY_VAR                     = "VAR_(\S+)";
    const LC_LISTARY_VAR_MACH_P1_H           = "^-(\s+)";
    const LC_LISTARY_VAR_MACH_P1_F           = "(\s*):(\s*)";
    const LC_LISTARY_VAR_MACH_P2_H           = "^(\s*)";
    const LC_LISTARY_VAR_MACH_P2_F           = "(\s*):(\s*)";
    const LC_LISTARY_VARNAME_CHK_P1          = "/" . self::LC_LISTARY_VAR_MACH_P1_H . 
                                                     self::LC_LISTARY_VAR . 
                                                     self::LC_LISTARY_VAR_MACH_P1_F . "/";
    const LC_LISTARY_VARNAME_CHK_P2          = "/" . self::LC_LISTARY_VAR_MACH_P2_H . 
                                                     self::LC_LISTARY_VAR . 
                                                     self::LC_LISTARY_VAR_MACH_P2_F . "/";
    const LC_VARNAME_CHK_MATCHING            = "/^VAR_[a-zA-Z0-9_]*$/";
    const LC_USER_LISTARY_VAR                = "[a-zA-Z0-9_]*";
    const LC_USER_VARNAME_MATCHING           = "/^[a-zA-Z0-9_]*(\s*):/";
    const LC_USER_LISTARY_VARNAME_CHK_P1     = "/" . self::LC_LISTARY_VAR_MACH_P1_H . 
                                                     self::LC_USER_LISTARY_VAR . 
                                                     self::LC_LISTARY_VAR_MACH_P1_F . "/";
    const LC_USER_LISTARY_VARNAME_CHK_P2     = "/" . self::LC_LISTARY_VAR_MACH_P2_H . 
                                                     self::LC_USER_LISTARY_VAR . 
                                                     self::LC_LISTARY_VAR_MACH_P2_F . "/";
    const LC_USER_VARNAME_CHK_MATCHING       = "/^[a-zA-Z0-9_]*$/";
    // 変数タイプ
    const LC_VAR_TYPE_ITA                    = "0";   // ITA (VAR_)
    const LC_VAR_TYPE_USER                   = "1";   // ユーザー
    const LC_VAR_TYPE_USER_ITA               = "2";   // ユーザー読替(LCA_)

    protected   $lv_objMTS;
    protected   $lv_msg_pkg_name;

    // 処理モード
    protected   $lv_run_mode;                

    ////////////////////////////////////////////////////////////////////////////////
    // F1001
    // 処理内容
    //   コンストラクタ
    //
    // パラメータ
    //   なし
    //
    // 戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function __construct(&$in_objMTS){
        $this->lv_objMTS = $in_objMTS;
        $this->lv_run_mode = LC_RUN_MODE_STD;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1002
    // 処理内容
    //   ロールパッケージ内のデェフォルト変数ファイルで定義されている配列変数の
    //   構造が一致しているか判定
    //
    // パラメータ
    //   $ina_vars_list:            defalte変数ファイルの変数リスト格納
    //                                非配列変数　ina_vars_list[ロール名][変数名] = 0;
    //                                配列変数　  ina_vars_list[ロール名][変数名] = array(配列変数名, ....)
    //   $ina_def_array_vars_list:  defalte変数ファイルの多次元変数リスト格納
    //   $ina_err_vars_list:        ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                                in_err_vars_list[変数名][ロールパッケージ名][ロール名]
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkVarsStruct( $ina_vars_list, $ina_def_array_vars_list, &$ina_err_vars_list){
         $ret_code = true;
         $in_err_vars_list = array();

         // 多次元変数をKeyに他ロールに多次元変数以外の変数があるか判定
         foreach($ina_def_array_vars_list as $role_name=>$vars_list){
             foreach($vars_list as $var_name=>$chl_vars_list){
                 // 他のロールで同じ変数名で構造が異なるのものがあるか確認
                 foreach($ina_vars_list as $chk_role_name=>$chk_vars_list){
                     if($role_name == $chk_role_name){
                         // 同一のロール内のチェックはスキップする。
                         continue;
                     }
                     if(@count($ina_vars_list[$chk_role_name][$var_name]) != 0){
                         // エラーになった変数とロールを退避
                         $ina_err_vars_list[$var_name][$chk_role_name] = 0;
                         $ina_err_vars_list[$var_name][$role_name]     = 0;
                         $ret_code = false;
                     }
                 }
             }
         }
         // 同じ多次元変数が他ロールにある場合に構造が同じか判定する。
         foreach($ina_def_array_vars_list as $role_name=>$vars_list){
             foreach($vars_list as $var_name=>$chl_vars_list){
                 foreach($ina_def_array_vars_list as $chk_role_name=>$chk_vars_list){
                     if($role_name == $chk_role_name){
                         // 同一のロール内のチェックはスキップする。
                         continue;
                     }
                     // 他ロールに同じ多次元変数がある場合
                     if(@count($ina_def_array_vars_list[$chk_role_name][$var_name]) != 0){
                         // 多次元構造を比較する。
                         $diff_vars_list = array();
                         $diff_vars_list[0] = $ina_def_array_vars_list[$role_name][$var_name]['DIFF_ARRAY'];
                         $diff_vars_list[1] = $ina_def_array_vars_list[$chk_role_name][$var_name]['DIFF_ARRAY'];
                         $error_code = "";
                         $line       = "";
                         $ret = $this->InnerArrayDiff($diff_vars_list,$error_code,$line);
                         if($ret === false){
                             // エラーになった変数とロールを退避
                             $ina_err_vars_list[$var_name][$chk_role_name] = 0;
                             $ina_err_vars_list[$var_name][$role_name]     = 0;
                             $ret_code = false;
                         }
                     }
                 }
             }
         }
         // 変数検索  ロール=>変数名
         foreach($ina_vars_list as $role_name=>$vars_list){
             if( is_array($vars_list) ){
                 if(@count($vars_list) !== 0){
                     // 変数名リスト=>変数名
                     foreach($vars_list as $var_name=>$var_type){
                         // 多次元配列に同じ変数名があるか判定
                         if(@count($ina_def_array_vars_list[$chk_role_name][$var_name])!==0){
                             // エラーになった変数とロールを退避
                             $ina_err_vars_list[$var_name][$chk_role_name] = 0;
                             $ina_err_vars_list[$var_name][$role_name]     = 0;
                             $ret_code = false;
                         }
                         // 他のロールで同じ変数名で構造が異なるのものがあるか確認
                         foreach($ina_vars_list as $chk_role_name=>$chk_vars_list){
                             if($role_name == $chk_role_name){
                                 // 同一のロール内のチェックはスキップする。
                                 continue;
                             }
                             
                             if(@count($ina_vars_list[$chk_role_name][$var_name])===0){
                                 // 同じ変数名なし
                                 continue;
                             }
                             else{
                                 // 配列変数以外の場合に一般変数と複数具体値変数の違いを判定
                                 if($ina_vars_list[$chk_role_name][$var_name] != 
                                    $ina_vars_list[$role_name][$var_name]){
                                     // エラーになった変数とロールを退避
                                     $ina_err_vars_list[$var_name][$chk_role_name] = 0;
                                     $ina_err_vars_list[$var_name][$role_name]     = 0;
                                     $ret_code = false;
                                 }
                             }
                         }
                     }
                }
            }
        }
        return $ret_code;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1003
    // 処理内容
    //   指定されているロールパッケージ内のデェフォルト変数ファイルで定義されている配列変数の
    //   構造が一致しているか判定
    //
    // パラメータ
    //   $ina_vars_list:          　defalte変数ファイルの変数リスト格納
    //                            　　非配列変数　ina_vars_list[ロールパッケージ名][ロール名][変数名] = 0;
    //                          　    配列変数　  ina_vars_list[ロールパッケージ名][ロール名][変数名] = array(配列変数名, ....)
    //   $ina_def_array_vars_list:  defalte変数ファイルの多次元変数リスト格納
    //   $ina_err_vars_list:　      ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                        　      in_err_vars_list[変数名][ロールパッケージ名][ロール名]
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkallVarsStruct( $ina_vars_list, $ina_def_array_vars_list, &$ina_err_vars_list){
         $ret_code = true;
         $in_err_vars_list = array();

         // 多次元変数をKeyに他ロールに多次元変数以外の変数があるか判定
         foreach($ina_def_array_vars_list as $pkg_name=>$role_list){
             foreach($role_list as $role_name=>$vars_list){
                 foreach($vars_list as $var_name=>$chl_vars_list){
                     // 他のロールで同じ変数名で構造が異なるのものがあるか確認
                     foreach($ina_vars_list as $chk_pkg_name=>$chk_role_list){
                         foreach($chk_role_list as $chk_role_name=>$chk_vars_list){
                             // 同一ロールパッケージ+ロールのチェックはスキップする。
                             if(($pkg_name == $chk_pkg_name) &&
                                ($role_name == $chk_role_name)){
                                 // 同一のロール内のチェックはスキップする。
                                 continue;
                             }
                             if(@count($ina_vars_list[$chk_pkg_name][$chk_role_name][$var_name]) != 0){
                                 // エラーになった変数とロールを退避
                                 $ina_err_vars_list[$var_name][$pkg_name][$role_name] = 0;
                                 $ina_err_vars_list[$var_name][$chk_pkg_name][$chk_role_name] = 0;
                                 $ret_code = false;
                             }
                         }
                     }
                 }
             }
         }
         // 同じ多次元変数が他ロールにある場合に構造が同じか判定する。
         foreach($ina_def_array_vars_list as $pkg_name=>$role_list){
             foreach($role_list as $role_name=>$vars_list){
                 foreach($vars_list as $var_name=>$chl_vars_list){
                     foreach($ina_def_array_vars_list as $chk_pkg_name=>$chk_role_list){
                         foreach($chk_role_list as $chk_role_name=>$chk_vars_list){
                             // 同一ロールパッケージ+ロールのチェックはスキップする。
                             if(($pkg_name == $chk_pkg_name) &&
                                ($role_name == $chk_role_name)){
                                 // 同一のロール内のチェックはスキップする。
                                 continue;
                             }
                             // 他ロールに同じ多次元変数がある場合
                             if(@count($ina_def_array_vars_list[$chk_pkg_name][$chk_role_name][$var_name]) != 0){
                                 // 多次元構造を比較する。
                                 $diff_vars_list = array();
                                 $diff_vars_list[0] = $ina_def_array_vars_list[$pkg_name][$role_name][$var_name]['DIFF_ARRAY'];
                                 $diff_vars_list[1] = $ina_def_array_vars_list[$chk_pkg_name][$chk_role_name][$var_name]['DIFF_ARRAY'];
                                 $error_code = "";
                                 $line       = "";
                                 $ret = $this->InnerArrayDiff($diff_vars_list,$error_code,$line);
                                 if($ret === false){
                                     // エラーになった変数とロールを退避
                                     $ina_err_vars_list[$var_name][$pkg_name][$role_name] = 0;
                                     $ina_err_vars_list[$var_name][$chk_pkg_name][$chk_role_name] = 0;
                                     $ret_code = false;
                                 }
                             }
                         }
                     }
                 }
             }
         }
         // 多次元変数以外をKeyに他ロールに多次元変数以外の変数があるか判定
         // 変数検索  ロールパッケージ名=>ロールリスト
         foreach($ina_vars_list as $pkg_name=>$role_list){
             // 変数検索  ロール=>変数名リスト
             foreach($role_list as $role_name=>$vars_list){
                 if( is_array($vars_list) ){
                     if(@count($vars_list) !== 0){
                         // 変数名リスト=>変数名
                         foreach($vars_list as $var_name=>$var_type){
                             // 多次元変数に同じ変数名があるか判定
                             if(@count($ina_def_array_vars_list[$chk_pkg_name][$chk_role_name][$var_name])!=0){
                                 // エラーになった変数とロールを退避
                                 $ina_err_vars_list[$var_name][$pkg_name][$role_name] = 0;
                                 $ina_err_vars_list[$var_name][$chk_pkg_name][$chk_role_name] = 0;
                                 $ret_code = false;
                                 continue;
                             }
                             // 他ロールパッケージ変数検索  ロールパッケージ名=>ロールリスト
                             foreach($ina_vars_list as $chk_pkg_name=>$chk_role_list){
                                 // 他のロールで同じ変数名で構造が異なるのものがあるか確認
                                 foreach($chk_role_list as $chk_role_name=>$chk_vars_list){
                                     // 同一ロールパッケージ+ロールのチェックはスキップする。
                                     if(($pkg_name  == $chk_pkg_name) &&
                                        ($role_name == $chk_role_name)){
                                         continue;
                                     }
                                     if(@count($chk_vars_list[$var_name])===0){
                                         // 同じ変数名なし
                                         continue;
                                     }
                                     // 一般変数と複数具体値変数の違いを判定
                                     if($ina_vars_list[$chk_pkg_name][$chk_role_name][$var_name] !=
                                        $ina_vars_list[$pkg_name][$role_name][$var_name]){
                                         // エラーになった変数とロールを退避
                                         $ina_err_vars_list[$var_name][$pkg_name][$role_name] = 0;
                                         $ina_err_vars_list[$var_name][$chk_pkg_name][$chk_role_name] = 0;
                                         $ret_code = false;
                                     }
                                 }
                             }
                         }
                     }
                 }
             }
         }
         return $ret_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F1004
    // 処理内容
    //   配列変数の構造が違う場合のエラーメッセージを編集
    //
    // パラメータ
    //   $ina_err_vars_list:      ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                              in_err_vars_list[変数名][ロール名]
    //
    // 戻り値
    //   エラーメッセージ
    ////////////////////////////////////////////////////////////////////////////////
    function VarsStructErrmsgEdit( $ina_err_vars_list){
         //ok $ary[70052] = "default変数ファイルに登録されている変数の属性が不一致。\n";
         $errmsg   = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70052");
         // $err_vars_list[変数名][ロール名]
         foreach($ina_err_vars_list as $err_var_name=>$err_role_list){
             $err_files = "";
             foreach($err_role_list as $err_role_name=>$dummy){
                 $err_files = $err_files . "roles/" . $err_role_name . "\n";
             }
             if($err_files != ""){
                 $errmsg = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70053",
                                                                      array($err_var_name,$err_files));
             }
         }
         return $errmsg;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1005
    // 処理内容
    //   配列変数の構造が違う場合のエラーメッセージを編集
    //
    // パラメータ
    //   $ina_err_vars_list:      ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                              in_err_vars_list[変数名][ロールパッケージ名][ロール名]
    //
    // 戻り値
    //   エラーメッセージ
    ////////////////////////////////////////////////////////////////////////////////
    function allVarsStructErrmsgEdit( $ina_err_vars_list){
         $errmsg   = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70052");
         foreach($ina_err_vars_list as $err_var_name=>$err_pkg_list){
             $err_files = "";
             foreach($err_pkg_list as $err_pkg_name=>$err_role_list){
                 foreach($err_role_list as $err_role_name=>$dummy){
                     $err_files = $err_files . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70055",
                                                                 array($err_pkg_name));

                     $err_files = $err_files . "roles/" . $err_role_name . "\n";
                 }
             }
             if($err_files != ""){
                 $errmsg = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70053",
                                                             array($err_var_name,$err_files));
             }
         }
         return $errmsg;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1006
    // 処理内容
    //   ロールパッケージ内のPlaybookで定義されている変数がデェフォルト変数定義
    //   ファイルで定義されているか判定
    //
    // パラメータ
    //   $ina_play_vars_list:     ロールパッケージ内のPlaybookで定義している変数リスト
    //                              [role名][変数名]=0
    //   $ina_def_vars_list:      defalte変数ファイルの変数リスト
    //                            　非配列変数　ina_vars_list[ロール名][変数名] = 0;
    //                              配列変数　  ina_vars_list[ロール名][変数名] = array(配列変数名, ....)
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkDefVarsListPlayBookVarsList( $ina_play_vars_list, $ina_def_vars_list,$ina_def_array_vars_list, &$in_errmsg, $ina_system_vars){
         $in_errmsg = "";
         $ret_code  = true;
         // ロールパッケージ内のPlaybookで定義している変数が無い場合は処理しない。
         if(count($ina_play_vars_list) == 0){
             return $ret_code;
         }
         foreach($ina_play_vars_list as $role_name=>$vars_list){
             foreach($vars_list as $vars_name=>$dummy){
                 // ITA独自変数はチェック対象外とする。
                 if(in_array($vars_name,$ina_system_vars) === true){
                     continue;
                 }

                 if(@count($ina_def_vars_list[$role_name][$vars_name])===0){
                     if(@count($ina_def_array_vars_list[$role_name][$vars_name])===0){
                         $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70058",
                                                                            array($role_name,$vars_name));
                         $ret_code  = false;
                     }
                 }
             }
        }
        return $ret_code;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1007
    // 処理内容
    //   デフォルト定義変数ファイルとユーザー義変数ファイルに定義されている変数
    //   の情報をマージする。
    //
    //   $ina_vars_list:          デフォルト定義変数ファイル内に定義されている変数リスト
    //                            　非配列変数　ina_vars_list[変数名]
    //                              配列変数　  ina_vars_list[変数名] = array(配列変数名, ....)
    //   $ina_vars_val_list:      デフォルト定義変数ファイル内に定義されている変数具体値リスト
    //                              一般変数
    //                                $ina_vars_val_list[変数名][0]=具体値
    //                              複数具体値変数
    //                                $ina_vars_val_list[変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //   $ina_user_vars_list:     ユーザー義変数ファイル内に定義されている変数リスト
    //                            　非配列変数　ina_vars_list[変数名]
    //                              配列変数　  ina_vars_list[変数名] = array(配列変数名, ....)
    //   $ina_user_vars_val_list: ユーザー義変数ファイル内に定義されている変数具体値リスト
    //                              一般変数
    //                                $ina_vars_val_list[変数名][0]=具体値
    //                              複数具体値変数
    //   $ina_array_vars_list:    デフォルト定義変数ファイル内に定義されている多次元変数リスト
    //   $ina_user_array_vars_list: ユーザー義変数ファイル内に定義されている多次元変数リスト
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function margeDefaultVarsList( &$ina_vars_list,     &$ina_vars_val_list,
                                   $ina_user_vars_list, $ina_user_vars_val_list,
                                   &$ina_array_vars_list, $ina_user_array_vars_list){
        if(@count($ina_user_vars_list) != 0){
            // ユーザー変数定義ファイルに登録されている変数をキーにループ
            foreach($ina_user_vars_list as $var_name=>$vars_list){
                // default変数定義ファイルに変数が登録されているか判定
                if(@count($ina_vars_list[$var_name]) != 0){
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー変数定義ファイルとdefault変数定義ファイルの両方にある変数のルート\n");
                    // default変数定義ファイルに変数が登録されている
                
                    // 変数の型を判定する。
                    if($ina_vars_list[$var_name] != $ina_user_vars_list[$var_name]){
                        // 変数の型が一致しない場合はdefault変数定義ファイルの変数具体値情報から該当変数の情報を削除する。
                        unset($ina_vars_val_list[$var_name]);
                    }
                    // default変数定義ファイルの変数情報から該当変数の情報を削除する。
                    unset($ina_vars_list[$var_name]);
               
                    // default変数定義ファイルの変数情報にユーザー変数定義ファイルに
                    // 登録されている変数情報を追加
                    $ina_vars_list[$var_name] = $ina_user_vars_list[$var_name];

                    // ユーザー変数定義ファイルの変数具体値情報は使わない                

                    // ユーザー変数定義ファイルの変数情報から削除
                    unset($ina_user_vars_list[$var_name]);
                }
                else{
                    // default変数定義ファイルに変数が登録されていない

                    // default変数定義ファイルに多次元変数として登録されているか判定
                    if(@count($ina_array_vars_list[$var_name]) != 0){
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー変数定義ファイルとdefault多次元変数定義ファイルの両方にある変数のルート\n");
                        // default変数定義ファイルの多次元変数の情報を削除
                        unset($ina_array_vars_list[$var_name]);
                    }
                    else{
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー変数定義ファイルにあるがdefault変数定義ファイルのない変数のルート\n");
                    }

                    // default変数定義ファイルの変数情報にユーザー変数定義ファイルに
                    // 登録されている変数情報を追加
                    $ina_vars_list[$var_name] = $ina_user_vars_list[$var_name];

                    // ユーザー変数定義ファイルの変数情報から削除
                    unset($ina_user_vars_list[$var_name]);

                    // ユーザー変数定義ファイルの変数具体値情報は使わない                
                }
            }
        }
        if(@count($ina_user_array_vars_list) != 0){
            // ユーザー変数定義ファイルに登録されている多次元変数をキーにループ
            foreach($ina_user_array_vars_list as $var_name=>$vars_list){
                // default変数定義ファイルに多次元変数が登録されているか判定
                if(@count($ina_array_vars_list[$var_name]) != 0){
                    // default変数定義ファイルに多次元変数が登録されている
                
                    // 変数構造が同じか判定する。
                    // 多次元構造を比較する。
                    $diff_vars_list = array();
                    $diff_vars_list[0] = $ina_array_vars_list[$var_name]['DIFF_ARRAY'];
                    $diff_vars_list[1] = $ina_user_array_vars_list[$var_name]['DIFF_ARRAY'];
                    $error_code = "";
                    $line       = "";
                    $ret = $this->InnerArrayDiff($diff_vars_list,$error_code,$line);
                    if($ret === false){
                        // 変数構造が一致しない
                        // ユーザー変数定義ファイルの情報をdefault変数定義ファイルに設定
                        unset($ina_array_vars_list[$var_name]);
                        $ina_array_vars_list[$var_name] = $ina_user_array_vars_list[$var_name];
    
                        // 具体値はなしにする。
                        unset($ina_array_vars_list[$var_name]['VAR_VALUE']);
                        $ina_array_vars_list[$var_name]['VAR_VALUE'] = array();
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー多次元変数定義ファイルとdefault多次元変数定義ファイルの両方にあり型が一致しない変数のルート\n");
                    }
                    else{
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー多次元変数定義ファイルとdefault多次元変数定義ファイルの両方にあり型が一致する変数のルート\n");
                        // 変数の構造が同じなのでdefault変数定義ファイルの内容をそのまま使う
                    }
 
                    // ユーザー変数定義ファイルの変数情報を削除する。
                    unset($ina_user_array_vars_list[$var_name]);
                }
                else{
                    // default変数定義ファイルに多次元変数が登録されていない

                    // default変数定義ファイルに変数が登録されているか判定
                    if(@count($ina_vars_list[$var_name]) != 0){
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー多次元変数定義ファイルとdefault変数定義ファイルの両方にある変数のルート\n");
                        // default変数定義ファイルに変数が登録されている
                    
                        // default変数定義ファイルの変数情報から該当変数の情報を削除する。
                        unset($ina_vars_list[$var_name]);

                        // default変数定義ファイルの変数具体値情報から該当変数の情報を削除する。
                        unset($ina_vars_val_list[$var_name]);
                
                        // default変数定義ファイルの変数情報にユーザー変数定義ファイルに
                        // 登録されている多次元変数情報を追加
                        $ina_array_vars_list[$var_name] = $ina_user_array_vars_list[$var_name];
    
                        // ユーザー変数定義ファイルの変数具体値情報は使わない                
                        unset($ina_array_vars_list[$var_name]['VAR_VALUE']);
                        $ina_array_vars_list[$var_name]['VAR_VALUE'] = array();

                        // ユーザー変数定義ファイルの変数情報から削除
                        unset($ina_user_vars_list[$var_name]);
                    }
                    else{
$this->debuglog(__LINE__,"[" . $var_name . "] ユーザー多次元変数定義ファイルにのみある変数のルート\n");

                        // default変数定義ファイルに変数が登録されていない

                        // default変数定義ファイルの変数情報にユーザー変数定義ファイルに
                        // 登録されている変数情報を追加
                        $ina_array_vars_list[$var_name] = $ina_user_array_vars_list[$var_name];
    
                        // 具体値はなしにする。
                        unset($ina_array_vars_list[$var_name]['VAR_VALUE']);
                        $ina_array_vars_list[$var_name]['VAR_VALUE'] = array();

                        // ユーザー変数定義ファイルの変数情報を削除する。
                        unset($ina_user_array_vars_list[$var_name]);
                    }
                }
            }
        }
        return;
    }
    function debuglog($line,$msg){

    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1008
    // 処理内容
    //   defalte変数ファイルを親変数毎に分解
    //
    // パラメータ
    //   $in_string:              defalte変数ファイルの内容
    //   $ina_parent_vars_list:   defalte変数ファイルの親変数毎に分解配列
    //                            []['LINE']                   親変数の行番号
    //                              ['VAR_NAME']               変数名
    //                              ['VAR_TYPE']               変数タイプ
    //                                                           self::LC_VAR_TYPE_ITA / self::LC_VAR_TYPE_USER / self::LC_VAR_TYPE_USER_ITA
    //                              ['VAR_STYLE']              変数型
    //                                                           self::LC_VAR_STYLE_STD etc
    //                              ['DATA'][][LINE]           行番号
    //                                        ['EDIT_SKIP']      編集により不要となった行(true)/default(false)
    //                                        [MARK_POS]       先頭からの - の位置　-がない場合は-1
    //                                        [VAR_POS]        先頭からの変数文字列の位置
    //                                        [VAL_POS]        先頭からの具体値文字列の位置
    //                                        [DATA]           行データ
    //                                        [VAR_NAME]       変数名
    //                                        [VAR_VAL]        具体値
    //                                        [VAR_DEF]        変数定義有無(true)/(false)
    //                                        [VAL_DEF]        具体値定義有無(true)/(false)
    //                                        [ARRAY_VAR_DEF]  配列変数定義有無(true)/(false)
    //                                                         - { }
    //                                        [ZERO_LIST_DEF]  複数具体値初期値定義(true)/(false)
    //                                                         []
    //                                        [ZERO_ARRAY_DEF] 配列変数初期値定義(true)/(false)
    //                                                         {}
    //   $in_role_name:           ロール名
    //   $in_file_name:           defalte変数ファイル名
    //   $ina_ITA2User_var_list   読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list   読替表の変数リスト　ユーザ変数=>ITA変数
    //   $in_errmsg:              エラー時のメッセージ格納
    //   $in_f_name:              ファイル名
    //   $in_f_line:              エラー発生行番号格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function FirstAnalysis($in_string,
                          &$ina_parent_vars_list,
                           $in_role_name, $in_file_name, 
                           $ina_ITA2User_var_list,  // 読替表の変数リスト 
                           $ina_User2ITA_var_list,  // 読替表の変数リスト
                          &$in_errmsg, &$in_f_name, &$in_f_line,
                           $in_msg_role_pkg_name)
    {
    
        $ina_parent_vars_list = array();
        // ファイル名
        $in_f_name = __FILE__;
        // 行番号
        $line = 0;
        // 変数定義のインデント数
        $var_pos = -1;
        // 変数解析結果退避
        $parent_vars_list = array();
        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_string);
        foreach($arry_list as $in_string){
            // 行番号
            $line = $line + 1;
            // コメント行解析
            if(mb_strpos($in_string,"#",0,"UTF-8") === 0){
                continue;
            }
            $wstr = $in_string;
            // コメント( #)マーク以降の文字列を削除する。
            // #の前の文字がスペースの場合にコメントとして扱う
            $wspstr = explode(" #",$wstr);
            $strRemainString = $wspstr[0];
            if( is_string($strRemainString)===true ){
                // 空行をスキップ
                if(strlen(trim($strRemainString))===0){
                    continue;
                }
                // ---は記載可能にする。
                $ret = preg_match('/^---(\s*)$/',$strRemainString,$matchi,PREG_OFFSET_CAPTURE);
                if ($ret == 1){
                    continue;
                }

                $error_code = "";
                // 読込んだ行の変数のインデントを取得
                $ret = $this->VarPosAnalysis($strRemainString,$wk_var_pos,$wk_mark_pos,$wk_val_pos,
                                      $wk_var_name,
                                      $wk_var_val,
                                      $wk_var_def,
                                      $wk_var_val_def,
                                      $wk_array_var_def,
                                      $wk_zero_list_def,
                                      $wk_zero_array_def,
                                      $error_code);
                if($ret === false){

                    if($error_code == ""){
                        $error_code = "ITAANSIBLEH-ERR-70044";
                    }
                    $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                    $error_code,
                                                    array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), $line));
                    $in_f_line   = __LINE__;
                    return false;
                }

                // 読込んだ行の情報を退避
                $info = array();
                $info['LINE']           = $line;
                $info['EDIT_SKIP']      = false;
                $info['MARK_POS']       = $wk_mark_pos;
                $info['VAR_POS']        = $wk_var_pos;
                $info['VAL_POS']        = $wk_val_pos;
                $info['DATA']           = $strRemainString;
                $info['VAR_NAME']       = $wk_var_name;
                $info['VAR_VAL']        = $wk_var_val;
                $info['VAR_DEF']        = $wk_var_def;
                $info['VAL_DEF']        = $wk_var_val_def;
                $info['ARRAY_VAR_DEF']  = $wk_array_var_def;
                $info['ZERO_LIST_DEF']  = $wk_zero_list_def;
                $info['ZERO_ARRAY_DEF'] = $wk_zero_array_def;

                // 最初の変数定義の判定
                if($var_pos == -1){
                    // 変数解析結果初期化
                    $parent_vars_list = array();
                    // 先頭からの文字数を退避
                    $var_pos = $wk_var_pos;

                    // 変数タイプと変数名を取得
                    $ret = $this->ParentVarAnalysis($strRemainString,$parent_vars_list,
                                                    $ina_ITA2User_var_list,  // 読替表の変数リスト 
                                                    $ina_User2ITA_var_list,  // 読替表の変数リスト
                                                    $line);
                    if($ret === false){
                        $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                        "ITAANSIBLEH-ERR-70044",
                                                        array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), $line));
                        $in_f_line   = __LINE__;
                        return false;
                    }
                }
                // インデント不正の判定
                //  xxxx: xxxx
                // xxxx: xxx
                else if(($var_pos > $wk_var_pos) && ($wk_var_pos != -1)){
                    $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                    "ITAANSIBLEH-ERR-70044",
                                                    array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), $line));
                    $in_f_line   = __LINE__;
                    return false;
                }
                // 別変数定義の判定
                else if($var_pos == $wk_var_pos){
                    // 変数解析結果退避
                    array_push($ina_parent_vars_list,$parent_vars_list);
                    // 変数解析結果初期化
                    $parent_vars_list = array();

                    // 変数タイプと変数名を取得
                    $ret = $this->ParentVarAnalysis($strRemainString,$parent_vars_list,
                                                    $ina_ITA2User_var_list,  // 読替表の変数リスト
                                                    $ina_User2ITA_var_list,  // 読替表の変数リスト
                                                    $line);
                    if($ret === false){
                        $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                        "ITAANSIBLEH-ERR-70044",
                                                        array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), $line));
                        $in_f_line   = __LINE__;
                        return false;
                    }
                }
                // 変数定義を退避
                array_push($parent_vars_list['DATA'],$info);
            }
        }
        // 最終定義の変数の情報を登録
        if(count($parent_vars_list) != 0){
            // 変数解析結果退避
            array_push($ina_parent_vars_list,$parent_vars_list);
        }
        $ret = true;
        $parent_vars = array();
        foreach($ina_parent_vars_list as $parent_vars_info) {
            $var_name = $parent_vars_info['VAR_NAME'];
            if(isset($parent_vars[$var_name])) {
                if(@strlen($in_errmsg) != 0) $in_errmsg .= "\n";
                $in_errmsg .= AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                 "ITAANSIBLEH-ERR-6000016",
                                                 array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), $var_name));
                $in_f_line   = __LINE__;
                $ret = false;
            }
            $parent_vars[$parent_vars_info['VAR_NAME']] = "";
        }    
        return $ret;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F1009
    // 処理内容
    //   ITA変数かユーザー変数かを判定
    //
    // パラメータ
    //   $in_string:              変数定義文字列
    //   $in_var_name:            定義されている変数名
    //   $in_match:               正規表記結果
    //   $ina_ITA2User_var_list:  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list:  読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   ITA変数:    self::LC_VAR_TYPE_ITA
    //   USER変数:   self::LC_VAR_TYPE_USER
    //   読替変数:   self::LC_VAR_TYPE_USER_ITA
    //   false:      異常
    ////////////////////////////////////////////////////////////////////////////////
    function VarTypeAnalysis($in_string,&$in_var_name,&$in_match,
                             $ina_ITA2User_var_list,
                             $ina_User2ITA_var_list){
        $in_var_name = "";
        // ITA変数かユーザー変数かを判定
        // ITA変数か判定　　　　 /^VAR_(\S+)(\s*):/
        $ret = preg_match_all(self::LC_VARNAME_MATCHING,trim($in_string),$in_match);
        if($ret == 1){
            // 変数名退避
            $in_var_name = preg_replace("/(\s*):(\s*)$/","",$in_match[0][0]); 

            // ITA変数
            return self::LC_VAR_TYPE_ITA;
        }
        else{
            // ユーザー変数か判定　　/^[a-zA-Z0-9_]*(\s*):/
            $ret = preg_match_all(self::LC_USER_VARNAME_MATCHING,trim($in_string),$in_match);
            if($ret == 1){
                // 変数名退避
                $in_var_name = preg_replace("/(\s*):(\s*)$/","",$in_match[0][0]); 

                // 読替表にある変数はITA変数として扱う
                if(@count($ina_User2ITA_var_list[$in_var_name]) != 0){
                    // 読替変数
                    return self::LC_VAR_TYPE_USER_ITA;
                }
                else{
                    // USER変数
                    return self::LC_VAR_TYPE_USER;
                }
            }
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1010
    // 処理内容
    //   行先頭から-までの文字数と変数名までの文字数を取得
    //
    // パラメータ
    //   $in_string:              変数定義文字列
    //   $in_var_pos:             行先頭から変数名までの文字数
    //                            -がない場合は -1 を返す。
    //   $in_mark_pos:            行先頭から-までの文字数
    //                            -がない場合は -1 を返す。
    //   $in_val_pos:             先頭からの具体値文字列の位置
    //                            -がない場合は -1 を返す。
    //   $in_var_name:            変数名
    //   $in_var_val:             具体値
    //   $in_var_def:             変数定義の有(true)/無(false)
    //   $in_var_val_def:          具体値定義の有(true)/無(false)
    //   $in_array_var_def:       配列変数定義有無(true)/(false)      - {}
    //   $in_zero_list_def:       複数具体値初期値定義(true)/(false)  xxx: []
    //   $in_zero_array_def:      配列変数初期値定義(true)/(false)    xxx: {}
    //                            現在未使用
    //   $in_error_code:          エラーコード
    // 戻り値
    //   false:      異常
    ////////////////////////////////////////////////////////////////////////////////
    function VarPosAnalysis( $in_string,&$in_var_pos,&$in_mark_pos,&$in_val_pos,
                            &$in_var_name,
                            &$in_var_val,
                            &$in_var_def,
                            &$in_var_val_def,
                            &$in_array_var_def,
                            &$in_zero_list_def,
                            &$in_zero_array_def,
                            &$in_error_code){
        $in_mark_pos       = -1;
        $in_var_pos        = -1;
        $in_val_pos        = -1;
        $in_var_name       = "";
        $in_var_val        = "";
        $in_var_def        = false;
        $in_var_val_def    = false;
        $in_array_var_def  = false;
        $in_zero_list_def  = false;
        $in_zero_array_def = false;
        $in_error_code     = "";

        // Spycが誤解析する文法をはじく
        // - のみの定義か判定
        $ret = preg_match('/^(\s*)-(\s+)$/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
        if($ret == 1){
            // Spycが - のみの定義は誤解釈するので禁止
            $in_error_code = "ITAANSIBLEH-ERR-70076"; 
            return false;
        }
        // - {} の定義か判定
        $ret = preg_match_all('/^(\s*)-(\s+){(\s*)}(\s*)$/',$in_string,$matchi);
        if($ret == 1){
            // Spycが - {} の定義は誤解釈するので禁止
            $in_error_code = "ITAANSIBLEH-ERR-70080"; 
            return false;
        }
        // - {} の定義か判定
        $ret = preg_match_all('/^(\s*)-(\s+)\[(\s*)\](\s*)$/',$in_string,$matchi);
        if($ret == 1){
            // Spycが - {} の定義は誤解釈するので禁止
            $in_error_code = "ITAANSIBLEH-ERR-70081"; 
            return false;
        }

        $ret = preg_match('/^(\s*)-(\s*)$/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
        if($ret == 1){
            // Spycが - のみの定義は誤解釈するので禁止
            $in_error_code = "ITAANSIBLEH-ERR-70076"; 
            return false;
        }
        // 配列変数 - { ... } の定義か判定
        $ret = preg_match('/^(\s*)-(\s*){/',$in_string,$haifun_matchi,PREG_OFFSET_CAPTURE);
        if($ret == 1){
            // -の後ろにスペースがないとエラー
            $ret = preg_match('/^(\s*)-(\s+){/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 0){
                return false;
            }
            // } で終わっていないとエラー
            $ret = preg_match('/}(\s*)$/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 0){
                return false;
            }
            $ret = preg_match('/^(\s*)-(\s+){(\s*)}(\s*)$/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                // Spycが - {} の定義は誤解釈するので禁止
                $in_error_code = "ITAANSIBLEH-ERR-70080"; 
                return false;
            }
            $in_array_var_def = true;
            // 行先頭から-までの文字数取得
            $in_mark_pos = strpos($haifun_matchi[0][0],"-",0);
            // Spycが配列の具体値を省略すると(xxx: ,)誤解釈をするので禁止
            $ret = preg_match_all('/[a-zA-Z0-9_]*(\s*):(\s*),/',$in_string,$matchi);
            if($ret > 0){
                $in_error_code = "ITAANSIBLEH-ERR-70077"; 
                return false;
            }
            $ret = preg_match_all('/[a-zA-Z0-9_]*(\s*):(\s*)}/',$in_string,$matchi);
            if($ret > 0){
                $in_error_code = "ITAANSIBLEH-ERR-70077"; 
                return false;
            }
            $ret = preg_match_all('/[a-zA-Z0-9_]*(\s*):(\s*),(\s*)}/',$in_string,$matchi);
            if($ret > 0){
                $in_error_code = "ITAANSIBLEH-ERR-70077"; 
                return false;
            }
            $ret = preg_match_all('/,(\s*)}(\s*)/',$in_string,$matchi);
            if($ret > 0){
                $in_error_code = "ITAANSIBLEH-ERR-70079"; 
                return false;
            }
            return true;
        }
        $ret = preg_match('/^(\s*)-(\s*)/',$in_string,$haifun_matchi,PREG_OFFSET_CAPTURE);
        if($ret == 1){
            // 行先頭から-までの文字数取得
            $in_mark_pos = strpos($haifun_matchi[0][0],"-",0);
            // 変数定義か判定
            $ret = preg_match('/^(\s*)-(\s*)[a-zA-Z0-9_]*(\s*):/',$in_string,$var_matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                // : の後ろはスペースがなにいとダメ
                $ret = preg_match('/^(\s*)-(\s*)[a-zA-Z0-9_]*(\s*):(\S)/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
                if($ret == 1){
                    return false;
                }
                // - の後ろにスペースがないとエラー
                $ret = preg_match('/^(\s*)-[a-zA-Z0-9_]*(\s*):/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
                if($ret == 1){
                    return false;
                }

                // 行先頭から変数名までの文字数取得
                $in_var_pos  = strlen($haifun_matchi[0][0]);
                // 変数名を取り出す
                $in_var_name = preg_replace("/(\s*):(\s*)$/","",$var_matchi[0][0]);
                $in_var_name = preg_replace("/^(\s*)-(\s+)/","",$in_var_name);
                $in_var_def = true;
                // 具体値取り出し
                $in_var_val = preg_replace("/^(\s*)-(\s+)[a-zA-Z0-9_]*(\s*):(\s*)/","",$in_string);
                $in_var_val = trim($in_var_val);
                if(strlen($in_var_val) != 0){
                    // 具体値定義あり
                    $in_var_val_def = true;
                }
            }
            else{
                $ret = preg_match('/^(\s*)-(\s+)(\S)/',$in_string,$val_matchi,PREG_OFFSET_CAPTURE);
                if($ret == 1){
                    $in_var_val = preg_replace("/^(\s*)-(\s+)/","",$in_string);
                    $in_var_val = trim($in_var_val);
                    if(strlen($in_var_val) != 0){
                        // 具体値定義あり
                        $in_var_val_def = true;
                    }
                    // 具体値定義位置取得
                    $in_val_pos  = strlen($val_matchi[0][0]) - 1;
                }
                else{
                    return false;
                }
            }
        }
        else{
            $ret = preg_match('/^(\s*)[a-zA-Z0-9_]*(\s*):(\S)/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                return false;
            }
            $ret = preg_match('/^(\s*)[a-zA-Z0-9_]*(\s*):(\s*)/',$in_string,$var_matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                // 行先頭から変数名までの文字数取得
                $ret = preg_match('/(\S)/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
                if($ret == 1){
                    $in_var_pos  = $matchi[0][1];
                }
                // 変数名を取り出す
                $in_var_name = preg_replace("/(\s*):(\s*)$/","",$var_matchi[0][0]);
                $in_var_name = trim($in_var_name);
                $in_var_def = true;
                // 具体値取り出し
                $in_var_val = preg_replace("/^(\s*)[a-zA-Z0-9_]*(\s*):(\s*)/","",$in_string);
                $in_var_val = trim($in_var_val);
                if(strlen($in_var_val) != 0){
                    // 具体値定義あり
                    $in_var_val_def = true;
                }
            }
            else{
                // 変数定義(英数字_以外の文字を含む変数名)か判定(xxxxx:)
                $ret = preg_match('/^(\s*)(\S+)(\s*):/',$in_string,$matchi,PREG_OFFSET_CAPTURE);
                if($ret == 1){
                    return false;
                }
                else {
                    $ret = preg_match('/^(\s*)(\S)/',$in_string,$val_matchi,PREG_OFFSET_CAPTURE);
                    if($ret == 1){
                        $in_var_val = trim($in_string);
                        if(strlen($in_var_val) != 0){
                            // 具体値定義あり
                            $in_var_val_def = true;
                        }
                        // 具体値定義位置取得
                        $in_val_pos  = strlen($val_matchi[0][0]) - 1;
                    }
                    else{
                        return false;
                    }
                }
            }
        }
        if($in_var_val_def === true){
            // 具体値が特殊か判定  []  {}
            $ret = preg_match('/^(\s*)\[(\s*)\](\s*)$/',$in_var_val,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                $in_zero_list_def = true;
            }
            $ret = preg_match('/^(\s*){(\s*)}(\s*)$/',$in_var_val,$matchi,PREG_OFFSET_CAPTURE);
            if($ret == 1){
                $in_error_code = "ITAANSIBLEH-ERR-70078"; 
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1011
    // 処理内容
    //   行先頭から-までの文字数と変数名までの文字数を取得
    //
    // パラメータ
    //   $in_string:              変数定義文字列
    //   $ina_var_Info_list:      defalte変数ファイルの親変数毎に分解配列
    //                            ['LINE']                   親変数の行番号
    //                            ['VAR_NAME']               変数名
    //                            ['VAR_TYPE']               変数タイプ
    //                                                         self::LC_VAR_TYPE_ITA / self::LC_VAR_TYPE_USER / self::LC_VAR_TYPE_USER_ITA
    //                            ['DATA'][][LINE]           行番号
    //                                      [MARK_POS]       先頭からの - の位置　-がない場合は-1
    //                                      [VAR_POS]        先頭からの変数文字列の位置
    //                                      [VAL_POS]        先頭からの具体値文字列の位置
    //                                      [DATA]           行データ
    //                                      [VAR_NAME]       変数名
    //                                      [VAR_VAL]        具体値
    //                                      [VAR_DEF]        変数定義有無(true)/(false)
    //                                      [VAL_DEF]        具体値定義有無(true)/(false)
    //                                      [ARRAY_VAR_DEF]  配列変数定義有無(true)/(false)
    //                                                       - { }
    //                                      [ZERO_LIST_DEF]  複数具体値初期値定義(true)/(false)
    //                                                       []
    //                                      [ZERO_ARRAY_DEF] 配列変数初期値定義(true)/(false)
    //                                                       {}
    //                                                       現在未使用
    //   $ina_ITA2User_var_list   読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list   読替表の変数リスト　ユーザ変数=>ITA変数
    //   $in_line:                親変数の行番号
    //
    // 戻り値
    //   false:      異常
    ////////////////////////////////////////////////////////////////////////////////
    function ParentVarAnalysis($in_string,&$ina_var_Info_list,
                               $ina_ITA2User_var_list,  // 読替表の変数リスト
                               $ina_User2ITA_var_list,  // 読替表の変数リスト
                               $in_line){
        // 変数タイプと変数名を取得
        $match = array();
        $ret = $this->VarTypeAnalysis($in_string,$var_name,$match,
                                      $ina_ITA2User_var_list,  // 読替表の変数リスト
                                      $ina_User2ITA_var_list); // 読替表の変数リスト
        if($ret === false){
            return false;
        }
        // 行番号
        $ina_var_Info_list = array();
        $ina_var_Info_list['LINE'] =     $in_line;
        // 変数タイプ   ITA変数(VAR_)/ユーザー変数
        $ina_var_Info_list['VAR_TYPE'] = $ret;
        // 変数名
        $ina_var_Info_list['VAR_NAME'] = $var_name;
        $ina_var_Info_list['DATA']     = array();

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1012
    // 処理内容
    //   defalte変数ファイルに定義さけている変数の情報で、
    //   ITAの文法に反する定義をエラーにする。
    //   Spyc.phpで誤解析する可能性がある文法を調整する。
    //
    // パラメータ
    //   $ina_parent_vars_list:   defalte変数ファイルの親変数毎に分解配列
    //                            []['LINE']                     親変数の行番号
    //                              ['VAR_NAME']                 変数名
    //                              ['VAR_TYPE']                 変数タイプ
    //                                                           self::LC_VAR_TYPE_ITA / self::LC_VAR_TYPE_USER / self::LC_VAR_TYPE_USER_ITA
    //                              ['DATA'][]['LINE]            行番号
    //                                        ['EDIT_SKIP']      編集により不要となった行(true)/default(false)
    //                                        ['MARK_POS']       先頭からの - の位置　-がない場合は-1
    //                                        ['VAR_POS']        先頭からの変数文字列の位置
    //                                        ['VAL_POS']        先頭からの具体値文字列の位置
    //                                        ['DATA']           行データ
    //                                        ['VAR_NAME']       変数名
    //                                        ['VAR_VAL']        具体値
    //                                        ['VAR_DEF']        変数定義有無(true)/(false)
    //                                        ['VAL_DEF']        具体値定義有無(true)/(false)
    //                                        ['ARRAY_VAR_DEF']  配列変数定義有無(true)/(false)
    //                                                         - { }
    //                                        ['ZERO_LIST_DEF']  複数具体値初期値定義(true)/(false)
    //                                                         []
    //                                        ['ZERO_ARRAY_DEF'] 配列変数初期値定義(true)/(false)
    //                                                         {}
    //                                                         現在未使用
    //
    //   $in_role_name:           ロール名
    //   $in_file_name:           defalte変数ファイル名
    //   $in_errmsg:              エラー時のメッセージ格納
    //   $in_f_name:              ファイル名
    //   $in_f_line:              エラー発生行番号格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function MiddleAnalysis(&$ina_parent_vars_list,
                             $in_role_name, $in_file_name, &$in_errmsg, &$in_f_name, &$in_f_line,
                             $in_msg_role_pkg_name) // #1241 2017/09/22 Append
    {
        // ファイル名
        $in_f_name = __FILE__;
        $array_line = 0;
        foreach($ina_parent_vars_list as $parent_vars_list){
            ///////////////////////////////////////////////
            // ITAの文法に反する定義をエラーにする。
            ///////////////////////////////////////////////
            // 具体値が複数行にまたがっている場合はエラー
            if(is_array($parent_vars_list['DATA'])){
                $row_count = count($parent_vars_list['DATA']);
                for($idx = 0;$idx < $row_count;$idx++){
                    // 具体値定義がある行か判定
                    if($parent_vars_list['DATA'][$idx]['VAL_DEF'] === true){
                        if($idx != ($row_count - 1)){
                            // 次の行が具体値のみの定義か判定
                            if(($parent_vars_list['DATA'][$idx + 1]['VAL_DEF'] === true) &&
                               ($parent_vars_list['DATA'][$idx + 1]['MARK_POS'] === -1 ) &&
                               ($parent_vars_list['DATA'][$idx + 1]['VAR_DEF'] === false)){
                                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                                "ITAANSIBLEH-ERR-70075",
                                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name), 
                                                                      $parent_vars_list['DATA'][$idx]['LINE']));
                                $in_f_line   = __LINE__;
                                return false;
                            }
                        }
                    }
                }
            }
            ///////////////////////////////////////////////
            // Spycで誤解析する可能性がある文法を調整する。
            ///////////////////////////////////////////////
            // 配列変数のカンマの位置を調整 VAR1: VAL.VAR2: ...  => VAR1: VAL. VAR2: ...
            if(is_array($parent_vars_list['DATA'])){
                $row_count = count($parent_vars_list['DATA']);
                for($idx = 0;$idx < $row_count;$idx++){
                    if($ina_parent_vars_list[$array_line]['DATA'][$idx]['ARRAY_VAR_DEF'] === true){
                        $string = $ina_parent_vars_list[$array_line]['DATA'][$idx]['DATA'];
                        $ret = preg_match_all('/,[a-zA-Z0-9_]*(\s*):(\s+)/',$string,$match);
                        if($ret > 0){
                            foreach($match[0] as $var_string){
                                $upd_var_string = preg_replace('/^,/',', ',$var_string);
                                $string = str_replace($var_string,$upd_var_string,$string);
                            }
                        }
                        $ina_parent_vars_list[$array_line]['DATA'][$idx]['DATA'] = $string;
                    }
                }
            }
            // 変数と具体値が別々の行にまたがっている場合に1行にまとめる
            if(is_array($parent_vars_list['DATA'])){
                $row_count = count($parent_vars_list['DATA']);
                for($idx = 0;$idx < $row_count;$idx++){
                    // 変数のみの定義
                    if(($parent_vars_list['DATA'][$idx]['VAR_DEF'] === true) &&
                       ($parent_vars_list['DATA'][$idx]['VAL_DEF'] === false)){
                        if($idx != ($row_count - 1)){
                            // 具体値のみの定義か判定
                            if(($parent_vars_list['DATA'][$idx + 1]['VAL_DEF']  === true ) &&
                               ($parent_vars_list['DATA'][$idx + 1]['MARK_POS'] === -1   ) &&
                               ($parent_vars_list['DATA'][$idx + 1]['VAR_DEF']  === false)){
                                // 変数と具体値を結合する
                                $ina_parent_vars_list[$array_line]['DATA'][$idx]['DATA'] =
                                $ina_parent_vars_list[$array_line]['DATA'][$idx]['DATA'] . " " . 
                                $ina_parent_vars_list[$array_line]['DATA'][$idx + 1]['VAR_VAL'];

                                $ina_parent_vars_list[$array_line]['DATA'][$idx]['VAL_DEF'] = true;
                                $ina_parent_vars_list[$array_line]['DATA'][$idx]['VAR_VAL'] = 
                                $ina_parent_vars_list[$array_line]['DATA'][$idx + 1]['VAR_VAL'];

                                // 具体値の行を無効にする
                                $ina_parent_vars_list[$array_line]['DATA'][$idx + 1]['EDIT_SKIP'] = true;
                            }
                        }
                    }
                }
            }
            $array_line++;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1013
    // 処理内容
    //   ITAで加工したdefalte変数ファイルの内容を
    //   一時ファイルに出力する。
    //
    // パラメータ
    //   $in_tmp_file_name:           一時ファイル名
    //   $ina_parent_vars_list:   defalte変数ファイルの親変数毎に分解配列
    //   $in_role_name:           ロール名
    //   $in_file_name:           defalte変数ファイル名
    //   $in_errmsg:              エラー時のメッセージ格納
    //   $in_f_name:              ファイル名
    //   $in_f_line:              エラー発生行番号格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateTempDefaultsVarsFile($in_tmp_file_name,$ina_parent_vars_list,
                                        $in_role_name, $in_file_name, &$in_errmsg, &$in_f_name, &$in_f_line,
                                        $in_msg_role_pkg_name) // #1241 2017/09/22 Append
    {
        $in_f_name = __FILE__;

        $fd = fopen($in_tmp_file_name, "w");
        if($fd == null){
            $in_errmsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70082");
            $in_f_line = __LINE__;

            return false;
        }

        // 加工されたdefalte変数ファイルの情報を一時ファイルに出力
        $row_count = @count($ina_parent_vars_list['DATA']);
        for($idx = 0;$idx < $row_count;$idx++){
            // 無効データか判定
            if($ina_parent_vars_list['DATA'][$idx]['EDIT_SKIP'] === true){
                continue;
            }
            // 改行を付けて一時ファイルに出力
            $string = $ina_parent_vars_list['DATA'][$idx]['DATA'] . "\n";
            if( @fputs($fd, $string) === false ){
                fclose($fd);
                $in_errmsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70083");
                $in_f_line = __LINE__;

                return false;
            }
        }
        fclose($fd);
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1014
    // 処理内容
    //   Spycモジュールの読み込み
    //
    // パラメータ
    //   $in_errmsg:              エラー時のメッセージ格納
    //   $in_f_name:              ファイル名
    //   $in_f_line:              エラー発生行番号格納
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function LoadSpycModule(&$in_errmsg, &$in_f_name, &$in_f_line){
        global $root_dir_path;

        $in_f_name = __FILE__;

        // Spycモジュールのパスを取得
        $spyc_path = @file_get_contents($root_dir_path . "/confs/commonconfs/path_PHPSpyc_Classes.txt");
        if($spyc_path === false){
            $in_errmsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70084");
            $in_f_line = __LINE__;
            return false;
        }
        // 改行コードが付いている場合に取り除く
        $spyc_path = str_replace("\n","",$spyc_path);
        $spyc_path = $spyc_path . "/Spyc.php";
        if( file_exists($spyc_path) === false ){
            $in_errmsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70085");
            $in_f_line = __LINE__;
            return false;
        }
        require ($spyc_path);

        return true;
    }
    // F1015
    function LastAnalysis($in_tmp_file_name,$ina_parent_vars_list,&$ina_vars_list,&$ina_varsval_list,
                         &$ina_array_vars_list,
                          $in_role_name, $in_file_name, &$in_errmsg, &$in_f_name, &$in_f_line,
                          $in_msg_role_pkg_name)
    {
        // 加工されたdefalte変数ファイルの情報を一時ファイルに出力
        foreach($ina_parent_vars_list as $parent_vars_list){
            $ret = $this->CreateTempDefaultsVarsFile($in_tmp_file_name,$parent_vars_list,
                                                     $in_role_name, $in_file_name, 
                                                     $in_errmsg, $in_f_name, $in_f_line,
                                                     $in_msg_role_pkg_name);
            if($ret === false){
                return false;
            }
            $var_array = array();
            try {
                $var_array = Spyc::YAMLLoad($in_tmp_file_name);
            } catch ( Exception $ex ) {
                @unlink($in_tmp_file_name);
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                "ITAANSIBLEH-ERR-6000029",
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $parent_vars_list['LINE']));
                $in_f_line = __LINE__;
                return false;
            }
            @unlink($in_tmp_file_name);

            if(is_array($var_array)){
                if(@count($var_array) != 1){
                    $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                    "ITAANSIBLEH-ERR-70086",
                                                    array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                          $parent_vars_list['LINE']));

                    $in_f_line = __LINE__;

                    return false;
                }
                foreach($var_array as $parent_var=>$val1);
            }
            else{
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                "ITAANSIBLEH-ERR-70086",
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $parent_vars_list['LINE']));
                $in_f_line = __LINE__;

                return false;
            }
            // 一般変数か判定
            $ret = $this->chkStandardVariable($parent_var,$var_array[$parent_var],$ina_vars_list,$ina_varsval_list,$parent_vars_list['VAR_TYPE']);
            if($ret === true){
                continue;
            }
            // 複数具体値変数か判定
            $ret = $this->chkMultiValueVariable($parent_var,$var_array[$parent_var],$ina_vars_list,$ina_varsval_list,$parent_vars_list['VAR_TYPE']);
            if($ret === true){
                continue;
            }
            // 多次元配列変数か判定　配列変数も多次元配列として扱う
            $ret = $this->chkMultiArrayVariable($parent_var,$var_array[$parent_var],$ina_vars_list,$ina_varsval_list,
                                                $parent_vars_list['VAR_TYPE'],$parent_vars_list['LINE'],
                                                $ina_array_vars_list,
                                                $in_role_name, $in_file_name, 
                                                $in_errmsg, $in_f_name, $in_f_line,
                                                $in_msg_role_pkg_name);
            if($ret === true){
                continue;
            }
            return false;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1016
    // 処理内容
    //   Spycから取得した配列構造が一般変数か判定
    // パラメータ
    //   $in_var_array:               Spycから取得した配列構造
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function chkStandardVariable($in_var,$in_var_array,&$ina_vars_list,&$ina_varsval_list,$in_var_type){
        if( ! is_array($in_var_array)){
            // VAR_か読替変数のみ変数情報退避
            if(($in_var_type == self::LC_VAR_TYPE_ITA ||
                $in_var_type == self::LC_VAR_TYPE_USER_ITA) ||
               ($this->GetRunModeVarFile() == LC_RUN_MODE_VARFILE))
            {

                $ina_vars_list[$in_var] = 0;
                $ina_varsval_list[$in_var][0] = $in_var_array;
            }
            return true;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F1017
    // 処理内容
    //   Spycから取得した配列構造が複数具体値の変数か判定
    // パラメータ
    //   $in_var_array:               Spycから取得した配列構造
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function chkMultiValueVariable($in_var,$in_var_array,&$ina_vars_list,&$ina_varsval_list,$in_var_type){
        $ret = $this->chkMultiValueVariableSub($in_var_array);
        if($ret === false)
            return false;

        // VAR_か読替変数のみ変数情報退避
        if(($in_var_type == self::LC_VAR_TYPE_ITA ||
            $in_var_type == self::LC_VAR_TYPE_USER_ITA) ||
           ($this->GetRunModeVarFile() == LC_RUN_MODE_VARFILE))
        {
            $ina_vars_list[$in_var] = 1;
            if(count($in_var_array) == 0){
            }
            $line = 1;
            foreach($in_var_array as $chk_array){
                $ina_varsval_list[$in_var][1][$line] = $chk_array;
                $line++;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1018
    // 処理内容
    //   配列構造が複数具体値の変数か判定
    // パラメータ
    //   $in_var_array:               Spycから取得した配列構造
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function chkMultiValueVariableSub($in_var_array){
        if(is_array($in_var_array)){
            if(count($in_var_array) == 0){
                return true;
            }
            foreach($in_var_array as $key => $chk_array){
                if( ! is_numeric($key)){
                    return false;
                }
                if(is_array($chk_array)){
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1019
    // 処理内容
    //   Spycから取得した配列構造を解析する。
    // パラメータ
    //   $in_var:                     親変数名
    //   $in_var_array:               Spycから取得した配列構造
    //   $ina_vars_list:              現在未使用
    //   $ina_varsval_list:           現在未使用
    //   $in_var_type:                変数区分 VAR_かどうか
    //   $in_var_line:                デフォルト定義ファイルに定義されている行番号
    //   $ina_array_vars_list:        配列構造の解析結果
    //   $in_role_name:               対象のロール名
    //   $in_file_name:               対象のパッケージファイル名 
    //   $in_errmsg:                  エラー時のエラーメッセージ
    //   $in_f_name:                  エラー時のファイル名
    //   $in_f_line:                  エラー時の行番号
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function chkMultiArrayVariable($in_var,$in_var_array,&$ina_vars_list,&$ina_varsval_list,
                                   $in_var_type,$in_var_line,
                                  &$ina_array_vars_list,
                                   $in_role_name, $in_file_name, 
                                  &$in_errmsg, &$in_f_name, &$in_f_line,
                                   $in_msg_role_pkg_name)
    {
        $in_f_line = __FILE__;
        if(is_array($in_var_array)){
            $ret = $this->is_assoc($in_var_array);
            if($ret == -1){
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                "ITAANSIBLEH-ERR-70087",
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $in_var_line));
                $in_f_line = __LINE__;
                return false;
            }

            $col_count    = 0;
            $assign_count = 0;
            $error_code   = "";
            $line         = "";           
            $diff_vars_list = array();
            $varval_list = array();
            $array_col_count_list = array();
            // Spycが取得した多次元配列の構造から具体値を排除する。また配列階層の配列数と具体値を取得する。
            $ret = $this->MakeMultiArrayToDiffMultiArray($in_var_array,
                                                         $diff_vars_list,
                                                         $varval_list,
                                                         "",
                                                         $array_col_count_list,
                                                         "", //配列要素番号
                                                         $error_code,$line,$col_count,$assign_count);
            if($ret === false){
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                $error_code,
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $in_var_line));

                $in_f_line = $line;
                return false;
            }

            $error_code = "";
            $line       = "";
            $ret = $this->InnerArrayDiff($diff_vars_list,$error_code,$line);
            if($ret === false){
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                $error_code,
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $in_var_line));
                $in_f_line = $line;
                return false;
            }

            $col_count      = 0;
            $assign_count   = 0;
            $error_code     = "";
            $line           = "";
            $parent_var_key = 0;
            $chl_var_key    = 0;
            $nest_lvl       = 0;
            $vars_chain_list = array();
            
            $chain_make_array = $in_var_array;

            $ret = $this->MakeMultiArrayToFirstVarChainArray(false,
                                                             "",
                                                             "",
                                                             $chain_make_array,
                                                             $vars_chain_list,
                                                             $error_code,
                                                             $line,
                                                             $col_count,
                                                             $assign_count,
                                                             $parent_var_key,
                                                             $chl_var_key,
                                                             $nest_lvl);
            if($ret === false){
                $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                $error_code,
                                                array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                      $in_var_line));
                $in_f_line = $line;
                return false;
            }

            // VAR_か読替変数のみ変数情報退避
            if(($in_var_type == self::LC_VAR_TYPE_ITA ||
                $in_var_type == self::LC_VAR_TYPE_USER_ITA) ||
               ($this->GetRunModeVarFile() == LC_RUN_MODE_VARFILE))
            {
                $vars_last_chain_list = array();
                $ret = $this->MakeMultiArrayToLastVarChainArray($vars_chain_list,$array_col_count_list,$vars_last_chain_list,$error_code,$line);
                if($ret === false){
                     $in_errmsg = AnsibleMakeMessage($this->lv_objMTS,$this->GetRunModeVarFile(),
                                                     $error_code,
                                                     array($in_msg_role_pkg_name, $in_role_name, basename($in_file_name),
                                                           $in_var_line));
                     $in_f_line = $line;
                     return false;

                }

                // 多次元変数構造比較用配列を退避
                $ina_array_vars_list[$in_var]['DIFF_ARRAY']     = $diff_vars_list;

                // 多次元変数親子関係のチェーン構造を退避
                $ina_array_vars_list[$in_var]['CHAIN_ARRAY']    = $vars_last_chain_list;

                // 配列階層の配列数を退避
                $ina_array_vars_list[$in_var]['COL_COUNT_LIST'] = $array_col_count_list;

                // 各変数の具体値を退避
                $ina_array_vars_list[$in_var]['VAR_VALUE']      = $varval_list;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1020
    // 処理内容
    //   Spycから取得し配列構造にはメンバー変数の具体値が含まれているので、
    //   具体値を取り除き配列数が1の配列構造(ina_vars_list)を作成する。
    //   各メンバー変数の具体値をina_varval_listに退避する。
    //   各配列階層の配列数退避をina_array_col_count_listに退避する。
    // パラメータ
    //   $ina_parent_var_array:       Spycから取得し配列構造
    //   $ina_vars_list:              具体値を取り除き配列数が1の配列構造退避
    //   $ina_varval_list:            各メンバー変数の具体値退避
    //   $in_var_name_path:           1つ前の階層までのメンバー変数のパス
    //   $ina_array_col_count_list:   配列階層の配列数退避
    //   $in_col_index_str:           各メンバー変数が属している配列の位置(1配列毎に3桁で位置を表した文字列)
    //   $in_error_code:              エラー時のエラーコード
    //   $in_line:                    エラー時の行番号格納
    //   $in_col_count:               現在未使用
    //   $in_assign_count:            現在未使用
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function MakeMultiArrayToDiffMultiArray($ina_parent_var_array,&$ina_vars_list,&$ina_varval_list,$in_var_name_path,&$ina_array_col_count_list,$in_col_index_str,&$in_error_code,&$in_line,&$in_col_count,&$in_assign_count){
        $demiritta_ch = ".";

        // 配列階層か判定
        $array_f = $this->is_assoc($ina_parent_var_array);
        if($array_f == -1){
            $in_error_code = "ITAANSIBLEH-ERR-70087";
            $in_line       = __LINE__;
            return false;
        }
        foreach($ina_parent_var_array as $var => $val) {
            // 具体値の配列の場合の判定
            // 具体値の配列の場合は具体値が全てとれない模様
            // - xxxx1
            //   - xxxx2
            // - xxxx3
            // array(2) {
            //    [0]=>
            //      array(1) {
            //      [0]=>
            //        string(5) "xxxxx2"
            if(is_numeric($var)) {
                // 具体値の配列の場合の判定
                $ret = $this->is_assoc($val);
                if($ret == "I"){
                    $in_error_code = "ITAANSIBLEH-ERR-70090";
                    $in_line       = __LINE__;
                    return false;
                }
                // 配列階層の配列数または複数具体値の具体値数が99999999以上あった場合はエラーにする。
                if($var >= 99999999)   // 0からなので $var >= 99999999
                {
                    // 配列階層の配列数が99999999以上あった
                    if($array_f == "I"){
                        $in_error_code = "ITAANSIBLEH-ERR-90218";
                        $in_line       = __LINE__;
                        return false;
                    }
                    else{
                    }
                }
            }
            // 複数具体値か判定する。
            if(is_numeric($var)) {
                // 具体値がある場合は排除する。
                if( ! is_array($val)){
                    // 代入順序を1オリジンにする。
                    $ina_varval_list[$in_var_name_path][1][$in_col_index_str][($var + 1)]=$val;
                    continue;
                }
            }
            // 配列階層か判定
            if($array_f == 'I'){
                // 配列階層の列番号を退避 各配列の位置を3桁の数値文字列で結合していく 
                #$wk_col_index_str = $in_col_index_str . sprintf("%03d",$var);                
                $wk_col_index_str = $in_col_index_str . sprintf("%08d",$var);                

                // 配列階層の場合の変数名を設定 変数名を0に設定する。
                if($in_var_name_path == ""){
                    $wk_var_name_path = '0';
                }
                else{
                    $wk_var_name_path = $in_var_name_path . $demiritta_ch . '0';
                }
                if(@count($ina_array_col_count_list[$wk_var_name_path]) == 0){
                    // 配列階層の配列数を退避
                    $ina_array_col_count_list[$wk_var_name_path] = count($ina_parent_var_array);
                }
            }
            else{
                // 配列階層の列番号を退避
                $wk_col_index_str = $in_col_index_str;

                // 配列階層の以外の場合の変数名を設定
                if($in_var_name_path == ""){
                    $wk_var_name_path = $var;
                }
                else{
                    $wk_var_name_path = $in_var_name_path . $demiritta_ch . $var;
                }
            }
            $ina_vars_list[$var] = array();
            // Key-Value変数か判定
            if( ! is_array($val)) {
                // 具体値がある場合は排除する。
                $ina_varval_list[$wk_var_name_path][0][$wk_col_index_str]=$val;
                continue;
            }
            $ret = $this->MakeMultiArrayToDiffMultiArray($val,$ina_vars_list[$var],$ina_varval_list,$wk_var_name_path,$ina_array_col_count_list,$wk_col_index_str,$in_error_code,$in_line,$in_col_count,$in_assign_count);
            if($ret === false){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1021
    // 処理内容
    //   指定された配列の構造が一致しているか判定
    //   左辺の内容が右辺に含まれているか
    //   同一かどうかは左右入れ替えて確認する
    // パラメータ
    //   $in_arrrayLeft:       左辺の配列構造
    //   $in_arrayRight:       右辺の配列構造
    //   $in_diff_array:       一致していない場合の簡易エラー情報
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function MultiArrayDiff($in_arrrayLeft,$in_arrayRight,&$in_diff_array){
        $diff = false;
        if (is_array($in_arrrayLeft)){
            foreach($in_arrrayLeft as $key => $item){
                if (@is_array($in_arrayRight[$key]) === false){
                    $in_diff_array[$key] = "key is not found";
                    return false;
                }
                //配列なら再帰呼び出し
                if (is_array($item)){
                    $ret = $this->MultiArrayDiff($item,$in_arrayRight[$key],$in_diff_array);
                    if ($ret === false){
                        return false;
                    }
                }else{
                    $in_diff_array[$key] = "item is not array";
                    return false;
                }
            }
        }
        else{
            $in_diff_array["arrrayLeft"] = "arrrayLeft is not array";
            return false;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1022
    // 処理内容
    //   多次元変数で配列構造を含んでいる場合、各配列の定義が一致しているか判定
    // パラメータ
    //   $ina_parent_var_array:       多次元変数の構造
    //   $in_error_code:              エラー時のエラーコード
    //   $in_line:                    エラー時の行番号格納
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function InnerArrayDiff($ina_parent_var_array,&$in_error_code,&$in_line){
        $diff_array = array();
        if( ! @is_array($ina_parent_var_array)){
            return true;
        }
        $is_key_array = $this->is_assoc($ina_parent_var_array);
        if($is_key_array == -1){
            $in_error_code = "ITAANSIBLEH-ERR-70087";
            $in_line       = __LINE__;
            return false;
        }
        $idx = 0;
        foreach($ina_parent_var_array as $var1 => $val1){
            if(is_numeric($var1)){
                if(is_array($val1)){
                    if($idx != 0){
                        $diff_array = array();
                        $ret = $this->MultiArrayDiff($ina_parent_var_array[0],   $ina_parent_var_array[$idx],$diff_array);
                        if($ret === false){
                            $in_error_code = "ITAANSIBLEH-ERR-70089";
                            $in_line       = __LINE__;
                            return false;
                        }
                        $ret = $this->MultiArrayDiff($ina_parent_var_array[$idx],$ina_parent_var_array[0],$diff_array);
                        if($ret === false){
                            $in_error_code = "ITAANSIBLEH-ERR-70089";
                            $in_line       = __LINE__;
                            return false;
                        }
                    }
                }
            }
            $ret = $this->InnerArrayDiff($val1,$in_error_code,$in_line);
            if($ret === false){
                return false;
            }
            $idx++;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1023
    // 処理内容
    //   多次元変数の特定階層が配列か判定する。
    // パラメータ
    //   $in_array:                   多次元変数の特定階層
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function is_assoc( $in_array ) {
        $key_int  = false;
        $key_char = false;
        if (!is_array($in_array)) 
            return -1;
        $keys = array_keys($in_array);
        foreach ($keys as $i => $value) {
            if (!is_int($value)){
                $key_char = true;
            }
            else{
                $key_int = true;
            }
        }
        if(($key_char === true) && ($key_int === true)){
            return -1;
        }
        if($key_char === true){
            return "C";
        }
        return "I";
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1024
    // 処理内容
    //   多次元変数の特定階層がKey-Value形式か判定する。
    // パラメータ
    //   $in_array:                   多次元変数の特定階層
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function is_stroc( $in_array ) {
        $vals = array_values($in_array);
        foreach ($vals as $value) {
            if (!is_string($value)){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1025
    // 処理内容
    //   多次元変数の構造を解析。ina_vars_chain_listに解析データを退避する。
    // パラメータ
    //   $in_fastarry_f:              配列定義内かを判定
    //   $in_var_name:                1つ前の階層のメンバー変数
    //   $in_var_name_path:           1つ前の階層のメンバー変数のパス
    //   $ina_parent_var_array:       多次元変数の階層構造
    //   $ina_vars_chain_list:        多次元変数の解析データ格納
    //   $in_error_code:              エラー時のエラーコード
    //   $in_line:                    エラー時の行番号格納
    //   $in_col_count:               未使用
    //   $in_assign_count:            未使用
    //   $ina_parent_var_key:         1つ前の階層のメンバー変数のID（0～）
    //   $in_chl_var_key:             同一階層の1つ前のメンバー変数のID（0～）
    //   $in_nest_lvl:                階層レベル（1～）
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function MakeMultiArrayToFirstVarChainArray($in_fastarry_f,
                                                $in_var_name,
                                                $in_var_name_path,
                                                $ina_parent_var_array,
                                               &$ina_vars_chain_list,
                                               &$in_error_code,
                                               &$in_line,
                                               &$in_col_count,
                                               &$in_assign_count,
                                                $ina_parent_var_key,
                                               &$in_chl_var_key,
                                                $in_nest_lvl){
        $demiritta_ch = ".";
        $in_nest_lvl++;
        $parent_var_key = $ina_parent_var_key;
        $ret = $this->is_assoc($ina_parent_var_array);
        if($ret == -1){
            $in_error_code = "ITAANSIBLEH-ERR-70087";
            $in_line       = __LINE__;
            return false;
        }
        $fastarry_f_on = false;
        foreach($ina_parent_var_array as $var => $val) {
            $col_array_f = "";
            // 複数具体値の場合
            if(is_numeric($var)) {
                if( ! is_array($val)){
                    continue;
                }
                else{
                    $col_array_f = "I";
                }
            }
            $MultiValueVar_f = $this->chkMultiValueVariableSub($val);
            if(strlen($in_var_name) != 0){
                $wk_var_name_path = $in_var_name_path . $demiritta_ch . $var;
                if(is_numeric($var) === false)
                    $wk_var_name = $in_var_name . $demiritta_ch . $var;
                else
                    $wk_var_name = $in_var_name;
            }
            else{
                $wk_var_name_path = $var;
                if(is_numeric($var) === false)
                    $wk_var_name = $var;
                else
                    $wk_var_name = $var;
            }
            // 配列の開始かを判定する。
            if($col_array_f == "I"){
                if($in_fastarry_f === false){
                    $in_fastarry_f = true;
                    $fastarry_f_on = true;
                }
            }               
            $in_chl_var_key++;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME']       = $var;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['NEST_LEVEL']     = $in_nest_lvl;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['LIST_STYLE']     = "0";
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME_PATH']  = $wk_var_name_path;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME_ALIAS'] = $wk_var_name;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['ARRAY_STYLE']    = "0";
            $MultiValueVar_f = $this->chkMultiValueVariableSub($val);
            if($MultiValueVar_f===true){
                $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['LIST_STYLE'] = "5";
            }
            // 配列の中の変数の場合
            if($in_fastarry_f === true){
                $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['ARRAY_STYLE'] = "1";
            }
            if( ! is_array($val)) {
                continue;
            }
            $ret = $this->MakeMultiArrayToFirstVarChainArray($in_fastarry_f,
                                                             $wk_var_name,
                                                             $wk_var_name_path,
                                                             $val,
                                                             $ina_vars_chain_list,
                                                             $in_error_code,
                                                             $in_line,
                                                             $in_col_count,
                                                             $in_assign_count,
                                                             $in_chl_var_key,
                                                             $in_chl_var_key,
                                                             $in_nest_lvl);
            if($ret === false){
                return false;
            }
            // 配列開始のマークを外す
            if($fastarry_f_on === true){
                $in_fastarry_f = false;
            }               
            if(is_numeric($var)){
                if($var === 0){
                    break;
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1026
    // 処理内容
    //   多次元変数の各メンバー構造で代入値管理系で列順序と代入順序が必要となる変数をマークする。
    //   配列の場合に配列数を設定する。
    // パラメータ
    //   $ina_first_vars_chain_list:    
    //   $array_col_count_list:
    //   $ina_vars_chain_list:
    //   $in_error_code:              エラー時のエラーコード
    //   $in_line:                    エラー時の行番号格納
    //   
    // 戻り値
    //   true: 正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function MakeMultiArrayToLastVarChainArray($ina_first_vars_chain_list,$array_col_count_list,&$ina_vars_chain_list,&$in_error_code,&$in_line){
        // 代入値管理系で列順序になる変数の候補にマークする。と代入順序が必要な変数を設定する。
        $ina_vars_chain_list = array();
        foreach($ina_first_vars_chain_list as $parent_vars_key_id=>$chl_vars_list){
            foreach($chl_vars_list as $vars_key_id=>$vars_info){
                $info_array = array();
                $info_array['PARENT_VARS_KEY_ID'] = $parent_vars_key_id;
                $info_array['VARS_KEY_ID']        = $vars_key_id;
                $info_array['VARS_NAME']          = $vars_info["VAR_NAME"];
                $info_array['ARRAY_NEST_LEVEL']   = $vars_info["NEST_LEVEL"];
                // 複数具体値なので代入順序が必要なのでマークする。
                if($vars_info['LIST_STYLE'] != 0){
                    $info_array['ASSIGN_SEQ_NEED']      = "1";
                }
                else{
                    $info_array['ASSIGN_SEQ_NEED']      = "0";
                }
                // 配列変数より下の階層にある変数なので列順序になる変数の候補にマークする。
                if($vars_info['ARRAY_STYLE'] != 0){
                    $info_array['COL_SEQ_MEMBER']       = "1";
                }
                else{
                    $info_array['COL_SEQ_MEMBER']       = "0";
                }
                $info_array['COL_SEQ_NEED']         = "0";
                $info_array['MEMBER_DISP']          = "0";
                $info_array['VRAS_NAME_PATH']       = $vars_info["VAR_NAME_PATH"];
                $info_array['VRAS_NAME_ALIAS']      = $vars_info["VAR_NAME_ALIAS"];

                // 配列階層(変数名が0)の場合に配列数を設定する。
                if($info_array['VARS_NAME'] == "0"){
                    if(@count($array_col_count_list[$info_array['VRAS_NAME_PATH']]) == 0){
                        $in_error_code = "ITAANSIBLEH-ERR-90220"; 
                        $in_line       = __LINE__;
                        return false;
                    }
                    else{
                        $info_array['MAX_COL_SEQ']     = $array_col_count_list[$info_array['VRAS_NAME_PATH']];
                    }
                }
                else{
                    $info_array['MAX_COL_SEQ']         = "0";
                }
                $ina_vars_chain_list[] = $info_array;
                unset($info_array);
            }
        }
        // 代入値管理系で表示する変数をマークする。列順序が必要な変数をマークする。
        $row_count = count($ina_vars_chain_list);
        $var_key_list = array();
        for($idx=0;$idx<$row_count;$idx++){
            $var_key_list[] = $ina_vars_chain_list[$idx]['VARS_KEY_ID'];
        }
        // 自分より下の階層がない変数を表示対象にする。
        for($key_idx=0;$key_idx < count($var_key_list);$key_idx++){
            $hit = false;
            for($idx=0;$idx<$row_count;$idx++){
                // 自分より下の階層がある。
                if($var_key_list[$key_idx] == $ina_vars_chain_list[$idx]['PARENT_VARS_KEY_ID']){
                     $hit = true;
                     break;
                }
            }
            // 自分より下の階層がなかった。
            if($hit === false){
                // 代入値管理系に表示する変数なのでマークする。
                $ina_vars_chain_list[$key_idx]['MEMBER_DISP'] = "1";
                // 代入値管理系で列順序が必要なのでマークする。
                if($ina_vars_chain_list[$key_idx]['COL_SEQ_MEMBER'] == "1"){
                    $ina_vars_chain_list[$key_idx]['COL_SEQ_NEED'] = "1";
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1027
    // 処理内容
    //   ロールパッケージ内のPlaybookで定義されているグローバル変数が
    //   グローバル変数管理で定義されているか判定
    //
    // パラメータ
    //   $ina_play_global_vars_list:     ロールパッケージ内のPlaybookで定義している変数リスト
    //                                   [role名][変数名]=0
    //   $ina_global_vars_list:          グローバル変数管理の変数リスト
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkDefVarsListPlayBookGlobalVarsList($ina_play_global_vars_list, $ina_global_vars_list, &$in_errmsg){
         $in_errmsg = "";
         $ret_code  = true;
         if(count($ina_play_global_vars_list) == 0){
             return $ret_code;
         }
         foreach($ina_play_global_vars_list as $role_name=>$vars_list){
             foreach($vars_list as $vars_name=>$dummy){
                 if(@count($ina_global_vars_list[$vars_name])===0){
                     $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90242",
                                                                            array($role_name,$vars_name));
                     $ret_code  = false;
                 }
             }
        }
        return $ret_code;
    }
   ////////////////////////////////////////////////////////////////////////////////
    // F1028
    // 処理内容
    //   読替表より変数の情報を取得する。
    //
    // パラメータ
    //   $in_filepath:            読替表ファイルパス
    //   $ina_ITA2User_var_list:  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list:  読替表の変数リスト　ユーザ変数=>ITA変数
    //   $in_errmsg:              エラーメッセージリスト
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function readTranslationFile($in_filepath,&$ina_ITA2User_var_list,&$ina_User2ITA_var_list,&$in_errmsg){
        $in_errmsg = "";
        $ret_code  = true;
        $dataString = file_get_contents($in_filepath);
        $line = 0;
        // 入力データを行単位に分解
        $arry_list = explode("\n",$dataString);
        foreach($arry_list as $strSourceString){
            $line = $line + 1;
            // コメント行は読み飛ばす。
            if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
                continue;
            }
            // 空行を読み飛ばす。
            if(strlen(trim($strSourceString)) == 0){
                continue;
            }
            // 読替変数の構文を確認
            // LCA_[0-9,a-Z_*]($s*):($s+)playbook内で使用している変数名
            // 読替変数名の構文判定
            $ret = preg_match_all("/^(\s*)LCA_[a-zA-Z0-9_]*(\s*):(\s+)/",$strSourceString,$ita_var_match);
            if($ret == 1){
                // :を取除き、読替変数名取得
                $ita_var_name    = trim(str_replace(':','',$ita_var_match[0][0]));
                // 任意変数を取得
                $user_var_name = trim(preg_replace('/^(\s*)LCA_[a-zA-Z0-9_]*(\s*):(\s+)/','',$strSourceString));
                if(strlen($user_var_name) != 0){
                    // 任意変数がVAR_でないことを判定
                    $ret = preg_match_all("/^VAR_/",$user_var_name ,$user_var_match);
                    if($ret == 1){
                        $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000000",array(basename($in_filepath),$line));
                        $ret_code = false;
                        continue;
                    }
                }
                else{
                    $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000001",array(basename($in_filepath),$line));
                    $ret_code = false;
                    continue;
                }
            }
            else{
                $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000002",array(basename($in_filepath),$line));
                $ret_code = false;
                continue;
            }
            // 任意変数が重複登録の二重登録判定
            if(@count($ina_User2ITA_var_list[$user_var_name]) != 0){
                $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000003",array(basename($in_filepath),$user_var_name));
                $ret_code = false;
                continue;
            }
            else{
                $ina_User2ITA_var_list[$user_var_name] = $ita_var_name;
            }
            // 読替変数が重複登録の二重登録判定
            if(@count($ina_ITA2User_var_list[$ita_var_name]) != 0){
                $in_errmsg = $in_errmsg . "\n" . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000004",array(basename($in_filepath),$ita_var_name));
                $ret_code = false;
                continue;
            }
            else{
                $ina_ITA2User_var_list[$ita_var_name] = $user_var_name;
            }
        }
        return $ret_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F1029
    // 処理内容
    //   読替表に定義されている読替変数と任意変数の組合せが一意か判定
    //
    // パラメータ
    //   $ina_ITA2User_var_list:   読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list:   読替表の変数リスト　ユーザ変数=>ITA変数
    //   $ina_comb_err_vars_list:　ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkTranslationTableVarsCombination($ina_ITA2User_var_list, $ina_User2ITA_var_list,&$ina_comb_err_vars_list){
        $ret_code = true;
        $ina_comb_err_vars_list = array();

        // 読替変数と任意変数の組合せが一意か確認する。
        // 読替変数をキーに読替変数と任意変数の組合せを確認
        foreach($ina_ITA2User_var_list as $pkg_name=>$role_list){
            foreach($role_list as $role_name=>$vars_list){
                foreach($vars_list as $ita_vars_name=>$user_vars_name){
                    foreach($ina_ITA2User_var_list as $chk_pkg_name=>$chk_role_list){
                        foreach($chk_role_list as $chk_role_name=>$chk_vars_list){
                            // 同一ロールパッケージ+ロールのチェックはスキップする。
                            if(($pkg_name == $chk_pkg_name) &&
                               ($role_name == $chk_role_name)){
                                // 同一のロール内のチェックはスキップする。
                                continue;
                            }
                            foreach($chk_vars_list as $chk_ita_vars_name=>$chk_user_vars_name){
                                if(($ita_vars_name == $chk_ita_vars_name) &&
                                   ($user_vars_name != $chk_user_vars_name)){
                                    // エラーになった変数とロールを退避
                                    $ina_comb_err_vars_list['USER_VAR'][$ita_vars_name][$pkg_name][$role_name] = $user_vars_name;
                                    $ina_comb_err_vars_list['USER_VAR'][$ita_vars_name][$chk_pkg_name][$chk_role_name] = $chk_user_vars_name;
                                    $ret_code = false;
                                } 
                                // 読替変数が同じ場合は、以降のチェックをスキップ
                                if($ita_vars_name == $chk_ita_vars_name){
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        // 任意変数をキーに読替変数と任意変数の組合せを確認
        foreach($ina_User2ITA_var_list as $pkg_name=>$role_list){
            foreach($role_list as $role_name=>$vars_list){
                foreach($vars_list as $user_vars_name=>$ita_vars_name){
                    foreach($ina_User2ITA_var_list as $chk_pkg_name=>$chk_role_list){
                        foreach($chk_role_list as $chk_role_name=>$chk_vars_list){
                            // 同一ロールパッケージ+ロールのチェックはスキップする。
                            if(($pkg_name == $chk_pkg_name) &&
                               ($role_name == $chk_role_name)){
                                // 同一のロール内のチェックはスキップする。
                                continue;
                            }
                            foreach($chk_vars_list as $chk_user_vars_name=>$chk_ita_vars_name){
                                if(($user_vars_name == $chk_user_vars_name) &&
                                   ($ita_vars_name != $chk_ita_vars_name)){
                                    // エラーになった変数とロールを退避
                                    $ina_comb_err_vars_list['ITA_VAR'][$user_vars_name][$pkg_name][$role_name] = $ita_vars_name;
                                    $ina_comb_err_vars_list['ITA_VAR'][$user_vars_name][$chk_pkg_name][$chk_role_name] = $chk_ita_vars_name;
                                    $ret_code = false;
                                }
                                // 読替変数が同じ場合は、以降のチェックをスキップ
                                if($user_vars_name == $chk_user_vars_name){
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret_code;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1030
    // 処理内容
    //   読替表の読替変数と任意変数の組合せが一致していないエラーメッセージを編集
    //
    // パラメータ
    //   $in_pkg_flg:             パッケージ名表示有無
    //   $ina_comb_err_vars_list: ロールパッケージ内で使用している変数で構造が違う変数のリスト
    //                            array(2) {
    //                               ["USER_VAR"]=>
    //                                 array(1) {
    //                                   ["LCA_sample_02"]=>
    //                                   array(1) {
    //                                     ["dummy pkg"]=>
    //                                     array(2) {
    //                                       ["ITAOrigVar"]=>
    //                                       string(14) "user_sample_02"
    //                                       ["test"]=>
    //                                       string(14) "user_sample_05"
    //                                 } } }
    //                                 ["ITA_VAR"]=>
    //                                 array(1) {
    //                                   ["user_sample_03"]=>
    //                                   array(1) {
    //                                     ["dummy pkg"]=>
    //                                     array(2) {
    //                                       ["ITAOrigVar"]=>
    //                                       string(13) "LCA_sample_03"
    //                                       ["test"]=>
    //                                       string(13) "LCA_sample_04"
    //                               } } } }
    //
    // 戻り値
    //   エラーメッセージ
    ////////////////////////////////////////////////////////////////////////////////
    function TranslationTableCombinationErrmsgEdit($in_pkg_flg, $ina_comb_err_vars_list){
         $errmsg = "";
         $errmsg   = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000006");

         if(@count($ina_comb_err_vars_list["USER_VAR"])!=0){
             foreach($ina_comb_err_vars_list["USER_VAR"]  as $ita_vars_name=>$pkg_list){
                 foreach($pkg_list as $pkg_name=>$role_list){
                     foreach($role_list as $role_name=>$user_vars_name){
                         if($in_pkg_flg === true){
                             $errmsg   = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000009",array($pkg_name, $role_name, $ita_vars_name, $user_vars_name));
                         }
                         else{
                             $errmsg   = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000007",array($role_name, $ita_vars_name, $user_vars_name));
                         }
                     }
                 }
             }
         }
         if(@count($ina_comb_err_vars_list["ITA_VAR"])!=0){
             foreach($ina_comb_err_vars_list["ITA_VAR"]  as $user_vars_name=>$pkg_list){
                 foreach($pkg_list as $pkg_name=>$role_list){
                     foreach($role_list as $role_name=>$ita_vars_name){
                         if($in_pkg_flg === true){
                             $errmsg   = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000010",array($pkg_name, $role_name, $user_vars_name, $ita_vars_name));
                         }
                         else{
                             $errmsg   = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000008",array($role_name, $user_vars_name, $ita_vars_name));
                         }
                     }
                 }
             }
         }
         return $errmsg;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1031
    // 処理内容
    //   ロールパッケージから抜出した変数名を読替表の情報を置換える。
    //   任意変数=>読替変数
    //
    // パラメータ
    //   $ina_vars_list: ロールパッケージから抜出した変数情報
    //                   [ロール名][変数名].....
    //   $ina_User2ITA_var_list  読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   エラーメッセージ
    ////////////////////////////////////////////////////////////////////////////////
    function ApplyTranslationTable(&$ina_vars_list, $ina_User2ITA_var_list){
        $wk_ina_vars_list = array(); 
        foreach($ina_vars_list as $role_name=>$var_list){
            foreach($var_list as $vars_name=>$info_list){
                if(@count($ina_User2ITA_var_list[$role_name][$vars_name])==0){
                    $wk_ina_vars_list[$role_name][$vars_name] = $info_list;
                    continue;
                }
                $ita_vars_name = $ina_User2ITA_var_list[$role_name][$vars_name];
                $wk_ina_vars_list[$role_name][$ita_vars_name] = $info_list;
            }
        }
        $ina_vars_list = array();
        $ina_vars_list = $wk_ina_vars_list;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F1032
    // 処理内容
    //   テンプレートの変数定義かロールパッケージのdefault定義ファイルかを判別
    //
    // パラメータ
    //   $run_mode:  モード
    //               LC_RUN_MODE_VARFILE: テンプレートの変数定義
    //               LC_RUN_MODE_STD:     ロールパッケージのdefault定義ファイル
    //               
    //
    // 戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function SetRunModeVarFile($mode){
        $this->lv_run_mode = $mode;
    } 
    ////////////////////////////////////////////////////////////////////////////////
    // F1033
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
    function GetRunModeVarFile(){
        return($this->lv_run_mode);
    } 
}
/////////////////////////////////////////////////////////////////////////////////
//  C0003
//  処理概要
//    変数定義を解析する。
//
/////////////////////////////////////////////////////////////////////////////////
class YAMLFileAnalysis{
    protected   $lv_objMTS;
    protected   $lv_lasterrmsg;

    function __construct(&$in_objMTS){
        $this->lv_objMTS = $in_objMTS;
        $this->lv_lasterrmsg;
    }

    function SetLastError($p1,$p2,$p3){
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        $this->lv_lasterrmsg[0] = $p3;
        $this->lv_lasterrmsg[1] = "FILE:$p1 LINE:$p2 $p3";
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }

    function VarsFileAnalysis($in_mode,    
                              $in_yaml_file,
                             &$in_parent_vars_list,
                             &$ina_vars_list,
                             &$ina_array_vars_list,
                             &$ina_varval_list,   
                              $in_role_pkg_name,
                              $in_rolename,
                              $in_display_file_name,
                              $ina_ITA2User_var_list,
                              $ina_User2ITA_var_list) {
        // 対象ファイル名
        $defvarfile = $in_yaml_file;

        // 対象ファイルからデータ読込
        $dataString = file_get_contents($defvarfile);

        // 対象ファイルから変数取得
        $chkObj = new DefaultVarsFileAnalysis($this->lv_objMTS);

        // テンプレートの変数定義ファイルか
        // ロールパッケージのdefault定義ファイルかを判別する設定
        $chkObj->SetRunModeVarFile($in_mode);

        $vars_list = array();
        $errmsg = "";
        $f_line = "";
        $varsval_list = array();
        $array_vars_list    = array();
        $array_varsval_list = array();

        // Spycモジュールの読み込み
        $ret = $chkObj->LoadSpycModule($errmsg, $f_name, $f_line);
        if($ret === false){
            $errmsg = $errmsg . "(" . $f_line . ")";
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
            return(false);
        }

        $parent_vars_list = array();
        $ret = $chkObj->FirstAnalysis($dataString,
                                      $parent_vars_list,
                                      $in_rolename,
                                      $in_display_file_name,
                                      $ina_ITA2User_var_list,
                                      $ina_User2ITA_var_list,
                                      $errmsg, $f_name, $f_line,
                                      $in_role_pkg_name);
        if($ret === false){
            // 変数取得失敗
            $errmsg = $errmsg . "(" . $f_line . ")";
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
            return(false);
        }

        $ret = $chkObj->MiddleAnalysis($parent_vars_list,
                                       $in_rolename,
                                       $in_display_file_name,
                                       $errmsg, $f_name, $f_line,
                                       $in_role_pkg_name);

        if($ret === false){
            // 変数取得失敗
            $errmsg = $errmsg . "(" . $f_line . ")";
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
            return(false);
        }

        // 一時ファイル名
        $tmp_file_name  = "/tmp/LegacyRoleDefaultsVarsFile_" . getmypid() . ".yaml";
        $ret = $chkObj->LastAnalysis($tmp_file_name,$parent_vars_list,
                                     $vars_list,$varsval_list,
                                     $array_vars_list,
                                     $in_rolename,
                                     $in_display_file_name,
                                     $errmsg, $f_name, $f_line,
                                     $in_role_pkg_name);
        if($ret === false){
            // 変数取得失敗
            $errmsg = $errmsg . "(" . $f_line . ")";
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
            return(false);
        }

        // ファイルに定義されている変数(親)を取り出す
        $in_parent_vars_list = $parent_vars_list;
        $ina_vars_list       = $vars_list;
        $ina_array_vars_list = $array_vars_list;
        $ina_varval_list     = $varsval_list;

        return true;
    }
}
/////////////////////////////////////////////////////////////////////////////////
//  C0003
//  処理概要
//    変数定義を解析する。
//
/////////////////////////////////////////////////////////////////////////////////
class VarStructAnalysisFileAccess{
    protected   $lv_objMTS;
    protected   $lv_objDBCA;
    protected   $lv_lasterrmsg;
    protected   $lva_global_vars_master_list;
    protected   $lva_template_master_list;
    protected   $log_level;
    protected   $web_mode;
    protected   $master_non_reg_chk;
    protected   $vars_struct_anal_only;

    function __construct($in_objMTS,$in_objDBCA,$in_global_vars_master_list,$in_template_master_list,$in_log_level,$master_non_reg_chk=true,$vars_struct_anal_only=false){
        $this->lv_objMTS                   = $in_objMTS;
        $this->lv_objDBCA                  = $in_objDBCA;
        $this->lva_global_vars_master_list = $in_global_vars_master_list;
        $this->lva_template_master_list    = $in_template_master_list;
        $this->log_level                   = $in_log_level;
        $this->web_mode                    = false;
        $this->master_non_reg_chk          = $master_non_reg_chk;
        $this->vars_struct_anal_only       = $vars_struct_anal_only;
        $this->lv_lasterrmsg = "";
        if( isset($_SERVER) === true ){
            if( array_key_exists('HTTP_HOST', $_SERVER) === true ){
                $this->web_mode  = true;
            }
        }
    }

    function SetLastError($p1,$p2,$p3){
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        $this->lv_lasterrmsg[0] = $p3;
        $this->lv_lasterrmsg[1] = "FILE:$p1 LINE:$p2 $p3";
    }

    function GetLastError() {
        return $this->lv_lasterrmsg;
    }


    function CreateVarStructAnalJsonStringFileDir($pkey) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";

        $cmd_list = array();
        $dir = sprintf("%s/uploadfiles",$root_dir_path);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/2100020303",$dir);
        if( ! file_exists($dir)) {
            $cmd_list[] = sprintf("mkdir -p %s",$dir);
            $cmd_list[] = sprintf("chmod 0777 %s",$dir);
        }
        $dir = sprintf("%s/VAR_STRUCT_ANAL_JSON_STRING_FILE",$dir);
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

    function getVarStructAnalJsonStringFileName($pkey) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
        $file = sprintf("%s/uploadfiles/2100020303/VAR_STRUCT_ANAL_JSON_STRING_FILE/%010d/AnalysFile.json",$root_dir_path,$pkey);
        return($file);
    }

    function getVarStructAnalJsonStringFileInfo($file,
                                               &$vars_list,
                                               &$array_vars_list,
                                               &$tpf_vars_list,
                                               &$ITA2User_var_list,
                                               &$GBL_vars_list) {

        // UIからよばれるので、ワーニング抑止
        $json_string = @file_get_contents($file);
        if($json_string === false) {
            #this->SetLastError(basename(__FILE__),__LINE__,$this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000055",array(__LINE__,$file)));
            return false;
        }
        $php_array         = json_decode($json_string,true);
        $vars_list         = $php_array['Vars_list'];
        $array_vars_list   = $php_array['Array_vars_list'];
        $tpf_vars_list     = $php_array['TPF_vars_list'];
        $ITA2User_var_list = $php_array['ITA2User_vars_list'];
        $GBL_vars_list     = $php_array['GBL_vars_list'];
        return true;
    }

    function putVarStructAnalJsonStringFileInfo($file,
                                                $vars_list,
                                                $array_vars_list,
                                                $tpf_vars_list,
                                                $ITA2User_var_list,
                                                $GBL_vars_list) {

        $php_array['Vars_list']           = $vars_list;
        $php_array['Array_vars_list']     = $array_vars_list;
        $php_array['TPF_vars_list']       = $tpf_vars_list;
        $php_array['ITA2User_vars_list']  = $ITA2User_var_list;
        $php_array['GBL_vars_list']       = $GBL_vars_list;
        // UIからよばれるので、ワーニング抑止
        $ret = @file_put_contents($file,json_encode($php_array));
// エラーチェック
        if($ret === false) {
            $this->SetLastError(basename(__FILE__),__LINE__,$this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000054",array($file)));
            return false;
        }
        $cmd = sprintf("chmod 0777 %s",$file);
        system($cmd);
        return true;
    }
    function getRolePackageInfo(&$role_package_master_list) {

        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";

        $ansible_common_php  = '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php';

        require_once ($root_dir_path . $ansible_common_php);

        $dbObj = new AnsibleCommonLibs();

        //////////////////////////////////////////////////////////////////////////////
        // ロールパッケージ管理の情報を取得
        //////////////////////////////////////////////////////////////////////////////
        $sql = "SELECT                           \n" .
              "    ROLE_PACKAGE_ID               \n" .
              "   ,ROLE_PACKAGE_NAME             \n" .
              "   ,ROLE_PACKAGE_FILE             \n" .
              "FROM                              \n" .
              "    B_ANSIBLE_LRL_ROLE_PACKAGE    \n" .
              "WHERE                             \n" .
              "    DISUSE_FLAG            = '0'; \n";
        $errmsg       = "";
        $errdetailmsg = "";
        $ret = $dbObj->selectDBRecodes($this->lv_objMTS,$this->lv_objDBCA,$sql,"ROLE_PACKAGE_ID",$role_package_master_list,
                                       $errmsg,$errdetailmsg);
// エラーチェック
        if($ret === false) {
            $this->SetLastError(basename(__FILE__),__LINE__,$errmsg . "\n" . $errdetailmsg);
            return false;
        }
        return true;
    }

    function getRolePackageFileName($pkey,$file) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
        $file = sprintf("%s/uploadfiles/2100020303/ROLE_PACKAGE_FILE/%010d/%s",$dir,$pkey,$file);
        return($file);
    }

    function getRolePackegeFileInfo($role_package_name,$zipfile,&$var_list) {

        // ロールパッケージファイル(ZIP)を解析するクラス生成
        $objRole = new CheckAnsibleRoleFiles($this->lv_objMTS);

        // ロールパッケージファイル(ZIP)の解凍先
        $roledir  = "/tmp/TemplateLegacyRoleZipvarget_" . getmypid();
        exec("/bin/rm -rf " . $roledir);

        // ロールパッケージファイル(ZIP)の解凍
        if($objRole->ZipextractTo($zipfile,$roledir) === false){
            $this->SetLastError(basename(__FILE__),__LINE__,$objRole->getlasterror());
            return false;
        }
        // ローカル変数のリスト作成
        $system_vars = array();

        $err_vars_list = array();
        $def_vars_list = array();

        $def_varsval_list = array();

        $def_array_vars_list = array();

        $cpf_vars_list      = array();
        $tpf_vars_list      = array();

        $ITA2User_var_list  = array();
        $User2ITA_var_list  = array();
        $comb_err_vars_list = array();

        // chkRolesDirectoryでcopyモジュールで使用している変数を取得する処理を追加
        // しているが、ここでは不要なので取得処理をしないパラメータを設定する
        $ret = $objRole->chkRolesDirectory($roledir,$system_vars,
                                           $role_package_name,
                                           $def_vars_list,
                                           $err_vars_list,
                                           $def_varsval_list,
                                           $def_array_vars_list,
                                           true,
                                           $cpf_vars_list,
                                           true,
                                           $tpf_vars_list,
                                           $ITA2User_var_list,
                                           $User2ITA_var_list,
                                           $comb_err_vars_list,
                                           true);

        exec("/bin/rm -rf " . $roledir);

        if($ret === false){
            // ロール内の読替表で読替変数と任意変数の組合せが一致していない
            if(@count($comb_err_vars_list) !== 0){
                $msgObj   = new DefaultVarsFileAnalysis($this->lv_objMTS);
                $this->SetLastError(basename(__FILE__),__LINE__,$msgObj->TranslationTableCombinationErrmsgEdit(true,$comb_err_vars_list));
                unset($msgObj);
            }
            // defaults定義ファイルに変数定義が複数あり形式が違う変数がない場合
            // $err_vars_list[変数名][ロールパッケージ名][ロール名]
            else if(@count($err_vars_list) !== 0){
                // defaults定義ファイルに変数定義が複数あり形式が違う変数がある場合
                $msgObj   = new DefaultVarsFileAnalysis($this->objMTS);
                $this->SetLastError(basename(__FILE__),__LINE__,$msgObj->VarsStructErrmsgEdit($err_vars_list));
                unset($msgObj);
            }
            else{
                // 情報不足で処理スキップのメッセージはデバックモード時のみ出力
                $this->SetLastError(basename(__FILE__),__LINE__,$objRole->getlasterror());
            }
            return false;
        } else {
            $var_list['Vars_list']          = $def_vars_list;
            $var_list['Array_vars_list']    = $def_array_vars_list;
            $var_list['TPF_vars_list']      = $tpf_vars_list;
            $var_list['ITA2User_vars_list'] = $ITA2User_var_list;
            $var_list['GBL_vars_list']      = $objRole->getglobalvarname();
            if( ! is_array($var_list['GBL_vars_list'])) {
                $var_lis['GBL_vars_list'] = array();
            }
            return true;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0100
    // 処理内容
    //   代入値管理の具体値に登録されているテンプレート変数を取得
    //   
    // パラメータ
    //   $role_package_name:    ロールパッケージ名
    //   $role_name_list:       ロール名リスト
    //   $tpf_vars_list:        代入値管理の具体値に登録されているテンプレート変数リスト
    //   $role_disuse_check:    ロールパッケージ・ロールが有効かりチェック有無
    //                          有:true　無:false
    //                          ロールパッケージ登録の場合は、true
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getVarEntryISTPFvars($role_package_name,$role_name_list,&$tpf_vars_list,$role_disuse_check=false) {

        ////////////////////////////////////////////////////////////////
        // 代入値管理の具体値に登録されているテンプレート変数を取得
        ////////////////////////////////////////////////////////////////
        $sqlUtnBody = "SELECT DISTINCT                                  \n" 
                     ."   TAB_A.PATTERN_ID                              \n"
                     ."  ,TAB_B.PATTERN_NAME                            \n"
                     ."  ,TAB_C.ROLE_PACKAGE_ID                         \n"
                     ."  ,TAB_F.ROLE_PACKAGE_NAME                       \n"
                     ."  ,TAB_C.ROLE_ID                                 \n"
                     ."  ,TAB_G.ROLE_NAME                               \n"
                     ."  ,TAB_A.VARS_ENTRY                              \n"
                     ."  ,TAB_E.VARS_NAME_ID                            \n"
                     ."  ,TAB_E.VARS_NAME                               \n"
                     ."  ,TAB_D.DISUSE_FLAG  PTN_VARS_LINK_DISUSE_FLAG  \n"
                     ."  ,TAB_E.DISUSE_FLAG  VARS_MASTER_DISUSE_FLAG    \n"
                     ."  ,TAB_F.DISUSE_FLAG  ROLE_PACKAGE_DISUSE_FLAG   \n"
                     ."  ,TAB_G.DISUSE_FLAG  ROLE_DISUSE_FLAG           \n"
                     ."FROM                                             \n"
                     ."   B_ANSIBLE_LRL_VARS_ASSIGN TAB_A               \n"
                     ."   LEFT JOIN E_ANSIBLE_LRL_PATTERN       TAB_B ON ( TAB_A.PATTERN_ID      = TAB_B.PATTERN_ID      ) \n"
                     ."   LEFT JOIN B_ANSIBLE_LRL_PATTERN_LINK  TAB_C ON ( TAB_A.PATTERN_ID      = TAB_C.PATTERN_ID      ) \n"
                     ."   LEFT JOIN B_ANS_LRL_PTN_VARS_LINK     TAB_D ON ( TAB_A.VARS_LINK_ID    = TAB_D.VARS_LINK_ID    ) \n"
                     ."   LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_E ON ( TAB_D.VARS_NAME_ID    = TAB_E.VARS_NAME_ID    ) \n"
                     ."   LEFT JOIN B_ANSIBLE_LRL_ROLE_PACKAGE  TAB_F ON ( TAB_C.ROLE_PACKAGE_ID = TAB_F.ROLE_PACKAGE_ID ) \n"
                     ."   LEFT JOIN B_ANSIBLE_LRL_ROLE          TAB_G ON ( TAB_C.ROLE_ID         = TAB_G.ROLE_ID         ) \n"
                     ." WHERE                                           \n"
                     ."   TAB_A.VARS_ENTRY LIKE  '%{{ TPF_% }}%' AND    \n"
                     ."   TAB_A.DISUSE_FLAG = '0' AND                   \n"
                     ."   TAB_B.DISUSE_FLAG = '0' AND                   \n"
                     ."   TAB_C.DISUSE_FLAG = '0'                       \n";

        $arrayUtnBind = array();

        $objQueryUtn = $this->lv_objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            $this->SetLastError(basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError() . "\n" . 
                                $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                  array(__FILE__,__LINE__,"00006000")));
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $this->SetLastError(basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError() . "\n" . 
                                $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                  array(__FILE__,__LINE__,"00006010")));
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $this->SetLastError(basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError() . "\n" . 
                                $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                 array(__FILE__,__LINE__,"00006020")));
            return false;
        }
        $dbObj = new AnsibleCommonLibs();
        while ( $row = $objQueryUtn->resultFetch() ){
            // テンプレート変数名の書式確認
            $ret = $this->chkValueIsVariable('TPF_',$row['VARS_ENTRY'],$tpf_var_name);
            if($ret === false) {
                continue;
            }
            // ロールパッケージ名が不一致の場合は除外
            // 復活のケースがあるので、廃止かは判定しない
            if($row['ROLE_PACKAGE_NAME'] != $role_package_name) {
                continue;
            }
            $hit = false;
            foreach($role_name_list as $role_name) {
                // ロールパッケージ内のロール名と不一致の場合
                // 復活のケースがあるので、廃止かは判定しない
                if($row['ROLE_NAME'] != $role_name) {
                    continue;
                }
                $hit = true;
            }
            // ロール名が不一致の場合は除外 
            if($hit === false) {
                continue;
            }
            // Movementに紐づいているロールを取得
            // これより前で、廃止されているMovementでないことは確認済み
            $sql = "SELECT                                          \n"
                  ."  TAB_A.LINK_ID                                 \n"
                  ." ,TAB_A.PATTERN_ID                              \n"
                  ." ,TAB_B.PATTERN_NAME                            \n"
                  ." ,TAB_A.ROLE_PACKAGE_ID                         \n"
                  ." ,TAB_C.ROLE_PACKAGE_NAME                       \n"
                  ." ,TAB_A.ROLE_ID                                 \n"
                  ." ,TAB_D.ROLE_NAME                               \n"
                  ."FROM                                            \n"
                  ."   B_ANSIBLE_LRL_PATTERN_LINK            TAB_A  \n"             
                  ."   LEFT JOIN E_ANSIBLE_LRL_PATTERN       TAB_B ON (TAB_A.PATTERN_ID      = TAB_B.PATTERN_ID )      \n"
                  ."   LEFT JOIN B_ANSIBLE_LRL_ROLE_PACKAGE  TAB_C ON (TAB_A.ROLE_PACKAGE_ID = TAB_C.ROLE_PACKAGE_ID ) \n"
                  ."   LEFT JOIN B_ANSIBLE_LRL_ROLE          TAB_D ON (TAB_A.ROLE_ID         = TAB_D.ROLE_ID         ) \n"
                  ." WHERE                                          \n" 
                  ."       TAB_A.DISUSE_FLAG = '0'                  \n"
                  ."   AND TAB_A.PATTERN_ID  = " . $row['PATTERN_ID'] . "\n";
            // ロールパッケージ管理からの場合で、新規・変更の場合の条件
            // 復活の場合は条件から除外
            if($role_disuse_check == true) {

                $sql .=   "   AND TAB_C.DISUSE_FLAG = '0'           \n"
                         ."   AND TAB_D.DISUSE_FLAG = '0'           \n";
            }
            $errmsg       = "";
            $errdetailmsg = "";
            $movement_use_role_name_row = array();
            $ret = $dbObj->selectDBRecodes($this->lv_objMTS,$this->lv_objDBCA,$sql,"LINK_ID",$movement_use_role_name_row,
                                           $errmsg,$errdetailmsg);
            
            if($ret === false) {
                unset($dbObj);
                $this->SetLastError(basename(__FILE__),__LINE__, $errmsg . $errdetailmsg);
                return false;
            }

            // Movementに紐づいているロールを取得
            foreach($movement_use_role_name_row as $linkid=>$movement_row) {
                $movement_use_role_name_list[$movement_row['ROLE_NAME']]=0;
            }
            unset($movement_use_role_name_row);

            // ロールパッケージのロールでMovementに紐づいているロールを取得
            $use_role_name_list = array();
            foreach($role_name_list as $role_name) {
                if(isset($movement_use_role_name_list[$role_name])) {
                    $tpf_vars_list[$role_name]['file']['line'][$tpf_var_name] = 0;
                }
            }
            unset($movement_use_role_name_list);
        }
        // DBアクセス事後処理
        unset($dbObj);
        unset($objQueryUtn);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0101
    // 処理内容
    //   テンプレートで使用している変数を取得
    //   
    // パラメータ
    //   $tpf_vars_list:       使用しているテンプレート変数リスト
    //   $ITA2User_var_list:   読替表(読替変数->ユーザー変数)
    //   $gbl_vars_list:       テンプレートで使用しているグローバル変数のリスト
    //   $tpf_vars_struct:     テンプレートで使用している変数の変数構造リスト
    //   $errormsg:            エラー時のメッセージ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function getTemplateUseVarsStructiMain($tpf_vars_list,$ITA2User_var_list,&$gbl_vars_list,&$tpf_vars_struct,&$errormsg) {
        $errormsg = "";
        $global_template_vars_list = array();
        foreach($tpf_vars_list as $rolename=>$tpf_vars_array1) {
            foreach($tpf_vars_array1 as $tgt_file_name=>$tpf_vars_array2) {
                foreach($tpf_vars_array2 as $line_no=>$tpf_vars_array3) {
                    foreach($tpf_vars_array3 as $tpf_var_name=>$dummy) {
                        $this->getTemplateUseVarsStructSub($tpf_var_name,$rolename,$ITA2User_var_list,$gbl_vars_list,$tpf_vars_struct,$errormsg);
                    }
                }
            }
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0102
    // 処理内容
    //   指定された変数の変数構造を取得
    //   
    // パラメータ
    //   $tpf_vars_list:       使用しているテンプレート変数リスト
    //   $ITA2User_var_list:   読替表(読替変数->ユーザー変数)
    //   $gbl_vars_list:       テンプレートで使用しているグローバル変数のリスト
    //   $tpf_vars_struct:     テンプレートで使用している変数の変数構造リスト
    //   $errormsg:            エラー時のメッセージ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function getTemplateUseVarsStructSub($tpf_var_name,$rolename,$ITA2User_var_list,&$gbl_vars_list,&$tpf_vars_struct,&$errormsg) {
        if(isset($this->lva_template_master_list[$tpf_var_name])) {
            // 変数構造解析結果
            $php_array = json_decode($this->lva_template_master_list[$tpf_var_name]['VAR_STRUCT_ANAL_JSON_STRING'],true);
            if(isset($php_array['Vars_list'])) {
                foreach($php_array['Vars_list'] as $var_name=>$var_struct) {
                    // 変数の種類確認
                    $var_type = $this->chkVariableType($var_name);
                    if($var_type == "VAR") {
                        // 変数の情報をマージする。
                        $tpf_vars_struct['Vars_list'][$rolename][$var_name]=$var_struct;
                    }
                    if($var_type == "LCA") {
                        // 読替表に読替変数が設定されているか判定する。
                        if(! @isset($ITA2User_var_list[$rolename][$var_name])) {
                            //読替表に読替変数未登録
                            if($this->log_level == "DEBUG") {
                                // 6000053 = "テンプレート管理に登録されている読替変数が読替表に登録されていません。この読替変数の処理をスキップします。(テ>ンプレート埋込変数:{} 読替変数:{})";
                                if(strlen($errormsg)!=0) $errormsg .= "\n";
                                $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000053", 
                                                                               array($tpf_var_name,$var_name));
                            }
                            // webから呼ばれている場合
                            if($this->web_mode === true) {
                                // 6000048 = "テンプレート管理で定義している読替変数が読替表に登録されていません。(ロール名:{} 読替変数:{} テンプレート埋込>変数名:{})";
                                if(strlen($errormsg)!=0) $errormsg .= "\n";
                                $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000048",
                                                                              array($rolename,$var_name,$tpf_var_name));
                            } else {
                                if($this->log_level == "DEBUG") {
                                    // 6000053 = "テンプレート管理に登録されている読替変数が読替表に登録されていません。この読替変数の処理をスキップします。(テ>ンプレート埋込変数:{} 読替変数:{})";
                                    if(strlen($errormsg)!=0) $errormsg .= "\n";
                                    $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000053", 
                                                                                   array($tpf_var_name,$var_name));
                                }
                            }
                            // 次の変数へ
                            continue;
                        }
                        // 変数の情報をマージする。
                        $tpf_vars_struct['Vars_list'][$rolename][$var_name]=$var_struct;
                    }
                    if($var_type == "GBL") {
                        // グローバル変数の具体値にテンプレート変数が設定されているか判定する。
                        if( ! isset($this->lva_global_vars_master_list[$var_name])) {
                            // webから呼ばれている場合
                            if($this->web_mode === true) {
                                // $ary[6000033] = "(テンプレート埋込変数:{} グローバル変数:{})";
                                if(strlen($errormsg)!=0) $errormsg .= "\n";
                                $parammsg .= $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-6000033",array($tpf_var_name,$gbl_var_name));
                                // $ary[6000032] = "テンプレート管理で使用しているグローバル変数がグローバル変数管理に登録されていません。{}";
                                $parammsg .= $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-6000032",array($parammsg));
                            } else {
                                if($this->log_level == "DEBUG") {
                                    //グローバル変数管理に変数未登録
                                    // 6000051 = "グローバル変数管理にグローバル変数が登録されていません。このグローバル変数の処理をスキップします。(グローバル>変数:{})";
                                    if(strlen($errormsg)!=0) $errormsg .= "\n";
                                    $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000051", array($var_name));
                                }
                            }
                            // 次の変数へ
                            continue;
                        }
                        $gbl_vars_list[$rolename][$var_name] = 0;
                    }
                }
            }

            if(isset($php_array['Array_vars_list'])) {
                foreach($php_array['Array_vars_list'] as $var_name=>$var_struct) {
                    // 変数の種類確認
                    $var_type = $this->chkVariableType($var_name);
                    if($var_type == "LCA") {
                        // 読替表に読替変数が設定されているか判定する。
                        if(! @isset($ITA2User_var_list[$rolename][$var_name])) {
                            // webから呼ばれている場合
                            if($this->web_mode === true) {
                                // 6000048 = "テンプレート管理で定義している読替変数が読替表に登録されていません。(ロール名:{} 読替変数:{} テンプレート埋込>変数名:{})";
                                if(strlen($errormsg)!=0) $errormsg .= "\n";
                                $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000048",
                                                                              array($rolename,$var_name,$tpf_var_name));
                            } else {
                                if($this->log_level == "DEBUG") {
                                    //読替表に読替変数未登録
                                    if(strlen($errormsg)!=0) $errormsg .= "\n";
                                    $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000053", array($tpf_var_name,$var_name));
                                }
                            }
                            // 次の変数へ
                            continue;
                        }
                    }
                    // 変数の情報をマージする。
                    $tpf_vars_struct['Array_vars_list'][$rolename][$var_name]=$var_struct;

                }
            }
        }else{
            if($this->master_non_reg_chk === true) {
                // webから呼ばれている場合
                if($this->web_mode === true) {
                    //テンプレート管理に変数未登録
                    if(strlen($errormsg)!=0) $errormsg .= "\n";
                    $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000057",array($rolename,$tpf_var_name));
                } else {
                    if($this->log_level == "DEBUG") {
                        //テンプレート管理に変数未登録
                        if(strlen($errormsg)!=0) $errormsg .= "\n";
                        $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000052",array($rolename,$tpf_var_name));
                    }
                }
            }
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0103
    // 処理内容
    //   指定された変数の種類を判定する。
    //   
    // パラメータ
    //   $var_name:            変数名
    // 
    // 戻り値
    //   変数の種類 VAR/LCA/GBL
    ////////////////////////////////////////////////////////////////////////////////
    function chkVariableType($var_name) {
        $ret = preg_match("/^VAR_[a-zA-Z0-9_]*/",$var_name);
        if($ret != 0) {
            return "VAR";
        } else {
            // 読替変数の場合
            $ret = preg_match("/^LCA_[a-zA-Z0-9_]*/",$var_name);
            if($ret != 0) {
                return "LCA";
                $LCA_vars_use = true;
            } else {
                // グローバル変数の場合
                $ret = preg_match("/^GBL_[a-zA-Z0-9_]*/",$var_name);
                if($ret != 0) {
                    return "GBL";
                } else {
                    return false;
                }
            }
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0104
    // 処理内容
    //   変数名の書式を判定する。
    //   
    // パラメータ
    //   $var_heder_id:     変数種別(VAR_/TPF_CPF_etc)
    //   $var_value:        変数名({{ xxxx }}
    //   $var_name:         {{}}を取り除いた変数名
    // 
    // 戻り値
    //   True:正常　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkValueIsVariable($var_heder_id,$var_value,&$var_name) {
        $var_name = '';
        // 変数名　{{ ???_[a-zA-Z0-9_] }} を取出す
        $ret = preg_match_all("/{{(\s)" . $var_heder_id . "[a-zA-Z0-9_]*(\s)}}/",$var_value,$var_match);
        if(($ret !== false) && ($ret >= 1)) {
            $ret = preg_match_all("/" . $var_heder_id . "[a-zA-Z0-9_]*/",$var_match[0][0],$var_name_match);
            $var_name =  trim($var_name_match[0][0]);
            return true;
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0105
    // 処理内容
    //   グローバル変数の具体値に設定されているテンプレート変数を取得
    //   
    // パラメータ
    //   $gbl_vars_list:    クローバル変数リスト
    //   $tpf_vars_lists:   グローバル変数の具体値に設定されているテンプレート変数のリスト
    //   $errormsg:         エラー時のメッセージ
    // 
    // 戻り値
    //   True:正常　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getGlobalVarsUseTemplateUseVars($gbl_vars_list,&$tpf_vars_list,&$errormsg) {
        $errormsg = "";
        $global_template_vars_list = array();
        foreach($gbl_vars_list as $rolename=>$gbl_vars_array1) {
            foreach($gbl_vars_array1 as $var_name=>$dummy) {
                // グローバル変数の具体値にテンプレート変数が設定されているか判定する。
                if(isset($this->lva_global_vars_master_list[$var_name])) {
                    $var_value = $this->lva_global_vars_master_list[$var_name]['VARS_ENTRY'];
                    $value_var_name = "";
                    $ret = $this->chkValueIsVariable('TPF_',$var_value,$value_var_name);
                    if($ret === true) {
                        // テンプレート変数退避
                        $tpf_vars_list[$rolename]['file']['line'][$value_var_name] = 0;
                    }
                } else {
                    if($this->master_non_reg_chk === true) {
                        //グローバル変数管理に変数未登録
                        // webから呼ばれている場合
                        if($this->web_mode === true) {
                            // [6000056] "グローバル変数管理にグローバル変数が登録されていません。(グローバル変数:{})";
                            if(strlen($errormsg)!=0) $errormsg .= "\n";
                            $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000056", array($var_name));
                        } else {
                            if($this->log_level == "DEBUG") {
                                // 6000051 "グローバル変数管理にグローバル変数が登録されていません。このグローバル変数の処理をスキップします。(グローバル>変数:{})";
    
                                if(strlen($errormsg)!=0) $errormsg .= "\n";
                                $errormsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000051", array($var_name));
                            }
                        }
                    }
                }
            }
        }
    }
    function RolePackageAnalysis($strTempFileFullname,
                                 $PkeyID,
                                 $role_package_name,
                                 $disuse_role_chk,
                                &$def_vars_list,
                                &$def_varsval_list,
                                &$def_array_vars_list,
                                 $cpf_vars_chk,
                                &$cpf_vars_list,
                                 $tpf_vars_chk,
                                &$tpf_vars_list,
                                &$gbl_vars_list,
                                &$ITA2User_var_list,
                                &$User2ITA_var_list,
                                &$save_vars_list) {
        global $g;

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php' );

        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = null;
        $arysystemvars = array();

        // ロールパッケージファイル(ZIP)を解析するクラス生成
        $roleObj = new CheckAnsibleRoleFiles($this->lv_objMTS);

        // ロールパッケージファイル(ZIP)の解凍先
        $outdir  = "/tmp/LegacyRoleZipFileUpload_" . getmypid();

        // ロールパッケージファイル(ZIP)の解凍
        if($roleObj->ZipextractTo($strTempFileFullname,$outdir) === false){
            $boolRet = false;
            $arryErrMsg = $roleObj->getlasterror();
            $strErrMsg = $arryErrMsg[0];

        } else{
            $def_vars_list = array();
            $err_vars_list = array();

            $def_varsval_list = array();

            $cpf_vars_list = array();
            $tpf_vars_list = array();

            $ITA2User_var_list = array();
            $User2ITA_var_list = array();
            $comb_err_vars_list = array();
            
            // ロールパッケージファイル(ZIP)の解析
            $ret = $roleObj->chkRolesDirectory($outdir,
                                               $arysystemvars,
                                               "",
                                               $def_vars_list,
                                               $err_vars_list,
                                               $def_varsval_list,
                                               $def_array_vars_list,
                                               $cpf_vars_chk,
                                               $cpf_vars_list,
                                               $tpf_vars_chk,
                                               $tpf_vars_list,
                                               $ITA2User_var_list,
                                               $User2ITA_var_list,
                                               $comb_err_vars_list,
                                               true);

            if($ret === false){
                // ロール内の読替表で読替変数と任意変数の組合せが一致していない
                if(@count($comb_err_vars_list) !== 0){
                    $msgObj = new DefaultVarsFileAnalysis($this->lv_objMTS);
                    $strErrMsg  = $msgObj->TranslationTableCombinationErrmsgEdit(false,$comb_err_vars_list);
                    unset($msgObj);
                    $boolRet = false;
                }

                // defaults定義ファイルに定義されている変数で形式が違う変数がある場合
                else if(@count($err_vars_list) !== 0){
                    // エラーメッセージ編集
                    $msgObj = new DefaultVarsFileAnalysis($this->lv_objMTS);
                    $strErrMsg  = $msgObj->VarsStructErrmsgEdit($err_vars_list);
                    unset($msgObj);
                    $boolRet = false;
                }
                else{
                    $boolRet = false;
                    $arryErrMsg = $roleObj->getlasterror();
                    $strErrMsg = $arryErrMsg[0];
                }
            }

            exec("/bin/rm -rf " . $outdir);

            // ロール名一覧取得
            $role_name_list = $roleObj->getrolename();

            // グローバル変数の一覧取得
            $gbl_vars_list    = $roleObj->getglobalvarname();
            if( ! is_array($gbl_vars_list)) {
                 $gbl_vars_list = array();
            }

            // 変数構造の解析のみの場合
            if($this->vars_struct_anal_only === true) {
                $boolRet  = true;
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg);
                return $retArray;
            }

            $dbObj = new AnsibleCommonLibs();

            if($boolRet === true){
                //////////////////////////////////////////////////////////////////////////////
                // グローバル変数の情報を取得
                //////////////////////////////////////////////////////////////////////////////
                $this->lva_global_vars_master_list = array();
                $sql = "SELECT                           \n" .
                      "    VARS_NAME,                    \n" .
                      "    VARS_ENTRY                    \n" .
                      "FROM                              \n" .
                      "    B_ANS_GLOBAL_VARS_MASTER      \n" .
                      "WHERE                             \n" .
                      "    DISUSE_FLAG            = '0'; \n";

                $errmsg       = "";
                $errdetailmsg = "";
                $ret = $dbObj->selectDBRecodes($this->lv_objMTS,$this->lv_objDBCA,$sql,"VARS_NAME",$this->lva_global_vars_master_list,
                                               $errmsg,$errdetailmsg);
                if($ret === false) {
                    $strErrMsg = $errmsg;
                    $boolRet = false;
                }
            }

            if($boolRet === true){
                //////////////////////////////////////////////////////////////////////////////
                // iコンテンツ管理(CPF変数Iの情報を取得
                //////////////////////////////////////////////////////////////////////////////
                $lva_contents_vars_master_list = array();
                $sql = "SELECT                           \n" .
                      "    CONTENTS_FILE_ID,             \n" .
                      "    CONTENTS_FILE_VARS_NAME       \n" .
                      "FROM                              \n" .
                      "    B_ANS_CONTENTS_FILE           \n" .
                      "WHERE                             \n" .
                      "    DISUSE_FLAG            = '0'; \n";

                $errmsg       = "";
                $errdetailmsg = "";
                $ret = $dbObj->selectDBRecodes($this->lv_objMTS,$this->lv_objDBCA,$sql,"CONTENTS_FILE_VARS_NAME",$lva_contents_vars_master_list,
                                               $errmsg,$errdetailmsg);
                if($ret === false) {
                    $strErrMsg = $errmsg;
                    $boolRet   = false;
                }
            }

            if($boolRet === true){
                //////////////////////////////////////////////////////////////////////////////
                // テンプレート管理の情報を取得
                //////////////////////////////////////////////////////////////////////////////
                $this->lva_template_master_list = array();
                $sql = "SELECT                           \n" .
                      "    ANS_TEMPLATE_ID,              \n" .
                      "    ANS_TEMPLATE_VARS_NAME,       \n" .
                      "    VARS_LIST                     \n" .
                      "FROM                              \n" .
                      "    B_ANS_TEMPLATE_FILE           \n" .
                      "WHERE                             \n" .
                      "    DISUSE_FLAG            = '0'; \n";
                $errmsg       = "";
                $errdetailmsg = "";
                $ret = $dbObj->selectDBRecodes($this->lv_objMTS,$this->lv_objDBCA,$sql,
                                               "ANS_TEMPLATE_VARS_NAME",
                                               $this->lva_template_master_list,
                                               $errmsg,$errdetailmsg);
                if($ret === false) {
                    $strErrMsg = $errmsg;
                    $boolRet   = false;
                } else {
                    foreach($this->lva_template_master_list as $strVarName=>$row) {
                        $Vars_list        = array();
                        $Array_vars_list  = array();
                        $LCA_vars_use     = false;
                        $Array_vars_use   = false;
                        $GBL_vars_info    = array();
                        $VarVal_list      = array();
                        $PkeyID           = $row['ANS_TEMPLATE_ID']; 
                        $strVarsList      = $row['VARS_LIST']; 

                        // 変数定義の解析結果を取得
                        $fileObj = new TemplateVarsStructAnalFileAccess($this->lv_objMTS,$this->lv_objDBCA);

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
                            $strErrMsg = $errmsg[0];
                            $boolRet   = false;
                        }
                        //変数定義の解析結果をjson形式の文字列に変換
                        $php_array = $fileObj->ArrayTOjsonString($Vars_list,
                                                                 $Array_vars_list,
                                                                 $LCA_vars_use,
                                                                 $Array_vars_use,
                                                                 $GBL_vars_info,
                                                                 $VarVal_list);
                        //配列に保存 
                        $this->lva_template_master_list[$strVarName]['VAR_STRUCT_ANAL_JSON_STRING'] = $php_array;
                        unset($fileObj);
                        if($boolRet === false) {
                            break;
                        }
                    }
                }
            }
            unset($dbObj);

            $GBLVars = '1';
            $CPFVars = '2';
            $TPFVars = '3';
            $save_vars_list = array();
            $save_vars_list[$GBLVars] = array();
            $save_vars_list[$CPFVars] = array();
            $save_vars_list[$TPFVars] = array();
            $objLibs = new AnsibleCommonLibs(LC_RUN_MODE_STD);
            if($boolRet === true){
                $strErrMsg = "";
                $strErrDetailMsg = "";
                foreach( $cpf_vars_list as $role_name => $tgt_file_list ){
                    foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                        foreach( $line_no_list as $line_no => $cpf_var_name_list ){
                            foreach( $cpf_var_name_list as $cpf_var_name => $dummy ){
                                $save_vars_list[$CPFVars][$cpf_var_name] = 0;
                                // CPF変数がファイル管理に登録されているか判定
                                if($this->master_non_reg_chk === true) {
                                    if( ! isset($lva_contents_vars_master_list[$cpf_var_name])) {
                                        if($strErrMsg != "") $strErrMsg .= "\n";
                                        $strErrMsg = $strErrMsg . AnsibleMakeMessage($this->lv_objMTS,LC_RUN_MODE_STD,
                                                                                     "ITAANSIBLEH-ERR-90090", array($role_name,
                                                                                                                    $tgt_file,
                                                                                                                    $line_no,
                                                                                                                    $cpf_var_name));
                                        $boolRet = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if($boolRet === true){
                $strErrMsg = "";
                $strErrDetailMsg = "";
                $tpf_vars_struct  = array();

                // テンプレートで使用している変数構造の取得
                // テンプレートで使用しているグローバル変数の登録確認
                // テンプレートで使用している読替変数の登録確認
                $errormsg         = "";
                $this->getTemplateUseVarsStructiMain($tpf_vars_list,$ITA2User_var_list,$gbl_vars_list,$tpf_vars_struct,$errormsg);
                if(strlen($errormsg) != 0) {
                    $boolRet   = false;
                    $strErrMsg = $errormsg;
                }
            }

            if($boolRet === true) {
                // 使用しているグローバル変数の具体値に設定されているテンプレート変数を取得する。
                $wk_tpf_vars_list = array();
                $errormsg         = "";
                $this->getGlobalVarsUseTemplateUseVars($gbl_vars_list,$wk_tpf_vars_list,$errormsg);
                // 戻りはチェックしない、エラーメッセージを出力して先に進む
                if(strlen($errormsg) != 0) {
                    $boolRet   = false;
                    $strErrMsg = $errormsg;
                }
            }

            if($boolRet === true) {
                // 代入値管理の具体値に設定されているテンプレート変数を取得する。
                $errormsg         = "";

                $ret = $this->getVarEntryISTPFvars($role_package_name,$roleObj->getrolename(),$wk_tpf_vars_list,$disuse_role_chk);
                if($ret === false) {
                    $errary    = $this->GetLastError();
                    $boolRet   = false;
                    $strErrMsg = $errary[0];
                }
            }

            if($boolRet === true) {
                // 代入値管理の具体値に設定されているテンプレート変数の変数構造を取得する。
                $errormsg         = "";
                $this->getTemplateUseVarsStructiMain($wk_tpf_vars_list,$ITA2User_var_list,$gbl_vars_list,$tpf_vars_struct,$errormsg);
                if(strlen($errormsg) != 0) {
                     $boolRet   = false;
                     $strErrMsg = $errormsg;
                }
            }

            if($boolRet === true) {
                // テンプレート管理の変数定義とロール内の変数定義が一致しているか確認する。
                foreach( $tpf_vars_list as $role_name => $tgt_file_list ){
                    foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                        foreach( $line_no_list as $line_no => $tpf_var_name_list ){
                            foreach( $tpf_var_name_list as $tpf_var_name => $row ){
                                $save_vars_list[$TPFVars][$tpf_var_name] = 0;
                            }
                        }
                    }
                }
                // テンプレート管理の変数定義とロール内の変数定義が一致しているか確認する。
                //if( isset($lva_template_master_list[$tpf_var_name])) {
                //    $row = $lva_template_master_list[$tpf_var_name];
                //} else {
                //    // テンプレート管理未登録の場合
                //    continue;
                //}
                $chk_list = array();
                foreach($this->lva_template_master_list as $tpf_var_name=>$row) {
                    // 重複チェック防止
                    if(isset($chk_list[$tpf_var_name])) {
                        continue;
                    }
                    $chk_list[$tpf_var_name] = 0;
 
                    // テンプレート管理の変数定義取得
                    $chk_json_Ary       = $row['VAR_STRUCT_ANAL_JSON_STRING'];
                    $chk_php_array      = json_decode($chk_json_Ary,true);

                    $chk_vars_list       = array();
                    $chk_Array_vars_list = array();
                    $chk_vars_list[$tpf_var_name]['dummy']       = $chk_php_array['Vars_list'];
                    $chk_Array_vars_list[$tpf_var_name]['dummy'] = $chk_php_array['Array_vars_list'];

                    // ロール毎の変数定義とテンプレート管理の変数定義が一致しているか確認
                    foreach($role_name_list as $no=>$crt_role_name)
                    {
                        $chk_vars_list[$tpf_var_name]['role']        = array();
                        $chk_Array_vars_list[$tpf_var_name]['role']  = array();
                        // ロール毎の変数定義取得
                        // 通常・複数具体値変数
                        if(isset($def_vars_list[$crt_role_name])) {
                            $chk_vars_list[$tpf_var_name]['role'] = $def_vars_list[$crt_role_name];
                        }
                        // 多段変数
                        if(isset($def_array_vars_list[$crt_role_name])) {
                            $chk_Array_vars_list[$tpf_var_name]['role']  = $def_array_vars_list[$crt_role_name];
                        }

                        $chkObj = new DefaultVarsFileAnalysis($this->lv_objMTS);

                        $err_vars_list = array();

                        // 変数構造が一致していない変数があるか確認
                        $ret = $chkObj->chkallVarsStruct($chk_vars_list, $chk_Array_vars_list, $err_vars_list);
                        if($ret === false){
                            // 変数構造が一致していない変数あり
                            foreach ($err_vars_list as $err_var_name=>$dummy){
                                if(strlen($strErrMsg)!=0) $strErrMsg.= "\n";
                                $strErrMsg .= $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000047",array($crt_role_name,$err_var_name,$tpf_var_name));
                                $boolRet = false;
                            }
                            unset($chkObj);
                        }
                    }
                }
            }

            if($boolRet === true){
                // ロール内で使用しているグローバル変数の登録確認は実施済み
                foreach( $gbl_vars_list as $role_name => $gbl_var_name_list ){
                    foreach( $gbl_var_name_list as $gbl_var_name => $dummy ){
                        $save_vars_list[$GBLVars][$gbl_var_name] = 0;
                    }
                }
            }
            unset($objLibs);
        }

        unset($roleObj);

        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg);

        return $retArray;
    }
    function getAnsible_RolePackage_file($in_dir,$in_pkey,$in_filename){
        $intNumPadding = 10;

        // sible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
        $file = $in_dir . '/' .
                str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ) . '/' .
                $in_filename;
        return($file);
    }
    function AllRolePackageAnalysis($tgt_PkeyID,$tgt_role_pkg_name,$tgt_vars_list,$tgt_array_vars_list,$error_msg_code="ITAANSIBLEH-ERR-6000058") {

        $def_vars_list        = array();
        $def_varsval_list     = array();
        $def_array_vars_list  = array();
        $cpf_vars_chk         = array();
        $cpf_vars_list        = array();
        $tpf_vars_chk         = array();
        $tpf_vars_list        = array();
        $gbl_vars_list        = array();
        $ITA2User_var_list    = array();
        $User2ITA_var_list    = array();
        $save_vars_array      = array();
        $disuse_role_chk      = true;
        $var_struct_errmag    = "";
        $all_err_vars_list    = array();

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );

        $role_package_master_list = array();
        $ret = $this->getRolePackageInfo($role_package_master_list);
        if($ret === false) {
            return false;
        }
        foreach($role_package_master_list as $PkeyID=>$PkgRow) {
            if($tgt_PkeyID == $PkeyID) {
                continue;
            }
            // 変数構造解析結果ファイルがあるか判定
            $analfile = $this->getVarStructAnalJsonStringFileName($PkeyID);
            if( ! file_exists($analfile)) {
                //変数構造解析結果ファイルがない場合はロールパッケージを解析

                // ロールパッケージファイル名(ZIP)を取得
                $zipfile = $this->getAnsible_RolePackage_file($root_dir_path . '/' . DF_ROLE_PACKAGE_FILE_CONTENTS_DIR,
                                                              $PkeyID,$PkgRow['ROLE_PACKAGE_FILE']);

               // ロールパッケージファイル名(ZIP)の存在確認
               if( file_exists($zipfile) === false ){
                   //"システムで管理しているロールパッケージ管理のファイルが存在しません。(ロールパッケージ管理 項番:{} file:{})";
                   $errormsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70008",array($PkeyID,basename($zipfile)));
                   $this->SetLastError(basename(__FILE__),__LINE__,$errormsg);
                   return false;
               }

               list($ret,
                    $intErrorType,
                    $aryErrMsgBody,
                    $retStrBody) = $this->RolePackageAnalysis($zipfile,
                                                              $PkeyID,$PkgRow['ROLE_PACKAGE_NAME'],
                                                              $disuse_role_chk,
                                                              $def_vars_list,
                                                              $def_varsval_list,
                                                              $def_array_vars_list,
                                                              true,
                                                              $cpf_vars_list,
                                                              true,
                                                              $tpf_vars_list,
                                                              $gbl_vars_list,
                                                              $ITA2User_var_list,
                                                              $User2ITA_var_list,
                                                              $save_vars_array);
                if($ret === false) {
                    $this->SetLastError(basename(__FILE__),__LINE__,$retStrBody);
                    return false;
                }
                // 変数構造解析結果を退避
                // 退避ディレクトリ作成・確認
                $dir = $this->CreateVarStructAnalJsonStringFileDir($PkeyID);

                // 退避ファイル名取得
                $analfile= $this->getVarStructAnalJsonStringFileName($PkeyID);

                // ファイルに退避
                $ret = $this->putVarStructAnalJsonStringFileInfo($analfile,
                                                                 $def_vars_list,
                                                                 $def_array_vars_list,
                                                                 $tpf_vars_list,
                                                                 $ITA2User_var_list,
                                                                 $gbl_vars_list);
// エラーチェック
                if($ret === false)
                {
                    $errmsg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000018');
                    $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
                    return false;
                }
            }
            //変数構造解析結果ファイルから変数構造取得
            $ret = $this->getVarStructAnalJsonStringFileInfo($analfile,
                                                             $def_vars_list,
                                                             $def_array_vars_list,
                                                             $tpf_vars_list,
                                                             $ITA2User_var_list,
                                                             $gbl_vars_list);
           if($ret === false) {
               return false;
            }
            $all_def_vars_list       = array();
            $all_def_array_vars_list = array();

            // 比較元ロールパッケージファイル default定義数名リスト退避
            $all_def_vars_list[$tgt_role_pkg_name]       = $tgt_vars_list;
            // 比較元ロールパッケージファイル default定義 多次元配列リスト退避
            $all_def_array_vars_list[$tgt_role_pkg_name] = $tgt_array_vars_list;

            // 比較元ロールパッケージファイル default定義数名リスト退避
            $all_def_vars_list[$PkgRow['ROLE_PACKAGE_NAME']]       = $def_vars_list;
            // 比較元ロールパッケージファイル default定義 多次元配列リスト退避
            $all_def_array_vars_list[$PkgRow['ROLE_PACKAGE_NAME']] = $def_array_vars_list;
            $Obj = new DefaultVarsFileAnalysis($this->lv_objMTS);

            $err_vars_list = array();
            $ret = $Obj->chkallVarsStruct($all_def_vars_list, $all_def_array_vars_list ,$err_vars_list);
            // 変数の構造が一致していないロールパッケージする。
            if($ret === false){
                foreach($err_vars_list as $err_var_name=>$err_pkg_list){
                    foreach($err_pkg_list as $err_pkg_name=>$err_role_list){
                        $all_err_vars_list[$err_var_name][$err_pkg_name] = $err_role_list;
                    }
                }
            }
            unset($Obj);
        }
        if(@count($all_err_vars_list) != 0) {
            $var_struct_errmag = $this->VarsStructErrmsgEdit($all_err_vars_list,$tgt_role_pkg_name,$error_msg_code);
            $this->SetLastError(basename(__FILE__),__LINE__,$var_struct_errmag);
            return false;
        }
        return true;
    }

    function VarsStructErrmsgEdit( $ina_err_vars_list,$tgt_role_pkg_name,$error_msg_code){
         $errmsg   = $this->lv_objMTS->getSomeMessage($error_msg_code);
         foreach($ina_err_vars_list as $err_var_name=>$err_pkg_list){
             $err_files = "";
             foreach($err_pkg_list as $err_pkg_name=>$err_role_list){
                 if($err_pkg_name == $tgt_role_pkg_name) {
                     continue;
                 }
                 foreach($err_role_list as $err_role_name=>$dummy){
                     $err_files = $err_files . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000060",
                                                                 array($err_pkg_name,$err_role_name));

                     $err_files = $err_files . "\n";
                 }
             }
             if($err_files != ""){
                 $errmsg = $errmsg . $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000059",
                                                             array($err_var_name,$err_files));
             }
         }
         return $errmsg;
    }
    function getVarStructAnalInfo($tgt_PkeyID,
                                  $tgt_role_pkg_name,
                                  $tgt_zipfile,
                                 &$tgt_def_vars_list,
                                 &$tgt_def_array_vars_list,
                                 &$tgt_tpf_vars_list,
                                 &$tgt_ITA2User_var_list,
                                 &$tgt_gbl_vars_list) {

        $def_vars_list        = array();
        $def_varsval_list     = array();
        $def_array_vars_list  = array();
        $cpf_vars_chk         = array();
        $cpf_vars_list        = array();
        $tpf_vars_chk         = array();
        $tpf_vars_list        = array();
        $gbl_vars_list        = array();
        $ITA2User_var_list    = array();
        $User2ITA_var_list    = array();
        $save_vars_array      = array();
        $disuse_role_chk      = true;
        $var_struct_errmag    = "";
        $all_err_vars_list    = array();

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );

        // 変数構造解析結果ファイルがあるか判定
        $analfile = $this->getVarStructAnalJsonStringFileName($tgt_PkeyID);
        if( ! file_exists($analfile)) {
            //変数構造解析結果ファイルがない場合はロールパッケージを解析

            // ロールパッケージファイル名(ZIP)を取得
            $zipfile = $this->getAnsible_RolePackage_file($root_dir_path . '/' . DF_ROLE_PACKAGE_FILE_CONTENTS_DIR,
                                                          $tgt_PkeyID,$tgt_zipfile);

            // ロールパッケージファイル名(ZIP)の存在確認
            if( file_exists($zipfile) === false ){
                //"システムで管理しているロールパッケージ管理のファイルが存在しません。(ロールパッケージ管理 項番:{} file:{})";
                $errormsg = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70008",array($tgt_PkeyID,basename($zipfile)));
                $this->SetLastError(basename(__FILE__),__LINE__,$errormsg);
                return false;
            }

            list($ret,
                 $intErrorType,
                 $aryErrMsgBody,
                 $retStrBody) = $this->RolePackageAnalysis($zipfile,
                                                           $tgt_PkeyID,
                                                           $tgt_role_pkg_name,
                                                           $disuse_role_chk,
                                                           $def_vars_list,
                                                           $def_varsval_list,
                                                           $def_array_vars_list,
                                                           true,
                                                           $cpf_vars_list,
                                                           true,
                                                           $tpf_vars_list,
                                                           $gbl_vars_list,
                                                           $ITA2User_var_list,
                                                           $User2ITA_var_list,
                                                           $save_vars_array);
            if($ret === false) {
                $this->SetLastError(basename(__FILE__),__LINE__,$retStrBody);
                return false;
            }
            // 変数構造解析結果を退避
            // 退避ディレクトリ作成・確認
            $dir = $this->CreateVarStructAnalJsonStringFileDir($tgt_PkeyID);

            // 退避ファイル名取得
            $analfile= $this->getVarStructAnalJsonStringFileName($tgt_PkeyID);
            // ファイルに退避
            $ret = $this->putVarStructAnalJsonStringFileInfo($analfile,
                                                             $def_vars_list,
                                                             $def_array_vars_list,
                                                             $tpf_vars_list,
                                                             $ITA2User_var_list,
                                                             $gbl_vars_list);
            if($ret === false)
            {
                $errmsg = $this->lv_objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000018');
                $this->SetLastError(basename(__FILE__),__LINE__,$errmsg);
                return false;
            }
        }
        //変数構造解析結果ファイルから変数構造取得
        $ret = $this->getVarStructAnalJsonStringFileInfo($analfile,
                                                         $tgt_def_vars_list,
                                                         $tgt_def_array_vars_list,
                                                         $tgt_tpf_vars_list,
                                                         $tgt_ITA2User_var_list,
                                                         $tgt_gbl_vars_list);
        if($ret === false) {
            return false;
        }
        return true;
    }
}
?>
