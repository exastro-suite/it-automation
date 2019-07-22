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
    //  【処理概要】
    //    ・WebDBCore機能を用いたWebページの、動的再描画などを行う。
    //
    //////////////////////////////////////////////////////////////////////

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--
        
        //----オペレーション
        function Mix1_1_operation_upd($strOperationNumeric){
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

            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('OPERATION_NO_UAPK'=>$strOperationNumeric);

            //作業パターン用
            $int_seq_no = 2;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream));
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
        
        function Mix2_1_operation_reg($strOperationNumeric){
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

            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('OPERATION_NO_UAPK'=>$strOperationNumeric);

            //作業パターン用
            $int_seq_no = 2;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream));
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
        //オペレーション----
        
        //----作業パターン
        function Mix1_1_pattern_upd($strOperationNumeric, $strPatternNumeric)
        {
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

            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('OPERATION_NO_UAPK'=>$strOperationNumeric, 'PATTERN_ID'=>$strPatternNumeric);

            //ホスト用
            $int_seq_no = 3;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            //変数名用
            $int_seq_no = 4;
            $arrayResult02 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

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
        
        function Mix2_1_pattern_reg($strOperationNumeric, $strPatternNumeric)
        {
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

            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('OPERATION_NO_UAPK'=>$strOperationNumeric, 'PATTERN_ID'=>$strPatternNumeric);

            //ホスト用
            $int_seq_no = 3;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            //変数名用
            $int_seq_no = 4;
            $arrayResult02 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

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
        //作業パターン----

        function Mix1_1_vars_upd($strVarsLinkIdNumeric)
        {
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

            $objTable = loadTable();

            // 本体ロジックをコール
            $aryVariant = array('VARS_LINK_ID'=>$strVarsLinkIdNumeric, 'COL_SEQ_COMBINATION_ID' => "");

            //メンバー変数名用
            $int_seq_no = 5;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3],$arrayResult01[4]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream));
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
        
        function Mix2_1_vars_reg($strVarsLinkIdNumeric)
        {
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

            $objTable = loadTable();

            // 本体ロジックをコール

            $aryVariant = array('VARS_LINK_ID'=>$strVarsLinkIdNumeric, 'COL_SEQ_COMBINATION_ID' => "");

            //メンバー変数名用
            $int_seq_no = 5;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3],$arrayResult01[4]));
                $strOutputStream = makeAjaxProxyResultStream(array($strResult01Stream));
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
        //変数名----
        function Mix1_1_view_val_upd($objPtnID, $objVarID, $objChlVarID, $objAssSeqID){
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
            $strOutputStream2 = "";

            $objTable = loadTable();

            // 本体ロジックをコール
            $ret = $this->DBGetVarVal($g['objDBCA'], $g['objMTS'], $objPtnID, $objVarID, $objChlVarID, $objAssSeqID, $strOutputStream);
            if($ret === false){
                $strOutputStream = " ";
            }

            $strResultCode = "000";
            $strDetailCode = "000";

            // 本体ロジックをコール
            $aryVariant = array('VARS_LINK_ID'=>$objVarID, 'COL_SEQ_COMBINATION_ID' => $objChlVarID);

            //メンバー変数名用
            $int_seq_no = 5;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);
            // 結果判定
            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3],$arrayResult01[4]));
                $strOutputStream2 = makeAjaxProxyResultStream(array($strResult01Stream));
            }else{
                $strResultCode = "500";
                $strDetailCode = "000";
            }

            $arrayResult = array($strResultCode,$strDetailCode,$strOutputStream, $strOutputStream2);
            return makeAjaxProxyResultStream($arrayResult);
        }
        // 登録時の具体値取得のonchangeトリガー
        function Mix2_1_view_val_reg($objPtnID, $objVarID, $objChlVarID, $objAssSeqID){
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
            $strOutputStream2 = "";

            $objTable = loadTable();

            // 本体ロジックをコール
            $ret = $this->DBGetVarVal($g['objDBCA'], $g['objMTS'], $objPtnID, $objVarID, $objChlVarID, $objAssSeqID, $strOutputStream);
            if($ret === false){
                $strOutputStream = " ";
            }

            $strResultCode = "000";
            $strDetailCode = "000";

            // 本体ロジックをコール
            $aryVariant = array('VARS_LINK_ID'=>$objVarID, 'COL_SEQ_COMBINATION_ID' => $objChlVarID);
            //メンバー変数名用
            $int_seq_no = 5;
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/12_singleRowTable_AddSelectTag.php");
            $arrayResult01 = AddSelectTagToDynamicSelectTab($objTable, "register_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);
            // 結果判定
            if( $arrayResult01[0]=="000" ){
                $strResultCode = "000";
                $strDetailCode = "000";
                $strResult01Stream = makeAjaxProxyResultStream(array($arrayResult01[2],$arrayResult01[3],$arrayResult01[4]));
                $strOutputStream2 = makeAjaxProxyResultStream(array($strResult01Stream));
            }else{
                $strResultCode = "500";
                $strDetailCode = "000";
            }
            $arrayResult = array($strResultCode,$strDetailCode,$strOutputStream, $strOutputStream2);

            return makeAjaxProxyResultStream($arrayResult);
        }
        function Mix1_1_view_val_initdisp($objPkey){
            // グローバル変数宣言
            global $g;
            
            $strResultCode = "";
            $strDetailCode = "";
            $strOutputStream = "";

            // 本体ロジックをコール
            $objPtnID     = "";
            $objVarID     = "";
            $objChlVarID  = "";
            $objColSeqID  = ""; 
            $objAssSeqID  = "";

            $ret = $this->DBGetVarAssData($g['objDBCA'], $g['objMTS'], $objPkey, $objPtnID, $objVarID, $objChlVarID, $objAssSeqID);
            if($ret === false){
                $strOutputStream = " ";
            }
            else{
                $ret = $this->DBGetVarVal($g['objDBCA'], $g['objMTS'], $objPtnID, $objVarID, $objChlVarID, $objAssSeqID, $strOutputStream);
                if($ret === false){
                    $strOutputStream = " ";
                }
            }
            $strResultCode = "000";
            $strDetailCode = "000";
            $arrayResult = array($strResultCode,$strDetailCode,$strOutputStream);

            return makeAjaxProxyResultStream($arrayResult);
        }

        // 同等の処理が00_loadTable.phpあり。修正注意
        
        function DBGetVarVal($objDBCA, $objMTS, $objPtnID, $objVarID, $objChlVarID, $objAssSeqID, &$varval)
        {
            $varval = "";
            if(strlen($objPtnID)==0){
                 return false;
            }
            if(strlen($objVarID)==0){
                 return false;
            }
            $sql =        "SELECT                                                         \n";
            $sql = $sql . "  TBL_A.ROLE_PACKAGE_ID M_ROLE_PACKAGE_ID,                     \n";
            $sql = $sql . "  TBL_A.ROLE_ID         M_ROLE_ID,                             \n";
            $sql = $sql . "  TBL_B.ROLE_PACKAGE_ID,                                       \n";
            $sql = $sql . "  TBL_B.ROLE_ID,                                               \n";
            $sql = $sql . "  TBL_B.VAR_TYPE,                                              \n";
            $sql = $sql . "  TBL_B.VARS_NAME_ID,                                          \n";
            $sql = $sql . "  TBL_B.COL_SEQ_COMBINATION_ID,                                \n";
            $sql = $sql . "  TBL_B.ASSIGN_SEQ,                                            \n";
            $sql = $sql . "  TBL_B.VARS_VALUE                                             \n";
            $sql = $sql . "FROM                                                           \n";
            $sql = $sql . "  (                                                            \n";
            $sql = $sql . "    SELECT                                                     \n";
            $sql = $sql . "      ROLE_PACKAGE_ID,                                         \n";
            $sql = $sql . "      ROLE_ID                                                  \n";
            $sql = $sql . "    FROM                                                       \n";
            $sql = $sql . "      B_ANSIBLE_LRL_PATTERN_LINK                               \n";
            $sql = $sql . "    WHERE                                                      \n";
            $sql = $sql . "      PATTERN_ID  = :PATTERN_ID AND                            \n";
            $sql = $sql . "      DISUSE_FLAG = '0'                                        \n";
            $sql = $sql . "  ) TBL_A                                                      \n";
            $sql = $sql . "  LEFT OUTER JOIN                                              \n";
            $sql = $sql . "  (                                                            \n";
            $sql = $sql . "    SELECT DISTINCT                                            \n";
            $sql = $sql . "      TBL_3.ROLE_PACKAGE_ID,                                   \n";
            $sql = $sql . "      TBL_3.ROLE_ID,                                           \n";
            $sql = $sql . "      TBL_3.VAR_TYPE,                                          \n";
            $sql = $sql . "      TBL_3.VARS_NAME_ID,                                      \n";
            $sql = $sql . "      TBL_3.COL_SEQ_COMBINATION_ID,                            \n";
            $sql = $sql . "      TBL_3.ASSIGN_SEQ,                                        \n";
            $sql = $sql . "      TBL_3.VARS_VALUE                                         \n";
            $sql = $sql . "    FROM                                                       \n";
            $sql = $sql . "      (                                                        \n";
            $sql = $sql . "        SELECT                                                 \n";
            $sql = $sql . "          TBL_2.ROLE_PACKAGE_ID,                               \n";
            $sql = $sql . "          TBL_2.ROLE_ID,                                       \n";
            $sql = $sql . "          TBL_2.VAR_TYPE,                                      \n";
            $sql = $sql . "          TBL_2.VARS_NAME_ID,                                  \n";
            $sql = $sql . "          TBL_2.COL_SEQ_COMBINATION_ID,                        \n";
            $sql = $sql . "          TBL_2.ASSIGN_SEQ,                                    \n";
            $sql = $sql . "          TBL_2.VARS_VALUE                                     \n";
            $sql = $sql . "        FROM                                                   \n";
            $sql = $sql . "          (                                                    \n";
            $sql = $sql . "            SELECT                                             \n";
            $sql = $sql . "              ROLE_PACKAGE_ID,                                 \n";
            $sql = $sql . "              ROLE_ID                                          \n";
            $sql = $sql . "            FROM                                               \n";
            $sql = $sql . "              B_ANSIBLE_LRL_PATTERN_LINK                       \n";
            $sql = $sql . "            WHERE                                              \n";
            $sql = $sql . "              PATTERN_ID  = :PATTERN_ID AND                    \n";
            $sql = $sql . "              DISUSE_FLAG = '0'                                \n";
            $sql = $sql . "          ) TBL_1                                              \n";
            $sql = $sql . "          LEFT  OUTER JOIN  B_ANS_LRL_ROLE_VARSVAL TBL_2 ON    \n";
            $sql = $sql . "                            (TBL_1.ROLE_PACKAGE_ID =           \n";
            $sql = $sql . "                             TBL_2.ROLE_PACKAGE_ID) AND        \n";
            $sql = $sql . "                            (TBL_1.ROLE_ID         =           \n";
            $sql = $sql . "                             TBL_2.ROLE_ID)                    \n";
            $sql = $sql . "        WHERE                                                  \n";
            $sql = $sql . "          TBL_2.DISUSE_FLAG = '0'                              \n";
            $sql = $sql . "      ) TBL_3                                                  \n";
            $sql = $sql . "    WHERE                                                      \n";
            $sql = $sql . "      TBL_3.VARS_NAME_ID IN                                    \n";
            $sql = $sql . "      (                                                        \n";
            $sql = $sql . "        SELECT                                                 \n";
            $sql = $sql . "          VARS_NAME_ID                                         \n";
            $sql = $sql . "        FROM                                                   \n";
            $sql = $sql . "          B_ANS_LRL_PTN_VARS_LINK                              \n";
            $sql = $sql . "        WHERE                                                  \n";
            $sql = $sql . "          PATTERN_ID    = :PATTERN_ID    AND                   \n";
            $sql = $sql . "          VARS_LINK_ID  = :VARS_LINK_ID  AND                   \n";
            $sql = $sql . "          DISUSE_FLAG   = '0'                                  \n";
            $sql = $sql . "      )                                                        \n";
            $sql = $sql . "      AND                                                      \n";
            if(strlen($objChlVarID) != 0){
                $sql = $sql . " TBL_3.COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID    \n";
            }
            else{
                $sql = $sql . " TBL_3.COL_SEQ_COMBINATION_ID IS NULL                      \n";
            }
            $sql = $sql . "  ) TBL_B ON (TBL_A.ROLE_PACKAGE_ID =                          \n";
            $sql = $sql . "              TBL_B.ROLE_PACKAGE_ID) AND                       \n";
            $sql = $sql . "             (TBL_A.ROLE_ID         =                          \n";
            $sql = $sql . "              TBL_B.ROLE_ID);                                  \n";

            $objQuery = $objDBCA->sqlPrepare($sql);
            if($objQuery->getStatus()===false){
                web_log($objQuery->getLastError());
                unset($objQuery);
                return false;
            }
            if(strlen($objChlVarID) == 0){
                $objQuery->sqlBind( array('PATTERN_ID'=>$objPtnID,
                                          'VARS_LINK_ID'=>$objVarID));
            }
            else{
                $objQuery->sqlBind( array('PATTERN_ID'=>$objPtnID,
                                          'VARS_LINK_ID'=>$objVarID,
                                          'COL_SEQ_COMBINATION_ID'=>$objChlVarID));
            }
            $r = $objQuery->sqlExecute();
            if (!$r){
                web_log($objQuery->getLastError());

                unset($objQuery);
                return false;
            }
            // FETCH行数を取得
            $num_of_rows = $objQuery->effectedRowCount();
            // レコード無しの場合
            if( $num_of_rows === 0 ){
                $varval = "undefined default value";
                unset($objQuery);
                return false;
            }
            $var_type  = "";
            $tgt_row = array();
            
            $errmsg    = "";
            $undef_cnt = 0;
            $def_cnt   = 0;
            $arr_type_def_list = array();
            $pkg_id    = "";
            while ( $row = $objQuery->resultFetch() ){
                $tgt_row[] =  $row;
                // 各ロールで変数が定義されているか判定
                // 複数具体値変数で具体値が未定義の場合は該当ロールの変数情報が具体値管理に登録されない。
                if(strlen($row['ROLE_ID'])==0){
                    $undef_cnt++;
                }
                else{
                    $def_cnt++;
                }
                // 同じロールパッケージが紐付てあるか判定
                if($pkg_id == ""){
                    $pkg_id = $row['M_ROLE_PACKAGE_ID'];
                }
                else{
                    if($pkg_id != $row['M_ROLE_PACKAGE_ID']){
                        // DBアクセス事後処理
                        unset($objQuery);

                        // 複数ロールパッケージが紐付られている
                        $varval   = "role packeage different";
                        return false; 
                    }
                }
            }
            unset($objQuery);

            // 全てのロールで具体値未定義を判定
            if($def_cnt == 0){
                return true;
            }
            // 一部のロールで具体値未定義を判定
            if(($def_cnt != 0) && ($undef_cnt != 0)){
                // 一部のロールで具体値未定義
                $varval   = "default value is undefined with some rolls";
                return false;
            }
            for($idx=0;$idx<count($tgt_row);$idx++){
                // 変数の属性を判定
                if($var_type == ""){
                    $var_type = $tgt_row[$idx]['VAR_TYPE'];
                }
                else{
                    if($var_type != $tgt_row[$idx]['VAR_TYPE']){
                        $varval   = "variable type error";
                        return false;
                    }
                }
                // 複数具体値変数の場合、ロール毎の具体値をカウントしておく
                if($var_type == '2'){
                    if(@count($arr_type_def_list[$tgt_row[$idx]['ROLE_ID']]) == 0){
                        $arr_type_def_list[$tgt_row[$idx]['ROLE_ID']] = 1;
                    }
                    else{
                        $arr_type_def_list[$tgt_row[$idx]['ROLE_ID']]++;
                    }
                }
            }
            // 複数具体値変数の場合、ロール毎の具体値の数が一致しているか判定
            $val_cnt = "";
            foreach($arr_type_def_list as $role_id=>$role_val_cnt){
                if($val_cnt == ""){
                    $val_cnt = $role_val_cnt;
                }
                else{
                    if($val_cnt != $role_val_cnt){
                        $varval   = "default value count not match";
                        return false;
                    }
                }
            }

            $wk_varval = array();
            $wk_seqs   = array();
            $errmsg    = "";

            // 一般変数の場合
            if('1' == $var_type){
                if(0 === count($tgt_row)){
                    $varval = "";
                }
                else if(1 === count($tgt_row)){
                    $varval = $tgt_row[0]['VARS_VALUE'];
                }
                else{
                    // 各ロールのデフォルト値が同じか確認する。同じ場合は表示する。
                    $varval = $tgt_row[0]['VARS_VALUE'];
                    for($idx=0;$idx<count($tgt_row);$idx++){
                        if($varval != $tgt_row[$idx]['VARS_VALUE']){
                            $errmsg = "default value is not match";
                        }
                    }
                }
            }
            // 複数具体値変数の場合
            else if('2' == $var_type){
                if(0 === count($tgt_row)){
                    $varval = "";
                }
                else{
                    foreach($tgt_row as $row){

                        if(@count($wk_varval[$row['ASSIGN_SEQ']]) != 0){
                            if($wk_varval[$row['ASSIGN_SEQ']] != $row['VARS_VALUE']){
                                $errmsg = "default value is not match";
                                break;
                            }
                        }
                        else{
                            $wk_varval[$row['ASSIGN_SEQ']] = $row['VARS_VALUE'];
                        }
                        // 各ロールの代入順序を退避
                        if(@count($wk_seqs[$row['ASSIGN_SEQ']]) != 0){
                            $wk_seqs[$row['ASSIGN_SEQ']] = $wk_seqs[$row['ASSIGN_SEQ']] + 1;
                        }
                        else{
                            $wk_seqs[$row['ASSIGN_SEQ']] = 1;
                        }
                    }

                    // 代入順序でソートする。
                    ksort($wk_varval);
                    $varval = "";
                    foreach($wk_varval as $seq=>$val){
                        if($varval != ""){
                            $varval = $varval . "<BR>";
                        }
                        $varval = $varval . "(" . $seq . ")" . $val;
                    }
                }
            }
            else{
                $errmsg = "variable type error";
                break;
            }

            if($errmsg != ""){
                $varval = $errmsg;
                return false;
            }
            return true;
        }
        function DBGetVarAssData($objDBCA, $objMTS, $objPkey, &$objPtnID, &$objVarID, &$objChlVarID, &$objAssSeqID)
        {
            $sql =        " SELECT                      \n";
            $sql = $sql . "   PATTERN_ID,               \n";
            $sql = $sql . "   VARS_LINK_ID,             \n";
            $sql = $sql . "   COL_SEQ_COMBINATION_ID,   \n";
            $sql = $sql . "   ASSIGN_SEQ                \n";
            $sql = $sql . " FROM                        \n";
            $sql = $sql . "   B_ANSIBLE_LRL_VARS_ASSIGN \n";
            $sql = $sql . " WHERE                       \n";
            $sql = $sql . "   ASSIGN_ID = :ASSIGN_ID    \n";

            $objQuery = $objDBCA->sqlPrepare($sql);
            if($objQuery->getStatus()===false){
                web_log($objQuery->getLastError());
                return false;
            }
            $objQuery->sqlBind( array('ASSIGN_ID'=>$objPkey));
            $r = $objQuery->sqlExecute();
            if (!$r){
                web_log($objQuery->getLastError());

                unset($objQuery);
                return false;
            }
            // FETCH行数を取得
            $num_of_rows = $objQuery->effectedRowCount();

            // レコード無しの場合
            if( $num_of_rows != 1 ){
                unset($objQuery);
                return false;
            }
            $row = $objQuery->resultFetch();
            $objPtnID    = $row['PATTERN_ID'];
            $objVarID    = $row['VARS_LINK_ID'];
            $objChlVarID = $row['COL_SEQ_COMBINATION_ID'];
            $objAssSeqID = $row['ASSIGN_SEQ'];
            return true;
        }
        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
