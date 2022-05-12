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
    global $arySYSCON, $p_login_id, $p_login_name, $p_login_name_jp, $ACRCM_id; //ライブラリ呼び出しでエラーにしないよう。

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
    $common_getInfo_loadTable        = '/libs/commonlibs/common_getInfo_LoadTable.php';

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );
    require_once ($root_dir_path . $create_param_menu);
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
    require_once ($root_dir_path . $common_getInfo_loadTable);

    //クエリデータを保管
    if(array_key_exists('user_id',$_GET) === false) {
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }
    $user_id = htmlspecialchars($_GET['user_id'], ENT_QUOTES, "UTF-8");

    if(array_key_exists('link_id',$_GET) === false) {
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }
    $link_id = htmlspecialchars($_GET['link_id'], ENT_QUOTES, "UTF-8");

    //システムコンフィグを取得
    $tmpAryRetBody = getSystemConfigFromConfigList($objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
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
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }
    $loginUserName = $auth->getUsername();

    //D_ACCOUNT_LISTから対象のユーザIDのデータを取得し、ユーザ名が一致するかを確認
    $sql =        " SELECT USER_ID, USERNAME \n";
    $sql = $sql . " FROM D_ACCOUNT_LIST \n";
    $sql = $sql . " WHERE  DISUSE_FLAG = '0' \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    $result = $objQuery->sqlExecute();
    if(!$result){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    $accountData = array();
    while ($row = $objQuery->resultFetch()){
        if($row['USER_ID'] == $user_id){
            $accountData = $row; //レコードは1つしかない想定
        }
    }

    if(empty($accountData)){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    //クエリパラメータのuser_idとセッションが持つユーザ名情報が不一致
    if($accountData['USERNAME'] != $loginUserName){       
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    unset($objQuery);


    ///////////////////////////////////////////////////
    // 他メニュー連携
    ///////////////////////////////////////////////////
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);
    $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0' AND LINK_ID = :LINK_ID");
    $sqlBind = array('LINK_ID' => $link_id);
    $result = $otherMenuLinkTable->selectTable($sql, $sqlBind);
    if(!is_array($result)){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }
    $result_other_menu_link = $result;

    //必要なデータを取得
    if(!empty($result_other_menu_link)){
        $menu_id = $result_other_menu_link[0]['MENU_ID'];
        $table_name = $result_other_menu_link[0]['TABLE_NAME'];
        $pri_name = $result_other_menu_link[0]['PRI_NAME'];
        $column_name = $result_other_menu_link[0]['COLUMN_NAME'];
    }else{
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    ///////////////////////////////////////////////////
    // 選択項目のテーブルからレコードを取得
    ///////////////////////////////////////////////////
    $sql =        " SELECT  $pri_name , $column_name ,ACCESS_AUTH \n";
    $sql = $sql . " FROM $table_name \n";
    $sql = $sql . " WHERE  DISUSE_FLAG = '0' \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    $result = $objQuery->sqlExecute();
    if(!$result){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }

    $result_default_value_list = array();
    while ($row = $objQuery->resultFetch()){
        $result_default_value_list[] = $row;
    }

    unset($objQuery);

    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
    $obj = new RoleBasedAccessControl($objDBCA);
    $ret = $obj->getAccountInfo($user_id);
    if($ret === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }

    // 権限があるデータのみに絞る
    $ret = $obj->chkRecodeArrayAccessPermission($result_default_value_list);
    if($ret === false) {
        throw new Exception($objMTS->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }

    //対象メニューのloadTable解析を利用して、対象カラムのClassTypeを特定する
    $tmpAryRetBody = getInfoOfLoadTable(sprintf("%010d",$menu_id));
    if( $tmpAryRetBody[1] !== null ){
        throw new Exception($objMTS->getSomeMessage("ITACREPAR-ERR-6001"));
    }
    $tableIudColumns = $tmpAryRetBody[0]['TABLE_IUD_COLUMNS'];

    //loadTable解析内容から、カラム名が一致する値のクラスを取得する
    $classType = "";
    foreach($tableIudColumns as $target){
        if($target[0] == $column_name){
            $classType = $target[5]; //classTypeをセット
            break;
        }
    }

    //「メニュー作成・定義」用に配列を形成
    $select_default_value_list = array();
    foreach($result_default_value_list as $data){
        $target = array();
        $target['id'] = $data[$pri_name];
        if($classType == "DateTimeColumn"){
            $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $data[$column_name]);
            $target['value'] =  $date->format('Y/m/d H:i:s'); //参照元がDateTimeColumnだった場合に形成
        }elseif($classType == "DateColumn"){
            $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $data[$column_name]);
            $target['value'] = $date->format('Y/m/d'); //参照元がDateColumnだった場合に形成
        }else{
            $target['value'] = $data[$column_name];
        }
        array_push($select_default_value_list, $target);
    }

    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($select_default_value_list);

}catch (Exception $e){
    if(function_exists("web_log")) {
        web_log($e->getMessage());
    } else {
        error_log($e->getMessage());
    }
    $select_default_value_list = 'failed';
    header("Content-Type: text/html; charset=utf-8");
    if ( $sessiontimeoutFlag == TRUE ){
        $ErrorMsg = $objMTS->getSomeMessage("ITAWDCH-MNU-1300006");
        $select_default_value_list=["redirectOrderForHADACClient",["0","/common/common_auth.php?login",'status'],$ErrorMsg];
    }
    echo json_encode($select_default_value_list);
}

?>

