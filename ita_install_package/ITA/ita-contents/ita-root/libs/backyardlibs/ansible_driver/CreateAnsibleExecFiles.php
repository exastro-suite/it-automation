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
//  【処理概要】
//    ・Ansibleの実行に必要な情報をデータベースから取得しAnsible実行ディレクトリファイルを生成する。
//
//  【その他】
//
//  F0027 getAnsibleWorkingDirectories
//  F0001 CreateAnsibleWorkingDir
//  F0002 CreateAnsibleWorkingFiles
//  F0003 CreateHostsfile
//  F0004-1 CreateRoleHostvarsfiles
//  F0004-2 CreateHostvarsfiles
//  F0005-1 CreateRoleHostvarsfile
//  F0005-2 CreateHostvarsfile
//  F0006 CreatePlaybookfile
//  F0007 CreateLegacyPlaybookfiles
//  F0008 CreateChildPlaybookfiles
//  F0009 CreatePioneerDialogfiles
//  F0010 CreateDialogfiles
//  F0011 CheckLegacyPlaybookfiles
//  F0012 CheckPioneerPlaybookfiles
//  F0013 CheckChildPlaybookFormat
//  F0014 CheckDialogfileFormat
//  F0024 initstateCommandInfo
//  F0025 errorstateCommand
//  F0026 checkstateCommand
//  F0015 getDBHostList
//  F0016-1 getDBRoleVarList
//  F0016-2 getDBVarList
//  F0016-3 getDBVarMultiArrayVarsList
//  F0017 getDBLegacyPlaybookList
//  F0018 getDBPioneerDialogFileList
//  F0019 addSystemvars
//  F0020 getDBLegacyTemplateMaster
//  F0021 CreateTemplatefiles
//  F0022 CreateLegacytemplatefiles
//  F0023 CheckTemplatefile
//  F0028 getDBPatternList
//  F0029 getDBRolePackage
//  F0031 getDBLegactRoleList
//  F0032 CreateLegacyRolePlaybookfiles
//  F0033 CheckLegacyRolePlaybookfiles
//  F0030 getRolePackageFile
//  F0034 getDBChildVarsList
//  F0037 CreateCopyfiles
//  F0038 CreateLegacyCopyFiles
//  F0039 getDBLegacyCopyMaster
//  F0040 makeHostVarsPath
//  F0041 makeHostVarsArray
//  F0042 MultiArrayVarsToYamlFormatSub
//  F0043 is_assoc
//  F0044 getDBGlobalVarsMaster
//  F0045 CreateLegacyRoleCopyFiles
//  F0046 getDBTranslationTable
//  F0047 LegacyCheckConcreteValueIsVar
//  F0048 LegacyCheckConcreteValueIsVarTemplatefile
//  F0049 LegacyRoleCheckConcreteValueIsVar
//  F0050 CommitHostVarsfiles
//  F0051 initCommandInfo
//  F0052 errorCommand
//  F0053 checkCommand
//  F0054 CreatePioneerCopyFiles
//  F0055 getDBPioneerCopyMaster
//  F0056 CreatePioneertemplatefiles
//  F0057 getDBPioneerTemplateMaster
//  F0058 CopyPioneerTemplatefiles
//
/////////////////////////////////////////////////////////////////////////////////////////
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php");

require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php' );

// 共通モジュールをロード
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');

class CreateAnsibleExecFiles {
    // Ansible 作業ディレクトリ名
    const LC_ANS_IN_DIR                      = "in";
    const LC_ANS_OUT_DIR                     = "out";
    const LC_ANS_TMP_DIR                     = "tmp";
    const LC_ANS_LEGACY_DIR                  = "legacy";
    const LC_ANS_PIONEER_DIR                 = "pioneer";
    const LC_ANS_CHILD_PLAYBOOKS_DIR         = "child_playbooks";
    const LC_ANS_DIALOG_FILES_DIR            = "dialog_files";
    const LC_ANS_HOST_VARS_DIR               = "host_vars";

    // VARS_ATTRIBUTE_01 の 具体値定義
    const LC_VARS_ATTR_STD          = '1';   // 一般変数
    const LC_VARS_ATTR_LIST         = '2';   // 複数具体値
    const LC_VARS_ATTR_STRUCT       = '3';   // 多次元変数

    // ホストグループ変数ディレクトリ
    const LC_ANS_GROUP_VARS_DIR              = "group_vars";
    const LC_ANS_ORG_DIALOG_FILES_DIR        = "original_dialog_files";
    const LC_ANS_ORG_HOST_VARS_DIR           = "original_host_vars";

    // テンプレートファイル格納ディレクトリ名
    const LC_ANS_TEMPLATE_FILES_DIR          = "template_files";

    // ユーザー公開用データリレイストレージパス 変数の名前
    const LC_ANS_OUTDIR_DIR                  = "user_files";

    // 親playbook(pioneer)に埋め込まれるリモート接続コマンド用変数の名前
    const LC_ANS_PROTOCOL_VAR_NAME           = "__loginprotocol__";
    // 親playbook(legacy)に埋め込まれるリモートログインのユーザー用変数の名前
    const LC_ANS_USERNAME_VAR_NAME           = "__loginuser__";
    // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
    const LC_ANS_PASSWD_VAR_NAME             = "__loginpassword__";
    // 対話ファイルに埋め込まれるホスト名用変数の名前
    const LC_ANS_LOGINHOST_VAR_NAME          = "__loginhostname__";
    // 対話ファイルに埋め込まれるIPアドレス用変数の名前
    const LC_ANS_LOGINIP_VAR_NAME            = "__logintarget__";

    // ユーザー公開用 Ansible作業用データリレイストレージパス 変数の名前
    const LC_ANS_OUTDIR_VAR_NAME             = "__workflowdir__";

    // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
    const LC_SYMPHONY_DIR_VAR_NAME           = "__symphony_workflowdir__";

    // 管理対象システム一覧のログイン・パスワード未登録時の内部変数値
    const LC_ANS_UNDEFINE_NAME               = "__undefinesymbol__";

    // Ansible 作業ファイル名
    const LC_ANS_HOSTS_FILE                  = "hosts";
    const LC_ANS_PLAYBOOK_FILE               = "playbook.yml";
    const LC_ANS_ROLE_PLAYBOOK_FILE          = "site.yml";

    const LC_ANS_HOST_VAR_FILE_MK            = "%s/%s";
    const LC_ANS_CHILD_PLAYBOOK_FILE_MK      = "%s/%s-%s";
    const LC_ANS_DIALOG_FILE_HOST_DIR_MK     = "%s/%s";
    const LC_ANS_DIALOG_FILE_MK              = "%s/%s/%s-%s";
    const LC_ANS_ORG_DIALOG_FILE_HOST_DIR_MK = "%s/%s";
    const LC_ANS_ORG_DIALOG_FILE_MK          = "%s/%s/%s-%s";
    const LC_ANS_ORG_HOST_VAR_FILE_MK        = "%s/%s";

    //ITA 子PlayBookファイル格納ディレクトリ
    const LC_ITA_CHILD_PLAYBOOKS_DIR_MK      = "%s/%s/%s";
    //ITA 対話ファイル格納ディレクトリ
    const LC_ITA_DIALOG_FILES_DIR_MK         = "%s/%s/%s";

    //Ansible実行時 テンプレートファイル
    const LC_ANS_TEMPLATE_FILE_MK            = "%s/%s-%s";
    //ITAが管理しているテンプレートファイル格納ディレクトリ
    const LC_ITA_TEMPLATE_FILE_DIR_MK        = "%s/%s/%s";

    // PlayBook.yml 子PlayBookパス
    const LC_PLAYBOOK_PLAYBOOK_CHILD_FILE_MK =  "%s/%s-%s";
    // PlayBook.yml 対話ファイル変数名
    const LC_PLAYBOOK_DIALOG_FILE_VARNAME_MK =  "var%d";

    // inディレクトリ配下のテンプレートファイルパス
    const LC_HOSTVARSFILE_TEMPLATE_FILE_MK =  "%s/%s-%s";

    // inディレクトリ配下のテンプレートファイル
    const LC_HOSTVARSFILE_PNS_TEMPLATE_FILE_MK = "%s-%s";

    // WINRM接続ポート デフォルト値
    const LC_WINRM_PORT                    = 5985;

    // 機器一覧 パスワード管理フラグ(LOGIN_PW_HOLD_FLAG)
    const LC_LOGIN_PW_HOLD_FLAG_OFF           = '0';         // パスワード管理なし
    const LC_LOGIN_PW_HOLD_FLAG_ON            = '1';         // パスワード管理あり
    const LC_LOGIN_PW_HOLD_FLAG_DEF           = '0';         // デフォルト値 パスワード管理なし
    // 機器一覧 Ansible認証方式(LOGIN_AUTH_TYPE)
    const LC_LOGIN_AUTH_TYPE_KEY              = '1';         // 鍵認証
    const LC_LOGIN_AUTH_TYPE_PW               = '2';         // パスワード認証
    const LC_LOGIN_AUTH_TYPE_DEF              = '1';         // デフォルト値 鍵認証

    //ローカル変数定義
    private $lv_Ansible_driver_id;                 //Ansibleドライバ(legacy/pioneer)区分
    private $lv_hostaddress_type;
    
    //ansible用各ディレクトリ変数
    private $lv_Ansible_base_Dir;                  //Ansibleベースディレクトリ
    private $lv_Ansible_in_Dir;                    //inディレクトリ
    private $lv_Ansible_child_playbooks_Dir;       //child_playbooksディレクトリ
    private $lv_Ansible_dialog_files_Dir;          //dialog_filesディレクトリ
    private $lv_Ansible_host_vars_Dir;             //host_varsディレクトリ
    private $lv_Ansible_out_Dir;                   //outディレクトリ
    private $lv_Ansible_tmp_Dir;                   //tmpディレクトリ
    private $lv_Ansible_original_dialog_files_Dir; //original_dialog_filesディレクトリ
    private $lv_Ansible_original_hosts_vars_Dir;   //original_hosts_varsディレクトリ
    private $lv_Ansible_template_files_Dir;        //template_filesディレクトリ

    //親PlayBook内各ディレクトリ変数
    private $lv_Playbook_child_playbooks_Dir;      //PlayBook内 子PlayBookパス
    private $lv_Hostvarsfile_template_file_Dir;    //inディレクトリ配下 テンプレートファイルパス
    private $lv_winrm_id;                          // 作業パターンの接続先がwindowsかを判別する項目

    //ITA用各ディレクトリ変数
    private $lv_ita_child_playbooks_Dir;           //子PlayBook格納ディレクトリ(ITA側)
    private $lv_ita_dialog_files_Dir;              //対話ファイル格納ディレクトリ(ITA側)
    private $lv_ita_template_files_Dir;            //テンプレートファイル格納ディレクトリ(ITA側)

    // テーブル名定義
    private $lv_ansible_vars_masterDB;             // 変数管理テーブルテーブル名
    private $lv_ansible_vars_assignDB;             // 代入値管理テーブルテーブル名
    private $lv_ansible_pattern_vars_linkDB;       // 作業パターン変数紐付管理テーブルテーブル名
    private $lv_ansible_pho_linkDB;                // 作業対象ホストテーブル テーブル名
    private $lv_ansible_master_fileDB;             // 素材管理テーブル テーブル名
    private $lv_ansible_master_file_pkeyITEM;      // 素材管理テーブル 素材ID(pkey)項目名
    private $lv_ansible_master_file_nameITEM;      // 素材管理テーブル 素材ファイル項目名
    private $lv_ansible_pattern_linkDB;            // 作業パターン詳細 テーブル名
    private $lv_ansible_role_packageDB;            // ロールパッケージ管理 テーブル名
    private $lv_ansible_roleDB;                    // ロール管理 テーブル名
    private $lv_ansible_role_varsDB;               // ロール変数管理 テーブル名
    private $lv_ansible_child_varsDB;              // メンバー変数管理  テーブル名

    // copyファイル格納ディレクトリ名
    const LC_ANS_COPY_FILES_DIR               = "copy_files";

    //Ansible実行時 コピーファイル
    const LC_ANS_COPY_FILE_MK                 = "%s/%s-%s";

    //ITAが管理しているコピーファイル格納ディレクトリ
    const LC_ITA_COPY_FILE_DIR_MK             = "%s/%s/%s";

    // inディレクトリ配下のcopyファイルパス ファイル名の前にPkeyを付けない。
    const LC_HOSTVARSFILE_COPY_FILE_MK        = "%s/%s-%s";

    //copy_filesディレクトリ
    private $lv_Ansible_copy_files_Dir; 

    //inディレクトリ配下 コピーファイルパス
    private $lv_Hostvarsfile_copy_file_Dir;

    //copyファイル格納ディレクトリ(ITA側)
    private $lv_ita_copy_files_Dir;                

    private $run_pattern_id;

    private $lv_objMTS;
    private $lv_objDBCA;

    // グローバル変数管理
    private $lva_global_vars_list;

    // ロール内のplaybookで定義されているcopy変数のリスト
    private $lva_cpf_vars_list = array();

    // ユーザー公開用データリレイストレージパス
    private $lv_user_out_Dir;
    // ユーザー公開用symphonyインスタンスストレージパス
    private $lv_symphony_instance_Dir;

    // 読替表のデータリスト
    private $translationtable_list;

    const LC_ANS_SSH_KEY_FILES_DIR    = "ssh_key_files";
    // ITAで管理している機器一覧のSSH秘密鍵ファイル格納先ディレクトリ
    const LC_ITA_SSH_KEY_FILE_PATH    = "/uploadfiles/2100000303/CONN_SSH_KEY_FILE";
    //ITAが管理している機器一覧のSSH秘密鍵ファイルパス
    const LC_ITA_SSH_KEY_FILE_DIR_MK  = "%s/%s/%s";
    //Ansible実行時のinディレクトリ配下のSSH秘密鍵ファイルパス
    const LC_IN_SSH_KEY_FILE_MK       = "%s/%s-%s";
    //Pioneer用 SSH秘密鍵ファイル用変数名
    const LC_ANS_SSH_KEY_FILE_VAR_NAME    = "__ssh_key_file__";
    //Pioneer用 ssh_extra_args変数名
    const LC_ANS_SSH_EXTRA_ARGS_VAR_NAME    = "__ssh_extra_args__";
    
    //Ansible実行時のinディレクトリ配下のSSH秘密鍵ファイル格納ディレクトリパス
    private $lv_Ansible_ssh_key_files_Dir;
    
    const LC_ANS_WIN_CA_FILES_DIR     = "winrm_ca_files";
    // ITAで管理している機器一覧のWinRMサーバー証明書ファイル格納先ディレクトリ
    const LC_ITA_WIN_CA_FILE_PATH     = "/uploadfiles/2100000303/WINRM_SSL_CA_FILE";
    //ITAが管理している機器一覧のWinRMサーバー証明書ファイルパス
    const LC_ITA_WIN_CA_FILE_DIR_MK  = "%s/%s/%s";
    //Ansible実行時のinディレクトリ配下のWinRMサーバー証明書ファイルパス
    const LC_IN_WIN_CA_FILE_MK       = "%s/%s-%s";

    //Ansible実行時のinディレクトリ配下のWinRMサーバー証明書格納ディレクトリパス
    private $lv_Ansible_win_ca_files_Dir;

