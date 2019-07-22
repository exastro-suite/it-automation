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
    
    // 各種ローカル定数を定義
    
    //----オーケストレータ別の設定記述
    
    $strExeTableIdForSelect    = 'C_ANSIBLE_PNS_EXE_INS_MNG';
    
    $strIfTableIdForSelect     = 'B_ANSIBLE_IF_INFO';  
    
    $strColIdOfDRSRPathFromWebSv = 'ANSIBLE_STORAGE_PATH_LNX';
    $strColIdOfTailLine          = 'ANSIBLE_TAILLOG_LINES';
    $strColIdOfRefreshInt        = 'ANSIBLE_REFRESH_INTERVAL';
    
    //----オーケストレータ別の設定記述----
    
    // 初期値を宣言
    $interval = null;
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    
    // メニューのディレクトリを取得
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }
    
    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
        
        // クエリ存在判定と成功時の取得
        if( !array_key_exists( "execution_no", $_GET ) ){
            // アクセスログ出力(クエリ無し警告)
            web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-401",__FILE__.':'.__LINE__) );
        }
        
        if( array_key_exists( "execution_no", $_GET ) === true ){
            // クエリからexecution_noを取得
            $execution_no = $_GET["execution_no"];
            
            // 整数でない場合はNGとする
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($execution_no) === false ){
                // アクセスログ出力(想定外エラー)
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-402",__FILE__.':'.__LINE__) );
            }
        }

        $exec_log_caption  = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-2000000");
        $error_log_caption = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-2000001");
        
        $prg_recorder_array = array(1=>array('PRG_RCDR_ID'=>'1'
                                            ,'PRG_RCDR_NAME'=>$exec_log_caption
                                            ,'PRG_FILE_NAME'=>'exec.log')
                                    ,2=>array('PRG_RCDR_ID'=>'2'
                                            ,'PRG_RCDR_NAME'=>$error_log_caption
                                            ,'PRG_FILE_NAME'=>'error.log')
                                   );
        
        // 作業№が設定されている場合
        if( isset($execution_no) ){
            ////////////////////////////////////////////////////////////////
            // ステータスを取得                                           //
            ////////////////////////////////////////////////////////////////
            // SQL生成
            $sql = "SELECT  STATUS_ID
                    FROM    {$strExeTableIdForSelect}
                    WHERE   DISUSE_FLAG = '0'
                    AND     EXECUTION_NO = :EXECUTION_NO_BV ";
            
            $objQuery = $g['objDBCA']->sqlPrepare($sql);
            
            if($objQuery->getStatus()===false){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000100") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000100")) );
                
                unset($objQuery);
                
                // 例外処理へ
                throw new Exception();
            }
            
            $objQuery->sqlBind( array( 'EXECUTION_NO_BV'=>$execution_no ) );
            
            $r = $objQuery->sqlExecute();
            
            if (!$r){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000200") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000200")) );
                
                unset($objQuery);
                
                // 例外処理へ
                throw new Exception();
            }
            
            while ( $row = $objQuery->resultFetch() ){
                // ステータスIDを取得
                $status_id_temp = $row['STATUS_ID'];
            }
            // ループ回数を取得
            $num_rows = $objQuery->effectedRowCount();
            
            // DBアクセス事後処理
            unset($objQuery);
            
            // 単一行セレクトか判定
            if( $num_rows != 1 ){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000300") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000300")) );
                
                // 例外処理へ
                throw new Exception();
            }
            
            ////////////////////////////////////////////////////////////////
            // インタフェース情報を取得                                   //
            ////////////////////////////////////////////////////////////////
            // SQL作成
            $sql = "SELECT {$strColIdOfDRSRPathFromWebSv} DRS_ROOT_PATH_FROM_ITAWEB "
                  .",{$strColIdOfTailLine} TAILLOG_LINES "
                  .",{$strColIdOfRefreshInt} REFRESH_INTERVAL "
                  ."FROM   {$strIfTableIdForSelect} "
                  ."WHERE  DISUSE_FLAG = '0' ";
            
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000600") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000600")) );
                
                // DBアクセス事後処理
                unset($objQuery);
                
                // 例外処理へ
                throw new Exception();
            }
            
            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000700") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000700")) );
                
                // DBアクセス事後処理
                unset($objQuery);
                
                // 例外処理へ
                throw new Exception();
            }
            
            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                $row_if_info = $row;
            }
            // FETCH行数を取得
            $num_of_rows = $objQuery->effectedRowCount();
            
            // 単一行セレクトでない場合はNG
            if( $num_of_rows != 1 ){
                // アクセスログ出力(想定外エラー)
                //web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",__FILE__,__LINE__,"00000800") );
                web_log( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-404",array(__FILE__,__LINE__,"00000800")) );
                
                // DBアクセス事後処理
                unset($objQuery);
                
                // 例外処理へ
                throw new Exception();
            }
            
            // DBアクセス事後処理
            unset($objQuery);
            
            $interval = $row_if_info['REFRESH_INTERVAL']; // 最新化のインターバルタイム(msec)
        }
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAANSIBLEC","STD","_js");

    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");

    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    $timeStamp_itabase_orchestrator_drive_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_orchestrator_drive_style.css");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_orchestrator_drive_style.css?{$timeStamp_itabase_orchestrator_drive_style_css}">
