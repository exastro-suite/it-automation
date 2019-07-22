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
    //    ・ModuleDistictCode(105)
    //
    //////////////////////////////////////////////////////////////////////

    try{

        require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_menu_info.php");

        if(array_key_exists('grp', $_GET) && "" != $_GET['grp']){
            $ACRCM_group_id = sprintf("%010d", $_GET['grp']);
            $ACRCM_id = "";
            $ACRCM_login_nf = "1";
        }
        else{
            //----メニューの情報取得
            $tmpAryRetBody = getMenuInfo(intval($_GET['no']), $objDBCA);
            if( $tmpAryRetBody[1] !== null ){
                if( $tmpAryRetBody[1] == 502 ){
                    // アクセスログ出力(想定外エラー)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-40",array($_SERVER["PHP_SELF"],$tmpAryRetBody[0]['rowLength'])));
                    // 想定外エラー通知画面にリダイレクト
                    webRequestForceQuitFromEveryWhere(500,10510101);
                    exit();
                }
                throw new Exception( $tmpAryRetBody[3] );
            }

            $getData = $tmpAryRetBody[0]['data'];

            $ACRCM_id               = sprintf("%010d", $getData[0]['MENU_ID']);
            $ACRCM_group_id         = sprintf("%010d", $getData[0]['MENU_GROUP_ID']);
            $ACRCM_login_nf         = $getData[0]['LOGIN_NECESSITY'];
            $ACRCM_serv_status      = $getData[0]['SERVICE_STATUS'];
            $ACRCM_auto_filter      = $getData[0]['AUTOFILTER_FLG'];
            $ACRCM_initial_filter   = $getData[0]['INITIAL_FILTER_FLG'];
            $ACRCM_web_limit        = $getData[0]['WEB_PRINT_LIMIT'];
            $ACRCM_web_confirm      = $getData[0]['WEB_PRINT_CONFIRM'];
            $ACRCM_xls_limit        = $getData[0]['XLS_PRINT_LIMIT'];
            unset($tmpAryRetBody);

            $ACRCM_representative_file_name = "/default/menu/01_browse.php?no=" . sprintf("%010d", $getData[0]['MENU_ID']);

        }

        //----メニューグループ名取得
        //----■テーブル【メニューグループリスト】から、リクエストされたPHPが所属するメニューの、メニューグループ名を取得する。
        $tmpAryRetBody = getMenuGroupNameByMenuGroupID(intval($ACRCM_group_id), $objDBCA);
        if( $tmpAryRetBody[1] !== null ){
            //----取得できなかった
            if( $tmpAryRetBody[1] == 502 ){
                // アクセスログ出力(想定外エラー)
                web_log($objMTS->getSomeMessage("ITAWDCH-ERR-41"));

                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,10510102);
                exit();
            }
            throw new Exception( $tmpAryRetBody[3] );
            // 取得できなかった----
        }
        $ACRCM_group_name = $tmpAryRetBody[0]['MenuGroupName'];

        $ACRCM_representative_file_name = "/default/mainmenu/01_browse.php?grp=" . $_GET['grp'];

        unset($tmpAryRetBody);
    }
    catch (Exception $e){
        $tmpErrMsgBody = $e->getMessage();
        if ( isset($objQuery) )    unset($objQuery);

        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-43",$tmpErrMsgBody));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10510104);
        exit();
    }

    // 後続のヒアドキュメント内で使用する置換文字列を準備
    $title_name = $ACRCM_group_name;
    $site_name  = $ACRCM_group_name;
    $admin_addr = file_get_contents( $root_dir_path . "/confs/webconfs/admin_mail_addr.txt");
?>
