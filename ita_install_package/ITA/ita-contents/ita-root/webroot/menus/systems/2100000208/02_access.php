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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--
        ////////////////////////////////////////
        //  dispRoleListHeaderファンクション  //
        ////////////////////////////////////////
        function dispRoleListHeader( $p_user_id ){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $error_flag = 0;
            $output_str = '';

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_dispRoleListHeader.php");

            // 結果判定
            if( $error_flag == 0 ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                
                // 結果を返却
                return $output_str;
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

                // 異常を返却
                return "unexpected_error";
            }
        }

        //////////////////////////////////////
        //  editRoleListファンクション  //
        //////////////////////////////////////
        function editRoleList( $mode,
                               $p_user_id,
                               $p_role_array,
                               $p_max_last_update_timestamp ){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $output_str      = "";
            $error_flag      = 0;

            // 本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/82_editRoleList.php");

            // 結果判定
            if( $error_flag == 0 ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4003",array(__FUNCTION__,$mode)));

                // 結果を返却
                return $output_str;
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4003",array(__FUNCTION__,$mode)));

                // 異常を返却
                return "unexpected_error";
            }
        }
        //////////////////////////////////////
        //  lockRemoveファンクション  //
        //////////////////////////////////////
        function lockRemove( $p_user_id ){
            // グローバル変数宣言
            global $g, $pwl_expiry, $pwl_threshold, $pwl_countmax;
            
            // ローカル変数宣言
            $intErrorFlag      = 0;
            $strResultCode     = "000";
            $strDetailCode     = "000";
            $retStrOutput      = "";
            
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/83_lockRemove.php");
            
            // 結果判定
            if( $intErrorFlag == 0 ){
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else{
                // アクセスログ出力
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

                // 異常を返却
                return "unexpected_error";
            }
            $arrayResult = array($strResultCode, $strDetailCode, $retStrOutput);
            return makeAjaxProxyResultStream($arrayResult);
            
        }
        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $server->registerClass(new Db_Access());
    $server->handleRequest();
?>
