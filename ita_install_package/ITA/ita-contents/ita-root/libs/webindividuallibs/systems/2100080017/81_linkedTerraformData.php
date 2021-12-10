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

class  terraformEnerpriseData {
    private $root_dir_path;

    function __construct() {
        // ルートディレクトリを取得
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path = $root_dir_temp[0] . "ita-root";

        require_once ( $this->root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
        require_once ( $this->root_dir_path . "/libs/commonlibs/common_terraform_function.php");
        require_once ( $this->root_dir_path . "/libs/commonlibs/common_terraform_restapi.php");

    }

    function getOrganizationData($hostname, $token, $proxySetting){
        //グローバル変数宣言
        global $g;

        $ret = array();
        $ret['result'] = false;
        $ret['htmlBody'] = "";

        //Organization一覧取得APIを実行
        $apiResponse = get_organizations_list($hostname, $token, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];
        if($statusCode != 200){
            $ret['result'] = false;
            //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
            return $ret;
        }
        $organizationListData = $apiResponse['ResponsContents']['data'];

        //Terraform側のOrganization登録が0件の場合
        if(empty($organizationListData)){
            $ret['result'] = false;
            //Organizationの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181030').'</p>';
            return $ret;
        }

        //Organization用のHtmlBodyを作成
        $organizationHtmlBody = $this->makeOrganizationTablePrint($organizationListData);
        if($organizationHtmlBody != ""){
            $ret['result'] = true;
            $ret['htmlBody'] = $organizationHtmlBody;
        }

        return $ret;
    }


    function makeOrganizationTablePrint($organizationListData){
        //グローバル変数宣言
        global $g;

        $HtmlBody = "";

        //ITAに登録されているOrganization一覧を取得
        $sql = "SELECT *
                FROM B_TERRAFORM_ORGANIZATIONS";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $itaOrganizationList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaOrganizationList, $row);
            }
            //項目名
            $th1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106110'); //Organization Name
            $th2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106120'); //Email address
            $th3 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106130'); //ITAの登録状態
            $th4 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106140'); //削除
            $td1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106310'); //削除

            $HtmlBody .=
<<<EOD
<div class="fakeContainer_Filter1Print" style="margin-top:20px; margin-bottom:20px">
    <table id="Mix1_1">
        <tbody>
            <tr class="defaultExplainRow">
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106500')}" onclick="tableSort(1, this, 'Mix1_1', 0 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th1}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106460')}" onclick="tableSort(1, this, 'Mix1_1', 1 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th2}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410')}" onclick="tableSort(1, this, 'Mix1_1', 2 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th3}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106490')}"><span class="generalBold">{$th4}</span></th>
            </tr>
EOD;
            $count = 1;
            foreach($organizationListData as $data){
                $cell_print = "cell_print_table_" . $count;
                $organizationName = $data['attributes']['name'];
                $organizationEmail = $data['attributes']['email'];
                $registerStatus = "";
                $registeredFlag = false;
                $disuseFlag = false;
                $disabledFlag = true;
                $disabled = "";

                //ITAに登録されているかどうかをチェック
                foreach($itaOrganizationList as $itaOrganization){
                    if($itaOrganization['ORGANIZATION_NAME'] == $organizationName){
                        $registeredFlag = true;
                        if($itaOrganization['DISUSE_FLAG'] == 1){
                            $disuseFlag = true;
                        }
                        break;
                    }
                }

                if($registeredFlag == true){
                    if($disuseFlag == true){
                        $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                    }else{
                        $disabledFlag = false;
                        $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                    }
                }else{
                    $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                }
                if ($disabledFlag) {
                    $disabled = "disabled=true";
                }

                $HtmlBody .=
<<<EOD
                        <tr valign="top">
                            <td id="{$cell_print}_1">{$organizationName}</td>
                            <td id="{$cell_print}_2">{$organizationEmail}</td>
                            <td id="{$cell_print}_3">{$registerStatus}</td>
                            <td id="{$cell_print}_4"><input class="deleteBtnInTbl" type="button" {$disabled} value='{$td1}' onclick="deleteOrganization(this, '{$organizationName}')"></td>
                        </tr>
EOD;
                $count++;
            }

            $HtmlBody .=
