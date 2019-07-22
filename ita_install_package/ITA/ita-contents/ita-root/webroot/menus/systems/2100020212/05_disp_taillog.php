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
    //  【特記事項】
    //      オーケストレータ別の設定記述あり
    //
    //////////////////////////////////////////////////////////////////////
    
    // 各種ローカル定数を定義
    $intNumPadding = 10;
    
    //----オーケストレータ別の設定記述
    
    $strIfTableIdForSelect       = 'B_ANSIBLE_IF_INFO';
    
    $strColIdOfDRSRPathFromWebSv = 'ANSIBLE_STORAGE_PATH_LNX';
    $strColIdOfTailLine          = 'ANSIBLE_TAILLOG_LINES';
    $strColIdOfOfRefreshInt      = 'ANSIBLE_REFRESH_INTERVAL';
    
    $strOrchestratorPath        = "pioneer/ns";
    //----オーケストレータ別の設定記述----
    
    // 各種ローカル変数を定義
    $error_flag     = 0;        // システムエラーフラグ
    $db_access_flag = false;    // DBアクセス実施中フラグ
    
    try{
        ////////////////////////////////
        // ルートディレクトリを取得   //
        ////////////////////////////////
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        
        ////////////////////////////////
        // 定数定義                   //
        ////////////////////////////////
        $db_connect_php = '/libs/commonlibs/common_db_connect.php';
        
        // 共通PHPファンクションをインクルード
        $aryOrderToReqGate = array("DBConnect"=>"LATE");
        require_once("{$root_dir_path}/libs/commonlibs/common_php_req_gate.php");
        
        // メニューのディレクトリを取得
        if(array_key_exists('no', $_GET)){
            $g['page_dir']  = $_GET['no'];
        }
        
        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        $exec_log_caption  = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-2000000");
        $error_log_caption = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-2000001");

        $prg_recorder_array = array(1=>array('PRG_RCDR_ID'=>'1'
                                            ,'PRG_RCDR_NAME'=>$exec_log_caption
                                            ,'PRG_FILE_NAME'=>'exec.log')
                                    ,2=>array('PRG_RCDR_ID'=>'2'
                                            ,'PRG_RCDR_NAME'=>$error_log_caption
                                            ,'PRG_FILE_NAME'=>'error.log')
        );
    
        ////////////////////////////////////////////////////////////////
        // ANSIBLEインタフェース情報を取得                                //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT {$strColIdOfDRSRPathFromWebSv} DRS_ROOT_PATH_FROM_ITAWEB "
                .",{$strColIdOfTailLine} TAILLOG_LINES "
                .",{$strColIdOfOfRefreshInt} REFRESH_INTERVAL "
                ."FROM   {$strIfTableIdForSelect} "
                ."WHERE  DISUSE_FLAG = '0' ";
        
        // DBアクセス実施中フラグをON
        $db_access_flag = true;
        
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // システムエラーフラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-501",array($strIfTableIdForSelect)) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // システムエラーフラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-502",array($strIfTableIdForSelect)) );
        }
        
        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $row_if_info = $row;
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();
        
        // 単一行セレクトでない場合はNG
        if( $num_of_rows != 1 ){
            // システムエラーフラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-503",array($strIfTableIdForSelect)) );
        }
        
        // DBアクセス実施中フラグをOFF
        $db_access_flag = false;
        
        // DBアクセス事後処理
        unset($objQuery);
        
        $lines    = $row_if_info['TAILLOG_LINES']; // 表示する末尾の行数
        $interval = $row_if_info['REFRESH_INTERVAL']; // 最新化のインターバルタイム(msec)
        
        // tail対象をtail(読み込み)
        if (isset($_GET['load'])){
            // ANSIBLEインタフェース情報をローカル変数に格納
            $drs_root_path_from_itaweb  = $row_if_info['DRS_ROOT_PATH_FROM_ITAWEB'];
            
            // メニュー情報取得パーツ
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
            
            $array_except_referer = array($ACRCM_representative_file_name,$_SERVER['PHP_SELF']);
            // access系共通ロジックパーツ01
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_access_01.php");
            
            // クエリ「execution_no」の存在判定と成功時の取得
            if( !array_key_exists( "execution_no", $_GET ) ){
                // システムエラーフラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-504") );
            }
            else{
                // クエリからexecution_noを取得
                $execution_no = $_GET["execution_no"];
                
                $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($execution_no) === false ){
                    // システムエラーフラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-505") );
                }
                unset($objIntNumVali);
            }
            
            // クエリ「prg_recorder」の存在判定と成功時の取得
            if( !array_key_exists( "prg_recorder", $_GET ) ){
                // システムエラーフラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-506") );
            }
            else{
                // クエリからprg_recorderを取得
                $prg_record_file_id = $_GET["prg_recorder"];
                
                $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($prg_record_file_id) === false ){
                    // システムエラーフラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-507") );
                }
                unset($objIntNumVali);
                
                if( array_key_exists($prg_record_file_id,$prg_recorder_array)===false ){
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-510") );
                }
                
                $prg_record_file_name = $prg_recorder_array[$prg_record_file_id]['PRG_FILE_NAME'];
                
            }
            
            ////////////////////////////////////////////////////////////////
            // tail対象のファイルパスを作成                               //
            ////////////////////////////////////////////////////////////////
            $if_dir                         = $drs_root_path_from_itaweb . "/" . $strOrchestratorPath . "/" . str_pad( $execution_no, $intNumPadding, "0", STR_PAD_LEFT );
            $if_file_repo_out               = 'out';
            $if_dir_out                     = $if_dir . "/" . $if_file_repo_out;
            $prg_record_file_name_fullpath  = $if_dir_out . "/" . $prg_record_file_name;
            
            ////////////////////////////////////////////////////////////////
            // テンポラリのファイル名を作成                               //
            ////////////////////////////////////////////////////////////////
            $temp_dir = $root_dir_path . '/temp';
            $temp_file_name_fullpath_1  = $temp_dir . "/" .  $prg_record_file_name . "_1_" .  date("YmdHis") . "_" . mt_rand();
            $temp_file_name_fullpath_2  = $temp_dir . "/" .  $prg_record_file_name . "_2_" .  date("YmdHis") . "_" . mt_rand();

            // ファイルステータスのキャッシュをクリア
            clearstatcache();
            
            if ( !file_exists($prg_record_file_name_fullpath) || is_dir( $prg_record_file_name_fullpath ) ){
                // 例外処理へ(例外ではないが)
                
                throw new Exception( '<div id="tail_show" style="display:none;"></div>'.$objMTS->getSomeMessage("ITAANSIBLEH-ERR-511") );
            }
            
            // ログファイルの内容を展開
            $file_data = file_get_contents( $prg_record_file_name_fullpath );
            
            // 文字コード判定
            if( $file_data != mb_convert_encoding( $file_data , 'UTF-8', 'UTF-8' ) ){
                // 文字コードをUTF-8に変換してからテンポラリファイルに出力
                file_put_contents( $temp_file_name_fullpath_1, mb_convert_encoding( $file_data, 'UTF-8', 'ASCII,JIS,UTF-8,SJIS-win' ), LOCK_EX );
            }
            else{
                // そのままテンポラリファイルに出力
                file_put_contents( $temp_file_name_fullpath_1, $file_data, LOCK_EX );
            }
            
            // フィルタ文字列があるか判定
            $filter_string = "";
            if( !empty($_GET['filter_string']) ){
                // フィルタ文字列
                $filter_string = urldecode($_GET['filter_string']);
                
                // フィルタ文字列をエスケープ
                $filter_string_escape = preg_quote($filter_string);
                
                $strMatchLineOnly = "";
                if( isset($_GET['match_line_only']) ){
                    $strMatchLineOnly =$_GET['match_line_only'];
                }
                
                if( $strMatchLineOnly == "on" ){
                    // フィルタしてコピー
                    $command_string = "grep '" . $filter_string_escape . "' < " . $temp_file_name_fullpath_1 . " > " . $temp_file_name_fullpath_2;
                    shell_exec( $command_string );
                }
                else{
                    copy( $temp_file_name_fullpath_1, $temp_file_name_fullpath_2 );
                }
            }
            else{
                // そのままコピー
                copy( $temp_file_name_fullpath_1, $temp_file_name_fullpath_2 );
            }
            
            echo '<div id="tail_show" style="display:none;"></div>';
            // tail処理を実施
            foreach (read_tail( $temp_file_name_fullpath_2, $lines ) as $i => $line){
                $line = rtrim($line,"\r\n");
                
                if( !empty($filter_string) ){
                    echo strtr(htmlspecialchars($line,ENT_QUOTES),array("\t" => '    ', $filter_string => "<span class=generalErrMsg><b>" . $filter_string . "</b></span>" ));
                }
                else{
                    echo strtr(htmlspecialchars($line,ENT_QUOTES),array("\t" => '    '));
                }
                if ( $i < ($lines - 1 ) ){
                    echo '<br>';
                }
            }
            
            
            // テンポラリファイルをお掃除
            unlink( $temp_file_name_fullpath_1 );
            unlink( $temp_file_name_fullpath_2 );
            
            // アクセスログ出力
            web_log( $objMTS->getSomeMessage("ITAWDCH-STD-603") );
            
            // 処理終了
            exit;
        }
        
        // DBアクセスを伴う処理を開始
        try{
            // メニュー情報取得パーツ
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
            
            // reg_n_up系共通ロジックパーツ01
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_reg_n_up_01.php");
            
            // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
            // (この処理は非テンプレートのコンテンツのみに必要)
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
        }
        catch (Exception $e){
            // DBアクセス例外処理パーツ
            require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
        }
        
        // 共通HTMLステートメントパーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
        
        // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
        $timeStamp_itabase_orchestrator_drive_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_orchestrator_drive_style.css");
        $timeStamp_05_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/05_javascript.js");
        
        print 
