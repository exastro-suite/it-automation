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
//      CMDB代入値紐付対象メニュー取得
//
//  主要配列
//  $lva_use_menu_id_list:          代入値紐付対象メニューリスト
//                                  [メニューグループID_メニューID]
//  $lva_use_menu_col_id_list:      代入値紐付カラムリスト
//                                  [メニューグループID_メニューID_テーブル名_テーブルカラム名]
//  $lva_hide_col_list              代入値自動登録設定の項目表示から除外するカラムリスト
//                                  [カラム名]=1
//
//
//  F0001  makeArrayMenuID
//  F0002  makeArrayMenuColID
//  F0003  DBGetMenuList
//  F0004  addCMDBMenuTblDB
//  F0005  delCMDBMenuTblDB
//  F0006  addCMDBMenuColDB
//  F0007  delCMDBMenuColDB
//  F0008  getHideMenuColumnName
//
///////////////////////////////////////////////////////////////////////
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
$log_file = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";
ini_set('display_errors',0);
ini_set('log_errors',1);
ini_set('error_log',$log_file);

////////////////////////////////
// 作業実行単位のログ出力設定 //
////////////////////////////////
$log_exec_workflow_flg = false;
$log_exec_workflow_dir = "";

////////////////////////////////
// DB更新ユーザー設定         //
////////////////////////////////
$db_access_user_id   = -4;

////////////////////////////////
// 定数定義                   //
////////////////////////////////
$log_output_php       = '/libs/backyardlibs/backyard_log_output.php';
$php_req_gate_php     = '/libs/commonlibs/common_php_req_gate.php';
$db_connect_php       = '/libs/commonlibs/common_db_connect.php';
$getloadtableinfo_php = '/libs/commonlibs/common_getInfo_LoadTable.php';

////////////////////////////////////////////////////////////////
//----CMDB代入値紐付対象メニューリスト
////////////////////////////////////////////////////////////////
$strCurTableMenu           = "B_CMDB_MENU_LIST";
$strJnlTableMenu           = $strCurTableMenu . "_JNL";
$strSeqOfCurTableMenu      = $strCurTableMenu . "_RIC";
$strSeqOfJnlTableMenu      = $strCurTableMenu . "_JSQ";

$arrayConfigOfMenu = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "MENU_LIST_ID"=>""            ,
    "MENU_ID"=>""                 ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);

$arrayValueTmplOfMenu = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "MENU_LIST_ID"=>""            ,
    "MENU_ID"=>""                 ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);
//CMDB代入値紐付対象メニュー----

////////////////////////////////////////////////////////////////
//----CMDB代入値紐付対象メニュー管理
////////////////////////////////////////////////////////////////
$strCurTableMenuTbl        = "B_CMDB_MENU_TABLE";
$strJnlTableMenuTbl        = $strCurTableMenuTbl . "_JNL";
$strSeqOfCurTableMenuTbl   = $strCurTableMenuTbl . "_RIC";
$strSeqOfJnlTableMenuTbl   = $strCurTableMenuTbl . "_JSQ";

$arrayConfigOfMenuTbl = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "TABLE_ID"=>""                ,
    "MENU_ID"=>""                 ,
    "TABLE_NAME"=>""              ,
    "PKEY_NAME"=>""               ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);

$arrayValueTmplOfMenuTbl = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "TABLE_ID"=>""                ,
    "MENU_ID"=>""                 ,
    "TABLE_NAME"=>""              ,
    "PKEY_NAME"=>""               ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);
//CMDB代入値紐付対象メニュー管理

////////////////////////////////////////////////////////////////
//----CMDB代入値紐付対象メニューカラムリスト
////////////////////////////////////////////////////////////////
$strCurTableMenuCol      = "B_CMDB_MENU_COLUMN";
$strJnlTableMenuCol      = $strCurTableMenuCol . "_JNL";
$strSeqOfCurTableMenuCol = $strCurTableMenuCol . "_RIC";
$strSeqOfJnlTableMenuCol = $strCurTableMenuCol . "_JSQ";

$arrayConfigOfMenuCol = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "COLUMN_LIST_ID"=>""          ,
    "MENU_ID"=>""                 ,
    "COL_NAME"=>""                ,
    "COL_TITLE"=>""               ,
    "COL_TITLE_DISP_SEQ"=>""      ,
    "REF_TABLE_NAME"=>""          ,
    "REF_PKEY_NAME"=>""           ,
    "REF_COL_NAME"=>""            ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);

$arrayValueTmplOfMenuCol = array(
    "JOURNAL_SEQ_NO"=>""          ,
    "JOURNAL_ACTION_CLASS"=>""    ,
    "JOURNAL_REG_DATETIME"=>""    ,
    "COLUMN_LIST_ID"=>""          ,
    "MENU_ID"=>""                 ,
    "COL_NAME"=>""                ,
    "COL_TITLE"=>""               ,
    "COL_TITLE_DISP_SEQ"=>""      ,
    "REF_TABLE_NAME"=>""          ,
    "REF_PKEY_NAME"=>""           ,
    "REF_COL_NAME"=>""            ,
    "DISP_SEQ"=>""                ,
    "DISUSE_FLAG"=>""             ,
    "NOTE"=>""                    ,
    "LAST_UPDATE_TIMESTAMP"=>""   ,
    "LAST_UPDATE_USER"=>""
);
//CMDB代入値紐付対象メニュー----

