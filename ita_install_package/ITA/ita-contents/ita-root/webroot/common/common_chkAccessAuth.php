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

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );
        
    $ErrorMsgBase = "([FILE]%s[LINE]%s)%s";
    ///////////////////////////////////////////////////
    // ファイル組み込み
    ///////////////////////////////////////////////////
    require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
    $RBACobj = new RoleBasedAccessControl($objDBCA);

    if(array_key_exists('OperationNoUAPK',$_GET)=== false) {
        $AddMsg = "OperationNoUAPK is not set in URL parameter";
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $OperationNoUAPK = htmlspecialchars($_GET['OperationNoUAPK'], ENT_QUOTES, "UTF-8");
    if(array_key_exists('PatternId',$_GET)=== false) {
        $AddMsg = "PatternId is not set in URL parameter";
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }
    $PatternId = htmlspecialchars($_GET['PatternId'], ENT_QUOTES, "UTF-8");

    $OpeAccessAuthStr = "";
    $ret = $RBACobj->getOperationAccessAuth($OperationNoUAPK,$OpeAccessAuthStr);
    if($ret !== true) {
        if($ret === false) {
            $AddMsg = "Input operation list access error.";
        } else {
            $AddMsg = "OperationID not found.";
        }
        $Exception['ERROR_LOG'] = sprintf($ErrorMsgBase,__FILE__,__LINE__,$AddMsg);
        $Exception['RESPONS_MSG'] = $objMTS->getSomeMessage("ITAWDCH-ERR-112"); // システムエラー
        throw new Exception(json_encode($Exception));
    }

    $MovementAccessAuthStr = "";
    $ret = $RBACobj->getMovementAccessAuth($PatternId,$MovementAccessAuthStr);
    if($ret !== true) {
        if($ret === false) {
            $AddMsg = "Movement list access error.";
        } else {
            $AddMsg = "MovementID not found.";
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
    HttpResponse("ER","",$Exception['RESPONS_MSG']);
}
function HttpResponse($Status,$AccessAuth,$ErrorMsg) {
    $ResponsAry = array("STATUS"=>$Status,"ACCESS_AUTH"=>$AccessAuth,"ERROR_MSG"=>$ErrorMsg);
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($ResponsAry);
    exit;
}
?>

