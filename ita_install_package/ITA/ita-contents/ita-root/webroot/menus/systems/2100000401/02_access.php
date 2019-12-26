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

        //////////////////////////////////
        //  ここから標準機能の切り取り  //
        //////////////////////////////////
        
        ///////////////////////////////////////
        //  Filter1Tbl_reloadファンクション  //
        ///////////////////////////////////////
        function Filter1Tbl_reload($intBackFxWake=1)
        {
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $symphony_class_dir = "2100000307";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000307_loadTable.php");
            $objTable1 = loadTable($symphony_class_dir,$aryTmpVariant,$aryTmpSetting);

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/11_filterTable_Reload.php");
            $aryOverride = array("Filter1Tbl","fakeContainer_Filter1Setting");
            $arrayResult = filterTableReloadMain($objTable1, "filter_table", $aryVariant, $arySetting, $aryOverride);

            $intBackFxWake=($intBackFxWake==0)?0:1;
            $arrayResult[3] = $intBackFxWake;

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        ////////////////////////////////
        //  recCountファンクション    //
        ////////////////////////////////
        function Filter1Tbl_recCount($arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $symphony_class_dir = "2100000307";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000307_loadTable.php");
            $objTable1 = loadTable($symphony_class_dir,$aryTmpVariant,$aryTmpSetting);

            $arrayRecCountData = array();
            $arrayRecCountData = convertReceptDataToDataForFilter($arrayReceptData);

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/01_recCount.php");
            $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
            $arrayResult = recCountMain($objTable1, "print_table", $arrayRecCountData, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        ////////////////////////////////
        //  printTableファンクション  //
        ////////////////////////////////
        function Filter1Tbl_printTable($mode, $arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $symphony_class_dir = "2100000307";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000307_loadTable.php");
            $aryTmpVariant['callType'] = 'insConstruct_SymphonyExport';
            $objTable1 = loadTable($symphony_class_dir,$aryTmpVariant,$aryTmpSetting);

            $arrayPrintData = array();
            $arrayPrintData = convertReceptDataToDataForFilter($arrayReceptData);

            // 廃止/復活ボタン
            $aryObjColumn = $objTable1->getColumns();
            $objDisuseColumn = $aryObjColumn[$objTable1->getRequiredDisuseColumnID()];
            $objDisuseColumn->getOutputType('print_table')->setVisible(false);

            // 詳細表示ボタン
            //$aryObjColumn = $objTable1->getColumns();
            $objDisuseColumn = $aryObjColumn['detail_show'];
            $objDisuseColumn->getOutputType('print_table')->setVisible(false);

            // 本体ロジックをコール

            $arySetting['tail_scene_rec_n0_prv1'] = '';

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/02_printTable.php");
            $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
            $arrayResult = printTableMain($objTable1, "print_table", $mode, $arrayPrintData, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        ///////////////////////////////////////
        //  Filter2Tbl_reloadファンクション  //
        ///////////////////////////////////////
        function Filter2Tbl_reload($intBackFxWake=1){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $op_dir = "2100000304";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000304_loadTable.php");
            $objTable2 = loadTable($op_dir,$aryTmpVariant,$aryTmpSetting);

            $aryOverride = array('Filter2Tbl','fakeContainer_Filter2Setting');

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/11_filterTable_Reload.php");
            $arrayResult = filterTableReloadMain($objTable2, "filter_table", $aryVariant, $arySetting, $aryOverride);

            $intBackFxWake=($intBackFxWake==0)?0:1;
            $arrayResult[3] = $intBackFxWake;

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        ////////////////////////////////
        //  recCountファンクション    //
        ////////////////////////////////
        function Filter2Tbl_recCount($arrayReceptData){
            // グローバル変数宣言
            global $g;
            $g['error_flag'] = 0;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $objTable2 = loadTable();

            $arrayRecCountData = array();
            $arrayRecCountData = convertReceptDataToDataForFilter($arrayReceptData);

            $op_dir = "2100000304";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000304_loadTable.php");
            $objTable2 = loadTable($op_dir,$aryTmpVariant,$aryTmpSetting);

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/01_recCount.php");
            $aryOverride = array('Mix2_1','fakeContainer_Filter2Print','Mix2_2','fakeContainer_ND_Filter2Sub');
            $arrayResult = recCountMain($objTable2, "print_table", $arrayRecCountData, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        ////////////////////////////////
        //  printTableファンクション  //
        ////////////////////////////////
        function Filter2Tbl_printTable($mode, $arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $data = array();
            $arrayPrintData = array();

            $arrayPrintData = convertReceptDataToDataForFilter($arrayReceptData);

            $op_dir = "2100000304";
            require_once($g['root_dir_path'] . "/webconfs/systems/2100000304_loadTable.php");
            $aryTmpVariant['callType'] = 'insConstruct_OperationExport';
            $objTable2 = loadTable($op_dir,$aryTmpVariant,$aryTmpSetting);

            // 廃止/復活ボタン
            $aryObjColumn = $objTable2->getColumns();
            $objDisuseColumn = $aryObjColumn[$objTable2->getRequiredDisuseColumnID()];
            $objDisuseColumn->getOutputType('print_table')->setVisible(false);

            // 本体ロジックをコール

            $arySetting['tail_scene_rec_n0_prv1'] = '';

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/02_printTable.php");
            $aryOverride = array('Mix2_1','fakeContainer_Filter2Print','Mix2_2','fakeContainer_ND_Filter2Sub');
            $arrayResult = printTableMain($objTable2, "print_table", $mode, $arrayPrintData, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        //////////////////////////////////
        //  ここまで標準機能の切り取り  //
        //////////////////////////////////

        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();
?>