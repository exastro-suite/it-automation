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
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php     = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php   = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php     = '/libs/commonlibs/common_db_connect.php';
    $ola_lib_agent_php  = '/libs/commonlibs/common_ola_classes.php';
    $web_php_function_php = '/libs/webcommonlibs/web_php_functions.php';
    $db_access_user_id  = -5; //定期実行管理プロシージャのユーザID
    $strFxName          = "proc({$log_file_prefix})";

    //ステータス定義
    const STATUS_IN_PREPARATION = 1; //ステータス：準備中
    const STATUS_IN_OPERATION = 2; //ステータス：稼働中
    const STATUS_COMPLETED = 3; //ステータス：完了
    const STATUS_MISMATCH_ERROR = 4; //ステータス：不整合エラー
    const STATUS_LINKING_ERROR = 5; //ステータス：紐付けエラー
    const STATUS_UNEXPECTED_ERROR = 6; //ステータス：想定外エラー
    const STATUS_CONDUCTOR_DISCARD = 7; //ステータス：symphony廃止
    const STATUS_OPERATION_DISCARD = 8; //ステータス：operation廃止

    //C_REGULARLY2_LIST
    $aryConfigForRegListIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "REGULARLY_ID"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "STATUS_ID"=>"",
        "NEXT_EXECUTION_DATE"=>"",
        "START_DATE"=>"",
        "END_DATE"=>"",
        "EXECUTION_STOP_START_DATE"=>"",
        "EXECUTION_STOP_END_DATE"=>"",
        "EXECUTION_INTERVAL"=>"",
        "REGULARLY_PERIOD_ID"=>"",
        "PATTERN_TIME"=>"",
        "PATTERN_DAY"=>"",
        "PATTERN_DAY_OF_WEEK"=>"",
        "PATTERN_WEEK_NUMBER"=>"",
        "EXECUTION_USER_ID"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //C_CONDUCTOR_INSTANCE_MNG
    $aryConfigForSymInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "I_CONDUCTOR_CLASS_NO"=>"",
        "I_CONDUCTOR_NAME"=>"",
        "I_DESCRIPTION"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "STATUS_ID"=>"",
        "EXECUTION_USER"=>"",
        "ABORT_EXECUTE_FLAG"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //C_CONDUCTOR_INSTANCE_MNG
    $aryConfigForMovInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_INSTANCE_NO"=>"",
        "I_MOVEMENT_CLASS_NO"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "EXECUTION_NO"=>"",
        "STATUS_ID"=>"",
        "ABORT_RECEPTED_FLAG"=>"",
        "EXE_SKIP_FLAG"=>"",
        "OVRD_OPERATION_NO_UAPK"=>"",
        "OVRD_I_OPERATION_NAME"=>"",
        "OVRD_I_OPERATION_NO_IDBH"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "RELEASED_FLAG"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $intWarningFlag               = 0;        // 警告フラグ(1：警告発生)
    $intErrorFlag                 = 0;        // 異常フラグ(1：異常発生)
    $boolInTransactionFlag = false;

    ////////////////////////////////
    // グローバル変数宣言         //
    ////////////////////////////////
    global $g;

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    require ($root_dir_path . $php_req_gate_php );

    // 開始メッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160001"); //[処理]プロシージャ(開始)
        require ($root_dir_path . $log_output_php );
    }
    
    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );
    
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160003"); //[処理]DBコネクト完了
        require ($root_dir_path . $log_output_php );
    }

    require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_get_sysconfig.php");

    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-36",$tmpAryRetBody[3]));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10410101);
        exit();
    }
    $arySYSCON = $tmpAryRetBody[0]['Items'];
    unset($tmpAryRetBody);

    $sc_interval_time=3;
    if(array_key_exists('INTERVAL_TIME', $arySYSCON)){
        $sc_interval_time = intval($arySYSCON['INTERVAL_TIME']);
    }
    if($sc_interval_time < 1 || $sc_interval_time > 525600){
        $sc_interval_time=3;
    }
    
    $strIntervalTime    = $sc_interval_time . " MINUTE"; //Symphony作業一覧に実行するどれくらい前に登録をするか

    ////////////////////////////////////////
    // （ここから）初回の次回実行日付のセット処理 //
    ////////////////////////////////////////
    try{
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160007"); //[処理]最初の次回実行日付取得(開始)
            require ($root_dir_path . $log_output_php );
        }

        //グローバル変数設定
        $g['objMTS'] = $objMTS;
        $g['objDBCA'] = $objDBCA;

        // 更新用のテーブル定義
        $aryConfigForIUD = $aryConfigForRegListIUD;

        // BIND用のベースソース
        $aryBaseSourceForBind = $aryConfigForRegListIUD;

        //NEXT_EXECUTION_DATEおよびSATUS_IDがSTATUS_IN_PREPARATION(準備中)のレコードを取得
        $statusInPreparation = STATUS_IN_PREPARATION;
        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND NEXT_EXECUTION_DATE IS NULL AND STATUS_ID = {$statusInPreparation}");
        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                               ,"SELECT FOR UPDATE"
                                               ,"REGULARLY_ID"
                                               ,"C_REGULARLY2_LIST"
                                               ,"C_REGULARLY2_LIST_JNL"
                                               ,$aryConfigForIUD
                                               ,$aryBaseSourceForBind
                                               ,$aryTempForSql);

        if( $aryRetBody[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $strSqlUtnBody = $aryRetBody[1];
        $aryUtnSqlBind = $aryRetBody[2];
        unset($aryRetBody);

        $aryUtnSqlBind = array();
        $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);

        if( $aryRetBody[0] !== true ){
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $objQueryUtn =& $aryRetBody[3];

        //取得したレコードがある場合に配列にセットする
        $aryCreateFirstNextDate = array();
        if($objQueryUtn->effectedRowCount() != 0){
            //----発見行だけループ
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryCreateFirstNextDate[] = $row;
            }
            //発見行だけループ----
        }

        //次回実行日付が無い対象を1個ずつループする
        foreach($aryCreateFirstNextDate as $rowOfReguralyList){
            //初回の次回実行日付およびステータスを取得
            $aryNextExecutionDateAndStatus = getNextExecutionDate($rowOfReguralyList);
            $regStatusId = $aryNextExecutionDateAndStatus['statusId'];
            $nextExecutionDate = $aryNextExecutionDateAndStatus['nextExecutionDate'];

            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForRegListIUD;

            // BIND用のベースソース
            $aryUtnSqlBind = $rowOfReguralyList;
            $aryUtnSqlBind['STATUS_ID'] = $regStatusId;
            $aryUtnSqlBind['NEXT_EXECUTION_DATE'] = $nextExecutionDate;
            $aryUtnSqlBind['LAST_UPDATE_USER'] = $db_access_user_id;

            $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                   ,"UPDATE"
                                                   ,"REGULARLY_ID"
                                                   ,"C_REGULARLY2_LIST"
                                                   ,"C_REGULARLY2_LIST_JNL"
                                                   ,$aryConfigForIUD
                                                   ,$aryUtnSqlBind);

            if( $aryRetBody[0] === false ){
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $strSqlUtnBody = $aryRetBody[1];
            $aryUtnSqlBind = $aryRetBody[2];
            $strSqlJnlBody = $aryRetBody[3];
            $aryJnlSqlBind = $aryRetBody[4];
            unset($aryRetBody);

            // ----REGULARLY_LIST-シーケンスを掴む（更新しか行わないためJSQのみ）
            $aryRetBody = getSequenceLockInTrz('C_REGULARLY2_LIST_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // -REGULARLY_LIST-シーケンスを掴む----

            // ----履歴シーケンス払い出し
            $aryRetBody = getSequenceValueFromTable('C_REGULARLY2_LIST_JSQ', 'A_SEQUENCE', FALSE );
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else{
                $varJSeq = $aryRetBody[0];
                $aryJnlSqlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            unset($aryRetBody);
            // 履歴シーケンス払い出し----

            //更新を実行
            $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
            $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
            if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody01);
            unset($aryRetBody02);

        }

        unset($aryCreateFirstNextDate);
        unset($objQueryUtn);
        unset($aryRetBody);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160008"); //[処理]最初の次回実行日付取得(終了)
            require ($root_dir_path . $log_output_php );
        }

    }
    catch(Exception $e){
        if( $log_level    === 'DEBUG' ||
            $intErrorFlag   != 0        ||
            $intWarningFlag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
        
        // DBアクセス事後処理
        if (isset($aryCreateFirstNextDate)) unset($aryCreateFirstNextDate);
        if (isset($objQueryUtn)) unset($objQueryUtn);
        if (isset($aryRetBody)) unset($aryRetBody);
        if (isset($aryRetBody01)) unset($aryRetBody01);
        if (isset($aryRetBody02)) unset($aryRetBody02);
        if (isset($strSqlUtnBody)) unset($strSqlUtnBody);
        if (isset($aryUtnSqlBind)) unset($aryUtnSqlBind);
        if (isset($strSqlJnlBody)) unset($strSqlJnlBody);
        if (isset($aryJnlSqlBind)) unset($aryJnlSqlBind);
    }
    ////////////////////////////////////////
    // （ここまで）初回の次回実行日付のセット処理 //
    ////////////////////////////////////////



    //////////////////////////////////////////////////////////
    // (ここから) Symphony作業一覧への登録および次回実行日付更新処理//
    /////////////////////////////////////////////////////////
    try{
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160009"); //[処理]symphony作業一覧への登録および次回実行日付の更新(開始)
            require ($root_dir_path . $log_output_php );
        }

        //シンフォニー/オペレーション/ムーブメント情報取得用関数を利用可能に
        require ($root_dir_path . $ola_lib_agent_php);
        $aryVariant = array('vars'=>array('fx'=>array('registerExecuteNo'=>array('update_user_id'=>$db_access_user_id)))
                           ,'root_dir_path'=>$root_dir_path);
        $objOLA = new OrchestratorLinkAgent($objMTS, $objDBCA,$aryVariant);

        //コンダクター登録時にアクセス権チェック処理を実行可能に
        require ($root_dir_path . $web_php_function_php);

        // 更新用のテーブル定義
        $aryConfigForIUD = $aryConfigForRegListIUD;

        // BIND用のベースソース
        $aryBaseSourceForBind = $aryConfigForRegListIUD;

        //STATUS_IDがSTATUS_IN_OPERATION(稼働中)かSTATUS_LINKING_ERROR(紐付けエラー)および、NEXT_EXECUTION_DATEがNOWの$strIntervalTime(分)後に訪れるレコードを検索
        $statusInOperation = STATUS_IN_OPERATION;
        $statusLinkingError = STATUS_LINKING_ERROR;
        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND (STATUS_ID = {$statusInOperation} OR STATUS_ID = {$statusLinkingError}) AND (NEXT_EXECUTION_DATE < NOW() + INTERVAL {$strIntervalTime})");
        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                               ,"SELECT FOR UPDATE"
                                               ,"REGULARLY_ID"
                                               ,"C_REGULARLY2_LIST"
                                               ,"C_REGULARLY2_LIST_JNL"
                                               ,$aryConfigForIUD
                                               ,$aryBaseSourceForBind
                                               ,$aryTempForSql);

        if( $aryRetBody[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00001100";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $strSqlUtnBody = $aryRetBody[1];
        $aryUtnSqlBind = $aryRetBody[2];
        unset($aryRetBody);

        $aryUtnSqlBind = array();
        $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);

        if( $aryRetBody[0] !== true ){
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $objQueryUtn =& $aryRetBody[3];

        //取得したレコードがある場合に配列にセットする
        $aryExecutionSoonNextDate = array();
        if($objQueryUtn->effectedRowCount() != 0){
            //----発見行だけループ
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryExecutionSoonNextDate[] = $row;
            }
            //発見行だけループ----
        }

        //次回実行日付が直前の対象を1個ずつループする
        foreach($aryExecutionSoonNextDate as $rowOfReguralyList){

            ///////////////////////
            //変数初期化(ループ冒頭)//
            //////////////////////
            //定期実行ID
            $regularlyId =  $rowOfReguralyList['REGULARLY_ID'];
            //ステータスID
            $regCurrentStatusId = $rowOfReguralyList['STATUS_ID'];
            $regStatusId = STATUS_IN_OPERATION; //ステータス：稼働中
            // トランザクションフラグ(初期値はfalse)
            $boolInTransactionFlag = false;
            //情報取得失敗フラグ(初期値はfalse)
            $getFailedSymphonyInfo = false;
            $getFailedOperationInfo = false;
            $registerFailedSymphonyInstance = false;
            //次回実行日付が過ぎた場合のフラグ(初期値はfalse)
            $passedNextExecutionDate = false;
            //インスタンス実行前エラーフラグ
            $beforeExecuteCheckFlag = false;
            //実行ユーザの廃止フラグ
            $userAbolishedFlag = false;
            //末端のコンダクタまでの各ノードのアクセス権限フラグ
            $noAccessAuthFlag = false;
            //実行ユーザID
            $executionUserId = $rowOfReguralyList['EXECUTION_USER_ID'];
            $g['login_id'] = $executionUserId;

            //実行ユーザIDから実行ユーザ名を取得
            $strSqlUtnBody = "SELECT * FROM D_ACCOUNT_LIST WHERE USER_ID = :USER_ID";
            $aryUtnSqlBind = array('USER_ID'=>$executionUserId);
            $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
            if( $aryRetBody[0] !== true ){
                // 例外処理へ
                $strErrStepIdInFx="00001210";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $objQueryUtn =& $aryRetBody[3];

            //取得したレコードがある場合に配列にセットする
            $executionUserName = "";
            if($objQueryUtn->effectedRowCount() != 0){
                //----発見行だけループ
                while ($row = $objQueryUtn->resultFetch()){
                    $executionUserName = $row['USERNAME_JP'];
                    //実行ユーザの廃止状態をチェック
                    if($row['DISUSE_FLAG'] == 1){
                        $userAbolishedFlag = true;
                        $beforeExecuteCheckFlag = true;
                    }
                }
                //発見行だけループ----
            }

            if($executionUserName == ""){
                // 例外処理へ
                $strErrStepIdInFx="00001220";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //次回実行日付がNOWを過ぎていた場合、ステータスと次回実行日付をnullにして更新を実施する。
            $nextExecutionDate = date('Y/m/d H:i', strtotime($rowOfReguralyList['NEXT_EXECUTION_DATE']));
            $now = date("Y/m/d H:i");
            if(strtotime($nextExecutionDate) >= strtotime($now)){
                //symphonyとoperationを取り出す
                $tmpsymphonyClassNo = $rowOfReguralyList['CONDUCTOR_CLASS_NO'];
                $opertionNoIdbh = $rowOfReguralyList['OPERATION_NO_IDBH'];

                //----Operation、Conductorの共通アクセス権の取得 #519
                $arrOpeConAccessAuth = $objOLA->getInfoAccessAuthWorkFlowOpe($tmpsymphonyClassNo,$opertionNoIdbh ,"C" );
                $strOpeConAccessAuth = $arrOpeConAccessAuth[4];

                if( $arrOpeConAccessAuth[3] != "" ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-ERR-160003", array($regularlyId)); //[処理]symphonyINSTANCEの登録に失敗しました(定期実行ID:{})。
                    require ($root_dir_path . $log_output_php );
                    $registerFailedSymphonyInstance = true;
                }else{

                    if($beforeExecuteCheckFlag == false){
                        // ----実行するConductorクラスIDに紐づくすべてのノードのアクセス権チェック
                        $arrayRetBody = $objOLA->checkConductorNodeAccessAuth($tmpsymphonyClassNo, $executionUserId);
                        if($arrayRetBody[0] == false){
                            //アクセス権限がないフラグをたてる
                            $noAccessAuthFlag = true;
                            $beforeExecuteCheckFlag = true;

                            //ログを出力
                            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-ERR-170037", array($regularlyId)); //コンダクタ内にアクセス権限がないノードが含まれています(定期実行ID:{})。
                            require ($root_dir_path . $log_output_php );
                        }
                        // ----実行するConductorクラスIDに紐づくすべてのノードのアクセス権チェック
                    }

                    if($beforeExecuteCheckFlag == false){
                        //--- Conductorクラス状態保存 
                        $arrayResult = $objOLA->convertConductorClassJson($tmpsymphonyClassNo,1);

                        // JSON形式の変換、不要項目の削除
                        $tmpReceptData = $arrayResult[4];
                        $arrayReceptData=$tmpReceptData['conductor'];
                        $strSortedData=$tmpReceptData;
                        unset($strSortedData['conductor']);
                        foreach ($strSortedData as $key => $value) {
                            if( preg_match('/line-/',$key) ){
                                unset($strSortedData[$key]);
                            }
                        }
                        unset($strSortedData['conductor']);
                        unset($strSortedData['config']); 

                        // アクセス権の上書き#519
                        $arrayReceptData['ACCESS_AUTH']=$strOpeConAccessAuth; 

                        $arrayResult = $objOLA->conductorClassRegister(null, $arrayReceptData, $strSortedData, null);

                        if( $arrayResult[0] == "000" ){
                            $symphonyClassNo = $arrayResult[2];
                        }else{
                            $symphonyClassNo="";
                        }
                        // Conductorクラス状態保存 ---


                        //シンフォニーインスタンスを新規登録処理
                        $aryOptionOrder = null;
                        $aryOptionOrderOverride = array();
                        $retArray = $objOLA->registerConductorInstance($symphonyClassNo, $opertionNoIdbh, $nextExecutionDate, "", $aryOptionOrderOverride, $executionUserId, $executionUserName);

                        if($retArray[0] !== true){
                            //エラー情報をセット
                            $intErrorType = $retArray[1];
                            $aryErrMsgBody = $retArray[2];
                            $strSysErrMsgBody = $retArray[4];
                            $aryFreeErrMsgBody = $retArray[7];

                            //エラー判定チェック
                            if($intErrorType === 101){
                                //symphonyが存在しない（廃止扱い）
                                $getFailedSymphonyInfo = true;
                                $regStatusId = STATUS_CONDUCTOR_DISCARD; //ステータス：symphony廃止
                            }elseif($intErrorType === 102){
                                //operationが存在しない（廃止扱い）
                                $getFailedOperationInfo = true;
                                $regStatusId = STATUS_OPERATION_DISCARD; //ステータス：operation廃止
                            }else{
                                $registerFailedSymphonyInstance = true;
                            }

                            //ログを出力
                            if($regCurrentStatusId != STATUS_LINKING_ERROR){
                                foreach($aryErrMsgBody as $msg){
                                    $FREE_LOG = $msg;
                                    require ($root_dir_path . $log_output_php );
                                }
                                foreach($aryFreeErrMsgBody as $msg){
                                    $FREE_LOG = $msg;
                                    require ($root_dir_path . $log_output_php );
                                }
                                if( 0 < strlen($strSysErrMsgBody)){
                                    $FREE_LOG = $strSysErrMsgBody;
                                    require ($root_dir_path . $log_output_php );  
                                }

                                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-ERR-160003", array($regularlyId)); //[処理]symphonyINSTANCEの登録に失敗しました(定期実行ID:{})。
                                require ($root_dir_path . $log_output_php );
                            }
                        }
                    }
                }
            }else{
                $passedNextExecutionDate = true;
            }

            ////////////////////////////////
            //次回実行日付を取得し、更新を実行//
            ////////////////////////////////
            //次回実行日付がNOWを過ぎていた場合
            if($passedNextExecutionDate == true){
                //ステータスを7(準備中)に、次回実行日付をnullにする
                $regStatusId = STATUS_IN_PREPARATION; //ステータス：準備中
                $nextExecutionDate = null;
            }
            //symphony情報・operation情報の取得に失敗していたかどうか（廃止されているかどうか）
            elseif($getFailedSymphonyInfo == true || $getFailedOperationInfo == true){
                //ステータスは失敗時にセットしたIDに。次回実行日付はnullにする
                $nextExecutionDate = null;
            }else{
                //次回実行日付およびステータスを取得
                $aryNextExecutionDateAndStatus = getNextExecutionDate($rowOfReguralyList);
                $regStatusId = $aryNextExecutionDateAndStatus['statusId'];
                $nextExecutionDate = $aryNextExecutionDateAndStatus['nextExecutionDate'];

                //実行ユーザが廃止の場合、ステータスを「紐付けエラー」にする
                if($userAbolishedFlag == true){
                    $regStatusId = STATUS_LINKING_ERROR; //ステータス：紐付けエラー
                }

                //末端コンダクタの各ノードにアクセス権限がないものがあった場合、ステータスを「紐付けエラー」にする
                if($noAccessAuthFlag == true){
                    $regStatusId = STATUS_LINKING_ERROR; //ステータス：紐付けエラー
                }

                //Symphony作業一覧への登録が失敗していた場合、ステータスを「紐付けエラー」にする
                if($registerFailedSymphonyInstance == true){
                    $regStatusId = STATUS_LINKING_ERROR; //ステータス：紐付けエラー
                }
            }

            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForRegListIUD;

            // BIND用のベースソース
            $aryUtnSqlBind = $rowOfReguralyList;
            $aryUtnSqlBind['STATUS_ID'] = $regStatusId;
            $aryUtnSqlBind['NEXT_EXECUTION_DATE'] = $nextExecutionDate;
            $aryUtnSqlBind['LAST_UPDATE_USER'] = $db_access_user_id;

            $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                   ,"UPDATE"
                                                   ,"REGULARLY_ID"
                                                   ,"C_REGULARLY2_LIST"
                                                   ,"C_REGULARLY2_LIST_JNL"
                                                   ,$aryConfigForIUD
                                                   ,$aryUtnSqlBind);

            if( $aryRetBody[0] === false ){
                // 例外処理へ
                $strErrStepIdInFx="00001500";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $strSqlUtnBody = $aryRetBody[1];
            $aryUtnSqlBind = $aryRetBody[2];
            $strSqlJnlBody = $aryRetBody[3];
            $aryJnlSqlBind = $aryRetBody[4];
            unset($aryRetBody);

            // ----REGULARLY_LIST-シーケンスを掴む（更新しか行わないためJSQのみ）
            $aryRetBody = getSequenceLockInTrz('C_REGULARLY2_LIST_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00001600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // -REGULARLY_LIST-シーケンスを掴む----

            // ----履歴シーケンス払い出し
            $aryRetBody = getSequenceValueFromTable('C_REGULARLY2_LIST_JSQ', 'A_SEQUENCE', FALSE );
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00001700";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else{
                $varJSeq = $aryRetBody[0];
                $aryJnlSqlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            unset($aryRetBody);
            // 履歴シーケンス払い出し----

            //更新を実行
            $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
            $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
            if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                // 異常フラグON
                $intErrorFlag = 1;

                // 例外処理へ
                $strErrStepIdInFx="00001800";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody01);
            unset($aryRetBody02);

        }

        unset($aryExecutionSoonNextDate);
        unset($objQueryUtn);
        unset($aryRetBody);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160010"); //[処理]symphony作業一覧への登録および次回実行日付の更新(終了)
            require ($root_dir_path . $log_output_php );
        }

    }
    catch(Exception $e){
        if( $log_level    === 'DEBUG' ||
            $intErrorFlag   != 0        ||
            $intWarningFlag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
        
        // DBアクセス事後処理
        if (isset($objQuery)) unset($objQuery);
        if (isset($objQueryUtn)) unset($objQueryUtn);
        if (isset($aryExecutionSoonNextDate)) unset($aryExecutionSoonNextDate);
        if (isset($aryRetBodySymphonyInfo)) unset($aryRetBodySymphonyInfo);
        if (isset($aryRetBodyOperationInfo)) unset($aryRetBodyOperationInfo);
        if (isset($aryRetBodyMovementInfo)) unset($aryRetBodyMovementInfo);
        if (isset($aryRetBodyPatternInfo)) unset($aryRetBodyPatternInfo);
        if (isset($strSqlUtnBody)) unset($strSqlUtnBody);
        if (isset($aryUtnSqlBind)) unset($aryUtnSqlBind);
        if (isset($strSqlJnlBody)) unset($strSqlJnlBody);
        if (isset($aryJnlSqlBind)) unset($aryJnlSqlBind);

    }
    /////////////////////////////////////////////////////////
    // (ここまで)Symphony作業一覧への登録および次回実行日付更新処理//
    ////////////////////////////////////////////////////////



    /////////////////////////////////////////////////////////////////
    // (ここから)symphonyおよびoperationの廃止対象が復活しているかのチェック処理//
    ////////////////////////////////////////////////////////////////
    try{
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160011"); //[処理]symphony,operationの廃止対象チェック(開始)
            require ($root_dir_path . $log_output_php );
        }

        // 更新用のテーブル定義
        $aryConfigForIUD = $aryConfigForRegListIUD;

        // BIND用のベースソース
        $aryBaseSourceForBind = $aryConfigForRegListIUD;

        //STATUS_IDがSTATUS_CONDUCTOR_DISCARD(symphony廃止)かSTATUS_OPERATION_DISCARD(operation廃止)の対象を取得
        $statusSymphonyDiscard = STATUS_CONDUCTOR_DISCARD;
        $statusOperationDiscard = STATUS_OPERATION_DISCARD;
        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND (STATUS_ID = {$statusSymphonyDiscard} OR STATUS_ID = {$statusOperationDiscard})");
        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                               ,"SELECT"
                                               ,"REGULARLY_ID"
                                               ,"C_REGULARLY2_LIST"
                                               ,"C_REGULARLY2_LIST_JNL"
                                               ,$aryConfigForIUD
                                               ,$aryBaseSourceForBind
                                               ,$aryTempForSql);

        if( $aryRetBody[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00002100";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $strSqlUtnBody = $aryRetBody[1];
        $aryUtnSqlBind = $aryRetBody[2];
        unset($aryRetBody);

        $aryUtnSqlBind = array();
        $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
        if( $aryRetBody[0] !== true ){
            // 例外処理へ
            $strErrStepIdInFx="00002200";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $objQueryUtn =& $aryRetBody[3];

        //取得したレコードがある場合に配列にセットする
        $aryAbolishedCheck = array();
        if($objQueryUtn->effectedRowCount() != 0){
            //----発見行だけループ
            while ($row = $objQueryUtn->resultFetch()){
                $aryAbolishedCheck[] = $row;
            }
            //発見行だけループ----
        }

        //symphony・operation廃止対象対象を1個ずつループする
        foreach($aryAbolishedCheck as $rowOfReguralyList){
            //ステータスを取得
            $regStatusId = $rowOfReguralyList['STATUS_ID'];

            //ステータス：symphony廃止の場合
            if($regStatusId == STATUS_CONDUCTOR_DISCARD){
                $symphonyClassNo = $rowOfReguralyList['CONDUCTOR_CLASS_NO'];
                //symphonyIDをもとに、C_CONDUCTOR_CLASS_MNGテーブルより情報を取得
                $aryRetBodySymphonyInfo = $objOLA->getInfoOfOneSymphony($symphonyClassNo);
                if( $aryRetBodySymphonyInfo[0] !== true ){
                    //処理終了（廃止のままなので以降の処理をしない）
                    break;
                }
            }

            //ステータス：operation廃止の場合
            if($regStatusId == STATUS_OPERATION_DISCARD){
                $opertionNoIdbh = $rowOfReguralyList['OPERATION_NO_IDBH'];
                $aryRetBodyOperationInfo = $objOLA->getInfoOfOneOperation($opertionNoIdbh);
                if( $aryRetBodyOperationInfo[0] !== true ){
                    //処理終了（廃止のままなので以降の処理をしない）
                    break;
                }
            }

            ////////////////////////////////////////////////////////////////////////////////////
            //symphony・operationが復活している場合、ステータスを7(準備中に、)次回実行日付をnullにして更新する//
            ///////////////////////////////////////////////////////////////////////////////////

            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForRegListIUD;

            // BIND用のベースソース
            $aryUtnSqlBind = $rowOfReguralyList;
            $aryUtnSqlBind['STATUS_ID'] = STATUS_IN_PREPARATION; //ステータス：準備中
            $aryUtnSqlBind['NEXT_EXECUTION_DATE'] = null;
            $aryUtnSqlBind['LAST_UPDATE_USER'] = $db_access_user_id;

            $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                   ,"UPDATE"
                                                   ,"REGULARLY_ID"
                                                   ,"C_REGULARLY2_LIST"
                                                   ,"C_REGULARLY2_LIST_JNL"
                                                   ,$aryConfigForIUD
                                                   ,$aryUtnSqlBind);

            if( $aryRetBody[0] === false ){
                // 例外処理へ
                $strErrStepIdInFx="00002300";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $strSqlUtnBody = $aryRetBody[1];
            $aryUtnSqlBind = $aryRetBody[2];
            $strSqlJnlBody = $aryRetBody[3];
            $aryJnlSqlBind = $aryRetBody[4];
            unset($aryRetBody);

            // ----REGULARLY_LIST-シーケンスを掴む（更新しか行わないためJSQのみ）
            $aryRetBody = getSequenceLockInTrz('C_REGULARLY2_LIST_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00002400";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // -REGULARLY_LIST-シーケンスを掴む----

            // ----履歴シーケンス払い出し
            $aryRetBody = getSequenceValueFromTable('C_REGULARLY2_LIST_JSQ', 'A_SEQUENCE', FALSE );
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00002500";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else{
                $varJSeq = $aryRetBody[0];
                $aryJnlSqlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            unset($aryRetBody);
            // 履歴シーケンス払い出し----

            //更新を実行
            $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
            $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
            if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                // 例外処理へ
                $strErrStepIdInFx="00002600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody01);
            unset($aryRetBody02);


        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160012"); //[処理]symphony,operationの廃止対象チェック(終了)
            require ($root_dir_path . $log_output_php );
        }

    }catch(Exception $e){
        if( $log_level    === 'DEBUG' ||
            $intErrorFlag   != 0        ||
            $intWarningFlag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
        
        // DBアクセス事後処理
        if (isset($aryAbolishedCheck)) unset($aryAbolishedCheck);
        if (isset($objQueryUtn)) unset($objQueryUtn);
        if (isset($aryRetBody)) unset($aryRetBody);
        if (isset($aryRetBody01)) unset($aryRetBody01);
        if (isset($aryRetBody02)) unset($aryRetBody02);
        if (isset($strSqlUtnBody)) unset($strSqlUtnBody);
        if (isset($aryUtnSqlBind)) unset($aryUtnSqlBind);
        if (isset($strSqlJnlBody)) unset($strSqlJnlBody);
        if (isset($aryJnlSqlBind)) unset($aryJnlSqlBind);
    }

    /////////////////////////////////////////////////////////////////
    // (ここまで)symphonyおよびoperationの廃止対象が復活しているかのチェック処理//
    ////////////////////////////////////////////////////////////////



    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $intErrorFlag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-ERR-160001"); //[処理]プロシージャ終了(異常)
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(1);
    }
    elseif( $intWarningFlag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-ERR-160002"); //[処理]プロシージャ終了(警告)
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-160002"); //[処理]プロシージャ終了(正常)
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(0);
    }



//////////////////////////////
//以下、次回実行日付計算用関数//
/////////////////////////////
/*
スケジュールの設定値から次回実行日付およびステータスを返す
*/
function getNextExecutionDate($rowOfReguralyList){
    //スケジュールの各値をセット
    $nowDate = date("Y/m/d H:i");
    $regularlyId = $rowOfReguralyList['REGULARLY_ID'];
    $startDate = ($rowOfReguralyList['START_DATE'] != null) ? date("Y/m/d H:i", strtotime($rowOfReguralyList['START_DATE'])) : "";
    $endDate = ($rowOfReguralyList['END_DATE'] != null) ? date("Y/m/d H:i", strtotime($rowOfReguralyList['END_DATE'])) : "";
    $exeStopStartDate = ($rowOfReguralyList['EXECUTION_STOP_START_DATE'] != null) ? date("Y/m/d H:i", strtotime($rowOfReguralyList['EXECUTION_STOP_START_DATE'])) : "";
    $exeStopEndDate = ($rowOfReguralyList['EXECUTION_STOP_END_DATE'] != null) ? date("Y/m/d H:i", strtotime($rowOfReguralyList['EXECUTION_STOP_END_DATE'])) : "";
    $regularlyPeriodID = $rowOfReguralyList['REGULARLY_PERIOD_ID'];
    $exeInterval = $rowOfReguralyList['EXECUTION_INTERVAL'];
    $patternTime = $rowOfReguralyList['PATTERN_TIME'];
    $patternDay = $rowOfReguralyList['PATTERN_DAY'];
    $patternDayOfWeek = $rowOfReguralyList['PATTERN_DAY_OF_WEEK'];
    $patternWeekNumber = $rowOfReguralyList['PATTERN_WEEK_NUMBER'];

    //バリデーションチェック
    $failedValiate = false;
    //作業停止期間のチェック(片方だけ存在していないか)
    if(!(($exeStopStartDate && $exeStopEndDate) || (!$exeStopStartDate && !$exeStopEndDate))){
        $failedValiate = true;
        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
    }
    //間隔のチェック
    if($exeInterval){
        $strRegexpFormat = '/^[1-9]$|^[1-9][0-9]$/';
        if(preg_match($strRegexpFormat, $exeInterval) !== 1){
            $failedValiate = true;
            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
        }
    }
    //時間のチェック
    if($patternTime){
        $strRegexpFormat = "/^$|^(0[0-9]{1}|1{1}[0-9]{1}|2{1}[0-3]{1}):(0[0-9]{1}|[1-5]{1}[0-9]{1})$/";
        if(preg_match($strRegexpFormat, $patternTime) !== 1){
            $failedValiate = true;
            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
        }
    }
    //日付のチェック
    if($patternDay){
        $strRegexpFormat = '/^[1-9]$|^[1-2][0-9]$|^3[0-1]$/';
        if(preg_match($strRegexpFormat, $patternDay) !== 1){
            $failedValiate = true;
            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
        }
    }
    //曜日のチェック
    if($patternDayOfWeek){
        $strRegexpFormat = '/^[1-9]\d*$/';
        if(preg_match($strRegexpFormat, $patternDayOfWeek) !== 1 || $patternDayOfWeek < 1 || 7 < $patternDayOfWeek){
            $failedValiate = true;
            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
        }
    }
    //週番号のチェック
    if($patternWeekNumber){
        $strRegexpFormat = '/^[1-9]\d*$/';
        if(preg_match($strRegexpFormat, $patternWeekNumber) !== 1 || $patternWeekNumber < 1 || 5 < $patternWeekNumber){
            $failedValiate = true;
            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
        }
    }

    //バリデーションに問題が無ければ次回実行日付作成の処理
    if($failedValiate == false){
        //次回実行日付の有無を判定
        if($rowOfReguralyList['NEXT_EXECUTION_DATE']){
            $nextExecutionDate = date("Y/m/d H:i", strtotime($rowOfReguralyList['NEXT_EXECUTION_DATE']));
        }else{
            $nextExecutionDate = null;
        }
        $newNextExecutionDate = null;

        //ステータスをセット
        $regStatusId = STATUS_IN_OPERATION; //ステータス：稼働中

        //以下周期ごとに次回実行日付を計算する処理
        switch($regularlyPeriodID){
            ////////////////
            //周期ID:1「時」//
            ////////////////
            case 1:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval);
                }else{
                    $required_column = array($exeInterval);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //時計算用
                $addHourStr = '+'.$exeInterval.' hour';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //開始日付が現在日付を過ぎているかどうかをチェック
                    if(strtotime($startDate) > strtotime($nowDate)){
                        //開始日付をそのまま次回実行日付にする
                        $newNextExecutionDate = $startDate;
                    }else{
                        //開始日付を基準に現在日付よりも未来になるまで間隔(時)を加算する
                        $loopCheckDate = $startDate;
                        $addIntervalStartDate = $startDate;
                        while(strtotime($nowDate) > strtotime($addIntervalStartDate)){
                            $addIntervalStartDate = date('Y/m/d H:i', strtotime($addIntervalStartDate.$addHourStr));
                            //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                            if($loopCheckDate >= $addIntervalStartDate){
                                $newNextExecutionDate = null;
                                $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                break 2; //switch文を抜ける
                            }
                        }
                        $newNextExecutionDate = $addIntervalStartDate;
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                    //次回実行日付を基準に間隔(時)を加算する
                    $newNextExecutionDate = date('Y/m/d H:i', strtotime($nextExecutionDate.$addHourStr));     
                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(時)を加算する
                    $loopCheckDate = $newNextExecutionDate;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                        $newNextExecutionDate = date('Y/m/d H:i', strtotime($newNextExecutionDate.$addHourStr));
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switch文を抜ける
                        }
                    }
                }

                break;

            ////////////////
            //周期ID:2「日」//
            ////////////////
            case 2:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval, $patternTime);
                }else{
                    $required_column = array($exeInterval, $patternTime);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //日付計算用
                $addDayStr = '+'.$exeInterval.' day';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //開始日付のYmd+patternTimeを生成
                    $startYmdPatternTime = date('Y/m/d', strtotime($startDate)).' '.$patternTime;
                    //現在日付のYmd+patternTimeを生成
                    $nowYmdPatternTime = date('Y/m/d', strtotime($nowDate)).' '.$patternTime;

                    //開始日付が現在日付より未来かどうか
                    if(strtotime($startDate) > strtotime($nowDate)){
                        //$startYmdPatternTimeが開始日付よりも未来の場合
                        if(strtotime($startYmdPatternTime) >= strtotime($startDate)){
                            //$startYmdPatternTimeを次回実行日付にする
                            $newNextExecutionDate = $startYmdPatternTime;
                        }else{
                            //$startYmdPatternTimeを基準に開始日付よりも未来になるまで間隔(日)を加算する
                            $loopCheckDate = $startYmdPatternTime;
                            $addIntervalStartYmdPatternTime = $startYmdPatternTime;
                            while(strtotime($startDate) > strtotime($addIntervalStartYmdPatternTime)){
                                $addIntervalStartYmdPatternTime = date('Y/m/d H:i', strtotime($addIntervalStartYmdPatternTime.$addDayStr));
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if($loopCheckDate >= $addIntervalStartYmdPatternTime){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switch文を抜ける
                                }
                            }
                            $newNextExecutionDate = $addIntervalStartYmdPatternTime;
                        }
                    }else{
                        //$startYmdPatternTimeを基準に現在日付よりも未来になるまで間隔(日)を加算する
                        $loopCheckDate = $startYmdPatternTime;
                        $addIntervalStartYmdPatternTime = $startYmdPatternTime;
                        while(strtotime($nowDate) > strtotime($addIntervalStartYmdPatternTime)){
                            $addIntervalStartYmdPatternTime = date('Y/m/d H:i', strtotime($addIntervalStartYmdPatternTime.$addDayStr));
                            //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                            if($loopCheckDate >= $addIntervalStartYmdPatternTime){
                                $newNextExecutionDate = null;
                                $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                break 2; //switch文を抜ける
                            }
                        }
                        $newNextExecutionDate = $addIntervalStartYmdPatternTime;
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                     //次回実行日付を基準に間隔(日)を加算する
                    $newNextExecutionDate = date('Y/m/d H:i', strtotime($nextExecutionDate.$addDayStr));

                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(日)を加算する
                    $loopCheckDate = $newNextExecutionDate;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                        $newNextExecutionDate = date('Y/m/d H:i', strtotime($newNextExecutionDate.$addDayStr));
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switch文を抜ける
                        }
                    }
                }

                break;

            ////////////////
            //周期ID:3「週」//
            ////////////////
            case 3:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval, $patternTime, $patternDayOfWeek);
                }else{
                    $required_column = array($exeInterval, $patternTime, $patternDayOfWeek);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //曜日計算用
                $addWeekStr = '+'.$exeInterval.' week';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //曜日をphpのdate標準に合わせるため-1する
                    $patternDayOfWeek = $patternDayOfWeek -1;
                    //開始日付のYmd+patternTimeを生成
                    $startYmdPatternTime = date('Y/m/d', strtotime($startDate)).' '.$patternTime;
                    //開始日付の曜日を取得
                    $startDateW = date('w', strtotime($startDate));
                    //開始日付の週の対象の曜日が何日かを取得
                    $startDateTargetDayOfWeek = date('Y/m/d', strtotime($startDate.'+'.($patternDayOfWeek - $startDateW).' day')).' '.$patternTime;
                    //現在日付のYmd+patternTimeを生成
                    $nowYmdPatternTime = date('Y/m/d', strtotime($nowDate)).' '.$patternTime;
                    //現在日付の曜日を取得
                    $nowDateW = date('w', strtotime($nowDate));

                     //開始日付が現在日付より未来かどうか
                    if(strtotime($startDate) > strtotime($nowDate)){
                        //$startYmdPatternTimeが開始日付より未来かつ、開始日付の曜日がpatternDayOfWeek(対象の曜日)と一致しているの場合
                        if(strtotime($startYmdPatternTime) >= strtotime($startDate) && $startDateW == $patternDayOfWeek){
                            //$startYmdPatternTimeを次回実行日付にする
                            $newNextExecutionDate = $startYmdPatternTime;

                        //$startYmdPatternTimeが開始日付より過去かつ、開始日付の曜日がpatternDayOfWeek(対象の曜日)と一致しているの場合
                        }elseif(strtotime($startDate) > strtotime($startYmdPatternTime) && $startDateW == $patternDayOfWeek){
                            //$startDateTargetDayOfWeekを基準に開始日付よりも未来になるまで間隔(週)を加算する
                            $loopCheckDate = $startDateTargetDayOfWeek;
                            $addIntervalstartDateTargetDayOfWeek = $startDateTargetDayOfWeek;
                            while(strtotime($startDate) > strtotime($addIntervalstartDateTargetDayOfWeek)){
                                $addIntervalstartDateTargetDayOfWeek = date('Y/m/d H:i', strtotime($addIntervalstartDateTargetDayOfWeek.$addWeekStr));
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if($loopCheckDate >= $addIntervalstartDateTargetDayOfWeek){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switch文を抜ける
                                }
                            }
                            $newNextExecutionDate = $addIntervalstartDateTargetDayOfWeek;
                        }else{
                            //$startDateTargetDayOfWeekを基準に開始日付よりも未来になるまで間隔(週)を加算する
                            $loopCheckDate = $startDateTargetDayOfWeek;
                            $addIntervalstartDateTargetDayOfWeek = $startDateTargetDayOfWeek;
                            while(strtotime($startDate) > strtotime($addIntervalstartDateTargetDayOfWeek)){
                                $addIntervalstartDateTargetDayOfWeek = date('Y/m/d H:i', strtotime($addIntervalstartDateTargetDayOfWeek.$addWeekStr));
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if(strtotime($loopCheckDate) >= strtotime($addIntervalstartDateTargetDayOfWeek)){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switch文を抜ける
                                }
                            }
                            $newNextExecutionDate = $addIntervalstartDateTargetDayOfWeek;
                        }
                    }else{
                        //$startDateTargetDayOfWeekを基準に現在日付よりも未来になるまで間隔(週)を加算する
                        $loopCheckDate = $startDateTargetDayOfWeek;
                        $addIntervalstartDateTargetDayOfWeek = $startDateTargetDayOfWeek;
                        while(strtotime($nowDate) > strtotime($addIntervalstartDateTargetDayOfWeek)){
                            $addIntervalstartDateTargetDayOfWeek = date('Y/m/d H:i', strtotime($addIntervalstartDateTargetDayOfWeek.$addWeekStr));
                            //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                            if(strtotime($loopCheckDate) >= strtotime($addIntervalstartDateTargetDayOfWeek)){
                                $newNextExecutionDate = null;
                                $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                break 2; //switch文を抜ける
                            }
                        }
                        $newNextExecutionDate = $addIntervalstartDateTargetDayOfWeek;
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                     //次回実行日付を基準に間隔(週)を加算する
                    $newNextExecutionDate = date('Y/m/d H:i', strtotime($nextExecutionDate.$addWeekStr));
     
                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(週)を加算する
                    $loopCheckDate = $newNextExecutionDate;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                        $newNextExecutionDate = date('Y/m/d H:i', strtotime($newNextExecutionDate.$addWeekStr));
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switch文を抜ける
                        }
                    }
                }

                break;

            ////////////////////////
            //周期ID:4「月(日付指定)」//
            ////////////////////////
            case 4:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval, $patternTime, $patternDay);
                }else{
                    $required_column = array($exeInterval, $patternTime, $patternDay);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //整合性チェック用
                $dateCheck = false;
                //月計算用
                $addMonthStr = '+'.$exeInterval.' month';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //開始日付のYmd+patternTimeを生成
                    $startYmdPatternTime = date('Y/m/d', strtotime($startDate)).' '.$patternTime;
                    //開始日付のYm+patternDay+patternTimeを生成
                    $startYmPatternDay = date('Y/m/', strtotime($startDate)).$patternDay;
                    $startYmPatternDayTime = $startYmPatternDay.' '.$patternTime;
                    //$startYmPatternDayの整合性をチェック
                    list($Y, $m, $d) = explode('/', $startYmPatternDay);
                    $dateCheck = checkdate((int)$m, (int)$d, (int)$Y);

                    //開始日付が現在日付より未来かつ、$startYmPatternDayTimeが開始日付よりも未来かつ、$startYmPatternDayTimeが存在する日付である場合
                    if(strtotime($startDate) > strtotime($nowDate) && strtotime($startYmPatternDayTime) >= strtotime($startDate) && $dateCheck == true){
                        //$startYmPatternDayTimeを次回実行日付にする
                        $newNextExecutionDate = $startYmPatternDayTime;
                    //開始日付が現在日付より過去かつ、$startYmPatternDayTimeが現在日付よりも未来かつ、$startYmPatternDayTimeが存在する日付である場合
                    }elseif(strtotime($nowDate) > strtotime($startDate) && strtotime($startYmPatternDayTime) >= strtotime($nowDate) && $dateCheck == true){
                        //$startYmPatternDayTimeを次回実行日付にする
                        $newNextExecutionDate = $startYmPatternDayTime;   
                    }else{
                        //$startYmPatternDayTimeを基準に現在日付よりも未来になるまで間隔(月)を加算する（存在しない日付の場合は加算を続行）
                        $loopCheckDate = $startYmPatternDayTime;
                        $dateCheck = false;
                        $addIntervalStartYmPatternDayTime = $startYmPatternDayTime;
                        while(strtotime($startDate) > strtotime($addIntervalStartYmPatternDayTime) || $dateCheck == false){
                            list($Y, $m, $d) = explode('/', $addIntervalStartYmPatternDayTime);
                            $addIntervalStartYm1 = $Y.'/'.$m.'/'.'1';
                            $addIntervalYm = date('Y/m', strtotime($addIntervalStartYm1.$addMonthStr));
                            $addIntervalStartYmPatternDay = $addIntervalYm.'/'.$patternDay;
                            $addIntervalStartYmPatternDayTime = $addIntervalStartYmPatternDay.' '.$patternTime;
                            //整合性をチェック
                            list($Y, $m, $d) = explode('/', $addIntervalStartYmPatternDay);
                            $dateCheck = checkdate((int)$m, (int)$d, (int)$Y);
                            //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                            if(strtotime($loopCheckDate) >= strtotime($addIntervalStartYmPatternDayTime)){
                                $newNextExecutionDate = null;
                                $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                break 2; //switchを抜ける
                            }
                        }
                        $newNextExecutionDate = $addIntervalStartYmPatternDayTime;
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                     //次回実行日付を基準に間隔(月)を加算する（存在しない日付の場合は加算を続行）
                    $loopCheckDate = $nextExecutionDate;
                    $dateCheck = false;
                    $addIntervalNextYmPatternDayTime = $nextExecutionDate;
                    while($dateCheck == false){
                        list($Y, $m, $d) = explode('/', $addIntervalNextYmPatternDayTime);
                        $addIntervalNextYm1 = $Y.'/'.$m.'/'.'1';
                        $addIntervalYm = date('Y/m', strtotime($addIntervalNextYm1.$addMonthStr));
                        $addIntervalNextYmPatternDay = $addIntervalYm.'/'.$patternDay;
                        $addIntervalNextYmPatternDayTime = $addIntervalNextYmPatternDay.' '.$patternTime;
                        //整合性をチェック
                        list($Y, $m, $d) = explode('/', $addIntervalNextYmPatternDay);
                        $dateCheck = checkdate((int)$m, (int)$d, (int)$Y);
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($addIntervalNextYmPatternDayTime)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switchを抜ける
                        }
                    }
                    $newNextExecutionDate = $addIntervalNextYmPatternDayTime;
     
                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(月)を加算する（存在しない日付の場合は加算を続行）
                    $loopCheckDate = $newNextExecutionDate;
                    $dateCheck = false;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate) || $dateCheck == false){
                        list($Y, $m, $d) = explode('/', $newNextExecutionDate);
                        $nextExecutionYm1 = $Y.'/'.$m.'/'.'1';
                        $addNextExecutionYm = date('Y/m', strtotime($nextExecutionYm1.$addMonthStr));
                        $newNextExecutionDateYmd = $addNextExecutionYm.'/'.$patternDay;
                        $newNextExecutionDate = $newNextExecutionDateYmd.' '.$patternTime;
                        //整合性をチェック
                        list($Y, $m, $d) = explode('/', $newNextExecutionDateYmd);
                        $dateCheck = checkdate((int)$m, (int)$d, (int)$Y);
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switchを抜ける
                        }
                    }
                }

                break;

            ////////////////////////
            //周期ID:5「月(曜日指定)」//
            ////////////////////////
            case 5:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval, $patternTime, $patternDayOfWeek, $patternWeekNumber);
                }else{
                    $required_column = array($exeInterval, $patternTime, $patternDayOfWeek, $patternWeekNumber);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //月計算用
                $addMonthStr = '+'.$exeInterval.' month';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //開始日付のYmd+patternTimeを生成
                    $startYmdPatternTime = date('Y/m/d', strtotime($startDate)).' '.$patternTime;
                    //開始日付の月の中で、指定したパターン（週番号）のYmdを取り出す
                    list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($startDate)));
                    //指定した週番号の日付を取得
                    $startMonthWeekNumberYmd = getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $m, $Y);
                    //patternTimeを追記
                    $sMWNYmdPatternTime = $startMonthWeekNumberYmd.' '.$patternTime;

                    //開始日付が現在日付より未来かどうか
                    if(strtotime($startDate) > strtotime($nowDate)){
                        //$sMWNYmdPatternTimeが開始日付よりも未来の場合
                        if(strtotime($sMWNYmdPatternTime) >= strtotime($startDate)){
                            //$sMWNYmdPatternTimeを次回実行日付にする
                            $newNextExecutionDate = $sMWNYmdPatternTime;
                        }else{
                            //$sMWNYmdPatternTimeを基準に、開始日付よりも未来になるまで間隔(月)を加算する
                            $loopCheckDate = $sMWNYmdPatternTime;
                            while(strtotime($startDate) > strtotime($sMWNYmdPatternTime)){
                                //間隔(月)を加算した週番号の日付を取得
                                list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($sMWNYmdPatternTime)));
                                $sMWNYm1 = $Y.'/'.$m.'/1';
                                $addSMWNYm = date('Y/m/d', strtotime($sMWNYm1.$addMonthStr));
                                list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($addSMWNYm)));
                                $startMonthWeekNumberYmd = getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $m, $Y);
                                //patternTimeを追記
                                $sMWNYmdPatternTime = $startMonthWeekNumberYmd.' '.$patternTime;
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if(strtotime($loopCheckDate) >= strtotime($sMWNYmdPatternTime)){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switchを抜ける
                                }
                            }
                            $newNextExecutionDate = $sMWNYmdPatternTime;
                        }
                    }else{
                        //$sMWNYmdPatternTimeを基準に、現在日付よりも未来になるまで間隔(月)を加算する
                        $loopCheckDate = $sMWNYmdPatternTime;
                        while(strtotime($nowDate) > strtotime($sMWNYmdPatternTime)){
                            //間隔(月)を加算した週番号の日付を取得
                            list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($sMWNYmdPatternTime)));
                            $sMWNYm1 = $Y.'/'.$m.'/1';
                            $addSMWNYm = date('Y/m/d', strtotime($sMWNYm1.$addMonthStr));
                            list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($addSMWNYm)));
                            $startMonthWeekNumberYmd = getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $m, $Y);
                            //patternTimeを追記
                            $sMWNYmdPatternTime = $startMonthWeekNumberYmd.' '.$patternTime;
                            //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                            if(strtotime($loopCheckDate) >= strtotime($sMWNYmdPatternTime)){
                                $newNextExecutionDate = null;
                                $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                break 2; //switchを抜ける
                            }
                        }
                        $newNextExecutionDate = $sMWNYmdPatternTime;
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                     //間隔(月)を加算した週番号の日付を取得し、次回実行日付を基準に間隔(月)を加算する
                    list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($nextExecutionDate)));
                    $nextExecutionDateYm1 = $Y.'/'.$m.'/1';
                    $addNextExecutionDateYm = date('Y/m/d', strtotime($nextExecutionDateYm1.$addMonthStr));
                    list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($addNextExecutionDateYm)));
                    $nextMonthWeekNumberYmd = getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $m, $Y);
                    //patternTimeを追記
                    $newNextExecutionDate = $nextMonthWeekNumberYmd.' '.$patternTime;
         
                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(月)を加算する
                    $loopCheck = false;
                    $loopCheckDate = $newNextExecutionDate;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                        //間隔(月)を加算した週番号の日付を取得
                        list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($newNextExecutionDate)));
                        $newNextExecutionDateYm1 = $Y.'/'.$m.'/1';
                        $addNextExecutionDateYm = date('Y/m/d', strtotime($newNextExecutionDateYm1.$addMonthStr));
                        list($Y, $m, $d) = explode('/', date('Y/n/d', strtotime($addNextExecutionDateYm)));
                        $startMonthWeekNumberYmd = getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $m, $Y);
                        //patternTimeを追記
                        $newNextExecutionDate = $startMonthWeekNumberYmd.' '.$patternTime;
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switchを抜ける
                        }
                    }
                }

                break;

            /////////////////
            //周期ID:6「月末」//
            /////////////////
            case 6:
                //入力必須カラムのチェック
                if($nextExecutionDate == null){
                    $required_column = array($startDate, $exeInterval, $patternTime);
                }else{
                    $required_column = array($exeInterval, $patternTime);
                }
                foreach($required_column as $value){
                    if(!$value){
                        $newNextExecutionDate = null;
                        $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                        break 2; //switch文を抜ける
                    }
                }

                //月計算用
                $addMonthStr = '+'.$exeInterval.' month';

                //次回実行日付がnull(初回登録の場合)
                if($nextExecutionDate == null){
                    //開始日付の月の月末の日付を取得し、patternTimeを追記
                    $startDateLastDayPatternTime = date('Y/m/t', strtotime($startDate)).' '.$patternTime;

                    //開始日付が現在日付より未来かどうか
                    if(strtotime($startDate) > strtotime($nowDate)){
                        //$startDateLastDayPatternTimeが開始日付より未来の場合
                        if(strtotime($startDateLastDayPatternTime) >= strtotime($startDate)){
                            //$startDateLastDayPatternTimeを次回実行日付にする
                            $newNextExecutionDate = $startDateLastDayPatternTime;
                        }else{
                            //$startDateLastDayPatternTimeを基準に、開始日付よりも未来になるまで間隔(月)を加算する
                            $loopCheckDate = $startDateLastDayPatternTime;
                            $addIntervalLastDayPatternTime = $startDateLastDayPatternTime;
                            while(strtotime($startDate) > strtotime($addIntervalLastDayPatternTime)){
                                list($Y, $m, $d) = explode('/', $addIntervalLastDayPatternTime);
                                $Ym1 = $Y.'/'.$m.'/'.'1';
                                $addIntervalYm1 = date('Y/m/d', strtotime($Ym1.$addMonthStr));
                                $addIntervalLastDayPatternTime = date('Y/m/t', strtotime($addIntervalYm1)).' '.$patternTime;
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if(strtotime($loopCheckDate) >= strtotime($addIntervalLastDayPatternTime)){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switchを抜ける
                                }
                            }
                            $newNextExecutionDate = $addIntervalLastDayPatternTime;
                        }
                    }else{
                        //$startDateLastDayPatternTimeが現在日付より未来の場合
                        if(strtotime($startDateLastDayPatternTime) >= strtotime($nowDate)){
                            //$startDateLastDayPatternTimeを次回実行日付にする
                            $newNextExecutionDate = $startDateLastDayPatternTime;
                        }else{
                            //$startDateLastDayPatternTimeを基準に、現在日付よりも未来になるまで間隔(月)を加算する
                            $loopCheckDate = $startDateLastDayPatternTime;
                            $addIntervalLastDayPatternTime = $startDateLastDayPatternTime;
                            while(strtotime($nowDate) > strtotime($addIntervalLastDayPatternTime)){
                                list($Y, $m, $d) = explode('/', $addIntervalLastDayPatternTime);
                                $Ym1 = $Y.'/'.$m.'/'.'1';
                                $addIntervalYm1 = date('Y/m/d', strtotime($Ym1.$addMonthStr));
                                $addIntervalLastDayPatternTime = date('Y/m/t', strtotime($addIntervalYm1)).' '.$patternTime;
                                //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                                if(strtotime($loopCheckDate) >= strtotime($addIntervalLastDayPatternTime)){
                                    $newNextExecutionDate = null;
                                    $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                                    break 2; //switchを抜ける
                                }
                            }
                            $newNextExecutionDate = $addIntervalLastDayPatternTime;
                        }
                    }

                //次回実行日付がある(2回目以降の登録の場合)
                }else{
                     //次回実行日付を基準に間隔(月)を加算する
                    list($Y, $m, $d) = explode('/', $nextExecutionDate);
                    $Ym1 = $Y.'/'.$m.'/'.'1';
                    $addIntervalYm1 = date('Y/m/d', strtotime($Ym1.$addMonthStr));
                    $newNextExecutionDate = date('Y/m/t', strtotime($addIntervalYm1)).' '.$patternTime;
     
                }

                //次回実行日付が作業停止期間中ではないかどうかをチェック
                if(strtotime($newNextExecutionDate) >= strtotime($exeStopStartDate) && strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                    //次回実行日付を基準に作業停止終了日付よりも未来になるまで間隔(月)を加算する
                    $loopCheckDate = $newNextExecutionDate;
                    while(strtotime($exeStopEndDate) >= strtotime($newNextExecutionDate)){
                        list($Y, $m, $d) = explode('/', $newNextExecutionDate);
                        $Ym1 = $Y.'/'.$m.'/'.'1';
                        $addIntervalYm1 = date('Y/m/d', strtotime($Ym1.$addMonthStr));
                        $newNextExecutionDate = date('Y/m/t', strtotime($addIntervalYm1)).' '.$patternTime;
                        //基準にした日付にたいして加算がうまくできていない場合、ループを終了する
                        if(strtotime($loopCheckDate) >= strtotime($newNextExecutionDate)){
                            $newNextExecutionDate = null;
                            $regStatusId = STATUS_MISMATCH_ERROR; //ステータス：不整合エラー
                            break 2; //switchを抜ける
                        }
                    }
                }

                break;

            default:
            //////////////////////////
            //どの周期IDにも当てはまらない//
            //////////////////////////
            $regStatusId = STATUS_UNEXPECTED_ERROR; //ステータス：想定外エラー
            $newNextExecutionDate = null;

        }

        //次回実行日付が終了日付を過ぎているかどうかをチェック
        if($newNextExecutionDate !== null){
            if($endDate != "" && strtotime($newNextExecutionDate) > strtotime($endDate)){
                $regStatusId = STATUS_COMPLETED; //ステータス：完了
                $newNextExecutionDate = null;
            }
        }

        //次回実行日付のフォーマットチェック
        if($newNextExecutionDate !== null){
            $format = 'Y/m/d H:i';
            $dateFormatCheck = DateTime::createFromFormat($format, $newNextExecutionDate);
            if($dateFormatCheck == false){
                $regStatusId = STATUS_UNEXPECTED_ERROR; //ステータス：想定外エラー
                $newNextExecutionDate = null;
            }
        }

    }


    $aryNextExecutionDateAndStatus = array();
    $aryNextExecutionDateAndStatus['statusId'] = $regStatusId;
    $aryNextExecutionDateAndStatus['nextExecutionDate'] = $newNextExecutionDate;

    return $aryNextExecutionDateAndStatus;

}


