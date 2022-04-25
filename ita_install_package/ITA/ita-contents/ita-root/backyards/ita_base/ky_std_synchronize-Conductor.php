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
    $weblog_output_php  = '/libs/webcommonlibs/web_php_functions.php';
    $db_access_user_id  = -6; //
    
    $strFxName          = "proc({$log_file_prefix})";
    
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
        "PAUSE_STATUS_ID"=>"",
        "EXECUTION_USER"=>"",
        "ABORT_EXECUTE_FLAG"=>"",
        "CONDUCTOR_CALL_FLAG"=>"",
        "CONDUCTOR_CALLER_NO"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "EXEC_LOG"=>"",
        "I_NOTICE_INFO"=>"",
        "NOTICE_LOG"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $arySymInsValueTmpl = array(
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
        "PAUSE_STATUS_ID"=>"",
        "EXECUTION_USER"=>"",
        "ABORT_EXECUTE_FLAG"=>"",
        "CONDUCTOR_CALL_FLAG"=>"",
        "CONDUCTOR_CALLER_NO"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "EXEC_LOG"=>"",
        "I_NOTICE_INFO"=>"",
        "NOTICE_LOG"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $aryConfigForMovInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "NODE_INSTANCE_NO"=>"",
        "I_NODE_CLASS_NO"=>"",
        "I_NODE_TYPE_ID"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_WINRM_ID"=>"",
        #"I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "CONDUCTOR_INSTANCE_CALL_NO"=>"",
        "EXECUTION_NO"=>"",
        "STATUS_ID"=>"",
        "ABORT_RECEPTED_FLAG"=>"",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RELEASED_FLAG"=>"",
        "EXE_SKIP_FLAG"=>"",
        "END_TYPE"=>"",
        "OVRD_OPERATION_NO_UAPK"=>"",
        "OVRD_I_OPERATION_NAME"=>"",
        "OVRD_I_OPERATION_NO_IDBH"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryMovInsValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "NODE_INSTANCE_NO"=>"",
        "I_NODE_CLASS_NO"=>"",
        "I_NODE_TYPE_ID"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_WINRM_ID"=>"",
        #"I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "CONDUCTOR_INSTANCE_CALL_NO"=>"",
        "EXECUTION_NO"=>"",
        "STATUS_ID"=>"",
        "ABORT_RECEPTED_FLAG"=>"",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RELEASED_FLAG"=>"",
        "EXE_SKIP_FLAG"=>"",
        "END_TYPE"=>"",
        "OVRD_OPERATION_NO_UAPK"=>"",
        "OVRD_I_OPERATION_NAME"=>"",
        "OVRD_I_OPERATION_NO_IDBH"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );


    $arrayConfigForNodeClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "END_TYPE"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryNodeClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "END_TYPE"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );


    $arrayConfigForSymInsIUD = array(
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
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $arrayConfigForMovInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_INSTANCE_NO"=>"",
        "I_MOVEMENT_CLASS_NO"=>"",
        "I_ORCHESTRATOR_ID"=>"",
        "I_PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_WINRM_ID"=>"",
        "I_MOVEMENT_SEQ"=>"",
        "I_NEXT_PENDING_FLAG"=>"",
        "I_DESCRIPTION"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "EXECUTION_NO"=>"",
        "STATUS_ID"=>"",
        "ABORT_RECEPTED_FLAG"=>"",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "RELEASED_FLAG"=>"",
        "EXE_SKIP_FLAG"=>"",
        "OVRD_OPERATION_NO_UAPK"=>"",
        "OVRD_I_OPERATION_NAME"=>"",
        "OVRD_I_OPERATION_NO_IDBH"=>"",
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
    
    $aryConductorOnRun = array();
    
    $aryMovement = array();
    
    $boolInTransactionFlag = false;


    ////////////////////////////////
    // グローバル変数宣言         //
    ////////////////////////////////
    global $g;
    
    $g['login_id'] = $db_access_user_id;

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////


    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        
        require ($root_dir_path . $weblog_output_php );


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

        // Conductorインタフェース情報を取得
        $aryConfigForSymIfIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "CONDUCTOR_IF_INFO_ID"=>"",
            "CONDUCTOR_STORAGE_PATH_ITA"=>"",
            "CONDUCTOR_REFRESH_INTERVAL"=>"",
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
                                               ,"CONDUCTOR_IF_INFO_ID"
                                               ,"C_CONDUCTOR_IF_INFO"
                                               ,"C_CONDUCTOR_IF_INFO_JNL"
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
            $rowOfConductorInterface = $objQueryUtn->resultFetch();
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

        //各ドライバのインターフェース情報設定　#587
        $arrDataRelaystoragePath = array();
        $arrDriversList = $objOLA->getStatusFileInfo();
        
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

            //各ドライバのインターフェース情報取得　#587  
            if( array_key_exists($strOrcIdNumeric, $arrDriversList) ){
                
                $strDriverTable = $arrDriversList[$strOrcIdNumeric]["table"];
                $strDriverCol = $arrDriversList[$strOrcIdNumeric]["column"];
                $strDriverPath = $arrDriversList[$strOrcIdNumeric]["path"];
                $sql =   " SELECT * FROM {$strDriverTable} TAB_A "
                        ." WHERE TAB_A.DISUSE_FLAG = '0' "
                        ."";
                $objQuery = $objDBCA->sqlPrepare($sql);
                $r = $objQuery->sqlExecute();
                if( $r == 1 && $strDriverCol != "" ){
                    $row = $objQuery->resultFetch();
                    $arrDataRelaystoragePath[$strOrcIdNumeric] = $row[$strDriverCol] ."/". $strDriverPath ;
                }else{
                    $arrDataRelaystoragePath[$strOrcIdNumeric] = "";
                }                
            }else{
                $arrDataRelaystoragePath[$strOrcIdNumeric] = "";
            }

            unset($aryRetBodyOfAddFunction);
        }
        unset($strOrcIdNumeric);
        unset($strOrcRPath);
        unset($aryOrcListRow);
        //存在するオーケスト—タ分回る----


        //////////////////////////////////
        // CONDUCTORの収集 //
        //////////////////////////////////

        //----CONDUCTORから、1:未実行/2:未実行(予約)/3:実行中/4:実行中(遅延)の、レコードを取得する
        
        $aryValue = $arySymInsValueTmpl;
        $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0') AND STATUS_ID IN (1,2,3,4) AND ( TIME_BOOK IS NULL OR TIME_BOOK <= :KY_DB_DATETIME(6): ) ");
        
        $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                              ,"SELECT"
                                              ,"CONDUCTOR_INSTANCE_NO"
                                              ,"C_CONDUCTOR_INSTANCE_MNG"
                                              ,"C_CONDUCTOR_INSTANCE_MNG_JNL"
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
                    // conductorインスタンス 共有パス
                    $CONDUCTOR_instance_Dir = $rowOfConductorInterface['CONDUCTOR_STORAGE_PATH_ITA'] . "/" . sprintf("%010s",$row['CONDUCTOR_INSTANCE_NO']);
                    // conductorインスタンス 共有パスが存在する場合は一度削除する。
                    if( is_dir( $CONDUCTOR_instance_Dir) ){
                        system('/bin/rm -rf ' . $CONDUCTOR_instance_Dir. ' >/dev/null 2>&1');
                    }
                    // conductorインスタンス 共有パスを生成
                    if( !mkdir( $CONDUCTOR_instance_Dir, 0777 ) ){
                        // 例外処理へ
                        $strErrStepIdInFx="00000401";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    if( !chmod( $CONDUCTOR_instance_Dir, 0777 ) ){
                        // 例外処理へ
                        $strErrStepIdInFx="00000402";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $aryConductorOnRun[] = $row;

                    break;
                case "3": //実行中
                    // conductorインスタンス 共有パス
                    $CONDUCTOR_instance_Dir = $rowOfConductorInterface['CONDUCTOR_STORAGE_PATH_ITA'] . "/" . sprintf("%010s",$row['CONDUCTOR_INSTANCE_NO']);
                    // Conductor call　で実行された場合処理 #733
                    if( is_dir( $CONDUCTOR_instance_Dir) === false  && $row["CONDUCTOR_CALL_FLAG"] == 2 ){
                        // conductorインスタンス 共有パスを生成
                        if( !mkdir( $CONDUCTOR_instance_Dir, 0777 ) ){
                            // 例外処理へ
                            $strErrStepIdInFx="00000401";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        if( !chmod( $CONDUCTOR_instance_Dir, 0777 ) ){
                            // 例外処理へ
                            $strErrStepIdInFx="00000402";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                case "4": //実行中(遅延)
                    $aryConductorOnRun[] = $row;
                    break;
            }
        }
        //発見行だけループ----
        
        unset($objQueryUtn);
        unset($aryRetBody);

        //----CONDUCTORを、一個ずつループする
        foreach($aryConductorOnRun as $rowOfConductor ){

            // ansible/DSC/AnsibleTower の00_shymphonyLinker.php で参照
            $g['__CONDUCTOR_INSTANCE_NO__'] = $rowOfConductor['CONDUCTOR_INSTANCE_NO'];
            $g['__TOP_ACCESS_AUTH__'] = $rowOfConductor['ACCESS_AUTH'];   

            ////////////////////////////
            // 変数初期化(ループ冒頭) //
            ////////////////////////////
            
            // トランザクションフラグ(初期値はfalse)
            $boolInTransactionFlag = false;
            
            $boolMovUpdateFlag = false;
            
            $boolMovementFinedAfterHP = false; // フラグ（保留解除ポイントを通過した場合に立てるフラグ）
            $boolScramAfterOrcFined = false; // フラグ（次のムーブメントへ進ませてはならない場合)
            $intFocusMovementSeq = 0; // 現在実行中のムーブメント番号
            
            $strStartTimeOfConductor = "";
            $strEndTimeOfConductor = "";
            $tmpexecLogMessage = "";

            $aryProperParameter = array('CALLER'=>array('NAME'=>'SYNCHRONIZE-CONDUCTOR'));

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
            
            // ---NODE-INSTANCE-シーケンスを掴む
            $aryRetBody = getSequenceLockInTrz('C_NODE_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // NODE-INSTANCE-シーケンスを掴む----
            
            // ----SYM-INSTANCE-シーケンスを掴む
            $aryRetBody = getSequenceLockInTrz('C_CONDUCTOR_INSTANCE_MNG_JSQ','A_SEQUENCE');
            if( $aryRetBody[1] != 0 ){
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($aryRetBody);
            // -SYM-INSTANCE-シーケンスを掴む----
            
            
            //////////////////////////////////////////////////////////////
            // (ここから)ある１のCONDUCTORの全NODEを取得する //
            //////////////////////////////////////////////////////////////

            //----各NODEの情報収集
            
            $aryTempForSql = array('WHERE'=>"CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO AND DISUSE_FLAG IN ('0') ORDER BY NODE_INSTANCE_NO ASC");
            
            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForMovInsIUD;
            
            // BIND用のベースソース
            $aryBaseSourceForBind = $aryMovInsValueTmpl;

            $arySqlBind = array( 'CONDUCTOR_INSTANCE_NO' => $rowOfConductor['CONDUCTOR_INSTANCE_NO'] );

            $aryMovement =  getNodeInstanceInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);

            //////////////////////////////////////////////////////////////
            // (ここまで)ある１のCONDUCTORの全NODEを取得する //
            //////////////////////////////////////////////////////////////


            ////////////////////////////////////////////////////////
            // (ここから)NODEクラスを取得する //
            ////////////////////////////////////////////////////////

            $aryRetBody = $objOLA->getInfoOfOneNodeTerminal($rowOfConductor['I_CONDUCTOR_CLASS_NO'], 0,0,1);#TERMINALあり

            //整形
            $arrNodeClassInfo=array();
            foreach ( $aryRetBody[4] as $key => $value) {
                    $arrNodeClassInfo[$value['NODE_CLASS_NO']]=$value; 
            }

            ////////////////////////////////////////////////////////
            // (ここまで)NODEクラスを取得する //
            ////////////////////////////////////////////////////////

            ////////////////////////////////////////////////////////
            // (ここから)現在のNODEの状態を取得する //
            ////////////////////////////////////////////////////////
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-170002",array($rowOfConductor['CONDUCTOR_INSTANCE_NO']));
                require ($root_dir_path . $log_output_php );
            }

            $aryRetBody = $objOLA->getConductorStatusFromNode($aryMovement);

            if( $aryRetBody[1] !== null ){
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $intMovementLength = $aryRetBody[0]['NODE_LENGTH'];
            $intFocusMovementSeq = $aryRetBody[0]['FOCUS_NODE_SEQ'];
            $rowOfFocusMovement = $aryRetBody[0]['FOCUS_NODE_ROW'];
            $arrOfFocusMovement = $aryRetBody[0]['RUNS_NODE'];
            $arrOfErrMovement = $aryRetBody[0]['ERR_NODE'];

            unset($aryRetBody);
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITABASEH-STD-170003",array($rowOfConductor['CONDUCTOR_INSTANCE_NO'],$intFocusMovementSeq));
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////////////////////////////
            // (ここまで)現在のNODEの状態を取得する //
            ////////////////////////////////////////////////////////


                //////////////////////////////////////////////////////
                // (ここから)未実行の場合 STARTノード実行準備　//
                //////////////////////////////////////////////////////

                if( $intFocusMovementSeq == 0 ){
                    //---対象のCONDUCTORインスタンスのSTARTノード情報の取得
                    $aryTempForSql = array('WHERE'=>"CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO AND I_NODE_TYPE_ID IN ('1') AND DISUSE_FLAG IN ('0') ORDER BY NODE_INSTANCE_NO ASC");
                    
                    // 更新用のテーブル定義
                    $aryConfigForIUD = $aryConfigForMovInsIUD;
                    
                    // BIND用のベースソース
                    $aryBaseSourceForBind = $aryMovInsValueTmpl;
                    
                    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                          ,"SELECT FOR UPDATE"
                                                          ,"NODE_INSTANCE_NO"
                                                          ,"C_NODE_INSTANCE_MNG"
                                                          ,"C_NODE_INSTANCE_MNG_JNL"
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
                    
                    $aryUtnSqlBind['CONDUCTOR_INSTANCE_NO'] = $rowOfConductor['CONDUCTOR_INSTANCE_NO'];
                    $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
                    if( $aryRetBody[0] !== true ){
                        // 例外処理へ
                        $strErrStepIdInFx="00000900";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $objQueryUtn =& $aryRetBody[3];
                    
                    //----発見行だけループ
                    $aryStartNode = array();
                    while ( $row = $objQueryUtn->resultFetch() ){
                        $aryStartNode[] = $row;
                    }
                    //発見行だけループ----

                    unset($objQueryUtn);
                    unset($aryRetBody);
                    $arrTargetNodeInstance = $aryStartNode[0];
                    $arrTargetNodeClassInfo = $arrNodeClassInfo[$arrTargetNodeInstance['I_NODE_CLASS_NO']];

                    //対象のCONDUCTORインスタンスのSTARTノード情報の取得---
                    $arrOfFocusMovement[]=$arrTargetNodeInstance;
                }

                //////////////////////////////////////////////////////
                // (ここまで)) 未実行の場合//
                //////////////////////////////////////////////////////


                //////////////////////////////////////////////////////
                // (ここから)実行中の場合　//
                //////////////////////////////////////////////////////
                $intSymInsStatus = $rowOfConductor["STATUS_ID"] ;
                
                // BIND用のベースソース(Conductor)
                $arySymInsUpdateTgtSource = $rowOfConductor;
                $arySymInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                $intNextJobStopflg=0;
                $intErrMovcnt=0;
                $boolEndNodeWaitflg = false;
                //実行中のノード毎に繰り返し
                foreach ($arrOfFocusMovement as $Movementkey => $rowOfFocusMovement) {

                    //対象の実行中のNODEのインスタンスとクラス
                    if( $rowOfFocusMovement != ""){
                        $arrTargetNodeClassInfo = $arrNodeClassInfo[$rowOfFocusMovement['I_NODE_CLASS_NO']];

                        //---ノードインスタンス取得

                        $intTerminaltype = 2;
                        if( $rowOfFocusMovement['I_NODE_TYPE_ID'] == 2 )$intTerminaltype = 1;

                        $arySqlBind=array(
                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                            "NODE_CLASS_NO" => $rowOfFocusMovement['I_NODE_CLASS_NO'],
                            "TERMINAL_TYPE_ID" => $intTerminaltype,
                            );
                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                        $arrTargetNodeInstance = $aryRetBody[0];         
                    }


                    ///////////////////////////////////////////////////////////////////////////////////
                    /// (ここから)緊急停止が発令されていて、受理フラグがなければ、緊急停止を発令する //
                    ///////////////////////////////////////////////////////////////////////////////////
                    $status_id = $rowOfFocusMovement['STATUS_ID'];
                    if( $intFocusMovementSeq === 0 ){
                        //----まだ最初のムーブメントも始まっていない場合
                        $boolMovementFinedAfterHP = true;
                        //まだ最初のムーブメントも始まっていない場合----
                    }

                    if( $intFocusMovementSeq != 0 || $status_id == 1 ){
                        //----すでに1個はムーブメントがはじまった後である場合

                        $boolMovUpdateFlag = false;
                        $aryMovInsUpdateTgtSource = $rowOfFocusMovement;

                        //NodeTYPEがMovementの場合のみ
                        if( $rowOfFocusMovement['I_NODE_TYPE_ID'] == 3 ){    
                            //////////////
                            // 緊急停止 //
                            //////////////
                            //----緊急停止が発令されているか？されていて、受理フラグがなければ、緊急停止を発令する
                            if( $rowOfConductor['ABORT_EXECUTE_FLAG'] == '2' && $rowOfFocusMovement['ABORT_RECEPTED_FLAG'] == '1' && $rowOfFocusMovement['EXECUTION_NO'] != "" ){
                                //----緊急停止発令フラグが[発令済(2)]で、緊急停止受付確認フラグが[未確認(1)]なので、緊急停止を発令する
                                $aryRetBodyOfScram = $objOLA->srcamExecute($rowOfFocusMovement['I_ORCHESTRATOR_ID']
                                                                          ,$rowOfFocusMovement['EXECUTION_NO']
                                                                          ,$aryProperParameter);
                                
                                // 次に緊急停止を控えているシンフォニー(i)があることを前提として
                                // 可能な限り、次のシンフォニー(i)へ進める、というポリシー
                                $tmpBoolScramExecute = false;
                                if( $aryRetBodyOfScram[1] === null  || $aryRetBodyOfScram[1] === 701 ){
                                    // REST-APIへのリクエストがタイムアウトする等もあるので、広くOK、とするポリシー、をとる。
                                    $tmpBoolScramExecute = true;
                                    if( $aryRetBodyOfScram[0] === 0 ||  $aryRetBodyOfScram[0] === 701 ){
                                        $boolMovUpdateFlag = true;
                                        $aryMovInsUpdateTgtSource['ABORT_RECEPTED_FLAG'] = '2';
                                    }
                                }

                                if( $tmpBoolScramExecute === false ){
                                        $strErrStepIdInFx="00001050";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                //緊急停止発令フラグが[発令済(2)]で、緊急停止受付確認フラグが[未確認(1)]なので、緊急停止を発令する----
                            }
                            //緊急停止が発令されているか？されていて、受理フラグがなければ、緊急停止を発令する----
                        }else{

                            if( $rowOfConductor['ABORT_EXECUTE_FLAG'] == '2' ){
                                //呼び出し中のConductorインスタンスの緊急停止
                                if( $rowOfFocusMovement['I_NODE_TYPE_ID'] == 4 && $rowOfFocusMovement['CONDUCTOR_INSTANCE_CALL_NO'] != "" ){
                                  $tmpAryRetBody = $objOLA->getConductorStatusFromNode($aryMovement);
                                  foreach ($tmpAryRetBody as $key => $value) {
                                    foreach ($value['RUNS_NODE'] as $subkey => $subvalue) {
                                      $arySqlBind=array(
                                          "CONDUCTOR_INSTANCE_NO" => $subvalue['CONDUCTOR_INSTANCE_CALL_NO'],
                                          );
                                      $aryRetBody = getsubConductorInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                                      if(count($aryRetBody) > 0){
                                        $arySymInsCallUpdateTgtSource = $aryRetBody[0];
                                        
                                        $arySymInsCallUpdateTgtSource['ABORT_EXECUTE_FLAG'] = '2';
                                        $arySymInsCallUpdateTgtSource['TIME_START'] = str_replace("-","/",$arySymInsCallUpdateTgtSource['TIME_START']) ;
                                        $arySymInsCallUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForSymInsIUD;

                                        // BIND用のベースソース≒
                                        $aryBaseSourceForBind = $arySymInsCallUpdateTgtSource;
                                        
                                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001000";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }
                                      }
                                    }
                                  }
                                }
                                if( $rowOfFocusMovement['I_NODE_TYPE_ID'] == 10 && $rowOfFocusMovement['CONDUCTOR_INSTANCE_CALL_NO'] != "" ){
                                  $tmpAryRetBody = $objOLA->getConductorStatusFromNode($aryMovement);
                                  foreach ($tmpAryRetBody as $key => $value) {
                                    foreach ($value['RUNS_NODE'] as $subkey => $subvalue) {
                                      $arySqlBind=array(
                                          "SYMPHONY_INSTANCE_NO" => $subvalue['CONDUCTOR_INSTANCE_CALL_NO'],
                                          );
                                      $aryRetBody = getsubSymphonyInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                                      if(count($aryRetBody) > 0){
                                        $arySymInsCallUpdateTgtSource = $aryRetBody[0];

                                        $arySymInsCallUpdateTgtSource['ABORT_EXECUTE_FLAG']   = 2; //発令済
                                        $arySymInsCallUpdateTgtSource['LAST_UPDATE_USER']     = $g['login_id'];
                                        $arySymInsCallUpdateTgtSource['TIME_START'] = str_replace("-","/",$arySymInsCallUpdateTgtSource['TIME_START']) ;
                                        
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $arrayConfigForSymInsIUD;

                                        // BIND用のベースソース≒
                                        $aryBaseSourceForBind = $arySymInsCallUpdateTgtSource;
                                        
                                        $aryRetBody = updateSymphonyInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001000";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }
                                      }
                                    }
                                  }


                                }
                                
                                $tmpAryRetBody = $objOLA->getConductorStatusFromNode($aryMovement);
                                foreach ($tmpAryRetBody as $key => $value) {
                                  foreach ($value['RUNS_NODE'] as $subkey => $subvalue) {
                                    //callノード
                                    $subvalue['STATUS_ID'] = 7;     //緊急停止
                                    $subvalue['TIME_START'] = str_replace("-","/",$subvalue['TIME_START']) ;
                                    $subvalue['TIME_END'] = "DATETIMEAUTO(6)";  
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $subvalue;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001100";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                  }
                                }

                                $arySymInsUpdateTgtSource = $rowOfConductor;
                                $arySymInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                $arySymInsUpdateTgtSource['STATUS_ID']= 6; //準備エラー
                                $arySymInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$arySymInsUpdateTgtSource['TIME_START']) ;
                                
                                if( $status_id != 1 ){
                                    $arySymInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                }
                               
                                break; 
                            }


                            
                        }
                        //すでに1個はムーブメントがはじまった後である場合----
                    }

                    ///////////////////////////////////////////////////////////////////////////////////
                    /// (ここから)緊急停止が発令されていて、受理フラグがなければ、緊急停止を発令する //
                    ///////////////////////////////////////////////////////////////////////////////////


                    //ノードパターン別の動作

                    // BIND用のベースソース(Node)
                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                    
                    //保留中のノードがあれば保留ステータスを更新する
                    $currentPauseStatusId = "";
                    $sql = "SELECT PAUSE_STATUS_ID
                            FROM   C_CONDUCTOR_INSTANCE_MNG
                            WHERE  CONDUCTOR_INSTANCE_NO = {$arySymInsUpdateTgtSource['CONDUCTOR_INSTANCE_NO']}";

                    //SQL準備
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    while ( $row = $objQuery->resultFetch() ){
                        $currentPauseStatusId = $row['PAUSE_STATUS_ID'];
                    }
                    
                    $pauseFlg = false;
                    //保留中のノードがある場合
                    if( $aryMovInsUpdateTgtSource["I_NODE_TYPE_ID"] == '8' && $aryMovInsUpdateTgtSource["RELEASED_FLAG"] == '1' && $currentPauseStatusId == '2'){
                        $arySymInsUpdateTgtSource['PAUSE_STATUS_ID'] = 1;
                        // 更新用のテーブル定義
                        $aryConfigForIUD = $aryConfigForSymInsIUD;
                        // BIND用のベースソース
                        $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                    //保留中のノードがない場合
                    }else if( $aryMovInsUpdateTgtSource["I_NODE_TYPE_ID"] == '8' && $aryMovInsUpdateTgtSource["RELEASED_FLAG"] == '2' && $currentPauseStatusId == '1'){
                        foreach ($aryMovement as $key => $value) {
                          if($value["I_NODE_TYPE_ID"] == '8' && $value["RELEASED_FLAG"] == '1'){
                            if($value["STATUS_ID"] != 14){
                              $pauseFlg = true;
                            }
                          }
                        }
                        if($pauseFlg === false){
                          $arySymInsUpdateTgtSource['PAUSE_STATUS_ID'] = 2;
                          // 更新用のテーブル定義
                          $aryConfigForIUD = $aryConfigForSymInsIUD;
                          // BIND用のベースソース
                          $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                          $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                        }
                    }

                    //ConductorCallがある場合
                    if($rowOfFocusMovement['CONDUCTOR_INSTANCE_CALL_NO'] != ''){
                      $aryConductorCallNo = array();
                      $sql = "SELECT CONDUCTOR_INSTANCE_CALL_NO
                              FROM   C_NODE_INSTANCE_MNG
                              WHERE  CONDUCTOR_INSTANCE_NO = {$rowOfFocusMovement['CONDUCTOR_INSTANCE_NO']}";

                      //SQL準備
                      $objQuery = $objDBCA->sqlPrepare($sql);
                      $r = $objQuery->sqlExecute();
                      while ( $row = $objQuery->resultFetch() ){
                        if($row['CONDUCTOR_INSTANCE_CALL_NO'] != ''){
                          $aryConductorCallNo[] = $row['CONDUCTOR_INSTANCE_CALL_NO'];
                        }
                      }

                      //呼び出し先のConductorのPAUSE_STATUS_ID取得
                      $aryCurrentCallConductorPauseStatusId = array();
                      foreach ($aryConductorCallNo as $key => $value) {
                        $sql = "SELECT PAUSE_STATUS_ID
                                FROM   C_CONDUCTOR_INSTANCE_MNG
                                WHERE  CONDUCTOR_INSTANCE_NO = {$value}";

                        //SQL準備
                        $objQuery = $objDBCA->sqlPrepare($sql);
                        $r = $objQuery->sqlExecute();
                        while ( $row = $objQuery->resultFetch() ){
                            $aryCurrentCallConductorPauseStatusId[] = $row['PAUSE_STATUS_ID'];
                        }
                      }
                      
                      //呼び出し元のConductorのPAUSE_STATUS_ID取得
                      $currentPauseStatusId = '';
                      $sql = "SELECT PAUSE_STATUS_ID
                              FROM   C_CONDUCTOR_INSTANCE_MNG
                              WHERE  CONDUCTOR_INSTANCE_NO = {$arySymInsUpdateTgtSource["CONDUCTOR_INSTANCE_NO"]}";

                      //SQL準備
                      $objQuery = $objDBCA->sqlPrepare($sql);
                      $r = $objQuery->sqlExecute();
                      while ( $row = $objQuery->resultFetch() ){
                          $currentPauseStatusId = $row['PAUSE_STATUS_ID'];
                      }
                      
                      if($currentPauseStatusId == '2' && in_array('1', $aryCurrentCallConductorPauseStatusId)){
                        $arySymInsUpdateTgtSource['PAUSE_STATUS_ID'] = 1;
                        // 更新用のテーブル定義
                        $aryConfigForIUD = $aryConfigForSymInsIUD;
                        // BIND用のベースソース
                        $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                      }elseif ($currentPauseStatusId == '1' && in_array('1', $aryCurrentCallConductorPauseStatusId) === false) {
                        $arySymInsUpdateTgtSource['PAUSE_STATUS_ID'] = 2;
                        // 更新用のテーブル定義
                        $aryConfigForIUD = $aryConfigForSymInsIUD;
                        // BIND用のベースソース
                        $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                      }
                    }

                    $boolNextNodeReadyflg = false;
                    $conditionalflg="";
                    $boolEndNodeWaitflg = false;
                    switch( $aryMovInsUpdateTgtSource["I_NODE_TYPE_ID"] ){
                        case "1":  #start
                            //---STARTノードを正常終了へ
                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";  
                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                            
                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00001100";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }

                            // STARTノードを正常終了へ---

                            //---STARTのノードの次のノードを取得
                            //全ノードの取得、（TERMINAL-OUT）
                            $aryRetBody = $objOLA->getInfoOfOneNodeTerminal($rowOfConductor['I_CONDUCTOR_CLASS_NO'], 0,0,1,2);#TERMINALあり
                            //整形
                            $arrNodeClassInfo=array();
                            foreach ( $aryRetBody[4] as $key => $value) {
                                    $arrNodeClassInfo[$value['NODE_CLASS_NO']]=$value; 
                            }

                            //START接続先のノード名取得                
                            $strNextTargetNode = $arrNodeClassInfo[$arrTargetNodeInstance['I_NODE_CLASS_NO']]['TERMINAL'][0]['CONNECTED_NODE_NAME'];

                            //次のノードのクラス取得
                            $arrTargetNodeClassID="";
                            foreach ( $arrNodeClassInfo as $key => $value) {
                                if( $value['NODE_NAME'] == $strNextTargetNode )$arrTargetNodeClassInfo=$value; 
                            }

                            //---ノードインスタンス取得
                            $arySqlBind=array(
                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                "TERMINAL_TYPE_ID" => 2, //out
                                );
                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                            $arrTargetNodeInstance = $aryRetBody[0];

                            //STARTのノードの次のノードを取得---

                            //---対象のCONDUCTORインスタンスのSTART後のノードを準備中へ
                            $aryMovInsUpdateTgtSourceStart=$arrTargetNodeInstance;
                            $aryMovInsUpdateTgtSourceStart['STATUS_ID'] = 2;     //準備中
                            $aryMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                            $aryMovInsUpdateTgtSourceStart['TIME_START'] = "DATETIMEAUTO(6)"; 
                            
                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSourceStart;
                            
                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00001101";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                        
                            // 対象のCONDUCTORインスタンスのSTART後のノードを準備中へ---
                            $arySymInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                            $arySymInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中へ
                            break;

                        case "2":  #end    

                            $aryTempForSql = array('WHERE'=>"CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO AND I_NODE_TYPE_ID = :I_NODE_TYPE_ID AND DISUSE_FLAG IN ('0') ORDER BY NODE_INSTANCE_NO ASC");
                            
                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;
                            
                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsValueTmpl;

                            $arySqlBind = array( 
                                                'CONDUCTOR_INSTANCE_NO' => $rowOfConductor['CONDUCTOR_INSTANCE_NO'] 
                                                ,'I_NODE_TYPE_ID' => 2 
                                                );

                            $aryMovement =  getNodeInstanceInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);


                            //---endノードを正常終了へ
                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)"; 
                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 

                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00001200";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }

                            //全ENDノードが終了、SKIP完了時のみConductor終了
                            $boolEndNodeWaitflg = true;
                            foreach ($aryMovement as $key => $value) {
                                //対象のENDノード以外のENDノードが終了、SKIP済みの場合
                                if( $value['NODE_INSTANCE_NO'] != $aryMovInsUpdateTgtSource['NODE_INSTANCE_NO'] ){
                                    if( $value['STATUS_ID'] == 14 || $value['STATUS_ID'] == 9 ){
                                    }else{
                                        $boolEndNodeWaitflg=false;
                                    }


                                }
                                }
                            if(  $boolEndNodeWaitflg == true){
                                //endノードを正常終了へ---
                                //Conductorインスタンスのステータスを実行中へ
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 5;     //正常終了
                                $arySymInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";                                 
                            } 

                            break;

                        case "3":  #movement

                            $boolNodeStatusUpdateReadyflg = true;

                            //実行
                                if( $arrTargetNodeInstance['EXE_SKIP_FLAG'] == '2' ){
                                    //----Skipフラグが立っていた場合
                                    if( $arrTargetNodeInstance['STATUS_ID'] == '2' ){
                                        $aryMovInsUpdateTgtSource['STATUS_ID']  = '12'; //Skip完了[実行完了(同位)]
                                        $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)"; 
                                    }elseif( $arrTargetNodeInstance['STATUS_ID'] == '12' ){
                                        $aryMovInsUpdateTgtSource['STATUS_ID'] = '14'; //Skip終了
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        $aryMovInsUpdateTgtSource['TIME_END']  = "DATETIMEAUTO(6)"; 
                                    }

                                    $boolNodeStatusUpdateReadyflg = true;
                                    $boolNextNodeReadyflg = true;
                                    
                                    //Skipフラグが立っていた場合----
                                }else{
                                    //EXECUTION_NO未発行の場合
                                    if( $arrTargetNodeInstance['EXECUTION_NO'] == '' ){
                                        //----オーケストレータ側のシーケンスをロックする
                                        $aryRetBody = $objOLA->sequencesLockInTrz($arrTargetNodeInstance['I_ORCHESTRATOR_ID']);
                                        if( $aryRetBody[1] !== null ){
                                            //----オーケストレータ側の、シーケンスをロックできなかった
                                            
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001300";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                            
                                            //オーケストレータ側の、シーケンスをロックできなかった----
                                        }
                                        unset($aryRetBody);

                                        //個別オペレーション指定時
                                        if( 0 < strlen($arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK']) ){
                                            $tmpVarOperationNoUAPK = $arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK'];
                                        }else{
                                            $tmpVarOperationNoUAPK = $rowOfConductor['OPERATION_NO_UAPK'];
                                        }
                                        $aryRetBody = $objOLA->registerExecuteNo($arrTargetNodeInstance['I_ORCHESTRATOR_ID']
                                                                                ,$arrTargetNodeInstance['I_PATTERN_ID']
                                                                                ,$tmpVarOperationNoUAPK
                                                                                ,""
                                                                                ,true);
                                        unset($tmpVarOperationNoUAPK);

                                        if( $aryRetBody[1] !== null ){
                                            //----オーケストレータ側に、レコードを挿入できなかった
                                            $aryMovInsUpdateTgtSource['STATUS_ID'] = '10'; //準備エラー
                                            //オーケストレータ側に、レコードを挿入できなかった----

                                            //Conductorインスタンスをエラーへ
                                            $arySymInsUpdateTgtSource['STATUS_ID'] = '7'; //想定外エラー

                                        }else{
                                            //----オーケストレータ側に、レコードを挿入できた
                                            $aryMovInsUpdateTgtSource['STATUS_ID']    = '2'; //準備中
                                            $aryMovInsUpdateTgtSource['EXECUTION_NO'] = $aryRetBody[0];

                                            //オーケストレータ側に、レコードを挿入できた----

                                        }
                                        $boolNodeStatusUpdateReadyflg = true;
                                    }else{
                                        $aryMovInsUpdateParm = getMovementStatus($objOLA,$aryMovInsUpdateTgtSource);
                                        //下位オーケストレータ、ステータス問い合わせ
                                        if( $arrTargetNodeInstance['STATUS_ID'] != $aryMovInsUpdateParm['STATUS_ID'] ){
                                            $boolNodeStatusUpdateReadyflg = true;

                                            //問い合わせ結果反映
                                            foreach ($aryMovInsUpdateParm as $key => $value) {
                                                $aryMovInsUpdateTgtSource[$key] =  $value;
                                            } 
                                        }
                                    }
                                }

                                if( $boolNodeStatusUpdateReadyflg == true){
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['TIME_END']="";
                                    if(  array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(6,7,9,10,11,14) ) !== false ){
                                        $aryMovInsUpdateTgtSource['TIME_END']  = "DATETIMEAUTO(6)";
                                    }

                                    if( $arrTargetNodeInstance['STATUS_ID'] == 2 || ( $arrTargetNodeInstance['STATUS_ID'] >= 3 && $arrTargetNodeInstance['STATUS_ID'] != $aryMovInsUpdateTgtSource['STATUS_ID'] ) ){

                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001301";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                    }

                                    //次のNode取得
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 2, //out
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                    //クラスの取得
                                    $arrNextTargetNodeClass=array();
                                    foreach ($aryRetBody as $key => $value) {
                                        foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                            if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                $arrNextTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                            }
                                        }
                                    }

                                    $conditionalflg="";
                                    foreach ($arrNextTargetNodeClass as $key => $nclass) {
                                        if($nclass['NODE_TYPE_ID'] == 6 ){
                                            $conditionalflg="1";
                                        }
                                    }



                                    //次のNodeがcondition
                                    if($conditionalflg == 1 ){
                                        //後続処理conditional用
                                        switch( $aryMovInsUpdateTgtSource["STATUS_ID"] ){
                                            case "10":  //準備エラー
                                            case "6":  //異常終了
                                            case "7":  //緊急停止
                                            case "11":  //想定外エラー    
                                            case "9":  //正常終了
                                            case "12":  //Skip完了
                                            case "14":  //Skip終了
                                                $boolNextNodeReadyflg = true;
                                                break;
                                            default:
                                                break;
                                        }
                                    }else{
                                        //次のNodeがcondition以外
                                        $boolNextNodeReadyflg = false;
                                        //---NODE-Conductorインスタンスのステータス同期
                                        switch( $aryMovInsUpdateTgtSource["STATUS_ID"] ){
                                            case "9": // mov.正常終了
                                            case "14": //mov.Skip終了
                                                $boolNextNodeReadyflg = true;
                                                break;
                                            case "4": // mov.実行中(遅延)
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 4;     //.実行中(遅延)へ
                                                break;
                                            case "10": // mov.準備エラー
                                            case "6": // mov.異常終了
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了へ
                                                break;
                                            case "7": // mov.緊急停止
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 6;     //緊急停止へ
                                                break;
                                            case "11": // mov.想定外エラー
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 8;     //想定外エラー
                                                break;
                                            default:
                                                break;
                                        } 
                                        //NODE-Conductorインスタンスのステータス同期---
                                    }
                                }
                                    
                            break;

                        case "4":  #call

                            if( $arrTargetNodeInstance['EXE_SKIP_FLAG'] == '2' ){
                                //----Skipフラグが立っていた場合
                                if( $arrTargetNodeInstance['STATUS_ID'] == '2' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID']  = '12'; //Skip完了[実行完了(同位)]
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                }elseif( $arrTargetNodeInstance['STATUS_ID'] == '12' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '14'; //Skip終了
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['TIME_END']  = "DATETIMEAUTO(6)";
                                    $boolNextNodeReadyflg = true;
                                }

                                $boolNodeStatusUpdateReadyflg = true;

                                //---CALLノードを実行中へ

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001400";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                                //Skipフラグが立っていた場合----
                            }else{
                                //---呼び出しConductorインスタンスのステータスを更新
                                if( $arrTargetNodeInstance['STATUS_ID'] == '2' ){

                                    $intSubSymcallInsNo="";

                                    if( $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] == ""){

                                        $lc_db_model_ch = $objDBCA->getModelChannel();
                                        $intShmphonyClassId=$arrTargetNodeInstance['CONDUCTOR_CALL_CLASS_NO'];#
                                        $intOperationNoUAPK=$rowOfConductor['OPERATION_NO_UAPK'];
                                        //個別オペレーション指定時
                                        if( $arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK'] != "" ){
                                            $intOperationNoUAPK=$arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK'];
                                        }

                                        $aryRetBody = $objOLA->getInfoOfOneConductor($intShmphonyClassId,0,1);

                                        if( $aryRetBody[1] !== null ){
                                            // エラーフラグをON
                                            // 例外処理へ
                                            $strErrMsg = $aryRetBody[4];
                                            $strErrStepIdInFx="00000100";


                                            //---CALLノードを異常終了へ
                                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";

                                            // 更新用のテーブル定義
                                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                                            // BIND用のベースソース
                                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                            
                                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                            
                                            if( $aryRetBody !== true  ){
                                                // 例外処理へ
                                                $strErrStepIdInFx="00001403";
                                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                            }


                                            break;
                                        }

                                        //Conductorクラス取得
                                        $arrayResult = $objOLA->convertConductorClassJson($intShmphonyClassId,1);
                                       
                                        if( $arrayResult[0] == "1" ){

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

                                            $g['objMTS'] = $objMTS;
                                            $g['objDBCA'] = $objDBCA;
                                            $g['root_dir_path'] = $root_dir_path;
                                            //Conductorクラス状態保存
                                            $arrayResult = $objOLA->conductorClassRegister(null, $arrayReceptData, $strSortedData, null);

                                            if( $arrayResult[0] == "000" ){
                                                $intShmphonyClassId = $arrayResult[2];
                                            }else{
                                                //クラス登録不正時
                                                //CALLノードを異常終了へ
                                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 11;     //想定外エラー
                                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";

                                                // 更新用のテーブル定義
                                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                                // BIND用のベースソース
                                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                                
                                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                                
                                                break;
                                            }
                                            
                                            // Conductorクラス状態保存 ---

                                            $userId=$db_access_user_id;
                                            $userName = $objMTS->getSomeMessage("ITABASEH-STD-170001");
                                            $intCallNo=$rowOfConductor['CONDUCTOR_INSTANCE_NO'];

                                            // 個別OP対応 #750
                                            $retArray = $objOLA->registerInstanceConductorNode($objDBCA, $lc_db_model_ch, $objMTS, $intShmphonyClassId, $intOperationNoUAPK, "", "", $strSortedData, $userId, $userName,$intCallNo);

                                            if($retArray[0] == false){
                                                //---CALLノードを異常終了へ
                                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                                $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                                
                                                if( isset( $retArray[5] ) )$intSubSymcallInsNo = $retArray[5];   
                                                if ( $intSubSymcallInsNo != "")$aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] =  $intSubSymcallInsNo;

                                                // 更新用のテーブル定義
                                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                                // BIND用のベースソース
                                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                                
                                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                                
                                                //呼び出し先を異常終了へ
                                                $arySqlBind=array(
                                                    "CONDUCTOR_INSTANCE_NO" => $retArray['5'],
                                                    );   
                                                $aryRetBody = getsubConductorInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                                                $arySymInsCallUpdateTgtSource = $aryRetBody[0];

                                                $arySymInsCallUpdateTgtSource['STATUS_ID'] = 7;     //異常終了

                                                //　#749対応
                                                if( $retArray[6] != "" ){
                                                    $arrexecLogMessage = explode( PHP_EOL, $retArray[6]);
                                                    foreach ($arrexecLogMessage as $tmpvalmsg ) {
                                                        if( $tmpvalmsg != ""){
                                                            $execLogMessage = "[ERROR] " . $tmpvalmsg;
                                                            //サブConductor（CALL先）のインスタンスへのログ登録
                                                            $arySymInsCallUpdateTgtSource['EXEC_LOG'] = $arySymInsCallUpdateTgtSource['EXEC_LOG'] . $execLogMessage .PHP_EOL ;

                                                            //Conductor（CALL元）インスタンスへのログ登録
                                                            $intNodeInsNo = $arrTargetNodeInstance['NODE_INSTANCE_NO'];
                                                            //[ERROR]Movementに作業対象ホストが登録されていません。(MovementID:X) [ Node instance ID :XXXX ]
                                                            $tmpexecLogMessage = $objMTS->getSomeMessage("ITABASEH-STD-170004",array($execLogMessage,$intNodeInsNo) );
                                                            $arySymInsUpdateTgtSource['EXEC_LOG'] = $arySymInsUpdateTgtSource['EXEC_LOG'] . $tmpexecLogMessage .PHP_EOL ;                                                            
                                                        }
                                                    }
                                                }

                                                // 更新用のテーブル定義
                                                $aryConfigForIUD = $aryConfigForSymInsIUD;

                                                // BIND用のベースソース≒
                                                $aryBaseSourceForBind = $arySymInsCallUpdateTgtSource;
                                                
                                                $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                                //次のNode取得
                                                $arySqlBind=array(
                                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                                    "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                                    "TERMINAL_TYPE_ID" => 2, //out
                                                    );
                                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                                //クラスの取得
                                                $arrParallelTargetNodeClass=array();
                                                foreach ($aryRetBody as $key => $value) {
                                                    foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                        if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                            $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                        }
                                                    }
                                                }

                                                $conditionalflg="";
                                                foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                                    //次のNodeがconditionの場合
                                                    if($nclass['NODE_TYPE_ID'] == 6 ){
                                                        $conditionalflg="1";
                                                    }
                                                }
                                                //次のNodeがcondition以外
                                                if($conditionalflg != 1 ){
                                                        //Conductorインスタンスのステータスを異常終了へ
                                                        $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了
                                                        break;    
                                                }else{
                                                    $boolNextNodeReadyflg = true;
           
                                                    break;

                                                }
                                            }
                                            $intSubSymcallInsNo = $retArray[5];
                                            //---ノードインスタンス取得
                                            $arySqlBind=array(
                                                "CONDUCTOR_INSTANCE_NO" =>  $intSubSymcallInsNo,
                                                );

                                        }

                                    }else{
                                        //---ノードインスタンス取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'],
                                            );                                    
                                    }

                                    // 呼び出しConductorインスタンスのステータスを実行中へ---

                                
                                    //---CALLノードを実行中へ
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中
                                    $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                    if ( $intSubSymcallInsNo != "")$aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] =  $intSubSymcallInsNo;

                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                    
                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001403";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                    // CALLノードを実行中へ---
                                    


                                    //Conductorインスタンスのステータスを実行中へ
                                    $arySymInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中へ
                                }else{

                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'],
                                        );
                                    $aryRetBody = getsubConductorInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arySymInsCallUpdateTgtSource = $aryRetBody[0];

                                    if( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 5 || $arySymInsCallUpdateTgtSource['STATUS_ID'] == 11 ){
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '9'; //正常終了

                                        //Call先、警告終了時
                                        if( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 11 ){
                                            $aryMovInsUpdateTgtSource['STATUS_ID']    = '15'; //警告終了
                                        }

                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $boolNextNodeReadyflg = true;
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        $boolNextNodeReadyflg = true;

                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 7 ){

                                        //Nodeインスタンスのステータスを異常終了へ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '6'; //異常終了
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを異常終了へ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了
                                                break;    
                                        }
                                        
                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 6 ){

                                        //Nodeインスタンスのステータスを異常終了へ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '7'; //緊急停止
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを異常終了へ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 6;     //緊急停止
                                                break;    
                                        }
                                        
                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 8 ){

                                        //Nodeインスタンスのステータスを想定外エラーへ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '11'; //想定外エラー
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを想定外エラーへ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 8;     //想定外エラー
                                                break;    
                                        }
                                        
                                    }

                                    //後続処理conditional用
                                    switch( $aryMovInsUpdateTgtSource["STATUS_ID"] ){
                                        case "10":  //準備エラー
                                        case "6":  //異常終了
                                        case "7":  //緊急停止
                                        case "11":  //想定外エラー
                                        case "12":  //Skip完了
                                        case "9":  //正常終了
                                        case "14":  //Skip終了
                                        case "15":  //警告終了
                                            $boolNextNodeReadyflg = true;
                                            break;
                                        default:
                                            break;
                                    }


                                }
                                //呼び出しConductorインスタンスのステータスを更新---

                            }


                            break;  

                        case "5":  #parallel-branch 

                            //---parallelノードを正常終了へ
                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                            
                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00001500";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }

                            // parallelノードを正常終了へ---

                            //---parallel接続先のNODEインスタンスを準備中へ
                            
                            //parallel接続先のNODEインスタンスの取得
                            $arySqlBind=array(
                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                "TERMINAL_TYPE_ID" => 2, //out
                                );
                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                            //クラスの取得
                            $arrParallelTargetNodeClass=array();
                            foreach ($aryRetBody as $key => $value) {
                                foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                        $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2; 
                                           
                                    }
                                }
                            }
                            //インスタンスの取得
                            $arrParallelTargetNodeInstance = array();
                            foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                $arySqlBind=array(
                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                    "NODE_CLASS_NO" => $nclass["NODE_CLASS_NO"],
                                    "TERMINAL_TYPE_ID" => 2, //out
                                    );
                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                $arrParallelTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']] = $aryRetBody[0];

                            }

                            //---parallel接続先のNODEインスタンスを準備中へ
                            foreach ($arrParallelTargetNodeInstance as $key => $arrTargetNodeInstance) {

                                $aryMovInsUpdateTgtSourceStart=$arrTargetNodeInstance;
                                $aryMovInsUpdateTgtSourceStart['STATUS_ID'] = 2;     //準備中
                                $aryMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                                $aryMovInsUpdateTgtSourceStart['TIME_START'] = "DATETIMEAUTO(6)";
                                
                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSourceStart;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001501";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                                
                            }
                            $arySymInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中へ

                            //parallel接続先のNODEインスタンスを準備中へ---

                            break;                    

                        case "6":  #conditional-branch

                            //---conditionalノードステータス更新
                            if(  $arrTargetNodeInstance['STATUS_ID'] == '2'  ){
                                //mergeノードを準備中へ

                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中
                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)"; 

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001600";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                            }else{

                                //---conditionalノードのステータスを正常終了へ
                                //conditional接続先のノードインスタンスの取得
                                $arySqlBind=array(
                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                    "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                    "TERMINAL_TYPE_ID" => 1, //in
                                    );
                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);


                                //ノードクラスの取得
                                $arrConditionalTargetNodeClass=array();
                                foreach ($aryRetBody as $key => $value) {
                                    foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                        if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                            $arrConditionalTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2; 
                                               
                                        }
                                    }
                                }
                                //ノードインスタンスの取得
                                $arrConditionalTargetNodeInstance = array();
                                foreach ($arrConditionalTargetNodeClass as $key => $nclass) {
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $nclass["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 2, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arrConditionalTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']] = $aryRetBody[0];

                                }
                                $strConditionalTargetStatusID = $arrConditionalTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']]['STATUS_ID'];

                                //次ののノードを準備中へ

                                $strConditionalTargetNodeInsID = $aryRetBody[0]['NODE_INSTANCE_NO'];
                                //#838 対応
                                $intconditionalStopflg = 0;
                                if( count($arrOfErrMovement) != 0 ){
                                    foreach ($arrOfErrMovement as $tmparrOfErrMovement) {
                                       if( $tmparrOfErrMovement['NODE_INSTANCE_NO'] != $strConditionalTargetNodeInsID ){
                                            $intconditionalStopflg = 1;

                                            //次のNode取得
                                            $arySqlBind=array(
                                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                                "NODE_CLASS_NO" => $tmparrOfErrMovement['I_NODE_CLASS_NO'],
                                                "TERMINAL_TYPE_ID" => 2, //out
                                                );
                                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                            //クラスの取得
                                            $arrNextTargetNodeClass=array();
                                            foreach ($aryRetBody as  $value) {
                                                foreach ( $arrNodeClassInfo as $value2) {
                                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                        $arrNextTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                    }
                                                }
                                            }
                                            
                                            foreach ($arrNextTargetNodeClass as  $nclass) {
                                                if($nclass['NODE_TYPE_ID'] == 6 ){
                                                    $intconditionalStopflg = 0;
                                                }
                                            }    
                                       }
                                    }
                                }

                                if( $intconditionalStopflg != 1 ){
                                    //---conditionalノードのステータスを正常終了へ
                                    //conditional接続先のノードインスタンスの取得
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 2, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                   
                                    $arrConditionalTargetStatusID=array();
                                    foreach ($aryRetBody as $key => $value) {

                                        $arrConditionalID = explode(',', $value['CONDITIONAL_ID']);

                                        if( in_array($strConditionalTargetStatusID, $arrConditionalID) ){
                                            $arrConditionalTargetStatusID[$value['CONDITIONAL_ID']] = $value['CONNECTED_NODE_NAME'];
                                            $strConditionalTargetStatusID = $value['CONDITIONAL_ID'];
                                        }else{
                                            $arrConditionalTargetStatusID[$value['CONDITIONAL_ID']] = $value['CONNECTED_NODE_NAME'];
                                        }
                                    }
                                    if( isset( $arrConditionalTargetStatusID[$strConditionalTargetStatusID] ) ) {
                                        $strNextTargetNode=$arrConditionalTargetStatusID[$strConditionalTargetStatusID];
                                    }else{
                                        $strNextTargetNode=$arrConditionalTargetStatusID["9999"];
                                    }
                                    $arrNextNodeList=array();
                                    foreach ($arrConditionalTargetStatusID as $key => $value) {
                                        $arrNextNodeList[$value]=$value;
                                    }
                                    unset($arrNextNodeList[$strNextTargetNode]);

                                    //全ノードの取得、（TERMINAL-OUT）
                                    $aryRetBody = $objOLA->getInfoOfOneNodeTerminal($rowOfConductor['I_CONDUCTOR_CLASS_NO'], 0,0,1,2);#TERMINALあり
                                    //整形
                                    $arrNodeClassInfo=array();
                                    foreach ( $aryRetBody[4] as $key => $value) {
                                            $arrNodeClassInfo[$value['NODE_CLASS_NO']]=$value; 
                                    }

                                    //不使用Case先をSKIP完了へ
                                    $aryRetBody = callconditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$arrNextNodeList);


                                    //次のノードを処理
                                    //次のノードのクラス取得
                                    $arrTargetNodeClassID="";
                                    foreach ( $arrNodeClassInfo as $key => $value) {
                                        if( $value['NODE_NAME'] == $strNextTargetNode )$arrTargetNodeClassInfo=$value; 
                                    }

                                    //次のノードインスタンス取得
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 1, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arrNextTargetNodeInstance = $aryRetBody[0];

                                    $aryMovInsUpdateTgtSourceStart=$arrNextTargetNodeInstance;
                                    $aryMovInsUpdateTgtSourceStart['STATUS_ID'] = 2;     //準備中
                                    $aryMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                                    $aryMovInsUpdateTgtSourceStart['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSourceStart['TIME_START']) ;
                                    $aryMovInsUpdateTgtSourceStart['TIME_END'] = str_replace("-","/",$aryMovInsUpdateTgtSourceStart['TIME_END']) ;

                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSourceStart;
                                    
                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001601";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }

                                    //mergeノードのステータス更新
                                
                                    //---mergeノードを正常終了へ
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001602";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                }else{
                                    //---mergeノードを正常終了へ
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001602";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                }

                            
                            //conditionalノードステータス更新---

                            }
                            break;

                        case "7":  #merge


                            //---mergeノードのステータスを更新へ
                            //merge接続先のノードインスタンスの取得
                            $arySqlBind=array(
                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                "TERMINAL_TYPE_ID" => 1, //out
                                );
                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                        
                            //ノードクラスの取得
                            $arrParallelTargetNodeClass=array();
                            foreach ($aryRetBody as $key => $value) {
                                foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                        $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2; 
                                           
                                    }
                                }
                            }
                            //ノードインスタンスの取得
                            $arrParallelTargetNodeInstance = array();
                            foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                $arySqlBind=array(
                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                    "NODE_CLASS_NO" => $nclass["NODE_CLASS_NO"],
                                    "TERMINAL_TYPE_ID" => 2, //in
                                    );
                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                $arrParallelTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']] = $aryRetBody[0];

                            }
                        
                            //merge接続元のノードインスタンスのステータス確認
                            $boolNextNodeReadyflg = true;
                            $boolMergeNodeErrEndflg = false;
                            foreach ($arrParallelTargetNodeInstance as $key => $arrParallelTarget) {
                                //parallel接続先のNODEインスタンスのステータス確認

                                switch( $arrParallelTarget['STATUS_ID'] ){
                                    case "1":  //未実行
                                    case "2":  //準備中
                                    case "3":  //実行中
                                    case "4":  //実行中(遅延)
                                    case "5":   //実行完了
                                    case "8":  //保留中
                                    case "10":  //準備エラー
                                    case "6":  //異常終了
                                    case "7":  //緊急停止
                                    case "11":  //想定外エラー
                                    case "12":  //Skip完了
                                    case "13":  //Skip後保留中
                                        $boolNextNodeReadyflg = false;
                                        break;
                                    case "9":  //正常終了
                                    case "14":  //Skip終了
                                    case "15":  //警告終了
                                        break;

                                    default: // 返し値として存在してはいけない値だった場合
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001701";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        break;
                                }

                                //parallel接続先が異常系時のステータス更新フラグ設定 #838 対応
                                switch( $arrParallelTarget['STATUS_ID'] ){
                                    case "10":  //準備エラー
                                    case "6":  //異常終了
                                    case "7":  //緊急停止
                                    case "11":  //想定外エラー
                                        $boolMergeNodeErrEndflg = true;
                                        break;
                                    case "1":  //未実行
                                    case "2":  //準備中
                                    case "3":  //実行中
                                    case "4":  //実行中(遅延)
                                    case "5":   //実行完了
                                    case "8":  //保留中
                                    case "12":  //Skip完了
                                    case "13":  //Skip後保留中
                                    case "9":  //正常終了
                                    case "14":  //Skip終了
                                    case "15":  //警告終了
                                        break;

                                    default: // 返し値として存在してはいけない値だった場合
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001701";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        break;
                                }
                            }

                            //---mergeノードステータス更新
                            if(  $arrTargetNodeInstance['STATUS_ID'] == '2' && $boolMergeNodeErrEndflg === false ){
                                //mergeノードを準備中へ
                                $boolNextNodeReadyflg = false;
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中
                                $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001700";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                            }else{
                                //mergeノードのステータスを更新
                            
                                if( $boolNextNodeReadyflg === true  ){
                                    //---mergeノードを正常終了へ
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001702";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }

                                }elseif( $boolMergeNodeErrEndflg === true  ){
                                    //---mergeノードを異常終了へ #838 対応
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001702";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }

                                }
                            
                            //mergeノードのステータスを更新---

                            }



                            break;

                        case "8":  #pause             
                            //---pauseノードをステータスを更新
                            if( $aryMovInsUpdateTgtSource['STATUS_ID'] == 2 ){
                            
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 8;     //保留中
                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001800";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }elseif( $aryMovInsUpdateTgtSource["RELEASED_FLAG"] != 1 ){
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                                $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001801";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                                $boolNextNodeReadyflg = true;
                            }
                             //pauseノードをステータスを更新---

                            break;
                        case "9":  #blank
                            
                            //---blankノードを正常終了へ
                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                            // 更新用のテーブル定義
                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                            
                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00001900";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            $boolNextNodeReadyflg = true;
                            // blankノードを正常終了へ---
                            break;

                        case "10":  #call-s

                            if( $arrTargetNodeInstance['EXE_SKIP_FLAG'] == '2' ){
                                //----Skipフラグが立っていた場合
                                if( $arrTargetNodeInstance['STATUS_ID'] == '2' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID']  = '12'; //Skip完了[実行完了(同位)]
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                }elseif( $arrTargetNodeInstance['STATUS_ID'] == '12' ){
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = '14'; //Skip終了
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['TIME_END']  = "DATETIMEAUTO(6)";
                                    $boolNextNodeReadyflg = true;

                                }

                                $boolNodeStatusUpdateReadyflg = true;
                               
                                //---CALLノードを実行中へ

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001400";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                                //Skipフラグが立っていた場合----
                            }else{
                                //---symphony呼び出しConductorインスタンスのステータスを更新
                                if( $arrTargetNodeInstance['STATUS_ID'] == '2' ){

                                    $intSubSymcallInsNo="";

                                    if( $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] == ""){

                                        $lc_db_model_ch = $objDBCA->getModelChannel();
                                        $intShmphonyClassId=$arrTargetNodeInstance['CONDUCTOR_CALL_CLASS_NO'];#
                                        $intOperationNoUAPK=$rowOfConductor['OPERATION_NO_UAPK'];
                                        //個別オペレーション指定時
                                        if( $arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK'] != "" ){
                                            $intOperationNoUAPK=$arrTargetNodeInstance['OVRD_OPERATION_NO_UAPK'];
                                        }

                                        $aryRetBody = $objOLA->getInfoFromOneOfSymphonyClasses($intShmphonyClassId,1);

                                        if( $aryRetBody[1] !== null ){
                                            // エラーフラグをON
                                            // 例外処理へ
                                            $strErrMsg = $aryRetBody[4];
                                            $strErrStepIdInFx="00000100";

                                            //---CALLノードを異常終了へ
                                            $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                            $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                            $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";

                                            // 更新用のテーブル定義
                                            $aryConfigForIUD = $aryConfigForMovInsIUD;

                                            // BIND用のベースソース
                                            $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                            
                                            $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                            
                                            if( $aryRetBody !== true  ){
                                                // 例外処理へ
                                                $strErrStepIdInFx="00001403";
                                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                            }


                                            break;
                                        }else{

                                            $g['objMTS'] = $objMTS;
                                            $g['objDBCA'] = $objDBCA;
                                            #$g['root_dir_path'] = $root_dir_path;

                                            $userId=$db_access_user_id;
                                            $userName = $objMTS->getSomeMessage("ITABASEH-STD-170001");
                                            $intCallNo=$rowOfConductor['CONDUCTOR_INSTANCE_NO'];
                                            
                                            // Movementクラスの不要な項目削除 #750
                                            $aryOptionOrder=array();
                                            foreach ($aryRetBody[5] as $tmpkey => $movInfo) {
                                                $aryOptionOrder[]=array(
                                                                "MOVEMENT_SEQ" => $movInfo["MOVEMENT_SEQ"],
                                                                "ORCHESTRATOR_ID" => $movInfo["ORCHESTRATOR_ID"],
                                                                "PATTERN_ID" => $movInfo["PATTERN_ID"],
                                                                "EXE_SKIP_FLAG" => "",
                                                                "OVRD_OPERATION_NO_IDBH" => $movInfo["OPERATION_NO_IDBH"]
                                                );
                                            }

                                            // ----シンフォニーIDおよびオペレーションNoからMovementのチェック
                                            $retArray = $objOLA->chkSymphonyInstanceForConductor($intShmphonyClassId, $intOperationNoUAPK,"", $aryOptionOrder, "", $userId, $userName);

                                            // Symphonyインスタンス生成時のエラー処理　#745
                                            if($retArray[0] == false){
                                                //---CALLノードを異常終了へ
                                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                                $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                                
                                                if( isset( $retArray[5] ) )$intSubSymcallInsNo = $retArray[5];   
                                                if ( $intSubSymcallInsNo != "")$aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] =  $intSubSymcallInsNo;

                                                $execLogMessage = $retArray[6];
    
                                                //　#749対応
                                                if( $retArray[6] != "" ){
                                                    $arrexecLogMessage = explode( PHP_EOL, $retArray[6]);
                                                    foreach ($arrexecLogMessage as $tmpvalmsg ) {
                                                        if( $tmpvalmsg != ""){
                                                            $execLogMessage = "[ERROR] " . $tmpvalmsg;

                                                            //Conductor（CALL元）インスタンスへのログ登録
                                                            $intNodeInsNo = $arrTargetNodeInstance['NODE_INSTANCE_NO'];
                                                            //[ERROR]Movementに作業対象ホストが登録されていません。(MovementID:X) [ Node instance ID :XXXX ]
                                                            $tmpexecLogMessage = $objMTS->getSomeMessage("ITABASEH-STD-170004",array($execLogMessage,$intNodeInsNo) );
                                                            $arySymInsUpdateTgtSource['EXEC_LOG'] = $arySymInsUpdateTgtSource['EXEC_LOG'] . $tmpexecLogMessage .PHP_EOL ;                                                            
                                                        }
                                                    }

                                                }
    
                                                // 更新用のテーブル定義
                                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                                // BIND用のベースソース
                                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                                
                                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                                //次のNode取得
                                                $arySqlBind=array(
                                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                                    "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                                    "TERMINAL_TYPE_ID" => 2, //out
                                                    );
                                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                                //クラスの取得
                                                $arrParallelTargetNodeClass=array();
                                                foreach ($aryRetBody as $key => $value) {
                                                    foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                        if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                            $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                        }
                                                    }
                                                }

                                                $conditionalflg="";
                                                foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                                    //次のNodeがconditionの場合
                                                    if($nclass['NODE_TYPE_ID'] == 6 ){
                                                        $conditionalflg="1";
                                                    }
                                                }
                                                //次のNodeがcondition以外
                                                if($conditionalflg != 1 ){
                                                        //Conductorインスタンスのステータスを異常終了へ
                                                        $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了
                                                        break;    
                                                }else{
                                                    $boolNextNodeReadyflg = true;
           
                                                    break;

                                                }
                                            }else{
                                                // ----シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録
                                                $retArray = $objOLA->registerSymphonyInstanceForConductor($intShmphonyClassId, $intOperationNoUAPK,"", $aryOptionOrder, "", $userId, $userName);
                                                // Symphonyインスタンス生成時のエラー処理　#745
                                                if($retArray[0] == false){
    
                                                    //---CALLノードを異常終了へ
                                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                                    $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                                    
                                                    if( isset( $retArray[5] ) )$intSubSymcallInsNo = $retArray[5];   
                                                    if ( $intSubSymcallInsNo != "")$aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] =  $intSubSymcallInsNo;
      
                                                    // 更新用のテーブル定義
                                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                                    // BIND用のベースソース
                                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                                    
                                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                                    //次のNode取得
                                                    $arySqlBind=array(
                                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                                        "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                                        "TERMINAL_TYPE_ID" => 2, //out
                                                        );
                                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                                    //クラスの取得
                                                    $arrParallelTargetNodeClass=array();
                                                    foreach ($aryRetBody as $key => $value) {
                                                        foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                            if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                                $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                            }
                                                        }
                                                    }

                                                    $conditionalflg="";
                                                    foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                                        //次のNodeがconditionの場合
                                                        if($nclass['NODE_TYPE_ID'] == 6 ){
                                                            $conditionalflg="1";
                                                        }
                                                    }
                                                    //次のNodeがcondition以外
                                                    if($conditionalflg != 1 ){
                                                            //Conductorインスタンスのステータスを異常終了へ
                                                            $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了
                                                            break;    
                                                    }else{
                                                        $boolNextNodeReadyflg = true;
               
                                                        break;

                                                    }
                                                }
                                                // シンフォニーIDおよびオペレーションNoからシンフォニーインスタンスを新規登録----

                                                $intSubSymcallInsNo = $retArray[5];
                                                //---ノードインスタンス取得
                                                $arySqlBind=array(
                                                    "SYMPHONY_INSTANCE_NO" =>  $intSubSymcallInsNo,
                                                    );
                                            }


                                        }

                                    }else{
                                        //---ノードインスタンス取得
                                        $arySqlBind=array(
                                            "SYMPHONY_INSTANCE_NO" => $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'],
                                            );                                    
                                    }

                                    //---CALLノードを実行中へ
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中
                                    $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)";
                                    if ( $intSubSymcallInsNo != "")$aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'] =  $intSubSymcallInsNo;

                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                    
                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001403";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                    // CALLノードを実行中へ---
                                    
                                    $aryRetBody = getsubSymphonyInstanceInfo($objDBCA,$arySqlBind,$strFxName);

                                    //Conductorインスタンスのステータスを実行中へ
                                    $arySymInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中へ
                                }else{

                                    $arySqlBind=array(
                                        "SYMPHONY_INSTANCE_NO" => $aryMovInsUpdateTgtSource['CONDUCTOR_INSTANCE_CALL_NO'],
                                        );
                                    $aryRetBody = getsubSymphonyInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arySymInsCallUpdateTgtSource = $aryRetBody[0];

                                    if( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 5 ){
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '9'; //正常終了
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $boolNextNodeReadyflg = true;
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        $boolNextNodeReadyflg = true;

                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 7 ){

                                        //Nodeインスタンスのステータスを異常終了へ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '6'; //異常終了
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを異常終了へ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了
                                                break;    
                                        }
                                        
                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 6 ){

                                        //Nodeインスタンスのステータスを異常終了へ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '7'; //緊急停止
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを異常終了へ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 6;     //緊急停止
                                                break;    
                                        }
                                        
                                    }elseif( $arySymInsCallUpdateTgtSource['STATUS_ID'] == 8 ){

                                        //Nodeインスタンスのステータスを想定外エラーへ
                                        $aryMovInsUpdateTgtSource['STATUS_ID']    = '11'; //想定外エラー
                                        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                                        $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                        // 更新用のテーブル定義
                                        $aryConfigForIUD = $aryConfigForMovInsIUD;
                                        
                                        // BIND用のベースソース
                                        $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;
                                        $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                        
                                        if( $aryRetBody !== true  ){
                                            // 例外処理へ
                                            $strErrStepIdInFx="00001404";
                                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                        }

                                        //次のNode取得
                                        $arySqlBind=array(
                                            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                            "NODE_CLASS_NO" => $arrTargetNodeInstance["I_NODE_CLASS_NO"],
                                            "TERMINAL_TYPE_ID" => 2, //out
                                            );
                                        $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                        //クラスの取得
                                        $arrParallelTargetNodeClass=array();
                                        foreach ($aryRetBody as $key => $value) {
                                            foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                                if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                    $arrParallelTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                }
                                            }
                                        }

                                        $conditionalflg="";
                                        foreach ($arrParallelTargetNodeClass as $key => $nclass) {
                                            if($nclass['NODE_TYPE_ID'] == 6 ){
                                                $conditionalflg="1";
                                            }
                                        }
                                        //次のNodeがcondition以外
                                        if($conditionalflg != 1 ){
                                                //Conductorインスタンスのステータスを想定外エラーへ
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = 8;     //想定外エラー
                                                break;    
                                        }
                                        
                                    }

                                    //後続処理conditional用
                                    switch( $aryMovInsUpdateTgtSource["STATUS_ID"] ){
                                        case "10":  //準備エラー
                                        case "6":  //異常終了
                                        case "7":  //緊急停止
                                        case "11":  //想定外エラー
                                        case "12":  //Skip完了
                                        case "9":  //正常終了
                                        case "14":  //Skip終了
                                            $boolNextNodeReadyflg = true;
                                            break;
                                        default:
                                            break;
                                    }


                                }
                                //symphony呼び出しConductorインスタンスのステータスを更新---

                            }
                            break;  

                        case "11":  #status-file-branch #587

                            //---status-file-branch ノードステータス更新
                            if(  $arrTargetNodeInstance['STATUS_ID'] == '2'  ){
                                //status-file-branchノードを準備中へ
                                $aryMovInsUpdateTgtSource['STATUS_ID'] = 3;     //実行中
                                $aryMovInsUpdateTgtSource['TIME_START'] = "DATETIMEAUTO(6)"; 

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00001600";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                            }else{
                                //---status-file-branchノードのステータスを正常終了へ
                                //status-file-branch接続先のノードインスタンスの取得
                                $arySqlBind=array(
                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                    "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                    "TERMINAL_TYPE_ID" => 1, //in
                                    );
                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                //ノードクラスの取得
                                $arrConditionalTargetNodeClass=array();
                                foreach ($aryRetBody as $key => $value) {
                                    foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                        if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                            $arrConditionalTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2; 
                                               
                                        }
                                    }
                                }
                                //ノードインスタンスの取得
                                $arrConditionalTargetNodeInstance = array();
                                foreach ($arrConditionalTargetNodeClass as $key => $nclass) {
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $nclass["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 2, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arrConditionalTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']] = $aryRetBody[0];
                                }

                                //作業NO取得
                                $intMovExecNo = $arrConditionalTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']]['EXECUTION_NO'];
                                $intOrcNo = $arrConditionalTargetNodeInstance[$aryRetBody[0]['NODE_INSTANCE_NO']]['I_ORCHESTRATOR_ID'];
                                $strgetStatusfile = "";
                                if( $arrDataRelaystoragePath[$intOrcNo] != "" ){
                                    $strDataRelaystoragePath = $arrDataRelaystoragePath[$intOrcNo]."/". str_pad($intMovExecNo, 10, '0', STR_PAD_LEFT)."/out/MOVEMENT_STATUS_FILE";
                                    if( file_exists($strDataRelaystoragePath) ) {
                                        //ステータスファイル取得
                                        $tmpgetStatusfile = file_get_contents( $strDataRelaystoragePath );
                                        
                                        //BOM削除
                                        $bomcode = hex2bin('EFBBBF');
                                        $tmpgetStatusfile = preg_replace("/^{$bomcode}/", '', $tmpgetStatusfile);
                                        
                                        //ステータス取得
                                        $tmpgetStatusfile = str_replace(array("\r\n", "\r", "\n"), PHP_EOL, $tmpgetStatusfile);
                                        $tmpgetStatusfile = explode( PHP_EOL , $tmpgetStatusfile);
                                        foreach ($tmpgetStatusfile as $tmpStatus) {
                                            if( $tmpStatus != "" ){
                                                $strgetStatusfile = $tmpStatus;
                                                break;   
                                            }
                                        }
                                    }
                                }

                                //次のノードを準備中へ
                                $strConditionalTargetNodeInsID = $aryRetBody[0]['NODE_INSTANCE_NO'];

                                //#838 対応
                                $intconditionalStopflg = 0;
                                if( count($arrOfErrMovement) != 0 ){
                                    foreach ($arrOfErrMovement as $tmparrOfErrMovement) {
                                       if( $tmparrOfErrMovement['NODE_INSTANCE_NO'] != $strConditionalTargetNodeInsID ){
                                            $intconditionalStopflg = 1;

                                            //次のNode取得
                                            $arySqlBind=array(
                                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                                "NODE_CLASS_NO" => $tmparrOfErrMovement['I_NODE_CLASS_NO'],
                                                "TERMINAL_TYPE_ID" => 2, //out
                                                );
                                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                            //クラスの取得
                                            $arrNextTargetNodeClass=array();
                                            foreach ($aryRetBody as  $value) {
                                                foreach ( $arrNodeClassInfo as $value2) {
                                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                                        $arrNextTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                                    }
                                                }
                                            }
                                            
                                            foreach ($arrNextTargetNodeClass as  $nclass) {
                                                if($nclass['NODE_TYPE_ID'] == 6 ){
                                                    $intconditionalStopflg = 0;
                                                }
                                            }    
                                       }
                                    }
                                }

                                if( $intconditionalStopflg != 1 ){
                                    //---status-file-branchノードのステータスを正常終了へ
                                    //status-file-branch接続先のノードインスタンスの取得
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 2, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                                    $arrConditionalTargetStatusID=array();
                                    foreach ($aryRetBody as $key => $value) {
                                        
                                        $strCaseNo = $value['CASE_NO'];
                                        if( $strCaseNo == "0" ){
                                            $strConditionalvalue = "____ELSE____";
                                        }else{
                                            $strConditionalvalue = $value['CONDITIONAL_ID'];
                                        }

                                        $arrConditionalTargetStatusID[$strConditionalvalue] = $value['CONNECTED_NODE_NAME'];
                                        if( $strConditionalvalue == $strgetStatusfile ){
                                            $strNextTargetNode = $value['CONNECTED_NODE_NAME'];
                                        }
                                    }

                                    //ステータスファイルの値と条件に一致するノードの設定
                                    if( isset( $arrConditionalTargetStatusID[$strgetStatusfile] ) ) {
                                        $strNextTargetNode=$arrConditionalTargetStatusID[$strgetStatusfile];
                                    }else{
                                        $strNextTargetNode=$arrConditionalTargetStatusID["____ELSE____"];
                                    }

                                    //不要側のSKIP用リスト
                                    $arrNextNodeList=array();
                                    foreach ($arrConditionalTargetStatusID as $key => $value) {
                                        $arrNextNodeList[$value]=$value;
                                    }
                                    unset($arrNextNodeList[$strNextTargetNode]);

                                    //全ノードの取得、（TERMINAL-OUT）
                                    $aryRetBody = $objOLA->getInfoOfOneNodeTerminal($rowOfConductor['I_CONDUCTOR_CLASS_NO'], 0,0,1,2);#TERMINALあり
                                    //整形
                                    $arrNodeClassInfo=array();
                                    foreach ( $aryRetBody[4] as $key => $value) {
                                            $arrNodeClassInfo[$value['NODE_CLASS_NO']]=$value; 
                                    }

                                    //不使用Case先をSKIP完了へ
                                    $aryRetBody = callconditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$arrNextNodeList);

                                    //次のノードを処理
                                    //次のノードのクラス取得
                                    $arrTargetNodeClassID="";
                                    foreach ( $arrNodeClassInfo as $key => $value) {
                                        if( $value['NODE_NAME'] == $strNextTargetNode )$arrTargetNodeClassInfo=$value; 
                                    }

                                    //次のノードインスタンス取得
                                    $arySqlBind=array(
                                        "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                        "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                        "TERMINAL_TYPE_ID" => 1, //in
                                        );
                                    $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                    $arrNextTargetNodeInstance = $aryRetBody[0];

                                    $aryMovInsUpdateTgtSourceStart=$arrNextTargetNodeInstance;
                                    $aryMovInsUpdateTgtSourceStart['STATUS_ID'] = 2;     //準備中
                                    $aryMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                                    $aryMovInsUpdateTgtSourceStart['TIME_START'] = "DATETIMEAUTO(6)"; 
                                    #$aryMovInsUpdateTgtSourceStart['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSourceStart['TIME_START']) ;
                                    $aryMovInsUpdateTgtSourceStart['TIME_END'] = str_replace("-","/",$aryMovInsUpdateTgtSourceStart['TIME_END']) ;

                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSourceStart;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                    
                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001601";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }

                                    //ノードのステータス更新
                                
                                    //---ノードを正常終了へ
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 9;     //正常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001602";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                }else{
                                    //ノードを正常終了へ
                                    $aryMovInsUpdateTgtSource = $arrTargetNodeInstance;
                                    $aryMovInsUpdateTgtSource['STATUS_ID'] = 6;     //異常終了
                                    $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)"; 
                                    $aryMovInsUpdateTgtSource['TIME_START'] = str_replace("-","/",$aryMovInsUpdateTgtSource['TIME_START']) ;
                                    $aryMovInsUpdateTgtSource['LAST_UPDATE_USER'] = $db_access_user_id;
                                    // 更新用のテーブル定義
                                    $aryConfigForIUD = $aryConfigForMovInsIUD;

                                    // BIND用のベースソース
                                    $aryBaseSourceForBind = $aryMovInsUpdateTgtSource;

                                    $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                    if( $aryRetBody !== true  ){
                                        // 例外処理へ
                                        $strErrStepIdInFx="00001602";
                                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                    }
                                }
                            
                            //status-file-branchノードステータス更新---

                            }
                            break;
    
                    }

                    //--- #838 対応

                    //----各NODEの情報収集                    
                    $aryTempForSql = array('WHERE'=>"CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO AND DISUSE_FLAG IN ('0') ORDER BY NODE_INSTANCE_NO ASC");
                    
                    // 更新用のテーブル定義
                    $aryConfigForIUD = $aryConfigForMovInsIUD;
                    
                    // BIND用のベースソース
                    $aryBaseSourceForBind = $aryMovInsValueTmpl;

                    $arySqlBind = array( 'CONDUCTOR_INSTANCE_NO' => $rowOfConductor['CONDUCTOR_INSTANCE_NO'] );
                    $tmpMovement =  getNodeInstanceInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                    $tmpRetBody = $objOLA->getConductorStatusFromNode($tmpMovement);

                    #$arrOfFocusMovement = $tmpRetBody[0]['RUNS_NODE'];
                    $arrOfErrMovement = $tmpRetBody[0]['ERR_NODE'];

                    //実行中待機状態でない場合,movement,call後のconditionalかの判定
                    if( $intNextJobStopflg != 2 || array_search($aryMovInsUpdateTgtSource['I_NODE_TYPE_ID'],array(3,4,10,8) ) !== false ){
                        if( array_search($aryMovInsUpdateTgtSource['I_NODE_TYPE_ID'],array(3,4,10,8) ) !== false && count($arrOfFocusMovement) >= 1  ){

                            //次のNode取得
                            $arySqlBind=array(
                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                "NODE_CLASS_NO" => $aryMovInsUpdateTgtSource["I_NODE_CLASS_NO"],
                                "TERMINAL_TYPE_ID" => 2, //out
                                );
                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                            //クラスの取得
                            $arrNextTargetNodeClass=array();
                            foreach ($aryRetBody as $key => $value) {
                                foreach ( $arrNodeClassInfo as $key2 => $value2) {
                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                        $arrNextTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                    }
                                }
                            }
                            //後続処理conditionalの場合
                            foreach ($arrNextTargetNodeClass as $key => $nclass) {
                                if($nclass['NODE_TYPE_ID'] == 6 ){
                                    $intNextJobStopflg="1";
                                }
                            }

                            //後続処理conditionalでない＋pause実行中の場合
                            if( $intNextJobStopflg != "1" && $aryMovInsUpdateTgtSource['I_NODE_TYPE_ID'] == 8 ){
                                $intNextJobStopflg="1";
                            }

                            //conditionalの場合後続処理実行
                            if( $intNextJobStopflg == 1 ){
                                $intErrMovcnt = 0;
                            }else{
                                //後続処理conditional以外の場合
                                if( array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(6,7,10,11) ) !== false ){
                                    $intErrMovcnt = 1;
                                }
                                //全ノード中に異常系のノードが存在する場合
                                if( count($arrOfErrMovement) != 0 ){
                                    $intErrMovcnt = 1;
                                    //対象ノードが、正常終了、SKIP終了、警告終了の場合
                                    if( array_search($aryMovInsUpdateTgtSource['I_NODE_TYPE_ID'],array(3,4,10) )  !== false && array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(9,14,15) ) !== false){
                                        $intErrMovcnt = 0;                                                
                                    }   
                                }
                            }
                             //conditional以外、異常系ノード時、後続処理実施しない、全ノード完了まで待機状態
                            if( $intErrMovcnt != 0 ){
                                $intNextJobStopflg=2;
                                $boolNextNodeReadyflg = false;
                            }else{
                                 //conditional、異常系ノードなし、
                                $intNextJobStopflg=1;
                                //対象ノードが、正常終了、SKIP終了、警告終了の場合、後続処理実施
                                if( array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(9,14,15) ) !== false ){
                                    $boolNextNodeReadyflg = true;   
                                }
                            }
                            
                        }else{
                            //実行中待機状態でない場合の、異常系ノードありの場合
                            if( count($arrOfErrMovement) != 0 ){
                                $intNextJobStopflg = 2;
                                $boolNextNodeReadyflg = false;
                            }
                        }                  
                    }
                    
                    //異常系のノードあり＋Movement,callの後続（conditional、その他）で後続処理フラグの判定
                    $tmpNextNodeReadyflg = false;
                    if( count($arrOfErrMovement) != 0 && array_search($aryMovInsUpdateTgtSource['I_NODE_TYPE_ID'],array(3,4,10) ) !== false ){
                        foreach ($arrOfErrMovement as $tmparrOfErrMovement) {

                            //次のNode取得
                            $arySqlBind=array(
                                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                "NODE_CLASS_NO" => $tmparrOfErrMovement['I_NODE_CLASS_NO'],
                                "TERMINAL_TYPE_ID" => 2, //out
                                );
                            $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);

                            //クラスの取得
                            $arrNextTargetNodeClass=array();
                            foreach ($aryRetBody as  $value) {
                                foreach ( $arrNodeClassInfo as $value2) {
                                    if( $value2['NODE_NAME'] == $value['CONNECTED_NODE_NAME'] ){
                                        $arrNextTargetNodeClass[$value2['NODE_CLASS_NO']]=$value2;        
                                    }
                                }
                            }
                            if( $tmparrOfErrMovement['NODE_INSTANCE_NO'] == $aryMovInsUpdateTgtSource['NODE_INSTANCE_NO'] ){
                                //異常系
                                if( array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(6,7,10,11) ) !== false ){
                                    foreach ($arrNextTargetNodeClass as  $nclass) {
                                        //後続処理conditionalの場合
                                        if($nclass['NODE_TYPE_ID'] == 6 ){
                                            $tmpNextNodeReadyflg = true;
                                            break;
                                        }else{
                                            $tmpNextNodeReadyflg = false;
                                            $intNextJobStopflg = 2;
                                        }
                                    }                               
                                }elseif( $intNextJobStopflg != 2 ){
                                    $tmpNextNodeReadyflg = true;
                                } 
                                break;                              
                            }else{
                                if( array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(6,7,10,11) ) !== false ){
                                    $tmpNextNodeReadyflg = false;
                                }elseif( array_search($aryMovInsUpdateTgtSource['STATUS_ID'],array(9,14,15) ) !== false ){
                                    $tmpNextNodeReadyflg = true;
                                    foreach ($arrNextTargetNodeClass as $nclass) {
                                        //後続処理conditionalの場合
                                        if($nclass['NODE_TYPE_ID'] == 6 ){
                                            $tmpNextNodeReadyflg = true;
                                        }else{
                                            $tmpNextNodeReadyflg = false;
                                            $intNextJobStopflg = 2;
                                        }
                                    }
                                }
                            }
                        }
                        //次のノードを準備中のフラグ上書き
                        $boolNextNodeReadyflg = $tmpNextNodeReadyflg;
                    }
                    // #838 対応---

                    if( $arySymInsUpdateTgtSource['STATUS_ID'] >= $intSymInsStatus ){

                        //---NODE-Conductorインスタンスのステータス同期
                        switch( $aryMovInsUpdateTgtSource["STATUS_ID"] ){
                            case "1": // mov.未実行
                            case "2": // mov.準備中
                            case "3": // mov.実行中
                            case "5": // mov.実行完了（下位オーケストレータは、正常に終了していた場合）
                            case "9": // mov.正常終了
                            case "15": // mov.警告終了
                                break;
                            case "4": // mov.実行中(遅延)
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 4;     //.実行中(遅延)へ
                                break;
                            case "10": // mov.準備エラー
                            case "6": // mov.異常終了
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了へ
                                break;
                            case "7": // mov.緊急停止
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 6;     //緊急停止へ
                                break;
                            case "11": // mov.想定外エラー
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 8;     //想定外エラー
                                break;
                            default:
                                break;
                        } 
                        //NODE-Conductorインスタンスのステータス同期---
                    }


                    //---次のノードを準備中へ
                    switch( $aryMovInsUpdateTgtSource["I_NODE_TYPE_ID"] ){                   
                        case "3":  #movement 
                        case "4":  #call
                        case "7":  #merge             
                        case "8":  #pause
                        case "9":  #blank
                        case "10": #call-s        
                            //---次のノードを準備中へ
                            if( $boolNextNodeReadyflg === true && $intNextJobStopflg != 2){

                                //全ノードの取得、（TERMINAL-OUT）
                                $aryRetBody = $objOLA->getInfoOfOneNodeTerminal($rowOfConductor['I_CONDUCTOR_CLASS_NO'], 0,0,1,2);#TERMINALあり
                                //整形
                                $arrNodeClassInfo=array();
                                foreach ( $aryRetBody[4] as $key => $value) {
                                        $arrNodeClassInfo[$value['NODE_CLASS_NO']]=$value; 
                                }
                                //START接続先のノード名取得                
                                $strNextTargetNode = $arrNodeClassInfo[$rowOfFocusMovement['I_NODE_CLASS_NO']]['TERMINAL'][0]['CONNECTED_NODE_NAME'];

                                //次のノードのクラス取得
                                $arrTargetNodeClassID="";
                                foreach ( $arrNodeClassInfo as $key => $value) {
                                    if( $value['NODE_NAME'] == $strNextTargetNode )$arrTargetNodeClassInfo=$value; 
                                }

                                //次のノードインスタンス取得
                                $arySqlBind=array(
                                    "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                                    "NODE_CLASS_NO" => $arrTargetNodeClassInfo["NODE_CLASS_NO"],
                                    "TERMINAL_TYPE_ID" => 1, //in
                                    );
                                $aryRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
                                $arrTargetNodeInstance = $aryRetBody[0];

                                $aryMovInsUpdateTgtSourceStart=$arrTargetNodeInstance;
    
                                $aryMovInsUpdateTgtSourceStart['STATUS_ID'] = 2;     //準備中    


                                $aryMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                                $aryMovInsUpdateTgtSourceStart['TIME_START'] = "DATETIMEAUTO(6)";

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForMovInsIUD;

                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryMovInsUpdateTgtSourceStart;
                                
                                $aryRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                                
                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00002000";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }

                               //実行中のノードがMovement CALL　で次のノードが、Condition場合のみ、Symphnonyインスタンスのステータス
                                if( $arrTargetNodeInstance['I_NODE_TYPE_ID'] == 6 ){
                                    $arySymInsUpdateTgtSource['STATUS_ID'] =  $rowOfConductor['STATUS_ID'];     //そのまま
                                }
                        
                            }
                            // 次のノードを準備中へ---
                            break;
                        default:
                            break;
                    }
                    //次のノードを準備中へ---
                    $intSymInsStatus = $arySymInsUpdateTgtSource['STATUS_ID'];

                }

                //////////////////////////////////////////////////////
                // (ここまで)) 実行中の場合　//
                //////////////////////////////////////////////////////

                if( $rowOfConductor['ABORT_EXECUTE_FLAG'] == '2' )$boolNextNodeReadyflg = false;#838 対応

                //---NODEとCONDUCTORインスタンスのへのステータス同期 
                $strBeforeStatusNumeric = $rowOfConductor['STATUS_ID'];

                // #838 対応
                if( $intFocusMovementSeq != 0 && count($arrOfFocusMovement) == 0 && count($arrOfErrMovement) != 0 ){
                    //開始済み、実行中無し、エラーあり、
                    $intNextJobStopflg = 3; 
                    $strAfterStatusNumeric = 7;
                    $arySymInsUpdateTgtSource['STATUS_ID'] = 7;
                    //エラー1件の場合、ノードのステータス継承
                    if( count($arrOfErrMovement) == 1 ){
                        switch( $arrOfErrMovement[0]['STATUS_ID'] ){
                            case "10": // mov.準備エラー
                            case "6": // mov.異常終了
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 7;     //異常終了へ
                                break;
                            case "7": // mov.緊急停止
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 6;     //緊急停止へ
                                break;
                            case "11": // mov.想定外エラー
                                $arySymInsUpdateTgtSource['STATUS_ID'] = 8;     //想定外エラー
                                break;
                            default:
                                break;
                        } 
                    }
                    
                    $arySymInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                    //緊急停止時
                    if( $rowOfConductor['ABORT_EXECUTE_FLAG'] == 2 ){
                        
                        $arySymInsUpdateTgtSource['STATUS_ID'] = 6;
                    }
                    //全END待ち対応
                    if( $boolEndNodeWaitflg === true ){
                        
                        $arySymInsUpdateTgtSource['STATUS_ID'] = 5;
                    }                       
                }elseif( $intNextJobStopflg == 2 && count($arrOfFocusMovement) >= 1 ){
                    //待機状態、実行中ノードあり
                    if( ( $aryMovInsUpdateTgtSource["STATUS_ID"] == 9 && 
                        $aryMovInsUpdateTgtSource["I_NODE_TYPE_ID"] == 2 ) !== true ){
                        
                        foreach ($arrOfFocusMovement as $tmparrMovement) {
                            if( $tmparrMovement['I_NODE_TYPE_ID'] == 7 ){
                            }else{
                                $strAfterStatusNumeric = $arySymInsUpdateTgtSource['STATUS_ID']; 
                            }
                        }
                    }else{
                        $intNextJobStopflg = 3; 
                        $strAfterStatusNumeric = $arySymInsUpdateTgtSource['STATUS_ID'];    
                    }
                }else{
                    //通常
                    $strAfterStatusNumeric = $arySymInsUpdateTgtSource['STATUS_ID'];                    
                }
                
                //Conductorインスタンスのステータス比較
                if( $strBeforeStatusNumeric == $strAfterStatusNumeric){
                    //ログのみ更新 　#749対応
                    if( $tmpexecLogMessage != "" ){
                            // 更新用のテーブル定義
                            $aryConfigForIUD = array(
                                    "JOURNAL_SEQ_NO"=>"",
                                    "JOURNAL_ACTION_CLASS"=>"",
                                    "JOURNAL_REG_DATETIME"=>"",
                                    "CONDUCTOR_INSTANCE_NO"=>"",
                                    "EXEC_LOG"=>"",
                                    "LAST_UPDATE_TIMESTAMP"=>"",
                                    "LAST_UPDATE_USER"=>""
                                ); 

                            // BIND用のベースソース
                            $aryBaseSourceForBind = $arySymInsUpdateTgtSource;

                            $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                            
                            if( $aryRetBody !== true  ){
                                // 例外処理へ
                                $strErrStepIdInFx="00002100";
                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }     
                    }

                    //Conductor、Nodeのステータスが一致の場合、Conductorのステータスこ更新はしない
                }elseif( $strBeforeStatusNumeric <= $strAfterStatusNumeric ){
                    //Conductor、Nodeのステータスが不一致の場合、Conductorのステータスこ更新
                    //待機状態でない場合、ステータス更新
                    if( $intNextJobStopflg != "2" ){
                        //異常系の場合、完了日時追加
                        if( $arySymInsUpdateTgtSource['STATUS_ID'] > 5 ){
                            $arySymInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
                        }

                        //正常終了時に、ENDオプションによるステータス上書き #467
                        if( $arySymInsUpdateTgtSource['STATUS_ID'] == 5 ){
                            //全ENDノードの終了タイプ確認
                            foreach ($tmpMovement as $key => $value) {
                                //ENDノード(正常終了)の終了タイプでConductorのステータス上書き(優先度:正常終了<正常終了(警告)<異常終了)
                                if( $value['I_NODE_TYPE_ID'] == 2 && $value['STATUS_ID'] == 9 ){
                                    switch ( $value['END_TYPE'] ) {
                                        //異常終了
                                        case '7':
                                            $arySymInsUpdateTgtSource['STATUS_ID'] = $value['END_TYPE'];
                                            break;
                                        //警告終了
                                        case '11':
                                            if( $arySymInsUpdateTgtSource['STATUS_ID'] == 5 ){
                                                $arySymInsUpdateTgtSource['STATUS_ID'] = $value['END_TYPE'];
                                            }
                                            break;
                                        default:
                                            break;
                                    }
                                }
                            }
                        }

                        // 更新用のテーブル定義
                        $aryConfigForIUD = $aryConfigForSymInsIUD;
                        
                        // BIND用のベースソース
                        $aryBaseSourceForBind = $arySymInsUpdateTgtSource;
                        
                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                        
                        if( $aryRetBody !== true  ){
                            // 例外処理へ
                            $strErrStepIdInFx="00002100";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        #312
                        //更新後の状態取得 
                        $arySqlBind=array(
                            "CONDUCTOR_INSTANCE_NO" => $arySymInsUpdateTgtSource['CONDUCTOR_INSTANCE_NO'],
                            );   
                        $aryRetBody = getsubConductorInstanceInfo($objDBCA,$arySqlBind,$strFxName);
                        $aryConInsInfo = $aryRetBody[0];
                        
                        //---通知関連処理 
                        if( isset($aryConInsInfo['CONDUCTOR_INSTANCE_NO']) ){

                            $tmpNoticeInfo = json_decode($aryConInsInfo['I_NOTICE_INFO'],true);
                            $arrNoticeInfo = $tmpNoticeInfo['NOTICE_INFO'];

                            //作業No
                            $execNo  = $rowOfConductor['CONDUCTOR_INSTANCE_NO'];
                            //通知結果　ログ出力先
                            $tmpNoticelogdir = $root_dir_path . "/uploadfiles/2100180006/NOTICE_LOG/" . sprintf('%010d', $execNo) ;
                            $tmpNoticelogfile = "NoticeLog_". sprintf('%010d', $execNo) . ".log" ;
                            $logPath = $tmpNoticelogdir . "/" . $tmpNoticelogfile;

                            //ログ出力先チェック、ディレクトリ作成
                            if( !is_dir($tmpNoticelogdir) ){
                                #1907　umask退避-設定-戻し
                                $mask = umask();
                                umask(000);
                                if ( mkdir($tmpNoticelogdir,0777,true) ){
                                    chmod($tmpNoticelogdir, 0777);
                                }
                                umask($mask);
                            }

                            //通知設定がある場合
                            if( $arrNoticeInfo != array() ){
                                foreach ($arrNoticeInfo as $strNoticeList => $strNoticeStatus) {
                                    //通知、ステータス設定時のみ実施
                                    if( $strNoticeStatus != "" && $strNoticeList != "" ){
                                        //通知実行ステータス判定
                                        if( array_search($aryConInsInfo['STATUS_ID'], explode( ",", $strNoticeStatus ) ) !== false ){
                                            //通知処理呼び出し
                                            $aryRetBody = $objOLA->getExecNotice($aryConInsInfo,$strNoticeList,$strNoticeStatus);
                                            if( $aryRetBody[1] !== null ){
                                                // 例外処理へ
                                                $strErrStepIdInFx="00000100";
                                                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                            }
                                            //廃止済み通知のログ出力
                                            if( $aryRetBody[2] !== array() ){
                                                //通知ログ出力
                                                foreach ($aryRetBody[2] as $subject ) {
                                                    error_log(print_r( date('Y-m-d H:i:s') . " " . $subject . "\n", true), 3, $logPath );
                                                }
                                            }
                                        }
                                    }
                                }
                            }else{
                                if( is_file($logPath) !== true ){
                                    //通知ログ出力
                                    $subject = $objMTS->getSomeMessage("ITABASEH-STD-171006");//"仮：通知設定がされていない為、通知は実行されませんでした。";    
                                    error_log(print_r( date('Y-m-d H:i:s') . " " . $subject . "\n", true), 3, $logPath );
                                }
                            }

                            //通知ログ初回のみ登録
                            if( $aryConInsInfo['NOTICE_LOG'] == "" && is_file($logPath) === true ){
                                // 更新用のテーブル定義
                                $aryConfigForIUD = array(
                                        "JOURNAL_SEQ_NO"=>"",
                                        "JOURNAL_ACTION_CLASS"=>"",
                                        "JOURNAL_REG_DATETIME"=>"",
                                        "CONDUCTOR_INSTANCE_NO"=>"",
                                        "NOTICE_LOG"=>"",
                                        "LAST_UPDATE_TIMESTAMP"=>"",
                                        "LAST_UPDATE_USER"=>""
                                    );
                                $aryConfigForIUD = $aryConfigForSymInsIUD;


                                // BIND用のベースソース
                                $aryBaseSourceForBind = $aryConInsInfo;

                                $aryBaseSourceForBind['NOTICE_LOG'] = "NoticeLog_". sprintf('%010d', $aryConInsInfo['CONDUCTOR_INSTANCE_NO']) . ".log" ;
                                $aryBaseSourceForBind['TIME_BOOK'] = str_replace("-","/",$aryBaseSourceForBind['TIME_BOOK']) ;
                                $aryBaseSourceForBind['TIME_START'] = str_replace("-","/",$aryBaseSourceForBind['TIME_START']) ;
                                $aryBaseSourceForBind['TIME_END'] = str_replace("-","/",$aryBaseSourceForBind['TIME_END']) ;

                                $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                                if( $aryRetBody !== true  ){
                                    // 例外処理へ
                                    $strErrStepIdInFx="00002100";
                                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }                            
                        }

                    }elseif( $tmpexecLogMessage != "" ){
                        //待機状態で、ログのみ更新の場合　#749対応
                        // 更新用のテーブル定義
                        $aryConfigForIUD = array(
                                "JOURNAL_SEQ_NO"=>"",
                                "JOURNAL_ACTION_CLASS"=>"",
                                "JOURNAL_REG_DATETIME"=>"",
                                "CONDUCTOR_INSTANCE_NO"=>"",
                                "EXEC_LOG"=>"",
                                "LAST_UPDATE_TIMESTAMP"=>"",
                                "LAST_UPDATE_USER"=>""
                            ); 

                        // BIND用のベースソース
                        $aryBaseSourceForBind = $arySymInsUpdateTgtSource;

                        $aryRetBody = updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);
                        
                        if( $aryRetBody !== true  ){
                            // 例外処理へ
                            $strErrStepIdInFx="00002100";
                            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }                        
                    }
                }
                //NODEとCONDUCTORインスタンスのへのステータス同期 ---   


            if( $objDBCA->transactionCommit() !== true ){
                // 異常フラグON
                $intErrorFlag = 1;
                
                // 例外処理へ
                $strErrStepIdInFx="00002200";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50015");
                require ($root_dir_path . $log_output_php );
            }
            unset($g['__CONDUCTOR_INSTANCE_NO__']);
            unset($g['__TOP_ACCESS_AUTH__']);

        }
        //CONDUCTORを、一個ずつループする----
        
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