<<<EOD
        </tbody>
    </table>
</div>
EOD;
        }

        return $HtmlBody;
    }


    function getWorkspaceData($hostname, $token, $proxySetting){
        //グローバル変数宣言
        global $g;

        $ret = array();
        $ret['result'] = false;
        $ret['htmlBody'] = "";

        //Organization一覧取得APIを実行
        $apiResponse = get_organizations_list($hostname, $token, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];
        if($statusCode != 200){
            $ret['result'] = false;
            //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
            return $ret;
        }
        $organizationListData = $apiResponse['ResponsContents']['data'];

        //Terraform側のOrganization登録が0件の場合
        if(empty($organizationListData)){
            $ret['result'] = false;
            //Workspaceの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181040').'</p>';
            return $ret;
        }

        //OrganizationNameをリスト化
        $organizationList = array();
        foreach($organizationListData as $data){
            $organizationName = $data['attributes']['name'];
            array_push($organizationList, $organizationName);
        }

        //OrganizationNameに紐づくWorkspace一覧を取得
        $workspaceListData = array();
        $workspaceCount = 0;
        foreach($organizationList as $organizationName){
            $apiResponse = get_workspaces_list($hostname, $token, $organizationName, $proxySetting);
            $statusCode = $apiResponse['StatusCode'];
            if($statusCode != 200){
                $ret['result'] = false;
                //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
                $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
                return $ret;
            }
            if(!empty($apiResponse['ResponsContents']['data'])){
                //OrganizationNameをkeyにWorkspace一覧を格納
                $workspaceListData[$organizationName] = $apiResponse['ResponsContents']['data'];
                $workspaceCount++;
            }
        }

        //Terraform側のWorkspace登録が0件の場合
        if($workspaceCount == 0){
            $ret['result'] = false;
            //Workspaceの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181040').'</p>';
            return $ret;
        }

        //Workspace用のHtmlBodyを作成
        $workspaceHtmlBody = $this->makeWorkspaceTablePrint($workspaceListData);
        if($workspaceHtmlBody != ""){
            $ret['result'] = true;
            $ret['htmlBody'] = $workspaceHtmlBody;
        }

        return $ret;
    }


    function makeWorkspaceTablePrint($workspaceListData){
        //グローバル変数宣言
        global $g;

        $HtmlBody = "";

        //ITAに登録されているWorkspace一覧を取得
        $sql = "SELECT *
                FROM D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $itaWorkspaceList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaWorkspaceList, $row);
            }
            //項目名
            $th1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106150'); //Organization Name
            $th2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106160'); //Workspace Name
            $th3 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106170'); //Terraform Version
            $th4 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106180'); //ITAの登録状態
            $th5 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106370'); //destroy
            $th6 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106190'); //削除
            $td1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106370'); //destroy
            $td2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106310'); //削除

            $HtmlBody .=
<<<EOD
<div class="fakeContainer_Filter1Print" style="margin-top:20px; margin-bottom:20px">
    <table id="Mix2_1">
        <tbody>
            <tr class="defaultExplainRow">
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106500')}" onclick="tableSort(1, this, 'Mix2_1', 0 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th1}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106510')}" onclick="tableSort(1, this, 'Mix2_1', 1 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th2}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106520')}" onclick="tableSort(1, this, 'Mix2_1', 2 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th3}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410')}" onclick="tableSort(1, this, 'Mix2_1', 3 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th4}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106450')}"><span class="generalBold">{$th5}</span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106440')}"><span class="generalBold">{$th6}</span></th>
            </tr>

