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

    //----説明
    //
    //・検索条件設定欄に、検索条件が設定され、最後に「表示」ボタンが押された結果を、エクセルファイルに出力する
    //・「EXCEL出力ボタン」が押される前に、「表示」ボタンが押されていない場合は、検索条件の変更は反映されない」
    //
    //説明----

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_03_create_excel.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで-- 
    tableDumpToFile($objDefaultTable,array('search_filter_data'=>$_POST['filter_data']));
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで-- 

    // ----アクセスログ出力
    web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-603"));
    // アクセスログ出力----
?>
