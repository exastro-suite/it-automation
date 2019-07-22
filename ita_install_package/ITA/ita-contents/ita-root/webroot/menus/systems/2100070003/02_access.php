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

            //ホスト用
            $int_seq_no = 3;
            $arrayResult02 = AddSelectTagToDynamicSelectTab($objTable, "update_table", $int_seq_no, $aryVariant, $arySetting, $aryOverride);

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

            // 結果判定
            if( $arrayResult01[0]=="000"){
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
        



        //値代入モーダルで「バリデート」ボタンを押下した際
        //画面に表示されている値とテンプレートを組み合わせ、OpenStackにバリデートのRESTをコールする。
        //・・・・なのだが
        //RESTを投げるまでは完成しているが、現在正常なレスポンスが戻ってきておらず
        //OpenStack側の不具合の可能性あり。
        //今後OpenStackがバージョンアップするので、その際に本機能を復活させ、動作確認を行うこと。
        function validateHeat($petternId,$projectId,$templateData){

            // グローバル変数宣言
            global $g;

            //REST用モジュール読み出し
            require_once($g['root_dir_path'] . "/libs/backyardlibs/openstack_driver/openStack_RESTCallLib.php");

            $response=array();
            $response['data']=$petternId; 
            $response['projectData']=$projectId;    
            $response['template']=$templateData;



            // インタフェース情報取得
            $openstack_if_info=[];
            $aryRetBody = singleSQLExecuteAgent("SELECT * " 
                         ."FROM B_OPENST_IF_INFO TAB_1 "
                         ."WHERE TAB_1.DISUSE_FLAG = '0'", array(), "");
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $openstack_if_info= $row;
            }

            $content=[
                "auth"=>[
                    "passwordCredentials"=>[
                        "username"=>$openstack_if_info['OPENST_USER'],
                        "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                    ],
                    "tenantId"=>$projectId
                ]
            ];
            $content=json_encode($content,true);

            $aryResult       = array();
            $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

            if($ret != '200'){
                $FREE_LOG = $g['objMTS']->getSomeMessage("ITAOPENST-ERR-104010");

                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($FREE_LOG);
            }else{
                //通信成功時（正常系）
                $resData=json_decode($aryResult['ResponsContents'],true);
                $token=$resData['access']['token']['id'];
            }

           // コールするAPI一覧
            $apiArray=[];
            for ($j=0; $j < count($resData['access']['serviceCatalog']); $j++) { 
                $node=$resData['access']['serviceCatalog'][$j];
                $name=$node['type'];
                $value=$node['endpoints'][0]['publicURL'];
                $apiArray[$value]=$name;
            }

            $content=array(
                "stack_name"=>"test",
                "template"=>$templateData
            );
            $content=json_encode($content,true);

            $apiUrl=$this->_getApiUrl("orchestration",$apiArray);
            $aryResult=array();
            $ret = openstack_rest_call("VALIDATE_TEMPLATE", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, $content, $aryResult);

            if($ret != '200'){
                var_dump($aryResult);
                exit;
                throw new Exception($FREE_LOG);
            }else{
                //通信成功時（正常系）
                $resData=json_decode($aryResult['ResponsContents'],true);
                
            }
            echo json_encode($resData,true);
            exit;

        }
    

        function _getApiUrl($category,$apiArray){        

            $category=mb_strtolower($category);
            foreach($apiArray as $key => $value){       

                if(preg_match("/$category/",$value)){
                    return $key;
                }
            }
            return null;        

        }


        //バッチ処理でOpenStackと同期したマスタデータを取得する。
        // petternId:テンプレートデータを特定するのに必要
        // projectId:プロジェクト毎に、読み込むマスタデータが異なるので必要

        function getOpenStackMasterData($petternId,$projectId,$command){

            // グローバル変数宣言
            global $g;

            $response=array();
            $response['data']=$petternId; 
            $response['projectData']=$projectId;    
            $response['command']=$command;

            //パラメータからテナントIDを取得

            //機器一覧
            $aryRetBody = singleSQLExecuteAgent("SELECT * FROM C_STM_LIST WHERE DISUSE_FLAG = '0'", array(), "");
            $objQuery = $aryRetBody[1];
            $aryDataSet = array();
            while($row = $objQuery->resultFetch() ){
                $aryDataSet[]= $row;
            }
            $response['host']=$aryDataSet;

            //紐づいているテンプレートを取得
            $aryRetBody = singleSQLExecuteAgent("SELECT OPENST_TEMPLATE from E_OPENST_PATTERN WHERE PATTERN_ID = :PATTERN_ID"
                , array("PATTERN_ID"=>$petternId), "");
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $templateFile=$row['OPENST_TEMPLATE'];
            }

            //MasterSyncからのマスタデータ取得
            $aryRetBody = singleSQLExecuteAgent("SELECT NAME,VALUE from B_OPENST_MASTER_SYNC WHERE TENANT_ID = :TENANT_ID OR TENANT_ID = 'general'"
                , array("TENANT_ID"=>$projectId), "");
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $response[$row['NAME']]=json_decode($row['VALUE'],true);
            }

            $fileId=str_pad($petternId,10,0,STR_PAD_LEFT);


            if(isset($templateFile) && $templateFile!=""){
                $ret=file_get_contents($g['root_dir_path'] . "/uploadfiles/2100070002/OPENST_TEMPLATE/".$fileId."/".$templateFile);
            }else{
                $ret = $g['objMTS']->getSomeMessage("ITAOPENST-ERR-104020");
            }

            $response['template']=$ret;

    
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
            exit;
        }

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
        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