EOD;
            $count = 1;
            foreach($workspaceListData as $organizationName => $workspaceData){
                if(!empty($workspaceData)){
                    foreach($workspaceData as $data){
                        $cell_print = "cell_print_table_" . $count;
                        $workspaceID = "";
                        $workspaceName = $data['attributes']['name'];
                        $terraformVersion = $data['attributes']['terraform-version'];
                        $registerStatus = "";
                        $registeredFlag = false;
                        $disuseFlag = false;
                        $disabledFlag = true;

                        //ITAに登録されているかどうかをチェック
                        foreach($itaWorkspaceList as $itaWorkspace){
                            if($itaWorkspace['ORGANIZATION_NAME'] == $organizationName && $itaWorkspace['WORKSPACE_NAME'] == $workspaceName){
                                $registeredFlag = true;
                                $workspaceID = $itaWorkspace["WORKSPACE_ID"];
                                if($itaWorkspace['DISUSE_FLAG'] == 1){
                                    $disuseFlag = true;
                                }
                                break;
                            }
                        }

                        if($registeredFlag == true){
                            if($disuseFlag == true){
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                            }else{
                                $disabledFlag = false;
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                            }
                        }else{
                            $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                        }

                        $disabled = "";
                        if ($disabledFlag) {
                            $disabled = "disabled=true";
                        }

                        $HtmlBody .=
<<<EOD
                                <tr valign="top">
                                    <td id="{$cell_print}_1">{$organizationName}</td>
                                    <td id="{$cell_print}_2">{$workspaceName}</td>
                                    <td id="{$cell_print}_2">{$terraformVersion}</td>
                                    <td id="{$cell_print}_3">{$registerStatus}</td>
                                    <td id="{$cell_print}_4"><input class="destroyBtnInTbl" type="button" value='{$td1}' onclick="destroyWorkspaceInsRegister(this, '{$workspaceID}', '{$workspaceName}')" {$disabled}></td>
                                    <td id="{$cell_print}_5"><input class="deleteBtnInTbl" type="button" value='{$td2}' onclick="deleteWorkspace(this, '{$organizationName}', '{$workspaceName}')" {$disabled}></td>
                                </tr>
EOD;
                        $count++;
                    }
                }
            }

            $HtmlBody .=
<<<EOD
        </tbody>
    </table>
</div>
EOD;
        }

        return $HtmlBody;
    }



    function getPolicyData($hostname, $token, $proxySetting){
        //グローバル変数宣言
        global $g;

        $ret = array();
        $ret['result'] = false;
        $ret['htmlBody'] = "";

        //Organization一覧取得APIを実行
        $apiResponse = get_organizations_list($hostname, $token, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];
        if($statusCode != 200){
            $ret['result'] = false;
            //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
            return $ret;
        }
        $organizationListData = $apiResponse['ResponsContents']['data'];

        //Terraform側のOrganization登録が0件の場合
        if(empty($organizationListData)){
            $ret['result'] = false;
            //Policyの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181050').'</p>';
            return $ret;
        }

        //OrganizationNameをリスト化
        $organizationList = array();
        foreach($organizationListData as $data){
            $organizationName = $data['attributes']['name'];
            array_push($organizationList, $organizationName);
        }

        //OrganizationNameに紐づくPolicy一覧を取得
        $policyListData = array();
        $policyCount = 0;
        foreach($organizationList as $organizationName){
            $apiResponse = get_policy_list($hostname, $token, $organizationName, $proxySetting);
            $statusCode = $apiResponse['StatusCode'];
            if($statusCode != 200){
                $ret['result'] = false;
                //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
                $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
                return $ret;
            }
            if(!empty($apiResponse['ResponsContents']['data'])){
                //OrganizationNameをkeyにpolicy一覧を格納
                $policyListData[$organizationName] = $apiResponse['ResponsContents']['data'];
                $policyCount++;
            }
        }

        //Terraform側のPolicy登録が0件の場合
        if($policyCount == 0){
            $ret['result'] = false;
            //Policyの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181050').'</p>';
            return $ret;
        }

        //Policy用のHtmlBodyを作成
        $policyHtmlBody = $this->makePolicyTablePrint($policyListData);
        if($policyHtmlBody != ""){
            $ret['result'] = true;
            $ret['htmlBody'] = $policyHtmlBody;
        }

        return $ret;
    }


    function makePolicyTablePrint($policyListData){
        //グローバル変数宣言
        global $g;

        $HtmlBody = "";

        //ITAに登録されているPolicy一覧を取得
        $sql = "SELECT *
                FROM B_TERRAFORM_POLICY";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $itaPolicyList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaPolicyList, $row);
            }
            //項目名
            $th1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106200'); //Organization Name
            $th2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106210'); //Policy Name
            $th3 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106220'); //ITAの登録状態
            $th4 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106230'); //Policy Codeをダウンロード
            $th5 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106240'); //削除
            $td1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106330'); //ダウンロード
            $td2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106310'); //削除

            $HtmlBody .=