////////////////////////////////
// ローカル変数(全体)宣言     //
////////////////////////////////
$warning_flag               = 0;        // 警告フラグ(1：警告発生)
$error_flag                 = 0;        // 異常フラグ(1：異常発生)
$cmdbMenuTableInsertCnt = 0;
$cmdbMenuTableUpdateCnt = 0;
$cmdbMenuTableDisuseCnt = 0;
$cmdbMenuColumnInsertCnt = 0;
$cmdbMenuColumnUpdateCnt = 0;
$cmdbMenuColumnDisuseCnt = 0;
//2019/01/15----

// 代入値自動登録設定の項目表示から除外するカラムリストファイル
$lv_hide_column_list_file = $root_dir_path . '/confs/backyardconfs/ita_base/hide_menu_column_list.txt';

try{

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );

    // 開始メッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = 'Start procedure';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require_once ($root_dir_path . $db_connect_php );

    ////////////////////////////////
    // loadtable読取モジュール    //
    ////////////////////////////////
    require_once ($root_dir_path . $getloadtableinfo_php );

    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = 'DB connect complete';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }

    //----2019/01/15
    ////////////////////////////////
    // 処理済みフラグを判定
    ////////////////////////////////
    $sql = "SELECT LOADED_FLG,LAST_UPDATE_TIMESTAMP " .
           "FROM A_PROC_LOADED_LIST " .
           "WHERE ROW_ID = :ROW_ID";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        $msgstr = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        $error_flag = 1;
        throw new Exception('SQL ERROR.');
    }

    $bindParam = array('ROW_ID'=>2100000501);
    $objQuery->sqlBind($bindParam);

    $r = $objQuery->sqlExecute();
    if (!$r){
        $msgstr = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        $error_flag = 1;
        throw new Exception('SQL ERROR.');
    }
    // FETCH行数を取得
    $procLoadList = array();
    while ( $row = $objQuery->resultFetch() ){
        $procLoadList[] = $row;
    }
    // DBアクセス事後処理
    unset($objQuery);

    if (1 != count($procLoadList)){
        $msgstr = 'The primary key 2100000501 data is missing in the A_PROC_LOADED_LIST table. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        $error_flag = 1;
        throw new Exception('SQL ERROR.');
    }

    if('1' == $procLoadList[0]['LOADED_FLG']){
        //処理対象がないため処理終了
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = 'End procedure (normal)';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        exit(0);
    }

    $procLastUpdateTimeStamp = $procLoadList[0]['LAST_UPDATE_TIMESTAMP'];

    ////////////////////////////////
    // トランザクション開始       //
    ////////////////////////////////
    if( $objDBCA->transactionStart()===false ){
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        throw new Exception('Start transaction has failed.');
    }
    
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = '[Process] Start transaction';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
    
    ///////////////////////////////////////////////////
    //                                               //
    // [0001] 関連シーケンスをロックする             //
    //                                               //
    //        デッドロック防止のために、昇順でロック //
    ///////////////////////////////////////////////////
    //----デッドロック防止のために、昇順でロック
    $aryTgtOfSequenceLock = array(
        $strSeqOfCurTableMenuTbl,
        $strSeqOfJnlTableMenuTbl,
        $strSeqOfCurTableMenuCol,
        $strSeqOfJnlTableMenuCol);
    
    // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
    asort($aryTgtOfSequenceLock);
    
    foreach($aryTgtOfSequenceLock as $strSeqName){
        //ジャーナルのシーケンス
        $retArray = getSequenceLockInTrz($strSeqName,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            // $ary[80002] = "シーケンスロックに失敗しました。";
            throw new Exception('Lock sequence has failed.');
        }
    }
    //デッドロック防止のために、昇順でロック----
    
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = '[Process] Get information of the associated menu';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }

    $lva_hide_col_list  = array();
    ////////////////////////////////////////////////////////////////////////////////
    // 代入値自動登録設定の項目表示から除外するカラムリストファイルからカラム名を取得
    ////////////////////////////////////////////////////////////////////////////////
    $ret = getHideMenuColumnName($lv_hide_column_list_file,$lva_hide_col_list);
    if($ret === false){
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = 'Get column information from the column list file which excludes display from the Substitution value auto-registration setting has failed. (file:{' . $lv_hide_column_list_file . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // P0001
    // CMDB内の代入値紐付対象メニューを抽出
    ///////////////////////////////////////////////////////////////////////////
    $lva_menu_list = array();
    $ret = DBGetMenuList($lva_menu_list);
    if($ret === false){
        // 異常フラグON  例外処理へ
        $error_flag = 1;
        throw new Exception('Get the associated menu group information has failed.');
    }

    // 代入値紐付対象メニューリストを初期化
    $lva_use_menu_id_list = array();
    // 代入値紐付カラムリストを初期化
    $lva_use_menu_col_id_list = array();

    foreach($lva_menu_list as $row){

        ///////////////////////////////////////////////////////////////////////////
        // P0002
        // CMDB内の代入値紐付対象メニューのカラム情報を取得
        ///////////////////////////////////////////////////////////////////////////
        $menu_id       = $row['MENU_ID'];


        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = '[Process] Get the menu of the associated menu column (MENU_ID:{' . $menu_id . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        // 改行コードが付いている場合に取り除く
        $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");
        $php_command = str_replace("\n","",$php_command);

        // メニューIDに対応する00_loadTable.phpにPHPの構文エラーなどが無いことを確認する。
        if ( $log_level === 'DEBUG' ){
            // 標準出力系をログファイルにリダイレクトする。
            $cmd = sprintf("%s %s%s %s >> %s 2>&1",
                           $php_command,
                           $root_dir_path,
                           "/backyards/ita_base/ky_loadtable_analysis.php",
                           $menu_id,
                           $log_file);
        }
        else{
            // 標準出力系を捨てる。
            $cmd = sprintf("%s %s%s %s > /dev/null 2>&1",
                           $php_command,
                           $root_dir_path,
                           "/backyards/ita_base/ky_loadtable_analysis.php",
                           $menu_id);
        }

        // プロセス起動
        $err = exec($cmd,$arry_out,$return_var);

        // トレースメッセージ
        if($return_var != 0){
            if ( $log_level === 'DEBUG' ){
                // 異常メッセージ
                $FREE_LOG = '00_loadTable.php of associated menu does not operate properly. Check the 00_loadTable.php. (MENU_ID:{' . $menu_id . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        
                LocalLogPrint(basename(__FILE__),__LINE__,"Exit code=[" . $return_var . "]");
            }
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }

        list($aryValue,
             $intErrorType,
             $strErrMsg) = getInfoOfLTUsingIdOfMenuForDBtoDBLink($menu_id,$objDBCA);

        if($intErrorType !== null){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'Get the associated menu column information has failed. (MENU_ID:{' . $menu_id . '} Error details:{' . $strErrMsg . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }
        // テーブル名
        if(@strlen($aryValue['TABLE_INFO']['UTN']['OBJECT_ID']) === 0){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'Table name is not set in the associated menu. (MENU_ID:{' . $menu_id . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }
        // テーブル名取得
        $table_name    = $aryValue['TABLE_INFO']['UTN']['OBJECT_ID'];

        // 主キー
        if(@strlen($aryValue['TABLE_INFO']['UTN']['ROW_INDENTIFY_COLUMN']) === 0){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'Primary key is not set in the associated menu. (MENU_ID:{' . $menu_id . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }
        // テーブル名取得
        $pkey_name    = $aryValue['TABLE_INFO']['UTN']['ROW_INDENTIFY_COLUMN'];

        // カラム名なし
        if(@count($aryValue['TABLE_IUD_COLUMNS']) === 0){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'Column is not set in the associated menu. (MENU_ID:{' . $menu_id . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }
        $host_hit = false;
        $ope_hit  = false;
        $error_hit = false;
        $lva_col_list = array();

        foreach($aryValue['TABLE_IUD_COLUMNS'] as $no=>$list){
            // カラム名空白確認
            if(@strlen($list[0]) == 0){
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = 'A blank column is set in the associated menu. (MENU_ID:{' . $menu_id . '})';
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                $warning_flag = 1;
                $error_hit = true;
            } 
            // カラムタイトル空白確認
            if(@strlen($list[1]) == 0){
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = 'A blank column is set in the associated menu. (MENU_ID:{' . $menu_id . '})';
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                $warning_flag = 1;
                $error_hit = true;
            }
            // カラムタイトルが256文字以内か判定
            if(@strlen($list[1]) > 256){
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = 'Item name of associated menu has exceeded the prescribed value (maximum 256 byte). (MENU_ID:{' . $list[1] . '} Item name:{' . $menu_id . '})';
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                $warning_flag = 1;
                $error_hit = true;
            }

            // ホストIDのカラムは登録対象外
            if($list[0] == "HOST_ID"){
                $host_hit = true;
                //次のカラムへ
                continue;
            }
            // オペレーションIDのカラムは登録対象外
            if($list[0] == "OPERATION_ID"){
                $ope_hit = true;
                //次のカラムへ
                continue;
            }
            if($error_hit === true){
                $warning_flag = 1;
                //次のカラムへ
                continue;
            }
            // カラム名重複登録確認
            if(@count($lva_col_list[$list[0]]) != 0){
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = 'A column with the same name is set in associated menu. (MENU_ID:{' . $list[0] . '} Column:{' . $menu_id . '})';
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                $warning_flag = 1;
                $error_hit = true;
                continue;
            }
            // カラム情報登録
            $lva_col_list[$list[0]] = array('COL_TITLE_DISP_SEQ'=>$no,'COL_TITLE'=>$list[1],'REF_TABLE_NAME'=>$list[2],'REF_PKEY_NAME'=>$list[3],'REF_COL_NAME'=>$list[4]);
        }
        if($host_hit === false){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'A host ID column is not set in the associated menu. (MENU_ID:{' . $menu_id . '} Column:HOST_ID)';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $error_hit = true;
        }
        if($ope_hit  === false){
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = 'Operation ID column is not set in the associated menu. (MENU_ID:{' . $menu_id . '} :OPERATION_ID)';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            $error_hit = true;
        }
        if($error_hit === true){
            $warning_flag = 1;
            //次のメニューへ
            continue;
        }
        ///////////////////////////////////////////////////////////////////////////
        // P0003
        // CMDB処理対象メニューテーブル管理の更新
        ///////////////////////////////////////////////////////////////////////////
        $ret = addCMDBMenuTblDB($strCurTableMenuTbl,      $strJnlTableMenuTbl,
                             $strSeqOfCurTableMenuTbl, $strSeqOfJnlTableMenuTbl,
                             $arrayConfigOfMenuTbl,    $arrayValueTmplOfMenuTbl,
                             $menu_id, $table_name,
                             $pkey_name,
                             $db_access_user_id);
        if($ret === false){
            $error_flag = 1;

            $FREE_LOG = 'Update the associated menu table list has failed. (MENU_ID:{' . $menu_id . '})';
            throw new Exception( $FREE_LOG );
        }
        // メニューグループIDとメニューIDの配列キー生成
        $make_menu_id = "";
        makeArrayMenuID($menu_id,$make_menu_id);

        // 代入値紐付対象メニューリストにメニューグループIDとメニューIDを記録
        $lva_use_menu_id_list[$menu_id] = 1;
        ///////////////////////////////////////////////////////////////////////////
        // P0004
        // CMDB処理対象メニューカラムの更新
        ///////////////////////////////////////////////////////////////////////////
        foreach($lva_col_list as $col_name=>$col_data){

            // 代入値自動登録設定の項目表示から除外するカラムリストに登録されているカラムは登録しない。
            if(@strlen($lva_hide_col_list[strtoupper($col_name)]) != 0){
                continue;
            }

            $ret = addCMDBMenuColDB($strCurTableMenuCol,      $strJnlTableMenuCol,
                                    $strSeqOfCurTableMenuCol, $strSeqOfJnlTableMenuCol,
                                    $arrayConfigOfMenuCol,    $arrayValueTmplOfMenuCol,
                                    $menu_id, $col_name, $col_data,                                     
                                    $db_access_user_id);
            if($ret === false){
                $error_flag = 1;
 
                $FREE_LOG = 'Update the associated menu column list has failed. (MENU_ID:{' . $menu_id . '} COLUMN:{' . $col_name . '})';
                throw new Exception( $FREE_LOG );
            }
            // メニューカラムIDの配列キー生成
            $make_menu_col_id = "";
            makeArrayMenuColID($menu_id, $col_name,$col_data['COL_TITLE_DISP_SEQ'],$make_menu_col_id);

            // 代入値紐付対象メニューカラムリストにメニューカラムIDを記録
            $lva_use_menu_col_id_list[$make_menu_col_id] = 1;

        }
    }

    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = '[Process] Delete the unnecessary menu from the Associated menu table list';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
    ///////////////////////////////////////////////////////////////////////////
    // P0005
    // CMDB処理対象メニューから対象外メニューを廃止
    ///////////////////////////////////////////////////////////////////////////
    $ret = delCMDBMenuTblDB($strCurTableMenuTbl,   $strJnlTableMenuTbl,
                         $strSeqOfCurTableMenuTbl, $strSeqOfJnlTableMenuTbl,
                         $arrayConfigOfMenuTbl,    $arrayValueTmplOfMenuTbl,
                         $lva_use_menu_id_list, $db_access_user_id);

    if($ret === false){
        $error_flag = 1;
 
        $FREE_LOG = 'Discard the unnecessary menu from associated menu table list has failed.';
        throw new Exception( $FREE_LOG );
    }

    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = '[Process] Delete the unnecessary menu from the Associated menu column list';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
    ///////////////////////////////////////////////////////////////////////////
    // P0006
    // CMDB処理対象メニューカラムから対象外メニューを廃止
    ///////////////////////////////////////////////////////////////////////////
    $ret = delCMDBMenuColDB($strCurTableMenuCol,      $strJnlTableMenuCol,
                            $strSeqOfCurTableMenuCol, $strSeqOfJnlTableMenuCol,
                            $arrayConfigOfMenuCol,    $arrayValueTmplOfMenuCol,
                            $lva_use_menu_col_id_list,$db_access_user_id);

    if($ret === false){
        $error_flag = 1;
 
        $FREE_LOG = 'Discard the unnecessary menu column from associated menu column list has failed.';
        throw new Exception( $FREE_LOG );
    }

    ////////////////////////////////
    // 処理済みフラグをONにする
    ////////////////////////////////
    $sql = "UPDATE A_PROC_LOADED_LIST " .
           "SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP " .
           "WHERE ROW_ID = :ROW_ID AND LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP2 ";

    $objQuery = $objDBCA->sqlPrepare($sql);

    if( $objQuery->getStatus() === false ){
        $error_flag = 1;

        $FREE_LOG = 'DB access error occurred.';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        throw new Exception( $FREE_LOG );
    }

    $objDBCA->setQueryTime();
    $bindParam = array('LOADED_FLG'=>'1', 'ROW_ID'=>2100000501, 'LAST_UPDATE_TIMESTAMP'=>$objDBCA->getQueryTime(), 'LAST_UPDATE_TIMESTAMP2'=>$procLastUpdateTimeStamp);
    $objQuery->sqlBind($bindParam);

    $r = $objQuery->sqlExecute();

    if (!$r){
        $error_flag = 1;

        $FREE_LOG = 'DB access error occurred.';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        throw new Exception( $FREE_LOG );
    }
    unset($objQuery);

    ////////////////////////////////
    // 登録/更新/廃止/復活があった場合、代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
    ////////////////////////////////
    if(0 != $cmdbMenuTableInsertCnt ||
       0 != $cmdbMenuTableUpdateCnt ||
       0 != $cmdbMenuTableDisuseCnt ||
       0 != $cmdbMenuColumnInsertCnt ||
       0 != $cmdbMenuColumnUpdateCnt ||
       0 != $cmdbMenuColumnDisuseCnt){

        if(file_exists($root_dir_path . "/libs/release/ita_ansible-driver")){

            $sql = "UPDATE A_PROC_LOADED_LIST "
                   ."SET LOADED_FLG=:LOADED_FLG, LAST_UPDATE_TIMESTAMP=:LAST_UPDATE_TIMESTAMP "
                   ."WHERE ROW_ID IN (2100020002, 2100020004, 2100020006)";

            $objQuery = $objDBCA->sqlPrepare($sql);

            if( $objQuery->getStatus() === false ){
                $error_flag = 1;

                //$ary[80000] = "ＤＢアクセス異常([FILE]｛｝[LINE]｛｝)"
                $FREE_LOG = 'DB access error occurred.';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                throw new Exception( $FREE_LOG );
            }

            $objDBCA->setQueryTime();
            $bindParam = array('LOADED_FLG'=>'0', 'LAST_UPDATE_TIMESTAMP'=>$objDBCA->getQueryTime());
            $objQuery->sqlBind($bindParam);

            $r = $objQuery->sqlExecute();

            if (!$r){
                $error_flag = 1;

                //$ary[80000] = "ＤＢアクセス異常([FILE]｛｝[LINE]｛｝)"
                $FREE_LOG = 'DB access error occurred.';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                throw new Exception( $FREE_LOG );
            }
            unset($objQuery);
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
        throw new Exception('Commit transaction has failed.');
    }

    ////////////////////////////////
    // トランザクション終了       //
    ////////////////////////////////
    $objDBCA->transactionExit();

    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = '[Process] End transaction';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
}
catch (Exception $e){

    $FREE_LOG = 'An exception occurred.';
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
            $FREE_LOG = '[Process] Rollback';
        }
        else{
            $FREE_LOG = 'Rollback has failed.';
        }
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        
        // トランザクション終了
        if( $objDBCA->transactionExit()=== true ){
            $FREE_LOG = '[Process] End transaction';
        }
        else{
            $FREE_LOG = 'An error occurred at the time of ending the transaction.';
        }
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
}

