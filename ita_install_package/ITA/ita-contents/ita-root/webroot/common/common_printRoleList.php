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
    $obj = new RoleBasedAccessControl($objDBCA);

    if(array_key_exists('user_id',$_GET)=== false) {
        throw new Exception("User_id is not set in UR parameter");
    }

    $user_id = $_GET['user_id'];

    $result_role_info = $obj->getUserRoleList($user_id);
    if($result_role_info === false) {
        throw new Exception("Failed to get user role information");
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
    echo json_encode($result_role_info);
}

?>

