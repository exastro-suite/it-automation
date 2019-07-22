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
    //      Windows PowerShell DSC リソースファイル（Config素材）・変数自動取得
    //
    // 主要連想配列
    //
    // T0002
    // aryMatterFilePerMatterId       Config素材管理マスタ
    //                                [Pkey] = リソースファイル
    // T0003
    // aryMattersPerPattern           作業パターン詳細マスタベース
    //                                [パターンID][array(Config素材 Pkey)]
    // T0004
    // aryVarNameIdsPerPattern        作業パターン毎 変数一覧    中間データ
    //                                $aryMattersPerPatternをパターンID毎の変数一覧にまとめる
    //                                [パターンID][array([変数マスタPkey]=1)]
    // T0005
    // aryVarsPerMatterId             Configファイル毎の変数一覧  素材マスタベース
    //                                [Pkey][変数名]=1
    // T0006
    // aryVarIdPerVarNameFromFiles    変数一覧(一意)             中間データ
    //                                aryVarsPerMatterIdを元にした変数一覧(一意)
    //                                [変数名(一意)]=変数マスタPkey (初期値 NULL)
    // T0007
    // aryRowFromDscVarsTable         変数マスタ
    //                                [変数名][変数マスタの各情報]
    // T0008
    // aryRowsPerPatternFromDscPatternVarsLink
    //                                作業パターン変数紐付マスタ
    //                                [パターンID][変数ID] = [作業パターン変数紐付の各情報]
    // T0009
    // aryVarIdPerVarNameFromFiles_fix  T0006と同様
    //                                素材で使用している変数を判定するのに使用
    //
    // 各テーブル
    // 変数マスタテーブル      B_DSC_VARS_MASTER
    // 作業パターン詳細テーブル B_DSC_PATTERN_LINK
    // Config素材テーブル   B_DSC_RESOURCE
    // 作業パターン詳細テーブル B_DSC_PATTERN_LINK
    // 代入値紐付テーブル  B_DSC_PTN_VARS_LINK
    //
    /////////////////////////////////////////////////////////////////////

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
    $log_level = getenv('LOG_LEVEL');

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
    $hostvar_search_php  = '/libs/backyardlibs/dsc_driver/WrappedStringReplaceAdmin.php';

    $db_access_user_id   = -100803;

    //----変数名テーブル関連
    $strCurTableDscVarsTable = "B_DSC_VARS_MASTER";
    $strJnlTableDscVarsTable = "B_DSC_VARS_MASTER_JNL";
    $strSeqOfCurTableDscVars = "B_DSC_VARS_MASTER_RIC";
    $strSeqOfJnlTableDscVars = "B_DSC_VARS_MASTER_JSQ";

    // ITA側で管理している DSC リソースファイル（Config素材）格納先ディレクトリ
    $vg_resource_contents_dir   = $root_dir_path . "/uploadfiles/2100060003/RESOURCE_MATTER_FILE";  // リソースファイルパス
    $vg_powershell_contents_dir = $root_dir_path . "/uploadfiles/2100060016/POWERSHELL_FILE";       // powershellファイルパス
    $vg_param_contents_dir      = $root_dir_path . "/uploadfiles/2100060017/PARAM_FILE";            // paramファイルパス
    $vg_import_contents_dir     = $root_dir_path . "/uploadfiles/2100060018/IMPORT_FILE";           // importファイルパス
    $vg_configdata_contents_dir = $root_dir_path . "/uploadfiles/2100060019/CONFIGDATA_FILE";       // configdataファイルパス
    $vg_cmpoption_contents_dir  = $root_dir_path . "/uploadfiles/2100060020/CMPOPTION_FILE";        // cmpoptionファイルパス

    // WrappedStringReplaceAdmin.phpで使用する変数定義
    // ユーザーホスト変数名の先頭文字
    define("DF_HOST_VAR_HED"               ,"VAR_");

    //----------------------------------------------
    // リソース管理構造体(B_DSC_RESOURCE)
    //----------------------------------------------
    $arrayConfigOfDscVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

	// テンプレート管理構造体
    $arrayValueTmplOfDscVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "VARS_NAME_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    //変数名テーブル関連----

    //----作業パターン変数名紐付テーブル関連
    $strCurTableDscPatternVarsLink = "B_DSC_PTN_VARS_LINK";
    $strJnlTableDscPatternVarsLink = "B_DSC_PTN_VARS_LINK_JNL";
    $strSeqOfCurTableDscPatternVarsLink = "B_DSC_PTN_VARS_LINK_RIC";
    $strSeqOfJnlTableDscPatternVarsLink = "B_DSC_PTN_VARS_LINK_JSQ";

    //----------------------------------------------
    // 作業パターン詳細テーブル構造体(B_DSC_PATTERN_LINK)
    //----------------------------------------------
    $arrayConfigOfDscPatternVarsLink = array(
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

	// テンプレート管理構造体
    $arrayValueTmplOfDscPatternVarsLink = array(
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

    ////////////////////////////////
    // ローカル変数(全体)宣言        //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し         //
        ////////////////////////////////
        require_once ($root_dir_path . $hostvar_search_php);

        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                    //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // DBコネクト完了
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション開始             //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]トランザクション開始
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55004");
            require ($root_dir_path . $log_output_php );
        }

        ///////////////////////////////////////////////////
        //                                               //
        // [0001] 関連シーケンスをロックする                    //
        //                                               //
        //        デッドロック防止のために、昇順でロック            //
        ///////////////////////////////////////////////////
        //----デッドロック防止のために、昇順でロック
        $aryTgtOfSequenceLock = array(
            $strSeqOfCurTableDscVars,
            $strSeqOfJnlTableDscVars,
            $strSeqOfCurTableDscPatternVarsLink,
            $strSeqOfJnlTableDscPatternVarsLink
        );
        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($strSeqName,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( 'シーケンス['. $strSeqName .']のロックに失敗しました。' );
            }
        }
        //デッドロック防止のために、昇順でロック----

        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        // [0002] ありもの変数名一覧(マルチユニーク[素材ID,変数名ID])の作成                                  //
        //        INPUT(1)系テーブル「変数が埋め込まれたファイル管理系テーブル」から                         //
        //        一時テーブル(1)役の変数(連想配列)へ集約する                                                //
        //        ・一時テーブル(1)役の変数(連想配列)には、テンプレート変数自体は格納しないが、              //
        //          テンプレートファイル内に埋め込まれた変数名は格納する                                     //
        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        //----一時テーブル(1)役の変数(連想配列)を宣言
        // T0005  aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
        $aryVarsPerMatterId = array();

        // T0002 aryMatterFilePerMatterId[素材管理Pkey] = リソースファイル
        $aryMatterFilePerMatterId = array();
        //一時テーブル(1)役の変数(連想配列)を宣言----

        $intFetchedFromDscTmpl = null;

        $intFetchedFromDscMatterFile = null;

        $strTableCurDscMatter    = "B_DSC_RESOURCE";
        $strColumnIdOfMatterId   = "RESOURCE_MATTER_ID";
        $strColumnIdOfMatterFile = "RESOURCE_MATTER_FILE";

        ////////////////////////////////////////////////////////////////
        // Config素材管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////

        //----------------------------------------------
        // SQL生成 Config素材テーブル(B_DSC_RESOURCE)
        //----------------------------------------------
        $sqlUtnBody = "SELECT "
                     ." {$strColumnIdOfMatterId} MATTER_ID,"
                     ." {$strColumnIdOfMatterFile} MATTER_FILE "
                     ."FROM {$strTableCurDscMatter} "     // リソーステーブル(B_DSC_RESOURCE)
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
        }
        //----------------------------------------------
        // SQL実行 Config素材テーブル(B_DSC_RESOURCE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
        }
        //----------------------------------------------
        // リソース（Config素材)ファイル名格納
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
           // T0002 aryMatterFilePerMatterId[素材管理Pkey] = リソース(Config素材)ファイル名(***.ps1)
            $aryMatterFilePerMatterId[$row["MATTER_ID"]] = $row["MATTER_FILE"];
        }
        // fetch行数を取得
        $intFetchedFromDscMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        //----リソース(Config素材)ごとにループ。配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。
        // aryMatterFilePerMatterId:[Pkey] = 素材ID
        //----------------------------------------------
        // リソース(Config素材)
        // 配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。
        //----------------------------------------------
        foreach($aryMatterFilePerMatterId as $intMatterId=>$strMatterFile){
            $aryVarName = array();

            // リソース(Config素材)が未登録の場合は処理スキップ
            if(strlen($strMatterFile)===0){
                // リソース(Config素材)(｛｝)が未登録。処理スキップ
                $msgstr = $objMTS->getSomeMessage("ITADSCH-ERR-55266",array($intMatterId));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                continue;
            }

            //******************************************************************
            // ******リソース(Config素材)で使用している変数を抜出す。*****
            //******************************************************************
            $ret = getHostVars($intMatterId,
                               $strMatterFile,
                               $aryVarName);
            if($ret === false){
                // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
                $warning_flag = 1;
            }

            //----変数（素材ID別変数コレクション）に、素材IDを鍵する値を初期化
            // 変数が未登録でも空の配列を入れる
            // T0005  aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
            $aryVarsPerMatterId[$intMatterId] = $aryVarName;
        }
        // リソース(Config素材)ファイル毎にループ。配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。----

        // PowerShell
        $ret = GetOtherFilesVars('pws', $aryVarsPerPowerShellId);
        if($ret === false){
            // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
            $warning_flag = 1;
        }

        // Param
        $ret = GetOtherFilesVars('prm', $aryVarsPerParamId);
        if($ret === false){
            // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
            $warning_flag = 1;
        }

        // Import
        $ret = GetOtherFilesVars('imp', $aryVarsPerImportId);
        if($ret === false){
            // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
            $warning_flag = 1;
        }

        // ConfigData
        $ret = GetOtherFilesVars('cnfd', $aryVarsPerConfigDataId);
        if($ret === false){
            // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
            $warning_flag = 1;
        }

        // CmpOption
        $ret = GetOtherFilesVars('cmp', $aryVarsPerCmpOptionId);
        if($ret === false){
            // リソース(Config素材)で使用している変数抜出で一部エラーがあった。
            $warning_flag = 1;
        }

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // [0003] ありもの一覧(マルチユニーク[作業パターンID,素材ID]と、でマルチユニーク)の作成             //
        //                                                                                                  //
        //        INPUT(2)系テーブル「作業パターン詳細等」から、一時テーブル(2)役の変数(連想配列)へ集約する //
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        //----一時テーブル(2)役の変数(連想配列)を宣言
        // T0003 aryMattersPerPattern:[パターンID][array(Config素材 Pkey)](作業パターン詳細マスタベース)
        $aryMattersPerPattern = array();
        
        $aryPowerShellsPerPattern = array();
        $aryParamsPerPattern      = array();
        $aryImportsPerPattern     = array();
        $aryCfgDatasPerPattern    = array();
        $aryCmpOptionsPerPattern  = array();
        //一時テーブル(2)役の変数(連想配列)を宣言----

        $intFetchedFromPatterDetail = null;

        ////////////////////////////////////////////////////////////////
        // 作業パターン詳細から必要なデータを取得
        ////////////////////////////////////////////////////////////////
        //----------------------------------------------
    	// SQL作成 B_DSC_PATTERN_LINK(作業パターン詳細テーブル)
        //----------------------------------------------
        $sqlUtnBody = "SELECT DISTINCT "
                     ."TAB_1.PATTERN_ID ,"
                     ."TAB_1.RESOURCE_MATTER_ID   MATTER_ID, "
                     ."TAB_1.POWERSHELL_FILE_ID   POWERSHELL_ID, "
                     ."TAB_1.PARAM_FILE_ID        PARAM_ID, "
                     ."TAB_1.IMPORT_FILE_ID       IMPORT_ID, "
                     ."TAB_1.CONFIGDATA_FILE_ID   CFGDATA_ID, "
                     ."TAB_1.CMPOPTION_FILE_ID    CMPOPTION_ID "
                     ."FROM B_DSC_PATTERN_LINK TAB_1 "
                     ."WHERE TAB_1.DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
    	$objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

    	if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
        }
        //----------------------------------------------
        // SQL実行 B_DSC_PATTERN_LINK(作業パターン詳細テーブル)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
        }
        //----------------------------------------------
        // 作業パターンに紐づくConfig素材IDを取得。作業パターンごとにグルーピングして格納
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
            // T0003 aryMattersPerPattern:[パターンID][array(Config素材 Pkey)](作業パターン詳細マスタベース)
            $intFoucsPattern = $row["PATTERN_ID"];
            if( array_key_exists($intFoucsPattern, $aryMattersPerPattern) === false ){
                $aryMattersPerPattern[$intFoucsPattern] = array();
            }
            $aryMattersPerPattern[$intFoucsPattern][] = $row["MATTER_ID"];


            // PowerShell
            if( array_key_exists($intFoucsPattern, $aryPowerShellsPerPattern) === false ){
                $aryPowerShellsPerPattern[$intFoucsPattern] = array();
            }
            $aryPowerShellsPerPattern[$intFoucsPattern][] = $row["POWERSHELL_ID"];

            // Param
            if( array_key_exists($intFoucsPattern, $aryParamsPerPattern) === false ){
                $aryParamsPerPattern[$intFoucsPattern] = array();
            }
            $aryParamsPerPattern[$intFoucsPattern][] = $row["PARAM_ID"];

            // Import
            if( array_key_exists($intFoucsPattern, $aryImportsPerPattern) === false ){
                $aryImportsPerPattern[$intFoucsPattern] = array();
            }
            $aryImportsPerPattern[$intFoucsPattern][] = $row["IMPORT_ID"];

            // ConfigData
            if( array_key_exists($intFoucsPattern, $aryCfgDatasPerPattern) === false ){
                $aryCfgDatasPerPattern[$intFoucsPattern] = array();
            }
            $aryCfgDatasPerPattern[$intFoucsPattern][] = $row["CFGDATA_ID"];

            // CmpOption
            if( array_key_exists($intFoucsPattern, $aryCmpOptionsPerPattern) === false ){
                $aryCmpOptionsPerPattern[$intFoucsPattern] = array();
            }
            $aryCmpOptionsPerPattern[$intFoucsPattern][] = $row["CMPOPTION_ID"];
        }
        //作業パターンに紐づくConfig素材IDを取得。作業パターンごとにグルーピングして格納----

        // fetch行数を取得
        $intFetchedFromPatterDetail = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        ////////////////////////////////////////////////////////////////////////////
        //                                                                        //
        // [0004] 一時テーブル(1)役の変数を利用して、変数名一覧テーブルを更新する //
        //                                                                        //
        ////////////////////////////////////////////////////////////////////////////
        //----一時テーブル(1)役の変数から、変数名を重複を排除した形でリストアップする
        // T0006  aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
        $aryVarIdPerVarNameFromFiles = array();
        $aryVarIdPerVarNameFromFiles_fix = array();

        //----------------------------------------------
        // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerMatterId as $intMatterId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //----------------------------------------------
        // aryVarsPerPowerShellId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerPowerShellId as $intPowerShellId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //----------------------------------------------
        // aryVarsPerParamId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerParamId as $intPparamId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //----------------------------------------------
        // aryVarsPerImportId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerImportId as $intImportd=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //----------------------------------------------
        // aryVarsPerConfigDataId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerConfigDataId as $intCfgDataId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //----------------------------------------------
        // aryVarsPerCmpOptionId:[Pkey][変数名]=1(素材マスタベース)
        //----------------------------------------------
        foreach($aryVarsPerCmpOptionId as $intCmpOptionId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }
        
        //一時テーブル(1)役の変数から、変数名を重複を排除した形でリストアップする----

        $intFetchedFromDscVarsTable = null;

        // T0007  aryRowFromDscVarsTable:[変数名][変数マスタの各情報](変数マスタ)
        $aryRowFromDscVarsTable = array();

        $arrayConfig = $arrayConfigOfDscVarsTable;
        $arrayValue = $arrayValueTmplOfDscVarsTable;

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        //----------------------------------------------
        // SQL作成  変数マスタテーブル  B_DSC_VARS_MASTER
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT",
                                             "VARS_NAME_ID",
                                             $strCurTableDscVarsTable,
                                             $strJnlTableDscVarsTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002100")) );
        }
        //----------------------------------------------
        // SQL実行
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002200")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $strForcusVarName = $row["VARS_NAME"];
            // T0007  aryRowFromDscVarsTable:[変数名][変数マスタの各情報](変数マスタ)
            $aryRowFromDscVarsTable[$strForcusVarName] = $row;
        }
        // fetch行数を取得
        $intFetchedFromDscVarsTable = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        //----実際にあるべき変数名をテーブルに反映させる【活性化】
        // aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
        $tmpAryKeysOfVarIdPerVarNameFromFiles = array_keys($aryVarIdPerVarNameFromFiles);
        //----------------------------------------------
        // 変数名テーブル作成
        //----------------------------------------------
        foreach($tmpAryKeysOfVarIdPerVarNameFromFiles as $strVarName){
            $intVarNameId = null;
            $boolLoopNext = false;
            $strSqlType = null;

            // Config素材各ファイルで使用している変数が変数マスタにあるか確認
            // aryRowFromDscVarsTable:[変数名][変数マスタの各情報](変数マスタ)
            if( array_key_exists($strVarName, $aryRowFromDscVarsTable) === true ){
                //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                $aryRowOfTableUpdate = $aryRowFromDscVarsTable[$strVarName];
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    //----SQLを発行せずループを抜けるフラグ、を立てる
                    $boolLoopNext = true;
                    //SQLを発行せずループを抜けるフラグ、を立てる----
                }
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                    //----SQLを発行するので、フラグは立てないまま維持する。
                    //$boolLoopNext = false;
                    //SQLを発行するので、フラグは立てないまま維持する。----
                }
                else{
                    //----存在しないはずの、値なので、想定外エラーに倒す。
                    // 異常フラグON
                    $error_flag = 1;
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002300")) );
                    //存在しないはずの、値なので、想定外エラーに倒す。----
                }

                $strSqlType = "UPDATE";
                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

                //活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。----
            }
            else{
                //----テーブルにないので、新たに挿入する。
                $aryRowOfTableUpdate = $arrayValueTmplOfDscVarsTable;

                // テーブルロック
                $retArray = getSequenceLockInTrz($strSeqOfCurTableDscVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002400")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfCurTableDscVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002500")) );
                }
                $intVarNameId = $retArray[0];

                $strSqlType = "INSERT";
                $aryRowOfTableUpdate["VARS_NAME_ID"] = $intVarNameId;
                $aryRowOfTableUpdate["VARS_NAME"] = $strVarName;
                //テーブルにないので、新たに挿入する。----
            }

            // 作業パターン変数紐付テーブル、を更新するときの準備として、変数名IDを代入。
            // T0006 aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
            $aryVarIdPerVarNameFromFiles[$strVarName] = $intVarNameId;

            if( $boolLoopNext === true ){
                //----すでにレコードがあり、活性化済('0')なので、次のループへ
                continue;
                //すでにレコードがあり、活性化済('0')なので、次のループへ----
            }

            $retArray = getSequenceLockInTrz($strSeqOfJnlTableDscVars,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002600")) );
            }
            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableDscVars, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002700")) );
            }
            $intJournalSeqNo = $retArray[0];

            $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $intJournalSeqNo;
            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";
            $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

            $arrayConfig = $arrayConfigOfDscVarsTable;
            $arrayValue = $aryRowOfTableUpdate;
            $temp_array = array();

            // DEBUGログに変更
            if ( $log_level === 'DEBUG' ){
                // 更新ログ
                ob_start();
                var_dump($arrayValue);
                $msgstr = ob_get_contents();
                ob_clean();
                LocalLogPrint(basename(__FILE__),__LINE__,"変数マスタ 更新($strSqlType)\n$msgstr");
            }

            //----------------------------------------------
            // SQL作成  変数マスタテーブル  B_DSC_VARS_MASTER
            //----------------------------------------------
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 $strSqlType,
                                                 "VARS_NAME_ID",
                                                 $strCurTableDscVarsTable,
                                                 $strJnlTableDscVarsTable,
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            //----------------------------------------------
            // クエリー生成
            //----------------------------------------------
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002800")) );
            }
            if( $objQueryJnl->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00002900")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003000")) );
            }
            if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003100")) );
            }
            //----------------------------------------------
            // SQL実行  objQueryUtn
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003200")) );
            }

            //----------------------------------------------
            // SQL実行  objQueryJnl
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
            }
            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($tmpAryKeysOfVarIdPerVarNameFromFiles);
        //実際にあるべき変数名をテーブルに反映させる【活性化】----

        //----存在しているレコードで、もう実際にない変数名を廃止する
        // aryRowFromDscVarsTable:[変数名][変数マスタの各情報](変数マスタ)
        //----------------------------------------------
        // 実際にない変数名を廃止する
        //----------------------------------------------
        foreach($aryRowFromDscVarsTable as $strVarName=>$row){
            // 作業パターン変数紐付テーブル、を更新するときの準備として、変数名IDを代入。
            // T0006 $aryVarIdPerVarNameFromFiles [変数名(一意)]=変数マスタPkey (初期値 NULL)
            $aryVarIdPerVarNameFromFiles[$strVarName] = $row["VARS_NAME_ID"];

            // $aryVarIdPerVarNameFromFilesは未使用の変数が追加されるのでaryVarIdPerVarNameFromFiles_fixで変数の使用・未使用を判定
            if( array_key_exists($strVarName, $aryVarIdPerVarNameFromFiles_fix) !== true ){
                //----廃止する
                // aryRowFromDscVarsTable:[変数名][変数マスタの各情報](変数マスタ)
                $aryRowOfTableUpdate = $aryRowFromDscVarsTable[$strVarName];
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
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
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
                    //想定外エラー----
                }

                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

                // テーブル ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableDscVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003500")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableDscVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003600")) );
                }
                $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                $strSqlType = "UPDATE";

                $arrayConfig = $arrayConfigOfDscVarsTable;
                $arrayValue  = $aryRowOfTableUpdate;
                $temp_array  = array();

                // DEBUGログに変更
                if ( $log_level === 'DEBUG' ){
                    // 更新ログ
                    ob_start();
                    var_dump($arrayValue);
                    $msgstr = ob_get_contents();
                    ob_clean();
                    LocalLogPrint(basename(__FILE__),__LINE__,"変数マスタ廃止($strSqlType)\n$msgstr");
                }

                //----------------------------------------------
                // SQL作成  変数マスタテーブル  B_DSC_VARS_MASTER
                //----------------------------------------------
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     $strSqlType,
                                                     "VARS_NAME_ID",
                                                     $strCurTableDscVarsTable,
                                                     $strJnlTableDscVarsTable,
                                                     $arrayConfig,
                                                     $arrayValue,
                                                     $temp_array );

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];

                //----------------------------------------------
                // クエリー生成
                //----------------------------------------------
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

                if( $objQueryUtn->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003700")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003800")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003900")) );
                }

                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004000")) );
                }
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004100")) );
                }
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004200")) );
                }
                //廃止する----
                // DBアクセス事後処理
                unset($objQueryUtn);
                unset($objQueryJnl);
            }
        }
        //存在しているレコードで、もう実際にない変数名を廃止する----

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // [0005] 一時テーブル(1)役の変数と一時テーブル(2)役の変数を利用して、作業パターン変数紐付テーブルを更新する //
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $intFetchedFromDscPatternVarsLink = null;
        // T0008  aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
        $aryRowsPerPatternFromDscPatternVarsLink = array();

        $arrayConfig = $arrayConfigOfDscPatternVarsLink;
        $arrayValue  = $arrayValueTmplOfDscPatternVarsLink;

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        //----------------------------------------------
        // SQL作成  代入値紐付テーブル  B_DSC_PTN_VARS_LINK
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT",
                                            "VARS_LINK_ID",
                                             $strCurTableDscPatternVarsLink,
                                             $strJnlTableDscPatternVarsLink,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004400")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004500")) );
        }
        //----------------------------------------------
        // SQL実行
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004600")) );
        }
        //----------------------------------------------
        //  更新対象のテーブルから行を取得。作業パターンごとにグルーピングして格納
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
            $intFoucsPattern = $row["PATTERN_ID"];
            $intFocusVarName = $row["VARS_NAME_ID"];
            // T0008  aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
            if( array_key_exists($intFoucsPattern, $aryRowsPerPatternFromDscPatternVarsLink) === false ){
                $aryRowsPerPatternFromDscPatternVarsLink[$intFoucsPattern] = array();
            }
            $aryRowsPerPatternFromDscPatternVarsLink[$intFoucsPattern][$intFocusVarName] = $row;
        }
        //更新対象のテーブルから行を取得。作業パターンごとにグルーピングして格納----

        // fetch行数を取得
        $intFetchedFromDscPatternVarsLink = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
        $aryVarNameIdsPerPattern = array();

        //----------------------------------------------
        //  実際にあるべき組み合わせをテーブルに反映させる【活性化】
        //----------------------------------------------
        // Config素材
        $init_flg = true;
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryMattersPerPattern, 
                                        $aryVarsPerMatterId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }

        $init_flg = false;
        // PowerShell
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryPowerShellsPerPattern, 
                                        $aryVarsPerPowerShellId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }

        // Param
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryParamsPerPattern, 
                                        $aryVarsPerParamId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }

        // Import
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryImportsPerPattern, 
                                        $aryVarsPerImportId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }

        // ConfigData
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryCfgDatasPerPattern, 
                                        $aryVarsPerConfigDataId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }

        // CmpOpstion
        $ret = SetPtnVarLinkActivation( $init_flg,
                                        $aryCmpOptionsPerPattern, 
                                        $aryVarsPerCmpOptionId,
                                        $aryVarIdPerVarNameFromFiles,
                                        $aryRowsPerPatternFromDscPatternVarsLink,
                                        $aryVarNameIdsPerPattern );
        if( $ret === false ){
            $warning_flag = 1;
        }
        //実際にあるべき組み合わせをテーブルに反映させる【活性化】----

        //----存在しているレコードで、もう実際にない組み合わせを廃止する
        // 作業パターン変数紐付マスタの内容でループ
        // aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
        foreach($aryRowsPerPatternFromDscPatternVarsLink as $intPatternId=>$aryRowsPerVarNameId){
            //----変数名IDごとにループする
            foreach($aryRowsPerVarNameId as $intVarNameId=>$row){
                // 作業パターン変数紐付マスタの情報取得
                // aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                $aryRowOfTableUpdate = $aryRowsPerPatternFromDscPatternVarsLink[$intPatternId][$intVarNameId];

                $boolDisuseOnFlag = false;

                // 作業パターン詳細にパターンID+変数IDが登録されているか判定
                // aryMattersPerPattern:[パターンID][array(Config素材 Pkey)](作業パターン詳細マスタベース)
                if( ( array_key_exists($intPatternId, $aryMattersPerPattern) === false ) and 
                    ( array_key_exists($intPatternId, $aryPowerShellsPerPattern) === false ) and 
                    ( array_key_exists($intPatternId, $aryParamsPerPattern) === false ) and 
                    ( array_key_exists($intPatternId, $aryImportsPerPattern) === false ) and 
                    ( array_key_exists($intPatternId, $aryCfgDatasPerPattern) === false ) and 
                    ( array_key_exists($intPatternId, $aryCmpOptionsPerPattern) === false ) ){
                    //----ファイルを解析した組み合わせの中に、調べている作業パターンがないので、廃止する
                    $boolDisuseOnFlag = true;
                    //ファイルを解析した組み合わせの中に、調べている作業パターンがないので、廃止する----
                }
                else{
                    //作業パターン詳細マスタをパターンIDがあるか判定
                    // aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
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
                //
                if( $boolDisuseOnFlag === false ){
                    //----登録されて活性されているべきレコードなので、なにもしない
                    continue;
                    //登録されて活性されているべきレコードなので、なにもしない----
                }
                // 作業パターン変数紐付が有効レコードか判定
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
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
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005800")) );
                    //想定外エラー----
                }
                // ジャーナル ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableDscPatternVarsLink,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005900")) );
                }
                // ジャーナルテーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableDscPatternVarsLink, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006000")) );
                }
                $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                $arrayConfig = $arrayConfigOfDscPatternVarsLink;
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

                //----------------------------------------------
                // SQL作成  代入値紐付テーブル  B_DSC_PTN_VARS_LINK
                //----------------------------------------------
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     $strSqlType,
                                                     "VARS_LINK_ID",
                                                     $strCurTableDscPatternVarsLink,
                                                     $strJnlTableDscPatternVarsLink,
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
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006100")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006200")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006300")) );
                }

                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006400")) );
                }

                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006500")) );
                }

                //----------------------------------------------
                // SQL実行
                //----------------------------------------------
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00006600")) );
                }
            }
        }
        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]コミット;
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55015");
            require ($root_dir_path . $log_output_php );
        }
        ////////////////////////////////
        // トランザクション終了              //
        ////////////////////////////////
        $objDBCA->transactionExit();
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]トランザクション終了
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55005");
            require ($root_dir_path . $log_output_php );
        }

    }
    catch (Exception $e){

        // 例外発生
        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-55272");
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
                // [処理]ロールバック
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55016");
            }
            else{
                // ロールバックに失敗しました
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50045");
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                // トランザクション終了
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50047");
            }
            else{
                // トランザクションの終了時に異常が発生しました
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50049");
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
            // プロシージャ終了(異常)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-55267");
            require ($root_dir_path . $log_output_php );
        }

        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(警告)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-55268");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(正常)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55002");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        echo $FREE_LOG . "\n";
        require ($root_dir_path . $log_output_php);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   リソースファイル（Config素材）で使用している変数を取得
    //
    // パラメータ
    //   $in_filename:       リソースファイル（Config素材）名(DSC)
    //   $in_pkey:           リソースファイル（Config素材） Pkey
    //   $ina_vars:          リソースファイル内の変数配列返却
    //                       [変数名]
    //
    // 戻り値
    //   Config素材ファイル名(DSC)
    ////////////////////////////////////////////////////////////////////////////////
    function getHostVars($in_pkey,
                         $in_filename,
                         &$ina_vars){
        global          $objMTS;
        global          $vg_resource_contents_dir;

        $ina_vars     = array();
        $intNumPadding = 10;

        //////////////////////////////////////////////
        // Config素材に登録されている変数を抜出す。
        //////////////////////////////////////////////
        // リソースファイル（Config素材）取得
        // リソースファイル（Config素材）名は $vg_resource_contents_dir/Pkey(10桁)/Config素材ファイル名 する。
        $file_name = sprintf("%s/%s/%s",
                             $vg_resource_contents_dir,
                             str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                             $in_filename);

        // リソースファイル（Config素材）名の存在チェック
        if( file_exists($file_name) === false ){
            // システムで管理しているリソースファイル（Config素材）名(｛｝:｛｝)が存在しない。
            $msgstr = $objMTS->getSomeMessage("ITADSCH-ERR-55210",array($in_pkey,basename($in_filename)));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            //これ以上処理続行できない
            return false;
        }
        //////////////////////////////////////////////
        // リソースファイル（Config素材）に登録されている変数を抜出す。
        //////////////////////////////////////////////
        // リソースファイル（Config素材）の内容読込
        $dataString = file_get_contents($file_name);

        // リソースファイル（Config素材）に登録されている変数を抜出。
        $local_vars = array();
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

        $aryResultParse = $objWSRA->getParsedResult();
        unset($objWSRA);

        // リソースファイル（Config素材）に登録されている変数退避
        foreach( $aryResultParse[1] as $var_name ){
            // 変数名を一意にする。
            $ina_vars[$var_name] = 1;
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //    Config素材以外のリソースファイルで使用している変数を取得
    //
    // パラメータ
    //   $in_get_type:       リソースファイルの種別
    //   $ina_aryVarsPerId:  リソースファイル内の変数配列返却
    //                       [Pkey][変数名]
    //
    // 戻り値
    //   Config素材ファイル名(DSC)
    ////////////////////////////////////////////////////////////////////////////////
    function GetOtherFilesVars( $in_get_type,
                              &$ina_aryVarsPerId ){
        global          $objDBCA;
        global          $objMTS;

        try{
            ///////////////////////////////////////////////////////////////////////////////////////////////////////
            // [0002] ありもの変数名一覧(マルチユニーク[素材ID,変数名ID])の作成                                  //
            //        INPUT(1)系テーブル「変数が埋め込まれたファイル管理系テーブル」から                         //
            //        一時テーブル(1)役の変数(連想配列)へ集約する                                                //
            ///////////////////////////////////////////////////////////////////////////////////////////////////////

            switch($in_get_type){
            case 'pws':
                $strTableCurDscTable = "B_DSC_POWERSHELL_FILE";
                $strColumnIdOfId     = "POWERSHELL_FILE_ID";
                $strColumnIdOfFile   = "POWERSHELL_FILE";
                break;
            case 'prm':
                $strTableCurDscTable = "B_DSC_PARAM_FILE";
                $strColumnIdOfId     = "PARAM_FILE_ID";
                $strColumnIdOfFile   = "PARAM_FILE";
                break;
            case 'imp':
                $strTableCurDscTable = "B_DSC_IMPORT_FILE";
                $strColumnIdOfId     = "IMPORT_FILE_ID";
                $strColumnIdOfFile   = "IMPORT_FILE";
                break;
            case 'cnfd':
                $strTableCurDscTable = "B_DSC_CONFIGDATA_FILE";
                $strColumnIdOfId     = "CONFIGDATA_FILE_ID";
                $strColumnIdOfFile   = "CONFIGDATA_FILE";
                break;
            case 'cmp':
                $strTableCurDscTable = "B_DSC_CMPOPTION_FILE";
                $strColumnIdOfId     = "CMPOPTION_FILE_ID";
                $strColumnIdOfFile   = "CMPOPTION_FILE";
                break;
            }

            //----一時テーブル(1)役の変数(連想配列)を宣言
            // T0005  aryVarsPerId:[Pkey][変数名]=1(素材マスタベース)
            $ina_aryVarsPerId = array();

            // T0002 aryMatterFilePerMatterId[素材管理Pkey] = リソースファイル
            $aryOtherFilesPerId = array();
            //一時テーブル(1)役の変数(連想配列)を宣言----

            $intFetchedFromDscOtherFiles = null;

            ////////////////////////////////////////////////////////////////
            // 素材管理から必要なデータを取得
            ////////////////////////////////////////////////////////////////

            //----------------------------------------------
            // SQL生成 Config素材テーブル(B_DSC_RESOURCE)
            //----------------------------------------------
            $sqlUtnBody = "SELECT "
                         ." {$strColumnIdOfId}  FILE_ID, "
                         ." {$strColumnIdOfFile} FILE_NAME "
                         ."FROM {$strTableCurDscTable} "     // リソーステーブル(B_DSC_RESOURCE)
                         ."WHERE DISUSE_FLAG = '0' ";

            $arrayUtnBind = array();

            //----------------------------------------------
            // クエリー生成
            //----------------------------------------------
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
            }
            //----------------------------------------------
            // SQL実行 素材テーブル(B_DSC_XXXXX)
            //----------------------------------------------
            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
            }
            //----------------------------------------------
            // 素材ファイル名格納
            //----------------------------------------------
            while ( $row = $objQueryUtn->resultFetch() ){
               // T0002 aryOtherFilesPerId[管理Pkey] = ファイル名(***.***)
                $aryOtherFilesPerId[$row["FILE_ID"]] = $row["FILE_NAME"];
            }
            // fetch行数を取得
            $intFetchedFromDscOtherFiles = $objQueryUtn->effectedRowCount();

            // DBアクセス事後処理
            unset($objQueryUtn);

            //----取得した素材ごとにループ。配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。
            // aryMatterFilePerMatterId:[Pkey] = 素材ID
            //----------------------------------------------
            // 素材
            // 配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。
            //----------------------------------------------
            foreach($aryOtherFilesPerId as $intOtherId=>$strOtherFile){
                $aryVarName = array();

                // 素材が未登録の場合は処理スキップ
                if(strlen($strOtherFile)===0){
                    // 素材(｛｝)が未登録。処理スキップ
                    $msgstr = $objMTS->getSomeMessage("ITADSCH-ERR-55266",array($intOtherId));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    continue;
                }

                //******************************************************************
                // ****** 素材で使用している変数を抜出す。*****
                //******************************************************************
                $ret = getHostVarsOther($in_get_type,
                                        $intOtherId,
                                        $strOtherFile,
                                        $aryVarName
                                   );
                if($ret === false){
                    // Config素材以外のリソースで使用している変数抜出で一部エラーがあった。
                    $warning_flag = 1;
                }

                //----変数（素材ID別変数コレクション）に、素材IDを鍵する値を初期化
                // 変数が未登録でも空の配列を入れる
                $ina_aryVarsPerId[$intOtherId] = $aryVarName;
            }
        }
        catch (Exception $e){
            // 例外発生
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-55272");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            // 例外メッセージ出力
            $FREE_LOG = $e->getMessage();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
            // 念のためロールバック/トランザクション終了
            if( $objDBCA->getTransactionMode() ){
                // ロールバック
                if( $objDBCA->transactionRollBack()=== true ){
                    // [処理]ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55016");
                }
                else{
                    // ロールバックに失敗しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50045");
                }

                // トランザクション終了
                if( $objDBCA->transactionExit()=== true ){
                    // トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50047");
                }
                else{
                    // トランザクションの終了時に異常が発生しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50049");
                }
            }
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Config素材以外のリソースファイルで使用している変数を取得
    //
    // パラメータ
    //   $in_get_type        取得種別
    //   $in_pkey:           リソースファイル（Config素材以外） Pkey
    //   $in_filename:       リソースファイル（Config素材以外）名(DSC)
    //   $ina_vars:          リソースファイル内の変数配列返却
    //                       [変数名]
    //
    // 戻り値
    //   Config素材ファイル名(DSC)
    ////////////////////////////////////////////////////////////////////////////////
    function getHostVarsOther( $in_get_type,
                               $in_pkey,
                               $in_filename,
                              &$ina_vars){
        global          $objMTS;
        global          $vg_powershell_contents_dir;
        global          $vg_param_contents_dir;
        global          $vg_import_contents_dir;
        global          $vg_configdata_contents_dir;
        global          $vg_cmpoption_contents_dir;

        switch($in_get_type){
        case 'pws':
            $contents_dir = $vg_powershell_contents_dir;
            break;
        case 'prm':
            $contents_dir = $vg_param_contents_dir;
            break;
        case 'imp':
            $contents_dir = $vg_import_contents_dir;
            break;
        case 'cnfd':
            $contents_dir = $vg_configdata_contents_dir;
            break;
        case 'cmp':
            $contents_dir = $vg_cmpoption_contents_dir;
        }

        $ina_vars     = array();
        $intNumPadding = 10;

        //////////////////////////////////////////////
        // 素材に登録されている変数を抜出す。
        //////////////////////////////////////////////
        // 素材取得
        $file_name = sprintf("%s/%s/%s",
                             $contents_dir,
                             str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                             $in_filename);

        // リソースファイル（Config素材）名の存在チェック
        if( file_exists($file_name) === false ){
            // システムで管理しているファイル名(｛｝:｛｝)が存在しない。
            $msgstr = $objMTS->getSomeMessage("ITADSCH-ERR-55210",array($in_pkey,basename($file_name)));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            //これ以上処理続行できない
            return false;
        }
        //////////////////////////////////////////////
        // 素材に登録されている変数を抜出す。
        //////////////////////////////////////////////
        // 素材の内容読込
        $dataString = file_get_contents($file_name);

        // リソースファイル（Config素材）に登録されている変数を抜出。
        $local_vars = array();
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

        $aryResultParse = $objWSRA->getParsedResult();
        unset($objWSRA);

        // リソースファイル（Config素材）に登録されている変数退避
        foreach( $aryResultParse[1] as $var_name ){
            // 変数名を一意にする。
            $ina_vars[$var_name] = 1;
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  処理内容
    //    実際にあるべき組み合わせを代入値紐付マスタに反映させる【活性化】
    //
    //  パラメータ
    //    $in_init_flg                   : 初期化フラグ
    //    $in_aryOtherPerPattern         : 作業パターン詳細マスタベース
    //                                     [パターンID][array(素材Pkey)]（素材ファイルの指定は呼び出し側で設定）
    //    $in_aryVarsPerOtherId          : 素材ファイル毎の変数一覧（素材ファイルの指定は呼び出し側で設定）
    //                                     [素材Pkey][変数名]=1
    //
    //    $in_aryVarIdPerVarNameFromFiles: 変数一覧(一意) 
    //                                     [変数名(一意)]=変数マスタPkey (初期値 NULL)
    //    $ina_aryRowsPerPatternFromDscPatternVarsLink: 作業パターン変数紐付マスタの情報
    //                                                  [パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
    //    $ina_aryVarNameIdsPerPattern   : 作業パターン毎 変数一覧    中間データ
    //                                     [パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
    //
    //  戻り値
    //    true:   正常
    //    false:  異常
    //
    ////////////////////////////////////////////////////////////////////////////////
    function SetPtnVarLinkActivation( $in_init_flg,
                                      $in_aryOtherPerPattern,
                                      $in_aryVarsPerOtherId,
                                      $in_aryVarIdPerVarNameFromFiles,
                                     &$ina_aryRowsPerPatternFromDscPatternVarsLink,
                                     &$ina_aryVarNameIdsPerPattern ){
        global $objDBCA;
        global $db_model_ch;
        global $objMTS;
        global $strCurTableDscPatternVarsLink;
        global $strJnlTableDscPatternVarsLink;
        global $strSeqOfCurTableDscPatternVarsLink;
        global $strSeqOfJnlTableDscPatternVarsLink;
        global $arrayConfigOfDscPatternVarsLink;
        global $arrayValueTmplOfDscPatternVarsLink;
        global $db_access_user_id;
        global $log_level;

        try{
            //----作業パターンごとにループする
            // aryMattersPerPattern:[パターンID][array(Config素材 Pkey)](作業パターン詳細マスタベース)
            //----------------------------------------------
            //  実際にあるべき組み合わせをテーブルに反映させる【活性化】
            //----------------------------------------------
            foreach($in_aryOtherPerPattern as $intPatternId=>$aryPkeyId){
                // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
                if( $in_init_flg === true ){
                    $ina_aryVarNameIdsPerPattern[$intPatternId] = array();
                }
                //----素材IDごとにループする
                foreach($aryPkeyId as $intPkeyId){
                    // Config素材毎の変数一覧に該当素材が登録されているか確認
                    // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
                    if( array_key_exists($intPkeyId, $in_aryVarsPerOtherId) === false ){
                        continue;
                    }

                    // Config素材毎の変数一覧から該当素材の変数リストを取得
                    // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
                    $aryVarsOfFocusPkeyId = $in_aryVarsPerOtherId[$intPkeyId];

                    //----------------------------------------------
                    //  変数名ごとにループする
                    //----------------------------------------------
                    foreach($aryVarsOfFocusPkeyId as $strVarName=>$dummy){
                        $intVarsLinkId = null;
                        $boolLoopNext = false;
                        $strSqlType = null;

                        // 変数名IDを取得
                        // in_aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
                        $intVarNameId = $in_aryVarIdPerVarNameFromFiles[$strVarName];

                        // 作業パターン+変数の情報を既に登録済みか判定
                        if( array_key_exists($intPatternId,$ina_aryVarNameIdsPerPattern) === true ){
                            // 作業パターン+変数は登録済みなのでスキップ
                            if( array_key_exists($intVarNameId,$ina_aryVarNameIdsPerPattern[$intPatternId]) === true ){
                                continue;
                            }
                        }

                        // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
                        $ina_aryVarNameIdsPerPattern[$intPatternId][$intVarNameId] = 1;

                        //----更新対象のテーブルのレコードに、存在するかを調べる
                        // 作業パターン変数紐付マスタにパターンID+変数IDが登録されているか判定
                        // aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                        if( isset($ina_aryRowsPerPatternFromDscPatternVarsLink[$intPatternId][$intVarNameId]) === true ){
                            //----更新対象のテーブルに存在した
                            // 作業パターン変数紐付マスタにパターンID+変数IDが登録されている
                            // aryRowsPerPatternFromDscPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                            $aryRowOfTableUpdate = $ina_aryRowsPerPatternFromDscPatternVarsLink[$intPatternId][$intVarNameId];

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
                                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004700")) );
                                //存在しないはずの、値なので、想定外エラーに倒す。----
                            }
                            $strSqlType = "UPDATE";
                            //以降の処理で設定
                            //$aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                            $aryRowOfTableUpdate['DISUSE_FLAG']      = "0";
                            //更新対象のテーブルに存在した----
                        }
                        else{
                            //----存在しなかったので、新規に挿入
                            $aryRowOfTableUpdate = $arrayValueTmplOfDscPatternVarsLink;

                            // 新しいレコードなので、CURシーケンスを発行する
                            $retArray = getSequenceLockInTrz($strSeqOfCurTableDscPatternVarsLink,'A_SEQUENCE');
                            if( $retArray[1] != 0 ){
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004800")) );
                            }
                            // テーブル シーケンスNoを採番
                            $retArray = getSequenceValueFromTable($strSeqOfCurTableDscPatternVarsLink, 'A_SEQUENCE', FALSE );
                            if( $retArray[1] != 0 ){
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00004900")) );
                            }
                            $strSqlType = "INSERT";
                            $aryRowOfTableUpdate["VARS_LINK_ID"]     = $retArray[0];
                            $aryRowOfTableUpdate["PATTERN_ID"]       = $intPatternId;
                            $aryRowOfTableUpdate["VARS_NAME_ID"]     = $intVarNameId;
                            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";

                            //存在しなかったので、新規に挿入----
                        }

                        if( $boolLoopNext === true ){
                            continue;
                        }
                        // ジャーナルテーブル ロック
                        $retArray = getSequenceLockInTrz($strSeqOfJnlTableDscPatternVarsLink,'A_SEQUENCE');
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005000")) );
                        }
                        // ジャーナルテーブル シーケンスNoを採番
                        $retArray = getSequenceValueFromTable($strSeqOfJnlTableDscPatternVarsLink, 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005100")) );
                        }
                        $intJournalSeqNo = $retArray[0];

                        $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                        $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                        $arrayConfig = $arrayConfigOfDscPatternVarsLink;
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

                        //----------------------------------------------
                        // SQL作成  代入値紐付テーブル  B_DSC_PTN_VARS_LINK
                        //----------------------------------------------
                        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                             $strSqlType,
                                                             "VARS_LINK_ID",
                                                             $strCurTableDscPatternVarsLink,
                                                             $strJnlTableDscPatternVarsLink,
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
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005200")) );
                        }
                        if( $objQueryJnl->getStatus()===false ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005300")) );
                        }
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005400")) );
                        }
                        if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005500")) );
                        }
                        $rUtn = $objQueryUtn->sqlExecute();
                        if($rUtn!=true){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005600")) );
                        }
                        //----------------------------------------------
                        // SQL実行
                        //----------------------------------------------
                        $rJnl = $objQueryJnl->sqlExecute();
                        if($rJnl!=true){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryJnl->getLastError());
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00005700")) );
                        }
                        //更新対象のテーブルのレコードに、存在するかを調べる----
                        // DBアクセス事後処理
                        unset($objQueryUtn);
                        unset($objQueryJnl);
                    }
                    //変数名ごとにループする----
                }
                //素材IDごとにループする----
            }
            //作業パターンごとにループする----
            //実際にあるべき組み合わせをテーブルに反映させる【活性化】----
        }
        catch (Exception $e){
            // 例外発生
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-55272");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            // 例外メッセージ出力
            $FREE_LOG = $e->getMessage();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
            // 念のためロールバック/トランザクション終了
            if( $objDBCA->getTransactionMode() ){
                // ロールバック
                if( $objDBCA->transactionRollBack()=== true ){
                    // [処理]ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-55016");
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                else{
                    // ロールバックに失敗しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50045");
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }

                // トランザクション終了
                if( $objDBCA->transactionExit()=== true ){
                    // トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50047");
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                else{
                    // トランザクションの終了時に異常が発生しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50049");
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
            }
        }

        return true;
    }
?>
