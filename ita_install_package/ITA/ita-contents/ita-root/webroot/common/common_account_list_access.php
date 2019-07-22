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
    //    ・ModuleDistictCode(201)
    //
    //////////////////////////////////////////////////////////////////////
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    
    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // リファラ取得(リダイレクト判定のため)
    if(isset($_SERVER["HTTP_REFERER"])){
        $host = $_SERVER['HTTP_REFERER'];
    }
    else{
        $host = "";
    }

    // 代表PHPファイルからのリダイレクトでない場合はNG
    if(!stristr($host,$ACRCM_representative_file_name)){
        // アクセスログ出力(リダイレクト判定NG)
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1170093"));

        // 不正操作によるアクセス警告画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,20110201);
        exit();
    }
    else{
        ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
        require_once 'HTML/AJAX/Server.php';
        
        //----ここからAJAX用クラス
        class Db_Access
        {
            ////////////////////////////////
            //  printTableファンクション  //
            ////////////////////////////////
            function printTable(){
                // ルートディレクトリを取得
                global $objDBCA, $objMTS, $arySYSCON;
                if ( empty($root_dir_path) ){
                    $root_dir_temp = array();
                    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
                    $root_dir_path = $root_dir_temp[0] . "ita-root";
                }
                
                // URLのスキーム＆オーソリティを取得
                if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' ){
                    $protocol = 'https://';
                }
                else{
                    $protocol = 'http://';
                }
                $scheme_n_authority = getSchemeNAuthority();
                
                $int_err_flag = 0;
                $row_counter = 0;
                $str_temp = "";
                
                try{
                    $boolShowSetting = false;
                    if( isset($arySYSCON['IP_ADDRESS_LIST']) ){
                        if( $arySYSCON['IP_ADDRESS_LIST'] == '1' ){
                            $boolShowSetting = true;
                        }
                    }
                    if( $boolShowSetting === false ){
                         throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    //----SQL
                    $sql = "SELECT  USER_ID,
                                USERNAME,
                                USERNAME_JP,
                                MAIL_ADDRESS
                        FROM   A_ACCOUNT_LIST
                        WHERE  DISUSE_FLAG = '0'
                        ORDER BY USERNAME ";
                    //SQL----

                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();

                    if( $r === false ){
                        throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    //----ここからwhileループ
                    while( $row = $objQuery->resultFetch() ){
                        // ----念のため改行コード(LF)を<br>に変換する
                        $row_counter += 1;
                        $COLUMN_01 = nl2br(htmlspecialchars($row['USERNAME']));
                        $COLUMN_02 = nl2br(htmlspecialchars($row['USERNAME_JP']));
                        $COLUMN_03 = nl2br(htmlspecialchars($row['MAIL_ADDRESS']));
                        $COLUMN_04 = nl2br(htmlspecialchars($row['USER_ID']));
                        $str_temp .=
<<< EOD
                        <tr valign="top">
                            <td>$COLUMN_01</td>
                            <td>$COLUMN_02</td>
                            <td>$COLUMN_03</td>
                            <td>$COLUMN_04</td>
                        </tr>
EOD;
                    }
                    //ここまでwhileループ----

                    unset($objQuery);

                }
                catch (Exception $e){
                    $int_err_flag = 1;

                    $retErrMsgBody = $e->getMessage();

                    // DBアクセス事後処理
                    if ( isset($objQuery) )    unset($objQuery);
                }

                if($int_err_flag == 0){

                    if($row_counter == 0){
                        // ---- 0件の場合はTABLEではなくメッセージのみを返却するようハンドリング
                        $str_merge = "<br>{$objMTS->getSomeMessage("ITAWDCH-MNU-1170005")}";
                        // 0件の場合はTABLEではなくメッセージのみを返却するようハンドリング ----
                    }else{
                        $str_merge = 
<<< EOD
                    <div class="fakeContainer_Table">
                    <table id="DbTable">
                        <tr>
                            <th scope="col"><b>{$objMTS->getSomeMessage("ITAWDCH-MNU-1170006")}</b></th>
                            <th scope="col"><b>{$objMTS->getSomeMessage("ITAWDCH-MNU-1170007")}</b></th>
                            <th scope="col"><b>{$objMTS->getSomeMessage("ITAWDCH-MNU-1170008")}</b></th>
                            <th scope="col"><b>{$objMTS->getSomeMessage("ITAWDCH-MNU-1170009")}</b></th>
                        </tr>
EOD;
                        $str_merge .= $str_temp;
                        $str_temp = 
<<< EOD
                    </table>
                    </div>
                    \n{$objMTS->getSomeMessage("ITAWDCH-MNU-1170010")}: {$row_counter}{$objMTS->getSomeMessage("ITAWDCH-MNU-1170011")}\n<br>
EOD;
                        $str_merge .= $str_temp;
                    }

                    // アクセスログ出力
                    web_log($objMTS->getSomeMessage("ITAWDCH-STD-4001",'FUNCTION:' . __FUNCTION__));
                    
                    // 結果を返却
                    return $str_merge;
                }else{
                    // アクセスログ出力
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-4011",array('FUNCTION:' . __FUNCTION__,$retErrMsgBody)));
                    
                    // 異常メッセージを返却
                    return $objMTS->getSomeMessage("ITAWDCH-ERR-3001");
                }
            }
        }
        //ここまでAJAX用クラス----
        
        $server = new HTML_AJAX_Server();
        $server->registerClass(new Db_Access());
        $server->handleRequest();
    }
?>
