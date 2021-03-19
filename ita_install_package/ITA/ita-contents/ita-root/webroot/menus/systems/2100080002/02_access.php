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
        function registerOrganization($registerData){
            $ret = $this->setOrganization($registerData, 'register');
            return($ret);
        }

        function updateOrganization($registerData){
            $ret = $this->setOrganization($registerData, 'update');
            return($ret);
        }


        function setOrganization($registerData, $type){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformOrganization.php");
            $terraformOrganization = new terraformOrganization();

            //$registerData中身をデコード
            $registerData['organizationID'] = urldecode($registerData['organizationID']);

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($registerData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211060", $retOrganizationData[2]));
                $ret = false;
                return($ret);
            }
            
            //データをセット
            $aryUtnSqlBind = $retOrganizationData[1];
            $registerData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];
            $registerData['emailAddress'] = $retOrganizationData[1]['EMAIL_ADDRESS'];

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211070", $retInterfaceInfo[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $registerData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $registerData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $registerData['proxySetting'] = array();
            $registerData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $registerData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];


            if($type == 'register'){
                //----------------------------------------------
                // Organizationを登録
                //----------------------------------------------
                //登録APIを実行
                $retCreateOrganization = $terraformOrganization->createOrganization($registerData);

                //API結果判定
                if($retCreateOrganization[0] == true){
                    $ret = true;
                }else{
                    //エラーログ出力
                    web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211010",'00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                    $ret = false;
                    return ($ret);
                }
            }

            if($type == 'update'){
                //----------------------------------------------
                // Organizationを更新
                //----------------------------------------------
                //更新APIを実行
                $retUpdateOrganization = $terraformOrganization->updateOrganization($registerData);

                //API結果判定
                if($retUpdateOrganization[0] == true){
                    $ret = true;

                }else{
                    //エラーログ出力
                    web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211020",'00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                    $ret = false;
                    return($ret);
                }
            }

            return($ret);
        }

        function checkOrganization($checkData){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformOrganization.php");
            $terraformOrganization = new terraformOrganization();

            //$checkData中身をデコード
            $checkData['resultAreaID'] = urldecode($checkData['resultAreaID']);
            $checkData['organizationID'] = urldecode($checkData['organizationID']);

            //返却値
            $retArray = array();
            $retArray[0] = false; //判定
            $retArray[1] = $checkData['resultAreaID']; //状態結果挿入先のhtmlのid
            $retArray[2] = ""; //状態結果メッセージ
            $retArray[3] = null; //結果タイプ[1:未登録, 2:登録済み, 3:更新あり]

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($checkData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211060", $retOrganizationData[2])); //Organization情報の取得に失敗しました。
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211060", $retOrganizationData[2]); //Organization情報の取得に失敗しました。
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
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211070", $retInterfaceInfo[2])); //インタフェース情報の取得に失敗しました。
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211070", $retInterfaceInfo[2]); //インタフェース情報の取得に失敗しました。
                return($retArray);
            }

            //データをセット
            $checkData['hostName'] = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $checkData['token'] = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $checkData['proxySetting'] = array();
            $checkData['proxySetting']['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $checkData['proxySetting']['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

            //----------------------------------------------
            // Organizationの登録状態をチェック
            //----------------------------------------------
            $retCheckOrganization = $terraformOrganization->checkOrganization($checkData);

            //API結果判定
            if($retCheckOrganization[0] == true){
                $retArray[0] = true;
                //Organization存在判定
                if($retCheckOrganization[2] == true){
                    //中身をチェックし、差異があるかどうかを確認
                    $diffFlag = false;
                    //メールアドレスをチェック
                    if($retCheckOrganization[3]['attributes']['email'] != $checkData['emailAddress']){
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
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211030",'00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                $retArray[0] = false;
                $retArray[2] = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211050"); //Terraformとの接続に失敗しました。インターフェース情報を確認して下さい。
                return($retArray);
            }

            return($retArray);
        }


        function deleteOrganization($deleteData){
            //グローバル変数宣言
            global $g;

            //ローカル変数宣言
            $ret = false;
            $strFxName = __FUNCTION__;

            //本体ロジックをコール
            require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/{$g['page_dir']}/81_terraformOrganization.php");
            $terraformOrganization = new terraformOrganization();

            //$deleteData中身をデコード
            $deleteData['organizationID'] = urldecode($deleteData['organizationID']);

            //----------------------------------------------
            // Organization情報を取得
            //----------------------------------------------
            $retOrganizationData = getOrganizationData($deleteData['organizationID']);
            if($retOrganizationData[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211060", $retOrganizationData[2]));
                $ret = false;
                return($ret);
            }

            //データをセット
            $aryUtnSqlBind = $retOrganizationData[1];
            $deleteData['organizationName'] = $retOrganizationData[1]['ORGANIZATION_NAME'];

            //----------------------------------------------
            // インタフェース情報を取得
            //----------------------------------------------
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211070", $retInterfaceInfo[2]));
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
            // Organizationの登録状態をチェック
            //----------------------------------------------
            $retCheckOrganization = $terraformOrganization->checkOrganization($deleteData);

            //API結果判定
            if($retCheckOrganization[0] == true){
                //Organization存在判定
                if($retCheckOrganization[2] == true){
                    //削除APIを実行
                    $retDeleteOrganization = $terraformOrganization->deleteOrganization($deleteData);
                    if($retDeleteOrganization[0] == true){
                        $ret = true;

                    }else{
                        //エラーログ出力
                        web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211040",'00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                        $ret = false;
                        return($ret);
                    }

                }else{
                    //TFEにOrganizationが登録されていない
                    $ret = false;
                    return($ret);
                }

            }else{
                //エラーログ出力
                web_log($g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-211030",'00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')'));
                $ret = false;
                return ($ret);

            }

            return($ret);

        }


        //-- サイト個別PHP要素、ここまで--
    }

    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();

?>
