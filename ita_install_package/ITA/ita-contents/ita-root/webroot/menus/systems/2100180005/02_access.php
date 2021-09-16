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
    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    $g['requestByHA'] = 'forHADAC'; //[H]tml-[A]AX.[D]b_[A]ccess_[C]ore

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // access系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_access_01.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
    require_once 'HTML/AJAX/Server.php';
    
    
    class Db_Access_Core {

        //////////////////////////////////////////////
        //  (Conductorインスタンス)予約取消  //
        //////////////////////////////////////////////
        function bookCancelConducrtorInstance($intConductorInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_conductor_ins_control.php");
            
            $arrayResult = bookCancelOneOfConductorInstances($intConductorInstanceId);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult;
        }
        //////////////////////////////////////////////
        //  (Conductorインスタンス)緊急停止  //
        //////////////////////////////////////////////
        function scramConducrtorInstance($intConductorInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_conductor_ins_control.php");
            
            $arrayResult = scramOneOfConductorInstances($intConductorInstanceId);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult;
        }
        //////////////////////////////////////////////
        //  (Conductorインスタンス)保留解除  //
        //////////////////////////////////////////////
        function holdReleaseNodeInstance($intNodeInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_conductor_ins_control.php");
            
            $arrayResult = holdReleaseOneOfNodeInstances($intNodeInstanceId);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult;
        }

        //////////////////////////////////////////////
        //  (Conductorクラス編集)JSON形式の取得  //
        //////////////////////////////////////////////
        function printconductorClass($intConductorClassId){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");

            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorInfoRegConductor($intConductorClassId);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }
        ////////////////////////////////
        //  オペレーション一覧の表示  //
        ////////////////////////////////

        function printOperationList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");

            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printOperationListInfoRegConductor();

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }
        ////////////////////////////////
        //  Conductor一覧の表示  //
        ////////////////////////////////
        function printConductorList( $conductorInstanceID ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorListInfoRegConductor();

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            #return $arrayResult[2];


            $tmpResult=json_decode($arrayResult[2],true);

            $strResult = "";
            #$tmpResult=array();
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
            $arrayResult = getInfoFromOneOfConductorInstances($conductorInstanceID,0);

            // 結果判定
            if($arrayResult[0]=="1"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }

            foreach ($arrayResult[5] as $key => $value) {
                if( $value["I_NODE_TYPE_ID"] == 4 && $value["CONDUCTOR_INSTANCE_CALL_NO"] !="" ){
                    $tmparrayResult = getInfoFromOneOfConductorInstances($value["CONDUCTOR_INSTANCE_CALL_NO"],0);

                    $insMov=array();
                    $insMov['CONDUCTOR_CLASS_NO']  = $value["CONDUCTOR_INSTANCE_CALL_NO"];
                    $insMov['CONDUCTOR_NAME']  =  $value["I_PATTERN_NAME"];
                    $tmpResult[]=$insMov;
                }
            }

            $strResult = json_encode($tmpResult,JSON_UNESCAPED_UNICODE);

            return $strResult;

        }

        //////////////////////////////////////////////
        //   Movement一覧の表示  //
        //////////////////////////////////////////////
        function printMatchedPatternListJson($conductorInstanceID=""){
            // グローバル変数宣言
            global $g;
            #648 対応
            // ローカル変数宣言
            $arrayResult = array();
            
            $strResult = "";
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
            $arrayResult = getInfoFromOneOfConductorInstances($conductorInstanceID,0);

            // 結果判定
            if($arrayResult[0]=="1"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }

            $tmpResult = array();
            foreach ($arrayResult[5] as $key => $value) {
                if( $value["I_NODE_TYPE_ID"] == 3 ){
                    $insMov=array();
                    $insMov['PATTERN_ID']  = $value["I_PATTERN_ID"];
                    $insMov['ORCHESTRATOR_ID']  = $value["I_ORCHESTRATOR_ID"];
                    $insMov['PATTERN_NAME']  = $value["I_PATTERN_NAME"];
                    $tmpResult[]=$insMov;
                }
            }
            $strResult = json_encode($tmpResult,JSON_UNESCAPED_UNICODE);

            return $strResult;

        }

        ////////////////////////////////////
        //  Concuctorインスタンスステータス  //
        ////////////////////////////////////
        function printConductorStatus($intConductorInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_conductor_ins_control.php");

            $arrayResult = printConductorInstanceStatus($intConductorInstanceId);

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];

        }

        ////////////////////////////////
        //  Symphony一覧の表示  //
        ////////////////////////////////

        function printSymphonyList( $conductorInstanceID ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_operation_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/"."2100180003"."/81_print_conductor_info.php");
        
            $arrayResult =  printConductorListInfoRegSymphony();

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            #return $arrayResult[2];
            #648 対応
            $tmpResult=json_decode($arrayResult[2],true);

            $strResult = "";
            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/75_conductorInstanceAdmin.php");
            $arrayResult = getInfoFromOneOfConductorInstances($conductorInstanceID,0);

            // 結果判定
            if($arrayResult[0]=="1"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }

            require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/73_symphonyInstanceAdmin.php");

            foreach ($arrayResult[5] as $key => $value) {
                if( $value["I_NODE_TYPE_ID"] == 10 && $value["CONDUCTOR_INSTANCE_CALL_NO"] !="" ){
                    $tmparrayResult = getSingleSymphonyInfoFromSymphonyInstances($value["CONDUCTOR_INSTANCE_CALL_NO"],0);

                    $insMov=array();
                    $insMov['SYMPHONY_CLASS_NO']  = $tmparrayResult[4]["SYMPHONY_INSTANCE_NO"];
                    $insMov['SYMPHONY_NAME']  =  $tmparrayResult[4]["I_SYMPHONY_NAME"];
                    $tmpResult[]=$insMov;
                }
            }

            $strResult = json_encode($tmpResult,JSON_UNESCAPED_UNICODE);

            return $strResult;


        }


        ////////////////////////////////
        //  通知一覧の表示  //
        ////////////////////////////////

        function printNoticeList( $conductorClassID="", $mode="" ){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            if( gettype($conductorClassID) == "NULL" )$conductorClassID = "";


            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
        
            $arrayResult =  printNoticeListInfoRegConductor( $conductorClassID , "EXECUTE" );

            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return $arrayResult[2];
        }

        ////////////////////////////////
        //  ステータスの表示  //
        ////////////////////////////////

        function printNoticeStatusList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();
            
            $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
            require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");
            #require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_print_conductor_info.php");
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/"."2100180003"."/81_print_conductor_info.php");
        
            $arrayResult =  printNoticeStatusListInfoRegConductor();
            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            //--　インスタンス情報で上書き処理 --//
            //--　インスタンス情報で上書き処理 --//
            
            return $arrayResult[2];
        }

    }



    class Db_Access extends Db_Access_Core {

    
    }
    
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);    
    $server->handleRequest();


?>
