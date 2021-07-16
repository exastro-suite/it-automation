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
try {
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );
        
    ///////////////////////////////////////////////////
    // ファイル組み込み
    ///////////////////////////////////////////////////
    require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");

    require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_functions.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');

    global $db_model_ch;
    global $objDBCA;
    global $objMTS;
    global $g;

    $cmDBobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$g['login_id']);

    $logfile = "";
    $log_level = "";
    $DBobj = new LocalDBAccessClass($db_model_ch,$cmDBobj,$objDBCA,$objMTS,$g['login_id'],$logfile,$log_level);


    $sqlBody = "SELECT   HOSTNAME, PROTOCOL, PORT   FROM B_CICD_IF_INFO WHERE DISUSE_FLAG = '0'";
    $arrayBind = array();
    $url = "";
    $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
    if($objQuery === false) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName ." Failed to get CICD interface information.\n". $DBobj->GetLastErrorMsg();
        throw new Exception($ErrorMsg);
    }
    $num_of_rows = $objQuery->effectedRowCount();
    if($num_of_rows === 0) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName ." CICD interface information is not registered.\n". $DBobj->GetLastErrorMsg();
        throw new Exception($ErrorMsg);
    }
    $tgtRepoListRow = array();
    while ( $row = $objQuery->resultFetch() ){
        $loopbackip ="/^127\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$/";
        $ret = preg_match($loopbackip,trim($row['HOSTNAME']));
        if($ret == 1) {
            $url = "";
        } else {
            $url = sprintf("%s://%s:%s",$row['PROTOCOL'],$row['HOSTNAME'],$row['PORT']);
        }
    }
    $ErrorMsg = "";
    HttpResponse("OK",$url,"");

}catch (Exception $e){
    web_log($e->getMessage());
    $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-MNU-5000002");
    HttpResponse("ER","",$ErrorMsg);
}
function HttpResponse($Status,$URL,$ErrorMsg) {
    $ResponsAry = array("STATUS"=>$Status,"URL"=>$URL,"ERROR_MSG"=>$ErrorMsg);
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($ResponsAry);
    exit;
}
?>
