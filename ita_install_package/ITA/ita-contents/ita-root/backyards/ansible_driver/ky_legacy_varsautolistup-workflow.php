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
    //      legacy変数自動更新
    //
    // 主要連想配列
    //
    // T0001
    // aryTmplFilePerTmplVarName      テンプレート管理マスタ
    //                                [テンプレート変数][Pkey] = テンプレートファイル
    // T0002
    // aryMatterFilePerMatterId       素材管理マスタ
    //                                [Pkey] = 素材ファイル
    // T0003
    // aryMattersPerPattern 　　　　　作業パターン詳細マスタベース
    //                                [パターンID][array(子Playbook Pkey)]
    // T0004
    // aryVarNameIdsPerPattern        作業パターン毎 変数一覧    中間データ
    //                                $aryMattersPerPatternをパターンID毎の変数一覧にまとめる
    //                                [パターンID][array([変数マスタPkey]=1)]
    // T0005  
    // aryVarsPerMatterId             素材毎の変数一覧           素材マスタベース
    //                                [Pkey][変数名]=1
    // T0006  
    // aryVarIdPerVarNameFromFiles    変数一覧(一意)             中間データ
    //                                aryVarsPerMatterIdを元にした変数一覧(一意)
    //                                [変数名(一意)]=変数マスタPkey (初期値 NULL)
    // T0007  
    // aryRowFromAnsVarsTable         変数マスタ
    //                                [変数名][変数マスタの各情報]
    // T0008  
    // aryRowsPerPatternFromAnsPatternVarsLink
    //                                作業パターン変数紐付マスタ
    //                                [パターンID][変数ID] = [作業パターン変数紐付の各情報]
    // T0009
    // aryVarIdPerVarNameFromFiles_fix  T0006と同様
    //                                素材で使用している変数を判定するのに使用
    //////////////////////////////////////////////////////////////////////
    
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

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>
    $db_access_user_id   = -100009;  // LEG(-100009):::PIO(-100010)

    //----変数名テーブル関連
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
    $strCurTableAnsVarsTable = "B_ANSIBLE_LNS_VARS_MASTER";
    $strJnlTableAnsVarsTable = "B_ANSIBLE_LNS_VARS_MASTER_JNL";
    $strSeqOfCurTableAnsVars = "B_ANSIBLE_LNS_VARS_MASTER_RIC";
    $strSeqOfJnlTableAnsVars = "B_ANSIBLE_LNS_VARS_MASTER_JSQ";


// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
    $arrayConfigOfAnsVarsTable = array(
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

    $arrayValueTmplOfAnsVarsTable = array(
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
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
    $strCurTableAnsPatternVarsLink = "B_ANS_LNS_PTN_VARS_LINK";
    $strJnlTableAnsPatternVarsLink = "B_ANS_LNS_PTN_VARS_LINK_JNL";
    $strSeqOfCurTableAnsPatternVarsLink = "B_ANS_LNS_PTN_VARS_LINK_RIC";
    $strSeqOfJnlTableAnsPatternVarsLink = "B_ANS_LNS_PTN_VARS_LINK_JSQ";

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

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    // #20181221 2019/01/17 Append 
    $db_update_flg              = false;    // DB更新フラグ
    $lv_a_proc_loaded_list_varsetup_pkey = 2100020001;
    $lv_a_proc_loaded_list_valsetup_pkey = 2100020002;

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        require_once ($root_dir_path . $ansible_common_php);

        require_once ($root_dir_path . $hostvar_search_php);

        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55001");
            require ($root_dir_path . $log_output_php );
        }

        //----2018/06/11
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        // ITA側で管理している 子playbookファイル格納先ディレクトリ
        $vg_playbook_contents_dir  = $vg_legacy_playbook_contents_dir;
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        //2018/06/11----

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //$FREE_LOG = 'DBコネクト完了';
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-55003");
            require ($root_dir_path . $log_output_php );
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースが更新されバックヤード処理が必要か判定
        ///////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70052");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $lv_UpdateRecodeInfo        = array();
        $ret = chkBackyardExecute($lv_a_proc_loaded_list_varsetup_pkey,
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
        //                                               //
        // [0001] 関連シーケンスをロックする             //
        //                                               //
        //        デッドロック防止のために、昇順でロック //
        ///////////////////////////////////////////////////
        //----デッドロック防止のために、昇順でロック
        $aryTgtOfSequenceLock = array(
            $strSeqOfCurTableAnsVars,
            $strSeqOfJnlTableAnsVars,
            $strSeqOfCurTableAnsPatternVarsLink,
            $strSeqOfJnlTableAnsPatternVarsLink
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

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        // T0001 
        //aryTmplFilePerTmplVarName:[テンプレート変数][Pkey] = テンプレートファイル(テンプレート管理マスタ)
        $aryTmplFilePerTmplVarName = array();

        // T0002 aryMatterFilePerMatterId[素材管理Pkey] = 素材ファイル
        $aryMatterFilePerMatterId = array();
        //一時テーブル(1)役の変数(連想配列)を宣言----

        $intFetchedFromAnsTmpl = null;

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>> ここから
        $strTableCurAnsTemplate = "B_ANS_TEMPLATE_FILE"; 

        ////////////////////////////////////////////////////////////////
        // テンプレート管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////
        $sqlUtnBody = "SELECT " 
                     ."ANS_TEMPLATE_ID, "
                     ."ANS_TEMPLATE_VARS_NAME ,"
                     ."ANS_TEMPLATE_FILE "
                     ."FROM {$strTableCurAnsTemplate} "
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
            // T0001
            //aryTmplFilePerTmplVarName:[テンプレート変数][Pkey] = テンプレートファイル(テンプレート管理マスタ)
            $aryTmplFilePerTmplVarName[$row["ANS_TEMPLATE_VARS_NAME"]][$row["ANS_TEMPLATE_ID"]] = $row["ANS_TEMPLATE_FILE"];
        }
        // fetch行数を取得
        $intFetchedFromAnsTmpl = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>> ここまで

        $intFetchedFromAnsMatterFile = null;

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        $strTableCurAnsMatter    = "B_ANSIBLE_LNS_PLAYBOOK";
        $strColumnIdOfMatterId   = "PLAYBOOK_MATTER_ID";
        $strColumnIdOfMatterFile = "PLAYBOOK_MATTER_FILE";

        ////////////////////////////////////////////////////////////////
        // 素材管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>> 
        $sqlUtnBody = "SELECT "
                     ." {$strColumnIdOfMatterId} MATTER_ID,"
                     ." {$strColumnIdOfMatterFile} MATTER_FILE "
                     ."FROM {$strTableCurAnsMatter} "
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
           // T0002 aryMatterFilePerMatterId[素材管理Pkey] = 素材ファイル
            $aryMatterFilePerMatterId[$row["MATTER_ID"]] = $row["MATTER_FILE"];
        }
        // fetch行数を取得
        $intFetchedFromAnsMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        /////////////////////////////////////////////////////////////
        // グローバル変数管理よりグローバル変数を取得
        /////////////////////////////////////////////////////////////
        $lva_global_vars_list              = array();
        $lva_global_vars_use_tpf_vars_list = array();
        $ret = getDBGlobalVarsMaster($lva_global_vars_list,$lva_global_vars_use_tpf_vars_list);
        if($ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001601")) );
        }

        //----素材(子プレイブックまたは対話ファイル)ごとにループ。配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。
        // aryMatterFilePerMatterId:[Pkey] = 素材ファイル(素材管理マスタ)
        foreach($aryMatterFilePerMatterId as $intMatterId=>$strMatterFile){
            $aryVarName = array();

            // 子Playbookが未登録の場合は処理スキップ
            if(strlen($strMatterFile)===0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55266",array($intMatterId)); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                
                continue;
            }

            // 子プレイブック及びテンプレートで使用している変数を抜出す。
            $ret = getHostVars($intMatterId,
                               $strMatterFile,
                               $aryVarName,
                               $aryTmplFilePerTmplVarName,
                               $lva_global_vars_use_tpf_vars_list);
            if($ret === false){
                // 子プレイブック及びテンプレートで使用している変数抜出で一部エラーがあった。
                $warning_flag = 1;
            }     

            //----変数（素材ID別変数コレクション）に、素材IDを鍵する値を初期化
            // 変数が未登録でも空の配列を入れる
            // T0005  aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
            $aryVarsPerMatterId[$intMatterId] = $aryVarName;
        }
        //素材(子プレイブックまたは対話ファイル)ごとにループ。配列[素材ID(Nx)] = array("変数名1","変数名1"・・・)で、格納。----

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // [0003] ありもの一覧(マルチユニーク[作業パターンID,素材ID]と、でマルチユニーク)の作成             //
        //                                                                                                  //
        //        INPUT(2)系テーブル「作業パターン詳細等」から、一時テーブル(2)役の変数(連想配列)へ集約する //
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        //----一時テーブル(2)役の変数(連想配列)を宣言
        // T0003 aryMattersPerPattern:[パターンID][array(子Playbook Pkey)](作業パターン詳細マスタベース)
        $aryMattersPerPattern = array();
        //一時テーブル(2)役の変数(連想配列)を宣言----

        $intFetchedFromPatterDetail = null;

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        ////////////////////////////////////////////////////////////////
        // 作業パターン詳細から必要なデータを取得
        ////////////////////////////////////////////////////////////////
        $sqlUtnBody = "SELECT DISTINCT " 
                     ."TAB_1.PATTERN_ID ,"
                     ."TAB_1.PLAYBOOK_MATTER_ID   MATTER_ID "
                     ."FROM B_ANSIBLE_LNS_PATTERN_LINK TAB_1 "
                     ."WHERE TAB_1.DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
        }
        //----作業パターンに紐づく素材IDを取得。作業パターンごとにグルーピングして格納
        while ( $row = $objQueryUtn->resultFetch() ){
            // T0003 aryMattersPerPattern:[パターンID][array(子Playbook Pkey)](作業パターン詳細マスタベース)
            $intFoucsPattern = $row["PATTERN_ID"];
            if( array_key_exists($intFoucsPattern, $aryMattersPerPattern) === false ){
                $aryMattersPerPattern[$intFoucsPattern] = array();
            }
            $aryMattersPerPattern[$intFoucsPattern][] = $row["MATTER_ID"];
        }
        //作業パターンに紐づく素材IDを取得。作業パターンごとにグルーピングして格納----

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

        // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
        foreach($aryVarsPerMatterId as $intMatterId=>$aryVarName){
            foreach($aryVarName as $strVarName=>$dummy){
                $aryVarIdPerVarNameFromFiles[$strVarName] = null;
                $aryVarIdPerVarNameFromFiles_fix[$strVarName] = null;
            }
        }

        //一時テーブル(1)役の変数から、変数名を重複を排除した形でリストアップする----

        $intFetchedFromAnsVarsTable = null;

        // T0007  aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
        $aryRowFromAnsVarsTable = array();

        $arrayConfig = $arrayConfigOfAnsVarsTable;
        $arrayValue = $arrayValueTmplOfAnsVarsTable;

        //$temp_array = array('WHERE'=>" VARS_NAME_ID = :VARS_NAME_ID AND DISUSE_FLAG = '0' ");
        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT",
                                             "VARS_NAME_ID",
                                             $strCurTableAnsVarsTable,
                                             $strJnlTableAnsVarsTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002100")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002200")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            $strForcusVarName = $row["VARS_NAME"];
            // T0007  aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
            $aryRowFromAnsVarsTable[$strForcusVarName] = $row;
        }
        // fetch行数を取得
        $intFetchedFromAnsVarsTable = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        //----実際にあるべき変数名をテーブルに反映させる【活性化】
        // aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
        $tmpAryKeysOfVarIdPerVarNameFromFiles = array_keys($aryVarIdPerVarNameFromFiles);
        foreach($tmpAryKeysOfVarIdPerVarNameFromFiles as $strVarName){
            $intVarNameId = null;
            $boolLoopNext = false;
            $strSqlType = null;

            // 子playbook/テンプレートの各ファイルで使用している変数が変数マスタにあるか確認
            // aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
            if( array_key_exists($strVarName, $aryRowFromAnsVarsTable) === true ){
                //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                $aryRowOfTableUpdate = $aryRowFromAnsVarsTable[$strVarName];
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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002300")) );
                    //存在しないはずの、値なので、想定外エラーに倒す。----
                }
                
                $strSqlType = "UPDATE";
                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002400")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfCurTableAnsVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002500")) );
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

            $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsVars,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002600")) );
            }
            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsVars, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002700")) );
            }
            $intJournalSeqNo = $retArray[0];

            $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $intJournalSeqNo;
            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";
            $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

            $arrayConfig = $arrayConfigOfAnsVarsTable;
            $arrayValue = $aryRowOfTableUpdate;
            $temp_array = array();

            // #0016 2016/08/15 Update start
            // DEBUGログに変更
            if ( $log_level === 'DEBUG' ){
// 更新ログ
ob_start();
var_dump($arrayValue);
$msgstr = ob_get_contents();
ob_clean();
LocalLogPrint(basename(__FILE__),__LINE__,"変数マスタ 更新($strSqlType)\n$msgstr");
            }
            $db_update_flg = true;   // DB更新をマーク

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
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002800")) );
            }
            if( $objQueryJnl->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00002900")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003000")) );
            }
            if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003100")) );
            }
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003200")) );
            }
            
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
            }
            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        unset($tmpAryKeysOfVarIdPerVarNameFromFiles);
        //実際にあるべき変数名をテーブルに反映させる【活性化】----

        $lva_var_value_tpf_vars_list       = array();
        $lva_use_VarsMaster_pkey_list      = array();
        $lva_use_PatternVarsLink_pkey_list = array();
        ////////////////////////////////////////////////////////////////
        // 代入値管理から具体値に設定されているテンプレート変数を取得
        ////////////////////////////////////////////////////////////////
        // 変数が有効でMovementが有効な代入値管理のデータを取得
        $sqlUtnBody = "SELECT                                                                  "
                     ."      TAB_A.PATTERN_ID,                                                 "
                     ."      TAB_A.VARS_ENTRY,                                                 "
                     ."      (                                                                 "
                     ."        SELECT                                                          "
                     ."          VARS_NAME_ID                                                  "
                     ."        FROM                                                            "
                     ."          B_ANSIBLE_LNS_VARS_MASTER                                     "
                     ."        WHERE                                                           "
                     ."          VARS_NAME_ID IN ( SELECT                                      "
                     ."                              VARS_NAME_ID                              "
                     ."                            FROM                                        "
                     ."                              B_ANS_LNS_PTN_VARS_LINK                   "
                     ."                            WHERE                                       "
                     ."                              VARS_LINK_ID = TAB_A.VARS_LINK_ID AND     "
                     ."                              DISUSE_FLAG = '0'                         "
                     ."                          ) AND                                         "
                     ."          DISUSE_FLAG = '0'                                             "
                     ."      ) VARS_NAME_ID,                                                   "
                     ."      (                                                                 "
                     ."        SELECT                                                          "
                     ."          PATTERN_ID                                                    "
                     ."        FROM                                                            "
                     ."          E_ANSIBLE_LNS_PATTERN                                         "
                     ."        WHERE                                                           "
                     ."          PATTERN_ID   = TAB_A.PATTERN_ID AND                           "
                     ."          DISUSE_FLAG  = '0'                                            "
                     ."      ) MAST_PATTERN_ID                                                      "
                     ."    FROM                                                                "
                     ."      B_ANSIBLE_LNS_VARS_ASSIGN TAB_A                                   "
                     ."    WHERE                                                               "
                     ."      TAB_A.VARS_ENTRY LIKE  '%{{ TPF_% }}%' AND                        "
                     ."      TAB_A.DISUSE_FLAG = '0'                                           "; 

        $arrayUtnBind = array();

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
        }
        $lv_var_value_tpf_vars_list = array();
        while ( $row = $objQueryUtn->resultFetch() ){
            if((@strlen($row['VARS_NAME_ID']) == 0) || (@strlen($row['MAST_PATTERN_ID']) == 0)){
                continue;
            }
            // テンプレート変数　{{ TPF_[a-zA-Z0-9_] }} を取出す
            $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$row['VARS_ENTRY'],$var_match);
            if(($ret !== false) && ($ret > 0)){
                foreach($var_match[0] as $tpf_var_name){
                    $ret = preg_match_all("/TPF_" . "[a-zA-Z0-9_]*/",$tpf_var_name,$var_name_match);
                    $tpf_var_name = trim($var_name_match[0][0]);
                    $lva_var_value_tpf_vars_list['TFP_VARS_LIST'][$tpf_var_name] = array();
                    $list = array();
                    $list['PATTERN_ID']   = $row['PATTERN_ID'];
                    $list['VARS_NAME_ID'] = $row['VARS_NAME_ID'];
                    $list['TPF_VAR_NAME'] = $tpf_var_name;
                    $lva_var_value_tpf_vars_list['PATTERN_LIST'][] = $list;
                    $lva_var_value_tpf_vars_list['VARS_LIST'] = array();
                }
            }
        }
        // DBアクセス事後処理
        unset($objQueryUtn);

        // テンプレートファイル内で使用されている変数を取得
        $ret = getVarsInTempfile($lva_var_value_tpf_vars_list,$aryTmplFilePerTmplVarName);
        // 戻りはチェックしない

        // テンプレートファイル内で使用されている変数を変数一覧に登録
        if(@count($lva_var_value_tpf_vars_list['VARS_LIST']) != 0){
            foreach($lva_var_value_tpf_vars_list['VARS_LIST'] as $var_name=>$dummy){
                $ret = AddVarsMasterTable($var_name,
                                          $strCurTableAnsVarsTable,
                                          $strJnlTableAnsVarsTable,
                                          $strSeqOfCurTableAnsVars,
                                          $strSeqOfJnlTableAnsVars,
                                          $arrayConfigOfAnsVarsTable,
                                          $arrayValueTmplOfAnsVarsTable,
                                          $db_access_user_id,
                                          $pkey);
                if($ret === false){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
                }
                // 変数一覧のPKey退避
                $lva_var_value_tpf_vars_list['VARS_LIST'][$var_name] = $pkey;
                // 具体値にテンプレート変数が登録されていて代入値管理からの登録データであることをマークする。
                $lva_use_VarsMaster_pkey_list[$pkey] = 0;
            }
        }

        //----存在しているレコードで、もう実際にない変数名を廃止する
        // aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
        foreach($aryRowFromAnsVarsTable as $strVarName=>$row){
            // 作業パターン変数紐付テーブル、を更新するときの準備として、変数名IDを代入。
            // T0006 $aryVarIdPerVarNameFromFiles [変数名(一意)]=変数マスタPkey (初期値 NULL)
            $aryVarIdPerVarNameFromFiles[$strVarName] = $row["VARS_NAME_ID"];

            // 具体値にテンプレート変数が登録されていて代入値管理からの登録データかチェック。
            if(@count($lva_use_VarsMaster_pkey_list[$row["VARS_NAME_ID"]]) != 0){
                continue;
            }

            // $aryVarIdPerVarNameFromFilesは未使用の変数が追加されるのでaryVarIdPerVarNameFromFiles_fixで変数の使用・未使用を判定
            if( array_key_exists($strVarName, $aryVarIdPerVarNameFromFiles_fix) !== true ){
                //----廃止する
                // aryRowFromAnsVarsTable:[変数名][変数マスタの各情報](変数マスタ)
                $aryRowOfTableUpdate = $aryRowFromAnsVarsTable[$strVarName];
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    // 最終更新者が自分以外の場合は廃止しない
                    if($aryRowOfTableUpdate["LAST_UPDATE_USER"] != $db_access_user_id){
if ( $log_level === 'DEBUG' ){
LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage("ITAANSIBLEH-STD-70040",
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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
                    //想定外エラー----
                }

                $intVarNameId = $aryRowOfTableUpdate["VARS_NAME_ID"];

                // テーブル　ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003500")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003600")) );
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
ob_start();
var_dump($arrayValue);
$msgstr = ob_get_contents();
ob_clean();
LocalLogPrint(basename(__FILE__),__LINE__,"変数マスタ　廃止($strSqlType)\n$msgstr");
                }
                $db_update_flg = true;   // DB更新をマーク

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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003700")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003800")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003900")) );
                }
                
                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004000")) );
                }
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004100")) );
                }
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004200")) );
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
        $intFetchedFromAnsPatternVarsLink = null;
        // T0008  aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
        $aryRowsPerPatternFromAnsPatternVarsLink = array();

        $arrayConfig = $arrayConfigOfAnsPatternVarsLink;
        $arrayValue  = $arrayValueTmplOfAnsPatternVarsLink;

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT",
                                            "VARS_LINK_ID",
                                             $strCurTableAnsPatternVarsLink,
                                             $strJnlTableAnsPatternVarsLink,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004400")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004500")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004600")) );
        }
        //----更新対象のテーブルから行を取得。作業パターンごとにグルーピングして格納
        while ( $row = $objQueryUtn->resultFetch() ){
            $intFoucsPattern = $row["PATTERN_ID"];
            $intFocusVarName = $row["VARS_NAME_ID"];
            // T0008  aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
            if( array_key_exists($intFoucsPattern, $aryRowsPerPatternFromAnsPatternVarsLink) === false ){
                $aryRowsPerPatternFromAnsPatternVarsLink[$intFoucsPattern] = array();
            }
            $aryRowsPerPatternFromAnsPatternVarsLink[$intFoucsPattern][$intFocusVarName] = $row;
        }
        //更新対象のテーブルから行を取得。作業パターンごとにグルーピングして格納----

        // fetch行数を取得
        $intFetchedFromAnsPatternVarsLink = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
        $aryVarNameIdsPerPattern = array();

        //----実際にあるべき組み合わせをテーブルに反映させる【活性化】
        //----作業パターンごとにループする
        // aryMattersPerPattern:[パターンID][array(子Playbook Pkey)](作業パターン詳細マスタベース)
        foreach($aryMattersPerPattern as $intPatternId=>$aryMatterId){
            // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
            $aryVarNameIdsPerPattern[$intPatternId] = array();
            //----素材IDごとにループする
            foreach($aryMatterId as $intMatterId){
                // 素材毎の変数一覧に該当素材が登録されているか確認
                // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
                if( array_key_exists($intMatterId, $aryVarsPerMatterId) === false ){
                    continue;
                }

                // 素材毎の変数一覧から該当素材の変数リストを取得
                // aryVarsPerMatterId:[Pkey][変数名]=1(素材マスタベース)
                $aryVarsOfFocusMatterId = $aryVarsPerMatterId[$intMatterId];

                //----変数名ごとにループする
                foreach($aryVarsOfFocusMatterId as $strVarName=>$dummy){
                    $intVarsLinkId = null;
                    $boolLoopNext = false;
                    $strSqlType = null;
                    
                    // 変数名IDを取得
                    // aryVarIdPerVarNameFromFiles:[変数名(一意)]=変数マスタPkey (初期値 NULL)(変数マスタベース)
                    $intVarNameId = $aryVarIdPerVarNameFromFiles[$strVarName];
                    
                    // 作業パターン+変数の情報を既に登録済みか判定
                    if( array_key_exists($intPatternId,$aryVarNameIdsPerPattern) === true ){
                        // 作業パターン+変数は登録済みなのでスキップ
                        if( array_key_exists($intVarNameId,$aryVarNameIdsPerPattern[$intPatternId]) === true ){
                            continue;
                        }
                    }

                    // T0004  aryVarNameIdsPerPattern:[パターンID][array([変数マスタPkey]=1)](作業パターン詳細ベース パターンID毎の変数一覧)
                    $aryVarNameIdsPerPattern[$intPatternId][$intVarNameId] = 1;

                    //----更新対象のテーブルのレコードに、存在するかを調べる
                    // 作業パターン変数紐付マスタにパターンID+変数IDが登録されているか判定
                    // aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                    if( isset($aryRowsPerPatternFromAnsPatternVarsLink[$intPatternId][$intVarNameId]) === true ){
                        //----更新対象のテーブルに存在した
                        // 作業パターン変数紐付マスタにパターンID+変数IDが登録されている
                        // aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                        $aryRowOfTableUpdate = $aryRowsPerPatternFromAnsPatternVarsLink[$intPatternId][$intVarNameId];
                        
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
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004700")) );
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
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004800")) );
                        }
                        // テーブル シーケンスNoを採番
                        $retArray = getSequenceValueFromTable($strSeqOfCurTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00004900")) );
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
                    // ジャーナルテーブル　ロック
                    $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsPatternVarsLink,'A_SEQUENCE');
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005000")) );
                    }
                    // ジャーナルテーブル シーケンスNoを採番
                    $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                    if( $retArray[1] != 0 ){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005100")) );
                    }
                    $intJournalSeqNo = $retArray[0];

                    $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                    $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                    $arrayConfig = $arrayConfigOfAnsPatternVarsLink;
                    $arrayValue  = $aryRowOfTableUpdate;
                    $temp_array  = array();

                    // #0016 2016/08/15 Update start
                    // DEBUGログに変更
                    if ( $log_level === 'DEBUG' ){
// 更新ログ
ob_start();
var_dump($arrayValue);
$msgstr = ob_get_contents();
ob_clean();
LocalLogPrint(basename(__FILE__),__LINE__,"作業パターン変数紐付マスタ  更新($strSqlType)\n$msgstr");
                    }

                    $db_update_flg = true;   // DB更新をマーク

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
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005200")) );
                    }
                    if( $objQueryJnl->getStatus()===false ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005300")) );
                    }
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005400")) );
                    }
                    if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005500")) );
                    }
                    $rUtn = $objQueryUtn->sqlExecute();
                    if($rUtn!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005600")) );
                    }
                    $rJnl = $objQueryJnl->sqlExecute();
                    if($rJnl!=true){
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryJnl->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005700")) );
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

        // テンプレートファイル内で使用されている変数を作業パターン変数紐付に登録
        if(@count($lva_var_value_tpf_vars_list['PATTERN_LIST']) != 0){
            foreach($lva_var_value_tpf_vars_list['PATTERN_LIST'] as $pattern_info){
                $pattern_id    = $pattern_info['PATTERN_ID'];
                $vars_name_id  = $pattern_info['VARS_NAME_ID'];
                $tpf_var_name  = $pattern_info['TPF_VAR_NAME'];
                if(@count($lva_var_value_tpf_vars_list['TFP_VARS_LIST'][$tpf_var_name]) == 0){
                   continue;
                }
                // 作業パターン変数紐付に登録されていないMovementと変数組合せは登録しない。
                if(@count($aryVarNameIdsPerPattern[$pattern_id][$vars_name_id]) == 0){
                    continue;
                }
                // テンプレート変数で使用している変数の情報を作業パターン変数紐付に登録。
                    foreach($lva_var_value_tpf_vars_list['TFP_VARS_LIST'][$tpf_var_name] as $var_name=>$dummy){
                    // 該当変数の変数一覧のPKey取得
                    $vars_master_pkey = $lva_var_value_tpf_vars_list['VARS_LIST'][$var_name];
                    $ret = AddPatternVarsLinkTable($pattern_id,
                                                   $vars_master_pkey,
                                                   $strCurTableAnsPatternVarsLink,     
                                                   $strJnlTableAnsPatternVarsLink,     
                                                   $strSeqOfCurTableAnsPatternVarsLink,
                                                   $strSeqOfJnlTableAnsPatternVarsLink,
                                                   $arrayConfigOfAnsPatternVarsLink,   
                                                   $arrayValueTmplOfAnsPatternVarsLink,
                                                   $db_access_user_id,
                                                   $pkey);
                    if($ret === false){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
                    }
                    // 具体値にテンプレート変数が登録されていて代入値管理からの登録データであることをマークする。
                    $lva_use_PatternVarsLink_pkey_list[$pkey] = 0;
                }
            }
        }

        //----存在しているレコードで、もう実際にない組み合わせを廃止する
        // 作業パターン変数紐付マスタの内容でループ
        // aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
        foreach($aryRowsPerPatternFromAnsPatternVarsLink as $intPatternId=>$aryRowsPerVarNameId){
            //----変数名IDごとにループする
            foreach($aryRowsPerVarNameId as $intVarNameId=>$row){
                // 作業パターン変数紐付マスタの情報取得
                // aryRowsPerPatternFromAnsPatternVarsLink:[パターンID][変数ID] = [作業パターン変数紐付の各情報](作業パターン変数紐付マスタ)
                $aryRowOfTableUpdate = $aryRowsPerPatternFromAnsPatternVarsLink[$intPatternId][$intVarNameId];

                // 具体値にテンプレート変数が登録されていて代入値管理からの登録データかチェック。
                if(@count($lva_use_PatternVarsLink_pkey_list[$aryRowOfTableUpdate["VARS_LINK_ID"]]) != 0){
                    continue;
                }

                $boolDisuseOnFlag = false;

                // 作業パターン詳細にパターンID+変数IDが登録されているか判定
                // aryMattersPerPattern:[パターンID][array(子Playbook Pkey)](作業パターン詳細マスタベース)
                if( array_key_exists($intPatternId, $aryMattersPerPattern) === false ){
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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005800")) );
                    //想定外エラー----
                }
                // ジャーナル ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableAnsPatternVarsLink,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00005900")) );
                }
                // ジャーナルテーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableAnsPatternVarsLink, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006000")) );
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
                $db_update_flg = true;   // DB更新をマーク

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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006100")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006200")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006300")) );
                }

                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006400")) );
                }
                
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006500")) );
                }
                
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00006600")) );
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
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
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
        // 関連データベースの更新反映完了を登録
        ///////////////////////////////////////////////////////////////////////////
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70054");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $ret = setBackyardExecuteComplete($lv_UpdateRecodeInfo);
        if($ret === false) {
            $error_flag = 1;
            $ary[90304] = "関連データベースの更新の反映完了の登録に失敗しました。";
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90304");
            throw new Exception($errorMsg);
        }
    
        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースを更新している場合、代入値自動登録設定のバックヤード起動を登録
        ///////////////////////////////////////////////////////////////////////////
        if($db_update_flg === true) {
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70055");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            $ret = setBackyardExecute($lv_a_proc_loaded_list_valsetup_pkey);
            if($ret === false) {
                $error_flag = 1;
                //$ary[90305] = "バックヤード処理(valautostup-workflow)起動の登録に失敗しました。";
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
        // playbookの有無や文法エラーなどが発生すると
        // 変数自動取得の常駐サービスが停止する。
        // 常駐プロセスが死なないようにした 
        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55268");
            require ($root_dir_path . $log_output_php );
        }        
        // playbookの有無や文法エラーなどが発生すると
        // 変数自動取得の常駐サービスが停止する。
        // 常駐プロセスが死なないようにした 
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

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        require ($root_dir_path . $log_output_php);
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   子PlayBookで使用している変数とテンプレートで使用されている変数を取得
    //   
    // パラメータ
    //   $in_filename:       子PlayBookファイル名(Legacy)
    //   $in_pkey:           子PlayBookファイル Pkey
    //   $ina_vars:          子PlayBookファイル内の変数配列返却
    //                       [変数名]
    //   $ina_aryTmplFilePerTmplVarName:
    //                       テンプレート管理情報配列
    //                       [テンプレート変数][Pkey] = テンプレートファイル
    //   $ina_global_vars_use_tpf_vars_list:  グローバル変数の具体値で使用している
    //                                        テンプレート変数リスト
    // 
    // 戻り値
    //   子PlayBookファイル名(Legacy)
    ////////////////////////////////////////////////////////////////////////////////
    function getHostVars($in_pkey,$in_filename,&$ina_vars,$ina_aryTmplFilePerTmplVarName,$ina_global_vars_use_tpf_vars_list){
        global          $log_level;
        global          $objMTS;
        global          $vg_playbook_contents_dir;
        global          $vg_template_contents_dir;

        $ina_vars     = array();
        $intNumPadding = 10;

        $chk_tfp_var_name_list = array();

        //////////////////////////////////////////////
        // 子PlayBookに登録されているテンプレート変数を抜出す。
        //////////////////////////////////////////////
        // 子Playbookファイルパス取得
        // 子Playbookファイル名は Pkey(10桁)-子Playbookファイル名 する。
        $file_name = sprintf("%s/%s/%s",
                             $vg_playbook_contents_dir,
                             str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                             $in_filename);

        // 子Playbookファイル名の存在チェック
        if( file_exists($file_name) === false ){
// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
            if($log_level == 'DEBUG')
            {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55210",array($in_pkey,basename($in_filename))); 
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            //これ以上処理続行できない
            return false;
        }
        //////////////////////////////////////////////
        // 子PlayBookに登録されている変数を抜出す。
        //////////////////////////////////////////////
        // 子PlayBookの内容読込
        $playbookdataString = file_get_contents($file_name);

        // 子PlayBookに登録されている変数を抜出。
        $local_vars = array();
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$playbookdataString,$local_vars);

        $aryResultParse = $objWSRA->getParsedResult();
        unset($objWSRA);

        // 子PlayBookに登録されている変数退避
        foreach( $aryResultParse[1] as $var_name ){
            // 変数名を一意にする。
            $ina_vars[$var_name] = 1;
        }

// <<<<<<<<<<pioneer/legacy差分箇所>>>>>>>>>>
        // 子PlayBookに登録されているテンプレート変数を抜出す。
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_TPF_HED,$playbookdataString);
        $aryResultParse = $objWSRA->getTPFvarsarrayResult();
        // $la_tpf_vars[行番号]=>テンプレート変数名
        $la_tpf_vars     = $aryResultParse[0];
        $la_tpf_errors   = $aryResultParse[1];
        unset($objWSRA);

        $result_code = true;
        // エラーが発生しているか確認
        if(count($la_tpf_errors) > 0){
            foreach( $la_tpf_errors as $line_no => $errcode ){
                if($log_level == 'DEBUG')
                {
                    //現在のエラーリスト
                    $msgstr = $objMTS->getSomeMessage($errcode,
                                                      array(basename($in_filename),
                                                            $line_no));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                $result_code = false;
            }
        }
        ///////////////////////////////////////////////////////////////////
        // テンプレート変数に紐づくテンプレートファイルの情報を取得
        ///////////////////////////////////////////////////////////////////
        foreach( $la_tpf_vars as $line_no => $tpf_var_name ){
            // WrappedStringReplaceAdmin(DF_HOST_TPF_HED,$playbookdataString) グローバル変数と一般変数も抜出すように修正
            // グローバル変数か一般変数の場合は処理スキップ
            $ret = preg_match_all('/(' . DF_HOST_VAR_HED . '|' . DF_HOST_GBL_HED . ')[a-zA-Z0-9_]*/',$tpf_var_name,$var_match1);
            if($ret == 1){
                continue;
            }

            // $ina_aryTmplFilePerTmplVarName:[テンプレート変数][Pkey] = テンプレートファイル(テンプレート管理マスタ)
            if( array_key_exists($tpf_var_name, $ina_aryTmplFilePerTmplVarName) === false ){
                // DEBUGログに変更
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55241",
                                                               array(basename($in_filename),
                                                               $line_no,
                                                               $tpf_var_name)); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }

                $result_code = false;
                continue;
            }
            
            // テンプレート情報取得
            // $ina_aryTmplFilePerTmplVarName:[テンプレート変数][Pkey] = テンプレートファイル(テンプレート管理マスタ)
            $tpf_info = $ina_aryTmplFilePerTmplVarName[$tpf_var_name];
            foreach( $tpf_info as $tpf_pkey => $tpf_file_name );
            // テンプレートファイル名が未登録の場合
            if((strlen($tpf_pkey) === 0 ) || 
               (strlen($tpf_file_name) === 0)){
                if($log_level == 'DEBUG')
                {
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55273",
                                                      array(basename($in_filename),
                                                            $line_no,
                                                            $tpf_var_name)); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                
                $result_code = false;
                continue;
            }
            //////////////////////////////////////////////////////////
            // テンプレートファイルに登録されている変数を抜出す。
            //////////////////////////////////////////////////////////
            // ITAで管理しているテンプレートファイルのパスを取得
            // テンプレートファイル名は Pkey(10桁)-子テンプレートファイル名 する。
            $file_name = sprintf("%s/%s/%s",
                                 $vg_template_contents_dir,
                                 str_pad( $tpf_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                                 $tpf_file_name);
            // テンプレートファイルの存在確認
            if( file_exists($file_name) === false ){
                if($log_level == 'DEBUG')
                {
                    //$ary[55239] = "システムで管理しているテンプレートファイル(｛｝:｛｝)が存在しない。";
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55239",
                                                      array($tpf_pkey,basename($tpf_file_name))); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                $result_code = false;
                continue;
            }

            // テンプレートファイルの内容読込
            $dataString = file_get_contents($file_name);
    
            // ホスト変数を抜出す
            $local_vars = array();
            $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$local_vars);
            $file_vars_list = $objWSRA->getTPFVARSParsedResult();
            unset($objWSRA);
    
            // テンプレートで使用している変数を退避
            foreach( $file_vars_list as $tfp_var ){
                // 子PlayBookに登録されている変数として退避
                // 変数名を一意とする。
                $ina_vars[$tfp_var] = 1;
            }
            
            // テンプレート情報取得済みのテンプレート変数更新
            $chk_tfp_var_name_list[$tpf_var_name] = 0;
        }

        ///////////////////////////////////////////////
        // グローバル変数をPlayBookから抜きす
        ///////////////////////////////////////////////
        $local_vars = array();
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_GBL_HED,$playbookdataString,$local_vars);
        $aryResultParse = $objWSRA->getParsedResult();
        $file_global_vars_list = $aryResultParse[1];

        unset($objWSRA);
        if(count($file_global_vars_list) != 0){
            foreach($file_global_vars_list as $global_var_name){
                // Playbookから抜き出したグローバル変数にテンプレート変数が使用されているか判定
                if(@count($ina_global_vars_use_tpf_vars_list[$global_var_name]) == 0){
                    continue; 
                }
                // グローバル変数に設定されているテンプレート変数がテンプレート管理に登録れれているか判定
                $tpf_var_name = $ina_global_vars_use_tpf_vars_list[$global_var_name];

                // テンプレート情報取得済みのテンプレート変数の場合
                if(@count($chk_tfp_var_name_list[$tpf_var_name]) != 0){
                    continue; 
                }
                $chk_tfp_var_name_list[$tpf_var_name] = 0;
                

                if( array_key_exists($tpf_var_name, $ina_aryTmplFilePerTmplVarName) === false ){
                    if ( $log_level === 'DEBUG' ){
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000032",
                                                                   array(basename($in_filename),
                                                                         $global_var_name,
                                                                         $tpf_var_name)); 
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    $result_code = false;
                    continue;
                }

                // テンプレート情報取得
                $tpf_info = $ina_aryTmplFilePerTmplVarName[$tpf_var_name];
                foreach( $tpf_info as $tpf_pkey => $tpf_file_name );
                // テンプレートファイル名が未登録の場合
                if((strlen($tpf_pkey) === 0 ) || 
                   (strlen($tpf_file_name) === 0)){
                    if($log_level == 'DEBUG')
                    {
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000033",
                                                                   array(basename($in_filename),
                                                                         $global_var_name,
                                                                         $tpf_var_name)); 
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    $result_code = false;
                    continue;
                }
                //////////////////////////////////////////////////////////
                // テンプレートファイルに登録されている変数を抜出す。
                //////////////////////////////////////////////////////////
                // ITAで管理しているテンプレートファイルのパスを取得
                // テンプレートファイル名は Pkey(10桁)-子テンプレートファイル名 する。
                $file_name = sprintf("%s/%s/%s",
                                     $vg_template_contents_dir,
                                     str_pad( $tpf_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                                     $tpf_file_name);
                // テンプレートファイルの存在確認
                if( file_exists($file_name) === false ){
                    if($log_level == 'DEBUG')
                    {
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55239",
                                                          array($tpf_pkey,basename($tpf_file_name))); 
                         LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    $result_code = false;
                    continue;
                }
         
                // テンプレートファイルの内容読込
                $dataString = file_get_contents($file_name);
    
                // ホスト変数を抜出す
                $local_vars = array();
                $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$local_vars);
                $file_vars_list = $objWSRA->getTPFVARSParsedResult();
                unset($objWSRA);
    
                // テンプレートで使用している変数を退避
                foreach( $file_vars_list as $tfp_var ){
                    // 子PlayBookに登録されている変数として退避
                    // 変数名を一意とする。
                    $ina_vars[$tfp_var] = 1;
                }
            }
        }
        return $result_code;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   テンプレートファイル内で使用されている変数を取得
    //   
    // パラメータ
    //   ina_var_value_tpf_vars_list:
    //                       ['TFP_VARS_LIST'][テンプレート変数] = 変数リスト
    //                       ['PATTERN_LIST'][作業パターン][テンプレート変数]=0
    //                       ['VARS_LIST'][変数]=0
    //   $ina_aryTmplFilePerTmplVarName:
    //                       テンプレート管理情報配列
    //                       [テンプレート変数][Pkey] = テンプレートファイル
    // 
    // 戻り値
    //   子PlayBookファイル名(Legacy)
    ////////////////////////////////////////////////////////////////////////////////
    function getVarsInTempfile(&$ina_var_value_tpf_vars_list,$ina_aryTmplFilePerTmplVarName){
        global          $log_level;
        global          $objMTS;
        global          $vg_template_contents_dir;

        $intNumPadding = 10;

        $result_code = true;
        ///////////////////////////////////////////////////////////////////
        // テンプレート変数に紐づくテンプレートファイルの情報を取得
        ///////////////////////////////////////////////////////////////////
        if(@count($ina_var_value_tpf_vars_list['TFP_VARS_LIST']) == 0){
            return true;
        }
        foreach( $ina_var_value_tpf_vars_list['TFP_VARS_LIST'] as $tpf_var_name=>$null_array){
            // テンプレート情報取得
            // $ina_aryTmplFilePerTmplVarName:[テンプレート変数][Pkey] = テンプレートファイル(テンプレート管理マスタ)
            if( ! isset($ina_aryTmplFilePerTmplVarName[$tpf_var_name])){
                if($log_level == 'DEBUG'){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000031",
                                                          array($tpf_var_name)); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                $result_code = false;
                continue;
            }
            $tpf_info = $ina_aryTmplFilePerTmplVarName[$tpf_var_name];
            foreach( $tpf_info as $tpf_pkey => $tpf_file_name );

            // テンプレートファイル名が未登録の場合
            if((strlen($tpf_pkey) === 0 ) || 
               (strlen($tpf_file_name) === 0)){
                if($log_level == 'DEBUG'){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-5000031",
                                                          array($tpf_var_name)); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                $result_code = false;
                continue;
            }
            //////////////////////////////////////////////////////////
            // テンプレートファイルに登録されている変数を抜出す。
            //////////////////////////////////////////////////////////
            // ITAで管理しているテンプレートファイルのパスを取得
            // テンプレートファイル名は Pkey(10桁)-子テンプレートファイル名 する。
            $file_name = sprintf("%s/%s/%s",
                                     $vg_template_contents_dir,
                                     str_pad( $tpf_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                                     $tpf_file_name);
            // テンプレートファイルの存在確認
            if( file_exists($file_name) === false ){
                if($log_level == 'DEBUG'){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-55239",
                                                           array($tpf_pkey,basename($tpf_file_name))); 
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                $result_code = false;
                continue;
            }
    
            // テンプレートファイルの内容読込
            $dataString = file_get_contents($file_name);
        
            // ホスト変数を抜出す
            $local_vars = array();
            $objWSRA = new WrappedStringReplaceAdmin("",$dataString,$local_vars);
            $file_vars_list = $objWSRA->getTPFVARSParsedResult();
            unset($objWSRA);

            // テンプレートで使用している変数を退避
            foreach( $file_vars_list as $var_name ){
                // 変数名を一意とする。
                $ina_var_value_tpf_vars_list['TFP_VARS_LIST'][$tpf_var_name][$var_name] = 0;
                $ina_var_value_tpf_vars_list['VARS_LIST'][$var_name] = 0;
            }
        }
        return $result_code;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   変数一覧に変数を登録
    //
    // パラメータ
    //   $in_var_name:                   変数名
    //   $in_strCurTable:                テーブル名
    //   $in_strJnlTable:                ジャーナルテーブル名
    //   $in_strSeqOfCurTable:           テーブル　シーケンス名
    //   $in_strSeqOfJnlTable:           ジャーナルテーブル シーケンス名
    //   $in_arrayConfig:                テーブル構造
    //   $in_arrayValue:                 テーブル構造
    //   $in_access_user_id:             テーブル更新者ID
    //   $ina_lstr_CTLDir:               ディレクトリパス情報
    //   &$in_pkey:                      削除対象外リスト
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function AddVarsMasterTable($in_var_name,
                                $in_strCurTable,
                                $in_strJnlTable,
                                $in_strSeqOfCurTable,
                                $in_strSeqOfJnlTable,
                                $in_arrayConfig,
                                $in_arrayValue,
                                $in_access_user_id,
                               &$in_pkey){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        global $db_update_flg;

        $temp_array = array('WHERE'=>"VARS_NAME = :VARS_NAME");

        $bind_array = array();
        $bind_array['VARS_NAME'] = $in_var_name;

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "VARS_NAME_ID",
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }

        $objQueryUtn->sqlBind($bind_array);

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $tgt_row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
            $action  = "INSERT";
            $tgt_row = $in_arrayValue;
        }
        else{
            $action = "UPDATE";
            // キー値が同値の場合は更新しない
            if($tgt_row["DISUSE_FLAG"] == "0"){
                $in_pkey = $tgt_row["VARS_NAME_ID"];

                return true;
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]     = $retArray[0];
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // 登録情報設定
            $tgt_row["VARS_NAME_ID"]       = $retArray[0];
            $tgt_row["VARS_NAME"]          = $in_var_name;
            $tgt_row["VARS_DESCRIPTION"]   = "";
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // ジャーナル情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];

        }

        $in_pkey = $tgt_row["VARS_NAME_ID"];

        $db_update_flg = true;   // DB更新をマーク

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "VARS_NAME_ID",
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   作業パターン変数紐付に変数を登録
    //
    // パラメータ
    //   $in_pattern_id:                 作業パターン
    //   $in_vars_master_pkey:           変数一覧 Pkey
    //   $in_strCurTable:                テーブル名
    //   $in_strJnlTable:                ジャーナルテーブル名
    //   $in_strSeqOfCurTable:           テーブル　シーケンス名
    //   $in_strSeqOfJnlTable:           ジャーナルテーブル シーケンス名
    //   $in_arrayConfig:                テーブル構造
    //   $in_arrayValue:                 テーブル構造
    //   $in_access_user_id:             テーブル更新者ID
    //   $in_pkey:                       削除対象外リスト
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function AddPatternVarsLinkTable($in_pattern_id,
                                     $in_vars_master_pkey,
                                     $in_strCurTable,
                                     $in_strJnlTable,
                                     $in_strSeqOfCurTable,
                                     $in_strSeqOfJnlTable,
                                     $in_arrayConfig,
                                     $in_arrayValue,
                                     $in_access_user_id,
                                    &$in_pkey){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;

        global $db_update_flg;

        $temp_array = array('WHERE'=>"PATTERN_ID = :PATTERN_ID AND VARS_NAME_ID = :VARS_NAME_ID");

        $bind_array = array();
        $bind_array['PATTERN_ID']   = $in_pattern_id;
        $bind_array['VARS_NAME_ID'] = $in_vars_master_pkey;

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "VARS_LINK_ID",
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $in_arrayConfig,
                                             $in_arrayValue,
                                             $temp_array);

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }

        $objQueryUtn->sqlBind($bind_array);

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            return false;
        }
        // fetch行数を取得
        $count = $objQueryUtn->effectedRowCount();
        $tgt_row = $objQueryUtn->resultFetch();
        unset($objQueryUtn);

        if ($count == 0){
            $action  = "INSERT";
            $tgt_row = $in_arrayValue;
        }
        else{
            $action = "UPDATE";
            // キー値が同値の場合は更新しない
            if($tgt_row["DISUSE_FLAG"] == "0"){
                $in_pkey = $tgt_row["VARS_LINK_ID"];
                return true;
            }
        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]     = $retArray[0];
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // 登録情報設定
            $tgt_row["VARS_LINK_ID"]       = $retArray[0];
            $tgt_row['PATTERN_ID']         = $in_pattern_id;
            $tgt_row['VARS_NAME_ID']       = $in_vars_master_pkey;
            $tgt_row["DISUSE_FLAG"]        = '0';
            $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($in_strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // ジャーナル情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];

        }

        $in_pkey = $tgt_row["VARS_LINK_ID"];

        $db_update_flg = true;   // DB更新をマーク

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "VARS_LINK_ID",
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(__FILE__,__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   グローバル変数の情報をデータベースより取得する。
    //
    // パラメータ
    //   $ina_global_vars_list:               グローバル変数のリスト
    //   $ina_global_vars_use_tpf_vars_list:  グローバル変数の具体値で使用している
    //                                        テンプレート変数リスト
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function getDBGlobalVarsMaster(&$ina_global_vars_list,&$ina_global_vars_use_tpf_vars_list){
        global $objMTS;
        global $objDBCA;
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

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        $ina_global_vars_list = array();
        $ina_global_vars_use_tpf_vars_list = array();

        while ( $row = $objQuery->resultFetch() ){
            /////////////////////////////////////////////////////////////
            // グローバル変数に設定されているテンプレート変数を抜き出す
            // テンプレート変数　{{ TPF_[a-zA-Z0-9_] }} を取出す
            /////////////////////////////////////////////////////////////
            $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$row['VARS_ENTRY'],$var_match);
            if(($ret !== false) && ($ret > 0)){
                foreach($var_match[0] as $var_name){
                    $ret = preg_match_all("/TPF_" . "[a-zA-Z0-9_]*/",$var_name,$var_name_match);
                    $var_name = trim($var_name_match[0][0]);
                    $ina_global_vars_use_tpf_vars_list[$row['VARS_NAME']] = $var_name;
                }
            }
            $ina_global_vars_list[$row['VARS_NAME']] = $row['VARS_ENTRY'];      
        }

        // DBアクセス事後処理
        unset($objQuery);
        
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
?>
