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
//    DSCの実行に必要な情報をデータベースから取得し
//    DSC実行ディレクトリ/ファイルを生成する。
//
// F0001 CreateDscWorkingDir               DSC用作業ディレクトリを作成する。
// F0002 CreateDscWorkingFiles             DSC用各作業ファイルを作成する。
// F0004 CreateHostvarsfiles               ホスト変数ファイルを作成する。
// F0005 CreateHostvarsfile                ホスト変数定義ファイル(1ホスト)を作成する。
// F0007 CreateDscResourcefiles            DSC用 リソースファイルを作成する。
// F0008 CreateChildResourcefiles          リソース(DSC版)を作成する。
// F0011 CheckDscResourcefiles             リソースで使用している変数がホスト変数に登録されているかチェックする。 CheckChildResourceFormatを呼び出す
// F0012-1 CheckDscConfigfiles             Configuファイルを作成しそのフォーマットをチェックする
// F0013 CheckChildResourceFormat          リソースファイルのフォーマットをチェックする。
// F0015 getDBHostList                     DSCで実行するHOST(作業対象ノード)をデータベースより取得する。
// F0016 getDBVarList                      DSCで実行する変数をデータベースより取得する。
// F0017 getDBDscResourceList              DSCで実行するリソースファイルをデータベースより取得する。
// F0019 addSystemvars                     システム予約変数を設定する
// F0027 getDscWorkingDirectories          DSC処理で出力される各ファイル用ディレクトリを作成する。
// F0034 getDBChildVarsList                メンバー変数マスタの情報を取得 chkChildVarsListから呼ばれる
// F0035 chkChildVars                      代入値管理のメンバー変数の入力情報を判定 getDBVarListから呼ばれる 
// F0036 chkChildVarsList                  代入値管理の配列変数のメンバー変数とメンバー変数マスタとの照合 getDBVarListからのみ呼ばれる
//
/////////////////////////////////////////////////////////////////////////////////////////

require_once ($root_dir_path . "/libs/backyardlibs/dsc_driver/WrappedStringReplaceAdmin.php");
require_once ($root_dir_path . '/libs/backyardlibs/dsc_driver/ky_dsc_common_setenv.php' );

class CreateDscExecFiles {
    // Dsc 作業ディレクトリ名
    const LC_DSC_IN_DIR                      = "in";
    const LC_DSC_OUT_DIR                     = "out";
    const LC_DSC_DIR                         = "dsc";
    const LC_DSC_HOST_VARS_DIR               = "host_vars";
    const LC_DSC_PW_DIR                      = "pw";

    // 予約変数---
    // リソースに埋め込まれるリモート接続コマンド用変数の名前
    const LC_DSC_PROTOCOL_VAR_NAME           = "__loginprotocol__";
    // リソースに埋め込まれるリモートログインのユーザー用変数の名前
    const LC_DSC_USERNAME_VAR_NAME           = "__loginuser__";
    // リソースに埋め込まれるリモートログインのパスワード用変数の名前
    const LC_DSC_PASSWD_VAR_NAME             = "__loginpassword__";
    // リソースに埋め込まれるホスト名用変数の名前
    const LC_DSC_LOGINHOST_VAR_NAME          = "__loginhostname__";
    // 機器一覧のログイン・パスワード未登録時の内部変数値
    const LC_DSC_UNDEFINE_NAME               = "__undefinesymbol__";
    //---予約変数

    // ユーザー公開用 DSC作業用データリレイストレージパス 変数の名前
    const LC_DSC_OUTDIR_VAR_NAME             = "__workflowdir__";
    // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
    const LC_SYMPHONY_DIR_VAR_NAME           = "__symphony_workflowdir__";

    // ユーザー公開用データリレイストレージパス 変数の名前
    const LC_DSC_OUTDIR_DIR                  = "user_files";

    // DSC作業ファイル名
    const LC_DSC_HOSTS_FILE                  = "hosts";
    const LC_DSC_RESOURCE_FILE               = "config.ps1";
    // REST API用ターゲットデバイスファイル名追加
	const LC_DSC_DEV_FILE                    = "device.txt";         // 作業対象ノード接続情報ファイル（並列処理）

    const LC_DSC_HOST_VAR_FILE_MK            = "%s/%s";              // ホスト定義ファイルパス用書式定数
    const LC_DSC_CHILD_RESOURCE_FILE_MK      = "%s/%s-%s";           // リソースファイル用書式定数 ?

    const LC_DSC_COPY_ATHOER_FILE_MK         = "%s/%s-%s-%s";        // PowerShell、Param、Import、コンフィグデータ、コンパイルオプションファイル用書式定数

    //ITA リソースファイル格納ディレクトリ
    const LC_ITA_RESOURCES_DIR_MK      = "%s/%s/%s";           // ITA側リソースファイルアップロードディレクトリパス用書式定数

    //ITA リソース以外のファイル格納ディレクトリ
    const LC_ITA_OTHER_FILES_DIR_MK    = "%s/%s/%s";           // ITA側PowerShell、Param、Import、コンフィグデータ、コンパイルオプションファイル
                                                               // のアップロードディレクトリパス用書式定数

    //ITA リソースファイルパス
    const LC_RESOURCE_RESOURCE_CHILD_FILE_MK =  "%s/%s-%s";    // ITA側リソースファイルパス用書式定数 ?

    // WINRM接続ポート デフォルト値
    const LC_WINRM_PORT                    = 5985;

    // 機器一覧 パスワード管理フラグ(LOGIN_PW_HOLD_FLAG)
    const LC_LOGIN_PW_HOLD_FLAG_OFF           = '0';         // パスワード管理なし
    const LC_LOGIN_PW_HOLD_FLAG_ON            = '1';         // パスワード管理あり
    const LC_LOGIN_PW_HOLD_FLAG_DEF           = '0';         // デフォルト値 パスワード管理なし
    // 機器一覧 Dsc認証方式(LOGIN_AUTH_TYPE)
    const LC_LOGIN_AUTH_TYPE_KEY              = '1';         // 鍵認証
    const LC_LOGIN_AUTH_TYPE_PW               = '2';         // パスワード認証
    const LC_LOGIN_AUTH_TYPE_DEF              = '1';         // デフォルト値 鍵認証
    const LC_NODE_NAME                        = "__ NODE __";     // NODE名称

    // ローカル変数定義
    private $lv_Dsc_driver_id;                 // DSCドライバ区分

    private $lv_hostaddress_type;               // ホストアドレス型式 null or 1:IP方式  2:ホスト名方式

    // DSC用各ディレクトリ変数
    private $lv_Dsc_base_Dir;                  // DSC データリレイストレージ（ITA側）
    private $lv_Dsc_in_Dir;                    // DSC作業用ディレクトリ inディレクトリ （ITA側ファイル出力）
    private $lv_Dsc_out_Dir;                   // DSC作業用ディレクトリ outディレクトリ （RESTAPI側ファイル出力)
    private $lv_Dsc_child_resources_Dir;       // child_resourcesディレクトリ ？
    private $lv_Dsc_host_vars_Dir;             // host_varsディレクトリ

    // ユーザー公開用データリレイストレージパス
    private $lv_user_out_Dir;
    // ユーザー公開用symphonyインスタンスストレージパス
    private $lv_symphony_instance_Dir;

    private $lv_Resource_child_resources_Dir;  // 

    private $lv_winrm_id;                      // 作業パターンの接続先がwindowsかを判別する項目 1:windows

    //ITA用各ディレクトリ変数
    private $lv_ita_resources_Dir;             // ITA リソースファイル格納ディレクトリ

    // 2013.03.22 Add
    private $lv_ita_powershell_file_Dir;       // DSC用 Powershell素材ファイル格納先ディレクトリ
    private $lv_ita_param_file_Dir;            // DSC用 Param素材ファイル格納先ディレクトリ(隠し)
    private $lv_ita_import_file_Dir;           // DSC用 Import素材ファイル格納先ディレクトリ
    private $lv_ita_configdata_file_Dir;       // DSC用 コンフィグデータ素材ファイル格納先ディレクトリ
    private $lv_ita_cmpoption_file_Dir;        // DSC用 コンパイルオプション素材ファイル格納先ディレクトリ(隠し)
    private $lv_ita_certificate_file_Dir;      // DSC用 認証キーファイル格納先ディレクトリ
    // 2013.03.22 Add

    private $lv_dsc_storage_path;              // DSCサーバ データストレージパス(DB情報）
    private $lv_dsc_in_dir_windows;            // DSCサーバ側から見た作業用ディレクトリ inディレクトリ

    // テーブル名定義
    private $lv_dsc_vars_masterDB;             // 変数管理テーブルテーブル名
    private $lv_dsc_vars_assignDB;             // 代入値管理テーブルテーブル名
    private $lv_dsc_pattern_vars_linkDB;       // 作業パターン変数紐付管理テーブルテーブル名
    private $lv_dsc_pho_linkDB;                // 作業対象ホストテーブル テーブル名
    private $lv_dsc_master_fileDB;             // 素材管理テーブル テーブル名
    private $lv_dsc_master_file_pkeyITEM;      // 素材管理テーブル 素材ID(pkey)項目名
    private $lv_dsc_master_file_nameITEM;      // 素材管理テーブル 素材ファイル項目名

    private $lv_dsc_pattern_linkDB;            // 作業パターン詳細 テーブル名
    private $lv_dsc_varsDB;                    // メンバー変数管理  テーブル名

    private $lv_dsc_powershell_fileDB;         // Powershell素材ファイル テーブル名(隠し)
    private $lv_dsc_param_fileDB;              // Param 素材ファイル テーブル名(隠し)
    private $lv_dsc_import_fileDB;             // Import素材ファイル テーブル名
    private $lv_dsc_configdata_fileDB;         // コンフィグデータ素材ファイル テーブル名
    private $lv_dsc_cmpoption_fileDB;          // コンパイルオプション素材ファイル テーブル名(隠し)
    private $lv_credentialDB;                  // 資格情報 テーブル名

    private $lv_objMTS;
    private $lv_objDBCA;

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    コンストラクタ
    //  パラメータ
    //    $in_driver_id:     ドライバ区分
    //                       DSC: DF_DSC_DRIVER_ID
    //    $in_dsc_ita_base_dir:  ansible作業用 NFSベースディレクトリ (ITA側)
    //    $in_dsc_ans_base_dir:  ansible作業用 NFSベースディレクトリ (Ansible側)
    //    $in_symphony_ans_base_dir: symphony NFSベースディレクトリ (Ansible側)
    //    $in_ita_resource_dir:  ITA側で管理（アップロードされた）しているリソースファイル格納ディレクトリ
    //    $in_dsc_vars_masterDB:
    //                       変数>管理テーブル テーブル名
    //    $in_dsc_vars_assignDB:
    //                       代入値管理テーブル テーブル名
    //    $in_dsc_pattern_vars_linkDB:
    //                       代入>変数名管理テーブル テーブル名
    //    $in_dsc_pho_linkDB:
    //                       作業対象ホストテーブル テーブル名
    //    $in_dsc_master_fileDB:
    //                       素材管理テーブル テーブル名
    //    $in_dsc_master_file_pkeyIIEM:
    //                       素材管理テーブル 素材ID(pkey)項目名
    //    $in_dsc_master_file_nameITEM:
    //                       素材管理テーブル 素材ファイル項目名
    //    $in_dsc_pattern_linkDB:
    //                       作業パターン詳細 テーブル名
    //    $in_dsc_powershell_file_dir:
    //                       Powershell素材ファイル格納先ディレクトリ
    //    $in_dsc_param_file_dir:
    //                       Param素材ファイル格納先ディレクトリ
    //    $in_dsc_import_file_dir:
    //                       Import素材ファイル格納先ディレクトリ
    //    $in_dsc_configdata_file_dir:
    //                       コンフィグデータ素材ファイル格納先ディレクトリ
    //    $in_dsc_cmpoption_file_dir:
    //                       コンパイルオプション素材ファイル格納先ディレクトリ
    //    &$in_objMTS:       メッセージ定義クラス変数
    //    &$in_objDBCA:      データベースアクセスクラス変数
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function __construct($in_driver_id,
                         $in_dsc_ita_base_dir,
                         $in_dsc_dsc_base_dir,
                         $in_symphony_dsc_base_dir,
                         $in_ita_resource_dir,
                         $in_dsc_vars_masterDB,
                         $in_dsc_vars_assignDB,
                         $in_dsc_pattern_vars_linkDB,
                         $in_dsc_pho_linkDB,
                         $in_dsc_master_fileDB,
                         $in_dsc_master_file_pkeyITEM,
                         $in_dsc_master_file_nameITEM,
                         $in_ita_powershell_file_dir,
                         $in_ita_param_file_dir,
                         $in_ita_import_file_dir,
                         $in_ita_configdata_file_dir,
                         $in_ita_cmpoption_file_dir,
                         $in_ita_certificate_file_dir,
                         &$in_objMTS,
                         &$in_objDBCA){
        global $root_dir_path;

        // DSCドライバ区分設定
        $this->setDscDriverID($in_driver_id);


        // DSC用ベースディレクトリ(データリレイストレージ)

        //ansible用ベースディレクトリ
        $this->setDscBaseDir('DSC_SH_PATH_ITA',$in_dsc_ita_base_dir);
        $this->setDscBaseDir('DSC_SH_PATH_DSC',$in_dsc_dsc_base_dir);
        $this->setDscBaseDir('SYMPHONY_SH_PATH_DSC',$in_symphony_dsc_base_dir);

        // リソースファイル(Config素材ファイル)格納ディレクトリ
        $this->setITA_resource_Dir($in_ita_resource_dir);
        // 変数管理テーブルテーブル名
        $this->lv_dsc_vars_masterDB = $in_dsc_vars_masterDB;
        // 代入値管理テーブルテーブル名
        $this->lv_dsc_vars_assignDB = $in_dsc_vars_assignDB;
        // 作業パターン変数紐付管理テーブルテーブル名
        $this->lv_dsc_pattern_vars_linkDB = $in_dsc_pattern_vars_linkDB;
        // 作業対象ホストテーブル テーブル名
        $this->lv_dsc_pho_linkDB = $in_dsc_pho_linkDB;
        // 素材管理テーブル テーブル名
        $this->lv_dsc_master_fileDB = $in_dsc_master_fileDB;
        // 素材管理テーブル 素材ID(pkey)項目名
        $this->lv_dsc_master_file_pkeyITEM = $in_dsc_master_file_pkeyITEM;
        // 素材管理テーブル 素材ファイル項目名
        $this->lv_dsc_master_file_nameITEM = $in_dsc_master_file_nameITEM;
        
        $this->lv_dsc_varsDB      = "B_DSC_VARS_ASSIGN";

        // Powershell素材ファイル格納先ディレクトリ
        $this->lv_ita_powershell_file_Dir = $in_ita_powershell_file_dir;
        // Param素材ファイル格納先ディレクトリ
        $this->lv_ita_param_file_Dir      = $in_ita_param_file_dir;
        // Import素材ファイル格納先ディレクトリ
        $this->lv_ita_import_file_Dir     = $in_ita_import_file_dir;
        // コンフィグデータ素材ファイル格納先ディレクトリ
        $this->lv_ita_configdata_file_Dir = $in_ita_configdata_file_dir;
        // コンパイルオプション素材ファイル格納先ディレクトリ
        $this->lv_ita_cmpoption_file_Dir  = $in_ita_cmpoption_file_dir;
        // 認証キーファイル格納先ディレクトリ
        $this->lv_ita_certificate_file_Dir  = $in_ita_certificate_file_dir;

        // Powershell素材ファイル テーブル名
        $this->lv_dsc_powershell_fileDB = "B_DSC_POWERSHELL_FILE";
        // Param素材ファイル テーブル名
        $this->lv_dsc_param_fileDB      = "B_DSC_PARAM_FILE";
        // Import素材ファイル テーブル名
        $this->lv_dsc_import_fileDB     = "B_DSC_IMPORT_FILE";
        // コンフィグデータ素材ファイル テーブル名
        $this->lv_dsc_configdata_fileDB = "B_DSC_CONFIGDATA_FILE";
        // コンパイルオプション素材ファイル テーブル名
        $this->lv_dsc_cmpoption_fileDB  = "B_DSC_CMPOPTION_FILE";
        // 資格情報 テーブル名
        $this->lv_credentialDB          = "B_DSC_CREDENTIAL";

        //outディレクトリ
        $lv_Dsc_out_Dir = "";

        $this->lv_objMTS  = $in_objMTS;
        $this->lv_objDBCA = $in_objDBCA;

    }

