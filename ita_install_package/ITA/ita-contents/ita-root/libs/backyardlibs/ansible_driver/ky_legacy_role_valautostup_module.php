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
    //    legacy-Roleの代入値自動登録処理
    //
    //////////////////////////////////////////////////////////////////////

    // 起動しているshellの起動判定を正常にするための待ち時間
    sleep(1);

    require($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_role_valautostup_setenv.php");

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php       = "/libs/backyardlibs/backyard_log_output.php";
    $php_req_gate_php     = "/libs/commonlibs/common_php_req_gate.php";
    $db_connect_php       = "/libs/commonlibs/common_db_connect.php";

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    $g_null_data_handling_def   = "";

    try {
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once($root_dir_path . $php_req_gate_php);

        // 開始メッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require_once($root_dir_path . $db_connect_php);

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースが更新されバックヤード処理が必要か判定
        ///////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70052");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $lv_a_proc_loaded_list_pkey = 2100020006;
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

        //////////////////////////////////////////////////////////////////////////////////////
        // インターフェース情報からNULLデータを代入値管理に登録するかのデフォルト値を取得する。
        //////////////////////////////////////////////////////////////////////////////////////
        $lv_if_info = array();
        $error_msg   = "";
        $ret = getIFInfoDB($lv_if_info,$error_msg);
        if($ret === false) {
            $error_flag = 1;
            throw new Exception( $error_msg );
        }
        $g_null_data_handling_def = $lv_if_info["NULL_DATA_HANDLING_FLG"];

        ///////////////////////////////////////////////////////////////////////////
        // 代入値自動登録設定からカラム毎の変数の情報を取得
        ///////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70015");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        // テーブル名配列
        $lv_tableNameToMenuIdList      = array();
        // カラム情報配列
        $lv_tabColNameToValAssRowList  = array();
        // 代入値紐付の登録に不備がある主キーの配列
        $lv_errorColumnIdList          = array();
        // テーブル名と主キーの配列
        $lv_tableNameToPKeyNameList    = array();

        $ret = readValAssign($lv_tableNameToMenuIdList,
                             $lv_tabColNameToValAssRowList,
                             $lv_errorColumnIdList,
                             $lv_tableNameToPKeyNameList);
        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90032");
            throw new Exception($errorMsg);
        }

        ///////////////////////////////////////////////////////////////////////////////
        //   紐付メニューへのSELECT文を生成する。
        ///////////////////////////////////////////////////////////////////////////////
        // 代入値紐付メニュー毎のSELECT文配列
        $lv_tableNameToSqlList   = array();

        createQuerySelectCMDB($lv_tableNameToMenuIdList,
                              $lv_tabColNameToValAssRowList,
                              $lv_tableNameToPKeyNameList,
                              $lv_tableNameToSqlList);

        ////////////////////////////////////////////////////////////////////////////////
        //   紐付メニューから具体値を取得する。
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70016");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $lv_varsAssList          = array();
        $lv_arrayVarsAssList     = array();

        $ret = getCMDBdata($lv_tableNameToSqlList,
                           $lv_tableNameToMenuIdList,
                           $lv_tabColNameToValAssRowList,
                           $lv_errorColumnIdList,
                           $lv_varsAssList,
                           $lv_arrayVarsAssList,
                           $warning_flag);

       // 不要となった配列変数を開放
       unset($lv_tableNameToSqlList);
       unset($lv_tableNameToMenuIdList);
       unset($lv_tabColNameToValAssRowList);
       unset($lv_errorColumnIdList);
       unset($lv_tableNameToPKeyNameList);

        ////////////////////////////////////////////////////////////////////////////////
        // トランザクション開始       
        ////////////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-80001"));
        }

        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理のA_SEQUENCEレコードをロックする。
        ////////////////////////////////////////////////////////////////////////////////
        $ret = SequenceTableLock(array($strSeqOfCurTableVarsAss,$strSeqOfJnlTableVarsAss));
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80002");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理のデータを全件読み込む
        ////////////////////////////////////////////////////////////////////////////////
        $lv_VarsAssignRecodes = array();
        $lv_ArryVarsAssignRecodes = array();
        $ret = getVarsAssignRecodes($lv_VarsAssignRecodes,$lv_ArryVarsAssignRecodes);
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90301");
            throw new Exception($errorMsg);
        }

        // 作業対象ホストに登録が必要な配列初期化
        $lv_phoLinkList          = array();

        ////////////////////////////////////////////////////////////////////////////////
        //   一般変数・複数具体値変数を紐付けている紐付メニューの具体値を代入値管理に登録
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70044");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        foreach($lv_varsAssList as $varsAssRecord) {
            // 処理対象外のデータかを判定
            if($varsAssRecord['STATUS'] === false) {
                continue;
            }

            // 代入値管理に具体値を登録
            $ret = addStg1StdListVarsAssign($varsAssRecord, $lv_VarsAssignRecodes);
            if($ret === false) {
                  $error_flag = 1;
                  $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90300");
                  throw new Exception($errorMsg);
            }

            // 作業対象ホストに登録が必要な情報を退避
            $lv_phoLinkList[$varsAssRecord['OPERATION_NO_UAPK']]
                           [$varsAssRecord['PATTERN_ID']]
                           [$varsAssRecord['SYSTEM_ID']] = 1;
        }

        ////////////////////////////////////////////////////////////////////////////////
        //   多次元変数を紐付けている紐付メニューの具体値を代入値管理に登録
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70045");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        foreach($lv_arrayVarsAssList as $varsAssRecord) {
            // 処理対象外のデータかを判定
            if($varsAssRecord['STATUS'] === false) {
                continue;
            }

            $ret = addStg1ArrayVarsAssign($varsAssRecord, $lv_ArryVarsAssignRecodes);
            if($ret === false) {
                $error_flag = 1;
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90210");
                throw new Exception($errorMsg);
            }

            // 作業対象ホストに登録が必要な情報を退避
            $lv_phoLinkList[$varsAssRecord['OPERATION_NO_UAPK']]
                           [$varsAssRecord['PATTERN_ID']]
                           [$varsAssRecord['SYSTEM_ID']] = 1;
        }

        ////////////////////////////////////////////////////////////////////////////////
        //   代入値管理から不要なデータを削除する
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70020");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $ret = deleteVarsAssign($lv_VarsAssignRecodes);
        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90053");
            throw new Exception($errorMsg);
        }
        unset($lv_VarsAssignRecodes);

        $ret = deleteVarsAssign($lv_ArryVarsAssignRecodes);
        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90053");
            throw new Exception($errorMsg);
        }
        unset($lv_ArryVarsAssignRecodes);

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if(!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80003");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション終了                                       //
        ////////////////////////////////////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション開始                                       //
        ////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-80001"));
        }

        ////////////////////////////////////////////////////////////////
        // 対象ホスト管理のA_SEQUENCEレコードをロックする。           //
        ////////////////////////////////////////////////////////////////
        $ret = SequenceTableLock(array($strSeqOfCurTablePhoLnk,$strSeqOfJnlTablePhoLnk));
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80002");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // 対象ホスト管理のデータを全件読込                           //
        ////////////////////////////////////////////////////////////////
        $lv_PhoLinkRecodes = array();
        $ret = getPhoLinkRecodes($lv_PhoLinkRecodes);
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90302");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理で登録したオペ+作業パターン+ホストが作業対象ホストに登録されている
        // か判定し、未登録の場合は登録する。
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70021");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        foreach($lv_phoLinkList as $ope_id=>$ptn_list) {
            foreach($ptn_list as $ptn_id=>$host_list) {
                foreach($host_list as $host_id=>$dummy) {
                    $lv_phoLinkData = array('OPERATION_NO_UAPK'=>$ope_id,
                                           'PATTERN_ID'=>$ptn_id,
                                           'SYSTEM_ID'=>$host_id);
                    $ret = addStg1PhoLink($lv_phoLinkData, $lv_PhoLinkRecodes);
                    if($ret === false) {
                        $error_flag = 1;
                        $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90054");
                        throw new Exception( $errorMsg );
                    }
                }
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        // 作業対象ホストから不要なデータを削除する
        ////////////////////////////////////////////////////////////////////////////////

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70022");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $ret = deletePhoLink($lv_PhoLinkRecodes);
        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90055");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if(!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80003");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション終了                                       //
        ////////////////////////////////////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースの更新反映を登録
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



    } catch(Exception $e) {

        $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80004");
        LocalLogPrint(basename(__FILE__),__LINE__,$errorMsg);

        // 例外メッセージ出力
        $errorMsg = $e->getMessage();
        LocalLogPrint(basename(__FILE__),__LINE__,$errorMsg);
        
        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if($objDBCA->getTransactionMode()) {
            // ロールバック
            if($objDBCA->transactionRollBack()=== true) {
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60004");
            } else {
                $error_flag = 1;
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80005");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$errorMsg);
            
            // トランザクション終了
            if($objDBCA->transactionExit()=== true) {
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            } else {
                $error_flag = 1;
                $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80006");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$errorMsg);
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if($error_flag != 0) {
        // 終了メッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
        exit(2); // backgroundserviceを止めるかどうかのハンドリングは別途検討
    } elseif($warning_flag != 0) {
        // 終了メッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
        exit(2); // backgroundserviceを止めるかどうかのハンドリングは別途検討
    } else {
        // 終了メッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
        exit(0);
    }

////////////////////////////////////////////////////////////////////////////////
// F0002
// 処理内容
//   代入値自動登録設定からカラム情報を取得する。
//   
// パラメータ
//   &$inout_tableNameToMenuIdList:      テーブル名配列
//                                           [テーブル名]=MENU_ID
//   &$inout_tabColNameToValAssRowList:  カラム情報配列
//                                           [テーブル名][カラム名][]=>array("代入値自動登録設定のカラム名"=>値)
//   &$inout_errorColumnIdList:          代入値紐付の登録に不備がある主キーの配列
//                                           [代入値紐付主キー]=1
//   &$inout_tableNameToPKeyNameList:    テーブル主キー名配列
//                                           [テーブル名]=主キー名
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function readValAssign(&$inout_tableNameToMenuIdList,
                       &$inout_tabColNameToValAssRowList,
                       &$inout_errorColumnIdList,
                       &$inout_tableNameToPKeyNameList
                       ) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $lv_val_assign_tbl;
    global $lv_pattern_link_tbl;
    global $lv_vars_master_tbl;
    global $lv_array_member_tbl;
    global $lv_member_col_comb_tbl;
    global $lv_ptn_vars_link_tbl;

    $sql =            " SELECT                                                           \n";
    $sql = $sql .     "   TBL_A.COLUMN_ID                                             ,  \n";
    $sql = $sql .     "   TBL_A.MENU_ID                                               ,  \n";
    $sql = $sql .     "   TBL_C.TABLE_NAME                                            ,  \n";
    $sql = $sql .     "   TBL_C.PKEY_NAME                                             ,  \n";
    $sql = $sql .     "   TBL_C.DISUSE_FLAG  AS TBL_DISUSE_FLAG                       ,  \n";
    $sql = $sql .     "   TBL_A.COLUMN_LIST_ID                                        ,  \n";
    $sql = $sql .     "   TBL_B.COL_NAME                                              ,  \n";
    $sql = $sql .     "   TBL_B.COL_TITLE                                             ,  \n";
    $sql = $sql .     "   TBL_B.REF_TABLE_NAME                                        ,  \n";
    $sql = $sql .     "   TBL_B.REF_PKEY_NAME                                         ,  \n";
    $sql = $sql .     "   TBL_B.REF_COL_NAME                                          ,  \n";
    $sql = $sql .     "   TBL_B.DISUSE_FLAG  AS COL_DISUSE_FLAG                       ,  \n";
    $sql = $sql .     "   TBL_A.COL_TYPE                                              ,  \n";
   
    // 代入値管理データ連携フラグ
    $sql = $sql .     "   TBL_A.NULL_DATA_HANDLING_FLG                                ,  \n";

    // 作業パターン詳細の登録確認
    $sql = $sql .     "   TBL_A.PATTERN_ID                                            ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       COUNT(*)                                                   \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_pattern_link_tbl                                       \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       PATTERN_ID  = TBL_A.PATTERN_ID AND                         \n";
    $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
    $sql = $sql .     "   ) AS PATTERN_CNT                                            ,  \n";
    $sql = $sql .     "                                                                  \n";

    // (Val)作業パターン変数紐付の登録確認
    $sql = $sql .     "   TBL_A.VAL_VARS_LINK_ID                                      ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       COUNT(*)                                                   \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_ptn_vars_link_tbl                                      \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
    $sql = $sql .     "       VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND                \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS VAL_PTN_VARS_LINK_CNT                                  ,  \n";

    // (Val)変数名一覧
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       VARS_NAME                                                  \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_vars_master_tbl                                        \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS VAL_VARS_NAME                                          ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       VARS_ATTRIBUTE_01                                          \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_vars_master_tbl                                        \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "                        )                                         \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS VAL_VARS_ATTRIBUTE_01                                  ,  \n";

    // (Val)多次元変数配列組合せ管理
    $sql = $sql .     "   TBL_A.VAL_COL_SEQ_COMBINATION_ID                            ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       COL_COMBINATION_MEMBER_ALIAS                               \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_member_col_comb_tbl                                    \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       COL_SEQ_COMBINATION_ID = TBL_A.VAL_COL_SEQ_COMBINATION_ID AND     \n";
    $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
    $sql = $sql .     "   ) AS VAL_COL_COMBINATION_MEMBER_ALIAS                       ,  \n";

    // (Val)多次元変数メンバー管理
    $sql = $sql .     "   TBL_A.VAL_ASSIGN_SEQ                                        ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       ASSIGN_SEQ_NEED                                            \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_array_member_tbl                                       \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       ARRAY_MEMBER_ID IN (                                       \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           ARRAY_MEMBER_ID                                        \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_member_col_comb_tbl                                \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           COL_SEQ_COMBINATION_ID = TBL_A.KEY_COL_SEQ_COMBINATION_ID AND \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
    $sql = $sql .     "   ) AS VAL_ASSIGN_SEQ_NEED                                    ,  \n";

    // (Key)作業パターン変数紐付の登録確認
    $sql = $sql .     "   TBL_A.KEY_VARS_LINK_ID                                      ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       COUNT(*)                                                   \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_ptn_vars_link_tbl                                      \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
    $sql = $sql .     "       VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND                \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS KEY_PTN_VARS_LINK_CNT                                  ,  \n";

    // (Key)変数名一覧
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       VARS_NAME                                                  \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_vars_master_tbl                                        \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS KEY_VARS_NAME                                          ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       VARS_ATTRIBUTE_01                                          \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_vars_master_tbl                                        \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "                        )                                         \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
    $sql = $sql .     "   ) AS KEY_VARS_ATTRIBUTE_01                                  ,  \n";

    // (Key)多次元変数配列組合せ管理
    $sql = $sql .     "   TBL_A.KEY_COL_SEQ_COMBINATION_ID                            ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       COL_COMBINATION_MEMBER_ALIAS                               \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_member_col_comb_tbl                                    \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       COL_SEQ_COMBINATION_ID = TBL_A.KEY_COL_SEQ_COMBINATION_ID AND     \n";
    $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
    $sql = $sql .     "   ) AS KEY_COL_COMBINATION_MEMBER_ALIAS                       ,  \n";

    // (Key)多次元変数メンバー管理
    $sql = $sql .     "   TBL_A.KEY_ASSIGN_SEQ                                        ,  \n";
    $sql = $sql .     "   (                                                              \n";
    $sql = $sql .     "     SELECT                                                       \n";
    $sql = $sql .     "       ASSIGN_SEQ_NEED                                            \n";
    $sql = $sql .     "     FROM                                                         \n";
    $sql = $sql .     "       $lv_array_member_tbl                                       \n";
    $sql = $sql .     "     WHERE                                                        \n";
    $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           VARS_NAME_ID                                           \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_ptn_vars_link_tbl                                  \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
    $sql = $sql .     "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       ARRAY_MEMBER_ID IN (                                       \n";
    $sql = $sql .     "         SELECT                                                   \n";
    $sql = $sql .     "           ARRAY_MEMBER_ID                                        \n";
    $sql = $sql .     "         FROM                                                     \n";
    $sql = $sql .     "           $lv_member_col_comb_tbl                                \n";
    $sql = $sql .     "         WHERE                                                    \n";
    $sql = $sql .     "           COL_SEQ_COMBINATION_ID = TBL_A.KEY_COL_SEQ_COMBINATION_ID AND \n";
    $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
    $sql = $sql .     "         )                                                        \n";
    $sql = $sql .     "       AND                                                        \n";
    $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
    $sql = $sql .     "   ) AS KEY_ASSIGN_SEQ_NEED                                       \n";

    $sql = $sql .     " FROM                                                             \n";
    $sql = $sql .     "   $lv_val_assign_tbl TBL_A                                       \n";
    $sql = $sql .     "   LEFT JOIN B_CMDB_MENU_COLUMN TBL_B ON                          \n";
    $sql = $sql .     "          (TBL_A.COLUMN_LIST_ID = TBL_B.COLUMN_LIST_ID)           \n";
    $sql = $sql .     "   LEFT JOIN B_CMDB_MENU_TABLE  TBL_C ON                          \n";
    $sql = $sql .     "          (TBL_A.MENU_ID        = TBL_C.MENU_ID)                  \n";
    $sql = $sql .     " WHERE                                                            \n";
    $sql = $sql .     "   TBL_A.DISUSE_FLAG='0'                                          \n";
    $sql = $sql .     " ORDER BY TBL_A.COLUMN_ID                                         \n";

    $sqlUtnBody = $sql;
    $arrayUtnBind = array();
    $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
    if($objQuery == null) {
        return false;
    }

    //$lva_var_assign_seq_list{作業パターン][変数][代入順序]=COLUMN_LIST_ID
    $lva_var_assign_seq_list   = array();

    //$lva_array_assign_seq_list{作業パターン][変数][メンバー変数][代入順序]=COLUMN_LIST_ID
    $lva_array_assign_seq_list = array();


    while($row = $objQuery->resultFetch()) {
        // CMDB代入値紐付メニューが廃止されているか判定
        if($row['TBL_DISUSE_FLAG'] != '0') {
            if($log_level === "DEBUG"){
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90014",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // CMDB代入値紐付メニューのカラムが廃止されているか判定
        if($row['COL_DISUSE_FLAG'] != '0') {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90016",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // 作業パターン詳細に作業パターンが未登録
        if($row['PATTERN_CNT'] == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90013",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // CMDB代入値紐付メニューが登録されているか判定
        if(@strlen($row['TABLE_NAME']) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90015",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // CMDB代入値紐付メニューの主キーが登録されているか判定
        if(@strlen($row['PKEY_NAME']) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90086",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // CMDB代入値紐付メニューのカラムが未登録か判定
        if(@strlen($row['COL_NAME']) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90017",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // CMDB代入値紐付メニューのカラムタイトルが未登録か判定
        if(@strlen($row['COL_TITLE']) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90018",array($row['COLUMN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        //カラムタイプ判定
        $type_chk = array(
            DF_COL_TYPE_VAL,
            DF_COL_TYPE_KEY,
            DF_COL_TYPE_KEYVAL
            );
        $col_type = $row['COL_TYPE'];
        if(!in_array($col_type, $type_chk)) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90019",array($row['COLUMN_ID'],$row['COL_TYPE']));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 次のカラムへ
            continue;
        }

        // Value型変数の変数タイプ
        $val_vars_attr = "";
        // Key型変数の変数タイプ
        $key_vars_attr = "";

        // Key項目・Value項目の検査（当該レコード）
        //カラムタイプにより処理分岐
        switch($col_type) {
        case DF_COL_TYPE_VAL:
        case DF_COL_TYPE_KEYVAL:
            //Value型に設定されている変数設定確認
            $ret = valAssColumnValidate("Value",
                                        $val_vars_attr,
                                        $row,
                                        "VAL_VARS_LINK_ID",
                                        "VAL_VARS_NAME",
                                        "VAL_PTN_VARS_LINK_CNT",
                                        "VAL_VARS_ATTRIBUTE_01",
                                        "VAL_COL_SEQ_COMBINATION_ID",
                                        "VAL_COL_COMBINATION_MEMBER_ALIAS",
                                        "VAL_ASSIGN_SEQ",
                                        "VAL_ASSIGN_SEQ_NEED"
                                        );
            if($ret === false) {
                // 次のカラムへ
                continue 2;
            }
            break;
        }

        switch($col_type) {
        case DF_COL_TYPE_KEY:
        case DF_COL_TYPE_KEYVAL:
            //Key型に設定されている変数設定確認
            $ret = valAssColumnValidate("Key",
                                        $key_vars_attr,
                                        $row,
                                        "KEY_VARS_LINK_ID",
                                        "KEY_VARS_NAME",
                                        "KEY_PTN_VARS_LINK_CNT",
                                        "KEY_VARS_ATTRIBUTE_01",
                                        "KEY_COL_SEQ_COMBINATION_ID",
                                        "KEY_COL_COMBINATION_MEMBER_ALIAS",
                                        "KEY_ASSIGN_SEQ",
                                        "KEY_ASSIGN_SEQ_NEED"
                                        );
            if($ret === false) {
                // 次のカラムへ
                continue 2;
            }
            break;
        }

        // Key項目・Value項目の検査（他レコードとの関連）
        //カラムタイプにより処理分岐
        switch($col_type) {
        case DF_COL_TYPE_VAL:
        case DF_COL_TYPE_KEYVAL:
            //Value型変数の代入順序の重複をチェック
            $ret = valAssSeqDuplicatePickUp("Value",
                                            $val_vars_attr,
                                            $row,
                                            $lva_var_assign_seq_list,
                                            $lva_array_assign_seq_list,
                                            $inout_errorColumnIdList,
                                            "PATTERN_ID",
                                            "VAL_VARS_LINK_ID",
                                            "VAL_COL_SEQ_COMBINATION_ID",
                                            "VAL_ASSIGN_SEQ"
                                            );
            if($ret === false) {
                // 次のカラムへ
                continue 2;
            }
            break;
        }

        switch($col_type) {
        case DF_COL_TYPE_KEY:
        case DF_COL_TYPE_KEYVAL:
            //key型変数を場合の代入順序をチェック
            $ret = valAssSeqDuplicatePickUp("Key",
                                            $key_vars_attr,
                                            $row,
                                            $lva_var_assign_seq_list,
                                            $lva_array_assign_seq_list,
                                            $inout_errorColumnIdList,
                                            "PATTERN_ID",
                                            "KEY_VARS_LINK_ID",
                                            "KEY_COL_SEQ_COMBINATION_ID",
                                            "KEY_ASSIGN_SEQ"
                                            );
            if($ret === false) {
                // 次のカラムへ
                continue 2;
            }
            break;
        }

        $inout_tableNameToMenuIdList[$row['TABLE_NAME']] = $row['MENU_ID'];

        $inout_tabColNameToValAssRowList[$row['TABLE_NAME']][$row['COL_NAME']][] = 
            array(
                'COLUMN_ID'                         => $row['COLUMN_ID'],
                'COL_TYPE'                          => $row['COL_TYPE'],
                'COL_TITLE'                         => $row['COL_TITLE'],
                'REF_TABLE_NAME'                    =>$row['REF_TABLE_NAME'],
                'REF_PKEY_NAME'                     =>$row['REF_PKEY_NAME'],
                'REF_COL_NAME'                      =>$row['REF_COL_NAME'],
                'PATTERN_ID'                        => $row['PATTERN_ID'],
                // Value項目
                'VAL_VARS_LINK_ID'                  => $row['VAL_VARS_LINK_ID'],
                'VAL_VARS_NAME'                     => $row['VAL_VARS_NAME'],
                'VAL_VAR_TYPE'                      => $val_vars_attr,
                'VAL_COL_SEQ_COMBINATION_ID'        => $row['VAL_COL_SEQ_COMBINATION_ID'],
                'VAL_COL_COMBINATION_MEMBER_ALIAS'  => $row['VAL_COL_COMBINATION_MEMBER_ALIAS'],
                'VAL_ASSIGN_SEQ'                    => $row['VAL_ASSIGN_SEQ'],
                // Key項目
                'KEY_VARS_LINK_ID'                  => $row['KEY_VARS_LINK_ID'],
                'KEY_VARS_NAME'                     => $row['KEY_VARS_NAME'],
                'KEY_VAR_TYPE'                      => $key_vars_attr,
                'KEY_COL_SEQ_COMBINATION_ID'        => $row['KEY_COL_SEQ_COMBINATION_ID'],
                'KEY_COL_COMBINATION_MEMBER_ALIAS'  => $row['KEY_COL_COMBINATION_MEMBER_ALIAS'],
                'KEY_ASSIGN_SEQ'                    => $row['KEY_ASSIGN_SEQ'],
                'NULL_DATA_HANDLING_FLG'            => $row['NULL_DATA_HANDLING_FLG']

            );

        // テーブルの主キー名退避
        $inout_tableNameToPKeyNameList[$row['TABLE_NAME']] = $row['PKEY_NAME'];
    }

    // DBアクセス事後処理
    unset($objQuery);
    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0003
// 処理内容
//   代入値自動登録設定のカラム情報を検査する。
//   
// パラメータ
//   $in_col_type:            カラムタイプ Value/Key
//   $inout_vars_attr:        変数区分 (1:一般変数, 2:複数具体値, 3:多次元変数)
//   $row:                    クエリ配列
//   $in_vars_link_id:        クエリ配列内のKey/Value型の変数IDキー 
//                            VAL_VARS_LINK_ID/KEY_VARS_LINK_ID
//   $in_vars_name:           クエリ配列内のKey/Value型の変数名キー
//                            VAL_VARS_NAME/KEY_VARS_NAME
//   $in_ptn_vars_link_cnt:   クエリ配列内のKey/Value型の作業パターン+変数の
//                            作業パターン変数紐付の登録件数
//                            VAL_PTN_VARS_LINK_CNT/KEY_PTN_VARS_LINK_CNT
//   $in_vars_attribute_01:   クエリ配列内の変数タイプ(Roleの場合のみ有効)
//                            VARS_ATTRIBUTE_01
//   $in_col_seq_combination_id:  クエリ配列内のKey/Value型のメンバー変数IDキー
//                            VAL_COL_SEQ_COMBINATION_ID/KEY_COL_SEQ_COMBINATION_ID
//   $in_col_combination_member_alias:  クエリ配列内のKey/Value型のメンバー変数名キー
//                            VAL_COL_COMBINATION_MEMBER_ARIAS/KEY_COL_COMBINATION_MEMBER_ARIAS
//   $in_assign_seq:          クエリ配列内のKey/Value型の代入順序キー
//                            VAL_ASSIGN_SEQ/KEY_ASSIGN_SEQ
//   $in_assign_seq_need:     クエリ配列内のKey/Value型の代入順序 要・不要
//                            VAL_ASSIGN_SEQ_NEED/KEY_ASSIGN_SEQ_NEED
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function valAssColumnValidate($in_col_type,           //カラムタイプ Value/Key
                              &$inout_vars_attr,
                              $row,
                              $in_vars_link_id,
                              $in_vars_name,
                              $in_ptn_vars_link_cnt,
                              $in_vars_attribute_01,
                              $in_col_seq_combination_id,
                              $in_col_combination_member_alias,
                              $in_assign_seq,
                              $in_assign_seq_need
                              ) {

    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    //変数の選択判定
    if(@strlen($row[$in_vars_link_id]) == 0) {
        if($log_level === "DEBUG") {
            // 代入値紐付（項番:｛｝）のValue型の変数が未選択。
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90033",array($row['COLUMN_ID'],$in_col_type));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
        // エラーリターン
        return false;
    }

    // 変数が作業パターン変数紐付にあるか判定
    if(@strlen($row[$in_ptn_vars_link_cnt]) == 0) {
        if($log_level === "DEBUG") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90025",array($row['COLUMN_ID'],$in_col_type));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
        // エラーリターン
        return false;
    }

    // 設定されている変数が変数一覧にあるか判定
    if(@strlen($row[$in_vars_name]) == 0) {
        if($log_level === "DEBUG") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90022",array($row['COLUMN_ID'],$in_col_type));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
        // エラーリターン
        return false;
    }

    switch($row[$in_vars_attribute_01]) {
    case GC_VARS_ATTR_STD:             // 一般変数
    case GC_VARS_ATTR_LIST:            // 複数具体値
    case GC_VARS_ATTR_M_ARRAY:         // 多次元変数
        $inout_vars_attr = $row[$in_vars_attribute_01];
        break;
    default:
        if($log_level === "DEBUG") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90204",array($row['COLUMN_ID'],$in_col_type));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
        // エラーリターン
        return false;
    }

    // メンバー変数がメンバー変数一覧にあるか判定
    switch($inout_vars_attr) {
    case GC_VARS_ATTR_M_ARRAY:        // 多次元変数
        //メンバー変数の選択判定
        if(@strlen($row[$in_col_seq_combination_id]) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90103",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        // カラムタイプ型に設定されているメンバー変数がメンバー変数一覧にあるか判定
        if(@strlen($row[$in_col_combination_member_alias]) == 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90026",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        break;
    default:
        //メンバー変数の選択判定
        if(@strlen($row[$in_col_seq_combination_id]) != 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90102",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        break;
    }

    switch($inout_vars_attr) {
    case GC_VARS_ATTR_LIST:            // 複数具体値
        if(@strlen($row[$in_assign_seq])===0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90027",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        break;
    case GC_VARS_ATTR_M_ARRAY:         // 多次元変数
        //ASSIGN_SEQ_NEED
        if(@$row[$in_assign_seq_need]===1 && @strlen($row[$in_assign_seq])===0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90027",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        break;
    }

    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0004
// 処理内容
//   代入値自動登録設定の代入順序の重複チェック
//   
// パラメータ
//   $in_col_type:                カラムタイプ Value/Key
//   $in_vars_attr:               変数区分 (1:一般変数, 2:複数具体値, 3:多次元変数)
//   $row:                        クエリ配列
//   $inout_varAssignSeqList:     作業パターン+変数名+代入順序の重複チェック配列
//   $inout_arrayAssignSeqList:   作業パターン+変数名+メンバー変数+代入順序の重複チェック配列
//   $inout_errorColumnIdList:    代入値紐付でエラーが発生している代入値紐付の主キーリスト
//   $in_pattern_id:              クエリ配列内のKey/Value型の変数名キー
//                                VAL_VARS_NAME/KEY_VARS_NAME
//   $in_vars_link_id:            クエリ配列内のKey/Value型の変数IDキー 
//                                VAL_VARS_LINK_ID/KEY_VARS_LINK_ID
//   $in_col_seq_combination_id:  クエリ配列内のKey/Value型のメンバー変数IDキー
//                                VAL_COL_SEQ_COMBINATION_ID/KEY_COL_SEQ_COMBINATION_ID
//   $in_assign_seq:              クエリ配列内のKey/Value型の代入順序キー
//                                VAL_ASSIGN_SEQ/KEY_ASSIGN_SEQ
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function valAssSeqDuplicatePickUp($in_col_type,
                                  $in_vars_attr,
                                  $row,
                                  &$inout_varAssignSeqList,
                                  &$inout_arrayAssignSeqList,
                                  &$inout_errorColumnIdList,
                                  $in_pattern_id,
                                  $in_vars_link_id,
                                  $in_col_seq_combination_id,
                                  $in_assign_seq) {

    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    switch($in_vars_attr) {
    case GC_VARS_ATTR_STD:             // 一般変数
        break;
    case GC_VARS_ATTR_LIST:            // 複数具体値
        if( isset($inout_varAssignSeqList[$row[$in_pattern_id]]
                                         [$row[$in_vars_link_id]]
                                         [$row[$in_assign_seq]])) {
            $column_id = $inout_varAssignSeqList[$row[$in_pattern_id]]
                                                [$row[$in_vars_link_id]]
                                                [$row[$in_assign_seq]];

            //重複しているのでエラーリストに代入値紐付の主キーを退避
            $inout_errorColumnIdList[$column_id]        = 1;
            $inout_errorColumnIdList[$row['COLUMN_ID']] = 1;

            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90029",array($row['COLUMN_ID'],$column_id,$in_col_type));

                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        //代入順序退避
        $inout_varAssignSeqList[$row[$in_pattern_id]]
                               [$row[$in_vars_link_id]]
                               [$row[$in_assign_seq]] = $row['COLUMN_ID'];
        break;
    case GC_VARS_ATTR_M_ARRAY:         // 多次元変数
        if( isset($inout_arrayAssignSeqList[$row[$in_pattern_id]]
                                           [$row[$in_vars_link_id]]
                                           [$row[$in_col_seq_combination_id]]
                                           [$row[$in_assign_seq]])) {
            $column_id = $inout_arrayAssignSeqList[$row[$in_pattern_id]]
                                                       [$row[$in_vars_link_id]]
                                                       [$row[$in_col_seq_combination_id]]
                                                       [$row[$in_assign_seq]];

            //重複しているのでエラーリストに代入値紐付の主キーを退避
            $inout_errorColumnIdList[$column_id]        = 1;
            $inout_errorColumnIdList[$row['COLUMN_ID']] = 1;

            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90208",array($row['COLUMN_ID'],$column_id,$in_col_type));

                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }
        //代入順序退避
        $inout_arrayAssignSeqList[$row[$in_pattern_id]]
                                      [$row[$in_vars_link_id]]
                                      [$row[$in_col_seq_combination_id]]
                                      [$row[$in_assign_seq]] = $row['COLUMN_ID'];
        break;
    }
    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0005
// 処理内容
//   代入値紐付メニューへのSELECT文を生成する。
//
// パラメータ
//   $in_tableNameToMenuIdList:      テーブル名配列
//                                  [テーブル名]=MENU_ID
//   $in_tabColNameToValAssRowList:   テーブル名+カラム名配列
//                                  [テーブル名][カラム名]=代入値自動登録設定情報
//   $in_tableNameToPKeyNameList:テーブル主キー名配列
//                                  [テーブル名]=主キー名
//   $inout_tableNameToSqlList:     代入値紐付メニュー毎のSELECT文配列
//                                  [テーブル名][SELECT文]
// 戻り値
//   なし
////////////////////////////////////////////////////////////////////////////////
function createQuerySelectCMDB($in_tableNameToMenuIdList,
                               $in_tabColNameToValAssRowList,
                               $in_tableNameToPKeyNameList,
                               &$inout_tableNameToSqlList) {

    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    $opeid_chk_sql  = "( SELECT                                       \n" .
                      "    OPERATION_NO_UAPK                          \n" .
                      "  FROM                                         \n" .
                      "    C_OPERATION_LIST                           \n" .
                      "  WHERE                                        \n" .
                      "    OPERATION_NO_IDBH = TBL_A.OPERATION_ID AND \n" .
                      "    DISUSE_FLAG = '0'                          \n" .
                      ") AS  OPERATION_ID ,                           \n";

    // 機器一覧にホストが登録されているかを判定するSQL
    $hostid_chk_sql = "( SELECT                                       \n" .
                      "    COUNT(*)                                   \n" .
                      "  FROM                                         \n" .
                      "    C_STM_LIST                                 \n" .
                      "  WHERE                                        \n" .
                      "    SYSTEM_ID   = TBL_A.HOST_ID     AND        \n" .
                      "    DISUSE_FLAG = '0'                          \n" .
                      ") AS " . DF_ITA_LOCAL_HOST_CNT  . ",           \n";

         
    // テーブル名+カラム名配列からテーブル名と配列名を取得
    foreach($in_tabColNameToValAssRowList as $table_name=>$col_list) {

        $pkey_name = $in_tableNameToPKeyNameList[$table_name];

        $col_sql = "";
        foreach(array_keys($col_list) as $col_name) {
            $col_sql = $col_sql .
                          ", TBL_A." . $col_name . "                           \n";
        }

        if($col_sql == "") {
            //SELECT対象の項目なし
            //エラーがあるのでスキップ
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90035",array($in_tableNameToMenuIdList[$table_name]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            //次のテーブルへ
            continue;
        }

        // SELECT文を生成
        $make_sql = "SELECT                                                    \n " .
                    $opeid_chk_sql . "                                         \n " .
                    $hostid_chk_sql . "                                        \n " .
                    "  TBL_A." . $pkey_name . " AS " . DF_ITA_LOCAL_PKEY . "   \n " .
                    ", TBL_A.HOST_ID                                           \n " .
                    $col_sql . "                                               \n " .
                    " FROM " . $table_name . " TBL_A                           \n " .
                    " WHERE DISUSE_FLAG = '0' ";

        //メニューテーブルのSELECT SQL文退避
        $inout_tableNameToSqlList[$table_name] = $make_sql;
    }
}

////////////////////////////////////////////////////////////////////////////////
// F0006
// 処理内容
//   CMDB代入値紐付対象メニューから具体値を取得する。
//
// パラメータ
//   $in_tableNameToSqlList:        CMDB代入値紐付メニュー毎のSELECT文配列
//                                  [テーブル名][SELECT文]
//   $in_tableNameToMenuIdList:     テーブル名配列
//                                  [テーブル名]=MENU_ID
//   $in_tabColNameToValAssRowList: カラム情報配列
//                                  [テーブル名][カラム名][]=>array("代入値紐付のカラム名"=>値)
//   $in_errorColumnIdList:         代入値自動登録設定の登録に不備がある主キーの配列
//                                  [代入値自動登録設定主キー]=1
//   $ina_vars_ass_list:            一般変数・複数具体値変数用 代入値登録情報配列
//   $ina_array_vars_ass_list:      多次元変数配列変数用 代入値登録情報配列
//   $warning_flag:                 警告フラグ
//
// 戻り値
//   true:   正常
//   false:  異常
////////////////////////////////////////////////////////////////////////////////
function getCMDBdata($in_tableNameToSqlList,
                     $in_tableNameToMenuIdList,
                     $in_tabColNameToValAssRowList,
                     $in_errorColumnIdList,
                     &$ina_vars_ass_list,
                     &$ina_array_vars_ass_list,
                     &$warning_flag) {

    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    // オペ+作業+ホスト+変数の組合せの代入順序 重複確認用
    $lv_varsAssChkList = array();
    // オペ+作業+ホスト+変数+メンバ変数の組合せの代入順序 重複確認用
    $lv_arrayVarsAssChkList = array();

    foreach($in_tableNameToSqlList as $table_name=>$sql) {

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70017",array($in_tableNameToMenuIdList[$table_name]));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $sqlUtnBody = $sql;
        $arrayUtnBind = array();
        // CMDB代入値紐付メニューのデータを取出す
        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            //DBアクセスエラー
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90036",array($in_tableNameToMenuIdList[$table_name]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            $warning_flag = 1;
            //次のテーブルへ
            continue;
        }


        // FETCH行数を取得
        $total_row = array();
        while($row = $objQuery->resultFetch()) {
            $total_row[] = $row;
        }

        // DBアクセス事後処理
        unset($objQuery);

        // CMDB代入値紐付メニューに具体値の登録なし
        if(@count($total_row) === 0) {
            if($log_level === "DEBUG") {
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90048",array($in_tableNameToMenuIdList[$table_name]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            //次のテーブルへ
            continue;
        }

        foreach($total_row as $row) {

            // 代入値紐付メニューに登録されているオペレーションIDを確認
            if(@strlen($row['OPERATION_ID']) == 0) {
                //オペレーションID未登録
                if($log_level === "DEBUG") {
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90040",array($in_tableNameToMenuIdList[$table_name],$row[DF_ITA_LOCAL_PKEY]));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }

                $warning_flag = 1;
                //次のデータへ
                continue;
            }

            $operation_id = $row['OPERATION_ID'];

            // 代入値紐付メニューに登録されているホストIDの紐付確認
            if($row[DF_ITA_LOCAL_HOST_CNT] == 0) {
                // ホストIDの紐付不正
                if($log_level === "DEBUG") {
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90038",array($in_tableNameToMenuIdList[$table_name],$row[DF_ITA_LOCAL_PKEY],$row['HOST_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }

                $warning_flag = 1;
                //次のデータへ
                continue;
            }

            // 代入値紐付メニューに登録されているホストIDを確認
            if(@strlen($row['HOST_ID']) == 0) {
                //ホストID未登録
                if($log_level === "DEBUG") {
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90041",array($in_tableNameToMenuIdList[$table_name],$row[DF_ITA_LOCAL_PKEY]));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }

                $warning_flag = 1;
                //次のデータへ
                continue;
            }

            $host_id = $row['HOST_ID'];

            // 代入値自動登録設定に登録されている変数に対応する具体値を取得する。
            foreach($row as $col_name=>$col_val) {
                $col_val_key = $col_val;

                switch($col_name) {
                // 具体値カラム以外を除外
                case DF_ITA_LOCAL_OPERATION_CNT:
                case DF_ITA_LOCAL_HOST_CNT:
                case DF_ITA_LOCAL_DUP_CHECK_ITEM:
                case "OPERATION_ID":
                case "HOST_ID":
                case DF_ITA_LOCAL_PKEY:
                    continue 2;
                }

                //再度カラムをチェック
                if( ! isset($in_tabColNameToValAssRowList[$table_name][$col_name])) {
                    continue;
                }

                foreach($in_tabColNameToValAssRowList[$table_name][$col_name] as $col_data) {
                    // テーブル名+カラム名の情報にエラーがないか判定
                    if(   isset($in_errorColumnIdList[$col_data['COLUMN_ID']])) {
                        continue;
                    }

                    // IDcolumnの場合は参照元から具体値を取得する
                    if("" != $col_data['REF_TABLE_NAME']){
                        $sql = "";
                        $sql = $sql . "SELECT " . $col_data['REF_COL_NAME'] . " ";
                        $sql = $sql . "FROM   " . $col_data['REF_TABLE_NAME'] . " ";
                        $sql = $sql . "WHERE " . $col_data['REF_PKEY_NAME'] . "=:" . $col_data['REF_PKEY_NAME'] . " ";
                        $sql = $sql . " AND DISUSE_FLAG='0'";

                        $objQuery = $objDBCA->sqlPrepare($sql);
                        if($objQuery->getStatus()===false){
                            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                            unset($objQuery);
                            continue;
                        }

                        $objQuery->sqlBind(array($col_data['REF_PKEY_NAME'] => $col_val_key));

                        $r = $objQuery->sqlExecute();
                        if (!$r){
                            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                            unset($objQuery);
                            continue;
                        }

                        // fetch行数を取得
                        $count = $objQuery->effectedRowCount();

                        // 1件ではない場合
                        if(1 != $count){
                            continue;
                        }
                        // fetch行を取得
                        $tgt_row = $objQuery->resultFetch();
                        $col_val = $tgt_row[$col_data['REF_COL_NAME']];
                        unset($objQuery);
                    }

                    // 代入値管理の登録に必要な情報を生成
                    makeVarsAssignData($table_name,
                                       $col_name,
                                       $col_val,
                                       $col_data['NULL_DATA_HANDLING_FLG'],
                                       $operation_id,
                                       $host_id,
                                       $col_data,
                                       $ina_vars_ass_list,
                                       $lv_varsAssChkList,
                                       $ina_array_vars_ass_list,
                                       $lv_arrayVarsAssChkList,
                                       $in_tableNameToMenuIdList[$table_name],
                                       $row[DF_ITA_LOCAL_PKEY]);
                    //戻り値は判定しない
                }
            }
        }
    }
}

////////////////////////////////////////////////////////////////////////////////
// F0008
// 処理内容
//   CMDB代入値紐付対象メニューの情報から代入値管理に登録する情報を生成
//
// パラメータ
//   $in_table_name:                テーブル名
//   $in_col_name:                  カラム名
//   $in_col_val:                   カラムの具体値
//   $in_null_data_handling_flg     代入値管理へのNULLデータ連携フラグ
//   $in_operation_id:              オペレーションID
//   $in_host_id:                   ホストID
//   $in_col_list:                  カラム情報配列
//   $ina_vars_ass_list:            一般変数・複数具体値変数用 代入値登録情報配列
//   $ina_vars_ass_chk_list:        一般変数・複数具体値変数用 代入順序重複チェック配列
//   $ina_array_vars_ass_list:      多次元変数配列変数用 代入値登録情報配列
//   $ina_array_vars_ass_chk_list:  多次元変数配列変数用 列順序重複チェック配列
//   $in_menu_id:                   紐付メニューID
//   $in_row_id:                    紐付テーブル主キー値
//
// 戻り値
//   true:   正常
//   false:  異常
////////////////////////////////////////////////////////////////////////////////
function makeVarsAssignData($in_table_name,
                            $in_col_name,
                            $in_col_val,
                            $in_null_data_handling_flg,
                            $in_operation_id,
                            $in_host_id,
                            $in_col_list,
                            &$ina_vars_ass_list,
                            &$ina_vars_ass_chk_list,
                            &$ina_array_vars_ass_list,
                            &$ina_array_vars_ass_chk_list,
                            $in_menu_id,
                            $in_row_id) {

    global $log_level;
    global $objMTS;
    global $objDBCA;

    // 外部CMDBメニューでColumnGroupを使用したカラムを読取ると
    // ロードテーブルより読み取るとColumnGroup名/ColumnTitleになる。
    // 代入値管理への登録はColumnTitleのみにする。
    $col_name_array = explode("/",$in_col_list['COL_TITLE']);
    if(($col_name_array === false) ||
       (count($col_name_array) == 1)){
        $col_name = $in_col_list['COL_TITLE'];
    }
    else{
        $idx = count($col_name_array);
        $idx = $idx - 1;
        $col_name = $col_name_array[$idx];
    }

    //カラムタイプを判定
    if($in_col_list['COL_TYPE'] == DF_COL_TYPE_VAL ||
        $in_col_list['COL_TYPE'] == DF_COL_TYPE_KEYVAL) {
        // Value型カラムの場合
        //具体値が空白または1024バイト以上ないか判定
        $ret = validateValueTypeColValue($in_col_val,
                                         $in_null_data_handling_flg,
                                         $in_menu_id,$in_row_id,$in_col_list['COL_TITLE']);
        if($ret === false) {
            return;
        }

        // checkAndCreateVarsAssignDataの戻りは判定しない。
        checkAndCreateVarsAssignData($in_table_name,
                                     $in_col_name,
                                     $in_col_list['VAL_VAR_TYPE'],
                                     $in_operation_id,
                                     $in_host_id,
                                     $in_col_list['PATTERN_ID'],
                                     $in_col_list['VAL_VARS_LINK_ID'],
                                     $in_col_list['VAL_COL_SEQ_COMBINATION_ID'],
                                     $in_col_list['VAL_ASSIGN_SEQ'],
                                     $in_col_val,
                                     $ina_vars_ass_list,
                                     $ina_vars_ass_chk_list,
                                     $ina_array_vars_ass_list,
                                     $ina_array_vars_ass_chk_list,
                                     $in_menu_id,
                                     $in_col_list['COLUMN_ID'],
                                     "Value",
                                     $in_row_id);
    }

    if($in_col_list['COL_TYPE'] == DF_COL_TYPE_KEY ||
       $in_col_list['COL_TYPE'] == DF_COL_TYPE_KEYVAL) {
        if($in_col_list['COL_TYPE'] == DF_COL_TYPE_KEY)
        {
            //具体値が空白か判定
            $ret = validateKeyTypeColValue($in_col_val,$in_menu_id,$in_row_id,$in_col_list['COL_TITLE']);
            if($ret === false) {
                // 空白の場合処理対象外
                return;
            }
        }

        // checkAndCreateVarsAssignDataの戻りは判定しない。
        checkAndCreateVarsAssignData($in_table_name,
                                     $in_col_name,
                                     $in_col_list['KEY_VAR_TYPE'],
                                     $in_operation_id,
                                     $in_host_id,
                                     $in_col_list['PATTERN_ID'],
                                     $in_col_list['KEY_VARS_LINK_ID'],
                                     $in_col_list['KEY_COL_SEQ_COMBINATION_ID'],
                                     $in_col_list['KEY_ASSIGN_SEQ'],
                                     $col_name,
                                     $ina_vars_ass_list,
                                     $ina_vars_ass_chk_list,
                                     $ina_array_vars_ass_list,
                                     $ina_array_vars_ass_chk_list,
                                     $in_menu_id,
                                     $in_col_list['COLUMN_ID'],
                                     "Key",
                                     $in_row_id);
    }
}

////////////////////////////////////////////////////////////////////////////////
// F0009
// 処理内容
//   CMDB代入値紐付対象メニューの情報から代入値管理に登録する情報を生成
//
// パラメータ
//   $in_table_name:                テーブル名
//   $in_col_name:                  カラム名
//   $in_vars_attr:                 変数区分 (1:一般変数, 2:複数具体値, 3:多次元変数)
//   $in_operation_id:              オペレーションID
//   $in_host_id:                   ホスト名
//   $in_patten_id:                 パターンID
//   $in_vars_link_id:              変数ID
//   $in_col_seq_combination_id:    メンバー変数ID
//   $in_vars_assign_seq:           代入順序
//   $in_col_val:                   具体値
//   $ina_vars_ass_list:            一般変数・複数具体値変数用 代入値登録情報配列
//   $ina_vars_ass_chk_list:        一般変数・複数具体値変数用 代入順序重複チェック配列
//   $ina_array_vars_ass_list:      多次元変数配列変数用 代入値登録情報配列
//   $ina_array_vars_ass_chk_list:  多次元変数配列変数用 列順序重複チェック配列
//   $in_menu_id:                   紐付メニューID
//   $in_column_id:                 代入値自動登録設定
//   $keyValueType                  Value/Key
//   $in_row_id:                    紐付テーブル主キー値
//
// 戻り値
//   なし
////////////////////////////////////////////////////////////////////////////////
function checkAndCreateVarsAssignData($in_table_name,
                                      $in_col_name,
                                      $in_vars_attr,
                                      $in_operation_id,
                                      $in_host_id,
                                      $in_patten_id,
                                      $in_vars_link_id,
                                      $in_col_seq_combination_id,
                                      $in_vars_assign_seq,
                                      $in_col_val,
                                      &$ina_vars_ass_list,
                                      &$ina_vars_ass_chk_list,
                                      &$ina_array_vars_ass_list,
                                      &$ina_array_vars_ass_chk_list,
                                      $in_menu_id,
                                      $in_column_id,
                                      $keyValueType,
                                      $in_row_id) {

    global $log_level;
    global $objMTS;
    global $objDBCA;

    $chk_status = false;

    //変数のタイプを判定
    switch($in_vars_attr) {
    case GC_VARS_ATTR_STD:             // 一般変数
    case GC_VARS_ATTR_LIST:            // 複数具体値
        //一般変数・複数具体値
        // オペ+作業+ホスト+変数の組合せで代入順序が重複していないか判定
        if( isset($ina_vars_ass_chk_list[$in_operation_id]
                                        [$in_patten_id]
                                        [$in_host_id]
                                        [$in_vars_link_id]
                                        [$in_vars_assign_seq])) {
            // 既に登録されている
            if($log_level === "DEBUG") {
                $dup_info = $ina_vars_ass_chk_list[$in_operation_id]
                                                  [$in_patten_id]
                                                  [$in_host_id]
                                                  [$in_vars_link_id]
                                                  [$in_vars_assign_seq];
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90050",array($in_menu_id,
                                                                                $in_row_id,
                                                                                $in_operation_id,
                                                                                $in_patten_id,
                                                                                $in_host_id,
                                                                                $in_column_id,
                                                                                $keyValueType,
                                                                                $dup_info['MENU_ID'],
                                                                                $dup_info[DF_ITA_LOCAL_PKEY]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
        } else {
            $chk_status = true;
            // オペ+作業+ホスト+変数+メンバ変数の組合せの代入順序退避
            $ina_vars_ass_chk_list[$in_operation_id]
                                  [$in_patten_id]
                                  [$in_host_id]
                                  [$in_vars_link_id]
                                  [$in_vars_assign_seq] = array(
                                                    'MENU_ID'         => $in_menu_id,
                                                    DF_ITA_LOCAL_PKEY => $in_row_id
                                                    );
        }
        // 代入値管理の登録に必要な情報退避
        $ina_vars_ass_list[] = array('TABLE_NAME'        => $in_table_name,
                                     'COL_NAME'          => $in_col_name,
                                     'OPERATION_NO_UAPK' => $in_operation_id,
                                     'PATTERN_ID'        => $in_patten_id,
                                     'SYSTEM_ID'         => $in_host_id,
                                     'VARS_LINK_ID'      => $in_vars_link_id,
                                     'ASSIGN_SEQ'        => $in_vars_assign_seq,
                                     'VARS_ENTRY'        => $in_col_val,
                                     'VAR_TYPE'          => $in_vars_attr,
                                     'STATUS'            => $chk_status);
        break;
    case GC_VARS_ATTR_M_ARRAY:         // 多次元変数
        //多次元変数
        // オペ+作業+ホスト+変数+メンバ変数の組合せで代入順序が重複していないか判定
        if( isset($ina_array_vars_ass_chk_list[$in_operation_id]
                                              [$in_patten_id]
                                              [$in_host_id]
                                              [$in_vars_link_id]
                                              [$in_col_seq_combination_id]
                                              [$in_vars_assign_seq])) {
            // 既に登録されている
            if($log_level === "DEBUG") {
                $dup_info = $ina_array_vars_ass_chk_list[$in_operation_id]
                                                        [$in_patten_id]
                                                        [$in_host_id]
                                                        [$in_vars_link_id]
                                                        [$in_col_seq_combination_id]
                                                        [$in_vars_assign_seq];
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90209",array($in_menu_id,
                                                                                $in_row_id,
                                                                                $in_operation_id,
                                                                                $in_patten_id,
                                                                                $in_host_id,
                                                                                $in_column_id,
                                                                                $keyValueType,
                                                                                $dup_info['MENU_ID'],
                                                                                $dup_info[DF_ITA_LOCAL_PKEY]));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
        } else {
            $chk_status = true;
            // オペ+作業+ホスト+変数+メンバ変数の組合せの列順序退避
            $ina_array_vars_ass_chk_list[$in_operation_id]
                                        [$in_patten_id]
                                        [$in_host_id]
                                        [$in_vars_link_id]
                                        [$in_col_seq_combination_id]
                                        [$in_vars_assign_seq] = array(
                                                    'MENU_ID'           => $in_menu_id,
                                                    DF_ITA_LOCAL_PKEY   => $in_row_id
                                                    );
        }
        // 代入値管理の登録に必要な情報退避
        $ina_array_vars_ass_list[] = array('TABLE_NAME'            => $in_table_name,
                                           'COL_NAME'              => $in_col_name,
                                           'OPERATION_NO_UAPK'     => $in_operation_id,
                                           'PATTERN_ID'            => $in_patten_id,
                                           'SYSTEM_ID'             => $in_host_id,
                                           'VARS_LINK_ID'          => $in_vars_link_id,
                                           'COL_SEQ_COMBINATION_ID'=> $in_col_seq_combination_id,
                                           'ASSIGN_SEQ'            => $in_vars_assign_seq,
                                           'VARS_ENTRY'            => $in_col_val,
                                           'VAR_TYPE'              => $in_vars_attr,
                                           'STATUS'                => $chk_status);
        break;
    }
}

////////////////////////////////////////////////////////////////////////////////
// F0010
// 処理内容
//   代入値管理（一般変数・複数具体値変数）を更新する。
//   
// パラメータ
//   $in_varsAssignList:              代入値管理更新情報配列
//   $in_VarsAssignRecodes:           代入値管理の全テータ配列
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg1StdListVarsAssign($in_varsAssignList, &$in_VarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;


    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $key = $in_varsAssignList["OPERATION_NO_UAPK"] . "_" .
           $in_varsAssignList["PATTERN_ID"]        . "_" .
           $in_varsAssignList["SYSTEM_ID"]         . "_" .
           $in_varsAssignList["VARS_LINK_ID"]      . "_" .
           ""  . "_" .
           $in_varsAssignList["ASSIGN_SEQ"]        . "_" .
           "0";
    $hit_flg = false;
    // 代入値管理に登録されているか判定
    if(isset($in_VarsAssignRecodes[$key]))
    {
        // 具体値が一致しているか判定
        if($in_VarsAssignRecodes[$key]['VARS_ENTRY'] == $in_varsAssignList['VARS_ENTRY']) {
            // 代入値管理に必要なレコードを削除
            unset($in_VarsAssignRecodes[$key]);

            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70026",
                                            array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                  $in_varsAssignList['PATTERN_ID'],
                                                  $in_varsAssignList['SYSTEM_ID'],
                                                  $in_varsAssignList['VARS_LINK_ID'],
                                                  $in_varsAssignList['ASSIGN_SEQ']));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }


            return true; 
        } else {
            $hit_flg = true;
            $tgt_row = $in_VarsAssignRecodes[$key];
            // 代入値管理に必要なレコードを削除
            unset($in_VarsAssignRecodes[$key]);
        }
    }

    // 代入値管理に有効レコードが未登録か判定
    if($hit_flg === false) {
        // 廃止レコードの復活または新規レコード追加する。
        return addStg2StdListVarsAssign($in_varsAssignList, $in_VarsAssignRecodes);

    } else {
        $action = "UPDATE";

        // 最終更新者が自分でない場合、更新処理はスキップする。
        if($tgt_row['LAST_UPDATE_USER'] != $db_valautostup_user_id) {


            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70046",
                                            array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                  $in_varsAssignList['PATTERN_ID'],
                                                  $in_varsAssignList['SYSTEM_ID'],
                                                  $in_varsAssignList['VARS_LINK_ID'],
                                                  $in_varsAssignList['ASSIGN_SEQ']));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            //更新処理はスキップ
            return true;
        }


        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70025",
                                            array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                  $in_varsAssignList['PATTERN_ID'],
                                                  $in_varsAssignList['SYSTEM_ID'],
                                                  $in_varsAssignList['VARS_LINK_ID'],
                                                  $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
    }

    // ロール管理ジャーナルに登録する情報設定
    $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
    if($seqValueOfJnlTable == -1) {
        return false;
    }
    $tgt_row['JOURNAL_SEQ_NO']          = $seqValueOfJnlTable;
    $tgt_row['VARS_ENTRY']              = $in_varsAssignList['VARS_ENTRY'];
    $tgt_row['COL_SEQ_COMBINATION_ID']  = "";
    $tgt_row['DISUSE_FLAG']             = "0";
    $tgt_row['LAST_UPDATE_USER']        = $db_valautostup_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "ASSIGN_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $tgt_row,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0011
// 処理内容
//   代入値管理（多次元配列変数）を更新する。
//   
// パラメータ
//   $in_varsAssignList:              代入値管理更新情報配列
//   $in_VarsAssignRecodes:           代入値管理の全テータ配列
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg1ArrayVarsAssign($in_varsAssignList, &$in_ArryVarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;

    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $key = $in_varsAssignList["OPERATION_NO_UAPK"] . "_" .
           $in_varsAssignList["PATTERN_ID"]        . "_" .
           $in_varsAssignList["SYSTEM_ID"]         . "_" .
           $in_varsAssignList["VARS_LINK_ID"]      . "_" .
           $in_varsAssignList["COL_SEQ_COMBINATION_ID"]  . "_" .
           $in_varsAssignList["ASSIGN_SEQ"]        . "_" .
           "0";

    $hit_flg = false;
    // 代入値管理に登録されているか判定
    if(isset($in_ArryVarsAssignRecodes[$key]))
    {
        // 具体値が一致しているか判定
        if($in_ArryVarsAssignRecodes[$key]['VARS_ENTRY'] == $in_varsAssignList['VARS_ENTRY']) {

            // 代入値管理に必要なレコードはリストから削除
            unset($in_ArryVarsAssignRecodes[$key]);
            return true; 
        }
        $hit_flg = true;
        $tgt_row = $in_ArryVarsAssignRecodes[$key];
        // 代入値管理に必要なレコードはリストから削除
        unset($in_ArryVarsAssignRecodes[$key]);
    }

    // 代入値管理に有効レコードが未登録か判定
    if($hit_flg === false) {
        // 廃止レコードの復活または新規レコード追加する。
        return addStg2ArrayVarsAssign($in_varsAssignList, $in_ArryVarsAssignRecodes);
    } else {

        $action = "UPDATE";

        // 最終更新者が自分でない場合、更新処理はスキップする。
        if($tgt_row['LAST_UPDATE_USER'] != $db_valautostup_user_id) {

            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70051",
                                            array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                  $in_varsAssignList['PATTERN_ID'],
                                                  $in_varsAssignList['SYSTEM_ID'],
                                                  $in_varsAssignList['VARS_LINK_ID'],
                                                  $in_varsAssignList['COL_SEQ_COMBINATION_ID'],
                                                  $in_varsAssignList['ASSIGN_SEQ']));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }

            //更新処理はスキップ
            return true;
        }

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70049",
                                            array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                  $in_varsAssignList['PATTERN_ID'],
                                                  $in_varsAssignList['SYSTEM_ID'],
                                                  $in_varsAssignList['VARS_LINK_ID'],
                                                  $in_varsAssignList['COL_SEQ_COMBINATION_ID'],
                                                  $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
    }

    // ロール管理ジャーナルに登録する情報設定
    $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
    if($seqValueOfJnlTable == -1) {
        return false;
    }
    $tgt_row['JOURNAL_SEQ_NO']   = $seqValueOfJnlTable;
    $tgt_row['VARS_ENTRY']       = $in_varsAssignList['VARS_ENTRY'];

    $tgt_row['DISUSE_FLAG']      = "0";
    $tgt_row['LAST_UPDATE_USER'] = $db_valautostup_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "ASSIGN_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $tgt_row,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0012
// 処理内容
//   代入値管理から不要なレコードを廃止
//   
// パラメータ
//   $in_VarsAssignRecodes:           代入値管理の全テータ配列
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function deleteVarsAssign($in_VarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;

    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $strPkey                = "ASSIGN_ID";

    foreach($in_VarsAssignRecodes as $key=>$tgt_row) {
        if($tgt_row['DISUSE_FLAG'] == '1')
        {

            //廃止レコードはなにもしない。
            continue;
        }

        // 最終更新者が自分でない場合、廃止処理はスキップする。
        if($tgt_row["LAST_UPDATE_USER"] != $db_valautostup_user_id) {
            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70038", array($tgt_row['ASSIGN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }

            //更新処理はスキップ
            continue;
        }

        // 廃止レコードにする。

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70031", array($tgt_row['ASSIGN_ID']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        // ロール管理ジャーナルに登録する情報設定
        $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
        if($seqValueOfJnlTable == -1) {
            return false;
        }
        $tgt_row['JOURNAL_SEQ_NO']   = $seqValueOfJnlTable;
        $tgt_row['DISUSE_FLAG']      = '1';
        $tgt_row['LAST_UPDATE_USER'] = $db_valautostup_user_id;

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

        if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
            unset($objQueryUtn_sel);
            return false;
        }
    }
    unset($objQueryUtn_sel);
    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0013
// 処理内容
//   作業対象ホストを更新する。
//   
// パラメータ
//   $in_phoLinkData:             作業対象ホスト更新情報配列
//   $in_PhoLinkRecodes:          作業対象ホストの全データ配列
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg1PhoLink($in_phoLinkData, &$in_PhoLinkRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTablePhoLnk;
    global $strJnlTablePhoLnk;
    global $strSeqOfCurTablePhoLnk;
    global $strSeqOfJnlTablePhoLnk;
    global $arrayConfigOfPhoLnk;
    global $arrayValueTmplOfPhoLnk;

    $strCurTable      = $strCurTablePhoLnk;
    $strJnlTable      = $strJnlTablePhoLnk;

    $arrayConfig      = $arrayConfigOfPhoLnk;
    $arrayValue       = $arrayValueTmplOfPhoLnk;

    $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
    $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;

    $key = $in_phoLinkData["OPERATION_NO_UAPK"] . "_" .
           $in_phoLinkData["PATTERN_ID"]        . "_" .
           $in_phoLinkData["SYSTEM_ID"]         . "_" .
           '0';
    if(! isset($in_PhoLinkRecodes[$key])) {
        // 廃止レコードを復活または新規レコード追加
        return addStg2PhoLink($in_phoLinkData, $in_PhoLinkRecodes);
    } else {

        //同一なので処理終了
        unset($in_PhoLinkRecodes[$key]);
        return true;
    }
}

////////////////////////////////////////////////////////////////////////////////
// F0014
// 処理内容
//   作業管理対象ホスト管理から不要なレコードを廃止
//   
// パラメータ
//   $in_PhoLinkRecodes:          不要な作業管理対象ホストの配列
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function deletePhoLink($in_PhoLinkRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTablePhoLnk;
    global $strJnlTablePhoLnk;
    global $strSeqOfCurTablePhoLnk;
    global $strSeqOfJnlTablePhoLnk;
    global $arrayConfigOfPhoLnk;
    global $arrayValueTmplOfPhoLnk;

    $strCurTable      = $strCurTablePhoLnk;
    $strJnlTable      = $strJnlTablePhoLnk;

    $arrayConfig      = $arrayConfigOfPhoLnk;
    $arrayValue       = $arrayValueTmplOfPhoLnk;

    $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
    $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;

    $strPkey                = "PHO_LINK_ID";

    foreach($in_PhoLinkRecodes as $key=>$tgt_row) {
        if($tgt_row['DISUSE_FLAG'] == '1')
        {

            //廃止レコードはなにもしない。
            continue;
        }
        // トレースメッセージ
        if($log_level === "DEBUG") {
             $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70034", array($tgt_row['PHO_LINK_ID']));
             LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        // 最終更新者が自分でない場合、廃止処理はスキップする。
        if($tgt_row['LAST_UPDATE_USER'] != $db_valautostup_user_id) {
            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70039", array($tgt_row['PHO_LINK_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }

            //更新処理はスキップ
            continue;
        }

        // 追加・更新した主キーリストに登録されていない場合は廃止レコードにする。
        $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
        if($seqValueOfJnlTable == -1) {
            return false;
        }
        $tgt_row['JOURNAL_SEQ_NO']   = $seqValueOfJnlTable;

        $tgt_row['DISUSE_FLAG']      = "1";
        $tgt_row['LAST_UPDATE_USER'] = $db_valautostup_user_id;

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

        if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
            unset($objQueryUtn_sel);
            return false;
        }
    }
    unset($objQueryUtn_sel);
    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F0015
// 処理内容
//   代入値管理（一般変数・複数具体値変数）の廃止レコードの復活またき新規レコード追加
//   
// パラメータ
//   $in_varsAssignList:              代入値管理更新情報配列
//   $in_VarsAssignRecodes:           代入値管理の全テータ配列
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg2StdListVarsAssign($in_varsAssignList, &$in_VarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;

    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $key = $in_varsAssignList["OPERATION_NO_UAPK"] . "_" .
           $in_varsAssignList["PATTERN_ID"]        . "_" .
           $in_varsAssignList["SYSTEM_ID"]         . "_" .
           $in_varsAssignList["VARS_LINK_ID"]      . "_" .
           ""  . "_" .
           $in_varsAssignList["ASSIGN_SEQ"]        . "_" .
           "1";
    $hit_flg = false;
    // 代入値管理に登録されているか判定
    if(! isset($in_VarsAssignRecodes[$key]))
    {
        $action  = "INSERT";
        $tgt_row = $arrayValue;

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70023",
                                                array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                      $in_varsAssignList['PATTERN_ID'],
                                                      $in_varsAssignList['SYSTEM_ID'],
                                                      $in_varsAssignList['VARS_LINK_ID'],
                                                      $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
         }

    } else {
        // 廃止レコードがあるので復活する。
        $action = "UPDATE";
        $tgt_row = $in_VarsAssignRecodes[$key];

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70024",
                                                array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                      $in_varsAssignList['PATTERN_ID'],
                                                      $in_varsAssignList['SYSTEM_ID'],
                                                      $in_varsAssignList['VARS_LINK_ID'],
                                                      $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

    }

    if($action == "INSERT") {

        $seqValueOfCurTable = getAndLockSeq($strSeqOfCurTable);
        if($seqValueOfCurTable == -1) {
            return false;
        }

        // 登録する情報設定
        $tgt_row['ASSIGN_ID']          = $seqValueOfCurTable;
        $tgt_row['OPERATION_NO_UAPK']  = $in_varsAssignList['OPERATION_NO_UAPK'];
        $tgt_row['PATTERN_ID']         = $in_varsAssignList['PATTERN_ID'];
        $tgt_row['SYSTEM_ID']          = $in_varsAssignList['SYSTEM_ID'];
        $tgt_row['VARS_LINK_ID']       = $in_varsAssignList['VARS_LINK_ID'];
        $tgt_row['ASSIGN_SEQ']         = $in_varsAssignList['ASSIGN_SEQ'];

    }

    // ロール管理ジャーナルに登録する情報設定
    $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
    if($seqValueOfJnlTable == -1) {
        return false;
    }
    $tgt_row['JOURNAL_SEQ_NO']          = $seqValueOfJnlTable;
    $tgt_row['VARS_ENTRY']              = $in_varsAssignList['VARS_ENTRY'];
    $tgt_row['COL_SEQ_COMBINATION_ID']  = "";
    $tgt_row['DISUSE_FLAG']             = "0";
    $tgt_row['LAST_UPDATE_USER']        = $db_valautostup_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "ASSIGN_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $tgt_row,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0016
// 処理内容
//   代入値管理（多次元配列変数）の廃止レコードの復活またき新規レコード追加
//   
// パラメータ
//   $in_varsAssignList:              代入値管理更新情報配列
//   $in_VarsAssignRecodes:           代入値管理の全テータ配列
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg2ArrayVarsAssign($in_varsAssignList, &$in_ArryVarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;

    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $key = $in_varsAssignList["OPERATION_NO_UAPK"] . "_" .
           $in_varsAssignList["PATTERN_ID"]        . "_" .
           $in_varsAssignList["SYSTEM_ID"]         . "_" .
           $in_varsAssignList["VARS_LINK_ID"]      . "_" .
           $in_varsAssignList['COL_SEQ_COMBINATION_ID']  . "_" .
           $in_varsAssignList["ASSIGN_SEQ"]        . "_" .
           "1";
    $hit_flg = false;
    // 代入値管理に登録されているか判定
    if(! isset($in_ArryVarsAssignRecodes[$key]))
    {
         $action  = "INSERT";
         $tgt_row = $arrayValue;

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70047",
                                                array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                      $in_varsAssignList['PATTERN_ID'],
                                                      $in_varsAssignList['SYSTEM_ID'],
                                                      $in_varsAssignList['VARS_LINK_ID'],
                                                      $in_varsAssignList['COL_SEQ_COMBINATION_ID'],
                                                      $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

    } else {
        // 廃止レコードがあるので復活する。
        $action = "UPDATE";
        $tgt_row = $in_ArryVarsAssignRecodes[$key];

        unset($in_ArryVarsAssignRecodes[$key]);

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70048",
                                                array($in_varsAssignList['OPERATION_NO_UAPK'],
                                                      $in_varsAssignList['PATTERN_ID'],
                                                      $in_varsAssignList['SYSTEM_ID'],
                                                      $in_varsAssignList['VARS_LINK_ID'],
                                                      $in_varsAssignList['COL_SEQ_COMBINATION_ID'],
                                                      $in_varsAssignList['ASSIGN_SEQ']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

    }

    if($action == "INSERT") {

        $seqValueOfCurTable = getAndLockSeq($strSeqOfCurTable);
        if($seqValueOfCurTable == -1) {
            return false;
        }

        // 登録する情報設定
        $tgt_row['ASSIGN_ID']              = $seqValueOfCurTable;
        $tgt_row['OPERATION_NO_UAPK']      = $in_varsAssignList['OPERATION_NO_UAPK'];
        $tgt_row['PATTERN_ID']             = $in_varsAssignList['PATTERN_ID'];
        $tgt_row['SYSTEM_ID']              = $in_varsAssignList['SYSTEM_ID'];
        $tgt_row['VARS_LINK_ID']           = $in_varsAssignList['VARS_LINK_ID'];
        $tgt_row['ASSIGN_SEQ']             = $in_varsAssignList['ASSIGN_SEQ'];
        $tgt_row['COL_SEQ_COMBINATION_ID'] = $in_varsAssignList['COL_SEQ_COMBINATION_ID'];

        // 追加する代入値管理主キー値を退避
        $inout_assingId = $tgt_row['ASSIGN_ID'];
    }

    // ロール管理ジャーナルに登録する情報設定
    $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
    if($seqValueOfJnlTable == -1) {
        return false;
    }
    $tgt_row['JOURNAL_SEQ_NO']   = $seqValueOfJnlTable;
    $tgt_row['VARS_ENTRY']       = $in_varsAssignList['VARS_ENTRY'];

    $tgt_row['DISUSE_FLAG']      = "0";
    $tgt_row['LAST_UPDATE_USER'] = $db_valautostup_user_id;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "ASSIGN_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $tgt_row,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F0017
// 処理内容
//   作業対象ホストの廃止レコードを復活または新規レコード追加
//   
// パラメータ
//   $in_phoLinkData:             作業対象ホスト更新情報配列
//   $in_PhoLinkRecodes:          作業対象ホストの全データ配列
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function addStg2PhoLink($in_phoLinkData, &$in_PhoLinkRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTablePhoLnk;
    global $strJnlTablePhoLnk;
    global $strSeqOfCurTablePhoLnk;
    global $strSeqOfJnlTablePhoLnk;
    global $arrayConfigOfPhoLnk;
    global $arrayValueTmplOfPhoLnk;

    $strCurTable      = $strCurTablePhoLnk;
    $strJnlTable      = $strJnlTablePhoLnk;

    $arrayConfig      = $arrayConfigOfPhoLnk;
    $arrayValue       = $arrayValueTmplOfPhoLnk;

    $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
    $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;

    $key = $in_phoLinkData["OPERATION_NO_UAPK"] . "_" .
           $in_phoLinkData["PATTERN_ID"]        . "_" .
           $in_phoLinkData["SYSTEM_ID"]         . "_" .
           '1';
    if(! isset($in_PhoLinkRecodes[$key])) {
        $action  = "INSERT";
        $tgt_row = $arrayValue;

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70032",
                                                 array($in_phoLinkData['OPERATION_NO_UAPK'],
                                                       $in_phoLinkData['PATTERN_ID'],
                                                       $in_phoLinkData['SYSTEM_ID']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
         }

    } else {
        // 廃止なので復活する。
        $action = "UPDATE";
        $tgt_row = $in_PhoLinkRecodes[$key];

        unset($in_PhoLinkRecodes[$key]);

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70033",
                                                 array($in_phoLinkData['OPERATION_NO_UAPK'],
                                                       $in_phoLinkData['PATTERN_ID'],
                                                       $in_phoLinkData['SYSTEM_ID']));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }
    }

    if($action == "INSERT") {

        $seqValueOfCurTable = getAndLockSeq($strSeqOfCurTable);
        if($seqValueOfCurTable == -1) {
            return false;
        }
        // 更新対象の作業対象ホスト管理主キー値を退避
        $inout_phoLinkId              = $seqValueOfCurTable;

        // 登録する情報設定
        $tgt_row['PHO_LINK_ID']       = $seqValueOfCurTable;
        $tgt_row['OPERATION_NO_UAPK'] = $in_phoLinkData['OPERATION_NO_UAPK'];
        $tgt_row['PATTERN_ID']        = $in_phoLinkData['PATTERN_ID'];
        $tgt_row['SYSTEM_ID']         = $in_phoLinkData['SYSTEM_ID'];

    }

    $tgt_row['DISUSE_FLAG']       = "0";
    $tgt_row['LAST_UPDATE_USER']  = $db_valautostup_user_id;

    // ロール管理ジャーナルに登録する情報設定
    $seqValueOfJnlTable = getAndLockSeq($strSeqOfJnlTable);
    if($seqValueOfJnlTable == -1) {
        return false;
    }
    $tgt_row['JOURNAL_SEQ_NO']       = $seqValueOfJnlTable;

    $temp_array = array();
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $action,
                                         "PHO_LINK_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $tgt_row,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];

    if(!recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind)) {
        return false;
    }

    return true;
}

function validateValueTypeColValue($in_col_val,
                                   $in_null_data_handling_flg,
                                   $in_menu_id,$in_row_id,$in_menu_title) {

    global    $objMTS;
    global    $log_level;

    //具体値が空白の場合
    if(strlen($in_col_val) == 0) {
        // 具体値が空でも代入値管理NULLデータ連携が有効か判定する
        if(getNullDataHandlingID($in_null_data_handling_flg) != '1') {
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90056",
                                 array($in_menu_id,$in_row_id,$in_menu_title));
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            return false;
        }
    }
    //具体値が1024バイト以上の場合
    if(strlen($in_col_val) > 1024) {
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90057",
                                 array($in_menu_id,$in_row_id,$in_menu_title));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        return false;
    }
    return true;
}

function validateKeyTypeColValue($in_col_val,$in_menu_id,$in_row_id,$in_menu_title) {

    global    $objMTS;
    global    $log_level;

    //具体値が空白の場合
    if(strlen($in_col_val) == 0) {
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90058",
                                 array($in_menu_id,$in_row_id,$in_menu_title));
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        return false;
    }
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

// シーケンスをロックして、採番した数値を返却
function getAndLockSeq($strSeqOfTable) {

    // シーケンスをロック
    $retArray = getSequenceLockInTrz($strSeqOfTable, "A_SEQUENCE");
    if($retArray[1] != 0) {
        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        return -1;
    }

    // シーケンスを採番
    $retArray = getSequenceValueFromTable($strSeqOfTable, "A_SEQUENCE", false);
    if($retArray[1] != 0) {
        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

        return -1;
    }

    return $retArray[0];
}

// 更新対象テーブルを更新し、履歴テーブルにレコード追加
// Bindが無い場合、nullかarray()を割り当てる
function recordUpdate($sqlUtnBody, $arrayUtnBind, $sqlJnlBody, $arrayJnlBind) {

    global    $objMTS;
    global    $objDBCA;

    try {
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if($objQueryUtn->getStatus() === false ||
            $objQueryJnl->getStatus() === false) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            $msgstr = $sqlJnlBody . "\n" . $arrayJnlBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return false;
        }

        if(empty($arrayUtnBind)) {$arrayUtnBind = array();}
        if(empty($arrayJnlBind)) {$arrayJnlBind = array();}
        if($objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            $msgstr = $sqlJnlBody . "\n" . $arrayJnlBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn != true) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl != true) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlJnlBody . "\n" . $arrayJnlBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return false;
        }
    } finally {
        unset($objQueryUtn);
        unset($objQueryJnl);
    }

    return true;
}

function LocalLogPrint($p1,$p2,$p3) {

    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $root_dir_path;
    global $log_output_php;
    $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
    require ($root_dir_path . $log_output_php);
}
////////////////////////////////////////////////////////////////////////////////
// F0017
// 処理内容
//   インターフェース情報を取得する。
//
// パラメータ
//   $ina_ans_if_info:        インターフェース情報
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function getIFInfoDB(&$ina_if_info,&$in_error_msg)
{
    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    // SQL作成
    $sql = "SELECT * FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG = '0'";

    // SQL準備
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
    while ( $row = $objQuery->resultFetch() )
        $ina_if_info = $row;
        
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    // レコード無しの場合は「ANSIBLEインタフェース情報」が登録されていない
    if( $num_of_rows === 0 ){
        if ( $log_level === 'DEBUG' ){
            //ANSIBLEインタフェース情報レコード無し
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56000");
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
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
//   パラメータシートの具体値がNULLの場合でも代入値管理ら登録するかを判定
//
// パラメータ
//   $in_null_data_handling_flg:    代入値自動登録設定のNULL登録フラグ
// 戻り値
//   '1':有効    '2':無効
////////////////////////////////////////////////////////////////////////////////
function getNullDataHandlingID($in_null_data_handling_flg)
{
    global $g_null_data_handling_def;
    //代入値自動登録設定のNULL登録フラグ判定
    switch($in_null_data_handling_flg) {
    case '1':   // 有効
        $id = '1'; break;
    case '2':   // 無効
        $id = '2'; break;
    default:    // インターフェース情報に従う
        // インターフェース情報のNULL登録フラグ判定
        switch($g_null_data_handling_def) {
        case '1':   // 有効
            $id = '1'; break;
        case '2':   // 無効
            $id = '2'; break;
        }
    }
    return($id);
}

////////////////////////////////////////////////////////////////////////////////
// F0018
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
// F0019
// 処理内容
//   代入値管理の情報を取得
//   
// パラメータ
//   $in_VarsAssignRecodes:      代入値管理に登録されている変数リスト
//   $ln_ArryVarsAssignRecodes:  代入値管理に登録されている多段変数リスト
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function getVarsAssignRecodes(&$in_VarsAssignRecodes,&$in_ArryVarsAssignRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTableVarsAss;
    global $strJnlTableVarsAss;
    global $strSeqOfCurTableVarsAss;
    global $strSeqOfJnlTableVarsAss;
    global $arrayConfigOfVarAss;
    global $arrayValueTmplOfVarAss;

    $strCurTable      = $strCurTableVarsAss;
    $strJnlTable      = $strJnlTableVarsAss;

    $arrayConfig      = $arrayConfigOfVarAss;
    $arrayValue       = $arrayValueTmplOfVarAss;

    $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
    $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

    $strPkey                = "ASSIGN_ID";

    // 廃止レコードも含めてる。 WHERE句がないと全件とれない模様
    $temp_array = array('WHERE'=>"$strPkey>'0'");

    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         "SELECT FOR UPDATE",
                                         $strPkey,
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $arrayValue,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    $objQueryUtn_sel = recordSelect($sqlUtnBody, $arrayUtnBind);
    if($objQueryUtn_sel == null) {
        return false;
    }

    while($row = $objQueryUtn_sel->resultFetch()) {
       $key = $row["OPERATION_NO_UAPK"] . "_" .
              $row["PATTERN_ID"]        . "_" .
              $row["SYSTEM_ID"]         . "_" .
              $row["VARS_LINK_ID"]      . "_" .
              $row["COL_SEQ_COMBINATION_ID"]  . "_" .
              $row["ASSIGN_SEQ"]        . "_" .
              $row["DISUSE_FLAG"];
       if(strlen($row["COL_SEQ_COMBINATION_ID"]) == 0) {
           $in_VarsAssignRecodes[$key] = $row;
       } else {
           $in_ArryVarsAssignRecodes[$key] = $row;
       }
    }
    unset($objQueryUtn_sel);
    return true;
}

////////////////////////////////////////////////////////////////////////////////
// F0020
// 処理内容
//   作業対象ホストの全データ読込
//   
// パラメータ
//   $in_PhoLinkRecodes:     作業対象ホストの全データ配列
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function getPhoLinkRecodes(&$in_PhoLinkRecodes) {

    global    $db_model_ch;
    global    $objMTS;
    global    $objDBCA;
    global    $log_level;

    global $db_valautostup_user_id;
    global $strCurTablePhoLnk;
    global $strJnlTablePhoLnk;
    global $strSeqOfCurTablePhoLnk;
    global $strSeqOfJnlTablePhoLnk;
    global $arrayConfigOfPhoLnk;
    global $arrayValueTmplOfPhoLnk;

    $strCurTable      = $strCurTablePhoLnk;
    $strJnlTable      = $strJnlTablePhoLnk;

    $arrayConfig      = $arrayConfigOfPhoLnk;
    $arrayValue       = $arrayValueTmplOfPhoLnk;

    $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
    $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;

    $temp_array = array('WHERE'=>"PHO_LINK_ID <> '0'");
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         "SELECT FOR UPDATE",
                                         "PHO_LINK_ID",
                                         $strCurTable,
                                         $strJnlTable,
                                         $arrayConfig,
                                         $arrayValue,
                                         $temp_array);

    $sqlUtnBody = $retArray[1];

    $arrayUtnBind = array();

    $objQueryUtn = recordSelect($sqlUtnBody, $arrayUtnBind);
    if($objQueryUtn == null) {
        return false;
    }

    while($row = $objQueryUtn->resultFetch()) {
       $key = $row["OPERATION_NO_UAPK"] . "_" .
              $row["PATTERN_ID"]        . "_" .
              $row["SYSTEM_ID"]         . "_" .
              $row["DISUSE_FLAG"];
       $in_PhoLinkRecodes[$key] = $row;
    }
    unset($objQueryUtn);
    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F0021
// 処理内容
//   指定されたA_SEQUENCEのレコードをロックする。
//   
// パラメータ
//   $aryTgtOfSequenceLock:  ロック対象のシーケンス名のリスト
// 
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function SequenceTableLock($aryTgtOfSequenceLock) {

    // デッドロック防止のために、昇順でロック
    asort($aryTgtOfSequenceLock);

    foreach($aryTgtOfSequenceLock as $strSeqName) {
        //ジャーナルのシーケンス
        $retArray = getSequenceLockInTrz($strSeqName, "A_SEQUENCE");
        if($retArray[1] != 0) {
            return false;
        }
    }
    return true;
}
////////////////////////////////////////////////////////////////////////////////
// F00T22
// 処理内容
//   関連するデータベースが更新されバックヤード処理を実行する必要があるか判定
//
// パラメータ
//   &$inout_UpdateRecodeInfo:    バックヤード処理を実行する必要がある場合
//                                A_PROC_LOADED_LISTのROW_IDとLAST_UPDATE_TIMESTAMPを待避
//
// 戻り値
//   True:正常　　False:異常
////////////////////////////////////////////////////////////////////////////////
function setBackyardExecuteComplete($inout_UpdateRecodeInfo)
{                         
    $sql =            " UPDATE A_PROC_LOADED_LIST SET                              \n";
    $sql = $sql .     "   LOADED_FLG = '1'                                         \n";
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
$beforeTime = 0;
function TimeStampPrint($logdata)
{
global $objMTS;
global $objDBCA;
global $log_level;
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
    LocalLogPrint(basename(__FILE__),__LINE__,"$strtime,$difftime," . $logdata);
}

?>
