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
    //      保存期間切れ作業インスタンス履歴定期廃止／物理削除
    //
    //  【起動パラメータ】
    //      php 本スクリプト パラメータファイル
    //      パラメータファイルの拡張子を「.debug」にすることでDBのコミット(ロールバックする。)
    //      及び履歴データパス配下の不要なディレクトリ削除を行わない。
    //
    //  【パラメータファイル】
    //      定義内容
    //        p1,p2,p3,p4,p5,p6,p7,p8,p9,p10,p11
    //        
    //        p1: 廃止までの日数(1～)
    //        
    //        p2: 物理削除までの日数(1～)
    //        ※起算日は投入オペレーション一覧の実施予定日
    //        
    //        np3: 物理テーブル名        (Ansible Legacy 作業インスタンスの場合:C_ANSIBLE_LNS_EXE_INS_MNG)
    //        
    //        p4: 主キー名              (Ansible Legacy 作業インスタンスの場合:EXECUTION_NO)
    //        
    //        p5: オペレーションID      (Ansible Legacy 作業インスタンスの場合:OPERATION_NO_UAPK)
    //        
    //        p6: 最終更新者ID          (Ansible Legacy の場合                :-100015)
    //        
    //        p7: 履歴データパス1～4でインターフェース情報のデータストレージ配下のパスを指定している場合
    //            インターフェース情報のデータストレージ情報を取得するSELECT文を記載します。
    //            データストレージ配下のパスを指定していない場合は省略可能です。
    //              exp)
    //                select ANSIBLE_STORAGE_PATH_LNX AS PATH from B_ANSIBLE_IF_INFO where DISUSE_FLAG='0';
    //                ※必ずエーリアス名にPPATHを設定して下さい。
    //        
    //        p8: 履歴データパス1(省略可能)
    //              ・作業インスタンスディレクトリの投入データ履歴や結果データ履歴など、/???/ita-root/配下のパスの場合は
    //                /???/ita-oot/からの相対パスを記載します。
    //                  exp)
    //                    Ansible Legacy 投入データ履歴の場合
    //                      uploadfiles/ansible_driver/legacy/ns/execution_management/FILE_INPUT
    //                    Ansible Legacy 結果データ履歴の場合
    //                      uploadfiles/ansible_driver/legacy/ns/execution_management/FILE_RESULT
    //        
    //              ・作業インスタンスディレクトリ(データストレージ)など、インターフェース情報のデータストレージ配下
    //                のパスの場合はデータストレージを示すキーワード「/__data_relay_storage__/」を付けたパスを記載します。
    //                  exp)
    //                    Ansible Legacy 作業インスタンスディレクトリの場合
    //                     /__data_relay_storage__/legacy/ns/
    //        
    //              ・/???/ita-root/配下またはデータストレージ配下以外のパスの場合は絶対パスを記載します。
    //                  exp)
    //                    /var/log/hoge
    //        
    //              履歴データパス2～履歴データパス4も同様
    //        
    //        p9: 履歴データパス2(省略可能)
    //        p10:履歴データパス3(省略可能)
    //        p11:履歴データパス4(省略可能)
    //        
    //  【特記事項】
    //      パラメータファイルの行数に関係なく、定義されているテーブル毎にロック(A_SEQUENCE)
    //      しトランザクション処理する。
    //      特定のテーブルに対する処理が失敗した場合は、ロールバックし次のテーブルに対して処理を行う。
    //      トランザクション処理に失敗した場合は異常終了する。
    //      <<引数>>
    //        パラメータファイル
    //      <<返却値>>
    //        0:正常 他:異常
    //
    // F0001  readParameterFile
    // F0002  ExpireDataDelete
    // F0003  LogicalDeleteDB
    // F0004  PhysicalDeleteDB
    // F0005  WorkDirectoryDelete
    //////////////////////////////////////////////////////////////////////
    
    // 起動しているshellの起動判定を正常にするための待ち時間
    sleep(5);
    
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

    define("DF_MSG_ERR_000", "ITABASEH-ERR-1990000");
    define("DF_MSG_ERR_001", "ITABASEH-ERR-1990001");
    define("DF_MSG_ERR_002", "ITABASEH-ERR-1990002");
    define("DF_MSG_ERR_003", "ITABASEH-ERR-1990003");
    define("DF_MSG_ERR_004", "ITABASEH-ERR-1990004");
    define("DF_MSG_ERR_005", "ITABASEH-ERR-1990005");
    define("DF_MSG_ERR_006", "ITABASEH-ERR-1990006");
    define("DF_MSG_ERR_007", "ITABASEH-ERR-1990007");
    define("DF_MSG_ERR_008", "ITABASEH-ERR-1990008");
    define("DF_MSG_ERR_009", "ITABASEH-ERR-1990009");
    define("DF_MSG_ERR_010", "ITABASEH-ERR-1990010");
    define("DF_MSG_ERR_011", "ITABASEH-ERR-1990011");
    define("DF_MSG_ERR_012", "ITABASEH-ERR-1990012");
    define("DF_MSG_ERR_013", "ITABASEH-ERR-1990013");
    define("DF_MSG_ERR_014", "ITABASEH-ERR-1990014");
    define("DF_MSG_ERR_015", "ITABASEH-ERR-1990015");
    define("DF_MSG_ERR_016", "ITABASEH-ERR-1990016");
    define("DF_MSG_ERR_017", "ITABASEH-ERR-1990017");
    define("DF_MSG_ERR_018", "ITABASEH-ERR-1990018");
    define("DF_MSG_ERR_019", "ITABASEH-ERR-1990019");
    define("DF_MSG_ERR_020", "ITABASEH-ERR-1990020");
    define("DF_MSG_ERR_021", "ITABASEH-ERR-1990021");
    define("DF_MSG_ERR_022", "ITABASEH-ERR-1990022");
    define("DF_MSG_ERR_023", "ITABASEH-ERR-1990023");
    define("DF_MSG_ERR_024", "ITABASEH-ERR-1990024");
    define("DF_MSG_ERR_025", "ITABASEH-ERR-1990025");
    define("DF_MSG_ERR_026", "ITABASEH-ERR-1990026");
    define("DF_MSG_ERR_027", "ITABASEH-ERR-1990027");
    define("DF_MSG_ERR_028", "ITABASEH-ERR-1990028");
    define("DF_MSG_ERR_029", "ITABASEH-ERR-1990029");
    define("DF_MSG_ERR_030", "ITABASEH-ERR-1990030");
    define("DF_MSG_ERR_031", "ITABASEH-ERR-1990031");
    define("DF_MSG_ERR_032", "ITABASEH-ERR-1990032");

    define("DF_MSG_STD_000", "ITABASEH-STD-1990000");
    define("DF_MSG_STD_001", "ITABASEH-STD-1990001");
    define("DF_MSG_STD_002", "ITABASEH-STD-1990002");
    define("DF_MSG_STD_003", "ITABASEH-STD-1990003");
    define("DF_MSG_STD_004", "ITABASEH-STD-1990004");
    define("DF_MSG_STD_005", "ITABASEH-STD-1990005");
    define("DF_MSG_STD_006", "ITABASEH-STD-1990006");
    define("DF_MSG_STD_007", "ITABASEH-STD-1990007");
    define("DF_MSG_STD_008", "ITABASEH-STD-1990008");
    define("DF_MSG_STD_009", "ITABASEH-STD-1990009");
    define("DF_MSG_STD_010", "ITABASEH-STD-1990010");
    define("DF_MSG_STD_011", "ITABASEH-STD-1990011");
    define("DF_MSG_STD_012", "ITABASEH-STD-1990012");
    define("DF_MSG_STD_013", "ITABASEH-STD-1990013");
    define("DF_MSG_STD_014", "ITABASEH-STD-1990014");
    define("DF_MSG_STD_015", "ITABASEH-STD-1990015");

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_001);  
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_002);  
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        $test_mode = false;
        ///////////////////////////////////////////
        // 起動パラメータの確認
        ///////////////////////////////////////////
        if($argc < 1){
            //$ary[xxxxx13] = "起動パラメータが不正です。";
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage(DF_MSG_ERR_013) );
        }

        /////////////////////////////////////////////
        // オペレーション削除管理から削除対象のテーブル情報を取得
        /////////////////////////////////////////////
        $Parameter_list = array();
        $ret = readParameterFile($Parameter_list);
        if( $ret === false){
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage(DF_MSG_ERR_014) );
        }

        $nomal_flag = 0;
        foreach($Parameter_list as $table_info){
            ////////////////////////////////
            // トランザクション開始       //
            ////////////////////////////////
            $ret = transactionStart($table_info['TABLE_NAME']);
            if($ret !== true){
                $FREE_LOG = $ret;
                $error_flag = 1;
                throw new Exception( $FREE_LOG );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // テーブル毎の開始メッセージ
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_008,array($table_info['TABLE_NAME']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            
            // 該当テーブルから保管期限切のデータ削除
            $ret = ExpireDataDelete($table_info);
            if($ret === false){
                // 警告フラグON
                $warning_flag = 1;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_015,array($table_info['TABLE_NAME']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }

                ////////////////////////////////
                // ロールバック               //
                ////////////////////////////////
                $ret = transactionRollback($table_info['TABLE_NAME']);
                if($ret !== true){
                    $FREE_LOG = $ret;
                    $error_flag = 1;
                    throw new Exception( $FREE_LOG );
                }

                continue;

            }
            // 完了をマーク
            $nomal_flag = 1;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // テーブル毎の終了メッセージ
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_009,array($table_info['TABLE_NAME']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

            // テストモードの場合はコミットしない
            if($test_mode === false){
                ////////////////////////////////
                // コミット                   //
                ////////////////////////////////
                $ret = transactionCommit($table_info['TABLE_NAME']);
                if($ret !== true){
                    $FREE_LOG = $ret;
                    $error_flag = 1;
                    throw new Exception( $FREE_LOG );
                }
            }
            else{
                ////////////////////////////////
                // ロールバック               //
                ////////////////////////////////
                $ret = transactionRollback($table_info['TABLE_NAME']);
                if($ret !== true){
                    $FREE_LOG = $ret;
                    $error_flag = 1;
                    throw new Exception( $FREE_LOG );
                }
            } 

        }
        if(($warning_flag == 1) && ($nomal_flag == 0)){
            $error_flag = 1;
        }
    }
    catch (Exception $e){
        global    $objMTS;
        global    $objDBCA;

        $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_001);
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_007);
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_002);
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_005);
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_003);
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_004);
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        // cron起動なので exit-code 2 で終了
        exit(2);
    }
    elseif( $warning_flag != 0 ){
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_005);
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }        
        // cron起動なので exit-code 1 で終了
        exit(1);
    }
    else{
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_003);
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        exit(0);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   オペレーション削除管理の情報を読取る
    //
    // パラメータ
    //   $in_file:                         保存期間定義ファイルパス
    //   $ina_Parameter_list:              パラメータ情報
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function readParameterFile(&$ina_Parameter_list){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;

        $ina_Parameter_list = array();

        /////////////////////////////////////////////////////////////////////
        // オペレーション削除管理のデータを取得する
        /////////////////////////////////////////////////////////////////////
        $sql = "SELECT " .
               " ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4 " .
               "FROM A_DEL_OPERATION_LIST " .
               "WHERE DISUSE_FLAG='0'";

        $objQuery = $objDBCA->sqlPrepare($sql);

        if($objQuery->getStatus()===false){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                               array("A_DEL_OPERATION_LIST")));
            unset($objQuery);
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                               array("A_DEL_OPERATION_LIST")));
            unset($objQuery);
            return false;
        }

        $delList = array();
        while ( $row = $objQuery->resultFetch() ){
            $delList[] = $row;
        }
        unset($objQuery);

        $line = 0;
        foreach($delList as $delData){
            $line = $line + 1;
            $tbl_info = array();

            // p1:廃止までの日数
            $tbl_info['LG_DAYS']    = $delData['LG_DAYS'];

            // p2:物理削除までの日数
            $tbl_info['PH_DAYS']    = $delData['PH_DAYS'];

            // 廃止までの日数の妥当性チェック
            if( strlen($tbl_info['LG_DAYS'])     == 0    ||
                // 数値判定
                is_numeric($tbl_info['LG_DAYS']) != true ||
                // 数値判定
                (int)$tbl_info['LG_DAYS']        <= 0    ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_018, array($delData['ROW_ID'],$delData['LG_DAYS']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                continue;
            }

            // 物理削除までの日数の妥当性チェック
            if( strlen($tbl_info['PH_DAYS'])     == 0    ||
                // 数値判定
                is_numeric($tbl_info['PH_DAYS']) != true ||
                // 数値判定
                (int)$tbl_info['PH_DAYS']        <= 0    ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_019, array($delData['ROW_ID'],$delData['PH_DAYS']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                continue;
            }

            // 廃止までの日数と物理削除までの日数の妥当性チェック
            if( (int)$tbl_info['LG_DAYS']        >= (int)$tbl_info['PH_DAYS'] ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_024, array($delData['ROW_ID'],$delData['LG_DAYS'],$delData['PH_DAYS']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                continue;
            }
            // 保存期間算出
            $tbl_info['LG_DATE'] = date('Y-m-d',strtotime("- ${tbl_info['LG_DAYS']} days"));
            $tbl_info['PH_DATE'] = date('Y-m-d',strtotime("- ${tbl_info['PH_DAYS']} days"));

            // 物理テーブル名
            $tbl_info['TABLE_NAME'] = $delData['TABLE_NAME'];

            // 主キー名
            $tbl_info['PKEY_NAME']  = $delData['PKEY_NAME'];

            // オペレーションID      (Ansible 作業インスタンスの場合:OPERATION_NO_UAPK)
            $tbl_info['OPE_ID_COL_NAME']  = $delData['OPE_ID_COL_NAME'];

            // 最終更新者ID
            $tbl_info['LAST_UPD_USER_ID']  = -100015;

            /////////////////////////////////////////////////////////////////////
            // インターフェース情報からデータストレージパスを取得する。
            /////////////////////////////////////////////////////////////////////
            $tbl_info['DATA_STORAGE'] = ""; 
            if(trim($delData['GET_DATA_STRAGE_SQL']) != ""){
                $sql = $delData['GET_DATA_STRAGE_SQL'];
                $objQuery = $objDBCA->sqlPrepare($sql);

                if($objQuery->getStatus()===false){
                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
                    LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                       array($sql)));
                    unset($objQuery);
                    continue;
                }
                $r = $objQuery->sqlExecute();
                if (!$r){
                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
                    LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_030,
                                                                                      array($delData['ROW_ID'], $sql)));
                    unset($objQuery);
                    continue;
                }
                while ( $row = $objQuery->resultFetch() ){
                    if(@strlen($row['PATH']) == 0){
                        LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_031,
                                                                                      array($delData['ROW_ID'], $sql)));
                        unset($objQuery);
                        continue;
                    }
                    $tbl_info['DATA_STORAGE'] = trim($row['PATH']);
                }
                unset($objQuery);
                if(@strlen($tbl_info['DATA_STORAGE'])==0){
                    LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_031,
                                                                                      array($delData['ROW_ID'], $sql)));
                    continue;
                }
            }

            // 履歴データディレクトリ1(省略可能)
            $ret = ChkHistoryDirectory($delData['DATA_PATH_1'], $tbl_info['DATA_STORAGE'], $tbl_info['DIR1']);
            if($ret === false)
                continue;

            // 履歴データディレクトリ2(省略可能)
            $ret = ChkHistoryDirectory($delData['DATA_PATH_2'], $tbl_info['DATA_STORAGE'], $tbl_info['DIR2']);
            if($ret === false)
                continue;

            // 履歴データディレクトリ3(省略可能)
            $ret = ChkHistoryDirectory($delData['DATA_PATH_3'], $tbl_info['DATA_STORAGE'], $tbl_info['DIR3']);
            if($ret === false)
                continue;

            // 履歴データディレクトリ4(省略可能)
            $ret = ChkHistoryDirectory($delData['DATA_PATH_4'], $tbl_info['DATA_STORAGE'], $tbl_info['DIR4']);
            if($ret === false)
                continue;

            /////////////////////////////////////////////////////////////////////
            // 対象テーブルとカラム名の存在を確認する。
            /////////////////////////////////////////////////////////////////////
            $sql = sprintf("SELECT %s , %s FROM %s\n",
                               $tbl_info['PKEY_NAME'],
                               $tbl_info['OPE_ID_COL_NAME'],
                               $tbl_info['TABLE_NAME']);

            $objQuery = $objDBCA->sqlPrepare($sql);

            if($objQuery->getStatus()===false){
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($tbl_info['TABLE_NAME'])));
                unset($objQuery);
                continue;
            }
            $r = $objQuery->sqlExecute();
            if (!$r){
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_025,
                                                                   array($delData['ROW_ID'])));
                unset($objQuery);
                continue;
            }
            unset($objQuery);

            // 履歴ディレクトリについてのチェックはしない。

            // パラメータ退避
            $ina_Parameter_list[] = $tbl_info;
        }
        return true;
    }


    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   保存期間切れデータを削除する。
    //
    // パラメータ
    //   $in_table_info:                   テーブル毎のパラメータ情報
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function ExpireDataDelete($in_table_info){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        
        $CurTable          = $in_table_info['TABLE_NAME'];  //テーブル名
        $JnlTable          = $CurTable . "_JNL";            //ジャーナルテーブル名
        $SeqOfCurTable     = $CurTable . "_RIC";            //テーブルシーケンス名
        $SeqOfJnlTable     = $CurTable . "_JSQ";            //ジャーナルシーケンス名

        ///////////////////////////////////////////////////
        // 関連シーケンスをロックする                    //
        // デッドロック防止のために、昇順でロック        //
        ///////////////////////////////////////////////////
        //----デッドロック防止のために、昇順でロック
        $aryTgtOfSequenceLock = array(
               $SeqOfCurTable
              ,$SeqOfJnlTable
        );
        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($strSeqName,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_012,array($strSeqName,$in_table_info['TABLE_NAME']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
        }
        //デッドロック防止のために、昇順でロック----
    
        ////////////////////////////////////////////////////
        // 廃止対象レコード抽出条件
        ////////////////////////////////////////////////////
        $string   = $in_table_info['OPE_ID_COL_NAME'] .
                    " IN ( " .
                           " SELECT " .
                           "   OPERATION_NO_UAPK " .
                           " FROM " .
                           "   C_OPERATION_LIST " .
                           " WHERE " .
                           "       OPERATION_DATE <= '" . $in_table_info['LG_DATE'] . "' " .
                    "    )" .
                    " AND DISUSE_FLAG = '0'";
        $where   = array('WHERE'=>$string);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_010,array($in_table_info['TABLE_NAME']));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
 
        if($log_level === 'DEBUG' ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_STD_015,
                                                                   array($CurTable,$in_table_info['LG_DATE'])));
        }

        ////////////////////////////////////////////////////
        // 該当テーブルから保管期限切れのレコードを廃止
        ////////////////////////////////////////////////////
        $ret = LogicalDeleteDB( $CurTable,      $JnlTable,
                                $SeqOfCurTable, $SeqOfJnlTable,
                                $where, $in_table_info['PKEY_NAME'], $in_table_info['OPE_ID_COL_NAME'], $in_table_info['LAST_UPD_USER_ID']);
        if($ret === false){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_016,array($in_table_info['TABLE_NAME']));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            return false;
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_011,array($in_table_info['TABLE_NAME']));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        //////////////////////////////////////////////////////////////////
        // 該当テーブルから保管期限切れのレコードを物理削除
        //////////////////////////////////////////////////////////////////
        $arr_table_list = array($CurTable,
                                $JnlTable);
    
        foreach($arr_table_list as $table_name){
            if($log_level === 'DEBUG' ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_STD_015,
                                                                   array($table_name,$in_table_info['PH_DATE'])));
            }

            $ret = PhysicalDeleteDB($table_name,$in_table_info['OPE_ID_COL_NAME'],$in_table_info['PH_DATE']);
            if($ret === false){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_017,array($in_table_info['TABLE_NAME']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                return false;
            }
        }
        //////////////////////////////////////////////////////////////////
        // 該当テーブルに登録されている作業インスタンスに紐づくディレクトリ
        // の情報を削除する。
        //////////////////////////////////////////////////////////////////
        if(($in_table_info['DIR1'] != "") ||
           ($in_table_info['DIR2'] != "") ||
           ($in_table_info['DIR3'] != "") ||
           ($in_table_info['DIR4'] != "")){
            $ret = WorkDirectoryDelete($in_table_info);
            if($ret === false){
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   作業対象ホスト管理から保管期限切れのレコードを廃止する。
    //   
    // パラメータ
    //   $in_strCurTable:                テーブル名  
    //   $in_strJnlTable:                ジャーナルテーブル名
    //   $in_strSeqOfCurTable:           テーブルシーケンス名
    //   $in_strSeqOfJnlTable:           ジャーナルシーケンス名
    //   $in_arrwhere:                   Where条件
    //   $in_pkey:                       Pkey項目名
    //   $in_ope_id:                     オペレーションID(Pkey)項目名
    //   $in_access_user_id:             データベース更新ユーザーID 
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function LogicalDeleteDB($in_strCurTable,                    $in_strJnlTable,
                             $in_strSeqOfCurTable,               $in_strSeqOfJnlTable,
                             $in_arrwhere, $in_pkey, $in_ope_id,$in_access_user_id){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $db_model_ch;

        $temp_array = $in_arrwhere;

        $arrayConfig = array("*" => "");

        $arrayValue = array(
             "JOURNAL_SEQ_NO"=>""
            ,"JOURNAL_REG_DATETIME"=>"" 
            ,"JOURNAL_ACTION_CLASS"=>"" 
            ,"DISP_SEQ"=>""             
            ,"NOTE"=>""                 
            ,"DISUSE_FLAG"=>""          
            ,"LAST_UPDATE_TIMESTAMP"=>""
            ,"LAST_UPDATE_USER"=>""     
            );
        $arrayValue[$in_pkey] = "";
        $arrayValue[$in_ope_id] = "";

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $in_pkey,
                                             $in_strCurTable,
                                             $in_strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn_sel->getStatus()===false ){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
            unset($objQueryUtn_sel);
            return false;
        }

        $objQueryUtn_sel->sqlBind($arrayUtnBind);

        $r = $objQueryUtn_sel->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn_sel->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
            unset($objQueryUtn_sel);
            return false;
        }

        $arrayConfig = array();

        // fetch行数を取得
        while ( $tgt_row = $objQueryUtn_sel->resultFetch() ){

            if(0 === count($arrayConfig)){
                $arrayConfig = array(
                     "JOURNAL_SEQ_NO"=>""
                    ,"JOURNAL_REG_DATETIME"=>"" 
                    ,"JOURNAL_ACTION_CLASS"=>""
                    );

                foreach($tgt_row as $key => $value){
                    $arrayConfig[$key] = "";
                }
            }
            if($log_level === 'DEBUG' ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_STD_012,
                                                                   array($in_strCurTable,$tgt_row[$in_ope_id],$tgt_row[$in_pkey])));
            }

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($in_strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
                unset($objQueryUtn_sel);
                return false;
            }

            // 廃止レコードにする。
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $in_access_user_id;

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $in_pkey,   
                                                 $in_strCurTable,
                                                 $in_strJnlTable,
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
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
                unset($objQueryUtn_sel);
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                                   array($in_strCurTable)));
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
    // F0004
    // 処理内容
    //   指定されたテーブルより保存日数に達したレコードと
    //   オペレーションIDの紐づかないレコードを物理削除する。
    //
    // パラメータ
    //   $in_strTable:                   テーブル名
    //   $in_ope_col_name:               オペレーションID カラム名
    //   $in_tgt_date:                   保存日数
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function PhysicalDeleteDB($in_strTable,$in_ope_col_name,$in_tgt_date){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;

        /////////////////////////////////////////////////////////////////////
        // オペレーションIDの紐づかないデータを物理削除
        /////////////////////////////////////////////////////////////////////
        $strsql = " delete TAB_A from %s TAB_A "   .
                   " where NOT EXISTS "      .
                   "   (select "             .
                   "      * "                .
                   "    from "               .
                   "      (select * from C_OPERATION_LIST) TAB_B " .
                   "    where "              .
                   "      TAB_A.%s = TAB_B.OPERATION_NO_UAPK " .
                   "   ) ";
                   

        $sql = sprintf($strsql, $in_strTable, $in_ope_col_name);

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                      array($in_strTable)));
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                      array($in_strTable)));

            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);

        /////////////////////////////////////////////////////////////////////
        // 保存日数に達したレコードを物理削除
        /////////////////////////////////////////////////////////////////////
        $strsql = " DELETE TAB_A from %s TAB_A "                                .
                  " where "                                                     .
                  "   TAB_A.%s IN ( "                                           .
                  "                SELECT "                                     .
                  "                  OPERATION_NO_UAPK "                        .
                  "                FROM "                                       .
                  "                  (SELECT * FROM C_OPERATION_LIST) TAB_B "   .
                  "                WHERE "                                      .
                  "                  TAB_B.OPERATION_DATE <= '%s' "             .
                                 ") ";

        $sql = sprintf($strsql,$in_strTable,$in_ope_col_name,$in_tgt_date);
        
        $objQuery = $objDBCA->sqlPrepare($sql);
        
        if($objQuery->getStatus()===false){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                      array($in_strTable)));
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                      array($in_strTable)));

            unset($objQuery);
            return false;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   該当テーブルに登録されている作業インスタンスに紐づくディレクトリ
    //   の情報を削除する。
    //
    // パラメータ
    //   $in_table_info:                 テーブル情報
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function WorkDirectoryDelete($in_table_info){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $test_mode;

        /////////////////////////////////////////////////////////////////////
        // 該当テーブルに登録されているレコードを取得
        /////////////////////////////////////////////////////////////////////
        $sql = sprintf("SELECT %s FROM %s\n",$in_table_info['PKEY_NAME'],$in_table_info['TABLE_NAME']);
        
        $objQuery = $objDBCA->sqlPrepare($sql);
        
        if($objQuery->getStatus()===false){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                     array($in_table_info['TABLE_NAME'])));
            return false;
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_009,
                                                  array($in_table_info['TABLE_NAME'])));
            unset($objQuery);
            return false;
        }
        while ( $row = $objQuery->resultFetch() ){
            $no = sprintf("%010d",$row[$in_table_info['PKEY_NAME']]);
            $pkey_list[$no] = 1; 
        }
        unset($objQuery);

        // 履歴データディレクトリの情報をまとめる
        if(strlen($in_table_info['DIR1']) != 0){
            $dir_list[] = $in_table_info['DIR1'];
        }
        if(strlen($in_table_info['DIR2']) != 0){
            $dir_list[] = $in_table_info['DIR2'];
        }
        if(strlen($in_table_info['DIR3']) != 0){
            $dir_list[] = $in_table_info['DIR3'];
        }
        if(strlen($in_table_info['DIR4']) != 0){
            $dir_list[] = $in_table_info['DIR4'];
        }
        // 履歴データディレクトリ毎にサブディレクトリを検索
        foreach($dir_list as $dir_name){
            if($log_level === 'DEBUG' ){
                LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_STD_013,
                                                                   array($in_table_info['TABLE_NAME'],$dir_name)));
            }
            // サブディレクトリの情報取得
            if( ! is_dir($dir_name)){
                if($log_level === 'DEBUG' ){
                    LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_032,
                                                             array($dir_name,$in_table_info['TABLE_NAME'])));
                }
                continue;
            }
            $files = scandir($dir_name);
            $files = array_filter($files,
                                  function ($file){
                                      return !in_array($file,array('.','..'));
                                  }
                                  );
            // サブディレクトリに作業番号のディレクトリがあるか判定
            foreach ($files as $sub_dirname){
                $tgt_dir = $dir_name . "/" . $sub_dirname;
                if(is_dir($tgt_dir)){
                    // 作業番号のディレクトリ(数値 10桁)か判定
                    $ret = preg_match('/^\d{10}$/',$sub_dirname);
                    if($ret != 1){
                        continue;
                    }
                    // 該当テーブルに残っている作業番号のディレクトリか判定
                    if(@count($pkey_list[$sub_dirname]) != 0){
                        continue;
                    }
                    if($log_level === 'DEBUG' ){
                        LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_STD_014,
                                                                   array($in_table_info['TABLE_NAME'],$sub_dirname)));
                    }

                    if($test_mode === false){
                        // 該当テーブルに残っていない作業番号のディレクトリなのでディレクトリ削除
                        exec('/bin/rm -rf ' . $tgt_dir. ' >/dev/null 2>&1' ,$arr ,$result);
                        if($result !== 0){
                            LocalLogPrint(basename(__FILE__),__LINE__,$objMTS->getSomeMessage(DF_MSG_ERR_028,
                                                   array($in_table_info['TABLE_NAME'],$tgt_dir)));
                        }
                    }
                }
            }
        }
        return true;
    }
    function transactionStart($table_name){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            $FREE = $objMTS->getSomeMessage(DF_MSG_ERR_010,array($table_name));
            return $FREE ;
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_004);
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        return true;
    }
    function transactionCommit($table_name){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;

        if( $objDBCA->getTransactionMode() ){
            ////////////////////////////////////////////////////////////////
            // コミット(レコードロックを解除)                             //
            ////////////////////////////////////////////////////////////////
            $r = $objDBCA->transactionCommit();
            if (!$r){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_011,array($table_name));
                return $FREE_LOG ;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_006);
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        
            ////////////////////////////////
            // トランザクション終了       //
            ////////////////////////////////
            $objDBCA->transactionExit();

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_005);
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
        return true;
    }
    function transactionRollback($table_name){
        global $objMTS;
        global $objDBCA;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;

        if( $objDBCA->getTransactionMode() ){
            ////////////////////////////////////////////////////////////////
            // ロールバック(レコードロックを解除)                         //
            ////////////////////////////////////////////////////////////////
            $r = $objDBCA->transactionRollBack();
            if (!$r){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_ERR_029,array($table_name));
                return $FREE_LOG ;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_007);
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
            
            ////////////////////////////////
            // トランザクション終了       //
            ////////////////////////////////
            $objDBCA->transactionExit();

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage(DF_MSG_STD_005);
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
        return true;
    }
    function ChkHistoryDirectory($in_csvdata, $in_storage_path, &$in_outdata){
        global $root_dir_path;

        $in_csvdata = trim($in_csvdata);
        if(strlen($in_csvdata) == 0){
            $in_outdata       = "";
            return true;
        }
        $pattern = "/^\/__data_relay_storage__\//";
        $ret = preg_match($pattern,$in_csvdata); 
        if($ret != 0){

            $in_outdata = preg_replace($pattern,$in_storage_path . "/",$in_csvdata);
            return true;
        }
        // 絶対パス判定
        $ret = preg_match("/^\//",$in_csvdata); 
        if($ret != 0){
            $in_outdata       = $in_csvdata;
        }
        else{
            // 相対パスの場合、/???/ita-rootを付ける
            $in_outdata      = $root_dir_path . "/" . $in_csvdata;
        }
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
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
 
        $FREE_LOG = "FILE:$p1 LINE:$p2\n" . print_r($p3,true);
        echo $FREE_LOG;
    }
?>
