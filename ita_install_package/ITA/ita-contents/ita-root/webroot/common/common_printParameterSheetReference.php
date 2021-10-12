<?php
//   Copyright 2021 NEC Corporation
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
    global $arySYSCON;

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
    $create_param_menu               = '/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php';
    $web_auth_config                 = '/libs/webcommonlibs/web_auth_config.php';
    $web_function_for_get_sysconfig  = '/libs/webcommonlibs/web_functions_for_get_sysconfig.php';

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );
    require_once ( $root_dir_path . $create_param_menu);
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
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }
    $user_id = htmlspecialchars($_GET['user_id'], ENT_QUOTES, "UTF-8");

    if(array_key_exists('menu_id',$_GET) === false) {
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }
    $menu_id = htmlspecialchars($_GET['menu_id'], ENT_QUOTES, "UTF-8");

    //システムコンフィグを取得
    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }
    $arySYSCON = $tmpAryRetBody[0]['Items'];
    unset($tmpAryRetBody);

    //Sessionのログインチェックをして、ユーザが一致していたら処理を継続
    $auth = null;
    saLoginExecute($auth, $objDBCA, null, false);
    $loginCheck = $auth->checkAuth();
    if($loginCheck == false){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }
    $loginUserName = $auth->getUsername();

    //D_ACCOUNT_LISTから対象のユーザIDのデータを取得し、ユーザ名が一致するかを確認
    $sql =        " SELECT USER_ID, USERNAME \n";
    $sql = $sql . " FROM D_ACCOUNT_LIST \n";
    $sql = $sql . " WHERE  DISUSE_FLAG = '0' \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }

    $result = $objQuery->sqlExecute();
    if(!$result){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }

    $accountData = array();
    while ($row = $objQuery->resultFetch()){
        if($row['USER_ID'] == $user_id){
            $accountData = $row; //レコードは1つしかない想定
        }
    }

    if(empty($accountData)){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }

    //クエリパラメータのuser_idとセッションが持つユーザ名情報が不一致
    if($accountData['USERNAME'] != $loginUserName){       
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }

    unset($objQuery);


    ///////////////////////////////////////////////////
    // パラメータシート参照の選択肢となる項目を取得
    ///////////////////////////////////////////////////
    $referenceSheetType3View = new ReferenceSheetType3View($objDBCA, $db_model_ch);
    $sql = $referenceSheetType3View->createSselect("WHERE DISUSE_FLAG = '0' AND MENU_ID = :MENU_ID");
    $sqlBind = array('MENU_ID' => $menu_id);
    $result = $referenceSheetType3View->selectTable($sql, $sqlBind);
    if(!is_array($result)){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6003"));
    }
    $result_type3_reference_item = $result;

    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
    $obj = new RoleBasedAccessControl($objDBCA);
    $ret = $obj->getAccountInfo($user_id);
    if($ret === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }

    // 権限があるデータのみに絞る
    $ret = $obj->chkRecodeArrayAccessPermission($result_type3_reference_item);
    if($ret === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }

    //「メニュー作成・定義」用に配列を形成
    $select_type3_reference_list = array();
    foreach($result_type3_reference_item as $data){
        $target = array();
        $target['itemId'] = $data['ITEM_ID'];
        $target['itemPulldown'] = ($data['FULL_COL_GROUP_NAME'] != "") ? $objMTS->getSomeMessage("ITACREPAR-MNU-102612")."/".$data['FULL_COL_GROUP_NAME']."/".$data['ITEM_NAME'] : $objMTS->getSomeMessage("ITACREPAR-MNU-102612")."/".$data['ITEM_NAME'];
        array_push($select_type3_reference_list, $target);
    }

    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($select_type3_reference_list);

}catch (Exception $e){
    if(function_exists("web_log")) {
        web_log($e->getMessage());
    } else {
        error_log($e->getMessage());
    }
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode('failed');
}

?>

