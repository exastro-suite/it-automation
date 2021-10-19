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
        ///////////////////////////////////
        //  registerTableファンクション  //
        ///////////////////////////////////
        function Mix2_1_registerTable($mode, $arrayReceptData, $aryVariant=array()){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $arySetting = array();

            $arrayRegisterData = array();

            $arrayRegisterData = convertReceptDataToDataForIUD($arrayReceptData);

            $arySetting = array("Mix2_1","fakeContainer_Register2","Filter1Tbl");

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
            $arrayResult = registerTableMain($mode, $arrayRegisterData, null, 0, $aryVariant, $arySetting);

            // DESIGN_TYPEだけ$innerSeq101にする
            if($mode == 2 && $arrayRegisterData["COL_IDSOP_9"] == "DESIGN_TYPE"){
                $arrayResult[1]=101;
            }

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
        /////////////////////////////////
        //  updateTableファンクション  //
        /////////////////////////////////
        function Mix1_1_updateTable($mode, $innerSeq, $arrayReceptData = null){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
           
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $arrayUpdateData = array();

            $arrayUpdateData = convertReceptDataToDataForIUD($arrayReceptData);

            $arySetting = array("Mix1_1","fakeContainer_Update1","Filter1Tbl");

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/04_updateTable.php");
            $arrayResult = updateTableMain($mode, $innerSeq, $arrayUpdateData, null, 0, $aryVariant, $arySetting);
            
            // DESIGN_TYPEとROLE_BUTTONだけ$innerSeq101にする

            if($mode == 3 && ($arrayUpdateData["COL_IDSOP_9"] == "DESIGN_TYPE" || $arrayUpdateData["COL_IDSOP_9"] == "ROLE_BUTTON")){
                $arrayResult[1]=101;
            }

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
        /////////////////////////////////
        //  deleteTableファンクション  //
        /////////////////////////////////
        function Mix1_1_deleteTable($mode, $innerSeq, $arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $arratDeleteData = array();

            $arratDeleteData = convertReceptDataToDataForIUD($arrayReceptData);

            $arySetting = array("Mix1_1","fakeContainer_Delete1","fakeContainer_Delete1");

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
            $arrayResult = deleteTableMain($mode, $innerSeq, $arratDeleteData, null, 0, $aryVariant, $arySetting);
            
            // DESIGN_TYPEだけ$innerSeq101にする
            //廃止・復活
            if(($mode == 3 || $mode == 5) && $aryVariant['edit_target_row']['CONFIG_ID'] == "DESIGN_TYPE"){
                $arrayResult[1]=101;
            }
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
    }
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();
?>
