<?php
//   Copyright 2022 NEC Corporation
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
        function destroyWorkspaceInsRegister($destroyData)
        {
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;
            $data = array();

            //本体ロジックをコール
            require_once($g['root_dir_path'] . "/libs/commonlibs/common_terraform_cli_function.php");

            //$destroyData中身をデコード
            $data["WORKSPACE_ID"] = urldecode($destroyData['workspaceID']);
            $data["WORKSPACE_NAME"] = base64_decode(urldecode($destroyData['workspaceName']));
            $data["EXE_USER_ID"] = $g["login_id"];

            // 作業管理に「リソース削除」で作業を登録する
            $retdestroyWorkspace = destroyInsRegister($data);
            if ($retdestroyWorkspace[0] == true) {
                $ret = array(
                    true,
                    $retdestroyWorkspace[1] // execution_no
                );
            } else {
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-ERR-205010", '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                web_log($retdestroyWorkspace[1]);
                $ret = false;
                return ($ret);
            }

            return ($ret);
        }

        //-- サイト個別PHP要素、ここまで--
    }

    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();

?>