    ////////////////////////////////////////////////////////////////////////////////
    //## F0001
    //
    //  処理内容
    //    DSC用作業ディレクトリを作成する。
    //    ディレクトリ階層
    //    /ベースディレクトリ/ドライバ名/オケストレータID/作業実行番号/in
    //                                              /out
    //                                             /tmp
    //  パラメータ
    //    $in_oct_id              オケストレータID
    //                             dsc        : ns
    //
    //    $in_execno              作業実行番号
    //    $in_hostaddress_type    ホストアドレス方式
    //                           null or 1:IP方式  2:ホスト名方式
    //    $in_winrm_id            対象ホストがwindowsかを判別
    //                           1: windows 他:windows以外
    //    $in_symphony_instance_no:  symphonyから起動された場合のsymphonyインスタンスID
    //                               作業実行の場合は空白
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscWorkingDir($in_oct_id,
                                     $in_execno,
                                     $in_statusid,
                                     $in_hostaddress_type,
                                     $in_winrm_id,
                                     &$aryDscWorkingDir,
                                     $in_symphony_instance_no
                                     ){
        $Contents = array();

        // null or 1:IP方式  2:ホスト名方式
        $this->lv_hostaddress_type = $in_hostaddress_type;

        // 1: windows 他:windows以外
        $this->lv_winrm_id  = $in_winrm_id;

        //ドライバ区分ディレクトリ作成
        $aryRetDscWorkingDir = $this->getDscWorkingDirectories($in_oct_id,$in_execno);
        $aryDscWorkingDir = $aryRetDscWorkingDir;

        if( $aryRetDscWorkingDir === false ){
            return false;
        }

        $c_dir = $aryRetDscWorkingDir[0];
        if( !is_dir( $c_dir ) ){
            //ドライバ区分ディレクトリが存在している場合はなにもしない
            if( !mkdir( $c_dir, 0777 ) ){
                // "ディレクトリの作成に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                // "ディレクトリのパーミッション設定に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }

        //オーケストラ区分ディレクトリ作成
        $c_dir = $aryRetDscWorkingDir[1];

        if( !is_dir( $c_dir ) ){
            //オーケストラ区分ディレクトリが存在している場合はなにもしない
            if( !mkdir( $c_dir, 0777 ) ){
                // "ディレクトリの作成に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                // "ディレクトリのパーミッション設定に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        // 作業実行番号用ディレクトリ作成----
        $c_dir = $aryRetDscWorkingDir[2];
        if( is_dir( $c_dir ) ){
            if( $in_statusid == "2" ){
            	return false;                              // 何らかの理由により前回準備中("2")で終了した
            }
        }
        // ----作業実行ディレクトリ作成
    	system('/bin/rm -rf ' . $c_dir . ' >/dev/null 2>&1');

        if( is_dir( $c_dir ) ){
            // 該当の作業実行番号のディレクトリが既に存在している。(作業No:{} ディレクトリ:{})
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55238",array($in_execno,$c_dir));
            // 作業実行番号用ディレクトリが存在している場合はエラー
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        else{
            if( !mkdir( $c_dir, 0777 ) ){
                // "ディレクトリの作成に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( !chmod( $c_dir, 0777 ) ){
                // "ディレクトリのパーミッション設定に失敗。(｛｝)"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }

        // outディレクトリ作成
        $c_outdir = $aryRetDscWorkingDir[4];
        if( !mkdir( $c_outdir, 0777 ) ){
            // "ディレクトリの作成に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_outdir, 0777 ) ){
            // "ディレクトリのパーミッション設定に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        // outディレクトリパスを設定
        $this->setDsc_out_Dir($c_outdir);
        // エラーメッセージを残す為にoutディレクトリの作成を先にする

        // ユーザー公開用データリレイストレージパス
        $user_out_Dir = $c_outdir . "/" . self::LC_DSC_OUTDIR_DIR;

        if( !mkdir( $user_out_Dir , 0777 ) ){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $user_out_Dir , 0777 ) ){
            // "ディレクトリのパーミッション設定に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // ホスト変数定義ファイルに記載するパスなのでDSC側のストレージパスに変更
        $make_dir = str_replace($this->getDSCBaseDir('DSC_SH_PATH_ITA'),
                                $this->getDSCBaseDir('DSC_SH_PATH_DSC'),
                                $user_out_Dir);
        $this->lv_user_out_Dir = str_replace("/","\\",$make_dir);

        // symphonyからの起動か判定
        if(strlen($in_symphony_instance_no) != 0) {
            // ユーザー公開用symphonyインスタンス作業用 データリレイストレージパス
            $make_dir = $this->getDSCBaseDir('SYMPHONY_SH_PATH_DSC') . "/" . sprintf("%010s",$in_symphony_instance_no);
        }
        else
        {
            $make_dir = $this->lv_user_out_Dir;
        }
        $this->lv_symphony_instance_Dir = str_replace("/","\\",$make_dir);

        //inディレクトリ作成
        $c_indir = $aryRetDscWorkingDir[3];

        if( !mkdir( $c_indir, 0777 ) ){
            // "ディレクトリの作成に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_indir, 0777 ) ){
            // "ディレクトリのパーミッション設定に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // inディレクトリパスを設定 データリレイストレージ in
        $this->setDsc_in_Dir($c_indir);

        // host_varsディレクトリ作成
        $c_dirwk = $c_indir . "/" . self::LC_DSC_HOST_VARS_DIR;
        if( !mkdir( $c_dirwk, 0777 ) ){
            // "ディレクトリの作成に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_dirwk, 0777 ) ){
            // "ディレクトリのパーミッション設定に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        // host_varsディレクトリパスを設定
        $this->setDsc_host_vars_Dir($c_dirwk);

        $this->setDsc_in_Dir_windows($aryRetDscWorkingDir[6]);

        // host_varsディレクトリ配下にpwディレクトリ作成
        $c_dirpw = $c_dirwk . "/" . self::LC_DSC_PW_DIR;
        if( !mkdir( $c_dirpw, 0777 ) ){
            // "ディレクトリの作成に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        if( !chmod( $c_dirpw, 0777 ) ){
            // "ディレクトリのパーミッション設定に失敗。(｛｝)"
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55203",array(__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    //  処理内容
    //    DSC用各作業ファイルを作成する。
    //
    //  パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //                           [管理システム項番]=[ホスト名(IP)]
    //
    //    $ina_host_vars:        ホスト変数配列
    //                           [ホスト名(IP)][ 変数名 ]=>具体値
    //
    //    $ina_resources:  リソースファイル配列
    //                           [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //
    //   $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //   #1073 下記を追加--------------------------------------------------------------------
    //   既存のデータが重なるが、今後の開発はこの変数を使用する。
    //   $ina_hostinfolist:     機器一覧ホスト情報配列
    //                          [ホスト名(IP)]=HOSTNAME=>''             ホスト名(IP)
    //                                         PROTOCOL_ID=>''          接続プロトコル
    //                                         LOGIN_USER=>''           ログインユーザー名
    //                                         LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
    //                                                                  1:管理(●)   0:未管理
    //                                         LOGIN_PW=>''             パスワード
    //                                                                  パスワード管理が1の場合のみ有効
    //                                         LOGIN_AUTH_TYPE=>''      DSC認証方式
    //                                                                  2:パスワード認証 1:鍵認証
    //                                         WINRM_PORT=>''           WinRM接続プロトコル
    //                                         OS_TYPE_ID=>''           OS種別
    //
    //   $ina_host_child_vars:  配列変数一覧返却配列(変数一覧に配列変数含む)
    //                          [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //   $ina_DB_child_vars_master:
    //                          メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                          [変数名][メンバー変数名]=0
    //   $ina_DscContents:      Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscWorkingFiles($ina_hosts,
                                       $ina_host_vars,
                                       $ina_resources,
                                       $ina_hostprotcollist,
                                       $ina_hostinfolist,
                                       $ina_host_child_vars,
                                       $ina_DB_child_vars_master,
                                       $ina_DscContents){
        $aryDsc_copy_files = array();
    
        //////////////////////////////////////
        // ホスト変数定義ファイル作成               //
        //////////////////////////////////////
        // #0001 host_varsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を渡す。
        if ( $this->CreateHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                        $ina_host_child_vars,$ina_DB_child_vars_master) === false){
            return false;
        }
        //ドライバ区分を判定
        switch($this->getDscDriverID()){
        case DF_DSC_DRIVER_ID:

            //////////////////////////////////////
            // DSC リソースファイル配置               //
            //////////////////////////////////////
            if ( $this->CreateDscResourcefiles($ina_resources, $aryDsc_copy_files) === false){
                return false;
            }

            /////////////////////////////////////////////////
            // DSCリソースファイル以外のファイルをDSC用ディレクトリに配置。
            // PowerShell、Param、Import、コンフィグデータ、コンパイルオプションファイルを配置
            /////////////////////////////////////////////////
            if ( $this->CreateDscOtherfiles($ina_DscContents,$ina_hosts,$ina_hostinfolist) === false){
                return false;
            }

            /////////////////////////////////////////////////
            // リソースファイルのフォーマットと変数定義チェック           //
            // 
            /////////////////////////////////////////////////
            if ( $this->CheckDscResourcefiles($ina_hosts,$ina_host_vars,$ina_resources) === false){
                return false;
            }

            /////////////////////////////////////////////////
            // ホスト数分リソースファイルを作成            //
            /////////////////////////////////////////////////
            if ( $this->CreateDscResourcefilesHostnum($ina_hosts,$ina_host_vars,$ina_resources) === false){
                return false;
            }

            /////////////////////////////////////////////////
            // リソースファイルにある資格情報変数を検索 //
            /////////////////////////////////////////////////
            if ( $this->CreateDscCredential($ina_hosts,$ina_hostinfolist,$ina_resources) === false){
                return false;
            }

            $in_dir=$this->getDsc_in_Dir();              // DSC側inディレクトリ取得
            $config_pass= "";

            $exeflg = "";
            foreach( $ina_resources as $config_name ){
                $exeflg = "1";
            }
            if ( $exeflg === "1"){
                $conkey = key($config_name);                          // config名取得
                // リソースファイルパスを取得
                //$config_pass = $in_dir . '/' . $config_name[$conkey]; // configパス作成
                $config_pass = $config_name[$conkey]; // configパス作成
            }

            /////////////////////////////////////////////////
            // リソースファイル、他ファイルをコンフィグファイルに記述
            /////////////////////////////////////////////////
            if ( $this->CreateDscConfigFileMerge($ina_hosts, $ina_host_vars, $ina_hostinfolist, $ina_resources, $ina_DscContents, $config_pass) === false){
                return false;
            }

            /////////////////////////////////////////////////
            // テンポラリファイルを削除
            /////////////////////////////////////////////////
            if ( $this->DelteTemporaryFile($ina_hosts, $ina_hostinfolist, $ina_resources, $ina_DscContents) === false) {
                return false;
            }

            break;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    //  処理内容
    //    ホスト変数ファイルを作成する。
    //
    //  パラメータ
    //    $ina_host_vars:        ホスト変数配列
    //                           [ipaddress][ 変数名 ]=>具体値
    //    $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //                           [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //    $ina_host_child_vars:  配列変数一覧返却配列(変数一覧に配列変数含む)
    //                           [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //    $ina_DB_child_vars_master:
    //                           メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                           [変数名][メンバー変数名]=0
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // hostsファイルにホスト名を設定可能するためにホスト毎プロトコル一覧を貰う  確認
    function CreateHostvarsfiles($ina_host_vars,$ina_hostprotcollist,
                                 $ina_host_child_vars,$ina_DB_child_vars_master){
        // ホスト変数配列よりホスト名(IP)を取得
        $host_list = array_keys($ina_host_vars);

        // ホスト変数配列のホスト)分繰返し
        foreach( $host_list as $host_name){
            // ホストアドレス方式がホスト名方式の場合はhost_varsをホスト名する。
            if($this->lv_hostaddress_type == 2){
                foreach($ina_hostprotcollist[$host_name] as $hostname=>$prolist)
                $host_vars_file = $hostname;
            }
            else{
                $host_vars_file = $host_name;
            }
            //ドライバ区分を判定しホスト変数定義ファイル名を取得
            switch($this->getDscDriverID()){
            case DF_DSC_DRIVER_ID:
                $file_name = $this->getDsc_host_var_file($host_vars_file);
                break;
            default:
                // 内部処理異常(FILE:{} LINE:{})
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55200",array(basename(__FILE__) . "-" . __LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            //該当ホストの変数配列を取得
             $vars_list = $ina_host_vars[$host_name];

            $chl_vars_list = array();
            switch($this->getDscDriverID()){
            case DF_DSC_DRIVER_ID:
                //該当ホストの配列変数を取得
                if(@count($ina_host_child_vars[$host_name]) != 0){
                    $chl_vars_list = $ina_host_child_vars[$host_name];
                }
                break;
            }

            // ホスト変数定義ファイル作成
            if($this->CreateHostvarsfile($file_name,$vars_list,
                                         $chl_vars_list,$ina_DB_child_vars_master) === false){
                return false;
            }
        }
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // F0005
    //  処理内容
    //    ホスト変数定義ファイル(1ホスト)を作成する。
    //  パラメータ
    //    $in_file_name:     ホスト変数定義ファイル名
    //    $ina_var_list:     ホスト変数配列
    //                       legacyの場合
    //                       [ 変数名 ]=>具体値
    //                       pioneerの場合
    //                       [対話ファイル変数名]=対話ファイル名
    //    $ina_host_child_vars:
    //                       配列変数一覧返却配列(変数一覧に配列変数含む)
    //                       [ 変数名 ][列順序][メンバー変数]=[具体値]
    //                       空の場合がある
    //    $ina_DB_child_vars_master:
    //                       メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                       [変数名][メンバー変数名]=0
    //    $in_mode:          書込モード
    //                       "w":上書   デフォルト
    //                       "a":追加
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    // ファイル作成時の書込モード(上書/追加)を追加
    function CreateHostvarsfile($in_file_name,
                                $ina_var_list,
                              $ina_child_vars,
                    $ina_DB_child_vars_master,
                                $in_mode="w" ){
        switch($this->getDscDriverID()){
        case DF_DSC_DRIVER_ID:
            $var_str = "";
            foreach( $ina_var_list as $var=>$val ){
                //ホスト変数ファイルのレコード生成
                //変数名: 具体値
                $var_str = $var_str . sprintf("%s: %s\n",$var,$val);
            }
            break;
        }

        if ( $var_str != "" ){
            $fd = @fopen($in_file_name, $in_mode);

            if($fd === null){
                // ホスト変数定義ファイルの作成に失敗しました。(ファイル名:{})
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55206",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            if( @fputs($fd, $var_str) === false ){
                // "ホスト変数定義ファイルの書込みに失敗しました。(ファイル名:{})"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
            if( @fclose($fd) === false ){
                // "ホスト変数定義ファイルの書込みに失敗しました。(ファイル名:{})"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55207",array(__LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    //  処理内容
    //    DSC用 リソースファイルを作成する。
    //    内部でCreateChildResourcefiles(リソースファイルITA・DSC転送関数)を呼び出す
    //
    //  パラメータ
    //    $ina_resources:  リソースファイル配列
    //                          ina_resources[INCLUDE順序][素材管理Pkey]=>素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscResourcefiles($ina_resources, &$ina_copy_files){
        $Dsc_in_org_Dir = "";
        //////////////////////////////////////
        // リソースファイル作成                    //
        //////////////////////////////////////

        //if($this->CreateChildResourcefiles($ina_resources,$Dsc_in_org_Dir) === false)
        if($this->CreateChildResourcefiles($ina_resources,$ina_copy_files) === false){
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    //  処理内容
    //    リソースファイル(DSC版)を作成する。（実処理）
    //    詳細：アップロードフォルダにアップロードされているリソースファイル(Config素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_resources:  リソースファイル配列
    //                        [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //                        の配列
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CreateChildResourcefiles($ina_resources,&$Dsc_in_org_Dir){
        foreach( $ina_resources as $resource_list ){
            foreach( $resource_list as $pkey=>$resource ){
                // アップロードされているリソースファイルのパスを取得
                $src_file = $this->getITA_resource_file($pkey,$resource);

                // リソースファイルが存在しているか確認
                if( file_exists($src_file) === false ){
                    // "システムで管理しているConfigファイルが存在しません。(KEY_ID:{}  ファイル名:{})"; TODO message修正 Configファイル⇒リソースファイルに訂正 TS
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55210",array($pkey,basename($src_file)));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
                //-------------------------------------------------------------------
                // DSC実行時のConfigファイル名を作成
                //-------------------------------------------------------------------
                $in_dir=$this->getDsc_in_Dir(); // ドライバ区分+オケストレータID付き+作業実行番号付き+inフォルダ名
                $exeflg = "";
                foreach( $ina_resources as $config_name ){
                    $exeflg = "1";
                }
                if ( $exeflg === "1"){
                    $lv_dsc_config = $config_name;                           // 0525修正
                	$conkey = key($config_name);                             // configファイルフルパス取得
                	$config_pass = $in_dir . '/' . $config_name[$conkey];    // configファイルフルパス取得

                }
                //-------------------------------------------------------------------
                // DSC実行時のConfigファイル名は ドライバ区分+オケストレータID付き+PKEY10桁+inフォルダ名+リソースファイル名とする。
                //-------------------------------------------------------------------
                $dst_file = $this->getDsc_resource_filename($pkey,$resource);    // 関数名変更 getDsc_child_playbiook_file⇒getDsc_resource_file

                $Dsc_in_org_Dir = $dst_file;

                // リソースファイルをDSC用ディレクトリにコピーする。
                if( copy($src_file,$dst_file) === false ){
                    // "Configファイルのコピーに失敗しました。(ファイル名:{})"  TODO （要メッセージ修正）Configファイル⇒リソースファイルのデータリレイストレージへのコピーに失敗しました。
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55211",array(basename($src_file)));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0011
    //  処理内容
    //    DSC用 リソースファイルのフォーマットをチェックする
    //    リソースファイルで使用している変数がホスト変数に登録されているかチェックする。
    //
    //  パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //                          [管理システム項番]=ホスト名(IP)
    //
    //    $ina_host_vars:        ホスト変数配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //
    //    $ina_resources:  リソースファイル配列  // 名前変更 child削除 10/16 TS
    //                          [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function CheckDscResourcefiles($ina_hosts,$ina_host_vars,$ina_resources){
        $result_code = true;

        foreach( $ina_resources as $no=>$resource_list ){
            foreach( $resource_list as $resourcepkey=>$resource ){
                // リソースファイルのバス（データリレイストレージ）を取得
                $file_name = $this->getDsc_resource_filename($resourcepkey,$resource);

                ///////////////////////////////////////////////////////////////////
                // リソースファイルフォーマットチェックを行う。
                ///////////////////////////////////////////////////////////////////
                if($this->CheckChildResourceFormat($file_name) === false){
                    // フォーマットチェックでエラーが発生した以下の処理はしない
                    $result_code = false;
                    continue;
                }
                ///////////////////////////////////////////////////////////////////
                // リソースファイルで使用している変数がホストの変数に登録されているか判定
                ///////////////////////////////////////////////////////////////////

                // ローカル変数のリスト作成
                $local_vars[] = array();
                $local_vars[] = self::LC_DSC_PROTOCOL_VAR_NAME;
                $local_vars[] = self::LC_DSC_USERNAME_VAR_NAME;
                $local_vars[] = self::LC_DSC_PASSWD_VAR_NAME;
                $local_vars[] = self::LC_DSC_LOGINHOST_VAR_NAME;

                // ユーザー公開用 データリレイストレージパス 変数の名前
                $local_vars[] = self::LC_DSC_OUTDIR_VAR_NAME;
                // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
                $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

                // リソースファイルに登録されている変数を抜出す。
                $dataString = file_get_contents($file_name);

                // ホスト変数の抜出を示すパラメータを追加
                $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

                $aryResultParse = $objWSRA->getParsedResult();
                unset($objWSRA);

                $file_vars_list = $aryResultParse[1];  //  リソースファイル内の変数を配列化

                // リソースファイルで変数が使用されているか判定
                if(count($file_vars_list) == 0){
                    // リソースファイルで変数が使用されていない場合は以降のチェックをスキップ
                    continue; 
                }

                // 各ホストのホスト変数があるか判定
                foreach( $ina_hosts as $no => $host_name ){
                    if(empty($ina_host_vars[$host_name])===true){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($resource,$host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        // 未登録でも処理は続行する。
                    }
                }
                // リソースファイルに登録されている変数のデータベース登録確認
                foreach( $file_vars_list as $var_name ){
                    // ホスト配列のホスト分繰り返し
                    foreach( $ina_hosts as $no=>$host_name ){
                        // 変数配列分繰り返し
                        // $ina_host_vars[ ipaddress ][ 変数名 ]=>具体値
                        // 変数の具体値が0の場合に具体値未登録になる対応
                        if((@strlen($ina_host_vars[$host_name][$var_name])==0) &&
                           (array_key_exists($var_name,$ina_host_vars[$host_name])==false)){
                            if($var_name == self::LC_DSC_PROTOCOL_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56213",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_USERNAME_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56211",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_PASSWD_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56212",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_LOGINHOST_VAR_NAME){
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56210",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            else{
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55214",
                                                                                array($resource,
                                                                                      $var_name,
                                                                                      $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                            }
                            // エラーリターンする
                            $result_code = false;
                        }
                        else{
                            //予約変数を使用している場合に対象システム一覧に該当データが登録されているか判定
                            if($ina_host_vars[$host_name][$var_name] == self::LC_DSC_UNDEFINE_NAME){
                                // プロトコル未登録
                                if($var_name == self::LC_DSC_PROTOCOL_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56213",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ユーザー名未登録
                                elseif($var_name == self::LC_DSC_USERNAME_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56211",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ログインパスワード未登録
                                elseif($var_name == self::LC_DSC_PASSWD_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56212",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    $result_code = false;
                                }
                                // ホスト名未登録
                                elseif($var_name == self::LC_DSC_LOGINHOST_VAR_NAME){
                                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56210",
                                                                               array($resource,
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
    //
    //    処理内容
    //      device.txtから作業対象ノードのIPアドレス情報を取得してConfigファイルに挿入し、
    //      最終的にConfigファイルを完成させる
    //    パラメータ
    //      $lv_Dsc_dev_Dir：  device.txt(作業対象ノード接続情報ファイル)
    //
    //      $config
    //
    //      $config_pass
    //
    //      $config_name
    //
    //    戻り値
    //      true:   正常
    //      false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function hostNodeAdd($lv_Dsc_dev_Dir,$config,$config_pass,$config_name){
        $pos = strpos($config_pass, '.');
        $conf = substr($config_pass, 0, $pos) . "." . "txt";
        // Config情報取得する
        $arydev = array();
        $aryconfig = file($config_pass);
        // ホスト分IP情報配列出力する
        $arydev = file($lv_Dsc_dev_Dir);
        $devcnt = (int)$arydev[0];
        // リソースに属するモジュール分
        $strModuleName = 'PSDesiredStateConfiguration'; // 配列化⇒aryModuleName //  *
        $strModulePath = ""; // 配列化⇒aryModulePath

        ////////////////////////////////////////////////////////////
        // Configurationファイル作成
        // device.txt(ノード情報),リソースファイルを１つのファイルに再構成する
        ////////////////////////////////////////////////////////////
        // Configurationブロックの冒頭Config宣言行を出力する
        $strConfigDeclar = "Configuration  " . $config_name . "\n" . " {  " . "\n";
        file_put_contents( $config_pass , $strConfigDeclar );
        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55202",array($config_pass));
        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        // リソースの属するモジュール名をキーワードコマンドレット行の引数として、利用するリソースが属するモジュール分、各１行づつ出力(配列ループ)
        $strDSCResourcModule = "\t" . "Import-DscResource -ModuleName " . $strModuleName . $strModulePath . "\n" ;
        file_put_contents( $config_pass , $strDSCResourcModule, FILE_APPEND | LOCK_EX );

        // ノードブロックの行を出力
        $strNode = "Node " .  " ( " . "\n" ;
        file_put_contents( $config_pass , $strNode, FILE_APPEND | LOCK_EX );

        // ホスト分IP情報出力する
        $i = 1;
        for ( $cnt = 0 ; $cnt < $devcnt ; $cnt ++ ){
        	$str = $arydev[$i];
            $pos = strpos($str, ',');
            $devip = substr($str, 0, $pos) ;
            $devstr = '"' . $devip ;
            if( $cnt + 1 < $devcnt ){
                $devstr = $devstr . '"' . ", " . "\n" ;                // 途中IP
            }
            else{
                $devstr = $devstr . '"' . "\n" ;                      // 最終IP
            }
            file_put_contents( $config_pass , $devstr , FILE_APPEND | LOCK_EX );
        	$i ++ ;
        }
        $devstr = " ) "  . "\n" ;
        file_put_contents( $config_pass , $devstr , FILE_APPEND | LOCK_EX );
        // ------------------Config情報マージする -----------------------------
        $devstr = "  { "  . "\n" ;
        file_put_contents( $config_pass , $devstr , FILE_APPEND | LOCK_EX );
        // リソースファイルの内容を追加マージする
        file_put_contents( $config_pass , $aryconfig , FILE_APPEND | LOCK_EX );
        $devstr = "  } " . "\n" ;
        file_put_contents( $config_pass , $devstr , FILE_APPEND | LOCK_EX );
        // ------------------Config情報マージする -----------------------------
        $devstr = " } " . $config_name  . "\n" ;
        file_put_contents( $config_pass , $devstr , FILE_APPEND | LOCK_EX );
        // 最終行でConfig名を出力する

        $boolRet = false;
        return $boolRet;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0013
    //  処理内容
    //    リソースファイルのフォーマットをチェックする。
    //
    //  パラメータ
    //    $in_file_name:        リソースファイルパス(DSC/データリレイストレージ)
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function CheckChildResourceFormat($in_file_name){
        $result_code = true;

        ///////////////////////////////////////////////////////////////////
        // リソースのフォーマット判定
        ///////////////////////////////////////////////////////////////////
        $resource = basename($in_file_name);

        $fd = @fopen($in_file_name, "r");
        if($fd === null){
            // "子Resourceファイル(｛｝)の読込に失敗。";    TODO メッセージ修正 子Resourceファイル⇒リソースファイル 
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55218",array($resource));
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
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55221",array($resource,$line_no));
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

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCドライバ区分をコンストラクト時に設定
    //
    //  パラメータ
    //    $in_val:      DSCドライバ区分
    //                    DSC:          DF_DSC_DRIVER_ID                            8
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDscDriverID($in_val){
        $this->lv_Dsc_driver_id = $in_val;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCドライバ区分を取得
    //
    //  パラメータ
    //    なし
    //
    //  戻り値
    //   DSC:          DF_DSC_DRIVER_ID
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDscDriverID(){
        return($this->lv_Dsc_driver_id);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //     DSC用 ベースディレクトリ（データリレイストレージ（Linux）パス)をコンストラクト時に設定
    //
    //  パラメータ
    //   $in_base_name:  共有パス区分
    //                     DSC_SH_PATH_ITA:  DSC作業用 ITA側
    //                     DSC_SH_PATH_DSC:  DSC作業用 DSC側
    //                     SYMPHONY_SH_PATH_DSC: symphony作業用 Ansible側
    //    $in_dir:      DSC用 ベースディレクトリ
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDscBaseDir($in_base_name,$in_dir){
        $this->lv_Dsc_base_Dir[$in_base_name]  = $in_dir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCドライバ用のベースディレクトリ(データリレイストレージ)パスを取得
    //  パラメータ
    //   $in_base_name:  共有パス区分
    //                     DSC_SH_PATH_ITA:  DSC作業用 ITA側
    //                     DSC_SH_PATH_DSC:  DSC作業用 DSC側
    //                     SYMPHONY_SH_PATH_DSC: symphony作業用 Ansible側
    //
    //  戻り値
    //    DSCドライバ用のベースディレクトリ(データリレイストレージ)パス
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDscBaseDir($in_base_name){
        return($this->lv_Dsc_base_Dir[$in_base_name]);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCドライバ作業用 inディレクトリ名を設定
    //  パラメータ
    //    $in_dir:      inディレクトリ
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDsc_in_Dir($in_indir){
        $this->lv_Dsc_in_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCドライバ作業用 inディレクトリ名を取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    inディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_in_Dir(){
        return($this->lv_Dsc_in_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //##  処理内容
    //      child_resourcesディレクトリ名を取得
    //    パラメータ
    //      なし
    //
    //    戻り値
    //      child_resourcesディレクトリ名
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_child_resources_Dir(){
        return($this->lv_Dsc_child_resources_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //#  処理内容
    //      リソースファイル格納ディレクトリ名を記憶
    //    パラメータ
    //      $in_dir:      child_resourcesディレクトリ
    //
    //    戻り値
    //      なし
    ////////////////////////////////////////////////////////////////////////////////
    function setResource_child_resources_Dir($in_dir){
        $this->lv_Resource_child_resources_Dir = $in_dir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //#  処理内容
    //      リソースファイル格納ディレクトリ名を取得
    //    パラメータ
    //      なし
    //
    //    戻り値
    //      child_resourcesディレクトリ名
    ////////////////////////////////////////////////////////////////////////////////
    function getResource_child_resources_Dir(){
        return($this->lv_Resource_child_resources_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    host_varsディレクトリ(ターゲットノード情報格納)パスを設定
    //
    //  パラメータ
    //    $in_dir:      host_varsディレクトリ
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDsc_host_vars_Dir($in_indir){
        $this->lv_Dsc_host_vars_Dir = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    host_varsディレクトリ(ターゲットノード情報格納)パスを取得
    //
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    host_varsディレクトリパス
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_host_vars_Dir(){
        return($this->lv_Dsc_host_vars_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSC作業用ディレクトリ outディレクトリパスを設定
    //
    //  パラメータ
    //    $in_dir:      outディレクトリパス
    //
    //  戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function setDsc_out_Dir($in_indir){
        $this->lv_Dsc_out_Dir = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSC作業用ディレクトリ outディレクトリパスを取得
    //
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    out_Dirディレクトリパス
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_out_Dir(){
        return($this->lv_Dsc_out_Dir);
    }
    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    リソースファイル(Config素材ファイル)格納ディレクトリパスを設定 (コンストラクト時設定)
    //
    //  パラメータ
    //    $in_dir:      リソースファイル(Config素材ファイル)格納ディレクトリ
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setITA_resource_Dir($in_indir){
        $this->lv_ita_resources_Dir = $in_indir;
    }
    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    リソースファイル(Config素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_resources_Dir:    リソースファイル(Config素材ファイル)格納ディレクトリ
    //    original_hosts_varsディレクトリ名 ???TS
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_resource_Dir(){
        return($this->lv_ita_resources_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //    hostsファイル名を取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    hostsファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_hosts_file(){
        $file = $this->lv_Dsc_in_Dir . "/" . self::LC_DSC_HOSTS_FILE;
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //  ホスト変数定義ファイル名を取得
    //  パラメータ
    //    $in_hostname:       ホスト名(IPアドレス)
    //
    //  戻り値
    //    ホスト変数定義ファイル名
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_host_var_file($in_hostname){
        $file = sprintf(self::LC_DSC_HOST_VAR_FILE_MK,$this->getDsc_host_vars_Dir(),$in_hostname);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //  DSC リソースファイルパスを取得する
    //
    //  パラメータ
    //    $in_filename:       リソースファイル名
    //    $in_pkey:           素材管理 Pkey
    //
    //  戻り値
    //    リソースファイルフルパス(DSC)
    //
    //  備考
    //    関数名変更 getDsc_child_playbiook_file⇒getDsc_resource_filename
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_resource_filename($in_pkey,$in_filename)  // 関数名変更
    {
        $intNumPadding = 10;
        $dscdir=$this->getDsc_in_Dir();

        $file = sprintf(self::LC_DSC_CHILD_RESOURCE_FILE_MK,
                        $dscdir,
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //  ITA リソースファイルパスを取得
    //  パラメータ
    //    $in_key:        リソースファイルのPkey(データベース)
    //    $in_filename:   リソースファイル名
    //
    //  戻り値
    //    ホスト変数定義ファイル名名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_resource_file($in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_RESOURCES_DIR_MK,
                        $this->getITA_resource_Dir(),
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

    // Log出力
    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        require ($root_dir_path . $log_output_php);
        if($this->getDsc_out_Dir() != ""){
            $logfile = $this->getDsc_out_Dir() . "/" . "error.log";
            $filepointer=fopen(  $logfile, "a");
            flock($filepointer, LOCK_EX);
            fputs($filepointer, $p3 . "\n" );
            flock($filepointer, LOCK_UN);
            fclose($filepointer);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0015
    //  処理内容
    //    データベースよりDSCの実行対象ノードの接続認証情報を取得し、device.txtを作成する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $in_operation_id:      オペレーションID
    //    $ina_hostlist:         ホスト一覧返却配列
    //                           [管理システム項番]=ホスト名(IP);
    //    $ina_hostprotcollist:  ホスト毎プロトコル一覧返却配列
    //                           [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //    $ina_hostostypelist:   ホスト毎OS種別一覧返却配列
    //                           [ホスト名(IP)]=$row[OS種別];
    //    #1073 下記を追加--------------------------------------------------
    //    既存のデータが重なるが、今後の開発はこの変数を使用する。
    //    $ina_hostinfolist:     機器一覧ホスト情報
    //                           [ホスト名(IP)]=HOSTNAME=>''             ホスト名
    //                                          PROTOCOL_ID=>''	        接続プロトコル
    //                                          LOGIN_USER=>''           ログインユーザー名
    //                                          LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
    //                                                                   1:管理(●)   N0:未管理
    //                                          LOGIN_PW=>''             パスワード
    //                                                                   パスワード管理が1の場合のみ有効
    //                                          LOGIN_AUTH_TYPE=>''      DSC認証方式
    //                                                                   2:パスワード認証 1:鍵認証
    //                                          WINRM_PORT=>''           WinRM接続プロトコル
    //                                          OS_TYPE_ID=>''           OS種別
    //  ------------------------------------------------#1073 ここまで追加
    //    // $ina_hostinfolist:に下記を追加--------------------------------------------------
    //                                          DSC_CERTIFICATE_FILE=>''        ODSC利用情報 認証キーファイル
    //                                          DSC_CERTIFICATE_THUMBPRINT=>''  ODSC利用情報 サムプリント
    //    -------------------------------------------------- ここまで追加
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDBHostList($in_pattern_id,
                           $in_operation_id,
                           &$ina_hostlist,
                           &$ina_hostprotcollist,
                           &$ina_hostostypelist,
                           &$ina_hostinfolist,
                           $in_retry_timeout){
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
               "  TBL_2.WINRM_PORT, \n" .
               "  TBL_2.LOGIN_PW_HOLD_FLAG, \n" .
               "  TBL_2.LOGIN_AUTH_TYPE, \n" .
               "  TBL_2.OS_TYPE_ID, \n" .
               "  TBL_2.DSC_CERTIFICATE_FILE, \n" .
               "  TBL_2.DSC_CERTIFICATE_THUMBPRINT, \n" .
               "  TBL_2.DISUSE_FLAG, \n" .
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
               "      $this->lv_dsc_pho_linkDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_4.PATTERN_ID   = :PATTERN_ID   AND \n" .
               "      TBL_4.DISUSE_FLAG  = '0' \n" .
               "  ) TBL_1 \n" .
               "LEFT OUTER JOIN C_STM_LIST TBL_2 ON ( TBL_1.SYSTEM_ID = TBL_2.SYSTEM_ID ) \n" .
               "ORDER BY TBL_2.IP_ADDRESS; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
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
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
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
        $devlist = array();                                // 並列実行情報リスト
    	$devcnt = 0;                                       // 並列実行数カウント
        while ( $row = $objQuery->resultFetch() ){
            //-------------------------------------------------------------------
            // 初期化
            //-------------------------------------------------------------------
            $hostname = '';
            $ip_address = '';
            $login_auth_type = '';
            $pw_hold_flag = '';
            $protocol = '';
            $login_user = '';
            $login_pass = '';
            $os_type_id = '';
            $target_username = '';
            $target_password = '';
            $system_id = '';
            $certificate_file = '';
            $certificate_thumbprint = '';

            if($row['DISUSE_FLAG']=='0'){
                //--------------------------------------------------------------------------------------
                // IPアドレス設定値確認
                //--------------------------------------------------------------------------------------
                if(strlen($row['IP_ADDRESS'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56205",
                                                               array($row['SYSTEM_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                $ip_address = $row['IP_ADDRESS'];
                //--------------------------------------------------------------------------------------
                // ホスト名称設定値確認
                //--------------------------------------------------------------------------------------
                if(strlen($row['HOSTNAME'])==0){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56202",
                                                               array($row['IP_ADDRESS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                $hostname = $row['HOSTNAME'];

                //--------------------------------------------------------------------------------------
                // 認証方式の設定値確認
                //--------------------------------------------------------------------------------------
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
                //--------------------------------------------------------------------------------------
                if($login_auth_type == ''){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70040",
                                                               array($row['IP_ADDRESS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }

                //--------------------------------------------------------------------------------------
                // パスワード管理フラグの設定値確認
                //--------------------------------------------------------------------------------------
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
                //--------------------------------------------------------------------------------------
               if($pw_hold_flag == ''){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70041",
                                                               array($row['IP_ADDRESS']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                //--------------------------------------------------------------------------------------
                // 認証方式がパスワード認証の場合に管理パスワードがありでパスワードが設定されているか判定
                //--------------------------------------------------------------------------------------
                if($login_auth_type === self::LC_LOGIN_AUTH_TYPE_PW){
                    // パスワード管理ありの判定
                    if($pw_hold_flag != self::LC_LOGIN_PW_HOLD_FLAG_ON){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70042",
                                                               array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                    // パスワード登録の判定
                    if(strlen($row['LOGIN_PW'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70043",
                                                               array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                }
                //--------------------------------------------------------------------------------------
                // パスワード管理ありでパスワードが設定されているか判定
                // パスワード管理ありの判定
                //--------------------------------------------------------------------------------------
                if($pw_hold_flag == self::LC_LOGIN_PW_HOLD_FLAG_ON){
                    // パスワード登録の判定
                    if(strlen($row['LOGIN_PW'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70043",
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
                    $login_pass = self::LC_DSC_UNDEFINE_NAME;
                }

                //**************************************************************************************
                switch($this->getDscDriverID()){
                case DF_DSC_DRIVER_ID:

                    if($row['PROTOCOL_NAME']===null){
                        $protocol = self::LC_DSC_UNDEFINE_NAME;
                    }
                    else{
                        $protocol = $row['PROTOCOL_NAME'];
                    }
                    if(strlen($row['LOGIN_USER'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56203",
                                                                   array($row['IP_ADDRESS']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                    $login_user = $row['LOGIN_USER'];

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
                
                //--------------------------------------------------------------------------------------
                // 認証キーファイル,サムプリント設定値確認
                //--------------------------------------------------------------------------------------
                if(strlen($row['DSC_CERTIFICATE_FILE'])==0){
                    if(strlen($row['DSC_CERTIFICATE_THUMBPRINT'])!=0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56214",
                                                                   array($row['SYSTEM_ID']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                }
                else{
                    if(strlen($row['DSC_CERTIFICATE_THUMBPRINT'])==0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56215",
                                                                   array($row['SYSTEM_ID']));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                        unset($objQuery);
                        return false;
                    }
                }
                $system_id = $row['SYSTEM_ID'];
                $certificate_file       = $row['DSC_CERTIFICATE_FILE'];
                $certificate_thumbprint = $row['DSC_CERTIFICATE_THUMBPRINT'];
                //----------------------------------------------
                // 読込情報の設定
                //----------------------------------------------
                $ina_hostinfolist[$row['IP_ADDRESS']]['HOSTNAME']           = $hostname;              //ホスト名
                $ina_hostinfolist[$row['IP_ADDRESS']]['IP_ADDRESS']         = $ip_address;            //IP
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_USER']         = $login_user;            //ログインユーザー名
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_PW']           = $login_pass;            //パスワード
                $ina_hostinfolist[$row['IP_ADDRESS']]['PROTOCOL_ID']        = $protocol;              //接続プロトコル
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_PW_HOLD_FLAG'] = $pw_hold_flag;          //パスワード管理フラグ
                $ina_hostinfolist[$row['IP_ADDRESS']]['LOGIN_AUTH_TYPE']    = $login_auth_type;       //Dsc認証方式
                $ina_hostinfolist[$row['IP_ADDRESS']]['WINRM_PORT']         = $winrm_port;            //WINRM接続プロトコル
                $ina_hostinfolist[$row['IP_ADDRESS']]['OS_TYPE_ID']         = $os_type_id;//OS種別
                $ina_hostinfolist[$row['IP_ADDRESS']]['SYSTEM_ID']                  = $system_id;              // システムID
                $ina_hostinfolist[$row['IP_ADDRESS']]['DSC_CERTIFICATE_FILE']       = $certificate_file;       // 認証キーファイル
                $ina_hostinfolist[$row['IP_ADDRESS']]['DSC_CERTIFICATE_THUMBPRINT'] = $certificate_thumbprint; // サムプリント
            }
            // 作業対象ホスト管理に登録されているホストが管理対象システム一覧(C_STM_LIST )に未登録
            elseif($row['DISUSE_FLAG']===null){
                // "作業対象ホスト(項番:｛｝)に登録されているホスト(管理システム項番:｛｝)が管理対象システム一覧に未登録";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56105",
                                                           array($row['PHO_LINK_ID'],
                                                                 $row['SYSTEM_ID'] ));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                unset($objQuery);
                return false;
            }
            $devlist[$devcnt]['IP_ADDRESS'] = $ip_address;            // IP
            $devlist[$devcnt]['HOSTNAME']   = $hostname;              // ホスト名
            $devlist[$devcnt]['LOGIN_USER'] = $login_user;            // ログインユーザー名
            $devlist[$devcnt]['LOGIN_PW']   = $login_pass;            // パスワード
            $devlist[$devcnt]['SYSTEM_ID']  = $os_type_id;                             // システムID
            $devlist[$devcnt]['DSC_CERTIFICATE_FILE']       = $certificate_file;       // 認証キーファイル
            $devlist[$devcnt]['DSC_CERTIFICATE_THUMBPRINT'] = $certificate_thumbprint; // サムプリント
            $devlist[$devcnt]['RETRY_TIMEOUT'] = $in_retry_timeout;
            $devcnt = $devcnt + 1;                                    // 並列実行数カウント
        }
        //***********************************************************************
        // 並列実行情報を出力(device.txt(作業対象ノード認証情報ファイル)作成)  
        //***********************************************************************
        $cntstr = "";
        $devstr = "";

    	$dev_dir = $this->getDsc_host_vars_Dir();
        // device.txt(作業対象ノード認証情報ファイル)のパスを取得
        $lv_Dsc_dev_Dir  = $dev_dir . "/" . self::LC_DSC_DEV_FILE;
        $cntstr = strval($devcnt) . "\n" ;

        file_put_contents( $lv_Dsc_dev_Dir , $cntstr );

        for ( $n = 0 ; $n < $devcnt ; $n ++ ){
            $devstr = "";

            $str = sprintf ( "%s" , $devlist[$n]['IP_ADDRESS']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" , $devlist[$n]['HOSTNAME']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" , $devlist[$n]['LOGIN_USER']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" , $devlist[$n]['LOGIN_PW']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" , $devlist[$n]['DSC_CERTIFICATE_FILE']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" ,  $devlist[$n]['DSC_CERTIFICATE_THUMBPRINT']) . "," ;
            $devstr = $devstr . $str ;
            $str = sprintf ( "%s" ,  $devlist[$n]['RETRY_TIMEOUT']) . "\n" ;
            $devstr = $devstr . $str ;
            file_put_contents( $lv_Dsc_dev_Dir , $devstr , FILE_APPEND | LOCK_EX );
        }

        // fetch行数を取得
        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56106",
                                                       array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            unset($objQuery);
            return false;
        }
        if (count($ina_hostlist) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56107",
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
    // F0016
    //  処理内容
    //    DSCで実行する変数をデータベースより取得する。
    //    データベースよりリソースファイル内の変数具体値の値を取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $in_operation_id:      オペレーションID
    //    $ina_host_vars:        変数一覧返却配列
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //    $ina_child_vars_list:  配列変数一覧返却配列
    //                          [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //    $ina_DB_child_vars_list:
    //                          メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                          [ 変数名 ][メンバー変数名]=0
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDBVarList($in_pattern_id,
                          $in_operation_id,
                          &$ina_host_vars,
                          &$ina_child_vars_list,
                          &$ina_DB_child_vars_list){
        $child_vars_list = array();
        $varerror_flg = true;
        switch($this->getDscDriverID()){
        case DF_DSC_DRIVER_ID:
            $sql = "SELECT \n" .
               "  TBL_1.ASSIGN_ID, \n" .
               "  TBL_1.SYSTEM_ID, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_dsc_pho_linkDB TBL_4 \n" .
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
               "      $this->lv_dsc_vars_masterDB TBL_4 \n" .
               "    WHERE \n" .
               "      TBL_4.VARS_NAME_ID = TBL_2.VARS_NAME_ID AND \n" .
               "      TBL_4.DISUSE_FLAG = '0' \n" .
               "  ) AS VARS_NAME, \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      COUNT(*) \n" .
               "    FROM \n" .
               "      $this->lv_dsc_vars_assignDB TBL_6 \n" .
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
               "      $this->lv_dsc_vars_assignDB TBL_3 \n" .
               "    WHERE \n" .
               "      TBL_3.OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND \n" .
               "      TBL_3.PATTERN_ID   = :PATTERN_ID   AND \n" .
               "      TBL_3.DISUSE_FLAG  = '0' \n" .
               "  ) TBL_1 \n" .
               "LEFT OUTER JOIN $this->lv_dsc_pattern_vars_linkDB TBL_2 ON ( TBL_1.VARS_LINK_ID = TBL_2.VARS_LINK_ID ) \n" .
               "ORDER BY IP_ADDRESS,VARS_NAME,ASSIGN_SEQ; \n";
            break;
        }

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
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
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
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
        while ( $row = $objQuery->resultFetch() ){
            array_push( $tgt_row, $row );
        }
        foreach( $tgt_row as $row ){
            $assign_seq = true;
            switch($this->getDscDriverID()){
            case DF_DSC_DRIVER_ID:
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
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                if($row['VARS_NAME']===null){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                // 下記予約変数が使用されているかチェックする。
                // リソースファイルに埋め込まれるリモート接続コマンド用変数の名前
                // リソースファイルに埋め込まれるリモートログインのユーザー用変数の名前
                // リソースファイルに埋め込まれるリモートログインのパスワード用変数の名前
                // リソースファイルに埋め込まれるホスト名用変数の名前
                if(($row['VARS_NAME']==self::LC_DSC_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_OUTDIR_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_PASSWD_VAR_NAME)){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56201",
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
                    //----------------------------------------------
                    // 配列変数の入力情報を判定
                    //----------------------------------------------
                    $ret = $this->chkChildVars($row,$ina_child_vars_list);
                    if($ret === false){
                        // エラーメッセージは出力済み
                        unset($objQuery);
                        return false;
                    }

                    // 配列変数か判定
                    if($row['VARS_ATTRIBUTE_01'] != '1'){
                        if($row['VARS_NAME_COUNT'] == 1){
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
                    else{
                        // 配列変数で変数の複数具体値はありえない
                        //ホスト変数配列作成
                        $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']]=$row['VARS_ENTRY'];
                    }

                }
            }
            elseif($row['DISUSE_FLAG']===null){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56109",
                                                           array($row['ASSIGN_ID']));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                unset($objQuery);
                return false;
            }
            // DISUSE_FLAG = '1'は読み飛ばし
        }

        // 代入順序がブランクの場合の具体値退避
        foreach( $tgt_row as $row ){
            // 代入順序がブランクか判定
            $assign_seq = false;
            switch($this->getDscDriverID()){
            case DF_DSC_DRIVER_ID:
                if(strlen($row['ASSIGN_SEQ']) === 0)
                    $assign_seq = true;
                break;
            }

            if($row['DISUSE_FLAG']=='0'){
                // 代入値管理のみあるホスト変数(作業対象ホストにない)をはじく
                if($row['PHO_LINK_HOST_COUNT'] == 0){
                    continue;
                }

                // "代入値管理(項番：｛｝)に登録されているホストが管理対象システム一覧に未登録"; TODO メッセージ修正 管理対象システム一覧⇒機器一覧
                if($row['IP_ADDRESS']===null){
                    // "代入値管理(項番：｛｝)に登録されているホストが管理対象システム一覧に未登録"; TODO 同上
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56108",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                if($row['VARS_NAME']===null){
                    // "代入値管理(項番：｛｝)に登録されている変数が変数名マスタに未登録";
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56110",
                                                               array($row['ASSIGN_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }
                // 下記予約変数が使用されているかチェックする。
                // リソースファイルに埋め込まれるリモート接続コマンド用変数の名前
                // リソースファイルに埋め込まれるリモートログインのユーザー用変数の名前
                // リソースファイルに埋め込まれるリモートログインのパスワード用変数の名前
                // リソースファイルに埋め込まれるホスト名用変数の名前
                if(($row['VARS_NAME']==self::LC_DSC_PROTOCOL_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_USERNAME_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_OUTDIR_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_SYMPHONY_DIR_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_LOGINHOST_VAR_NAME) ||
                   ($row['VARS_NAME']==self::LC_DSC_PASSWD_VAR_NAME)){
                    // "ホスト(IP:｛｝)で使用している変数(｛｝)は予約変数なので使用出来ない。";
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56201",
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

                    //----------------------------------------------
                    // 配列変数の入力情報を判定
                    //----------------------------------------------
                    $ret = $this->chkChildVars($row,$ina_child_vars_list);
                    if($ret === false){
                        // エラーメッセージは出力済み
                        unset($objQuery);
                        return false;
                    }

                    // 配列変数か判定
                    if($row['VARS_ATTRIBUTE_01'] != '1'){
                        if($row['VARS_NAME_COUNT'] == 1){
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
                    else{
                        // 配列変数で変数の複数具体値はありえない
                        //ホスト変数配列作成
                        $ina_host_vars[$row['IP_ADDRESS']][$row['VARS_NAME']]=$row['VARS_ENTRY'];
                    }

                }
            }
            elseif($row['DISUSE_FLAG']===null){
                // "代入値管理(項番：｛｝)に登録されている変数が作業パターン変数紐付管理に未登録";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56109",
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
            //----------------------------------------------
            // 列順序毎のメンバー変数がメンバー変数マスタと一致するか判定
            // 配列変数の入力情報を判定
            //----------------------------------------------
            $varerror_flg = $this->chkChildVarsList($ina_child_vars_list,$ina_DB_child_vars_list);
        }

        return $varerror_flg;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0017
    //  処理内容
    //    DSCで実行するリソースファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $ina_resources:  リソースファイル返却配列
    //                             ina_resources[INCLUDE順序][素材管理Pkey]=>素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscResourceList($in_pattern_id,&$ina_resources,&$ina_resourceName){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_RESOURCEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.RESOURCE_MATTER_ID, \n" .
               "TBL_1.INCLUDE_SEQ, \n" .
               "TBL_2.RESOURCE_MATTER_FILE, \n" .
               "TBL_2.RESOURCE_MATTER_NAME, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.RESOURCE_MATTER_ID, \n" .
               "      TBL3.INCLUDE_SEQ \n" .
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_RESOURCE TBL_2 ON \n" .
               "      ( TBL_1.RESOURCE_MATTER_ID = TBL_2.RESOURCE_MATTER_ID) \n" .
               "ORDER BY TBL_1.INCLUDE_SEQ; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        $ina_resources = array();
        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_resources[インクルード順序][素材管理Pkey]=>リソースファイルの配列作成
                if(strlen($row['RESOURCE_MATTER_FILE']) == 0){
                    // "プレイブップ素材集(｛｝)にプレイブック素材が未登録。";  TODO メッセージ修正 プレイブップ素材集⇒コンフィグ素材集にリソース（コンフィグ素材）
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70026",
                                                               array($row['RESOURCE_MATTER_ID']));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

                    unset($objQuery);
                    return false;
                }

                $ina_resources[$row['INCLUDE_SEQ']][$row['RESOURCE_MATTER_ID']]=$row['RESOURCE_MATTER_FILE'];
                $ina_resourceName[$row['INCLUDE_SEQ']][$row['RESOURCE_MATTER_ID']]=$row['RESOURCE_MATTER_NAME'];
            }
            // 素材管理(B_DSC_RESOURCE)にリソースが未登録の場合
            elseif($row['DISUSE_FLAG']===null){
                // "作業 パターン詳細(紐付項番:｛｝)に登録されているプレイブック素材がプレイブック素材集 に未登録。";  TODO メッセージ修正 プレイブック⇒リソースファイルがコンフィグ素材集 TS
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56101",
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
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56102",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            unset($objQuery);
            return false;
        }
        //対象リソースの数を確認
        $count_res = count($ina_resources);
        if (count($ina_resources) < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56103",array($in_pattern_id));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0019
    //  処理内容
    //    システム予約変数を設定する
    //
    //  パラメータ
    //    $ina_host_vars:        変数一覧
    //                          [ホスト名(IP)][ 変数名 ]=>具体値
    //    $ina_hostprotcollist:  ホスト毎プロトコル一覧返却配列
    //                          [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function addSystemvars(&$ina_host_vars,$ina_hostprotcollist){
       foreach($ina_hostprotcollist as $host_ip=>$hostnamelist){
           foreach($hostnamelist as $host_name=>$prolist)
           foreach($prolist      as $pro=>$userlist)
           foreach($userlist     as $user_name=>$user_pass)

           //システム予約変数を設定
           // リソースファイルに埋め込まれるリモート接続コマンド用変数の名前
           $ina_host_vars[$host_ip][self::LC_DSC_PROTOCOL_VAR_NAME]  = $pro;

           // リソースファイルに埋め込まれるリモートログインのユーザー用変数の名前
           $ina_host_vars[$host_ip][self::LC_DSC_USERNAME_VAR_NAME]  = $user_name;

           //リモートログインのパスワードが未登録か判定
           if($user_pass != self::LC_DSC_UNDEFINE_NAME){
               // リソースファイルに埋め込まれるリモートログインのパスワード用変数の名前
               $ina_host_vars[$host_ip][self::LC_DSC_PASSWD_VAR_NAME]    = $user_pass;
           }

           // リソースファイルに埋め込まれるホスト名用変数の名前
           $ina_host_vars[$host_ip][self::LC_DSC_LOGINHOST_VAR_NAME] = $host_name;

           // ユーザー公開用データリレイストレージパス 変数の名前
           $ina_host_vars[$host_ip][self::LC_DSC_OUTDIR_VAR_NAME]   = $this->lv_user_out_Dir;
           // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
           $ina_host_vars[$host_ip][self::LC_SYMPHONY_DIR_VAR_NAME] = $this->lv_symphony_instance_Dir;
           
       }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0027
    //  処理内容
    //    DSC用作業ディレクトリパス配列を作成する。
    //    ディレクトリ階層
    //    /ベースディレクトリ/ドライバ名/オケストレータID/作業実行番号/in
    //                                                  /out
    //
    //  パラメータ
    //    $in_oct_id              オケストレータID
    //                            dsc        : ns
    //
    //    $in_execno              作業実行番号
    //
    //  戻り値
    //    array:  キー[0]～[6]：各種DSCドライバ用ディレクトリパス
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDscWorkingDirectories($in_oct_id,$in_execno){
        $aryRetDscWorkingDir = array();

        $base_dir = $this->getDscBaseDir('DSC_SH_PATH_ITA');

        //ベースディレクトリの存在チェック
        if( !is_dir( $base_dir ) ){
            //ベースディレクトリが存在しない場合はエラー
            // "NFSディレクトリが見つからない。"     （データリレイストレージ）
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55201");
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }

        $driver_id = $this->getDscDriverID();
        switch($driver_id){
            case DF_DSC_DRIVER_ID:
                $c_dir_per_dsc_orc_type_id = $base_dir . "/" . self::LC_DSC_DIR;
                $c_dir_per_dsc             = "\\" . self::LC_DSC_DIR;
                break;

            default:
                // 内部処理異常(FILE:{} LINE:{})
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55200",array(basename(__FILE__) . "-" .     __LINE__));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
        }

        // 返し値[0] ドライバ区分付
        $aryRetDscWorkingDir[0] = $c_dir_per_dsc_orc_type_id;

        // 返し値[1] ドライバ区分+オケストレータID付き
        $c_dir_per_orc_id           = $c_dir_per_dsc_orc_type_id . "/" . $in_oct_id;
        $aryRetDscWorkingDir[1] = $c_dir_per_orc_id;

        // 返し値[2] ドライバ区分+オケストレータID付き+作業実行番号付き
        $c_dir_per_exe_no           = $c_dir_per_orc_id . "/" . sprintf("%010s",$in_execno);
        $aryRetDscWorkingDir[2] = $c_dir_per_exe_no;

        // 返し値[3] ドライバ区分+オケストレータID付き+作業実行番号付き+inフォルダ名
        $c_dir_in_per_exe_no        = $c_dir_per_exe_no . "/" . self::LC_DSC_IN_DIR;
        $aryRetDscWorkingDir[3] = $c_dir_in_per_exe_no;

        // 返し値[4] ドライバ区分+オケストレータID付き+作業実行番号付き+outフォルダ名
        $c_dir_out_per_exe_no       = $c_dir_per_exe_no . "/" . self::LC_DSC_OUT_DIR;
        $aryRetDscWorkingDir[4] = $c_dir_out_per_exe_no;

        // 返し値[5] ベースDirectory
        $aryRetDscWorkingDir[5] = $base_dir;

        // 返し値[6] dsc_storage_path  Windows環境用パス //0525修正
        $c_dir_dsc_storage_path = $c_dir_per_dsc . "\\" . "ns" . "\\" . sprintf("%010s",$in_execno) . "\\" . self::LC_DSC_IN_DIR;
        $aryRetDscWorkingDir[6] = $c_dir_dsc_storage_path;

        // 返し値[7] host_varsディレクトリ
        $c_dir_in_host_vars     = $c_dir_in_per_exe_no . "/" . self::LC_DSC_HOST_VARS_DIR;
        $aryRetDscWorkingDir[7] = $c_dir_in_host_vars;

        return $aryRetDscWorkingDir;
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
        if($this->getDsc_out_Dir() != ""){
            $logfile = $this->getDsc_out_Dir() . "/" . "error.log";
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
               "       $this->lv_dsc_vars_masterDB                 \n" .
               "     WHERE                                             \n" .
               "       VARS_NAME_ID = TBL_1.VARS_LINK_ID AND    \n" .
               "       DISUSE_FLAG = '0'                               \n" .
               "   ) VARS_NAME,                                        \n" .
               "   TBL_1.VARS_ENTRY                               \n" .
               " FROM                                                  \n" .
               "   $this->lv_dsc_varsDB TBL_1                \n" .
               " WHERE                                                 \n" .
               "   TBL_1.DISUSE_FLAG = '0';                            \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        $ina_child_vars = array();
        while ( $row = $objQuery->resultFetch() ){
            $ina_child_vars[$row['VARS_NAME']][$row['VARS_ENTRY']] = 0;
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0035
    //  処理内容
    //    代入値管理のメンバー変数の入力情報を判定
    //
    //  パラメータ
    //    $in_row:                getDBVarListのクエリー結果
    //    $ina_child_vars_list:   メンバー変数リスト
    //                            [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkChildVars($in_row,&$ina_child_vars_list){
        switch($this->getDscDriverID()){
        case DF_DSC_DRIVER_ID:
            // 配列変数か判定
            if($in_row['VARS_ATTRIBUTE_01'] == '1'){
                // 列順序の登録判定                        //  *
            }
            else{
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0036
    //  処理内容
    //    代入値管理の配列変数のメンバー変数とメンバー変数マスタとの照合
    //
    //  パラメータ
    //    $ina_child_vars_list:   メンバー変数リスト
    //                            [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
    //
    //    $ina_DB_child_vars_list: メンバー変数マスタの配列変数のメンバー変数リスト返却
    //                             [ 変数名 ][メンバー変数名]=0
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkChildVarsList($ina_child_vars_list,&$ina_DB_child_vars_list){
        $ret_code = true;
        $ina_DB_child_vars_list = array();
        switch($this->getDscDriverID()){
        case DF_DSC_DRIVER_ID:
            //----------------------------------------------
            // メンバー変数マスタの情報取得
            //----------------------------------------------
             $ret = $this->getDBChildVarsList($ina_DB_child_vars_list);
            if($ret === false){
                return false;
            }
            // メンバー変数マスタのメンバー変数と代入値管理で登録されているメンバー変数が同一か判定
            foreach($ina_child_vars_list as $host_ip=>$vars_list ){
                foreach($vars_list as $vars_name=>$col_seq_list ){
                    // 代入値管理で登録されている配列変数がメンバー変数マスタに登録されているか判定
                    $DB_chl_vars_count = @count($ina_DB_child_vars_list[$vars_name]);
                    if($DB_chl_vars_count === 0){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70065",
                                                                   array($vars_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                    // 代入値管理で登録されている列順序毎のメンバー変数が
                    // メンバー変数マスタに登録されているメンバー変数と一致しているか判定
                    foreach($col_seq_list as $col_seq=>$chl_vars_list ){
                        foreach($chl_vars_list as $chl_vars_name=>$chl_vars_val ){
                            // 代入値管理で登録されているメンバー変数がメンバー変数マスタに登録されているか判定
                            if(@count($ina_DB_child_vars_list[$vars_name][$chl_vars_name]) === 0){
                                // "配列変数(｛｝)のメンバー変数(｛｝)がメンバー変数マスタに未登録。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70066",
                                                                           array($vars_name,$chl_vars_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                return false;
                            }
                        }
                        $err_chl_vars_name_list = "";
                        // メンバー変数マスタのメンバー変数が代入値管理で登録しているか
                        foreach($ina_DB_child_vars_list[$vars_name] as $DB_chl_vars_name=>$dummy){
                            // 代入値管理で登録されていないメンバー変数をリストアップ
                            if(@count($col_seq_list[$col_seq][$DB_chl_vars_name]) === 0){
                                // メンバー変数マスタに
                                if($err_chl_vars_name_list == ""){
                                    $err_chl_vars_name_list = $DB_chl_vars_name;
                                }
                                else{
                                    $err_chl_vars_name_list = $err_chl_vars_name_list . "/" . $DB_chl_vars_name;
                                }
                            }
                        }
                        if($err_chl_vars_name_list != ""){
                            // "ホスト(IP:｛｝)の配列変数(｛｝)の列順序(｛｝)のメンバー変数(｛｝)の具体値が未登録。";
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-70067",
                                                                       array($host_ip,$vars_name,$col_seq,$err_chl_vars_name_list));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $ret_code = false;
                        }
                    }
                }
            }
            break;
        }
        return $ret_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCで実行するPowerShellファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:       作業パターンID
    //    $ina_powershellfile:  PowerShellファイル返却配列
    //                          ina_powershell[管理Pkey]=>PowerShell素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscPowerShellFileData($in_pattern_id,&$ina_powershellfile){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_POWERSHELL_FILEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.POWERSHELL_FILE_ID, \n" . // ファイルID(PowerShell)
               "TBL_2.POWERSHELL_NAME, \n" .
               "TBL_2.POWERSHELL_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.POWERSHELL_FILE_ID \n" . // ファイルID(PowerShell)
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_POWERSHELL_FILE TBL_2 ON \n" .
               "      ( TBL_1.POWERSHELL_FILE_ID = TBL_2.POWERSHELL_FILE_ID); \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_powershellfile[管理Pkey]=>PowerShellファイルの配列作成
                if(strlen($row['POWERSHELL_FILE']) == 0){
                    // PowerShellファイルは必須ではないため、空白を設定
                    $ina_powershellfile[$row['POWERSHELL_FILE_ID']] = "";
                }
                else{
                    $ina_powershellfile[$row['POWERSHELL_FILE_ID']] = $row['POWERSHELL_FILE'];
                }
            }
            else{
                $ina_powershellfile[$row['POWERSHELL_FILE_ID']] = "";
            }
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCで実行するParamファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $ina_paramfile:        Paramファイル返却配列
    //                           ina_paramfile[管理Pkey]=>Param素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscParamFileData($in_pattern_id,&$ina_paramfile){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_PARAM_FILEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.PARAM_FILE_ID, \n" . // ファイルID(param)
               "TBL_2.PARAM_NAME, \n" .
               "TBL_2.PARAM_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.PARAM_FILE_ID \n" . // ファイルID(param)
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_PARAM_FILE TBL_2 ON \n" .
               "      ( TBL_1.PARAM_FILE_ID = TBL_2.PARAM_FILE_ID); \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_paramfile[管理Pkey]=>paramファイルの配列作成
                if(strlen($row['PARAM_FILE']) == 0){
                    // ファイルは必須ではないため、空白を設定
                    $ina_paramfile[$row['PARAM_FILE_ID']] = "";
                }
                else{
                    $ina_paramfile[$row['PARAM_FILE_ID']] = $row['PARAM_FILE'];
                }
            }
            else{
                $ina_paramfile[$row['PARAM_FILE_ID']] = "";
            }
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCで実行するImportファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $ina_importfile:      Importファイル返却配列
    //                          ina_importfile[管理Pkey]=>Import素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscImportFileData($in_pattern_id,&$ina_importfile){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_IMPORT_FILEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.IMPORT_FILE_ID, \n" . // ファイルID(Import)
               "TBL_2.IMPORT_NAME, \n" .
               "TBL_2.IMPORT_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.IMPORT_FILE_ID \n" . // ファイルID(Import)
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_IMPORT_FILE TBL_2 ON \n" .
               "      ( TBL_1.IMPORT_FILE_ID = TBL_2.IMPORT_FILE_ID); \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_importfile[管理Pkey]=>Importファイルの配列作成
                if(strlen($row['IMPORT_FILE']) == 0){
                    // ファイルは必須ではないため、空白を設定
                    $ina_importfile[$row['IMPORT_FILE_ID']] = "";
                }
                else{
                    $ina_importfile[$row['IMPORT_FILE_ID']] = $row['IMPORT_FILE'];
                }
            }
            else{
                $ina_importfile[$row['IMPORT_FILE_ID']] = "";
            }
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCで実行するコンフィグデータファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $ina_configdatafile:  コンフィグデータファイル返却配列
    //                          ina_configdatafile[管理Pkey]=>素材ファイル
    //    $ina_configdataName:  コンフィグデータ名返却配列
    //                          ina_configdataName[管理Pkey]=>コンフィグデータ名
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscConfigDataFileData($in_pattern_id,&$ina_configdatafile,&$ina_configdataName){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_CONFIGDATA_FILEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.CONFIGDATA_FILE_ID, \n" . // ファイルID(コンフィグデータ)
               "TBL_2.CONFIGDATA_NAME, \n" .
               "TBL_2.CONFIGDATA_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.CONFIGDATA_FILE_ID \n" . // ファイルID(コンフィグデータ)
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_CONFIGDATA_FILE TBL_2 ON \n" .
               "      ( TBL_1.CONFIGDATA_FILE_ID = TBL_2.CONFIGDATA_FILE_ID); \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_configdatafile[管理Pkey]=>リソースファイルの配列作成
                if(strlen($row['CONFIGDATA_FILE']) == 0){
                    // 必須ではないため、空白を設定
                    $ina_configdatafile[$row['CONFIGDATA_FILE_ID']] = "";
                    $ina_configdataName[$row['CONFIGDATA_FILE_ID']] = "";
                }
                else{
                    $ina_configdatafile[$row['CONFIGDATA_FILE_ID']] = $row['CONFIGDATA_FILE'];
                    $ina_configdataName[$row['CONFIGDATA_FILE_ID']] = $row['CONFIGDATA_NAME'];
                }
            }
            else{
                $ina_configdatafile[$row['CONFIGDATA_FILE_ID']] = "";
                $ina_configdataName[$row['CONFIGDATA_FILE_ID']] = "";
            }
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCで実行するコンパイルオプションファイルをデータベースより取得する。
    //
    //  パラメータ
    //    $in_pattern_id:        作業パターンID
    //    $ina_compoptionfile:   コンパイルオプションファイル返却配列
    //                           ina_cmpoptionfile[素材管理Pkey]=>素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscCmpOptionFileData($in_pattern_id,&$ina_cmpoptionfile){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        // B_DSC_CMPOPTION_FILEに対するDISUSE_FLAG = '0'の
        // 条件はSELECT文に入れない。
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.CMPOPTION_FILE_ID, \n" . // ファイルID
               "TBL_2.CMPOPTION_NAME, \n" .
               "TBL_2.CMPOPTION_FILE, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.CMPOPTION_FILE_ID \n" . // ファイルID
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_CMPOPTION_FILE TBL_2 ON \n" .
               "      ( TBL_1.CMPOPTION_FILE_ID = TBL_2.CMPOPTION_FILE_ID); \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $this->DebugLogPrint(basename(__FILE__),__LINE__,$sql);
            $this->DebugLogPrint(basename(__FILE__),__LINE__,"PATTERN_ID=>$in_pattern_id");
            $this->DebugLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){
            if($row['DISUSE_FLAG']=='0'){
                // $ina_resources[素材管理Pkey]=>リソースファイルの配列作成
                if(strlen($row['CMPOPTION_FILE']) == 0){
                    // ファイルは必須ではないため、空白を設定
                    $ina_cmpoptionfile[$row['CMPOPTION_FILE_ID']] = "";
                }
                else{
                    $ina_cmpoptionfile[$row['CMPOPTION_FILE_ID']] = $row['CMPOPTION_FILE'];
                }
            }
            else{
                $ina_cmpoptionfile[$row['CMPOPTION_FILE_ID']] = "";
            }
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCリソースファイル以外のファイルをDSC用ディレクトリに配置。
    //       PowerShellファイル
    //       Paramファイル
    //       Importファイル
    //       コンフィグデータファイル
    //       コンパイルオプションファイル
    //
    //
    //  パラメータ
    //    $ina_DscContents:   Contents領域
    //    $ina_hosts:         ホスト名(IP)配列
    //    $ina_hostinfolist:  機器一覧ホスト情報配列
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscOtherfiles($ina_DscContents,$ina_hosts,$ina_hostinfolist){
        // Powershell
        if($this->CopyPowershellfile($ina_DscContents) === false){
            return false;
        }

        // Param
        if($this->CopyParamfile($ina_DscContents) === false){
            return false;
        }

        // Import
        if($this->CopyImportfile($ina_DscContents) === false){
            return false;
        }

        // コンフィグデータ
        if($this->CopyConfigdatafile($ina_DscContents) === false){
            return false;
        }

        // コンパイルオプション
        if($this->CopyCmpoptionfile($ina_DscContents) === false){
            return false;
        }

        // 認証キーファイル
        if($this->CopyCertificatefile($ina_hosts,$ina_hostinfolist) === false){
            return false;
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    PowerShellファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          PowerShellファイル(素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_DscContents:   Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyPowershellfile($ina_DscContents){
        $lv_target_id   = $ina_DscContents[10];
        $lv_target_file = $ina_DscContents[11];
        $lv_target_type = 'pws';

        if(strlen($lv_target_file)!=0){
            // アップロードされているファイルのパスを取得
            $lv_target_dir = $this->getITA_powershell_Dir();
            $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

            // PowerShellファイルが存在しているか確認
            if( file_exists($src_file) === false ){
                // "システムで管理しているPowershellファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55276",array($lv_target_id,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            //-------------------------------------------------------------------
            // Copy先のパスを取得
            // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[pws-PKEY10桁-ファイル名]とする。
            //-------------------------------------------------------------------
            $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

            // ファイルをDSC用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                // "PowerShellファイルのコピーに失敗しました。(ファイル名:{})"
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55277",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    Paramファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          Paramファイル(素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_DscContents:   Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyParamfile($ina_DscContents){
        $lv_target_id   = $ina_DscContents[12];
        $lv_target_file = $ina_DscContents[13];
        $lv_target_type = 'prm';

        if(strlen($lv_target_file)!=0){
            // アップロードされているファイルのパスを取得
            $lv_target_dir = $this->getITA_param_Dir();
            $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

            // Paramファイルが存在しているか確認
            if( file_exists($src_file) === false ){
                // "システムで管理しているParamファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55278",array($lv_target_id,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            //-------------------------------------------------------------------
            // Copy先のパスを取得
            // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[prm-PKEY10桁-ファイル名]とする。
            //-------------------------------------------------------------------
            $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

            // リソースファイルをDSC用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                // "Paramファイルのコピーに失敗しました。(ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55279",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    Importファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          Importファイル(素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_DscContents:   Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyImportfile($ina_DscContents){
        $lv_target_id   = $ina_DscContents[14];
        $lv_target_file = $ina_DscContents[15];
        $lv_target_type = 'imp';

        if(strlen($lv_target_file)!=0){
            // アップロードされているファイルのパスを取得
            $lv_target_dir = $this->getITA_import_Dir();
            $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

            // Importファイルが存在しているか確認
            if( file_exists($src_file) === false ){
                // "システムで管理しているImportファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55280",array($lv_target_id,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            //-------------------------------------------------------------------
            // Copy先のパスを取得
            // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[imp-PKEY10桁-ファイル名]とする。
            //-------------------------------------------------------------------
            $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

            // ファイルをDSC用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                // "Importファイルのコピーに失敗しました。(ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55281",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    コンフィグデータファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          コンフィグデータファイル(素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_DscContents:  Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyConfigdatafile($ina_DscContents){
        $lv_target_id   = $ina_DscContents[16];
        $lv_target_file = $ina_DscContents[17];
        $lv_target_type = 'cnfd';

        if(strlen($lv_target_file)!=0){
            // アップロードされているファイルのパスを取得
            $lv_target_dir = $this->getITA_configdata_Dir();
            $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

            // コンフィグデータファイルが存在しているか確認
            if( file_exists($src_file) === false ){
                // "システムで管理しているConfigデータファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55282",array($lv_target_id,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            //-------------------------------------------------------------------
            // Copy先のパスを取得
            // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[cnfd-PKEY10桁-ファイル名]とする。
            //-------------------------------------------------------------------
            $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

            // ファイルをDSC用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                // "Configデータファイルのコピーに失敗しました。(ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55283",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    コンパイルオプションファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          コンパイルオプションファイル(素材ファイル)をDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_DscContents:  Contents領域
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyCmpoptionfile($ina_DscContents){
        $lv_target_id   = $ina_DscContents[19];
        $lv_target_file = $ina_DscContents[20];
        $lv_target_type = 'cmp';

        if(strlen($lv_target_file)!=0){
            // アップロードされているファイルのパスを取得
            $lv_target_dir = $this->getITA_cmpoption_Dir();
            $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

            // ファイルが存在しているか確認
            if( file_exists($src_file) === false ){
                // "システムで管理しているコンパイルオプションファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55284",array($lv_target_id,basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            //-------------------------------------------------------------------
            // Copy先のパスを取得
            // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[cmp-PKEY10桁-ファイル名]とする。
            //-------------------------------------------------------------------
            $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

            // ファイルをDSC用ディレクトリにコピーする。
            if( copy($src_file,$dst_file) === false ){
                // "コンパイルオプションファイルのコピーに失敗しました。(ファイル名:{})";
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55285",array(basename($src_file)));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    認証キーファイルをコピーする。（実処理）
    //    詳細：アップロードフォルダにアップロードされている
    //          認証キーファイルをDSC側の作業ディレクトリへ配置する
    //  パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //    $ina_hostinfolist:     機器一覧ホスト情報配列
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //////////////////////////////////////////////////////////////////////////////////
    function CopyCertificatefile($ina_hosts,$ina_hostinfolist){
        $lv_target_type = 'cer';

        foreach( $ina_hosts as $host_name ){
            if(strlen(trim($ina_hostinfolist[$host_name]['DSC_CERTIFICATE_FILE'])) != 0){
                $lv_target_file = trim($ina_hostinfolist[$host_name]['DSC_CERTIFICATE_FILE']);
                $lv_target_id = $ina_hostinfolist[$host_name]['SYSTEM_ID'];

                // アップロードされているファイルのパスを取得
                $lv_target_dir = $this->getITA_Certificate_Dir();
                $src_file = $this->getITA_other_file($lv_target_dir,$lv_target_id,$lv_target_file);

                // 認証キーファイルが存在しているか確認
                if( file_exists($src_file) === false ){
                    // "システムで管理している認証キーファイルが存在しません。(KEY_ID:{}  ファイル名:{})";
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55286",array($lv_target_id,basename($src_file)));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }

                //-------------------------------------------------------------------
                // Copy先のパスを取得
                // ファイル名は ドライバ区分+オケストレータID/作業実行番号/in/[cer-PKEY10桁-リソースファイル名]とする。
                //-------------------------------------------------------------------
                $dst_file = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);

                // アップロードされているファイルをDSC用ディレクトリにコピーする。
                if( copy($src_file,$dst_file) === false ){
                    // "認証キーファイルのコピーに失敗しました。(ファイル名:{})";
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55287",array(basename($src_file)));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    PowerShellファイル(素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_powershell_Dir:    PowerShellファイル(素材ファイル)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_powershell_Dir(){
        return($this->lv_ita_powershell_file_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    Paramファイル(素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_resources_Dir:    Paramファイル(素材ファイル)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_param_Dir(){
        return($this->lv_ita_param_file_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    Importファイル(素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_import_Dir:    Importファイル(素材ファイル)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_import_Dir(){
        return($this->lv_ita_import_file_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    コンフィグデータファイル(素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_configdata_Dir:    コンフィグデータファイル(素材ファイル)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_configdata_Dir(){
        return($this->lv_ita_configdata_file_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    コンパイルオプションファイル(素材ファイル)格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_cmpoption_Dir:    コンパイルオプションファイル(素材ファイル)格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_cmpoption_Dir(){
        return($this->lv_ita_cmpoption_file_Dir);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    認証キーファイル格納ディレクトリパスを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    lv_ita_certificate_file_Dir:    認証キーファイル格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_Certificate_Dir(){
        return($this->lv_ita_certificate_file_Dir);
    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    // 処理内容
    //  パラメータで指定されたリソースファイル以外のアップロードされているファイルパスを取得
    //  パラメータ
    //    $in_dir:        ファイル格納ディレクトリパス
    //    $in_key:        ファイルのPkey(データベース)
    //    $in_filename:   ファイル名
    //
    //  戻り値
    //    $file:          アップロードされているファイルパス名
    ////////////////////////////////////////////////////////////////////////////////
    function getITA_other_file($in_dir,$in_key,$in_filename){
        $intNumPadding = 10;
        $file = sprintf(self::LC_ITA_OTHER_FILES_DIR_MK,
                        $in_dir,
                        str_pad( $in_key, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // 処理内容
    //  パラメータで指定されたリソースファイル以外の配置先のファイルパスを取得する
    //
    //  パラメータ
    //    $in_type:       'pws'：PowerShellファイル
    //                    'prm'：Paramファイル
    //                    'imp'：Importファイル
    //                    'cnfd’：コンフィグデータファイル
    //                    'cmp'：コンパイルオプションファイル
    //    $in_pkey:       管理 Pkey
    //    $in_filename:   ファイル名
    //
    //  戻り値
    //    $file :          配置先のファイルパス(DSC)
    //
    //  備考
    //    
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_dist_other_file($in_type,$in_pkey,$in_filename){
        $intNumPadding = 10;
        $in_dir=$this->getDsc_in_Dir();

        $file = sprintf(self::LC_DSC_COPY_ATHOER_FILE_MK,
                        $in_dir,
                        $in_type,
                        str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                        $in_filename);
        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    配置した全てのファイルをコンフィグファイルとしてマージする
    //
    //    パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //    $ina_host_vars:        ホスト変数配列
    //    $ina_hostprotcollist:  ホスト毎プロトコル一覧
    //    $ina_resources:        リソースファイル配列
    //    $ina_DscContents:      Contents領域
    //    $in_config_pass:       コンフィグファイル名
    //
    //    戻り値
    //      true:   正常
    //      false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscConfigFileMerge($ina_hosts, $ina_host_vars, $ina_hostinfolist, $ina_resources, $ina_DscContents, $in_config_pass){
        // ホスト数分のコンフィグファイルを出力
        // ホスト数分ループ
        foreach( $ina_hosts as $host_name ){
            $host_ip          = $ina_hostinfolist[$host_name]['IP_ADDRESS'];

            // コンフィグファイルをノード単位で出力
            // [IPアドレス]_コンフィグファイル名で出力
            $in_dir=$this->getDsc_in_Dir();
            $config_pass = $in_dir . '/' . $host_ip . '_' . $in_config_pass;
            $config_name = $ina_DscContents[9];

            // PowerShellファイル
            $lv_target_file = $ina_DscContents[11];
            if(strlen($lv_target_file)!=0){
                $lv_target_id   = $ina_DscContents[10];
                $lv_target_type = 'pws';
                $strFiledata = $this->ReplaseOtherFile($lv_target_type, $lv_target_id, $lv_target_file, $host_name, $ina_host_vars);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }

            //  Configurationブロックの冒頭Config宣言行を出力
            $strConfigDeclar = "Configuration  " . $config_name . "\n" . " {  " . "\n";
            if( file_put_contents( $config_pass , $strConfigDeclar, FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // Paramファイル
            $lv_target_file = $ina_DscContents[13];
            if(strlen($lv_target_file)!=0){
                $lv_target_id   = $ina_DscContents[12];
                $lv_target_type = 'prm';
                $strFiledata = $this->ReplaseOtherFile($lv_target_type, $lv_target_id, $lv_target_file, $host_name, $ina_host_vars);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    $result_code = false;
                }
            }

            // ITAで出力するデフォルトのImportリソース
            $strModuleName = 'PSDesiredStateConfiguration';
            $strDSCResourcModule = "    Import-DscResource -ModuleName " . $strModuleName . "\n" ;
            if( file_put_contents( $config_pass , $strDSCResourcModule, FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // Importファイルの内容を出力
            $lv_target_file = $ina_DscContents[15];
            if(strlen($lv_target_file)!=0){
                $lv_target_id   = $ina_DscContents[14];
                $lv_target_type = 'imp';
                $strFiledata = $this->ReplaseOtherFile($lv_target_type, $lv_target_id, $lv_target_file, $host_name, $ina_host_vars);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }

            $strNode = '    Node $AllNodes.Where{$_.NodeName -eq "' . 
                       $host_ip . 
                       '"}.NodeName' . 
                       "\n" .
                       '    {' . 
                       "\n";
            if( file_put_contents( $config_pass , $strNode , FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // リソース数分ループ
            foreach( $ina_resources as $no=>$resource_list ){
                foreach( $resource_list as $resourcepkey=>$resource ){
                    // リソースファイルのバス（データリレイストレージ）を取得
                    $org_file_name = $this->getDsc_resource_filename($resourcepkey,$resource);
                    $copy_file_name = $org_file_name . '_' . $host_ip;

                    // リソースファイルの書き込み
                    $strFiledata = "";
                    $strFiledata = file_get_contents($copy_file_name);
                    if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }

                    $strFiledata    = "\n";
                    if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                }
            }

            // Thumbprintを設定している場合、ローカルコンフィグマネージャの設定を追加
            $host_Thumbprint  = $ina_hostinfolist[$host_name]['DSC_CERTIFICATE_THUMBPRINT'];
            $lv_target_file = $ina_DscContents[17];

            if( ( strlen($host_Thumbprint) !== 0 ) || ( strlen($lv_target_file) !== 0 ) ) {
                $LCMWrite = false;
                if( strlen($host_Thumbprint) !== 0 ) {

                    $strLocalConfigurationManager = "        LocalConfigurationManager\n" .
                                                    "        {\n" ;

                    $strLocalConfigurationManager = $strLocalConfigurationManager .
                                                    '            CertificateId =  $Node.ThumbPrint' . "\n";
                    $LCMWrite = true;
                }

                // 再起動のON/OFFを指定
                if( strlen($lv_target_file) != 0 ) {
                    $lv_target_id   = $ina_DscContents[16];
                    $lv_target_type = 'cnfd';
                    $get_file       = $this->getDsc_dist_other_file($lv_target_type, $lv_target_id, $lv_target_file);
                    $strFiledata    = file_get_contents($get_file);
                    if( strpos( $strFiledata, "RebootNodeIfNeeded" ) !== false ) {
                        if( $LCMWrite === false ) {
                            $strLocalConfigurationManager = "        LocalConfigurationManager\n" .
                                                            "        {\n" ;
                        }
                        $strLocalConfigurationManager = $strLocalConfigurationManager .
                                                        '            RebootNodeIfNeeded =  $Node.RebootNodeIfNeeded' . "\n";
                        $LCMWrite = true;
                    }
                }

                if( $LCMWrite === true ) {
                    $strLocalConfigurationManager = $strLocalConfigurationManager .
                                                    "        }\n";
                    if( file_put_contents( $config_pass , $strLocalConfigurationManager , FILE_APPEND | LOCK_EX ) === false ){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        return false;
                    }
                }
            }

            $strNode = "    }\n\n" . 
                       "}\n\n";
            if( file_put_contents( $config_pass , $strNode , FILE_APPEND | LOCK_EX ) === false ) {
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // コンフィグデータ作成処理
            // コンフィグデータファイル名取得
            $lv_target_file = $ina_DscContents[17];
            if(strlen($lv_target_file)!=0){
                $lv_configdata_name = $ina_DscContents[18];
            }
            else{
                $lv_configdata_name = "ITAConfigData";
            }

            // ITAで生成するデフォルトのコンフィグデータ作成
            $strConfigData = "\$" . $lv_configdata_name . " = \n" . "@{\n" .
                             "    AllNodes = \n" . '    @(' ."\n" .
                             "        @{\n" .
                             "            NodeName = \"" . $host_ip . "\"\n";
            if( file_put_contents( $config_pass , $strConfigData , FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            $host_system_id   = $ina_hostinfolist[$host_name]['SYSTEM_ID'];
            $host_Certificate = $ina_hostinfolist[$host_name]['DSC_CERTIFICATE_FILE'];

            // 認証キーファイルが設定されているか？
            if( strlen($host_Certificate) != 0 ){
                $lv_target_certificate = $host_Certificate;
                $lv_target_id = $host_system_id;
                $certificate_file = $this->getDsc_certificatefilepath_windows($lv_target_id,$lv_target_certificate);

                $strConfigData    = "            PSDscAllowPlainTextPassword = \$false\n" .
                                    "            CertificateFile = \"" . $certificate_file . "\"\n" .
                                    "            Thumbprint = \"" . $host_Thumbprint . "\"\n";
            }
            else{
                $strConfigData    = "            PSDscAllowPlainTextPassword = \$true\n";
            }
            if( file_put_contents( $config_pass , $strConfigData , FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // 資格情報設定
            $host_vars_dir = $this->getDsc_host_vars_Dir();
            $credential_file_name = $host_vars_dir . '/' . DF_HOST_CDT_HED . $host_ip;
            if( file_exists($credential_file_name) === true ){
                $strFiledata    = '';
                $strFiledata  = file_get_contents($credential_file_name);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }

            // コンフィグデータファイルの内容設定
            if(strlen($lv_target_file)!=0){
                $lv_target_id   = $ina_DscContents[16];
                $lv_target_type = 'cnfd';
                $strFiledata = $this->ReplaseOtherFile($lv_target_type, $lv_target_id, $lv_target_file, $host_name, $ina_host_vars);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
                $strConfigData    = "            \n";
                if( file_put_contents( $config_pass , $strConfigData , FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
            $strConfigData    = "        }\n" .
                                '    )'. "\n" . "}\n" . "\n" ;
            if( file_put_contents( $config_pass , $strConfigData , FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // ITAで設定するデフォルトのコンパイルオプション
            $strCmpOption    = $config_name . " -ConfigurationData " . '$' . $lv_configdata_name ;
            if( file_put_contents( $config_pass , $strCmpOption , FILE_APPEND | LOCK_EX ) === false ){
                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                return false;
            }

            // コンパイルオプションファイル
            $lv_target_file = $ina_DscContents[20];
            if(strlen($lv_target_file)!=0){
                $strCmpOption    = " `\n";
                if( file_put_contents( $config_pass , $strCmpOption , FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }

                $lv_target_id   = $ina_DscContents[19];
                $lv_target_type = 'cmp';
                $strFiledata = $this->ReplaseOtherFile($lv_target_type, $lv_target_id, $lv_target_file, $host_name, $ina_host_vars);
                if( file_put_contents( $config_pass , $strFiledata, FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
            else{
                $strCmpOption    = " \n";
                if( file_put_contents( $config_pass , $strCmpOption , FILE_APPEND | LOCK_EX ) === false ){
                    $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55232",array($in_config_pass));
                    $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    return false;
                }
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //   ホスト数分リソースファイルを作成し、リソースファイルに定義されている変数を
    //   具体値に置換する
    //   [DSC側inディレクトリ]/[リソースファイル名]_[ホスト名(IPアドレス)]の形式でホスト数分ファイルを作成
    //
    //  パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //                           [管理システム項番]=[ホスト名(IP)]
    //
    //    $ina_host_vars:        ホスト変数配列
    //                           [ホスト名(IP)][ 変数名 ]=>具体値
    //
    //    $ina_resources:  リソースファイル配列
    //                           [INCLUDE順序][素材管理Pkey]=>素材ファイル
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //  備考  
    //  
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscResourcefilesHostnum($ina_hosts,
                                           $ina_host_vars,
                                           $ina_resources){
        $result_code = true;

        // hogeファイル配列のホスト分繰返し
        foreach( $ina_hosts as $no=>$host_name ){
            foreach( $ina_resources as $no=>$resource_list ){
                // リソースファイル毎にコピー＆変数置換
                foreach( $resource_list as $resourcepkey=>$resource ){
                    // リソースファイルのバス（データリレイストレージ）を取得
                    $org_file_name = $this->getDsc_resource_filename($resourcepkey,$resource);
                    $copy_file_name = $org_file_name . '_' . $host_name;

                    ///////////////////////////////////////////////////////////////////
                    // リソースファイルで使用している変数がホストの変数に登録されているか判定
                    ///////////////////////////////////////////////////////////////////
                    // リソースファイルに登録されている変数を抜出す。
                    $dataString = file_get_contents($org_file_name);

                    // ローカル変数のリスト作成
                    $local_vars[] = array();
                    $local_vars[] = self::LC_DSC_PROTOCOL_VAR_NAME;
                    $local_vars[] = self::LC_DSC_USERNAME_VAR_NAME;
                    $local_vars[] = self::LC_DSC_PASSWD_VAR_NAME;
                    $local_vars[] = self::LC_DSC_LOGINHOST_VAR_NAME;

                    // ユーザー公開用 データリレイストレージパス 変数の名前
                    $local_vars[] = self::LC_DSC_OUTDIR_VAR_NAME;
                    // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
                    $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

                    $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

                    $aryResultParse = $objWSRA->getParsedResult();


                    $file_vars_list = $aryResultParse[1];

                    // リソースファイル（Config素材）で変数が使用されているか判定
                    if(count($file_vars_list) == 0){
                        // リソースファイル（Config素材）で変数が使用されていない場合は以降のチェックをスキップ移行
                        unset($objWSRA);
                        if(file_put_contents( $copy_file_name,$dataString) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55217",
                                                                    array($host_name,$resource));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                        }
                        continue;
                    }

                    // 該当ホストのホスト変数が登録されているか判定
                    if(array_key_exists($host_name,$ina_host_vars)===false){
                        $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55215",
                                                           array($host_name));
                        $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        $result_code = false;
                        // ホスト変数が登録されていないので以降のチェックをスキップ
                        unset($objWSRA);
                        if(file_put_contents( $copy_file_name,$dataString) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55217",
                                                                    array($host_name,$resource));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                        }
                        continue;
                    }

                    //該当ホストの変数配列を取得
                    $varSetTo = array();
                    $vars_list = $ina_host_vars[$host_name];

                    //-------------------------------------------------------------------
                    // $vars_list[ 変数名 ]=>具体値
                    //-------------------------------------------------------------------
                    foreach( $file_vars_list as $var_name ){
                        // リソースファイル（Config素材）で使用している変数がホストの変数に登録されているか判定
                        if(array_key_exists($var_name,$vars_list)===false){
                            if($var_name == self::LC_DSC_PROTOCOL_VAR_NAME){
                                // "対話ファイル (｛｝)で使用している変数(｛｝)は管理対象システム一覧に登録されているホスト(IP:｛｝)のプロトコルが未入力なので使用できません。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56209",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_USERNAME_VAR_NAME){
                                // "対話ファイル (｛｝)で使用している変数(｛｝)は管理対象システム一覧に登録されているホスト(IP:｛｝)のログインユーザIDが未入力なので使用できません。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56207",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_PASSWD_VAR_NAME){
                                // "対話ファイル (｛｝)で使用している変数(｛｝)は管理対象システム一覧に登録されているホスト(IP:｛｝)のログインパスワードが未入力なので使用できません。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56208",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            elseif($var_name == self::LC_DSC_LOGINHOST_VAR_NAME){
                                // "対話ファイル (｛｝)で使用している変数(｛｝)は管理対象システム一覧に登録されているホスト(IP:｛｝)のホスト名が未入力なので使用できません。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56206",
                                                                               array($resource,
                                                                               $var_name,
                                                                               $host_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            else{
                                // "ホスト(IP:｛｝)の対話ファイル(｛｝)で使用している変数(｛｝)がホストの変数に未登録。";
                                $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55216",
                                                                               array($host_name,
                                                                               $resource,
                                                                               $var_name));
                                $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            }
                            $result_code = false;
                            // 未登録でも処理は続行する。
                        }
                        else{
                            // 変数を置換える具体値を設定
                            $varSetTo[$var_name]=$vars_list[$var_name];
                        }
                    }

                    if(count($varSetTo) != 0){
                        //-------------------------------------------------------------------
                        // 変数を具体値で置換える
                        //-------------------------------------------------------------------
                        $objWSRA->stringReplace($dataString,$varSetTo);
                        $ReplaceSourceString= $objWSRA->getReplacedString();

                        //-------------------------------------------------------------------
                        // NODE名称を置き換える  " __NODE__ " ==> host_name
                        //-------------------------------------------------------------------
                        $Node_name = self::LC_NODE_NAME ;
                        $objWSRA->NodeReplace($ReplaceSourceString,$Node_name,$host_name);
                        $ReplaceSourceString= $objWSRA->getReplacedString();

                        //-------------------------------------------------------------------
                        // ファイル書き込み
                        //-------------------------------------------------------------------
                        $ReplaceSourceString= $objWSRA->getReplacedString();
                        if(file_put_contents( $copy_file_name,$objWSRA->getReplacedString()) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55217",
                                                                    array($host_name,$resource));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                        }
                    }
                    else{
                        if(file_put_contents( $copy_file_name,$dataString) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55217",
                                                                    array($host_name,$resource));
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
    //
    //  処理内容
    //    DSCサーバ側から見た作業用ディレクトリ(in)を設定
    //
    //  パラメータ
    //    $in_dir:     作業用ディレクトリ(in)
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDsc_in_Dir_windows($in_indir){
        $this->lv_dsc_in_dir_windows = $this->getDsc_storage_path() . $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCサーバ側から見た作業用ディレクトリ(in)を取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    $lv_ita_resources_Dir:    作業用ディレクトリ(in)ディレクトリ
    //
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_in_Dir_windows(){
        return($this->lv_dsc_in_dir_windows);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // 処理内容
    //  DSCサーバ側から見た認証キーファイルパスを取得する
    //
    //  パラメータ
    //    $in_pkey:           管理 Pkey
    //    $in_filename:       認証キーファイル
    //
    //  戻り値
    //    $file:              認証キーファイルパスフルパス(DSC)
    //
    //  備考
    //    
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_certificatefilepath_windows($in_pkey,$in_filename){
        $intNumPadding = 10;
        $in_type = 'cer';

        $file = $this->getDsc_in_Dir_windows() . "\\" .
                $in_type . "-" .
                str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ) . "-" .
                $in_filename;

        return($file);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    パスワードファイルディレクトリを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    pw_path:    パスワードファイル格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_pw_path(){
        $pw_path = $this->getDsc_host_vars_Dir() . "/" .
                   self::LC_DSC_PW_DIR;
    
        return($pw_path);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCサーバ側から見たパスワードファイルディレクトリを取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    $pw_path:    パスワードファイル格納ディレクトリ
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_pw_path_windows(){
        $pw_path = $this->getDsc_in_Dir_windows() . "\\" .
                   self::LC_DSC_HOST_VARS_DIR . "\\" .
                   self::LC_DSC_PW_DIR;
    
        return($pw_path);
    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCサーバ データストレージパス(DB情報）を設定
    //
    //  パラメータ
    //    $in_dir:       DSCサーバ データストレージパス
    //
    //  戻り値
    //    なし
    ////////////////////////////////////////////////////////////////////////////////
    function setDsc_storage_path($in_indir){
        $this->lv_dsc_storage_path = $in_indir;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    DSCサーバ データストレージパス(DB情報）を取得
    //  パラメータ
    //    なし
    //
    //  戻り値
    //    $lv_dsc_storage_path:     DSCサーバ データストレージパス
    ////////////////////////////////////////////////////////////////////////////////
    function getDsc_storage_path(){
        return($this->lv_dsc_storage_path);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //   リソースファイルに定義されている資格情報変数を検索し、具体値に置換する
    //   [DSC側inディレクトリ]/host_vars/CDT_[IPアドレス]の形式でホスト数分ファイルを作成
    //
    //  パラメータ
    //    $ina_hosts:        ホスト名(IP)配列
    //    $ina_hostinfolist: 機器一覧ホスト情報配列
    //    $ina_resources:    リソースファイル配列
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //  備考  
    //  
    ////////////////////////////////////////////////////////////////////////////////
    function CreateDscCredential($ina_hosts,
                                 $ina_hostinfolist,
                                 $ina_resources){
        $result_code = true;

        // hogeファイル配列のホスト分繰返し
        foreach( $ina_hosts as $no=>$host_name ){
            $host_ip   = $ina_hostinfolist[$host_name]['IP_ADDRESS'];
            $system_id = $ina_hostinfolist[$host_name]['SYSTEM_ID'];

            foreach( $ina_resources as $no=>$resource_list ){
                // リソースファイル名取得＆資格情報変数置換
                foreach( $resource_list as $resourcepkey=>$resource ){
                    // リソースファイルのバス（データリレイストレージ）を取得
                    $org_file_name = $this->getDsc_resource_filename($resourcepkey,$resource);
                    $tmp_file_name = $org_file_name . '_' . $host_ip;

                    ///////////////////////////////////////////////////////////////////
                    // リソースファイルで使用している変数がホストの変数に登録されているか判定
                    ///////////////////////////////////////////////////////////////////
                    // リソースファイルに登録されている変数を抜出す。
                    $dataString = file_get_contents($tmp_file_name);

                    // ローカル変数のリスト作成
                    $local_cdts[] = array();

                    $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_CDT_HED,$dataString,$local_cdts);

                    $aryResultParse = $objWSRA->getParsedResult();
                    $file_cdts_list = $aryResultParse[1];

                    // リソースファイル（Config素材）で資格情報変数が使用されているか判定
                    if(count($file_cdts_list) == 0){
                        // リソースファイル（Config素材）に資格情報変数が使用されていない場合は以降のチェックをスキップ
                        unset($objWSRA);
                        continue;
                    }

                    //該当ホストの資格情報変数配列を取得
                    $cdtSetTo = array();
                    $credential_info = array();

                    //-------------------------------------------------------------------
                    // $vars_list[ 変数名 ]=>具体値
                    //-------------------------------------------------------------------
                    foreach( $file_cdts_list as $cdt_name ){
                        // 具体値の設定（Config素材）
                        // CDT_XXXX の場合、$Node.XXXを具体値とする
                        $node_rdt_name = str_replace(DF_HOST_CDT_HED, "", $cdt_name);
                        $varSetTo[$cdt_name]= '$Node.' . $node_rdt_name;

                        //-------------------------------------------------------------------
                        // 変数を具体値で置換える
                        //-------------------------------------------------------------------
                        $objWSRA->stringReplace($dataString,$varSetTo);
                        $ReplaceSourceString= $objWSRA->getReplacedString();

                        //-------------------------------------------------------------------
                        // コンフィグファイル書き込み
                        //-------------------------------------------------------------------
                        $ReplaceSourceString= $objWSRA->getReplacedString();
                        if(file_put_contents( $tmp_file_name,$objWSRA->getReplacedString()) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55288",
                                                                    array($host_name,$resource));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                        }

                        // 該当ホストの資格情報変数が登録されているか判定
                        $ret = $this->getDBDscCredentialInfo($system_id, $cdt_name, $credential_info);
                        if($ret <> true){
                            // 例外処理へ
                            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                            require ($in_root_dir_path . $in_log_output_php );
                            unset($objWSRA);
                            return false;
                        }

                        $db_cdtid    = $credential_info['CREDENTIAL_ID'];
                        $db_cdt_user = $credential_info['CREDENTIAL_USER'];
                        $db_cdt_pw   = $credential_info['CREDENTIAL_PW'];
                        $db_cdt_name = $credential_info['CREDENTIAL_VARS_NAME'];

                        if( strlen($db_cdt_name) == 0 ){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55275",array($resource,$host_name,$cdt_name));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                            continue;
                        }

                        //-------------------------------------------------------------------
                        // パスワードファイルに出力
                        // in/host_vars/pw/[CREDENTIAL_ID]ファイルにパスワードを記述
                        //-------------------------------------------------------------------
                        $intNumPadding = 10;
                        $pw_dir = $this->getDsc_pw_path();
                        $pw_file_name = $pw_dir . "/" . str_pad( $db_cdtid, $intNumPadding, "0", STR_PAD_LEFT );
                        
                        // 既にパスワードファイルがある場合は、スキップ
                        if( file_exists($pw_file_name) === true ){
                            continue;
                        }
                        if( file_put_contents( $pw_file_name , $db_cdt_pw, FILE_APPEND | LOCK_EX ) === false){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55289",
                                                                    array($host_name,$resource));
                            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            $result_code = false;
                        }

                        //-------------------------------------------------------------------
                        // コンフィグデータ書き込み用データ出力
                        // in/host_vars/配下にCDT_[IPアドレス]で追加書き込み
                        //-------------------------------------------------------------------
                        $pw_dir_win = $this->getDsc_pw_path_windows();
                        $pw_file_name_win = $pw_dir_win . "\\" . str_pad( $db_cdtid, $intNumPadding, "0", STR_PAD_LEFT );

                        $host_vars_dir = $this->getDsc_host_vars_Dir();
                        $file_name = $host_vars_dir . '/' . DF_HOST_CDT_HED . $host_ip;
                        $strDSCCredential = "            " . $node_rdt_name . " = " .
                                            "New-Object System.Management.Automation.PSCredential (" .
                                            "\"" . $db_cdt_user . "\", " .
                                            "(Get-Content " .
                                            $pw_file_name_win .
                                            " | ConvertTo-SecureString -AsPlainText -Force)) \n";
                        if( file_put_contents( $file_name , $strDSCCredential, FILE_APPEND | LOCK_EX ) === false ){
                            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-55288",
                                                                    array($host_name,$resource));
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
    //
    //  処理内容
    //    パラメータで指定されたホストID、資格情報埋込変数の資格情報を
    //    データベースより取得する
    //
    //  パラメータ
    //    $in_systemid:        ホストID
    //    $in_cdt_name:        資格情報埋込変数名
    //    $ina_credentialinf:  資格情報
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBDscCredentialInfo($in_systemid,$in_cdt_name,&$ina_credentialinfo){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $sql = "SELECT \n" .
               "  TBL_1.CREDENTIAL_ID, \n" .
               "  TBL_1.CREDENTIAL_USER, \n" .
               "  TBL_1.CREDENTIAL_PW, \n" .
               "  TBL_1.CREDENTIAL_VARS_NAME, \n" .
               "  TBL_1.SYSTEM_ID, \n" .
               "  TBL_1.DISUSE_FLAG \n" .
               "FROM \n" .
               "  B_DSC_CREDENTIAL TBL_1 \n" .
               "WHERE \n" .
               "  TBL_1.CREDENTIAL_VARS_NAME  = :CREDENTIAL_VARS_NAME AND \n" .
               "  TBL_1.SYSTEM_ID  = :SYSTEM_ID; \n";

        $objQuery = $this->lv_objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            return false;
        }
        $objQuery->sqlBind( array('SYSTEM_ID'=>$in_systemid));
        $objQuery->sqlBind( array('CREDENTIAL_VARS_NAME'=>$in_cdt_name));

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56100",array(basename(__FILE__),__LINE__));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            unset($objQuery);
            return false;
        }

        $ina_credentialinfo['CREDENTIAL_ID']        = "";
        $ina_credentialinfo['CREDENTIAL_USER']      = "";
        $ina_credentialinfo['CREDENTIAL_PW']        = "";
        $ina_credentialinfo['CREDENTIAL_VARS_NAME'] = "";

        while ( $row = $objQuery->resultFetch() ){
            $credential_id        = '';
            $credential_user      = '';
            $credential_pw        = '';
            $credential_vars_name = '';

            $credential_id        = $row['CREDENTIAL_ID'];
            $credential_user      = $row['CREDENTIAL_USER'];
            $credential_pw        = ky_decrypt($row['CREDENTIAL_PW']);
            $credential_vars_name = $row['CREDENTIAL_VARS_NAME'];

            if($row['DISUSE_FLAG']=='0'){
                if(strlen($row['CREDENTIAL_VARS_NAME']) != 0){
                    $ina_credentialinfo['CREDENTIAL_ID']        = $credential_id;
                    $ina_credentialinfo['CREDENTIAL_USER']      = $credential_user;
                    $ina_credentialinfo['CREDENTIAL_PW']        = $credential_pw;
                    $ina_credentialinfo['CREDENTIAL_VARS_NAME'] = $credential_vars_name;
                    // 1件見つかれば、ループを抜ける
                    break;
                }
            }
        }

        $fetch_counter = $objQuery->effectedRowCount();
        if ($fetch_counter < 1){
            $msgstr = $this->lv_objMTS->getSomeMessage("ITADSCH-ERR-56120",array($in_systemid,$in_cdt_name));
            $this->LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }

        // DBアクセス事後処理
        unset($objQuery);
        return true;
    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    パラメータで指定されたファイルで使用しているホスト変数を具体値に変換する
    //
    //  パラメータ
    //    $in_type:       'pws'：PowerShellファイル
    //                    'prm'：Paramファイル
    //                    'imp'：Importファイル
    //                    'cnfd’：コンフィグデータファイル
    //                    'cmp'：コンパイルオプションファイル
    //    $in_pkey:       管理 Pkey
    //    $in_filename:   ファイル名
    //    $in_host_name:  ホスト名(IP)
    //    $ina_host_vars: ホスト変数配列
    //                    [ホスト名(IP)][ 変数名 ]=>具体値
    //
    //  戻り値
    //    strFiledata:   具体値に変換したデータ
    //
    ////////////////////////////////////////////////////////////////////////////////
    function ReplaseOtherFile($in_target_type, $in_target_pkey, $in_target_file, $in_host_name, $ina_host_vars){
        $strFiledata    = '';

        if(strlen($in_target_file)!=0){
            $get_file       = $this->getDsc_dist_other_file($in_target_type, $in_target_pkey, $in_target_file);
            $strFiledata    = file_get_contents($get_file);

            // 定義されている変数を具体値に置換
            $local_vars[] = array();
            $local_vars[] = self::LC_DSC_PROTOCOL_VAR_NAME;
            $local_vars[] = self::LC_DSC_USERNAME_VAR_NAME;
            $local_vars[] = self::LC_DSC_PASSWD_VAR_NAME;
            $local_vars[] = self::LC_DSC_LOGINHOST_VAR_NAME;    

            // ユーザー公開用 データリレイストレージパス 変数の名前
            $local_vars[] = self::LC_DSC_OUTDIR_VAR_NAME;
            // ユーザー公開用 symphonyインスタンス作業用データリレイストレージパス 変数の名前
            $local_vars[] = self::LC_SYMPHONY_DIR_VAR_NAME;

            $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$strFiledata,$local_vars);
            $aryResultParse = $objWSRA->getParsedResult();
            $file_vars_list = $aryResultParse[1];

            if(count($file_vars_list) != 0){
                if(array_key_exists($in_host_name,$ina_host_vars)===true){
                    $varSetTo = array();
                    $vars_list = $ina_host_vars[$in_host_name];

                    foreach( $file_vars_list as $var_name ){
                        if(array_key_exists($var_name,$vars_list)===true){
                            $varSetTo[$var_name]=$vars_list[$var_name];
                        }
                    }
                    if(count($varSetTo) != 0){
                        $objWSRA->stringReplace($strFiledata,$varSetTo);
                        $strFiledata = $objWSRA->getReplacedString();
                    }
                }
            }
            unset($objWSRA);
        }
        return($strFiledata);
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    テンポラリファイルを削除する
    //
    //    パラメータ
    //    $ina_hosts:            ホスト名(IP)配列
    //    $ina_hostinfolist:     機器一覧ホスト情報配列
    //    $ina_resources:        リソースファイル配列
    //    $ina_DscContents:      Contents領域
    //
    //    戻り値
    //      true:   正常
    //      false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function DelteTemporaryFile($ina_hosts, $ina_hostinfolist, $ina_resources, $ina_DscContents) {

        $error_flag = true;

        // ホスト数分ループ
        foreach( $ina_hosts as $host_name ) {
            $host_ip          = $ina_hostinfolist[$host_name]['IP_ADDRESS'];
            $in_dir = $this->getDsc_in_Dir();

            // リソース数分ループ
            foreach( $ina_resources as $no=>$resource_list ) {
                foreach( $resource_list as $resourcepkey=>$resource ) {
                    // リソースファイルのバス（データリレイストレージ）を取得
                    $org_file_name = $this->getDsc_resource_filename($resourcepkey,$resource);
                    $delete_file_name = $org_file_name . '_' . $host_ip;

                    if(!unlink($delete_file_name)) {
                        $error_flag = false;
                    }
                }
            }

            // 資格情報設定ファイル削除
            $host_vars_dir = $this->getDsc_host_vars_Dir();
            $delete_file_name = $host_vars_dir . '/' . DF_HOST_CDT_HED . $host_ip;
            if( file_exists($delete_file_name) === true ) {
                if(!unlink($delete_file_name)) {
                    // 異常フラグON
                    $error_flag = false;
                }
            }
        }

        // PowerShellファイル削除
        $lv_target_file = $ina_DscContents[11];
        if( strlen($lv_target_file) != 0 ) {
            $lv_target_id   = $ina_DscContents[10];
            $lv_target_type = 'pws';
            $delete_file_name = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);
            if(!unlink($delete_file_name)) {
                $error_flag = false;
            }
        }

        // Paramファイル削除
        $lv_target_file = $ina_DscContents[13];
        if( strlen($lv_target_file) != 0 ) {
            $lv_target_id   = $ina_DscContents[12];
            $lv_target_type = 'prm';
            $delete_file_name = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);
            if(!unlink($delete_file_name)) {
                $error_flag = false;
            }
        }

        // Importファイル削除
        $lv_target_file = $ina_DscContents[15];
        if( strlen($lv_target_file) != 0 ) {
            $lv_target_id   = $ina_DscContents[14];
            $lv_target_type = 'imp';
            $delete_file_name = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);
            if(!unlink($delete_file_name)) {
                $error_flag = false;
            }
        }

        // コンフィグデータファイル削除
        $lv_target_file = $ina_DscContents[17];
        if( strlen($lv_target_file) != 0 ) {
            $lv_target_id   = $ina_DscContents[16];
            $lv_target_type = 'cnfd';
            $delete_file_name = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);
            if(!unlink($delete_file_name)) {
                $error_flag = false;
            }
        }

        // コンパイルオプションファイル削除
        $lv_target_file = $ina_DscContents[20];
        if( strlen($lv_target_file) != 0 ) {
            $lv_target_id   = $ina_DscContents[19];
            $lv_target_type = 'cmp';
            $delete_file_name = $this->getDsc_dist_other_file($lv_target_type,$lv_target_id,$lv_target_file);
            if(!unlink($delete_file_name)) {
                $error_flag = false;
            }
        }
        return $error_flag;
    }

}
?>
