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
    //    ・ModuleDistictCode(202)
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
    
    $tmpBoolShowSetting = false;
    if( isset($arySYSCON['IP_ADDRESS_LIST']) ){
        if( $arySYSCON['IP_ADDRESS_LIST'] == '1' ){
            $tmpBoolShowSetting = true;
        }
    }
    
    if( $tmpBoolShowSetting === false ){
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1170094"));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,20210101);
        exit();
    }
    unset($tmpBoolShowSetting);
    
    if( isset($_GET['no']) ){
        $req_menu_id = $_GET['no'];
    }
    
    $ASJTM_grp_id = "";

    // ----メニューIDがGETクエリーで与えられているか判定
    if( !isset($req_menu_id) ){
        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1170091"));
        
        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,20210102);
        exit();
    }
    else if( !is_numeric($req_menu_id) ){

        if( isset($_GET['grp']) ){
            $ASJTM_id = "";
            $ASJTM_grp_id = sprintf("%010d", $_GET['grp']);
        }
        else{
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1170092"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,20210103);
            exit();
        }

    }
    else{
        $ASJTM_id = addslashes($req_menu_id);
    }
    // メニューIDがGETクエリーで与えられているか判定----
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    $strMailTag = "";
    if( 0 < strlen($admin_addr) ){
        $strMailTag = $objMTS->getSomeMessage("ITAWDCH-MNU-1170003",$admin_addr);
    }
    
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    // サイト個別のHTMLステートメント

    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_common_valueControllers_js=filemtime("$root_dir_path/webroot/common/javascripts/common_valueControllers.js");
    $timeStamp_common_ky_javasctipts_js=filemtime("$root_dir_path/webroot/common/javascripts/common_ky_javasctipts.js");
    $timeStamp_common_account_list_00_javascript_js=filemtime("$root_dir_path/webroot/common/javascripts/common_account_list_00_javascript.js");
    $timeStamp_ita_icon_png=filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");
    
    print 
<<< EOD
        <script type="text/javascript" src="{$scheme_n_authority}/common/common_account_list_access.php?client=all"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/common_account_list_access.php?stub=all"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_valueControllers.js?{$timeStamp_common_valueControllers_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_ky_javasctipts.js?{$timeStamp_common_ky_javasctipts_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_account_list_00_javascript.js?{$timeStamp_common_account_list_00_javascript_js}"></script>
    </head>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <body id="INDEX">
        <div class="wholecontainer">
            <div id="PAGETOP">
                <!--================-->
                <!--　　ヘッダー　　-->
                <!--================-->
                <div id="HEADER">
                <div style="width:190px; height:70px; float:left; display:flex">
                    <img src="{$scheme_n_authority}/common/imgs/ita_icon.png?{$timeStamp_ita_icon_png}" style="margin-top:7px; height:48px;">
                    <div class="ita_name">IT<span style="margin-left:5px;"></span>Automation</div>
                </div>
                <ul id="PAN"><li>index</li></ul>
            </div>
            <hr>
            <!--================-->
            <!--　　記事部分　　-->
            <!--================-->
            <div id="KIZI">
                <h2>
                {$objMTS->getSomeMessage("ITAWDCH-MNU-1170001")}
                </h2>
                <div class="text">
                    <p>
                        {$objMTS->getSomeMessage("ITAWDCH-MNU-1170002")}<br>
                        {$strMailTag}<br>
                    </p>
                    <div id="table_area"></div>
                    <form method="POST" name="change_pw_form" action="{$scheme_n_authority}/common/common_auth.php?login&grp={$ASJTM_grp_id}&no={$ASJTM_id}">
                        <input type="submit" name="submit" value="{$objMTS->getSomeMessage("ITAWDCH-MNU-1170004")}">
                    </form>
                </div>
EOD;
    
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
