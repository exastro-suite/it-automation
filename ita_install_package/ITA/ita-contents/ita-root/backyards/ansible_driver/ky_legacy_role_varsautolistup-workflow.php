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
    //      legacy Role変数自動更新
    //
    // 主要連想配列
    //
    // T0001
    // $lta_role_package_list　　　　B_ANSIBLE_LRL_ROLE_PACKAGEから読込んだデータリスト
    //                               ["ROLE_PACKAGE_ID"]["ROLE_PACKAGE_NAME"] = ["ROLE_PACKAGE_FILE"];
    //                                Array  (
    //                                    [6] => Array        (
    //                                            [pkg] => roles.zip
    //                                        )
    //                                )
    // T0002
    // $lta_role_package_id_list　　 B_ANSIBLE_LRL_ROLE_PACKAGEから読込んだデータリスト
    //                               ["ROLE_PACKAGE_ID"]=1;
    //                                Array(
    //                                    [6] => 1
    //                                )
    // T0003
    // ifa_role_name_list            各ロールパッケージ毎のrolesディレクトリ内のロール名リスト
    //                               ["ROLE_PACKAGE_ID"] = ロール名;
    //                                Array(
    //                                    [6] => Array         (
    //                                         [0] => test_1
    //                                         [1] => test_2
    //                                    )
    //                               )
    // T0004
    // ifa_role_var_list             ロールパッケージファイルのPlaybookで使用している変数名リスト
    //                               ["ROLE_PACKAGE_ID"][role名][変数名]=0
    // 
    // T0005
    // lva_use_role_name_list　　　　ロールパッケージファイルから取得したロール名リストで
    // 　　　　　　　　　　　　　　　ロール管理に登録が必要なロール名リスト
    // 　　　　　　　　　　　　　　　[ROEL_PACKAGE_ID][ROLE_ID] = 1;
    //                                (
    //                                    [6] => Array   (
    //                                            [5] => 1
    //                                            [6] => 1
    //                                        )
    //                                )
    // T0006
    // lva_role_nameTOrole_id dump　 ロール管理に登録されているロール名のPkeyリスト
    //                               [ROEL_PACKAGE_ID][ROLE_NAEM]=ROLE_ID;
    //                                (
    //                                    [6] => Array        (
    //                                            [test_1] => 5
    //                                            [test_2] => 6
    //                                        )
    //                                )
    // 
    // T0007
    // lva_use_role_vars_name_list:  ロールパッケージのデフォルト変数定義ファイルから取得したロール変数リスト
    //                               ロール変数管理に登録が必要なロール変数リスト
    //                               [ROLE_PACKAGE_ID][ROLE_ID][ROLE_VARS_NAME_ID] = ROLE_VARS_NAME;
    //                                Array(
    //                                    [6] => Array        (
    //                                            [5] => Array                (
    //                                                    [822] => VAR_std_test1
    //                                                    [823] => VAR_list_test1
    //                                                    [826] => VAR_str_test1
    //                                                    [827] => VAR_array_test1
    //                                                )
    //                                            [6] => Array                (
    //                                                    [824] => VAR_std_test2
    //                                                    [825] => VAR_list_test2
    //                                                    [828] => VAR_str_test2
    //                                                    [829] => VAR_array_test2
    //                                                )
    //                                        )
    //                                )
    // 
    // 
    // T0008
    // lta_pattern_list              パターン詳細から取得したデータリスト
    //                               ['PATTERN_ID']['ROLE_PACKAGE_ID']['ROLE_ID']=1
    //                                Array(
    //                                    [4] => Array        (
    //                                            [1] => Array                (
    //                                                    [1] => 1
    //                                                )
    //                                            [2] => Array                (
    //                                                    [3] => 1
    //                                                )
    //                                        )
    // 
    // 
    // T0009
    // $aryVarIdPerVarNameFromFiles　変数一覧(一意)             中間データ
    //                               lva_use_role_vars_name_listを元にした変数一覧(一意)
    //                               [変数名(一意)]=変数マスタPkey (初期値 NULL)
    // 
    // $aryVarIdPerVarNameFromFiles_fix T0009と同様
    // 
    // T0010
    // $aryRowFromAnsVarsTable　　　 変数マスタから取得したデータリスト
    //                               [VARS_NAME] = $row;
    // 
    // T0011
    // aryRowsPerPatternFromAnsPatternVarsLink  作業パターン変数紐付マスタから取得したデータリスト
    //                                          [パターンID][変数ID] = [作業パターン変数紐付の各情報]
    // 
    // T0012
    // aryVarNameIdsPerPattern       作業パターン毎 変数一覧    中間データ
    //                               $lta_pattern_listをパターンID毎の変数一覧にまとめる
    //                               [パターンID][変数マスタPkey]=1
    // T0013
    // ifa_role_def_var_list         各ロール毎のdefault変数定義ファイルの変数リスト(多次元変数を除く)
    //                               [ROLE_PACKAGE_ID][role名][変数名]=0　一般変数
    //                               [ROLE_PACKAGE_ID][role名][変数名]=1　複数具体値変数
    //                                Array (
    //                                    [6] => Array        (
    //                                            [test_1] => Array                (
    //                                                    [VAR_std_test1] => 0　一般変数
    //                                                    [VAR_list_test1] => 1 複数具体値変数
    //                                                )
    //                                            [test_2] => Array                (
    //                                                    [VAR_std_test2] => 0
    //                                                    [VAR_list_test2] => 1
    //                                                )
    //                                        )
    //                                )
    // 
    // T0014
    // ifa_err_vars_list             各ロールのデフォルト定義変数で変数構造が違う変数のリストアップ(多次元変数も含む)
    //                                [変数名] = 0;
    // 
    // T0015
    // lva_use_role_child_vars_name_list　現在未使用
    //                                    ロール変数管理に登録が必要なメンバー変数リスト
    //                                    [ROEL_PACKAGE_ID][$ROLE_ID][$ROLE_VARS_NAME_ID] = $CHILD_VARS_NAME;
    // 
    // T0016
    // aryChildVarNameFromFiles      現在未使用
    //                               メンバー変数名の一意リスト
    //                               [ROLE_VARS_NAME][CHILD_VARS_NAME] = 0;
    // T0017
    // lva_vars_attr_list            各親変数のタイプを退避
    //                               [変数名]=変数型
    //                                        LC_VARS_ATTR_STD:     一般変数
    //                                        LC_VARS_ATTR_LIST:    複数具体値
    //                                        LC_VARS_ATTR_STRUCT:  多次元変数
    //                                Array(
    //                                    [VAR_std_test1] => 1
    //                                    [VAR_list_test1] => 2
    //                                    [VAR_std_test2] => 1
    //                                    [VAR_list_test2] => 2
    //                                    [VAR_str_test1] => 3
    //                                    [VAR_array_test1] => 3
    //                                    [VAR_str_test2] => 3
    //                                    [VAR_array_test2] => 3
    //                                )
    // T0018
    // aryRowFromAnsChildVarsTable   メンバー変数管理のデータ
    //                               [PARENT_VARS_NAME_ID[CHILD_VARS_NAME][メンバー変数管理の各項目]=項目値;
    //                                Array　(
    //                                    [400] => Array   (
    //                                            [0.VAR_str_test1_1] => Array  (
    //                                                    [CHILD_VARS_NAME_ID] => 519
    //                                                    [PARENT_VARS_NAME_ID] => 400
    //                                                    [CHILD_VARS_NAME] => 0.VAR_str_test1_1
    //                                                    [ARRAY_MEMBER_ID] => 36
    //                                                    [ASSIGN_SEQ_NEED] => 0
    //                                                    [COL_SEQ_NEED] => 1
    //                                                    [DISP_SEQ] =>
    //                                                    [DISUSE_FLAG] => 0
    //                                                    [NOTE] =>
    //                                                    [LAST_UPDATE_TIMESTAMP] => 2017/06/15 15:33:57
    //                                                    [UPD_UPDATE_TIMESTAMP] => T_20170615153357992690
    //                                                    [LAST_UPDATE_USER] => -100013
    //                                                )
    //                                            [0.VAR_str_test1_2] => Array
    // 
    // T0019
    // aryChildVarId                 メンバー変数管理のPkey退避
    //                               [CHILD_VARS_NAME_ID ]=0
    // 
    // T0020                              
    // ifa_role_def_varsval_list     各ロール毎のdefault変数定義ファイルの変数具体値(多次元変数を除く)
    //                                Array  (
    //                                    [6] => Array        (
    //                                            [test_1] => Array                (
    //                                                    [VAR_std_test1] => Array                        (
    //                                                            ↓　一般変数
    //                                                            [0] => VAR_std_test_1
    //                                                        )
    //                                                    [VAR_list_test1] => Array                        (
    //                                                            ↓　複数具体値変数
    //                                                            [1] => Array                                (
    //                                                                    
    //                                                                    [1] => VAR_list_test1_1
    //                                                                    [2] => VAR_list_test1_2
    //                                                                    [3] => VAR_list_test1_3
    //                                                                )
    //                                                        )
    //                                                )
    //                                                               
    // T0021
    // all_def_vars_list             各ロール毎のdefault変数定義ファイルの変数リスト(多次元変数を除く)
    //                               [ロールパッケージ名][role名][変数名]=0　一般変数
    //                               [ロールパッケージ名][role名][変数名]=1　複数具体値変数
    //                                Array(
    //                                    [pkg] => Array   (
    //                                            [test_1] => Array           (
    //                                                    [VAR_std_test1] => 0
    //                                                    [VAR_list_test1] => 1
    //                                                )
    //                                            [test_2] => Array           (
    //                                                    [VAR_std_test2] => 0
    //                                                    [VAR_list_test2] => 1
    //                                                )
    //                                        )
    //                                 )
    // T0022
    // ifa_role_array_vars_list      各ロール毎のdefault変数定義ファイルの多次元変数リスト
    //                               [ROLE_PACKAGE_ID][role名][変数名]["DIFF_ARRAY"]
    //                               [ROLE_PACKAGE_ID][role名][変数名]["CHAIN_ARRAY"]
    //                               [ROLE_PACKAGE_ID][role名][変数名]["COL_COUNT_LIST"]
    //                               [ROLE_PACKAGE_ID][role名][変数名]["VAR_VALUE"]
    //                                Array                               (
    //                                    [6] => Array                                       (
    //                                            [test_1] => Array                                               (
    //                                                    [VAR_str_test1] => Array                                                       (
    //                                                            多次元配列の構造ほ示す配列
    //                                                            [DIFF_ARRAY] => Array ()
    //                                                            多次元配列の各メンバー毎の情報
    //                                                            [CHAIN_ARRAY] => Array  (
    //                                                                    [0] => Array    (
    //                                                                            [PARENT_VARS_KEY_ID] => 0
    //                                                                            [VARS_KEY_ID] => 1
    //                                                                            [VARS_NAME] => 0
    //                                                                            [ARRAY_NEST_LEVEL] => 1
    //                                                                            [ASSIGN_SEQ_NEED] => 0
    //                                                                            [COL_SEQ_MEMBER] => 1
    //                                                                            [COL_SEQ_NEED] => 0
    //                                                                            [MEMBER_DISP] => 0
    //                                                                            [VRAS_NAME_PATH] => 0
    //                                                                            [VRAS_NAME_ALIAS] => 0
    //                                                                            [MAX_COL_SEQ] => 2
    //                                                                        )
    //                                                            多次元配列内の配列毎の配列数
    //                                                            [COL_COUNT_LIST] => Array   (
    //                                                                    [array1.array2.0] => 1
    //                                                                    [array1.array2.0.array2_2.0] => 2
    //                                                                    [array1.array2.0.array2_2.0.array2_2_2.0] => 3
    //                                                                )
    //                                                            多次元配列内の各メンバーの具体値
    //                                                            [VAR_VALUE] => Array    (
    //                                                                    [array1.array2.0.array2_1] => Array    (
    //                                                                            ↓　一般変数
    //                                                                            [0] => Array       (
    //                                                                                    ↓　配列内のメンバー変数の場合、配列の位置(0オリジン)
    //                                                                                        配列内のメンバー変数でない場合は [] となる。
    //                                                                                    [000] => 2_array2_1
    //                                                                                )
    //                                                                        )
    //                                                                    [array1.array2.0.array2_2.0.array2_2_3] => Array
    //                                                                        (
    //                                                                            ↓　複数具体値の場合
    //                                                                            [1] => Array  (
    //                                                                                    ↓　配列内のメンバー変数の場合、配列の位置(0オリジン)
    //                                                                                        配列内のメンバー変数でない場合は [] となる。
    //                                                                                    [000000] => Array  (
    //                                                                                            ↓　代入順序(1オリジン)
    //                                                                                            [1] => 2_array2_2_3_1_1
    //                                                                                            [2] => 2_array2_2_3_1_2
    //                                                                                        )
    //                                                                                    [000001] => Array  (
    //                                                                                            [1] => 2_array2_2_3_2_1
    //                                                                                            [2] => 2_array2_2_3_2_2
    //                                                                                        )
    //                                                                                )
    //                                                                        )
    //                                                                )
    // T0023
    // all_def_array_vars_list       各ロール毎のdefault変数定義ファイルの多次元変数リスト
    //                               [ロールパッケージ名][role名][変数名]["DIFF_ARRAY"]
    //                               [ロールパッケージ名][role名][変数名]["CHAIN_ARRAY"]
    //                               [ロールパッケージ名][role名][変数名]["COL_COUNT_LIST"]
    //                               [ロールパッケージ名][role名][変数名]["VAR_VALUE"]
    //                                Array                               (
    //                                    [pkg] => Array                                       (
    //                                            [test_1] => Array                                               (
    //                                                    [VAR_str_test1] => Array                                                       (
    //                                                            多次元配列の構造ほ示す配列
    //                                                            [DIFF_ARRAY] => Array ()
    //                                                            多次元配列の各メンバー毎の情報
    //                                                            [CHAIN_ARRAY] => Array  (
    //                                                                    [0] => Array    (
    //                                                                            [PARENT_VARS_KEY_ID] => 0
    //                                                                            [VARS_KEY_ID] => 1
    //                                                                            [VARS_NAME] => 0
    //                                                                            [ARRAY_NEST_LEVEL] => 1
    //                                                                            [ASSIGN_SEQ_NEED] => 0
    //                                                                            [COL_SEQ_MEMBER] => 1
    //                                                                            [COL_SEQ_NEED] => 0
    //                                                                            [MEMBER_DISP] => 0
    //                                                                            [VRAS_NAME_PATH] => 0
    //                                                                            [VRAS_NAME_ALIAS] => 0
    //                                                                            [MAX_COL_SEQ] => 2
    //                                                                        )
    //                                                            多次元配列内の配列毎の配列数
    //                                                            [COL_COUNT_LIST] => Array   (
    //                                                                    [array1.array2.0] => 1
    //                                                                    [array1.array2.0.array2_2.0] => 2
    //                                                                    [array1.array2.0.array2_2.0.array2_2_2.0] => 3
    //                                                                )
    //                                                            多次元配列内の各メンバーの具体値
    //                                                            [VAR_VALUE] => Array    (
    //                                                                    [array1.array2.0.array2_1] => Array    (
    //                                                                            ↓　一般変数
    //                                                                            [0] => Array       (
    //                                                                                    ↓　配列内のメンバー変数の場合、配列の位置(0オリジン)
    //                                                                                        配列内のメンバー変数でない場合は [] となる。
    //                                                                                    [000] => 2_array2_1
    //                                                                                )
    //                                                                        )
    //                                                                    [array1.array2.0.array2_2.0.array2_2_3] => Array
    //                                                                        (
    //                                                                            ↓　複数具体値の場合
    //                                                                            [1] => Array  (
    //                                                                                    ↓　配列内のメンバー変数の場合、配列の位置(0オリジン)
    //                                                                                        配列内のメンバー変数でない場合は [] となる。
    //                                                                                    [000000] => Array  (
    //                                                                                            ↓　代入順序(1オリジン)
    //                                                                                            [1] => 2_array2_2_3_1_1
    //                                                                                            [2] => 2_array2_2_3_1_2
    //                                                                                        )
    //                                                                                    [000001] => Array  (
    //                                                                                            [1] => 2_array2_2_3_2_1
    //                                                                                            [2] => 2_array2_2_3_2_2
    //                                                                                        )
    //                                                                                )
    //                                                                        )
    //                                                                )
    // T0024                         
    // aryAddVarNameList             現在未使用
    // 
    // T0025
    // lva_array_member_vars_id_list 多次元変数メンバー管理のPkey退避
    //                               [ARRAY_MEMBER_ID]=0
    // T0026
    // aryVarIdPerArrayMemberVarNameFromFiles  多次元メンバー変数をメンバー変数管理マスタの登録に必要な変数の情報を退避
    //                                         [VAR_NAME_ID][VAR_NAME][多次元メンバー変数][ARRAY_MEMBER_ID] = ARRAY_MEMBER_ID
    //                                         [VAR_NAME_ID][VAR_NAME][多次元メンバー変数][ASSIGN_SEQ_NEED] = ASSIGN_SEQ_NEED
    //                                         [VAR_NAME_ID][VAR_NAME][多次元メンバー変数][COL_SEQ_NEED] = COL_SEQ_NEED
    //                                         [VAR_NAME_ID][VAR_NAME][多次元メンバー変数][MEMBER_DISP] = MEMBER_DISP
    //                                          Array  (    
    //                                            [400] => Array (
    //                                                      [VAR_str_test1] => Array (
    //                                                              [0] => Array            (
    //                                                                      [ARRAY_MEMBER_ID] => 35
    //                                                                      [ASSIGN_SEQ_NEED] => 0
    //                                                                      [COL_SEQ_NEED] => 0
    //                                                                      [MEMBER_DISP] => 0
    //                                                                  )
    //                                                              [0.VAR_str_test1_1] => Array
    // 
    // T0027
    // lva_MemberColComb_list        多次元変数配列組合せ管理のデータ
    //                               [VARS_NAME_ID][VRAS_NAME_PATH][COL_SEQ_VALUE]=[COL_SEQ_COMBINATION_ID]
    // T0028
    // lva_OtherUserLastUpdate_vars_list      変数一覧の最終更新者が他プロセスの変数リスト
    //                                        [VARS_NAME_ID]=[VARS_NAME]
    // T0030
    // ifa_ITA2User_var_list
    // 
    // T0031
    // ifa_User2ITA_var_list
    // 
    // T0032
    // ifa_ITA2User_var_list_pkgid
    // 
    // T0033
    // lva_use_rpkg_translation_vars_pkey_list
    // 
    // T0034
    // lva_use_rpkg_translation_vars_list
    // 
    // T0035
    // lva_use_translation_vars_pkey_list
    // 
    // P0001　関連シーケンスをロックする
    // P0002　ロールパッケージ管理からデータ取得
    // P0003　ロールパッケージファイル(ZIP)を解凍しロール名と変数名を取得
    // P0004　ロールパッケージファイルからロール名と変数名を取得
    // P0005-1　全ロールパッケージファイルで読替変数と任意変数の組合せが一致しないものをリストアップ
    // P0005-2　全ロールパッケージファイルで定義変数で変数の構造が違うものをリストアップ
    // P0006　ロールパッケージファイル ロール名リストのロール名をロール管理に反映
    // P0007　ロール内のPlaybookで使用している変数名をロール変数管理に反映
    // P0008　デフォルト変数定義ファイルのみに定義されている変数をロール変数名をロール変数管理に反映
    // P0009　デフォルト変数定義ファイルのみに定義されている多次元変数をロール変数名をロール変数管理に反映
    // P0010　ロール管理に登録されているロールでロールパッケージファイルで使用していないロールを廃止する。
    // P0011-1　ロール変数管理に登録されているロール変数でロールパッケージファイルで使用していないロール変数を廃止する。
    // P0011-2  読替表の内容をロールパッケージ毎読替表テーブルに反映する。
    // P0011-3  ロールパッケージ毎読替表テーブルから不要なデータを廃止する。
    // P0012　作業パターン詳細から必要なデータを取得
    // P0013　ロールパッケージファイル内の変数名の一意リスト作成
    // P0014　変数マスタの情報取得
    // P0015　変数リスト(一意)を元に変数マスタにロール変数を登録
    // P0016　変数リスト(一意)になくて変数マスタにある変数を廃止する。
    // P0017　多次元変数メンバー変数を多次元変数メンバー管理に登録する。
    // P0018  多次元変数メンバー管理から不要なレコードを削除する。
    // P0019  メンバー変数マスタの情報取得
    // P0020  多次元配列のメンバー変数をメンバー変数マスタに登録
    // P0021  メンバー変数マスタの不要なメンバー変数を廃止する。
    // P0022  多次元変数最大繰返数管理の更新
    // P0023  多次元変数配列組合せ管理の更新
    // P0024  デフォルト変数定義ファイルに定義されている変数の具体値をロール変数具体値管理に反映
    // P0025  多次元変数配列組合せ管理からデータを取得する。
    // P0026  デフォルト変数定義ファイルに定義されている多次元変数の具体値をロール変数具体値管理に反映
    // P0027  ロール変数具体値管理に反映で使用していない変数の情報を廃止する。
    // P0028  作業パターン変数紐付管理からデータを取得
    // P0029  作業パターン詳細を元に作業パターン変数紐付マスタに紐付を登録
    // P0030  作業パターン変数紐付マスタあって作業パターン詳細にない紐付情報を廃止する。
    // P0031  コミット(レコードロックを解除)
    //
    // F0001  getRolePackageDB
    // F0002  getRolePackageInfo
    // F0003  getAnsible_RolePackage_file
    // F0004  addRoleDB
    // F0005  addRoleVarsDB
    // F0006  delRolesDB
    // F0007  getPatternLinkDB
    // F0008  getVarsMasterDB
    // F0009  getPatternVarsLinkDB
    // F0010  getChildVarsMasterDB
    // F0011  chkVarsAttributeError
    // F0012  chkVarsAttribute
    // F0015  addRoleVarsValDB
    // F0016  delRoleVarsValDB
    //
    ////////////////////////////////////////////////////////////////////////

    // 起動しているshellの起動判定を正常にするための待ち時間
    sleep(1);
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    ////////////////////////////////
    // PHPエラー時のログ出力先設定//
    ////////////////////////////////
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";
    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $hostvar_search_php  = '/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php';
    $ansible_common_php  = '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php';
    $legacy_role_common_php = '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php';
    $ansible_nestedVariableExpanders_php = '/libs/backyardlibs/ansible_driver/ansible_nestedVariableExpander.php';   

    $db_access_user_id   = -100013;

    //----変数名テーブル関連
    $strCurTableAnsVarsTable = "B_ANSIBLE_LRL_VARS_MASTER";
    $strJnlTableAnsVarsTable = "B_ANSIBLE_LRL_VARS_MASTER_JNL";
    $strSeqOfCurTableAnsVars = "B_ANSIBLE_LRL_VARS_MASTER_RIC";
    $strSeqOfJnlTableAnsVars = "B_ANSIBLE_LRL_VARS_MASTER_JSQ";

    $arrayConfigOfAnsVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_ATTRIBUTE_01"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfAnsVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_ATTRIBUTE_01"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    //変数名テーブル関連----

    //----メンバー変数管理テーブル関連
    $strCurTableAnsChlVarsTable = "B_ANSIBLE_LRL_CHILD_VARS";
    $strJnlTableAnsChlVarsTable = "B_ANSIBLE_LRL_CHILD_VARS_JNL";
    $strSeqOfCurTableAnsChlVars = "B_ANSIBLE_LRL_CHILD_VARS_RIC";
    $strSeqOfJnlTableAnsChlVars = "B_ANSIBLE_LRL_CHILD_VARS_JSQ";
    $arrayConfigOfAnsChlVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CHILD_VARS_NAME_ID"=>"", 
        "PARENT_VARS_NAME_ID"=>"",
        "CHILD_VARS_NAME"=>"",    
        "ARRAY_MEMBER_ID"=>"",
        "ASSIGN_SEQ_NEED"=>"",
        "COL_SEQ_NEED"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfAnsChlVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CHILD_VARS_NAME_ID"=>"", 
        "PARENT_VARS_NAME_ID"=>"",
        "CHILD_VARS_NAME"=>"",    
        "ARRAY_MEMBER_ID"=>"",
        "ASSIGN_SEQ_NEED"=>"",
        "COL_SEQ_NEED"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    //メンバー変数管理テーブル関連----

    //----作業パターン変数名紐付テーブル関連
    $strCurTableAnsPatternVarsLink = "B_ANS_LRL_PTN_VARS_LINK";
    $strJnlTableAnsPatternVarsLink = "B_ANS_LRL_PTN_VARS_LINK_JNL";
    $strSeqOfCurTableAnsPatternVarsLink = "B_ANS_LRL_PTN_VARS_LINK_RIC";
    $strSeqOfJnlTableAnsPatternVarsLink = "B_ANS_LRL_PTN_VARS_LINK_JSQ";

    $arrayConfigOfAnsPatternVarsLink = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_LINK_ID"=>"",
        "PATTERN_ID"=>"",
        "VARS_NAME_ID"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfAnsPatternVarsLink = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_LINK_ID"=>"",
        "PATTERN_ID"=>"",
        "VARS_NAME_ID"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    //作業パターン変数名紐付テーブル関連----

    //ロールパッケージ管理テーブル関連----
    $strCurTableRolePackeage            = "B_ANSIBLE_LRL_ROLE_PACKAGE";
    $strJnlTableRolePackeage            = "B_ANSIBLE_LRL_ROLE_PACKAGE_JNL";
    $strSeqOfCurTableRolePackeage       = "B_ANSIBLE_LRL_ROLE_PACKAGE_RIC";
    $strSeqOfJnlTableRolePackeage       = "B_ANSIBLE_LRL_ROLE_PACKAGE_JSQ";

    // ロールパッケージ管理
    $arrayConfigOf_RolPkg_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_PACKAGE_NAME"=>"",
        "ROLE_PACKAGE_FILE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_RolPkg_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_PACKAGE_NAME"=>"",
        "ROLE_PACKAGE_FILE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //ロール管理テーブル関連----
    $strCurTableRole            = "B_ANSIBLE_LRL_ROLE";
    $strJnlTableRole            = "B_ANSIBLE_LRL_ROLE_JNL";
    $strSeqOfCurTableRole       = "B_ANSIBLE_LRL_ROLE_RIC";
    $strSeqOfJnlTableRole       = "B_ANSIBLE_LRL_ROLE_JSQ";

    // ロール管理
    $arrayConfigOf_Rol_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROLE_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_Rol_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROLE_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //ロール変数テーブル関連----
    $strCurTableRoleVars            = "B_ANSIBLE_LRL_ROLE_VARS";
    $strJnlTableRoleVars            = "B_ANSIBLE_LRL_ROLE_VARS_JNL";
    $strSeqOfCurTableRoleVars       = "B_ANSIBLE_LRL_ROLE_VARS_RIC";
    $strSeqOfJnlTableRoleVars       = "B_ANSIBLE_LRL_ROLE_VARS_JSQ";

    // ロール変数管理
    $arrayConfigOf_RolVars_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_ATTRIBUTE_01"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_RolVars_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_ATTRIBUTE_01"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //ロール変数具体値管理テーブル関連----
    $strCurTableRoleVarsVal            = "B_ANS_LRL_ROLE_VARSVAL";
    $strJnlTableRoleVarsVal            = $strCurTableRoleVarsVal . "_JNL";
    $strSeqOfCurTableRoleVarsVal       = $strCurTableRoleVarsVal . "_RIC";
    $strSeqOfJnlTableRoleVarsVal       = $strCurTableRoleVarsVal . "_JSQ";

    //ロール変数具体値管理
    $arrayConfigOf_RolVarsVal_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARSVAL_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "VAR_TYPE"=>"",
        "VARS_NAME_ID"=>"",
        "COL_SEQ_COMBINATION_ID"=>"",
        "ASSIGN_SEQ"=>"",
        "VARS_VALUE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_RolVarsVal_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARSVAL_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "VAR_TYPE"=>"",
        "VARS_NAME_ID"=>"",
        "COL_SEQ_COMBINATION_ID"=>"",
        "ASSIGN_SEQ"=>"",
        "VARS_VALUE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    // 多次元変数メンバー管理　B_ANS_LRL_ARRAY_MEMBER
    $strCurTableArrayMember            = "B_ANS_LRL_ARRAY_MEMBER";
    $strJnlTableArrayMember            = $strCurTableArrayMember . "_JNL";
    $strSeqOfCurTableArrayMember       = $strCurTableArrayMember . "_RIC";
    $strSeqOfJnlTableArrayMember       = $strCurTableArrayMember . "_JSQ";

   $arrayConfigOf_ArrayMember_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ARRAY_MEMBER_ID"=>"",
        "VARS_NAME_ID"=>"",
        "PARENT_VARS_KEY_ID"=>"",
        "VARS_KEY_ID"=>"",
        "VARS_NAME"=>"",
        "ARRAY_NEST_LEVEL"=>"",
        "ASSIGN_SEQ_NEED"=>"",
        "COL_SEQ_NEED"=>"",
        "MEMBER_DISP"=>"",
        "MAX_COL_SEQ"=>"",
        "VRAS_NAME_PATH"=>"",
        "VRAS_NAME_ALIAS"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_ArrayMember_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ARRAY_MEMBER_ID"=>"",
        "VARS_NAME_ID"=>"",
        "PARENT_VARS_KEY_ID"=>"",
        "VARS_KEY_ID"=>"",
        "VARS_NAME"=>"",
        "ARRAY_NEST_LEVEL"=>"",
        "ASSIGN_SEQ_NEED"=>"",
        "COL_SEQ_NEED"=>"",
        "MEMBER_DISP"=>"",
        "MAX_COL_SEQ"=>"",
        "VRAS_NAME_PATH"=>"",
        "VRAS_NAME_ALIAS"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    // 多次元変数最大繰返数管理　B_ANS_LRL_MAX_MEMBER_COL
    $strCurTableMaxNumberCol           = "B_ANS_LRL_MAX_MEMBER_COL";
    $strJnlTableMaxNumberCol           = $strCurTableMaxNumberCol . "_JNL";
    $strSeqOfCurTableMaxNumberCol      = $strCurTableMaxNumberCol . "_RIC";
    $strSeqOfJnlTableMaxNumberCol      = $strCurTableMaxNumberCol . "_JSQ";

    // 多次元変数配列組合せ管理　B_ANS_LRL_MEMBER_COL_COMB
    $strCurTableMemberColComb          = "B_ANS_LRL_MEMBER_COL_COMB";
    $strJnlTableMemberColComb          = $strCurTableMemberColComb . "_JNL";
    $strSeqOfCurTableMemberColComb     = $strCurTableMemberColComb . "_RIC";
    $strSeqOfJnlTableMemberColComb     = $strCurTableMemberColComb . "_JSQ";
    $arrayConfigOf_MemberColComb_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "COL_SEQ_COMBINATION_ID"=>"",
        "VARS_NAME_ID"=>"",
        "ARRAY_MEMBER_ID"=>"",
        "COL_COMBINATION_MEMBER_ALIAS"=>"",
        "COL_SEQ_VALUE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_MemberColComb_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "COL_SEQ_COMBINATION_ID"=>"",
        "VARS_NAME_ID"=>"",
        "ARRAY_MEMBER_ID"=>"",
        "COL_COMBINATION_MEMBER_ALIAS"=>"",
        "COL_SEQ_VALUE"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    // ロールパッケージ毎読替変数管理
    $strCurTableRpRepVar               = "B_ANS_LRL_RP_REP_VARS_LIST";
    $strJnlTableRpRepVar               = $strCurTableRpRepVar . "_JNL";
    $strSeqOfCurTableRpRepVar          = $strCurTableRpRepVar . "_RIC";
    $strSeqOfJnlTableRpRepVar          = $strCurTableRpRepVar . "_JSQ";
    $arrayConfigOf_RpRepVar_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROW_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "REP_VARS_NAME"=>"",
        "ANY_VARS_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_RpRepVar_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROW_ID"=>"",
        "ROLE_PACKAGE_ID"=>"",
        "ROLE_ID"=>"",
        "REP_VARS_NAME"=>"",
        "ANY_VARS_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    // 毎読替変数管理
    $strCurTableRepVar                 = "B_ANS_LRL_REP_VARS_LIST";
    $strJnlTableRepVar                 = $strCurTableRepVar . "_JNL";
    $strSeqOfCurTableRepVar            = $strCurTableRepVar . "_RIC";
    $strSeqOfJnlTableRepVar            = $strCurTableRepVar . "_JSQ";
    $arrayConfigOf_RepVar_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "REP_VARS_ID"=>"",
        "REP_VARS_NAME"=>"",
        "ANY_VARS_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOf_RepVar_Table = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "REP_VARS_ID"=>"",
        "REP_VARS_NAME"=>"",
        "ANY_VARS_NAME"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    // VARS_ATTRIBUTE_01 の 具体値定義
    const LC_VARS_ATTR_STD          = '1';   // 一般変数
    const LC_VARS_ATTR_LIST         = '2';   // 複数具体値
    const LC_VARS_ATTR_STRUCT       = '3';   // 多次元変数

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    $db_update_flg              = false;    // DB更新フラグ

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        require_once ($root_dir_path . $ansible_common_php);
        require_once ($root_dir_path . $legacy_role_common_php);

        require_once ($root_dir_path . $hostvar_search_php);

        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );


        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55003");
            require ($root_dir_path . $log_output_php );
        }


        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースが更新されバックヤード処理が必要か判定
        ///////////////////////////////////////////////////////////////////////////
        $lv_a_proc_loaded_list_pkey = 2100020005;
        $lv_UpdateRecodeInfo        = array();
        $ret = chkBackyardExecute($lv_a_proc_loaded_list_pkey,
                                  $lv_UpdateRecodeInfo);

        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90303");
            throw new Exception($errorMsg);
        }


        if(count($lv_UpdateRecodeInfo) == 0) {
            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70053");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            exit(0);
        }

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55004");
            require ($root_dir_path . $log_output_php );
        }

        ///////////////////////////////////////////////////
        // P0001                                         //
        // 関連シーケンスをロックする                    //
        //        デッドロック防止のために、昇順でロック //
        ///////////////////////////////////////////////////
        //----デッドロック防止のために、昇順でロック
        $aryTgtOfSequenceLock = array(
            $strSeqOfCurTableAnsVars,
            $strSeqOfJnlTableAnsVars,
            $strSeqOfCurTableAnsPatternVarsLink,
            $strSeqOfJnlTableAnsPatternVarsLink,
            $strSeqOfCurTableRole,
            $strSeqOfJnlTableRole,
            $strSeqOfCurTableRoleVars,
            $strSeqOfJnlTableRoleVars,
            $strSeqOfCurTableAnsChlVars,
            $strSeqOfJnlTableAnsChlVars,
            $strSeqOfCurTableArrayMember,
            $strSeqOfJnlTableArrayMember,
            $strSeqOfCurTableMaxNumberCol,
            $strSeqOfJnlTableMaxNumberCol,
            $strSeqOfCurTableMemberColComb,
            $strSeqOfJnlTableMemberColComb,
            $strSeqOfCurTableRpRepVar,
            $strSeqOfJnlTableRpRepVar
        );
        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($strSeqName,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( 'Lock sequence has failed.(' . $strSeqName . ')');
            }
        }
        //デッドロック防止のために、昇順でロック----

        //////////////////////////////////////////////////////////////////////////////
        // P0002
        // ロールパッケージ管理からデータ取得
        //////////////////////////////////////////////////////////////////////////////
        // T0001
        $lta_role_package_list = array();

        // T0002
        $lta_role_package_id_list = array();

        $ret = getRolePackageDB($lta_role_package_list,$lta_role_package_id_list,$warning_flag);
        if( $ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001010")) );
        }

        //////////////////////////////////////////////////////////////////////////////
        // P0003
        // ロールパッケージファイル(ZIP)を解凍しロール名と変数名を取得
        //////////////////////////////////////////////////////////////////////////////
        //  T0003
        $ifa_role_name_list = array();

        //  T0004
        $ifa_role_var_list  = array();

        //  T0013
        $ifa_role_def_var_list = array();

        //  T0022
        $ifa_role_array_vars_list = array();

        //  T0020
        $ifa_role_def_varsval_list = array();

        //  T0014
        $ifa_err_vars_list     = array();
 
        //  T0021
        $all_def_vars_list = array();

        //  T0023
        $all_def_array_vars_list = array();

        //  T0030
        $ifa_ITA2User_var_list = array();
        //  T0031
        $ifa_User2ITA_var_list = array();
        //  T0032
        $ifa_ITA2User_var_list_pkgid = array();
        //  T0033
        $lva_use_rpkg_translation_vars_pkey_list = array();
        //  T0034
        $lva_use_rpkg_translation_vars_list = array();
        //  T0035
        $lva_use_translation_vars_pkey_list = array();

        foreach($lta_role_package_list as $role_package_id=>$role_package_list){
            foreach($role_package_list as $role_package_name=>$role_package_file){

                // ロールパッケージファイル(ZIP)の解凍先
                $roledir  = "/tmp/LegacyRoleZipvarget_" . getmypid();
                exec("/bin/rm -rf " . $roledir);

                // ロールパッケージファイル(ZIP)を解析するクラス生成
                $objRole = new CheckAnsibleRoleFiles($objMTS);

                /////////////////////////////////////////////////////////////////////
                // P0004
                // ロールパッケージファイルからロール名と変数名を取得
                /////////////////////////////////////////////////////////////////////
                $role_name_list = array();
                $role_var_list  = array();

                $role_def_var_list     = array();
                $role_def_var_err_list = array();

                $role_def_varsval_list = array();

                $role_def_array_var_list = array();

                $ITA2User_var_list = array();
                $User2ITA_var_list = array();

                $ret = getRolePackageInfo($role_package_id,
                                          $role_package_file,
                                          $roledir,
                                          $role_name_list,
                                          $role_var_list,
                                          $role_package_name,
                                          $role_def_var_list,
                                          $role_def_varsval_list,
                                          $role_def_array_vars_list,
                                          $ITA2User_var_list,
                                          $User2ITA_var_list);

                if($ret === false){
                    $warning_flag = 1;

// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70029",array($role_package_name));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                }
                else{
                    // rolesディレクトリ配下のロール名リスト返却
                    $ifa_role_name_list[$role_package_id] = $role_name_list;

                    // ロール内のPlaybookで使用している変数名リスト返却
                    $ifa_role_var_list[$role_package_id]  = $role_var_list;

                    // ロールパッケージファイル default定義数名リスト退避
                    $ifa_role_def_var_list[$role_package_id] = $role_def_var_list;

                    // 全 ロールパッケージファイル default定義数名リスト退避
                    $all_def_vars_list[$role_package_name] = $role_def_var_list;

                    // デフォルト定義変数で変数の構造が違うものをリストアップ
                    foreach ($role_def_var_err_list as $err_var_name=>$dummy){
                        $ifa_err_vars_list[$err_var_name] = 0;
                    }

                    // ロールパッケージファイル default定義数名リスト退避
                    $ifa_role_array_vars_list[$role_package_id]  = $role_def_array_vars_list;
                    // 全 ロールパッケージファイル default定義 多次元配列リスト退避
                    $all_def_array_vars_list[$role_package_name] = $role_def_array_vars_list;

                    // ロールパッケージファイル default定義変数の具体値リスト
                    $ifa_role_def_varsval_list[$role_package_id] = $role_def_varsval_list;

                    $ifa_ITA2User_var_list[$role_package_name] = $ITA2User_var_list;
                    $ifa_User2ITA_var_list[$role_package_name] = $User2ITA_var_list;
                    $ifa_ITA2User_var_list_pkgid[$role_package_id] = $ITA2User_var_list;
                }
                exec("/bin/rm -rf " . $roledir);

                //リソース解放
                unset($objRole);

            }
        }

        ///////////////////////////////////////////////////////////////////////////
        // P0005-2
        // 全ロールパッケージファイルで定義変数で変数の構造が違うものをリストアップ
        ///////////////////////////////////////////////////////////////////////////
        $Obj = new DefaultVarsFileAnalysis($objMTS);

        $err_vars_list = array();

        $ret = $Obj->chkallVarsStruct($all_def_vars_list, $all_def_array_vars_list ,$err_vars_list);
        if($ret === false){
            foreach ($err_vars_list as $err_var_name=>$dummy){
                $ifa_err_vars_list[$err_var_name] = 0;
            }

if ( $log_level === 'DEBUG' ){
            // エラーになった変数情報を設定
            $errmag = $Obj->allVarsStructErrmsgEdit($err_vars_list);
            LocalLogPrint(basename(__FILE__),__LINE__,$errmag);
}
        }
        unset($Obj);

        ///////////////////////////////////////////////////////////////////////////
        // P0006
        // ロールパッケージファイル ロール名リストのロール名をロール管理に反映
        ///////////////////////////////////////////////////////////////////////////
        // T0005
        $lva_use_role_name_list = array();

        // T0006
        $lva_role_nameTOrole_id = array();

        foreach($ifa_role_name_list as $role_package_id=>$role_name_list){
            // ロールパッケージファイル ロール名
            foreach($role_name_list as $role_name){
                // ロールパッケージファイル内のロール名がロール管理に登録されているか確認
                $ret = addRoleDB($strCurTableRole,           $strJnlTableRole,
                                 $strSeqOfCurTableRole,      $strSeqOfJnlTableRole,
                                 $arrayConfigOf_Rol_Table,   $arrayValueTmplOf_Rol_Table,
                                 $role_package_id,$role_name,$db_access_user_id,$role_id);
                if($ret === false){
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001020")) );
                }
                // ロール管理に登録が必要なロール情報を退避
                $lva_use_role_name_list[$role_package_id][$role_id] = 1;
                $lva_role_nameTOrole_id[$role_package_id][$role_name] = $role_id;
            }
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0007
        // ロール内のPlaybookで使用している変数名をロール変数管理に反映
        //////////////////////////////////////////////////////////////////////////////////
        // T0007
        $lva_use_role_vars_name_list       = array();
        // T0015 現在未使用     
        $lva_use_role_child_vars_name_list = array();
        // T0016 現在未使用
        $aryChildVarNameFromFiles          = array();
        // T0024 現在未使用
        $aryAddVarNameList                 = array();

        // T0017
        // $lva_vars_attr_list[変数名]=変数型 
        $lva_vars_attr_list = array();        
        //////////////////////////////////////////////////////////////////////////////////
        // P0008
        // デフォルト変数定義ファイルのみに定義されている変数をロール変数名をロール変数管理に反映
        //////////////////////////////////////////////////////////////////////////////////
        foreach($ifa_role_def_var_list as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$role_vars_name_list){
                foreach($role_vars_name_list as $role_vars_name=>$dummy){
                    // 一般変数と複数具体値変数の場合を区別するデータがvalue($dummy)に入っている
                    if($dummy == 0){
                        // 一般変数の場合
                        $vars_attr = LC_VARS_ATTR_STD;
                    }
                    else{
                        // 複数具体値の場合
                        $vars_attr = LC_VARS_ATTR_LIST;
                    }
                    // 変数の型を退避
                    $lva_vars_attr_list[$role_vars_name] = $vars_attr;

                    // ロール名からロールIDを求める
                    if(isset($lva_role_nameTOrole_id[$role_package_id][$role_name]) === false){
                        $warning_flag = 1;

// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //ロール名からロールIDが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70030",array($role_name,$role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}

                        //次へ
                        continue;
                    }
                    $role_id = $lva_role_nameTOrole_id[$role_package_id][$role_name];

                    // デフォルト変数定義ファイルのみに定義されているか判定
                    if(@count($aryAddVarNameList[$role_package_id][$role_name][$role_vars_name]) != 0){
                        continue;
                    }

                    // 該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
                    $ret = chkVarsAttributeError($role_vars_name,true);
                    if($ret === false){
                        // 変数構造エラーリストの変数は変数一覧に登録しない。
                        continue;
                    }

                    // 親変数を登録
                    $ret = addRoleVarsDB($strCurTableRoleVars,            $strJnlTableRoleVars,
                                         $strSeqOfCurTableRoleVars,       $strSeqOfJnlTableRoleVars,
                                         $arrayConfigOf_RolVars_Table,    $arrayValueTmplOf_RolVars_Table,
                                         $role_package_id,$role_id,$role_vars_name,
                                         $vars_attr,
                                         $db_access_user_id,$role_vars_name_id);

                    if($ret === false){
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                    }

                    // ロール変数管理に登録が必要なロール変数を退避
                    $lva_use_role_vars_name_list[$role_package_id][$role_id][$role_vars_name_id] = $role_vars_name;
                }
            }
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0009
        // デフォルト変数定義ファイルのみに定義されている多次元変数をロール変数名をロール変数管理に反映
        //////////////////////////////////////////////////////////////////////////////////
        foreach($ifa_role_array_vars_list as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$var_list){
                foreach($var_list as $role_vars_name=>$info_list){
                    // 変数の型を退避
                    $lva_vars_attr_list[$role_vars_name] = LC_VARS_ATTR_STRUCT;

                    // ロール名からロールIDを求める
                    if(isset($lva_role_nameTOrole_id[$role_package_id][$role_name]) === false){
                        $warning_flag = 1;

// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //ロール名からロールIDが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70030",array($role_name,$role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}

                        //次へ
                        continue;
                    }
                    $role_id = $lva_role_nameTOrole_id[$role_package_id][$role_name];

                    // デフォルト変数定義ファイルのみに定義されているか判定
                    if(@count($aryAddVarNameList[$role_package_id][$role_name][$role_vars_name]) != 0){
                        continue;
                    }

                    // 該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
                    $ret = chkVarsAttributeError($role_vars_name,true);
                    if($ret === false){
                        // 変数構造エラーリストの変数は変数一覧に登録しない。
                        continue;
                    }

                    $vars_attr = LC_VARS_ATTR_STRUCT;

                    // 配列変数の場合は親変数として登録
                    $ret = addRoleVarsDB($strCurTableRoleVars,            $strJnlTableRoleVars,
                                         $strSeqOfCurTableRoleVars,       $strSeqOfJnlTableRoleVars,
                                         $arrayConfigOf_RolVars_Table,    $arrayValueTmplOf_RolVars_Table,
                                         $role_package_id,$role_id,$role_vars_name,
                                         $vars_attr,
                                         $db_access_user_id,$role_vars_name_id);

                    if($ret === false){
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                    }

                    // ロール変数管理に登録が必要なロール変数を退避
                    $lva_use_role_vars_name_list[$role_package_id][$role_id][$role_vars_name_id] = $role_vars_name;
                }
            }
        }

        /////////////////////////////////////////////////////////////////////////////////
        // P0010
        // ロール管理に登録されているロールでロールパッケージファイルで使用していない
        // ロールを廃止する。
        /////////////////////////////////////////////////////////////////////////////////
        $ret = delRolesDB($strCurTableRole,                $strJnlTableRole,
                          $strSeqOfCurTableRole,           $strSeqOfJnlTableRole,
                          $arrayConfigOf_Rol_Table,        $arrayValueTmplOf_Rol_Table,
                          $strCurTableRoleVars,            $strJnlTableRoleVars,
                          $strSeqOfCurTableRoleVars,       $strSeqOfJnlTableRoleVars,
                          $arrayConfigOf_RolVars_Table,    $arrayValueTmplOf_RolVars_Table,
                          "ROLE",$lva_use_role_name_list,  $lva_use_role_vars_name_list,
                          $lva_use_role_child_vars_name_list,
                          $db_access_user_id);
        if($ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001040")) );
        }

        //////////////////////////////////////////////////////////////////////////////////////////
        // P0011-1
        // ロール変数管理に登録されているロール変数でロールパッケージファイルで使用していない
        // ロール変数を廃止する。
        //////////////////////////////////////////////////////////////////////////////////////////
        $ret = delRolesDB($strCurTableRole,                $strJnlTableRole,
                          $strSeqOfCurTableRole,           $strSeqOfJnlTableRole,
                          $arrayConfigOf_Rol_Table,        $arrayValueTmplOf_Rol_Table,
                          $strCurTableRoleVars,            $strJnlTableRoleVars,
                          $strSeqOfCurTableRoleVars,       $strSeqOfJnlTableRoleVars,
                          $arrayConfigOf_RolVars_Table,    $arrayValueTmplOf_RolVars_Table,
                          "ROLEVARS",$lva_use_role_name_list,$lva_use_role_vars_name_list,
                          $lva_use_role_child_vars_name_list,
                          $db_access_user_id);
        if($ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001050")) );
        }

        //////////////////////////////////////////////////////////////////////////////////////////
        // P0011-2
        // 読替表の内容をロールパッケージ毎読替表テーブルに反映する。
        //////////////////////////////////////////////////////////////////////////////////////////
        // ロールパッケージ毎読替表にデータを反映
        foreach($ifa_ITA2User_var_list_pkgid as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$vars_name_list){
                foreach($vars_name_list as $ita_vars_name=>$user_vars_name){
                    // ロール名からロールIDが求められるか判定
                    if(isset($lva_role_nameTOrole_id[$role_package_id][$role_name]) === false){
                        $warning_flag = 1;

// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //ロール名からロールIDが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70030",array($role_name,$role_package_id,$role_name,$role_ita_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}

                        //次へ
                        continue;
                    }
                    // ロール名からロールIDを求める
                    $role_id = $lva_role_nameTOrole_id[$role_package_id][$role_name];
                    // 該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
                    $ret = chkVarsAttributeError($ita_vars_name,true);
                    if($ret === false){
                        // 変数構造エラーリストの変数は変数一覧に登録しない。
                        continue;
                    }
    
                    // ロールパッケージ毎読替表にデータを反映
                    $value_list = $arrayValueTmplOf_RpRepVar_Table;
                    $value_list["ROLE_PACKAGE_ID"] = $role_package_id;
                    $value_list["ROLE_ID"]         = $role_id;
                    $value_list["REP_VARS_NAME"]   = $ita_vars_name;
                    $value_list["ANY_VARS_NAME"]   = $user_vars_name;
                    $value_list["DISUSE_FLAG"]     = '0';
            
                    $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID = $role_package_id  AND " .
                                                 "ROLE_ID         = $role_id          AND " .
                                                 "REP_VARS_NAME   = '$ita_vars_name'  AND " .
                                                 "ANY_VARS_NAME   = '$user_vars_name'     ");
                    $pkey_name = "ROW_ID";
                    $ret = addTranslationVarsDB($strCurTableRpRepVar,            $strJnlTableRpRepVar,
                                                $strSeqOfCurTableRpRepVar,       $strSeqOfJnlTableRpRepVar,
                                                $arrayConfigOf_RpRepVar_Table,   $value_list,
                                                $temp_array,                     $pkey_name,
                                                $db_access_user_id,              $pky_id);
                    if($ret === false){
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                    }
                
                    // 登録・更新した変数情報を退避
                    $lva_use_rpkg_translation_vars_pkey_list[$pky_id] = 0;
                    $lva_use_rpkg_translation_vars_list[$ita_vars_name][$user_vars_name] = 0;
                }                                
            }                                
        }                                

       //////////////////////////////////////////////////////////////////////////////////////////
       // P0011-3
       // ロールパッケージ毎読替表テーブルから不要なデータを廃止する。
       //////////////////////////////////////////////////////////////////////////////////////////

       // ロールパッケージ毎読替表から不要なデータを削除する。
       $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ");
       $pkey_name = "ROW_ID";
       $ret = delTranslationVarsDB($strCurTableRpRepVar,            $strJnlTableRpRepVar,
                                   $strSeqOfCurTableRpRepVar,       $strSeqOfJnlTableRpRepVar,
                                   $arrayConfigOf_RpRepVar_Table,   $arrayValueTmplOf_RpRepVar_Table,
                                   $temp_array,                     $pkey_name,
                                   $db_access_user_id,              $lva_use_rpkg_translation_vars_pkey_list);
       if($ret === false){
           $error_flag = 1;
           throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
       }                                

        /////////////////////////////////////////////////////////////////////////////////
        // P0012
        // 作業パターン詳細から必要なデータを取得
        /////////////////////////////////////////////////////////////////////////////////
        // T0008
        $lta_pattern_list = array();
        $ret = getPatternLinkDB($lta_pattern_list);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001060")) );
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0013
        // ロールパッケージファイル内の変数名の一意リスト作成
        //////////////////////////////////////////////////////////////////////////////////
        // T0009
        $aryVarIdPerVarNameFromFiles = array();
        $aryVarIdPerVarNameFromFiles_fix = array();

        foreach($lva_use_role_vars_name_list as $role_package_id=>$role_id_list){
            foreach($role_id_list as $role_id=>$vars_name_list){
                foreach($vars_name_list as $vars_name_id=>$vars_name){
                    $aryVarIdPerVarNameFromFiles[$vars_name] = null;
                    $aryVarIdPerVarNameFromFiles_fix[$vars_name] = null;
                }
            }
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0014
        // 変数マスタの情報取得 
        //////////////////////////////////////////////////////////////////////////////////
        // T0010
        $aryRowFromAnsVarsTable = array();

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        $ret = getVarsMasterDB($strCurTableAnsVarsTable,
                               $strJnlTableAnsVarsTable,
                               $arrayConfigOfAnsVarsTable,
                               $arrayValueTmplOfAnsVarsTable,
                               $temp_array,
                               $aryRowFromAnsVarsTable);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001070")) );
        }

        // 最終更新者が他プロセスの変数リスト
        $lva_OtherUserLastUpdate_vars_list = array();
        ///////////////////////////////////////////////////////////////////////
        // P0015
        // 変数リスト(一意)を元に変数マスタにロール変数を登録
        ///////////////////////////////////////////////////////////////////////
        $tmpAryKeysOfVarIdPerVarNameFromFiles = array_keys($aryVarIdPerVarNameFromFiles);
        foreach($tmpAryKeysOfVarIdPerVarNameFromFiles as $strVarName){
            $intVarNameId = null;
            $boolLoopNext = false;
            $strSqlType = null;

            // ロールで使用している変数が変数マスタにあるか確認
            // aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
            if( array_key_exists($strVarName, $aryRowFromAnsVarsTable) === true ){
                //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                $aryRowOfTableUpdate = $aryRowFromAnsVarsTable[$strVarName];
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    //----SQLを発行せずループを抜けるフラグ、を立てる
                    $boolLoopNext = true;
                    //SQLを発行せずループを抜けるフラグ、を立てる----
                
                    // 最終更新者が自分以外の場合は更新しない
                    if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
$ary[70043] = "[処理]変数名管理 最終更新者が自分でないので更新スキップ \n｛｝";
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70043",
                                          array(print_r($aryRowOfTableUpdate,true))));
}
                        // 最終更新者が他処理の変数を退避
                        $lva_OtherUserLastUpdate_vars_list[$aryRowOfTableUpdate["VARS_NAME_ID"]] = $aryRowOfTableUpdate["VARS_NAME"];
                    }
                }
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                }
                else{
                    //----存在しないはずの、値なので、想定外エラーに倒す。
                    // 異常フラグON
                    $error_flag = 1;
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001080")) );
                    //存在しないはずの、値なので、想定外エラーに倒す。----
                }
                
                $strSqlType = "UPDATE";
                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

                // 配列変数かを判定
                $vars_attr = chkVarsAttribute($strVarName);

                // 変数マスタの属性(配列変数)を判定
                $db_attr   = $aryRowOfTableUpdate["VARS_ATTRIBUTE_01"];

                // 変数マスタの属性の変更をするか判定
                if($vars_attr != $db_attr){

                    // 変数マスタの属性の変更する。
                    $aryRowOfTableUpdate["VARS_ATTRIBUTE_01"] = $vars_attr;

                    if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0"){
                        // 最終更新者が自分以外の場合は更新しない
                        if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
$ary[70043] = "[処理]変数名管理 最終更新者が自分でないので更新スキップ \n｛｝";
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70043",
                                          array(print_r($aryRowOfTableUpdate,true))));
}
                            // 最終更新者が他処理の変数を退避
                            $lva_OtherUserLastUpdate_vars_list[$aryRowOfTableUpdate["VARS_NAME_ID"]] = $aryRowOfTableUpdate["VARS_NAME"];
                            //----SQLを発行しない
                            $boolLoopNext = true;
                        }
                        else{
                            //----SQLを発行するので、フラグは立てないまま維持する。
                            $boolLoopNext = false;
                            //SQLを発行するので、フラグは立てないまま維持する。----
                        }
                    }
                    else{
                        //----SQLを発行するので、フラグは立てないまま維持する。
                        $boolLoopNext = false;
                        //SQLを発行するので、フラグは立てないまま維持する。----
                    }
                }                
                //活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。----
            }
            else{
                //----テーブルにないので、新たに挿入する。
                $aryRowOfTableUpdate = $arrayValueTmplOfAnsVarsTable;

                // テーブルロック
                $retArray = getSequenceLockInTrz($strSeqOfCurTableAnsVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001090")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfCurTableAnsVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
                }
                $intVarNameId = $retArray[0];

                $strSqlType = "INSERT";
                $aryRowOfTableUpdate["VARS_NAME_ID"] = $intVarNameId;
                $aryRowOfTableUpdate["VARS_NAME"] = $strVarName;
                //テーブルにないので、新たに挿入する。----

                // 配列変数かを判定
                $vars_attr = chkVarsAttribute($strVarName);
                // 変数マスタの属性の変更する。
                $aryRowOfTableUpdate["VARS_ATTRIBUTE_01"] = $vars_attr;

            }
            // 作業パターン変数紐付テーブル、を更新するときの準備として、変数名IDを代入。
            $aryVarIdPerVarNameFromFiles[$strVarName] = $intVarNameId;

            if( $boolLoopNext === true ){
                //----すでにレコードがあり、活性化済('0')なので、次のループへ
                continue;
                //すでにレコードがあり、活性化済('0')なので、次のループへ----
            }

            $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsVars,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
            }
            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsVars, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
            }
            $intJournalSeqNo = $retArray[0];

            $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $intJournalSeqNo;
            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";
            $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

            $arrayConfig = $arrayConfigOfAnsVarsTable;
            $arrayValue = $aryRowOfTableUpdate;
            $temp_array = array();

            setDBUpdateflg();

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 $strSqlType,
                                                 "VARS_NAME_ID",
                                                 $strCurTableAnsVarsTable,
                                                 $strJnlTableAnsVarsTable,
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
            
            if( $objQueryUtn->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
            }
            if( $objQueryJnl->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
            }
            if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
            }
            
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
            }
            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($tmpAryKeysOfVarIdPerVarNameFromFiles);
        //実際にあるべき変数名をテーブルに反映させる【活性化】----

        ///////////////////////////////////////////////////////////////////////
        // P0016
        // 変数リスト(一意)になくて変数マスタにある変数を廃止する。
        ///////////////////////////////////////////////////////////////////////
        foreach($aryRowFromAnsVarsTable as $strVarName=>$row){

            // 作業パターン変数紐付テーブル、を更新するときの準備として、変数名IDを代入。
            $aryVarIdPerVarNameFromFiles[$strVarName] = $row["VARS_NAME_ID"];

            // $aryVarIdPerVarNameFromFilesは未使用の変数が追加されるのでaryVarIdPerVarNameFromFiles_fixで変数の使用・未使用を判定
            if( array_key_exists($strVarName, $aryVarIdPerVarNameFromFiles_fix) !== true ){

                //----廃止する
                $aryRowOfTableUpdate = $aryRowFromAnsVarsTable[$strVarName];

                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){

                    // 最終更新者が自分以外の場合は廃止しない
                    if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
//$ary[70040] = "[処理]変数名管理 最終更新者が自分でないので廃止スキップ \n｛｝";
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70040",
                                          array(print_r($aryRowOfTableUpdate,true))));
}
                       // 最終更新者が他処理の変数を退避
                       $lva_OtherUserLastUpdate_vars_list[$aryRowOfTableUpdate["VARS_NAME_ID"]] = $aryRowOfTableUpdate["VARS_NAME"];


                        continue;

                    }

                    //----廃止する
                    $strSqlType = "UPDATE";
                    //廃止する----
                }
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){

                    //----廃止するべきレコードで、すでに廃止されている。
                    continue;
                    //廃止するべきレコードで、すでに廃止されている。----
                }
                else{
                    //----想定外エラー
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
                    //想定外エラー----
                }

                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

                // テーブル　ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002100")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002200")) );
                }
                $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;
                
                $strSqlType = "UPDATE";
                
                $arrayConfig = $arrayConfigOfAnsVarsTable;
                $arrayValue  = $aryRowOfTableUpdate;
                $temp_array  = array();

                // DEBUGログに変更
                if ( $log_level === 'DEBUG' ){
// 更新ログ
print_r($arrayValue);
LocalLogPrint(basename(__FILE__),__LINE__,"変数マスタ　廃止($strSqlType)");
                }
                setDBUpdateflg();

                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     $strSqlType,
                                                     "VARS_NAME_ID",
                                                     $strCurTableAnsVarsTable,
                                                     $strJnlTableAnsVarsTable,
                                                     $arrayConfig,
                                                     $arrayValue,
                                                     $temp_array );

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                
                if( $objQueryUtn->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002300")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002400")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002500")) );
                }
                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002600")) );
                }
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002700")) );
                }
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002800")) );
                }                
                //廃止する----
                // DBアクセス事後処理
                unset($objQueryUtn);
                unset($objQueryJnl);
            }
        }
        //存在しているレコードで、もう実際にない変数名を廃止する----

        //////////////////////////////////////////////////////////////////////////////////
        // P0017
        // 多次元変数メンバー変数を多次元変数メンバー管理に登録する。
        //////////////////////////////////////////////////////////////////////////////////
        // T0025
        $lva_array_member_vars_id_list = array();
        // T0026
        $aryVarIdPerArrayMemberVarNameFromFiles = array();
        // パッケージ・ロールまたがりで同じ変数名の二重処理防止用
        $add_var_name_list = array();
        foreach($ifa_role_array_vars_list as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$var_list){
                foreach($var_list as $vars_name=>$info_list){

                    // パッケージ・ロールまたがりで同じ変数名の二重処理防止
                    if(@count($add_var_name_list[$vars_name]) != 0){
                        continue;
                    }
                    else{
                        $add_var_name_list[$vars_name] = 0;
                    }
                    // 変数一覧管理のPkeyを取得する。
                    if(@count($aryVarIdPerVarNameFromFiles[$vars_name]) == 0){
                        $warning_flag = 1;
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //多次元変数名を変数一覧管理から求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70094",array(vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                        //次へ
                        continue;
                    }
                    // 変数一覧管理から多次元変数名のPkey取得
                    $vars_name_id = $aryVarIdPerVarNameFromFiles[$vars_name];

                    // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
                    if(@count($lva_OtherUserLastUpdate_vars_list[$vars_name_id]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90223",
                                                                   array($vars_name)));

}

                        //次へ
                        continue;
                    }

                    foreach($info_list['CHAIN_ARRAY'] as $chl_vars_list){
                        $row_value = array();
                        $row_value['VARS_NAME_ID']       =  $vars_name_id;                             // 親変数へのキー 
                        $row_value['PARENT_VARS_KEY_ID'] =  $chl_vars_list['PARENT_VARS_KEY_ID'];      // 親メンバー変数へのキー 
                        $row_value['VARS_KEY_ID']        =  $chl_vars_list['VARS_KEY_ID'];             // 自メンバー変数のキー
                        $row_value['VARS_NAME']          =  $chl_vars_list['VARS_NAME'];               // メンバー変数名　　0:配列変数を示す
                        $row_value['ARRAY_NEST_LEVEL']   =  $chl_vars_list['ARRAY_NEST_LEVEL'];        // 階層 1～
                        $row_value['ASSIGN_SEQ_NEED']    =  $chl_vars_list['ASSIGN_SEQ_NEED'];         // 代入順序有無　1:必要　初期値:NULL
                        $row_value['COL_SEQ_NEED']       =  $chl_vars_list['COL_SEQ_NEED'];            // 列順序有無  　1:必要　初期値:NULL
                        $row_value['MEMBER_DISP']        =  $chl_vars_list['MEMBER_DISP'];             // 代入値管理系の表示有無　1:必要　初期値:NULL
                        $row_value['VRAS_NAME_PATH']     =  $chl_vars_list['VRAS_NAME_PATH'];          // メンバー変数の階層パス
                        $row_value['VRAS_NAME_ALIAS']    =  $chl_vars_list['VRAS_NAME_ALIAS'];         // 代入値管理系の表示メンバー変数名
                        $row_value['MAX_COL_SEQ']        =  $chl_vars_list['MAX_COL_SEQ'];             // 最大繰返数
                        
                        $ret = addArrayMemberDB($strCurTableArrayMember            ,$strJnlTableArrayMember,            
                                                  $strSeqOfCurTableArrayMember       ,$strSeqOfJnlTableArrayMember,       
                                                  $arrayConfigOf_ArrayMember_Table   ,$arrayValueTmplOf_ArrayMember_Table,
                                                  $row_value,
                                                  $db_access_user_id,$pkey);

                        if($ret === false){
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001050")) );
                        }
                        // 多次元変数メンバー管理のPkey退避
                        $lva_array_member_vars_id_list[$pkey] = 0;

                        // 多次元メンバ—変数をメンバー変数管理マスタの登録に必要な変数の情報を退避する。
                        $aryVarIdPerArrayMemberVarNameFromFiles[$vars_name_id][$vars_name][$chl_vars_list['VRAS_NAME_ALIAS']]['ARRAY_MEMBER_ID'] = $pkey;

                        $aryVarIdPerArrayMemberVarNameFromFiles[$vars_name_id][$vars_name][$chl_vars_list['VRAS_NAME_ALIAS']]['ASSIGN_SEQ_NEED']  = $chl_vars_list['ASSIGN_SEQ_NEED'];
                        $aryVarIdPerArrayMemberVarNameFromFiles[$vars_name_id][$vars_name][$chl_vars_list['VRAS_NAME_ALIAS']]['COL_SEQ_NEED']     = $chl_vars_list['COL_SEQ_NEED'];
                        $aryVarIdPerArrayMemberVarNameFromFiles[$vars_name_id][$vars_name][$chl_vars_list['VRAS_NAME_ALIAS']]['MEMBER_DISP']     = $chl_vars_list['MEMBER_DISP'];
                    }
                }
            }
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0018
        // 多次元変数メンバー管理から不要なレコードを削除する。
        //////////////////////////////////////////////////////////////////////////////////
        $ret = delDB($strCurTableArrayMember            ,$strJnlTableArrayMember,            
                     $strSeqOfCurTableArrayMember       ,$strSeqOfJnlTableArrayMember,       
                     $arrayConfigOf_ArrayMember_Table   ,$arrayValueTmplOf_ArrayMember_Table,
                     "ARRAY_MEMBER_ID"                  ,$lva_array_member_vars_id_list,
                     $db_access_user_id                 ,$lva_OtherUserLastUpdate_vars_list);
        if($ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001050")) );
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0019
        // メンバー変数マスタの情報取得 
        //////////////////////////////////////////////////////////////////////////////////
        // T0018
        $aryRowFromAnsChildVarsTable = array();

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        $ret = getChildVarsMasterDB($strCurTableAnsChlVarsTable,
                                    $strJnlTableAnsChlVarsTable,
                                    $arrayConfigOfAnsChlVarsTable,
                                    $arrayValueTmplOfAnsChlVarsTable,
                                    $temp_array,
                                    $aryRowFromAnsChildVarsTable);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001070")) );
        }

        // T0019
        $aryChildVarId = array();

        ///////////////////////////////////////////////////////////////////////
        // P0020
        // 多次元配列のメンバー変数をメンバー変数マスタに登録
        ///////////////////////////////////////////////////////////////////////
        // $aryVarIdPerArrayMemberVarNameFromFiles
        // Array (
        //     [392] => Array (
        //             [VAR_str_test1] => Array (
        //                     [0] => Array (
        //                             [ARRAY_MEMBER_ID] => 1
        //                             [ASSIGN_SEQ_NEED] => 0
        //                             [COL_SEQ_NEED] => 0
        //                             [MEMBER_DISP] => 0
        //                         )
        //                     [0.VAR_str_test1_1] => Array (
        //                             [ARRAY_MEMBER_ID] => 2
        //                             [ASSIGN_SEQ_NEED] => 0
        //                             [COL_SEQ_NEED] => 1
        //                             [MEMBER_DISP] => 1
        //                         )
        //                     [0.VAR_str_test1_2] => Array (
        //                             [ARRAY_MEMBER_ID] => 3
        //                             [ASSIGN_SEQ_NEED] => 0
        //                             [COL_SEQ_NEED] => 1
        //                             [MEMBER_DISP] => 1
        //                         )
        //                 )
        //         )
        //     [393] => Array
        //         (
        //             [VAR_array_test1] => Array

        foreach($aryVarIdPerArrayMemberVarNameFromFiles as $intVarID=>$vars_list){


            // aryVarIdPerArrayMemberVarNameFromFilesには更新対象の変数の情報が入っているここでチェックは現在意味なし
            // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
            if(@count($lva_OtherUserLastUpdate_vars_list[$intVarID]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90224",
                                                                   array($lva_OtherUserLastUpdate_vars_list[$intVarID])));

}

                //次へ
                continue;
            }

            foreach($vars_list as $strVarName=>$chl_vars_list){

                foreach($chl_vars_list as $strChlVarName=>$dummy){


                    // 代入値管理系に表示しないメンバーはDB登録しない。
                    if($chl_vars_list[$strChlVarName]['MEMBER_DISP'] == 0){
                        continue;
                    }

                    $chl_vars_info = $chl_vars_list[$strChlVarName];
                        
                    $intChlVarNameId = null;
                    $boolLoopNext = false;
                    $strSqlType = null;
                    if(@count($aryRowFromAnsChildVarsTable[$intVarID][$strChlVarName])!==0){
                        //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                        $aryRowOfTableUpdate = $aryRowFromAnsChildVarsTable[$intVarID][$strChlVarName];
                        if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                            // 多次元メンバー変数用の項目が一致しているか判定
                            if(($chl_vars_info['ARRAY_MEMBER_ID']  != $aryRowOfTableUpdate["ARRAY_MEMBER_ID"]   ) ||
                               ($chl_vars_info['ASSIGN_SEQ_NEED']  != $aryRowOfTableUpdate["ASSIGN_SEQ_NEED"]   ) ||
                               ($chl_vars_info['COL_SEQ_NEED']     != $aryRowOfTableUpdate["COL_SEQ_NEED"]      )){

                                //----SQLを発行するので、フラグは立てないまま維持する。
                                $boolLoopNext = false;
                                //SQLを発行するので、フラグは立てないまま維持する。----
                            }
                            else{
                                //----SQLを発行せずループを抜けるフラグ、を立てる
                                $boolLoopNext = true;
                                //SQLを発行せずループを抜けるフラグ、を立てる----
                            }
                        }
                        else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                            //----SQLを発行するので、フラグは立てないまま維持する。
                            $boolLoopNext = false;
                            //SQLを発行するので、フラグは立てないまま維持する。----
                        }
                        else{
                            //----存在しないはずの、値なので、想定外エラーに倒す。
                            // 異常フラグON
                            $error_flag = 1;
                            // 例外処理へ
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001080")) );
                            //存在しないはずの、値なので、想定外エラーに倒す。----
                        }
                        $strSqlType = "UPDATE";
                        $aryRowOfTableUpdate["ARRAY_MEMBER_ID"]  =  $chl_vars_info['ARRAY_MEMBER_ID'];
                        $aryRowOfTableUpdate["ASSIGN_SEQ_NEED"]  =  $chl_vars_info['ASSIGN_SEQ_NEED'];
                        $aryRowOfTableUpdate["COL_SEQ_NEED"]     =  $chl_vars_info['COL_SEQ_NEED'];
                        $intChlVarNameId = $aryRowOfTableUpdate["CHILD_VARS_NAME_ID"];
                        //活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。----
                    }
                    else{
                        //----テーブルにないので、新たに挿入する。
                        $aryRowOfTableUpdate = $arrayValueTmplOfAnsChlVarsTable;
    
                        // テーブルロック
                        $retArray = getSequenceLockInTrz($strSeqOfCurTableAnsChlVars,'A_SEQUENCE');
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001090")) );
                        }
                        // テーブル シーケンスNoを採番
                        $retArray = getSequenceValueFromTable($strSeqOfCurTableAnsChlVars, 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
                        }
                        $intChlVarNameId = $retArray[0];
    
                        $strSqlType = "INSERT";
                        $aryRowOfTableUpdate["CHILD_VARS_NAME_ID"]  = $intChlVarNameId;
                        $aryRowOfTableUpdate["PARENT_VARS_NAME_ID"] = $intVarID;
                        $aryRowOfTableUpdate["CHILD_VARS_NAME"]     = $strChlVarName;
                
                        $aryRowOfTableUpdate["ARRAY_MEMBER_ID"]     = $chl_vars_info['ARRAY_MEMBER_ID'];

                        $aryRowOfTableUpdate["ASSIGN_SEQ_NEED"]     = $chl_vars_info['ASSIGN_SEQ_NEED']; 
                        $aryRowOfTableUpdate["COL_SEQ_NEED"]        = $chl_vars_info['COL_SEQ_NEED'];    
                        //テーブルにないので、新たに挿入する。----
                    }
    
                    // メンバー変数マスタに登録が必要なPkeyリスト
                    $aryChildVarId[$intChlVarNameId] = 0;
    
                    if( $boolLoopNext === true ){
                        //----すでにレコードがあり、活性化済('0')なので、次のループへ
                        continue;
                        //すでにレコードがあり、活性化済('0')なので、次のループへ----
                    }
    
                    $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsChlVars,'A_SEQUENCE');
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
                    }
                    // テーブル シーケンスNoを採番
                    $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsChlVars, 'A_SEQUENCE', FALSE );
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
                    }
                    $intJournalSeqNo = $retArray[0];
    
                    $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $intJournalSeqNo;
                    $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";
                    $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;
    
                    $arrayConfig = $arrayConfigOfAnsChlVarsTable;
                    $arrayValue  = $aryRowOfTableUpdate;
                    $temp_array  = array();

                    setDBUpdateflg();

                    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                         $strSqlType,
                                                         "CHILD_VARS_NAME_ID",
                                                         $strCurTableAnsChlVarsTable,
                                                         $strJnlTableAnsChlVarsTable,
                                                         $arrayConfig,
                                                         $arrayValue,
                                                         $temp_array );
    
                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];
   
                    $sqlJnlBody = $retArray[3];
                    $arrayJnlBind = $retArray[4];
                    
                    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                    $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                        
                    if( $objQueryUtn->getStatus()===false ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
                    }
                    if( $objQueryJnl->getStatus()===false ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
                    }
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
                    }
                    if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                    }
                    $rUtn = $objQueryUtn->sqlExecute();
                    if($rUtn!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                    }
                        
                    $rJnl = $objQueryJnl->sqlExecute();
                    if($rJnl!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                    }
                    // DBアクセス事後処理
                    unset($objQueryUtn);
                    unset($objQueryJnl);
                }
            }
        }
        //実際にあるべき変数名をテーブルに反映させる【活性化】----

        ///////////////////////////////////////////////////////////////////////
        // P0021 
        // メンバー変数マスタの不要なメンバー変数を廃止する。
        ///////////////////////////////////////////////////////////////////////
        // メンバーマスタの情報検索
        foreach($aryRowFromAnsChildVarsTable as $intVarID=>$chl_vars_list){

            // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
            if(@count($lva_OtherUserLastUpdate_vars_list[$intVarID]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90224",
                                                                   array($lva_OtherUserLastUpdate_vars_list[$intVarID])));
}
                //次へ
                continue;
            }
            foreach($chl_vars_list as $strChlVarName=>$aryRowOfTableUpdate){

                // メンバー変数マスタに必要か判定
                if(@count($aryChildVarId[$aryRowOfTableUpdate["CHILD_VARS_NAME_ID"]]) === 0){
                    //----廃止する
                    if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                        // 最終更新者が自分以外の場合は廃止しない
                        if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70041",
                                          array(print_r($aryRowOfTableUpdate,true))));
}
                            continue;
                        }
                        //----廃止する
                        $strSqlType = "UPDATE";
                        //廃止する----
                    }
                    else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                        //----廃止するべきレコードで、すでに廃止されている。
                        continue;
                        //廃止するべきレコードで、すでに廃止されている。----
                    }
                    else{
                        //----想定外エラー
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
                        //想定外エラー----
                    }

                    $intChlVarNameId = $aryRowOfTableUpdate["CHILD_VARS_NAME_ID"];

                    // テーブル　ロック
                    $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsChlVars,'A_SEQUENCE');
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002100")) );
                    }
                    // テーブル シーケンスNoを採番
                    $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsChlVars, 'A_SEQUENCE', FALSE );
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002200")) );
                    }
                    $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                    $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                    $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;
                    
                    $strSqlType = "UPDATE";

                    $arrayConfig = $arrayConfigOfAnsChlVarsTable;
                    $arrayValue  = $aryRowOfTableUpdate;
                    $temp_array  = array();

                    setDBUpdateflg();

                    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                         $strSqlType,
                                                         "CHILD_VARS_NAME_ID",
                                                         $strCurTableAnsChlVarsTable,
                                                         $strJnlTableAnsChlVarsTable,
                                                         $arrayConfig,
                                                         $arrayValue,
                                                         $temp_array );

                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];

                    $sqlJnlBody = $retArray[3];
                    $arrayJnlBind = $retArray[4];
                
                    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                    $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                    
                    if( $objQueryUtn->getStatus()===false ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002300")) );
                    }
                    if( $objQueryJnl->getStatus()===false ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002400")) );
                    }
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002500")) );
                    }
                    if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002600")) );
                    }
                    $rUtn = $objQueryUtn->sqlExecute();
                    if($rUtn!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002700")) );
                    }
                    $rJnl = $objQueryJnl->sqlExecute();
                    if($rJnl!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002800")) );
                    }                
                    //廃止する----
                    // DBアクセス事後処理
                    unset($objQueryUtn);
                    unset($objQueryJnl);
                }
                else
                {
                }
            }
        }
        //存在しているレコードで、もう実際にない変数名を廃止する----

        //////////////////////////////////////////////////////////////////////////////////
        // P0022
        // 多次元変数最大繰返数管理の更新
        // P0023
        // 多次元変数配列組合せ管理の更新
        //////////////////////////////////////////////////////////////////////////////////
        require_once ($root_dir_path . $ansible_nestedVariableExpanders_php);

        // T0027
        $lva_use_role_varsval = array();
        //////////////////////////////////////////////////////////////////////////////////
        // P0024
        // デフォルト変数定義ファイルに定義されている変数の具体値をロール変数具体値管理に反映
        //////////////////////////////////////////////////////////////////////////////////
        // $ifa_role_def_varsval_list
        // array(1) {
        //   [6]=>
        //   array(2) {
        //     ["test_1"]=>
        //     array(2) {
        //       ["VAR_std_test1"]=>
        //       array(1) {
        //         [0]=>
        //         string(14) "VAR_std_test_1"
        //       }
        //       ["VAR_list_test1"]=>
        //       array(1) {
        //         [1]=>
        //         array(3) {
        //           [1]=>
        //           string(16) "VAR_list_test1_1"
        //           [2]=>
        //           string(16) "VAR_list_test1_2"
        //           [3]=>
        //           string(16) "VAR_list_test1_3"
        //         }
        //       }
        //     }
        //     ["test_2"]=>
        //     array(2) {
        //       ["VAR_std_test2"]=>
        // 
        foreach($ifa_role_def_varsval_list as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$role_vars_name_list){
                foreach($role_vars_name_list as $role_vars_name=>$vars_type_list){

                    // 変数一覧管理から多次元変数名のPkey取得
                    $vars_name_id = $aryVarIdPerVarNameFromFiles[$role_vars_name];

                    // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
                    if(@count($lva_OtherUserLastUpdate_vars_list[$vars_name_id]) != 0){
if ( $log_level === 'DEBUG' ){
//$ary[90225] = "変数(｛｝)は変数名一覧の最終更新者が他プロセスなので変数具体値管理の更新スキップ \n";
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90225",
                                                                   array($lva_OtherUserLastUpdate_vars_list[$vars_name_id])));

}
                        //次へ
                        continue;
                    }
                    // ロール名からロールIDを求める
                    if(isset($lva_role_nameTOrole_id[$role_package_id][$role_name]) === false){
                        $warning_flag = 1;
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //ロール名からロールIDが求められない
                        //$ary[70030] = "ロール管理(｛｝)の情報が取得出来ない為、ロール変数(｛｝:｛｝:｛｝)の登録処理はスキップします。";
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70030",array($role_name,$role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                        //次へ
                        continue;
                    }
                    $role_id = $lva_role_nameTOrole_id[$role_package_id][$role_name];

                    // デフォルト変数定義ファイルのみに定義されているか判定
                    if(@count($aryAddVarNameList[$role_package_id][$role_name][$role_vars_name]) != 0){
                        continue;
                    }

                    // 該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
                    $ret = chkVarsAttributeError($role_vars_name,true);
                    if($ret === false){
                        // 変数構造エラーリストの変数は変数一覧に登録しない。
                        continue;
                    }

                    // 変数一覧のPkeyを取得
                    if(@count($aryVarIdPerVarNameFromFiles[$role_vars_name]) == 0){
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        // 変数一覧のPkeyが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90221",array($role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                        //次へ
                        continue;
                    }
                    $vars_name_id = $aryVarIdPerVarNameFromFiles[$role_vars_name];

                    // 変数タイプを取得
                    foreach($vars_type_list as $vars_type=>$varsval_list01){
                        // 変数タイプで分岐
                        switch($vars_type){
                        case '0':     //一般変数
                            // 具体値取得
                            $var_val = $varsval_list01;
                            // 変数の具体値を登録
                            $ret = addRoleVarsValDB($strCurTableRoleVarsVal,         $strJnlTableRoleVarsVal,
                                                    $strSeqOfCurTableRoleVarsVal,    $strSeqOfJnlTableRoleVarsVal,
                                                    $arrayConfigOf_RolVarsVal_Table, $arrayValueTmplOf_RolVarsVal_Table,
                                                    $role_package_id,$role_id,$vars_type,
                                                    '1',               // DBに登録するVAR_TYPE
                                                    $vars_name_id,     // 親変数名
                                                    '',                // メンバー変数名
                                                    '',                // 代入順序
                                                    $var_val,          // 具体値
                                                    $db_access_user_id,$varsval_id);
                            if($ret === false){
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                            }

                            // ロール変数具体値管理に登録が必要なPkeyを退避
                            $lva_use_role_varsval[$varsval_id] = 0;

                            break;
                        case '1':     //複数具体値変数
                            // 具体値の登録があるか
                            if(count($varsval_list01) == 0){
                                // 具体値がないので次へ
                                continue 2;
                            }
                            // 代入順序毎の具体値取得
                            foreach($varsval_list01 as $assign_seq=>$var_val){
                                // 代入順序毎の具体値を登録
                                $ret = addRoleVarsValDB($strCurTableRoleVarsVal,         $strJnlTableRoleVarsVal,
                                                        $strSeqOfCurTableRoleVarsVal,    $strSeqOfJnlTableRoleVarsVal,
                                                        $arrayConfigOf_RolVarsVal_Table, $arrayValueTmplOf_RolVarsVal_Table,
                                                        $role_package_id,$role_id,$vars_type,
                                                        '2',               // DBに登録するVAR_TYPE
                                                        $vars_name_id,     // 親変数名
                                                        '',                // メンバー変数名
                                                        $assign_seq,       // 代入順序
                                                        $var_val,          // 具体値
                                                        $db_access_user_id,$varsval_id);
                                if($ret === false){
                                    $error_flag = 1;
                                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                                }

                                // ロール変数具体値管理に登録が必要なPkeyを退避
                                $lva_use_role_varsval[$varsval_id] = 0;

                            }
                            break;
                        }
                    }
                }
            }
        }

        ////////////////////////////////////////////////////////////////////
        // P0025
        // 多次元変数配列組合せ管理からデータを取得する。
        ////////////////////////////////////////////////////////////////////
        // T0027
        $lva_MemberColComb_list = array();
        $ret = getMemberColCombDB($lva_MemberColComb_list);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001070")) );
        }

        //////////////////////////////////////////////////////////////////////////////////
        // P0026
        // デフォルト変数定義ファイルに定義されている多次元変数の具体値をロール変数具体値管理に反映
        //////////////////////////////////////////////////////////////////////////////////
        // $ifa_role_array_vars_list
        // array(1) {
        //   [6]=>
        //   array(2) {
        //     ["test_1"]=>
        //     array(2) {
        //       ["VAR_str_test1"]=>
        //       array(4) {
        //         ["DIFF_ARRAY"]=>
        //         ["VAR_VALUE"]=>
        //         array(2) {
        //           ["0.VAR_str_test1_1"]=>
        //           array(1) {
        //             Key-Value型
        //             [0]=>
        //             array(2) {
        //                 ↓非配列の場合は空文字になる
        //               ["000"]=>
        //               string(17) "VAR_str_test1_1_1"
        //               ["001"]=>
        //               string(17) "VAR_str_test1_1_2"
        //             }
        //           }
        //           ["array1.array2.0.array2_2.0.array2_2_3"]=>
        //           array(1) {
        //             複数具体値型
        //             [1]=>
        //             array(2) {
        //                 ↓非配列の場合は空文字になる
        //               ["000000"]=>
        //               array(2) {
        //                 [0]=>
        //                 string(16) "2_array2_2_3_1_1"
        //                 [1]=>
        //                 string(16) "2_array2_2_3_1_2"
        //               }
        //             }
        //           }
        foreach($ifa_role_array_vars_list as $role_package_id=>$role_name_list){
            foreach($role_name_list as $role_name=>$role_vars_name_list){
                foreach($role_vars_name_list as $role_vars_name=>$vars_info_list){

                    // 変数一覧管理から多次元変数名のPkey取得
                    $vars_name_id = $aryVarIdPerVarNameFromFiles[$role_vars_name];

                    // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
                    if(@count($lva_OtherUserLastUpdate_vars_list[$vars_name_id]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90225",
                                                                   array($lva_OtherUserLastUpdate_vars_list[$vars_name_id])));
}
                        //次へ
                        continue;
                    }
                    // ロール名からロールIDを求める
                    if(isset($lva_role_nameTOrole_id[$role_package_id][$role_name]) === false){
                        $warning_flag = 1;
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        //ロール名からロールIDが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70030",array($role_name,$role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                        //次へ
                        continue;
                    }
                    $role_id = $lva_role_nameTOrole_id[$role_package_id][$role_name];

                    // デフォルト変数定義ファイルのみに定義されているか判定
                    if(@count($aryAddVarNameList[$role_package_id][$role_name][$role_vars_name]) != 0){
                        continue;
                    }

                    // 該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
                    $ret = chkVarsAttributeError($role_vars_name,true);
                    if($ret === false){
                        // 変数構造エラーリストの変数は変数一覧に登録しない。
                        continue;
                    }
                    // 変数一覧のPkeyを取得
                    if(@count($aryVarIdPerVarNameFromFiles[$role_vars_name]) == 0){
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                        // 変数一覧のPkeyが求められない
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90221",array($role_package_id,$role_name,$role_vars_name));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}

                        //次へ
                        continue;
                    }
                    $vars_name_id = $aryVarIdPerVarNameFromFiles[$role_vars_name];

                    // メンバー変数の具体値が登録されているか判定
                    if(@count($vars_info_list["VAR_VALUE"]) == 0){


                        //次へ
                        continue;
                    }


                    // メンバーを取得
                    foreach($vars_info_list["VAR_VALUE"] as $member_vars_name=>$varsval_list00){
                        // 変数タイプを取得
                        foreach($varsval_list00 as $vars_type=>$varsval_list01)
                        // 変数タイプで分岐
                        switch($vars_type){
                        case '0':     //一般変数
                            foreach($varsval_list01 as $col_seq_str=>$var_val){

                                // 多次元変数配列組合せ管理のPkeyを取得する。
                                if(strlen($col_seq_str) == 0){
                                    $col_seq_str = "-";
                                }
                                if(@count($lva_MemberColComb_list[$vars_name_id][$member_vars_name][$col_seq_str]) == 0){
                                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90222",array($role_package_id,$role_name,$role_vars_name,$member_vars_name,$col_seq_str));
                                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                    continue 2;
                                }
                                $col_seq_combination_id = $lva_MemberColComb_list[$vars_name_id][$member_vars_name][$col_seq_str];
                                // 変数の具体値を登録
                                $ret = addRoleVarsValDB($strCurTableRoleVarsVal,         $strJnlTableRoleVarsVal,
                                                        $strSeqOfCurTableRoleVarsVal,    $strSeqOfJnlTableRoleVarsVal,
                                                        $arrayConfigOf_RolVarsVal_Table, $arrayValueTmplOf_RolVarsVal_Table,
                                                        $role_package_id,$role_id,$vars_type,
                                                        '1',               // DBに登録するVAR_TYPE
                                                        $vars_name_id,     // 親変数名
                                                        $col_seq_combination_id,  // メンバー変数名
                                                        '',                // 代入順序
                                                        $var_val,          // 具体値
                                                        $db_access_user_id,$varsval_id);
                                if($ret === false){
                                    $error_flag = 1;
                                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                                }

                                // ロール変数具体値管理に登録が必要なPkeyを退避
                                $lva_use_role_varsval[$varsval_id] = 0;
                            }
                            break;
                        case '1':     //複数具体値変数
                            // 具体値の登録があるか
                            if(count($varsval_list01) == 0){
                                // 具体値がないので次へ
                                continue 2;
                            }
                            foreach($varsval_list01 as $col_seq_str=>$varsval_list02){
                                foreach($varsval_list02 as $assign_seq=>$var_val){

                                    // 多次元変数配列組合せ管理のPkeyを取得する。
                                    if(strlen($col_seq_str) == 0){
                                        $col_seq_str = "-";
                                    }
                                    if(@count($lva_MemberColComb_list[$vars_name_id][$member_vars_name][$col_seq_str]) == 0){
                                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90222",array($role_package_id,$role_name,$role_vars_name,$member_vars_name,$col_seq_str));
                                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                        continue 2;
                                    }
                                    $col_seq_combination_id = $lva_MemberColComb_list[$vars_name_id][$member_vars_name][$col_seq_str];

                                    // 代入順序毎の具体値を登録
                                    $ret = addRoleVarsValDB($strCurTableRoleVarsVal,         $strJnlTableRoleVarsVal,
                                                            $strSeqOfCurTableRoleVarsVal,    $strSeqOfJnlTableRoleVarsVal,
                                                            $arrayConfigOf_RolVarsVal_Table, $arrayValueTmplOf_RolVarsVal_Table,
                                                            $role_package_id,$role_id,$vars_type,
                                                            '2',               // DBに登録するVAR_TYPE
                                                            $vars_name_id,     // 親変数名
                                                            $col_seq_combination_id,  // メンバー変数名
                                                            $assign_seq,       // 代入順序
                                                            $var_val,          // 具体値
                                                            $db_access_user_id,$varsval_id);
                                    if($ret === false){
                                        $error_flag = 1;
                                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001030")) );
                                    }

                                    // ロール変数具体値管理に登録が必要なPkeyを退避
                                    $lva_use_role_varsval[$varsval_id] = 0;

                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        /////////////////////////////////////////////////////////////////////////////////
        // P0027
        // ロール変数具体値管理に反映で使用していない変数の情報を廃止する。
        /////////////////////////////////////////////////////////////////////////////////
        $ret = delRoleVarsValDB($strCurTableRoleVarsVal,         $strJnlTableRoleVarsVal,
                                $strSeqOfCurTableRoleVarsVal,    $strSeqOfJnlTableRoleVarsVal,
                                $arrayConfigOf_RolVarsVal_Table, $arrayValueTmplOf_RolVarsVal_Table,
                                $lva_use_role_varsval,
                                $db_access_user_id,              $lva_OtherUserLastUpdate_vars_list);
        if($ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001050")) );
        }

        /////////////////////////////////////////////////////////////////////////////////
        // P0028
        // 作業パターン変数紐付管理からデータを取得
        /////////////////////////////////////////////////////////////////////////////////
        // T0011
        $aryRowsPerPatternFromAnsPatternVarsLink = array();

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        $ret = getPatternVarsLinkDB($strCurTableAnsPatternVarsLink,
                                    $strJnlTableAnsPatternVarsLink,
                                    $arrayConfigOfAnsPatternVarsLink,
                                    $arrayValueTmplOfAnsPatternVarsLink,
                                    $temp_array,
                                    $aryRowsPerPatternFromAnsPatternVarsLink);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002900")) );


        }

        // T0012
        $aryVarNameIdsPerPattern = array();

        ///////////////////////////////////////////////////////////////////////
        // P0029
        // 作業パターン詳細を元に作業パターン変数紐付マスタに紐付を登録
        ///////////////////////////////////////////////////////////////////////
        foreach($lta_pattern_list as $patten_id=>$pattern_list){

            // 作業パターンID毎の変数一覧作成
            $aryVarNameIdsPerPattern[$patten_id] = array();

            foreach($pattern_list as $role_package_id=>$role_list){
                foreach($role_list as $role_id=>$dummy){

                    // ワーニングがでるので@を付ける
                    if(@count($lva_use_role_vars_name_list[$role_package_id][$role_id])===0){
                        continue;
                    }
                    // ロール変数管理 登録リストから該当ロール変数の情報を取得
                    $aryVarsOfFocusMatterId = $lva_use_role_vars_name_list[$role_package_id][$role_id];

                    //----変数名ごとにループする
                    foreach($aryVarsOfFocusMatterId as $role_vars_name_id => $role_vars_name){
                        $intVarsLinkId = null;
                        $boolLoopNext = false;
                        $strSqlType = null;
                    
                        // 変数名から変数マスタPkeyを取得
                        $vars_name_id = $aryVarIdPerVarNameFromFiles[$role_vars_name];

                        //作業パターンID毎の変数一覧にパターンIDがあるか判定
                        if( array_key_exists($patten_id,$aryVarNameIdsPerPattern) === true ){
                            //作業パターン+変数があるか判定
                            if( array_key_exists($vars_name_id,$aryVarNameIdsPerPattern[$patten_id]) === true ){
                                //登録スキップ
                                continue;
                            }
                        }
                        // 作業パターンID毎の変数一覧作成 
                        $aryVarNameIdsPerPattern[$patten_id][$vars_name_id] = 1;

                        //----更新対象のテーブルのレコードに、存在するかを調べる
                        // 作業パターン変数紐付マスタにパターンID+変数IDが登録されているか判定
                        if( isset($aryRowsPerPatternFromAnsPatternVarsLink[$patten_id][$vars_name_id]) === true ){
                            //----更新対象のテーブルに存在した
                            // 作業パターン変数紐付マスタにパターンID+変数IDが登録されている
                            $aryRowOfTableUpdate = $aryRowsPerPatternFromAnsPatternVarsLink[$patten_id][$vars_name_id];
                        
                            if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                                continue;
                            }
                            else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                                //----SQLを発行するので、フラグは立てないまま維持する。
                                //$boolLoopNext = false;
                                //SQLを発行するので、フラグは立てないまま維持する。----
                            }
                            else{
                                //----存在しないはずの、値なので、想定外エラーに倒す。
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003000")) );
                                //存在しないはずの、値なので、想定外エラーに倒す。----
                            }
                            $strSqlType = "UPDATE";
                            //以降の処理で設定
                            $aryRowOfTableUpdate['DISUSE_FLAG']      = "0";
                            //更新対象のテーブルに存在した----

                        }
                        else{
                            //----存在しなかったので、新規に挿入
                            $aryRowOfTableUpdate = $arrayValueTmplOfAnsPatternVarsLink;
    
                            // 新しいレコードなので、CURシーケンスを発行する
                            $retArray = getSequenceLockInTrz($strSeqOfCurTableAnsPatternVarsLink,'A_SEQUENCE');
                            if( $retArray[1] != 0 ){
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003100")) );
                            }
                            // テーブル シーケンスNoを採番
                            $retArray = getSequenceValueFromTable($strSeqOfCurTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                            if( $retArray[1] != 0 ){
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003200")) );
                            }
                            $strSqlType = "INSERT";
                            $aryRowOfTableUpdate["VARS_LINK_ID"]     = $retArray[0];
                            $aryRowOfTableUpdate["PATTERN_ID"]       = $patten_id;
                            $aryRowOfTableUpdate["VARS_NAME_ID"]     = $vars_name_id;
                            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";

                            //存在しなかったので、新規に挿入----
                        }

                        if( $boolLoopNext === true ){

                            continue;
                        }
                        // ジャーナルテーブル　ロック
                        $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsPatternVarsLink,'A_SEQUENCE');
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
                        }
                        // ジャーナルテーブル シーケンスNoを採番
                        $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
                        }
                        $intJournalSeqNo = $retArray[0];

                        $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                        $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                        $arrayConfig = $arrayConfigOfAnsPatternVarsLink;
                        $arrayValue  = $aryRowOfTableUpdate;
                        $temp_array  = array();

                        // DEBUGログに変更
                        if ( $log_level === 'DEBUG' ){
// 更新ログ
ob_start();
var_dump($arrayValue);
$msgstr = ob_get_contents();
ob_clean();
LocalLogPrint(basename(__FILE__),__LINE__,"作業パターン変数紐付マスタ  更新($strSqlType)\n$msgstr");
                        }
                        setDBUpdateflg();

                        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                             $strSqlType,
                                                             "VARS_LINK_ID",
                                                             $strCurTableAnsPatternVarsLink,
                                                             $strJnlTableAnsPatternVarsLink,
                                                             $arrayConfig,
                                                             $arrayValue,
                                                             $temp_array );

                        $sqlUtnBody = $retArray[1];
                        $arrayUtnBind = $retArray[2];

                        $sqlJnlBody = $retArray[3];
                        $arrayJnlBind = $retArray[4];
                    
                        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                    
                        if( $objQueryUtn->getStatus()===false ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003500")) );
                        }
                        if( $objQueryJnl->getStatus()===false ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003600")) );
                        }
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003700")) );
                        }
                        if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003800")) );
                        }
                        $rUtn = $objQueryUtn->sqlExecute();
                        if($rUtn!=true){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003900")) );
                        }
                        $rJnl = $objQueryJnl->sqlExecute();
                        if($rJnl!=true){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004000")) );
                        }
                        //更新対象のテーブルのレコードに、存在するかを調べる----
                        // DBアクセス事後処理
                        unset($objQueryUtn);
                        unset($objQueryJnl);
                    }//ロールごとにループする----
                }
                //ロールパッケージごとにループする----
            }
            //作業パターンIDごとにループする----
        }
        //作業パターンごとにループする----
        //実際にあるべき組み合わせをテーブルに反映させる【活性化】----

        ///////////////////////////////////////////////////////////////////////
        // P0030
        // 作業パターン変数紐付マスタあって作業パターン詳細にない紐付情報を廃止する。
        ///////////////////////////////////////////////////////////////////////
        //----存在しているレコードで、もう実際にない組み合わせを廃止する
        // 作業パターン変数紐付マスタの内容でループ
        foreach($aryRowsPerPatternFromAnsPatternVarsLink as $intPatternId=>$aryRowsPerVarNameId){
            //----変数名IDごとにループする
            foreach($aryRowsPerVarNameId as $intVarNameId=>$row){
                // 作業パターン変数紐付マスタの情報取得
                $aryRowOfTableUpdate = $aryRowsPerPatternFromAnsPatternVarsLink[$intPatternId][$intVarNameId];

                $boolDisuseOnFlag = false;

                // 作業パターン詳細にパターンIDが登録されているか判定
                if( array_key_exists($intPatternId, $lta_pattern_list) === false ){
                    //----ファイルを解析した組み合わせの中に、調べている作業パターンがないので、廃止する
                    $boolDisuseOnFlag = true;
                    //ファイルを解析した組み合わせの中に、調べている作業パターンがないので、廃止する----
                }
                else{
                    //作業パターン詳細マスタをパターンIDがあるか判定
                    if( array_key_exists($intPatternId,$aryVarNameIdsPerPattern) === false ){
                        //----ファイルを解析した組み合わせの中に、調べている変数名IDがないので、廃止する
                        $boolDisuseOnFlag = true;
                        //ファイルを解析した組み合わせの中に、調べている変数名IDがないので、廃止する----
                    }
                    else{
                        if( array_key_exists($intVarNameId,$aryVarNameIdsPerPattern[$intPatternId]) === false ){
                            //----ファイルを解析した組み合わせの中に、調べている変数名IDがないので、廃止する
                            $boolDisuseOnFlag = true;
                            //ファイルを解析した組み合わせの中に、調べている変数名IDがないので、廃止する----
                        }
                    }
                }
                if( $boolDisuseOnFlag === false ){
                    //----登録されて活性されているべきレコードなので、なにもしない
                    continue;
                    //登録されて活性されているべきレコードなので、なにもしない----
                }
                // 作業パターン変数紐付が有効レコードか判定
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    // 最終更新者が自分以外の場合は廃止しない
                    if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70042",
                                          array(print_r($aryRowOfTableUpdate,true))));
}
                        continue;

                    }
                    //----廃止する
                    $strSqlType = "UPDATE";
                    //廃止する----
                }
                // 作業パターン変数紐付が廃止レコードか判定
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){


                    //----廃止するべきレコードで、すでに廃止されている。
                    continue;
                    //廃止するべきレコードで、すでに廃止されている。----
                }
                else{
                    //----想定外エラー
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004100")) );
                    //想定外エラー----
                }
                // ジャーナル ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsPatternVarsLink,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004200")) );
                }
                // ジャーナルテーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004300")) );
                }
                $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                $arrayConfig = $arrayConfigOfAnsPatternVarsLink;
                $arrayValue  = $aryRowOfTableUpdate;
                $temp_array  = array();

                // DEBUGログに変更
                if ( $log_level === 'DEBUG' ){
// 更新ログ
ob_start();
var_dump($arrayValue);
$msgstr = ob_get_contents();
ob_clean();
LocalLogPrint(basename(__FILE__),__LINE__,"作業パターン変数紐付マスタ  廃止($strSqlType)\n$msgstr");
                }
                setDBUpdateflg();

                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     $strSqlType,
                                                     "VARS_LINK_ID",
                                                     $strCurTableAnsPatternVarsLink,
                                                     $strJnlTableAnsPatternVarsLink,
                                                     $arrayConfig,
                                                     $arrayValue,
                                                     $temp_array );

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                
                if( $objQueryUtn->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004400")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004500")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004600")) );
                }

                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004700")) );
                }
                
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004800")) );
                }
                
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004900")) );
                }                
            }
        }

        ////////////////////////////////////////////////////////////////
        // P0031
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005000")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55015");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55005");
            require ($root_dir_path . $log_output_php );
        }
        
        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースの更新反映を登録
        ///////////////////////////////////////////////////////////////////////////
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70054");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        // 関連データベースの更新の反映完了を登録
        $ret = setBackyardExecuteComplete($lv_UpdateRecodeInfo);
        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90304");
            throw new Exception($errorMsg);
        }
        if($db_update_flg === true) {
            // DBを更新した場合、代入値自動登録設定のバックヤード起動を登録
            $ret = setBackyardExecute(2100020006);
            if($ret === false) {
                $error_flag = 1;
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90305");
                throw new Exception($errorMsg);
            }
        }

    }
    catch (Exception $e){

        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55272");
        require ($root_dir_path . $log_output_php );

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );
        
        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55016");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50045");
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50047");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50049");
            }
            require ($root_dir_path . $log_output_php );
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55267");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55268");
            require ($root_dir_path . $log_output_php );
        }        
        exit(0);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55002");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   ロールパッケージ管理からデータ取得
    //   
    // パラメータ
    //   $ina_role_package_list:  
    //            ロールパッケージ管理 データリスト
    //            [ROLE_PACKAGE_ID][ROLE_PACKAGE_NAME] = ROLE_PACKAGE_FILE
    //   $ina_role_package_id_list:
    //            ロールパッケージ管理 ロールパッケージIDリスト
    //            [ROLE_PACKAGE_ID]=1;
    //   $warning_flag:
    //            警告フラグ
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getRolePackageDB(&$ina_role_package_list,&$ina_role_package_id_list,&$warning_flag){
        global          $objMTS;
        global          $objDBCA;
        global          $log_level;

        $ina_role_package_list = array();
        $ina_role_package_id_list = array();
        ////////////////////////////////////////////////////////////////
        // ロールパッケージ管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////
        $sqlUtnBody = "SELECT " 
                     ."ROLE_PACKAGE_ID, "
                     ."ROLE_PACKAGE_NAME ,"
                     ."ROLE_PACKAGE_FILE "
                     ."FROM  B_ANSIBLE_LRL_ROLE_PACKAGE "
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00006000")));
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00006010")));
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00006020")));
            return false;
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            if(strlen($row['ROLE_PACKAGE_FILE']) == 0){
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                 $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70028",array($row["ROLE_PACKAGE_ID"]));
                 LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
                 $warning_flag = 1;

            }
            else{
                 // T0001
                 //ina_role_package_list:[ROLE_PACKAGE_ID][ROLE_PACKAGE_NAME] = ROLE_PACKAGE_FILE
                 $ina_role_package_list[$row["ROLE_PACKAGE_ID"]][$row["ROLE_PACKAGE_NAME"]] = $row["ROLE_PACKAGE_FILE"];
                 // T0002
                 //ina_role_package_id_list[$row["ROLE_PACKAGE_ID"]]=1;
                 $ina_role_package_id_list[$row["ROLE_PACKAGE_ID"]]=1;
            }
        }
        // DBアクセス事後処理
        unset($objQueryUtn);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   ロールパッケージファイルからロール情報取得
    //
    // パラメータ
    //   $in_role_package_id:     ロールパッケージ管理 pkey
    //   $in_role_package_file:   ロールパッケージファイル
    //   $ina_role_name_list:     rolesディレクトリ配下のロール名リスト返却
    //                            [role名]
    //   $ina_role_var_list:      ロール内のPlaybookで使用している変数名リスト返却
    //                            [role名][変数名]=0
    //   $in_role_package_name:   ロールパッケージ名
    //   $ina_role_def_var_list:  default変数リスト返却
    //                             変数名のリスト
    //                               一般変数
    //                                 $ina_role_def_var_list[ロール名][変数名]=0
    //                             #1186 2017/05/18 Append start
    //                               リスト変数
    //                                 $ina_role_def_var_list[ロール名][変数名]=4
    //                             #1186 2017/05/18 Append end
    //                               配列変数
    //                                 $ina_role_def_var_list[ロール名][配列数名]=array([子供変数名]=0,...)
    //   $ina_role_def_varsval_list:       
    //                            各ロールのデフォルト変数ファイル内に定義されている変数名の具体値リスト
    //                              一般変数
    //                                [変数名][0]=具体値
    //                              複数具体値変数
    //                                [変数名][1]=array(1=>具体値,2=>具体値....)
    //                              配列変数
    //                                [変数名][2][メンバー変数]=array(1=>具体値,2=>具体値....)
    //   $ina_role_def_array_vars_list:
    //            ['CHAIN_ARRAY'][親変数のKey][自身のKey]['VAR_NAME']    = 変数名   0:リスト配列開始の意味
    //                                                   ['NEST_LEVEL']  = 階層     1～
    //                                                   ['LIST_STYLE']  = 5:複数具体値変数  0:初期値
    //                                                   ['VAR_NAME_PATH']  = 変数名(階層:xx.xx.xx.xx.xxxx)
    //                                                   ['VAR_NAME_ALIAS'] = 代入値管理に表示する変数名
    //            ['VAR_VALUE']     未使用
    //            ['DIFF_ARRAY']    変数構造配列
    //   $ina_ITA2User_var_list  読替表の変数リスト　ITA変数=>ユーザ変数
    //   $ina_User2ITA_var_list  読替表の変数リスト　ユーザ変数=>ITA変数
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getRolePackageInfo($in_role_package_id,
                                $in_role_package_file,
                                $in_roledir,
                                &$ina_role_name_list,
                                &$ina_role_var_list,
                                $in_role_package_name,        // #1081 2016/10/31 Append
                                &$ina_role_def_var_list,      // #1081 2016/10/31 Append
                                &$ina_role_def_varsval_list,  // #1182 2017/03/23 Append
                                &$ina_role_def_array_vars_list,     // #1186 2017/05/18 Append
                                &$ina_ITA2User_var_list,      // #1241 2017/09/20 Append
                                &$ina_User2ITA_var_list)      // #1241 2017/09/20 Append
    {
        global          $objMTS;
        global          $objDBCA;
        global          $objRole;
        global          $root_dir_path;
        global          $log_level;
 
        $ina_role_name_list = array();
        $ina_role_var_list  = array();
        
        // ロールパッケージファイル名(ZIP)を取得
        $zipfile = getAnsible_RolePackage_file($root_dir_path . '/' . DF_ROLE_PACKAGE_FILE_CONTENTS_DIR,
                                               $in_role_package_id,
                                               $in_role_package_file);

        // ロールパッケージファイル名(ZIP)の存在確認
        if( file_exists($zipfile) === false ){
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-70008",array($in_role_package_id,basename($zipfile)));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
}
            // false return
            return false;
        }

        // ロールパッケージファイル(ZIP)の解凍
        if($objRole->ZipextractTo($zipfile,$in_roledir) === false){
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
            $arryErrMsg = $objRole->getlasterror();
            LocalLogPrint(basename(__FILE__),__LINE__,$strErrMsg);
}

            // false return
            return false;
        }
        // ローカル変数のリスト作成
        $system_vars = array();

        $err_vars_list = array();
        $def_vars_list = array();

        $def_varsval_list = array();
        
        $def_array_vars_list = array();
        
        $dummy_array = array();

        $ITA2User_var_list  = array();
        $User2ITA_var_list  = array();
        $comb_err_vars_list = array();

        // chkRolesDirectoryでcopyモジュールで使用している変数を取得する処理を追加
        // しているが、ここでは不要なので取得処理をしないパラメータを設定する
        if($objRole->chkRolesDirectory($in_roledir,$system_vars,
                                       $in_role_package_name,
                                       $def_vars_list,
                                       $err_vars_list,
                                       $def_varsval_list,
                                       $def_array_vars_list,
                                       false,
                                       $dummy_array,
                                       $ITA2User_var_list,
                                       $User2ITA_var_list,
                                       $comb_err_vars_list,
                                       true) === false){
            // ロール内の読替表で読替変数と任意変数の組合せが一致していない
            if(@count($comb_err_vars_list) !== 0){
if ( $log_level === 'DEBUG' ){
                $msgObj  = new DefaultVarsFileAnalysis($objMTS);
                $errmag  = $msgObj->TranslationTableCombinationErrmsgEdit(true,$comb_err_vars_list);
                unset($msgObj);
                LocalLogPrint(basename(__FILE__),__LINE__,$errmag);
}
            }
            // defaults定義ファイルに変数定義が複数あり形式が違う変数がない場合
            // $err_vars_list[変数名][ロールパッケージ名][ロール名]
            else if(@count($err_vars_list) !== 0){
                // defaults定義ファイルに変数定義が複数あり形式が違う変数がある場合
if ( $log_level === 'DEBUG' ){
                $msgObj = new DefaultVarsFileAnalysis($objMTS);
                $errmag = $msgObj->VarsStructErrmsgEdit($err_vars_list);
                unset($msgObj);
                LocalLogPrint(basename(__FILE__),__LINE__,$errmag);
}

            }
            else{
// 情報不足で処理スキップのメッセージはデバックモード時のみ出力
if ( $log_level === 'DEBUG' ){
                $arryErrMsg = $objRole->getlasterror();
                LocalLogPrint(basename(__FILE__),__LINE__,$arryErrMsg[0]);
}
            }

            return false;

        }
        // rolesディレクトリ内のロール名取得
        // $ina_role_name_list[role名]
        $ina_role_name_list = $objRole->getrolename();
        // ロール内のplaybookに定義されている変数取得
        // $ina_role_var_list[role名][変数名]=0
        $ina_role_var_list  = $objRole->getvarname();

        // ロール内の変数取得
        $ina_role_def_var_list = $def_vars_list;

        // ロール内の変数具体値取得
        $ina_role_def_varsval_list = $def_varsval_list;
        
        $ina_role_def_array_vars_list = $def_array_vars_list;

        $ina_ITA2User_var_list = $ITA2User_var_list;
        $ina_User2ITA_var_list = $User2ITA_var_list;

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   ロールパッケージファイル名(ZIP)を取得
    //
    // パラメータ
    //   $in_dir:            ロールパッケージファイルディレクトリ名
    //   $in_pkey:           ロールパッケージファイル名(ZIP) Pkey
    //   $in_filename:       ロールパッケージファイル名(ZIP)
    //
    // 戻り値
    //   子PlayBookファイル名(Legacy)
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_RolePackage_file($in_dir,$in_pkey,$in_filename){
        $intNumPadding = 10;

        // sible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
        $file = $in_dir . '/' .
                str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ) . '/' .
                $in_filename;
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    // 処理内容
    //   ロール管理のデータを更新する。
    //   
    // パラメータ
    //   $in_strCurTableRole:                ロール管理テーブル名  
    //   $in_strJnlTableRole:                ロール管理ジャーナルテーブル名
    //   $in_strSeqOfCurTableRole:           ロール管理テーブルシーケンス名
    //   $in_strSeqOfJnlTableRole:           ロール管理ジャーナルシーケンス名
    //   $in_arrayConfigOf_Rol_Table:        ロール管理項目リスト 
    //   $in_arrayValueTmplOf_Rol_Table:     ロール管理更新用項目リスト
    //   $in_role_package_id:                ロール管理に登録するロールパッケージID
    //   $in_role_name:                      ロール管理に登録するロール名
    //   $in_access_user_ids:                データベース更新ユーザーID 
    //   $in_role_id:                        ロール管理に登録したレコードのPkey
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addRoleDB( $in_strCurTableRole,           $in_strJnlTableRole,
                        $in_strSeqOfCurTableRole,      $in_strSeqOfJnlTableRole,
                        $in_arrayConfigOf_Rol_Table,   $in_arrayValueTmplOf_Rol_Table,
                        $in_role_package_id,$in_role_name,$in_access_user_id,&$in_role_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $strCurTable            = $in_strCurTableRole;
        $strJnlTable            = $in_strJnlTableRole;
        $strSeqOfCurTable       = $in_strSeqOfCurTableRole;
        $strSeqOfJnlTable       = $in_strSeqOfJnlTableRole;

        $arrayConfig = $in_arrayConfigOf_Rol_Table;

        $arrayValue  = $in_arrayValueTmplOf_Rol_Table;

        $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID = :ROLE_PACKAGE_ID AND " .
                                     "ROLE_NAME       = :ROLE_NAME ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                             "SELECT FOR UPDATE", 
                                             "ROLE_ID", 
                                             $strCurTable, 
                                             $strJnlTable, 
                                             $arrayConfig, 
                                             $arrayValue, 
                                             $temp_array );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007000")));
            return false;
        }

        $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                     'ROLE_NAME'=>$in_role_name));
        
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007010")));
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
             $action  = "INSERT";
             $tgt_row = $arrayValue;

        }
        else{
            // ロールID退避
            $in_role_id     = $row['ROLE_ID'];
            if($row['DISUSE_FLAG'] == '1'){
                 // 廃止なので復活する。
                 $action = "UPDATE";
                 $tgt_row = $row;
            }
            else{
                 //登録済みなので処理終了
                 return true;
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007020")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007030")));
                return false;
            }
            
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;
            
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007040")) );
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007050")) );
            }

            // ロールID退避
            $in_role_id                      = $retArray[0];

            // ロール管理に登録する情報設定
            $tgt_row["ROLE_ID"]          = $retArray[0];
            $tgt_row["ROLE_PACKAGE_ID"]  = $in_role_package_id;
            $tgt_row["ROLE_NAME"]        = $in_role_name;
            $tgt_row["DISUSE_FLAG"]      = '0';

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007060")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007070")));
                return false;
            }
            
            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        }
        setDBUpdateflg();

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "ROLE_ID",
                                             $strCurTable, 
                                             $strJnlTable, 
                                             $arrayConfig, 
                                             $tgt_row,
                                             $temp_array );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
        
        if( $objQueryUtn->getStatus()===false || 
            $objQueryJnl->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007080")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007090")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007100")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007110")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   ロール変数管理のデータを更新する。(多次元変数以外)
    //   
    // パラメータ
    //   $in_strCurTableRoleVars:            ロール変数管理テーブル名  
    //   $in_strJnlTableRoleVars:            ロール変数管理ジャーナルテーブル名
    //   $in_strSeqOfCurTableRoleVars:       ロール変数管理テーブルシーケンス名
    //   $in_strSeqOfJnlTableRoleVars:       ロール変数管理ジャーナルシーケンス名
    //   $in_arrayConfigOf_RolVars_Table:    ロール変数管理項目リスト 
    //   $in_arrayValueTmplOf_RolVars_Table: ロール変数管理更新用項目リスト
    //   $in_role_package_id:                ロール変数管理に登録するロールパッケージID
    //   $in_role_id:                        ロール変数管理に登録するロールID
    //   $in_vars_name:                      ロール変数管理に変数名
    //   $in_chl_vars_name:                  ロール変数管理に配列変数の変数名
    //   $in_access_user_ids:                データベース更新ユーザーID 
    //   $in_vars_attr:                      ロール変数管理に登録する変数属性
    //   $in_vars_grp_name:                  ロール変数管理に登録するグループ名
    //   $in_vars_name_id:                   ロール変数管理に登録したレコードのPkey
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addRoleVarsDB($in_strCurTableRoleVars,            $in_strJnlTableRoleVars,
                           $in_strSeqOfCurTableRoleVars,       $in_strSeqOfJnlTableRoleVars,
                           $in_arrayConfigOf_RolVars_Table,    $in_arrayValueTmplOf_RolVars_Table,
                           $in_role_package_id,$in_role_id,$in_vars_name,
                           $in_vars_attr,
                          &$in_access_user_id,&$in_vars_name_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $strCurTable            = $in_strCurTableRoleVars;
        $strJnlTable            = $in_strJnlTableRoleVars;
        $strSeqOfCurTable       = $in_strSeqOfCurTableRoleVars;
        $strSeqOfJnlTable       = $in_strSeqOfJnlTableRoleVars;

        $arrayConfig = $in_arrayConfigOf_RolVars_Table;
        $arrayValue  = $in_arrayValueTmplOf_RolVars_Table;

        $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID = :ROLE_PACKAGE_ID AND " .
                                     "ROLE_ID         = :ROLE_ID         AND " .
                                     "VARS_NAME       = :VARS_NAME           ");

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "VARS_NAME_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007120")));
            return false;
        }

        $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                     'ROLE_ID'=>$in_role_id,
                                     'VARS_NAME'=>$in_vars_name));

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007130")));
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
             $action  = "INSERT";
             $tgt_row = $arrayValue;

        }
        else{
            // ロール変数ID退避
            $in_vars_name_id     = $row['VARS_NAME_ID'];
            if($row['DISUSE_FLAG'] == '1'){
                 $action = "UPDATE";
                 $tgt_row = $row;

            }
            else{
                // GROUP_VARS_NAMEのNULL判定をここでやる
                if($row["VARS_ATTRIBUTE_01"] != $in_vars_attr){
                     $action = "UPDATE";
                     $tgt_row = $row;
                }
                else{
                 //登録済みなので処理終了
                 return true;
                }
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007140")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007150")));
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;
            $tgt_row["VARS_ATTRIBUTE_01"] = $in_vars_attr;
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007160")) );
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007170")) );
            }

            // ロール変数ID退避
            $in_vars_name_id             = $retArray[0];

            // ロール変数管理に登録する情報設定
            $tgt_row["VARS_NAME_ID"]     = $retArray[0];
            $tgt_row["ROLE_PACKAGE_ID"]  = $in_role_package_id;
            $tgt_row["ROLE_ID"]          = $in_role_id;
            $tgt_row["VARS_NAME"]        = $in_vars_name;
            $tgt_row["VARS_ATTRIBUTE_01"] = $in_vars_attr;
            $tgt_row["DISUSE_FLAG"]      = '0';

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007180")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007190")));
                return false;
            }

            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        }
        setDBUpdateflg();

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "VARS_NAME_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $tgt_row,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if( $objQueryUtn->getStatus()===false ||
            $objQueryJnl->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007200")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007210")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007220")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007230")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   指定(ロール管理/ロール変数管理)されたテーブルから不要データを廃止する。
    //   
    // パラメータ
    //   $in_strCurTableRole:                ロール管理テーブル名  
    //   $in_strJnlTableRole:                ロール管理ジャーナルテーブル名
    //   $in_strSeqOfCurTableRole:           ロール管理テーブルシーケンス名
    //   $in_strSeqOfJnlTableRole:           ロール管理ジャーナルシーケンス名
    //   $in_arrayConfigOf_Rol_Table:        ロール管理項目リスト 
    //   $in_arrayValueTmplOf_Rol_Table:     ロール管理更新用項目リスト
    //   $in_strCurTableRoleVars:            ロール変数管理テーブル名  
    //   $in_strJnlTableRoleVars:            ロール変数管理ジャーナルテーブル名
    //   $in_strSeqOfCurTableRoleVars:       ロール変数管理テーブルシーケンス名
    //   $in_strSeqOfJnlTableRoleVars:       ロール変数管理ジャーナルシーケンス名
    //   $in_arrayConfigOf_RolVars_Table:    ロール変数管理項目リスト 
    //   $in_arrayValueTmplOf_RolVars_Table: ロール変数管理更新用項目リスト
    //   $in_tbale_id:                       更新するテーブル区分
    //                                       ROLE:ロール管理 他:ロール変数管理
    //   $ina_use_role_id_list:              ロール管理に登録が必要なロール名リスト
    //                                       [ROEL_PACKAGE_ID][ROLE_ID] = 1;
    //   $ina_use_role_vars_name_id_list:    ロール変数管理に登録が必要なロール変数リスト
    //                                       [ROLE_PACKAGE_ID][ROLE_ID][ROLE_VARS_NAME_ID] = ROLE_VARS_NAME;
    //   $ina_use_role_child_vars_name_id_list:    
    //                                       ロール変数管理に登録が必要なロール配列変数リスト
    //                                       [ROLE_PACKAGE_ID][ROLE_ID][ROLE_VARS_NAME_ID] = ROLE_VARS_NAME;
    //   $in_access_user_ids:                データベース更新ユーザーID 
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delRolesDB($in_strCurTableRole,                $in_strJnlTableRole,
                        $in_strSeqOfCurTableRole,           $in_strSeqOfJnlTableRole,
                        $in_arrayConfigOf_Rol_Table,        $in_arrayValueTmplOf_Rol_Table,
                        $in_strCurTableRoleVars,            $in_strJnlTableRoleVars,
                        $in_strSeqOfCurTableRoleVars,       $in_strSeqOfJnlTableRoleVars,
                        $in_arrayConfigOf_RolVars_Table,    $in_arrayValueTmplOf_RolVars_Table,
                        $in_tbale_id,$ina_use_role_id_list,$ina_use_role_vars_name_id_list,
                        $ina_use_role_child_vars_name_id_list,
                        $in_access_user_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        if($in_tbale_id == "ROLE"){
            $strPkey                = "ROLE_ID";
            $strCurTable            = $in_strCurTableRole;
            $strJnlTable            = $in_strJnlTableRole;
            $strSeqOfCurTable       = $in_strSeqOfCurTableRole;
            $strSeqOfJnlTable       = $in_strSeqOfJnlTableRole;
            $arrayConfig            = $in_arrayConfigOf_Rol_Table;
            $arrayValue             = $in_arrayValueTmplOf_Rol_Table;
        }
        else{
            $strPkey                = "VARS_NAME_ID";
            $strCurTable            = $in_strCurTableRoleVars;
            $strJnlTable            = $in_strJnlTableRoleVars;
            $strSeqOfCurTable       = $in_strSeqOfCurTableRoleVars;
            $strSeqOfJnlTable       = $in_strSeqOfJnlTableRoleVars;
            $arrayConfig            = $in_arrayConfigOf_RolVars_Table;
            $arrayValue             = $in_arrayValueTmplOf_RolVars_Table;
        }

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $strPkey,
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn_sel->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007240")));
            unset($objQueryUtn_sel);
            return false;
        }

        $objQueryUtn_sel->sqlBind($arrayUtnBind);

        $r = $objQueryUtn_sel->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007250")));
            unset($objQueryUtn_sel);
            return false;
        }
        // fetch行数を取得
        while ( $row = $objQueryUtn_sel->resultFetch() ){

            if($in_tbale_id == "ROLE"){
                // 該当ロールがロールパッケージファイルに登録されているか判定
                if(isset($ina_use_role_id_list[$row['ROLE_PACKAGE_ID']][$row['ROLE_ID']]) === true){
                    // 登録されている場合はなにもしない。
                    continue;
                }
            }
            else{
                // 該当ロール変数がロールパッケージファイルに登録されているか判定
                if((isset($ina_use_role_vars_name_id_list[$row['ROLE_PACKAGE_ID']]
                                                         [$row['ROLE_ID']]
                                                         [$row['VARS_NAME_ID']]) === true)
                   ||
                   (isset($ina_use_role_child_vars_name_id_list[$row['ROLE_PACKAGE_ID']]
                                                               [$row['ROLE_ID']]
                                                               [$row['VARS_NAME_ID']]) === true)){
                    // 登録されている場合はなにもしない。
                    continue;
                }
            }
            $tgt_row = $row;

            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007260")));
                unset($objQueryUtn_sel);
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true)); 
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007270")));
                unset($objQueryUtn_sel);
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

            setDBUpdateflg();

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $strPkey,
                                                 $strCurTable,
                                                 $strJnlTable,
                                                 $arrayConfig,
                                                 $tgt_row,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007280")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007290")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007300")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007310")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($objQueryUtn_sel);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    // 処理内容
    //   パターン詳細のデータ取得
    //   
    // パラメータ
    //   $getPatternLinkDB
    //            パターン詳細リスト
    //            [PATTERN_ID][ROLE_PACKAGE_ID][ROLE_ID]=1
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getPatternLinkDB(&$ina_pattern_list){
        global          $objMTS;
        global          $objDBCA;

        $ina_pattern_list= array();
        ////////////////////////////////////////////////////////////////
        // ロールパッケージ管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////
        $sqlUtnBody = "SELECT " 
                     ." PATTERN_ID, "              
                     ." ROLE_PACKAGE_ID, "
                     ." ROLE_ID "
                     ."FROM  B_ANSIBLE_LRL_PATTERN_LINK "
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007320")));
            unset($objQueryUtn);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007330")));
            unset($objQueryUtn);
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007340")));
            unset($objQueryUtn);
            return false;
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $ina_pattern_list[$row['PATTERN_ID']][$row['ROLE_PACKAGE_ID']][$row['ROLE_ID']] = 1;
        }
        // DBアクセス事後処理
        unset($objQueryUtn);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    // 処理内容
    //   変数マスタからデータを取得する。
    //   
    // パラメータ
    //  $in_strCurTableAnsVarsTable:   変数マスタテーブル名
    //  $in_strJnlTableAnsVarsTable:   変数マスタジャーナルテーブル名
    //  $in_arrayConfig:               変数マスタ項目リスト
    //  $in_arrayValues:               変数マスタ更新項目リスト
    //  $in_temp_array:                変数マスタ SELECT条件
    //  $ina_varsmaster_list:          変数マスタ 取得データ返却
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getVarsMasterDB($in_strCurTableAnsVarsTable,$in_strJnlTableAnsVarsTable,
                             $in_arrayConfig,$in_arrayValue,$in_temp_array,&$ina_varsmaster_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $ina_varsmaster_list = array();        

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT",
                                             "VARS_NAME_ID",
                                             $in_strCurTableAnsVarsTable,
                                             $in_strJnlTableAnsVarsTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $in_temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007350")));
            unset($objQueryUtn);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007360")));
            unset($objQueryUtn);
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007370")));
            unset($objQueryUtn);
            return false;
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $strForcusVarName = $row["VARS_NAME"];
            $ina_varsmaster_list[$strForcusVarName] = $row;
        }
        unset($objQueryUtn);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   作業パターン変数紐付マスタからデータを取得する。
    //   
    // パラメータ
    //  $in_strCurTableAnsPatternVarsLink:   作業パターン変数紐付マスタテーブル名
    //  $in_strJnlTableAnsPatternVarsLink:   作業パターン変数紐付マスタジャーナルテーブル名
    //  $in_arrayConfig:                     作業パターン変数紐付マスタ項目リスト
    //  $in_arrayValues:                     作業パターン変数紐付マスタ更新項目リスト
    //  $in_temp_array:                      作業パターン変数紐付マスタ SELECT条件
    //  $ina_varsmaster_list:                作業パターン変数紐付マスタ 取得データ返却
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getPatternVarsLinkDB($in_strCurTableAnsPatternVarsLink,$in_strJnlTableAnsPatternVarsLink,
                                  $in_arrayConfig,$in_arrayValue,
                                  $in_temp_array,&$ina_patternvarslink_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $ina_patternvarslink_list = array();        

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT",
                                            "VARS_LINK_ID",
                                             $in_strCurTableAnsPatternVarsLink,
                                             $in_strJnlTableAnsPatternVarsLink,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $in_temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007380")));
            unset($objQueryUtn);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007390")));
            unset($objQueryUtn);
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007400")));
            unset($objQueryUtn);
            return false;
        }
        //----更新対象のテーブルから行を取得。作業パターンごとにグルーピングして格納
        while ( $row = $objQueryUtn->resultFetch() ){
            $intFoucsPattern = $row["PATTERN_ID"];
            $intFocusVarName = $row["VARS_NAME_ID"];
            // T0008  aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
            if( array_key_exists($intFoucsPattern, $ina_patternvarslink_list) === false ){
                $ina_patternvarslink_list[$intFoucsPattern] = array();
            }
            $ina_patternvarslink_list[$intFoucsPattern][$intFocusVarName] = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   配列変数マスタからデータを取得する。
    //   
    // パラメータ
    //  $in_strCurTable:               配列変数マスタテーブル名
    //  $in_strJnlTable:               配列変数マスタジャーナルテーブル名
    //  $in_arrayConfig:               配列変数マスタ項目リスト
    //  $in_arrayValues:               配列変数マスタ更新項目リスト
    //  $in_temp_array:                配列変数マスタ SELECT条件
    //  $ina_childvarsmaster_list:     配列変数マスタ 取得データ返却
    //                                   [PARENT_VARS_NAME_ID]][CHILD_VARS_NAME] = $row;
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getChildVarsMasterDB($in_strCurTable,$in_strJnlTable,
                                  $in_arrayConfig,$in_arrayValue,$in_temp_array,&$ina_childvarsmaster_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $ina_childvarsmaster_list = array();        

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT",
                                             "VARS_NAME_ID",
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $in_temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007350")));
            unset($objQueryUtn);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007360")));
            unset($objQueryUtn);
            return false;
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007370")));
            unset($objQueryUtn);
            return false;
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $ina_childvarsmaster_list[$row["PARENT_VARS_NAME_ID"]][$row["CHILD_VARS_NAME"]] = $row;
        }
        unset($objQueryUtn);
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0011
    // 処理内容
    //   該当変数がdefault変数定義ファイルの変数構造エラーリストに登録されているか確認
    //   
    // パラメータ
    //  $in_vars_name:                 変数名
    //  $in_errmsg_flg:                登録時のエラーメッセージ
    // 
    // 戻り値
    //   True:未登録　　False:登録
    ////////////////////////////////////////////////////////////////////////////////
    function chkVarsAttributeError($in_vars_name,$in_errmsg_flg=false){
        global    $objMTS;
        global    $log_level;
        global    $ifa_err_vars_list;

        if(@count($ifa_err_vars_list[$in_vars_name]) !== 0){
if ( $log_level === 'DEBUG' ){
            if($in_errmsg_flg===true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-70056",
                                                                                    array($in_vars_name)));
            }
}

            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0012
    // 処理内容
    //   変数の型を判定
    //   
    // パラメータ
    //  $in_vars_name:                 変数名
    // 
    // 戻り値
    //   True:配列変数　　False:通常変数
    ////////////////////////////////////////////////////////////////////////////////
    function chkVarsAttribute($in_vars_name){
        global $lva_vars_attr_list;
        return $lva_vars_attr_list[$in_vars_name];
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0015
    // 処理内容
    //   ロール変数管理のデータを更新する。
    //   
    // パラメータ
    //   $in_strCurTable:            ロール変数具体値管理テーブル名  
    //   $in_strJnlTable:            ロール変数具体値管理ジャーナルテーブル名
    //   $in_strSeqOfCurTable:       ロール変数具体値管理テーブルシーケンス名
    //   $in_strSeqOfJnlTable:       ロール変数具体値管理ジャーナルシーケンス名
    //   $in_arrayConfigOf:          ロール変数具体値管理項目リスト 
    //   $in_arrayValueTmplOf:       ロール変数具体値管理更新用項目リスト
    //   $in_role_package_id:        登録するロールパッケージID
    //   $in_role_id:                登録するロールID
    //   $in_var_type:               変数タイプ
    //   $in_db_var_type:            登録する変数タイプ
    //   $in_vars_name_id:           登録する親変数名 変数一覧 Pkey
    //   $in_col_seq_combination_ids:登録するメンバー変数名 Pkey
    //   $in_assign_seq:             登録する代入順序
    //   $in_var_val:                登録する具体値
    //   $in_access_user_ids:        データベース更新ユーザーID 
    //   $in_varsval_id:             ロール変数具体値管理に登録したレコードのPkey
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addRoleVarsValDB($in_strCurTable,            $in_strJnlTable,
                              $in_strSeqOfCurTable,       $in_strSeqOfJnlTable,
                              $in_arrayConfigOf,          $in_arrayValueTmplOf,
                              $in_role_package_id,$in_role_id,$in_var_type,
                              $in_db_var_type,            // DB登録用 VAR_TYPE
                              $in_vars_name_id,           // 親変数名
                              $in_col_seq_combination_id, // メンバー変数名
                              $in_assign_seq,             // 代入順序
                              $in_var_val,                // 具体値
                              $in_access_user_id,&$in_varsval_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $strCurTable            = $in_strCurTable;
        $strJnlTable            = $in_strJnlTable;
        $strSeqOfCurTable       = $in_strSeqOfCurTable;
        $strSeqOfJnlTable       = $in_strSeqOfJnlTable;

        $arrayConfig = $in_arrayConfigOf;
        $arrayValue  = $in_arrayValueTmplOf;

        $col_seq_need = false;
        $ass_seq_need = false;
        switch($in_var_type){
        case '0':      //一般変数
            if(strlen($in_col_seq_combination_id) == 0){
                $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID = :ROLE_PACKAGE_ID AND " .
                                             "ROLE_ID         = :ROLE_ID         AND " .
                                             "VAR_TYPE        = :VAR_TYPE        AND " .
                                             "VARS_NAME_ID    = :VARS_NAME_ID    AND " .
                                             "COL_SEQ_COMBINATION_ID IS NULL     AND " .
                                             "ASSIGN_SEQ             IS NULL");
            }
            else{
                $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID        = :ROLE_PACKAGE_ID        AND " .
                                             "ROLE_ID                = :ROLE_ID                AND " .
                                             "VAR_TYPE               = :VAR_TYPE               AND " .
                                             "VARS_NAME_ID           = :VARS_NAME_ID           AND " .
                                             "COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID AND " .
                                             "ASSIGN_SEQ             IS NULL");
                $col_seq_need = true;
            }
            break;
        case '1':      //複数管位置変数
            if(strlen($in_col_seq_combination_id) == 0){
                $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID = :ROLE_PACKAGE_ID AND " .
                                             "ROLE_ID         = :ROLE_ID         AND " .
                                             "VAR_TYPE        = :VAR_TYPE        AND " .
                                             "VARS_NAME_ID    = :VARS_NAME_ID    AND " .
                                             "COL_SEQ_COMBINATION_ID IS NULL     AND " .
                                             "ASSIGN_SEQ      = :ASSIGN_SEQ");
                $ass_seq_need = true;
            }
            else{
                $temp_array = array('WHERE'=>"ROLE_PACKAGE_ID        = :ROLE_PACKAGE_ID        AND " .
                                             "ROLE_ID                = :ROLE_ID                AND " .
                                             "VAR_TYPE               = :VAR_TYPE               AND " .
                                             "VARS_NAME_ID           = :VARS_NAME_ID           AND " .
                                             "COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID AND " .
                                             "ASSIGN_SEQ             = :ASSIGN_SEQ");
                $col_seq_need = true;
                $ass_seq_need = true;
            }
            break;
        }
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "VARSVAL_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007120")));
            return false;
        }

        if(($col_seq_need === false) && ($ass_seq_need === false)){
            $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                         'ROLE_ID'=>$in_role_id,
                                         'VAR_TYPE'=>$in_db_var_type,
                                         'VARS_NAME_ID'=>$in_vars_name_id));
        }
        if(($col_seq_need === false) && ($ass_seq_need === true)){
            $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                         'ROLE_ID'=>$in_role_id,
                                         'VAR_TYPE'=>$in_db_var_type,
                                         'VARS_NAME_ID'=>$in_vars_name_id,
                                         'ASSIGN_SEQ'=>$in_assign_seq));
        }
        if(($col_seq_need === true) && ($ass_seq_need === false)){
            $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                         'ROLE_ID'=>$in_role_id,
                                         'VAR_TYPE'=>$in_db_var_type,
                                         'VARS_NAME_ID'=>$in_vars_name_id,
                                         'COL_SEQ_COMBINATION_ID'=>$in_col_seq_combination_id));
        }
        if(($col_seq_need === true) && ($ass_seq_need === true)){
            $objQueryUtn->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id,
                                         'ROLE_ID'=>$in_role_id,
                                         'VAR_TYPE'=>$in_db_var_type,
                                         'VARS_NAME_ID'=>$in_vars_name_id,
                                         'ASSIGN_SEQ'=>$in_assign_seq,
                                         'COL_SEQ_COMBINATION_ID'=>$in_col_seq_combination_id));
        }

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007130")));
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
             $action  = "INSERT";
             $tgt_row = $arrayValue;

        }
        else{
            // ロール変数ID退避
            $in_varsval_id     = $row['VARSVAL_ID'];
            if($row['DISUSE_FLAG'] == '1'){
                 $action = "UPDATE";
                 $tgt_row = $row;

            }
            else{
                if($row['VARS_VALUE'] != $in_var_val){
                    $action = "UPDATE";
                    $tgt_row = $row;
                }
                else{
                    //登録済みなので処理終了
                    return true;
                }
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007140")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007150")));
                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["ROLE_PACKAGE_ID"]    = $in_role_package_id;
            $tgt_row["ROLE_ID"]            = $in_role_id;
            $tgt_row["VAR_TYPE"]           = $in_db_var_type;
            $tgt_row["VARS_NAME_ID"]       = $in_vars_name_id;
            $tgt_row["COL_SEQ_COMBINATION_ID"]  = $in_col_seq_combination_id;
            $tgt_row["ASSIGN_SEQ"]         = $in_assign_seq;      
            $tgt_row["VARS_VALUE"]         = $in_var_val;         
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007160")) );
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007170")) );
            }

            // ロール変数ID退避
            $in_varsval_id                 = $retArray[0];

            // ロール変数具体値管理に登録する情報設定
            $tgt_row["VARSVAL_ID"]         = $retArray[0];
            $tgt_row["ROLE_PACKAGE_ID"]    = $in_role_package_id;
            $tgt_row["ROLE_ID"]            = $in_role_id;
            $tgt_row["VAR_TYPE"]           = $in_db_var_type;
            $tgt_row["VARS_NAME_ID"]       = $in_vars_name_id;
            $tgt_row["COL_SEQ_COMBINATION_ID"]  = $in_col_seq_combination_id;
            $tgt_row["ASSIGN_SEQ"]         = $in_assign_seq;      
            $tgt_row["VARS_VALUE"]         = $in_var_val;         
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007180")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007190")));
                return false;
            }

            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        }

        setDBUpdateflg();

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "VARSVAL_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $tgt_row,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if( $objQueryUtn->getStatus()===false ||
            $objQueryJnl->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007200")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007210")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007220")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007230")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0016
    // 処理内容
    //   ロール変数具体値管理テーブルから不要データを廃止する。
    //   
    // パラメータ
    //   $in_strCurTableRoleVars:            ロール変数具体値管理テーブル名  
    //   $in_strJnlTableRoleVars:            ロール変数具体値管理ジャーナルテーブル名
    //   $in_strSeqOfCurTableRoleVars:       ロール変数具体値管理テーブルシーケンス名
    //   $in_strSeqOfJnlTableRoleVars:       ロール変数具体値管理ジャーナルシーケンス名
    //   $in_arrayConfigOf_RolVars_Table:    ロール変数具体値管理項目リスト 
    //   $in_arrayValueTmplOf_RolVars_Table: ロール変数具体値管理更新用項目リスト
    //   $ina_use_role_varsval:    
    //                                       ロール変数具体値管理に登録が必要なロール変数(Pkey)リスト
    //                                       [VARSVAL_ID] = 0;
    //   $in_access_user_ids:                データベース更新ユーザーID 
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delRoleVarsValDB($in_strCurTable,            $in_strJnlTable,
                              $in_strSeqOfCurTable,       $in_strSeqOfJnlTable,
                              $in_arrayConfigOf,          $in_arrayValueTmplOf,
                              $ina_use_role_varsval,
                              $in_access_user_id,         $ina_OtherUserLastUpdate_vars_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        $strPkey                = "VARSVAL_ID";
        $strCurTable            = $in_strCurTable;
        $strJnlTable            = $in_strJnlTable;
        $strSeqOfCurTable       = $in_strSeqOfCurTable;
        $strSeqOfJnlTable       = $in_strSeqOfJnlTable;
        $arrayConfig            = $in_arrayConfigOf;
        $arrayValue             = $in_arrayValueTmplOf;

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $strPkey,
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn_sel->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007240")));
            unset($objQueryUtn_sel);
            return false;
        }

        $objQueryUtn_sel->sqlBind($arrayUtnBind);

        $r = $objQueryUtn_sel->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007250")));
            unset($objQueryUtn_sel);
            return false;
        }
        // fetch行数を取得
        while ( $row = $objQueryUtn_sel->resultFetch() ){

            // 該当ロールがロールパッケージファイルに登録されているか判定
            if(isset($ina_use_role_varsval[$row[$strPkey]]) === true){

                // 登録されている場合はなにもしない。
                continue;
            }
            else{
            }

            // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
            if(@count($ina_OtherUserLastUpdate_vars_list[$row["VARS_NAME_ID"]]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90225",
                                                                   array($ina_OtherUserLastUpdate_vars_list[$row["VARS_NAME_ID"]])));
}
                //次へ
                continue;
            }
            $tgt_row = $row;

            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007260")));
                unset($objQueryUtn_sel);
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007270")));
                unset($objQueryUtn_sel);
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

            setDBUpdateflg();

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $strPkey,
                                                 $strCurTable,
                                                 $strJnlTable,
                                                 $arrayConfig,
                                                 $tgt_row,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007280")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007290")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007300")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007310")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($objQueryUtn_sel);
        return true;
    }

    function chkDefVarsList($in_role_package_id,$in_role_name,$in_role_vars_name,&$in_chl_var_list,$in_errmsg_flg=false){
        global    $objMTS;
        global    $log_level;
        global $lta_role_package_list;
        global $ifa_role_def_var_list;

        $in_chl_var_list = array();
        foreach($lta_role_package_list[$in_role_package_id] as $role_package_name=>$role_package_file)

        if(@count($ifa_role_def_var_list[$in_role_package_id][$in_role_name][$in_role_vars_name]) === 0){
if ( $log_level === 'DEBUG' ){
            if($in_errmsg_flg===true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-70074",
                                                   array($role_package_name,$in_role_name,$in_role_vars_name)));
            }
}
            return false;
        }
        $in_chl_var_list = $ifa_role_def_var_list[$in_role_package_id][$in_role_name][$in_role_vars_name];
        return true;
    }

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";

        require ($root_dir_path . $log_output_php);
    }

    // debug only
    function Local_var_dump($p1,$p2,$p3){
        ob_start();
        var_dump($p3);
        $ret = ob_get_contents();
        ob_clean();
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $ret";
        require ($root_dir_path . $log_output_php);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Fxxxx
    // 処理内容
    //   多次元変数メンバー管理のデータを更新する。
    //   
    // パラメータ
    //   $in_strCurTable:                テーブル名  
    //   $in_strJnlTable:                ジャーナルテーブル名
    //   $in_strSeqOfCurTable:           テーブルシーケンス名
    //   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
    //   $in_arrayConfig:                管理項目リスト 
    //   $in_arrayValue:                 管理更新用項目リスト
    //   $in_row_value:                  登録データリスト
    //                                     ['ARRAY_MEMBER_ID']           親変数へのキー 
    //                                     ['PARENT_VARS_KEY_ID']     親メンバー変数へのキー 
    //                                     ['VARS_KEY_ID']            自メンバー変数のキー
    //                                     ['VARS_NAME']              メンバー変数名　　0:配列変数を示す
    //                                     ['ARRAY_NEST_LEVEL']       階層 1～
    //                                     ['ASSIGN_SEQ_NEED']        代入順序有無　1:必要　初期値:NULL
    //                                     ['COL_SEQ_NEED']           列順序有無  　1:必要　初期値:NULL
    //                                     ['MEMBER_DISP']            代入値管理系の表示有無　1:必要　初期値:NULL
    //                                     ['MAX_COL_SEQ']            最大繰返数
    //                                     ['VRAS_NAME_PATH']         メンバー変数の階層パス
    //                                     ['VRAS_NAME_ALIAS']        代入値管理系の表示メンバー変数名
    //   $in_access_user_ids:            データベース更新ユーザーID 
    //   $ina_pkey_id:                   グループメンバー変数管理に登録したレコードのPkey
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addArrayMemberDB( $in_strCurTable,           $in_strJnlTable,
                                 $in_strSeqOfCurTable,      $in_strSeqOfJnlTable,
                                 $in_arrayConfig,           $in_arrayValue,
                                 $in_row_value,
                                 $in_access_user_id,&$ina_pkey_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $temp_array = array('WHERE'=>"VARS_NAME_ID       = :VARS_NAME_ID          AND " .
                                     "PARENT_VARS_KEY_ID = :PARENT_VARS_KEY_ID    AND " .
                                     "VARS_KEY_ID        = :VARS_KEY_ID           AND " .
                                     "VARS_NAME          = :VARS_NAME             AND " .
                                     "ARRAY_NEST_LEVEL   = :ARRAY_NEST_LEVEL      AND " .
                                     "ASSIGN_SEQ_NEED    = :ASSIGN_SEQ_NEED       AND " .
                                     "COL_SEQ_NEED       = :COL_SEQ_NEED          AND " .
                                     "MEMBER_DISP        = :MEMBER_DISP           AND " .
                                     "VRAS_NAME_PATH     = :VRAS_NAME_PATH        AND " .
                                     "MAX_COL_SEQ        = :MAX_COL_SEQ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                             "SELECT FOR UPDATE", 
                                             "ARRAY_MEMBER_ID",
                                             $in_strCurTable, 
                                             $in_strJnlTable, 
                                             $in_arrayConfig, 
                                             $in_arrayValue, 
                                             $temp_array );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007000")));
            return false;
        }


        $objQueryUtn->sqlBind( array('VARS_NAME_ID'=>$in_row_value['VARS_NAME_ID'],
                                     'PARENT_VARS_KEY_ID'=>$in_row_value['PARENT_VARS_KEY_ID'],  // 親メンバー変数へのキー 
                                     'VARS_KEY_ID'=>$in_row_value['VARS_KEY_ID'],                // 自メンバー変数のキー
                                     'VARS_NAME'=>$in_row_value['VARS_NAME'],                    // メンバー変数名　　0:配列変数を示す
                                     'ARRAY_NEST_LEVEL'=>$in_row_value['ARRAY_NEST_LEVEL'],      // 階層 1～
                                     'ASSIGN_SEQ_NEED'=>$in_row_value['ASSIGN_SEQ_NEED'],        // 代入順序有無　1:必要　初期値:NULL
                                     'COL_SEQ_NEED'=>$in_row_value['COL_SEQ_NEED'],              // 列順序有無  　1:必要　初期値:NULL
                                     'MEMBER_DISP'=>$in_row_value['MEMBER_DISP'],                // 代入値管理系の表示有無　1:必要　初期値:NULL
                                     'VRAS_NAME_PATH'=>$in_row_value['VRAS_NAME_PATH'],          // メンバー変数の階層パス
                                     'MAX_COL_SEQ'=>$in_row_value['MAX_COL_SEQ'],        // 代入値管理系の表示メンバー変数名
                                    ));
        
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007010")));
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
             $action  = "INSERT";
             $tgt_row = $in_arrayValue;
        }
        else{
            // Pkey退避
            $ina_pkey_id     = $row['ARRAY_MEMBER_ID'];
            if($row['DISUSE_FLAG'] == '1'){
                 // 廃止なので復活する。
                 $action = "UPDATE";
                 $tgt_row = $row;
            }
            else{
                 //登録済みなので処理終了
                 return true;
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007020")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007030")));
                return false;
            }
            
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;
            
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007040")) );
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007050")) );
            }

            // Pkey退避
            $ina_pkey_id                      = $retArray[0];

            // 登録する情報設定
            $tgt_row['ARRAY_MEMBER_ID']    = $retArray[0];
            $tgt_row['VARS_NAME_ID']       = $in_row_value['VARS_NAME_ID'];
            $tgt_row['PARENT_VARS_KEY_ID'] = $in_row_value['PARENT_VARS_KEY_ID'];  // 親メンバー変数へのキー 
            $tgt_row['VARS_KEY_ID']        = $in_row_value['VARS_KEY_ID'];         // 自メンバー変数のキー
            $tgt_row['VARS_NAME']          = $in_row_value['VARS_NAME'];           // メンバー変数名　　0:配列変数を示す
            $tgt_row['ARRAY_NEST_LEVEL']   = $in_row_value['ARRAY_NEST_LEVEL'];    // 階層 1～
            $tgt_row['ASSIGN_SEQ_NEED']    = $in_row_value['ASSIGN_SEQ_NEED'];     // 代入順序有無　1:必要　初期値:NULL
            $tgt_row['COL_SEQ_NEED']       = $in_row_value['COL_SEQ_NEED'];        // 列順序有無  　1:必要　初期値:NULL
            $tgt_row['MEMBER_DISP']        = $in_row_value['MEMBER_DISP'];         // 代入値管理系の表示有無　1:必要　初期値:NULL
            $tgt_row['VRAS_NAME_PATH']     = $in_row_value['VRAS_NAME_PATH'];      // メンバー変数の階層パス
            $tgt_row['VRAS_NAME_ALIAS']    = $in_row_value['VRAS_NAME_ALIAS'];     // 代入値管理系の表示メンバー変数名
            $tgt_row['MAX_COL_SEQ']        = $in_row_value['MAX_COL_SEQ'];         // 繰返最大数

            $tgt_row["DISUSE_FLAG"]        = '0';

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007060")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007070")));
                return false;
            }
            
            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        }
        setDBUpdateflg();

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "ARRAY_MEMBER_ID",
                                             $in_strCurTable, 
                                             $in_strJnlTable, 
                                             $in_arrayConfig, 
                                             $tgt_row,
                                             $temp_array );
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
        
        if( $objQueryUtn->getStatus()===false || 
            $objQueryJnl->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007080")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007090")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007100")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007110")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Fxxxx
    // 処理内容
    //   指定されたテーブルから不要データを廃止する。
    //   
    // パラメータ
    //   $in_strCurTable:                    テーブル名  
    //   $in_strJnlTable:                    ジャーナルテーブル名
    //   $in_strSeqOfCurTable:               テーブルシーケンス名
    //   $in_strSeqOfJnlTable:               ジャーナルシーケンス名
    //   $in_arrayConfig:                    項目リスト 
    //   $in_arrayValue:                     更新用項目リスト
    //   $in_strPkey:                        Pkey項目名
    //   $ina_use_pkey_list:                 廃止対象外のPkeyリスト
    //                                        [pkey1]
    //                                        ・
    //                                       [pkeyn]
    //   $in_access_user_ids:                データベース更新ユーザーID 
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delDB($in_strCurTable,                $in_strJnlTable,
                   $in_strSeqOfCurTable,           $in_strSeqOfJnlTable,
                   $in_arrayConfig,                $in_arrayValue,
                   $in_strPkey,                    $ina_use_pkey_list,
                   $in_access_user_id,             $ina_OtherUserLastUpdate_vars_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $in_strPkey,
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn_sel->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007240")));
            unset($objQueryUtn_sel);
            return false;
        }

        $objQueryUtn_sel->sqlBind($arrayUtnBind);

        $r = $objQueryUtn_sel->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007250")));
            unset($objQueryUtn_sel);
            return false;
        }
        // fetch行数を取得
        while ( $row = $objQueryUtn_sel->resultFetch() ){
            // 変数一覧の該当変数の最終更新者が他プロセスの場合、多次元変数メンバー管理の更新をしない
            if(@count($ina_OtherUserLastUpdate_vars_list[$row["VARS_NAME_ID"]]) != 0){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-90223",
                                                                   array($ina_OtherUserLastUpdate_vars_list[$row["VARS_NAME_ID"]])));

}

                //次へ
                continue;
            }
            // 廃止対象外のPkeyリストに登録されているか判定
            if(@count($ina_use_pkey_list[$row[$in_strPkey]]) != 0){
               // 登録されている場合はなにもしない。
                continue;
            }
            $tgt_row = $row;

            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007260")));
                unset($objQueryUtn_sel);
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007270")));
                unset($objQueryUtn_sel);
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

            setDBUpdateflg();

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $in_strPkey,
                                                 $in_strCurTable,
                                                 $in_strJnlTable,
                                                 $in_arrayConfig,
                                                 $tgt_row,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007280")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007290")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007300")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007310")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($objQueryUtn_sel);
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   多次元変数配列組合せ管理からデータを取得する。
    //   
    // パラメータ
    //  $in_strCurTable:               多次元変数配列組合せ管理テーブル名
    //  $in_strJnlTable:               多次元変数配列組合せ管理ジャーナルテーブル名
    //  $in_arrayConfig:               多次元変数配列組合せ管理項目リスト
    //  $in_arrayValues:               多次元変数配列組合せ管理更新項目リスト
    //  $in_temp_array:                多次元変数配列組合せ管理SELECT条件
    //  $ina_MemberColComb_list:       多次元変数配列組合せ管理取得データ返却
    //                                   [VARS_NAME_ID][ARRAY_MEMBER_ID][COL_SEQ_VALUE] = $row;
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getMemberColCombDB(&$ina_MemberColComb_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $sql = "SELECT                            \n" .
               "   TAB_A.VARS_NAME_ID          ,  \n" .
               "   TAB_A.VRAS_NAME_PATH        ,  \n" .
               "   TAB_A.ARRAY_MEMBER_ID       ,  \n" .
               "   TAB_B.COL_SEQ_COMBINATION_ID,  \n" .
               "   TAB_B.COL_SEQ_VALUE            \n" .
               "FROM B_ANS_LRL_ARRAY_MEMBER TAB_A \n" .
               "LEFT JOIN B_ANS_LRL_MEMBER_COL_COMB TAB_B ON ( TAB_A.ARRAY_MEMBER_ID = TAB_B.ARRAY_MEMBER_ID ) \n" .
               "AND TAB_A.DISUSE_FLAG = '0'       \n" .
               "AND TAB_B.DISUSE_FLAG = '0'         ";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007350")));
            unset($objQuery);
            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007370")));
            unset($objQuery);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $ina_MemberColComb_list[$row["VARS_NAME_ID"]][$row["VRAS_NAME_PATH"]][$row["COL_SEQ_VALUE"]] = $row["COL_SEQ_COMBINATION_ID"];
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   読替表管理のデータを更新する。
    //   
    // パラメータ
    //   $in_strCurTable:                    テーブル名  
    //   $in_strJnlTable:                    ジャーナルテーブル名
    //   $in_strSeqOfCurTabl:                テーブルシーケンス名
    //   $in_strSeqOfJnlTabl:                ジャーナルシーケンス名
    //   $in_arrayConfig:                    管理項目リスト 
    //   $in_arrayValue:                     更新用項目リスト
    //   $in_temp_array:                     検索条件レスト
    //   $in_pkey_name:                      主キー項目名
    //   $in_access_user_id:                 データベース更新ユーザーID 
    //   $in_pky_id:                         更新したレコードの主キー値
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addTranslationVarsDB($in_strCurTable,            $in_strJnlTable,
                                  $in_strSeqOfCurTable,       $in_strSeqOfJnlTable,
                                  $in_arrayConfig,            $in_arrayValue,
                                  $in_temp_array,             $in_pkey_name,
                                  $in_access_user_id,        &$in_pky_id){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $in_pkey_name,
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $in_temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007120")));
            return false;
        }

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007130")));
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
             $action  = "INSERT";
             $tgt_row = $in_arrayValue;
        }
        else{
            // 主キー退避
            $in_pky_id= $row[$in_pkey_name];
            if($row['DISUSE_FLAG'] == '1'){
                 $action = "UPDATE";
                 $tgt_row = $row;
            }
            else{
                 //登録済みなので処理終了
                 return true;
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007140")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007150")));
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007160")) );
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00007170")) );
            }

            // 主キー退避
            $in_pky_id             = $retArray[0];
            $tgt_row[$in_pkey_name]     = $retArray[0];

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007180")));
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007190")));
                return false;
            }

            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        }
        setDBUpdateflg();

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             $in_pkey_name,
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $tgt_row,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if( $objQueryUtn->getStatus()===false ||
            $objQueryJnl->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007200")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007090")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007220")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                               array(__FILE__,__LINE__,"00007230")));
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   読替表から不要データを廃止する。
    //   
    // パラメータ
    //   $in_strCurTable:                    テーブル名  
    //   $in_strJnlTable:                    ジャーナルテーブル名
    //   $in_strSeqOfCurTabl:                テーブルシーケンス名
    //   $in_strSeqOfJnlTabl:                ジャーナルシーケンス名
    //   $in_arrayConfig:                    管理項目リスト 
    //   $in_arrayValue:                     更新用項目リスト
    //   $in_temp_array:                     検索条件レスト
    //   $in_pkey_name:                      主キー項目名
    //   $in_access_user_id:                 データベース更新ユーザーID 
    //   $ina_use_pkey_list:                 読替表に必要な変数リスト 
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delTranslationVarsDB($in_strCurTable,            $in_strJnlTable,
                                     $in_strSeqOfCurTable,       $in_strSeqOfJnlTable,
                                     $in_arrayConfig,            $in_arrayValue,
                                     $in_temp_array,             $in_pkey_name,
                                     $in_access_user_id,         $ina_use_pkey_list){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ");
        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $in_pkey_name,
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $in_temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn_sel->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007240")));
            unset($objQueryUtn_sel);
            return false;
        }

        $objQueryUtn_sel->sqlBind($arrayUtnBind);

        $r = $objQueryUtn_sel->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007250")));
            unset($objQueryUtn_sel);
            return false;
        }
        // fetch行数を取得
        while ( $row = $objQueryUtn_sel->resultFetch() ){

            // 該当変数が登録されているか判定
            if(@count($ina_use_pkey_list[$row[$in_pkey_name]]) !== 0){

                // 登録されている場合はなにもしない。
                continue;
            }
            $tgt_row = $row;

            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007260")));
                unset($objQueryUtn_sel);
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,print_r($retArray,true));
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007270")));
                unset($objQueryUtn_sel);
                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;
            
            setDBUpdateflg();

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $in_pkey_name,
                                                 $in_strCurTable,
                                                 $in_strJnlTable,
                                                 $in_arrayConfig,
                                                 $tgt_row,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007280")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007290")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007300")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(__FILE__,__LINE__,"00007310")));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($objQueryUtn_sel);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   関連するデータベースが更新されバックヤード処理を実行する必要があるか判定
    //
    // パラメータ
    //   $in_a_proc_loaded_list_pkey: A_PROC_LOADED_LISTのROW_ID
    //   &$inout_UpdateRecodeInfo:    バックヤード処理を実行する必要がある場合
    //                                A_PROC_LOADED_LISTのROW_IDとLAST_UPDATE_TIMESTAMPを待避
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkBackyardExecute($in_a_proc_loaded_list_pkey,&$inout_UpdateRecodeInfo)
    {
        $inout_UpdateRecodeInfo = array();
    
        $sql =            " SELECT                                                            \n";
        $sql = $sql .     "   ROW_ID                                                      ,   \n";
        $sql = $sql .     "   LOADED_FLG                                                  ,   \n";
        $sql = $sql .     "   DATE_FORMAT(LAST_UPDATE_TIMESTAMP,'%Y%m%d%H%i%s%f') LAST_UPDATE_TIMESTAMP \n";
        $sql = $sql .     " FROM                                                              \n";
        $sql = $sql .     "   A_PROC_LOADED_LIST                                              \n";
        $sql = $sql .     " WHERE  ROW_ID = $in_a_proc_loaded_list_pkey and (LOADED_FLG is NULL or LOADED_FLG <> '1') \n";
    
        $sqlUtnBody = $sql;
        $arrayUtnBind = array();
        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }

        while($row = $objQuery->resultFetch()) {
            // 代入値自動登録設定で更新されたレコード情報待避
            $inout_UpdateRecodeInfo['ROW_ID']                = $row['ROW_ID'];
            $inout_UpdateRecodeInfo['LAST_UPDATE_TIMESTAMP'] = $row['LAST_UPDATE_TIMESTAMP'];
        }
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   関連するデータベースが更新さりれバックヤード処理が完了したことを記録
    //
    // パラメータ
    //   &$inout_UpdateRecodeInfo:    バックヤード処理が完了したことを記録する情報
    //                                A_PROC_LOADED_LISTのROW_IDとLAST_UPDATE_TIMESTAMP
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function setBackyardExecuteComplete($inout_UpdateRecodeInfo)
    {
        $sql =            " UPDATE A_PROC_LOADED_LIST SET                              \n";
        $sql = $sql .     "   LOADED_FLG = '1' ,LAST_UPDATE_TIMESTAMP = NOW(6)         \n";
        $sql = $sql .     " WHERE                                                      \n";
        $sql = $sql .     "   ROW_ID = :ROW_ID AND                                     \n";
        $sql = $sql .     "   DATE_FORMAT(LAST_UPDATE_TIMESTAMP,'%Y%m%d%H%i%s%f') = :LAST_UPDATE_TIMESTAMP \n";
    
        $sqlUtnBody = $sql;
        $arrayUtnBind = array("ROW_ID"=>$inout_UpdateRecodeInfo['ROW_ID'],
                              "LAST_UPDATE_TIMESTAMP"=>$inout_UpdateRecodeInfo['LAST_UPDATE_TIMESTAMP']);
    
        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }
    
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   バックヤード処理の起動が必要なことを記録 
    //
    // パラメータ
    //   $row_id:                      バックヤード処理ID
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function setBackyardExecute($row_id)
    {
        $sql =            " UPDATE A_PROC_LOADED_LIST SET                              \n";
        $sql = $sql .     "   LOADED_FLG = '0' ,LAST_UPDATE_TIMESTAMP = NOW(6)         \n";
        $sql = $sql .     " WHERE                                                      \n";
        $sql = $sql .     "   ROW_ID = :ROW_ID                                         \n";
    
        $sqlUtnBody = $sql;
        $arrayUtnBind = array("ROW_ID"=>$row_id);
    
        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }
    
        unset($objQuery);
    
        return true;
    }
    // ExecuteしてFetch前のDBアクセスオブジェクトを返却
    function recordSelect($sqlUtnBody, $arrayUtnBind) {

        global    $objMTS;
        global    $objDBCA;

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if($objQueryUtn->getStatus()===false) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }
        $errstr = $objQueryUtn->sqlBind($arrayUtnBind);
        if($errstr != "") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $errstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }

        $r = $objQueryUtn->sqlExecute();
        if(!$r) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }

        return $objQueryUtn;
    }
    function setDBUpdateflg() {
    global $db_update_flg;
        $db_update_flg = true;
    }

    $beforeTime = 0;
    function TimeStampPrint($logdata)
    {
        global $beforeTime;
        $arrayStr = explode(" ", microtime());
        $micro = substr(str_replace("0.","",$arrayStr[0]),0,4);
        $strtime = date('Y-m-d H:i:s', $arrayStr[1]) . '.' . $micro;
        $unixtime = $arrayStr[1] . '.' . $micro;
        if($beforeTime != 0)
        {
            $difftime = "\t" . round(round($unixtime,4) - round($beforeTime,4),4);
            if($difftime == "0")
            {
                $difftime = "\t" . "0.0000";
            }
        }
        else
        {
            $difftime = "\t" . "0.0000";
        }
        $beforeTime  = $unixtime;
        LocalLogPrint("","","$strtime,$difftime," . $logdata);
        return array($strtime,$unixtime);
    }
?>
