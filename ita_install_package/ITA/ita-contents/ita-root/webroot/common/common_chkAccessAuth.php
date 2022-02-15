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
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    // PHP エラー時のログ出力先を設定
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

    ini_set('display_errors',1);
    ini_set('log_errors',1);
    ini_set('error_log', $logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';
    $web_auth_config                 = '/libs/webcommonlibs/web_auth_config.php';
    $web_function_for_get_sysconfig  = '/libs/webcommonlibs/web_functions_for_get_sysconfig.php';

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );
    require_once ($root_dir_path . $web_auth_config);
    require_once ($root_dir_path . $web_function_for_get_sysconfig);

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
        
    $ErrorMsgBase = "([FILE]%s[LINE]%s)%s";

    //クエリデータを保管
    if(array_key_exists('UserId',$_GET) === false) {
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60002");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }
    $UserId = htmlspecialchars($_GET['UserId'], ENT_QUOTES, "UTF-8");

    if(array_key_exists('OperationNoUAPK',$_GET) === false) {
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60003");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }
    $OperationNoUAPK = htmlspecialchars($_GET['OperationNoUAPK'], ENT_QUOTES, "UTF-8");

    if(array_key_exists('PatternId',$_GET) === false) {
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60004");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }
    $PatternId = htmlspecialchars($_GET['PatternId'], ENT_QUOTES, "UTF-8");

    //システムコンフィグを取得
    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
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
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }
    $loginUserName = $auth->getUsername();

    //D_ACCOUNT_LISTから対象のユーザIDのデータを取得し、ユーザ名が一致するかを確認
    $sql =        " SELECT USER_ID, USERNAME \n";
    $sql = $sql . " FROM D_ACCOUNT_LIST \n";
    $sql = $sql . " WHERE  DISUSE_FLAG = '0' \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $result = $objQuery->sqlExecute();
    if(!$result){
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $accountData = array();
    while ($row = $objQuery->resultFetch()){
        if($row['USER_ID'] == $UserId){
            $accountData = $row; //レコードは1つしかない想定
        }
    }

    if(empty($accountData)){
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    //クエリパラメータのuser_idとセッションが持つユーザ名情報が不一致
    if($accountData['USERNAME'] != $loginUserName){       
        $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60005");
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    unset($objQuery);

    ///////////////////////////////////////////////////
    // ファイル組み込み
    ///////////////////////////////////////////////////
    require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
    $RBACobj = new RoleBasedAccessControl($objDBCA);

    $OpeAccessAuthStr = "";
    $ret = $RBACobj->getOperationAccessAuth($OperationNoUAPK,$OpeAccessAuthStr);
    if($ret !== true) {
        if($ret === false) {
            $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60006");
        } else {
            $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60007");
        }
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $MovementAccessAuthStr = "";
    $ret = $RBACobj->getMovementAccessAuth($PatternId,$MovementAccessAuthStr);
    if($ret !== true) {
        if($ret === false) {
            $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60008");
        } else {
            $AddMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-60009");
        }
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $AccessAuthAry   = array();
    $AccessAuthAry[] = explode(",",$OpeAccessAuthStr);
    $AccessAuthAry[] = explode(",",$MovementAccessAuthStr);
    $ResultAccessAuthStr = "";
    $ret = $RBACobj->AccessAuthExclusiveAND($AccessAuthAry,$ResultAccessAuthStr);
    if($ret === false) {
        $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-ERR-1118"); // アクセス許可ロール不適合
        HttpResponse("NG","",$ErrorMsg);
    } else {
        HttpResponse("OK",$ResultAccessAuthStr,"");
    }
    exit;
}catch (Exception $e){
    $Exception = json_decode($e->getMessage(),true);
    if($Exception['ERROR_LOG'] != "") {
        if(function_exists("web_log")) {
            web_log($Exception['ERROR_LOG']);
        } else {
            error_log($Exception['ERROR_LOG']);
        }
    }
    $ErrorMsg = $Exception['RESPONS_MSG'];
    if ( $sessiontimeoutFlag == TRUE ){
        $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-MNU-1300006");
        HttpResponse("redirectOrderForHADACClient",["0","/common/common_auth.php?login",'status'],$ErrorMsg);
    }
    HttpResponse("ER","",$Exception['RESPONS_MSG']);
}
function HttpResponse($Status,$AccessAuth,$ErrorMsg) {
    $ResponsAry = array("STATUS"=>$Status,"ACCESS_AUTH"=>$AccessAuth,"ERROR_MSG"=>$ErrorMsg);
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($ResponsAry);
    exit;
}
?>

