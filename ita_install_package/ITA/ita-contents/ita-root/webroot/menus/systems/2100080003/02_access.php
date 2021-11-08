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
        function registerWorkspace($registerData){
            $ret = $this->setWorkspace($registerData, 'register');
            return($ret);
        }

        function updateWorkspace($registerData){
            $ret = $this->setWorkspace($registerData, 'update');
            return($ret);
        }

        function setWorkspace($registerData, $type){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformWorkspace.php");
            $terraformWorkspace = new terraformWorkspace();

            //$registerData中身をデコード
            $registerData['workspaceID'] = urldecode($registerData['workspaceID']);

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $registerData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $registerData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $registerData['proxySetting'] = array();
            $registerData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $registerData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

            //----------------------------------------------
            // Workspace情報を取得
            //----------------------------------------------
            $retWorkspaceData = getWorkspaceData($registerData['workspaceID']);
            if($retWorkspaceData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211180", $retWorkspaceData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $aryUtnSqlBind = $retWorkspaceData[1];
            $registerData['organizationID'] = $retWorkspaceData[1]['ORGANIZATION_ID'];
            $registerData['workspaceName'] = $retWorkspaceData[1]['WORKSPACE_NAME'];
            $registerData['terraformVersion'] = $retWorkspaceData[1]['TERRAFORM_VERSION'];

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($registerData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211160", $retOrganizationData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $registerData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];

            if($type == 'register'){
                //----------------------------------------------
                // Workspaceを登録
                //----------------------------------------------
                //登録APIを実行
                $retCreateWorkspace = $terraformWorkspace->createWorkspace($registerData);

                //API結果判定
                if($retCreateWorkspace[0] == true){
                    $ret = true;
                }else{
                    //エラーログ出力
                    web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211110",'00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                    $ret = false;
                    return($ret);
                }

            }

            if($type == 'update'){
                //----------------------------------------------
                // Workspaceを更新
                //----------------------------------------------
                //更新APIを実行
                $retUpdateWorkspace = $terraformWorkspace->updateWorkspace($registerData);

                //API結果判定
                if($retUpdateWorkspace[0] == true){
                    $ret = true;

                }else{
                    //エラーログ出力
                    web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211120",'00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                    $ret = false;
                    return($ret);
                }
            }

            return($ret);

        }

       function checkWorkspace($checkData){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformWorkspace.php");
            $terraformWorkspace = new terraformWorkspace();

            //$checkData中身をデコード
            $checkData['resultAreaID'] = urldecode($checkData['resultAreaID']);
            $checkData['workspaceID'] = urldecode($checkData['workspaceID']);

            //返却値
            $retArray = array();
            $retArray[0] = false; //判定
            $retArray[1] = $checkData['resultAreaID']; //状態結果挿入先のhtmlのid
            $retArray[2] = ""; //状態結果メッセージ
            $retArray[3] = null; //結果タイプ[1:未登録, 2:登録済み, 3:更新あり]

            //----------------------------------------------
            // Workspace情報を取得
            //----------------------------------------------
            $retWorkspaceData = getWorkspaceData($checkData['workspaceID']);
            if($retWorkspaceData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211180", $retWorkspaceData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $aryUtnSqlBind = $retWorkspaceData[1];
            $checkData['organizationID'] = $retWorkspaceData[1]['ORGANIZATION_ID'];
            $checkData['workspaceName'] = $retWorkspaceData[1]['WORKSPACE_NAME'];
            $checkData['terraformVersion'] = $retWorkspaceData[1]['TERRAFORM_VERSION'];

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($checkData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211160", $retOrganizationData[2])); //Organization情報の取得に失敗しました。
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211160", $retOrganizationData[2]); //Organization情報の取得に失敗しました。
                return($retArray);
            }

            //データをセット
            $checkData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];
            $checkData['emailAddress'] = $retOrganizationData[1]['EMAIL_ADDRESS'];

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2])); //インタフェース情報の取得に失敗しました。
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2]); //インタフェース情報の取得に失敗しました。
                return($retArray);
            }

            //データをセット
            $checkData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $checkData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $checkData['proxySetting'] = array();
            $checkData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $checkData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

            //----------------------------------------------
            // Workspaceの登録状態をチェック
            //----------------------------------------------
            $retCheckWorkspace = $terraformWorkspace->checkWorkspace($checkData);

            //API結果判定
            if($retCheckWorkspace[0] == true){
                $retArray[0] = true;
                //Workspace存在判定
                if($retCheckWorkspace[2] == true){
                    //中身をチェックし、差異があるかどうかを確認
                    $diffFlag = false;

                    //Terraform Versionをチェック(ITA側の登録がNULLの場合はチェックをしない)
                    $TFE_TerraformVersion = $retCheckWorkspace[3]['attributes']['terraform-version'];
                    $ITA_TerraformVersion = $checkData['terraformVersion'];
                    if($ITA_TerraformVersion != null && $ITA_TerraformVersion != $TFE_TerraformVersion){
                        $diffFlag = true;
                    }

                    if($diffFlag == true){
                        //登録状態に差異があった場合
                        $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104510"); //更新あり
                        $retArray[3] = 3;
                    }else{
                        //登録状態に差異が無かった場合
                        $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104520"); //登録済み
                        $retArray[3] = 2;
                    }

                }else{
                    //登録が無かった場合
                    $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104530"); //登録なし
                    $retArray[3] = 1;
                }

            }else{
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211130",'00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211150"); //Terraformとの接続に失敗しました。インターフェース情報を確認して下さい。
                return($retArray);
            }

            return($retArray);

        }

        function deleteWorkspace($deleteData){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformWorkspace.php");
            $terraformWorkspace = new terraformWorkspace();

            //$deleteData中身をデコード
            $deleteData['workspaceID'] = urldecode($deleteData['workspaceID']);

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $deleteData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $deleteData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $deleteData['proxySetting'] = array();
            $deleteData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $deleteData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

            //----------------------------------------------
            // Workspace情報を取得
            //----------------------------------------------
            $retWorkspaceData = getWorkspaceData($deleteData['workspaceID']);
            if($retWorkspaceData[0] == false){
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211180", $retWorkspaceData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $aryUtnSqlBind = $retWorkspaceData[1];
            $deleteData['organizationID'] = $retWorkspaceData[1]['ORGANIZATION_ID'];
            $deleteData['workspaceName'] = $retWorkspaceData[1]['WORKSPACE_NAME'];

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($deleteData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211160", $retOrganizationData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $deleteData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];

            //----------------------------------------------
            // Workspaceの登録状態をチェック
            //----------------------------------------------
            $retCheckWorkspace = $terraformWorkspace->checkWorkspace($deleteData);
            //API結果判定
            if($retCheckWorkspace[0] == true){
                //Workspace存在判定
                if($retCheckWorkspace[2] == true){
                    //Workspace削除APIを実行
                    $retDeleteWorkspace = $terraformWorkspace->deleteWorkspace($deleteData);
                    if($retDeleteWorkspace[0] == true){
                        $ret = true;

                    }else{
                        //エラーログ出力
                        web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211140",'00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                        $ret = false;
                        return($ret);
                    }
                }else{
                    //TFEにWorkspaceが登録されていない
                    $ret = false;
                    return($ret);
                }
            }else{
                //エラーログ出力
               web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211130",'00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));

                $ret = false;
                return($ret);
            }

            return($ret);
        }

        function destroyWorkspaceInsRegister($destroyData)
        {
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;
            $data = array();

            //本体ロジックをコール
            require_once($g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once($g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformWorkspace.php");
            $terraformWorkspace = new terraformWorkspace();

            //$destroyData中身をデコード
            $destroyData['workspaceID']   = urldecode($destroyData['workspaceID']);
            $destroyData['workspaceName'] = urldecode($destroyData['workspaceName']);

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if ($retInterfaceInfo[0] == false) {
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211170", $retInterfaceInfo[2]));
                $ret = false;
                return ($ret);
            }

            //データをセット
            $destroyData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $destroyData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $destroyData['proxySetting'] = array();
            $destroyData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $destroyData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];


            //----------------------------------------------
            // Workspace情報を取得
            //----------------------------------------------
            $retWorkspaceData = getWorkspaceData($destroyData['workspaceID']);
            if ($retWorkspaceData[0] == false) {
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211180", $retWorkspaceData[2]));
                $ret = false;
                return ($ret);
            }

            //データをセット
            $aryUtnSqlBind = $retWorkspaceData[1];
            $destroyData['organizationID'] = $retWorkspaceData[1]['ORGANIZATION_ID'];
            $destroyData['workspaceName'] = $retWorkspaceData[1]['WORKSPACE_NAME'];

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($destroyData['organizationID']);
            if ($retOrganizationData[0] == false) {
                //エラーログを出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211160", $retOrganizationData[2]));
                $ret = false;
                return ($ret);
            }

            //データをセット
            $destroyData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];

            //----------------------------------------------
            // Workspaceの登録状態をチェック
            //----------------------------------------------
            $retCheckWorkspace = $terraformWorkspace->checkWorkspace($destroyData);
            //API結果判定
            if ($retCheckWorkspace[0] == true) {
                //Workspace存在判定
                if ($retCheckWorkspace[2] == true) {
                    //Workspace削除APIを実行
                    $data["WORKSPACE_ID"] = $destroyData['workspaceID'];
                    $data["EXE_USER_ID"] = $g["login_id"];
                    $retdestroyWorkspace = destroyInsRegister($data);
                    if ($retdestroyWorkspace[0] == true) {
                        $ret = array(
                            true,
                            $retdestroyWorkspace[1] // execution_no
                        );
                    } else {
                        //エラーログ出力
                        web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-142018", '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                        web_log($retdestroyWorkspace[1]);
                        $ret = false;
                        return ($ret);
                    }
                } else {
                    //TFEにWorkspaceが登録されていない
                    $ret = false;
                    return ($ret);
                }
            } else {
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211130", '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));

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