<<<EOD
<div class="fakeContainer_Filter1Print" style="margin-top:20px; margin-bottom:20px">
    <table id="Mix3_1">
        <tbody>
            <tr class="defaultExplainRow">
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106500')}" onclick="tableSort(1, this, 'Mix3_1', 0 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th1}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106530')}" onclick="tableSort(1, this, 'Mix3_1', 1 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th2}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410')}" onclick="tableSort(1, this, 'Mix3_1', 2 ,null,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl"><span class="generalBold">{$th3}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106540')}"><span class="generalBold">{$th4}</span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106550')}"><span class="generalBold">{$th5}</span></th>
            </tr>

EOD;
            $count = 1;
            foreach($policyListData as $organizationName => $policyData){
                if(!empty($policyData)){
                    foreach($policyData as $data){
                        $cell_print = "cell_print_table_" . $count;
                        $policyId = $data['id'];
                        $policyName = $data['attributes']['name'];
                        $registerStatus = "";
                        $registeredFlag = false;
                        $disuseFlag = false;
                        $downloadUrl = urlencode($data['links']['download']);
                        $url = $g['scheme_n_authority'] . "/default/menu/05_preupload.php?no=2100080017&purl=" . $downloadUrl . "&policyName=" . $policyName;
                        $disabledFlag = true;
                        $disabled = "";
                        $disabled_a = "";

                        //ITAに登録されているかどうかをチェック
                        foreach($itaPolicyList as $itaPolicy){
                            if($itaPolicy['POLICY_NAME'] == $policyName){
                                $registeredFlag = true;
                                if($itaPolicy['DISUSE_FLAG'] == 1){
                                    $disuseFlag = true;
                                }
                                break;
                            }
                        }

                        if($registeredFlag == true){
                            if($disuseFlag == true){
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                            }else{
                                $disabledFlag = false;
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                            }
                        }else{
                            $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                        }

                        if ($disabledFlag) {
                            $disabled  = "disabled=true";
                            $url       = "";
                            $disabled_a = "style='pointer-events: none; color: #868686; text-decoration:none;  '";
                        }

                        $HtmlBody .=
<<<EOD
                                <tr valign="top">
                                    <td id="{$cell_print}_1">{$organizationName}</td>
                                    <td id="{$cell_print}_2">{$policyName}</td>
                                    <td id="{$cell_print}_3">{$registerStatus}</td>
                                    <td id="{$cell_print}_4"><a href='{$url}' target="_blank" {$disabled_a}>{$td1}<a/></td>
                                    <td id="{$cell_print}_5"><input class="deleteBtnInTbl" type="button" value='{$td2}' onclick="deletePolicy(this, '{$policyId}', '{$policyName}')" {$disabled}></td>
                                </tr>
EOD;
                        $count++;
                    }
                }
            }

            $HtmlBody .=
<<<EOD
        </tbody>
    </table>