////////////////////////////////
//// 結果出力               ////
////////////////////////////////
// 処理結果コードを判定してアクセスログを出し分ける
if( $error_flag != 0 ){
    // 終了メッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = 'End procedure (error)';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
    exit(0);
}
elseif( $warning_flag != 0 ){
    // 終了メッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = 'End procedure (warning)';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }        
    exit(0);
}
else{
    // 終了メッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = 'End procedure (normal)';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }
    exit(0);
}
////////////////////////////////////////////////////////////////////////////////
// F0001
// 処理内容
//   メニューIDの配列キー生成
//
// パラメータ
//   $in_menu_id:             メニューID
//   $in_make_menu_id:        生成したメニューIDの配列キー
// 戻り値
//   なし
////////////////////////////////////////////////////////////////////////////////
function makeArrayMenuID($in_menu_id,&$in_make_menu_id){
    $in_make_menu_id = $in_menu_id;
}

////////////////////////////////////////////////////////////////////////////////
// F0002
// 処理内容
//   メニューIDとカラム名の配列キー生成
//
// パラメータ
//   $in_menu_id:             メニューID
//   $in_col_name:            カラム名
//   $in_col_title_disp_seq:  カラム位置(表示順)
//   $in_make_menu_col_id:    生成したメニューIDとカラム名の配列キー
// 戻り値
//   なし
////////////////////////////////////////////////////////////////////////////////
function makeArrayMenuColID($in_menu_id,$in_col_name,$in_col_title_disp_seq,&$in_make_menu_col_id){
    $in_make_menu_col_id = $in_menu_id . '_' . $in_col_name . '_' . $in_col_title_disp_seq;
}

