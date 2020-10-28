<?php
//   Copyright 2020 NEC Corporation
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
//////////////////////////////////////////////////////////
//
//  【処理概要】
//   ・updateTable内のrequire先を ita-root/libs/webindividuallibs/systems/2100000216/04_updateTable.php に変更
//
//////////////////////////////////////////////////////////

$root_dir_path = preg_replace('|^(.*/ita-root)/.*$|', '$1', __FILE__);
//-- サイト個別PHP要素、ここから--
//-- サイト個別PHP要素、ここまで--
require_once $root_dir_path."/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php";
//-- サイト個別PHP要素、ここから--
//-- サイト個別PHP要素、ここまで--
class Db_Access extends Db_Access_Core {
    //-- サイト個別PHP要素、ここから--

    /////////////////////////////////
    //  updateTableファンクション  //
    /////////////////////////////////
    public function Mix1_1_updateTable($mode, $innerSeq, $arrayReceptData = null){
        // グローバル変数宣言
        global $g;

        // ローカル変数宣言
        $arrayResult = [];
        $aryVariant = [];
        $arySetting = [];
        $arrayUpdateData = [];

        $arrayUpdateData = convertReceptDataToDataForIUD($arrayReceptData);
        $arySetting = ["Mix1_1","fakeContainer_Update1","Filter1Tbl"];

        // 本体ロジックをコール
        require_once $g['root_dir_path']."/libs/webindividuallibs/systems/2100000216/04_updateTable.php";
        $arrayResult = updateTableMain($mode, $innerSeq, $arrayUpdateData, null, 0, $aryVariant, $arySetting);

        // 結果判定
        if ($arrayResult[0] === "000") {
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
        } else if (intval($arrayResult[0]) < 500) {
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
        } else {
            web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        }
        return makeAjaxProxyResultStream($arrayResult);
    }
    //-- サイト個別PHP要素、ここまで--
}
$server = new HTML_AJAX_Server();
$db_access = new Db_Access();
$server->registerClass($db_access);
$server->handleRequest();