<<< EOD
        <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/05_javascript.js?{$timeStamp_05_javascript_js}"></script>
        <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_orchestrator_drive_style.css?{$timeStamp_itabase_orchestrator_drive_style_css}">
        </head>
        <body>
            <div id="iframeInDispTail">
                <table border="0">
                    <tr>
                        <!--//フィルタ-->
                        <td style="padding-right:10px">{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-508030")}：</td>
                        <td>
                            <input onkeydown="pre_filter(event.keyCode)" type="text" id="filter_string" name="filter_string" size="20" maxlength="256">
                        </td>
                        <td style="padding-right:10px">
                            <!--//該当行のみ表示-->
                            <input type="checkbox" id="match_line_only" name="match_line_only" value="on" onClick="pre_filter('13')" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-508040")}
                        </td>
                    </tr>
                </table>
                <br>
                <pre id="console"></pre>
            </div>
        <div id="interval" style="display:none;">$interval</div>
        <div id="before_height" style="display:none;"></div>
        <div id="stop_update" style="display:none;"></div>
        </body>
        </html>
EOD;
        
        // アクセスログ出力
        web_log( $objMTS->getSomeMessage("ITAWDCH-STD-603") );
    }
    catch (Exception $e){
        // DBアクセス実施中フラグがONの場合
        if( $db_access_flag == true ){
            // DBアクセス事後処理
            unset($objQuery);
        }
        
        if( $error_flag != 0 ){
            print 
<<< EOD
            </head>
            <body><div id="IFRAME"><span class="generalErrMsg">{$objMTS->getSomeMessage("ITAWDCH-ERR-3001")}<br></span></div></body>
            </html>
EOD;
            
            // アクセスログ出力
            web_log( $objMTS->getSomeMessage("ITAWDCH-ERR-2001",$e->getMessage()) );
        }
        else{
            $disp_msg = $e->getMessage();
            
            print 
<<< EOD
            </head>
            <body><div id="IFRAME" >${disp_msg}</div></body>
            </html>
EOD;
            
            // アクセスログ出力
            web_log( $objMTS->getSomeMessage("ITAWDCH-STD-603") );
        }
    }
?>