</div>
EOD;
        }

        return $HtmlBody;
    }

    function getPolicySetData($hostname, $token, $proxySetting){
        //グローバル変数宣言
        global $g;

        $ret = array();
        $ret['result'] = false;
        $ret['htmlBody'] = "";

        //Organization一覧取得APIを実行
        $apiResponse = get_organizations_list($hostname, $token, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];
        if($statusCode != 200){
            $ret['result'] = false;
            //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
            return $ret;
        }
        $organizationListData = $apiResponse['ResponsContents']['data'];

        //Terraform側のOrganization登録が0件の場合
        if(empty($organizationListData)){
            $ret['result'] = true;
            //PolicySetの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181060').'</p>';
            return $ret;
        }

        //OrganizationNameをリスト化
        $organizationList = array();
        foreach($organizationListData as $data){
            $organizationName = $data['attributes']['name'];
            array_push($organizationList, $organizationName);
        }

        //OrganizationNameに紐づくWorkspace一覧を取得
        $workspaceListData = array();
        $workspaceCount = 0;
        foreach($organizationList as $organizationName){
            $apiResponse = get_workspaces_list($hostname, $token, $organizationName, $proxySetting);
            $statusCode = $apiResponse['StatusCode'];
            if($statusCode != 200){
                $ret['result'] = false;
                //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
                $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
                return $ret;
            }
            if(!empty($apiResponse['ResponsContents']['data'])){
                //OrganizationNameをkeyにWorkspace一覧を格納
                $workspaceListData[$organizationName] = $apiResponse['ResponsContents']['data'];
                $workspaceCount++;
            }
        }

        //OrganizationNameに紐づくPolicy一覧を取得
        $policyListData = array();
        $policyCount = 0;
        foreach($organizationList as $organizationName){
            $apiResponse = get_policy_list($hostname, $token, $organizationName, $proxySetting);
            $statusCode = $apiResponse['StatusCode'];
            if($statusCode != 200){
                $ret['result'] = false;
                //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
                $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
                return $ret;
            }
            if(!empty($apiResponse['ResponsContents']['data'])){
                //OrganizationNameをkeyにpolicy一覧を格納
                $policyListData[$organizationName] = $apiResponse['ResponsContents']['data'];
                $policyCount++;
            }
        }


        //OrganizationNameに紐づくPolicySet一覧を取得
        $policySetListData = array();
        $policySetCount = 0;
        foreach($organizationList as $organizationName){
            $apiResponse = get_policy_sets_list($hostname, $token, $organizationName, $proxySetting);
            $statusCode = $apiResponse['StatusCode'];
            if($statusCode != 200){
                $ret['result'] = false;
            //Terraformからデータを取得できませんでした。インターフェース情報を確認して下さい。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181020').'</p>';
                return $ret;
            }
            if(!empty($apiResponse['ResponsContents']['data'])){
                //OrganizationNameをkeyにpolicySet一覧を格納
                $policySetListData[$organizationName] = $apiResponse['ResponsContents']['data'];
                $policySetCount++;
            }
        }

        //Terraform側のPolicySet登録が0件の場合
        if($policySetCount == 0){
            $ret['result'] = false;
            //PolicySetの登録がありません。
            $ret['htmlBody'] = '<p style="margin-top:20px;">'.$g['objMTS']->getSomeMessage('ITATERRAFORM-ERR-181060').'</p>';
            return $ret;
        }

        //PolicySet用のHtmlBodyを作成
        $policySetHtmlBody = $this->makePolicySetTablePrint($policySetListData, $workspaceListData, $policyListData);
        if($policySetHtmlBody != ""){
            $ret['result'] = true;
            $ret['htmlBody'] = $policySetHtmlBody;
        }

        return $ret;
    }


    function makePolicySetTablePrint($policySetListData, $workspaceListData, $policyListData){
        //グローバル変数宣言
        global $g;

        $HtmlBody = "";
        $dbSuccessFlag = false;

        //ITAに登録されているPolicySet一覧を取得
        $sql = "SELECT *
                FROM B_TERRAFORM_POLICY_SETS";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $dbSuccessFlag = true;
            $itaPolicySetList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaPolicySetList, $row);
            }
        }

        //ITAに登録されているWorkspace一覧を取得
        $sql = "SELECT *
                FROM D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $dbSuccessFlag = true;
            $itaWorkspaceList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaWorkspaceList, $row);
            }
        }

        //ITAに登録されているPolicy一覧を取得
        $sql = "SELECT *
                FROM B_TERRAFORM_POLICY";
        $tmpAryBind = array();
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);
        if($retArray[0] === true){
            $itaPolicyList = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                array_push($itaPolicyList, $row);
            }
        }

        if($dbSuccessFlag == true){
            //項目名
            $th1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106250'); //Organization Name
            $th2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106260'); //PolicySet Name
            $th3 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106270'); //ITAの登録状態
            $th4 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106280'); //削除/紐付け解除
            $th5 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106290'); //紐付Workspace
            $th6 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106300'); //紐付Policy
            $td1 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106310'); //削除
            $td2 = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106320'); //紐付解除

            $HtmlBody .=
<<<EOD
<div class="fakeContainer_Filter1Print" style="margin-top:20px; margin-bottom:20px">
    <table id="Mix4_1">
        <tbody>
            <tr class="defaultExplainRow">
                <th scope="col" rowspan="2" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106500')}"><span class="generalBold">{$th1}</span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106560')}"colspan="3" style="text-align:left;"><span class="generalBold">{$th2}</span></th>
                <th scope="col" rowspan="2" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410')}"><span class="generalBold">{$th3}</span></th>
                <th scope="col" rowspan="2" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106590')}"><span class="generalBold">{$th4}</span></th>
            </tr>
            <tr class="defaultExplainRow">
                <th scope="col" rowspan="1"><span class="generalBold"></span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106570')}"><span class="generalBold">{$th5}</span></th>
                <th scope="col" rowspan="1" title="{$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106580')}"><span class="generalBold">{$th6}</span></th>
            </tr>

