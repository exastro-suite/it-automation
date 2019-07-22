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
//////////////////////////////////////////////////////////////////////
//
//
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////
require_once ($root_dir_path . "/libs/webcommonlibs/web_auth_config.php");
require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_menu_info.php");
require_once ($root_dir_path . "/libs/webcommonlibs/web_functions_for_user_auth.php");


function printReleasedMainMenuLinks($call_mode, $mode){
    global $root_dir_path,$g;    
    $varRet = null;
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    $username = $g['login_name'];
    $body = array("DISUSE_FLAG__0" => "0");
    $strFxName = __FUNCTION__;
    
    $aryInfoRepreFile = array();
    $aryHiddenUrl = array();
    $strOrderByType = "separate:productToOther";
    $strSpecialSeqTarget = "";
    
    $tmpAryRetBody = getInfoOfRepresentativeFiles($objDBCA);

    if( $tmpAryRetBody[1] !== null ){
        // ERROR:UNEXPECTED, DETAIL:PRESENTIVE FILES INFO REFER FAILER.
        web_log($objMTS->getSomeMessage("ITABASEH-ERR-102090"));
        webRequestForceQuitFromEveryWhere(500,11610101);//105-＞116
        exit();
    }
    $aryInfoRepreFile = $tmpAryRetBody[0]['InfoOfRepresentativeFilenames'];


    $sql1 = "select SORT_ID_LIST from A_SORT_MENULIST where USER_NAME='". $username ."'";
    $ret_array = singleSQLExecuteAgent($sql1, $body, $strFxName);

    if (!$ret_array[0]){
        // アクセスログ出力(想定外エラー)
        web_log('ERROR:UNEXPECTED_ERROR([FILE]'.__FILE__.'[LINE]'.__LINE__.'[ETC-Code]00000200');

        unset($objQuery);

        // 例外処理へ
        throw new Exception();
    }
    $objQuery =& $ret_array[1];
    $set_sort_list = "";
    while ( $list_row = $objQuery->resultFetch() ){
        $set_sort_list = implode($list_row);
    }
    unset($objQuery);
    $tmp_sort_kv = explode( ",", $set_sort_list);

    $sql2 = "select MENU_ID_LIST from A_SORT_MENULIST where USER_NAME='". $username ."'";
    $ret_array2 = singleSQLExecuteAgent($sql2, $body, $strFxName);

    if (!$ret_array2[0]){
        // アクセスログ出力(想定外エラー)
        web_log('ERROR:UNEXPECTED_ERROR([FILE]'.__FILE__.'[LINE]'.__LINE__.'[ETC-Code]00000200');

        unset($objQuery);

        // 例外処理へ
        throw new Exception();
    }
    $objQuery =& $ret_array2[1];
    $set_menu_list = "";
    while ( $list_row = $objQuery->resultFetch() ){
        $set_menu_list = implode($list_row);
    }
    unset($objQuery);
    $tmp_sort_kv2 = explode( ",", $set_menu_list);

    $sort_menu_list = array_combine($tmp_sort_kv2, $tmp_sort_kv);
    $cnt = count($tmp_sort_kv) + 1;
    $add_index = $cnt * 10;

    // 表示順序、項番の順に並び替え
    foreach ($aryInfoRepreFile as $key => $value){

        if ($call_mode == 0){
            
            // ユーザーが作成したメニューグループは10桁の文字列に整形
            $menu_group_id_length = strlen($value['MENU_GROUP_ID']);
            if( $menu_group_id_length != 10 ) {
                $menu_group_id = $value['MENU_GROUP_ID'];
                $blank_length = 10 - $menu_group_id_length;
                $i = 1;
                $blank = '0';
                while($i < $blank_length){
                    $blank = $blank . '0';
                    $i++;
                }
                $menu_group_id = $blank . $menu_group_id;
            }
            else{
                $menu_group_id = $value['MENU_GROUP_ID'];
            }

            // DBに並び順情報が登録されていれば、表示順序の並び替えを実施
            if ($tmp_sort_kv[0] != ""){
                if (isset( $sort_menu_list[$menu_group_id] )){
                    $value['DISP_SEQ'] = $sort_menu_list[$menu_group_id];
                }
                else if ($value['DISP_SEQ'] != null){
                    $value['DISP_SEQ'] = $add_index;
                    $add_index = $add_index + 10;
                }
            }
        }
        $keyDispSeq[$key] = $value['DISP_SEQ'];
        $keyId[$key] = $value['MENU_GROUP_ID'];
    }
    array_multisort($keyDispSeq , SORT_ASC , $keyId , SORT_ASC , $aryInfoRepreFile);

    $aryPrintTargetAnchorTagPerContentId = array();
    $aryAnchorTagPerContentId = array();
    $aryBaseContentIdPerUrl = array();
    $strTitleString = "";

    // ID採番用
    $menu_list_id = array();

    foreach($aryInfoRepreFile as $strUrl=>$rowDataOfRepreFile){
        // メニューグループの表示順序が空のデータは表示しない
        if("" == $rowDataOfRepreFile['DISP_SEQ']){
            continue;
        }
        $strMenuGroupId = $rowDataOfRepreFile['MENU_GROUP_ID'];
        $strMenuGroupName = $rowDataOfRepreFile['MENU_GROUP_NAME'];
        $strDispSeq = $rowDataOfRepreFile['DISP_SEQ'];

        // メニューグループが表示対象か判定する
        $strTmpBody = printLinkHtmlTagWithReleaseFile(sprintf("%010d", $strMenuGroupId), $strMenuGroupName, $strDispSeq, $mode);
        array_push($menu_list_id, $strMenuGroupId);
        if( 0 < strlen($strTmpBody) ){
            $aryPrintTargetAnchorTagPerContentId[] = $strTmpBody;
        }
    }

    // ----最終的に要素に入っているものを出力
    $strRetBody = implode($aryPrintTargetAnchorTagPerContentId);
    print $strRetBody;
    $varRet = true;
    if($call_mode == 1){
        return $menu_list_id;
    }
    else{
        return $varRet;
    }
}


