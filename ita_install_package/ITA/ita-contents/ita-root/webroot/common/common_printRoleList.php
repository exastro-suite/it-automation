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

    ini_set('display_errors',0);
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

    //クエリデータを保管
    if(array_key_exists('user_id',$_GET) === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }
    $user_id = htmlspecialchars($_GET['user_id'], ENT_QUOTES, "UTF-8");

    //システムコンフィグを取得
    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
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
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }
    $loginUserName = $auth->getUsername();

    //D_ACCOUNT_LISTから対象のユーザIDのデータを取得し、ユーザ名が一致するかを確認
    $sql =        " SELECT USER_ID, USERNAME \n";
    $sql = $sql . " FROM D_ACCOUNT_LIST \n";
    $sql = $sql . " WHERE  DISUSE_FLAG = '0' \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }

    $result = $objQuery->sqlExecute();
    if(!$result){
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }

    $accountData = array();
    while ($row = $objQuery->resultFetch()){
        if($row['USER_ID'] == $user_id){
            $accountData = $row; //レコードは1つしかない想定
        }
    }

    if(empty($accountData)){
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }

    //クエリパラメータのuser_idとセッションが持つユーザ名情報が不一致
    if($accountData['USERNAME'] != $loginUserName){       
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }

    unset($objQuery);


    ///////////////////////////////////////////////////
    // ファイル組み込み
    ///////////////////////////////////////////////////
    require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
    $obj = new RoleBasedAccessControl($objDBCA);

    if(array_key_exists('user_id',$_GET)=== false) {
        throw new Exception("User_id is not set in UR parameter");
    }

    $result_role_info = $obj->getUserRoleList($user_id);
    if($result_role_info === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }

    //全ロール取得
    $RoleID2Name = array();
    $RoleName2ID = array();
    $result_role_info_all = $obj->getAllRoleSearchHashList($user_id,$AllRoleID2Name,$AllRoleName2ID);
    if($result_role_info_all === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-60001"));
    }
    $allRolelist = $AllRoleID2Name;

    // 権限無しロールをリストへ追加(ロール名=********)
    foreach ($allRolelist as $tmpRoleid => $tmpRole) {
        $invisiflg=0;
        foreach ($result_role_info as $key => $tmpRoleinfo) {
            if ( $tmpRoleid == $tmpRoleinfo['ROLE_ID']  ){
                $invisiflg=1;
                break;
            }
        }
        if( $invisiflg == 0 && $tmpRole['DISUSE_FLAG'] == 0 ){
            $result_role_info[] = array( 
                'ROLE_ID' => $tmpRoleid,
                'ROLE_NAME' => $objMTS->getSomeMessage("ITAWDCH-STD-11102"),  #"********", 
                'DEFAULT' => ""
            );
        }
    }

    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($result_role_info);

}catch (Exception $e){
    if(function_exists("web_log")) {
        web_log($e->getMessage());
    } else {
        error_log($e->getMessage());
    }
    $result_role_info = array();
    header("Content-Type: text/html; charset=utf-8");
    if ( $sessiontimeoutFlag == TRUE ){
        $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-MNU-1300006");
        $result_role_info=["redirectOrderForHADACClient",["0","/common/common_auth.php?login",'status'],$ErrorMsg,$_GET];
    }
    echo json_encode($result_role_info);
}

?>