    private  $lv_legacy_Role_cpf_vars_list;
    private  $lv_legacy_tpf_vars_list;
    private  $lv_legacy_cpf_vars_list;
    private  $lv_legacy_tfp_use_gbl_vars_list;
    private  $lv_legacy_parent_vars_list;

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   コンストラクタ
    // パラメータ
    //   $in_driver_id:    ドライバ区分
    //                        legacy:       DF_LEGACY_DRIVER_ID
    //                        pioneer:      DF_PIONEER_DRIVER_ID
    //   $in_ansible_ita_base_dir:  ansible作業用 NFSベースディレクトリ (ITA側)
    //   $in_ansible_ans_base_dir:  ansible作業用 NFSベースディレクトリ (Ansible側)
    //   $in_symphony_ans_base_dir: symphony NFSベースディレクトリ (Ansible側)
    //   $in_ita_child_playbook_dir:    
    //                      ITA側で管理している子PlayBook格納ディレクトリ
    //                      ※Pkeyyの直前のディレクトリ
    //   $in_ita_dialog_file_dir:    
    //                      ITA側で管理している対話ファイル格納ディレクトリ
    //                      ※Pkeyyの直前のディレクトリ
    //   $in_ita_template_file_dir
    //                      ITA側で管理しているテンプレートファイル格納ディレクトリ
    //                      ※Pkeyの直前のディレクトリ
    //   $in_ita_copy_file_dir
    //                      ITA側で管理しているコピーファイル格納ディレクトリ
    //                      ※Pkeyの直前のディレクトリ
    //   $in_ansible_vars_masterDB:
    //                      変数>管理テーブル テーブル名
    //   $in_ansible_vars_assignDB:
    //                      代入値管理テーブル テーブル名
    //   $in_ansible_pattern_vars_linkDB:
    //                      代入>変数名管理テーブル テーブル名
    //   $in_ansible_pho_linkDB:                
    //                      作業対象ホストテーブル テーブル名
    //   $in_ansible_master_fileDB:
    //                      素材管理テーブル テーブル名
    //   $in_ansible_master_file_pkeyIIEM:
    //                      素材管理テーブル 素材ID(pkey)項目名
    //   $in_ansible_master_file_nameITEM:
    //                      素材管理テーブル 素材ファイル項目名
    //   $in_ansible_pattern_linkDB:
    //                      作業パターン詳細 テーブル名
    //   $in_ansible_role_packageDB:
    //                      ロールパッケージ管理 テーブル名
    //   $in_ansible_roleDB:
    //                      ロール管理 テーブル名
    //   $in_ansible_role_varsDB:
    //                      ロール変数管理 テーブル名
    //   &$in_objMTS:       メッセージ定義クラス変数
    //   &$in_objDBCA:      データベースアクセスクラス変数
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function __construct($in_driver_id, 
                         $in_ansible_ita_base_dir,
                         $in_ansible_ans_base_dir,
                         $in_symphony_ans_base_dir,
                         $in_ita_child_playbook_dir,
                         $in_ita_dialog_file_dir,
                         $in_ita_template_file_dir,
                         $in_ita_pns_template_file_dir,
                         $in_ita_copy_file_dir,
                         $in_ansible_vars_masterDB,
                         $in_ansible_vars_assignDB,
                         $in_ansible_pattern_vars_linkDB,
                         $in_ansible_pho_linkDB,
                         $in_ansible_master_fileDB,
                         $in_ansible_master_file_pkeyITEM,
                         $in_ansible_master_file_nameITEM,
                         $in_ansible_pattern_linkDB,
                         $in_ansible_role_packageDB,
                         $in_ansible_roleDB,
                         $in_ansible_role_varsDB,
                         &$in_objMTS,&$in_objDBCA){
        global $root_dir_path;

        //Ansibleドライバ(legacy/pioneer)区分設定
        $this->setAnsibleDriverID($in_driver_id);

        //ansible用ベースディレクトリ
        $this->setAnsibleBaseDir('ANSIBLE_SH_PATH_ITA',$in_ansible_ita_base_dir);
        $this->setAnsibleBaseDir('ANSIBLE_SH_PATH_ANS',$in_ansible_ans_base_dir);
        $this->setAnsibleBaseDir('SYMPHONY_SH_PATH_ANS',$in_symphony_ans_base_dir);

        //ITA子PlayBook格納ディレクトリ
        $this->setITA_child_playbook_Dir($in_ita_child_playbook_dir);
        //ITA対話ファイル格納ディレクトリ
        $this->setITA_dialog_files_Dir($in_ita_dialog_file_dir);

        //ITAテンプレートファイル格納ディレクトリ
        $this->setITA_template_file_Dir($in_ita_template_file_dir);

        //ITAテンプレートファイル格納ディレクトリ(Pioneer用)
        $this->setITA_pns_template_file_Dir($in_ita_pns_template_file_dir);

        //ITAcopyファイル格納ディレクトリ
        $this->setITA_copy_file_Dir($in_ita_copy_file_dir);

        // 変数管理テーブルテーブル名
        $this->lv_ansible_vars_masterDB = $in_ansible_vars_masterDB;
        // 代入値管理テーブルテーブル名
        $this->lv_ansible_vars_assignDB = $in_ansible_vars_assignDB;
        // 作業パターン変数紐付管理テーブルテーブル名
        $this->lv_ansible_pattern_vars_linkDB = $in_ansible_pattern_vars_linkDB;
        // 作業対象ホストテーブル テーブル名
        $this->lv_ansible_pho_linkDB = $in_ansible_pho_linkDB;
        // 素材管理テーブル テーブル名
        $this->lv_ansible_master_fileDB = $in_ansible_master_fileDB;
        // 素材管理テーブル 素材ID(pkey)項目名
        $this->lv_ansible_master_file_pkeyITEM = $in_ansible_master_file_pkeyITEM;
        // 素材管理テーブル 素材ファイル項目名
        $this->lv_ansible_master_file_nameITEM = $in_ansible_master_file_nameITEM;

        // 作業パターン詳細 テーブル名
        $this->lv_ansible_pattern_linkDB    = $in_ansible_pattern_linkDB;
        // ロールパッケージ管理 テーブル名
        $this->lv_ansible_role_packageDB    = $in_ansible_role_packageDB;  
        // ロール管理 テーブル名
        $this->lv_ansible_roleDB            = $in_ansible_roleDB;
        // ロール変数管理 テーブル名
        $this->lv_ansible_role_varsDB       = $in_ansible_role_varsDB;

        $this->lv_ansible_child_varsDB      = "B_ANSIBLE_LRL_CHILD_VARS";

        //outディレクトリ
        $lv_Ansible_out_Dir = "";                   

        $this->lv_objMTS  = $in_objMTS;
        $this->lv_objDBCA = $in_objDBCA;

        $this->lv_legacy_Role_cpf_vars_list = array();
        $this->lv_legacy_tpf_vars_list      = array();
        $this->lv_legacy_cpf_vars_list      = array();
        $this->lv_legacy_tfp_use_gbl_vars_list = array();
        $this->lv_legacy_parent_vars_list      = array();

    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0027
    // 処理内容
    //   ansible用作業ディレクトリを作成する。
    //   ディレクトリ階層
    //   /ベースディレクトリ/ドライバ名/オケストレータID/作業実行番号/in
    //                                                               /out
    //                                                               /tmp
    // パラメータ
    //   $in_oct_id              オケストレータID
    //                             legacy     : ns
    //                             pioneer    : ns
    //                             legacy-Role: rl
    //   $in_execno              作業実行番号
    // 
    // 戻り値
    //   array:  キー[0]～[5]：各種ディレクトリパス 
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsibleWorkingDirectories($in_oct_id,$in_execno){
        $aryRetAnsibleWorkingDir = array();
        
        $base_dir = $this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ITA');

        //ベースディレクトリの存在チェック
        if( !is_dir( $base_dir ) ){
            //ベースディレクトリが存在しない場合はエラー
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55201");
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        
        $driver_id = $this->getAnsibleDriverID();
        switch($driver_id){
        case DF_LEGACY_DRIVER_ID:

        // Legacy-Role対応
        case DF_LEGACY_ROLE_DRIVER_ID:

            $c_dir_per_ans_orc_type_id = $base_dir . "/" . self::LC_ANS_LEGACY_DIR;
            break;
        case DF_PIONEER_DRIVER_ID:
            $c_dir_per_ans_orc_type_id = $base_dir . "/" . self::LC_ANS_PIONEER_DIR;
            break;
        default:
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55200",array(basename(__FILE__) . "-" . __LINE__)); //"内部処理異常  FILE:｛｝  LINE:｛｝が見つからない。"
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        
        // 返し値[0] ドライバ区分付
        $aryRetAnsibleWorkingDir[0] = $c_dir_per_ans_orc_type_id;
        
        // 返し値[1] ドライバ区分+オケストレータID付き
        $c_dir_per_orc_id           = $c_dir_per_ans_orc_type_id . "/" . $in_oct_id;
        $aryRetAnsibleWorkingDir[1] = $c_dir_per_orc_id;
        
        // 返し値[2] ドライバ区分+オケストレータID付き+作業実行番号付き
        $c_dir_per_exe_no           = $c_dir_per_orc_id . "/" . sprintf("%010s",$in_execno);
        $aryRetAnsibleWorkingDir[2] = $c_dir_per_exe_no;
        
        // 返し値[3] ドライバ区分+オケストレータID付き+作業実行番号付き+inフォルダ名
        $c_dir_in_per_exe_no        = $c_dir_per_exe_no . "/" . self::LC_ANS_IN_DIR;
        $aryRetAnsibleWorkingDir[3] = $c_dir_in_per_exe_no;
        
        // 返し値[4] ドライバ区分+オケストレータID付き+作業実行番号付き+outフォルダ名
        $c_dir_out_per_exe_no       = $c_dir_per_exe_no . "/" . self::LC_ANS_OUT_DIR;
        $aryRetAnsibleWorkingDir[4] = $c_dir_out_per_exe_no;
        
        return $aryRetAnsibleWorkingDir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   ansible用作業ディレクトリを作成する。
    //   ディレクトリ階層
    //   /ベースディレクトリ/ドライバ名/オケストレータID/作業実行番号/in
    //                                                               /out
    //                                                               /tmp
    // パラメータ
    //   $in_oct_id              オケストレータID
    //                             legacy:      ns
    //                             pioneer:     ns
    //                             legacy-Role: rl
    //   $in_execno              作業実行番号
    //   $in_hostaddress_type    ホストアドレス方式
    //                           null or 1:IP方式  2:ホスト名方式
    //   $in_winrm_id            対象ホストがwindowsかを判別
    //                           1: windows 他:windows以外
    //   $in_zipdir              Legacy-Role パッケージファイルディレクトリ
    //                           ※Legacy-Role時のみ必須
    //   $in_pattern_id          作業パターンID
    //                           ※Legacy-Role時のみ必須
    //   $ina_rolenames          Legacy-Role role名リスト
    //                           ※Legacy-Role時のみ必須
    //                             $ina_rolename[role名]
    //   $ina_rolevars           Legacy-Role role内変数リスト
    //                           ※Legacy-Role時のみ必須
    //                             $ina_rolevars[role名][変数名]=0
    //   $ina_roleglobalvars     Legacy-Role role内グローバル変数リスト
    //                           ※Legacy-Role時のみ必須
    //                             $ina_roleglobalvars[role名][グローバル変数名]=0
    //   $in_role_rolepackage_id ロールパッケージ管理 Pkey 返却
    //                           ※Legacy-Role時のみ必須
    //   $ina_def_vars_list:     各ロールのデフォルト変数ファイル内に定義されている変数リスト
    //   $ina_def_array_vars_list:  各ロールのデフォルト変数ファイル内に定義されている多次元変数の情報
    //   $in_symphony_instance_no:  symphonyから起動された場合のsymphonyインスタンスID
    //                              作業実行の場合は空白
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateAnsibleWorkingDir($in_oct_id,
                                     $in_execno,
                                     $in_hostaddress_type,
                                     $in_winrm_id,
                                     $in_zipdir             = "",
                                     $in_pattern_id         = "",
                                     &$ina_rolenames        = "",
                                     &$ina_rolevars         = "",  
                                     &$ina_roleglobalvars   = "",
                                     &$in_role_rolepackage_id  = "",
                                     &$ina_def_vars_list,
                                     &$ina_def_array_vars_list,
                                     $in_symphony_instance_no
                                     ){
        $this->run_pattern_id = $in_pattern_id;

        // null or 1:IP方式  2:ホスト名方式
        $this->lv_hostaddress_type = $in_hostaddress_type;


        // 対象ホストがwindowsかを判別
        // 1: windows 他:windows以外
        $this->lv_winrm_id  = $in_winrm_id;

        //ドライバ区分ディレクトリ作成
        $aryRetAnsibleWorkingDir = $this->getAnsibleWorkingDirectories($in_oct_id,$in_execno);

        if( $aryRetAnsibleWorkingDir === false ){
            return false;
        }

        $c_dir = $aryRetAnsibleWorkingDir[0];
        if( !is_dir( $c_dir ) ){
            //ドライバ区分ディレクトリが存在している場合はなにもしない
            if( !mkdir( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }

        //オーケストラ区分ディレクトリ作成
        $c_dir = $aryRetAnsibleWorkingDir[1];

        if( !is_dir( $c_dir ) ){
            //オーケストラ区分ディレクトリが存在している場合はなにもしない
            if( !mkdir( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }


        //作業実行番号用ディレクトリ作成
        $c_dir = $aryRetAnsibleWorkingDir[2];

        system('/bin/rm -rf ' . $c_dir . ' >/dev/null 2>&1');

        if( is_dir( $c_dir ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55238",array($in_execno,$c_dir));
            //作業実行番号用ディレクトリが存在している場合はエラー
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        else{
            if( !mkdir( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }

        // outディレクトリ作成
        //$c_outdir = $c_dir . "/" . self::LC_ANS_OUT_DIR;
        $c_outdir = $aryRetAnsibleWorkingDir[4];
        if( !mkdir( $c_outdir, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_outdir, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // outディレクトリ名を記憶
        $this->setAnsible_out_Dir($c_outdir);
    
        // ユーザー公開用データリレイストレージパス
        $user_out_Dir = $c_outdir . "/" . self::LC_ANS_OUTDIR_DIR;

        if( !mkdir( $user_out_Dir , 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $user_out_Dir , 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
     
        // ホスト変数定義ファイルに記載するパスなのでAnsible側のストレージパスに変更
        $this->lv_user_out_Dir = str_replace($this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ITA'),
                                             $this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ANS'),
                                             $user_out_Dir);

        // symphonyからの起動か判定
        if(strlen($in_symphony_instance_no) != 0) {
            // ユーザー公開用symphonyインスタンス作業用 データリレイストレージパス
            $this->lv_symphony_instance_Dir = $this->getAnsibleBaseDir('SYMPHONY_SH_PATH_ANS') . "/" . sprintf("%010s",$in_symphony_instance_no);
        }
        else
        {
            $this->lv_symphony_instance_Dir = $this->lv_user_out_Dir;
        }

        //inディレクトリ作成
        $c_indir = $aryRetAnsibleWorkingDir[3];
        
        if( !mkdir( $c_indir, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_indir, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        
        // INディレクトリ名を記憶
        $this->setAnsible_in_Dir($c_indir);
    
        // ドライバ区分がLEGACYの場合にchild_playbooksディレクトリ作成
        if ($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID){
            //child_playbooksディレクトリ作成
            $c_dirwk = $c_indir . "/" . self::LC_ANS_CHILD_PLAYBOOKS_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
    
            // child_playbooksディレクトリ名を記憶
            $this->setAnsible_child_playbooks_Dir($c_dirwk);
            // PlayBook内 子PlayBookパスを記憶
            $this->setPlaybook_child_playbooks_Dir(self::LC_ANS_CHILD_PLAYBOOKS_DIR);
        }

        // ドライバ区分がLEGACYまたはPioneerの場合にtemplate_filesディレクトリを作成する。
        if (($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
            ($this->getAnsibleDriverID() == DF_PIONEER_DRIVER_ID)){
            //template_filesディレクトリ作成
            $c_dirwk = $c_indir . "/" . self::LC_ANS_TEMPLATE_FILES_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            // template_filesディレクトリ名を記憶
            $this->setAnsible_template_files_Dir($c_dirwk);
            // ホスト変数ファイル内 template_filesディレクトリパスを記憶
            $this->setHostvarsfile_template_file_Dir(self::LC_ANS_TEMPLATE_FILES_DIR);
        }
        // ドライバ区分がLEGACYかPioneer、ROLEの場合にcopy_filesディレクトリを作成する。
        if(($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
           ($this->getAnsibleDriverID() == DF_PIONEER_DRIVER_ID) ||
           ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID)){
            //copy_filesディレクトリ作成
            $c_dirwk = $c_indir . "/" . self::LC_ANS_COPY_FILES_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            // copy_filesディレクトリ名を記憶
            $this->setAnsible_copy_files_Dir($c_dirwk);
            // ホスト変数ファイル内 copy_filesディレクトリパスを記憶
            $this->setHostvarsfile_copy_file_Dir(self::LC_ANS_COPY_FILES_DIR);
        }

        //ssh_key_filesディレクトリ作成
        $c_dirwk = $c_indir . "/" . self::LC_ANS_SSH_KEY_FILES_DIR;
        if( !mkdir( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // ssh_key_filesディレクトリ名を記憶
        $this->setAnsible_ssh_key_files_Dir($c_dirwk);

        //win_ca_filesディレクトリ作成
        $c_dirwk = $c_indir . "/" . self::LC_ANS_WIN_CA_FILES_DIR;
        if( !mkdir( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // win_ca_fileディレクトリ名を記憶
        $this->setAnsible_win_ca_files_Dir($c_dirwk);

        // ドライバ区分がPIONEERの場合にdialog_filesディレクトリ作成
        if ($this->getAnsibleDriverID() == DF_PIONEER_DRIVER_ID){
            $c_dirwk = $c_indir . "/" . self::LC_ANS_DIALOG_FILES_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
    
            // dialog_filesディレクトリ名を記憶
            $this->setAnsible_dialog_files_Dir($c_dirwk);
        }

        // ドライバ区分がLegacy-Roleの場合
        // 作業パターンIDに紐づくパッケージファイルを取得
        // パッケージファイルをZIPファイルをinディレクトリに解凍し
        // 不要なファイルを削除する。
        if ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID){
            // 作業パターンIDに紐づくパッケージファイルを取得
            // $in_role_rolepackage_idと$role_package_fileは返却される。
            $ret = $this->getRolePackageFile($in_pattern_id,$in_role_rolepackage_id,$role_package_file);
            if($ret === false){
                return false;
            }
            $roleObj = new CheckAnsibleRoleFiles($this->lv_objMTS);

            // ロールパッケージファイル名(ZIP)を取得
            $zipfile = $this->getAnsible_RolePackage_file($in_zipdir,$in_role_rolepackage_id,$role_package_file);

            // ロールパッケージファイル名(ZIP)の存在確認
            if( file_exists($zipfile) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70008",array($pkey,basename($zipfile))); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // inディレクトリにロールパッケージファイル(ZIP)展開
            if($roleObj->ZipextractTo($zipfile,
                                      $this->getAnsible_in_Dir()) === false){
                $arryErrMsg = $roleObj->getlasterror();
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$arryErrMsg[0]);
                return false;
            }
            else{
                // ローカル変数のリスト作成
                $system_vars = array();
                $system_vars[] = self::LC_ANS_PROTOCOL_VAR_NAME;
                $system_vars[] = self::LC_ANS_USERNAME_VAR_NAME;
                $system_vars[] = self::LC_ANS_PASSWD_VAR_NAME;
                $system_vars[] = self::LC_ANS_LOGINHOST_VAR_NAME;

                // ユーザー公開用 データリレイストレージパス 変数の名前
                $system_vars[] = self::LC_ANS_OUTDIR_VAR_NAME;

                // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
                $system_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

                $ina_def_vars_list = array();
                $err_vars_list = array();
                $def_varsval_list = array();

                // ロール内のplaybookで定義されているcopy変数のリスト
                $this->lva_cpf_vars_list = array();

                $ITA2User_var_list = array();
                $User2ITA_var_list = array();
                $comb_err_vars_list = array();

                // $this->lva_cpf_vars_listの構造
                // $lva_cpf_vars_list[ロール名][ロール名/--/Playbook名][行番号][変数名] = 1
                $ret = $roleObj->chkRolesDirectory($this->getAnsible_in_Dir(),
                                               $system_vars,
                                               "",
                                               $ina_def_vars_list,$err_vars_list,
                                               $def_varsval_list,
                                               $ina_def_array_vars_list,
                                               // ロール内のplaybookからcopy変数を抽出する。
                                               true,
                                               $this->lva_cpf_vars_list,
                                               $ITA2User_var_list,
                                               $User2ITA_var_list,
                                               $comb_err_vars_list,
                                               true);


                if($ret === false){
                    // ロール内の読替表で読替変数と任意変数の組合せが一致していない
                    if(@count($comb_err_vars_list) !== 0){
                        $msgObj = new DefaultVarsFileAnalysis($g['objMTS']);
                        $strErrMsg  = $msgObj->TranslationTableCombinationErrmsgEdit(false,$comb_err_vars_list);
                        unset($msgObj);
                        return false;
                    }

                    // defaults定義ファイルに定義されている変数で属性が違う変数がある場合
                    else if(@count($err_vars_list) !== 0){
                        // エラーメッセージ編集
                        $msgObj = new DefaultVarsFileAnalysis($g['objMTS']);
                        $strErrMsg  = $msgObj->VarsStructErrmsgEdit($err_vars_list);
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$strErrMsg);
                        unset($msgObj);
                        return false;
                    }
                    else{
                        $arryErrMsg = $roleObj->getlasterror();
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$arryErrMsg[0]);
                        return false;
                    }
                }

                // 紐づけされていないロールで使用しているCopy変数を
                // $this->lva_cpf_vars_listから取り除く
                $w_RoleInfoList = array();
                $w_RoleNameList = array();
                $this->getDBLegactRoleList($in_pattern_id,$w_RoleInfoList);
                foreach($w_RoleInfoList as $no=>$array1) {
                    foreach($array1 as $keyno=>$w_rolename) {
                        $w_RoleNameList[$w_rolename] = 0;
                    }
                }
                foreach($this->lva_cpf_vars_list as $w_rolename=>$array1) {
                    if( ! isset($w_RoleNameList[$w_rolename]) ) {
                         unset($this->lva_cpf_vars_list[$w_rolename]);
                    }
                }

                // copy変数がファイル管理に登録されているか判定
                $strErrMsg = "";;
                $strErrDetailMsg = "";
                $objLibs = new AnsibleCommonLibs();

                // $this->lva_cpf_vars_listの構造 CONTENTS_FILE_ID/CONTENTS_FILEはchkCPFVarsMasterRegの戻り値
                // $lva_cpf_vars_list[ロール名][ロール名/--/Playbook名][行番号][変数名]['CONTENTS_FILE_ID'] = Pkey
                // $lva_cpf_vars_list[ロール名][ロール名/--/Playbook名][行番号][変数名]['CONTENTS_FILE'] = ファイル名
                $ret = $objLibs->chkCPFVarsMasterReg($this->lv_objMTS,$this->lv_objDBCA,
                                                     $this->lva_cpf_vars_list,
                                                     $strErrMsg,$strErrDetailMsg);
                unset($objLibs);
                if($ret === false){
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$strErrMsg);
                    if($strErrDetailMsg != ""){
                        $this->DebugLogPrint(basename(__FILE__),__LINE__,$strErrDetailMsg);
                    }
                    return false;
                }

                // ロール名取得
                // $ina_rolename[role名]
                $ina_rolenames = $roleObj->getrolename();
                // ロール内の変数取得
                // $ina_varname[role名][変数名]=0
                $ina_rolevars  = $roleObj->getvarname();
                
                // ロール内のグローバル変数取得
                $ina_roleglobalvars  = $roleObj->getglobalvarname();

            }
            unset($roleObj);

            // 展開先にhostsファイルがあれば削除する。
            $wk_dir = $c_indir . "/" . self::LC_ANS_HOSTS_FILE;
            if( file_exists($wk_dir) === true ){
                exec("/bin/rm -f " . $wk_dir);
            }

            // 展開先にホスト変数ディレクトリがあれば削除する。
            $wk_dir = $c_indir . "/" . self::LC_ANS_HOST_VARS_DIR;
            if( file_exists($wk_dir) === true ){
                exec("/bin/rm -rf " . $wk_dir);
            }

            // 展開先にホストグループ変数ディレクトリがあれば削除する。
            $wk_dir = $c_indir . "/" . self::LC_ANS_GROUP_VARS_DIR;
            if( file_exists($wk_dir) === true ){
                exec("/bin/rm -rf " . $wk_dir);
            }
        }

        // host_varsディレクトリ作成
        $c_dirwk = $c_indir . "/" . self::LC_ANS_HOST_VARS_DIR;
        if( !mkdir( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_dirwk, 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
    
        // host_varsディレクトリ名を記憶
        $this->setAnsible_host_vars_Dir($c_dirwk);
    
        // ドライバ区分がPIONEERの場合にPIONEER用作業ディレクトリ作成
        if ($this->getAnsibleDriverID() == DF_PIONEER_DRIVER_ID){
            $c_tmpdir = $c_dir . "/" . self::LC_ANS_TMP_DIR;
            if( !mkdir( $c_tmpdir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_tmpdir, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
    
            // tmpディレクトリ名を記憶
            $this->setAnsible_tmp_Dir($c_tmpdir);
    
            // original_dialog_filesディレクトリ作成
            $c_dirwk = $c_tmpdir . "/" . self::LC_ANS_ORG_DIALOG_FILES_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
    
            // original_dialog_filesディレクトリ名を記憶
            $this->setAnsible_original_dialog_files_Dir($c_dirwk);

            // original_host_varsディレクトリ作成
            $c_dirwk = $c_tmpdir . "/" . self::LC_ANS_ORG_HOST_VARS_DIR;
            if( !mkdir( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dirwk, 0777 ) ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
    
            // original_hosts_varsディレクトリ名を記憶
            $this->setAnsible_original_hosts_vars_Dir($c_dirwk);
    
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   ansible用各作業ファイルを作成する。
    // 
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=[ホスト名(IP)]
    //
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //                          ※Legacyの場合のみ必須
    //
    //   $ina_dialog_files:     対話ファイル配列
    //                          [ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
    //                          ※Pioneerの場合のみ必須
    //
    //   $ina_rolenames:        ロール名リスト配列(データベースの登録内容)
    //                          ※Legacy-Roleの場合のみ必須
    //                          [実行順序][ロールID(Pkey)]=>ロール名
    //
    //   $ina_role_rolenames:   ロール名リスト配列(Role内登録内容)
    //                          ※Legacy-Roleの場合のみ必須
    //                          [ロール名]
    //
    //   $ina_role_rolevars:    ロール内変数リスト配列(Role内登録内容)
    //                          ※Legacy-Roleの場合のみ必須
    //                          [ロール名][変数名]=0
    //
    //   $ina_role_roleglobalvars:  ロール内グローバル変数リスト配列(Role内登録内容)
    //                              ※Legacy-Roleの場合のみ必須
    //                              [ロール名][グローバル変数名]=0
    //
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //   既存のデータが重なるが、今後の開発はこの変数を使用する。
    //   $ina_hostinfolist:     機器一覧ホスト情報配列
    //                          [ホスト名(IP)]=HOSTNAME=>''             ホスト名
    //                                         PROTOCOL_ID=>''          接続プロトコル
    //                                         LOGIN_USER=>''           ログインユーザー名
    //                                         LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
    //                                                                  1:管理(●)   0:未管理
    //                                         LOGIN_PW=>''             パスワード
    //                                                                  パスワード管理が1の場合のみ有効
    //                                         LOGIN_AUTH_TYPE=>''      Ansible認証方式
    //                                                                  2:パスワード認証 1:鍵認証
    //                                         WINRM_PORT=>''           WinRM接続プロトコル
    //                                         OS_TYPE_ID=>''           OS種別
    //
    //   $ina_host_child_vars:  配列変数一覧返却配列(変数一覧に配列変数含む)
    //                          [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //   $ina_DB_child_vars_master: 
    //                          メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                          [変数名][メンバー変数名]=0
    //   $in_exec_mode:         実行エンジン
    //                           1: Ansible  2: Ansible Tower
    //   $in_exec_playbook_hed_def; 親playbookヘッダセクション
    //   $in_exec_option:       予約
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateAnsibleWorkingFiles($ina_hosts,
                                       $ina_host_vars,
                                       $ina_child_playbooks,
                                       $ina_dialog_files,
                                       $ina_rolenames,
                                       $ina_role_rolenames,
                                       $ina_role_rolevars,
                                       $ina_role_roleglobalvars,
                                       $ina_hostprotcollist,
                                       $ina_hostinfolist,
                                       $ina_host_child_vars,
                                       $ina_DB_child_vars_master,
                                       $ina_MultiArray_vars_list,
                                       $ina_def_vars_list,
                                       $ina_def_array_vars_list,
                                       $in_exec_mode,
                                       $in_exec_playbook_hed_def,
                                       $in_exec_option)
    {

        //////////////////////////////////////
        // グローバル変数管理よりグローバル変数を取得
        //////////////////////////////////////
        $this->lva_global_vars_list = array();
        $ret = $this->getDBGlobalVarsMaster($this->lva_global_vars_list);
        if($ret = false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90235");
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        //////////////////////////////////////
        // 読替表のデータを取得する。
        //////////////////////////////////////
        $this->translationtable_list = array();
        $ret = $this->getDBTranslationTable($this->run_pattern_id,$this->translationtable_list);
        if($ret === false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000011");
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }              

        //////////////////////////////////////
        // hostsファイル作成                //
        //////////////////////////////////////
        $pioneer_sshkeyfilelist   = array();
        $pioneer_sshextraargslist = array();
        // #0001 hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を渡す。
        if ( $this->CreateHostsfile("hostgroups",$ina_hosts,$ina_hostprotcollist,
                                    $ina_hostinfolist,
                                    $pioneer_sshkeyfilelist,
                                    $pioneer_sshextraargslist
                                    ) === false){
            return false;
        }
        //////////////////////////////////////
        // ホスト変数定義ファイル作成       //
        //////////////////////////////////////
        //ドライバ区分を判定
        switch($this->getAnsibleDriverID()){
        case DF_LEGACY_DRIVER_ID:
        case DF_PIONEER_DRIVER_ID:
            // host_varsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を渡す。
            if ( $this->CreateHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                            $ina_host_child_vars,$ina_DB_child_vars_master) === false)
            {
                return false;
            }
            break;
        case DF_LEGACY_ROLE_DRIVER_ID:
            // Role専用のモジュールでホスト変数定義ファイル作成
            if ( $this->CreateRoleHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                                $ina_MultiArray_vars_list,$ina_def_vars_list,$ina_def_array_vars_list) === false){
                return false;
            }        

            //////////////////////////////////////////////////////////////////////////
            // Role内で使用しているcopyモジュール変数をホスト変数定義ファイルに追加 //
            //////////////////////////////////////////////////////////////////////////
            if( $this->CreateLegacyRoleCopyFiles($ina_hosts,$ina_hostprotcollist,$ina_rolenames,$this->lva_cpf_vars_list) === false){
                return false;
            }

            break;
        }
        //ドライバ区分を判定
        switch($this->getAnsibleDriverID()){
        case DF_LEGACY_DRIVER_ID:
            //////////////////////////////////////
            // Legacy PlayBookファイル作成      //
            //////////////////////////////////////
            if ( $this->CreateLegacyPlaybookfiles($ina_child_playbooks,$in_exec_mode,$in_exec_playbook_hed_def) === false){
                return false;
            }
            /////////////////////////////////////////////////
            // 子Playbookのフォーマットと変数定義チェック  //
            /////////////////////////////////////////////////
            if ( $this->CheckLegacyPlaybookfiles($ina_hosts,$ina_host_vars,$ina_child_playbooks) === false){
                return false;
            }
            ////////////////////////////////////////////////
            // 子Playbook内のtemplateモジュールをチェック //
            ////////////////////////////////////////////////
            // ホスト変数配列追加
            if( $this->CreateLegacytemplatefiles($ina_hosts,$ina_child_playbooks,$ina_hostprotcollist,$ina_host_vars) === false)
            {
                return false;
            }

            ////////////////////////////////////////////////
            // 子Playbook内のcopyモジュールをチェック     //
            ////////////////////////////////////////////////
            if( $this->CreateLegacyCopyFiles($ina_hosts,$ina_child_playbooks,$ina_hostprotcollist,$ina_host_vars) === false){
                return false;
            }

            ////////////////////////////////////////////////
            // ホスト変数ファイルにグローバル変数・コピー変数
            // テンプレート変数の情報を出力する。
            ////////////////////////////////////////////////
            if( $this->CommitHostVarsfiles($ina_hosts,$ina_hostprotcollist,$ina_host_vars) === false){
                return false;
            }
            break;

        case DF_PIONEER_DRIVER_ID:
            //////////////////////////////////////
            // Pionner 対話ファイル作成         //
            //////////////////////////////////////
            if ( $this->CreatePioneerDialogfiles($ina_dialog_files,$ina_host_vars,$ina_hostprotcollist,
                                                 $pioneer_sshkeyfilelist,
                                                 $pioneer_sshextraargslist,
                                                 $in_exec_mode
                                                ) === false){
                return false;
            }
            //////////////////////////////////////////////////
            // 対話ファイル内のtemplateモジュールをチェック //
            //////////////////////////////////////////////////
            if( $this->CreatePioneertemplatefiles($ina_hosts,$ina_dialog_files,$ina_hostprotcollist,$ina_host_vars) === false)
            {
                return false;
            }
            ////////////////////////////////////////////////
            // 対話ファイル内のcopyモジュールをチェック   //
            ////////////////////////////////////////////////
            if( $this->CreatePioneerCopyFiles($ina_hosts,$ina_dialog_files,$ina_hostprotcollist,$ina_host_vars) === false){
                return false;
            }
            /////////////////////////////////////////////////
            // 対話ファイルのフォーマットと変数定義チェック//
            /////////////////////////////////////////////////
            if ( $this->CheckPioneerPlaybookfiles($ina_hosts,$ina_host_vars,$ina_dialog_files,$ina_hostprotcollist) === false){
                return false;
            }
            break;
        case DF_LEGACY_ROLE_DRIVER_ID:
            //////////////////////////////////////
            // Legacy-Role PlayBookファイル作成 //
            //////////////////////////////////////
            if ( $this->CreateLegacyRolePlaybookfiles($ina_rolenames,$in_exec_mode,$in_exec_playbook_hed_def) === false){
                return false;
            }
            /////////////////////////////////////////////////
            // ロール内の変数定義チェック                  //
            /////////////////////////////////////////////////
            if ( $this->CheckLegacyRolePlaybookfiles($ina_hosts,
                                                     $ina_host_vars,
                                                     $ina_rolenames,
                                                     $ina_role_rolenames,
                                                     $ina_role_rolevars,
                                                     $ina_role_roleglobalvars) === false){
                return false;
            }
            break;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   hostsファイルを作成する。
    // パラメータ
    //   $in_group_name:        ホストグループ名
    //   $ina_hosts:            ホスト名(IPアドレス)の配列
    //                          $ina_hosts[ホスト名(IP)]
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //   $ina_hostinfolist:     機器一覧ホスト情報配列
    //                          [ホスト名(IP)]=HOSTNAME=>''             ホスト名
    //                                         PROTOCOL_ID=>''          接続プロトコル
    //                                         LOGIN_USER=>''           ログインユーザー名
    //                                         LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
    //                                                                  1:管理(●)   0:未管理
    //                                         LOGIN_PW=>''             パスワード
    //                                                                  パスワード管理が1の場合のみ有効
    //                                         LOGIN_AUTH_TYPE=>''      Ansible認証方式
    //                                                                  2:パスワード認証 1:鍵認証
    //                                         WINRM_PORT=>''           WinRM接続プロトコル
    //                                         OS_TYPE_ID=>''           OS種別
    //                                         SYSTEM_ID=>''            機器一覧主キー
    //                                         SSH_KEY_FILE=>''         SSH秘密鍵ファイル
    //   $ina_sshkeyfilelist:             SSHSSH秘密鍵ファイルリスト(pioneer専用)
    //   $ina_pioneer_sshextraargslist:   SSH_EXTRA_ARGSリスト(pioneer専用)
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // #0001 hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を貰う
    function CreateHostsfile($in_group_name,$ina_hosts,$ina_hostprotcollist,
                             $ina_hostinfolist,
                            &$ina_pioneer_sshkeyfilelist,
                            &$ina_pioneer_sshextraargslist) 
    {

        $ina_pioneer_sshkeyfilelist   = array();
        $ina_pioneer_sshextraargslist = array();

        $file_name = $this->getAnsible_hosts_file();
        $fd = @fopen($file_name, "w");
        if($fd == null){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55204",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
    
        $value = "[" . $in_group_name . "]\n";
        if( @fputs($fd, $value) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55205",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        foreach( $ina_hosts as $host_name ){
            $ssh_extra_args = "";
            // ssh_extra_argsの設定の有無を判定しssh_extra_argsの内容を退避
            if(strlen(trim($ina_hostinfolist[$host_name]['SSH_EXTRA_ARGS'])) != 0){
                $ssh_extra_args = trim($ina_hostinfolist[$host_name]['SSH_EXTRA_ARGS']);
                // "を\"に置き換え
                $ssh_extra_args = str_replace('"','\\"',trim($ina_hostinfolist[$host_name]['SSH_EXTRA_ARGS']));
                if(($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
                   ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID)){
                    // hostsファイルに追加するssh_extra_argsを生成
                    $ssh_extra_args = ' ansible_ssh_extra_args="' . $ssh_extra_args . '"';
                }
                else{
                    // Pioneer用にssh_extra_argsを退避
                    $ina_pioneer_sshextraargslist[$host_name] = $ssh_extra_args;
                    $ssh_extra_args = "";
                }
            }

            $hosts_extra_args = "";
            // hosts_extra_argsの設定の有無を判定しhosts_extra_argsの内容を退避
            if(strlen(trim($ina_hostinfolist[$host_name]['HOSTS_EXTRA_ARGS'])) != 0){
                $hosts_extra_args = trim($ina_hostinfolist[$host_name]['HOSTS_EXTRA_ARGS']);
            }

            $win_param = "";

            // legacyの場合で対象ホストがwindowsの場合またはパスワード認証か判定
            if( (($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
                 ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID)) &&
                 // 対象ホストがwindowsの場合
                (($this->lv_winrm_id == 1) ||
                 // パスワード認証の場合
                 ($ina_hostinfolist[$host_name]['LOGIN_AUTH_TYPE'] == self::LC_LOGIN_AUTH_TYPE_PW)) ){
                // sshの接続パラメータを作成する。
                // ユーザー名
                $win_param = $win_param . " ansible_ssh_user=" . $ina_hostinfolist[$host_name]['LOGIN_USER'];
                // パスワードが設定されているか(windowsの場合に有効)
                // パスワード
                if($ina_hostinfolist[$host_name]['LOGIN_PW'] != self::LC_ANS_UNDEFINE_NAME)
                {
                    $win_param = $win_param . " ansible_ssh_pass=" . $ina_hostinfolist[$host_name]['LOGIN_PW'];
                }
                // 対象ホストがwindowsの場合
                if($this->lv_winrm_id == 1){
                    // WINRM接続プロトコルよりポート番号取得
                    $win_param = $win_param . " ansible_ssh_port=" 
                                              . $ina_hostinfolist[$host_name]['WINRM_PORT'];
                    $win_param = $win_param . " ansible_connection=winrm";
                }
            }

            $ssh_key_file = '';
            // 認証方式が鍵認証でWinRM接続でないか判定
            if(($ina_hostinfolist[$host_name]['LOGIN_AUTH_TYPE'] == self::LC_LOGIN_AUTH_TYPE_KEY ) &&
               ($this->lv_winrm_id != 1)){
                if(strlen(trim($ina_hostinfolist[$host_name]['SSH_KEY_FILE'])) != 0){
                    // 機器一覧にSSH鍵認証ファイルが登録されている場合はSSH鍵認証ファイルをinディレクトリ配下にコピーする。
                    $ret = $this->CreateSSH_key_file($ina_hostinfolist[$host_name]['SYSTEM_ID'],
                                                     $ina_hostinfolist[$host_name]['SSH_KEY_FILE'],
                                                     $ssh_key_file_path);

                    if($ret === false){
                        return false;
                    }
                    if(($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
                       ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID)){
                        // hostsファイルに追加するSSH鍵認証ファイルのパラメータ生成
                        $ssh_key_file = ' ansible_ssh_private_key_file=' . $ssh_key_file_path;
                    }
                    else{
                        $ina_pioneer_sshkeyfilelist[$host_name]=$ssh_key_file_path;
                    }
                }
            }

            $win_ca_file = '';
            // WinRM接続か判定
            if($this->lv_winrm_id == 1){
                if(strlen(trim($ina_hostinfolist[$host_name]['WINRM_SSL_CA_FILE'])) != 0){
                    // 機器一覧にサーバー証明書ファイルが登録されている場合はサーバー証明書ファイルをinディレクトリ配下にコピーする
                    $ret = $this->CreateWIN_cs_file($ina_hostinfolist[$host_name]['SYSTEM_ID'],
                                                    $ina_hostinfolist[$host_name]['WINRM_SSL_CA_FILE'],
                                                    $win_ca_file_path);

                    if($ret === false){
                        return false;
                    }
                    if(($this->getAnsibleDriverID() == DF_LEGACY_DRIVER_ID) ||
                       ($this->getAnsibleDriverID() == DF_LEGACY_ROLE_DRIVER_ID)){
                        // hostsファイルに追加するサーバー証明書ファイルのパラメータ生成
                        $win_ca_file = ' ansible_winrm_ca_trust_path=' . $win_ca_file_path;
                    }
                }
            }

            // ホストアドレス方式がホスト名方式の場合はホスト名をhostsに登録する。
            if($this->lv_hostaddress_type == 2){
                $host_name = $ina_hostinfolist[$host_name]['HOSTNAME'] . $win_param . ' ' .  $ssh_key_file . ' ' . $ssh_extra_args . ' ' . $hosts_extra_args . ' ' . $win_ca_file . "\n";
            }
            else{
                // ホストアドレス方式がIPアドレスの場合
                $host_name = $ina_hostinfolist[$host_name]['HOSTNAME'] . ' ansible_ssh_host=' . $host_name . ' ' .  $win_param . ' ' . $ssh_key_file . ' ' . $ssh_extra_args . ' ' . $hosts_extra_args . ' ' . $win_ca_file . "\n";
            }

            if( @fputs($fd, $host_name) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55205",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        if( @fclose($fd) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55205",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004-1
    // 処理内容
    //   ホスト変数ファイルを作成する。(Role専用)
    // 
    // パラメータ
    //   $ina_host_vars:        ホスト変数配列
    //                          [ipaddress][ 変数名 ]=>具体値
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を貰う
    function CreateRoleHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                     $ina_MultiArray_vars_list,$def_vars_list,$ina_def_array_vars_list)
    {
        // ホスト変数配列よりホスト名(IP)を取得
        $host_list = array_keys($ina_host_vars);

        // ホスト変数配列のホスト)分繰返し
        foreach( $host_list as $host_name){
            foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
            $host_vars_file = $hostname;
            //ドライバ区分を判定しホスト変数定義ファイル名を取得
            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_ROLE_DRIVER_ID:
                // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
                //LEGACY用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_host_var_file($host_vars_file);
                break;
            default:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55200",array(basename(__FILE__) . "-" . __LINE__)); //"内部処理異常  FILE:｛｝  LINE:｛｝が見つからない。"
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            //該当ホストの変数配列を取得
            $vars_list = $ina_host_vars[$host_name];


            $chl_vars_list = array();

            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_ROLE_DRIVER_ID:
                //該当ホストの配列変数を取得
                if(@count($ina_host_child_vars[$host_name]) != 0){
                    $chl_vars_list = $ina_host_child_vars[$host_name];
                }
                break;
            }

            // ホスト変数定義ファイル作成
            if($this->CreateRoleHostvarsfile("VAR",
                                             $file_name,$vars_list,
                                             $ina_MultiArray_vars_list,$def_vars_list,$ina_def_array_vars_list,
                                             $host_name
                                             ) === false)
            {
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004-2
    // 処理内容
    //   ホスト変数ファイルを作成する。
    // 
    // パラメータ
    //   $ina_host_vars:        ホスト変数配列
    //                          [ipaddress][ 変数名 ]=>具体値
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //   $ina_host_child_vars:  配列変数一覧返却配列(変数一覧に配列変数含む)
    //                          [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //   $ina_DB_child_vars_master: 
    //                          メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                          [変数名][メンバー変数名]=0
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // #0001 hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を貰う
    function CreateHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                 $ina_host_child_vars,$ina_DB_child_vars_master) 
    {
        // ホスト変数配列よりホスト名(IP)を取得
        $host_list = array_keys($ina_host_vars);

        // ホスト変数配列のホスト)分繰返し
        foreach( $host_list as $host_name){
            // legacy/roleのhostsの記述をホスト名ベースに変更しているので
            // ホスト変数ファイル名もホスト名にする
            foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
            $host_vars_file = $hostname;
            //ドライバ区分を判定しホスト変数定義ファイル名を取得
            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_DRIVER_ID:
                // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
                //LEGACY用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_host_var_file($host_vars_file);
                break;
            case DF_PIONEER_DRIVER_ID:
                // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
                //PIONEER用のオリジナルホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_org_host_var_file($host_vars_file);        
                break;
            default:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55200",array(basename(__FILE__) . "-" . __LINE__)); //"内部処理異常  FILE:｛｝  LINE:｛｝が見つからない。"
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            //該当ホストの変数配列を取得
            $vars_list = $ina_host_vars[$host_name];


            $chl_vars_list = array();
            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_ROLE_DRIVER_ID:
                //該当ホストの配列変数を取得
                if(@count($ina_host_child_vars[$host_name]) != 0){
                    $chl_vars_list = $ina_host_child_vars[$host_name];
                }
                break;
            }

            // ホスト変数定義ファイル作成
            if($this->CreateHostvarsfile("VAR",$host_name,
                                         $file_name,$vars_list,
                                         $chl_vars_list,$ina_DB_child_vars_master) === false)
            {
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005-1
    // 処理内容
    //   ホスト変数定義ファイル(1ホスト)を作成する。(Role専用)
    // パラメータ
    //   $in_var_type:      登録対象の変数タイプ
    //                      "VAR"/"CPF"
    //   $in_file_name:     ホスト変数定義ファイル名
    //   $ina_var_list:     ホスト変数配列 
    //                      legacyの場合
    //                      [ 変数名 ]=>具体値
    //                      pioneerの場合
    //                      [対話ファイル変数名]=対話ファイル名
    //   $in_mode:          書込モード
    //                      "w":上書   デフォルト
    //                      "a":追加
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateRoleHostvarsfile($in_var_type,
                                    $in_file_name,$ina_var_list,
                                    $ina_MultiArray_vars_list,$ina_role_rolevars,$ina_def_array_vars_list,
                                    $in_host_ipaddr,
                                    $in_mode="w"){
        $parent_vars_list = array();

        if(@is_array($this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr]) === false){
            $this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr] = array();
        }

        $var_str = "";
        foreach( $ina_var_list as $var=>$val ){
            // 変数の重複出力防止
            if(@count($parent_vars_list[$var]) != 0)
                continue;

            // コピー変数の登録の場合に、VAR変数の具体値に
            // 使用されているコピー変数か確認する。
            if($in_var_type == "CPF"){
                if(@strlen($this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr][$var]) != 0)
                {
                    continue;
                }
            }

            $parent_vars_list[$var] = 0;

            // 読替変数か判定。読替変数の場合は任意変数に置き換える
            if(@count($this->translationtable_list[$var]) != 0){
                $var = $this->translationtable_list[$var];
            }
            
            //ホスト変数ファイルのレコード生成
            //変数名: 具体値
            $var_str = $var_str . sprintf("%s: %s\n",$var,$val);

            // 変数の具体値に使用しているコピー変数の情報を確認
            if($in_var_type == "VAR"){
                $objLibs = new AnsibleCommonLibs();
                $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                $val,
                                                                $this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr]);
                unset($objLibs);
                if($ret == false){
                    //エラーメッセージは出力しているので、ここでは何も出さない。
                    return false;
                }
            }
        }

        // copyモジュール変数のみ登録で呼ばれるケースの対応
        if($in_mode == "w"){
            $parent_vars_list = array();
            $MultiArrayVars_str = "";
            $ret = $this->MultiArrayVarsToYamlFormatMain($ina_MultiArray_vars_list,$MultiArrayVars_str,$parent_vars_list,
                                                         $in_host_ipaddr,$this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr]);
            if($ret === false){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90234");
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            $var_str = $var_str . $MultiArrayVars_str;

            // グローバル変数をホスト変数ファイルに登録する。
            foreach( $this->lva_global_vars_list as $var=>$val ){
                if(@count($parent_vars_list[$var]) != 0){
                    continue;
                }
                $parent_vars_list[$var] = 0;
            
                //ホスト変数ファイルのレコード生成
                //変数名: 具体値
                $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
                //グローバル変数の具体値にコピー変数があるか確認
                $objLibs = new AnsibleCommonLibs();
                $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                $val,
                                                                $this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr]);
                unset($objLibs);
                if($ret == false){
                    //エラーメッセージは出力しているので、ここでは何も出さない。
                    return false;
                }
            }

            // "VAR"でしかこないルート 多段変数と他変数と同時に出力する。
            // 変数の具体値に使用しているコピー変数の情報をホスト変数ファイルに出力
            foreach($this->lv_legacy_Role_cpf_vars_list[$in_host_ipaddr] as $var=>$val){
                //ホスト変数ファイルのレコード生成
                //変数名: 具体値
                $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
            }
        }

        if ( $var_str != "" ){
            $fd = @fopen($in_file_name, $in_mode);

            if($fd == null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55206",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        
            if( @fputs($fd, $var_str) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            if( @fclose($fd) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005-2
    // 処理内容
    //   ホスト変数定義ファイル(1ホスト)を作成する。
    // パラメータ
    //   $in_var_type:      登録対象の変数タイプ
    //                      "VAR"/"CPF"/"TPF"/"CMT"
    //   $in_host_name:     ホスト名
    //   $in_file_name:     ホスト変数定義ファイル名
    //   $ina_var_list:     ホスト変数配列 
    //                      legacyの場合
    //                      [ 変数名 ]=>具体値
    //                      pioneerの場合
    //                      [対話ファイル変数名]=対話ファイル名
    //   $ina_host_child_vars:  
    //                      配列変数一覧返却配列(変数一覧に配列変数含む)
    //                      [ 変数名 ][列順序][メンバー変数]=[具体値]
    //                      空の場合がある
    //   $ina_DB_child_vars_master: 
    //                      メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                      [変数名][メンバー変数名]=0
    //   $in_mode:          書込モード
    //                      "w":上書   デフォルト
    //                      "a":追加
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateHostvarsfile($in_var_type,$in_host_name,
                                $in_file_name,$ina_var_list,
                                $ina_child_vars,$ina_DB_child_vars_master,
                                $in_mode="w"){
        switch($this->getAnsibleDriverID()){
        case DF_PIONEER_DRIVER_ID:
            $parent_vars_list = array();
            $var_str = "";
            foreach( $ina_var_list as $var=>$val ){
                if(@count($parent_vars_list[$var]) != 0){
                    continue;
                }
                $parent_vars_list[$var] = 0;
                //ホスト変数ファイルのレコード生成
                //変数名: 具体値
                $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
            }

            // ファイル管理とテンプレート管理の変数登録でも呼ばれるので、この場合は
            // グローバル変数をホスト変数ファイルに登録しない。
            if($in_mode == "w"){
                // グローバル変数をホスト変数ファイルに登録する。
                foreach( $this->lva_global_vars_list as $var=>$val ){
                    if(@count($parent_vars_list[$var]) != 0){
                        continue;
                    }
                    $parent_vars_list[$var] = 0;
                    //ホスト変数ファイルのレコード生成
                    //変数名: 具体値
                    $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
                }
            }
            break;
        case DF_LEGACY_DRIVER_ID:
            $var_str = "";
            if($in_var_type != "CMT"){
                foreach( $ina_var_list as $var=>$val ){
                    if(@count($this->lv_legacy_parent_vars_list[$in_host_name][$var]) != 0){
                        continue;
                    }
                    // テンプレート変数の登録の場合に、VAR変数の具体値に
                    // 使用されているテンプレート変数か確認する。
                    if($in_var_type == "TPF"){
                        if(@strlen($this->lv_legacy_tpf_vars_list[$in_host_name][$var]) != 0){
                            continue;
                        }
                    }
                    // コピー変数の登録の場合に、VAR変数の具体値に
                    // 使用されているコピー変数か確認する。
                    if($in_var_type == "CPF"){
                        if(@strlen($this->lv_legacy_cpf_vars_list[$in_host_name][$var]) != 0){
                            continue;
                        }
                    }
    
                    $this->lv_legacy_parent_vars_list[$in_host_name][$var] = 0;
                    //ホスト変数ファイルのレコード生成
                    //変数名: 具体値
                    $var_str = $var_str . sprintf("%s: %s\n",$var,$val);

                    if($in_var_type == "VAR"){
                        // テンプレートファイル内の変数具体値登録をチェックする設定にする。
                        $templ_vars_chk = true;
    
                        //具体値にコピー/テンプレート変数が使用されているか判定する。
                        //変数の具体値にテンプレート変数かコピー変数が設定されている場合
                        //各変数に紐づくファイルを所定のディレクトリにコピーする。
                        //テンプレート変数の場合、テンプレートファイル内に変数がある場合
                        //変数の具体値が登録されているか判定する。
                        $ret = $this->LegacyCheckConcreteValueIsVar($templ_vars_chk,
                                                                    $val,
                                                                    $ina_var_list,
                                                                    $in_host_name,
                                                                    $this->lv_legacy_tpf_vars_list,
                                                                    $this->lv_legacy_cpf_vars_list);
                        if($ret === false){
                            return false;
                        }
                    }
                }
            } 
            // ファイル管理とテンプレート管理の変数を使用していないケースは CPF/TPF で
            // このモジュールが呼ばれない。
            // グローバル変数の情報をホスト変数ファイルに出力するタイミングでファイル管理とテンプレート管理の変数を出力する。
            if($in_var_type == "CMT"){
                // グローバル変数をホスト変数ファイルに登録する。
                foreach( $this->lva_global_vars_list as $var=>$val ){
                    if(@count($this->lv_legacy_parent_vars_list[$in_host_name][$var]) != 0){
                        continue;
                    }
                    $this->lv_legacy_parent_vars_list[$in_host_name][$var] = 0;

                    //ホスト変数ファイルのレコード生成
                    //変数名: 具体値
                    $var_str = $var_str . sprintf("%s: %s\n",$var,$val);

                    // playbookのテンプレートモジュールでグローバル変数を使用している場合
                    // テンプレートファイル内の変数具体値登録をチェックする設定にする。
                    $templ_vars_chk = false;
                    if(@count($this->lv_legacy_tfp_use_gbl_vars_list[$var]) != 0){
                        $templ_vars_chk = true;
                    }

                    //具体値にコピー/テンプレート変数が使用されているか判定する。
                    //変数の具体値にテンプレート変数かコピー変数が設定されている場合
                    //各変数に紐づくファイルを所定のディレクトリにコピーする。
                    //テンプレート変数の場合、テンプレートファイル内に変数がある場合
                    //変数の具体値が登録されているか判定する。
                    $ret = $this->LegacyCheckConcreteValueIsVar($templ_vars_chk,
                                                                $val,
                                                                $ina_var_list,
                                                                $in_host_name,
                                                                $this->lv_legacy_tpf_vars_list,
                                                                $this->lv_legacy_cpf_vars_list);
                    if($ret === false){
                        return false;
                    }
                }

                // 変数の具体値に使用しているテンプレート変数の情報をホスト変数ファイルに出力
                if(@count($this->lv_legacy_tpf_vars_list[$in_host_name]) != 0){
                    foreach($this->lv_legacy_tpf_vars_list[$in_host_name] as $var=>$val){
                        if(@count($this->lv_legacy_parent_vars_list[$in_host_name][$var]) != 0){
                            continue;
                        }
                        $this->lv_legacy_parent_vars_list[$in_host_name][$var] = 0;

                        //ホスト変数ファイルのレコード生成
                        //変数名: 具体値
                        $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
                    }
                }
                // 変数の具体値に使用しているコピー変数の情報をホスト変数ファイルに出力
                if(@count($this->lv_legacy_cpf_vars_list[$in_host_name]) != 0){
                    foreach($this->lv_legacy_cpf_vars_list[$in_host_name] as $var=>$val){
                        if(@count($this->lv_legacy_parent_vars_list[$in_host_name][$var]) != 0){
                            continue;
                        }
                        $this->lv_legacy_parent_vars_list[$in_host_name][$var] = 0;

                        //ホスト変数ファイルのレコード生成
                        //変数名: 具体値
                        $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
                    }
                }
            }
            break;

        }

        if ( $var_str != "" ){
            $fd = @fopen($in_file_name, $in_mode);

            if($fd == null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55206",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        
            if( @fputs($fd, $var_str) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( @fclose($fd) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }


    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   playbookファイルを作成する。
    // パラメータ
    //   $in_file_name:        playbookファイル名
    //   $ina_playbook_list:   legacy: 子プレイブックの配列
    //                         [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //                         pioneer: 対話ファイル変数の配列
    //                         [通番][pkey固定][変数名(var%d)]
    //                         Legacy-Role: ロール名の配列
    //                         [実行順序][role_id]=>ロール名
    //   $in_exec_mode:        実行エンジン
    //                           1: Ansible  2: Ansible Tower
    //   $in_exec_playbook_hed_def: 親Playbookヘッダセクション
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreatePlaybookfile($in_file_name,$ina_playbook_list,$in_exec_mode,$in_exec_playbook_hed_def){
        global $root_dir_path;

        $fd = @fopen($in_file_name, "w");
        if($fd == null){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55208",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
    
        //ドライバ区分判定
        $value = "";
        switch($this->getAnsibleDriverID()){
        case DF_LEGACY_DRIVER_ID:
            if(strlen(trim($in_exec_playbook_hed_def)) == 0) {
                if($in_exec_mode == DF_EXEC_MODE_ANSIBLE) {
                    $value =          "- hosts: all\n";
                    $value = $value . "  remote_user: \"{{ " . self::LC_ANS_USERNAME_VAR_NAME . " }}\"\n";
                    $value = $value . "  gather_facts: no\n";

                    // 対象ホストがwindowsか判別。windows以外の場合は become: yes を設定
                    if($this->lv_winrm_id != 1){
                        $value = $value . "  become: yes\n";
                    }
                } else {
                    $value =          "- hosts: all\n";
                    $value = $value . "  gather_facts: no\n";
                    // 対象ホストがwindowsか判別。windows以外の場合は become: yes を設定
                    if($this->lv_winrm_id != 1){
                        $value = $value . "  become: yes\n";
                    }
                }
            } else {
                $value  = $in_exec_playbook_hed_def;
                $value  = $value . "\n";
            }

            $value = $value . "\n";
            $value = $value . "  tasks:\n";
            break;
        case DF_PIONEER_DRIVER_ID:
            $value =          "- hosts: all\n";
            $value = $value . "  gather_facts: false\n";
            $value = $value . "\n";
            $value = $value . "  tasks:\n";
            break;
            
        case DF_LEGACY_ROLE_DRIVER_ID:
            if(strlen(trim($in_exec_playbook_hed_def)) == 0) {
                if($in_exec_mode == DF_EXEC_MODE_ANSIBLE) {
                    $value =          "- hosts: all\n";
                    $value = $value . "  remote_user: \"{{ " . self::LC_ANS_USERNAME_VAR_NAME . " }}\"\n";

                    // 対象ホストがwindowsか判別。windows以外の場合は becom: yes を設定
                    if($this->lv_winrm_id != 1){
                        $value = $value . "  become: yes\n";
                    }
                } else {
                    $value =          "- hosts: all\n";
                    $value = $value . "  become: yes\n";
                }
            } else {
                $value  = $in_exec_playbook_hed_def;
                $value  = $value . "\n";
            }
            $value = $value . "\n";
            $value = $value . "  roles:\n";
            break;
        
        default:
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55200",
                                                       array(basename(__FILE__) . __LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }    
        if( @fputs($fd, $value) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55209",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        $value = "";
        foreach( $ina_playbook_list as $no=>$file_list ){
            foreach( $file_list as $key=>$file ){
                //ドライバ区分判定
                switch($this->getAnsibleDriverID()){
                case DF_LEGACY_DRIVER_ID:
                    $value = $value . "    - include: " . $this->getPlaybook_child_playbook_file($key,$file) . "\n";
                    break;
                case DF_PIONEER_DRIVER_ID:
                    $value = $value . "    - name: pioneer_module exec\n";
                    $value = $value . "      pioneer_module: username={{ " . self::LC_ANS_USERNAME_VAR_NAME . " }} " .
                                                  "protocol={{ " . self::LC_ANS_PROTOCOL_VAR_NAME . " }} " .
                                                  "exec_file={{ " . $file . " }} " .
                                                  "inventory_hostname={{ " . self::LC_ANS_LOGINIP_VAR_NAME . " }} " .
                                                  "host_vars_file='" . $this->getAnsible_original_hosts_vars_Dir() . "/{{ " . self::LC_ANS_LOGINHOST_VAR_NAME . " }}' ".
                                                  "grep_shell_dir='" . $root_dir_path . "'" . " " .
                                                  "log_file_dir='" . $this->getAnsible_out_Dir() . "' " . 
                                                  "ssh_key_file={{ " . self::LC_ANS_SSH_KEY_FILE_VAR_NAME . " }} " .
                                                  "extra_args={{ " . self::LC_ANS_SSH_EXTRA_ARGS_VAR_NAME . " }}\n";

                    $value = $value . "      delegate_to: 127.0.0.1\n";
                    break;

                case DF_LEGACY_ROLE_DRIVER_ID:
                    $value = $value . "    - role: " . $file . "\n";
                    break;

                default:
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55200",
                                                                array(basename(__FILE__) . __LINE__));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }    
            }
        }
        if( @fputs($fd, $value) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55209", array(__LINE__)); 
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( @fclose($fd) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55209", array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    // 処理内容
    //   Legacy用 PlayBookファイルを作成する。
    // 
    // パラメータ
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          ina_child_playbooks[INCLUDE順序][素材管理Pkey]=>素材ファイル
    //   $in_exec_mode:         実行エンジン
    //                           1: Ansible  2: Ansible Tower
    //   $in_exec_playbook_hed_def: 親Playbookヘッダセクション
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateLegacyPlaybookfiles($ina_child_playbooks,$in_exec_mode,$in_exec_playbook_hed_def){
        //////////////////////////////////////
        // 子PlayBookファイル作成           //
        //////////////////////////////////////
        if($this->CreateChildPlaybookfiles($ina_child_playbooks) === false){
            return false;
        }
        //////////////////////////////////////
        // 親PlayBookファイル作成(Legacy)   //
        //////////////////////////////////////
        $file_name = $this->getAnsible_playbook_file();

        if($this->CreatePlaybookfile($file_name,$ina_child_playbooks,$in_exec_mode,$in_exec_playbook_hed_def) === false){
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    // 処理内容
    //   子Playbook(legacy版)を作成する。
    // パラメータ
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //                          の配列
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateChildPlaybookfiles($ina_child_playbooks){
        foreach( $ina_child_playbooks as $playbook_list ){
            foreach( $playbook_list as $pkey=>$playbook ){
                //子Playbookが存在しているか確認
                $src_file = $this->getITA_child_playbiook_file($pkey,$playbook);

                if( file_exists($src_file) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55210",array($pkey,basename($src_file))); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
                // Ansible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
                $dst_file = $this->getAnsible_child_playbiook_file($pkey,$playbook);

                //子Playbookをansible用ディレクトリにコピーする。
                if( copy($src_file,$dst_file) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55211",array(basename($src_file))); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   Pioneer用 PlayBook(対話ファイル)を作成する。
    // 
    // パラメータ
    //   $ina_dialog_files:     対話ファイル配列
    //                          [ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //   $pioneer_sshkeyfilelist:         SSH秘密鍵ファイルリスト
    //                                    [ホスト名(IP)] = SSH秘密鍵ファイル 
    //   $ina_pioneer_sshextraargslist:   SSH_EXTRA_ARGSリスト(pioneer専用)
    //                                    [ホスト名(IP)] = SSH秘密鍵ファイル 
    //   $in_exec_mode:         実行エンジン
    //                           1: Ansible  2: Ansible Tower
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を貰う
    function CreatePioneerDialogfiles($ina_dialog_files,$ina_host_vars,$ina_hostprotcollist,
                                      $ina_pioneer_sshkeyfilelist,
                                      $ina_pioneer_sshextraargslist,
                                      $in_exec_mode)
    {
        $max_file = 0;
        // 対話ファイル配列よりホスト名(IP)を取得
        $host_list = array_keys($ina_dialog_files);

        // 対話ファイル配列のホスト)分繰返し 各ホストでの最大対話ファイル数を記憶
        foreach( $host_list as $host_name ){
            // 対話ファイル配列より該当ホストの対話リスト配列を取得
            $dialog_file_list = $ina_dialog_files[$host_name];
            // 各ホストでの最大対話ファイル数を記憶
            if($max_file < count($dialog_file_list)){
                $max_file = count($dialog_file_list);
            }
        }

        $max_vars = 0;
        // 対話ファイル配列よりホスト名(IP)を取得
        $host_list = array_keys($ina_dialog_files);

        // 対話ファイル配列のホスト)分繰返し
        foreach( $host_list as $host_name ){
            // 対話ファイル配列より該当ホストの対話リスト配列を取得
            $dialog_file_list = $ina_dialog_files[$host_name];

            // 各ホストでの最大対話ファイル数を記憶
            if($max_vars < count($dialog_file_list)){
                $max_vars = count($dialog_file_list);
                $var_name_list=array();
                for($idx=1;$idx<=$max_vars;$idx++){
                    // ホスト変数ファイルに登録する対話ファイルの変数名(var%d)配列作成
                    // [通番][pkey固定][変数名(var%d)]
                    array_push($var_name_list,array("pkey"=>sprintf(self::LC_PLAYBOOK_DIALOG_FILE_VARNAME_MK,$idx)));
                }
            }
            
            foreach($ina_hostprotcollist[$host_name] as $hostname_2=>$prolist)
            
            //////////////////////////////////////////
            // 対話ファイル作成                     //
            //////////////////////////////////////////
            if($this->CreateDialogfiles($hostname_2,$dialog_file_list) === false){
                return false;
            }

            // 対話ファイルの変数名(var%d)配列作成
            $host_vars_list = array();
            $idx=0;
            //[INCLUDE順番][素材管理Pkey]=対話ファイル
            foreach( $dialog_file_list as $inclodeno=>$pkeylist ){
                 foreach( $pkeylist as $pkey=>$dialog_file ){
                     $idx++;
                     //変数名生成 var%d
                     $arry_key = sprintf(self::LC_PLAYBOOK_DIALOG_FILE_VARNAME_MK,
                                         $idx);
                     // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する。
                     //対話ファイル名(絶対パス)生成
                     $intNumPadding = 10;
                     $arry_val = sprintf(self::LC_ANS_ORG_DIALOG_FILE_MK,
                                         $this->getAnsible_dialog_files_Dir(),
                                         $hostname_2,
                                         str_pad( $pkey, $intNumPadding, "0", STR_PAD_LEFT ),       
                                         $dialog_file);

                     //[対話ファイル変数名]=対話ファイル名
                     $host_vars_list[$arry_key] = $arry_val;
                }
            }
            //対話ファイル数が他ホストより少ないか判定
            for(;$idx < $max_file;){
                //不足分を仮の対話ファイルで埋める
                $idx++;
                $arry_key = sprintf(self::LC_PLAYBOOK_DIALOG_FILE_VARNAME_MK,
                                    $idx);
                $host_vars_list[$arry_key] = ""; //self::LC_ANS_UNDEFINE_NAME;
            }

            // システム予約変数をホスト変数ファイルに登録する為の準備
            $host_vars_list[self::LC_ANS_PROTOCOL_VAR_NAME]  = 
 $ina_host_vars[$host_name][self::LC_ANS_PROTOCOL_VAR_NAME];
            $host_vars_list[self::LC_ANS_USERNAME_VAR_NAME]  = 
 $ina_host_vars[$host_name][self::LC_ANS_USERNAME_VAR_NAME];
            if(!empty($ina_host_vars[$host_name][self::LC_ANS_PASSWD_VAR_NAME])){
                $host_vars_list[self::LC_ANS_PASSWD_VAR_NAME] = 
     $ina_host_vars[$host_name][self::LC_ANS_PASSWD_VAR_NAME];
            }
            $host_vars_list[self::LC_ANS_LOGINHOST_VAR_NAME] = 
 $ina_host_vars[$host_name][self::LC_ANS_LOGINHOST_VAR_NAME];
            
            if($this->lv_hostaddress_type == 2){
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_list[self::LC_ANS_LOGINIP_VAR_NAME] = $hostname;
            }
            else{
                $host_vars_list[self::LC_ANS_LOGINIP_VAR_NAME] = $host_name;
            }

            // SSH秘密鍵での接続の場合にSSH秘密鍵ファイルを変数として登録する。
            if(@strlen($ina_pioneer_sshkeyfilelist[$host_name]) != 0){
                $host_vars_list[self::LC_ANS_SSH_KEY_FILE_VAR_NAME] = $ina_pioneer_sshkeyfilelist[$host_name];
            }
            else{
                $host_vars_list[self::LC_ANS_SSH_KEY_FILE_VAR_NAME] = self::LC_ANS_UNDEFINE_NAME;
            }
            // SSH_EXTRA_ARGSを変数として登録する。
            if(@strlen($ina_pioneer_sshextraargslist[$host_name]) != 0){
                // SSH_EXTRA_ARGSは"で囲む
                $host_vars_list[self::LC_ANS_SSH_EXTRA_ARGS_VAR_NAME] = '"' . $ina_pioneer_sshextraargslist[$host_name] . '"';
            }
            else{
                $host_vars_list[self::LC_ANS_SSH_EXTRA_ARGS_VAR_NAME] = self::LC_ANS_UNDEFINE_NAME;
            }

            // ユーザー公開用データリレイストレージパス 変数の名前
            $host_vars_list[self::LC_ANS_OUTDIR_VAR_NAME] = $this->lv_user_out_Dir;

            // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
            $host_vars_list[self::LC_SYMPHONY_DIR_VAR_NAME] = $this->lv_symphony_instance_Dir;

            // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
            foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
            $host_vars_file = $hostname;
            // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
            // ホスト変数定義ファイル名を取得
            $file_name = $this->getAnsible_host_var_file($host_vars_file);
            //////////////////////////////////////////
            // ホスト変数ファイル作成(Pioneer)      //
            //////////////////////////////////////////
            if($this->CreateHostvarsfile("VAR",$host_name,$file_name,$host_vars_list,"","") === false)
            {
                return false;
            }
        }
        //////////////////////////////////////
        // 親PlayBookファイル作成(Pioneer)  //
        //////////////////////////////////////
        // $var_name_list [通番][pkey固定][変数名(var%d)]

        if($this->CreatePlaybookfile($this->getAnsible_playbook_file(),$var_name_list,$in_exec_mode,"") === false){
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   対話ファイル(Pioneer版)をAnsible用ディレクトリにコピーする。
    // パラメータ
    //   $in_hostname:       ホスト名(IP)
    //   $dialog_file_list:  [INCLUDE順番][素材管理Pkey]=対話ファイル
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDialogfiles($in_hostname,$dialog_file_list){
        foreach( $dialog_file_list as $includeno=>$pkeylist ){
            foreach( $pkeylist as $pkey=>$dialogfile ){
                //ITA側で管理されている対話ファイルが存在しているか確認
                $src_file = $this->getITA_dialog_file($pkey,$dialogfile);
                if( file_exists($src_file) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55212",array($pkey,basename($src_file))); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
                //該当ホスト用加工前対話ファイルディレクトリを作成
                $c_dir = $this->getAnsible_org_dialog_file_host_Dir($in_hostname);
    
                if( !is_dir( $c_dir ) ){
                    //ディレクトリが存在している場合はなにもしない
                    if( !mkdir( $c_dir, 0777 ) ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                    if( !chmod( $c_dir, 0777 ) ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                }
                // Ansible実行時のファイル名は Pkey(10桁)-対話ファイル名 する。
                //対話ファイルをオリジナル用対話ファイルディレクトリにコピーする。
                $dst_file = $this->getAnsible_org_dialog_file($in_hostname,$pkey,$dialogfile);

                if( copy($src_file,$dst_file) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55213",array(basename($src_file)));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
    
                //該当ホスト用加工後対話ファイルディレクトリを作成
                $c_dir = $this->getAnsible_dialog_file_host_Dir($in_hostname);
    
                if( !is_dir( $c_dir ) ){
                    //ディレクトリが存在している場合はなにもしない
                    if( !mkdir( $c_dir, 0777 ) ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55202",array(__LINE__)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                    if( !chmod( $c_dir, 0777 ) ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55203",array(__LINE__));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                }
                // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する。
                //対話ファイルをansible用加工後対話ファイルディレクトリにコピーする。
                $dst_file = $this->getAnsible_dialog_file($in_hostname,$pkey,$dialogfile);

                if( copy($src_file,$dst_file) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55213",array(basename($src_file))); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0011
    // 処理内容
    //   Legacy用 Playbookのフォーマットをチェックする
    //   Playbookで使用している変数がホスト変数に登録されているかチェックする。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckLegacyPlaybookfiles($ina_hosts,$ina_host_vars,$ina_child_playbooks){
        $result_code = true;

        foreach( $ina_child_playbooks as $no=>$playbook_list ){
            // 子PlayBook分の繰返し
            foreach( $playbook_list as $playbookpkey=>$playbook ){
                // Ansible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
                // 子PlayBookのバスを取得
                $file_name = $this->getAnsible_child_playbiook_file($playbookpkey,$playbook);

                ///////////////////////////////////////////////////////////////////
                // 子PlayBookのフォーマットチェックを行う。
                ///////////////////////////////////////////////////////////////////
                if($this->CheckChildPlaybookFormat($file_name) === false){
                    //フォーマットチェックでエラーが発生した以下の処理はしない
                    $result_code = false;
                    continue;
                }
                ///////////////////////////////////////////////////////////////////
                // 子PlayBookで使用している変数がホストの変数に登録されているか判定
                ///////////////////////////////////////////////////////////////////

                // ローカル変数のリスト作成
                $local_vars = array();

                // 子PlayBookに登録されている変数を抜出す。
                $dataString = file_get_contents($file_name);

                // グローバル変数を対話ファイルから抜出しグローバル変数管理に登録されていることを確認する。
                // グローバル変数を対話ファイルから抜きす
                $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_GBL_HED,$dataString,$local_vars);
                $aryResultParse = $objWSRA->getParsedResult();
                $file_global_vars_list = $aryResultParse[1];
                unset($objWSRA);

                if(count($file_global_vars_list) != 0){
                    // グローバル変数管理にグローバル変数が未定義の判定
                    if(count($this->lva_global_vars_list) == 0){

                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90238");
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                    // Playbookから抜き出したグローバル変数がグローバル変数管理に登録されているか判定
                    foreach( $file_global_vars_list as $var_name ){
                        if(@count($this->lva_global_vars_list[$var_name]) == 0){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90239",array($var_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            // エラーリターンする
                            $result_code = false;
                        }
                    }
                }

                // ローカル変数のリスト作成
                $local_vars = array();
                $local_vars[] = self::LC_ANS_PROTOCOL_VAR_NAME;
                $local_vars[] = self::LC_ANS_USERNAME_VAR_NAME;
                $local_vars[] = self::LC_ANS_PASSWD_VAR_NAME;
                $local_vars[] = self::LC_ANS_LOGINHOST_VAR_NAME;

                // ユーザー公開用データリレイストレージパス 変数の名前
                $local_vars[] = self::LC_ANS_OUTDIR_VAR_NAME;

                // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
                $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

                // ホスト変数の抜出を示すパラメータを追加
                $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

                $aryResultParse = $objWSRA->getParsedResult();
                unset($objWSRA);

                $file_vars_list = $aryResultParse[1];
    
                // 子PlayBookで変数が使用されているか判定
                if(count($file_vars_list) == 0){
                    // 子PlayBookで変数が使用されていない場合は以降のチェックをスキップ
                    continue;            
                }

                // 各ホストのホスト変数があるか判定
                foreach( $ina_hosts as $no => $host_name ){
                    if(empty($ina_host_vars[$host_name])===true){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55232",array($playbook,$host_name)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        // 未登録でも処理は続行する。
                    }
                }
                // PlayBookに登録されている変数のデータベース登録確認 
                foreach( $file_vars_list as $var_name ){

                    // ホスト配列のホスト分繰り返し
                    foreach( $ina_hosts as $no=>$host_name ){

                        if((@strlen($ina_host_vars[$host_name][$var_name])==0) &&
                           (array_key_exists($var_name,$ina_host_vars[$host_name])==false))

                        {
                            if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56213",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56211",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56212",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56210",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            else{
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55214",
                                                                                array($playbook,
                                                                                      $var_name,
                                                                                      $host_name)); 
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                            }
                            // エラーリターンする
                            $result_code = false;
                        }
                        else{
                            //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                            if($ina_host_vars[$host_name][$var_name] == self::LC_ANS_UNDEFINE_NAME){
                                // プロトコル未登録
                                if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56213",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ユーザー名未登録
                                elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56211",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ログインパスワード未登録
                                elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56212",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ホスト名未登録
                                elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56210",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return($result_code);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0012
    // 処理内容
    //   Pioneer用 Playbookのフォーマットをチェックする
    //   対話ファイルで使用している変数がホスト変数に登録されているかチェックする。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=[ホスト名(IP)]
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    //   $ina_dialog_files:     対話ファイル配列
    //                          [ホストIP][INCLUDE順番][素材管理Pkey]=対話ファイル
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckPioneerPlaybookfiles($ina_hosts,$ina_host_vars,$ina_dialog_files,$ina_hostprotcollist){
        $result_code = true;

        // 対話ファイル配列よりホストリストを取得
        $host_list = array_keys($ina_dialog_files);
        // 各ホストの対話ファイルがあるか判定
        foreach( $ina_hosts as $host_name ){
            if(array_key_exists($host_name,$ina_dialog_files)===false){
                 $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55233",array($host_name));
                 $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                 $result_code = false;
                 return($result_code);
            }
        }

        $ProcessedFileList = array();
        // 対話ファイル配列のホスト分繰返し
        foreach( $ina_hosts as $no=>$host_name ){
            // 対話ファイル配列より該当ホストの対話ファイル配列取得            
            $dialog_file_list = $ina_dialog_files[$host_name];
            foreach( $dialog_file_list as $includeno=>$pkeylist ){
                foreach( $pkeylist as $playbook_pkey=>$playbook ){
                    // Movement詳細に同一対話ファイル(TPF/CPF変数を使用)が複数登録された場合
                    // 複数回処理されないようにガードする。
                    // ガードしないと対話ファイル内のTPF/CPF変数が具体値に置き換わる場合がある。
                    if( isset($ProcessedFileList[$host_name][$playbook])) {
                        continue;
                    }
                    $ProcessedFileList[$host_name][$playbook] = 1;
                
                    // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する。
                    // 対話ファイルのパス取得
                    foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                    
                    $file_name = $this->getAnsible_dialog_file($hostname,$playbook_pkey,$playbook);

                    ///////////////////////////////////////////////////////////////////
                    // 対話ファイルのフォーマットチェックを行う。
                    ///////////////////////////////////////////////////////////////////
                    if($this->CheckDialogfileFormat($file_name,$host_name) === false){
                        //フォーマットチェックでエラーが発生した場合は変数チェックはしない。
                        $result_code = false;
                        continue;
                    }
                
                    ///////////////////////////////////////////////////////////////////
                    // 子PlayBookで使用している変数がホストの変数に登録されているか判定
                    ///////////////////////////////////////////////////////////////////
                    // 子PlayBookに登録されている変数を抜出す。
                    $dataString = file_get_contents($file_name);
                    if( $dataString === false){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90241",
                                                                   array(basename($file_name)));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        return($result_code);
                    }

                    // ローカル変数のリスト作成
                    $local_vars = array();

                    // グローバル変数を対話ファイルから抜出しグローバル変数管理に登録されていることを確認する。
                    // グローバル変数を対話ファイルから抜きす
                    $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_GBL_HED,$dataString,$local_vars);
                    $aryResultParse = $objWSRA->getParsedResult();
                    $file_global_vars_list = $aryResultParse[1];
                    unset($objWSRA);

                    $globalvarSetTo = array();
                    if(count($file_global_vars_list) != 0){
                        // グローバル変数管理にグローバル変数が未定義の判定
                        if(count($this->lva_global_vars_list) == 0){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90236");
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            return false;
                        }
                        // 対話ファイルから抜き出したグローバル変数がグローバル変数管理に登録されているか判定
                        foreach( $file_global_vars_list as $var_name ){
                            if(@count($this->lva_global_vars_list[$var_name]) == 0){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90237",array($var_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                return false;
                            }
                            // グローバル変数の具体値を退避
                            $globalvarSetTo[$var_name] = $this->lva_global_vars_list[$var_name];
                        }
                    }

                    // copy変数を対話ファイルから抜出しファイル管理に登録されていることを確認する。
                    // copy変数を対話ファイルから抜きす。
                    $local_vars = array();
                    $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_CPF_HED,$dataString,$local_vars);
                    $aryResultParse = $objWSRA->getParsedResult();
                    $file_copy_vars_list = $aryResultParse[1];
                    unset($objWSRA);

                    if(count($file_copy_vars_list) == 0){
                        // 対話ファイルで変数が使用されていない場合は以降のチェックをスキップ
                    } else {
                        // 該当ホストのホスト変数が登録されているか判定
                        if(array_key_exists($host_name,$ina_host_vars)===false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55215",
                                                                       array($host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            // ホスト変数が登録されていないので以降のチェックをスキップ
                            continue;
                        }
                    }
                    $this->LoadSpycModule($errmsg, $f_name, $f_line);
                    $copyvarSetTo = array();
                    $copy_list = array();
                    $host_vars_file = $hostname;
                    $file_name2 = $this->getAnsible_org_host_var_file($host_vars_file);
                    $copy_list = Spyc::YAMLLoad($file_name2);

                    // $copy_list[ 変数名 ]=>具体値
                    foreach( $file_copy_vars_list as $var_name ){
                        // 対話ファイルで使用している変数がホストの変数に登録されているか判定
                        if(array_key_exists($var_name,$copy_list)===false){
                            $result_code = false;
                            // 未登録でも処理は続行する。
                        } else {
                            // 変数を置換える具体値を設定
                            $copyvarSetTo[$var_name]=$copy_list[$var_name];
                        }
                    }

                    // template変数を対話ファイルから抜出しファイル管理に登録されていることを確認する。
                    // template変数を対話ファイルから抜きす。
                    SimpleVerSearch(DF_HOST_TPF_HED,$dataString,$la_tpf_vars);

                    if(count($la_tpf_vars) == 0){
                        // 対話ファイルで変数が使用されていない場合は以降のチェックをスキップ
                    } else {
                        // 該当ホストのホスト変数が登録されているか判定
                        if(array_key_exists($host_name,$ina_host_vars)===false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55215",
                                                                       array($host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            // ホスト変数が登録されていないので以降のチェックをスキップ
                            continue;
                        }
                    }
                    $this->LoadSpycModule($errmsg, $f_name, $f_line);
                    $tpfvarSetTo = array();
                    $tpf_list = array();
                    $host_vars_file2 = $hostname;
                    $file_name3 = $this->getAnsible_org_host_var_file($host_vars_file2);
                    $tpf_list = Spyc::YAMLLoad($file_name3);

                    foreach( $la_tpf_vars as $no => $tpf_var_list ) {
                        foreach( $tpf_var_list as $line_no  => $tpf_var_name ) {
                            // 対話ファイルで使用している変数がホストの変数に登録されているか判定
                            if(array_key_exists($tpf_var_name,$tpf_list)===false){
                                $result_code = false;
                                // 未登録でも処理は続行する。
                            } else {
                                // 変数を置換える具体値を設定
                                $tpfvarSetTo[$tpf_var_name]=$tpf_list[$tpf_var_name];
                            }
                        }
                    }

                    // ローカル変数のリスト作成
                    $local_vars = array();
                    $local_vars[] = self::LC_ANS_PROTOCOL_VAR_NAME;
                    $local_vars[] = self::LC_ANS_USERNAME_VAR_NAME;
                    $local_vars[] = self::LC_ANS_PASSWD_VAR_NAME;
                    $local_vars[] = self::LC_ANS_LOGINHOST_VAR_NAME;

                    // ユーザー公開用データリレイストレージパス 変数の名前
                    $local_vars[] = self::LC_ANS_OUTDIR_VAR_NAME;

                    // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
                    $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;
 
                    $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

                    $aryResultParse = $objWSRA->getParsedResult();

                    $file_vars_list = $aryResultParse[1];
                    // 対話ファイルで変数が使用されているか判定
                    if(count($file_vars_list) == 0){
                        // 対話ファイルで変数が使用されていない場合は以降のチェックをスキップ
                    }
                    else{
                        // 該当ホストのホスト変数が登録されているか判定
                        if(array_key_exists($host_name,$ina_host_vars)===false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55215",
                                                                       array($host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            // ホスト変数が登録されていないので以降のチェックをスキップ
                            continue;            
                        }
                    }
                    //該当ホストの変数配列を取得
                    $varSetTo = array();
                    $vars_list = $ina_host_vars[$host_name];
                    //$vars_list[ 変数名 ]=>具体値
                    foreach( $file_vars_list as $var_name ){
                        // 対話ファイルで使用している変数がホストの変数に登録されているか判定
                        if(array_key_exists($var_name,$vars_list)===false){
                            if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56209",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56207",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56208",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56206",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            else{
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55216",
                                                                               array($host_name,
                                                                               $playbook,
                                                                               $var_name));

                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            $result_code = false;
                            // 未登録でも処理は続行する。
                        }
                        else{
                            //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                            if($vars_list[$var_name] == self::LC_ANS_UNDEFINE_NAME){
                                // プロトコル未登録
                                if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56209",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ユーザー名未登録
                                elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56207",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ログインパスワード未登録
                                elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56208",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ホスト名未登録
                                elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56206",
                                                                               array($playbook,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                            }
                            // 変数を置換える具体値を設定
                            $varSetTo[$var_name]=$vars_list[$var_name];
                        }
                    }

                    // グローバル変数を具体値で置換える
                    $book_upd = false;
                    if(count($globalvarSetTo) != 0){
                        $objWSRA->stringReplace($dataString,$globalvarSetTo);
                        $dataString = $objWSRA->getReplacedString();
                        $book_upd = true;
                    }
                    if(count($copyvarSetTo) != 0){
                        $objWSRA->stringReplace($dataString,$copyvarSetTo);
                        $dataString = $objWSRA->getReplacedString();
                        $book_upd = true;
                    }
                    if(count($tpfvarSetTo) != 0){
                        $objWSRA->stringReplace($dataString,$tpfvarSetTo);
                        $dataString = $objWSRA->getReplacedString();
                        $book_upd = true;
                    }
                    if(count($varSetTo) != 0){
                        // 変数を具体値で置換える
                        $objWSRA->stringReplace($dataString,$varSetTo);
                        $dataString = $objWSRA->getReplacedString();
                        $book_upd = true;
                    }
                    if($book_upd === true){
                        if(file_put_contents( $file_name,$dataString) === false){
                             $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55217",
                                                                        array($host_name,$playbook));
                             $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                             $result_code = false;
                        }
                    }
                    unset($objWSRA);
                }
            }
        }
        return($result_code);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0013
    // 処理内容
    //   子PlayBookファイルのフォーマットをチェックする。
    // 
    // パラメータ
    //   $in_file_name:        子PlayBookファイル名
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckChildPlaybookFormat($in_file_name){
        $result_code = true;

        ///////////////////////////////////////////////////////////////////
        // 子PlayBookのフォーマット判定
        ///////////////////////////////////////////////////////////////////
        $playbook = basename($in_file_name);
        
        $fd = @fopen($in_file_name, "r");
        if($fd == null){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55218",array($playbook)); //"子Playbookファイル(｛｝)の読込に失敗"
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            $result_code = false;
        }
        else{
            $line_no = 0;
            while(!feof($fd)){
                $line_no++;

                $read_line = @fgets($fd);

                //コメント行の判定
                if(strpos($read_line,"#") === 0){
                    continue;
                }
                //TABキー入力の判定
                if(strstr($read_line,"\t") !== false){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55219",array($playbook,$line_no));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                    continue;
                }

                //空白行の判定
                if(trim($read_line) == ""){
                    continue;
                }

                //空白行その２の判定
                if(trim($read_line) == "\n"){
                    continue;
                }

                //先頭が半角スペースか判定
                //文字列から配列に変換
                $read_array = str_split($read_line);
                if($read_array[0] == " "){
                    //インデントが半角スペース2文字を使用しているか判定
                    $space_count = 1;
                    for($idx=1;$idx<count($read_array);$idx++){
                        if($read_array[$idx] == " "){
                            $space_count++;
                        }
                        else{
                            if(($space_count%2) == 0){
                                 //インデントOK
                                 break;
                            }
                            else{
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55221",array($playbook,$line_no)); 
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                                break;
                            }
                        }
                    }
                }
            }
        }
        if($fd !== null){
            @fclose($fd);
        }
        return($result_code);
    }

    /////////////////////////////////////////////////////////////////////////////////
    // F0014
    // 処理内容
    //   対話ファイルの独自フォーマットをチェックする。
    // 
    // パラメータ
    //   $in_file_name:   対話ファイル
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckDialogfileFormat($in_file_name,$in_host_name){
        $result_code = true;

        // stateコマンドの情報退避
        $state_info     = array();
        $state_line_no  = 0;

        // commandの情報退避
        $command_info    = array();
        $command_line_no = 0;

        $fd = @fopen($in_file_name, "r");
        if($fd == null){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55222",array($in_host_name,basename($in_file_name)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            $result_code = false;
        }
        else{
            $line_no = 0;
            $mysts  = 0;
            while(!feof($fd)){
                $line_no++;
                $rbuff = @fgets($fd);
                // 改行をとれ除く
                $read_line = str_replace("\n","",$rbuff);
                //コメント行の判定
                if(strpos($read_line,"#") === 0){
                    continue;
                }
                // #の前の文字がスペースの場合、以降をコメントとして扱う
                $wspstr = explode(" #",$read_line);
                $read_line = $wspstr[0];

                //TABキー入力の判定
                if(strstr($read_line,"\t") !== false){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55223",
                                                                array($in_host_name,
                                                                      basename($in_file_name),
                                                                      $line_no)); //"ホスト(｛｝)の対話ファイル(｛｝)の｛｝行目にTABキーがあります。"
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                    continue;
                }

                //空白行の判定
                if(trim($read_line) == ""){
                    continue;
                }

                //先頭が半角スペースか判定
                //文字列から配列に変換
                $read_array = str_split($read_line);
                if($read_array[0] == " "){
                    //インデントが半角スペース2文字を使用しているか判定
                    $space_count = 1;
                    for($idx=1;$idx<count($read_array);$idx++){
                        if($read_array[$idx] == " "){
                            $space_count++;
                        }
                        else{
                            if(($space_count%2) == 0){
                                 //インデントOK
                                 break;
                            }
                            else{
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55225",
                                                                           array($in_host_name,
                                                                                 basename($in_file_name),
                                                                                 $line_no));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                                break;
                            }
                        }
                    }
                }

                // conf:セクションか判定
                if(strcmp(rtrim($read_line),"conf:") === 0){
                    if($mysts == 0){
                        $mysts = 1;
                    }
                    else{
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55226",
                                                                    array($in_host_name,
                                                                          basename($in_file_name),
                                                                          $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        break;
                    }
                }
                // conf=>timeout:キーか判定
                elseif(strpos($read_line,"  timeout:") === 0){
                    if($mysts == 1){
                        $arry_list = explode(":",$read_line);
                        if (count($arry_list) != 2){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55227",
                                                                       array($in_host_name,
                                                                             basename($in_file_name),
                                                                             $line_no));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            break;
                        } 
                        $timeout = rtrim(trim($arry_list[1]));
                        if(is_numeric($timeout) == false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55228",
                                                                        array($in_host_name,
                                                                              basename($in_file_name),
                                                                              $line_no));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            break;
                        }
                        if(($timeout < 1) || ($timeout > 3600)){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55228",
                                                                       array($in_host_name,
                                                                             basename($in_file_name),
                                                                             $line_no));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            break;
                        }
                        $mysts = 2;
                    }
                    else{
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55229",
                                                                   array($in_host_name,
                                                                         basename($in_file_name),
                                                                         $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        break;
                    }
                }
                // exec_list:セクションか判定
                elseif(strcmp(rtrim($read_line),"exec_list:") === 0){
                    if($mysts == 2){
                        $mysts = 3;
                    }
                    else{
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55230",
                                                                   array($in_host_name,
                                                                         basename($in_file_name),
                                                                         $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        break;
                    }
                }
                // exec_list=>expect:キーか判定
                elseif(strpos($read_line,"  - expect:") === 0){
                    if(($mysts == 5) || ($mysts == 6))
                    {
                       $ret = $this->checkstateCommand($mysts,$result_code,$state_info,$state_line_no,$in_file_name,$in_host_name);
                       if($ret === false){
                           break;
                       } 
                       //対話ファイルのチェック状態を戻す
                       $mysts = 3;
                    } else if( ($mysts == 7) || ($mysts == 8) ) {
                        // 1個前のcommandの構文が正しいか判定
                        // $result_codeはエラーの場合にfalseになる
                        $ret = $this->checkCommand( $mysts, $result_code, $command_info, $command_line_no, $in_file_name, $in_host_name );
                        // 対話ファイルのチェック状態を戻す
                        if($ret === false) {
                            break;
                        }
                        $mysts = 3;
                    }
                    if($mysts == 3){
                        $arry_list = explode(":",$read_line);
                        if (count($arry_list) < 2){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55236",
                                                                       array($in_host_name,
                                                                             basename($in_file_name),
                                                                             $line_no));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            break;
                        }
                        else{
                            $expect = rtrim(trim($arry_list[1]));
                            if($expect == ""){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55236",
                                                                           array($in_host_name,
                                                                                 basename($in_file_name),
                                                                                 $line_no));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                                break;
                            }
                        }
                        //エラーでも状態は進める
                        $mysts = 4;
                    }
                    else{
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55231",
                                                                   array($in_host_name,
                                                                         basename($in_file_name),
                                                                         $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        break;
                    }
                }
                // exec_list=>exec:キーか判定
                elseif(strpos($read_line,"    exec:") === 0){
                    if($mysts == 4){
                        $arry_list = explode(":",$read_line);
                        $arry_list = explode(":",$read_line);
                        if (count($arry_list) < 2){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55237",
                                                                       array($in_host_name,
                                                                             basename($in_file_name),
                                                                             $line_no));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
// エラーの場合は終了
                            break;
                        }
                        else{
                            $exec = rtrim(trim($arry_list[1]));
                            if($exec == ""){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55237",
                                                                           array($in_host_name,
                                                                           basename($in_file_name),
                                                                           $line_no));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                                break;
                            }
                        }
                        //エラーでも状態は進める
                        $mysts = 3;
                    }
                    else{
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55234",
                                                                   array($in_host_name,
                                                                         basename($in_file_name),
                                                                         $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        break;
                    }
                }
// 作業パターンの出力結果から特定の文字列でOK/NGを判断するコマンド(state)の処理
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:キーか判定
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"  - state:") === 0){
                    $now_cmd = "state";
                    if(($mysts == 5) || ($mysts == 6))
                    {  // 1個前のstateコマンドの構文が正しいか判定
                       // $result_codeはエラーの場合にfalseになる
                       $ret = $this->checkstateCommand($mysts,$result_code,$state_info,$state_line_no,$in_file_name,$in_host_name);
                       //対話ファイルのチェック状態を戻す
                       if($ret === false){
                           break;
                       } 
                       $mysts = 3;
                    } else if( ($mysts == 7) || ($mysts == 8) ) {
                        // 1個前のcommandの構文が正しいか判定
                        // $result_codeはエラーの場合にfalseになる
                        $ret = $this->checkCommand( $mysts, $result_code, $command_info, $command_line_no, $in_file_name, $in_host_name );
                        // 対話ファイルのチェック状態を戻す
                        if($ret === false) {
                            break;
                        }
                        $mysts = 3;
                    }
                    if($mysts == 3){
                        $mysts = 5;

                        // stateコマンドの情報を初期化
                        $this->initstateCommandInfo($state_info,$state_line_no,$line_no);

                        $arry_list = explode(":",$read_line);
                        if (count($arry_list) < 2){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55245",
                                                     array($in_host_name,basename($in_file_name),$line_no));
                            //終了
                            break;
                        }
                        $expect = rtrim(trim($arry_list[1]));
                        if($expect == ""){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55245",
                                                     array($in_host_name,basename($in_file_name),$line_no));
                            //終了
                            break;
                        }
                        else{
                            // state 記述済みにマーク
                            $state_info[$now_cmd] = "1";
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                 "ITAANSIBLEH-ERR-55244",
                                                 array($in_host_name,basename($in_file_name),$line_no));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->prompt:キーか判定
                // exec_list=>command:->prompt:キーか判定
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    prompt:") === 0){
                    $now_cmd = "prompt";
                    if(($mysts == 5) || ($mysts == 6)){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                         "ITAANSIBLEH-ERR-55247",
                                                         array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            $expect = rtrim(trim($arry_list[1]));
                            if($expect == ""){
                                $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                         "ITAANSIBLEH-ERR-55247",
                                                         array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            else{
                                // state 記述済みにマーク
                                $state_info[$now_cmd] = "1";
                                // parameterのリスト取得中の場合にstate中に変更
                                if($mysts == 6){
                                    $mysts = 5;
                                }
                            }
                        }
                    } else if(($mysts == 7) || ($mysts == 8)) {
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            $expect = rtrim(trim($arry_list[1]));
                            if($expect == ""){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            else{
                                // command 記述済みにマーク
                                $command_info[$now_cmd] = "1";
                                // リスト取得中の場合にcommand中に変更
                                if($mysts == 8){
                                    $mysts = 7;
                                }
                            }
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                 "ITAANSIBLEH-ERR-55280", 
                                                 array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->shell:キーか判定
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    shell:") === 0){
                    $now_cmd = "shell";
                    if(($mysts == 5) || ($mysts == 6)){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                         "ITAANSIBLEH-ERR-55247",
                                                         array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            // パラメータが文字列(ファイル名または変数？)か判定 
                            //$ret = preg_match("/(\S+):(\s+)(((\S){1,})|(\{\{( ){1}(\S+)( ){1}\}\}))(\s*)$/",$read_line);
                            $expect = rtrim(trim($arry_list[1]));
                            if($expect == ""){
                                $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                         "ITAANSIBLEH-ERR-55247",
                                                         array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            else{
                                // state 記述済みにマーク
                                $state_info[$now_cmd] = "1";
                                // parameterのリスト取得中の場合にstate中に変更
                                if($mysts == 6){
                                    $mysts = 5;
                                }
                            }
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                          "ITAANSIBLEH-ERR-55249",
                                          array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->parameter:キーか判定
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    parameter:") === 0){
                    $now_cmd = "parameter";
                    if($mysts == 5){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        // パラメータが空白か判定
                        $ret = preg_match("/(\S+):(\s*)$/",$read_line);
                        if($ret !== 1){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            // parameterのリスト取得中
                            $mysts = 6;
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                          "ITAANSIBLEH-ERR-55249",
                                          array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->parameter:->-キーか判定
                // exec_list=>command:->with_items:->-キーか判定
                // exec_list=>command:->when:->-キーか判定:1
                // exec_list=>command:->exec_when:->-キーか判定:1
                // exec_list=>command:->failed_when:->-キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"      - ") === 0){
                    if($mysts == 6){
                        $now_cmd = "parameter";
                        $arry_list = explode("-",$read_line);
                        if (count($arry_list) < 2){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        $expect = rtrim(trim($arry_list[1]));
                        if($expect == ""){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            // state 記述済みにマーク
                            $state_info[$now_cmd] = "1";
                        }
                    } else if($mysts == 8){
                        $arry_list = explode("-",$read_line);
                        if (count($arry_list) < 2){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55277",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        $expect = rtrim(trim($arry_list[1]));
                        if($expect == ""){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55277",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else{
                            // state 記述済みにマーク
                            $command_info[$now_cmd] = "1";
                        }
                    } else {
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                 "ITAANSIBLEH-ERR-55280",
                                                 array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->stdout_file:キーか判定:1
                ////////////////////////////////////////////////////////////////
                
                elseif(strpos($read_line,"    stdout_file:") === 0){
                    $now_cmd = "stdout_file";
                    if(($mysts == 5) || ($mysts == 6)){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        //$ret = preg_match("/(\S+):(\s+)(((\S){1,})|(\{\{( ){1}(\S+)( ){1}\}\}))(\s*)$/",$read_line);
                        // パラメータが文字列か判定 
                        $arry_list = explode("stdout_file:",$read_line);
                        if (count($arry_list) < 2){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        $stdout_file = rtrim(trim($arry_list[1]));
                        if ($stdout_file == ""){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            // state 記述済みにマーク
                            $state_info[$now_cmd] = "1";
                            // parameterのリスト取得中の場合にstate中に変更
                            if($mysts == 6){
                                $mysts = 5;
                            }
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                          "ITAANSIBLEH-ERR-55249",
                                          array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }

                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->success_exit:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    success_exit:") === 0){
                    $now_cmd = "success_exit";
                    if(($mysts == 5) || ($mysts == 6)){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        // パラメータにyes/noか変数が指定されているか判定
                        $ret = preg_match("/(\S+):(\s+)(no|yes|(\{\{( ){1}(\S+)( ){1}\}\}))(\s*)$/",$read_line);
                        if($ret !== 1){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            // state 記述済みにマーク
                            $state_info[$now_cmd] = "1";
                            // parameterのリスト取得中の場合にstate中に変更
                            if($mysts == 6){
                                $mysts = 5;
                            }
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                          "ITAANSIBLEH-ERR-55249",
                                          array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>state:->ignore_errors:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    ignore_errors:") === 0){
                    $now_cmd = "ignore_errors";
                    if(($mysts == 5) || ($mysts == 6)){
                        // コマンドが既に設定済みか判定
                        if($state_info[$now_cmd]=="1"){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55248",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        // パラメータにyes/noか変数が指定されているか判定
                        $ret = preg_match("/(\S+):(\s+)(no|yes|(\{\{( ){1}(\S+)( ){1}\}\}))(\s*)$/",$read_line);
                        if($ret !== 1){
                            $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                                     "ITAANSIBLEH-ERR-55247",
                                                     array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        }
                        else{
                            // state 記述済みにマーク
                            $state_info[$now_cmd] = "1";
                            // parameterのリスト取得中の場合にstate中に変更
                            if($mysts == 6){
                                $mysts = 5;
                            }
                        }
                    }
                    else{
                        $this->errorstateCommand($mysts,$result_code,$state_info,$state_line_no,
                                          "ITAANSIBLEH-ERR-55249",
                                          array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }

                ////////////////////////////////////////////////////////////////
                // exec_list=>command:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"  - command:") === 0) {
                    $now_cmd = "command";
                    if(($mysts == 5) || ($mysts == 6)) {
                        // 1個前のstateコマンドの構文が正しいか判定
                        // $result_codeはエラーの場合にfalseになる
                        $ret = $this->checkstateCommand($mysts,$result_code,$state_info,$state_line_no,$in_file_name,$in_host_name);
                        //対話ファイルのチェック状態を戻す
                        if($ret === false){
                            break;
                        }
                        $mysts = 3;
                    } else if( ($mysts == 7) || ($mysts == 8) ) {
                        // 1個前のcommandの構文が正しいか判定
                        // $result_codeはエラーの場合にfalseになる
                        $ret = $this->checkCommand( $mysts, $result_code, $command_info, $command_line_no, $in_file_name, $in_host_name );
                        // 対話ファイルのチェック状態を戻す
                        if($ret === false) {
                            break;
                        }
                        $mysts = 3;
                    }
                    if($mysts == 3){
                        $mysts = 7;

                        // commandの情報を初期化
                        $this->initCommandInfo($command_info,$command_line_no,$line_no);

                        $arry_list = explode(":",$read_line);
                        if (count($arry_list) < 2){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55274",
                                                array($in_host_name,basename($in_file_name),$line_no));
                            //終了
                            break;
                        }
                        $expect = rtrim(trim($arry_list[1]));
                        if($expect == ""){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55274",
                                                array($in_host_name,basename($in_file_name),$line_no));
                            //終了
                            break;
                        } else {
                            // command 記述済みにマーク
                            $command_info[$now_cmd] = "1";
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55275",
                                            array($in_host_name,basename($in_file_name),$line_no));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->timeout:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    timeout:") === 0){
                    $now_cmd = "timeout";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                break;
                            }
                            $timeout = rtrim(trim($arry_list[1]));
                            if( $timeout == "" ){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                break;
                            } else {
                                // command 記述済みにマーク
                                $command_info[$now_cmd] = "1";
                                // リスト取得中の場合にcommand中に変更
                                if($mysts == 8){
                                    $mysts = 7;
                                }
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->register:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    register:") === 0){
                    $now_cmd = "register";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            $expect = rtrim(trim($arry_list[1]));
                            if($expect == ""){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            } else {
                                // command 記述済みにマーク
                                $command_info[$now_cmd] = "1";
                                // リスト取得中の場合にcommand中に変更
                                if($mysts == 8){
                                    $mysts = 7;
                                }
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->with_items:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    with_items:") === 0){
                    $now_cmd = "with_items";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            // パラメータが空白か判定
                            $ret = preg_match("/(\S+):(\s*)$/",$read_line);
                            if($ret !== 1){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            else{
                                // with_itemsのリスト取得中
                                $mysts = 8;
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->when:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    when:") === 0){
                    $now_cmd = "when";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            // パラメータが空白か判定
                            $ret = preg_match("/(\S+):(\s*)$/",$read_line);
                            if($ret !== 1){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            } else {
                                // whenのリスト取得中
                                $mysts = 8;
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->failed_when:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    failed_when:") === 0){
                    $now_cmd = "failed_when";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            // パラメータが空白か判定
                            $ret = preg_match("/(\S+):(\s*)$/",$read_line);
                            if($ret !== 1){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            } else {
                                // failed_whenのリスト取得中
                                $mysts = 8;
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }
                ////////////////////////////////////////////////////////////////
                // exec_list=>command:->exec_when:キーか判定:1
                ////////////////////////////////////////////////////////////////
                elseif(strpos($read_line,"    exec_when:") === 0){
                    $now_cmd = "exec_when";
                    if(($mysts == 7) || ($mysts == 8)){
                        // コマンドが既に設定済みか判定
                        if($command_info[$now_cmd]=="1"){
                            $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                "ITAANSIBLEH-ERR-55276",
                                                array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                            //終了
                            break;
                        } else {
                            $arry_list = explode(":",$read_line);
                            if (count($arry_list) < 2){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            }
                            // パラメータが空白か判定
                            $ret = preg_match("/(\S+):(\s*)$/",$read_line);
                            if($ret !== 1){
                                $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                                    "ITAANSIBLEH-ERR-55277",
                                                    array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                                //終了
                                break;
                            } else {
                                // exec_whenのリスト取得中
                                $mysts = 8;
                            }
                        }
                    } else {
                        $this->errorCommand($mysts,$result_code,$command_info,$command_line_no,
                                            "ITAANSIBLEH-ERR-55278",
                                            array($in_host_name,basename($in_file_name),$line_no,$now_cmd));
                        //終了
                        break;
                    }
                }

                elseif(trim($read_line) != ""){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55235",
                                                               array($in_host_name,
                                                                     basename($in_file_name),
                                                               $line_no));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                    break;
                }
            }
// 対話ファイルが正しく終了していることか判定
            switch($mysts){
            case 0:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55250",
                                                           array($in_host_name,
                                                           basename($in_file_name)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                $result_code = false;
                break;
            case 1:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55251",
                                                           array($in_host_name,
                                                           basename($in_file_name)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                $result_code = false;
                break;
            case 2:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55252",
                                                           array($in_host_name,
                                                           basename($in_file_name)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                $result_code = false;
                break;
            case 4:
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55253",
                                                           array($in_host_name,
                                                           basename($in_file_name)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                $result_code = false;
                break;
            case 5:
            case 6:
                // 1個前のstateコマンドの構文が正しいか判定
                // $result_codeはエラーの場合にfalseになる
                $ret = $this->checkstateCommand($mysts,$result_code,$state_info,$state_line_no,$in_file_name,$in_host_name);
                break;
            case 7:
            case 8:
                // 1個前のstateコマンドの構文が正しいか判定
                // $result_codeはエラーの場合にfalseになる
                $ret = $this->checkCommand($mysts,$result_code,$command_info,$command_line_no,$in_file_name,$in_host_name);
                break;
            }
        }
        if($fd !== null){
            @fclose($fd);
        }
        return($result_code);
    }

// 作業パターンの出力結果から特定の文字列でOK/NGを判断するコマンド(state)の処理追加
    /////////////////////////////////////////////////////////////////////////////////
    // F0024
    // 処理内容
    //   stateコマンドのパラメータ情報初期化
    // 
    // パラメータ
    //   $in_state_info:    stateコマンドパラメータ設定有無配列
    //   $in_state_line_no: stateコマンド行番号 退避用
    //   $in_line_no:       stateコマンド行番号
    //   
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function initstateCommandInfo(&$in_state_info,&$in_state_line_no,$in_line_no){
        // stateコマンドのパラメータ設定有無をクリア
        $in_state_info                   = array();
        $in_state_info["state"]          = "0";
        $in_state_info["prompt"]         = "0";
        $in_state_info["shell"]          = "0";
        $in_state_info["parameter"]      = "0";
        $in_state_info["stdout_file"]    = "0";
        $in_state_info["success_exit"]   = "0";
        $in_state_info["ignore_errors"]  = "0";
        // stateコマンドの行番号を退避
        $in_state_line_no                = $in_line_no;
    }
    /////////////////////////////////////////////////////////////////////////////////
    // F0025
    // 処理内容
    //   stateコマンドのパラメータでエラーがあった場合の処理
    // 
    // パラメータ
    //   $in_mysts:         対話ファイルのチェック状態
    //   $in_result_code:   モジュール戻り値
    //   $in_state_info:    stateコマンドパラメータ設定有無配列
    //   $in_state_line_no: stateコマンド行番号 退避用
    //   $in_error_code:    エラーメッセージコード
    //   $ina_error_info:   エラーメッセージパラメータ
    //   
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function errorstateCommand(&$in_mysts,
                               &$in_result_code,
                               &$in_state_info,
                               &$in_state_line_no,
                               $in_error_code,
                               $ina_error_info){
        // エラーメッセージを出力
        $msgstr = $this->lv_objMTS->getSomeMessage($in_error_code,$ina_error_info);
        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        $in_result_code = false;
        $in_mysts       = 3;
        // stateコマンドのパラメータ設定有無をクリア
        $this->initstateCommandInfo($in_state_info,$in_state_line_no,$in_state_line_no);
    }

    
    /////////////////////////////////////////////////////////////////////////////////
    // F0026
    // 処理内容
    //   対話ファイルの独自フォーマットをチェックする。
    // 
    // パラメータ
    //   $in_mysts:         対話ファイルのチェック状態
    //   $in_result_code:   モジュール戻り値
    //   $in_state_info:    stateコマンドパラメータ設定有無配列
    //   $in_state_line_no: stateコマンド行番号 退避用
    //   $in_file_name:     対話ファイル名
    //   $in_host_name:     ホスト名
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkstateCommand(&$in_state_sts,&$in_result_code,&$in_state_info,&$in_state_line_no,$in_file_name,$in_host_name){
        $error_list = array();
        // パラメータリスト
        $cmd_list  = array("state"       ,"prompt"       ,"shell"        ,"parameter",
                           "stdout_file" ,"success_exit" ,"ignore_errors");

        // パラメータ判定リスト
        $ok_list[] = array("state"=>"1"     , "prompt"=>"1"      , "shell"=>"?"        ,
                           "parameter"=>"?" ,"stdout_file"=>"?"  , "success_exit"=>"?" ,
                           "ignore_errors"=>"?");
        $ok_list[] = array("state"=>"1"     , "prompt"=>"1"      , "shell"=>"1"        ,
                           "parameter"=>"?" ,"stdout_file"=>"?"  , "success_exit"=>"?" ,
                           "ignore_errors"=>"?");
        $ok_list[] = array("state"=>"1"     , "prompt"=>"1"      , "shell"=>"?"        ,
                           "parameter"=>"1" , "stdout_file"=>"?" , "success_exit"=>"?" ,
                           "ignore_errors"=>"?");

        $result_code = false;
        foreach($ok_list as $pattern){
            $pattern_ret = true;
            foreach($cmd_list as $cmd){
                // 任意入力のパラメータでないか判定
                if($pattern[$cmd] != "?"){
                    // パラメータの設定が正しいか判定
                    if($pattern[$cmd] != $in_state_info[$cmd]){
                        // エラーになったパラメータを退避
                        $error_list[$cmd] = "1";
                        $pattern_ret = false;
                    }
                }
            }
            // チェックがOKなら次のパターンチェックはしない
            if($pattern_ret == true){
                $result_code = true;
                break;
            }
        }
        if($result_code == false){
            $error_str = "";
            // エラーになったパラメータを取出
            foreach($error_list as $cmd=>$val){
                if(strlen($error_str) != 0)
                    $error_str = $error_str . "/$cmd:";
                else
                    $error_str = $error_str . "$cmd:";
            }
            // エラーになったパラメータをメッセージで表示
            $this->errorstateCommand($in_state_sts,$in_result_code,$in_state_sts,$in_state_line_no,
                                     "ITAANSIBLEH-ERR-55254",
                                     array($in_host_name,
                                           basename($in_file_name),
                                           $in_state_line_no,
                                           $error_str));
        }
        $this->initstateCommandInfo($in_state_info,$in_state_line_no,0);
        return $result_code;
    }

// 作業パターンの出力結果から特定の文字列でOK/NGを判断するコマンド(command)の処理追加
    /////////////////////////////////////////////////////////////////////////////////
    // F0051
    // 処理内容
    //   commandコマンドのパラメータ情報初期化
    // 
    // パラメータ
    //   $in_command_info:    commandコマンドパラメータ設定有無配列
    //   $in_command_line_no: commandコマンド行番号 退避用
    //   $in_line_no:         commandコマンド行番号
    //   
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function initCommandInfo(&$in_command_info,&$in_command_line_no,$in_line_no) {
        // commandコマンドのパラメータ設定有無をクリア
        $in_command_info                  = array();
        $in_command_info["command"]       = "0";
        $in_command_info["prompt"]        = "0";
        $in_command_info["timeout"]       = "0";
        $in_command_info["register"]      = "0";
        $in_command_info["with_items"]    = "0";
        $in_command_info["when"]          = "0";
        $in_command_info["failed_when"]   = "0";
        $in_command_info["exec_when"]     = "0";
        // commandコマンドの行番号を退避
        $in_command_line_no               = $in_line_no;
    }
    /////////////////////////////////////////////////////////////////////////////////
    // F0052
    // 処理内容
    //   commandコマンドのパラメータでエラーがあった場合の処理
    // 
    // パラメータ
    //   $in_mysts:           対話ファイルのチェック状態
    //   $in_result_code:     モジュール戻り値
    //   $in_command_info:    commandコマンドパラメータ設定有無配列
    //   $in_command_line_no: commandコマンド行番号 退避用
    //   $in_error_code:      エラーメッセージコード
    //   $ina_error_info:     エラーメッセージパラメータ
    //   
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function errorCommand( &$in_mysts,
                           &$in_result_code,
                           &$in_command_info,
                           &$in_command_line_no,
                           $in_error_code,
                           $ina_error_info){
        // エラーメッセージを出力
        $msgstr = $this->lv_objMTS->getSomeMessage($in_error_code,$ina_error_info);
        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        $in_result_code = false;
        $in_mysts       = 3;
        // commandのパラメータ設定有無をクリア
        $this->initCommandInfo($in_command_info,$in_command_line_no,$in_command_line_no);
    }

    
    /////////////////////////////////////////////////////////////////////////////////
    // F0053
    // 処理内容
    //   対話ファイルの独自フォーマットをチェックする。
    // 
    // パラメータ
    //   $in_mysts:           対話ファイルのチェック状態
    //   $in_result_code:     モジュール戻り値
    //   $in_command_info:    commandコマンドパラメータ設定有無配列
    //   $in_command_line_no: commandコマンド行番号 退避用
    //   $in_file_name:       対話ファイル名
    //   $in_host_name:       ホスト名
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkCommand(&$in_mysts,&$in_result_code,&$in_command_info,&$in_command_line_no,$in_file_name,$in_host_name){
        $error_list = array();
        // パラメータリスト
        $cmd_list  = array("command"       ,"prompt"        ,"timeout"       ,"register",
                           "with_items"    ,"when"          ,"failed_when"   ,"exec_when");

        // パラメータ判定リスト
        $ok_list[] = array("command"=>"1"     , "prompt"=>"1"     , "timeout"=>"?" ,
                           "register"=>"?"    , "with_items"=>"?" , "when"=>"?" ,
                           "failed_when"=>"?" , "exec_when"=>"?");

        $result_code = false;
        foreach($ok_list as $pattern){
            $pattern_ret = true;
            foreach($cmd_list as $cmd){
                // 任意入力のパラメータでないか判定
                if($pattern[$cmd] != "?"){
                    // パラメータの設定が正しいか判定
                    if($pattern[$cmd] != $in_command_info[$cmd]){
                        // エラーになったパラメータを退避
                        $error_list[$cmd] = "1";
                        $pattern_ret = false;
                    }
                }
            }
            // チェックがOKなら次のパターンチェックはしない
            if($pattern_ret == true){
                $result_code = true;
                break;
            }
        }
        if($result_code == false){
            $error_str = "";
            // エラーになったパラメータを取出
            foreach($error_list as $cmd=>$val){
                if(strlen($error_str) != 0)
                    $error_str = $error_str . "/$cmd:";
                else
                    $error_str = $error_str . "$cmd:";
            }
            // エラーになったパラメータをメッセージで表示
            $this->errorCommand($in_mysts,$in_result_code,$in_command_info,$in_command_line_no,
                                "ITAANSIBLEH-ERR-55279",
                                array($in_host_name, basename($in_file_name), $in_command_line_no, $error_str));
        }
        $this->initCommandInfo($in_command_info,$in_command_line_no,0);
        return $result_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansibleドライバ(legacy/pioneer)区分を記憶
    // パラメータ
    //   $in_val:      Ansibleドライバ(legacy/pioneer)区分
    //                   legacy:       DF_LEGACY_DRIVER_ID
    //                   pioneer:      DF_PIONEER_DRIVER_ID
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsibleDriverID($in_val){
        $this->lv_Ansible_driver_id = $in_val;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansibleドライバ(legacy/pioneer)区分を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   legacy:       DF_LEGACY_DRIVER_ID
    //   pioneer:      DF_PIONEER_DRIVER_ID
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsibleDriverID(){
        return($this->lv_Ansible_driver_id);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansible用 ベースディレクトリ名を記憶
    // パラメータ
    //   $in_base_name:  共有パス区分
    //                     ANSIBLE_SH_PATH_ITA:  Ansible作業用 ITA側
    //                     ANSIBLE_SH_PATH_ANS:  Ansible作業用 Ansible側
    //                     SYMPHONY_SH_PATH_ANS: symphony作業用 Ansible側
    //   $in_dir:        ベースディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsibleBaseDir($in_base_name,$in_dir){
        $this->lv_Ansible_base_Dir[$in_base_name]  = $in_dir;
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansible用 ベースディレクトリ名を取得
    // パラメータ
    //   $in_base_name:  共有パス区分
    //                     ANSIBLE_SH_PATH_ITA:  Ansible作業用 ITA側
    //                     ANSIBLE_SH_PATH_ANS:  Ansible作業用 Ansible側
    //                     SYMPHONY_SH_PATH_ANS: symphony作業用 Ansible側
    // 
    // 戻り値
    //   Ansible用 ベースディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsibleBaseDir($in_base_name){
        return($this->lv_Ansible_base_Dir[$in_base_name]);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      inディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_in_Dir($in_indir){
        $this->lv_Ansible_in_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   inディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_in_Dir(){
        return($this->lv_Ansible_in_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   child_playbooksディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      child_playbooksディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_child_playbooks_Dir($in_indir){
        $this->lv_Ansible_child_playbooks_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   child_playbooksディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   child_playbooksディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_child_playbooks_Dir(){
        return($this->lv_Ansible_child_playbooks_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   親PlayBook内の子PlayBookディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      child_playbooksディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setPlaybook_child_playbooks_Dir($in_dir){
        $this->lv_Playbook_child_playbooks_Dir = $in_dir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   親PlayBook内の子PlayBookディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   child_playbooksディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getPlaybook_child_playbooks_Dir(){
        return($this->lv_Playbook_child_playbooks_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   dialog_filesディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      dialog_filesディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_dialog_files_Dir($in_indir){
        $this->lv_Ansible_dialog_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   dialog_filesディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   dialog_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_dialog_files_Dir(){
        return($this->lv_Ansible_dialog_files_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   host_varsディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      host_varsディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_host_vars_Dir($in_indir){
        $this->lv_Ansible_host_vars_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   host_varsディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   host_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_host_vars_Dir(){
        return($this->lv_Ansible_host_vars_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   outディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      outディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_out_Dir($in_indir){
        $this->lv_Ansible_out_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   out_Dirディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   out_Dirディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_out_Dir(){
        return($this->lv_Ansible_out_Dir);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   tmpディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      tmpディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_tmp_Dir($in_indir){
        $this->lv_Ansible_tmp_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   tmpディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   tmpディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_tmp_Dir(){
        return($this->lv_Ansible_tmp_Dir);
    }
       
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   original_dialog_filesディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      original_dialog_filesディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_original_dialog_files_Dir($in_indir){
            $this->lv_Ansible_original_dialog_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   dialog_filesディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   dialog_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_original_dialog_files_Dir(){
        return($this->lv_Ansible_original_dialog_files_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   original_hosts_varsディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      original_hosts_varsディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_original_hosts_vars_Dir($in_indir){
        $this->lv_Ansible_original_hosts_vars_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   original_hosts_varsディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_original_hosts_vars_Dir(){
        return($this->lv_Ansible_original_hosts_vars_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   子PlayBook格納ディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      子PlayBook格納ディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_child_playbook_Dir($in_indir){
        $this->lv_ita_child_playbooks_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   子PlayBook格納ディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_child_playbook_Dir(){
        return($this->lv_ita_child_playbooks_Dir);
    }


    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   対話ファイル格納ディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      対話ファイル格納ディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_dialog_files_Dir($in_indir){
        $this->lv_ita_dialog_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   対話ファイル格納ディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_dialog_files_Dir(){
        return($this->lv_ita_dialog_files_Dir);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   hostsファイル名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   hostsファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_hosts_file(){
        $file = $this->lv_Ansible_in_Dir . "/" . self::LC_ANS_HOSTS_FILE;
        return($file);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   playbookファイル名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   playbookファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_playbook_file(){
        $file = $this->lv_Ansible_in_Dir . "/" . self::LC_ANS_PLAYBOOK_FILE;
        return($file);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ホスト変数定義ファイル名を取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    // 
    // 戻り値
    //   ホスト変数定義ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_host_var_file($in_hostname){
        $file = sprintf(self::LC_ANS_HOST_VAR_FILE_MK,$this->getAnsible_host_vars_Dir(),$in_hostname);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ホスト変数定義ファイル名(pioneer)を取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    // 
    // 戻り値
    //   ホスト変数定義ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_org_host_var_file($in_hostname){
        $file = sprintf(self::LC_ANS_ORG_HOST_VAR_FILE_MK,$this->getAnsible_original_hosts_vars_Dir(),$in_hostname);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   子PlayBookファイル名(Legacy)を取得
    //   
    // パラメータ
    //   $in_filename:       子PlayBookファイル名(Legacy)
    //   $in_pkey:           子PlayBookファイル Pkey
    // 
    // 戻り値
    //   子PlayBookファイル名(Legacy)
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_child_playbiook_file($in_pkey,$in_filename){
        $intNumPadding = 10;

        // Ansible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
        $file = sprintf(self::LC_ANS_CHILD_PLAYBOOK_FILE_MK,
                        $this->getAnsible_child_playbooks_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   親playbook内の子playbookパスを取得
    // パラメータ
    //   $in_pkey:    子playbookファイル Pkey
    //   $in_file:    子playbookファイル
    // 
    // 戻り値
    //   親playbook内の子playbookパス
    ////////////////////////////////////////////////////////////////////////////////
    function getPlaybook_child_playbook_file($in_pkey,$in_file){
        $intNumPadding = 10;

        $file = sprintf(self::LC_PLAYBOOK_PLAYBOOK_CHILD_FILE_MK,
                        $this->getPlaybook_child_playbooks_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA 対話ファイル名(Pioneer)を取得
    // パラメータ
    //   $in_key:        対話ファイルのPkey(データベース)
    //   $in_filename:   対話ファイル名    
    // 
    // 戻り値
    //   ITA管理 対話ファイル名(Pioneer)
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_dialog_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_DIALOG_FILES_DIR_MK,
                        $this->getITA_dialog_files_Dir(),
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   加工前の対話ファイル名(Pioneer)を取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    //   $in_pkey:           対話ファイル Pkey
    //   $in_filename:       対話ファイル名
    // 
    // 戻り値
    //   対話ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_org_dialog_file($in_hostname,$in_pkey,$in_filename){
        $intNumPadding = 10;

        // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する。
        $file = sprintf(self::LC_ANS_ORG_DIALOG_FILE_MK,
                        $this->getAnsible_original_dialog_files_Dir(),
                        $in_hostname,
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ホスト毎の加工前の対話ファイル(Pioneer)格納ディレクトリを取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    // 
    // 戻り値
    //   対話ファイル(Pioneer)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_org_dialog_file_host_Dir($in_hostname){
        $file = sprintf(self::LC_ANS_ORG_DIALOG_FILE_HOST_DIR_MK,$this->getAnsible_original_dialog_files_Dir(),$in_hostname);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   加工後の対話ファイル名(Pioneer)を取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    //   $in_pkey:           対話ファイル Pkey
    //   $in_filename:       対話ファイル名
    // 
    // 戻り値
    //   対話ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_dialog_file($in_hostname,$in_pkey,$in_filename){
        $intNumPadding = 10;

        // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する。
        $file = sprintf(self::LC_ANS_DIALOG_FILE_MK,
                        $this->getAnsible_dialog_files_Dir(),
                        $in_hostname,
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ), 
                        $in_filename);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ホスト毎の加工後の対話ファイル(Pioneer)格納ディレクトリを取得
    // パラメータ
    //   $in_hostname:       ホスト名(IPアドレス)
    //
    // 戻り値
    //   対話ファイル(Pioneer)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_dialog_file_host_Dir($in_hostname){
        $file = sprintf(self::LC_ANS_DIALOG_FILE_HOST_DIR_MK,$this->getAnsible_dialog_files_Dir(),$in_hostname);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA 子PlayBookファイル名(Legacy)を取得
    // パラメータ
    //   $in_key:        子PlayBookファイルのPkey(データベース)
    //   $in_filename:   子PlayBookファイル名    
    // 
    // 戻り値
    //   ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_child_playbiook_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_CHILD_PLAYBOOKS_DIR_MK,
                        $this->getITA_child_playbook_Dir(),
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }

    function DebugLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        require ($root_dir_path . $log_output_php);
    }

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        require ($root_dir_path . $log_output_php);
        if($this->getAnsible_out_Dir() != ""){
            $logfile = $this->getAnsible_out_Dir() . "/" . "error.log";
            $filepointer=fopen(  $logfile, "a");
            flock($filepointer, LOCK_EX);
            fputs($filepointer, $p3 . "\n" );
            flock($filepointer, LOCK_UN);
            fclose($filepointer);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0015
    // 処理内容
    //   legacyで実行するHOSTをデータベースより取得する。
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $in_operation_id:      オペレーションID
    //   $ina_hostlist:         ホスト一覧返却配列
    //                          [管理システム項番]=ホスト名(IP);
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧返却配列
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //   $ina_hostostypelist:   ホスト毎OS種別一覧返却配列
    //                          [ホスト名(IP)]=$row[OS種別];
    //   既存のデータが重なるが、今後の開発はこの変数を使用する。
    //   $ina_hostinfolist:     機器一覧ホスト情報
    //                          [ホスト名(IP)]=HOSTNAME=>''             ホスト名
    //                                         PROTOCOL_ID=>''	        接続プロトコル
    //                                         LOGIN_USER=>''           ログインユーザー名
    //                                         LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
    //                                                                  1:管理(●)   N0:未管理
    //                                         LOGIN_PW=>''             パスワード
    //                                                                  パスワード管理が1の場合のみ有効
    //                                         LOGIN_AUTH_TYPE=>''      Ansible認証方式
    //                                                                  2:パスワード認証 1:鍵認証
    //                                         WINRM_PORT=>''           WinRM接続プロトコル
    //                                         OS_TYPE_ID=>''           OS種別
    //                                         SSH_EXTRA_ARGS=>         SSHコマンド 追加パラメータ
    //                                         SSH_KEY_FILE=>           SSH秘密鍵ファイル
    //                                         SYSTEM_ID=>              項番
    //                                         WINRM_SSL_CA_FILE=>      サーバー証明書ファイル
    //                                         HOSTS_EXTRA_ARGS=>       インベントリファイル 追加パラメータ
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBHostList($in_pattern_id,$in_operation_id,
                           &$ina_hostlist,
                           &$ina_hostprotcollist,
                           &$ina_hostostypelist,
                           &$ina_hostinfolist)
    {
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // C_STM_LISTに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "  TBL_1.PHO_LINK_ID, \n" .
               "  TBL_1.SYSTEM_ID, \n" .
               "  TBL_2.HOSTNAME, \n" .
               "  TBL_2.IP_ADDRESS, \n" .
               "  TBL_2.LOGIN_USER, \n" .
               "  TBL_2.LOGIN_PW, \n" .
               "  TBL_2.CONN_SSH_KEY_FILE, \n" .
               "  TBL_2.SSH_EXTRA_ARGS, \n" .
               "  TBL_2.WINRM_PORT, \n" .
               "  TBL_2.LOGIN_PW_HOLD_FLAG, \n" .
               "  TBL_2.LOGIN_AUTH_TYPE, \n" .
               "  TBL_2.OS_TYPE_ID, \n" .
               "  TBL_2.DISUSE_FLAG, \n" .
               "  TBL_2.WINRM_SSL_CA_FILE , \n".
               "  TBL_2.HOSTS_EXTRA_ARGS, \n".
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_3.PROTOCOL_NAME \n" .
               "    FROM \n" .
               "      B_PROTOCOL TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.PROTOCOL_ID = TBL_2.PROTOCOL_ID AND \n" .
               "      TBL_3.DISUSE_FLAG = '0' \n" .
               "  ) AS PROTOCOL_NAME \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_4.PHO_LINK_ID, \n" .
               "      TBL_4.SYSTEM_ID \n" .
               "    FROM \n" .
               "      $this->lv_ansible_pho_linkDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_4.PATTERN_ID   = :PATTERN_ID   AND \n" .
               "      TBL_4.DISUSE_FLAG  = '0' \n" .
               "  ) TBL_1 \n" .
               "LEFT OUTER JOIN C_STM_LIST TBL_2 ON ( TBL_1.SYSTEM_ID = TBL_2.SYSTEM_ID ) \n" .
               "ORDER BY TBL_2.IP_ADDRESS; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('OPERATION_NO_UAPK'=>$in_operation_id,
                                  'PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
    
        $ina_hostlist = array();
        $ina_hostostypelist = array();
        $ina_hostprotcollist = array();
        $ina_hostinfolist = array();
        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                if(strlen($row['IP_ADDRESS'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56205",
                                                               array($row['SYSTEM_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                if(strlen($row['HOSTNAME'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56202",
                                                               array($row['IP_ADDRESS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }

                // 認証方式の設定値確認
                $driver_id = $this->getAnsibleDriverID();
                switch($this->getAnsibleDriverID()) {
                case DF_PIONEER_DRIVER_ID:
                    break;
                case DF_LEGACY_DRIVER_ID:
                case DF_LEGACY_ROLE_DRIVER_ID:
                    if(strlen($row['LOGIN_AUTH_TYPE']) === 0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70040",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                    break;
                }

                $login_auth_type = '';
                if(strlen($row['LOGIN_AUTH_TYPE']) === 0){
                    // 未設定なのでデフォルト値設定
                    $login_auth_type = self::LC_LOGIN_AUTH_TYPE_DEF;  // 鍵認証
                }
                else{
                    switch($row['LOGIN_AUTH_TYPE']){
                    case self::LC_LOGIN_AUTH_TYPE_KEY:               // 鍵認証
                    case self::LC_LOGIN_AUTH_TYPE_PW:                // パスワード認証
                        $login_auth_type = $row['LOGIN_AUTH_TYPE'];
                        break;
                    }
                }

                // パスワード管理フラグの設定値確認
                $pw_hold_flag = '';
                if(@strlen($row['LOGIN_PW_HOLD_FLAG']) === 0){
                    // 未設定なのでデフォルト値設定
                    $pw_hold_flag = self::LC_LOGIN_PW_HOLD_FLAG_DEF;  // パスワード管理なし
                }
                else{
                    switch($row['LOGIN_PW_HOLD_FLAG']){
                    case self::LC_LOGIN_PW_HOLD_FLAG_ON: // パスワード管理あり
                        $pw_hold_flag = $row['LOGIN_PW_HOLD_FLAG']; 
                        break;
                    }
                }
                if($pw_hold_flag == ''){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70041",
                                                               array($row['IP_ADDRESS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                // 認証方式がパスワード認証の場合に管理パスワードがありでパスワードが設定されているか判定
                if($login_auth_type === self::LC_LOGIN_AUTH_TYPE_PW){
                    // パスワード管理ありの判定
                    if($pw_hold_flag != self::LC_LOGIN_PW_HOLD_FLAG_ON){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70042",
                                                               array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        unset($objQuery);
                        return false;
                    }   
                    // パスワード登録の判定
                    if(strlen($row['LOGIN_PW'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70043",
                                                               array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        unset($objQuery);
                        return false;
                    }
                }
                // パスワード管理ありでパスワードが設定されているか判定
                // パスワード管理ありの判定
                if($pw_hold_flag == self::LC_LOGIN_PW_HOLD_FLAG_ON){
                    // パスワード登録の判定
                    if(strlen($row['LOGIN_PW'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70043",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                    // パスワード退避
                    $login_pass = ky_decrypt($row['LOGIN_PW']);
               
                }
                else{
                    // パスワード未設定を退避
                    $login_pass = self::LC_ANS_UNDEFINE_NAME;
                }
                        
                switch($this->getAnsibleDriverID()){
                case DF_LEGACY_DRIVER_ID:
                case DF_LEGACY_ROLE_DRIVER_ID:
                    if($row['PROTOCOL_NAME']===null){
                        $protocol = self::LC_ANS_UNDEFINE_NAME;
                    }
                    else{
                        $protocol = $row['PROTOCOL_NAME'];
                    }
                    if(strlen($row['LOGIN_USER'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56203",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        unset($objQuery);
                        return false;
                    }
                    $login_user = $row['LOGIN_USER'];
                    break;
                case DF_PIONEER_DRIVER_ID:
                    if($row['PROTOCOL_NAME']===null){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56104",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        
                        unset($objQuery);
                        return false;
                    }
                    $protocol = $row['PROTOCOL_NAME'];
                    if(strlen($row['LOGIN_USER'])==0){
                        $login_user = self::LC_ANS_UNDEFINE_NAME;
                    }
                    else{
                        $login_user = $row['LOGIN_USER'];
                    }
                    if(strlen($row['OS_TYPE_ID'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56204",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);    
                        unset($objQuery);
                        return false;
                    }
                    // IPアドレス,OS種別の配列作成 pioneerの場合のみ作成
                    $ina_hostostypelist[$row['IP_ADDRESS']]=$row['OS_TYPE_ID'];

                    break;
                }
                // IPアドレスの配列作成
                $ina_hostlist[$row['SYSTEM_ID']]=$row['IP_ADDRESS'];
                // IPアドレス,ホスト名,プロトコル,ログインユーザー,パスワードの配列作成
                $ina_hostprotcollist[$row['IP_ADDRESS']][$row['HOSTNAME']][$protocol][$login_user]=$login_pass;

                // WINRM接続プロトコル配列作成
                if(strlen($row['WINRM_PORT']) === 0)
                {   //WINRM接続プロトコルが空白の場合はデフォルト値を設定
                    $winrm_port = self::LC_WINRM_PORT;
                }
                else{
                    $winrm_port = $row['WINRM_PORT'];
                }
                $ina_hostinfolist[$row['IP_ADDRESS']]['WINRM_SSL_CA_FILE']  = $row['WINRM_SSL_CA_FILE'];
                $ina_hostinfolist[$row['IP_ADDRESS']]['HOSTS_EXTRA_ARGS']   = $row['HOSTS_EXTRA_ARGS'];

                // SSH認証ファイル/SSH_EXTRA_ARGSと機器一覧の項番を退避
                $ina_hostinfolist[$row['IP_ADDRESS']]['SSH_EXTRA_ARGS']     = $row['SSH_EXTRA_ARGS'];
                $ina_hostinfolist[$row['IP_ADDRESS']]['SSH_KEY_FILE']       = $row['CONN_SSH_KEY_FILE'];
                $ina_hostinfolist[$row['IP_ADDRESS']]['SYSTEM_ID']          = $row['SYSTEM_ID'];

                $ina_hostinfolist[$row['IP_ADDRESS']]['HOSTNAME']           = $row['HOSTNAME'];  //ホスト名
                $ina_hostinfolist[$row['IP_ADDRESS']]['PROTOCOL_ID']        = $protocol;         //接続プロトコル
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_USER']         = $login_user;       //ログインユーザー名
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_PW']           = $login_pass;       //パスワード
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_PW_HOLD_FLAG'] = $pw_hold_flag;     //パスワード管理フラグ
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_AUTH_TYPE']    = $login_auth_type;  //Ansible認証方式
                $ina_hostinfolist[$row['IP_ADDRESS']]['WINRM_PORT']         = $winrm_port;       //WINRM接続プロトコル
                $ina_hostinfolist[$row['IP_ADDRESS']]['OS_TYPE_ID']         = $row['OS_TYPE_ID'];//OS種別

            }
            // 作業対象ホスト管理に登録されているホストが管理対象システム一覧(C_STM_LIST )に未登録
            elseif($row['DISUSE_FLAG']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56105",
                                                           array($row['PHO_LINK_ID'],
                                                                 $row['SYSTEM_ID'] ));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56106",
                                                       array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
        if (count($ina_hostlist) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56107",
                                                       array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0016-1
    // 処理内容
    //   ansibleで実行する変数をデータベースより取得する。(Role専用)
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $in_operation_id:      オペレーションID
    //   $ina_host_vars:        変数一覧返却配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBRoleVarList($in_pattern_id,$in_operation_id,&$ina_host_vars,
                          &$ina_MultiArray_vars_list,&$ina_All_vars_list)
    {
        $vars_assign_seq_list = array();
        $child_vars_list = array();
        $varerror_flg = true;
        // B_ANSIBLE_LNS_PATTERN_VARS_LINKに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT                                                                                      \n" .
               "       TBL.*                                                                                \n" .
               "FROM                                                                                        \n" .
               "(                                                                                           \n" .
               "SELECT                                                                                      \n" .
               "  TBL_1.ASSIGN_ID,                                                                          \n" .
               "  TBL_1.SYSTEM_ID,                                                                          \n" .
               "  TBL_1.VARS_ENTRY,                                                                         \n" .
               "  TBL_1.ASSIGN_SEQ,                                                                         \n" .
               "  TBL_2.VARS_NAME_ID AS VARS_NAME_ID,                                                       \n" .
               "  TBL_1.COL_SEQ_COMBINATION_ID,                                                             \n" .
               "  TBL_3.COL_COMBINATION_MEMBER_ALIAS,                                                       \n" .
               "  TBL_3.COL_SEQ_VALUE,                                                                      \n" .
               "  TBL_4.ARRAY_MEMBER_ID,                                                                    \n" .
               "  TBL_4.PARENT_VARS_KEY_ID,                                                                 \n" .
               "  TBL_4.VARS_KEY_ID,                                                                        \n" .
               "  TBL_4.VARS_NAME     AS MEMBER_VARS_NAME,                                                  \n" .
               "  TBL_4.ARRAY_NEST_LEVEL,                                                                   \n" .
               "  TBL_4.ASSIGN_SEQ_NEED,                                                                    \n" .
               "  TBL_4.COL_SEQ_NEED,                                                                       \n" .
               "  TBL_4.MEMBER_DISP,                                                                        \n" .
               "  TBL_4.MAX_COL_SEQ,                                                                        \n" .
               "  TBL_4.VRAS_NAME_PATH,                                                                     \n" .
               "  TBL_4.VRAS_NAME_ALIAS,                                                                    \n" .
               "  TBL_5.CHILD_VARS_NAME_ID,                                                                 \n" .
               "  TBL_2.DISUSE_FLAG   AS PTN_VARS_LINK_DISUSE_FLAG,                                         \n" .
               "  TBL_3.DISUSE_FLAG   AS MEMBER_COL_COMB_DISUSE_FLAG,                                       \n" .
               "  TBL_4.DISUSE_FLAG   AS ARRAY_MEMBER_DISUSE_FLAG,                                          \n" .
               "  TBL_5.DISUSE_FLAG   AS CHILD_VARS_DISUSE_FLAG,                                            \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      COUNT(*)                                                                              \n" .
               "    FROM                                                                                    \n" .
               "      B_ANSIBLE_LRL_PHO_LINK TBL_4                                                          \n" .
               "    WHERE                                                                                   \n" .
               "      TBL_4.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND                                      \n" .
               "      TBL_4.PATTERN_ID        = :PATTERN_ID        AND                                      \n" .
               "      TBL_4.SYSTEM_ID         = TBL_1.SYSTEM_ID    AND                                      \n" .
               "      TBL_4.DISUSE_FLAG       = '0'                                                         \n" .
               "  ) AS PHO_LINK_HOST_COUNT,                                                                 \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      TBL_3.IP_ADDRESS                                                                      \n" .
               "    FROM                                                                                    \n" .
               "      C_STM_LIST TBL_3                                                                      \n" .
               "    WHERE                                                                                   \n" .
               "      TBL_3.SYSTEM_ID   = TBL_1.SYSTEM_ID          AND                                      \n" .
               "      TBL_3.DISUSE_FLAG = '0'                                                               \n" .
               "  ) AS IP_ADDRESS,                                                                          \n" .
               "  TBL_1.VARS_LINK_ID,                                                                       \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      TBL_4.VARS_NAME                                                                       \n" .
               "    FROM                                                                                    \n" .
               "      B_ANSIBLE_LRL_VARS_MASTER TBL_4                                                       \n" .
               "    WHERE                                                                                   \n" .
               "      TBL_4.VARS_NAME_ID  = TBL_2.VARS_NAME_ID     AND                                      \n" .
               "      TBL_4.DISUSE_FLAG   = '0'                                                             \n" .
               "  ) AS VARS_NAME,                                                                           \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      COUNT(*)                                                                              \n" .
               "    FROM                                                                                    \n" .
               "      B_ANSIBLE_LRL_VARS_ASSIGN TBL_6                                                       \n" .
               "    WHERE                                                                                   \n" .
               "      TBL_6.OPERATION_NO_UAPK = :OPERATION_NO_UAPK  AND                                     \n" .
               "      TBL_6.PATTERN_ID        = :PATTERN_ID         AND                                     \n" .
               "      TBL_6.SYSTEM_ID         = TBL_1.SYSTEM_ID     AND                                     \n" .
               "      TBL_6.VARS_LINK_ID      = TBL_1.VARS_LINK_ID  AND                                     \n" .
               "      TBL_6.DISUSE_FLAG       = '0'                                                         \n" .
               "    GROUP BY OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,VARS_LINK_ID                            \n" .
               "  ) AS VARS_NAME_COUNT,                                                                     \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      TBL_7.VARS_ATTRIBUTE_01                                                               \n" .
               "    FROM                                                                                    \n" .
               "      B_ANSIBLE_LRL_VARS_MASTER TBL_7                                                       \n" .
               "    WHERE                                                                                   \n" .
               "      TBL_7.VARS_NAME_ID = TBL_2.VARS_NAME_ID AND                                           \n" .
               "      TBL_7.DISUSE_FLAG = '0'                                                               \n" .
               "  ) AS VARS_ATTRIBUTE_01                                                                    \n" .
               "FROM                                                                                        \n" .
               "  (                                                                                         \n" .
               "    SELECT                                                                                  \n" .
               "      TBL.ASSIGN_ID,                                                                        \n" .
               "      TBL.SYSTEM_ID,                                                                        \n" .
               "      TBL.VARS_LINK_ID,                                                                     \n" .
               "      TBL.COL_SEQ_COMBINATION_ID,                                                           \n" .
               "      TBL.VARS_ENTRY,                                                                       \n" .
               "      TBL.ASSIGN_SEQ                                                                        \n" .
               "    FROM                                                                                    \n" .
               "      B_ANSIBLE_LRL_VARS_ASSIGN TBL                                                         \n" .
               "    WHERE                                                                                   \n" .
               "      TBL.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND                                        \n" .
               "      TBL.PATTERN_ID        = :PATTERN_ID        AND                                        \n" .
               "      TBL.DISUSE_FLAG       = '0'                                                           \n" .
               "  ) TBL_1                                                                                   \n" .
               " LEFT OUTER JOIN B_ANS_LRL_PTN_VARS_LINK   TBL_2 ON ( TBL_1.VARS_LINK_ID           =        \n" .
               "                                                      TBL_2.VARS_LINK_ID )                  \n" .
               " LEFT OUTER JOIN B_ANS_LRL_MEMBER_COL_COMB TBL_3 ON ( TBL_1.COL_SEQ_COMBINATION_ID =        \n" .
               "                                                      TBL_3.COL_SEQ_COMBINATION_ID )        \n" .
               " LEFT OUTER JOIN B_ANS_LRL_ARRAY_MEMBER    TBL_4 ON ( TBL_3.ARRAY_MEMBER_ID        =        \n" .
               "                                                      TBL_4.ARRAY_MEMBER_ID )               \n" .
               " LEFT OUTER JOIN B_ANSIBLE_LRL_CHILD_VARS  TBL_5 ON ( TBL_3.ARRAY_MEMBER_ID        =        \n" .
               "                                                      TBL_5.ARRAY_MEMBER_ID )               \n" .
               " ) TBL                                                                                      \n" .
               " ORDER BY IP_ADDRESS,VARS_NAME,ARRAY_NEST_LEVEL,VARS_KEY_ID,COL_SEQ_VALUE,ASSIGN_SEQ          ";
        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('OPERATION_NO_UAPK'=>$in_operation_id,
                                  'PATTERN_ID'=>$in_pattern_id));
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
    
        $ina_host_vars = array();
        $tgt_row = array();
        $array_tgt_row = array();
        while ( $row = $objQuery->resultFetch() ){
            switch($row['VARS_ATTRIBUTE_01']){
            case self::LC_VARS_ATTR_STRUCT:       // 多次元変数
                array_push( $array_tgt_row, $row );
                break;
            default:
                array_push( $tgt_row, $row );
                break;
            }
        }
        foreach( $tgt_row as $row )
        {
            $assign_seq = true;
            if(strlen($row['ASSIGN_SEQ']) === 0){
                $assign_seq = false;
            }

            if($row['PTN_VARS_LINK_DISUSE_FLAG']=='0'){
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] == 0){
                    continue;
                }

                if(strlen($row['IP_ADDRESS'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                if(strlen($row['VARS_NAME'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }

                // 下記予約変数が使用されているかチェックする。
                // 親playbook(pioneer)に埋め込まれるリモート接続コマンド用変数の名前
                // 親playbook(legacy)に埋め込まれるリモートログインのユーザー用変数の名前
                // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
                // 対話ファイルに埋め込まれるホスト名用変数の名前
                if(($row['VARS_NAME']==self::LC_ANS_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_OUTDIR_VAR_NAME)   ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME)  ||
                   ($row['VARS_NAME']==self::LC_ANS_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_PASSWD_VAR_NAME)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56201",
                                                                array($row['IP_ADDRESS'],
                                                                $row['VARS_NAME']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] > 0){
                    // 多次元変数以外か判定
                    if($row['VARS_ATTRIBUTE_01'] == self::LC_VARS_ATTR_STRUCT)
                    {                        
                        if($row['MEMBER_COL_COMB_DISUSE_FLAG'] !='0'){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90228",
                                                                        array($row['ASSIGN_ID']));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                        if($row['ARRAY_MEMBER_DISUSE_FLAG'] !='0'){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90229",
                                                                        array($row['ASSIGN_ID']));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                        if($row['CHILD_VARS_DISUSE_FLAG'] !='0'){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90230",
                                                                        array($row['ASSIGN_ID']));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                    }
                    
                    if($row['VARS_ATTRIBUTE_01'] == self::LC_VARS_ATTR_LIST)
                    {
                        // 配列変数以外で代入順序がnullの場合はエラーにする。
                        if($assign_seq === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90100",
                                                                        array($row['ASSIGN_ID'],
                                                                              $row['VARS_NAME']));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                    }
                    if($row['VARS_ATTRIBUTE_01'] == self::LC_VARS_ATTR_STD)
                    {
                        // 代入順序がnull以外の場合はエラーにする。
                        if($assign_seq === true){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90213",
                                                                       array($row['ASSIGN_ID'],
                                                                             $row['VARS_NAME']));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                    }

                    // 多次元変数以外か判定
                    if($row['VARS_ATTRIBUTE_01'] != self::LC_VARS_ATTR_STRUCT)
                    {
                        // 配列変数以外で代入順序が重複していないか判定する。
                        if(@count($vars_assign_seq_list[$row['IP_ADDRESS']]
                                                           [$row['VARS_NAME']]
                                                           [$row['ASSIGN_SEQ']]) != 0){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90101",
                                                                            array($row['ASSIGN_ID'],
                                                                                  $vars_assign_seq_list[$row['IP_ADDRESS']][$row['VARS_NAME']][$row['ASSIGN_SEQ']],
                                                                                  $row['VARS_NAME'],
                                                                                  $row['ASSIGN_SEQ']));
                                                                                  
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                        // 配列変数以外で代入順序の重複チェックリスト作成
                        $vars_assign_seq_list[$row['IP_ADDRESS']]
                                             [$row['VARS_NAME']]
                                             [$row['ASSIGN_SEQ']] = $row['ASSIGN_ID'];

                        if($row['VARS_ATTRIBUTE_01'] == self::LC_VARS_ATTR_STD){
                            //ホスト変数配列作成
                            $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']]=$row['VARS_ENTRY'];
                        }
                        else{
                            if(@count($ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']])==0){
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = "\n- " . $row['VARS_ENTRY'];
                            }
                            else{
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = 
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] .  "\n- " . $row['VARS_ENTRY'];
                            }
                        }
                    }
                    // 多次元変数の場合は具体値をここでは退避しない。
                }
            }
            elseif(strlen($row['PTN_VARS_LINK_DISUSE_FLAG'])==0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56109",
                                                           array($row['ASSIGN_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }


        // 変数未登録の場合もあるので fetch行数などはチェックしない。

        // DBアクセス事後処理
        unset($objQuery);


        if($varerror_flg === true){
            $varerror_flg = $this->getDBVarMultiArrayVarsList($array_tgt_row,$ina_MultiArray_vars_list);
        } 
        return $varerror_flg;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0016-2
    // 処理内容
    //   ansibleで実行する変数をデータベースより取得する。
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $in_operation_id:      オペレーションID
    //   $ina_host_vars:        変数一覧返却配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //   $ina_child_vars_list:  配列変数一覧返却配列
    //                          [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //   $ina_DB_child_vars_list: 
    //                          メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                          [ 変数名 ][メンバー変数名]=0
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBVarList($in_pattern_id,$in_operation_id,&$ina_host_vars,
                          &$ina_child_vars_list,&$ina_DB_child_vars_list)
    {
        $vars_assign_seq_list = array();
        $child_vars_list = array();
        $varerror_flg = true;
        switch($this->getAnsibleDriverID()){
        case DF_LEGACY_DRIVER_ID:
            // B_ANSIBLE_LNS_PATTERN_VARS_LINKに対するDISUSE_FLAG = '0'の
            // 条件はSELECT文に入れない。
            $sql = "SELECT \n" .
               "  TBL_1.ASSIGN_ID, \n" .
               "  TBL_1.SYSTEM_ID, \n" .
// 代入値管理のみあるホスト変数(作業対象ホストにない)はじくためのSELECT追加
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_ansible_pho_linkDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_4.PATTERN_ID   = :PATTERN_ID             AND \n" .
               "      TBL_4.SYSTEM_ID    = TBL_1.SYSTEM_ID         AND \n" .
               "      TBL_4.DISUSE_FLAG  = '0' \n" .
               "  ) AS PHO_LINK_HOST_COUNT, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_3.IP_ADDRESS \n" .
               "    FROM \n" .
               "      C_STM_LIST TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.SYSTEM_ID = TBL_1.SYSTEM_ID AND \n" .
               "      TBL_3.DISUSE_FLAG = '0' \n" .
               "  ) AS IP_ADDRESS, \n" .
               "  TBL_1.VARS_LINK_ID, \n" .
               "  TBL_2.VARS_NAME_ID, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_4.VARS_NAME \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_masterDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.VARS_NAME_ID = TBL_2.VARS_NAME_ID AND \n" .
               "      TBL_4.DISUSE_FLAG = '0' \n" .
               "  ) AS VARS_NAME, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_assignDB TBL_6 \n" .
               "    WHERE \n".
               "      TBL_6.OPERATION_NO_UAPK = :OPERATION_NO_UAPK  AND \n" .
               "      TBL_6.PATTERN_ID        = :PATTERN_ID         AND \n" .
               "      TBL_6.SYSTEM_ID         = TBL_1.SYSTEM_ID     AND \n" .
               "      TBL_6.VARS_LINK_ID      = TBL_1.VARS_LINK_ID  AND \n" .
               "      TBL_6.DISUSE_FLAG       = '0' \n" .
               "  ) AS VARS_NAME_COUNT, \n" .
               "  TBL_1.VARS_ENTRY, \n" .
               "  TBL_1.ASSIGN_SEQ, \n" .
               "  TBL_2.DISUSE_FLAG, \n" .
               "  '' AS VARS_ATTRIBUTE_01 \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_3.ASSIGN_ID, \n" .
               "      TBL_3.SYSTEM_ID, \n" .
               "      TBL_3.VARS_LINK_ID, \n" .
               "      TBL_3.VARS_ENTRY, \n" .
               "      TBL_3.ASSIGN_SEQ \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_assignDB TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_3.PATTERN_ID   = :PATTERN_ID   AND \n" .
               "      TBL_3.DISUSE_FLAG  = '0' \n" .
               "  ) TBL_1 \n" .
               "LEFT OUTER JOIN $this->lv_ansible_pattern_vars_linkDB TBL_2 ON ( TBL_1.VARS_LINK_ID = TBL_2.VARS_LINK_ID ) \n" .
               "ORDER BY IP_ADDRESS,VARS_NAME,ASSIGN_SEQ; \n";
            break;
        case DF_PIONEER_DRIVER_ID:
            $sql = "SELECT \n" .
               "  TBL_1.ASSIGN_ID, \n" .
               "  TBL_1.SYSTEM_ID, \n" .
// 代入値管理のみあるホスト変数(作業対象ホストにない)はじくためのSELECT追加
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_ansible_pho_linkDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_4.PATTERN_ID   = :PATTERN_ID             AND \n" .
               "      TBL_4.SYSTEM_ID    = TBL_1.SYSTEM_ID         AND \n" .
               "      TBL_4.DISUSE_FLAG  = '0' \n" .
               "  ) AS PHO_LINK_HOST_COUNT, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_3.IP_ADDRESS \n" .
               "    FROM \n" .
               "      C_STM_LIST TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.SYSTEM_ID = TBL_1.SYSTEM_ID AND \n" .
               "      TBL_3.DISUSE_FLAG = '0' \n" .
               "  ) AS IP_ADDRESS, \n" .
               "  TBL_1.VARS_LINK_ID, \n" .
               "  TBL_2.VARS_NAME_ID, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_4.VARS_NAME \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_masterDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.VARS_NAME_ID = TBL_2.VARS_NAME_ID AND \n" .
               "      TBL_4.DISUSE_FLAG = '0' \n" .
               "  ) AS VARS_NAME, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_assignDB TBL_6 \n" .
               "    WHERE \n".
               "      TBL_6.OPERATION_NO_UAPK = :OPERATION_NO_UAPK  AND \n" .
               "      TBL_6.PATTERN_ID        = :PATTERN_ID         AND \n" .
               "      TBL_6.SYSTEM_ID         = TBL_1.SYSTEM_ID     AND \n" .
               "      TBL_6.VARS_LINK_ID      = TBL_1.VARS_LINK_ID  AND \n" .
               "      TBL_6.DISUSE_FLAG       = '0' \n" .
               "  ) AS VARS_NAME_COUNT, \n" .
               "  TBL_1.VARS_ENTRY, \n" .
               "  TBL_1.ASSIGN_SEQ, \n" .
               "  TBL_2.DISUSE_FLAG, \n" .
               "  '' AS VARS_ATTRIBUTE_01 \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL_3.ASSIGN_ID, \n" .
               "      TBL_3.SYSTEM_ID, \n" .
               "      TBL_3.VARS_LINK_ID, \n" .
               "      TBL_3.VARS_ENTRY, \n" .
               "      TBL_3.ASSIGN_SEQ \n" .
               "    FROM \n" .
               "      $this->lv_ansible_vars_assignDB TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_3.PATTERN_ID   = :PATTERN_ID   AND \n" .
               "      TBL_3.DISUSE_FLAG  = '0' \n" .
               "  ) TBL_1 \n" .
               "LEFT OUTER JOIN $this->lv_ansible_pattern_vars_linkDB TBL_2 ON ( TBL_1.VARS_LINK_ID = TBL_2.VARS_LINK_ID ) \n" .
               "ORDER BY IP_ADDRESS,VARS_NAME,ASSIGN_SEQ; \n";
            break;
        }

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('OPERATION_NO_UAPK'=>$in_operation_id,
                                  'PATTERN_ID'=>$in_pattern_id));
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
    
        $ina_host_vars = array();
        $tgt_row = array();
        $array_tgt_row = array();
        while ( $row = $objQuery->resultFetch() ){
            array_push( $tgt_row, $row );
        }
        foreach( $tgt_row as $row )
        {
            $assign_seq = true;
            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_DRIVER_ID:
            case DF_PIONEER_DRIVER_ID:
                if(strlen($row['ASSIGN_SEQ']) === 0)
                    $assign_seq = false;
                break;
            }

            if($row['DISUSE_FLAG']=='0'){
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] == 0){
                    continue;
                }

                if($row['IP_ADDRESS']===null){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                if($row['VARS_NAME']===null){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                // 下記予約変数が使用されているかチェックする。
                // 親playbook(pioneer)に埋め込まれるリモート接続コマンド用変数の名前
                // 親playbook(legacy)に埋め込まれるリモートログインのユーザー用変数の名前
                // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
                // 対話ファイルに埋め込まれるホスト名用変数の名前
                if(($row['VARS_NAME']==self::LC_ANS_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_OUTDIR_VAR_NAME)   ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME)  ||
                   ($row['VARS_NAME']==self::LC_ANS_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_PASSWD_VAR_NAME)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56201",
                                                                array($row['IP_ADDRESS'],
                                                                $row['VARS_NAME']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] > 0){
                    // 代入順序がブランクの場合はスキップ
                    if($assign_seq === false){
                        continue;
                    }

                    switch($this->getAnsibleDriverID()){
                    case DF_LEGACY_DRIVER_ID:
                    case DF_PIONEER_DRIVER_ID:
                        // 配列変数以外で代入順序が重複していないか判定する。
                        if(@count($vars_assign_seq_list[$row['IP_ADDRESS']]
                                                       [$row['VARS_NAME']]
                                                       [$row['ASSIGN_SEQ']]) != 0){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90101",
                                                                        array($row['ASSIGN_ID'],
                                                                              $vars_assign_seq_list[$row['IP_ADDRESS']][$row['VARS_NAME']][$row['ASSIGN_SEQ']],
                                                                              $row['VARS_NAME'],
                                                                              $row['ASSIGN_SEQ']));

                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            unset($objQuery);
                            return false;
                        }
                        // 配列変数以外で代入順序の重複チェックリスト作成
                        $vars_assign_seq_list[$row['IP_ADDRESS']]
                                             [$row['VARS_NAME']]
                                             [$row['ASSIGN_SEQ']] = $row['ASSIGN_ID'];
                        break;
                    }    

                    // 複数具体値変数で具体値が1つの場合の不備対応
                    if(($row['VARS_NAME_COUNT'] == 1) && (@strlen($row['ASSIGN_SEQ']) == 0))
                    {
                        //ホスト変数配列作成
                        $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']]=$row['VARS_ENTRY'];
                    }
                    else{
                        if(@count($ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']])==0){
                            switch($this->getAnsibleDriverID()){
                            case DF_PIONEER_DRIVER_ID:
                                // Pioneerドライバの場合、先頭と末尾にダブルクォーテーションを付ける
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = "\n- " . "\"" . $row['VARS_ENTRY'] . "\"";
                                break;
                            case DF_LEGACY_DRIVER_ID:
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = "\n- " . $row['VARS_ENTRY'];
                                break;
                            }
                        }
                        else{
                            switch($this->getAnsibleDriverID()){
                            case DF_PIONEER_DRIVER_ID:
                                // Pioneerドライバの場合、先頭と末尾にダブルクォーテーションを付ける
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = 
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] .  "\n- " . "\"" . $row['VARS_ENTRY'] . "\"";
                                break;
                            case DF_LEGACY_DRIVER_ID:
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = 
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] .  "\n- " . $row['VARS_ENTRY'];
                                break;
                            }
                        }
                    }
                }
            }
            elseif($row['DISUSE_FLAG']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56109",
                                                           array($row['ASSIGN_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }

        // 代入順序がブランクの場合の処理は不要なのでコメントアウト
        foreach( $tgt_row as $row ){
            // 代入順序がブランクか判定
            $assign_seq = false;
            switch($this->getAnsibleDriverID()){
            case DF_LEGACY_DRIVER_ID:
            case DF_PIONEER_DRIVER_ID:
                if(strlen($row['ASSIGN_SEQ']) === 0)
                    $assign_seq = true;
                break;
            }

            if($row['DISUSE_FLAG']=='0'){
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] == 0){
                    continue;
                }

                if($row['IP_ADDRESS']===null){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                if($row['VARS_NAME']===null){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                // 親playbook(legacy)に埋め込まれるリモートログインのユーザー用変数の名前
                // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
                // 対話ファイルに埋め込まれるホスト名用変数の名前
                if(($row['VARS_NAME']==self::LC_ANS_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_OUTDIR_VAR_NAME)   ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME)  ||
                   ($row['VARS_NAME']==self::LC_ANS_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_PASSWD_VAR_NAME)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56201",
                                                                array($row['IP_ADDRESS'],
                                                                $row['VARS_NAME']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] > 0){
                    // 代入順序がブランク以外の場合はスキップ
                    if($assign_seq === false){
                        continue;
                    }

                    // 複数具体値変数で具体値が1つの場合の不備対応
                    if(($row['VARS_NAME_COUNT'] == 1) && (@strlen($row['ASSIGN_SEQ']) == 0))
                    {
                        //ホスト変数配列作成
                        $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']]=$row['VARS_ENTRY'];
                    }
                    else{
                        if(@count($ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']])==0){
                            switch($this->getAnsibleDriverID()){
                            case DF_PIONEER_DRIVER_ID:
                                // Pioneerドライバの場合、先頭と末尾にダブルクォーテーションを付ける
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = "\n- " . "\"" . $row['VARS_ENTRY'] . "\"";
                                break;
                            case DF_LEGACY_DRIVER_ID:
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = "\n- " . $row['VARS_ENTRY'];
                                break;
                            }
                        }
                        else{
                            switch($this->getAnsibleDriverID()){
                            case DF_PIONEER_DRIVER_ID:
                                // Pioneerドライバの場合、先頭と末尾にダブルクォーテーションを付ける
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = 
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] .  "\n- " . "\"" . $row['VARS_ENTRY'] . "\"";
                                break;
                            case DF_LEGACY_DRIVER_ID:
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] = 
                                $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']] .  "\n- " . $row['VARS_ENTRY'];
                                break;
                            }
                        }
                    }
                }
            }
            elseif($row['DISUSE_FLAG']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56109",
                                                           array($row['ASSIGN_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }
        // 変数未登録の場合もあるので fetch行数などはチェックしない。

        // DBアクセス事後処理
        unset($objQuery);

        return $varerror_flg;
    }

    function MultiArrayVarsToYamlFormatMain($ina_MultiArray_vars_list,&$in_str_hostvars,&$ina_parent_vars_list,
                                            $in_host_ipaddr,
                                           &$ina_legacy_Role_cpf_vars_list)
    {
        $ina_parent_vars_list = array();

        $in_str_hostvars = "";

        foreach( $ina_MultiArray_vars_list as $parent_vars_name=>$parent_vars_list ){
            // 該当ホストの具体値が未登録か判定
            if(@count($parent_vars_list[$in_host_ipaddr]) == 0){
                 continue;
            }
            $host_vars_array = $parent_vars_list[$in_host_ipaddr];

            $ina_parent_vars_list[$parent_vars_name] = 1;

            // 読替変数か判定。読替変数の場合は任意変数に置き換える
            if(@count($this->translationtable_list[$parent_vars_name]) != 0){
                $var = $this->translationtable_list[$parent_vars_name];
                $cur_str_hostvars = $var . ":" . "\n";
            }
            else{
                $cur_str_hostvars = $parent_vars_name . ":" . "\n";
            }

            $error_code   = "";
            $line         = "";           
            $before_vars  = "";
            $indent       = "";
            $nest_level   = 1;
            // 多次元配列の具体値構造体から。ホスト変数定義を生成する。
            $ret = $this->MultiArrayVarsToYamlFormatSub($host_vars_array,
                                                        $cur_str_hostvars,
                                                        $before_vars,
                                                        $indent,
                                                        $nest_level,
                                                        $error_code,$line,
                                                        $ina_legacy_Role_cpf_vars_list);
            if($ret === false){
                // 変数の具体値にコピー変数が使用されているかの判定では
                // メッセージを直接出力している。
                if($error_code == ""){
                    return false;
                }
                
                // エラーリスト
                $msgstr = $this->lv_objMTS->getSomeMessage($error_code,array($parent_vars_name));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            $in_str_hostvars = $in_str_hostvars . $cur_str_hostvars;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0016-3
    // 処理内容
    //   ansibleで実行する多次元変数をデータベースより取得する。
    // 
    // パラメータ
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBVarMultiArrayVarsList($in_tgt_row,&$ina_MultiArray_vars_list){
        $vars_seq_list = array();
        $parent_vars_list = array();
        foreach( $in_tgt_row as $row )
        {
            if($row['PTN_VARS_LINK_DISUSE_FLAG']=='0'){
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] == 0){
                    continue;
                }

                if(strlen($row['IP_ADDRESS']) == 0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }
                if(strlen($row['VARS_NAME']) == 0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    unset($objQuery);
                    return false;
                }


                if(($row['VARS_NAME']==self::LC_ANS_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_OUTDIR_VAR_NAME)   ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME)  ||
                   ($row['VARS_NAME']==self::LC_ANS_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_ANS_PASSWD_VAR_NAME)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56201",
                                                                array($row['IP_ADDRESS'],
                                                                $row['VARS_NAME']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }

                if(($row['ASSIGN_SEQ_NEED'] == 0) && (@strlen($row['ASSIGN_SEQ']) != 0)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90213",
                                                                array($row['ASSIGN_ID'],
                                                                      $row['COL_COMBINATION_MEMBER_ALIAS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }

                if(($row['ASSIGN_SEQ_NEED'] == 1) && (@strlen($row['ASSIGN_SEQ']) == 0)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90100",
                                                                array($row['ASSIGN_ID'],
                                                                      $row['COL_COMBINATION_MEMBER_ALIAS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }
                // 各変数の代入順序と説順序が重複していないか判定する。
                $dup_key = 0;
                if(@count($vars_seq_list[$row['IP_ADDRESS']]
                                         [$row['VARS_NAME']]
                                         [$row['COL_SEQ_COMBINATION_ID']]
                                         [$row['ASSIGN_SEQ']]) != 0){
                    $dup_key = $vars_seq_list[$row['IP_ADDRESS']]
                                             [$row['VARS_NAME']]
                                             [$row['COL_SEQ_COMBINATION_ID']]
                                             [$row['ASSIGN_SEQ']];
                }
                if($dup_key != 0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90216",
                                                               array($row['ASSIGN_ID'],
                                                                     $dup_key,
                                                                     $row['CHILD_VARS_NAME']));

                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    unset($objQuery);
                    return false;
                }
                // 各変数の代入順序と説順序が重複リスト生成
                $dup_key = $vars_seq_list[$row['IP_ADDRESS']]
                                         [$row['VARS_NAME']]
                                         [$row['COL_SEQ_COMBINATION_ID']]
                                         [$row['ASSIGN_SEQ']] = $row['ASSIGN_ID'];

                if(@count($ina_MultiArray_vars_list[$row['VARS_NAME']][$row['IP_ADDRESS']]) == 0){
                    $ina_MultiArray_vars_list[$row['VARS_NAME']][$row['IP_ADDRESS']] = array();
                }
                if(strlen($row['ASSIGN_SEQ']) == 0){
                    $var_type = 1;
                }
                else{
                    $var_type = 2;
                }
                // 多次元配列のメンバー変数へのパス配列を生成
                $var_path_array = array();
                $this->makeHostVarsPath($row['COL_COMBINATION_MEMBER_ALIAS'],$var_path_array);
                // 多次元配列の具体値情報をホスト変数ファイルに戻す為の配列作成
                $this->makeHostVarsArray($var_path_array,0,
                                         $ina_MultiArray_vars_list[$row['VARS_NAME']][$row['IP_ADDRESS']],
                                         $var_type,$row['VARS_ENTRY'],$row['ASSIGN_SEQ']);


            }
            elseif(strlen($row['PTN_VARS_LINK_DISUSE_FLAG'])==0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56109",
                                                           array($row['ASSIGN_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                unset($objQuery);
                return false;
            }
            else{
                // DISUSE_FLAG = '1'は読み飛ばし
                continue;
            }
            if(@count($parent_vars_list[$row['VARS_NAME_ID']]) == 0){
                $parent_vars_list[$row['VARS_NAME_ID']] = 1;
            }
        }        
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0017
    // 処理内容
    //   Legacyで実行する子PlayBookファイルをデータベースより取得する。
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $ina_child_playbooks:  子PlayBookファイル返却配列
    //                          ina_child_playbooks[INCLUDE順序][素材管理Pkey]=>素材ファイル
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBLegacyPlaybookList($in_pattern_id,&$ina_child_playbooks){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_ANSIBLE_LNS_PLAYBOOKに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.PLAYBOOK_MATTER_ID, \n" .
               "TBL_1.INCLUDE_SEQ, \n" .
               "TBL_2.PLAYBOOK_MATTER_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.PLAYBOOK_MATTER_ID, \n" .
               "      TBL3.INCLUDE_SEQ \n" .
               "    FROM \n" .
               "      B_ANSIBLE_LNS_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_ANSIBLE_LNS_PLAYBOOK TBL_2 ON \n" .
               "      ( TBL_1.PLAYBOOK_MATTER_ID = TBL_2.PLAYBOOK_MATTER_ID) \n" .
               "ORDER BY TBL_1.INCLUDE_SEQ; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }
        
        $ina_child_playbooks = array();
        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                if(strlen($row['PLAYBOOK_MATTER_FILE']) == 0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70026",
                                                               array($row['PLAYBOOK_MATTER_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            
                    unset($objQuery);
                    return false;
                }

                $ina_child_playbooks[$row['INCLUDE_SEQ']][$row['PLAYBOOK_MATTER_ID']]=$row['PLAYBOOK_MATTER_FILE'];
            }
            // 素材管理(B_ANSIBLE_LNS_PLAYBOOK)にPlaybookが未登録の場合
            elseif(strlen($row['DISUSE_FLAG'])==0)
            {
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56101",
                                                           array($row['LINK_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56102",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
        //対象playbookの数を確認
        if (count($ina_child_playbooks) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56103",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0018
    // 処理内容
    //   Pioneerで実行する対話ファイルをデータベースより取得する。
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $in_operation_id:      オペレーションID
    //   $ina_dialog_files:     子PlayBookファイル返却配列
    //                          $ina_dialog_files[ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
    //   $ina_hostostypelist:   ホスト毎のOS種別配列 
    //                          $ina_hostostypelist[IP_ADDRESS]=>OS種別
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBPioneerDialogFileList($in_pattern_id,$in_operation_id,&$ina_dialog_files,$ina_hostostypelist){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $sql = "SELECT                                                 \n" .
               "  TBL_1.LINK_ID,                                       \n" .
               "  TBL_1.INCLUDE_SEQ,                                   \n" .
               "  TBL_2.OS_TYPE_ID,                                    \n" .
               "  TBL_2.DIALOG_MATTER_ID,                              \n" .
               "  TBL_2.DIALOG_MATTER_FILE                             \n" .
               "FROM                                                   \n" .
               "  (                                                    \n" .
               "    SELECT                                             \n" .
               "      LINK_ID,                                         \n" .
               "      DIALOG_TYPE_ID,                                  \n" .
               "      INCLUDE_SEQ                                      \n" .
               "    FROM                                               \n" .
               "      B_ANSIBLE_PNS_PATTERN_LINK                       \n" .
               "    WHERE                                              \n" .
               "      PATTERN_ID  = :PATTERN_ID AND                    \n" .
               "      DISUSE_FLAG = '0'                                \n" .
               "  )TBL_1                                               \n" .
               "LEFT OUTER JOIN                                        \n" .
               "  (                                                    \n" .
               "    SELECT                                             \n" .
               "      OS_TYPE_ID,                                      \n" .
               "      DIALOG_TYPE_ID,                                  \n" .
               "      DIALOG_MATTER_ID,                                \n" .
               "      DIALOG_MATTER_FILE                               \n" .
               "    FROM                                               \n" .
               "      B_ANSIBLE_PNS_DIALOG                             \n" .
               "    WHERE                                              \n" .
               "      OS_TYPE_ID IN (                                  \n" .
               "                     SELECT                            \n" .
               "                       OS_TYPE_ID                      \n" .
               "                     FROM                              \n" .
               "                       C_STM_LIST                      \n" .
               "                     WHERE                             \n" .
               "                       DISUSE_FLAG = '0' AND           \n" .
               "                       SYSTEM_ID   in (                \n" .
               "                         SELECT                        \n" .
               "                           SYSTEM_ID                   \n" .
               "                         FROM                          \n" .
               "                           B_ANSIBLE_PNS_PHO_LINK      \n" .
               "                         WHERE                         \n" .
               "                           PATTERN_ID   =  :PATTERN_ID    AND \n" .
               "                           OPERATION_NO_UAPK =  :OPERATION_NO_UAPK  AND \n" .
               "                           DISUSE_FLAG  = '0'          \n" .
               "                                    )                  \n" .
               "                    )                                  \n" .
               "      AND                                              \n" .
               "      DISUSE_FLAG = '0'                                \n" .
               "  ) TBL_2 ON                                           \n" .
               "  (TBL_1.DIALOG_TYPE_ID = TBL_2.DIALOG_TYPE_ID)        \n" .
               "ORDER BY OS_TYPE_ID,INCLUDE_SEQ                        \n";
    
        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('OPERATION_NO_UAPK'=>$in_operation_id,
                                  'PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"OPERATION_NO_UAPK=>$in_operation_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }
        
        $ina_dialogfilelist= array();
        $w_dialogfilelist= array();
        while ( $row = $objQuery->resultFetch() ){
            // 作業対象ホスト管理に登録されているホストが
            if($row['OS_TYPE_ID']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56113",
                                                           array($row['LINK_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            
                unset($objQuery);
                return false;
            }
            if(strlen($row['DIALOG_MATTER_FILE']) == 0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70026",
                                                               array($row['DIALOG_MATTER_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            
                unset($objQuery);
                return false;
            }

            // 対話ファイルの情報を退避
            $w_dialogfilelist[$row['OS_TYPE_ID']][$row['INCLUDE_SEQ']][$row['DIALOG_MATTER_ID']]=$row['DIALOG_MATTER_FILE'];
        }

        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56115",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            unset($objQuery);
            return false;
        }
        //対象playbookの数を確認
        if (count($w_dialogfilelist) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56116",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
        // DBアクセス事後処理
        unset($objQuery);

        $ret_code = true;
        // $ina_hostostypelist[ホスト名(IP)]=$row[OS種別]
        // より各ホスト毎のOS種別を取得
        // w_dialogfilelist[OS種別][インクルード順序][pkey]=子PlayBookファイル
        // のOS種別と一致する情報を抜出しホスト毎の対話ファイル配列を作成
        // $ina_dialog_files[ホストIP][INCLUDE順番][素材管理Pkey]=対話ファイル
        // ホスト数分繰返す
        foreach($ina_hostostypelist as $host_name=>$host_ostype){
            $hit = false;
            foreach($w_dialogfilelist as $file_ostype=>$file_include_list){
                // OS種別が一致しているか判定
                if($host_ostype == $file_ostype){
                    $hit = true;
                    foreach($file_include_list as $file_include=>$file_pkey_list){
                        foreach($file_pkey_list as $file_pkey=>$dialogfile){
                            // ホスト毎の対話ファイル配列作成
                            // [ホストIP][INCLUDE順番][素材管理Pkey]=対話ファイル
                            $ina_dialog_files[$host_name][$file_include][$file_pkey]=$dialogfile;
                            $hit = true;
                        }
                    }
                }
            }
            if($hit === false){
                 $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56119",array($host_name,$host_ostype));
                 $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                 $ret_code = false;
            }
        }

        return $ret_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0019
    // 処理内容
    //   システム予約変数を設定する
    // 
    // パラメータ
    //   $ina_host_vars:        変数一覧
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧返却配列
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function addSystemvars(&$ina_host_vars,$ina_hostprotcollist){
       foreach($ina_hostprotcollist as $host_ip=>$hostnamelist){
           foreach($hostnamelist as $host_name=>$prolist)
           foreach($prolist      as $pro=>$userlist)
           foreach($userlist     as $user_name=>$user_pass)

           //システム予約変数を設定
           // 親playbook(pioneer)に埋め込まれるリモート接続コマンド用変数の名前
           $ina_host_vars[$host_ip][self::LC_ANS_PROTOCOL_VAR_NAME]  = $pro;

           // 親playbook(legacy)に埋め込まれるリモートログインのユーザー用変数の名前
           $ina_host_vars[$host_ip][self::LC_ANS_USERNAME_VAR_NAME]  = $user_name;

           //リモートログインのパスワードが未登録か判定
           if($user_pass != self::LC_ANS_UNDEFINE_NAME){
               // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
               $ina_host_vars[$host_ip][self::LC_ANS_PASSWD_VAR_NAME]    = $user_pass;
           }

           // 対話ファイルに埋め込まれるホスト名用変数の名前
           $ina_host_vars[$host_ip][self::LC_ANS_LOGINHOST_VAR_NAME] = $host_name;

           // ユーザー公開用データリレイストレージパス 変数の名前
           $ina_host_vars[$host_ip][self::LC_ANS_OUTDIR_VAR_NAME] = $this->lv_user_out_Dir;

           // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
           $ina_host_vars[$host_ip][self::LC_SYMPHONY_DIR_VAR_NAME] = $this->lv_symphony_instance_Dir;
            
       }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0020
    // 処理内容
    //   テンプレートファイルの情報をデータベースより取得する。
    // 
    // パラメータ
    //   $in_tpf_var_name:      テンプレート変数名
    //   $in_tpf_key:           テンプレートKey格納変数
    //   $in_tpf_file_name:     テンプレートファイル格納変数
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBLegacyTemplateMaster($in_tpf_var_name,&$in_tpf_key,&$in_tpf_file_name){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        $sql = "SELECT                         \n" .
               "  ANS_TEMPLATE_ID,             \n" .
               "  ANS_TEMPLATE_FILE            \n" .
               "FROM                           \n" .
               "  B_ANS_TEMPLATE_FILE          \n" .
               "WHERE                          \n" .
               "  ANS_TEMPLATE_VARS_NAME = :ANS_TEMPLATE_VARS_NAME AND \n" .
               "  DISUSE_FLAG            = '0';\n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ANS_TEMPLATE_VARS_NAME=>$in_tpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('ANS_TEMPLATE_VARS_NAME'=>$in_tpf_var_name));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ANS_TEMPLATE_VARS_NAME=>$in_tpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }
        
        $ina_child_playbooks = array();
        $row = $objQuery->resultFetch();
        
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            // テンプレートが未登録の場合のエラー処理は呼び側にまかせる。
            unset($objQuery);
            return true;
        }
        $in_tpf_key       = $row["ANS_TEMPLATE_ID"];
        $in_tpf_file_name = $row["ANS_TEMPLATE_FILE"];

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のテンプレートファイル格納ディレクトリパスを記憶
    // パラメータ
    //   $in_dir:      template_filesディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setHostvarsfile_template_file_Dir($in_dir){
        $this->lv_Hostvarsfile_template_file_Dir = $in_dir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のテンプレートファイル格納ディレクトリパスを取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   child_playbooksディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_template_file_Dir(){
        return($this->lv_Hostvarsfile_template_file_Dir);
    }
        
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのtemplate_filesディレクトリパスを記憶
    // パラメータ
    //   $in_dir:      child_playbooksディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_template_files_Dir($in_indir){
        $this->lv_Ansible_template_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのtemplate_filesディレクトリパスを取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   child_playbooksディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_template_files_Dir(){
        return($this->lv_Ansible_template_files_Dir);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA側テンプレートファイル格納ディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      子PlayBook格納ディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_template_file_Dir($in_indir){
        $this->lv_ita_template_files_Dir = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA側テンプレートファイル格納ディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_template_file_Dir(){
        return($this->lv_ita_template_files_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITAが管理しているテンプレートファイルのパスを取得
    // パラメータ
    //   $in_key:        テンプレートファイルのPkey(データベース)
    //   $in_filename:   テンプレートファイル名    
    // 
    // 戻り値
    //   ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_template_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_TEMPLATE_FILE_DIR_MK,
                        $this->getITA_template_file_Dir(),
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のテンプレートファイルパスを取得
    // パラメータ
    //   $in_pkey:    テンプレートファイル Pkey
    //   $in_file:    テンプレートファイル
    // 
    // 戻り値
    //   ホスト変数ファイル内のテンプレートファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_template_file_value($in_pkey,$in_file){
        $intNumPadding = 10;

        $file = sprintf(self::LC_HOSTVARSFILE_TEMPLATE_FILE_MK,
                        $this->getHostvarsfile_template_file_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansible実行時のテンプレートファイル名を取得
    //   
    // パラメータ
    //   $in_filename:       テンプレートファイル名
    //   $in_pkey:           テンプレートファイル Pkey
    // 
    // 戻り値
    //   Ansible実行時のテンプレートファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_template_file($in_pkey,$in_filename){
        $intNumPadding = 10;

        // Ansible実行時のテンプレートファイル名は Pkey(10桁)-子テンプレートファイル名 する。
        $file = sprintf(self::LC_ANS_TEMPLATE_FILE_MK,
                        $this->getAnsible_template_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0021
    // 処理内容
    //   テンブレートファイルを所定のディレクトリにコピーする。
    // パラメータ
    //   $ina_template_files:   テンプレートファイル配列
    //                          [Pkey]=>テンプレートファイル
    //                          の配列
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateTemplatefiles($ina_template_files){
        foreach( $ina_template_files as $pkey=>$template_file ){
            //テンプレートファイルが存在しているか確認
            $src_file = $this->getITA_template_file($pkey,$template_file);

            if( file_exists($src_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55239",array($pkey,basename($src_file))); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            // Ansible実行時のテンプレートファイル名は Pkey(10桁)-テンプレートファイル名
            $dst_file = $this->getAnsible_template_file($pkey,$template_file);

            if(file_exists($dst_file) === true){
                // 既にコピー済み
                return true;
            }

            //子Playbookをansible用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55240",array(basename($src_file))); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0022
    // 処理内容
    //   Legacy用 Playbookよりtemplateモジュールで使用しているテンプレート変数
    //   抜出しホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    // 
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateLegacytemplatefiles($ina_hosts,$ina_child_playbooks,$ina_hostprotcollist,$ina_host_vars)
    {
        $result_code = true;

        $la_tpf_path  = array();
        foreach( $ina_child_playbooks as $no=>$playbook_list ){
            // 子PlayBook分の繰返し
            foreach( $playbook_list as $playbookpkey=>$playbook ){
                // Ansible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
                // 子PlayBookのバスを取得
                $file_name = $this->getAnsible_child_playbiook_file($playbookpkey,$playbook);

                // 子PlayBookの内容を取得 ここまでの過程でファイルの存在は確認
                $dataString = file_get_contents($file_name);

                ///////////////////////////////////////////////////////////////////
                // 子PlayBookのtemplateモジュールが使用されているか確認
                // $la_tpf_vars[行番号]=テンプレート変数を返す
                ///////////////////////////////////////////////////////////////////
                $la_tpf_vars = array();
                $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_TPF_HED,$dataString);
                $aryResultParse = $objWSRA->getTPFvarsarrayResult();
                $la_tpf_vars     = $aryResultParse[0];
                $la_tpf_errors   = $aryResultParse[1];

                unset($objWSRA);
                // エラーが発生しているか確認
                if(count($la_tpf_errors) > 0){
                    foreach( $la_tpf_errors as $line_no => $errcode ){
                        //現在のエラーリスト
                        $msgstr = $this->lv_objMTS->getSomeMessage($errcode,
                                                                   array(basename($playbook),
                                                                   $line_no));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    $result_code = false;
                }
                ///////////////////////////////////////////////////////////////////
                // テンプレート変数に紐づくテンプレートファイルの情報を取得
                ///////////////////////////////////////////////////////////////////
                $la_tpf_files = array();
                foreach( $la_tpf_vars as $line_no => $tpf_var_name ){
                    // グローバル変数か一般変数が定義されているか判定。グローバル変数の場合は、グローバル変数を退避
                    $ret = preg_match_all('/(' . DF_HOST_VAR_HED . '|' . DF_HOST_GBL_HED . ')[a-zA-Z0-9_]*/',$tpf_var_name,$var_match1);
                    if($ret == 1){
                        //グローバル変数か判定
                        $ret = preg_match_all('/' . DF_HOST_GBL_HED . '[a-zA-Z0-9_]*' . '/',$var_match1[0][0],$var_match2);
                        if($ret == 1){
                            // グローバル変数を退避
                            $this->lv_legacy_tfp_use_gbl_vars_list[$var_match2[0][0]] = 0;
                        }
                        continue;
                    }
                    $tpf_key = "";
                    $tpf_file_name = "";
                    // テンプレート変数名からテンプレートファイル名とPkeyを取得する。
                    $ret = $this->getDBLegacyTemplateMaster($tpf_var_name,$tpf_key,$tpf_file_name);
                    if( $ret == false ){
                        //エラーが発生した場合は処理終了
                        return false;
                    }
                    // テンプレート変数名が未登録の場合
                    if( $tpf_key == "" ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55241",
                                                                   array(basename($playbook),
                                                                   $line_no,
                                                                   $tpf_var_name)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        //テンプレート変数名が未登録
                        $result_code = false;
                        continue;
                    }
                    else{
                        // テンプレートファイル名が未登録の場合
                        if($tpf_file_name == "" ){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55273",
                                                                       array(basename($playbook),
                                                                       $line_no,
                                                                       $tpf_var_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                            $result_code = false;
                            continue;
                        }
                    }

                    // テンプレートファイルのpkeyとファイル名を退避 
                    // la_tpf_files[pkey]=テンプレートファイル
                    $la_tpf_files[$tpf_key]=$tpf_file_name;

                    // ホスト変数ファイル内のテンプレートファイルバスを取得
                    $tpf_path = $this->getHostvarsfile_template_file_value($tpf_key,$tpf_file_name);
                    // $la_tpf_path[テンプレート変数]=ホスト変数ファイル内のテンプレートファイルパス
                    $la_tpf_path[$tpf_var_name] = $tpf_path;

                    // テンプレートファイル内のホスト変数を確認
                    $ret = $this->CheckTemplatefile($ina_hosts,$ina_host_vars,$playbook,$tpf_key,$tpf_file_name);
                    if( $ret === false ){
                        $result_code = false;
                    }

                }
                // 前処理でエラーが発生している場合は次のファイルへ
                if($result_code === false){
                    continue;
                }
                
                if(count($la_tpf_files) > 0){
                    // テンプレートファイルを所定のディレクトリにコピーする。
                    $ret = $this->CreateTemplatefiles($la_tpf_files);
                    if( $ret == false ){
                        return false;
                    }
                }
            }
        }
        // 前処理でエラーが発生している場合は処理終了
        if($result_code === false){
            return false;
        }
                
        if ( count($la_tpf_path) > 0 ){
            // ホスト変数配列のホスト)分繰返し
            foreach( $ina_hosts as $host_name){
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_file = $hostname;
        
                //LEGACY用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_host_var_file($host_vars_file);
                // ホスト変数定義ファイルにテンプレート変数を追加
                if($this->CreateHostvarsfile("TPF",$host_name,$file_name,$la_tpf_path,"","","a") === false)
                {
                    return false;
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0023
    // 処理内容
    //   Legacy用 
    //   Playbook内のテンプレートで使用している変数がホスト変数に登録されているかチェックする。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    //   $ina_child_playbook:   子PlayBookファイル
    // 
    //   $in_tpf_key:           テンプレートファイルPkey
    // 
    //   $in_tpf_file_name:     テンプレートファイル名
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckTemplatefile($ina_hosts,$ina_host_vars,$in_child_playbook,$in_tpf_key,$in_tpf_file_name){
        $result_code = true;

        ///////////////////////////////////////////////////////////////////
        // テンプレートで使用している変数がホストの変数に登録されているか判定
        ///////////////////////////////////////////////////////////////////


        $templatefile = $this->getITA_template_file($in_tpf_key,$in_tpf_file_name);

        // テンプレートに登録されている変数を抜出す。
        $dataString = file_get_contents($templatefile);

        if($dataString === false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55265",
                                                       array(basename($in_child_playbook),
                                                       basename($templatefile)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            $result_code = false;
            return $result_code;
        }

        // ローカル変数のリスト初期化
        $local_vars = array();

        $file_global_vars_list = array();
        // テンプレートに登録されているグローバル変数を抜出す
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_TEMP_GBL_HED,$dataString,$local_vars);
        $file_global_vars_list = $objWSRA->getTPFVARSParsedResult();
        unset($objWSRA);

        // テンプレートに登録されているグローバル変数のデータベース登録確認 
        foreach( $file_global_vars_list as $var_name ){
            if(@count($this->lva_global_vars_list[$var_name]) == 0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90240",
                                                            array(basename($in_child_playbook),
                                                                  basename($templatefile),
                                                                  $var_name));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                $result_code = false;
            }
        }

        // ローカル変数のリスト作成
        $local_vars[] = self::LC_ANS_PROTOCOL_VAR_NAME;
        $local_vars[] = self::LC_ANS_USERNAME_VAR_NAME;
        $local_vars[] = self::LC_ANS_PASSWD_VAR_NAME;
        $local_vars[] = self::LC_ANS_LOGINHOST_VAR_NAME;

        // ユーザー公開用データリレイストレージパス 変数の名前
        $local_vars[] = self::LC_ANS_OUTDIR_VAR_NAME;

        // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
        $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

        $file_vars_list = array();
        // ホスト変数を抜出す
        $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$local_vars);
        $file_vars_list = $objWSRA->getTPFVARSParsedResult();
        unset($objWSRA);
    
        // テンプレートで変数が使用されているか判定
        if(count($file_vars_list) > 0){
            // 各ホストのホスト変数があるか判定
            foreach( $ina_hosts as $no => $host_name ){
                if(empty($ina_host_vars[$host_name])===true){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55255",
                                                                array(basename($in_child_playbook),
                                                                      basename($templatefile),
                                                                      $host_name)); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                    // 未登録でも処理は続行する。
                }
            }
            // テンプレートに登録されている変数のデータベース登録確認 
            foreach( $file_vars_list as $var_name ){
                // ホスト配列のホスト分繰り返し
                foreach( $ina_hosts as $no=>$host_name ){
                    // 変数配列分繰り返し
                    // $ina_host_vars[ ipaddress ][ 変数名 ]=>具体値
                    if(@strlen($ina_host_vars[$host_name][$var_name])==0)
                    {
                        if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55256",
                                                                       array(basename($in_child_playbook),
                                                                             basename($templatefile),
                                                                             $var_name,
                                                                             $host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55257",
                                                                       array(basename($in_child_playbook),
                                                                             basename($templatefile),
                                                                             $var_name,
                                                                             $host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55258",
                                                                       array(basename($in_child_playbook),
                                                                             basename($templatefile),
                                                                             $var_name,
                                                                             $host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55259",
                                                                       array(basename($in_child_playbook),
                                                                             basename($templatefile),
                                                                             $var_name,
                                                                             $host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        else{
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55260",
                                                                       array(basename($in_child_playbook),
                                                                             basename($templatefile),
                                                                             $var_name,
                                                                             $host_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        $result_code = false;
                    }
                    else{
                        //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                        if($ina_host_vars[$host_name][$var_name] == self::LC_ANS_UNDEFINE_NAME){
                            // プロトコル未登録
                            if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55261",
                                                                           array(basename($in_child_playbook),
                                                                                 basename($templatefile),
                                                                                 $var_name,
                                                                                 $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                            }
                            // ユーザー名未登録
                            elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55262",
                                                                           array(basename($in_child_playbook),
                                                                                 basename($templatefile),
                                                                                 $var_name,
                                                                                 $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                            }
                            // ログインパスワード未登録
                            elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55263",
                                                                           array(basename($in_child_playbook),
                                                                                 basename($templatefile),
                                                                                 $var_name,
                                                                                 $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                            }
                            // ホスト名未登録
                            elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55264",
                                                                           array(basename($in_child_playbook),
                                                                                 basename($templatefile),
                                                                                 $var_name,
                                                                                 $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                $result_code = false;
                            }
                        }
                    }
                }
            }
        }
        return($result_code);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0028
    // 処理内容
    //   作業パターン詳細の情報を取得
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $ina_pattern_list:     作業パターン一覧返却配列
    //                          [ロールパッケージID][ロールID]=>実行順
    //   $in_single_pkg:        ロールパッケージの複数指定有無
    //                          true: 単一　false:複数
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBPatternList($in_pattern_id,&$ina_pattern_list,&$in_single_pkg){
        $sql = "SELECT                             \n" .
               "  ROLE_PACKAGE_ID,                 \n" .
               "  ROLE_ID,                         \n" .
               "  INCLUDE_SEQ                      \n" .
               "FROM                               \n" .
               "  $this->lv_ansible_pattern_linkDB \n" .
               "WHERE                              \n" .
               "  PATTERN_ID  = :PATTERN_ID AND    \n" .
               "  DISUSE_FLAG = 0;                   ";
    
        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        // 作業パターンID登録確認
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56102",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
    
        $pkgid = 0;
        $idx   = 0;
        $in_single_pkg = true;

        $ina_pattern_list = array();
        
        while ( $row = $objQuery->resultFetch() ){
            // 複数のロールパッケージが使用されているか判定する。
            if($idx === 0){
                $pkgid = $row['ROLE_PACKAGE_ID'];
            }
            else{
                if($pkgid <> $row['ROLE_PACKAGE_ID']){
                    $in_single_pkg = false;
                }
            }
            $idx = $idx + 1;

            //作業パターン一覧配列作成
            $ina_pattern_list[$row['ROLE_PACKAGE_ID']][$row['ROLE_ID']]=$row['INCLUDE_SEQ'];
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0029
    // 処理内容
    //   ロールパッケージ管理の情報を取得
    // 
    // パラメータ
    //   $in_pattern_id:          ロールパッケージID
    //   $ina_role_package_list:  ロールパッケージリスト
    //                            [ロールパッケージID][ロールパッケージ名]=>ロールパッケージファイル
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBRolePackage($in_role_package_id,&$ina_role_package_list){
        $sql = "SELECT                                \n" .
               "  ROLE_PACKAGE_ID,                    \n" .
               "  ROLE_PACKAGE_NAME,                  \n" .
               "  ROLE_PACKAGE_FILE                   \n" .
               "FROM                                  \n" .
               "  $this->lv_ansible_role_packageDB    \n" .
               "WHERE                                 \n" .
               "  ROLE_PACKAGE_ID = :ROLE_PACKAGE_ID AND  \n" .
               "  DISUSE_FLAG = 0;                      ";
    
        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ROLE_PACKAGE_ID=>$in_role_package_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('ROLE_PACKAGE_ID'=>$in_role_package_id));
    
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ROLE_PACKAGE_ID=>$in_role_package_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        $ina_role_package_list = array();

        // ロールパッケージID登録確認
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70011",array($in_role_package_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
    
        $row = $objQuery->resultFetch();

        if(strlen($row['ROLE_PACKAGE_FILE']) == 0){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70027",array($in_role_package_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
        //作業パターン一覧配列作成
        $ina_role_package_list[$row['ROLE_PACKAGE_ID']][$row['ROLE_PACKAGE_NAME']]=$row['ROLE_PACKAGE_FILE'];

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0031
    // 処理内容
    //   データベースからロール名を取得
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $ina_rolenamelist:     ロール名返却配列
    //                          [実行順序][ロールID(Pkey)]=>ロール名
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBLegactRoleList($in_pattern_id,&$ina_rolenamelist){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // DISUSE_FLAG = '0'の条件はSELECT文に入れない。
        $sql = "SELECT                                           \n" .
               "TBL_1.LINK_ID,                                   \n" .
               "TBL_1.ROLE_ID,                                   \n" .
               "TBL_1.INCLUDE_SEQ,                               \n" .
               "TBL_2.ROLE_NAME,                                 \n" .
               "TBL_2.DISUSE_FLAG                                \n" .
               "FROM                                             \n" .
               "  (                                              \n" .
               "    SELECT                                       \n" .
               "      TBL3.LINK_ID,                              \n" .
               "      TBL3.PATTERN_ID,                           \n" .
               "      TBL3.ROLE_ID,                              \n" .
               "      TBL3.INCLUDE_SEQ                           \n" .
               "    FROM                                         \n" .
               "      $this->lv_ansible_pattern_linkDB  TBL3     \n" .
               "    WHERE                                        \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND         \n" .
               "      TBL3.DISUSE_FLAG = '0'                     \n" .
               "  )TBL_1                                         \n" .
               "LEFT OUTER JOIN  $this->lv_ansible_roleDB  TBL_2 ON \n" .
               "      ( TBL_1.ROLE_ID = TBL_2.ROLE_ID) \n" .
               "ORDER BY TBL_1.INCLUDE_SEQ; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }
        
        $ina_child_playbooks = array();
        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_rolenamelist[実行順序][ロールID(Pkey)]=>ロール名
                $ina_rolenamelist[$row['INCLUDE_SEQ']][$row['ROLE_ID']]=$row['ROLE_NAME'];
            }
            // ロール管理にロールが未登録の場合
            elseif($row['DISUSE_FLAG']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70012",
                                                           array($row['LINK_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            
                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }
        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56102",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }
        //対象ロールの数を確認
        if (count($ina_rolenamelist) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70013",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0032
    // 処理内容
    //   Legacy-Role用 PlayBookファイルを作成する。
    // 
    // パラメータ
    //   $ina_rolenames:    ロール名リスト配列
    //                      [実行順序][ロールID(Pkey)]=>ロール名
    //   $in_exec_mode:         実行エンジン
    //                           1: Ansible  2: Ansible Tower
    //   $in_exec_playbook_hed_def; 親playbookヘッダセクション
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateLegacyRolePlaybookfiles($ina_rolenames,$in_exec_mode,$in_exec_playbook_hed_def){
        /////////////////////////////////////////
        // 親PlayBookファイル作成(Legacy-Role) //
        /////////////////////////////////////////
        $file_name = $this->getAnsible_RolePlaybook_file();
        if($this->CreatePlaybookfile($file_name,$ina_rolenames,$in_exec_mode,$in_exec_playbook_hed_def) === false){
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0033
    // 処理内容
    //   Legacy用 Playbookのフォーマットをチェックする
    //   Playbookで使用している変数がホスト変数に登録されているかチェックする。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    //   $ina_rolenames:        ロール名リスト配列(データベース側)
    //                          [実行順序][ロールID(Pkey)]=>ロール名
    // 
    //   $ina_role_rolenames:   ロール名リスト配列(Role内登録内容)
    //                          [ロール名]
    // 
    //   $ina_role_rolevars:    ロール内変数リスト配列(Role内登録内容)
    //                          [ロール名][変数名]=0
    // 
    //   $ina_role_roleglobalvars:    ロール内グローバル変数リスト配列(Role内登録内容)
    //                                [ロール名][グローバル変数名]=0
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CheckLegacyRolePlaybookfiles($ina_hosts,
                                          $ina_host_vars,
                                          $ina_rolenames,
                                          $ina_role_rolenames,
                                          $ina_role_rolevars,
                                          $ina_role_roleglobalvars){
        ///////////////////////////////////////////////////////////////////
        // グローバル変数以外の変数の具体値が未登録でもエラーにしていないので
        // グローバル変数についてもグローバル変数管理の登録の有無をチェックしない
        //////////////////////////////////////////////////////////////////////////

        $result_code = true;

        // ロール分の繰返し(データベース側)
        foreach( $ina_rolenames as $no=>$rolename_list ){
            // ロール名取得(データベース側)
            foreach( $rolename_list as $rolepkey=>$rolename ){
                // データベース側のロールがロール内に存在しているか判定
                if(in_array($rolename,$ina_role_rolenames) === false){
                    //存在していない
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70024",array($rolename)); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                    continue;            
                }
                // ロール内に変数が登録されているか
                if(@count($ina_role_rolevars[$rolename]) === 0)
                {
                    // ロール内に変数が使用されていない場合は以降のチェックをスキップ
                    continue;            
                }

                // ロールに登録されている変数のデータベース登録確認 
                foreach( $ina_role_rolevars[$rolename] as $var_name=>$dummy){
                    // ホスト配列のホスト分繰り返し
                    foreach( $ina_hosts as $no=>$host_name ){
                        // 変数配列分繰り返し
                        // $ina_host_vars[ ipaddress ][ 変数名 ]=>具体値
                        if(@strlen($ina_host_vars[$host_name][$var_name])==0)
                        {
                            if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70015",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70016",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70017",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70018",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            else{
                                continue;

                            }
                            // エラーリターンする
                            $result_code = false;
                        }
                        else{
                            //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                            if($ina_host_vars[$host_name][$var_name] == self::LC_ANS_UNDEFINE_NAME){
                                // プロトコル未登録
                                if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70020",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ユーザー名未登録
                                elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70021",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ログインパスワード未登録
                                elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70022",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ホスト名未登録
                                elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70023",
                                                                               array($rolename,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return($result_code);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansible Role playbookファイル名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   playbookファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_RolePlaybook_file(){
        $file = $this->lv_Ansible_in_Dir . "/" . self::LC_ANS_ROLE_PLAYBOOK_FILE;
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0030
    // 処理内容
    //   作業パターンIDに紐づくパッケージファイルを取得
    // 
    // パラメータ
    //   $in_pattern_id:        作業パターンID
    //   $in_role_package_pkey  ロールパッケージファイル Pkey返却
    //   $in_role_package_file  ロールパッケージファイル(ZIP)返却
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getRolePackageFile($in_pattern_id,&$in_role_package_pkey,&$in_role_package_file){
        $in_role_package_file = "";
        $rolepackagelist  = array();
        $patternlist      = array();
        // 該当作業パターンIDに紐づく作業パターン詳細を取得する。
        $single_pkg       = true;
        // patternlist[ロールパッケージID][ロールID]=>実行順
        /////////////////////////////////////////////////////////////////////////////
        // データベースから該当作業パターンIDに紐づく作業パターン詳細を取得
        //   $patternlist:   作業パターンリスト返却配列
        //                   [ロールパッケージID][ロールID]=>実行順
        /////////////////////////////////////////////////////////////////////////////
        $ret = $this->getDBPatternList($in_pattern_id,$patternlist,$single_pkg);
        if($ret <> true){
            // 例外処理へ
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
            // DebugLogPrint
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // 作業パターン詳細に複数のロールパッケージが紐づいていないか判定する。
        if($single_pkg === false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-70010");
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // 作業パターンIDの紐づけがない場合のチェックはgetDBPatternListで実施済み

        // 作業パターンIDに紐づくロールパッケージIDを取出す
        foreach( $patternlist as $role_package_id=>$role_package_list )

        // ロールパッケージIDに紐づいているロールパッケージファイル(ZIP)を取得する。
        // $rolepackagelist[ロールパッケージID][ロールパッケージ名]=>ロールパッケージファイル
        $ret = $this->getDBRolePackage($role_package_id,$rolepackagelist);
        if($ret <> true){
            // 例外処理へ
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
            // DebugLogPrint
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // ロールパッケージIDに紐づけがない場合のチェックはgetDBRolePackageで実施済み
        // ロールパッケージIDに紐づくロールパッケージPkeyを取出す
        foreach( $rolepackagelist as $in_role_package_pkey=>$role_package_list )
        // ロールパッケージIDに紐づくロールパッケージファイルを取出す
        foreach( $role_package_list as $role_package_name=>$in_role_package_file)

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
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
        if($this->getAnsible_out_Dir() != ""){
            $logfile = $this->getAnsible_out_Dir() . "/" . "error.log";
            $filepointer=fopen(  $logfile, "a");
            flock($filepointer, LOCK_EX);
            fputs($filepointer, $ret . "\n" );
            flock($filepointer, LOCK_UN);
            fclose($filepointer);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0034
    // 処理内容
    //   メンバー変数マスタの情報を取得
    // 
    // パラメータ
    //   $in_var_name_id:       変数マスタの変数ID(Pkey)
    //   $ina_child_vars:       メンバー変数マスタリスト
    //                           [VARS_NAME][CHILD_VARS_NAME]=0
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBChildVarsList(&$ina_child_vars){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        $sql = " SELECT                                                \n" .
               "   (                                                   \n" .
               "     SELECT                                            \n" .
               "       VARS_NAME                                       \n" .
               "     FROM                                              \n" .
               "       $this->lv_ansible_vars_masterDB                 \n" .
               "     WHERE                                             \n" .
               "       VARS_NAME_ID = TBL_1.PARENT_VARS_NAME_ID AND    \n" .
               "       DISUSE_FLAG = '0'                               \n" .
               "   ) VARS_NAME,                                        \n" .
               "   TBL_1.CHILD_VARS_NAME                               \n" .
               " FROM                                                  \n" .
               "   $this->lv_ansible_child_varsDB TBL_1                \n" .
               " WHERE                                                 \n" .
               "   TBL_1.DISUSE_FLAG = '0';                            \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }
        
        $ina_child_vars = array();
        while ( $row = $objQuery->resultFetch() ){
            $ina_child_vars[$row['VARS_NAME']][$row['CHILD_VARS_NAME']] = 0;
        }

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のcopyファイル格納ディレクトリバスを記憶
    // パラメータ
    //   $in_dir:      copy_filesディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setHostvarsfile_copy_file_Dir($in_dir){
        $this->lv_Hostvarsfile_copy_file_Dir = $in_dir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のcopyファイル格納ディレクトリバスを取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   copy_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_copy_file_Dir(){
        return($this->lv_Hostvarsfile_copy_file_Dir);
    }    

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのcopy_filesディレクトリバスを記憶
    // パラメータ
    //   $in_dir:      copy_filesディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_copy_files_Dir($in_indir){
        $this->lv_Ansible_copy_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのcopy_filesディレクトリバスを取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   copy_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_copy_files_Dir(){
        return($this->lv_Ansible_copy_files_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA側copyファイル格納ディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      子PlayBook格納ディレクトリ
    // 
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_copy_file_Dir($in_indir){
        $this->lv_ita_copy_files_Dir = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA側copyファイル格納ディレクトリ名を取得
    // パラメータ
    //   なし
    // 
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_copy_file_Dir(){
        return($this->lv_ita_copy_files_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITAが管理しているcopyファイルのパスを取得
    // パラメータ
    //   $in_key:        copyファイルのPkey(データベース)
    //   $in_filename:   copyファイル名    
    // 
    // 戻り値
    //   ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_copy_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_COPY_FILE_DIR_MK,
                        $this->getITA_copy_file_Dir(),
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のcopyファイルパスを取得
    // パラメータ
    //   $in_pkey:    テンプレートファイル Pkey 現在未使用
    //   $in_file:    テンプレートファイル
    // 
    // 戻り値
    //   ホスト変数ファイル内のテンプレートファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_copy_file_value($in_pkey,$in_file){
        $intNumPadding = 10;
        $file = sprintf(self::LC_HOSTVARSFILE_COPY_FILE_MK,
                        $this->getHostvarsfile_copy_file_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Ansible実行時のコピーファイル名を取得
    //   
    // パラメータ
    //   $in_filename:       テンプレートファイル名 現在未使用
    //   $in_pkey:           テンプレートファイル Pkey
    // 
    // 戻り値
    //   Ansible実行時のテンプレートファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_copy_file($in_pkey,$in_filename){
        $intNumPadding = 10;
        // Ansible実行時のcopyファイル名は Pkey(10桁)を付けないファイル名にする。
        $file = sprintf(self::LC_ANS_COPY_FILE_MK,
                        $this->getAnsible_copy_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0037
    // 処理内容
    //   copyファイルを所定のディレクトリにコピーする。
    // パラメータ
    //   $ina_copy_files:   copyファイル配列
    //                      [Pkey]=>copyファイル
    //                      の配列
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateCopyfiles($ina_copy_files){
        foreach( $ina_copy_files as $pkey=>$copy_file ){
            //copyファイルが存在しているか確認
            $src_file = $this->getITA_copy_file($pkey,$copy_file);

            if( file_exists($src_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90092",array($pkey,basename($src_file))); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            // Ansible実行時のcopyファイル名
            $dst_file = $this->getAnsible_copy_file($pkey,$copy_file);

            if(file_exists($dst_file) === true){
                // 既にコピー済み
                return true;
            }

            //子Playbookをansible用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90093",array(basename($src_file))); 
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0038
    // 処理内容
    //   Legacy用 Playbookよりcopyモジュールで使用している変数
    //   抜出しホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_child_playbooks:  子PlayBookファイル配列
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    // 
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateLegacyCopyFiles($ina_hosts,$ina_child_playbooks,$ina_hostprotcollist,$ina_host_vars){
        $result_code = true;

        $la_cpf_path  = array();
        foreach( $ina_child_playbooks as $no=>$playbook_list ){
            // 子PlayBook分の繰返し
            foreach( $playbook_list as $playbookpkey=>$playbook ){
                // Ansible実行時の子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
                // 子PlayBookのバスを取得
                $file_name = $this->getAnsible_child_playbiook_file($playbookpkey,$playbook);

                // 子PlayBookの内容を取得 ここまでの過程でファイルの存在は確認
                $dataString = file_get_contents($file_name);

                ///////////////////////////////////////////////////////////////////
                // 子PlayBookのcopyモジュールが使用されているか確認
                ///////////////////////////////////////////////////////////////////
                SimpleVerSearch(DF_HOST_CPF_HED,$dataString,$la_cpf_vars);

                ///////////////////////////////////////////////////////////////////
                // copy変数に紐づくファイルの情報を取得
                ///////////////////////////////////////////////////////////////////
                $la_cpf_files = array();
                foreach( $la_cpf_vars as $no => $cpf_var_list ){
                    foreach( $cpf_var_list as $line_no  => $cpf_var_name ){
                        $cpf_key = "";
                        $cpf_file_name = "";
                        // copy変数名からコピーファイル名とPkeyを取得する。
                        $ret = $this->getDBLegacyCopyMaster($cpf_var_name,$cpf_key,$cpf_file_name);
                        if( $ret == false ){
                            //エラーが発生した場合は処理終了
                            return false;
                        }
                        // copy変数名が未登録の場合
                        if( $cpf_key == "" ){
                            //OK

                            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90090",
                                                                       array(basename($playbook),
                                                                       $line_no,
                                                                       $cpf_var_name)); 
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            //copy変数名が未登録
                            $result_code = false;
                            continue;
                        }
                        else{
                            // copyファイル名が未登録の場合
                            if($cpf_file_name == "" ){
                                //OK
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90091",
                                                                           array(basename($playbook),
                                                                           $line_no,
                                                                           $cpf_var_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                $result_code = false;
                                continue;
                            }
                        }

                        // copyファイルのpkeyとファイル名を退避 
                        $la_cpf_files[$cpf_key]=$cpf_file_name;

                        // inディレクトリ配下のcopyファイルバスを取得
                        $cpf_path = $this->getHostvarsfile_copy_file_value($cpf_key,$cpf_file_name);
                        $la_cpf_path[$cpf_var_name] = $cpf_path;
                    }
                }

                // 前処理でエラーが発生している場合は次のファイルへ
                if($result_code === false){
                    continue;
                }
                
                if(count($la_cpf_files) > 0){
                    // copyファイルを所定のディレクトリにコピーする。
                    $ret = $this->CreateCopyfiles($la_cpf_files);
                    if( $ret == false ){
                        return false;
                    }
                }
            }
        }
        // 前処理でエラーが発生している場合は処理終了
        if($result_code === false){
            return false;
        }
                
        if ( count($la_cpf_path) > 0 ){
            // ホスト変数配列のホスト)分繰返し
            foreach( $ina_hosts as $host_name){
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_file = $hostname;

                //LEGACY用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_host_var_file($host_vars_file);
                // ホスト変数定義ファイルにコピー変数を追加
                if($this->CreateHostvarsfile("CPF",$host_name,$file_name,$la_cpf_path,"","","a") === false)
                {
                    return false;
                }
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0039
    // 処理内容
    //   copyファイルの情報をデータベースより取得する。
    // 
    // パラメータ
    //   $in_cpf_var_name:      copy変数名
    //   $in_cpf_key:           PKey格納変数
    //   $in_cpf_file_name:     copyファイル格納変数
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBLegacyCopyMaster($in_cpf_var_name,&$in_cpf_key,&$in_cpf_file_name){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        $sql = "SELECT                         \n" .
               "  CONTENTS_FILE_ID,            \n" .
               "  CONTENTS_FILE                \n" .
               "FROM                           \n" .
               "  B_ANS_CONTENTS_FILE          \n" .
               "WHERE                          \n" .
               "  CONTENTS_FILE_VARS_NAME = :CONTENTS_FILE_VARS_NAME AND \n" .
               "  DISUSE_FLAG            = '0';\n";

        $in_cpf_key       = "";
        $in_cpf_file_name = "";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"CONTENTS_FILE_VARS_NAME=>$in_cpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('CONTENTS_FILE_VARS_NAME'=>$in_cpf_var_name));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"CONTENTS_FILE_VARS_NAME=>$in_cpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
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

    function getVarsStructure($vars_name_id, &$result_array, &$master_array) {

        $sql = "SELECT * \n" .
               "FROM \n" .
               "  B_ANS_LRL_ARRAY_MEMBER TBL_1 \n" .
               "WHERE TBL_1.VARS_NAME_ID = $vars_name_id \n" .
               "AND TBL_1.DISUSE_FLAG = 0; ";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false) {
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"VARS_NAME_ID=>$vars_name_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r) {
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"VARS_NAME_ID=>$vars_name_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
        $tgt_row = array();
        while ( $row = $objQuery->resultFetch() ) {
            $master_array[$row["ARRAY_MEMBER_ID"]] = $row;
            $tgt_row[] = $row;
        }

        // DBアクセス事後処理
        unset($objQuery);

        // ｛階層順, 構造体selfキー｝にソート
        usort($tgt_row, function($a, $b) {
            // 階層
            $integer_a_nest_level = intval($a['ARRAY_NEST_LEVEL']);
            $integer_b_nest_level = intval($b['ARRAY_NEST_LEVEL']);
            if($integer_a_nest_level < $integer_b_nest_level) {
                return -1;
            }
            if($integer_a_nest_level > $integer_b_nest_level) {
                return 1;
            }

            // 構造体selfキー
            $integer_a_vars_key_id = intval($a['VARS_KEY_ID']);
            $integer_b_vars_key_id = intval($b['VARS_KEY_ID']);
            if($integer_a_vars_key_id < $integer_b_vars_key_id) {
                return -1;
            }
            if($integer_a_vars_key_id > $integer_b_vars_key_id) {
                return 1;
            }

            return 0;
        });

        // topレベルコンテナ準備
        $topContainer = new AnsLrlVarsGrpMemberContainer(null);

        // データ階層コンテナ生成
        foreach( $tgt_row as $row ) {
            $descendant = new AnsLrlVarsGrpMemberContainer($row);
            $topContainer->setDescendant($descendant);
        }

        // 配列生成
        $result_array = $topContainer->getConstructionArray();

        return true;
    }

    function getVarsArray($in_vars_name_id,&$result_array) {
        $sql = "SELECT * \n" .
               "FROM \n" .
               "      B_ANS_LRL_ARRAY_MEMBER " .
               "WHERE \n" .
               "      VARS_NAME_ID = :VARS_NAME_ID \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false) {
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"VARS_NAME_ID=>$in_vars_name_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        $objQuery->sqlBind( array('VARS_NAME_ID'=>$in_vars_name_id) );

        $r = $objQuery->sqlExecute();
        if (!$r) {
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"VARS_NAME_ID=>$in_vars_name_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        // 主キー指定の取得のため1レコード固定
        //$tgt_row = $objQuery->resultFetch();
        $result_array = array();
        while ( $tgt_row = $objQuery->resultFetch() ){
            $result_array[$tgt_row["ARRAY_MEMBER_ID"]] = $tgt_row;
        }
        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0040
    // 処理内容
    //   多次元配列のメンバー変数へのパス配列を生成
    //
    // パラメータ
    //   $in_var_name_str:     多次元配列のメンバー変数へのパス
    //   $ina_var_path_array:  多次元配列のメンバー変数へのパス配列
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function makeHostVarsPath($in_var_name_str,&$ina_var_path_array){
        $ina_var_path_array = array();
        // [3].array1.array2[0].array2_2[0].array2_2_2[0].array2_2_2_2
        // []を取り除く
        $in_var_name_str = str_replace('[','.',$in_var_name_str);
        $in_var_name_str = str_replace(']','',$in_var_name_str);
        // 先頭が配列の場合の . を取り除く
        $in_var_name_str = preg_replace('/^\./',"", $in_var_name_str);
        $ina_var_path_array = explode('.',$in_var_name_str);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0041
    // 処理内容
    //   多次元配列の具体値情報をホスト変数ファイルに戻す為の配列作成
    //
    // パラメータ
    //   $ina_var_path_array:     多次元配列のメンバー変数へのパス配列
    //   $in_idx:                 階層番号(0～)
    //   $in_out_array:           ホスト変数ファイルに戻す為の配列
    //   $in_var_type:            メンバー変数のタイプ
    //                              1: Key-Value変数
    //                              2: 複数具体値変数
    //   $in_var_val:             メンバー変数の具体値
    //   $in_ass_no:              複数具体値変数の場合の代入順序
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function makeHostVarsArray($in_key_array,$in_idx,&$in_out_array,$in_var_type,$in_var_val,$in_ass_no){
        // 末端の変数に達したか判定
        if(count($in_key_array) <= $in_idx){
            // 末端の変数か判定
            if(count($in_key_array) == $in_idx){
                // 具体値を埋め込む
                if($in_var_type == '1'){
                    // Key-Value変数の場合
                    $in_out_array = trim($in_var_val);
                }
                else{
                    // 複数具体値の場合
                    $in_out_array[$in_ass_no] = trim($in_var_val);
                    // 代入順序で昇順ソートする。
                    ksort($in_out_array);
                }
            }
            return;
         }
         // 該当階層の変数名を取得
         $var_name = $in_key_array[$in_idx];
         // ホスト変数配列に変数名が退避されているか判定
         if(@count($in_out_array[$var_name]) == 0){
             // 変数名をホスト変数配列に退避
             $in_out_array[$var_name] = array();
             // 配列の場合に列順序で昇順ソート
             if(is_numeric($var_name)){
                 ksort($in_out_array);
             }
         }
         $in_idx++;
         // 次の階層へ
         $this->makeHostVarsArray($in_key_array,$in_idx,$in_out_array[$var_name],$in_var_type,$in_var_val,$in_ass_no);
    }
    // F0042
    function MultiArrayVarsToYamlFormatSub($ina_host_vars_array,
                                          &$in_str_hostvars,
                                           $in_before_vars,
                                           $in_indent,
                                           $nest_level,
                                          &$in_error_code,
                                          &$in_line,
                                          &$ina_legacy_Role_cpf_vars_list)
    {
        $idx = 0;

        // 配列階層か判定
        $array_f = $this->is_assoc($ina_host_vars_array);
        if($array_f == -1){
            $in_error_code = "ITAANSIBLEH-ERR-90232";
            $in_line       = __LINE__;
            return false;
        }

        if($array_f != 'I'){
            $indent = $in_indent . "  ";
            $nest_level = $nest_level + 1;
        }
        else{
            $indent = $in_indent;
        }

        foreach($ina_host_vars_array as $var => $val) {

            // 繰返数設定
            $idx = $idx + 1;

            // 現階層の変数名退避
            $before_vars = $var;

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
                    $in_error_code = "ITAANSIBLEH-ERR-90233";
                    $in_line       = __LINE__;
                    return false;
                }
            }
            // 複数具体値か判定する。
            if(is_numeric($var)){

                // 具体値があるか判定
                if( ! is_array($val)){
                    // 変数の具体値にコピー変数が使用されていないか確認
                    $objLibs = new AnsibleCommonLibs();
                    $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                    $val,
                                                                    $ina_legacy_Role_cpf_vars_list);
                    unset($objLibs);
                    if($ret == false){
                        //エラーメッセージは出力しているので、ここでは何も出さない。
                        $in_error_code = "";
                        return false;
                    }
                    
                    // 具体値出力
                    // - xxxxxxx
                    $vars_str = sprintf("%s- %s\n",$indent,$val);
                    $in_str_hostvars = $in_str_hostvars . $vars_str;

                    continue;
                }
                else{
                    // 具体値がないので配列階層
                    // 配列階層の場合はインデントを1つ戻す。
                    if($idx == 1){
                        $indent = substr($indent,0,-2);

                    }
                }
            }
            else{
                // 1つ前の階層が配列階層か判定
                if(is_numeric($in_before_vars)){

                    // Key-Value変数か判定
                    if( ! is_array($val)){
                        // Key-Value変数の場合
                        if($idx == 1){
                            // 変数の具体値にコピー変数が使用されていないか確認
                            $objLibs = new AnsibleCommonLibs();
                            $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                            $val,
                                                                            $ina_legacy_Role_cpf_vars_list);
                            unset($objLibs);
                            if($ret == false){
                                //エラーメッセージは出力しているので、ここでは何も出さない。
                                $in_error_code = "";
                                return false;
                            }

                            // 変数と具体値出力 配列の先頭変数なので - を付ける
                            // - xxxxx: xxxxxxx
                            $vars_str = sprintf("%s- %s: %s\n",$indent,$var,$val);
                            $in_str_hostvars = $in_str_hostvars . $vars_str;

                            // インデント位置を加算
                            $indent = $indent . "  ";

                        }
                        else{
                            // 変数の具体値にコピー変数が使用されていないか確認
                            $objLibs = new AnsibleCommonLibs();
                            $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                            $val,
                                                                            $ina_legacy_Role_cpf_vars_list);
                            unset($objLibs);
                            if($ret == false){
                                //エラーメッセージは出力しているので、ここでは何も出さない。
                                $in_error_code = "";
                                return false;
                            }

                            // 変数と具体値出力 配列の先頭変数ではないので - は付けない
                            //   xxxxx: xxxxxx
                            // インデント位置は加算済み
                            $vars_str = sprintf("%s%s: %s\n",$indent,$var,$val);
                            $in_str_hostvars = $in_str_hostvars . $vars_str;

                        }
                        continue;
                    }
                    else{
                        // ネスト変数の場合
                        if($idx == 1){
                            // 変数出力 配列の先頭変数なので - を付ける
                            $vars_str = sprintf("%s- %s:\n",$indent,$var);
                            $in_str_hostvars = $in_str_hostvars . $vars_str;

                            // インデント位置を加算
                            $indent = $indent . "  ";

                        }
                        else{
                            // 変数出力 配列の先頭変数ではないので - は付けない
                            $vars_str = sprintf("%s%s:\n",$indent,$var);
                            $in_str_hostvars = $in_str_hostvars . $vars_str;

                        }
                    }
                }
                else{
                    // Key-Value変数か判定
                    if( ! is_array($val)){
                        // 変数の具体値にコピー変数が使用されていないか確認
                        $objLibs = new AnsibleCommonLibs();
                        $ret = $this->LegacyRoleCheckConcreteValueIsVar($objLibs,
                                                                        $val,
                                                                        $ina_legacy_Role_cpf_vars_list);
                        unset($objLibs);
                        if($ret == false){
                            //エラーメッセージは出力しているので、ここでは何も出さない。
                            $in_error_code = "";
                            return false;
                        }

                        // 変数と具体値出力
                        // xxxxx: xxxxxxx
                        $vars_str = sprintf("%s%s: %s\n",$indent,$var,$val);
                        $in_str_hostvars = $in_str_hostvars . $vars_str;

                        continue;
                    }
                    else{
                        // ネスト変数として出力
                        // xxxxx:
                        $vars_str = sprintf("%s%s:\n",$indent,$var);
                        $in_str_hostvars = $in_str_hostvars . $vars_str;

                    }
                }
            }
            $ret = $this->MultiArrayVarsToYamlFormatSub($val,
                                                        $in_str_hostvars,
                                                        $before_vars,
                                                        $indent,
                                                        $nest_level,
                                                        $in_error_code,
                                                        $in_line,
                                                        $ina_legacy_Role_cpf_vars_list);
            if($ret === false){
                return false;
            }
        }
        return true;
    }

    // F0043
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
    ////////////////////////////////////////////////////////////////////////////////
    // F0044
    // 処理内容
    //   グローバル変数の情報をデータベースより取得する。
    // 
    // パラメータ
    //   $in_global_vars_list:     グローバル変数のリスト
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBGlobalVarsMaster(&$ina_global_vars_list){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $sql = "SELECT                         \n" .
               "  VARS_NAME,                   \n" .
               "  VARS_ENTRY                   \n" .
               "FROM                           \n" .
               "  B_ANS_GLOBAL_VARS_MASTER     \n" .
               "WHERE                          \n" .
               "  DISUSE_FLAG            = '0';\n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
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
    // F0045
    // 処理内容
    //   Legacy Role用 Role内のcopyモジュールで使用している変数
    //   抜出しホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    //
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //   $ina_rolenames:        処理対象ロールリスト
    //                          [INCLUDE_SEQ][ROLE_ID]=ROLE_NAME
    //
    //   $ina_cpf_vars_list:    copyモジュールで使用している変数リスト
    //                          [使用Playbookファイル名][行番号][変数名][CONTENTS_FILE_ID]
    //                          [使用Playbookファイル名][行番号][変数名][CONTENTS_FILE]
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateLegacyRoleCopyFiles($ina_hosts,$ina_hostprotcollist,$ina_rolenames,$ina_cpf_vars_list){
        ///////////////////////////////////////////////////////////////////
        // 処理対象のロール名抽出
        ///////////////////////////////////////////////////////////////////
        // 処理対象のロール名抽出
        $tgt_role_list = array();
        foreach( $ina_rolenames as $no=>$rolename_list ){
            foreach( $rolename_list as $rolepkey=>$rolename ){
                $tgt_role_list[$rolename] = 1;
            }
        }
        ///////////////////////////////////////////////////////////////////
        // copy変数に紐づくファイルの情報を取得
        ///////////////////////////////////////////////////////////////////
        $la_cpf_files = array();
        $la_cpf_path = array();
        foreach( $ina_cpf_vars_list as $role_name => $tgt_file_list ){
            if(@strlen($tgt_role_list[$role_name]) == 0){
                continue;
            }
            foreach( $tgt_file_list as $tgt_file => $line_no_list ){
                foreach( $line_no_list as $line_no => $cpf_var_name_list ){
                    foreach( $cpf_var_name_list as $cpf_var_name => $file_info_list ){
                        // inディレクトリ配下のcopyファイルバスを取得
                        $cpf_path = $this->getHostvarsfile_copy_file_value($file_info_list['CONTENTS_FILE_ID'],
                                                                           $file_info_list['CONTENTS_FILE']);
                        // $la_cpf_path[copy変数]=inディレクトリ配下ののcopyファイルパス
                        $la_cpf_path[$cpf_var_name] = $cpf_path;

                        // copyファイルのpkeyとファイル名を退避
                        $la_cpf_files[$file_info_list['CONTENTS_FILE_ID']]=$file_info_list['CONTENTS_FILE'];
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////////////////
        // copyファイルを所定のディレクトリにコピー
        ///////////////////////////////////////////////////////////////////
        if(count($la_cpf_files) > 0){
            $ret = $this->CreateCopyfiles($la_cpf_files);
            if( $ret == false ){
                return false;
            }
        }
        ///////////////////////////////////////////////////////////////////
        // ホスト変数定義ファイルにcopy変数を追加
        ///////////////////////////////////////////////////////////////////
        if ( count($la_cpf_path) > 0 ){
            // ホスト変数配列のホスト)分繰返し
            foreach( $ina_hosts as $host_name){
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                //ホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_host_var_file($hostname);
                // ホスト変数定義ファイルにテンプレート変数を追加
                if($this->CreateRoleHostvarsfile("CPF",$file_name,$la_cpf_path,"","","","","a") === false)
                {
                    return false;
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0046
    // 処理内容
    //   読替表のデータを取得する。
    // 
    // パラメータ
    //   $in_pattern_id:                 該当作業パターン
    //   $ina_translationtable_list:     読替表のデータリスト
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBTranslationTable($in_pattern_id,&$ina_translationtable_list){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $sql = "SELECT                                              \n" .
               "  TBL_2.ROLE_PACKAGE_ID,                            \n" .
               "  TBL_2.ROLE_ID,                                    \n" .
               "  TBL_2.REP_VARS_NAME,                              \n" .
               "  TBL_2.ANY_VARS_NAME                               \n" .
               "FROM                                                \n" .
               "  B_ANS_LRL_RP_REP_VARS_LIST TBL_2 LEFT OUTER JOIN  \n" .
               "  (                                                 \n" .
               "    SELECT                                          \n" .
               "      ROLE_PACKAGE_ID,                              \n" .
               "      ROLE_ID                                       \n" .
               "    FROM                                            \n" .
               "      B_ANSIBLE_LRL_PATTERN_LINK                    \n" .
               "    WHERE                                           \n" .
               "      PATTERN_ID  = :PATTERN_ID AND                 \n" .
               "      DISUSE_FLAG = '0'                             \n" .
               "  ) TBL_1 ON (TBL_1.ROLE_PACKAGE_ID =               \n" .
               "              TBL_2.ROLE_PACKAGE_ID AND             \n" .
               "              TBL_1.ROLE_ID         =               \n" .
               "              TBL_2.ROLE_ID)                        \n" .
               "WHERE                                               \n" .
               "  TBL_2.DISUSE_FLAG = '0' AND                       \n" .                  
               "  TBL_1.ROLE_PACKAGE_ID is not NULL                 \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
    
            unset($objQuery);
            return false;
        }

        $ina_translationtable_list = array();

        while ( $row = $objQuery->resultFetch() ){
            $ina_translationtable_list[$row['REP_VARS_NAME']] = $row['ANY_VARS_NAME'];        
        }

        // DBアクセス事後処理
        unset($objQuery);
    
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのSSH秘密鍵ファイル格納ディレクトリパス(ssh_key_files)を記憶
    // パラメータ
    //   $in_dir:      ssh_key_filesディレクトリ
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_ssh_key_files_Dir($in_indir){
        $this->lv_Ansible_ssh_key_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのSSH秘密鍵ファイル格納ディレクトリパス(ssh_key_files)を取得
    // パラメータ
    //   なし
    //
    // 戻り値
    //   copy_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_ssh_key_files_Dir(){
        return($this->lv_Ansible_ssh_key_files_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITAが機器一覧で管理しているSSH秘密鍵ファイルのパスを取得
    // パラメータ
    //   $in_key:        SSH秘密鍵ファイルのPkey(データベース)
    //   $in_filename:   SSH秘密鍵ファイル名
    //
    // 戻り値
    //   ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_ssh_key_file($in_key,$in_filename){
        global  $root_dir_path;

        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_SSH_KEY_FILE_DIR_MK,
                        $root_dir_path . "/" . self::LC_ITA_SSH_KEY_FILE_PATH,
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのSSH秘密鍵ファイルパス(ssh_key_files)を取得
    // パラメータ
    //   $in_pkey:    SSH秘密鍵ファイルのPkey(データベース)
    //   $in_file:    SSH秘密鍵ファイル
    //
    // 戻り値
    //   inディレクトリ内のSSH認証ファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getIN_ssh_key_file($in_pkey,$in_file){
        $intNumPadding = 10;
        $file = sprintf(self::LC_IN_SSH_KEY_FILE_MK,
                        $this->getAnsible_ssh_key_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのSSH秘密鍵ファイルパス(ssh_key_files)を取得
    // パラメータ
    //   $in_pkey:    SSH秘密鍵ファイルのPkey(データベース)
    //   $in_file:    SSH秘密鍵ファイル
    //
    // 戻り値
    //   inディレクトリ内のSSH秘密鍵ファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function CreateSSH_key_file($in_pkey,$in_ssh_key_file,&$in_in_dir_ssh_key_file){
        //SSH秘密鍵ファイルが存在しているか確認
        $src_file = $this->getITA_ssh_key_file($in_pkey,$in_ssh_key_file);
        if( file_exists($src_file) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000012",array($in_pkey,basename($src_file)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // Ansible実行時のSSH秘密鍵ファイルパス取得
        $dst_file = $this->getIN_ssh_key_file($in_pkey,$in_ssh_key_file);

        //SSH認証ファイルをansible用ディレクトリにコピーする。
        if( copy($src_file,$dst_file) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000013",array($in_pkey,basename($src_file)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        if( !chmod( $dst_file, 0600 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000014",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // Ansible実行時のSSH秘密鍵ファイルパス退避
        $in_in_dir_ssh_key_file = $dst_file;
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのwinRMサーバー証明書ファイル格納ディレクトリパス(win_ca_files)を記憶
    // パラメータ
    //   $in_dir:      win_ca_filesディレクトリ
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setAnsible_win_ca_files_Dir($in_indir){
        $this->lv_Ansible_win_ca_files_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのwinRMサーバー証明書ファイル格納ディレクトリパス(win_ca_files)を取得
    // パラメータ
    //   なし
    //
    // 戻り値
    //   copy_filesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getAnsible_win_ca_files_Dir(){
        return($this->lv_Ansible_win_ca_files_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITAが機器一覧で管理しているwinRMサーバー証明書ファイルのパスを取得
    // パラメータ
    //   $in_key:        winRMサーバー証明書ファイルのPkey(データベース)
    //   $in_filename:   winRMサーバー証明書ファイル名
    //
    // 戻り値
    //   ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_win_ca_file($in_key,$in_filename){
        global  $root_dir_path;

        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_WIN_CA_FILE_DIR_MK,
                        $root_dir_path . "/" . self::LC_ITA_WIN_CA_FILE_PATH,
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリからのwinRMサーバー証明書ファイルパス(win_ca_files)を取得
    // パラメータ
    //   $in_pkey:    winRMサーバー証明書ファイルのPkey(データベース)
    //   $in_file:    winRMサーバー証明書ファイル名
    //
    // 戻り値
    //   inディレクトリ内のSSH認証ファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getIN_win_ca_file($in_pkey,$in_file){
        $intNumPadding = 10;
        $file = sprintf(self::LC_IN_WIN_CA_FILE_MK,
                        $this->getAnsible_win_ca_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリにwinRMサーバー証明書ファイルをコピーする。
    // パラメータ
    //   $in_pkey:    winRMサーバー証明書ファイルのPkey(データベース)
    //   $in_file:    winRMサーバー証明書ファイル名
    //
    // 戻り値
    //   
    ////////////////////////////////////////////////////////////////////////////////
    function CreateWIN_cs_file($in_pkey,$in_win_ca_file,&$in_dir_win_ca_file){
        //サーバー証明書が存在しているか確認
        $src_file = $this->getITA_win_ca_file($in_pkey,$in_win_ca_file);
        if( file_exists($src_file) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000034",array($in_pkey,basename($src_file)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // Ansible実行時のサーバー証明書パス取得
        $dst_file = $this->getIN_win_ca_file($in_pkey,$in_win_ca_file);

        //サーバー証明書をansible用ディレクトリにコピーする。
        if( copy($src_file,$dst_file) === false ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000035",array($in_pkey,basename($src_file)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        if( !chmod( $dst_file, 0600 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000036",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // Ansible実行時のサーバー証明書ファイルパス退避
        $in_dir_win_ca_file = $dst_file;
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0047
    // 処理内容
    //   Legacy用 
    //   変数の具体値にコピー/テンプレート変数が使用されてるかを判定
    //   使用されている場合に各ファイルを所定のディレクトリにコピーする。
    // パラメータ
    //   $in_templ_vars_chk:         テンプレート変数の場合にテンプレートファイル内の変数具体値チェック有無
    //                               true:チェック有 false:チェック無
    //   $in_var_name:               変数名
    //   $in_var_val:                変数の具体値
    //
    //   $ina_var_list:              ホスト変数配列
    //                               [ 変数名 ]=>具体値
    //
    //   $in_host_name:              ホスト名
    //
    //   $ina_legacy_tpf_vars_list:  変数の具体値にテンプレート変数が使用されているコピー変数のリスト
    //
    //   $ina_legacy_cpf_vars_list:  変数の具体値にコピー変数が使用されているコピー変数のリスト
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    // 
    ////////////////////////////////////////////////////////////////////////////////
    function LegacyCheckConcreteValueIsVar($in_templ_vars_chk,
                                           $in_var_val,
                                           $ina_var_list,
                                           $in_host_name,
                                          &$ina_legacy_tpf_vars_list,
                                          &$ina_legacy_cpf_vars_list){
        $tpf_vars_list = array();
        $cpf_vars_list = array();
        // テンプレート変数　{{ TPF_[a-zA-Z0-9_] }} を取出す
        $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$in_var_val,$var_match);
        if(($ret !== false) && ($ret > 0)){
            foreach($var_match[0] as $var_name){
                $ret = preg_match_all("/TPF_" . "[a-zA-Z0-9_]*/",$var_name,$var_name_match);
                $var_name = trim($var_name_match[0][0]);
                if(@strlen($ina_legacy_tpf_vars_list[$in_host_name][$var_name]) == 0){
                    $tpf_vars_list[$var_name] = "";
                }
            }
        }
        // コピー変数　{{ CPF_[a-zA-Z0-9_] }} を取出す
        $ret = preg_match_all("/{{(\s)" . "CPF_" . "[a-zA-Z0-9_]*(\s)}}/",$in_var_val,$var_match);
        if(($ret !== false) && ($ret > 0)){
            foreach($var_match[0] as $var_name){
                $ret = preg_match_all("/CPF_" . "[a-zA-Z0-9_]*/",$var_name,$var_name_match);
                $var_name = trim($var_name_match[0][0]);
                if(@strlen($ina_legacy_cpf_vars_list[$in_host_name][$var_name]) == 0){
                    $cpf_vars_list[$var_name] = "";
                }
            }
        }
        ///////////////////////////////////////////////////////////////////
        // テンプレート変数の情報処理
        ///////////////////////////////////////////////////////////////////
        $la_tpf_files = array();
        foreach( $tpf_vars_list as $tpf_var_name=>$dummy){
            if(@strlen($ina_legacy_tpf_vars_list[$in_host_name][$tpf_var_name]) == 0){
                ///////////////////////////////////////////////////////////////////
                // テンプレート変数に紐づくテンプレートファイルの情報を取得
                ///////////////////////////////////////////////////////////////////
                $tpf_key = "";
                $tpf_file_name = "";
                // テンプレート変数名からテンプレートファイル名とPkeyを取得する。
                $ret = $this->getDBLegacyTemplateMaster($tpf_var_name,$tpf_key,$tpf_file_name);
                if( $ret == false ){
                    //エラーが発生した場合は処理終了
                    return false;
                }
                // テンプレート変数名が未登録の場合
                if( $tpf_key == "" ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000017",
                                                                array($tpf_var_name)); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    //テンプレート変数名が未登録
                    return false;
                }
                else{
                    // テンプレートファイル名が未登録の場合
                    if($tpf_file_name == "" ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000018",
                                                                    array($tpf_var_name)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        return false;
                    }
                }

                ///////////////////////////////////////////////////////////////////
                // テンプレート変数に紐づくテンプレートファイル内の変数確認
                ///////////////////////////////////////////////////////////////////
                // テンプレートファイルバスを取得
                $tpf_path = $this->getHostvarsfile_template_file_value($tpf_key,$tpf_file_name);

                // テンプレートファイルパスのパスを退避
                $ina_legacy_tpf_vars_list[$in_host_name][$tpf_var_name] = $tpf_path;

                // テンプレートファイルのpkeyとファイル名を退避 
                $la_tpf_files[$tpf_key]=$tpf_file_name;

                if($in_templ_vars_chk === true){
                    // テンプレートファイル内のホスト変数を確認
                    $ret = $this->LegacyCheckConcreteValueIsVarTemplatefile($in_host_name,$ina_var_list,
                                                                        $tpf_var_name,$tpf_key,$tpf_file_name);
                    if( $ret === false ){
                        return false;
                    }
                }
            }
        }

        ///////////////////////////////////////////////////////////////////
        // テンプレート変数に紐づくテンプレートファイルを所定のディレクトリに配置
        ///////////////////////////////////////////////////////////////////
        if(count($la_tpf_files) > 0){
            // テンプレートファイルを所定のディレクトリにコピーする。
            $ret = $this->CreateTemplatefiles($la_tpf_files);
            if( $ret == false ){
                return false;
            }
        }
        ///////////////////////////////////////////////////////////////////
        // コピー変数の情報処理
        ///////////////////////////////////////////////////////////////////
        $la_cpf_files = array();
        foreach( $cpf_vars_list as $cpf_var_name=>$dummy){
            if(@strlen($ina_legacy_cpf_vars_list[$in_host_name][$cpf_var_name]) == 0){
                ///////////////////////////////////////////////////////////////////
                // コピー変数に紐づくファイルの情報を取得
                ///////////////////////////////////////////////////////////////////
                $cpf_key = "";
                $cpf_file_name = "";
                // copy変数名からコピーファイル名とPkeyを取得する。
                $ret = $this->getDBLegacyCopyMaster($cpf_var_name,$cpf_key,$cpf_file_name);
                if( $ret == false ){
                    //エラーが発生した場合は処理終了
                    return false;
                }
                // コピー変数名が未登録の場合
                if( $cpf_key == "" ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000029",
                                                                array($cpf_var_name)); 
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                    //コピー変数名が未登録
                    return false;
                }
                else{
                    // コピーファイル名が未登録の場合
                    if($cpf_file_name == "" ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000030",
                                                                    array($cpf_var_name)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        return false;
                    }
                }
                // inディレクトリ配下のcopyファイルバスを取得
                $cpf_path = $this->getHostvarsfile_copy_file_value($cpf_key,$cpf_file_name);

                // $ina_legacy_cpf_vars_list[copy変数]=inディレクトリ配下ののcopyファイルパス
                $ina_legacy_cpf_vars_list[$in_host_name][$cpf_var_name] = $cpf_path;

                // copyファイルのpkeyとファイル名を退避 
                $la_cpf_files[$cpf_key]=$cpf_file_name;

            }
        }
        ///////////////////////////////////////////////////////////////////
        // コピー変数に紐づくファイルを所定のディレクトリに配置
        ///////////////////////////////////////////////////////////////////
        if(count($la_cpf_files) > 0){
            // copyファイルを所定のディレクトリにコピーする。
            $ret = $this->CreateCopyfiles($la_cpf_files);
            if( $ret == false ){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0048
    // 処理内容
    //   Legacy用 
    //   変数の具体値にテンプレート変数が使用されていた場合にテンプレートで使用
    //   している変数がホスト変数に登録されているかチェックする。
    // パラメータ
    //   $in_host_name:         ホスト名
    // 
    //   $ina_var_list:         ホスト変数配列
    //                          [ 変数名 ]=>具体値
    //
    //   $in_tpf_val_name:      テンプレート変数名
    //
    //   $in_tpf_key:           テンプレートファイルPkey
    // 
    //   $in_tpf_file_name:     テンプレートファイル名
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function LegacyCheckConcreteValueIsVarTemplatefile($in_host_name,$ina_var_list,
                                                       $in_tpf_val_name,$in_tpf_key,$in_tpf_file_name){
        $result_code = true;
        ///////////////////////////////////////////////////////////////////
        // テンプレートで使用している変数がホストの変数に登録されているか判定
        ///////////////////////////////////////////////////////////////////
        $templatefile = $this->getITA_template_file($in_tpf_key,$in_tpf_file_name);

        // テンプレートに登録されている変数を抜出す。
        $dataString = file_get_contents($templatefile);

        if($dataString === false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000015",
                                                        array(basename($templatefile)));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // ローカル変数のリスト初期化
        $local_vars = array();
        
        $file_global_vars_list = array();
        // テンプレートに登録されているグローバル変数を抜出す
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_TEMP_GBL_HED,$dataString,$local_vars);
        $file_global_vars_list = $objWSRA->getTPFVARSParsedResult();
        unset($objWSRA);

        // テンプレートに登録されているグローバル変数のデータベース登録確認 
        foreach( $file_global_vars_list as $var_name ){
            if(@count($this->lva_global_vars_list[$var_name]) == 0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000016",
                                                            array(basename($templatefile),
                                                                  $var_name));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }

        // ローカル変数のリスト作成
        $local_vars = array();
        $local_vars[] = self::LC_ANS_PROTOCOL_VAR_NAME;
        $local_vars[] = self::LC_ANS_USERNAME_VAR_NAME;
        $local_vars[] = self::LC_ANS_PASSWD_VAR_NAME;
        $local_vars[] = self::LC_ANS_LOGINHOST_VAR_NAME;
        $local_vars[] = self::LC_ANS_OUTDIR_VAR_NAME;
  
        // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
        $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

        $file_vars_list = array();
        // ホスト変数を抜出す
        $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$local_vars);
        $file_vars_list = $objWSRA->getTPFVARSParsedResult();
        unset($objWSRA);
    
        // テンプレートで変数が使用されているか判定
        if(count($file_vars_list) > 0){
            if(count($ina_var_list) == 0){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000019",
                                                            array($in_host_name,
                                                                  basename($templatefile)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        // テンプレートに登録されている変数のデータベース登録確認 
        foreach( $file_vars_list as $var_name ){
            if(@strlen($ina_var_list[$var_name])==0){
                if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000020",
                                                                array(basename($templatefile),
                                                                      $var_name,
                                                                      $in_host_name));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
                elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000021",
                                                                array(basename($templatefile),
                                                                      $var_name,
                                                                      $in_host_name));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
                elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000022",
                                                                array(basename($templatefile),
                                                                      $var_name,
                                                                      $in_host_name));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
                elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000023",
                                                                array(basename($templatefile),
                                                                      $var_name,
                                                                      $in_host_name));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
                else{
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000024",
                                                                array(basename($templatefile),
                                                                      $var_name,
                                                                      $in_host_name));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
                $result_code = false;
            }
            else{
                //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                if($ina_var_list[$var_name] == self::LC_ANS_UNDEFINE_NAME){
                    // プロトコル未登録
                    if($var_name == self::LC_ANS_PROTOCOL_VAR_NAME){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000025",
                                                                    array(basename($templatefile),
                                                                          $var_name,
                                                                          $in_host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                    }
                    // ユーザー名未登録
                    elseif($var_name == self::LC_ANS_USERNAME_VAR_NAME){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000026",
                                                                    array(basename($templatefile),
                                                                          $var_name,
                                                                          $in_host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                    }
                    // ログインパスワード未登録
                    elseif($var_name == self::LC_ANS_PASSWD_VAR_NAME){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000027",
                                                                    array(basename($templatefile),
                                                                          $var_name,
                                                                          $in_host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                    }
                    // ホスト名未登録
                    elseif($var_name == self::LC_ANS_LOGINHOST_VAR_NAME){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000028",
                                                                    array(basename($templatefile),
                                                                          $var_name,
                                                                          $in_host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                    }
                }
            }
        }
        return($result_code);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0049
    // 処理内容
    //   LegacyRole用 
    //   変数の具体値にコピー/テンプレート変数が使用されてるかを判定
    //   使用されている場合に各ファイルを所定のディレクトリにコピーする。
    // パラメータ
    //   $in_objLibs:                AnsibleCommonLibsクラスオブジェクト
    //
    //   $in_var_val:                変数の具体値
    //
    //   $ina_legacy_cpf_vars_list:  変数の具体値にコピー変数が使用されているコピー変数のリスト
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    // 
    ////////////////////////////////////////////////////////////////////////////////
    function LegacyRoleCheckConcreteValueIsVar($in_objLibs,
                                               $in_var_val,
                                              &$ina_legacy_Role_cpf_vars_list){
        $cpf_vars_list = array();
        // コピー変数　{{ CPF_[a-zA-Z0-9_] }} を取出す
        $ret = preg_match_all("/{{(\s)" . "CPF_" . "[a-zA-Z0-9_]*(\s)}}/",$in_var_val,$var_match);
        if(($ret !== false) && ($ret > 0)){
            foreach($var_match[0] as $var_name){
                $ret = preg_match_all("/CPF_" . "[a-zA-Z0-9_]*/",$var_name,$var_name_match);
                $var_name = trim($var_name_match[0][0]);
                if(@strlen($ina_legacy_Role_cpf_vars_list[$var_name]) == 0){
                    $cpf_vars_list[$var_name] = "";
                }
            }
        }
        if(count($cpf_vars_list) == 0){
            return true;
        }
        $objLibs = new AnsibleCommonLibs();
        ///////////////////////////////////////////////////////////////////
        // コピー変数の情報処理
        ///////////////////////////////////////////////////////////////////
        $la_cpf_files = array();
        foreach( $cpf_vars_list as $cpf_var_name=>$dummy){
            if(@strlen($ina_legacy_Role_cpf_vars_list[$cpf_var_name]) == 0){
                $strErrMsg       = "";
                $strErrDetailMsg = "";
                $cpf_key         = "";
                $cpf_file_name   = "";
                ///////////////////////////////////////////////////////////////////
                // コピー変数に紐づくファイルの情報を取得
                ///////////////////////////////////////////////////////////////////
                $ret = $in_objLibs->getDBLegacyRoleCopyMaster($this->lv_objMTS,$this->lv_objDBCA,
                                                       $cpf_var_name,$cpf_key,$cpf_file_name,
                                                       $strErrMsg,$strErrDetailMsg);
                if($ret === false){
                    //エラーが発生した場合は処理終了
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$strErrMsg);
                    if($strErrDetailMsg != ""){
                        $this->DebugLogPrint(basename(__FILE__),__LINE__,$strErrDetailMsg);
                    }
                    return false;
                }
                else{
                    // コピーファイル名が未登録の場合
                    if($cpf_file_name == "" ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000030",
                                                                    array($cpf_var_name)); 
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
    
                        return false;
                    }
                }
                // inディレクトリ配下のcopyファイルバスを取得
                $cpf_path = $this->getHostvarsfile_copy_file_value($cpf_key,$cpf_file_name);
   
                // $ina_legacy_Role_cpf_vars_list[copy変数]=inディレクトリ配下ののcopyファイルパス
                $ina_legacy_Role_cpf_vars_list[$cpf_var_name] = $cpf_path;
    
                // copyファイルのpkeyとファイル名を退避 
                $la_cpf_files[$cpf_key]=$cpf_file_name;

            }
        }
        ///////////////////////////////////////////////////////////////////
        // コピー変数に紐づくファイルを所定のディレクトリに配置
        ///////////////////////////////////////////////////////////////////
        if(count($la_cpf_files) > 0){
            // copyファイルを所定のディレクトリにコピーする。
            $ret = $this->CreateCopyfiles($la_cpf_files);
            if( $ret == false ){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0050
    // 処理内容
    //   グローバル変数・テンプレート変数・コピー変数をホスト変数の情報を
    //   ホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CommitHostVarsfiles($ina_hosts,$ina_hostprotcollist,$ina_host_vars){
        // ホスト変数配列のホスト)分繰返し
        foreach( $ina_hosts as $host_name){
            foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
            $host_vars_file = $hostname;
            //該当ホストの変数配列を取得
            $vars_list = $ina_host_vars[$host_name];
            $file_name = $this->getAnsible_host_var_file($host_vars_file);
            // グローバル変数・テンプレート変数・コピー変数をホスト変数ファイルに出力
            if($this->CreateHostvarsfile("CMT",$host_name,$file_name,$vars_list,"","","a") === false){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のcopyファイルパスを取得
    // パラメータ
    //   $in_pkey:    テンプレートファイル Pkey 現在未使用
    //   $in_file:    テンプレートファイル
    // 
    // 戻り値
    //   ホスト変数ファイル内のテンプレートファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_pioneer_copy_file_value($in_pkey,$in_file){
        $intNumPadding = 10;
        $file = sprintf(self::LC_HOSTVARSFILE_COPY_FILE_MK,
                        $this->getAnsible_copy_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0054
    // 処理内容
    //   Pioneer用 対話ファイルよりcopyモジュールで使用している変数を
    //   抜出しホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    // 
    //   $ina_dialog_files:     対話ファイル配列
    //                          [ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
    // 
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    // 
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreatePioneerCopyFiles($ina_hosts,$ina_dialog_files,$ina_hostprotcollist,$ina_host_vars){
        $result_code = true;

        $la_cpf_path  = array();

        // 対話ファイル配列のホスト分繰返し
        foreach( $ina_hosts as $no=>$host_name ){
            // 対話ファイル配列より該当ホストの対話ファイル配列取得
            $dialog_file_list = $ina_dialog_files[$host_name];
            foreach( $dialog_file_list as $includeno=>$pkeylist ){
                foreach( $pkeylist as $playbook_pkey=>$playbook ){
                    // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 する
                    // 対話ファイルのパス取得
                    foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)

                    $file_name = $this->getAnsible_dialog_file($hostname,$playbook_pkey,$playbook);

                    // 対話ファイルの内容を取得 ここまでの過程でファイルの存在は確認
                    $dataString = file_get_contents($file_name);

                    ///////////////////////////////////////////////////////////////////
                    // 対話ファイルでcopy変数が使用されているか確認
                    ///////////////////////////////////////////////////////////////////
                    SimpleVerSearch(DF_HOST_CPF_HED,$dataString,$la_cpf_vars);

                    ///////////////////////////////////////////////////////////////////
                    // copy変数に紐づくファイルの情報を取得
                    ///////////////////////////////////////////////////////////////////
                    $la_cpf_files = array();
                    foreach( $la_cpf_vars as $no => $cpf_var_list ){
                        foreach( $cpf_var_list as $line_no  => $cpf_var_name ){
                            $cpf_key = "";
                            $cpf_file_name = "";
                            // copy変数名からコピーファイル名とPkeyを取得する。
                            $ret = $this->getDBPioneerCopyMaster($cpf_var_name,$cpf_key,$cpf_file_name);
                            if( $ret == false ){
                                //エラーが発生した場合は処理終了
                                return false;
                            }
                            // copy変数名が未登録の場合
                            if( $cpf_key == "" ){
                                //OK
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90090",
                                                                           array(basename($playbook),
                                                                           $line_no,
                                                                           $cpf_var_name)); 
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                //copy変数名が未登録
                                $result_code = false;
                                continue;
                            } else {
                                // copyファイル名が未登録の場合
                                if($cpf_file_name == "" ){
                                    //OK
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-90091",
                                                                               array(basename($playbook),
                                                                               $line_no,
                                                                               $cpf_var_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                    $result_code = false;
                                    continue;
                                }
                            }

                            // copyファイルのpkeyとファイル名を退避
                            // la_cpf_files[pkey]=copyファイル
                            $la_cpf_files[$cpf_key]=$cpf_file_name;

                            // inディレクトリ配下のcopyファイルバスを取得
                            $cpf_path = $this->getHostvarsfile_pioneer_copy_file_value($cpf_key,$cpf_file_name);

                            $cpf_path = str_replace($this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ITA'),
                                                    $this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ANS'),
                                                    $cpf_path);

                            // $la_cpf_path[copy変数]=inディレクトリ配下のcopyファイルパス
                            $la_cpf_path[$cpf_var_name] = $cpf_path;
                        }
                    }
                }

                // 前処理でエラーが発生している場合は次のファイルへ
                if($result_code === false){
                    continue;
                }

                if(count($la_cpf_files) > 0){
                    // copyファイルを所定のディレクトリにコピーする。
                    $ret = $this->CreateCopyfiles($la_cpf_files);
                    if( $ret == false ){
                        return false;
                    }
                }
            }
        }
        // 前処理でエラーが発生している場合は処理終了
        if($result_code === false){
            return false;
        }

        if ( count($la_cpf_path) > 0 ){
            // ホスト変数配列のホスト)分繰返し
            foreach( $ina_hosts as $host_name){
                // ホスト変数ファイルはホストアドレス方式に関係なく ホスト名にする。
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_file = $hostname;

                //Pioneer用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_org_host_var_file($host_vars_file);
                // ホスト変数定義ファイルにコピー変数を追加
                if($this->CreateHostvarsfile("CPF",$host_name,$file_name,$la_cpf_path,"","","a") === false)
                {
                    return false;
                }
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0055
    // 処理内容
    //   copyファイルの情報をデータベースより取得する。(Pioneer用)
    // 
    // パラメータ
    //   $in_cpf_var_name:      copy変数名
    //   $in_cpf_key:           PKey格納変数
    //   $in_cpf_file_name:     copyファイル格納変数
    // 
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBPioneerCopyMaster($in_cpf_var_name,&$in_cpf_key,&$in_cpf_file_name){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        $sql = "SELECT                         \n" .
               "  CONTENTS_FILE_ID,            \n" .
               "  CONTENTS_FILE                \n" .
               "FROM                           \n" .
               "  B_ANS_CONTENTS_FILE          \n" .
               "WHERE                          \n" .
               "  CONTENTS_FILE_VARS_NAME = :CONTENTS_FILE_VARS_NAME AND \n" .
               "  DISUSE_FLAG            = '0';\n";

        $in_cpf_key       = "";
        $in_cpf_file_name = "";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"CONTENTS_FILE_VARS_NAME=>$in_cpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('CONTENTS_FILE_VARS_NAME'=>$in_cpf_var_name));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"CONTENTS_FILE_VARS_NAME=>$in_cpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

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
    // 処理内容
    //   ITA側テンプレートファイル格納ディレクトリ名を記憶
    // パラメータ
    //   $in_dir:      子PlayBook格納ディレクトリ
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_pns_template_file_Dir($in_indir){
        $this->lv_ita_pns_template_files_Dir = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITA側テンプレートファイル格納ディレクトリ名を取得
    // パラメータ
    //   なし
    //
    // 戻り値
    //   original_hosts_varsディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_pns_template_file_Dir(){
        return($this->lv_ita_pns_template_files_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   ITAが管理しているテンプレートファイルのパスを取得
    // パラメータ
    //   $in_key:        テンプレートファイルのPkey(データベース)
    //   $in_filename:   テンプレートファイル名
    //
    // 戻り値
    //   ホスト変数定義ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_pns_template_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_TEMPLATE_FILE_DIR_MK,
                        $this->getITA_pns_template_file_Dir(),
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のテンプレートファイルパスを取得
    // パラメータ
    //   $in_pkey:     テンプレートファイル Pkey
    //   $in_file:     テンプレートファイル
    //   $in_hostname: ホスト名
    //
    // 戻り値
    //   ホスト変数ファイル内のテンプレートファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_pioneer_template_file_value($in_pkey,$in_file,$in_hostname) {
        $intNumPadding = 10;
        $file = sprintf(self::LC_HOSTVARSFILE_PNS_TEMPLATE_FILE_MK,
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);

        $file = sprintf(self::LC_HOSTVARSFILE_TEMPLATE_FILE_MK,
                        $this->getAnsible_template_files_Dir(),
                        $in_hostname,
                        $file);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   inディレクトリ配下のテンプレートファイルパスを取得
    // パラメータ
    //   $in_pkey:     テンプレートファイル Pkey
    //   $in_file:     テンプレートファイル
    //   $in_hostname: ホスト名
    //
    // 戻り値
    //   ホスト変数ファイル内のテンプレートファイルパス
    ////////////////////////////////////////////////////////////////////////////////
    function getHostvarsfile_pioneer_template_file($in_pkey,$in_file) {
        $intNumPadding = 10;

        $file = sprintf(self::LC_HOSTVARSFILE_TEMPLATE_FILE_MK,
                        $this->getAnsible_template_files_Dir(),
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_file);
        return($file);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0056
    // 処理内容
    //   Pioneer用 対話ファイルよりtemplateモジュールで使用しているテンプレート変数
    //   抜出しホスト変数ファイルに追加する。
    // パラメータ
    //   $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    //
    //   $ina_dialog_files:     対話ファイル配列
    //                          [ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
    //
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //   $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreatePioneertemplatefiles($ina_hosts,$ina_dialog_files,$ina_hostprotcollist,$ina_host_vars) {
        $result_code = true;

        $la_tpf_path  = array();
        $la_tpf_file  = array();
        $Template_Flg = 0;

        // 対話ファイル配列のホスト分繰返し
        foreach( $ina_hosts as $no=>$host_name ) {
            // 対話ファイル配列より該当ホストの対話ファイル配列取得
            $dialog_file_list = $ina_dialog_files[$host_name];
            foreach( $dialog_file_list as $includeno=>$pkeylist ) {
                foreach( $pkeylist as $playbook_pkey=>$playbook ) {
                    // Ansible実行時の対話ファイル名は Pkey(10桁)-対話ファイル名 とする
                    // 対話ファイルのパス取得
                    foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)

                    $file_name = $this->getAnsible_dialog_file($hostname,$playbook_pkey,$playbook);

                    // 対話ファイルの内容を取得 ここまでの過程でファイルの存在は確認
                    $dataString = file_get_contents($file_name);

                    ///////////////////////////////////////////////////////////////////
                    // 対話ファイルでtemplate変数が使用されているか確認
                    ///////////////////////////////////////////////////////////////////
                    SimpleVerSearch(DF_HOST_TPF_HED,$dataString,$la_tpf_vars);

                    ///////////////////////////////////////////////////////////////////
                    // template変数に紐づくファイルの情報を取得
                    ///////////////////////////////////////////////////////////////////
                    $la_tpf_files = array();
                    foreach( $la_tpf_vars as $no => $tpf_var_list ) {
                        foreach( $tpf_var_list as $line_no  => $tpf_var_name ) {
                            $tpf_key = "";
                            $tpf_file_name = "";
                            // template変数名からtemplateファイル名とPkeyを取得する。
                            $ret = $this->getDBPioneerTemplateMaster($tpf_var_name,$tpf_key,$tpf_file_name);
                            if( $ret == false ) {
                                //エラーが発生した場合は処理終了
                                return false;
                            }
                            // template変数名が未登録の場合
                            if( $tpf_key == "" ) {
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55241",
                                                                           array(basename($playbook),
                                                                           $line_no,
                                                                           $tpf_var_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                //テンプレート変数名が未登録
                                $result_code = false;
                                continue;
                            } else {
                                // テンプレートファイル名が未登録の場合
                                if($tpf_file_name == "" ) {
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55273",
                                                                               array(basename($playbook),
                                                                               $line_no,
                                                                               $tpf_var_name));

                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                                    $result_code = false;
                                    continue;
                                }
                            }

                            // templateファイルのpkeyとファイル名を退避
                            // la_tpf_files[pkey] = テンプレートファイル
                            $la_tpf_files[$tpf_key] = $tpf_file_name;

                            // inディレクトリ配下のtemplateファイルバスを取得
                            $tpf_path = $this->getHostvarsfile_pioneer_template_file_value($tpf_key,$tpf_file_name,$hostname);

                            $tpf_path = str_replace($this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ITA'),
                                                    $this->getAnsibleBaseDir('ANSIBLE_SH_PATH_ANS'),
                                                    $tpf_path);

                            // $la_tpf_path[template変数]=inディレクトリ配下のtemplateファイルパス
                            $la_tpf_path[$tpf_var_name] = $tpf_path;

                            $tpf_file = $this->getHostvarsfile_pioneer_template_file($tpf_key,$tpf_file_name);
                            $la_tpf_file[$tpf_var_name] = $tpf_file;
                        }
                    }
                }

                // 前処理でエラーが発生している場合は次のファイルへ
                if ( $result_code === false ) {
                    continue;
                }

                if ( count($la_tpf_files) > 0 ) {
                    // templateファイルを所定のディレクトリにコピーする。
                    $ret = $this->CopyPioneerTemplatefiles($la_tpf_files);
                    if( $ret == false ) {
                        return false;
                    }
                }
            }
        }
        // 前処理でエラーが発生している場合は処理終了
        if ( $result_code === false ) {
            return false;
        }

        if ( count($la_tpf_path) > 0 ) {
            // ホスト変数配列のホスト)分繰返し
            foreach( $ina_hosts as $no=>$host_name ) {
                // ホスト変数ファイルはホストアドレス方式に関係なく ホスト名にする。
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_file = $hostname;

                //Pioneer用のホスト変数定義ファイル名を取得
                $file_name = $this->getAnsible_org_host_var_file($host_vars_file);
                // ホスト変数定義ファイルにテンプレート変数を追加
                if($this->CreateHostvarsfile("TPF",$host_name,$file_name,$la_tpf_path,"","","a") === false) {
                    return false;
                }
                $Template_Flg = 1;
            }
        }

        if ( $Template_Flg == 1 ) {

            $playbookread  = array();
            $playbookwrite = array();

            // playbookの読み込み
            $playbookread = file($this->getAnsible_playbook_file());

            $j = 0;
            // Templateファイルがある場合、追加でplaybookに書き込み
            for ( $i = 0; $i < count($playbookread); $i++ ) {

                // 読み込みデータを書き込みデータに代入
                $playbookwrite[$j] = $playbookread[$i];

                if( strpos($playbookread[$i],'tasks:') !== false ) {
                    // "  tasks:" の後に追記
                    $j = $j + 1;
                    $playbookwrite[$j] = "    - name: include\n";
                    $j = $j + 1;
                    $playbookwrite[$j] = "      include_vars: " . $this->getAnsible_original_hosts_vars_Dir() . "/{{ " . self::LC_ANS_LOGINHOST_VAR_NAME . " }}\n";
                    foreach( $la_tpf_path as $var=>$val ) {
                        $j = $j + 1;
                        $playbookwrite[$j] = "    - name: Templatefile Create " . sprintf("[%s]\n", $var);
                        $j = $j + 1;
                        $playbookwrite[$j] = "      template: src=" . sprintf("%s", $la_tpf_file[$var]) . " dest=" . sprintf("%s\n", $val);
                        $j = $j + 1;
                        $playbookwrite[$j] = "      delegate_to: 127.0.0.1\n";
                    }
                }
                $j = $j + 1;
            }
            $fp = fopen($this->getAnsible_playbook_file(),"w");
            foreach ($playbookwrite as $write) {
                fputs($fp,$write);
            }
            fclose($fp);
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0057
    // 処理内容
    //   テンプレートファイルの情報をデータベースより取得する。
    //
    // パラメータ
    //   $in_tpf_var_name:      テンプレート変数名
    //   $in_tpf_key:           テンプレートKey格納変数
    //   $in_tpf_file_name:     テンプレートファイル格納変数
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBPioneerTemplateMaster($in_tpf_var_name,&$in_tpf_key,&$in_tpf_file_name) {
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        $sql = "SELECT                         \n" .
               "  ANS_TEMPLATE_ID,             \n" .
               "  ANS_TEMPLATE_FILE            \n" .
               "FROM                           \n" .
               "  B_ANS_TEMPLATE_FILE          \n" .
               "WHERE                          \n" .
               "  ANS_TEMPLATE_VARS_NAME = :ANS_TEMPLATE_VARS_NAME AND \n" .
               "  DISUSE_FLAG            = '0';\n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ANS_TEMPLATE_VARS_NAME=>$in_tpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('ANS_TEMPLATE_VARS_NAME'=>$in_tpf_var_name));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"ANS_TEMPLATE_VARS_NAME=>$in_tpf_var_name");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        $ina_child_playbooks = array();
        $row = $objQuery->resultFetch();

        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1) {
            // テンプレートが未登録の場合のエラー処理は呼び側にまかせる。
            unset($objQuery);
            return true;
        }
        $in_tpf_key       = $row["ANS_TEMPLATE_ID"];
        $in_tpf_file_name = $row["ANS_TEMPLATE_FILE"];

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0058
    // 処理内容
    //   テンブレートファイルを所定のディレクトリにコピーする。
    // パラメータ
    //   $ina_template_files:   テンプレートファイル配列
    //                          [Pkey]=>テンプレートファイル
    //                          の配列
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CopyPioneerTemplatefiles($ina_template_files) {

        foreach( $ina_template_files as $pkey=>$template_file ) {

            //テンプレートファイルが存在しているか確認
            $src_file = $this->getITA_pns_template_file($pkey,$template_file);

            if( file_exists($src_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55239",array($pkey,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // Ansible実行時のテンプレートファイル名は Pkey(10桁)-テンプレートファイル名
            $dst_file = $this->getAnsible_template_file($pkey,$template_file);

            if(file_exists($dst_file) === true){
                // 既にコピー済み
                return true;
            }

            //テンプレートファイルを所定ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITAANSIBLEH-ERR-55240",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }
}

/**
 * B_ANS_LRL_VARS_GRP_MEMBER 格納用コンテナ
 * （Composite パターン）
 * 上位階層から格納すること
 */
class AnsLrlVarsGrpMemberContainer {

    private $parentVarsKeyId;
    private $rowObject;
    private $isBelowTheRoot = false;

    private $children;

    function __construct($rowObject) {

        $this->parentVarsKeyId = $rowObject['PARENT_VARS_KEY_ID'];
        $this->rowObject = $rowObject;

        if($this->rowObject['ARRAY_NEST_LEVEL'] == '1') {
            $this->isBelowTheRoot = true;
        }

        $this->children = array();
    }

    function getParentVarsKeyId() {
        return $this->parentVarsKeyId;
    }

    function isBelowTheRoot() {
        return $this->isBelowTheRoot;
    }

    function setDescendant(AnsLrlVarsGrpMemberContainer $descendant, $nest_level = 0) {

        if($descendant->isBelowTheRoot) {
            $this->children[] = $descendant;
            return true;
        }

        // TODO 階層チェック入れるか

        if($this->rowObject['VARS_KEY_ID'] == $descendant->getParentVarsKeyId()) {
            $this->children[] = $descendant;
            return true;
        } else {
            foreach($this->children as $child) {
                $result = $child->setDescendant($descendant, $nest_level + 1);
                if($result) {
                    return $result; // true
                }
            }
        }

        return false;
    }

    function getConstructionArray() {

        $childrenConstructArray = array();

        foreach($this->children as $child) {
            $childrenConstruct = $child->getConstructionArray();
            $childrenConstructArray = $childrenConstructArray + $childrenConstruct;
        }

        if(is_null($this->parentVarsKeyId)) { // topレベルのみそのまま返す
            return $childrenConstructArray;
        } else { // 子階層はすべて自身のIDを付けてラップして返す
            return array($this->rowObject['ARRAY_MEMBER_ID'] => $childrenConstructArray);
        }
    }
}
?>
