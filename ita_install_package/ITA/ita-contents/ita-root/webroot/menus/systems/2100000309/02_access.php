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

    global $g;
    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    $g['requestByHA'] = 'forHADAC'; //[H]tml-[A]AX.[D]b_[A]ccess_[C]ore

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // access系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_access_01.php");
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    ky_include_path_add(getApplicationRootDirPath()."/confs/webconfs/path_HTML_AJAX.txt", 1);
    require_once 'HTML/AJAX/Server.php';
    
    
    class Db_Access_Core {
        //////////////////////////////////
        //  ここから標準機能の切り取り  //
        //////////////////////////////////
        

        //////////////////////////////////////////////////////////
        //  (シンフォニーインスタンス作業確認)フローの読み込み  //
        //////////////////////////////////////////////////////////
        
        // ポリシー1:SQL関数（makeSQLForUtnTableUpdate）は、SELECTのみのプロセスでは使わない

        function printSymphonyInstance($intShyphonyInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_symphony_ins_control.php");
            
            $arrayResult = printOneOfSymphonyInstances($intShyphonyInstanceId);
            
            // 結果判定
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
            //symphony_ins_noごとに作業パターンの流れを収集する----
        }

        function bookCancelSymphonyInstance($intShyphonyInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_symphony_ins_control.php");
            
            $arrayResult = bookCancelOneOfSymphonyInstances($intShyphonyInstanceId);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        function scramSymphonyInstance($intShyphonyInstanceId){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_symphony_ins_control.php");
            
            $arrayResult = scramOneOfSymphonyInstances($intShyphonyInstanceId);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        function holdReleaseMovementInstance($intShyphonyInstanceId,$intSeqNo){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_symphony_ins_control.php");
            
            $arrayResult = holdReleaseOneOfMovementInstances($intShyphonyInstanceId, $intSeqNo);
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

    }
    
    class Db_Access extends Db_Access_Core {

    
    }
    
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
