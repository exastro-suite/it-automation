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

    ini_set("display_errors",1);


    global $g;
    $tmpEscapeDataBody = array();
    $tmpEscapeDataKey = array('requestByHA','requestByREST');
    if( $g !== null ){
        foreach($tmpEscapeDataKey as $tmpStrKey){
            if( array_key_exists($tmpStrKey, $g) ){
                $tmpEscapeDataBody[$tmpStrKey] = $g[$tmpStrKey];
            }
        }
    }
    //----配列を初期化
    $g = array();
    //配列を初期化----
    foreach($tmpEscapeDataBody as $tmpStrKey=>$tmpVarValue){
        $g[$tmpStrKey] = $tmpVarValue;
    }
    unset($tmpEscapeDataKey);
    unset($tmpEscapeDataBody);
    unset($tmpStrKey);
    unset($tmpVarValue);

    $g['objMTS']        = $objMTS;

    $g['db_model_ch']   = $db_model_ch;
    $g['objDBCA']       = $objDBCA;

    $g['scheme_n_authority']   = getSchemeNAuthority();
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }

    $arrayReqInfo = requestTypeAnalyze();
    if( $arrayReqInfo[0] == "web" ){
        $g['login_id']      = $p_login_id;      //----ID(数値)
        $g['login_name']    = $p_login_name;    //----ログイン用ID(英数字)
        $g['login_name_jp'] = $p_login_name_jp; //----利用者名(日本語)

        //----URL指定でリクエストされたメニューに関する情報等
        $g['menu_id']       = $ACRCM_id;
    }
    else{
        $g['login_id']      = ''; //----ID(数値)
        $g['login_name']    = ''; //----ログイン用ID(英数字)
        $g['login_name_jp'] = ''; //----利用者名(日本語)
        $g['menu_id']       = '';
    }

    //----URL指定でリクエストされたメニューに関する情報等
    $g['privilege']     = isset($privilege)?$privilege:0; // 本来、メニュー単位でもつべきもの
    $g['menu_autofilter']       = isset($ACRCM_auto_filter)?$ACRCM_auto_filter:null;
    $g['menu_initial_filter']   = isset($ACRCM_initial_filter)?$ACRCM_initial_filter:null;
    $g['menu_web_limit']   = isset($ACRCM_web_limit)?$ACRCM_web_limit:null;
    $g['menu_web_confirm'] = isset($ACRCM_web_confirm)?$ACRCM_web_confirm:null;
    $g['menu_xls_limit']   = isset($ACRCM_xls_limit)?$ACRCM_xls_limit:null;
    
    //URL指定でリクエストされたメニューに関する情報等----

    $g['request_time']         = $_SERVER['REQUEST_TIME'];
    $g['request_time_micro']   = $_SERVER['REQUEST_TIME_FLOAT'];

    $g['root_dir_path']        = $root_dir_path;

    $g['error_flag']           = 0;

    $g['ary_forbidden_upload'] = isset($forbidden_upload)?explode(";",$forbidden_upload):array();

    $g['event_mail_send']      = isset($event_mail_send)?$event_mail_send:1;

    $g['dev_log_dir']          = isset($dev_log_dir)?$dev_log_dir:"";
    $g['dev_log_level']        = isset($dev_log_level)?intval($dev_log_level):0;

    //----開発者権限のあるロールならば1以上が入る
    $g['dev_log_developer'] = isset($p_debug_log_developer)?$p_debug_log_developer:0;
    //開発者権限のあるロールならば1以上が入る----

    $g['admin_excel_download'] = isset($p_admin_excel_download)?$p_admin_excel_download:0;

    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_01_class_validator.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_02_class_cell_formatter.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_03_class_outputtype.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_04_class_column.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_05_class_rowdata.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_06_class_list_formatter.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_98_class_etc.php");
    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_99_class_table_control_agent.php");

    if( file_exists("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_xx_class_dev_now.php") ){
        require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/88_xx_class_dev_now.php");
    }

    require_once("{$g['root_dir_path']}/libs/webcommonlibs/table_control_agent/99_functions2.php");

?>
