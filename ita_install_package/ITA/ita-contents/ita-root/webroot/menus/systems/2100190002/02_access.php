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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
    //-- サイト個別PHP要素、ここから--

    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--

        //----メニューID
        function Mix1_1_contrast_menu_upd($strMenuNumeric){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $aryOverride = array("Mix1_1");
            
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $strResultCode = "";
            $strDetailCode = "";
            $strOutputStream = "";
            $strFxName="";
            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('CONTRAST_LIST_ID'=>$strMenuNumeric);

            $strMenuIDNumeric = $strMenuNumeric;

            $strQuery = "SELECT *"
                       ."FROM "
                       ." A_CONTRAST_LIST  TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                       ."ORDER BY CONTRAST_LIST_ID";

            $aryForBind['CONTRAST_LIST_ID'] = $strMenuIDNumeric;
            $rows=array();
            if( 0 < strlen($strMenuIDNumeric) ){
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];

                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    }
                    while($row = $objQuery->resultFetch() ){
                        // レコード毎のアクセス権を判定
                        list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                        if($ret === false) {
                            $intErrorType = 500;
                            $retBool = false;
                            break;
                        }else{
                            if($permission === true){
                                $rows[]=$row;
                            }
                        }
                    }
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }
            }

            $strMenuIDNumeric1="";
            $strMenuIDNumeric2="";

            if( count($rows) == 1) {
                $strMenuIDNumeric1 = $rows[0]['CONTRAST_MENU_ID_1'];
                $strMenuIDNumeric2 = $rows[0]['CONTRAST_MENU_ID_2'];
            }
            $aryVariant = array('MENU_ID'=>$strMenuIDNumeric1);

            //カラムタイトル
            $int_seq_no = 3;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            $aryVariant = array('MENU_ID'=>$strMenuIDNumeric2);
            //カラムタイトル
            $int_seq_no = 4;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult02 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if( $arrayResult01[0]=="000" && $arrayResult02[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3]));
                $strResult02Stream = makeAjaxProxyResultStream(array($arrayResult02[2],$arrayResult02[3]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream,$strResult02Stream));
            }else{
                $strResultCode = "500";
                $strDetailCode = "000";
            }
            $arrayResult = array($strResultCode,$strDetailCode,$strOutputStream);

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        
        function Mix2_1_contrast_menu_reg($strMenuNumeric){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $aryOverride = array("Mix2_1");
            
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $strResultCode = "";
            $strDetailCode = "";
            $strOutputStream = "";
            $strFxName="";
            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('CONTRAST_LIST_ID'=>$strMenuNumeric);

            $strMenuIDNumeric = $strMenuNumeric;

            $strQuery = "SELECT *"
                       ."FROM "
                       ." A_CONTRAST_LIST  TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                       ."ORDER BY CONTRAST_LIST_ID";

            $aryForBind['CONTRAST_LIST_ID'] = $strMenuIDNumeric;
            $rows=array();
            if( 0 < strlen($strMenuIDNumeric) ){
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];

                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    }
                    while($row = $objQuery->resultFetch() ){
                        // レコード毎のアクセス権を判定
                        list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                        if($ret === false) {
                            $intErrorType = 500;
                            $retBool = false;
                            break;
                        }else{
                            if($permission === true){
                                $rows[]=$row;
                            }
                        }
                    }
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }
            }

            $strMenuIDNumeric1="";
            $strMenuIDNumeric2="";

            if( count($rows) == 1) {
                $strMenuIDNumeric1 = $rows[0]['CONTRAST_MENU_ID_1'];
                $strMenuIDNumeric2 = $rows[0]['CONTRAST_MENU_ID_2'];
            }
            $aryVariant = array('MENU_ID'=>$strMenuIDNumeric1);

            //カラムタイトル
            $int_seq_no = 3;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            $aryVariant = array('MENU_ID'=>$strMenuIDNumeric2);
            //カラムタイトル
            $int_seq_no = 4;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult02 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            // 結果判定
            if( $arrayResult01[0]=="000" && $arrayResult02[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3]));
                $strResult02Stream = makeAjaxProxyResultStream(array($arrayResult02[2],$arrayResult02[3]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream,$strResult02Stream));
            }else{
                $strResultCode = "500";
                $strDetailCode = "000";
            }
            $arrayResult = array($strResultCode,$strDetailCode,$strOutputStream);

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        //メニューID----

        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();
?>
