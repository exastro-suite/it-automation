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
    $db_access_user_id  = -3; //
    
    $strFxName          = "proc({$log_file_prefix})";
    
    $aryConfigForSymInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "I_SYMPHONY_CLASS_NO"=>"",
        "I_SYMPHONY_NAME"=>"",
        "I_DESCRIPTION"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "STATUS_ID"=>"",
        "EXECUTION_USER"=>"",
        "ABORT_EXECUTE_FLAG"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $arySymInsValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "I_SYMPHONY_CLASS_NO"=>"",
        "I_SYMPHONY_NAME"=>"",
        "I_DESCRIPTION"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "STATUS_ID"=>"",
        "EXECUTION_USER"=>"",
        "ABORT_EXECUTE_FLAG"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $aryConfigForMovInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_INSTANCE_NO"=>"",
        "I_MOVEMENT_CLASS_NO"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "EXECUTION_NO"=>"",
        "STATUS_ID"=>"",
        "ABORT_RECEPTED_FLAG"=>"",
        "EXE_SKIP_FLAG"=>"",
        "OVRD_OPERATION_NO_UAPK"=>"",
        "OVRD_I_OPERATION_NAME"=>"",
        "OVRD_I_OPERATION_NO_IDBH"=>"",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RELEASED_FLAG"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryMovInsValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_INSTANCE_NO"=>"",
        "I_MOVEMENT_CLASS_NO"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
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
    
    $arySymphonyOnRun = array();
    
    $aryMovement = array();
    
    $boolInTransactionFlag = false;

    // #3115 2018/08/24 Append start
    ////////////////////////////////
    // グローバル変数宣言         //
    ////////////////////////////////
    global $g;
    // #3115 2018/08/24 Append end
    
    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        
        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        // Symphonyインタフェース情報を取得
        $aryConfigForSymIfIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "SYMPHONY_IF_INFO_ID"=>"",
            "SYMPHONY_STORAGE_PATH_ITA"=>"",
            "SYMPHONY_REFRESH_INTERVAL"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        // 更新用のテーブル定義
        $aryConfigForIUD = $aryConfigForSymIfIUD;

        // BIND用のベースソース
        $aryBaseSourceForBind = $aryConfigForSymIfIUD;

        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0')");

        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                               ,"SELECT"
                                               ,"SYMPHONY_IF_INFO_ID"
                                               ,"C_SYMPHONY_IF_INFO"
                                               ,"C_SYMPHONY_IF_INFO_JNL"
                                               ,$aryConfigForIUD
                                               ,$aryBaseSourceForBind
                                               ,$aryTempForSql);

        if( $aryRetBody[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00000500";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $strSqlUtnBody = $aryRetBody[1];
        $aryUtnSqlBind = $aryRetBody[2];
        unset($aryRetBody);

        $aryUtnSqlBind = array();
        
        $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
        if( $aryRetBody[0] !== true ){
            // 例外処理へ
            $strErrStepIdInFx="00000501";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $objQueryUtn =& $aryRetBody[3];

        if($objQueryUtn->effectedRowCount() == 0) {
            // 例外処理へ
            $strErrStepIdInFx="00000502";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        if($objQueryUtn->effectedRowCount() == 1) {
            $rowOfSymphonyInterface = $objQueryUtn->resultFetch();
        } else {
            // 例外処理へ
            $strErrStepIdInFx="00000503";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        unset($objQueryUtn);
        unset($aryRetBody);

        
        //////////////////////////////////
        // 各オーケストレータ情報の収集 //
        //////////////////////////////////
        
        require ($root_dir_path . $ola_lib_agent_php);
        $aryVariant = array('vars'=>array('fx'=>array('registerExecuteNo'=>array('update_user_id'=>$db_access_user_id)))
                           ,'root_dir_path'=>$root_dir_path);
        
        $objOLA = new OrchestratorLinkAgent($objMTS, $objDBCA,$aryVariant);
        
        $aryRetBody = $objOLA->getLiveOrchestratorFromMaster();
        if( $aryRetBody[1] !== null ){
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryOrcListRow = $aryRetBody[0];
        unset($aryRetBody);
        
        //オーケストレータ情報の収集----
        
        //----存在するオーケスト—タ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $strOrcIdNumeric = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $strOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            $aryRetBodyOfAddFunction = $objOLA->addFuncionsPerOrchestrator($strOrcIdNumeric,$strOrcRPath);
            if( $aryRetBodyOfAddFunction[1] !== null ){
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBodyOfAddFunction);
        }
        unset($strOrcIdNumeric);
        unset($strOrcRPath);
        unset($aryOrcListRow);
        //存在するオーケスト—タ分回る----
        
        //----シンフォニーテーブルから、1:未実行/2:未実行(予約)/3:実行中/4:実行中(遅延)の、レコードを取得する
        
        $aryValue = $arySymInsValueTmpl;
        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND STATUS_ID IN (1,2,3,4) AND ( TIME_BOOK IS NULL OR TIME_BOOK <= :KY_DB_DATETIME(6): ) ");
        
        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                              ,"SELECT"
                                              ,"SYMPHONY_INSTANCE_NO"
                                              ,"C_SYMPHONY_INSTANCE_MNG"
                                              ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                              ,$aryConfigForSymInsIUD
                                              ,$aryValue
                                              ,$aryTempForSql);
        
        if( $aryRetBody[0] === false ){
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $strSqlUtnBody = $aryRetBody[1];
        $aryUtnSqlBind = $aryRetBody[2];
        unset($aryRetBody);
        
        $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
        if( $aryRetBody[0] !== true ){
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $objQueryUtn =& $aryRetBody[3];
        
        //----発見行だけループ
        while ( $row = $objQueryUtn->resultFetch() ){
            switch( $row["STATUS_ID"] ){
                case "1": //未実行
                case "2": //未実行(予約)
                    // symphonyインスタンス 共有パス
                    $symphony_instance_Dir = $rowOfSymphonyInterface['SYMPHONY_STORAGE_PATH_ITA'] . "/" . sprintf("%010s",$row['SYMPHONY_INSTANCE_NO']);
                    // symphonyインスタンス 共有パスが存在する場合は一度削除する。
                    if( is_dir( $symphony_instance_Dir) ){
                        system('/bin/rm -rf ' . $symphony_instance_Dir. ' >/dev/null 2>&1');
                    }
                    // symphonyインスタンス 共有パスを生成
                    if( !mkdir( $symphony_instance_Dir, 0777 ) ){
                        // 例外処理へ
                        $strErrStepIdInFx="00000401";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    if( !chmod( $symphony_instance_Dir, 0777 ) ){
                        // 例外処理へ
                        $strErrStepIdInFx="00000402";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $arySymphonyOnRun[] = $row;
                    break;
                case "3": //実行中
                case "4": //実行中(遅延)
                    $arySymphonyOnRun[] = $row;
                    break;
            }
        }
        //発見行だけループ----
        
        unset($objQueryUtn);
        unset($aryRetBody);
        
        //----シンフォニー、を、一個ずつループする
        foreach($arySymphonyOnRun as $rowOfSymphony ){

            // ansible/DSC/AnsibleTower の00_shymphonyLinker.php で参照
            $g['__SYMPHONY_INSTANCE_NO__'] = $rowOfSymphony['SYMPHONY_INSTANCE_NO'];

            ////////////////////////////
            // 変数初期化(ループ冒頭) //
            ////////////////////////////
            
            // トランザクションフラグ(初期値はfalse)
            $boolInTransactionFlag = false;
            
            $boolMovUpdateFlag = false;
            
            $boolMovementFinedAfterHP = false; // フラグ（保留解除ポイントを通過した場合に立てるフラグ）
            $boolScramAfterOrcFined = false; // フラグ（次のムーブメントへ進ませてはならない場合)
            $intFocusMovementSeq = 0; // 現在実行中のムーブメント番号
            
            $strStartTimeOfSymphony = "";
            $strEndTimeOfSymphony = "";
            
            $aryProperParameter = array('CALLER'=>array('NAME'=>'SYNCHRONIZE-SYMPHONY'));
            
            //////////////////////////
            // トランザクション開始 //
            //////////////////////////
            
            if( $objDBCA->transactionStart() === false ){
                // 異常フラグON
                $intErrorFlag = 1;
                
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // トランザクションフラグをONにする
            $boolInTransactionFlag = true;
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //$FREE_LOG = '[処理]トランザクション開始';
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50004");
                require ($root_dir_path . $log_output_php );
            }
            
            // ----MOV-INSTANCE-シーケンスを掴む
            $aryRetBody = getSequenceLockInTrz('C_MOVEMENT_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // MOV-INSTANCE-シーケンスを掴む----
            
            // ----SYM-INSTANCE-シーケンスを掴む
            $aryRetBody = getSequenceLockInTrz('C_SYMPHONY_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // -SYM-INSTANCE-シーケンスを掴む----
            
            //////////////////////////////////////////////////////////////
            // (ここから)ある１のシンフォニーの全ムーブメントを取得する //
            //////////////////////////////////////////////////////////////
            
            //----各ムーブメントの情報収集
            
            $aryTempForSql = array('WHERE'=>"SYMPHONY_INSTANCE_NO = :SYMPHONY_INSTANCE_NO AND DISUSE_FLAG IN ('0') ORDER BY I_MOVEMENT_SEQ ASC");
            
            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForMovInsIUD;
            
            // BIND用のベースソース
            $aryBaseSourceForBind = $aryMovInsValueTmpl;
            
            $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                  ,"SELECT FOR UPDATE"
                                                  ,"MOVEMENT_INSTANCE_NO"
                                                  ,"C_MOVEMENT_INSTANCE_MNG"
                                                  ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                                  ,$aryConfigForIUD
                                                  ,$aryBaseSourceForBind
                                                  ,$aryTempForSql);
            
            if( $aryRetBody[0] === false ){
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $strSqlUtnBody = $aryRetBody[1];
            $aryUtnSqlBind = $aryRetBody[2];
            unset($aryRetBody);
            
            $aryUtnSqlBind['SYMPHONY_INSTANCE_NO'] = $rowOfSymphony['SYMPHONY_INSTANCE_NO'];
            
            $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
            if( $aryRetBody[0] !== true ){
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $objQueryUtn =& $aryRetBody[3];
            
            //----発見行だけループ
            $aryMovement = array();
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryMovement[] = $row;
            }
            //発見行だけループ----
            
            unset($objQueryUtn);
            unset($aryRetBody);
            
            //////////////////////////////////////////////////////////////
            // (ここまで)ある１のシンフォニーの全ムーブメントを取得する //
            //////////////////////////////////////////////////////////////
            
            ////////////////////////////////////////////////////////
            // (ここから)現在のムーブメントSEQUENCEの値を取得する //
            ////////////////////////////////////////////////////////
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101010",array($rowOfSymphony['SYMPHONY_INSTANCE_NO']));
                require ($root_dir_path . $log_output_php );
            }
            
            $aryRetBody = $objOLA->getSymphonyStatusFromMovement($aryMovement);
            if( $aryRetBody[1] !== null ){
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $intMovementLength = $aryRetBody[0]['MOVEMENT_LENGTH'];
            $intFocusMovementSeq = $aryRetBody[0]['FOCUS_MOVEMENT_SEQ'];
            $rowOfFocusMovement = $aryRetBody[0]['FOCUS_MOVEMENT_ROW'];
            unset($aryRetBody);
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101020",array($rowOfSymphony['SYMPHONY_INSTANCE_NO'],$intFocusMovementSeq));
                require ($root_dir_path . $log_output_php );
            }
            
            ////////////////////////////////////////////////////////
            // (ここまで)現在のムーブメントSEQUENCEの値を取得する //
            ////////////////////////////////////////////////////////
            
            
            
            ///////////////////////////////////////////////////////////////////////////////////
            /// (ここから)緊急停止が発令されていて、受理フラグがなければ、緊急停止を発令する //
            ///////////////////////////////////////////////////////////////////////////////////
            
            if( $intFocusMovementSeq === 0 ){
                //----まだ最初のムーブメントも始まっていない場合
                $boolMovementFinedAfterHP = true;
                //まだ最初のムーブメントも始まっていない場合----
            }
            else{
                //----すでに1個はムーブメントがはじまった後である場合
                
                $boolMovUpdateFlag = false;
                $aryMovInsUpdateTgtSource = $rowOfFocusMovement;
                
                //////////////
                // 緊急停止 //
                //////////////
                
                //----緊急停止が発令されているか？されていて、受理フラグがなければ、緊急停止を発令する
                if( $rowOfSymphony['ABORT_EXECUTE_FLAG'] == '2' && $rowOfFocusMovement['ABORT_RECEPTED_FLAG'] == '1' ){
                    //----緊急停止発令フラグが[発令済(2)]で、緊急停止受付確認フラグが[未確認(1)]なので、緊急停止を発令する
                    $aryRetBodyOfScram = $objOLA->srcamExecute($rowOfFocusMovement['I_ORCHESTRATOR_ID']
                                                              ,$rowOfFocusMovement['EXECUTION_NO']
                                                              ,$aryProperParameter);
                    
                    // 次に緊急停止を控えているシンフォニー(i)があることを前提として
                    // 可能な限り、次のシンフォニー(i)へ進める、というポリシー
                    $tmpBoolScramExecute = false;
                    if( $aryRetBodyOfScram[1] === null ){
                        // REST-APIへのリクエストがタイムアウトする等もあるので、広くOK、とするポリシー、をとる。
                        $tmpBoolScramExecute = true;
                        if( $aryRetBodyOfScram[0] === 0 ){
                            $boolMovUpdateFlag = true;
                            $aryMovInsUpdateTgtSource['ABORT_RECEPTED_FLAG'] = '2';
                        }
                    }
                    if( tmpBoolScramExecute === false ){
                        // ロールバック
                        $tmpBoolRollBack = $objDBCA->transactionRollBack();
                        
                        if( $tmpBoolRollBack === false ){
                            //[処理]ロールバック
                            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50016");
                        }
                        else{
                            //ロールバックに失敗しました
                            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50005");
                        }
                        require ($root_dir_path . $log_output_php );
                        //
                        if( $tmpBoolRollBack === false ){
                            // 例外処理へ
                            $strErrStepIdInFx="00001050";
                            //
                            unset($tmpBoolRollBack);
                            unset($tmpBoolScramExecute);
                            unset($aryRetBodyOfScram);
                            //
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //
                        }
                        unset($tmpBoolRollBack);
                        unset($tmpBoolScramExecute);
                        unset($aryRetBodyOfScram);
                        
                        continue;
                    }
                    unset($tmpBoolScramExecute);
                    unset($aryRetBodyOfScram);
                    //緊急停止発令フラグが[発令済(2)]で、緊急停止受付確認フラグが[未確認(1)]なので、緊急停止を発令する----
                }
                //緊急停止が発令されているか？されていて、受理フラグがなければ、緊急停止を発令する----
                
                //すでに1個はムーブメントがはじまった後である場合----
            }
            
            ///////////////////////////////////////////////////////////////////////////////////
            /// (ここから)緊急停止が発令されていて、受理フラグがなければ、緊急停止を発令する //
            ///////////////////////////////////////////////////////////////////////////////////
            
            
            
            //////////////////////////////////////////////////////
            // (ここから)かく在るべき現在のステータスを算出する //
            //////////////////////////////////////////////////////
            
            if( $intFocusMovementSeq == 0 ){
                //----まだ最初のムーブメントも始まっていない場合
                $boolMovementFinedAfterHP = true;
                //まだ最初のムーブメントも始まっていない場合----
            }
            else{
                //----すでに1個はムーブメントがはじまった後である場合
                //----RedMineチケット1026
                $boolOrcEndPhase = false;
                $boolSqlExexuteToSync = false;
                switch( $rowOfFocusMovement['STATUS_ID'] ){
                    case "5":  // 実行完了
                    case "12": // Skip完了
                    case "8":  // 保留中
                    case "13": // Skip後保留中
                    case "9":  // 正常終了
                    case "14": // Skip終了
                        $boolSqlExexuteToSync = false;
                        $boolOrcEndPhase = true;
                        break;
                    case "2": // 準備中
                    case "3": // 実行中
                    case "4": // 実行中(遅延)
                        ////////////////////////////////////////////////////////////////////////////////////
                        // (ここから)現在のムーブメントについて、下位オーケストレータに状態を問い合わせる //
                        ////////////////////////////////////////////////////////////////////////////////////
                        $boolSqlExexuteToSync = true;
                        $aryRetBodyOfSMfO = $objOLA->getMovementStatusFromOrchestrator($rowOfFocusMovement['I_ORCHESTRATOR_ID']
                                                                                      ,$rowOfFocusMovement['EXECUTION_NO']);
                        
                        if( $aryRetBodyOfSMfO[1] !== null ){
                            // 例外処理へ
                            $strErrStepIdInFx="00001100";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $strStatusFromOrch = $aryRetBodyOfSMfO[0];
                        $strDateOfTimeEndOnOrch = $aryRetBodyOfSMfO[4]['TIME_END'];
                        switch( $strStatusFromOrch ){
                            case "5": // mov.実行完了（下位オーケストレータは、正常に終了していた場合）
                                $boolOrcEndPhase = true;
                                break;
                            case "11": // mov.想定外エラー
                            case "7": // mov.緊急停止
                            case "6": // mov.異常終了
                            case "4": // mov.実行中(遅延)
                            case "3": // mov.実行中
                                break;
                            case "9": // mov.正常終了
                            case "8": // mov.保留中
                            case "2": // mov.準備中
                            case "1": // mov.未実行
                            default: // 返し値として存在してはいけない値だった場合
                                // 例外処理へ
                                $strErrStepIdInFx="00001200";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                break;
                        }
                        unset($aryRetBodyOfSMfO);
                        ////////////////////////////////////////////////////////////////////////////////////
                        // (ここまで)現在のムーブメントについて、下位オーケストレータに状態を問い合わせる //
                        ////////////////////////////////////////////////////////////////////////////////////
                        break;
                }
                
                if( $boolOrcEndPhase === true ){
                    //----オーケストレータ側が「完了」している場合
                    if( $boolSqlExexuteToSync === false 
                        && ( $rowOfFocusMovement['STATUS_ID'] === '5' || $rowOfFocusMovement['STATUS_ID'] === '12' )
                       ){
                        //----ムーブメント側が実行完了の場合
                        if( 0 < strlen($rowOfFocusMovement['RELEASED_FLAG']) ){
                            if( $rowOfFocusMovement['I_NEXT_PENDING_FLAG'] == '1' 
                                && $rowOfFocusMovement['RELEASED_FLAG'] == '1' ){
                                //----保留ポイントがあり、(1=)未解除の場合なので、保留中、に変更する
                                $boolMovUpdateFlag = true;
                                
                                if( $rowOfFocusMovement['STATUS_ID'] == '5' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '8';
                                }
                                else if( $rowOfFocusMovement['STATUS_ID'] == '12' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '13';
                                }
                                else{
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001340";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                //保留ポイントがあり、(1=)未解除の場合なので、保留中、に変更する----
                            }
                            else if( $rowOfFocusMovement['I_NEXT_PENDING_FLAG'] == '1' 
                                     && $rowOfFocusMovement['RELEASED_FLAG'] == '2' ){
                                //----保留ポイントがあり、(2=)解除済の場合なので、正常終了、に変更する
                                $boolMovUpdateFlag = true;
                                
                                if( $rowOfFocusMovement['STATUS_ID'] == '5' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
                                }
                                else if( $rowOfFocusMovement['STATUS_ID'] == '12' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '14';
                                }
                                else{
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001345";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                
                                $boolMovementFinedAfterHP = true;
                                //保留ポイントがあり、(2=)解除済の場合なので、正常終了、に変更する----
                            }
                            else{
                                // 例外処理へ
                                $strErrStepIdInFx="00001300";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                        }
                        else{
                            if( $rowOfFocusMovement['I_NEXT_PENDING_FLAG'] == '2' 
                                && strlen($rowOfFocusMovement['RELEASED_FLAG']) === 0 ){
                                //----保留ポイントがない場合、正常終了、に変更する
                                $boolMovUpdateFlag = true;
                                
                                if( $rowOfFocusMovement['STATUS_ID'] == '5' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
                                }
                                else if( $rowOfFocusMovement['STATUS_ID'] == '12' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '14';
                                }
                                else{
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001350";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                
                                $boolMovementFinedAfterHP = true;
                                //保留ポイントがない場合、正常終了、に変更する----
                            }
                            else{
                                // 例外処理へ
                                $strErrStepIdInFx="00001400";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                        }
                        
                        //----緊急停止が発令されていた場合
                        if( $rowOfSymphony['ABORT_EXECUTE_FLAG'] == '2' ){
                            $boolScramAfterOrcFined = true;
                        }
                        //緊急停止が発令されていた場合----
                                     
                        //ムーブメント側が実行完了の場合----
                    }
                    else if ( $boolSqlExexuteToSync === false 
                              && ( $rowOfFocusMovement['STATUS_ID'] == '8' || $rowOfFocusMovement['STATUS_ID'] == '13' ) 
                             ){
                        //----ムーブメント側が保留の場合
                        if( $rowOfFocusMovement['I_NEXT_PENDING_FLAG'] == '1' 
                            && $rowOfFocusMovement['RELEASED_FLAG'] == '2' ){
                            //----保留ポイントがあり、解除された場合なので、正常終了、に変更する
                            $boolMovUpdateFlag = true;
                            
                            if( $rowOfFocusMovement['STATUS_ID'] == '8' ){
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
                            }
                            else if( $rowOfFocusMovement['STATUS_ID'] == '13' ){
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = '14';
                            }
                            else{
                                // 例外処理へ
                                $strErrStepIdInFx="00001450";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            
                            $boolMovementFinedAfterHP = true;
                            //解除された場合なので、正常終了、に変更する----
                        }
                        else if( $rowOfFocusMovement['I_NEXT_PENDING_FLAG'] == '1' 
                                 && $rowOfFocusMovement['RELEASED_FLAG'] == '1' ){
                            //----保留ポイントがあり、(1=)未解除の場合なので、保留中、で維持する
                            //保留ポイントがあり、(1=)未解除の場合なので、保留中、で維持する----
                        }
                        else{
                            // 例外処理へ
                            $strErrStepIdInFx="00001500";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        //----緊急停止が発令されていた場合
                        if( $rowOfSymphony['ABORT_EXECUTE_FLAG'] == '2' ){
                            $boolScramAfterOrcFined = true;
                        }
                        //緊急停止が発令されていた場合----
                        
                        //ムーブメント側が保留の場合----
                    }
                    else if ( $boolSqlExexuteToSync === false 
                              && $rowOfFocusMovement['STATUS_ID'] == '9' ){
                        //----正常終了の場合
                        $boolMovementFinedAfterHP = true;
                        //正常終了の場合----
                    }
                    else if( $boolSqlExexuteToSync === true ){
                        //----オーケストレータに問い合わせた場合は、一旦「実行完了」に変更する
                        $boolMovUpdateFlag = true;
                        
                        $aryMovInsUpdateTgtSource['STATUS_ID'] = '5';
                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                        //オーケストレータに問い合わせた場合は、一旦「実行完了」に変更する----
                    }
                    //オーケストレータ側が「完了」している場合----
                }
                else{
                    //----オーケストレータ側が「完了」していない場合
                    if( strlen($strStatusFromOrch) != 0 
                        && $strStatusFromOrch != $rowOfFocusMovement['STATUS_ID'] ){
                        //----問い合わせ結果と、現在のステータスに差が発生している
                        $boolMovUpdateFlag = true;
                        $aryMovInsUpdateTgtSource['STATUS_ID'] = $strStatusFromOrch;
                        if( $strStatusFromOrch == '6' || $strStatusFromOrch == '7' ){
                            //----異常停止、緊急停止なので、終了日時を、オーケストレータ側の値で更新する
                            $aryMovInsUpdateTgtSource['TIME_END'] = $strDateOfTimeEndOnOrch;
                            //異常停止、緊急停止なので、終了日時を、オーケストレータ側の値で更新する----
                        }
                        //問い合わせ結果と、現在のステータスに差が発生している----
                    }
                    //オーケストレータ側が「完了」していない場合----
                }
                unset($boolSqlExexuteToSync);
                //すでに1個はムーブメントがはじまった後である場合----
            }
            //////////////////////////////////////////////////////
            // (ここまで)かく在るべき現在のステータスを算出する //
            //////////////////////////////////////////////////////
            
            
            
            ////////////////////////////////////////////////////////
            // (ここから)現在のムーブメントのステータスを更新する //
            ////////////////////////////////////////////////////////
            
            if( $boolMovUpdateFlag === true ){
                $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                
                // 更新用のテーブル定義
                $aryConfigForIUD = $aryConfigForMovInsIUD;
                
                // BIND用のベースソース
                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                
                $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                    ,"UPDATE"
                                                    ,"MOVEMENT_INSTANCE_NO"
                                                    ,"C_MOVEMENT_INSTANCE_MNG"
                                                    ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                                    ,$aryConfigForIUD
                                                    ,$aryBaseSourceForBind);
                
                if( $aryRetBody[0] === false ){
                    // 例外処理へ
                    $strErrStepIdInFx="00001600";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                $strSqlUtnBody = $aryRetBody[1];
                $aryUtnSqlBind = $aryRetBody[2];
                
                $strSqlJnlBody = $aryRetBody[3];
                $aryJnlSqlBind = $aryRetBody[4];
                unset($aryRetBody);
                
                // ----履歴シーケンス払い出し
                $aryRetBody = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
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
                
                $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
                $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
                if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                    // 例外処理へ
                    $strErrStepIdInFx="00001800";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($aryRetBody01);
                unset($aryRetBody02);
                
                $rowOfFocusMovement = $aryMovInsUpdateTgtSource;
            }
            
            ////////////////////////////////////////////////////////
            // (ここまで)現在のムーブメントのステータスを更新する //
            ////////////////////////////////////////////////////////
            
            
            
            //////////////////////////////////////
            // (ここから)新たなムーブメント起動 //
            //////////////////////////////////////
            
            if( $boolMovementFinedAfterHP === true ){
                //----各ムーブメントのHP(ホールドポイント)を通過した場合
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    //$FREE_LOG = '[処理]新たなムーブメント起動判定(開始)';
                    $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101030");
                    require ($root_dir_path . $log_output_php );
                }
                
                if( $intFocusMovementSeq + 1 <= $intMovementLength ){
                    //----最後のムーブメントが終わっていない場合
                    if( $rowOfSymphony['ABORT_EXECUTE_FLAG'] == '1' ){
                        //----緊急停止が発令されていないので、次のムーブメントへ進む
                        
                        $aryStartTargetMovement = $aryMovement[$intFocusMovementSeq];
                        $intFocusMovementSeq = intval($aryStartTargetMovement['I_MOVEMENT_SEQ']);
                        
                        $aryMovInsUpdateTgtSource = $aryStartTargetMovement;
                        
                        if( $aryStartTargetMovement['STATUS_ID'] != '1' ){
                            //----未実行以外だった
                            
                            // 例外処理へ
                            $strErrStepIdInFx="00001900";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            
                            //未実行以外だった----
                        }
                        
                        if( $aryStartTargetMovement['EXE_SKIP_FLAG'] == '2' ){
                            //----Skipフラグが立っていた場合
                            $aryMovInsUpdateTgtSource['STATUS_ID']    = '12'; //Skip完了[実行完了(同位)]
                            //Skipフラグが立っていた場合----
                        }
                        else{
                            //----オーケストレータ側のシーケンスをロックする
                            $aryRetBody = $objOLA->sequencesLockInTrz($aryStartTargetMovement['I_ORCHESTRATOR_ID']);
                            if( $aryRetBody[1] !== null ){
                                //----オーケストレータ側の、シーケンスをロックできなかった
                                
                                // 例外処理へ
                                $strErrStepIdInFx="00002000";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                
                                //オーケストレータ側の、シーケンスをロックできなかった----
                            }
                            unset($aryRetBody);

                            if( 0 < strlen($aryStartTargetMovement['OVRD_OPERATION_NO_UAPK']) ){
                                $tmpVarOperationNoUAPK = $aryStartTargetMovement['OVRD_OPERATION_NO_UAPK'];
                            }
                            else{
                                $tmpVarOperationNoUAPK = $rowOfSymphony['OPERATION_NO_UAPK'];
                            }
                            $aryRetBody = $objOLA->registerExecuteNo($aryStartTargetMovement['I_ORCHESTRATOR_ID']
                                                                    ,$aryStartTargetMovement['I_PATTERN_ID']
                                                                    ,$tmpVarOperationNoUAPK
                                                                    ,""
                                                                    ,true);
                            unset($tmpVarOperationNoUAPK);
                            
                            if( $aryRetBody[1] !== null ){
                                //----オーケストレータ側に、レコードを挿入できなかった
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = '10'; //準備エラー
                                //オーケストレータ側に、レコードを挿入できなかった----
                            }
                            else{
                                //----オーケストレータ側に、レコードを挿入できた
                                $aryMovInsUpdateTgtSource['STATUS_ID']    = '2'; //準備中
                                $aryMovInsUpdateTgtSource['EXECUTION_NO'] = $aryRetBody[0];
                                $aryMovInsUpdateTgtSource['TIME_START']   = $aryRetBody[4];
                                //オーケストレータ側に、レコードを挿入できた----
                            }
                        }
                        
                        $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                        
                        // 更新用のテーブル定義
                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                        
                        // BIND用のベースソース
                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                        
                        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                            ,"UPDATE"
                                                            ,"MOVEMENT_INSTANCE_NO"
                                                            ,"C_MOVEMENT_INSTANCE_MNG"
                                                            ,"C_MOVEMENT_INSTANCE_MNG_JNL"
                                                            ,$aryConfigForIUD
                                                            ,$aryBaseSourceForBind);
                        
                        if( $aryRetBody[0] === false ){
                            // 例外処理へ
                            $strErrStepIdInFx="00002100";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $strSqlUtnBody = $aryRetBody[1];
                        $aryUtnSqlBind = $aryRetBody[2];
                        
                        $strSqlJnlBody = $aryRetBody[3];
                        $aryJnlSqlBind = $aryRetBody[4];
                        
                        unset($aryRetBody);
                        
                        // ----履歴シーケンス払い出し
                        $aryRetBody = getSequenceValueFromTable('C_MOVEMENT_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
                        if( $aryRetBody[1] != 0 ){
                            // 例外処理へ
                            $strErrStepIdInFx="00002200";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        else{
                            $varJSeq = $aryRetBody[0];
                            $aryJnlSqlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                        }
                        unset($aryRetBody);
                        // 履歴シーケンス払い出し----
                        
                        $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
                        $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
                        if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                            // 例外処理へ
                            $strErrStepIdInFx="00002300";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($aryRetBody01);
                        unset($aryRetBody02);
                        
                        $rowOfFocusMovement = $aryMovInsUpdateTgtSource;
                        
                        if( $intFocusMovementSeq == 1 ){
                            $strStartTimeOfSymphony = $aryUtnSqlBind['TIME_START'];
                        }
                        
                        //最後のムーブメントが終わっておらず、緊急停止が発令されていないので、次のムーブメントへ進む----
                    }
                    else if( $rowOfSymphony['ABORT_EXECUTE_FLAG'] == '2' ){
                        //----緊急停止が発令されている場合
                        $boolScramAfterOrcFined = true;
                        //緊急停止が発令されている場合----
                    }
                    //最後のムーブメントが終わっていない場合----
                }
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101040");
                    require ($root_dir_path . $log_output_php );
                }
                
                //各ムーブメントのHP(ホールドポイント)を通過した場合----
            }
            //////////////////////////////////////
            // (ここまで)新たなムーブメント起動 //
            //////////////////////////////////////
            
            
            
            //////////////////////////////////////////////////////////////
            // (ここから)ムーブメントからシンフォニーへのステータス同期 //
            //////////////////////////////////////////////////////////////
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101050");
                require ($root_dir_path . $log_output_php );
            }
            
            $strBeforeStatusNumeric = $rowOfSymphony['STATUS_ID'];
            $strAfterStatusNumeric = $strBeforeStatusNumeric;
            
            if( $intFocusMovementSeq === 0 ){
                //----未実行の場合
                if( $boolScramAfterOrcFined === true ){
                    $strAfterStatusNumeric = '6'; //緊急停止
                    // 終了日時を入れる
                    $strEndTimeOfSymphony = "DATETIMEAUTO(6)";
                }
                //未実行の場合----
            }
            else{
                //----実行開始されている場合
                //----RedMineチケット1026
                switch( $rowOfFocusMovement['STATUS_ID'] ){
                    case "1": // 未実行
                        $strAfterStatusNumeric = '1';
                        break;
                    case "2":  // 準備中
                    case "3":  // 実行中
                    case "5":  // 実行完了
                    case "12": // Skip完了
                    case "8":  // 保留中
                    case "13": // Skip後保留中
                    case "9":  // 正常終了
                    case "14": // Skip終了
                        $strAfterStatusNumeric = '3'; //実行中
                        if( ( $rowOfFocusMovement['STATUS_ID'] == '9' || $rowOfFocusMovement['STATUS_ID'] == '14' ) 
                              && $intMovementLength == $intFocusMovementSeq ){
                            $strAfterStatusNumeric = '5'; //正常終了
                           // 終了日時を入れる
                           $strEndTimeOfSymphony = "DATETIMEAUTO(6)";
                        }
                        break;
                    case "4": // 実行中(遅延)
                        $strAfterStatusNumeric = '4'; //実行中(遅延)
                        break;
                    case "7": // 緊急停止
                        $strAfterStatusNumeric = '6'; //緊急停止
                        // 終了日時を入れる
                        $strEndTimeOfSymphony = "DATETIMEAUTO(6)";
                        break;
                    case "6": // 異常終了
                    case "10": // 準備エラー
                        $strAfterStatusNumeric = '7'; //異常終了
                        if( $rowOfFocusMovement['STATUS_ID'] == '6' ){
                            // 終了日時を入れる
                            $strEndTimeOfSymphony = "DATETIMEAUTO(6)";
                        }
                        break;
                    case "11": // 想定外エラー
                        $strAfterStatusNumeric = '8'; //想定外エラー
                        break;
                }
                if( $boolScramAfterOrcFined === true ){
                    $strAfterStatusNumeric = '6'; //緊急停止
                    // 終了日時を入れる
                    $strEndTimeOfSymphony = "DATETIMEAUTO(6)";
                }
                //実行開始されている場合----
            }
            
            if( $strBeforeStatusNumeric!=$strAfterStatusNumeric){
                $arySymInsUpdateTgtSource = $rowOfSymphony;
                
                // ----シンフォニー-INSTANCEを更新
                
                $arySymInsUpdateTgtSource['STATUS_ID']        = $strAfterStatusNumeric;
                $arySymInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                
                if( strlen($rowOfSymphony['TIME_START']) === 0 && 0 < strlen($strStartTimeOfSymphony) ){
                    $arySymInsUpdateTgtSource['TIME_START'] = $strStartTimeOfSymphony;
                }
                if( strlen($rowOfSymphony['TIME_END']) === 0 && 0 < strlen($strEndTimeOfSymphony) ){
                    $arySymInsUpdateTgtSource['TIME_END'] = $strEndTimeOfSymphony;
                }
                
                // 更新用のテーブル定義
                $aryConfigForIUD = $aryConfigForSymInsIUD;
                
                // BIND用のベースソース
                $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                
                $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                    ,"UPDATE"
                                                    ,"SYMPHONY_INSTANCE_NO"
                                                    ,"C_SYMPHONY_INSTANCE_MNG"
                                                    ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                                    ,$aryConfigForIUD
                                                    ,$aryBaseSourceForBind);
                
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
                
                // ----履歴シーケンス払い出し
                $aryRetBody = getSequenceValueFromTable('C_SYMPHONY_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
                if( $aryRetBody[1] != 0 ){
                    // 例外処理へ
                    $strErrStepIdInFx="00002400";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                else{
                    $varJSeq = $aryRetBody[0];
                    $aryJnlSqlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                }
                unset($aryRetBody);
                // 履歴シーケンス払い出し----
                
                $aryRetBody01 = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
                $aryRetBody02 = singleSQLCoreExecute($objDBCA, $strSqlJnlBody, $aryJnlSqlBind, $strFxName);
                if( $aryRetBody01[0] !== true || $aryRetBody02[0] !== true ){
                    // 例外処理へ
                    $strErrStepIdInFx="00002500";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($aryRetBody01);
                unset($aryRetBody02);
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-101060");
                require ($root_dir_path . $log_output_php );
            }
            
            //////////////////////////////////////////////////////////////
            // (ここまで)ムーブメントからシンフォニーへのステータス同期 //
            //////////////////////////////////////////////////////////////
            
            if( $objDBCA->transactionCommit() !== true ){
                // 異常フラグON
                $intErrorFlag = 1;
                
                // 例外処理へ
                $strErrStepIdInFx="00002600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50015");
                require ($root_dir_path . $log_output_php );
            }
        }
        //シンフォニー、を、一個ずつループする----
        
        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50005");
            require ($root_dir_path . $log_output_php );
        }
    }
    catch (Exception $e){
        if( $log_level    === 'DEBUG' ||
            $intErrorFlag   != 0        ||
            $intWarningFlag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
        
        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $boolInTransactionFlag ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                //[処理]ロールバック
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50016");
            }
            else{
                //ロールバックに失敗しました
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50005");
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()===true ){
                //$FREE_LOG = 'トランザクション終了';
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50005");
            }
            else{
                //$FREE_LOG = 'トランザクション処理で重大な異常が発生しました。';
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50004");
            }
            
            require ($root_dir_path . $log_output_php );
        }
    }
    
    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $intErrorFlag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(1);
    }
    elseif( $intWarningFlag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        // リターンコード
        exit(0);
    }
?>