//リンクタイトルを表示するためのユーザー関数
function printLinkHtmlTagWithReleaseFile($strMenuGroupId, $strMenuGroupName, $strDispSeq, $mode){
    global $root_dir_path,$scheme_n_authority,$ACRCM_id,$g;
    //ログインユーザーの権限を確認する

    $strRetPrePrintBody = ""; 

    $aryValues = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $strErrorBuf = "";

    $objDBCA = $g['objDBCA'];
    $objMTS = $g['objMTS'];
    $username = $g['login_name'];

    $privilege = array();
    $boolExecute = true;
    $num_rows = 0;

    // GETからメニューグループIDを取得する
    if(array_key_exists('grp', $_GET)){
        $strGetMenuGroupId = sprintf("%010d", $_GET['grp']);
    }
    else{
        $strGetMenuGroupId = "";
    }

    // ログイン状態を確認する
    if(0 === strlen($username)){
        $login_status_flag = 0;
    }
    else{
        $login_status_flag = 1;
    }

    // メニューグループの中で表示可能なメニューを取得する
    $tmpAryRetBody = getFilenameAndMenuNameByMenuGroupID(intval($strMenuGroupId), $login_status_flag, $username, $objDBCA);
    if($tmpAryRetBody[1] !== null){

        $tmpErrMsgBody = $tmpAryRetBody[3];

        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITABASEH-ERR-103020",$tmpErrMsgBody));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,11610103);//106-＞
        exit();
    }

    if(0 < count($tmpAryRetBody[0]['MenuNames'])){

        $strMenuGroupName = htmlspecialchars($strMenuGroupName);
        $strMenuName = htmlspecialchars(trim($objMTS->getSomeMessage("ITAWDCH-MNU-1100001")));
        $menu_icon = "/common/imgs/default.png";
        $dir = $root_dir_path . "/webroot/uploadfiles/2100000204/MENU_GROUP_ICON/" . $strMenuGroupId . "/";
        $dir_ = "/uploadfiles/2100000204/MENU_GROUP_ICON/" . $strMenuGroupId . "/";
        if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
            while(false !== ($file = readdir($handle))) {
                if( filetype( $path = $dir . $file ) == "file" ) {
                    $menu_icon = $dir_ . $file;
                }
            }
        }

        //メニュに表示したい文字列が現在開いているメニュと同じ場合
        if( $strMenuGroupId === $strGetMenuGroupId ){
            //メニュのタイトルのみを表示する
            // 選択中のメニューは画像の表示を変更する

            if($mode == "classic"){
                $strRetPrePrintBody = ''.$strMenuGroupName.'<br><br>';
            }
            else{
                //javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
                $timeStamp_drag_png=filemtime("$root_dir_path/webroot/common/imgs/drag.png");
                $timeStamp_default_png=filemtime("$root_dir_path/webroot$menu_icon");

                // 枠表示
                $strRetPrePrintBody = '
                <li class="mm_list" value="'. $strMenuGroupId .'">
                    <div class="mm_text">'.'<p>'. $strMenuGroupName .'</p>'.'</div>
                    <i class="ui-corner-all"><img class="drag_img" src="/common/imgs/drag.png?'.$timeStamp_drag_png.'"
                      onmouseover="this.style.opacity = 1" onmouseout="this.style.opacity = 0.2" ></i>
                    <div class="menu_border">
                        <img class="mm_icon selected_icon" src="'. $menu_icon .'"?"'.$timeStamp_default_png.'" alt="'. $strMenuGroupName .'">
                    </div>
                </li>';
            }
        }
        //メニュに表示したい文字列が現在開いているメニュと違う場合
        else{
            $strUrl = "/default/mainmenu/01_browse.php?grp=" . $strMenuGroupId;
            //リンク込のメニュタイトルを表示する(改行込)            

            if($mode == "classic"){
                $strRetPrePrintBody = '<a href="' . $scheme_n_authority . ''.$strUrl.'">'.$strMenuGroupName.'</a><br><br>';
            }
            else{
                //javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
                $timeStamp_drag_png=filemtime("$root_dir_path/webroot/common/imgs/drag.png");
                $timeStamp_default_png=filemtime("$root_dir_path/webroot$menu_icon");

                // リンク込のメニュー画像を表示する
                $strRetPrePrintBody = '
                <li class="mm_list" value="'. $strMenuGroupId .'">
                    <div class="mm_text">'.'<p>'. $strMenuGroupName .'</p>'.'</div>
                    <i class="ui-corner-all"><img class="drag_img" src="/common/imgs/drag.png?'.$timeStamp_drag_png.'"
                     onmouseover="this.style.opacity = 1" onmouseout="this.style.opacity = 0.2" ></i>
                    <a href="' . $scheme_n_authority . $strUrl.'" title="'. $strMenuGroupName .'">
                        <img class="mm_icon" src="'. $menu_icon .'?'.$timeStamp_default_png.'" alt="'. $strMenuGroupName .'">
                    </a>
                </li>';
            }
        }
    }

    return $strRetPrePrintBody;
}
?>
