<?php
//   Copyright 2020 NEC Corporation
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
    $db_access_user_id  = -100021; //
    
    $strFxName          = "proc({$log_file_prefix})";

    //B_ANSIBLE_IF_INFO
    $aryConfigForAnsibleIfIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ANSIBLE_IF_INFO_ID"=>"",
        #"ANSIBLE_HOSTNAME"=>"",
        #"ANSIBLE_PROTOCOL"=>"",
        #"ANSIBLE_PORT"=>"",
        #"ANSTWR_HOST_ID"=>"",
        #"ANSTWR_PROTOCOL"=>"",
        #"ANSTWR_PORT"=>"",
        #"ANSIBLE_EXEC_MODE"=>"",
        "ANSIBLE_STORAGE_PATH_LNX"=>"",
        "ANSIBLE_STORAGE_PATH_ANS"=>"",
        "SYMPHONY_STORAGE_PATH_ANS"=>"",
        "CONDUCTOR_STORAGE_PATH_ANS"=>"",
        #"ANSIBLE_EXEC_OPTIONS"=>"",
        #"ANSIBLE_EXEC_USER"=>"",
        #"ANSIBLE_ACCESS_KEY_ID"=>"",
        #"ANSIBLE_SECRET_ACCESS_KEY"=>"",
        #"ANSTWR_ORGANIZATION"=>"",
        #"ANSTWR_AUTH_TOKEN"=>"",
        #"ANSTWR_DEL_RUNTIME_DATA"=>"",
        #"NULL_DATA_HANDLING_FLG"=>"",
        #"ANSIBLE_NUM_PARALLEL_EXEC"=>"",
        #"ANSIBLE_REFRESH_INTERVAL"=>"",
        #"ANSIBLE_TAILLOG_LINES"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

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
        "CONDUCTOR_CALL_FLAG"=>"",
        "CONDUCTOR_CALLER_NO"=>"",
        "COLLECT_FLAG"=>"",
        "TIME_BOOK"=>"DATETIME",
        "TIME_START"=>"DATETIME",
        "TIME_END"=>"DATETIME",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    //C_OPERATION_LIST
    $aryConfigForOpeIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "OPERATION_NO_UAPK"=>"",
        "OPERATION_NAME"=>"",
        "OPERATION_DATE"=>"",
        "OPERATION_NO_IDBH"=>"",
        "LAST_EXECUTE_TIMESTAMP"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryOpeValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "OPERATION_NO_UAPK"=>"",
        "OPERATION_NAME"=>"",
        "OPERATION_DATE"=>"",
        "OPERATION_NO_IDBH"=>"",
        "LAST_EXECUTE_TIMESTAMP"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //C_STM_LIST
    $aryConfigForSTMIUD=array(
            "JOURNAL_SEQ_NO"=>"",            
            "JOURNAL_REG_DATETIME"=>"",      
            "JOURNAL_ACTION_CLASS"=>"",      
            "SYSTEM_ID"=>"",                 
            #"HARDAWRE_TYPE_ID"=>"",          
            "HOSTNAME"=>"",                  
            #"IP_ADDRESS"=>"",                
            #"ETH_WOL_MAC_ADDRESS"=>"",       
            #"ETH_WOL_NET_DEVICE"=>"",        
            #"PROTOCOL_ID"=>"",               
            #"LOGIN_USER"=>"",                
            #"LOGIN_PW_HOLD_FLAG"=>"",        
            #"LOGIN_PW"=>"",                  
            #"LOGIN_PW_ANSIBLE_VAULT"=>"",    
            #"LOGIN_AUTH_TYPE"=>"",           
            #"WINRM_PORT"=>"",                
            #"WINRM_SSL_CA_FILE"=>"",         
            #"OS_TYPE_ID"=>"",                
            #"SSH_EXTRA_ARGS"=>"",            
            #"HOSTS_EXTRA_ARGS"=>"",          
            #"CREDENTIAL_TYPE_ID"=>"",        
            #"SYSTEM_NAME"=>"",               
            #"COBBLER_PROFILE_ID"=>"",        
            #"INTERFACE_TYPE"=>"",            
            #"MAC_ADDRESS"=>"",               
            #"NETMASK"=>"",                   
            #"GATEWAY"=>"",                   
            #"STATIC"=>"",                    
            #"CONN_SSH_KEY_FILE"=>"",         
            #"ANSTWR_INSTANCE_GROUP_NAME"=>"",
            #"DISP_SEQ"=>"",                  
            #"NOTE"=>"",                      
            #"DISUSE_FLAG"=>"",               
            #"LAST_UPDATE_TIMESTAMP"=>"",     
            #"LAST_UPDATE_USER"=>"" 
    );

    $arySTMValueTmpl=array(
            "JOURNAL_SEQ_NO"=>"",            
            "JOURNAL_REG_DATETIME"=>"",      
            "JOURNAL_ACTION_CLASS"=>"",      
            "SYSTEM_ID"=>"",                 
            #"HARDAWRE_TYPE_ID"=>"",          
            "HOSTNAME"=>"",                  
            #"IP_ADDRESS"=>"",                
            #"ETH_WOL_MAC_ADDRESS"=>"",       
            #"ETH_WOL_NET_DEVICE"=>"",        
            #"PROTOCOL_ID"=>"",               
            #"LOGIN_USER"=>"",                
            #"LOGIN_PW_HOLD_FLAG"=>"",        
            #"LOGIN_PW"=>"",                  
            #"LOGIN_PW_ANSIBLE_VAULT"=>"",    
            #"LOGIN_AUTH_TYPE"=>"",           
            #"WINRM_PORT"=>"",                
            #"WINRM_SSL_CA_FILE"=>"",         
            #"OS_TYPE_ID"=>"",                
            #"SSH_EXTRA_ARGS"=>"",            
            #"HOSTS_EXTRA_ARGS"=>"",          
            #"CREDENTIAL_TYPE_ID"=>"",        
            #"SYSTEM_NAME"=>"",               
            #"COBBLER_PROFILE_ID"=>"",        
            #"INTERFACE_TYPE"=>"",            
            #"MAC_ADDRESS"=>"",               
            #"NETMASK"=>"",                   
            #"GATEWAY"=>"",                   
            #"STATIC"=>"",                    
            #"CONN_SSH_KEY_FILE"=>"",         
            #"ANSTWR_INSTANCE_GROUP_NAME"=>"",
            #"DISP_SEQ"=>"",                  
            #"NOTE"=>"",                      
            #"DISUSE_FLAG"=>"",               
            #"LAST_UPDATE_TIMESTAMP"=>"",     
            #"LAST_UPDATE_USER"=>""       
    );

    //D_ANS_CMDB_LINK :LNS PNS LRL
    $aryConfigForCMDBLinkIUD = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "COLUMN_ID"=>"",
            "MENU_ID"=>"",
            "COLUMN_LIST_ID"=>"",
            #"COL_TYPE"=>"",
            #"PATTERN_ID"=>"",
            #"VAL_VARS_LINK_ID"=>"",
            #"VAL_COL_SEQ_COMBINATION_ID"=>"",
            #"VAL_ASSIGN_SEQ"=>"",
            #"KEY_VARS_LINK_ID"=>"",
            #"KEY_COL_SEQ_COMBINATION_ID"=>"",
            #"KEY_ASSIGN_SEQ"=>"",
            #"NULL_DATA_HANDLING_FLG"=>"",
            "MENU_GROUP_ID"=>"",
            "MENU_GROUP_NAME"=>"",
            "MENU_ID_CLONE"=>"",
            "MENU_NAME"=>"",
            #"REST_COLUMN_LIST_ID"=>"",
            #"REST_VAL_VARS_LINK_ID"=>"",
            #"REST_VAL_COL_SEQ_COMBINATION_ID"=>"",
            #"REST_KEY_VARS_LINK_ID"=>"",
            #"REST_KEY_COL_SEQ_COMBINATION_ID"=>"",
            "PARSE_TYPE_ID"=>"",
            "FILE_PREFIX"=>"",
            "VARS_NAME"=>"",
            "VRAS_MEMBER_NAME"=>"",
            "COL_NAME"=>"",
            "COL_TITLE"=>"",
            "COL_CLASS"=>"",
            "TABLE_NAME"=>"",
            "DISP_SEQ"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
    );  

    $aryCMDBLinkValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "COLUMN_ID"=>"",
            "MENU_ID"=>"",
            "COLUMN_LIST_ID"=>"",
            #"COL_TYPE"=>"",
            #"PATTERN_ID"=>"",
            #"VAL_VARS_LINK_ID"=>"",
            #"VAL_COL_SEQ_COMBINATION_ID"=>"",
            #"VAL_ASSIGN_SEQ"=>"",
            #"KEY_VARS_LINK_ID"=>"",
            #"KEY_COL_SEQ_COMBINATION_ID"=>"",
            #"KEY_ASSIGN_SEQ"=>"",
            #"NULL_DATA_HANDLING_FLG"=>"",
            "MENU_GROUP_ID"=>"",
            "MENU_GROUP_NAME"=>"",
            "MENU_ID_CLONE"=>"",
            "MENU_NAME"=>"",
            #"REST_COLUMN_LIST_ID"=>"",
            #"REST_VAL_VARS_LINK_ID"=>"",
            #"REST_VAL_COL_SEQ_COMBINATION_ID"=>"",
            #"REST_KEY_VARS_LINK_ID"=>"",
            #"REST_KEY_COL_SEQ_COMBINATION_ID"=>"",
            "PARSE_TYPE_ID"=>"",
            "FILE_PREFIX"=>"",
            "VARS_NAME"=>"",
            "VRAS_MEMBER_NAME"=>"",
            "COL_NAME"=>"",
            "COL_TITLE"=>"",
            "COL_CLASS"=>"",
            "TABLE_NAME"=>"",
            "DISP_SEQ"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
    );

    //F_KY_AUTO_TABLE_YYYY_H　ベース
    $tmpConfigForCMDBbaseIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "ROW_ID"=>"",
        "HOST_ID"=>"",
        "OPERATION_ID"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $tmpCMDBbaseValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "ROW_ID"=>"",
        "HOST_ID"=>"",
        "OPERATION_ID"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //F_MENU_TABLE_LINK
    $tmpConfigForMenuTableLinkIUD= array(
        "JOURNAL_SEQ_NO" => "",
        "JOURNAL_REG_DATETIME" => "", 
        "JOURNAL_ACTION_CLASS" => "",
        "MENU_TABLE_LINK_ID" => "",
        "MENU_ID" => "",
        "TABLE_NAME" => "",
        "KEY_COL_NAME" => "",
        "TABLE_NAME_JNL" => "",
        "NOTE" => "",
        "DISUSE_FLAG" => "", 
        "LAST_UPDATE_TIMESTAMP" => "", 
        "LAST_UPDATE_USER" => ""
    );

    //C_COLLECT_IF_INFO
    $aryConfigForCollectIUD = array(
        "JOURNAL_SEQ_NO" => "",
        "JOURNAL_REG_DATETIME" => "",
        "JOURNAL_ACTION_CLASS" => "",
        "COLLECT_IF_INFO_ID" => "",
        "HOSTNAME" => "",
        "IP_ADDRESS" => "",
        "HOST_DESIGNATE_TYPE_ID" => "",
        "PROTOCOL" => "",
        "PORT" => "",
        "LOGIN_USER" => "",
        "LOGIN_PW" => "",
        "LOGIN_PW_ANSIBLE_VAULT" => "",
        "NOTE" => "",
        "DISUSE_FLAG" => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER" => ""
    );
    $aryCollectValueTmpl = array(
        "JOURNAL_SEQ_NO" => "",
        "JOURNAL_REG_DATETIME" => "",
        "JOURNAL_ACTION_CLASS" => "",
        "COLLECT_IF_INFO_ID" => "",
        "HOSTNAME" => "",
        "IP_ADDRESS" => "",
        "HOST_DESIGNATE_TYPE_ID" => "",
        "PROTOCOL" => "",
        "PORT" => "",
        "LOGIN_USER" => "",
        "LOGIN_PW" => "",
        "LOGIN_PW_ANSIBLE_VAULT" => "",
        "NOTE" => "",
        "DISUSE_FLAG" => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER" => ""
    );

    //C_ANSIBLE_XXX_EXE_INS_MNG :LNS PNS LRL
    $tmpConfigForMovementIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
        "SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "SYMPHONY_INSTANCE_NO"=>"",
        "PATTERN_ID"=>"",
        "I_PATTERN_NAME"=>"",
        "I_TIME_LIMIT"=>"",
        "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
        "I_ANS_PARALLEL_EXE"=>"",
        "I_ANS_WINRM_ID"=>"",
        "I_ANS_PLAYBOOK_HED_DEF"=>"",
        "I_ANS_EXEC_OPTIONS"=>"",
        "OPERATION_NO_UAPK"=>"",
        "I_OPERATION_NAME"=>"",
        "I_OPERATION_NO_IDBH"=>"",
        "I_VIRTUALENV_NAME"=>"",
        "TIME_BOOK"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "FILE_INPUT"=>"",
        "FILE_RESULT"=>"",
        "RUN_MODE"=>"",
        "EXEC_MODE"=>"",
        "MULTIPLELOG_MODE"=>"",
        "LOGFILELIST_JSON"=>"",
        "CONDUCTOR_NAME"=>"",
        "CONDUCTOR_INSTANCE_NO"=>"",
        "COLLECT_STATUS"=>"",
        "COLLECT_LOG"=>"",
        "DISP_SEQ"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>"",
    );

    //A_ROLE_LIST
    $aryConfigForRoleIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "ROLE_ID"=>"",
        "ROLE_NAME"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    //REST共通項目数
    $arrVertivalRestBase =  array(
        0 => "",  #実行処理種別
        1 => "",  #廃止
        2 => "",  #No
        3 => "",  #ホスト名
        4 => "",  #オペレーション/ID
        5 => "",  #オペレーション/オペレーション名
        6 => "",  #オペレーション/基準日時
        7 => "",  #オペレーション/実施予定日時
        8 => "",  #オペレーション/最終実行日時
        9 => "",  #オペレーション/オペレーション
        10 => "",  #代入順序
        13 => "",  #備考
        14 => "",  #最終更新日時
        15 => "",  #更新用の最終更新日時
        16 => ""   #最終更新者
    );

    //Collect対象オーケストレータのディレクトリ、テーブル
    $arrAllowCollectOrc = array( 
        3 => "legacy/ns", 
        4 => "pioneer/ns", 
        5 => "legacy/rl", 
         );
    $arrCollectOrcList=array(
        3 => 'C_ANSIBLE_LNS_EXE_INS_MNG',
        4 => 'C_ANSIBLE_PNS_EXE_INS_MNG',
        5 => 'C_ANSIBLE_LRL_EXE_INS_MNG'
    );

    $arrAnsibleInsMenuID = array( 
        3 => "2100020113", 
        4 => "2100020214", 
        5 => "2100020314", 
    );

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $intWarningFlag               = 0;        // 警告フラグ(1：警告発生)
    $intErrorFlag                 = 0;        // 異常フラグ(1：異常発生)
    
    $aryConductorOnRun = array();
    
    $aryMovement = array();
    
    $boolInTransactionFlag = false;

    $devmode = 0;

    $NOTICE_FLG = 0;

    $RESTEXEC_FLG = 0;

    $FREE_LOG = "";

    ////////////////////////////////
    // グローバル変数宣言         //
    ////////////////////////////////
    global $g;
    
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
            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForAnsibleIfIUD;

            // BIND用のベースソース
            $aryBaseSourceForBind = $aryConfigForAnsibleIfIUD;

            $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0')");

            $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                                   ,"SELECT"
                                                   ,"ANSIBLE_IF_INFO_ID"
                                                   ,"B_ANSIBLE_IF_INFO"
                                                   ,"B_ANSIBLE_IF_INFO_JNL"
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
                $rowOfAnsiInterface = $objQueryUtn->resultFetch();
            } else {
                // 例外処理へ
                $strErrStepIdInFx="00000503";
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            unset($objQueryUtn);
            unset($aryRetBody);


            //メニュー作成未インストール時対応
            $strQuery = " SELECT * FROM D_ANS_CMDB_LINK";
            $retArray = singleSQLCoreExecute($objDBCA, $strQuery, array(), $strFxName);

            if( $retArray[0]!==true ){
                exit(0);
            }


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
                //収集対象のオーケストレータ判定
                if( isset( $arrCollectOrcList[$arySingleOrcInfo['ITA_EXT_STM_ID']] ) == true ){

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

            }

            unset($strOrcIdNumeric);
            unset($strOrcRPath);
            unset($aryOrcListRow);
            //存在するオーケスト—タ分回る----

        //////////////////////////////////
        // RESTベースパラメータ生成 //
        //////////////////////////////////
        
            //RESTのアクセス情報取得

            $arrCollectOrcRPath = array();
            $strCollectTargetDir = "_parameters";
            $strCollectTargetFilesDir_ = "_parameters_file";

            // 更新用のテーブル定義
            $aryConfigForIUD = $aryConfigForCollectIUD;

            // BIND用のベースソース
            $aryBaseSourceForBind = $aryCollectValueTmpl;

            $arySqlBind = array();

            $aryTempForSql = array('WHERE'=>"DISUSE_FLAG IN ('0')");

            $arycollectSVInfo = getCollectSVInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);

            //USER：PWの簡易チェック
            if( $arycollectSVInfo['LOGIN_USER'] == "" || $arycollectSVInfo['LOGIN_PW'] == "" ){
                exit(0);
            }

            //RESTベースパラメータ作成

            $aryParm=array();
            // プロトコル
            $aryParm['protocol'] = $arycollectSVInfo['PROTOCOL'];
            // ホスト名
            if($arycollectSVInfo['HOST_DESIGNATE_TYPE_ID'] == 2 ){
                $aryParm['hostName'] =  $arycollectSVInfo['HOSTNAME'];
            }else{
                $aryParm['hostName'] =  $arycollectSVInfo['IP_ADDRESS'];
            }
            // ポート
            $aryParm['portNo'] =  $arycollectSVInfo['PORT'];

            // ユーザID：パスワード（base64エンコード）
            $tmpUserPw = $arycollectSVInfo['LOGIN_USER'] . ":" . ky_decrypt($arycollectSVInfo['LOGIN_PW']);
            $aryParm['accessKeyId'] = base64_encode($tmpUserPw);

            // コンテントタイプ
            $aryParm['contentType'] = "application/json";
            // REST APIのパス
            $aryParm['requestURI'] = "/default/menu/07_rest_api_ver1.php?no=" ;
            // POSTorGET
            $aryParm['method'] = "POST";
            // X-Command
            $aryParm['xCommand'] = ""; 
            $aryFilter = ""; 
            $aryParm['strParaJsonEncoded'] = json_encode($aryFilter,
                                              JSON_UNESCAPED_UNICODE
                                             ); 

        //オーケストレータ順に実施
        foreach ($arrCollectOrcList as $intCollectOrcNo => $strCollectOrcTablename ) { 

            //---正常完了、未収集のMovement一覧作成
            $strMovKeyname = "EXECUTION_NO";

            $aryConfigForIUD = $tmpConfigForMovementIUD;

            #$aryTempForSql = array('WHERE'=> "STATUS_ID IN ('5') AND COLLECT_STATUS IS NULL AND DISUSE_FLAG IN ('0') ORDER BY EXECUTION_NO ASC ");
            //作業終了　[完了,(完了(異常),想定外エラー,緊急停止,予約取消] + 収集未実行
            $aryTempForSql = array('WHERE'=> "STATUS_ID IN ('5','6','7','8','10') AND COLLECT_STATUS IS NULL AND DISUSE_FLAG IN ('0') ORDER BY EXECUTION_NO ASC ");
            $arySqlBind = array();

            $aryBaseSourceForBind = array();

            $aryMovements =  getInfoFromTablename($objDBCA,$db_model_ch,$strCollectOrcTablename,$strMovKeyname,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
            
            //正常完了、未収集のMovement一覧作成---


            $arrSqlinsertParm = array();
            
            //Movement毎に実施
            foreach ($aryMovements as $aryMovement) {
                $NOTICE_FLG = 0;
                $RESTEXEC_FLG = 0;
                $FREE_LOG = "";
      
                //オーケストレータID
                $varOrchestratorId = $intCollectOrcNo;

                //作業No
                $execNo = $aryMovement['EXECUTION_NO'];
                
                //収取対象のPATH作成
                $strCollectBasePath = $rowOfAnsiInterface['ANSIBLE_STORAGE_PATH_LNX'];                
                $strCollectTargetPath = $strCollectBasePath ."/". $arrAllowCollectOrc[$varOrchestratorId] . "/" . sprintf('%010d', $aryMovement['EXECUTION_NO'] ) . "/in/" . $strCollectTargetDir ;

                //収集結果　ログ出力先
                $tmpCollectlogdir = $root_dir_path . "/uploadfiles/" . $arrAnsibleInsMenuID[$varOrchestratorId] . "/COLLECT_LOG/" . sprintf('%010d', $execNo) ;
                $tmpCollectlogfile = "CollectData_". sprintf('%010d', $execNo) . ".log" ;

                $strCollectlogPath = $tmpCollectlogdir . "/" . $tmpCollectlogfile;
                //ログ出力先チェック、ディレクトリ作成
                if( !is_dir($tmpCollectlogdir) ){
                    #1907　umask退避-umask設定-mkdir,umask戻し
                    $mask = umask();
                    umask(000);
                    if ( mkdir($tmpCollectlogdir,0777,true) ){
                        chmod($tmpCollectlogdir, 0777);
                        umask($mask);
                    }else{
                        umask($mask);
                        exit;
                    }
                }

                //完了の場合
                if( $aryMovement['STATUS_ID'] == 5){
                    //対象のDIRがある場合
                    $aryRetBody = getTargetPath($strCollectTargetPath);

                    //inに無い場合、outを確認
                    if( $aryRetBody == array() ){
                        $strCollectTargetPath = $strCollectBasePath ."/". $arrAllowCollectOrc[$varOrchestratorId] . "/" . sprintf('%010d', $aryMovement['EXECUTION_NO'] ) . "/out/" . $strCollectTargetDir ;
                        $aryRetBody = getTargetPath($strCollectTargetPath);

                    }

                    $arrTargetfiles = $aryRetBody;

                    //---ファイルアップロードカラム対応 #449 
                    //収取対象ファイルのPATH作成              
                    $strCollectTargetFilesPath = $strCollectBasePath ."/". $arrAllowCollectOrc[$varOrchestratorId] . "/" . sprintf('%010d', $aryMovement['EXECUTION_NO'] ) . "/in/" . $strCollectTargetFilesDir_ ;

                    //対象のDIRがある場合
                    $aryRetBody = getTargetPath($strCollectTargetFilesPath);

                    //inに無い場合、outを確認
                    if( $aryRetBody == array() ){
                        $strCollectTargetFilesPath = $strCollectBasePath ."/". $arrAllowCollectOrc[$varOrchestratorId] . "/" . sprintf('%010d', $aryMovement['EXECUTION_NO'] ) . "/out/" . $strCollectTargetFilesDir_ ;
                        $aryRetBody = getTargetPath($strCollectTargetFilesPath);

                    }
                    $arrTargetUploadfiles = $aryRetBody;

                    //ファイルアップロードカラム対応 #449 ---


                    //---ロール情報の取得、確認 #517
                    $strRoleList="";
                    $arrAccessAuth = explode(",", $aryMovement['ACCESS_AUTH']);

                    $aryTempForSql = array('WHERE'=>"ROLE_ID = :ROLE_ID AND DISUSE_FLAG IN ('0') ORDER BY ROLE_ID ASC");
                    
                    // 更新用のテーブル定義
                    $aryConfigForIUD = $aryConfigForRoleIUD;

                    // BIND用のベースソース
                    $aryBaseSourceForBind = $aryConfigForRoleIUD;
                    $tmpRoleList = array();
                    foreach ($arrAccessAuth as $intAccessAuth) {
                        $arySqlBind = array( 
                                            'ROLE_ID' => $intAccessAuth
                                            );

                        $aryRole =  getRoleInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                        
                        if( $aryRole !== false ){
                            $tmpRoleList[] = $aryRole['ROLE_NAME'];
                        }

                       
                    }
                    $strRoleList = implode(",", $tmpRoleList);
                    //ロール情報の取得、確認 #517---

                    //---Operationの取得、確認
                    $intOpeNoUAPK = $aryMovement['OPERATION_NO_UAPK'];
                    
                    //Operationの取得
                    $aryTempForSql = array('WHERE'=>"OPERATION_NO_UAPK = :OPERATION_NO_UAPK AND DISUSE_FLAG IN ('0') ORDER BY OPERATION_NO_UAPK ASC");
                    
                    // 更新用のテーブル定義
                    $aryConfigForIUD = $aryConfigForOpeIUD;
                    
                    // BIND用のベースソース
                    $aryBaseSourceForBind = $aryOpeValueTmpl;

                    $arySqlBind = array( 
                                        'OPERATION_NO_UAPK' => $intOpeNoUAPK
                                        );

                    $aryOperation =  getOperationInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                    
                    //Operationの取得、確認---

                    if( is_array($aryOperation) === true ){

                        //REST用のオペレーションパラメータ
                        $strOpeName = $aryOperation['OPERATION_NAME'];
                        preg_match('|\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}|', $aryOperation['OPERATION_DATE'], $tmpOpedate);
                        $strOpedate = str_replace('-', '/', $tmpOpedate[0]);
                        $strOpeInfo = $strOpedate . "_" . $intOpeNoUAPK . ":" . $strOpeName ;

                        $resultprm = array();
                        $arrTargetLists = array();
                        $arrTargetUploadLists = array();
                        $arrTargetUploadListFullpath = array();
                        $intParseTypeID = 1;    //1:yaml

                        //対象のファイル有の場合
                        if( is_array($arrTargetfiles) && $arrTargetfiles != array() ){
                           
                            //対象ホスト、ファイルのリスト作成
                            foreach ($arrTargetfiles as $strTargetfile) {
                                $targetHosts =  explode( "/", str_replace( $strCollectTargetPath , "" , $strTargetfile ) );
                                $ext = substr($strTargetfile, strrpos($strTargetfile, '.') + 1);
                                $targetFileName =  str_replace( ".".$ext , "" ,  basename($strTargetfile) );
                                $arrTargetLists[$targetHosts[1]][]=$strTargetfile;
                            }
                            
                            //パラメータ取得変換
                            foreach ($arrTargetLists as $strTargetHost => $arrfileslists) {
                                foreach ($arrfileslists as $strTargetfile) {
                                    $ext = substr($strTargetfile, strrpos($strTargetfile, '.') + 1);
                                    $targetFileName =   str_replace( ".".$ext , "" ,  basename($strTargetfile) );
                                    $tmpresultprm = yamlParseAnalysis($strTargetfile);
                                    //yaml形式の場合(yaml_parse_file可の場合)
                                    if( is_array($tmpresultprm)  ){
                                        $resultprm[$strTargetHost][$targetFileName] = $tmpresultprm;
                                    }else{
                                    //収集対象の形式でない場合
                                        #$FREE_LOG = "[処理]収集対象のファイル形式ではありません。( ホスト名: $strTargetHost ファイル名:$targetFileName )";
                                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80010",array( $strTargetHost,$targetFileName ));
                                        outputLog($strCollectlogPath,$FREE_LOG);
                                        
                                        // トレースメッセージ
                                        if ( $log_level === 'DEBUG' ){
                                            require ($root_dir_path . $log_output_php );
                                        }
                                        
                                        $NOTICE_FLG = 3; //対象外
                                    }
                                }
                            }

                            //対象ホスト、ファイルのリスト作成
                            foreach ($arrTargetUploadfiles as $strTargetUploadfile) {
                                $targetHosts =  explode( "/", str_replace( $strCollectTargetPath , "" , $strTargetUploadfile ) );
                                //ホスト名、ファイル名、パス
                                if ( isset($arrTargetUploadLists[$targetHosts[1]][basename($strTargetUploadfile)]) !== true ){
                                    $arrTargetUploadLists[$targetHosts[1]][basename($strTargetUploadfile)]=$strTargetUploadfile;
                                }
                                if ( isset($arrTargetUploadListFullpath[$targetHosts[1]][$strTargetUploadfile]) !== true ){
                                    $arrTargetUploadListFullpath[$targetHosts[1]][$strTargetUploadfile]=$strTargetUploadfile;
                                }
                            }

                            //ホスト毎に実施
                            foreach ($resultprm as $hostname => $parmdata ) {
                                
                                //---ホスト名の取得、確認

                                //ホスト名から対象機器のID取得
                                $aryTempForSql = array('WHERE'=>"HOSTNAME = :HOSTNAME AND DISUSE_FLAG IN ('0') ORDER BY SYSTEM_ID ASC");

                                // 更新用のテーブル定義
                                $aryConfigForIUD = $aryConfigForSTMIUD;
                                
                                // BIND用のベースソース
                                $aryBaseSourceForBind = $arySTMValueTmpl;

                                $arySqlBind = array( 
                                                    'HOSTNAME' => $hostname
                                                    );

                                $aryhostInfo =  getHostInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                                //ホスト名の取得、確認---

                                $arrTableForMenuLists=array();

                                //機器一覧に対象がある場合
                                if( is_array( $aryhostInfo ) === true ){
                                    $hostid = $aryhostInfo['SYSTEM_ID'];
                                    
                                    $arrSqlinsertParm = array();
                                    $arrSqlinsertParmDn = array();
                                    $arrFileUploadList = array();
                                    //ソースファイルからパラメータ整形　（ファイル名、メニューID、項目名、値）
                                    foreach ( $parmdata as $filename => $vardata) {
                                        foreach ($vardata as $varname => $varvalue) {

                                            // 更新用のテーブル定義
                                            $aryConfigForIUD = $aryConfigForCMDBLinkIUD;
                                            
                                            // BIND用のベースソース
                                            $aryBaseSourceForBind = $aryCMDBLinkValueTmpl;

                                            //配列、ハッシュ構造の場合(メンバー変数名あり)
                                            if( is_array( $varvalue ) ){
                                                foreach ($varvalue as $varmember => $varmembermembervalue) {
                                                    $aryBaseSourceForBind=array(
                                                        "PARSE_TYPE_ID" => $intParseTypeID ,
                                                        "FILE_PREFIX" => $filename ,
                                                        "VARS_NAME" => $varname ,
                                                        "VRAS_MEMBER_NAME" => $varmember
                                                    );

                                                    $aryTempForSql = array('WHERE'=> " PARSE_TYPE_ID = :PARSE_TYPE_ID AND FILE_PREFIX = :FILE_PREFIX AND VARS_NAME = :VARS_NAME AND VRAS_MEMBER_NAME = :VRAS_MEMBER_NAME AND DISUSE_FLAG IN ('0') ORDER BY COLUMN_ID ASC");
                                                    $retResult = getInfocmdbLinkInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                                                    
                                                    if( $retResult != array() ){
                                                        foreach ($retResult as $tmpkey => $tmpvalue) {

                                                            $tmpBaseSourceForBind=array(
                                                                "TABLE_NAME" => $retResult[$tmpkey]['TABLE_NAME'] ,
                                                            );
                                                            $tmpTempForSql = array('WHERE'=> " TABLE_NAME = :TABLE_NAME AND PRIVILEGE IN ('1')  ORDER BY LINK_ID ASC");
                                                            $writemenuinfo = getInfoWriteMenuInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$tmpBaseSourceForBind,$tmpTempForSql,$strFxName);
                                                            //メンテンナンス可能なメニューIDを取得
                                                            if( $writemenuinfo != ""){
                                                                $writemenuid = $writemenuinfo['MENU_ID'];
                                                            }else{
                                                                //縦メニューは登録前に、メンテナンス可能な入力用メニュー、ID、テーブルを別途検索
                                                                $writemenuid = $retResult[$tmpkey]['MENU_ID'];
                                                            }
                                                            $arrSqlinsertParm[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $varmembermembervalue;
                                                            $arrTableForMenuLists[$writemenuid]=$retResult[$tmpkey]['TABLE_NAME'];
                                                            if( $retResult[$tmpkey]['COL_CLASS'] == "FileUploadColumn" ){
                                                                $arrFileUploadList[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $retResult[$tmpkey]['COL_CLASS'];
                                                            } 
                                                        }
                                                    }

                                                }
                                            }else{
                                                //メンバ変数無し
                                                $aryBaseSourceForBind=array(
                                                    "PARSE_TYPE_ID" => 1 ,
                                                    "FILE_PREFIX" => $filename ,
                                                    "VARS_NAME" => $varname
                                                );

                                                $aryTempForSql = array('WHERE'=> " PARSE_TYPE_ID = :PARSE_TYPE_ID AND FILE_PREFIX = :FILE_PREFIX AND VARS_NAME = :VARS_NAME AND DISUSE_FLAG IN ('0') ORDER BY COLUMN_ID ASC");
                                                $retResult = getInfocmdbLinkInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);

                                                if( $retResult != array() ){
                                                    foreach ($retResult as $tmpkey => $tmpvalue) {
                                                        //メンテナンス可能なメニューIDの検索
                                                        $tmpBaseSourceForBind=array(
                                                            "TABLE_NAME" => $retResult[$tmpkey]['TABLE_NAME'] ,
                                                        );
                                                        $tmpTempForSql = array('WHERE'=> " TABLE_NAME = :TABLE_NAME AND PRIVILEGE IN ('1')  ORDER BY LINK_ID ASC");
                                                        $writemenuinfo = getInfoWriteMenuInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$tmpBaseSourceForBind,$tmpTempForSql,$strFxName);

                                                        //メンテンナンス可能なメニューIDを取得
                                                        if( $writemenuinfo != ""){
                                                            $writemenuid = $writemenuinfo['MENU_ID'];
                                                        }else{
                                                            //縦メニューは登録前に、メンテナンス可能な入力用メニュー、ID、テーブルを別途検索
                                                            $writemenuid = $retResult[$tmpkey]['MENU_ID'];
                                                        }
                                                        
                                                        //項目 [X]有無 （項目,,,→項目[X],,,)
                                                        if(  !preg_match('/[\[][0-9]*[\]]$/', $retResult[$tmpkey]['COL_TITLE']) ){
                                                            $arrSqlinsertParm[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $varvalue;
                                                            $arrTableForMenuLists[$writemenuid]=$retResult[$tmpkey]['TABLE_NAME'];
                                                            if( $retResult[$tmpkey]['COL_CLASS'] == "FileUploadColumn" ){
                                                                $arrFileUploadList[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $retResult[$tmpkey]['COL_CLASS'];
                                                            } 
                                                        }else{
                                                            $arrSqlinsertParmDn[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $varvalue;
                                                            $arrTableForMenuLists[$writemenuid]=$retResult[$tmpkey]['TABLE_NAME'];
                                                            if( $retResult[$tmpkey]['COL_CLASS'] == "FileUploadColumn" ){
                                                                $arrFileUploadList[$filename][$writemenuid][$retResult[$tmpkey]['COL_TITLE']] = $retResult[$tmpkey]['COL_CLASS'];
                                                            }  
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //代入順序1->X （項目,,,→項目[X],,,)
                                    if( $arrSqlinsertParmDn != array() ){
                                        foreach ($arrSqlinsertParmDn as $tmpfn => $arrrmenu) {
                                            foreach ($arrrmenu as $tmpmid => $tmparrprm) {
                                                if( isset($arrSqlinsertParm[$tmpfn][$tmpmid]) ){
                                                    $arrSqlinsertParm[$tmpfn][$tmpmid] = $arrSqlinsertParm[$tmpfn][$tmpmid] + $arrSqlinsertParmDn[$tmpfn][$tmpmid] ;
                                                }else{                                                   
                                                    $arrSqlinsertParm[$tmpfn][$tmpmid] = $arrSqlinsertParmDn[$tmpfn][$tmpmid] ; 
                                                }   
                                            }
                                        }
                                    }
                                    $tmpFilter = array();

                                    if( $arrSqlinsertParm != array() ){
                                        foreach ( $arrSqlinsertParm as $filename => $tmparr3) {
                                            #$FREE_LOG=" Collect START ( HOSTNAME:$hostname TARGETFILE:$filename )";
                                            $FREE_LOG1 = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80001",array($hostname,$filename));
                                            outputLog($strCollectlogPath,$FREE_LOG1);   
                                            foreach ($tmparr3 as $menuid => $tgtSource_row) {

                                                //メニューID桁埋め
                                                $strmenuid = sprintf('%010d', $menuid);
                                                $strMenuType = "";

                                                //テーブル名
                                                $tablename = $arrTableForMenuLists[$menuid];  
                                                
                                                //テーブル名（縦メニュー）
                                                $tablenameConv = str_replace("_H", "_CONV_H", $arrTableForMenuLists[$menuid]);

                                                //縦メニュー判定
                                                $aryConfigForIUD = $tmpConfigForMenuTableLinkIUD;

                                                $aryTempForSql = array('WHERE'=>"TABLE_NAME =:TABLE_NAME AND DISUSE_FLAG IN ('0') ORDER BY MENU_TABLE_LINK_ID DESC");//ASC
                                                
                                                $arySqlBind = array( 
                                                                    'TABLE_NAME' => $tablenameConv
                                                                    );
                                                $aryBaseSourceForBind = array();

                                                $tmpResult =  getInfoFromTablename($objDBCA,$db_model_ch,"F_MENU_TABLE_LINK","MENU_TABLE_LINK_ID",$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                                                
                                                if( $tmpResult != array() ) {
                                                    $strMenuType = "Vertical";
                                                    $tablename = $tablenameConv;
                                                    $strmenuid = $strmenuid = sprintf('%010d', $tmpResult[0]['MENU_ID']);
                                                }
                                                
                                                
                                                //INFO取得（REST）
                                                $tmpParm = $aryParm;
                                                $tmpParm['xCommand'] = "INFO";
                                                $tmpParm['requestURI'] = $tmpParm['requestURI'] .  $strmenuid ; 
                                                
                                                $tmpRestInfo = execute_rest($tmpParm,$devmode);
                                                if( $tmpRestInfo[0] == 200 ){

                                                    $arrRestInfo =  $tmpRestInfo[1]['CONTENTS']['INFO'];

                                                    // #517
                                                    $tmppramGroup = $objMTS->getSomeMessage("ITAWDCH-MNU-1300001");
                                                    $tmppramName = $objMTS->getSomeMessage("ITAWDCH-MNU-1300002");
                                                    ###$numAccessAuth = "アクセス権/アクセス許可ロール"; 
                                                    $numAccessAuth =  "$tmppramGroup/$tmppramName"; 
                                                    $arrRestAUTH =  array_search( $numAccessAuth , $arrRestInfo );
                                                    
                                                    //登録、更新種別判定用データ検索
                                                    $aryConfigForIUD = $tmpConfigForCMDBbaseIUD;
                                                    if( $strMenuType == "Vertical" )$aryConfigForIUD['INPUT_ORDER']= "";

                                                    $aryTempForSql = array('WHERE'=>"HOST_ID = :HOST_ID AND OPERATION_ID = :OPERATION_ID AND DISUSE_FLAG IN ('0') ORDER BY ROW_ID ASC");

                                                    $arySqlBind = array( 
                                                                        'HOST_ID' => $hostid
                                                                        ,'OPERATION_ID' => $intOpeNoUAPK
                                                                        );

                                                    $aryBaseSourceForBind = array();

                                                    $tmpResult =  getInfoFromTablename($objDBCA,$db_model_ch,$tablename,"ROW_ID",$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName);
                                                    
                                                    $tmpFilter = array();   
                                                    $insertData = array();
                                                    $insertNullflg=array();
                                                    $tmpFilternullflg=array();
                                                    $UpdateFileData = array();

                                                    //RESTパラメータ生成（横メニュー）
                                                    if( $strMenuType == "" ){

                                                        foreach ( $arrRestInfo as $parmNO => $pramName ) {
                                                            if( isset($tgtSource_row[$pramName]) ){
                                                                $insertData[$parmNO]=$tgtSource_row[$pramName];
                                                                if( gettype( $tgtSource_row[$pramName] ) === "NULL" ) $insertNullflg[$parmNO] = 1;

                                                                // #449 ファイルアップロードカラム対応
                                                                if(isset($arrFileUploadList[$filename][$menuid]) == true ){
                                                                    if( array_key_exists($pramName, $arrFileUploadList[$filename][$menuid] ) ){
                                                                        if( isset($arrTargetUploadLists[$hostname][$tgtSource_row[$pramName]] ) == true ){
                                                                            $upload_filepath = $arrTargetUploadLists[$hostname][$tgtSource_row[$pramName]];
                                                                            if( is_file( $upload_filepath ) == true ){
                                                                                $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                            }
                                                                        }else{
                                                                            $matchflg = "";
                                                                            foreach ($arrTargetUploadListFullpath[$hostname] as $tmpfilekey => $tmparrfilepath ) {
                                                                                //フルパス完全一致
                                                                                if( $tmpfilekey == $tgtSource_row[$pramName] ){
                                                                                    $matchflg = "FULL";
                                                                                    #$insertData[$parmNO] = basename($tgtSource_row[$pramName]);
                                                                                    $upload_filepath = $tmparrfilepath;
                                                                                    if( is_file( $upload_filepath ) == true && $insertData[$parmNO] != "" ){
                                                                                        $insertData[$parmNO] = basename($upload_filepath);
                                                                                        $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                                    }
                                                                                }else{
                                                                                    //部分後方一致
                                                                                    if ( preg_match("<{$tgtSource_row[$pramName]}$>", $tmpfilekey) ) {
                                                                                        $matchflg = "AFTER";
                                                                                        #$insertData[$parmNO] = basename($tgtSource_row[$pramName]);

                                                                                        $upload_filepath = $tmparrfilepath;
                                                                                        if( is_file( $upload_filepath ) == true  && $insertData[$parmNO] != "" ){
                                                                                            $insertData[$parmNO] = basename($upload_filepath);
                                                                                            $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                                        }
                                                                                    } 
                                                                                }
                                                                                if( $matchflg != "" )break;
                                                                            }
                                                                            if( $matchflg == ""){
                                                                                $insertData[$parmNO]="";
                                                                                $UpdateFileData[$parmNO] ="";
                                                                            }
                                                                        }
                                                                    }                                                                
                                                                }

                                                            }else{
                                                                $insertData[$parmNO]=null;
                                                            }
                                                        }

                                                        if( $tmpResult == array() ){
                                                            $insertData[0] = $objMTS->getSomeMessage("ITAWDCH-STD-12202"); //登録
                                                        }else{
                                                            $insertData[0] = $objMTS->getSomeMessage("ITAWDCH-STD-12203"); //更新
                                                            $insertData[2] = $tmpResult[0]['ROW_ID'];
                                                            $updeatetimeNo = count($arrRestInfo)-2;
                                                            $insertData[$updeatetimeNo] = $tmpResult[0]['UPD_UPDATE_TIMESTAMP'];
                                                        }

                                                        //共通
                                                        $insertData[3] = $hostname;
                                                        $insertData[9] = $strOpeInfo;

                                                        if( $arrRestAUTH != "" ){
                                                            $insertData[$arrRestAUTH] = $strRoleList;
                                                        }

                                                        ksort($insertData);
                                                        $tmpFilter[] = $insertData;

                                                        if( $insertNullflg != array() ){
                                                            $tmpFilternullflg[] = $insertNullflg;
                                                        }
                                                        // #449 ファイルアップロードカラム対応
                                                        if( $UpdateFileData != array() ){
                                                            #$tmpFilter['UPLOAD_FILE'] = $UpdateFileData;
                                                            $tmpFilter['UPLOAD_FILE'][] = $UpdateFileData;

                                                        }

                                                    }else{
                                                        //RESTパラメータ生成（縦メニュー）
                                                        $insertData = array();
                                                        $UpdateFileData = array();
                                                        $insertNullflg=array();
                                                        $intColmun=0;
                                                        $intColmunnum = count($arrRestInfo) - count($arrVertivalRestBase) +1;


                                                        $regData =  array();
                                                        foreach ($tmpResult as $tmpkey => $tmpvalue) {
                                                            $regData[] = array(
                                                                'ROW_ID' => $tmpvalue['ROW_ID'],
                                                                'UPD_UPDATE_TIMESTAMP' => $tmpvalue['UPD_UPDATE_TIMESTAMP'],
                                                                'INPUT_ORDER' => $tmpvalue['INPUT_ORDER']
                                                            );
                                                            
                                                        }

                                                        $regData =  $tmpResult;

                                                        foreach ($tgtSource_row as $tgtSource_key => $value) {
                                                            foreach ( $arrRestInfo as $parmNO => $pramName ) {
                                                                
                                                                //リピート部分比較用 [項目名[X]除外]
                                                                $tmpColname =preg_replace('/[\[][0-9]*[\]]$/',"",$tgtSource_key);
                                                                
                                                                //項目名：完全一致
                                                                if( $pramName == $tgtSource_key ){
                                                                    if( isset($insertData[10]) !== true ){
                                                                        if( array_key_exists(10, $insertData ) !== true ){
                                                                            $insertData[10] = 1;
                                                                        }else{
                                                                            if( $insertData[10] == "" ){
                                                                                $insertData[10]=1;   
                                                                            }                                                                            
                                                                        }
                                                                    }
                                                                    $insertData[$parmNO]=$value;
                                                                    if( gettype( $value ) == "NULL" || $value == "" ) $tmpFilternullflg[$intColmun][$parmNO] = 1;
                                                                //項目名：リピート部分[X]
                                                                }elseif( $tmpColname == $pramName ){
                                                                    $insertData[10] = str_replace(array('[',']'), "",  mb_eregi_replace($pramName, "", $tgtSource_key) );

                                                                    $insertData[$parmNO]=$value;
                                                                    $intColmun = str_replace(array('[',']'), "",  mb_eregi_replace($pramName, "", $tgtSource_key) )-1; 
                                                                    if( gettype( $value ) == "NULL" || $value == "" ) $tmpFilternullflg[$intColmun][$parmNO] = 1;
                                                                //その他
                                                                }else{
                                                                    if( isset($insertData[$parmNO]) != true )$insertData[$parmNO]=null;
                                                                }

                                                                // #449 ファイルアップロードカラム対応 + #1532
                                                                if(isset($arrFileUploadList[$filename][$menuid]) == true && ($pramName == $tgtSource_key || mb_strpos($tgtSource_key,$pramName) !== false) ){
                                                                    //ファイルアップロードカラムの場合
                                                                    if( array_key_exists($pramName, $arrFileUploadList[$filename][$menuid] ) ){
                                                                        
                                                                        //ファイルアップロード対象リスト（key：ファイル名-valuse:パス）にある場合
                                                                        if( array_key_exists( $value ,$arrTargetUploadLists[$hostname] )  == true ){
                                                                            $upload_filepath = $arrTargetUploadLists[$hostname][$value];
                                                                            if( is_file( $upload_filepath ) == true && $insertData[$parmNO] != ""  ){
                                                                                $insertData[$parmNO] = basename($upload_filepath);
                                                                                $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                            }
                                                                        }else{
                                                                        //ファイルアップロード対象リスト（key：パス-valuse:パス）にある場合
                                                                            $matchflg = "";
                                                                            foreach ($arrTargetUploadListFullpath[$hostname] as $tmpfilekey => $tmparrfilepath ) {
                                                                                $matchflg = "";
                                                                                $upload_filepath = $tmparrfilepath;
                                                                                $upload_filename = basename($upload_filepath);
                                                                                //ファイルフルパス完全/部分一致
                                                                                if( $tmpfilekey == $tgtSource_row[$tgtSource_key] ){
                                                                                    //フルパス完全一致
                                                                                    $matchflg = "FULL";
                                                                                }elseif ( preg_match("<{$tgtSource_row[$tgtSource_key]}$>", $tmpfilekey) ) {
                                                                                    //部分後方一致
                                                                                    $matchflg = "AFTER";
                                                                                }
                                                                                if( $matchflg != "" ){
                                                                                    if( is_file( $upload_filepath ) == true && $insertData[$parmNO] != ""  ){
                                                                                        $insertData[$parmNO] = $upload_filename;
                                                                                        $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                                    }
                                                                                    break;   
                                                                                }
                                                                            }
                                                                            //不要項目除外
                                                                            if( $matchflg == ""){
                                                                                $insertData[$parmNO]="";
                                                                                unset($UpdateFileData[$parmNO]);
                                                                            }
                                                                        }

                                                                        if( $pramName != $tgtSource_key && $tmpColname != $pramName && ( $insertData[10] != 1 && $insertData[10] != null )  ){
                                                                            unset($insertData[$parmNO]);
                                                                            $insertData[$parmNO]="";
                                                                        }
                                                                    }else{
                                                                         //値がNULLの項目を除外　#1050,1051
                                                                        if( array_key_exists($tgtSource_key, $arrFileUploadList[$filename][$menuid] ) ){
                                                                            $tmpColname =preg_replace('/\[[0-9]+?\]/u',"",$tgtSource_key);
                                                                            if( $tmpColname == $pramName ){
                                                                                if( isset($arrTargetUploadLists[$hostname][$value] ) == true ){
                                                                                    $upload_filepath = $arrTargetUploadLists[$hostname][$value];
                                                                                    if( is_file( $upload_filepath ) == true ){
                                                                                        $UpdateFileData[$parmNO] = base64_encode(file_get_contents( $upload_filepath ));
                                                                                    }
                                                                                }else{
                                                                                    $insertData[$parmNO]="";
                                                                                    unset($UpdateFileData[$parmNO]);
                                                                                }                                                                                
                                                                            }
                                                                        }
                                                                    }                                                                
                                                                }

                                                                if(  ( count($arrRestInfo)  ==  count($insertData) ) ){
                                                                    //登録更新種別判定　#1050,1051
                                                                    foreach ( $regData as $regkey => $arrRegDate) {
                                                                        if(  $arrRegDate['INPUT_ORDER'] == $insertData[10] ){
                                                                            $insertData[0] = $objMTS->getSomeMessage("ITAWDCH-STD-12203"); //更新
                                                                            $insertData[2] = $arrRegDate['ROW_ID'];
                                                                            $updeatetimeNo = count($arrRestInfo)-2;
                                                                            $insertData[$updeatetimeNo] = $arrRegDate['UPD_UPDATE_TIMESTAMP'];
                                                                        }
                                                                    }

                                                                    if( $insertData[0] !== $objMTS->getSomeMessage("ITAWDCH-STD-12203") ){
                                                                        $insertData[0] = $objMTS->getSomeMessage("ITAWDCH-STD-12202"); //登録
                                                                    }
                                                                    if(isset($insertData[0])){
                                                                        //共通
                                                                        $insertData[3] = $hostname;
                                                                        $insertData[9] = $strOpeInfo;
                                                                        ksort($insertData);

                                                                        //同一代入順序、パラメータ結合
                                                                        $inputorderwflg=0;
                                                                        foreach ( $tmpFilter as $insertDataNO => $tmpinsertData) {

                                                                            if ( isset( $tmpinsertData[10] ) ){
                                                                                if( $tmpinsertData[10] == $insertData[10] ){
                                                                                    foreach ( $tmpinsertData as $tmpinsertDatakey => $tmpinsertDatavalue) {
                                                                                        if( $tmpinsertDatavalue == "") {
                                                                                            if($tmpFilter[$insertDataNO][$tmpinsertDatakey] === NULL ){
                                                                                                $tmpFilter[$insertDataNO][$tmpinsertDatakey] = $insertData[$tmpinsertDatakey];
                                                                                            }
                                                                                            if( isset($UpdateFileData[$tmpinsertDatakey]) ){
                                                                                                $tmpFilter['UPLOAD_FILE'][$insertDataNO][$tmpinsertDatakey] = $UpdateFileData[$tmpinsertDatakey];
                                                                                            }
                                                                                            $inputorderwflg =1;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        if( $inputorderwflg == 0 ){
                                                                            if( $arrRestAUTH != "" ){
                                                                                $insertData[$arrRestAUTH] = $strRoleList;
                                                                            }
                                                                            //種別、オペレーション、ホスト、代入順序　#1050,1051
                                                                            if( isset($insertData[0]) && isset($insertData[3]) && isset($insertData[9]) && isset($insertData[10]) ){
                                                                                $tmpFilter[$intColmun] = $insertData;
                                                                                
                                                                                // #449 ファイルアップロードカラム対応
                                                                                if( $UpdateFileData != array() ){
                                                                                    #$tmpFilter['UPLOAD_FILE'] = $UpdateFileData;

                                                                                    foreach ($UpdateFileData as $upfileskey => $upfilesval) {
                                                                                        if( $insertData[$upfileskey] == "" ){
                                                                                            unset( $UpdateFileData[$upfileskey] );
                                                                                        }
                                                                                    }

                                                                                    $tmpFilter['UPLOAD_FILE'][$intColmun] = $UpdateFileData;
                                                                                }

                                                                            }

                                                                        }

                                                                        $insertData=array();
                                                                    }
                                                                }else{
                                                                     //同一代入順序、パラメータ結合[X]無し時対応　#1050,1051
                                                                    if( isset($insertData[10]) ){
                                                                        foreach ( $tmpFilter as $insertDataNO => $tmpinsertData) {
                                                                            if ( isset( $tmpinsertData[10] ) ){
                                                                                if( $tmpinsertData[10] == $insertData[10] ){
                                                                                    foreach ( $tmpinsertData as $tmpinsertDatakey => $tmpinsertDatavalue) {
                                                                                        if( isset($insertData[$tmpinsertDatakey]) ) {
                                                                                            if( $insertData[$tmpinsertDatakey] != "" && $tmpFilter[$insertDataNO][$tmpinsertDatakey] == "" ) {
                                                                                                $tmpFilter[$insertDataNO][$tmpinsertDatakey] = $insertData[$tmpinsertDatakey];
                                                                                                if( isset($UpdateFileData[$tmpinsertDatakey]) ){
                                                                                                    $tmpFilter['UPLOAD_FILE'][$insertDataNO][$tmpinsertDatakey] = $UpdateFileData[$tmpinsertDatakey];
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    //登録、更新（EDIT）
                                                    $tmpParm = $aryParm;
                                                    $tmpParm['requestURI'] = $tmpParm['requestURI'] .  $strmenuid ; 
                                                    $tmpParm['xCommand'] = "EDIT";
                                                    
                                                    //値がNULLの項目を除外　#1050,1051
                                                    foreach ( $tmpFilter as $tk => $tarr) {
                                                        if( is_numeric($tk) === true ){
                                                            foreach ( $tarr as $tk1 => $tval) {
                                                                if( gettype($tval) === "NULL" ){
                                                                    if( !isset( $tmpFilternullflg[$tk][$tk1] ) ){
                                                                        unset( $tmpFilter[$tk][$tk1] ); 
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ksort($tmpFilter);
                                                    $tmpParm['strParaJsonEncoded'] = json_encode($tmpFilter,
                                                                                      JSON_UNESCAPED_UNICODE
                                                                                     );
                                                    $RESTEXEC_FLG = 1;
                                                    $arrRestInfo = execute_rest($tmpParm,$devmode);
                                                    if( $arrRestInfo[0] == 200 ){
                                                        #FREE_LOG=" REST DATA ( Hostname: $hostname MenuID: $strmenuid OperationNO: $intOpeNoUAPK )";
                                                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80007", array($hostname,$strmenuid,$intOpeNoUAPK) );
                                                        outputLog($strCollectlogPath,$FREE_LOG, array($arrRestInfo[2],$arrRestInfo[3],json_encode(json_decode($arrRestInfo[4],true),JSON_UNESCAPED_UNICODE) ) );

                                                        if( $arrRestInfo[1]['LIST']['NORMAL']['error']['ct'] != 0 ){
                                                            
                                                            #$FREE_LOG = "[処理]CMDBへのデータ登録、更新に失敗しました。({}件)";
                                                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80003",array( $arrRestInfo[1]['LIST']['NORMAL']['error']['ct'] ."/". count($tmpFilter) ) );
                                                            outputLog($strCollectlogPath,$FREE_LOG);
                                                            
                                                            // トレースメッセージ
                                                            if ( $log_level === 'DEBUG' ){
                                                                require ($root_dir_path . $log_output_php );
                                                            }

                                                            $NOTICE_FLG = 2; //収集済み（通知あり）
                                                        }else{
                                                            //異常がすでにある場合、通知あり
                                                            if( $NOTICE_FLG != 0 ){
                                                                $NOTICE_FLG = 2; //収集済み（通知あり）
                                                            }
                                                        }
     
                                                    }else{
                                                        #$FREE_LOG = "[処理]RESTアクセスに失敗しました。";
                                                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80002");
                                                        outputLog($strCollectlogPath,$FREE_LOG, array($arrRestInfo[2],$arrRestInfo[3],json_encode(json_decode($arrRestInfo[4],true),JSON_UNESCAPED_UNICODE)  ) );
                                                        // トレースメッセージ
                                                        if ( $log_level === 'DEBUG' ){
                                                            require ($root_dir_path . $log_output_php );
                                                        }
                                                        #break;
                                                        $NOTICE_FLG = 2; //収集済み（通知あり）

                                                    }
                                                }else{
                                                    #$FREE_LOG = "[処理]RESTアクセスに失敗しました。"
                                                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80002");
                                                    outputLog($strCollectlogPath,$FREE_LOG);
                                                    // トレースメッセージ
                                                    if ( $log_level === 'DEBUG' ){
                                                        require ($root_dir_path . $log_output_php );
                                                    }

                                                    #FREE_LOG=" REST DATA ( Hostname: $hostname MenuID: $strmenuid OperationNO: $intOpeNoUAPK )";
                                                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80007", array($hostname,$strmenuid,$intOpeNoUAPK) );
                                                    outputLog($strCollectlogPath,$FREE_LOG, array($tmpRestInfo[2],$tmpRestInfo[3],json_encode(json_decode($tmpRestInfo[4],true),JSON_UNESCAPED_UNICODE ) ) );

                                                    // トレースメッセージ
                                                    if ( $log_level === 'DEBUG' ){
                                                        require ($root_dir_path . $log_output_php );
                                                    }
                                                    #break;
                                                    $NOTICE_FLG = 2; //収集済み（通知あり）                                          
                                                }
                                            }
                                            #$FREE_LOG=" Collect END ( HOSTNAME:$hostname TARGETFILE:$filename )";
                                            $FREE_LOG=$objMTS->getSomeMessage("ITAANSIBLEH-STD-80005",array( $hostname , $filename ) );
                                            outputLog($strCollectlogPath,$FREE_LOG );  
                                        }
                                    }else{
                                        //収集項目値管理上の対象ファイル無しの場合
                                        #$FREE_LOG = "[処理]収集項目値管理で指定されたファイルがありません。 ";
                                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80008" );
                                        outputLog($strCollectlogPath,$FREE_LOG); 
                                        
                                        // トレースメッセージ
                                        if ( $log_level === 'DEBUG' ){
                                            require ($root_dir_path . $log_output_php );
                                        }
                                        
                                        if( $RESTEXEC_FLG == 1 ){
                                            $NOTICE_FLG = 2; //収集済み（通知あり）   
                                        }else{
                                            $NOTICE_FLG = 3; ///対象外   
                                        }
                                    }
                                }elseif( $aryhostInfo === false ){
                                    //ホストが存在しない
                                    #$FREE_LOG = "[処理]対象機器が登録されていないか、廃止されています。 hostname:${hostname} "
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80004",array( $hostname ));
                                    outputLog($strCollectlogPath,$FREE_LOG);
                                    
                                    // トレースメッセージ
                                    if ( $log_level === 'DEBUG' ){
                                        require ($root_dir_path . $log_output_php );
                                    }

                                    if( $RESTEXEC_FLG == 1 ){
                                        $NOTICE_FLG = 2; //収集済み（通知あり）   
                                    }else{
                                        $NOTICE_FLG = 4; //収集エラー用    
                                    }
                                    
                                }
                            }
                        }else{
                            //対象のファイル無しの場合
                            #$FREE_LOG = "[処理]収集対象ディレクトリにファイルがありません。 ";
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80009");
                            outputLog($strCollectlogPath,$FREE_LOG);

                            // トレースメッセージ
                             if ( $log_level === 'DEBUG' ){
                                require ($root_dir_path . $log_output_php );
                            }
                            $NOTICE_FLG = 3; //対象外       
                        }
                    }elseif( $aryOperation === false ){
                        //Operationが廃止済み
                        #$FREE_LOG = "[処理]Operationが廃止されています。 OperationNo:${intOpeNoUAPK} ";
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80006",array( $intOpeNoUAPK ));
                        outputLog($strCollectlogPath,$FREE_LOG);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            require ($root_dir_path . $log_output_php );
                        }
                        $NOTICE_FLG = 4; //収集エラー用
                    }

                    $tmpMovement = $aryMovement ;

                    if( file_exists($strCollectlogPath) ){
                        $tmpMovement['COLLECT_LOG'] = $tmpCollectlogfile;

                        switch ( $NOTICE_FLG ) {
                            case '0': // 収集済み：正常終了
                                $tmpMovement['COLLECT_STATUS'] ="1";
                                break;
                            case '2': // 収集済み（通知あり）：REST（登録、更新）で正常終了以外含む、機器一覧に対象ホスト無し
                                $tmpMovement['COLLECT_STATUS'] ="2";
                                break;
                            case '3': //対象外：ファイル無し、YAMLファイル無し、YAMLパース失敗
                                $tmpMovement['COLLECT_STATUS'] ="3";     
                                break;
                            case '4': // 収集エラー：オペレーション廃止済み
                                $tmpMovement['COLLECT_STATUS'] ="4";
                                break;
                        }
                    }else{
                        $tmpMovement['COLLECT_LOG'] = "";
                        $tmpMovement['COLLECT_STATUS'] ="3";
                    }



                    $tmpMovement['LAST_UPDATE_USER'] = $db_access_user_id;

                    $aryRetBody = updateMovmentInstance($objDBCA,$db_model_ch,$tmpConfigForMovementIUD,$db_access_user_id,$tmpMovement,$strCollectOrcTablename,$strMovKeyname,$aryBaseSourceForBind,$strFxName);

                }else{
                ////完了以外の場合対象外へ [(完了(異常),想定外エラー,緊急停止,予約取消]
                    #$FREE_LOG = "[処理]収集機能対象外です。";
                    #$FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-80002");
                    #outputLog($strCollectlogPath,$FREE_LOG);

                    $strMovKeyname = "EXECUTION_NO";
                    $tmpMovement = $aryMovement ;
                    #$tmpMovement['COLLECT_LOG'] = $tmpCollectlogfile;
                    $tmpMovement['COLLECT_LOG'] = "";
                    $tmpMovement['COLLECT_STATUS'] ="3";
                    $tmpMovement['LAST_UPDATE_USER'] = $db_access_user_id;
                    $aryRetBody = updateMovmentInstance($objDBCA,$db_model_ch,$tmpConfigForMovementIUD,$db_access_user_id,$tmpMovement,$strCollectOrcTablename,$strMovKeyname,$aryBaseSourceForBind,$strFxName);
                }
            }

        }

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


//収集サーバの情報の取得
function getCollectSVInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,"COLLECT_IF_INFO_ID"
                                          ,"C_COLLECT_IF_INFO"
                                          ,"C_COLLECT_IF_INFO_JNL"
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
    $aryCollectSV = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryCollectSV[] = $row;
    }

    if( count($aryCollectSV) != 1 ){
        return false;
    }

    //発見行だけループ----
    return $aryCollectSV[0];

}

//オペレーション情報の取得
function getOperationInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,"OPERATION_NO_UAPK"
                                          ,"C_OPERATION_LIST"
                                          ,"C_OPERATION_LIST_JNL"
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
    $aryTargetOpe = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryTargetOpe[] = $row;
    }

    if( count($aryTargetOpe) != 1 ){
        return false;
    }

    //発見行だけループ----
    return $aryTargetOpe[0];

}

//機器情報の取得
function getHostInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,"SYSTEM_ID"
                                          ,"C_STM_LIST"
                                          ,"C_STM_LIST_JNL"
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
    $aryTargetHost = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryTargetHost[] = $row;
    }

    if( count($aryTargetHost) != 1 ){
        return false;
    }

    //発見行だけループ----
    return $aryTargetHost[0];

}

//収集済みリスト
function updateMovmentInstance($objDBCA,$db_model_ch,$tmpConfigForMovementIUD,$db_access_user_id,$aryMovement,$strCollectTablename,$strCollectKeyname,$aryBaseSourceForBind,$strFxName){

    //収集済み一覧登録
    $aryCollectTgtSource = $aryMovement;

    // 更新用のテーブル定義
    $aryConfigForIUD = $tmpConfigForMovementIUD;

    // BIND用のベースソース
    $aryBaseSourceForBindTmp = $aryCollectTgtSource;

    $aryRetBody = insertFromTablename($objDBCA,$db_model_ch,"UPDATE",$strCollectTablename,$strCollectKeyname,$aryConfigForIUD,$aryBaseSourceForBindTmp,$strFxName);

    return $aryRetBody;
}


//ファイル一覧取得（フルパス）
function getTargetPath( $TargetPath ){
    if( is_dir($TargetPath) ){
        //ファイル一覧
        $check_dirs = [ $TargetPath ] ;
        $file_paths = [] ;
        while( $check_dirs ) {
            $dir_path = $check_dirs[0] ;
            if( is_dir ( $dir_path ) && $handle = opendir ( $dir_path ) ) {
                while( ( $file = readdir ( $handle ) ) !== false ) {
                    if( in_array ( $file, [ ".", ".." ] ) !== false ) continue ;
                    $path = rtrim ( $dir_path, "/" ) . "/" . $file ;
                    if ( filetype ( $path ) === "dir" ) {
                        $check_dirs[] = $path ;
                    } else {
                        $file_paths[] = $path;
                    }
                }
            }
            array_shift( $check_dirs ) ;
        }
    }else{
        return false;
    }
    return $file_paths;
}

//yaml配列構造変換
function yamlParseAnalysis($strTargetfile){

    $arrTargetParm = @yaml_parse_file($strTargetfile);

    if( $arrTargetParm == false){
        return false;
    } 

    $arrVarsList = array();
  
    if( $arrTargetParm  != array() ){
        foreach ($arrTargetParm as $key1 => $value1) {
            if( is_array($value1) ){

                # 1897
                foreach ($value1 as $key2 => $value2) {
                    if( !is_array( $value2 ) ){
                        if( is_numeric( $key2 ) ){
                            $arrVarsList[$key1][ '['.$key2.']' ] = $value2 ;
                        }
                    }
                }

                $in_fastarry_f = "";
                $in_var_name = "";
                $in_var_name_path = "";
                $ina_parent_var_array = $value1;
                $ina_vars_chain_list = array();
                $in_error_code = "";
                $in_line = "";
                $in_col_count = 0;
                $in_assign_count = 0;
                $ina_parent_var_key = 0;
                $in_chl_var_key =0;
                $in_nest_lvl = 1;

                $result = MakeMultiArrayToFirstVarChainArray(
                                                    $in_fastarry_f,
                                                    $in_var_name,
                                                    $in_var_name_path,
                                                    $ina_parent_var_array,
                                                    $ina_vars_chain_list,
                                                    $in_error_code,
                                                    $in_line,
                                                    $in_col_count,
                                                    $in_assign_count,
                                                    $ina_parent_var_key,
                                                    $in_chl_var_key,
                                                    $in_nest_lvl
                );

                foreach ( $result as $key2 => $value2) {
                    foreach ($value2 as $ke3y => $value3) {
                        if( !is_array( $value3['VAR_VALUE'] ) ){
                            if( !isset( $arrVarsList[$key1][ $value3['VAR_NAME_PATH'] ] ) ) {
                                $arrVarsList[$key1][ $value3['VAR_NAME_PATH'] ] = $value3['VAR_VALUE'] ;
                            }else{
                                $arrVarsList[$key1][ $value3['VAR_NAME_PATH'] ][] = $value3['VAR_VALUE'] ;
                            }
                        }else{
                            foreach ( $value3['VAR_VALUE'] as $key4 => $value4 ) {
                                if( is_numeric( $key4 ) ){
                                    if( !is_array( $value4 ) ){
                                        //----　　$arrVarsList　に値設定   --------//
                                        $arrVarsList[$key1][ $value3['VAR_NAME_PATH'].'['.$key4.']' ] = $value4 ;
                                    }                              
                                }
                            }
                        }
                    }
                }
            }else{
                $arrVarsList[$key1] = $value1 ;
            }
        }

    }
    return $arrVarsList;
}

//配列構造変換
    function MakeMultiArrayToFirstVarChainArray($in_fastarry_f,
                                                $in_var_name,
                                                $in_var_name_path,
                                                $ina_parent_var_array,
                                               &$ina_vars_chain_list,
                                               &$in_error_code,
                                               &$in_line,
                                               &$in_col_count,
                                               &$in_assign_count,
                                                $ina_parent_var_key,
                                               &$in_chl_var_key,
                                                $in_nest_lvl){
        $demiritta_ch = ".";
        $in_nest_lvl++;
        $parent_var_key = $ina_parent_var_key;
        $ret = is_assoc($ina_parent_var_array);
        if($ret == -1){
            $in_error_code = "ITAANSIBLEH-ERR-70087";
            $in_line       = __LINE__;
            return false;
        }

        $fastarry_f_on = false;
        foreach($ina_parent_var_array as $var => $val) {

            $col_array_f = "";
            // 複数具体値の場合
            if(is_numeric($var)) {
                if( ! is_array($val)){

                    continue;
                }
                else{
                    $col_array_f = "I";
                }
            }
            $MultiValueVar_f = chkMultiValueVariableSub($val);
            if(strlen($in_var_name) != 0){
                $wk_var_name_path = $in_var_name_path . $demiritta_ch . $var;
                if( is_numeric($var) )              $wk_var_name_path = $in_var_name_path ."[".$var."]";
                if( is_numeric($in_var_name_path) ) $wk_var_name_path = "[". $in_var_name_path."]." .$var;

                if(is_numeric($var) === false)

                    $wk_var_name = $in_var_name . $demiritta_ch . $var;
                else
                    $wk_var_name = $in_var_name;
            }
            else{
                $wk_var_name_path = $var;
                $wk_var_name = $var;
            }
            // 配列の開始かを判定する。
            if($col_array_f == "I"){
                if($in_fastarry_f === false){
                    $in_fastarry_f = true;
                    $fastarry_f_on = true;
                }
            }   
            $in_chl_var_key++;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME']       = $var;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['NEST_LEVEL']     = $in_nest_lvl;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['LIST_STYLE']     = "0";
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME_PATH']  = $wk_var_name_path;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_NAME_ALIAS'] = $wk_var_name;
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['ARRAY_STYLE']    = "0";
            $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['VAR_VALUE']       = $val;
            $MultiValueVar_f = chkMultiValueVariableSub($val);
            if($MultiValueVar_f===true){
                $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['LIST_STYLE'] = "5";
            }
            // 配列の中の変数の場合
            if($in_fastarry_f === true){
                $ina_vars_chain_list[$parent_var_key][$in_chl_var_key]['ARRAY_STYLE'] = "1";
            }
            if( ! is_array($val)) {
                continue;
            }

            $ret = MakeMultiArrayToFirstVarChainArray($in_fastarry_f,
                                                             $wk_var_name,
                                                             $wk_var_name_path,
                                                             $val,
                                                             $ina_vars_chain_list,
                                                             $in_error_code,
                                                             $in_line,
                                                             $in_col_count,
                                                             $in_assign_count,
                                                             $in_chl_var_key,
                                                             $in_chl_var_key,
                                                             $in_nest_lvl);
            if($ret === false){
                return false;
            }
            // 配列開始のマークを外す
            if($fastarry_f_on === true){
                $in_fastarry_f = false;
            }               
        }

        return $ina_vars_chain_list;
    }

//配列構造チェック
function chkMultiValueVariableSub($in_var_array){
    if(is_array($in_var_array)){
        if(count($in_var_array) == 0){
            return true;
        }
        foreach($in_var_array as $key => $chk_array){
            if( ! is_numeric($key)){
                return false;
            }
            if(is_array($chk_array)){
                return false;
            }
        }
        return true;
    }
    return false;
}

//配列値チェック
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


//収集項目紐づけ
function getInfocmdbLinkInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $varTableName = "D_ANS_CMDB_LINK";

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,"COLUMN_ID"
                                          ,$varTableName
                                          ,$varTableName . "_JNL"
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

    foreach ($aryBaseSourceForBind as $key => $value) {
        $aryUtnSqlBind[$key] = $value;
    }

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

function getInfoWriteMenuInfo($objDBCA,$db_model_ch,$aryConfigForIUD,$arySqlBind,$aryTempForSql,$strFxName){
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryDataSet = array();
    $aryForBind = array();

    $strQuery = "SELECT DISTINCT "
                ." TAB_A.MENU_ID AS MENU_ID ,"
                ." TAB_A.PRIVILEGE AS PRIVILEGE"
               ." FROM "
               ." A_ROLE_MENU_LINK_LIST TAB_A "
               ."LEFT JOIN "
               ." F_MENU_TABLE_LINK TAB_B "
               ." ON TAB_B.MENU_ID = TAB_A.MENU_ID "
               ."WHERE "
               ."    TAB_A.DISUSE_FLAG IN ('0') "
               ."AND TAB_B.DISUSE_FLAG IN ('0') "
               ."AND "
               ."";

    foreach ($aryTempForSql as $key =>  $value) {
       $strQuery = $strQuery . $value;
    }

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
    }

    if( count($aryRowOfMovClassTable) == 1 ){
        return $aryRowOfMovClassTable[0];
    }else{
        return false;
    }
    
}

//Rest実行
function execute_rest($aryParm,$devmode=1){

    ////////////////////////////////
    // RequestHeader作成          //
    ////////////////////////////////
    $Header = array("Host: "            . $aryParm['hostName'] . ":" . $aryParm['portNo'],
                    "Content-Type: "    . $aryParm['contentType'],
                    "Authorization: "   . $aryParm['accessKeyId'],
                    "X-Command: "       . $aryParm['xCommand'] ,
                   );

    ////////////////////////////////
    // HTTPコンテキスト作成       //
    ////////////////////////////////
    $HttpContext = array( "http" => array('method'        => $aryParm['method'],
                                          'header'        => implode("\r\n", $Header),
                                          'content'       => $aryParm['strParaJsonEncoded'],
                                          'ignore_errors' => true,
                                          'timeout'       => 10,
                                         ),
                          "ssl" => array('verify_peer' => false,
                                         'verify_peer_name' => false,
                                        )
                        );

    ////////////////////////////////
    // REST APIアクセス           //
    ////////////////////////////////
    $http_response_header = null;

    if($devmode == 1 )echo "\n\nRequest URL\n" . $aryParm['protocol'] . "://" . $aryParm['hostName'] . ":" . $aryParm['portNo'] . $aryParm['requestURI'] . "\n";
    $ResponsContents = file_get_contents( $aryParm['protocol'] . "://" . $aryParm['hostName'] . ":" . $aryParm['portNo'] . $aryParm['requestURI'],
                                           false,
                                           stream_context_create($HttpContext) );

    ////////////////////////////////
    // 通信結果を判定             //
    ////////////////////////////////
    if($devmode == 1 )echo print_r($ResponsContents);
    if($devmode == 1 )echo print_r($http_response_header);
    if( count( $http_response_header ) > 0 ){
        ////////////////////////////////
        // HTTPレスポンスコード取得   //
        ////////////////////////////////
        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status_code = $matches[1];

        ////////////////////////////////
        // 返却用のArrayを編集        //
        ////////////////////////////////
        $respons_array['StatusCode']      = ( int ) $status_code;
        $respons_array['ResponsContents'] = json_decode( $ResponsContents, true );
    } else{
        ////////////////////////////////
        // 返却用のArrayを編集        //
        ////////////////////////////////
        $respons_array['StatusCode']      = ( int ) -2;
        $respons_array['ResponsContents'] = array( "ErrorMessage" => "HTTP Socket Timeout" );
    }

    ////////////////////////////////
    // 結果を返却                  //
    ////////////////////////////////
    if($devmode == 1 )echo "\n\nHTTP Respons Contents\n";
    if($devmode == 1 )echo print_r($respons_array);
    if($devmode == 1 )echo "\n";

    if($respons_array['StatusCode'] != 200){
        $respons_array['ResponsContents']['resultdata']=array();
    }
    return array(
        $respons_array['StatusCode'] ,
        $respons_array['ResponsContents']['resultdata'],
        $aryParm['protocol'] . "://" . $aryParm['hostName'] . ":" . $aryParm['portNo'] . $aryParm['requestURI'],
        $HttpContext['http']['content'],
        $ResponsContents
    );

}


//
function getInfoFromTablename($objDBCA,$db_model_ch,$tablename,$primarykey,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,$primarykey
                                          ,$tablename
                                          ,$tablename. "_JNL"
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

//
function insertFromTablename($objDBCA,$db_model_ch,$sqlType,$tablename,$primarykey,$aryConfigForIUD,$aryBaseSourceForBind,$strFxName){


    // ----SYM-INSTANCE-シーケンスを掴む
    $retArray = getSequenceLockInTrz($tablename. '_JSQ','A_SEQUENCE');
    if( $retArray[1] != 0 ){
        // エラーフラグをON
        // 例外処理へ
        $strErrStepIdInFx="00000400";
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    $retArray = getSequenceLockInTrz($tablename. '_RIC','A_SEQUENCE');
    if( $retArray[1] != 0 ){
        // エラーフラグをON
        // 例外処理へ
        $strErrStepIdInFx="00000500";
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    // -SYM-INSTANCE-シーケンスを掴む----

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                        ,$sqlType
                                        ,$primarykey
                                        ,$tablename
                                        ,$tablename. "_JNL"
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
    $aryRetBody = getSequenceValueFromTable( $tablename . '_JSQ', 'A_SEQUENCE', FALSE );
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


function outputLog($logPath,$subject="",$messages=""){

    error_log(print_r( date('Y-m-d H:i:s') . " " . $subject . "\n", true), 3, $logPath );

    if( $messages != "" || $messages != array() ){
        error_log(print_r( $messages, true ), 3, $logPath );

    }
}


//ロール情報の取得
function getRoleInfo($objDBCA,$db_model_ch,$arySqlBind,$aryConfigForIUD,$aryBaseSourceForBind,$aryTempForSql,$strFxName){

    $aryRetBody = makeSQLForUtnTableUpdate($db_model_ch
                                          ,"SELECT"
                                          ,"ROLE_ID"
                                          ,"A_ROLE_LIST"
                                          ,"A_ROLE_LIST_JNL"
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
    $aryTargetRole = array();
    while ( $row = $objQueryUtn->resultFetch() ){
        $aryTargetRole[] = $row;
    }

    if( count($aryTargetRole) == 0 ){
        return false;
    }

    //発見行だけループ----
    return $aryTargetRole[0];

}

?>
