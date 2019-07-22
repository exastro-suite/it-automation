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
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    
    if(isset($_SERVER["HTTP_REFERER"])){
        $g['requestByHA'] = 'forHADAC'; //[H]tml-[A]AX.[D]b_[A]ccess_[C]ore
    }
    
    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // access系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_access_01.php");
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 以降、HTML_AJAXの処理
    ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
    require_once 'HTML/AJAX/Server.php';
    
    class Db_Access
    {
        ///////////////////////////////////
        //  dispExecutionファンクション  //
        ///////////////////////////////////
        function dispExecution( $target_execution_no ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $error_info   = '';
            $warning_info = '';
            $output_str   = '';
            $info         = '';
            
            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/01_dispExecution.php");
            

            // 結果判定
            if( empty($error_info) ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));

                // 結果を返却
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
                exit;
            }
            else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-2002",array(__FUNCTION__,$error_info)) );
                
                // システムエラーのURLを作成
                $sys_err_url = $g['root_dir_path'] . "/common/common_unexpected_error.php";
                
                // 異常を返却
                return "unexpected_error" . $sys_err_url;
            }
        }
        
        ///////////////////////////////////
        //  scramExecution //
        ///////////////////////////////////
        function scramExecution( $target_execution_no )        {
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $error_info   = '';
            $warning_info = '';
            $output_str   = '';
            $info         = '';
            
            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/04_scram.php");
            
            // 結果判定
            if( !empty($error_info) ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-2002",array(__FUNCTION__,$error_info)) );
                
                // 異常を返却
                return "unexpected_error";
            }
            else if( !empty($warning_info) ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4102",array(__FUNCTION__,$warning_info)) );
                
                // 異常を返却
                return "warning" . $warning_info;
            }
            else{
                if( !empty( $info ) ){
                    // アクセスログ出力
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4002",array(__FUNCTION__,$info)) );
                }
                else{
                    // アクセスログ出力
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__) );
                }
                
                // 結果を返却
                return $output_str;
            }
        }
        
        ///////////////////////////////////
        //  BookCancelファンクション //
        ///////////////////////////////////
        function BookCancel( $target_execution_no ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $error_info   = '';
            $warning_info = '';
            $output_str   = '';
            $info         = '';
            
            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/05_bookCancel.php");
            
            // 結果判定
            if( !empty($error_info) ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-2002",array(__FUNCTION__,$error_info)) );
                
                // 異常を返却
                return "unexpected_error";
            }
            else if( !empty($warning_info) ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4102",array(__FUNCTION__,$warning_info)) );
                
                // 異常を返却
                return "warning" . $warning_info;
            }
            else{
                if( !empty( $info ) ){
                    // アクセスログ出力
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4002",array(__FUNCTION__,$info)) );
                }
                else{
                    // アクセスログ出力
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__) );
                }
                
                // 結果を返却
                return $output_str;
            }
        }
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
