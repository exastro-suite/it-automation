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

    global $g;
    // ルートディレクトリを取得
    $tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
    if(isset($_SERVER["HTTP_REFERER"])){
        $g['requestByHA'] = 'forHADAC'; //[H]tml-[A]AX.[D]b_[A]ccess_[C]ore
    }

    // DBアクセスを伴う処理を開始
    try{
        //----ここから01_系から06_系全て共通
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/mainmenu/web_parts_mainmenu_info.php");
        //ここまで01_系から06_系全て共通----

        // access系共通ロジックパーツ01
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_access_01.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
        throw $e;
    }


    ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
    require_once 'HTML/AJAX/Server.php';
    // 以降、HTML_AJAXの処理

    class Db_Access_Core
    {
        //////////////////////////////////////////////
        //  Filter1Tbl_add_selectboxファンクション  //
        //////////////////////////////////////////////
        function Filter1Tbl_add_selectbox($int_seq_no){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $objTable = loadTable();

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/10_filterTable_AddSelectTag.php");
            $aryOverride = array("Filter1Tbl");
            $arrayResult = AddSelectTagToTextFilterTab($objTable, "filter_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

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

            $objTable = loadTable();

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/11_filterTable_Reload.php");
            $aryOverride = array("Filter1Tbl","fakeContainer_Filter1Setting");
            $arrayResult = filterTableReloadMain($objTable, "filter_table", $aryVariant, $arySetting, $aryOverride);

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

            $objTable = loadTable();

            $arrayRecCountData = array();
            $arrayRecCountData = convertReceptDataToDataForFilter($arrayReceptData);

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/01_recCount.php");
            $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
            $arrayResult = recCountMain($objTable, "print_table", $arrayRecCountData, $aryVariant, $arySetting, $aryOverride);

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

            $objTable = loadTable();

            $arrayPrintData = array();
            $arrayPrintData = convertReceptDataToDataForFilter($arrayReceptData);

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/02_printTable.php");
            $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
            $arrayResult = printTableMain($objTable, "print_table", $mode, $arrayPrintData, $aryVariant, $arySetting, $aryOverride);

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

        ///////////////////////////////////
        //  registerTableファンクション  //
        ///////////////////////////////////
        function Mix2_1_registerTable($mode, $arrayReceptData, $aryVariant=array()){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $arrayRegisterData = array();

            $arrayRegisterData = convertReceptDataToDataForIUD($arrayReceptData);

            $arySetting = array("Mix2_1","fakeContainer_Register2","Filter1Tbl");

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
            $arrayResult = registerTableMain($mode, $arrayRegisterData, null, 0, $aryVariant, $arySetting);

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
        //  printJournalファンクション  //
        //////////////////////////////////
        function Journal1Tbl_printJournal($arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $objTable = loadTable();

            $arrayPrintJournal = array();
            $arrayPrintJournal = convertReceptDataToDataForFilter($arrayReceptData);

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/06_printJournal.php");
            $aryOverride = array("Journal1Tbl","fakeContainer_Journal1Print");
            $arrayResult = printJournalMain($objTable, "print_journal_table", $arrayPrintJournal, $aryVariant, $arySetting, $aryOverride);

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

    class Db_Access_Core_ForReview extends Db_Access_Core
    {
        //////////////////////////////////////////////
        //  Filter2Tbl_add_selectboxファンクション  //
        //////////////////////////////////////////////
        function Filter2Tbl_add_selectbox($int_seq_no){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $aryTmpVariant['pageType'] = 'view';
            $objTCAFRRV = loadTable(null,$aryTmpVariant,$aryTmpSetting);

            $aryOverride = array('Filter2Tbl');

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/10_filterTable_AddSelectTag.php");
            $arrayResult = AddSelectTagToTextFilterTab($objTCAFRRV, "filter_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

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
        function Filter2Tbl_reload($intBackFxWake=1)
        {
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $aryTmpVariant['pageType'] = 'view';
            $objTCAFRRV = loadTable(null,$aryTmpVariant,$aryTmpSetting);

            $aryOverride = array('Filter2Tbl','fakeContainer_Filter2Setting');

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/11_filterTable_Reload.php");
            $arrayResult = filterTableReloadMain($objTCAFRRV, "filter_table", $aryVariant, $arySetting, $aryOverride);

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

            $objTable = loadTable();

            $arrayRecCountData = array();
            $arrayRecCountData = convertReceptDataToDataForFilter($arrayReceptData);

            $aryTmpVariant['pageType'] = 'view';
            $objTCAFRRV = loadTable(null,$aryTmpVariant,$aryTmpSetting);

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/01_recCount.php");
            $aryOverride = array('Mix2_1','fakeContainer_Filter2Print','Mix2_2','fakeContainer_ND_Filter2Sub');
            $arrayResult = recCountMain($objTCAFRRV, "print_table", $arrayRecCountData, $aryVariant, $arySetting, $aryOverride);

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

            $aryTmpVariant['pageType'] = 'view';
            $objTCAFRRV = loadTable(null,$aryTmpVariant,$aryTmpSetting);

            $aryObjColumn = $objTCAFRRV->getColumns();
            $objDisuseColumn = $aryObjColumn[$objTCAFRRV->getRequiredDisuseColumnID()];
            $objDisuseColumn->getOutputType('print_table')->setVisible(false);

            $strTextForRegisterForUpdate = $objTCAFRRV->getActionNameOfApplyRegistrationForNew();

            $arySetting['tail_scene_rec_n0_prv1'] = "<input type=\"button\" value=\"{$strTextForRegisterForUpdate}\" onClick=location.href=\"javascript:Mix2_1_register_async(1);\" >";
            $arySetting['tail_scene_rec_nx_prv1'] = "<input type=\"button\" value=\"{$strTextForRegisterForUpdate}\" onClick=location.href=\"javascript:Mix2_1_register_async(1);\" >";

            $aryVariant['commonHiddenSend01'] = "view";

            // 本体ロジックをコール

            require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/02_printTable.php");
            $aryOverride = array('Mix2_1','fakeContainer_Filter2Print','Mix2_2','fakeContainer_ND_Filter2Sub');
            $arrayResult = printTableMain($objTCAFRRV, "print_table", $mode, $arrayPrintData, $aryVariant, $arySetting, $aryOverride);

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
        ///////////////////////////////////
        //  新規追加/編集ファンクション  //
        ///////////////////////////////////
        function Mix2_1_registerTable($mode, $arrayReceptData, $aryVariant=array()){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $strActionName = '';

            $arrayRegisterData = array();

            $objTCAFR = loadTable();
            if( $objTCAFR->getPageType()=='apply' ){
                // 申請ページ
                $arrayRegisterData = convertReceptDataToDataForIUD($arrayReceptData);

                $strTextForRegisterForNew = $objTCAFR->getActionNameOfApplyRegistrationForNew();
                $strTextForRegisterForUpdate = $objTCAFR->getActionNameOfApplyRegistrationForUpdate();

                $arySetting = array("Mix2_1","fakeContainer_Register2","Filter1Tbl");
                $arySetting['register_start_scene'] = "<input type=\"button\" value=\"{$strTextForRegisterForNew}\" onClick=location.href=\"javascript:Mix2_1_register_async(1);\" >";

                if( $mode==0 ){
                    $strActionName = $strTextForRegisterForNew;
                }else if( $mode==1 ){
                    // 登録フォームの表示
                    $strActionName = $strTextForRegisterForNew;

                    $msgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-20001");
                    $arySubSetting = array('JsFunctionAddVars'=>",'{$msgBody}'");
                    $arySetting['register_edit_setting'] = array('Edit02Button'=>$arySubSetting);
                }else if( $mode==2 ){
                    // DBへのアクセス
                    $strLockTargetColumnID = $objTCAFR->getLockTargetColumnID();
                    $aryObjColumn = $objTCAFR->getColumns();
                    $objLTColumn = $aryObjColumn[$strLockTargetColumnID];

                    $strValueOfLockTarget = '';
                    if( array_key_exists($objLTColumn->getIDSOP(), $arrayRegisterData)===true ){
                        $varValueOfLockTarget = $arrayRegisterData[$objLTColumn->getIDSOP()];
                        if( is_string($varValueOfLockTarget)===true ){
                            $strValueOfLockTarget = $varValueOfLockTarget;
                        }
                    }
                    if( 0==strlen($strValueOfLockTarget) ){
                        // 新規追加
                        $strActionName = $strTextForRegisterForNew;
                    }else{
                        // 編集
                        $strActionName = $strTextForRegisterForUpdate;
                    }
                    $arrayRegisterData[$objTCAFR->getEditStatusColumnID()] = 1;
                    $arySubSetting = array('Show'=>false);
                    $arySetting['register_finish_setting'] = array('Finish02Button'=>$arySubSetting);                
                }
            }

            if( $strActionName!='' ){
                // 本体ロジックをコール
                $arySetting['system_action_names'] = array('register'=>$strActionName);
                $aryVariant['action_sub_order'] = array('actionNameOnUI'=>$strActionName);
                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
                $arrayResult = registerTableMain($mode, $arrayRegisterData, null, 0, $aryVariant, $arySetting);

                // 結果判定
                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }
            }else{
                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        ////////////////////////////////////////
        //  編集(フォーム表示)ファンクション  //
        ////////////////////////////////////////
        function Mix2_1_updateTable( $numWkPk ){
            // グローバル変数宣言
            global $g;
            $g['error_flag'] = 0;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();
            $aryTmpVariant = array();
            $aryTmpSetting = array();

            $strActionName = '';

            $arrayRegisterData = array();

            $objTCAFR = loadTable();
            if( $objTCAFR->getPageType()=='apply' ){
                // 申請ページ

                $aryTmpVariant['pageType'] = 'view';
                $objTCAFRRV = loadTable(null,$aryTmpVariant,$aryTmpSetting);
                // ----更新対象レコードをSELECT
                $arrayResult = selectRowForUpdate($objTCAFRRV, $numWkPk, 0, 0);
                $selectRowLength = $arrayResult[0];
                $editTgtRow = $arrayResult[1];
                $intErrorType = $arrayResult[2];
                // 更新対象レコードをSELECT----
                if($selectRowLength == 1){
                    $aryVariant['register_default_row'] = $editTgtRow;
                }

                // 編集
                $strActionName = $objTCAFR->getActionNameOfApplyRegistrationForUpdate();
            }

            if( $strActionName!='' ){
                $arySetting['system_action_names'] = array('register'=>$strActionName);
                $msgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-20001",$strActionName);
                $arySubSetting = array('JsFunctionAddVars'=>",'{$msgBody}'");
                $arySetting['register_edit_setting'] = array('Edit02Button'=>$arySubSetting);
                $aryVariant['action_sub_order'] = array('actionNameOnUI'=>$strActionName);

                // 本体ロジックをコール
                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
                $arrayResult = registerTableMain(1, $arrayRegisterData, null, 0, $aryVariant, $arySetting);

                // 結果判定
                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }
            }else{
                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        ////////////////////////////////////////////
        //  編集ステータス変更確認ファンクション  //
        ////////////////////////////////////////////
        function preEditStatusControl($innerSeq, $cmdMode, $strTempTampForCheck)
        {
            global $g;

            // ローカル変数宣言
            $strResultCode = '000';
            $strDetailCode = '000';

            $strActionName = '';

            $objTCAFR = loadTable();
            if($objTCAFR->getPageType()=='apply'){
                switch($cmdMode){
                    case 0: //取下
                        $strActionName = $objTCAFR->getActionNameOfApplyWithdrawn();
                        break;
                    case 1: // 申請取消
                        $strActionName = $objTCAFR->getActionNameOfApplyEditRestart();
                        break;
                    case 2: // 申請実行
                        $strActionName = $objTCAFR->getActionNameOfApplyExecute();
                        break;
                }
            }else if($objTCAFR->getPageType()=='confirm'){
                switch($cmdMode){
                    case 1: // 差戻
                        $strActionName = $objTCAFR->getActionNameOfConfirmReturn();
                        break;
                    case 3: // 承認
                        $strActionName = $objTCAFR->getActionNameOfConfirmAccept();
                        break;
                    case 4: // 却下
                        $strActionName = $objTCAFR->getActionNameOfConfirmNonsuit();
                        break;
                }
            }
            if( $strActionName!='' ){
                $msgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-20001",$strActionName);
                $arrayResult = array($strResultCode, $strDetailCode, $innerSeq, $cmdMode, $strTempTampForCheck , $msgBody);
            }else{
                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        ////////////////////////////////////////
        //  編集ステータス変更ファンクション  //
        ////////////////////////////////////////
        function editStatusControl($innerSeq, $cmdMode, $strTempTampForCheck)
        {
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $strActionName = '';

            $arrayUpdateData = array();
            $data = array();

            $objTCAFR = loadTable();
            if( $objTCAFR->getPageType()=='apply' || $objTCAFR->getPageType()=='confirm' ){

                $arrayUpdateData[$objTCAFR->getEditStatusColumnID()] = $cmdMode;
                $arrayUpdateData[$objTCAFR->getRequiredUpdateDate4UColumnID()] = $strTempTampForCheck;//'UPD_UPDATE_TIMESTAMP'

                $boolUniqueCheckSkip = false; //ユニークチェックをスキップするか？(原則：スキップしない)
                $boolCheckExistLockTargetSkip = false; //ロック対象番号の実在チェックをスキップするか？(原則：スキップしない)
                $boolRequiredColumnCheckSkip = true; //必須カラムの送信チェックをスキップするか？(原則：スキップする)
            }

            if($objTCAFR->getPageType()=='apply'){
                // 申請者ページ
                switch($cmdMode){
                    case 0: // 取下
                        $strActionName = $objTCAFR->getActionNameOfApplyWithdrawn();
                        $boolUniqueCheckSkip = true; //ユニークチェックしない
                        $boolCheckExistLockTargetSkip = true; //ロックNO実在チェックをスキップ
                        break;
                    case 1: // 申請取消
                        $strActionName = $objTCAFR->getActionNameOfApplyEditRestart();
                        $boolUniqueCheckSkip = true; //ユニークチェックしない
                        break;
                    case 2: // 申請実行
                        $strActionName = $objTCAFR->getActionNameOfApplyExecute();
                        break;
                }
            }else if($objTCAFR->getPageType()=='confirm'){
                // 承認者ページ
                switch($cmdMode){
                    case 1: // 差戻
                        $strActionName = $objTCAFR->getActionNameOfConfirmReturn();
                        $boolUniqueCheckSkip = true; //ユニークチェックしない
                        break;
                    case 3: // 承認
                        $strActionName = $objTCAFR->getActionNameOfConfirmAccept();
                        break;
                    case 4: // 却下
                        $strActionName = $objTCAFR->getActionNameOfConfirmNonsuit();
                        $boolUniqueCheckSkip = true; //ユニークチェックしない
                        $boolCheckExistLockTargetSkip = true; //ロックNO実在チェックをスキップ
                        break;
                }
            }

            if( $strActionName!='' ){
                $aryVariant['objTable'] = $objTCAFR;
                $arySetting['system_action_names'] = array('update'=>$strActionName);
                $aryVariant['action_sub_order'] = array('name'=>'editStatus'
                                                        ,'uniqueCheckSkip'=>$boolUniqueCheckSkip
                                                        ,'checkExistLockTargetSkip'=>$boolCheckExistLockTargetSkip
                                                        ,'requiredColumnCheckSkip'=>$boolRequiredColumnCheckSkip
                                                        ,'actionNameOnUI'=>$strActionName
                                                        );

                // 本体ロジックをコール

                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/04_updateTable.php");
                $arrayResult = updateTableMain(3, $innerSeq, $arrayUpdateData, null, 0, $aryVariant, $arySetting);

                // 結果判定
                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        //////////////////////////////
        //  内容修正ファンクション  //
        //////////////////////////////
        function Mix1_1_updateTable($mode, $innerSeq, $arrayReceptData = null){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $strActionName = '';
            $arrayUpdateData = array();

            $objTCAFR = loadTable();
            if( $objTCAFR->getPageType()=='apply' || $objTCAFR->getPageType()=='confirm' ){
                $arrayUpdateData = convertReceptDataToDataForIUD($arrayReceptData);
                $arySetting = array("Mix1_1","fakeContainer_Update1","Filter1Tbl");
            }

            if($objTCAFR->getPageType()=='apply'){
                // 申請者ページ
                // 修正
                $strActionName = $objTCAFR->getActionNameOfApplyUpdate();
            }else if($objTCAFR->getPageType()=='confirm'){
                // 承認者ページ
                // 修正
                $strActionName = $objTCAFR->getActionNameOfConfirmUpdate();
            }
            if( $strActionName!='' ){
                $arySetting['system_action_names'] = array('update'=>$strActionName);

                $msgBody = $g['objMTS']->getSomeMessage("ITAWDCH-STD-20001",$strActionName);
                $arySubSetting = array('JsFunctionAddVars'=>",'{$msgBody}'");
                $arySetting['update_edit_setting'] = array('Edit02Button'=>$arySubSetting);
                $aryVariant['action_sub_order'] = array('actionNameOnUI'=>$strActionName);

                // 本体ロジックをコール
                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/04_updateTable.php");
                $arrayResult = updateTableMain($mode, $innerSeq, $arrayUpdateData, null, 0, $aryVariant, $arySetting);

                // 結果判定
                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
        /////////////////////////////////
        //  廃止/復活ファンクション    //
        /////////////////////////////////
        function Mix1_1_deleteTable($mode, $innerSeq, $arrayReceptData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $aryVariant = array();
            $arySetting = array();

            $arratDeleteData = array();

            $objTCAFR = loadTable();
            if( $objTCAFR->getPageType()=='apply' || $objTCAFR->getPageType()=='confirm' || $objTCAFR->getPageType()=='view' ){
                $arratDeleteData = convertReceptDataToDataForIUD($arrayReceptData);
                $arySetting = array("Mix1_1","fakeContainer_Delete1","fakeContainer_Delete1");
                if( $mode == 1 || $mode == 4 ){
                    //廃止
                    $strActionName = $objTCAFR->getActionNameOfLogicDeleteOn();
                    $arySetting['system_action_names']['delete_on'] = $strActionName;
                    $arySetting['system_action_names']['delete_off'] = $objTCAFR->getActionNameOfLogicDeleteOff();
                }else if( $mode == 3 || $mode == 5 ){
                    //復活
                    $strActionName = $objTCAFR->getActionNameOfLogicDeleteOff();
                    $arySetting['system_action_names']['delete_on'] = $objTCAFR->getActionNameOfLogicDeleteOn();
                    $arySetting['system_action_names']['delete_off'] = $strActionName;
                }
            }

            if( $strActionName!='' ){
                $aryVariant['action_sub_order'] = array('actionNameOnUI'=>$strActionName);
                // 本体ロジックをコール
                require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
                $arrayResult = deleteTableMain($mode, $innerSeq, $arratDeleteData, null, 0, $aryVariant, $arySetting);

                // 結果判定
                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

                $arrayResult[0] = '';
                $arrayResult[1] = '';
                $arrayResult[2] = 'unexpected_error';
                if(0 < $g['dev_log_developer']){
                    $arrayResult[1] = '999';
                }
            }
            return makeAjaxProxyResultStream($arrayResult);
        }
    }
?>