EOD;
            $count = 1;
            foreach($policySetListData as $organizationName => $policyData){
                if(!empty($policyData)){
                    foreach($policyData as $data){
                        $cell_print = "cell_print_table_" . $count;
                        $policySetId = $data['id'];
                        $policySetName = $data['attributes']['name'];
                        $linkWorkspaceData = array();
                        if(isset($data['relationships']['workspaces']['data'])){
                            $linkWorkspaceData = $data['relationships']['workspaces']['data'];
                        }
                        $linkPolicyData = array();
                        if(isset($data['relationships']['policies']['data'])){
                            $linkPolicyData = $data['relationships']['policies']['data'];
                        }
                        $registerStatus = "";
                        $registeredFlag = false;
                        $disuseFlag = false;
                        $linkWorkspaceCount = count($linkWorkspaceData);
                        $linkPolicyCount = count($linkPolicyData);
                        $organizationRowCount = 1 + $linkWorkspaceCount + $linkPolicyCount;
                        $dummyRowCount = $linkWorkspaceCount + $linkPolicyCount;
                        $disabledFlag = true;
                        $disabled = "";

                        //ITAに登録されているかどうかをチェック
                        foreach($itaPolicySetList as $itaPolicySet){
                            if($itaPolicySet['POLICY_SET_NAME'] == $policySetName){
                                $registeredFlag = true;
                                if($itaPolicySet['DISUSE_FLAG'] == 1){
                                    $disuseFlag = true;
                                }
                                break;
                            }
                        }

                        if($registeredFlag == true){
                            if($disuseFlag == true){
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                            }else{
                                $disabledFlag = false;
                                $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                            }
                        }else{
                            $registerStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                        }

                        $disabled = "";
                        if ($disabledFlag) {
                            $disabled = "disabled=true";
                        }

                        $HtmlBody .=
<<<EOD
                                <tr valign="top">
                                    <td rowspan="{$organizationRowCount}" id="{$cell_print}_1">{$organizationName}</td>
                                    <td colspan="3" id="{$cell_print}_2">{$policySetName}</td>
                                    <td id="{$cell_print}_3">{$registerStatus}</td>
                                    <td id="{$cell_print}_4"><input class="deleteBtnInTbl" type="button" value={$td1} onclick="deletePolicySet(this, '{$policySetId}', '{$policySetName}')" {$disabled}></td>
                                </tr>

EOD;
                        //紐づいているWorkspaceの数だけループ
                        $workspaceLoopCount = 0;
                        foreach($linkWorkspaceData as $workspaceData){
                            $workspaceId = $workspaceData['id'];
                            $workspaceName = "";
                            $workspaceRegisteredFlag = false;
                            $workspaceDisuseFlag = false;
                            $workspaceRegisterStatus = "";

                            //$workspaceListDataからworkspaceIdが一致するものを探してworkspaceNameを取得
                            foreach($workspaceListData[$organizationName] as $wData){
                                if(!empty($wData)){
                                    if($wData['id'] == $workspaceId){
                                        $workspaceName = $wData['attributes']['name'];
                                        break;
                                    }
                                }
                            }
                            $disabledFlag = true;
                            $disabled = "";

                            //ITAに登録されているかどうかをチェック
                            foreach($itaWorkspaceList as $itaWorkspace){
                                if($itaWorkspace['ORGANIZATION_NAME'] == $organizationName && $itaWorkspace['WORKSPACE_NAME'] == $workspaceName){
                                    $workspaceRegisteredFlag = true;
                                    if($itaWorkspace['DISUSE_FLAG'] == 1){
                                        $workspaceDisuseFlag = true;
                                    }
                                    break;
                                }
                            }

                            if($workspaceRegisteredFlag == true){
                                if($workspaceDisuseFlag == true){
                                    $workspaceRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                                }else{
                                    $disabledFlag = false;
                                    $workspaceRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                                }
                            }else{
                                $workspaceRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                            }

                            $disabled = "";
                            if ($disabledFlag) {
                                $disabled = "disabled=true";
                            }

                            //余白スペースを作成
                            $dummyWorkspaceTdArea = '';
                            if($workspaceLoopCount == 0){
                                if($linkWorkspaceCount != 0){
                                    $dummyWorkspaceTdArea = '<td rowspan="'. $dummyRowCount . '"id="'.$cell_print.'_5"></td>';
                                }
                            }


                            $HtmlBody .=
<<<EOD
                                    <tr valign="top">
                                        {$dummyWorkspaceTdArea}
                                        <td id="{$cell_print}_6">{$workspaceName}</td>
                                        <td id="{$cell_print}_7">-</td>
                                        <td id="{$cell_print}_8">{$workspaceRegisterStatus}</td>
                                        <td id="{$cell_print}_9"><input class="deleteBtnInTbl" type="button" value='{$td2}' onclick="deleteRelationshipWorkspace(this, '{$policySetId}', '{$policySetName}', '{$workspaceId}', '{$workspaceName}')" {$disabled}></td>
                                    </tr>
EOD;
                            $workspaceLoopCount++;
                        }

                        //紐づいているPolicyの数だけループ
                        $policyLoopCount = 0;
                        foreach($linkPolicyData as $policyData){
                            $policyId = $policyData['id'];
                            $policyName = "";
                            $policyRegisteredFlag = false;
                            $policyDisuseFlag = false;
                            $policyRegisterStatus = "";
                            $disabledFlag = true;
                            $disabled = "";

                            //$policyListDataからpolicyIdが一致するものを探してpolicyNameを取得
                            foreach($policyListData[$organizationName] as $pData){
                                if(!empty($pData)){
                                    if($pData['id'] == $policyId){
                                        $policyName = $pData['attributes']['name'];
                                        break;
                                    }
                                }
                            }

                            //ITAに登録されているかどうかをチェック
                            foreach($itaPolicyList as $itaPolicy){
                                if($itaPolicy['POLICY_NAME'] == $policyName){
                                    $policyRegisteredFlag = true;
                                    if($itaPolicy['DISUSE_FLAG'] == 1){
                                        $policyDisuseFlag = true;
                                    }
                                    break;
                                }
                            }

                            if($policyRegisteredFlag == true){
                                if($policyDisuseFlag == true){
                                    $policyRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106350'); //廃止済み
                                }else{
                                    $disabledFlag = false;
                                    $policyRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106340'); //登録済み
                                }
                            }else{
                                $policyRegisterStatus = $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106360'); //未登録
                            }

                            $disabled = "";
                            if ($disabledFlag) {
                                $disabled = "disabled=true";
                            }

                            //余白スペースを作成
                            $dummyPolicyTdArea = '';
                            if($policyLoopCount == 0){
                                if($linkWorkspaceCount == 0){
                                    $dummyPolicyTdArea = '<td rowspan="'. $dummyRowCount . '"id="'.$cell_print.'_5"></td>';
                                }
                            }

                            $HtmlBody .=
<<<EOD
                                    <tr valign="top">
                                        {$dummyPolicyTdArea}
                                        <td id="{$cell_print}_6">-</td>
                                        <td id="{$cell_print}_7">{$policyName}</td>
                                        <td id="{$cell_print}_8">{$policyRegisterStatus}</td>
                                        <td id="{$cell_print}_9"><input class="deleteBtnInTbl" type="button" value={$td2} onclick="deleteRelationshipPolicy(this, '{$policySetId}', '{$policySetName}', '{$policyId}', '{$policyName}')" {$disabled}></td>
                                    </tr>
EOD;
                            $policyLoopCount++;
                        }

                        $count++;

                    }
                }
            }

            $HtmlBody .=
<<<EOD
        </tbody>
    </div>
</div>
EOD;
        }




        return $HtmlBody;
    }

}


?>