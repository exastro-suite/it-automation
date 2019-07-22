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
    
    ////////////////////////////////////////////////////////////////////////
    //                                                                    //
    // 1.ログイン後に表示させるメニューのIDがPOSTで引き渡された事。       //
    //   ただし、common_authの所属するメニューのIDを含まない              //
    // 2.Authインスタンスの作成は、web_parts_for_auth_02.phpが、          //
    //   web_parts_for_browse_01.php(web_parts_for_common.php)に代わり、  //
    //   web_auth_config.phpを直接呼び出す                                //
    ////////////////////////////////////////////////////////////////////////
    
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
        
        //  auth特有ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_auth_01.php");
        
        // 共通HTMLステートメントパーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
        
        //  auth特有ロジックパーツ02
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_auth_02.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
