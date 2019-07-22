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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--

    // メニューID設定
    if(!array_key_exists('no', $_GET)){
        $_GET['no'] = "2100060011";
    }

    if(array_key_exists("mode",$_GET)===true){
        if($_GET['mode']=="dl"){
            $array_except_referer = array("/default/menu/01_browse.php?no=2100060010");
        }
    }
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_05_preupload.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで-- 
    noRetFileWithColumnAccessAgent($objDefaultTable);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--

    // ----アクセスログ出力
    web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-603"));
    // アクセスログ出力----
?>
