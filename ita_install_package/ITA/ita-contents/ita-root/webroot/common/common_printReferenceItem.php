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
    //$web_php_function                = '/libs/webcommonlibs/web_php_functions.php';

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require_once ($root_dir_path . $php_req_gate_php );
    require_once ( $root_dir_path . $create_param_menu);

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );


    ///////////////////////////////////////////////////
    // 他メニュー連携
    ///////////////////////////////////////////////////
    $link_id = htmlspecialchars($_GET['link_id'], ENT_QUOTES, "UTF-8");
    $otherMenuLinkTable = new OtherMenuLinkTable($objDBCA, $db_model_ch);
    $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0' AND LINK_ID = " . $link_id);
    $result = $otherMenuLinkTable->selectTable($sql);
    if(!is_array($result)){
        throw new Exception("Failed to get Reference Item");
    }
    $result_other_menu_link = $result;

    //メニューIDを取得
    if(!empty($result_other_menu_link)){
        $menu_id = $result_other_menu_link[0]['MENU_ID'];
    }else{
        throw new Exception("Failed to get Reference Item");
    }

    ///////////////////////////////////////////////////
    // 参照項目情報
    ///////////////////////////////////////////////////
    $referenceItemTable = new ReferenceItemTable($objDBCA, $db_model_ch);
    $sql = $referenceItemTable->createSselect("WHERE DISUSE_FLAG = '0' AND MENU_ID = ". $menu_id ." ORDER BY  DISP_SEQ");
    $result = $referenceItemTable->selectTable($sql);
    if(!is_array($result)){
        throw new Exception("Failed to get Reference Item");
    }

    $result_reference_item = $result;
    $select_reference_item = array();
    foreach($result_reference_item as $key => $data){
        if($data['ORIGINAL_MENU_FLAG'] == 1){
            //既存メニューの場合は、他メニュー連携IDが一致ものだけを参照項目の対象とする
            if($data['LINK_ID'] == $link_id){
                array_push($select_reference_item, $data);
            }
        }else{
            //作成メニューの場合は、カラム名が一致するものは参照項目の対象から除外する
            if($result_other_menu_link[0]['COLUMN_NAME'] != $data['COLUMN_NAME']){
                array_push($select_reference_item, $data);
            }
        }
    }

    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
    $user_id = htmlspecialchars($_GET['user_id'], ENT_QUOTES, "UTF-8");
    $obj = new RoleBasedAccessControl($objDBCA);
    $ret = $obj->getAccountInfo($user_id);
    if($ret === false) {
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        $arrayResult = array("999","", "");
        return makeAjaxProxyResultStream($arrayResult);
    }

    // 権限があるデータのみに絞る
    $ret = $obj->chkRecodeArrayAccessPermission($select_reference_item);
    if($ret === false) {
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        $arrayResult = array("999","", "");
        return makeAjaxProxyResultStream($arrayResult);
    }

    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($select_reference_item);

}catch (Exception $e){
    if(function_exists("web_log")) {
        web_log($e->getMessage());
    } else {
        error_log($e->getMessage());
    }
    $select_reference_item = array();
    header("Content-Type: text/html; charset=utf-8");
    echo json_encode($select_reference_item);
}

?>

