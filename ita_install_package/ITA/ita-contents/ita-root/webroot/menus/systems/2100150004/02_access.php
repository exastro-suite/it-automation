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
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--

        ///////////////////////////////////////
        //  Filter1Tbl_showDlファンクション  //
        ///////////////////////////////////////
        function Filter1Tbl_showDl($arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $strOutputStr = "";
            $resultArray = array();
            $strErrMsgBodyToHtmlUI = "";

            $objTable = loadTable();

            $arrayPrintData = array();
            $arrayPrintData = convertReceptDataToDataForFilter($arrayReceptData);

            require_once ("{$g['root_dir_path']}/libs/webindividuallibs/systems/{$g['page_dir']}/98_searchTableFunc.php");
            $result = searchFunc(array('search_filter_data' => $arrayPrintData), $resultArray, $strErrMsgBodyToHtmlUI);

            // 正常の場合
            if(true === $result && 0 < count($resultArray)){

                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/02_printTable.php");
                $strCommonElementFilterData = makeHiddenInputTagFromFilterData($arrayPrintData);

                $strOutputStr .= 
<<< EOD
                <form name="matDl" action="{$g['scheme_n_authority']}/menus/systems/{$g['page_dir']}/99_material_download.php?no={$g['page_dir']}" method="POST">
EOD;
                $strOutputStr .= $strCommonElementFilterData;
                $strOutputStr .= 
<<< EOD
                    <input type="submit" value="{$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101017")}">
                    <input type="hidden" name="filteroutputfiletype" value="excel" >
                    <input type="hidden" name="FORMATTER_ID" value="excel">
                </form>
                <br>
EOD;
                $arrayResult[0] = "000";
            }
            // 対象データが0件の場合
            else if(true === $result && 0 === count($resultArray)){
                $arrayResult[0] = "001";
            }
            // 異常の場合
            else{
                $arrayResult[0] = "002";
            }
            $arrayResult[1] = $strErrMsgBodyToHtmlUI;
            $arrayResult[2] = $strOutputStr;

            return makeAjaxProxyResultStream($arrayResult);
        }

        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