//nodeインスタンスのステータスの更新
function updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName){
    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                        ,"UPDATE"
                                        ,"NODE_INSTANCE_NO"
                                        ,"C_NODE_INSTANCE_MNG"
                                        ,"C_NODE_INSTANCE_MNG_JNL"
                                        ,$aryConfigForIUD
                                        ,$aryBaseSourceForBind);

    if( $aryRetBody[0] === false ){
        // 例外処理へ
        $strErrStepIdInFx="00003000";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    
    $strSqlUtnBody = $aryRetBody[1];
    $aryUtnSqlBind = $aryRetBody[2];
    
    $strSqlJnlBody = $aryRetBody[3];
    $aryJnlSqlBind = $aryRetBody[4];
    unset($aryRetBody);
    
    // ----履歴シーケンス払い出し
    $aryRetBody = getSequenceValueFromTable('C_NODE_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
    if( $aryRetBody[1] != 0 ){
        // 例外処理へ
        $strErrStepIdInFx="00003001";
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
        $strErrStepIdInFx="00003002";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    unset($aryRetBody01);
    unset($aryRetBody02);

    return true;
}

//Conductorインスタンスのステータスの更新
function updateConductorInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName){
    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                        ,"UPDATE"
                                        ,"CONDUCTOR_INSTANCE_NO"
                                        ,"C_CONDUCTOR_INSTANCE_MNG"
                                        ,"C_CONDUCTOR_INSTANCE_MNG_JNL"
                                        ,$aryConfigForIUD
                                        ,$aryBaseSourceForBind);

    if( $aryRetBody[0] === false ){
        // 例外処理へ
        $strErrStepIdInFx="00003003";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    
    $strSqlUtnBody = $aryRetBody[1];
    $aryUtnSqlBind = $aryRetBody[2];
    
    $strSqlJnlBody = $aryRetBody[3];
    $aryJnlSqlBind = $aryRetBody[4];
    unset($aryRetBody);
    
    // ----履歴シーケンス払い出し
    $aryRetBody = getSequenceValueFromTable('C_CONDUCTOR_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
    if( $aryRetBody[1] != 0 ){
        // 例外処理へ
        $strErrStepIdInFx="00003004";
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
        $strErrStepIdInFx="00003005";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    unset($aryRetBody01);
    unset($aryRetBody02);

    return true;
}

//nodeインスタンスの取得（Node,Terminalクラス付き）
function getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName){
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryDataSet = array();
    $aryForBind = array();


    $strQuery = "SELECT "
                 ." * "
               ." FROM "
               ." C_NODE_INSTANCE_MNG NINS "
               ."LEFT JOIN "
               ." C_NODE_CLASS_MNG NCLASS "
               ." ON NINS.I_NODE_CLASS_NO = NCLASS.NODE_CLASS_NO "
               ."LEFT JOIN "
               ." C_NODE_TERMINALS_CLASS_MNG TCLASS "
               ." ON NCLASS.NODE_CLASS_NO = TCLASS.NODE_CLASS_NO "
               ."WHERE "
               ."    NINS.DISUSE_FLAG IN ('0') "
               ."AND TCLASS.DISUSE_FLAG IN ('0') "
               ."AND NCLASS.DISUSE_FLAG IN ('0') "
               ."AND NINS.CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO "
               ."AND NINS.I_NODE_CLASS_NO = :NODE_CLASS_NO "
               ."AND TCLASS.TERMINAL_TYPE_ID = :TERMINAL_TYPE_ID "
               ."";

    foreach ($arySqlBind as $key => $value) {
       $aryForBind[$key] = $value;
    }
    $retArray = singleSQLCoreExecute($objDBCA, $strQuery, $aryForBind, $strFxName);

    if( $retArray[0]!==true ){
        $intErrorType = $retArray[1];
        $aryErrMsgBody = $retArray[2];
        $strErrMsg = $retArray[4];
        // 例外処理へ
        $strErrStepIdInFx="00003005";
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    $objQueryUtn =& $retArray[3];

    //----発見行だけループ
    $intCount = 0;
    $aryRowOfMovClassTable = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryRowOfMovClassTable[] = $row;
        $intCount += 1;
    }

    return $aryRowOfMovClassTable;
}

//Conductorインスタンスの取得
function getsubConductorInstanceInfo($objDBCA,$arySqlBind,$strFxName){
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryDataSet = array();
    $aryForBind = array();


    $strQuery = "SELECT "
               ." SINS.* , "
               ." SYSSTATUS.SYM_EXE_STATUS_NAME AS STATUS_NAME , "
               ." SYSABORT.SYM_ABORT_FLAG_NAME AS ABORT_FLAG_NAME "
               ." FROM "
               ." C_CONDUCTOR_INSTANCE_MNG SINS "
               ."LEFT JOIN "
               ." B_SYM_EXE_STATUS SYSSTATUS "
               ." ON SINS.STATUS_ID = SYSSTATUS.SYM_EXE_STATUS_ID "
               ."LEFT JOIN "
               ." B_SYM_ABORT_FLAG SYSABORT "
               ." ON SINS.ABORT_EXECUTE_FLAG = SYSABORT.SYM_ABORT_FLAG_ID "

               ."WHERE "
               ."    SINS.DISUSE_FLAG IN ('0') "
               ."AND SINS.CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO "
       
               ."";

    foreach ($arySqlBind as $key => $value) {
       $aryForBind[$key] = $value;
    }

    $retArray = singleSQLCoreExecute($objDBCA, $strQuery, $aryForBind, $strFxName);

    if( $retArray[0]!==true ){
        $intErrorType = $retArray[1];
        $aryErrMsgBody = $retArray[2];
        $strErrMsg = $retArray[4];
        // 例外処理へ
        $strErrStepIdInFx="00003006";
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    $objQueryUtn =& $retArray[3];

    //----発見行だけループ
    $intCount = 0;
    $aryRowOfMovClassTable = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryRowOfMovClassTable[] = $row;
        $intCount += 1;
    }

    return $aryRowOfMovClassTable;
}

//MovementからConductor更新用のステータス取得
function getMovementStatus($objOLA,$rowOfFocusMovement){
    //NodeTYPEがMovementの場合のみ
    if( $rowOfFocusMovement['I_NODE_TYPE_ID'] == 3 ){
        //----すでに1個はムーブメントがはじまった後である場合
        //----RedMineチケット1026
        $boolOrcEndPhase = false;
        switch( $rowOfFocusMovement['STATUS_ID'] ){
            case "5":  // 実行完了
            case "12": // Skip完了
            #case "8":  // 保留中
            #case "13": // Skip後保留中
            case "9":  // 正常終了
            case "14": // Skip終了
                break;
            case "2": // 準備中
            case "3": // 実行中
            case "4": // 実行中(遅延)
                ////////////////////////////////////////////////////////////////////////////////////
                // (ここから)現在のムーブメントについて、下位オーケストレータに状態を問い合わせる //
                ////////////////////////////////////////////////////////////////////////////////////
                $aryRetBodyOfSMfO = $objOLA->getMovementStatusFromOrchestrator($rowOfFocusMovement['I_ORCHESTRATOR_ID']
                                                                              ,$rowOfFocusMovement['EXECUTION_NO']);

                if( $aryRetBodyOfSMfO[1] !== null ){
                    // 例外処理へ
                    $strErrStepIdInFx="00003007";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $strStatusFromOrch = $aryRetBodyOfSMfO[0];
                $strDateOfTimeEndOnOrch = $aryRetBodyOfSMfO[4]['TIME_END'];
                switch( $strStatusFromOrch ){
                        case "5": // mov.実行完了（下位オーケストレータは、正常に終了していた場合）
                        case "11": // mov.想定外エラー
                        case "7": // mov.緊急停止
                        case "6": // mov.異常終了
                        case "4": // mov.実行中(遅延)
                        case "3": // mov.実行中
                                $boolOrcEndPhase = true;
                                break;
                        case "9": // mov.正常終了
                        case "8": // mov.保留中
                        case "2": // mov.準備中
                        case "1": // mov.未実行
                    default: // 返し値として存在してはいけない値だった場合
                        // 例外処理へ
                        $strErrStepIdInFx="00003008";
                        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        break;
                }

                ////////////////////////////////////////////////////////////////////////////////////
                // (ここまで)現在のムーブメントについて、下位オーケストレータに状態を問い合わせる //
                ////////////////////////////////////////////////////////////////////////////////////
                break;        
        }
    }
    if( $boolOrcEndPhase === true ){

        $aryMovInsUpdateTgtSource['STATUS_ID'] = $aryRetBodyOfSMfO[0];
        //----オーケストレータ側が～実行中の場合
        $aryMovInsUpdateTgtSource['TIME_END'] = "DATETIMEAUTO(6)";
    }else{
        //----オーケストレータ側が終了している場合
        $aryMovInsUpdateTgtSource = $rowOfFocusMovement;
        if( $rowOfFocusMovement['STATUS_ID'] == '5' )$aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
        if( $rowOfFocusMovement['STATUS_ID'] == '12' )$aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
        if( $rowOfFocusMovement['STATUS_ID'] == '14' )$aryMovInsUpdateTgtSource['STATUS_ID'] = '9';
    }
        
    return $aryMovInsUpdateTgtSource;

}

//nodeインスタンスの取得（Conductorインスタンス単体）intConductorInsetance
function getNodeInstanceInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT FOR UPDATE"
                                          ,"NODE_INSTANCE_NO"
                                          ,"C_NODE_INSTANCE_MNG"
                                          ,"C_NODE_INSTANCE_MNG_JNL"
                                          ,$aryConfigForIUD
                                          ,$aryBaseSourceForBind
                                          ,$aryTempForSql);
    
    if( $aryRetBody[0] === false ){
        // 例外処理へ
        $strErrStepIdInFx="00003009";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }

    $strSqlUtnBody = $aryRetBody[1];
    $aryUtnSqlBind = $aryRetBody[2];
    unset($aryRetBody);

    foreach ($arySqlBind as $key => $value) {
        $aryUtnSqlBind[$key] = $value;
    }

    $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);
  
    
    if( $aryRetBody[0] !== true ){
        // 例外処理へ
        $strErrStepIdInFx="00003010";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    $objQueryUtn =& $aryRetBody[3];
    
    //----発見行だけループ
    $aryStartNode = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryStartNode[] = $row;
    }
    //発見行だけループ----
    return $aryStartNode;

}
//nodeクラスの取得（Conductorクラス単体）
function getNodeClassInfo($objDBCA,$db_model_ch,$intConductorclass,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT FOR UPDATE"
                                          ,"NODE_INSTANCE_NO"
                                          ,"C_NODE_CLASS_MNG"
                                          ,"C_NODE_CLASS_MNG_JNL"
                                          ,$aryConfigForIUD
                                          ,$aryBaseSourceForBind
                                          ,$aryTempForSql);
    
    if( $aryRetBody[0] === false ){
        // 例外処理へ
        $strErrStepIdInFx="00003011";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }

    $strSqlUtnBody = $aryRetBody[1];
    $aryUtnSqlBind = $aryRetBody[2];
    unset($aryRetBody);
    $aryUtnSqlBind['CONDUCTOR_CLASS_NO'] = $intConductorclass;
    $aryRetBody = singleSQLCoreExecute($objDBCA, $strSqlUtnBody, $aryUtnSqlBind, $strFxName);   
    
    if( $aryRetBody[0] !== true ){
        // 例外処理へ
        $strErrStepIdInFx="00003012";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    $objQueryUtn =& $aryRetBody[3];
    
    //----発見行だけループ
    $aryStartNode = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryStartNode[] = $row;
    }
    //発見行だけループ----
    return $aryStartNode;

}


function callconditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$arrNextNodeList){
    $aryRetBody = conditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$arrNextNodeList);
    return $aryRetBody;
}

function conditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$arrNextNodeList){
    //条件不一致のConndition以降のノードステータスをSKIPへ変更
    foreach ($arrNextNodeList as $strSkipNode) {

        $boolConditionalSkipafterflg=false;
        //次のノードを処理
        //次のノードのクラス取得
        $arrTargetNodeClassID="";
        foreach ( $arrNodeClassInfo as $key => $value) {
            if( $value['NODE_NAME'] == $strSkipNode )$tmpTargetNodeClassInfo=$value; 
        }

        //次のノードインスタンス取得
        $arySqlBind=array(
            "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
            "NODE_CLASS_NO" => $tmpTargetNodeClassInfo["NODE_CLASS_NO"],
            "TERMINAL_TYPE_ID" => 2, //in
            );
        $tmpArrRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
        if( $tmpArrRetBody != array() ){
            foreach ($tmpArrRetBody as $key2 => $value2) {
                $tmpNextTargetNodeInstance = $value2;

                $tmpMovInsUpdateTgtSourceStart=$tmpNextTargetNodeInstance;
                $tmpMovInsUpdateTgtSourceStart['STATUS_ID'] = 14;     //準備中
                $tmpMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
                $tmpMovInsUpdateTgtSourceStart['TIME_START']= "DATETIMEAUTO(6)";
                $tmpMovInsUpdateTgtSourceStart['TIME_END']  = "DATETIMEAUTO(6)";

                // 更新用のテーブル定義
                $aryConfigForIUD = $aryConfigForMovInsIUD;

                // BIND用のベースソース
                $aryBaseSourceForBind = $tmpMovInsUpdateTgtSourceStart;
                
                $tmpRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

                if( $tmpRetBody !== true  ){
                    // 例外処理へ
                    $strErrStepIdInFx="00001601";
                    throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( $boolConditionalSkipafterflg == false ){
                    $tmpNextTargetNode = $tmpNextTargetNodeInstance['CONNECTED_NODE_NAME'];
                    $tmpNodeClassInfo =array( $tmpNextTargetNode => $tmpNextTargetNode );
                    $aryRetBody = callconditionalafterskip($objDBCA,$db_model_ch,$strFxName,$db_access_user_id,$aryConfigForMovInsIUD,$rowOfConductor,$arrNodeClassInfo,$tmpNodeClassInfo);
                }else{
                    return true;
                }

            }
        }else{
            //OUTTerminalがない場合（ENDノードの場合）、インスタンス再取得
            $arySqlBind=array(
                "CONDUCTOR_INSTANCE_NO" => $rowOfConductor['CONDUCTOR_INSTANCE_NO'],
                "NODE_CLASS_NO" => $tmpTargetNodeClassInfo["NODE_CLASS_NO"],
                "TERMINAL_TYPE_ID" => 1, //in
                );
            $tmpArrRetBody = getNodeInstanceTerminalInfo($objDBCA,$arySqlBind,$strFxName);
            $tmpNextTargetNodeInstance = $tmpArrRetBody[0];

            $tmpMovInsUpdateTgtSourceStart=$tmpNextTargetNodeInstance;
            $tmpMovInsUpdateTgtSourceStart['STATUS_ID'] = 14;     //準備中
            $tmpMovInsUpdateTgtSourceStart['LAST_UPDATE_USER'] = $db_access_user_id;
            $tmpMovInsUpdateTgtSourceStart['TIME_START']= "DATETIMEAUTO(6)";
            $tmpMovInsUpdateTgtSourceStart['TIME_END']  = "DATETIMEAUTO(6)";

            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForMovInsIUD;

            // BIND用のベースソース
            $aryBaseSourceForBind = $tmpMovInsUpdateTgtSourceStart;
            
            $tmpRetBody = updateNodeInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName);

            if( $tmpRetBody !== true  ){
                // 例外処理へ
                $strErrStepIdInFx="00001601";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
    }
    return true;
}


//Symphonyンスタンスの取得
function getsubSymphonyInstanceInfo($objDBCA,$arySqlBind,$strFxName){
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryDataSet = array();
    $aryForBind = array();


    $strQuery = "SELECT "
                 ." * "
               ." FROM "
               ." C_SYMPHONY_INSTANCE_MNG SINS "
               ."WHERE "
               ."    SINS.DISUSE_FLAG IN ('0') "
               ."AND SINS.SYMPHONY_INSTANCE_NO = :SYMPHONY_INSTANCE_NO "
       
               ."";

    foreach ($arySqlBind as $key => $value) {
       $aryForBind[$key] = $value;
    }

    $retArray = singleSQLCoreExecute($objDBCA, $strQuery, $aryForBind, $strFxName);

    if( $retArray[0]!==true ){
        $intErrorType = $retArray[1];
        $aryErrMsgBody = $retArray[2];
        $strErrMsg = $retArray[4];
        // 例外処理へ
        $strErrStepIdInFx="00003006";
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    $objQueryUtn =& $retArray[3];

    //----発見行だけループ
    $intCount = 0;
    $aryRowOfMovClassTable = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryRowOfMovClassTable[] = $row;
        $intCount += 1;
    }

    return $aryRowOfMovClassTable;
}

//symphonyインスタンスのステータスの更新
function updateSymphonyInstanceStatus($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName){
    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                        ,"UPDATE"
                                        ,"SYMPHONY_INSTANCE_NO"
                                        ,"C_SYMPHONY_INSTANCE_MNG"
                                        ,"C_SYMPHONY_INSTANCE_MNG_JNL"
                                        ,$aryConfigForIUD
                                        ,$aryBaseSourceForBind);

    if( $aryRetBody[0] === false ){
        // 例外処理へ
        $strErrStepIdInFx="00003003";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    
    $strSqlUtnBody = $aryRetBody[1];
    $aryUtnSqlBind = $aryRetBody[2];
    
    $strSqlJnlBody = $aryRetBody[3];
    $aryJnlSqlBind = $aryRetBody[4];
    unset($aryRetBody);
    
    // ----履歴シーケンス払い出し
    $aryRetBody = getSequenceValueFromTable('C_CONDUCTOR_INSTANCE_MNG_JSQ', 'A_SEQUENCE', FALSE );
    if( $aryRetBody[1] != 0 ){
        // 例外処理へ
        $strErrStepIdInFx="00003004";
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
        $strErrStepIdInFx="00003005";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    unset($aryRetBody01);
    unset($aryRetBody02);

    return true;
}

?>




