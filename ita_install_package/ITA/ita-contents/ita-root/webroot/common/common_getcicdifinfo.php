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

    ///////////////////////////////////////////////////
    // アクセス制限
    ///////////////////////////////////////////////////
    //ブラウザから直接アクセスさせない
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
        // アクセスログ出力(リダイレクト判定NG)
        web_log($objMTS->getSomeMessage("ITAWDCH-MNU-1190093"));
        
        // 不正操作によるアクセス警告画面にリダイレクト
        webRequestForceQuitFromEveryWhere(403,10000403);
        exit();
    }

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );

    ///////////////////////////////////////////////////
    // ファイル組み込み
    ///////////////////////////////////////////////////
    require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
    require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");
    require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_get_sysconfig.php");

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

    //クエリデータを保管
    if(array_key_exists('user_id',$_GET) === false) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000");
        throw new Exception($ErrorMsg);
    }
    $user_id = htmlspecialchars($_GET['user_id'], ENT_QUOTES, "UTF-8");

    //システムコンフィグを取得
    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000");
        throw new Exception($ErrorMsg);
    }
    $arySYSCON = $tmpAryRetBody[0]['Items'];
    unset($tmpAryRetBody);

    //Sessionのログインチェックをして、ユーザが一致していたら処理を継続
    $auth = null;
    $sessiontimeoutFlag = FALSE;
    saLoginExecute($auth, $objDBCA, null, false);
    $loginCheck = $auth->checkAuth();
    if($loginCheck == false){
        $sessiontimeoutFlag = TRUE;
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000");
        throw new Exception($ErrorMsg);
    }
    $loginUserName = $auth->getUsername();

    //D_ACCOUNT_LISTから対象のユーザIDのデータを取得し、ユーザ名が一致するかを確認
    $sqlBody =            " SELECT USER_ID, USERNAME \n";
    $sqlBody = $sqlBody . " FROM D_ACCOUNT_LIST \n";
    $sqlBody = $sqlBody . " WHERE  DISUSE_FLAG = '0' \n";
    $arrayBind = array();

    $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
    if($objQuery === false){
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000") . "\n" . $DBobj->GetLastErrorMsg();
        throw new Exception($ErrorMsg);
    }

    $num_of_rows = $objQuery->effectedRowCount();
    if($num_of_rows === 0) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000") . "\n" . $DBobj->GetLastErrorMsg();
        throw new Exception($ErrorMsg);
    }

    $accountData = array();
    while ($row = $objQuery->resultFetch()){
        if($row['USER_ID'] == $user_id){
            $accountData = $row; //レコードは1つしかない想定
        }
    }

    if(empty($accountData)){
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000");
        throw new Exception($ErrorMsg);
    }

    //クエリパラメータのuser_idとセッションが持つユーザ名情報が不一致
    if($accountData['USERNAME'] != $loginUserName){       
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000");
        throw new Exception($ErrorMsg);
    }

    unset($objQuery);

    $sqlBody = "SELECT   HOSTNAME, PROTOCOL, PORT   FROM B_CICD_IF_INFO WHERE DISUSE_FLAG = '0'";
    $arrayBind = array();
    $url = "";
    $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
    if($objQuery === false) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6000") . "\n" . $DBobj->GetLastErrorMsg();
        throw new Exception($ErrorMsg);
    }
    $num_of_rows = $objQuery->effectedRowCount();
    if($num_of_rows === 0) {
        $strFxName = "[FILE]" . basename(__FILE__) . "[LINE]" . __LINE__;
        $ErrorMsg = $strFxName . $objMTS->getSomeMessage("ITACICDFORIAC-ERR-6001") . "\n" . $DBobj->GetLastErrorMsg();
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
    if ( $sessiontimeoutFlag == TRUE ){
        $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-MNU-1300006");
        HttpResponse("redirectOrderForHADACClient",["0","/common/common_auth.php?login",'status'],$ErrorMsg);
    }
    HttpResponse("ER","",$ErrorMsg);
}
function HttpResponse($Status,$URL,$ErrorMsg) {
    $ResponsAry = array("STATUS"=>$Status,"URL"=>$URL,"ERROR_MSG"=>$ErrorMsg);
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($ResponsAry);
    exit;
}
?>
