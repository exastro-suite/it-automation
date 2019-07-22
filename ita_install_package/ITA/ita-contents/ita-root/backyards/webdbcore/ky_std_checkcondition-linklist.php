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
    $db_access_user_id  = -2; //----(-1=>-2)
    
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    
    $aryDUListOnAccount = array();
    $aryDUListOnMenu    = array();
    $aryDUListOnRole    = array();
    
    $tmpAddWhereAccount = "";
    $tmpAddWhereMenu    = "";
    $tmpAddWhereRole    = "";
    
    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;
    
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
        
        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000100")) );
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50004");
            require ($root_dir_path . $log_output_php );
        }
        
        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // ----01.ユーザの廃止行をリストアップする

        // SQL作成
        $sql = "SELECT USER_ID 
                FROM   A_ACCOUNT_LIST 
                WHERE  DISUSE_FLAG = '1' 
                FOR UPDATE";
        
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000200")) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000300")) );
        }
        
        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $aryDUListOnAccount[] = $row['USER_ID'];
        }
        
        // DBアクセス事後処理
        unset($objQuery);
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50006",count($aryDUListOnAccount));
            require ($root_dir_path . $log_output_php );
        }
        
        if( 0 < count($aryDUListOnAccount) ){
            $tmpAddWhereAccount = "USER_ID IN (".implode(",",$aryDUListOnAccount).")";
        }
        
        // 01.ユーザの廃止行をリストアップする----

        // ----02.メニューの廃止行をリストアップする
        // SQL作成
        $sql = "SELECT MENU_ID 
                FROM   A_MENU_LIST 
                WHERE  DISUSE_FLAG = '1' 
                FOR UPDATE";
        
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }
        
        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $aryDUListOnMenu[] = $row['MENU_ID'];
        }
        
        // DBアクセス事後処理
        unset($objQuery);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50007",count($aryDUListOnMenu));
            require ($root_dir_path . $log_output_php );
        }
        
        if( 0 < count($aryDUListOnMenu) ){
            $tmpAddWhereMenu = "MENU_ID IN (".implode(",",$aryDUListOnMenu).")";
        }
        
        // 02.メニューの廃止行をリストアップする----

        // ----03.ロールの廃止行をリストアップする
        // SQL作成
        $sql = "SELECT ROLE_ID 
                FROM   A_ROLE_LIST 
                WHERE  DISUSE_FLAG = '1' 
                FOR UPDATE";
        
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
        }
        
        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $aryDUListOnRole[] = $row['ROLE_ID'];
        }
        
        // DBアクセス事後処理
        unset($objQuery);
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50008",count($aryDUListOnRole));
            require ($root_dir_path . $log_output_php );
        }

        if( 0 < count($aryDUListOnRole) ){
            $tmpAddWhereRole = "ROLE_ID IN (".implode(",",$aryDUListOnRole).")";
        
        }
        
        // 03.ロールの廃止行をリストアップする----

        // ----04.ロール・ユーザのシーケンス・ロック取得
        // ----シーケンスを掴む
        $retArray = getSequenceLockInTrz('JSEQ_A_ROLE_ACCOUNT_LINK_LIST','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
        }
        // シーケンスを掴む----
        // 04.ロール・ユーザのシーケンス・ロック取得----

        // ----05.ロール・メニューのシーケンス・ロック取得
        // ----シーケンスを掴む
        $retArray = getSequenceLockInTrz('JSEQ_A_ROLE_MENU_LINK_LIST','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
        }
        // シーケンスを掴む----
        // 05.ロール・メニューのシーケンス・ロック取得----
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50009");
            require ($root_dir_path . $log_output_php );
        }
        
        // ----06.ロール・ユーザの更新
        $tmpAddWhere = "";
        if( $tmpAddWhereRole != "" || $tmpAddWhereAccount != "" ){
            $tmpAddWhere = "AND (";
            if( $tmpAddWhereRole != "" ){
                $tmpAddWhere .= $tmpAddWhereRole;
            }
            
            if( $tmpAddWhereAccount != "" ){
                if( $tmpAddWhereRole != "" ){
                    $tmpAddWhere .= " OR ";
                }
                $tmpAddWhere .= $tmpAddWhereAccount;
            }
            $tmpAddWhere .= ")";
            
            $arrayConfig = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LINK_ID"=>"",
                "USER_ID"=>"",
                "ROLE_ID"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LINK_ID"=>"",
                "USER_ID"=>"",
                "ROLE_ID"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );
            
            $arrayValue = $arrayValueTmpl;
            
            $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ".$tmpAddWhere);
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT FOR UPDATE",
                                            "LINK_ID",
                                            "A_ROLE_ACCOUNT_LINK_LIST",
                                            "A_ROLE_ACCOUNT_LINK_LIST_JNL",
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array
            );
            
            $aryResult01 = array();
            if( $retArray[0] === false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
            }
            
            $r = $objQueryUtn->sqlExecute();
            if(!$r){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
            }
            
            //----発見行だけループ
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryResult01[] = $row;
            }
            //発見行だけループ----
            
            unset($objQueryUtn);
            
            foreach($aryResult01 as $key=>$val){
                $retArray = getSequenceValueFromTable('JSEQ_A_ROLE_ACCOUNT_LINK_LIST', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
                }
                
                $varJSeq = $retArray[0];
                
                // ----ジャーナルSEQ.NO、廃止フラグ、最終更新者の更新
                $intLinkId               = $val['LINK_ID'];
                $val['JOURNAL_SEQ_NO']   = $varJSeq;
                $val['DISUSE_FLAG']      = '1';
                $val['LAST_UPDATE_USER'] = $db_access_user_id;
                // ジャーナルSEQ.NO、廃止フラグ、最終更新者の更新----
                
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                        "UPDATE",
                        "LINK_ID",
                        "A_ROLE_ACCOUNT_LINK_LIST",
                        "A_ROLE_ACCOUNT_LINK_LIST_JNL",
                        $arrayConfig,
                        $val );
                
                // ----共通ロジック[1]
                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];
                
                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                
                if( $retArray[0] === false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
                }
                
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                
                if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
                }
                
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                
                //----SQL実行
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50013",$intLinkId);
                    require ($root_dir_path . $log_output_php );
                }
                
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50014",$intLinkId);
                    require ($root_dir_path . $log_output_php );
                }
                //SQL実行----
                
                // 共通ロジック[1]----
                
                //SQL実行----
                unset($objQueryUtn);
                unset($objQueryJnl);
                //ループ----
            }
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50010");
            require ($root_dir_path . $log_output_php );
        }
        
        // 06.ロール・ユーザの更新----
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50011");
            require ($root_dir_path . $log_output_php );
        }
        
        // ----07.ロール・メニューの更新
        $tmpAddWhere = "";
        if( $tmpAddWhereRole != "" || $tmpAddWhereMenu != "" ){
            $tmpAddWhere = "AND (";
            if( $tmpAddWhereRole != "" ){
                $tmpAddWhere .= $tmpAddWhereRole;
            }
            
            if( $tmpAddWhereMenu != "" ){
                if( $tmpAddWhereRole != "" ){
                    $tmpAddWhere .= " OR ";
                }
                $tmpAddWhere .= $tmpAddWhereMenu;
            }
            $tmpAddWhere .= ")";
            
            $arrayConfig = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LINK_ID"=>"",
                "MENU_ID"=>"",
                "ROLE_ID"=>"",
                "PRIVILEGE"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );
            
            $arrayValueTmpl = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "LINK_ID"=>"",
                "MENU_ID"=>"",
                "ROLE_ID"=>"",
                "PRIVILEGE"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );
            
            $arrayValue = $arrayValueTmpl;
            
            $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' ".$tmpAddWhere);
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT FOR UPDATE",
                                            "LINK_ID",
                                            "A_ROLE_MENU_LINK_LIST",
                                            "A_ROLE_MENU_LINK_LIST_JNL",
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array
            );
            
            $aryResult02 = array();
            if( $retArray[0] === false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002100")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002200")) );
            }
            
            $r = $objQueryUtn->sqlExecute();
            if(!$r){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002300")) );
            }
            
            //----発見行だけループ
            while ( $row = $objQueryUtn->resultFetch() ){
                $aryResult02[] = $row;
            }
            //発見行だけループ----
            
            unset($objQueryUtn);
            
            foreach($aryResult02 as $key=>$val){
                $retArray = getSequenceValueFromTable('JSEQ_A_ROLE_MENU_LINK_LIST', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002400")) );
                }
                
                $varJSeq = $retArray[0];
                
                // ----ジャーナルSEQ.NO、廃止フラグ、最終更新者の更新
                $intLinkId               = $val['LINK_ID'];
                $val['JOURNAL_SEQ_NO']   = $varJSeq;
                $val['DISUSE_FLAG']      = '1';
                $val['LAST_UPDATE_USER'] = $db_access_user_id;
                // ジャーナルSEQ.NO、廃止フラグ、最終更新者の更新----
                
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                        "UPDATE",
                        "LINK_ID",
                        "A_ROLE_MENU_LINK_LIST",
                        "A_ROLE_MENU_LINK_LIST_JNL",
                        $arrayConfig,
                        $val );
                
                // ----共通ロジック[1]
                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];
                
                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                
                if( $retArray[0] === false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002500")) );
                }
                
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
                
                if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002600")) );
                }
                
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002700")) );
                }
                
                //----SQL実行
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002800")) );
                }
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50013",$intLinkId);
                    require ($root_dir_path . $log_output_php );
                }
                
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00002900")) );
                }
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50014",$intLinkId);
                    require ($root_dir_path . $log_output_php );
                }
                //SQL実行----
                
                // 共通ロジック[1]----
                
                unset($objQueryUtn);
                unset($objQueryJnl);
                //ループ----
            }
        }
        // 07.ロール・ユーザの更新----
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50012");
            require ($root_dir_path . $log_output_php );
        }
        
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-50003",array(__FILE__,__LINE__,"00003000")) );
                                        
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
            $error_flag   != 0        ||
            $warning_flag != 0        ){
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
        if( $transaction_flag ){
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50005");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50004");
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(0);
    }
?>