/*
指定した月($targetMonth)の週番号($patternWeekNumber)曜日($patternDayOfWeek)をYYYY/MM/DD形式で返す
*/
function getWeekNnumberDate($patternDayOfWeek, $patternWeekNumber, $targetMonth, $targetYear){
    //曜日計算用のリスト
    $weekNameList = array('dummy', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    //週番号計算用のリスト
    $patternWeekNumberList = array('dummy', 'First', 'Second', 'Third', 'Fourth', 'Fifth');
    //月（曜日指定）で利用するの名前リスト
    $patternMonthNameList = array('dummy', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');

    //曜日と週番号かと月の文字列
    $strW = $weekNameList[$patternDayOfWeek];
    $strWN = $patternWeekNumberList[$patternWeekNumber];
    $strM = $patternMonthNameList[$targetMonth];

    //指定した週番号の日付を取得
    $targetMonthWeekNumberYmd = date('Y/m/d', strtotime($strWN.' '.$strW.' of '.$strM.' '.$targetYear));

    //週番号の存在を判定（存在しない場合に取り出したYmdが翌月になる）
    list($cY, $cm, $cd) = explode('/', $targetMonthWeekNumberYmd);
    if($cm > $targetMonth){
        //取り出したYmdが翌月になった場合、pattern_week_numberを-1して再度実行
        $lastWN = $patternWeekNumberList[($patternWeekNumber -1)];
        $targetMonthWeekNumberYmd = date('Y/m/d', strtotime($lastWN.' '.$strW.' of '.$strM.' '.$targetYear));
    }

    return $targetMonthWeekNumberYmd;
}

    
?>
