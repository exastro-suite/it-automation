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
    //////////////////////////////////////////////////////////////////////
    //
    //  【処理概要】
    //    ・WebDBCore機能を用いたWebページの、動的再描画などを行う。
    //
    //////////////////////////////////////////////////////////////////////

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

    global $g;
    // ルートディレクトリを取得
    $tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = htmlspecialchars($_GET['no'], ENT_QUOTES, "UTF-8");
    }

    $g['requestByREST'] = array('resultStatusCode'=>200,
                                'preResponsContents'=>array('successInfo'=>array('status'=>'SUCCEED',
                                                                                 'resultdata'=>'none'
                                                                                 ),
                                                            'errorInfo'=>array('Error'=>'Unexpcted Error',
                                                                               'Exception'=>'Generic error',
                                                                               'StackTrace'=>'none'
                                                                               )
                                                            )
                                );

    //-- サイト個別PHP要素、ここから--
    $expandRestCommandPerMenu = array('DOWNLOAD');
    //-- サイト個別PHP要素、ここまで--  
    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        //----ここから01_系から07_系全て共通

        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_rest_request_01.php");

        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_rest_request_02.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    //----デフォルトのロードテーブル関数をコレクション
    $systemFile = "{$g['root_dir_path']}/webconfs/systems/{$g['page_dir']}_loadTable.php";
    $userFile = "{$g['root_dir_path']}/webconfs/users/{$g['page_dir']}_loadTable.php";
    if(file_exists($systemFile)){
        require_once($systemFile);
    }
    else if(file_exists($userFile)){
        require_once($userFile);
    }

    $objTable = loadTable();
    
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/99_functions_for_rest_request.php");
    require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/07_front_ref_lib.php" );

    $intResultStatusCode = $aryForResultData[0]['ResultStatusCode'];

    $tmpAryForResultData    = $aryForResultData[0]['ResultData'];
    $objJSONOfResultData = @json_encode($tmpAryForResultData);
    unset($tmpAryForResultData);

    if( $aryForResultData[1] !== null ){
        if( $aryForResultData[1] < 500 ){
            //----本作業自体までに、想定されたエラーが発生した

            // RESULT:WARINIG [RESPONSE：｛｝]
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-40001",array($intResultStatusCode)));

            //本作業自体までに、想定されたエラーが発生した----
        }
        else{
            //----本作業自体まで、システムエラー系(500以上)が発生した

            // RESULT:ERROR [RESPONSE：｛｝]
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-40002",array($intResultStatusCode)));

            //本作業自体まで、システムエラー系(500以上)が発生した----
        }
    }
    else{
        //----本作業自体までは、問題なく通過した

        // RESULT:SUCCESS [RESPONSE：｛｝]
        web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-40003",array($intResultStatusCode)));

        //本作業自体までは、問題なく通過した----
    }

    header('Content-Type: application/json; charset=utf-8', true, $intResultStatusCode);
    header('REST-API-Version: '.$strCalledRestVer);

    //念のため[$intResultStatusCode]で上書き----
    //----JSON形式で返す
    exit($objJSONOfResultData);
    //JSON形式で返す----
?>