////////////////////////////////////////////////////////////////////////////////
// F0003
// 処理内容
//   CMDB内の代入値紐付対象メニューを抽出
//
// パラメータ
//   $in_menu_info:       インターフェース情報返却領域
// 戻り値
//   true:   正常
//   false:  異常
////////////////////////////////////////////////////////////////////////////////
function DBGetMenuList(&$in_menu_info){
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $objMTS;
    global $objDBCA;

    $in_menu_info = array();
    // CMDB内の代入値紐付対象メニューを抽出
    $sql = "SELECT                          \n" .
           "  TAB_A.MENU_ID        MENU_ID  \n" .
           "FROM                            \n" .
           "  B_CMDB_MENU_LIST TAB_A        \n" .
           "WHERE                           \n" .
           "  TAB_A.DISUSE_FLAG = '0'       \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        $msgstr = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        return false;
    }

    $r = $objQuery->sqlExecute();
    if (!$r){
        $msgstr = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    while ( $row = $objQuery->resultFetch() ){
        $in_menu_info[] = $row;
    }
    // DBアクセス事後処理
    unset($objQuery);

    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F0004
// 処理内容
//   CMDB処理対象メニューテーブル管理を更新する。
//   
// パラメータ
//   $in_strCurTable:                テーブル名  
//   $in_strJnlTable:                ジャーナルテーブル名
//   $in_strSeqOfCurTable:           理テーブルシーケンス名
//   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
//   $in_arrayConfig:                項目リスト 
//   $in_arrayValue:                 更新用項目リスト
//   $in_menu_id:                    メニューID
//   $in_tablename:                  テーブル名
//   $in_pkeyname:                   PKey名
//   $in_access_user_id:             最終更新ユーザーID
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addCMDBMenuTblDB($in_strCurTable,           $in_strJnlTable,
                          $in_strSeqOfCurTable,      $in_strSeqOfJnlTable,
                          $in_arrayConfig,           $in_arrayValue,
                          $in_menu_id, $in_tablename,
                          $in_pkeyname,
                          $in_access_user_id){
    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;
    global    $cmdbMenuTableInsertCnt;
    global    $cmdbMenuTableUpdateCnt;

    $strCurTable      = $in_strCurTable;
    $strJnlTable      = $in_strJnlTable;

    $arrayConfig      = $in_arrayConfig;
    $arrayValue       = $in_arrayValue;

    $strSeqOfCurTable = $in_strSeqOfCurTable;
    $strSeqOfJnlTable = $in_strSeqOfJnlTable;

    $temp_array = array('WHERE'=>"MENU_ID = :MENU_ID");
    
    $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                         "SELECT FOR UPDATE", 
                                         "TABLE_ID", 
                                         $strCurTable, 
                                         $strJnlTable, 
                                         $arrayConfig, 
                                         $arrayValue, 
                                         $temp_array );
    
    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];
    
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    if( $objQueryUtn->getStatus()===false ){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        return false;
    }

    $objQueryUtn->sqlBind( array('MENU_ID'=>$in_menu_id));
    
    $r = $objQueryUtn->sqlExecute();
    if (!$r){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        return false;
    }
    // fetch行数を取得
    $count = $objQueryUtn->effectedRowCount();
    $row = $objQueryUtn->resultFetch();

    unset($objQueryUtn);

    if ($count == 0){
        $action  = "INSERT";
        $tgt_row = $arrayValue;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = '[Process] Add the menu of the Associated menu table list (MENU_ID:{' . $in_menu_id . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
    }
    else{
        if($row['DISUSE_FLAG'] == '1'){
            // 廃止なので復活する。
            $action = "UPDATE";
            $tgt_row = $row;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $ary[70002] = "[処理]CMDB代入値紐付メニュー復活 MENU_ID：｛｝";
                $FREE_LOG = '[Process] Restore the menu of the Associated menu table list (MENU_ID:{' . $in_menu_id . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
        else{
            // テーブル名とPkeyが変更になっているか判定する。
            if($row["TABLE_NAME"]  == $in_tablename &&
               $row["PKEY_NAME"]   == $in_pkeyname ){
                //同一みなので処理終了
                return true;
            }
            $action = "UPDATE";
            $tgt_row = $row;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = '[Process] Update the menu of the Associated menu table list (MENU_ID:{' . $in_menu_id . '} TBALE:{' . $in_tablename . '})';
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
    }
    if($action == "UPDATE"){
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
        $tgt_row["MENU_ID"]          = $in_menu_id;
        $tgt_row["TABLE_NAME"]       = $in_tablename;
        $tgt_row["PKEY_NAME"]        = $in_pkeyname;
        $tgt_row["DISUSE_FLAG"]      = '0';
        $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

        $cmdbMenuTableUpdateCnt++;
    }
    else{
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスをロック                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスを採番                                   //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }

        // 登録する情報設定
        $tgt_row["TABLE_ID"]         = $retArray[0];
        $tgt_row["MENU_ID"]          = $in_menu_id;
        $tgt_row["TABLE_NAME"]       = $in_tablename;
        $tgt_row["PKEY_NAME"]        = $in_pkeyname;
        $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;
        $tgt_row["DISUSE_FLAG"]      = '0';

        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        
        // ロール管理ジャーナルに登録する情報設定
        $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
        $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        $cmdbMenuTableInsertCnt++;
    }

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "TABLE_ID",
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
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
        $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    $rUtn = $objQueryUtn->sqlExecute();
    if($rUtn!=true){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    $rJnl = $objQueryJnl->sqlExecute();
    if($rJnl!=true){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
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
// F0005
// 処理内容
//   CMDB処理対象メニューテーブル管理から対象外メニューを廃止
//   
// パラメータ
//   $in_strCurTable:                テーブル名  
//   $in_strJnlTable:                ジャーナルテーブル名
//   $in_strSeqOfCurTable:           テーブルシーケンス名
//   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
//   $in_arrayConfig:                項目リスト 
//   $in_arrayValue:                 更新用項目リスト
//   $ina_use_menu_id_list:          登録が必要なメニューIDのリスト
//   $in_access_user_id:             最終更新ユーザーID
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function delCMDBMenuTblDB($in_strCurTable,           $in_strJnlTable,
                          $in_strSeqOfCurTable,      $in_strSeqOfJnlTable,
                          $in_arrayConfig,           $in_arrayValue,
                          $ina_use_menu_id_list,     $in_access_user_id){
    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;
    global    $cmdbMenuTableDisuseCnt;

    $strPkey                = "TABLE_ID";
    $strCurTable            = $in_strCurTable;
    $strJnlTable            = $in_strJnlTable;
    $strSeqOfCurTable       = $in_strSeqOfCurTable;
    $strSeqOfJnlTable       = $in_strSeqOfJnlTable;
    $arrayConfig            = $in_arrayConfig;
    $arrayValue             = $in_arrayValue;

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
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn_sel->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn_sel);
        return false;
    }

    $objQueryUtn_sel->sqlBind($arrayUtnBind);

    $r = $objQueryUtn_sel->sqlExecute();
    if (!$r){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn_sel->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn_sel);
        return false;
    }
    // fetch行数を取得
    while ( $tgt_row = $objQueryUtn_sel->resultFetch() ){
        // メニューIDの配列キー生成
        $menu_id       = $tgt_row["MENU_ID"];
        $make_menu_id = "";
        makeArrayMenuID($menu_id,$make_menu_id);

        // メニューグループIDとメニューIDが登録されているか判定
        if(@strlen($ina_use_menu_id_list[$make_menu_id]) !== 0){
            // 登録されている場合はなにもしない。
            continue;
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $ary[70004] = "[処理]CMDB代入値紐付メニュー廃止 MENU_ID：｛｝";
            $FREE_LOG = '[Process] Discard the menu from the Associated menu table list (MENU_ID:{' . $menu_id . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        
        // 登録されていない場合は廃止レコードにする。
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            return false;
        }

        $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
        $tgt_row["DISUSE_FLAG"]      = '1';
        $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

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
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);

        $cmdbMenuTableDisuseCnt++;
    }
    unset($objQueryUtn_sel);
    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F0006
// 処理内容
//   CMDB処理対象メニューカラム一覧を更新する。
//   
// パラメータ
//   $in_strCurTable:                テーブル名  
//   $in_strJnlTable:                ジャーナルテーブル名
//   $in_strSeqOfCurTable:           理テーブルシーケンス名
//   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
//   $in_arrayConfig:                項目リスト 
//   $in_arrayValue:                 更新用項目リスト
//   $in_menu_id:                    メニューID
//   $in_col_name:                   カラム名
//   $in_col_data:                   カラムデータ
//   $in_access_user_id:             最終更新ユーザーID
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addCMDBMenuColDB($in_strCurTable,           $in_strJnlTable,
                          $in_strSeqOfCurTable,      $in_strSeqOfJnlTable,
                          $in_arrayConfig,           $in_arrayValue,
                          $in_menu_id, $in_col_name, $in_col_data,
                          $in_access_user_id){
    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;
    global    $cmdbMenuColumnInsertCnt;
    global    $cmdbMenuColumnUpdateCnt;

    $strCurTable      = $in_strCurTable;
    $strJnlTable      = $in_strJnlTable;

    $arrayConfig      = $in_arrayConfig;
    $arrayValue       = $in_arrayValue;

    $strSeqOfCurTable = $in_strSeqOfCurTable;
    $strSeqOfJnlTable = $in_strSeqOfJnlTable;

    // 表示順は条件に設定しない。
    $temp_array = array('WHERE'=>"MENU_ID       = :MENU_ID       AND " .
                                 "COL_NAME      = :COL_NAME");
    
    $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                         "SELECT FOR UPDATE", 
                                         "COLUMN_LIST_ID", 
                                         $strCurTable, 
                                         $strJnlTable, 
                                         $arrayConfig, 
                                         $arrayValue, 
                                         $temp_array );
    
    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];
    
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    if( $objQueryUtn->getStatus()===false ){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        return false;
    }

    $objQueryUtn->sqlBind( array('MENU_ID'=>$in_menu_id,'COL_NAME'=>$in_col_name));
    
    $r = $objQueryUtn->sqlExecute();
    if (!$r){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        return false;
    }
    // fetch行数を取得
    $count = $objQueryUtn->effectedRowCount();
    $row = $objQueryUtn->resultFetch();
    unset($objQueryUtn);

    if ($count == 0){
         $action  = "INSERT";
         $tgt_row = $arrayValue;

         // トレースメッセージ
         if ( $log_level === 'DEBUG' ){
             $FREE_LOG = '[Process] Add the menu of the Associated menu column list (MENU_ID:{' . $in_menu_id . '} COLUMN:{' . $in_col_name . '})';
             LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
         }
    }
    else{
        if($row['DISUSE_FLAG'] == '1'){
             // 廃止なので復活する。
             $action = "UPDATE";
             $tgt_row = $row;

             // トレースメッセージ
             if ( $log_level === 'DEBUG' ){
                 $FREE_LOG = '[Process] Restore the menu of the Associated menu column list (MENU_ID:{' . $in_menu_id . '} COLUMN:{' . $in_col_name . '})';
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
             }
        }
        else{
             // カラムデータが変更になっているか判定する。
             if($row['COL_TITLE']          == $in_col_data['COL_TITLE'] &&
                $row['COL_TITLE_DISP_SEQ'] == $in_col_data['COL_TITLE_DISP_SEQ'] &&
                $row['REF_TABLE_NAME']     == $in_col_data['REF_TABLE_NAME'] &&
                $row['REF_PKEY_NAME']      == $in_col_data['REF_PKEY_NAME'] &&
                $row['REF_COL_NAME']       == $in_col_data['REF_COL_NAME']){

                 //同一なので処理終了
                 return true;
             }
             $action = "UPDATE";
             $tgt_row = $row;

             // トレースメッセージ
             if ( $log_level === 'DEBUG' ){
                 $FREE_LOG = '[Process] Update the menu of the Associated menu column list (MENU_ID:{' . $in_menu_id . '} COLUMN:{' . $in_col_name . '})';
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
             }
        }
    }
    if($action == "UPDATE"){
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }

        $tgt_row["JOURNAL_SEQ_NO"]     = $retArray[0];
        $tgt_row["MENU_ID"]            = $in_menu_id;
        $tgt_row["COL_NAME"]           = $in_col_name;
        $tgt_row["COL_TITLE"]          = $in_col_data['COL_TITLE'];
        $tgt_row['COL_TITLE_DISP_SEQ'] = $in_col_data['COL_TITLE_DISP_SEQ'];
        $tgt_row["REF_TABLE_NAME"]     = $in_col_data['REF_TABLE_NAME'];
        $tgt_row["REF_PKEY_NAME"]      = $in_col_data['REF_PKEY_NAME'];
        $tgt_row["REF_COL_NAME"]       = $in_col_data['REF_COL_NAME'];
        $tgt_row["DISUSE_FLAG"]        = '0';
        $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;
        
        $cmdbMenuColumnUpdateCnt++;
    }
    else{
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスをロック                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // テーブルシーケンスを採番                                   //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }

        // 登録する情報設定
        $tgt_row["COLUMN_LIST_ID"]     = $retArray[0];
        $tgt_row["MENU_ID"]            = $in_menu_id;
        $tgt_row["COL_NAME"]           = $in_col_name;
        $tgt_row["COL_TITLE"]          = $in_col_data['COL_TITLE'];
        $tgt_row['COL_TITLE_DISP_SEQ'] = $in_col_data['COL_TITLE_DISP_SEQ'];
        $tgt_row["REF_TABLE_NAME"]     = $in_col_data['REF_TABLE_NAME'];
        $tgt_row["REF_PKEY_NAME"]      = $in_col_data['REF_PKEY_NAME'];
        $tgt_row["REF_COL_NAME"]       = $in_col_data['REF_COL_NAME'];
        $tgt_row["LAST_UPDATE_USER"]   = $in_access_user_id;
        $tgt_row["DISUSE_FLAG"]        = '0';

        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            return false;
        }
        
        // ロール管理ジャーナルに登録する情報設定
        $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
        $tgt_row["LAST_UPDATE_USER"]     = $in_access_user_id;

        $cmdbMenuColumnInsertCnt++;
    }

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "COLUMN_LIST_ID",
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
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
        $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    $rUtn = $objQueryUtn->sqlExecute();
    if($rUtn!=true){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn);
        unset($objQueryJnl);
        return false;
    }
    
    $rJnl = $objQueryJnl->sqlExecute();
    if($rJnl!=true){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
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
// F0007
// 処理内容
//   CMDB処理対象メニューカラム一覧から対象外メニューを廃止
//   
// パラメータ
//   $in_strCurTable:                テーブル名  
//   $in_strJnlTable:                ジャーナルテーブル名
//   $in_strSeqOfCurTable:           テーブルシーケンス名
//   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
//   $in_arrayConfig:                項目リスト 
//   $in_arrayValue:                 更新用項目リスト
//   $ina_use_menu_col_id_list:      登録が必要なメニューグループIDとメニューID
//                                   テーブル名とカラム名の組合せ
//   $in_access_user_id:             最終更新ユーザーID
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function delCMDBMenuColDB($in_strCurTable,           $in_strJnlTable,
                          $in_strSeqOfCurTable,      $in_strSeqOfJnlTable,
                          $in_arrayConfig,           $in_arrayValue,
                          $ina_use_menu_col_id_list, $in_access_user_id){
    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;
    global    $cmdbMenuColumnDisuseCnt;

    $strPkey                = "COLUMN_LIST_ID";
    $strCurTable            = $in_strCurTable;
    $strJnlTable            = $in_strJnlTable;
    $strSeqOfCurTable       = $in_strSeqOfCurTable;
    $strSeqOfJnlTable       = $in_strSeqOfJnlTable;
    $arrayConfig            = $in_arrayConfig;
    $arrayValue             = $in_arrayValue;

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
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn_sel->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn_sel);
        return false;
    }

    $objQueryUtn_sel->sqlBind($arrayUtnBind);

    $r = $objQueryUtn_sel->sqlExecute();
    if (!$r){
        $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        $FREE_LOG = $objQueryUtn_sel->getLastError();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        unset($objQueryUtn_sel);
        return false;
    }
    // fetch行数を取得
    while ( $tgt_row = $objQueryUtn_sel->resultFetch() ){
        // メニューグループIDとメニューIDとカラム名の配列キー生成
        $menu_id            = $tgt_row["MENU_ID"];
        $col_name           = $tgt_row["COL_NAME"];
        $col_title_disp_seq = $tgt_row["COL_TITLE_DISP_SEQ"];
        $make_menu_col_id = "";
        makeArrayMenuColID($menu_id,$col_name,$col_title_disp_seq,$make_menu_col_id);

        // メニューグループIDとメニューIDが登録されているか判定
        if(@strlen($ina_use_menu_col_id_list[$make_menu_col_id]) !== 0){
            // 登録されている場合はなにもしない。
            continue;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = '[Process] Discard the menu from the Associated menu column list (MENU_ID:{' . $menu_id . '} COLUMN:{' . $col_name . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        // 登録されていない場合は廃止レコードにする。
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスをロック                               //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            return false;
        }
        ////////////////////////////////////////////////////////////////
        // ジャーナルシーケンスを採番                                 //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            return false;
        }

        $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
        $tgt_row["DISUSE_FLAG"]      = '1';
        $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

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
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = 'DB access error occurred. (file:{' . basename(__FILE__) . '}line:{' . __LINE__ . '})';
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn_sel);
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);

        $cmdbMenuColumnDisuseCnt++;
    }
    unset($objQueryUtn_sel);
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
////////////////////////////////////////////////////////////////////////////////
// F0008
// 処理内容
//   代入値自動登録設定の項目表示から除外するカラムリストファイルから
//   カラム名を取得する。
//
// パラメータ
//   $in_file:                       非表示カラムリストファイル
//   $ina_hide_col_name:             非表示カラムリスト
//                                   [カラム名]=主キー名
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function getHideMenuColumnName($in_file,&$ina_hide_col_name){
    $ina_hide_col_name = array();
    $strSourceString = file_get_contents($in_file);
    if($strSourceString === false){
        return false;
    }
    // 入力データを行単位に分解
    $arry_list = explode("\n",$strSourceString);
    $line = 0;
    foreach($arry_list as $strSourceString){
        $col_name = trim($strSourceString);
        // 空行は読み飛ばす
        if(strlen($col_name) == 0){
            continue;
        }
        // コメント行は読み飛ばす
        if(mb_strpos($col_name,"#",0,"UTF-8") === 0){
            continue;
        }
        // HOST_IDとOPERATION_IDの場合は除外
        switch(strtoupper($col_name)){
        case 'HOST_ID':
        case 'OPERATION_ID':
            continue 2;
        }
        // 非表示のカラムを退避する。一応大文字で統一
        $ina_hide_col_name[strtoupper($col_name)] = 0;
    }
    return true;
}
?>