EOD;
    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    $strPageExplainBody = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-508025");
    
print <<< EOD
    <!-------------------------------- 説明 -------------------------------->
    <div id="intervalOfDisp" style="display:none" class="text">{$interval}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <h2>
        <table width="100%">
            <tr>
                <!--説明-->
                <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-507050")}</div></td>
                <td>
                    <div id="SetsumeiMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="SetsumeiNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            {$strPageExplainBody}
        </div>
    </div>
    
    <!-------------------------------- 対象作業 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--対象作業-->
                <td><div onClick=location.href="javascript:show('ExecutionMidashi','ExecutionNakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-507060")}</div></td>
                <td>
                    <div id="ExecutionMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('ExecutionMidashi','ExecutionNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="ExecutionNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="disp_execution_area"></div>
        </div>
    </div>
    
EOD;
    // 作業№が設定されている場合
    if( isset($execution_no) ){
        // ステータスが「未実行(予約)」の場合
        if( $status_id_temp == 9 ){
print <<< EOD
                <!-------------------------------- 予約取消 -------------------------------->
                <h2>
                    <table width="100%">
                        <tr>
                            <!--予約取消-->
                            <td><div onClick=location.href="javascript:show('Booking_Cancel_Midashi','Booking_Cancel_Nakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-507070")}</div></td>
                            <td>
                                <div id="Booking_Cancel_Midashi" align="right">
                                    <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Booking_Cancel_Midashi','Booking_Cancel_Nakami');" >
                                </div>
                            </td>
                        </tr>
                    </table>
                </h2>
                <div id="Booking_Cancel_Nakami" style="display:block" class="text">
                    <div style="margin-left:10px">
                        <!--予約取消-->
                        <input type="button" id="book_cancel_button" class="updatebutton" value="{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-507080")}" onClick=location.href="javascript:book_cancel();" >
                    </div>
                </div>

EOD;
        }
        
        // 進行記録ファイル
        if( count( $prg_recorder_array ) > 0 ){
            $loop_counter = 0;
            foreach( $prg_recorder_array as $prg_recorder ){
                // loop_counterをインクリメント
                $loop_counter++;
                
                // ファイル名をURLエンコード
                $prg_record_file_id = htmlspecialchars($prg_recorder['PRG_RCDR_ID']);
                // 進行記録ファイルのtailウィンドウを生成
print <<< EOD
                <!-------------------------------- 進行状況 -------------------------------->
                <h2>
                    <table width="100%">
                        <tr>
                            <!--進行状況-->
                            <td><div onClick=location.href="javascript:show('Monitor_{$loop_counter}_Midashi','Monitor_{$loop_counter}_Nakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-507090")}({$prg_recorder['PRG_RCDR_NAME']})</div></td>
                            <td>
                                <div id="Monitor_{$loop_counter}_Midashi" align="right">
                                    <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Monitor_{$loop_counter}_Midashi','Monitor_{$loop_counter}_Nakami');" >
                                </div>
                            </td>
                        </tr>
                    </table>
                </h2>
                <div id="Monitor_{$loop_counter}_Nakami" style="display:block" class="text">
                    <div style="margin-left:10px">
                        <iframe id="TailWindow_{$loop_counter}" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/05_disp_taillog.php?no={$g['page_dir']}&execution_no={$execution_no}&prg_recorder={$prg_record_file_id}" scrolling="no" width="950px" height="365px" border="0" style="border:none;" frameborder="0" ></iframe>
                        <br>
                        <br>
                    </div>
                </div>
EOD;
            }
        }
    }
    
print <<< EOD
    
    <!-------------------------------- 緊急停止 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--緊急停止-->
                <td><div onClick=location.href="javascript:show('ScramMidashi','ScramNakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-508010")}</div></td>
                <td>
                    <div id="ScramMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('ScramMidashi','ScramNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="ScramNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="scram_area">
            <!--緊急停止-->
            <input type="button" id="scrumTryExecute" class="updatebutton scrumSubmitElement tryExecute" value="{$objMTS->getSomeMessage("ITAANSIBLEH-MNU-508020")}" onClick=location.href="javascript:scrum_execution();" disabled >
            </div>
        </div>
    </div>
    
    <!-------------------------------- 必要情報 -------------------------------->
EOD;
    if( isset($execution_no) ){
print <<< EOD
    <div id="target_execution_no" style="display:none;">$execution_no</div>
EOD;
    }
    
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
